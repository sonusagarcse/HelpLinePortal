<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Study Materials';

// Get all study materials
$query = "SELECT s.*, c.name as category_name 
          FROM studymaterials s 
          LEFT JOIN course_category c ON s.cid = c.id 
          ORDER BY s.id DESC";
$result = mysqli_query($con, $query);
$materials = [];
while ($row = mysqli_fetch_assoc($result)) {
    $materials[] = $row;
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
                        <h1>Study Materials</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Study Materials</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="materials-add.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Material</a>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Drive Link</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $material): ?>
                                <tr>
                                    <td><?php echo $material['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars(($material['title']) ?? ''); ?></strong></td>
                                    <td><?php echo htmlspecialchars(($material['category_name']) ?? ''); ?></td>
                                    <td><a href="<?php echo htmlspecialchars(($material['driveadd']) ?? ''); ?>" target="_blank" class="text-primary truncate-text"><?php echo htmlspecialchars(substr(($material['driveadd']) ?? '', 0, 40)); ?>...</a></td>
                                    <td><span class="badge <?php echo $material['status'] == 1 ? 'bg-success' : 'bg-danger'; ?>"><?php echo $material['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="materials-edit.php?id=<?php echo $material['id']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                            <a href="materials-delete.php?id=<?php echo $material['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
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
