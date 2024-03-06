<?php
include 'header.php'; // Include your header file
include 'session_check.php'; // Include your session check code
include 'db_params.php'; // Include your database connection code
?>

<div class="container mt-4"> <!-- Add margin-top -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <button class="btn btn-primary mb-3" onclick="window.location.href='upload.php'">Go to Upload Page</button>
            <?php include 'display_waiting_users.php'; // Include your user display code ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
