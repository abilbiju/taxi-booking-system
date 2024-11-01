<?php
session_start();
include 'db_connection.php';

// Check if user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM bookings WHERE id=$id";
$result = $conn->query($sql);
$booking = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $renter_id = $_POST['renter_id'];
    $vehicle_id = $_POST['vehicle_id'];
    $booking_time = $_POST['booking_time'];
    $status = $_POST['status'];

    $sql = "UPDATE bookings SET renter_id='$renter_id', vehicle_id='$vehicle_id', booking_time='$booking_time', status='$status' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Booking updated successfully";
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
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Edit Booking</title>
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



    <h1>Edit Booking</h1>
    <form method="POST" action="">
        <label for="renter_id">Renter ID:</label>
        <input type="number" id="renter_id" name="renter_id" value="<?php echo $booking['renter_id']; ?>" required><br><br>

        <label for="vehicle_id">Vehicle ID:</label>
        <input type="number" id="vehicle_id" name="vehicle_id" value="<?php echo $booking['vehicle_id']; ?>" required><br><br>

        <label for="booking_time">Booking Time:</label>
        <input type="datetime-local" id="booking_time" name="booking_time" value="<?php echo $booking['booking_time']; ?>" required><br><br>

        <label for="status">Status:</label>
        <input type="text" id="status" name="status" value="<?php echo $booking['status']; ?>" required><br><br>

        <input type="submit" value="Update Booking">
    </form>
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
