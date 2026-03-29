<?php
require_once('../connection.php');
require_once('config/auth.php');

$page_title = 'Direct Admission Confirmations';

// Fetch Direct Admissions (coordinator_approval_status = 1)
$direct_admissions = [];
$direct_query = "SELECT r.*, 
                 (SELECT cn.name FROM mquery m JOIN caller cn ON m.callerid = cn.id WHERE m.studentid = r.id AND m.status = 0 ORDER BY m.id DESC LIMIT 1) as caller_name 
                 FROM registration r 
                 WHERE r.coordinator_approval_status = 1 AND r.reg_status = 0 AND r.bid = ? 
                 ORDER BY r.id DESC";
$d_stmt = mysqli_prepare($con, $direct_query);
mysqli_stmt_bind_param($d_stmt, "i", $coordinator_bid);
mysqli_stmt_execute($d_stmt);
$d_result = mysqli_stmt_get_result($d_stmt);
while ($row = mysqli_fetch_assoc($d_result)) {
    $direct_admissions[] = $row;
}
$direct_count = count($direct_admissions);

include('includes/header.php');
?>
<div class="wrapper d-flex">
    <?php include('includes/sidebar.php'); ?>

    <div id="content" class="flex-grow-1">
        
        <!-- Minimal Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1 h3">Direct Admissions</h2>
                <p class="text-muted small mb-0"><i class="fas fa-headset me-1 opacity-50"></i>Caller confirmed students</p>
            </div>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm border-light-subtle bg-white">
                <i class="fas fa-arrow-left me-2 x-small"></i>Dashboard
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="badge-minimal badge-green w-100 p-3 mb-4 shadow-sm">
                <i class="fas fa-check-circle me-2"></i>Action processed successfully!
            </div>
        <?php endif; ?>

        <div class="minimal-card p-0 shadow-sm border-light-subtle overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="directTable">
                    <thead>
                        <tr>
                            <th class="ps-4">Reg No</th>
                            <th>Student Info</th>
                            <th>Contact</th>
                            <th>Verified By</th>
                            <th class="text-center pe-4">Final Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($direct_admissions as $student): ?>
                            <tr>
                                <td class="ps-4 text-dark fw-bold small"><?php echo htmlspecialchars($student['regno']); ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($student['name']); ?></span>
                                        <span class="text-muted x-small">Guardian: <?php echo htmlspecialchars($student['father']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-medium text-dark small"><?php echo htmlspecialchars($student['mob']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-minimal badge-blue">
                                        <i class="fas fa-user-check me-1 x-small opacity-50"></i><?php echo htmlspecialchars($student['caller_name'] ?? 'Direct'); ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <form action="action.php" method="POST" class="d-inline">
                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="redirect" value="direct_approvals.php">
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold border-0" style="background:#4a6cf7" onclick="return confirm('Finalize this admission?');">
                                                Confirm
                                            </button>
                                        </form>
                                        <form action="action.php" method="POST" class="d-inline">
                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="redirect" value="direct_approvals.php">
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold border-light-subtle bg-white text-danger" onclick="return confirm('Reject this student?');">
                                                Reject
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
        $('#directTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "language": {
                "emptyTable": '<div class="text-center py-5 text-muted small"><i class="fas fa-clipboard-list fa-2x opacity-25 d-block mb-2"></i>No pending confirmations</div>'
            }
        });
    });
</script>
<style>
    .x-small { font-size: 0.75rem; }
</style>
<?php include('includes/footer.php'); ?>
