<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Bulk Upload Students';

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
                <h1>Bulk Upload Students</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="list.php">Students</a></li>
                        <li class="breadcrumb-item active">Bulk Upload</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="table-card">
                        <h4 class="mb-3">Upload Excel File</h4>
                        <div id="alertContainer"></div>

                        <form id="uploadForm" enctype="multipart/form-data">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Select Branch *</label>
                                    <select name="bid" id="bid" class="form-select shadow-sm" required>
                                        <option value="">-- Select Branch --</option>
                                        <?php
                                        $branches = mysqli_query($con, "SELECT id, bname FROM branch WHERE status = 1 ORDER BY bname ASC");
                                        while ($b = mysqli_fetch_assoc($branches)) {
                                            echo "<option value='{$b['id']}'>{$b['bname']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Select Category *</label>
                                    <select name="mcategory" id="mcategory" class="form-select shadow-sm" required disabled>
                                        <option value="">-- Select Category --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Select Excel File *</label>
                                <input type="file" name="excel_file" id="excel_file" class="form-control"
                                    accept=".xlsx,.xls,.csv" required>
                                <small class="text-muted">Supported formats: .xlsx, .xls, .csv (Max size: 5MB)</small>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary" id="uploadBtn">
                                    <i class="fas fa-upload me-2"></i>Upload Students
                                </button>
                                <a href="list.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>

                        <div id="progressContainer" style="display: none;">
                            <div class="progress mb-3">
                                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                                    role="progressbar" style="width: 0%">0%</div>
                            </div>
                            <p id="progressText" class="text-center">Processing...</p>
                        </div>

                        <div id="resultsContainer" style="display: none;">
                            <h5 class="mt-4">Upload Results</h5>
                            <div class="alert alert-info">
                                <p class="mb-1"><strong>Total Rows:</strong> <span id="totalRows">0</span></p>
                                <p class="mb-1"><strong>Successfully Imported:</strong> <span id="successCount"
                                        class="text-success">0</span></p>
                                <p class="mb-0"><strong>Failed:</strong> <span id="errorCount"
                                        class="text-danger">0</span></p>
                            </div>

                            <div id="errorDetails" style="display: none;">
                                <h6 class="text-danger">Error Details:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Row</th>
                                                <th>Error</th>
                                            </tr>
                                        </thead>
                                        <tbody id="errorTableBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="table-card">
                        <h4 class="mb-3">Instructions</h4>
                        <ol class="small">
                            <li>Download the sample template below</li>
                            <li>Fill in student data according to the template</li>
                            <li>Save the file in Excel format (.xlsx or .xls)</li>
                            <li>Upload the file using the form</li>
                            <li>Review the results and fix any errors</li>
                        </ol>

                        <a href="templates/sample_template.xlsx" class="btn btn-success btn-sm w-100 mb-3" download>
                            <i class="fas fa-download me-2"></i>Download Sample Template
                        </a>

                        <h5 class="mt-4 mb-3">Required Columns</h5>
                        <ul class="small">
                            <li>Registration No</li>
                            <li>Student Name</li>
                            <li>Father Name</li>
                            <li>Mobile Number</li>
                            <li>Qualification</li>
                            <li>Date of Birth (YYYY-MM-DD)</li>
                            <li>Gender (Male/Female/Other)</li>
                            <li>Address</li>
                            <li>State</li>
                            <li>District</li>
                        </ul>

                        <h6 class="mt-3 mb-2">Optional Columns</h6>
                        <ul class="small text-muted">
                            <li>Mother Name</li>
                            <li>Email Address</li>
                            <li>Pincode</li>
                            <li>Caste</li>
                            <li>Aadhar Number</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Handle Branch Selection -> Fetch Categories
        $('#bid').on('change', function() {
            const bid = $(this).val();
            const catSelect = $('#mcategory');
            
            if (!bid) {
                catSelect.html('<option value="">-- Select Category --</option>').prop('disabled', true);
                return;
            }

            // Show loading state
            catSelect.html('<option value="">Loading categories...</option>').prop('disabled', true);

            $.ajax({
                url: 'ajax_get_categories.php',
                type: 'GET',
                data: { bid: bid },
                success: function(response) {
                    try {
                        const categories = typeof response === 'string' ? JSON.parse(response) : response;
                        let html = '<option value="">-- Select Category --</option>';
                        categories.forEach(cat => {
                            html += `<option value="${cat.id}">${cat.name}</option>`;
                        });
                        catSelect.html(html).prop('disabled', false);
                    } catch (e) {
                        catSelect.html('<option value="">Error loading categories</option>');
                    }
                },
                error: function() {
                    catSelect.html('<option value="">Error loading categories</option>');
                }
            });
        });

        $('#uploadForm').on('submit', function (e) {
            e.preventDefault();

            const fileInput = $('#excel_file')[0];
            if (!fileInput.files.length) {
                showAlert('Please select a file', 'danger');
                return;
            }

            const file = fileInput.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB

            if (file.size > maxSize) {
                showAlert('File size exceeds 5MB limit', 'danger');
                return;
            }

            const formData = new FormData(this);

            $('#uploadBtn').prop('disabled', true);
            $('#progressContainer').show();
            $('#resultsContainer').hide();
            $('#alertContainer').empty();

            $.ajax({
                url: 'process_bulk_upload.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function () {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            $('#progressBar').css('width', percent + '%').text(percent + '%');
                        }
                    });
                    return xhr;
                },
                success: function (response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;

                        $('#progressContainer').hide();
                        $('#resultsContainer').show();

                        $('#totalRows').text(data.total);
                        $('#successCount').text(data.success);
                        $('#errorCount').text(data.errors.length);

                        if (data.errors.length > 0) {
                            $('#errorDetails').show();
                            let errorHtml = '';
                            data.errors.forEach(function (error) {
                                errorHtml += `<tr><td>${error.row}</td><td>${error.message}</td></tr>`;
                            });
                            $('#errorTableBody').html(errorHtml);
                            showAlert(`Upload completed with ${data.success} successful and ${data.errors.length} failed records`, 'warning');
                        } else {
                            $('#errorDetails').hide();
                            showAlert(`Successfully uploaded ${data.success} students!`, 'success');
                        }

                        $('#uploadForm')[0].reset();
                    } catch (e) {
                        showAlert('Error processing response: ' + e.message, 'danger');
                    }
                },
                error: function (xhr) {
                    $('#progressContainer').hide();
                    let errorMsg = 'Upload failed';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch (e) {
                        errorMsg = xhr.responseText || errorMsg;
                    }
                    showAlert(errorMsg, 'danger');
                },
                complete: function () {
                    $('#uploadBtn').prop('disabled', false);
                    $('#progressBar').css('width', '0%').text('0%');
                }
            });
        });
    });

    function showAlert(message, type) {
        const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
        $('#alertContainer').html(alertHtml);
    }
</script>

<?php include('../../includes/footer.php'); ?>
