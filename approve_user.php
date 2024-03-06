<?php
ob_start();
include 'db_params.php'; // Include your database connection code
include 'session_check.php';
include 'footer.php'; // Include your header file

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo '<br>';
    echo '<div class="alert alert-danger" role="alert">Invalid admin credentials.</div>';
    exit();
}

// Check if the button was clicked for an individual user
if (isset($_POST['approve_user'])) {
    // Get admin input
    $adminUsername = $_SESSION['admin'];

    // Get the username of the user to approve from data attribute
    $userToApprove = $_POST['approve_user'];

    // Approve the specific user in the user table
    $approveUserStmt = $usersDb->prepare("UPDATE user SET approved = 1 WHERE username = :username");
    $approveUserStmt->bindParam(':username', $userToApprove, SQLITE3_TEXT);
    $approveUserStmt->execute();

    echo '<br>';
    echo '<div class="alert alert-success" role="alert">User \'' . htmlspecialchars($userToApprove) . '\' approved by admin.</div>';
    header("refresh:2;url=admin.php");
    exit(); // Stop script execution

} else {
    // Approve all users in the user table
    $approveAllUsersStmt = $usersDb->prepare("UPDATE user SET approved = 1 WHERE approved = 0");
    $approveAllUsersStmt->execute();

    // Close the users database connection
    $usersDb->close();

    echo '<br>';
    echo '<div class="alert alert-success" role="alert">All users approved by admin.</div>';
    ob_end_flush();
    header("refresh:2;url=admin.php");
    exit(); // Stop script execution
}
?>