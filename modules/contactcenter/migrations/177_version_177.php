<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_177 extends App_module_migration
{
    public function up()
    {
        if (!$this->db->field_exists('public_form_token', db_prefix() . 'contactcenter_assistants_ai')) {
            $this->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `public_form_token` VARCHAR(64) NULL DEFAULT NULL UNIQUE");
        }

        if (!$this->db->table_exists(db_prefix() . 'contactcenter_assistant_onboarding')) {
            $this->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_assistant_onboarding` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `assistant_id` int(11) NOT NULL,
                `form_data` LONGTEXT DEFAULT NULL,
                `submitted_at` DATETIME DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                KEY `assistant_id` (`assistant_id`),
                KEY `submitted_at` (`submitted_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }
    }
}
