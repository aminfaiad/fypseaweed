<?php
session_start();
require 'database.php'; // Include database connection

header('Content-Type: application/json'); // Set the response header to JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $passwordInput = trim($_POST['password']);
    $fcmToken = isset($_POST['fcm_token']) ? trim($_POST['fcm_token']) : null; // Retrieve fcm_token or default to null

    try {
        // Prepare the SQL statement to find the user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    
        // Fetch the user record
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && $passwordInput == $user['password']) {
            // Login successful
    
            // If an fcm_token is provided, delete any existing records with the same fcm_token
            if ($fcmToken) {
                $deleteStmt = $pdo->prepare("DELETE FROM mobile_login_token WHERE fcm_token = :fcm_token");
                $deleteStmt->bindParam(':fcm_token', $fcmToken);
                $deleteStmt->execute();

                $deleteStmt = $pdo->prepare("DELETE FROM user_fcm_tokens WHERE fcm_token = :fcm_token");
                $deleteStmt->bindParam(':fcm_token', $fcmToken);
                $deleteStmt->execute();
            }
    
            // Generate a secure random token
            $mobile_token = bin2hex(random_bytes(16)); // 32-character random token
    
            // Insert the token and fcm_token into the mobile_login_token table
            $insertStmt = $pdo->prepare("
                INSERT INTO mobile_login_token (user_id, mobile_token, fcm_token) 
                VALUES (:user_id, :mobile_token, :fcm_token)
            ");
            $insertStmt->bindParam(':user_id', $user['user_id']);
            $insertStmt->bindParam(':mobile_token', $mobile_token);
            $insertStmt->bindParam(':fcm_token', $fcmToken, $fcmToken ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $insertStmt->execute();
    
            // Return success response with token and user details
            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful',
                'name' => $user['name'],
                'mobile_token' => $mobile_token
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
