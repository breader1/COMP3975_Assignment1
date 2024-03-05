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
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validate input (you might want to add more validation)
    if (empty($username) || empty($password)) {
        echo "Please enter both username and password.";
    } elseif (strlen($username) > 30 || strlen($password) > 30) {
        echo "Error: Username and password must be 30 characters or less.";
    } else {
        // Check if the username already exists
        $checkUserStmt = $usersDb->prepare('SELECT COUNT(*) FROM user WHERE username = :username');
        $checkUserStmt->bindParam(':username', $username, SQLITE3_TEXT);
        $result = $checkUserStmt->execute();
        $count = $result->fetchArray(SQLITE3_NUM)[0];

        if ($count > 0) {
            echo "Username already exists. Choose a different username.";
        } else {
            // Insert user data into the user table
            $insertUserStmt = $usersDb->prepare("INSERT INTO user (username, password, approved) VALUES (:username, :password, 1)");
            $insertUserStmt->bindParam(':username', $username, SQLITE3_TEXT);
            $insertUserStmt->bindParam(':password', $password, SQLITE3_TEXT);
            $insertUserStmt->execute();

            echo "User '$username' added successfully!";
            echo "<br>redirecting...";
            header("refresh:2;url=edit_users.php");
        }
    }
}

// Display the form for inserting a new user
echo '<h2>Insert User</h2>';
echo '<form action="insert_user.php" method="post">';
echo '  <label for="username">Username:</label>';
echo '  <input type="text" id="username" name="username" required><br>';
echo '  <label for="password">Password:</label>';
echo '  <input type="password" id="password" name="password" required><br>';
echo '  <br><br>';
echo '  <button type="submit">Insert User</button>';
echo '</form>';

// Link to go back to edit_users.php
echo '<br><br>';
echo '<button onclick="location.href=\'edit_users.php\'">Back to Edit Users</button>';

// End output buffering and flush the buffer
ob_end_flush();
?>
