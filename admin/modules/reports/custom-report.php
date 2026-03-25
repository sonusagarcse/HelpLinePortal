<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$type = $_GET['type'] ?? $_POST['report_type'] ?? '';
$from_date = $_POST['from_date'] ?? '';
$to_date = $_POST['to_date'] ?? '';

if (empty($type)) {
    header('Location: index.php');
    exit;
}

$page_title = ucfirst($type) . ' Report';
$date_filter = ($from_date && $to_date) ? " BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59'" : "";

$report_data = [];
$columns = [];

switch ($type) {
    case 'branch':
        $columns = ['Branch ID', 'Branch Name', 'Total Registrations'];
        $query = "SELECT b.id, b.bname, COUNT(r.id) as total 
                  FROM branch b 
                  LEFT JOIN registration r ON b.id = r.bid";
        if ($date_filter) $query .= " WHERE r.date $date_filter OR r.date IS NULL";
        $query .= " GROUP BY b.id";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $report_data[] = [$row['id'], $row['bname'], $row['total']];
        }
        break;

    case 'student':
        $columns = ['Reg No', 'Student Name', 'Mobile', 'Branch', 'Registration Date', 'Status'];
        $query = "SELECT r.regno, r.name, r.mob, b.bname, r.date, 
                         CASE WHEN r.status = 1 THEN 'Active' ELSE 'Inactive' END as status_text
                  FROM registration r 
                  LEFT JOIN branch b ON r.bid = b.id";
        if ($date_filter) $query .= " WHERE r.date $date_filter";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $report_data[] = [$row['regno'], $row['name'], $row['mob'], $row['bname'], date('Y-m-d', strtotime($row['date'])), $row['status_text']];
        }
        break;

    case 'course':
        $columns = ['Course ID', 'Course Name', 'Total Enrolled'];
        $query = "SELECT c.id, c.coursename, COUNT(r.id) as total 
                  FROM course c 
                  LEFT JOIN registration r ON c.id = r.course";
        if ($date_filter) $query .= " WHERE r.date $date_filter OR r.date IS NULL";
        $query .= " GROUP BY c.id";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $report_data[] = [$row['id'], $row['coursename'], $row['total']];
        }
        break;
        
    case 'revenue':
        $columns = ['Transaction ID', 'Caller', 'Student Name', 'Amount', 'Date Approved'];
        $query = "SELECT ce.id, c.name as caller_name, r.name as student_name, ce.amount, ce.date
                  FROM caller_earnings ce
                  JOIN caller c ON ce.caller_id = c.id
                  JOIN registration r ON ce.student_id = r.id";
        if ($date_filter) $query .= " WHERE ce.date $date_filter";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $report_data[] = [$row['id'], $row['caller_name'], $row['student_name'], '₹' . number_format($row['amount'], 2), date('Y-m-d H:i', strtotime($row['date']))];
        }
        break;

    case 'caller':
        $columns = ['Caller ID', 'Caller Name', 'Branch', 'Calls Made', 'Total Earnings'];
        $query = "SELECT c.id, c.name, b.bname, 
                         (SELECT COUNT(*) FROM mquery m WHERE m.callerid = c.id";
        if ($date_filter) $query .= " AND m.date $date_filter";
        $query .= ") as calls_made,
                         (SELECT SUM(amount) FROM caller_earnings ce WHERE ce.caller_id = c.id";
        if ($date_filter) $query .= " AND ce.date $date_filter";
        $query .= ") as total_earnings
                  FROM caller c
                  LEFT JOIN branch b ON c.bid = b.id";
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $report_data[] = [$row['id'], $row['name'], $row['bname'] ?? 'N/A', $row['calls_made'], '₹' . number_format($row['total_earnings'] ?? 0, 2)];
        }
        break;

    case 'query':
        $columns = ['Query ID', 'Student', 'Caller', 'Remark', 'Status', 'Date'];
        $query = "SELECT m.id, r.name as student_name, c.name as caller_name, m.des, 
                         CASE WHEN m.status = 1 THEN 'Pending' WHEN m.status = 0 THEN 'Closed' ELSE m.status END as status_text, 
                         m.date
                  FROM mquery m
                  JOIN registration r ON m.studentid = r.id
                  JOIN caller c ON m.callerid = c.id";
        if ($date_filter) $query .= " WHERE m.date $date_filter";
        $query .= " ORDER BY m.id DESC LIMIT 1000"; // limit to avoid massive load
        $result = mysqli_query($con, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $report_data[] = [$row['id'], $row['student_name'], $row['caller_name'], $row['des'], $row['status_text'], date('Y-m-d H:i', strtotime($row['date']))];
        }
        break;

    default:
        $columns = ['Error'];
        $report_data = [['Invalid report type selected.']];
        break;
}

include('../../includes/header.php');
?>

<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<style>
    .dt-buttons .btn { margin-right: 5px; margin-bottom: 15px; }
</style>

<div class="wrapper">
    <?php include('../../includes/sidebar.php'); ?>

    <div id="content">
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-link">
                <i class="fas fa-bars"></i>
            </button>

            <div class="user-menu">
                <div class="user-info">
                    <div class="name"><?php echo $admin_name; ?></div>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h1><?php echo $page_title; ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="index.php">Reports</a></li>
                            <li class="breadcrumb-item active"><?php echo ucfirst($type); ?></li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <?php if ($from_date && $to_date): ?>
                        <span class="badge bg-primary px-3 py-2 fs-6">
                            <i class="fas fa-calendar-alt me-2"></i><?php echo $from_date . ' to ' . $to_date; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-card mt-4">
                <div class="table-responsive">
                    <table id="reportTable" class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <?php foreach ($columns as $col): ?>
                                    <th><?php echo htmlspecialchars($col); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $row): ?>
                                <tr>
                                    <?php foreach ($row as $cell): ?>
                                        <td><?php echo htmlspecialchars($cell ?? ''); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts for DataTables Export Buttons -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#reportTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', className: 'btn btn-secondary btn-sm' },
            { extend: 'csv', className: 'btn btn-primary btn-sm' },
            { extend: 'excel', className: 'btn btn-success btn-sm' },
            { extend: 'pdf', className: 'btn btn-danger btn-sm' },
            { extend: 'print', className: 'btn btn-info btn-sm text-white' }
        ],
        pageLength: 25,
        order: [[0, 'desc']]
    });
});
</script>

<?php include('../../includes/footer.php'); ?>
