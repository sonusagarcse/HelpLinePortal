<?php
session_start();
require_once(__DIR__ . '/../connection.php');

if (!isset($_SESSION['supervisor_id'])) {
    exit('Unauthorized');
}

$supervisor_id = $_SESSION['supervisor_id'];
$caller_id = isset($_POST['caller_id']) ? (int) $_POST['caller_id'] : 0;

if ($caller_id <= 0) {
    exit('<tr><td colspan="7" class="text-center">Please select a caller first.</td></tr>');
}

// Fetch Students that are NOT already explicitly assigned to this caller
// We restrict assignment to the supervisor's branch
$supervisor_bid = $_SESSION['supervisor_bid'] ?? 0;
$branch_filter = ($supervisor_bid > 0) ? " AND r.bid = " . (int)$supervisor_bid : "";

$students_query = "SELECT r.id, r.regno, r.name, r.father, r.mob, r.bid, r.mcategory, 
                   b.bname, mc.name as category_name
                   FROM registration r
                   LEFT JOIN branch b ON r.bid = b.id
                   LEFT JOIN member_category mc ON r.mcategory = mc.id
                   WHERE r.assigned_caller = 0 AND r.status = 1 $branch_filter
                   ORDER BY r.id DESC LIMIT 300";

$result = mysqli_query($con, $students_query);

$output_rows = 0;
while ($student = mysqli_fetch_assoc($result)) {
    $output_rows++;
    ?>
    <tr>
        <td>
            <input type="checkbox" name="students[]" value="<?php echo $student['id']; ?>" class="student-checkbox"
                onchange="updateCount()">
        </td>
        <td><?php echo htmlspecialchars($student['regno']); ?></td>
        <td><?php echo htmlspecialchars($student['name']); ?></td>
        <td><?php echo htmlspecialchars($student['father']); ?></td>
        <td><?php echo htmlspecialchars($student['mob']); ?></td>
        <td><?php echo htmlspecialchars($student['bname'] ?? 'N/A'); ?></td>
        <td><?php echo htmlspecialchars($student['category_name'] ?? 'N/A'); ?></td>
    </tr>
    <?php
}

if ($output_rows == 0) {
    echo '<tr><td colspan="7" class="text-center">No available unassigned data found.</td></tr>';
}
?>