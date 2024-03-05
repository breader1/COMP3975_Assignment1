<?php
include 'db_params.php'; // Include your database connection code

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
                echo "Your registration is pending approval. Please wait for admin approval.";
                
                //button to go back to index page
                echo "<br><br><button onclick='window.location.href=\"index.php\"'>Back</button>";
                
                exit();
            }
        }
    }

    // Invalid login credentials
    echo "Invalid login credentials.";
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
    <h2>Login</h2>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">Log In</button>
    </form>
</body>

</html>
