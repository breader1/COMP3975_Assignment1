<?php ob_start(); ?>
<?php
include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo '<div class="alert alert-danger" role="alert">Invalid admin credentials.</div>';
    exit();
}

// Check if the username is provided in the query string
if (!isset($_GET['username'])) {
    echo '<div class="alert alert-danger" role="alert">Username not provided.</div>';
    exit();
}

$usernameToDelete = $_GET['username'];

// Fetch the user details
$getUserStmt = $usersDb->prepare('SELECT username FROM user WHERE username = :username');
$getUserStmt->bindParam(':username', $usernameToDelete, SQLITE3_TEXT);
$result = $getUserStmt->execute();
$userDetails = $result->fetchArray(SQLITE3_ASSOC);

// Check if the user exists
if (!$userDetails) {
    echo '<div class="alert alert-danger" role="alert">User not found.</div>';
    exit();
}

// Check if the delete form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete the user
    $deleteUserStmt = $usersDb->prepare('DELETE FROM user WHERE username = :username');
    $deleteUserStmt->bindParam(':username', $usernameToDelete, SQLITE3_TEXT);
    $deleteUserStmt->execute();

    echo '<div class="alert alert-success" role="alert">User deleted successfully! Redirecting...</div>';
    header("refresh:2;url=edit_users.php");
    exit();
}

// Display the user details and delete form
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0yJTl8io/zCWcqR5LVDqpKvgoZ8yzoL+3i80pFFhsz1HqM+q8o/xKbqlGPKrj/u" crossorigin="anonymous">
    <style>
        .delete-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h2>Delete User</h2>
        <div class="delete-message">
            <p>Are you sure you want to delete the user '<?php echo $userDetails['username']; ?>'?</p>
        </div>
        <form action="" method="post">
            <button type="submit" class="btn btn-danger">Delete User</button>
            <a href="edit_users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>

</html>
<?php ob_end_flush(); ?>
<?php include 'footer.php'; ?>
