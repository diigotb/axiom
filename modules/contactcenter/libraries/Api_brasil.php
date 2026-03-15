<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Api_brasil
{

    private $UrlApi;
    private $token;
     
    public function __construct()
    {
        $this->UrlApi = "https://gateway.apibrasil.io/api/v2/";
        $this->token = get_option("tokenBearer_contactcenter");
    }

    public function send($data, $prefixUrl, $tokenDevice)
    {

        $ApiUrl = $this->UrlApi . $prefixUrl;

        $json_data = json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $ApiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token . '',
                'DeviceToken: ' . $tokenDevice . ' '
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    public function get_media_evolution_base64($key_id, $tokenDevice, $video = false)
    {
        $curl = curl_init();

        $data = array(
            "message" => array(
                "key" => array(
                    "id" => $key_id
                )
            ),
            "convertToMp4" => $video
        );

        $data_json = json_encode($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://cluster.apigratis.com/api/v2/evolution/chat/getBase64FromMediaMessage',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data_json,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                "DeviceToken: {$tokenDevice}",
                'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2FwcC5hcGlicmFzaWwuaW8vYXV0aC9yZWdpc3RlciIsImlhdCI6MTcxMDQ0MjQyMywiZXhwIjoxNzQxOTc4NDIzLCJuYmYiOjE3MTA0NDI0MjMsImp0aSI6IkZQaDR4WDFWZXk3WFNmOHMiLCJzdWIiOiI3OTI5IiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.pgVHrqUfop2ZW3sK0AoDiitmuyVr4qQ_LUMWV3l-Y70'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

    /**
     * Da start para a conexão com a api, tem quer ser chamado toda vez que for gerado QrCode
     * @param type $tokenDevice
     * @return type
     */
    public function start_device($tokenDevice)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://gateway.apibrasil.io/api/v2/whatsapp/start',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer {$this->token}",
                "DeviceToken: {$tokenDevice}"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    /**
     * busca o qrcode para vincular o device
     * @param type $tokenDevice
     * @return type
     */
    public function get_qrcode($tokenDevice)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->UrlApi . 'whatsapp/qrcode',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "device_password": "123456"
            }',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer {$this->token}",
                "DeviceToken: {$tokenDevice}"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true);
    }

    /**
     * Faz a intance com api evolution
     * @param type $token
     * @param type $deviceName
     * @param type $phoneNumber
     */
    public function start_isntance_evolution($tokenDevice, $deviceName, $phoneNumber)
    {


        $curl = curl_init();

        $data = array(
            "number" => $phoneNumber,
            "instanceName" => $deviceName,
            "qrcode" => true
        );
        $data_string = json_encode($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://cluster.apigratis.com/api/v2/evolution/instance/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer {$this->token}",
                "DeviceToken: {$tokenDevice}"
            ),
        ));

        $response = curl_exec($curl);
        $start_array = json_decode($response, true);
        curl_close($curl);
        return $start_array;
    }

    public function connect_isntance_evolution($tokenDevice)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://cluster.apigratis.com/api/v2/evolution/instance/connect',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer {$this->token}",
                "DeviceToken: {$tokenDevice}"
            ),
        ));

        $response = curl_exec($curl);
        $start_array = json_decode($response, true);
        curl_close($curl);
        return $start_array;
    }

    public function restart_isntance_evolution($tokenDevice)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://cluster.apigratis.com/api/v2/evolution/instance/restart',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer {$this->token}",
                "DeviceToken: {$tokenDevice}"
            ),
        ));

        $response = curl_exec($curl);
        $start_array = json_decode($response, true);
        curl_close($curl);
        return $start_array;
    }

    public function verify_Number_Whatsapp($prefix, $tokenDevice, $phoneNumber)
    {

        $ApiUrl = $this->UrlApi . $prefix;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $ApiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($phoneNumber),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer {$this->token}",
                "DeviceToken: {$tokenDevice}"
            ),
        ));

        $response = curl_exec($curl);
        $result = json_decode($response, true);
        curl_close($curl);
        if ($result["response"]["profile"]["numberExists"]) {
            return true;
        } elseif ($result["response"][0]["exists"]) {
            return true;
        } else {
            return false;
        }
    }

    public function getConnectionStatus($ApiUrl, $tokenDevice, $TYPE)
    {
        $curl = curl_init();       

        curl_setopt_array($curl, array(
            CURLOPT_URL => $ApiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $TYPE,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer {$this->token}",
                "DeviceToken: {$tokenDevice}"
            ),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }
}
