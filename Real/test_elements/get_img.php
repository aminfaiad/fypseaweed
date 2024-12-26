<?php
// Include the database connection
require_once 'database.php'; // Ensure this file defines `$pdo` with the PDO instance

// Check if farm_token is provided via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['farm_token'])) {
    $farm_token = $_POST['farm_token'];

    try {
        // Prepare the SQL query to fetch the latest image for the given token
        $stmt = $pdo->prepare("
            SELECT image_path, time 
            FROM farm_images 
            WHERE farm_token = :farm_token 
            ORDER BY time DESC 
            LIMIT 1
        ");

        // Bind the farm_token parameter
        $stmt->bindParam(':farm_token', $farm_token, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Return the latest image path and time as JSON
            echo json_encode([
                'status' => 'success',
                'image_path' => $result['image_path'],
                'time' => $result['time']
            ]);
        } else {
            // No images found for the token
            echo json_encode([
                'status' => 'error',
                'message' => 'No images found for the given farm_token.'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Query failed: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request. Please provide a farm_token.'
    ]);
}
?>
