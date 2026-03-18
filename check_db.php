<?php
$c = mysqli_connect('localhost', 'root', '', 'yuvahelpline');
if (!$c) die("Connection failed: " . mysqli_connect_error());

$tables = [];
$r = mysqli_query($c, 'SHOW TABLES');
while($row = mysqli_fetch_row($r)) $tables[] = $row[0];

echo "Tables: " . implode(', ', $tables) . "\n";

if (in_array('settings', $tables)) {
    $r = mysqli_query($c, 'DESCRIBE settings');
    echo "Settings columns: ";
    while($col = mysqli_fetch_assoc($r)) echo $col['Field'] . ", ";
    echo "\n";
} else {
    echo "No settings table found.\n";
}
?>
