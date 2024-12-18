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
            justify-content: flex-end;
            align-items: center;
            height: 50px;
            background-color: #333;
            color: white;
            padding: 0 20px;
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

        .sidebar button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
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
        <a href="settings.php">Settings</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3>Farms</h3>
            <div>Farm 1</div>
            <div>Farm 2</div>
            <button onclick="addFarm()">Add Farm</button>
        </div>

        <!-- Content Area -->
        <div class="content"></div>
    </div>

    <!-- Script -->
    <script>
        function addFarm() {
            const sidebar = document.querySelector('.sidebar');
            const newFarm = document.createElement('div');
            newFarm.textContent = 'New Farm';
            sidebar.insertBefore(newFarm, sidebar.querySelector('button'));
        }
    </script>
</body>
</html>
