<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('api-response-logger.dashboard.app_name', 'API Response Monitor') }} - API Server</title>
    <meta name="description" content="{{ config('api-response-logger.dashboard.app_name', 'API Response Monitor') }} API Server">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #009DEA;
            --primary-dark: #0077b6;
            --primary-light: #38bdf8;
            --bg-dark: #0a0a0f;
            --bg-card: #12121a;
            --bg-card-hover: #1a1a24;
            --text-primary: #ffffff;
            --text-secondary: #9ca3af;
            --text-muted: #6b7280;
            --border: #1f2937;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-gradient {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(ellipse at 20% 20%, rgba(0, 157, 234, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(0, 157, 234, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(30, 41, 59, 0.5) 0%, transparent 70%);
            z-index: -1;
        }

        .grid-pattern {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: -1;
        }

        /* Main Container */
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        /* Logo */
        .logo {
            width: 120px;
            height: 120px;
            border-radius: 24px;
            object-fit: contain;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 10px 40px rgba(0, 157, 234, 0.3));
        }

        /* Brand */
        .brand-name {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            font-size: 1.125rem;
            color: var(--text-secondary);
            font-weight: 400;
            margin-bottom: 2rem;
        }

        /* Status Card */
        .status-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1.5rem 2.5rem;
            margin-bottom: 1.5rem;
            min-width: 320px;
        }

        .status-header {
            font-size: 0.875rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .status-dot {
            width: 14px;
            height: 14px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 2s infinite;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
        }

        .status-dot.offline {
            background: var(--error);
            animation: none;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.6;
                transform: scale(1.15);
            }
        }

        .status-text {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--success);
        }

        .status-text.offline {
            color: var(--error);
        }

        /* Server Time */
        .server-time {
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        /* Response Time Card */
        .response-time-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1.5rem;
            width: 100%;
            max-width: 900px;
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .card-subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .stats-row {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .stat-value.fast {
            color: var(--success);
        }

        .stat-value.medium {
            color: var(--warning);
        }

        .stat-value.slow {
            color: var(--error);
        }

        .stat-value.primary {
            color: var(--primary);
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .chart-container {
            position: relative;
            height: 280px;
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
        }

        .chart-container::-webkit-scrollbar {
            height: 8px;
        }

        .chart-container::-webkit-scrollbar-track {
            background: var(--bg-card);
            border-radius: 4px;
        }

        .chart-container::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }

        .chart-container::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }

        .chart-wrapper {
            min-width: 100%;
            height: 100%;
        }

        .no-data {
            text-align: center;
            color: var(--text-muted);
            padding: 2rem;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 1.5rem;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .developer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            color: var(--text-secondary);
        }

        .developer a {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .developer a:hover {
            text-decoration: underline;
        }

        .ati-red {
            color: #E31E24;
            font-weight: 700;
        }

        .ati-green {
            color: #00A651;
            font-weight: 700;
        }

        .copyright {
            font-size: 0.8rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .brand-name {
                font-size: 1.75rem;
            }

            .logo {
                width: 100px;
                height: 100px;
            }

            .status-card {
                padding: 1.25rem 1.5rem;
                min-width: auto;
                width: 100%;
            }

            .response-time-card {
                padding: 1rem;
            }

            .stats-row {
                gap: 1rem;
            }

            .stat-value {
                font-size: 1rem;
            }

            .chart-container {
                height: 200px;
            }
        }
    </style>
</head>

<body>
    <div class="bg-gradient"></div>
    <div class="grid-pattern"></div>

    <div class="container">


        <h1 class="brand-name">{{ config('api-response-logger.dashboard.app_name', config('app.name', 'API Response Monitor')) }}</h1>
        <p class="brand-subtitle">{{ config('api-response-logger.dashboard.subtitle', 'API Server') }}</p>

        <div class="status-card">
            <div class="status-header">Server Status</div>
            <div class="status-indicator">
                <span class="status-dot" id="status-dot"></span>
                <span class="status-text" id="status-text">Checking...</span>
            </div>
            <div class="server-time">
                <span id="server-time">{{ now()->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>

        <!-- Response Time Graph -->
        <div class="response-time-card">
            <div class="card-header">
                <div>
                    <div class="card-title">📊 Response Time Monitor</div>
                    <div class="card-subtitle">Last {{ config('api-response-logger.retention_hours', 24) }} hours (<span id="total-requests">0</span> requests)</div>
                </div>
            </div>

            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-value fast" id="avg-time">--</div>
                    <div class="stat-label">Avg Response</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value medium" id="max-time">--</div>
                    <div class="stat-label">Max Response</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value primary" id="min-time">--</div>
                    <div class="stat-label">Min Response</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value slow" id="slow-count">0</div>
                    <div class="stat-label">Slow (&gt;{{ config('api-response-logger.slow_threshold', 500) }}ms)</div>
                </div>
            </div>

            <div class="chart-container">
                <div class="chart-wrapper" id="chartWrapper">
                    <canvas id="responseTimeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @php
    $developer = config('api-response-logger.dashboard.developer');
    @endphp

    <footer>
        @if($developer)
        <div class="developer">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
            </svg>
            Developed by
            <a href="{{ $developer['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer">
                <span class="ati-red">{{ explode(' ', $developer['name'] ?? 'Developer')[0] }}</span>&nbsp;<span class="ati-green">{{ implode(' ', array_slice(explode(' ', $developer['name'] ?? ''), 1)) }}</span>
            </a>
        </div>
        @endif
        <p class="copyright">
            © {{ date('Y') }} {{ config('api-response-logger.dashboard.app_name', config('app.name', 'API Response Monitor')) }}. All rights reserved.
        </p>
    </footer>

    <script>
        const slowThreshold = 500;

        // Initialize Chart
        const ctx = document.getElementById('responseTimeChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Response Time (ms)',
                    data: [],
                    borderColor: '#009DEA',
                    backgroundColor: 'rgba(0, 157, 234, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: function(context) {
                        const value = context.raw;
                        if (value > slowThreshold) return '#ef4444';
                        if (value > 100) return '#f59e0b';
                        return '#10b981';
                    },
                    pointBorderColor: '#12121a',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#12121a',
                        titleColor: '#fff',
                        bodyColor: '#9ca3af',
                        borderColor: '#1f2937',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return `${context.raw.toFixed(2)} ms`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: '#6b7280',
                            maxTicksLimit: 12,
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        display: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: '#6b7280',
                            callback: function(value) {
                                return value + ' ms';
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Format time display
        function formatTime(ms) {
            if (ms >= 1000) {
                return `${(ms / 1000).toFixed(2)}s`;
            }
            return `${ms.toFixed(2)}ms`;
        }

        // Fetch response times and update server status
        async function fetchResponseTimes() {
            const dot = document.getElementById('status-dot');
            const text = document.getElementById('status-text');

            try {
                const response = await fetch('{{ url("api-stats") }}');
                const data = await response.json();

                // Server is online if we got a response
                dot.classList.remove('offline');
                text.classList.remove('offline');
                text.textContent = 'Online';

                // Update stats
                document.getElementById('total-requests').textContent = data.total_requests || 0;
                document.getElementById('avg-time').textContent = data.average_response_time > 0 ? formatTime(data.average_response_time) : '--';
                document.getElementById('max-time').textContent = data.max_response_time > 0 ? formatTime(data.max_response_time) : '--';
                document.getElementById('min-time').textContent = data.min_response_time > 0 ? formatTime(data.min_response_time) : '--';

                // Calculate slow count from hourly data
                const slowCount = data.hourly_data ? data.hourly_data.filter(d => d.max > slowThreshold).length : 0;
                document.getElementById('slow-count').textContent = slowCount;

                // Update chart with individual request data for better visualization
                const requests = data.requests || [];
                if (requests.length > 0) {
                    const labels = requests.map(d => {
                        const time = d.timestamp.split(' ')[1];
                        return time ? time.substring(0, 5) : d.timestamp;
                    });
                    const values = requests.map(d => d.response_time);

                    // Dynamically calculate width based on data points
                    const container = document.getElementById('chartWrapper');
                    const parentWidth = container.parentElement.clientWidth;
                    const minWidthPerPoint = 25;
                    const calculatedWidth = requests.length * minWidthPerPoint;

                    if (calculatedWidth > parentWidth) {
                        container.style.width = calculatedWidth + 'px';
                    } else {
                        container.style.width = '100%';
                    }

                    chart.data.labels = labels;
                    chart.data.datasets[0].data = values;
                    chart.update('none');
                }

            } catch (error) {
                // Server is offline
                dot.classList.add('offline');
                text.classList.add('offline');
                text.textContent = 'Offline';
                console.error('Failed to fetch response times:', error);
            }
        }

        // Update time every second
        function updateTime() {
            const timeEl = document.getElementById('server-time');
            const now = new Date();
            timeEl.textContent = now.toLocaleString('en-GB', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).replace(',', '');
        }

        // Initial checks
        fetchResponseTimes();

        // Periodic updates
        setInterval(updateTime, 1000);
        setInterval(fetchResponseTimes, 30000);
    </script>
</body>

</html>
