<?php
include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo "Invalid admin credentials.";
    exit();
}

// Check if the username is provided in the query string
if (!isset($_GET['username'])) {
    echo "Username not provided.";
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
    echo "User not found.";
    exit();
}

// Check if the delete form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete the user
    $deleteUserStmt = $usersDb->prepare('DELETE FROM user WHERE username = :username');
    $deleteUserStmt->bindParam(':username', $usernameToDelete, SQLITE3_TEXT);
    $deleteUserStmt->execute();

    echo "User deleted successfully!";
    echo "<br>redirecting...";
    header("refresh:2;url=edit_users.php");
}

// Display the user details and delete form
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
</head>

<body>
    <h2>Delete User</h2>
    <p>Are you sure you want to delete the user '<?php echo $userDetails['username']; ?>'?</p>
    <form action="" method="post">
        <button type="submit">Delete User</button>
        <a href="edit_users.php"><button>Cancel</button></a>
    </form>
</body>

</html>
