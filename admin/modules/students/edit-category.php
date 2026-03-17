<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Edit Member Category';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $des = mysqli_real_escape_string($con, $_POST['des']);
    $bid = (int)$_POST['bid'];
    $status = isset($_POST['status']) ? 1 : 0;

    $query = "UPDATE member_category SET name = ?, bid = ?, des = ?, status = ? WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "sisii", $name, $bid, $des, $status, $id);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: categories.php?success=updated');
        exit;
    } else {
        $error = 'Failed to update category: ' . mysqli_error($con);
    }
}

// Fetch current data
$result = mysqli_query($con, "SELECT * FROM member_category WHERE id = $id");
$category = mysqli_fetch_assoc($result);

if (!$category) {
    header('Location: categories.php');
    exit;
}

// Get branches for dropdown
$branches = mysqli_query($con, "SELECT id, bname FROM branch WHERE status = 1 ORDER BY bname ASC");

include('../../includes/header.php');
?>

<div class="wrapper">
    <?php include('../../includes/sidebar.php'); ?>

    <div id="content">
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-link">
                <i class="fas fa-bars"></i>
            </button>
            <div class="user-info">
                <div class="name"><?php echo htmlspecialchars($admin_name ?? ''); ?></div>
                <div class="role"><?php echo ($admin_type ?? 0) == 1 ? 'Super Admin' : 'Admin'; ?></div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <h1>Edit Category</h1>
            </div>

            <div class="table-card">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Category Name *</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch Association *</label>
                            <select name="bid" class="form-select" required>
                                <option value="0" <?php echo ($category['bid'] == 0) ? 'selected' : ''; ?>>Global (All Branches)</option>
                                <?php while($b = mysqli_fetch_assoc($branches)): ?>
                                    <option value="<?php echo $b['id']; ?>" <?php echo ($category['bid'] == $b['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($b['bname']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="des" class="form-control" rows="3"><?php echo htmlspecialchars($category['des']); ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" id="status" <?php echo ($category['status'] == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Update Category</button>
                            <a href="categories.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
