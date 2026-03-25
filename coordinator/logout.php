<?php
session_start();
session_destroy();
header('Location: ../coordinator_login.php');
exit;
?>
