document.addEventListener("DOMContentLoaded", function () {
    // Dummy data for different time ranges
    const timeRanges = {
        hours: ["12AM", "3AM", "6AM", "9AM", "12PM", "3PM", "6PM", "9PM"],
        days: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        weeks: ["Week 1", "Week 2", "Week 3", "Week 4"],
        months: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug"],
    };

    let salinityData = [20, 25, 23, 22, 26, 28, 24];
    let phData = [7.0, 7.2, 7.1, 7.3, 7.0, 6.8, 7.1];
    let temperatureData = [24, 25, 26, 27, 25, 24, 26];
    let lightIntensityData = [300, 320, 310, 290, 300, 330, 340];

    const farmStatusElement = document.getElementById("farmStatus");

    // Initialize charts
    const salinityChart = createChart("salinityChart", "Salinity (ppt)", salinityData, timeRanges.days, "rgba(54, 162, 235, 0.2)", "rgba(54, 162, 235, 1)");
    const phChart = createChart("phChart", "pH Level", phData, timeRanges.days, "rgba(75, 192, 192, 0.2)", "rgba(75, 192, 192, 1)");
    const temperatureChart = createChart("temperatureChart", "Temperature (°C)", temperatureData, timeRanges.days, "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 1)");
    const lightIntensityChart = createChart("lightIntensityChart", "Light Intensity", lightIntensityData, timeRanges.days, "rgba(255, 206, 86, 0.2)", "rgba(255, 206, 86, 1)");

    // Update charts when time range changes
    document.getElementById("timeRange").addEventListener("change", function (e) {
        const range = e.target.value;

        // Update chart labels for the selected time range
        updateChart(salinityChart, timeRanges[range]);
        updateChart(phChart, timeRanges[range]);
        updateChart(temperatureChart, timeRanges[range]);
        updateChart(lightIntensityChart, timeRanges[range]);
    });

    // Simulate farm status (Online/Offline)
    setInterval(() => {
        const isOnline = Math.random() > 0.3; // 70% chance to be online
        farmStatusElement.textContent = isOnline ? "Online" : "Offline";
        farmStatusElement.classList.toggle("online", isOnline);
        farmStatusElement.classList.toggle("offline", !isOnline);
    }, 5000); // Update every 5 seconds

    function createChart(chartId, label, data, labels, backgroundColor, borderColor) {
        const ctx = document.getElementById(chartId).getContext("2d");
        return new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: label,
                        data: data,
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        borderWidth: 2,
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                    },
                },
            },
        });
    }

    function updateChart(chart, labels) {
        chart.data.labels = labels;
        chart.update();
    }
});
