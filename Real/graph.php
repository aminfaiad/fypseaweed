<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Line Chart with Tooltips</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="lineChart" width="400" height="200"></canvas>
    <script>
        const ctx = document.getElementById('lineChart').getContext('2d');

// Define data and configuration
new Chart(ctx, {
    type: 'line', // Type of chart
    data: {
        labels: ['Point 1', 'Point 2', 'Point 3', 'Point 4', 'Point 5'], // Labels for tooltips
        datasets: [
            {
                label: 'Min', // First line
                data: [10, 20, 15, 25, 30], // Data points
                borderColor: 'rgba(75, 192, 192, 1)', // Line color
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Area under line
                pointBackgroundColor: 'rgba(255, 99, 132, 1)', // Point color
                pointBorderColor: 'rgba(255, 99, 132, 1)', // Border color of points
                pointHoverBackgroundColor: 'rgba(54, 162, 235, 1)', // Hover color
                pointHoverBorderColor: 'rgba(54, 162, 235, 1)', // Hover border color
                borderWidth: 2, // Line width
                tension: 0.4 // Line smoothness
            },
            {
                label: 'Average', // Second line
                data: [15, 10, 20, 30, 25], // Data points
                borderColor: 'rgba(255, 99, 132, 1)', // Line color
                backgroundColor: 'rgba(255, 99, 132, 0.2)', // Area under line
                pointBackgroundColor: 'rgba(75, 192, 192, 1)', // Point color
                pointBorderColor: 'rgba(75, 192, 192, 1)', // Border color of points
                pointHoverBackgroundColor: 'rgba(153, 102, 255, 1)', // Hover color
                pointHoverBorderColor: 'rgba(153, 102, 255, 1)', // Hover border color
                borderWidth: 2, // Line width
                tension: 0.4 // Line smoothness
            },
            {
                label: 'Max', // Third line
                data: [5, 15, 10, 20, 35], // Data points
                borderColor: 'rgba(54, 162, 235, 1)', // Line color
                backgroundColor: 'rgba(54, 162, 235, 0.2)', // Area under line
                pointBackgroundColor: 'rgba(255, 206, 86, 1)', // Point color
                pointBorderColor: 'rgba(255, 206, 86, 1)', // Border color of points
                pointHoverBackgroundColor: 'rgba(75, 192, 192, 1)', // Hover color
                pointHoverBorderColor: 'rgba(75, 192, 192, 1)', // Hover border color
                borderWidth: 2, // Line width
                tension: 0.4 // Line smoothness
            }
        ]
    },
    options: {
        responsive: true, // Make chart responsive
        plugins: {
            title: { // Title plugin configuration
                display: true, // Enable title
                text: 'My Line Chart Example', // Title text
                font: {
                    size: 18 // Title font size
                },
                padding: {
                    top: 10,
                    bottom: 20
                }
            },
            tooltip: { // Tooltip configuration
                enabled: true, // Enable tooltips
                callbacks: {
                    // Customize tooltip content
                    label: function(context) {
                        return `Value: ${context.raw}`;
                    }
                }
            }
        },
        scales: {
            x: { // X-axis options
                title: {
                    display: true,
                    text: 'Points'
                }
            },
            y: { // Y-axis options
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Values'
                }
            }
        }
    }
});


    </script>
</body>
</html>
