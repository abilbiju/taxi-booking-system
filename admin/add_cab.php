<?php
session_start();
include 'db_connection.php';

// Check if user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Initialize variables
$driver_id = "";

// Insert new cab into the database if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $model = $_POST['model'];
    $plate_number = $_POST['plate_number'];
    $capacity = $_POST['capacity'];
    $fuel_type = $_POST['fuel_type'];
    $price_per_km = $_POST['price_per_km'];
    $driver_id = $_POST['driver_id'] ?? ""; // Use null coalescing operator to prevent undefined index

    // Check if driver_id is provided
    if (empty($driver_id)) {
        echo "Please select a driver.";
    } else {
        // Prepare the SQL statement to prevent SQL injection
        $insert_sql = "INSERT INTO cabs (model, plate_number, capacity, fuel_type, price_per_km, driver_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssisis", $model, $plate_number, $capacity, $fuel_type, $price_per_km, $driver_id);

        if ($stmt->execute()) {
            header("Location: manage_cabs.php");
            exit();
        } else {
            echo "Error adding new cab.";
        }
    }
}

// Fetch all drivers for the dropdown
$drivers_sql = "SELECT id, username FROM users WHERE role = 'driver'"; // Adjusted to use username
$drivers_result = $conn->query($drivers_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Cab</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
        select {
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
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>

    <script>
        function handleDriverSelection(value) {
            if (value === "add_new") {
                window.location.href = 'add_driver.php'; // Change to the actual path of your add driver page
            }
        }
    </script>
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
        <h1>Add New Cab</h1>
        <form method="POST" action="">
            <label for="model">Cab Model:</label>
            <input type="text" id="model" name="model" required>

            <label for="plate_number">Plate Number:</label>
            <input type="text" id="plate_number" name="plate_number" required>

            <label for="capacity">Capacity:</label>
            <input type="number" id="capacity" name="capacity" required>

            <label for="fuel_type">Fuel Type:</label>
            <input type="text" id="fuel_type" name="fuel_type" required>

            <label for="price_per_km">Price per Km:</label>
            <input type="text" id="price_per_km" name="price_per_km" required>

            <label for="driver_id">Select Driver:</label>
            <select id="driver_id" name="driver_id" required onchange="handleDriverSelection(this.value)">
                <option value="">Select a driver</option>
                <?php while ($row = $drivers_result->fetch_assoc()) { ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['username']; ?></option>
                <?php } ?>
                <option value="add_new">Add New Driver</option> <!-- Option to add a new driver -->
            </select>

            <input type="submit" value="Add Cab">
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
