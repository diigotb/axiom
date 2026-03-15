<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_186 extends App_module_migration
{
    public function up()
    {
        $table = db_prefix() . 'contactcenter_confirmation_tokens';

        if (!$this->ci->db->table_exists($table)) {
            $this->ci->db->query("CREATE TABLE `{$table}` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `token` VARCHAR(64) NOT NULL,
                `event_id` INT(11) NOT NULL,
                `phone` VARCHAR(50) DEFAULT NULL,
                `status` ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `acted_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_token` (`token`),
                KEY `idx_event` (`event_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $this->ci->db->char_set . ";");
        }
    }
}
