<?php
session_start();
session_destroy();
//go back to home
header('Location: index.php');
?>
