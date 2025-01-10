<?php
// Include the database connection file
require_once 'database.php'; // Ensure this path is correct based on your project structure

// Check if POST values are set
if (isset($_POST['farm_token'], $_POST['current_water_level'], $_POST['new_water_level'])) {
    $farmToken = $_POST['farm_token'];
    $currentWaterLevel = floatval($_POST['current_water_level']);
    $newWaterLevel = floatval($_POST['new_water_level']);

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
    // Return error if required POST values are missing
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid input. Please provide farm_token, current_water_level, and new_water_level.'
    ]);
}
?>
