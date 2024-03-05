<?php
include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code
echo '<br><button onclick="window.location.href=\'upload.php\'">Go to Upload Page</button><br><br>';
include 'display_waiting_users.php'; // Include your user display code
include 'footer.php'; // Include your footer code
?>
