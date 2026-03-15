<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Appointment_confirm_public extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        if (function_exists('load_client_language')) {
            load_client_language();
        }
        $this->load->model('contactcenter/contactcenter_model');
    }

    /**
     * Display the public appointment confirmation page
     * @param string $token Confirmation token
     */
    public function index($token = null)
    {
        if (!$token || strlen($token) < 20) {
            show_404();
            return;
        }

        $record = $this->contactcenter_model->get_confirmation_by_token($token);
        if (!$record) {
            $data = [
                'error'   => true,
                'message' => _l('contac_confirm_public_invalid'),
            ];
            $this->load->view('contactcenter/appointment_confirm_public', $data);
            return;
        }

        $contact_name = '';
        if ($record->rel_type == 'lead') {
            $this->db->select('name');
            $this->db->where('id', $record->rel_id);
            $lead = $this->db->get(db_prefix() . 'leads')->row();
            $contact_name = $lead ? $lead->name : '';
        } elseif ($record->rel_type == 'customer') {
            $this->db->select('company');
            $this->db->where('userid', $record->rel_id);
            $client = $this->db->get(db_prefix() . 'clients')->row();
            $contact_name = $client ? $client->company : '';
        }

        $company_name = get_option('companyname');

        $data = [
            'error'        => false,
            'token'        => $token,
            'record'       => $record,
            'contact_name' => $contact_name,
            'company_name' => $company_name,
            'event_date'   => _dt($record->start),
            'event_title'  => $record->title ?? '',
            'already_used' => ($record->status !== 'pending'),
            'previous_status' => $record->status,
        ];

        $this->load->view('contactcenter/appointment_confirm_public', $data);
    }

    /**
     * Process confirmation/cancellation from the public page
     * @param string $token Confirmation token
     */
    public function process($token = null)
    {
        if (!$token || strlen($token) < 20) {
            show_404();
            return;
        }

        $action = $this->input->post('action');
        if (!in_array($action, ['confirm', 'cancel'])) {
            show_404();
            return;
        }

        $result = $this->contactcenter_model->process_confirmation_token($token, $action);

        $record = $this->contactcenter_model->get_confirmation_by_token($token);

        $contact_name = '';
        if ($record) {
            if ($record->rel_type == 'lead') {
                $this->db->select('name');
                $this->db->where('id', $record->rel_id);
                $lead = $this->db->get(db_prefix() . 'leads')->row();
                $contact_name = $lead ? $lead->name : '';
            } elseif ($record->rel_type == 'customer') {
                $this->db->select('company');
                $this->db->where('userid', $record->rel_id);
                $client = $this->db->get(db_prefix() . 'clients')->row();
                $contact_name = $client ? $client->company : '';
            }
        }

        $data = [
            'error'           => !$result['success'],
            'token'           => $token,
            'record'          => $record,
            'contact_name'    => $contact_name,
            'company_name'    => get_option('companyname'),
            'event_date'      => $record ? _dt($record->start) : '',
            'event_title'     => $record ? ($record->title ?? '') : '',
            'already_used'    => true,
            'previous_status' => $record ? $record->status : '',
            'process_result'  => $result,
        ];

        $this->load->view('contactcenter/appointment_confirm_public', $data);
    }
}
