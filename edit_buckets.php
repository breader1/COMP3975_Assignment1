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

// Check if the delete button is clicked
if (isset($_POST['delete_category']) && isset($_POST['delete_keyword'])) {
    $deleteCategory = $_POST['delete_category'];
    $deleteKeyword = $_POST['delete_keyword'];

    // Delete the filter
    $deleteFilterStmt = $transactionsDb->prepare("DELETE FROM filters WHERE category = :category AND keyword = :keyword");
    $deleteFilterStmt->bindParam(':category', $deleteCategory, SQLITE3_TEXT);
    $deleteFilterStmt->bindParam(':keyword', $deleteKeyword, SQLITE3_TEXT);
    $deleteFilterStmt->execute();

    echo "Filter deleted successfully!";
    echo "<br>redirecting...";
    header("refresh:2;url=edit_buckets.php");
}

// Fetch all filters
$getAllFiltersStmt = $transactionsDb->prepare('SELECT category, keyword FROM filters');
$result = $getAllFiltersStmt->execute();

// Start building the table
echo '<h2>Edit Buckets</h2>';

// Insert Filter button that redirects to insert_filter.php
echo '<button onclick="location.href=\'insert_bucket.php\'">Insert Bucket</button>';
echo '<br><br>';
echo '<table border="1">';
echo '<tr>';
echo '<th>Category</th>';
echo '<th>Keyword</th>';
echo '<th>Actions</th>'; // New column for actions
echo '</tr>';

// Loop through the results and display each filter in a table row
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo '<tr>';
    echo '<td>' . $row['category'] . '</td>';
    echo '<td>' . $row['keyword'] . '</td>';
    echo '<td>';
    echo '<a href="update_bucket.php?category=' . $row['category'] . '&keyword=' . $row['keyword'] . '">Update</a>'; // Link to update_bucket.php
    echo ' | ';
    echo '<form method="post" action="delete_bucket.php" style="display:inline-block;">'; // Updated form action
    echo '<input type="hidden" name="delete_category" value="' . $row['category'] . '">';
    echo '<input type="hidden" name="delete_keyword" value="' . $row['keyword'] . '">';
    echo '<button type="submit">Delete</button>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
}

// Finish the table
echo '</table>';

// End output buffering and flush the buffer
ob_end_flush();
?>
