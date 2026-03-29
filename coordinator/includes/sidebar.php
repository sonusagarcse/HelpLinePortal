<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_path = $_SERVER['SCRIPT_NAME'];
$base_pos = strpos($script_path, '/coordinator/');
$base_dir = $base_pos !== false ? substr($script_path, 0, $base_pos) : '';
$panel_base = $protocol . '://' . $host . $base_dir . '/coordinator/';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar">
    <div class="sidebar-header border-bottom border-light border-opacity-10 mb-2">
        <h3><i class="fas fa-sitemap me-2"></i>Coordinator</h3>
        <p class="mb-0 text-muted small px-1"><?php echo $SITE_NAME ?? 'Yuva Helpline'; ?></p>
    </div>

    <ul class="components list-unstyled">
        <li>
            <a href="<?php echo $panel_base; ?>index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i> Dashboard Hub
            </a>
        </li>
        <li>
            <a href="<?php echo $panel_base; ?>direct_approvals.php" class="<?php echo $current_page == 'direct_approvals.php' ? 'active' : ''; ?>">
                <i class="fas fa-headset"></i> Direct Admissions
            </a>
        </li>
        <li>
            <a href="<?php echo $panel_base; ?>reg_credentials.php" class="<?php echo $current_page == 'reg_credentials.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-shield"></i> Supervisor Credentials
            </a>
        </li>
        
        <div class="sidebar-heading px-4 py-2 mt-4 text-uppercase small text-muted opacity-50 fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Audit Trails</div>
        
        <li>
            <a href="<?php echo $panel_base; ?>past-approvals.php" class="<?php echo $current_page == 'past-approvals.php' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i> Approval History
            </a>
        </li>

        <li class="mt-auto">
            <a href="<?php echo $panel_base; ?>logout.php" class="text-danger">
                <i class="fas fa-power-off text-danger"></i> Sign Out
            </a>
        </li>
    </ul>
</nav>
