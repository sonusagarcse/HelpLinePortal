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
$deo_bid = $_SESSION['deo_bid'];

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
