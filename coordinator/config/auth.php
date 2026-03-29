<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_path = $_SERVER['SCRIPT_NAME'];
$base_pos = strpos($script_path, '/coordinator/');

if ($base_pos !== false) {
    $base_dir = substr($script_path, 0, $base_pos);
} else {
    $base_dir = ''; 
}
$BASE_URL = $protocol . '://' . $host . $base_dir;

if (!isset($_SESSION['coordinator_id'])) {
    header("Location: " . $BASE_URL . "/coordinator_login.php");
    exit;
}

$coordinator_id = $_SESSION['coordinator_id'];
$coordinator_name = $_SESSION['coordinator_name'];
$coordinator_bid = $_SESSION['coordinator_bid'];
$coordinator_bids = $_SESSION['coordinator_bids'] ?? [$coordinator_bid];

// Fetch branch names for UI
$coordinator_bname = "Unknown Branch";
if (!empty($coordinator_bids)) {
    $bids_list = implode(',', array_map('intval', $coordinator_bids));
    $b_query = "SELECT bname FROM branch WHERE id IN ($bids_list)";
    $b_res = mysqli_query($con, $b_query);
    $bnames = [];
    while($b_row = mysqli_fetch_assoc($b_res)) {
        $bnames[] = $b_row['bname'];
    }
    if (!empty($bnames)) {
        $coordinator_bname = implode(', ', $bnames);
    }
}
?>
