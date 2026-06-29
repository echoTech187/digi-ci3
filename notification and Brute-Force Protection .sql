CREATE TABLE IF NOT EXISTS `admin_notifications` (
    `id`         BIGINT NOT NULL AUTO_INCREMENT,
    `type`       ENUM(
                     'maintenance',  
                     'login_new_ip', 
                     'dlq_failed'     
                 ) NOT NULL,
    `title`      VARCHAR(255)      NOT NULL COMMENT 'Judul singkat notifikasi',
    `message`    TEXT              NOT NULL COMMENT 'Isi pesan lengkap notifikasi',
    `ref_data`   VARCHAR(1000)     NULL     COMMENT 'JSON: context data tambahan (queue_name, ip_address, admin_email, dll)',
    `is_read`    TINYINT(1)        NOT NULL DEFAULT 0 COMMENT '0=belum dibaca, 1=sudah dibaca',
    `created_at` DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_unread_recent`  (`is_read`, `created_at`),
    KEY `idx_created_at`     (`created_at`),
    KEY `idx_type_read_date` (`type`, `is_read`, `created_at`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Notifikasi real-time untuk admin dashboard digi-ci3';


CREATE TABLE IF NOT EXISTS `admin_known_ips` (
    `id`          INT  NOT NULL AUTO_INCREMENT,
    `admin_id`    INT           NOT NULL COMMENT 'Referensi ke tabel admin.id',
    `ip_address`  VARCHAR(45)   NOT NULL COMMENT 'IPv4 (max 15 char) atau IPv6 (max 45 char)',
    `first_seen`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Pertama kali IP ini digunakan oleh admin ini',
    `last_seen`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Terakhir kali IP ini digunakan',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_admin_ip`   (`admin_id`, `ip_address`),
    KEY `idx_admin_id`         (`admin_id`)

) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='IP address yang sudah dikenal per admin, untuk deteksi login IP baru';

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `email` varchar(120) NOT NULL,
  `time` int(11) NOT NULL,
  `cleared` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_ip_search` (`ip_address`, `cleared`, `time`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
