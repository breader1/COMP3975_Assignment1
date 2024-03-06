<?php
include 'db_params.php';

echo '<div class="container mt-4">'; // Add margin-top and start container
echo '<div class="row justify-content-center">'; // Center the column
echo '<div class="col-md-8">'; // Set the column width to medium (col-md-8)

echo '<form action="approve_user.php" method="post">';
echo '<table class="table table-bordered table-striped">';
echo '<thead class="thead-dark">';
echo '<tr>';
echo '<th scope="col">Username</th>';
echo '<th scope="col">Action</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Fetch users waiting for approval
$getWaitingUsersStmt = $usersDb->prepare('SELECT username FROM user WHERE approved = 0');
$result = $getWaitingUsersStmt->execute();

// Fetch the first row to check if there are waiting users
$firstRow = $result->fetchArray(SQLITE3_ASSOC);

if ($firstRow === false) {
    // If there are no users waiting for approval
    echo '<tr><td colspan="2" class="text-center">No users waiting for approval.</td></tr>';
} else {
    // If there are users, display the first row
    echo "<tr>";
    echo "<td>{$firstRow['username']}</td>";
    echo '<td><button type="submit" name="approve_user" value="' . $firstRow['username'] . '" class="btn btn-primary">Approve</button></td>';
    echo "</tr>";

    // Display the rest of the users waiting for approval
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['username']}</td>";
        echo '<td><button type="submit" name="approve_user" value="' . $row['username'] . '" class="btn btn-primary">Approve</button></td>';
        echo "</tr>";
    }
}

echo '</tbody>';
echo '</table>';
echo '</form>';

echo '</div>'; // Close the col-md-8 div
echo '</div>'; // Close the row div
echo '</div>'; // Close the container div

// Close the users database connection
$usersDb->close();
?>
<?php include 'footer.php'; ?>
