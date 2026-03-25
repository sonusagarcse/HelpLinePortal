<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_path = $_SERVER['SCRIPT_NAME'];
$base_pos = strpos($script_path, '/coordinator/');
$base_dir = $base_pos !== false ? substr($script_path, 0, $base_pos) : '';
$panel_base = $protocol . '://' . $host . $base_dir . '/coordinator/';
?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<nav id="sidebar">
    <div class="sidebar-header border-bottom border-light border-opacity-10">
        <h3><i class="fas fa-sitemap me-2 text-info"></i>Coordinator</h3>
        <p class="mb-0" style="font-size: 13px; opacity: 0.8;"><?php echo $SITE_NAME ?? 'Yuva Helpline'; ?></p>
    </div>

    <ul class="components mt-3">
        <li>
            <a href="<?php echo $panel_base; ?>index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-clock fs-5"></i> Pending Approvals
            </a>
        </li>
        <li>
            <a href="<?php echo $panel_base; ?>past-approvals.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'past-approvals.php' ? 'active' : ''; ?>">
                <i class="fas fa-check-double fs-5"></i> Approval History
            </a>
        </li>
    </ul>
</nav>
