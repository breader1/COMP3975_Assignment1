<?php
include 'db_params.php'; // Include your database connection code
include 'session_check.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo "Invalid admin credentials.";
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

    echo "<br><br>User '$userToApprove' approved by admin.";
} else {
    // Approve all users in the user table
    $approveAllUsersStmt = $usersDb->prepare("UPDATE user SET approved = 1 WHERE approved = 0");
    $approveAllUsersStmt->execute();

    echo "All users approved by admin.";
}

// Close the users database connection
$usersDb->close();
?>
