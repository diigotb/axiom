<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_148 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        $CI->load->database();

        if (!$CI->db->field_exists('notify_by_whatsapp', db_prefix() . 'reminders')) {
            $CI->db->query("ALTER TABLE " . db_prefix() . "reminders ADD COLUMN notify_by_whatsapp int(11) DEFAULT 0 ");
        }  
        
        if (!$CI->db->field_exists('isnotify_whatsapp', db_prefix() . 'reminders')) {
            $CI->db->query("ALTER TABLE " . db_prefix() . "reminders ADD COLUMN isnotify_whatsapp int(11) DEFAULT 0 ");
        }

        $this->copiar_arquivos('modules/contactcenter/files/helpers/modules_helper.php', 'application/helpers/modules_helper.php');
        $this->copiar_arquivos('modules/contactcenter/files/models/Misc_model.php', 'application\models\Misc_model.php');
        $this->copiar_arquivos('modules/contactcenter/views/new/reminder_fields.php', 'application\views\admin\includes\reminder_fields.php');
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
