<?php
require_once('../connection.php');
require_once('config/auth.php');

$page_title = 'Direct Admission Confirmations';

// Fetch Direct Admissions (coordinator_approval_status = 1)
$direct_admissions = [];
if (!empty($coordinator_bids)) {
    $bids_list = implode(',', array_map('intval', $coordinator_bids));
    $direct_query = "SELECT r.*, 
                     (SELECT cn.name FROM mquery m JOIN caller cn ON m.callerid = cn.id WHERE m.studentid = r.id AND m.status = 0 ORDER BY m.id DESC LIMIT 1) as caller_name 
                     FROM registration r 
                     WHERE r.coordinator_approval_status = 1 AND r.reg_status = 0 AND r.bid IN ($bids_list) 
                     ORDER BY r.id DESC";
    $d_result = mysqli_query($con, $direct_query);
    while ($row = mysqli_fetch_assoc($d_result)) {
        $direct_admissions[] = $row;
    }
}
$direct_count = count($direct_admissions);

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
                        <i class="fas fa-headset fs-5"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1 h4 text-dark">Direct Admissions</h2>
                        <p class="text-muted mb-0 small">Review and finalize caller confirmed students.</p>
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
                <span class="align-middle fw-medium">Action processed successfully!</span>
                <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="wow-table-wrapper mb-5 text-dark">
            <div class="table-responsive border-0 bg-transparent" style="overflow-x: auto;">
                <table class="table wow-table align-middle mb-0" id="directTable" style="width:100%">
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
                                <td class="ps-4">
                                    <span class="badge bg-white text-primary border border-primary border-opacity-25 px-3 py-2 fs-6 shadow-sm rounded-pill">
                                        <?php echo htmlspecialchars($student['regno']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary text-white fw-bold rounded-4 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                            <?php echo strtoupper(substr($student['name'], 0, 1)); ?>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark" style="font-size: 1.05rem; letter-spacing: -0.3px;"><?php echo htmlspecialchars($student['name']); ?></span>
                                            <span class="text-muted small fw-medium"><i class="fas fa-user-friends me-1 text-primary opacity-50"></i><?php echo htmlspecialchars($student['father']); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-white bg-opacity-75 rounded-3 px-3 py-2 border border-white shadow-sm d-inline-flex align-items-center" style="backdrop-filter: blur(10px);">
                                            <i class="fas fa-phone-alt text-success me-2 opacity-75"></i>
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($student['mob']); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-white shadow-sm text-purple border border-white px-3 py-2 rounded-pill fw-bold" style="color: #6d28d9;">
                                        <i class="fas fa-user-check me-1 opacity-75"></i><?php echo htmlspecialchars($student['caller_name'] ?? 'Direct'); ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <form action="action.php" method="POST" class="d-inline">
                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="redirect" value="direct_approvals.php">
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold border-0 action-btn" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);" onclick="return confirm('Finalize this admission?');">
                                                <i class="fas fa-check"></i> Confirm
                                            </button>
                                        </form>
                                        <form action="action.php" method="POST" class="d-inline">
                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="redirect" value="direct_approvals.php">
                                            <button type="submit" class="btn bg-white btn-sm rounded-pill px-3 fw-bold border overflow-hidden shadow-sm text-danger action-btn" onclick="return confirm('Reject this student?');">
                                                <i class="fas fa-times"></i> Reject
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
                "emptyTable": '<div class="text-center py-5 text-muted"><i class="fas fa-headset fa-3x mb-3 text-primary opacity-25"></i><h5 class="fw-bold text-dark mb-1">No pending confirmations</h5><p class="small mb-0">You\'re all caught up! No caller-flagged direct admissions are waiting.</p></div>'
            }
        });
    });
</script>
<style>
    .x-small { font-size: 0.75rem; }
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
