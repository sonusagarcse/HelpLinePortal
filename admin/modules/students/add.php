<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Add Student';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $father = mysqli_real_escape_string($con, $_POST['father'] ?? '');
    $mother = mysqli_real_escape_string($con, $_POST['mother'] ?? '');
    $mob = mysqli_real_escape_string($con, $_POST['mob']);
    $email = mysqli_real_escape_string($con, $_POST['email'] ?? '');
    $dob = mysqli_real_escape_string($con, $_POST['dob'] ?? '0000-00-00');
    if (empty($dob)) $dob = '0000-00-00';
    $gender = mysqli_real_escape_string($con, $_POST['gender'] ?? '');
    $qualification = mysqli_real_escape_string($con, $_POST['qualification'] ?? '');
    $address = mysqli_real_escape_string($con, $_POST['address'] ?? '');
    $state = mysqli_real_escape_string($con, $_POST['state'] ?? 'Bihar');
    $dis = mysqli_real_escape_string($con, $_POST['dis'] ?? '');
    $pincode = mysqli_real_escape_string($con, $_POST['pincode'] ?? '');
    $bid = isset($_POST['bid']) ? (int)$_POST['bid'] : 0;
    $mcategory = isset($_POST['mcategory']) ? (int)$_POST['mcategory'] : 0;
    $aadhar = mysqli_real_escape_string($con, $_POST['aadhar'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;
    $date = date('d-m-Y');
    
    // Generate Registration Number if not provided
    if (empty($_POST['regno'])) {
        $year = date('Y');
        $result = mysqli_query($con, "SELECT COUNT(*) as count FROM registration WHERE regno LIKE 'YUVA-$year-%'");
        $count = mysqli_fetch_assoc($result)['count'] + 1;
        $regno = "YUVA-$year-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    } else {
        $regno = mysqli_real_escape_string($con, $_POST['regno']);
    }

    $query = "INSERT INTO registration (regno, name, father, mother, mob, email, dob, gender, qualification, address, state, dis, pincode, bid, mcategory, aadhar, status, date, callerid) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "sssssssssssssiisisi", 
        $regno, $name, $father, $mother, $mob, $email, $dob, $gender, $qualification, 
        $address, $state, $dis, $pincode, $bid, $mcategory, $aadhar, $status, $date, $admin_id
    );

    if (mysqli_stmt_execute($stmt)) {
        $student_id = mysqli_insert_id($con);
        logActivity('create_student', 'registration', $student_id, null, json_encode($_POST));
        header('Location: list.php?success=added');
        exit;
    } else {
        $error = 'Failed to add student: ' . mysqli_error($con);
    }
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
                <h1>Add New Student</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="list.php">Students</a></li>
                        <li class="breadcrumb-item active">Add Student</li>
                    </ol>
                </nav>
            </div>

            <div class="table-card">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Basic Information</h5>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Registration Number (Leave blank to auto-generate)</label>
                            <input type="text" name="regno" class="form-control" placeholder="Optional">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Student Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Father's Name</label>
                            <input type="text" name="father" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mother's Name</label>
                            <input type="text" name="mother" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Qualification</label>
                            <input type="text" name="qualification" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Aadhar Number</label>
                            <input type="text" name="aadhar" class="form-control">
                        </div>
                        
                        <div class="col-md-12 mt-4">
                            <h5 class="border-bottom pb-2">Contact Details</h5>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile Number *</label>
                            <input type="text" name="mob" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Full Address</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">District</label>
                            <input type="text" name="dis" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" value="Bihar">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pincode</label>
                            <input type="text" name="pincode" class="form-control">
                        </div>

                        <div class="col-md-12 mt-4">
                            <h5 class="border-bottom pb-2">Academic & Branch Distribution</h5>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch</label>
                            <select name="bid" class="form-select">
                                <option value="0">Select Branch</option>
                                <?php while($b = mysqli_fetch_assoc($branches)): ?>
                                    <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['bname']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category/School</label>
                            <select name="mcategory" class="form-select">
                                <option value="0">Select Category</option>
                                <?php while($c = mysqli_fetch_assoc($categories)): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" id="status" checked>
                                <label class="form-check-label" for="status">Active Student</label>
                            </div>
                        </div>

                        <div class="col-md-12 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Save Student
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
