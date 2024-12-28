<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
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

        // Send the reset link via email
        $resetLink = "http://yourwebsite.com/reset_password.php?token=" . $token;
        $subject = "Password Reset Request";
        $message = "Click the link to reset your password: " . $resetLink;
        $headers = "From: no-reply@yourwebsite.com";
        
        if (mail($email, $subject, $message, $headers)) {
            echo "Password reset link has been sent to your email.";
        } else {
            echo "Failed to send email.";
        }
    } else {
        echo "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <form action="forgot.php" method="POST">
        <label for="email">Enter your registered email:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>