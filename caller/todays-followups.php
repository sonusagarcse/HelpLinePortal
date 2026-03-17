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

// Get all students assigned to this caller who have a follow-up today
$query = "SELECT r.*, r.id as student_id, r.regno as student_regno, r.name as student_name, 
                 r.father as student_father, r.mob as student_mob, 
                 r.address as student_address, r.village as student_village, r.dis as student_dis, 
                 r.state as student_state, r.pincode as student_pincode, 
                 mc.name as category_name, b.bname, b.bcode,
                 (SELECT nextdate FROM mquery mq WHERE mq.studentid = r.id AND mq.callerid = $caller_id ORDER BY id DESC LIMIT 1) as nextdate,
                 (SELECT status FROM mquery mq WHERE mq.studentid = r.id AND mq.callerid = $caller_id ORDER BY id DESC LIMIT 1) as latest_status,
                 (SELECT des FROM mquery mq WHERE mq.studentid = r.id AND mq.callerid = $caller_id ORDER BY id DESC LIMIT 1) as latest_remarks
          FROM registration r 
          LEFT JOIN member_category mc ON r.mcategory = mc.id 
          LEFT JOIN branch b ON r.bid = b.id
          WHERE (r.bid IN (SELECT branch_id FROM caller_branches WHERE caller_id = $caller_id AND status = 1) 
             OR r.bid = " . ($_SESSION['caller_bid'] ?? 0) . "
             OR r.assigned_caller = $caller_id) AND r.status = 1
          HAVING (latest_status IS NULL OR latest_status = 1) AND nextdate = CURDATE()
          ORDER BY nextdate ASC, r.id DESC";

$followups = [];
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $followups[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Follow-ups - <?php echo htmlspecialchars($SITE_NAME ?? 'Yuva Helpline'); ?></title>
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
                    <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Today's Scheduled Follow-ups</h5>
                    <div class="table-responsive">
                        <table id="followupsTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Regno</th>
                                    <th>Student Name</th>
                                    <th>Mobile</th>
                                    <th>Branch</th>
                                    <th>Previous Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($followups as $data): ?>
                                    <tr>
                                        <td><?php echo $data['id']; ?></td>
                                        <td><?php echo htmlspecialchars($data['student_regno'] ?? 'N/A'); ?></td>
                                        <td><strong><?php echo htmlspecialchars($data['student_name'] ?? 'N/A'); ?></strong></td>
                                        <td><?php echo htmlspecialchars($data['student_mob'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($data['bname'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($data['latest_remarks'] ?? 'No remarks'); ?></td>
                                        <td>
                                            <a href="make-call.php?id=<?php echo $data['id']; ?>&student_id=<?php echo $data['student_id']; ?>"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-phone"></i> Call Now
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
            $('#followupsTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100]
            });
        });
    </script>
</body>
</html>
