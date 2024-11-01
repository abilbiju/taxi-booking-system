<?php
session_start();

// Destroy the session and log the user out
session_unset();  // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to login page
header("Location: ../driver/login.php");
exit();
?>
