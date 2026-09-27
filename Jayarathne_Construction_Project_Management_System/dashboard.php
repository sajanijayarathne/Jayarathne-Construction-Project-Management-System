<?php
// File Path: dashboard.php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 1. KPI Counts
$total_projects     = $conn->query("SELECT COUNT(*) AS total FROM projects")->fetch_assoc()['total'] ?? 0;
$ongoing_projects   = $conn->query("SELECT COUNT(*) AS total FROM projects WHERE status = 'In Progress'")->fetch_assoc()['total'] ?? 0;
$completed_projects = $conn->query("SELECT COUNT(*) AS total FROM projects WHERE status = 'Completed'")->fetch_assoc()['total'] ?? 0;
$delayed_projects   = $conn->query("SELECT COUNT(*) AS total FROM projects WHERE status = 'Delayed'")->fetch_assoc()['total'] ?? 0;

// 2. Fetch Recent Rental Requests (Safe query)
$recent_rentals = $conn->query("SELECT * FROM rentals ORDER BY id DESC LIMIT 5");

// 3. Fetch Top Equipment
$top_equipment = $conn->query("SELECT * FROM equipment ORDER BY id DESC LIMIT 5");

// 4. Progress Overview (Uses progress_percentage to match your DB)
$prog_0_25   = $conn->query("SELECT COUNT(*) AS total FROM projects WHERE progress_percentage BETWEEN 0 AND 25")->fetch_assoc()['total'] ?? 0;
$prog_26_50  = $conn->query("SELECT COUNT(*) AS total FROM projects WHERE progress_percentage BETWEEN 26 AND 50")->fetch_assoc()['total'] ?? 0;
$prog_51_75  = $conn->query("SELECT COUNT(*) AS total FROM projects WHERE progress_percentage BETWEEN 51 AND 75")->fetch_assoc()['total'] ?? 0;
$prog_76_99  = $conn->query("SELECT COUNT(*) AS total FROM projects WHERE progress_percentage BETWEEN 76 AND 99")->fetch_assoc()['total'] ?? 0;
$prog_100    = $conn->query("SELECT COUNT(*) AS total FROM projects WHERE progress_percentage = 100")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Jayarathne Construction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }
        .sidebar { width: 250px; background-color: #1e2530; min-height: 100vh; color: #a6b0cf; }
        .sidebar .brand { padding: 18px; font-weight: 700; color: #fff; border-bottom: 1px solid #273142; }
        .sidebar .nav-link { color: #a6b0cf; padding: 10px 20px; font-size: 0.9rem; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { color: #fff; background-color: #2b3548; }
        .main-content { flex: 1; }
        .kpi-card { border: none; border-radius: 10px; }
    </style>
</head>
<body class="d-flex">

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <div class="brand"><i class="fa-solid fa-truck-monster text-warning me-2"></i> Jayarathne</div>
        <div class="nav flex-column my-2">
            <a href="dashboard.php" class="nav-link active"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
            <a href="projects.php" class="nav-link"><i class="fa-solid fa-city me-2"></i> Projects</a>
            <a href="equipment.php" class="nav-link"><i class="fa-solid fa-screwdriver-wrench me-2"></i> Equipment</a>
            <a href="rentals.php" class="nav-link"><i class="fa-solid fa-truck-ramp-box me-2"></i> Equipment Rental</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content p-4">
        <h4 class="fw-bold mb-3"><i class="fa-solid fa-gauge text-primary me-2"></i>Dashboard</h4>

        <!-- Top KPI Cards -->
         <!-- Live Weather Widget Row -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm p-3 bg-white rounded-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="pe-3 border-end">
                        <i id="weatherIcon" class="fa-solid fa-cloud-sun fa-3x text-warning"></i>
                    </div>
                    <div class="ps-3">
                        <h5 class="fw-bold mb-0" id="cityName">Colombo, Sri Lanka</h5>
                        <small class="text-muted" id="weatherDesc">Loading live weather...</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <div class="text-center">
                        <span class="d-block text-muted small">Temperature</span>
                        <h4 class="fw-bold mb-0"><span id="tempValue">--</span>°C</h4>
                    </div>
                    <div class="text-center">
                        <span class="d-block text-muted small">Humidity</span>
                        <h4 class="fw-bold mb-0"><span id="humidityValue">--</span>%</h4>
                    </div>
                    <div class="text-center">
                        <span class="d-block text-muted small">Wind Speed</span>
                        <h4 class="fw-bold mb-0"><span id="windValue">--</span> km/h</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card kpi-card p-3 shadow-sm border-start border-4 border-primary">
                    <small class="text-muted fw-semibold">Total Projects</small>
                    <h2 class="fw-bold my-1"><?php echo $total_projects; ?></h2>
                    <a href="projects.php" class="small text-decoration-none">View all projects &rarr;</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card p-3 shadow-sm border-start border-4 border-success">
                    <small class="text-muted fw-semibold">Ongoing Projects</small>
                    <h2 class="fw-bold my-1 text-success"><?php echo $ongoing_projects; ?></h2>
                    <a href="projects.php" class="small text-decoration-none">View ongoing projects &rarr;</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card p-3 shadow-sm border-start border-4 border-warning">
                    <small class="text-muted fw-semibold">Completed Projects</small>
                    <h2 class="fw-bold my-1 text-warning"><?php echo $completed_projects; ?></h2>
                    <a href="projects.php" class="small text-decoration-none">View completed projects &rarr;</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card p-3 shadow-sm border-start border-4 border-danger">
                    <small class="text-muted fw-semibold">Delayed Projects</small>
                    <h2 class="fw-bold my-1 text-danger"><?php echo $delayed_projects; ?></h2>
                    <a href="projects.php" class="small text-decoration-none">View delayed projects &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm p-3">
                    <h6 class="fw-bold mb-3">Projects by Status</h6>
                    <canvas id="statusChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-3">
                    <h6 class="fw-bold mb-3">Project Progress Overview</h6>
                    <canvas id="progressChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="row g-3">
            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Recent Rental Requests</h6>
                        <a href="rentals.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recent_rentals && $recent_rentals->num_rows > 0): ?>
                                    <?php while($r = $recent_rentals->fetch_assoc()): ?>
                                        <tr>
                                            <td><code><?php echo $r['id'] ?? $r['request_id'] ?? '-'; ?></code></td>
                                            <td><?php echo $r['start_date'] ?? '-'; ?></td>
                                            <td><?php echo $r['end_date'] ?? '-'; ?></td>
                                            <td><span class="badge bg-warning text-dark"><?php echo $r['status'] ?? 'Pending'; ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted">No rental requests found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Top Equipment</h6>
                        <a href="equipment.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Equipment</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($top_equipment && $top_equipment->num_rows > 0): ?>
                                    <?php while($eq = $top_equipment->fetch_assoc()): ?>
                                        <tr>
                                            <td class="fw-semibold"><?php echo htmlspecialchars($eq['equipment_name'] ?? $eq['name'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($eq['category'] ?? '-'); ?></td>
                                            <td>
                                                <span class="badge bg-success"><?php echo $eq['status'] ?? 'Available'; ?></span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center text-muted">No equipment found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Live Charts Data Script -->
    <script>
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Ongoing', 'Completed', 'Delayed'],
                datasets: [{
                    data: [<?php echo "$ongoing_projects, $completed_projects, $delayed_projects"; ?>],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        new Chart(document.getElementById('progressChart'), {
            type: 'bar',
            data: {
                labels: ['0%-25%', '26%-50%', '51%-75%', '76%-99%', '100%'],
                datasets: [{
                    label: 'Projects',
                    data: [<?php echo "$prog_0_25, $prog_26_50, $prog_51_75, $prog_76_99, $prog_100"; ?>],
                    backgroundColor: '#0d6efd'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
        });
        // Fetch Live Weather Data (Colombo Coordinates: 6.9271, 79.8612)
async function fetchWeather() {
    try {
        const response = await fetch('https://api.open-meteo.com/v1/forecast?latitude=6.9271&longitude=79.8612&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m');
        const data = await response.json();
        const current = data.current;

        document.getElementById('tempValue').textContent = Math.round(current.temperature_2m);
        document.getElementById('humidityValue').textContent = current.relative_humidity_2m;
        document.getElementById('windValue').textContent = Math.round(current.wind_speed_10m);

        // Map Weather Codes to Text & FontAwesome Icons
        const code = current.weather_code;
        let desc = "Clear Sky";
        let iconClass = "fa-sun text-warning";

        if (code >= 1 && code <= 3) { desc = "Partly Cloudy"; iconClass = "fa-cloud-sun text-warning"; }
        else if (code >= 45 && code <= 48) { desc = "Foggy"; iconClass = "fa-smog text-secondary"; }
        else if (code >= 51 && code <= 67) { desc = "Rainy"; iconClass = "fa-cloud-rain text-primary"; }
        else if (code >= 80 && code <= 82) { desc = "Heavy Showers"; iconClass = "fa-cloud-showers-heavy text-primary"; }

        document.getElementById('weatherDesc').textContent = desc;
        document.getElementById('weatherIcon').className = `fa-solid ${iconClass} fa-3x`;

    } catch (error) {
        document.getElementById('weatherDesc').textContent = "Weather unavailable";
    }
}

// Call on page load
fetchWeather();
    </script>
</body>
</html>