<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_144 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        $CI->load->database();

        if (!$CI->db->field_exists('tags', db_prefix() . 'contactcenter_conversation_engine')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `tags` varchar(50) DEFAULT NULL");
        }
    }
}
