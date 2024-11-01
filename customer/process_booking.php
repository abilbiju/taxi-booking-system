<?php
session_start();
include '../db.php';

// Check if the user is a customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

// Get the customer ID from the session
$customer_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vehicle_id = $_POST['vehicle_id'];
    $pickup_location = $_POST['pickup_location'];
    $dropoff_location = $_POST['dropoff_location'];

    // Insert the booking into the database
    $sqlBooking = "INSERT INTO bookings (customer_id, driver_id, pickup_location, dropoff_location, booking_status, booking_date) VALUES (?, ?, ?, ?, 'Pending', NOW())";
    $stmtBooking = $conn->prepare($sqlBooking);
    $stmtBooking->bind_param("iiss", $customer_id, $vehicle_id, $pickup_location, $dropoff_location);

    if ($stmtBooking->execute()) {
        echo "Booking created successfully!";
        // Redirect or show success message
        header("Location: customer_dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmtBooking->error;
    }
    $stmtBooking->close();
}

// Close the database connection
$conn->close();
?>
