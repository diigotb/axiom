<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_161 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        if (!$CI->db->field_exists('instructions', db_prefix() . 'contactcenter_assistants_ai')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `instructions` LONGTEXT DEFAULT NULL");
        }

        if (!$CI->db->field_exists('model', db_prefix() . 'contactcenter_assistants_ai')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `model` varchar(100) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('functions', db_prefix() . 'contactcenter_assistants_ai')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `functions` TEXT DEFAULT NULL");
        }

        if (!$CI->db->field_exists('vector_id', db_prefix() . 'contactcenter_assistants_ai')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `vector_id` varchar(255) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('last_update', db_prefix() . 'contactcenter_assistants_ai')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `last_update` DATETIME DEFAULT NULL");
        }

        if (!$CI->db->field_exists('staffid', db_prefix() . 'contactcenter_assistants_ai')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `staffid` int(11) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('staffid_update', db_prefix() . 'contactcenter_assistants_ai')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `staffid_update` int(11) DEFAULT NULL");
        }

        if (!$CI->db->field_exists('create_date', db_prefix() . 'contactcenter_assistants_ai')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `create_date` DATETIME DEFAULT NULL");
        }

        if (!$CI->db->table_exists(db_prefix() . 'contactcenter_assistants_files')) {
            $CI->db->query("CREATE TABLE `tblcontactcenter_assistants_files` (
                        `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,                
                        `assist_id` varchar(255) DEFAULT NULL, 
                        `files_id` varchar(255) DEFAULT NULL, 
                        `vector_id` varchar(255) DEFAULT NULL, 
                        `files` varchar(255) DEFAULT NULL, 
                        `staffid` int(11) DEFAULT NULL, 
                        `date` DATETIME DEFAULT CURRENT_TIMESTAMP
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
        
        //verifica se existe a chave plan_key
        $query = $CI->db->get_where(db_prefix() . 'contactcenter_saas_planos', array('plan_key' => ''));
        if ($query->num_rows() == 0) {
            $defaultBearer = bin2hex(random_bytes(32)); // Gera um token seguro
            $CI->db->query("INSERT INTO `tblcontactcenter_saas_planos` (`plan_id`, `plan_key`) VALUES (NULL, '$defaultBearer')");
        }
    }
}
