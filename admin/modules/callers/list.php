<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Callers';

$caller_type_filter = $_GET['type'] ?? '';

$where_clause = "";
if ($caller_type_filter === 'UG_PG') {
    $page_title = 'UG/PG Callers';
    $where_clause = "WHERE c.caller_type = 'UG_PG'";
} elseif ($caller_type_filter === 'KYP') {
    $where_clause = "WHERE c.caller_type = 'KYP'";
}

// Get all callers
$query = "SELECT c.*, s.name as supervisor_name, b.bname FROM caller c 
          LEFT JOIN supervisor s ON c.svid = s.id 
          LEFT JOIN branch b ON c.bid = b.id 
          $where_clause
          ORDER BY c.id DESC";
$result = mysqli_query($con, $query);
$callers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $callers[] = $row;
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
                        <h1><?php echo $page_title; ?> Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active"><?php echo $page_title; ?></li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="add.php<?php echo $caller_type_filter ? '?type='.$caller_type_filter : ''; ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Caller
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
                                <th>Reg No</th>
                                <th>Name</th>
                                <th>Father Name</th>
                                <th>Supervisor</th>
                                <th>Branch</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>DOJ</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($callers as $caller): ?>
                                <tr>
                                    <td><?php echo $caller['id']; ?></td>
                                    <td><span
                                            class="badge bg-primary"><?php echo htmlspecialchars($caller['regno']); ?></span>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($caller['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($caller['father']); ?></td>
                                    <td><?php echo htmlspecialchars($caller['supervisor_name'] ?? 'Not Assigned'); ?></td>
                                    <td><?php echo htmlspecialchars($caller['bname'] ?? 'Not Assigned'); ?></td>
                                    <td><?php echo htmlspecialchars($caller['mob']); ?></td>
                                    <td><?php echo htmlspecialchars($caller['email']); ?></td>
                                    <td><?php echo htmlspecialchars($caller['doj']); ?></td>
                                    <td>
                                        <?php if ($caller['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="view.php?id=<?php echo $caller['id']; ?>" class="btn btn-info"
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $caller['id']; ?>" class="btn btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?php echo $caller['id']; ?>" class="btn btn-danger"
                                                onclick="return confirmDelete('Are you sure you want to delete this caller?')"
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
