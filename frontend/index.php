<?php
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['admin']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            margin: 20px 0;
            color: #333;
        }
        .dropdown-container {
            margin: 20px 0;
        }
        label {
            font-size: 1.1em;
            margin-right: 10px;
        }
        select {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s;
        }
        select:focus {
            border-color: #007bff;
        }
        .chart-container {
            width: 80%;
            max-width: 1000px;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }
        .grid-item {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
        }
        canvas {
            width: 100% !important;
            height: auto !important;
        }
                .login-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
        <button class="login-button" onclick="window.location.href='./pages/user/logout.php'">Login</button>

    <h1>Weather Stations</h1>
    <div class="dropdown-container">
        <label for="weatherStationSelect">Select Weather Station: </label>
        <select id="weatherStationSelect">
            <!-- Options will be populated dynamically -->
        </select>
    </div>
    <div class="grid-container" id="chartsContainer"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const weatherStationSelect = document.getElementById('weatherStationSelect');
            const chartsContainer = document.getElementById('chartsContainer');

            // Fetch the list of weather stations and populate the dropdown
            fetch('fetch_station.php')
                .then(response => response.json())
                .then(stations => {
                    stations.forEach(station => {
                        const option = document.createElement('option');
                        option.value = station.email;
                        option.textContent = station.username;
                        weatherStationSelect.appendChild(option);
                    });

                    // Fetch and display data for the first weather station
                    if (stations.length > 0) {
                        fetchWeatherData(stations[0].email);
                    }
                });

            weatherStationSelect.addEventListener('change', function() {
                const stationId = this.value;
                fetchWeatherData(stationId);
            });

            function fetchWeatherData(stationId) {
                fetch(`fetch_data.php?station_id=${stationId}`)
                    .then(response => response.json())
                    .then(result => {
                        const data = result.data;
                        const columns = result.columns;

                        chartsContainer.innerHTML = ''; // Clear existing charts
                        if(data.length === 0) {
                            const noDataMessage = document.createElement('div');
                            noDataMessage.className = 'grid-item';
                            noDataMessage.textContent = 'No data available for this station';
                            chartsContainer.appendChild(noDataMessage);
                            return;
                        }
                        columns.forEach(column => {
                            if (column === 'Wind_Speed_(km/h)' || column === 'Wind_Speed_(m/s)') {
                                createWindRoseGraph(data, column);
                            } else {
                                createLineChart(data, column);
                            }
                        });
                    });
            }

            function createLineChart(data, column) {
                const chartContainer = document.createElement('div');
                chartContainer.className = 'grid-item';
                const canvas = document.createElement('canvas');
                chartContainer.appendChild(canvas);
                chartsContainer.appendChild(chartContainer);

                const dataset = data.map(item => parseFloat(item[column]));
                const timestamps = data.map(item => new Date(item.timestamp));
                const ctx = canvas.getContext('2d');
                const color = 'rgba(75, 192, 192, 1)'; // Example color

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: timestamps,
                        datasets: [{
                            label: `${column.charAt(0).toUpperCase() + column.slice(1)}`,
                            data: dataset,
                            backgroundColor: color.replace('1)', '0.2)'),
                            borderColor: color,
                            borderWidth: 2,
                            fill: false
                        }]
                    },
                    options: {
                        scales: {
                            xAxes: [{
                                type: 'time',
                                time: {
                                    unit: 'hour',
                                    tooltipFormat: 'll HH:mm'
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Time'
                                }
                            }],
                            yAxes: [{
                                scaleLabel: {
                                    display: true,
                                    labelString: column.charAt(0).toUpperCase() + column.slice(1)
                                }
                            }]
                        },
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            function createWindRoseGraph(data, windSpeedColumn) {
                const chartContainer = document.createElement('div');
                chartContainer.className = 'grid-item';
                const canvas = document.createElement('canvas');
                chartContainer.appendChild(canvas);
                chartsContainer.appendChild(chartContainer);

                const ctx = canvas.getContext('2d');
                const speeds = data.map(item => parseFloat(item[windSpeedColumn]));
                const directions = data.map(item => parseFloat(item['Wind_Direction']));

                const dataCounts = {};
                for (let i = 0; i < speeds.length; i++) {
                    const direction = directions[i];
                    const speed = speeds[i];
                    if (speed > 0) {
                        if (!dataCounts[direction]) {
                            dataCounts[direction] = 0;
                        }
                        dataCounts[direction] += 1;
                    }
                }

                const labels = Object.keys(dataCounts).map(key => {
                    const angle = (parseInt(key) + 22.5) % 360;
                    if (angle >= 0 && angle < 45) {
                        return 'NE';
                    } else if (angle >= 45 && angle < 90) {
                        return 'E';
                    } else if (angle >= 90 && angle < 135) {
                        return 'SE';
                    } else if (angle >= 135 && angle < 180) {
                        return 'S';
                    } else if (angle >= 180 && angle < 225) {
                        return 'SW';
                    } else if (angle >= 225 && angle < 270) {
                        return 'W';
                    } else if (angle >= 270 && angle < 315) {
                        return 'NW';
                    } else {
                        return 'N';
                    }
                });

                const dataValues = Object.values(dataCounts);

                new Chart(ctx, {
                    type: 'polarArea',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: `Wind Rose (${windSpeedColumn})`,
                            data: dataValues,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.5)',
                                'rgba(54, 162, 235, 0.5)',
                                'rgba(255, 206, 86, 0.5)',
                                'rgba(75, 192, 192, 0.5)',
                                'rgba(153, 102, 255, 0.5)',
                                'rgba(255, 159, 64, 0.5)',
                                'rgba(106, 90, 205, 0.5)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scale: {
                            ticks: {
                                beginAtZero: true,
                                maxTicksLimit: 8,
                                stepSize: Math.ceil(Math.max(...dataValues) / 8)
                            },
                            reverse: false
                        },
                        title: {
                            display: true,
                            text: `Wind Rose (${windSpeedColumn})`
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
