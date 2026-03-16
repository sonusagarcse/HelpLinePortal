<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'View Caller';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Get caller data with supervisor and branch info
$query = "SELECT c.*, s.name as supervisor_name, b.bname as branch_name, b.bcode 
          FROM caller c 
          LEFT JOIN supervisor s ON c.svid = s.id 
          LEFT JOIN branch b ON c.bid = b.id 
          WHERE c.id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$caller = mysqli_fetch_assoc($result);

if (!$caller) {
    header('Location: list.php?error=not_found');
    exit;
}

// Get assigned branches for calling
$branch_query = "SELECT b.id, b.bname, b.bcode, cb.assigned_date 
                 FROM caller_branches cb 
                 JOIN branch b ON cb.branch_id = b.id 
                 WHERE cb.caller_id = ? AND cb.status = 1 
                 ORDER BY b.bname";
$branch_stmt = mysqli_prepare($con, $branch_query);
mysqli_stmt_bind_param($branch_stmt, "i", $id);
mysqli_stmt_execute($branch_stmt);
$branch_result = mysqli_stmt_get_result($branch_stmt);
$calling_branches = [];
while ($row = mysqli_fetch_assoc($branch_result)) {
    $calling_branches[] = $row;
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Caller Details</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="list.php">Callers</a></li>
                                <li class="breadcrumb-item active">View Caller</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="edit.php?id=<?php echo $caller['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                        <a href="list.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <h4>Personal Information</h4>
                        <hr>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Registration Number:</strong>
                        <p><span class="badge bg-primary"><?php echo htmlspecialchars($caller['regno']); ?></span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Status:</strong>
                        <p>
                            <?php if ($caller['status'] == 1): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Full Name:</strong>
                        <p><?php echo htmlspecialchars($caller['name']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Father's Name:</strong>
                        <p><?php echo htmlspecialchars($caller['father']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Mother's Name:</strong>
                        <p><?php echo htmlspecialchars($caller['mother'] ?: 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Gender:</strong>
                        <p><?php echo htmlspecialchars($caller['gender']); ?></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Date of Birth:</strong>
                        <p><?php echo $caller['dob'] != '0000-00-00' ? date('d-m-Y', strtotime($caller['dob'])) : 'N/A'; ?>
                        </p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Age:</strong>
                        <p><?php echo $caller['age'] ?: 'N/A'; ?></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Date of Joining:</strong>
                        <p><?php echo $caller['doj'] != '0000-00-00' ? date('d-m-Y', strtotime($caller['doj'])) : 'N/A'; ?>
                        </p>
                    </div>

                    <div class="col-md-12 mb-3 mt-3">
                        <h4>Contact Information</h4>
                        <hr>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Mobile Number:</strong>
                        <p><?php echo htmlspecialchars($caller['mob']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Alternate Mobile:</strong>
                        <p><?php echo htmlspecialchars($caller['othermob_no'] ?: 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Email:</strong>
                        <p><?php echo htmlspecialchars($caller['email'] ?: 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Aadhar Number:</strong>
                        <p><?php echo htmlspecialchars($caller['aadhar'] ?: 'N/A'); ?></p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <strong>Address:</strong>
                        <p><?php echo htmlspecialchars($caller['address']); ?></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>State:</strong>
                        <p><?php echo htmlspecialchars($caller['state']); ?></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>District:</strong>
                        <p><?php echo htmlspecialchars($caller['dis']); ?></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Pincode:</strong>
                        <p><?php echo htmlspecialchars($caller['pincode']); ?></p>
                    </div>

                    <div class="col-md-12 mb-3 mt-3">
                        <h4>Other Details</h4>
                        <hr>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Qualification:</strong>
                        <p><?php echo htmlspecialchars($caller['qualification']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Category:</strong>
                        <p><?php echo htmlspecialchars($caller['category'] ?: 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Marital Status:</strong>
                        <p><?php echo htmlspecialchars($caller['marital_status'] ?: 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Registration Date:</strong>
                        <p><?php echo htmlspecialchars($caller['date']); ?></p>
                    </div>

                    <div class="col-md-12 mb-3 mt-3">
                        <h4>Assignment Information</h4>
                        <hr>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Supervisor:</strong>
                        <p><?php echo htmlspecialchars($caller['supervisor_name'] ?: 'Not Assigned'); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Primary Branch:</strong>
                        <p><?php echo htmlspecialchars(($caller['bcode'] ?? '') . ' - ' . ($caller['branch_name'] ?? 'Not Assigned')); ?>
                        </p>
                    </div>
                    <div class="col-md-12 mb-3">
                        <strong>Branches for Calling:</strong>
                        <?php if (!empty($calling_branches)): ?>
                            <div class="mt-2">
                                <?php foreach ($calling_branches as $branch): ?>
                                    <span class="badge bg-info me-2 mb-2">
                                        <?php echo htmlspecialchars($branch['bcode'] . ' - ' . $branch['bname']); ?>
                                        <small class="ms-1">(Assigned:
                                            <?php echo date('d-m-Y', strtotime($branch['assigned_date'])); ?>)</small>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No branches assigned for calling</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
