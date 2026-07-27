-- Migration: Auth Security & Rate Limiting
-- Adds tables required for Brute-Force protection and New IP detection

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ip_address` VARCHAR(45) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `time` INT(11) NOT NULL,
  `cleared` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_ip_time` (`ip_address`, `time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `admin_known_ips` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `admin_id` INT(11) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `first_seen` DATETIME NOT NULL,
  `last_seen` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admin_ip` (`admin_id`, `ip_address`),
  INDEX `idx_admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
