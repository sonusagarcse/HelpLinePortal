<?php
session_start();

// Check if supervisor is logged in
if (!isset($_SESSION['supervisor_id'])) {
    header('Location: ' . (isset($SITE_URL) ? $SITE_URL : '') . '/supervisor_login.php');
    exit;
}

require_once(__DIR__ . '/../connection.php');

$supervisor_id = $_SESSION['supervisor_id'];
$supervisor_name = $_SESSION['supervisor_name'];
$supervisor_bid = $_SESSION['supervisor_bid'] ?? 0;

// Get statistics
$stats = [];

// Total callers under this supervisor
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM caller WHERE svid = $supervisor_id");
$stats['total_callers'] = mysqli_fetch_assoc($result)['total'];

// Active callers
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM caller WHERE svid = $supervisor_id AND status = 1");
$stats['active_callers'] = mysqli_fetch_assoc($result)['total'];

// Total calls made by supervised callers today
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM mquery q 
              JOIN caller c ON q.callerid = c.id 
              WHERE c.svid = $supervisor_id AND DATE(q.date) = CURDATE()");
$stats['today_calls'] = mysqli_fetch_assoc($result)['total'];

// Pending calls
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM mquery q 
              JOIN caller c ON q.callerid = c.id 
              WHERE c.svid = $supervisor_id AND q.status = 1");
$stats['pending_calls'] = mysqli_fetch_assoc($result)['total'];

// Get callers list
$query = "SELECT c.*, b.bname, 
          (SELECT COUNT(*) FROM mquery WHERE callerid = c.id) as total_calls,
          (SELECT COUNT(*) FROM mquery WHERE callerid = c.id AND DATE(date) = CURDATE()) as today_calls,
          (SELECT COUNT(*) FROM dataallotment WHERE callerid = c.id) as assigned_data
          FROM caller c 
          LEFT JOIN branch b ON c.bid = b.id 
          WHERE c.svid = $supervisor_id 
          ORDER BY c.id DESC";
$callers = [];
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $callers[] = $row;
}

// Get recent calls by supervised callers
$query = "SELECT q.*, c.name as caller_name, b.bname 
          FROM mquery q 
          JOIN caller c ON q.callerid = c.id 
          LEFT JOIN branch b ON q.bid = b.id 
          WHERE c.svid = $supervisor_id 
          ORDER BY q.id DESC 
          LIMIT 20";
$recent_calls = [];
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $recent_calls[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard - <?php echo $SITE_NAME ?? 'Yuva Helpline'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card:nth-child(1) {
            border-left-color: #667eea;
        }

        .stat-card:nth-child(2) {
            border-left-color: #43e97b;
        }

        .stat-card:nth-child(3) {
            border-left-color: #f093fb;
        }

        .stat-card:nth-child(4) {
            border-left-color: #4facfe;
        }

        .stat-card .icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .stat-card h3 {
            font-size: 36px;
            font-weight: bold;
            margin: 10px 0 5px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .table-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .table-card h5 {
            color: #667eea;
            font-weight: 600;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
        }

        .btn-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border: none;
        }

        .btn-info:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(79, 172, 254, 0.4);
        }

        .page-title {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .quick-action-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .quick-action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-user-tie me-2"></i>Supervisor Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="callers.php">
                            <i class="fas fa-users me-1"></i>My Callers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="assign_data.php">
                            <i class="fas fa-tasks me-1"></i>Assign Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-chart-bar me-1"></i>Reports
                        </a>
                    </li>
                </ul>
                <div class="ms-3 d-flex align-items-center">
                    <span class="text-white me-3">
                        <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($supervisor_name); ?>
                    </span>
                    <a href="logout.php" class="btn btn-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Page Title -->
        <div class="page-title">
            <h4 class="mb-0">
                <i class="fas fa-chart-line me-2 text-primary"></i>
                Supervisor Dashboard
            </h4>
            <p class="text-muted mb-0 mt-2">Monitor your team's performance and manage calling assignments</p>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-3">
            <div class="col-md-12">
                <a href="assign_data.php" class="text-decoration-none">
                    <div class="quick-action-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1"><i class="fas fa-tasks me-2"></i>Assign Calling Data</h5>
                                <p class="mb-0 opacity-75">Assign students to your callers for follow-up calls</p>
                            </div>
                            <i class="fas fa-arrow-right fa-2x"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="ms-3">
                            <h3><?php echo $stats['total_callers']; ?></h3>
                            <p class="mb-0 text-muted">Total Callers</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="ms-3">
                            <h3><?php echo $stats['active_callers']; ?></h3>
                            <p class="mb-0 text-muted">Active Callers</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="ms-3">
                            <h3><?php echo $stats['today_calls']; ?></h3>
                            <p class="mb-0 text-muted">Today's Calls</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="ms-3">
                            <h3><?php echo $stats['pending_calls']; ?></h3>
                            <p class="mb-0 text-muted">Pending Calls</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Callers Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-card">
                    <h5 class="mb-3"><i class="fas fa-users me-2"></i>My Callers Performance</h5>
                    <div class="table-responsive">
                        <table id="callersTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Regno</th>
                                    <th>Name</th>
                                    <th>Branch</th>
                                    <th>Mobile</th>
                                    <th>Total Calls</th>
                                    <th>Today's Calls</th>
                                    <th>Assigned Data</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($callers as $caller): ?>
                                    <tr>
                                        <td><?php echo $caller['id']; ?></td>
                                        <td><?php echo htmlspecialchars($caller['regno']); ?></td>
                                        <td><?php echo htmlspecialchars($caller['name']); ?></td>
                                        <td><?php echo htmlspecialchars($caller['bname'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($caller['mob']); ?></td>
                                        <td><span class="badge bg-primary"><?php echo $caller['total_calls']; ?></span></td>
                                        <td><span class="badge bg-info"><?php echo $caller['today_calls']; ?></span></td>
                                        <td><span class="badge bg-secondary"><?php echo $caller['assigned_data']; ?></span>
                                        </td>
                                        <td>
                                            <?php if ($caller['status'] == 1): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="caller_details.php?id=<?php echo $caller['id']; ?>"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
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

        <!-- Recent Calls -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-card">
                    <h5 class="mb-3"><i class="fas fa-history me-2"></i>Recent Calls by My Team</h5>
                    <div class="table-responsive">
                        <table id="callsTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Caller</th>
                                    <th>Branch</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_calls as $call): ?>
                                    <tr>
                                        <td><?php echo $call['id']; ?></td>
                                        <td><?php echo htmlspecialchars($call['caller_name']); ?></td>
                                        <td><?php echo htmlspecialchars($call['bname'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars(substr($call['des'], 0, 50)); ?>...</td>
                                        <td><?php echo htmlspecialchars($call['date']); ?></td>
                                        <td>
                                            <?php if ($call['status'] == 1): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php endif; ?>
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

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#callersTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10
            });
            $('#callsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10
            });
        });
    </script>
</body>

</html>