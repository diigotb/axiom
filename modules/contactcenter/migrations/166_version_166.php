<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_166 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        if (!$CI->db->field_exists('fromMe', db_prefix() . 'contactcenter_leads_engine')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `fromMe` int(11) DEFAULT 1");
        }
        if (!$CI->db->field_exists('sincronizar', db_prefix() . 'contactcenter_contact')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD `sincronizar` int(11) DEFAULT 0");
        }
        if (!$CI->db->field_exists('remoteJid', db_prefix() . 'contactcenter_contact')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD `remoteJid` varchar(255) DEFAULT NULL");
        }
    }
}
