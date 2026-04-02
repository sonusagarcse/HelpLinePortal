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
$supervisor_bids = $_SESSION['supervisor_bids'] ?? [];

function time_ago($timestamp) {
    if (!$timestamp) return 'N/A';
    $time = is_numeric($timestamp) ? $timestamp : strtotime($timestamp);
    if (!$time) return 'N/A';
    
    $diff = time() - $time;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' mins ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hrs ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    
    return date('d M, h:i A', $time);
}

// Handle credential submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_credentials'])) {
    $student_id = (int)$_POST['student_id'];
    $login_id = mysqli_real_escape_string($con, $_POST['reg_login_id']);
    $password = mysqli_real_escape_string($con, $_POST['reg_password']);
    
    // 1. Fetch the branch ID for this specific registration
    $reg_data_query = mysqli_query($con, "SELECT bid FROM registration WHERE id = $student_id");
    $reg_data_row = mysqli_fetch_assoc($reg_data_query);
    $bid = $reg_data_row ? (int)$reg_data_row['bid'] : 0;

    // 2. Fetch the first available coordinator for this specific branch
    $coord_query = mysqli_query($con, "SELECT coordinator_id FROM coordinator_branches WHERE branch_id = $bid AND status = 1 LIMIT 1");
    $coord_row = mysqli_fetch_assoc($coord_query);
    $assigned_coordinator = $coord_row ? (int)$coord_row['coordinator_id'] : 0;

    if ($student_id > 0 && !empty($login_id) && !empty($password) && $assigned_coordinator > 0) {
        $update_query = "UPDATE registration SET reg_login_id = ?, reg_password = ?, reg_status = 2, coordinator_approval_status = 1, assigned_coordinator = ?, submitted_by_supervisor = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $update_query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssiii", $login_id, $password, $assigned_coordinator, $supervisor_id, $student_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = "Registration credentials saved and auto-routed to Branch Coordinator!";
            } else {
                $_SESSION['error_message'] = "Database Error (Execute): " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['error_message'] = "Database Error (Prepare): " . mysqli_error($con);
        }
    } elseif ($student_id <= 0) {
        $_SESSION['error_message'] = "Invalid Student ID.";
    } elseif (empty($login_id) || empty($password)) {
        $_SESSION['error_message'] = "Login ID and Password are required.";
    } elseif ($assigned_coordinator === 0) {
        $_SESSION['error_message'] = "No Coordinator assigned to this branch (BID: $bid). Please contact Admin.";
    } else {
        $_SESSION['error_message'] = "Something went wrong. Please check if all fields are correct.";
    }
    header('Location: reg_approvals.php');
    exit;
}

// Fetch students ready for registration (reg_status = 1) in supervisor's branches
$pending_registration = [];
if (!empty($supervisor_bids)) {
    $bids_list = implode(',', array_map('intval', $supervisor_bids));
    $query = "SELECT r.*, b.bname, b.bcode, mc.name as category_name, 
                     c.name as caller_name
              FROM registration r
              JOIN branch b ON r.bid = b.id
              LEFT JOIN member_category mc ON r.mcategory = mc.id
              LEFT JOIN caller c ON r.assigned_caller = c.id
              WHERE r.reg_status = 1 AND r.status = 1 AND r.bid IN ($bids_list)
              ORDER BY r.id DESC";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $pending_registration[] = $row;
    }



    // Fetch past approvals (reg_status = 2 (Awaiting) or 3 (Approved))
    $past_approvals = [];
    $past_query = "SELECT r.*, b.bname, b.bcode, mc.name as category_name, 
                         c.name as caller_name, cc.name as coordinator_name
                  FROM registration r
                  JOIN branch b ON r.bid = b.id
                  LEFT JOIN member_category mc ON r.mcategory = mc.id
                  LEFT JOIN caller c ON r.assigned_caller = c.id
                  LEFT JOIN centre_coordinator cc ON r.assigned_coordinator = cc.id
                  WHERE r.reg_status IN (2, 3) AND r.bid IN ($bids_list)
                  ORDER BY r.id DESC LIMIT 20";
    $p_result = mysqli_query($con, $past_query);
    while ($p_row = mysqli_fetch_assoc($p_result)) {
        $past_approvals[] = $p_row;
    }
}

$page_title = "Registration Approval";
include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Registration Approval</h1>
        <p class="page-subtitle">Add login credentials for students marked as 'Ready for Registration'.</p>
    </div>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
        <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-bottom-0 pt-4 px-4">
        <h5 class="mb-0 fw-bold"><i class="fas fa-clipboard-list text-primary me-2"></i>Pending Credentials</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Student Info</th>
                        <th>Branch/Category</th>
                        <th>Caller</th>
                        <th>Sent Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pending_registration)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-check-double fs-1 text-success opacity-25 mb-3"></i>
                                <h6 class="text-muted fw-semibold">No pending registration approvals</h6>
                                <p class="small text-muted mb-0">All students have been processed.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pending_registration as $student): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($student['name']); ?></span>
                                        <span class="small text-muted">Reg: <?php echo htmlspecialchars($student['regno']); ?></span>
                                        <span class="small text-muted">Mob: <?php echo htmlspecialchars($student['mob']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 mb-1" style="width: fit-content;">
                                            <?php echo htmlspecialchars($student['bcode']); ?>
                                        </span>
                                        <span class="small fw-medium"><?php echo htmlspecialchars($student['category_name'] ?? 'General'); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-secondary">
                                        <i class="fas fa-headset me-1 opacity-50"></i><?php echo htmlspecialchars($student['caller_name'] ?? 'Direct'); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-muted fw-medium">
                                        <i class="fas fa-clock me-1 opacity-50"></i><?php echo time_ago($student['reg_ready_at']); ?>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#credModal<?php echo $student['id']; ?>">
                                        <i class="fas fa-key me-1"></i> Add Credentials
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="credModal<?php echo $student['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header border-bottom-0 pt-4 px-4">
                                                    <h5 class="modal-title fw-bold">Set Login Credentials</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                                    <div class="modal-body px-4">
                                                        <div class="bg-primary bg-opacity-5 p-3 rounded-4 mb-4 border border-primary border-opacity-10">
                                                            <div class="small text-muted">Student Name</div>
                                                            <div class="fw-bold fs-5"><?php echo htmlspecialchars($student['name']); ?></div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small">Login ID *</label>
                                                            <input type="text" name="reg_login_id" class="form-control rounded-3" required placeholder="Generate or enter login ID">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small">Password *</label>
                                                            <input type="text" name="reg_password" class="form-control rounded-3" required placeholder="Generate or enter password">
                                                        </div>
                                                        <div class="bg-light p-3 rounded-4 mb-3 border border-light">
                                                            <div class="small text-muted"><i class="fas fa-info-circle me-1"></i>Routing Information</div>
                                                            <div class="fw-medium small mt-1 text-primary">Credentials will be automatically routed to the Branch Coordinator for final approval.</div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 pb-4 px-4">
                                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-toggle="modal">Cancel</button>
                                                        <button type="submit" name="submit_credentials" class="btn btn-primary rounded-pill px-4">
                                                            Save & Send for Approval
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-5 mb-5">
    <div class="card-header bg-transparent border-bottom-0 pt-4 px-4">
        <h5 class="mb-0 fw-bold"><i class="fas fa-check-circle text-success me-2"></i>Recently Processed</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Student Info</th>
                        <th>Branch</th>
                        <th>Processed By</th>
                        <th>Approval Time</th>
                        <th>Coordinator Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($past_approvals)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted small">No recent approvals found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($past_approvals as $student): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($student['name']); ?></span>
                                        <span class="small text-muted">Reg No: <?php echo htmlspecialchars($student['regno']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 mb-1">
                                            <?php echo htmlspecialchars($student['bcode']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <div class="text-dark"><i class="fas fa-headset me-1 text-muted"></i><?php echo htmlspecialchars($student['caller_name'] ?? 'Direct'); ?></div>
                                        <?php if($student['coordinator_approval_status'] == 2): ?>
                                            <div class="text-muted"><i class="fas fa-user-check me-1 text-muted"></i><?php echo htmlspecialchars($student['coordinator_name'] ?? 'Coordinator'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-muted fw-medium">
                                        <i class="fas fa-calendar-check me-1 opacity-50"></i><?php echo time_ago($student['coordinator_approved_at']); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                    if($student['coordinator_approval_status'] == 1) {
                                        echo '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Awaiting Coord.</span>';
                                    } elseif($student['coordinator_approval_status'] == 2) {
                                        echo '<span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill"><i class="fas fa-check-circle me-1"></i>Approved</span>';
                                    } elseif($student['coordinator_approval_status'] == 3) {
                                        echo '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Rejected</span>';
                                    } else {
                                        echo '<span class="badge bg-light text-muted">Processing...</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
