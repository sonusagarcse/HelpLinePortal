<?php
/**
 * Run Migration: Create caller_branches table
 * Execute this file once to create the table
 */

require_once(dirname(dirname(dirname(__DIR__))) . '/connection.php');

echo "Running migration: Create caller_branches table...\n\n";

// Read the SQL file
$sql = file_get_contents(__DIR__ . '/create_caller_branches_table.sql');

// Split by semicolon to execute multiple statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = true;
foreach ($statements as $statement) {
    if (empty($statement))
        continue;

    echo "Executing: " . substr($statement, 0, 50) . "...\n";

    if (mysqli_query($con, $statement)) {
        echo "✓ Success\n\n";
    } else {
        echo "✗ Error: " . mysqli_error($con) . "\n\n";
        $success = false;
    }
}

if ($success) {
    echo "\n✓ Migration completed successfully!\n";
    echo "The caller_branches table has been created.\n";
} else {
    echo "\n✗ Migration failed. Please check the errors above.\n";
}

// Verify table exists
$result = mysqli_query($con, "SHOW TABLES LIKE 'caller_branches'");
if (mysqli_num_rows($result) > 0) {
    echo "\n✓ Verified: caller_branches table exists in database.\n";

    // Show table structure
    echo "\nTable structure:\n";
    $structure = mysqli_query($con, "DESCRIBE caller_branches");
    while ($row = mysqli_fetch_assoc($structure)) {
        echo "  - {$row['Field']} ({$row['Type']})\n";
    }
} else {
    echo "\n✗ Warning: caller_branches table not found!\n";
}
?>
