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
        $hashedPassword = password_hash($passwordInput, PASSWORD_BCRYPT);

        // Insert the new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            echo 'Registration successful.';
        } else {
            echo 'Registration failed.';
        }
    } catch (Exception $e) {
        // Handle any errors during insertion
        die("Error: " . $e->getMessage());
    }
}
?>
