<?php
require 'database.php'; // Assumes $pdo is defined here

// Return JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Only POST requests are allowed.'
    ]);
    exit;
}

// Check for required mobile_token
if (!isset($_POST['mobile_token']) || empty($_POST['mobile_token'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'mobile_token is required.'
    ]);
    exit;
}

$mobileToken = trim($_POST['mobile_token']);

// Optional fcm_token
$fcmToken = isset($_POST['fcm_token']) ? trim($_POST['fcm_token']) : '';

// Begin the logout process
try {
    // 1) Delete mobile token entry
    $stmt = $pdo->prepare("DELETE FROM mobile_login_token WHERE mobile_token = :mobile_token");
    $stmt->bindParam(':mobile_token', $mobileToken, PDO::PARAM_STR);
    $stmt->execute();

    // Check if any row was deleted for the mobile token
    if ($stmt->rowCount() === 0) {
        // The provided mobile_token was not found in the database
        echo json_encode([
            'status'  => 'warning',
            'message' => 'No matching mobile token found (already logged out or invalid token).'
        ]);
        // We continue, because user might still want to remove FCM token if provided
    }

    // 2) If fcm_token is provided, delete it from user_fcm_tokens
    if (!empty($fcmToken)) {
        $stmtFcm = $pdo->prepare("DELETE FROM user_fcm_tokens WHERE fcm_token = :fcm_token");
        $stmtFcm->bindParam(':fcm_token', $fcmToken, PDO::PARAM_STR);
        $stmtFcm->execute();
        // We don’t necessarily check rowCount here, because it’s optional and might not exist
    }

    // 3) Return success response
    echo json_encode([
        'status'  => 'success',
        'message' => 'Logout successful.'
    ]);

} catch (Exception $e) {
    // Handle any errors
    echo json_encode([
        'status'  => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
    exit;
}
