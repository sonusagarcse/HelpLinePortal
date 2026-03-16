<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Add Study Material';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid = (int)$_POST['cid'];
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $driveadd = mysqli_real_escape_string($con, $_POST['driveadd']);
    $status = isset($_POST['status']) ? 1 : 0;
    $date = date('d-m-Y');
    
    $query = "INSERT INTO studymaterials (cid, title, driveadd, status, date) VALUES ($cid, '$title', '$driveadd', $status, '$date')";
    if (mysqli_query($con, $query)) {
        header('Location: materials.php?success=added');
        exit;
    }
}

$categories = mysqli_query($con, "SELECT id, name FROM course_category ORDER BY name ASC");

include('../../includes/header.php');
?>
<div class="wrapper">
    <?php include('../../includes/sidebar.php'); ?>
    <div id="content">
        <div class="main-content">
            <div class="page-header"><h1>Add Study Material</h1></div>
            <div class="table-card">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Course Category</label>
                        <select name="cid" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php while($c = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Google Drive Link</label>
                        <input type="url" name="driveadd" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="status" id="status" checked>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Material</button>
                    <a href="materials.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('../../includes/footer.php'); ?>
