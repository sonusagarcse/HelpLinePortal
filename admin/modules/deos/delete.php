<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get DEO info for logging
    $query = "SELECT * FROM deo WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $deo = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if ($deo) {
        $query = "DELETE FROM deo WHERE id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            logActivity('delete_deo', 'deo', $id, json_encode($deo), null);
            header('Location: list.php?success=deleted');
            exit;
        }
    }
}

header('Location: list.php');
exit;
?>
