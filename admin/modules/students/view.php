<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'View Student';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = (int)$_GET['id'];

$query = "SELECT r.*, b.bname, mc.name as category_name 
          FROM registration r 
          LEFT JOIN branch b ON r.bid = b.id 
          LEFT JOIN member_category mc ON r.mcategory = mc.id 
          WHERE r.id = $id";
$result = mysqli_query($con, $query);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    header('Location: list.php');
    exit;
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
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Student Details: <?php echo htmlspecialchars($student['name']); ?></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="list.php">Students</a></li>
                                <li class="breadcrumb-item active">View Student</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="edit.php?id=<?php echo $student['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                        <a href="list.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="table-card mb-4">
                        <h5 class="mb-3 border-bottom pb-2">Personal Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Registration No:</strong></td>
                                <td><span class="badge bg-primary"><?php echo htmlspecialchars($student['regno']); ?></span></td>
                            </tr>
                            <tr>
                                <td><strong>Student Name:</strong></td>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Father's Name:</strong></td>
                                <td><?php echo htmlspecialchars($student['father']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Mother's Name:</strong></td>
                                <td><?php echo htmlspecialchars($student['mother'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Date of Birth:</strong></td>
                                <td><?php echo date('d M, Y', strtotime($student['dob'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Gender:</strong></td>
                                <td><?php echo htmlspecialchars($student['gender']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Qualification:</strong></td>
                                <td><?php echo htmlspecialchars($student['qualification']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Aadhar No:</strong></td>
                                <td><?php echo htmlspecialchars($student['aadhar'] ?? 'N/A'); ?></td>
                            </tr>
                        </table>
                    </div>

                    <div class="table-card">
                        <h5 class="mb-3 border-bottom pb-2">Academic Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Branch:</strong></td>
                                <td><?php echo htmlspecialchars($student['bname'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Category/School:</strong></td>
                                <td><?php echo htmlspecialchars($student['category_name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Admission Date:</strong></td>
                                <td><?php echo htmlspecialchars($student['date']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <?php if ($student['status'] == 1): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="table-card mb-4">
                        <h5 class="mb-3 border-bottom pb-2">Contact Details</h5>
                        <p><strong>Mobile:</strong> <?php echo htmlspecialchars($student['mob']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></p>
                        <p><strong>Address:</strong><br><?php echo nl2br(htmlspecialchars($student['address'])); ?></p>
                        <p><strong>District:</strong> <?php echo htmlspecialchars($student['dis']); ?></p>
                        <p><strong>State:</strong> <?php echo htmlspecialchars($student['state']); ?></p>
                        <p><strong>Pincode:</strong> <?php echo htmlspecialchars($student['pincode']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
