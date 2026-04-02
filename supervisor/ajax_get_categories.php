<?php
session_start();
require_once(__DIR__ . '/../connection.php');

if (!isset($_SESSION['supervisor_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$branch_id = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : 0;

if ($branch_id > 0) {
    // Security Check: Ensure the selected branch is assigned to the supervisor
    $supervisor_bids = $_SESSION['supervisor_bids'] ?? [];
    if (!in_array($branch_id, $supervisor_bids)) {
        http_response_code(403);
        exit('Unauthorized branch');
    }

    $query = "SELECT id, name FROM member_category WHERE bid = ? ORDER BY name ASC";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $branch_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($categories);
} else {
    echo json_encode([]);
}
?>
