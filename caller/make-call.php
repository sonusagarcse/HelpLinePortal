<?php
session_start();

// Check if caller is logged in
if (!isset($_SESSION['caller_id'])) {
    $redirect_url = (isset($SITE_URL) ? $SITE_URL : '') . '/caller_login.php';
    header('Location: ' . $redirect_url);
    exit;
}

require_once(__DIR__ . '/../connection.php');

// Get student ID (could come as id or student_id depending on link origin)
$student_id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0);
$caller_id = $_SESSION['caller_id'];

// Handle Student Details Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_student_details') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $father = mysqli_real_escape_string($con, $_POST['father']);
    $mob = mysqli_real_escape_string($con, $_POST['mob']);
    $othermob = mysqli_real_escape_string($con, $_POST['othermob_no']);
    $dob = mysqli_real_escape_string($con, $_POST['dob']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $qualification = mysqli_real_escape_string($con, $_POST['qualification']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $village = mysqli_real_escape_string($con, $_POST['village']);
    $dis = mysqli_real_escape_string($con, $_POST['dis']);
    $state = mysqli_real_escape_string($con, $_POST['state']);
    $pincode = mysqli_real_escape_string($con, $_POST['pincode']);

    $update_sql = "UPDATE registration SET 
                    name = ?, father = ?, mob = ?, othermob_no = ?, 
                    dob = ?, gender = ?, qualification = ?, 
                    address = ?, village = ?, dis = ?, state = ?, pincode = ? 
                   WHERE id = ?";
    $update_stmt = mysqli_prepare($con, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ssssssssssssi",
        $name, $father, $mob, $othermob,
        $dob, $gender, $qualification,
        $address, $village, $dis, $state, $pincode,
        $student_id
    );

    if (mysqli_stmt_execute($update_stmt)) {
        $_SESSION['success_msg'] = "Student details updated successfully!";
    }
    else {
        $_SESSION['error_msg'] = "Error updating details: " . mysqli_error($con);
    }
    header("Location: make-call.php?id=$student_id");
    exit;
}

// Fetch registration details directly
$query = "SELECT r.*, mc.name as category_name 
          FROM registration r 
          LEFT JOIN member_category mc ON r.mcategory = mc.id 
          WHERE r.id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    header('Location: index.php?error=not_found');
    exit;
}
$data = $student; // To keep HTML variable compatibility

// Fetch global previous call info for this student (to see last schedule regardless of caller)
$prev_query = "SELECT nextdate, pdate, des FROM mquery WHERE studentid = ? ORDER BY id DESC LIMIT 1";
$prev_stmt = mysqli_prepare($con, $prev_query);
mysqli_stmt_bind_param($prev_stmt, "i", $student_id);
mysqli_stmt_execute($prev_stmt);
$prev_result = mysqli_stmt_get_result($prev_stmt);
$prev_call = mysqli_fetch_assoc($prev_result);

$data['nextdate'] = $prev_call['nextdate'] ?? 'None yet';
$data['pdate'] = (isset($prev_call['pdate']) && $prev_call['pdate'] != '') ? $prev_call['pdate'] : date('Y-m-d');

$data['des'] = $prev_call['des'] ?? '';
$data['remarks'] = $student['caller_remark'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $remarks = mysqli_real_escape_string($con, $_POST['remarks']);
    $next_date = mysqli_real_escape_string($con, $_POST['next_date']);
    $status = isset($_POST['status']) ? 0 : 1; // 0 = completed, 1 = pending
    $is_rejected = isset($_POST['reject_student']) ? true : false;

    if ($is_rejected) {
        $status = 2; // 2 = Rejected
        $next_date = '0000-00-00'; // Clear follow-up
    }

    // Insert into mquery table
    $insert_query = "INSERT INTO mquery (callerid, studentid, bid, des, pdate, nextdate, remarks, date, status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)";
    $insert_stmt = mysqli_prepare($con, $insert_query);
    $pdate = ($data['nextdate'] === 'None yet') ? date('Y-m-d') : $data['nextdate'];
    mysqli_stmt_bind_param(
        $insert_stmt,
        "iiissssi",
        $caller_id,
        $student_id,
        $student['bid'],
        $description,
        $pdate,
        $next_date,
        $remarks,
        $status
    );

    if (mysqli_stmt_execute($insert_stmt)) {
        // Update registration tracking caller_remark
        $reg_update_sql = "caller_remark = ?";
        if ($is_rejected) {
            $reg_update_sql .= ", status = 0";
        }
        $update_query = "UPDATE registration SET $reg_update_sql WHERE id = ?";
        $update_stmt = mysqli_prepare($con, $update_query);
        if ($is_rejected) {
            mysqli_stmt_bind_param($update_stmt, "si", $remarks, $student_id);
        }
        else {
            mysqli_stmt_bind_param($update_stmt, "si", $remarks, $student_id);
        }
        mysqli_stmt_execute($update_stmt);

        header('Location: index.php?success=call_logged' . ($is_rejected ? '&rejected=1' : ''));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Call - Caller Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .student-details-card {
            background: #f1f3f5;
            border-left: 5px solid #28a745;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 12px;
        }

        .detail-item {
            margin-bottom: 15px;
        }

        .detail-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 15px;
            }
            .btn-lg {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-headset me-2"></i>Caller Dashboard
            </a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="form-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0"><i class="fas fa-phone me-2"></i>Make Call - Record Details</h4>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editStudentModal">
                    <i class="fas fa-edit me-1"></i>Edit Student Details
                </button>
            </div>

            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success_msg'];
    unset($_SESSION['success_msg']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
endif; ?>

            <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $_SESSION['error_msg'];
    unset($_SESSION['error_msg']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
endif; ?>

            <?php if ($student): ?>
                <div class="student-details-card">
                    <h5 class="mb-3 text-success"><i class="fas fa-user-graduate me-2"></i>Student Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Full Name:</span>
                                <b class="fs-4 text-dark"><?php echo htmlspecialchars($student['name']); ?></b>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Father's Name:</span>
                                <?php echo htmlspecialchars($student['father']); ?>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Mobile Number:</span>
                                <div>
                                    <strong class="fs-4 text-dark"><?php echo htmlspecialchars($student['mob']); ?></strong>
                                    <div class="mt-3">
                                        <a href="tel:<?php echo htmlspecialchars($student['mob']); ?>"
                                            class="btn btn-success btn-lg w-100 py-3 rounded-pill shadow d-flex align-items-center justify-content-center">
                                            <i class="fas fa-phone-alt fa-shake me-3 fa-lg"></i>
                                            <span class="fw-bold">START CALL NOW</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($student['othermob_no'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Other Mobile:</span>
                                    <?php echo htmlspecialchars($student['othermob_no']); ?>
                                </div>
                            <?php
    endif; ?>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span class="detail-label">Reg Number:</span>
                                <?php echo htmlspecialchars($student['regno']); ?>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Date of Birth:</span>
                                <?php echo htmlspecialchars($student['dob']); ?>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Gender:</span>
                                <?php echo htmlspecialchars($student['gender']); ?>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Qualification:</span>
                                <span class="badge bg-warning text-dark fs-6 shadow-sm">
                                    <i class="fas fa-graduation-cap me-1"></i>
                                    <?php echo htmlspecialchars(!empty($student['qualification']) ? $student['qualification'] : 'Not specified'); ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="detail-item">
                                <span class="detail-label">Address:</span>
                                <?php
    $addr_parts = array_filter([
        $student['address'],
        $student['village'],
        $student['dis'],
        $student['state'],
        $student['pincode']
    ]);
    echo htmlspecialchars(implode(', ', $addr_parts));
?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
endif; ?>

            <div class="alert alert-info">
                <strong>Category:</strong> <?php echo htmlspecialchars($data['category_name'] ?? 'N/A'); ?><br>
                <strong>Previous Date:</strong> <?php echo htmlspecialchars($data['pdate'] ?? 'N/A'); ?><br>
                <strong>Next Date:</strong> <?php echo htmlspecialchars($data['nextdate'] ?? 'N/A'); ?>
            </div>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Call Description *</label>
                    <textarea name="description" class="form-control" rows="4" required
                        placeholder="Enter details about the call..."><?php echo htmlspecialchars($data['des'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="3"
                        placeholder="Any additional remarks..."><?php echo htmlspecialchars($data['remarks'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Next Follow-up Date</label>
                    <input type="date" name="next_date" class="form-control" 
                        value="<?php echo(isset($data['nextdate']) && $data['nextdate'] != 'None yet') ? htmlspecialchars($data['nextdate']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="status" class="form-check-input" id="completed">
                        <label class="form-check-label" for="completed">
                            Mark as Completed
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" name="reject_student" class="form-check-input" id="reject" onchange="toggleReject(this)">
                        <label class="form-check-label text-danger fw-bold" for="reject">
                            <i class="fas fa-user-times me-1"></i> Reject Student (Mark as Inactive)
                        </label>
                    </div>
                </div>

                <script>
                    function toggleReject(checkbox) {
                        const statusCheck = document.getElementById('completed');
                        const nextDateInput = document.querySelector('input[name="next_date"]');
                        if (checkbox.checked) {
                            statusCheck.checked = true;
                            statusCheck.disabled = true;
                            nextDateInput.required = false;
                            nextDateInput.disabled = true;
                            nextDateInput.value = '';
                        } else {
                            statusCheck.disabled = false;
                            nextDateInput.disabled = false;
                            nextDateInput.required = true;
                        }
                    }
                </script>

                <div class="d-grid gap-2 mb-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Save Call Record
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>

            <hr>

            <!-- Student History Section -->
            <div class="mt-4">
                <h5 class="mb-3"><i class="fas fa-history me-2"></i>Previous Call History</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-bordered">
                        <thead>
                            <tr class="table-dark">
                                <th>Date</th>
                                <th>Description</th>
                                <th>Remarks</th>
                                <th>Next Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$history_query = "SELECT * FROM mquery WHERE studentid = ? ORDER BY id DESC";
$h_stmt = mysqli_prepare($con, $history_query);
mysqli_stmt_bind_param($h_stmt, "i", $student_id);
mysqli_stmt_execute($h_stmt);
$h_result = mysqli_stmt_get_result($h_stmt);
$history_found = false;
while ($h = mysqli_fetch_assoc($h_result)) {
    $history_found = true;
?>
                                <tr>
                                    <td><?php echo date('d-M-Y H:i', strtotime($h['date'])); ?></td>
                                    <td><?php echo htmlspecialchars($h['des']); ?></td>
                                    <td><?php echo htmlspecialchars($h['remarks']); ?></td>
                                    <td><?php echo htmlspecialchars($h['nextdate'] ?: 'N/A'); ?></td>
                                    <td>
                                        <?php if ($h['status'] == 0): ?>
                                            <span class="badge bg-success">Completed</span>
                                        <?php
    else: ?>
                                            <span class="badge bg-warning">Follow-up</span>
                                        <?php
    endif; ?>
                                    </td>
                                </tr>
                                <?php
}
if (!$history_found) {
    echo '<tr><td colspan="5" class="text-center text-muted">No previous calls recorded.</td></tr>';
}
?>
                        </tbody>
                    </table>
                </div>
            </div>
    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_student_details">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStudentModalLabel"><i class="fas fa-user-edit me-2"></i>Edit Student Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Father's Name</label>
                                <input type="text" name="father" class="form-control" value="<?php echo htmlspecialchars($student['father']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile Number</label>
                                <input type="text" name="mob" class="form-control" value="<?php echo htmlspecialchars($student['mob']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Other Mobile</label>
                                <input type="text" name="othermob_no" class="form-control" value="<?php echo htmlspecialchars($student['othermob_no']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="<?php echo htmlspecialchars($student['dob']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="Male" <?php echo $student['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo $student['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo $student['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Qualification</label>
                                <input type="text" name="qualification" class="form-control" value="<?php echo htmlspecialchars($student['qualification']); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($student['address']); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Village/City</label>
                                <input type="text" name="village" class="form-control" value="<?php echo htmlspecialchars($student['village']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">District</label>
                                <input type="text" name="dis" class="form-control" value="<?php echo htmlspecialchars($student['dis']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($student['state']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pincode</label>
                                <input type="text" name="pincode" class="form-control" value="<?php echo htmlspecialchars($student['pincode']); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>