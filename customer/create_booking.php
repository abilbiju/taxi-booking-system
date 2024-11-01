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

// Check if vehicle ID is passed in the URL
if (!isset($_GET['vehicle_id'])) {
    header("Location: customer_dashboard.php");
    exit();
}

$vehicle_id = $_GET['vehicle_id'];

// Fetch vehicle details
$sqlVehicle = "SELECT * FROM cabs WHERE id = ?";
$stmtVehicle = $conn->prepare($sqlVehicle);
$stmtVehicle->bind_param("i", $vehicle_id);
$stmtVehicle->execute();
$resultVehicle = $stmtVehicle->get_result();

if ($resultVehicle->num_rows === 0) {
    header("Location: customer_dashboard.php");
    exit();
}

$vehicle = $resultVehicle->fetch_assoc();
$stmtVehicle->close();

// Process booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup_location = $_POST['pickup_location'];
    $dropoff_location = $_POST['dropoff_location'];
    $booking_date = date('Y-m-d H:i:s'); // Current date and time

    // Insert booking into the database
    $sqlBooking = "INSERT INTO bookings (customer_id, driver_id, pickup_location, dropoff_location, booking_status, booking_date) VALUES (?, ?, ?, ?, 'Pending', ?)";
    $stmtBooking = $conn->prepare($sqlBooking);
    $stmtBooking->bind_param("iisss", $customer_id, $vehicle['driver_id'], $pickup_location, $dropoff_location, $booking_date);

    if ($stmtBooking->execute()) {
        $success_message = "Booking created successfully!";
        $stmtBooking->close();
    } else {
        $error_message = "Error creating booking: " . $stmtBooking->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Create Booking</title>
    <style>
        /* Add your CSS styles here */
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 20px auto;
            max-width: 600px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #007BFF;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #007BFF;
            border-radius: 5px;
        }
        .form-group button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 20px;
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Booking</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="message"><?php echo htmlspecialchars($success_message); ?></div>
            <a href="customer_dashboard.php">Back to Dashboard</a>
        <?php elseif (isset($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="pickup_location">Pickup Location:</label>
                    <input type="text" name="pickup_location" id="pickup_location" required>
                </div>
                <div class="form-group">
                    <label for="dropoff_location">Drop-off Location:</label>
                    <input type="text" name="dropoff_location" id="dropoff_location" required>
                </div>
                <button type="submit">Create Booking</button>
            </form>
        <?php endif; ?>
        
        <h2>Vehicle Details</h2>
        <p><strong>Model:</strong> <?php echo htmlspecialchars($vehicle['model']); ?></p>
        <p><strong>Plate Number:</strong> <?php echo htmlspecialchars($vehicle['plate_number']); ?></p>
        <p><strong>Capacity:</strong> <?php echo htmlspecialchars($vehicle['capacity']); ?></p>
        <p><strong>Fuel Type:</strong> <?php echo htmlspecialchars($vehicle['fuel_type']); ?></p>
        <p><strong>Rate per Distance:</strong> ₹<?php echo htmlspecialchars($vehicle['price_per_km']); ?> per km</p>
    </div>
</body>
</html>
