<?php
session_start();
require 'database.php'; // Include database connection
//print_r($_SERVER);
//$_SESSION['user_id'] = 2;
if  (!isset($_SESSION['user_id'])){
    header("Location: login.php");
            exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Dashboard</title>
    <style>
        /* General styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        /* Navbar Styling */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 50px;
            background-color: #333;
            color: white;
            padding: 0 20px;
        }

        .navbar .title {
            font-size: 18px;
            font-weight: bold;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        /* Main Container */
        .main-container {
            display: flex;
            flex: 1;
        }

        /* Sidebar */
        .sidebar {
            width: 200px;
            background-color: #f4f4f4;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sidebar h3 {
            margin-bottom: 10px;
        }

        .sidebar .farm {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #dfe6e9;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.2s;
        }

        .sidebar .farm:hover {
            background-color: #b2bec3;
            transform: scale(1.05);
        }

        .sidebar .delete-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #d63031;
        }

        .sidebar .delete-btn:hover {
            color: #e74c3c;
        }

        .sidebar button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .sidebar button:hover {
            background-color: #0056b3;
        }

        /* Content Area */
        .content {
            flex: 1;
            background-color: var(--bg-color);
        }
        :root {
            --bg-color: #e1fff1;
            --primary-color: #4caf50;
            --secondary-color: #2196f3;
            --error-color: #f44336;
            --text-color: #333;
            --border-radius: 10px;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            max-width: 100%;
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
            min-width: 30%
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
            min-width: 30%;
            max-width: 100%;
        }

        #image-container img {
            min-width: 640px;
            max-height: 60vh;
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
    <!-- Top Navigation Bar -->
    <div class="navbar">
        <div class="title">Select a Farm</div>
        <div>
            <a href="settings.php">Settings</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3>Farms</h3>
            
            <button id= "addfarm-button" onclick="addNewFarm()">Add Farm</button>
        </div>

        <!-- Content Area -->
        <div class="content">


        <header>
        <h1>Farm Dashboard</h1>
        <p id="farm-token">Farm Token: <strong><span id="tokenvalue">Loading...</span></strong></p>
        <div id="status-bar" class="status-bar offline" aria-live="polite">Offline</div>
    </header>

    <div id="farm-dashboard-container">
        <main id="sensor-data-container">
            <h2 class="section-title">Live sensor data:</h2>
            <section class="data-container">
                <div class="data-item" aria-label="pH Level">
                    <span class="label">pH:</span>
                    <div class="meter-container">
                        <div id="ph-meter" class="meter-fill" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="14"></div>
                    </div>
                    <span id="ph-value">-</span>
                </div>
                <div class="data-item" aria-label="Salinity Level">
                    <span class="label">Salinity (ppt):</span>
                    <div class="meter-container">
                        <div id="salinity-meter" class="meter-fill" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="50"></div>
                    </div>
                    <span id="salinity-value">-</span>
                </div>
                <div class="data-item" aria-label="Temperature">
                    <span class="label">Temperature (°C):</span>
                    <div class="meter-container">
                        <div id="temperature-meter" class="meter-fill" role="progressbar" aria-valuenow="0" aria-valuemin="-50" aria-valuemax="50"></div>
                    </div>
                    <span id="temperature-value">-</span>
                </div>
                <div class="data-item" aria-label="Light Intensity">
                    <span class="label">Light Intensity (lux):</span>
                    <div class="meter-container">
                        <div id="light-intensity-meter" class="meter-fill" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="1000"></div>
                    </div>
                    <span id="light-intensity-value">-</span>
                </div>
                <div class="data-item" aria-label="Water Level">
                    <span class="label">Water Level (cm):</span>
                    <div class="meter-container">
                        <div id="water-level-meter" class="meter-fill" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="200"></div>
                    </div>
                    <span id="water-level-value">-</span>
                </div>
            </section>
        </main>

        <aside id="image-container">
            <h2 class="section-title">Latest image:</h2>
            <img id="last-image" src="uploads/default.png" alt="Farm Overview Image">
        </aside>
    </div>
        </div>
    </div>

    <!-- Script -->
    <script>
        let firstFarm;
        let farmName;
        let farmToken;
        let farm_list;
        async function fetchFarmData() {
            farm_list = document.getElementsByClassName("farm");
            const farmsArray = Array.from(farm_list);

            farmsArray.forEach(farm => farm.remove());

            try {
                // Fetch farm data from the backend
                const response = await fetch('get_farm.php', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch farm data');
                }

                farmsData = await response.json();

                // Extract the first farm's details
                firstFarm = farmsData.farms[0];
                farmName = firstFarm.name;       // Extract name
                farmToken = firstFarm.farm_token; // Extract token
                
                for(let i=0;i<farmsData.farms.length;i++){
                    console.log(i)
                    addFarm(farmsData.farms[i].name,farmsData.farms[i].farm_token);
                }
                // Update the dashboard with farm name and token
                document.querySelector("h1").innerText = farmName.charAt(0).toUpperCase() + farmName.slice(1);
                document.getElementById("tokenvalue").innerText = farmToken;

                // Pass the token to the fetchData function for live updates
                fetchData(farmToken);
                fetchImage(farmToken);
            } catch (error) {
                console.error('Error fetching farm data:', error);
                //document.getElementById("status-bar").innerText = "Error fetching farm data";
            }
        }

        function updateTitle(divelement,farmName) {
            if (divelement.farm_token == farmToken){
                //console.log("test")
                return;
            }
            const title = document.querySelector('.navbar .title');
            title.textContent = farmName;
            document.querySelector("h1").innerText = divelement.farm_name;
            document.getElementById("tokenvalue").innerText = divelement.farm_token;
            farmName = divelement.farm_name;
            farmToken = divelement.farm_token;
            fetchData(farmToken);
            fetchImage(farmToken);
            //console.log("tester123")
        }

        async function deleteFarm(button, farmName) {
            if (confirm(`Are you sure you want to delete ${farmName}?`)) {
                const farmDiv = button.parentElement;

                const response = await fetch('del_farm.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `farm_token=${encodeURIComponent(farmDiv.farm_token)}` 
                });
                
                farmDiv.remove();
                const title = document.querySelector('.navbar .title');
                if (title.textContent === farmName) {
                    title.textContent = "Select a Farm";
                }
            }
        }

        function addFarm(name,token) {
            const sidebar = document.querySelector('.sidebar');

            // Create the new farm container div
            const newFarm = document.createElement('div');
            newFarm.className = 'farm';
            newFarm.setAttribute("onclick",`updateTitle(this,'${name}')`)
            newFarm.farm_name = `${name}`;
            newFarm.farm_token = `${token}`;

            // Create the span for the farm name
            const farmName = `${name}`;
            const farmSpan = document.createElement('span');
            farmSpan.textContent = farmName;
            farmSpan.onclick = () => updateTitle(farmName);

            // Create the delete button
            const deleteButton = document.createElement('button');
            deleteButton.className = 'delete-btn';
            deleteButton.innerHTML = '🗑';
            deleteButton.onclick = () => deleteFarm(deleteButton, farmName);

            // Add the span and delete button to the farm div
            newFarm.appendChild(farmSpan);
            newFarm.appendChild(deleteButton);

            // Insert the new farm above the Add Farm button
            sidebar.insertBefore(newFarm, sidebar.querySelector('#addfarm-button'));
        }

        async function addNewFarm(){
            const response = await fetch('add_farm.php', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });
            if (response.ok){
                await fetchFarmData();
            }
        }
        async function fetchImage(farmToken) {
            try {
                // Fetch farm data from the backend
                const response = await fetch('get_img.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `farm_token=${farmToken}`,
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch farm data');
                }
                const json_response = await response.json();
                const img_url =json_response.image_path
                console.log(json_response)
                if (json_response.status == "error"){
                    console.log("No image found");
                    document.getElementById("last-image").src= "default.png";
                    return;
                }
                
                document.getElementById("last-image").src= img_url;

            } catch (error) {
                console.error('Error fetching farm data:', error);
                //document.getElementById("status-bar").innerText = "Error fetching farm data";
            }
        }

        async function fetchData(farmToken) {
            try {
                const response = await fetch('get_data.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `farm_token=${farmToken}&farm_range=current`,
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
            const statusBar = document.getElementById("status-bar");
            if (status === "online") {
                statusBar.className = "status-bar online";
                statusBar.innerText = "Online";
            } else {
                statusBar.className = "status-bar offline";
                statusBar.innerText = "Offline";
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
                meterFill.classList.add("out-of-range");
            } else {
                meterFill.classList.remove("out-of-range");
            }
        }

        function resetMeters() {
            ["ph", "salinity", "temperature", "light-intensity", "water-level"].forEach((id) => {
                const meterFill = document.getElementById(`${id}-meter`);
                const meterValue = document.getElementById(`${id}-value`);
                meterFill.style.width = "0";
                meterFill.setAttribute("aria-valuenow", "0");
                meterFill.classList.remove("out-of-range");
                meterValue.innerText = "-";
            });
        }


        document.addEventListener("DOMContentLoaded", fetchFarmData);
        setInterval(function() {fetchData(farmToken); },5000);
        setInterval(function() {fetchImage(farmToken); },5000);
    </script>
</body>
</html>
