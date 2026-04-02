<?php
require_once('../../config/config.php');
require_once('../../config/auth.php');

$page_title = 'Approval Manager';

// Fetch all completed interactions waiting for or already processed by coordinators
$approvals = [];
$query = "SELECT m.id as mquery_id, m.callerid as actual_callerid, 
                 r.id as student_id, r.regno, r.name as student_name, r.mob, 
                 b.bname, c.name as caller_name, r.coordinator_approval_status, 
                 r.caller_remark, m.date as submitted_date, r.reg_status
          FROM mquery m
          JOIN registration r ON m.studentid = r.id
          LEFT JOIN branch b ON r.bid = b.id
          LEFT JOIN caller c ON m.callerid = c.id
          WHERE m.status = 0 
          ORDER BY m.id DESC";
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $approvals[] = $row;
}
?>

<?php include('../../includes/header.php'); ?>

<div class="wrapper">
    <?php include('../../includes/sidebar.php'); ?>

    <div id="content">
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-link">
                <i class="fas fa-bars"></i>
            </button>
            <div class="user-menu">
                <div class="user-info">
                    <div class="name"><?php echo $admin_name; ?></div>
                    <div class="role">Administrator</div>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <h1>Approval & Payout Manager</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Approvals</li>
                    </ol>
                </nav>
            </div>

            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Action `<?php echo htmlspecialchars($_GET['success']); ?>` executed successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Override & Review Caller Confirmations</h5>
                </div>
                
                <div class="table-responsive">
                    <table id="approvalsTable" class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Reg No</th>
                                <th>Student Name</th>
                                <th>Branch</th>
                                <th>Submitting Caller</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Admin Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($approvals as $app): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($app['regno'] ?? 'N/A'); ?></td>
                                    <td><strong><?php echo htmlspecialchars($app['student_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($app['bname'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($app['caller_name'] ?? 'Unknown'); ?></td>
                                    <td>
                                        <?php 
                                        if($app['coordinator_approval_status'] == 1) {
                                            echo '<span class="badge bg-warning text-dark">Pending</span>';
                                        } elseif($app['coordinator_approval_status'] == 2) {
                                            echo '<span class="badge bg-success">Approved</span>';
                                        } elseif($app['coordinator_approval_status'] == 3) {
                                            echo '<span class="badge bg-danger">Rejected</span>';
                                        } else {
                                            echo '<span class="badge bg-secondary">System Default</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($app['submitted_date'])); ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Manage Override
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                <?php if($app['coordinator_approval_status'] != 2): ?>
                                                    <li>
                                                        <form action="action.php" method="POST" class="d-inline">
                                                            <input type="hidden" name="action" value="force_approve">
                                                            <input type="hidden" name="student_id" value="<?php echo $app['student_id']; ?>">
                                                            <input type="hidden" name="caller_id" value="<?php echo $app['actual_callerid']; ?>">
                                                            <button class="dropdown-item text-success fw-bold" type="submit" onclick="return confirm('Force approval? This will credit the caller immediately.');">
                                                                <i class="fas fa-check-circle me-2"></i>Force Approve
                                                            </button>
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                                
                                                <?php if($app['coordinator_approval_status'] != 3): ?>
                                                    <li>
                                                        <form action="action.php" method="POST" class="d-inline">
                                                            <input type="hidden" name="action" value="force_reject">
                                                            <input type="hidden" name="student_id" value="<?php echo $app['student_id']; ?>">
                                                            <input type="hidden" name="caller_id" value="<?php echo $app['actual_callerid']; ?>">
                                                            <button class="dropdown-item text-danger fw-bold" type="submit" onclick="return confirm('Force rejection? This will revoke caller earnings if previously approved.');">
                                                                <i class="fas fa-times-circle me-2"></i>Force Reject
                                                            </button>
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                                
                                                <li><hr class="dropdown-divider"></li>
                                                
                                                <li>
                                                    <form action="action.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="action" value="reset_caller">
                                                        <input type="hidden" name="student_id" value="<?php echo $app['student_id']; ?>">
                                                        <input type="hidden" name="mquery_id" value="<?php echo $app['mquery_id']; ?>">
                                                        <input type="hidden" name="caller_id" value="<?php echo $app['actual_callerid']; ?>">
                                                        <button class="dropdown-item text-danger" type="submit" onclick="return confirm('Reset submission? This deletes the completion entry, revokes earnings, enables the caller to call again, and resets coordinator pipeline.');">
                                                            <i class="fas fa-undo me-2"></i>Unlock Caller Entry
                                                        </button>
                                                    </form>
                                                </li>
                                                
                                                <?php if($app['reg_status'] >= 2): ?>
                                                <li>
                                                    <form action="action.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="action" value="reset_supervisor">
                                                        <input type="hidden" name="student_id" value="<?php echo $app['student_id']; ?>">
                                                        <button class="dropdown-item text-warning fw-bold" type="submit" onclick="return confirm('Unlock Supervisor? This deletes generated credentials and sends the student back to the Supervisor\'s pending list.');">
                                                            <i class="fas fa-unlock me-2"></i>Unlock Supervisor Entry
                                                        </button>
                                                    </form>
                                                </li>
                                                <?php endif; ?>
                                            </ul>
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
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#approvalsTable').DataTable({
        pageLength: 25,
        order: [[5, 'desc']]
    });
});
</script>

<?php include('../../includes/footer.php'); ?>
