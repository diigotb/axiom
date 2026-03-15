<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_141 extends App_module_migration
{
    public function up()
    {
        // adicione coluna msg_fromMe na tabela tblcontactcenter_contact
        $CI = &get_instance();
        $CI->load->database();
        $CI->db->query("ALTER TABLE `tblcontactcenter_contact` ADD `msg_fromMe` int(11) NOT NULL DEFAULT 0");  
        $CI->db->query("ALTER TABLE `tblcontactcenter_contact`  ADD `is_locked` TINYINT(1) DEFAULT 0");       
        
        $CI->db->query("
            UPDATE tblcontactcenter_contact c
            INNER JOIN (
                SELECT m.msg_conversation_number, m.msg_fromMe
                FROM tblcontactcenter_message m
                INNER JOIN (
                    SELECT msg_conversation_number, MAX(msg_id) as max_id
                    FROM tblcontactcenter_message
                    GROUP BY msg_conversation_number
                ) lm ON m.msg_id = lm.max_id
            ) last_msg ON c.phonenumber = last_msg.msg_conversation_number
            SET c.msg_FromMe = last_msg.msg_fromMe;
        ");

        $CI->db->query("UPDATE tblcontactcenter_contact c
        INNER JOIN tblleads l ON c.phonenumber = l.phonenumber
        SET c.leadid = l.id");
        
        if (!$CI->db->table_exists(db_prefix() . 'contactcenter_leads_engine')) {
            $CI->db->query("CREATE TABLE `tblcontactcenter_leads_engine` (
                            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            `staffid` int(11) DEFAULT NULL,
                            `title` varchar(255) DEFAULT NULL,                                               
                            `status` int(11) NULL DEFAULT 0,
                            `leads_status` int(11) DEFAULT 0, 
                            `leads_status_final` int(11) DEFAULT 0,             
                            `date` datetime DEFAULT NULL,
                            `start_time` TIME NOT NULL DEFAULT '08:00:00',
                            `end_time` TIME NOT NULL DEFAULT '18:00:00', 
                            `hours_since_last_contact` int(11) DEFAULT 24             
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
        
        if (!$CI->db->table_exists(db_prefix() . 'contactcenter_leads_engine_messages')) {
            $CI->db->query("CREATE TABLE `tblcontactcenter_leads_engine_messages` (
                            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            `contactcenter_leads_engine_id` int(11) DEFAULT NULL,
                            `text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                     
                            `image` varchar(255) DEFAULT NULL,
                            `media_type` varchar(50) DEFAULT NULL,                                      
                            `ordenation` int(11) DEFAULT NULL
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
    }
}