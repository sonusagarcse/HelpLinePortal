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

// Get callers under this supervisor
$callers_query = "SELECT id, regno, name FROM caller WHERE svid = $supervisor_id AND status = 1 ORDER BY name";
$callers = [];
$result = mysqli_query($con, $callers_query);
while ($row = mysqli_fetch_assoc($result)) {
    $callers[] = $row;
}

// Get branches
$branches_query = "SELECT id, bcode, bname FROM branch WHERE status = 1 ORDER BY bname";
$branches = [];
$result = mysqli_query($con, $branches_query);
while ($row = mysqli_fetch_assoc($result)) {
    $branches[] = $row;
}

// Get member categories
$categories_query = "SELECT id, name FROM member_category WHERE status = 1 ORDER BY name";
$categories = [];
$result = mysqli_query($con, $categories_query);
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

// Get students for assignment (from registration table)
$students_query = "SELECT r.id, r.regno, r.name, r.father, r.mob, r.email, b.bname, mc.name as category_name
                   FROM registration r
                   LEFT JOIN branch b ON r.bid = b.id
                   LEFT JOIN member_category mc ON r.mcategory = mc.id
                   WHERE r.status = 1
                   ORDER BY r.id DESC
                   LIMIT 100";
$students = [];
$result = mysqli_query($con, $students_query);
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

// Handle assignment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_data'])) {
    $caller_id = (int) $_POST['caller_id'];
    $selected_students = $_POST['students'] ?? [];

    if (!empty($selected_students) && $caller_id > 0) {
        $success_count = 0;

        foreach ($selected_students as $student_id) {
            $student_id = (int) $student_id;
            // Unassign from others and assign to specific caller
            $update_query = "UPDATE registration SET assigned_caller = ? WHERE id = ?";
            $stmt = mysqli_prepare($con, $update_query);
            mysqli_stmt_bind_param($stmt, "ii", $caller_id, $student_id);

            if (mysqli_stmt_execute($stmt)) {
                $success_count++;
            }
        }

        $_SESSION['success_message'] = "$success_count records explicitly assigned successfully!";
        header('Location: assign_data.php');
        exit;
    }
}

// Handle assignment deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];
    $delete_query = "UPDATE registration SET assigned_caller = 0 WHERE id = ?";
    $stmt = mysqli_prepare($con, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Explicit assignment removed successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to remove assignment.";
    }
    header('Location: assign_data.php');
    exit;
}

// Handle Delete All
if (isset($_POST['delete_all']) && isset($_POST['delete_caller_id'])) {
    $del_caller_id = (int) $_POST['delete_caller_id'];
    if ($del_caller_id > 0) {
        $delete_all_query = "UPDATE registration SET assigned_caller = 0 WHERE assigned_caller = ?";
        $stmt = mysqli_prepare($con, $delete_all_query);
        mysqli_stmt_bind_param($stmt, "i", $del_caller_id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = "All explicitly assigned data for this caller has been removed.";
        } else {
            $_SESSION['error_message'] = "Failed to remove data.";
        }
    }
    header('Location: assign_data.php');
    exit;
}

// Get existing explicit assignments
$assignments_query = "SELECT r.id, r.date, c.name as caller_name, 
                      b.bname, mc.name as category_name,
                      (SELECT nextdate FROM mquery mq WHERE mq.studentid = r.id ORDER BY id DESC LIMIT 1) as nextdate
                      FROM registration r 
                      JOIN caller c ON r.assigned_caller = c.id 
                      LEFT JOIN branch b ON r.bid = b.id 
                      LEFT JOIN member_category mc ON r.mcategory = mc.id 
                      WHERE c.svid = $supervisor_id AND r.assigned_caller > 0
                      ORDER BY r.id DESC LIMIT 500";
$assignments = [];
$assignments_result = mysqli_query($con, $assignments_query);
while ($row = mysqli_fetch_assoc($assignments_result)) {
    if (empty($row['nextdate'])) {
        $row['nextdate'] = 'Pending First Call';
    }
    $assignments[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Data - Supervisor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        body {
            background: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .page-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .assignment-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .section-title {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }

        .student-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .student-card:hover {
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
        }

        .student-card.selected {
            background: #f0f4ff;
            border-color: #667eea;
        }

        .btn-assign {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
        }

        .btn-assign:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
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
                        <a class="nav-link" href="callers.php">
                            <i class="fas fa-users me-1"></i>My Callers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="assign_data.php">
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
        <!-- Page Header -->
        <div class="page-header">
            <h4 class="mb-0">
                <i class="fas fa-tasks me-2 text-primary"></i>
                Assign Calling Data to Callers
            </h4>
            <p class="text-muted mb-0 mt-2">Select students and assign them to your callers for follow-up calls</p>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form method="POST" id="assignmentForm">
            <div class="row">
                <!-- Assignment Details -->
                <div class="col-md-4">
                    <div class="assignment-card">
                        <h5 class="section-title">
                            <i class="fas fa-clipboard-list me-2"></i>Assignment Details
                        </h5>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user me-2 text-primary"></i>Select Caller *
                            </label>
                            <select name="caller_id" id="callerSelect" class="form-select form-select-lg" required>
                                <option value="">Choose a caller...</option>
                                <?php foreach ($callers as $caller): ?>
                                    <option value="<?php echo $caller['id']; ?>">
                                        <?php echo htmlspecialchars($caller['regno'] . ' - ' . $caller['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-muted">Selecting a caller will show relevant student data.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">
                                <i class="fas fa-info-circle me-2"></i>Note: Students assigned here will bypass branch restrictions and appear in the caller's dashboard immediately.
                            </label>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Selected:</strong> <span id="selectedCount">0</span> students
                        </div>

                        <button type="submit" name="assign_data" class="btn btn-assign btn-primary w-100 mb-2">
                            <i class="fas fa-check me-2"></i>Assign Selected Data
                        </button>
                    </div>

                    <!-- Bulk Delete Card -->
                    <div class="assignment-card mt-3">
                        <h5 class="section-title text-danger">
                            <i class="fas fa-trash-alt me-2"></i>Bulk Actions
                        </h5>
                        <p class="small text-muted">Clear all pending assignments for the selected caller.</p>
                        <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal"
                            data-bs-target="#deleteModal" id="btnDeleteAll" disabled>
                            <i class="fas fa-bomb me-2"></i>Delete All Assignments
                        </button>
                    </div>
                </div>

                <!-- Students List -->
                <div class="col-md-8">
                    <div class="assignment-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="section-title mb-0">
                                <i class="fas fa-users me-2"></i>Available Students
                            </h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                    <i class="fas fa-check-double me-1"></i>Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                                    <i class="fas fa-times me-1"></i>Deselect All
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="studentsTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAllCheckbox" onclick="toggleAll(this)">
                                        </th>
                                        <th>Regno</th>
                                        <th>Name</th>
                                        <th>Father</th>
                                        <th>Mobile</th>
                                        <th>Branch</th>
                                        <th>Category</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center p-4">
                                            <i class="fas fa-arrow-left me-2"></i>Please select a caller to view
                                            available data.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Manage Assigned Data -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="assignment-card">
                    <h5 class="section-title">
                        <i class="fas fa-trash-alt me-2"></i>Manage Assigned Data
                    </h5>
                    <p class="text-muted small">Removing an assignment decreases the "Assigned Data" slot for the
                        caller.
                    </p>

                    <div class="table-responsive">
                        <table id="assignmentsTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Caller</th>
                                    <th>Branch</th>
                                    <th>Category</th>
                                    <th>Assigned Date</th>
                                    <th>Next Follow-up</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignments as $assignment): ?>
                                    <tr>
                                        <td><?php echo $assignment['id']; ?></td>
                                        <td><?php echo htmlspecialchars($assignment['caller_name']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['bname'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['category_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['date']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['nextdate']); ?></td>
                                        <td>
                                            <a href="assign_data.php?delete=<?php echo $assignment['id']; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to remove this assignment?');">
                                                <i class="fas fa-trash"></i> Remove
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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Confirm Bulk Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>ALL</strong> assigned data for the selected caller?</p>
                    <p class="text-danger small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <form method="POST">
                        <input type="hidden" name="delete_caller_id" id="modalCallerId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_all" class="btn btn-danger">Delete All</button>
                    </form>
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
            // Initialize assignments table
            $('#assignmentsTable').DataTable({
                pageLength: 25,
                order: [[0, 'desc']]
            });
            // Initialize students table placeholder
            // Note: We don't init it yet because it's empty/placeholder.
        });

        // AJAX to fetch filtered student data
        $('#callerSelect').change(function () {
            const callerId = $(this).val();
            const btnDelete = $('#btnDeleteAll');
            const tableBody = $('#studentsTableBody');
            
            // Destroy existing DataTable if it exists to allow HTML replacement
            if ($.fn.DataTable.isDataTable('#studentsTable')) {
                $('#studentsTable').DataTable().destroy();
            }

            if (callerId) {
                // Enable delete button and set ID
                btnDelete.prop('disabled', false);
                $('#modalCallerId').val(callerId);

                // Show loading state
                tableBody.html('<tr><td colspan="7" class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">Fetching filtered data...</div></td></tr>');

                // Fetch data
                $.ajax({
                    url: 'get_filtered_students.php',
                    method: 'POST',
                    data: { caller_id: callerId },
                    success: function (response) {
                        tableBody.html(response);
                        updateCount();
                        $('#selectAllCheckbox').prop('checked', false);
                        
                        // Re-initialize DataTable for the new data
                        $('#studentsTable').DataTable({
                            pageLength: 100, // Show more data by default
                            lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
                            order: [[1, 'asc']], // Order by Regno
                            retrieve: true
                        });
                    },
                    error: function () {
                        tableBody.html('<tr><td colspan="7" class="text-center text-danger p-4">Error fetching data. Please try again.</td></tr>');
                    }
                });
            } else {
                btnDelete.prop('disabled', true);
                tableBody.html('<tr><td colspan="7" class="text-center p-4"><i class="fas fa-arrow-left me-2"></i>Please select a caller to view available data.</td></tr>');
            }
        });

        function updateCount() {
            const count = document.querySelectorAll('.student-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
        }

        function toggleAll(checkbox) {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateCount();
        }

        function selectAll() {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = true);
            document.getElementById('selectAllCheckbox').checked = true;
            updateCount();
        }

        function deselectAll() {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAllCheckbox').checked = false;
            updateCount();
        }

        // Form validation
        document.getElementById('assignmentForm').addEventListener('submit', function (e) {
            const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
            if (selectedCount === 0) {
                e.preventDefault();
                alert('Please select at least one student to assign!');
                return false;
            }
        });
    </script>
</body>

</html>