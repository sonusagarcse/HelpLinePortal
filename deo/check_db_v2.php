<?php
require_once('../connection.php');
$res = mysqli_query($con, "DESCRIBE member_category");
if ($res) {
    while($row = mysqli_fetch_assoc($res)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error: " . mysqli_error($con);
}
?>
