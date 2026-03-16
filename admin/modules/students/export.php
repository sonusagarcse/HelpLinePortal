<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');

// Get all students
$query = "SELECT r.*, b.bname, mc.name as category_name 
          FROM registration r 
          LEFT JOIN branch b ON r.bid = b.id 
          LEFT JOIN member_category mc ON r.mcategory = mc.id 
          ORDER BY r.id DESC";
$result = mysqli_query($con, $query);

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="students_list_' . date('Y-m-d') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Output Excel content
echo '<table border="1">';
echo '<thead>';
echo '<tr>';
echo '<th>ID</th>';
echo '<th>Reg No</th>';
echo '<th>Name</th>';
echo '<th>Father Name</th>';
echo '<th>Mother Name</th>';
echo '<th>Branch</th>';
echo '<th>Category</th>';
echo '<th>Qualification</th>';
echo '<th>DOB</th>';
echo '<th>Age</th>';
echo '<th>Gender</th>';
echo '<th>Mobile</th>';
echo '<th>Email</th>';
echo '<th>State</th>';
echo '<th>District</th>';
echo '<th>Address</th>';
echo '<th>Aadhar</th>';
echo '<th>Registration Date</th>';
echo '<th>Status</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

while ($student = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    echo '<td>' . $student['id'] . '</td>';
    echo '<td>' . htmlspecialchars($student['regno'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['name'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['father'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['mother'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['bname'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['category_name'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['qualification'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['dob'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['age'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['gender'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['mob'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['email'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['state'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['dis'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['address'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['aadhar'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($student['date'] ?? '') . '</td>';
    echo '<td>' . ($student['status'] == 1 ? 'Active' : 'Inactive') . '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';
exit;
?>
