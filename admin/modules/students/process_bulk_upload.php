<?php
require_once(dirname(dirname(__DIR__)) . '/config/config.php');
require_once(dirname(dirname(__DIR__)) . '/config/auth.php');
require_once(dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

header('Content-Type: application/json');

$response = [
    'success' => 0,
    'total' => 0,
    'errors' => []
];

try {
    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }

    $file = $_FILES['excel_file'];
    $allowedExtensions = ['xlsx', 'xls', 'csv'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        throw new Exception('Invalid file format. Only .xlsx, .xls, and .csv files are allowed');
    }

    // Load spreadsheet
    $spreadsheet = IOFactory::load($file['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    if (empty($rows)) {
        throw new Exception('The uploaded file is empty');
    }

    // Get header row
    $headers = array_shift($rows);
    $headers = array_map('trim', $headers);

    // Expected columns - map user-friendly names to database fields
    $columnMapping = [
        'Registration No' => 'regno',
        'regno' => 'regno',
        'Student Name' => 'name',
        'name' => 'name',
        'Father Name' => 'father',
        'father' => 'father',
        'Mother Name' => 'mother',
        'mother' => 'mother',
        'Mobile Number' => 'mob',
        'mob' => 'mob',
        'Email Address' => 'email',
        'email' => 'email',
        'Qualification' => 'qualification',
        'qualification' => 'qualification',
        'Date of Birth' => 'dob',
        'dob' => 'dob',
        'Gender' => 'gender',
        'gender' => 'gender',
        'Address' => 'address',
        'address' => 'address',
        'State' => 'state',
        'state' => 'state',
        'District' => 'dis',
        'dis' => 'dis',
        'Pincode' => 'pincode',
        'pincode' => 'pincode',
        'Caste' => 'caste',
        'caste' => 'caste',
        'Aadhar Number' => 'aadhar',
        'aadhar' => 'aadhar'
    ];

    // Required database fields (now excluding bid/mcategory as they come from POST)
    $requiredFields = ['regno', 'name', 'father', 'mob', 'qualification', 'dob', 'gender', 'address', 'state', 'dis'];

    // Create column index map (map Excel columns to database fields)
    $columnMap = [];
    foreach ($headers as $index => $header) {
        if (isset($columnMapping[$header])) {
            $dbField = $columnMapping[$header];
            $columnMap[$dbField] = $index;
        }
    }

    // Validate that all required fields are present
    $missingFields = array_diff($requiredFields, array_keys($columnMap));
    if (!empty($missingFields)) {
        throw new Exception('Missing required columns. Please ensure your Excel file has all required columns.');
    }

    $response['total'] = count($rows);
    $successCount = 0;
    $date = date('d-m-Y');

    // Process each row
    foreach ($rows as $rowIndex => $row) {
        $actualRow = $rowIndex + 2; // +2 because we removed header and Excel is 1-indexed

        try {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Extract data using the mapped column indices
            $regno = isset($columnMap['regno']) ? trim($row[$columnMap['regno']]) : '';
            $name = isset($columnMap['name']) ? trim($row[$columnMap['name']]) : '';
            $father = isset($columnMap['father']) ? trim($row[$columnMap['father']]) : '';
            $mother = isset($columnMap['mother']) ? trim($row[$columnMap['mother']]) : '';
            $mob = isset($columnMap['mob']) ? trim($row[$columnMap['mob']]) : '';
            $email = isset($columnMap['email']) ? trim($row[$columnMap['email']]) : '';
            $bid = (int)$_POST['bid'];
            $mcategory = (int)$_POST['mcategory'];
            $qualification = isset($columnMap['qualification']) ? trim($row[$columnMap['qualification']]) : '';
            $dob = isset($columnMap['dob']) ? trim($row[$columnMap['dob']]) : '';
            $gender = isset($columnMap['gender']) ? trim($row[$columnMap['gender']]) : '';
            $address = isset($columnMap['address']) ? trim($row[$columnMap['address']]) : '';
            $state = isset($columnMap['state']) ? trim($row[$columnMap['state']]) : '';
            $dis = isset($columnMap['dis']) ? trim($row[$columnMap['dis']]) : '';
            $pincode = isset($columnMap['pincode']) ? trim($row[$columnMap['pincode']]) : '';
            $caste = isset($columnMap['caste']) ? trim($row[$columnMap['caste']]) : '';
            $aadhar = isset($columnMap['aadhar']) ? trim($row[$columnMap['aadhar']]) : '';

            // Validate required fields
            if (empty($regno)) {
                throw new Exception('Registration number is required');
            }
            if (empty($name)) {
                throw new Exception('Name is required');
            }
            if (empty($father)) {
                throw new Exception('Father name is required');
            }
            if (empty($mob)) {
                throw new Exception('Mobile number is required');
            }
            if (!preg_match('/^[0-9]{10}$/', $mob)) {
                throw new Exception('Invalid mobile number format');
            }
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }
            if (empty($qualification)) {
                throw new Exception('Qualification is required');
            }

            // Validate branch exists
            $branchCheck = mysqli_query($con, "SELECT id FROM branch WHERE id = $bid");
            if (mysqli_num_rows($branchCheck) == 0) {
                throw new Exception("Branch ID $bid does not exist");
            }

            // Validate category exists
            $categoryCheck = mysqli_query($con, "SELECT id FROM member_category WHERE id = $mcategory");
            if (mysqli_num_rows($categoryCheck) == 0) {
                throw new Exception("Category ID $mcategory does not exist");
            }

            // Check for duplicate registration number
            $dupCheck = mysqli_query($con, "SELECT id FROM registration WHERE regno = '" . mysqli_real_escape_string($con, $regno) . "'");
            if (mysqli_num_rows($dupCheck) > 0) {
                throw new Exception("Registration number $regno already exists");
            }

            // Convert Excel date to MySQL format if needed
            if (!empty($dob)) {
                if (is_numeric($dob)) {
                    $dob = Date::excelToDateTimeObject($dob)->format('Y-m-d');
                } else {
                    $dob = date('Y-m-d', strtotime($dob));
                }
            } else {
                $dob = '0000-00-00';
            }

            // Prepare data for insertion
            $insertQuery = "INSERT INTO registration (regno, name, father, mother, mob, email, bid, mcategory, qualification, dob, gender, address, state, dis, pincode, caste, aadhar, date, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";

            $stmt = mysqli_prepare($con, $insertQuery);
            mysqli_stmt_bind_param(
                $stmt,
                "ssssssiissssssssss",
                $regno,
                $name,
                $father,
                $mother,
                $mob,
                $email,
                $bid,
                $mcategory,
                $qualification,
                $dob,
                $gender,
                $address,
                $state,
                $dis,
                $pincode,
                $caste,
                $aadhar,
                $date
            );

            if (mysqli_stmt_execute($stmt)) {
                $successCount++;
            } else {
                throw new Exception('Database error: ' . mysqli_error($con));
            }

        } catch (Exception $e) {
            $response['errors'][] = [
                'row' => $actualRow,
                'message' => $e->getMessage()
            ];
        }
    }

    $response['success'] = $successCount;

    // Log activity
    logActivity('bulk_upload_students', 'registration', 0, null, json_encode([
        'total' => $response['total'],
        'success' => $successCount,
        'errors' => count($response['errors'])
    ]));

} catch (Exception $e) {
    http_response_code(400);
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
?>
