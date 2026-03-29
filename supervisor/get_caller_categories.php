<?php
session_start();

// Check if supervisor is logged in
if (!isset($_SESSION['supervisor_id'])) {
    header('Location: ../supervisor_login.php');
    exit;
}

require_once(__DIR__ . '/../connection.php');

$supervisor_id = $_SESSION['supervisor_id'];
$caller_id = isset($_POST['caller_id']) ? (int)$_POST['caller_id'] : 0;
$supervisor_bid = $_SESSION['supervisor_bid'] ?? 0;

if ($caller_id <= 0) {
    exit('<div class="text-center py-5 text-muted"><i class="fas fa-mouse-pointer fs-1 text-primary opacity-50 mb-3"></i><h6 class="fw-semibold">No Caller Selected</h6><p class="small mb-0">Please select a caller from the left panel to assign data.</p></div>');
}

// Ensure the caller belongs to this supervisor
$check_caller = mysqli_query($con, "SELECT id FROM caller WHERE id = $caller_id AND svid = $supervisor_id AND status = 1");
if (mysqli_num_rows($check_caller) === 0) {
    exit('<div class="alert alert-danger">Invalid Caller Selected.</div>');
}

// Get branches for this supervisor
$branches = [];
$supervisor_bids = $_SESSION['supervisor_bids'] ?? [];

if (!empty($supervisor_bids)) {
    $bids_list = implode(',', array_map('intval', $supervisor_bids));
    $b_query = mysqli_query($con, "SELECT id, bname, bcode FROM branch WHERE id IN ($bids_list) AND status = 1 ORDER BY bname ASC");
    while ($b = mysqli_fetch_assoc($b_query)) {
        $branches[] = $b;
    }
}

// Get all categories grouped by branch
$categories_by_branch = [];
$cat_query = mysqli_query($con, "SELECT id, name, bid FROM member_category WHERE status = 1 ORDER BY name ASC");
while ($row = mysqli_fetch_assoc($cat_query)) {
    $categories_by_branch[$row['bid']][] = $row;
}

// Get assigned branches and categories for THIS caller
$assigned_assignments = [];
$branch_stmt = mysqli_query($con, "SELECT branch_id, category_id FROM caller_branches WHERE caller_id = $caller_id AND status = 1");
while ($row = mysqli_fetch_assoc($branch_stmt)) {
    $assigned_assignments[$row['branch_id']][] = $row['category_id'];
}

// Get ALL active assignments for OTHER callers in these branches to prevent overlap
$other_assignments = [];
$bids_list = !empty($supervisor_bids) ? implode(',', array_map('intval', $supervisor_bids)) : '0';

$other_query = "SELECT cb.branch_id, cb.category_id, c.name as caller_name 
                FROM caller_branches cb 
                JOIN caller c ON cb.caller_id = c.id 
                WHERE cb.status = 1 AND cb.caller_id != $caller_id 
                AND cb.branch_id IN ($bids_list)";
$other_result = mysqli_query($con, $other_query);
while ($row = mysqli_fetch_assoc($other_result)) {
    $other_assignments[$row['branch_id']][$row['category_id']][] = $row['caller_name'];
}
?>

<?php if (empty($branches)): ?>
    <div class="card-body d-flex flex-column justify-content-center align-items-center py-5 text-center">
        <div class="rounded-circle bg-warning bg-opacity-10 p-4 mb-4">
            <i class="fas fa-exclamation-triangle fs-1 text-warning"></i>
        </div>
        <h5 class="fw-bold text-dark">No Branches Assigned</h5>
        <p class="text-muted small max-width-300 mx-auto">You currently have no branches assigned to your account. Please contact the administrator to assign branches so you can manage caller data.</p>
        <a href="index.php" class="btn btn-outline-secondary btn-sm mt-3">Return to Dashboard</a>
    </div>
<?php else: ?>
<div class="assignment-section p-4" style="background: #fdfdfd; border-radius: 15px; border: 1px solid #eee;">
    <h5 class="fw-bold mb-3 d-flex align-items-center text-dark">
        <i class="fas fa-shield-alt text-primary me-2"></i>
        Data Access Assignment (Branches & Categories)
    </h5>
    <p class="text-muted small mb-4">Select the branches and categories this caller is authorized to view and call.</p>
    
    <div class="assignment-grid">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2 g-4">
            <?php foreach ($branches as $branch): ?>
                <div class="col">
                    <div class="card h-100 assignment-card border-0 shadow-sm rounded-4" style="border: 1px solid #eef0f5 !important; overflow: hidden; transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="branch-title fw-bold" style="color: #4361ee; font-size: 0.95rem; background: #f8faff; margin: -1.5rem -1.5rem 1rem -1.5rem; padding: 0.75rem 1.5rem; border-bottom: 1px solid #f0f2f7;">
                                <i class="fas fa-building me-2"></i><?php echo htmlspecialchars($branch['bname'] . ' (' . $branch['bcode'] . ')'); ?>
                            </div>
                            
                            <div class="all-cat-wrapper bg-light p-2 rounded-3 mb-3 border" style="background: #f8f9fa;">
                                <div class="form-check">
                                    <?php 
                                        $all_cat_checked = isset($assigned_assignments[$branch['id']]) && in_array(0, $assigned_assignments[$branch['id']]); 
                                        $others_all = $other_assignments[$branch['id']][0] ?? [];
                                    ?>
                                    <input type="checkbox" name="calling_assignments[]" value="<?php echo $branch['id']; ?>_0" class="form-check-input" id="branch_<?php echo $branch['id']; ?>_all" <?php echo $all_cat_checked ? 'checked' : ''; ?> <?php echo !empty($others_all) ? 'disabled' : ''; ?>>
                                    <label class="form-check-label fw-bold" for="branch_<?php echo $branch['id']; ?>_all">All Categories</label>
                                    <?php if (!empty($others_all)): ?>
                                        <div class="other-assigned text-danger d-block mt-1" style="font-size: 0.75rem; font-weight: 500;">
                                            <i class="fas fa-user-lock me-1"></i>Held by: <span class="bg-danger bg-opacity-10 px-2 py-0.5 rounded text-danger"><?php echo htmlspecialchars(implode(', ', $others_all)); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="category-list-container">
                                <div class="category-scroll pe-2" style="max-height: 180px; overflow-y: auto;">
                                    <?php if (isset($categories_by_branch[$branch['id']]) && !empty($categories_by_branch[$branch['id']])): ?>
                                        <?php foreach ($categories_by_branch[$branch['id']] as $cat): ?>
                                            <div class="cat-item py-2" style="border-bottom: 1px dashed #f0f0f0;">
                                                <div class="form-check">
                                                    <?php 
                                                        $cat_checked = isset($assigned_assignments[$branch['id']]) && in_array($cat['id'], $assigned_assignments[$branch['id']]); 
                                                        $others_cat = $other_assignments[$branch['id']][$cat['id']] ?? [];
                                                        $is_cat_disabled = !empty($others_cat) || !empty($others_all) || $all_cat_checked;
                                                    ?>
                                                    <input type="checkbox" name="calling_assignments[]" value="<?php echo $branch['id']; ?>_<?php echo $cat['id']; ?>" class="form-check-input branch-cat-<?php echo $branch['id']; ?>" id="cat_<?php echo $branch['id']; ?>_<?php echo $cat['id']; ?>" <?php echo $cat_checked ? 'checked' : ''; ?> <?php echo $is_cat_disabled ? 'disabled' : ''; ?> data-assigned="<?php echo (!empty($others_cat) || !empty($others_all)) ? 'true' : 'false'; ?>">
                                                    <label class="form-check-label" for="cat_<?php echo $branch['id']; ?>_<?php echo $cat['id']; ?>">
                                                        <?php echo htmlspecialchars($cat['name']); ?>
                                                    </label>
                                                    <?php if (!empty($others_cat)): ?>
                                                        <div class="other-assigned text-muted d-block mt-1" style="font-size: 0.7rem;">
                                                            <i class="fas fa-user-lock text-danger me-1"></i>Assigned to: <span class="fw-medium text-dark bg-light px-2 py-0.5 rounded border"><?php echo htmlspecialchars(implode(', ', $others_cat)); ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-muted font-italic py-3 small">No categories map to this branch.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    // JS to handle "All Categories" toggle per branch
    document.querySelectorAll('[id^="branch_"][id$="_all"]').forEach(function(allCheckbox) {
        allCheckbox.addEventListener('change', function() {
            const branchId = this.id.split('_')[1];
            const catCheckboxes = document.querySelectorAll('.branch-cat-' + branchId);
            catCheckboxes.forEach(cb => {
                if (this.checked) {
                    cb.disabled = true;
                    cb.checked = false;
                } else {
                    // Only re-enable if NOT assigned to someone else
                    cb.disabled = cb.getAttribute('data-assigned') === 'true';
                }
            });
            updateCount();
        });
    });
</script>
