<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Students';

// Fetch all active branches
$branches_query = "SELECT id, bname FROM branch WHERE status = 1 ORDER BY bname ASC";
$branches_result = mysqli_query($con, $branches_query);
$branches = [];
while ($row = mysqli_fetch_assoc($branches_result)) {
    $branches[] = $row;
}

// Get filter parameters
$selected_bid = isset($_GET['bid']) ? (int)$_GET['bid'] : 0;
$selected_cid = isset($_GET['cid']) ? (int)$_GET['cid'] : 0;

// Fetch categories for the selected branch
$categories = [];
if ($selected_bid > 0) {
    $cat_query = "SELECT id, name FROM member_category WHERE bid = ? ORDER BY name ASC";
    $cat_stmt = mysqli_prepare($con, $cat_query);
    mysqli_stmt_bind_param($cat_stmt, "i", $selected_bid);
    mysqli_stmt_execute($cat_stmt);
    $cat_result = mysqli_stmt_get_result($cat_stmt);
    while ($row = mysqli_fetch_assoc($cat_result)) {
        $categories[] = $row;
    }
}

// Get students based on filters
$students = [];
if ($selected_bid > 0) {
    $query = "SELECT r.*, b.bname, mc.name as category_name 
              FROM registration r 
              LEFT JOIN branch b ON r.bid = b.id 
              LEFT JOIN member_category mc ON r.mcategory = mc.id 
              WHERE r.bid = ?";
    
    if ($selected_cid > 0) {
        $query .= " AND r.mcategory = ?";
    }
    
    $query .= " ORDER BY r.id DESC";
    
    $stmt = mysqli_prepare($con, $query);
    if ($selected_cid > 0) {
        mysqli_stmt_bind_param($stmt, "ii", $selected_bid, $selected_cid);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $selected_bid);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
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
                    <div class="name"><?php echo htmlspecialchars($admin_name ?? 'Administrator'); ?></div>
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
                <div class="row align-items-center g-3">
                    <div class="col-md">
                        <h1>Students Management</h1>
                        <p class="text-muted small mb-0">Select a branch to view and manage students.</p>
                    </div>
                    <div class="col-md-auto">
                        <div class="d-flex gap-2">
                            <a href="add.php" class="btn btn-primary shadow-sm">
                                <i class="fas fa-plus me-2"></i>Add Student
                            </a>
                            <a href="bulk_upload.php" class="btn btn-outline-primary shadow-sm">
                                <i class="fas fa-file-upload me-2"></i>Bulk
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-card mt-4 p-3 border-0 shadow-sm">
                    <form method="GET" action="" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">Select Branch</label>
                            <select name="bid" class="form-select shadow-sm" onchange="this.form.submit()">
                                <option value="0">-- Select Branch --</option>
                                <?php foreach ($branches as $b): ?>
                                    <option value="<?php echo $b['id']; ?>" <?php echo $selected_bid == $b['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($b['bname']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if ($selected_bid > 0): ?>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">Filter by Category</label>
                            <select name="cid" class="form-select shadow-sm" onchange="this.form.submit()">
                                <option value="0">All Categories</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo $selected_cid == $c['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="btn-group shadow-sm">
                                <a href="export.php?bid=<?php echo $selected_bid; ?>&cid=<?php echo $selected_cid; ?>" class="btn btn-success">
                                    <i class="fas fa-file-excel me-2"></i>Excel
                                </a>
                                <a href="print-pdf.php?bid=<?php echo $selected_bid; ?>&cid=<?php echo $selected_cid; ?>" target="_blank" class="btn btn-danger">
                                    <i class="fas fa-file-pdf me-2"></i>PDF / Print
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <?php if ($selected_bid == 0): ?>
                <div class="table-card text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-search fa-3x text-light"></i>
                    </div>
                    <h4 class="text-muted">Please select a branch to view students</h4>
                    <p class="text-muted small">Choose a branch from the dropdown above to load the registered students list.</p>
                </div>
            <?php else: ?>

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
            <?php endif; ?>
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
