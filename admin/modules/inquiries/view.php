<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'View Inquiry';

if (!isset($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT m.*, b.bname as branch_name, r.name as student_name 
          FROM mquery m 
          LEFT JOIN branch b ON m.bid = b.id 
          LEFT JOIN registration r ON m.regno = r.regno
          WHERE m.id = $id";
$result = mysqli_query($con, $query);
$q = mysqli_fetch_assoc($result);

if (!$q) {
    header('Location: list.php');
    exit;
}

// Handle status update
if (isset($_POST['update_status'])) {
    $new_status = (int)$_POST['status'];
    $remarks = mysqli_real_escape_string($con, $_POST['remarks']);
    if (mysqli_query($con, "UPDATE mquery SET status = $new_status, remarks = '$remarks' WHERE id = $id")) {
        header("Location: view.php?id=$id&success=1");
        exit;
    }
}

include('../../includes/header.php');
?>

<div class="wrapper">
    <?php include('../../includes/sidebar.php'); ?>

    <div id="content">
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-link"><i class="fas fa-bars"></i></button>
            <div class="user-menu">
                <div class="user-info">
                    <div class="name"><?php echo $admin_name; ?></div>
                    <div class="role">Admin</div>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <div class="d-flex justify-content-between">
                    <h1>Inquiry Details #<?php echo $id; ?></h1>
                    <a href="list.php" class="btn btn-secondary">Back to List</a>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Status updated successfully!</div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="table-card mb-4">
                        <h5 class="border-bottom pb-2">Information</h5>
                        <table class="table">
                            <tr><th>Student:</th><td><?php echo htmlspecialchars($q['student_name'] ?? 'Guest'); ?> (<?php echo htmlspecialchars($q['regno'] ?? 'N/A'); ?>)</td></tr>
                            <tr><th>Branch:</th><td><?php echo htmlspecialchars($q['branch_name'] ?? 'General'); ?></td></tr>
                            <tr><th>Date:</th><td><?php echo htmlspecialchars($q['pdate']); ?></td></tr>
                            <tr><th>Follow-up Date:</th><td><?php echo htmlspecialchars($q['nextdate']); ?></td></tr>
                            <tr><th>Description:</th><td><?php echo nl2br(htmlspecialchars($q['des'])); ?></td></tr>
                        </table>
                    </div>

                    <div class="table-card">
                        <h5 class="border-bottom pb-2">Admin Remarks & Action</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Update Status</label>
                                <select name="status" class="form-select">
                                    <option value="0" <?php echo $q['status'] == 0 ? 'selected' : ''; ?>>Pending</option>
                                    <option value="1" <?php echo $q['status'] == 1 ? 'selected' : ''; ?>>Resolved</option>
                                    <option value="2" <?php echo $q['status'] == 2 ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" class="form-control" rows="3"><?php echo htmlspecialchars($q['remarks']); ?></textarea>
                            </div>
                            <button type="submit" name="update_status" class="btn btn-primary">Update Inquiry</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-4">
                    <?php if ($q['img']): ?>
                        <div class="table-card">
                            <h5 class="border-bottom pb-2">Attachment</h5>
                            <img src="../../../<?php echo $q['img']; ?>" class="img-fluid rounded" alt="Inquiry Attachment">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
