<?php
session_start();
include 'db_connection.php';

// Check if user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Insert new booking into the database if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $renter_id = $_POST['renter_id'];
    $vehicle_id = $_POST['vehicle_id'];
    $booking_time = $_POST['booking_time'];
    $status = $_POST['status'];
    $cash = $_POST['cash']; // Cash earned for the booking

    // Prepare the SQL statement to prevent SQL injection
    $sql = "INSERT INTO bookings (renter_id, vehicle_id, booking_time, status, cash) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissd", $renter_id, $vehicle_id, $booking_time, $status, $cash);

    if ($stmt->execute()) {
        echo "New booking created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Add New Booking</title>
    <style>
        body {
            background-color: #f4f4f9; /* Light background */
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Sidebar styling */
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

        /* Form styling */
        form {
            background-color: white; /* Form background */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        input[type="datetime-local"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
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

    <div class="container">
        <h1>Add New Booking</h1>
        <form method="POST" action="">
            <label for="renter_id">Renter ID:</label>
            <input type="number" id="renter_id" name="renter_id" required>

            <label for="vehicle_id">Vehicle ID:</label>
            <input type="number" id="vehicle_id" name="vehicle_id" required>

            <label for="booking_time">Booking Time:</label>
            <input type="datetime-local" id="booking_time" name="booking_time" required>

            <label for="status">Status:</label>
            <input type="text" id="status" name="status" required>

            <label for="cash">Cash Earned:</label>
            <input type="number" step="0.01" id="cash" name="cash" required>

            <input type="submit" value="Add Booking">
        </form>
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
