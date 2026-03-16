<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'User Inquiries';

// Get all queries
$query = "SELECT m.*, b.bname as branch_name 
          FROM mquery m 
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
                        <h1>User Inquiries</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Inquiries</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Branch</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($queries as $q): ?>
                                <tr>
                                    <td><?php echo $q['id']; ?></td>
                                    <td><strong>Category <?php echo $q['mcategory']; ?></strong></td>
                                    <td><?php echo htmlspecialchars(substr(($q['des']) ?? '', 0, 100)); ?>...</td>
                                    <td><?php echo htmlspecialchars(($q['pdate']) ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(($q['branch_name']) ?? 'General'); ?></td>
                                    <td><span class="badge <?php echo $q['status'] == 1 ? 'bg-success' : 'bg-warning'; ?>"><?php echo $q['status'] == 1 ? 'Resolved' : 'Pending'; ?></span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="view.php?id=<?php echo $q['id']; ?>" class="btn btn-info"><i class="fas fa-eye"></i></a>
                                            <a href="delete.php?id=<?php echo $q['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete inquiry?')"><i class="fas fa-trash"></i></a>
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
