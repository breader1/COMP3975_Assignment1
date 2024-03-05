<?php
// Start output buffering
ob_start();

include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo "Invalid admin credentials.";
    exit();
}

// Fetch all users
$getAllUsersStmt = $usersDb->prepare('SELECT username, approved FROM user');
$result = $getAllUsersStmt->execute();

// Start building the table
echo '<h2>Edit Users</h2>';

// Insert User button that redirects to insert_user.php
echo '<button onclick="location.href=\'insert_user.php\'">Insert User</button>';
echo '<br><br>';
echo '<table border="1">';
echo '<tr>';
echo '<th>Username</th>';
echo '<th>Approval Status</th>';
echo '<th>Actions</th>'; // New column for actions
echo '</tr>';

// Loop through the results and display each user in a table row
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo '<tr>';
    echo '<td>' . $row['username'] . '</td>';
    echo '<td>' . ($row['approved'] ? 'Approved' : 'Pending Approval') . '</td>';
    echo '<td>';
    echo '<a href="update_user.php?username=' . $row['username'] . '">Update</a>'; // Link to update_user.php
    echo ' | ';
    echo '<a href="delete_user.php?username=' . $row['username'] . '">Delete</a>'; // Link to delete_user.php
    echo '</td>';
    echo '</tr>';
}

// Finish the table
echo '</table>';

// End output buffering and flush the buffer
ob_end_flush();
?>
