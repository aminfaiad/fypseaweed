<?php
require 'database.php'; // Include database connection

// Return JSON response
header('Content-Type: application/json');

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Only POST requests are allowed.'
    ]);
    exit;
}

// Check if mobile_token is provided
if (!isset($_POST['mobile_token'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No mobile token provided.'
    ]);
    exit;
}

$mobileToken = trim($_POST['mobile_token']);
$defaultPlantType = 'Seaweed'; // Default plant type

try {
    // 1) Validate the mobile token and get the associated user ID
    $stmt = $pdo->prepare("SELECT user_id FROM mobile_login_token WHERE mobile_token = :mobile_token");
    $stmt->bindParam(':mobile_token', $mobileToken, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Mobile token not found or invalid
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid mobile token.'
        ]);
        exit;
    }

    // Extract the valid user ID
    $userId = $user['user_id'];

    // 2) Count the current farms for the user
    $stmt = $pdo->prepare("SELECT COUNT(*) AS farm_count FROM farms WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3) Generate the farm name based on the farm count
    $farmCount = (int) $result['farm_count'];
    $farmName = "Farm " . ($farmCount + 1);

    // 4) Generate a unique random farm token (32 characters)
    $farmToken = bin2hex(random_bytes(16));

    // 5) Insert the new farm record into the database
    $stmt = $pdo->prepare("INSERT INTO farms (user_id, farm_token, name, plant_type) 
                           VALUES (:user_id, :farm_token, :name, :plant_type)");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':farm_token', $farmToken, PDO::PARAM_STR);
    $stmt->bindParam(':name', $farmName, PDO::PARAM_STR);
    $stmt->bindParam(':plant_type', $defaultPlantType, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode([
            'status'     => 'success',
            'message'    => 'Farm added successfully.',
            'farm_token' => $farmToken
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Failed to add farm.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
    exit;
}
