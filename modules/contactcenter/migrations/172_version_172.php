<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_172 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        // Add chat_marked_read field to contactcenter_contact table
        if (!$CI->db->field_exists('chat_marked_read', db_prefix() . 'contactcenter_contact')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `chat_marked_read` TINYINT(1) DEFAULT 0 AFTER `isread`");
        }
    }
}
