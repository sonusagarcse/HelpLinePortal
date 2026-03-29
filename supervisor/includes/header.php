<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Sync supervisor branches from database to keep session up-to-date with Admin changes
if (isset($_SESSION['supervisor_id']) && isset($con)) {
    $sync_bids = [];
    $sync_query = mysqli_query($con, "SELECT branch_id FROM supervisor_branches WHERE supervisor_id = " . (int)$_SESSION['supervisor_id'] . " AND status = 1");
    if ($sync_query) {
        while ($sync_row = mysqli_fetch_assoc($sync_query)) {
            $sync_bids[] = (int)$sync_row['branch_id'];
        }
        $_SESSION['supervisor_bids'] = $sync_bids;
        // Also update the primary supervisor_bid for backward compatibility
        if (!empty($sync_bids)) {
            $_SESSION['supervisor_bid'] = $sync_bids[0];
        } else {
            $_SESSION['supervisor_bid'] = 0;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Supervisor Dashboard'; ?> - <?php echo $SITE_NAME ?? 'Yuva Helpline'; ?></title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --sidebar-bg: #ffffff;
            --sidebar-text: #64748b;
            --sidebar-text-active: #0f172a;
            --sidebar-border: #f1f5f9;
            --body-bg: #f8fafc;
            --card-bg: #ffffff;
            --card-border: #f1f5f9;
            --text-main: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-main);
            overflow-x: hidden;
            margin: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Layout Structure */
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            position: fixed;
            height: 100vh;
            z-index: 1040;
        }

        .sidebar-header {
            padding: 24px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--sidebar-border);
        }

        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.025em;
        }

        .sidebar-nav {
            padding: 24px 16px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-item-link {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            gap: 12px;
        }

        .nav-item-link:hover {
            background: var(--body-bg);
            color: var(--primary-color);
        }
        
        .nav-item-link.active {
            background: var(--body-bg);
            color: var(--primary-color);
            font-weight: 600;
        }

        .nav-item-link i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
            opacity: 0.8;
        }
        
        .nav-item-link.active i {
            opacity: 1;
        }

        .sidebar-footer {
            padding: 20px 16px;
            border-top: 1px solid var(--sidebar-border);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            background: transparent;
            color: var(--sidebar-text);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            gap: 8px;
            border: 1px solid var(--sidebar-border);
        }

        .logout-btn:hover {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fecaca;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - 260px);
            transition: margin 0.3s ease, width 0.3s ease;
        }

        .top-header {
            height: 72px;
            background: var(--card-bg);
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.4rem;
            color: var(--text-main);
            cursor: pointer;
            padding: 8px;
            margin-left: -8px;
        }

        .header-profile {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-left: auto;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .profile-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text-main);
        }

        .profile-role {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--body-bg);
            border: 1px solid var(--sidebar-border);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .content-area {
            padding: 32px;
            flex-grow: 1;
            width: 100%;
        }

        /* UI Utilities */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: none;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--card-border);
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: var(--text-main);
            font-size: 1.1rem;
        }

        .card-body {
            padding: 24px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            letter-spacing: -0.025em;
        }

        .page-subtitle {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-top: 4px;
        }
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Abstracted Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid var(--card-border);
            padding: 10px 16px;
            font-size: 0.95rem;
            background-color: var(--body-bg);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            background-color: #fff;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-main);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        /* Tables & Badges */
        .table > :not(caption) > * > * {
            padding: 16px 24px;
            vertical-align: middle;
            border-bottom-color: var(--card-border);
            color: var(--text-main);
        }

        .table > thead {
            background: var(--body-bg);
        }

        .table > thead th {
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border-bottom: none;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
            letter-spacing: 0.025em;
        }

        .badge.bg-success { background-color: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0; }
        .badge.bg-danger { background-color: #fef2f2 !important; color: #dc2626 !important; border: 1px solid #fecaca; }
        .badge.bg-warning { background-color: #fffbeb !important; color: #d97706 !important; border: 1px solid #fde68a; }
        .badge.bg-info { background-color: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe; }
        .badge.bg-primary { background-color: #eef2ff !important; color: #4f46e5 !important; border: 1px solid #c7d2fe; }
        .badge.bg-secondary { background-color: #f8fafc !important; color: #475569 !important; border: 1px solid #e2e8f0; }

        /* Datatable Specific Overrides */
        div.dataTables_wrapper div.dataTables_filter input {
            border: 1px solid var(--card-border);
            border-radius: 8px;
            padding: 6px 12px;
            background-color: var(--body-bg);
        }
        
        .pagination .page-link {
            border: 1px solid var(--card-border);
            color: var(--text-main);
            margin: 0 2px;
            border-radius: 6px;
        }
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.4);
            z-index: 1035;
            backdrop-filter: blur(2px);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            .mobile-toggle {
                display: block;
            }
            .sidebar-overlay.show {
                display: block;
            }
            .content-area {
                padding: 24px 16px;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="index.php" class="sidebar-brand">
                    <i class="fas fa-layer-group"></i>
                    Supervisor
                </a>
            </div>
            
            <div class="sidebar-nav">
                <a href="index.php" class="nav-item-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="callers.php" class="nav-item-link <?php echo $current_page == 'callers.php' || $current_page == 'caller_details.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>My Callers</span>
                </a>
                <a href="reg_approvals.php" class="nav-item-link <?php echo $current_page == 'reg_approvals.php' ? 'active' : ''; ?>">
                    <i class="fas fa-id-card-clip"></i>
                    <span>Registration Approval</span>
                </a>
                <a href="assign_data.php" class="nav-item-link <?php echo $current_page == 'assign_data.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tasks"></i>
                    <span>Assign Data</span>
                </a>
                <a href="reports.php" class="nav-item-link <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span>Reports</span>
                </a>
            </div>
            
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <button class="mobile-toggle" id="mobileToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="header-profile">
                    <div class="profile-info d-none d-sm-flex">
                        <span class="profile-name"><?php echo htmlspecialchars($supervisor_name ?? 'User'); ?></span>
                        <span class="profile-role">Supervisor</span>
                    </div>
                    <div class="avatar">
                        <?php echo strtoupper(substr($supervisor_name ?? 'U', 0, 1)); ?>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
