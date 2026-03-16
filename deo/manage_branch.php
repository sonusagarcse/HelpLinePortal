<?php
require_once('../connection.php');
require_once('config/auth.php');

$page_title = 'Select Active Branch';

// Handle branch activation
if (isset($_GET['activate_id'])) {
    $activate_id = (int)$_GET['activate_id'];
    
    // Security check: Verify this branch is assigned to this DEO
    $query = "SELECT b.id, b.bname FROM deo_branches db 
              JOIN branch b ON db.branch_id = b.id 
              WHERE db.deo_id = ? AND db.branch_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ii", $deo_id, $activate_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt)->fetch_assoc();
    
    if ($res) {
        $_SESSION['active_bid'] = $res['id'];
        $_SESSION['active_bname'] = $res['bname'];
        header('Location: manage_branch.php?success=activated');
        exit;
    }
}

// Get all assigned branches
$assigned_branches = [];
$query = "SELECT b.* FROM deo_branches db 
          JOIN branch b ON db.branch_id = b.id 
          WHERE db.deo_id = ? 
          ORDER BY b.bname";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $deo_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $assigned_branches[] = $row;
}

include('includes/header.php');
?>

<div class="wrapper d-flex">
    <?php include('includes/sidebar.php'); ?>

    <div id="content" class="flex-grow-1 p-3 p-md-4">
        <div class="page-header mb-4">
            <h2 class="fw-bold mb-0">Select Active Branch</h2>
            <p class="text-muted">Choose the branch you are currently entering data for.</p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i> Active branch updated to <strong><?php echo htmlspecialchars($active_bname); ?></strong>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <?php if (empty($assigned_branches)): ?>
                <div class="col-12 text-center py-5">
                    <div class="bg-light p-5 rounded-4 border">
                        <i class="fas fa-building fa-4x text-muted mb-3"></i>
                        <h5>No Branches Assigned</h5>
                        <p class="text-muted">Please contact admin to assign branches to your account.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($assigned_branches as $b): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 <?php echo $active_bid == $b['id'] ? 'border-primary border-2' : ''; ?>" 
                             style="<?php echo $active_bid == $b['id'] ? 'border-style: solid !important;' : ''; ?>">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                                        <i class="fas fa-building fa-2x"></i>
                                    </div>
                                    <?php if ($active_bid == $b['id']): ?>
                                        <span class="badge bg-primary rounded-pill px-3">ACTIVE</span>
                                    <?php endif; ?>
                                </div>
                                <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($b['bname']); ?></h4>
                                <p class="text-muted small mb-4">
                                    <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($b['baddress']); ?>
                                </p>
                                
                                <div class="mt-auto">
                                    <?php if ($active_bid == $b['id']): ?>
                                        <button class="btn btn-secondary w-100 rounded-pill fw-bold" disabled>Already Active</button>
                                    <?php else: ?>
                                        <a href="manage_branch.php?activate_id=<?php echo $b['id']; ?>" class="btn btn-primary w-100 rounded-pill fw-bold shadow-sm">
                                            Activate This Branch
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
