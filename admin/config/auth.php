<?php
/**
 * Authentication Middleware
 * Checks if user is logged in and has proper permissions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in (Admin, Manager, Supervisor, or Healthcare)
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Admin is logged in
    $admin_id = $_SESSION['admin_id'];
    $admin_name = $_SESSION['admin_name'];
    $admin_email = $_SESSION['admin_email'];
    $admin_type = $_SESSION['admin_type']; // 1 = Super Admin, 0 = Admin
    $admin_bid = $_SESSION['admin_bid'];
}
else {
    // Determine redirect URL
    $redirect_url = (isset($SITE_URL) ? $SITE_URL : '') . '/admin/login.php';
    header('Location: ' . $redirect_url);
    exit;
}

// Session timeout check
$timeout_duration = 3600; // Increased to 60 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    $redirect_url = (isset($SITE_URL) ? $SITE_URL : '') . '/admin/login.php?timeout=1';
    header('Location: ' . $redirect_url);
    exit;
}
$_SESSION['last_activity'] = time();

/**
 * Check if user has permission for a specific action
 * @param string $module Module name
 * @param string $action Action type (view, create, edit, delete)
 * @return bool
 */
function hasPermission($module, $action)
{
    global $con, $admin_id, $admin_type;

    // Super admin (type 1) has all permissions
    if ($admin_type == 1) {
        return true;
    }

    // Check permissions table
    $query = "SELECT * FROM admin_permissions WHERE admin_id = ? AND module = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "is", $admin_id, $module);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        switch ($action) {
            case 'view':
                return $row['can_view'] == 1;
            case 'create':
                return $row['can_create'] == 1;
            case 'edit':
                return $row['can_edit'] == 1;
            case 'delete':
                return $row['can_delete'] == 1;
            default:
                return false;
        }
    }

    return false;
}

/**
 * Log admin activity
 * @param string $action Action performed
 * @param string $table_name Table affected
 * @param int $record_id Record ID
 * @param string $old_value Old value (JSON)
 * @param string $new_value New value (JSON)
 */
function logActivity($action, $table_name = null, $record_id = null, $old_value = null, $new_value = null)
{
    global $con, $admin_id;

    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $query = "INSERT INTO admin_logs (admin_id, action, table_name, record_id, old_value, new_value, ip_address, user_agent) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ississss", $admin_id, $action, $table_name, $record_id, $old_value, $new_value, $ip, $user_agent);
    mysqli_stmt_execute($stmt);
}
?>
