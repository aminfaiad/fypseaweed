<?php
header('Content-Type: application/json'); // Return JSON

require_once 'database.php'; // Ensure $pdo is properly connected

// 1) Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Only POST requests are allowed.'
    ]);
    exit;
}

// 2) Check required POST fields
if (!isset($_POST['mobile_token']) || empty($_POST['mobile_token'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Mobile token not provided.'
    ]);
    exit;
}

if (!isset($_POST['old_password']) || !isset($_POST['new_password'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Missing old_password or new_password.'
    ]);
    exit;
}

// 3) Retrieve the mobile token and passwords
$mobileToken = trim($_POST['mobile_token']);
$oldPassword = trim($_POST['old_password']);
$newPassword = trim($_POST['new_password']);

try {
    // 4) Get user_id associated with the mobile_token
    $stmt = $pdo->prepare("SELECT user_id FROM mobile_login_token WHERE mobile_token = :mobile_token");
    $stmt->bindParam(':mobile_token', $mobileToken, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Invalid mobile token.'
        ]);
        exit;
    }

    $userId = $row['user_id'];

    // 5) Fetch the user details from the users table
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'User not found.'
        ]);
        exit;
    }

    // 6) Validate old password
    if ($oldPassword !== $user['password']) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Old password is incorrect.'
        ]);
        exit;
    }

    // 7) Validate new password
    if ($newPassword === $oldPassword) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'New password cannot be the same as the old password.'
        ]);
        exit;
    }

    if (strlen($newPassword) < 8) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'New password must be at least 8 characters long.'
        ]);
        exit;
    }

    // 8) Update the user’s password in the database
    // In production, strongly consider using password_hash & password_verify for secure storage!
    $stmt = $pdo->prepare("UPDATE users SET password = :new_password WHERE user_id = :user_id");
    $stmt->bindParam(':new_password', $newPassword, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    // 9) Return success message
    echo json_encode([
        'status'  => 'success',
        'message' => 'Password updated successfully.'
    ]);

} catch (Exception $e) {
    // Handle any errors
    echo json_encode([
        'status'  => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
    exit;
}
