<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Add DEO';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $regno = mysqli_real_escape_string($con, $_POST['regno']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $mob = mysqli_real_escape_string($con, $_POST['mob']);
    $bid = mysqli_real_escape_string($con, $_POST['bid']);
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
    $status = isset($_POST['status']) ? 1 : 0;
    $date = date('d-m-Y');

    // Check for duplicate registration number
    $check_query = "SELECT id FROM deo WHERE regno = ? OR email = ?";
    $check_stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ss", $regno, $email);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        $error = 'Registration number or Email already exists!';
    } else {
        $query = "INSERT INTO deo (name, email, mob, pass, regno, bid, status, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "sssssiis", $name, $email, $mob, $pass, $regno, $bid, $status, $date);

        if (mysqli_stmt_execute($stmt)) {
            logActivity('create_deo', 'deo', mysqli_insert_id($con), null, json_encode($_POST));
            header('Location: list.php?success=added');
            exit;
        } else {
            $error = 'Failed to add DEO: ' . mysqli_error($con);
        }
    }
}

// Auto-generate registration number
$result = mysqli_query($con, "SELECT MAX(CAST(SUBSTRING(regno, 4) AS UNSIGNED)) as max_num FROM deo WHERE regno LIKE 'DEO%'");
$row = mysqli_fetch_assoc($result);
$next_num = ($row['max_num'] ?? 0) + 1;
$auto_regno = 'DEO' . str_pad($next_num, 4, '0', STR_PAD_LEFT);

// Get branches
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
            <div class="user-menu">
                <div class="user-info">
                    <div class="name"><?php echo $admin_name; ?></div>
                    <div class="role">Admin</div>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <h1>Add New DEO Account</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="list.php">DEOs</a></li>
                        <li class="breadcrumb-item active">Add DEO</li>
                    </ol>
                </nav>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-card">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Registration Number *</label>
                            <input type="text" name="regno" class="form-control" value="<?php echo $auto_regno; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Number *</label>
                            <input type="text" name="mob" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Branch Assignment</label>
                            <select name="bid" class="form-select">
                                <option value="0">All Branches</option>
                                <?php while($b = mysqli_fetch_assoc($branches)): ?>
                                    <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['bname']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" name="pass" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" id="status" checked>
                                <label class="form-check-label" for="status">Active Account</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Save DEO Account</button>
                            <a href="list.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
