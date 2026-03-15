<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_187 extends App_module_migration
{
    public function up()
    {
        $charset = $this->ci->db->char_set;

        $tbl_config = db_prefix() . 'contactcenter_auto_followup';
        if (!$this->ci->db->table_exists($tbl_config)) {
            $this->ci->db->query("CREATE TABLE `{$tbl_config}` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(255) DEFAULT NULL,
                `status` TINYINT(1) DEFAULT 0,
                `objective` VARCHAR(50) DEFAULT 'lead_warmer',
                `custom_objective` TEXT NULL,
                `tags` VARCHAR(255) NULL,
                `time_amount` INT(11) NOT NULL DEFAULT 24,
                `time_unit` VARCHAR(20) NOT NULL DEFAULT 'hours',
                `hours_equivalent` DECIMAL(10,2) DEFAULT 24.00,
                `start_time` TIME DEFAULT '08:00:00',
                `end_time` TIME DEFAULT '18:00:00',
                `device_id` INT(11) NULL,
                `staffid` INT(11) NULL,
                `daily_limit` INT(11) DEFAULT 50,
                `generation_window_hours` INT(11) DEFAULT 2,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET={$charset};");
        }

        $tbl_queue = db_prefix() . 'contactcenter_auto_followup_queue';
        if (!$this->ci->db->table_exists($tbl_queue)) {
            $this->ci->db->query("CREATE TABLE `{$tbl_queue}` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `followup_id` INT(11) NOT NULL,
                `lead_id` INT(11) NOT NULL,
                `phone` VARCHAR(50) DEFAULT NULL,
                `lead_name` VARCHAR(255) DEFAULT NULL,
                `scheduled_at` DATETIME NOT NULL,
                `message_text` TEXT NULL,
                `context_summary` TEXT NULL,
                `status` ENUM('pending','sending','sent','failed','cancelled') DEFAULT 'pending',
                `sent_at` DATETIME NULL,
                `error_message` TEXT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_followup_status` (`followup_id`, `status`),
                KEY `idx_scheduled` (`status`, `scheduled_at`),
                KEY `idx_lead` (`lead_id`, `followup_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET={$charset};");
        }
    }
}
