<nav id="sidebar">
    <div class="sidebar-header d-flex align-items-center justify-content-between">
        <h3 class="mb-0"><i class="fas fa-keyboard me-2"></i>DEO Panel</h3>
        <button type="button" id="sidebarCollapseMobile" class="btn text-white d-lg-none p-0">
            <i class="fas fa-times fa-lg"></i>
        </button>
    </div>

    <ul class="list-unstyled components">
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <a href="index.php"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_branch.php' ? 'active' : ''; ?>">
            <a href="manage_branch.php"><i class="fas fa-building me-2"></i>Select Branch 
                <span class="badge bg-light text-primary ms-1" style="font-size: 0.7rem;"><?php echo htmlspecialchars($active_bname); ?></span>
            </a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'add_student.php' ? 'active' : ''; ?>">
            <a href="add_student.php"><i class="fas fa-user-plus me-2"></i>Add Student</a>
        </li>
        <li>
            <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
        </li>
    </ul>
</nav>
