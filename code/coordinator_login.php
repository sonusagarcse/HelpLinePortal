<?php
session_start();
require_once('../connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = $_POST['password'];

    // Check centre_coordinator table (Allow login by username or email)
    $query = "SELECT * FROM centre_coordinator WHERE (username = ? OR email = ?) AND status = 1";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['pass'])) {
            // Login Success
            $_SESSION['coordinator_id'] = $row['id'];
            $_SESSION['coordinator_name'] = $row['name'];
            $_SESSION['coordinator_bid'] = $row['bid'];
            $_SESSION['coordinator_email'] = $row['email'];

            header('Location: ../coordinator/index.php');
            exit;
        } else {
            // Invalid Password
            header('Location: ../coordinator_login.php?msg=error');
            exit;
        }
    } else {
        // Invalid Username/Email or Inactive
        header('Location: ../coordinator_login.php?msg=error');
        exit;
    }
} else {
    header('Location: ../coordinator_login.php');
    exit;
}
?>
