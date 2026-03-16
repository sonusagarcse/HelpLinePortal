<?php
session_start();
require_once(dirname(__DIR__) . '/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    $username = mysqli_real_escape_string($con, $_POST['regno']); // Can be regno or username
    $password = $_POST['password'];

    // Query to check branch credentials (support both username and regno)
    $query = "SELECT * FROM branch WHERE (username = ? OR regno = ?) AND status = 1";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Verify hashed password
        if (password_verify($password, $row['pass'])) {
            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['branch_logged_in'] = true;
            $_SESSION['branch_id'] = $row['id'];
            $_SESSION['branch_name'] = $row['bname'];
            $_SESSION['branch_email'] = $row['bemail'];
            $_SESSION['branch_code'] = $row['bcode'];
            $_SESSION['last_activity'] = time();

            // Redirect to admin panel (branch users use admin panel)
            header('Location: ../admin/index.php');
            exit;
        } else {
            // Invalid password
            header('Location: ../branch_login?msg=error');
            exit;
        }
    } else {
        // Branch not found or inactive
        header('Location: ../branch_login?msg=error');
        exit;
    }
} else {
    header('Location: ../branch_login');
    exit;
}
?>
