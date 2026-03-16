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

// Get caller details
$caller_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Verify caller belongs to this supervisor
$query = "SELECT c.*, b.bname, b.bcode 
          FROM caller c 
          LEFT JOIN branch b ON c.bid = b.id 
          WHERE c.id = ? AND c.svid = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ii", $caller_id, $supervisor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$caller = mysqli_fetch_assoc($result);

if (!$caller) {
    header('Location: index.php?error=not_found');
    exit;
}

// Get caller statistics
$stats = [];

// Total calls
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM mquery WHERE callerid = $caller_id");
$stats['total_calls'] = mysqli_fetch_assoc($result)['total'];

// Completed calls
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM mquery WHERE callerid = $caller_id AND status = 0");
$stats['completed'] = mysqli_fetch_assoc($result)['total'];

// Pending calls
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM mquery WHERE callerid = $caller_id AND status = 1");
$stats['pending'] = mysqli_fetch_assoc($result)['total'];

// Today's calls
$result = mysqli_query($con, "SELECT COUNT(*) as total FROM mquery WHERE callerid = $caller_id AND DATE(date) = CURDATE()");
$stats['today'] = mysqli_fetch_assoc($result)['total'];

// Get call history
$query = "SELECT q.*, b.bname 
          FROM mquery q 
          LEFT JOIN branch b ON q.bid = b.id 
          WHERE q.callerid = $caller_id 
          ORDER BY q.id DESC 
          LIMIT 50";
$calls = [];
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $calls[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caller Details - Supervisor Dashboard</title>
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

        .info-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
        }

        .stat-card h4 {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-user-tie me-2"></i>Supervisor Dashboard
            </a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
                <a href="logout.php" class="btn btn-light btn-sm ms-2">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Caller Information -->
            <div class="col-md-4">
                <div class="info-card">
                    <h5 class="mb-3"><i class="fas fa-user me-2"></i>Caller Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Regno:</strong></td>
                            <td><?php echo htmlspecialchars($caller['regno']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?php echo htmlspecialchars($caller['name']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Father:</strong></td>
                            <td><?php echo htmlspecialchars($caller['father']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mobile:</strong></td>
                            <td><?php echo htmlspecialchars($caller['mob']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><?php echo htmlspecialchars($caller['email']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Branch:</strong></td>
                            <td><?php echo htmlspecialchars($caller['bcode'] . ' - ' . $caller['bname']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>DOJ:</strong></td>
                            <td><?php echo date('d-m-Y', strtotime($caller['doj'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <?php if ($caller['status'] == 1): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Performance Stats -->
                <div class="row">
                    <div class="col-6">
                        <div class="stat-card">
                            <i class="fas fa-phone fa-2x text-primary"></i>
                            <h4><?php echo $stats['total_calls']; ?></h4>
                            <p class="mb-0 text-muted">Total Calls</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                            <h4><?php echo $stats['completed']; ?></h4>
                            <p class="mb-0 text-muted">Completed</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                            <h4><?php echo $stats['pending']; ?></h4>
                            <p class="mb-0 text-muted">Pending</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <i class="fas fa-calendar-day fa-2x text-info"></i>
                            <h4><?php echo $stats['today']; ?></h4>
                            <p class="mb-0 text-muted">Today</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call History -->
            <div class="col-md-8">
                <div class="info-card">
                    <h5 class="mb-3"><i class="fas fa-history me-2"></i>Call History</h5>
                    <div class="table-responsive">
                        <table id="callsTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Branch</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($calls as $call): ?>
                                    <tr>
                                        <td><?php echo $call['id']; ?></td>
                                        <td><?php echo htmlspecialchars($call['bname'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars(substr($call['des'], 0, 100)); ?></td>
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
            $('#callsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25
            });
        });
    </script>
</body>

</html>