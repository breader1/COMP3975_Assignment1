<?php ob_start(); ?>
<?php
// Include your session check code
include 'session_check.php';

// Include your database connection code
include 'db_params.php';

// Include your header code
//include 'header.php';

// Include your footer code
include 'footer.php';

// Initialize the message variable
$message = "";

// Check if the delete_transaction_id is provided in the URL
if (isset($_GET['delete_transaction_id'])) {
    // Get the transaction ID from the URL
    $deleteTransactionId = $_GET['delete_transaction_id'];

    // Validate input (you might want to add more validation)
    if (empty($deleteTransactionId)) {
        $message = '<div class="alert alert-success" role="alert">Please enter the transaction ID to delete.</div>';
        echo $message;
    } else {
        // Check if the transaction ID exists
        $checkTransactionStmt = $transactionsDb->prepare('SELECT COUNT(*) FROM transactions WHERE transaction_id = :transaction_id');
        $checkTransactionStmt->bindParam(':transaction_id', $deleteTransactionId, SQLITE3_TEXT);
        $result = $checkTransactionStmt->execute();
        $count = $result->fetchArray(SQLITE3_NUM)[0];

        if ($count > 0) {
            // Delete the transaction
            $deleteTransactionStmt = $transactionsDb->prepare("DELETE FROM transactions WHERE transaction_id = :transaction_id");
            $deleteTransactionStmt->bindParam(':transaction_id', $deleteTransactionId, SQLITE3_TEXT);
            $deleteTransactionStmt->execute();

            $message = '<div class="alert alert-success" role="alert">Transaction deleted successfully!<br>redirecting...</div>';
            echo $message;
            header("refresh:2;url=edit_transactions.php");
            exit(); // Ensure that the script terminates after the redirect header
        } else {
            $message = '<div class="alert alert-danger" role="alert">Transaction ID does not exist.</div>';
            echo $message;
        }
    }


}

?>
<?php ob_end_flush(); ?>