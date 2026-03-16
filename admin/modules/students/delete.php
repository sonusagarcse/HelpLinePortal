<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = (int)$_GET['id'];

// Get student name for logging
$result = mysqli_query($con, "SELECT name FROM registration WHERE id = $id");
$student = mysqli_fetch_assoc($result);

if ($student) {
    if (mysqli_query($con, "DELETE FROM registration WHERE id = $id")) {
        logActivity('delete_student', 'registration', $id, null, json_encode(['name' => $student['name']]));
        header('Location: list.php?success=deleted');
        exit;
    } else {
        header('Location: list.php?error=failed');
        exit;
    }
} else {
    header('Location: list.php');
    exit;
}
