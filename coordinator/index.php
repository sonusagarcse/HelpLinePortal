<?php
require_once('../connection.php');
require_once('config/auth.php');

$page_title = 'Pending Approvals Dashboard';

// Fetch pending students
$pending_students = [];
$query = "SELECT r.*, 
                 (SELECT c.name FROM mquery m JOIN caller c ON m.callerid = c.id WHERE m.studentid = r.id AND m.status = 0 ORDER BY m.id DESC LIMIT 1) as caller_name 
          FROM registration r 
          WHERE r.coordinator_approval_status = 1 AND r.bid = ? 
          ORDER BY r.id DESC";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $coordinator_bid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $pending_students[] = $row;
}
$pending_count = count($pending_students);

include('includes/header.php');
?>
<div class="wrapper d-flex">
    <?php include('includes/sidebar.php'); ?>

    <div id="content" class="flex-grow-1 p-3 p-md-4">
        
        <div class="glass-card mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 border-0" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
            <div>
                <h2 class="fw-bold mb-1" style="color: #1e3c72;">Welcome back, <?php echo htmlspecialchars($coordinator_name); ?>!</h2>
                <div class="d-flex align-items-center mt-2 flex-wrap gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-building me-2"></i><?php echo htmlspecialchars($coordinator_bname); ?>
                    </span>
                    <span class="badge bg-warning text-dark border border-warning px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-clock me-2"></i><?php echo $pending_count; ?> Pending Approvals
                    </span>
                </div>
            </div>
            <div class="text-md-end text-center mt-3 mt-md-0 d-flex flex-column align-items-center align-items-md-end">
                <div class="badge bg-white text-dark p-2 border shadow-sm px-4 py-2 rounded-3 mb-2 fs-6">
                    <i class="far fa-calendar-alt text-primary me-2"></i><?php echo date('l, d M Y'); ?>
                </div>
                <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-4 shadow-sm fw-bold">
                    <i class="fas fa-power-off me-2"></i>Sign Out
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
                <i class="fas fa-check-circle me-2"></i>Action processed successfully `<?php echo htmlspecialchars($_GET['success']); ?>`!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
                <h4 class="fw-bold mb-0" style="color: #1a1a2e;"><i class="fas fa-tasks text-primary me-2"></i>Pending Confirmations</h4>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="pendingTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-top-0">Reg No</th>
                            <th class="border-top-0">Student Profile</th>
                            <th class="border-top-0">Contact</th>
                            <th class="border-top-0">Verified By</th>
                            <th class="border-top-0">Date Sent</th>
                            <th class="border-top-0 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_students as $student): ?>
                            <tr>
                                <td><span class="badge bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25 px-2 py-1"><?php echo htmlspecialchars($student['regno']); ?></span></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong class="text-dark fs-6"><?php echo htmlspecialchars($student['name']); ?></strong>
                                        <span class="text-muted small"><?php echo htmlspecialchars($student['father']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="fas fa-phone-alt text-primary small"></i>
                                        </div>
                                        <span class="fw-medium text-dark"><?php echo htmlspecialchars($student['mob']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="bg-light rounded-pill px-3 py-1 d-inline-flex align-items-center border shadow-sm">
                                        <i class="fas fa-headset text-secondary me-2"></i>
                                        <span class="fw-semibold text-secondary-emphasis"><?php echo htmlspecialchars($student['caller_name'] ?? 'Unknown Caller'); ?></span>
                                    </div>
                                </td>
                                <td><span class="text-muted"><i class="far fa-clock me-1"></i><?php echo date('d M Y', strtotime($student['date'])); ?></span></td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <form action="action.php" method="POST" class="d-inline">
                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-success btn-sm rounded-3 shadow-sm px-3 fw-bold" onclick="return confirm('Confirm authentic admission? Earning will be credited to Caller.');">
                                                <i class="fas fa-check me-1"></i> Approve
                                            </button>
                                        </form>
                                        <form action="action.php" method="POST" class="d-inline">
                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-danger btn-sm rounded-3 shadow-sm px-3 fw-bold" onclick="return confirm('Reject this confirmation?');">
                                                <i class="fas fa-times me-1"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#pendingTable').DataTable({
            "order": [[4, "desc"]], /* Sort by date */
            "pageLength": 10,
            "language": {
                "emptyTable": '<div class="text-center py-5"><i class="fas fa-clipboard-check fa-3x text-success opacity-50 mb-3 d-block"></i><h5 class="text-muted fw-bold">Queue Empty</h5><p class="text-muted mb-0">All caller confirmations have been approved or rejected.</p></div>'
            }
        });
    });
</script>
<?php include('includes/footer.php'); ?>
