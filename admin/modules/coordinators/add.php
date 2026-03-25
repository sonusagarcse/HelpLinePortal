<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Add Coordinator';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $mob = mysqli_real_escape_string($con, $_POST['mob']);
    $bid = mysqli_real_escape_string($con, $_POST['bid']);
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
    $status = isset($_POST['status']) ? 1 : 0;
    $date = date('Y-m-d H:i:s');

    $query = "INSERT INTO centre_coordinator (bid, username, name, email, mob, pass, status, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "isssssis", $bid, $username, $name, $email, $mob, $pass, $status, $date);

    try {
        if (mysqli_stmt_execute($stmt)) {
            header('Location: list.php?success=added');
            exit;
        } else {
            $error = 'Failed: ' . mysqli_error($con);
        }
    } catch (Exception $e) {
        $error = 'Failed: This Username or Email might already be registered to another Coordinator. Please try a different one.';
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
                <h1>Add Coordinator</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="list.php">Coordinators</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </nav>
            </div>
            <div class="table-card">
                <?php if(isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Mobile</label>
                            <input type="text" name="mob" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Branch</label>
                            <select name="bid" class="form-control" required>
                                <option value="">Select Branch</option>
                                <?php foreach($branches as $b): ?>
                                    <option value="<?php echo $b['id']; ?>"><?php echo $b['bcode'].' - '.$b['bname']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Password</label>
                            <input type="password" name="pass" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" name="status" class="form-check-input" id="status" checked>
                                <label for="status">Active</label>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="list.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('../../includes/footer.php'); ?>
