<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Add Service';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s_name = mysqli_real_escape_string($con, $_POST['s_name']);
    $des = mysqli_real_escape_string($con, $_POST['des']);
    $date = date('d-m-Y');
    
    $query = "INSERT INTO services (s_name, des, date) VALUES ('$s_name', '$des', '$date')";
    if (mysqli_query($con, $query)) {
        header('Location: list.php?success=added');
        exit;
    }
}

include('../../includes/header.php');
?>
<div class="wrapper">
    <?php include('../../includes/sidebar.php'); ?>
    <div id="content">
        <div class="main-content">
            <div class="page-header"><h1>Add Service</h1></div>
            <div class="table-card">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Service Name</label>
                        <input type="text" name="s_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="des" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Service</button>
                    <a href="list.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('../../includes/footer.php'); ?>
