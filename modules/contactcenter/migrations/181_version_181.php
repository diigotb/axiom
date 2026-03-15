<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_181 extends App_module_migration
{
    public function up()
    {
        if (!$this->ci->db->table_exists(db_prefix() . 'contactcenter_assistant_templates')) {
            return;
        }

        $templates = [
            [
                'name' => 'Appointment Scheduling',
                'file' => 'appointment_template.txt',
                'icon' => 'fa-calendar-check-o',
                'functions' => ['get_lead_info', 'get_horario_agenda'],
                'description' => 'Schedule appointments. Checks availability and confirms slots.'
            ],
            [
                'name' => 'Lead Qualification',
                'file' => 'qualification_template.txt',
                'icon' => 'fa-filter',
                'functions' => ['get_lead_info', 'update_leads'],
                'description' => 'Qualify leads by asking questions and saving data to fields.'
            ],
            [
                'name' => 'Content Distribution',
                'file' => 'media_template.txt',
                'icon' => 'fa-paper-plane-o',
                'functions' => ['get_lead_info', 'send_media'],
                'description' => 'Send specific media (PDFs, Videos, Images) based on user interest.'
            ],
            [
                'name' => 'Human Handoff',
                'file' => 'handoff_template.txt',
                'icon' => 'fa-headphones',
                'functions' => ['get_lead_info', 'get_lead_context', 'manage_conversation'],
                'description' => 'Handle initial triage and transfer complex issues to human agents.'
            ],
            [
                'name' => 'Contract Creator',
                'file' => 'contract_template.txt',
                'icon' => 'fa-file-text-o',
                'functions' => ['get_lead_info', 'create_contract'],
                'description' => 'Generate and send contract links for the lead to sign.'
            ],
            [
                'name' => 'Price Inquiry',
                'file' => 'prices_template.txt',
                'icon' => 'fa-tags',
                'functions' => ['get_lead_info', 'get_tabela_precos'],
                'description' => 'Consult the price table and inform users about product costs.'
            ],
            [
                'name' => 'Financial Helper',
                'file' => 'financial_template.txt',
                'icon' => 'fa-money',
                'functions' => ['get_lead_info', 'get_faturas_axiom'],
                'description' => 'Check for open invoices and provide payment methods.'
            ],
            [
                'name' => 'Support Ticket',
                'file' => 'ticket_template.txt',
                'icon' => 'fa-life-ring',
                'functions' => ['get_lead_info', 'open_ticket'],
                'description' => 'Open support tickets for users directly from the chat.'
            ],
            [
                'name' => 'Group Onboarding',
                'file' => 'group_template.txt',
                'icon' => 'fa-users',
                'functions' => ['get_lead_info', 'create_group_chat'],
                'description' => 'Create WhatsApp groups with the lead and staff members.'
            ],
            [
                'name' => 'Context Aware Assistant',
                'file' => 'context_template.txt',
                'icon' => 'fa-history',
                'functions' => ['get_lead_info', 'get_lead_context'],
                'description' => 'Personalized experience using lead history and context.'
            ]
        ];

        $base_path = module_dir_path('contactcenter') . 'assets/templates/';

        foreach ($templates as $tpl) {
            // Check if template exists
            $this->ci->db->where('name', $tpl['name']);
            $this->ci->db->where('is_system', 1);
            $exists = $this->ci->db->get(db_prefix() . 'contactcenter_assistant_templates')->row();

            $content = '';
            if (file_exists($base_path . $tpl['file'])) {
                $content = file_get_contents($base_path . $tpl['file']);
            } else {
                $content = "Template file not found: " . $tpl['file'];
            }

            $data = [
                'name' => $tpl['name'],
                'description' => $tpl['description'],
                'icon' => $tpl['icon'],
                'instructions' => $content,
                'model' => 'gpt-4o-mini',
                'functions' => json_encode($tpl['functions']),
                'is_system' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($exists) {
                $this->ci->db->where('id', $exists->id);
                $this->ci->db->update(db_prefix() . 'contactcenter_assistant_templates', $data);
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->ci->db->insert(db_prefix() . 'contactcenter_assistant_templates', $data);
            }
        }
    }
}
