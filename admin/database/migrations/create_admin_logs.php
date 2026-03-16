<?php
require_once(dirname(dirname(dirname(__DIR__))) . '/connection.php');

echo "Creating admin_logs table...\n\n";

$sql = "CREATE TABLE IF NOT EXISTS `admin_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `admin_id` INT(11) NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `table_name` VARCHAR(100) DEFAULT NULL,
  `record_id` INT(11) DEFAULT NULL,
  `old_value` TEXT DEFAULT NULL,
  `new_value` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `action` (`action`),
  KEY `table_name` (`table_name`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if (mysqli_query($con, $sql)) {
    echo "✓ admin_logs table created successfully!\n\n";

    // Verify
    $result = mysqli_query($con, "SHOW TABLES LIKE 'admin_logs'");
    if (mysqli_num_rows($result) > 0) {
        echo "✓ Verified: admin_logs table exists\n\n";
        echo "Table structure:\n";
        $structure = mysqli_query($con, "DESCRIBE admin_logs");
        while ($row = mysqli_fetch_assoc($structure)) {
            echo "  - {$row['Field']} ({$row['Type']})\n";
        }
    }
} else {
    echo "✗ Error: " . mysqli_error($con) . "\n";
}
?>
