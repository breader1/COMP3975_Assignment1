<?php
ob_start();
session_start();
session_destroy();
//go back to home
header('Location: index.php');
?>
<?php ob_end_flush(); ?>
