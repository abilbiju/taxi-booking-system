<?php
session_start();
include '../db.php';

// Check if user is a driver
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'driver') {
    header("Location: login.php");
    exit();
}

// Get the driver ID from the session
$driver_id = $_SESSION['id']; // Assuming the session stores the driver's ID

// Fetch the driver's name from the database
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$stmt->bind_result($driver_name);
$stmt->fetch();
$stmt->close();

// If driver name is not found, set a default value
if (empty($driver_name)) {
    $driver_name = 'Driver'; // Default fallback
}

// Fetch statistics: total bookings and cash earned
$totalBookings = 0;
$totalCash = 0;

// Fetch total bookings
$sqlBookings = "SELECT COUNT(*) FROM bookings WHERE driver_id = ?";
$stmtBookings = $conn->prepare($sqlBookings);
$stmtBookings->bind_param("i", $driver_id);
$stmtBookings->execute();
$stmtBookings->bind_result($totalBookings);
$stmtBookings->fetch();
$stmtBookings->close();

// Fetch total cash earned
$sqlCash = "SELECT SUM(cash) FROM bookings WHERE driver_id = ?";
$stmtCash = $conn->prepare($sqlCash);
$stmtCash->bind_param("i", $driver_id);
$stmtCash->execute();
$stmtCash->bind_result($totalCash);
$stmtCash->fetch();
$stmtCash->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Driver Dashboard</title>
    <style>
        body {
            background-color: #f4f4f9; /* Light background */
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 60px; /* Initial width of the sidebar */
            height: 100vh; /* Full height */
            background-color: #343a40; /* Dark background */
            position: fixed; /* Fixed position */
            transition: width 0.3s; /* Smooth transition for width change */
        }

        .sidebar-header {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60px; /* Height of the header */
            background-color: #007BFF; /* Header background */
        }

        .menu-toggle {
            cursor: pointer;
            color: white;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            align-items: center; /* Center the items horizontally */
        }

        .sidebar-menu a {
            padding: 15px 0;
            color: white; /* Text color */
            text-decoration: none; /* Remove underline */
            display: flex; /* Use flexbox for alignment */
            justify-content: center; /* Center the icon and text horizontally */
            align-items: center; /* Center the icon and text vertically */
            width: 100%; /* Full width of the sidebar */
        }

        .sidebar-menu .menu-text {
            display: none; /* Hide text initially */
            margin-left: 10px; /* Space between icon and text */
        }

        .sidebar-menu a:hover {
            background-color: #007BFF; /* Hover color */
        }

        .sidebar.active {
            width: 200px; /* Expanded width */
        }

        .sidebar.active .menu-text {
            display: inline; /* Show text when active */
        }

        .container {
            margin-left: 220px; /* Adjust for the sidebar */
            padding: 20px;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .stat-box {
            background-color: #007BFF;
            color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            width: 30%; /* Adjust width as needed */
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
    <div class="sidebar-menu" id="sidebar-menu">
        <a href="driver_dashboard.php"><i class="fas fa-tachometer-alt"></i> <span class="menu-text">Dashboard</span></a>
        <a href="manage_bookings.php"><i class="fas fa-folder"></i><span class="menu-text">Manage Booking</span></a>
        <a href="manage_profile.php"><i class="fas fa-user"></i><span class="menu-text">Profile Details</span></a>
        <a href="vehicle_details.php"><i class="fas fa-car"></i> <span class="menu-text">Vehicle Details</span></a>
        <a href="../driver/logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a>
    </div>
</div>

<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($driver_name); ?></h1>
    
    <div class="stats">
        <div class="stat-box">
            <h2><?php echo $totalBookings; ?></h2>
            <p>Total Bookings</p>
        </div>
        <div class="stat-box">
            <h2><?php echo number_format($totalCash, 2); ?> ₹</h2>
            <p>Total Cash Earned</p>
        </div>
    </div>
    
    <p>Use the menu to manage your bookings and profile.</p>
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
