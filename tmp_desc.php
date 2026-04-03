<?php
require_once('connection.php');
$res = mysqli_query($con, "DESCRIBE mquery");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
