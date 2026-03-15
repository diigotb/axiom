<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends App_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('contactcenter_model');
        $this->load->model('staff_model');
        $this->load->model('invoices_model');
        $this->session->sess_destroy();
    }

    public function index()
    {
        //cron do follow-up de leads
        $this->contactcenter_model->cron_leads_engine();
        //cron do contador
        $this->contactcenter_model->cron_contador_leads();
        //cron do health monitor (Number Health & Maturation Engine)
        $this->contactcenter_model->update_device_health_scores();
        //cron do internal maturation (Auto-Chat)
        $this->contactcenter_model->cron_internal_maturation();
        //cron do safe groups
        $this->contactcenter_model->cron_safe_groups();
        //cron notificação staff
        $this->contactcenter_model->cron_notification_staff();
        //cron notificação de lembretes
        $this->contactcenter_model->cron_notification_reminders_whatsapp_leads();
        //cron agendamento
        $this->contactcenter_model->cron_confirm_agendamento();

        //cron status device
        $this->contactcenter_model->cron_status_device();

        //cron coverte file base64 em arquivo
        // $this->contactcenter_model->cron_file_message_base64();

        //cron fila de mensagens
        $this->contactcenter_model->process_contactcenter_queue();

        //cron sincroniza contatos whatsapp
        // $this->contactcenter_model->cron_sincroniza_contact_whatsapp();       

        //cron historico
        // if (get_option("contac_settings_sincronizacao_whatsapp_active") == 1) {
        //     $this->contactcenter_model->sincronize_historico_cron();
        // }
    }

    public function whats()
    {
        $s = $this->contactcenter_model->cron_engine_converstion();
        print_r($s);
    }

    public function ia()
    {
        if (get_option("historico_mgs_ai_active")) {
            $this->contactcenter_model->cron_send_msg_openai_historico_msg();
        } else {
            $this->contactcenter_model->cron_send_msg_openai();
        }
    }


    public function teste()
    {
        echo "<pre>";
        // $s = $this->contactcenter_model->convert_to_customer(1496);
        $s = $this->contactcenter_model->cron_sincroniza_contact_whatsapp();
        print_r($s);
    }
    public function contador_leads()
    {
        echo "<pre>";
        $s = $this->contactcenter_model->cron_contador_leads();
        print_r($s);
    }
}
