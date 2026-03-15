<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_159 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        if (!$CI->db->field_exists('reply_participant', db_prefix() . 'contactcenter_message')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD  `reply_participant` varchar(255) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('reply_msg', db_prefix() . 'contactcenter_message')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD  `reply_msg` varchar(255) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('reply_id', db_prefix() . 'contactcenter_message')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD  `reply_id` varchar(255) DEFAULT NULL");
        }

        $query = $CI->db->query("SHOW INDEX FROM tblcontactcenter_contact WHERE Key_name = 'consultDate'");
        if ($query->num_rows() == 0) {
            $CI->db->query("CREATE INDEX consultDate ON tblcontactcenter_contact (date)");
        }
    }
}
