<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

// Get filter parameters
$selected_bid = isset($_GET['bid']) ? (int)$_GET['bid'] : 0;
$selected_cid = isset($_GET['cid']) ? (int)$_GET['cid'] : 0;

if ($selected_bid == 0) {
    die("Please select a branch first.");
}

// Fetch branch info
$b_stmt = mysqli_prepare($con, "SELECT bname FROM branch WHERE id = ?");
mysqli_stmt_bind_param($b_stmt, "i", $selected_bid);
mysqli_stmt_execute($b_stmt);
$b_res = mysqli_stmt_get_result($b_stmt);
$branch = mysqli_fetch_assoc($b_res);

// Fetch category info if selected
$cat_name = "All Categories";
if ($selected_cid > 0) {
    $c_stmt = mysqli_prepare($con, "SELECT name FROM member_category WHERE id = ?");
    mysqli_stmt_bind_param($c_stmt, "i", $selected_cid);
    mysqli_stmt_execute($c_stmt);
    $c_res = mysqli_stmt_get_result($c_stmt);
    $cat = mysqli_fetch_assoc($c_res);
    $cat_name = $cat['name'] ?? "Category #$selected_cid";
}

// Get filtered students
$query = "SELECT r.*, b.bname, mc.name as category_name 
          FROM registration r 
          LEFT JOIN branch b ON r.bid = b.id 
          LEFT JOIN member_category mc ON r.mcategory = mc.id 
          WHERE r.bid = ?";
$params = "i";
$bind_values = [$selected_bid];

if ($selected_cid > 0) {
    $query .= " AND r.mcategory = ?";
    $params .= "i";
    $bind_values[] = $selected_cid;
}
$query .= " ORDER BY r.id DESC";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, $params, ...$bind_values);
mysqli_stmt_execute($stmt);
$students = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student List - <?php echo htmlspecialchars($branch['bname']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; padding: 40px; background: white; }
        .print-header { border-bottom: 2px solid #333; margin-bottom: 30px; padding-bottom: 10px; }
        .table thead th { background-color: #f8f9fa !important; color: #333 !important; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .table-responsive { overflow: visible !important; }
        }
    </style>
</head>
<body>
    <div class="no-print mb-4 text-center">
        <button onclick="window.print()" class="btn btn-primary btn-lg px-5 shadow-sm">
            <i class="fas fa-print me-2"></i>PRINT / SAVE AS PDF
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary btn-lg ms-2">Close</button>
    </div>

    <div class="print-header">
        <div class="row align-items-end">
            <div class="col-8">
                <h2 class="mb-0 text-uppercase fw-bold"><?php echo htmlspecialchars($SITE_NAME ?? 'YUVA HELPLINE'); ?></h2>
                <h4 class="text-primary mb-0">Registered Students List</h4>
            </div>
            <div class="col-4 text-end">
                <p class="mb-0 small text-muted">Generated on: <strong><?php echo date('d M Y, h:i A'); ?></strong></p>
                <p class="mb-0 small text-muted">Total Students: <strong><?php echo mysqli_num_rows($students); ?></strong></p>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <div class="p-3 bg-light rounded border">
                <span class="text-muted small text-uppercase fw-bold d-block mb-1">Branch</span>
                <span class="h5 mb-0"><?php echo htmlspecialchars($branch['bname']); ?></span>
            </div>
        </div>
        <div class="col-6">
            <div class="p-3 bg-light rounded border">
                <span class="text-muted small text-uppercase fw-bold d-block mb-1">Category</span>
                <span class="h5 mb-0"><?php echo htmlspecialchars($cat_name); ?></span>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Reg No</th>
                    <th>Name</th>
                    <th>Father Name</th>
                    <th>Gender</th>
                    <th>Mobile</th>
                    <th>District</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($s = mysqli_fetch_assoc($students)): ?>
                <tr>
                    <td><?php echo $s['id']; ?></td>
                    <td><small class="fw-bold"><?php echo htmlspecialchars($s['regno'] ?? ''); ?></small></td>
                    <td><strong><?php echo htmlspecialchars($s['name'] ?? ''); ?></strong></td>
                    <td><?php echo htmlspecialchars($s['father'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($s['gender'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($s['mob'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($s['dis'] ?? ''); ?></td>
                    <td><?php echo ($s['status'] == 1 ? 'Active' : 'Inactive'); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-5 text-end pt-4 border-top">
        <p class="small text-muted italic">This is a system-generated document.</p>
    </div>

    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
    <script>
        // Optional: Auto-print on load if needed
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
