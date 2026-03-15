<?php

defined('BASEPATH') || exit('No direct script access allowed');

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Exception\RequestException;

class Chatbot extends App_Model
{
    private $Contactcenter_model;

    public function __construct()
    {
        //$this->Contactcenter_model = new Contactcenter_model();

    }



    /**
     * save_automation do drawflow
     */
    public function save_automation($data)
    {



        if ($data) {
            $data = json_decode($data);

            $CoutInput = get_option("update_input_automation");
            $CoutInput = $CoutInput + 1;
            update_option("update_input_automation", $CoutInput);

            $json = json_encode($data->drawflowData);
            $crear = [
                "data" => $json,
                "date" => date('Y-m-d H:i:s'),
                "status" => $data->status,
                "title" => $data->title,
            ];

            $this->db->where('draw_id', $data->id);
            $this->db->update(db_prefix() . 'contactcenter_drawflow', $crear);
        }
    }

    /**
     * get_automation do drawflow
     */
    public function get_automation($draw_id = null)
    {

        if ($draw_id) {
            $this->db->where('draw_id', $draw_id);
            $result =  $this->db->get(db_prefix() . 'contactcenter_drawflow')->row();
        } else {
            $result =  $this->db->get(db_prefix() . 'contactcenter_drawflow')->result();
        }
        return $result;
    }

    /**
     * get_automation do drawflow
     */
    public function get_automation_active($draw_id = null)
    {

        if ($draw_id) {
            $this->db->where('draw_id', $draw_id);
            $this->db->where('status', 1);
            $result =  $this->db->get(db_prefix() . 'contactcenter_drawflow')->row();
        } else {
            $this->db->where('status', 1);
            $result =  $this->db->get(db_prefix() . 'contactcenter_drawflow')->result();
        }
        return $result;
    }

    /**
     * fluxo_create
     * Criar o fluxo
     */
    public function fluxo_create()
    {

        $start = [
            "title" => _l('drawflow_flow_sketch'),
            "date" => date('Y-m-d H:i:s'),
        ];

        $this->db->insert(db_prefix() . 'contactcenter_drawflow', $start);
        $insert = $this->db->insert_id();
        if ($insert) {
            return $insert;
        } else {
            return false;
        }
    }

    public function delete_fluxo($data)
    {
        if ($data) {
            $this->db->where('draw_id', $data["id"]);
            $this->db->delete(db_prefix() . 'contactcenter_drawflow');

            $this->db->where('draw_id', $data["id"]);
            $this->db->delete(db_prefix() . 'contactcenter_drawflow_group_children');

            $this->db->where('draw_id', $data["id"]);
            $this->db->delete(db_prefix() . 'contactcenter_drawflow_group');

            return true;
        }
    }
    /**
     * save_group_banco
     */
    public function save_group_banco($data)
    {
        if ($data) {

            //Cria o start         
            $this->db->where('group_id', 1);
            $this->db->where('draw_id', $data['draw_id']);
            $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group')->row();
            if (!$Result) {
                $start = [
                    'group_id' => 1,
                    'type' => "start",
                    'title' => "Start",
                    'date' => date('Y-m-d H:i:s'),
                    'draw_id' => $data['draw_id'],
                ];
                $this->db->insert(db_prefix() . 'contactcenter_drawflow_group', $start);
            }


            switch ($data["action"]):
                case 'crear':
                    if ($data['id']) {
                        $crear["group_id"] = $data['id'];
                    }
                    if ($data['type']) {
                        $crear["type"] = $data['type'];
                    }
                    if ($data['draw_id']) {
                        $crear["draw_id"] = $data['draw_id'];
                    }
                    if ($data['custom_fields']) {
                        $crear["custom_fields"] = $data['custom_fields'];
                    }
                    if ($data['title']) {
                        $crear["title"] = $data['title'];
                    }
                    $crear["date"] = date('Y-m-d H:i:s');

                    $this->db->where('group_id', $data['id']);
                    $this->db->where('draw_id', $data['draw_id']);
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group')->row();
                    if ($Result) {
                        $this->db->where('group_id', $Result->group_id);
                        $this->db->update(db_prefix() . 'contactcenter_drawflow_group', $crear);
                        return true;
                    } else {
                        $this->db->insert(db_prefix() . 'contactcenter_drawflow_group', $crear);
                        return true;
                    }
                    break;

                case 'crearGpt':
                    $crear = [
                        'gpt_model' => $data['gpt_model'],
                        'gpt_caracters' => $data['gpt_caracters'],
                        'gpt_tag_exit' => $data['gpt_tag_exit'],
                        'gpt_prompt' => $data['gpt_prompt'],
                    ];

                    $this->db->where('group_id', $data['id']);
                    $this->db->where('draw_id', $data['draw_id']);
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group')->row();
                    if ($Result) {
                        $this->db->where('group_id', $Result->group_id);
                        $this->db->update(db_prefix() . 'contactcenter_drawflow_group', $crear);
                        return true;
                    }
                    break;
                case 'connectionCreated':
                    $this->db->where('group_output', $data['id']);
                    $this->db->where('draw_id', $data['draw_id']);
                    $this->db->where_in('type',  ["start", "group"]);
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group')->row();
                    if ($Result) {
                        return false;
                        break;
                    }

                    $crear = [
                        'group_inputs' => $data['group_inputs'],
                        'group_output' => $data['group_output'],
                    ];

                    $this->db->where('group_id', $data['id']);
                    $this->db->where('draw_id', $data['draw_id']);
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group')->row();
                    if ($Result) {
                        $this->db->where('id', $Result->id);
                        $this->db->update(db_prefix() . 'contactcenter_drawflow_group', $crear);
                        return true;
                    }
                    break;
                case 'connectionRemoved':
                    $crear = [
                        'group_inputs' => null,
                        'group_output' => null,
                    ];
                    $this->db->where('group_inputs', $data['group_inputs']);
                    $this->db->where('group_output', $data['group_output']);
                    $this->db->where('draw_id', $data['draw_id']);
                    $this->db->update(db_prefix() . 'contactcenter_drawflow_group', $crear);
                    if ($this->db->affected_rows() > 0) {
                        return true;
                    }
                    break;

                case 'nodeMoved':
                    //deleta o group
                    $this->db->where('group_id', $data['id']);
                    $this->db->where('draw_id', $data['draw_id']);
                    $this->db->delete(db_prefix() . 'contactcenter_drawflow_group');

                    //deleta o children
                    $this->db->where('group_id', $data['id']);
                    $this->db->where('draw_id', $data['draw_id']);
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->result();
                    if ($Result) {
                        foreach ($Result as $value) {

                            //deleta arquivos
                            if ($value->url) {
                                if (file_exists("uploads/{$value->url}")) {
                                    unlink("uploads/{$value->url}");
                                }
                            }
                            $this->db->where('child_id', $value->child_id);
                            $this->db->delete(db_prefix() . 'contactcenter_drawflow_group_children');
                        }
                    }

                    //deleta o static_lead
                    $this->db->where('group_id', $data['id']);
                    $this->db->where('draw_id', $data['draw_id']);
                    $this->db->delete(db_prefix() . 'contactcenter_drawflow_static_lead');

                    return true;
                    break;
                case "removeItem":

                    $this->db->where('input_id', $data['id']);
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                    if ($Result) {
                        if ($Result->url) {
                            if (file_exists("uploads/{$Result->url}")) {
                                unlink("uploads/{$Result->url}");
                            }
                        }
                        $this->db->where('child_id', $Result->child_id);
                        $this->db->delete(db_prefix() . 'contactcenter_drawflow_group_children');
                    }
                    return true;
                    break;
            endswitch;
        }
    }


    public function save_group_children($data)
    {

        if ($data) {
            $data["date"] = date('Y-m-d H:i:s');
            switch ($data["type"]):
                case 'text':

                    $this->db->where('input_id', $data['input_id']);
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                    if ($Result) {
                        $this->db->where('child_id', $Result->child_id);
                        $this->db->update(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($this->db->affected_rows() > 0) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    } else {
                        $insertId = $this->db->insert(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($insertId) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    }
                    break;
                case 'location':

                    $location = [
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'name' => $data['name'],
                        'endress' => $data['endress'],
                        'endress' => $data['endress'],
                    ];
                    $loc = json_encode($location);
                    $crear = [
                        'text' => $loc,
                        'group_id' => $data['group_id'],
                        'draw_id' => $data['draw_id'],
                        'type' => 'location',
                    ];


                    $this->db->where('group_id', $data['group_id']);
                    $this->db->where('draw_id', $data['draw_id']);
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                    if ($Result) {
                        $this->db->where('child_id', $Result->child_id);
                        $this->db->update(db_prefix() . 'contactcenter_drawflow_group_children', $crear);
                        if ($this->db->affected_rows() > 0) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    } else {
                        $insertId = $this->db->insert(db_prefix() . 'contactcenter_drawflow_group_children', $crear);
                        if ($insertId) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    }
                    break;
                case 'sleep':

                    $this->db->where('input_id', $data['input_id']);
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                    if ($Result) {
                        $this->db->where('child_id', $Result->child_id);
                        $this->db->update(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($this->db->affected_rows() > 0) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    } else {
                        $insertId = $this->db->insert(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($insertId) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    }
                    break;
                case 'staff':

                    $this->db->where('input_id', $data['input_id']);
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                    if ($Result) {
                        $this->db->where('child_id', $Result->child_id);
                        $this->db->update(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($this->db->affected_rows() > 0) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    } else {
                        $insertId = $this->db->insert(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($insertId) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    }
                    break;

                case 'integretion-http':

                    $crear = [
                        'text' => json_encode($data),
                        'group_id' => $data['group_id'],
                        'draw_id' => $data['draw_id'],
                        'type' => 'integretion-http',
                        'input_id' => $data['input_id'],
                        'date' => $data['date'],
                    ];

                    $this->db->where('input_id', $data['input_id']);
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                    if ($Result) {
                        $this->db->where('child_id', $Result->child_id);
                        $this->db->update(db_prefix() . 'contactcenter_drawflow_group_children', $crear);
                        if ($this->db->affected_rows() > 0) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    } else {
                        $insertId = $this->db->insert(db_prefix() . 'contactcenter_drawflow_group_children', $crear);
                        if ($insertId) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    }


                    break;
                case 'notification':

                    $this->db->where('input_id', $data['input_id']);
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                    if ($Result) {
                        $this->db->where('child_id', $Result->child_id);
                        $this->db->update(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($this->db->affected_rows() > 0) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    } else {
                        $insertId = $this->db->insert(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($insertId) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    }
                    break;
                case 'conditionConection':

                    $this->db->where('group_id', $data['group_id']);
                    $this->db->where('draw_id', $data['draw_id']);
                    $this->db->where('conexao', $data['conexao']);
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                    if ($Result) {
                        if ($Result->next <= 0 && $data['next'] > 0) {
                            $this->db->where('child_id', $Result->child_id);
                            $this->db->update(db_prefix() . 'contactcenter_drawflow_group_children', ["next" => $data['next']]);
                            if ($this->db->affected_rows() > 0) {
                                $response = [
                                    'success' => true,
                                ];
                            }
                        } elseif ($data['next'] == 0 && $Result->next > 0) {
                            $this->db->where('child_id', $Result->child_id);
                            $this->db->update(db_prefix() . 'contactcenter_drawflow_group_children', ["next" => $data['next']]);
                            if ($this->db->affected_rows() > 0) {
                                $response = [
                                    'success' => true,
                                ];
                            }
                        } else {
                            $response = [
                                'success' => false,
                            ];
                        }
                    }

                    return $response;

                    break;

                case 'condition':

                    if ($data['else']) {
                        $CrearElse = [
                            "operador" => "else",
                            "group_id" => $data['group_id'],
                            "draw_id" => $data['draw_id'],
                            "type" => $data['type'],
                            "conexao" => $data['else'],
                            "date" =>  $data["date"],
                        ];
                        $this->db->where('group_id', $data['group_id']);
                        $this->db->where('operador', "else");
                        $ResultElse =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                        if ($ResultElse) {
                            $this->db->where('child_id', $ResultElse->child_id);
                            $this->db->update(db_prefix() . 'contactcenter_drawflow_group_children', $CrearElse);
                        } else {
                            $insertId = $this->db->insert(db_prefix() . 'contactcenter_drawflow_group_children', $CrearElse);
                        }
                    }
                    unset($data["else"]);

                    $this->db->where('input_id', $data['input_id']);
                    $this->db->where('operador !=', "else");
                    $Result =  $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                    if ($Result) {
                        $this->db->where('child_id', $Result->child_id);
                        $this->db->update(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($this->db->affected_rows() > 0) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    } else {
                        $insertId = $this->db->insert(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($insertId) {
                            $response = [
                                'success' => true,
                            ];
                        }
                        return $response;
                    }
                    break;

                case 'image':
                    unset($data["text"]);
                    if ($_FILES["file"] && $_FILES['file']['name']) {
                        $media = $this->uploads_media("contactcenter/chatbot/image", 30720, 'image');
                        $data["url"] = $media;

                        $insertId = $this->db->insert(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($insertId) {
                            $response = [
                                'success' => true,
                                'url' => "/uploads/{$media}"
                            ];
                            return $response;
                        }
                    }
                    break;
                case 'video':
                    unset($data["text"]);
                    if ($_FILES["file"] && $_FILES['file']['name']) {
                        $media = $this->uploads_media("contactcenter/chatbot/video", 30720, 'video');
                        $data["url"] = $media;

                        $insertId = $this->db->insert(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($insertId) {
                            $response = [
                                'success' => true,
                                'url' => "/uploads/{$media}"
                            ];
                            return $response;
                        }
                    }
                    break;
                case 'audio':
                    unset($data["text"]);
                    if ($_FILES["file"] && $_FILES['file']['name']) {
                        $media = $this->uploads_media("contactcenter/chatbot/audio", 30720, 'audio');
                        $data["url"] = $media;

                        $insertId = $this->db->insert(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($insertId) {
                            $response = [
                                'success' => true,
                                'url' => "/uploads/{$media}"
                            ];
                            return $response;
                        }
                    }
                    break;
                case 'document':
                    unset($data["text"]);
                    if ($_FILES["file"] && $_FILES['file']['name']) {
                        $media = $this->uploads_media("contactcenter/chatbot/document", 30720, 'document');
                        $data["url"] = $media;

                        $insertId = $this->db->insert(db_prefix() . 'contactcenter_drawflow_group_children', $data);
                        if ($insertId) {
                            $response = [
                                'success' => true,
                                'url' => "/uploads/{$media}"
                            ];
                            return $response;
                        }
                    }
                    break;

            endswitch;
        }
    }


    public function uploads_media($path, $maxSize = null, $mediaType = 'image')
    {
        // Define o caminho do diretório de upload
        $upload_path = FCPATH . "uploads/{$path}/" . date("m-Y");
        $return_path = "{$path}/" . date("m-Y");
        // Se o diretório não existir, cria-o
        if (!is_dir($upload_path)) {
            // Permissões 0777 dão permissões máximas, ajuste conforme necessário
            mkdir($upload_path, 0777, true);
        }

        // Configuração para tipos de mídia
        $config['upload_path'] = $upload_path;
        $config['file_name'] = uniqid(); // Nome do arquivo

        if ($mediaType == 'image') {
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size'] = $maxSize;
        } elseif ($mediaType == 'audio') {
            $config['allowed_types'] = 'mp3';
            $config['max_size'] = $maxSize;
        } elseif ($mediaType == 'document') {
            $config['allowed_types'] = 'pdf';
            $config['max_size'] = $maxSize;
        } elseif ($mediaType == 'video') {
            $config['allowed_types'] = 'mp4|avi|mov|wmv';
            $config['max_size'] = $maxSize;
        } else {
            return false; // Tipo de mídia não suportado
        }

        $this->load->library('upload', $config);

        if ($this->upload->do_upload("file")) {
            $upload_data = $this->upload->data();
            return $return_path . "/" . $upload_data['file_name'];
        } else {
            $error = $this->upload->display_errors();
            return false;
        }
    }

    /**
     * next_group_lead
     * atualiza o grupo de um contato
     * @param [int] $lead_id
     * @param [int] $session
     * @param [int] $group_id
     * @return void
     */
    private function upadate_group_lead($lead_id, $session, $group_id, $ClearAgenda = false)
    {
        $this->db->where('leadid', $lead_id);
        $this->db->where('session', $session);
        if ($group_id) {
            $group_id = $group_id;
        } else {
            $group_id = null;
        }

        if ($ClearAgenda) {
            $this->db->update(db_prefix() . 'contactcenter_contact', ["chatbot_group_id" => $group_id, "chatbot_agenda" => null]);
        } else {
            $this->db->update(db_prefix() . 'contactcenter_contact', ["chatbot_group_id" => $group_id]);
        }
    }

    /**
     * pega o grupo de um lead
     *
     * @param [array] $data
     * @return void
     */
    private function get_group_lead($data)
    {
        $this->db->where('leadid', $data["leadid"]);
        $this->db->where('session', $data["msg_session"]);
        $result = $this->db->get(db_prefix() . 'contactcenter_contact')->row();
        if ($result->chatbot_group_id) {
            return $result->chatbot_group_id;
        } else {
            return false;
        }
    }

    public function get_group_banco($data)
    {
        if ($data) {
            $this->db->where('group_id', $data["id"]);
            $this->db->where('draw_id', $data["draw_id"]);
            $result = $this->db->get(db_prefix() . 'contactcenter_drawflow_group')->row();
            if ($result) {
                return $result;
            } else {
                return false;
            }
        }
    }

    public function get_http_request($data)
    {
        if ($data) {
            $this->db->where('group_id', $data["id"]);
            $this->db->where('draw_id', $data["draw_id"]);
            $result = $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
            if ($result) {
                return $result;
            } else {
                return false;
            }
        }
    }


    private function next_group_lead($data, $chatbot_id)
    {
        // Limitando profundidade da recursão
        static $recursion_depth = 0;
        $recursion_depth++;
        if ($recursion_depth > 5) {
            log_message('error', 'Recursion depth exceeded in next_group_lead');
            return;
        }

        // Chama a função principal
        $this->get_chatbot($data, $chatbot_id);

        // Reseta o contador no final da execução
        $recursion_depth--;
    }

    /**
     * bloquear envio de mensagem
     *
     * @param [int] $leadid
     * @param integer $gpt_status
     * @return void
     */
    private function block_send_message($leadid, $gpt_status = 0)
    {
        $this->db->where('id', $leadid);
        $this->db->update(db_prefix() . 'leads', [
            'gpt_status' => $gpt_status
        ]);
    }

    private function get_lead($leadid)
    {
        $this->db->where('id', $leadid);
        $result = $this->db->get(db_prefix() . 'leads')->row();
        return $result;
    }

    /**
     * Get_chatbot
     * Faz fluxo de chatbot
     * @param [array] $data
     * @param integer $chatbot_id
     * @return void
     */
    public function get_chatbot($data, $chatbot_id)
    {
        $this->Contactcenter_model = new Contactcenter_model();

        if ($data["msg_fromMe"] == false) {
            // Busca o dados do lead
            $dadosLeads = $this->get_lead($data["leadid"]);
            
            // Refresh lead data to get latest gpt_status (in case it was updated by toggle)
            if ($dadosLeads && isset($dadosLeads->id)) {
                $this->db->flush_cache();
                $this->db->where('id', $dadosLeads->id);
                $dadosLeads = $this->db->get(db_prefix() . 'leads')->row();
            }
            
            // Check gpt_status - if AI is disabled for this lead, don't process
            if (!$dadosLeads || $dadosLeads->gpt_status == 1) {
                log_message('debug', "get_chatbot SKIP - AI disabled for lead ID: " . ($data["leadid"] ?? 'unknown') . ", gpt_status: " . ($dadosLeads->gpt_status ?? 'null'));
                log_activity("get_chatbot SKIP - AI disabled for lead ID: " . ($data["leadid"] ?? 'unknown'), get_staff_user_id());
                return false;
            }

            // Busca o chatbot 
            $this->db->where('draw_id', $chatbot_id);
            $this->db->where('type', "start");
            $chatbot = $this->db->get(db_prefix() . 'contactcenter_drawflow_group')->row();
            if ($chatbot) {

                // Busca o grupo de entrada
                $lead_group = $this->get_group_lead($data);

                if ($lead_group) {
                    $next_group = $lead_group;
                } else {
                    $next_group = $chatbot->group_inputs;
                }

                // Busca o grupo       
                $this->db->where('group_id', $next_group);
                $this->db->where('draw_id', $chatbot->draw_id);
                $Group = $this->db->get(db_prefix() . 'contactcenter_drawflow_group')->row();
                if ($Group) {

                    if ($Group->type == "group") {
                        //bloquea para não mandar mais mensagens
                        if ($dadosLeads->gpt_status == 0) {
                            $this->block_send_message($data["leadid"], 1);                           
                        } else {
                            return;
                        }
                        // Busca os filhos
                        $this->db->where('group_id', $Group->group_id);
                        $this->db->where('draw_id', $Group->draw_id);
                        $GroupChildren = $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->result();
                        if ($GroupChildren) {
                            foreach ($GroupChildren as $GroupChild) {

                                $texto = str_replace('{Lead-name}', $data["lead_name"], $GroupChild->text);
                                $texto = str_replace('{Agent-name}', get_contactcenter_staff_name($data["staffid"]), $texto);
                                //mgs de texto ou imagem
                                if ($GroupChild->type == "text") {
                                    $send =  $this->Contactcenter_model->send_text($data["msg_from"], $texto, $data["staffid"]);
                                } else if ($GroupChild->type == "image" || $GroupChild->type == "video" || $GroupChild->type == "audio" || $GroupChild->type == "document") {
                                    $url = site_url('uploads/' . $GroupChild->url);
                                    $name = explode('/', $GroupChild->url);
                                    $fileName = end($name);
                                    $send =  $this->Contactcenter_model->send_file($data["msg_from"],  $texto, $url, $GroupChild->type, $data["staffid"],$fileName);
                                } else if ($GroupChild->type == "sleep") {
                                    sleep($GroupChild->text);
                                }
                            }
                            $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Group->group_inputs);
                            $this->static_lead_group($data, $Group->group_id,  $Group->draw_id);
                            //libera para poder mandar novas mensagens
                            $this->block_send_message($data["leadid"]);                           
                            return;
                        }else{
                            // caso não tenha filho deve mandar para o proximo grupo
                            $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Group->group_inputs);
                            $this->block_send_message($data["leadid"]);                          
                            return;
                        }
                    } else if ($Group->type == "sleep") {
                        // Verifica se há registros no banco de dados
                        $this->db->where('group_id', $Group->group_id);
                        $this->db->where('draw_id', $Group->draw_id);
                        $GroupChildren = $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                        if ($GroupChildren) {
                            if ($GroupChildren->text) {
                                sleep($GroupChildren->text);
                            } else {
                                sleep(1);
                            }

                            $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Group->group_inputs);
                            $this->static_lead_group($data, $Group->group_id,  $Group->draw_id);
                            if ($Group->group_inputs) {
                                $this->next_group_lead($data, $chatbot_id);
                            }
                        }
                        return;
                    } else if ($Group->type == "moment-day") {
                        $hour = date('H');

                        if ($hour >= 5 && $hour < 12) {
                            $data["msg_content"] = 'morning';
                        } elseif ($hour >= 12 && $hour < 17) {
                            $data["msg_content"] =  'afternoon';
                        } elseif ($hour >= 17 && $hour < 21) {
                            $data["msg_content"] =  'evening';
                        } else {
                            $data["msg_content"] =  'night';
                        }

                        $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Group->group_inputs);
                        $this->static_lead_group($data, $Group->group_id,  $Group->draw_id);
                        if ($Group->group_inputs) {
                            $this->next_group_lead($data, $chatbot_id);
                        }

                        return;
                    } else if ($Group->type == "condition") {
                        // Busca os filhos
                        // Verifica se há registros no banco de dados
                        $this->db->where('group_id', $Group->group_id);
                        $this->db->where('draw_id', $Group->draw_id);
                        $this->db->where('type', 'condition');
                        $this->db->order_by("conexao", "ASC");
                        $GroupCondition = $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->result();


                        if ($GroupCondition) {
                            foreach ($GroupCondition as  $Condition) {

                                $Comtem = explode(",", $Condition->text);

                                $msg_content_normalized = contactcenter_remove_acentos($data["msg_content"]);
                                $found = false;
                                foreach ($Comtem as $keyword) {
                                    $keyword_normalized = contactcenter_remove_acentos(trim($keyword));
                                    if (stripos($msg_content_normalized, $keyword_normalized) !== false) {
                                        $found = true;
                                        break;
                                    }
                                }

                                $this->static_lead_group($data, $Group->group_id,  $Group->draw_id);
                                // Verifica o operador
                                if ($Condition->operador == "igual" && $Condition->text == $data["msg_content"]) {
                                    $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Condition->next);
                                    if ($Condition->next) {
                                        $this->next_group_lead($data, $chatbot_id);
                                    }
                                    return;
                                } else if ($Condition->operador == "contem" && $found) {

                                    $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Condition->next);
                                    if ($Condition->next) {
                                        $this->next_group_lead($data, $chatbot_id);
                                    }
                                    return;
                                } else if ($Condition->operador == "else") {
                                    $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Condition->next);
                                    //redireciona para o proximo grupo
                                    if ($Condition->next) {
                                        $this->next_group_lead($data, $chatbot_id);
                                    }
                                    return;
                                }
                            }
                        }
                    } else if ($Group->type == "status-leads") {
                        $this->static_lead_group($data, $Group->group_id,  $Group->draw_id);
                        // Busca os filhos
                        $this->db->where('group_id', $Group->group_id);
                        $this->db->where('draw_id', $Group->draw_id);
                        $GroupChildren = $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                        if ($GroupChildren) {
                            $leads =  new Leads_model();
                            $status = [
                                "status" => $GroupChildren->text,
                                "leadid" => $data["leadid"],
                            ];
                            $leads->update_lead_status($status);
                            $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Group->group_inputs);
                            //redireciona para o proximo grupo
                            if ($Group->group_inputs) {
                                $this->next_group_lead($data, $chatbot_id);
                            }
                        }
                        return;
                    } else if ($Group->type == "staff") {
                        $this->static_lead_group($data, $Group->group_id,  $Group->draw_id);
                        // Busca os filhos
                        $this->db->where('group_id', $Group->group_id);
                        $this->db->where('draw_id', $Group->draw_id);
                        $GroupChildren = $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                        if ($GroupChildren) {
                            $leads =  new Leads_model();
                            $Crear = [
                                "assigned" => $GroupChildren->text,
                            ];
                            $leads->update($Crear, $data["leadid"]);
                            $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Group->group_inputs);
                            //redireciona para o proximo grupo
                            if ($Group->group_inputs) {
                                $this->next_group_lead($data, $chatbot_id);
                            }
                        }
                        return;
                    } else if ($Group->type == "desactivateAi") {

                        $leads =  new Leads_model();
                        $Crear = [
                            "gpt_status" => 1,
                        ];
                        $leads->update($Crear, $data["leadid"]);
                        $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Group->group_inputs);
                        $this->static_lead_group($data, $Group->group_id,  $Group->draw_id);
                        //redireciona para o proximo grupo
                        if ($Group->group_inputs) {
                            $this->next_group_lead($data, $chatbot_id);
                        }
                        return;
                    } else if ($Group->type == "notification") {
                        // Busca os filhos
                        $this->db->where('group_id', $Group->group_id);
                        $this->db->where('draw_id', $Group->draw_id);
                        $GroupChildren = $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                        if ($GroupChildren) {


                            $texto = str_replace('{Lead-name}', $data["lead_name"], $GroupChildren->text);
                            $texto = str_replace('{Agent-name}', get_contactcenter_staff_name($data["staffid"]), $texto);

                            $notified = add_notification([
                                'description'     => $texto,
                                'touserid'        => $GroupChildren->url,
                                'fromuserid'        => $data["staffid"],
                                'link'            => '',
                            ]);

                            $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Group->group_inputs);
                            $this->static_lead_group($data, $Group->group_id,  $Group->draw_id);
                            //redireciona para o proximo grupo
                            if ($Group->group_inputs) {
                                $this->next_group_lead($data, $chatbot_id);
                            }
                        }
                        return;
                    } else if ($Group->type == "location") {
                        // Busca os filhos
                        $this->db->where('group_id', $Group->group_id);
                        $this->db->where('draw_id', $Group->draw_id);
                        $GroupChildren = $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();
                        if ($GroupChildren) {

                            $location = json_decode($GroupChildren->text, true);
                            $latitude = (float)$location["latitude"];
                            $longitude = (float)$location["longitude"];
                            $address = (string)$location["endress"];
                            $name = $location["name"];
                            $send =  $this->Contactcenter_model->send_location($data["msg_from"], $name, $address, $latitude, $longitude, $data["staffid"]);

                            $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Group->group_inputs);
                            $this->static_lead_group($data, $Group->group_id,  $Group->draw_id);
                            //redireciona para o proximo grupo
                            if ($Group->group_inputs) {
                                $this->next_group_lead($data, $chatbot_id);
                            }
                        }
                    } else if ($Group->type == "inputs") {
                        // Busca os filhos
                        $leads =  new Leads_model();

                        if ($data["msg_content"]) {
                            if (is_numeric($Group->custom_fields)) {
                                $Crear = [
                                    "custom_fields" => [
                                        "leads" => [
                                            $Group->custom_fields => $data["msg_content"]
                                        ]
                                    ]
                                ];
                            } else {
                                $Crear = [
                                    $Group->custom_fields => $data["msg_content"]
                                ];
                            }
                            $leads->update($Crear, $data["leadid"]);
                        }



                        $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Group->group_inputs);
                        $this->static_lead_group($data, $Group->group_id,  $Group->draw_id);
                        //redireciona para o proximo grupo
                        if ($Group->group_inputs) {
                            $this->next_group_lead($data, $chatbot_id);
                            
                        }
                    } else if ($Group->type == "agenda") {

                        $GetTimeLead = $this->save_time_agenda($data);

                        if (!$GetTimeLead) {

                            $text = "Só um minuto, vou verificar nossos horários disponíveis. ";
                            $send =  $this->Contactcenter_model->send_text($data["msg_from"], $text, $data["staffid"]);

                            $availableTimes = $this->get_time_agenda($data["staffid"]);
                            //salva o horário no contato
                            $this->save_time_agenda($data, $availableTimes);
                            $formattedTimes = $this->format_available_times($availableTimes);
                            $send =  $this->Contactcenter_model->send_text($data["msg_from"], $formattedTimes, $data["staffid"]);
                        } else {

                            // Trata a escolha do usuário com base nos horários salvos
                            $chosenTime = $this->handle_user_choice($data, $GetTimeLead->dates);

                            if ($chosenTime) {
                                $send = $this->Contactcenter_model->send_text($data["msg_from"], "Combinado! Agendado para " . date("d/m/Y H:i", strtotime($chosenTime)) . ". Obrigado!", $data["staffid"]);
                                $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Group->group_inputs, $ClearAgenda = true);
                                $this->static_lead_group($data, $Group->group_id,  $Group->draw_id);
                                //redireciona para o proximo grupo
                                if ($Group->group_inputs) {
                                    $this->next_group_lead($data, $chatbot_id);
                                }
                            } else {
                                $text = "Desculpe, não consegui entender a sua escolha. Por favor, selecione um dos horários disponíveis.";
                                $send =  $this->Contactcenter_model->send_text($data["msg_from"], $text, $data["staffid"]);
                            }
                        }
                    } else if ($Group->type == "IA") {

                        // Busca os filhos
                        $this->db->where('group_id', $Group->group_id);
                        $this->db->where('draw_id', $Group->draw_id);
                        $GroupChildren = $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->row();

                        $Comtem = explode(",", $GroupChildren->text);
                        $msg_content_normalized = contactcenter_remove_acentos($data["msg_content"]);

                        $found = false;
                        foreach ($Comtem as $keyword) {
                            $keyword_normalized = contactcenter_remove_acentos(trim($keyword));
                            if (stripos($msg_content_normalized, $keyword_normalized) !== false) {
                                $found = true;
                                break;
                            }
                        }


                        //redireciona para o proximo grupo
                        if ($found) {
                            $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Group->group_inputs);
                            $this->static_lead_group($data, $Group->group_id,  $Group->draw_id);
                            if ($Group->group_inputs) {
                                $this->next_group_lead($data, $chatbot_id);
                            }
                        } else {
                            //Manda para o Cron IA
                            $this->Contactcenter_model->insert_msg_cron_ia($data);
                        }
                    } else if ($Group->type == "IA-ChatGPT") {                        
                        $Comtem = explode(",", $Group->gpt_tag_exit);

                        $msg_content_normalized = contactcenter_remove_acentos($data["msg_content"]);

                        $found = false;
                        foreach ($Comtem as $keyword) {
                            $keyword_normalized = contactcenter_remove_acentos(trim($keyword));
                            if (stripos($msg_content_normalized, $keyword_normalized) !== false) {
                                $found = true;
                                break;
                            }
                        }


                        if ($found) {
                            $this->upadate_group_lead($data["leadid"], $data["msg_session"], $Group->group_inputs);
                            $this->static_lead_group($data, $Group->group_id,  $Group->draw_id);
                            //redireciona para o proximo grupo
                            if ($Group->group_inputs) {
                                $this->next_group_lead($data, $chatbot_id);
                            }
                        } else {
                            $messages = [];
                            $hoje = date('Y-m-d 00:00:01');
                            $this->db->where('msg_conversation_number', $data["msg_conversation_number"]);
                            $this->db->where('msg_session', $data["msg_session"]);
                            $this->db->where('msg_date >=', $hoje);
                            $result = $this->db->get(db_prefix() . 'contactcenter_message')->result();

                            if ($result) {
                                foreach ($result as $msg) {
                                    $role = $msg->msg_fromMe == 1 ? "assistant" : "user";

                                    $messages[] = [
                                        "role" => $role,
                                        "content" => $msg->msg_content
                                    ];
                                }
                            }
                            $maxTokens = intval($Group->gpt_caracters);


                            try {
                                $response = $this->sendChatGPTRequest($messages, $Group->gpt_prompt, $Group->gpt_model, $maxTokens);
                                if ($response['choices'][0]['message']['content']) {
                                    $send = $this->Contactcenter_model->send_text($data["msg_from"], $response['choices'][0]['message']['content'], $data["staffid"]);
                                } else {
                                    log_activity("Error send gpt sem assitente " . json_encode($response));
                                }
                            } catch (Exception $e) {
                                log_activity("Error send gpt sem assitente  try catch " . $e->getMessage());
                            }
                        }
                    }

                    // count next group

                } else {
                    //se não existir o grupo redireciona para o primeiro grupo
                    $this->upadate_group_lead($data["leadid"], $data["msg_session"], null);
                    $this->next_group_lead($data, $chatbot_id);
                }
            }
        }
    }

    /**
     * salva a data e horário no contato para usar no fluxo
     *
     * @param [type] $data
     * @return void
     */
    private function save_time_agenda($data, $time = null)
    {
        if ($time) {
            $date = json_encode($time);
            $this->db->where('leadid', $data["leadid"]);
            $this->db->where('session', $data["msg_session"]);
            $this->db->update(db_prefix() . 'contactcenter_contact', ['chatbot_agenda' => $date]);
        } else {
            $this->db->where('leadid', $data["leadid"]);
            $this->db->where('session', $data["msg_session"]);
            $array =  $this->db->get(db_prefix() . 'contactcenter_contact')->row();
            return json_decode($array->chatbot_agenda);
        }
    }


    // Formata os horários disponíveis para envio ao usuário
    private function format_available_times($availableTimes)
    {
        $formattedTimes = "Aqui estão os horários disponíveis:\n";
        foreach ($availableTimes['dates'] as $index => $time) {
            $formattedTimes .= ($index + 1) . ". " . date("d/m/Y H:i", strtotime($time)) . "\n";
        }
        // Adiciona a opção de cancelar ao final da lista
        $formattedTimes .= (count($availableTimes['dates']) + 1) . ". Cancelar\n";
        return $formattedTimes;
    }


    /**
     * Pega a data e horário disponíveis
     *
     * @param [int] $staffid
     * @return void
     */
    private function get_time_agenda($staffid)
    {
        $this->Contactcenter_model = new Contactcenter_model();
        $time = get_option("time_contactcenter");
        $explodeTime = explode(",", $time);
        $currentDateTime = date("Y-m-d H:i:s"); // Data e hora atual
        $content = [];

        $minutesToAdd = get_option("agendaMinutesToAdd");

        // Se houver mais de 4 horários, seleciona 4 aleatoriamente, mantendo a ordem
        if (count($explodeTime) >  get_option('quant_time_contactcenter')) {
            $randomKeys = array_rand($explodeTime,  get_option('quant_time_contactcenter')); // Seleciona quantidade índices aleatórios
            sort($randomKeys); // Ordena os índices aleatórios para preservar a sequência cronológica
            $explodeTime = array_intersect_key($explodeTime, array_flip($randomKeys)); // Filtra os 4 horários
        }

        foreach ($explodeTime as $hora) {
            $dataHora = date("Y-m-d {$hora}:00"); // Concatena a data fornecida com a hora
            if ($dataHora < $currentDateTime) {
                // Se a data/hora for menor que a atual, adicione um dia
                $dataHora = date("Y-m-d H:i:s", strtotime($dataHora . " +1 day"));
            }

            $Userid = ($staffid == 0) ? 1 : $staffid; // Define o ID do usuário
            $timeBanco = $this->Contactcenter_model->get_date_agenda($dataHora, $Userid);

            // se o horario for menor que a data atual mais os minutos, nao entra no loop. Exemplo. Se tiver horarios para as 14:30 e os minutos for 90, nao colocar esse
            if($minutesToAdd > 0) {
                if ($timeBanco < date("Y-m-d H:i:s", strtotime($currentDateTime . " +{$minutesToAdd} minutes"))) {
                    continue;
                }
            }

            // Adiciona o horário formatado ao array
            $content[] = $timeBanco;
        }

        $dataToSend = [
            "dates" => $content
        ];

        return $dataToSend;
    }




    /**
     * handle_user_choice
     * Pega o horário escolhido pelo usuário *
     * @param [array] $data
     * @param [array] $availableTimes
     * @return void
     */
    private function handle_user_choice($data, $availableTimes)
    {
        $this->Contactcenter_model = new Contactcenter_model();

        $userInput = strtolower($data["msg_content"]);

        // Adiciona a opção de cancelamento ao final do array de horários
        $availableTimes[] = "cancelar";

        // Primeiro, verificar se a resposta é um número
        if (is_numeric($userInput)) {
            $choiceIndex = (int)$userInput - 1;
            if ($choiceIndex === count($availableTimes) - 1) {
                // Se o usuário escolheu cancelar
                $this->Contactcenter_model->send_text($data["msg_from"], "Operação cancelada.", $data["staffid"]);
                $this->upadate_group_lead($data["leadid"], $data["msg_session"], null, $ClearAgenda = true);
                return null;  // Retorna null para indicar que foi cancelado
            } elseif (isset($availableTimes[$choiceIndex])) {
                $chosenTime = $availableTimes[$choiceIndex];
                if ($chosenTime !== "cancelar") {
                    $agenda = $this->Contactcenter_model->insert_time_agenda($chosenTime, $data["msg_from"], $data["staffid"]);
                    if ($agenda) {
                        return $chosenTime;
                    }
                }
            }
        }

        // Se o usuário digitar "cancelar" diretamente
        if (stripos($userInput, "cancelar") !== false) {
            $this->Contactcenter_model->send_text($data["msg_from"], "Operação cancelada.", $data["staffid"]);
            $this->upadate_group_lead($data["leadid"], $data["msg_session"], null, $ClearAgenda = true);
            return null;  // Retorna null para indicar que foi cancelado
        }

        // Lógica original para tratar a escolha de horário
        foreach ($availableTimes as $time) {
            if ($time === "cancelar") continue;

            $date = date("d/m/Y", strtotime($time));
            $timeFormatted = date("H:i", strtotime($time));

            // Verifica se o input contém a data (dia/mês ou apenas dia) e a hora
            if (strpos($userInput, substr($date, 0, 2)) !== false && strpos($userInput, substr($date, 3, 2)) !== false) {
                if (strpos($userInput, substr($timeFormatted, 0, 2)) !== false) {
                    $agenda = $this->Contactcenter_model->insert_time_agenda($time, $data["msg_from"], $data["staffid"]);
                    if ($agenda) {
                        return $time;
                    }
                }
            } elseif (strpos($userInput, substr($date, 0, 2)) !== false && strpos($userInput, substr($timeFormatted, 0, 2)) !== false) {
                $agenda = $this->Contactcenter_model->insert_time_agenda($time, $data["msg_from"], $data["staffid"]);
                if ($agenda) {
                    return $time;
                }
            } elseif (strpos($userInput, substr($timeFormatted, 0, 2)) !== false) {
                $agenda = $this->Contactcenter_model->insert_time_agenda($time, $data["msg_from"], $data["staffid"]);
                if ($agenda) {
                    return $time;
                }
            }
        }

        return null;
    }


    private function sendChatGPTRequest($messages, $instruction, $model, $maxTokens)
    {
        // Recuperar a chave da API
        $apiKey = get_option("tokenopenai_contactcenter");
        $url = 'https://api.openai.com/v1/chat/completions';

        // Construir o payload com o array de mensagens e a instrução
        $payload = [
            "model" => $model,
            "messages" => array_merge(
                [["role" => "system", "content" => $instruction]],
                $messages,
            ),
            "max_tokens" => $maxTokens
        ];

        // Criar uma nova instância do Guzzle Client
        $client = new Client();

        try {
            // Fazer a requisição POST
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ],
                'json' => $payload,
            ]);

            // Decodificar a resposta JSON
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            // Tratar erros de requisição
            if ($e->hasResponse()) {
                $errorBody = $e->getResponse()->getBody()->getContents(); // Captura o corpo da resposta de erro
                log_activity("Error send gpt sem assitente erro 1 " . $e->getResponse()->getStatusCode() . ' - ' . $e->getResponse()->getReasonPhrase() . ' - ' . $errorBody);
                throw new Exception('HTTP Error: ' . $e->getResponse()->getStatusCode() . ' - ' . $e->getResponse()->getReasonPhrase() . ' - ' . $errorBody);
            } else {
                log_activity("Error send gpt sem assitente erro 2 " . $e->getMessage());
                throw new Exception('Request Error: ' . $e->getMessage());
            }
        }
    }


    private function static_lead_group($data, $groupid, $draw_id)
    {
        //verifica se exite um grupo
        if ($groupid) {
            $this->db->where('group_id', $groupid);
            $this->db->where('draw_id', $draw_id);
            $this->db->set('count', 'count + 1', FALSE);
            $this->db->update(db_prefix() . 'contactcenter_drawflow_group');

            // da insert na tabela static_lead            
            $this->db->where('draw_id', $draw_id);
            $this->db->where('lead_id',  $data['leadid']);
            $result = $this->db->get(db_prefix() . 'contactcenter_drawflow_static_lead')->row();

            if ($result) {
                $this->db->where('id', $result->id);
                $this->db->update(db_prefix() . 'contactcenter_drawflow_static_lead', [
                    'group_id' => $groupid,
                    'date' => date('Y-m-d')
                ]);
                return true;
            } else {
                $this->db->insert(db_prefix() . 'contactcenter_drawflow_static_lead', [
                    'draw_id' => $draw_id,
                    'group_id' => $groupid,
                    'lead_id' => $data['leadid'],
                    'date' => date('Y-m-d')
                ]);
                return true;
            }
        } else {
            //deleta o registro da tabela static_lead
            $this->db->where('draw_id', $draw_id);
            $this->db->where('lead_id',  $data['leadid']);
            $this->db->delete(db_prefix() . 'contactcenter_drawflow_static_lead');
        }
    }

    public function get_static_fluxo($data)
    {
        if ($data) {
            $this->db->where('draw_id', $data["draw_id"]);
            $this->db->where('type !=', "start");
            $result = $this->db->get(db_prefix() . 'contactcenter_drawflow_group')->result();

            if ($result) {
                $ln = [];
                foreach ($result as $Group) {
                    $TotalRead = $Group->count;

                    // Contar registros específicos para o grupo atual
                    $this->db->where('draw_id', $data["draw_id"]);
                    $this->db->where('group_id', $Group->group_id);
                    $this->db->from(db_prefix() . 'contactcenter_drawflow_static_lead');
                    $TotalLost = $this->db->count_all_results();

                    $ln[] = [
                        'group_id' => $Group->group_id,
                        'total_read' => $TotalRead,
                        'total_lost' => $TotalLost
                    ];
                }
            }

            return $ln;
        }
    }


    /**
     * get_count_leads - Retorna o total de leads
     *
     * @param [array] $data
     * @return void
     */
    public function get_count_leads($data)
    {
        // Junta a tabela de leads com a tabela principal usando o `lead_id`
        $this->db->select('leads.name, leads.phonenumber, leads.id, contactcenter_drawflow_static_lead.date');
        $this->db->from(db_prefix() . 'contactcenter_drawflow_static_lead');
        $this->db->join(db_prefix() . 'leads', db_prefix() . 'leads.id = ' . db_prefix() . 'contactcenter_drawflow_static_lead.lead_id');

        // Aplica as condições de filtragem
        $this->db->where('contactcenter_drawflow_static_lead.draw_id', $data["draw_id"]);
        $this->db->where('contactcenter_drawflow_static_lead.group_id', $data["id"]);

        // Executa a consulta e obtém os resultados
        $result = $this->db->get()->result();

        // Verifica se houve resultados e os organiza em um array
        if ($result) {
            $ln = [];
            foreach ($result as $lead) {
                $ln[] = [
                    'name' => $lead->name,
                    'fone' => $lead->phonenumber,
                    'date' => $lead->date,
                    'id' => $lead->id
                ];
            }
            return $ln;
        }


        return [];
    }

    /**
     * export_drawflow
     *
     * @param [type] $draw_id
     * @param [type] $encryption_key
     * @return void
     */
    public function export_drawflow($data)
    {
        $draw_id = $data['id'];
        $encryption_key = '123456';

        // Seleciona todos os fluxos do drawflow
        $this->db->where('draw_id', $draw_id);
        $drawflow = $this->db->get(db_prefix() . 'contactcenter_drawflow')->result();

        // Seleciona todos os grupos do drawflow
        $this->db->where('draw_id', $draw_id);
        $drawflow_group = $this->db->get(db_prefix() . 'contactcenter_drawflow_group')->result();

        // Seleciona todos os filhos dos grupos do drawflow
        $this->db->where('draw_id', $draw_id);
        $drawflow_group_children = $this->db->get(db_prefix() . 'contactcenter_drawflow_group_children')->result();

        // Organiza os dados em um array
        $export_data = [
            'drawflow' => $drawflow,
            'drawflow_group' => $drawflow_group,
            'drawflow_group_children' => $drawflow_group_children,
        ];

        // Converte o array para JSON
        $json_data = json_encode($export_data);

        // Criptografa o JSON
        $encrypted_data = openssl_encrypt($json_data, 'aes-256-cbc', $encryption_key, 0, $this->generate_iv());

        // Retorna os dados criptografados e o nome do arquivo como JSON
        $response = [
            'filename' => 'drawflow_export_' . $draw_id . '_' . date('Y-m-d_H-i-s') . '.txt',
            'data' => $encrypted_data
        ];

        return json_encode($response);
    }

    private function generate_iv()
    {
        return substr(hash('sha256', 'your_iv_key'), 0, 16);
    }



    public function import_drawflow($encrypted_data, $new_draw_id)
    {
        $encryption_key = '123456';
        // Descriptografa os dados
        $json_data = openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $this->generate_iv());

        if (!$json_data) {
            return false; // Retorna falso se a descriptografia falhar
        }


        // Decodifica os dados JSON
        $import_data = json_decode($json_data, true);

        if (!$import_data) {
            return false; // Retorna falso se a decodificação JSON falhar
        }

        foreach ($import_data['drawflow_group'] as &$group) {
            $group['draw_id'] = $new_draw_id;
            unset($group['id']);
            $this->db->insert(db_prefix() . 'contactcenter_drawflow_group', $group);
        }

        foreach ($import_data['drawflow_group_children'] as &$child) {
            $child['draw_id'] = $new_draw_id;
            unset($child['child_id']);
            $this->db->insert(db_prefix() . 'contactcenter_drawflow_group_children', $child);
        }


        foreach ($import_data['drawflow'] as $flow) {
            unset($flow['draw_id']);
            $this->db->where('draw_id', $new_draw_id);
            $this->db->update(db_prefix() . 'contactcenter_drawflow', $flow);
        }



        return true;
    }
}
