<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Admin Profile';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $uname = mysqli_real_escape_string($con, $_POST['uname']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        
        $query = "UPDATE admin_login SET uname = '$uname', email = '$email' WHERE id = " . $_SESSION['admin_id'];
        if (mysqli_query($con, $query)) {
            $_SESSION['admin_name'] = $uname;
            $success = "Profile updated successfully!";
        } else {
            $error = "Error updating profile: " . mysqli_error($con);
        }
    } elseif (isset($_POST['change_password'])) {
        $old_pass = $_POST['old_pass'];
        $new_pass = $_POST['new_pass'];
        $confirm_pass = $_POST['confirm_pass'];
        
        if ($new_pass !== $confirm_pass) {
            $error = "New passwords do not match!";
        } else {
            $query = "SELECT pass FROM admin_login WHERE id = " . $_SESSION['admin_id'];
            $result = mysqli_query($con, $query);
            $row = mysqli_fetch_assoc($result);
            
            if (password_verify($old_pass, $row['pass'])) {
                $hashed_pass = password_hash($new_pass, PASSWORD_BCRYPT);
                $update_query = "UPDATE admin_login SET pass = '$hashed_pass' WHERE id = " . $_SESSION['admin_id'];
                if (mysqli_query($con, $update_query)) {
                    $success = "Password changed successfully!";
                } else {
                    $error = "Error changing password: " . mysqli_error($con);
                }
            } else {
                $error = "Current password is incorrect!";
            }
        }
    }
}

// Fetch current user data
$query = "SELECT * FROM admin_login WHERE id = " . $_SESSION['admin_id'];
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

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
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="fas fa-user-circle fa-2x"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <h1>My Profile</h1>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="uname" class="form-control" value="<?php echo htmlspecialchars($user['uname']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="old_pass" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_pass" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="confirm_pass" class="form-control" required>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-warning text-white">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
