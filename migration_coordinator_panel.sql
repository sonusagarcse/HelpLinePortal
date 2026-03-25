-- ==========================================================
-- Database Migration Script
-- Feature: Centre Coordinator Panel & Caller Earnings
-- Database: lkvmyuvahelp
-- ==========================================================

-- 1. Create centre_coordinator table
CREATE TABLE IF NOT EXISTS `centre_coordinator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bid` int(11) NOT NULL COMMENT 'Branch ID maps to branch table',
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create caller_earnings table
CREATE TABLE IF NOT EXISTS `caller_earnings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caller_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Add earning_per_admission to caller table
-- Only add if it does not already exist
ALTER TABLE `caller` ADD IF NOT EXISTS `earning_per_admission` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `status`;

-- 4. Add coordinator_approval_status to registration table
-- Workflow states: 
-- 0 = Default system resolved
-- 1 = Pending Coordinator Approval
-- 2 = Fully Approved (Earnings Triggered)
-- 3 = Rejected (Earnings Revoked)
ALTER TABLE `registration` ADD IF NOT EXISTS `coordinator_approval_status` tinyint(2) NOT NULL DEFAULT 0 AFTER `status`;

-- ==========================================================
