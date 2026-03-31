<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_path = $_SERVER['SCRIPT_NAME'];
$base_pos = strpos($script_path, '/coordinator/');
$base_dir = $base_pos !== false ? substr($script_path, 0, $base_pos) : '';
$panel_base = $protocol . '://' . $host . $base_dir . '/coordinator/';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar" class="shadow-sm">
    <div class="sidebar-header border-bottom border-light mb-4 pb-4 px-4 pt-4">
        <div class="d-flex align-items-center gap-3 mb-2">
            <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                <i class="fas fa-sitemap fs-5"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">Coordinator</h4>
                <p class="mb-0 text-muted small" style="font-size: 0.75rem; font-weight: 500;"><?php echo $SITE_NAME ?? 'Yuva Helpline'; ?></p>
            </div>
        </div>
    </div>

    <ul class="components list-unstyled px-3">
        <div class="px-3 mb-2 text-uppercase text-muted opacity-50 fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Core Modules</div>
        <li class="mb-1">
            <a href="<?php echo $panel_base; ?>index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?> px-3 py-2 rounded-3">
                <i class="fas fa-th-large"></i> Dashboard Hub
            </a>
        </li>
        <li class="mb-1">
            <a href="<?php echo $panel_base; ?>direct_approvals.php" class="<?php echo $current_page == 'direct_approvals.php' ? 'active' : ''; ?> px-3 py-2 rounded-3">
                <i class="fas fa-headset"></i> Direct Admissions
            </a>
        </li>
        <li class="mb-1">
            <a href="<?php echo $panel_base; ?>reg_credentials.php" class="<?php echo $current_page == 'reg_credentials.php' ? 'active' : ''; ?> px-3 py-2 rounded-3">
                <i class="fas fa-user-shield"></i> Registration Approvals
            </a>
        </li>
        
        <div class="px-3 mt-4 mb-2 text-uppercase text-muted opacity-50 fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Audit Trails</div>
        <li class="mb-1">
            <a href="<?php echo $panel_base; ?>past-approvals.php" class="<?php echo $current_page == 'past-approvals.php' ? 'active' : ''; ?> px-3 py-2 rounded-3">
                <i class="fas fa-history"></i> Approval History
            </a>
        </li>

        <li class="mt-4 pt-4 border-top border-light px-3">
            <a href="<?php echo $panel_base; ?>logout.php" class="text-danger bg-danger bg-opacity-10 rounded-3 px-3 py-2 fw-bold" style="transition: all 0.3s;">
                <i class="fas fa-power-off text-danger"></i> Secure Sign Out
            </a>
        </li>
    </ul>
</nav>
