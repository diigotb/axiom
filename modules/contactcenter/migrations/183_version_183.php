<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_183 extends App_module_migration
{
    public function up()
    {
        $table = db_prefix() . 'contactcenter_script_updates';

        if (!$this->ci->db->table_exists($table)) {
            $this->ci->db->query("CREATE TABLE `{$table}` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `assistant_id` INT(11) NOT NULL,
                `current_script` LONGTEXT NULL,
                `proposed_script` LONGTEXT NULL,
                `summary` TEXT NULL,
                `contacts_analyzed` INT(11) DEFAULT 0,
                `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
                `reviewed_by` INT(11) NULL,
                `reviewed_at` DATETIME NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_assistant_status` (`assistant_id`, `status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $this->ci->db->char_set . ";");
        }
    }
}
