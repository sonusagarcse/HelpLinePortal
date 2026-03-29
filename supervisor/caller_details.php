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

// Get caller details
$caller_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Verify caller belongs to this supervisor
$query = "SELECT c.*, b.bname, b.bcode 
          FROM caller c 
          LEFT JOIN branch b ON c.bid = b.id 
          WHERE c.id = ? AND c.svid = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ii", $caller_id, $supervisor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$caller = mysqli_fetch_assoc($result);

if (!$caller) {
    header('Location: index.php?error=not_found');
    exit;
}

// Get caller statistics
$stats = [];

// Total calls
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM mquery WHERE callerid = $caller_id");
$stats['total_calls'] = mysqli_fetch_assoc($result)['total'];

// Completed calls
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM mquery WHERE callerid = $caller_id AND status = 0");
$stats['completed'] = mysqli_fetch_assoc($result)['total'];

// Pending calls
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM mquery WHERE callerid = $caller_id AND status = 1");
$stats['pending'] = mysqli_fetch_assoc($result)['total'];

// Today's calls
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM mquery WHERE callerid = $caller_id AND DATE(date) = CURDATE()");
$stats['today'] = mysqli_fetch_assoc($result)['total'];

// Get call history
$query = "SELECT q.*, b.bname 
          FROM mquery q 
          LEFT JOIN branch b ON q.bid = b.id 
          WHERE q.callerid = $caller_id 
          ORDER BY q.id DESC 
          LIMIT 50";
$calls = [];
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $calls[] = $row;
}
?>
<?php 
$page_title = "Caller Details";
include 'includes/header.php'; 
?>

<!-- Action Bar -->
<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div class="d-flex align-items-center gap-3">
        <a href="index.php" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center shadow-sm hover-elevate" style="width: 40px; height: 40px;" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h4 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <?php echo htmlspecialchars($caller['name']); ?>
                <?php if ($caller['status'] == 1): ?>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 fs-6 d-flex align-items-center gap-1">
                        <i class="fas fa-circle" style="font-size: 6px;"></i> Active
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-1 fs-6 d-flex align-items-center gap-1">
                        <i class="fas fa-circle" style="font-size: 6px;"></i> Inactive
                    </span>
                <?php endif; ?>
            </h1>
            <p class="text-muted mb-0 small"><i class="fas fa-id-badge me-1"></i><?php echo htmlspecialchars($caller['regno']); ?></p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Caller Profile & Stats -->
    <div class="col-xl-4 col-lg-5">
        
        <!-- Profile Card -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                <h5 class="mb-0 fw-semibold text-dark"><i class="fas fa-address-card text-primary opacity-75 me-2"></i>Profile Details</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom border-light">
                        <span class="text-muted small fw-medium">Father's Name</span>
                        <span class="text-dark fw-medium text-end"><?php echo htmlspecialchars($caller['father']); ?></span>
                    </li>
                    <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom border-light">
                        <span class="text-muted small fw-medium">Mobile Number</span>
                        <a href="tel:<?php echo htmlspecialchars($caller['mob']); ?>" class="text-primary fw-semibold text-decoration-none">
                            <i class="fas fa-phone-alt me-1 opacity-50"></i><?php echo htmlspecialchars($caller['mob']); ?>
                        </a>
                    </li>
                    <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom border-light">
                        <span class="text-muted small fw-medium">Email Address</span>
                        <a href="mailto:<?php echo htmlspecialchars($caller['email']); ?>" class="text-primary fw-medium text-decoration-none text-truncate" style="max-width: 150px;">
                            <?php echo htmlspecialchars($caller['email']); ?>
                        </a>
                    </li>
                    <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom border-light">
                        <span class="text-muted small fw-medium">Branch Assignment</span>
                        <span class="badge bg-light text-dark border px-2 py-1 text-wrap text-end" style="max-width: 150px;">
                            <?php echo htmlspecialchars($caller['bcode'] . ' - ' . $caller['bname']); ?>
                        </span>
                    </li>
                    <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-medium">Date Joined</span>
                        <span class="text-dark fw-medium"><i class="far fa-calendar-alt text-muted me-1"></i><?php echo date('d M, Y', strtotime($caller['doj'])); ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Performance Stats Grid -->
        <h6 class="text-uppercase text-muted fw-bold mb-3 small pe-3 ms-2">Performance Metrics</h6>
        <div class="row g-3">
            <div class="col-6">
                <div class="card h-100 border-0 shadow-sm" style="background-color: #f8fafc;">
                    <div class="card-body text-center p-3">
                        <div class="d-inline-flex bg-primary bg-opacity-10 text-primary rounded-circle p-2 mb-2">
                            <i class="fas fa-phone-volume" style="width: 20px; height: 20px; line-height: 20px;"></i>
                        </div>
                        <h3 class="mb-0 fw-bold text-dark fs-4"><?php echo $stats['total_calls']; ?></h3>
                        <p class="text-muted small mb-0 fw-medium">Total Calls</p>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card h-100 border-0 shadow-sm" style="background-color: #f8fafc;">
                    <div class="card-body text-center p-3">
                        <div class="d-inline-flex bg-success bg-opacity-10 text-success rounded-circle p-2 mb-2">
                            <i class="fas fa-check-circle" style="width: 20px; height: 20px; line-height: 20px;"></i>
                        </div>
                        <h3 class="mb-0 fw-bold text-dark fs-4"><?php echo $stats['completed']; ?></h3>
                        <p class="text-muted small mb-0 fw-medium">Completed</p>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card h-100 border-0 shadow-sm" style="background-color: #f8fafc;">
                    <div class="card-body text-center p-3">
                        <div class="d-inline-flex bg-warning bg-opacity-10 text-warning rounded-circle p-2 mb-2">
                            <i class="fas fa-clock" style="width: 20px; height: 20px; line-height: 20px;"></i>
                        </div>
                        <h3 class="mb-0 fw-bold text-dark fs-4"><?php echo $stats['pending']; ?></h3>
                        <p class="text-muted small mb-0 fw-medium">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card h-100 border-0 shadow-sm" style="background-color: #f8fafc;">
                    <div class="card-body text-center p-3">
                        <div class="d-inline-flex bg-info bg-opacity-10 text-info rounded-circle p-2 mb-2">
                            <i class="fas fa-calendar-day" style="width: 20px; height: 20px; line-height: 20px;"></i>
                        </div>
                        <h3 class="mb-0 fw-bold text-dark fs-4"><?php echo $stats['today']; ?></h3>
                        <p class="text-muted small mb-0 fw-medium">Today's Act</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Call History table -->
    <div class="col-xl-8 col-lg-7">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom pt-4 pb-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-dark"><i class="fas fa-history text-primary opacity-75 me-2"></i>Recent Call Log</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table id="callsTable" class="table table-hover align-middle w-100">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-muted fw-semibold small text-uppercase">ID</th>
                                <th class="text-muted fw-semibold small text-uppercase">Branch</th>
                                <th class="text-muted fw-semibold small text-uppercase">Details</th>
                                <th class="text-muted fw-semibold small text-uppercase">Date & Time</th>
                                <th class="text-muted fw-semibold small text-uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php foreach ($calls as $call): ?>
                                <tr>
                                    <td><span class="text-muted small">#<?php echo $call['id']; ?></span></td>
                                    <td>
                                        <span class="badge bg-light border text-dark fw-medium">
                                            <?php echo htmlspecialchars($call['bname'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate text-dark" style="max-width: 200px;" title="<?php echo htmlspecialchars($call['des']); ?>">
                                            <?php echo htmlspecialchars($call['des']) ?: '<em class="text-muted">No details provided</em>'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium text-dark"><?php echo date('d M, Y', strtotime($call['date'])); ?></span>
                                            <span class="small text-muted"><?php echo date('h:i A', strtotime($call['date'])); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($call['status'] == 1): ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">
                                                <i class="fas fa-clock fs-xs me-1"></i> Pending
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                                                <i class="fas fa-check fs-xs me-1"></i> Completed
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($calls)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted d-flex flex-column align-items-center">
                                            <i class="fas fa-clipboard-list fs-1 mb-3 opacity-25"></i>
                                            <h6 class="fw-semibold">No calls logged yet</h6>
                                            <p class="small mb-0">This caller hasn't made any recorded calls.</p>
                                        </div>
                                    </td>
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        $('#callsTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "All"]],
            language: {
                search: "",
                searchPlaceholder: "Search calls...",
                lengthMenu: "Show _MENU_ entries"
            },
            dom: '<"row mb-3 align-items-center px-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row align-items-center px-3 py-3 border-top"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm mb-0 justify-content-end');
                $('.dataTables_filter input').addClass('form-control form-control-sm border-light shadow-sm').removeClass('form-control-sm');
                $('.dataTables_length select').addClass('form-select form-select-sm border-light shadow-sm');
            }
        });
    });
</script>