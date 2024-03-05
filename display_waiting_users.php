<?php
// Assuming you have already established a connection to the users database
include 'db_params.php';

// Fetch users waiting for approval
$getWaitingUsersStmt = $usersDb->prepare('SELECT username FROM user WHERE approved = 0');
$result = $getWaitingUsersStmt->execute();

echo "<form action='approve_user.php' method='post'>
        <table border='1'>
            <tr>
                <th>Username</th>
                <th>Action</th>
            </tr>";

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo "<tr>
            <td>{$row['username']}</td>
            <td><button type='submit' name='approve_user' value='{$row['username']}'>Approve</button></td>
          </tr>";
}

echo "</table></form>";

// Close the users database connection
$usersDb->close();
?>
