<?php
//$_POST['farm_token'] = "testtoken";
//$_POST['farm_range'] = "month";
if (!isset($_POST['farm_token']) || !isset($_POST['farm_range'])) {
    // Error response
    echo json_encode([
        'status' => 'error',
        'message' => 'All fields required'
    ]);
    exit; // Stop further script execution
}

// Proceed with your logic when the fields are set
$farmToken = $_POST['farm_token'];
$farmRange = $_POST['farm_range'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dynamic Chart Example</title>
</head>
<body>
    <canvas id="myChart" width="400" height="200"></canvas>

    <script>
        async function fetchData() {
            try {
                const response = await fetch('get_data.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        farm_token: '<?php echo $farmToken ?>',
                        farm_range: '<?php echo $farmRange ?>',
                    }),
                });

                const data = await response.json();

                if (data.status === 'success') {
                    return data;
                } else {
                    throw new Error('Failed to fetch data');
                }
            } catch (error) {
                console.error('Error fetching data:', error);
                return null;
            }
        }

        async function renderChart() {
            let farm_token = '<?php echo $farmToken ?>';
            let farmRange = '<?php echo $farmRange ?>' ; 
            const data = await fetchData();

            if (!data) return;

            let labels = data.labels;
            // let test = new Date("2023-12-01 00:00:00");
            // Array of weekday names
            const daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const monthsOfYear = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug" , "Sep", "Oct", "Nov", "Dec"];

            // Convert labels to days
            if (farmRange == 'week'){
                labels = labels.map(dateString => {
                const date = new Date(dateString); // Convert to Date object
                return daysOfWeek[date.getDay()];  // Map to day name
                });
                labels[labels.length-1] = labels[labels.length-1] +"(TODAY)";
            }

            if (farmRange == 'year'){
                labels = labels.map(dateString => {
                const date = new Date(dateString); // Convert to Date object
                return monthsOfYear[date.getMonth()];  // Map to day name
                });
            }

            if (farmRange == 'day'){
                labels = labels.map(dateString => {
                const date = new Date(dateString); // Convert to Date object
                return date.getHours() + ":00";  // Map to day name
                });
            }

            if (farmRange == 'month'){
                labels = labels.map(dateString => {
                return dateString.slice(0,-8);  // Map to day name
                });
            }
            


            const minData = data.min.map(item => parseFloat(item.temperature));
            const averageData = data.average.map(item => parseFloat(item.temperature));
            const maxData = data.max.map(item => parseFloat(item.temperature));

            const ctx = document.getElementById('myChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Min Temperature',
                            data: minData,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            fill: false,
                            tension: 0.4,
                            //showLine: false
                        },
                        {
                            label: 'Average Temperature',
                            data: averageData,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            fill: false,
                            tension: 0.4,
                        },
                        {
                            label: 'Max Temperature',
                            data: maxData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            fill: false,
                            tension: 0.4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Temperature Over Time',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Time',
                            },
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Temperature (°C)',
                            },
                            min: 0, // Set the minimum Y-axis value
                            max: 40, // Set the maximum Y-axis value
                        },
                    },
                },
            });
        }

        // Call the renderChart function on page load
        renderChart();
    </script>
</body>
</html>
