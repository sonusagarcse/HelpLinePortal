<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'View Course';

if (!isset($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT c.*, cc.name as category_name FROM courses c LEFT JOIN course_category cc ON c.pid = cc.id WHERE c.id = $id";
$result = mysqli_query($con, $query);
$course = mysqli_fetch_assoc($result);

if (!$course) {
    header('Location: list.php');
    exit;
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
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <h1>Course Details: <?php echo htmlspecialchars($course['name']); ?></h1>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="table-card text-center">
                        <?php if ($course['img']): ?>
                            <img src="../../../<?php echo $course['img']; ?>" class="img-fluid rounded mb-3" alt="Course Image">
                        <?php else: ?>
                            <div class="bg-light p-5 rounded mb-3"><i class="fas fa-book fa-5x text-muted"></i></div>
                        <?php endif; ?>
                        <h4><?php echo htmlspecialchars($course['title']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($course['category_name']); ?></p>
                        <div class="d-grid gap-2">
                            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning">Edit Course</a>
                            <a href="list.php" class="btn btn-secondary">Back to List</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="table-card">
                        <table class="table">
                            <tr><th>Duration:</th><td><?php echo $course['duration']; ?> Months</td></tr>
                            <tr><th>Course Fees:</th><td>₹<?php echo number_format($course['fees']); ?></td></tr>
                            <tr><th>Status:</th><td><span class="badge <?php echo $course['status'] == 1 ? 'bg-success' : 'bg-danger'; ?>"><?php echo $course['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td></tr>
                            <tr><th>Added Date:</th><td><?php echo htmlspecialchars($course['date']); ?></td></tr>
                        </table>
                        <h5 class="mt-4">Description:</h5>
                        <div class="p-3 border rounded bg-light">
                            <?php echo nl2br(htmlspecialchars($course['des'])); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
