<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - DEO Panel' : 'DEO Panel'; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../admin/css/style.css">
    <style>
        :root {
            --primary-color: #1e3c72;
            --secondary-color: #2a5298;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: #fff;
            transition: all 0.3s ease;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1050;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        #sidebar.active {
            left: calc(-1 * var(--sidebar-width));
        }

        #sidebar .sidebar-header {
            padding: 25px 20px;
            background: rgba(0, 0, 0, 0.15);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        #sidebar ul.components {
            padding: 15px 0;
        }

        #sidebar ul li a {
            padding: 12px 25px;
            font-size: 1rem;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 4px solid transparent;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        #sidebar ul li a i {
            flex-shrink: 0;
        }

        #sidebar ul li a .badge {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        #sidebar ul li a:hover, #sidebar ul li.active > a {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #4facfe;
        }

        /* Top Navbar for Mobile */
        .mobile-header {
            display: none;
            background: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        /* Content Adjustment */
        #content {
            width: 100%;
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        #content.active {
            margin-left: 0;
        }

        /* Responsive Settings */
        @media (max-width: 991.98px) {
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
            .desktop-hide {
                display: none !important;
            }
        }

        /* Form and Card Styling */
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
    </style>
</head>
<body>

    <!-- Mobile Header -->
    <div class="mobile-header d-lg-none">
        <button type="button" id="sidebarCollapse" class="btn btn-primary">
            <i class="fas fa-bars"></i>
        </button>
        <h5 class="mb-0 fw-bold text-primary">DEO Panel</h5>
        <div style="width: 40px;"></div> <!-- placeholder for centering -->
    </div>
