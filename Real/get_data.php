if ($customGrouping && $farmRange === 'month') {
    $query .= " GROUP BY FLOOR((TO_DAYS(time) - TO_DAYS(DATE_SUB(NOW(), INTERVAL MOD(DAYOFMONTH(NOW()) - 1, 7) DAY))) / 7)";
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
                // Align to the start of the 7-day interval
                $startOfInterval = clone $time;
                $daysSinceIntervalStart = ($time->format('d') - 1) % 7;
                $startOfInterval->modify("-{$daysSinceIntervalStart} days");
                $startOfInterval->setTime(0, 0, 0);
                $time = $startOfInterval;
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
