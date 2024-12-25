const temperatureElement = document.getElementById('temperature');
const phElement = document.getElementById('ph');
const salinityElement = document.getElementById('salinity');
const lightElement = document.getElementById('light');
const waterLevelElement = document.getElementById('water-level');
const statusBar = document.getElementById('status-bar');
const statusImage = document.getElementById('status-image');

async function fetchData() {
    try {
        const response = await fetch('https://smartseaweed.site/Real/get_data.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'farm_token': 'test',
                'farm_range': 'current',
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const jsonData = await response.json();

        if (jsonData.status === 'success') {
            const data = jsonData.data;
            temperatureElement.textContent = `${parseFloat(data.temperature)}°C`;
            phElement.textContent = `pH: ${parseFloat(data.ph_value)}`;
            salinityElement.textContent = `Salinity: ${parseFloat(data.salinity)} ppt`;
            lightElement.textContent = `Light: ${parseFloat(data.light_intensity)} lux`;
            waterLevelElement.textContent = `Water Level: ${parseFloat(data.water_level)}%`;

            // Update status bar
            statusBar.textContent = 'Farm is Online';
            statusBar.style.backgroundColor = '#4CAF50';
        } else {
            console.error('Failed to fetch data:', jsonData);
            statusBar.textContent = 'Farm is Offline';
            statusBar.style.backgroundColor = '#F44336';
        }
    } catch (error) {
        console.error('Error fetching data:', error);
        statusBar.textContent = 'Farm is Offline';
        statusBar.style.backgroundColor = '#F44336';
    }
}

async function updateValues() {
    await fetchData();
    setTimeout(updateValues, 1000);
}

updateValues();