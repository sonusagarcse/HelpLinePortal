<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

header('Content-Type: application/json');

if (!isset($_GET['bid']) || empty($_GET['bid'])) {
    echo json_encode([]);
    exit;
}

$bid = (int)$_GET['bid'];
$categories = [];

$query = "SELECT id, name FROM member_category WHERE bid = ? ORDER BY name ASC";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $bid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

echo json_encode($categories);
?>
