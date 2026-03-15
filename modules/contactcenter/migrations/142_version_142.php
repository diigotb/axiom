<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_142 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        $CI->load->database();

        if (!$CI->db->table_exists(db_prefix() . 'contactcenter_drawflow')) {
            $CI->db->query("CREATE TABLE `tblcontactcenter_drawflow` (
                            `draw_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,                   
                            `title` varchar(255) DEFAULT NULL,
                            `data` longtext NOT NULL,
                            `status` int(11) NULL DEFAULT 0,
                            `date` datetime DEFAULT NULL
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
        
        
        if (!$CI->db->table_exists(db_prefix() . 'contactcenter_drawflow_group')) {
            $CI->db->query("CREATE TABLE `tblcontactcenter_drawflow_group` (
                            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,    
                            `group_id` int(11) DEFAULT NULL,
                            `draw_id` int(11) DEFAULT NULL,
                            `title` varchar(255) DEFAULT NULL,                   
                            `type` varchar(255) DEFAULT NULL,                 
                            `group_inputs` int(11) DEFAULT NULL,
                            `group_output` int(11) DEFAULT NULL,
                            `gpt_caracters` int(11) DEFAULT NULL,
                            `gpt_tag_exit` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                    
                            `gpt_prompt` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                    
                            `gpt_model` varchar(255) DEFAULT NULL,                 
                            `custom_fields` varchar(50) DEFAULT NULL,                 
                            `count` int(11) DEFAULT 0,               
                            `date` datetime DEFAULT NULL
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
        
        if (!$CI->db->table_exists(db_prefix() . 'contactcenter_drawflow_group_children')) {
            $CI->db->query("CREATE TABLE `tblcontactcenter_drawflow_group_children` (
                            `child_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,                   
                            `group_id` int(11) DEFAULT NULL,
                            `draw_id` int(11) DEFAULT NULL,
                            `input_id` varchar(255) DEFAULT NULL,
                            `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, 
                            `url` text DEFAULT NULL,
                            `link` varchar(255) DEFAULT NULL,
                            `type` varchar(255) DEFAULT NULL,
                            `operador` varchar(10) DEFAULT NULL,
                            `conexao` varchar(15) DEFAULT NULL,
                            `next` int(11) DEFAULT 0,
                            `date` datetime DEFAULT NULL
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        if (!$CI->db->table_exists(db_prefix() . 'contactcenter_drawflow_static_lead')) {
            $CI->db->query("CREATE TABLE `tblcontactcenter_drawflow_static_lead` (
                            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,                   
                            `group_id` int(11) DEFAULT NULL,
                            `draw_id` int(11) DEFAULT NULL,
                            `lead_id` int(11) DEFAULT NULL,
                            `date` date DEFAULT NULL
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
        
        if (!$CI->db->field_exists('chatbot_group_id', db_prefix() . 'contactcenter_contact')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `chatbot_group_id` INT(11) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('chatbot_agenda', db_prefix() . 'contactcenter_contact')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `chatbot_agenda` text DEFAULT NULL");
        }

        if (!$CI->db->field_exists('chatbot_id', db_prefix() . 'contactcenter_device')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD COLUMN `chatbot_id` int(11) DEFAULT NULL");
        }



    }
}