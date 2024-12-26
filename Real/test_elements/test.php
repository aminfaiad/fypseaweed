<?php
session_start();
require 'database.php'; // Include database connection
//print_r($_SERVER);
$_SESSION['user_id'] = 2;
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
            background-color: pink;
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
        <div class="content"></div>
    </div>

    <!-- Script -->
    <script>
        let firstFarm;
        let farmName;
        let farmToken;
        async function fetchFarmData() {
            const farm_list = document.getElementsByClassName("farm");
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
                document.getElementById("status-bar").innerText = "Error fetching farm data";
            }
        }

        function updateTitle(farmName) {
            const title = document.querySelector('.navbar .title');
            title.textContent = farmName;
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
            newFarm.setAttribute("onclick",`updateTitle('${name}')`)
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
        
        document.addEventListener("DOMContentLoaded", fetchFarmData);
        
    </script>
</body>
</html>
