<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

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
        INDEX `idx_phone`    (`phone_number`),
        INDEX `idx_device`   (`device_id`),
        INDEX `idx_staff`    (`assigned_staff`),
        INDEX `idx_last_msg` (`last_message_at`)
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
        `type`            ENUM('text','image','audio','video','document','sticker','location') DEFAULT 'text',
        `content`         TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `media_url`       TEXT DEFAULT NULL,
        `media_filename`  VARCHAR(255) DEFAULT NULL,
        `is_read`         TINYINT(1) DEFAULT 0,
        `sent_by_staff`   INT(11) DEFAULT NULL,
        `sent_by_ai`      TINYINT(1) DEFAULT 0,
        `status`          ENUM('pending','sent','delivered','read','failed') DEFAULT 'sent',
        `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_contact`  (`contact_id`),
        INDEX `idx_device`   (`device_id`),
        INDEX `idx_external` (`external_id`),
        INDEX `idx_created`  (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// -------------------------------------------------------
// Tabela: transferências
// -------------------------------------------------------
if (!$CI->db->table_exists(db_prefix() . 'axch_transfers')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_transfers` (
        `id`          INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `contact_id`  INT(11) NOT NULL,
        `from_staff`  INT(11) DEFAULT NULL,
        `to_staff`    INT(11) NOT NULL,
        `note`        TEXT DEFAULT NULL,
        `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// -------------------------------------------------------
// Tabela: fila de envio
// -------------------------------------------------------
if (!$CI->db->table_exists(db_prefix() . 'axch_queue')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_queue` (
        `id`           INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `device_id`    INT(11) NOT NULL,
        `phone_number` VARCHAR(50) NOT NULL,
        `message`      TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `media_url`    TEXT DEFAULT NULL,
        `type`         VARCHAR(50) DEFAULT 'text',
        `status`       ENUM('pending','processing','sent','failed') DEFAULT 'pending',
        `attempts`     INT(11) DEFAULT 0,
        `scheduled_at` DATETIME DEFAULT NULL,
        `sent_at`      DATETIME DEFAULT NULL,
        `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_status`    (`status`),
        INDEX `idx_scheduled` (`scheduled_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

log_activity('AxiomChannel: instalado com sucesso v1.0.0');

// -------------------------------------------------------
// Tabela: pipelines (cada negócio tem seu pipeline)
// -------------------------------------------------------
if (!$CI->db->table_exists(db_prefix() . 'axch_pipelines')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_pipelines` (
        `id`          INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `name`        VARCHAR(255) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `device_id`   INT(11) DEFAULT NULL,
        `is_default`  TINYINT(1) DEFAULT 0,
        `created_by`  INT(11) DEFAULT NULL,
        `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at`  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// -------------------------------------------------------
// Tabela: estágios do pipeline
// -------------------------------------------------------
if (!$CI->db->table_exists(db_prefix() . 'axch_pipeline_stages')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_pipeline_stages` (
        `id`            INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `pipeline_id`   INT(11) NOT NULL,
        `name`          VARCHAR(255) NOT NULL,
        `color`         VARCHAR(20) DEFAULT '#8A9BAE',
        `position`      INT(11) DEFAULT 0,
        `ai_action`     TEXT DEFAULT NULL,
        `ai_keywords`   TEXT DEFAULT NULL,
        `auto_move`     TINYINT(1) DEFAULT 0,
        `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_pipeline` (`pipeline_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// -------------------------------------------------------
// Tabela: leads do CRM (ligados às conversas)
// -------------------------------------------------------
if (!$CI->db->table_exists(db_prefix() . 'axch_crm_leads')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_crm_leads` (
        `id`            INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `contact_id`    INT(11) NOT NULL,
        `pipeline_id`   INT(11) NOT NULL,
        `stage_id`      INT(11) NOT NULL,
        `name`          VARCHAR(255) DEFAULT NULL,
        `phone`         VARCHAR(50) DEFAULT NULL,
        `email`         VARCHAR(255) DEFAULT NULL,
        `notes`         TEXT DEFAULT NULL,
        `value`         DECIMAL(10,2) DEFAULT 0,
        `assigned_staff` INT(11) DEFAULT NULL,
        `perfex_lead_id` INT(11) DEFAULT NULL,
        `position`      INT(11) DEFAULT 0,
        `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at`    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_contact`  (`contact_id`),
        INDEX `idx_pipeline` (`pipeline_id`),
        INDEX `idx_stage`    (`stage_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// -------------------------------------------------------
// Tabela: histórico de movimentação no pipeline
// -------------------------------------------------------
if (!$CI->db->table_exists(db_prefix() . 'axch_pipeline_history')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_pipeline_history` (
        `id`            INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `lead_id`       INT(11) NOT NULL,
        `from_stage`    INT(11) DEFAULT NULL,
        `to_stage`      INT(11) NOT NULL,
        `moved_by`      ENUM('human','ai') DEFAULT 'human',
        `staff_id`      INT(11) DEFAULT NULL,
        `note`          TEXT DEFAULT NULL,
        `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_lead` (`lead_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// -------------------------------------------------------
// Tabela: assistentes de IA por dispositivo
// -------------------------------------------------------
if (!$CI->db->table_exists(db_prefix() . 'axch_assistants')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_assistants` (
        `id`                   INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `device_id`            INT(11) DEFAULT NULL,
        `name`                 VARCHAR(255) NOT NULL DEFAULT 'Assistente IA',
        `is_active`            TINYINT(1) NOT NULL DEFAULT 0,
        `business_name`        VARCHAR(255) DEFAULT NULL,
        `business_type`        VARCHAR(100) DEFAULT NULL,
        `tone_of_voice`        ENUM('profissional','descontraido','persuasivo','tecnico') DEFAULT 'profissional',
        `greeting_message`     TEXT DEFAULT NULL,
        `transfer_keywords`    TEXT DEFAULT NULL,
        `working_hours_start`  TIME DEFAULT '08:00:00',
        `working_hours_end`    TIME DEFAULT '18:00:00',
        `created_at`           DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at`           DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_device` (`device_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// -------------------------------------------------------
// Tabela: base de conhecimento do negócio
// -------------------------------------------------------
if (!$CI->db->table_exists(db_prefix() . 'axch_knowledge_base')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_knowledge_base` (
        `id`           INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `assistant_id` INT(11) NOT NULL,
        `category`     ENUM('product','faq','objection','info','sales_tip') NOT NULL DEFAULT 'info',
        `title`        VARCHAR(255) NOT NULL,
        `content`      TEXT NOT NULL,
        `is_active`    TINYINT(1) NOT NULL DEFAULT 1,
        `position`     INT(11) NOT NULL DEFAULT 0,
        `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at`   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_assistant` (`assistant_id`),
        INDEX `idx_category`  (`category`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// -------------------------------------------------------
// Tabela: etapas do fluxo de qualificação
// -------------------------------------------------------
if (!$CI->db->table_exists(db_prefix() . 'axch_assistant_stages')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_assistant_stages` (
        `id`                INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `assistant_id`      INT(11) NOT NULL,
        `pipeline_stage_id` INT(11) DEFAULT NULL,
        `stage_name`        VARCHAR(255) NOT NULL,
        `question`          TEXT DEFAULT NULL,
        `action`            ENUM('ask','inform','qualify','close','transfer') NOT NULL DEFAULT 'ask',
        `save_field`        VARCHAR(100) DEFAULT NULL,
        `position`          INT(11) NOT NULL DEFAULT 0,
        `created_at`        DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_assistant` (`assistant_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// -------------------------------------------------------
// Tabela: mídia de conhecimento (imagens, vídeos, docs)
// -------------------------------------------------------
if (!$CI->db->table_exists(db_prefix() . 'axch_knowledge_media')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_knowledge_media` (
        `id`            INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `assistant_id`  INT(11) NOT NULL,
        `file_type`     ENUM('image','video','audio','pdf','document') NOT NULL DEFAULT 'document',
        `original_name` VARCHAR(255) NOT NULL,
        `file_path`     VARCHAR(500) NOT NULL,
        `file_size`     FLOAT DEFAULT NULL,
        `mime_type`     VARCHAR(100) DEFAULT NULL,
        `media_label`   VARCHAR(255) DEFAULT NULL,
        `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_assistant_media` (`assistant_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}


// -------------------------------------------------------
// Tabela: preferências do staff (dashboard customization)
// -------------------------------------------------------
if (!$CI->db->table_exists(db_prefix() . 'axch_staff_preferences')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "axch_staff_preferences` (
        `id`          INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `staff_id`    INT(11) NOT NULL,
        `preferences` TEXT DEFAULT NULL,
        `updated_at`  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `uq_staff` (`staff_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}
