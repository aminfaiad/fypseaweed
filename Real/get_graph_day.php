<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Chart</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: beige;
            margin: 0;
        }
        .chart-container {
            width: 90vw;
            height: 80vh; /* Ensure the container height is 80% of the viewport */
        }
        canvas {
            display: block;
        }
    </style>
</head>
<body>
    <div class="chart-container">
        <canvas id="myChart"></canvas>
    </div>

    <script>
        let farm_data = {};

        async function fetchData() {
            try {
                const response = await fetch('get_data.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        farm_token: '<?php echo $farmToken ?>',
                        farm_range: 'day',
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
            const data = await fetchData();

            if (!data) return;

            let labels = data.labels;

            if ('<?php echo $farmRange ?>' === 'day') {
                labels = labels.map(dateString => {
                    const date = new Date(dateString);
                    return date.getHours() + ":00";
                });
            }

            const minData = data.min.map(item => parseFloat(item.<?php echo $_POST['type'] ?>));
            const averageData = data.average.map(item => parseFloat(item.<?php echo $_POST['type'] ?>));
            const maxData = data.max.map(item => parseFloat(item.<?php echo $_POST['type'] ?>));

            const ctx = document.getElementById('myChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Min <?php echo $label ?>',
                            data: minData,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            fill: false,
                            tension: 0.4,
                        },
                        {
                            label: 'Average <?php echo $label ?>',
                            data: averageData,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            fill: false,
                            tension: 0.4,
                        },
                        {
                            label: 'Max <?php echo $label ?>',
                            data: maxData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            fill: false,
                            tension: 0.4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Ensure the chart stretches to fit the container
                    plugins: {
                        title: {
                            display: true,
                            text: '<?php echo $label ?> Over Time <?php echo $units ?>',
                            font: {
                                size: 2 * window.innerWidth / 100, // Adjust font size based on viewport
                            },
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            bodyFont: {
                                size: 2 * window.innerWidth / 100, // Adjust tooltip font size
                            },
                        },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Time',
                                font: {
                                    size: 2 * window.innerWidth / 100,
                                },
                            },
                        },
                        y: {
                            <?php if ($farmType == "light_intensity") echo "type : 'logarithmic',"; ?>
                            title: {
                                display: true,
                                text: '<?php echo $label ?> <?php echo $units ?>',
                                font: {
                                    size: 2 * window.innerWidth / 100,
                                },
                            },
                            min: <?php echo $minvalue ?>,
                            max: <?php echo $maxvalue ?>,
                        },
                    },
                },
            });
        }

        renderChart();
    </script>
</body>
</html>
