<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Global Settings';

// Fetch settings
$query = "SELECT * FROM global_setting LIMIT 1";
$result = mysqli_query($con, $query);
$settings = mysqli_fetch_assoc($result);

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
                        <h1>Global Settings</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Settings</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="save_settings.php" method="POST">
                        <div class="row">
                            <?php foreach ($settings as $key => $value): 
                                if ($key == 'id' || $key == 'supervisor_commission') continue;
                            ?>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><?php echo ucwords(str_replace('_', ' ', $key)); ?></label>
                                    <?php if ($key == 'whatsapp_msg'): ?>
                                        <textarea name="settings[<?php echo $key; ?>]" class="form-control" rows="4"><?php echo htmlspecialchars($value ?? ''); ?></textarea>
                                        <div class="form-text">This message will be sent to students via WhatsApp. You can use <code>[name]</code> to personalize the message.</div>
                                    <?php else: ?>
                                        <input type="text" name="settings[<?php echo $key; ?>]" class="form-control" value="<?php echo htmlspecialchars($value ?? ''); ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4">Save All Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
