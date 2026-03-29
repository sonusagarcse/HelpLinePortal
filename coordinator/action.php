<?php
require_once('../connection.php');
require_once('config/auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php';

    if ($student_id > 0) {
        // Verify student belongs to this coordinator's branch and is pending
        $query = "SELECT m.callerid, c.earning_per_admission 
                  FROM registration r 
                  LEFT JOIN mquery m ON m.studentid = r.id AND m.status = 0
                  LEFT JOIN caller c ON m.callerid = c.id 
                  WHERE r.id = ? AND r.bid = ? AND r.coordinator_approval_status = 1
                  ORDER BY m.id DESC LIMIT 1";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ii", $student_id, $coordinator_bid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $student = mysqli_fetch_assoc($result);

        if ($student) {
            $caller_id = $student['callerid'];
            $earning_amount = $student['earning_per_admission'] ?? 0;

            if ($action === 'approve') {
                mysqli_begin_transaction($con);
                try {
                    // Update registration status to approved
                    $u_stmt = mysqli_prepare($con, "UPDATE registration SET coordinator_approval_status = 2 WHERE id = ?");
                    mysqli_stmt_bind_param($u_stmt, "i", $student_id);
                    mysqli_stmt_execute($u_stmt);

                    // Add earning to caller_earnings tracking table
                    if ($caller_id > 0 && $earning_amount > 0) {
                        $e_stmt = mysqli_prepare($con, "INSERT INTO caller_earnings (caller_id, student_id, amount, date) VALUES (?, ?, ?, NOW())");
                        mysqli_stmt_bind_param($e_stmt, "iid", $caller_id, $student_id, $earning_amount);
                        mysqli_stmt_execute($e_stmt);
                    }
                    mysqli_commit($con);
                    header("Location: $redirect?success=approved");
                } catch(Exception $e) {
                    mysqli_rollback($con);
                    header("Location: $redirect?error=db_error");
                }
                exit;

            } elseif ($action === 'reject') {
                $u_stmt = mysqli_prepare($con, "UPDATE registration SET coordinator_approval_status = 3 WHERE id = ?");
                mysqli_stmt_bind_param($u_stmt, "i", $student_id);
                mysqli_stmt_execute($u_stmt);
                header("Location: $redirect?success=rejected");
                exit;
            }
        }
    }
}
header('Location: index.php');
exit;
?>
