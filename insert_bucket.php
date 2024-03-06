<?php
// Start output buffering
ob_start();

include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code
include 'header.php'; // Include your header code

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo '<br>';
    echo '<div class="alert alert-danger" role="alert">Invalid admin credentials.</div>';
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';

    // Validate input (you might want to add more validation)
    if (empty($category) || empty($keyword)) {
        echo '<br>';
        echo '<div class="alert alert-danger" role="alert">Please enter both category and keyword.</div>';
    } elseif (strlen($category) > 30 || strlen($keyword) > 30) {
        echo '<br>';
        echo '<div class="alert alert-danger" role="alert">Category and keyword must not exceed 30 characters.</div>';
    } else {
        // Check if the keyword already exists
        $checkKeywordStmt = $transactionsDb->prepare('SELECT COUNT(*) FROM filters WHERE keyword = :keyword');
        $checkKeywordStmt->bindParam(':keyword', $keyword, SQLITE3_TEXT);
        $result = $checkKeywordStmt->execute();
        $count = $result->fetchArray(SQLITE3_NUM)[0];

        if ($count > 0) {
            echo '<br>';
            echo '<div class="alert alert-danger" role="alert">Keyword already exists. Choose a different keyword.</div>';
        } else {
            // Insert data into the filters table
            $insertFilterStmt = $transactionsDb->prepare("INSERT INTO filters (category, keyword) VALUES (:category, :keyword)");
            $insertFilterStmt->bindParam(':category', $category, SQLITE3_TEXT);
            $insertFilterStmt->bindParam(':keyword', $keyword, SQLITE3_TEXT);
            $insertFilterStmt->execute();

            echo '<br>';
            echo '<div class="alert alert-success" role="alert">Filter added successfully!<br>redirecting...</div>';
            header("refresh:2;url=edit_buckets.php");
        }
    }
}

// Display the form for inserting a new filter
echo '<div class="container mt-5">';
echo '<h2>Insert Bucket</h2>';
echo '<form action="insert_bucket.php" method="post">';
echo '  <div class="form-group">';
echo '      <label for="category">Category:</label>';
echo '      <input type="text" class="form-control" id="category" name="category" required>';
echo '  </div>';
echo '  <div class="form-group">';
echo '      <label for="keyword">Keyword:</label>';
echo '      <input type="text" class="form-control" id="keyword" name="keyword" required>';
echo '  </div>';
echo '  <button type="submit" class="btn btn-primary">Insert Bucket</button>';
echo '</form>';

// Link to go back to edit_buckets.php
echo '<br><br>';
echo '<button onclick="location.href=\'edit_buckets.php\'" class="btn btn-secondary">Back to Edit Buckets</button>';
echo '</div>'; // Close container

// End output buffering and flush the buffer
ob_end_flush();
?>
