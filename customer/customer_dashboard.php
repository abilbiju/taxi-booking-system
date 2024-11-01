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

// Fetch the customer's name from the database
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->bind_result($customer_name);
$stmt->fetch();
$stmt->close();

// If customer name is not found, set a default value
if (empty($customer_name)) {
    $customer_name = 'Customer';
}

// Fetch available areas
$areas = [];
$sqlAreas = "SELECT DISTINCT local_area FROM users WHERE local_area NOT LIKE 'Admin Area' AND local_area IS NOT NULL";
$stmtAreas = $conn->prepare($sqlAreas);
$stmtAreas->execute();
$result = $stmtAreas->get_result();
while ($row = $result->fetch_assoc()) {
    $areas[] = $row['local_area'];
}
$stmtAreas->close();

// Fetch vehicles based on the selected area, if any
$vehicles = [];
$local_area = isset($_POST['area']) ? $_POST['area'] : null;
if ($local_area) {
    $sqlVehicles = "SELECT * FROM cabs WHERE driver_id IN (SELECT id FROM users WHERE local_area = ?)";
    $stmtVehicles = $conn->prepare($sqlVehicles);
    $stmtVehicles->bind_param("s", $local_area);
    $stmtVehicles->execute();
    $result = $stmtVehicles->get_result();
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
    $stmtVehicles->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Customer Dashboard</title>
    <style>
        /* Add your CSS styles here */
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
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-left: 220px;
            padding: 20px;
            max-width: 800px; 
            margin: auto; 
        }
        h1 {
            color: #007BFF;
        }
        .area-selection {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .area-selection label {
            font-weight: bold;
            margin-right: 10px;
        }
        .area-selection select {
            padding: 10px;
            border: 1px solid #007BFF;
            border-radius: 5px;
            width: calc(100% - 20px);
            margin-right: 10px;
            transition: border-color 0.3s;
        }
        .area-selection button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .vehicle-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }
        .vehicle-item {
            padding: 15px;
            background-color: #ffffff;
            border: 1px solid #007BFF;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #28a745; 
            color: white;
            text-decoration: none;
            border-radius: 5px;
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
        <a href="customer_dashboard.php"><i class="fas fa-tachometer-alt"></i> <span class="menu-text">Dashboard</span></a>
        <a href="manage_bookings.php"><i class="fas fa-folder"></i><span class="menu-text">Manage Bookings</span></a>
        <a href="manage_profile.php"><i class="fas fa-user"></i><span class="menu-text">Profile Details</span></a>
        <a href="../customer/logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a>
    </div>
</div>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($customer_name); ?></h1>

        <form method="POST" class="area-selection">
            <label for="area">Select Your Area:</label>
            <select name="area" id="area" required>
                <option value="">--Select an Area--</option>
                <?php foreach ($areas as $area): ?>
                    <option value="<?php echo htmlspecialchars($area); ?>" <?php echo ($area === $local_area) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($area); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Show Vehicles</button>
        </form>

        <?php if ($local_area): ?>
            <h2>Available Cabs in <?php echo htmlspecialchars($local_area); ?>:</h2>
            <div class="vehicle-list">
                <?php if (count($vehicles) > 0): ?>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <div class="vehicle-item">
                            <p><strong>Model:</strong> <?php echo htmlspecialchars($vehicle['model']); ?></p>
                            <p><strong>Plate Number:</strong> <?php echo htmlspecialchars($vehicle['plate_number']); ?></p>
                            <p><strong>Capacity:</strong> <?php echo htmlspecialchars($vehicle['capacity']); ?></p>
                            <p><strong>Fuel Type:</strong> <?php echo htmlspecialchars($vehicle['fuel_type']); ?></p>
                            <p><strong>Rate per Distance:</strong> ₹<?php echo htmlspecialchars($vehicle['price_per_km']); ?> per km</p>
                            <a href="create_booking.php?vehicle_id=<?php echo $vehicle['id']; ?>" class="button">Book This Cab</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No vehicles available in your selected area.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <script>
    // Toggle sidebar visibility
    document.getElementById('menu-toggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
</body>
</html>
