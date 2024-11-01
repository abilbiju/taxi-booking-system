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

// Fetch the customer's bookings
$sql = "SELECT id, pickup_location, dropoff_location, booking_status, booking_date FROM bookings WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->bind_result($booking_id, $pickup_location, $dropoff_location, $booking_status, $booking_date);
$bookings = [];
while ($stmt->fetch()) {
    $bookings[] = [
        'id' => $booking_id,
        'pickup' => $pickup_location,
        'dropoff' => $dropoff_location,
        'status' => $booking_status,
        'date' => $booking_date,
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Manage Bookings</title>
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
        <a href="customer_dashboard.php"><i class="fas fa-tachometer-alt"></i> <span class="menu-text">Dashboard</span></a>
        <a href="manage_bookings.php"><i class="fas fa-folder"></i><span class="menu-text">Manage Booking</span></a>
        <a href="manage_profile.php"><i class="fas fa-user"></i><span class="menu-text">Profile Details</span></a>
        <a href="../customer/logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a>
    </div>
</div>
<div class="container">
    <h1>Manage Bookings</h1>

    <?php if (empty($bookings)): ?>
        <p>No bookings found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pickup</th>
                    <th>Dropoff</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo $booking['id']; ?></td>
                        <td><?php echo htmlspecialchars($booking['pickup']); ?></td>
                        <td><?php echo htmlspecialchars($booking['dropoff']); ?></td>
                        <td><?php echo htmlspecialchars($booking['status']); ?></td>
                        <td><?php echo htmlspecialchars($booking['date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<script>
    document.getElementById('menu-toggle').onclick = function() {
        var sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    };
</script>
</body>
</html>
