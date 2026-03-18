<?php
require_once('../connection.php');
require_once('config/auth.php');

$page_title = 'Select Active Branch';

// Branch is now automatically managed in config/auth.php based on Admin assignment.

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

// assigned_branches list removed as per single-branch restriction

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
            <h2 class="fw-bold mb-0">Select Category</h2>
            <p class="text-muted">Choose the course category for current data entry session in <strong><?php echo htmlspecialchars($active_bname); ?></strong></p>
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

        <!-- Branch list removed. Branch is fixed based on Admin assignment. -->

        <?php if ($active_bid > 0): ?>
            <div class="mt-2">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Available Categories</h5>
                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus me-2"></i>Create Category
                    </button>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <form action="manage_branch.php" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Choose Category</label>
                            <select name="activate_cid" class="form-select shadow-sm rounded-3">
                                <?php if (empty($categories)): ?>
                                    <option value="">No categories found</option>
                                <?php else: ?>
                                    <?php foreach ($categories as $c): ?>
                                        <option value="<?php echo $c['id']; ?>" <?php echo $active_cid == $c['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($c['name']); ?>
                                        <?php echo $active_cid == $c['id'] ? ' (Currently Active)' : ''; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold shadow-sm">
                                <i class="fas fa-check-circle me-2"></i>Assign Category
                            </button>
                        </div>
                    </form>
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
