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
$supervisor_bids = $_SESSION['supervisor_bids'] ?? [];

// Get assigned branches for the filter dropdown
$branches = [];
if (!empty($supervisor_bids)) {
    $bids_list = implode(',', array_map('intval', $supervisor_bids));
    $b_query = mysqli_query($con, "SELECT id, bname, bcode FROM branch WHERE id IN ($bids_list) AND status = 1 ORDER BY bname ASC");
    while ($b = mysqli_fetch_assoc($b_query)) {
        $branches[] = $b;
    }
}

// Get date range from request or default to this month
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$selected_bid = isset($_GET['bid']) ? (int)$_GET['bid'] : 0;

// Filter condition for branches
$branch_filter = "";
if ($selected_bid > 0 && in_array($selected_bid, $supervisor_bids)) {
    $branch_filter = " AND q.bid = $selected_bid";
} elseif (!empty($supervisor_bids)) {
    $branch_filter = " AND q.bid IN (" . implode(',', array_map('intval', $supervisor_bids)) . ")";
}

// Get performance report
$query = "SELECT c.id, c.regno, c.name, c.mob,
          COUNT(q.id) as total_calls,
          SUM(CASE WHEN q.status = 0 THEN 1 ELSE 0 END) as completed_calls,
          SUM(CASE WHEN q.status = 1 THEN 1 ELSE 0 END) as pending_calls
          FROM caller c
          LEFT JOIN mquery q ON c.id = q.callerid AND DATE(q.date) BETWEEN '$start_date' AND '$end_date' $branch_filter
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
          WHERE c.svid = $supervisor_id AND DATE(q.date) BETWEEN '$start_date' AND '$end_date' $branch_filter
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
<?php 
$page_title = "Reports & Analytics";
include 'includes/header.php'; 
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Reports & Analytics</h1>
        <p class="page-subtitle">Track and analyze caller performance and contact outcomes.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Filter Card -->
    <div class="col-xl-12">
        <div class="card mb-0">
            <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent">
                <h5 class="mb-0"><i class="fas fa-filter text-primary me-2"></i>Filter Reports</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label text-dark fw-medium">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label text-dark fw-medium">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label text-dark fw-medium">Branch Filter</label>
                        <select name="bid" class="form-select">
                            <option value="">All Assigned Branches</option>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?php echo $branch['id']; ?>" <?php echo $selected_bid == $branch['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($branch['bname']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <button type="submit" class="btn btn-primary w-100 py-2 d-flex justify-content-center align-items-center gap-2">
                            <i class="fas fa-search"></i> Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm" style="background-color: #f8fafc;">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; background-color: rgba(79, 70, 229, 0.1); color: var(--primary-color);">
                    <i class="fas fa-phone-alt fs-4"></i>
                </div>
                <div>
                    <h3 class="mb-1 text-dark fw-bold fs-3"><?php echo $total_calls; ?></h3>
                    <p class="mb-0 text-muted fw-medium text-uppercase small">Total Calls</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm" style="background-color: #f8fafc;">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-check-circle fs-4"></i>
                </div>
                <div>
                    <h3 class="mb-1 text-dark fw-bold fs-3"><?php echo $total_completed; ?></h3>
                    <p class="mb-0 text-muted fw-medium text-uppercase small">Completed</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm" style="background-color: #f8fafc;">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-clock fs-4"></i>
                </div>
                <div>
                    <h3 class="mb-1 text-dark fw-bold fs-3"><?php echo $total_pending; ?></h3>
                    <p class="mb-0 text-muted fw-medium text-uppercase small">Pending</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Daily Calls Chart -->
    <div class="col-xl-12">
        <div class="card mb-0">
            <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent">
                <h5 class="mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Daily Calls Trend</h5>
            </div>
            <div class="card-body">
                <div style="height: 300px; width: 100%;">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Table -->
    <div class="col-xl-12">
        <div class="card mb-0">
            <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0"><i class="fas fa-table text-primary me-2"></i>Caller Performance Report</h5>
                <button class="btn btn-sm btn-light border shadow-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print Report
                </button>
            </div>
            <div class="card-body mt-3">
                <div class="table-responsive">
                    <table id="performanceTable" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Regno</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th class="text-center">Total Calls</th>
                                <th class="text-center">Completed</th>
                                <th class="text-center">Pending</th>
                                <th class="text-center">Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php foreach ($performance as $p): ?>
                                <tr>
                                    <td><span class="fw-medium text-dark"><?php echo htmlspecialchars($p['regno']); ?></span></td>
                                    <td class="fw-medium text-dark"><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td><span class="text-muted"><?php echo htmlspecialchars($p['mob']); ?></span></td>
                                    <td class="text-center"><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-2 py-1 fs-6"><?php echo $p['total_calls']; ?></span></td>
                                    <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2 py-1"><?php echo $p['completed_calls']; ?></span></td>
                                    <td class="text-center"><span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1"><?php echo $p['pending_calls']; ?></span></td>
                                    <td class="text-center">
                                        <?php
                                        $rate = $p['total_calls'] > 0 ? round(($p['completed_calls'] / $p['total_calls']) * 100, 1) : 0;
                                        $color = $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger');
                                        ?>
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; background-color: #f1f5f9; width: 60px;">
                                                <div class="progress-bar bg-<?php echo $color; ?>" role="progressbar" style="width: <?php echo $rate; ?>%; border-radius: 3px;"></div>
                                            </div>
                                            <span class="small fw-semibold text-<?php echo $color; ?>" style="width: 35px;"><?php echo $rate; ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($performance)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No performance data found for this period.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        $('#performanceTable').DataTable({
            order: [[3, 'desc']],
            pageLength: 25,
            dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
            buttons: [
                { extend: 'copy', className: 'btn btn-sm btn-light border me-1' },
                { extend: 'csv', className: 'btn btn-sm btn-light border me-1' },
                { extend: 'excel', className: 'btn btn-sm btn-light border' }
            ]
        });

        // Daily calls chart
        const ctx = document.getElementById('dailyChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($daily_stats, 'call_date')); ?>,
                datasets: [{
                    label: 'Daily Calls',
                    data: <?php echo json_encode(array_column($daily_stats, 'total_calls')); ?>,
                    borderColor: '#4f46e5',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
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
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: "'Inter', sans-serif", size: 13 },
                        bodyFont: { family: "'Inter', sans-serif", size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            font: { family: "'Inter', sans-serif", size: 11 },
                            color: '#64748b'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            font: { family: "'Inter', sans-serif", size: 11 },
                            color: '#64748b',
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>