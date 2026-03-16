<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ============================================================
 * AULA DE PHP — CONTROLLER NO PERFEX (lê junto com o código!)
 * ============================================================
 *
 * CONCEITO: O que é um Controller?
 * É o "gerente" da sua rota. Quando o usuário acessa uma URL,
 * o Perfex chama a função correspondente neste arquivo.
 *
 *   URL: /admin/axiomchannel/inbox
 *   → chama: class Axiomchannel → public function inbox()
 *
 * REGRA 1: O nome do arquivo = nome da classe (com maiúscula)
 *   arquivo: Axiomchannel.php  →  class Axiomchannel
 *
 * REGRA 2: "extends AdminController" = herda login, permissões, etc.
 *   Sem isso, qualquer pessoa sem login consegue acessar.
 *
 * REGRA 3: Tudo dentro da classe fica entre { } do class.
 *   BUG ANTERIOR: uma função estava FORA do } final — PHP trava.
 */
class Axiomchannel extends AdminController
{
    /**
     * __construct = "inicializador"
     * Roda automaticamente ANTES de qualquer função.
     * Aqui carregamos o que vamos usar em todo o controller.
     */
    public function __construct()
    {
        parent::__construct(); // Chama o __construct do AdminController (login, sessão, etc)

        // Carrega o Model — nosso "garçom do banco de dados"
        // BUG CORRIGIDO: o Perfex busca models dentro do módulo automaticamente
        $this->load->model('axiomchannel_model');

        // Carrega o helper com nossas funções utilitárias (axch_format_time, etc)
        // SINTAXE CORRETA para módulo: 'nome_modulo/nome_helper'
        // BUG ANTERIOR: estava tentando carregar sem o prefixo do módulo
        $this->load->helper('axiomchannel/axiomchannel');

        // Carrega as traduções PT-BR
        // SINTAXE CORRETA para módulo: 'nome_modulo/nome_arquivo_lang'
        // BUG ANTERIOR: sintaxe errada — causava erro fatal no construtor
        $this->lang->load('axiomchannel', 'portuguese_br');
    }

    // ============================================================
    // FUNÇÃO PRIVADA: _get_evolution()
    //
    // "private" = só pode ser chamada DENTRO desta classe.
    // Usamos private para funções de "bastidor" que não são rotas.
    //
    // BUG ANTERIOR: estava tentando carregar a library pelo sistema
    // do CodeIgniter ($this->load->library) mas nossa classe não é
    // uma library CI padrão — é uma classe PHP comum.
    // CORREÇÃO: require_once + instanciar manualmente com "new".
    // ============================================================
    private function _get_evolution($device)
    {
        require_once(module_dir_path(AXIOMCHANNEL_MODULE, 'libraries/AxiomChannel_Evolution.php'));
        return new AxiomChannel_Evolution($device->server_url, $device->instance_name, $device->api_key);
    }

    // ============================================================
    // INDEX
    // ============================================================
    public function index()
    {
        redirect(admin_url('axiomchannel/inbox'));
    }

    // ============================================================
    // INBOX
    // ============================================================
    public function inbox()
    {
        // $data[] = array associativo que passamos para a view
        // Na view, cada chave vira uma variável: $data['title'] → $title
        $data['title']    = 'Todas as Conversas';
        $data['devices']  = $this->axiomchannel_model->get_devices();
        $data['contacts'] = $this->axiomchannel_model->get_contacts(['limit' => 30]);
        $data['unread']   = $this->axiomchannel_model->count_unread_contacts();

        // load->view() — SINTAXE CORRETA para módulo:
        // O Perfex busca em modules/axiomchannel/views/chat/inbox.php
        // BUG ANTERIOR: caminho estava como 'axiomchannel/views/chat/inbox'
        // O Perfex já sabe que é dentro do módulo — não repete 'views/'
        $this->load->view('axiomchannel/chat/inbox', $data);
    }

    // ============================================================
    // CHAT INDIVIDUAL
    // ============================================================
    public function chat($contact_id)
    {
        // (int) = "cast" — força o valor a ser número inteiro
        // Evita injeção de SQL se alguém colocar texto na URL
        $contact_id = (int) $contact_id;

        $contact = $this->axiomchannel_model->get_contact($contact_id);

        // show_404() = função do Perfex que mostra página de erro
        if (!$contact) {
            show_404();
        }

        $this->axiomchannel_model->mark_contact_read($contact_id);

        $data['title']    = $contact->name ?: $contact->phone_number;
        $data['contact']  = $contact;
        $data['device']   = $this->axiomchannel_model->get_device($contact->device_id);
        $data['messages'] = $this->axiomchannel_model->get_messages($contact_id);
        $data['staff']    = $this->db->get('tblstaff')->result();

        // Pipeline CRM — busca lead existente ou pipeline padrão
        $pipelines = $this->axiomchannel_model->get_pipelines();
        $crm_lead  = null;
        $pipeline  = null;
        $stages    = [];

        if (!empty($pipelines)) {
            $crm_lead = $this->axiomchannel_model->get_lead_by_contact_any($contact_id);
            if ($crm_lead) {
                $pipeline = $this->axiomchannel_model->get_pipeline($crm_lead->pipeline_id);
                $stages   = $this->axiomchannel_model->get_stages($crm_lead->pipeline_id);
            } else {
                $pipeline = $pipelines[0];
                $stages   = $this->axiomchannel_model->get_stages($pipeline->id);
            }
        }

        $data['pipelines'] = $pipelines;
        $data['crm_lead']  = $crm_lead;
        $data['pipeline']  = $pipeline;
        $data['stages']    = $stages;

        $this->load->view('axiomchannel/chat/chat_single', $data);
    }

    // ============================================================
    // DEVICES — página de gestão de dispositivos
    // ============================================================
    public function devices()
    {
        $data['title']   = 'AxiomChannel — Dispositivos';
        $data['devices'] = $this->axiomchannel_model->get_devices();
        $this->load->view('axiomchannel/devices/index', $data);
    }

    // ============================================================
    // ADD DEVICE — chamado via AJAX do modal
    //
    // CONCEITO: is_ajax_request()
    // Garante que esta rota só responde a chamadas JavaScript (fetch/XHR).
    // Se alguém digitar a URL no navegador, retorna 404.
    // ============================================================
    public function add_device()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // $this->input->post('campo') = pega o valor enviado pelo formulário
        // É mais seguro que $_POST['campo'] porque o Perfex sanitiza
        $insert = [
            'name'           => $this->input->post('name'),
            'instance_name'  => $this->input->post('instance_name'),
            'server_url'     => $this->input->post('server_url') ?: 'http://localhost:8080',
            'api_key'        => $this->input->post('api_key'),
            'assigned_staff' => get_staff_user_id(),
        ];

        $device_id = $this->axiomchannel_model->add_device($insert);

        if ($device_id) {
            // Tenta configurar webhook — usa try/catch porque pode falhar
            // se a Evolution API ainda não estiver rodando
            // CONCEITO: try/catch — tenta executar, se der erro não trava tudo
            try {
                $device  = $this->axiomchannel_model->get_device($device_id);
                $evo     = $this->_get_evolution($device);
                $webhook = site_url('axiomchannel/webhook/' . $device->instance_name);
                $evo->set_webhook($webhook);
            } catch (Exception $e) {
                log_activity('AxiomChannel webhook config error: ' . $e->getMessage());
            }

            // json_encode = converte array PHP para JSON (formato que o JS lê)
            echo json_encode(['success' => true, 'device_id' => $device_id, 'message' => 'Dispositivo adicionado!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar dispositivo']);
        }
    }

    public function delete_device($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $result = $this->axiomchannel_model->delete_device((int) $id);
        echo json_encode(['success' => $result]);
    }

    // ============================================================
    // QR CODE
    // ============================================================
    public function qrcode($device_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $device = $this->axiomchannel_model->get_device((int) $device_id);
        if (!$device) {
            echo json_encode(['success' => false, 'message' => 'Dispositivo não encontrado']);
            return; // "return" sai da função imediatamente — não executa o resto
        }

        $evo    = $this->_get_evolution($device);
        $result = $evo->get_qrcode();

        if ($result['success'] && !empty($result['data']['base64'])) {
            echo json_encode(['success' => true, 'qrcode' => $result['data']['base64']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao obter QR Code. Evolution API está rodando?']);
        }
    }

    public function device_status($device_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $device = $this->axiomchannel_model->get_device((int) $device_id);
        if (!$device) {
            echo json_encode(['success' => false, 'status' => 'disconnected']);
            return;
        }

        $evo    = $this->_get_evolution($device);
        $result = $evo->get_status();

        $status = 'disconnected';
        if ($result['success'] && isset($result['data']['instance']['state'])) {
            $status = $result['data']['instance']['state'] === 'open' ? 'connected' : 'disconnected';
        }

        $this->axiomchannel_model->update_device_status($device->id, $status);
        echo json_encode(['success' => true, 'status' => $status]);
    }

    // ============================================================
    // ENVIAR MENSAGEM
    // ============================================================
    public function send_message()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $contact_id = (int) $this->input->post('contact_id');
        $message    = trim($this->input->post('message')); // trim() remove espaços extras

        if (!$contact_id || $message === '') {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            return;
        }

        $contact = $this->axiomchannel_model->get_contact($contact_id);
        if (!$contact) {
            echo json_encode(['success' => false, 'message' => 'Contato não encontrado']);
            return;
        }

        $device = $this->axiomchannel_model->get_device($contact->device_id);
        if (!$device) {
            echo json_encode(['success' => false, 'message' => 'Dispositivo não encontrado']);
            return;
        }

        $evo    = $this->_get_evolution($device);
        $result = $evo->send_text($contact->phone_number, $message);

        if ($result['success']) {
            $msg_id = $this->axiomchannel_model->save_message([
                'contact_id'    => $contact_id,
                'device_id'     => $contact->device_id,
                'external_id'   => $result['data']['key']['id'] ?? null,
                'direction'     => 'outbound',
                'type'          => 'text',
                'content'       => $message,
                'sent_by_staff' => get_staff_user_id(),
                'status'        => 'sent',
            ]);
            echo json_encode(['success' => true, 'message_id' => $msg_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Falha ao enviar — verifique a conexão WhatsApp']);
        }
    }

    // ============================================================
    // BUSCAR MENSAGENS (polling a cada 5s)
    // ============================================================
    public function get_messages()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $contact_id = (int) $this->input->post('contact_id');
        $since_id   = (int) $this->input->post('since_id');

        $messages = $this->axiomchannel_model->get_messages_since($contact_id, $since_id);
        echo json_encode(['success' => true, 'messages' => $messages]);
    }

    // ============================================================
    // BUSCAR CONTATOS (sidebar AJAX)
    // ============================================================
    public function get_contacts()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $contacts = $this->axiomchannel_model->get_contacts([
            'device_id' => $this->input->post('device_id'),
            'status'    => $this->input->post('status'),
            'search'    => $this->input->post('search'),
            'limit'     => 30,
            'offset'    => (int) $this->input->post('offset'),
        ]);

        echo json_encode(['success' => true, 'contacts' => $contacts]);
    }

    // ============================================================
    // ATUALIZAR STATUS DO CONTATO
    // ============================================================
    public function update_contact_status()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $contact_id = (int) $this->input->post('contact_id');
        $status     = $this->input->post('status');
        $allowed    = ['open', 'pending', 'resolved', 'bot'];

        // in_array() = verifica se $status existe dentro do array $allowed
        if (!in_array($status, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Status inválido']);
            return;
        }

        $result = $this->axiomchannel_model->update_contact($contact_id, ['status' => $status]);
        echo json_encode(['success' => $result]);
    }

    // ============================================================
    // TRANSFERIR ATENDIMENTO
    // ============================================================
    public function transfer()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $contact_id = (int) $this->input->post('contact_id');
        $to_staff   = (int) $this->input->post('staff_id');
        $note       = $this->input->post('note');

        $result = $this->axiomchannel_model->transfer_contact($contact_id, $to_staff, $note);
        echo json_encode(['success' => $result, 'message' => 'Atendimento transferido']);
    }

    // ============================================================
    // WEBHOOK — recebe eventos da Evolution API
    //
    // CONCEITO IMPORTANTE: Esta rota é chamada pelo servidor Evolution,
    // não pelo navegador. Por isso não tem sessão de admin.
    // Não colocamos is_ajax_request() aqui.
    // ============================================================
    public function webhook($instance_name)
    {
        // file_get_contents('php://input') = lê o corpo raw do POST
        // A Evolution manda JSON puro no body, não form data
        $raw     = file_get_contents('php://input');
        $payload = json_decode($raw, true);

        if (!$payload || !isset($payload['event'])) {
            http_response_code(200);
            exit('ok');
        }

        $event = $payload['event'];

        if ($event === 'messages.upsert') {
            $this->_process_incoming_message($instance_name, $payload['data'] ?? []);
        } elseif ($event === 'connection.update') {
            $this->_process_connection_update($instance_name, $payload['data'] ?? []);
        }

        http_response_code(200);
        echo 'ok';
        exit;
    }

    private function _process_incoming_message($instance_name, $data)
    {
        $device = $this->axiomchannel_model->get_device_by_instance($instance_name);
        if (!$device) return;

        if (!empty($data['key']['fromMe'])) return;

        $phone   = preg_replace('/@.*/', '', $data['key']['remoteJid'] ?? '');
        $name    = $data['pushName'] ?? null;
        $content = $data['message']['conversation']
                ?? $data['message']['extendedTextMessage']['text']
                ?? null;

        $type = 'text';
        if (isset($data['message']['imageMessage']))        $type = 'image';
        elseif (isset($data['message']['audioMessage']))    $type = 'audio';
        elseif (isset($data['message']['documentMessage'])) $type = 'document';
        elseif (isset($data['message']['videoMessage']))    $type = 'video';

        $contact = $this->axiomchannel_model->get_or_create_contact($device->id, $phone, $name);

        $this->axiomchannel_model->save_message([
            'contact_id'  => $contact->id,
            'device_id'   => $device->id,
            'external_id' => $data['key']['id'] ?? null,
            'direction'   => 'inbound',
            'type'        => $type,
            'content'     => $content,
            'status'      => 'received',
            'created_at'  => date('Y-m-d H:i:s', (int)($data['messageTimestamp'] ?? time())),
        ]);

        // ============================================================
        // LÓGICA DO ASSISTENTE IA
        // ============================================================

        // Guard 1: IA habilitada no dispositivo
        if (empty($device->ai_enabled)) return;

        // Guard 2: contato resolvido ou em atendimento humano (pending = aguardando staff)
        if (in_array($contact->status, ['resolved', 'pending'])) return;

        // Guard 3: só processa texto
        if ($type !== 'text' || empty($content)) return;

        // Guard 4: assistente configurado e ativo
        $assistant = $this->axiomchannel_model->get_assistant($device->id);
        if (!$assistant || !$assistant->is_active) return;

        // Guard 5: horário de funcionamento
        $now   = date('H:i');
        $start = substr($assistant->working_hours_start ?? '00:00', 0, 5);
        $end   = substr($assistant->working_hours_end   ?? '23:59', 0, 5);
        if ($end === '00:00') $end = '23:59';
        if ($now < $start || $now > $end) return;

        $evo = $this->_get_evolution($device);

        // ---- Verifica palavras-chave de transferência ----
        if (!empty($assistant->transfer_keywords)) {
            $keywords    = array_map('trim', preg_split('/[\n,]+/', $assistant->transfer_keywords));
            $lower_msg   = mb_strtolower($content);
            $transfer_triggered = false;
            foreach ($keywords as $kw) {
                if ($kw !== '' && mb_strpos($lower_msg, mb_strtolower($kw)) !== false) {
                    $transfer_triggered = true;
                    break;
                }
            }
            if ($transfer_triggered) {
                $transfer_msg = 'Entendido! Vou transferir você para um de nossos atendentes. Aguarde um momento. 🙏';
                $evo->send_text($phone, $transfer_msg);
                $this->axiomchannel_model->save_message([
                    'contact_id' => $contact->id,
                    'device_id'  => $device->id,
                    'direction'  => 'outbound',
                    'type'       => 'text',
                    'content'    => $transfer_msg,
                    'sent_by_ai' => 1,
                    'status'     => 'sent',
                ]);
                // Marca contato como pendente (aguardando staff)
                $this->axiomchannel_model->update_contact($contact->id, ['status' => 'pending']);
                return;
            }
        }

        // ---- Saudação inicial (primeira mensagem do bot) ----
        $is_first_interaction = empty($contact->ast_stage_id);
        if ($is_first_interaction && !empty($assistant->greeting_message)) {
            $evo->send_text($phone, $assistant->greeting_message);
            $this->axiomchannel_model->save_message([
                'contact_id' => $contact->id,
                'device_id'  => $device->id,
                'direction'  => 'outbound',
                'type'       => 'text',
                'content'    => $assistant->greeting_message,
                'sent_by_ai' => 1,
                'status'     => 'sent',
            ]);
        }

        // ---- Etapa atual do fluxo de qualificação ----
        $ast_stages    = $this->axiomchannel_model->get_assistant_stages($assistant->id);
        $current_stage = null;

        if (!empty($contact->ast_stage_id)) {
            foreach ($ast_stages as $s) {
                if ($s->id == $contact->ast_stage_id) {
                    $current_stage = $s;
                    break;
                }
            }
        } elseif (!empty($ast_stages)) {
            $current_stage = $ast_stages[0];
            $this->axiomchannel_model->update_contact($contact->id, ['ast_stage_id' => $current_stage->id]);
        }

        // Ação de transferência forçada por etapa
        if ($current_stage && $current_stage->action === 'transfer') {
            $msg = $current_stage->question ?: 'Vou transferir você para nossa equipe. Aguarde! 🙏';
            $evo->send_text($phone, $msg);
            $this->axiomchannel_model->save_message([
                'contact_id' => $contact->id,
                'device_id'  => $device->id,
                'direction'  => 'outbound',
                'type'       => 'text',
                'content'    => $msg,
                'sent_by_ai' => 1,
                'status'     => 'sent',
            ]);
            $this->axiomchannel_model->update_contact($contact->id, ['status' => 'pending']);
            return;
        }

        // ---- Monta histórico de conversa (últimas 10 mensagens) ----
        $raw_history = $this->axiomchannel_model->get_messages($contact->id, 10);
        $history = [];
        foreach ($raw_history as $msg) {
            if (empty($msg->content)) continue;
            $history[] = [
                'direction' => $msg->direction,
                'content'   => $msg->content,
            ];
        }

        // ---- Monta system prompt ----
        $business_name = $assistant->business_name ?: get_option('companyname');
        $business_type = $assistant->business_type ?: '';
        $tone          = $assistant->tone_of_voice  ?: 'profissional e amigável';

        $system = "Você é o assistente virtual de {$business_name}";
        if ($business_type) $system .= " ({$business_type})";
        $system .= ".\nSeu tom de voz é: {$tone}.\n";
        $system .= "Responda SEMPRE em português do Brasil. Seja conciso. Não invente informações.\n\n";

        // Etapas do fluxo como contexto
        if (!empty($ast_stages)) {
            $system .= "=== FLUXO DE QUALIFICAÇÃO ===\n";
            foreach ($ast_stages as $idx => $s) {
                $active = ($current_stage && $s->id == $current_stage->id) ? ' ← ETAPA ATUAL' : '';
                $system .= ($idx + 1) . ". [{$s->stage_name}]{$active}: {$s->question}\n";
            }
            $system .= "\n";
        }

        // Base de conhecimento
        $knowledge = $this->axiomchannel_model->get_knowledge_base($assistant->id);
        if (!empty($knowledge)) {
            $system .= "=== BASE DE CONHECIMENTO ===\n";
            foreach ($knowledge as $item) {
                if (!$item->is_active) continue;
                $system .= "• [{$item->title}]: {$item->content}\n";
            }
            $system .= "\n";
        }

        if ($current_stage) {
            $system .= "Neste momento você está na etapa '{$current_stage->stage_name}'. ";
            if ($current_stage->action === 'ask' && $current_stage->question) {
                $system .= "Sua tarefa é: {$current_stage->question}\n";
            }
        }

        // ---- Chama Gemini ----
        $gemini_key = get_option('axch_gemini_key');
        if (!$gemini_key) {
            log_message('error', '[AxiomChannel AI] Chave Gemini não configurada');
            return;
        }

        require_once(module_dir_path(AXIOMCHANNEL_MODULE, 'libraries/AxiomChannel_Gemini.php'));
        $gemini = new AxiomChannel_Gemini($gemini_key);
        $result = $gemini->generate_response($system, $history, $content);

        if (!$result['success'] || empty($result['text'])) {
            log_message('error', '[AxiomChannel AI] Gemini falhou: ' . ($result['error'] ?? 'sem resposta'));
            return;
        }

        $ai_reply = trim($result['text']);

        // ---- Envia mídia da etapa (se configurada: before_message) ----
        if ($current_stage && $current_stage->media_id && $current_stage->media_send_position === 'before_message') {
            $this->_send_stage_media($evo, $phone, $current_stage->media_id);
        }

        // ---- Envia resposta de texto ----
        $evo->send_text($phone, $ai_reply);
        $this->axiomchannel_model->save_message([
            'contact_id' => $contact->id,
            'device_id'  => $device->id,
            'direction'  => 'outbound',
            'type'       => 'text',
            'content'    => $ai_reply,
            'sent_by_ai' => 1,
            'status'     => 'sent',
        ]);

        // ---- Envia mídia da etapa (with_message ou after_message) ----
        if ($current_stage && $current_stage->media_id && $current_stage->media_send_position !== 'before_message') {
            $this->_send_stage_media($evo, $phone, $current_stage->media_id);
        }

        // ---- Avança para próxima etapa (se action = ask ou qualify) ----
        if ($current_stage && in_array($current_stage->action, ['ask', 'qualify'])) {
            $advanced = false;
            foreach ($ast_stages as $idx => $s) {
                if ($s->id == $current_stage->id && isset($ast_stages[$idx + 1])) {
                    $next = $ast_stages[$idx + 1];
                    $this->axiomchannel_model->update_contact($contact->id, ['ast_stage_id' => $next->id]);
                    // Move lead no pipeline se etapa tem pipeline_stage_id
                    if ($next->pipeline_stage_id) {
                        $lead = $this->axiomchannel_model->get_lead_by_contact_any($contact->id);
                        if ($lead) {
                            $this->axiomchannel_model->move_lead_stage($lead->id, $next->pipeline_stage_id, 'ai');
                        }
                    }
                    $advanced = true;
                    break;
                }
            }
            // Sem próxima etapa: encerra fluxo
            if (!$advanced) {
                $this->axiomchannel_model->update_contact($contact->id, [
                    'ast_stage_id' => null,
                    'status'       => 'resolved',
                ]);
            }
        }
    }

    private function _send_stage_media($evo, $phone, $media_id)
    {
        $media = $this->db->get_where(db_prefix() . 'axch_knowledge_media', ['id' => $media_id])->row();
        if (!$media || !file_exists(FCPATH . $media->file_path)) return;

        $base_url   = base_url($media->file_path);
        $file_type  = $media->file_type;
        $label      = $media->media_label ?: $media->original_name;

        if ($file_type === 'image') {
            $evo->send_image($phone, $base_url, $label);
        } elseif ($file_type === 'audio') {
            $evo->send_audio($phone, $base_url);
        } elseif (in_array($file_type, ['pdf', 'document'])) {
            $evo->send_document($phone, $base_url, $media->original_name);
        } elseif ($file_type === 'video') {
            $evo->send_video($phone, $base_url, $label);
        }
    }

    private function _process_connection_update($instance_name, $data)
    {
        $device = $this->axiomchannel_model->get_device_by_instance($instance_name);
        if (!$device) return;

        $status = ($data['state'] ?? '') === 'open' ? 'connected' : 'disconnected';
        $this->axiomchannel_model->update_device_status($device->id, $status);
    }

    // ============================================================
    // CRON — processa fila de envio agendado
    // ============================================================
    public function axiomchannel_process_queue()
    {
        $queue = $this->axiomchannel_model->get_pending_queue(20);
        foreach ($queue as $item) {
            $device = $this->axiomchannel_model->get_device($item->device_id);
            if (!$device) continue;

            $evo    = $this->_get_evolution($device);
            $result = $evo->send_text($item->phone_number, $item->message);
            $status = $result['success'] ? 'sent' : 'failed';

            $this->db->update(db_prefix() . 'axch_queue', [
                'status'   => $status,
                'sent_at'  => date('Y-m-d H:i:s'),
                'attempts' => $item->attempts + 1,
            ], ['id' => $item->id]);
        }
    }


    // ============================================================
    // PIPELINE — páginas
    // ============================================================

    public function pipeline($pipeline_id = null)
    {
        $pipelines = $this->axiomchannel_model->get_pipelines();

        if (!$pipeline_id && !empty($pipelines)) {
            $pipeline_id = $pipelines[0]->id;
        }

        if (!$pipeline_id) {
            redirect(admin_url('axiomchannel/pipeline_wizard'));
        }

        $pipeline = $this->axiomchannel_model->get_pipeline($pipeline_id);
        if (!$pipeline) show_404();

        $data['title']     = 'CRM — ' . $pipeline->name;
        $data['pipeline']  = $pipeline;
        $data['pipelines'] = $pipelines;
        $data['stages']    = $this->axiomchannel_model->get_stages($pipeline_id);
        $data['staff']     = $this->db->get('tblstaff')->result();

        // Carrega leads por estágio
        $data['leads_by_stage'] = [];
        foreach ($data['stages'] as $stage) {
            $data['leads_by_stage'][$stage->id] = $this->axiomchannel_model->get_crm_leads($pipeline_id, $stage->id);
        }

        $this->load->view('axiomchannel/pipeline/kanban', $data);
    }

    public function pipeline_wizard()
    {
        $data['title']   = 'Criar Pipeline com IA';
        $data['devices'] = $this->axiomchannel_model->get_devices();
        $this->load->view('axiomchannel/pipeline/wizard', $data);
    }

    // ============================================================
    // PIPELINE — AJAX
    // ============================================================

    public function pipeline_generate()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $business_type  = $this->input->post('business_type');
        $channels       = $this->input->post('channels');
        $challenges     = $this->input->post('challenges');
        $cycle          = $this->input->post('cycle');
        $pipeline_name  = $this->input->post('pipeline_name');

        require_once(module_dir_path(AXIOMCHANNEL_MODULE, 'libraries/AxiomChannel_Gemini.php'));

        $gemini_key = get_option('axch_gemini_key');
        if (!$gemini_key) {
            echo json_encode(['success' => false, 'message' => 'Chave do Gemini não configurada']);
            return;
        }

        $gemini = new AxiomChannel_Gemini($gemini_key);

        $prompt = "Você é um especialista em CRM e vendas. 
Crie um pipeline de vendas para um negócio com estas características:
- Tipo de negócio: {$business_type}
- Canais de captação: {$channels}
- Principais desafios: {$challenges}
- Ciclo de venda: {$cycle}

Retorne APENAS um JSON válido (sem markdown, sem explicações) com esta estrutura exata:
{
  \"pipeline_name\": \"nome sugerido para o pipeline\",
  \"stages\": [
    {
      \"name\": \"nome do estágio\",
      \"color\": \"#hexcolor\",
      \"ai_action\": \"instrução para a IA neste estágio (o que ela deve fazer/responder)\",
      \"ai_keywords\": [\"palavra1\", \"palavra2\"],
      \"auto_move\": true
    }
  ]
}

Crie entre 5 e 7 estágios. Use cores diferentes e progressivas do cinza ao verde.
As palavras-chave devem ser termos que o cliente usaria para avançar para o próximo estágio.";

        $result = $gemini->generate_response('Você é um especialista em CRM.', [], $prompt);

        if (!$result['success']) {
            echo json_encode(['success' => false, 'message' => 'Erro ao conectar com o Gemini']);
            return;
        }

        log_activity('AXIOM Gemini raw: ' . $result['text']);

        // Limpa a resposta e faz parse do JSON
        $raw = $result['text'];

        // Remove markdown code blocks
        $raw = preg_replace('/```json\s*/i', '', $raw);
        $raw = preg_replace('/```\s*/i', '', $raw);
        $raw = trim($raw);

        // Tenta encontrar o JSON dentro da resposta
        if (preg_match('/\{.*\}/s', $raw, $matches)) {
            $raw = $matches[0];
        }

        $data = json_decode($raw, true);

        // Log para debug
        log_activity('AXIOM Pipeline raw: ' . substr($raw, 0, 500));
        log_activity('AXIOM Pipeline json_error: ' . json_last_error_msg());

        if (!$data || !isset($data['stages'])) {
            echo json_encode(['success' => false, 'message' => 'Erro ao processar resposta da IA', 'raw' => $raw]);
            return;
        }

        echo json_encode(['success' => true, 'pipeline' => $data]);
    }

    public function pipeline_save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $name      = $this->input->post('name');
        $device_id = $this->input->post('device_id');
        $stages    = json_decode($this->input->post('stages'), true);

        if (!$name || !$stages) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            return;
        }

        $pipeline_id = $this->axiomchannel_model->create_pipeline([
            'name'       => $name,
            'device_id'  => $device_id ?: null,
            'is_default' => 1,
        ]);

        $this->axiomchannel_model->save_wizard_pipeline($pipeline_id, $stages);

        echo json_encode(['success' => true, 'pipeline_id' => $pipeline_id]);
    }

    public function lead_move()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $lead_id  = (int) $this->input->post('lead_id');
        $stage_id = (int) $this->input->post('stage_id');
        $note     = $this->input->post('note');

        $result = $this->axiomchannel_model->move_lead_stage($lead_id, $stage_id, 'human', get_staff_user_id(), $note);
        echo json_encode(['success' => $result]);
    }

    public function lead_reorder()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $leads = json_decode($this->input->post('leads'), true);
        $result = $this->axiomchannel_model->reorder_leads($leads);
        echo json_encode(['success' => $result]);
    }

    public function lead_create()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $contact_id  = (int) $this->input->post('contact_id');
        $pipeline_id = (int) $this->input->post('pipeline_id');
        $stage_id    = (int) $this->input->post('stage_id');
        $name        = $this->input->post('name');
        $phone       = $this->input->post('phone');
        $value       = (float) $this->input->post('value');

        $lead_id = $this->axiomchannel_model->create_crm_lead([
            'contact_id'     => $contact_id,
            'pipeline_id'    => $pipeline_id,
            'stage_id'       => $stage_id,
            'name'           => $name,
            'phone'          => $phone,
            'value'          => $value,
            'assigned_staff' => get_staff_user_id(),
        ]);

        echo json_encode(['success' => (bool)$lead_id, 'lead_id' => $lead_id]);
    }

    public function lead_update()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id   = (int) $this->input->post('id');
        $data = [
            'name'  => $this->input->post('name'),
            'notes' => $this->input->post('notes'),
            'value' => (float) $this->input->post('value'),
        ];

        $result = $this->axiomchannel_model->update_crm_lead($id, $data);
        echo json_encode(['success' => $result]);
    }

    // ============================================================
    // ASSISTENTE DE IA — página de configuração
    // ============================================================
    public function assistant($device_id = null)
    {
        $pipelines = $this->axiomchannel_model->get_pipelines();
        $devices   = $this->axiomchannel_model->get_devices();

        // Se não veio device_id, usa o primeiro dispositivo disponível
        if (!$device_id && !empty($devices)) {
            $device_id = $devices[0]->id;
        }

        $assistant = null;
        $knowledge  = [];
        $ast_stages = [];
        $pipeline_stages = [];

        if ($device_id) {
            $assistant  = $this->axiomchannel_model->get_assistant($device_id);
            if ($assistant) {
                $knowledge  = $this->axiomchannel_model->get_knowledge_base($assistant->id);
                $ast_stages = $this->axiomchannel_model->get_assistant_stages($assistant->id);
            }
            // Estágios de todos pipelines para vincular às etapas
            foreach ($pipelines as $p) {
                foreach ($this->axiomchannel_model->get_stages($p->id) as $s) {
                    $pipeline_stages[] = $s;
                }
            }
        }

        $data['title']           = 'Assistente IA';
        $data['devices']         = $devices;
        $data['device_id']       = (int) $device_id;
        $data['assistant']       = $assistant;
        $data['knowledge']       = $knowledge;
        $data['ast_stages']      = $ast_stages;
        $data['pipeline_stages'] = $pipeline_stages;

        $this->load->view('axiomchannel/assistant/index', $data);
    }

    public function assistant_save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $device_id = (int) $this->input->post('device_id');
        if (!$device_id) {
            echo json_encode(['success' => false, 'message' => 'Dispositivo inválido']);
            return;
        }

        $appt_days = $this->input->post('appointment_days') ?: '1,2,3,4,5';
        $tmpl_id   = (int) $this->input->post('default_contract_template_id');

        $data = [
            'device_id'                    => $device_id,
            'name'                         => $this->input->post('name') ?: 'Assistente IA',
            'business_name'                => $this->input->post('business_name'),
            'business_type'                => $this->input->post('business_type'),
            'tone_of_voice'                => $this->input->post('tone_of_voice') ?: 'profissional',
            'emoji_enabled'                => (int) $this->input->post('emoji_enabled'),
            'greeting_message'             => $this->input->post('greeting_message'),
            'transfer_keywords'            => $this->input->post('transfer_keywords'),
            'working_hours_start'          => $this->input->post('working_hours_start') ?: '08:00',
            'working_hours_end'            => $this->input->post('working_hours_end') ?: '18:00',
            'is_active'                    => (int) $this->input->post('is_active'),
            'assistant_types'              => $this->input->post('assistant_types') ?: '[]',
            'appointment_duration'         => (int) ($this->input->post('appointment_duration') ?: 60),
            'appointment_start'            => $this->input->post('appointment_start') ?: '08:00',
            'appointment_end'              => $this->input->post('appointment_end') ?: '18:00',
            'appointment_days'             => $appt_days,
            'appointment_interval'         => (int) ($this->input->post('appointment_interval') ?: 30),
            'default_contract_template_id' => $tmpl_id ?: null,
        ];

        $assistant_id = $this->axiomchannel_model->save_assistant($data);
        echo json_encode(['success' => (bool) $assistant_id, 'assistant_id' => $assistant_id]);
    }

    public function knowledge_save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $data = [
            'assistant_id' => (int) $this->input->post('assistant_id'),
            'category'     => $this->input->post('category') ?: 'info',
            'title'        => $this->input->post('title'),
            'content'      => $this->input->post('content'),
            'is_active'    => 1,
            'position'     => (int) $this->input->post('position'),
        ];

        $id_post = (int) $this->input->post('id');
        if ($id_post) {
            $data['id'] = $id_post;
        }

        if (!$data['assistant_id'] || !$data['title'] || !$data['content']) {
            echo json_encode(['success' => false, 'message' => 'Preencha título e conteúdo']);
            return;
        }

        $id = $this->axiomchannel_model->save_knowledge_item($data);
        echo json_encode(['success' => (bool) $id, 'id' => $id]);
    }

    public function knowledge_delete()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id     = (int) $this->input->post('id');
        $result = $this->axiomchannel_model->delete_knowledge_item($id);
        echo json_encode(['success' => $result]);
    }

    public function knowledge_reorder()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $items  = json_decode($this->input->post('items'), true);
        $result = $this->axiomchannel_model->reorder_knowledge($items ?: []);
        echo json_encode(['success' => $result]);
    }

    public function stage_save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $media_id = $this->input->post('media_id');
        $data = [
            'assistant_id'       => (int) $this->input->post('assistant_id'),
            'pipeline_stage_id'  => (int) $this->input->post('pipeline_stage_id') ?: null,
            'stage_name'         => $this->input->post('stage_name'),
            'question'           => $this->input->post('question'),
            'action'             => $this->input->post('action') ?: 'ask',
            'save_field'         => $this->input->post('save_field'),
            'position'           => (int) $this->input->post('position'),
            'media_id'           => $media_id !== '' && $media_id !== null ? (int) $media_id : null,
            'media_send_position' => $this->input->post('media_send_position') ?: 'with_message',
        ];

        $id_post = (int) $this->input->post('id');
        if ($id_post) {
            $data['id'] = $id_post;
        }

        if (!$data['assistant_id'] || !$data['stage_name']) {
            echo json_encode(['success' => false, 'message' => 'Preencha o nome da etapa']);
            return;
        }

        $id = $this->axiomchannel_model->save_assistant_stage($data);
        echo json_encode(['success' => (bool) $id, 'id' => $id]);
    }

    public function stage_delete()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id     = (int) $this->input->post('id');
        $result = $this->axiomchannel_model->delete_assistant_stage($id);
        echo json_encode(['success' => $result]);
    }

    // ============================================================
    // UPDATE STAGE — atualiza nome e cor de um estágio
    // ============================================================
    public function update_stage()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $stage_id = (int) $this->input->post('stage_id');
        $name     = trim($this->input->post('name'));
        $color    = $this->input->post('color');

        if (!$stage_id || !$name) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            return;
        }

        $data = ['name' => $name];
        if ($color) {
            $data['color'] = $color;
        }

        $result = $this->axiomchannel_model->update_stage($stage_id, $data);
        echo json_encode(['success' => $result]);
    }

    // ============================================================
    // LEAD MOVE FROM CHAT — move ou cria lead pelo chat de atendimento
    // ============================================================
    public function lead_move_from_chat()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $contact_id  = (int) $this->input->post('contact_id');
        $pipeline_id = (int) $this->input->post('pipeline_id');
        $stage_id    = (int) $this->input->post('stage_id');

        if (!$contact_id || !$pipeline_id) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            return;
        }

        // Se stage_id não foi informado, usa o primeiro estágio do pipeline
        if (!$stage_id) {
            $stages = $this->axiomchannel_model->get_stages($pipeline_id);
            if (empty($stages)) {
                echo json_encode(['success' => false, 'message' => 'Pipeline sem estágios']);
                return;
            }
            $stage_id = $stages[0]->id;
        }

        $lead = $this->axiomchannel_model->get_lead_by_contact($contact_id, $pipeline_id);

        if (!$lead) {
            $contact = $this->axiomchannel_model->get_contact($contact_id);
            $lead_id = $this->axiomchannel_model->create_crm_lead([
                'contact_id'     => $contact_id,
                'pipeline_id'    => $pipeline_id,
                'stage_id'       => $stage_id,
                'name'           => $contact->name ?: $contact->phone_number,
                'phone'          => $contact->phone_number,
                'assigned_staff' => get_staff_user_id(),
            ]);
        } else {
            $this->axiomchannel_model->move_lead_stage($lead->id, $stage_id, 'human', get_staff_user_id());
            $lead_id = $lead->id;
        }

        echo json_encode(['success' => true, 'stage_id' => $stage_id, 'lead_id' => $lead_id]);
    }

    // ============================================================
    // KNOWLEDGE MEDIA
    // ============================================================

    public function media_upload()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $assistant_id = (int) $this->input->post('assistant_id');
        $media_label  = $this->input->post('media_label') ?: null;

        if (!$assistant_id) {
            echo json_encode(['success' => false, 'message' => 'assistant_id obrigatório']);
            return;
        }

        $upload_path = FCPATH . 'uploads/axiomchannel/media/';

        $this->load->library('upload', [
            'upload_path'   => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|gif|webp|mp4|mov|mp3|ogg|wav|pdf|doc|docx|xls|xlsx',
            'max_size'      => 20480, // 20 MB
            'encrypt_name'  => true,
        ]);

        if (!$this->upload->do_upload('file')) {
            echo json_encode(['success' => false, 'message' => $this->upload->display_errors('', '')]);
            return;
        }

        $up   = $this->upload->data();
        $mime = $up['file_type'];

        if (strpos($mime, 'image/') === 0) {
            $file_type = 'image';
        } elseif (strpos($mime, 'video/') === 0) {
            $file_type = 'video';
        } elseif (strpos($mime, 'audio/') === 0) {
            $file_type = 'audio';
        } elseif ($mime === 'application/pdf') {
            $file_type = 'pdf';
        } else {
            $file_type = 'document';
        }

        $id = $this->axiomchannel_model->save_knowledge_media([
            'assistant_id'  => $assistant_id,
            'file_type'     => $file_type,
            'original_name' => $up['orig_name'],
            'file_path'     => 'uploads/axiomchannel/media/' . $up['file_name'],
            'file_size'     => $up['file_size'],
            'mime_type'     => $mime,
            'media_label'   => $media_label,
        ]);

        echo json_encode([
            'success'   => true,
            'id'        => $id,
            'file_type' => $file_type,
            'file_path' => base_url('uploads/axiomchannel/media/' . $up['file_name']),
            'name'      => $up['orig_name'],
        ]);
    }

    public function media_delete()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id  = (int) $this->input->post('id');
        $row = $this->axiomchannel_model->delete_knowledge_media($id);

        if ($row && !empty($row->file_path)) {
            $full = FCPATH . $row->file_path;
            if (file_exists($full)) {
                @unlink($full);
            }
        }

        echo json_encode(['success' => (bool) $row]);
    }

    public function media_list($assistant_id)
    {
        if (!$this->input->is_ajax_request()) show_404();

        $media = $this->axiomchannel_model->get_media_by_assistant((int) $assistant_id);

        foreach ($media as &$m) {
            $m->url = base_url($m->file_path);
        }

        echo json_encode(['success' => true, 'data' => $media]);
    }

    // ============================================================
    // APPOINTMENTS
    // ============================================================

    public function appointments()
    {
        $device_id = $this->input->get('device_id');
        $date      = $this->input->get('date') ?: date('Y-m-d');
        $week_start = date('Y-m-d', strtotime('monday this week', strtotime($date)));

        $devices      = $this->axiomchannel_model->get_devices();
        $device_id    = $device_id ?: ($devices[0]->id ?? null);
        $appointments = $this->axiomchannel_model->get_appointments(null, $device_id);
        $google_cal   = $device_id ? $this->axiomchannel_model->get_google_calendar($device_id) : null;

        $data = [
            'title'        => 'Agendamentos',
            'devices'      => $devices,
            'device_id'    => $device_id,
            'appointments' => $appointments,
            'google_cal'   => $google_cal,
            'current_date' => $date,
            'week_start'   => $week_start,
        ];

        $this->load->view('axiomchannel/appointments/index', $data);
    }

    public function appointment_save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id   = (int) $this->input->post('id');
        $data = [
            'contact_id'     => (int) $this->input->post('contact_id'),
            'device_id'      => (int) $this->input->post('device_id'),
            'assistant_id'   => (int) $this->input->post('assistant_id') ?: null,
            'title'          => $this->input->post('title'),
            'description'    => $this->input->post('description'),
            'start_datetime' => $this->input->post('start_datetime'),
            'end_datetime'   => $this->input->post('end_datetime'),
            'status'         => $this->input->post('status') ?: 'pending',
            'notes'          => $this->input->post('notes'),
            'created_by'     => 'human',
        ];

        if ($id) {
            $this->axiomchannel_model->update_appointment($id, $data);
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            $new_id = $this->axiomchannel_model->create_appointment($data);
            echo json_encode(['success' => true, 'id' => $new_id]);
        }
    }

    public function appointment_cancel()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id = (int) $this->input->post('id');
        $this->axiomchannel_model->update_appointment($id, ['status' => 'cancelled']);
        echo json_encode(['success' => true]);
    }

    public function appointment_slots()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $device_id = (int) $this->input->post('device_id');
        $date      = $this->input->post('date');

        $slots = $this->axiomchannel_model->get_available_slots($device_id, $date);
        echo json_encode(['success' => true, 'slots' => $slots]);
    }

    public function google_calendar_connect()
    {
        $device_id = (int) $this->input->get('device_id');
        $devices   = $this->axiomchannel_model->get_devices();
        if (!$device_id && !empty($devices)) {
            $device_id = $devices[0]->id;
        }

        require_once(APPPATH . '../modules/axiomchannel/vendor/autoload.php');

        $client = new Google\Client();
        $client->setClientId(get_option('axch_google_client_id'));
        $client->setClientSecret(get_option('axch_google_client_secret'));
        $client->setRedirectUri(get_option('axch_google_redirect_uri'));
        $client->addScope(Google\Service\Calendar::CALENDAR);
        $client->addScope(Google\Service\Oauth2::USERINFO_EMAIL);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $state = base64_encode(json_encode(['device_id' => $device_id]));
        $client->setState($state);

        redirect($client->createAuthUrl());
    }

    public function google_calendar_callback()
    {
        $code  = $this->input->get('code');
        $state = json_decode(base64_decode((string) $this->input->get('state')), true);

        if (!$code || empty($state['device_id'])) {
            redirect(admin_url('axiomchannel/appointments?error=oauth_failed'));
            return;
        }

        require_once(APPPATH . '../modules/axiomchannel/vendor/autoload.php');

        $client = new Google\Client();
        $client->setClientId(get_option('axch_google_client_id'));
        $client->setClientSecret(get_option('axch_google_client_secret'));
        $client->setRedirectUri(get_option('axch_google_redirect_uri'));

        try {
            $token = $client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                redirect(admin_url('axiomchannel/appointments?error=token_' . urlencode($token['error'])));
                return;
            }

            $client->setAccessToken($token);

            // Get user email
            $oauth2   = new Google\Service\Oauth2($client);
            $userInfo = $oauth2->userinfo->get();
            $email    = $userInfo->email;

            // Find primary calendar ID
            $calService = new Google\Service\Calendar($client);
            $calList    = $calService->calendarList->listCalendarList();
            $primaryCal = 'primary';
            foreach ($calList->getItems() as $cal) {
                if ($cal->getPrimary()) {
                    $primaryCal = $cal->getId();
                    break;
                }
            }

            $expires = isset($token['expires_in'])
                ? date('Y-m-d H:i:s', time() + (int) $token['expires_in'])
                : null;

            $this->axiomchannel_model->save_google_calendar([
                'device_id'      => (int) $state['device_id'],
                'google_account' => $email,
                'calendar_id'    => $primaryCal,
                'access_token'   => json_encode($token),
                'refresh_token'  => $token['refresh_token'] ?? null,
                'token_expires'  => $expires,
                'is_active'      => 1,
            ]);

            redirect(admin_url('axiomchannel/appointments?device_id=' . (int) $state['device_id'] . '&google=connected'));

        } catch (Exception $e) {
            log_message('error', '[AxiomChannel] Google OAuth error: ' . $e->getMessage());
            redirect(admin_url('axiomchannel/appointments?error=oauth_exception'));
        }
    }

    public function google_calendar_save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $data = [
            'device_id'      => (int) $this->input->post('device_id'),
            'google_account' => $this->input->post('google_account'),
            'calendar_id'    => $this->input->post('calendar_id') ?: 'primary',
            'is_active'      => (int) $this->input->post('is_active'),
        ];

        $this->axiomchannel_model->save_google_calendar($data);
        echo json_encode(['success' => true]);
    }

    // ============================================================
    // AUTOMATIONS
    // ============================================================

    public function automations()
    {
        $devices    = $this->axiomchannel_model->get_devices();
        $device_id  = $this->input->get('device_id') ?: ($devices[0]->id ?? null);

        $automations = $device_id
            ? $this->db->get_where(db_prefix() . 'axch_automations', ['device_id' => $device_id])->result()
            : [];

        $data['title']       = 'Automações';
        $data['devices']     = $devices;
        $data['device_id']   = $device_id;
        $data['automations'] = $automations;
        $this->load->view('axiomchannel/automations/index', $data);
    }

    public function automation_save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id        = (int) $this->input->post('id');
        $device_id = (int) $this->input->post('device_id');
        $data = [
            'device_id'    => $device_id,
            'type'         => $this->input->post('type'),
            'template'     => $this->input->post('template'),
            'trigger_days' => (int) $this->input->post('trigger_days') ?: 1,
            'max_attempts' => (int) $this->input->post('max_attempts') ?: 3,
            'is_active'    => (int) $this->input->post('is_active'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        if ($id) {
            $this->db->update(db_prefix() . 'axch_automations', $data, ['id' => $id]);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert(db_prefix() . 'axch_automations', $data);
            $id = $this->db->insert_id();
        }

        echo json_encode(['success' => true, 'id' => $id]);
    }

    public function automation_delete()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id = (int) $this->input->post('id');
        $this->db->delete(db_prefix() . 'axch_automations', ['id' => $id]);
        echo json_encode(['success' => true]);
    }

    public function automation_toggle()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $id        = (int) $this->input->post('id');
        $is_active = (int) $this->input->post('is_active');
        $this->db->update(db_prefix() . 'axch_automations', ['is_active' => $is_active], ['id' => $id]);
        echo json_encode(['success' => true]);
    }

    // ============================================================
    // CONTRACTS
    // ============================================================

    public function contracts()
    {
        $device_id  = $this->input->get('device_id');
        $status     = $this->input->get('status');
        $devices    = $this->axiomchannel_model->get_devices();
        $device_id  = $device_id ?: ($devices[0]->id ?? null);
        $contracts  = $this->axiomchannel_model->get_contracts(null, $device_id);

        if ($status) {
            $contracts = array_filter($contracts, fn($c) => $c->status === $status);
        }

        $data = [
            'title'     => 'Contratos',
            'devices'   => $devices,
            'device_id' => $device_id,
            'contracts' => array_values($contracts),
            'status'    => $status,
        ];

        $this->load->view('axiomchannel/contracts/index', $data);
    }

    public function contract_new()
    {
        $device_id = $this->input->get('device_id');
        $devices   = $this->axiomchannel_model->get_devices();
        $device_id = $device_id ?: ($devices[0]->id ?? null);
        $templates = $this->axiomchannel_model->get_contract_templates($device_id);
        $contacts  = $this->axiomchannel_model->get_contacts(['device_id' => $device_id]);

        $data = [
            'title'     => 'Novo Contrato',
            'devices'   => $devices,
            'device_id' => $device_id,
            'templates' => $templates,
            'contacts'  => $contacts,
            'contract'  => null,
        ];

        $this->load->view('axiomchannel/contracts/editor', $data);
    }

    public function contract_save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id   = (int) $this->input->post('id');
        $data = [
            'contact_id' => (int) $this->input->post('contact_id'),
            'device_id'  => (int) $this->input->post('device_id'),
            'title'      => $this->input->post('title'),
            'content'    => $this->input->post('content'),
            'status'     => 'draft',
        ];

        if ($id) {
            $this->axiomchannel_model->update_contract($id, $data);
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            $new_id = $this->axiomchannel_model->create_contract($data);
            echo json_encode(['success' => true, 'id' => $new_id]);
        }
    }

    public function contract_send()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $id       = (int) $this->input->post('id');
        $contract = $this->axiomchannel_model->get_contract($id);

        if (!$contract) {
            echo json_encode(['success' => false, 'message' => 'Contrato não encontrado']);
            return;
        }

        $sign_url = base_url('axiomchannel/contract_sign/' . $contract->sign_token);
        $contact  = $this->axiomchannel_model->get_contact($contract->contact_id);
        $device   = $this->axiomchannel_model->get_device($contract->device_id);

        if (!$contact || !$device) {
            echo json_encode(['success' => false, 'message' => 'Contato ou dispositivo não encontrado']);
            return;
        }

        $message = "Olá {$contact->name}! 👋\n\nSeu contrato *{$contract->title}* está pronto para assinatura.\n\nAcesse o link abaixo para visualizar e assinar:\n{$sign_url}\n\n_Este link é único e intransferível._";

        // Envia via Evolution API
        $evo    = $this->_get_evolution($device);
        $result = $evo->send_text($contact->phone_number, $message);

        $this->axiomchannel_model->update_contract($id, [
            'status'  => 'sent',
            'sent_at' => date('Y-m-d H:i:s'),
        ]);

        echo json_encode(['success' => true, 'sign_url' => $sign_url]);
    }

    public function contract_sign($token)
    {
        $contract = $this->axiomchannel_model->get_contract_by_token($token);

        if (!$contract) show_404();

        if ($contract->status === 'signed') {
            $data = ['contract' => $contract, 'already_signed' => true];
        } else {
            // Marca como visualizado
            if ($contract->status === 'sent') {
                $this->axiomchannel_model->update_contract($contract->id, [
                    'status'    => 'viewed',
                    'viewed_at' => date('Y-m-d H:i:s'),
                ]);
            }
            $data = ['contract' => $contract, 'already_signed' => false];
        }

        $this->load->view('axiomchannel/contracts/sign', $data);
    }

    public function contract_sign_submit()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $token   = $this->input->post('token');
        $name    = $this->input->post('signer_name');
        $cpf     = $this->input->post('signer_cpf');
        $accepted = $this->input->post('accepted');

        if (!$accepted) {
            echo json_encode(['success' => false, 'message' => 'Você precisa aceitar os termos']);
            return;
        }

        $contract = $this->axiomchannel_model->get_contract_by_token($token);
        if (!$contract || $contract->status === 'signed') {
            echo json_encode(['success' => false, 'message' => 'Contrato inválido ou já assinado']);
            return;
        }

        $hash   = hash('sha256', $contract->content . $name . $cpf . date('Y-m-d H:i:s'));
        $result = $this->axiomchannel_model->sign_contract($token, [
            'signer_name'       => $name,
            'signer_cpf'        => $cpf,
            'signer_ip'         => $this->input->ip_address(),
            'signer_user_agent' => substr($this->input->user_agent(), 0, 500),
            'document_hash'     => $hash,
        ]);

        echo json_encode(['success' => (bool) $result]);
    }

    public function contract_pdf($id)
    {
        $contract = $this->axiomchannel_model->get_contract($id);
        if (!$contract) show_404();

        // PDF simples via HTML — sem dependência externa
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html><head><meta charset="utf-8">';
        echo '<style>body{font-family:Arial,sans-serif;max-width:800px;margin:40px auto;padding:20px}';
        echo 'h1{font-size:20px;border-bottom:2px solid #333;padding-bottom:10px}';
        echo '.meta{font-size:12px;color:#666;margin-bottom:20px}';
        echo '.content{line-height:1.6;white-space:pre-wrap}';
        echo '.signature-block{margin-top:60px;border-top:1px solid #333;padding-top:20px}';
        echo '@media print{body{margin:0}}</style>';
        echo '<title>' . htmlspecialchars($contract->title) . '</title></head><body>';
        echo '<h1>' . htmlspecialchars($contract->title) . '</h1>';
        echo '<div class="meta">Status: ' . ucfirst($contract->status);
        if ($contract->signed_at) echo ' | Assinado em: ' . date('d/m/Y H:i', strtotime($contract->signed_at));
        echo '</div>';
        echo '<div class="content">' . nl2br(htmlspecialchars($contract->content)) . '</div>';
        if ($contract->status === 'signed') {
            echo '<div class="signature-block">';
            echo '<p><strong>Assinado por:</strong> ' . htmlspecialchars($contract->signer_name) . '</p>';
            echo '<p><strong>CPF:</strong> ' . htmlspecialchars($contract->signer_cpf) . '</p>';
            echo '<p><strong>Data/hora:</strong> ' . date('d/m/Y H:i:s', strtotime($contract->signed_at)) . '</p>';
            echo '<p><strong>IP:</strong> ' . htmlspecialchars($contract->signer_ip) . '</p>';
            echo '<p><strong>Hash:</strong> <small>' . $contract->document_hash . '</small></p>';
            echo '</div>';
        }
        echo '<script>window.print()</script>';
        echo '</body></html>';
    }

    public function contract_templates()
    {
        $device_id = $this->input->get('device_id');
        $devices   = $this->axiomchannel_model->get_devices();
        $device_id = $device_id ?: ($devices[0]->id ?? null);
        $templates = $this->axiomchannel_model->get_contract_templates($device_id);

        $data = [
            'title'     => 'Templates de Contrato',
            'devices'   => $devices,
            'device_id' => $device_id,
            'templates' => $templates,
        ];

        $this->load->view('axiomchannel/contracts/index', $data);
    }

    public function contract_template_save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $data = [
            'id'        => (int) $this->input->post('id') ?: null,
            'device_id' => (int) $this->input->post('device_id') ?: null,
            'name'      => $this->input->post('name'),
            'content'   => $this->input->post('content'),
            'variables' => $this->input->post('variables'),
        ];

        if (!$data['id']) unset($data['id']);

        $id = $this->axiomchannel_model->save_contract_template($data);
        echo json_encode(['success' => true, 'id' => $id]);
    }

    // ============================================================
    // COPILOTO IA — análise em tempo real da conversa
    // ============================================================
    public function copilot_analyze()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $contact_id = (int) $this->input->post('contact_id');
        if (!$contact_id) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            return;
        }

        $messages = $this->axiomchannel_model->get_messages($contact_id, 15, 0);
        if (empty($messages)) {
            echo json_encode(['success' => false, 'message' => 'Sem mensagens para analisar']);
            return;
        }

        $gemini_key = get_option('axch_gemini_key');
        if (!$gemini_key) {
            echo json_encode(['success' => false, 'message' => 'Chave Gemini não configurada']);
            return;
        }

        $conv = '';
        foreach ($messages as $msg) {
            if (empty($msg->content)) continue;
            $role = ($msg->direction === 'outbound') ? 'Atendente' : 'Cliente';
            $conv .= "{$role}: {$msg->content}\n";
        }

        $prompt = 'Analise esta conversa de atendimento via WhatsApp e responda SOMENTE com JSON válido, sem markdown, sem explicações.

Conversa:
' . $conv . '
Retorne exatamente este JSON:
{
  "sentiment": "neutro|interessado|hesitante|pronto",
  "tags": ["topico1", "topico2"],
  "suggestions": [
    {"label": "Rótulo curto", "text": "mensagem sugerida 1"},
    {"label": "Rótulo curto", "text": "mensagem sugerida 2"}
  ]
}

Regras:
- sentiment: "neutro" (sem interesse claro), "interessado" (demonstra interesse), "hesitante" (tem objeções/dúvidas), "pronto" (pronto para fechar/comprar)
- tags: máximo 4 palavras-chave curtas sobre o assunto da conversa
- suggestions: exatamente 2 sugestões de resposta em português para o atendente enviar, adequadas ao contexto atual';

        require_once(module_dir_path(AXIOMCHANNEL_MODULE, 'libraries/AxiomChannel_Gemini.php'));
        $gemini = new AxiomChannel_Gemini($gemini_key);
        $result = $gemini->generate_response('', [], $prompt);

        if (!$result['success']) {
            echo json_encode(['success' => false, 'message' => 'Erro ao consultar IA: ' . $result['error']]);
            return;
        }

        $text = trim($result['text']);
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/i', '', $text);
        $parsed = json_decode($text, true);

        if (!$parsed || !isset($parsed['sentiment'])) {
            echo json_encode(['success' => false, 'message' => 'Resposta inválida da IA']);
            return;
        }

        $valid = ['neutro', 'interessado', 'hesitante', 'pronto'];
        if (!in_array($parsed['sentiment'], $valid)) $parsed['sentiment'] = 'neutro';
        if (!is_array($parsed['tags']))        $parsed['tags']        = [];
        if (!is_array($parsed['suggestions'])) $parsed['suggestions'] = [];

        echo json_encode([
            'success'     => true,
            'sentiment'   => $parsed['sentiment'],
            'tags'        => array_slice($parsed['tags'], 0, 4),
            'suggestions' => array_slice($parsed['suggestions'], 0, 2),
        ]);
    }

    // ============================================================
    // META — Facebook + Instagram
    // ============================================================

    public function meta_connect()
    {
        $data['title']           = 'Conectar Facebook & Instagram';
        $data['devices']         = $this->axiomchannel_model->get_devices();
        $device_id               = !empty($data['devices']) ? $data['devices'][0]->id : null;
        $data['meta_connection'] = $device_id ? $this->axiomchannel_model->get_meta_connection($device_id) : null;
        $this->load->view('axiomchannel/meta/connect', $data);
    }

    public function meta_disconnect()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $device_id = (int)$this->input->post('device_id');
        if ($device_id) {
            $this->db->update(
                db_prefix() . 'axch_meta_connections',
                ['is_active' => 0],
                ['device_id' => $device_id]
            );
        }
        echo json_encode(['success' => true]);
    }

    public function meta_save()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $device_id   = (int)$this->input->post('device_id');
        $page_id     = $this->input->post('page_id');
        $page_name   = $this->input->post('page_name');
        $page_token  = $this->input->post('page_access_token');
        $ig_id       = $this->input->post('instagram_account_id');
        $ig_user     = $this->input->post('instagram_username');

        if (!$device_id || !$page_id || !$page_token) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
            return;
        }

        $this->axiomchannel_model->save_meta_connection([
            'device_id'              => $device_id,
            'page_id'                => $page_id,
            'page_name'              => $page_name,
            'page_access_token'      => $page_token,
            'instagram_account_id'   => $ig_id ?: null,
            'instagram_username'     => $ig_user ?: null,
            'connection_type'        => $ig_id ? 'both' : 'facebook',
            'is_active'              => 1,
            'webhook_verified'       => 0,
        ]);

        echo json_encode(['success' => true]);
    }

    public function meta_webhook()
    {
        // Verificação do webhook Meta (GET)
        if ($this->input->get('hub_mode') === 'subscribe') {
            $verify_token = get_option('axch_meta_verify_token') ?: 'axiom_meta_webhook';
            if ($this->input->get('hub_verify_token') === $verify_token) {
                echo $this->input->get('hub_challenge');
                return;
            }
            http_response_code(403);
            return;
        }

        // Recebe eventos (POST)
        $raw     = file_get_contents('php://input');
        $payload = json_decode($raw, true);

        if (!$payload || !isset($payload['entry'])) return;

        foreach ($payload['entry'] as $entry) {
            $page_id = $entry['id'] ?? null;
            if (!$page_id) continue;

            $connection = $this->db->get_where(
                db_prefix() . 'axch_meta_connections',
                ['page_id' => $page_id, 'is_active' => 1]
            )->row();

            if (!$connection) continue;

            // Facebook Messenger
            if (!empty($entry['messaging'])) {
                foreach ($entry['messaging'] as $event) {
                    if (!isset($event['message']['text'])) continue;
                    $this->_process_meta_message(
                        $connection,
                        $event['sender']['id'],
                        $event['message']['text'],
                        'facebook',
                        $event['message']['mid'] ?? null
                    );
                }
            }

            // Instagram Direct
            if (!empty($entry['changes'])) {
                foreach ($entry['changes'] as $change) {
                    if ($change['field'] !== 'messages') continue;
                    $msg = $change['value'] ?? null;
                    if (!$msg || !isset($msg['message']['text'])) continue;
                    $this->_process_meta_message(
                        $connection,
                        $msg['sender']['id'],
                        $msg['message']['text'],
                        'instagram',
                        $msg['message']['mid'] ?? null
                    );
                }
            }
        }

        echo 'OK';
    }

    private function _process_meta_message($connection, $sender_id, $text, $channel, $ext_msg_id)
    {
        $device_id = $connection->device_id;

        $contact = $this->axiomchannel_model->get_contact_by_external(
            $sender_id, $channel, $device_id
        );

        if (!$contact) {
            $profile_name = $this->_get_meta_profile_name(
                $sender_id, $connection->page_access_token, $channel
            );

            $this->db->insert(
                db_prefix() . 'axch_contacts',
                [
                    'device_id'       => $device_id,
                    'phone_number'    => $sender_id,
                    'name'            => $profile_name,
                    'channel'         => $channel,
                    'external_id'     => $sender_id,
                    'status'          => 'open',
                    'is_read'         => 0,
                    'last_message'    => $text,
                    'last_message_at' => date('Y-m-d H:i:s'),
                ]
            );
            $contact = $this->axiomchannel_model->get_contact($this->db->insert_id());
        } else {
            $this->db->update(
                db_prefix() . 'axch_contacts',
                ['last_message' => $text, 'last_message_at' => date('Y-m-d H:i:s'), 'is_read' => 0],
                ['id' => $contact->id]
            );
        }

        $this->axiomchannel_model->save_message([
            'contact_id'          => $contact->id,
            'device_id'           => $device_id,
            'direction'           => 'inbound',
            'type'                => 'text',
            'content'             => $text,
            'channel'             => $channel,
            'external_message_id' => $ext_msg_id,
            'is_read'             => 0,
            'status'              => 'delivered',
        ]);
    }

    private function _get_meta_profile_name($user_id, $page_token, $channel)
    {
        $url = "https://graph.facebook.com/{$user_id}?fields=name&access_token={$page_token}";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        return $data['name'] ?? ($channel === 'instagram' ? 'Instagram User' : 'Facebook User');
    }

    public function meta_send_message()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $contact_id = (int)$this->input->post('contact_id');
        $message    = $this->input->post('message');
        $contact    = $this->axiomchannel_model->get_contact($contact_id);

        if (!$contact || !$message) {
            echo json_encode(['success' => false]);
            return;
        }

        $connection = $this->axiomchannel_model->get_meta_connection($contact->device_id);
        if (!$connection) {
            echo json_encode(['success' => false, 'message' => 'Meta não conectado']);
            return;
        }

        $url  = "https://graph.facebook.com/v18.0/me/messages?access_token={$connection->page_access_token}";
        $body = json_encode([
            'recipient' => ['id' => $contact->external_id],
            'message'   => ['text' => $message],
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200) {
            $this->axiomchannel_model->save_message([
                'contact_id' => $contact_id,
                'device_id'  => $contact->device_id,
                'direction'  => 'outbound',
                'type'       => 'text',
                'content'    => $message,
                'channel'    => $contact->channel,
                'status'     => 'sent',
                'sent_by_ai' => 0,
            ]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao enviar: ' . $response]);
        }
    }

    public function dashboard()
    {
        $hoje = date('Y-m-d');

        // Mensagens por canal hoje
        $canais = $this->db->query("
            SELECT COALESCE(channel,'whatsapp') as channel,
                   COUNT(DISTINCT contact_id) as total
            FROM tblaxch_messages
            WHERE DATE(created_at) = '{$hoje}'
            GROUP BY channel
        ")->result();

        // IA performance
        $ia_total = $this->db->query("
            SELECT COUNT(*) as total FROM tblaxch_messages
            WHERE DATE(created_at) = '{$hoje}'
            AND direction = 'outbound' AND sent_by_ai = 1
        ")->row()->total;

        $conv_total = $this->db->query("
            SELECT COUNT(DISTINCT contact_id) as total
            FROM tblaxch_messages WHERE DATE(created_at) = '{$hoje}'
        ")->row()->total;

        $ia_pct = $conv_total > 0 ? round(($ia_total / $conv_total) * 100) : 0;

        $ia_transferiu = $this->db->query("
            SELECT COUNT(*) as total FROM tblaxch_contacts
            WHERE DATE(updated_at) = '{$hoje}' AND status = 'pending'
        ")->row()->total;

        $ag_ia = $this->db->query("
            SELECT COUNT(*) as total FROM tblaxch_appointments
            WHERE DATE(created_at) = '{$hoje}' AND created_by = 'ai'
        ")->row()->total;

        $contratos_ia = $this->db->query("
            SELECT COUNT(*) as total FROM tblaxch_contracts
            WHERE DATE(sent_at) = '{$hoje}'
        ")->row()->total;

        // Tempo médio de resposta da IA (segundos)
        $tempo_row = $this->db->query("
            SELECT AVG(TIMESTAMPDIFF(SECOND, m1.created_at, m2.created_at)) as avg_t
            FROM tblaxch_messages m1
            JOIN tblaxch_messages m2 ON m2.contact_id = m1.contact_id
            WHERE m1.direction = 'inbound' AND m2.direction = 'outbound'
            AND DATE(m1.created_at) = '{$hoje}' AND m2.created_at > m1.created_at
            AND m2.sent_by_ai = 1
        ")->row();
        $tempo = round($tempo_row->avg_t ?? 0);

        // Automações disparadas hoje
        $automacoes = $this->db->query("
            SELECT a.type, COUNT(l.id) as total
            FROM tblaxch_automations a
            LEFT JOIN tblaxch_automation_log l
              ON l.automation_id = a.id AND DATE(l.sent_at) = '{$hoje}'
            WHERE a.is_active = 1
            GROUP BY a.type
        ")->result();

        // Pipeline
        $pipeline_data = [];
        $pipelines = $this->db->get('tblaxch_pipelines')->result();
        if (!empty($pipelines)) {
            $stages = $this->db->where('pipeline_id', $pipelines[0]->id)
                ->order_by('position', 'ASC')
                ->get('tblaxch_pipeline_stages')->result();
            foreach ($stages as $s) {
                $total = $this->db->where('stage_id', $s->id)
                    ->count_all_results('tblaxch_crm_leads');
                $pipeline_data[] = ['name' => $s->name, 'color' => $s->color, 'total' => $total];
            }
        }

        // Conversas abertas recentes
        $recentes = $this->db->query("
            SELECT c.*,
              (SELECT content FROM tblaxch_messages WHERE contact_id = c.id ORDER BY created_at DESC LIMIT 1) as last_msg
            FROM tblaxch_contacts c
            WHERE c.status = 'open'
            ORDER BY c.last_message_at DESC LIMIT 5
        ")->result();

        $data['title']         = 'Dashboard AXIOM';
        $data['canais']        = $canais;
        $data['ia_total']      = $ia_total;
        $data['conv_total']    = $conv_total;
        $data['ia_pct']        = $ia_pct;
        $data['ia_transferiu'] = $ia_transferiu;
        $data['ag_ia']         = $ag_ia;
        $data['contratos_ia']  = $contratos_ia;
        $data['tempo']         = $tempo;
        $data['automacoes']    = $automacoes;
        $data['pipeline']      = $pipeline_data;
        $data['recentes']      = $recentes;
        $data['devices']       = $this->db->get('tblaxch_devices')->result();

        $this->load->view('axiomchannel/dashboard', $data);
    }

    // ============================================================
    // SPA — Single Page Application
    // ============================================================

    public function spa()
    {
        $data['title'] = 'AXIOM';
        $this->load->view('axiomchannel/spa/index', $data);
    }

    public function spa_page($page = 'dashboard')
    {
        $pages = [
            'dashboard','conversas','pipeline','assistente',
            'automacoes','agendamentos','contratos','dispositivos',
            'clientes','financeiro','relatorios','leads'
        ];

        if (!in_array($page, $pages)) {
            echo '<p style="color:rgba(255,255,255,.4)">Página não encontrada.</p>';
            return;
        }

        $hoje = date('Y-m-d');
        $data = [];
        $data['csrf_token'] = $this->security->get_csrf_hash();
        $data['csrf_name']  = $this->security->get_csrf_token_name();

        switch ($page) {
            case 'dashboard':
                $mes     = date('Y-m');
                $mes_ant = date('Y-m', strtotime('-1 month'));

                // Filtro por device: admin vê tudo, atendente vê só o seu device
                $device_scope = axch_get_device_scope();
                $scope_str    = !empty($device_scope) ? implode(',', array_map('intval', $device_scope)) : '0';
                $data['is_admin'] = axch_is_admin();

                $data['fat_atual']      = $this->db->query("SELECT COALESCE(SUM(total),0) as t FROM tblinvoices WHERE DATE_FORMAT(date,'%Y-%m')='{$mes}' AND status=2")->row()->t;
                $data['fat_ant']        = $this->db->query("SELECT COALESCE(SUM(total),0) as t FROM tblinvoices WHERE DATE_FORMAT(date,'%Y-%m')='{$mes_ant}' AND status=2")->row()->t;
                $data['fat_pct']        = $data['fat_ant'] > 0 ? round((($data['fat_atual'] - $data['fat_ant']) / $data['fat_ant']) * 100) : 0;
                $data['conv_hoje']      = $this->db->query("SELECT COUNT(DISTINCT contact_id) as t FROM tblaxch_messages WHERE DATE(created_at)='{$hoje}' AND device_id IN ({$scope_str})")->row()->t;
                $data['ia_hoje']        = $this->db->query("SELECT COUNT(*) as t FROM tblaxch_messages WHERE DATE(created_at)='{$hoje}' AND direction='outbound' AND sent_by_ai=1 AND device_id IN ({$scope_str})")->row()->t;
                $data['leads_hoje']     = $this->db->query("SELECT COUNT(*) as t FROM tblleads WHERE DATE(dateadded)='{$hoje}'")->row()->t;
                $data['ag_hoje']        = $this->db->query("SELECT a.*,c.name as cn FROM tblaxch_appointments a LEFT JOIN tblaxch_contacts c ON c.id=a.contact_id WHERE DATE(a.start_datetime)='{$hoje}' AND a.status!='cancelled' ORDER BY a.start_datetime ASC LIMIT 5")->result();
                $data['devices']        = $this->axiomchannel_model->get_devices();
                $data['recentes']       = $this->db->query("SELECT c.*,(SELECT content FROM tblaxch_messages WHERE contact_id=c.id ORDER BY created_at DESC LIMIT 1) as lm FROM tblaxch_contacts c WHERE c.status='open' AND c.device_id IN ({$scope_str}) ORDER BY c.last_message_at DESC LIMIT 5")->result();
                $data['pipeline_stages'] = [];
                $pipelines = $this->axiomchannel_model->get_pipelines();
                if (!empty($pipelines)) {
                    $stages = $this->axiomchannel_model->get_stages($pipelines[0]->id);
                    foreach ($stages as $s) {
                        $s->total = $this->db->where('stage_id', $s->id)->count_all_results(db_prefix() . 'axch_crm_leads');
                        $data['pipeline_stages'][] = $s;
                    }
                }
                break;

            case 'conversas':
                $data['contacts'] = $this->db->query("SELECT c.*,(SELECT content FROM tblaxch_messages WHERE contact_id=c.id ORDER BY created_at DESC LIMIT 1) as lm FROM tblaxch_contacts c ORDER BY c.last_message_at DESC LIMIT 30")->result();
                break;

            case 'pipeline':
                $data['pipelines']      = $this->axiomchannel_model->get_pipelines();
                $data['stages']         = [];
                $data['leads_by_stage'] = [];
                if (!empty($data['pipelines'])) {
                    $data['stages'] = $this->axiomchannel_model->get_stages($data['pipelines'][0]->id);
                    foreach ($data['stages'] as $s) {
                        $data['leads_by_stage'][$s->id] = $this->axiomchannel_model->get_crm_leads($data['pipelines'][0]->id, $s->id);
                    }
                }
                break;

            case 'assistente':
                $data['devices']   = $this->axiomchannel_model->get_devices();
                $device_id = !empty($data['devices']) ? $data['devices'][0]->id : null;
                $data['assistant'] = $device_id ? $this->axiomchannel_model->get_assistant($device_id) : null;
                $data['knowledge'] = $data['assistant'] ? $this->axiomchannel_model->get_knowledge_base($data['assistant']->id) : [];
                break;

            case 'agendamentos':
                $data['appointments'] = $this->db->query("SELECT a.*,c.name as cn FROM tblaxch_appointments a LEFT JOIN tblaxch_contacts c ON c.id=a.contact_id WHERE a.status!='cancelled' ORDER BY a.start_datetime ASC LIMIT 20")->result();
                $data['devices']      = $this->axiomchannel_model->get_devices();
                break;

            case 'contratos':
                $data['contracts'] = $this->db->query("SELECT c.*,ct.name as cn FROM tblaxch_contracts c LEFT JOIN tblaxch_contacts ct ON ct.id=c.contact_id ORDER BY c.created_at DESC LIMIT 20")->result();
                break;

            case 'dispositivos':
                $data['devices'] = $this->axiomchannel_model->get_devices();
                break;

            case 'clientes':
                $data['clients'] = $this->db->query("SELECT * FROM tblclients ORDER BY datecreated DESC LIMIT 20")->result();
                break;

            case 'financeiro':
                $data['invoices'] = $this->db->query("SELECT i.*,c.company FROM tblinvoices i LEFT JOIN tblclients c ON c.userid=i.clientid ORDER BY i.date DESC LIMIT 20")->result();
                break;

            case 'automacoes':
                $data['automations'] = $this->db->get(db_prefix() . 'axch_automations')->result();
                break;

            case 'relatorios':
                break;

            case 'leads':
                $data['contacts'] = $this->db->query("
                    SELECT c.*,
                      (SELECT content FROM tblaxch_messages
                       WHERE contact_id=c.id
                       ORDER BY created_at DESC LIMIT 1) as lm,
                      (SELECT COUNT(*) FROM tblaxch_messages
                       WHERE contact_id=c.id) as msg_count
                    FROM tblaxch_contacts c
                    ORDER BY c.last_message_at DESC
                    LIMIT 50
                ")->result();
                break;
        }

        $data['page'] = $page;
        $this->load->view('axiomchannel/spa/pages/' . $page, $data);
    }

    public function save_theme()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $staff_id = get_staff_user_id();
        $theme    = $this->input->post('theme') ?: 'dark';
        $accent   = $this->input->post('accent_color') ?: '#2D7A6B';
        $val = json_encode(['theme' => $theme, 'accent_color' => $accent]);
        $exists = $this->db->get_where('tblstaff_meta', ['staffid' => $staff_id, 'meta_key' => 'axiom_theme'])->row();
        if ($exists) {
            $this->db->update('tblstaff_meta', ['meta_value' => $val], ['staffid' => $staff_id, 'meta_key' => 'axiom_theme']);
        } else {
            $this->db->insert('tblstaff_meta', ['staffid' => $staff_id, 'meta_key' => 'axiom_theme', 'meta_value' => $val]);
        }
        echo json_encode(['success' => true]);
    }

    public function get_theme()
    {
        if (!$this->input->is_ajax_request()) show_404();
        $staff_id = get_staff_user_id();
        $row = $this->db->get_where('tblstaff_meta', ['staffid' => $staff_id, 'meta_key' => 'axiom_theme'])->row();
        $default = ['theme' => 'dark', 'accent_color' => '#2D7A6B'];
        if ($row && $row->meta_value) {
            $pref = json_decode($row->meta_value, true);
            echo json_encode(array_merge($default, $pref));
        } else {
            echo json_encode($default);
        }
    }

} // ← FECHA A CLASSE.
