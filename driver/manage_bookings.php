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

// Fetch bookings for the driver
$bookingsQuery = "SELECT b.id, b.customer_id, b.pickup_location, b.dropoff_location, b.booking_status, b.booking_date 
                  FROM bookings b 
                  WHERE b.driver_id = ?";
$bookingsStmt = $conn->prepare($bookingsQuery);
$bookingsStmt->bind_param("i", $driver_id);
$bookingsStmt->execute();
$bookingsResult = $bookingsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Manage Bookings</title>
    <style>
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
            margin-left: 70px; /* Adjust the container margin */
            padding: 20px;
        }
        
        h1 {
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
    <div class="sidebar-menu" id="sidebar-menu">
        <a href="driver_dashboard.php"><i class="fas fa-tachometer-alt"></i> <span class="menu-text">Dashboard</span></a>
        <a href="manage_bookings.php"><i class="fas fa-folder"></i><span class="menu-text">Manage Booking</span></a>
        <a href="manage_profile.php"><i class="fas fa-user"></i><span class="menu-text">Profile Details</span></a>
        <a href="vehicle_details.php"><i class="fas fa-car"></i> <span class="menu-text">Vehicle Details</span></a>
        <a href="../driver/logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a>
    </div>
</div>

<div class="container">
    <h1>Your Bookings</h1>

    <?php if ($bookingsResult->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer ID</th>
                    <th>Pickup Location</th>
                    <th>Dropoff Location</th>
                    <th>Booking Status</th>
                    <th>Booking Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = $bookingsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['customer_id']); ?></td>
                        <td><?php echo htmlspecialchars($booking['pickup_location']); ?></td>
                        <td><?php echo htmlspecialchars($booking['dropoff_location']); ?></td>
                        <td><?php echo htmlspecialchars($booking['booking_status']); ?></td>
                        <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No bookings found.</p>
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
