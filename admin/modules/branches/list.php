<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Branches';

// Filter by status
$status_filter = isset($_GET['status']) ? (int)$_GET['status'] : 1;

// Get branches based on status
$query = "SELECT b.*, m.name as manager_name 
          FROM branch b 
          LEFT JOIN manager m ON b.mid = m.id 
          WHERE b.status = ? 
          ORDER BY b.id DESC";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $status_filter);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$branches = [];
while ($row = mysqli_fetch_assoc($result)) {
    $branches[] = $row;
}

include('../../includes/header.php');
?>

<div class="wrapper">
    <?php include('../../includes/sidebar.php'); ?>

    <div id="content">
        <!-- Top Navbar -->
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
                        <li><a class="dropdown-item" href="../../modules/settings/profile.php"><i
                                    class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="../../modules/settings/index.php"><i
                                    class="fas fa-cog me-2"></i>Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="../../logout.php"><i
                                    class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Branches Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Branches</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="btn-group shadow-sm">
                            <a href="list.php?status=1" class="btn <?php echo $status_filter == 1 ? 'btn-success' : 'btn-outline-success'; ?> px-3">
                                <i class="fas fa-check-circle me-1"></i> Active
                            </a>
                            <a href="list.php?status=0" class="btn <?php echo $status_filter == 0 ? 'btn-danger' : 'btn-outline-danger'; ?> px-3">
                                <i class="fas fa-times-circle me-1"></i> Inactive
                            </a>
                        </div>
                        <a href="add.php" class="btn btn-primary shadow-sm ms-2">
                            <i class="fas fa-plus me-2"></i>Add New Branch
                        </a>
                    </div>
                </div>
            </div>

            <!-- Branches Table -->
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Branch Code</th>
                                <th>Branch Name</th>
                                <th>Registration No</th>
                                <th>Manager</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($branches as $branch): ?>
                                <tr>
                                    <td><?php echo $branch['id']; ?></td>
                                    <td><span
                                            class="badge bg-primary"><?php echo htmlspecialchars($branch['bcode']); ?></span>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($branch['bname']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($branch['regno']); ?></td>
                                    <td><?php echo htmlspecialchars($branch['manager_name'] ?? 'Not Assigned'); ?></td>
                                    <td><?php echo htmlspecialchars($branch['bcontact']); ?></td>
                                    <td><?php echo htmlspecialchars($branch['bemail']); ?></td>
                                    <td><?php echo htmlspecialchars($branch['dis'] . ', ' . $branch['state']); ?></td>
                                    <td>
                                        <?php if ($branch['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="view.php?id=<?php echo $branch['id']; ?>" class="btn btn-info"
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $branch['id']; ?>" class="btn btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?php echo $branch['id']; ?>" class="btn btn-danger"
                                                onclick="return confirmDelete('Are you sure you want to delete this branch?')"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
