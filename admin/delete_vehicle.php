<?php
session_start();
include 'db_connection.php';

// Check if user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];
$sql = "DELETE FROM vehicles WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    echo "Vehicle deleted successfully";
} else {
    echo "Error deleting vehicle: " . $conn->error;
}

header("Location: manage_vehicles.php");
?>
