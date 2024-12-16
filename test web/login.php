<?php
session_start();
require 'database.php'; // Include database connection

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

        if ($user && password_verify($passwordInput, $user['password'])) {
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