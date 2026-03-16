<?php
session_start();

// Check if supervisor is logged in
if (!isset($_SESSION['supervisor_id'])) {
    header('Location: ../supervisor_login.php');
    exit;
}

unset($_SESSION['supervisor_logged_in']);
unset($_SESSION['supervisor_id']);
unset($_SESSION['supervisor_name']);
unset($_SESSION['supervisor_email']);
unset($_SESSION['supervisor_bid']);
unset($_SESSION['supervisor_regno']);

session_destroy();
header('Location: ' . (isset($SITE_URL) ? $SITE_URL : '') . '/supervisor_login.php');
exit;
?>