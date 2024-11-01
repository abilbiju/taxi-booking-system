<?php
session_start();
include 'db_connection.php';
include 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Initialize variables
$success = $error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Get current user's details
    $admin_id = $_SESSION['user_id']; // Assuming admin's ID is stored in session
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($stored_password);
    $stmt->fetch();
    $stmt->close();

    // Verify current password
    if (password_verify($current_password, $stored_password)) {
        // Check if new passwords match
        if ($new_password === $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password in the database
            $update_query = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("si", $hashed_password, $admin_id);
            if ($stmt->execute()) {
                $success = "Password updated successfully!";
            } else {
                $error = "Error updating password. Please try again.";
            }
            $stmt->close();
        } else {
            $error = "New passwords do not match.";
        }
    } else {
        $error = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .btn {
            background-color: #007BFF;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .message {
            color: green;
            text-align: center;
            margin-top: 15px;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 15px;
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
            <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i><span class="menu-text">Dashboard</span></a>
            <a href="manage_users.php"><i class="fas fa-users"></i><span class="menu-text">Manage Users</span></a>
            <a href="manage_cabs.php"><i class="fas fa-taxi"></i><span class="menu-text">Manage Cabs</span></a>
            <a href="manage_bookings.php"><i class="fas fa-file-alt"></i><span class="menu-text">Manage Bookings</span></a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-text">Logout</span></a>
        </div>
    </div>



    <div class="container">
        <h2>Change Password</h2>

        <?php if ($success): ?>
            <p class="message"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="account_settings.php" method="POST">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" name="current_password" id="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit" class="btn">Update Password</button>
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
