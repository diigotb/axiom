<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Assistant_form_public extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        // Use application default language for public form (not admin session)
        if (function_exists('load_client_language')) {
            load_client_language();
        }
        $this->load->model('contactcenter/contactcenter_model');
    }

    /**
     * Display the public onboarding wizard form
     * @param string $token Public form token
     */
    public function index($token = null)
    {
        if (!$token) {
            show_404();
            return;
        }

        $assistant = $this->contactcenter_model->get_assistant_by_form_token($token);
        if (!$assistant) {
            show_404();
            return;
        }

        $saved = $this->contactcenter_model->get_assistant_onboarding($assistant->id);
        $saved_data = null;
        if ($saved && !empty($saved->form_data)) {
            $saved_data = is_string($saved->form_data) ? json_decode($saved->form_data, true) : (array) $saved->form_data;
        }

        $data = [
            'assistant' => $assistant,
            'token' => $token,
            'saved_data' => $saved_data,
        ];
        $this->load->view('contactcenter/assistant_onboarding_wizard', $data);
    }

    /**
     * Save onboarding form data (AJAX)
     */
    public function save()
    {
        $this->config->set_item('csrf_protection', false);
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        header('Content-Type: application/json');

        $token = $this->input->post('token');
        if (!$token) {
            echo json_encode(['success' => false, 'message' => 'Invalid token']);
            return;
        }

        $assistant = $this->contactcenter_model->get_assistant_by_form_token($token);
        if (!$assistant) {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired link']);
            return;
        }

        $form_data = $this->input->post('form_data');
        if (is_string($form_data)) {
            $form_data = json_decode($form_data, true);
        }
        if (!is_array($form_data)) {
            $form_data = [];
        }

        // Handle knowledge materials file uploads
        if (!empty($_FILES['materials_files']['name'])) {
            $upload_result = $this->contactcenter_model->upload_onboarding_materials($assistant->id);
            if (!empty($upload_result['paths'])) {
                $form_data['uploaded_materials'] = $upload_result['paths'];
            }
            if (!empty($upload_result['errors'])) {
                echo json_encode([
                    'success' => false,
                    'message' => implode('; ', $upload_result['errors'])
                ]);
                return;
            }
        }

        $id = $this->contactcenter_model->save_assistant_onboarding($assistant->id, $form_data);
        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Data saved successfully', 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save']);
        }
    }
}
