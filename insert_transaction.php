<?php
// Include your session check code
include 'session_check.php';

// Include your database connection code
include 'db_params.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $transactionDate = isset($_POST['transaction_date']) ? $_POST['transaction_date'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $debit = isset($_POST['debit']) ? $_POST['debit'] : '';
    $credit = isset($_POST['credit']) ? $_POST['credit'] : '';

    // Validate input
    if (empty($transactionDate) || empty($description)) {
        echo "Please enter a date and description.";
    } else {
        // Convert the date format to mm/dd/yyyy
        $transactionDate = DateTime::createFromFormat('Y-m-d', $transactionDate);
        $formattedDate = $transactionDate ? $transactionDate->format('m/d/Y') : '';

        // Fetch the latest balance from the previous row
        $getLatestBalanceStmt = $transactionsDb->prepare('SELECT balance FROM transactions ORDER BY transaction_date DESC LIMIT 1');
        $result = $getLatestBalanceStmt->execute();
        $latestBalance = $result->fetchArray(SQLITE3_NUM)[0];

        // Calculate the new balance
        $balance = $latestBalance + $credit - $debit;

        // Insert data into the transactions table
        $insertTransactionStmt = $transactionsDb->prepare("
            INSERT INTO transactions (transaction_date, description, debit, credit, balance)
            VALUES (:transaction_date, :description, :debit, :credit, :balance)
        ");

        $insertTransactionStmt->bindParam(':transaction_date', $formattedDate, SQLITE3_TEXT);
        $insertTransactionStmt->bindParam(':description', $description, SQLITE3_TEXT);
        $insertTransactionStmt->bindParam(':debit', $debit, SQLITE3_TEXT);
        $insertTransactionStmt->bindParam(':credit', $credit, SQLITE3_TEXT);
        $insertTransactionStmt->bindParam(':balance', $balance, SQLITE3_TEXT);

        if (!$insertTransactionStmt->execute()) {
            die("Error inserting transaction: " . $transactionsDb->lastErrorMsg());
        }

        // Truncate the buckets table
        $transactionsDb->exec('DELETE FROM buckets');

        // Insert data into the buckets table
        $insertBucketStmt = $transactionsDb->prepare("
            INSERT INTO buckets (category, description)
            SELECT f.category, t.description
            FROM transactions t
            JOIN filters f ON LOWER(t.description) LIKE '%' || f.keyword || '%'
        ");

        if (!$insertBucketStmt->execute()) {
            die("Error inserting into buckets: " . $transactionsDb->lastErrorMsg());
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
            GROUP BY category
        ");

        if (!$insertReportStmt->execute()) {
            die("Error inserting into reports: " . $transactionsDb->lastErrorMsg());
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
            GROUP BY b.category, t.transaction_date
        ");

        if (!$insertAggregatedDataStmt->execute()) {
            die("Error inserting into aggregated_data: " . $transactionsDb->lastErrorMsg());
        }

        echo "Transaction added successfully!";
        echo "<br>redirecting...";
        header("refresh:2;url=edit_transactions.php");
    }
}

// Display the form for inserting a new transaction
echo '<h2>Insert Transaction</h2>';
echo '<form action="insert_transaction.php" method="post">';
echo '  <label for="transaction_date">Transaction Date:</label>';
echo '  <input type="date" id="transaction_date" name="transaction_date" required><br>'; // Use type="date"
echo '  <label for="description">Description:</label>';
echo '  <input type="text" id="description" name="description" required><br>';
echo '  <label for="debit">Debit:</label>';
echo '  <input type="text" id="debit" name="debit" required><br>';
echo '  <label for="credit">Credit:</label>';
echo '  <input type="text" id="credit" name="credit" required><br>';
echo '  <br><br>';
echo '  <button type="submit">Insert Transaction</button>';
echo '</form>';

// Link to go back to edit_transactions.php
echo '<br><br>';
echo '<button onclick="location.href=\'edit_transactions.php\'">Back to Edit Transactions</button>';
?>
