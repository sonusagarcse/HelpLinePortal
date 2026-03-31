<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Coordinator' : 'Coordinator Hub'; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-indigo: #4a6cf7;
            --dark-indigo: #334ab2;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-700: #334155;
            --slate-900: #0f172a;
            --sidebar-width: 280px;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --card-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,0.03) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,0.03) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.03) 0, transparent 50%);
            background-attachment: fixed;
            color: var(--slate-700);
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* Ambient Orbs */
        .ambient-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.6;
            animation: float 20s infinite ease-in-out;
            pointer-events: none;
        }
        .orb-1 { top: -10%; left: -10%; width: 50vw; height: 50vw; background: rgba(74, 108, 247, 0.08); }
        .orb-2 { bottom: -10%; right: -10%; width: 60vw; height: 60vw; background: rgba(139, 92, 246, 0.08); animation-delay: -10s; }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(5%, 5%); }
        }

        h1, h2, h3, h4, .fw-bold {
            font-family: 'Outfit', sans-serif;
            color: var(--slate-900);
            letter-spacing: -0.02em;
        }

        /* Minimal Sidebar (True Glass) */
        #sidebar {
            width: var(--sidebar-width);
            background: rgba(255, 255, 255, 0.6) !important;
            backdrop-filter: blur(24px) !important;
            -webkit-backdrop-filter: blur(24px) !important;
            color: var(--slate-700);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1050;
            border-right: 1px solid rgba(255, 255, 255, 0.8) !important;
        }

        #sidebar.active {
            left: calc(-1 * var(--sidebar-width));
        }

        #sidebar .sidebar-header {
            padding: 30px 24px;
            display: flex;
            align-items: center;
        }

        #sidebar .sidebar-header h3 {
            font-size: 1.25rem;
            margin-bottom: 0;
            color: var(--primary-indigo);
            font-weight: 700;
        }

        #sidebar ul.components {
            padding: 10px 16px;
        }

        #sidebar ul li {
            margin-bottom: 4px;
        }

        #sidebar ul li a {
            padding: 12px 16px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            color: var(--slate-700);
            text-decoration: none;
            transition: all 0.2s;
            border-radius: 8px;
            font-weight: 500;
        }

        #sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.6);
            color: var(--primary-indigo);
            transform: translateX(4px);
        }

        #sidebar ul li a.active {
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary-indigo);
            box-shadow: inset 3px 0 0 var(--primary-indigo), 0 4px 15px rgba(0,0,0,0.02);
            border: 1px solid rgba(255,255,255,0.6);
        }

        #sidebar ul li a i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
            opacity: 0.7;
        }

        #sidebar ul li a.active i {
            opacity: 1;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            background: #ffffff;
            padding: 12px 16px;
            border-bottom: 1px solid var(--slate-200);
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        /* Content Adjustment */
        #content {
            width: 100%;
            margin-left: var(--sidebar-width);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 40px;
        }

        #content.active {
            margin-left: 0;
        }

        /* Minimal Cards */
        .minimal-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--slate-200);
            box-shadow: var(--card-shadow);
            padding: 24px;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .minimal-card:hover {
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-2px);
        }

        /* Glass Card Premium Utilities (True Glass) */
        .glass-card {
            background: rgba(255, 255, 255, 0.6) !important;
            backdrop-filter: blur(24px) !important;
            -webkit-backdrop-filter: blur(24px) !important;
            border: 1px solid rgba(255, 255, 255, 0.8) !important;
            border-radius: 20px !important;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05) !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .action-btn { transition: all 0.2s ease; }
        .action-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important; }

        /* Stat Grid Hub */
        .hub-option {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
        }

        .hub-card {
            border-left: 4px solid var(--primary-indigo);
            padding: 32px;
        }

        .hub-card.purple { border-left-color: #8b5cf6; }
        .hub-card.blue { border-left-color: #3b82f6; }

        .icon-box-minimal {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        /* Tables Minimal */
        .table-responsive {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid var(--slate-200);
            padding: 4px;
        }

        .table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--slate-700);
            background: var(--slate-50);
            border-bottom: 1px solid var(--slate-200);
            padding: 16px;
            font-weight: 700;
        }

        .table tbody td {
            padding: 16px;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--slate-100);
        }

        .table-hover tbody tr:hover {
            background-color: var(--slate-50);
        }

        /* Badges Minimal */
        .badge-minimal {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .badge-blue { background: #eff6ff; color: #1e40af; border-color: #dbeafe; }
        .badge-purple { background: #f5f3ff; color: #5b21b6; border-color: #ede9fe; }
        .badge-green { background: #f0fdf4; color: #166534; border-color: #dcfce7; }
        .badge-red { background: #fef2f2; color: #991b1b; border-color: #fee2e2; }

        /* Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.4);
            z-index: 1045;
            transition: all 0.3s;
            backdrop-filter: blur(4px);
        }

        @media (max-width: 991px) {
            #content {
                margin-left: 0;
                padding: 20px;
            }
            #sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.active {
                left: 0;
            }
            .mobile-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .sidebar-overlay.active {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Ambient Background Elements -->
    <div class="ambient-orb orb-1"></div>
    <div class="ambient-orb orb-2"></div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="mobile-header">
        <button type="button" id="sidebarCollapse" class="btn btn-light border p-2">
            <i class="fas fa-bars text-secondary"></i>
        </button>
        <span class="fw-bold text-dark">Coordinator Hub</span>
        <div style="width: 40px;"></div> 
    </div>
