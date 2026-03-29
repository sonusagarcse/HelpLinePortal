<?php
require_once('connection.php');
$tables = ['caller_branches', 'caller_earnings', 'registration', 'centre_coordinator'];
foreach ($tables as $table) {
    echo "\n--- Table: $table ---\n";
    $res = mysqli_query($con, "DESC $table");
    if ($res) {
        while($row = mysqli_fetch_assoc($res)) {
            echo "{$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Key']} | {$row['Default']} | {$row['Extra']}\n";
        }
    } else {
        echo "Table $table not found.\n";
    }
}
?>
