<?php
require_once('../connection.php');
require_once('config/auth.php');

$page_title = 'Registration Credential Approvals';

// Handle Registration Credential Approval
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
                $amount = $info['earning_per_admission'] ?? 200.00;

                // 2. Update registration statuses
                $update_query = "UPDATE registration SET reg_status = 3, coordinator_approval_status = 2 WHERE id = ?";
                $u_stmt = mysqli_prepare($con, $update_query);
                mysqli_stmt_bind_param($u_stmt, "i", $student_id);
                mysqli_stmt_execute($u_stmt);

                // 3. Add to caller_earnings tracking table
                $e_stmt = mysqli_prepare($con, "INSERT INTO caller_earnings (caller_id, student_id, amount, date) VALUES (?, ?, ?, NOW())");
                mysqli_stmt_bind_param($e_stmt, "iid", $caller_id, $student_id, $amount);
                mysqli_stmt_execute($e_stmt);

                mysqli_commit($con);
                header('Location: reg_credentials.php?success=1');
            } else {
                throw new Exception("Caller information missing.");
            }
        } catch (Exception $e) {
            mysqli_rollback($con);
            header('Location: reg_credentials.php?error=' . urlencode($e->getMessage()));
        }
        exit;
    }
}

// Fetch Supervisor Registrations (reg_status = 2)
$supervisor_registrations = [];
$reg_query = "SELECT r.*, mc.name as category_name, c.name as caller_name
              FROM registration r
              LEFT JOIN member_category mc ON r.mcategory = mc.id
              LEFT JOIN caller c ON r.assigned_caller = c.id
              WHERE r.reg_status = 2 AND r.coordinator_approval_status = 0 AND r.bid = ?
              ORDER BY r.id DESC";
$r_stmt = mysqli_prepare($con, $reg_query);
mysqli_stmt_bind_param($r_stmt, "i", $coordinator_bid);
mysqli_stmt_execute($r_stmt);
$r_result = mysqli_stmt_get_result($r_stmt);
while ($row = mysqli_fetch_assoc($r_result)) {
    $supervisor_registrations[] = $row;
}

include('includes/header.php');
?>
<div class="wrapper d-flex">
    <?php include('includes/sidebar.php'); ?>

    <div id="content" class="flex-grow-1">
        
        <!-- Minimal Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1 h3">Registration Approvals</h2>
                <p class="text-muted small mb-0"><i class="fas fa-id-card-clip me-1 opacity-50"></i>Supervisor-submitted credentials</p>
            </div>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm border-light-subtle bg-white">
                <i class="fas fa-arrow-left me-2 x-small"></i>Dashboard
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="badge-minimal badge-green w-100 p-3 mb-4 shadow-sm">
                <i class="fas fa-check-double me-2"></i>Credentials approved and final commission added!
            </div>
        <?php endif; ?>

        <div class="minimal-card p-0 shadow-sm border-light-subtle overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="regTable" style="width:100%">
                    <thead>
                        <tr>
                            <th class="ps-4">Reg No</th>
                            <th>Student Profile</th>
                            <th>Credentials Info</th>
                            <th>Lead Source</th>
                            <th class="text-center pe-4">Final Approval</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($supervisor_registrations as $student): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark small"><?php echo htmlspecialchars($student['regno']); ?></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($student['name']); ?></span>
                                        <span class="text-muted x-small">ID: <?php echo htmlspecialchars($student['id']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="p-2 border rounded-3 bg-light shadow-sm x-small" style="max-width: 150px;">
                                        <div class="mb-1"><strong>L:</strong> <code class="text-primary"><?php echo htmlspecialchars($student['reg_login_id']); ?></code></div>
                                        <div><strong>P:</strong> <code class="text-secondary"><?php echo htmlspecialchars($student['reg_password']); ?></code></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-minimal badge-purple">
                                        <i class="fas fa-headset me-1 x-small opacity-50"></i><?php echo htmlspecialchars($student['caller_name'] ?? 'Direct'); ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <form method="POST">
                                        <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                        <button type="submit" name="approve_registration" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-bold border-0" style="background:#4a6cf7" onclick="return confirm('Authorize this registration?')">
                                            Authorize
                                        </button>
                                    </form>
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
        $('#regTable').DataTable({
            "order": [[0, "desc"]],
            "language": {
                "emptyTable": '<div class="text-center py-5 text-muted small"><i class="fas fa-shield d-block fa-2x opacity-25 mb-2"></i>No pending credentials found</div>'
            }
        });
    });
</script>
<style>
    .x-small { font-size: 0.75rem; }
    #regTable code { background: rgba(74, 108, 247, 0.05); padding: 2px 4px; border-radius: 4px; border: 1px solid rgba(0,0,0,0.05);}
</style>
<?php include('includes/footer.php'); ?>
