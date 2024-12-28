<?php
require 'database.php';

$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    $token = $_GET['token'];

    // Validate the token
    $stmt = $pdo->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
    $stmt->execute([$token]);
    $resetRequest = $stmt->fetch();

    if ($resetRequest) {
        $email = $resetRequest['email'];
        $expires_at = $resetRequest['expires_at'];

        if (strtotime($expires_at) < time()) {
            $error = "This token has expired. Please request a new password reset.";
        }
    } else {
        $error = "Invalid or expired token.";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token'])) {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        $resetRequest = $stmt->fetch();

        if ($resetRequest) {
            $email = $resetRequest['email'];

            // Update the user's password
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            if ($stmt->execute([$password, $email])) {
                // Delete the reset token
                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
                $stmt->execute([$token]);

                $success = "Your password has been reset successfully.";
            } else {
                $error = "Failed to reset password. Please try again.";
            }
        } else {
            $error = "Invalid or expired token.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php else: ?>
        <?php if (isset($email)): ?>
            <form action="reset_password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <label for="password">New Password:</label>
                <input type="password" name="password" id="password" required>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
                <button type="submit">Reset Password</button>
            </form>
        <?php else: ?>
            <p>Please request a new password reset.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
