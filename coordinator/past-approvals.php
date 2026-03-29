<?php
require_once('../connection.php');
require_once('config/auth.php');

$page_title = 'Approval & Rejection History';

// Fetch History (coordinator_approval_status 2 or 3)
$history_students = [];
$query = "SELECT r.*, 
                 (SELECT cn.name FROM mquery m JOIN caller cn ON m.callerid = cn.id WHERE m.studentid = r.id AND m.status = 0 ORDER BY m.id DESC LIMIT 1) as caller_name 
          FROM registration r 
          WHERE r.coordinator_approval_status IN (2, 3) AND r.bid = ? 
          ORDER BY r.id DESC";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $coordinator_bid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $history_students[] = $row;
}

include('includes/header.php');
?>
<div class="wrapper d-flex">
    <?php include('includes/sidebar.php'); ?>

    <div id="content" class="flex-grow-1">
        
        <!-- Minimal Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1 h3">Approval History</h2>
                <p class="text-muted small mb-0"><i class="fas fa-clock-rotate-left me-1 opacity-50"></i>Audit trail for all past decisions</p>
            </div>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm border-light-subtle bg-white">
                <i class="fas fa-arrow-left me-2 x-small"></i>Dashboard
            </a>
        </div>

        <div class="minimal-card p-0 shadow-sm border-light-subtle overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="historyTable" style="width:100%">
                    <thead>
                        <tr>
                            <th class="ps-4">Reg No</th>
                            <th>Student Profile</th>
                            <th>Processing Info</th>
                            <th>Verified By</th>
                            <th class="text-center pe-4">Final Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history_students as $student): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark small"><?php echo htmlspecialchars($student['regno']); ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($student['name']); ?></span>
                                        <span class="text-muted x-small"><?php echo htmlspecialchars($student['father']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($student['reg_login_id']): ?>
                                        <div class="p-2 border rounded-3 bg-light x-small shadow-sm d-inline-block">
                                            <i class="fas fa-key me-1 text-primary opacity-50"></i><code class="text-dark fw-medium"><?php echo htmlspecialchars($student['reg_login_id']); ?></code>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted x-small italic font-monospace">Direct Confirmation</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge-minimal badge-blue">
                                        <i class="fas fa-headset me-1 x-small opacity-50"></i><?php echo htmlspecialchars($student['caller_name'] ?? 'Direct'); ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <?php if ($student['coordinator_approval_status'] == 2): ?>
                                        <span class="badge-minimal badge-green shadow-sm">
                                            <i class="fas fa-check-circle me-1 small"></i>Approved
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-minimal badge-red shadow-sm">
                                            <i class="fas fa-times-circle me-1 small"></i>Rejected
                                        </span>
                                    <?php endif; ?>
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
        $('#historyTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "language": {
                "emptyTable": '<div class="text-center py-5 text-muted small">No historical records found</div>'
            }
        });
    });
</script>
<style>
    .x-small { font-size: 0.75rem; }
    .italic { font-style: italic; }
</style>
<?php include('includes/footer.php'); ?>
