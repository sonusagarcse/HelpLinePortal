<?php
session_start();
session_destroy();
header('Location: ../caller_login');
exit;
?>