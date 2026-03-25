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

// Total Earnings (from Coordinator Approvals)
$earnings_query = "SELECT SUM(amount) as total_earnings FROM caller_earnings WHERE caller_id = $caller_id";
$earnings_result = mysqli_query($con, $earnings_query);
$stats['earnings'] = mysqli_fetch_assoc($earnings_result)['total_earnings'] ?? 0;

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            --secondary-gradient: linear-gradient(135deg, #f43f5e 0%, #fb7185 100%);
            --accent-gradient: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.4);
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --header-bg: #0f172a;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        body {
            background-color: #f1f5f9;
            background-image: radial-gradient(#6366f1 0.5px, #f1f5f9 0.5px);
            background-size: 24px 24px;
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        .ambient-blob {
            position: fixed;
            width: 40vmax;
            height: 40vmax;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(139, 92, 246, 0.05) 100%);
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
            animation: float 20s infinite alternate linear;
        }

        @keyframes float {
            0% { transform: translate(-10%, -10%) rotate(0deg); }
            100% { transform: translate(20%, 30%) rotate(360deg); }
        }

        h1, h2, h3, h4, h5, .navbar-brand {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
        }

        /* Navbar Styling */
        .navbar {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 1rem 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            letter-spacing: -0.025em;
        }

        /* Stat Card Styling */
        .stat-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 1.25rem;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            opacity: 0.8;
        }

        .stat-card.primary::before { background: var(--primary-gradient); }
        .stat-card.secondary::before { background: var(--secondary-gradient); }
        .stat-card.accent::before { background: var(--accent-gradient); }
        .stat-card.warning::before { background: var(--warning-gradient); }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .stat-card .icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-right: 1.25rem;
            flex-shrink: 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            font-size: 2rem;
            letter-spacing: -0.05em;
            margin-bottom: 0.25rem;
        }

        .stat-card p {
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        /* Table Card Styling */
        .table-card {
            background: white;
            border-radius: 1.25rem;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .table-card h5 {
            color: var(--text-dark);
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .table-card h5 i {
            width: 2.5rem;
            height: 2.5rem;
            background: #f8fafc;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6366f1;
            margin-right: 1rem;
            font-size: 1rem;
        }

        /* DataTable Overrides */
        .table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 1rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-gradient) !important;
            border: none !important;
            border-radius: 0.5rem !important;
            color: white !important;
        }

        /* Special Components */
        .followup-alert {
            background: linear-gradient(135deg, #fff1f2 0%, #fff 100%);
            border-left: 6px solid #f43f5e;
            position: relative;
        }

        .blinking-badge {
            background: #f43f5e;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            animation: pulse-red 2s infinite;
        }

        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(244, 63, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(244, 63, 94, 0); }
        }

        .btn-premium {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 0.75rem;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);
        }

        .btn-premium:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
            color: white;
        }

        .stat-link { text-decoration: none; color: inherit; }

        @media (max-width: 768px) {
            .stat-card { padding: 1rem; }
            .stat-card h3 { font-size: 1.5rem; }
        }
    </style>
</head>

<body>
    <div class="ambient-blob"></div>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <div class="bg-primary bg-gradient rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="fas fa-headset text-white"></i>
                </div>
                <span class="fw-bold">Caller <span class="text-primary-emphasis">Portal</span></span>
            </a>
            
            <div class="ms-auto d-flex align-items-center">
                <div class="d-none d-md-flex flex-column align-items-end me-4">
                    <span class="text-white-50 small text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.1em;">Logged In As</span>
                    <span class="text-white fw-bold"><?php echo $caller_name; ?></span>
                </div>
                <div class="vr bg-white opacity-25 mx-3 d-none d-md-block" style="height: 30px;"></div>
                <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fas fa-power-off me-2"></i>Sign Out
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 mt-4">
        <!-- Dashboard Greeting -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between bg-white p-4 rounded-4 shadow-sm border border-white">
                    <div>
                        <h2 class="h4 mb-1">Welcome back, <span class="text-primary"><?php echo $caller_name; ?></span>!</h2>
                        <p class="text-muted small mb-0">Here's what's happening with your students today.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 px-lg-3 py-2 rounded-pill fs-6 shadow-sm w-100 w-lg-auto d-flex justify-content-center">
                            <i class="fas fa-wallet me-2"></i>Total Earnings: ₹<?php echo number_format($stats['earnings'], 2); ?>
                        </span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill shadow-sm d-none d-md-inline-block">
                            <i class="fas fa-calendar-alt me-2"></i><?php echo date('M d, Y'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <div class="icon" style="background: var(--primary-gradient);">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <h3><?php echo number_format($stats['total_assigned']); ?></h3>
                        <p>Total Assigned</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <a href="todays-calls.php" class="stat-link">
                    <div class="stat-card secondary">
                        <div class="icon" style="background: var(--secondary-gradient);">
                            <i class="fas fa-phone-volume"></i>
                        </div>
                        <div>
                            <h3><?php echo number_format($stats['today']); ?></h3>
                            <p>Calls Handled Today</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="icon" style="background: var(--warning-gradient);">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <h3><?php echo number_format($stats['pending']); ?></h3>
                        <p>Pending Action</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <a href="completed-calls.php" class="stat-link">
                    <div class="stat-card accent">
                        <div class="icon" style="background: var(--accent-gradient);">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <h3><?php echo number_format($stats['completed']); ?></h3>
                            <p>Successfully Closed</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>



        <div class="row">
            <div class="col-md-12">
                <?php if (!empty($todays_followups)): ?>
                    <div class="table-card followup-alert">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-check" style="color: #f43f5e; background: #fff1f2;"></i>
                                Today's Critical Follow-ups
                                <?php if ($followup_count > 0): ?>
                                    <span class="blinking-badge ms-3"><?php echo $followup_count; ?> REQ</span>
                                <?php endif; ?>
                            </h5>
                            <a href="todays-followups.php" class="btn btn-sm btn-outline-danger rounded-pill px-4">See All</a>
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
                                                    class="btn btn-premium btn-sm">
                                                    <i class="fas fa-phone me-1"></i> Call Now
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
                    <h5><i class="fas fa-users-viewfinder"></i> assigned Students Directory</h5>
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
                                                    class="btn btn-premium btn-sm">
                                                    <i class="fas fa-phone me-1"></i> Call Now
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