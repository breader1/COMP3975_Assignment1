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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .username {
            font-weight: bold;
            color: #FFA533; /* Your preferred color for username */
        }
        .welcome-banner {
            background-color: #6c757d; /* The gray color for the banner */
            color: white;
            padding: 10px 0;
            margin-bottom: 20px; /* Separation from the content below */
        }

        .container {
            width: 80%; /* Adjust the width as needed */
            margin: auto;
        }

        .nav-buttons .btn {
            margin: 5px;
            background-color: #0056b3; /* Blue color for buttons */
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .nav-buttons .btn:hover {
            background-color: #0056b3; /* Darker blue for hover state */
        }

        .main-content {
            text-align: center; /* Center the content */
            width: 80%;
            margin: 20px auto;
        }

        /* Style for the main content heading */
        .main-content > h2 {
            margin: 0;
            padding: 10px;
            text-align: center;
        }

        /* Style for the 'Back to Edit Transactions' button */
        .btn-secondary.btn-block {
            background-color: #6c757d; /* Same color as the banner */
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            display: block;
        }
    </style>
</head>
<body>
    <div class="welcome-banner text-center">
        <h2>Welcome, <span class="username"><?php echo htmlspecialchars($username); ?></span>!</h2>
        <p>You are logged in as <?php echo htmlspecialchars($role); ?>.</p>
        <?php if ($role === 'admin'): ?>
            <div class="nav-buttons">
                <button class="btn btn-primary mr-2" onclick="location.href='admin.php'">Admin</button>
                <button class="btn btn-primary mr-2" onclick="location.href='edit_users.php'">Edit Users</button>
                <button class="btn btn-primary mr-2" onclick="location.href='edit_buckets.php'">Edit Buckets</button>
                <button class="btn btn-primary" onclick="location.href='edit_transactions.php'">Edit Transactions</button>
            </div>
        <?php endif; ?>
    </div>
    <!-- Rest of the body content will go here, including your forms and other UI elements -->
</body>
</html>
