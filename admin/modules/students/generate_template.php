<?php
require_once(dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Create new Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Students Data');

// Set headers with user-friendly names
$headers = [
    'Registration No',
    'Student Name',
    'Father Name',
    'Mother Name',
    'Mobile Number',
    'Email Address',
    'Branch ID',
    'Category ID',
    'Qualification',
    'Date of Birth',
    'Gender',
    'Address',
    'State',
    'District',
    'Pincode',
    'Caste',
    'Aadhar Number'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $sheet->getStyle($col . '1')->getFont()->setBold(true);
    $sheet->getStyle($col . '1')->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setRGB('4472C4');
    $sheet->getStyle($col . '1')->getFont()->getColor()->setRGB('FFFFFF');
    $sheet->getStyle($col . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $col++;
}

// Add sample data row
$sampleData = [
    'STU2024001',
    'Rahul Kumar',
    'Rajesh Kumar',
    'Sunita Devi',
    '9876543210',
    'rahul.kumar@example.com',
    '1',
    '1',
    'B.Tech',
    '2000-01-15',
    'Male',
    'House No 123, Main Road, Sector 5',
    'Bihar',
    'Gaya',
    '823001',
    'General',
    '123456789012'
];

$col = 'A';
foreach ($sampleData as $data) {
    $sheet->setCellValue($col . '2', $data);
    $col++;
}

// Add instructions sheet
$instructionsSheet = $spreadsheet->createSheet();
$instructionsSheet->setTitle('Instructions');

$instructions = [
    ['BULK UPLOAD INSTRUCTIONS'],
    [''],
    ['1. Fill in the "Students Data" sheet with student information'],
    ['2. Do not modify the column headers'],
    ['3. All fields are required except Mother Name, Email, Pincode, Caste, and Aadhar'],
    ['4. Date format: YYYY-MM-DD (e.g., 2000-01-15)'],
    ['5. Gender: Male, Female, or Other'],
    ['6. Mobile: 10-digit number without country code'],
    ['7. Branch ID and Category ID must exist in the system'],
    ['8. Save as .xlsx or .xls format and upload through admin panel'],
    [''],
    ['COLUMN DESCRIPTIONS:'],
    ['Registration No - Unique registration number (e.g., STU2024001)'],
    ['Student Name - Full name of the student'],
    ['Father Name - Father\'s full name'],
    ['Mother Name - Mother\'s full name (optional)'],
    ['Mobile Number - 10-digit mobile number'],
    ['Email Address - Valid email address (optional)'],
    ['Branch ID - Branch identifier (must exist in system)'],
    ['Category ID - Student category identifier (must exist in system)'],
    ['Qualification - Educational qualification (e.g., B.Tech, Graduate)'],
    ['Date of Birth - Format: YYYY-MM-DD'],
    ['Gender - Male, Female, or Other'],
    ['Address - Complete residential address'],
    ['State - State name'],
    ['District - District name'],
    ['Pincode - 6-digit postal code (optional)'],
    ['Caste - Caste category (optional)'],
    ['Aadhar Number - 12-digit Aadhar number (optional)']
];

$row = 1;
foreach ($instructions as $instruction) {
    $instructionsSheet->setCellValue('A' . $row, $instruction[0]);
    if ($row == 1) {
        $instructionsSheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
    } elseif (strpos($instruction[0], 'COLUMN DESCRIPTIONS') !== false) {
        $instructionsSheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(12);
    }
    $row++;
}

$instructionsSheet->getColumnDimension('A')->setWidth(80);

// Auto-size columns in data sheet
$spreadsheet->setActiveSheetIndex(0);
foreach (range('A', 'Q') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Save file
$writer = new Xlsx($spreadsheet);
$writer->save(__DIR__ . '/templates/sample_template.xlsx');

echo "Sample template created successfully!";
?>
