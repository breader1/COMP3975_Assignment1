<?php
include 'db_params.php'; // Include your database connection code

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $username = $_POST['username'];
    $password = $_POST['password']; // No need to hash the password

    // Insert user data into the user table
    $insertUserStmt = $usersDb->prepare("INSERT INTO user (username, password) VALUES (:username, :password)");
    $insertUserStmt->bindParam(':username', $username);
    $insertUserStmt->bindParam(':password', $password);
    $insertUserStmt->execute();

    echo "Registration successful. Waiting for admin approval.";
    echo "<br><br>Redirecting to home page...";
    //rediect to home page after 2 seconds
    header('Refresh: 3; URL=index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
</head>
<body>
    <h2>User Registration</h2>
    <form action="register.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
