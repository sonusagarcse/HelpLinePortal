-- SQL Migration Script for Yuva Helpline Project Updates
-- Generated on 2026-03-31 (Latest Updates Only)

-- 1. Supervisor Wallet System
ALTER TABLE `supervisor` 
  ADD COLUMN IF NOT EXISTS `wallet_balance` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `status`;

-- 2. Supervisor Earnings Table (Commission Tracking)
CREATE TABLE IF NOT EXISTS `supervisor_earnings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supervisor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) DEFAULT 'Registration Approval Commission',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `supervisor_id` (`supervisor_id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Supervisor Individual Commission System
ALTER TABLE `supervisor` 
  ADD COLUMN IF NOT EXISTS `commission_per_reg` DECIMAL(10,2) DEFAULT 0.00 AFTER `wallet_balance`;

-- 4. WhatsApp Template Management System
CREATE TABLE IF NOT EXISTS `whatsapp_templates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `status` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Initial WhatsApp Templates
INSERT INTO `whatsapp_templates` (`title`, `message`) VALUES 
('Default Welcome', 'Hello [name], this is from Yuva Helpline.'),
('Document Required', 'Hello [name], we noticed some of your documents are missing. Please send them as soon as possible.'),
('Fee Reminder', 'Hello [name], this is a reminder regarding your course fee payment. Please process it soon.'),
('Follow-up', 'Hello [name], I tried calling you earlier. Please call back when you\'re available.');

