<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_179 extends App_module_migration
{
    public function up()
    {
        if (!$this->ci->db->table_exists(db_prefix() . 'contactcenter_assistant_templates')) {
            return;
        }
        $this->update_system_templates_from_files();
    }

    private function update_system_templates_from_files()
    {
        $base = module_dir_path('contactcenter') . 'assets/templates/';
        $aesthetic_path = $base . 'aesthetic_clinics_template.txt';
        $franchise_path = $base . 'franchise_sales_template.txt';

        // Update Aesthetic Clinics
        $this->ci->db->where('is_system', 1);
        $this->ci->db->like('name', 'Aesthetic');
        $aesthetic = $this->ci->db->get(db_prefix() . 'contactcenter_assistant_templates')->row();
        if ($aesthetic && file_exists($aesthetic_path)) {
            $content = file_get_contents($aesthetic_path);
            $this->ci->db->where('id', $aesthetic->id);
            $this->ci->db->update(db_prefix() . 'contactcenter_assistant_templates', [
                'instructions' => $content,
                'description' => 'Template for aesthetic clinics: leads from campaigns, treatments info, scheduling evaluations. Replace [NOME_CLINICA], [TELEFONE_RECEPCAO], [ENDEREÇO_COMPLETO].',
            ]);
        }

        // Update Franchise Sales
        $this->ci->db->where('is_system', 1);
        $this->ci->db->like('name', 'Franchise');
        $franchise = $this->ci->db->get(db_prefix() . 'contactcenter_assistant_templates')->row();
        if ($franchise && file_exists($franchise_path)) {
            $content = file_get_contents($franchise_path);
            $this->ci->db->where('id', $franchise->id);
            $this->ci->db->update(db_prefix() . 'contactcenter_assistant_templates', [
                'instructions' => $content,
                'description' => 'Template for franchise/licensing sales: qualification, pitch, Google Meet scheduling. Replace [NOME_EMPRESA], [NOME_AGENTE], [VALOR_LICENCA].',
            ]);
        }
    }
}
