<?php
require_once('../connection.php');
require_once('config/auth.php');

$page_title = 'Coordinator Dashboard Hub';

// Fetch Counts for Dashboard
$bids_list = !empty($coordinator_bids) ? implode(',', array_map('intval', $coordinator_bids)) : '0';

// 1. Direct Admissions (Based on assigned branches)
$direct_count_res = mysqli_query($con, "SELECT COUNT(*) as total FROM registration WHERE coordinator_approval_status = 1 AND reg_status = 0 AND bid IN ($bids_list)");
$direct_count = mysqli_fetch_assoc($direct_count_res)['total'];

// 2. Supervisor Registrations (Based on assigned branches)
$reg_count_res = mysqli_query($con, "SELECT COUNT(*) as total FROM registration WHERE reg_status = 2 AND coordinator_approval_status = 1 AND bid IN ($bids_list)");
$reg_count = mysqli_fetch_assoc($reg_count_res)['total'];

include('includes/header.php');
?>
<div class="wrapper d-flex">
    <?php include('includes/sidebar.php'); ?>

    <div id="content" class="flex-grow-1">
        
        <!-- Premium Welcome Header -->
        <div class="wow-card mb-5 border-0 p-4 p-md-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                <div class="d-flex align-items-center gap-4">
                    <div class="icon-glow-blue rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; border: 4px solid rgba(255,255,255,0.5);">
                        <i class="fas fa-th-large fs-3"></i>
                    </div>
                    <div>
                        <h1 class="fw-bold mb-1 h2 text-gradient-dark" style="letter-spacing: -1px;">Dashboard Hub</h1>
                        <p class="text-muted mb-0 fw-medium"><i class="fas fa-building me-2 text-primary opacity-50"></i><?php echo htmlspecialchars($coordinator_bname); ?> Branch(es)</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="p-3 px-4 bg-white bg-opacity-75 border border-white rounded-pill shadow-sm small fw-bold text-primary" style="backdrop-filter: blur(10px);">
                        <i class="far fa-calendar-alt me-2 fs-6 opacity-75"></i><?php echo date('D, d M Y'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Summary Cards (Glassmorphism & Gradients) -->
        <div class="row g-4 mb-5">
            <div class="col-lg-6">
                <a href="direct_approvals.php" class="hub-option text-decoration-none">
                    <div class="wow-card h-100 p-5 px-4 overflow-hidden">
                        <div class="position-absolute opacity-10" style="right: -20px; bottom: -20px; font-size: 10rem; color: #3b82f6;">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="position-relative z-1">
                            <div class="d-flex justify-content-between align-items-start mb-5">
                                <div class="icon-glow-blue rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 60px; height: 60px;">
                                    <i class="fas fa-headset fs-4"></i>
                                </div>
                                <span class="badge bg-white shadow-sm text-primary border border-white px-4 py-2 rounded-pill fs-6 fw-bold">
                                    <?php echo $direct_count; ?> Pending
                                </span>
                            </div>
                            <h3 class="fw-bold mb-3 text-gradient-dark" style="letter-spacing: -0.5px;">Direct Admissions</h3>
                            <p class="text-muted fw-medium mb-0" style="font-size: 0.95rem; line-height: 1.6; max-width: 85%;">Confirm students flagged for immediate commission payout by callers.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-6">
                <a href="reg_credentials.php" class="hub-option text-decoration-none">
                    <div class="wow-card h-100 p-5 px-4 overflow-hidden">
                        <div class="position-absolute opacity-10" style="right: -20px; bottom: -20px; font-size: 10rem; color: #8b5cf6;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="position-relative z-1">
                            <div class="d-flex justify-content-between align-items-start mb-5">
                                <div class="icon-glow-purple rounded-4 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 60px; height: 60px;">
                                    <i class="fas fa-user-shield fs-4"></i>
                                </div>
                                <span class="badge bg-white shadow-sm text-purple border border-white px-4 py-2 rounded-pill fs-6 fw-bold" style="color: #6d28d9;">
                                    <?php echo $reg_count; ?> Awaiting
                                </span>
                            </div>
                            <h3 class="fw-bold mb-3 text-gradient-dark" style="letter-spacing: -0.5px;">Registration Approvals</h3>
                            <p class="text-muted fw-medium mb-0" style="font-size: 0.95rem; line-height: 1.6; max-width: 85%;">Approve IDs and Passwords generated by the branch supervisor for online registration.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Secondary Section: Logs & Activity -->
        <div class="row">
            <div class="col-12">
                <div class="wow-card p-4 p-md-5 border-0">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold text-gradient-dark mb-0"><i class="fas fa-layer-group me-3 text-primary opacity-50"></i>Operational Shortcuts</h4>
                    </div>
                    
                    <div class="list-group list-group-flush border-0 mt-3">
                        <a href="past-approvals.php" class="list-group-item list-group-item-action border-0 px-4 py-4 rounded-4 shadow-sm action-btn d-flex align-items-center justify-content-between" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);">
                            <div class="d-flex align-items-center">
                                <div class="bg-white border rounded-circle shadow-sm d-flex align-items-center justify-content-center me-4" style="width: 56px; height: 56px;">
                                    <i class="fas fa-history text-secondary fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold text-dark">Audit Trail & History</h5>
                                    <span class="text-muted fw-medium">View all previously approved and rejected registrations.</span>
                                </div>
                            </div>
                            <div class="bg-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-primary transition-all" style="width: 40px; height: 40px;">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Attractive Glassmorphism for Dashboard */
.wow-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.4) 0%, rgba(255, 255, 255, 0.1) 100%) !important;
    backdrop-filter: blur(40px) saturate(150%) !important;
    -webkit-backdrop-filter: blur(40px) saturate(150%) !important;
    border: 1px solid rgba(255, 255, 255, 0.7) !important;
    border-radius: 24px !important;
    box-shadow: 
        0 15px 35px rgba(31, 38, 135, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.6) !important;
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
}

.wow-card::before {
    content: '';
    position: absolute;
    top: 0; left: -100%; width: 50%; height: 100%;
    background: linear-gradient(to right, transparent, rgba(255,255,255,0.4), transparent);
    transform: skewX(-20deg);
    transition: all 0.7s ease;
}

.wow-card:hover::before {
    left: 150%;
}

.wow-card:hover {
    transform: translateY(-8px);
    box-shadow: 
        0 25px 50px rgba(31, 38, 135, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.6) 0%, rgba(255, 255, 255, 0.2) 100%) !important;
}

.icon-glow-blue {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.5);
    color: white;
}

.icon-glow-purple {
    background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
    box-shadow: 0 10px 25px rgba(139, 92, 246, 0.5);
    color: white;
}

.text-gradient-dark {
    background: linear-gradient(135deg, #0f172a, #334155);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
<?php include('includes/footer.php'); ?>
