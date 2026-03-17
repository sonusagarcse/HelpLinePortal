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

// Get all calls made by the caller today
$query = "SELECT q.*, b.bname, r.name as student_name, r.mob as student_mob, 
                 r.regno as student_regno, r.address as student_address 
          FROM mquery q 
          LEFT JOIN branch b ON q.bid = b.id 
          LEFT JOIN registration r ON q.studentid = r.id
          WHERE q.callerid = $caller_id AND DATE(q.date) = CURDATE()
          ORDER BY q.id DESC";
$today_calls = [];
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $today_calls[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Calls - <?php echo htmlspecialchars($SITE_NAME ?? 'Yuva Helpline'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        body { background: #f8f9fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .table-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            border: 1px solid rgba(0,0,0,0.05);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-white me-3">
                    <i class="fas fa-user me-2"></i><?php echo $caller_name; ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="table-card">
                    <h5 class="mb-3"><i class="fas fa-phone-alt me-2"></i>Today's Calls Details</h5>
                    <div class="table-responsive">
                        <table id="todayCallsTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Regno</th>
                                    <th>Student Name</th>
                                    <th>Mobile</th>
                                    <th>Branch</th>
                                    <th>Description/Remarks</th>
                                    <th>Next Followup</th>
                                    <th>Date/Time</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($today_calls as $call): ?>
                                    <tr>
                                        <td><?php echo $call['id']; ?></td>
                                        <td><?php echo htmlspecialchars($call['student_regno'] ?? 'N/A'); ?></td>
                                        <td><strong><?php echo htmlspecialchars($call['student_name'] ?? 'N/A'); ?></strong></td>
                                        <td><?php echo htmlspecialchars($call['student_mob'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($call['bname'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($call['des'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($call['nextdate'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($call['date'] ?? ''); ?></td>
                                        <td>
                                            <?php if ($call['status'] == 1): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php elseif ($call['status'] == 2): ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="make-call.php?id=<?php echo $call['studentid']; ?>" class="btn btn-sm btn-outline-primary">
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
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#todayCallsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100]
            });
        });
    </script>
</body>
</html>
