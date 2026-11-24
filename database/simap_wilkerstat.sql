-- ============================================================
-- SIMAP WILKERSTAT - Skema Database
-- Engine: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION';

CREATE DATABASE IF NOT EXISTS `simap_wilkerstat`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `simap_wilkerstat`;

-- ------------------------------------------------------------
-- 1. USERS & PROFIL
-- ------------------------------------------------------------
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('super_admin','ketua_tim') NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `failed_attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `locked_until` DATETIME NULL,
    `last_login_at` DATETIME NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_username` (`username`),
    KEY `idx_users_role` (`role`),
    KEY `idx_users_active` (`is_active`),
    CONSTRAINT `fk_users_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_profiles` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `whatsapp` VARCHAR(20) NULL,
    `photo_path` VARCHAR(255) NULL,
    `jabatan` VARCHAR(100) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_user_profiles_user` (`user_id`),
    CONSTRAINT `fk_user_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_assignments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `region_id` BIGINT UNSIGNED NOT NULL,
    `level` ENUM('provinsi','kabupaten','kecamatan','desa','sls') NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_user_assignments` (`user_id`,`region_id`),
    KEY `idx_user_assignments_region` (`region_id`),
    CONSTRAINT `fk_user_assignments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `token_hash` CHAR(64) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `used_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_password_resets_token` (`token_hash`),
    KEY `idx_password_resets_user` (`user_id`),
    CONSTRAINT `fk_password_resets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `login_attempts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `success` TINYINT(1) NOT NULL DEFAULT 0,
    `attempted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_login_attempts_user_ip_time` (`username`,`ip_address`,`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `login_histories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `login_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `logout_at` DATETIME NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `session_id` VARCHAR(128) NULL,
    PRIMARY KEY (`id`),
    KEY `idx_login_histories_user` (`user_id`),
    CONSTRAINT `fk_login_histories_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. REGIONS & WILKERSTAT
-- ------------------------------------------------------------
CREATE TABLE `regions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_id` BIGINT UNSIGNED NULL,
    `level` ENUM('provinsi','kabupaten','kecamatan','desa','sls') NOT NULL,
    `kode` VARCHAR(20) NOT NULL,
    `nama` VARCHAR(150) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_regions_kode` (`kode`),
    KEY `idx_regions_parent_level` (`parent_id`,`level`),
    CONSTRAINT `fk_regions_parent` FOREIGN KEY (`parent_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `user_assignments`
    ADD CONSTRAINT `fk_user_assignments_region` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE `wilkerstat` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_wilkerstat` VARCHAR(30) NOT NULL,
    `region_id` BIGINT UNSIGNED NOT NULL,
    `nama_sls` VARCHAR(150) NOT NULL,
    `metadata` JSON NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_wilkerstat_id` (`id_wilkerstat`),
    KEY `idx_wilkerstat_region` (`region_id`),
    CONSTRAINT `fk_wilkerstat_region` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. MAP FILES
-- ------------------------------------------------------------
CREATE TABLE `map_files` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `wilkerstat_id` BIGINT UNSIGNED NOT NULL,
    `file_type` ENUM('jpg','rar') NOT NULL,
    `drive_file_id` VARCHAR(100) NOT NULL,
    `current_version_id` BIGINT UNSIGNED NULL,
    `status` ENUM('active','archived','missing') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_map_files_drive` (`drive_file_id`),
    KEY `idx_map_files_wilkerstat` (`wilkerstat_id`),
    CONSTRAINT `fk_map_files_wilkerstat` FOREIGN KEY (`wilkerstat_id`) REFERENCES `wilkerstat` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `map_file_versions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `map_file_id` BIGINT UNSIGNED NOT NULL,
    `version_number` INT UNSIGNED NOT NULL,
    `drive_file_id` VARCHAR(100) NOT NULL,
    `file_name` VARCHAR(255) NULL,
    `file_size` BIGINT UNSIGNED NULL,
    `checksum` CHAR(64) NULL,
    `uploaded_by` BIGINT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_map_file_versions` (`map_file_id`,`version_number`),
    CONSTRAINT `fk_map_file_versions_map` FOREIGN KEY (`map_file_id`) REFERENCES `map_files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_map_file_versions_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `map_files`
    ADD CONSTRAINT `fk_map_files_current_version` FOREIGN KEY (`current_version_id`) REFERENCES `map_file_versions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- ------------------------------------------------------------
-- 4. REQUESTS
-- ------------------------------------------------------------
CREATE TABLE `requests` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_number` VARCHAR(30) NOT NULL,
    `requester_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('draft','waiting_verification','processing','completed','rejected','cancelled') NOT NULL DEFAULT 'draft',
    `need_download` TINYINT(1) NOT NULL DEFAULT 0,
    `need_print` TINYINT(1) NOT NULL DEFAULT 0,
    `print_copies` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    `notes` TEXT NULL,
    `submitted_at` DATETIME NULL,
    `completed_at` DATETIME NULL,
    `cancelled_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_requests_number` (`request_number`),
    KEY `idx_requests_requester` (`requester_id`),
    KEY `idx_requests_status` (`status`),
    KEY `idx_requests_submitted` (`submitted_at`),
    CONSTRAINT `fk_requests_requester` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `request_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_id` BIGINT UNSIGNED NOT NULL,
    `wilkerstat_id` BIGINT UNSIGNED NOT NULL,
    `source` ENUM('manual','excel') NOT NULL DEFAULT 'manual',
    `excel_file_name` VARCHAR(255) NULL,
    `status` ENUM('pending','approved','rejected','on_hold') NOT NULL DEFAULT 'pending',
    `reject_reason` TEXT NULL,
    `verified_by` BIGINT UNSIGNED NULL,
    `verified_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_request_items` (`request_id`,`wilkerstat_id`),
    KEY `idx_request_items_status` (`status`),
    CONSTRAINT `fk_request_items_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_request_items_wilkerstat` FOREIGN KEY (`wilkerstat_id`) REFERENCES `wilkerstat` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_request_items_verifier` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `request_services` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_id` BIGINT UNSIGNED NOT NULL,
    `service_type` ENUM('download','print') NOT NULL,
    `status` ENUM('pending','processing','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
    `progress_percent` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `started_at` DATETIME NULL,
    `completed_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_request_services` (`request_id`,`service_type`),
    CONSTRAINT `fk_request_services_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `request_status_histories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_id` BIGINT UNSIGNED NOT NULL,
    `from_status` VARCHAR(30) NULL,
    `to_status` VARCHAR(30) NOT NULL,
    `changed_by` BIGINT UNSIGNED NULL,
    `note` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_rsh_request` (`request_id`),
    CONSTRAINT `fk_rsh_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_rsh_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `request_approvals` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_item_id` BIGINT UNSIGNED NOT NULL,
    `decision` ENUM('approve','reject','hold') NOT NULL,
    `reason` TEXT NULL,
    `decided_by` BIGINT UNSIGNED NOT NULL,
    `decided_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_request_approvals_item` (`request_item_id`),
    CONSTRAINT `fk_request_approvals_item` FOREIGN KEY (`request_item_id`) REFERENCES `request_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_request_approvals_user` FOREIGN KEY (`decided_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `request_cancellations` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_id` BIGINT UNSIGNED NOT NULL,
    `reason` TEXT NOT NULL,
    `cancelled_by` BIGINT UNSIGNED NOT NULL,
    `cancelled_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_request_cancellations` (`request_id`),
    CONSTRAINT `fk_request_cancellations_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_request_cancellations_user` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. DOWNLOADS
-- ------------------------------------------------------------
CREATE TABLE `downloads` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_id` BIGINT UNSIGNED NOT NULL,
    `download_type` ENUM('jpg','rar','zip') NOT NULL,
    `status` ENUM('preparing','ready','expired','failed','cancelled') NOT NULL DEFAULT 'preparing',
    `file_path` VARCHAR(255) NULL,
    `file_size` BIGINT UNSIGNED NULL,
    `token_hash` CHAR(64) NULL,
    `expires_at` DATETIME NULL,
    `download_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `max_downloads` SMALLINT UNSIGNED NOT NULL DEFAULT 10,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ready_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_downloads_token` (`token_hash`),
    KEY `idx_downloads_request` (`request_id`),
    KEY `idx_downloads_status` (`status`),
    CONSTRAINT `fk_downloads_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_downloads_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `download_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `download_id` BIGINT UNSIGNED NOT NULL,
    `request_item_id` BIGINT UNSIGNED NOT NULL,
    `map_file_id` BIGINT UNSIGNED NOT NULL,
    `included` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_download_items_download` (`download_id`),
    CONSTRAINT `fk_download_items_download` FOREIGN KEY (`download_id`) REFERENCES `downloads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_download_items_request_item` FOREIGN KEY (`request_item_id`) REFERENCES `request_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_download_items_map_file` FOREIGN KEY (`map_file_id`) REFERENCES `map_files` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. PRINTING
-- ------------------------------------------------------------
CREATE TABLE `printers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `location` VARCHAR(150) NULL,
    `driver_name` VARCHAR(150) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `agent_key_hash` CHAR(64) NULL,
    `last_seen_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `print_jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_id` BIGINT UNSIGNED NOT NULL,
    `printer_id` BIGINT UNSIGNED NULL,
    `paper_size` VARCHAR(10) NOT NULL DEFAULT 'A4',
    `orientation` ENUM('portrait','landscape') NOT NULL DEFAULT 'portrait',
    `copies` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    `order_no` INT UNSIGNED NOT NULL DEFAULT 0,
    `status` ENUM('queued','printing','done','failed','cancelled') NOT NULL DEFAULT 'queued',
    `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `agent_key_hash` CHAR(64) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `started_at` DATETIME NULL,
    `finished_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_print_jobs_request` (`request_id`),
    KEY `idx_print_jobs_status` (`status`),
    CONSTRAINT `fk_print_jobs_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_print_jobs_printer` FOREIGN KEY (`printer_id`) REFERENCES `printers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `print_job_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `print_job_id` BIGINT UNSIGNED NOT NULL,
    `request_item_id` BIGINT UNSIGNED NOT NULL,
    `map_file_id` BIGINT UNSIGNED NULL,
    `status` ENUM('pending','printed','failed') NOT NULL DEFAULT 'pending',
    PRIMARY KEY (`id`),
    KEY `idx_print_job_items_job` (`print_job_id`),
    CONSTRAINT `fk_print_job_items_job` FOREIGN KEY (`print_job_id`) REFERENCES `print_jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_print_job_items_request_item` FOREIGN KEY (`request_item_id`) REFERENCES `request_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_print_job_items_map_file` FOREIGN KEY (`map_file_id`) REFERENCES `map_files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `print_histories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `print_job_id` BIGINT UNSIGNED NOT NULL,
    `event` VARCHAR(50) NOT NULL,
    `message` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_print_histories_job` (`print_job_id`),
    CONSTRAINT `fk_print_histories_job` FOREIGN KEY (`print_job_id`) REFERENCES `print_jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `print_receipts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `print_job_id` BIGINT UNSIGNED NOT NULL,
    `received_by_name` VARCHAR(100) NOT NULL,
    `received_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `confirmed_by` BIGINT UNSIGNED NULL,
    `signature_note` TEXT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_print_receipts_job` (`print_job_id`),
    CONSTRAINT `fk_print_receipts_job` FOREIGN KEY (`print_job_id`) REFERENCES `print_jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_print_receipts_user` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. NOTIFIKASI
-- ------------------------------------------------------------
CREATE TABLE `notifications` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `ref_type` VARCHAR(50) NULL,
    `ref_id` BIGINT UNSIGNED NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `read_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_notifications_user_read` (`user_id`,`is_read`),
    CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notification_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `notification_id` BIGINT UNSIGNED NOT NULL,
    `channel` ENUM('web','wa','email') NOT NULL,
    `status` ENUM('pending','sent','failed') NOT NULL DEFAULT 'pending',
    `provider_id` VARCHAR(100) NULL,
    `error_message` TEXT NULL,
    `sent_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_notification_logs_notif` (`notification_id`),
    CONSTRAINT `fk_notification_logs_notif` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. KOMUNIKASI
-- ------------------------------------------------------------
CREATE TABLE `conversations` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_id` BIGINT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_conversations_request` (`request_id`),
    CONSTRAINT `fk_conversations_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `messages` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `conversation_id` BIGINT UNSIGNED NOT NULL,
    `sender_id` BIGINT UNSIGNED NOT NULL,
    `body` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_messages_conversation` (`conversation_id`,`created_at`),
    CONSTRAINT `fk_messages_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `message_attachments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `message_id` BIGINT UNSIGNED NOT NULL,
    `drive_file_id` VARCHAR(100) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) NULL,
    PRIMARY KEY (`id`),
    KEY `idx_message_attachments_message` (`message_id`),
    CONSTRAINT `fk_message_attachments_message` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. PENGUMUMAN
-- ------------------------------------------------------------
CREATE TABLE `announcements` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(150) NOT NULL,
    `body` MEDIUMTEXT NOT NULL,
    `priority` ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
    `publish_at` DATETIME NULL,
    `expire_at` DATETIME NULL,
    `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    `created_by` BIGINT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_announcements_status_publish` (`status`,`publish_at`),
    CONSTRAINT `fk_announcements_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `announcement_targets` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `announcement_id` BIGINT UNSIGNED NOT NULL,
    `role` ENUM('super_admin','ketua_tim') NULL,
    `user_id` BIGINT UNSIGNED NULL,
    PRIMARY KEY (`id`),
    KEY `idx_ann_targets_ann` (`announcement_id`),
    CONSTRAINT `fk_ann_targets_ann` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ann_targets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `announcement_reads` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `announcement_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `read_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_ann_reads` (`announcement_id`,`user_id`),
    CONSTRAINT `fk_ann_reads_ann` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ann_reads_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `announcement_attachments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `announcement_id` BIGINT UNSIGNED NOT NULL,
    `drive_file_id` VARCHAR(100) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_ann_attach_ann` (`announcement_id`),
    CONSTRAINT `fk_ann_attach_ann` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 10. SISTEM
-- ------------------------------------------------------------
CREATE TABLE `audit_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `actor_id` BIGINT UNSIGNED NULL,
    `action` VARCHAR(50) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` BIGINT UNSIGNED NULL,
    `old_values` JSON NULL,
    `new_values` JSON NULL,
    `ip` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_audit_logs_actor` (`actor_id`),
    KEY `idx_audit_logs_entity` (`entity_type`,`entity_id`),
    KEY `idx_audit_logs_created` (`created_at`),
    CONSTRAINT `fk_audit_logs_actor` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `backups` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `type` ENUM('manual','automatic') NOT NULL,
    `scope` ENUM('db','files','full') NOT NULL,
    `file_path_local` VARCHAR(255) NULL,
    `drive_file_id` VARCHAR(100) NULL,
    `checksum` CHAR(64) NULL,
    `size` BIGINT UNSIGNED NULL,
    `encrypted` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('success','failed') NOT NULL DEFAULT 'success',
    `message` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_backups_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_settings` (
    `key` VARCHAR(100) NOT NULL,
    `value` TEXT NULL,
    `type` ENUM('string','int','bool','json') NOT NULL DEFAULT 'string',
    `group_name` VARCHAR(50) NOT NULL DEFAULT 'general',
    `is_secret` TINYINT(1) NOT NULL DEFAULT 0,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `integration_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `provider` ENUM('gdrive','wa','smtp','printer') NOT NULL,
    `operation` VARCHAR(100) NOT NULL,
    `status` ENUM('success','failed') NOT NULL,
    `request_payload` JSON NULL,
    `response_payload` JSON NULL,
    `error_message` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_integration_logs_provider` (`provider`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;