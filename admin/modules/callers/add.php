<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Add Caller';
$page_type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'KYP';

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
    $svid = mysqli_real_escape_string($con, $_POST['svid']);
    $bid = mysqli_real_escape_string($con, $_POST['bid']);
    $earning_per_admission = isset($_POST['earning_per_admission']) ? (float)$_POST['earning_per_admission'] : 0.00;
    $caller_type = isset($_POST['caller_type']) ? mysqli_real_escape_string($con, $_POST['caller_type']) : 'KYP';
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
    $status = isset($_POST['status']) ? 1 : 0;
    $date = date('d-m-Y');

    // Get selected branches and categories for calling
    $calling_assignments = isset($_POST['calling_assignments']) ? $_POST['calling_assignments'] : [];

    // Insert caller
    $query = "INSERT INTO caller (svid, regno, name, father, mother, dob, age, doj, gender, email, mob, state, dis, pincode, category, marital_status, qualification, aadhar, othermob_no, pass, address, bid, earning_per_admission, caller_type, status, date) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "isssssissssssssssssssidsis", $svid, $regno, $name, $father, $mother, $dob, $age, $doj, $gender, $email, $mob, $state, $dis, $pincode, $category, $marital_status, $qualification, $aadhar, $othermob_no, $pass, $address, $bid, $earning_per_admission, $caller_type, $status, $date);

    if (mysqli_stmt_execute($stmt)) {
        $caller_id = mysqli_insert_id($con);

        // Insert branch assignments for calling
        if (!empty($calling_assignments)) {
            $branch_query = "INSERT INTO caller_branches (caller_id, branch_id, category_id, assigned_date, status) VALUES (?, ?, ?, ?, 1)";
            $branch_stmt = mysqli_prepare($con, $branch_query);
            $assigned_date = date('Y-m-d');

            foreach ($calling_assignments as $assignment) {
                $parts = explode('_', $assignment);
                if (count($parts) == 2) {
                    $branch_id = (int)$parts[0];
                    $category_id = (int)$parts[1];
                    mysqli_stmt_bind_param($branch_stmt, "iiis", $caller_id, $branch_id, $category_id, $assigned_date);
                    mysqli_stmt_execute($branch_stmt);
                }
            }
            mysqli_stmt_close($branch_stmt);
        }

        logActivity('create_caller', 'caller', $caller_id, null, json_encode($_POST));
        header('Location: list.php?success=added');
        exit;
    } else {
        $error = 'Failed to add caller: ' . mysqli_error($con);
    }
}

// Get supervisors for dropdown
$supervisors = [];
$result = mysqli_query($con, "SELECT id, name FROM supervisor WHERE status = 1");
while ($row = mysqli_fetch_assoc($result)) {
    $supervisors[] = $row;
}

// Get branches for dropdown
$branches = [];
$result = mysqli_query($con, "SELECT id, bname, bcode FROM branch WHERE status = 1 ORDER BY bname");
while ($row = mysqli_fetch_assoc($result)) {
    $branches[] = $row;
}

// Get all categories grouped by branch
$categories_by_branch = [];
$cat_result = mysqli_query($con, "SELECT id, name, bid FROM member_category WHERE status = 1 ORDER BY name ASC");
while ($row = mysqli_fetch_assoc($cat_result)) {
    $categories_by_branch[$row['bid']][] = $row;
}

// Get ALL active assignments for OTHER callers to highlight
$other_assignments = [];
$other_query = "SELECT cb.branch_id, cb.category_id, c.name as caller_name 
                FROM caller_branches cb 
                JOIN caller c ON cb.caller_id = c.id 
                WHERE cb.status = 1 AND c.caller_type = '$page_type'";
$other_result = mysqli_query($con, $other_query);
while ($row = mysqli_fetch_assoc($other_result)) {
    $other_assignments[$row['branch_id']][$row['category_id']][] = $row['caller_name'];
}

// Auto-generate registration number
$result = mysqli_query($con, "SELECT MAX(CAST(SUBSTRING(regno, 3) AS UNSIGNED)) as max_num FROM caller WHERE regno LIKE 'CC%'");
$row = mysqli_fetch_assoc($result);
$next_num = ($row['max_num'] ?? 0) + 1;
$auto_regno = 'CC' . str_pad($next_num, 4, '0', STR_PAD_LEFT);

include('../../includes/header.php');
?>

<style>
    .assignment-section {
        background: #fdfdfd;
        border-radius: 15px;
        padding: 20px;
        border: 1px solid #eee;
    }
    .assignment-card {
        border: 1px solid #eef0f5 !important;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .assignment-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .branch-title {
        color: #4361ee;
        font-size: 0.95rem;
        background: #f8faff;
        margin: -1rem;
        margin-bottom: 1rem;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f0f2f7;
    }
    .all-cat-wrapper {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 10px;
    }
    .category-scroll {
        max-height: 180px;
        overflow-y: auto;
        padding-right: 5px;
    }
    .category-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .category-scroll::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }
    .other-assigned {
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 10px;
        background: #fff5f5;
        border: 1px solid #ffe3e3;
        display: inline-block;
        margin-top: 2px;
    }
    .cat-item {
        padding: 4px 0;
        border-bottom: 1px dashed #f0f0f0;
    }
    .cat-item:last-child {
        border-bottom: none;
    }
</style>

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
                <h1>Add New Caller</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="list.php">Callers</a></li>
                        <li class="breadcrumb-item active">Add Caller</li>
                    </ol>
                </nav>
            </div>

            <div class="table-card">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="" id="callerForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Registration Number *</label>
                            <input type="text" name="regno" class="form-control" value="<?php echo $auto_regno; ?>"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Father's Name *</label>
                            <input type="text" name="father" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mother's Name</label>
                            <input type="text" name="mother" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth *</label>
                            <input type="date" name="dob" class="form-control" id="dob" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Age</label>
                            <input type="number" name="age" class="form-control" id="age" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender *</label>
                            <select name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile Number *</label>
                            <input type="text" name="mob" class="form-control" pattern="[0-9]{10}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alternate Mobile</label>
                            <input type="text" name="othermob_no" class="form-control" pattern="[0-9]{10}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Aadhar Number</label>
                            <input type="text" name="aadhar" class="form-control" pattern="[0-9]{12}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Qualification *</label>
                            <input type="text" name="qualification" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-control">
                                <option value="">Select Category</option>
                                <option value="GEN">General</option>
                                <option value="OBC">OBC</option>
                                <option value="SC">SC</option>
                                <option value="ST">ST</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marital Status</label>
                            <select name="marital_status" class="form-control">
                                <option value="">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address *</label>
                            <textarea name="address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State *</label>
                            <input type="text" name="state" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">District *</label>
                            <input type="text" name="dis" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pincode *</label>
                            <input type="text" name="pincode" class="form-control" pattern="[0-9]{6}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Joining *</label>
                            <input type="date" name="doj" class="form-control" value="<?php echo date('Y-m-d'); ?>"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supervisor *</label>
                            <select name="svid" class="form-control" required>
                                <option value="">Select Supervisor</option>
                                <?php foreach ($supervisors as $supervisor): ?>
                                    <option value="<?php echo $supervisor['id']; ?>">
                                        <?php echo htmlspecialchars($supervisor['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Primary Branch *</label>
                            <select name="bid" class="form-control" required>
                                <option value="">Select Branch</option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?php echo $branch['id']; ?>">
                                        <?php echo htmlspecialchars($branch['bcode'] . ' - ' . $branch['bname']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="assignment-section mb-4">
                                <h5 class="fw-bold mb-3 d-flex align-items-center">
                                    <i class="fas fa-shield-alt text-primary me-2"></i>
                                    Data Access Assignment (Branches & Categories)
                                </h5>
                                <p class="text-muted small mb-4">Select the branches and categories this caller is authorized to view and call.</p>
                                
                                <div class="assignment-grid">
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                        <?php foreach ($branches as $branch): ?>
                                            <div class="col">
                                                <div class="card h-100 assignment-card border-0 shadow-sm rounded-4">
                                                    <div class="card-body p-4">
                                                        <div class="branch-title fw-bold">
                                                            <i class="fas fa-building me-2"></i><?php echo htmlspecialchars($branch['bname']); ?>
                                                        </div>
                                                        
                                                        <div class="all-cat-wrapper mb-3">
                                                            <div class="form-check">
                                                                <?php 
                                                                    $others_all = $other_assignments[$branch['id']][0] ?? [];
                                                                ?>
                                                                <input type="checkbox" name="calling_assignments[]" value="<?php echo $branch['id']; ?>_0" class="form-check-input" id="branch_<?php echo $branch['id']; ?>_all" <?php echo !empty($others_all) ? 'disabled' : ''; ?>>
                                                                <label class="form-check-label fw-bold" for="branch_<?php echo $branch['id']; ?>_all">All Categories</label>
                                                                <?php if (!empty($others_all)): ?>
                                                                    <div class="other-assigned text-danger d-block">
                                                                        <i class="fas fa-user-check me-1"></i><?php echo htmlspecialchars(implode(', ', $others_all)); ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>

                                                        <div class="category-list-container">
                                                            <div class="category-scroll">
                                                                <?php if (isset($categories_by_branch[$branch['id']])): ?>
                                                                    <?php foreach ($categories_by_branch[$branch['id']] as $cat): ?>
                                                                        <div class="cat-item">
                                                                            <div class="form-check">
                                                                                <?php 
                                                                                    $others_cat = $other_assignments[$branch['id']][$cat['id']] ?? [];
                                                                                    // Disable if this category is assigned to someone else OR if 'All Categories' is assigned to someone else
                                                                                    $is_cat_disabled = !empty($others_cat) || !empty($others_all);
                                                                                ?>
                                                                                <input type="checkbox" name="calling_assignments[]" value="<?php echo $branch['id']; ?>_<?php echo $cat['id']; ?>" class="form-check-input branch-cat-<?php echo $branch['id']; ?>" id="cat_<?php echo $branch['id']; ?>_<?php echo $cat['id']; ?>" <?php echo $is_cat_disabled ? 'disabled' : ''; ?> data-assigned="<?php echo (!empty($others_cat) || !empty($others_all)) ? 'true' : 'false'; ?>">
                                                                                <label class="form-check-label" for="cat_<?php echo $branch['id']; ?>_<?php echo $cat['id']; ?>">
                                                                                    <?php echo htmlspecialchars($cat['name']); ?>
                                                                                </label>
                                                                                <?php if (!empty($others_cat)): ?>
                                                                                    <div class="other-assigned text-danger d-block">
                                                                                        <i class="fas fa-info-circle me-1"></i><?php echo htmlspecialchars(implode(', ', $others_cat)); ?>
                                                                                    </div>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                <?php else: ?>
                                                                    <small class="text-muted italic">No categories available.</small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Toggle category checkboxes when "All Categories" is checked
                            document.querySelectorAll('[id^="branch_"][id$="_all"]').forEach(function(allCheckbox) {
                                allCheckbox.addEventListener('change', function() {
                                    const branchId = this.id.split('_')[1];
                                    const catCheckboxes = document.querySelectorAll('.branch-cat-' + branchId);
                                    catCheckboxes.forEach(function(cb) {
                                        if (this.checked) {
                                            cb.disabled = true;
                                            cb.checked = false;
                                        } else {
                                            // Only re-enable if NOT assigned to someone else
                                            cb.disabled = cb.getAttribute('data-assigned') === 'true';
                                        }
                                    }.bind(this));
                                });
                            });
                        });
                        </script>
                        <div class="col-md-6">
                            <label class="form-label">Password *</label>
                            <input type="password" name="pass" class="form-control" id="password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control" id="confirm_password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Earning Per Admission (₹)</label>
                            <input type="number" step="0.01" name="earning_per_admission" class="form-control" value="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <?php $page_type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'KYP'; ?>
                            <input type="hidden" name="caller_type" value="<?php echo $page_type; ?>">
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" name="status" class="form-check-input" id="status" checked>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Caller
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

    // Password confirmation validation
    document.getElementById('callerForm').addEventListener('submit', function (e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }
    });
</script>

<?php include('../../includes/footer.php'); ?>
