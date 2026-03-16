<?php
error_reporting(0);
$con = mysqli_connect("localhost", "root", "", "lkvmyuvahelp");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8");

// Get global settings
$settings_result = mysqli_query($con, "SELECT * FROM global_setting WHERE id = 1");
$settings = mysqli_fetch_assoc($settings_result);

// Dynamic Site URL Detection
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
// Calculate path relative to document root
$project_dir = str_replace('\\', '/', __DIR__);
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$path = str_replace($doc_root, '', $project_dir);

// Remove leading slash if present in path to avoid double slashes if needed, 
// though generally browsers handle it. But strict equivalence:
// If path is empty (root), dynamic_url is just protocol://host
$dynamic_url = $protocol . "://" . $host . $path;

// Define common site variables from global settings
// We override SITE_URL with our dynamic one
$SITE_URL = $dynamic_url;
$SITE_NAME = $settings['site_name'];
$SITE_TITLE = $settings['site_name']; // Using site_name as title
$ADDRESS = $settings['address'];
$EMAIL_ID = $settings['email'];
$CONTACT_PHONE = $settings['phone'];
$MOBILE = $settings['phone'];

// Error reporting (comment these lines in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>