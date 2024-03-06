<?php
// Include your session check code
include 'session_check.php';

// Include your database connection code
include 'db_params.php';

// Initialize the message variable
$message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $transactionId = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $debit = isset($_POST['debit']) ? $_POST['debit'] : '';
    $credit = isset($_POST['credit']) ? $_POST['credit'] : '';

    // Validate input (you might want to add more validation)
    if (empty($transactionId) || empty($description)) {
        echo '<div class="alert alert-danger" role="alert">Please enter all required fields.</div>';
    } else {
        // Check if the transaction ID exists
        $checkTransactionStmt = $transactionsDb->prepare('SELECT COUNT(*) FROM transactions WHERE transaction_id = :transaction_id');
        $checkTransactionStmt->bindParam(':transaction_id', $transactionId, SQLITE3_TEXT);
        $result = $checkTransactionStmt->execute();
        $count = $result->fetchArray(SQLITE3_NUM)[0];

        if ($count > 0) {
            // Fetch the latest balance from the previous row
            $getLatestBalanceStmt = $transactionsDb->prepare('SELECT balance FROM transactions WHERE transaction_id = :transaction_id');
            $getLatestBalanceStmt->bindParam(':transaction_id', $transactionId, SQLITE3_TEXT);
            $result = $getLatestBalanceStmt->execute();
            $latestBalance = $result->fetchArray(SQLITE3_NUM)[0];

            // Calculate the new balance
            $balance = $latestBalance - $debit + $credit/2; //for whatever reason credit was doubling

            // Update transaction data in the transactions table
            $updateTransactionStmt = $transactionsDb->prepare("UPDATE transactions SET description = :description, debit = :debit, credit = :credit, balance = :balance WHERE transaction_id = :transaction_id");
            $updateTransactionStmt->bindParam(':description', $description, SQLITE3_TEXT);
            $updateTransactionStmt->bindParam(':debit', $debit, SQLITE3_TEXT);
            $updateTransactionStmt->bindParam(':credit', $credit, SQLITE3_TEXT);
            $updateTransactionStmt->bindParam(':balance', $balance, SQLITE3_TEXT);
            $updateTransactionStmt->bindParam(':transaction_id', $transactionId, SQLITE3_TEXT);
            $updateTransactionStmt->execute();

            // Truncate the buckets table
            $transactionsDb->exec('DELETE FROM buckets');

            // Insert data into the buckets table
            $insertBucketStmt = $transactionsDb->prepare("
            INSERT INTO buckets (category, description)
            SELECT f.category, t.description
            FROM transactions t
            JOIN filters f ON LOWER(t.description) LIKE '%' || f.keyword || '%'");

            if (!$insertBucketStmt->execute()) {
                die("<div class='alert alert-danger' role='alert'>Error inserting into buckets: " . $transactionsDb->lastErrorMsg() . "</div>");
            }

            // Truncate the reports table
            $transactionsDb->exec('DELETE FROM reports');

            // Insert data into the reports table
            $insertReportStmt = $transactionsDb->prepare("
            INSERT INTO reports (category, total)
            SELECT category, COUNT(*) as total
            FROM (
                SELECT b.category, COUNT(*) as total
                FROM buckets b
                GROUP BY b.category, b.description
            )
            GROUP BY category");

            if (!$insertReportStmt->execute()) {
                die("<div class='alert alert-danger' role='alert'>Error inserting into reports: " . $transactionsDb->lastErrorMsg() . "</div>");
            }

            // Truncate the aggregated_data table
            $transactionsDb->exec('DELETE FROM aggregated_data');

            // Insert data into the aggregated_data table
            $insertAggregatedDataStmt = $transactionsDb->prepare("
            INSERT INTO aggregated_data (category, total, transaction_date)
            SELECT b.category, COUNT(*) as total, MAX(t.transaction_date) as transaction_date
            FROM buckets b
            JOIN transactions t ON LOWER(b.description) LIKE '%' || LOWER(t.description) || '%'
            WHERE t.transaction_date IS NOT NULL
            GROUP BY b.category, t.transaction_date");

            if (!$insertAggregatedDataStmt->execute()) {
                die("<div class='alert alert-danger' role='alert'>Error inserting into aggregated_data: " . $transactionsDb->lastErrorMsg() . "</div>");
            }
            echo "<div class='alert alert-success' role='alert'>Transaction details updated successfully!</div>";
            echo "<br>redirecting...";
            header("refresh:2;url=edit_transactions.php");
        } else {
            echo "<div class='alert alert-danger' role='alert'>Transaction ID does not exist.</div>";
        }
    }
}

// Fetch the details of the transaction to be updated
if (isset($_GET['transaction_id'])) {
    $transactionId = $_GET['transaction_id'];

    // Retrieve transaction details
    $getTransactionDetailsStmt = $transactionsDb->prepare('SELECT transaction_id, description, debit, credit FROM transactions WHERE transaction_id = :transaction_id');
    $getTransactionDetailsStmt->bindParam(':transaction_id', $transactionId, SQLITE3_TEXT);
    $result = $getTransactionDetailsStmt->execute();
    $transactionDetails = $result->fetchArray(SQLITE3_ASSOC);

    // Display the form for updating the transaction
    ?>
    <?php include 'header.php'; ?>
    <div class="container">
        <h2 class="text-center mp-4">Update Transaction</h2>
        <form action="update_transaction.php" method="post" class="mx-auto" style="max-width: 500px;">
            <input type="hidden" name="transaction_id" value="<?php echo $transactionDetails['transaction_id']; ?>">
            <div class="form-group">
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" value="<?php echo $transactionDetails['description']; ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="debit">Debit:</label>
                <input type="text" id="debit" name="debit" value="<?php echo $transactionDetails['debit']; ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="credit">Credit:</label>
                <input type="text" id="credit" name="credit" value="<?php echo $transactionDetails['credit']; ?>" class="form-control" required>
            </div>
            <br><br>
            <button type="submit" class="btn btn-primary btn-block">Update Transaction</button>
        </form>
        <br><br>
        <button onclick="location.href='edit_transactions.php'" class="btn btn-secondary btn-block">Back to Edit Transactions</button>
    </div>
    <?php
    exit;
}

echo $message;

//include footer
include 'footer.php';
?>
