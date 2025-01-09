<?php
require_once 'database.php'; // Assumes $pdo is already defined here

// Get posted data
$mobile_token = $_POST['mobile_token'] ?? null;
$fcm_token = $_POST['fcm_token'] ?? null;
$device_type = $_POST['device_type'] ?? null;

// Check if required data is provided
if (!$mobile_token || !$fcm_token || !$device_type) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing mobile_token, fcm_token, or device_type.'
    ]);
    exit;
}

try {
    // Retrieve user_id from mobile_login_token table
    $query = "SELECT user_id FROM mobile_login_token WHERE mobile_token = :mobile_token LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['mobile_token' => $mobile_token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid mobile_token.'
        ]);
        exit;
    }

    $user_id = $user['user_id'];

    // Check if the fcm_token is already registered
    $query = "SELECT id FROM user_fcm_tokens WHERE fcm_token = :fcm_token LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['fcm_token' => $fcm_token]);
    $existingToken = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingToken) {
        echo json_encode([
            'status' => 'error',
            'message' => 'FCM token already registered.'
        ]);
        exit;
    }

    // Insert into user_fcm_tokens table
    $query = "INSERT INTO user_fcm_tokens (user_id, fcm_token, device_type, created_at) VALUES (:user_id, :fcm_token, :device_type, :created_at)";
    $stmt = $pdo->prepare($query);

    $stmt->execute([
        'user_id' => $user_id,
        'fcm_token' => $fcm_token,
        'device_type' => $device_type,
        'created_at' => date('Y-m-d H:i:s')
    ]);


    

    echo json_encode([
        'status' => 'success',
        'message' => 'FCM token added successfully.'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
