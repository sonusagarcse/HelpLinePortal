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

    // Query to check healthcare credentials (support both regno and email)
    $query = "SELECT * FROM healthcare WHERE (regno = ? OR email = ?) AND status = 1";
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
        // Set session variables
        $_SESSION['healthcare_logged_in'] = true;
        $_SESSION['healthcare_id'] = $row['id'];
        $_SESSION['healthcare_regno'] = $row['regno'];
        $_SESSION['healthcare_name'] = $row['name'];
        $_SESSION['healthcare_email'] = $row['email'];
        $_SESSION['last_activity'] = time();

        // Redirect to admin panel (healthcare users use admin panel)
        header('Location: ../admin/index.php');
        exit;
    } else {
        // Invalid credentials
        header('Location: ../healthcare_login.php?msg=error');
        exit;
    }
} else {
    header('Location: ../healthcare_login.php');
    exit;
}
?>