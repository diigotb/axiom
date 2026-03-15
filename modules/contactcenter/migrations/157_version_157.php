<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_157 extends App_module_migration
{
    public function up()
    {

        $CI = &get_instance();
        if (!$CI->db->field_exists('name', db_prefix() . 'contactcenter_contact')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `name` varchar(255) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('thumb', db_prefix() . 'contactcenter_contact')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `thumb` varchar(500) DEFAULT NULL");
        }
        if (!$CI->db->field_exists('isGroup', db_prefix() . 'contactcenter_contact')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `isGroup` BOOLEAN DEFAULT 0");
        }

        if (!$CI->db->field_exists('status_sinc', db_prefix() . 'contactcenter_device')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD COLUMN `status_sinc` BOOLEAN DEFAULT 0");
        }

        if (!$CI->db->field_exists('currentPage', db_prefix() . 'contactcenter_device')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD COLUMN `currentPage` int(11) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('totalPages', db_prefix() . 'contactcenter_device')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD COLUMN `totalPages` int(11) DEFAULT NULL");
        }

        $query = $CI->db->query("SHOW INDEX FROM tblcontactcenter_message WHERE Key_name = 'consultDate'");
        if ($query->num_rows() == 0) {
            $CI->db->query("CREATE INDEX consultDate ON tblcontactcenter_message (msg_date)");
        }

        $query = $CI->db->query("SHOW INDEX FROM tblcontactcenter_contact WHERE Key_name = 'consultDate'");
        if ($query->num_rows() == 0) {
            $CI->db->query("CREATE INDEX consultDate ON tblcontactcenter_contact (msg_date)");
        }

        if (!$CI->db->field_exists('active', db_prefix() . 'contactcenter_contact')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `active` BOOLEAN DEFAULT 1");
        }
      
        if (!$CI->db->field_exists('is_processing', db_prefix() . 'contactcenter_device')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD COLUMN `is_processing` int(11) DEFAULT 0");
        }

        add_option("contac_settings_sincronizacao_whatsapp_leads", 0);
        add_option("contac_settings_sincronizacao_whatsapp_active", 0);
    }
}
