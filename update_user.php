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

$usernameToUpdate = $_GET['username'];

// Fetch the user details
$getUserStmt = $usersDb->prepare('SELECT username, approved FROM user WHERE username = :username');
$getUserStmt->bindParam(':username', $usernameToUpdate, SQLITE3_TEXT);
$result = $getUserStmt->execute();
$userDetails = $result->fetchArray(SQLITE3_ASSOC);

// Check if the user exists
if (!$userDetails) {
    echo "User not found.";
    exit();
}

// Check if the update form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated username and approval status
    $updatedUsername = isset($_POST['new_username']) ? $_POST['new_username'] : $usernameToUpdate;
    $updatedApprovalStatus = isset($_POST['approve_user']) ? 1 : 0;

    // Validate username length
    if (strlen($updatedUsername) > 30) {
        echo "Error: Username must be 30 characters or less.";
        header("refresh:2;url=edit_users.php");
        exit();
    }

    // Check if the password is provided
    if (isset($_POST['new_password'])) {
        $newPassword = $_POST['new_password'];

        // Validate password length
        if (strlen($newPassword) > 30) {
            echo "Error: Password must be 30 characters or less.";
            header("refresh:2;url=edit_users.php");
            exit();
        }

        // Update both username and password
        $updateUserStmt = $usersDb->prepare('UPDATE user SET username = :new_username, password = :new_password, approved = :approved WHERE username = :username');
        $updateUserStmt->bindParam(':new_password', $newPassword, SQLITE3_TEXT);
    } else {
        // Update only username and approval status
        $updateUserStmt = $usersDb->prepare('UPDATE user SET username = :new_username, approved = :approved WHERE username = :username');
    }

    $updateUserStmt->bindParam(':new_username', $updatedUsername, SQLITE3_TEXT);
    $updateUserStmt->bindParam(':approved', $updatedApprovalStatus, SQLITE3_INTEGER);
    $updateUserStmt->bindParam(':username', $usernameToUpdate, SQLITE3_TEXT);
    $updateUserStmt->execute();

    echo "User details updated successfully!";
    echo "<br>redirecting...";
    //return to previous page in 2 seconds
    header("refresh:2;url=edit_users.php");
    
}

// Display the user details and update form
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User Details</title>
</head>

<body>
    <h2>Update User Details</h2>
    <form action="" method="post">
        <label for="new_username">New Username:</label>
        <input type="text" id="new_username" name="new_username" value="<?php echo $userDetails['username']; ?>" required>
        <br>
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password">
        <br>
        <label for="approve_user">Approve User:</label>
        <input type="checkbox" id="approve_user" name="approve_user" <?php echo $userDetails['approved'] ? 'checked' : ''; ?>>
        <br>
        <button type="submit">Update User Details</button>
    </form>
</body>

</html>
