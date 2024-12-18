<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Environmental Monitor</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background-color: #2c3e50;
            color: #ecf0f1;
            width: 250px;
            min-width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow: hidden;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .sidebar.minimized {
            width: 70px;
            min-width: 70px;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            background: #34495e;
            font-size: 18px;
            font-weight: bold;
        }

        .sidebar ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sidebar ul li {
            padding: 15px;
            cursor: pointer;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }

        .sidebar ul li:hover {
            background: #1abc9c;
        }

        .sidebar ul li i {
            margin-right: 10px;
        }

        .toggle-sidebar {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
        }

        /* Top Bar */
        .topbar {
            background-color: #2980b9;
            height: 60px;
            width: 100%;
            position: fixed;
            top: 0;
            left: 250px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0 20px;
            transition: left 0.3s;
            z-index: 900;
        }

        .topbar .icon {
            color: white;
            font-size: 24px;
            margin-left: 20px;
            cursor: pointer;
            text-decoration: none;
        }

        /* Adjust topbar when sidebar is minimized */
        .topbar.sidebar-minimized {
            left: 70px;
        }

        /* Main Container */
        .container {
            margin-left: 250px;
            margin-top: 60px;
            padding: 20px;
            flex: 1;
            transition: margin-left 0.3s;
        }

        .container.sidebar-minimized {
            margin-left: 70px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            position: relative;
            width: 80%;
            max-width: 600px;
        }

        .modal .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            Monitor
            <span class="toggle-sidebar" onclick="toggleSidebar()">&#9776;</span>
        </div>
        <ul>
            <li onclick="alert('Option 1')">Option 1</li>
            <li onclick="alert('Option 2')">Option 2</li>
            <li onclick="alert('Option 3')">Option 3</li>
        </ul>
    </div>

    <!-- Top Bar -->
    <div class="topbar" id="topbar">
        <a href="setting.php" class="icon">&#9881;</a> <!-- Settings Icon -->
        <a href="logout.php" class="icon">&#x2716;</a> <!-- Logout Icon -->
    </div>

    <!-- Main Content -->
    <div class="container" id="main-container">
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

    <!-- Modal -->
    <div id="details-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDetails()">&times;</span>
            <canvas id="chart"></canvas>
        </div>
    </div>

    <script>
        // Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const topbar = document.getElementById('topbar');
            const container = document.getElementById('main-container');

            sidebar.classList.toggle('minimized');
            topbar.classList.toggle('sidebar-minimized');
            container.classList.toggle('sidebar-minimized');
        }

        // Placeholder for existing modal functions
        function showDetails(parameter) {
            document.getElementById('details-modal').style.display = 'flex';
        }

        function closeDetails() {
            document.getElementById('details-modal').style.display = 'none';
        }
    </script>
</body>
</html>
