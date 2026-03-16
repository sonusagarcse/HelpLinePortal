<?php
require_once('../connection.php');
require_once('config/auth.php');

$page_title = 'Add Student';

// Handle branch selection update
if (isset($_POST['update_branch'])) {
    $_SESSION['sticky_bid'] = (int)$_POST['sticky_bid'];
    header('Location: add_student.php?branch_updated=1');
    exit;
}

// Initial sticky branch
if ($deo_bid > 0) {
    $sticky_bid = $deo_bid;
} else {
    $sticky_bid = $_SESSION['sticky_bid'] ?? 0;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $mob = mysqli_real_escape_string($con, $_POST['mob']);
    $bid = $sticky_bid;
    
    // Form values & Defaults
    $father = isset($_POST['father']) ? mysqli_real_escape_string($con, $_POST['father']) : '';
    $village = isset($_POST['village']) ? mysqli_real_escape_string($con, $_POST['village']) : '';
    $mother = ''; $email = ''; $dob = '0000-00-00';
    $gender = ''; $qualification = isset($_POST['qualification']) ? mysqli_real_escape_string($con, $_POST['qualification']) : ''; 
    $address = ''; $state = 'Bihar';
    $dis = ''; $pincode = ''; $mcategory = 0; $aadhar = '';
    $status = 1; $date = date('d-m-Y');
    
    // Registration Number
    $year = date('Y');
    $result = mysqli_query($con, "SELECT COUNT(*) as count FROM registration WHERE regno LIKE 'YUVA-$year-%'");
    $count = mysqli_fetch_assoc($result)['count'] + 1;
    $regno = "YUVA-$year-" . str_pad($count, 4, '0', STR_PAD_LEFT);

    $query = "INSERT INTO registration (regno, name, mob, bid, callerid, date, father, mother, email, dob, gender, qualification, address, village, state, dis, pincode, mcategory, aadhar, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, '', '', '0000-00-00', '', ?, '', ?, 'Bihar', '', '', 0, '', 1)";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "sssiissss", $regno, $name, $mob, $bid, $deo_id, $date, $father, $qualification, $village);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: add_student.php?success=1');
        exit;
    } else {
        $error = 'Error: ' . mysqli_error($con);
    }
}

// Branches
$branches = mysqli_query($con, "SELECT id, bname FROM branch WHERE status = 1 ORDER BY bname");

include('includes/header.php');
?>

<div class="wrapper d-flex">
    <?php include('includes/sidebar.php'); ?>

    <div id="content" class="flex-grow-1 p-3 p-md-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-0"><i class="fas fa-user-plus text-primary me-2 desktop-hide"></i>Student Quick Entry</h2>
                <p class="text-muted mb-0">Register a new student to the database</p>
            </div>
            <div>
                <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm border-0 w-100 w-md-auto">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <?php if ($deo_bid == 0): ?>
                <div class="card border-0 shadow-sm rounded-4 mb-4" style="border-left: 5px solid #198754 !important;">
                    <div class="card-body p-4">
                        <form method="POST">
                            <label class="form-label fw-bold"><i class="fas fa-building text-success me-2"></i>Active Target Branch</label>
                            <div class="input-group">
                                <select name="sticky_bid" class="form-select form-select-lg border-0 bg-light">
                                    <option value="0">General / No Branch</option>
                                    <?php mysqli_data_seek($branches, 0); while($b = mysqli_fetch_assoc($branches)): ?>
                                        <option value="<?php echo $b['id']; ?>" <?php echo $sticky_bid == $b['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($b['bname']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <button type="submit" name="update_branch" class="btn btn-success px-4 fw-bold">SET BRANCH</button>
                            </div>
                            <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> All subsequent student entries will be automatically linked to this selected branch.</small>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                    <div class="alert alert-info rounded-4 border-0 mb-4 shadow-sm d-flex align-items-center p-4">
                        <i class="fas fa-info-circle fa-2x me-3 text-info"></i> 
                        <div>
                            <h5 class="alert-heading mb-1 fw-bold">Branch Assigned</h5>
                            <span class="mb-0">All entries will be automatically linked to your permanently assigned branch.</span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0">Student Registration Form</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success rounded-4 border-0 mb-4 shadow-sm d-flex align-items-center p-3">
                                <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                                <div>
                                    <h6 class="alert-heading mb-0 fw-bold">Success!</h6>
                                    <span>Student registered successfully.</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="row g-4 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-secondary">Student Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border border-light">
                                        <span class="input-group-text bg-light border-0 text-muted px-4"><i class="fas fa-user"></i></span>
                                        <input type="text" name="name" class="form-control border-0 bg-light" placeholder="e.g. Rahul Kumar" required autofocus>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-secondary">Mobile Number <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border border-light">
                                        <span class="input-group-text bg-light border-0 text-muted px-4"><i class="fas fa-phone-alt"></i></span>
                                        <input type="tel" name="mob" class="form-control border-0 bg-light" placeholder="10-digit mobile number" pattern="[0-9]{10}" required>
                                    </div>
                                    <div class="form-text mt-2 ms-2 text-muted"><i class="fas fa-shield-alt me-1"></i> Ensure the mobile number is active and correct.</div>
                                </div>

                                <div class="col-md-6 mt-3 mt-md-4">
                                    <label class="form-label fw-semibold text-secondary">Father's Name (Optional)</label>
                                    <div class="input-group shadow-sm rounded-4 overflow-hidden border border-light">
                                        <span class="input-group-text bg-light border-0 text-muted px-4"><i class="fas fa-male"></i></span>
                                        <input type="text" name="father" class="form-control border-0 bg-light" placeholder="e.g. Shyam Kumar">
                                    </div>
                                </div>

                                <div class="col-md-6 mt-3 mt-md-4">
                                    <label class="form-label fw-semibold text-secondary">Village/Town (Optional)</label>
                                    <div class="input-group shadow-sm rounded-4 overflow-hidden border border-light">
                                        <span class="input-group-text bg-light border-0 text-muted px-4"><i class="fas fa-home"></i></span>
                                        <input type="text" name="village" class="form-control border-0 bg-light" placeholder="e.g. Rampur">
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3 mt-md-4">
                                    <label class="form-label fw-semibold text-secondary">Qualification (Optional)</label>
                                    <div class="input-group shadow-sm rounded-4 overflow-hidden border border-light">
                                        <span class="input-group-text bg-light border-0 text-muted px-4"><i class="fas fa-graduation-cap"></i></span>
                                        <input type="text" name="qualification" class="form-control border-0 bg-light" placeholder="e.g. 10th, 12th, Graduate">
                                    </div>
                                </div>
                            </div>

                            <hr class="text-muted opacity-10 my-4">

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-light btn-lg rounded-pill px-5 fw-bold me-md-2 mb-2 mb-md-0 shadow-sm">
                                    Clear
                                </button>
                                <button type="submit" name="add_student" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">
                                    <i class="fas fa-paper-plane me-2"></i> Register Student
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
