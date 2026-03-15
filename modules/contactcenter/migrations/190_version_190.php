<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_190 extends App_module_migration
{
    public function up()
    {
        $tbl = db_prefix() . 'contactcenter_conversation_engine';

        // Extend date_filter_type ENUM to add 'birthday'
        $this->ci->db->query("ALTER TABLE `{$tbl}` MODIFY COLUMN `date_filter_type` ENUM('creation_date', 'last_contact', 'birthday') DEFAULT 'creation_date'");

        // Add birthday_field column (custom field ID for lead birthday)
        if (!$this->ci->db->field_exists('birthday_field', $tbl)) {
            $this->ci->db->query("ALTER TABLE `{$tbl}` ADD `birthday_field` INT(11) DEFAULT NULL COMMENT 'Custom field ID containing lead birthday date'");
        }
    }
}
