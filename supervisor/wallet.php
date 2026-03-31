<?php
session_start();

if (!isset($_SESSION['supervisor_id'])) {
    header('Location: ../supervisor_login.php');
    exit;
}

require_once(__DIR__ . '/../connection.php');

$supervisor_id = $_SESSION['supervisor_id'];
$page_title = "My Wallet & Earnings";

// Fetch supervisor details (for balance)
$sup_query = mysqli_query($con, "SELECT wallet_balance FROM supervisor WHERE id = $supervisor_id");
$sup_data = mysqli_fetch_assoc($sup_query);
$balance = $sup_data['wallet_balance'] ?? 0.00;

// Fetch transaction history
$transactions = [];
$trans_query = "SELECT se.*, r.name as student_name, r.regno 
               FROM supervisor_earnings se
               JOIN registration r ON se.student_id = r.id
               WHERE se.supervisor_id = $supervisor_id
               ORDER BY se.date DESC";
$t_result = mysqli_query($con, $trans_query);
while ($t_row = mysqli_fetch_assoc($t_result)) {
    $transactions[] = $t_row;
}

include 'includes/header.php';
?>

<div class="row g-4 mb-4">
    <!-- Balance Header Card -->
    <div class="col-12">
        <div class="wow-card p-4 d-flex justify-content-between align-items-center flex-wrap gap-4">
            <div class="d-flex align-items-center gap-4">
                <div class="balance-icon-box">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <h5 class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing: 0.05em;">Current Balance</h5>
                    <h1 class="display-5 fw-extrabold mb-0 text-gradient-primary">₹<?php echo number_format($balance, 2); ?></h1>
                </div>
            </div>
            <div class="d-flex gap-3 mt-md-0 mt-3">
                <div class="stat-pill">
                    <span class="label">Total Earned</span>
                    <span class="value">₹<?php echo number_format($balance, 2); ?></span>
                </div>
                <div class="stat-pill success">
                    <span class="label">Total Approvals</span>
                    <span class="value"><?php echo count($transactions); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="table-container pt-2">
            <div class="d-flex justify-content-between align-items-center mb-4 ps-2">
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-receipt text-primary me-2"></i>Earning History
                    <span class="badge bg-primary bg-opacity-10 text-primary ms-2 fs-6 fw-medium"><?php echo count($transactions); ?> Records</span>
                </h4>
            </div>

            <div class="table-responsive">
                <table class="table wow-table align-middle" id="walletTable">
                    <thead>
                        <tr>
                            <th class="border-0">Transaction Detail</th>
                            <th class="border-0">Registration ID</th>
                            <th class="border-0">Status Info</th>
                            <th class="border-0 text-end">Commission</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 no-hover">
                                    <div class="opacity-25 mb-3"><i class="fas fa-receipt fa-4x text-muted"></i></div>
                                    <h6 class="text-muted">No transactions recorded yet.</h6>
                                    <p class="small text-muted mb-0">Earnings appear here when your submissions are approved by a Coordinator.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $t): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="transaction-icon income">
                                                <i class="fas fa-plus"></i>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($t['student_name']); ?></span>
                                                <span class="text-muted small">
                                                    <i class="far fa-calendar-alt me-1"></i><?php echo date('d M, Y', strtotime($t['date'])); ?> 
                                                    <i class="far fa-clock ms-2 me-1"></i><?php echo date('h:i A', strtotime($t['date'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border-0 p-2 px-3 fw-semibold">
                                            <?php echo htmlspecialchars($t['regno']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark small fw-medium"><?php echo htmlspecialchars($t['description']); ?></span>
                                            <span class="text-success small fw-bold mt-1"><i class="fas fa-check-circle me-1"></i>Approved</span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="amount-badge">
                                            +₹<?php echo number_format($t['amount'], 2); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium Styling Variables */
    :root {
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.4);
        --accent-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }

    .fw-extrabold { font-weight: 800; }
    .text-gradient-primary {
        background: var(--accent-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Glassmorphic Card */
    .wow-card {
        background: white;
        border: 1px solid var(--sidebar-border);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease;
    }

    .balance-icon-box {
        width: 64px;
        height: 64px;
        background: var(--accent-gradient);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 8px 16px rgba(79, 70, 229, 0.2);
    }

    .stat-pill {
        display: flex;
        flex-direction: column;
        padding: 12px 24px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        min-width: 140px;
    }
    .stat-pill.success { background: #f0fdf4; border-color: #dcfce7; }
    .stat-pill .label { font-size: 0.7rem; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 2px; }
    .stat-pill.success .label { color: #16a34a; }
    .stat-pill .value { font-size: 1.25rem; font-weight: 800; color: #1e293b; }

    /* WOW Table Design */
    .wow-table {
        border-collapse: separate;
        border-spacing: 0 12px !important;
        margin-top: -12px;
    }
    .wow-table thead th {
        background: transparent !important;
        color: #64748b !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.1em !important;
        padding: 12px 24px !important;
    }
    .wow-table tbody tr {
        background: white !important;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02) !important;
        transition: all 0.3s ease !important;
        border-radius: 16px;
    }
    .wow-table tbody tr:hover {
        transform: translateY(-4px) scale(1.002);
        box-shadow: 0 12px 24px rgba(0,0,0,0.06) !important;
        background: #ffffff !important;
    }
    .wow-table tbody tr td {
        border: none !important;
        padding: 20px 24px !important;
        background: transparent !important;
    }
    .wow-table tbody tr td:first-child { border-radius: 16px 0 0 16px !important; }
    .wow-table tbody tr td:last-child { border-radius: 0 16px 16px 0 !important; }

    /* Transaction Styling */
    .transaction-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }
    .transaction-icon.income { background: #f0fdf4; color: #16a34a; }
    
    .amount-badge {
        display: inline-block;
        padding: 10px 16px;
        background: #f0fdf4;
        color: #16a34a;
        font-weight: 800;
        font-size: 1.1rem;
        border-radius: 12px;
        border: 1px solid #dcfce7;
    }

    /* Custom Scrollbar for responsiveness */
    .table-responsive::-webkit-scrollbar { height: 6px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    
    /* Remove default DataTables border */
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 20px; }
    table.dataTable.no-footer { border-bottom: none !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof jQuery !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
            $('#walletTable').DataTable({
                "retrieve": true,
                "order": [[0, "desc"]],
                "pageLength": 10,
                "dom": '<"d-flex justify-content-between align-items-center mb-4"f>t<"d-flex justify-content-between align-items-center mt-4"ip>',
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search transactions..."
                }
            });
            $('.dataTables_filter input').addClass('form-control shadow-sm border-0 bg-white px-4 py-2 rounded-pill').css('width', '300px');
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
