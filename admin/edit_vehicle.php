<?php
session_start();
include 'db_connection.php';

// Check if user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM vehicles WHERE id=$id";
$result = $conn->query($sql);
$vehicle = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vehicle_number = $_POST['vehicle_number'];
    $type = $_POST['type'];
    $capacity = $_POST['capacity'];
    $price_per_hour = $_POST['price_per_hour'];

    $sql = "UPDATE vehicles SET vehicle_number='$vehicle_number', type='$type', capacity='$capacity', price_per_hour='$price_per_hour' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Vehicle updated successfully";
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
    <title>Edit Vehicle</title>
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



    <h1>Edit Vehicle</h1>
    <form method="POST" action="">
        <label for="vehicle_number">Vehicle Number:</label>
        <input type="text" id="vehicle_number" name="vehicle_number" value="<?php echo $vehicle['vehicle_number']; ?>" required><br><br>

        <label for="type">Type:</label>
        <input type="text" id="type" name="type" value="<?php echo $vehicle['type']; ?>" required><br><br>

        <label for="capacity">Capacity:</label>
        <input type="number" id="capacity" name="capacity" value="<?php echo $vehicle['capacity']; ?>" required><br><br>

        <label for="price_per_hour">Price per Hour:</label>
        <input type="number" id="price_per_hour" name="price_per_hour" value="<?php echo $vehicle['price_per_hour']; ?>" required><br><br>

        <input type="submit" value="Update Vehicle">
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
