
<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Active extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('contactcenter_model');
    }


    public function activate_key()
    {


        // busco ip do servidor
        $ip = gethostbyname(gethostname());

        // busco o host do servidor
        $host = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
        $host .= "://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/';
     

        // busco o parametro purchase_key
        $purchase_key = $this->input->post('purchase_key');

        $data = [
            'key' => $purchase_key,
            'ip' => $ip,
            'url' => $host
        ];
       
        // chamo método para validar o plano
        $response = $this->contactcenter_model->active_module($data);

        // retorno a resposta
        if ($response['error'] == false) {
            $res['status'] = true;
            $res['message'] = $response['message'];
            $res['original_url'] = admin_url('modules/activate/contactcenter');
            update_option('contactcenter_verification_id', '123456');
            echo json_encode($res);
            return;
        }

        $res['status'] = false;
        $res['message'] = $response['message'];
        echo json_encode($res);
    }
}
