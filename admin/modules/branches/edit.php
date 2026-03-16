<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Edit Branch';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Get branch data
$query = "SELECT * FROM branch WHERE id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$branch = mysqli_fetch_assoc($result);

if (!$branch) {
    header('Location: list.php?error=not_found');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bcode = mysqli_real_escape_string($con, $_POST['bcode']);
    $bname = mysqli_real_escape_string($con, $_POST['bname']);
    $regno = mysqli_real_escape_string($con, $_POST['regno']);
    $rname = mysqli_real_escape_string($con, $_POST['rname']);
    $rnumber = mysqli_real_escape_string($con, $_POST['rnumber']);
    $bcontact = mysqli_real_escape_string($con, $_POST['bcontact']);
    $bemail = mysqli_real_escape_string($con, $_POST['bemail']);
    $baddress = mysqli_real_escape_string($con, $_POST['baddress']);
    $state = mysqli_real_escape_string($con, $_POST['state']);
    $dis = mysqli_real_escape_string($con, $_POST['dis']);
    $pincode = mysqli_real_escape_string($con, $_POST['pincode']);
    $mid = mysqli_real_escape_string($con, $_POST['mid']);
    $status = isset($_POST['status']) ? 1 : 0;

    $update_query = "UPDATE branch SET bcode=?, bname=?, regno=?, rname=?, rnumber=?, bcontact=?, bemail=?, baddress=?, state=?, dis=?, pincode=?, mid=?, status=? WHERE id=?";
    $update_stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($update_stmt, "sssssssssssiis", $bcode, $bname, $regno, $rname, $rnumber, $bcontact, $bemail, $baddress, $state, $dis, $pincode, $mid, $status, $id);

    if (mysqli_stmt_execute($update_stmt)) {
        logActivity('update_branch', 'branch', $id, json_encode($branch), json_encode($_POST));
        header('Location: list.php?success=updated');
        exit;
    } else {
        $error = 'Failed to update branch';
    }
}

// Get managers for dropdown
$managers = [];
$result = mysqli_query($con, "SELECT id, name FROM manager WHERE status = 1");
while ($row = mysqli_fetch_assoc($result)) {
    $managers[] = $row;
}

include('../../includes/header.php');
?>

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
                    <div class="role"><?php echo $admin_type == 1 ? 'Super Admin' : ($admin_type == 2 ? 'Manager' : ($admin_type == 3 ? 'Healthcare' : ($admin_type == 4 ? 'Supervisor' : ($admin_type == 5 ? 'Branch' : 'Admin')))); ?></div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../../logout.php"><i
                                    class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <h1>Edit Branch</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="list.php">Branches</a></li>
                        <li class="breadcrumb-item active">Edit Branch</li>
                    </ol>
                </nav>
            </div>

            <div class="table-card">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Branch Code *</label>
                            <input type="text" name="bcode" class="form-control"
                                value="<?php echo htmlspecialchars($branch['bcode']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch Name *</label>
                            <input type="text" name="bname" class="form-control"
                                value="<?php echo htmlspecialchars($branch['bname']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Registration Number *</label>
                            <input type="text" name="regno" class="form-control"
                                value="<?php echo htmlspecialchars($branch['regno']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Registered Name</label>
                            <input type="text" name="rname" class="form-control"
                                value="<?php echo htmlspecialchars($branch['rname']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Registration Contact</label>
                            <input type="text" name="rnumber" class="form-control"
                                value="<?php echo htmlspecialchars($branch['rnumber']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch Contact *</label>
                            <input type="text" name="bcontact" class="form-control"
                                value="<?php echo htmlspecialchars($branch['bcontact']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch Email</label>
                            <input type="email" name="bemail" class="form-control"
                                value="<?php echo htmlspecialchars($branch['bemail']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Manager</label>
                            <select name="mid" class="form-control">
                                <option value="0">Not Assigned</option>
                                <?php foreach ($managers as $manager): ?>
                                    <option value="<?php echo $manager['id']; ?>" <?php echo $branch['mid'] == $manager['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($manager['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address *</label>
                            <textarea name="baddress" class="form-control" rows="3"
                                required><?php echo htmlspecialchars($branch['baddress']); ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State *</label>
                            <input type="text" name="state" class="form-control"
                                value="<?php echo htmlspecialchars($branch['state']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">District *</label>
                            <input type="text" name="dis" class="form-control"
                                value="<?php echo htmlspecialchars($branch['dis']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pincode *</label>
                            <input type="text" name="pincode" class="form-control"
                                value="<?php echo htmlspecialchars($branch['pincode']); ?>" required>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" name="status" class="form-check-input" id="status" <?php echo $branch['status'] == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Branch
                            </button>
                            <a href="list.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
