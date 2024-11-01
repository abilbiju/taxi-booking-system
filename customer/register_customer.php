<?php
session_start();
include '../db.php'; // Include your database connection file

// Initialize error messages and success flag
$errors = [];
$success = false;

// Initialize variables
$name = $email = $phone = $username = $address = $gender = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $address = trim($_POST['address'] ?? '');
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
    if (empty($username)) {
        $errors[] = 'Username is required';
    }
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }
    if (empty($address)) {
        $errors[] = 'Address is required';
    }
    if (empty($gender)) {
        $errors[] = 'Gender is required';
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
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
                
                $sql = "UPDATE users SET password = ?, role = 'customer', gender = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $password, $gender, $user_id);
                if (!$stmt->execute()) {
                    throw new Exception("Error updating user: " . $stmt->error);
                }
            } else {
                // Insert new user
                $sql = "INSERT INTO users (username, password, role, gender) VALUES (?, ?, 'customer', ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $username, $password, $gender);
                if (!$stmt->execute()) {
                    throw new Exception("Error inserting user: " . $stmt->error);
                }
                $user_id = $stmt->insert_id; // Get the newly inserted user's ID
            }
            $stmt->close();

            // Insert into customer table (with address)
            $sql = "INSERT INTO customers (id, name, email, phone, address) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issss", $user_id, $name, $email, $phone, $address);
            if (!$stmt->execute()) {
                throw new Exception("Error inserting into customer: " . $stmt->error);
            }
            $stmt->close();

            // Commit the transaction
            $conn->commit();

            // Set session variables for the logged-in customer
            $_SESSION['id'] = $user_id;
            $_SESSION['role'] = 'customer';

            // Redirect to customer dashboard after successful registration
            header('Location: customer_dashboard.php');
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
    <title>Customer Registration</title>
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
    <h2>Customer Registration</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="register_customer.php" method="post">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($name); ?>">

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($email); ?>">

        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" required value="<?php echo htmlspecialchars($phone); ?>">

        <label for="address">Address</label>
        <input type="text" name="address" id="address" required value="<?php echo htmlspecialchars($address); ?>">

        <label for="username">Username</label>
        <input type="text" name="username" id="username" required value="<?php echo htmlspecialchars($username); ?>">

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

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
