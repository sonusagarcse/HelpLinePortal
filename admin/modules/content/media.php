<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Media Library';

// Get all photos
$query = "SELECT p.*, c.name as category_name 
          FROM photos p 
          LEFT JOIN photo_category c ON p.cid = c.id 
          ORDER BY p.id DESC";
$result = mysqli_query($con, $query);
$photos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $photos[] = $row;
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
                        <h1>Media Library</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Media</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="media-add.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Upload Media</a>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php foreach ($photos as $photo): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm border-0 gallery-card">
                            <div class="position-relative">
                                <img src="<?php echo $SITE_URL; ?>/uploads/gallery/<?php echo $photo['img']; ?>" class="card-img-top" alt="Media" style="height: 180px; object-fit: cover;">
                                <span class="badge bg-dark position-absolute top-0 end-0 m-2 opacity-75"><?php echo htmlspecialchars(($photo['category_name']) ?? ''); ?></span>
                            </div>
                            <div class="card-body p-2">
                                <h6 class="card-title text-truncate mb-1"><?php echo htmlspecialchars(($photo['name']) ?? ''); ?></h6>
                                <p class="text-muted small mb-0"><?php echo $photo['date']; ?></p>
                            </div>
                            <div class="card-footer bg-transparent border-0 text-center pb-2">
                                <div class="btn-group btn-group-sm">
                                    <a href="media-edit.php?id=<?php echo $photo['id']; ?>" class="btn btn-outline-warning text-warning"><i class="fas fa-edit"></i></a>
                                    <a href="media-delete.php?id=<?php echo $photo['id']; ?>" class="btn btn-outline-danger text-danger" onclick="return confirm('Delete media?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.gallery-card:hover { transform: translateY(-5px); transition: all 0.3s ease; box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
</style>

<?php include('../../includes/footer.php'); ?>
