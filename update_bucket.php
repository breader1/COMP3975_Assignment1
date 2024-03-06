<?php
ob_start();
include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code
//include 'header.php'; // Include your header code

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo '<div class="alert alert-danger" role="alert">Invalid admin credentials.</div>';
    exit();
}

// Check if the category and keyword are provided in the query string
if (!isset($_GET['category']) || !isset($_GET['keyword'])) {
    echo '<div class="alert alert-danger" role="alert">Category or keyword not provided.</div>';
    exit();
}

$categoryToUpdate = $_GET['category'];
$keywordToUpdate = $_GET['keyword'];

// Fetch the bucket details
$getBucketStmt = $transactionsDb->prepare('SELECT * FROM filters WHERE category = :category AND keyword = :keyword');
$getBucketStmt->bindParam(':category', $categoryToUpdate, SQLITE3_TEXT);
$getBucketStmt->bindParam(':keyword', $keywordToUpdate, SQLITE3_TEXT);
$result = $getBucketStmt->execute();
$bucketDetails = $result->fetchArray(SQLITE3_ASSOC);

// Check if the bucket exists
if (!$bucketDetails) {
    echo '<div class="alert alert-danger" role="alert">Bucket not found.</div>';
    exit();
}

// Check if the update form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated category and keyword
    $updatedCategory = isset($_POST['new_category']) ? $_POST['new_category'] : $categoryToUpdate;
    $updatedKeyword = isset($_POST['new_keyword']) ? $_POST['new_keyword'] : '';

    // Validate input
    if (empty($updatedCategory) || empty($updatedKeyword)) {
        echo '<div class="alert alert-danger" role="alert">Please enter both category and keyword.</div>';
    } elseif (strlen($updatedCategory) > 30 || strlen($updatedKeyword) > 30) {
        echo '<div class="alert alert-danger" role="alert">Category and keyword must be 30 characters or less.</div>';
    } else {
        // Update data in the filters table for the specific row using the fetched details
        $updateBucketStmt = $transactionsDb->prepare('UPDATE filters SET keyword = :new_keyword WHERE id = :id');
        $updateBucketStmt->bindParam(':new_keyword', $updatedKeyword, SQLITE3_TEXT);
        $updateBucketStmt->bindParam(':id', $bucketDetails['id'], SQLITE3_INTEGER);
        $updateBucketStmt->execute();

        echo '<div class="alert alert-success" role="alert">Bucket details updated successfully!<br>redirecting...</div>';
        //return to previous page in 2 seconds
        header("refresh:2;url=edit_buckets.php");
    }
}

// Display the bucket details and update form
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Bucket Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Update Bucket Details</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="new_category">New Category:</label>
                <input type="text" class="form-control" id="new_category" name="new_category" value="<?php echo $bucketDetails['category']; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="new_keyword">New Keyword:</label>
                <input type="text" class="form-control" id="new_keyword" name="new_keyword" value="<?php echo $bucketDetails['keyword']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Bucket Details</button>
        </form>
    </div>
</body>

</html>
<?php ob_end_flush() ?>