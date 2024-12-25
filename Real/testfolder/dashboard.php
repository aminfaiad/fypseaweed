<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm 1</title>
    <style>
        #farm-dashboard-container {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
            background-color: #f4f4f9;
        }
        #farm-dashboard-container h1 {
            margin-bottom: 10px;
            color: #333;
        }
        #farm-dashboard-container #farm-token {
            font-size: 1.1em;
            color: #666;
            margin-bottom: 20px;
        }
        #farm-dashboard-container #status-bar {
            margin-bottom: 20px;
            padding: 10px;
            color: white;
            font-weight: bold;
            font-size: 1.2em;
            border-radius: 5px;
        }
        #farm-dashboard-container .online {
            background-color: #4caf50;
        }
        #farm-dashboard-container .offline {
            background-color: #f44336;
        }
        #farm-dashboard-container .data-container {
            display: inline-block;
            text-align: left;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        #farm-dashboard-container .data-item {
            margin: 15px 0;
        }
        #farm-dashboard-container .label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        #farm-dashboard-container .meter-container {
            position: relative;
            width: 100%;
            height: 20px;
            background-color: #ddd;
            border-radius: 10px;
            overflow: hidden;
        }
        #farm-dashboard-container .meter-fill {
            height: 100%;
            width: 0;
            background-color: #2196f3;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        #farm-dashboard-container .out-of-range {
            background-color: #f44336;
        }
    </style>
</head>
<body>
    <div id="farm-dashboard-container" class="farm-dashboard">
        <h1>Farm 1</h1>
        <div id="farm-token">Farm Token: testtoken</div>
        <div id="status-bar" class="status-bar offline">Offline</div>

        <div class="data-container">
            <div class="data-item">
                <span class="label">pH:</span>
                <div class="meter-container">
                    <div id="ph-meter" class="meter-fill"></div>
                </div>
                <span id="ph-value">-</span>
            </div>
            <div class="data-item">
                <span class="label">Salinity (ppt):</span>
                <div class="meter-container">
                    <div id="salinity-meter" class="meter-fill"></div>
                </div>
                <span id="salinity-value">-</span>
            </div>
            <div class="data-item">
                <span class="label">Temperature (°C):</span>
                <div class="meter-container">
                    <div id="temperature-meter" class="meter-fill"></div>
                </div>
                <span id="temperature-value">-</span>
            </div>
            <div class="data-item">
                <span class="label">Light Intensity (lux):</span>
                <div class="meter-container">
                    <div id="light-intensity-meter" class="meter-fill"></div>
                </div>
                <span id="light-intensity-value">-</span>
            </div>
            <div class="data-item">
                <span class="label">Water Level (cm):</span>
                <div class="meter-container">
                    <div id="water-level-meter" class="meter-fill"></div>
                </div>
                <span id="water-level-value">-</span>
            </div>
        </div>
    </div>

    <script>
        async function fetchData() {
            try {
                const response = await fetch('get_data.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'farm_token=testtoken&farm_range=current',
                });
                const data = await response.json();

                const statusBar = document.getElementById('status-bar');

                if (data.status === 'success') {
                    updateMeter('ph', data.data.ph_value, 14, 5, 9);
                    updateMeter('salinity', data.data.salinity, 50, 28, 35);
                    updateMeter('temperature', data.data.temperature, 50, -Infinity, 32);
                    updateMeter('light-intensity', data.data.light_intensity, 1000);
                    updateMeter('water-level', data.data.water_level || 0, 200);

                    statusBar.className = 'status-bar online';
                    statusBar.innerText = 'Online';
                } else {
                    resetMeters();
                    statusBar.className = 'status-bar offline';
                    statusBar.innerText = 'Offline';
                }
            } catch (error) {
                console.error('Error fetching data:', error);
                resetMeters();
                const statusBar = document.getElementById('status-bar');
                statusBar.className = 'status-bar offline';
                statusBar.innerText = 'Offline';
            }
        }

        function updateMeter(id, value, max, minOptimal = null, maxOptimal = null) {
            const meterFill = document.getElementById(`${id}-meter`);
            const meterValue = document.getElementById(`${id}-value`);
            const percentage = Math.min((value / max) * 100, 100);
            meterFill.style.width = `${percentage}%`;
            meterValue.innerText = value;

            if (minOptimal !== null && maxOptimal !== null) {
                if (value < minOptimal || value > maxOptimal) {
                    meterFill.classList.add('out-of-range');
                } else {
                    meterFill.classList.remove('out-of-range');
                }
            } else {
                meterFill.classList.remove('out-of-range');
            }
        }

        function resetMeters() {
            ['ph', 'salinity', 'temperature', 'light-intensity', 'water-level'].forEach(id => {
                const meterFill = document.getElementById(`${id}-meter`);
                const meterValue = document.getElementById(`${id}-value`);
                meterFill.style.width = '0';
                meterFill.classList.remove('out-of-range');
                meterValue.innerText = '-';
            });
        }

        // Fetch data every 5 seconds
        setInterval(fetchData, 5000);
        fetchData(); // Initial call
    </script>
</body>
</html>
