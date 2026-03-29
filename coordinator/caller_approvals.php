<?php
require_once('../connection.php');
require_once('config/auth.php');

$page_title = 'Caller Registration Approvals';

// Handle approval submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_registration'])) {
    $student_id = (int)$_POST['student_id'];
    if ($student_id > 0) {
        mysqli_begin_transaction($con);
        try {
            // 1. Fetch caller and payout info
            $info_query = "SELECT r.assigned_caller, c.earning_per_admission 
                           FROM registration r 
                           LEFT JOIN caller c ON r.assigned_caller = c.id 
                           WHERE r.id = ?";
            $info_stmt = mysqli_prepare($con, $info_query);
            mysqli_stmt_bind_param($info_stmt, "i", $student_id);
            mysqli_stmt_execute($info_stmt);
            $info_result = mysqli_stmt_get_result($info_stmt);
            $info = mysqli_fetch_assoc($info_result);

            if ($info && $info['assigned_caller'] > 0) {
                $caller_id = $info['assigned_caller'];
                $amount = $info['earning_per_admission'] ?? 200.00; // Fallback to 200

                // 2. Update registration statuses
                $update_query = "UPDATE registration SET reg_status = 3, coordinator_approval_status = 2 WHERE id = ?";
                $u_stmt = mysqli_prepare($con, $update_query);
                mysqli_stmt_bind_param($u_stmt, "i", $student_id);
                mysqli_stmt_execute($u_stmt);

                // 3. Add to caller_earnings tracking
                $e_stmt = mysqli_prepare($con, "INSERT INTO caller_earnings (caller_id, student_id, amount, date) VALUES (?, ?, ?, NOW())");
                mysqli_stmt_bind_param($e_stmt, "iid", $caller_id, $student_id, $amount);
                mysqli_stmt_execute($e_stmt);

                mysqli_commit($con);
                $_SESSION['success'] = "Registration approved! Earning credited to Caller.";
            } else {
                throw new Exception("Caller information missing.");
            }
        } catch (Exception $e) {
            mysqli_rollback($con);
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
        header('Location: caller_approvals.php');
        exit;
    }
}

// Fetch students with supervisor-filled credentials (reg_status = 2) in coordinator's branch
$pending_registration = [];
$query = "SELECT r.*, mc.name as category_name, c.name as caller_name
          FROM registration r
          LEFT JOIN member_category mc ON r.mcategory = mc.id
          LEFT JOIN caller c ON r.assigned_caller = c.id
          WHERE r.reg_status = 2 AND r.bid = ?
          ORDER BY r.id DESC";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $coordinator_bid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $pending_registration[] = $row;
}

include('includes/header.php');
?>
<div class="wrapper d-flex">
    <?php include('includes/sidebar.php'); ?>

    <div id="content" class="flex-grow-1 p-3 p-md-4">
        <div class="glass-card mb-4 border-0" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white;">
            <h2 class="fw-bold mb-1">Registration Approvals</h2>
            <p class="opacity-75 mb-0">Review and finalize registration credentials provided by supervisors.</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="registrationTable" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Reg No</th>
                            <th>Student</th>
                            <th>Credentials</th>
                            <th>Context</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_registration as $student): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25 px-2 py-1">
                                        <?php echo htmlspecialchars($student['regno']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong class="text-dark"><?php echo htmlspecialchars($student['name']); ?></strong>
                                        <span class="text-muted small">ID: <?php echo htmlspecialchars($student['id']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="p-2 rounded bg-light border small">
                                        <div class="mb-1"><strong>Login ID:</strong> <code class="text-primary"><?php echo htmlspecialchars($student['reg_login_id']); ?></code></div>
                                        <div><strong>Password:</strong> <code class="text-secondary"><?php echo htmlspecialchars($student['reg_password']); ?></code></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border px-2 py-1 mb-1" style="width: fit-content;">
                                            <?php echo htmlspecialchars($student['category_name'] ?? 'General'); ?>
                                        </span>
                                        <span class="text-muted"><i class="fas fa-headset me-1"></i><?php echo htmlspecialchars($student['caller_name'] ?? 'Unknown'); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <form method="POST">
                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                            <button type="submit" name="approve_registration" class="btn btn-success btn-sm rounded-pill px-4 shadow-sm" onclick="return confirm('Approve these credentials and finalize registration?')">
                                                <i class="fas fa-check me-1"></i> Approve
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>Reg No</th>
                            <th>Student</th>
                            <th>Credentials</th>
                            <th>Context</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </tfoot>
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
        if ($.fn.DataTable.isDataTable('#registrationTable')) {
            $('#registrationTable').DataTable().destroy();
        }
        $('#registrationTable').DataTable({
            "pageLength": 10,
            "order": [[0, "desc"]],
            "responsive": true,
            "autoWidth": false,
            "columnDefs": [
                { "orderable": false, "targets": 4 }
            ],
            "language": {
                "emptyTable": '<div class="text-center py-5 text-muted"><i class="fas fa-check-double fa-3x mb-3 opacity-25"></i><p class="fw-bold mb-0">No registration approvals pending</p></div>'
            }
        });
    });
</script>
<?php include('includes/footer.php'); ?>
