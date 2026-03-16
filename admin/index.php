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
                    <div class="name"><?php echo $admin_name; ?></div>
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
            <!-- Page Header -->
            <div class="page-header">
                <h1>Dashboard Overview</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="content ms-3">
                                <h3><?php echo $stats['branches']; ?></h3>
                                <p>Total Branches</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="content ms-3">
                                <h3><?php echo $stats['students']; ?></h3>
                                <p>Total Students</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="content ms-3">
                                <h3><?php echo $stats['courses']; ?></h3>
                                <p>Total Courses</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <i class="fas fa-rupee-sign"></i>
                            </div>
                            <div class="content ms-3">
                                <h3>₹<?php echo number_format($stats['revenue']); ?></h3>
                                <p>Total Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Stats -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="content ms-3">
                                <h3><?php echo $stats['managers']; ?></h3>
                                <p>Managers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="content ms-3">
                                <h3><?php echo $stats['supervisors']; ?></h3>
                                <p>Supervisors</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="content ms-3">
                                <h3><?php echo $stats['callers']; ?></h3>
                                <p>Callers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="content ms-3">
                                <h3><?php echo $stats['queries']; ?></h3>
                                <p>Active Queries</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row g-3 mb-4">
                <div class="col-xl-8">
                    <div class="table-card">
                        <h5 class="mb-3">Registration Trends (Last 6 Months)</h5>
                        <canvas id="registrationChart" height="80"></canvas>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="table-card">
                        <h5 class="mb-3">Top 10 Branches by Students</h5>
                        <canvas id="branchChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="row g-3">
                <div class="col-xl-6">
                    <div class="table-card">
                        <h5 class="mb-3">Recent Registrations</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Branch</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_registrations as $reg): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($reg['name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($reg['bname'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($reg['date'] ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="modules/students/list.php" class="btn btn-sm btn-primary mt-2">View All Students</a>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="table-card">
                        <h5 class="mb-3">Recent Queries</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Caller</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_queries as $query): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($query['caller_name'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars(substr(strip_tags($query['des'] ?? ''), 0, 50)); ?>...
                                            </td>
                                            <td><?php echo htmlspecialchars($query['date'] ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="modules/inquiries/list.php" class="btn btn-sm btn-primary mt-2">View All Queries</a>
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
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
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
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(118, 75, 162, 0.8)',
                    'rgba(240, 147, 251, 0.8)',
                    'rgba(245, 87, 108, 0.8)',
                    'rgba(79, 172, 254, 0.8)',
                    'rgba(0, 242, 254, 0.8)',
                    'rgba(247, 112, 154, 0.8)',
                    'rgba(254, 225, 64, 0.8)',
                    'rgba(48, 207, 208, 0.8)',
                    'rgba(51, 8, 103, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 10
                        }
                    }
                }
            }
        }
    });
</script>

<?php include('includes/footer.php'); ?>
