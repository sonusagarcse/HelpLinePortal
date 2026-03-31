<?php
require_once(__DIR__ . '/../connection.php');
session_start();

// Check if caller is logged in
if (!isset($_SESSION['caller_id'])) {
    $redirect_url = (isset($SITE_URL) ? $SITE_URL : '') . '/caller_login.php';
    header('Location: ' . $redirect_url);
    exit;
}

// Get student ID (could come as id or student_id depending on link origin)
$student_id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0);
$caller_id = $_SESSION['caller_id'];

// Security Check: Verify student is assigned to this caller (Branch/Category Match)
$verify_query = "SELECT r.id FROM registration r 
                 WHERE r.id = $student_id AND (
                    EXISTS (SELECT 1 FROM caller_branches cb WHERE cb.caller_id = $caller_id AND cb.status = 1 AND cb.branch_id = r.bid AND (cb.category_id = 0 OR cb.category_id = r.mcategory))
                    OR r.assigned_caller = $caller_id
                 ) AND r.statusValue = 1"; // Wait, it's status in registration
// Actually let's use the exact names from index.php
$verify_query = "SELECT r.id FROM registration r 
                 WHERE r.id = $student_id AND (
                    EXISTS (SELECT 1 FROM caller_branches cb WHERE cb.caller_id = $caller_id AND cb.status = 1 AND cb.branch_id = r.bid AND (cb.category_id = 0 OR cb.category_id = r.mcategory))
                    OR r.assigned_caller = $caller_id
                 ) AND r.status = 1";

$verify_res = mysqli_query($con, $verify_query);
if (mysqli_num_rows($verify_res) == 0) {
    header('Location: index.php?error=unauthorized');
    exit;
}

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
    $status = (isset($_POST['status']) || isset($_POST['reg_ready'])) ? 0 : 1; // 0 = completed, 1 = pending
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
        // Update registration tracking caller_remark and assignment
        $reg_update_sql = "caller_remark = ?, assigned_caller = ?";
        $params = [$remarks, $caller_id];
        $types = "si";

        // Handle Status and Special Workflow
        if (isset($_POST['reg_ready'])) {
            $reg_update_sql .= ", reg_status = 1";
        }

        // If status is 0 (Resolved) and not rejected, it's pending coordinator approval
        if ($status == 0 && !$is_rejected) {
            $reg_update_sql .= ", coordinator_approval_status = 1";
        }

        if ($is_rejected) {
            $reg_update_sql .= ", status = 0";
        }
        
        $reg_update_sql .= " WHERE id = ?";
        $params[] = $student_id;
        $types .= "i";

        $update_query = "UPDATE registration SET $reg_update_sql";
        $update_stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($update_stmt, $types, ...$params);

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            --secondary-gradient: linear-gradient(135deg, #f43f5e 0%, #fb7185 100%);
            --accent-gradient: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.4);
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --header-bg: #0f172a;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        body {
            background-color: #f1f5f9;
            background-image: radial-gradient(#6366f1 0.5px, #f1f5f9 0.5px);
            background-size: 24px 24px;
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        .ambient-blob {
            position: fixed;
            width: 40vmax;
            height: 40vmax;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(139, 92, 246, 0.05) 100%);
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
            animation: float 20s infinite alternate linear;
        }

        @keyframes float {
            0% { transform: translate(-10%, -10%) rotate(0deg); }
            100% { transform: translate(20%, 30%) rotate(360deg); }
        }

        h1, h2, h3, h4, h5, .navbar-brand {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
        }

        .navbar {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 1rem 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .premium-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }

        .info-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
            display: block;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .call-action-btn {
            height: 4rem;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
        }

        .call-action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.4);
        }

        .wa-btn {
            background: #25D366;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(37, 211, 102, 0.3);
        }

        /* Timeline Styling */
        .timeline {
            position: relative;
            padding-left: 3rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 1rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2.5rem;
        }

        .timeline-marker {
            position: absolute;
            left: -2.5rem;
            width: 1.2rem;
            height: 1.2rem;
            border-radius: 50%;
            background: #6366f1;
            border: 3px solid white;
            box-shadow: 0 0 0 3px #e2e8f0;
            z-index: 1;
        }

        .timeline-content {
            background: #f8fafc;
            padding: 1.25rem;
            border-radius: 1rem;
            border: 1px solid #f1f5f9;
        }

        .btn-premium {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 1rem;
            padding: 1rem;
            font-weight: 700;
            transition: all 0.2s;
        }

        .btn-premium:hover {
            transform: scale(1.02);
            color: white;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }
    </style>
</head>

    <div class="ambient-blob"></div>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <div class="bg-primary bg-gradient rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="fas fa-headset text-white"></i>
                </div>
                <span class="fw-bold">Caller <span class="text-primary-emphasis">Portal</span></span>
            </a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-4 fw-bold">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-lg-5 mt-5">
        <div class="row g-4">
            <!-- Left Column: Student Details & History -->
            <div class="col-lg-7">
                <div class="premium-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0"><i class="fas fa-user-circle text-primary me-3"></i>Student Profile</h4>
                        <button type="button" class="btn btn-primary-subtle border border-primary-subtle text-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#editStudentModal">
                            <i class="fas fa-pen-nib me-2"></i>Edit Details
                        </button>
                    </div>

                    <?php
$addr_parts = [];
if ($student):
    // Define address parts for the redesign
    $addr_parts = array_filter([
        $student['address'] ?? '',
        $student['village'] ?? '',
        $student['dis'] ?? '',
        $student['state'] ?? '',
        $student['pincode'] ?? ''
    ]);

    // Fetch available WhatsApp templates
    $templates_query = mysqli_query($con, "SELECT * FROM whatsapp_templates WHERE status = 1 ORDER BY title ASC");
    $wa_templates = [];
    while ($row = mysqli_fetch_assoc($templates_query)) {
        $wa_templates[] = $row;
    }
endif;

if ($student):
?>
                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="bg-primary bg-opacity-10 p-4 rounded-4 border border-primary border-opacity-10 mb-2 text-center">
                                    <h2 class="h1 fw-bold mb-1"><?php echo htmlspecialchars($student['name']); ?></h2>
                                    <p class="text-muted mb-0 uppercase fw-bold small letter-spacing-1">REG: <?php echo htmlspecialchars($student['regno']); ?></p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <span class="info-label"><i class="fas fa-phone-alt me-2"></i>Direct Line</span>
                                <div class="info-value mb-4"><?php echo htmlspecialchars($student['mob']); ?></div>
                                
                                <span class="info-label"><i class="fas fa-id-card-alt me-2"></i>Father's Name</span>
                                <div class="info-value mb-1"><?php echo htmlspecialchars($student['father']); ?></div>
                            </div>

                            <div class="col-md-6">
                                <span class="info-label"><i class="fas fa-graduation-cap me-2"></i>Qualification</span>
                                <div class="info-value mb-4">
                                    <span class="badge bg-indigo text-white fw-bold px-3 py-2 rounded-pill" style="background: #6366f1;">
                                        <?php echo htmlspecialchars(!empty($student['qualification']) ? $student['qualification'] : 'In Progress'); ?>
                                    </span>
                                </div>
                                
                                <span class="info-label"><i class="fas fa-birthday-cake me-2"></i>Date of Birth</span>
                                <div class="info-value mb-1"><?php echo htmlspecialchars($student['dob']); ?></div>
                            </div>

                            <div class="col-12">
                                <div class="p-3 rounded-4" style="background: #f8fafc; border: 1px dashed #e2e8f0;">
                                    <span class="info-label"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Full Residence Address</span>
                                    <div class="small fw-semibold mt-1">
                                        <?php echo htmlspecialchars(implode(', ', (array)$addr_parts)); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <a href="tel:<?php echo htmlspecialchars($student['mob']); ?>"
                                            class="btn btn-success btn-lg w-100 call-action-btn shadow">
                                            <i class="fas fa-phone-alt fa-shake me-3"></i>START CALL SESSION
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn wa-btn btn-lg w-100 call-action-btn shadow" data-bs-toggle="modal" data-bs-target="#waTemplateModal">
                                            <i class="fab fa-whatsapp me-3"></i>CHAT
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
endif; ?>
                </div>

                <div class="premium-card">
                    <div class="d-flex align-items-center mb-4">
                         <h4 class="mb-0"><i class="fas fa-history text-primary me-3"></i>Call Timeline</h4>
                    </div>
                   
                    <div class="timeline">
                        <?php
$history_query = "SELECT * FROM mquery WHERE studentid = ? ORDER BY id DESC";
$h_stmt = mysqli_prepare($con, $history_query);
mysqli_stmt_bind_param($h_stmt, "i", $student_id);
mysqli_stmt_execute($h_stmt);
$h_result = mysqli_stmt_get_result($h_stmt);
$history_found = false;
while ($h = mysqli_fetch_assoc($h_result)):
    $history_found = true;
?>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-white text-dark shadow-sm border px-3 py-2 rounded-pill small fw-bold">
                                            <i class="far fa-clock me-2 text-primary"></i><?php echo date('d M Y | h:i A', strtotime($h['date'])); ?>
                                        </span>
                                        <?php if ($h['status'] == 0): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Completed</span>
                                        <?php
    else: ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Follow-up</span>
                                        <?php
    endif; ?>
                                    </div>
                                    <p class="mb-2 fw-semibold text-dark"><?php echo htmlspecialchars($h['des']); ?></p>
                                    <div class="pt-2 mt-2 border-top small text-muted">
                                        <i class="far fa-comment-dots me-2"></i><strong>Remark:</strong> <?php echo htmlspecialchars($h['remarks']); ?>
                                        <?php if ($h['nextdate']): ?>
                                            <span class="ms-3"><i class="far fa-calendar-alt me-2"></i><strong>Next:</strong> <?php echo $h['nextdate']; ?></span>
                                        <?php
    endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php
endwhile; ?>
                        <?php if (!$history_found): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-comment-slash fa-3x mb-3 opacity-25"></i>
                                <p>No previous call interactions recorded for this student.</p>
                            </div>
                        <?php
endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: record New Call -->
            <div class="col-lg-5">
                <div class="premium-card sticky-top" style="top: 100px;">
                    <h4 class="mb-4"><i class="fas fa-file-signature text-primary me-3"></i>New Interaction</h4>
                    
                    <div class="d-flex gap-3 mb-4 bg-light p-3 rounded-4 border">
                        <div class="flex-fill">
                            <span class="info-label">Category</span>
                            <span class="fw-bold text-primary small"><?php echo htmlspecialchars($data['category_name'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="vr text-muted opacity-25"></div>
                        <div class="flex-fill text-center">
                            <span class="info-label text-center">Last Contact</span>
                            <span class="fw-bold small"><?php echo htmlspecialchars($data['pdate'] ?? 'Never'); ?></span>
                        </div>
                    </div>

                    <form method="POST" action="">
                        <div class="mb-4">
                            <label class="info-label"><i class="fas fa-comment-dots me-2"></i>Response Description *</label>
                            <textarea name="description" class="form-control rounded-4 p-3" rows="5" required
                                placeholder="What was the student's response?"><?php echo htmlspecialchars($data['des'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="info-label"><i class="fas fa-sticky-note me-2"></i>Administrative Remarks</label>
                            <textarea name="remarks" class="form-control rounded-4 p-3" rows="2"
                                placeholder="Internal internal notes..."><?php echo htmlspecialchars($data['remarks'] ?? ''); ?></textarea>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="info-label"><i class="fas fa-calendar-plus me-2"></i>Schedule next Follow-up</label>
                                <input type="date" name="next_date" class="form-control rounded-4 p-3 shadow-sm border-primary border-opacity-25" 
                                    style="background: #fffcf0;"
                                    value="<?php echo(isset($data['nextdate']) && $data['nextdate'] != 'None yet') ? htmlspecialchars($data['nextdate']) : ''; ?>">
                            </div>
                        </div>

                        <div class="bg-light p-4 rounded-4 mb-4 border border-white">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-0 fw-bold">Workflow Actions</h6>
                                    <small class="text-muted">Finalize student status</small>
                                </div>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" name="reg_ready" id="reg_ready">
                                <label class="form-check-label fw-semibold text-primary" for="reg_ready">
                                    <i class="fas fa-id-card me-1"></i> Ready for Registration
                                </label>
                                <div class="small text-muted ms-1" style="font-size: 0.7rem;">Sends to Supervisor for credentials</div>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" name="status" id="completed">
                                <label class="form-check-label fw-semibold" for="completed">Admission Confirmed</label>
                            </div>

                            <hr class="my-3 opacity-10">

                            <div class="form-check">
                                <input type="checkbox" name="reject_student" class="form-check-input" id="reject" onchange="toggleReject(this)">
                                <label class="form-check-label text-danger fw-bold" for="reject">
                                    <i class="fas fa-user-times me-1"></i> Permanently Reject Student
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-premium btn-lg rounded-4 shadow py-3">
                                <i class="fas fa-cloud-upload-alt me-2"></i>SUBMIT RECORD
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
                                <input type="text" name="mob" class="form-control" disabled value="<?php echo htmlspecialchars($student['mob']); ?>" required>
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

    <!-- WhatsApp Template Selection Modal -->
    <div class="modal fade" id="waTemplateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="fab fa-whatsapp text-success me-2"></i>Select Message Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Choose a message to send to <strong><?php echo htmlspecialchars($student['name']); ?></strong>.</p>
                    
                    <div class="list-group list-group-flush gap-2">
                        <?php if (empty($wa_templates)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                                <p class="mb-0">No WhatsApp templates found.</p>
                                <small class="text-muted">Please contact admin to add templates.</small>
                            </div>
                        <?php else: ?>
                            <?php foreach ($wa_templates as $wt): 
                                // Prepare the message for JS
                                $raw_msg = $wt['message'];
                                $formatted_msg = str_replace('[name]', $student['name'], $raw_msg);
                                $wa_link = "https://wa.me/91" . $student['mob'] . "?text=" . urlencode($formatted_msg);
                            ?>
                                <a href="<?php echo $wa_link; ?>" target="_blank" class="list-group-item list-group-item-action border rounded-3 p-3 wa-template-item" onclick="closeWAModal()">
                                    <div class="d-flex w-100 justify-content-between mb-1">
                                        <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($wt['title']); ?></h6>
                                        <i class="fas fa-chevron-right text-muted small"></i>
                                    </div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 100%;"><?php echo htmlspecialchars($formatted_msg); ?></small>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .wa-template-item {
            transition: all 0.2s;
        }
        .wa-template-item:hover {
            border-color: #25D366 !important;
            background-color: #f0fdf4 !important;
            transform: scale(1.02);
            z-index: 5;
        }
    </style>

    <script>
        function closeWAModal() {
            setTimeout(() => {
                var modal = bootstrap.Modal.getInstance(document.getElementById('waTemplateModal'));
                if (modal) modal.hide();
            }, 500);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>