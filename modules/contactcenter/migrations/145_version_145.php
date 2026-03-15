<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_145 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        $CI->load->database();
        if (!$CI->db->table_exists(db_prefix() . 'contactcenter_meta')) {
            $CI->db->query("CREATE TABLE `tblcontactcenter_meta` (
                            `meta_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,  
                            `lead_id` int(11) DEFAULT NULL,  
                            `session` varchar(255) DEFAULT NULL,               
                            `conversionSource` varchar(255) DEFAULT NULL, 
                            `title` varchar(255) DEFAULT NULL,
                            `body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                            `mediaType` varchar(255) DEFAULT NULL,
                            `thumbnailUrl` TEXT DEFAULT NULL,
                            `sourceId` varchar(255) DEFAULT NULL,
                            `sourceUrl` varchar(255) DEFAULT NULL,                   
                            `date` datetime DEFAULT NULL
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
    }
}
