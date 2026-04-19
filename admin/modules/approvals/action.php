<?php
require_once('../../config/config.php');
require_once('../../config/auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $student_id = (int)($_POST['student_id'] ?? 0);
    $caller_id = (int)($_POST['caller_id'] ?? 0);
    $mquery_id = (int)($_POST['mquery_id'] ?? 0);
    $query_type = $_POST['query_type'] ?? 'KYP';

    if ($student_id > 0) {
        
        // Helper to grab caller earning rate
        $earning_rate = 0;
        if ($caller_id > 0) {
            $e_result = mysqli_query($con, "SELECT earning_per_admission FROM caller WHERE id = $caller_id");
            if ($e_row = mysqli_fetch_assoc($e_result)) {
                $earning_rate = $e_row['earning_per_admission'];
            }
        }

        if ($action === 'force_approve') {
            mysqli_begin_transaction($con);
            try {
                // Update registration
                if ($query_type == 'UG_PG') {
                    mysqli_query($con, "UPDATE registration SET ugpg_status = 3 WHERE id = $student_id");
                } else {
                    mysqli_query($con, "UPDATE registration SET coordinator_approval_status = 2 WHERE id = $student_id");
                }
                
                // Ensure earning is credited if not already present
                $check = mysqli_query($con, "SELECT id FROM caller_earnings WHERE student_id = $student_id AND caller_id = $caller_id");
                if (mysqli_num_rows($check) == 0 && $caller_id > 0 && $earning_rate > 0) {
                    $e_stmt = mysqli_prepare($con, "INSERT INTO caller_earnings (caller_id, student_id, amount, date) VALUES (?, ?, ?, NOW())");
                    mysqli_stmt_bind_param($e_stmt, "iid", $caller_id, $student_id, $earning_rate);
                    mysqli_stmt_execute($e_stmt);
                }
                
                // Admin log
                // mysqli_query($con, "INSERT INTO admin_logs (admin_id, action, record_id, details) VALUES (".$_SESSION['admin_id'].", 'force_approve', $student_id, 'Admin forced approval on student')");
                
                mysqli_commit($con);
                header('Location: list.php?success=Approved');
            } catch(Exception $e) {
                mysqli_rollback($con);
                header('Location: list.php?error=db_error');
            }
            exit;

        } elseif ($action === 'force_reject') {
            mysqli_begin_transaction($con);
            try {
                // Update registration
                if ($query_type == 'UG_PG') {
                    mysqli_query($con, "UPDATE registration SET ugpg_status = 2 WHERE id = $student_id");
                } else {
                    mysqli_query($con, "UPDATE registration SET coordinator_approval_status = 3 WHERE id = $student_id");
                }
                
                // Revoke earning if it was given
                mysqli_query($con, "DELETE FROM caller_earnings WHERE student_id = $student_id");
                
                // Admin log
                // mysqli_query($con, "INSERT INTO admin_logs (admin_id, action, record_id, details) VALUES (".$_SESSION['admin_id'].", 'force_reject', $student_id, 'Admin forced rejection on student')");
                
                mysqli_commit($con);
                header('Location: list.php?success=Rejected');
            } catch(Exception $e) {
                mysqli_rollback($con);
                header('Location: list.php?error=db_error');
            }
            exit;

        } elseif ($action === 'reset_caller') {
            if ($mquery_id > 0) {
                mysqli_begin_transaction($con);
                try {
                    // Reset registration status to allow caller to act again
                    if ($query_type == 'UG_PG') {
                        mysqli_query($con, "UPDATE registration SET 
                            ugpg_status = 0
                            WHERE id = $student_id");
                    } else {
                        mysqli_query($con, "UPDATE registration SET 
                            coordinator_approval_status = 0, 
                            reg_status = 0, 
                            reg_ready_at = NULL 
                            WHERE id = $student_id");
                    }
                    
                    // Revoke any earnings
                    mysqli_query($con, "DELETE FROM caller_earnings WHERE student_id = $student_id");
                    
                    // Delete the "Resolved" interaction so caller can call again
                    mysqli_query($con, "DELETE FROM mquery WHERE id = $mquery_id AND status = 0");
                    
                    // Admin log
                    // mysqli_query($con, "INSERT INTO admin_logs (admin_id, action, record_id, details) VALUES (".$_SESSION['admin_id'].", 'reset_caller', $student_id, 'Admin unlocked caller submission')");
                    
                    mysqli_commit($con);
                    header('Location: list.php?success=Caller_Unlocked');
                } catch(Exception $e) {
                    mysqli_rollback($con);
                    header('Location: list.php?error=db_error');
                }
                exit;
            }
        } elseif ($action === 'reset_supervisor') {
            mysqli_begin_transaction($con);
            try {
                // Reset to regular 'Ready for Registration' (reg_status = 1)
                // Clear out coordinator approval if any
                // Clear the submitted credentials
                mysqli_query($con, "UPDATE registration SET 
                    reg_status = 1, 
                    coordinator_approval_status = 0,
                    reg_login_id = NULL,
                    reg_password = NULL,
                    submitted_by_supervisor = 0,
                    coordinator_approved_at = NULL
                    WHERE id = $student_id");
                    
                mysqli_commit($con);
                header('Location: list.php?success=Supervisor_Unlocked');
            } catch(Exception $e) {
                mysqli_rollback($con);
                header('Location: list.php?error=db_error');
            }
            exit;
        }
    }
}

header('Location: list.php');
exit;
?>
