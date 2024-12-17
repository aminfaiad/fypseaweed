<?php
require 'database.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input values from the POST request
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $passwordInput = trim($_POST['password']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($passwordInput)) {
        echo 'All fields are required.';
        exit;
    }

    try {
        // Check if the email is already in use
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo 'Email is already registered.';
            exit;
        }

        // Hash the password securely
        // $hashedPassword = password_hash($passwordInput, PASSWORD_BCRYPT); Using hashedpassword
        $hashedPassword = $passwordInput;

        // Insert the new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            echo 'Registration successful.';
            header("Location: login.php");
        } else {
            echo 'Registration failed.';
        }
    } catch (Exception $e) {
        // Handle any errors during insertion
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="reg.css">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        Smart Seaweed - Register
    </div>

    <!-- Register Form -->
    <div class="register-container">
        <h2>Register</h2>
        <form action="register.php" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">Select your gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="form-group">
            <input id="countrycode" type="hidden" name="ccode" value="MY" />
            <div class="select-box">
        <div class="selected-option">
            <div>
                <span class="iconify" data-icon="flag:my-4x3"></span>
                <strong>+60</strong>
            </div>
            <input type="tel" name="tel" placeholder="Phone Number">
        </div>
        <div class="options">
            <input type="text" class="search-box" placeholder="Search Country Name">
            <ol>

            </ol>
        </div>
    </div>
            </div>
            <div class="form-group">
                <label for="country">Country:</label>
                <input type="text" id="country" name="country" placeholder="Enter your country" required>
            </div>
            <div class="form-group">
                <label for="state">State:</label>
                <input type="text" id="state" name="state" placeholder="Enter your state" required>
            </div>
            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" placeholder="Enter your city" required>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
        <div class="links">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; 2024 Smart Seaweed Project. All Rights Reserved.
    </div>

    <script>
        function validateForm() {
            const name = document.getElementById("name").value;
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            const countryCode = document.getElementById("country_code").value;
            const phone = document.getElementById("phone").value;
            const country = document.getElementById("country").value;
            const state = document.getElementById("state").value;
            const city = document.getElementById("city").value;

            if (!name || !email || !password || !countryCode || !phone || !country || !state || !city) {
                alert("Please fill out all fields.");
                return false;
            }
            return true;
        }
    </script>
    <script src="register.js"></script>
</body>
</html>
