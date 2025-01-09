<?php
require 'database.php'; // Include database connection

// Always return JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If the request is not POST, respond with an error
    echo json_encode([
        'status' => 'error',
        'message' => 'Only POST requests are allowed.'
    ]);
    exit;
}

// Get input values from the POST request
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$passwordInput = trim($_POST['password'] ?? '');

// Validate inputs
if (empty($name) || empty($email) || empty($passwordInput)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'All fields (name, email, password) are required.'
    ]);
    exit;
}

try {
    // Check if the email is already in use
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email is already registered.'
        ]);
        exit;
    }

    // Hash the password securely
    // It is strongly recommended to store hashed passwords:
    // $hashedPassword = password_hash($passwordInput, PASSWORD_BCRYPT);
    // For now, using the password as-is (as per your original code):
    $hashedPassword = $passwordInput;

    // Insert the new user into the database
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) 
                           VALUES (:name, :email, :password)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);

    if ($stmt->execute()) {
        // Registration successful
        echo json_encode([
            'status' => 'success',
            'message' => 'Registration successful.'
        ]);
    } else {
        // Registration failed due to a database insertion issue
        echo json_encode([
            'status' => 'error',
            'message' => 'Registration failed.'
        ]);
    }
} catch (Exception $e) {
    // Handle any errors during insertion
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
    exit;
}
