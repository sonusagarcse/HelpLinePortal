<?php
session_start();

// Log the logout
if (isset($_SESSION['admin_id'])) {
    require_once(__DIR__ . '/config/config.php');

    $admin_id = $_SESSION['admin_id'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $query = "INSERT INTO admin_logs (admin_id, action, ip_address, user_agent) VALUES (?, 'logout', ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "iss", $admin_id, $ip, $user_agent);
    mysqli_stmt_execute($stmt);
}

// Destroy all role sessions
unset($_SESSION['admin_logged_in']);
unset($_SESSION['manager_logged_in']);
unset($_SESSION['supervisor_logged_in']);
unset($_SESSION['healthcare_logged_in']);
unset($_SESSION['branch_logged_in']);
unset($_SESSION['caller_logged_in']);

// Destroy session
session_unset();
session_destroy();

// Redirect to login (root)
header('Location: ../index.php?logout=1');
exit;
?>
