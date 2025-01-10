<?php
// Include the database connection file
require_once 'database.php'; // Ensure this path is correct based on your project structure

// Get the raw input data
$rawInput = file_get_contents("php://input");
$inputData = json_decode($rawInput, true); // Decode JSON input to an associative array

// Check if JSON decoding was successful and required fields are present
if (json_last_error() === JSON_ERROR_NONE && isset($inputData['farm_token'], $inputData['current_water_level'], $inputData['new_water_level'])) {
    $farmToken = $inputData['farm_token'];
    $currentWaterLevel = floatval($inputData['current_water_level']);
    $newWaterLevel = floatval($inputData['new_water_level']);

    try {
        // Calculate the difference
        $difference = $newWaterLevel - $currentWaterLevel;

        // Fetch the current farm_max_water_level from the database
        $query = $pdo->prepare("SELECT farm_max_water_level FROM farms WHERE farm_token = :farm_token");
        $query->bindParam(':farm_token', $farmToken);
        $query->execute();
        $farm = $query->fetch(PDO::FETCH_ASSOC);

        if ($farm) {
            // Add the difference to farm_max_water_level
            $updatedMaxWaterLevel = $farm['farm_max_water_level'] + $difference;

            // Ensure the updated max water level is within the range 0 to 199
            $updatedMaxWaterLevel = max(0, min(199, $updatedMaxWaterLevel));

            // Update the farm_max_water_level in the database
            $updateQuery = $pdo->prepare("UPDATE farms SET farm_max_water_level = :updated_max_water_level WHERE farm_token = :farm_token");
            $updateQuery->bindParam(':updated_max_water_level', $updatedMaxWaterLevel);
            $updateQuery->bindParam(':farm_token', $farmToken);
            $updateQuery->execute();

            // Return success response
            echo json_encode([
                'status' => 'success',
                'message' => 'Water level updated successfully.',
                'updated_max_water_level' => $updatedMaxWaterLevel
            ]);
        } else {
            // Return error if farm not found
            echo json_encode([
                'status' => 'error',
                'message' => 'Farm not found.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
} else {
    // Return error if input is invalid or required fields are missing
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid input. Please provide farm_token, current_water_level, and new_water_level in JSON format.'
    ]);
}
?>
