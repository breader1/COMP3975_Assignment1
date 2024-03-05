<?php
// Include your session check code
include 'session_check.php';

// Include your database connection code
include 'db_params.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $transactionId = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : '';
    $amount = isset($_POST['amount']) ? $_POST['amount'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';

    // Validate input (you might want to add more validation)
    if (empty($transactionId) || empty($amount) || empty($description)) {
        echo "Please enter all required fields.";
    } else {
        // Check if the transaction ID exists
        $checkTransactionStmt = $transactionsDb->prepare('SELECT COUNT(*) FROM transactions WHERE transaction_id = :transaction_id');
        $checkTransactionStmt->bindParam(':transaction_id', $transactionId, SQLITE3_TEXT);
        $result = $checkTransactionStmt->execute();
        $count = $result->fetchArray(SQLITE3_NUM)[0];

        if ($count > 0) {
            // Update transaction data in the transactions table
            $updateTransactionStmt = $transactionsDb->prepare("UPDATE transactions SET amount = :amount, description = :description WHERE transaction_id = :transaction_id");
            $updateTransactionStmt->bindParam(':amount', $amount, SQLITE3_TEXT);
            $updateTransactionStmt->bindParam(':description', $description, SQLITE3_TEXT);
            $updateTransactionStmt->bindParam(':transaction_id', $transactionId, SQLITE3_TEXT);
            $updateTransactionStmt->execute();

            echo "Transaction details updated successfully!";
            echo "<br>redirecting...";
            header("refresh:2;url=edit_transactions.php");
        } else {
            echo "Transaction ID does not exist.";
        }
    }
}

// Fetch all transactions
$getAllTransactionsStmt = $transactionsDb->prepare('SELECT transaction_id, transaction_date, description, debit, credit, balance FROM transactions');
$result = $getAllTransactionsStmt->execute();

// Start building the table
echo '<h2>Edit Transactions</h2>';

// Insert Transaction button that redirects to insert_transaction.php
echo '<button onclick="location.href=\'insert_transaction.php\'">Insert Transaction</button>';
echo '<br><br>';
echo '<table border="1">';
echo '<tr>';
echo '<th>Transaction ID</th>';
echo '<th>Transaction Date</th>';
echo '<th>Description</th>';
echo '<th>Debit</th>';
echo '<th>Credit</th>';
echo '<th>Balance</th>';
echo '<th>Actions</th>'; // New column for actions
echo '</tr>';

// Loop through the results and display each transaction in a table row
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo '<tr>';
    echo '<td>' . $row['transaction_id'] . '</td>';
    echo '<td>' . $row['transaction_date'] . '</td>';
    echo '<td>' . $row['description'] . '</td>';
    echo '<td>' . $row['debit'] . '</td>';
    echo '<td>' . $row['credit'] . '</td>';
    echo '<td>' . $row['balance'] . '</td>';
    echo '<td>';
    echo '<a href="update_transaction.php?transaction_id=' . $row['transaction_id'] . '">Update</a>'; // Link to update_transaction.php
    echo ' | ';
    echo '<a href="delete_transaction.php?delete_transaction_id=' . $row['transaction_id'] . '">Delete</a>';
    echo '</td>';
    echo '</tr>';
}

// Finish the table
echo '</table>';

// End output buffering and flush the buffer
ob_end_flush();
?>
