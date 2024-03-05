<?php
include 'session_check.php';
// Assuming you have already established a connection to the users database
include 'db_params.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted username
    $usernameToApprove = isset($_POST['username']) ? $_POST['username'] : '';

    // Check if the username exists in the user table
    $checkUserStmt = $usersDb->prepare('SELECT COUNT(*) FROM users WHERE username = :username AND approved = 0');
    $checkUserStmt->bindParam(':username', $usernameToApprove, SQLITE3_TEXT);
    $result = $checkUserStmt->execute();
    $count = $result->fetchArray(SQLITE3_NUM)[0];

    if ($count > 0) {
        // Update the user approval status
        $approveUserStmt = $usersDb->prepare('UPDATE users SET approved = 1 WHERE username = :username');
        $approveUserStmt->bindParam(':username', $usernameToApprove, SQLITE3_TEXT);
        $approveUserStmt->execute();

        echo "User '$usernameToApprove' has been approved.";
    } else {
        echo "User '$usernameToApprove' not found or already approved.";
    }
}

// Close the users database connection
$usersDb->close();
?>