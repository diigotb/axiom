<?php
class Chat extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('contactcenter_model');
        $this->load->library('chatweb');
        $this->load->driver('cache', array('adapter' => 'file')); // Carregar a biblioteca de cache com o driver de arquivos
        
    }

    public function axiom($token)
    {
        $data["chatweb"] = $this->chatweb->get_chatweb_token($token);
        if (!$data["chatweb"]) {
            die("chat Block");
        }
        
        $timestamp = time()."_".mt_rand(100000, 999999);
        $token_pusher = array(
            'name'   => "chat_web_cache_token",
            'value'  => $timestamp,
            'expire' => 86400,
            'path'   => '/',
        );

        set_cookie($token_pusher);             
         
        $cache = get_cookie("chat_web", TRUE);   
        $response_array = json_decode($cache, true); 
        
        $data["lead"] = $response_array["lead"];
        $data["chat"] = $response_array["chat"];
        $data["cache_token"] = $timestamp;
    
        //print_r($data["chat"]);   
        $data["hmtl_chat"] = $this->chatweb->get_msg( $data["lead"]["id"]);
        $this->load->view('chat_web/chat_web', $data);
    }

    public function start_chat()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                $token = $data["cache_token"];
                unset($data["cache_token"]);
                $response = $this->chatweb->start_chat($data);
                if ($response) {
                    $response_json = json_encode($response);
                    // Armazenar o valor no cache
                    $token_pusher = array(
                        'name'   => "chat_web",
                        'value'  => $response_json,
                        'expire' => 86400,
                        'path'   => '/',
                    );
            
                    set_cookie($token_pusher);

                    //$this->cache->save('chat_web', $response, 300); // 30 dias
                    $jSON["lead"] = $response;

                    $CrearMsg = [                        
                        "text" => "Olá {$data["name"]}! Como posso te ajudar hoje em relação aos nossos serviços da AXIOM ou em agendar uma reunião para explicar mais detalhes? 😊",                        
                        "fromMe" => 1,
                        "cache_token" =>  $token,                        
                    ];
                    
                    $this->chatweb->send_pusher_chat($CrearMsg);

                }
            } else {
                show_404();
            }
        }
        echo json_encode($jSON);
    }


    public function send_msg()
    {
        $jSON = array();
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $data = $this->input->post();
                               
                 // print_r($data);
                $response = $this->chatweb->send_msg($data);
                if ($response) {
                    $jSON["lead"] = $response;
                }
            } else {
                show_404();
            }
        }
        echo json_encode($jSON);
    }
}
