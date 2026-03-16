<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Add Course';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = (int)$_POST['pid'];
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $duration = (int)$_POST['duration'];
    $fees = (int)$_POST['fees'];
    $des = mysqli_real_escape_string($con, $_POST['des']);
    $status = isset($_POST['status']) ? 1 : 0;
    $date = date('d-m-Y');
    
    // Handle image upload (basic)
    $img = '';
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $upload_dir = '../../../images/courses/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = time() . '_' . $_FILES['img']['name'];
        if (move_uploaded_file($_FILES['img']['tmp_name'], $upload_dir . $filename)) {
            $img = 'images/courses/' . $filename;
        }
    }

    $query = "INSERT INTO courses (pid, name, title, img, des, duration, fees, status, date) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "issssiiss", $pid, $name, $title, $img, $des, $duration, $fees, $status, $date);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: list.php?success=added');
        exit;
    } else {
        $error = 'Failed to add course: ' . mysqli_error($con);
    }
}

// Get categories for dropdown
$categories = mysqli_query($con, "SELECT id, name FROM course_category ORDER BY name ASC");

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
                    <div class="role"><?php echo $admin_type == 1 ? 'Super Admin' : ($admin_type == 2 ? 'Manager' : ($admin_type == 3 ? 'Healthcare' : ($admin_type == 4 ? 'Supervisor' : ($admin_type == 5 ? 'Branch' : 'Admin')))); ?></div>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <h1>Add New Course</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="list.php">Courses</a></li>
                        <li class="breadcrumb-item active">Add Course</li>
                    </ol>
                </nav>
            </div>

            <div class="table-card">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Category *</label>
                            <select name="pid" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php while($c = mysqli_fetch_assoc($categories)): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course Code (e.g. ADCA) *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Course Full Title *</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Duration (Months) *</label>
                            <input type="number" name="duration" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Course Fees (₹) *</label>
                            <input type="number" name="fees" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Course Image</label>
                            <input type="file" name="img" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="des" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" id="status" checked>
                                <label class="form-check-label" for="status">Visible on Website</label>
                            </div>
                        </div>

                        <div class="col-md-12 mt-4">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Course</button>
                            <a href="list.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
