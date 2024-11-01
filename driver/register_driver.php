<?php
session_start();
include '../db.php'; // Include your database connection file

// Initialize error messages and success flag
$errors = [];
$success = false;

// Initialize variables
$name = $email = $phone = $license_number = $username = $local_area = $gender = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $license_number = trim($_POST['license_number'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $local_area = trim($_POST['local_area'] ?? '');
    $gender = trim($_POST['gender'] ?? '');

    // Input validation
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required';
    }
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    }
    if (empty($license_number)) {
        $errors[] = 'License number is required';
    }
    if (empty($username)) {
        $errors[] = 'Username is required';
    }
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    if (empty($local_area)) {
        $errors[] = 'Local area is required';
    }
    if (empty($gender)) {
        $errors[] = 'Gender is required';
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // SECURITY WARNING: Storing passwords without hashing is a severe security risk.
        // In a real-world application, always use password_hash() here.
        $plain_password = $password;

        // Start transaction
        $conn->begin_transaction();

        try {
            // Check if username already exists
            $sql = "SELECT id FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Username exists, update the existing user
                $user = $result->fetch_assoc();
                $user_id = $user['id'];
                
                $sql = "UPDATE users SET password = ?, role = 'driver', local_area = ?, gender = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $plain_password, $local_area, $gender, $user_id);
                if (!$stmt->execute()) {
                    throw new Exception("Error updating user: " . $stmt->error);
                }
            } else {
                // Insert new user
                $sql = "INSERT INTO users (username, password, role, local_area, gender) VALUES (?, ?, 'driver', ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $username, $plain_password, $local_area, $gender);
                if (!$stmt->execute()) {
                    throw new Exception("Error inserting user: " . $stmt->error);
                }
                $user_id = $stmt->insert_id;
            }
            $stmt->close();

            // Check if driver entry exists
            $sql = "SELECT id FROM drivers WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Update existing driver entry
                $sql = "UPDATE drivers SET name = ?, email = ?, phone = ?, license_number = ?, username = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssi", $name, $email, $phone, $license_number, $username, $user_id);
            } else {
                // Insert new driver entry
                $sql = "INSERT INTO drivers (id, name, email, phone, license_number, username) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssss", $user_id, $name, $email, $phone, $license_number, $username);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Error inserting/updating driver: " . $stmt->error);
            }
            $stmt->close();

            // Commit the transaction
            $conn->commit();

            // Set session variables for the logged-in driver
            $_SESSION['id'] = $user_id;
            $_SESSION['role'] = 'driver';

            // Redirect to driver dashboard after successful registration
            header('Location: driver_dashboard.php');
            exit();
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $conn->rollback();
            $errors[] = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .error {
            color: #ff0000;
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Driver Registration</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="register_driver.php" method="post">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($name); ?>">

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($email); ?>">

        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" required value="<?php echo htmlspecialchars($phone); ?>">

        <label for="license_number">License Number</label>
        <input type="text" name="license_number" id="license_number" required value="<?php echo htmlspecialchars($license_number); ?>">

        <label for="username">Username</label>
        <input type="text" name="username" id="username" required value="<?php echo htmlspecialchars($username); ?>">

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <label for="local_area">Local Area</label>
        <input type="text" name="local_area" id="local_area" required value="<?php echo htmlspecialchars($local_area); ?>">

        <label for="gender">Gender</label>
        <select name="gender" id="gender" required>
            <option value="">Select Gender</option>
            <option value="male" <?php echo ($gender === 'male') ? 'selected' : ''; ?>>Male</option>
            <option value="female" <?php echo ($gender === 'female') ? 'selected' : ''; ?>>Female</option>
            <option value="other" <?php echo ($gender === 'other') ? 'selected' : ''; ?>>Other</option>
        </select>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</div>
</body>
</html>