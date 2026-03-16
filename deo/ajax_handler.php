<?php
require_once('../connection.php');
require_once('config/auth.php');

$action = $_GET['action'] ?? '';

if ($action === 'check_mobile') {
    $mobile = mysqli_real_escape_string($con, $_GET['mobile']);
    $query = "SELECT name, regno FROM registration WHERE mob = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $mobile);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    
    echo json_encode(['exists' => !!$data, 'student' => $data]);
    exit;
}

if ($action === 'suggest_village') {
    $term = mysqli_real_escape_string($con, $_GET['term']);
    $query = "SELECT DISTINCT village FROM registration WHERE village LIKE ? AND village != '' ORDER BY village ASC LIMIT 10";
    $stmt = mysqli_prepare($con, $query);
    $search = $term . "%";
    mysqli_stmt_bind_param($stmt, "s", $search);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $suggestions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions[] = $row['village'];
    }
    
    echo json_encode($suggestions);
    exit;
}
