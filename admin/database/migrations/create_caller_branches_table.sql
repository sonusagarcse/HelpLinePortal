-- Migration script for caller_branches table
-- This table stores multiple branch assignments for each caller for calling purposes

CREATE TABLE IF NOT EXISTS `caller_branches` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `caller_id` INT(11) NOT NULL,
  `branch_id` INT(11) NOT NULL,
  `assigned_date` DATE NOT NULL,
  `status` INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `caller_id` (`caller_id`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `fk_caller_branches_caller` FOREIGN KEY (`caller_id`) REFERENCES `caller` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_caller_branches_branch` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
