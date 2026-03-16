<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Edit Service';

if (!isset($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s_name = mysqli_real_escape_string($con, $_POST['s_name']);
    $des = mysqli_real_escape_string($con, $_POST['des']);
    
    $query = "UPDATE services SET s_name = '$s_name', des = '$des' WHERE id = $id";
    if (mysqli_query($con, $query)) {
        header('Location: list.php?success=updated');
        exit;
    }
}

$result = mysqli_query($con, "SELECT * FROM services WHERE id = $id");
$service = mysqli_fetch_assoc($result);

if (!$service) {
    header('Location: list.php');
    exit;
}

include('../../includes/header.php');
?>
<div class="wrapper">
    <?php include('../../includes/sidebar.php'); ?>
    <div id="content">
        <div class="main-content">
            <div class="page-header"><h1>Edit Service</h1></div>
            <div class="table-card">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Service Name</label>
                        <input type="text" name="s_name" class="form-control" value="<?php echo htmlspecialchars($service['s_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="des" class="form-control" rows="5" required><?php echo htmlspecialchars($service['des']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Service</button>
                    <a href="list.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('../../includes/footer.php'); ?>
