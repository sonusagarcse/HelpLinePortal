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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --sidebar-width: 260px;
            --header-height: 60px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }

        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        /* Sidebar Styles */
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
            transition: all 0.3s;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 999;
            overflow-y: auto;
        }

        #sidebar.active {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.1);
        }

        #sidebar .sidebar-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        #sidebar ul.components {
            padding: 20px 0;
        }

        #sidebar ul li {
            list-style: none;
        }

        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 15px;
            display: block;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s;
        }

        #sidebar ul li a:hover,
        #sidebar ul li a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        #sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
        }

        /* Content Area */
        #content {
            width: 100%;
            min-height: 100vh;
            transition: all 0.3s;
            margin-left: var(--sidebar-width);
        }

        #content.active {
            margin-left: 0;
        }

        /* Header */
        .top-navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-navbar .navbar-brand {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .top-navbar .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .top-navbar .user-info {
            text-align: right;
        }

        .top-navbar .user-info .name {
            font-weight: 600;
            color: #333;
        }

        .top-navbar .user-info .role {
            font-size: 12px;
            color: #666;
        }

        /* Main Content */
        .main-content {
            padding: 30px;
        }

        /* Cards */
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-card .content h3 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            color: #333;
        }

        .stat-card .content p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .page-header .breadcrumb {
            background: none;
            padding: 0;
            margin: 10px 0 0 0;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        /* Tables */
        .table-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }

            #sidebar.active {
                margin-left: 0;
            }

            #content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
