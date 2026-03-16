<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Settings';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = mysqli_real_escape_string($con, $_POST['site_name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $address = mysqli_real_escape_string($con, $_POST['address']);

    $query = "UPDATE global_setting SET site_name=?, email=?, phone=?, address=? WHERE id=1";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $site_name, $email, $phone, $address);

    if (mysqli_stmt_execute($stmt)) {
        $success = 'Settings updated successfully';
        logActivity('update_settings', 'global_setting', 1, null, json_encode($_POST));
    } else {
        $error = 'Failed to update settings';
    }
}

// Get current settings
$query = "SELECT * FROM global_setting WHERE id = 1";
$result = mysqli_query($con, $query);
$settings = mysqli_fetch_assoc($result);

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
                <h1>System Settings</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </nav>
            </div>

            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="table-card">
                        <h5 class="mb-3">Global Settings</h5>

                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Site Name</label>
                                <input type="text" name="site_name" class="form-control"
                                    value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?php echo htmlspecialchars($settings['email']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control"
                                    value="<?php echo htmlspecialchars($settings['phone']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3"
                                    required><?php echo htmlspecialchars($settings['address']); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Settings
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="table-card mb-3">
                        <h5 class="mb-3">Quick Actions</h5>
                        <div class="d-grid gap-2">
                            <a href="backup.php" class="btn btn-outline-primary">
                                <i class="fas fa-database me-2"></i>Backup Database
                            </a>
                            <a href="logs.php" class="btn btn-outline-secondary">
                                <i class="fas fa-history me-2"></i>View Activity Logs
                            </a>
                            <a href="profile.php" class="btn btn-outline-info">
                                <i class="fas fa-user me-2"></i>Edit Profile
                            </a>
                        </div>
                    </div>

                    <div class="table-card">
                        <h5 class="mb-3">System Info</h5>
                        <table class="table table-sm">
                            <tr>
                                <td>PHP Version:</td>
                                <td><strong><?php echo phpversion(); ?></strong></td>
                            </tr>
                            <tr>
                                <td>MySQL Version:</td>
                                <td><strong><?php echo mysqli_get_server_info($con); ?></strong></td>
                            </tr>
                            <tr>
                                <td>Server:</td>
                                <td><strong><?php echo $_SERVER['SERVER_SOFTWARE']; ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
