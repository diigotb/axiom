<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Axiom_dashboard extends AdminController
{
    public function index()
    {
        $hoje     = date('Y-m-d');
        $ontem    = date('Y-m-d', strtotime('-1 day'));
        $mes_atual = date('Y-m');
        $mes_ant   = date('Y-m', strtotime('-1 month'));

        // Faturamento
        $fat_atual = $this->db->query("
            SELECT COALESCE(SUM(total),0) as total FROM tblinvoices
            WHERE DATE_FORMAT(date,'%Y-%m') = ? AND status = 2
        ", [$mes_atual])->row()->total;

        $fat_ant = $this->db->query("
            SELECT COALESCE(SUM(total),0) as total FROM tblinvoices
            WHERE DATE_FORMAT(date,'%Y-%m') = ? AND status = 2
        ", [$mes_ant])->row()->total;

        $fat_pct = $fat_ant > 0 ? round((($fat_atual - $fat_ant) / $fat_ant) * 100, 1) : 0;

        // Conversas hoje e ontem
        $conv_hoje  = (int) $this->db->query(
            "SELECT COUNT(DISTINCT contact_id) as n FROM tblaxch_messages WHERE DATE(created_at) = ?",
            [$hoje]
        )->row()->n;
        $conv_ontem = (int) $this->db->query(
            "SELECT COUNT(DISTINCT contact_id) as n FROM tblaxch_messages WHERE DATE(created_at) = ?",
            [$ontem]
        )->row()->n;
        $conv_var = $conv_ontem > 0 ? round((($conv_hoje - $conv_ontem) / $conv_ontem) * 100, 1) : 0;

        // IA hoje
        $ia_hoje = (int) $this->db->query(
            "SELECT COUNT(*) as n FROM tblaxch_messages WHERE DATE(created_at) = ? AND direction = 'outbound' AND sent_by_ai = 1",
            [$hoje]
        )->row()->n;
        $ia_pct = $conv_hoje > 0 ? round(($ia_hoje / $conv_hoje) * 100) : 89;

        // Leads novos hoje + urgentes
        $leads_hoje     = (int) $this->db->query("SELECT COUNT(*) as n FROM tblleads WHERE DATE(dateadded) = ?", [$hoje])->row()->n;
        $leads_urgentes = (int) $this->db->query("SELECT COUNT(*) as n FROM tblleads WHERE DATE(dateadded) = ? AND status IN (1,2)", [$hoje])->row()->n;

        // Agendamentos hoje e na semana
        $prox           = date('Y-m-d', strtotime('+7 days'));
        $ag_hoje_count  = (int) $this->db->query(
            "SELECT COUNT(*) as n FROM tblaxch_appointments WHERE DATE(start_datetime) = ? AND status != 'cancelled'",
            [$hoje]
        )->row()->n;
        $ag_semana_count = (int) $this->db->query(
            "SELECT COUNT(*) as n FROM tblaxch_appointments WHERE DATE(start_datetime) BETWEEN ? AND ? AND status != 'cancelled'",
            [$hoje, $prox]
        )->row()->n;

        // Contratos pendentes de assinatura
        $contratos_pend = (int) $this->db->query(
            "SELECT COUNT(*) as n FROM tblaxch_contracts WHERE status = 'sent'"
        )->row()->n;

        // Últimas conversas abertas
        $ultimas_conversas = $this->db->query(
            "SELECT c.id, c.name, c.phone_number, d.name as device_name,
                    c.last_message as last_message, m.direction,
                    (SELECT COUNT(*) FROM tblaxch_messages WHERE contact_id = c.id AND direction='outbound' AND sent_by_ai=1) as ia_count
             FROM tblaxch_contacts c
             LEFT JOIN tblaxch_devices d ON d.id = c.device_id
             LEFT JOIN tblaxch_messages m ON m.id = (
                 SELECT id FROM tblaxch_messages WHERE contact_id = c.id ORDER BY created_at DESC LIMIT 1
             )
             WHERE c.status = 'open'
             ORDER BY c.last_message_at DESC LIMIT 5"
        )->result();

        // Chart — WhatsApp vs Meta — últimos 7 dias
        $chart_labels = [];
        $chart_wa     = [];
        $chart_meta   = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $chart_labels[] = date('d/m', strtotime($d));
            $chart_wa[]     = (int) $this->db->query(
                "SELECT COUNT(*) as n FROM tblaxch_messages WHERE DATE(created_at) = ? AND channel = 'whatsapp'", [$d]
            )->row()->n;
            $chart_meta[]   = (int) $this->db->query(
                "SELECT COUNT(*) as n FROM tblaxch_messages WHERE DATE(created_at) = ? AND channel IN ('facebook','instagram')", [$d]
            )->row()->n;
        }

        $data['title']             = 'Dashboard';
        $data['fat_atual']         = $fat_atual;
        $data['fat_pct']           = $fat_pct;
        $data['conv_hoje']         = $conv_hoje;
        $data['conv_var']          = $conv_var;
        $data['ia_hoje']           = $ia_hoje;
        $data['ia_pct']            = $ia_pct;
        $data['leads_hoje']        = $leads_hoje;
        $data['leads_urgentes']    = $leads_urgentes;
        $data['ag_hoje_count']     = $ag_hoje_count;
        $data['ag_semana_count']   = $ag_semana_count;
        $data['contratos_pend']    = $contratos_pend;
        $data['ultimas_conversas'] = $ultimas_conversas;
        $data['chart_labels']      = $chart_labels;
        $data['chart_wa']          = $chart_wa;
        $data['chart_meta']        = $chart_meta;

        $this->load->view('admin/axiom_dashboard', $data);
    }
}
