<?php
session_start();
require_once(dirname(__DIR__) . '/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    $username = mysqli_real_escape_string($con, $_POST['regno']); // Can be regno or email
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Query to check caller credentials (support both regno and email)
    $query = "SELECT * FROM caller WHERE (regno = ? OR email = ?) AND status = 1";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Verify hashed password
        if (password_verify($password, $row['pass'])) {
            // Regenerate session ID
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['caller_logged_in'] = true;
            $_SESSION['caller_id'] = $row['id'];
            $_SESSION['caller_regno'] = $row['regno'];
            $_SESSION['caller_name'] = $row['name'];
            $_SESSION['caller_email'] = $row['email'];
            $_SESSION['caller_bid'] = $row['bid'];
            $_SESSION['caller_svid'] = $row['svid'];
            $_SESSION['caller_type'] = $row['caller_type'] ?? 'KYP';
            $_SESSION['last_activity'] = time();

            // Redirect to caller dashboard
            header('Location: ../caller/index.php');
            exit;
        } else {
            // Invalid credentials
            header('Location: ../caller_login?msg=error');
            exit;
        }
    } else {
        // User not found
        header('Location: ../caller_login?msg=error');
        exit;
    }
} else {
    // Not a POST request
    header('Location: ../caller_login');
    exit;
}
?>