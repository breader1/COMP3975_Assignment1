<?php
// Start output buffering
ob_start();

include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo "Invalid admin credentials.";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';

    // Validate input (you might want to add more validation)
    if (empty($category) || empty($keyword)) {
        echo "Please enter both category and keyword.";
    } elseif (strlen($category) > 30 || strlen($keyword) > 30) {
        echo "Category and keyword must not exceed 30 characters.";
    } else {
        // Check if the keyword already exists
        $checkKeywordStmt = $transactionsDb->prepare('SELECT COUNT(*) FROM filters WHERE keyword = :keyword');
        $checkKeywordStmt->bindParam(':keyword', $keyword, SQLITE3_TEXT);
        $result = $checkKeywordStmt->execute();
        $count = $result->fetchArray(SQLITE3_NUM)[0];

        if ($count > 0) {
            echo "Keyword already exists. Choose a different keyword.";
        } else {
            // Insert data into the filters table
            $insertFilterStmt = $transactionsDb->prepare("INSERT INTO filters (category, keyword) VALUES (:category, :keyword)");
            $insertFilterStmt->bindParam(':category', $category, SQLITE3_TEXT);
            $insertFilterStmt->bindParam(':keyword', $keyword, SQLITE3_TEXT);
            $insertFilterStmt->execute();

            echo "Filter added successfully!";
            echo "<br>redirecting...";
            header("refresh:2;url=edit_buckets.php");
        }
    }
}

// Display the form for inserting a new filter
echo '<h2>Insert Bucket</h2>';
echo '<form action="insert_bucket.php" method="post">';
echo '  <label for="category">Category:</label>';
echo '  <input type="text" id="category" name="category" required><br>';
echo '  <label for="keyword">Keyword:</label>';
echo '  <input type="text" id="keyword" name="keyword" required><br>';
echo '  <br><br>';
echo '  <button type="submit">Insert Bucket</button>';
echo '</form>';

// Link to go back to edit_buckets.php
echo '<br><br>';
echo '<button onclick="location.href=\'edit_buckets.php\'">Back to Edit Buckets</button>';

// End output buffering and flush the buffer
ob_end_flush();
?>
