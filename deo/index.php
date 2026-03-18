<?php
require_once('../connection.php');
require_once('config/auth.php');

$page_title = 'Dashboard';

$stats = [];
$today = date('d-m-Y');
$yesterday = date('d-m-Y', strtotime("-1 day"));
$current_month = date('-m-Y');

// Stats filtering by DEO ID
// Stats filtering by DEO ID and Active Branch
function get_count($con, $deo_id, $bid, $date_pattern) {
    if (strpos($date_pattern, '%') !== false) {
        $query = "SELECT COUNT(*) as count FROM registration WHERE callerid = ? AND bid = ? AND date LIKE ?";
    } else {
        $query = "SELECT COUNT(*) as count FROM registration WHERE callerid = ? AND bid = ? AND date = ?";
    }
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "iis", $deo_id, $bid, $date_pattern);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt)->fetch_assoc()['count'];
}

$stats['today'] = get_count($con, $deo_id, $active_bid, $today);
$stats['yesterday'] = get_count($con, $deo_id, $active_bid, $yesterday);
$stats['monthly'] = get_count($con, $deo_id, $active_bid, "%" . $current_month);

// Total entries for this branch (by anyone)
$q_total = "SELECT COUNT(*) as count FROM registration WHERE bid = ?";
$s_total = mysqli_prepare($con, $q_total);
mysqli_stmt_bind_param($s_total, "i", $active_bid);
mysqli_stmt_execute($s_total);
$stats['branch_total'] = mysqli_stmt_get_result($s_total)->fetch_assoc()['count'];

// Recent entries
$recent_entries = [];
$query = "SELECT r.*, b.bname FROM registration r 
          LEFT JOIN branch b ON r.bid = b.id 
          WHERE r.callerid = ? AND r.bid = ? 
          ORDER BY r.id DESC LIMIT 10";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ii", $deo_id, $active_bid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $recent_entries[] = $row;
}

include('includes/header.php');
?>

<div class="wrapper d-flex">
    <?php include('includes/sidebar.php'); ?>

    <div id="content" class="flex-grow-1 p-3 p-md-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-0">Welcome, <?php echo htmlspecialchars($deo_name); ?></h2>
                <div class="d-flex align-items-center mt-1">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill">
                        <i class="fas fa-building me-2"></i><?php echo htmlspecialchars($active_bname); ?>
                    </span>
                    <p class="text-muted mb-0 ms-3">Here's your data entry performance overview</p>
                </div>
            </div>
            <div class="text-md-end">
                <span class="badge bg-white text-dark p-2 border shadow-sm"><?php echo date('l, d M Y'); ?></span>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card p-4 bg-white shadow-sm border-0 rounded-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-calendar-day text-primary fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?php echo $stats['today']; ?></h3>
                            <p class="text-muted mb-0">Added Today</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card p-4 bg-white shadow-sm border-0 rounded-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-history text-success fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?php echo $stats['yesterday']; ?></h3>
                            <p class="text-muted mb-0">Added Yesterday</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card p-4 bg-white shadow-sm border-0 rounded-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-calendar-alt text-info fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?php echo $stats['monthly']; ?></h3>
                            <p class="text-muted mb-0">This Month</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card p-4 bg-white shadow-sm border-0 rounded-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-users text-warning fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold"><?php echo $stats['branch_total']; ?></h3>
                            <p class="text-muted mb-0">Total Branch Entries</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-4 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Your Recent Entries</h4>
                <a href="add_student.php" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-plus me-2"></i>New Entry
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Reg No</th>
                            <th>Student Name</th>
                            <th>Mobile</th>
                            <th>Branch</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_entries)): ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">No entries found yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_entries as $e): ?>
                                <tr>
                                    <td><span class="badge bg-secondary opacity-75"><?php echo $e['regno']; ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($e['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($e['mob']); ?></td>
                                    <td><?php echo htmlspecialchars($e['bname'] ?? 'N/A'); ?></td>
                                    <td><?php echo $e['date']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
