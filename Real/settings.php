<?php
session_start();
require_once 'database.php'; // Assuming you have already connected $pdo here

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to access this page.";
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch the current user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit;
}

// Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Update username if it's different
    if ($new_username !== $user['name']) {
        $stmt = $pdo->prepare("UPDATE users SET name = :new_name WHERE user_id = :user_id");
        $stmt->execute(['new_name' => $new_username, 'user_id' => $user_id]);
        $message .= "Username updated successfully.<br>";
    }

    // Update password if old password is correct and new password is provided
    if (!empty($old_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($old_password !== $user['password']) {
            $message .= "Old password is incorrect.<br>";
        } elseif ($new_password !== $confirm_password) {
            $message .= "New password and confirm password do not match.<br>";
        } elseif ($new_password === $old_password) {
            $message .= "New password cannot be the same as the old password.<br>";
        } elseif (strlen($new_password) < 8) {
            $message .= "New password must be at least 8 characters long.<br>";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET password = :new_password WHERE user_id = :user_id");
            $stmt->execute(['new_password' => $new_password, 'user_id' => $user_id]);
            $message .= "Password updated successfully.<br>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #333;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }

        .sidebar h2 {
            margin-bottom: 30px;
        }

        .sidebar a {
            text-decoration: none;
            color: #fff;
            padding: 10px 20px;
            width: 100%;
            text-align: center;
            margin-bottom: 10px;
            background: #444;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background: #555;
        }

        .main-content {
            flex-grow: 1;
            background: #eaf7f8;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .settings-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[readonly] {
            background-color: #f5f5f5;
            color: #999;
            cursor: not-allowed;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .checkbox-container input {
            margin-right: 10px;
        }

        button {
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            margin-top: 10px;
            text-align: center;
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Settings</h2>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
    <div class="main-content">
        <div class="settings-container">
            <h1>Settings</h1>
            <form method="post" onsubmit="return validateForm()">
                <label for="email">Email:</label>
                <input type="text" id="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>

                <label for="username">New Username:</label>
                <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['name']) ?>" required>

                <label for="old_password">Old Password:</label>
                <input type="password" name="old_password" id="old_password">

                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password">

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" id="confirm_password">

                <button type="submit">Save Changes</button>
            </form>
            <div class="message <?= strpos($message, 'incorrect') !== false || strpos($message, 'cannot') !== false || strpos($message, 'must') !== false || strpos($message, 'do not match') !== false ? 'error' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const oldPassword = document.getElementById('old_password').value;

            if (newPassword && confirmPassword && newPassword !== confirmPassword) {
                alert('New password and confirm password do not match.');
                return false;
            }
            if (newPassword && newPassword === oldPassword) {
                alert('New password cannot be the same as the old password.');
                return false;
            }
            if (newPassword && newPassword.length < 8) {
                alert('New password must be at least 8 characters long.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
