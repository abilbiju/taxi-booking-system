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

// Fetch the customer's area
$sqlArea = "SELECT local_area FROM users WHERE id = ?";
$stmtArea = $conn->prepare($sqlArea);
$stmtArea->bind_param("i", $customer_id);
$stmtArea->execute();
$stmtArea->bind_result($customer_area);
$stmtArea->fetch();
$stmtArea->close();

// Fetch vehicles available in the customer's area
$vehicles = [];
$sqlVehicles = "SELECT id, model FROM vehicles WHERE driver_id IN (SELECT id FROM users WHERE local_area = ?)";
$stmtVehicles = $conn->prepare($sqlVehicles);
$stmtVehicles->bind_param("s", $customer_area);
$stmtVehicles->execute();
$stmtVehicles->bind_result($vehicle_id, $vehicle_model);

while ($stmtVehicles->fetch()) {
    $vehicles[] = ['id' => $vehicle_id, 'model' => $vehicle_model];
}
$stmtVehicles->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Create New Booking</title>
</head>
<body>
    <div class="container">
        <h1>Create New Booking</h1>

        <form action="process_booking.php" method="post">
            <label for="vehicle">Select a Vehicle:</label>
            <select name="vehicle_id" id="vehicle" required>
                <option value="">-- Select a Vehicle --</option>
                <?php foreach ($vehicles as $vehicle): ?>
                    <option value="<?php echo $vehicle['id']; ?>"><?php echo htmlspecialchars($vehicle['model']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="pickup_location">Pickup Location:</label>
            <input type="text" name="pickup_location" id="pickup_location" required>

            <label for="dropoff_location">Drop-off Location:</label>
            <input type="text" name="dropoff_location" id="dropoff_location" required>

            <input type="submit" value="Book Now">
        </form>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
