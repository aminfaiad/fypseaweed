<?php
session_start();
require 'database.php'; // Include database connection
//print_r($_SERVER);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $passwordInput = trim($_POST['password']);
    
    try {  
        // Prepare the SQL statement
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Fetch the user record
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        //if ($user && password_verify($passwordInput, $user['password'])) {
        if ($user && $passwordInput== $user['password']) {
            // Login successful, redirect to dashboard
            $_SESSION['user_id'] = $user['id']; // Store user ID in session
            header("Location: dashboard.php");
            exit;
        } else {
            // Login failed, redirect back to login page with error
            $_SESSION['error'] = 'Invalid email or password';
            header("Location: login.php");
            exit;
        }
    } catch (Exception $e) {
        // Handle query errors
        die("Error: " . $e->getMessage());
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        Smart Seaweed
    </div>

    <!-- Login Form -->
    <div class="login-container">
        <h2>Login</h2>
        <form action="/Real/login.php" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <div class="links">
            <p><a href="forgot.php">Forgot Password?</a></p>
            <p><a href="register.php">Register here</a></p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; 2024 Smart Seaweed Project. All Rights Reserved.
    </div>

    <script>
        function validateForm() {
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;

            if (!email || !password) {
                alert("Please fill out all fields.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
