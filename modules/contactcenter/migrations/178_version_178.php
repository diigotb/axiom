<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_178 extends App_module_migration
{
    public function up()
    {
        if (!$this->ci->db->table_exists(db_prefix() . 'contactcenter_assistant_templates')) {
            $this->ci->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_assistant_templates` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` varchar(255) NOT NULL,
                `description` text DEFAULT NULL,
                `icon` varchar(100) DEFAULT 'fa-robot',
                `image_path` varchar(500) DEFAULT NULL,
                `instructions` LONGTEXT NOT NULL,
                `model` varchar(100) DEFAULT 'gpt-4o-mini',
                `functions` TEXT DEFAULT NULL,
                `visual_data` LONGTEXT DEFAULT NULL,
                `is_system` tinyint(1) DEFAULT 0,
                `staffid` int(11) DEFAULT NULL,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                KEY `is_system` (`is_system`),
                KEY `staffid` (`staffid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        $this->seed_system_templates();
    }

    private function seed_system_templates()
    {
        $aesthetic = $this->get_aesthetic_clinics_template();
        $franchise = $this->get_franchise_sales_template();

        $this->ci->db->where('is_system', 1);
        $exists = $this->ci->db->get(db_prefix() . 'contactcenter_assistant_templates')->num_rows();
        if ($exists > 0) {
            return;
        }

        $this->ci->db->insert(db_prefix() . 'contactcenter_assistant_templates', [
            'name' => 'Aesthetic Clinics',
            'description' => 'Template for aesthetic clinics: leads from campaigns, treatments info, scheduling evaluations, FAQ.',
            'icon' => 'fa-spa',
            'image_path' => null,
            'instructions' => $aesthetic,
            'model' => 'gpt-4o-mini',
            'functions' => json_encode(['get_lead_info', 'get_lead_context', 'update_leads', 'get_horario_agenda', 'send_media']),
            'visual_data' => null,
            'is_system' => 1,
        ]);
        $this->ci->db->insert(db_prefix() . 'contactcenter_assistant_templates', [
            'name' => 'Franchise Sales',
            'description' => 'Template for franchise/licensing sales: qualification, pitch, Google Meet scheduling, update leads.',
            'icon' => 'fa-briefcase',
            'image_path' => null,
            'instructions' => $franchise,
            'model' => 'gpt-4o-mini',
            'functions' => json_encode(['get_lead_info', 'get_lead_context', 'update_leads', 'manage_conversation', 'get_horario_agenda', 'create_group_chat']),
            'visual_data' => null,
            'is_system' => 1,
        ]);
    }

    private function get_aesthetic_clinics_template()
    {
        $path = module_dir_path('contactcenter') . 'assets/templates/aesthetic_clinics_template.txt';
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return "Aesthetic Clinics template - Configure your clinic name, address, phone and treatments.";
    }

    private function get_franchise_sales_template()
    {
        $path = module_dir_path('contactcenter') . 'assets/templates/franchise_sales_template.txt';
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return "Franchise Sales template - Configure your company name and qualification flow.";
    }
}
