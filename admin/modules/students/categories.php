<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Member Categories';

// Fetch all branches with category counts
$branches_query = "SELECT b.id, b.bname, 
                  (SELECT COUNT(*) FROM member_category WHERE bid = b.id) as cat_count 
                  FROM branch b 
                  WHERE b.status = 1 
                  ORDER BY b.bname";
$branches_result = mysqli_query($con, $branches_query);
$branches = [];
while ($row = mysqli_fetch_assoc($branches_result)) {
    $branches[] = $row;
}

// Get selected branch ID (default to first branch if available)
$selected_bid = isset($_GET['bid']) ? (int)$_GET['bid'] : ($branches[0]['id'] ?? 0);

// Get member categories for selected branch with student counts
$query = "SELECT mc.*, b.bname as branch_name,
          (SELECT COUNT(*) FROM registration WHERE mcategory = mc.id) as student_count
          FROM member_category mc 
          LEFT JOIN branch b ON mc.bid = b.id 
          WHERE mc.bid = ? 
          ORDER BY mc.id DESC";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $selected_bid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Member Categories</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Member Categories</li>
                            </ol>
                        </nav>
                    </div>
                <div class="row align-items-center g-3 mt-1">
                    <div class="col-md-auto">
                        <form method="GET" action="" id="branchFilterForm">
                            <label class="form-label small fw-bold text-muted mb-1">Filter by Branch</label>
                            <select name="bid" class="form-select shadow-sm" onchange="this.form.submit()" style="min-width: 350px;">
                                <?php foreach ($branches as $b): ?>
                                    <option value="<?php echo $b['id']; ?>" <?php echo $selected_bid == $b['id'] ? 'selected' : ''; ?>>
                                        (<?php echo $b['cat_count']; ?>) <?php echo htmlspecialchars($b['bname']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                    <div class="col-md text-md-end align-self-end">
                        <a href="add-category.php?bid=<?php echo $selected_bid; ?>" class="btn btn-primary shadow-sm">
                            <i class="fas fa-plus me-2"></i>Add Category
                        </a>
                    </div>
                </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="table table-hover data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Branch</th>
                                <th>Students</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars(($category['name']) ?? ''); ?></strong></td>
                                    <td>
                                        <?php if ($category['bid'] == 0): ?>
                                            <span class="badge bg-info">Global</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark"><?php echo htmlspecialchars($category['branch_name']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill">
                                            <?php echo $category['student_count']; ?> Students
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars(($category['date']) ?? ''); ?></td>
                                    <td>
                                        <?php if ($category['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit-category.php?id=<?php echo $category['id']; ?>"
                                                class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete-category.php?id=<?php echo $category['id']; ?>"
                                                class="btn btn-danger"
                                                onclick="return confirmDelete('Are you sure you want to delete this category?')"
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

<?php include('../../includes/footer.php'); ?>
