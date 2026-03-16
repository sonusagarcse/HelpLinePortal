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

// Get all callers under this supervisor
$query = "SELECT c.*, b.bname, b.bcode,
          (SELECT COUNT(*) FROM mquery WHERE callerid = c.id) as total_calls,
          (SELECT COUNT(*) FROM mquery WHERE callerid = c.id AND status = 0) as completed_calls,
          (SELECT COUNT(*) FROM mquery WHERE callerid = c.id AND DATE(date) = CURDATE()) as today_calls
          FROM caller c 
          LEFT JOIN branch b ON c.bid = b.id 
          WHERE c.svid = $supervisor_id 
          ORDER BY c.name ASC";
$callers = [];
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $callers[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Callers - Supervisor Dashboard</title>
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

        .table-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="callers.php">
                            <i class="fas fa-users me-1"></i>My Callers
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
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5><i class="fas fa-users me-2"></i>My Callers (<?php echo count($callers); ?>)</h5>
            </div>
            <div class="table-responsive">
                <table id="callersTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Regno</th>
                            <th>Name</th>
                            <th>Father</th>
                            <th>Branch</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Total Calls</th>
                            <th>Completed</th>
                            <th>Today</th>
                            <th>DOJ</th>
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
                                <td><?php echo htmlspecialchars($caller['father']); ?></td>
                                <td><?php echo htmlspecialchars($caller['bcode'] . ' - ' . $caller['bname']); ?></td>
                                <td><?php echo htmlspecialchars($caller['mob']); ?></td>
                                <td><?php echo htmlspecialchars($caller['email']); ?></td>
                                <td><span class="badge bg-primary"><?php echo $caller['total_calls']; ?></span></td>
                                <td><span class="badge bg-success"><?php echo $caller['completed_calls']; ?></span></td>
                                <td><span class="badge bg-info"><?php echo $caller['today_calls']; ?></span></td>
                                <td><?php echo date('d-m-Y', strtotime($caller['doj'])); ?></td>
                                <td>
                                    <?php if ($caller['status'] == 1): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="caller_details.php?id=<?php echo $caller['id']; ?>" class="btn btn-sm btn-info"
                                        title="View Details">
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

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#callersTable').DataTable({
                order: [[1, 'asc']],
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
</body>

</html>