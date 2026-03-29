<?php
require_once('connection.php');
$res = mysqli_query($con, "DESC registration");
while($row = mysqli_fetch_assoc($res)) {
    if (in_array($row['Field'], ['coordinator_approval_status', 'reg_status', 'caller_remark', 'reg_login_id', 'reg_password'])) {
        echo "{$row['Field']} | {$row['Type']}\n";
    }
}
?>
