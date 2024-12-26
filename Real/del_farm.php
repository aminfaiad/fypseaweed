<?php
session_start(); // Start the session to access session variables
include 'database.php'; // Include your database connection

header('Content-Type: application/json'); // Set the response content type to JSON

// Check if user_id exists in the session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Check if farm_token is provided in POST data
if (!isset($_POST['farm_token'])) {
    echo json_encode(['error' => 'Farm token not provided']);
    exit;
}

$user_id = $_SESSION['user_id']; // Get the user_id from the session
$farm_token = $_POST['farm_token']; // Get the farm_token from POST data

try {
    // Prepare and execute the SQL query to delete the farm
    $stmt = $pdo->prepare("DELETE FROM farms WHERE user_id = :user_id AND farm_token = :farm_token");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':farm_token', $farm_token, PDO::PARAM_STR);
    $stmt->execute();

    // Check if any rows were affected (deleted)
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => 'Farm deleted successfully']);
    } else {
        echo json_encode(['error' => 'Farm not found or unauthorized']);
    }
} catch (PDOException $e) {
    // Handle database errors
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
