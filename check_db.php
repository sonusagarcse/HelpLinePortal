<?php
require_once('connection.php');
$res = mysqli_query($con, "DESCRIBE member_category");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
