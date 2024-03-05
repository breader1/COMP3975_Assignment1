<?php
include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo "Invalid admin credentials.";
    exit();
}

// Check if the delete form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $deleteCategory = isset($_POST['delete_category']) ? $_POST['delete_category'] : '';
    $deleteKeyword = isset($_POST['delete_keyword']) ? $_POST['delete_keyword'] : '';

    // Validate input (you might want to add more validation)
    if (empty($deleteCategory) || empty($deleteKeyword)) {
        echo "Invalid category or keyword.";
    } else {
        // Delete the filter
        $deleteFilterStmt = $transactionsDb->prepare("DELETE FROM filters WHERE category = :category AND keyword = :keyword");
        $deleteFilterStmt->bindParam(':category', $deleteCategory, SQLITE3_TEXT);
        $deleteFilterStmt->bindParam(':keyword', $deleteKeyword, SQLITE3_TEXT);
        $deleteFilterStmt->execute();

        echo "Filter deleted successfully!";
        echo "<br>redirecting...";
        header("refresh:2;url=edit_buckets.php");
    }
} else {
    echo "Invalid request.";
}
?>
