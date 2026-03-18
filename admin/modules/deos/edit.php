<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

if (!isset($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM deo WHERE id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$deo = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$deo) {
    header('Location: list.php');
    exit;
}

$page_title = 'Edit DEO';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $mob = mysqli_real_escape_string($con, $_POST['mob']);
    $bid = isset($_POST['branch']) ? (int)$_POST['branch'] : 0;
    $assigned_branches = $bid > 0 ? [$bid] : [];
    $status = isset($_POST['status']) ? 1 : 0;
    
    mysqli_begin_transaction($con);
    try {
        if (!empty($_POST['pass'])) {
            $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
            $query = "UPDATE deo SET name=?, email=?, mob=?, status=?, pass=? WHERE id=?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $mob, $status, $pass, $id);
        } else {
            $query = "UPDATE deo SET name=?, email=?, mob=?, status=? WHERE id=?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "ssssi", $name, $email, $mob, $status, $id);
        }
        mysqli_stmt_execute($stmt);

        // Sync branches
        $delete_query = "DELETE FROM deo_branches WHERE deo_id = ?";
        $delete_stmt = mysqli_prepare($con, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $id);
        mysqli_stmt_execute($delete_stmt);

        if (!empty($assigned_branches)) {
            $insert_query = "INSERT INTO deo_branches (deo_id, branch_id) VALUES (?, ?)";
            $insert_stmt = mysqli_prepare($con, $insert_query);
            foreach ($assigned_branches as $branch_id) {
                mysqli_stmt_bind_param($insert_stmt, "ii", $id, $branch_id);
                mysqli_stmt_execute($insert_stmt);
            }
        }

        mysqli_commit($con);
        logActivity('edit_deo', 'deo', $id, json_encode($deo), json_encode($_POST));
        header('Location: list.php?success=updated');
        exit;
    } catch (Exception $e) {
        mysqli_rollback($con);
        $error = 'Failed to update DEO: ' . $e->getMessage();
    }
}

// Get currently assigned branches
$assigned_result = mysqli_query($con, "SELECT branch_id FROM deo_branches WHERE deo_id = $id");
$assigned_branches = [];
while ($row = mysqli_fetch_assoc($assigned_result)) {
    $assigned_branches[] = $row['branch_id'];
}

$branches = mysqli_query($con, "SELECT id, bname FROM branch WHERE status = 1 ORDER BY bname");

include('../../includes/header.php');
?>

<div class="wrapper">
    <?php include('../../includes/sidebar.php'); ?>

    <div id="content">
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-link">
                <i class="fas fa-bars"></i>
            </button>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <h1>Edit DEO Account</h1>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="table-card">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Registration Number</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($deo['regno']); ?>" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($deo['name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($deo['email']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Number *</label>
                            <input type="text" name="mob" class="form-control" value="<?php echo htmlspecialchars($deo['mob']); ?>" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Assign Branch *</label>
                            <select name="branch" class="form-select shadow-sm" required>
                                <option value="">Select Branch</option>
                                <?php mysqli_data_seek($branches, 0); while($b = mysqli_fetch_assoc($branches)): ?>
                                    <option value="<?php echo $b['id']; ?>" <?php echo in_array($b['id'], $assigned_branches) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($b['bname']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password (leave blank to keep current)</label>
                            <input type="password" name="pass" class="form-control">
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" id="status" <?php echo $deo['status'] == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="status">Active Account</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Update DEO Account</button>
                            <a href="list.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
