<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $dashboard_title = 'Admin Dashboard';
    if (isset($admin_type)) {
        if ($admin_type == 2) $dashboard_title = 'Manager Dashboard';
        elseif ($admin_type == 3) $dashboard_title = 'Healthcare Dashboard';
        elseif ($admin_type == 4) $dashboard_title = 'Supervisor Dashboard';
        elseif ($admin_type == 5) $dashboard_title = 'Branch Dashboard';
    }
    ?>
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo $dashboard_title; ?> - <?php echo $SITE_NAME; ?>
    </title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter & Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --primary-dark: #3730a3;
            --secondary: #6366f1;
            --accent: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --dark: #1e293b;
            --light: #f8fafc;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 0px;
            --header-height: 70px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --card-shadow-hover: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--slate-50);
            color: var(--slate-800);
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background: var(--slate-900);
            color: white;
            transition: var(--transition);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1050;
            overflow-y: auto;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        #sidebar.active {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        #sidebar .sidebar-header {
            padding: 24px;
            background: rgba(255, 255, 255, 0.03);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        #sidebar .sidebar-header h3 {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, #fff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        #sidebar ul.components {
            padding: 15px 12px;
        }

        #sidebar ul li {
            margin-bottom: 4px;
        }

        #sidebar ul li a {
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            color: var(--slate-400);
            text-decoration: none;
            transition: var(--transition);
            border-radius: 8px;
        }

        #sidebar ul li a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        #sidebar ul li a.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        #sidebar ul li a i {
            margin-right: 12px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        /* Content Area */
        #content {
            flex: 1;
            min-width: 0;
            min-height: 100vh;
            transition: var(--transition);
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
        }

        #content.active {
            margin-left: 0;
        }

        /* Header / Navbar */
        .top-navbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--slate-200);
            padding: 0 24px;
            height: var(--header-height);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        #sidebarCollapse {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--slate-100);
            color: var(--slate-600);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        #sidebarCollapse:hover {
            background: var(--slate-200);
            color: var(--slate-900);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            line-height: 1.2;
        }

        .user-info .name {
            font-weight: 600;
            font-size: 14px;
            color: var(--slate-900);
        }

        .user-info .role {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--primary);
        }

        /* Main Content Padding */
        .main-content {
            padding: 24px;
            flex: 1;
        }

        /* Cards & Stats */
        .stat-card {
            background: white;
            border: 1px solid var(--slate-200);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-hover);
            border-color: var(--primary-light);
        }

        .stat-card .icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 16px;
            transition: var(--transition);
        }

        .stat-card:hover .icon {
            transform: scale(1.1) rotate(5deg);
        }

        .stat-card .content h3 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            font-family: 'Outfit', sans-serif;
            color: var(--slate-900);
            letter-spacing: -0.5px;
        }

        .stat-card .content p {
            margin: 4px 0 0 0;
            color: var(--slate-500);
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Page Headers */
        .page-header {
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .breadcrumb-item {
            font-size: 13px;
            font-weight: 500;
        }

        .breadcrumb-item.active {
            color: var(--slate-500);
        }

        /* Premium Tables */
        .table-card {
            background: white;
            border: 1px solid var(--slate-200);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--card-shadow);
        }

        .table thead th {
            background: var(--slate-50);
            color: var(--slate-600);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--slate-200);
            padding: 16px;
        }

        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            font-size: 14px;
            color: var(--slate-700);
        }

        /* UI Components */
        .badge {
            padding: 6px 10px;
            font-weight: 600;
            border-radius: 6px;
        }

        .btn {
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 10px;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
        }

        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.5);
            z-index: 1040;
            top: 0;
            left: 0;
            backdrop-filter: blur(4px);
        }

        /* Mobile Adjustments */
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                margin-left: 0 !important;
            }
            .sidebar-overlay.active {
                display: block;
            }
            .main-content {
                padding: 16px;
            }
            .top-navbar {
                padding: 0 16px;
            }
            .page-header h1 {
                font-size: 22px;
            }
        }
        }
    </style>
</head>

<body>
