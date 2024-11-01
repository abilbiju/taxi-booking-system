<?php
session_start();
include '../db.php';

// Check if the user is a driver
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'driver') {
    header("Location: login.php");
    exit();
}

// Get the driver_id from the session
$driver_id = $_SESSION['id'];

// Fetch the vehicles associated with the logged-in driver
$query = "SELECT model, plate_number, capacity, fuel_type, price_per_km, availability FROM cabs WHERE driver_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Vehicle Details</title>
    <style>
        .sidebar {
            width: 60px;
            height: 100vh;
            background-color: #343a40;
            position: fixed;
            transition: width 0.3s;
        }

        .sidebar-header {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60px;
            background-color: #007BFF;
        }

        .menu-toggle {
            cursor: pointer;
            color: white;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar-menu a {
            padding: 15px 0;
            color: white;
            text-decoration: none;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .sidebar-menu .menu-text {
            display: none;
            margin-left: 10px;
        }

        .sidebar-menu a:hover {
            background-color: #007BFF;
        }

        .sidebar.active {
            width: 200px;
        }

        .sidebar.active .menu-text {
            display: inline;
        }

        .container {
            margin-left: 70px; /* Adjust the container margin */
            padding: 20px;
        }

        .container h1 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007BFF;
            color: white;
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="menu-toggle" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    <div class="sidebar-menu">
        <a href="driver_dashboard.php"><i class="fas fa-tachometer-alt"></i> <span class="menu-text">Dashboard</span></a>
        <a href="manage_bookings.php"><i class="fas fa-folder"></i><span class="menu-text">Manage Bookings</span></a>
        <a href="manage_profile.php"><i class="fas fa-user"></i><span class="menu-text">Profile Details</span></a>
        <a href="vehicle_details.php"><i class="fas fa-car"></i> <span class="menu-text">Vehicle Details</span></a>
        <a href="../driver/logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a>
    </div>
</div>

<div class="container">
    <h1>Vehicle Details</h1>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Model</th>
                    <th>Plate Number</th>
                    <th>Capacity</th>
                    <th>Fuel Type</th>
                    <th>Price per Km</th>
                    <th>Availability</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['model']); ?></td>
                        <td><?php echo htmlspecialchars($row['plate_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['capacity']); ?></td>
                        <td><?php echo htmlspecialchars($row['fuel_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['price_per_km']); ?></td>
                        <td><?php echo htmlspecialchars($row['availability']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No vehicles are assigned to you.</p>
    <?php endif; ?>

</div>

<script>
    // Sidebar toggle functionality
    document.getElementById('menu-toggle').onclick = function() {
        var sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    };
</script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
