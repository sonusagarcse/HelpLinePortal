<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['deo_logged_in']) || $_SESSION['deo_logged_in'] !== true) {
    $redirect_url = (isset($SITE_URL) ? $SITE_URL : '') . '/deo_login.php';
    header('Location: ' . $redirect_url);
    exit;
}

$deo_id = $_SESSION['deo_id'];
$deo_name = $_SESSION['deo_name'];
$deo_email = $_SESSION['deo_email'];

// Update active branch from session or database
if (!isset($_SESSION['active_bid'])) {
    // Try to get first assigned branch if none selected
    $b_query = "SELECT db.branch_id, b.bname 
                FROM deo_branches db 
                JOIN branch b ON db.branch_id = b.id 
                WHERE db.deo_id = ? LIMIT 1";
    $b_stmt = mysqli_prepare($con, $b_query);
    mysqli_stmt_bind_param($b_stmt, "i", $deo_id);
    mysqli_stmt_execute($b_stmt);
    $b_res = mysqli_stmt_get_result($b_stmt)->fetch_assoc();
    if ($b_res) {
        $_SESSION['active_bid'] = $b_res['branch_id'];
        $_SESSION['active_bname'] = $b_res['bname'];
    } else {
        $_SESSION['active_bid'] = 0;
        $_SESSION['active_bname'] = 'None';
    }
}

$active_bid = $_SESSION['active_bid'];
$active_bname = $_SESSION['active_bname'];

// Session timeout
$timeout = 3600;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    $redirect_url = (isset($SITE_URL) ? $SITE_URL : '') . '/deo_login.php?timeout=1';
    header('Location: ' . $redirect_url);
    exit;
}
$_SESSION['last_activity'] = time();
?>
