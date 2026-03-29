<?php
session_start();

// Check if supervisor is logged in
if (!isset($_SESSION['supervisor_id'])) {
    header('Location: ../supervisor_login.php');
    exit;
}

require_once(__DIR__ . '/../connection.php');

$supervisor_id = $_SESSION['supervisor_id'];
$supervisor_name = $_SESSION['supervisor_name'];
$supervisor_bids = $_SESSION['supervisor_bids'] ?? [];
$supervisor_bid = $_SESSION['supervisor_bid'] ?? 0;

// Handle assignment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_data'])) {
    $caller_id = (int)$_POST['caller_id'];
    $calling_assignments = isset($_POST['calling_assignments']) ? $_POST['calling_assignments'] : [];

    if ($caller_id > 0) {
        // First, deactivate all existing category assignments for this caller (scoped to supervisor's branches)
        // First, deactivate all existing category assignments for this caller (scoped to supervisor's branches)
        if (!empty($supervisor_bids)) {
            $bids_list = implode(',', array_map('intval', $supervisor_bids));
            mysqli_query($con, "UPDATE caller_branches SET status = 0 WHERE caller_id = $caller_id AND branch_id IN ($bids_list)");
        } else {
            // Should not happen if logged in correctly
            mysqli_query($con, "UPDATE caller_branches SET status = 0 WHERE caller_id = $caller_id");
        }

        // Then reactivate/insert the checked ones
        if (!empty($calling_assignments)) {
            $branch_query = "INSERT INTO caller_branches (caller_id, branch_id, category_id, assigned_date, status) VALUES (?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE status = 1, assigned_date = ?";
            $branch_stmt = mysqli_prepare($con, $branch_query);
            $assigned_date = date('Y-m-d');

            foreach ($calling_assignments as $assignment) {
                // assignment format: branchID_categoryID
                $parts = explode('_', $assignment);
                if (count($parts) == 2) {
                    $assign_branch_id = (int)$parts[0];
                    $cat_id = (int)$parts[1];
                    
                    // Security check: If supervisor has specific branches, they can only assign for those branches
                    if (empty($supervisor_bids) || in_array($assign_branch_id, $supervisor_bids)) {
                        mysqli_stmt_bind_param($branch_stmt, "iiiss", $caller_id, $assign_branch_id, $cat_id, $assigned_date, $assigned_date);
                        mysqli_stmt_execute($branch_stmt);
                    }
                }
            }
            mysqli_stmt_close($branch_stmt);
        }
        $_SESSION['success_message'] = "Category data access assigned successfully!";
        header('Location: assign_data.php');
        exit;
    }
}

// Handle assignment deletion
if (isset($_GET['delete_caller'])) {
    $del_caller_id = (int)$_GET['delete_caller'];
    if (!empty($supervisor_bids)) {
        $bids_list = implode(',', array_map('intval', $supervisor_bids));
        mysqli_query($con, "UPDATE caller_branches SET status = 0 WHERE caller_id = $del_caller_id AND branch_id IN ($bids_list)");
    } else {
        mysqli_query($con, "UPDATE caller_branches SET status = 0 WHERE caller_id = $del_caller_id");
    }
    $_SESSION['success_message'] = "All configurations for caller have been removed.";
    header('Location: assign_data.php');
    exit;
}

// Get callers under this supervisor
$callers_query = "SELECT id, regno, name FROM caller WHERE svid = $supervisor_id AND status = 1 ORDER BY name";
$callers = [];
$result = mysqli_query($con, $callers_query);
while ($row = mysqli_fetch_assoc($result)) {
    $callers[] = $row;
}

    $bids_list = !empty($supervisor_bids) ? implode(',', array_map('intval', $supervisor_bids)) : '0';
    $assignments_query = "
        SELECT c.id as caller_id, c.name as caller_name, c.regno as caller_regno, 
               GROUP_CONCAT(DISTINCT 
                    CONCAT(b.bcode, ': ', IF(cb.category_id=0, 'All Categories', mc.name)) 
                    SEPARATOR '<br>') as categories_assigned,
               MAX(cb.assigned_date) as last_assigned
        FROM caller_branches cb
        JOIN caller c ON cb.caller_id = c.id
        JOIN branch b ON cb.branch_id = b.id
        LEFT JOIN member_category mc ON cb.category_id = mc.id
        WHERE cb.branch_id IN ($bids_list) AND cb.status = 1 AND c.svid = $supervisor_id
        GROUP BY c.id
    ";
$assignments = [];
$assignments_result = mysqli_query($con, $assignments_query);
if ($assignments_result) {
    while ($row = mysqli_fetch_assoc($assignments_result)) {
        $assignments[] = $row;
    }
}

$page_title = "Assign Data";
include 'includes/header.php'; 
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">Assign Calling Categories</h1>
        <p class="page-subtitle">Allocate specific student categories to your callers to manage data flow.</p>
    </div>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="background-color: #ecfdf5; color: #065f46;">
        <i class="fas fa-check-circle me-2 text-success"></i><?php echo $_SESSION['success_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" style="background-color: #fef2f2; color: #991b1b;">
        <i class="fas fa-exclamation-circle me-2 text-danger"></i><?php echo $_SESSION['error_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<form method="POST" id="assignmentForm">
    <div class="row g-4">
        <!-- Assignment Settings -->
        <div class="col-xl-4 col-lg-5">
            <div class="card h-100 mb-0">
                <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent">
                    <h5 class="mb-0"><i class="fas fa-user-cog text-primary me-2"></i>Select Caller</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">
                            Caller <span class="text-danger">*</span>
                        </label>
                        <select name="caller_id" id="callerSelect" class="form-select" required>
                            <option value="">Choose a caller...</option>
                            <?php foreach ($callers as $caller): ?>
                                <option value="<?php echo $caller['id']; ?>">
                                    <?php echo htmlspecialchars($caller['regno'] . ' - ' . $caller['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text text-muted mt-2">Pick a caller to configure their authorized student categories for your branch.</div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-4" style="background-color: #f8fafc; border: 1px solid var(--card-border);">
                        <span class="fw-semibold text-dark">Selected Categories</span>
                        <span class="badge bg-primary rounded-pill px-3 fs-6" id="selectedCount">0</span>
                    </div>

                    <button type="submit" name="assign_data" id="assignBtn" class="btn btn-primary w-100 py-2 d-flex justify-content-center align-items-center gap-2" disabled>
                        <i class="fas fa-check"></i> Save Assignment Profile
                    </button>
                </div>
            </div>
        </div>

        <!-- Categories List via AJAX -->
        <div class="col-xl-8 col-lg-7">
            <div class="card h-100 mb-0" id="categoriesContainer">
                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5 text-muted">
                    <i class="fas fa-layer-group fs-1 text-primary opacity-50 mb-3"></i>
                    <h6 class="fw-semibold">No Caller Selected</h6>
                    <p class="small mb-0">Please select a caller from the left panel to modify category access.</p>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Manage Active Assignments -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom-0 pt-4 pb-0 bg-transparent">
                <h5 class="mb-0"><i class="fas fa-sitemap text-primary me-2"></i>Active Caller Configurations</h5>
                <p class="text-muted small mt-2 mb-0">Overview of categories callers are allowed to pull data from for your branch.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="assignmentsTable" class="table table-hover w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Caller Name</th>
                                <th>Assigned Categories</th>
                                <th>Last Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php $sn = 1; foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td class="text-muted"><?php echo $sn++; ?></td>
                                    <td class="fw-medium text-dark">
                                        <?php echo htmlspecialchars($assignment['caller_name']); ?>
                                        <div class="small text-muted"><?php echo htmlspecialchars($assignment['caller_regno']); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1" style="white-space: normal; line-height: 1.5;">
                                            <?php echo htmlspecialchars($assignment['categories_assigned']); ?>
                                        </span>
                                    </td>
                                    <td class="text-muted"><?php echo htmlspecialchars(date('M d, Y', strtotime($assignment['last_assigned']))); ?></td>
                                    <td>
                                        <a href="assign_data.php?delete_caller=<?php echo $assignment['caller_id']; ?>"
                                            class="btn btn-sm btn-light text-danger border border-danger border-opacity-25" style="background-color: #fef2f2;"
                                            onclick="return confirm('Remove all Category Assignments for this caller?');" title="Clear Configuration">
                                            <i class="fas fa-trash-alt"></i> Revoke
                                        </a>
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

<?php include 'includes/footer.php'; ?>
<script>
    $(document).ready(function () {
        $('#assignmentsTable').DataTable({
            pageLength: 25,
            order: [[0, 'asc']]
        });
    });

    // AJAX to fetch caller category checkboxes
    $('#callerSelect').change(function () {
        const callerId = $(this).val();
        const container = $('#categoriesContainer');
        const assignBtn = $('#assignBtn');
        
        if (callerId) {
            assignBtn.prop('disabled', false);

            container.html('<div class="card-body d-flex flex-column justify-content-center align-items-center py-5"><div class="spinner-border text-primary border-3" role="status" style="width: 3rem; height: 3rem;"></div><div class="mt-3 text-muted fw-medium">Loading caller configuration...</div></div>');

            $.ajax({
                url: 'get_caller_categories.php',
                method: 'POST',
                data: { caller_id: callerId },
                success: function (response) {
                    container.html(response);
                    
                    // Attach change events to newly loaded checkboxes
                    container.find('input[type="checkbox"]').on('change', function() {
                        updateCount();
                    });
                    
                    updateCount();
                },
                error: function () {
                    container.html('<div class="card-body d-flex flex-column justify-content-center align-items-center py-5 text-danger"><i class="fas fa-exclamation-triangle fs-1 opacity-50 mb-3"></i><br>Error fetching data. Please try again.</div>');
                }
            });
        } else {
            assignBtn.prop('disabled', true);
            container.html('<div class="card-body d-flex flex-column justify-content-center align-items-center py-5 text-muted"><i class="fas fa-layer-group fs-1 text-primary opacity-50 mb-3"></i><h6 class="fw-semibold">No Caller Selected</h6><p class="small mb-0">Please select a caller from the left panel to modify category access.</p></div>');
            $('#selectedCount').text('0');
        }
    });

    function updateCount() {
        const count = $('.branch-cat:checked').length + ($('#cat_all').is(':checked') ? 1 : 0);
        $('#selectedCount').text(count);
    }
</script>