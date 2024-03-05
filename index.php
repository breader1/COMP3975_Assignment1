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
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="text-center">
            <h2>Welcome to the Homepage</h2>
            <button class="btn btn-primary" onclick="window.location.href='login.php'">Login</button>
            <button class="btn btn-success" onclick="window.location.href='register.php'">Register</button>
        </div>
    </div>

    <!-- Add Bootstrap JS (optional, if you plan to use Bootstrap JavaScript features) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>