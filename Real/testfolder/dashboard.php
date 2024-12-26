<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm 1 Dashboard</title>
    <style>
        :root {
            --bg-color: #f4f4f9;
            --primary-color: #4caf50;
            --secondary-color: #2196f3;
            --error-color: #f44336;
            --text-color: #333;
            --border-radius: 10px;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        #farm-token {
            font-size: 1.1em;
            color: #666;
            margin-bottom: 10px;
        }

        #status-bar {
            margin: 10px auto;
            max-width: 200px;
            padding: 10px;
            color: white;
            font-weight: bold;
            font-size: 1.2em;
            border-radius: var(--border-radius);
            text-align: center;
        }

        .online {
            background-color: var(--primary-color);
        }

        .offline {
            background-color: var(--error-color);
        }

        #farm-dashboard-container {
            margin: 20px auto;
            max-width: 1200px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        #sensor-data-container {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .section-title {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: left;
        }

        .data-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .data-item {
            flex: 1 1 calc(50% - 20px);
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 15px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .label {
            font-weight: bold;
        }

        .meter-container {
            position: relative;
            height: 20px;
            background-color: #ddd;
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .meter-fill {
            height: 100%;
            width: 0;
            background-color: var(--secondary-color);
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .out-of-range {
            background-color: var(--error-color);
        }

        #image-container {
            flex: 0 1 auto;
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        #image-container img {
            max-width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        @media (max-width: 768px) {
            .data-item {
                flex: 1 1 100%;
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>Farm 1 Dashboard</h1>
        <p id="farm-token">Farm Token: <strong><span id="tokenvalue">testtoken</span></strong></p>
        <div id="status-bar" class="status-bar offline" aria-live="polite">Offline</div>
    </header>

    <div id="farm-dashboard-container">
        <main id="sensor-data-container">
            <h2 class="section-title">Live sensor data:</h2>
            <section class="data-container">
                <div class="data-item" aria-label="pH Level">
                    <span class="label">pH:</span>
                    <div class="meter-container">
                        <div id="ph-meter" class="meter-fill" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="14"></div>
                    </div>
                    <span id="ph-value">-</span>
                </div>
                <div class="data-item" aria-label="Salinity Level">
                    <span class="label">Salinity (ppt):</span>
                    <div class="meter-container">
                        <div id="salinity-meter" class="meter-fill" role="progressbar" aria-valuenow="0"
                            aria-valuemin="0" aria-valuemax="50"></div>
                    </div>
                    <span id="salinity-value">-</span>
                </div>
                <div class="data-item" aria-label="Temperature">
                    <span class="label">Temperature (°C):</span>
                    <div class="meter-container">
                        <div id="temperature-meter" class="meter-fill" role="progressbar" aria-valuenow="0"
                            aria-valuemin="-50" aria-valuemax="50"></div>
                    </div>
                    <span id="temperature-value">-</span>
                </div>
                <div class="data-item" aria-label="Light Intensity">
                    <span class="label">Light Intensity (lux):</span>
                    <div class="meter-container">
                        <div id="light-intensity-meter" class="meter-fill" role="progressbar" aria-valuenow="0"
                            aria-valuemin="0" aria-valuemax="1000"></div>
                    </div>
                    <span id="light-intensity-value">-</span>
                </div>
                <div class="data-item" aria-label="Water Level">
                    <span class="label">Water Level (cm):</span>
                    <div class="meter-container">
                        <div id="water-level-meter" class="meter-fill" role="progressbar" aria-valuenow="0"
                            aria-valuemin="0" aria-valuemax="200"></div>
                    </div>
                    <span id="water-level-value">-</span>
                </div>
            </section>
        </main>

        <aside id="image-container">
            <h2 class="section-title">Latest image:</h2>
            <img src="image.webp" alt="Farm Overview Image">
        </aside>
    </div>

    <script>
        async function fetchData() {
            try {
                const response = await fetch('get_data.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'farm_token=testtoken&farm_range=current',
                });
                const data = await response.json();

                updateStatus(data.status === 'success' ? 'online' : 'offline');

                if (data.status === 'success') {
                    updateMeter('ph', data.data.ph_value, 14, 5, 9);
                    updateMeter('salinity', data.data.salinity, 50, 28, 35);
                    updateMeter('temperature', data.data.temperature, 50, -Infinity, 32);
                    updateMeter('light-intensity', data.data.light_intensity, 1000);
                    updateMeter('water-level', data.data.water_level || 0, 200);
                } else {
                    resetMeters();
                }
            } catch {
                updateStatus('offline');
                resetMeters();
            }
        }

        function updateStatus(status) {
            const statusBar = document.getElementById('status-bar');
            if (status === 'online') {
                statusBar.className = 'status-bar online';
                statusBar.innerText = 'Online';
            } else {
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

            meterFill.setAttribute('aria-valuenow', value);

            if (minOptimal !== null && maxOptimal !== null && (value < minOptimal || value > maxOptimal)) {
                meterFill.classList.add('out-of-range');
            } else {
                meterFill.classList.remove('out-of-range');
            }
        }

        function resetMeters() {
            ['ph', 'salinity', 'temperature', 'light-intensity', 'water-level'].forEach(id => {
                const meterFill = document.getElementById(`${id}-meter`);
                const meterValue = document.getElementById(`${id}-value`);
                meterFill.style.width = '0';
                meterFill.setAttribute('aria-valuenow', '0');
                meterFill.classList.remove('out-of-range');
                meterValue.innerText = '-';
            });
        }

        setInterval(fetchData, 5000);
        fetchData();
    </script>
</body>

</html>
