<?php
require 'database.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve input data from the POST request
    $farmToken = trim($_POST['farm_token']);
    $phValue = trim($_POST['ph_value']);
    $temperature = trim($_POST['temperature']);
    $salinity = trim($_POST['salinity']);
    $lightIntensity = trim($_POST['light_intensity']);
    $currentTime = date('Y-m-d H:i:s'); // Get current time in datetime format
    $sonicDistance = isset($_POST["sonic_distance"]) ? trim($_POST["sonic_distance"]) : null;

    // Validate required fields
    if (empty($farmToken) || empty($phValue) || empty($temperature) || empty($salinity) || empty($lightIntensity)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required except sonic_distance.']);
        exit;
    }

    try {
        // Check if the farm exists and fetch max water level
        $checkFarmStmt = $pdo->prepare("
        SELECT COUNT(*) AS farm_exists, farm_max_water_level 
        FROM farms 
        WHERE farm_token = :farm_token
        ");
        $checkFarmStmt->bindParam(':farm_token', $farmToken);
        $checkFarmStmt->execute();
        $farmData = $checkFarmStmt->fetch(PDO::FETCH_ASSOC);

        $farmExists = $farmData['farm_exists'];

        if (!$farmExists) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid farm.']);
            exit;
        }

        // Fetch the max water level
        $farmMaxWaterLevel = $farmData['farm_max_water_level'];

        // Calculate water level if sonic distance is provided
        $currWaterLevel = is_numeric($sonicDistance) ? $farmMaxWaterLevel - $sonicDistance : null;

        // Insert data into farm_data table
        $stmt = $pdo->prepare("
            INSERT INTO farm_data (farm_token, ph_value, temperature, salinity, light_intensity, water_level, time) 
            VALUES (:farm_token, :ph_value, :temperature, :salinity, :light_intensity, :water_level, :time)
        ");
        $stmt->bindParam(':farm_token', $farmToken);
        $stmt->bindParam(':ph_value', $phValue);
        $stmt->bindParam(':temperature', $temperature);
        $stmt->bindParam(':salinity', $salinity);
        $stmt->bindParam(':light_intensity', $lightIntensity);
        $stmt->bindValue(':water_level', $currWaterLevel, is_null($currWaterLevel) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':time', $currentTime);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Data recorded successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to record data.']);
        }
    } catch (Exception $e) {
        // Handle insertion errors
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
