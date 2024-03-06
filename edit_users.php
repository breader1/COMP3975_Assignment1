<?php
// Start output buffering
ob_start();

include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    echo '<div class="alert alert-danger" role="alert">Invalid admin credentials.</div>';
    exit();
}

// Fetch all users
$getAllUsersStmt = $usersDb->prepare('SELECT username, approved FROM user');
$result = $getAllUsersStmt->execute();

// Start building the table
echo '<div class="container">';
echo '<h2 class="my-4">Users</h2>';

// Insert User button that redirects to insert_user.php
echo '<a class="btn btn-primary mb-3" href="insert_user.php">Insert User</a>';
echo '<table class="table table-bordered">';
echo '<thead class="thead-dark">';
echo '<tr>';
echo '<th>Username</th>';
echo '<th>Approval Status</th>';
echo '<th>Actions</th>'; // New column for actions
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Loop through the results and display each user in a table row
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo '<tr>';
    echo '<td>' . $row['username'] . '</td>';
    echo '<td>' . ($row['approved'] ? 'Approved' : 'Pending Approval') . '</td>';
    echo '<td>';
    echo '<a class="btn btn-sm btn-primary mr-2" href="update_user.php?username=' . $row['username'] . '">Update</a>'; // Link to update_user.php
    echo '<a class="btn btn-sm btn-danger" href="delete_user.php?username=' . $row['username'] . '">Delete</a>'; // Link to delete_user.php
    echo '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';
echo '</div>';

// End output buffering and flush the buffer
ob_end_flush();
?>
<?php include 'footer.php'; ?>
