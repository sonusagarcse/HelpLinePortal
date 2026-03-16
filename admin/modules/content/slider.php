<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Slider Management';

// Get all slider items
$query = "SELECT * FROM slider ORDER BY id DESC";
$result = mysqli_query($con, $query);
$slides = [];
while ($row = mysqli_fetch_assoc($result)) {
    $slides[] = $row;
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
                        <h1>Slider Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Slider</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="slider-add.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Slide</a>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php foreach ($slides as $slide): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="<?php echo $SITE_URL; ?>/uploads/slider/<?php echo $slide['img']; ?>" class="card-img-top" alt="Slide" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars(($slide['title']) ?? ''); ?></h5>
                                <p class="card-text text-muted small"><?php echo htmlspecialchars(($slide['subtitle']) ?? ''); ?></p>
                            </div>
                            <div class="card-footer bg-transparent border-0 d-flex justify-content-between">
                                <span class="text-muted small"><?php echo $slide['date']; ?></span>
                                <div class="btn-group btn-group-sm">
                                    <a href="slider-edit.php?id=<?php echo $slide['id']; ?>" class="btn btn-outline-warning"><i class="fas fa-edit"></i></a>
                                    <a href="slider-delete.php?id=<?php echo $slide['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Delete slide?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
