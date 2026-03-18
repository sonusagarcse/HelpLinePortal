<?php
include 'connection.php';
$r = mysqli_query($con, 'DESCRIBE global_setting');
$cols = [];
while($row = mysqli_fetch_assoc($r)) $cols[] = $row['Field'];
echo "Columns: " . implode(', ', $cols) . "\n";
?>
