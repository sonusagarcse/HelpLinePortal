<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Zoom Live Classes';

// Get all zoom classes
$query = "SELECT z.*, c.name as category_name, s.name as subject_name 
          FROM zoomliveclasses z 
          LEFT JOIN course_category c ON z.cid = c.id 
          LEFT JOIN subjects s ON z.sid = s.id 
          ORDER BY z.id DESC";
$result = mysqli_query($con, $query);
$classes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $classes[] = $row;
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
                        <h1>Zoom Live Classes</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Zoom Classes</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="zoom-add.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Zoom Class</a>
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
                                <th>Subject</th>
                                <th>Meeting Code</th>
                                <th>Password</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classes as $class): ?>
                                <tr>
                                    <td><?php echo $class['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars(($class['title']) ?? ''); ?></strong></td>
                                    <td><?php echo htmlspecialchars(($class['category_name']) ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(($class['subject_name']) ?? ''); ?></td>
                                    <td><code><?php echo htmlspecialchars(($class['code']) ?? ''); ?></code></td>
                                    <td><code><?php echo htmlspecialchars(($class['password']) ?? ''); ?></code></td>
                                    <td><span class="badge <?php echo $class['status'] == 1 ? 'bg-success' : 'bg-danger'; ?>"><?php echo $class['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="zoom-edit.php?id=<?php echo $class['id']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                            <a href="zoom-delete.php?id=<?php echo $class['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
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
