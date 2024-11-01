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

// Fetch the driver's profile details from the drivers table
$query = "SELECT name, email, phone, license_number FROM drivers WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();

// Fetch the associated cabs of the driver
$cabsQuery = "SELECT id, model, plate_number, capacity, fuel_type, price_per_km, availability FROM cabs WHERE driver_id = ?";
$cabsStmt = $conn->prepare($cabsQuery);
$cabsStmt->bind_param("i", $driver_id);
$cabsStmt->execute();
$cabsResult = $cabsStmt->get_result();

// Change password if the change password form is submitted
if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Fetch the current password from the users table
    $currentPasswordQuery = "SELECT password FROM users WHERE id = ?";
    $currentPasswordStmt = $conn->prepare($currentPasswordQuery);
    $currentPasswordStmt->bind_param("i", $driver_id);
    $currentPasswordStmt->execute();
    $currentPasswordResult = $currentPasswordStmt->get_result();

    if ($currentPasswordResult->num_rows === 0) {
        $password_error_message = "User not found.";
    } else {
        $currentPassword = $currentPasswordResult->fetch_assoc()['password'];

        // Verify the old password directly without hashing
        if ($old_password === $currentPassword) {
            // Update the password directly
            $passwordUpdateQuery = "UPDATE users SET password = ? WHERE id = ?";
            $passwordUpdateStmt = $conn->prepare($passwordUpdateQuery);
            $passwordUpdateStmt->bind_param("si", $new_password, $driver_id);

            if ($passwordUpdateStmt->execute()) {
                $password_success_message = "Password changed successfully.";
            } else {
                $password_error_message = "Error changing password. Please try again.";
            }
        } else {
            $password_error_message = "Old password is incorrect.";
        }
    }
}

// Handle cab management (add/edit/delete)
if (isset($_POST['add_cab'])) {
    // Add a new cab
    $model = $_POST['model'];
    $plate_number = $_POST['plate_number'];
    $capacity = $_POST['capacity'];
    $fuel_type = $_POST['fuel_type'];
    $price_per_km = $_POST['price_per_km'];
    $availability = $_POST['availability'];

    $addCabQuery = "INSERT INTO cabs (model, plate_number, capacity, fuel_type, price_per_km, availability, driver_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $addCabStmt = $conn->prepare($addCabQuery);
    $addCabStmt->bind_param("ssissssi", $model, $plate_number, $capacity, $fuel_type, $price_per_km, $availability, $driver_id);
    
    if ($addCabStmt->execute()) {
        $cab_success_message = "Cab added successfully.";
    } else {
        $cab_error_message = "Error adding cab. Please try again.";
    }
}

// Handle cab deletion
if (isset($_GET['delete_cab'])) {
    $cab_id = $_GET['delete_cab'];
    
    $deleteCabQuery = "DELETE FROM cabs WHERE id = ?";
    $deleteCabStmt = $conn->prepare($deleteCabQuery);
    $deleteCabStmt->bind_param("i", $cab_id);
    
    if ($deleteCabStmt->execute()) {
        $cab_success_message = "Cab deleted successfully.";
    } else {
        $cab_error_message = "Error deleting cab. Please try again.";
    }
}

// Handle cab update (assumed to be done via a separate edit form, not shown here)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Profile Details</title>
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
        /* Add your styles here */
        .container {
            margin-left: 70px; /* Adjust the container margin */
            padding: 20px;
        }
        
        h1 {
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
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
    <div class="sidebar-menu">
        <a href="driver_dashboard.php"><i class="fas fa-tachometer-alt"></i> <span class="menu-text">Dashboard</span></a>
        <a href="manage_bookings.php"><i class="fas fa-folder"></i><span class="menu-text">Manage Bookings</span></a>
        <a href="manage_profile.php"><i class="fas fa-user"></i><span class="menu-text">Profile Details</span></a>
        <a href="vehicle_details.php"><i class="fas fa-car"></i> <span class="menu-text">Vehicle Details</span></a>
        <a href="../driver/logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a>
    </div>
</div>

<div class="container">
    <h1>Profile Details</h1>
    
    <?php if (isset($password_success_message)): ?>
        <p class="success"><?php echo $password_success_message; ?></p>
    <?php elseif (isset($password_error_message)): ?>
        <p class="error"><?php echo $password_error_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($driver['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($driver['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($driver['phone']); ?>" required>
        </div>
        <div class="form-group">
            <label for="license_number">License Number</label>
            <input type="text" id="license_number" name="license_number" value="<?php echo htmlspecialchars($driver['license_number']); ?>" required>
        </div>
        <button type="submit" class="btn">Update Profile</button>
    </form>

    <h2>Change Password</h2>
    <?php if (isset($password_success_message)): ?>
        <p class="success"><?php echo $password_success_message; ?></p>
    <?php elseif (isset($password_error_message)): ?>
        <p class="error"><?php echo $password_error_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="old_password">Old Password</label>
            <input type="password" id="old_password" name="old_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        <button type="submit" name="change_password" class="btn">Change Password</button>
    </form>

    <h2>Add New Cab</h2>
    <?php if (isset($cab_success_message)): ?>
        <p class="success"><?php echo $cab_success_message; ?></p>
    <?php elseif (isset($cab_error_message)): ?>
        <p class="error"><?php echo $cab_error_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="model">Model</label>
            <input type="text" id="model" name="model" required>
        </div>
        <div class="form-group">
            <label for="plate_number">Plate Number</label>
            <input type="text" id="plate_number" name="plate_number" required>
        </div>
        <div class="form-group">
            <label for="capacity">Capacity</label>
            <input type="number" id="capacity" name="capacity" required>
        </div>
        <div class="form-group">
            <label for="fuel_type">Fuel Type</label>
            <input type="text" id="fuel_type" name="fuel_type" required>
        </div>
        <div class="form-group">
            <label for="price_per_km">Price per Km</label>
            <input type="number" id="price_per_km" name="price_per_km" required>
        </div>
        <div class="form-group">
        <label for="availability">Availability</label>
        <select id="availability" name="availability" required>
            <option value="available">Available</option>
            <option value="not available">Not Available</option>
            <option value="in maintenance">In Maintenance</option>
            <!-- Add more options as needed -->
        </select>
    </div>
        <button type="submit" name="add_cab" class="btn">Add Cab</button>
    </form>

    <h2>Your Cabs</h2>
    <?php if ($cabsResult->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Model</th>
                    <th>Plate Number</th>
                    <th>Capacity</th>
                    <th>Fuel Type</th>
                    <th>Price per Km</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cab = $cabsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cab['model']); ?></td>
                        <td><?php echo htmlspecialchars($cab['plate_number']); ?></td>
                        <td><?php echo htmlspecialchars($cab['capacity']); ?></td>
                        <td><?php echo htmlspecialchars($cab['fuel_type']); ?></td>
                        <td><?php echo htmlspecialchars($cab['price_per_km']); ?></td>
                        <td><?php echo htmlspecialchars($cab['availability']); ?></td>
                        <td>
                            <a href="edit_cab.php?id=<?php echo $cab['id']; ?>" class="btn">Edit</a>
                            <a href="?delete_cab=<?php echo $cab['id']; ?>" class="btn">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No cabs are associated with you.</p>
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
