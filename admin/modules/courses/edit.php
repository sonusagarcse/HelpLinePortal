<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Edit Course';

if (!isset($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = (int)$_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = (int)$_POST['pid'];
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $duration = (int)$_POST['duration'];
    $fees = (int)$_POST['fees'];
    $des = mysqli_real_escape_string($con, $_POST['des']);
    $status = isset($_POST['status']) ? 1 : 0;

    // Handle image upload
    $img_update = "";
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $upload_dir = '../../../images/courses/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = time() . '_' . $_FILES['img']['name'];
        if (move_uploaded_file($_FILES['img']['tmp_name'], $upload_dir . $filename)) {
            $img_path = 'images/courses/' . $filename;
            $img_update = ", img = '$img_path'";
        }
    }

    $query = "UPDATE courses SET pid = ?, name = ?, title = ?, des = ?, duration = ?, fees = ?, status = ? $img_update WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "isssisii", $pid, $name, $title, $des, $duration, $fees, $status, $id);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: list.php?success=updated');
        exit;
    } else {
        $error = 'Failed to update course: ' . mysqli_error($con);
    }
}

// Fetch course data
$result = mysqli_query($con, "SELECT * FROM courses WHERE id = $id");
$course = mysqli_fetch_assoc($result);

if (!$course) {
    header('Location: list.php');
    exit;
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
                    <div class="role">Admin</div>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <h1>Edit Course: <?php echo htmlspecialchars($course['name']); ?></h1>
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
                                <?php while($c = mysqli_fetch_assoc($categories)): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo $course['pid'] == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course Code *</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($course['name']); ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Course Title *</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($course['title']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Duration (Months) *</label>
                            <input type="number" name="duration" class="form-control" value="<?php echo $course['duration']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fees (₹) *</label>
                            <input type="number" name="fees" class="form-control" value="<?php echo $course['fees']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">New Course Image (Optional)</label>
                            <input type="file" name="img" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="des" class="form-control" rows="4"><?php echo htmlspecialchars($course['des']); ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" id="status" <?php echo $course['status'] == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="status">Visible on Website</label>
                            </div>
                        </div>

                        <div class="col-md-12 mt-4">
                            <button type="submit" class="btn btn-primary">Update Course</button>
                            <a href="list.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
