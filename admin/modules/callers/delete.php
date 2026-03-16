<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: list.php?error=invalid_id');
    exit;
}

// Get caller data before deletion for logging
$query = "SELECT * FROM caller WHERE id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$caller = mysqli_fetch_assoc($result);

if (!$caller) {
    header('Location: list.php?error=not_found');
    exit;
}

// Delete caller (this will cascade delete caller_branches due to foreign key)
$delete_query = "DELETE FROM caller WHERE id = ?";
$delete_stmt = mysqli_prepare($con, $delete_query);
mysqli_stmt_bind_param($delete_stmt, "i", $id);

if (mysqli_stmt_execute($delete_stmt)) {
    logActivity('delete_caller', 'caller', $id, json_encode($caller), null);
    header('Location: list.php?success=deleted');
} else {
    header('Location: list.php?error=delete_failed');
}
exit;
?>
