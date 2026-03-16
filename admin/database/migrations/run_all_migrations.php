<?php
/**
 * Run All Migrations
 * Execute this file to create all required tables
 */

require_once(dirname(dirname(dirname(__DIR__))) . '/connection.php');

echo "==============================================\n";
echo "Running All Migrations\n";
echo "==============================================\n\n";

$migrations = [
    'create_caller_branches_table.sql',
    'create_admin_logs_table.sql'
];

$totalSuccess = 0;
$totalFailed = 0;

foreach ($migrations as $migrationFile) {
    $filePath = __DIR__ . '/' . $migrationFile;

    if (!file_exists($filePath)) {
        echo "✗ Migration file not found: $migrationFile\n\n";
        $totalFailed++;
        continue;
    }

    echo "Running: $migrationFile\n";
    echo "----------------------------------------------\n";

    // Read the SQL file
    $sql = file_get_contents($filePath);

    // Split by semicolon to execute multiple statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    $success = true;
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0)
            continue;

        if (mysqli_query($con, $statement)) {
            echo "✓ ";
        } else {
            echo "✗ Error: " . mysqli_error($con) . "\n";
            $success = false;
        }
    }

    if ($success) {
        echo "\n✓ $migrationFile completed successfully!\n\n";
        $totalSuccess++;
    } else {
        echo "\n✗ $migrationFile failed!\n\n";
        $totalFailed++;
    }
}

echo "==============================================\n";
echo "Migration Summary\n";
echo "==============================================\n";
echo "✓ Successful: $totalSuccess\n";
echo "✗ Failed: $totalFailed\n\n";

// Verify tables exist
echo "Verifying tables...\n";
echo "----------------------------------------------\n";

$tables = ['caller_branches', 'admin_logs'];
foreach ($tables as $table) {
    $result = mysqli_query($con, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "✓ $table exists\n";
    } else {
        echo "✗ $table NOT found\n";
    }
}

echo "\n==============================================\n";
echo "Migration process completed!\n";
echo "==============================================\n";
?>
