<?php
include 'database.php'; // Include your database connection

header('Content-Type: application/json'); // Set the response content type to JSON

// Check if mobile_token is provided in the POST request
if (!isset($_POST['mobile_token'])) {
    echo json_encode(['error' => 'Mobile token not provided']);
    exit;
}

$mobile_token = $_POST['mobile_token'];

try {
    // Validate the mobile token and get the user_id
    $stmt = $pdo->prepare("SELECT user_id FROM mobile_login_token WHERE mobile_token = :mobile_token");
    $stmt->bindParam(':mobile_token', $mobile_token, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the mobile_token is valid
    if (!$user) {
        echo json_encode(['error' => 'Invalid mobile token']);
        exit;
    }

    $user_id = $user['user_id']; // Get the user_id from the database result

    // Fetch farms associated with the user_id
    $stmt = $pdo->prepare("SELECT name, plant_type, farm_token FROM farms WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch all results
    $farms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the farms as JSON
    echo json_encode(['farms' => $farms]);
} catch (PDOException $e) {
    // Handle database errors
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
