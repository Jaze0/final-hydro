<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.2.0"></script>
    <style>
        body {
            background-color: #e0f7fa;
        }

        .navbar {
            background-color: #00796b;
        }

        .navbar a {
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .navbar a:hover {
            background-color: #004d40;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .section-header {
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .sensor-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }

        .sensor-card h3 {
            color: #00796b;
            font-size: 1.1rem;
        }

        .sensor-card p {
            color: #555;
        }

        .dashboard-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 1.5rem;
        }

        .text-muted {
            color: #9e9e9e;
        }
    </style>
</head>

<body class="p-6">
    <!-- Navbar -->
    <div class="navbar flex justify-between items-center p-4">
        <div class="text-xl font-bold">Sensor Dashboard</div>
        <div class="flex space-x-4">
            <a href="#" id="overview-tab" class="bg-teal-800 text-white">Overview</a>
            <a href="#" id="sensor-data-tab">Sensor Data</a>
        </div>
    </div>

    <!-- Overview Section -->
    <div id="overview-section" class="max-w-6xl mx-auto dashboard-container p-8 mt-6">
        <h1 class="section-header">Sensor Overview</h1>
        @if($sensorData->count() > 0)
        @php $latest = $sensorData->first(); @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @foreach(['temperature' => 'Temperature (°C)', 'humidity' => 'Humidity (%)', 'soil_moisture' => 'Soil Moisture (%)', 'water_level' => 'Water Level (%)'] as $key => $label)
            <div class="sensor-card">
                <h3>{{ $label }}</h3>
                <p class="text-2xl font-semibold">{{ $latest->{$key} }}{{ $key == 'temperature' ? '°C' : '%' }}</p>
                <p class="text-sm text-muted">Last updated: {{ $latest->created_at->diffForHumans() }}</p>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-gray-500">No sensor data available</div>
        @endif
    </div>

<!-- Sensor Data Section with Charts -->
<div id="sensor-data-section" class="max-w-6xl mx-auto dashboard-container p-8 mt-6 hidden">
    <h1 class="section-header">Sensor Data Timeline</h1>
    @if($sensorData->count() > 0)
    <div class="mb-6 text-muted">
        Showing last {{ $sensorData->count() }} readings (from {{ $sensorData->last()->created_at->format('M j, Y H:i') }} to {{ $sensorData->first()->created_at->format('M j, Y H:i') }})
    </div>

    <!-- Individual Sensor Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @foreach([
            ['id' => 'temp-chart', 'label' => 'Temperature (°C)', 'color' => '#FF5733'],
            ['id' => 'humidity-chart', 'label' => 'Humidity (%)', 'color' => '#00BFFF'],
            ['id' => 'soil-chart', 'label' => 'Soil Moisture (%)', 'color' => '#228B22'],
            ['id' => 'water-chart', 'label' => 'Water Level (%)', 'color' => '#8A2BE2'],
        ] as $sensor)
        <div class="sensor-card">
            <h3 class="text-center text-xl font-semibold mb-4">{{ $sensor['label'] }}</h3>
            <div class="chart-container relative">
                <canvas id="{{ $sensor['id'] }}"></canvas>
                <div id="{{ $sensor['id'] }}-error" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-red-500 hidden">
                    ⚠ Unable to display chart.
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-8 text-gray-500">No sensor data available</div>
    @endif
</div>

<script>
    const tabs = {
        'overview-tab': 'overview-section',
        'sensor-data-tab': 'sensor-data-section'
    };

    const chartInstances = {};

    Object.entries(tabs).forEach(([tabId, sectionId]) => {
        document.getElementById(tabId).addEventListener('click', (e) => {
            e.preventDefault();
            Object.values(tabs).forEach(id => document.getElementById(id).classList.add('hidden'));
            document.getElementById(sectionId).classList.remove('hidden');

            document.querySelectorAll('.navbar a').forEach(a => a.classList.remove('bg-teal-800'));
            e.target.classList.add('bg-teal-800');

            if (sectionId === 'sensor-data-section') {
                initializeCharts();
            }
        });
    });

    function initializeCharts() {
        // Prevent reinitializing
        if (chartInstances.initialized) return;

        const sensorData = @json($sensorData);
        if (!sensorData || sensorData.length === 0) {
            console.warn("No sensor data available.");
            return;
        }

        const chronologicalData = [...sensorData].reverse();
        const dates = chronologicalData.map(data => new Date(data.created_at));

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'minute',
                        tooltipFormat: 'MMM d, yyyy HH:mm',
                    },
                    title: {
                        display: true,
                        text: 'Date & Time',
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Value',
                    },
                    beginAtZero: true,
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        };

        const createIndividualChart = (elementId, dataKey, label, color) => {
            try {
                const ctx = document.getElementById(elementId).getContext('2d');
                if (!ctx) {
                    console.warn(`Canvas not found for ${elementId}`);
                    return;
                }

                chartInstances[elementId] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        datasets: [{
                            label: label,
                            data: chronologicalData.map((data, i) => ({
                                x: dates[i],
                                y: parseFloat(data[dataKey]) || 0
                            })),
                            borderColor: color,
                            backgroundColor: `${color}20`,
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: chartOptions
                });
            } catch (error) {
                console.error(`Error creating chart for ${elementId}:`, error);
                const container = document.getElementById(elementId).parentElement;
                container.innerHTML += `<div class="text-red-500 text-sm mt-2 text-center">Error loading chart for ${label}</div>`;
            }
        };

        // Initialize individual sensor charts
        createIndividualChart("temp-chart", "temperature", "Temperature (°C)", "#FF5733");
        createIndividualChart("humidity-chart", "humidity", "Humidity (%)", "#00BFFF");
        createIndividualChart("soil-chart", "soil_moisture", "Soil Moisture (%)", "#228B22");
        createIndividualChart("water-chart", "water_level", "Water Level (%)", "#8A2BE2");

        chartInstances.initialized = true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (!document.getElementById('sensor-data-section').classList.contains('hidden')) {
            initializeCharts();
        }
    });
</script>

</body>
</html>