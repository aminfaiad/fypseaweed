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
        $checkFarmStmt = $pdo->prepare("SELECT COUNT(*) AS farm_exists FROM farms WHERE farm_token = :farm_token");
        $checkFarmStmt->bindParam(':farm_token', $farmToken);
        $checkFarmStmt->execute();
        $farmExists = $checkFarmStmt->fetch(PDO::FETCH_ASSOC)['farm_exists'];

        if (!$farmExists) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid farm.']);
            exit;
        }

        $intervals = [
            'day' => ['duration' => '-1 day'],
            'week' => ['duration' => '-1 week'],
            'month' => ['duration' => '-1 month', 'custom_grouping' => true],
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

        $query = "SELECT 
                    DATE_FORMAT(time, '%Y-%m-%d %H:%i:%s') AS time_label,
                    AVG(ph_value) AS avg_ph, MIN(ph_value) AS min_ph, MAX(ph_value) AS max_ph,
                    AVG(temperature) AS avg_temp, MIN(temperature) AS min_temp, MAX(temperature) AS max_temp,
                    AVG(salinity) AS avg_salinity, MIN(salinity) AS min_salinity, MAX(salinity) AS max_salinity,
                    AVG(light_intensity) AS avg_light, MIN(light_intensity) AS min_light, MAX(light_intensity) AS max_light
                  FROM farm_data
                  WHERE farm_token = :farm_token AND time >= :time_frame";

        if ($customGrouping && $farmRange === 'month') {
            $query .= " GROUP BY DATE_SUB(time, INTERVAL MOD(DAYOFMONTH(time) - 1, 7) DAY)";
        } elseif ($customGrouping && $farmRange === 'year') {
            $query .= " GROUP BY DATE_FORMAT(time, '%Y-%m-01 00:00:00')";
        } else {
            $query .= " GROUP BY FLOOR(UNIX_TIMESTAMP(time) / (CASE 
                        WHEN :farm_range = 'day' THEN 3600 
                        WHEN :farm_range = 'week' THEN 86400 
                      END))";
        }

        $query .= " ORDER BY time";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':farm_token', $farmToken);
        $stmt->bindParam(':time_frame', $timeFrame);

        // Bind only if :farm_range is needed
        if (!$customGrouping) {
            $stmt->bindParam(':farm_range', $farmRange);
        }

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($data) {
            foreach ($data as &$row) {
                $time = new DateTime($row['time_label']);
                switch ($farmRange) {
                    case 'day':
                        $time->setTime($time->format('H'), 0, 0);
                        break;
                    case 'week':
                        $time->setTime(0, 0, 0);
                        break;
                    case 'month':
                        $time->modify('-' . ($time->format('d') % 7) . ' days');
                        $time->setTime(0, 0, 0);
                        break;
                    case 'year':
                        $time->modify('first day of this month');
                        $time->setTime(0, 0, 0);
                        break;
                }
                $row['time_label'] = $time->format('Y-m-d H:i:s');
            }

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
