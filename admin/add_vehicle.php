<?php
session_start();
include 'db_connection.php';

// Check if user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $renter_id = $_POST['renter_id'];
    $model = $_POST['model'];
    $plate_number = $_POST['plate_number'];
    $capacity = $_POST['capacity'];
    $fuel_type = $_POST['fuel_type'];

    // Insert vehicle into the database
    $sql = "INSERT INTO vehicles (renter_id, model, plate_number, capacity, fuel_type) VALUES ('$renter_id', '$model', '$plate_number', '$capacity', '$fuel_type')";

    if ($conn->query($sql) === TRUE) {
        header("Location: manage_vehicles.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header">
        <h2>Taxi Rental</h2>
        <div class="menu-toggle" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    <div class="sidebar-menu" id="sidebar-menu">
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
        <a href="manage_vehicles.php"><i class="fas fa-car"></i> Manage Vehicles</a>
        <a href="manage_cabs.php"><i class="fas fa-taxi"></i> Manage Cabs</a>
        <a href="manage_rentals.php"><i class="fas fa-folder"></i> Manage Rentals</a>
        <a href="manage_bookings.php"><i class="fas fa-file-alt"></i> Manage Bookings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>



    <h1>Add New Vehicle</h1>
    <form method="POST" action="">
        <label for="renter_id">Renter ID:</label>
        <input type="number" id="renter_id" name="renter_id" required><br><br>

        <label for="model">Model:</label>
        <input type="text" id="model" name="model" required><br><br>

        <label for="plate_number">Plate Number:</label>
        <input type="text" id="plate_number" name="plate_number" required><br><br>

        <label for="capacity">Capacity:</label>
        <input type="number" id="capacity" name="capacity" required><br><br>

        <label for="fuel_type">Fuel Type:</label>
        <select id="fuel_type" name="fuel_type" required>
            <option value="petrol">Petrol</option>
            <option value="diesel">Diesel</option>
            <option value="electric">Electric</option>
        </select><br><br>

        <input type="submit" value="Add Vehicle">
    </form>
    <a href="manage_vehicles.php">Back to Manage Vehicles</a>
    <script>
        // Handle sidebar toggle functionality
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');

        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('expanded');
        });
    </script>

</body>
</html>
