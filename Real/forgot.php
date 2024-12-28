<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'database.php';
require_once 'email_function_send_reset_link.php';

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

        // Send the reset link via email using the predefined function
        $resetLink = "https://smartseaweed.com/Real/forgot_pass/reset_password.php?token=" . $token;
        if (sendPasswordResetEmail($email, $resetLink)) {
            $message = "Password reset link has been sent to your email.";
        } else {
            $message = "Failed to send email. Please try again later.";
        }
    } else {
        $message = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #555;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }
        label {
            font-size: 14px;
            margin-bottom: 8px;
            text-align: left;
        }
        input[type="email"] {
            padding: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }
        button {
            padding: 10px;
            font-size: 16px;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            margin-top: 15px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form action="forgot.php" method="POST">
            <label for="email">Enter your registered email:</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
            <button type="submit">Submit</button>
        </form>
        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $message= ""; ?>