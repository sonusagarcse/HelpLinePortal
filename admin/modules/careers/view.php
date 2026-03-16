<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'View Application';

if (!isset($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM career WHERE id = $id";
$result = mysqli_query($con, $query);
$app = mysqli_fetch_assoc($result);

if (!$app) {
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
                <div class="d-flex justify-content-between">
                    <h1>Application Details: <?php echo htmlspecialchars($app['name']); ?></h1>
                    <a href="list.php" class="btn btn-secondary">Back to List</a>
                </div>
            </div>

            <div class="table-card">
                <div class="row">
                    <div class="col-md-8">
                        <table class="table">
                            <tr><th width="30%">Candidate Name:</th><td><?php echo htmlspecialchars($app['name']); ?></td></tr>
                            <tr><th>Mobile Number:</th><td><?php echo htmlspecialchars($app['mob']); ?></td></tr>
                            <tr><th>Email Address:</th><td><?php echo htmlspecialchars($app['email']); ?></td></tr>
                            <tr><th>Application Date:</th><td><?php echo htmlspecialchars($app['date']); ?></td></tr>
                        </table>
                    </div>
                    <div class="col-md-4 text-center">
                        <h5 class="mb-3">Resume / Documents</h5>
                        <?php if ($app['resume']): ?>
                            <a href="../../../<?php echo $app['resume']; ?>" target="_blank" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-file-download me-2"></i>View Resume
                            </a>
                        <?php else: ?>
                            <div class="alert alert-warning">No resume uploaded</div>
                        <?php endif; ?>
                        
                        <a href="delete.php?id=<?php echo $id; ?>" class="btn btn-danger w-100" onclick="return confirm('Delete this application?')">
                            <i class="fas fa-trash me-2"></i>Delete Application
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
