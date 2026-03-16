<?php
session_start();

// Check if supervisor is logged in
if (!isset($_SESSION['supervisor_id'])) {
    header('Location: ' . (isset($SITE_URL) ? $SITE_URL : '') . '/supervisor_login.php');
    exit;
}

require_once(__DIR__ . '/../connection.php');

$supervisor_id = $_SESSION['supervisor_id'];
$supervisor_name = $_SESSION['supervisor_name'];

// Get date range from request or default to this month
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get performance report
$query = "SELECT c.id, c.regno, c.name, c.mob,
          COUNT(q.id) as total_calls,
          SUM(CASE WHEN q.status = 0 THEN 1 ELSE 0 END) as completed_calls,
          SUM(CASE WHEN q.status = 1 THEN 1 ELSE 0 END) as pending_calls
          FROM caller c
          LEFT JOIN mquery q ON c.id = q.callerid AND DATE(q.date) BETWEEN '$start_date' AND '$end_date'
          WHERE c.svid = $supervisor_id
          GROUP BY c.id
          ORDER BY total_calls DESC";
$performance = [];
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $performance[] = $row;
}

// Get daily call statistics
$query = "SELECT DATE(q.date) as call_date, COUNT(*) as total_calls
          FROM mquery q
          JOIN caller c ON q.callerid = c.id
          WHERE c.svid = $supervisor_id AND DATE(q.date) BETWEEN '$start_date' AND '$end_date'
          GROUP BY DATE(q.date)
          ORDER BY call_date ASC";
$daily_stats = [];
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $daily_stats[] = $row;
}

// Overall statistics
$total_calls = 0;
$total_completed = 0;
$total_pending = 0;
foreach ($performance as $p) {
    $total_calls += $p['total_calls'];
    $total_completed += $p['completed_calls'];
    $total_pending += $p['pending_calls'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Supervisor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .report-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }

        .stat-box h3 {
            font-size: 36px;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-user-tie me-2"></i>Supervisor Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="callers.php">
                            <i class="fas fa-users me-1"></i>My Callers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reports.php">
                            <i class="fas fa-chart-bar me-1"></i>Reports
                        </a>
                    </li>
                </ul>
                <div class="ms-3 d-flex align-items-center">
                    <span class="text-white me-3">
                        <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($supervisor_name); ?>
                    </span>
                    <a href="logout.php" class="btn btn-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Date Filter -->
        <div class="report-card">
            <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filter Report</h5>
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Statistics -->
        <div class="row">
            <div class="col-md-4">
                <div class="stat-box">
                    <i class="fas fa-phone fa-3x"></i>
                    <h3><?php echo $total_calls; ?></h3>
                    <p class="mb-0">Total Calls</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <i class="fas fa-check-circle fa-3x"></i>
                    <h3><?php echo $total_completed; ?></h3>
                    <p class="mb-0">Completed</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fas fa-clock fa-3x"></i>
                    <h3><?php echo $total_pending; ?></h3>
                    <p class="mb-0">Pending</p>
                </div>
            </div>
        </div>

        <!-- Daily Calls Chart -->
        <div class="report-card">
            <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Daily Calls Trend</h5>
            <canvas id="dailyChart" height="80"></canvas>
        </div>

        <!-- Performance Table -->
        <div class="report-card">
            <h5 class="mb-3"><i class="fas fa-table me-2"></i>Caller Performance Report</h5>
            <div class="table-responsive">
                <table id="performanceTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Regno</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Total Calls</th>
                            <th>Completed</th>
                            <th>Pending</th>
                            <th>Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($performance as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['regno']); ?></td>
                                <td><?php echo htmlspecialchars($p['name']); ?></td>
                                <td><?php echo htmlspecialchars($p['mob']); ?></td>
                                <td><span class="badge bg-primary"><?php echo $p['total_calls']; ?></span></td>
                                <td><span class="badge bg-success"><?php echo $p['completed_calls']; ?></span></td>
                                <td><span class="badge bg-warning"><?php echo $p['pending_calls']; ?></span></td>
                                <td>
                                    <?php
                                    $rate = $p['total_calls'] > 0 ? round(($p['completed_calls'] / $p['total_calls']) * 100, 1) : 0;
                                    $color = $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge bg-<?php echo $color; ?>"><?php echo $rate; ?>%</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#performanceTable').DataTable({
                order: [[3, 'desc']],
                pageLength: 25
            });

            // Daily calls chart
            const ctx = document.getElementById('dailyChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_column($daily_stats, 'call_date')); ?>,
                    datasets: [{
                        label: 'Daily Calls',
                        data: <?php echo json_encode(array_column($daily_stats, 'total_calls')); ?>,
                        borderColor: 'rgb(102, 126, 234)',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>