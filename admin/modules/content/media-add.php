<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Upload Media';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid = (int)$_POST['cid'];
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $date = date('d-m-Y');
    
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $upload_dir = '../../../uploads/gallery/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = time() . '_' . $_FILES['img']['name'];
        if (move_uploaded_file($_FILES['img']['tmp_name'], $upload_dir . $filename)) {
            $query = "INSERT INTO photos (cid, name, img, date) VALUES ($cid, '$name', '$filename', '$date')";
            if (mysqli_query($con, $query)) {
                header('Location: media.php?success=1');
                exit;
            }
        }
    }
}

$categories = mysqli_query($con, "SELECT id, name FROM photo_category ORDER BY name ASC");

include('../../includes/header.php');
?>
<div class="wrapper">
    <?php include('../../includes/sidebar.php'); ?>
    <div id="content">
        <div class="main-content">
            <div class="page-header"><h1>Upload Media</h1></div>
            <div class="table-card">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="cid" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php while($c = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title/Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image File</label>
                        <input type="file" name="img" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                    <a href="media.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('../../includes/footer.php'); ?>
