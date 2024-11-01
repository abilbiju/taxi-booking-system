<?php
session_start();
include 'db_connection.php';
include 'functions.php';

// Check if user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetching report data
$users_count = getCount($conn, 'users');
$drivers_count = getCountByRole($conn, 'driver');
$bookings_count = getCount($conn, 'bookings');

// Fetching detailed user data for gender and area analysis
$users = fetchAll($conn, 'users');

// Count users by gender
$gender_counts = array_count_values(array_column($users, 'gender'));

// Count users by local area
$area_counts = array_count_values(array_column($users, 'local_area'));

// Prepare data for area and gender charts
$gender_labels = array_keys($gender_counts);
$gender_data = array_values($gender_counts);

$area_labels = array_keys($area_counts);
$area_data = array_values($area_counts);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Sidebar styles */
        .sidebar {
            width: 60px; /* Initial width */
            height: 100vh; /* Full height */
            background-color: #343a40; /* Dark background */
            position: fixed; /* Fixed position */
            transition: width 0.3s; /* Smooth width transition */
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
            align-items: center; /* Center items horizontally */
        }

        .sidebar-menu a {
            padding: 15px 0;
            color: white; /* Text color */
            text-decoration: none; /* Remove underline */
            display: flex; /* Flexbox for alignment */
            justify-content: center; /* Center icon & text horizontally */
            align-items: center; /* Center icon & text vertically */
            width: 100%; /* Full width */
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
            display: inline; /* Show text when expanded */
        }

        /* General page styles */
        body {
            background-image: url('https://pass-new-york.fr/wp-content/uploads/sites/10/2023/09/taxis-dans-avenue-new-york.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            margin: 30px auto;
            max-width: 1200px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

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

        h1 {
            margin-bottom: 20px;
        }

        .report-grid {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }

        .report-box {
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .report-box h2 {
            font-size: 36px;
            margin: 10px 0;
        }

        .report-box p {
            font-size: 16px;
            color: #666;
        }

        .report-box i {
            font-size: 50px;
            color: #007BFF;
            margin-bottom: 10px;
        }

        /* Table style */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #dee2e6;
            text-align: center;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
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
            <a href="reports.php"><i class="fas fa-chart-line"></i><span class="menu-text">Reports</span></a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a>
        </div>
    </div>

    <div class="account-section">
        <a href="account_settings.php">Change Password</a> |
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Reports</h1>

        <div class="report-grid">
            <div class="report-box">
                <i class="fas fa-users"></i>
                <h2><?php echo $users_count; ?></h2>
                <p>Total Users</p>
            </div>

            <div class="report-box">
                <i class="fas fa-user-tie"></i>
                <h2><?php echo $drivers_count; ?></h2>
                <p>Total Drivers</p>
            </div>

            <div class="report-box">
                <i class="fas fa-calendar-alt"></i>
                <h2><?php echo $bookings_count; ?></h2>
                <p>Total Bookings</p>
            </div>
        </div>

        <!-- Graphical Representation -->
        <h2>Graphical Representation</h2>

        <canvas id="reportsChart" style="max-width: 600px; margin: auto;"></canvas>
        <canvas id="genderChart" style="max-width: 600px; margin: auto; margin-top: 40px;"></canvas>
        <canvas id="areaChart" style="max-width: 600px; margin: auto; margin-top: 40px;"></canvas>

        <!-- Detailed User Report Table -->
        <h2>User Details</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Local Area</th>
                    <th>Gender</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td><?php echo htmlspecialchars($user['local_area']); ?></td>
                        <td><?php echo htmlspecialchars($user['gender']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Sidebar toggle script
        document.getElementById('menu-toggle').onclick = function() {
            var sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        };

        // Chart data and configuration
        var ctxReports = document.getElementById('reportsChart').getContext('2d');
        var reportsChart = new Chart(ctxReports, {
            type: 'bar',
            data: {
                labels: ['Total Users', 'Total Drivers', 'Total Bookings'],
                datasets: [{
                    label: 'Report Statistics',
                    data: [<?php echo $users_count; ?>, <?php echo $drivers_count; ?>, <?php echo $bookings_count; ?>],
                    backgroundColor: ['#007BFF', '#28A745', '#DC3545']
                }]
            }
        });

        var ctxGender = document.getElementById('genderChart').getContext('2d');
        var genderChart = new Chart(ctxGender, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($gender_labels); ?>,
                datasets: [{
                    label: 'Gender Distribution',
                    data: <?php echo json_encode($gender_data); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
                }]
            }
        });

        var ctxArea = document.getElementById('areaChart').getContext('2d');
        var areaChart = new Chart(ctxArea, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($area_labels); ?>,
                datasets: [{
                    label: 'Users by Area',
                    data: <?php echo json_encode($area_data); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                }]
            }
        });
    </script>
</body>
</html>
