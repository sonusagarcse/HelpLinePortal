<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Video Library';

// Get all videos
$query = "SELECT v.*, c.name as category_name 
          FROM videos v 
          LEFT JOIN video_category c ON v.cid = c.id 
          ORDER BY v.id DESC";
$result = mysqli_query($con, $query);
$videos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $videos[] = $row;
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
                        <h1>Video Library</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Videos</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="video-add.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Video</a>
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
                                <th>Video Link/ID</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($videos as $video): ?>
                                <tr>
                                    <td><?php echo $video['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars(($video['title']) ?? ''); ?></strong></td>
                                    <td><?php echo htmlspecialchars(($video['category_name']) ?? ''); ?></td>
                                    <td><code><?php echo htmlspecialchars(($video['vdo']) ?? ''); ?></code></td>
                                    <td><span class="badge <?php echo $video['status'] == 1 ? 'bg-success' : 'bg-danger'; ?>"><?php echo $video['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="video-edit.php?id=<?php echo $video['id']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                            <a href="video-delete.php?id=<?php echo $video['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
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
