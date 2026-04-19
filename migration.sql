-- 5. Caller Types & UG/PG Workflows
ALTER TABLE `caller` ADD COLUMN IF NOT EXISTS `caller_type` ENUM('KYP', 'UG_PG') NOT NULL DEFAULT 'KYP' AFTER `status`;

ALTER TABLE `registration` 
  ADD COLUMN IF NOT EXISTS `ugpg_caller_remark` TEXT NULL AFTER `caller_remark`,
  ADD COLUMN IF NOT EXISTS `ugpg_status` INT(1) NOT NULL DEFAULT 0 AFTER `reg_status`,
  ADD COLUMN IF NOT EXISTS `ugpg_assigned_caller` INT(11) NULL AFTER `assigned_caller`;

ALTER TABLE `mquery` ADD COLUMN IF NOT EXISTS `query_type` VARCHAR(20) NOT NULL DEFAULT 'KYP' AFTER `status`;
