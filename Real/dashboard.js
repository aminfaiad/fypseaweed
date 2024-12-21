const temperatureElement = document.getElementById('temperature');
const phElement = document.getElementById('ph');
const salinityElement = document.getElementById('salinity');
const lightElement = document.getElementById('light');
const statusImage = document.getElementById('status-image');
const modal = document.getElementById('details-modal');
const chartCanvas = document.getElementById('chart');

// Generate random data for environmental parameters
const temperatureData = Array.from({ length: 24 }, () => (Math.random() * 10 + 20).toFixed(1));
const phData = Array.from({ length: 24 }, () => (Math.random() * 2 + 6).toFixed(1)); // 6-8
const salinityData = Array.from({ length: 24 }, () => (Math.random() * 5 + 30).toFixed(1)); // 30-35 ppt
const lightData = Array.from({ length: 24 }, () => (Math.random() * 500 + 1000).toFixed(0)); // 1000-1500 lux


// Random images for current status
const images = [
    'https://via.placeholder.com/300x200?text=Status+1',
    'https://via.placeholder.com/300x200?text=Status+2',
    'https://via.placeholder.com/300x200?text=Status+3',
];

// Set initial values
temperatureElement.textContent = `${temperatureData[23]}°C`;
phElement.textContent = `pH: ${phData[23]}`;
salinityElement.textContent = `Salinity: ${salinityData[23]} ppt`;
lightElement.textContent = `Light: ${lightData[23]} lux`;
statusImage.src = images[Math.floor(Math.random() * images.length)];

// Show details modal
function showDetails(type) {
    modal.style.display = 'flex';

    let data, label, title, min, max;

    switch (type) {
        case 'temperature':
            data = temperatureData;
            label = 'Temperature (°C)';
            title = 'Temperature Over Time';
            min = 15;
            max = 35;
            break;
        case 'ph':
            data = phData;
            label = 'pH';
            title = 'pH Over Time';
            min = 5;
            max = 9;
            break;
        case 'salinity':
            data = salinityData;
            label = 'Salinity (ppt)';
            title = 'Salinity Over Time';
            min = 25;
            max = 40;
            break;
        case 'light':
            data = lightData;
            label = 'Light Intensity (lux)';
            title = 'Light Intensity Over Time';
            min = 500;
            max = 2000;
            break;
    }

    renderChart(data, label, title, min, max);
}

// Close modal
function closeDetails() {
    modal.style.display = 'none';
}

// Render chart
function renderChart(data, label, title, min, max) {
    new Chart(chartCanvas.getContext('2d'), {
        type: 'line',
        data: {
            labels: Array.from({ length: 24 }, (_, i) => `${i}:00`),
            datasets: [{
                label: label,
                data: data,
                borderColor: '#0078d7',
                backgroundColor: 'rgba(0, 120, 215, 0.2)',
                borderWidth: 2,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Time (hours)'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: label
                    },
                    min: min,
                    max: max
                }
            }
        }
    });
}


// Initialize variables
let currtemperatureData;
let currphData;
let currsalinityData;
let currlightData;

// Function to fetch data
async function fetchData() {
    try {
        const response = await fetch('http://localhost/Real/get_data.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'farm_token': 'testtoken',
                'farm_range': 'current',
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const jsonData = await response.json();

        if (jsonData.status === 'success') {
            const data = jsonData.data;
            currtemperatureData = parseFloat(data.temperature);
            currphData = parseFloat(data.ph_value);
            currsalinityData = parseFloat(data.salinity);
            currlightData = parseFloat(data.light_intensity);

            console.log('Temperature:', currtemperatureData);
            console.log('pH:', currphData);
            console.log('Salinity:', currsalinityData);
            console.log('Light Intensity:', currlightData);
        } else {
            console.error('Failed to fetch data:', jsonData);
            return "offline"
        }
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

// Call the function



async function updateValues(){
    //let currtemperatureData = (Math.random() * 10 + 20).toFixed(1)
    //let currphData = (Math.random() * 2 + 6).toFixed(1)
    //let currsalinityData = (Math.random() * 5 + 30).toFixed(1)
    //let currlightData = (Math.random() * 500 + 1000).toFixed(0)
    await fetchData();
    temperatureElement.textContent = `${currtemperatureData}°C`;
    phElement.textContent = `pH: ${currphData}`;
    salinityElement.textContent = `Salinity: ${currsalinityData} ppt`;
    lightElement.textContent = `Light: ${currlightData} lux`;
    statusImage.src = images[Math.floor(Math.random() * images.length)];
    setTimeout(updateValues,1000);
}
updateValues();