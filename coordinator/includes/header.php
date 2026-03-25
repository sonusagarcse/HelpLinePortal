<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Coordinator Panel' : 'Coordinator Panel'; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            --secondary-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --sidebar-width: 260px;
            --glass-bg: rgba(255, 255, 255, 0.95);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            background-image: radial-gradient(rgba(30, 60, 114, 0.05) 1px, transparent 1px);
            background-size: 20px 20px;
            overflow-x: hidden;
            min-height: 100vh;
        }

        h1, h2, h3, h4, .fw-bold {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            background: var(--primary-gradient);
            color: #fff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1050;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }

        #sidebar.active {
            left: calc(-1 * var(--sidebar-width));
        }

        #sidebar .sidebar-header {
            padding: 30px 25px;
            background: rgba(0, 0, 0, 0.2);
        }

        #sidebar ul.components {
            padding: 15px 0;
        }

        #sidebar ul li a {
            padding: 15px 25px;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            font-weight: 500;
        }

        #sidebar ul li a.active, #sidebar ul li a:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #00f2fe;
        }

        #sidebar ul li a i {
            width: 30px;
            font-size: 1.2rem;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            background: white;
            padding: 15px 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        /* Content Adjustment */
        #content {
            width: 100%;
            margin-left: var(--sidebar-width);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
        }

        #content.active {
            margin-left: 0;
        }

        /* Cards and Elements */
        .glass-card {
            background: var(--glass-bg);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid rgba(255,255,255,0.4);
            padding: 25px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-right: 20px;
        }

        .table-responsive {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
            border: 1px solid #ebebeb;
        }

        .table thead th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(2px);
            z-index: 1045;
            transition: all 0.3s;
        }
        @media (max-width: 991px) {
            .sidebar-overlay.active {
                display: block;
            }
        }

        /* Responsive */
        @media (max-width: 991px) {
            #sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.active {
                left: 0;
            }
            #content {
                margin-left: 0;
            }
            .mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>

    <div class="mobile-header d-lg-none">
        <button type="button" id="sidebarCollapse" class="btn btn-primary bg-gradient border-0 rounded-3">
            <i class="fas fa-bars"></i>
        </button>
        <h5 class="mb-0 fw-bold" style="color: #1e3c72;">Coordinator Panel</h5>
        <div style="width: 40px;"></div> 
    </div>
