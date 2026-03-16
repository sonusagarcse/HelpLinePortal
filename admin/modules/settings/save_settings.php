<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['settings'])) {
    $updates = [];
    foreach ($_POST['settings'] as $key => $value) {
        $key = mysqli_real_escape_string($con, $key);
        $value = mysqli_real_escape_string($con, $value);
        $updates[] = "$key = '$value'";
    }

    if (!empty($updates)) {
        $query = "UPDATE global_setting SET " . implode(', ', $updates) . " WHERE id = 1";
        if (mysqli_query($con, $query)) {
            header('Location: global.php?success=1');
            exit();
        } else {
            die("Error updating settings: " . mysqli_error($con));
        }
    }
}

header('Location: global.php');
exit();
