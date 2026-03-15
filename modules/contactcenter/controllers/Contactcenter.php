<?php

use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;

defined('BASEPATH') or exit('No direct script access allowed');

class Contactcenter extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('contactcenter_model');
        $this->load->model('staff_model');
        $this->load->model('leads_model');
        $this->load->library('app_modules');
        $this->load->library("chatweb");
        $this->load->library("chatbot");

        if (option_exists('contactcenter_verification_id')) {
            if (!$this->contactcenter_model->is_valid_plan()) {
                set_alert('danger', _l('contac_aviso_plano_invalido'));
                redirect(admin_url("leads"));
            }
        }
    }

    public function activate()
    {
        $this->load->view('activate');
    }

    public function index()
    {
        $this->home();
    }

    public function home()
    {
        redirect(admin_url());
    }

    public function plano()
    {
        $data['plano'] = $this->contactcenter_model->get_planos();
        $this->load->view('plano', $data);
    }


    public function device()
    {

        $data['members'] = $this->staff_model->get();
        $data['drawflow'] = $this->chatbot->get_automation_active();
        $data['assistants'] = $this->contactcenter_model->get_assistants_ai();
        $data["device"] = $this->contactcenter_model->get_device();
        $data["servers"] = $this->contactcenter_model->get_server();
        $data["models_contract"] = $this->contactcenter_model->get_templates_contract();
        $data["category_contract"] = $this->contactcenter_model->get_contract_type_id();
        $this->load->view('device', $data);
    }

    public function qrcode_single($id)
    {
        $device = $this->contactcenter_model->get_device($id);
        $data["device"] = $device;

        if ($device->api_local == 1) {
            redirect(admin_url("contactcenter/chatall"));
        }

        if (!has_permission('contactcenter', '', 'chat_viwer_all') && get_staff_user_id() != $device->staffid) {
            set_alert('danger', _l("contac_aviso_sem_acesso"));
            redirect(admin_url("contactcenter/chatall"));
        }

        $device = $this->contactcenter_model->get_device($id);
        $server = $this->contactcenter_model->get_server($device->server_id);
        $dadoServe = [
            "server" => $server,
            "device" => $device
        ];
        $data["dadoServe"] = json_encode($dadoServe);

        $this->load->view('qrcode_single', $data);
    }

    public function conversation_engine()
    {


        $data['devices'] = $this->contactcenter_model->get_device_by_type(2);

        // busco os taggable onde real_type = lead               
        $data['tagsArray']  = $this->db->get(db_prefix() . 'tags')->result_array();

        $data['statuses'] = $this->leads_model->get_status();

        // Get sources, cities, and states for filters
        $data['sources'] = $this->leads_model->get_source();

        // Get unique cities from leads
        $this->db->select('city');
        $this->db->where('city !=', '');
        $this->db->where('city IS NOT NULL');
        $this->db->group_by('city');
        $this->db->order_by('city', 'ASC');
        $data['cities'] = $this->db->get(db_prefix() . 'leads')->result_array();

        // Get unique states from leads
        $this->db->select('state');
        $this->db->where('state !=', '');
        $this->db->where('state IS NOT NULL');
        $this->db->group_by('state');
        $this->db->order_by('state', 'ASC');
        $data['states'] = $this->db->get(db_prefix() . 'leads')->result_array();

        // Get custom fields for leads (for backup phone field)
        $this->load->helper('custom_fields');
        $data['custom_fields'] = get_custom_fields('leads');
        // Date-type custom fields for birthday campaign (date_picker stores Y-m-d)
        $data['birthday_custom_fields'] = get_custom_fields('leads', ['type' => 'date_picker']);

        // Get countries for backup phone country selection
        $this->load->helper('countries');
        $data['countries'] = get_all_countries();

        if (!is_admin()) {
            $staffid = get_staff_user_id();
        } else {
            $staffid = '';
        }

        // Get filter preferences from session or default
        $show_inactive = $this->session->userdata('conversation_engine_show_inactive') ? true : false;
        $filters = [
            'show_inactive' => $show_inactive
        ];

        $data["conversation"] = $this->contactcenter_model->get_conversation_engine('', $staffid, $filters);
        $data['show_inactive'] = $show_inactive;
        $this->load->view('conversation_engine', $data);
    }

    public function conversation_list($id)
    {
        $data["conversation"] = $this->contactcenter_model->get_conversation_engine($id);
        $data["list"] = $this->contactcenter_model->get_conversation_engine_list($id);

        // Load merge fields for the message editor (same as leads_engine_messages)
        if (!class_exists('Leads_merge_fields', false)) {
            $this->load->library('merge_fields/leads_merge_fields');
        }

        // Get standard merge fields
        $merge_fields = $this->leads_merge_fields->build();

        // Add custom fields for leads
        $custom_fields = get_custom_fields('leads');
        foreach ($custom_fields as $field) {
            $merge_fields[] = [
                'name' => $field['name'] . ' (Custom Field)',
                'key' => '{' . $field['slug'] . '}',
                'available' => ['leads'],
            ];
        }

        $data['merge_fields'] = $merge_fields;

        $this->load->view('conversation_list', $data);
    }


    public function assistant_ai()
    {
        $data["assistants"] = $this->contactcenter_model->get_assistants_ai();
        $data["templates"] = $this->contactcenter_model->get_assistant_templates();
        if (!$this->db->table_exists(db_prefix() . 'contactcenter_assistant_templates')) {
            set_alert('warning', _l('contac_assistant_templates_migration_required'));
        }
        $this->load->view('assistant_ai', $data);
    }

    public function assistant_templates()
    {
        if (!has_permission('contractcenter', '', 'create')) {
            access_denied('contactcenter');
        }
        $data['templates'] = $this->contactcenter_model->get_assistant_templates();
        $data['templates_table_exists'] = $this->db->table_exists(db_prefix() . 'contactcenter_assistant_templates');
        if (!$data['templates_table_exists']) {
            set_alert('warning', _l('contac_assistant_templates_migration_required'));
        }
        $data['available_functions'] = [
            'get_lead_info' => _l("contac_assistent_function_get_lead_info"),
            'get_lead_context' => _l("contac_assistent_function_get_lead_context"),
            'manage_conversation' => _l("contac_assistent_function_manage_conversation"),
            'update_leads' => _l("contac_assistent_function_update_leads"),
            'get_horario_agenda' => _l("contac_assistent_function_agendar"),
            'create_contract' => _l("contac_assistent_function_create_contract"),
            'get_tabela_precos' => _l("contac_assistent_function_get_tabela_precos"),
            'open_ticket' => _l("contac_assistent_function_open_ticket"),
            'get_faturas_axiom' => _l("contac_assistent_function_get_faturas"),
            'send_media' => _l("contac_assistent_function_send_media"),
            'create_group_chat' => _l("contac_assistent_function_create_group_chat"),
        ];
        $this->load->view('assistant_templates', $data);
    }

    public function assistant_template_edit($id = null)
    {
        if (!has_permission('contractcenter', '', 'create')) {
            access_denied('contactcenter');
        }
        $template = $id ? $this->contactcenter_model->get_assistant_templates($id) : null;
        if ($id && (!$template || $template->is_system)) {
            set_alert('danger', _l('contac_assistant_template_cannot_edit'));
            redirect(admin_url('contactcenter/assistant_templates'));
        }
        $data['template'] = $template;
        $data['available_functions'] = [
            'get_lead_info' => _l("contac_assistent_function_get_lead_info"),
            'get_lead_context' => _l("contac_assistent_function_get_lead_context"),
            'manage_conversation' => _l("contac_assistent_function_manage_conversation"),
            'update_leads' => _l("contac_assistent_function_update_leads"),
            'get_horario_agenda' => _l("contac_assistent_function_agendar"),
            'create_contract' => _l("contac_assistent_function_create_contract"),
            'get_tabela_precos' => _l("contac_assistent_function_get_tabela_precos"),
            'open_ticket' => _l("contac_assistent_function_open_ticket"),
            'get_faturas_axiom' => _l("contac_assistent_function_get_faturas"),
            'send_media' => _l("contac_assistent_function_send_media"),
            'create_group_chat' => _l("contac_assistent_function_create_group_chat"),
        ];
        $this->load->view('assistant_template_edit', $data);
    }

    public function save_assistant_template()
    {
        if (!has_permission('contractcenter', '', 'create') || !$this->input->post()) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false]);
                return;
            }
            redirect(admin_url('contactcenter/assistant_templates'));
        }
        if (!$this->db->table_exists(db_prefix() . 'contactcenter_assistant_templates')) {
            set_alert('warning', _l('contac_assistant_templates_migration_required'));
            redirect(admin_url('contactcenter/assistant_templates'));
        }
        $data = $this->input->post();
        $data['functions'] = $this->input->post('functions') ?: [];
        if (!empty($_FILES['file']['name'])) {
            $uploaded = $this->contactcenter_model->uploads_image('contactcenter/assistant_templates', 2048);
            if ($uploaded) {
                $data['image_path'] = 'uploads/' . $uploaded;
            }
        }
        $result = $this->contactcenter_model->save_assistant_template($data);
        if ($this->input->is_ajax_request()) {
            echo json_encode($result);
            return;
        }
        set_alert('success', _l('contac_save'));
        redirect(admin_url('contactcenter/assistant_templates'));
    }

    public function delete_assistant_template($id)
    {
        if (!has_permission('contractcenter', '', 'create')) {
            access_denied('contactcenter');
        }
        if (!$this->db->table_exists(db_prefix() . 'contactcenter_assistant_templates')) {
            set_alert('warning', _l('contac_assistant_templates_migration_required'));
            redirect(admin_url('contactcenter/assistant_templates'));
        }
        if ($this->contactcenter_model->delete_assistant_template($id)) {
            set_alert('success', _l('contac_assistant_template_deleted'));
        } else {
            set_alert('danger', _l('contac_assistant_template_cannot_delete'));
        }
        redirect(admin_url('contactcenter/assistant_templates'));
    }

    /**
     * Sync system templates (Aesthetic Clinics, Franchise Sales) from template files.
     * Use this when migrations have already run and you want to update template content from files.
     */
    public function sync_assistant_templates_from_files()
    {
        if (!has_permission('contractcenter', '', 'create')) {
            access_denied('contactcenter');
        }
        if (!$this->db->table_exists(db_prefix() . 'contactcenter_assistant_templates')) {
            set_alert('warning', _l('contac_assistant_templates_migration_required'));
            redirect(admin_url('contactcenter/assistant_templates'));
        }
        
        $base = module_dir_path('contactcenter') . 'assets/templates/';
        $updated = 0;

        $templates = [
            'Aesthetic Clinics' => 'aesthetic_clinics_template.txt',
            'Franchise Sales' => 'franchise_sales_template.txt',
            'Appointment Scheduling' => 'appointment_template.txt',
            'Lead Qualification' => 'qualification_template.txt',
            'Content Distribution' => 'media_template.txt',
            'Human Handoff' => 'handoff_template.txt',
            'Contract Creator' => 'contract_template.txt',
            'Price Inquiry' => 'prices_template.txt',
            'Financial Helper' => 'financial_template.txt',
            'Support Ticket' => 'ticket_template.txt',
            'Group Onboarding' => 'group_template.txt',
            'Context Aware Assistant' => 'context_template.txt'
        ];

        foreach ($templates as $name => $file) {
            $path = $base . $file;
            if (file_exists($path)) {
                // Try to find by exact name first
                $this->db->where('is_system', 1);
                $this->db->where('name', $name);
                $row = $this->db->get(db_prefix() . 'contactcenter_assistant_templates')->row();
                
                // Fallback for the original 2 templates that might have slight name variations in older DBs
                if (!$row && ($name == 'Aesthetic Clinics' || $name == 'Franchise Sales')) {
                    $this->db->where('is_system', 1);
                    $this->db->like('name', explode(' ', $name)[0]); // 'Aesthetic' or 'Franchise'
                    $row = $this->db->get(db_prefix() . 'contactcenter_assistant_templates')->row();
                }

                if ($row) {
                    $this->db->where('id', $row->id);
                    $this->db->update(db_prefix() . 'contactcenter_assistant_templates', [
                        'instructions' => file_get_contents($path),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $updated++;
                }
            }
        }

        set_alert('success', $updated ? _l('contac_assistant_templates_synced') : _l('contac_assistant_templates_sync_none'));
        redirect(admin_url('contactcenter/assistant_templates'));
    }

    public function generate_assistant_from_onboarding($assistant_id)
    {
        // Increase execution time to handle large prompts/responses
        set_time_limit(300); 
        ini_set('memory_limit', '512M');

        if (!has_permission('contractcenter', '', 'create')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }

        $assistant = $this->contactcenter_model->get_assistants_ai($assistant_id);
        if (!$assistant) {
            echo json_encode(['success' => false, 'message' => _l('not_found')]);
            return;
        }

        $onboarding_data = $this->contactcenter_model->get_assistant_onboarding($assistant_id);
        if (!$onboarding_data) {
            echo json_encode(['success' => false, 'message' => _l('contac_assistant_no_onboarding')]);
            return;
        }

        $gemini_api_key = trim(get_option('contactcenter_gemini_api_key'));
        if (empty($gemini_api_key)) {
            $gemini_api_key = trim(get_option('axiom_studio_gemini_api_key'));
        }

        if (empty($gemini_api_key)) {
            echo json_encode(['success' => false, 'message' => _l('ads_analytics_gemini_api_not_configured')]);
            return;
        }

        // Prepare examples
        $example_aesthetic = file_get_contents(module_dir_path('contactcenter') . 'assets/templates/aesthetic_clinics_template.txt');
        $example_franchise = file_get_contents(module_dir_path('contactcenter') . 'assets/templates/franchise_sales_template.txt');

        $prompt = "You are an expert AI assistant configurator for a CRM system.
Your task is to generate the configuration for an AI assistant based on the user's onboarding data.

ONBOARDING DATA:
" . json_encode($onboarding_data) . "

EXAMPLE TEMPLATE 1 (Aesthetic Clinic):
" . $example_aesthetic . "

EXAMPLE TEMPLATE 2 (Franchise Sales):
" . $example_franchise . "

INSTRUCTIONS:
1. Analyze the ONBOARDING DATA to understand the business type, tone, objectives, and specific details.
2. Generate a JSON object with the following fields:
   - `ai_name`: A creative and professional name for the assistant.
   - `description`: A short description of the assistant's role.
   - `instructions`: The complete system instructions (system prompt) for the assistant.
     - The instructions MUST be in Portuguese (Brazil).
     - Follow the structure and depth of the EXAMPLE TEMPLATES.
     - Replace all placeholders (like [NOME_EMPRESA], [TELEFONE]) with actual data from the onboarding.
     - If specific data is missing in the onboarding, use a clear placeholder like [PREENCHER_INFO].
     - Incorporate the defined tone, objectives, and rules from the onboarding.
   - `functions`: An array of function names to enable. Choose ONLY from this list:
     ['get_lead_info', 'get_lead_context', 'manage_conversation', 'update_leads', 'get_horario_agenda', 'create_contract', 'get_tabela_precos', 'open_ticket', 'get_faturas_axiom', 'send_media', 'create_group_chat'].
     - Analyze the 'functions' (or 'capabilities') field in the ONBOARDING DATA.
     - Map the user's requested capabilities to these technical functions:
       - 'Acessar informações do cliente' -> 'get_lead_info'
       - 'Ver histórico de conversas' -> 'get_lead_context'
       - 'Repassar para humano' -> 'manage_conversation'
       - 'Atualizar dados do cliente' -> 'update_leads'
       - 'Agendar compromissos' -> 'get_horario_agenda'
       - 'Criar contratos' -> 'create_contract'
       - 'Enviar tabela de preços' -> 'get_tabela_precos'
       - 'Abrir tickets de suporte' -> 'open_ticket'
       - 'Buscar faturas' -> 'get_faturas_axiom'
       - 'Enviar imagens e arquivos' -> 'send_media'
       - 'Criar grupos de chat' -> 'create_group_chat'
     - ONLY enable functions that are explicitly requested in the onboarding data or are strictly necessary for the requested flow.

OUTPUT FORMAT:
Return ONLY the raw JSON object. Do not include markdown formatting like ```json or any other text. Start with { and end with }.
";

        $result = $this->_call_gemini_api($gemini_api_key, $prompt, true);

        if (isset($result['error'])) {
            // Log the error for debugging
            log_activity('Gemini API Error in generate_assistant_from_onboarding: ' . json_encode($result));
            echo json_encode(['success' => false, 'message' => $result['error'] . (isset($result['http_code']) ? ' (HTTP ' . $result['http_code'] . ')' : '')]);
            return;
        }

        // Parse the result
        $config = $result; // Result is already parsed JSON from _call_gemini_api

        if (!$config || !is_array($config)) {
             echo json_encode(['success' => false, 'message' => 'Failed to parse Gemini response: Invalid structure']);
             return;
        }

        // Update the assistant
        $update_data = [
            'id' => $assistant_id,
            'ai_name' => $config['ai_name'] ?? $assistant->ai_name,
            'instructions' => $config['instructions'] ?? $assistant->instructions,
            'functions' => isset($config['functions']) ? $config['functions'] : (is_string($assistant->functions) ? json_decode($assistant->functions, true) : $assistant->functions),
            'vector_id' => $assistant->vector_id,
            'model' => $assistant->model,
            'ai_token' => $assistant->ai_token
        ];

        // Ensure functions is an array
        if (isset($update_data['functions']) && !is_array($update_data['functions'])) {
            $update_data['functions'] = [];
        }

        // Log generated functions for debugging
        log_activity('AXIOM Generated Functions: ' . json_encode($update_data['functions']));

        $this->contactcenter_model->add_assistant($update_data);

        echo json_encode(['success' => true, 'message' => _l('contac_assistant_generated_success')]);
    }

    private function sync_function_templates()
    {
        $updated = 0;
        $base = module_dir_path('contactcenter') . 'assets/templates/functions/';
        $items = [
            ['key' => 'get_lead_info', 'icon' => 'fa-user'],
            ['key' => 'get_lead_context', 'icon' => 'fa-history'],
            ['key' => 'manage_conversation', 'icon' => 'fa-comments'],
            ['key' => 'update_leads', 'icon' => 'fa-edit'],
            ['key' => 'get_horario_agenda', 'icon' => 'fa-calendar'],
            ['key' => 'create_contract', 'icon' => 'fa-file-text'],
            ['key' => 'get_tabela_precos', 'icon' => 'fa-money'],
            ['key' => 'open_ticket', 'icon' => 'fa-ticket'],
            ['key' => 'get_faturas_axiom', 'icon' => 'fa-file-invoice'],
            ['key' => 'send_media', 'icon' => 'fa-paperclip'],
            ['key' => 'create_group_chat', 'icon' => 'fa-users'],
        ];
        foreach ($items as $item) {
            $name = 'Function: ' . $item['key'];
            $this->db->where('name', $name);
            if ($this->db->get(db_prefix() . 'contactcenter_assistant_templates')->num_rows() > 0) {
                continue;
            }
            $path = $base . $item['key'] . '.txt';
            $instructions = file_exists($path) ? file_get_contents($path) : $this->get_function_template_default($item['key']);
            $this->db->insert(db_prefix() . 'contactcenter_assistant_templates', [
                'name' => $name,
                'description' => '',
                'icon' => $item['icon'],
                'instructions' => $instructions,
                'model' => 'gpt-4o-mini',
                'functions' => json_encode([$item['key']]),
                'is_system' => 1,
            ]);
            $updated++;
        }
        return $updated;
    }

    private function get_function_template_default($key)
    {
        $defaults = [
            'get_lead_info' => "# Get Lead Info\nAlways call get_lead_info at conversation start.\nUse for: personalization, any niche. Combine with get_lead_context, update_leads.",
            'get_lead_context' => "# Get Lead Context\nCall get_lead_context to see full history before replying.\nUse for: support, sales, follow-ups. Combine with get_lead_info, manage_conversation.",
            'manage_conversation' => "# Manage Conversation\nUse when lead asks for human or manager.\nUse for: support escalation, sales handoff. Combine with get_lead_context, update_leads.",
            'update_leads' => "# Update Leads\nSave lead answers and status via update_leads in real time.\nUse for: qualification, pipelines, CRM. Combine with get_lead_info, get_horario_agenda.",
            'get_horario_agenda' => "# Scheduling\nUse get_horario_agenda to check availability, set_horario_agenda to confirm.\nUse for: clinics, services, consultants. Combine with update_leads, send_media.",
            'create_contract' => "# Create Contract\nGenerate and send contracts to leads.\nUse for: services, freelancers, B2B. Combine with get_lead_info, update_leads.",
            'get_tabela_precos' => "# Price Table\nConsult and share price tables.\nUse for: retail, services, B2B. Combine with get_lead_info, send_media.",
            'open_ticket' => "# Create Ticket\nOpen support tickets from conversations.\nUse for: support, SaaS, IT. Combine with get_lead_context, manage_conversation.",
            'get_faturas_axiom' => "# Fetch Invoices\nFetch and send invoice info to leads.\nUse for: finance, subscriptions. Combine with get_lead_info, update_leads.",
            'send_media' => "# Send Media\nSend images, videos, documents, audio via send_media.\nUse for: catalogs, portfolios, any niche. Combine with get_lead_info, get_tabela_precos.",
            'create_group_chat' => "# Create Group Chat\nCreate WhatsApp groups with lead and team.\nUse for: sales, onboarding, meetings. Combine with get_horario_agenda, manage_conversation.",
        ];
        return $defaults[$key] ?? "# Template: {$key}\nCustomize as needed.";
    }

    public function generate_examples_from_contacts()
    {
        if (!has_permission('contractcenter', '', 'create')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }

        $contact_ids = $this->input->post('contact_ids');
        $assistant_id = $this->input->post('assistant_id');

        if (empty($contact_ids) || !is_array($contact_ids)) {
            echo json_encode(['success' => false, 'message' => _l('contac_select_at_least_one_contact')]);
            return;
        }

        $gemini_api_key = trim(get_option('contactcenter_gemini_api_key'));
        if (empty($gemini_api_key)) {
            $gemini_api_key = trim(get_option('axiom_studio_gemini_api_key'));
        }

        if (empty($gemini_api_key)) {
            echo json_encode(['success' => false, 'message' => _l('ads_analytics_gemini_api_not_configured')]);
            return;
        }

        // Fetch interactions for selected contacts
        $interactions = [];
        foreach ($contact_ids as $contact_id) {
            // Assuming contact_id here refers to the ID in the contacts table or leads table
            // You might need to adjust this query based on how you store chat history
            // For now, let's assume we fetch from contactcenter_chat_history or similar
            // Using a hypothetical model method:
            $history = $this->contactcenter_model->get_chat_history_for_contact($contact_id, 10); // Get last 10 messages
            if ($history) {
                $interactions[] = [
                    'contact_id' => $contact_id,
                    'messages' => $history
                ];
            }
        }

        if (empty($interactions)) {
            echo json_encode(['success' => false, 'message' => _l('no_interactions_found')]);
            return;
        }

        $prompt = "Analyze the following real chat interactions between agents and customers:\n\n";
        $prompt .= json_encode($interactions) . "\n\n";
        $prompt .= "Based on these interactions, generate 3-5 high-quality, realistic 'Interaction Examples' for an AI assistant training prompt.\n";
        $prompt .= "Format the output as:\n";
        $prompt .= "Usuário: [User query]\nAgente: [Agent response]\n\n";
        $prompt .= "Focus on successful resolutions, good tone, and correct procedure usage. Anonymize any personal data.";

        $result = $this->_call_gemini_api($gemini_api_key, $prompt, false); // Expect text, not JSON

        if (isset($result['error'])) {
            echo json_encode(['success' => false, 'message' => $result['error']]);
            return;
        }

        // The result from _call_gemini_api with expect_json=false is the raw text
        $examples = $result; 

        echo json_encode(['success' => true, 'examples' => $examples]);
    }

    public function generate_script_from_contacts()
    {
        if (!has_permission('contractcenter', '', 'create')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }

        $contact_ids = $this->input->post('contact_ids');
        $assistant_id = $this->input->post('assistant_id');

        if (empty($contact_ids) || !is_array($contact_ids)) {
            echo json_encode(['success' => false, 'message' => _l('contac_select_at_least_one_contact')]);
            return;
        }

        $gemini_api_key = trim(get_option('contactcenter_gemini_api_key'));
        if (empty($gemini_api_key)) {
            $gemini_api_key = trim(get_option('axiom_studio_gemini_api_key'));
        }

        if (empty($gemini_api_key)) {
            echo json_encode(['success' => false, 'message' => _l('ads_analytics_gemini_api_not_configured')]);
            return;
        }

        $assistant = null;
        if ($assistant_id) {
            $assistant = $this->contactcenter_model->get_assistants_ai($assistant_id);
        }

        $interactions = [];
        foreach ($contact_ids as $contact_id) {
            $history = $this->contactcenter_model->get_chat_history_for_contact($contact_id, 20);
            if ($history) {
                $interactions[] = [
                    'contact_id' => $contact_id,
                    'messages'   => $history
                ];
            }
        }

        if (empty($interactions)) {
            echo json_encode(['success' => false, 'message' => _l('no_interactions_found')]);
            return;
        }

        $context_block = '';
        if ($assistant && !empty($assistant->instructions)) {
            $context_block = "CURRENT ASSISTANT INSTRUCTIONS (for context only, to understand the business):\n" . substr($assistant->instructions, 0, 2000) . "\n\n";
        }

        $prompt  = "You are an expert at analyzing real customer service conversations and creating detailed conversation scripts.\n\n";
        $prompt .= $context_block;
        $prompt .= "REAL CONVERSATIONS DATA:\n" . json_encode($interactions, JSON_UNESCAPED_UNICODE) . "\n\n";
        $prompt .= "Based on these real conversations, create a DETAILED CONVERSATION SCRIPT (Script e Fluxo de Conversa) for an AI assistant.\n\n";
        $prompt .= "The script must be written in PORTUGUESE (pt-BR) and follow this structure:\n\n";
        $prompt .= "1. **Saudação Inicial** - How the assistant should greet the customer (based on patterns you see in the real data).\n";
        $prompt .= "2. **Qualificação / Coleta de Dados** - What questions to ask, in what order, to understand the customer's needs. Identify the common data points collected from the real conversations.\n";
        $prompt .= "3. **Apresentação de Soluções** - How the assistant presents products/services/solutions based on the customer's needs. Extract the common offerings mentioned.\n";
        $prompt .= "4. **Tratamento de Objeções** - Common objections or concerns you see in the conversations, and how to handle them.\n";
        $prompt .= "5. **Fechamento / Agendamento** - How conversations typically close (scheduling, next steps, etc.).\n";
        $prompt .= "6. **Encerramento** - How to wrap up the conversation politely.\n\n";
        $prompt .= "IMPORTANT RULES:\n";
        $prompt .= "- Anonymize ALL personal data (names, phone numbers, addresses, specific prices). Replace with generic placeholders like [nome do cliente], [valor], etc.\n";
        $prompt .= "- Identify PATTERNS and BEST PRACTICES from the real conversations.\n";
        $prompt .= "- The script should be actionable - each step should have clear instructions for the AI agent.\n";
        $prompt .= "- Use numbered steps within each section.\n";
        $prompt .= "- Include example phrases the agent should use, extracted and improved from real conversations.\n";
        $prompt .= "- Output ONLY the script text, no markdown code blocks, no JSON.\n";

        $result = $this->_call_gemini_api($gemini_api_key, $prompt, false);

        if (isset($result['error'])) {
            log_activity('AXIOM Generate Script Error: ' . $result['error']);
            echo json_encode(['success' => false, 'message' => $result['error']]);
            return;
        }

        echo json_encode(['success' => true, 'content' => $result]);
    }

    public function get_recent_contacts_for_selection()
    {
        if (!has_permission('contractcenter', '', 'view')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }

        // Fetch recent contacts who have chatted
        // This is a placeholder query - adjust to your actual schema
        // Example: Get contacts from recent messages in contactcenter_message
        $query = "SELECT DISTINCT c.id, c.firstname as name, c.phonenumber 
                  FROM " . db_prefix() . "contacts c
                  JOIN " . db_prefix() . "contactcenter_message m ON m.contact_id = c.id
                  ORDER BY m.date DESC LIMIT 20";
        
        // If you use leads instead/also:
        // $query = "SELECT id, name, phonenumber FROM " . db_prefix() . "leads ORDER BY lastcontact DESC LIMIT 20";
        
        // Let's assume we use a model method or direct DB for now. 
        // Adjusting to a generic approach assuming 'leads' table for simplicity in this context if contacts aren't primary
        
        $contacts = $this->db->query("SELECT id, name, phonenumber FROM " . db_prefix() . "leads WHERE phonenumber IS NOT NULL ORDER BY lastcontact DESC LIMIT 20")->result_array();

        echo json_encode(['success' => true, 'contacts' => $contacts]);
    }

    // ──────────────────────────────────────────────────
    //  Script Auto-Update
    // ──────────────────────────────────────────────────

    public function send_confirmation_test()
    {
        if (!has_permission('contractcenter', '', 'create')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $phone = $this->input->post('phone');
        if (empty($phone)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => _l('contac_confirm_test_phone_required')]);
            return;
        }

        $phone = preg_replace('/\D/', '', $phone);
        $generate_only = $this->input->post('generate_only') == 1;

        $test_event_id = 0;
        $token = $this->contactcenter_model->create_confirmation_token($test_event_id, $phone);

        if (!$token) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => _l('contac_confirm_test_token_error')]);
            return;
        }

        $confirm_link = site_url('contactcenter/appointment_confirm_public/index/' . $token);
        $name = _l('contac_confirm_test_sample_name');
        $date = _dt(date('Y-m-d H:i:s', strtotime('+1 day')));

        $custom_template = get_option('contac_confirm_msg_template');
        if (!empty($custom_template)) {
            $text_message = str_replace(
                ['{name}', '{date}', '{link}'],
                [$name, $date, $confirm_link],
                $custom_template
            );
        } else {
            $title = _l('contac_notification_title_whats_list');
            $greeting = _l('contac_notification_description_whats_saudacao_list');
            $desc = _l('contac_notification_description_whats_list');

            $text_message  = "*{$title}*\n\n";
            $text_message .= "{$greeting} {$name}, {$desc} {$date}\n\n";
            $text_message .= _l('contac_confirm_link_label') . " {$confirm_link}\n\n";
            $text_message .= _l('contac_confirm_keyword_instructions');
        }

        $send_success = false;
        if (!$generate_only && !empty($phone) && $phone !== '0') {
            $send_result = $this->contactcenter_model->send_text($phone, $text_message, get_staff_user_id(), null, null, true, 'crm');
            if ($send_result) {
                if (isset($send_result['key']) || isset($send_result['id']) || isset($send_result['message'])) {
                    $send_success = true;
                }
            }
        }

        if ($generate_only) {
            $msg = _l('contac_confirm_test_link_generated');
        } else {
            $msg = $send_success
                ? _l('contac_confirm_test_sent_success')
                : _l('contac_confirm_test_sent_preview_only');
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success'      => true,
            'sent'         => $send_success,
            'message'      => $msg,
            'link'         => $confirm_link,
            'text_message' => $text_message,
        ]);
    }

    public function save_script_autoupdate_settings()
    {
        if (!has_permission('contractcenter', '', 'create')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }

        $assistant_id    = (int) $this->input->post('assistant_id');
        $enabled         = $this->input->post('enabled') ? 1 : 0;
        $frequency_days  = (int) $this->input->post('frequency_days');
        $lead_count      = (int) $this->input->post('lead_count');
        $lead_status_id  = (int) $this->input->post('lead_status_id');
        $notify_staff_id = (int) $this->input->post('notify_staff_id');

        if ($assistant_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid assistant']);
            return;
        }

        if ($frequency_days < 1) $frequency_days = 7;
        if ($lead_count < 1)     $lead_count = 50;

        $data = [
            'enabled'         => $enabled,
            'frequency_days'  => $frequency_days,
            'lead_count'      => $lead_count,
            'lead_status_id'  => $lead_status_id,
            'notify_staff_id' => $notify_staff_id,
        ];

        $this->contactcenter_model->save_script_update_settings($assistant_id, $data);

        echo json_encode(['success' => true, 'message' => _l('contac_script_autoupdate_saved')]);
    }

    public function get_script_updates($assistant_id)
    {
        if (!has_permission('contractcenter', '', 'view')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }

        $pending = $this->contactcenter_model->get_pending_script_updates((int) $assistant_id);
        $history = $this->contactcenter_model->get_script_update_history((int) $assistant_id, 5);

        echo json_encode([
            'success' => true,
            'pending' => $pending,
            'history' => $history,
        ]);
    }

    public function review_script_update()
    {
        if (!has_permission('contractcenter', '', 'create')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }

        $update_id = (int) $this->input->post('update_id');
        $action    = $this->input->post('action');

        if (!in_array($action, ['approved', 'rejected'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            return;
        }

        $result = $this->contactcenter_model->review_script_update($update_id, $action, get_staff_user_id());

        if ($result) {
            $msg = $action === 'approved'
                ? _l('contac_script_update_approved')
                : _l('contac_script_update_rejected');
            echo json_encode(['success' => true, 'message' => $msg]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('contac_script_update_review_error')]);
        }
    }

    public function run_script_autoupdate_manual($assistant_id)
    {
        if (!has_permission('contractcenter', '', 'create')) {
            echo json_encode(['success' => false, 'message' => _l('access_denied')]);
            return;
        }

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $result = $this->contactcenter_model->run_script_autoupdate((int) $assistant_id);

        if (isset($result['success'])) {
            echo json_encode(['success' => true, 'message' => _l('contac_script_autoupdate_run_success')]);
        } else {
            $error = isset($result['error']) ? $result['error'] : _l('contac_script_autoupdate_run_error');
            echo json_encode(['success' => false, 'message' => $error]);
        }
    }

    public function assistant_edit($id)
    {
        $data["assistants"] = $this->contactcenter_model->get_assistants_ai($id);
        if (!$data["assistants"]) {
            set_alert('danger', _l("contac_aviso_sem_assistant"));
            redirect(admin_url("contactcenter/assistant_ai"));
        }
        $data['available_merge_fields'] = $this->app_merge_fields->all();
        $data['leads_status'] = $this->leads_model->get_status();
        $data["files"] = $this->contactcenter_model->get_files_assistants_ai($data["assistants"]->assist_id);
        $data["media_files"] = $this->contactcenter_model->get_assistants_media($id);
        $data["library_media"] = $this->contactcenter_model->get_library_media();
        // Get active staff for manage_conversation function
        $data['staff_members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        // Get version history
        $data["versions"] = $this->contactcenter_model->get_assistant_versions($id);
        // Onboarding link (show on load if token exists)
        $data['onboarding_url'] = '';
        if (!empty($data["assistants"]->public_form_token)) {
            $data['onboarding_url'] = rtrim(site_url(), '/') . '/contactcenter/assistant_form_public/index/' . $data["assistants"]->public_form_token;
        }
        $data['onboarding_list'] = $this->contactcenter_model->get_assistant_onboardings($id);
        $template_path = module_dir_path('contactcenter') . 'assets/templates/scheduling_flow_default.txt';
        $data['scheduling_flow_default'] = file_exists($template_path) ? file_get_contents($template_path) : '';
        $data['script_autoupdate_settings'] = $this->contactcenter_model->get_script_update_settings($id);
        $data['pending_script_updates'] = $this->contactcenter_model->get_pending_script_updates($id);
        $data['script_update_history'] = $this->contactcenter_model->get_script_update_history($id, 5);
        $this->load->view('assistant_edit', $data);
    }

    /**
     * Visual Builder for Assistant
     */
    public function assistant_visual_builder($id)
    {
        $data["assistants"] = $this->contactcenter_model->get_assistants_ai($id);
        if (!$data["assistants"]) {
            set_alert('danger', _l("contac_aviso_sem_assistant"));
            redirect(admin_url("contactcenter/assistant_ai"));
        }

        // Get visual builder data if exists
        $visual_data = $this->contactcenter_model->get_assistant_visual_data($id);
        $data["visual_data"] = $visual_data ? json_decode($visual_data, true) : null;

        // Get available functions list
        $data['available_functions'] = [
            'get_lead_info' => _l("contac_assistent_function_get_lead_info"),
            'get_lead_context' => _l("contac_assistent_function_get_lead_context"),
            'manage_conversation' => _l("contac_assistent_function_manage_conversation"),
            'update_leads' => _l("contac_assistent_function_update_leads"),
            'get_horario_agenda' => _l("contac_assistent_function_agendar"),
            'create_contract' => _l("contac_assistent_function_create_contract"),
            'get_tabela_precos' => _l("contac_assistent_function_get_tabela_precos"),
            'open_ticket' => _l("contac_assistent_function_open_ticket"),
            'get_faturas_axiom' => _l("contac_assistent_function_get_faturas"),
            'send_media' => _l("contac_assistent_function_send_media"),
            'create_group_chat' => _l("contac_assistent_function_create_group_chat"),
        ];

        // Get media files
        $data["media_files"] = $this->contactcenter_model->get_assistants_media($id);
        $data["library_media"] = $this->contactcenter_model->get_library_media();

        // Get staff members for manage_conversation
        $data['staff_members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['leads_status'] = $this->leads_model->get_status();

        $this->load->view('assistant_visual_builder', $data);
    }

    /**
     * Save visual builder data (AJAX)
     */
    public function save_assistant_visual_builder()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            // Accept both POST form data and JSON
            $assistant_id = $this->input->post('assistant_id');
            $drawflowData = $this->input->post('drawflowData');

            // If drawflowData is a string, decode it
            if (is_string($drawflowData)) {
                $drawflowData = json_decode($drawflowData, true);
            }

            // If not in POST, try JSON input
            if (!$assistant_id || !$drawflowData) {
                $jsonData = file_get_contents('php://input');
                $data = json_decode($jsonData, true);
                if ($data) {
                    $assistant_id = isset($data['assistant_id']) ? $data['assistant_id'] : null;
                    $drawflowData = isset($data['drawflowData']) ? $data['drawflowData'] : null;
                }
            }

            if ($assistant_id && $drawflowData) {
                $result = $this->contactcenter_model->save_assistant_visual_data(
                    $assistant_id,
                    is_array($drawflowData) ? json_encode($drawflowData) : $drawflowData
                );

                if ($result) {
                    $jSON["success"] = true;
                    $jSON["message"] = _l('contac_save');
                } else {
                    $jSON["success"] = false;
                    $jSON["message"] = _l('contac_save_error');
                }
            } else {
                $jSON["success"] = false;
                $jSON["message"] = "Invalid data - missing assistant_id or drawflowData";
            }
        } else {
            $jSON["success"] = false;
            $jSON["message"] = "Not an AJAX request";
        }
        echo json_encode($jSON);
    }

    /**
     * Convert visual builder to form format and save
     */
    public function save_assistant_from_visual()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $visual_data = json_decode($data['visual_data'], true);

            // Convert visual format to form format
            $form_data = $this->convert_visual_to_form($visual_data, $data);

            $response = $this->contactcenter_model->add_assistant($form_data);
            if ($response["sucesso"] == true) {
                set_alert('success', _l('contac_save'));
                redirect(admin_url('contactcenter/assistant_edit/' . $response['id']));
            } else {
                set_alert('danger', _l('contac_save_error'));
                redirect(admin_url('contactcenter/assistant_visual_builder/' . $data['id']));
            }
        } else {
            show_404();
        }
    }

    /**
     * Get assistant template (AJAX)
     */
    public function get_assistant_template()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            $template_path = module_dir_path('contactcenter') . 'assets/templates/assistant_visual_template_axiom.json';

            if (file_exists($template_path)) {
                $template_content = file_get_contents($template_path);
                $template_data = json_decode($template_content, true);

                if ($template_data && isset($template_data['drawflow'])) {
                    $jSON = $template_data['drawflow'];
                } else {
                    $jSON['error'] = 'Invalid template format';
                }
            } else {
                $jSON['error'] = 'Template file not found';
            }
        }
        echo json_encode($jSON);
    }

    /**
     * Convert visual builder data to form format
     */
    private function convert_visual_to_form($visual_data, $post_data)
    {
        $form_data = $post_data;
        $form_data['instructions'] = '';
        $form_data['functions'] = [];

        if (isset($visual_data['drawflow']['Home']['data'])) {
            $nodes = $visual_data['drawflow']['Home']['data'];

            // Collect instructions from instruction nodes
            $instructions = [];
            foreach ($nodes as $node) {
                if ($node['name'] === 'instruction') {
                    if (isset($node['data']['content'])) {
                        $instructions[] = $node['data']['content'];
                    }
                } elseif ($node['name'] === 'function') {
                    if (isset($node['data']['function_name'])) {
                        $form_data['functions'][] = $node['data']['function_name'];
                    }
                }
            }

            // Combine instructions
            $form_data['instructions'] = implode("\n\n", $instructions);
        }

        return $form_data;
    }

    public function group()
    {
        $data["devices"] = $this->contactcenter_model->get_device();
        $data["groups"] = $this->contactcenter_model->get_groups();
        $this->load->view('group', $data);
    }

    public function import_leads()
    {
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);

        $data['title'] = _l('import');
        $this->load->view('import_leads', $data);
    }

    public function add_device()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['show_messages_all_devices'] = $this->input->post('show_messages_all_devices') ? 1 : 0;
            $response = $this->contactcenter_model->add_device($data);
            if ($response["sucesso"] == true) {
                if ($data["dev_id"]) {
                    log_activity('Editou Device [' . $this->input->post('dev_name') . ']', get_staff_user_id());
                } else {
                    log_activity('Criou Device [' . $this->input->post('dev_name') . ']', get_staff_user_id());
                }
                set_alert('success', _l('contac_save'));
                redirect(admin_url('contactcenter/device'));
            } else {
                if ($response["limit"]) {
                    redirect(admin_url('contactcenter/device?device=true'));
                }
                set_alert('danger', $response["aviso"]);
                redirect(admin_url('contactcenter/device'));
            }
        } else {
            show_404();
        }
    }


    public function toggle_device_active()
    {
        if (!has_permission('contractcenter', '', 'edit')) {
            ajax_access_denied();
        }

        $device_id = $this->input->post('device_id');
        $is_active = $this->input->post('is_active');

        if (!$device_id) {
            echo json_encode([
                'success' => false,
                'message' => _l('device_id_required')
            ]);
            return;
        }

        $device = $this->contactcenter_model->get_device($device_id);
        if (!$device) {
            echo json_encode([
                'success' => false,
                'message' => _l('device_not_found')
            ]);
            return;
        }

        // Check permissions - user can only toggle their own devices unless admin
        if (!is_admin() && $device->staffid != get_staff_user_id()) {
            ajax_access_denied();
        }

        $this->db->where('dev_id', $device_id);
        $this->db->update(db_prefix() . 'contactcenter_device', ['is_active' => $is_active ? 1 : 0]);

        if ($this->db->affected_rows() > 0) {
            echo json_encode([
                'success' => true,
                'message' => $is_active ? _l('device_activated') : _l('device_deactivated')
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => _l('device_update_failed')
            ]);
        }
    }

    public function delete_device()
    {

        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $response = $this->contactcenter_model->delete_device($data["dev_id"]);
                if ($response) {
                    log_activity('Deletou Device [' . $data["dev_id"] . ']', get_staff_user_id());
                    set_alert('success', _l('contac_deleted'));
                } else {
                    set_alert('danger', _l('contac_save_error_delete'));
                }
                $jSON['redirect'] = admin_url('contactcenter/device');
            }
        }
        echo json_encode($jSON);
    }



    public function add_conversation_engine_list()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $response = $this->contactcenter_model->add_conversation_engine_list($data);
            if ($response) {
                log_activity('Criou menssagem  [' . $response . ']', get_staff_user_id());
                set_alert('success', _l('contac_save'));
                redirect(admin_url('contactcenter/conversation_list/' . $data["con_id"]));
            } else {
                set_alert('danger', _l('contac_save_error'));
                redirect(admin_url('contactcenter/conversation_list/' . $data["con_id"]));
            }
        } else {
            show_404();
        }
    }

    public function update_conversation_engine_list()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $list_id = $data["list_id"];
            unset($data["list_id"]);

            $response = $this->contactcenter_model->update_conversation_engine_list($list_id, $data);
            if ($response) {
                log_activity('Editou menssagem  [' . $list_id . ']', get_staff_user_id());
                set_alert('success', _l('contac_save'));
                redirect(admin_url('contactcenter/conversation_list/' . $data["con_id"]));
            } else {
                set_alert('danger', _l('contac_save_error'));
                redirect(admin_url('contactcenter/conversation_list/' . $data["con_id"]));
            }
        } else {
            show_404();
        }
    }

    public function migrate_backup_phone_country_code()
    {
        if (!is_admin()) {
            show_404();
            return;
        }

        $this->load->database();
        if (!$this->db->field_exists('backup_phone_country_code', db_prefix() . 'contactcenter_conversation_engine')) {
            $this->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `backup_phone_country_code` VARCHAR(10) DEFAULT '55'");
            set_alert('success', 'Column backup_phone_country_code added successfully');
        } else {
            set_alert('info', 'Column backup_phone_country_code already exists');
        }
        redirect(admin_url('contactcenter/conversation_engine'));
    }

    public function add_conversation_engine()
    {
        // Redirect GET requests to the conversation_engine page
        if (!$this->input->post()) {
            redirect(admin_url('contactcenter/conversation_engine'));
            return;
        }

        // Ensure backup_phone_country_code column exists
        if (!$this->db->field_exists('backup_phone_country_code', db_prefix() . 'contactcenter_conversation_engine')) {
            $this->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `backup_phone_country_code` VARCHAR(10) DEFAULT '55'");
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            // Handle checkbox values
            $data['is_warmup_active'] = isset($data['is_warmup_active']) ? 1 : 0;
            $data['stop_on_reply'] = isset($data['stop_on_reply']) ? 1 : 0;

            // Set default values if not provided
            if (!isset($data['daily_limit']) || empty($data['daily_limit'])) {
                $data['daily_limit'] = 1000;
            }
            if (!isset($data['batch_size']) || empty($data['batch_size'])) {
                $data['batch_size'] = 5;
            }
            if (!isset($data['batch_cooldown']) || empty($data['batch_cooldown'])) {
                $data['batch_cooldown'] = 5;
            }
            if (!isset($data['message_interval_min']) || empty($data['message_interval_min'])) {
                $data['message_interval_min'] = 1; // Default: 1 minute
            }
            if (!isset($data['message_interval_max']) || empty($data['message_interval_max'])) {
                $data['message_interval_max'] = 3; // Default: 3 minutes
            }

            // Set warmup_start_date if warmup is activated for the first time
            if ($data['is_warmup_active'] == 1 && !empty($data['con_id'])) {
                $existing = $this->contactcenter_model->get_conversation_engine($data['con_id']);
                if ($existing && empty($existing->warmup_start_date)) {
                    $data['warmup_start_date'] = date('Y-m-d H:i:s');
                }
            } elseif ($data['is_warmup_active'] == 1 && empty($data['con_id'])) {
                $data['warmup_start_date'] = date('Y-m-d H:i:s');
            }

            // Handle advanced features checkboxes
            $data['vcard_enable'] = isset($data['vcard_enable']) ? 1 : 0;
            $data['inbound_bait_enable'] = isset($data['inbound_bait_enable']) ? 1 : 0;
            $data['safe_groups_enable'] = isset($data['safe_groups_enable']) ? 1 : 0;

            // Handle date filter type
            if (!isset($data['date_filter_type']) || empty($data['date_filter_type'])) {
                $data['date_filter_type'] = 'creation_date';
            }

            // Handle birthday_field for birthday campaigns
            $birthday_field = $this->input->post('birthday_field');
            if ($data['date_filter_type'] === 'birthday') {
                $data['birthday_field'] = (!empty($birthday_field) && $birthday_field !== '0') ? (int)$birthday_field : null;
            } else {
                $data['birthday_field'] = null;
            }

            // Handle filter arrays (source, city, state)
            if (isset($data['filter_source']) && is_array($data['filter_source'])) {
                $data['filter_source'] = implode(',', $data['filter_source']);
            } else {
                $data['filter_source'] = null;
            }

            if (isset($data['filter_city']) && is_array($data['filter_city'])) {
                $data['filter_city'] = implode(',', $data['filter_city']);
            } else {
                $data['filter_city'] = null;
            }

            if (isset($data['filter_state']) && is_array($data['filter_state'])) {
                $data['filter_state'] = implode(',', $data['filter_state']);
            } else {
                $data['filter_state'] = null;
            }

            // Handle spare devices array
            if (isset($data['spare_devices']) && is_array($data['spare_devices'])) {
                // Remove primary device from spare devices if it's selected
                if (isset($data['device_id']) && in_array($data['device_id'], $data['spare_devices'])) {
                    $data['spare_devices'] = array_diff($data['spare_devices'], [$data['device_id']]);
                }
                $data['spare_devices'] = !empty($data['spare_devices']) ? implode(',', $data['spare_devices']) : null;
            } else {
                $data['spare_devices'] = null;
            }

            // Handle backup_phone_field - ensure it's saved even if empty
            // Selectpicker might not send the field if empty, so we check POST directly
            $backup_phone_field = $this->input->post('backup_phone_field');
            if (empty($backup_phone_field) || $backup_phone_field === '' || $backup_phone_field === '0') {
                $data['backup_phone_field'] = null;
            } else {
                $data['backup_phone_field'] = (int)$backup_phone_field; // Ensure it's an integer
            }

            // Handle backup_phone_country_code - default to Brazil (55) if not set
            $backup_phone_country_code = $this->input->post('backup_phone_country_code');
            if (empty($backup_phone_country_code)) {
                $data['backup_phone_country_code'] = '55'; // Default to Brazil
            } else {
                $data['backup_phone_country_code'] = preg_replace('/[^0-9]/', '', $backup_phone_country_code); // Ensure only digits
            }

            // Log for debugging
            log_message('debug', 'Backup phone field POST value: ' . var_export($backup_phone_field, true));
            log_message('debug', 'Backup phone field data value: ' . var_export($data['backup_phone_field'], true));
            log_message('debug', 'Backup phone country code: ' . var_export($data['backup_phone_country_code'], true));

            // Initialize device rotation index
            if (!isset($data['device_rotation_index'])) {
                $data['device_rotation_index'] = 0;
            }

            $response = $this->contactcenter_model->add_conversation_engine($data);
            if ($response == true) {
                if ($data["con_id"]) {
                    log_activity('Editou conversation engine [' . $this->input->post('con_title') . ']', get_staff_user_id());
                    set_alert('success', _l('contac_save'));
                } else {
                    log_activity('Criou conversation engine [' . $this->input->post('con_title') . ']', get_staff_user_id());
                    set_alert('success', _l('contac_save'));
                }
                redirect(admin_url('contactcenter/conversation_engine'));
            } else {
                set_alert('danger', _l('contac_save_error'));
                redirect(admin_url('contactcenter/conversation_engine'));
            }
        } else {
            show_404();
        }
    }

    public function add_conversation_engine_template()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            return;
        }

        $config   = $this->input->post('config');
        $messages = $this->input->post('messages');

        if (empty($config) || empty($messages)) {
            echo json_encode(['success' => false, 'error' => 'Missing config or messages']);
            return;
        }

        $data = [
            'con_id'               => '',
            'con_title'            => isset($config['con_title']) ? $config['con_title'] : 'Campaign Template',
            'leads_status'         => isset($config['leads_status']) ? $config['leads_status'] : 0,
            'leads_status_final'   => isset($config['leads_status_final']) ? $config['leads_status_final'] : 0,
            'date_filter_type'     => isset($config['date_filter_type']) ? $config['date_filter_type'] : 'creation_date',
            'birthday_field'       => isset($config['birthday_field']) && !empty($config['birthday_field']) ? (int)$config['birthday_field'] : null,
            'start_time'           => isset($config['start_time']) ? $config['start_time'] : '08:00',
            'end_time'             => isset($config['end_time']) ? $config['end_time'] : '18:00',
            'leads_day'            => isset($config['leads_day']) ? (int)$config['leads_day'] : 50,
            'daily_limit'          => isset($config['daily_limit']) ? (int)$config['daily_limit'] : 200,
            'batch_size'           => isset($config['batch_size']) ? (int)$config['batch_size'] : 5,
            'batch_cooldown'       => isset($config['batch_cooldown']) ? (int)$config['batch_cooldown'] : 5,
            'message_interval_min' => isset($config['message_interval_min']) ? (int)$config['message_interval_min'] : 2,
            'message_interval_max' => isset($config['message_interval_max']) ? (int)$config['message_interval_max'] : 5,
            'stop_on_reply'        => isset($config['stop_on_reply']) ? (int)$config['stop_on_reply'] : 1,
            'con_status'           => 0,
            'tags'                 => [],
        ];

        $data['con_date'] = date('Y-m-d H:i:s');
        $data['staffid']  = get_staff_user_id();
        $data['tags']     = '';

        $this->db->insert(db_prefix() . 'contactcenter_conversation_engine', $data);
        $con_id = $this->db->insert_id();

        if (!$con_id) {
            echo json_encode(['success' => false, 'error' => 'Failed to create campaign']);
            return;
        }

        $order = 1;
        foreach ($messages as $msg_text) {
            if (empty(trim($msg_text))) continue;
            $this->db->insert(db_prefix() . 'contactcenter_conversation_engine_list', [
                'con_id'     => $con_id,
                'list_text'  => $msg_text,
                'list_ordem' => $order,
            ]);
            $order++;
        }

        log_activity('Created campaign from template [' . $data['con_title'] . '] ID: ' . $con_id, get_staff_user_id());

        echo json_encode(['success' => true, 'con_id' => $con_id]);
    }

    public function edit_device()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->get_device($data["id"]);
                if ($device) {
                    $jSON['dev_id'] = $device->dev_id;
                    $jSON['staffid'] = $device->staffid;
                    $jSON['dev_name'] = $device->dev_name;
                    $jSON['dev_number'] = $device->dev_number;
                    $jSON['dev_type'] = $device->dev_type;
                    $jSON['dev_token'] = $device->dev_token;
                    $jSON['dev_voz_id'] = $device->dev_voz_id;
                    $jSON['dev_openai'] = $device->dev_openai;
                    $jSON['assistant_ai_id'] = $device->assistant_ai_id;
                    $jSON['dev_engine'] = $device->dev_engine;
                    $jSON['api_type'] = $device->api_type;
                    $jSON['dev_instance_name'] = $device->dev_instance_name;
                    $jSON['chatbot_id'] = $device->chatbot_id;
                    $jSON['server_id'] = $device->server_id;
                    $jSON['contract_template'] = $device->contract_template;
                    $jSON['contract_category'] = $device->contract_category;
                    $jSON['contract_msg'] = $device->contract_msg;
                    $jSON['api_local'] = $device->api_local;
                    $jSON['sales_knowledge'] = $device->sales_knowledge ?? '';
                    $jSON['show_messages_all_devices'] = isset($device->show_messages_all_devices) ? $device->show_messages_all_devices : 0;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_mass_edit_engine()
    {
        $jSON = array('success' => false, 'message' => '');

        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $ids = $this->input->post('ids');
                $update_data = array();

                // Get all fields that can be updated
                $fields = array('device_id', 'start_time', 'end_time', 'leads_day', 'leads_status', 'leads_status_final', 'daily_limit', 'batch_size');

                foreach ($fields as $field) {
                    $value = $this->input->post($field);
                    if ($value !== null && $value !== '') {
                        $update_data[$field] = $value;
                    }
                }

                // Ensure IDs are integers
                if (is_array($ids)) {
                    $ids = array_map('intval', $ids);
                    $ids = array_filter($ids); // Remove any invalid IDs
                    $ids = array_values($ids); // Re-index array
                }

                if (empty($ids) || !is_array($ids) || count($ids) == 0) {
                    $jSON['message'] = _l('no_campaigns_selected');
                    echo json_encode($jSON);
                    return;
                }

                if (empty($update_data)) {
                    $jSON['message'] = _l('mass_edit_no_fields');
                    echo json_encode($jSON);
                    return;
                }

                $success_count = 0;
                $failed_count = 0;

                foreach ($ids as $id) {
                    $id = intval($id);
                    if ($id <= 0) {
                        $failed_count++;
                        continue;
                    }

                    // Update the engine
                    $this->db->where('con_id', $id);
                    if ($this->db->update(db_prefix() . 'contactcenter_conversation_engine', $update_data)) {
                        $success_count++;
                    } else {
                        $failed_count++;
                    }
                }

                if ($success_count > 0) {
                    $jSON['success'] = true;
                    $jSON['message'] = sprintf(_l('mass_edit_success_message'), $success_count, count($ids));
                    log_activity('Mass edited ' . $success_count . ' conversation engines', get_staff_user_id());
                } else {
                    $jSON['message'] = _l('mass_edit_error');
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_mass_edit_leads_engine()
    {
        $jSON = array('success' => false, 'message' => '');

        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $ids = $this->input->post('ids');
                $update_data = array();

                // Get all fields that can be updated
                $fields = array('device_id', 'start_time', 'end_time', 'hours_since_last_contact', 'leads_status', 'leads_status_final', 'fromMe', 'daily_limit', 'batch_size');

                foreach ($fields as $field) {
                    $value = $this->input->post($field);
                    if ($value !== null && $value !== '') {
                        $update_data[$field] = $value;
                    }
                }

                // Handle tags separately (convert array to comma-separated string)
                $tags = $this->input->post('tags');
                if ($tags !== null && $tags !== '') {
                    if (is_array($tags)) {
                        $update_data['tags'] = implode(",", $tags);
                    } else {
                        $update_data['tags'] = $tags;
                    }
                }

                // Ensure IDs are integers
                if (is_array($ids)) {
                    $ids = array_map('intval', $ids);
                    $ids = array_filter($ids); // Remove any invalid IDs
                    $ids = array_values($ids); // Re-index array
                }

                if (empty($ids) || !is_array($ids) || count($ids) == 0) {
                    $jSON['message'] = _l('no_campaigns_selected');
                    echo json_encode($jSON);
                    return;
                }

                if (empty($update_data)) {
                    $jSON['message'] = _l('mass_edit_no_fields');
                    echo json_encode($jSON);
                    return;
                }

                $success_count = 0;
                $failed_count = 0;

                foreach ($ids as $id) {
                    $id = intval($id);
                    if ($id <= 0) {
                        $failed_count++;
                        continue;
                    }

                    // Update the leads engine
                    $this->db->where('id', $id);
                    if ($this->db->update(db_prefix() . 'contactcenter_leads_engine', $update_data)) {
                        $success_count++;
                    } else {
                        $failed_count++;
                    }
                }

                if ($success_count > 0) {
                    $jSON['success'] = true;
                    $jSON['message'] = sprintf(_l('mass_edit_success_message'), $success_count, count($ids));
                    log_activity('Mass edited ' . $success_count . ' leads engines', get_staff_user_id());
                } else {
                    $jSON['message'] = _l('mass_edit_error');
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_conversation_list()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->delete_conversation_list($data["id"]);
                if ($device) {
                    $jSON["result"] = true;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_get_media_evolution()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->get_media_evolution($data["id"]);
                if ($result) {
                    $jSON["base64"] = $result["base64"];
                    $jSON["type"] = $result["type"];
                    $jSON["thumb"] = $result["thumb"];
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_send_audio()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $result = $this->contactcenter_model->ajax_send_audio($data);
            if ($result) {
                $jSON["result"] = true;
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_conversation_list_order()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->ajax_conversation_list_order($data);
                if ($device) {
                    $jSON["result"] = true;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_engine()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->delete_engine($data);
                if ($device) {
                    $jSON["result"] = true;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_engine_status()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->update_status_engine($data);
                if ($device) {
                    $jSON["result"] = true;
                } else {
                    $jSON["result"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_bulk_engine_status()
    {
        $jSON = array('success' => false, 'message' => '');

        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $ids = $this->input->post('ids');
                $status = intval($this->input->post('status'));

                // Handle 'all' campaigns
                if ($ids === 'all' || (is_array($ids) && count($ids) == 1 && $ids[0] === 'all')) {
                    // Get all conversation engines
                    if (!is_admin()) {
                        $staffid = get_staff_user_id();
                    } else {
                        $staffid = '';
                    }
                    $all_engines = $this->contactcenter_model->get_conversation_engine('', $staffid);
                    $ids = array();
                    if ($all_engines) {
                        if (is_array($all_engines)) {
                            foreach ($all_engines as $engine) {
                                if (isset($engine->con_id) && $engine->con_id > 0) {
                                    $ids[] = intval($engine->con_id);
                                }
                            }
                        } elseif (is_object($all_engines) && isset($all_engines->con_id)) {
                            // Single result
                            $ids[] = intval($all_engines->con_id);
                        }
                    }
                }

                // Ensure IDs are integers
                if (is_array($ids)) {
                    $ids = array_map('intval', $ids);
                    $ids = array_filter($ids); // Remove any invalid IDs
                    $ids = array_values($ids); // Re-index array
                }

                if (empty($ids) || !is_array($ids) || count($ids) == 0) {
                    $jSON['message'] = _l('no_campaigns_selected');
                    echo json_encode($jSON);
                    return;
                }

                $success_count = 0;
                $failed_count = 0;
                $failed_ids = array();

                foreach ($ids as $id) {
                    $id = intval($id);
                    if ($id <= 0) {
                        $failed_count++;
                        continue;
                    }

                    // Get current status - reset query first
                    $this->db->reset_query();
                    $this->db->where('con_id', $id);
                    $current = $this->db->get(db_prefix() . 'contactcenter_conversation_engine')->row();

                    if (!$current) {
                        $failed_count++;
                        continue;
                    }

                    $current_status = intval($current->con_status);

                    // If already in desired state, count as success
                    if ($current_status == $status) {
                        $success_count++;
                        continue;
                    }

                    // For starting campaigns, check if messages exist
                    if ($status == 1) {
                        $data = array(
                            'id' => $id,
                            'con_status' => $status
                        );
                        $result = $this->contactcenter_model->update_status_engine($data);
                        if ($result === true) {
                            $success_count++;
                        } else {
                            $failed_count++;
                            $failed_ids[] = $id;
                        }
                    } else {
                        // For stopping, we can always do it - no validation needed
                        $this->db->reset_query();
                        $this->db->where('con_id', $id);
                        $this->db->update(db_prefix() . 'contactcenter_conversation_engine', array('con_status' => 0));

                        // Always count as success when stopping (no validation required)
                        // affected_rows might be 0 if already stopped, but that's still success
                        $success_count++;
                    }
                }

                if ($success_count > 0 || count($ids) > 0) {
                    $jSON['success'] = true;
                    if ($status == 1) {
                        $jSON['message'] = _l('campaigns_started_successfully');
                    } else {
                        $jSON['message'] = _l('campaigns_stopped_successfully');
                    }
                } else {
                    $jSON['message'] = _l('no_campaigns_updated');
                }
            }
        }
        echo json_encode($jSON);
    }

    public function get_qrcode()
    {

        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->qrcode_device($data);
                if ($result) {
                    if (isset($result['error']) && $result['error'] === false) {
                        $jSON['qrcode'] = ($result["response"]["qrcode"]["base64"] ? $result["response"]["qrcode"]["base64"] : $result["response"]["base64"]);
                        $jSON['pairingCode'] = ($result["response"]["qrcode"]["pairingCode"] ? $result["response"]["qrcode"]["pairingCode"] : $result["response"]["pairingCode"]);
                        $jSON['message'] = $result["message"];
                        $jSON['status'] = $result["device"]["status"];
                    } else {
                        $jSON['status'] = $result["instance"]["status"];
                        $jSON['message'] = $result["message"];
                    }
                }
            }
        }
        echo json_encode($jSON);
    }

    public function get_status_connection()
    {

        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->getConnectionStatus($data["id"]);
                if ($result["response"]["status"] == "CONNECTED" || $result["response"]["status"] == "inChat") {
                    $jSON['status'] = $result["response"]["status"];
                    $jSON["redirect"] = true;
                } else {
                    $jSON['status'] = $result["response"]["status"];
                }
            }
        }
        echo json_encode($jSON);
    }

    /**
     * Reconnect a device - changes server sequentially and generates QR code
     */
    public function reconnect_device()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }

        $device_id = $this->input->post('device_id');

        if (!$device_id) {
            echo json_encode([
                'success' => false,
                'message' => _l('device_id_required')
            ]);
            return;
        }

        // Get device
        $device = $this->contactcenter_model->get_device($device_id);

        if (!$device) {
            echo json_encode([
                'success' => false,
                'message' => _l('device_not_found')
            ]);
            return;
        }

        // Check permissions - user can only reconnect their own devices unless admin
        if (!is_admin() && $device->staffid != get_staff_user_id()) {
            ajax_access_denied();
        }

        // Restart device with sequential server change
        $restart_result = $this->contactcenter_model->restart_device_sequential(['id' => $device_id]);

        if (!$restart_result) {
            echo json_encode([
                'success' => false,
                'message' => _l('device_reconnect_failed')
            ]);
            return;
        }

        // Get updated device with new server
        $device = $this->contactcenter_model->get_device($device_id);

        // Wait a moment for the instance to be ready after sequential restart
        sleep(2);

        // Use the same approach as get_qrcode endpoint (which is used by qrcode_single page)
        // This ensures consistency with the working qrcode_single page
        $qrcode_result = $this->contactcenter_model->qrcode_device(['id' => $device_id]);

        if ($qrcode_result && isset($qrcode_result['error']) && $qrcode_result['error'] === false) {
            // Extract QR code and pairing code - handle both response formats
            $qrcode_base64 = null;
            $pairing_code = null;

            if (isset($qrcode_result["response"]["qrcode"]["base64"])) {
                $qrcode_base64 = $qrcode_result["response"]["qrcode"]["base64"];
            } elseif (isset($qrcode_result["response"]["base64"])) {
                $qrcode_base64 = $qrcode_result["response"]["base64"];
            }

            if (isset($qrcode_result["response"]["qrcode"]["pairingCode"])) {
                $pairing_code = $qrcode_result["response"]["qrcode"]["pairingCode"];
            } elseif (isset($qrcode_result["response"]["pairingCode"])) {
                $pairing_code = $qrcode_result["response"]["pairingCode"];
            }

            if ($qrcode_base64) {
                echo json_encode([
                    'success' => true,
                    'message' => isset($qrcode_result['message']) ? $qrcode_result['message'] : _l('device_reconnected'),
                    'device_id' => $device_id,
                    'qrcode' => $qrcode_base64,
                    'pairingCode' => $pairing_code,
                    'status' => isset($qrcode_result["device"]["status"]) ? $qrcode_result["device"]["status"] : (isset($qrcode_result["instance"]["status"]) ? $qrcode_result["instance"]["status"] : 'close')
                ]);
            } else {
                // QR code not available yet - return error
                echo json_encode([
                    'success' => false,
                    'message' => isset($qrcode_result['message']) ? $qrcode_result['message'] : _l('qrcode_not_available')
                ]);
            }
        } else {
            // Try one more time after a short delay
            sleep(1);
            $retry_result = $this->contactcenter_model->qrcode_device(['id' => $device_id]);

            if ($retry_result && isset($retry_result['error']) && $retry_result['error'] === false) {
                $qrcode_base64 = null;
                $pairing_code = null;

                if (isset($retry_result["response"]["qrcode"]["base64"])) {
                    $qrcode_base64 = $retry_result["response"]["qrcode"]["base64"];
                } elseif (isset($retry_result["response"]["base64"])) {
                    $qrcode_base64 = $retry_result["response"]["base64"];
                }

                if (isset($retry_result["response"]["qrcode"]["pairingCode"])) {
                    $pairing_code = $retry_result["response"]["qrcode"]["pairingCode"];
                } elseif (isset($retry_result["response"]["pairingCode"])) {
                    $pairing_code = $retry_result["response"]["pairingCode"];
                }

                if ($qrcode_base64) {
                    echo json_encode([
                        'success' => true,
                        'message' => isset($retry_result['message']) ? $retry_result['message'] : _l('device_reconnected'),
                        'device_id' => $device_id,
                        'qrcode' => $qrcode_base64,
                        'pairingCode' => $pairing_code,
                        'status' => isset($retry_result["device"]["status"]) ? $retry_result["device"]["status"] : (isset($retry_result["instance"]["status"]) ? $retry_result["instance"]["status"] : 'close')
                    ]);
                    return;
                }
            }

            // Return error with message from result if available
            $error_message = _l('device_reconnect_failed');
            if (isset($qrcode_result['message'])) {
                $error_message = $qrcode_result['message'];
            } elseif (isset($retry_result['message'])) {
                $error_message = $retry_result['message'];
            }

            echo json_encode([
                'success' => false,
                'message' => $error_message
            ]);
        }
    }

    /**
     * Get device status for header widget
     */
    public function get_device_status_header()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->load->helper('contactcenter/contactcenter');

        if (function_exists('get_disconnected_devices')) {
            $disconnected_devices = get_disconnected_devices(null, is_admin());
            $my_disconnected = get_disconnected_devices(get_staff_user_id(), false);
            $total_disconnected = is_array($disconnected_devices) ? count($disconnected_devices) : 0;
            $my_disconnected_count = is_array($my_disconnected) ? count($my_disconnected) : 0;

            echo json_encode([
                'success' => true,
                'data' => [
                    'totalDisconnected' => $total_disconnected,
                    'myDisconnected' => $my_disconnected_count,
                    'devices' => $disconnected_devices,
                    'myDevices' => $my_disconnected
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Helper function not available'
            ]);
        }
    }

    public function get_status_connection_device()
    {

        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->get_status_connection_device($data["id"]);
                if ($result) {
                    $jSON['status'] = true;
                } else {
                    $jSON['status'] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function desconnect_device()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->get_device($data["id"]);
                $result = $this->contactcenter_model->disconnect_device($device);
                if ($result["error"] == true) {
                    set_alert('danger', $result["message"]);
                } else {
                    set_alert('success', $result["message"]);
                }
                $jSON["redirect"] = true;
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_send_msg()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $Send = $this->contactcenter_model->send_chat($data);
                if ($Send) {
                    $jSON["send"] = true;
                } else {
                    $jSON["send"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function chatall()
    {

        if (is_admin()) {
            // Only show active devices in chatall
            $data["device"] = $this->contactcenter_model->get_device(null, null, true);
        } else {
            // get_departments_staff_device now filters by is_active = 1
            $data["device"] = $this->contactcenter_model->get_departments_staff_device();
        }

        $data["title"] = "Todos os chats";
        $this->load->view('chatall', $data);
    }

    public function chatsingle($chatid)
    {

        $device = $this->contactcenter_model->get_device($chatid);
        $server = $this->contactcenter_model->get_server($device->server_id);
        $dadoServe = [
            "server" => $server,
            "device" => $device
        ];
        $data["dadoServe"] = json_encode($dadoServe);
        $data["device_server"] = $server->version;

        $deviceDepartment = $this->contactcenter_model->get_staff_members($device->staffid);
        $department = $this->contactcenter_model->get_staff_members(get_staff_user_id());

        // Verifica se há interseção entre os arrays
        $commonDepartments = array_intersect($deviceDepartment, $department);

        $libera = false;
        if ($device->dev_type == 3 && empty($commonDepartments) && !has_permission('contactcenter', '', 'chat_viwer_all')) {
            $libera = false;
        } elseif ($device->dev_type == 3 && empty($commonDepartments) && has_permission('contactcenter', '', 'chat_viwer_all')) {
            $libera = true;
        } elseif ($device->dev_type == 3 && !empty($commonDepartments)) {
            $libera = true;
        } else {
            if (!has_permission('contactcenter', '', 'chat_viwer_all') && get_staff_user_id() != $device->staffid) {
                set_alert('danger', _l("contac_aviso_sem_acesso_departamento"));
                redirect(admin_url("contactcenter/chatall"));
            }
            $libera = true;
        }

        if (!$device) {
            set_alert('danger', _l("contac_aviso_sem_device"));
            redirect(admin_url());
        }

        if (!$libera) {
            set_alert('danger', _l("contac_aviso_sem_acesso_departamento"));
            redirect(admin_url("contactcenter/chatall"));
        }

        $data["hook_number"] = $this->input->get("number");
        $data["theme"] = $this->session->userdata("contactcenter_themes_change");

        //$data["contact_transfer"] = $this->contactcenter_model->get_transfer_leads($device->staffid);
        $data['statuses'] = $this->leads_model->get_status();
        $data['members'] = $this->staff_model->get();
        $data["bearer"] = get_option("tokenBearer_contactcenter");
        $data["channelName"] = get_option("tokenprofileid_contactcenter");
        $data["device"] = $device;
        $data['members'] = $this->staff_model->get();

        // Load data for device modal (only needed if admin, but load it anyway for simplicity)
        $data['drawflow'] = $this->chatbot->get_automation_active();
        $data['assistants'] = $this->contactcenter_model->get_assistants_ai();
        $data["servers"] = $this->contactcenter_model->get_server();
        $data["models_contract"] = $this->contactcenter_model->get_templates_contract();
        $data["category_contract"] = $this->contactcenter_model->get_contract_type_id();

        $chat_marked_read_filter = $this->input->get('chat_marked_read');
        $chat_sort_filter = $this->input->get('chat_sort');
        $staffid = $device->staffid ? $device->staffid : 0;
        // When staffid is 0 (local env, department device, api_local), pass device token to fetch by session
        $device_token_for_fetch = ($staffid ? null : $device->dev_token);
        $data["LabelContacts"] = $this->contactcenter_model->get_contact_chat($staffid, "", null, null, null, $chat_marked_read_filter !== null && $chat_marked_read_filter !== "" ? (int)$chat_marked_read_filter : null, $chat_sort_filter ? $chat_sort_filter : "newest_first", false, $device_token_for_fetch);
        // Local env fallback: if no contacts by staffid, try by device token (contacts may have session only)
        if (empty($data["LabelContacts"]) && $staffid && $device->dev_token && isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false)) {
            $data["LabelContacts"] = $this->contactcenter_model->get_contact_chat(0, "", null, null, null, $chat_marked_read_filter !== null && $chat_marked_read_filter !== "" ? (int)$chat_marked_read_filter : null, $chat_sort_filter ? $chat_sort_filter : "newest_first", false, $device->dev_token);
        }
        // $data["LabelContacts"] = $this->contactcenter_model->get_contact_chat_open(($device->staffid ? $device->staffid : 0));
        $data["devicetoken"] = $device->dev_token;
        $data["chat_marked_read_filter"] = $chat_marked_read_filter;

        // Birthday custom fields for lead header (date_picker type)
        $this->load->helper('custom_fields');
        $data['birthday_custom_fields'] = get_custom_fields('leads', ['type' => 'date_picker']);

        // Load Omni Pilot wizard data - check for ACTIVE sessions only
        $data['has_active_omni_pilot'] = false;
        if ($this->db->table_exists(db_prefix() . 'contactcenter_omni_pilot_sessions')) {
            // Check if there's an active Omni Pilot session for this device
            $active_session = $this->contactcenter_model->get_active_omni_pilot_session($device->dev_id);
            $data['has_active_omni_pilot'] = !empty($active_session);
        }

        $this->load->view('chat_single', $data);
    }

    public function chattransfer()
    {
        if (is_admin()) {
            $data["contact_transfer"] = $this->contactcenter_model->get_transfer_leads("", 1);
            $data["contact_accepted"] = $this->contactcenter_model->get_transfer_leads("", 0);
        } else {
            $data["contact_transfer"] = $this->contactcenter_model->get_transfer_leads(get_staff_user_id(), 1);
            $data["contact_accepted"] = $this->contactcenter_model->get_transfer_leads(get_staff_user_id());
        }

        $data['members'] = $this->staff_model->get();
        $this->load->view('chattransfer', $data);
    }



    public function ajax_get_all_group()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->get_group_chat($data["token"]);
                //   print_r($result);
                if ($result) {
                    $jSON["retorno"] = monta_html_all_group($result);
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_insert_msg()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->insert_webhook($data["id"]);
                if ($result) {
                    $jSON["insert"] = true;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_get_messages_chat()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();

                $active_leads_funnel = $this->app_modules->is_inactive("leads_funnel");
                if (!$active_leads_funnel) {
                    $progress = get_status_leads_chat($data["phonenumber"], $data["token"]);
                    $jSON["progress"] = $progress;
                }

                $isGroup = isset($data["group"]) ? intval($data["group"]) : 0;

                // For groups, always fetch group name from API when opening chat
                if ($isGroup == 1) {
                    $group_id = $data["phonenumber"]; // For groups, phonenumber is the group_api_id
                    $group_name = null;
                    $fetched_group_picture = null; // Initialize for scope

                    log_activity("ajax_get_messages_chat GROUP CHAT OPENED - Group ID: {$group_id}, Token: " . ($data["token"] ?? 'null') . ", All POST data: " . json_encode($data), get_staff_user_id());

                    // First, try to get from database as fallback
                    $this->db->where('group_api_id', $group_id);
                    $group = $this->db->get(db_prefix() . 'contactcenter_group_api')->row();

                    if ($group) {
                        log_activity("ajax_get_messages_chat GROUP_API RECORD FOUND - Device ID: " . ($group->device_id ?? 'null'), get_staff_user_id());
                    } else {
                        log_activity("ajax_get_messages_chat GROUP_API RECORD NOT FOUND", get_staff_user_id());
                    }

                    if ($group && !empty($group->group_api_name)) {
                        $group_name = $group->group_api_name;
                        log_activity("ajax_get_messages_chat GROUP NAME FROM DB - Group ID: {$group_id}, Name: {$group_name}", get_staff_user_id());

                        // Update contact name in contact table with the stored group name
                        // Update ALL contact records for this group (not just matching token, since token might be empty)
                        $this->db->where('phonenumber', $group_id);
                        $this->db->where('isGroup', 1);
                        // Don't limit to specific session since token might be empty
                        $this->db->update(db_prefix() . 'contactcenter_contact', [
                            'name' => $group_name
                        ]);
                    }

                    // Try to get device - first from token, then from contact record
                    $device_obj = null;

                    // Method 1: Try from token
                    if (!empty($data["token"])) {
                        $device_obj = $this->contactcenter_model->get_device_token($data["token"]);
                        log_activity("ajax_get_messages_chat DEVICE FROM TOKEN - Token: {$data["token"]}, Device: " . ($device_obj ? 'found' : 'not found'), get_staff_user_id());
                    }

                    // Method 2: Get device from group_api record (this should work!)
                    if (!$device_obj && $group && !empty($group->device_id)) {
                        $device_obj = $this->contactcenter_model->get_device($group->device_id);
                        log_activity("ajax_get_messages_chat DEVICE FROM GROUP_API - Device ID: {$group->device_id}, Device: " . ($device_obj ? 'found' : 'not found'), get_staff_user_id());
                    }

                    // Method 3: Get device from contact record if previous methods failed
                    if (!$device_obj) {
                        $this->db->where('phonenumber', $group_id);
                        $this->db->where('isGroup', 1);
                        $this->db->limit(1);
                        $contact = $this->db->get(db_prefix() . 'contactcenter_contact')->row();

                        if ($contact && !empty($contact->session)) {
                            $device_obj = $this->contactcenter_model->get_device_token($contact->session);
                            log_activity("ajax_get_messages_chat DEVICE FROM CONTACT - Session: {$contact->session}, Device: " . ($device_obj ? 'found' : 'not found'), get_staff_user_id());
                        } else {
                            log_activity("ajax_get_messages_chat CONTACT NOT FOUND - Group ID: {$group_id}", get_staff_user_id());
                        }
                    }

                    // Method 4: Try to get any device for the current staff (last resort)
                    if (!$device_obj) {
                        $staff_id = get_staff_user_id();
                        if ($staff_id) {
                            $devices = $this->contactcenter_model->get_device(null, $staff_id);
                            if ($devices && is_array($devices) && !empty($devices)) {
                                // Use first device found for this staff
                                $device_obj = $devices[0];
                                log_activity("ajax_get_messages_chat DEVICE FROM STAFF - Staff ID: {$staff_id}, Device ID: " . ($device_obj->dev_id ?? 'null'), get_staff_user_id());
                            } elseif ($devices && !is_array($devices)) {
                                $device_obj = $devices;
                                log_activity("ajax_get_messages_chat DEVICE FROM STAFF (single) - Staff ID: {$staff_id}, Device ID: " . ($device_obj->dev_id ?? 'null'), get_staff_user_id());
                            }
                        }
                    }

                    if ($device_obj) {
                        // Log all device fields for debugging
                        $device_fields = get_object_vars($device_obj);
                        log_activity("ajax_get_messages_chat DEVICE OBJECT - " . json_encode($device_fields), get_staff_user_id());

                        $dev_id = isset($device_obj->dev_id) ? $device_obj->dev_id : 'not_set';
                        $instance = isset($device_obj->dev_instance_name) ? $device_obj->dev_instance_name : 'not_set';
                        $server_id = isset($device_obj->server_id) ? $device_obj->server_id : 'not_set';
                        log_activity("ajax_get_messages_chat DEVICE FOUND - Device ID: {$dev_id}, Instance: {$instance}, Server ID: {$server_id}", get_staff_user_id());

                        // Get server information
                        $server = null;
                        if (!empty($device_obj->server_id)) {
                            $server = $this->contactcenter_model->get_server($device_obj->server_id);
                            if ($server) {
                                $server_api_type = isset($server->api_type) ? $server->api_type : 'not_set';
                                $server_url = isset($server->url) ? $server->url : 'not_set';
                                log_activity("ajax_get_messages_chat SERVER FOUND - Server ID: {$device_obj->server_id}, API Type: {$server_api_type}, URL: {$server_url}", get_staff_user_id());
                            } else {
                                log_activity("ajax_get_messages_chat SERVER NOT FOUND - Server ID: {$device_obj->server_id} not found in database", get_staff_user_id());
                            }
                        } else {
                            log_activity("ajax_get_messages_chat SERVER ID MISSING - Device has no server_id", get_staff_user_id());
                        }

                        if ($server && isset($server->api_type) && $server->api_type == "axiom_evolution") {
                            log_activity("ajax_get_messages_chat SERVER OK FOR API CALL - Server ID: {$device_obj->server_id}, API Type: {$server->api_type}, URL: {$server->url}", get_staff_user_id());

                            // Load library
                            $this->load->library('axiom_evolution');

                            log_activity("ajax_get_messages_chat FETCHING FROM API - Group ID: {$group_id}, Instance: {$device_obj->dev_instance_name}", get_staff_user_id());

                            // Fetch group info from API
                            $group_info = $this->axiom_evolution->get_group_info_evolution(
                                $server->url,
                                $device_obj->dev_instance_name,
                                $device_obj->dev_token,
                                $group_id
                            );

                            log_activity("ajax_get_messages_chat API RESPONSE - Group ID: {$group_id}, Response: " . json_encode($group_info), get_staff_user_id());

                            // Check for group name in response
                            $fetched_group_name = null;
                            if (!empty($group_info['subject'])) {
                                $fetched_group_name = $group_info['subject'];
                            } elseif (!empty($group_info['name'])) {
                                $fetched_group_name = $group_info['name'];
                            }

                            // Check for group picture in response
                            $fetched_group_picture = null;
                            if (!empty($group_info['pictureUrl'])) {
                                $fetched_group_picture = $group_info['pictureUrl'];
                            } elseif (!empty($group_info['picture'])) {
                                $fetched_group_picture = $group_info['picture'];
                            }

                            if ($fetched_group_name) {
                                $group_name = $fetched_group_name; // Use API name (most up-to-date)

                                log_activity("ajax_get_messages_chat GROUP NAME FETCHED - Group ID: {$group_id}, Name: {$group_name}", get_staff_user_id());

                                // Prepare update data
                                $update_data = [
                                    'name' => $group_name
                                ];

                                // Add picture if available
                                if ($fetched_group_picture) {
                                    $update_data['thumb'] = $fetched_group_picture;
                                    log_activity("ajax_get_messages_chat GROUP PICTURE FETCHED - Group ID: {$group_id}, Picture URL: {$fetched_group_picture}", get_staff_user_id());
                                }

                                // Update contactcenter_contact table with the group name and picture
                                // Update ALL contact records for this group (groups can appear in multiple sessions)
                                $this->db->where('phonenumber', $group_id);
                                $this->db->where('isGroup', 1);
                                $update_result = $this->db->update(db_prefix() . 'contactcenter_contact', $update_data);

                                log_activity("ajax_get_messages_chat CONTACT UPDATED (all sessions) - Group ID: {$group_id}, Update result: " . ($update_result ? 'success' : 'failed') . ", Rows affected: " . $this->db->affected_rows() . ", Picture: " . ($fetched_group_picture ? 'updated' : 'not found'), get_staff_user_id());

                                if ($group) {
                                    // Update existing record
                                    $this->db->where('group_api_id', $group_id);
                                    $this->db->update(db_prefix() . 'contactcenter_group_api', [
                                        'group_api_name' => $group_name
                                    ]);
                                } else {
                                    // Insert new record
                                    $group_data = [
                                        'group_api_id' => $group_id,
                                        'group_api_name' => $group_name,
                                        'device_id' => $device_obj->dev_id,
                                        'numbers' => ''
                                    ];
                                    if ($this->db->field_exists('date_created', db_prefix() . 'contactcenter_group_api')) {
                                        $group_data['date_created'] = date('Y-m-d H:i:s');
                                    }
                                    $this->db->insert(db_prefix() . 'contactcenter_group_api', $group_data);
                                }
                            } else {
                                log_activity("ajax_get_messages_chat GROUP NAME NOT IN API RESPONSE - Group ID: {$group_id}, Response keys: " . implode(', ', array_keys($group_info ?: [])), get_staff_user_id());
                            }
                        } else {
                            log_activity("ajax_get_messages_chat SERVER ISSUE - Server: " . ($server ? $server->api_type : 'null'), get_staff_user_id());
                        }
                    } else {
                        log_activity("ajax_get_messages_chat DEVICE NOT FOUND - Token: " . ($data["token"] ?? 'null'), get_staff_user_id());
                    }

                    $jSON["id"] = 0; // Groups don't have lead IDs

                    // Use group name from DB or API, fallback to phonenumber
                    if (empty($group_name)) {
                        // Try to get from contact record
                        $this->db->where('phonenumber', $group_id);
                        $this->db->where('isGroup', 1);
                        if (!empty($data["token"])) {
                            $this->db->where('session', $data["token"]);
                        }
                        $this->db->limit(1);
                        $contact_check = $this->db->get(db_prefix() . 'contactcenter_contact')->row();
                        if ($contact_check) {
                            if (!empty($contact_check->name)) {
                                $group_name = $contact_check->name;
                                log_activity("ajax_get_messages_chat GROUP NAME FROM CONTACT - Group ID: {$group_id}, Name: {$group_name}", get_staff_user_id());
                            }
                            // Also get picture from contact if available and not already fetched
                            if (empty($fetched_group_picture) && !empty($contact_check->thumb)) {
                                $fetched_group_picture = $contact_check->thumb;
                                log_activity("ajax_get_messages_chat GROUP PICTURE FROM CONTACT - Group ID: {$group_id}, Picture URL: {$fetched_group_picture}", get_staff_user_id());
                            }
                        } else {
                            // Last resort: try to get from the most recent group message
                            $this->db->where('msg_conversation_number', $group_id);
                            $this->db->where('msg_isGroupMsg', 1);
                            $this->db->order_by('msg_date', 'DESC');
                            $this->db->limit(1);
                            $last_message = $this->db->get(db_prefix() . 'contactcenter_message')->row();

                            // Check if message has group metadata (some APIs include group subject in message data)
                            if ($last_message && !empty($last_message->msg_name)) {
                                // msg_name might be the sender, but let's check if there's group info
                                log_activity("ajax_get_messages_chat CHECKING LAST MESSAGE - Group ID: {$group_id}, msg_name: " . ($last_message->msg_name ?? 'null'), get_staff_user_id());
                            }
                        }
                    }

                    $jSON["name"] = $group_name ?? $data["phonenumber"];
                    $jSON["phonenumber"] = $data["phonenumber"];
                    $jSON["gpt_status"] = null; // Groups don't have GPT status

                    // Get group picture if available from contact record or fetched from API
                    if (!empty($contact_check) && !empty($contact_check->thumb)) {
                        $jSON["thumb"] = $contact_check->thumb;
                        log_activity("ajax_get_messages_chat GROUP PICTURE IN RESPONSE - Group ID: {$group_id}, Picture URL: {$contact_check->thumb}", get_staff_user_id());
                    } elseif (isset($fetched_group_picture) && !empty($fetched_group_picture)) {
                        $jSON["thumb"] = $fetched_group_picture;
                        log_activity("ajax_get_messages_chat GROUP PICTURE FROM API IN RESPONSE - Group ID: {$group_id}, Picture URL: {$fetched_group_picture}", get_staff_user_id());
                    }

                    log_activity("ajax_get_messages_chat GROUP CHAT RESPONSE - Group ID: {$group_id}, Final Name: " . $jSON["name"], get_staff_user_id());
                } else {
                    // For regular contacts, get from leads table
                    $leads = $this->contactcenter_model->get_dados_leads_phone($data["phonenumber"]);
                    if ($leads) {
                        $jSON["id"] = $leads->id;
                        $jSON["name"] = $leads->name;
                        $jSON["phonenumber"] = $leads->phonenumber;
                        $jSON["gpt_status"] = $leads->gpt_status;
                        // Birthday from date_picker custom field (prefer slug with birthday/aniversario/nascimento)
                        $birthday_fields = get_custom_fields('leads', ['type' => 'date_picker']);
                        $birthday_value = null;
                        $birthday_field_id = null;
                        $preferred_field = null;
                        foreach ($birthday_fields as $bf) {
                            $bf_slug = strtolower($bf['slug'] ?? '');
                            if (strpos($bf_slug, 'birthday') !== false || strpos($bf_slug, 'aniversario') !== false || strpos($bf_slug, 'nascimento') !== false) {
                                $preferred_field = $bf;
                                break;
                            }
                        }
                        $field_to_use = $preferred_field ?: (isset($birthday_fields[0]) ? $birthday_fields[0] : null);
                        if ($field_to_use) {
                            $birthday_field_id = $field_to_use['id'];
                            $birthday_value = get_custom_field_value($leads->id, $birthday_field_id, 'leads', false);
                        }
                        $jSON["birthday"] = $birthday_value;
                        $jSON["birthday_field_id"] = $birthday_field_id;
                        $jSON["birthday_is_today"] = false;
                        if ($birthday_value) {
                            $bdate = date_parse_from_format('Y-m-d', $birthday_value);
                            if ($bdate && $bdate['month'] == (int)date('n') && $bdate['day'] == (int)date('j')) {
                                $jSON["birthday_is_today"] = true;
                            }
                        }
                        // Sales count (si_lead_filters) for Add Sale button indicator
                        $jSON["sales_count"] = 0;
                        if (!$this->app_modules->is_inactive('si_lead_filters') && $this->db->table_exists(db_prefix() . 'si_lf_sales')) {
                            $client_id = 0;
                            $client = $this->db->select('userid')->from(db_prefix() . 'clients')->where('leadid', (int)$leads->id)->get()->row();
                            if ($client) {
                                $client_id = (int)$client->userid;
                            }
                            if ($client_id > 0) {
                                $this->db->where('client_id', $client_id);
                                $jSON["sales_count"] = (int)$this->db->count_all_results(db_prefix() . 'si_lf_sales');
                            }
                        }
                    } else {
                        $jSON["id"] = 0;
                        $jSON["name"] = $data["phonenumber"];
                        $jSON["phonenumber"] = $data["phonenumber"];
                        $jSON["gpt_status"] = null;
                        $jSON["birthday"] = null;
                        $jSON["birthday_field_id"] = null;
                        $jSON["birthday_is_today"] = false;
                        $jSON["sales_count"] = 0;
                    }
                }

                // Get device to pass show_messages_all_devices option
                $device_obj = null;
                if (!empty($data["token"])) {
                    $device_obj = $this->contactcenter_model->get_device_token($data["token"]);
                    if ($device_obj && isset($device_obj->show_messages_all_devices)) {
                        $data["show_messages_all_devices"] = $device_obj->show_messages_all_devices;
                        $data["show_all_devices"] = $device_obj->show_messages_all_devices;
                    }
                }
                $show_device_badge = ($device_obj && !empty($device_obj->show_messages_all_devices));

                $result = $this->contactcenter_model->get_messages_chat($data, $isRead = true);
                if ($result) {
                    // Obtém a data do último elemento do array
                    $lastMessage = reset($result);
                    $jSON["paginadorChat"] = $lastMessage->msg_date;
                    $jSON["retorno"] = monta_html_chat($result, $show_device_badge);
                } else {
                    $jSON["retorno"] = monta_html_chat($result, $show_device_badge);
                }
            }
        } else {
            show_404();
        }
        echo json_encode($jSON);
    }


    public function ajax_get_chat_paginador()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device_obj = null;
                if (!empty($data["token"])) {
                    $device_obj = $this->contactcenter_model->get_device_token($data["token"]);
                    if ($device_obj && isset($device_obj->show_messages_all_devices)) {
                        $data["show_messages_all_devices"] = $device_obj->show_messages_all_devices;
                        $data["show_all_devices"] = $device_obj->show_messages_all_devices;
                    }
                }
                $show_device_badge = ($device_obj && !empty($device_obj->show_messages_all_devices));
                $result = $this->contactcenter_model->get_messages_chat($data, $isRead = true);
                if ($result) {
                    $jSON["paginadorChat"] = $result[0]->msg_date;
                    $jSON["retorno"] = monta_html_chat($result, $show_device_badge);
                }
            }
        }
        echo json_encode($jSON);
    }


    public function ajax_off_ai_user()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();

                // Log the request
                log_message('debug', 'ajax_off_ai_user called - Lead ID: ' . ($data['id'] ?? 'missing') . ', Status: ' . ($data['status'] ?? 'missing'));

                $result = $this->contactcenter_model->off_ai_user_single($data);
                if ($result) {
                    $jSON["retorno"] = true;
                    $jSON["message"] = "AI status updated successfully";
                } else {
                    $jSON["retorno"] = false;
                    $jSON["message"] = "Failed to update AI status";
                }
            } else {
                $jSON["retorno"] = false;
                $jSON["message"] = "No POST data received";
            }
        } else {
            $jSON["retorno"] = false;
            $jSON["message"] = "Not an AJAX request";
        }
        echo json_encode($jSON);
    }

    public function ajax_off_ai_all()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->ajax_off_ai_all($data);
                if ($result) {
                    $jSON["retorno"] = true;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_verify_number_whats()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->verify_Number_Whatsapp($data,);
                if ($result["type"] == "sistema" && $result["exists"]) {
                    $jSON["retorno"] = true;
                } else if ($result["type"] == "local") {
                    $jSON["local"] = $result;
                } else {
                    $jSON["retorno"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_get_contact_chat()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $chat_marked_read = isset($data["chat_marked_read"]) ? $data["chat_marked_read"] : null;
                $chat_sort = isset($data["chat_sort"]) ? $data["chat_sort"] : "newest_first";
                $staffid = isset($data["token"]) ? (int) $data["token"] : 0;
                $device_token = isset($data["device_token"]) ? $data["device_token"] : null;
                $is_local = isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
                if ($is_local && $device_token && $staffid) {
                    $result = $this->contactcenter_model->get_contact_chat($staffid, $data["msg_fromMe"], null, $data["statuLead"], $data["assignLead"], $chat_marked_read, $chat_sort, false, null);
                    if (!$result && $device_token) {
                        $result = $this->contactcenter_model->get_contact_chat(0, $data["msg_fromMe"], null, $data["statuLead"], $data["assignLead"], $chat_marked_read, $chat_sort, false, $device_token);
                    }
                } else {
                    $result = $this->contactcenter_model->get_contact_chat($staffid, $data["msg_fromMe"], null, $data["statuLead"], $data["assignLead"], $chat_marked_read, $chat_sort, false, $device_token);
                }
                if ($result) {
                    $jSON["retorno"] = monta_html_contact_banco($result);
                }
            }
        } else {
            show_404();
        }
        echo json_encode($jSON, JSON_UNESCAPED_UNICODE);
    }

    public function ajax_get_contact_chat_paginador()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $staffid = isset($data["staff"]) ? (int) $data["staff"] : 0;
                $device_token = isset($data["device_token"]) ? $data["device_token"] : null;
                $is_local = isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
                $paginador_key = ($staffid && !($is_local && $device_token)) ? $staffid : (!empty($device_token) ? "dev_{$device_token}" : $staffid);
                $date = $this->session->userdata("contacenter_paginadorContact_{$paginador_key}");
                $chat_marked_read = isset($data["chat_marked_read"]) ? $data["chat_marked_read"] : null;
                $chat_sort = isset($data["chat_sort"]) ? $data["chat_sort"] : "newest_first";
                $result = $this->contactcenter_model->get_contact_chat($staffid, null, $date, $data["statuLead"], $data["assignLead"], $chat_marked_read, $chat_sort, false, $device_token);
                if (!$result && $is_local && $device_token && $staffid) {
                    $paginador_key = "dev_{$device_token}";
                    $date = $this->session->userdata("contacenter_paginadorContact_{$paginador_key}");
                    $result = $this->contactcenter_model->get_contact_chat(0, null, $date, $data["statuLead"], $data["assignLead"], $chat_marked_read, $chat_sort, false, $device_token);
                }
                if ($result) {
                    $jSON["retorno"] = monta_html_contact_banco($result);
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_get_search_chat()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->ajax_get_search_chat($data);
                // print_r($result);                
                if ($result) {
                    $jSON["search"] =  $result;
                }
            }
        }

        // print_r(json_encode($jSON));
        echo json_encode($jSON);
    }




    public function add_assistant()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!empty($data['template_id'])) {
                $data['template_id'] = (int) $data['template_id'];
            }
            $response = $this->contactcenter_model->add_assistant($data);
            if ($response["sucesso"] == true) {
                if ($data["id"]) {
                    log_activity('Editou assistante [' . $data["ai_token"] . ']', get_staff_user_id());
                } else {
                    log_activity('Criou assistante [' . $data["ai_token"] . ']', get_staff_user_id());
                }
                set_alert('success', _l('contac_save'));
                redirect(admin_url('contactcenter/assistant_edit/' . $response['id']));
            } else {
                if ($response["limit"]) {
                    redirect(admin_url('contactcenter/assistant_ai?assistant=true'));
                }

                if ($data["id"]) {
                    redirect(admin_url('contactcenter/assistant_edit/' . $data["id"]));
                }
                set_alert('danger', _l('contac_save_error'));
                redirect(admin_url('contactcenter/assistant_ai'));
            }
        } else {
            show_404();
        }
    }

    /**
     * Generate or get public onboarding form link for assistant (AJAX)
     */
    public function generate_assistant_form_link()
    {
        header('Content-Type: application/json');
        $jSON = ['success' => false, 'message' => '', 'url' => '', 'token' => ''];
        if ($this->input->is_ajax_request()) {
            $assistant_id = (int) $this->input->post('assistant_id');
            $assistant = $this->contactcenter_model->get_assistants_ai($assistant_id);
            if ($assistant) {
                $token = $assistant->public_form_token;
                if (empty($token)) {
                    $token = $this->contactcenter_model->generate_assistant_form_token($assistant_id);
                }
                $base = rtrim(site_url(), '/');
                $url = $base . '/contactcenter/assistant_form_public/index/' . $token;
                $jSON['success'] = true;
                $jSON['url'] = $url;
                $jSON['token'] = $token;
            } else {
                $jSON['message'] = _l('contac_aviso_sem_assistant');
            }
        }
        echo json_encode($jSON);
    }

    public function delete_file_cectorstore()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->delete_File_VectorStore($data["id"]);
                if ($device) {
                    $jSON["result"] = true;
                } else {
                    $jSON["result"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    /**
     * Get assistant version history (AJAX)
     */
    public function get_assistant_versions()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            $assistant_id = $this->input->post('assistant_id');
            if ($assistant_id) {
                $versions = $this->contactcenter_model->get_assistant_versions($assistant_id);
                $jSON["success"] = true;
                $jSON["versions"] = $versions;
            } else {
                $jSON["success"] = false;
                $jSON["message"] = "Assistant ID required";
            }
        }
        echo json_encode($jSON);
    }

    /**
     * Restore assistant to a previous version (AJAX)
     */
    public function restore_assistant_version()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            $assistant_id = $this->input->post('assistant_id');
            $version_id = $this->input->post('version_id');

            if ($assistant_id && $version_id) {
                $result = $this->contactcenter_model->restore_assistant_version($assistant_id, $version_id);
                if ($result) {
                    $jSON["success"] = true;
                    $jSON["message"] = _l('contac_assistant_version_restored');
                    log_activity('Restored assistant version [Assistant ID: ' . $assistant_id . ', Version ID: ' . $version_id . ']', get_staff_user_id());
                } else {
                    $jSON["success"] = false;
                    $jSON["message"] = _l('contac_assistant_version_restore_error');
                }
            } else {
                $jSON["success"] = false;
                $jSON["message"] = "Assistant ID and Version ID required";
            }
        }
        echo json_encode($jSON);
    }

    /**
     * Upload media file for assistant
     */
    public function upload_assistant_media()
    {
        $jSON = array();
        // Accept both AJAX and POST requests for file uploads
        if ($this->input->is_ajax_request() || $this->input->server('REQUEST_METHOD') === 'POST') {
            $assist_id = $this->input->post('assist_id');
            $file_name = $this->input->post('file_name');

            if (!$assist_id) {
                $jSON["result"] = false;
                $jSON["message"] = "Missing required parameters";
                echo json_encode($jSON);
                return;
            }

            // Se não forneceu nome, usa o nome do arquivo sem extensão
            if (empty($file_name) && isset($_FILES['media_file']['name'])) {
                $original_filename = $_FILES['media_file']['name'];
                $file_name = pathinfo($original_filename, PATHINFO_FILENAME); // Remove extensão
            }

            // Check if file was uploaded
            if (!isset($_FILES['media_file']) || $_FILES['media_file']['error'] !== UPLOAD_ERR_OK) {
                $jSON["result"] = false;
                $error_message = "No file uploaded";
                if (isset($_FILES['media_file']['error'])) {
                    switch ($_FILES['media_file']['error']) {
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            $error_message = "File is too large";
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $error_message = "File was only partially uploaded";
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $error_message = "No file was uploaded";
                            break;
                        case UPLOAD_ERR_NO_TMP_DIR:
                            $error_message = "Missing temporary folder";
                            break;
                        case UPLOAD_ERR_CANT_WRITE:
                            $error_message = "Failed to write file to disk";
                            break;
                        case UPLOAD_ERR_EXTENSION:
                            $error_message = "A PHP extension stopped the file upload";
                            break;
                    }
                }
                $jSON["message"] = $error_message;
                log_activity('Media upload error: ' . $error_message . ' - Assistant ID: ' . $assist_id, get_staff_user_id());
                echo json_encode($jSON);
                return;
            }

            // Determine media type based on file extension
            $allowed_types = array(
                'image' => array('jpg', 'jpeg', 'png', 'gif', 'webp'),
                'audio' => array('mp3', 'wav', 'ogg', 'm4a', 'aac'),
                'video' => array('mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv')
            );

            $original_filename = $_FILES['media_file']['name'];
            $file_extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
            $media_type = 'other';

            foreach ($allowed_types as $type => $extensions) {
                if (in_array($file_extension, $extensions)) {
                    $media_type = $type;
                    break;
                }
            }

            // Upload file with all supported formats
            $upload_path = 'contactcenter/assistants_media';
            $max_size = 50 * 1024; // 50MB in KB

            $upload_result = $this->contactcenter_model->upload_assistant_media_file($upload_path, $max_size, 'media_file');

            if ($upload_result && is_array($upload_result) && isset($upload_result['success']) && $upload_result['success']) {
                $file_path = $upload_result['file_path'];
                $is_library = $this->input->post('is_library') == '1' ? 1 : 0;

                // Garante que file_name tenha a extensão do arquivo
                // Se o usuário não forneceu extensão, adiciona a extensão do arquivo original
                if (!empty($file_extension)) {
                    // Remove extensão se já tiver
                    $file_name_without_ext = pathinfo($file_name, PATHINFO_FILENAME);
                    // Adiciona a extensão correta
                    $file_name = $file_name_without_ext . '.' . $file_extension;
                }

                $data = array(
                    'assist_id' => $is_library ? null : $assist_id, // Library media doesn't have assist_id
                    'file_name' => $file_name, // Agora com extensão
                    'file_path' => $file_path,
                    'file_type' => $media_type,
                    'file_size' => $_FILES['media_file']['size'],
                    'variable_name' => '{media_' . pathinfo($file_name, PATHINFO_FILENAME) . '}', // Variable name sem extensão
                    'is_library' => $is_library
                );

                $media_id = $this->contactcenter_model->add_assistant_media($data);

                if ($media_id) {
                    $jSON["result"] = true;
                    $jSON["message"] = "File uploaded successfully";
                    $jSON["media"] = array(
                        'id' => $media_id,
                        'file_name' => $file_name,
                        'file_path' => $file_path,
                        'file_type' => $media_type,
                        'variable_name' => $data['variable_name']
                    );
                    log_activity('Media file uploaded: ' . $file_name . ' - Assistant ID: ' . $assist_id, get_staff_user_id());
                } else {
                    $jSON["result"] = false;
                    $jSON["message"] = "Failed to save file information";
                    log_activity('Failed to save media file info: ' . $file_name . ' - Assistant ID: ' . $assist_id, get_staff_user_id());
                }
            } else {
                $jSON["result"] = false;
                $error_msg = isset($upload_result['error']) ? $upload_result['error'] : "Failed to upload file";
                $jSON["message"] = $error_msg;
                log_activity('Media upload error: ' . $error_msg . ' - File: ' . $original_filename . ' - Assistant ID: ' . $assist_id, get_staff_user_id());
            }
        } else {
            $jSON["result"] = false;
            $jSON["message"] = "Invalid request";
        }
        echo json_encode($jSON);
    }

    /**
     * Delete media file from assistant
     */
    public function delete_assistant_media()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->delete_assistant_media($data["id"]);
                if ($result) {
                    $jSON["result"] = true;
                } else {
                    $jSON["result"] = false;
                    $jSON["message"] = "Failed to delete file";
                }
            }
        }
        echo json_encode($jSON);
    }

    /**
     * Get library media (AJAX)
     */
    public function get_library_media()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            $library_media = $this->contactcenter_model->get_library_media();
            $jSON["success"] = true;
            $jSON["media"] = $library_media;
        } else {
            $jSON["success"] = false;
        }
        echo json_encode($jSON);
    }

    /**
     * Add library media to assistant (AJAX)
     */
    public function add_library_media_to_assistant()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            $assistant_id = $this->input->post('assistant_id');
            $media_id = $this->input->post('media_id');

            if ($assistant_id && $media_id) {
                $result = $this->contactcenter_model->add_library_media_to_assistant($assistant_id, $media_id);
                if ($result) {
                    $jSON["success"] = true;
                    $jSON["message"] = _l('contac_assistant_library_media_added');
                    // Get the added media
                    $media = $this->contactcenter_model->get_assistants_media($assistant_id);
                    $added_media = null;
                    foreach ($media as $m) {
                        if ($m->id == $result) {
                            $added_media = $m;
                            break;
                        }
                    }
                    $jSON["media"] = $added_media;
                } else {
                    $jSON["success"] = false;
                    $jSON["message"] = _l('contac_assistant_library_media_add_error');
                }
            } else {
                $jSON["success"] = false;
                $jSON["message"] = "Missing required parameters";
            }
        }
        echo json_encode($jSON);
    }

    public function delete_assistant()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $assistant = $this->contactcenter_model->delete_assistant($data);
                if ($assistant) {
                    $jSON["result"] = true;
                } else {
                    $jSON["result"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function edit_engine()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->get_engine($data["id"]);
                if ($device) {
                    $jSON['con_id'] = $device->con_id;
                    $jSON['con_title'] = $device->con_title;
                    $jSON['leads_status'] = $device->leads_status;
                    $jSON['date_filter_type'] = isset($device->date_filter_type) ? $device->date_filter_type : 'creation_date';
                    $jSON['leads_create_data'] = $device->leads_create_data;
                    $jSON['leads_status_final'] = $device->leads_status_final;
                    $jSON['leads_create_data_final'] = $device->leads_create_data_final;
                    $jSON['leads_last_contact_data'] = isset($device->leads_last_contact_data) ? $device->leads_last_contact_data : '';
                    $jSON['leads_last_contact_data_final'] = isset($device->leads_last_contact_data_final) ? $device->leads_last_contact_data_final : '';
                    $jSON['filter_source'] = isset($device->filter_source) ? $device->filter_source : '';
                    $jSON['filter_city'] = isset($device->filter_city) ? $device->filter_city : '';
                    $jSON['filter_state'] = isset($device->filter_state) ? $device->filter_state : '';
                    $jSON['spare_devices'] = isset($device->spare_devices) && !empty($device->spare_devices) ? explode(',', $device->spare_devices) : [];
                    $jSON['leads_sleep'] = $device->leads_sleep;
                    $jSON['leads_day'] = $device->leads_day;
                    $jSON['start_time'] = $device->start_time;
                    $jSON['end_time'] = $device->end_time;
                    $jSON['device_id'] = $device->device_id;

                    $jSON['tags'] = explode(',', $device->tags);
                    $jSON['campaign_tag_id'] = isset($device->campaign_tag_id) ? $device->campaign_tag_id : '';

                    // Safety Settings fields
                    $jSON['daily_limit'] = isset($device->daily_limit) ? $device->daily_limit : 1000;
                    $jSON['batch_size'] = isset($device->batch_size) ? $device->batch_size : 5;
                    $jSON['batch_cooldown'] = isset($device->batch_cooldown) ? $device->batch_cooldown : 5;
                    $jSON['message_interval_min'] = isset($device->message_interval_min) ? $device->message_interval_min : 1;
                    $jSON['message_interval_max'] = isset($device->message_interval_max) ? $device->message_interval_max : 3;
                    $jSON['is_warmup_active'] = isset($device->is_warmup_active) ? $device->is_warmup_active : 0;
                    $jSON['stop_on_reply'] = isset($device->stop_on_reply) ? $device->stop_on_reply : 1;

                    // Advanced features
                    $jSON['vcard_enable'] = isset($device->vcard_enable) ? $device->vcard_enable : 0;
                    $jSON['vcard_name'] = isset($device->vcard_name) ? $device->vcard_name : '';
                    $jSON['vcard_phone'] = isset($device->vcard_phone) ? $device->vcard_phone : '';
                    $jSON['inbound_bait_enable'] = isset($device->inbound_bait_enable) ? $device->inbound_bait_enable : 0;
                    $jSON['inbound_bait_message'] = isset($device->inbound_bait_message) ? $device->inbound_bait_message : '';
                    $jSON['safe_groups_enable'] = isset($device->safe_groups_enable) ? $device->safe_groups_enable : 0;

                    // Backup phone field
                    $jSON['backup_phone_field'] = isset($device->backup_phone_field) ? $device->backup_phone_field : null;
                    $jSON['backup_phone_country_code'] = isset($device->backup_phone_country_code) ? $device->backup_phone_country_code : '55';

                    // Birthday field for birthday campaigns
                    $jSON['birthday_field'] = isset($device->birthday_field) ? $device->birthday_field : null;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function add_group()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $response = $this->contactcenter_model->add_group($data);
            if ($response == true) {
                if ($data["id"]) {
                    log_activity('Editou grupo [' . $this->input->post('name') . ']', get_staff_user_id());
                } else {
                    log_activity('Criou grupo [' . $this->input->post('name') . ']', get_staff_user_id());
                }
                set_alert('success', _l('contac_save'));
                redirect(admin_url('contactcenter/group'));
            } else {
                set_alert('danger', _l('contac_save_error'));
                redirect(admin_url('contactcenter/group'));
            }
        } else {
            show_404();
        }
    }

    public function ajax_delete_group()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $group = $this->contactcenter_model->delete_group($data);
                if ($group) {
                    $jSON["result"] = true;
                } else {
                    $jSON["result"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_delete_participant_group()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $group = $this->contactcenter_model->delete_participant_group($data);
                if ($group) {
                    $jSON["result"] = true;
                } else {
                    $jSON["result"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function add_import_leads()
    {
        if ($this->input->post() && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

            $data = $this->input->post();
            $files = $_FILES['file_csv'];

            $response = $this->contactcenter_model->import_leads($data, $files);
            if ($response >= 0) {
                set_alert('success', _l('import_total_imported', $response));
                redirect(admin_url('contactcenter/import_leads'));
            } else {
                set_alert('danger', _l('import_leads_error'));
                redirect(admin_url('contactcenter/import_leads'));
            }
        } else {
            set_alert('danger', _l('import_file_not_uploaded'));
            redirect(admin_url('contactcenter/import_leads'));
        }
    }

    /**
     * AJAX endpoint to search leads using AI
     */
    public function ajax_search_leads_ai()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $params = [
            'country' => $this->input->post('country'),
            'state' => $this->input->post('state'),
            'state_code' => $this->input->post('state_code'), // State code (e.g., SP, RJ) for Brazil
            'city' => $this->input->post('city'),
            'category' => $this->input->post('category'),
            'quantity' => $this->input->post('quantity') ? (int)$this->input->post('quantity') : 100,
            'batch_size' => $this->input->post('batch_size') ? (int)$this->input->post('batch_size') : 100,
            'enable_gemini_enrichment' => $this->input->post('enable_gemini_enrichment') === 'true' || $this->input->post('enable_gemini_enrichment') === true
        ];

        // Validate required fields
        if (empty($params['country'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Country is required.'
            ]);
            return;
        }

        // For Brazil, state and city are required
        if (strtolower($params['country']) === 'brazil') {
            if (empty($params['state']) || empty($params['city'])) {
                echo json_encode([
                    'success' => false,
                    'error' => 'State and City are required for Brazil.'
                ]);
                return;
            }
        } else {
            if (empty($params['city'])) {
                echo json_encode([
                    'success' => false,
                    'error' => 'City is required.'
                ]);
                return;
            }
        }

        $results = $this->contactcenter_model->search_leads_ai($params);

        if (isset($results['error'])) {
            echo json_encode([
                'success' => false,
                'error' => $results['error']
            ]);
        } else {
            // Extract enrichment stats if present
            $enrichment_stats = null;
            if (isset($results['_enrichment_stats'])) {
                $enrichment_stats = $results['_enrichment_stats'];
                unset($results['_enrichment_stats']); // Remove from leads array
            }

            echo json_encode([
                'success' => true,
                'leads' => $results,
                'count' => count($results),
                'enrichment_stats' => $enrichment_stats,
                'enrichment_enabled' => $params['enable_gemini_enrichment'],
                'enrichment_complete' => true // Always true for synchronous processing
            ]);
        }
    }

    /**
     * AJAX endpoint to get field mappings configuration
     */
    public function ajax_get_field_mappings()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $mappings_json = get_option('contactcenter_ai_lead_field_mappings');
        $mappings = [];
        if (!empty($mappings_json)) {
            $mappings = json_decode($mappings_json, true);
            if (!is_array($mappings)) {
                $mappings = [];
            }
        }

        // Get custom field names for display
        $custom_fields = get_custom_fields('leads');
        $field_labels = [];
        $field_raw = []; // Store raw field names for reference

        foreach ($mappings as $ai_field => $target_field) {
            if (strpos($target_field, 'custom_field_') === 0) {
                $cf_id = str_replace('custom_field_', '', $target_field);
                foreach ($custom_fields as $cf) {
                    if ($cf['id'] == $cf_id) {
                        $custom_field_label = _l('custom_field');
                        $field_labels[$ai_field] = $cf['name'] . ' (' . $custom_field_label . ')';
                        $field_raw[$ai_field] = $target_field;
                        break;
                    }
                }
            } else {
                // Standard field - get label
                $standard_labels = [
                    'name' => _l('leads_dt_name'),
                    'company' => _l('lead_company'),
                    'phonenumber' => _l('leads_dt_phonenumber'),
                    'email' => _l('lead_email'),
                    'website' => _l('lead_website'),
                    'address' => _l('lead_address'),
                    'city' => _l('lead_city'),
                    'state' => _l('lead_state'),
                    'country' => _l('clients_country')
                ];
                $field_labels[$ai_field] = isset($standard_labels[$target_field]) ? $standard_labels[$target_field] : $target_field;
                $field_raw[$ai_field] = $target_field;
            }
        }

        echo json_encode([
            'success' => true,
            'mappings' => $field_labels,
            'raw_mappings' => $field_raw // Include raw mappings for reference
        ]);
    }

    /**
     * AJAX endpoint to get Brazilian cities by state
     */
    public function ajax_get_brazilian_cities()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $state = strtoupper(trim($this->input->post('state')));

        if (empty($state) || strlen($state) !== 2) {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid state code'
            ]);
            return;
        }

        $cities = $this->get_brazilian_cities_by_state($state);

        echo json_encode([
            'success' => true,
            'cities' => $cities
        ]);
    }

    /**
     * Get Brazilian cities for a state
     */
    private function get_brazilian_cities_by_state($state)
    {
        // Try to use AXIOM Lite model if available
        if (class_exists('Omniu_lite_model')) {
            $this->load->model('axiom_lite/axiom_lite_model');
            if (method_exists($this->axiom_lite_model, 'get_brazilian_cities')) {
                return $this->axiom_lite_model->get_brazilian_cities($state);
            }
        }

        // Fallback: Use IBGE API to get cities
        $url = 'https://servicodados.ibge.gov.br/api/v1/localidades/estados/' . $state . '/municipios';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200 && $response) {
            $data = json_decode($response, true);
            if (is_array($data)) {
                $cities = [];
                foreach ($data as $city) {
                    $cities[] = [
                        'value' => $city['nome'],
                        'label' => $city['nome']
                    ];
                }
                // Sort by name
                usort($cities, function ($a, $b) {
                    return strcmp($a['label'], $b['label']);
                });
                return $cities;
            }
        }

        // Ultimate fallback: return common cities for major states
        return $this->get_fallback_cities($state);
    }

    /**
     * Fallback cities list for major Brazilian states
     */
    private function get_fallback_cities($state)
    {
        $fallback = [
            'SP' => ['São Paulo', 'Campinas', 'Santos', 'São José dos Campos', 'Ribeirão Preto', 'Sorocaba', 'Jundiaí', 'Piracicaba', 'Bauru', 'Franca'],
            'RJ' => ['Rio de Janeiro', 'Niterói', 'Campos dos Goytacazes', 'Petrópolis', 'Volta Redonda', 'Nova Iguaçu', 'Duque de Caxias', 'São Gonçalo'],
            'MG' => ['Belo Horizonte', 'Uberlândia', 'Contagem', 'Juiz de Fora', 'Betim', 'Montes Claros', 'Ribeirão das Neves', 'Uberaba'],
            'RS' => ['Porto Alegre', 'Caxias do Sul', 'Pelotas', 'Canoas', 'Santa Maria', 'Gravataí', 'Viamão', 'Novo Hamburgo'],
            'PR' => ['Curitiba', 'Londrina', 'Maringá', 'Ponta Grossa', 'Cascavel', 'São José dos Pinhais', 'Foz do Iguaçu', 'Colombo'],
            'SC' => ['Florianópolis', 'Joinville', 'Blumenau', 'São José', 'Criciúma', 'Chapecó', 'Itajaí', 'Lages'],
            'BA' => ['Salvador', 'Feira de Santana', 'Vitória da Conquista', 'Camaçari', 'Juazeiro', 'Ilhéus', 'Itabuna', 'Jequié'],
            'GO' => ['Goiânia', 'Aparecida de Goiânia', 'Anápolis', 'Rio Verde', 'Luziânia', 'Águas Lindas de Goiás', 'Valparaíso de Goiás'],
            'PE' => ['Recife', 'Jaboatão dos Guararapes', 'Olinda', 'Caruaru', 'Petrolina', 'Paulista', 'Cabo de Santo Agostinho'],
            'CE' => ['Fortaleza', 'Caucaia', 'Juazeiro do Norte', 'Maracanaú', 'Sobral', 'Crato', 'Itapipoca', 'Maranguape'],
            'PA' => ['Belém', 'Ananindeua', 'Marabá', 'Paragominas', 'Castanhal', 'Abaetetuba', 'Cametá'],
            'MA' => ['São Luís', 'Imperatriz', 'Caxias', 'Timon', 'Codo', 'Paço do Lumiar', 'Açailândia'],
            'ES' => ['Vitória', 'Vila Velha', 'Cariacica', 'Serra', 'Cachoeiro de Itapemirim', 'Linhares', 'São Mateus'],
            'PB' => ['João Pessoa', 'Campina Grande', 'Santa Rita', 'Patos', 'Bayeux', 'Sousa', 'Cajazeiras'],
            'AM' => ['Manaus', 'Parintins', 'Itacoatiara', 'Manacapuru', 'Coari', 'Tefé', 'Maués'],
            'RN' => ['Natal', 'Mossoró', 'Parnamirim', 'São Gonçalo do Amarante', 'Macaíba', 'Ceará-Mirim'],
            'AL' => ['Maceió', 'Arapiraca', 'Rio Largo', 'Palmeira dos Índios', 'União dos Palmares', 'São Miguel dos Campos'],
            'MT' => ['Cuiabá', 'Várzea Grande', 'Rondonópolis', 'Sinop', 'Tangará da Serra', 'Cáceres', 'Barra do Garças'],
            'MS' => ['Campo Grande', 'Dourados', 'Três Lagoas', 'Corumbá', 'Ponta Porã', 'Naviraí', 'Nova Andradina'],
            'RO' => ['Porto Velho', 'Ji-Paraná', 'Ariquemes', 'Vilhena', 'Cacoal', 'Rolim de Moura', 'Guajará-Mirim'],
            'TO' => ['Palmas', 'Araguaína', 'Gurupi', 'Porto Nacional', 'Paraíso do Tocantins', 'Colinas do Tocantins'],
            'AC' => ['Rio Branco', 'Cruzeiro do Sul', 'Sena Madureira', 'Tarauacá', 'Feijó', 'Brasiléia'],
            'AP' => ['Macapá', 'Santana', 'Laranjal do Jari', 'Oiapoque', 'Mazagão', 'Vitória do Jari'],
            'RR' => ['Boa Vista', 'Rorainópolis', 'Caracaraí', 'Alto Alegre', 'Bonfim', 'Mucajaí'],
            'PI' => ['Teresina', 'Parnaíba', 'Picos', 'Piripiri', 'Campo Maior', 'Floriano'],
            'SE' => ['Aracaju', 'Nossa Senhora do Socorro', 'Lagarto', 'Itabaiana', 'São Cristóvão', 'Estância'],
            'DF' => ['Brasília', 'Taguatinga', 'Ceilândia', 'Sobradinho', 'Planaltina', 'Gama']
        ];

        $state = strtoupper($state);
        if (isset($fallback[$state])) {
            $cities = [];
            foreach ($fallback[$state] as $city) {
                $cities[] = [
                    'value' => $city,
                    'label' => $city
                ];
            }
            return $cities;
        }

        return [];
    }

    /**
     * AJAX endpoint to import AI-found leads
     */
    public function ajax_import_ai_leads()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $leads_json = $this->input->post('leads_json');
        $leads = json_decode($leads_json, true);

        if (!is_array($leads) || empty($leads)) {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid leads data.'
            ]);
            return;
        }

        $data = [
            'status' => $this->input->post('status'),
            'source' => $this->input->post('source'),
            'staffid' => $this->input->post('staffid'),
            'country' => $this->input->post('country'),
            'gpt_status' => $this->input->post('gpt_status')
        ];

        // Validate required fields
        if (empty($data['status']) || empty($data['source']) || empty($data['staffid'])) {
            echo json_encode([
                'success' => false,
                'error' => 'Status, Source, and Assignee are required fields.'
            ]);
            return;
        }

        // Import leads using existing import logic
        $this->load->model('leads_model');
        $count = 0;
        $errors = [];

        // Get field mappings
        $field_mappings_json = get_option('contactcenter_ai_lead_field_mappings');
        $field_mappings = [];
        if (!empty($field_mappings_json)) {
            $field_mappings = json_decode($field_mappings_json, true);
            if (!is_array($field_mappings)) {
                $field_mappings = [];
            }
        }

        foreach ($leads as $lead) {
            $lead_data = [
                'name' => $lead['name'] ?? ($lead['company'] ?? ''),
                'company' => $lead['company'] ?? '',
                'phonenumber' => $lead['phone'] ?? '',
                'website' => $lead['website'] ?? '',
                'email' => $lead['email'] ?? '',
                'city' => $lead['city'] ?? '',
                'state' => $lead['state'] ?? '',
                'address' => $lead['address'] ?? '',
                'country' => $data['country'],
                'status' => $data['status'],
                'source' => $data['source'],
                'assigned' => $data['staffid'],
                'gpt_status' => $data['gpt_status'] ?? 0
            ];

            // Apply field mappings for enriched data
            $custom_fields_data = [];

            // Map WhatsApp number
            if (!empty($lead['whatsapp_number']) || !empty($lead['whatsapp_enriched'])) {
                $whatsapp_value = $lead['whatsapp_number'] ?? $lead['whatsapp_enriched'];
                $whatsapp_mapping = isset($field_mappings['whatsapp_number']) ? $field_mappings['whatsapp_number'] : '';

                if (!empty($whatsapp_mapping)) {
                    if (strpos($whatsapp_mapping, 'custom_field_') === 0) {
                        $cf_id = str_replace('custom_field_', '', $whatsapp_mapping);
                        $custom_fields_data[$cf_id] = $whatsapp_value;
                    } else {
                        $lead_data[$whatsapp_mapping] = $whatsapp_value;
                    }
                }
            }

            // Map Social Media
            if (!empty($lead['social_media'])) {
                $social_mapping = isset($field_mappings['social_media']) ? $field_mappings['social_media'] : '';

                if (!empty($social_mapping)) {
                    if (strpos($social_mapping, 'custom_field_') === 0) {
                        $cf_id = str_replace('custom_field_', '', $social_mapping);
                        $custom_fields_data[$cf_id] = $lead['social_media'];
                    } else {
                        $lead_data[$social_mapping] = $lead['social_media'];
                    }
                }
            }

            // Map Rating
            if (!empty($lead['rating'])) {
                $rating_mapping = isset($field_mappings['rating']) ? $field_mappings['rating'] : '';

                if (!empty($rating_mapping)) {
                    if (strpos($rating_mapping, 'custom_field_') === 0) {
                        $cf_id = str_replace('custom_field_', '', $rating_mapping);
                        $custom_fields_data[$cf_id] = $lead['rating'];
                    } else {
                        $lead_data[$rating_mapping] = $lead['rating'];
                    }
                }
            }

            // Map Description
            if (!empty($lead['description'])) {
                $desc_mapping = isset($field_mappings['description']) ? $field_mappings['description'] : '';

                if (!empty($desc_mapping)) {
                    if (strpos($desc_mapping, 'custom_field_') === 0) {
                        $cf_id = str_replace('custom_field_', '', $desc_mapping);
                        $custom_fields_data[$cf_id] = $lead['description'];
                    } else {
                        $lead_data[$desc_mapping] = $lead['description'];
                    }
                }
            }

            // Add custom fields to lead_data in the format expected by handle_custom_fields_post
            if (!empty($custom_fields_data)) {
                $lead_data['custom_fields'] = ['leads' => $custom_fields_data];
            }

            // Check for duplicates
            $is_duplicate = false;
            $uniqueValidationFields = json_decode(get_option('lead_unique_validation'), true);
            if (is_array($uniqueValidationFields)) {
                foreach ($uniqueValidationFields as $field) {
                    if (!empty($lead_data[$field])) {
                        $this->db->where($field, $lead_data[$field]);
                        if ($this->db->count_all_results('tblleads') > 0) {
                            $is_duplicate = true;
                            break;
                        }
                    }
                }
            }

            if (!$is_duplicate) {
                $id = $this->leads_model->add($lead_data);
                if ($id) {
                    $count++;
                } else {
                    $errors[] = 'Failed to import: ' . ($lead['company'] ?? 'Unknown');
                }
            } else {
                $errors[] = 'Duplicate skipped: ' . ($lead['company'] ?? 'Unknown');
            }
        }

        echo json_encode([
            'success' => true,
            'imported' => $count,
            'total' => count($leads),
            'errors' => $errors
        ]);
    }


    public function ajax_get_error_engine()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();

                $result = $this->contactcenter_model->get_error_engine($data["id"]);

                $jSON["retorno"] = monta_html_engine_error($result);
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_change_phone()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();

                $result = $this->contactcenter_model->change_phone($data);

                $jSON["retorno"] = $result;
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_duplicate_engine()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();

                $result = $this->contactcenter_model->duplicate_engine($data);

                $jSON["retorno"] = $result;
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_replicate_engine()
    {
        $jSON = array('success' => false, 'count' => 0, 'message' => '');

        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $engine_id = $this->input->post('id');
                $devices = $this->input->post('devices');

                if (empty($engine_id) || empty($devices) || !is_array($devices)) {
                    $jSON['message'] = _l('invalid_parameters');
                    echo json_encode($jSON);
                    return;
                }

                $result = $this->contactcenter_model->replicate_engine($engine_id, $devices);

                if ($result['success']) {
                    $jSON['success'] = true;
                    $jSON['count'] = $result['count'];
                } else {
                    $jSON['message'] = $result['message'];
                }
            }
        }
        echo json_encode($jSON);
    }




    public function ajax_engine_show_leads()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();

                $result = $this->contactcenter_model->get_leads_engine($data["id"]);

                $jSON["retorno"] = monta_html_engine_leas($result);
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_valid_if_engine_will_run()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();

                $result = $this->contactcenter_model->valid_if_engine_will_run($data["id"]);

                $jSON["retorno"] = monta_html_valid_engine($result);
            }
        }
        echo json_encode($jSON);
    }


    public function contador()
    {
        $this->load->view('contactcenter/contador');
    }

    public function get_stuck_leads()
    {
        header('Content-Type: application/json');

        try {
            // Get date filter parameter (default: 7 days)
            $date_filter = $this->input->get('date_filter') ?: '7';
            $search_term = $this->input->get('search') ?: '';

            // Get custom field for leads_unidade
            $custom_fields = get_custom_fields('leads', ['slug' => 'leads_unidade', 'active' => 1]);

            if (empty($custom_fields)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Campo personalizado "leads_unidade" não encontrado'
                ]);
                return;
            }

            $field_id = $custom_fields[0]['id'];

            // Get all assigned campaigns from groups
            $this->db->distinct();
            $this->db->select('gitem_name');
            $assigned_campaigns_query = $this->db->get(db_prefix() . 'leads_grupo_item');
            $assigned_campaigns = [];
            if ($assigned_campaigns_query && $assigned_campaigns_query->num_rows() > 0) {
                foreach ($assigned_campaigns_query->result() as $row) {
                    if (!empty($row->gitem_name)) {
                        $assigned_campaigns[] = $row->gitem_name;
                    }
                }
            }

            if (empty($assigned_campaigns)) {
                // If no campaigns are assigned, all leads with the field are stuck
                $this->db->select('l.id, l.name, l.status, l.source, l.dateadded, c.value as campaign, s.name as status_name, src.name as source_name');
                $this->db->from(db_prefix() . 'leads l');
                $this->db->join(db_prefix() . 'customfieldsvalues c', 'l.id = c.relid AND c.fieldid = ' . $field_id, 'left');
                $this->db->join(db_prefix() . 'leads_status s', 'l.status = s.id', 'left');
                $this->db->join(db_prefix() . 'leads_sources src', 'l.source = src.id', 'left');
                $this->db->where('c.value !=', '');
                $this->db->where('c.value IS NOT NULL');
            } else {
                // Get leads with campaign values that are NOT in assigned campaigns
                $this->db->select('l.id, l.name, l.status, l.source, l.dateadded, c.value as campaign, s.name as status_name, src.name as source_name');
                $this->db->from(db_prefix() . 'leads l');
                $this->db->join(db_prefix() . 'customfieldsvalues c', 'l.id = c.relid AND c.fieldid = ' . $field_id, 'left');
                $this->db->join(db_prefix() . 'leads_status s', 'l.status = s.id', 'left');
                $this->db->join(db_prefix() . 'leads_sources src', 'l.source = src.id', 'left');
                $this->db->where('c.value !=', '');
                $this->db->where('c.value IS NOT NULL');
                $this->db->where_not_in('c.value', $assigned_campaigns);
            }

            // Apply date filter
            if ($date_filter !== 'all') {
                $days = (int)$date_filter;
                if ($days === 1) {
                    // Today only
                    $this->db->where('DATE(l.dateadded)', date('Y-m-d'));
                } else {
                    // Last N days
                    $this->db->where('l.dateadded >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
                }
            }

            // Apply search filter
            if (!empty($search_term)) {
                $this->db->group_start();
                $this->db->like('l.name', $search_term);
                $this->db->or_like('l.id', $search_term);
                $this->db->group_end();
            }

            $this->db->order_by('l.id', 'DESC');
            $this->db->limit(500); // Increased limit since we have date filtering

            $query = $this->db->get();
            $stuck_leads = $query->result_array();

            echo json_encode([
                'success' => true,
                'stuck_leads' => $stuck_leads,
                'count' => count($stuck_leads)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ]);
        }
    }

    public function quick_assign_campaign()
    {
        header('Content-Type: application/json');

        if (!$this->input->post('campaign') || !$this->input->post('group_id')) {
            echo json_encode([
                'success' => false,
                'message' => 'Campanha e grupo são obrigatórios'
            ]);
            return;
        }

        try {
            $campaign = $this->input->post('campaign');
            $group_id = intval($this->input->post('group_id'));

            // Validate group exists
            $this->db->where('grup_id', $group_id);
            $group = $this->db->get(db_prefix() . 'leads_grupo')->row();
            if (!$group) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Grupo não encontrado'
                ]);
                return;
            }

            // Check if already assigned
            $this->db->where('grup_id', $group_id);
            $this->db->where('gitem_name', $campaign);
            $existing = $this->db->get(db_prefix() . 'leads_grupo_item')->row();

            if ($existing) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Esta campanha já está atribuída a este grupo'
                ]);
                return;
            }

            // Assign campaign to group
            $data = [
                'grup_id' => $group_id,
                'gitem_name' => $campaign
            ];
            $this->db->insert(db_prefix() . 'leads_grupo_item', $data);

            if ($this->db->affected_rows() > 0) {
                // Immediately process stuck leads for this campaign
                $leads_updated = $this->contactcenter_model->process_stuck_leads_for_campaign($campaign, $group_id);

                log_activity("Quick assign campaign - Campaign: {$campaign} assigned to Group ID: {$group_id}, Leads updated: {$leads_updated}", get_staff_user_id());

                echo json_encode([
                    'success' => true,
                    'message' => 'Campanha atribuída com sucesso',
                    'leads_updated' => $leads_updated
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro ao atribuir campanha'
                ]);
            }
        } catch (Exception $e) {
            log_activity("Quick assign campaign ERROR - " . $e->getMessage(), get_staff_user_id());
            echo json_encode([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ]);
        }
    }

    public function chatweb()
    {
        $data['leads_sources'] = $this->leads_model->get_source();
        $data['members'] = $this->staff_model->get('', ['active' => 1]);
        $data['statuses'] = $this->leads_model->get_status();
        $data['chatweb'] = $this->chatweb->get_chatweb();
        $data['assistants'] = $this->contactcenter_model->get_assistants_ai();
        $this->load->view('contactcenter/chatweb', $data);
    }

    public function add_chatweb()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $response = $this->chatweb->add_chatweb($data);
            if ($response) {
                log_activity('Criou chatweb [' . $this->input->post('chat_name') . ']', get_staff_user_id());
                set_alert('success', _l('contac_save'));
                redirect(admin_url('contactcenter/chatweb'));
            } else {
                set_alert('danger', _l("contac_save_error"));
                redirect(admin_url('contactcenter/chatweb'));
            }
        } else {
            show_404();
        }
    }

    public function transferir_leads()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $response = $this->contactcenter_model->transferir_leads($data);
            if ($response) {
                log_activity('Transferiu leads [' . $this->input->post('phonenumber_trans') . '] para o usuario ' . get_staff_full_name($data['staffid_to']), get_staff_user_id());
                set_alert('success', _l('contact_transfer_save'));
            }

            if ($data["dev_id"]) {
                redirect(admin_url("contactcenter/chatsingle/{$data['dev_id']}"));
            } else {
                redirect(admin_url("contactcenter/chattransfer"));
            }
        } else {
            show_404();
        }
    }

    /**
     * Ajax de aceitar o contato
     */
    public function ajax_accept_contact()
    {

        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->ajax_accept_contact($data);
                if ($result) {
                    $jSON["result"] = true;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function leads_engine()
    {
        $data['statuses'] = $this->leads_model->get_status();
        $data['devices'] = $this->contactcenter_model->get_device_by_type(2);

        // Get tags array for tag filtering
        $data['tagsArray'] = $this->db->get(db_prefix() . 'tags')->result_array();

        // Get filter preference from session
        $show_inactive = $this->session->userdata('leads_engine_show_inactive');
        $data['show_inactive'] = $show_inactive ? true : false;

        // Build filters array
        $filters = array();
        if (!$data['show_inactive']) {
            $filters['con_status'] = 1; // Only active
        }
        $filters['order_by'] = 'id DESC'; // Newest first

        $data["leads_engines"] = $this->contactcenter_model->get_leads_engine_conversation(null, $filters);
        $this->load->view('leads_engine', $data);
    }

    public function leads_engine_messages($id)
    {
        $data["conversation"] = $this->contactcenter_model->get_leads_engine_conversation($id);
        $data["list"] = $this->contactcenter_model->get_leads_engine_messages($id);

        // Load merge fields for display
        if (!class_exists('Leads_merge_fields', false)) {
            $this->load->library('merge_fields/leads_merge_fields');
        }

        // Get standard merge fields
        $merge_fields = $this->leads_merge_fields->build();

        // Add custom fields for leads
        $custom_fields = get_custom_fields('leads');
        foreach ($custom_fields as $field) {
            $merge_fields[] = [
                'name' => $field['name'] . ' (Custom Field)',
                'key' => '{' . $field['slug'] . '}',
                'available' => ['leads'],
            ];
        }

        $data['merge_fields'] = $merge_fields;

        $this->load->view('leads_engine_messages', $data);
    }

    public function add_leads_engine_messages()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $response = $this->contactcenter_model->add_leads_engine_messages($data);
            if ($response) {
                //log_activity('Criou menssagem  [' . $response . ']', get_staff_user_id());
                set_alert('success', _l('contac_save'));
                redirect(admin_url('contactcenter/leads_engine_messages/' . $data["contactcenter_leads_engine_id"]));
            } else {
                set_alert('danger', _l('contac_save_error'));
                redirect(admin_url('contactcenter/leads_engine_messages/' . $data["contactcenter_leads_engine_id"]));
            }
        } else {
            show_404();
        }
    }

    public function update_leads_engine_messages()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $message_id = $data["id"];
            unset($data["id"]);

            $response = $this->contactcenter_model->update_leads_engine_messages($message_id, $data);
            if ($response) {
                log_activity('Editou menssagem leads engine [' . $message_id . ']', get_staff_user_id());
                set_alert('success', _l('contac_save'));
                redirect(admin_url('contactcenter/leads_engine_messages/' . $data["contactcenter_leads_engine_id"]));
            } else {
                set_alert('danger', _l('contac_save_error'));
                redirect(admin_url('contactcenter/leads_engine_messages/' . $data["contactcenter_leads_engine_id"]));
            }
        } else {
            show_404();
        }
    }

    public function add_leads_engine()
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            // Handle checkbox values
            $data['is_warmup_active'] = isset($data['is_warmup_active']) ? 1 : 0;
            $data['stop_on_reply'] = isset($data['stop_on_reply']) ? 1 : 0;

            // Set default values if not provided
            if (!isset($data['daily_limit']) || empty($data['daily_limit'])) {
                $data['daily_limit'] = 1000;
            }
            if (!isset($data['batch_size']) || empty($data['batch_size'])) {
                $data['batch_size'] = 5;
            }
            if (!isset($data['batch_cooldown']) || empty($data['batch_cooldown'])) {
                $data['batch_cooldown'] = 5;
            }
            if (!isset($data['message_interval_min']) || empty($data['message_interval_min'])) {
                $data['message_interval_min'] = 1; // Default: 1 minute
            }
            if (!isset($data['message_interval_max']) || empty($data['message_interval_max'])) {
                $data['message_interval_max'] = 3; // Default: 3 minutes
            }

            // Set warmup_start_date if warmup is activated for the first time
            if ($data['is_warmup_active'] == 1 && !empty($data['id'])) {
                $existing = $this->contactcenter_model->get_leads_engine_conversation($data['id']);
                if ($existing && empty($existing->warmup_start_date)) {
                    $data['warmup_start_date'] = date('Y-m-d H:i:s');
                }
            } elseif ($data['is_warmup_active'] == 1 && empty($data['id'])) {
                $data['warmup_start_date'] = date('Y-m-d H:i:s');
            }

            // Handle advanced features checkboxes
            $data['vcard_enable'] = isset($data['vcard_enable']) ? 1 : 0;
            $data['inbound_bait_enable'] = isset($data['inbound_bait_enable']) ? 1 : 0;
            $data['safe_groups_enable'] = isset($data['safe_groups_enable']) ? 1 : 0;

            $response = $this->contactcenter_model->add_leads_engine($data);
            if ($response == true) {
                set_alert('success', _l('contac_save'));
                if ($data["id"]) {
                    log_activity('Editou conversation engine [' . $this->input->post('title') . ']', get_staff_user_id());
                    redirect(admin_url('contactcenter/leads_engine'));
                } else {
                    log_activity('Criou conversation engine [' . $this->input->post('title') . ']', get_staff_user_id());
                    redirect(admin_url('contactcenter/leads_engine'));
                }
            } else {
                set_alert('danger', _l('contac_save_error'));
                redirect(admin_url('contactcenter/leads_engine'));
            }
        } else {
            show_404();
        }
    }

    public function ajax_delete_leads_engine()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->delete_leads_engine($data);
                if ($device) {
                    $jSON["result"] = true;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_leads_engine_status()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->update_leads_status_engine($data);
                if ($device) {
                    $jSON["result"] = true;
                } else {
                    $jSON["result"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_bulk_leads_engine_status()
    {
        $jSON = array('success' => false, 'message' => '');

        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $ids = $this->input->post('ids');
                $status = intval($this->input->post('status'));

                // Handle 'all' campaigns
                if ($ids === 'all' || (is_array($ids) && count($ids) == 1 && $ids[0] === 'all')) {
                    // Get all leads engines
                    $all_engines = $this->contactcenter_model->get_leads_engine_conversation();
                    $ids = array();
                    if ($all_engines) {
                        if (is_array($all_engines)) {
                            foreach ($all_engines as $engine) {
                                if (isset($engine->id) && $engine->id > 0) {
                                    $ids[] = intval($engine->id);
                                }
                            }
                        } elseif (is_object($all_engines) && isset($all_engines->id)) {
                            // Single result
                            $ids[] = intval($all_engines->id);
                        }
                    }
                }

                // Ensure IDs are integers
                if (is_array($ids)) {
                    $ids = array_map('intval', $ids);
                    $ids = array_filter($ids); // Remove any invalid IDs
                    $ids = array_values($ids); // Re-index array
                }

                if (empty($ids) || !is_array($ids) || count($ids) == 0) {
                    $jSON['message'] = _l('no_campaigns_selected');
                    echo json_encode($jSON);
                    return;
                }

                $success_count = 0;
                $failed_count = 0;
                $failed_ids = array();

                foreach ($ids as $id) {
                    $id = intval($id);
                    if ($id <= 0) {
                        $failed_count++;
                        continue;
                    }

                    // Get current status - reset query first
                    $this->db->reset_query();
                    $this->db->where('id', $id);
                    $current = $this->db->get(db_prefix() . 'contactcenter_leads_engine')->row();

                    if (!$current) {
                        $failed_count++;
                        continue;
                    }

                    $current_status = intval($current->status);

                    // If already in desired state, count as success
                    if ($current_status == $status) {
                        $success_count++;
                        continue;
                    }

                    // For starting campaigns, check if messages exist
                    if ($status == 1) {
                        $data = array(
                            'id' => $id,
                            'status' => $status
                        );
                        $result = $this->contactcenter_model->update_leads_status_engine($data);
                        if ($result === true) {
                            $success_count++;
                        } else {
                            $failed_count++;
                            $failed_ids[] = $id;
                        }
                    } else {
                        // For stopping, we can always do it - no validation needed
                        $this->db->reset_query();
                        $this->db->where('id', $id);
                        $this->db->update(db_prefix() . 'contactcenter_leads_engine', array('status' => 0));

                        // Always count as success when stopping (no validation required)
                        // affected_rows might be 0 if already stopped, but that's still success
                        $success_count++;
                    }
                }

                if ($success_count > 0 || count($ids) > 0) {
                    $jSON['success'] = true;
                    if ($status == 1) {
                        $jSON['message'] = _l('campaigns_started_successfully');
                    } else {
                        $jSON['message'] = _l('campaigns_stopped_successfully');
                    }
                } else {
                    $jSON['message'] = _l('no_campaigns_updated');
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_duplicate_leads_engine()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();

                $result = $this->contactcenter_model->duplicate_leads_engine($data);

                $jSON["retorno"] = $result;
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_replicate_leads_engine()
    {
        $jSON = array('success' => false, 'count' => 0, 'message' => '');

        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $engine_id = $this->input->post('id');
                $devices = $this->input->post('devices');

                if (empty($engine_id) || empty($devices) || !is_array($devices)) {
                    $jSON['message'] = _l('invalid_parameters');
                    echo json_encode($jSON);
                    return;
                }

                $result = $this->contactcenter_model->replicate_leads_engine($engine_id, $devices);

                if ($result['success']) {
                    $jSON['success'] = true;
                    $jSON['count'] = $result['count'];
                } else {
                    $jSON['message'] = $result['message'];
                }
            }
        }
        echo json_encode($jSON);
    }

    public function update_leads_engine_filters()
    {
        $jSON = array('success' => false);

        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $show_inactive = $this->input->post('show_inactive_campaigns');
                $this->session->set_userdata('leads_engine_show_inactive', $show_inactive ? 1 : 0);
                $jSON['success'] = true;
            }
        }
        echo json_encode($jSON);
    }


    public function edit_leads_engine()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->get_leads_engine_conversation($data["id"]);
                if ($device) {
                    $jSON['id'] = $device->id;
                    $jSON['title'] = $device->title;
                    $jSON['leads_status'] = $device->leads_status;
                    $jSON['leads_status_final'] = $device->leads_status_final;
                    $jSON['start_time'] = $device->start_time;
                    $jSON['end_time'] = $device->end_time;
                    $jSON['hours_since_last_contact'] = $device->hours_since_last_contact;
                    $jSON['device_id'] = $device->device_id;
                    $jSON['fromMe'] = $device->fromMe;
                    $jSON['tags'] = isset($device->tags) && !empty($device->tags) ? explode(',', $device->tags) : [];
                    $jSON['campaign_tag_id'] = isset($device->campaign_tag_id) ? $device->campaign_tag_id : '';

                    // Safety Settings fields
                    $jSON['daily_limit'] = isset($device->daily_limit) ? $device->daily_limit : 1000;
                    $jSON['batch_size'] = isset($device->batch_size) ? $device->batch_size : 5;
                    $jSON['batch_cooldown'] = isset($device->batch_cooldown) ? $device->batch_cooldown : 5;
                    $jSON['message_interval_min'] = isset($device->message_interval_min) ? $device->message_interval_min : 1;
                    $jSON['message_interval_max'] = isset($device->message_interval_max) ? $device->message_interval_max : 3;
                    $jSON['is_warmup_active'] = isset($device->is_warmup_active) ? $device->is_warmup_active : 0;
                    $jSON['stop_on_reply'] = isset($device->stop_on_reply) ? $device->stop_on_reply : 1;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_leads_engine_message_order()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->ajax_leads_engine_message_order($data);
                if ($device) {
                    $jSON["result"] = true;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_delete_leads_engine_messages()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->delete_leads_engine_messages($data["id"]);
                if ($device) {
                    $jSON["result"] = true;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_leads_engine_show()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();

                $result = $this->contactcenter_model->show_leads_engine($data["id"]);

                $jSON["retorno"] = monta_html_leads_engine($result);
            }
        }
        echo json_encode($jSON);
    }


    public function automation($draw_id)
    {
        $drawflow = $this->chatbot->get_automation($draw_id);
        if (!$drawflow) {
            redirect(admin_url('contactcenter/fluxo'));
        }

        $data['fields']    = [
            'name',
            'email',
            'company',
            'address',
            'city',
            'state',
        ];
        $data['members'] = $this->staff_model->get();
        $data['custom_fields']    = get_custom_fields('leads', 'type != "link"');
        $data['title']          = _l('drawflow_page');
        $data['statuses'] = $this->leads_model->get_status();
        $data["data_automation"] = html_entity_decode($drawflow->data);
        $data["drawflow"] = $drawflow;
        $data["idInput"] = get_option("update_input_automation");

        $this->load->view('drawflow', $data);
    }

    public function fluxo()
    {
        $data['title']          = _l('drawflow_page');
        $data["drawflow"] = $this->chatbot->get_automation();
        $this->load->view('fluxo', $data);
    }


    public function fluxo_create()
    {
        $data['title']          = _l('drawflow_page');
        $id = $this->chatbot->fluxo_create();
        redirect(admin_url("contactcenter/automation/{$id}"));
    }



    public function save_automation()
    {
        $jSON = array();
        $jsonData = file_get_contents('php://input');
        $result = $this->chatbot->save_automation($jsonData);
        echo json_encode($jSON);
    }


    public function save_group_banco()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->chatbot->save_group_banco($data);
                if ($result) {
                    $jSON["retorno"] = true;
                } else {
                    $jSON["retorno"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function save_group_children()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->chatbot->save_group_children($data);
                if ($result["success"]) {
                    $jSON["retorno"] = true;
                    $jSON["url"] = $result["url"];
                } else {
                    $jSON["retorno"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function get_group_banco()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->chatbot->get_group_banco($data);

                if ($result) {
                    $jSON["retorno"] = true;
                    $jSON["gpt_caracters"] = $result->gpt_caracters;
                    $jSON["gpt_tag_exit"] = $result->gpt_tag_exit;
                    $jSON["gpt_prompt"] = $result->gpt_prompt;
                    $jSON["gpt_model"] = $result->gpt_model;
                } else {
                    $jSON["retorno"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function get_http_request()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->chatbot->get_http_request($data);

                if ($result) {
                    $jSON["http_request"] = $result->text;
                }
            }
        }
        echo json_encode($jSON);
    }
    public function delete_fluxo()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->chatbot->delete_fluxo($data);
                if ($result) {
                    $jSON["result"] = true;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function get_static_fluxo()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->chatbot->get_static_fluxo($data);
                if ($result) {
                    $jSON["result"] = $result;
                }
            }
        }
        echo json_encode($jSON);
    }
    public function get_count_leads()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->chatbot->get_count_leads($data);
                if ($result) {
                    $jSON["result"] = $result;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function export_drawflow()
    {
        $response = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->chatbot->export_drawflow($data);

                // Decodifique a string JSON para garantir que está manipulando dados JSON
                $decoded_result = json_decode($result, true);

                if ($decoded_result) {
                    $response["filename"] = $decoded_result['filename'];
                    $response["data"] = $decoded_result['data'];
                }
            }
        }

        // Retorne a resposta como JSON
        echo json_encode($response);
    }


    public function import_drawflow()
    {
        if ($this->input->post() && !empty($_FILES['file']['tmp_name'])) {
            $draw_id = $this->input->post('draw_id');
            $file = $_FILES['file']['tmp_name'];

            // Lê o conteúdo do arquivo
            $encrypted_data = file_get_contents($file);

            // Chama a função de importação com o draw_id e os dados do arquivo
            $result = $this->chatbot->import_drawflow($encrypted_data, $draw_id);

            if ($result) {
                // Sucesso                
                set_alert('success', "'Importação bem-sucedida.");
                redirect(admin_url("contactcenter/automation/{$draw_id}"));
            } else {
                // Falha
                set_alert('danger', "Falha na importação.");
                redirect(admin_url("contactcenter/automation/{$draw_id}"));
            }
        } else {
            // Dados ou arquivo ausentes
            set_alert('danger', "Dados ou arquivo ausentes.");
            redirect(admin_url("contactcenter/fluxo"));
        }
    }


    public function meta()
    {
        $data['title']          = _l('contactcenter_meta');

        // Get filter parameters
        $filters = [
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to'),
            'status' => $this->input->get('status'),
            'source' => $this->input->get('source'),
            'assigned' => $this->input->get('assigned')
        ];

        $data["meta_analytics"] = $this->contactcenter_model->get_meta_analytics($filters);

        // Get data for filter dropdowns
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['staff_members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['filters'] = $filters;


        //         $url = "https://graph.facebook.com/v18.0/me/adaccounts?fields=id,name&limit=1000";
        //         $data["meta_account"] = $this->contactcenter_model->getFacebookData($url);

        //         $adAccountId = $data["meta_account"]["data"][25]["id"];
        //         $url = "https://graph.facebook.com/v18.0/$adAccountId/campaigns?fields=id,name,status,objective,created_time,budget_remaining,daily_budget&limit,lifetime_budget&limit=1000";
        //         $data["meta_camp"] = $this->contactcenter_model->getFacebookData($url);


        //         $campaignId = $data["meta_camp"]["data"][5]["id"];; // ID da campanha específica
        //         $url = "https://graph.facebook.com/v22.0/$campaignId/insights?fields=impressions,clicks,spend,reach,cpc,ctr,conversions,cost_per_conversion,frequency,objective,actions,cost_per_action_type&limit=1000";       
        //         $data["meta_camp_capem"] = $this->contactcenter_model->getFacebookData($url);


        //         $url = "https://graph.facebook.com/v22.0/$campaignId/adsets?fields=id,name,status,daily_budget,lifetime_budget,bid_strategy,targeting,start_time,end_time";
        //         $data["meta_camp_adset"] = $this->contactcenter_model->getFacebookData($url);

        //         $adSetId = $data["meta_camp_adset"]["data"][0]["id"];
        //         $url = "https://graph.facebook.com/v22.0/$adSetId/ads?fields=id,name,status,creative,effective_status,adset_id,campaign_id";        
        //         $data["meta_camp_ad"] = $this->contactcenter_model->getFacebookData($url);

        //         //pega direto o id do anuncio
        //         $url = "https://graph.facebook.com/v22.0/120217035517660634?fields=id,name,status,creative,effective_status,adset_id,campaign_id";
        //         $data["meta_camp_ad_id"] = $this->contactcenter_model->getFacebookData($url);


        // echo "<pre>";
        // //print_r($data["meta_account"]);
        // //print_r($data["meta_camp"]);
        // //print_r($data["meta_camp_capem"]);
        // //print_r($data["meta_camp_adset"]["data"][0]);
        // //print_r($data["meta_camp_ad"]);
        // print_r($data["meta_camp_ad_id"]);
        // echo "</pre>";


        $this->load->view('meta', $data);
    }

    public function meta_analytics($id = null)
    {
        $data['title']          = _l('contactcenter_meta');

        // Get filter parameters from URL (passed from meta page or set here)
        $filters = [
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to'),
            'status' => $this->input->get('status'),
            'source' => $this->input->get('source'),
            'assigned' => $this->input->get('assigned')
        ];

        $data["meta_analytics"] = $this->contactcenter_model->get_leads_meta_analytics($id, $filters);
        $data["chart_meta_dia"] = $this->contactcenter_model->conta_meta_por_dia($id, $filters);
        $data["chart_meta_status"] = $this->contactcenter_model->conta_status_meta($id, $filters);
        $data["meta_analytics_id"] = $id;

        // Get data for filter dropdowns
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['staff_members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['filters'] = $filters;

        $this->load->view('meta_analytics', $data);
    }

    public function linkscustom()
    {

        if ($this->input->post()) {
            $data = $this->input->post();
            $result = $this->contactcenter_model->insert_links_custom($data);
            if ($result) {
                set_alert('success', _l('contac_save'));
                redirect(admin_url('contactcenter/linkscustom'));
            } else {
                set_alert('danger', _l('contac_save_error'));
                redirect(admin_url('contactcenter/linkscustom'));
            }
        }

        $data['links'] = $this->contactcenter_model->get_links_custom();
        $data['title'] = _l('links_personalizados');
        $this->load->view('links', $data);
    }

    public function number_health()
    {
        if (!has_permission('contactcenter', '', 'view')) {
            access_denied('contactcenter');
        }

        // Update health scores for all devices
        $this->contactcenter_model->update_device_health_scores();

        $data['devices'] = $this->contactcenter_model->get_devices_with_health();
        $data['scripts'] = $this->contactcenter_model->get_maturation_scripts();

        // Get safe groups
        $this->db->where('is_active', 1);
        $data['safe_groups'] = $this->db->get(db_prefix() . 'contactcenter_safe_groups')->result();

        // Get device pairs
        $this->db->order_by('priority', 'DESC');
        $this->db->order_by('date_created', 'ASC');
        $data['device_pairs'] = $this->db->get(db_prefix() . 'contactcenter_device_pairs')->result();

        $data['title'] = _l('number_health_title');
        $this->load->view('number_health', $data);
    }

    public function generate_wa_me_link()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->get_device($data["device_id"]);
                if ($device && $device->dev_number) {
                    $phone = preg_replace('/[^0-9]/', '', $device->dev_number);
                    $text = isset($data['text']) ? urlencode($data['text']) : '';
                    $jSON['link'] = "https://wa.me/{$phone}" . ($text ? "?text={$text}" : '');
                    $jSON['success'] = true;
                } else {
                    $jSON['success'] = false;
                    $jSON['message'] = 'Device not found or number not available';
                }
            }
        }
        echo json_encode($jSON);
    }

    public function add_maturation_script()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $result = $this->contactcenter_model->add_maturation_script($data);
            if ($result) {
                set_alert('success', _l('contac_save'));
            } else {
                set_alert('danger', _l('contac_save_error'));
            }
            redirect(admin_url('contactcenter/number_health'));
        }
    }

    public function delete_maturation_script()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->delete_maturation_script($data["id"]);
                $jSON['success'] = $result;
            }
        }
        echo json_encode($jSON);
    }

    public function add_safe_group()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['is_active'] = 1;

            $result = $this->contactcenter_model->add_safe_group($data);
            if ($result) {
                set_alert('success', _l('contac_save'));
            } else {
                set_alert('danger', _l('contac_save_error'));
            }
            redirect(admin_url('contactcenter/number_health'));
        }
    }

    public function delete_safe_group()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->delete_safe_group($data["id"]);
                $jSON['success'] = $result;
            }
        }
        echo json_encode($jSON);
    }

    public function quick_warmup()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->quick_warmup_device($data["device_id"]);
                if ($result['success']) {
                    $jSON['success'] = true;
                    $jSON['message'] = $result['message'];
                } else {
                    $jSON['success'] = false;
                    $jSON['message'] = $result['message'];
                }
            }
        }
        echo json_encode($jSON);
    }

    public function add_device_pair()
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            // Validate that devices are different
            if ($data['device_a_id'] == $data['device_b_id']) {
                set_alert('danger', 'Os dispositivos devem ser diferentes');
                redirect(admin_url('contactcenter/number_health'));
                return;
            }

            // Ensure device_a_id is always smaller than device_b_id to avoid duplicates
            if ($data['device_a_id'] > $data['device_b_id']) {
                $temp = $data['device_a_id'];
                $data['device_a_id'] = $data['device_b_id'];
                $data['device_b_id'] = $temp;
            }

            $data['date_created'] = date('Y-m-d H:i:s');
            $data['is_active'] = 1;
            if (!isset($data['priority'])) {
                $data['priority'] = 0;
            }

            $result = $this->contactcenter_model->add_device_pair($data);
            if ($result) {
                set_alert('success', _l('contac_save'));
            } else {
                set_alert('danger', 'Par já existe ou erro ao salvar');
            }
            redirect(admin_url('contactcenter/number_health'));
        }
    }

    public function delete_device_pair()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->delete_device_pair($data["id"]);
                $jSON['success'] = $result;
            }
        }
        echo json_encode($jSON);
    }

    public function toggle_device_pair()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->toggle_device_pair($data["id"], $data["is_active"]);
                $jSON['success'] = $result;
            }
        }
        echo json_encode($jSON);
    }



    public function delete_link()
    {

        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result =  $this->contactcenter_model->delete_links_custom($data);
                if ($result) {
                    set_alert('success', _l('contac_deleted'));
                    $jSON["result"] = true;
                }
            }
        }

        // Retorne a resposta como JSON
        echo json_encode($jSON);
    }

    public function restart_device()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result =  $this->contactcenter_model->restart_device($data);
                if ($result) {

                    $jSON["result"] = true;
                }
            }
        }

        // Retorne a resposta como JSON
        echo json_encode($jSON);
    }

    public function get_contact_websocket()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result =  $this->contactcenter_model->get_contact_websocket($data["id"]);
                if ($result) {
                    $jSON["contact"] = $result;
                } else {
                    $jSON["contact"] = false;
                }
            }
        }
        // Retorne a resposta como JSON
        echo json_encode($jSON);
    }

    public function ajax_time_ia()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $device = $this->contactcenter_model->ajax_time_ia($data);
                $jSON["message"] = _l('contac_save');
            }
        }
        echo json_encode($jSON);
    }


    public function messenger()
    {
        $pages = json_decode(get_option('facebook_pages'), true);
        $monitorando = json_decode(get_option('subscribed_pages'));

        // Obter o código salvo
        $appId = get_option('appId');

        // Novo array para armazenar os resultados
        $filteredPages = [];

        // Itera sobre o primeiro array ($pages)
        foreach ($monitorando as $id) {
            // Remove o código específico do ID
            $cleanId = str_replace($appId, '', $id);

            // Itera sobre o segundo array ($data['pages'])
            foreach ($pages as $page) {
                // Compara o ID limpo com o ID do array 2
                if ($page['id'] === $cleanId) {
                    $filteredPages[] = $page; // Adiciona ao resultado
                }
            }
        }

        $data['pages'] = $filteredPages;

        $data['title'] = _l('contac_messenger_title');
        $this->load->view('messenger/manager', $data);
    }

    public function messengerchat($id)
    {
        $filteredPages = [];
        $pages = json_decode(get_option('facebook_pages'), true);
        foreach ($pages as $page) {
            if ($page['id'] === $id) {
                $filteredPages[] = $page;
            }
        }
        if (count($filteredPages) > 0) {
            $data['pages'] = $filteredPages;
        } else {
            redirect(admin_url('contactcenter/messenger'));
        }
        $data['LabelContacts'] = $this->contactcenter_model->get_contact_messenger(['page_id' => $id]);
        $data['statuses'] = $this->leads_model->get_status();
        $data['title'] = _l('contac_messenger_title');
        $this->load->view('messenger/chat', $data);
    }

    public function ajax_get_contact_messenger()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result =  $this->contactcenter_model->get_contact_messenger($data);
                if ($result) {
                    $jSON["retorno"] = monta_html_contact_messenger($result);
                } else {
                    $jSON["retorno"] = false;
                }
            }
        }
        // Retorne a resposta como JSON
        echo json_encode($jSON);
    }

    public function ajax_get_message_meta()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();

                $active_leads_funnel = $this->app_modules->is_inactive("leads_funnel");
                if (!$active_leads_funnel) {
                    $progress = get_status_leads_messenger($data["sender_id"]);
                    $jSON["progress"] = $progress;
                }
                $leads = $this->contactcenter_model->get_dados_leads_sender_id($data["sender_id"]);
                $result = $this->contactcenter_model->get_messages_meta($data, $isRead = true);

                if ($result) {
                    $jSON["paginadorChat"] = $result[0]->msg_id;
                    $jSON["retorno"] = monta_html_chat_messenger($result);
                } else {
                    $jSON["retorno"] = monta_html_chat_messenger($result);
                }
                $jSON["id"] = $leads->id;
                $jSON["name"] = $leads->name;
                $jSON["phonenumber"] = $leads->phonenumber;
                $jSON["gpt_status"] = $leads->gpt_status;
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_get_chat_messenger_paginador()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->get_messages_meta($data, $isRead = true);
                if ($result) {
                    $jSON["paginadorChat"] = $result[0]->messenger_id;
                    $jSON["retorno"] = monta_html_chat_messenger($result);
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_get_search_chat_messenger()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->ajax_get_search_chat_messenger($data);
                if ($result) {
                    $jSON["search"] =  $result;
                }
            }
        }
        echo json_encode($jSON);
    }
    public function ajax_off_ai_all_messenger()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $active = get_option('active_ia_messenger_Facebook');
                if ($active == 0) {
                    update_option('active_ia_messenger_Facebook', 1);
                    $jSON["retorno"] = true;
                } else {
                    update_option('active_ia_messenger_Facebook', 0);
                    $jSON["retorno"] = true;
                }
            }
        }
        echo json_encode($jSON);
    }
    public function ajax_send_msg_messenger()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $active_facebookleadsintegration = $this->app_modules->is_inactive("facebookleadsintegration");
                if (!$active_facebookleadsintegration) {
                    $data = $this->input->post();
                    $result = $this->contactcenter_model->ajax_send_msg_messenger($data);
                    if ($result["status"] == "error" && $result["code"] === 10) {
                        $jSON["error"] =  "Essa mensagem foi enviada fora do espaço de tempo permitido.";
                    } else {
                        $jSON["retorno"] = monta_html_chat_messenger($result["data"]);
                    }
                } else {
                    $jSON["active_modules"] =  false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function get_contact_messenger_pusher()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $active_facebookleadsintegration = $this->app_modules->is_inactive("facebookleadsintegration");
                if (!$active_facebookleadsintegration) {
                    $data = $this->input->post();
                    $result = $this->contactcenter_model->get_contact_messenger_pusher($data);
                    if ($result) {
                        $jSON["retorno"] = monta_html_contact_messenger($result);
                    }
                } else {
                    $jSON["active_modules"] =  false;
                }
            }
        }
        echo json_encode($jSON);
    }
    public function ajax_clear_threads_leads()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->clear_theads($data);
                if ($result) {
                    $jSON["retorno"] = true;
                } else {
                    $jSON["retorno"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_create_variacoe_openai()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->ajax_create_variacoe_openai($data);
                if ($result) {
                    $jSON["retorno"] = $result;
                } else {
                    $jSON["retorno"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function teste()
    {
        $this->load->view('teste');
    }

    public function msgspeed($device_id)
    {
        if (!$device_id) {
            show_404();
        }

        $device = $this->contactcenter_model->get_device($device_id);
        if (!$device) {
            show_404();
        }

        $data['msgspeed'] = $this->contactcenter_model->get_msgspeed($device_id);
        $data['device_id'] = $device_id;
        $this->load->view('msgspeed', $data);
    }

    public function save_msgspeed()
    {

        if ($this->input->post()) {
            $data = $this->input->post();
            $result = $this->contactcenter_model->save_msgspeed($data);
            if ($result) {
                set_alert('success', _l('contac_save'));
            } else {
                set_alert('danger', _l('contac_save_error'));
            }
            redirect(admin_url('contactcenter/msgspeed/' . $data['device_id']));
        }
    }

    public function delete_msgspeed()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->delete_msgspeed($data);
                if ($result) {
                    set_alert('success', _l('contac_deleted'));
                    $jSON["retorno"] = true;
                } else {
                    set_alert('danger', _l('contac_save_error_delete'));
                    $jSON["retorno"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_search_msgspeed()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->get_msgspeed_search($data);
                if ($result) {
                    $jSON["retorno"] = $result;
                } else {
                    $jSON["retorno"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_delete_msg_whats()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->ajax_delete_msg_whats($data);
                if ($result) {
                    $jSON["retorno"] = true;
                } else {
                    $jSON["retorno"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function edit_contact()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->edit_contact($data);
                if ($result) {
                    $jSON["retorno"] = true;
                    $jSON["message"] = _l('contac_save');
                } else {
                    $jSON["retorno"] = false;
                    $jSON["message"] = _l('contac_save_error');
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_get_file_contact()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->get_file_contact($data);
                if ($result) {
                    $jSON["retorno"] = $result;
                } else {
                    $jSON["retorno"] = false;
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_save_lead_birthday()
    {
        $jSON = array('success' => false);
        if ($this->input->is_ajax_request() && $this->input->post()) {
            $lead_id = (int) $this->input->post('lead_id');
            $field_id = (int) $this->input->post('field_id');
            $value = $this->input->post('value');
            if ($lead_id > 0 && $field_id > 0) {
                $this->load->helper('custom_fields');
                $custom_fields = array('leads' => array($field_id => $value ?: ''));
                handle_custom_fields_post($lead_id, $custom_fields);
                $jSON['success'] = true;
            }
        }
        echo json_encode($jSON);
    }


    public function themes_change($id)
    {
        $theme = $this->session->userdata("contactcenter_themes_change");
        if ($theme == 1) {
            $data = $this->session->set_userdata("contactcenter_themes_change", 0);
        } else {
            $data = $this->session->set_userdata("contactcenter_themes_change", 1);
        }

        redirect(admin_url('contactcenter/chatsingle/' . $id));
    }

    /**
     * Mark chat as read/unread
     */
    public function ajax_mark_chat_read_status()
    {
        $jSON = array('success' => false);
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $contact_id = isset($data['contact_id']) ? (int)$data['contact_id'] : 0;
                $marked_read = isset($data['marked_read']) ? (int)$data['marked_read'] : 0;

                // If contact_id is 0, try to find by phonenumber and token (don't create - contact should exist)
                if ($contact_id <= 0 && isset($data['phonenumber']) && isset($data['token'])) {
                    // Try using the model method if available
                    if (method_exists($this->contactcenter_model, 'get_contact_by_phone_and_token')) {
                        $contact = $this->contactcenter_model->get_contact_by_phone_and_token($data['phonenumber'], $data['token']);
                        if ($contact) {
                            $contact_id = (int)$contact->id;
                        }
                    }

                    // If still not found, try direct query
                    if ($contact_id <= 0) {
                        $this->db->where('phonenumber', $data['phonenumber']);
                        $this->db->where('session', $data['token']);
                        $contact = $this->db->get(db_prefix() . 'contactcenter_contact')->row();

                        if ($contact) {
                            $contact_id = (int)$contact->id;
                        }
                    }

                    // If still not found, try by phone number only (maybe session changed)
                    if ($contact_id <= 0) {
                        $this->db->where('phonenumber', $data['phonenumber']);
                        $this->db->order_by('id', 'DESC');
                        $contact = $this->db->get(db_prefix() . 'contactcenter_contact')->row();

                        if ($contact) {
                            $contact_id = (int)$contact->id;
                            // Update the session to match
                            $this->db->where('id', $contact_id);
                            $this->db->update(db_prefix() . 'contactcenter_contact', ['session' => $data['token']]);
                        }
                    }
                }

                if ($contact_id > 0) {
                    // Check if column exists before updating
                    if ($this->db->field_exists('chat_marked_read', db_prefix() . 'contactcenter_contact')) {
                        $this->db->where('id', $contact_id);
                        $this->db->update(db_prefix() . 'contactcenter_contact', ['chat_marked_read' => $marked_read]);

                        // Check for database errors
                        $error = $this->db->error();
                        if (!empty($error['message'])) {
                            // Database error occurred
                            log_message('error', 'Mark chat read status - Database error: ' . json_encode($error));
                            $jSON['message'] = _l('error_updating_chat_status');
                        } else {
                            // Update was successful (affected_rows can be 0 if value was already set, which is OK)
                            $jSON['success'] = true;
                            $jSON['contact_id'] = $contact_id;
                            $jSON['message'] = $marked_read ? _l('chat_marked_as_read') : _l('chat_marked_as_unread');
                        }
                    } else {
                        // Column doesn't exist - log and return error
                        log_message('error', 'Mark chat read status - Column chat_marked_read does not exist');
                        $jSON['message'] = _l('error_updating_chat_status');
                    }
                } else {
                    // Log the error for debugging
                    log_message('error', 'Mark chat read status failed - Contact not found. Phone: ' . (isset($data['phonenumber']) ? $data['phonenumber'] : 'N/A') . ', Token: ' . (isset($data['token']) ? $data['token'] : 'N/A'));
                    $jSON['message'] = _l('invalid_contact_id');
                }
            }
        }
        echo json_encode($jSON);
    }

    public function ajax_get_or_create_contact_id()
    {
        $jSON = array('success' => false);
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $phonenumber = isset($data['phonenumber']) ? $data['phonenumber'] : '';
                $token = isset($data['token']) ? $data['token'] : '';

                if ($phonenumber && $token) {
                    $this->db->where('phonenumber', $phonenumber);
                    $this->db->where('session', $token);
                    $contact = $this->db->get(db_prefix() . 'contactcenter_contact')->row();

                    if ($contact) {
                        $jSON['success'] = true;
                        $jSON['contact_id'] = (int)$contact->id;
                    } else {
                        // Create the contact
                        $insert_data = [
                            'phonenumber' => $phonenumber,
                            'session' => $token,
                            'name' => isset($data['name']) ? $data['name'] : '',
                            'date' => date('Y-m-d H:i:s'),
                            'chat_marked_read' => 0
                        ];
                        if (isset($data['leadid'])) {
                            $insert_data['leadid'] = $data['leadid'];
                        }
                        $this->db->insert(db_prefix() . 'contactcenter_contact', $insert_data);
                        $contact_id = $this->db->insert_id();

                        if ($contact_id > 0) {
                            $jSON['success'] = true;
                            $jSON['contact_id'] = $contact_id;
                        }
                    }
                }
            }
        }
        echo json_encode($jSON);
    }


    public function reload_device()
    {
        $jSON = array();

        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $result = $this->contactcenter_model->reload_device($data);

                if ($result->status == 404) {

                    $jSON["success"] = false;

                    // Verifica se existe a mensagem de erro
                    if (isset($result->response->message[0])) {
                        $jSON["message"] = $result->response->message[0];
                    } elseif (isset($result->error)) {
                        $jSON["message"] = $result->error;
                    } else {
                        $jSON["message"] = _l('contac_save_error');
                    }
                } else {
                    $jSON["success"] = true;
                    $jSON["message"] = _l('contac_reload_success');
                }
            }
        }

        echo json_encode($jSON);
    }

    /**
     * Message Triggers Management
     */
    public function message_triggers()
    {
        if (!has_permission('contactcenter', '', 'view')) {
            access_denied('contactcenter');
        }

        $data['title'] = _l('message_triggers');
        $data['triggers'] = $this->contactcenter_model->get_message_triggers();
        $data['lead_statuses'] = $this->leads_model->get_status();
        $data['lead_sources'] = $this->leads_model->get_source();
        $data['staff_members'] = $this->staff_model->get('', ['active' => 1]);
        $data['tags'] = get_tags();
        $data['custom_fields'] = get_custom_fields('leads');

        $this->load->view('message_triggers', $data);
    }

    /**
     * Add or edit message trigger
     */
    public function add_message_trigger()
    {
        if (!has_permission('contactcenter', '', 'create')) {
            access_denied('contactcenter');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['created_by'] = get_staff_user_id();
            $data['datecreated'] = date('Y-m-d H:i:s');

            if (isset($data['id']) && !empty($data['id'])) {
                // Update
                $id = $data['id'];
                unset($data['id']);
                $data['dateupdated'] = date('Y-m-d H:i:s');
                $result = $this->contactcenter_model->update_message_trigger($id, $data);
            } else {
                // Insert
                $result = $this->contactcenter_model->add_message_trigger($data);
            }

            if ($result) {
                set_alert('success', _l('message_trigger_saved_successfully'));
            } else {
                set_alert('danger', _l('message_trigger_save_failed'));
            }

            redirect(admin_url('contactcenter/message_triggers'));
        }
    }

    /**
     * Get trigger data for editing
     */
    public function get_message_trigger($id)
    {
        if (!has_permission('contactcenter', '', 'view')) {
            access_denied('contactcenter');
        }

        $trigger = $this->contactcenter_model->get_message_trigger($id);
        if ($trigger) {
            echo json_encode($trigger);
        } else {
            echo json_encode(['error' => 'Trigger not found']);
        }
    }

    /**
     * Delete message trigger
     */
    public function delete_message_trigger($id)
    {
        if (!has_permission('contactcenter', '', 'delete')) {
            access_denied('contactcenter');
        }

        $result = $this->contactcenter_model->delete_message_trigger($id);
        if ($result) {
            set_alert('success', _l('message_trigger_deleted_successfully'));
        } else {
            set_alert('danger', _l('message_trigger_delete_failed'));
        }

        redirect(admin_url('contactcenter/message_triggers'));
    }

    /**
     * Update conversation engine filters
     */
    public function update_conversation_engine_filters()
    {
        $show_inactive = $this->input->post('show_inactive') ? true : false;
        $this->session->set_userdata('conversation_engine_show_inactive', $show_inactive);

        echo json_encode(['success' => true]);
    }

    /**
     * Toggle trigger active status
     */
    public function toggle_message_trigger($id)
    {
        if (!has_permission('contactcenter', '', 'edit')) {
            access_denied('contactcenter');
        }

        $result = $this->contactcenter_model->toggle_message_trigger($id);
        if ($result) {
            set_alert('success', _l('message_trigger_updated_successfully'));
        } else {
            set_alert('danger', _l('message_trigger_update_failed'));
        }

        redirect(admin_url('contactcenter/message_triggers'));
    }

    /**
     * Create new tag via AJAX
     */
    public function ajax_create_tag()
    {
        if (!has_permission('contactcenter', '', 'create')) {
            access_denied('contactcenter');
        }

        $tag_name = $this->input->post('tag_name');

        if (empty($tag_name)) {
            echo json_encode(['success' => false, 'message' => _l('tag_name_required')]);
            return;
        }

        $this->load->library('app_tags');

        // Check if tag already exists
        $existing_tag = get_tag_by_name($tag_name);
        if ($existing_tag) {
            echo json_encode([
                'success' => true,
                'tag_id' => $existing_tag->id,
                'tag_name' => $existing_tag->name,
                'message' => _l('tag_already_exists')
            ]);
            return;
        }

        // Create new tag
        $tag_id = $this->app_tags->create(['name' => trim($tag_name)]);

        if ($tag_id) {
            // Clear cache to refresh tags list
            $CI = &get_instance();
            $CI->app_object_cache->delete('db-tags-array');

            echo json_encode([
                'success' => true,
                'tag_id' => $tag_id,
                'tag_name' => trim($tag_name),
                'message' => _l('tag_created_successfully')
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('tag_creation_failed')]);
        }
    }

    /**
     * Ads Analytics - Main page (similar to meta page)
     */
    public function ads_analytics()
    {
        $data['title'] = 'Ads Analytics';

        // Get filter parameters
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        // Convert dates from DMY format (d/m/Y) to YYYY-MM-DD for database queries
        // Keep original format for display in view
        $filters = [
            'date_from' => !empty($date_from) ? $this->_convert_to_sql_date($date_from) : '',
            'date_to' => !empty($date_to) ? $this->_convert_to_sql_date($date_to) : '',
            'creative_id' => $this->input->get('creative_id'),
            'campaign_name' => $this->input->get('campaign_name'),
            'status' => $this->input->get('status'),
            'source' => $this->input->get('source'),
            'assigned' => $this->input->get('assigned')
        ];

        // Store original dates for display
        $filters['date_from_display'] = $date_from;
        $filters['date_to_display'] = $date_to;

        // Get all creatives with their analytics
        $data["creatives_analytics"] = $this->contactcenter_model->get_ads_analytics_creatives($filters);

        // Get data for filter dropdowns
        $data['creatives'] = $this->contactcenter_model->get_ads_creatives();
        $data['campaigns'] = $this->contactcenter_model->get_available_campaigns(); // From custom field
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['staff_members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['ad_sets'] = $this->contactcenter_model->get_ads_sets(); // Load ad sets
        $data['filters'] = $filters;

        $this->load->view('ads_analytics', $data);
    }

    /**
     * Ads Analytics - Ad Sets view
     */
    public function ads_analytics_sets()
    {
        $data['title'] = 'Ads Analytics - Ad Sets';

        // Get filter parameters
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        // Convert dates from DMY format (d/m/Y) to YYYY-MM-DD for database queries
        // Keep original format for display in view
        $filters = [
            'date_from' => !empty($date_from) ? $this->_convert_to_sql_date($date_from) : '',
            'date_to' => !empty($date_to) ? $this->_convert_to_sql_date($date_to) : '',
            'status' => $this->input->get('status'),
            'source' => $this->input->get('source'),
            'assigned' => $this->input->get('assigned')
        ];

        // Store original dates for display
        $filters['date_from_display'] = $date_from;
        $filters['date_to_display'] = $date_to;

        // Get all ad sets with their analytics
        $data["ad_sets_analytics"] = $this->contactcenter_model->get_ads_analytics_sets($filters);

        // Get data for filter dropdowns
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['staff_members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['filters'] = $filters;

        $this->load->view('ads_analytics_sets', $data);
    }

    /**
     * Convert date to SQL format (YYYY-MM-DD)
     * Handles multiple date formats including DD/MM/YYYY and DD-MM-YYYY
     * 
     * @param string $date
     * @return string|false SQL date in YYYY-MM-DD format or false if invalid
     */
    private function _convert_to_sql_date($date)
    {
        if (empty($date)) {
            return '';
        }

        $date = trim($date);

        // Check if it's already in YYYY-MM-DD format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $parts = explode('-', $date);
            $year = (int)$parts[0];
            $month = (int)$parts[1];
            $day = (int)$parts[2];

            if (checkdate($month, $day, $year)) {
                return $date; // Already in correct format
            }
            return '';
        }

        // Check if it's in DD-MM-YYYY format
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
            $parts = explode('-', $date);
            $day = (int)$parts[0];
            $month = (int)$parts[1];
            $year = (int)$parts[2];

            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
            return '';
        }

        // Check if it's in DD/MM/YYYY format (most common for Brazilian format)
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            $parts = explode('/', $date);
            $day = (int)$parts[0];
            $month = (int)$parts[1];
            $year = (int)$parts[2];

            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
            return '';
        }

        // Try to parse with strtotime as fallback
        $timestamp = strtotime($date);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return '';
    }

    /**
     * Ads Analytics - Detail page for a specific creative
     */
    public function ads_analytics_detail($creative_id = null)
    {
        $data['title'] = 'Ads Analytics - Creative Detail';

        if (!$creative_id) {
            set_alert('danger', 'Creative ID is required');
            redirect(admin_url('contactcenter/ads_analytics'));
        }

        // Get filter parameters
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        // Convert dates from DMY format (d/m/Y) to YYYY-MM-DD for database queries
        // Keep original format for display in view
        $filters = [
            'date_from' => !empty($date_from) ? $this->_convert_to_sql_date($date_from) : '',
            'date_to' => !empty($date_to) ? $this->_convert_to_sql_date($date_to) : '',
            'status' => $this->input->get('status'),
            'source' => $this->input->get('source'),
            'assigned' => $this->input->get('assigned')
        ];

        // Store original dates for display
        $filters['date_from_display'] = $date_from;
        $filters['date_to_display'] = $date_to;

        // Get creative details
        $data["creative"] = $this->contactcenter_model->get_ads_creative($creative_id);
        if (!$data["creative"]) {
            set_alert('danger', _l('ads_analytics_creative_not_found'));
            redirect(admin_url('contactcenter/ads_analytics'));
        }

        // Get leads for this creative
        $data["leads"] = $this->contactcenter_model->get_ads_creative_leads($creative_id, $filters);

        // Get charts data
        $data["chart_leads_daily"] = $this->contactcenter_model->get_ads_leads_by_day($creative_id, $filters);
        $data["chart_leads_status"] = $this->contactcenter_model->get_ads_leads_by_status($creative_id, $filters);

        // Get investment data
        $data["investment"] = $this->contactcenter_model->get_ads_creative_investment($creative_id);

        // Calculate CPL using the same lead count as displayed
        $data["total_invested"] = $this->contactcenter_model->get_ads_total_invested($creative_id, $filters);
        $data["cpl"] = $this->contactcenter_model->calculate_ads_cpl($creative_id, $filters, count($data["leads"]));

        // Get data for filter dropdowns
        $data['campaigns'] = $this->contactcenter_model->get_available_campaigns();
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['staff_members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['filters'] = $filters;
        $data['creative_id'] = $creative_id;

        $this->load->view('ads_analytics_detail', $data);
    }

    /**
     * Manage Creative - Add/Edit creative
     */
    public function manage_creative($id = null)
    {
        if ($this->input->post()) {
            $post_data = $this->input->post();
            $data = [
                'name' => $post_data['name'],
                'campaign_name' => $post_data['campaign_name'],
                'media_id' => $post_data['media_id'],
                'ad_set_id' => !empty($post_data['ad_set_id']) ? (int)$post_data['ad_set_id'] : null,
                'is_active' => isset($post_data['is_active']) ? (int)$post_data['is_active'] : 1,
                'staffid' => get_staff_user_id()
            ];

            if (!empty($id)) {
                // Update existing creative
                $result = $this->contactcenter_model->update_ads_creative($id, $data);
                $creative_id = $id;
            } else {
                // Create new creative
                $creative_id = $this->contactcenter_model->add_ads_creative($data);
                $result = $creative_id > 0;
            }

            if ($result && $creative_id) {
                // Save/Update investment if provided
                $budget_type = !empty($post_data['budget_type']) ? $post_data['budget_type'] : 'ad_set';

                if (!empty($post_data['daily_investment']) && !empty($post_data['start_date'])) {
                    // Convert dates from DMY format to SQL format
                    $start_date = $this->_convert_to_sql_date($post_data['start_date']);
                    $end_date = !empty($post_data['end_date']) ? $this->_convert_to_sql_date($post_data['end_date']) : null;

                    // Validate that date conversion was successful
                    if (empty($start_date)) {
                        set_alert('danger', 'Data de início inválida. Por favor, use o formato dd/mm/aaaa.');
                        redirect(admin_url('contactcenter/manage_creative' . (!empty($id) ? '/' . $id : '')));
                        return;
                    }

                    // Validate end_date if provided
                    if (!empty($post_data['end_date']) && empty($end_date)) {
                        set_alert('danger', 'Data de término inválida. Por favor, use o formato dd/mm/aaaa.');
                        redirect(admin_url('contactcenter/manage_creative' . (!empty($id) ? '/' . $id : '')));
                        return;
                    }

                    $investment_data = [
                        'budget_type' => $budget_type,
                        'daily_investment' => floatval($post_data['daily_investment']),
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'is_active' => 1,
                        'staffid' => get_staff_user_id()
                    ];

                    // Set creative_id or ad_set_id based on budget_type
                    if ($budget_type == 'creative') {
                        $investment_data['creative_id'] = $creative_id;
                        $investment_data['ad_set_id'] = null;

                        // Check if creative-level investment already exists
                        $existing_investment = $this->contactcenter_model->get_ads_creative_investment($creative_id);

                        // Also check if there's an ad_set investment that should be deactivated
                        // (but don't delete it as it might be used by other creatives)
                        if (!empty($data['ad_set_id'])) {
                            $ad_set_investment = $this->contactcenter_model->get_ads_set_investment($data['ad_set_id']);
                            // Note: We keep the ad_set investment active as other creatives might use it
                            // User should manage ad_set investments from the ad_set management page
                        }
                    } else if ($budget_type == 'ad_set' && !empty($data['ad_set_id'])) {
                        $investment_data['ad_set_id'] = $data['ad_set_id'];
                        $investment_data['creative_id'] = null;

                        // Check if ad_set-level investment already exists
                        $existing_investment = $this->contactcenter_model->get_ads_set_investment($data['ad_set_id']);

                        // If creative had its own investment, we should deactivate it
                        $creative_investment = $this->contactcenter_model->get_ads_creative_investment($creative_id);
                        if ($creative_investment) {
                            // Deactivate the creative-level investment since we're using ad_set level
                            $this->contactcenter_model->update_ads_investment($creative_investment->id, ['is_active' => 0]);
                        }
                    } else {
                        $existing_investment = null;
                    }

                    if ($existing_investment) {
                        // Update existing investment
                        $investment_result = $this->contactcenter_model->update_ads_investment($existing_investment->id, $investment_data);
                    } else if ($investment_data['creative_id'] || $investment_data['ad_set_id']) {
                        // Create new investment
                        $investment_result = $this->contactcenter_model->add_ads_investment($investment_data);
                    } else {
                        $investment_result = true; // No investment to save
                    }

                    if (isset($investment_result) && $investment_result === false) {
                        set_alert('danger', 'Erro ao salvar investimento. Por favor, verifique os dados e tente novamente.');
                        redirect(admin_url('contactcenter/manage_creative' . (!empty($id) ? '/' . $id : '')));
                        return;
                    }
                }

                set_alert('success', _l('ads_analytics_creative_saved_successfully'));
                redirect(admin_url('contactcenter/ads_analytics'));
            } else {
                set_alert('danger', _l('ads_analytics_error_saving_creative'));
            }
        }

        $data['title'] = empty($id) ? _l('ads_analytics_add_creative') : _l('ads_analytics_edit_creative');
        $data['campaigns'] = $this->contactcenter_model->get_available_campaigns();
        $data['media'] = $this->contactcenter_model->get_ads_media();
        $data['ad_sets'] = $this->contactcenter_model->get_ads_sets(); // Load ad sets

        if (!empty($id)) {
            $data['creative'] = $this->contactcenter_model->get_ads_creative($id);
            if (!$data['creative']) {
                set_alert('danger', _l('ads_analytics_creative_not_found'));
                redirect(admin_url('contactcenter/ads_analytics'));
            }
            // Load investment data - check both creative and ad_set level
            $data['investment'] = $this->contactcenter_model->get_ads_creative_investment($id);
            if (!$data['investment'] && !empty($data['creative']->ad_set_id)) {
                $data['investment'] = $this->contactcenter_model->get_ads_set_investment($data['creative']->ad_set_id);
            }
        }

        $this->load->view('manage_creative', $data);
    }

    /**
     * Manage Ad Sets
     */
    public function manage_ad_set($id = null)
    {
        if ($this->input->post()) {
            $post_data = $this->input->post();
            $data = [
                'name' => $post_data['name'],
                'description' => !empty($post_data['description']) ? $post_data['description'] : null,
                'is_active' => isset($post_data['is_active']) ? (int)$post_data['is_active'] : 1,
                'staffid' => get_staff_user_id()
            ];

            if (!empty($id)) {
                // Update existing ad set
                $result = $this->contactcenter_model->update_ads_set($id, $data);
                $ad_set_id = $id;
            } else {
                // Create new ad set
                $ad_set_id = $this->contactcenter_model->add_ads_set($data);
                $result = $ad_set_id > 0;
            }

            if ($result && $ad_set_id) {
                // Save/Update investment if provided
                if (!empty($post_data['daily_investment']) && !empty($post_data['start_date'])) {
                    // Convert dates from DMY format to SQL format
                    $start_date = $this->_convert_to_sql_date($post_data['start_date']);
                    $end_date = !empty($post_data['end_date']) ? $this->_convert_to_sql_date($post_data['end_date']) : null;

                    // Validate that date conversion was successful
                    if (empty($start_date)) {
                        set_alert('danger', 'Data de início inválida. Por favor, use o formato dd/mm/aaaa.');
                        redirect(admin_url('contactcenter/manage_ad_set' . (!empty($id) ? '/' . $id : '')));
                        return;
                    }

                    // Validate end_date if provided
                    if (!empty($post_data['end_date']) && empty($end_date)) {
                        set_alert('danger', 'Data de término inválida. Por favor, use o formato dd/mm/aaaa.');
                        redirect(admin_url('contactcenter/manage_ad_set' . (!empty($id) ? '/' . $id : '')));
                        return;
                    }

                    $investment_data = [
                        'budget_type' => 'ad_set',
                        'ad_set_id' => $ad_set_id,
                        'creative_id' => null,
                        'daily_investment' => floatval($post_data['daily_investment']),
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'is_active' => 1,
                        'staffid' => get_staff_user_id()
                    ];

                    // Check if investment already exists
                    $existing_investment = $this->contactcenter_model->get_ads_set_investment($ad_set_id);

                    if ($existing_investment) {
                        $investment_result = $this->contactcenter_model->update_ads_investment($existing_investment->id, $investment_data);
                    } else {
                        $investment_result = $this->contactcenter_model->add_ads_investment($investment_data);
                    }

                    if ($investment_result === false) {
                        set_alert('danger', 'Erro ao salvar investimento. Por favor, verifique os dados e tente novamente.');
                        redirect(admin_url('contactcenter/manage_ad_set' . (!empty($id) ? '/' . $id : '')));
                        return;
                    }
                }

                set_alert('success', _l('ads_analytics_ad_set_saved'));
                redirect(admin_url('contactcenter/manage_ad_set' . (!empty($id) ? '/' . $id : '')));
            } else {
                set_alert('danger', _l('ads_analytics_error_saving_ad_set'));
            }
        }

        $data['title'] = empty($id) ? _l('ads_analytics_add_ad_set') : _l('ads_analytics_edit_ad_set');

        if (!empty($id)) {
            $data['ad_set'] = $this->contactcenter_model->get_ads_set($id);
            if (!$data['ad_set']) {
                set_alert('danger', 'Ad Set not found');
                redirect(admin_url('contactcenter/manage_ad_set'));
            }
            $data['creatives'] = $this->contactcenter_model->get_ads_set_creatives($id);
            // Load investment data for this ad set
            $data['investment'] = $this->contactcenter_model->get_ads_set_investment($id);
        }

        // Load all ad sets for listing
        $data['ad_sets'] = $this->contactcenter_model->get_ads_sets();

        $this->load->view('manage_ad_set', $data);
    }

    /**
     * Delete Ad Set
     */
    public function delete_ad_set($id)
    {
        if (!$id) {
            set_alert('danger', 'Ad Set ID is required');
            redirect(admin_url('contactcenter/manage_ad_set'));
        }

        $result = $this->contactcenter_model->delete_ads_set($id);

        if ($result) {
            set_alert('success', _l('ads_analytics_ad_set_deleted'));
        } else {
            set_alert('danger', _l('ads_analytics_error_deleting_ad_set'));
        }

        redirect(admin_url('contactcenter/manage_ad_set'));
    }

    /**
     * Upload media file
     */
    public function upload_ads_media()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
            return;
        }

        // Check if file was uploaded
        if (empty($_FILES['media_file']['name'])) {
            echo json_encode([
                'success' => false,
                'message' => _l('ads_analytics_select_file')
            ]);
            return;
        }

        $config['upload_path'] = FCPATH . 'uploads/contactcenter/ads_media/' . date('Y/m/');
        $config['allowed_types'] = 'jpg|jpeg|png|gif|mp4|avi|mov|wmv';
        $config['max_size'] = 50000; // 50MB
        $config['encrypt_name'] = true;

        // Create directory if it doesn't exist
        if (!is_dir($config['upload_path'])) {
            if (!mkdir($config['upload_path'], 0755, true)) {
                echo json_encode([
                    'success' => false,
                    'message' => _l('ads_analytics_error_uploading')
                ]);
                return;
            }
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('media_file')) {
            $error = $this->upload->display_errors('', '');
            echo json_encode([
                'success' => false,
                'message' => $error ? $error : _l('ads_analytics_error_uploading')
            ]);
            return;
        }

        $upload_data = $this->upload->data();

        // Determine file type
        $file_type = 'image';
        if (in_array(strtolower($upload_data['file_ext']), ['.mp4', '.avi', '.mov', '.wmv'])) {
            $file_type = 'video';
        }

        // Create thumbnail for images
        $thumbnail_path = null;
        if ($file_type === 'image' && function_exists('imagecreatefromjpeg')) {
            try {
                $this->load->library('image_lib');
                $thumbnail_path = $config['upload_path'] . 'thumb_' . $upload_data['file_name'];
                $config_thumb = [
                    'image_library' => 'gd2',
                    'source_image' => $upload_data['full_path'],
                    'new_image' => $thumbnail_path,
                    'maintain_ratio' => true,
                    'width' => 300,
                    'height' => 300
                ];
                $this->image_lib->initialize($config_thumb);
                if ($this->image_lib->resize()) {
                    $this->image_lib->clear();
                    $thumbnail_path = str_replace(FCPATH, '', $thumbnail_path);
                } else {
                    $thumbnail_path = null;
                    $this->image_lib->clear();
                }
            } catch (Exception $e) {
                $thumbnail_path = null;
            }
        }

        // Save media info to database
        $media_data = [
            'file_name' => $upload_data['orig_name'],
            'file_path' => 'uploads/contactcenter/ads_media/' . date('Y/m/') . $upload_data['file_name'],
            'file_type' => $file_type,
            'file_size' => $upload_data['file_size'],
            'thumbnail_path' => $thumbnail_path,
            'staffid' => get_staff_user_id()
        ];

        $media_id = $this->contactcenter_model->add_ads_media($media_data);

        if ($media_id) {
            echo json_encode([
                'success' => true,
                'message' => _l('ads_analytics_media_uploaded_success'),
                'media_id' => $media_id,
                'file_path' => base_url($media_data['file_path'])
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => _l('ads_analytics_error_saving_media')
            ]);
        }
    }

    /**
     * Save investment data for a creative
     */
    public function save_ads_investment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]));
            return;
        }

        $data = [
            'creative_id' => $this->input->post('creative_id'),
            'daily_investment' => $this->input->post('daily_investment'),
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date') ? $this->input->post('end_date') : null,
            'is_active' => $this->input->post('is_active') ? 1 : 0,
            'staffid' => get_staff_user_id()
        ];

        $id = $this->input->post('investment_id');

        if (!empty($id)) {
            $result = $this->contactcenter_model->update_ads_investment($id, $data);
        } else {
            $result = $this->contactcenter_model->add_ads_investment($data);
        }

        if ($result) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => true,
                'message' => 'Investment saved successfully'
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => false,
                'message' => _l('ads_analytics_error_saving_investment')
            ]));
        }
    }

    /**
     * Delete creative
     */
    public function delete_ads_creative($id)
    {
        if (!$id) {
            set_alert('danger', 'Creative ID is required');
            redirect(admin_url('contactcenter/ads_analytics'));
        }

        $result = $this->contactcenter_model->delete_ads_creative($id);

        if ($result) {
            set_alert('success', _l('ads_analytics_creative_deleted_successfully'));
        } else {
            set_alert('danger', _l('ads_analytics_error_deleting_creative'));
        }

        redirect(admin_url('contactcenter/ads_analytics'));
    }

    /**
     * Link lead to creative (AJAX)
     */
    public function link_lead_to_creative()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]));
            return;
        }

        $creative_id = $this->input->post('creative_id');
        $lead_id = $this->input->post('lead_id');

        if (!$creative_id || !$lead_id) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => false,
                'message' => 'Creative ID and Lead ID are required'
            ]));
            return;
        }

        $result = $this->contactcenter_model->link_lead_to_creative($creative_id, $lead_id);

        if ($result) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => true,
                'message' => 'Lead linked to creative successfully'
            ]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'success' => false,
                'message' => 'Error linking lead to creative'
            ]));
        }
    }

    /**
     * Replicate Creative (AJAX)
     */
    public function replicate_creative()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => _l('ads_analytics_invalid_request_method')
            ]);
            return;
        }

        $original_creative_id = $this->input->post('creative_id');
        $name = $this->input->post('name');
        $campaigns = $this->input->post('campaign_name');
        $media_id = $this->input->post('media_id');
        $daily_investment = $this->input->post('daily_investment');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        // Handle campaigns - can be array or single value
        if (!is_array($campaigns)) {
            $campaigns = [$campaigns];
        }

        // Validate required fields
        if (!$original_creative_id || !$name || empty($campaigns) || !$media_id) {
            echo json_encode([
                'success' => false,
                'message' => _l('ads_analytics_creative_id_required')
            ]);
            return;
        }

        // Get original creative data
        $original_creative = $this->contactcenter_model->get_ads_creative($original_creative_id);
        if (!$original_creative) {
            echo json_encode([
                'success' => false,
                'message' => _l('ads_analytics_original_creative_not_found')
            ]);
            return;
        }

        $created_creatives = [];
        $errors = [];

        // Create one creative for each selected campaign
        foreach ($campaigns as $campaign) {
            if (empty($campaign)) {
                continue;
            }

            // Create new creative with same media but different campaign and settings
            $data = [
                'name' => $name,
                'campaign_name' => $campaign,
                'media_id' => $media_id,
                'is_active' => $original_creative->is_active,
                'staffid' => get_staff_user_id()
            ];

            $new_creative_id = $this->contactcenter_model->add_ads_creative($data);

            if ($new_creative_id) {
                $created_creatives[] = $new_creative_id;

                // Add investment if provided (apply to all created creatives)
                if (!empty($daily_investment) && !empty($start_date)) {
                    // Convert dates from DMY format to SQL format
                    $sql_start_date = $this->_convert_to_sql_date($start_date);
                    $sql_end_date = !empty($end_date) ? $this->_convert_to_sql_date($end_date) : null;

                    $investment_data = [
                        'creative_id' => $new_creative_id,
                        'daily_investment' => $daily_investment,
                        'start_date' => $sql_start_date,
                        'end_date' => $sql_end_date,
                        'is_active' => 1,
                        'staffid' => get_staff_user_id()
                    ];

                    $this->contactcenter_model->add_ads_investment($investment_data);
                }
            } else {
                $errors[] = 'Error creating creative for campaign: ' . $campaign;
            }
        }

        if (!empty($created_creatives)) {
            $count = count($created_creatives);
            $message = $count > 1
                ? str_replace('{count}', $count, _l('ads_analytics_success_replicated_multiple'))
                : _l('ads_analytics_success_replicated');

            echo json_encode([
                'success' => true,
                'message' => $message,
                'creative_ids' => $created_creatives,
                'count' => $count
            ]);
        } else {
            $error_msg = !empty($errors) ? implode(', ', $errors) : _l('ads_analytics_error_creating_replicated');
            echo json_encode([
                'success' => false,
                'message' => $error_msg
            ]);
        }
    }

    /**
     * Ads Analytics Dashboard - Overview page with KPIs and charts
     */
    public function ads_analytics_dashboard()
    {
        $data['title'] = _l('ads_analytics_dashboard');

        // Get filter parameters
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        // Convert dates from DMY format to YYYY-MM-DD for database queries
        $filters = [
            'date_from' => !empty($date_from) ? $this->_convert_to_sql_date($date_from) : '',
            'date_to' => !empty($date_to) ? $this->_convert_to_sql_date($date_to) : '',
            'status' => $this->input->get('status'),
            'assigned' => $this->input->get('assigned')
        ];

        // Store original dates for display
        $filters['date_from_display'] = $date_from;
        $filters['date_to_display'] = $date_to;

        // Get summary KPIs
        $data['summary'] = $this->contactcenter_model->get_ads_analytics_summary($filters);

        // Get data for charts
        $data['leads_trend'] = $this->contactcenter_model->get_ads_leads_trend($filters);
        $data['campaign_performance'] = $this->contactcenter_model->get_ads_performance_by_campaign($filters);
        $data['cpl_distribution'] = $this->contactcenter_model->get_ads_cpl_distribution($filters);
        $data['top_performers'] = $this->contactcenter_model->get_ads_top_performers($filters, 5);

        // Prepare Investment vs Leads data
        $investment_vs_leads = [];
        $creatives_analytics = $this->contactcenter_model->get_ads_analytics_creatives($filters);
        foreach ($creatives_analytics as $creative) {
            $invested = $this->contactcenter_model->get_ads_total_invested($creative->id, $filters);
            $leads = $creative->total_leads ?? 0;
            if ($invested > 0 && $leads > 0) {
                $investment_vs_leads[] = [
                    'x' => $invested,
                    'y' => $leads,
                    'creative_name' => $creative->name
                ];
            }
        }
        $data['investment_vs_leads'] = $investment_vs_leads;

        // Get data for filter dropdowns
        $data['statuses'] = $this->leads_model->get_status();
        $data['staff_members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['filters'] = $filters;

        $this->load->view('ads_analytics_dashboard', $data);
    }

    /**
     * Ads Analytics Reports page
     */
    public function ads_analytics_reports()
    {
        $data['title'] = _l('ads_analytics_reports');

        // Get filter parameters
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $filters = [
            'date_from' => !empty($date_from) ? $this->_convert_to_sql_date($date_from) : '',
            'date_to' => !empty($date_to) ? $this->_convert_to_sql_date($date_to) : '',
            'creative_id' => $this->input->get('creative_id'),
            'campaign_name' => $this->input->get('campaign_name'),
            'status' => $this->input->get('status'),
            'assigned' => $this->input->get('assigned')
        ];

        $filters['date_from_display'] = $date_from;
        $filters['date_to_display'] = $date_to;

        // Get report data
        $data['summary'] = $this->contactcenter_model->get_ads_analytics_summary($filters);
        $data['creatives_data'] = $this->contactcenter_model->get_ads_analytics_creatives($filters);
        $data['campaign_performance'] = $this->contactcenter_model->get_ads_performance_by_campaign($filters);
        $top_performers_data = $this->contactcenter_model->get_ads_top_performers($filters, 10);
        $data['top_performers'] = isset($top_performers_data['top']) ? $top_performers_data['top'] : [];
        $data['leads_trend'] = $this->contactcenter_model->get_ads_leads_trend($filters);

        // Get data for filters
        $data['creatives'] = $this->contactcenter_model->get_ads_creatives();
        $data['campaigns'] = $this->contactcenter_model->get_available_campaigns();
        $data['statuses'] = $this->leads_model->get_status();
        $data['staff_members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['filters'] = $filters;

        $this->load->view('ads_analytics_reports', $data);
    }

    /**
     * Ads Analytics Comparison page
     */
    public function ads_analytics_comparison()
    {
        $data['title'] = _l('ads_analytics_comparison');

        // Get filter parameters
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $creative_ids = $this->input->get('creative_ids'); // Array of creative IDs to compare

        $filters = [
            'date_from' => !empty($date_from) ? $this->_convert_to_sql_date($date_from) : '',
            'date_to' => !empty($date_to) ? $this->_convert_to_sql_date($date_to) : '',
            'status' => $this->input->get('status'),
            'assigned' => $this->input->get('assigned')
        ];

        $filters['date_from_display'] = $date_from;
        $filters['date_to_display'] = $date_to;

        // Get comparison data if creatives are selected
        $data['comparison_data'] = [];
        if (!empty($creative_ids) && is_array($creative_ids)) {
            $data['comparison_data'] = $this->contactcenter_model->get_ads_comparison_data($creative_ids, $filters);
        }

        // Get all creatives for selection
        $data['creatives'] = $this->contactcenter_model->get_ads_creatives();
        $data['statuses'] = $this->leads_model->get_status();
        $data['staff_members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['filters'] = $filters;
        $data['selected_creative_ids'] = $creative_ids;

        $this->load->view('ads_analytics_comparison', $data);
    }

    /**
     * Ads Analytics AI Insights page
     */
    public function ads_analytics_ai_insights()
    {
        $data['title'] = _l('ads_analytics_ai_insights');

        // Get filter parameters
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $filters = [
            'date_from' => !empty($date_from) ? $this->_convert_to_sql_date($date_from) : '',
            'date_to' => !empty($date_to) ? $this->_convert_to_sql_date($date_to) : '',
            'status' => $this->input->get('status'),
            'assigned' => $this->input->get('assigned')
        ];

        $filters['date_from_display'] = $date_from;
        $filters['date_to_display'] = $date_to;

        // Check for cached insights
        log_message('debug', '=== AI INSIGHTS PAGE LOAD ===');
        log_message('debug', 'Filters for cache check: ' . json_encode($filters));
        $data['cached_insights'] = $this->contactcenter_model->get_cached_ai_insights($filters);
        if ($data['cached_insights']) {
            log_message('debug', 'Cached insights found on page load');
            log_message('debug', 'Cached insights keys: ' . implode(', ', array_keys($data['cached_insights'] ?: [])));
        } else {
            log_message('debug', 'No cached insights found on page load');
        }

        // Get data for filters
        $data['statuses'] = $this->leads_model->get_status();
        $data['staff_members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['filters'] = $filters;

        $this->load->view('ads_analytics_ai_insights', $data);
    }

    /**
     * AJAX endpoint to generate AI insights using Gemini
     */
    public function ads_analytics_generate_ai_insights()
    {
        log_message('debug', '=== AI INSIGHTS ENDPOINT CALLED ===');
        log_message('debug', 'Request Method: ' . $_SERVER['REQUEST_METHOD']);
        log_message('debug', 'Is AJAX: ' . ($this->input->is_ajax_request() ? 'Yes' : 'No'));
        log_message('debug', 'POST Data: ' . json_encode($_POST));

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            log_message('error', 'Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
            echo json_encode([
                'success' => false,
                'message' => _l('ads_analytics_invalid_request_method')
            ]);
            exit;
        }

        log_message('debug', 'POST method confirmed, processing request...');

        // Get filter parameters
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');

        log_message('debug', 'Date From: ' . ($date_from ?: 'empty'));
        log_message('debug', 'Date To: ' . ($date_to ?: 'empty'));

        // Store original dates for display
        $filters = [
            'date_from' => !empty($date_from) ? $this->_convert_to_sql_date($date_from) : '',
            'date_to' => !empty($date_to) ? $this->_convert_to_sql_date($date_to) : '',
            'date_from_display' => $date_from ?: '',
            'date_to_display' => $date_to ?: '',
            'status' => $this->input->post('status'),
            'assigned' => $this->input->post('assigned')
        ];

        log_message('debug', 'Filters processed: ' . json_encode($filters));

        // Check if we should force refresh (regenerate insights)
        $force_refresh = $this->input->post('force_refresh') == '1';

        // Always check database first (unless forcing refresh) - insights are saved permanently
        if (!$force_refresh) {
            log_message('debug', 'Checking database for saved insights...');
            $saved_insights = $this->contactcenter_model->get_cached_ai_insights($filters);
            if ($saved_insights) {
                // Validate that saved insights have the correct root structure
                $has_valid_structure = isset($saved_insights['performance_score']) ||
                    isset($saved_insights['assessment']) ||
                    isset($saved_insights['optimization_opportunities']) ||
                    isset($saved_insights['next_steps']);

                // Check if it's a nested object (wrong structure)
                $is_nested_object = isset($saved_insights['title']) && isset($saved_insights['description']) &&
                    isset($saved_insights['priority']) && !$has_valid_structure;

                if ($is_nested_object || !$has_valid_structure) {
                    log_message('warning', '=== INVALID SAVED INSIGHTS STRUCTURE DETECTED ===');
                    log_message('warning', 'Saved insights keys: ' . implode(', ', array_keys($saved_insights)));
                    log_message('warning', 'This appears to be a nested object, not the root structure. Will regenerate.');

                    // Delete the invalid cache entry
                    $this->contactcenter_model->delete_cached_ai_insights($filters);
                    log_message('debug', 'Deleted invalid cached insights, will generate new ones');
                } else {
                    log_message('debug', '=== FOUND VALID SAVED INSIGHTS IN DATABASE ===');
                    log_message('debug', 'Returning saved insights (no AI regeneration needed)');

                    $response = [
                        'success' => true,
                        'insights' => $saved_insights,
                        'cached' => true,
                        'message' => 'Insights carregados do banco de dados'
                    ];

                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    exit;
                }
            }
            log_message('debug', 'No saved insights found in database, will generate new insights');
        } else {
            log_message('debug', 'Force refresh requested, generating new insights (will overwrite saved data)');
        }

        log_message('debug', 'No cached insights found, generating new insights...');

        // Get comprehensive data for AI analysis
        log_message('debug', 'Fetching analytics data for AI analysis...');
        $analytics_data = $this->contactcenter_model->get_ads_data_for_ai_analysis($filters);
        log_message('debug', 'Analytics data retrieved. Keys: ' . implode(', ', array_keys($analytics_data ?: [])));

        // Generate insights using Gemini
        log_message('debug', 'Calling Gemini API to generate insights...');
        $insights = $this->_generate_gemini_insights($analytics_data, $filters);

        log_message('debug', 'Gemini insights received. Type: ' . gettype($insights));
        if (is_array($insights)) {
            log_message('debug', 'Insights keys: ' . implode(', ', array_keys($insights)));
            if (isset($insights['error'])) {
                log_message('error', 'Gemini returned error: ' . $insights['error']);
            }
        }

        if ($insights && isset($insights['error'])) {
            log_message('error', 'Error in insights: ' . $insights['error']);
            echo json_encode([
                'success' => false,
                'message' => $insights['error']
            ]);
            exit;
        }

        // Save insights to database (permanent storage)
        if ($insights && !isset($insights['error'])) {
            log_message('debug', 'Saving insights to database for permanent storage...');
            $this->contactcenter_model->cache_ai_insights($filters, $insights);
            log_message('debug', 'Insights saved to database. Future requests will use saved data instead of calling AI.');
        }

        // Ensure we always return a valid JSON response
        if (!$insights || empty($insights)) {
            log_message('error', 'No insights generated or insights is empty');
            echo json_encode([
                'success' => false,
                'message' => 'Não foi possível gerar insights. Verifique se há dados suficientes no período selecionado.'
            ]);
            exit;
        }

        log_message('debug', 'Sending successful response with insights');
        log_message('debug', 'Response size: ~' . strlen(json_encode($insights)) . ' bytes');

        echo json_encode([
            'success' => true,
            'insights' => $insights,
            'cached' => false
        ]);
        exit;
    }

    /**
     * Generate AI insights using Gemini API (following AXIOM Studio pattern)
     */
    private function _generate_gemini_insights($analytics_data, $filters)
    {
        try {
            // Get Gemini API key (same pattern as AXIOM Studio)
            $gemini_api_key = get_option('contactcenter_gemini_api_key');
            if (empty($gemini_api_key)) {
                $gemini_api_key = get_option('axiom_studio_gemini_api_key');
            }

            if (empty($gemini_api_key)) {
                return ['error' => _l('ads_analytics_gemini_api_not_configured')];
            }

            // Prepare comprehensive prompt
            $date_from_display = !empty($filters['date_from_display']) ? $filters['date_from_display'] : date('d/m/Y', strtotime('-30 days'));
            $date_to_display = !empty($filters['date_to_display']) ? $filters['date_to_display'] : date('d/m/Y');

            $prompt = "Analyze this ads analytics data for the period {$date_from_display} to {$date_to_display} and provide comprehensive insights:\n\n";
            $prompt .= "Summary Metrics:\n";
            $prompt .= "- Total Leads: " . ($analytics_data['total_leads'] ?? 0) . "\n";
            $prompt .= "- Total Converted: " . ($analytics_data['converted_leads'] ?? 0) . " (" . number_format($analytics_data['conversion_rate'] ?? 0, 2) . "%)\n";
            $prompt .= "- Total Invested: R$ " . number_format($analytics_data['total_invested'] ?? 0, 2, ',', '.') . "\n";
            $prompt .= "- Average CPL: R$ " . number_format($analytics_data['avg_cpl'] ?? 0, 2, ',', '.') . "\n";
            $prompt .= "- ROI: " . number_format($analytics_data['roi'] ?? 0, 2) . "%\n";
            $prompt .= "- Active Creatives: " . ($analytics_data['creatives_count'] ?? 0) . "\n\n";

            if (!empty($analytics_data['top_creatives'])) {
                $prompt .= "Top Performing Creatives:\n" . json_encode($analytics_data['top_creatives'], JSON_PRETTY_PRINT) . "\n\n";
            }

            if (!empty($analytics_data['worst_creatives'])) {
                $prompt .= "Underperforming Creatives:\n" . json_encode($analytics_data['worst_creatives'], JSON_PRETTY_PRINT) . "\n\n";
            }

            if (!empty($analytics_data['campaign_performance'])) {
                $prompt .= "Campaign Performance:\n" . json_encode($analytics_data['campaign_performance'], JSON_PRETTY_PRINT) . "\n\n";
            }

            if (!empty($analytics_data['trends'])) {
                $prompt .= "Trend Analysis:\n" . json_encode($analytics_data['trends'], JSON_PRETTY_PRINT) . "\n\n";
            }

            $prompt .= "Please provide a comprehensive analysis in JSON format with:\n";
            $prompt .= "1. Overall performance assessment (score 0-100)\n";
            $prompt .= "2. Top 3 optimization opportunities (with priority: High/Medium/Low)\n";
            $prompt .= "3. Underperforming areas and root cause analysis\n";
            $prompt .= "4. Budget allocation recommendations\n";
            $prompt .= "5. Creative performance insights and recommendations\n";
            $prompt .= "6. Trend predictions for next period\n";
            $prompt .= "7. Actionable next steps\n\n";
            $prompt .= "IMPORTANT: Respond ONLY with valid JSON. Do not include markdown code blocks, explanations, or any text outside the JSON object.\n\n";
            $prompt .= "Format response as JSON with this exact structure:\n";
            $prompt .= "{\n";
            $prompt .= '  "performance_score": 75,' . "\n";
            $prompt .= '  "assessment": "Overall assessment text",' . "\n";
            $prompt .= '  "optimization_opportunities": [' . "\n";
            $prompt .= '    {' . "\n";
            $prompt .= '      "title": "Opportunity title",' . "\n";
            $prompt .= '      "description": "Detailed description",' . "\n";
            $prompt .= '      "priority": "High|Medium|Low",' . "\n";
            $prompt .= '      "impact": "Expected impact description",' . "\n";
            $prompt .= '      "action": "Recommended action"' . "\n";
            $prompt .= '    }' . "\n";
            $prompt .= '  ],' . "\n";
            $prompt .= '  "underperforming_areas": [...],' . "\n";
            $prompt .= '  "budget_recommendations": [...],' . "\n";
            $prompt .= '  "creative_insights": [...],' . "\n";
            $prompt .= '  "trend_predictions": {...},' . "\n";
            $prompt .= '  "next_steps": [...]' . "\n";
            $prompt .= "}\n\n";
            $prompt .= "Respond ONLY with the JSON object above, no markdown, no code blocks, no explanations. Use Portuguese (Brazil) for all text content within the JSON.";

            $data = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 4000,
                    'responseMimeType' => 'application/json'
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $gemini_api_key);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200) {
                $result = json_decode($response, true);
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $analysis_text = $result['candidates'][0]['content']['parts'][0]['text'];
                    log_message('debug', "Gemini response length: " . strlen($analysis_text) . " characters");
                    log_message('debug', "Gemini response (first 1000 chars): " . substr($analysis_text, 0, 1000));

                    // Remove markdown code block markers if present
                    $original_text = $analysis_text;
                    $analysis_text = preg_replace('/```json\s*/i', '', $analysis_text);
                    $analysis_text = preg_replace('/```\s*/', '', $analysis_text);
                    $analysis_text = trim($analysis_text);

                    log_message('debug', "After removing markdown, length: " . strlen($analysis_text));

                    // Try multiple strategies to extract and parse JSON
                    $parsed_analysis = null;

                    // Strategy 1: Try to parse the entire cleaned text as JSON
                    $parsed_analysis = json_decode($analysis_text, true);
                    if ($parsed_analysis && json_last_error() === JSON_ERROR_NONE) {
                        log_message('debug', "Successfully parsed full cleaned text as JSON");
                        return $parsed_analysis;
                    } else {
                        log_message('debug', "Strategy 1 failed: " . json_last_error_msg());
                    }

                    // Strategy 2: Find the first complete JSON object (properly handle strings)
                    $json_start = strpos($analysis_text, '{');
                    if ($json_start !== false) {
                        // Find matching closing brace by counting braces, accounting for strings
                        $brace_count = 0;
                        $json_end = -1;
                        $in_string = false;
                        $escape_next = false;

                        for ($i = $json_start; $i < strlen($analysis_text); $i++) {
                            $char = $analysis_text[$i];

                            if ($escape_next) {
                                $escape_next = false;
                                continue;
                            }

                            if ($char === '\\') {
                                $escape_next = true;
                                continue;
                            }

                            if ($char === '"' && !$escape_next) {
                                $in_string = !$in_string;
                                continue;
                            }

                            if (!$in_string) {
                                if ($char === '{') {
                                    $brace_count++;
                                } elseif ($char === '}') {
                                    $brace_count--;
                                    if ($brace_count === 0) {
                                        $json_end = $i + 1;
                                        break;
                                    }
                                }
                            }
                        }

                        if ($json_end > $json_start) {
                            $json_part = substr($analysis_text, $json_start, $json_end - $json_start);
                            log_message('debug', "Strategy 2: Extracted JSON part length: " . strlen($json_part));
                            $parsed_analysis = json_decode($json_part, true);
                            if ($parsed_analysis && json_last_error() === JSON_ERROR_NONE) {
                                log_message('debug', "Strategy 2: Successfully parsed extracted JSON object");
                                log_message('debug', "Root object keys: " . implode(', ', array_keys($parsed_analysis)));
                                return $parsed_analysis;
                            } else {
                                log_message('error', "Strategy 2: JSON parse error: " . json_last_error_msg());
                                log_message('error', "JSON part (first 500 chars): " . substr($json_part, 0, 500));
                                log_message('error', "JSON part (last 500 chars): " . substr($json_part, -500));
                            }
                        } else {
                            log_message('error', "Strategy 2: Could not find matching closing brace for JSON object");
                        }
                    }

                    // Strategy 3: Try to find and parse any JSON-like structure (last resort fallback)
                    // This is less reliable but might catch partial structures
                    if (preg_match('/\{[^{}]*(?:\{[^{}]*\}[^{}]*)*\}/s', $analysis_text, $matches)) {
                        log_message('warning', "Strategy 3: Using regex fallback - trying to match JSON structure");
                        $parsed_analysis = json_decode($matches[0], true);
                        if ($parsed_analysis && json_last_error() === JSON_ERROR_NONE) {
                            log_message('warning', "Strategy 3: Regex matched but structure may be incomplete");
                            log_message('debug', "Regex-matched object keys: " . implode(', ', array_keys($parsed_analysis)));
                            // Only return if it has the expected root keys
                            if (isset($parsed_analysis['performance_score']) || isset($parsed_analysis['assessment'])) {
                                log_message('debug', "Strategy 3: Has expected root keys, returning");
                                return $parsed_analysis;
                            } else {
                                log_message('warning', "Strategy 3: Matched object doesn't have expected root keys, ignoring");
                            }
                        }
                    }

                    // If all strategies failed, return raw analysis with error message
                    log_message('error', "All JSON parsing strategies failed. Returning raw analysis.");
                    log_message('error', "Full analysis text (last 1000 chars): " . substr($original_text, -1000));

                    return [
                        'error' => 'Erro ao processar resposta do Gemini: ' . json_last_error_msg(),
                        'raw_analysis' => $original_text
                    ];
                } else {
                    log_message('error', "Gemini response missing expected structure");
                    log_message('error', "Response keys: " . implode(', ', array_keys($result ?: [])));
                    log_message('error', "Response (first 1000 chars): " . substr($response, 0, 1000));
                }
            } else {
                log_message('error', "Gemini API returned HTTP code: " . $http_code);
                log_message('error', "Response (first 1000 chars): " . substr($response, 0, 1000));
            }

            return ['error' => _l('ads_analytics_gemini_analysis_failed'), 'http_code' => $http_code];
        } catch (Exception $e) {
            log_message('error', "Gemini ads analytics insights failed: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Export ads analytics data
     */
    public function ads_analytics_export()
    {
        $format = $this->input->get('format'); // excel, pdf, csv
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $filters = [
            'date_from' => !empty($date_from) ? $this->_convert_to_sql_date($date_from) : '',
            'date_to' => !empty($date_to) ? $this->_convert_to_sql_date($date_to) : '',
            'creative_id' => $this->input->get('creative_id'),
            'campaign_name' => $this->input->get('campaign_name'),
            'status' => $this->input->get('status'),
            'assigned' => $this->input->get('assigned')
        ];

        // Get data
        $creatives_analytics = $this->contactcenter_model->get_ads_analytics_creatives($filters);

        switch ($format) {
            case 'csv':
                $this->_export_csv($creatives_analytics, $filters);
                break;
            case 'excel':
                $this->_export_excel($creatives_analytics, $filters);
                break;
            case 'pdf':
                $this->_export_pdf($creatives_analytics, $filters);
                break;
            default:
                set_alert('danger', _l('ads_analytics_invalid_export_format'));
                redirect(admin_url('contactcenter/ads_analytics_reports'));
        }
    }

    private function _export_csv($data, $filters)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="ads_analytics_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // BOM for UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Headers
        fputcsv($output, [
            _l('ads_analytics_creative_name'),
            _l('ads_analytics_campaign'),
            _l('ads_analytics_leads'),
            _l('ads_analytics_converted'),
            _l('ads_analytics_cpl'),
            _l('ads_analytics_total_invested'),
            _l('ads_analytics_status')
        ]);

        // Data
        foreach ($data as $creative) {
            $cpl = $this->contactcenter_model->calculate_ads_cpl($creative->id, $filters, $creative->total_leads ?? 0);
            $total_invested = $this->contactcenter_model->get_ads_total_invested($creative->id, $filters);

            fputcsv($output, [
                $creative->name,
                $creative->campaign_name ?? '',
                $creative->total_leads ?? 0,
                $creative->converted_leads ?? 0,
                $cpl !== null ? 'R$ ' . number_format($cpl, 2, ',', '.') : 'N/A',
                'R$ ' . number_format($total_invested, 2, ',', '.'),
                $creative->is_active ? _l('ads_analytics_active') : _l('ads_analytics_inactive')
            ]);
        }

        fclose($output);
        exit;
    }

    private function _export_excel($data, $filters)
    {
        // Excel export would use PhpSpreadsheet if available
        // For now, fallback to CSV
        $this->_export_csv($data, $filters);
    }

    private function _export_pdf($data, $filters)
    {
        // PDF export would use TCPDF or similar if available
        // For now, show message
        set_alert('info', _l('ads_analytics_pdf_export_coming_soon'));
        redirect(admin_url('contactcenter/ads_analytics_reports'));
    }

    /**
     * AJAX search leads for new chat
     */
    public function ajax_search_leads()
    {
        $jSON = array('success' => false, 'results' => array());

        if ($this->input->is_ajax_request()) {
            $search = $this->input->post('search');
            $search = $search ? trim($search) : '';

            // Allow empty search to return all results
            $this->db->select('id, name, company, email, phonenumber');
            $this->db->from(db_prefix() . 'leads');

            // Only non-converted leads (client_id is 0 or null)
            $this->db->where('(client_id = 0 OR client_id IS NULL)', null, false);

            // Search in multiple fields (only if search is not empty)
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('name', $search);
                $this->db->or_like('company', $search);
                $this->db->or_like('email', $search);
                $this->db->or_like('phonenumber', $search);
                $this->db->group_end();
            }

            // Check permissions
            if (!has_permission('leads', '', 'view')) {
                $this->db->group_start();
                $this->db->where('assigned', get_staff_user_id());
                $this->db->or_where('addedfrom', get_staff_user_id());
                $this->db->or_where('is_public', 1);
                $this->db->group_end();
            }

            $this->db->limit(20);
            $this->db->order_by('name', 'ASC');

            $query = $this->db->get();

            // Check for database errors
            $db_error = $this->db->error();
            if ($db_error['code'] != 0) {
                log_message('error', 'Search leads SQL error: ' . json_encode($db_error));
                $jSON['error'] = $db_error['message'];
            } else {
                $results = $query ? $query->result_array() : array();
                if ($results && count($results) > 0) {
                    $jSON['success'] = true;
                    $jSON['results'] = $results;
                }
            }
        }

        echo json_encode($jSON);
    }

    /**
     * AJAX search clients for new chat
     */
    public function ajax_search_clients()
    {
        $jSON = array('success' => false, 'results' => array());

        try {
            if ($this->input->is_ajax_request()) {
                $search = $this->input->post('search');

                // Allow empty search to return all results
                $search = $search ? trim($search) : '';

                $this->load->model('clients_model');

                // Get all clients first (clients table doesn't have firstname/lastname/email - those are in contacts)
                // Clients table typically has: userid, company, phonenumber, vat, active, etc.
                $this->db->select('c.userid, c.company, c.phonenumber, c.vat');
                $this->db->from(db_prefix() . 'clients c');
                $this->db->where('c.active', 1);

                // Search in client fields (only if search is not empty)
                // Note: clients table doesn't have firstname/lastname/email - those are in contacts table
                if (!empty($search)) {
                    $this->db->group_start();
                    $this->db->like('c.company', $search);
                    $this->db->or_like('c.phonenumber', $search);
                    $this->db->or_like('c.vat', $search);
                    $this->db->group_end();
                }

                $this->db->limit(50); // Get more clients to search their contacts
                $query = $this->db->get();

                // Check for query errors first
                $db_error = $this->db->error();
                if ($db_error['code'] != 0) {
                    log_message('error', 'Search clients SQL error (main query): ' . json_encode($db_error));
                    $jSON['success'] = false;
                    $jSON['error'] = $db_error['message'];
                    echo json_encode($jSON);
                    return;
                }

                $clients = $query ? $query->result_array() : array();

                $formatted_results = array();
                $seen_phones = array();

                if ($clients && count($clients) > 0) {
                    foreach ($clients as $client) {
                        $client_id = $client['userid'];

                        // Reset query builder for next query
                        $this->db->reset_query();

                        // Get all contacts for this client
                        $this->db->select('id, firstname, lastname, email, phonenumber');
                        $this->db->from(db_prefix() . 'contacts');
                        $this->db->where('userid', $client_id);
                        $this->db->where('active', 1);
                        $this->db->where('phonenumber !=', '');
                        $this->db->where('phonenumber IS NOT NULL');

                        // Search in contact fields if search term exists
                        if (!empty($search)) {
                            $this->db->group_start();
                            $this->db->like('firstname', $search);
                            $this->db->or_like('lastname', $search);
                            $this->db->or_like('email', $search);
                            $this->db->or_like('phonenumber', $search);
                            $this->db->group_end();
                        }

                        $contact_query = $this->db->get();

                        // Check for contact query errors
                        $contact_db_error = $this->db->error();
                        if ($contact_db_error['code'] != 0) {
                            log_message('error', 'Search clients SQL error (contact query for client ' . $client_id . '): ' . json_encode($contact_db_error));
                            // Continue with other clients even if one fails
                            $contacts = array();
                        } else {
                            $contacts = $contact_query ? $contact_query->result_array() : array();
                        }

                        // Add client phone if exists
                        if (!empty($client['phonenumber'])) {
                            $phone_key = preg_replace('/\D/', '', $client['phonenumber']);
                            if (!isset($seen_phones[$phone_key])) {
                                $seen_phones[$phone_key] = true;
                                $name = $client['company'] ? $client['company'] : 'Cliente #' . $client_id;
                                $formatted_results[] = array(
                                    'userid' => $client_id,
                                    'company' => $client['company'],
                                    'firstname' => '',
                                    'lastname' => '',
                                    'fullname' => $name,
                                    'email' => '', // Email comes from contacts, not clients table
                                    'phonenumber' => $client['phonenumber'],
                                    'contact_id' => null
                                );
                            }
                        }

                        // Add all contacts with phone numbers
                        foreach ($contacts as $contact) {
                            if (!empty($contact['phonenumber'])) {
                                $phone_key = preg_replace('/\D/', '', $contact['phonenumber']);
                                if (!isset($seen_phones[$phone_key])) {
                                    $seen_phones[$phone_key] = true;
                                    $contact_name = trim($contact['firstname'] . ' ' . $contact['lastname']);
                                    $display_name = $contact_name ? $contact_name : ($client['company'] ? $client['company'] : 'Cliente #' . $client_id);

                                    $formatted_results[] = array(
                                        'userid' => $client_id,
                                        'company' => $client['company'],
                                        'firstname' => $contact['firstname'],
                                        'lastname' => $contact['lastname'],
                                        'fullname' => $display_name,
                                        'email' => $contact['email'],
                                        'phonenumber' => $contact['phonenumber'],
                                        'contact_id' => $contact['id']
                                    );
                                }
                            }
                        }

                        // If no search or search matches client, also include client without phone if it has contacts
                        if (empty($search) || (!empty($client['company']) && stripos($client['company'], $search) !== false)) {
                            // Already handled above with client phone
                        }
                    }
                }

                // Check for database errors
                $db_error = $this->db->error();
                if ($db_error['code'] != 0) {
                    log_message('error', 'Search clients SQL error: ' . json_encode($db_error));
                    $jSON['success'] = false;
                    $jSON['error'] = $db_error['message'];
                } else {
                    // Always set success to true, even if no results
                    $jSON['success'] = true;
                    if (count($formatted_results) > 0) {
                        // Limit to 20 results
                        $jSON['results'] = array_slice($formatted_results, 0, 20);
                    } else {
                        $jSON['results'] = array();
                    }
                }
            } else {
                $jSON['success'] = false;
                $jSON['error'] = 'Invalid request';
            }
        } catch (Exception $e) {
            log_message('error', 'Search clients exception: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            $jSON['success'] = false;
            $jSON['error'] = 'An error occurred while searching clients.';
        }

        echo json_encode($jSON);
    }

    /**
     * Media Library - View
     */
    public function media_library()
    {
        if (!has_permission('contactcenter', '', 'view')) {
            access_denied('contactcenter');
        }

        $device = $this->contactcenter_model->get_device($this->input->get('device_id'));
        if (!$device) {
            set_alert('danger', _l("contac_aviso_sem_device"));
            redirect(admin_url("contactcenter/chatall"));
        }

        $data['title'] = _l('media_library');
        $data['device'] = $device;
        $this->load->view('media_library', $data);
    }

    /**
     * Media Library - List media files
     */
    public function ajax_get_media_library()
    {
        $jSON = array('success' => false, 'media' => array());

        try {
            if ($this->input->is_ajax_request()) {
                $device_id = $this->input->post('device_id');
                $device = $this->contactcenter_model->get_device($device_id);

                if (!$device) {
                    $jSON['error'] = _l("contac_aviso_sem_device");
                    echo json_encode($jSON);
                    return;
                }

                $this->db->select('*');
                $this->db->from(db_prefix() . 'contactcenter_media_library');

                // Show global media OR media owned by this device's staff
                $this->db->group_start();
                $this->db->where('is_global', 1);
                $this->db->or_where('staffid', $device->staffid);
                $this->db->group_end();

                // Filter by file type if provided
                $file_type = $this->input->post('file_type');
                if ($file_type && in_array($file_type, ['audio', 'image', 'video', 'document'])) {
                    $this->db->where('file_type', $file_type);
                }

                $this->db->order_by('date_created', 'DESC');
                $query = $this->db->get();

                $media = $query ? $query->result_array() : array();

                $jSON['success'] = true;
                $jSON['media'] = $media;
            }
        } catch (Exception $e) {
            log_message('error', 'ajax_get_media_library error: ' . $e->getMessage());
            $jSON['error'] = $e->getMessage();
        }

        echo json_encode($jSON);
    }

    /**
     * Media Library - Upload media
     */
    public function ajax_upload_media_library()
    {
        $jSON = array('success' => false);

        try {
            if ($this->input->is_ajax_request() && isset($_FILES['media_file'])) {
                $device_id = $this->input->post('device_id');
                $is_global = $this->input->post('is_global') ? 1 : 0;
                $title = $this->input->post('title');
                $description = $this->input->post('description');

                $device = $this->contactcenter_model->get_device($device_id);
                if (!$device) {
                    $jSON['error'] = _l("contac_aviso_sem_device");
                    echo json_encode($jSON);
                    return;
                }

                // Check permission for global uploads
                if ($is_global && !is_admin()) {
                    $jSON['error'] = _l('access_denied');
                    echo json_encode($jSON);
                    return;
                }

                // Create upload directory
                $upload_path = FCPATH . 'uploads/contactcenter/media_library/' . date('Y/m/');
                if (!file_exists($upload_path)) {
                    mkdir($upload_path, 0755, true);
                }

                // Configure upload
                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = 'jpg|jpeg|png|gif|mp3|wav|ogg|m4a|mp4|avi|mov|pdf|doc|docx|xls|xlsx|zip|rar';
                $config['max_size'] = 100 * 1024 * 1024; // 100MB
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('media_file')) {
                    $upload_data = $this->upload->data();
                    $file_path = 'uploads/contactcenter/media_library/' . date('Y/m/') . $upload_data['file_name'];

                    // Determine file type
                    $file_type = 'document';
                    $ext = strtolower($upload_data['file_ext']);
                    if (in_array($ext, ['.jpg', '.jpeg', '.png', '.gif'])) {
                        $file_type = 'image';
                    } elseif (in_array($ext, ['.mp3', '.wav', '.ogg', '.m4a'])) {
                        $file_type = 'audio';
                    } elseif (in_array($ext, ['.mp4', '.avi', '.mov'])) {
                        $file_type = 'video';
                    }

                    // Insert into database
                    $data = array(
                        'staffid' => $device->staffid,
                        'device_id' => $device_id,
                        'filename' => $upload_data['file_name'],
                        'file_path' => $file_path,
                        'file_type' => $file_type,
                        'file_size' => $upload_data['file_size'],
                        'is_global' => $is_global,
                        'title' => $title ?: $upload_data['orig_name'],
                        'description' => $description
                    );

                    $this->db->insert(db_prefix() . 'contactcenter_media_library', $data);
                    $media_id = $this->db->insert_id();

                    $jSON['success'] = true;
                    $jSON['media'] = array(
                        'id' => $media_id,
                        'file_path' => $file_path,
                        'file_type' => $file_type,
                        'filename' => $upload_data['orig_name']
                    );
                } else {
                    $jSON['error'] = $this->upload->display_errors('', '');
                }
            }
        } catch (Exception $e) {
            log_message('error', 'ajax_upload_media_library error: ' . $e->getMessage());
            $jSON['error'] = $e->getMessage();
        }

        echo json_encode($jSON);
    }

    /**
     * Media Library - Delete media
     */
    public function ajax_delete_media_library()
    {
        $jSON = array('success' => false);

        try {
            if ($this->input->is_ajax_request()) {
                $media_id = $this->input->post('media_id');
                $device_id = $this->input->post('device_id');

                $device = $this->contactcenter_model->get_device($device_id);
                if (!$device) {
                    $jSON['error'] = _l("contac_aviso_sem_device");
                    echo json_encode($jSON);
                    return;
                }

                // Get media info
                $this->db->where('id', $media_id);
                $media = $this->db->get(db_prefix() . 'contactcenter_media_library')->row();

                if (!$media) {
                    $jSON['error'] = _l('media_not_found');
                    echo json_encode($jSON);
                    return;
                }

                // Check permissions: can delete if owner or admin
                if ($media->staffid != $device->staffid && !is_admin()) {
                    $jSON['error'] = _l('access_denied');
                    echo json_encode($jSON);
                    return;
                }

                // Delete file
                if (file_exists(FCPATH . $media->file_path)) {
                    @unlink(FCPATH . $media->file_path);
                }

                // Delete from database
                $this->db->where('id', $media_id);
                $this->db->delete(db_prefix() . 'contactcenter_media_library');

                $jSON['success'] = true;
            }
        } catch (Exception $e) {
            log_message('error', 'ajax_delete_media_library error: ' . $e->getMessage());
            $jSON['error'] = $e->getMessage();
        }

        echo json_encode($jSON);
    }

    /**
     * Media Library - Toggle global/private
     */
    public function ajax_toggle_media_visibility()
    {
        $jSON = array('success' => false);

        try {
            if ($this->input->is_ajax_request()) {
                $media_id = $this->input->post('media_id');
                $device_id = $this->input->post('device_id');
                $is_global = $this->input->post('is_global') ? 1 : 0;

                // Only admins can toggle global
                if ($is_global && !is_admin()) {
                    $jSON['error'] = _l('access_denied');
                    echo json_encode($jSON);
                    return;
                }

                $device = $this->contactcenter_model->get_device($device_id);
                if (!$device) {
                    $jSON['error'] = _l("contac_aviso_sem_device");
                    echo json_encode($jSON);
                    return;
                }

                // Get media info
                $this->db->where('id', $media_id);
                $media = $this->db->get(db_prefix() . 'contactcenter_media_library')->row();

                if (!$media) {
                    $jSON['error'] = _l('media_not_found');
                    echo json_encode($jSON);
                    return;
                }

                // Check permissions: can toggle if owner or admin
                if ($media->staffid != $device->staffid && !is_admin()) {
                    $jSON['error'] = _l('access_denied');
                    echo json_encode($jSON);
                    return;
                }

                // Update visibility
                $this->db->where('id', $media_id);
                $this->db->update(db_prefix() . 'contactcenter_media_library', array('is_global' => $is_global));

                $jSON['success'] = true;
            }
        } catch (Exception $e) {
            log_message('error', 'ajax_toggle_media_visibility error: ' . $e->getMessage());
            $jSON['error'] = $e->getMessage();
        }

        echo json_encode($jSON);
    }

    /**
     * AJAX search staff for bulk send
     */
    public function ajax_search_staff()
    {
        $jSON = array('success' => false, 'results' => array());

        if ($this->input->is_ajax_request()) {
            $search = $this->input->post('search');
            $search = $search ? trim($search) : '';

            $this->db->select('staffid, firstname, lastname, email, phonenumber');
            $this->db->from(db_prefix() . 'staff');
            $this->db->where('active', 1);
            $this->db->where('phonenumber !=', '');
            $this->db->where('phonenumber IS NOT NULL');

            // Search in staff fields (only if search is not empty)
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('firstname', $search);
                $this->db->or_like('lastname', $search);
                $this->db->or_like('email', $search);
                $this->db->or_like('phonenumber', $search);
                $this->db->group_end();
            }

            $this->db->limit(100); // Limit results to prevent too many
            $this->db->limit(100); // Limit results to prevent too many
            $this->db->order_by('firstname', 'ASC');
            $this->db->order_by('lastname', 'ASC');

            $query = $this->db->get();

            // Check for database errors
            $db_error = $this->db->error();
            if ($db_error['code'] != 0) {
                log_message('error', 'Search staff SQL error: ' . json_encode($db_error));
                $jSON['error'] = $db_error['message'];
            } else {
                $results = $query ? $query->result_array() : array();
                if ($results && count($results) > 0) {
                    $formatted_staff = [];
                    foreach ($results as $staff) {
                        $phone = preg_replace('/\D/', '', $staff['phonenumber']);
                        if (strlen($phone) >= 10) { // Basic validation for phone number length
                            $formatted_staff[] = [
                                'id' => $staff['staffid'],
                                'fullname' => trim($staff['firstname'] . ' ' . $staff['lastname']),
                                'firstname' => $staff['firstname'],
                                'lastname' => $staff['lastname'],
                                'phonenumber' => $staff['phonenumber'],
                                'phone' => $staff['phonenumber'], // Alias for compatibility
                                'email' => $staff['email']
                            ];
                        }
                    }
                    $jSON['success'] = true;
                    $jSON['results'] = $formatted_staff;
                } else {
                    // Even if no results, return success with empty array
                    $jSON['success'] = true;
                    $jSON['results'] = array();
                }
            }
        }

        echo json_encode($jSON);
    }

    /**
     * Get campaign queue status for device
     */
    public function ajax_get_campaign_queue_status()
    {
        $jSON = array('success' => false, 'has_campaign' => false, 'total_leads' => 0, 'sent_leads' => 0, 'pending_count' => 0, 'campaign_name' => '');

        if ($this->input->is_ajax_request()) {
            $device_id = $this->input->post('device_id');

            if ($device_id) {
                // Get device info
                $this->db->where('dev_id', $device_id);
                $device = $this->db->get(db_prefix() . 'contactcenter_device')->row();

                if ($device) {
                    // Check for active campaigns for this device
                    $this->db->where('device_id', $device_id);
                    $this->db->where('con_status', 1); // Active campaigns
                    $campaigns = $this->db->get(db_prefix() . 'contactcenter_conversation_engine')->result();

                    if ($campaigns && count($campaigns) > 0) {
                        $jSON['has_campaign'] = true;
                        $jSON['campaign_name'] = count($campaigns) > 1 ? count($campaigns) . ' ' . _l('contac_conversation_engine') : (isset($campaigns[0]->con_title) ? $campaigns[0]->con_title : 'Campaign');

                        $this->load->model('contactcenter_model');

                        $total_leads = 0;
                        $sent_leads = 0;

                        // Get all campaign IDs for easier querying
                        $campaign_ids = array();
                        foreach ($campaigns as $campaign) {
                            $campaign_ids[] = $campaign->con_id;
                        }

                        // Count total eligible leads across all campaigns
                        // This is the sum of all leads that match each campaign's criteria (including not yet sent)
                        foreach ($campaigns as $campaign) {
                            $leads_status = isset($campaign->leads_status) ? $campaign->leads_status : null;
                            $date_filter_type = isset($campaign->date_filter_type) ? $campaign->date_filter_type : 'creation_date';
                            $date = ($date_filter_type == 'last_contact' && isset($campaign->leads_last_contact_data)) ? $campaign->leads_last_contact_data : (isset($campaign->leads_create_data) ? $campaign->leads_create_data : null);
                            $date_final = ($date_filter_type == 'last_contact' && isset($campaign->leads_last_contact_data_final)) ? $campaign->leads_last_contact_data_final : (isset($campaign->leads_create_data_final) ? $campaign->leads_create_data_final : null);
                            $birthday_field = isset($campaign->birthday_field) ? $campaign->birthday_field : null;
                            $filter_source = isset($campaign->filter_source) ? $campaign->filter_source : null;
                            $filter_city = isset($campaign->filter_city) ? $campaign->filter_city : null;
                            $filter_state = isset($campaign->filter_state) ? $campaign->filter_state : null;

                            // Skip if required fields are missing
                            if ($leads_status === null) {
                                continue;
                            }
                            if ($date_filter_type == 'birthday') {
                                if (empty($birthday_field)) {
                                    continue;
                                }
                            } elseif ($date === null || $date_final === null) {
                                continue;
                            }

                            $count = 0;

                            // If tags are set, we need to query differently since get_count doesn't handle tags
                            if (isset($campaign->tags) && !empty($campaign->tags)) {
                                $this->db->select('l.id');
                                $this->db->from(db_prefix() . 'leads as l');
                                $this->db->join(db_prefix() . 'taggables as t', 't.rel_id = l.id', 'inner');
                                $this->db->where('l.status', $leads_status);
                                $this->db->where('l.assigned', $device->staffid);
                                $this->db->where('l.phonenumber !=', '');

                                if ($date_filter_type == 'birthday' && !empty($birthday_field)) {
                                    $this->db->join(db_prefix() . 'customfieldsvalues as cfv', "cfv.relid = l.id AND cfv.fieldid = " . (int)$birthday_field . " AND cfv.fieldto = 'leads'", 'inner');
                                    $this->db->where('cfv.value IS NOT NULL');
                                    $this->db->where('cfv.value !=', '');
                                    $this->db->where('MONTH(STR_TO_DATE(cfv.value, \'%Y-%m-%d\')) = MONTH(CURDATE())', null, false);
                                    $this->db->where('DAY(STR_TO_DATE(cfv.value, \'%Y-%m-%d\')) = DAY(CURDATE())', null, false);
                                } else {
                                    $date_field = ($date_filter_type == 'last_contact') ? 'lastcontact' : 'dateadded';
                                    $this->db->where($date_field . ' >=', $date);
                                    $this->db->where($date_field . ' <=', $date_final);
                                }
                                $this->db->group_start();
                                $this->db->where('l.conversation_engine_id IS NULL', null, false);
                                $this->db->or_where('l.conversation_engine_id !=', $campaign->con_id);
                                $this->db->group_end();
                                $this->db->group_start();
                                $this->db->where('l.gpt_thread IS NULL', null, false);
                                $this->db->or_where('l.gpt_thread', '');
                                $this->db->group_end();
                                $this->db->group_start();
                                $this->db->where('l.status_whats IS NULL', null, false);
                                $this->db->or_where('l.status_whats !=', 2);
                                $this->db->group_end();

                                // Add source filter
                                if ($filter_source && !empty($filter_source)) {
                                    $sources = explode(',', $filter_source);
                                    $sources_clean = array_map('intval', $sources);
                                    $this->db->where_in('l.source', $sources_clean);
                                }

                                // Add city filter
                                if ($filter_city && !empty($filter_city)) {
                                    $cities = explode(',', $filter_city);
                                    $cities_clean = array_map('trim', $cities);
                                    $this->db->where_in('l.city', $cities_clean);
                                }

                                // Add state filter
                                if ($filter_state && !empty($filter_state)) {
                                    $states = explode(',', $filter_state);
                                    $states_clean = array_map('trim', $states);
                                    $this->db->where_in('l.state', $states_clean);
                                }

                                $this->db->where_in('t.tag_id', explode(',', $campaign->tags));
                                $this->db->group_by('l.id');

                                $count = $this->db->count_all_results();
                            } else {
                                // Get count of eligible leads (leads that match criteria but haven't been sent by THIS campaign yet)
                                $count = $this->contactcenter_model->get_count_leads_status_cadastrado_staff(
                                    $device->staffid,
                                    $leads_status,
                                    $date,
                                    $date_final,
                                    $campaign->con_id,
                                    $date_filter_type,
                                    $filter_source,
                                    $filter_city,
                                    $filter_state,
                                    $birthday_field
                                );
                            }

                            $total_leads += $count;
                        }

                        // Count how many leads have been sent by any of these active campaigns
                        if (!empty($campaign_ids)) {
                            $this->db->select('COUNT(DISTINCT l.id) as sent_count');
                            $this->db->from(db_prefix() . 'leads as l');
                            $this->db->where('l.assigned', $device->staffid);
                            $this->db->where_in('l.conversation_engine_id', $campaign_ids);
                            $this->db->where('l.conversation_engine_send IS NOT NULL', null, false);
                            $sent_result = $this->db->get()->row();
                            $sent_leads = $sent_result ? intval($sent_result->sent_count) : 0;
                        }

                        $jSON['total_leads'] = $total_leads;
                        $jSON['sent_leads'] = $sent_leads;
                        $jSON['pending_count'] = $total_leads - $sent_leads;
                    }

                    $jSON['success'] = true;
                }
            }
        } else {
            show_404();
        }

        echo json_encode($jSON);
    }

    /**
     * Get follow-up (leads_engine) queue status for device
     */
    public function ajax_get_followup_queue_status()
    {
        $jSON = array('success' => false, 'has_followup' => false, 'total_leads' => 0, 'sent_leads' => 0, 'pending_count' => 0, 'followup_name' => '');

        if ($this->input->is_ajax_request()) {
            $device_id = $this->input->post('device_id');

            if ($device_id) {
                // Get device info
                $this->db->where('dev_id', $device_id);
                $device = $this->db->get(db_prefix() . 'contactcenter_device')->row();

                if ($device) {
                    // Check for active follow-up engines for this device
                    $this->db->where('device_id', $device_id);
                    $this->db->where('status', 1); // Active follow-up engines
                    $followups = $this->db->get(db_prefix() . 'contactcenter_leads_engine')->result();

                    if ($followups && count($followups) > 0) {
                        $jSON['has_followup'] = true;
                        $jSON['followup_name'] = count($followups) > 1 ? count($followups) . ' ' . _l('followups') : (isset($followups[0]->title) ? $followups[0]->title : 'Follow-up');

                        $total_leads = 0;
                        $sent_leads = 0;
                        $currentDateTime = date('Y-m-d H:i:s');

                        // Check if within time window
                        $start_time = strtotime(date("H:i:s"));
                        $end_time = strtotime(date("H:i:s"));
                        $is_within_time_window = false;

                        // Loop through all active follow-ups and sum up totals
                        foreach ($followups as $followup) {
                            $followup_start_time = strtotime($followup->start_time);
                            $followup_end_time = strtotime($followup->end_time);
                            $followup_current_time = strtotime(date("H:i:s"));

                            // Check if this follow-up is within its time window
                            $followup_is_active = ($followup_current_time >= $followup_start_time && $followup_current_time <= $followup_end_time);

                            if (!$followup_is_active) {
                                continue; // Skip if outside time window
                            }

                            if (!$is_within_time_window) {
                                $is_within_time_window = true;
                            }

                            // Hours since last contact
                            $hours_since = isset($followup->hours_since_last_contact) ? intval($followup->hours_since_last_contact) : 24;

                            // Build query for total eligible leads (all leads matching criteria, regardless of lock status)
                            // Ensure fromMe is cast to integer for proper comparison
                            $fromMe = isset($followup->fromMe) ? intval($followup->fromMe) : 0;

                            $sql = "
                                SELECT c.id, c.phonenumber, l.id as leadid, c.msg_fromMe, c.date, c.is_locked
                                FROM `" . db_prefix() . "contactcenter_contact` c
                                INNER JOIN `" . db_prefix() . "leads` l ON c.phonenumber = l.phonenumber
                                WHERE c.msg_fromMe = ?
                                AND c.isGroup = 0
                                AND TIMESTAMPDIFF(HOUR, c.date, ?) >= ?                
                                AND c.session = ?
                            ";

                            $params = [
                                $fromMe,
                                $currentDateTime,
                                $hours_since,
                                $device->dev_token
                            ];

                            // Add status filtering only if leads_status is set (not NULL/empty)
                            if (!empty($followup->leads_status) && $followup->leads_status > 0) {
                                $sql .= " AND l.status = ?";
                                $params[] = $followup->leads_status;
                            }

                            // Add tag filtering if tags are set
                            if (!empty($followup->tags)) {
                                $sql .= " AND EXISTS (
                                    SELECT 1
                                    FROM " . db_prefix() . "taggables AS taggables
                                    WHERE taggables.rel_id = l.id
                                      AND taggables.rel_type = 'lead'
                                      AND taggables.tag_id IN ({$followup->tags})
                                )";
                            }

                            $leads = $this->db->query($sql, $params)->result();

                            if ($leads) {
                                $total_leads += count($leads);

                                // Count sent leads: those that are locked (currently being processed/sent)
                                foreach ($leads as $lead) {
                                    if ($lead->is_locked == 1) {
                                        $sent_leads++;
                                    }
                                }
                            }
                        }

                        // If no follow-up is within time window, set counts to 0
                        if (!$is_within_time_window) {
                            $total_leads = 0;
                            $sent_leads = 0;
                        }

                        $jSON['total_leads'] = $total_leads;
                        $jSON['sent_leads'] = $sent_leads;
                        $jSON['pending_count'] = max(0, $total_leads - $sent_leads);
                    }

                    $jSON['success'] = true;
                }
            }
        } else {
            show_404();
        }

        echo json_encode($jSON);
    }

    /**
     * Media Library - Send media from library to chat
     */
    public function ajax_send_media_from_library()
    {
        $jSON = array('send' => false);

        try {
            if ($this->input->is_ajax_request()) {
                $media_file_path = $this->input->post('media_file_path');
                $phonenumber = $this->input->post('phonenumber');
                $action = $this->input->post('action');
                $staffid_post = $this->input->post('staffid');
                $device_id = $this->input->post('device_id');

                if (!$media_file_path || !$phonenumber) {
                    $jSON['error'] = _l('missing_required_fields');
                    echo json_encode($jSON);
                    return;
                }

                // Get device - use device_id if provided, otherwise use staffid
                $device = null;
                if ($device_id) {
                    $device = $this->contactcenter_model->get_device($device_id);
                }

                // If no device found, try to get device by staffid
                if (!$device && $staffid_post) {
                    $this->db->where('staffid', $staffid_post);
                    $device = $this->db->get(db_prefix() . 'contactcenter_device')->row();
                }

                // If still no device, get default device
                if (!$device) {
                    $this->db->where('dev_type', 1);
                    $device = $this->db->get(db_prefix() . 'contactcenter_device')->row();
                }

                if (!$device) {
                    $jSON['error'] = 'No device found';
                    log_activity("Media library error: No device found. Device ID: {$device_id}, Staff ID: {$staffid_post}");
                    echo json_encode($jSON);
                    return;
                }

                // Use the device's staffid
                $staffid = $device->staffid;
                $device_status = $device->status ?? 'unknown';

                // Check if file exists
                $full_path = FCPATH . $media_file_path;
                if (!file_exists($full_path)) {
                    $jSON['error'] = _l('file_not_found');
                    echo json_encode($jSON);
                    return;
                }

                $staffid = $staffid ?: get_staff_user_id();

                // Determine media type from file extension (must be one of: image, document, video, audio)
                $file_ext = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
                $media_type = 'document'; // default

                if ($action === 'image' || in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $media_type = 'image';
                } elseif (in_array($file_ext, ['mp3', 'wav', 'ogg', 'm4a', 'amr', 'opus'])) {
                    $media_type = 'audio'; // Use 'audio' not 'ptt' - valid enum value
                } elseif (in_array($file_ext, ['mp4', 'avi', 'mov', 'mkv', 'webm', '3gp'])) {
                    $media_type = 'video';
                } else {
                    // For other extensions (pdf, doc, xlsx, etc.), use 'document'
                    $media_type = 'document';
                }

                $fileName = basename($media_file_path);

                // Verify file exists and is readable
                if (!file_exists($full_path) || !is_readable($full_path)) {
                    $jSON['error'] = 'File does not exist or is not readable';
                    log_activity("Media library error: File does not exist or is not readable - {$full_path}");
                    echo json_encode($jSON);
                    return;
                }

                // Copy file to temporary location in uploads/chat (like attachment_chat does)
                // This ensures the file is accessible via URL, matching the attachment_chat flow
                $temp_dir = FCPATH . 'uploads/chat/' . date('Y/m/');
                if (!is_dir($temp_dir)) {
                    mkdir($temp_dir, 0755, true);
                }

                $temp_filename = uniqid() . '_' . $fileName;
                $temp_path = $temp_dir . $temp_filename;
                $temp_url_path = 'chat/' . date('Y/m/') . $temp_filename;

                // Copy file to temp location
                if (!copy($full_path, $temp_path)) {
                    $jSON['error'] = 'Could not copy file to temporary location';
                    log_activity("Media library error: Failed to copy file from {$full_path} to {$temp_path}");
                    echo json_encode($jSON);
                    return;
                }

                // Use site_url() like attachment_chat does (line 14941, 14960, 14981)
                $url = site_url("uploads/{$temp_url_path}");

                // Log for debugging - this will appear in activity log
                log_activity("Media library sending file - Original path: {$media_file_path}, Temp path: {$temp_url_path}, URL: {$url}, Media type: {$media_type}, File size: " . filesize($temp_path));

                // Verify URL format
                if (!preg_match('/^https?:\/\//', $url)) {
                    // Try base_url if site_url doesn't return full URL
                    $url = base_url("uploads/{$temp_url_path}");
                    log_activity("Media library URL corrected to: {$url}");
                }

                // Ensure URL is a valid string before sending
                if (!is_string($url) || empty($url)) {
                    $jSON['error'] = 'Invalid media data - URL is not a valid string';
                    log_activity("Media library error: URL is not a valid string. Type: " . gettype($url) . ", Value: " . var_export($url, true));
                    // Clean up temp file
                    if (file_exists($temp_path)) {
                        @unlink($temp_path);
                    }
                    echo json_encode($jSON);
                    return;
                }

                log_activity("Media library calling send_file - Phone: {$phonenumber}, URL: {$url}, Media type: {$media_type}, Staff ID: {$staffid}, Device ID: {$device->dev_id}, Device Status: {$device_status}");

                // Use send_file directly instead of send_chat
                $Send = $this->contactcenter_model->send_file(
                    $phonenumber,
                    '', // msg - empty for media files
                    $url,
                    $media_type, // This must be one of: image, document, video, audio
                    $staffid,
                    $fileName,
                    $reply_id = null,
                    $queue = true,
                    $type_source = 'crm_user'
                );

                // Log the response for debugging
                log_activity("Media library send_file response: " . json_encode($Send) . " | Type: " . gettype($Send));

                // Check if send_file returned null (device not connected or status check failed)
                if ($Send === null || $Send === false) {
                    $error_type = $Send === null ? 'null' : 'false';
                    $jSON['error'] = 'Failed to send media - device may not be connected or status check failed';
                    $jSON['debug'] = array(
                        'send_result' => $Send,
                        'send_result_type' => $error_type,
                        'media_type' => $media_type,
                        'url' => $url,
                        'phone' => $phonenumber,
                        'staffid' => $staffid,
                        'device_status' => $device_status,
                        'allowed_statuses' => array('inChat', 'CONNECTED', 'open', 'connecting')
                    );
                    log_activity("Media library error: send_file returned {$error_type} - device may not be connected. Staff ID: {$staffid}, Phone: {$phonenumber}, Device Status: {$device_status}, URL: {$url}");
                    // Clean up temp file
                    if (file_exists($temp_path)) {
                        @unlink($temp_path);
                    }
                } elseif (is_array($Send) && !$Send["error"]) {
                    $jSON['send'] = true;
                    log_activity("Media library success: File sent successfully");
                    // Clean up temp file after successful send
                    if (file_exists($temp_path)) {
                        @unlink($temp_path);
                    }
                } else {
                    // Extract error message from response
                    $error_msg = _l('error_sending_media');

                    if (is_array($Send)) {
                        // Try different ways to extract error message
                        if (isset($Send["response"]["message"][0])) {
                            $error_msg = $Send["response"]["message"][0];
                        } elseif (isset($Send["response"]["message"])) {
                            if (is_array($Send["response"]["message"])) {
                                $error_msg = implode(', ', $Send["response"]["message"]);
                            } else {
                                $error_msg = $Send["response"]["message"];
                            }
                        } elseif (isset($Send["message"])) {
                            if (is_array($Send["message"])) {
                                $error_msg = implode(', ', $Send["message"]);
                            } else {
                                $error_msg = $Send["message"];
                            }
                        } elseif (isset($Send["error"])) {
                            $error_msg = is_array($Send["error"]) ? json_encode($Send["error"]) : $Send["error"];
                        }
                    } elseif (is_string($Send)) {
                        $error_msg = $Send;
                    }

                    $jSON['error'] = $error_msg;
                    $jSON['debug'] = array(
                        'send_result' => $Send,
                        'media_type' => $media_type,
                        'url' => $url,
                        'phone' => $phonenumber
                    );

                    log_activity("Media library error sending: {$error_msg} | Media type: {$media_type} | Full response: " . json_encode($Send));
                    // Clean up temp file on error
                    if (file_exists($temp_path)) {
                        @unlink($temp_path);
                    }
                }
            }
        } catch (Exception $e) {
            log_message('error', 'ajax_send_media_from_library error: ' . $e->getMessage());
            $jSON['error'] = $e->getMessage();
        }

        echo json_encode($jSON);
    }

    /**
     * Load Omni Pilot Wizard view
     */
    public function omni_pilot_wizard()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $device_id = $this->input->get('device_id');
        if (!$device_id) {
            echo json_encode(['success' => false, 'message' => 'Device ID required']);
            return;
        }

        $device = $this->contactcenter_model->get_device($device_id);
        if (!$device) {
            echo json_encode(['success' => false, 'message' => 'Device not found']);
            return;
        }

        // Check if user has access to this device
        if (!has_permission('contactcenter', '', 'chat_viwer_all') && get_staff_user_id() != $device->staffid) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            return;
        }

        // Check if user has created any Omni Pilot sessions before
        $has_previous_sessions = false;
        if ($this->db->table_exists(db_prefix() . 'contactcenter_omni_pilot_sessions')) {
            $this->db->where('staffid', get_staff_user_id());
            $has_previous_sessions = $this->db->count_all_results(db_prefix() . 'contactcenter_omni_pilot_sessions') > 0;
        }

        $data['device'] = $device;
        $data['initial_device_id'] = $device_id; // Pass initial device ID for pre-selection
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['members'] = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['tagsArray'] = $this->db->get(db_prefix() . 'tags')->result_array();
        $data['devices'] = $this->contactcenter_model->get_device_by_type(2);
        $data['assistants'] = $this->contactcenter_model->get_assistants_ai();
        $data['has_previous_sessions'] = $has_previous_sessions;

        // Get Brazilian states and cities for random selection
        $this->db->distinct();
        $this->db->select('state');
        $this->db->where('state !=', '');
        $this->db->where('state IS NOT NULL');
        $this->db->where('country', 'Brazil');
        $data['brazilian_states'] = $this->db->get(db_prefix() . 'leads')->result_array();

        // Get categories from import_leads
        $data['categories'] = [
            'restaurants' => _l('import_leads_category_restaurants'),
            'retail stores' => _l('import_leads_category_retail'),
            'services' => _l('import_leads_category_services'),
            'healthcare' => _l('import_leads_category_healthcare'),
            'automotive' => _l('import_leads_category_automotive'),
            'real estate' => _l('import_leads_category_real_estate'),
            'beauty salons' => _l('import_leads_category_beauty'),
            'gyms' => _l('import_leads_category_gyms'),
            'hotels' => _l('import_leads_category_hotels'),
            'law firms' => _l('import_leads_category_law'),
            'accounting' => _l('import_leads_category_accounting'),
        ];

        $html = $this->load->view('omni_pilot_wizard', $data, true);
        echo json_encode([
            'success' => true,
            'html' => $html,
            'categories' => $data['categories'] // Pass categories to JS
        ]);
    }

    /**
     * Validate wizard step
     */
    public function ajax_omni_pilot_step_validate()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $jSON = ['success' => false, 'message' => ''];
        $step = $this->input->post('step');
        $data = $this->input->post('data');

        switch ($step) {
            case 0: // Goal Configuration
                if (empty($data['goal_target']) || empty($data['goal_status_id']) || empty($data['deadline_date'])) {
                    $jSON['message'] = _l('omni_pilot_validation_step_0');
                } else {
                    $jSON['success'] = true;
                }
                break;

            case 1: // Supply (Leads)
                if (empty($data['import_method'])) {
                    $jSON['message'] = _l('omni_pilot_validation_step_1_method');
                } elseif ($data['import_method'] == 'ai' && (empty($data['ai_state']) || empty($data['ai_city']) || empty($data['ai_category']))) {
                    $jSON['message'] = _l('omni_pilot_validation_step_1_ai');
                } elseif ($data['import_method'] == 'file' && empty($data['file_uploaded'])) {
                    $jSON['message'] = _l('omni_pilot_validation_step_1_file');
                } else {
                    $jSON['success'] = true;
                }
                break;

            case 2: // Campaign Strategy
                if (empty($data['device_id'])) {
                    $jSON['message'] = _l('omni_pilot_validation_step_2');
                } else {
                    $jSON['success'] = true;
                }
                break;

            case 3: // First Strike (Message)
                if (empty($data['selected_message'])) {
                    $jSON['message'] = _l('omni_pilot_validation_step_3');
                } else {
                    $jSON['success'] = true;
                }
                break;

            case 4: // Assistant
                $jSON['success'] = true; // Always valid, just confirmation
                break;

            case 5: // Follow-up
                if (empty($data['followup_messages']) || count($data['followup_messages']) < 1) {
                    $jSON['message'] = _l('omni_pilot_validation_step_5');
                } else {
                    $jSON['success'] = true;
                }
                break;

            default:
                $jSON['message'] = _l('omni_pilot_validation_invalid_step');
        }

        echo json_encode($jSON);
    }

    /**
     * Generate AI messages for Step 3
     */
    public function ajax_omni_pilot_generate_messages()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $jSON = ['success' => false, 'messages' => []];

        try {
            $category = $this->input->post('category');
            $context = $this->input->post('context', '');
            $language = $this->input->post('language', 'pt-BR');

            // Generate 5 message variations using AI
            // This will integrate with existing AI assistant service
            $messages = $this->contactcenter_model->generate_omni_pilot_messages($category, $context, 5, $language);

            if ($messages && count($messages) > 0) {
                $jSON['success'] = true;
                $jSON['messages'] = $messages;
            } else {
                $jSON['message'] = _l('omni_pilot_ai_generation_failed');
            }
        } catch (Exception $e) {
            log_activity('Error in ajax_omni_pilot_generate_messages: ' . $e->getMessage(), get_staff_user_id());
            $jSON['message'] = 'Error generating messages: ' . $e->getMessage();
        }

        echo json_encode($jSON);
    }

    /**
     * Get assistant info for a device (Step 4)
     */
    public function ajax_omni_pilot_get_assistant()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $jSON = ['success' => false, 'assistant' => null, 'message' => '', 'assistants' => []];

        try {
            $device_id = $this->input->post('device_id');

            if (!$device_id) {
                $jSON['message'] = 'Device ID required';
                echo json_encode($jSON);
                return;
            }

            $device = $this->contactcenter_model->get_device($device_id);
            if (!$device) {
                $jSON['message'] = 'Device not found';
                echo json_encode($jSON);
                return;
            }

            // Check if user has access to this device
            if (!has_permission('contactcenter', '', 'chat_viwer_all') && get_staff_user_id() != $device->staffid) {
                $jSON['message'] = 'Access denied';
                echo json_encode($jSON);
                return;
            }

            // Get all assistants for the dropdown
            $all_assistants = $this->contactcenter_model->get_assistants_ai();
            $jSON['assistants'] = [];
            if ($all_assistants) {
                foreach ($all_assistants as $assist) {
                    $jSON['assistants'][] = [
                        'id' => (int)$assist->id,
                        'name' => !empty($assist->ai_name) ? $assist->ai_name : ('Assistant ' . $assist->id),
                        'desc' => !empty($assist->ai_desc) ? $assist->ai_desc : ''
                    ];
                }
            }

            // Check device assistant_ai_id field
            $current_assistant_id = null;
            if (isset($device->assistant_ai_id) && $device->assistant_ai_id !== null && $device->assistant_ai_id !== '') {
                $current_assistant_id = (int)$device->assistant_ai_id;
            }

            if ($current_assistant_id && $current_assistant_id > 0) {
                $assistant = $this->contactcenter_model->get_assistants_ai($current_assistant_id);
                if ($assistant) {
                    $jSON['success'] = true;
                    $jSON['assistant'] = [
                        'id' => (int)$assistant->id,
                        'assist_name' => !empty($assistant->ai_name) ? $assistant->ai_name : '',
                        'assist_desc' => !empty($assistant->ai_desc) ? $assistant->ai_desc : ''
                    ];
                    $jSON['current_assistant_id'] = $current_assistant_id;
                } else {
                    $jSON['message'] = _l('omni_pilot_no_assistant');
                    $jSON['current_assistant_id'] = null;
                }
            } else {
                $jSON['message'] = _l('omni_pilot_no_assistant');
                $jSON['current_assistant_id'] = null;
            }
        } catch (Exception $e) {
            log_activity('Error in ajax_omni_pilot_get_assistant: ' . $e->getMessage(), get_staff_user_id());
            $jSON['message'] = 'Error loading assistant information: ' . $e->getMessage();
        }

        echo json_encode($jSON);
    }

    /**
     * Update device assistant (Step 4)
     */
    public function ajax_omni_pilot_update_assistant()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $jSON = ['success' => false, 'message' => ''];
        $device_id = $this->input->post('device_id');
        $assistant_id = $this->input->post('assistant_ai_id');

        if (!$device_id) {
            $jSON['message'] = 'Device ID required';
            echo json_encode($jSON);
            return;
        }

        $device = $this->contactcenter_model->get_device($device_id);
        if (!$device) {
            $jSON['message'] = 'Device not found';
            echo json_encode($jSON);
            return;
        }

        // Check if user has access to this device
        if (!has_permission('contactcenter', '', 'chat_viwer_all') && get_staff_user_id() != $device->staffid) {
            $jSON['message'] = 'Access denied';
            echo json_encode($jSON);
            return;
        }

        // Update device assistant
        $update_data = ['assistant_ai_id' => $assistant_id ? (int)$assistant_id : null];
        $this->db->where('dev_id', $device_id);
        if ($this->db->update(db_prefix() . 'contactcenter_device', $update_data)) {
            $jSON['success'] = true;
            $jSON['message'] = _l('contac_save');
        } else {
            $jSON['message'] = 'Error updating device assistant';
        }

        echo json_encode($jSON);
    }

    /**
     * Generate follow-up messages for Step 5
     */
    public function ajax_omni_pilot_generate_followups()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $jSON = ['success' => false, 'messages' => []];
        $initial_message = $this->input->post('initial_message');
        $category = $this->input->post('category', '');
        $hours = [1, 8, 24, 48, 168]; // 1h, 8h, 24h, 48h, 1 week

        // Generate follow-up messages for each time slot
        $messages = $this->contactcenter_model->generate_omni_pilot_followup_messages($initial_message, $category, $hours);

        if ($messages && count($messages) > 0) {
            $jSON['success'] = true;
            $jSON['messages'] = $messages;
        } else {
            $jSON['message'] = _l('omni_pilot_ai_generation_failed');
        }

        echo json_encode($jSON);
    }

    /**
     * Execute Omni Pilot wizard (background job)
     */
    public function ajax_omni_pilot_execute()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $jSON = ['success' => false, 'message' => '', 'session_id' => null];

        $wizard_data_json = $this->input->post('wizard_data');
        if (empty($wizard_data_json)) {
            $jSON['message'] = _l('omni_pilot_no_data');
            echo json_encode($jSON);
            return;
        }

        // Decode wizard data
        $wizard_data = json_decode($wizard_data_json, true);
        if (!$wizard_data) {
            $jSON['message'] = _l('omni_pilot_no_data');
            echo json_encode($jSON);
            return;
        }

        // Handle media file upload if present
        if (!empty($_FILES['message_media']['name'])) {
            $upload_path = 'contactcenter/omni_pilot_media';
            $this->load->library('upload');

            $config['upload_path'] = FCPATH . 'uploads/' . $upload_path . '/' . date('Y/m');
            $config['allowed_types'] = 'jpg|jpeg|png|gif|mp4|mov|avi|mp3|wav|ogg|pdf|doc|docx';
            $config['max_size'] = 50 * 1024; // 50MB
            $config['encrypt_name'] = true;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0755, true);
            }

            $this->upload->initialize($config);

            if ($this->upload->do_upload('message_media')) {
                $upload_data = $this->upload->data();
                $relative_path = $upload_path . '/' . date('Y/m') . '/' . $upload_data['file_name'];

                // Update wizard data with media path
                if (isset($wizard_data['step_3']['selected_message'])) {
                    $wizard_data['step_3']['selected_message']['media'] = $relative_path;
                    $wizard_data['step_3']['selected_message']['media_type'] = $this->getMediaTypeFromFile($upload_data['file_type']);
                }
            }
        }

        // Create session
        $session_id = $this->contactcenter_model->create_omni_pilot_session($wizard_data);

        if ($session_id) {
            // Initialize session status - don't execute yet, wait for step-by-step execution
            $this->contactcenter_model->update_omni_pilot_status($session_id, 'pending', 0, _l('omni_pilot_starting'));

            $jSON['success'] = true;
            $jSON['session_id'] = $session_id;
            $jSON['message'] = _l('omni_pilot_started');
        } else {
            $jSON['message'] = _l('omni_pilot_session_failed');
        }

        echo json_encode($jSON);
    }

    /**
     * Execute a specific Omni Pilot step
     */
    public function ajax_omni_pilot_execute_step()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $session_id = $this->input->post('session_id');
        $step = $this->input->post('step'); // 'import', 'campaign', 'message', 'followup', 'activate'

        if (!$session_id || !$step) {
            echo json_encode(['success' => false, 'message' => 'Session ID and step are required']);
            return;
        }

        try {
            $result = $this->contactcenter_model->execute_omni_pilot_step($session_id, $step);

            // Ensure result has success flag if not already set
            if (is_array($result) && !isset($result['success'])) {
                // Check for successful indicators in result
                $has_success_indicator = false;

                // Import step success indicators
                if (isset($result['tag_id']) || isset($result['imported_count'])) {
                    $has_success_indicator = true;
                }
                // Campaign step success indicator
                if (isset($result['campaign_id']) && $result['campaign_id'] > 0) {
                    $has_success_indicator = true;
                }
                // Message step success indicator
                if (isset($result['message_result']) && $result['message_result'] > 0) {
                    $has_success_indicator = true;
                }
                // Follow-up step success indicator
                if (isset($result['leads_engine_id']) && $result['leads_engine_id'] > 0) {
                    $has_success_indicator = true;
                }
                // Activate step success indicator
                if (isset($result['completed']) && $result['completed'] === true) {
                    $has_success_indicator = true;
                }

                if ($has_success_indicator) {
                    $result['success'] = true;
                } else {
                    $result['success'] = false;
                }
            }

            // Log the result for debugging
            log_message('debug', 'Omni Pilot step ' . $step . ' executed. Result: ' . json_encode($result));

            // Determine overall success from result
            // If result has success indicators (like message_result, campaign_id, etc.), consider it successful
            $overall_success = true;
            if (is_array($result)) {
                // Check if result explicitly says success is false AND has no success indicators
                if (isset($result['success']) && $result['success'] === false) {
                    // But check if there are success indicators anyway
                    $has_success_indicator =
                        (isset($result['message_result']) && $result['message_result'] > 0) ||
                        (isset($result['campaign_id']) && $result['campaign_id'] > 0) ||
                        (isset($result['tag_id']) && $result['tag_id'] > 0) ||
                        (isset($result['leads_engine_id']) && $result['leads_engine_id'] > 0) ||
                        (isset($result['completed']) && $result['completed'] === true);

                    if ($has_success_indicator) {
                        // Override: if we have success indicators, treat as success
                        $overall_success = true;
                        $result['success'] = true;
                    } else {
                        $overall_success = false;
                    }
                }
            }

            echo json_encode([
                'success' => $overall_success,
                'result' => $result,
                'message' => $overall_success ? 'Step executed successfully' : ($result['message'] ?? 'Step execution completed with warnings'),
                'step' => $step
            ]);
        } catch (Exception $e) {
            log_message('error', 'Omni Pilot step execution error for step ' . $step . ': ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage(), 'step' => $step]);
        }
    }

    /**
     * Get media type from file extension
     */
    private function getMediaTypeFromFile($file_type)
    {
        if (strpos($file_type, 'image/') === 0) {
            return 'image';
        } elseif (strpos($file_type, 'video/') === 0) {
            return 'video';
        } elseif (strpos($file_type, 'audio/') === 0) {
            return 'audio';
        } elseif (strpos($file_type, 'application/pdf') === 0) {
            return 'document';
        }
        return 'file';
    }

    /**
     * Get Omni Pilot progress
     */
    public function ajax_omni_pilot_progress()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $session_id = $this->input->post('session_id');
        if (!$session_id) {
            echo json_encode(['success' => false]);
            return;
        }

        $progress = $this->contactcenter_model->get_omni_pilot_progress($session_id);
        echo json_encode(['success' => true, 'progress' => $progress]);
    }

    /**
     * Get active Omni Pilot session for device
     */
    public function ajax_omni_pilot_get_active_session()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $device_id = $this->input->post('device_id');
        if (!$device_id) {
            echo json_encode(['success' => false, 'message' => 'Device ID required']);
            return;
        }

        $session = $this->contactcenter_model->get_active_omni_pilot_session($device_id);
        if ($session) {
            echo json_encode(['success' => true, 'session_id' => $session->id, 'progress' => $this->contactcenter_model->get_omni_pilot_progress($session->id)]);
        } else {
            echo json_encode(['success' => false, 'session_id' => null]);
        }
    }

    /**
     * Stop/Cancel Omni Pilot session
     */
    public function ajax_omni_pilot_stop()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $session_id = $this->input->post('session_id');
        if (!$session_id) {
            echo json_encode(['success' => false, 'message' => _l('omni_pilot_session_id_required')]);
            return;
        }

        $result = $this->contactcenter_model->stop_omni_pilot_session($session_id);
        if ($result) {
            echo json_encode(['success' => true, 'message' => _l('omni_pilot_stopped_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('omni_pilot_stop_failed')]);
        }
    }

    /**
     * Save Omni Pilot template
     */
    public function ajax_omni_pilot_save_template()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $name = $this->input->post('name');
        $description = $this->input->post('description');
        $wizard_data_json = $this->input->post('wizard_data');
        $template_id = $this->input->post('template_id'); // For updates

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => _l('omni_pilot_template_name_required')]);
            return;
        }

        if (empty($wizard_data_json)) {
            echo json_encode(['success' => false, 'message' => _l('omni_pilot_template_data_required')]);
            return;
        }

        $wizard_data = json_decode($wizard_data_json, true);
        if (!$wizard_data) {
            echo json_encode(['success' => false, 'message' => _l('omni_pilot_invalid_template_data')]);
            return;
        }

        $data = [
            'staffid' => get_staff_user_id(),
            'name' => $name,
            'description' => $description,
            'wizard_data' => json_encode($wizard_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($template_id) {
            // Update existing template
            $this->db->where('id', $template_id);
            $this->db->where('staffid', get_staff_user_id());
            $this->db->update(db_prefix() . 'contactcenter_omni_pilot_templates', $data);
            $result_id = $template_id;
        } else {
            // Create new template
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert(db_prefix() . 'contactcenter_omni_pilot_templates', $data);
            $result_id = $this->db->insert_id();
        }

        if ($result_id) {
            echo json_encode(['success' => true, 'template_id' => $result_id, 'message' => _l('omni_pilot_template_saved')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('omni_pilot_template_save_failed')]);
        }
    }

    /**
     * Get Omni Pilot templates
     */
    public function ajax_omni_pilot_get_templates()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $templates = $this->contactcenter_model->get_omni_pilot_templates(get_staff_user_id());
        echo json_encode(['success' => true, 'templates' => $templates]);
    }

    /**
     * Get single Omni Pilot template
     */
    public function ajax_omni_pilot_get_template()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $template_id = $this->input->post('template_id');
        if (!$template_id) {
            echo json_encode(['success' => false, 'message' => _l('omni_pilot_template_id_required')]);
            return;
        }

        $template = $this->contactcenter_model->get_omni_pilot_template($template_id, get_staff_user_id());
        if ($template) {
            echo json_encode(['success' => true, 'template' => $template]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('omni_pilot_template_not_found')]);
        }
    }

    /**
     * Delete Omni Pilot template
     */
    public function ajax_omni_pilot_delete_template()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $template_id = $this->input->post('template_id');
        if (!$template_id) {
            echo json_encode(['success' => false, 'message' => _l('omni_pilot_template_id_required')]);
            return;
        }

        $result = $this->contactcenter_model->delete_omni_pilot_template($template_id, get_staff_user_id());
        if ($result) {
            echo json_encode(['success' => true, 'message' => _l('omni_pilot_template_deleted')]);
        } else {
            echo json_encode(['success' => false, 'message' => _l('omni_pilot_template_delete_failed')]);
        }
    }

    public function ajax_manual_create_group()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $lead_id = $this->input->post('lead_id');
        $phonenumber = $this->input->post('phonenumber');
        $device_id = $this->input->post('device_id');

        if (empty($lead_id) || empty($phonenumber) || empty($device_id)) {
            echo json_encode([
                'success' => false,
                'message' => _l('error_creating_group_missing_data')
            ]);
            return;
        }

        $result = $this->contactcenter_model->manual_create_group_chat($lead_id, $phonenumber, $device_id);
        echo json_encode($result);
    }

    /**
     * AXIOM - DealPulse: Predictive Scoring
     */
    public function ajax_axiom_deal_pulse()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $lead_id = $this->input->post('lead_id');

        if (empty($lead_id)) {
            echo json_encode(['success' => false, 'message' => 'Lead ID required']);
            return;
        }

        // Get chat history for analysis
        // Note: $lead_id is actually the phonenumber in this context
        $chat_history = $this->contactcenter_model->get_chat_history_for_lead($lead_id);

        // Get enriched lead data (CRM fields, custom fields, related contacts, dates, etc.)
        $lead_data = $this->contactcenter_model->get_lead_data($lead_id);

        // Get Gemini API key
        $gemini_api_key = get_option('contactcenter_gemini_api_key');
        if (empty($gemini_api_key)) {
            $gemini_api_key = get_option('axiom_studio_gemini_api_key');
        }

        if (empty($gemini_api_key)) {
            echo json_encode(['success' => false, 'message' => 'Gemini API not configured']);
            return;
        }

        $cached = $this->_axiom_get_single_cache($lead_id, 'deal_pulse');
        if ($cached !== null) {
            echo json_encode($cached);
            return;
        }

        // Check if chat history is empty
        if (empty($chat_history)) {
            $payload = [
                'success' => true,
                'score' => 50,
                'label' => 'Morno',
                'reasoning' => 'Histórico de conversa vazio'
            ];
            $this->_axiom_set_single_cache($lead_id, 'deal_pulse', $payload);
            echo json_encode($payload);
            return;
        }

        // Build prompt for scoring (Portuguese)
        $prompt = "Você é um analista de vendas especializado em prever a probabilidade de fechamento de negócios.\n\n";
        $prompt .= "Analise esta conversa COMPLETA e calcule a probabilidade de sucesso do negócio (0-100%).\n";
        $prompt .= "IMPORTANTE: A análise deve considerar as mensagens MAIS RECENTES como mais relevantes.\n\n";

        // Include lead data context
        if ($lead_data) {
            $prompt .= $this->_format_lead_context($lead_data, true);
        }
        $prompt .= "ATENÇÃO: Sinais de ALTA probabilidade (80-100%):\n";
        $prompt .= "- Lead expressa desejo de comprar ('QUERO COMPRAR', 'quero adquirir', 'vou comprar', 'estou interessado em comprar', 'to pronto pra comprar', 'estou pronto pra comprar', 'pronto pra comprar', 'ready to buy')\n";
        $prompt .= "- Lead expressa DISPOSIÇÃO para investir/comprar ('disposto a investir', 'estou disposto', 'to disposto', 'disposto a pagar', 'disposto a comprar', 'to pronto pra comprar', 'estou pronto')\n";
        $prompt .= "- Lead menciona valores específicos de investimento ('R$100 mil', 'R$ 100 mil', '100 mil reais', 'investir X reais')\n";
        $prompt .= "- Lead menciona condições de compra/investimento ('se você conseguir me provar', 'se for verdade', 'se funcionar')\n";
        $prompt .= "- Lead pergunta sobre preço, condições de pagamento, formas de pagamento, entrega\n";
        $prompt .= "- Lead pergunta sobre GARANTIAS ('qual a garantia', 'que garantia', 'quais garantias', 'tem garantia', 'que garantias você oferece', 'guarantee')\n";
        $prompt .= "- Lead menciona PAGAR HOJE/AGORA ('se eu pagar hoje', 'vou pagar agora', 'pagar hoje', 'pagar agora', 'pago hoje', 'pago agora')\n";
        $prompt .= "- Lead pede proposta ou orçamento\n";
        $prompt .= "- Lead demonstra urgência\n";
        $prompt .= "IMPORTANTE: Se o lead pergunta sobre GARANTIAS junto com PAGAMENTO (ex: 'se eu pagar hoje, qual a garantia'), isso é um sinal MUITO FORTE de interesse avançado - o lead está na fase de FECHAMENTO. Score deve ser ALTO (80-95%).\n";
        $prompt .= "IMPORTANTE: Mesmo que o lead coloque condições ('se você conseguir me provar'), se ele expressa DISPOSIÇÃO para investir/comprar, isso é um sinal FORTE de interesse. Score deve ser ALTO (70-90%).\n\n";
        $prompt .= "Sinais de MÉDIA probabilidade (31-70%):\n";
        $prompt .= "- Lead faz perguntas gerais sobre produtos/serviços\n";
        $prompt .= "- Lead demonstra interesse mas ainda não decidiu\n\n";
        $prompt .= "Sinais de BAIXA probabilidade (0-30%):\n";
        $prompt .= "- Lead expressa desconfiança ou negação ('isso é mentira', 'isso é golpe', 'não acredito', 'não confio', 'isso é verdade?', 'é mentira', 'nao acho que isso eh mentira', 'isso eh mentira')\n";
        $prompt .= "- Lead expressa desinteresse ou vontade de encerrar ('não vou mais falar', 'vou parar de falar', 'não quero mais', 'não tenho interesse', 'não estou interessado')\n";
        $prompt .= "- Lead pede para ser removido ou encerra a conversa\n";
        $prompt .= "- Apenas perguntas exploratórias sem demonstração de interesse\n";
        $prompt .= "- Lead não demonstra intenção de compra\n\n";
        $prompt .= "REGRA CRÍTICA: Analise as mensagens MAIS RECENTES PRIMEIRO. Se o lead disse recentemente que:\n";
        $prompt .= "- Expressou desconfiança ou disse que 'é mentira' / 'não acredito' → probabilidade DEVE ser BAIXA (0-30%)\n";
        $prompt .= "- Disse que vai parar de falar ou não quer mais continuar → probabilidade DEVE ser BAIXA (0-30%)\n";
        $prompt .= "- Mudou de opinião recentemente (de positivo para negativo) → a análise DEVE refletir a opinião MAIS RECENTE, independente de sinais positivos anteriores.\n\n";

        // Log chat history being sent (first 10 and last 10 messages)
        $history_sample = array_merge(
            array_slice($chat_history, 0, 10),
            array_slice($chat_history, -10)
        );
        log_activity("AXIOM DealPulse: Chat History Sample (first 10 + last 10): " . json_encode($history_sample, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $prompt .= "=== HISTÓRICO DA CONVERSA ===\n";
        $prompt .= "IMPORTANTE: As mensagens MAIS RECENTES (últimas 10) são as MAIS RELEVANTES. Se o contexto mudou ou o lead mudou de assunto, FOCE APENAS nas mensagens mais recentes e IGNORE mensagens antigas que não são relevantes para o contexto atual.\n\n";

        // Show LAST 10 messages FIRST (most recent context)
        $recent_messages = array_slice($chat_history, -10);
        $prompt .= "--- ÚLTIMAS 10 MENSAGENS (CONTEXTO ATUAL - MAIS IMPORTANTE) ---\n";
        foreach ($recent_messages as $msg) {
            $fromMe = isset($msg['fromMe']) ? (int)$msg['fromMe'] : 1;
            $sender = $fromMe ? 'Agente' : 'Lead';
            $date = !empty($msg['date']) ? date('d/m/Y H:i', strtotime($msg['date'])) : '';
            $text = trim($msg['text'] ?? '');
            if (!empty($text)) {
                $prompt .= "[{$date}] {$sender}: {$text}\n";
            }
        }

        // Show older messages as additional context (if any)
        $older_messages = array_slice($chat_history, 0, -10);
        if (!empty($older_messages)) {
            $prompt .= "\n--- MENSAGENS ANTERIORES (CONTEXTO HISTÓRICO - use apenas se relevante para o contexto atual) ---\n";
            foreach (array_slice($older_messages, -20) as $msg) {
                $fromMe = isset($msg['fromMe']) ? (int)$msg['fromMe'] : 1;
                $sender = $fromMe ? 'Agente' : 'Lead';
                $date = !empty($msg['date']) ? date('d/m/Y H:i', strtotime($msg['date'])) : '';
                $text = trim($msg['text'] ?? '');
                if (!empty($text)) {
                    $prompt .= "[{$date}] {$sender}: {$text}\n";
                }
            }
        }

        $prompt .= "\nREGRA CRÍTICA: Se há mensagens antigas sobre assuntos diferentes (como clínica de estética, criolipólise, ou outros temas não relacionados ao contexto atual), IGNORE essas mensagens antigas e FOQUE APENAS no contexto das mensagens MAIS RECENTES (últimas 10).\n";
        // Add explicit instruction about latest message priority
        $last_message_text = '';
        if (!empty($chat_history)) {
            $last_msg = end($chat_history);
            $fromMe = isset($last_msg['fromMe']) ? (int)$last_msg['fromMe'] : 1;
            if ($fromMe == 0) {
                $last_message_text = trim($last_msg['text'] ?? '');
            }
        }

        if (!empty($last_message_text)) {
            $prompt .= "\n=== ÚLTIMA MENSAGEM DO LEAD (MAIS IMPORTANTE) ===\n";
            $prompt .= $last_message_text . "\n\n";
            $prompt .= "ATENÇÃO: Esta é a mensagem MAIS RECENTE do lead. Ela DEVE ter o maior peso na análise.\n\n";
        }

        $prompt .= "\nForneça SOMENTE uma resposta JSON válida (sem texto adicional):\n";
        $prompt .= '{"score": 85, "label": "Quente", "reasoning": "Lead expressou claramente desejo de comprar"}' . "\n";
        $prompt .= "Use os rótulos em português: Frio (0-30%), Morno (31-70%), Quente (71-100%).\n";
        $prompt .= "Se o lead disse 'QUERO COMPRAR', 'DISPOSTO A INVESTIR', 'estou disposto a investir R$X', 'pagar hoje', 'vou pagar hoje', ou similar, a probabilidade deve ser acima de 70-90%.\n";
        $prompt .= "🔥 ATENÇÃO ESPECIAL: Se o lead pergunta sobre GARANTIAS junto com PAGAMENTO (ex: 'se eu pagar hoje, qual a garantia'), isso é um SINAL MUITO FORTE de interesse avançado - o lead está na fase de FECHAMENTO. Score deve ser ALTO (80-95%), não baixo.\n";
        $prompt .= "ATENÇÃO ESPECIAL: Se o lead disse 'disposto a investir' ou mencionou valores específicos (ex: 'R$100 mil'), mesmo com condições ('se você conseguir me provar'), isso é um SINAL FORTE de interesse. Score deve ser ALTO (70-90%), não baixo.\n";
        $prompt .= "Se o lead pergunta sobre GARANTIAS (sem contexto negativo), isso indica interesse sério. Score deve ser ALTO (75-90%), não baixo.\n";
        $prompt .= "Se o lead disse 'é mentira', 'não acredito', 'desconfio' ou similar (SEM mencionar disposição para investir ou pagar), a probabilidade DEVE ser 0-30%.\n";
        $prompt .= "CRÍTICO: A última mensagem do lead tem precedência sobre todas as mensagens anteriores. Se a última mensagem expressa DISPOSIÇÃO para investir/comprar/pagar OU pergunta sobre garantias/pagamento (mesmo com condições), o score DEVE ser ALTO (70-95%). Se a última mensagem é negativa (desconfiança, negação, SEM disposição para pagar), o score DEVE ser baixo (0-30%).";

        // Debug: Log comprehensive information to Activity Log
        log_activity("AXIOM DealPulse: Analysis Start - Lead ID: {$lead_id}, Messages: " . count($chat_history));
        if (!empty($chat_history)) {
            log_activity("AXIOM DealPulse: Last 3 Messages: " . substr(json_encode(array_slice($chat_history, -3)), 0, 500));
            if (!empty($last_message_text)) {
                log_activity("AXIOM DealPulse: Last Lead Message: " . substr($last_message_text, 0, 200));
            }
        }
        if ($lead_data) {
            log_activity("AXIOM DealPulse: Lead Data - Name: " . ($lead_data['name'] ?? 'N/A') . ", Status: " . ($lead_data['status'] ?? 'N/A'));
        }
        log_activity("AXIOM DealPulse: Prompt Length: " . strlen($prompt) . " chars");

        $result = $this->_call_gemini_api($gemini_api_key, $prompt);

        // Log AI response
        log_activity("AXIOM DealPulse: AI Response - Score: " . ($result['score'] ?? 'N/A') . "%, Label: " . ($result['label'] ?? 'N/A') . ", Full: " . substr(json_encode($result), 0, 300));

        if (isset($result['error'])) {
            log_activity("AXIOM DealPulse: API Error - " . substr(json_encode($result), 0, 300));

            // Fallback: Try to calculate score based on keywords in chat history
            $fallback_score = 50;
            $fallback_label = 'Morno';

            if (!empty($chat_history)) {
                $all_text = '';
                foreach ($chat_history as $msg) {
                    if (isset($msg['text'])) {
                        $all_text .= ' ' . strtolower($msg['text']);
                    }
                }

                // Check for negative signals FIRST (most recent messages have priority)
                // Distrust/negation keywords (check these first as they're most critical)
                $distrust_keywords = ['isso é mentira', 'isso eh mentira', 'nao acho que isso eh mentira', 'é mentira', 'eh mentira', 'isso é golpe', 'isso eh golpe', 'não acredito', 'nao acredito', 'não confio', 'nao confio', 'isso é verdade', 'isso eh verdade'];
                // General negative keywords
                $negative_keywords = ['vou parar', 'não vou mais', 'nao vou mais', 'não quero mais', 'nao quero mais', 'não tenho interesse', 'nao tenho interesse', 'não estou interessado', 'nao estou interessado', 'vou parar de falar', 'não quero continuar', 'nao quero continuar'];

                $recent_text = '';
                // Get last 5 messages for recent context (prioritize most recent)
                foreach (array_slice($chat_history, -5) as $msg) {
                    if (isset($msg['text'])) {
                        $recent_text .= ' ' . strtolower($msg['text']);
                    }
                }

                // Check for distrust first (most critical - leads to very low score)
                $has_distrust = false;
                foreach ($distrust_keywords as $keyword) {
                    if (strpos($recent_text, $keyword) !== false) {
                        $has_distrust = true;
                        break;
                    }
                }

                // Check for other negative signals
                $has_negative_signal = false;
                if (!$has_distrust) {
                    foreach ($negative_keywords as $keyword) {
                        if (strpos($recent_text, $keyword) !== false) {
                            $has_negative_signal = true;
                            break;
                        }
                    }
                }

                if ($has_distrust) {
                    // Very low score if distrust in recent messages
                    $fallback_score = 10;
                    $fallback_label = 'Frio';
                } elseif ($has_negative_signal) {
                    // Low score if negative signals in recent messages
                    $fallback_score = 15;
                    $fallback_label = 'Frio';
                } else {
                    // Check for buying/investment intent keywords (including conditional buying)
                    $buying_keywords = [
                        'quero comprar',
                        'vou comprar',
                        'quero adquirir',
                        'estou interessado em comprar',
                        'to pronto pra comprar',
                        'estou pronto pra comprar',
                        'pronto pra comprar',
                        'ready to buy',
                        'disposto a investir',
                        'estou disposto',
                        'to disposto',
                        'disposto a pagar',
                        'disposto a comprar',
                        'investir r$',
                        'investir R$',
                        'r$100',
                        'r$ 100',
                        '100 mil',
                        'mil reais',
                        'preço',
                        'valor',
                        'pagamento',
                        'proposta',
                        'orçamento',
                        'garantia',
                        'garantias',
                        'qual a garantia',
                        'que garantia',
                        'tem garantia',
                        'guarantee',
                        'pagar hoje',
                        'pagar agora',
                        'pago hoje',
                        'pago agora',
                        'vou pagar hoje',
                        'vou pagar agora',
                        'se eu pagar',
                        'se eu pagar hoje',
                        'se eu pagar agora'
                    ];

                    // Check recent messages first for investment intent
                    $recent_has_investment = false;
                    $investment_amount = 0;
                    foreach (array_slice($chat_history, -3) as $msg) {
                        if (isset($msg['text'])) {
                            $msg_lower = strtolower($msg['text']);
                            // Check for investment keywords in recent messages (including "pronto pra comprar", payment, guarantees)
                            if (preg_match('/disposto.*investir|investir.*r\$|r\$.*mil|r\$.*reais|pronto.*pra.*comprar|to pronto|ready.*to.*buy|pagar.*hoje|pagar.*agora|qual.*garantia|que.*garantia|garantia.*você/i', $msg_lower)) {
                                $recent_has_investment = true;
                                // Try to extract amount
                                if (preg_match('/r\$\s*(\d+)\s*(mil|milh[ao]|k)/i', $msg_lower, $matches)) {
                                    $investment_amount = intval($matches[1]) * (strpos(strtolower($matches[2]), 'mil') !== false ? 1000 : 1000000);
                                }
                                break;
                            }
                        }
                    }

                    $found_keywords = 0;
                    foreach ($buying_keywords as $keyword) {
                        if (strpos($recent_text, $keyword) !== false) {
                            $found_keywords++;
                        }
                    }

                    if ($recent_has_investment) {
                        // High score if recent message mentions investment/willingness to invest
                        $fallback_score = 85;
                        $fallback_label = 'Quente';
                    } elseif ($found_keywords > 0) {
                        $fallback_score = min(85, 50 + ($found_keywords * 15));
                        $fallback_label = $fallback_score >= 71 ? 'Quente' : 'Morno';
                    }
                }
            }

            $payload = [
                'success' => true,
                'score' => $fallback_score,
                'label' => $fallback_label,
                'reasoning' => 'Análise baseada em palavras-chave (API indisponível)'
            ];
            $this->_axiom_set_single_cache($lead_id, 'deal_pulse', $payload);
            echo json_encode($payload);
            return;
        }

        // Parse response - handle both object and array responses
        $score = 50;
        $label = 'Morno';
        $reasoning = '';

        if (is_array($result)) {
            $score = isset($result['score']) ? intval($result['score']) : 50;
            $label = isset($result['label']) ? $result['label'] : 'Morno';
            $reasoning = isset($result['reasoning']) ? $result['reasoning'] : '';
        } elseif (is_numeric($result)) {
            $score = intval($result);
        }

        // Map labels to Portuguese
        $labelMap = ['Cold' => 'Frio', 'Warm' => 'Morno', 'Hot' => 'Quente'];
        $label = isset($result['label']) ? $result['label'] : ($score <= 30 ? 'Frio' : ($score <= 70 ? 'Morno' : 'Quente'));
        if (isset($labelMap[$label])) {
            $label = $labelMap[$label];
        }

        $payload = [
            'success' => true,
            'score' => max(0, min(100, $score)),
            'label' => $label,
            'reasoning' => $result['reasoning'] ?? ''
        ];
        $this->_axiom_set_single_cache($lead_id, 'deal_pulse', $payload);
        echo json_encode($payload);
    }

    /**
     * AXIOM - ClientDNA: Sentiment Analysis
     */
    public function ajax_axiom_client_dna()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $lead_id = $this->input->post('lead_id');

        if (empty($lead_id)) {
            echo json_encode(['success' => false]);
            return;
        }

        $chat_history = $this->contactcenter_model->get_chat_history_for_lead($lead_id);

        // Get enriched lead data (CRM fields, custom fields, related contacts, dates, etc.)
        $lead_data = $this->contactcenter_model->get_lead_data($lead_id);

        $gemini_api_key = get_option('contactcenter_gemini_api_key');
        if (empty($gemini_api_key)) {
            $gemini_api_key = get_option('axiom_studio_gemini_api_key');
        }

        if (empty($gemini_api_key)) {
            echo json_encode(['success' => false]);
            return;
        }

        $cached = $this->_axiom_get_single_cache($lead_id, 'client_dna');
        if ($cached !== null) {
            echo json_encode($cached);
            return;
        }

        // Check if chat history is empty
        if (empty($chat_history)) {
            $payload = ['success' => true, 'sentiment' => ['emoji' => '🤔', 'label' => 'Neutro'], 'tags' => []];
            $this->_axiom_set_single_cache($lead_id, 'client_dna', $payload);
            echo json_encode($payload);
            return;
        }

        // Debug: Log comprehensive information for ClientDNA
        log_activity("AXIOM ClientDNA: Analysis Start - Lead ID: {$lead_id}, Messages: " . count($chat_history));

        // Get last lead message FIRST for emphasis
        $last_lead_message_text = '';
        if (!empty($chat_history)) {
            foreach (array_reverse($chat_history) as $msg) {
                $fromMe = isset($msg['fromMe']) ? (int)$msg['fromMe'] : 1;
                if ($fromMe == 0) {
                    $last_lead_message_text = trim($msg['text'] ?? '');
                    break;
                }
            }
        }

        // Build prompt with last message emphasized at the start
        $prompt = "Analise o sentimento e extraia características-chave das mensagens do LEAD nesta conversa.\n\n";

        // Emphasize the LAST lead message at the beginning
        if (!empty($last_lead_message_text)) {
            $prompt .= "⚠️⚠️⚠️ ÚLTIMA MENSAGEM DO LEAD (ANALISE ESTA PRIMEIRO - É A MAIS IMPORTANTE) ⚠️⚠️⚠️\n";
            $prompt .= "\"{$last_lead_message_text}\"\n\n";
            $prompt .= "REGRA CRÍTICA: O sentimento DEVE refletir APENAS esta última mensagem do lead, não mensagens antigas. Se o lead mudou de opinião ou fez uma nova pergunta, o sentimento DEVE refletir o estado ATUAL, não o passado.\n\n";

            // Detect specific intents in last message
            if (preg_match('/golpe|scam|fraude|mentira|não acredito|não confio|desconfio|suspeito|to achando que é golpe|isso eh golpe|parece golpe|achei que é golpe|isso é mentira|isso eh mentira|não é verdade|não confio nisso/i', $last_lead_message_text)) {
                $prompt .= "🔥 ATENÇÃO CRÍTICA: A última mensagem expressa DESCONFIANÇA. O sentimento DEVE ser 'Desconfiado' (emoji: 😟 ou 🚫), NÃO 'Curioso' ou qualquer outro.\n\n";
            }

            if (preg_match('/quero comprar|quero adquirir|vou comprar|estou interessado em comprar|to pronto pra comprar|estou pronto pra comprar|pronto pra comprar|estou disposto a investir|ready to buy/i', $last_lead_message_text)) {
                $prompt .= "🔥 ATENÇÃO: A última mensagem expressa INTENÇÃO DE COMPRA. O sentimento DEVE ser 'Interessado' (emoji: 🎯 ou 💰), NÃO 'Curioso'.\n\n";
            }

            if (preg_match('/como paga|como pagar|forma de pagamento|como é o pagamento|condições de pagamento|valor|preço|quanto|parcela|parcelamento|pagar hoje|pagar agora|pago hoje|pago agora/i', $last_lead_message_text)) {
                $prompt .= "🔥 ATENÇÃO: A última mensagem pergunta sobre PAGAMENTO. O sentimento DEVE ser 'Interessado' (emoji: 🎯 ou 💰), pois o lead está avançando na conversa de vendas (fase de fechamento), NÃO 'Curioso' ou 'Desconfiado'.\n\n";
            }

            if (preg_match('/garantia|garantias|qual a garantia|que garantia|tem garantia|quais garantias|que garantias você oferece|guarantee/i', $last_lead_message_text)) {
                $prompt .= "🔥 ATENÇÃO: A última mensagem pergunta sobre GARANTIAS. Se o lead está perguntando sobre garantias, especialmente junto com pagamento ('se eu pagar hoje, qual a garantia'), isso indica INTERESSE AVANÇADO - o lead está na fase de FECHAMENTO/NEGOCIAÇÃO.\n";
                $prompt .= "O sentimento DEVE ser 'Interessado' (emoji: 🎯 ou 💰), NÃO 'Desconfiado' ou 'Curioso'. Perguntar sobre garantias antes de pagar é NORMAL e indica SERIEDADE, não desconfiança.\n\n";
            }
        }

        $prompt .= "REGRA CRÍTICA: Analise as mensagens MAIS RECENTES PRIMEIRO. O sentimento DEVE refletir a opinião MAIS RECENTE do lead.\n\n";
        $prompt .= "IMPORTANTE - DETECÇÃO DE SENTIMENTOS:\n";
        $prompt .= "- Se o lead disse 'QUERO COMPRAR', 'quero adquirir', 'vou comprar', 'to pronto pra comprar', 'estou pronto pra comprar', 'estou disposto a investir', perguntou sobre pagamento ('como paga', 'valor', 'preço', 'pagar hoje', etc.) ou garantias ('qual a garantia', 'tem garantia', etc.), o sentimento deve ser 'Interessado' (emoji: 🎯 ou 💰), NÃO 'Curioso' ou 'Desconfiado'.\n";
        $prompt .= "- Se o lead disse QUALQUER variação de: 'golpe', 'scam', 'fraude', 'mentira', 'não acredito', 'não confio', 'desconfio', 'suspeito', 'to achando que é golpe', 'isso eh golpe', 'parece golpe', 'achei que é golpe', 'isso é mentira', 'isso eh mentira', 'não é verdade', 'não confio nisso', o sentimento DEVE ser 'Desconfiado' (emoji: 😟 ou 🚫), NÃO 'Curioso' ou 'Interessado'.\n";
        $prompt .= "- Se o lead expressa dúvidas sobre legitimidade, credibilidade ou confiabilidade, o sentimento DEVE ser 'Desconfiado'.\n";
        $prompt .= "- 'Curioso' (emoji: 🤔) deve ser usado APENAS quando o lead faz perguntas exploratórias SEM expressar desconfiança, dúvidas sobre legitimidade, intenção de compra, ou perguntas sobre pagamento/preço.\n";
        $prompt .= "- Se o lead mudou de opinião recentemente (de positivo para negativo, ou vice-versa), o sentimento DEVE refletir a opinião MAIS RECENTE.\n\n";

        // Include lead data context
        if ($lead_data) {
            $prompt .= $this->_format_lead_context($lead_data, true);
        }
        // Log chat history being sent (first 10 and last 10 messages)
        $history_sample = array_merge(
            array_slice($chat_history, 0, 10),
            array_slice($chat_history, -10)
        );
        log_activity("AXIOM ClientDNA: Chat History Sample (first 10 + last 10): " . json_encode($history_sample, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $prompt .= "=== MENSAGENS DO LEAD ===\n";
        $prompt .= "IMPORTANTE: As mensagens MAIS RECENTES (últimas 10) são as MAIS RELEVANTES. Se o contexto mudou ou o lead mudou de assunto, FOCE APENAS nas mensagens mais recentes e IGNORE mensagens antigas que não são relevantes para o contexto atual.\n\n";

        // Extract lead messages (fromMe == 0)
        $lead_messages = [];
        foreach ($chat_history as $msg) {
            $fromMe = isset($msg['fromMe']) ? (int)$msg['fromMe'] : 1;
            if ($fromMe == 0) {
                $lead_messages[] = $msg;
            }
        }

        // Show LAST 10 lead messages FIRST (most recent context)
        $recent_lead_messages = array_slice($lead_messages, -10);
        $prompt .= "--- ÚLTIMAS 10 MENSAGENS DO LEAD (CONTEXTO ATUAL - MAIS IMPORTANTE) ---\n";
        foreach ($recent_lead_messages as $msg) {
            $date = !empty($msg['date']) ? date('d/m/Y H:i', strtotime($msg['date'])) : '';
            $text = trim($msg['text'] ?? '');
            if (!empty($text)) {
                $prompt .= "[{$date}] Lead: {$text}\n";
            }
        }

        // Show older lead messages as additional context (if any)
        $older_lead_messages = array_slice($lead_messages, 0, -10);
        if (!empty($older_lead_messages)) {
            $prompt .= "\n--- MENSAGENS ANTERIORES DO LEAD (CONTEXTO HISTÓRICO - use apenas se relevante para o contexto atual) ---\n";
            foreach (array_slice($older_lead_messages, -20) as $msg) {
                $date = !empty($msg['date']) ? date('d/m/Y H:i', strtotime($msg['date'])) : '';
                $text = trim($msg['text'] ?? '');
                if (!empty($text)) {
                    $prompt .= "[{$date}] Lead: {$text}\n";
                }
            }
        }

        $lead_messages_count = count($lead_messages);
        $prompt .= "\nREGRA CRÍTICA: Se há mensagens antigas sobre assuntos diferentes (como clínica de estética, criolipólise, ou outros temas não relacionados ao contexto atual), IGNORE essas mensagens antigas e FOQUE APENAS no contexto das mensagens MAIS RECENTES (últimas 10).\n";

        log_activity("AXIOM ClientDNA: Lead Messages Count: {$lead_messages_count}, Last Message: " . substr($last_lead_message_text, 0, 200));
        if ($lead_messages_count == 0 && !empty($chat_history)) {
            log_activity("AXIOM ClientDNA: Warning - No lead messages found, but chat history exists");
        }
        if ($lead_data) {
            log_activity("AXIOM ClientDNA: Lead Data - Name: " . ($lead_data['name'] ?? 'N/A') . ", Status: " . ($lead_data['status'] ?? 'N/A'));
        }
        log_activity("AXIOM ClientDNA: Prompt Length: " . strlen($prompt) . " chars");

        $prompt .= "\nResponda SOMENTE com JSON válido (sem texto adicional):\n";
        $prompt .= '{"sentiment": {"emoji": "🎯", "label": "Interessado"}, "tags": ["AltoInteresse", "TomadorDeDecisão"]}' . "\n";
        $prompt .= "Sentimentos possíveis em português:\n";
        $prompt .= "- 'Interessado' (emoji: 🎯 ou 💰) - quando há intenção de compra clara\n";
        $prompt .= "- 'Desconfiado' (emoji: 😟 ou 🚫) - quando expressa desconfiança, dúvidas sobre legitimidade, diz 'golpe', 'scam', 'fraude', 'mentira', 'não acredito', 'não confio', ou qualquer variação dessas palavras\n";
        $prompt .= "- 'Curioso' (emoji: 🤔) - quando apenas faz perguntas exploratórias SEM desconfiança ou intenção de compra\n";
        $prompt .= "- 'Frustrado' (emoji: 😤) - quando demonstra irritação\n";
        $prompt .= "- 'Positivo' (emoji: 😊) - quando demonstra satisfação\n";
        $prompt .= "- 'Neutro' (emoji: 😐) - quando não há emoção clara\n";
        $prompt .= "\nEXEMPLOS DE DETECÇÃO:\n";
        $prompt .= "- 'To achando que isso eh um golpe' → Sentimento: 'Desconfiado', Tag: 'Desconfianca'\n";
        $prompt .= "- 'Isso é mentira' → Sentimento: 'Desconfiado', Tag: 'Desconfianca'\n";
        $prompt .= "- 'Não confio nisso' → Sentimento: 'Desconfiado', Tag: 'Desconfianca'\n";
        $prompt .= "- 'Quero comprar' → Sentimento: 'Interessado', Tag: 'AltoInteresse'\n";
        $prompt .= "- 'O que vocês fazem?' → Sentimento: 'Curioso', Tag: 'BuscaPorInformacao'\n";
        $prompt .= "\nTags em português, sem espaços: AltoInteresse, BuscaPorInformacao, SensivelAoPreco, TomadorDeDecisao, AltaUrgencia, Desconfianca, Negativo";

        $result = $this->_call_gemini_api($gemini_api_key, $prompt);

        // Log AI response
        $sentiment_label = isset($result['sentiment']['label']) ? $result['sentiment']['label'] : 'N/A';
        $tags_count = isset($result['tags']) ? count($result['tags']) : 0;
        log_activity("AXIOM ClientDNA: AI Response - Sentiment: {$sentiment_label}, Tags: {$tags_count}");

        if (isset($result['error'])) {
            log_activity("AXIOM ClientDNA: API Error - " . substr(json_encode($result), 0, 300));
            $payload = ['success' => true, 'sentiment' => ['emoji' => '🤔', 'label' => 'Neutral'], 'tags' => []];
            $this->_axiom_set_single_cache($lead_id, 'client_dna', $payload);
            echo json_encode($payload);
            return;
        }

        $final_response = [
            'success' => true,
            'sentiment' => $result['sentiment'] ?? ['emoji' => '🤔', 'label' => 'Neutral'],
            'tags' => $result['tags'] ?? []
        ];
        $this->_axiom_set_single_cache($lead_id, 'client_dna', $final_response);
        log_activity("AXIOM ClientDNA: Final Response - Sentiment: " . ($final_response['sentiment']['label'] ?? 'N/A') . ", Tags: " . count($final_response['tags'] ?? []));
        echo json_encode($final_response);
    }

    /**
     * AXIOM - EchoSense: Audio Transcription
     */
    public function ajax_axiom_echo_sense()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $audio_url = $this->input->post('audio_url');
        $lead_id = $this->input->post('lead_id');

        if (empty($audio_url)) {
            echo json_encode(['success' => false, 'message' => 'Audio URL required']);
            return;
        }

        // Note: For actual audio transcription, you would need to:
        // 1. Download the audio file
        // 2. Convert to base64 or use Google Speech-to-Text API
        // 3. For now, return a placeholder

        // In a real implementation, you would transcribe the audio here
        // This is a placeholder that would need to be implemented with actual audio processing

        echo json_encode([
            'success' => true,
            'transcript' => 'Audio transcription would be implemented here using Google Speech-to-Text API or similar service.',
            'tone' => 'Voice indicates neutral tone'
        ]);
    }

    /**
     * AXIOM - StratPath: Strategic Suggestions
     */
    public function ajax_axiom_strat_path()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $lead_id = $this->input->post('lead_id');

        if (empty($lead_id)) {
            echo json_encode(['success' => false]);
            return;
        }

        $chat_history = $this->contactcenter_model->get_chat_history_for_lead($lead_id);

        // Get enriched lead data (CRM fields, custom fields, related contacts, dates, etc.)
        $lead_data = $this->contactcenter_model->get_lead_data($lead_id);

        // Get device information (for sales knowledge/product context)
        $device = null;
        $sales_knowledge = null;

        // Try multiple methods to get the device for this lead
        if (!empty($lead_id)) {
            // Method 1: Get device from contact record (session/token)
            $this->db->where('phonenumber', $lead_id);
            $this->db->where('isGroup', 0);
            $this->db->limit(1);
            $contact = $this->db->get(db_prefix() . 'contactcenter_contact')->row();

            if ($contact && !empty($contact->session)) {
                $device = $this->contactcenter_model->get_device_token($contact->session);
            }

            // Method 2: If device not found, try multiple contacts with same phonenumber
            if (!$device && $contact) {
                // Get all contacts with this phonenumber and try to find a device using session
                $this->db->select('session');
                $this->db->where('phonenumber', $lead_id);
                $this->db->where('isGroup', 0);
                $this->db->where('session IS NOT NULL');
                $this->db->where('session !=', '');
                $this->db->order_by('id', 'DESC');
                $this->db->limit(5);
                $contacts = $this->db->get(db_prefix() . 'contactcenter_contact')->result();

                foreach ($contacts as $contact_item) {
                    if (!empty($contact_item->session)) {
                        $device = $this->contactcenter_model->get_device_token($contact_item->session);
                        if ($device) {
                            break;
                        }
                    }
                }
            }
        }

        // If device found, get sales knowledge
        if ($device && !empty($device->sales_knowledge)) {
            $sales_knowledge = trim($device->sales_knowledge);
            log_activity("AXIOM StratPath: Device found - Device ID: " . ($device->dev_id ?? 'N/A') . ", Sales Knowledge length: " . strlen($sales_knowledge) . " chars");
        } else {
            log_activity("AXIOM StratPath: Device not found or no sales knowledge for lead: " . $lead_id);
        }

        $gemini_api_key = get_option('contactcenter_gemini_api_key');
        if (empty($gemini_api_key)) {
            $gemini_api_key = get_option('axiom_studio_gemini_api_key');
        }

        if (empty($gemini_api_key)) {
            echo json_encode(['success' => false]);
            return;
        }

        $cached = $this->_axiom_get_single_cache($lead_id, 'strat_path');
        if ($cached !== null) {
            echo json_encode($cached);
            return;
        }

        // Debug: Log comprehensive information for StratPath
        log_activity("AXIOM StratPath: Analysis Start - Lead ID: {$lead_id}, Messages: " . count($chat_history));

        $last_lead_message = '';
        foreach (array_reverse($chat_history) as $msg) {
            // Check fromMe correctly (0 means from lead, 1 means from agent)
            $fromMe = isset($msg['fromMe']) ? (int)$msg['fromMe'] : 1;
            if ($fromMe == 0) {
                $last_lead_message = trim($msg['text'] ?? '');
                break;
            }
        }

        log_activity("AXIOM StratPath: Last Lead Message: " . substr($last_lead_message, 0, 200));
        if ($lead_data) {
            log_activity("AXIOM StratPath: Lead Data - Name: " . ($lead_data['name'] ?? 'N/A') . ", Status: " . ($lead_data['status'] ?? 'N/A'));
        }

        if (empty($last_lead_message) && empty($chat_history)) {
            $payload = ['success' => true, 'strategies' => []];
            $this->_axiom_set_single_cache($lead_id, 'strat_path', $payload);
            echo json_encode($payload);
            return;
        }

        $prompt = "Você é um consultor de vendas especializado. Com base nesta conversa COMPLETA, sugira 2-3 caminhos estratégicos de resposta para o agente.\n\n";

        // CRITICAL: Put the last lead message FIRST and emphasize it strongly
        if ($last_lead_message) {
            $prompt .= "⚠️⚠️⚠️ ÚLTIMA MENSAGEM DO LEAD (LEIA COM ATENÇÃO - É A MAIS IMPORTANTE) ⚠️⚠️⚠️\n";
            $prompt .= "\"{$last_lead_message}\"\n\n";
            $prompt .= "REGRA CRÍTICA #1: TODAS as estratégias DEVEM ser ESPECÍFICAS e DIRETAMENTE RELACIONADAS a esta última mensagem. As estratégias devem ADDRESSAR DIRETAMENTE o que o lead perguntou ou disse nesta mensagem. NÃO forneça estratégias genéricas sobre introdução, apresentação ou benefícios se o lead já passou dessa fase.\n\n";

            // Detect payment questions (including variations)
            if (preg_match('/como paga|como pagar|forma de pagamento|como é o pagamento|condições de pagamento|valor|preço|quanto|parcela|parcelamento|cartão|boleto|transferência|pix|à vista|pagar hoje|pagar agora|pago hoje|pago agora/i', $last_lead_message)) {
                $prompt .= "🔥 ATENÇÃO CRÍTICA: O lead está perguntando sobre PAGAMENTO. A última mensagem foi: '{$last_lead_message}'\n";
                $prompt .= "As estratégias DEVEM:\n";
                $prompt .= "- FOCAR em responder sobre condições de pagamento, formas de pagamento, parcelamento\n";
                $prompt .= "- Se o agente já respondeu sobre pagamento, focar em próximos passos (agendar, fechar, esclarecer dúvidas sobre pagamento)\n";
                $prompt .= "- NÃO fornecer estratégias sobre apresentação, introdução ou benefícios - o lead já está na fase de pagamento\n";
                $prompt .= "- Seja ESPECÍFICO sobre as opções de pagamento disponíveis\n";
                $prompt .= "- Mencione valores, parcelas, formas de pagamento, prazos conforme relevante\n\n";
            }

            // Detect guarantee questions (especially with payment)
            if (preg_match('/garantia|garantias|qual a garantia|que garantia|tem garantia|quais garantias|que garantias você oferece|guarantee|se eu pagar.*garantia|pagar.*hoje.*garantia/i', $last_lead_message)) {
                $prompt .= "🔥🔥🔥 ATENÇÃO CRÍTICA: O lead está perguntando sobre GARANTIAS. A última mensagem foi: '{$last_lead_message}'\n";
                $prompt .= "Isso indica que o lead está na fase de FECHAMENTO/NEGOCIAÇÃO - ele está SERIAMENTE considerando pagar.\n";
                $prompt .= "As estratégias DEVEM:\n";
                $prompt .= "- ADDRESSAR DIRETAMENTE a pergunta sobre garantias - NÃO ignore esta pergunta\n";
                $prompt .= "- Listar garantias ESPECÍFICAS e CONCRETAS (suporte, treinamento, garantia de resultados, reembolso, etc.)\n";
                $prompt .= "- Se o lead mencionou 'se eu pagar hoje' ou similar, focar em garantir que ele se sinta SEGURO para proceder\n";
                $prompt .= "- Oferecer prova social (depoimentos, casos de sucesso, informações verificáveis)\n";
                $prompt .= "- Ser TRANSPARENTE sobre o que está incluído e o que está garantido\n";
                $prompt .= "- Se o agente já respondeu sobre garantias, focar em próximos passos (processo de pagamento, fechamento, agendamento)\n";
                $prompt .= "- NÃO fornecer estratégias sobre apresentação, introdução ou benefícios - o lead já está pronto para fechar\n";
                $prompt .= "- As mensagens sugeridas devem mencionar explicitamente as GARANTIAS oferecidas\n\n";
            }

            // Detect buying intent (including "pronto pra comprar")
            if (preg_match('/quero comprar|quero adquirir|vou comprar|estou interessado em comprar|to pronto pra comprar|estou pronto pra comprar|pronto pra comprar|ready to buy/i', $last_lead_message)) {
                $prompt .= "🔥 ATENÇÃO: O lead expressou DESEJO DE COMPRAR. As estratégias devem focar em:\n";
                $prompt .= "- Coletar informações necessárias para fechar o negócio\n";
                $prompt .= "- Apresentar condições de pagamento e entrega\n";
                $prompt .= "- Agendar próxima etapa (proposta, visita, etc.)\n\n";
            }

            // Detect negative signals (ending conversation, disinterest)
            if (preg_match('/vou parar|não vou mais|não quero mais|não tenho interesse|não estou interessado|vou parar de falar|não quero continuar|achei que vou parar/i', $last_lead_message)) {
                $prompt .= "🔥 ATENÇÃO CRÍTICA: O lead expressou desejo de ENCERRAR ou PARAR a conversa. As estratégias devem focar em:\n";
                $prompt .= "- Mostrar empatia e respeito pela decisão do lead\n";
                $prompt .= "- Oferecer valor adicional ou solução alternativa (sem ser insistente)\n";
                $prompt .= "- Deixar porta aberta para futuro contato, mas de forma respeitosa\n";
                $prompt .= "- NÃO ser insistente ou tentar forçar a venda\n\n";
            }

            // Detect distrust/skepticism (including variations)
            if (preg_match('/golpe|scam|fraude|mentira|não acredito|não confio|desconfio|suspeito|to achando que|parece|achei que/i', $last_lead_message)) {
                $prompt .= "🔥 ATENÇÃO CRÍTICA: O lead demonstra DESCONFIANÇA ou dúvidas sobre legitimidade. A última mensagem foi: '{$last_lead_message}'\n";
                $prompt .= "As estratégias DEVEM:\n";
                $prompt .= "- ADDRESSAR DIRETAMENTE a preocupação do lead sobre golpe/scam/fraude\n";
                $prompt .= "- Reforçar credibilidade de forma específica (não genérica)\n";
                $prompt .= "- Oferecer prova social concreta (depoimentos, casos de sucesso, informações verificáveis)\n";
                $prompt .= "- Ser transparente sobre processos, garantias e políticas\n";
                $prompt .= "- Respeitar a desconfiança do lead sem ser defensivo\n";
                $prompt .= "- As mensagens sugeridas devem mencionar explicitamente a preocupação do lead (ex: 'Entendo sua preocupação sobre...', 'Sobre sua dúvida de que isso possa ser...')\n";
                $prompt .= "NÃO forneça estratégias genéricas que ignoram a preocupação específica do lead.\n\n";
            }

            $prompt .= "⚠️ LEMBRE-SE: Se o lead fez uma pergunta ESPECÍFICA (como 'como paga?', 'quanto custa?', 'quando entrega?'), as estratégias DEVEM responder essa pergunta específica, NÃO falar sobre apresentação ou introdução que já foi feita antes.\n\n";
        }

        $prompt .= "REGRA CRÍTICA #2: As estratégias devem ser ESPECÍFICAS e DIRETAMENTE RELACIONADAS à última mensagem do lead. Cada estratégia deve ADDRESSAR diretamente o que o lead disse, não ser genérica.\n";
        $prompt .= "IMPORTANTE: Se o lead expressou uma preocupação específica ou fez uma pergunta específica, as estratégias DEVEM abordar isso diretamente. Não forneça estratégias genéricas que ignoram o que o lead disse.\n";
        $prompt .= "REGRA SOBRE NOMES DE PRODUTOS/SERVIÇOS: NÃO mencione nomes de produtos ou serviços (como 'AXIOM', 'AXIOM', etc.) nas estratégias a menos que:\n";
        $prompt .= "- O lead mencionou especificamente esse nome na ÚLTIMA mensagem, OU\n";
        $prompt .= "- É absolutamente necessário para responder à preocupação específica do lead\n";
        $prompt .= "Foque em ADDRESSAR a preocupação ou questão do lead, não em promover produtos ou serviços.\n\n";

        // Include product/sales knowledge (context for creating specific strategies)
        if (!empty($sales_knowledge)) {
            $prompt .= "=== CONHECIMENTO SOBRE PRODUTO/SERVIÇO E ABORDAGEM DE VENDAS ===\n";
            $prompt .= "Use estas informações como CONTEXTO para criar estratégias ESPECÍFICAS e CONTEXTUALIZADAS:\n\n";
            $prompt .= $sales_knowledge . "\n\n";
            $prompt .= "INSTRUÇÕES: Use este conhecimento para ENRIQUECER as estratégias, mas SEMPRE combine com a última mensagem do lead. Se o lead perguntou sobre pagamento, use este conhecimento para criar estratégias específicas sobre pagamento baseadas no produto/serviço.\n\n";
        }

        // Include lead data context (status, custom fields, dates, related contacts, etc.)
        if ($lead_data) {
            $prompt .= $this->_format_lead_context($lead_data, true);
        }

        // Log chat history being sent (first 10 and last 10 messages)
        $history_sample = array_merge(
            array_slice($chat_history, 0, 10),
            array_slice($chat_history, -10)
        );
        log_activity("AXIOM StratPath: Chat History Sample (first 10 + last 10): " . json_encode($history_sample, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $prompt .= "=== CONTEXTO DA CONVERSA (para contexto apenas - a ÚLTIMA mensagem do lead já foi destacada acima) ===\n";
        $prompt .= "IMPORTANTE: As mensagens MAIS RECENTES (últimas 10) são as MAIS RELEVANTES. Use este contexto para entender o fluxo da conversa, mas SEMPRE priorize a ÚLTIMA mensagem do lead que foi destacada no início.\n";
        $prompt .= "Se a conversa já passou da fase de apresentação/introdução, NÃO forneça estratégias sobre isso. Foque na fase atual da conversa (ex: se o lead está perguntando sobre pagamento, foque em responder sobre pagamento).\n\n";

        // Show LAST 10 messages FIRST (most recent context)
        $recent_messages = array_slice($chat_history, -10);
        $prompt .= "--- ÚLTIMAS 10 MENSAGENS (CONTEXTO ATUAL - MAIS IMPORTANTE) ---\n";
        foreach ($recent_messages as $msg) {
            $fromMe = isset($msg['fromMe']) ? (int)$msg['fromMe'] : 1;
            $sender = $fromMe ? 'Agente' : 'Lead';
            $date = !empty($msg['date']) ? date('d/m/Y H:i', strtotime($msg['date'])) : '';
            $text = trim($msg['text'] ?? '');
            if (!empty($text)) {
                $prompt .= "[{$date}] {$sender}: {$text}\n";
            }
        }

        // Show older messages as additional context (if any)
        $older_messages = array_slice($chat_history, 0, -10);
        if (!empty($older_messages)) {
            $prompt .= "\n--- MENSAGENS ANTERIORES (CONTEXTO HISTÓRICO - use apenas se relevante para o contexto atual) ---\n";
            foreach (array_slice($older_messages, -20) as $msg) {
                $fromMe = isset($msg['fromMe']) ? (int)$msg['fromMe'] : 1;
                $sender = $fromMe ? 'Agente' : 'Lead';
                $date = !empty($msg['date']) ? date('d/m/Y H:i', strtotime($msg['date'])) : '';
                $text = trim($msg['text'] ?? '');
                if (!empty($text)) {
                    $prompt .= "[{$date}] {$sender}: {$text}\n";
                }
            }
        }
        $prompt .= "\n=== INSTRUÇÕES FINAIS ===\n";
        $prompt .= "REGRA CRÍTICA: Se o contexto da conversa mudou ou se há mensagens antigas sobre assuntos diferentes (como clínica de estética, criolipólise, ou outros temas não relacionados), IGNORE essas mensagens antigas e FOQUE APENAS no contexto das mensagens MAIS RECENTES (últimas 10).\n";
        $prompt .= "As estratégias devem ser baseadas SOMENTE no contexto atual e relevante, não em mensagens antigas que não têm relação com a conversa atual.\n\n";

        $prompt .= "EXEMPLOS DE ESTRATÉGIAS CONTEXTUAIS:\n";
        $prompt .= "Se o lead disse 'To achando que isso eh um golpe':\n";
        $prompt .= "- Estratégia deve mencionar: 'Entendo sua preocupação sobre isso poder ser um golpe...' ou 'Sobre sua dúvida de que isso seja um golpe...'\n";
        $prompt .= "- Deve oferecer: informações verificáveis, depoimentos, garantias, transparência\n";
        $prompt .= "- NÃO deve ser: genérica como 'Vamos conversar sobre nossos serviços' ou 'Sobre o AXIOM...'\n";
        $prompt .= "- NÃO deve mencionar nomes de produtos a menos que o lead tenha mencionado\n\n";
        $prompt .= "Se o lead disse 'Quero comprar':\n";
        $prompt .= "- Estratégia deve focar: coletar informações, apresentar condições, agendar próxima etapa\n";
        $prompt .= "- NÃO deve ser: genérica como 'Como posso ajudar?' ou 'Sobre o AXIOM...'\n";
        $prompt .= "- Foque no processo de compra, não em promover o produto\n\n";
        $prompt .= "Se o lead disse 'Como paga?' ou perguntou sobre pagamento:\n";
        $prompt .= "- Estratégia deve focar: responder sobre formas de pagamento, condições, parcelamento, valores\n";
        $prompt .= "- Se o agente já respondeu sobre pagamento, focar em próximos passos (fechar, agendar, esclarecer dúvidas)\n";
        $prompt .= "- NÃO deve ser: genérica sobre apresentação, introdução ou benefícios - o lead já passou dessa fase\n";
        $prompt .= "- Seja ESPECÍFICO sobre opções de pagamento disponíveis\n\n";
        $prompt .= "Se o lead disse 'Se eu pagar hoje, qual a garantia?' ou perguntou sobre garantias:\n";
        $prompt .= "- Estratégia deve focar: ADDRESSAR DIRETAMENTE as garantias oferecidas (suporte, treinamento, garantia de resultados, reembolso, etc.)\n";
        $prompt .= "- Deve mencionar garantias ESPECÍFICAS e CONCRETAS, não vagas\n";
        $prompt .= "- Deve oferecer prova social (depoimentos, casos de sucesso)\n";
        $prompt .= "- Deve ser TRANSPARENTE sobre processos e políticas\n";
        $prompt .= "- Se o agente já respondeu sobre garantias, focar em próximos passos (processo de pagamento, fechamento)\n";
        $prompt .= "- NÃO deve ser: genérica sobre apresentação, introdução ou benefícios - o lead está pronto para fechar\n";
        $prompt .= "- NÃO deve ignorar a pergunta sobre garantias - ela é CRÍTICA para o fechamento\n\n";

        $prompt .= "Responda SOMENTE com array JSON válido (sem texto adicional):\n";
        $prompt .= '[{"title": "Caminho A: Proposta de Valor", "preview": "Prévia breve do que esta estratégia faz", "message": "Texto completo da mensagem sugerida em português brasileiro"}]' . "\n";
        $prompt .= "IMPORTANTE:\n";
        $prompt .= "- Todas as mensagens sugeridas devem estar em PORTUGUÊS BRASILEIRO\n";
        $prompt .= "- Baseie as estratégias APENAS no contexto das mensagens mais recentes (últimas 10)\n";
        $prompt .= "- Cada estratégia deve ADDRESSAR DIRETAMENTE o que o lead disse na última mensagem\n";
        $prompt .= "- Se o lead expressou uma preocupação específica, a mensagem sugerida deve mencionar essa preocupação explicitamente\n";
        $prompt .= "- Se o lead disse 'QUERO COMPRAR', as estratégias devem ser proativas para fechar o negócio\n";
        $prompt .= "- Se o lead disse 'GOLPE' ou 'SCAM', as estratégias devem abordar essa preocupação diretamente\n";
        $prompt .= "- NÃO mencione nomes de produtos/serviços (AXIOM, etc.) a menos que o lead tenha mencionado na última mensagem\n";
        $prompt .= "- Foque em resolver a preocupação ou questão do lead, não em promover produtos\n";
        $prompt .= "- Forneça estratégias práticas e acionáveis que respondem ao contexto real\n";
        $prompt .= "- Retorne um array JSON válido com 2-3 estratégias";

        log_activity("AXIOM StratPath: Prompt Length: " . strlen($prompt) . " chars");

        $result = $this->_call_gemini_api($gemini_api_key, $prompt);

        // Log AI response
        $strategies_count = 0;
        if (is_array($result)) {
            if (isset($result[0])) $strategies_count = count($result);
            elseif (isset($result['strategies'])) $strategies_count = count($result['strategies']);
        }
        log_activity("AXIOM StratPath: AI Response - Strategies: {$strategies_count}");

        if (isset($result['error'])) {
            log_activity("AXIOM StratPath: Error - " . substr(json_encode($result), 0, 300));

            // Generate fallback strategies based on last message
            $fallback_strategies = [];
            if (!empty($last_lead_message)) {
                $lower_message = strtolower($last_lead_message);

                // Check for negative signals first
                if (preg_match('/vou parar|não vou mais|não quero mais|não tenho interesse|não estou interessado|vou parar de falar|não quero continuar|achei que vou parar/i', $last_lead_message)) {
                    // Lead wants to end conversation
                    $fallback_strategies = [
                        [
                            'title' => 'Caminho A: Respeitar Decisão com Empatia',
                            'preview' => 'Demonstrar compreensão e respeito pela decisão, deixando porta aberta sem insistir',
                            'message' => 'Entendo completamente sua decisão e respeito seu espaço. Se mudar de ideia ou tiver alguma dúvida no futuro, estaremos sempre por aqui para ajudar. Muito obrigado pelo tempo que dedicou à nossa conversa!'
                        ],
                        [
                            'title' => 'Caminho B: Oferecer Valor Adicional (Sem Pressão)',
                            'preview' => 'Oferecer conteúdo útil ou alternativa sem forçar a venda',
                            'message' => 'Compreendo sua decisão. Se no futuro precisar de informações ou tiver alguma dúvida, fique à vontade para entrar em contato. Desejo muito sucesso em sua jornada!'
                        ]
                    ];
                } elseif (preg_match('/golpe|scam|fraude|mentira|não acredito|não confio|desconfio|suspeito|to achando que|parece|achei que/i', $last_lead_message)) {
                    // Lead shows distrust - make it contextual to what they said
                    $fallback_strategies = [
                        [
                            'title' => 'Caminho A: Abordar Preocupação Diretamente',
                            'preview' => 'Reconhecer a preocupação do lead sobre golpe/scam e oferecer transparência',
                            'message' => 'Entendo completamente sua preocupação sobre isso poder ser um golpe. É muito importante que você se sinta seguro. Posso compartilhar informações verificáveis sobre nossa empresa, depoimentos de clientes reais e nossas garantias. Gostaria que eu enviasse essas informações para você verificar?'
                        ],
                        [
                            'title' => 'Caminho B: Transparência e Prova Social',
                            'preview' => 'Ser transparente e oferecer prova social concreta para construir confiança',
                            'message' => 'Sua desconfiança é totalmente compreensível, especialmente em negócios online. Sobre sua dúvida de que isso seja um golpe, posso explicar todo o nosso processo de forma transparente, mostrar nossas garantias e fornecer informações verificáveis sobre nossa empresa para que você possa pesquisar. O que acha?'
                        ]
                    ];
                } elseif (preg_match('/quero comprar|vou comprar|quero adquirir|to pronto pra comprar|estou pronto pra comprar|pronto pra comprar|ready to buy/i', $last_lead_message)) {
                    // Buying intent
                    $fallback_strategies = [
                        [
                            'title' => 'Caminho A: Coletar Informações para Fechamento',
                            'preview' => 'Coletar dados necessários para preparar proposta e condições de pagamento',
                            'message' => 'Perfeito! Vamos avançar com sua compra. Para preparar a melhor proposta, preciso de algumas informações: você já teve experiência com empreendimentos antes ou seria sua primeira vez?'
                        ],
                        [
                            'title' => 'Caminho B: Apresentar Condições e Próximos Passos',
                            'preview' => 'Apresentar condições de pagamento e agendar próxima etapa',
                            'message' => 'Excelente! Estou muito feliz com seu interesse. Vamos conversar sobre as condições de pagamento e como podemos agendar uma visita ou reunião para finalizar os detalhes. Qual seria o melhor dia e horário para você?'
                        ]
                    ];
                } else {
                    // Generic response
                    $fallback_strategies = [
                        [
                            'title' => 'Caminho A: Entender Necessidade',
                            'preview' => 'Fazer perguntas para entender melhor a necessidade do lead',
                            'message' => 'Entendi sua mensagem. Para eu poder ajudá-lo da melhor forma, você poderia me contar um pouco mais sobre o que está buscando?'
                        ],
                        [
                            'title' => 'Caminho B: Apresentar Solução',
                            'preview' => 'Apresentar os principais benefícios e como podemos ajudar',
                            'message' => 'Obrigado pela sua mensagem! Gostaria de apresentar como podemos ajudá-lo. Nossa solução oferece [benefícios principais]. Gostaria de saber mais sobre algum aspecto específico?'
                        ]
                    ];
                }
            }

            $payload = ['success' => true, 'strategies' => $fallback_strategies];
            $this->_axiom_set_single_cache($lead_id, 'strat_path', $payload);
            echo json_encode($payload);
            return;
        }

        // Ensure result is an array
        $strategies = [];
        if (is_array($result)) {
            if (isset($result[0]) && is_array($result[0])) {
                // Direct array of strategies
                $strategies = $result;
            } elseif (isset($result['strategies']) && is_array($result['strategies'])) {
                // Wrapped in object
                $strategies = $result['strategies'];
            } elseif (isset($result['title']) || isset($result['message'])) {
                // Single strategy object
                $strategies = [$result];
            }
        }

        // Validate strategies structure
        $validStrategies = [];
        foreach ($strategies as $strategy) {
            if (is_array($strategy) && (isset($strategy['title']) || isset($strategy['message']))) {
                $validStrategies[] = [
                    'title' => $strategy['title'] ?? 'Estratégia',
                    'preview' => $strategy['preview'] ?? substr($strategy['message'] ?? '', 0, 100),
                    'message' => $strategy['message'] ?? $strategy['preview'] ?? ''
                ];
            }
        }

        // If no valid strategies, use fallback
        if (empty($validStrategies) && !empty($last_lead_message)) {
            $lower_message = strtolower($last_lead_message);
            if (preg_match('/quero comprar|vou comprar|quero adquirir/i', $last_lead_message)) {
                $validStrategies = [
                    [
                        'title' => 'Caminho A: Coletar Informações para Fechamento',
                        'preview' => 'Coletar dados necessários para preparar proposta',
                        'message' => 'Perfeito! Vamos avançar com sua compra. Para preparar a melhor proposta, preciso de algumas informações: você já teve experiência com empreendimentos antes?'
                    ]
                ];
            }
        }

        $strategies = $validStrategies;

        $final_response = [
            'success' => true,
            'strategies' => array_slice($validStrategies, 0, 3) // Max 3 strategies
        ];
        $this->_axiom_set_single_cache($lead_id, 'strat_path', $final_response);
        log_activity("AXIOM StratPath: Final Response - Valid Strategies: " . count($strategies));
        echo json_encode($final_response);
    }

    /**
     * AXIOM - Intelligence bundle: one request returns DealPulse + ClientDNA + StratPath (with 60s cache)
     * Reduces round-trips and speeds up repeat loads for the same lead
     */
    public function ajax_axiom_intelligence_bundle()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $lead_id = $this->input->post('lead_id');
        if (empty($lead_id)) {
            echo json_encode(['success' => false, 'message' => 'Lead ID required']);
            return;
        }

        $cached = $this->_axiom_get_bundle_cache($lead_id);
        if ($cached !== null) {
            echo json_encode([
                'success' => true,
                'from_cache' => true,
                'deal_pulse' => $cached['deal_pulse'] ?? null,
                'client_dna' => $cached['client_dna'] ?? null,
                'strat_path' => $cached['strat_path'] ?? null
            ]);
            return;
        }

        $urls = [
            'deal_pulse' => site_url('admin/contactcenter/ajax_axiom_deal_pulse'),
            'client_dna' => site_url('admin/contactcenter/ajax_axiom_client_dna'),
            'strat_path' => site_url('admin/contactcenter/ajax_axiom_strat_path')
        ];
        $post_data = http_build_query(['lead_id' => $lead_id]);
        $cookie_header = '';
        if (!empty($_COOKIE)) {
            $parts = [];
            foreach ($_COOKIE as $k => $v) {
                $parts[] = $k . '=' . rawurlencode($v);
            }
            $cookie_header = implode('; ', $parts);
        }

        $mh = curl_multi_init();
        $handles = [];
        foreach ($urls as $key => $url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_TIMEOUT, 45);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            if ($cookie_header !== '') {
                curl_setopt($ch, CURLOPT_COOKIE, $cookie_header);
            }
            curl_multi_add_handle($mh, $ch);
            $handles[$key] = $ch;
        }

        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh, 0.1);
        } while ($running > 0);

        $deal_pulse = null;
        $client_dna = null;
        $strat_path = null;
        foreach ($handles as $key => $ch) {
            $body = curl_multi_getcontent($ch);
            $decoded = $body ? json_decode($body, true) : null;
            if ($key === 'deal_pulse') {
                $deal_pulse = is_array($decoded) ? $decoded : null;
            } elseif ($key === 'client_dna') {
                $client_dna = is_array($decoded) ? $decoded : null;
            } elseif ($key === 'strat_path') {
                $strat_path = is_array($decoded) ? $decoded : null;
            }
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
        curl_multi_close($mh);

        $payload = [
            'deal_pulse' => $deal_pulse,
            'client_dna' => $client_dna,
            'strat_path' => $strat_path
        ];
        $this->_axiom_set_bundle_cache($lead_id, $payload);

        echo json_encode([
            'success' => true,
            'from_cache' => false,
            'deal_pulse' => $deal_pulse,
            'client_dna' => $client_dna,
            'strat_path' => $strat_path
        ]);
    }

    /**
     * AXIOM - FlowSync: Auto Actions
     */
    public function ajax_axiom_flow_sync()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $action = $this->input->post('action');
        $lead_id = $this->input->post('lead_id');
        $phonenumber = $this->input->post('phonenumber');

        if (empty($action) || empty($lead_id)) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            return;
        }

        // Execute action based on type
        switch ($action) {
            case 'calendar':
                // Add to calendar logic would go here
                echo json_encode(['success' => true, 'message' => 'Event added to calendar']);
                break;

            case 'crm_stage':
                // Update CRM stage logic would go here
                echo json_encode(['success' => true, 'message' => 'CRM stage updated']);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Unknown action']);
        }
    }

    /**
     * AXIOM - DeepQuery: RAG Chat
     */
    public function ajax_axiom_deep_query()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $query = $this->input->post('query');
        $lead_id = $this->input->post('lead_id');

        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Query required']);
            return;
        }

        $gemini_api_key = get_option('contactcenter_gemini_api_key');
        if (empty($gemini_api_key)) {
            $gemini_api_key = get_option('axiom_studio_gemini_api_key');
        }

        if (empty($gemini_api_key)) {
            echo json_encode(['success' => false, 'message' => 'Gemini API not configured']);
            return;
        }

        // Get lead data and chat history for RAG context
        $lead_data = $this->contactcenter_model->get_lead_data($lead_id);
        $chat_history = $this->contactcenter_model->get_chat_history_for_lead($lead_id, 50); // Get more messages

        $prompt = "Você é AXIOM, um coach de vendas e assistente estratégico. O agente está conversando com um lead e precisa de VOCÊ para:\n";
        $prompt .= "1. ANALISAR a conversa (não apenas resumir) – leia o tom, objeções, interesses, urgência do lead\n";
        $prompt .= "2. RESPONDER diretamente à pergunta ou dúvida que o agente fez – interaja com o que ele perguntou\n";
        $prompt .= "3. DAR DICAS PRÁTICAS – próximos passos, o que dizer, o que evitar, como conduzir o atendimento\n";
        $prompt .= "4. SUGERIR IDEIAS DE RESPOSTA – frases ou abordagens que o agente pode usar ou adaptar\n\n";
        $prompt .= "NÃO faça apenas um resumo da conversa. O agente quer ANÁLISE, INSIGHTS e conselhos para conduzir melhor o chat.\n\n";

        // Enhanced Lead Information
        if ($lead_data) {
            $prompt .= $this->_format_lead_context($lead_data, true);
        } else {
            $prompt .= "Informações do Lead: Não disponíveis\n\n";
        }

        // Full Conversation History
        $prompt .= "\n=== HISTÓRICO DA CONVERSA ===\n";
        if (!empty($chat_history)) {
            foreach ($chat_history as $msg) {
                $fromMe = isset($msg['fromMe']) ? (int)$msg['fromMe'] : 1;
                $sender = $fromMe ? 'Agente' : 'Lead';
                $date = !empty($msg['date']) ? date('d/m/Y H:i', strtotime($msg['date'])) : '';
                $text = trim($msg['text'] ?? '');
                if (!empty($text)) {
                    $prompt .= "[{$date}] {$sender}: {$text}\n";
                }
            }
        } else {
            $prompt .= "Nenhuma mensagem ainda.\n";
        }

        $prompt .= "\n=== O QUE O AGENTE PERGUNTOU ===\n";
        $prompt .= "{$query}\n\n";
        $prompt .= "=== INSTRUÇÕES OBRIGATÓRIAS ===\n";
        $prompt .= "1. NÃO comece com um resumo da conversa. Responda DIRETAMENTE ao que o agente perguntou.\n";
        $prompt .= "2. Use o histórico como CONTEXTO para dar insights e conselhos – análise, leitura do lead, sugestões de como conduzir o chat.\n";
        $prompt .= "3. Inclua DICAS PRÁTICAS: o que priorizar, o que evitar, próximos passos sugeridos.\n";
        $prompt .= "4. Quando fizer sentido, ofereça EXEMPLOS DE MENSAGENS ou frases que o agente pode enviar ao lead.\n";
        $prompt .= "5. Base-se APENAS no histórico acima – não invente mensagens. Use PORTUGUÊS BRASILEIRO.\n";
        $prompt .= "6. Formate com Markdown (**negrito**, listas, parágrafos) para organizar insights, dicas e sugestões.";

        $result = $this->_call_gemini_api($gemini_api_key, $prompt, false);

        if (isset($result['error'])) {
            echo json_encode(['success' => false, 'message' => 'Error getting answer']);
            return;
        }

        $answer = is_string($result) ? $result : ($result['text'] ?? $result['answer'] ?? 'Unable to generate answer');

        echo json_encode([
            'success' => true,
            'answer' => $answer
        ]);
    }

    /**
     * AXIOM - SmartChips: Quick Suggestions
     */
    public function ajax_axiom_smart_chips()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $lead_id = $this->input->post('lead_id');

        if (empty($lead_id)) {
            echo json_encode(['success' => false, 'chips' => []]);
            return;
        }

        $chat_history = $this->contactcenter_model->get_chat_history_for_lead($lead_id);

        // Get enriched lead data (CRM fields, custom fields, related contacts, dates, etc.)
        $lead_data = $this->contactcenter_model->get_lead_data($lead_id);

        // Get last incoming message
        $last_message = '';
        foreach (array_reverse($chat_history) as $msg) {
            if (!$msg['fromMe']) {
                $last_message = $msg['text'];
                break;
            }
        }

        if (empty($last_message)) {
            echo json_encode(['success' => true, 'chips' => []]);
            return;
        }

        $gemini_api_key = get_option('contactcenter_gemini_api_key');
        if (empty($gemini_api_key)) {
            $gemini_api_key = get_option('axiom_studio_gemini_api_key');
        }

        if (empty($gemini_api_key)) {
            echo json_encode(['success' => true, 'chips' => []]);
            return;
        }

        $prompt = "Com base nesta mensagem do lead e nas informações do CRM, sugira 2-3 ações rápidas como chips curtos.\n\n";

        // Include lead data context for better suggestions
        if ($lead_data) {
            $prompt .= $this->_format_lead_context($lead_data, false); // Don't include related contacts for chips
        }

        $prompt .= "Mensagem do Lead: {$last_message}\n\n";
        $prompt .= "Responda com array JSON:\n";
        $prompt .= '[{"label": "Agendar Visita", "message": "Mensagem de resposta completa em português"}, {"label": "Enviar PDF", "message": "Mensagem de resposta completa em português"}]' . "\n";
        $prompt .= "IMPORTANTE: Todos os labels e mensagens devem estar em PORTUGUÊS BRASILEIRO.";

        $result = $this->_call_gemini_api($gemini_api_key, $prompt);

        if (isset($result['error']) || !is_array($result)) {
            echo json_encode(['success' => true, 'chips' => []]);
            return;
        }

        $chips = is_array($result) && isset($result[0]) ? $result : (isset($result['chips']) ? $result['chips'] : []);

        echo json_encode([
            'success' => true,
            'chips' => array_slice($chips, 0, 3) // Max 3 chips
        ]);
    }

    /**
     * AXIOM - Quick Reply: AI-generated reply suggestions by context + objective
     */
    public function ajax_axiom_quick_replies()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $lead_id = $this->input->post('lead_id');
        $context = $this->input->post('context') ?: 'last_message';
        $objective = $this->input->post('objective') ?: '';

        if (empty($lead_id)) {
            echo json_encode(['success' => false, 'message' => 'Lead required']);
            return;
        }

        $chat_history = $this->contactcenter_model->get_chat_history_for_lead($lead_id, 100);
        $today_start = date('Y-m-d 00:00:00');
        $today_end = date('Y-m-d 23:59:59');

        // Filter/slice by context
        if ($context === 'today') {
            $chat_history = array_filter($chat_history, function ($msg) use ($today_start, $today_end) {
                $d = $msg['date'] ?? '';
                return $d >= $today_start && $d <= $today_end;
            });
            $chat_history = array_values($chat_history);
        } elseif ($context === 'last_message') {
            $chat_history = array_slice($chat_history, -15);
        } elseif ($context === 'last_5') {
            $chat_history = array_slice($chat_history, -5);
        } elseif ($context === 'last_10') {
            $chat_history = array_slice($chat_history, -10);
        }
        // 'full' = keep all (already limited by 100)

        if (empty($chat_history)) {
            echo json_encode(['success' => false, 'message' => _l('axiom_no_data')]);
            return;
        }

        $conversation_text = '';
        foreach ($chat_history as $msg) {
            $fromMe = isset($msg['fromMe']) ? (int) $msg['fromMe'] : 1;
            $sender = $fromMe ? 'Agente' : 'Lead';
            $date = !empty($msg['date']) ? date('d/m H:i', strtotime($msg['date'])) : '';
            $text = trim($msg['text'] ?? '');
            if ($text !== '') {
                $conversation_text .= "[{$date}] {$sender}: {$text}\n";
            }
        }

        $gemini_api_key = get_option('contactcenter_gemini_api_key');
        if (empty($gemini_api_key)) {
            $gemini_api_key = get_option('axiom_studio_gemini_api_key');
        }
        if (empty($gemini_api_key)) {
            echo json_encode(['success' => false, 'message' => 'API not configured']);
            return;
        }

        $prompt = "Você é um assistente que sugere respostas para o agente de vendas com base na conversa.\n\n";
        $prompt .= "=== CONVERSA (CONTEXTO SELECIONADO) ===\n" . $conversation_text . "\n";
        if ($objective !== '') {
            $prompt .= "=== OBJETIVO/PRODUTO DO AGENTE ===\n" . $objective . "\n\n";
        }
        $prompt .= "Gere 2 a 4 sugestões de resposta prontas para o AGENTE enviar ao lead. ";
        $prompt .= "Cada resposta deve ser direta, em português brasileiro, e adequada ao contexto.";
        if ($objective !== '') {
            $prompt .= " Considere o objetivo informado.";
        }
        $prompt .= "\n\nResponda APENAS com um array JSON de strings. Exemplo:\n";
        $prompt .= "[\"Primeira resposta completa aqui.\", \"Segunda resposta aqui.\"]";

        $result = $this->_call_gemini_api($gemini_api_key, $prompt);

        if (isset($result['error'])) {
            echo json_encode(['success' => false, 'message' => 'Error generating replies']);
            return;
        }

        $replies = [];
        if (is_array($result)) {
            $replies = $result;
        } elseif (is_string($result)) {
            $decoded = json_decode($result, true);
            $replies = is_array($decoded) ? $decoded : [];
        }

        $replies = array_slice(array_filter(array_map(function ($r) {
            return is_string($r) ? trim($r) : (isset($r['text']) ? trim($r['text']) : '');
        }, $replies)), 0, 4);

        echo json_encode([
            'success' => true,
            'replies' => $replies
        ]);
    }

    /**
     * AXIOM - Quick Reply: STREAMING - tokens appear as they're generated for faster perceived response
     */
    public function ajax_axiom_quick_replies_stream()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $lead_id = $this->input->post('lead_id');
        $context = $this->input->post('context') ?: 'last_message';
        $objective = $this->input->post('objective') ?: '';

        if (empty($lead_id)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Lead required']);
            return;
        }

        $chat_history = $this->contactcenter_model->get_chat_history_for_lead($lead_id, 100);
        $today_start = date('Y-m-d 00:00:00');
        $today_end = date('Y-m-d 23:59:59');

        if ($context === 'today') {
            $chat_history = array_filter($chat_history, function ($msg) use ($today_start, $today_end) {
                $d = $msg['date'] ?? '';
                return $d >= $today_start && $d <= $today_end;
            });
            $chat_history = array_values($chat_history);
        } elseif ($context === 'last_message') {
            $chat_history = array_slice($chat_history, -15);
        } elseif ($context === 'last_5') {
            $chat_history = array_slice($chat_history, -5);
        } elseif ($context === 'last_10') {
            $chat_history = array_slice($chat_history, -10);
        }

        if (empty($chat_history)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => _l('axiom_no_data')]);
            return;
        }

        $conversation_text = '';
        foreach ($chat_history as $msg) {
            $fromMe = isset($msg['fromMe']) ? (int) $msg['fromMe'] : 1;
            $sender = $fromMe ? 'Agente' : 'Lead';
            $date = !empty($msg['date']) ? date('d/m H:i', strtotime($msg['date'])) : '';
            $text = trim($msg['text'] ?? '');
            if ($text !== '') {
                $conversation_text .= "[{$date}] {$sender}: {$text}\n";
            }
        }

        $gemini_api_key = get_option('contactcenter_gemini_api_key');
        if (empty($gemini_api_key)) {
            $gemini_api_key = get_option('axiom_studio_gemini_api_key');
        }
        if (empty($gemini_api_key)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'API not configured']);
            return;
        }

        $prompt = "Você é um assistente que sugere respostas para o agente de vendas com base na conversa.\n\n";
        $prompt .= "=== CONVERSA (CONTEXTO SELECIONADO) ===\n" . $conversation_text . "\n";
        if ($objective !== '') {
            $prompt .= "=== OBJETIVO/PRODUTO DO AGENTE ===\n" . $objective . "\n\n";
        }
        $prompt .= "Gere 2 a 4 sugestões de resposta prontas para o AGENTE enviar ao lead. ";
        $prompt .= "Cada resposta deve ser direta, em português brasileiro, e adequada ao contexto.";
        if ($objective !== '') {
            $prompt .= " Considere o objetivo informado.";
        }
        $prompt .= "\n\nIMPORTANTE: Responda com CADA sugestão em UMA LINHA separada. Uma linha = uma resposta completa. Não use JSON, não numere. Exemplo:\n";
        $prompt .= "Primeira resposta completa aqui.\nSegunda resposta aqui.\nTerceira resposta aqui.";

        $request_data = [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1024
            ]
        ];

        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        for ($i = 0; $i < ob_get_level(); $i++) {
            ob_end_flush();
        }
        ob_implicit_flush(1);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:streamGenerateContent?alt=sse&key=' . $gemini_api_key);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) {
            $len = strlen($data);
            if ($len === 0) {
                return 0;
            }
            $lines = explode("\n", $data);
            foreach ($lines as $line) {
                $line = trim($line);
                if (strpos($line, 'data: ') === 0) {
                    $json = substr($line, 5);
                    if ($json === '[DONE]' || $json === '') {
                        continue;
                    }
                    $decoded = json_decode($json, true);
                    if ($decoded && isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
                        $text = $decoded['candidates'][0]['content']['parts'][0]['text'];
                        echo "data: " . json_encode(['delta' => $text], JSON_UNESCAPED_UNICODE) . "\n\n";
                        if (ob_get_level()) {
                            ob_flush();
                        }
                        flush();
                    }
                }
            }
            return $len;
        });

        curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err !== '') {
            echo "data: " . json_encode(['error' => $err]) . "\n\n";
        }
        echo "data: {\"done\":true}\n\n";
        if (ob_get_level()) {
            ob_flush();
        }
        flush();
        exit;
    }

    /**
     * Helper: Format lead data for AI prompts
     */
    private function _format_lead_context($lead_data, $include_related = true)
    {
        if (empty($lead_data)) {
            return "Informações do Lead: Não disponíveis\n";
        }

        $context = "=== INFORMAÇÕES DO LEAD ===\n";
        $context .= "Nome: " . ($lead_data['name'] ?? 'N/A') . "\n";
        $context .= "Telefone: " . ($lead_data['phonenumber'] ?? 'N/A') . "\n";

        if (!empty($lead_data['email'])) {
            $context .= "Email: " . $lead_data['email'] . "\n";
        }
        if (!empty($lead_data['company'])) {
            $context .= "Empresa: " . $lead_data['company'] . "\n";
        }
        if (!empty($lead_data['title'])) {
            $context .= "Cargo: " . $lead_data['title'] . "\n";
        }
        if (!empty($lead_data['status'])) {
            $context .= "Status: " . $lead_data['status'] . "\n";
        }
        if (!empty($lead_data['source_name'])) {
            $context .= "Origem: " . $lead_data['source_name'] . "\n";
        } elseif (!empty($lead_data['source'])) {
            $context .= "Origem: " . $lead_data['source'] . "\n";
        }

        // Address information
        if (!empty($lead_data['address'])) {
            $context .= "Endereço: " . $lead_data['address'];
            if (!empty($lead_data['city'])) {
                $context .= ", " . $lead_data['city'];
            }
            if (!empty($lead_data['state'])) {
                $context .= " - " . $lead_data['state'];
            }
            if (!empty($lead_data['country'])) {
                $context .= ", " . $lead_data['country'];
            }
            $context .= "\n";
        }

        // Dates
        if (!empty($lead_data['date_created_formatted'])) {
            $context .= "Criado em: " . $lead_data['date_created_formatted'] . "\n";
        }
        if (!empty($lead_data['last_contact_formatted'])) {
            $context .= "Último contato: " . $lead_data['last_contact_formatted'] . "\n";
        }

        // Description
        if (!empty($lead_data['description'])) {
            $context .= "Descrição: " . substr(strip_tags($lead_data['description']), 0, 500) . "\n";
        }

        // Custom fields
        if (!empty($lead_data['custom_fields']) && is_array($lead_data['custom_fields'])) {
            $context .= "\nCampos Personalizados:\n";
            foreach ($lead_data['custom_fields'] as $cf) {
                if (!empty($cf['name']) && !empty($cf['value'])) {
                    $context .= "- " . $cf['name'] . ": " . $cf['value'] . "\n";
                }
            }
        }

        // Related contacts (other contacts/leads with same email or company)
        if ($include_related && !empty($lead_data['related_contacts']) && is_array($lead_data['related_contacts'])) {
            $context .= "\nContatos Relacionados:\n";
            foreach ($lead_data['related_contacts'] as $related) {
                $type_label = $related['type'] == 'contact' ? 'Contato' : 'Lead';
                $context .= "- {$type_label}: " . ($related['name'] ?? 'N/A');
                if (!empty($related['email'])) {
                    $context .= " (" . $related['email'] . ")";
                }
                if (!empty($related['phone'])) {
                    $context .= " - Tel: " . $related['phone'];
                }
                $context .= "\n";
            }
        }

        $context .= "\n";

        return $context;
    }

    /**
     * AXIOM Intelligence bundle cache (60s TTL) - faster repeat loads for same lead
     */
    private function _axiom_get_bundle_cache($lead_id)
    {
        $key = 'axiom_intel_bundle_' . md5($lead_id);
        $raw = get_option($key);
        if (empty($raw)) {
            return null;
        }
        $data = json_decode($raw, true);
        if (empty($data) || !isset($data['ts']) || (time() - (int)$data['ts']) > 60) {
            return null;
        }
        return isset($data['payload']) ? $data['payload'] : null;
    }

    private function _axiom_set_bundle_cache($lead_id, $payload)
    {
        $key = 'axiom_intel_bundle_' . md5($lead_id);
        update_option($key, json_encode(['ts' => time(), 'payload' => $payload]));
    }

    /** Per-feature cache (60s TTL) for single endpoints */
    private function _axiom_get_single_cache($lead_id, $type)
    {
        $key = 'axiom_intel_' . $type . '_' . md5($lead_id);
        $raw = get_option($key);
        if (empty($raw)) {
            return null;
        }
        $data = json_decode($raw, true);
        if (empty($data) || !isset($data['ts']) || (time() - (int)$data['ts']) > 60) {
            return null;
        }
        return isset($data['payload']) ? $data['payload'] : null;
    }

    private function _axiom_set_single_cache($lead_id, $type, $payload)
    {
        $key = 'axiom_intel_' . $type . '_' . md5($lead_id);
        update_option($key, json_encode(['ts' => time(), 'payload' => $payload]));
    }

    /**
     * Helper: Call Gemini API
     */
    private function _call_gemini_api($api_key, $prompt, $expect_json = true)
    {
        try {
            $data = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 4096  // Increased to handle longer JSON responses (strategies, reasoning, etc.)
                ]
            ];

            // For non-JSON responses (like sales knowledge extraction), allow more tokens
            if (!$expect_json) {
                $data['generationConfig']['maxOutputTokens'] = 8192;  // More tokens for longer text extraction
            }

            if ($expect_json) {
                $data['generationConfig']['responseMimeType'] = 'application/json';
            }

            // Log request details to Activity Log (without full prompt to avoid bloat)
            log_activity("AXIOM Gemini API: Request - expect_json=" . ($expect_json ? 'true' : 'false') . ", prompt_length=" . strlen($prompt));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $api_key);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_TIMEOUT, 180);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            // Log response details to Activity Log
            if ($http_code !== 200) {
                log_activity("AXIOM Gemini API: HTTP Error {$http_code}" . ($curl_error ? " - " . substr($curl_error, 0, 200) : ""));
            }

            if ($http_code === 200) {
                $result = json_decode($response, true);
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $text = $result['candidates'][0]['content']['parts'][0]['text'];

                    if ($expect_json) {
                        // Remove markdown code blocks
                        $text = preg_replace('/```json\s*/i', '', $text);
                        $text = preg_replace('/```\s*/', '', $text);
                        $text = trim($text);

                        // Try multiple strategies to parse JSON
                        $parsed = null;

                        // Strategy 1: Try to parse the entire cleaned text as JSON
                        $parsed = json_decode($text, true);
                        $json_error = json_last_error();

                        if ($parsed !== null && $json_error === JSON_ERROR_NONE) {
                            // Log successful parse (only key info to avoid bloat)
                            if (isset($parsed['score'])) {
                                log_activity("AXIOM Gemini API: Parsed Successfully - Score: " . ($parsed['score'] ?? 'N/A'));
                            } elseif (isset($parsed['sentiment'])) {
                                log_activity("AXIOM Gemini API: Parsed Successfully - Sentiment: " . ($parsed['sentiment']['label'] ?? 'N/A'));
                            } elseif (isset($parsed[0]) && is_array($parsed[0])) {
                                log_activity("AXIOM Gemini API: Parsed Successfully - Array with " . count($parsed) . " items");
                            }
                            return $parsed;
                        }

                        // Strategy 2: Find first { or [ and extract complete JSON structure (handles strings properly)
                        $json_start = false;
                        $json_char = '';
                        $close_char = '';

                        if (($pos = strpos($text, '{')) !== false) {
                            $json_start = $pos;
                            $json_char = '{';
                            $close_char = '}';
                        } elseif (($pos = strpos($text, '[')) !== false) {
                            $json_start = $pos;
                            $json_char = '[';
                            $close_char = ']';
                        }

                        if ($json_start !== false) {
                            // Find matching closing bracket by counting brackets, accounting for strings
                            $bracket_count = 0;
                            $json_end = -1;
                            $in_string = false;
                            $escape_next = false;

                            for ($i = $json_start; $i < strlen($text); $i++) {
                                $char = $text[$i];

                                if ($escape_next) {
                                    $escape_next = false;
                                    continue;
                                }

                                if ($char === '\\') {
                                    $escape_next = true;
                                    continue;
                                }

                                if ($char === '"' && !$escape_next) {
                                    $in_string = !$in_string;
                                    continue;
                                }

                                if (!$in_string) {
                                    if ($char === $json_char) {
                                        $bracket_count++;
                                    } elseif ($char === $close_char) {
                                        $bracket_count--;
                                        if ($bracket_count === 0) {
                                            $json_end = $i + 1;
                                            break;
                                        }
                                    }
                                }
                            }

                            if ($json_end > $json_start) {
                                $json_part = substr($text, $json_start, $json_end - $json_start);
                                $parsed = json_decode($json_part, true);
                                if ($parsed !== null && json_last_error() === JSON_ERROR_NONE) {
                                    // Log successful parse
                                    if (isset($parsed['score'])) {
                                        log_activity("AXIOM Gemini API: Parsed Successfully (extracted) - Score: " . ($parsed['score'] ?? 'N/A'));
                                    } elseif (isset($parsed['sentiment'])) {
                                        log_activity("AXIOM Gemini API: Parsed Successfully (extracted) - Sentiment: " . ($parsed['sentiment']['label'] ?? 'N/A'));
                                    } elseif (isset($parsed[0]) && is_array($parsed[0])) {
                                        log_activity("AXIOM Gemini API: Parsed Successfully (extracted) - Array with " . count($parsed) . " items");
                                    }
                                    return $parsed;
                                }
                            } else {
                                // JSON is truncated (no matching closing bracket found)
                                log_activity("AXIOM Gemini API: JSON Truncated - No closing bracket found. Text length: " . strlen($text) . " chars. First 500: " . substr($text, $json_start, 500));
                                // Try to extract partial data from truncated JSON (for debugging)
                                $partial_json = substr($text, $json_start);
                                return ['error' => 'JSON response truncated by API', 'json_error' => 'Truncated response (no closing bracket)', 'raw_text_preview' => substr($partial_json, 0, 500)];
                            }
                        }

                        // Strategy 3: Loose JSON parsing (for when model returns text with JSON embedded but not in code blocks)
                        // Look for the first '{' and last '}'
                        $first_brace = strpos($text, '{');
                        $last_brace = strrpos($text, '}');
                        
                        if ($first_brace !== false && $last_brace !== false && $last_brace > $first_brace) {
                            $json_candidate = substr($text, $first_brace, $last_brace - $first_brace + 1);
                            $parsed = json_decode($json_candidate, true);
                            
                            if ($parsed !== null && json_last_error() === JSON_ERROR_NONE) {
                                log_activity("AXIOM Gemini API: Parsed Successfully (loose extraction)");
                                return $parsed;
                            }
                        }

                        // All parsing strategies failed
                        $error_msg = json_last_error_msg();
                        log_activity("AXIOM Gemini API: JSON Parse Error - " . $error_msg . " - Text length: " . strlen($text) . " chars - First 300: " . substr($text, 0, 300));
                        
                        // Log full response for debugging
                        log_activity("AXIOM Gemini API: Full Response Text (failed to parse): " . $text);
                        
                        // Return the raw text if parsing fails, but warn about it
                        // This allows the caller to try to salvage it or display it
                        return ['error' => 'Failed to parse JSON', 'json_error' => $error_msg, 'raw_text_preview' => substr($text, 0, 500), 'full_text' => $text];
                    }

                    return $text;
                } else {
                    log_activity("AXIOM Gemini API: Unexpected response structure");
                }
            } else {
                log_activity("AXIOM Gemini API: HTTP Error {$http_code} - " . substr($response, 0, 300));
            }

            return ['error' => 'API request failed', 'http_code' => $http_code];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * AXIOM - Fill Sales Knowledge with AI from Assistant Instructions
     */
    public function ajax_axiom_fill_sales_knowledge()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $assistant_id = $this->input->post('assistant_id');

        if (empty($assistant_id)) {
            echo json_encode(['success' => false, 'message' => 'Assistant ID required']);
            return;
        }

        // Get assistant instructions
        $assistant = $this->contactcenter_model->get_assistants_ai($assistant_id);

        if (!$assistant || empty($assistant->instructions)) {
            echo json_encode(['success' => false, 'message' => 'Assistant not found or has no instructions']);
            return;
        }

        $gemini_api_key = get_option('contactcenter_gemini_api_key');
        if (empty($gemini_api_key)) {
            $gemini_api_key = get_option('axiom_studio_gemini_api_key');
        }

        if (empty($gemini_api_key)) {
            echo json_encode(['success' => false, 'message' => 'Gemini API not configured']);
            return;
        }

        // Build concise prompt focused only on product/sales knowledge
        // Limit instructions to avoid hitting token limits (keep room for prompt and response)
        $instructions = mb_substr($assistant->instructions, 0, 8000);

        $prompt = "Das instruções abaixo, extraia SOMENTE informações sobre PRODUTO/SERVIÇO e ABORDAGEM DE VENDAS. Ignore procedimentos, formatação, regras operacionais.\n\n";
        $prompt .= "INSTRUÇÕES:\n" . $instructions . "\n\n";
        $prompt .= "EXTRAIR APENAS:\n";
        $prompt .= "- O que é o produto/serviço\n";
        $prompt .= "- Principais benefícios e vantagens\n";
        $prompt .= "- Como apresentar (pitch de vendas)\n";
        $prompt .= "- Público-alvo\n";
        $prompt .= "- Informações que ajudam a vender\n\n";
        $prompt .= "IGNORAR:\n";
        $prompt .= "- Instruções sobre como responder mensagens\n";
        $prompt .= "- Regras de formatação ou estrutura de texto\n";
        $prompt .= "- Procedimentos operacionais\n";
        $prompt .= "- Avisos ou desclaimers\n\n";
        $prompt .= "FORMATO: Retorne APENAS o conteúdo extraído, SEM prefixos como 'Aqui estão', '---', ou explicações. Comece direto com as informações.\n";
        $prompt .= "Se não encontrar informações relevantes, retorne apenas: 'Nenhuma informação relevante sobre produto ou vendas encontrada.'";

        log_activity("AXIOM Fill Sales Knowledge: Assistant ID: {$assistant_id}, Instructions length: " . strlen($assistant->instructions) . ", Truncated to: " . strlen($instructions));

        // Use API call with higher token limit for text extraction
        try {
            $data = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 8192  // Higher limit for longer text extraction
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $gemini_api_key);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Longer timeout for text extraction

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($http_code === 200) {
                $result = json_decode($response, true);
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $extracted_knowledge = $result['candidates'][0]['content']['parts'][0]['text'];
                } else {
                    log_activity("AXIOM Fill Sales Knowledge: Unexpected response structure");
                    echo json_encode(['success' => false, 'message' => 'Unexpected API response structure']);
                    return;
                }
            } else {
                log_activity("AXIOM Fill Sales Knowledge: HTTP Error {$http_code} - " . substr($response, 0, 300));
                echo json_encode(['success' => false, 'message' => 'API request failed: HTTP ' . $http_code]);
                return;
            }
        } catch (Exception $e) {
            log_activity("AXIOM Fill Sales Knowledge: Exception - " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            return;
        }

        // Clean up the response - remove prefix text, markdown, and extra formatting
        $extracted_knowledge = trim($extracted_knowledge);

        // Remove common prefix phrases
        $prefixes = [
            '/^Aqui estão as informações extraídas[^\n]*\n*/i',
            '/^Here are the extracted[^\n]*\n*/i',
            '/^Informações extraídas[^\n]*\n*/i',
            '/^Extracted information[^\n]*\n*/i',
            '/^---+\s*\n*/',
            '/^===+\s*\n*/',
        ];
        foreach ($prefixes as $pattern) {
            $extracted_knowledge = preg_replace($pattern, '', $extracted_knowledge);
        }

        // Remove markdown code blocks
        $extracted_knowledge = preg_replace('/```[\w]*\n?/', '', $extracted_knowledge);
        $extracted_knowledge = preg_replace('/```\n?/', '', $extracted_knowledge);

        // Remove leading/trailing dashes and extra whitespace
        $extracted_knowledge = preg_replace('/^[\s\-=]+\s*/', '', $extracted_knowledge);
        $extracted_knowledge = preg_replace('/\s*[\s\-=]+\s*$/', '', $extracted_knowledge);
        $extracted_knowledge = trim($extracted_knowledge);

        log_activity("AXIOM Fill Sales Knowledge: Extracted " . strlen($extracted_knowledge) . " characters");

        echo json_encode([
            'success' => true,
            'sales_knowledge' => $extracted_knowledge
        ]);
    }

    // =========================================================================
    // AUTOMATIC FOLLOW-UP
    // =========================================================================

    public function auto_followup()
    {
        if (!has_permission('contactcenter', '', 'engine')) {
            access_denied('contactcenter');
        }

        $show_inactive_raw = $this->session->userdata('auto_followup_show_inactive');
        $data['show_inactive'] = ($show_inactive_raw === null || $show_inactive_raw === false || $show_inactive_raw === '') ? true : (bool)$show_inactive_raw;

        $filters = [];
        if (!$data['show_inactive']) {
            $filters['status'] = 1;
        }

        $data['followups'] = $this->contactcenter_model->get_auto_followups($filters);
        $data['devices']   = $this->contactcenter_model->get_device_by_type(2);
        $data['tagsArray'] = $this->db->get(db_prefix() . 'tags')->result_array();
        $data['staff']     = $this->staff_model->get('', ['active' => 1]);
        $data['statuses']  = $this->leads_model->get_status();

        $this->load->view('auto_followup', $data);
    }

    public function auto_followup_queue($followup_id = null)
    {
        if (!has_permission('contactcenter', '', 'engine')) {
            access_denied('contactcenter');
        }

        $data['followup_id'] = $followup_id;
        $data['followups']   = $this->contactcenter_model->get_auto_followups();

        $filters = [];
        if ($followup_id) {
            $filters['followup_id'] = $followup_id;
        }
        $status_filter = $this->input->get('status');
        if ($status_filter) {
            $filters['status'] = $status_filter;
        }

        $data['queue_items'] = $this->contactcenter_model->get_followup_queue($filters);
        $data['stats']       = $this->contactcenter_model->get_queue_stats($followup_id);

        $this->load->view('auto_followup_queue', $data);
    }

    public function add_auto_followup()
    {
        if (!has_permission('contactcenter', '', 'engine')) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'error' => 'access_denied']);
                return;
            }
            access_denied('contactcenter');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            $id = isset($data['id']) ? $data['id'] : '';
            unset($data['id']);

            if (isset($data['tags']) && !is_array($data['tags'])) {
                $data['tags'] = '';
            }
            if (isset($data['lead_statuses']) && !is_array($data['lead_statuses'])) {
                $data['lead_statuses'] = '';
            }

            if (!empty($id)) {
                $this->contactcenter_model->update_auto_followup($id, $data);
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => true]);
                    return;
                }
                set_alert('success', _l('contac_auto_followup_updated'));
            } else {
                $new_id = $this->contactcenter_model->add_auto_followup($data);
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => true, 'id' => $new_id]);
                    return;
                }
                set_alert('success', _l('contac_auto_followup_added'));
            }
        }
        redirect(admin_url('contactcenter/auto_followup'));
    }

    public function delete_auto_followup()
    {
        if (!has_permission('contactcenter', '', 'engine')) {
            access_denied('contactcenter');
        }
        $id = $this->input->post('id');
        if ($id) {
            $this->contactcenter_model->delete_auto_followup($id);
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function toggle_auto_followup_status()
    {
        if (!has_permission('contactcenter', '', 'engine')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false]);
            return;
        }
        $id     = $this->input->post('id');
        $status = $this->input->post('status');
        $this->contactcenter_model->update_auto_followup($id, ['status' => (int)$status]);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function test_followup_generation()
    {
        header('Content-Type: application/json');

        if (!has_permission('contactcenter', '', 'engine')) {
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        $followup_id = $this->input->post('id');
        if (empty($followup_id)) {
            echo json_encode(['success' => false, 'error' => 'No follow-up ID provided']);
            return;
        }

        $fu = $this->contactcenter_model->get_auto_followup($followup_id);
        if (!$fu) {
            echo json_encode(['success' => false, 'error' => 'Follow-up rule not found']);
            return;
        }

        $gemini_key = get_option('contactcenter_gemini_api_key');
        if (empty($gemini_key)) {
            echo json_encode(['success' => false, 'error' => 'No Gemini API key configured in settings']);
            return;
        }

        $hours_eq = (float)$fu->hours_equivalent;
        $config_table = db_prefix() . 'contactcenter_auto_followup';
        $queue_table  = db_prefix() . 'contactcenter_auto_followup_queue';

        $tag_join = '';
        if (!empty($fu->tags)) {
            $tag_ids = array_map('intval', explode(',', $fu->tags));
            $tag_ids_str = implode(',', $tag_ids);
            $tag_join = " INNER JOIN " . db_prefix() . "taggables tg ON tg.rel_id = l.id AND tg.rel_type = 'lead' AND tg.tag_id IN ({$tag_ids_str})";
        }

        $status_where = '';
        if (!empty($fu->lead_statuses)) {
            $status_ids = array_map('intval', explode(',', $fu->lead_statuses));
            $status_ids_str = implode(',', $status_ids);
            $status_where = " AND l.status IN ({$status_ids_str})";
        }

        $msg_table = db_prefix() . 'contactcenter_message';

        $sql = "SELECT l.id, l.name, l.phonenumber, l.lastcontact, l.email,
                       l.lastcontact as last_activity
                FROM " . db_prefix() . "leads l
                {$tag_join}
                WHERE l.phonenumber IS NOT NULL
                  AND l.phonenumber != ''
                  AND l.lastcontact IS NOT NULL
                  {$status_where}
                  AND TIMESTAMPADD(HOUR, {$hours_eq}, l.lastcontact) <= NOW()
                  AND EXISTS (
                      SELECT 1 FROM {$msg_table} m
                      WHERE m.msg_conversation_number = l.phonenumber
                      AND m.msg_content IS NOT NULL AND m.msg_content != ''
                      LIMIT 1
                  )
                ORDER BY l.lastcontact DESC
                LIMIT 5";

        $sample_leads = $this->db->query($sql)->result();

        if (empty($sample_leads)) {
            echo json_encode(['success' => false, 'error' => 'No leads match the current filter criteria (tags/status)']);
            return;
        }

        $objective_prompts = [
            'lead_warmer'  => 'Lead Warmer - Re-engage the lead with a warm, friendly message',
            'appointment'  => 'Appointment Booking - Encourage the lead to schedule a meeting',
            'reactivation' => 'Reactivation - Bring back an inactive lead',
            'feedback'     => 'Feedback/Survey - Ask for feedback or satisfaction',
            'upsell'       => 'Upsell/Cross-sell - Present additional products or services',
            'custom'       => '',
        ];

        $obj_prompt = isset($objective_prompts[$fu->objective]) ? $objective_prompts[$fu->objective] : $fu->objective;
        if ($fu->objective === 'custom' && !empty($fu->custom_objective)) {
            $obj_prompt = $fu->custom_objective;
        }

        $obj_display = _l('contac_auto_followup_obj_' . $fu->objective);
        if ($fu->objective === 'custom' && !empty($fu->custom_objective)) {
            $obj_display = $fu->custom_objective;
        }

        $results = [];
        $generated_count = 0;

        $sender_staff    = _l('contac_auto_followup_chat_staff');
        $sender_customer = _l('contac_auto_followup_chat_customer');

        foreach ($sample_leads as $lead) {
            if ($generated_count >= 3) break;

            $messages = $this->contactcenter_model->get_lead_recent_messages($lead->id, 10);
            if (empty($messages)) continue;

            $formatted_prompt = '';
            $formatted_display = '';
            foreach ($messages as $msg) {
                $content = trim($msg['msg_content']);
                if (!empty($content)) {
                    $sender_en = $msg['msg_fromMe'] ? 'Staff' : 'Customer';
                    $sender_ui = $msg['msg_fromMe'] ? $sender_staff : $sender_customer;
                    $formatted_prompt  .= "[{$sender_en}]: {$content}\n";
                    $formatted_display .= "[{$sender_ui}]: {$content}\n";
                }
            }

            if (empty(trim($formatted_prompt))) continue;

            $prompt = "You are a sales/customer service assistant. Generate a natural, personalized follow-up WhatsApp message for this contact.\n\n"
                . "OBJECTIVE: {$obj_prompt}\n"
                . "CONTACT NAME: {$lead->name}\n\n"
                . "RECENT CONVERSATION:\n{$formatted_prompt}\n"
                . "INSTRUCTIONS:\n"
                . "- Write a single short WhatsApp message (2-4 sentences)\n"
                . "- Reference specific details from the conversation naturally\n"
                . "- Match the tone used in previous messages\n"
                . "- Include a clear call to action aligned with the objective\n"
                . "- Do NOT use brackets, placeholders, or template variables\n"
                . "- Write in the same language used in the conversation\n"
                . "- Be warm but professional\n\n"
                . "Return ONLY the message text, nothing else.";

            $result = contactcenter_call_gemini_api($gemini_key, $prompt, false);
            $message_text = is_string($result) ? trim($result) : '';

            if (empty($message_text)) continue;

            $results[] = [
                'lead_id'       => $lead->id,
                'lead_name'     => $lead->name,
                'phone'         => $lead->phonenumber,
                'last_activity' => $lead->last_activity,
                'chat_preview'  => mb_substr($formatted_display, 0, 300),
                'generated_msg' => $message_text,
            ];
            $generated_count++;
        }

        if (empty($results)) {
            echo json_encode(['success' => false, 'error' => _l('contac_auto_followup_test_no_results')]);
            return;
        }

        echo json_encode(['success' => true, 'samples' => $results, 'rule_title' => $fu->title, 'objective' => $obj_display]);
    }

    public function get_auto_followup_data()
    {
        $id = $this->input->get('id');
        $fu = $this->contactcenter_model->get_auto_followup($id);
        header('Content-Type: application/json');
        echo json_encode($fu);
    }

    public function send_followup_now()
    {
        if (!has_permission('contactcenter', '', 'engine')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $queue_id = $this->input->post('id');
        $item = $this->contactcenter_model->get_queue_item($queue_id);

        if (!$item || $item->status !== 'pending') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => _l('contac_auto_followup_queue_not_pending')]);
            return;
        }

        $fu = $this->contactcenter_model->get_auto_followup($item->followup_id);
        $staffid = $fu ? $fu->staffid : null;

        $this->contactcenter_model->update_queue_item($queue_id, ['status' => 'sending']);
        $send_result = $this->contactcenter_model->send_text($item->phone, $item->message_text, $staffid, null, null, true, 'crm');

        $success = false;
        if ($send_result && (isset($send_result['key']) || isset($send_result['id']) || isset($send_result['message']))) {
            $success = true;
        }

        if ($success) {
            $this->contactcenter_model->update_queue_item($queue_id, ['status' => 'sent', 'sent_at' => date('Y-m-d H:i:s')]);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => _l('contac_auto_followup_sent_success')]);
        } else {
            $err = $send_result ? json_encode($send_result) : 'send_text returned false';
            $this->contactcenter_model->update_queue_item($queue_id, ['status' => 'failed', 'error_message' => mb_substr($err, 0, 500)]);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => _l('contac_auto_followup_sent_failed')]);
        }
    }

    public function cancel_followup_item()
    {
        if (!has_permission('contactcenter', '', 'engine')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false]);
            return;
        }
        $queue_id = $this->input->post('id');
        $this->contactcenter_model->update_queue_item($queue_id, ['status' => 'cancelled']);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function update_followup_message()
    {
        if (!has_permission('contactcenter', '', 'engine')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false]);
            return;
        }
        $queue_id = $this->input->post('id');
        $message  = $this->input->post('message_text');
        $this->contactcenter_model->update_queue_item($queue_id, ['message_text' => $message]);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function auto_followup_filter_inactive()
    {
        $show = $this->input->post('show_inactive');
        $this->session->set_userdata('auto_followup_show_inactive', $show ? 1 : 0);
        redirect(admin_url('contactcenter/auto_followup'));
    }

    public function count_followup_leads()
    {
        header('Content-Type: application/json');
        if (!has_permission('contactcenter', '', 'engine')) {
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        $id = $this->input->post('id');
        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'No ID']);
            return;
        }

        $fu = $this->contactcenter_model->get_auto_followup($id);
        if (!$fu) {
            echo json_encode(['success' => false, 'error' => 'Rule not found']);
            return;
        }

        $count = $this->contactcenter_model->count_followup_eligible_leads($id);

        $debug = $this->contactcenter_model->debug_followup_lead_distribution($id);

        echo json_encode([
            'success'    => true,
            'total'      => $count,
            'rule_title' => $fu->title,
            'objective'  => $fu->objective,
            'debug'      => $debug,
        ]);
    }

    public function generate_followup_batch()
    {
        header('Content-Type: application/json');
        if (!has_permission('contactcenter', '', 'engine')) {
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        $id = $this->input->post('id');
        $batch_size = $this->input->post('batch_size') ?: 20;
        $batch_size = min(max((int)$batch_size, 1), 50);

        if (empty($id)) {
            echo json_encode(['success' => false, 'error' => 'No ID']);
            return;
        }

        $result = $this->contactcenter_model->generate_followup_batch($id, $batch_size);

        if (isset($result['error'])) {
            echo json_encode(['success' => false, 'error' => $result['error']]);
            return;
        }

        echo json_encode(array_merge(['success' => true], $result));
    }

    public function sync_lastcontact_count()
    {
        header('Content-Type: application/json');
        if (!has_permission('contactcenter', '', 'engine')) {
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        $total = $this->contactcenter_model->count_leads_with_phone();
        echo json_encode(['success' => true, 'total' => $total]);
    }

    public function debug_phone_formats()
    {
        header('Content-Type: application/json');
        if (!is_admin()) {
            echo json_encode(['error' => 'Admin only']);
            return;
        }

        $lead_table = db_prefix() . 'leads';
        $msg_table  = db_prefix() . 'contactcenter_message';

        $sample_leads = $this->db->query(
            "SELECT id, phonenumber, lastcontact FROM {$lead_table}
             WHERE lastcontact IS NOT NULL AND phonenumber IS NOT NULL AND phonenumber != ''
             ORDER BY lastcontact DESC LIMIT 5"
        )->result();

        $results = [];
        foreach ($sample_leads as $lead) {
            $clean = preg_replace('/\D/', '', $lead->phonenumber);

            $msg_exact = $this->db->query(
                "SELECT COUNT(*) as c FROM {$msg_table} WHERE msg_conversation_number = " . $this->db->escape($lead->phonenumber)
            )->row()->c;

            $msg_clean = $this->db->query(
                "SELECT COUNT(*) as c FROM {$msg_table} WHERE msg_conversation_number = " . $this->db->escape($clean)
            )->row()->c;

            $msg_like = $this->db->query(
                "SELECT COUNT(*) as c FROM {$msg_table} WHERE msg_conversation_number LIKE " . $this->db->escape('%' . $clean)
            )->row()->c;

            $msg_jid = $this->db->query(
                "SELECT COUNT(*) as c FROM {$msg_table} WHERE remoteJid = " . $this->db->escape($clean . '@s.whatsapp.net')
            )->row()->c;

            $sample_conv = $this->db->query(
                "SELECT DISTINCT msg_conversation_number FROM {$msg_table}
                 WHERE msg_conversation_number LIKE " . $this->db->escape('%' . $clean) . " LIMIT 3"
            )->result();

            $results[] = [
                'lead_id'        => $lead->id,
                'raw_phone'      => $lead->phonenumber,
                'clean_phone'    => $clean,
                'lastcontact'    => $lead->lastcontact,
                'match_exact'    => (int)$msg_exact,
                'match_clean'    => (int)$msg_clean,
                'match_like'     => (int)$msg_like,
                'match_jid'      => (int)$msg_jid,
                'msg_conv_numbers' => array_map(function($r) { return $r->msg_conversation_number; }, $sample_conv),
            ];
        }

        echo json_encode(['leads' => $results], JSON_PRETTY_PRINT);
    }

    public function sync_lastcontact_run()
    {
        header('Content-Type: application/json');
        if (!has_permission('contactcenter', '', 'engine')) {
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        $offset    = (int)($this->input->post('offset') ?: 0);
        $max_leads = (int)($this->input->post('max_leads') ?: 0);
        $batch_size = 200;

        $result = $this->contactcenter_model->sync_leads_lastcontact_batch($offset, $batch_size, $max_leads);
        echo json_encode(array_merge(['success' => true], $result));
    }

    // =========================================================================
    // INVOICE FOLLOW-UP
    // =========================================================================

    public function invoice_followup()
    {
        if (!has_permission('contactcenter', '', 'engine')) {
            access_denied('contactcenter');
        }

        $data['title'] = _l('contac_invoice_followup_title');
        $data['followups'] = $this->contactcenter_model->get_invoice_followups();

        $show_inactive = $this->session->userdata('inv_fu_show_inactive');
        if ($show_inactive === null) $show_inactive = true;
        $data['show_inactive'] = $show_inactive;

        if (!$show_inactive) {
            $data['followups'] = array_filter($data['followups'], function($f) { return $f->status == 1; });
        }

        $data['devices'] = $this->contactcenter_model->get_device_by_type('evolution');
        $data['staff']   = $this->staff_model->get();

        $this->load->view('invoice_followup', $data);
    }

    public function invoice_followup_queue($followup_id = null)
    {
        if (!has_permission('contactcenter', '', 'engine')) {
            access_denied('contactcenter');
        }

        $data['title']       = _l('contac_invoice_followup_queue_title');
        $data['followup_id'] = $followup_id;
        $data['followups']   = $this->contactcenter_model->get_invoice_followups();
        $data['items']       = $this->contactcenter_model->get_invoice_followup_queue_items($followup_id);
        $data['stats']       = $this->contactcenter_model->get_invoice_followup_queue_stats($followup_id);

        $this->load->view('invoice_followup_queue', $data);
    }

    public function add_invoice_followup()
    {
        if (!has_permission('contactcenter', '', 'engine')) {
            access_denied('contactcenter');
        }
        $data = $this->input->post();
        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if (!empty($data['id'])) {
            $id = $data['id'];
            unset($data['id']);
            $this->contactcenter_model->update_invoice_followup($id, $data);
            if ($is_ajax) { echo json_encode(['success' => true, 'id' => $id]); return; }
            set_alert('success', _l('contac_invoice_followup_updated'));
        } else {
            unset($data['id']);
            $id = $this->contactcenter_model->add_invoice_followup($data);
            if ($is_ajax) { echo json_encode(['success' => true, 'id' => $id]); return; }
            set_alert('success', _l('contac_invoice_followup_added'));
        }
        redirect(admin_url('contactcenter/invoice_followup'));
    }

    public function delete_invoice_followup()
    {
        if (!has_permission('contactcenter', '', 'engine')) {
            access_denied('contactcenter');
        }
        $id = $this->input->post('id');
        if ($id) {
            $this->contactcenter_model->delete_invoice_followup($id);
        }
        redirect(admin_url('contactcenter/invoice_followup'));
    }

    public function toggle_invoice_followup_status()
    {
        header('Content-Type: application/json');
        $id     = $this->input->post('id');
        $status = $this->input->post('status');
        if ($id !== null && $status !== null) {
            $this->contactcenter_model->update_invoice_followup($id, ['status' => (int)$status]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function get_invoice_followup_data()
    {
        header('Content-Type: application/json');
        $id = $this->input->post('id');
        $fu = $this->contactcenter_model->get_invoice_followup($id);
        if ($fu) {
            echo json_encode(['success' => true, 'data' => $fu]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function invoice_followup_filter_inactive()
    {
        $show = $this->input->post('show');
        $this->session->set_userdata('inv_fu_show_inactive', $show == '1');
        redirect(admin_url('contactcenter/invoice_followup'));
    }

    public function count_invoice_followup_invoices()
    {
        header('Content-Type: application/json');
        if (!has_permission('contactcenter', '', 'engine')) {
            echo json_encode(['success' => false, 'error' => 'Access denied']); return;
        }
        $id = $this->input->post('id');
        if (empty($id)) { echo json_encode(['success' => false, 'error' => 'No ID']); return; }

        $fu = $this->contactcenter_model->get_invoice_followup($id);
        if (!$fu) { echo json_encode(['success' => false, 'error' => 'Rule not found']); return; }

        $count = $this->contactcenter_model->count_invoice_followup_eligible($id);
        $debug = $this->contactcenter_model->debug_invoice_followup_distribution($id);

        echo json_encode([
            'success'    => true,
            'total'      => $count,
            'rule_title' => $fu->title,
            'objective'  => $fu->objective,
            'debug'      => $debug,
        ]);
    }

    public function generate_invoice_followup_batch()
    {
        header('Content-Type: application/json');
        if (!has_permission('contactcenter', '', 'engine')) {
            echo json_encode(['success' => false, 'error' => 'Access denied']); return;
        }
        $id = $this->input->post('id');
        $batch_size = min(max((int)($this->input->post('batch_size') ?: 20), 1), 50);
        if (empty($id)) { echo json_encode(['success' => false, 'error' => 'No ID']); return; }

        $result = $this->contactcenter_model->generate_invoice_followup_batch($id, $batch_size);
        if (isset($result['error'])) {
            echo json_encode(['success' => false, 'error' => $result['error']]); return;
        }
        echo json_encode(array_merge(['success' => true], $result));
    }

    public function test_invoice_followup_generation()
    {
        header('Content-Type: application/json');
        if (!has_permission('contactcenter', '', 'engine')) {
            echo json_encode(['success' => false, 'error' => 'Access denied']); return;
        }

        $followup_id = $this->input->post('id');
        if (empty($followup_id)) { echo json_encode(['success' => false, 'error' => 'No ID']); return; }

        $fu = $this->contactcenter_model->get_invoice_followup($followup_id);
        if (!$fu) { echo json_encode(['success' => false, 'error' => 'Rule not found']); return; }

        $gemini_key = get_option('contactcenter_gemini_api_key');
        if (empty($gemini_key)) { echo json_encode(['success' => false, 'error' => 'No Gemini API key']); return; }

        $w = $this->contactcenter_model->_build_invoice_followup_where($fu);

        $sql = "SELECT i.id as invoice_id, i.number as invoice_number, i.total as invoice_total,
                       i.duedate, i.status as inv_status, i.clientid,
                       c.company as client_name, c.phonenumber
                FROM {$w['inv_table']} i
                INNER JOIN {$w['cli_table']} c ON c.userid = i.clientid
                WHERE c.phonenumber IS NOT NULL AND c.phonenumber != ''
                  {$w['inv_status_where']}
                  AND TIMESTAMPADD(HOUR, {$w['hours_eq']}, i.duedate) <= NOW()
                  AND EXISTS (SELECT 1 FROM {$w['msg_table']} m WHERE m.msg_conversation_number = c.phonenumber AND m.msg_content IS NOT NULL AND m.msg_content != '' LIMIT 1)
                ORDER BY i.duedate DESC LIMIT 3";

        $sample_invoices = $this->db->query($sql)->result();
        if (empty($sample_invoices)) {
            echo json_encode(['success' => false, 'error' => _l('contac_invoice_followup_test_no_results')]); return;
        }

        $objective_labels = [
            'payment_reminder'  => 'Payment Reminder', 'overdue_collection' => 'Overdue Collection',
            'partial_payment'   => 'Partial Payment',  'friendly_reminder'  => 'Friendly Reminder',
            'final_notice'      => 'Final Notice',     'custom' => '',
        ];
        $obj_label = isset($objective_labels[$fu->objective]) ? $objective_labels[$fu->objective] : $fu->objective;
        if ($fu->objective === 'custom' && !empty($fu->custom_objective)) $obj_label = $fu->custom_objective;

        $results = ['success' => true, 'rule_title' => $fu->title, 'objective' => $obj_label, 'samples' => []];

        foreach ($sample_invoices as $inv) {
            $messages = $this->contactcenter_model->get_client_recent_messages($inv->clientid, 10);
            $formatted = '';
            foreach ($messages as $msg) {
                $sender = $msg['msg_fromMe'] ? _l('contac_auto_followup_chat_staff') : _l('contac_auto_followup_chat_customer');
                $content = trim($msg['msg_content']);
                if (!empty($content)) $formatted .= "[{$sender}]: {$content}\n";
            }

            if (empty(trim($formatted))) continue;

            $days_overdue = max(0, (int)floor((time() - strtotime($inv->duedate)) / 86400));
            $prompt = "You are a professional accounts receivable assistant. Generate a natural WhatsApp message for invoice collection.\n\n"
                . "OBJECTIVE: {$obj_label}\nCLIENT: {$inv->client_name}\nINVOICE #: {$inv->invoice_number}\n"
                . "AMOUNT: R$ " . number_format((float)$inv->invoice_total, 2, ',', '.') . "\n"
                . "DUE DATE: " . date('d/m/Y', strtotime($inv->duedate)) . "\nDAYS OVERDUE: {$days_overdue}\n\n"
                . "CONVERSATION:\n{$formatted}\n"
                . "Write a single short WhatsApp message (2-4 sentences). Be professional. Reference the invoice. Write in the same language. Return ONLY the message.";

            $result = contactcenter_call_gemini_api($gemini_key, $prompt, false);
            $msg_text = is_string($result) ? trim($result) : '';
            if (empty($msg_text)) continue;

            $results['samples'][] = [
                'client_name'    => $inv->client_name,
                'phone'          => $inv->phonenumber,
                'invoice_number' => $inv->invoice_number,
                'invoice_total'  => $inv->invoice_total,
                'duedate'        => $inv->duedate,
                'days_overdue'   => $days_overdue,
                'chat_preview'   => mb_substr($formatted, 0, 400),
                'generated_msg'  => $msg_text,
            ];
        }

        if (empty($results['samples'])) {
            echo json_encode(['success' => false, 'error' => _l('contac_invoice_followup_test_no_results')]); return;
        }
        echo json_encode($results);
    }

    public function send_invoice_followup_now()
    {
        header('Content-Type: application/json');
        if (!has_permission('contactcenter', '', 'engine')) {
            echo json_encode(['success' => false, 'error' => 'Access denied']); return;
        }
        $id = $this->input->post('id');
        $queue_table = db_prefix() . 'contactcenter_invoice_followup_queue';
        $item = $this->db->where('id', $id)->where('status', 'pending')->get($queue_table)->row();
        if (!$item) {
            echo json_encode(['success' => false, 'error' => _l('contac_auto_followup_queue_not_pending')]); return;
        }

        $fu = $this->contactcenter_model->get_invoice_followup($item->followup_id);
        $staffid = $fu ? ($fu->staffid ?: null) : null;

        $this->contactcenter_model->update_invoice_queue_item($id, ['status' => 'sending']);
        $send_result = $this->contactcenter_model->send_text($item->phone, $item->message_text, $staffid, null, null, true, 'crm');

        $success = $send_result && (isset($send_result['key']) || isset($send_result['id']) || isset($send_result['message']));
        if ($success) {
            $this->contactcenter_model->update_invoice_queue_item($id, ['status' => 'sent', 'sent_at' => date('Y-m-d H:i:s')]);
            echo json_encode(['success' => true]);
        } else {
            $err = $send_result ? json_encode($send_result) : 'send_text failed';
            $this->contactcenter_model->update_invoice_queue_item($id, ['status' => 'failed', 'error_message' => mb_substr($err, 0, 500)]);
            echo json_encode(['success' => false, 'error' => _l('contac_auto_followup_sent_failed')]);
        }
    }

    public function cancel_invoice_followup_item()
    {
        header('Content-Type: application/json');
        $id = $this->input->post('id');
        $this->contactcenter_model->update_invoice_queue_item($id, ['status' => 'cancelled']);
        echo json_encode(['success' => true]);
    }

    public function update_invoice_followup_message()
    {
        header('Content-Type: application/json');
        $id  = $this->input->post('id');
        $msg = $this->input->post('message');
        if ($id && $msg !== null) {
            $this->contactcenter_model->update_invoice_queue_item($id, ['message_text' => $msg]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
}
