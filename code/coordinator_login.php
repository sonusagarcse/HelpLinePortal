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
            $_SESSION['coordinator_email'] = $row['email'];
            
            // Fetch all assigned branches
            $bids = [];
            $b_query = mysqli_query($con, "SELECT branch_id FROM coordinator_branches WHERE coordinator_id = " . $row['id'] . " AND status = 1");
            if ($b_query) {
                while($b_row = mysqli_fetch_assoc($b_query)){
                    $bids[] = $b_row['branch_id'];
                }
            }
            $_SESSION['coordinator_bids'] = $bids;
            
            // Legacy fallback if needed (useful before full migration)
            if (empty($bids) && $row['bid'] > 0) {
                $_SESSION['coordinator_bids'] = [$row['bid']];
                $_SESSION['coordinator_bid'] = $row['bid'];
            } else {
                $_SESSION['coordinator_bid'] = !empty($bids) ? $bids[0] : 0;
            }

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
