-- SQL Migration Script for Yuva Helpline Project Updates
-- Generated on 2026-03-28

-- 1. Create Centre Coordinator Table
CREATE TABLE IF NOT EXISTS `centre_coordinator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bid` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mob` varchar(20) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Create Caller Branches (Multi-Branch Assignment)
CREATE TABLE IF NOT EXISTS `caller_branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caller_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT 0,
  `assigned_date` date NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `caller_id` (`caller_id`),
  KEY `branch_id` (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create Caller Earnings (Commission Tracking)
CREATE TABLE IF NOT EXISTS `caller_earnings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caller_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `caller_id` (`caller_id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Update Registration Table Schema
-- Adding columns for Coordinator approvals and Student Credentials
ALTER TABLE `registration` 
  ADD COLUMN IF NOT EXISTS `coordinator_approval_status` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `reg_status` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `caller_remark` text DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `reg_login_id` varchar(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `reg_password` varchar(100) DEFAULT NULL;

-- 5. Standardize Status Defaults (Optional but recommended)
-- Ensure all existing records have status 0 for new columns
UPDATE `registration` SET `coordinator_approval_status` = 0 WHERE `coordinator_approval_status` IS NULL;
UPDATE `registration` SET `reg_status` = 0 WHERE `reg_status` IS NULL;
