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
    $bid = mysqli_real_escape_string($con, $_POST['bid']);
    $status = isset($_POST['status']) ? 1 : 0;

    if (!empty($_POST['pass'])) {
        $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
        $query = "UPDATE deo SET name=?, email=?, mob=?, bid=?, status=?, pass=? WHERE id=?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ssssisi", $name, $email, $mob, $bid, $status, $pass, $id);
    } else {
        $query = "UPDATE deo SET name=?, email=?, mob=?, bid=?, status=? WHERE id=?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ssssii", $name, $email, $mob, $bid, $status, $id);
    }

    if (mysqli_stmt_execute($stmt)) {
        logActivity('edit_deo', 'deo', $id, json_encode($deo), json_encode($_POST));
        header('Location: list.php?success=updated');
        exit;
    } else {
        $error = 'Failed to update DEO: ' . mysqli_error($con);
    }
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Branch Assignment</label>
                            <select name="bid" class="form-select">
                                <option value="0">All Branches</option>
                                <?php while($b = mysqli_fetch_assoc($branches)): ?>
                                    <option value="<?php echo $b['id']; ?>" <?php echo $deo['bid'] == $b['id'] ? 'selected' : ''; ?>>
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
