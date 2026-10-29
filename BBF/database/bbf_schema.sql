-- ============================================================
-- BBF — Brigade Brotherhood Fellowship
-- Database Schema
-- Engine: MySQL 5.7+ / MariaDB 10.3+
-- Charset: utf8mb4
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `bbf_db`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `bbf_db`;

-- ─── MEMBERS ─────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `members` (
  `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `member_id`         VARCHAR(20)     NOT NULL COMMENT '',
  `first_name`        VARCHAR(80)     NOT NULL,
  `last_name`         VARCHAR(80)     NOT NULL,
  `email`             VARCHAR(180)    NOT NULL,
  `phone`             VARCHAR(30)     DEFAULT NULL,
  `password_hash`     VARCHAR(255)    NOT NULL,
  `dob`               DATE            DEFAULT NULL,
  `gender`            ENUM('male','female','prefer-not') DEFAULT NULL,
  `state`             VARCHAR(60)     DEFAULT NULL,
  `lga`               VARCHAR(60)     DEFAULT NULL,
  `company`           VARCHAR(150)    DEFAULT NULL COMMENT 'Boys Brigade company',
  `council`           VARCHAR(100)    DEFAULT NULL COMMENT 'BB Council / Diocese',
  `bb_status`         ENUM('active','former') DEFAULT 'active',
  `bsc`               ENUM('yes','in-progress','no') DEFAULT 'in-progress' COMMENT 'Brigade School Certificate',
  `chapter`           VARCHAR(80)     DEFAULT NULL,
  `referral`          VARCHAR(60)     DEFAULT NULL,
  `membership_status` ENUM('Pending','Active','Suspended') NOT NULL DEFAULT 'Pending',
  `profile_photo`     VARCHAR(255)    DEFAULT NULL,
  `reset_token`       VARCHAR(100)    DEFAULT NULL,
  `reset_expires`     DATETIME        DEFAULT NULL,
  `email_verified`    TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_member_id`  (`member_id`),
  UNIQUE KEY `uq_email`      (`email`),
  KEY `idx_status`           (`membership_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── ADMIN USERS ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(100)  NOT NULL,
  `email`         VARCHAR(180)  NOT NULL,
  `password_hash` VARCHAR(255)  NOT NULL,
  `role`          VARCHAR(60)   NOT NULL DEFAULT 'Super Admin',
  `last_login`    DATETIME      DEFAULT NULL,
  `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admin_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default super admin (password: Admin@BBF2026)
INSERT INTO `admin_users` (`name`, `email`, `password_hash`, `role`)
VALUES (
  'BBF Super Admin',
  'admin@bbfellowship.org',
  '$2y$12$OqAr2sW8GJJhHo5NmJs.i.h5PEnCSceu6otCQ3KQnhmCrmmPn7K5i',
  'Super Admin'
) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ─── LOGIN LOGS ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `login_logs` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id`  INT UNSIGNED DEFAULT NULL COMMENT 'NULL = unknown/failed',
  `email`      VARCHAR(180) NOT NULL,
  `ip_address` VARCHAR(45)  DEFAULT NULL,
  `user_agent` TEXT         DEFAULT NULL,
  `status`     ENUM('success','failed') NOT NULL DEFAULT 'success',
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_member`    (`member_id`),
  KEY `idx_created`   (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── EVENTS ──────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `events` (
  `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `title`            VARCHAR(200)  NOT NULL,
  `category`         VARCHAR(60)   NOT NULL DEFAULT 'Fellowship',
  `description`      TEXT          DEFAULT NULL,
  `event_date`       DATE          NOT NULL,
  `end_date`         DATE          DEFAULT NULL,
  `location`         VARCHAR(200)  DEFAULT 'To Be Confirmed',
  `status`           ENUM('upcoming','past','cancelled') NOT NULL DEFAULT 'upcoming',
  `featured`         TINYINT(1)    NOT NULL DEFAULT 0,
  `registration_link` VARCHAR(255) DEFAULT NULL,
  `capacity`         INT UNSIGNED  DEFAULT NULL,
  `image`            VARCHAR(255)  DEFAULT NULL,
  `created_by`       INT UNSIGNED  DEFAULT NULL COMMENT 'admin_users.id',
  `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status`   (`status`),
  KEY `idx_date`     (`event_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── EVENT REGISTRATIONS ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `event_registrations` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id`   INT UNSIGNED NOT NULL,
  `member_id`  INT UNSIGNED NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_event_member` (`event_id`, `member_id`),
  KEY `idx_event`  (`event_id`),
  KEY `idx_member` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── NEWS / ARTICLES ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `news` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(255)  NOT NULL,
  `category`   VARCHAR(60)   NOT NULL DEFAULT 'Announcement',
  `author`     VARCHAR(100)  NOT NULL DEFAULT 'BBF Secretariat',
  `summary`    TEXT          DEFAULT NULL,
  `content`    LONGTEXT      DEFAULT NULL,
  `image`      VARCHAR(255)  DEFAULT NULL,
  `status`     ENUM('published','draft') NOT NULL DEFAULT 'draft',
  `featured`   TINYINT(1)    NOT NULL DEFAULT 0,
  `views`      INT UNSIGNED  NOT NULL DEFAULT 0,
  `published_at` DATETIME    DEFAULT NULL,
  `created_by` INT UNSIGNED  DEFAULT NULL,
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status`  (`status`),
  KEY `idx_featured`(`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── GALLERY ─────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `gallery` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(200)  NOT NULL,
  `category`    VARCHAR(60)   NOT NULL DEFAULT 'Fellowship',
  `image`       VARCHAR(255)  NOT NULL COMMENT 'stored file path',
  `year`        YEAR          DEFAULT NULL,
  `status`      ENUM('pending','published','rejected') NOT NULL DEFAULT 'pending',
  `uploaded_by` INT UNSIGNED  DEFAULT NULL COMMENT 'members.id or admin_users.id',
  `uploader_name` VARCHAR(100) DEFAULT NULL,
  `approved_by` INT UNSIGNED  DEFAULT NULL,
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status`   (`status`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── CONTACT MESSAGES ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120)  NOT NULL,
  `email`      VARCHAR(180)  NOT NULL,
  `phone`      VARCHAR(30)   DEFAULT NULL,
  `subject`    VARCHAR(200)  DEFAULT NULL,
  `message`    TEXT          NOT NULL,
  `read_at`    DATETIME      DEFAULT NULL,
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── NEWSLETTER SUBSCRIBERS ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `email`      VARCHAR(180)  NOT NULL,
  `status`     ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── MEMBER ID SEQUENCE (for BBF-XXXX generation) ────────────────────────────
CREATE TABLE IF NOT EXISTS `member_id_seq` (
  `last_id` INT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `member_id_seq` (`last_id`) VALUES (0);
