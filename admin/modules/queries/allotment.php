<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Data Allotment';

// Get all data allotments
$query = "SELECT d.*, c.name as caller_name, s.name as supervisor_name 
          FROM dataallotment d 
          LEFT JOIN caller c ON d.callerid = c.id 
          LEFT JOIN supervisor s ON d.svid = s.id 
          ORDER BY d.id DESC";
$result = mysqli_query($con, $query);
$allotments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $allotments[] = $row;
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Data Allotment Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Data Allotment</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="add.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Assign Data
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Caller</th>
                                <th>Supervisor</th>
                                <th>Previous Date</th>
                                <th>Next Date</th>
                                <th>Remarks</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allotments as $allotment): ?>
                                <tr>
                                    <td><?php echo $allotment['id']; ?></td>
                                    <td><?php echo htmlspecialchars($allotment['caller_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($allotment['supervisor_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($allotment['pdate']); ?></td>
                                    <td><?php echo htmlspecialchars($allotment['nextdate']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($allotment['remarks'], 0, 30)); ?>...</td>
                                    <td><?php echo htmlspecialchars($allotment['mcategory']); ?></td>
                                    <td><?php echo htmlspecialchars($allotment['date']); ?></td>
                                    <td>
                                        <?php if ($allotment['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Completed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="view.php?id=<?php echo $allotment['id']; ?>" class="btn btn-info"
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $allotment['id']; ?>" class="btn btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
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
