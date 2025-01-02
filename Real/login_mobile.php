<?php
session_start();
require 'database.php'; // Include database connection

header('Content-Type: application/json'); // Set the response header to JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $passwordInput = trim($_POST['password']);

    try {
        // Prepare the SQL statement to find the user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    
        // Fetch the user record
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && $passwordInput == $user['password']) {
            // Login successful
    
            // Generate a secure random token
            $token = bin2hex(random_bytes(16)); // 32-character random token
    
            // Insert the token into the mobile_token table
            $stmt = $pdo->prepare("INSERT INTO mobile_token (user_id, token) VALUES (:user_id, :token)");
            $stmt->bindParam(':user_id', $user['user_id']);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
    
            // Return success response with token and user details
            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful',
                'user_id' => $user['user_id'],
                'name' => $user['name'],
                'token' => $token
            ]);
        } else {
            // Login failed, return error response
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid email or password'
            ]);
        }
    } catch (Exception $e) {
        // Handle query errors
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
} else {
    // Invalid request method
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>
