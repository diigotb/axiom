<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_143 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        $CI->load->database();

        if (!$CI->db->table_exists(db_prefix() . 'contactcenter_server')) {
            $CI->db->query("CREATE TABLE `tblcontactcenter_server` (
                            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,                   
                            `url` varchar(255) DEFAULT NULL,
                            `version` varchar(255) DEFAULT NULL,
                            `api_type` varchar(255) DEFAULT NULL,
                            `name` varchar(255) DEFAULT NULL                          
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // insiro a tabela de servidores
            $CI->db->query("INSERT INTO `tblcontactcenter_server` (`url`, `version`, `name`, `api_type`) VALUES
        ('http://localhost:8080/', '2', 'AXIOM Evolution - Local', 'axiom_evolution');");

            $CI->db->query("INSERT INTO `tblcontactcenter_server` (`url`, `version`, `name`, `api_type`) VALUES
                    ('https://api.hbfbsoft.online/', '2', 'HBFB Servidor', 'axiom_evolution');");

$CI->db->query("INSERT INTO `tblcontactcenter_server` (`url`, `version`, `name`, `api_type`) VALUES



            if (!$CI->db->field_exists('server_id', db_prefix() . 'contactcenter_device')) {                

                $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD COLUMN `server_id` INT(11) DEFAULT NULL");

                // recupero primeiro id do servidor e atualizo os devices
                $server = $CI->db->query("SELECT * FROM `tblcontactcenter_server` WHERE `api_type` = 'axiom_evolution' ORDER BY `id` ASC LIMIT 1")->row();

                $CI->db->query("UPDATE `" . db_prefix() . "contactcenter_device` SET `server_id` = " . $server->id);
            }
        }
    }
}
