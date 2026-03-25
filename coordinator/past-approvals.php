<?php
require_once('../connection.php');
require_once('config/auth.php');

$page_title = 'Approval History Dashboard';

// Fetch past approvals (Approved = 2, Rejected = 3)
$history_students = [];
// Assuming same subquery for caller name
$query = "SELECT r.*, 
                 (SELECT c.name FROM mquery m JOIN caller c ON m.callerid = c.id WHERE m.studentid = r.id AND m.status = 0 ORDER BY m.id DESC LIMIT 1) as caller_name 
          FROM registration r 
          WHERE r.coordinator_approval_status IN (2, 3) AND r.bid = ? 
          ORDER BY r.id DESC";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $coordinator_bid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$approved_count = 0;
$rejected_count = 0;

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['coordinator_approval_status'] == 2) {
        $approved_count++;
    } elseif ($row['coordinator_approval_status'] == 3) {
        $rejected_count++;
    }
    $history_students[] = $row;
}
$total_count = count($history_students);

include('includes/header.php');
?>
<div class="wrapper d-flex">
    <?php include('includes/sidebar.php'); ?>

    <div id="content" class="flex-grow-1 p-3 p-md-4">
        
        <div class="glass-card mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 border-0" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);">
            <div>
                <h2 class="fw-bold mb-1" style="color: #1e3c72;">Approval History</h2>
                <div class="d-flex align-items-center mt-2 flex-wrap gap-2">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-check-double me-2"></i><?php echo $approved_count; ?> Approved
                    </span>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-times me-2"></i><?php echo $rejected_count; ?> Rejected
                    </span>
                </div>
            </div>
            <div class="text-md-end text-center mt-3 mt-md-0 d-flex flex-column align-items-center align-items-md-end">
                <a href="index.php" class="btn btn-outline-primary btn-sm rounded-pill px-4 shadow-sm fw-bold">
                    <i class="fas fa-arrow-left me-2"></i>Back to Pending
                </a>
            </div>
        </div>

        <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
                <h4 class="fw-bold mb-0" style="color: #1a1a2e;"><i class="fas fa-history text-secondary me-2"></i>Past Records</h4>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="historyTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-top-0">Reg No</th>
                            <th class="border-top-0">Student Profile</th>
                            <th class="border-top-0">Contact</th>
                            <th class="border-top-0">Verified By</th>
                            <th class="border-top-0 text-center">Final Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history_students as $student): ?>
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
                                <td class="text-center">
                                    <?php if ($student['coordinator_approval_status'] == 2): ?>
                                        <span class="badge bg-success text-white px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-check-circle me-1"></i>Approved</span>
                                    <?php elseif ($student['coordinator_approval_status'] == 3): ?>
                                        <span class="badge bg-danger text-white px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-times-circle me-1"></i>Rejected</span>
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
            "order": [[0, "desc"]], /* Sort by ID desc roughly mapping to Reg No */
            "pageLength": 25,
            "language": {
                "emptyTable": '<div class="text-center py-5"><i class="fas fa-folder-open fa-3x text-secondary opacity-25 mb-3 d-block"></i><h5 class="text-muted fw-bold">No History</h5><p class="text-muted mb-0">You have not approved or rejected any admissions yet.</p></div>'
            }
        });
    });
</script>
<?php include('includes/footer.php'); ?>
