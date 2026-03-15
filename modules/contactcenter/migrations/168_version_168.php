<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_168 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        if (!$CI->db->field_exists('api_local_status', db_prefix() . 'contactcenter_device')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD `api_local_status` varchar(50) DEFAULT 'close'");
        }

        if (!$CI->db->field_exists('api_web_status', db_prefix() . 'contactcenter_device')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD `api_web_status` varchar(50) DEFAULT 'close'");
        }
    }
}
