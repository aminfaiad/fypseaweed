<?php
require_once 'database.php'; // Assuming you have already connected $pdo here

// Validate and retrieve the mobile token from the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['mobile_token']) || empty($_POST['mobile_token'])) {
        echo "Mobile token is required.";
        exit;
    }

    $mobile_token = $_POST['mobile_token'];
    $new_username = $_POST['username'];

    // Fetch the user_id using the mobile token
    $stmt = $pdo->prepare("SELECT user_id FROM mobile_login_token WHERE mobile_token = :mobile_token");
    $stmt->execute(['mobile_token' => $mobile_token]);
    $user_id_result = $stmt->fetch();

    if (!$user_id_result) {
        echo "Invalid mobile token.";
        exit;
    }

    $user_id = $user_id_result['user_id'];

    // Fetch the current user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "User not found.";
        exit;
    }

    // Update username if it's different
    if ($new_username !== $user['name']) {
        $stmt = $pdo->prepare("UPDATE users SET name = :new_name WHERE user_id = :user_id");
        $stmt->execute(['new_name' => $new_username, 'user_id' => $user_id]);
        $_SESSION['message'] = "Username updated successfully.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $_SESSION['message'] = "No changes were made to the username.";
    }
} else {
    echo "Invalid request method.";
    exit;
}
?>
