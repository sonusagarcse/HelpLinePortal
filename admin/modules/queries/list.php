<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Queries';

// Get all queries
$query = "SELECT m.*, c.name as caller_name, b.bname 
          FROM mquery m 
          LEFT JOIN caller c ON m.callerid = c.id 
          LEFT JOIN branch b ON m.bid = b.id 
          ORDER BY m.id DESC";
$result = mysqli_query($con, $query);
$queries = [];
while ($row = mysqli_fetch_assoc($result)) {
    $queries[] = $row;
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
                        <h1>Queries Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Queries</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="add.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Query
                        </a>
                        <a href="allotment.php" class="btn btn-secondary">
                            <i class="fas fa-tasks me-2"></i>Data Allotment
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
                                <th>Branch</th>
                                <th>Description</th>
                                <th>Previous Date</th>
                                <th>Next Date</th>
                                <th>Remarks</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($queries as $query_item): ?>
                                <tr>
                                    <td><?php echo $query_item['id']; ?></td>
                                    <td><?php echo htmlspecialchars($query_item['caller_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($query_item['bname'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars(substr(strip_tags($query_item['des'] ?? ''), 0, 50)); ?>...
                                    </td>
                                    <td><?php echo htmlspecialchars($query_item['pdate'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($query_item['nextdate'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars(substr($query_item['remarks'] ?? '', 0, 30)); ?></td>
                                    <td><?php echo htmlspecialchars($query_item['date'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if ($query_item['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Closed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="view.php?id=<?php echo $query_item['id']; ?>" class="btn btn-info"
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $query_item['id']; ?>" class="btn btn-warning"
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
