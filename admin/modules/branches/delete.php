<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    // Get old data for logging
    $query = "SELECT * FROM branch WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $old_data = mysqli_fetch_assoc($result);

    if ($old_data) {
        // Delete the branch
        $delete_query = "DELETE FROM branch WHERE id = ?";
        $delete_stmt = mysqli_prepare($con, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $id);

        if (mysqli_stmt_execute($delete_stmt)) {
            logActivity('delete_branch', 'branch', $id, json_encode($old_data), null);
            header('Location: list.php?success=deleted');
            exit;
        }
    }
}

header('Location: list.php?error=delete_failed');
exit;
?>
