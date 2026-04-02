<?php
session_start();

// Check if supervisor is logged in
if (!isset($_SESSION['supervisor_id'])) {
    header('Location: ../supervisor_login.php');
    exit;
}

require_once(__DIR__ . '/../connection.php');

$supervisor_id = (int) $_SESSION['supervisor_id'];
$supervisor_name = $_SESSION['supervisor_name'];
$supervisor_bids = $_SESSION['supervisor_bids'] ?? [];

$page_title = 'Add New Student';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $father = mysqli_real_escape_string($con, $_POST['father']);
    $mob = mysqli_real_escape_string($con, $_POST['mob']);
    $bid = (int) $_POST['bid'];
    $mcategory = (int) $_POST['mcategory'];
    $qualification = mysqli_real_escape_string($con, $_POST['qualification']);
    $village = mysqli_real_escape_string($con, $_POST['village']);

    // Security check: Ensure selected branch is assigned to supervisor
    if (!in_array($bid, $supervisor_bids)) {
        $error = "Unauthorized branch selection.";
    } elseif (strlen($mob) != 10) {
        $error = "Mobile number must be exactly 10 digits.";
    } else {
        // Generate Registration Number: YUVA-YYYY-XXXX
        $year = date('Y');
        $count_query = mysqli_query($con, "SELECT COUNT(*) as total FROM registration WHERE regno LIKE 'YUVA-$year-%'");
        $count_data = mysqli_fetch_assoc($count_query);
        $next_count = $count_data['total'] + 1;
        $regno = "YUVA-$year-" . str_pad($next_count, 4, '0', STR_PAD_LEFT);

        $date = date('d-m-Y');

        // Insert into registration
        $insert_query = "INSERT INTO registration (regno, name, father, mob, bid, mcategory, qualification, village, date, status, reg_status, submitted_by_supervisor) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0, 1)";
        $stmt = mysqli_prepare($con, $insert_query);
        mysqli_stmt_bind_param($stmt, "ssssiisss", $regno, $name, $father, $mob, $bid, $mcategory, $qualification, $village, $date);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_msg'] = "Student registered successfully! Reg No: $regno";
            header("Location: add_student.php");
            exit;
        } else {
            $error = "Error adding student: " . mysqli_error($con);
        }
    }
}

// Fetch assigned branches
$assigned_branches = [];
if (!empty($supervisor_bids)) {
    $bids_list = implode(',', array_map('intval', $supervisor_bids));
    $branches_query = mysqli_query($con, "SELECT id, bname, bcode FROM branch WHERE id IN ($bids_list) AND status = 1 ORDER BY bname");
    while ($row = mysqli_fetch_assoc($branches_query)) {
        $assigned_branches[] = $row;
    }
}

include('includes/header.php');
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Quick Student Entry</h1>
        <p class="page-subtitle">Register new students directly into your assigned branches.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4"
                style="background: rgba(239, 68, 68, 0.1); color: #b91c1c;">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4"
                style="background: rgba(16, 185, 129, 0.1); color: #065f46;">
                <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['success_msg']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5"
            style="background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px);">
            <div class="card-body p-4 p-md-5">
                <form method="POST" id="addStudentForm">
                    <div class="row g-4">
                        <!-- Branch & Category Row -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark"><i
                                    class="fas fa-building me-2 text-primary opacity-50"></i>Select Branch <span
                                    class="text-danger">*</span></label>
                            <select name="bid" id="branchSelect" class="form-select rounded-3 py-2" required>
                                <option value="">-- Select Branch --</option>
                                <?php foreach ($assigned_branches as $branch): ?>
                                    <option value="<?php echo $branch['id']; ?>">
                                        <?php echo htmlspecialchars($branch['bname']); ?> (<?php echo $branch['bcode']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark"><i
                                    class="fas fa-tags me-2 text-primary opacity-50"></i>Select Category <span
                                    class="text-danger">*</span></label>
                            <select name="mcategory" id="categorySelect" class="form-select rounded-3 py-2" required
                                disabled>
                                <option value="">-- Choose Branch First --</option>
                            </select>
                            <div id="catLoader" class="spinner-border spinner-border-sm text-primary mt-2 d-none"
                                role="status"></div>
                        </div>

                        <hr class="my-4 opacity-5">

                        <!-- Personal Details -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Student Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control rounded-3 py-2"
                                placeholder="Enter full name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Mobile Number <span
                                    class="text-danger">*</span></label>
                            <input type="tel" name="mob" id="mobInput" class="form-control rounded-3 py-2"
                                placeholder="10 digit mobile" pattern="[0-9]{10}" maxlength="10" required>
                            <div id="mobFeedback" class="small mt-1 fw-medium"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Father's Name</label>
                            <input type="text" name="father" class="form-control rounded-3 py-2"
                                placeholder="Enter father's name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Qualification</label>
                            <input type="text" name="qualification" class="form-control rounded-3 py-2"
                                placeholder="e.g. 10th, 12th, Graduate">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">Village/City</label>
                            <input type="text" name="village" class="form-control rounded-3 py-2"
                                placeholder="Enter village or city name">
                        </div>

                        <div class="col-12 mt-5">
                            <button type="submit" name="add_student"
                                class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm border-0 w-100"
                                style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);">
                                <i class="fas fa-user-plus me-2"></i> Register Student
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
    $(document).ready(function () {
        function checkMobile() {
            const mob = $('#mobInput').val();
            const bid = $('#branchSelect').val();
            const feedback = $('#mobFeedback');

            if (mob.length === 10 && bid) {
                feedback.html('<i class="fas fa-circle-notch fa-spin me-1"></i> Checking number...');
                feedback.removeClass('text-danger text-success').addClass('text-muted');

                $.ajax({
                    url: 'ajax_check_mobile.php',
                    type: 'GET',
                    data: { mob: mob, bid: bid },
                    dataType: 'json',
                    success: function (response) {
                        if (response.exists) {
                            feedback.html('<i class="fas fa-exclamation-triangle me-1"></i> ' + response.message);
                            feedback.removeClass('text-muted text-success').addClass('text-danger');
                            $('#mobInput').addClass('is-invalid');
                        } else {
                            feedback.html('<i class="fas fa-check-circle me-1"></i> Number Not available in this branch');
                            feedback.removeClass('text-muted text-danger').addClass('text-success');
                            $('#mobInput').removeClass('is-invalid');
                        }
                    },
                    error: function () {
                        feedback.html('');
                    }
                });
            } else {
                feedback.html('');
                $('#mobInput').removeClass('is-invalid');
            }
        }

        $('#mobInput').on('input', checkMobile);
        $('#branchSelect').on('change', checkMobile);

        $('#branchSelect').on('change', function () {
            const branchId = $(this).val();
            const catSelect = $('#categorySelect');
            const loader = $('#catLoader');

            if (branchId) {
                catSelect.prop('disabled', true);
                loader.removeClass('d-none');
                catSelect.html('<option value="">Loading categories...</option>');

                $.ajax({
                    url: 'ajax_get_categories.php',
                    type: 'GET',
                    data: { branch_id: branchId },
                    dataType: 'json',
                    success: function (response) {
                        loader.addClass('d-none');
                        catSelect.prop('disabled', false);
                        let options = '<option value="">-- Select Category --</option>';
                        if (response.length > 0) {
                            response.forEach(function (cat) {
                                options += `<option value="${cat.id}">${cat.name}</option>`;
                            });
                        } else {
                            options = '<option value="">No categories found</option>';
                        }
                        catSelect.html(options);
                    },
                    error: function () {
                        loader.addClass('d-none');
                        catSelect.html('<option value="">Error loading data</option>');
                    }
                });
            } else {
                catSelect.prop('disabled', true);
                catSelect.html('<option value="">-- Choose Branch First --</option>');
            }
        });
    });
</script>