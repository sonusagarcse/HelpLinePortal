<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Add WhatsApp Template';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $status = isset($_POST['status']) ? 1 : 0;

    $stmt = mysqli_prepare($con, "INSERT INTO whatsapp_templates (title, message, status) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssi", $title, $message, $status);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: list.php?success=1');
        exit;
    } else {
        $error = "Error adding template: " . mysqli_error($con);
    }
}

include('../../includes/header.php');
?>

<div class="wrapper">
    <?php include('../../includes/sidebar.php'); ?>

    <div id="content">
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-link"><i class="fas fa-bars"></i></button>
            <div class="user-menu">
                <div class="user-info">
                    <div class="name"><?php echo $admin_name; ?></div>
                    <div class="role">Admin</div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="fas fa-user-circle fa-2x"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Add New Template</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="list.php">WhatsApp Templates</a></li>
                                <li class="breadcrumb-item active">Add</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Template Title *</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Welcome Message" required>
                                <div class="form-text">Give it a short, descriptive name to help callers identify it.</div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">Message Content *</label>
                                <textarea name="message" class="form-control" rows="8" placeholder="Type your message here..." required></textarea>
                                <div class="form-text">Use <code>[name]</code> to automatically insert the student's name.</div>
                            </div>
                            <div class="col-md-12 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="status" checked>
                                    <label class="form-check-label" for="status">Active (Available for Callers)</label>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-5 rounded-pill">
                                <i class="fas fa-save me-2"></i>Save Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
