<?php
session_start();
require_once(dirname(__DIR__) . '/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    $username = mysqli_real_escape_string($con, $_POST['regno']);
    $password = $_POST['password'];

    $query = "SELECT * FROM deo WHERE (regno = ? OR email = ?) AND status = 1";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['pass'])) {
            session_regenerate_id(true);
            $_SESSION['deo_logged_in'] = true;
            $_SESSION['deo_id'] = $row['id'];
            $_SESSION['deo_name'] = $row['name'];
            $_SESSION['deo_email'] = $row['email'];
            $_SESSION['deo_bid'] = $row['bid'];
            $_SESSION['last_activity'] = time();

            header('Location: ../deo/index.php');
            exit;
        }
    }
    header('Location: ../deo_login?msg=error');
    exit;
}
?>
