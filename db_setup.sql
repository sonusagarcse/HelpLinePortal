CREATE TABLE IF NOT EXISTS `centre_coordinator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bid` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mob` varchar(20) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `date` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `caller` ADD COLUMN `earning_per_admission` DECIMAL(10,2) NOT NULL DEFAULT 0.00;

CREATE TABLE IF NOT EXISTS `caller_earnings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caller_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `registration` ADD COLUMN `coordinator_approval_status` INT(11) NOT NULL DEFAULT 0;
