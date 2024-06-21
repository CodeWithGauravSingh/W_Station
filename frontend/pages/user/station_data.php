<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            margin: 20px 0;
            color: #333;
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
    </style
</head>
<body>
    <h1>Weather Dashboard</h1>
    <div class="grid-container" id="chartsContainer"></div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const response = await fetch('fetch_data.php');
            const result = await response.json();
            const data = result.data;
            const columns = result.columns;

            const colors = {
                temperature: 'rgba(255, 99, 132, 1)',
                humidity: 'rgba(54, 162, 235, 1)',
                pressure: 'rgba(255, 206, 86, 1)',
                wind_speed: 'rgba(75, 192, 192, 1)'
            };

            const timestamps = data.map(item => new Date(item.timestamp));
            console.log('Timestamps:', timestamps); // Debugging: Check timestamps

            const chartsContainer = document.getElementById('chartsContainer');

            columns.forEach(column => {
                if (column === 'Wind_Speed_(km/h)' || column === 'Wind_Speed_(m/s)') {
                    // Create wind rose graph
                    createWindRoseGraph(data, timestamps, chartsContainer, column);
                } else {
                    // Create line chart for other data
                    createLineChart(column, data, timestamps, chartsContainer);
                }
            });

            function createLineChart(column, data, timestamps, chartsContainer) {
                const chartContainer = document.createElement('div');
                chartContainer.className = 'grid-item';
                const canvas = document.createElement('canvas');
                chartContainer.appendChild(canvas);
                chartsContainer.appendChild(chartContainer);

                const dataset = data.map(item => parseFloat(item[column]));
                console.log(`Data for ${column}:`, dataset); // Debugging: Check dataset values
                const ctx = canvas.getContext('2d');
                const color = colors[column] || 'rgba(153, 102, 255, 1)';

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

            function createWindRoseGraph(data, timestamps, chartsContainer, windSpeedColumn) {
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
