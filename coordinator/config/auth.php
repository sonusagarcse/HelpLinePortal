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

// Fetch latest branch name for UI
$b_query = "SELECT bname FROM branch WHERE id = ?";
$b_stmt = mysqli_prepare($con, $b_query);
mysqli_stmt_bind_param($b_stmt, "i", $coordinator_bid);
mysqli_stmt_execute($b_stmt);
$b_res = mysqli_stmt_get_result($b_stmt);
if($b_row = mysqli_fetch_assoc($b_res)) {
    $coordinator_bname = $b_row['bname'];
} else {
    $coordinator_bname = "Unknown Branch";
}
?>
