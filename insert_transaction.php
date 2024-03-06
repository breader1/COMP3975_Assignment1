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
        $transactionDate = isset($_POST['transaction_date']) ? $_POST['transaction_date'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $debit = isset($_POST['debit']) ? $_POST['debit'] : '';
        $credit = isset($_POST['credit']) ? $_POST['credit'] : '';

        // Validate input
        if (empty($transactionDate) || empty($description)) {
            $message = '<br><div class="alert alert-danger" role="alert">Please enter a date and description.</div>';
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
                $message = '<br><div class="alert alert-danger" role="alert">Error inserting transaction: ' . $transactionsDb->lastErrorMsg() . '</div>';
            } else {
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
                    $message = '<br><div class="alert alert-danger" role="alert">Error inserting into buckets: ' . $transactionsDb->lastErrorMsg() . '</div>';
                } else {
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
                        $message = '<br><div class="alert alert-danger" role="alert">Error inserting into reports: ' . $transactionsDb->lastErrorMsg() . '</div>';
                    } else {
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
                            $message = '<br><div class="alert alert-danger" role="alert">Error inserting into aggregated_data: ' . $transactionsDb->lastErrorMsg() . '</div>';
                        } else {
                            $message = '<br><div class="alert alert-success" role="alert">Transaction added successfully!<br>redirecting...</div>';
                            header("refresh:2;url=edit_transactions.php");
                        }
                    }
                }
            }
        }
    }

    ?>
    <?php include 'header.php'; ?>

    <div class="container">
        <h2 class="text-center mp-4">Insert Transaction</h2>
        <form action="insert_transaction.php" method="post" class="mx-auto" style="max-width: 500px;"> <!-- Adjusted width here -->
            <div class="form-group">
                <label for="transaction_date">Transaction Date:</label>
                <input type="date" class="form-control" id="transaction_date" name="transaction_date" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <input type="text" class="form-control" id="description" name="description" required>
            </div>
            <div class="form-group">
                <label for="debit">Debit:</label>
                <input type="text" class="form-control" id="debit" name="debit" required>
            </div>
            <div class="form-group">
                <label for="credit">Credit:</label>
                <input type="text" class="form-control" id="credit" name="credit" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Insert Transaction</button>
        </form>

        <br><br>
        <button onclick="location.href='edit_transactions.php'" class="btn btn-secondary btn-block">Back to Transactions</button>

        <?php echo $message; ?>
    </div>
    <?php include 'footer.php'; ?>
