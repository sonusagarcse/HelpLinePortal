<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Courses';

// Get all courses
$query = "SELECT c.*, cc.name as category_name 
          FROM courses c 
          LEFT JOIN course_category cc ON c.pid = cc.id 
          ORDER BY c.id DESC";
$result = mysqli_query($con, $query);
$courses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $courses[] = $row;
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
                        <h1>Courses Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Courses</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="add.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Course
                        </a>
                        <a href="categories.php" class="btn btn-secondary">
                            <i class="fas fa-folder me-2"></i>Categories
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
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Duration (Months)</th>
                                <th>Fees (₹)</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?php echo $course['id']; ?></td>
                                    <td><span
                                            class="badge bg-primary"><?php echo htmlspecialchars(($course['name']) ?? ''); ?></span>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars(($course['title']) ?? ''); ?></strong></td>
                                    <td><?php echo htmlspecialchars(substr($course['title'], 0, 50)); ?></td>
                                    <td><?php echo htmlspecialchars(($course['category_name']) ?? 'N/A'); ?></td>
                                    <td><?php echo $course['duration']; ?> months</td>
                                    <td>₹<?php echo number_format($course['fees']); ?></td>
                                    <td><?php echo htmlspecialchars(($course['date']) ?? ''); ?></td>
                                    <td>
                                        <?php if ($course['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="view.php?id=<?php echo $course['id']; ?>" class="btn btn-info"
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $course['id']; ?>" class="btn btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?php echo $course['id']; ?>" class="btn btn-danger"
                                                onclick="return confirmDelete('Are you sure you want to delete this course?')"
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
