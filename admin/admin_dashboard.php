<?php
session_start();
include 'db_connection.php';
include 'functions.php';

// Check if user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
      /* Add background image */
      body {
            background-image: url('https://pass-new-york.fr/wp-content/uploads/sites/10/2023/09/taxis-dans-avenue-new-york.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
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

        /* Add styling for the account section */
        .account-section {
            position: absolute;
            top: 20px;
            right: 20px;
            text-align: right;
        }

        .account-section a {
            color: #fff;
            background-color: #007BFF;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .account-section a:hover {
            background-color: #0056b3;
        }

        /* Style for dashboard statistics */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 20px 20px;
        }

        .dashboard-box {
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .dashboard-box h2 {
            font-size: 36px;
            margin: 10px 0;
        }

        .dashboard-box p {
            font-size: 16px;
            color: #666;
        }

        .dashboard-box i {
            font-size: 50px;
            color: #007BFF;
            margin-bottom: 10px;
        }

        .manage-options {
            text-align: center;
        }

        /* Manage Options Section */
        .manage-section {
            position: relative;
            margin-left: 60px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-top: 40px;
        }

        .manage-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative; /* Required for absolute positioning of overlapping elements */
        }

        .manage-box a {
            display: block;
            margin: 10px 0;
            padding: 10px 0;
            color: #007BFF;
            text-decoration: none;
        }

        .manage-box:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="menu-toggle" id="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
        <div class="sidebar-menu" id="sidebar-menu">
            <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i><span class="menu-text">Dashboard</span></a>
            <a href="manage_users.php"><i class="fas fa-users"></i><span class="menu-text">Manage Users</span></a>
            <a href="manage_cabs.php"><i class="fas fa-taxi"></i><span class="menu-text">Manage Cabs</span></a>
            <a href="manage_bookings.php"><i class="fas fa-file-alt"></i><span class="menu-text">Manage Bookings</span></a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a>
        </div>
    </div>

    <!-- Account section in the top right corner -->
    <div class="account-section">
        <a href="account_settings.php">Change Password</a> |
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Welcome, Admin</h1>

        <!-- Display Statistics in Boxes -->
        <div class="dashboard-grid">
            <div class="dashboard-box">
                <i class="fas fa-users"></i>
                <h2><?php echo getCount($conn, 'users'); ?></h2>
                <p>Total Users</p>
            </div>

            <div class="dashboard-box">
                <i class="fas fa-user-tie"></i>
                <h2><?php echo getCountByRole($conn, 'driver'); ?></h2>
                <p>Total Drivers</p>
            </div>

            <div class="dashboard-box">
                <i class="fas fa-taxi"></i>
                <h2><?php echo getCount($conn, 'cabs'); ?></h2>
                <p>Total Cabs</p>
            </div>

            <div class="dashboard-box">
                <i class="fas fa-file-alt"></i>
                <h2><?php echo getCount($conn, 'bookings'); ?></h2>
                <p>Total Bookings</p>
            </div>
        </div>

        <!-- Manage Options Section -->
        <div class="manage-options">
            <h2>Manage Options</h2>
        </div>
        <div class="manage-section">
            <div class="manage-box">
                <h3>Users</h3>
                <a href="manage_users.php">Manage Users</a>
            </div>
            <div class="manage-box">
                <h3>Cabs</h3>
                <a href="manage_cabs.php">Manage Cabs</a>
            </div>
            <div class="manage-box">
                <h3>Bookings</h3>
                <a href="manage_bookings.php">Manage Bookings</a>
            </div>
            <div class="manage-box">
                <h3>Report</h3>
                <a href="reports.php">View Report</a>
            </div>
        </div>
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
