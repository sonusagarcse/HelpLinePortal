<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

$page_title = 'Locations Master';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'states';

// Data fetching based on active tab
$data = [];
switch ($active_tab) {
    case 'states':
        $query = "SELECT * FROM state ORDER BY name ASC";
        break;
    case 'districts':
        $query = "SELECT d.*, s.name as state_name FROM district d LEFT JOIN state s ON d.stid = s.id ORDER BY d.name ASC";
        break;
    case 'blocks':
        $query = "SELECT b.*, d.name as district_name FROM rblock b LEFT JOIN district d ON b.disid = d.id ORDER BY b.name ASC";
        break;
    case 'panchayats':
        $query = "SELECT p.*, d.name as district_name FROM panchayat p LEFT JOIN district d ON p.disid = d.id ORDER BY p.name ASC";
        break;
    case 'villages':
        $query = "SELECT v.*, p.name as panchayat_name FROM village v LEFT JOIN panchayat p ON v.pid = p.id ORDER BY v.name ASC";
        break;
}

$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

include('../../includes/header.php');
?>

<div class="wrapper">
    <?php include('../../includes/sidebar.php'); ?>

    <div id="content">
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-link"><i class="fas fa-bars"></i></button>
            <div class="user-menu">
                <div class="user-info">
                    <div class="name"><?php echo $admin_name; ?></div>
                    <div class="role">Admin</div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown"><i class="fas fa-user-circle fa-2x"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="main-content">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Locations Master</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Locations</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="card p-0 overflow-hidden">
                <div class="card-header bg-light">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_tab == 'states' ? 'active' : ''; ?>" href="?tab=states">States</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_tab == 'districts' ? 'active' : ''; ?>" href="?tab=districts">Districts</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_tab == 'blocks' ? 'active' : ''; ?>" href="?tab=blocks">Blocks</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_tab == 'panchayats' ? 'active' : ''; ?>" href="?tab=panchayats">Panchayats</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $active_tab == 'villages' ? 'active' : ''; ?>" href="?tab=villages">Villages</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="card-title"><?php echo ucfirst($active_tab); ?> List</h5>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="fas fa-plus me-1"></i> Add <?php echo rtrim(ucfirst($active_tab), 's'); ?>
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <?php if ($active_tab == 'districts'): ?><th>State</th><?php endif; ?>
                                    <?php if ($active_tab == 'blocks' || $active_tab == 'panchayats'): ?><th>District</th><?php endif; ?>
                                    <?php if ($active_tab == 'villages'): ?><th>Panchayat</th><?php endif; ?>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $item): ?>
                                    <tr>
                                        <td><?php echo $item['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars(($item['name']) ?? ''); ?></strong></td>
                                        <?php if ($active_tab == 'districts'): ?><td><?php echo htmlspecialchars(($item['state_name']) ?? ''); ?></td><?php endif; ?>
                                        <?php if ($active_tab == 'blocks' || $active_tab == 'panchayats'): ?><td><?php echo htmlspecialchars(($item['district_name']) ?? ''); ?></td><?php endif; ?>
                                        <?php if ($active_tab == 'villages'): ?><td><?php echo htmlspecialchars(($item['panchayat_name']) ?? ''); ?></td><?php endif; ?>
                                        <td><span class="badge <?php echo $item['status'] == 1 ? 'bg-success' : 'bg-danger'; ?>"><?php echo $item['status'] == 1 ? 'Active' : 'Inactive'; ?></span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-warning"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
