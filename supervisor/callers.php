<?php
session_start();

// Check if supervisor is logged in
if (!isset($_SESSION['supervisor_id'])) {
    header('Location: ' . (isset($SITE_URL) ? $SITE_URL : '') . '/supervisor_login.php');
    exit;
}

require_once(__DIR__ . '/../connection.php');

$supervisor_id = $_SESSION['supervisor_id'];
$supervisor_name = $_SESSION['supervisor_name'];
$supervisor_bids = $_SESSION['supervisor_bids'] ?? [];

// Get assigned branches for the filter dropdown
$branches = [];
if (!empty($supervisor_bids)) {
    $bids_list = implode(',', array_map('intval', $supervisor_bids));
    $b_query = mysqli_query($con, "SELECT id, bname, bcode FROM branch WHERE id IN ($bids_list) AND status = 1 ORDER BY bname ASC");
    while ($b = mysqli_fetch_assoc($b_query)) {
        $branches[] = $b;
    }
}

$selected_bid = isset($_GET['bid']) ? (int)$_GET['bid'] : 0;
$branch_filter = "";
if ($selected_bid > 0 && in_array($selected_bid, $supervisor_bids)) {
    $branch_filter = " AND c.bid = $selected_bid";
}

// Get all callers under this supervisor
$query = "SELECT c.*, b.bname, b.bcode,
          (SELECT COUNT(*) FROM mquery WHERE callerid = c.id) as total_calls,
          (SELECT COUNT(*) FROM mquery WHERE callerid = c.id AND status = 0) as completed_calls,
          (SELECT COUNT(*) FROM mquery WHERE callerid = c.id AND DATE(date) = CURDATE()) as today_calls
          FROM caller c 
          LEFT JOIN branch b ON c.bid = b.id 
          WHERE c.svid = $supervisor_id $branch_filter
          ORDER BY c.name ASC";
$callers = [];
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $callers[] = $row;
}
?>
<?php 
$page_title = "My Callers";
include 'includes/header.php'; 
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">My Callers</h1>
        <p class="page-subtitle">Manage and monitor the performance of your assigned callers.</p>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-dark fw-medium">Filter by Branch</label>
                <select name="bid" class="form-select">
                    <option value="">All Assigned Branches</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['id']; ?>" <?php echo $selected_bid == $branch['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($branch['bname']); ?> (<?php echo htmlspecialchars($branch['bcode']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 py-2 d-flex justify-content-center align-items-center gap-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
            <?php if ($selected_bid > 0): ?>
            <div class="col-md-2">
                <a href="callers.php" class="btn btn-outline-secondary w-100 py-2">Clear</a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-users text-primary me-2"></i>Callers List <span class="badge bg-secondary ms-2"><?php echo count($callers); ?></span></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="callersTable" class="table table-hover mb-0 w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Regno</th>
                        <th>Name</th>
                        <th>Father</th>
                        <th>Branch</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Total Calls</th>
                        <th>Completed</th>
                        <th>Today</th>
                        <th>DOJ</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php foreach ($callers as $caller): ?>
                        <tr>
                            <td class="text-muted">#<?php echo $caller['id']; ?></td>
                            <td><span class="fw-medium text-dark"><?php echo htmlspecialchars($caller['regno']); ?></span></td>
                            <td class="fw-medium text-dark"><?php echo htmlspecialchars($caller['name']); ?></td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($caller['father']); ?></span></td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($caller['bcode'] . ' - ' . $caller['bname']); ?></span></td>
                            <td><?php echo htmlspecialchars($caller['mob']); ?></td>
                            <td><span class="text-muted small"><?php echo htmlspecialchars($caller['email']); ?></span></td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-2 py-1"><?php echo $caller['total_calls']; ?></span></td>
                            <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 px-2 py-1"><?php echo $caller['completed_calls']; ?></span></td>
                            <td><span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 px-2 py-1"><?php echo $caller['today_calls']; ?></span></td>
                            <td class="text-muted"><?php echo date('d M Y', strtotime($caller['doj'])); ?></td>
                            <td>
                                <?php if ($caller['status'] == 1): ?>
                                    <span class="badge bg-success border-success border-opacity-25 px-2 py-1">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger border-danger border-opacity-25 px-2 py-1">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="caller_details.php?id=<?php echo $caller['id']; ?>" class="btn btn-sm btn-light border shadow-sm px-3" title="View Details">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>
    $(document).ready(function () {
        $('#callersTable').DataTable({
            order: [[1, 'asc']],
            pageLength: 25,
            dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-sm btn-light border me-1'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-sm btn-light border me-1'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-light border me-1'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-sm btn-light border me-1'
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-light border'
                }
            ]
        });
    });
</script>