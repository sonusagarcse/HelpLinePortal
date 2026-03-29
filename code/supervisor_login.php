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

    // Query to check supervisor credentials (support both regno and email)
    $query = "SELECT * FROM supervisor WHERE (regno = ? OR email = ?) AND status = 1";
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
            $_SESSION['supervisor_logged_in'] = true;
            $_SESSION['supervisor_id'] = $row['id'];
            $_SESSION['supervisor_regno'] = $row['regno'];
            $_SESSION['supervisor_name'] = $row['name'];
            $_SESSION['supervisor_email'] = $row['email'];
            $_SESSION['last_activity'] = time();

            // Fetch all assigned branches
            $bids = [];
            $b_query = mysqli_query($con, "SELECT branch_id FROM supervisor_branches WHERE supervisor_id = " . $row['id'] . " AND status = 1");
            while ($b_row = mysqli_fetch_assoc($b_query)) {
                $bids[] = $b_row['branch_id'];
            }
            $_SESSION['supervisor_bids'] = $bids;
            $_SESSION['supervisor_bid'] = !empty($bids) ? $bids[0] : 0; // Primary branch (first one)

            // Redirect to supervisor dashboard
            header('Location: ../supervisor/index.php');
            exit;
        } else {
            // Invalid credentials
            header('Location: ../supervisor_login?msg=error');
            exit;
        }
    } else {
        // User not found
        header('Location: ../supervisor_login?msg=error');
        exit;
    }
} else {
    // Not a POST request
    header('Location: ../supervisor_login');
    exit;
}
?>