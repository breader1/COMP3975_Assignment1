<?php
session_start();

// Check if any user is logged in
if (!isset($_SESSION['user']) && !isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Display user-specific or admin-specific content
$username = isset($_SESSION['user']) ? $_SESSION['user'] : $_SESSION['admin'];
$role = isset($_SESSION['admin']) ? 'admin' : 'user';

echo "Welcome, $username! You are logged in as $role.<br><br>";

// Display admin-specific buttons
if ($role === 'admin') {
    echo '<button onclick="location.href=\'admin.php\'">Admin</button>';
    echo '<button onclick="location.href=\'edit_users.php\'">Edit Users</button>';
    echo '<button onclick="location.href=\'edit_buckets.php\'">Edit Buckets</button><br><br>';
}

?>
