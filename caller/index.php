<?php
session_start();

// Check if caller is logged in
if (!isset($_SESSION['caller_id'])) {
    header('Location: ' . (isset($SITE_URL) ? $SITE_URL : '') . '/caller_login.php');
    exit;
}

require_once(__DIR__ . '/../connection.php');

$caller_id = $_SESSION['caller_id'];
$caller_name = $_SESSION['caller_name'];

$where_clause = "(
    EXISTS (SELECT 1 FROM caller_branches cb WHERE cb.caller_id = $caller_id AND cb.status = 1 AND cb.branch_id = r.bid AND (cb.category_id = 0 OR cb.category_id = r.mcategory))
    OR r.assigned_caller = $caller_id
) AND r.status = 1";

// 1. Total Assigned & Statistics (Optimized with a single pass or efficient subqueries)
// Get latest query status for all relevant students in one go
$latest_mq_subquery = "SELECT studentid, status, nextdate, des
                      FROM mquery 
                      WHERE callerid = $caller_id 
                      AND id IN (SELECT MAX(id) FROM mquery WHERE callerid = $caller_id GROUP BY studentid)";

$total_query = "SELECT COUNT(*) as total FROM registration r 
                LEFT JOIN ($latest_mq_subquery) mq_latest ON r.id = mq_latest.studentid
                WHERE $where_clause 
                AND (mq_latest.status NOT IN (0, 2) OR mq_latest.status IS NULL)";
$result = mysqli_query($con, $total_query);
$stats['total_assigned'] = mysqli_fetch_assoc($result)['total'];

// Completed calls
$result_unique = mysqli_query($con, "SELECT COUNT(DISTINCT studentid) as total FROM mquery WHERE callerid = $caller_id AND status = 0");
$stats['completed'] = mysqli_fetch_assoc($result_unique)['total'];

// 3. Pending Data
$precise_pending_query = "SELECT COUNT(*) as total FROM registration r
                         LEFT JOIN ($latest_mq_subquery) mq_latest ON r.id = mq_latest.studentid
                         WHERE $where_clause
                         AND (mq_latest.status IS NULL OR mq_latest.status = 1) 
                         AND (mq_latest.nextdate IS NULL OR mq_latest.nextdate <= CURDATE())";
$result = mysqli_query($con, $precise_pending_query);
$stats['pending'] = mysqli_fetch_assoc($result)['total'];

// Today's calls
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM mquery WHERE callerid = $caller_id AND DATE(date) = CURDATE()");
$stats['today'] = mysqli_fetch_assoc($result)['total'];

// Get assigned data (The main loop)
$query = "SELECT r.*, r.id as student_id, r.regno as student_regno, r.name as student_name, 
                 r.father as student_father, r.mob as student_mob, r.email as student_email, 
                 r.address as student_address, r.village as student_village, r.dis as student_dis, 
                 r.state as student_state, r.pincode as student_pincode, 
                 mc.name as category_name, b.bname, b.bcode,
                 mq_latest.nextdate, mq_latest.status as latest_status, mq_latest.des as latest_remarks
          FROM registration r 
          LEFT JOIN member_category mc ON r.mcategory = mc.id 
          LEFT JOIN branch b ON r.bid = b.id
          LEFT JOIN ($latest_mq_subquery) mq_latest ON r.id = mq_latest.studentid
          WHERE $where_clause
          HAVING (latest_status IS NULL OR latest_status = 1) AND (nextdate IS NULL OR nextdate <= CURDATE())
          ORDER BY (nextdate IS NOT NULL) DESC, nextdate ASC, r.id DESC";
$assigned_data = [];
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_assoc($result)) {
    // Standardize array structure
    $row['remarks'] = $row['latest_remarks'] ?: $row['caller_remark'];
    $assigned_data[] = $row;
}

// Filter Today's Follow-ups based on the latest mquery nextdate
$todays_followups = array_filter($assigned_data, function ($item) {
    return isset($item['nextdate']) && $item['nextdate'] == date('Y-m-d');
});

// Get recent queries/calls
$query = "SELECT q.*, b.bname, r.name as student_name 
          FROM mquery q 
          LEFT JOIN branch b ON q.bid = b.id 
          LEFT JOIN registration r ON q.studentid = r.id
          WHERE q.callerid = $caller_id 
          ORDER BY q.id DESC 
          LIMIT 5";
$recent_calls = [];
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $recent_calls[] = $row;
}

$followup_count = count($todays_followups);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caller Dashboard - <?php echo $SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        body {
            background: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            flex-shrink: 0;
        }

        .stat-card h3 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .stat-card p {
            font-size: 0.85rem;
            margin: 0;
        }

        .table-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .followup-card {
            border-left: 5px solid #ffc107;
            background: #fffbef;
        }

        .stat-card.highlight {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);
            border: 2px solid #f5576c;
        }

        .stat-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .blinking-badge {
            display: inline-block;
            background: #f5576c;
            color: white;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 1rem;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 0 10px rgba(245, 87, 108, 0.5);
            animation: blinker 1.5s linear infinite;
        }

        @keyframes blinker {
            50% { opacity: 0; }
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 10px;
                padding-right: 10px;
            }
            .stat-card h3 {
                font-size: 20px;
            }
            .navbar-brand {
                font-size: 1.1rem;
            }
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-headset me-2"></i>Caller Dashboard
            </a>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-white me-3">
                    <i class="fas fa-user me-2"></i><?php echo $caller_name; ?>
                </span>
                <a href="logout.php" class="btn btn-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="ms-3">
                            <h3><?php echo $stats['total_assigned']; ?></h3>
                            <p class="mb-0 text-muted">Assigned</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <a href="todays-calls.php" class="stat-link">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="ms-3">
                                <h3><?php echo $stats['today']; ?></h3>
                                <p class="mb-0 text-muted">Today Calls</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="ms-3">
                            <h3><?php echo $stats['pending']; ?></h3>
                            <p class="mb-0 text-muted">Pending</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <a href="completed-calls.php" class="stat-link">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h3><?php echo $stats['completed']; ?></h3>
                                <p class="mb-0 text-muted">Completed</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>



        <div class="row">
            <div class="col-md-12">
                <?php if (!empty($todays_followups)): ?>
                    <div class="table-card mb-4" style="border-left: 5px solid #f5576c;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 text-danger">
                                <i class="fas fa-calendar-check me-2"></i>Top 5 Today's Follow-ups
                                <?php if ($followup_count > 0): ?>
                                    <a href="todays-followups.php" class="blinking-badge ms-2"><?php echo $followup_count; ?></a>
                                <?php endif; ?>
                            </h5>
                            <a href="todays-followups.php" class="btn btn-sm btn-outline-danger">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Regno</th>
                                        <th>Student Name</th>
                                        <th>Mobile</th>
                                        <th>Remarks</th>
                                        <th>Branch</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $count = 0;
                                    foreach ($todays_followups as $data): 
                                        if ($count >= 5) break;
                                        $count++;
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($data['student_regno'] ?? 'N/A'); ?></td>
                                            <td><strong><?php echo htmlspecialchars($data['student_name'] ?? 'N/A'); ?></strong></td>
                                            <td><?php echo htmlspecialchars($data['student_mob'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($data['remarks'] ?? 'No remarks'); ?></td>
                                            <td><?php echo htmlspecialchars($data['bname'] ?? 'N/A'); ?></td>
                                            <td>
                                                <a href="make-call.php?id=<?php echo $data['id']; ?>&student_id=<?php echo $data['student_id']; ?>"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-phone"></i> Call
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-card">
                    <h5 class="mb-3"><i class="fas fa-users me-2"></i>Assigned Students</h5>
                    <div class="table-responsive">
                        <table id="assignedTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Regno</th>
                                    <th>Student Name</th>
                                    <th>Father Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Branch</th>
                                    <th>Category</th>
                                    <th>Next Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assigned_data as $data): ?>
                                    <tr>
                                        <td><?php echo $data['id']; ?></td>
                                        <td><?php echo htmlspecialchars($data['student_regno'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($data['student_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($data['student_father'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($data['student_mob'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($data['student_email'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php
                                            $address_parts = array_filter([
                                                $data['student_address'] ?? '',
                                                $data['student_village'] ?? '',
                                                $data['student_dis'] ?? '',
                                                $data['student_state'] ?? '',
                                                $data['student_pincode'] ?? ''
                                            ]);
                                            echo htmlspecialchars(implode(', ', $address_parts) ?: 'N/A');
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($data['bname'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($data['category_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($data['nextdate'] ?? 'Pending'); ?></td>
                                        <td>
                                            <a href="make-call.php?id=<?php echo $data['id']; ?>&student_id=<?php echo $data['student_id']; ?>"
                                                class="btn btn-sm btn-success">
                                                <i class="fas fa-phone"></i> Call
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#assignedTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100]
            });
        });
    </script>
</body>

</html>