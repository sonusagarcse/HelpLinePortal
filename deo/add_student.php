<?php
require_once('../connection.php');
require_once('config/auth.php');

$page_title = 'Add Student';

// Branch selection logic
if ($active_bid == 0) {
    header('Location: manage_branch.php?error=no_active_branch');
    exit;
}
$sticky_bid = $active_bid;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $mob = mysqli_real_escape_string($con, $_POST['mob']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $bid = $sticky_bid;
    $mcategory = (int)$active_cid;
    
    // Form values & Defaults
    $father = isset($_POST['father']) ? mysqli_real_escape_string($con, $_POST['father']) : '';
    $qualification = isset($_POST['qualification']) ? mysqli_real_escape_string($con, $_POST['qualification']) : ''; 
    $village = isset($_POST['village']) ? mysqli_real_escape_string($con, $_POST['village']) : '';
    $status = 1; $date = date('d-m-Y');

    // Validation
    if (strlen($mob) != 10) {
        $error = 'Mobile number must be exactly 10 digits.';
    } else {
        // Registration Number
        $year = date('Y');
        $result = mysqli_query($con, "SELECT COUNT(*) as count FROM registration WHERE regno LIKE 'YUVA-$year-%'");
        $count = mysqli_fetch_assoc($result)['count'] + 1;
        $regno = "YUVA-$year-" . str_pad($count, 4, '0', STR_PAD_LEFT);

        $query = "INSERT INTO registration (regno, name, mob, bid, callerid, date, father, mother, email, dob, gender, qualification, address, village, state, dis, pincode, mcategory, aadhar, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, '', '', '0000-00-00', '', ?, '', ?, 'Bihar', '', '', ?, '', 1)";
        
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "sssiissssi", $regno, $name, $mob, $bid, $deo_id, $date, $father, $qualification, $village, $mcategory);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: add_student.php?success=1');
            exit;
        } else {
            $error = 'Error: ' . mysqli_error($con);
        }
    }
}

// Branches
$branches = mysqli_query($con, "SELECT id, bname FROM branch WHERE status = 1 ORDER BY bname");

// Check if category is selected
if ($active_cid == 0) {
    header('Location: manage_branch.php?error=no_active_category');
    exit;
}

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
                
                <div class="alert bg-white rounded-4 border-0 mb-4 shadow-sm d-flex align-items-center p-4">
                    <div class="row w-100 g-0">
                        <div class="col-md-12 d-flex align-items-center ps-md-4">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="fas fa-tags text-success fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Active Category: <?php echo htmlspecialchars($active_cname); ?></h6>
                                <span class="text-muted small"><a href="manage_branch.php" class="text-decoration-none">Change Category</a></span>
                            </div>
                        </div>
                    </div>
                </div>

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
                                    <label class="form-label fw-semibold text-secondary">Mobile Number <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border border-light">
                                        <span class="input-group-text bg-light border-0 text-muted px-4"><i class="fas fa-phone-alt"></i></span>
                                        <input type="tel" name="mob" id="student_mobile" class="form-control border-0 bg-light" placeholder="10-digit mobile number" pattern="[0-9]{10}" required autofocus autocomplete="off">
                                    </div>
                                    <div id="mobile_alert" class="mt-2" style="display: none;"></div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-secondary">Student Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden border border-light">
                                        <span class="input-group-text bg-light border-0 text-muted px-4"><i class="fas fa-user"></i></span>
                                        <input type="text" name="name" id="student_name" class="form-control border-0 bg-light" placeholder="e.g. Rahul Kumar" required>
                                    </div>
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
                                    <div class="input-group shadow-sm rounded-4 overflow-hidden border border-light" style="position: relative;">
                                        <span class="input-group-text bg-light border-0 text-muted px-4"><i class="fas fa-home"></i></span>
                                        <input type="text" name="village" id="village_input" class="form-control border-0 bg-light" placeholder="e.g. Rampur" autocomplete="off">
                                        <div id="village_suggestions" class="list-group shadow-sm" style="position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; display: none; border-radius: 0 0 15px 15px; overflow: hidden;"></div>
                                    </div>
                                </div>

                                <div class="col-md-6 mt-3 mt-md-4">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Mobile Check
    $('#student_mobile').on('keyup mouseup change click', function() {
        let mobile = $(this).val();
        
        // Remove non-digits
        mobile = mobile.replace(/\D/g, '');
        $(this).val(mobile);

        if (mobile.length === 10) {
            $(this).removeClass('is-invalid').addClass('is-valid');
            $.getJSON('ajax_handler.php?action=check_mobile&mobile=' + mobile, function(data) {
                if (data.exists) {
                    $('#mobile_alert').html(
                        '<div class="alert alert-warning py-2 mb-0 d-flex align-items-center">' +
                        '<i class="fas fa-exclamation-triangle me-2"></i>' +
                        '<span>Already exists: <strong>' + data.student.name + '</strong> (' + data.student.regno + ')</span>' +
                        '</div>'
                    ).fadeIn();
                } else {
                    $('#mobile_alert').fadeOut();
                }
            });
        } else if (mobile.length > 0) {
            $(this).addClass('is-invalid').removeClass('is-valid');
            $('#mobile_alert').html(
                '<div class="alert alert-danger py-2 mb-0 d-flex align-items-center">' +
                '<i class="fas fa-exclamation-circle me-2"></i>' +
                '<span>Please enter exactly 10 digits. Current: <strong>' + mobile.length + '</strong></span>' +
                '</div>'
            ).fadeIn();
        } else {
            $(this).removeClass('is-invalid').removeClass('is-valid');
            $('#mobile_alert').fadeOut();
        }
    });

    // Handle form submission validation for 10 digits
    $('form').on('submit', function(e) {
        let mobile = $('#student_mobile').val();
        if (mobile.length !== 10) {
            e.preventDefault();
            alert('Mobile number must be exactly 10 digits.');
            $('#student_mobile').focus();
        }
    });

    // Village Autocomplete
    $('#village_input').on('keyup', function() {
        let term = $(this).val();
        if (term.length >= 2) {
            $.getJSON('ajax_handler.php?action=suggest_village&term=' + term, function(data) {
                if (data.length > 0) {
                    let html = '';
                    data.forEach(function(v) {
                        html += '<button type="button" class="list-group-item list-group-item-action py-2 village-opt">' + v + '</button>';
                    });
                    $('#village_suggestions').html(html).show();
                } else {
                    $('#village_suggestions').hide();
                }
            });
        } else {
            $('#village_suggestions').hide();
        }
    });

    $(document).on('click', '.village-opt', function() {
        $('#village_input').val($(this).text());
        $('#village_suggestions').hide();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#village_input').length && !$(e.target).closest('#village_suggestions').length) {
            $('#village_suggestions').hide();
        }
    });
});
</script>
