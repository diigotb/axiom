<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_151 extends App_module_migration
{
    public function up()
    {

        // carrego o model
        $CI = &get_instance();
        $CI->load->model('contactcenter/contactcenter_model');
        add_option('contactcenter_notify_whatsapp_agendamento',1);

        // Atualizo os devices nos servidor pra web socket
        $CI->contactcenter_model->update_web_socket();
        $CI->contactcenter_model->delete_server();
        function copiar_arquivos(
            string $filename_source,
            string $filename_dest
        ) {
            if (file_exists($filename_dest)) {
                unlink($filename_dest);
            }
            copy($filename_source, $filename_dest);
        }

        if (!$CI->db->field_exists('timer_ia', db_prefix() . 'contactcenter_device')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD COLUMN `timer_ia` varchar(500) DEFAULT NULL");
        }
        
        if (!$CI->db->field_exists('notifyWhatsapp', db_prefix() . 'events')) {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "events` ADD COLUMN `notifyWhatsapp` int(11) DEFAULT 0");
        }        

        //Helpers
        copiar_arquivos('modules/contactcenter/views/new/modules_helper.php', 'application/helpers/modules_helper.php');
        copiar_arquivos('modules/contactcenter/views/new/reminder_fields.php', 'application/views/admin/includes/reminder_fields.php');
        add_option('contactcenter_api_bearer_token', 'GdckVkHjOI48ScJGlETxIg7VozEAIuEwoIGCPfkaxEh31oj2y2XwfUPZER2GTTSAUFoTDz0CUC4CDhSdxVV3Lkh1JZLuCyP9IuHPlih');

    }
}