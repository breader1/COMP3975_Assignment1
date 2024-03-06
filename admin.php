<?php
include 'header.php'; // Include your header file
include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code
?>

<div class="container mt-4"> <!-- Add margin-top -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php include 'display_waiting_users.php'; // Include your user display code ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
