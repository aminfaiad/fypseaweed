<?php
require 'database.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $farmToken = trim($_POST['farm_token']);
    $farmRange = trim($_POST['farm_range']);
    
    if (empty($farmToken) || empty($farmRange)) {
        echo json_encode(['status' => 'error', 'message' => 'farm_token and farm_range are required.']);
        exit;
    }

    try {
        // Check if the farm exists
        $checkFarmStmt = $pdo->prepare("SELECT COUNT(*) AS farm_exists FROM farms WHERE farm_token = :farm_token");
        $checkFarmStmt->bindParam(':farm_token', $farmToken);
        $checkFarmStmt->execute();
        $farmExists = $checkFarmStmt->fetch(PDO::FETCH_ASSOC)['farm_exists'];

        if (!$farmExists) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid farm.']);
            exit;
        }

        // Handle 'current' range
        if ($farmRange === 'current') {
            $stmt = $pdo->prepare("SELECT ph_value, temperature, salinity, light_intensity 
                                   FROM farm_data 
                                   WHERE farm_token = :farm_token AND time >= NOW() - INTERVAL 30 SECOND 
                                   ORDER BY time DESC 
                                   LIMIT 1");
            $stmt->bindParam(':farm_token', $farmToken);
            $stmt->execute();
            $latestData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($latestData) {
                echo json_encode(['status' => 'success', 'data' => $latestData]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No recent data found.']);
            }
            exit;
        }

        // Prepare the range and interval for aggregation
        $intervals = [
            'day' => ['interval' => '1 HOUR', 'label' => 'HOUR'],
            'week' => ['interval' => '1 DAY', 'label' => 'DAY'],
            'month' => ['interval' => '1 WEEK', 'label' => 'WEEK'],
            'year' => ['interval' => '1 MONTH', 'label' => 'MONTH']
        ];

        if (!isset($intervals[$farmRange])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid range.']);
            exit;
        }

        $interval = $intervals[$farmRange]['interval'];
        $label = $intervals[$farmRange]['label'];
        $timeFrame = [
            'day' => 'NOW() - INTERVAL 1 DAY',
            'week' => 'NOW() - INTERVAL 1 WEEK',
            'month' => 'NOW() - INTERVAL 1 MONTH',
            'year' => 'NOW() - INTERVAL 1 YEAR'
        ][$farmRange];

        // Query for aggregated data
        $stmt = $pdo->prepare("SELECT 
                                DATE_FORMAT(MIN(time), '%Y-%m-%d %H:%i:%s') AS time_label,
                                AVG(ph_value) AS avg_ph, MIN(ph_value) AS min_ph, MAX(ph_value) AS max_ph,
                                AVG(temperature) AS avg_temp, MIN(temperature) AS min_temp, MAX(temperature) AS max_temp,
                                AVG(salinity) AS avg_salinity, MIN(salinity) AS min_salinity, MAX(salinity) AS max_salinity,
                                AVG(light_intensity) AS avg_light, MIN(light_intensity) AS min_light, MAX(light_intensity) AS max_light
                               FROM farm_data
                               WHERE farm_token = :farm_token AND time >= $timeFrame
                               GROUP BY FLOOR(UNIX_TIMESTAMP(time) / (CASE
                                   WHEN :label = 'HOUR' THEN 3600
                                   WHEN :label = 'DAY' THEN 86400
                                   WHEN :label = 'WEEK' THEN 604800
                                   WHEN :label = 'MONTH' THEN 2592000
                               END))
                               ORDER BY MIN(time)");
        $stmt->bindParam(':farm_token', $farmToken);
        $stmt->bindParam(':label', $label);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($data) {
            $result = [
                'status' => 'success',
                'average' => array_map(fn($row) => [
                    'ph' => $row['avg_ph'],
                    'temperature' => $row['avg_temp'],
                    'salinity' => $row['avg_salinity'],
                    'light_intensity' => $row['avg_light']
                ], $data),
                'min' => array_map(fn($row) => [
                    'ph' => $row['min_ph'],
                    'temperature' => $row['min_temp'],
                    'salinity' => $row['min_salinity'],
                    'light_intensity' => $row['min_light']
                ], $data),
                'max' => array_map(fn($row) => [
                    'ph' => $row['max_ph'],
                    'temperature' => $row['max_temp'],
                    'salinity' => $row['max_salinity'],
                    'light_intensity' => $row['max_light']
                ], $data),
                'labels' => array_column($data, 'time_label')
            ];
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No data found for the selected range.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
