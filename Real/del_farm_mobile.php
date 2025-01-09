<?php
require 'database.php'; // Include your database connection

header('Content-Type: application/json'); // Set the response to JSON

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Only POST requests are allowed.'
    ]);
    exit;
}

// Check if mobile_token is provided
if (!isset($_POST['mobile_token']) || empty($_POST['mobile_token'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Mobile token not provided.'
    ]);
    exit;
}

// Check if farm_token is provided
if (!isset($_POST['farm_token']) || empty($_POST['farm_token'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Farm token not provided.'
    ]);
    exit;
}

// Retrieve tokens from POST
$mobileToken = trim($_POST['mobile_token']);
$farmToken   = trim($_POST['farm_token']);

try {
    // 1) Validate the mobile token and get the associated user ID
    $stmt = $pdo->prepare("SELECT user_id FROM mobile_login_token WHERE mobile_token = :mobile_token");
    $stmt->bindParam(':mobile_token', $mobileToken, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Mobile token not found or invalid
        echo json_encode([
            'status'  => 'error',
            'message' => 'Invalid mobile token.'
        ]);
        exit;
    }

    // Extract the valid user ID
    $userId = $user['user_id'];

    // 2) Prepare and execute the SQL query to delete the farm
    $stmt = $pdo->prepare("DELETE FROM farms WHERE user_id = :user_id AND farm_token = :farm_token");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':farm_token', $farmToken, PDO::PARAM_STR);
    $stmt->execute();

    // 3) Check if any rows were affected (deleted)
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'status'  => 'success',
            'message' => 'Farm deleted successfully.'
        ]);
    } else {
        // If rowCount is 0, either the farm doesn't exist or user doesn’t own this farm
        echo json_encode([
            'status'  => 'error',
            'message' => 'Farm not found or unauthorized.'
        ]);
    }
} catch (PDOException $e) {
    // Handle database errors
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    exit;
}
