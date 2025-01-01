<?php
session_start();
require 'database.php'; // Include database connection

header('Content-Type: application/json'); // Set the response header to JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate a unique token
            $token = bin2hex(random_bytes(50));
            
            // Save the token in the database with an expiration date
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
            $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expiry]);

            // Send the reset link via email using the predefined function
            $resetLink = "https://smartseaweed.site/Real/reset_password.php?token=" . $token;
            if (sendPasswordResetEmail($email, $resetLink)) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Password reset link has been sent to your email."
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Failed to send email. Please try again later."
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No account found with that email."
            ]);
        }
    } catch (Exception $e) {
        // Handle any server or query errors
        echo json_encode([
            "status" => "error",
            "message" => "An error occurred: " . $e->getMessage()
        ]);
    }
} else {
    // Invalid request method
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
?>
