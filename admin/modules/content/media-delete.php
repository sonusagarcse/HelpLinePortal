<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($con, "DELETE FROM photos WHERE id = $id");
}
header('Location: media.php');
exit;
