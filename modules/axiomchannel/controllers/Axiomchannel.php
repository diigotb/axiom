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
        $data['title']    = 'AxiomChannel — Inbox';
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

        // BUG ANTERIOR: $this->staff_model não existe no Perfex
        // CORREÇÃO: buscar direto no banco com $this->db
        $data['staff'] = $this->db->get('tblstaff')->result();

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
        $offset     = (int) $this->input->post('offset');
        $limit      = (int) $this->input->post('limit') ?: 50;

        $messages = $this->axiomchannel_model->get_messages($contact_id, $limit, $offset);
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
        if (isset($data['message']['imageMessage']))    { $type = 'image'; }
        elseif (isset($data['message']['audioMessage'])) { $type = 'audio'; }
        elseif (isset($data['message']['documentMessage'])) { $type = 'document'; }
        elseif (isset($data['message']['videoMessage'])) { $type = 'video'; }

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

} // ← FECHA A CLASSE. Nada pode vir depois deste ponto.
