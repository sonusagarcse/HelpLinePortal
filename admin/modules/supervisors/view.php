<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'View Supervisor';

// Get supervisor ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$supervisor_id = (int) $_GET['id'];

// Get supervisor details with manager info
$query = "SELECT s.*, b.bname, b.bcode, m.name as manager_name, m.regno as manager_regno 
          FROM supervisor s 
          LEFT JOIN branch b ON s.bid = b.id 
          LEFT JOIN manager m ON s.mnid = m.id
          WHERE s.id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $supervisor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: list.php?error=notfound');
    exit;
}

$supervisor = mysqli_fetch_assoc($result);

// Get callers under this supervisor
$callers_query = "SELECT c.*, b.bname FROM caller c 
                  LEFT JOIN branch b ON c.bid = b.id 
                  WHERE c.svid = ? 
                  ORDER BY c.name";
$callers_stmt = mysqli_prepare($con, $callers_query);
mysqli_stmt_bind_param($callers_stmt, "i", $supervisor_id);
mysqli_stmt_execute($callers_stmt);
$callers_result = mysqli_stmt_get_result($callers_stmt);
$callers = [];
while ($row = mysqli_fetch_assoc($callers_result)) {
    $callers[] = $row;
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
                        <h1>Supervisor Details</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="list.php">Supervisors</a></li>
                                <li class="breadcrumb-item active">View Supervisor</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="edit.php?id=<?php echo $supervisor_id; ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                        <a href="list.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Manager Info -->
                <div class="col-md-12">
                    <div class="table-card bg-light">
                        <h5 class="mb-3"><i class="fas fa-user-tie me-2"></i>Reports To</h5>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="20%">Manager:</th>
                                <td>
                                    <?php if ($supervisor['manager_name']): ?>
                                        <strong><?php echo htmlspecialchars($supervisor['manager_name']); ?></strong>
                                        <span
                                            class="badge bg-info ms-2"><?php echo htmlspecialchars($supervisor['manager_regno']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">No manager assigned</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="col-md-6 mt-3">
                    <div class="table-card">
                        <h5 class="mb-3"><i class="fas fa-user me-2"></i>Personal Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Registration No:</th>
                                <td><span
                                        class="badge bg-primary"><?php echo htmlspecialchars($supervisor['regno']); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Name:</th>
                                <td><strong><?php echo htmlspecialchars($supervisor['name']); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Father's Name:</th>
                                <td><?php echo htmlspecialchars($supervisor['father']); ?></td>
                            </tr>
                            <tr>
                                <th>Mother's Name:</th>
                                <td><?php echo htmlspecialchars($supervisor['mother']); ?></td>
                            </tr>
                            <tr>
                                <th>Date of Birth:</th>
                                <td><?php echo htmlspecialchars($supervisor['dob']); ?></td>
                            </tr>
                            <tr>
                                <th>Age:</th>
                                <td><?php echo htmlspecialchars($supervisor['age']); ?> years</td>
                            </tr>
                            <tr>
                                <th>Gender:</th>
                                <td><?php echo htmlspecialchars($supervisor['gender']); ?></td>
                            </tr>
                            <tr>
                                <th>Marital Status:</th>
                                <td><?php echo htmlspecialchars($supervisor['marital_status']); ?></td>
                            </tr>
                            <tr>
                                <th>Category:</th>
                                <td><?php echo htmlspecialchars($supervisor['category']); ?></td>
                            </tr>
                            <tr>
                                <th>Qualification:</th>
                                <td><?php echo htmlspecialchars($supervisor['qualification']); ?></td>
                            </tr>
                            <tr>
                                <th>Aadhar Number:</th>
                                <td><?php echo htmlspecialchars($supervisor['aadhar']); ?></td>
                            </tr>
                            <tr>
                                <th>Date of Joining:</th>
                                <td><?php echo htmlspecialchars($supervisor['doj']); ?></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <?php if ($supervisor['status'] == 1): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Contact & Address Information -->
                <div class="col-md-6 mt-3">
                    <div class="table-card">
                        <h5 class="mb-3"><i class="fas fa-phone me-2"></i>Contact Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Mobile:</th>
                                <td><?php echo htmlspecialchars($supervisor['mob']); ?></td>
                            </tr>
                            <tr>
                                <th>Alternate Mobile:</th>
                                <td><?php echo htmlspecialchars($supervisor['othermob_no']); ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?php echo htmlspecialchars($supervisor['email']); ?></td>
                            </tr>
                        </table>
                    </div>

                    <div class="table-card mt-3">
                        <h5 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Address:</th>
                                <td><?php echo nl2br(htmlspecialchars($supervisor['address'])); ?></td>
                            </tr>
                            <tr>
                                <th>State:</th>
                                <td><?php echo htmlspecialchars($supervisor['state']); ?></td>
                            </tr>
                            <tr>
                                <th>District:</th>
                                <td><?php echo htmlspecialchars($supervisor['dis']); ?></td>
                            </tr>
                            <tr>
                                <th>Pincode:</th>
                                <td><?php echo htmlspecialchars($supervisor['pincode']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Branch & Bank Information -->
                <div class="col-md-6 mt-3">
                    <div class="table-card">
                        <h5 class="mb-3"><i class="fas fa-building me-2"></i>Branch Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Assigned Branch:</th>
                                <td><?php echo htmlspecialchars($supervisor['bname'] ?? 'Not Assigned'); ?>
                                    <?php if ($supervisor['bcode']): ?>
                                        (<?php echo htmlspecialchars($supervisor['bcode']); ?>)
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="col-md-6 mt-3">
                    <div class="table-card">
                        <h5 class="mb-3"><i class="fas fa-university me-2"></i>Bank Details</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Bank Name:</th>
                                <td><?php echo htmlspecialchars($supervisor['bank']); ?></td>
                            </tr>
                            <tr>
                                <th>Branch:</th>
                                <td><?php echo htmlspecialchars($supervisor['bank_branch']); ?></td>
                            </tr>
                            <tr>
                                <th>IFSC Code:</th>
                                <td><?php echo htmlspecialchars($supervisor['ifsccode']); ?></td>
                            </tr>
                            <tr>
                                <th>Account Number:</th>
                                <td><?php echo htmlspecialchars($supervisor['accountno']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Callers Under This Supervisor -->
                <div class="col-md-12 mt-3">
                    <div class="table-card">
                        <h5 class="mb-3"><i class="fas fa-headset me-2"></i>Callers (<?php echo count($callers); ?>)
                        </h5>
                        <?php if (count($callers) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Reg No</th>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>Branch</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($callers as $caller): ?>
                                            <tr>
                                                <td><span
                                                        class="badge bg-secondary"><?php echo htmlspecialchars($caller['regno']); ?></span>
                                                </td>
                                                <td><?php echo htmlspecialchars($caller['name']); ?></td>
                                                <td><?php echo htmlspecialchars($caller['mob']); ?></td>
                                                <td><?php echo htmlspecialchars($caller['email']); ?></td>
                                                <td><?php echo htmlspecialchars($caller['bname'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <?php if ($caller['status'] == 1): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No callers assigned to this supervisor yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
