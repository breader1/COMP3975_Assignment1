<?php
// Include your session check code
include 'session_check.php';

// Include your database connection code
include 'db_params.php';

// Include your header code
include 'header.php';

// Initialize the message variable
$message = "";


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $transactionId = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : '';
    $amount = isset($_POST['amount']) ? $_POST['amount'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';

    // Validate input (you might want to add more validation)
    if (empty($transactionId) || empty($amount) || empty($description)) {
        $message = '<br><div class="alert alert-danger" role="alert">Please enter all required fields.</div>';
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

            $message = '<br><div class="alert alert-success" role="alert">Transaction details updated successfully!<br>redirecting...</div>';
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
echo '<div class="container mt-4">';
echo '<h2>Edit Transactions</h2>';

// Insert Transaction button that redirects to insert_transaction.php
echo '<button onclick="location.href=\'insert_transaction.php\'" class="btn btn-primary">Insert Transaction</button>';
echo '<br><br>';
echo '<table class="table">';
echo '<thead class="thead-dark">';
echo '<tr>';
echo '<th>Transaction ID</th>';
echo '<th>Transaction Date</th>';
echo '<th>Description</th>';
echo '<th>Debit</th>';
echo '<th>Credit</th>';
echo '<th>Balance</th>';
echo '<th>Actions</th>'; // New column for actions
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Loop through the results and display each transaction in a table row
if ($result) {
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echo '<tr>';
        echo '<td>' . $row['transaction_id'] . '</td>';
        echo '<td>' . $row['transaction_date'] . '</td>';
        echo '<td>' . $row['description'] . '</td>';
        echo '<td>' . $row['debit'] . '</td>';
        echo '<td>' . $row['credit'] . '</td>';
        echo '<td>' . $row['balance'] . '</td>';
        echo '<td>';
        echo '<a href="update_transaction.php?transaction_id=' . $row['transaction_id'] . '" class="btn btn-primary">Update</a>'; // Link to update_transaction.php
        echo ' ';
        echo '<a href="delete_transaction.php?delete_transaction_id=' . $row['transaction_id'] . '" class="btn btn-danger">Delete</a>';
        echo '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="7">No transactions found.</td></tr>';
}

echo '</tbody>';
echo '</table>';

echo $message;

// End output buffering and flush the buffer
ob_end_flush();

// Include your footer code
include 'footer.php';
echo '</div>'; // Close the container
