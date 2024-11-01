<?php
session_start();
include 'db_connection.php';

// Check if user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];
$sql = "DELETE FROM rentals WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    echo "Rental deleted successfully";
} else {
    echo "Error deleting rental: " . $conn->error;
}

header("Location: manage_rentals.php");
?>
