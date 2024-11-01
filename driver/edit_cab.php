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

// Check if cab ID is provided
if (!isset($_GET['id'])) {
    header("Location: manage_profile.php"); // Redirect if no cab ID is given
    exit();
}

$cab_id = $_GET['id'];

// Fetch the cab details
$cabQuery = "SELECT id, model, plate_number, capacity, fuel_type, price_per_km, availability FROM cabs WHERE id = ? AND driver_id = ?";
$cabStmt = $conn->prepare($cabQuery);
$cabStmt->bind_param("ii", $cab_id, $driver_id);
$cabStmt->execute();
$cabResult = $cabStmt->get_result();

if ($cabResult->num_rows === 0) {
    // Redirect if cab not found
    header("Location: manage_profile.php");
    exit();
}

$cab = $cabResult->fetch_assoc();

// Update cab details if the form is submitted
if (isset($_POST['update_cab'])) {
    $model = $_POST['model'];
    $plate_number = $_POST['plate_number'];
    $capacity = $_POST['capacity'];
    $fuel_type = $_POST['fuel_type'];
    $price_per_km = $_POST['price_per_km'];
    $availability = $_POST['availability'];

    $updateCabQuery = "UPDATE cabs SET model = ?, plate_number = ?, capacity = ?, fuel_type = ?, price_per_km = ?, availability = ? WHERE id = ? AND driver_id = ?";
    $updateCabStmt = $conn->prepare($updateCabQuery);
    $updateCabStmt->bind_param("ssisssii", $model, $plate_number, $capacity, $fuel_type, $price_per_km, $availability, $cab_id, $driver_id);

    if ($updateCabStmt->execute()) {
        $success_message = "Cab details updated successfully.";
    } else {
        $error_message = "Error updating cab details. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Edit Cab</title>
    <style>

    .container {
    margin-left: 70px; /* Adjust the container margin */
    padding: 20px;
    background-color: #f8f9fa; /* Light background for the container */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}

h1 {
    margin-bottom: 20px;
    color: #007BFF; /* Header color */
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold; /* Make labels bold */
}

input[type="text"],
input[type="email"],
input[type="tel"],
input[type="password"],
input[type="number"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    transition: border 0.3s; /* Smooth transition for border */
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="tel"]:focus,
input[type="password"]:focus,
input[type="number"]:focus {
    border-color: #007BFF; /* Highlight border on focus */
    outline: none; /* Remove outline */
}

.btn {
    margin-top:20px;
    background-color: #007BFF; /* Primary button color */
    color: white;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s; /* Smooth transition for background */
}
.backbtn {
    margin-top:20px;
    background-color: #007BFF; /* Primary button color */
    color: white;
    border: none;
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s; /* Smooth transition for background */
}
.btn:hover {
    background-color: #0056b3; /* Darker blue on hover */
}
.backbtn:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

.error {
    color: red; /* Error message color */
}

.success {
    color: green; /* Success message color */
}
</style>
</head>
<body>

<div class="container">
    <h1>Edit Cab Details</h1>
    
    <?php if (isset($success_message)): ?>
        <p class="success"><?php echo $success_message; ?></p>
    <?php elseif (isset($error_message)): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="model">Model</label>
            <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($cab['model']); ?>" required>
        </div>
        <div class="form-group">
            <label for="plate_number">Plate Number</label>
            <input type="text" id="plate_number" name="plate_number" value="<?php echo htmlspecialchars($cab['plate_number']); ?>" required>
        </div>
        <div class="form-group">
            <label for="capacity">Capacity</label>
            <input type="number" id="capacity" name="capacity" value="<?php echo htmlspecialchars($cab['capacity']); ?>" required>
        </div>
        <div class="form-group">
            <label for="fuel_type">Fuel Type</label>
            <input type="text" id="fuel_type" name="fuel_type" value="<?php echo htmlspecialchars($cab['fuel_type']); ?>" required>
        </div>
        <div class="form-group">
            <label for="price_per_km">Price per Km</label>
            <input type="number" id="price_per_km" name="price_per_km" value="<?php echo htmlspecialchars($cab['price_per_km']); ?>" required>
        </div>
        <div class="form-group">
    <label for="availability">Availability</label>
    <select id="availability" name="availability" required>
        <option value="available" <?php echo $cab['availability'] === 'available' ? 'selected' : ''; ?>>Available</option>
        <option value="not available" <?php echo $cab['availability'] === 'not available' ? 'selected' : ''; ?>>Not Available</option>
        <option value="in maintenance" <?php echo $cab['availability'] === 'in maintenance' ? 'selected' : ''; ?>>In Maintenance</option>
        <!-- Add more options as needed -->
    </select>
</div>

        <button type="submit" name="update_cab" class="backbtn">Update Cab</button>
    </form>

    <a href="manage_profile.php" class="btn">Back to Profile</a>
</div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
