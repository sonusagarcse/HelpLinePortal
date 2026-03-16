<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

// Get manager ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$manager_id = (int) $_GET['id'];

// Check if manager has supervisors
$check_query = "SELECT COUNT(*) as count FROM supervisor WHERE mnid = ?";
$check_stmt = mysqli_prepare($con, $check_query);
mysqli_stmt_bind_param($check_stmt, "i", $manager_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
$check_row = mysqli_fetch_assoc($check_result);

if ($check_row['count'] > 0) {
    header('Location: list.php?error=has_supervisors');
    exit;
}

// Delete manager
$delete_query = "DELETE FROM manager WHERE id = ?";
$delete_stmt = mysqli_prepare($con, $delete_query);
mysqli_stmt_bind_param($delete_stmt, "i", $manager_id);

if (mysqli_stmt_execute($delete_stmt)) {
    logActivity('delete_manager', 'manager', $manager_id, null, null);
    header('Location: list.php?success=deleted');
} else {
    header('Location: list.php?error=delete_failed');
}
exit;
?>
