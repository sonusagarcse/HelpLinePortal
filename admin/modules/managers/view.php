<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'View Manager';

// Get manager ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$manager_id = (int) $_GET['id'];

// Get manager details
$query = "SELECT m.*, b.bname, b.bcode 
          FROM manager m 
          LEFT JOIN branch b ON m.bid = b.id 
          WHERE m.id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $manager_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: list.php?error=notfound');
    exit;
}

$manager = mysqli_fetch_assoc($result);

// Get supervisors under this manager
$supervisors_query = "SELECT s.*, b.bname FROM supervisor s 
                      LEFT JOIN branch b ON s.bid = b.id 
                      WHERE s.mnid = ? 
                      ORDER BY s.name";
$supervisors_stmt = mysqli_prepare($con, $supervisors_query);
mysqli_stmt_bind_param($supervisors_stmt, "i", $manager_id);
mysqli_stmt_execute($supervisors_stmt);
$supervisors_result = mysqli_stmt_get_result($supervisors_stmt);
$supervisors = [];
while ($row = mysqli_fetch_assoc($supervisors_result)) {
    $supervisors[] = $row;
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
                        <h1>Manager Details</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="list.php">Managers</a></li>
                                <li class="breadcrumb-item active">View Manager</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="edit.php?id=<?php echo $manager_id; ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                        <a href="list.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Personal Information -->
                <div class="col-md-6">
                    <div class="table-card">
                        <h5 class="mb-3"><i class="fas fa-user me-2"></i>Personal Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Registration No:</th>
                                <td><span
                                        class="badge bg-primary"><?php echo htmlspecialchars($manager['regno']); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Name:</th>
                                <td><strong><?php echo htmlspecialchars($manager['name']); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Father's Name:</th>
                                <td><?php echo htmlspecialchars($manager['father']); ?></td>
                            </tr>
                            <tr>
                                <th>Mother's Name:</th>
                                <td><?php echo htmlspecialchars($manager['mother']); ?></td>
                            </tr>
                            <tr>
                                <th>Date of Birth:</th>
                                <td><?php echo htmlspecialchars($manager['dob']); ?></td>
                            </tr>
                            <tr>
                                <th>Age:</th>
                                <td><?php echo htmlspecialchars($manager['age']); ?> years</td>
                            </tr>
                            <tr>
                                <th>Gender:</th>
                                <td><?php echo htmlspecialchars($manager['gender']); ?></td>
                            </tr>
                            <tr>
                                <th>Marital Status:</th>
                                <td><?php echo htmlspecialchars($manager['marital_status']); ?></td>
                            </tr>
                            <tr>
                                <th>Category:</th>
                                <td><?php echo htmlspecialchars($manager['category']); ?></td>
                            </tr>
                            <tr>
                                <th>Qualification:</th>
                                <td><?php echo htmlspecialchars($manager['qualification']); ?></td>
                            </tr>
                            <tr>
                                <th>Aadhar Number:</th>
                                <td><?php echo htmlspecialchars($manager['aadhar']); ?></td>
                            </tr>
                            <tr>
                                <th>Date of Joining:</th>
                                <td><?php echo htmlspecialchars($manager['doj']); ?></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <?php if ($manager['status'] == 1): ?>
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
                <div class="col-md-6">
                    <div class="table-card">
                        <h5 class="mb-3"><i class="fas fa-phone me-2"></i>Contact Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Mobile:</th>
                                <td><?php echo htmlspecialchars($manager['mob']); ?></td>
                            </tr>
                            <tr>
                                <th>Alternate Mobile:</th>
                                <td><?php echo htmlspecialchars($manager['othermob_no']); ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?php echo htmlspecialchars($manager['email']); ?></td>
                            </tr>
                        </table>
                    </div>

                    <div class="table-card mt-3">
                        <h5 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Address:</th>
                                <td><?php echo nl2br(htmlspecialchars($manager['address'])); ?></td>
                            </tr>
                            <tr>
                                <th>State:</th>
                                <td><?php echo htmlspecialchars($manager['state']); ?></td>
                            </tr>
                            <tr>
                                <th>District:</th>
                                <td><?php echo htmlspecialchars($manager['dis']); ?></td>
                            </tr>
                            <tr>
                                <th>Pincode:</th>
                                <td><?php echo htmlspecialchars($manager['pincode']); ?></td>
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
                                <td><?php echo htmlspecialchars($manager['bname'] ?? 'Not Assigned'); ?>
                                    <?php if ($manager['bcode']): ?>
                                        (<?php echo htmlspecialchars($manager['bcode']); ?>)
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
                                <td><?php echo htmlspecialchars($manager['bank']); ?></td>
                            </tr>
                            <tr>
                                <th>Branch:</th>
                                <td><?php echo htmlspecialchars($manager['bank_branch']); ?></td>
                            </tr>
                            <tr>
                                <th>IFSC Code:</th>
                                <td><?php echo htmlspecialchars($manager['ifsccode']); ?></td>
                            </tr>
                            <tr>
                                <th>Account Number:</th>
                                <td><?php echo htmlspecialchars($manager['accountno']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Supervisors Under This Manager -->
                <div class="col-md-12 mt-3">
                    <div class="table-card">
                        <h5 class="mb-3"><i class="fas fa-users me-2"></i>Supervisors
                            (<?php echo count($supervisors); ?>)</h5>
                        <?php if (count($supervisors) > 0): ?>
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
                                        <?php foreach ($supervisors as $supervisor): ?>
                                            <tr>
                                                <td><span
                                                        class="badge bg-info"><?php echo htmlspecialchars($supervisor['regno']); ?></span>
                                                </td>
                                                <td><?php echo htmlspecialchars($supervisor['name']); ?></td>
                                                <td><?php echo htmlspecialchars($supervisor['mob']); ?></td>
                                                <td><?php echo htmlspecialchars($supervisor['email']); ?></td>
                                                <td><?php echo htmlspecialchars($supervisor['bname'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <?php if ($supervisor['status'] == 1): ?>
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
                            <p class="text-muted">No supervisors assigned to this manager yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
