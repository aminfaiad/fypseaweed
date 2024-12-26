<?php
session_start(); // Start the session to access session variables
include 'database.php'; // Include your database connection

header('Content-Type: application/json'); // Set the response content type to JSON

// Check if user_id exists in the session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id']; // Get the user_id from the session

try {
    // Prepare and execute the SQL query
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
