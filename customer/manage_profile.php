<?php
session_start();
include '../db.php';

// Check if the user is a customer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

// Get the customer_id from the session
$customer_id = $_SESSION['id'];

// Fetch the customer's profile details from the customers and users table
$query = "SELECT u.username, c.email, c.phone FROM users u 
          JOIN customers c ON u.id = c.id 
          WHERE u.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

// Change password if the change password form is submitted
if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Fetch the current password from the users table
    $currentPasswordQuery = "SELECT password FROM users WHERE id = ?";
    $currentPasswordStmt = $conn->prepare($currentPasswordQuery);
    $currentPasswordStmt->bind_param("i", $customer_id);
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
            $passwordUpdateStmt->bind_param("si", $new_password, $customer_id);

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

// Update customer profile information
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];

    // Update the users table
    $updateUserQuery = "UPDATE users SET username = ? WHERE id = ?";
    $updateUserStmt = $conn->prepare($updateUserQuery);
    $updateUserStmt->bind_param("si", $new_username, $customer_id);
    
    // Update the customers table
    $updateCustomerQuery = "UPDATE customers SET email = ?, phone = ? WHERE id = ?";
    $updateCustomerStmt = $conn->prepare($updateCustomerQuery);
    $updateCustomerStmt->bind_param("ssi", $new_email, $new_phone, $customer_id);
    
    if ($updateUserStmt->execute() && $updateCustomerStmt->execute()) {
        $profile_success_message = "Profile updated successfully.";
    } else {
        $profile_error_message = "Error updating profile. Please try again.";
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
    <title>Customer Profile Manage</title>
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

        /* Container styling */
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
    <h1>Manage Profile</h1>

    <?php if (isset($profile_success_message)): ?>
        <p class="success"><?php echo $profile_success_message; ?></p>
    <?php elseif (isset($profile_error_message)): ?>
        <p class="error"><?php echo $profile_error_message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($customer['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
        </div>
        <button type="submit" name="update_profile" class="btn">Update Profile</button>
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
</div>

<script>
    // Toggle sidebar visibility
    document.getElementById('menu-toggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>

<?php
// Close the database connection
$conn->close();
?>
</body>
</html>
