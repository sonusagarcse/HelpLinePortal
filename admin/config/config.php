<?php
/**
 * Admin Configuration File
 * This file should be included in all admin pages
 * It handles database connection and global settings
 */

// Include the main connection file from root
// admin/config/config.php -> go up 2 levels to reach root
require_once(dirname(dirname(__DIR__)) . '/connection.php');

// Admin-specific configurations can go here
?>
