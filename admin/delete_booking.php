<?php
session_start();
include 'db_connection.php';

// Check if user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];
$sql = "DELETE FROM bookings WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    echo "Booking deleted successfully";
} else {
    echo "Error deleting booking: " . $conn->error;
}

header("Location: manage_bookings.php");
?>
