<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Edit Manager';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Get manager data
$query = "SELECT * FROM manager WHERE id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$manager = mysqli_fetch_assoc($result);

if (!$manager) {
    header('Location: list.php?error=not_found');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $regno = mysqli_real_escape_string($con, $_POST['regno']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $father = mysqli_real_escape_string($con, $_POST['father']);
    $mother = mysqli_real_escape_string($con, $_POST['mother']);
    $dob = mysqli_real_escape_string($con, $_POST['dob']);
    $age = mysqli_real_escape_string($con, $_POST['age']);
    $doj = mysqli_real_escape_string($con, $_POST['doj']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $mob = mysqli_real_escape_string($con, $_POST['mob']);
    $state = mysqli_real_escape_string($con, $_POST['state']);
    $dis = mysqli_real_escape_string($con, $_POST['dis']);
    $pincode = mysqli_real_escape_string($con, $_POST['pincode']);
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $marital_status = mysqli_real_escape_string($con, $_POST['marital_status']);
    $qualification = mysqli_real_escape_string($con, $_POST['qualification']);
    $aadhar = mysqli_real_escape_string($con, $_POST['aadhar']);
    $othermob_no = mysqli_real_escape_string($con, $_POST['othermob_no']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $bid = mysqli_real_escape_string($con, $_POST['bid']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $status = isset($_POST['status']) ? 1 : 0;

    // Bank details
    $bank = mysqli_real_escape_string($con, $_POST['bank']);
    $bank_branch = mysqli_real_escape_string($con, $_POST['bank_branch']);
    $ifsccode = mysqli_real_escape_string($con, $_POST['ifsccode']);
    $accountno = mysqli_real_escape_string($con, $_POST['accountno']);

    // Update password only if provided
    if (!empty($_POST['pass'])) {
        $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
        $update_query = "UPDATE manager SET username=?, regno=?, name=?, father=?, mother=?, dob=?, age=?, doj=?, gender=?, email=?, mob=?, state=?, dis=?, pincode=?, category=?, marital_status=?, qualification=?, aadhar=?, othermob_no=?, pass=?, address=?, bank=?, bank_branch=?, ifsccode=?, accountno=?, bid=?, status=? WHERE id=?";
        $update_stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($update_stmt, "isssssissssssssssssssssssiii", $username, $regno, $name, $father, $mother, $dob, $age, $doj, $gender, $email, $mob, $state, $dis, $pincode, $category, $marital_status, $qualification, $aadhar, $othermob_no, $pass, $address, $bank, $bank_branch, $ifsccode, $accountno, $bid, $status, $id);
    } else {
        $update_query = "UPDATE manager SET username=?, regno=?, name=?, father=?, mother=?, dob=?, age=?, doj=?, gender=?, email=?, mob=?, state=?, dis=?, pincode=?, category=?, marital_status=?, qualification=?, aadhar=?, othermob_no=?, address=?, bank=?, bank_branch=?, ifsccode=?, accountno=?, bid=?, status=? WHERE id=?";
        $update_stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($update_stmt, "isssssisssssssssssssssssiii", $username, $regno, $name, $father, $mother, $dob, $age, $doj, $gender, $email, $mob, $state, $dis, $pincode, $category, $marital_status, $qualification, $aadhar, $othermob_no, $address, $bank, $bank_branch, $ifsccode, $accountno, $bid, $status, $id);
    }

    if (mysqli_stmt_execute($update_stmt)) {
        logActivity('update_manager', 'manager', $id, null, json_encode($_POST));
        header('Location: list.php?success=updated');
        exit;
    } else {
        $error = 'Failed to update manager: ' . mysqli_error($con);
    }
}

// Get branches for dropdown
$branches = [];
$result = mysqli_query($con, "SELECT id, bname, bcode FROM branch WHERE status = 1 ORDER BY bname");
while ($row = mysqli_fetch_assoc($result)) {
    $branches[] = $row;
}

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
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../../logout.php"><i
                                    class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <h1>Edit Manager</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="list.php">Managers</a></li>
                        <li class="breadcrumb-item active">Edit Manager</li>
                    </ol>
                </nav>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-card">
                <form method="POST" id="managerForm">
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col-md-12">
                            <h5 class="mb-3"><i class="fas fa-user me-2"></i>Personal Information</h5>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Registration Number *</label>
                            <input type="text" name="regno" class="form-control"
                                value="<?php echo htmlspecialchars($manager['regno']); ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control"
                                value="<?php echo htmlspecialchars($manager['name']); ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Father's Name *</label>
                            <input type="text" name="father" class="form-control"
                                value="<?php echo htmlspecialchars($manager['father']); ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mother's Name</label>
                            <input type="text" name="mother" class="form-control"
                                value="<?php echo htmlspecialchars($manager['mother']); ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date of Birth *</label>
                            <input type="date" name="dob" class="form-control" id="dob"
                                value="<?php echo htmlspecialchars($manager['dob']); ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Age *</label>
                            <input type="number" name="age" class="form-control" id="age"
                                value="<?php echo htmlspecialchars($manager['age']); ?>" readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date of Joining *</label>
                            <input type="date" name="doj" class="form-control"
                                value="<?php echo htmlspecialchars($manager['doj']); ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Gender *</label>
                            <select name="gender" class="form-select" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo $manager['gender'] == 'Male' ? 'selected' : ''; ?>>Male
                                </option>
                                <option value="Female" <?php echo $manager['gender'] == 'Female' ? 'selected' : ''; ?>>
                                    Female</option>
                                <option value="Other" <?php echo $manager['gender'] == 'Other' ? 'selected' : ''; ?>>Other
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Marital Status</label>
                            <select name="marital_status" class="form-select">
                                <option value="">Select Status</option>
                                <option value="Single" <?php echo $manager['marital_status'] == 'Single' ? 'selected' : ''; ?>>Single</option>
                                <option value="Married" <?php echo $manager['marital_status'] == 'Married' ? 'selected' : ''; ?>>Married</option>
                                <option value="Divorced" <?php echo $manager['marital_status'] == 'Divorced' ? 'selected' : ''; ?>>Divorced</option>
                                <option value="Widowed" <?php echo $manager['marital_status'] == 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">Select Category</option>
                                <option value="General" <?php echo $manager['category'] == 'General' ? 'selected' : ''; ?>>General</option>
                                <option value="OBC" <?php echo $manager['category'] == 'OBC' ? 'selected' : ''; ?>>OBC
                                </option>
                                <option value="SC" <?php echo $manager['category'] == 'SC' ? 'selected' : ''; ?>>SC
                                </option>
                                <option value="ST" <?php echo $manager['category'] == 'ST' ? 'selected' : ''; ?>>ST
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Qualification *</label>
                            <input type="text" name="qualification" class="form-control"
                                value="<?php echo htmlspecialchars($manager['qualification']); ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Aadhar Number</label>
                            <input type="text" name="aadhar" class="form-control" maxlength="12" pattern="[0-9]{12}"
                                value="<?php echo htmlspecialchars($manager['aadhar']); ?>">
                        </div>

                        <!-- Contact Information -->
                        <div class="col-md-12 mt-3">
                            <h5 class="mb-3"><i class="fas fa-phone me-2"></i>Contact Information</h5>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mobile Number *</label>
                            <input type="text" name="mob" class="form-control" maxlength="10" pattern="[0-9]{10}"
                                value="<?php echo htmlspecialchars($manager['mob']); ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Alternate Mobile</label>
                            <input type="text" name="othermob_no" class="form-control" maxlength="10"
                                pattern="[0-9]{10}" value="<?php echo htmlspecialchars($manager['othermob_no']); ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control"
                                value="<?php echo htmlspecialchars($manager['email']); ?>" required>
                        </div>

                        <!-- Address Information -->
                        <div class="col-md-12 mt-3">
                            <h5 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h5>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address *</label>
                            <textarea name="address" class="form-control" rows="2"
                                required><?php echo htmlspecialchars($manager['address']); ?></textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">State *</label>
                            <input type="text" name="state" class="form-control"
                                value="<?php echo htmlspecialchars($manager['state']); ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">District *</label>
                            <input type="text" name="dis" class="form-control"
                                value="<?php echo htmlspecialchars($manager['dis']); ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pincode</label>
                            <input type="text" name="pincode" class="form-control" maxlength="6" pattern="[0-9]{6}"
                                value="<?php echo htmlspecialchars($manager['pincode']); ?>">
                        </div>

                        <!-- Branch Assignment -->
                        <div class="col-md-12 mt-3">
                            <h5 class="mb-3"><i class="fas fa-building me-2"></i>Branch Assignment</h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assigned Branch *</label>
                            <select name="bid" class="form-select" required>
                                <option value="">Select Branch</option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?php echo $branch['id']; ?>" <?php echo $manager['bid'] == $branch['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($branch['bname']) . ' (' . htmlspecialchars($branch['bcode']) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Bank Details -->
                        <div class="col-md-12 mt-3">
                            <h5 class="mb-3"><i class="fas fa-university me-2"></i>Bank Details</h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank" class="form-control"
                                value="<?php echo htmlspecialchars($manager['bank']); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Branch Name</label>
                            <input type="text" name="bank_branch" class="form-control"
                                value="<?php echo htmlspecialchars($manager['bank_branch']); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" name="ifsccode" class="form-control" maxlength="11"
                                value="<?php echo htmlspecialchars($manager['ifsccode']); ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account Number</label>
                            <input type="text" name="accountno" class="form-control"
                                value="<?php echo htmlspecialchars($manager['accountno']); ?>">
                        </div>

                        <!-- Login Credentials -->
                        <div class="col-md-12 mt-3">
                            <h5 class="mb-3"><i class="fas fa-key me-2"></i>Login Credentials</h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username *</label>
                            <input type="text" name="username" class="form-control"
                                value="<?php echo htmlspecialchars($manager['username']); ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password <small class="text-muted">(leave blank to keep
                                    current)</small></label>
                            <input type="password" name="pass" class="form-control">
                        </div>

                        <!-- Status -->
                        <div class="col-md-12 mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" id="status" <?php echo $manager['status'] == 1 ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="status">
                                    Active Status
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="col-md-12 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Manager
                            </button>
                            <a href="list.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Calculate age from DOB
    document.getElementById('dob').addEventListener('change', function () {
        const dob = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        document.getElementById('age').value = age;
    });
</script>

<?php include('../../includes/footer.php'); ?>
