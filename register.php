<?php
include 'db_params.php'; // Include your database connection code
include 'header.php'; // Include your header code

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $username = $_POST['username'];
    $password = $_POST['password']; // No need to hash the password

    // Insert user data into the user table
    $insertUserStmt = $usersDb->prepare("INSERT INTO user (username, password) VALUES (:username, :password)");
    $insertUserStmt->bindParam(':username', $username);
    $insertUserStmt->bindParam(':password', $password);
    $insertUserStmt->execute();

    echo '<div class="container mt-5">';
    echo '<div class="alert alert-success" role="alert">';
    echo '<h4 class="alert-heading">Registration successful!</h4>';
    echo '<p>Thank you for registering. Your account is pending admin approval.</p>';
    echo '<hr>';
    echo '<p class="mb-0">Redirecting to home page...</p>';
    echo '</div>';
    echo '</div>';

    // Redirect to home page after 3 seconds
    header('Refresh: 3; URL=index.php');
}
?>

<div class="container">
    <h2 class="mt-5">User Registration</h2>
    <form class="mt-3" action="register.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>
