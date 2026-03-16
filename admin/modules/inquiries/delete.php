<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (mysqli_query($con, "DELETE FROM mquery WHERE id = $id")) {
        header('Location: list.php?success=deleted');
    } else {
        header('Location: list.php?error=delete_failed');
    }
} else {
    header('Location: list.php');
}
exit;
