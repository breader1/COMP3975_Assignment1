<?php
session_start();
session_destroy();
include 'db_params.php'; // Include your database connection code
include 'header.php'; // Include your header code


$usersDb->exec("CREATE TABLE IF NOT EXISTS user (
    username TEXT PRIMARY KEY,
    password TEXT,
    approved INTEGER DEFAULT 0
)");
?>

<!DOCTYPE html>
<html lang="en">
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="text-center">
            <h2>Welcome to the Homepage</h2>
            <button class="btn btn-primary" onclick="window.location.href='login.php'">Login</button>
            <button class="btn btn-success" onclick="window.location.href='register.php'">Register</button>
        </div>
    </div>
</body>

</html>