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
            // 1. Fetch registration, caller, and supervisor info
            $info_query = "SELECT r.assigned_caller, r.submitted_by_supervisor, c.earning_per_admission 
                           FROM registration r 
                           LEFT JOIN caller c ON r.assigned_caller = c.id 
                           WHERE r.id = ?";
            $info_stmt = mysqli_prepare($con, $info_query);
            mysqli_stmt_bind_param($info_stmt, "i", $student_id);
            mysqli_stmt_execute($info_stmt);
            $info_result = mysqli_stmt_get_result($info_stmt);
            $info = mysqli_fetch_assoc($info_result);

            if ($info) {
                // Determine caller payout
                $caller_id = $info['assigned_caller'];
                $caller_amount = ($caller_id > 0) ? ($info['earning_per_admission'] ?? 200.00) : 0;

                // 2. Update registration statuses
                $update_query = "UPDATE registration SET reg_status = 3, coordinator_approval_status = 2 WHERE id = ?";
                $u_stmt = mysqli_prepare($con, $update_query);
                mysqli_stmt_bind_param($u_stmt, "i", $student_id);
                mysqli_stmt_execute($u_stmt);

                // 3. Credit Caller (if assigned)
                if ($caller_id > 0) {
                    $e_stmt = mysqli_prepare($con, "INSERT INTO caller_earnings (caller_id, student_id, amount, date) VALUES (?, ?, ?, NOW())");
                    mysqli_stmt_bind_param($e_stmt, "iid", $caller_id, $student_id, $caller_amount);
                    mysqli_stmt_execute($e_stmt);
                }

                // 4. Credit Supervisor (if linked to submission)
                $supervisor_id = $info['submitted_by_supervisor'];
                if ($supervisor_id > 0) {
                    // Get individual supervisor commission amount
                    $sup_rate_query = mysqli_query($con, "SELECT commission_per_reg FROM supervisor WHERE id = $supervisor_id");
                    $sup_rate_data = mysqli_fetch_assoc($sup_rate_query);
                    $sup_commission = $sup_rate_data['commission_per_reg'] ?? 0;

                    // Fallback to global if individual is not set (optional, based on preference)
                    if ($sup_commission <= 0) {
                        $settings_res = mysqli_query($con, "SELECT supervisor_commission FROM global_setting LIMIT 1");
                        $sett = mysqli_fetch_assoc($settings_res);
                        $sup_commission = $sett['supervisor_commission'] ?? 0;
                    }

                    if ($sup_commission > 0) {
                        // Increment supervisor balance
                        mysqli_query($con, "UPDATE supervisor SET wallet_balance = wallet_balance + $sup_commission WHERE id = $supervisor_id");
                        
                        // Record earning
                        $sup_e_stmt = mysqli_prepare($con, "INSERT INTO supervisor_earnings (supervisor_id, student_id, amount, date, description) VALUES (?, ?, ?, NOW(), 'Registration Approval Commission')");
                        mysqli_stmt_bind_param($sup_e_stmt, "iid", $supervisor_id, $student_id, $sup_commission);
                        mysqli_stmt_execute($sup_e_stmt);
                    }
                }

                mysqli_commit($con);
                header('Location: reg_credentials.php?success=1');
            } else {
                throw new Exception("Registration record not found.");
            }
        } catch (Exception $e) {
            mysqli_rollback($con);
            header('Location: reg_credentials.php?error=' . urlencode($e->getMessage()));
        }
        exit;
    }
}

// Fetch Supervisor Registrations (reg_status = 2) for this coordinator's branches
$supervisor_registrations = [];
if (!empty($coordinator_bids)) {
    $bids_list = implode(',', array_map('intval', $coordinator_bids));
    $reg_query = "SELECT r.*, mc.name as category_name, c.name as caller_name
                  FROM registration r
                  LEFT JOIN member_category mc ON r.mcategory = mc.id
                  LEFT JOIN caller c ON r.assigned_caller = c.id
                  WHERE r.reg_status = 2 AND r.coordinator_approval_status = 1 AND r.bid IN ($bids_list)
                  ORDER BY r.id DESC";
    $r_result = mysqli_query($con, $reg_query);
    while ($row = mysqli_fetch_assoc($r_result)) {
        $supervisor_registrations[] = $row;
    }
}

include('includes/header.php');
?>
<div class="wrapper d-flex">
    <?php include('includes/sidebar.php'); ?>

    <div id="content" class="flex-grow-1">
        
        <!-- Premium Page Header -->
        <div class="glass-card glass-header mb-4 p-4 rounded-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="fas fa-id-card-clip fs-5"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1 h4 text-dark">Registration Approvals</h2>
                        <p class="text-muted mb-0 small">Review and authorize supervisor-submitted login credentials.</p>
                    </div>
                </div>
                <a href="index.php" class="btn bg-white rounded-pill px-4 shadow-sm text-primary fw-bold action-btn border">
                    <i class="fas fa-arrow-left me-2"></i>Dashboard
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" style="background: rgba(16, 185, 129, 0.1); color: #065f46; border-left: 4px solid #10b981 !important;">
                <i class="fas fa-check-circle me-2 fs-5 align-middle text-success"></i>
                <span class="align-middle fw-medium">Credentials approved and final commission credited!</span>
                <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" style="background: rgba(239, 68, 68, 0.1); color: #991b1b; border-left: 4px solid #ef4444 !important;">
                <i class="fas fa-exclamation-circle me-2 fs-5 align-middle text-danger"></i>
                <span class="align-middle fw-medium"><?php echo htmlspecialchars($_GET['error']); ?></span>
                <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="wow-table-wrapper mb-5 text-dark">
            <div class="table-responsive border-0 bg-transparent" style="overflow-x: auto;">
                <table class="table wow-table align-middle mb-0" id="regTable" style="width:100%">
                    <thead>
                        <tr>
                            <th class="ps-4">Reg No</th>
                            <th>Student Profile</th>
                            <th>Credentials</th>
                            <th>Source</th>
                            <th class="text-center pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($supervisor_registrations as $student): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-white text-primary border border-primary border-opacity-25 px-3 py-2 fs-6 shadow-sm rounded-pill">
                                        <?php echo htmlspecialchars($student['regno']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary text-white fw-bold rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                            <?php echo strtoupper(substr($student['name'], 0, 1)); ?>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark" style="font-size: 1.05rem; letter-spacing: -0.3px;"><?php echo htmlspecialchars($student['name']); ?></span>
                                            <span class="text-muted small fw-medium"><i class="fas fa-fingerprint me-1 text-primary opacity-50"></i>ID: <?php echo htmlspecialchars($student['id']); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-2 mb-1 mt-1">
                                        <div class="bg-white bg-opacity-75 rounded-3 px-3 py-2 border border-white shadow-sm d-inline-flex align-items-center" style="width: fit-content; backdrop-filter: blur(10px);">
                                            <span class="text-muted me-2" style="width: 15px;"><i class="fas fa-user text-primary opacity-75"></i></span>
                                            <code class="text-primary fw-bold" style="background:transparent; padding:0; font-size: 0.9rem;"><?php echo htmlspecialchars($student['reg_login_id']); ?></code>
                                        </div>
                                        <div class="bg-white bg-opacity-75 rounded-3 px-3 py-2 border border-white shadow-sm d-inline-flex align-items-center" style="width: fit-content; backdrop-filter: blur(10px);">
                                            <span class="text-muted me-2" style="width: 15px;"><i class="fas fa-lock text-secondary opacity-75"></i></span>
                                            <code class="text-secondary fw-bold" style="background:transparent; padding:0; font-size: 0.9rem;"><?php echo htmlspecialchars($student['reg_password']); ?></code>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-white shadow-sm text-purple border border-white px-3 py-2 rounded-pill fw-bold" style="color: #6d28d9;">
                                        <i class="fas fa-headset me-1 opacity-75"></i><?php echo htmlspecialchars($student['caller_name'] ?? 'Direct'); ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <form method="POST">
                                        <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                        <button type="submit" name="approve_registration" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold border-0 action-btn" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);" onclick="return confirm('Authorize this registration?')">
                                            <i class="fas fa-check-circle me-2"></i>Authorize
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
                "emptyTable": '<div class="text-center py-5 text-muted"><i class="fas fa-id-card-clip fa-3x mb-3 text-primary opacity-25"></i><h5 class="fw-bold text-dark mb-1">No pending credentials</h5><p class="small mb-0">You\'re all caught up! No supervisor submissions are waiting for approval.</p></div>'
            }
        });
    });
</script>
<style>
    .x-small { font-size: 0.75rem; }
    #regTable code { font-family: 'Inter', monospace; }
    .action-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3) !important; }
    
    /* Wow Table Styling */
    .wow-table-wrapper {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.4) 0%, rgba(255, 255, 255, 0.1) 100%) !important;
        backdrop-filter: blur(40px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(40px) saturate(150%) !important;
        border: 1px solid rgba(255, 255, 255, 0.7) !important;
        border-radius: 24px !important;
        box-shadow: 0 15px 35px rgba(31, 38, 135, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.6) !important;
        padding: 1.5rem;
    }

    .wow-table {
        border-collapse: separate !important;
        border-spacing: 0 12px !important;
        margin-top: -12px !important;
    }

    .wow-table thead th {
        background: transparent !important;
        border: none !important;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.75rem;
        padding: 1rem 1.5rem;
    }

    .wow-table tbody tr {
        background: rgba(255, 255, 255, 0.6);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        transition: all 0.3s ease;
        border-radius: 16px;
    }

    .wow-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateY(-2px) scale(1.005);
        box-shadow: 0 10px 25px rgba(31, 38, 135, 0.08);
    }

    .wow-table tbody td {
        border: none !important;
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
    }

    .wow-table tbody td:first-child { border-top-left-radius: 16px; border-bottom-left-radius: 16px; }
    .wow-table tbody td:last-child { border-top-right-radius: 16px; border-bottom-right-radius: 16px; }

    /* DataTables specific fixes for Wow Table */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid rgba(255,255,255,0.8);
        background: rgba(255,255,255,0.5);
        border-radius: 12px;
        padding: 0.5rem 1rem;
        backdrop-filter: blur(10px);
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid rgba(255,255,255,0.8);
        background: rgba(255,255,255,0.5);
        border-radius: 8px;
        padding: 0.3rem 1rem;
    }
    .dataTables_wrapper .dataTables_info {
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 500;
        padding-top: 1rem;
    }
    .page-item.active .page-link {
        background-color: #4f46e5;
        border-color: #4f46e5;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
    }
    .page-item .page-link {
        border: none;
        background: transparent;
        color: #4f46e5;
        font-weight: 600;
        border-radius: 8px;
        margin: 0 2px;
    }
    .page-item:not(.active) .page-link:hover {
        background: rgba(255,255,255,0.8);
    }
</style>
<?php include('includes/footer.php'); ?>
