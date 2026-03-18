<?php
require_once(__DIR__ . '/config/config.php');
require_once(__DIR__ . '/config/auth.php');

$page_title = 'Dashboard';

// Get statistics
$stats = [];

// Total Branches
$result = mysqli_query($con, "SELECT COUNT(*) as count FROM branch WHERE status = 1");
$stats['branches'] = mysqli_fetch_assoc($result)['count'];

// Total Managers
$result = mysqli_query($con, "SELECT COUNT(*) as count FROM manager WHERE status = 1");
$stats['managers'] = mysqli_fetch_assoc($result)['count'];

// Total Supervisors
$result = mysqli_query($con, "SELECT COUNT(*) as count FROM supervisor WHERE status = 1");
$stats['supervisors'] = mysqli_fetch_assoc($result)['count'];

// Total Callers
$result = mysqli_query($con, "SELECT COUNT(*) as count FROM caller WHERE status = 1");
$stats['callers'] = mysqli_fetch_assoc($result)['count'];

// Total Students/Registrations
$result = mysqli_query($con, "SELECT COUNT(*) as count FROM registration");
$stats['students'] = mysqli_fetch_assoc($result)['count'];

// Total Courses
$result = mysqli_query($con, "SELECT COUNT(*) as count FROM courses WHERE status = 1");
$stats['courses'] = mysqli_fetch_assoc($result)['count'];

// Active Queries
$result = mysqli_query($con, "SELECT COUNT(*) as count FROM mquery WHERE status = 1");
$stats['queries'] = mysqli_fetch_assoc($result)['count'];

// Total Revenue (from bwallets)
$result = mysqli_query($con, "SELECT SUM(amount) as total FROM bwallets WHERE status = 1");
$stats['revenue'] = mysqli_fetch_assoc($result)['total'] ?? 0;

// Get recent registrations
$recent_registrations = [];
$query = "SELECT r.*, b.bname FROM registration r 
          LEFT JOIN branch b ON r.bid = b.id 
          ORDER BY r.id DESC LIMIT 5";
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $recent_registrations[] = $row;
}

// Get recent queries
$recent_queries = [];
$query = "SELECT m.*, c.name as caller_name FROM mquery m 
          LEFT JOIN caller c ON m.callerid = c.id 
          ORDER BY m.id DESC LIMIT 5";
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $recent_queries[] = $row;
}

// Get monthly registration data for chart (last 6 months)
$monthly_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $query = "SELECT COUNT(*) as count FROM registration 
              WHERE DATE_FORMAT(STR_TO_DATE(date, '%d-%m-%Y'), '%Y-%m') = '$month'";
    $result = mysqli_query($con, $query);
    $count = mysqli_fetch_assoc($result)['count'];
    $monthly_data[] = [
        'month' => date('M Y', strtotime("-$i months")),
        'count' => $count
    ];
}

// Get branch-wise student distribution
$branch_data = [];
$query = "SELECT b.bname, COUNT(r.id) as count FROM branch b 
          LEFT JOIN registration r ON b.id = r.bid 
          WHERE b.status = 1 
          GROUP BY b.id 
          ORDER BY count DESC 
          LIMIT 10";
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $branch_data[] = $row;
}

include('includes/header.php');
?>

<div class="wrapper">
    <?php include('includes/sidebar.php'); ?>

    <div id="content">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-link">
                <i class="fas fa-bars"></i>
            </button>

            <div class="user-menu">
                <div class="user-info">
                    <div class="name"><?php echo htmlspecialchars($admin_name ?? 'Administrator'); ?></div>
                    <div class="role"><?php echo $admin_type == 1 ? 'Super Admin' : ($admin_type == 2 ? 'Manager' : ($admin_type == 3 ? 'Healthcare' : ($admin_type == 4 ? 'Supervisor' : ($admin_type == 5 ? 'Branch' : 'Admin')))); ?></div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="modules/settings/profile.php"><i
                                    class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="modules/settings/global.php"><i
                                    class="fas fa-cog me-2"></i>Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout.php"><i
                                    class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Welcome Header -->
            <div class="page-header d-md-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1>Dashboard Overview</h1>
                    <p class="text-muted small mb-0">Welcome back, <strong><?php echo $admin_name; ?></strong>. Here is what's happening today.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <span class="badge bg-white text-dark shadow-sm border py-2 px-3">
                        <i class="far fa-calendar-alt me-2 text-primary"></i>
                        <?php echo date('D, d M Y'); ?>
                    </span>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card">
                        <div class="icon" style="background: rgba(79, 70, 229, 0.1); color: var(--primary);">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="content">
                            <h3><?php echo number_format($stats['branches']); ?></h3>
                            <p>Total Branches</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card">
                        <div class="icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="content">
                            <h3><?php echo number_format($stats['students']); ?></h3>
                            <p>Total Students</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card">
                        <div class="icon" style="background: rgba(59, 130, 246, 0.1); color: var(--info);">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="content">
                            <h3><?php echo number_format($stats['courses']); ?></h3>
                            <p>Total Courses</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card">
                        <div class="icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div class="content">
                            <h3>₹<?php echo number_format($stats['revenue']); ?></h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Stats Grid -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon mb-0" style="background: var(--slate-100); color: var(--slate-600); width: 44px; height: 44px; font-size: 18px;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="content">
                                <h3 style="font-size: 20px;"><?php echo $stats['managers']; ?></h3>
                                <p style="font-size: 11px;">Managers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon mb-0" style="background: var(--slate-100); color: var(--slate-600); width: 44px; height: 44px; font-size: 18px;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="content">
                                <h3 style="font-size: 20px;"><?php echo $stats['supervisors']; ?></h3>
                                <p style="font-size: 11px;">Supervisors</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon mb-0" style="background: var(--slate-100); color: var(--slate-600); width: 44px; height: 44px; font-size: 18px;">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="content">
                                <h3 style="font-size: 20px;"><?php echo $stats['callers']; ?></h3>
                                <p style="font-size: 11px;">Callers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon mb-0" style="background: var(--slate-100); color: var(--slate-600); width: 44px; height: 44px; font-size: 18px;">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="content">
                                <h3 style="font-size: 20px;"><?php echo $stats['queries']; ?></h3>
                                <p style="font-size: 11px;">Queries</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-4 mb-4">
                <div class="col-xl-8">
                    <div class="table-card h-100">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="mb-0 fw-bold">Registration Trends</h5>
                            <span class="text-muted small">Last 6 Months</span>
                        </div>
                        <canvas id="registrationChart" height="100"></canvas>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="table-card h-100">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="mb-0 fw-bold">Branch Distribution</h5>
                            <i class="fas fa-chart-pie text-muted"></i>
                        </div>
                        <canvas id="branchChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row g-4 mb-4">
                <div class="col-xl-6">
                    <div class="table-card h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold">Recent Registrations</h5>
                            <a href="modules/students/list.php" class="btn btn-sm btn-link text-decoration-none p-0 text-primary fw-bold">
                                View All <i class="fas fa-chevron-right ms-1 small"></i>
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Branch</th>
                                        <th class="text-end">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_registrations as $reg): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-600 text-dark"><?php echo htmlspecialchars($reg['name'] ?? 'N/A'); ?></div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($reg['bname'] ?? 'General'); ?></span>
                                            </td>
                                            <td class="text-end text-muted small"><?php echo htmlspecialchars($reg['date'] ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="table-card h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold">Recent Queries</h5>
                            <a href="modules/inquiries/list.php" class="btn btn-sm btn-link text-decoration-none p-0 text-primary fw-bold">
                                View All <i class="fas fa-chevron-right ms-1 small"></i>
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Caller</th>
                                        <th>Description</th>
                                        <th class="text-end">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_queries as $query): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-600 text-dark"><?php echo htmlspecialchars($query['caller_name'] ?? 'N/A'); ?></div>
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                                    <?php echo htmlspecialchars(substr(strip_tags($query['des'] ?? ''), 0, 50)); ?>...
                                                </span>
                                            </td>
                                            <td class="text-end text-muted small"><?php echo htmlspecialchars($query['date'] ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Registration Trends Chart
    const regCtx = document.getElementById('registrationChart').getContext('2d');
    new Chart(regCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($monthly_data, 'month')); ?>,
            datasets: [{
                label: 'Registrations',
                data: <?php echo json_encode(array_column($monthly_data, 'count')); ?>,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 3,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4f46e5',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { family: 'Inter', size: 13, weight: '600' },
                    bodyFont: { family: 'Inter', size: 12 },
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(226, 232, 240, 0.5)', drawBorder: false },
                    ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }
                }
            }
        }
    });

    // Branch Distribution Chart
    const branchCtx = document.getElementById('branchChart').getContext('2d');
    new Chart(branchCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($branch_data, 'bname')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($branch_data, 'count')); ?>,
                backgroundColor: [
                    '#4f46e5', '#10b981', '#3b82f6', '#f59e0b', '#ef4444',
                    '#8b5cf6', '#ec4899', '#06b6d4', '#14b8a6', '#6366f1'
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 8,
                        boxHeight: 8,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 15,
                        font: { family: 'Inter', size: 11, weight: '500' },
                        color: '#64748b'
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    cornerRadius: 8,
                    bodyFont: { family: 'Inter', size: 12 }
                }
            }
        }
    });
</script>

<?php include('includes/footer.php'); ?>
