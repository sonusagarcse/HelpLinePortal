<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Students';

// Get all students
$query = "SELECT r.*, b.bname, mc.name as category_name 
          FROM registration r 
          LEFT JOIN branch b ON r.bid = b.id 
          LEFT JOIN member_category mc ON r.mcategory = mc.id 
          ORDER BY r.id DESC";
$result = mysqli_query($con, $query);
$students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
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
                        <h1>Students Management</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Students</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="bulk_upload.php" class="btn btn-info">
                            <i class="fas fa-file-upload me-2"></i>Bulk Upload
                        </a>
                        <a href="export.php" class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>Export to Excel
                        </a>
                        <a href="add.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Student
                        </a>
                        <a href="categories.php" class="btn btn-secondary">
                            <i class="fas fa-tags me-2"></i>Categories
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reg No</th>
                                <th>Name</th>
                                <th>Father Name</th>
                                <th>Branch</th>
                                <th>Category</th>
                                <th>Qualification</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $student['id']; ?></td>
                                    <td><span
                                            class="badge bg-primary"><?php echo htmlspecialchars($student['regno'] ?? 'N/A'); ?></span>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($student['name'] ?? 'N/A'); ?></strong></td>
                                    <td><?php echo htmlspecialchars($student['father'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['bname'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['category_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['qualification'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['mob'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['date'] ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if ($student['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="view.php?id=<?php echo $student['id']; ?>" class="btn btn-info"
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $student['id']; ?>" class="btn btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?php echo $student['id']; ?>" class="btn btn-danger"
                                                onclick="return confirmDelete('Are you sure you want to delete this student?')"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Force DataTables initialization for students table
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('.data-table')) {
            $('.data-table').DataTable({
                "pageLength": 25,
                "order": [[0, "desc"]],
                "responsive": true,
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    },
                    "zeroRecords": "No matching records found"
                }
            });
        }
    });
</script>

<?php include('../../includes/footer.php'); ?>
