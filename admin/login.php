<?php
session_start();
include '../admin/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['id'] = $row['id'];
        $_SESSION['role'] = $row['role'];

        // Redirect based on role
        if ($row['role'] === 'admin') {
            header("Location: ../admin/admin_dashboard.php");
        } 
        else {
            echo "<p class='error-msg'>Access denied! Only admins can log in here.</p>";
        }
    } else {
        echo "<p class='error-msg'>Invalid username or password.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taxi Rental Login</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Background and general styles */
        body {
            font-family: Arial, sans-serif;
            background: url('https://i.ibb.co/s9kCfq2/Gemini-Generated-Image-jzeqgtjzeqgtjzeq.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
        }

        /* Login container */
        .login-container {
            background-color: rgba(0, 0, 0, 0.7); /* Dark overlay for contrast */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 300px;
        }

        /* Form styling */
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            font-weight: bold;
            color: #FFD700; /* Taxi yellow color */
        }

        input[type="text"], input[type="password"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            outline: none;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #FFD700; /* Taxi yellow */
            color: black;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #FFAA00;
        }

        /* Error message styling */
        .error-msg {
            color: red;
            font-size: 14px;
            margin-top: -15px;
            margin-bottom: 15px;
        }

        h1 {
            color: #FFD700; /* Taxi yellow */
            margin-bottom: 20px;
        }

        /* For responsive behavior */
        @media (max-width: 768px) {
            .login-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
