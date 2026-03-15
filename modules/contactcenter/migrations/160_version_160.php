<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_160 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        if (!$CI->db->field_exists('contract_template', db_prefix() . 'contactcenter_device')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD  `contract_template` int(11) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('contract_category', db_prefix() . 'contactcenter_device')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD  `contract_category` int(11) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('contract_msg', db_prefix() . 'contactcenter_device')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD  `contract_msg` TEXT DEFAULT NULL");
        }

        if (!$CI->db->field_exists('assigned', db_prefix() . 'contactcenter_contact')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD  `assigned` int(11) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('client_id', db_prefix() . 'contactcenter_contact')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD  `client_id` int(11) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('rel_type', db_prefix() . 'contactcenter_contact')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD  `rel_type` varchar(10) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('lead_status', db_prefix() . 'contactcenter_contact')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD  `lead_status` int(11) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('remoteJid', db_prefix() . 'contactcenter_message')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD  `remoteJid` varchar(100) DEFAULT NULL");
        }

        if (!$CI->db->table_exists(db_prefix() . 'contactcenter_message_speed')) {
            $CI->db->query("CREATE TABLE `tblcontactcenter_message_speed` (
                        `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        `title` varchar(255) DEFAULT NULL,               
                        `content` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                        `device_id` int(11) DEFAULT NULL, 
                        `staffid` int(11) DEFAULT NULL, 
                        `restrict` int(11) DEFAULT NULL, 
                        `date` DATETIME DEFAULT CURRENT_TIMESTAMP
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }


        // if (!$CI->db->table_exists(db_prefix() . 'contactcenter_meta_head')) {
        //     $CI->db->query("CREATE TABLE tblcontactcenter_meta_head (
        //                 id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        //                 title varchar(255) DEFAULT NULL,               
        //                 body TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                
        //                 device_id int(11) DEFAULT NULL, 
        //                 sourceId varchar(255) DEFAULT NULL,               
        //                 dev_session varchar(255) DEFAULT NULL,               
        //                 thumbnailUrl varchar(255) DEFAULT NULL,               
        //                 sourceUrl varchar(255) DEFAULT NULL,
        //                 amount  decimal(12,2) DEFAULT 0, 
        //                 date DATETIME DEFAULT CURRENT_TIMESTAMP
        //               ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        
        
        //     //copia as informações da tabela tblcontactcenter_meta para tblcontactcenter_meta_head agrupando por sourceId
        //     $CI->db->query("INSERT INTO tblcontactcenter_meta_head (title, body, dev_session, sourceId, thumbnailUrl, sourceUrl, date)
        //                     SELECT title, body, session, sourceId, thumbnailUrl, sourceUrl, date
        //                     FROM tblcontactcenter_meta
        //                     GROUP BY sourceId;");
        // }


        add_option('openai_speed_send', 0);
        $this->copiar_arquivos('modules/contactcenter/files/controllers/Contract.php', 'application/controllers/Contract.php');
    }

    private function copiar_arquivos(
        string $filename_source,
        string $filename_dest
    ) {
        if (file_exists($filename_dest)) {
            unlink($filename_dest);
        }
        copy($filename_source, $filename_dest);
    }
}
