<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Edit Coordinator';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT * FROM centre_coordinator WHERE id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$coord = mysqli_fetch_assoc($result);

if (!$coord) {
    header('Location: list.php?error=not_found');
    exit;
}

// Get assigned branches for coordinator
$assigned_bids = [];
$b_res = mysqli_query($con, "SELECT branch_id FROM coordinator_branches WHERE coordinator_id = $id AND status = 1");
while ($b = mysqli_fetch_assoc($b_res)) {
    $assigned_bids[] = $b['branch_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $mob = mysqli_real_escape_string($con, $_POST['mob']);
    
    $bids = isset($_POST['bids']) && is_array($_POST['bids']) ? $_POST['bids'] : [];
    $legacy_bid = 0; // Legacy column fallback

    $status = isset($_POST['status']) ? 1 : 0;

    if (!empty($_POST['pass'])) {
        $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
        $u_query = "UPDATE centre_coordinator SET bid=?, username=?, name=?, email=?, mob=?, pass=?, status=? WHERE id=?";
        $u_stmt = mysqli_prepare($con, $u_query);
        mysqli_stmt_bind_param($u_stmt, "isssssii", $legacy_bid, $username, $name, $email, $mob, $pass, $status, $id);
    } else {
        $u_query = "UPDATE centre_coordinator SET bid=?, username=?, name=?, email=?, mob=?, status=? WHERE id=?";
        $u_stmt = mysqli_prepare($con, $u_query);
        mysqli_stmt_bind_param($u_stmt, "issssii", $legacy_bid, $username, $name, $email, $mob, $status, $id);
    }

    if (mysqli_stmt_execute($u_stmt)) {
        // Update branches
        mysqli_query($con, "UPDATE coordinator_branches SET status = 0 WHERE coordinator_id = $id");
        if (!empty($bids)) {
            $b_query = "INSERT INTO coordinator_branches (coordinator_id, branch_id, assigned_date, status) VALUES (?, ?, ?, 1) ON DUPLICATE KEY UPDATE status = 1";
            $b_stmt = mysqli_prepare($con, $b_query);
            $date = date('Y-m-d');
            foreach ($bids as $branch_id) {
                $branch_id = (int)$branch_id;
                mysqli_stmt_bind_param($b_stmt, "iis", $id, $branch_id, $date);
                mysqli_stmt_execute($b_stmt);
            }
        }
        
        header('Location: list.php?success=updated');
        exit;
    } else {
        $error = 'Failed: ' . mysqli_error($con);
    }
}

$branches = [];
$res = mysqli_query($con, "SELECT id, bname, bcode FROM branch WHERE status=1 ORDER BY bname");
while($r = mysqli_fetch_assoc($res)) $branches[] = $r;

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
                <h1>Edit Coordinator</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="list.php">Coordinators</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
            <div class="table-card">
                <?php if(isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($coord['username']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($coord['name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($coord['email']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Mobile</label>
                            <input type="text" name="mob" class="form-control" value="<?php echo htmlspecialchars($coord['mob']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Branch Assignment *</label>
                            <select name="bids[]" class="form-control" multiple required style="height:120px;">
                                <?php foreach($branches as $b): ?>
                                    <option value="<?php echo $b['id']; ?>" <?php echo in_array($b['id'], $assigned_bids) ? 'selected' : ''; ?>><?php echo htmlspecialchars($b['bcode'].' - '.$b['bname']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Hold CTRL/CMD to select multiple branches</small>
                        </div>
                        <div class="col-md-6">
                            <label>New Password (leave blank to keep current)</label>
                            <input type="password" name="pass" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" name="status" class="form-check-input" id="status" <?php echo $coord['status'] == 1 ? 'checked' : ''; ?>>
                                <label for="status">Active</label>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="list.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('../../includes/footer.php'); ?>
