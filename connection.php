<?php

// Load ENV
$env = parse_ini_file(__DIR__ . '/.env');

// DB Connection
$con = mysqli_connect(
    $env['DB_HOST'],
    $env['DB_USER'],
    $env['DB_PASS'],
    $env['DB_NAME']
);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8mb4");

// Get global settings
$settings_result = mysqli_query($con, "SELECT * FROM global_setting WHERE id = 1");
$settings = mysqli_fetch_assoc($settings_result);

// Dynamic Site URL Detection
if (!empty($env['APP_URL'])) {
    $SITE_URL = rtrim($env['APP_URL'], '/');
} else {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $project_dir = str_replace('\\', '/', __DIR__);
    $doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $path = str_replace($doc_root, '', $project_dir);
    $SITE_URL = $protocol . "://" . $host . $path;
}

$SITE_NAME = $settings['site_name'];
$SITE_TITLE = $settings['site_name'];
$ADDRESS = $settings['address'];
$EMAIL_ID = $settings['email'];
$CONTACT_PHONE = $settings['phone'];
$MOBILE = $settings['phone'];

?>