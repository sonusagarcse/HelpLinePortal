<?php
// Calculate the admin base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

// Automatically find the base path up to "/admin/"
$script_path = $_SERVER['SCRIPT_NAME'];
$admin_pos = strpos($script_path, '/admin/');
if ($admin_pos !== false) {
    $base_dir = substr($script_path, 0, $admin_pos);
} else {
    $base_dir = '';
}

$admin_base = $protocol . '://' . $host . $base_dir . '/admin/';
?>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<nav id="sidebar">
    <div class="sidebar-header">
        <?php
        $panel_title = 'Admin Panel';
        if (isset($admin_type)) {
            if ($admin_type == 2) $panel_title = 'Manager Panel';
            elseif ($admin_type == 3) $panel_title = 'Healthcare Panel';
            elseif ($admin_type == 4) $panel_title = 'Supervisor Panel';
            elseif ($admin_type == 5) $panel_title = 'Branch Panel';
        }
        ?>
        <h3><i class="fas fa-tachometer-alt me-2"></i><?php echo $panel_title; ?></h3>
        <p class="mb-0" style="font-size: 12px; opacity: 0.8;"><?php echo $SITE_NAME; ?></p>
    </div>

    <ul class="components">
        <li>
            <a href="<?php echo $admin_base; ?>index.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="#userSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-users"></i> User Management
            </a>
            <ul class="collapse list-unstyled" id="userSubmenu" style="padding-left: 20px;">
                <li><a href="<?php echo $admin_base; ?>modules/branches/list.php"><i class="fas fa-building"></i>
                        Branches</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/managers/list.php"><i class="fas fa-user-tie"></i>
                        Managers</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/supervisors/list.php"><i class="fas fa-user-shield"></i>
                        Supervisors</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/callers/list.php"><i class="fas fa-headset"></i>
                        Callers</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/deos/list.php"><i class="fas fa-keyboard"></i>
                        DEOs</a></li>
            </ul>
        </li>

        <li>
            <a href="#studentSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-user-graduate"></i> Students
            </a>
            <ul class="collapse list-unstyled" id="studentSubmenu" style="padding-left: 20px;">
                <li><a href="<?php echo $admin_base; ?>modules/students/list.php"><i class="fas fa-list"></i> All
                        Students</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/students/add.php"><i class="fas fa-plus"></i> Add
                        Student</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/students/categories.php"><i class="fas fa-tags"></i>
                        Categories</a></li>
            </ul>
        </li>

        <li>
            <a href="#courseSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-book"></i> Courses
            </a>
            <ul class="collapse list-unstyled" id="courseSubmenu" style="padding-left: 20px;">
                <li><a href="<?php echo $admin_base; ?>modules/courses/list.php"><i class="fas fa-list"></i> All
                        Courses</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/courses/add.php"><i class="fas fa-plus"></i> Add
                        Course</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/courses/categories.php"><i class="fas fa-folder"></i>
                        Categories</a></li>
            </ul>
        </li>

        <li>
            <a href="#querySubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-comments"></i> Communication
            </a>
            <ul class="collapse list-unstyled" id="querySubmenu" style="padding-left: 20px;">
                <li><a href="<?php echo $admin_base; ?>modules/queries/list.php"><i class="fas fa-question-circle"></i>
                        Queries</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/queries/allotment.php"><i class="fas fa-tasks"></i> Data
                        Allotment</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/contact/list.php"><i class="fas fa-envelope"></i> Contact
                        Forms</a></li>
            </ul>
        </li>

        <li>
            <a href="#financeSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-money-bill-wave"></i> Finance
            </a>
            <ul class="collapse list-unstyled" id="financeSubmenu" style="padding-left: 20px;">
                <li><a href="<?php echo $admin_base; ?>modules/payments/list.php"><i class="fas fa-receipt"></i>
                        Payments</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/payments/wallets.php"><i class="fas fa-wallet"></i>
                        Wallets</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/payments/fees.php"><i class="fas fa-dollar-sign"></i> Fee
                        Management</a></li>
            </ul>
        </li>

        <li>
            <a href="#academicSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-graduation-cap"></i> Academic
            </a>
            <ul class="collapse list-unstyled" id="academicSubmenu" style="padding-left: 20px;">
                <li><a href="<?php echo $admin_base; ?>modules/academic/admit-cards.php"><i class="fas fa-id-card"></i>
                        Admit Cards</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/academic/id-cards.php"><i
                            class="fas fa-address-card"></i> ID Cards</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/academic/marksheets.php"><i class="fas fa-file-alt"></i>
                        Marksheets</a></li>
            </ul>
        </li>

        <li>
            <a href="#contentSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-newspaper"></i> CMS Content
            </a>
            <ul class="collapse list-unstyled" id="contentSubmenu" style="padding-left: 20px;">
                <li><a href="<?php echo $admin_base; ?>modules/content/news/list.php"><i class="fas fa-rss"></i>
                        News</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/content/slider.php"><i class="fas fa-images"></i>
                        Slider</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/content/media.php"><i class="fas fa-photo-video"></i>
                        Media Library</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/content/materials.php"><i class="fas fa-file-pdf"></i>
                        Study Materials</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/videos/list.php"><i class="fas fa-video"></i>
                        Video Library</a></li>
            </ul>
        </li>

        <li>
            <a href="#liveSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-broadcast-tower"></i> Live Classes
            </a>
            <ul class="collapse list-unstyled" id="liveSubmenu" style="padding-left: 20px;">
                <li><a href="<?php echo $admin_base; ?>modules/live/zoom.php"><i class="fab fa-zoom"></i>
                        Zoom Classes</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/live/youtube.php"><i class="fab fa-youtube"></i>
                        YouTube Live</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/live/meet.php"><i class="fas fa-video"></i>
                        Google Meet</a></li>
            </ul>
        </li>

        <li>
            <a href="#masterSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-database"></i> Master Data
            </a>
            <ul class="collapse list-unstyled" id="masterSubmenu" style="padding-left: 20px;">
                <li><a href="<?php echo $admin_base; ?>modules/settings/locations.php"><i class="fas fa-map-marker-alt"></i>
                        Locations</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/master/exam-types.php"><i class="fas fa-file-signature"></i>
                        Exam Types</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/master/vendors.php"><i class="fas fa-truck-loading"></i>
                        Vendors</a></li>
            </ul>
        </li>

        <li>
            <a href="#proSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-briefcase"></i> Professional
            </a>
            <ul class="collapse list-unstyled" id="proSubmenu" style="padding-left: 20px;">
                <li><a href="<?php echo $admin_base; ?>modules/careers/list.php"><i class="fas fa-user-tie"></i>
                        Job Applications</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/services/list.php"><i class="fas fa-concierge-bell"></i>
                        Our Services</a></li>
            </ul>
        </li>

        <li>
            <a href="<?php echo $admin_base; ?>modules/reports/index.php">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
        </li>

        <li>
            <a href="#settingSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-cog"></i> Settings
            </a>
            <ul class="collapse list-unstyled" id="settingSubmenu" style="padding-left: 20px;">
                <li><a href="<?php echo $admin_base; ?>modules/settings/global.php"><i class="fas fa-sliders-h"></i>
                        Global Settings</a></li>
                <li><a href="<?php echo $admin_base; ?>modules/settings/profile.php"><i class="fas fa-user-cog"></i>
                        Admin Profile</a></li>
            </ul>
        </li>
    </ul>
</nav>
