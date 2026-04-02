<?php
session_start();
require_once(__DIR__ . '/../connection.php');

if (!isset($_SESSION['supervisor_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$mob = isset($_GET['mob']) ? mysqli_real_escape_string($con, $_GET['mob']) : '';
$bid = isset($_GET['bid']) ? (int)$_GET['bid'] : 0;

$response = ['exists' => false, 'message' => ''];

if (strlen($mob) == 10 && $bid > 0) {
    $query = "SELECT id, name, regno FROM registration WHERE mob = ? AND bid = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "si", $mob, $bid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $response['exists'] = true;
        $response['message'] = "Already registered: " . $row['name'] . " (" . $row['regno'] . ")";
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
