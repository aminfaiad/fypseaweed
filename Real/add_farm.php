<?php
session_start();
require 'database.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

$userId = $_SESSION['user_id'];
$defaultPlantType = 'Seaweed'; // Default plant type

try {
    // Count the current farms for the user
    $stmt = $pdo->prepare("SELECT COUNT(*) AS farm_count FROM farms WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Generate the farm name based on the farm count
    $farmCount = $result['farm_count'];
    $farmName = "Farm " . ($farmCount + 1);

    // Generate a unique random farm token
    $farmToken = bin2hex(random_bytes(16)); // 32-character random string

    // Insert the new farm record into the database
    $stmt = $pdo->prepare("INSERT INTO farms (user_id, farm_token, name, plant_type) 
                           VALUES (:user_id, :farm_token, :name, :plant_type)");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':farm_token', $farmToken);
    $stmt->bindParam(':name', $farmName);
    $stmt->bindParam(':plant_type', $defaultPlantType);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Farm added successfully.', 'farm_token' => $farmToken]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add farm.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>
