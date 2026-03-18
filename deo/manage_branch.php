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
        
        // Reset category if it doesn't belong to new branch
        unset($_SESSION['active_cid']);
        unset($_SESSION['active_cname']);
        
        header('Location: manage_branch.php?success=activated');
        exit;
    }
}

// Handle category activation
if (isset($_GET['activate_cid'])) {
    $activate_cid = (int)$_GET['activate_cid'];
    
    $query = "SELECT id, name FROM member_category WHERE id = ? AND bid = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ii", $activate_cid, $active_bid);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt)->fetch_assoc();
    
    if ($res) {
        $_SESSION['active_cid'] = $res['id'];
        $_SESSION['active_cname'] = $res['name'];
        header('Location: manage_branch.php?success=cat_activated');
        exit;
    }
}

// Handle category creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $cat_name = mysqli_real_escape_string($con, $_POST['cat_name']);
    if (!empty($cat_name)) {
        $today = date('d-m-Y');
        $query = "INSERT INTO member_category (name, bid, date, status) VALUES (?, ?, ?, 1)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "sis", $cat_name, $active_bid, $today);
        if (mysqli_stmt_execute($stmt)) {
            $new_id = mysqli_insert_id($con);
            $_SESSION['active_cid'] = $new_id;
            $_SESSION['active_cname'] = $cat_name;
            header('Location: manage_branch.php?success=cat_added');
            exit;
        }
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

// Get categories for active branch
$categories = [];
if ($active_bid > 0) {
    $query = "SELECT * FROM member_category WHERE bid = ? AND status = 1 ORDER BY name";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $active_bid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
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
                <i class="fas fa-check-circle me-2"></i> 
                <?php 
                    if($_GET['success'] == 'activated') echo "Active branch updated to <strong>".htmlspecialchars($active_bname)."</strong>";
                    elseif($_GET['success'] == 'cat_activated') echo "Active category updated to <strong>".htmlspecialchars($active_cname)."</strong>";
                    elseif($_GET['success'] == 'cat_added') echo "New category created and activated: <strong>".htmlspecialchars($active_cname)."</strong>";
                ?>
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

        <?php if ($active_bid > 0): ?>
            <div class="mt-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-0">Select Sticky Category</h2>
                        <p class="text-muted mb-0">Choose the category for current data entry session in <strong><?php echo htmlspecialchars($active_bname); ?></strong></p>
                    </div>
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus me-2"></i>Create Category
                    </button>
                </div>

                <div class="row g-4">
                    <?php if (empty($categories)): ?>
                        <div class="col-12 text-center py-4">
                            <p class="text-muted">No categories found for this branch. Please create one.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($categories as $c): ?>
                            <div class="col-md-4 col-lg-3">
                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 <?php echo $active_cid == $c['id'] ? 'border-success border-2' : ''; ?>"
                                     style="<?php echo $active_cid == $c['id'] ? 'border-style: solid !important;' : ''; ?>">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                                                <i class="fas fa-tags"></i>
                                            </div>
                                            <?php if ($active_cid == $c['id']): ?>
                                                <span class="badge bg-success rounded-pill">ACTIVE</span>
                                            <?php endif; ?>
                                        </div>
                                        <h6 class="fw-bold mb-3"><?php echo htmlspecialchars($c['name']); ?></h6>
                                        <a href="manage_branch.php?activate_cid=<?php echo $c['id']; ?>" 
                                           class="btn <?php echo $active_cid == $c['id'] ? 'btn-secondary' : 'btn-outline-success'; ?> btn-sm w-100 rounded-pill fw-bold"
                                           <?php echo $active_cid == $c['id'] ? 'disabled' : ''; ?>>
                                            <?php echo $active_cid == $c['id'] ? 'Selected' : 'Select'; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Create New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">This category will be available for <strong><?php echo htmlspecialchars($active_bname); ?></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name</label>
                        <input type="text" name="cat_name" class="form-control rounded-3" placeholder="e.g. Computer Course" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_category" class="btn btn-primary rounded-pill px-4">Create & Activate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include('includes/footer.php'); ?>
