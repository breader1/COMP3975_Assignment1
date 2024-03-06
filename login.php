<?php
include 'db_params.php'; // Include your database connection code
include 'header.php'; // Include your header file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $enteredUsername = $_POST['username'];
    $enteredPassword = $_POST['password'];

    // Check if the entered username exists in the admin table
    $checkAdminStmt = $usersDb->prepare('SELECT password FROM admin WHERE username = :username');
    $checkAdminStmt->bindParam(':username', $enteredUsername, SQLITE3_TEXT);
    $resultAdmin = $checkAdminStmt->execute();
    $rowAdmin = $resultAdmin->fetchArray(SQLITE3_ASSOC);

    // Check if the entered username exists in the user table
    $checkUserStmt = $usersDb->prepare('SELECT password, approved FROM user WHERE username = :username');
    $checkUserStmt->bindParam(':username', $enteredUsername, SQLITE3_TEXT);
    $resultUser = $checkUserStmt->execute();
    $rowUser = $resultUser->fetchArray(SQLITE3_ASSOC);

    if ($rowAdmin !== false) {
        // Admin login successful
        session_start();
        $_SESSION['admin'] = $enteredUsername;
        header('Location: admin.php');
        exit();
    } elseif ($rowUser !== false) {
        $userPassword = $rowUser['password'];
        $approvalStatus = $rowUser['approved'];

        if ($userPassword === $enteredPassword) {
            if ($approvalStatus == 1) {
                // User login successful
                session_start();
                $_SESSION['user'] = $enteredUsername;
                header('Location: upload.php');
                exit();
            } elseif ($approvalStatus == 0) {
                // User has not been approved
                echo '<br>';
                echo '<div class="alert alert-warning" role="alert">Your registration is pending approval. Please wait for admin approval.</div>';
                //button to go back to index page
                echo '<br><button onclick="window.location.href=\'index.php\'" class="btn btn-primary">Back</button>';
                exit();
            }
        }
    }

    // Invalid login credentials
    echo '<div class="alert alert-danger" role="alert">Invalid login credentials.</div>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <div class="container mt-5">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Log In</button>
        </form>
    </div>
</body>

</html>

<?php include 'footer.php'; ?>
