<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_188 extends App_module_migration
{
    public function up()
    {
        $table = db_prefix() . 'contactcenter_auto_followup';
        if ($this->ci->db->table_exists($table) && !$this->ci->db->field_exists('lead_statuses', $table)) {
            $this->ci->db->query("ALTER TABLE `{$table}` ADD COLUMN `lead_statuses` VARCHAR(255) NULL AFTER `tags`");
        }
    }
}
