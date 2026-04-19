<?php
require_once(__DIR__ . '/../connection.php');
session_start();

if (!isset($_SESSION['caller_id'])) {
    header('Location: ' . (isset($SITE_URL) ? $SITE_URL : '') . '/caller_login.php');
    exit();
}

$caller_id = $_SESSION['caller_id'];

$caller_type = $_SESSION['caller_type'] ?? 'KYP';
$mquery_type = ($caller_type == 'UG_PG') ? 'UG_PG' : 'KYP';

// Get counts for boxes
$stats['completed_today'] = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(DISTINCT studentid) as total FROM mquery WHERE callerid = $caller_id AND query_type = '$mquery_type' AND (status = 0 OR (query_type = 'UG_PG' AND status = 2)) AND DATE(date) = CURDATE()"))['total'];
$stats['completed_month'] = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(DISTINCT studentid) as total FROM mquery WHERE callerid = $caller_id AND query_type = '$mquery_type' AND (status = 0 OR (query_type = 'UG_PG' AND status = 2)) AND MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())"))['total'];

// Get full list (Group by studentid to avoid duplicates if multiple calls were marked completed)
$query = "SELECT m.*, r.name as student_name, r.regno as student_regno, r.mob as student_mob, 
                 r.coordinator_approval_status, r.reg_status, r.ugpg_status, b.bname, ce.amount as earned_amount 
          FROM mquery m 
          JOIN registration r ON m.studentid = r.id 
          LEFT JOIN branch b ON r.bid = b.id 
          LEFT JOIN caller_earnings ce ON r.id = ce.student_id AND ce.caller_id = $caller_id
          WHERE m.callerid = $caller_id AND (m.status = 0 OR (m.query_type = 'UG_PG' AND m.status = 2)) AND m.query_type = '$mquery_type'
          AND m.id IN (
              SELECT MAX(mq.id) 
              FROM mquery mq 
              JOIN registration reg ON mq.studentid = reg.id 
              WHERE mq.callerid = $caller_id AND mq.status = 0 AND mq.query_type = '$mquery_type'
              GROUP BY reg.mob
          )
          ORDER BY m.id DESC";
$result = mysqli_query($con, $query);
$completed_calls = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Calls - <?php echo htmlspecialchars($SITE_NAME ?? 'Yuva Helpline'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #4facfe;
            --secondary-color: #00f2fe;
            --dark-color: #1a1a2e;
        }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background: linear-gradient(135deg, var(--dark-color) 0%, #16213e 100%); padding: 15px 0; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; }
        .table-card { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); padding: 30px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-chevron-left me-2"></i> Completed Calls
            </a>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <!-- Summary Boxes -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="stat-card" style="border-left: 5px solid #43e97b;">
                    <div class="d-flex align-items-center">
                        <div class="icon me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <h3 class="mb-0"><?php echo $stats['completed_today']; ?></h3>
                            <p class="mb-0 text-muted fw-bold">Completed Today</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card" style="border-left: 5px solid #2196f3;">
                    <div class="d-flex align-items-center">
                        <div class="icon me-3" style="background: linear-gradient(135deg, #2196f3 0%, #00bcd4 100%);">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h3 class="mb-0"><?php echo $stats['completed_month']; ?></h3>
                            <p class="mb-0 text-muted fw-bold">Completed This Month</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 font-weight-bold">
                    <i class="fas fa-list me-2 text-primary"></i>
                    All Completed Students
                </h4>
            </div>
            
            <div class="table-responsive">
                <table id="completedTable" class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Regno</th>
                            <th>Student</th>
                            <th>Mobile</th>
                            <th>Branch</th>
                            <th>Approval Status</th>
                            <th>Earnings</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($completed_calls as $call): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($call['student_regno'] ?? 'N/A'); ?></td>
                                <td><strong><?php echo htmlspecialchars($call['student_name'] ?? 'N/A'); ?></strong></td>
                                <td><?php echo htmlspecialchars($call['student_mob'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($call['bname'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php 
                                    if ($caller_type == 'UG_PG') {
                                        if ($call['ugpg_status'] == 1) {
                                            echo '<span class="badge bg-success shadow-sm rounded-pill px-3 py-2"><i class="fas fa-check-double me-1"></i>Admission Done</span>';
                                        } elseif ($call['status'] == 0) {
                                            echo '<span class="badge bg-secondary shadow-sm rounded-pill px-3 py-2"><i class="fas fa-check me-1"></i>Call Completed</span>';
                                        } else {
                                            echo '<span class="badge bg-light text-muted rounded-pill px-3 py-2">Unknown</span>';
                                        }
                                    } else {
                                        if ($call['coordinator_approval_status'] == 2) {
                                            echo '<span class="badge bg-success shadow-sm rounded-pill px-3 py-2"><i class="fas fa-check-double me-1"></i>Admission Approved</span>';
                                        } elseif ($call['coordinator_approval_status'] == 3) {
                                            echo '<span class="badge bg-danger shadow-sm rounded-pill px-3 py-2"><i class="fas fa-times-circle me-1"></i>Rejected</span>';
                                        } elseif ($call['reg_status'] == 2) {
                                            echo '<span class="badge bg-info text-white shadow-sm rounded-pill px-3 py-2"><i class="fas fa-user-check me-1"></i>Reg. Done (Awaiting Coord)</span>';
                                        } elseif ($call['reg_status'] == 1) {
                                            echo '<span class="badge bg-primary shadow-sm rounded-pill px-3 py-2"><i class="fas fa-paper-plane me-1"></i>Sent to Supervisor</span>';
                                        } elseif ($call['status'] == 0) {
                                            echo '<span class="badge bg-secondary shadow-sm rounded-pill px-3 py-2"><i class="fas fa-check me-1"></i>Call Completed</span>';
                                        } else {
                                            echo '<span class="badge bg-light text-muted rounded-pill px-3 py-2">Unknown</span>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    if($call['coordinator_approval_status'] == 2 && $call['earned_amount']) {
                                        echo '<span class="text-success fw-bold">+₹' . number_format($call['earned_amount'], 2) . '</span>';
                                    } else {
                                        echo '<span class="text-muted">-</span>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo date('d M Y', strtotime($call['date'])); ?></td>
                                <td>
                                    <?php $make_call_link = ($caller_type == 'UG_PG') ? 'make-call-ugpg.php' : 'make-call.php'; ?>
                                    <a href="<?php echo $make_call_link; ?>?id=<?php echo $call['studentid']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#completedTable').DataTable({
                order: [[5, 'desc']],
                pageLength: 10
            });
        });
    </script>
</body>
</html>

