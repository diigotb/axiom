<?php

defined('BASEPATH') || exit('No direct script access allowed');

class ChatWeb
{
    private $tokenOpenai;
    private $Contactcenter_model;
    private $leads_model;

    public function __construct()
    {
        $this->tokenOpenai = get_option("tokenopenai_contactcenter");
    }



    public function start_chat($data)
    {
        $CI = &get_instance();
        $lead = $this->add_lead($data);
        return $lead;
    }


    public function add_chatweb($data)
    {
        if ($data) {
            $CI = &get_instance();
            $data["date"] = date("Y-m-d H:i:s");
            $data["chat_hash"] = mt_rand(100000, 999999) . time();
            $CI->db->insert(db_prefix() . 'contactcenter_chat_header', $data);
            $insert_id = $CI->db->insert_id();
            if ($insert_id) {
                return true;
            }
        }
    }

    public function get_chatweb($chatId = null)
    {
        if ($chatId) {
            $CI = &get_instance();
            $CI->db->where("chat_id", $chatId);
            $chat = $CI->db->get(db_prefix() . 'contactcenter_chat_header')->row();
        } else {
            $CI = &get_instance();
            $chat = $CI->db->get(db_prefix() . 'contactcenter_chat_header')->result();
        }
        return $chat;
    }

    public function get_chatweb_token($token)
    {
        if ($token) {
            $CI = &get_instance();
            $CI->db->where("chat_hash", $token);
            $chat = $CI->db->get(db_prefix() . 'contactcenter_chat_header')->row();
            return $chat;
        }
    }
    /**
     * da insert do lead
     *
     * @param [array] $data
     * @return void
     */
    public function add_lead($data)
    {
        $CI = &get_instance();
        $this->leads_model = new leads_model();

        //Pega o chat
        $chatId = $this->get_chatweb($data["chat_id"]);
        if($chatId){
            $count = ["chat_count"=> $chatId->chat_count + 1];
            $CI->db->where("chat_id", $chatId->chat_id);
            $CI->db->update(db_prefix() . 'contactcenter_chat_header', $count);
        }


        //pega o Lead
        $CI->db->select("id,name,gpt_thread,phonenumber");
        $CI->db->where("phonenumber", $data["phonenumber"]);
        $lead = $CI->db->get(db_prefix() . 'leads')->row();
        if (!$lead) {

            $custom_fields = get_custom_fields('leads', ['slug' => 'leads_unidade', 'active' => 1]);
            $custom_id = $custom_fields[0]['id'];
            $creaLead = [
                "name" => $data["name"],
                "phonenumber" => $data["phonenumber"],
                "email" => $data["email"],
                "status" => $chatId->chat_status,
                "assigned" =>  $chatId->chat_assigned,
                "source" => $chatId->chat_source,
                "gpt_status" => 0,
                "gpt_thread" =>  $this->crearThreadsOpenai(),
                "custom_fields" => [
                    "leads" => [
                        "$custom_id" => "ChaWeb ID:{$chatId->chat_id}"
                    ]
                ],
            ];

            $id = $this->leads_model->add($creaLead);

            $CI->db->select("id,name,gpt_thread,phonenumber");
            $CI->db->where('id', $id);
            $lead = $CI->db->get(db_prefix() . 'leads')->row();
        }
        $result["lead"] = $lead;
        $result["chat"] = $chatId;
        return $result;
    }

    public function send_msg($data)
    {


        $CrearMsg = [
            "leadid" => $data["leadid"],
            "text" => $data["msg"],
            "chat_id" => $data["chat_id"],
            "fromMe" => 0,
            "cache_token" => $data["cache_token"],
            "date" => date("Y-m-d H:i:s")
        ];


        $this->send_pusher_chat($CrearMsg);
        unset($CrearMsg["cache_token"]);


        $CI = &get_instance();
        $CI->db->insert(db_prefix() . 'contactcenter_chat_web', $CrearMsg);
        $insert_id = $CI->db->insert_id();
        if ($insert_id) {
            $send = $this->send_msg_openai($data["thread"], $data["msg"]);

            $reply = $this->reply_send_openai($data["thread"], $data["phonenumber"], $staffid = null, $data["chat_assitent"]);

            $gtp = [
                "leadid" => $data["leadid"],
                "text" => $reply,
                "chat_id" => $data["chat_id"],
                "fromMe" => 1,
                "cache_token" => $data["cache_token"],
                "date" => date("Y-m-d H:i:s")
            ];
            $this->send_pusher_chat($gtp);
            unset($gtp["cache_token"]);
            $CI->db->insert(db_prefix() . 'contactcenter_chat_web', $gtp);
            return true;
        }
    }


    /**
     * Faz o envio das msg para tela do chat 
     * @param array $data
     */
    public function send_pusher_chat($data)
    {

        $CI = &get_instance();
        $CI->load->library('app_pusher');
        $chat = $this->monta_html_chat($data);
        $device_name = "{$_SERVER["SERVER_NAME"]}_{$data["cache_token"]}";
        $CI->app_pusher->trigger($device_name, 'chatweb', $chat);
    }


    public function monta_html_chat($data)
    {
        if ($data["fromMe"] == 0) {
            return "<div class='person'>{$data["text"]}</div>";
        } else {
            return "<div class='staff'>{$data["text"]}</div>";
        }
    }

    public function get_msg($leadid)
    {
        if ($leadid) {
            $CI = &get_instance();
            $date = date("Y-m-d");
            $CI->db->where('leadid', $leadid);            
            $CI->db->where('date >=', $date);            
            $chats = $CI->db->get(db_prefix() . 'contactcenter_chat_web')->result();
            if ($chats) {
                $ln = null;
                foreach ($chats as $chat) {
                    if ($chat->fromMe == 0) {
                        $ln.= "<div class='person'>{$chat->text}</div>";
                    } else {
                        $ln.= "<div class='staff'>{$chat->text}</div>";
                    }
                }
                return $ln;
            }
        }
    }
    /****************  openai ********************** */
    public function reply_send_openai($thread, $phonenumber, $staffid = null, $assistant_id = 0)
    {

        $this->Contactcenter_model = new Contactcenter_model();


        $Runsid = $this->runsAssistOpenai($thread, $assistant_id);

        if ($Runsid) {

            $result = $this->getStatusMsgOpenai($Runsid, $thread);

            do {
                $result = $this->getStatusMsgOpenai($Runsid, $thread);
                sleep(2);

                if ($result["status"] == "requires_action") {

                    $function = $result["required_action"]["submit_tool_outputs"]["tool_calls"][0];
                    $tool_callsId = $function["id"];
                    $function_name = $function["function"]["name"];
                    $function_arguments = $function["function"]["arguments"];
                    $output = "";
                    if ($function_name == "get_horario_agenda") {

                        $time = get_option("time_contactcenter");
                        $explodeTime = explode(",", $time);
                        $currentDateTime = date("Y-m-d H:i:s"); // Data e hora atual

                        foreach ($explodeTime as $data => $hora) {
                            $dataHora = date("Y-m-d {$hora}:00"); // Concatena a data fornecida com a hora
                            if ($dataHora < $currentDateTime) {
                                // Se a data/hora for maior que a atual, adicione um dia
                                $dataHora = date("Y-m-d H:i:s", strtotime($dataHora . " +1 day"));
                            }


                            if ($staffid == 0) {
                                $Userid = 1;
                            } else {
                                $Userid = $staffid;
                            }
                            $timeBanco = $this->Contactcenter_model->get_date_agenda($dataHora, $Userid);

                            // Adiciona o horário formatado ao array
                            $output .= '{"date": "' . $timeBanco . '"},';
                        }

                        $output = rtrim($output, ',');

                        $this->send_function_openai($thread, $Runsid, $tool_callsId, $output);
                    } elseif ($function_name == "set_horario_agenda") {

                        $horario = $function_arguments["date"];
                        $function_arguments = json_decode($function_arguments, true);
                        $horario = $function_arguments["date"];

                        if ($horario) {
                            //faz o agendamento
                            $Userid = ($staffid ? $staffid : 1);
                            $agenda = $this->Contactcenter_model->insert_time_agenda($horario, $phonenumber, $Userid);
                            if ($agenda) {
                                //$this->send_text($phonenumber, get_option("texto_end_contactcenter"), $staffid);
                                $output = "success";
                                $this->send_function_openai($thread, $Runsid, $tool_callsId, $output);
                            }
                        }
                    } elseif ($function_name == "set_reagendamento") {

                        $horario = $function_arguments["date"];
                        $function_arguments = json_decode($function_arguments, true);
                        $horario = $function_arguments["date"];

                        if ($horario) {
                            //faz o agendamento
                            $Userid = ($staffid ? $staffid : 1);
                            $agenda = $this->Contactcenter_model->insert_time_agenda($horario, $phonenumber, $Userid, $reagendamento = true);
                            if ($agenda) {
                                //$this->send_text($phonenumber, get_option("texto_end_contactcenter"), $staffid);
                                $output = "success";
                                $this->send_function_openai($thread, $Runsid, $tool_callsId, $output);
                            }
                        }
                    } elseif ($function_name == "verificar_agendamento_lead") {
                        //Verifica se o lead já tem agendamento 
                        $result = $this->Contactcenter_model->verificar_agendamento_lead($phonenumber);
                        $output = '{"date": "' . $result . '"}';
                        $this->send_function_openai($thread, $Runsid, $tool_callsId, $output);
                    }
                }
            } while ($result["status"] !== "completed");

            $RespotaOpenai = $this->getMessagesOpenai($thread);
            return $RespotaOpenai;
        } else {
            return false;
        }
    }

    //Passo 01
    public function crearThreadsOpenai()
    {


        // URL da API do OpenAI
        $url = "https://api.openai.com/v1/threads";

        // Cabeçalhos da requisição
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer $this->tokenOpenai",
            "OpenAI-Beta: assistants=v2"
        );

        // Configuração da requisição cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');

        // Executa a requisição e obtém a resposta
        $response = curl_exec($ch);

        // Verifica por erros
        if (curl_errno($ch)) {
            echo 'Erro na requisição cURL: ' . curl_error($ch);
        }

        // Fecha a sessão cURL
        curl_close($ch);

        // Exibe a resposta
        $responseArray = json_decode($response, true);
        // Verificar se a decodificação foi bem-sucedida
        if ($responseArray === null) {
            return 'Erro ao decodificar JSON';
        } else {
            // Acessar o campo "status"
            return $responseArray['id'];
        }
    }

    /*
     * Envia a menssagem para o Thread
     * Passo 02
     * adicionar uma mensagem a um tópico
     */

    public function send_msg_openai($THREAD_ID, $Message)
    {

        // URL da API do OpenAI
        $url = "https://api.openai.com/v1/threads/{$THREAD_ID}/messages";


        // Cabeçalhos da requisição
        $data = array(
            "role" => "user",
            "content" => $Message
        );

        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer $this->tokenOpenai",
            "OpenAI-Beta: assistants=v2"
        );

        // Dados da requisição


        // Configuração da requisição cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Executa a requisição e obtém a resposta
        $response = curl_exec($ch);

        // Verifica por erros
        if (curl_errno($ch)) {
            return 'Erro na requisição cURL: ' . curl_error($ch);
            //return false;
        }

        // Fecha a sessão cURL
        curl_close($ch);

        return true;
    }

    /*
     * Passo 03
     * execute o assistante
     */

    public function runsAssistOpenai($THREAD_ID, $assistant_token = 0)
    {

        // URL da API do OpenAI
        $url = "https://api.openai.com/v1/threads/{$THREAD_ID}/runs";

        // Cabeçalhos da requisição
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer $this->tokenOpenai",
            "OpenAI-Beta: assistants=v2"
        );

        // Dados da requisição
        $data = array(
            "assistant_id" => $assistant_token,
            "instructions" => "",
        );

        // Configuração da requisição cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Executa a requisição e obtém a resposta
        $response = curl_exec($ch);

        // Verifica por erros
        if (curl_errno($ch)) {
            echo 'Erro na requisição cURL: ' . curl_error($ch);
        }

        // Fecha a sessão cURL
        curl_close($ch);

        $responseArray = json_decode($response, true);
        // Verificar se a decodificação foi bem-sucedida
        return $responseArray['id'];
    }

    /** Passo 04
     * Verifica se a resposta esta pronta 
     * @param type $RunId
     * @param type $THREAD_ID
     * @return type
     */
    public function getStatusMsgOpenai($RunId, $THREAD_ID)
    {

        // URL da API do OpenAI
        $url = "https://api.openai.com/v1/threads/{$THREAD_ID}/runs/{$RunId}";


        // Cabeçalhos da requisição
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer $this->tokenOpenai",
            "OpenAI-Beta: assistants=v2"
        );

        // Configuração da requisição cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Executa a requisição e obtém a resposta
        $response = curl_exec($ch);

        // Verifica por erros
        if (curl_errno($ch)) {
            echo 'Erro na requisição cURL: ' . curl_error($ch);
        }

        // Fecha a sessão cURL
        curl_close($ch);

        // Exibe a resposta
        $responseArray = json_decode($response, true);
        // Verificar se a decodificação foi bem-sucedida
        return $responseArray;
    }

    /** Passo 05
     * Pega a msg 
     * @param type $THREAD_ID
     * @return string
     */
    public function getMessagesOpenai($THREAD_ID)
    {

        // URL da API do OpenAI
        $url = "https://api.openai.com/v1/threads/{$THREAD_ID}/messages";


        // Cabeçalhos da requisição
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer $this->tokenOpenai",
            "OpenAI-Beta: assistants=v2"
        );

        // Configuração da requisição cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Executa a requisição e obtém a resposta
        $response = curl_exec($ch);

        // Verifica por erros
        if (curl_errno($ch)) {
            return 'Erro na requisição cURL: ' . curl_error($ch);
        }

        // Fecha a sessão cURL
        curl_close($ch);

        $responseArray = json_decode($response, true);

        // Verificar se a decodificação foi bem-sucedida
        if ($responseArray === null) {
            return 'Erro ao decodificar JSON';
        } else {
            // Find the first assistant message (messages are returned in reverse chronological order)
            if (isset($responseArray['data']) && is_array($responseArray['data'])) {
                foreach ($responseArray['data'] as $message) {
                    // Check if this message is from the assistant
                    if (isset($message['role']) && $message['role'] === 'assistant') {
                        // Check if message has content
                        if (isset($message['content']) && is_array($message['content'])) {
                            // Loop through content items to find text content
                            foreach ($message['content'] as $contentItem) {
                                if (isset($contentItem['type']) && $contentItem['type'] === 'text' && isset($contentItem['text']['value'])) {
                                    $assistantMessageContent = $contentItem['text']['value'];
                                    return nl2br($assistantMessageContent);
                                }
                            }
                        }
                    }
                }
            }
            
            // If no assistant message found, return error
            return 'Erro: Nenhuma mensagem do assistente encontrada';
        }
    }

    /**
     * Envia os dado da funções para a openai 
     * @param type $THREAD_ID
     * @param type $RunId
     * @param type $call_id
     * @param type $dados
     * @return type
     */
    public function send_function_openai($THREAD_ID, $RunId, $call_id, $dados)
    {

        // URL da API do OpenAI
        $url = "https://api.openai.com/v1/threads/{$THREAD_ID}/runs/{$RunId}/submit_tool_outputs";

        // Cabeçalhos da requisição
        $headers = array(
            "Authorization: Bearer $this->tokenOpenai",
            "Content-Type: application/json",
            "OpenAI-Beta: assistants=v2"
        );

        // Dados da requisição
        $data = array(
            "tool_outputs" => array(
                array(
                    "tool_call_id" => $call_id,
                    "output" => $dados
                )
            )
        );

        // Configuração da requisição cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Executa a requisição e obtém a resposta
        $response = curl_exec($ch);

        // Verifica por erros
        if (curl_errno($ch)) {
            echo 'Erro na requisição cURL: ' . curl_error($ch);
        }

        // Fecha a sessão cURL
        curl_close($ch);

        $responseArray = json_decode($response, true);
        // Verificar se a decodificação foi bem-sucedida
        return $responseArray;
    }
}
