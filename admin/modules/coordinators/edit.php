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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $mob = mysqli_real_escape_string($con, $_POST['mob']);
    $bid = mysqli_real_escape_string($con, $_POST['bid']);
    $status = isset($_POST['status']) ? 1 : 0;

    if (!empty($_POST['pass'])) {
        $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
        $u_query = "UPDATE centre_coordinator SET bid=?, username=?, name=?, email=?, mob=?, pass=?, status=? WHERE id=?";
        $u_stmt = mysqli_prepare($con, $u_query);
        mysqli_stmt_bind_param($u_stmt, "isssssii", $bid, $username, $name, $email, $mob, $pass, $status, $id);
    } else {
        $u_query = "UPDATE centre_coordinator SET bid=?, username=?, name=?, email=?, mob=?, status=? WHERE id=?";
        $u_stmt = mysqli_prepare($con, $u_query);
        mysqli_stmt_bind_param($u_stmt, "issssii", $bid, $username, $name, $email, $mob, $status, $id);
    }

    if (mysqli_stmt_execute($u_stmt)) {
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
                            <label>Branch</label>
                            <select name="bid" class="form-control" required>
                                <option value="">Select Branch</option>
                                <?php foreach($branches as $b): ?>
                                    <option value="<?php echo $b['id']; ?>" <?php echo $coord['bid'] == $b['id'] ? 'selected' : ''; ?>><?php echo $b['bcode'].' - '.$b['bname']; ?></option>
                                <?php endforeach; ?>
                            </select>
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
