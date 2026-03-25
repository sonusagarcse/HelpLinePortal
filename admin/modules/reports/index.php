<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Reports';

include('../../includes/header.php');
?>

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
                    <div class="role"><?php echo $admin_type == 1 ? 'Super Admin' : ($admin_type == 2 ? 'Manager' : ($admin_type == 3 ? 'Healthcare' : ($admin_type == 4 ? 'Supervisor' : ($admin_type == 5 ? 'Branch' : 'Admin')))); ?></div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../../logout.php"><i
                                    class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <h1>Reports & Analytics</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </nav>
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="content ms-3">
                                <h5>Branch Report</h5>
                                <p class="mb-0">Branch-wise statistics</p>
                            </div>
                        </div>
                        <a href="custom-report.php?type=branch" class="btn btn-sm btn-primary mt-3 w-100">
                            <i class="fas fa-file-pdf me-2"></i>Generate Report
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="content ms-3">
                                <h5>Student Report</h5>
                                <p class="mb-0">Registration statistics</p>
                            </div>
                        </div>
                        <a href="custom-report.php?type=student" class="btn btn-sm btn-primary mt-3 w-100">
                            <i class="fas fa-file-pdf me-2"></i>Generate Report
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="content ms-3">
                                <h5>Course Report</h5>
                                <p class="mb-0">Enrollment statistics</p>
                            </div>
                        </div>
                        <a href="custom-report.php?type=course" class="btn btn-sm btn-primary mt-3 w-100">
                            <i class="fas fa-file-pdf me-2"></i>Generate Report
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <i class="fas fa-rupee-sign"></i>
                            </div>
                            <div class="content ms-3">
                                <h5>Revenue Report</h5>
                                <p class="mb-0">Financial statistics</p>
                            </div>
                        </div>
                        <a href="custom-report.php?type=revenue" class="btn btn-sm btn-primary mt-3 w-100">
                            <i class="fas fa-file-pdf me-2"></i>Generate Report
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="content ms-3">
                                <h5>Caller Performance</h5>
                                <p class="mb-0">Call tracking report</p>
                            </div>
                        </div>
                        <a href="custom-report.php?type=caller" class="btn btn-sm btn-primary mt-3 w-100">
                            <i class="fas fa-file-pdf me-2"></i>Generate Report
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="stat-card">
                        <div class="d-flex align-items-center">
                            <div class="icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="content ms-3">
                                <h5>Query Report</h5>
                                <p class="mb-0">Communication statistics</p>
                            </div>
                        </div>
                        <a href="custom-report.php?type=query" class="btn btn-sm btn-primary mt-3 w-100">
                            <i class="fas fa-file-pdf me-2"></i>Generate Report
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-card mt-4">
                <h5 class="mb-3">Custom Report Generator</h5>
                <form method="POST" action="custom-report.php">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Report Type</label>
                            <select name="report_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="branch">Branch Report</option>
                                <option value="student">Student Report</option>
                                <option value="course">Course Report</option>
                                <option value="revenue">Revenue Report</option>
                                <option value="caller">Caller Report</option>
                                <option value="query">Query Report</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-chart-bar me-2"></i>Generate
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
