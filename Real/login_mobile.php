<?php
session_start();
require 'database.php'; // Include database connection

header('Content-Type: application/json'); // Set the response header to JSON

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

        if ($user && $passwordInput == $user['password']) {
            // Login successful, return success response with user details
            $_SESSION['user_id'] = $user['user_id']; // Store user ID in session
            $_SESSION['name'] = $user['name']; // Store username in session

            echo json_encode([
                'status' => 'success',
                'message' => 'Login successful',
                'user_id' => $user['user_id'],
                'name' => $user['name']
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
