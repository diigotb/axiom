<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_146 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        $CI->load->database();

        if (!$CI->db->field_exists('source', db_prefix() . 'contactcenter_meta')) {
            $CI->db->query("ALTER TABLE " . db_prefix() . "contactcenter_meta ADD COLUMN source varchar(50) DEFAULT NULL ");
        }

        if (!$CI->db->field_exists('entryPointConversionApp', db_prefix() . 'contactcenter_meta')) {
            $CI->db->query("ALTER TABLE " . db_prefix() . "contactcenter_meta ADD COLUMN entryPointConversionApp varchar(50) DEFAULT NULL ");
        }

        if (!$CI->db->field_exists('type', db_prefix() . 'contactcenter_meta')) {
            $CI->db->query("ALTER TABLE " . db_prefix() . "contactcenter_meta ADD COLUMN type int(11) DEFAULT 0 ");
        }

        if (!$CI->db->table_exists(db_prefix() . 'contactcenter_links_custom')) {
            $CI->db->query("CREATE TABLE `tblcontactcenter_links_custom` (
                            `link_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            `phonenumber` varchar(255) DEFAULT NULL,               
                            `msg` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                                  
                            `link` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                                  
                            `source` varchar(255) DEFAULT NULL,
                            `hash` varchar(255) DEFAULT NULL,
                            `count` int(11) DEFAULT 0, 
                            `date` datetime DEFAULT NULL
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
    }
}
