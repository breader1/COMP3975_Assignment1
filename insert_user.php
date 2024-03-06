<?php
// Start output buffering
ob_start();

include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo '<div class="alert alert-danger" role="alert">Invalid admin credentials.</div>';
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validate input (you might want to add more validation)
    if (empty($username) || empty($password)) {
        echo '<div class="alert alert-danger" role="alert">Please enter both username and password.</div>';
    } elseif (strlen($username) > 30 || strlen($password) > 30) {
        echo '<div class="alert alert-danger" role="alert">Error: Username and password must be 30 characters or less.</div>';
    } else {
        // Check if the username already exists
        $checkUserStmt = $usersDb->prepare('SELECT COUNT(*) FROM user WHERE username = :username');
        $checkUserStmt->bindParam(':username', $username, SQLITE3_TEXT);
        $result = $checkUserStmt->execute();
        $count = $result->fetchArray(SQLITE3_NUM)[0];

        if ($count > 0) {
            echo '<div class="alert alert-danger" role="alert">Username already exists. Choose a different username.</div>';
        } else {
            // Insert user data into the user table
            $insertUserStmt = $usersDb->prepare("INSERT INTO user (username, password, approved) VALUES (:username, :password, 1)");
            $insertUserStmt->bindParam(':username', $username, SQLITE3_TEXT);
            $insertUserStmt->bindParam(':password', $password, SQLITE3_TEXT);
            $insertUserStmt->execute();

            echo "<br>";
            echo '<div class="alert alert-success" role="alert">User \'' . htmlspecialchars($username) . '\' added successfully! <br>redirecting...</div>';
            header("refresh:2;url=edit_users.php");
        }
    }
}

// Display the form for inserting a new user
?>
<div class="container mt-5">
    <h2>Insert User</h2>
    <form action="insert_user.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Insert User</button>
    </form>
    <br><br>
    <button class="btn btn-secondary" onclick="location.href='edit_users.php'">Back to Users</button>
</div>
<?php
// End output buffering and flush the buffer
ob_end_flush();
?>
<?php include 'footer.php'; ?>