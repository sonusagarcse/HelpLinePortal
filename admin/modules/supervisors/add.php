<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Add Supervisor';

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
    // Removed single bid, getting array of bids
    $bids = isset($_POST['bids']) && is_array($_POST['bids']) ? $_POST['bids'] : [];
    $mnid = (int)$_POST['mnid']; // Manager ID
    $assigned_coordinator_id = (int)$_POST['assigned_coordinator_id']; // Coordinator ID
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

    // Bank details
    $bank = mysqli_real_escape_string($con, $_POST['bank']);
    $bank_branch = mysqli_real_escape_string($con, $_POST['bank_branch']);
    $ifsccode = mysqli_real_escape_string($con, $_POST['ifsccode']);
    $accountno = mysqli_real_escape_string($con, $_POST['accountno']);

    $status = isset($_POST['status']) ? 1 : 0;
    $date = date('Y-m-d');
    $asession = date('Y');

    // Check for duplicate registration number
    $check_query = "SELECT id FROM supervisor WHERE regno = ?";
    $check_stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($check_stmt, "s", $regno);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) > 0) {
        $error = 'Registration number already exists!';
    } else {
        // Insert supervisor (setting legacy bid to 0)
        $legacy_bid = 0;
        $query = "INSERT INTO supervisor (mnid, assigned_coordinator_id, username, regno, name, father, mother, asession, dob, age, doj, gender, email, mob, state, dis, pincode, category, marital_status, qualification, aadhar, othermob_no, pass, address, bank, bank_branch, ifsccode, accountno, bid, status, date, reg_type) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param(
            $stmt,
            "iiissssisissssssssssssssssssiis",
            $mnid,
            $assigned_coordinator_id,
            $username,
            $regno,
            $name,
            $father,
            $mother,
            $asession,
            $dob,
            $age,
            $doj,
            $gender,
            $email,
            $mob,
            $state,
            $dis,
            $pincode,
            $category,
            $marital_status,
            $qualification,
            $aadhar,
            $othermob_no,
            $pass,
            $address,
            $bank,
            $bank_branch,
            $ifsccode,
            $accountno,
            $legacy_bid,
            $status,
            $date
        );

        if (mysqli_stmt_execute($stmt)) {
            $supervisor_id = mysqli_insert_id($con);
            
            // Insert multiple branches into supervisor_branches table
            if (!empty($bids)) {
                $b_query = "INSERT INTO supervisor_branches (supervisor_id, branch_id, assigned_date, status) VALUES (?, ?, ?, 1)";
                $b_stmt = mysqli_prepare($con, $b_query);
                foreach ($bids as $branch_id) {
                    $branch_id = (int)$branch_id;
                    mysqli_stmt_bind_param($b_stmt, "iis", $supervisor_id, $branch_id, $date);
                    mysqli_stmt_execute($b_stmt);
                }
            }
            
            logActivity('create_supervisor', 'supervisor', $supervisor_id, null, json_encode($_POST));
            header('Location: list.php?success=added');
            exit;
        } else {
            $error = 'Failed to add supervisor: ' . mysqli_error($con);
        }
    }
}

// Get managers for dropdown
$managers = [];
$result = mysqli_query($con, "SELECT id, name, regno FROM manager WHERE status = 1 ORDER BY name");
while ($row = mysqli_fetch_assoc($result)) {
    $managers[] = $row;
}

// Get branches for dropdown
$branches = [];
$result = mysqli_query($con, "SELECT id, bname, bcode FROM branch WHERE status = 1 ORDER BY bname");
while ($row = mysqli_fetch_assoc($result)) {
    $branches[] = $row;
}

// Get coordinators for dropdown
$coordinators = [];
$result = mysqli_query($con, "SELECT id, name, username FROM centre_coordinator WHERE status = 1 ORDER BY name");
while ($row = mysqli_fetch_assoc($result)) {
    $coordinators[] = $row;
}

// Auto-generate registration number
$result = mysqli_query($con, "SELECT MAX(CAST(SUBSTRING(regno, 3) AS UNSIGNED)) as max_num FROM supervisor WHERE regno LIKE 'SV%'");
$row = mysqli_fetch_assoc($result);
$next_num = ($row['max_num'] ?? 0) + 1;
$auto_regno = 'SV' . str_pad($next_num, 4, '0', STR_PAD_LEFT);

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
                <h1>Add New Supervisor</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="list.php">Supervisors</a></li>
                        <li class="breadcrumb-item active">Add Supervisor</li>
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
                <form method="POST" id="supervisorForm">
                    <div class="row">
                        <!-- Manager Assignment -->
                        <div class="col-md-12">
                            <h5 class="mb-3"><i class="fas fa-user-tie me-2"></i>Manager Assignment</h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assigned Manager *</label>
                            <select name="mnid" class="form-select" required>
                                <option value="">Select Manager</option>
                                <?php foreach ($managers as $manager): ?>
                                    <option value="<?php echo $manager['id']; ?>">
                                        <?php echo htmlspecialchars($manager['name']) . ' (' . htmlspecialchars($manager['regno']) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">This supervisor will report to the selected manager</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-primary"><i class="fas fa-network-wired me-1"></i>Assigned Centre Coordinator *</label>
                            <select name="assigned_coordinator_id" class="form-select border-primary" required>
                                <option value="">Select Coordinator</option>
                                <?php foreach ($coordinators as $coord): ?>
                                    <option value="<?php echo $coord['id']; ?>">
                                        <?php echo htmlspecialchars($coord['name']) . ' (' . htmlspecialchars($coord['username']) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Credentials created by this supervisor will auto-route to this coordinator.</small>
                        </div>

                        <!-- Personal Information -->
                        <div class="col-md-12 mt-3">
                            <h5 class="mb-3"><i class="fas fa-user me-2"></i>Personal Information</h5>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Registration Number *</label>
                            <input type="text" name="regno" class="form-control" value="<?php echo $auto_regno; ?>"
                                required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Father's Name *</label>
                            <input type="text" name="father" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mother's Name</label>
                            <input type="text" name="mother" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date of Birth *</label>
                            <input type="date" name="dob" class="form-control" id="dob" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Age *</label>
                            <input type="number" name="age" class="form-control" id="age" readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date of Joining *</label>
                            <input type="date" name="doj" class="form-control" value="<?php echo date('Y-m-d'); ?>"
                                required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Gender *</label>
                            <select name="gender" class="form-select" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Marital Status</label>
                            <select name="marital_status" class="form-select">
                                <option value="">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">Select Category</option>
                                <option value="General">General</option>
                                <option value="OBC">OBC</option>
                                <option value="SC">SC</option>
                                <option value="ST">ST</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Qualification *</label>
                            <input type="text" name="qualification" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Aadhar Number</label>
                            <input type="text" name="aadhar" class="form-control" maxlength="12" pattern="[0-9]{12}">
                        </div>

                        <!-- Contact Information -->
                        <div class="col-md-12 mt-3">
                            <h5 class="mb-3"><i class="fas fa-phone me-2"></i>Contact Information</h5>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mobile Number *</label>
                            <input type="text" name="mob" class="form-control" maxlength="10" pattern="[0-9]{10}"
                                required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Alternate Mobile</label>
                            <input type="text" name="othermob_no" class="form-control" maxlength="10"
                                pattern="[0-9]{10}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <!-- Address Information -->
                        <div class="col-md-12 mt-3">
                            <h5 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h5>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address *</label>
                            <textarea name="address" class="form-control" rows="2" required></textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">State *</label>
                            <input type="text" name="state" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">District *</label>
                            <input type="text" name="dis" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pincode</label>
                            <input type="text" name="pincode" class="form-control" maxlength="6" pattern="[0-9]{6}">
                        </div>

                        <!-- Branch Assignment -->
                        <div class="col-md-12 mt-3">
                            <h5 class="mb-3"><i class="fas fa-building me-2"></i>Branch Assignment</h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Assigned Branches *</label>
                            <select name="bids[]" class="form-select" multiple required style="height: 120px;">
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?php echo $branch['id']; ?>">
                                        <?php echo htmlspecialchars($branch['bname']) . ' (' . htmlspecialchars($branch['bcode']) . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Hold CTRL (or CMD on Mac) to select multiple branches.</small>
                        </div>

                        <!-- Bank Details -->
                        <div class="col-md-12 mt-3">
                            <h5 class="mb-3"><i class="fas fa-university me-2"></i>Bank Details</h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Branch Name</label>
                            <input type="text" name="bank_branch" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" name="ifsccode" class="form-control" maxlength="11">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account Number</label>
                            <input type="text" name="accountno" class="form-control">
                        </div>

                        <!-- Login Credentials -->
                        <div class="col-md-12 mt-3">
                            <h5 class="mb-3"><i class="fas fa-key me-2"></i>Login Credentials</h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username *</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" name="pass" class="form-control" required>
                        </div>

                        <!-- Status -->
                        <div class="col-md-12 mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" id="status" checked>
                                <label class="form-check-label" for="status">
                                    Active Status
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="col-md-12 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Supervisor
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
