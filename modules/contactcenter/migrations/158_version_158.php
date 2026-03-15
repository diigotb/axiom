<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_158 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        if (!$CI->db->field_exists('device_id', db_prefix() . 'contactcenter_leads_engine')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD  `device_id` int(11) DEFAULT NULL");
        }
    }
}
