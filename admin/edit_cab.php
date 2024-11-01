<?php
session_start();
include 'db_connection.php';

// Check if user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if ID is set
if (isset($_GET['id'])) {
    $cab_id = $_GET['id'];

    // Fetch the current details of the cab
    $sql = "SELECT * FROM cabs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cab_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "No cab found with this ID.";
        exit();
    }

    $cab = $result->fetch_assoc();
} else {
    echo "No cab ID provided.";
    exit();
}

// Update the cab details if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $model = $_POST['model']; // Updated attribute name
    $plate_number = $_POST['plate_number'];
    $capacity = $_POST['capacity'];
    $fuel_type = $_POST['fuel_type'];
    $price_per_km = $_POST['price_per_km'];

    $update_sql = "UPDATE cabs SET model = ?, plate_number = ?, capacity = ?, fuel_type = ?, price_per_km = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssissi", $model, $plate_number, $capacity, $fuel_type, $price_per_km, $cab_id);

    if ($update_stmt->execute()) {
        header("Location: manage_cabs.php");
        exit();
    } else {
        echo "Error updating cab details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Cab</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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


    <div class="container">
        <h1>Edit Cab Details</h1>
        <form method="POST" action="">
            <label for="model">Cab Model:</label>
            <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($cab['model']); ?>" required><br><br>

            <label for="plate_number">Plate Number:</label>
            <input type="text" id="plate_number" name="plate_number" value="<?php echo htmlspecialchars($cab['plate_number']); ?>" required><br><br>

            <label for="capacity">Capacity:</label>
            <input type="number" id="capacity" name="capacity" value="<?php echo htmlspecialchars($cab['capacity']); ?>" required><br><br>

            <label for="fuel_type">Fuel Type:</label>
            <input type="text" id="fuel_type" name="fuel_type" value="<?php echo htmlspecialchars($cab['fuel_type']); ?>" required><br><br>

            <label for="price_per_km">Price per Km:</label>
            <input type="text" id="price_per_km" name="price_per_km" value="<?php echo htmlspecialchars($cab['price_per_km']); ?>" required><br><br>

            <input type="submit" value="Update Cab">
        </form>
    </div>
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
