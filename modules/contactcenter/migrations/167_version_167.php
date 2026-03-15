<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_167 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();

        if (!$CI->db->field_exists('sent_source', db_prefix() . 'contactcenter_message')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD `sent_source` varchar(50) DEFAULT 'crm'");
        }

        if (!$CI->db->field_exists('api_local', db_prefix() . 'contactcenter_device')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD `api_local` int(11) DEFAULT 0");
        }
        if (!$CI->db->field_exists('last_status', db_prefix() . 'contactcenter_device')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD `last_status` DATETIME DEFAULT NULL");
        }
        if (!$CI->db->field_exists('is_whatsapp', db_prefix() . 'leads')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "leads` ADD `is_whatsapp` varchar(50) DEFAULT 'pending'");
        }
        if (!$CI->db->field_exists('msg_reaction', db_prefix() . 'contactcenter_message')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD `msg_reaction` TEXT DEFAULT NULL");
        }
    
    }
}
