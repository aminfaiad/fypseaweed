<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Environmental Monitor</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="meter" onclick="showDetails('temperature')">
            <div id="temperature">--°C</div>
        </div>
        <div class="meter" onclick="showDetails('ph')">
            <div id="ph">pH: --</div>
        </div>
        <div class="meter" onclick="showDetails('salinity')">
            <div id="salinity">Salinity: -- ppt</div>
        </div>
        <div class="meter" onclick="showDetails('light')">
            <div id="light">Light: -- lux</div>
        </div>
        <div class="status-box">
            <h3>Current Status</h3>
            <img id="status-image" src="" alt="Current Status">
        </div>
    </div>

    <div id="details-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDetails()">&times;</span>
            <canvas id="chart"></canvas>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>
