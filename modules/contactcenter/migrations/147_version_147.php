<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_147 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        $CI->load->database();

        if (!$CI->db->field_exists('value_key', db_prefix() . 'contactcenter_server')) {
            $CI->db->query("ALTER TABLE " . db_prefix() . "contactcenter_server ADD COLUMN value_key varchar(500) DEFAULT NULL");

            // insert value in key
            $CI->db->query("UPDATE `" . db_prefix() . "contactcenter_server` SET value_key = '12d7cf0603036564a574eedcf904d84b'");
        }
    }
}
