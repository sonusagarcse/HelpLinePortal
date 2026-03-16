<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Edit Student';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = (int)$_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $regno = mysqli_real_escape_string($con, $_POST['regno']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $father = mysqli_real_escape_string($con, $_POST['father']);
    $mother = mysqli_real_escape_string($con, $_POST['mother']);
    $mob = mysqli_real_escape_string($con, $_POST['mob']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $dob = mysqli_real_escape_string($con, $_POST['dob']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $qualification = mysqli_real_escape_string($con, $_POST['qualification']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $state = mysqli_real_escape_string($con, $_POST['state']);
    $dis = mysqli_real_escape_string($con, $_POST['dis']);
    $pincode = mysqli_real_escape_string($con, $_POST['pincode']);
    $bid = (int)$_POST['bid'];
    $mcategory = (int)$_POST['mcategory'];
    $aadhar = mysqli_real_escape_string($con, $_POST['aadhar']);
    $status = isset($_POST['status']) ? 1 : 0;

    $query = "UPDATE registration SET 
                regno = ?, name = ?, father = ?, mother = ?, mob = ?, email = ?, 
                dob = ?, gender = ?, qualification = ?, address = ?, state = ?, 
                dis = ?, pincode = ?, bid = ?, mcategory = ?, aadhar = ?, status = ?
              WHERE id = ?";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "sssssssssssssiisii", 
        $regno, $name, $father, $mother, $mob, $email, $dob, $gender, $qualification, 
        $address, $state, $dis, $pincode, $bid, $mcategory, $aadhar, $status, $id
    );

    if (mysqli_stmt_execute($stmt)) {
        logActivity('update_student', 'registration', $id, null, json_encode($_POST));
        $success_msg = 'Student updated successfully!';
    } else {
        $error = 'Failed to update student: ' . mysqli_error($con);
    }
}

// Fetch current student data
$query = "SELECT * FROM registration WHERE id = $id";
$result = mysqli_query($con, $query);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    header('Location: list.php');
    exit;
}

// Get branches and categories for dropdowns
$branches = mysqli_query($con, "SELECT id, bname FROM branch WHERE status = 1 ORDER BY bname ASC");
$categories = mysqli_query($con, "SELECT id, name FROM member_category ORDER BY name ASC");

include('../../includes/header.php');
?>

<div class="wrapper">
    <?php include('../../includes/sidebar.php'); ?>

    <div id="content">
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-link">
                <i class="fas fa-bars"></i>
            </button>
            <div class="user-menu">
                <div class="user-info">
                    <div class="name"><?php echo $admin_name; ?></div>
                    <div class="role"><?php echo $admin_type == 1 ? 'Super Admin' : ($admin_type == 2 ? 'Manager' : ($admin_type == 3 ? 'Healthcare' : ($admin_type == 4 ? 'Supervisor' : ($admin_type == 5 ? 'Branch' : 'Admin')))); ?></div>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <h1>Edit Student: <?php echo htmlspecialchars($student['name']); ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="list.php">Students</a></li>
                        <li class="breadcrumb-item active">Edit Student</li>
                    </ol>
                </nav>
            </div>

            <div class="table-card">
                <?php if (isset($success_msg)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo $success_msg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Basic Information</h5>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Registration Number *</label>
                            <input type="text" name="regno" class="form-control" value="<?php echo htmlspecialchars($student['regno']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Student Name *</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Father's Name *</label>
                            <input type="text" name="father" class="form-control" value="<?php echo htmlspecialchars($student['father']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mother's Name</label>
                            <input type="text" name="mother" class="form-control" value="<?php echo htmlspecialchars($student['mother']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth *</label>
                            <input type="date" name="dob" class="form-control" value="<?php echo $student['dob']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender *</label>
                            <select name="gender" class="form-select" required>
                                <option value="Male" <?php echo ($student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($student['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Qualification *</label>
                            <input type="text" name="qualification" class="form-control" value="<?php echo htmlspecialchars($student['qualification']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Aadhar Number</label>
                            <input type="text" name="aadhar" class="form-control" value="<?php echo htmlspecialchars($student['aadhar']); ?>">
                        </div>
                        
                        <div class="col-md-12 mt-4">
                            <h5 class="border-bottom pb-2">Contact Details</h5>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile Number *</label>
                            <input type="text" name="mob" class="form-control" value="<?php echo htmlspecialchars($student['mob']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Full Address *</label>
                            <textarea name="address" class="form-control" rows="2" required><?php echo htmlspecialchars($student['address']); ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">District *</label>
                            <input type="text" name="dis" class="form-control" value="<?php echo htmlspecialchars($student['dis']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State *</label>
                            <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($student['state']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pincode *</label>
                            <input type="text" name="pincode" class="form-control" value="<?php echo htmlspecialchars($student['pincode']); ?>" required>
                        </div>

                        <div class="col-md-12 mt-4">
                            <h5 class="border-bottom pb-2">Academic & Branch Distribution</h5>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch *</label>
                            <select name="bid" class="form-select" required>
                                <?php while($b = mysqli_fetch_assoc($branches)): ?>
                                    <option value="<?php echo $b['id']; ?>" <?php echo ($student['bid'] == $b['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($b['bname']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category/School *</label>
                            <select name="mcategory" class="form-select" required>
                                <?php while($c = mysqli_fetch_assoc($categories)): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo ($student['mcategory'] == $c['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" id="status" <?php echo ($student['status'] == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="status">Active Student</label>
                            </div>
                        </div>

                        <div class="col-md-12 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Update Student
                            </button>
                            <a href="list.php" class="btn btn-secondary px-4">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
