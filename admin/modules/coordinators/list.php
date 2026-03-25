<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Centre Coordinators';

$query = "SELECT c.*, b.bname, b.bcode FROM centre_coordinator c 
          LEFT JOIN branch b ON c.bid = b.id 
          ORDER BY c.id DESC";
$result = mysqli_query($con, $query);
$coordinators = [];
while ($row = mysqli_fetch_assoc($result)) {
    $coordinators[] = $row;
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
                    <div class="role"><?php echo $admin_type == 1 ? 'Super Admin' : 'Admin'; ?></div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="fas fa-user-circle fa-2x"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Coordinator Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Centre Coordinators</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="add.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Coordinator</a>
                    </div>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Operation completed successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Assigned Branch</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coordinators as $coord): ?>
                                <tr>
                                    <td><?php echo $coord['id']; ?></td>
                                    <td><span class="badge bg-primary"><?php echo htmlspecialchars($coord['username']); ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($coord['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($coord['mob']); ?></td>
                                    <td><?php echo htmlspecialchars($coord['email']); ?></td>
                                    <td><?php echo htmlspecialchars($coord['bcode'] . ' - ' . $coord['bname']); ?></td>
                                    <td>
                                        <?php if ($coord['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit.php?id=<?php echo $coord['id']; ?>" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="delete.php?id=<?php echo $coord['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this Coordinator?')" title="Delete"><i class="fas fa-trash"></i></a>
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
