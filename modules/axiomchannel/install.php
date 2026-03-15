<?php
defined('BASEPATH') or exit('No direct script access allowed');

function axiomchannel_run_install($CI)
{
    // -------------------------------------------------------
    // Tabela: dispositivos WhatsApp
    // -------------------------------------------------------
    if (!$CI->db->table_exists(db_prefix() . 'axch_devices')) {
        $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_devices` (
            `id`              INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name`            VARCHAR(255) NOT NULL,
            `phone_number`    VARCHAR(50) DEFAULT NULL,
            `instance_name`   VARCHAR(255) NOT NULL,
            `server_url`      VARCHAR(500) NOT NULL DEFAULT 'http://localhost:8080',
            `api_key`         VARCHAR(500) DEFAULT NULL,
            `status`          ENUM('connected','disconnected','connecting') DEFAULT 'disconnected',
            `assigned_staff`  INT(11) DEFAULT NULL,
            `ai_enabled`      TINYINT(1) DEFAULT 0,
            `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    // -------------------------------------------------------
    // Tabela: contatos/conversas
    // -------------------------------------------------------
    if (!$CI->db->table_exists(db_prefix() . 'axch_contacts')) {
        $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_contacts` (
            `id`              INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `device_id`       INT(11) NOT NULL,
            `phone_number`    VARCHAR(50) NOT NULL,
            `name`            VARCHAR(255) DEFAULT NULL,
            `avatar`          TEXT DEFAULT NULL,
            `lead_id`         INT(11) DEFAULT NULL,
            `assigned_staff`  INT(11) DEFAULT NULL,
            `status`          ENUM('open','pending','resolved','bot') DEFAULT 'open',
            `is_read`         TINYINT(1) DEFAULT 0,
            `last_message`    TEXT DEFAULT NULL,
            `last_message_at` DATETIME DEFAULT NULL,
            `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_phone`     (`phone_number`),
            INDEX `idx_device`    (`device_id`),
            INDEX `idx_staff`     (`assigned_staff`),
            INDEX `idx_last_msg`  (`last_message_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    // -------------------------------------------------------
    // Tabela: mensagens
    // -------------------------------------------------------
    if (!$CI->db->table_exists(db_prefix() . 'axch_messages')) {
        $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_messages` (
            `id`              INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `contact_id`      INT(11) NOT NULL,
            `device_id`       INT(11) NOT NULL,
            `external_id`     VARCHAR(255) DEFAULT NULL,
            `direction`       ENUM('inbound','outbound') NOT NULL,
            `type`            ENUM('text','image','audio','video','document','sticker','location','reaction') DEFAULT 'text',
            `content`         TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            `media_url`       TEXT DEFAULT NULL,
            `media_base64`    LONGTEXT DEFAULT NULL,
            `media_filename`  VARCHAR(255) DEFAULT NULL,
            `is_read`         TINYINT(1) DEFAULT 0,
            `sent_by_staff`   INT(11) DEFAULT NULL,
            `sent_by_ai`      TINYINT(1) DEFAULT 0,
            `status`          ENUM('pending','sent','delivered','read','failed') DEFAULT 'sent',
            `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_contact`   (`contact_id`),
            INDEX `idx_device`    (`device_id`),
            INDEX `idx_external`  (`external_id`),
            INDEX `idx_created`   (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    // -------------------------------------------------------
    // Tabela: transferências de atendimento
    // -------------------------------------------------------
    if (!$CI->db->table_exists(db_prefix() . 'axch_transfers')) {
        $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_transfers` (
            `id`              INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `contact_id`      INT(11) NOT NULL,
            `from_staff`      INT(11) DEFAULT NULL,
            `to_staff`        INT(11) NOT NULL,
            `note`            TEXT DEFAULT NULL,
            `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    // -------------------------------------------------------
    // Tabela: fila de mensagens para envio
    // -------------------------------------------------------
    if (!$CI->db->table_exists(db_prefix() . 'axch_queue')) {
        $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_queue` (
            `id`              INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `device_id`       INT(11) NOT NULL,
            `phone_number`    VARCHAR(50) NOT NULL,
            `message`         TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            `media_url`       TEXT DEFAULT NULL,
            `type`            VARCHAR(50) DEFAULT 'text',
            `status`          ENUM('pending','processing','sent','failed') DEFAULT 'pending',
            `attempts`        INT(11) DEFAULT 0,
            `scheduled_at`    DATETIME DEFAULT NULL,
            `sent_at`         DATETIME DEFAULT NULL,
            `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_status`    (`status`),
            INDEX `idx_scheduled` (`scheduled_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    log_activity('AxiomChannel: tabelas instaladas com sucesso');
}
