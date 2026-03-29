<?php
session_start();

// Check if supervisor is logged in
if (!isset($_SESSION['supervisor_id'])) {
    header('Location: ../supervisor_login.php');
    exit;
}

require_once(__DIR__ . '/../connection.php');

$supervisor_id = $_SESSION['supervisor_id'];
$supervisor_name = $_SESSION['supervisor_name'];
$supervisor_bid = $_SESSION['supervisor_bid'] ?? 0;

// Get statistics
$stats = [];

// Total callers under this supervisor
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM caller WHERE svid = $supervisor_id");
$stats['total_callers'] = mysqli_fetch_assoc($result)['total'];

// Active callers
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM caller WHERE svid = $supervisor_id AND status = 1");
$stats['active_callers'] = mysqli_fetch_assoc($result)['total'];

// Total calls made by supervised callers today
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM mquery q 
              JOIN caller c ON q.callerid = c.id 
              WHERE c.svid = $supervisor_id AND DATE(q.date) = CURDATE()");
$stats['today_calls'] = mysqli_fetch_assoc($result)['total'];

// Pending calls
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM mquery q 
              JOIN caller c ON q.callerid = c.id 
              WHERE c.svid = $supervisor_id AND q.status = 1");
$stats['pending_calls'] = mysqli_fetch_assoc($result)['total'];

// Get callers list
$query = "SELECT c.*, b.bname, 
          (SELECT COUNT(*) FROM mquery WHERE callerid = c.id) as total_calls,
          (SELECT COUNT(*) FROM mquery WHERE callerid = c.id AND DATE(date) = CURDATE()) as today_calls,
          (SELECT COUNT(*) FROM dataallotment WHERE callerid = c.id) as assigned_data
          FROM caller c 
          LEFT JOIN branch b ON c.bid = b.id 
          WHERE c.svid = $supervisor_id 
          ORDER BY c.id DESC";
$callers = [];
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $callers[] = $row;
}

// Get recent calls by supervised callers
$query = "SELECT q.*, c.name as caller_name, b.bname 
          FROM mquery q 
          JOIN caller c ON q.callerid = c.id 
          LEFT JOIN branch b ON q.bid = b.id 
          WHERE c.svid = $supervisor_id 
          ORDER BY q.id DESC 
          LIMIT 20";
$recent_calls = [];
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $recent_calls[] = $row;
}
?>
<?php 
$page_title = "Dashboard Overview";
include 'includes/header.php'; 
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Overview</h1>
        <p class="page-subtitle">Welcome back, monitor your team's performance today.</p>
    </div>
    <div>
        <a href="assign_data.php" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="fas fa-tasks"></i> Assign Calling Data
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm" style="background-color: #f8fafc;">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: rgba(79, 70, 229, 0.1); color: var(--primary-color);">
                    <i class="fas fa-users fs-5"></i>
                </div>
                <div>
                    <h3 class="mb-1 text-dark fw-bold fs-4"><?php echo $stats['total_callers']; ?></h3>
                    <p class="mb-0 text-muted small fw-medium text-uppercase">Total Callers</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm" style="background-color: #f8fafc;">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-user-check fs-5"></i>
                </div>
                <div>
                    <h3 class="mb-1 text-dark fw-bold fs-4"><?php echo $stats['active_callers']; ?></h3>
                    <p class="mb-0 text-muted small fw-medium text-uppercase">Active Callers</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm" style="background-color: #f8fafc;">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <i class="fas fa-phone-alt fs-5"></i>
                </div>
                <div>
                    <h3 class="mb-1 text-dark fw-bold fs-4"><?php echo $stats['today_calls']; ?></h3>
                    <p class="mb-0 text-muted small fw-medium text-uppercase">Today's Calls</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm" style="background-color: #f8fafc;">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-clock fs-5"></i>
                </div>
                <div>
                    <h3 class="mb-1 text-dark fw-bold fs-4"><?php echo $stats['pending_calls']; ?></h3>
                    <p class="mb-0 text-muted small fw-medium text-uppercase">Pending Calls</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Callers Table -->
<div class="card">
    <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent">
        <h5 class="mb-0"><i class="fas fa-list text-primary me-2"></i>My Callers Performance</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="callersTable" class="table table-hover mb-0 w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Regno</th>
                        <th>Name</th>
                        <th>Branch</th>
                        <th>Mobile</th>
                        <th>Total Calls</th>
                        <th>Today's Calls</th>
                        <th>Assigned Data</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php foreach ($callers as $caller): ?>
                        <tr>
                            <td class="text-muted">#<?php echo $caller['id']; ?></td>
                            <td><span class="fw-medium text-dark"><?php echo htmlspecialchars($caller['regno']); ?></span></td>
                            <td class="fw-medium text-dark"><?php echo htmlspecialchars($caller['name']); ?></td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($caller['bname'] ?? 'N/A'); ?></span></td>
                            <td><?php echo htmlspecialchars($caller['mob']); ?></td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-2 py-1"><?php echo $caller['total_calls']; ?></span></td>
                            <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2 py-1"><?php echo $caller['today_calls']; ?></span></td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 px-2 py-1"><?php echo $caller['assigned_data']; ?></span></td>
                            <td>
                                <?php if ($caller['status'] == 1): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="caller_details.php?id=<?php echo $caller['id']; ?>" class="btn btn-sm btn-light border shadow-sm px-3">
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Calls -->
<div class="card mt-4">
    <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent">
        <h5 class="mb-0"><i class="fas fa-history text-primary me-2"></i>Recent Calls by My Team</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="callsTable" class="table table-hover mb-0 w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Caller</th>
                        <th>Branch</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php foreach ($recent_calls as $call): ?>
                        <tr>
                            <td class="text-muted">#<?php echo $call['id']; ?></td>
                            <td><span class="fw-medium text-dark"><?php echo htmlspecialchars($call['caller_name']); ?></span></td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($call['bname'] ?? 'N/A'); ?></span></td>
                            <td><span class="text-muted"><?php echo htmlspecialchars(substr($call['des'], 0, 50)); ?>...</span></td>
                            <td class="text-muted"><?php echo htmlspecialchars(date('M d, Y', strtotime($call['date']))); ?></td>
                            <td>
                                <?php if ($call['status'] == 1): ?>
                                    <span class="badge bg-warning text-dark border-warning border-opacity-25 px-2 py-1">Pending</span>
                                <?php else: ?>
                                    <span class="badge bg-success border-success border-opacity-25 px-2 py-1">Completed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>
    $(document).ready(function () {
        $('#callersTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 10
        });
        $('#callsTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 10
        });
    });
</script>