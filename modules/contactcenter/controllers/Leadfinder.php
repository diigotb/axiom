<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Leadfinder extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('leadfinder_model');
    }

    public function index()
    {

        if ($this->input->post()) {
            $params = $this->input->post();
            $data['params'] = $params;
            $data['results'] = $this->leadfinder_model->search_leads($params);       
            if (isset($data['results']['error'])) {
                $data['error'] = $data['results']['error'];
            }else {
                $data['error'] = null;
            }
        }
        $data['info'] = $this->leadfinder_model->getImportGuidelinesMessage();        
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['title'] = _l('leadfinder_title');
        $this->load->view('leadfinder_form', $data);
    }

    public function import()
    {
        if ($this->input->post()) {
            $results = json_decode($this->input->post('results_json'), true);
            $selected = $this->input->post('selected');
            $data = $this->input->post();
            $leads = [];
            if (is_array($selected)) {
                foreach ($selected as $idx) {
                    if (isset($results[$idx])) {
                        $leads[] = $results[$idx];
                    }
                }
            }
            $count = $this->leadfinder_model->import_leads($leads, $data);
            set_alert('success', _l('leadfinder_imported', $count));
        }
        redirect(admin_url('contactcenter/leadfinder'));
    }
}
