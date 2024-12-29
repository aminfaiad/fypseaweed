<?php
require 'database.php'; // Include database connection

//$_POST['farm_token'] = "0784ada79c7d715686eb72d52d14261d";
//$_POST['farm_range'] = "year";
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
            // Calculate the time threshold in PHP
            $dateTime = new DateTime();
            $dateTime->modify('-30 seconds');
            $timeThreshold = $dateTime->format('Y-m-d H:i:s');

            $stmt = $pdo->prepare("SELECT ph_value, temperature, salinity, light_intensity 
                                   FROM farm_data 
                                   WHERE farm_token = :farm_token AND time >= :time_threshold
                                   ORDER BY time DESC 
                                   LIMIT 1");
            $stmt->bindParam(':farm_token', $farmToken);
            $stmt->bindParam(':time_threshold', $timeThreshold);
            $stmt->execute();
            $latestData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($latestData) {
                echo json_encode(['status' => 'success', 'data' => $latestData]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No recent data found.']);
            }
            exit;
        }
        

        // Define range intervals
        $intervals = [
            'day' => ['duration' => '-1 day'],
            'week' => ['duration' => '-1 week'],
            'month' => ['duration' => '-3 week', 'custom_grouping' => true],
            'year' => ['duration' => '-1 year', 'custom_grouping' => true]
        ];

        if (!isset($intervals[$farmRange])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid range.']);
            exit;
        }

        $duration = $intervals[$farmRange]['duration'];
        $dateTime = new DateTime();
        $dateTime->modify($duration);
        $timeFrame = $dateTime->format('Y-m-d H:i:s');
        $customGrouping = $intervals[$farmRange]['custom_grouping'] ?? false;

        // Build query
        switch($farmRange){
            case 'day':
                $query = "SELECT 
                    DATE_FORMAT(`time`, '%Y-%m-%d %H:00:00') AS time_label,
                    AVG(ph_value) AS avg_ph, MIN(ph_value) AS min_ph, MAX(ph_value) AS max_ph,
                    AVG(temperature) AS avg_temp, MIN(temperature) AS min_temp, MAX(temperature) AS max_temp,
                    AVG(salinity) AS avg_salinity, MIN(salinity) AS min_salinity, MAX(salinity) AS max_salinity,
                    AVG(light_intensity) AS avg_light, MIN(light_intensity) AS min_light, MAX(light_intensity) AS max_light
                  FROM farm_data
                  WHERE farm_token = :farm_token AND `time` >= :time_frame GROUP BY time_label; ";
                  break;
            case 'week':
                $query = "SELECT 
                    DATE_FORMAT(`time`, '%Y-%m-%d 00:00:00') AS time_label,
                    AVG(ph_value) AS avg_ph, MIN(ph_value) AS min_ph, MAX(ph_value) AS max_ph,
                    AVG(temperature) AS avg_temp, MIN(temperature) AS min_temp, MAX(temperature) AS max_temp,
                    AVG(salinity) AS avg_salinity, MIN(salinity) AS min_salinity, MAX(salinity) AS max_salinity,
                    AVG(light_intensity) AS avg_light, MIN(light_intensity) AS min_light, MAX(light_intensity) AS max_light
                  FROM farm_data
                  WHERE farm_token = :farm_token AND `time` >= :time_frame GROUP BY time_label; ";
                  break;
            case 'month':
                $query = "SELECT 
                    DATE_FORMAT(FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(`time`)) - UNIX_TIMESTAMP(`time`) % 604800), '%Y-%m-%d 00:00:00') AS time_label,
                    AVG(ph_value) AS avg_ph, MIN(ph_value) AS min_ph, MAX(ph_value) AS max_ph,
                    AVG(temperature) AS avg_temp, MIN(temperature) AS min_temp, MAX(temperature) AS max_temp,
                    AVG(salinity) AS avg_salinity, MIN(salinity) AS min_salinity, MAX(salinity) AS max_salinity,
                    AVG(light_intensity) AS avg_light, MIN(light_intensity) AS min_light, MAX(light_intensity) AS max_light
                FROM farm_data
                WHERE farm_token = :farm_token AND `time` >= :time_frame GROUP BY time_label; ";
                break;
            case 'year':
                $query = "SELECT 
                    DATE_FORMAT(`time`, '%Y-%m-1 00:00:00') AS time_label,
                    AVG(ph_value) AS avg_ph, MIN(ph_value) AS min_ph, MAX(ph_value) AS max_ph,
                    AVG(temperature) AS avg_temp, MIN(temperature) AS min_temp, MAX(temperature) AS max_temp,
                    AVG(salinity) AS avg_salinity, MIN(salinity) AS min_salinity, MAX(salinity) AS max_salinity,
                    AVG(light_intensity) AS avg_light, MIN(light_intensity) AS min_light, MAX(light_intensity) AS max_light
                  FROM farm_data
                  WHERE farm_token = :farm_token AND `time` >= :time_frame GROUP BY time_label; ";
                  break;
        }
            

        

     
            //$query .= " GROUP BY FLOOR(UNIX_TIMESTAMP(`time`) / (CASE 
             //           WHEN :farm_range = 'day' THEN 3600 
              //          WHEN :farm_range = 'week' THEN 86400 
               //       END))";
        

        $query .= " ORDER BY time";

        // Prepare and execute query
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':farm_token', $farmToken);
        $stmt->bindParam(':time_frame', $timeFrame);

        if (!$customGrouping) {
            //$stmt->bindParam(':farm_range', $farmRange);
        }

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Process data
        if ($data) {
            $timeLabels = array_column($data, 'time_label');
            // Convert time labels to DateTime objects
            $timeObjects = array_map(function ($time) {
                return new DateTime($time);
            }, $timeLabels);
            // Get the last time
            $lastTime = end($timeObjects);


            switch($farmRange){
                case "day":
                    $lastTime->modify('-23 hours');
                    // Loop through 23 hours and add missing times
                    for ($i = 0; $i < 23; $i++) {
                        // Check if the current time exists in the array
                        if (!in_array($lastTime->format('Y-m-d H:i:s'), $timeLabels)) {
                            array_splice(
                                $data, 
                                $i, 
                                0, 
                                [['time_label' => $lastTime->format('Y-m-d H:i:s')] + array_fill_keys(['avg_ph', 'min_ph', 'max_ph', 'avg_temp', 'min_temp', 'max_temp', 'avg_salinity', 'min_salinity', 'max_salinity', 'avg_light', 'min_light', 'max_light'], null)]

                            );
                        }
                        // Increment time by 1 hour
                        $lastTime->modify('+1 hour');
                    }
                    break;

                    case "week":
                        $lastTime->modify('-6 days');
                        // Loop through 23 hours and add missing times
                        for ($i = 0; $i < 6; $i++) {
                            // Check if the current time exists in the array
                            if (!in_array($lastTime->format('Y-m-d H:i:s'), $timeLabels)) {
                                array_splice(
                                    $data, 
                                    $i, 
                                    0, 
                                    [['time_label' => $lastTime->format('Y-m-d H:i:s')] + array_fill_keys(['avg_ph', 'min_ph', 'max_ph', 'avg_temp', 'min_temp', 'max_temp', 'avg_salinity', 'min_salinity', 'max_salinity', 'avg_light', 'min_light', 'max_light'], null)]
    
                                );
                            }
                            // Increment time by 1 hour
                            $lastTime->modify('+1 days');
                        }
                        break;

                        case "month":
                            $lastTime->modify('-3 weeks');
                            // Loop through 23 hours and add missing times
                            for ($i = 0; $i < 3; $i++) {
                                // Check if the current time exists in the array
                                if (!in_array($lastTime->format('Y-m-d H:i:s'), $timeLabels)) {
                                    array_splice(
                                        $data, 
                                        $i, 
                                        0, 
                                        [['time_label' => $lastTime->format('Y-m-d H:i:s')] + array_fill_keys(['avg_ph', 'min_ph', 'max_ph', 'avg_temp', 'min_temp', 'max_temp', 'avg_salinity', 'min_salinity', 'max_salinity', 'avg_light', 'min_light', 'max_light'], null)]
        
                                    );
                                }
                                // Increment time by 1 hour
                                $lastTime->modify('+1 weeks');
                            }
                            break;

                            case "year":
                                $lastTime->modify('-11 months');
                                $lastTime->setDate($lastTime->format('Y'), $lastTime->format('m'), 1); // Set to the first day of the month
                                $lastTime->setTime(0, 0, 0); // Set time to midnight (00:00:00)
                                // Loop through 23 hours and add missing times
                                for ($i = 0; $i < 11; $i++) {
                                    // Check if the current time exists in the array
                                    if (!in_array($lastTime->format('Y-m-d H:i:s'), $timeLabels)) {
                                        array_splice(
                                            $data, 
                                            $i, 
                                            0, 
                                            [['time_label' => $lastTime->format('Y-m-d H:i:s')] + array_fill_keys(['avg_ph', 'min_ph', 'max_ph', 'avg_temp', 'min_temp', 'max_temp', 'avg_salinity', 'min_salinity', 'max_salinity', 'avg_light', 'min_light', 'max_light'], null)]
            
                                        );
                                    }
                                    // Increment time by 1 hour
                                    $lastTime->modify('+1 months');
                                    $lastTime->setDate($lastTime->format('Y'), $lastTime->format('m'), 1); // Set to the first day of the month
                                    $lastTime->setTime(0, 0, 0); // Set time to midnight (00:00:00)
                                }
                                break;


            }
            
            //print_r($data[1]);

            // Build result
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
