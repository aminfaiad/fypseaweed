<?php
// Initialize error messages
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sample hardcoded credentials
    $valid_username = 'user';
    $valid_password = 'password123';

    // Get user input
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if credentials are correct
    if ($username === $valid_username && $password === $valid_password) {
        // Redirect to a dashboard or another page if login is successful
        header('Location: dashboard.php');
        exit();
    } else {
        // Set error message if credentials are invalid
        $error = 'Invalid username or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Agriculture</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Welcome Back to <br> Seaweed Management System</h2>

            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <div class="textbox">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="textbox">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button class="btn" type="submit">Sign In</button>
                <div class="links">
                    <a href="#">Don't have an account? Click here</a>
                    <br>
                    <a href="#">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
