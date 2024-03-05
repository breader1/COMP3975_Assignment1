<?php
session_start();
session_destroy();
include 'db_params.php'; // Include your database connection code


$usersDb->exec("CREATE TABLE IF NOT EXISTS user (
    username TEXT PRIMARY KEY,
    password TEXT,
    approved INTEGER DEFAULT 0
)");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
</head>

<body>
    <h2>Welcome to the Homepage</h2>

    <button onclick="window.location.href='login.php'">Login</button>
    <button onclick="window.location.href='register.php'">Register</button>
</body>

</html>
