<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Vendor Management';

// Get all vendors
$query = "SELECT * FROM add_vendor ORDER BY id DESC";
$result = mysqli_query($con, $query);
$vendors = [];
while ($row = mysqli_fetch_assoc($result)) {
    $vendors[] = $row;
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Vendor Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Vendors</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="vendor-add.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Vendor</a>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vendor Name</th>
                                <th>Contact</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vendors as $vendor): ?>
                                <tr>
                                    <td><?php echo $vendor['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars(($vendor['name']) ?? ''); ?></strong></td>
                                    <td><?php echo htmlspecialchars(($vendor['mobile']) ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(($vendor['date']) ?? ''); ?></td>
                                    <td><span class="badge <?php echo $vendor['status'] == 1 ? 'bg-success' : 'bg-danger'; ?>"><?php echo $vendor['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="vendor-edit.php?id=<?php echo $vendor['id']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                            <a href="vendor-delete.php?id=<?php echo $vendor['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete vendor?')"><i class="fas fa-trash"></i></a>
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
