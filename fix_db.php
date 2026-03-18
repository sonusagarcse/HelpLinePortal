<?php
include 'connection.php';
$check = mysqli_query($con, "SHOW COLUMNS FROM global_setting LIKE 'whatsapp_msg'");
if(mysqli_num_rows($check) == 0) {
    echo "Column missing. Adding it now...\n";
    $res = mysqli_query($con, "ALTER TABLE global_setting ADD COLUMN whatsapp_msg TEXT AFTER mobile1");
    if($res) echo "Successfully added whatsapp_msg column.\n";
    else echo "Error adding column: " . mysqli_error($con) . "\n";
} else {
    echo "Column already exists.\n";
}
?>
