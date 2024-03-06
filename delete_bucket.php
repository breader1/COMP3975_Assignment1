<?php
include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code
include 'header.php'; // Include your header code

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo'<br>';
    echo '<div class="alert alert-danger" role="alert">Invalid admin credentials.</div>';
    exit();
}

// Check if the delete form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $deleteCategory = isset($_POST['delete_category']) ? $_POST['delete_category'] : '';
    $deleteKeyword = isset($_POST['delete_keyword']) ? $_POST['delete_keyword'] : '';

    // Validate input (you might want to add more validation)
    if (empty($deleteCategory) || empty($deleteKeyword)) {
        echo'<br>';
        echo '<div class="alert alert-danger" role="alert">Invalid category or keyword.</div>';
    } else {
        // Delete the filter
        $deleteFilterStmt = $transactionsDb->prepare("DELETE FROM filters WHERE category = :category AND keyword = :keyword");
        $deleteFilterStmt->bindParam(':category', $deleteCategory, SQLITE3_TEXT);
        $deleteFilterStmt->bindParam(':keyword', $deleteKeyword, SQLITE3_TEXT);
        $deleteFilterStmt->execute();

        echo'<br>';
        echo '<div class="alert alert-success" role="alert">Filter deleted successfully!<br>redirecting...</div>';
        header("refresh:2;url=edit_buckets.php");
    }
} else {
    echo'<br>';
    echo '<div class="alert alert-danger" role="alert">Invalid request.</div>';
}
    include 'footer.php'; // Include your footer code
?>
