<?php
require_once('c:/xampp/htdocs/yuvahelpline/connection.php');
$res = mysqli_query($con, 'SHOW TABLES');
while($row = mysqli_fetch_row($res)) {
    echo $row[0] . "\n";
}
?>
