<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_180 extends App_module_migration
{
    public function up()
    {
        if (!$this->ci->db->table_exists(db_prefix() . 'contactcenter_assistant_templates')) {
            return;
        }
        $this->update_aesthetic_template_from_file();
        $this->update_franchise_template_from_file();
    }

    private function update_aesthetic_template_from_file()
    {
        $path = module_dir_path('contactcenter') . 'assets/templates/aesthetic_clinics_template.txt';
        if (!file_exists($path)) {
            return;
        }
        $this->ci->db->where('is_system', 1);
        $this->ci->db->like('name', 'Aesthetic');
        $aesthetic = $this->ci->db->get(db_prefix() . 'contactcenter_assistant_templates')->row();
        if ($aesthetic) {
            $content = file_get_contents($path);
            $this->ci->db->where('id', $aesthetic->id);
            $this->ci->db->update(db_prefix() . 'contactcenter_assistant_templates', [
                'instructions' => $content,
                'description' => 'Template for aesthetic clinics: leads from campaigns, treatments info, scheduling. Replace [NOME_CLINICA], [NOME_AGENTE], [TELEFONE_RECEPCAO], [ENDEREÇO_COMPLETO], [LINK_INSTAGRAM].',
            ]);
        }
    }

    private function update_franchise_template_from_file()
    {
        $path = module_dir_path('contactcenter') . 'assets/templates/franchise_sales_template.txt';
        if (!file_exists($path)) {
            return;
        }
        $this->ci->db->where('is_system', 1);
        $this->ci->db->like('name', 'Franchise');
        $franchise = $this->ci->db->get(db_prefix() . 'contactcenter_assistant_templates')->row();
        if ($franchise) {
            $content = file_get_contents($path);
            $this->ci->db->where('id', $franchise->id);
            $this->ci->db->update(db_prefix() . 'contactcenter_assistant_templates', [
                'instructions' => $content,
                'description' => 'Template for franchise/licensing sales: qualification flow, pitch, Google Meet scheduling. Replace [NOME_EMPRESA], [NOME_AGENTE], [VALOR_LICENCA], [NOME_VARIAVEL_AUDIO_1], [NOME_VARIAVEL_AUDIO_2], [TELEFONE_CONTATO].',
            ]);
        }
    }
}
