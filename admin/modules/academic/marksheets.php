<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Marksheets';

// Get all marksheets
$query = "SELECT m.*, b.bname as branch_name 
          FROM m_marksheet m 
          LEFT JOIN branch b ON m.bid = b.id 
          ORDER BY m.id DESC";
$result = mysqli_query($con, $query);
$marksheets = [];
while ($row = mysqli_fetch_assoc($result)) {
    $marksheets[] = $row;
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
                        <li><a class="dropdown-item" href="../../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
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
                        <h1>Marksheets Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Marksheets</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="marksheets-add.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Generate Marksheet
                        </a>
                    </div>
                </div>
            </div>

            <!-- Marksheets Table -->
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Roll No</th>
                                <th>Exam</th>
                                <th>Exam Date</th>
                                <th>Theory</th>
                                <th>Practical</th>
                                <th>Branch</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($marksheets as $ms): ?>
                                <tr>
                                    <td><?php echo $ms['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars(($ms['rollno']) ?? ''); ?></strong></td>
                                    <td><?php echo htmlspecialchars(($ms['exam']) ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(($ms['exam_date']) ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(($ms['theory']) ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(($ms['practical']) ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(($ms['branch_name']) ?? 'General'); ?></td>
                                    <td>
                                        <?php if ($ms['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="marksheets-view.php?id=<?php echo $ms['id']; ?>" class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="marksheets-edit.php?id=<?php echo $ms['id']; ?>" class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="marksheets-delete.php?id=<?php echo $ms['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
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
