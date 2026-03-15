<?php

class Webhook extends ClientsController
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('contactcenter_model');
    $this->session->sess_destroy();
  }


  public function docs()
  {
    $this->load->view('docs');
  }

  public function index()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');
    $result =  $this->contactcenter_model->insert_webhook($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }

  public function get_device_status()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');
    $result =  $this->contactcenter_model->save_status_device($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }

  public function qrcode()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');
    $result =  $this->contactcenter_model->get_webhook_qrcode($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }

  public function update_module()
  {
    header('Content-Type: application/json');

    // Lê o conteúdo JSON
    $jsonData = file_get_contents('php://input');

    // Decodifica para array associativo
    $data = json_decode($jsonData, true);

    // Agora sim envia para o model
    $response = $this->contactcenter_model->update_module($data);

    echo json_encode($response);
  }

  public function messages_upsert()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');
    //log_activity('messages_upsert: '. $jsonData);
    $result =  $this->contactcenter_model->insert_webhook($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }

  public function messages_delete()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');
    //log_activity('messages_delete: '. $jsonData);
    $result =  $this->contactcenter_model->insert_webhook($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }


  public function messages_set()
  {
    // $jSON = array();
    // header('Content-Type: application/json');
    // $jsonData = file_get_contents('php://input');
    // //log_activity('messages_set: '. $jsonData);
    // if (get_option("contac_settings_sincronizacao_whatsapp_active") == 1) {
    //   $result =  $this->contactcenter_model->insert_historico_webhook($jsonData);
    // }

    // if ($result) {
    //   $jSON["insert"] = true;
    // } else {
    //   $jSON["insert"] = false;
    // }
    // echo json_encode($jSON);
  }

  public function send_message()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');
    //log_activity('send_message: '. $jsonData);
    $result =  $this->contactcenter_model->insert_webhook($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }

  public function qrcode_updated()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');
    //log_activity('qrcode_updated' . $jsonData);
    $result =  $this->contactcenter_model->insert_webhook($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }

  public function connection_update()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');
    //log_activity('connection_update' . $jsonData);
    $result =  $this->contactcenter_model->insert_webhook($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }
  public function contacts_update()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');

    //log_activity('connection_update'. $jsonData);

    $result =  $this->contactcenter_model->insert_webhook($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }

  public function messages_update()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');

    //log_activity('connection_update'. $jsonData);

    $result =  $this->contactcenter_model->insert_webhook($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }


  public function voice()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');
    //log_activity('voice' . $jsonData);
    $result =  $this->voice->getVoice($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }


  public function update_lead()
  {

    $result = $this->contactcenter_model->update_lead_past();

    echo json_encode($result);
  }

  public function testeEnvio()
  {


    $result =  $this->voice->create_call();
    print_r($result);

    echo json_encode($result);
  }


  /**
   * Receives messages from the local webhook
   *
   * @return void
   */
  public function messages_local()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');
    // log_activity('messages_local ' . $jsonData);    
    $result = $this->contactcenter_model->get_webhook_local($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }

  /**
   * Receives messages from the local webhook and updates the status
   */
  public function messages_local_status()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');
   // log_activity('messages_local_status ' . $jsonData);
    $result = $this->contactcenter_model->update_webhook_local_status($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }

  /**
   * recebe as verificações de whats do local
   *
   * @return void
   */
  public function local_verify_whats()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');
    // log_activity('local_verify_whats ' . $jsonData);    
    $result = $this->contactcenter_model->local_verify_whats($jsonData);
    if ($result) {
      $jSON["insert"] = true;
    } else {
      $jSON["insert"] = false;
    }
    echo json_encode($jSON);
  }

  /**
   * Receives logs from the local webhook
   *
   * @return void
   */
  public function messages_logs()
  {
    $jSON = array();
    header('Content-Type: application/json');
    $jsonData = file_get_contents('php://input');
    log_activity('log_api_local ' . $jsonData);
    $jSON["insert"] = true;
    echo json_encode($jSON);
  }
}
