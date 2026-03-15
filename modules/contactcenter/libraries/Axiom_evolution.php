<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Axiom_evolution
{
    public function __construct() {}

    public function send($data, $url, $tokenDevice)
    {

        $ApiUrl = $url;

        $json_data = json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $ApiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'ApiKey: ' . $tokenDevice . ' '
            ),
        ));

        $response = curl_exec($curl);
        $curlError = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Para logar erros de cURL ou status HTTP
        if ($curlError) {
            log_activity("cURL Error Axiom_evolution->send(): {$curlError} URL: {$ApiUrl} Data: {$json_data}");
        }

        if ($httpCode >= 400) {
            log_activity("HTTP Error Code: {$httpCode} Response: {$response} url: {$ApiUrl} Data: {$json_data}");
            // Return error response so caller can check for errors
            $decoded = json_decode($response, true);
            if ($decoded) {
                $decoded['http_code'] = $httpCode; // Add HTTP code to response
            } else {
                $decoded = ['error' => 'HTTP Error', 'http_code' => $httpCode, 'response' => $response];
            }
            return $decoded;
        }

        return json_decode($response, true);
    }

    public function get_media_evolution_base64($url, $key_id, $tokenDevice, $video = false)
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
            CURLOPT_URL => $url,
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
                "ApiKey: {$tokenDevice}"
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
    public function start_isntance_evolution($url, $tokenDevice, $deviceName)
    {

        $curl = curl_init();

        $data = array(
            "token" => $tokenDevice,
            "instanceName" => $deviceName,
            "qrcode" => true
        );
        $data_string = json_encode($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
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
                "ApiKey: {$tokenDevice}"
            ),
        ));

        $response = curl_exec($curl);
        $start_array = json_decode($response, true);
        curl_close($curl);
        return $start_array;
    }

    /**
     * Após dar start vc faz o connect
     * @param type $tokenDevice
     * @return type
     */
    public function connect_isntance_evolution($url, $tokenDevice)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "ApiKey: {$tokenDevice}"
            ),
        ));

        $response = curl_exec($curl);
        $start_array = json_decode($response, true);
        curl_close($curl);
        return $start_array;
    }

    public function verify_Number_Whatsapp($ApiUrl, $tokenDevice, $phoneNumber)
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
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($phoneNumber),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "ApiKey: {$tokenDevice}"
            ),
        ));

        $response = curl_exec($curl);
        $start_array = json_decode($response, true);

        curl_close($curl);

        if ($start_array[0]['exists'] == false) {
            return false;
        }

        return true;
    }

    public function getConnectionStatus($ApiUrl, $tokenDevice)
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
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "ApiKey: {$tokenDevice}"
            ),
        ));

        $response = curl_exec($curl);
        $start_array = json_decode($response, true);
        curl_close($curl);
        return $start_array;
    }



    public function restart_instance_evolution($url, $tokenDevice)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_HTTPHEADER => [
                "apikey: $tokenDevice"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            log_activity("cURL Error #:" . $err);
        } else {
            $result = json_encode($response);
            log_activity("Response restart qrcode:" . $result);
        }
    }


    public function reload_instance_evolution($url, $apikeyDevice,$method)
    {       

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                "apikey: $apikeyDevice"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            log_activity("cURL Error #:" . $err);
        } 
        $result = json_decode($response);           
        return $result;
    }


    public function logout($url, $tokenDevice)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => [
                "apikey: $tokenDevice"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);



        curl_close($curl);
        return json_decode($response, true);
    }

    public function create_instance_v1($server, $url, $data)
    {

        $curl = curl_init();

        $request = [
            "instanceName" => $data['dev_instance_name'],
            "token" => $data['dev_token'],
            "qrcode" => true,
            "reject_call" => true,
            "integration" => "WHATSAPP-BAILEYS",
            "msg_call" => get_option('whatsapp_msg_call'),
            "groups_ignore" => true,
            "always_online" => false,
            "read_messages" => false,
            "read_status" => false,
            "webhook" => $url . "contactcenter/webhook",
            "webhook_by_events" => true,
            "webhook_base64" => true,
            "events" => [
                "QRCODE_UPDATED",
                "MESSAGES_UPSERT",
                "SEND_MESSAGE",
                "CONNECTION_UPDATE",
                "MESSAGES_UPDATE",
                "CONTACTS_UPDATE",
                "MESSAGES_DELETE"
            ],
            "websocket_enabled" => true,
            "websocket_events" => [
                "MESSAGES_UPSERT",
                "SEND_MESSAGE",
                "CONNECTION_UPDATE",
                "MESSAGES_UPDATE",
                "QRCODE_UPDATED",
                "MESSAGES_DELETE"
            ]


        ];

        $data_string = json_encode($request);

        curl_setopt_array($curl, [
            CURLOPT_URL => $server->url . "instance/create",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "apikey: {$server->value_key}"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            log_activity("cURL Error #:" . $err);
        } else {
            $result = json_decode($response, true);

            if ($result['instance'] != null) {
                log_activity("Instance {$data['dev_instance_name']} created in server {$server->name}");
            } else {
                log_activity("Error creating instance {$data['dev_instance_name']} in server {$server->name}");
            }
        }
    }

    public function create_instance_v2($server, $url, $data)
    {
        $curl = curl_init();

        $request = [
            "instanceName" => $data['dev_instance_name'],
            "token" => $data['dev_token'],
            "qrcode" => true,
            "integration" => "WHATSAPP-BAILEYS",
            "rejectCall" => true,
            "msgCall" =>  get_option('whatsapp_msg_call'),
            "groupsIgnore" => false,
            "alwaysOnline" => false,
            "readMessages" => false,
            "readStatus" => false,
            "syncFullHistory" => true,
            "webhook" => [
                "url" => $url . "contactcenter/webhook",
                "byEvents" => true,
                "base64" => true,
                "enabled" => true,
                "events" => [
                    "QRCODE_UPDATED",
                    "CONTACTS_UPDATE",
                    "MESSAGES_UPSERT",
                    "MESSAGES_UPDATE",
                    "SEND_MESSAGE",
                    "MESSAGES_SET",
                    "CONNECTION_UPDATE",
                    "MESSAGES_DELETE"
                ]
            ],
            "websocket" => [
                "enabled" => true,
                "events" => [
                    "MESSAGES_UPSERT",
                    "SEND_MESSAGE",
                    "CONNECTION_UPDATE",
                    "MESSAGES_UPDATE",
                    "QRCODE_UPDATED",
                    "MESSAGES_DELETE"
                ]
            ],
        ];

        $data_string = json_encode($request);

        curl_setopt_array($curl, [
            CURLOPT_URL => $server->url . "instance/create",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "apikey: {$server->value_key}"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            log_activity("cURL Error #:" . $err);
        } else {
            $result = json_decode($response, true);

            if ($result['instance'] != null) {
                log_activity("Instance {$data['dev_instance_name']} created in server {$server->name}");
            } else {
                log_activity("Error create instance {$data['dev_instance_name']} in server {$server->name}");
            }
        }
    }


    public function delete_instance($server, $url, $instance_name)
    {
        $curl = curl_init();

        $url = $server->url . "instance/delete/" . $instance_name;

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => [
                "apikey: {$server->value_key}"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            log_activity("cURL Error #:" . $err);
        } else {
            $result = json_decode($response, true);
            if ($result['status'] == 'SUCCESS') {
                log_activity("Instance {$instance_name} deleted in server {$server->name}");
            } else {
                log_activity("Error deleting instance {$instance_name} in server {$server->name}");
            }
        }
    }

    public function update_web_socket_v1($server, $instance)
    {
        $curl = curl_init();

        $request = [
            "enabled" => true,
            "events" => [
                "MESSAGES_UPSERT",
                "SEND_MESSAGE",
                "CONNECTION_UPDATE",
                "MESSAGES_UPDATE",
                "QRCODE_UPDATED",
                "MESSAGES_DELETE"
            ]
        ];

        $data_string = json_encode($request);

        curl_setopt_array($curl, [
            CURLOPT_URL => $server->url . "websocket/set/" . $instance,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "apikey: {$server->value_key}"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            log_activity("cURL Error #:" . $err);
        } else {
            $result = json_decode($response, true);
        }
    }

    public function update_webhook_v1($server, $instance)
    {
        $curl = curl_init();

        $request = [
            "enabled" => true,
            "url" => site_url("contactcenter/webhook"),
            "webhook_by_events" => true,
            "webhook_base64" => true,
            "events" => [
                "MESSAGES_UPSERT",
                "SEND_MESSAGE",
                "CONNECTION_UPDATE",
                "CONTACTS_UPDATE",
                "MESSAGES_UPDATE",
                "QRCODE_UPDATED",
                "MESSAGES_DELETE"
            ]
        ];

        $data_string = json_encode($request);

        curl_setopt_array($curl, [
            CURLOPT_URL => $server->url . "webhook/set/" . $instance,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "apikey: {$server->value_key}"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            log_activity("cURL Error #:" . $err);
        } else {
            $result = json_decode($response, true);
        }
    }

    public function update_web_socket_v2($server, $instance)
    {
        $curl = curl_init();

        $request = [
            "websocket" => [
                "enabled" => true,
                "events" => [
                    "MESSAGES_UPSERT",
                    "SEND_MESSAGE",
                    "CONNECTION_UPDATE",
                    "MESSAGES_UPDATE",
                    "QRCODE_UPDATED",
                    "MESSAGES_DELETE"
                ]
            ],
        ];

        $data_string = json_encode($request);

        curl_setopt_array($curl, [
            CURLOPT_URL => $server->url . "websocket/set/" . $instance,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "apikey: {$server->value_key}"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            log_activity("cURL Error #:" . $err);
        } else {
            $result = json_decode($response, true);
        }
    }

    public function update_webhook_v2($server, $instance)
    {
        $curl = curl_init();

        $request = [
            "webhook" => [
                "byEvents" => true,
                "base64" => true,
                "enabled" => true,
                "url" => site_url("contactcenter/webhook"),
                "events" => [
                    "MESSAGES_UPSERT",
                    "SEND_MESSAGE",
                    "CONNECTION_UPDATE",
                    "CONTACTS_UPDATE",
                    "MESSAGES_UPDATE",
                    "MESSAGES_SET",
                    "QRCODE_UPDATED",
                    "MESSAGES_DELETE"
                ]
            ],
        ];

        $data_string = json_encode($request);

        curl_setopt_array($curl, [
            CURLOPT_URL => $server->url . "webhook/set/" . $instance,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "apikey: {$server->value_key}"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            log_activity("cURL Error #:" . $err);
        } else {
            $result = json_decode($response, true);
        }
    }

    /**
     * Create a group using Evolution API
     * @param string $url Server URL
     * @param string $instanceName Instance name
     * @param string $tokenDevice API Key
     * @param string $groupName Group name
     * @param array $participants Array of phone numbers (with country code, e.g., ["5511999999999", "5522888888888"])
     * @return array Response from Evolution API
     */
    public function create_group_evolution($url, $instanceName, $tokenDevice, $groupName, $participants)
    {
        $curl = curl_init();

        // Ensure participants is an array
        if (is_string($participants)) {
            // If it's a comma-separated string, convert to array
            $participants = array_map('trim', explode(',', $participants));
        }

        $data = [
            "subject" => $groupName,
            "participants" => $participants
        ];

        $data_string = json_encode($data);

        curl_setopt_array($curl, [
            CURLOPT_URL => $url . "group/create/" . $instanceName,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "apikey: {$tokenDevice}"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err) {
            log_activity("cURL Error create_group_evolution: {$err}");
            return ['error' => $err, 'status' => 'error'];
        }

        $result = json_decode($response, true);
        
        // HTTP 201 (Created) and 200 (OK) are both success codes
        if ($httpCode >= 200 && $httpCode < 300) {
            log_activity("create_group_evolution SUCCESS - HTTP Code: {$httpCode}, Response: " . json_encode($result));
        } else {
            log_activity("HTTP Error create_group_evolution: Code {$httpCode}, Response: {$response}");
        }

        return $result;
    }

    /**
     * Set group description using Evolution API
     * @param string $url Server URL
     * @param string $instanceName Instance name
     * @param string $tokenDevice API Key
     * @param string $groupId Group ID (JID format)
     * @param string $description Group description
     * @return array Response from Evolution API
     */
    public function set_group_description_evolution($url, $instanceName, $tokenDevice, $groupId, $description)
    {
        $curl = curl_init();

        $data = [
            "groupId" => $groupId,
            "description" => $description
        ];

        $data_string = json_encode($data);

        // Evolution API endpoint for setting group description
        curl_setopt_array($curl, [
            CURLOPT_URL => $url . "group/updateDescription/" . $instanceName,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "apikey: {$tokenDevice}"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err) {
            log_activity("cURL Error set_group_description_evolution: {$err}");
            return ['error' => $err, 'status' => 'error'];
        }

        $result = json_decode($response, true);
        
        if ($httpCode != 200) {
            log_activity("HTTP Error set_group_description_evolution: Code {$httpCode}, Response: {$response}");
        }

        return $result;
    }

    /**
     * Generate group invite link using Evolution API
     * @param string $url Server URL
     * @param string $instanceName Instance name
     * @param string $tokenDevice API Key
     * @param string $groupId Group ID (JID format)
     * @return array Response containing invite link
     */
    public function generate_group_invite_link_evolution($url, $instanceName, $tokenDevice, $groupId)
    {
        $data = [
            "groupId" => $groupId
        ];
        $data_string = json_encode($data);

        // Try multiple endpoints and methods
        // Priority: Use groupJid query parameter first since API error message suggested it
        $attempts = [
            [
                'method' => 'GET',
                'url' => $url . "group/inviteCode/" . $instanceName . "?groupJid=" . urlencode($groupId),
                'data' => null,
                'description' => 'GET /group/inviteCode/{instance}?groupJid={groupId}'
            ],
            [
                'method' => 'POST',
                'url' => $url . "group/inviteCode/" . $instanceName,
                'data' => json_encode(["groupJid" => $groupId]),
                'description' => 'POST /group/inviteCode/{instance} with groupJid in body'
            ],
            [
                'method' => 'POST',
                'url' => $url . "group/groupInviteCode/" . $instanceName,
                'data' => json_encode(["groupJid" => $groupId]),
                'description' => 'POST /group/groupInviteCode/{instance} with groupJid in body'
            ],
            [
                'method' => 'GET',
                'url' => $url . "group/inviteCode/" . $instanceName . "?groupId=" . urlencode($groupId),
                'data' => null,
                'description' => 'GET /group/inviteCode/{instance}?groupId={groupId}'
            ],
            [
                'method' => 'GET',
                'url' => $url . "group/groupInviteCode/" . $instanceName . "?groupId=" . urlencode($groupId),
                'data' => null,
                'description' => 'GET /group/groupInviteCode/{instance}?groupId={groupId}'
            ],
            [
                'method' => 'POST',
                'url' => $url . "group/inviteCode/" . $instanceName,
                'data' => $data_string,
                'description' => 'POST /group/inviteCode/{instance} with groupId in body'
            ],
            [
                'method' => 'POST',
                'url' => $url . "group/groupInviteCode/" . $instanceName,
                'data' => $data_string,
                'description' => 'POST /group/groupInviteCode/{instance} with groupId in body'
            ]
        ];

        foreach ($attempts as $index => $attempt) {
            $curl = curl_init();
            
            log_activity("generate_group_invite_link_evolution ATTEMPT " . ($index + 1) . " - {$attempt['description']}, Group ID: {$groupId}", get_staff_user_id());
            
            $headers = [
                "apikey: {$tokenDevice}"
            ];
            
            // Add Content-Type header if we're sending data
            if ($attempt['data'] !== null && ($attempt['method'] == 'POST' || $attempt['method'] == 'PUT')) {
                $headers[] = "Content-Type: application/json";
            }
            
            curl_setopt_array($curl, [
                CURLOPT_URL => $attempt['url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $attempt['method'],
                CURLOPT_HTTPHEADER => $headers,
            ]);
            
            if ($attempt['data'] !== null) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $attempt['data']);
            }
            
            $response = curl_exec($curl);
            $err = curl_error($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($err) {
                log_activity("generate_group_invite_link_evolution cURL ERROR (attempt " . ($index + 1) . "): {$err}", get_staff_user_id());
                continue;
            }
            
            $result = json_decode($response, true);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                log_activity("generate_group_invite_link_evolution SUCCESS (attempt " . ($index + 1) . ") - HTTP Code: {$httpCode}, Response: " . json_encode($result), get_staff_user_id());
                return $result;
            } else {
                log_activity("generate_group_invite_link_evolution HTTP ERROR (attempt " . ($index + 1) . "): Code {$httpCode}, Response: {$response}", get_staff_user_id());
            }
        }
        
        // If all attempts failed, return error
        log_activity("generate_group_invite_link_evolution ALL ATTEMPTS FAILED - Could not find valid endpoint for invite link generation. Group ID: {$groupId}", get_staff_user_id());
        return ['error' => 'Invite link endpoint not available', 'status' => 'error', 'http_code' => 404];
    }

    /**
     * Set group picture using Evolution API
     * @param string $url Server URL
     * @param string $instanceName Instance name
     * @param string $tokenDevice API Key
     * @param string $groupId Group ID (JID format)
     * @param string $imagePath Full path to the image file
     * @return array Response from Evolution API
     */
    /**
     * Get group information from Evolution API
     * @param string $url Server URL
     * @param string $instanceName Instance name
     * @param string $tokenDevice API Key
     * @param string $groupId Group ID (JID format)
     * @return array Response from Evolution API containing group info (subject, participants, etc.)
     */
    public function get_group_info_evolution($url, $instanceName, $tokenDevice, $groupId)
    {
        $curl = curl_init();

        // Try multiple endpoint variations based on Evolution API patterns
        $endpoints = [
            [
                'url' => $url . "group/fetchAllGroups/{$instanceName}?getParticipants=true",
                'method' => 'GET',
                'data' => null,
                'desc' => "GET /group/fetchAllGroups/{instance}?getParticipants=true (then filter by groupId)"
            ],
            [
                'url' => $url . "group/fetchAllGroups/{$instanceName}?getParticipants=false",
                'method' => 'GET',
                'data' => null,
                'desc' => "GET /group/fetchAllGroups/{instance}?getParticipants=false (then filter by groupId)"
            ],
            [
                'url' => $url . "group/fetchAllGroups/{$instanceName}",
                'method' => 'GET',
                'data' => null,
                'desc' => "GET /group/fetchAllGroups/{instance} (then filter by groupId)"
            ],
            [
                'url' => $url . "group/{$instanceName}",
                'method' => 'GET',
                'data' => null,
                'params' => ['groupJid' => $groupId],
                'desc' => "GET /group/{instance}?groupJid={groupId}"
            ],
        ];

        foreach ($endpoints as $index => $attempt) {
            $requestUrl = $attempt['url'];
            
            curl_setopt_array($curl, [
                CURLOPT_URL => $requestUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30, // Increased timeout for fetchAllGroups which can be slow
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $attempt['method'],
                CURLOPT_HTTPHEADER => [
                    "apikey: {$tokenDevice}",
                    "Content-Type: application/json"
                ],
            ]);
            
            if ($attempt['data'] !== null) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $attempt['data']);
            }

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $err = curl_error($curl);

            if (!$err && $httpCode >= 200 && $httpCode < 300) {
                $result = json_decode($response, true);
                
                // Check if response is a direct array (fetchAllGroups returns array directly)
                $groupsArray = null;
                if (is_array($result) && isset($result[0]) && is_array($result[0])) {
                    // Response is a direct array of groups
                    $groupsArray = $result;
                } elseif (isset($result['groups']) && is_array($result['groups'])) {
                    // Response has groups key
                    $groupsArray = $result['groups'];
                }
                
                if ($groupsArray) {
                    log_activity("get_group_info_evolution FOUND " . count($groupsArray) . " GROUPS - Searching for Group ID: {$groupId}", get_staff_user_id());
                    
                    // Normalize group ID for comparison (remove @g.us if present, or add it)
                    $groupIdWithSuffix = strpos($groupId, '@g.us') !== false ? $groupId : $groupId . '@g.us';
                    $groupIdWithoutSuffix = str_replace('@g.us', '', $groupId);
                    
                    foreach ($groupsArray as $group) {
                        // Check multiple possible ID fields and formats
                        $groupMatchId = null;
                        $groupApiId = null;
                        
                        if (isset($group['id'])) {
                            $groupApiId = $group['id'];
                            // Match with or without @g.us suffix
                            if ($groupApiId == $groupId || $groupApiId == $groupIdWithSuffix || $groupApiId == $groupIdWithoutSuffix) {
                                $groupMatchId = $groupApiId;
                            }
                        }
                        
                        if (!$groupMatchId && isset($group['groupId'])) {
                            $groupApiId = $group['groupId'];
                            if ($groupApiId == $groupId || $groupApiId == $groupIdWithSuffix || $groupApiId == $groupIdWithoutSuffix) {
                                $groupMatchId = $groupApiId;
                            }
                        }
                        
                        if (!$groupMatchId && isset($group['groupJid'])) {
                            $groupApiId = $group['groupJid'];
                            if ($groupApiId == $groupId || $groupApiId == $groupIdWithSuffix || $groupApiId == $groupIdWithoutSuffix) {
                                $groupMatchId = $groupApiId;
                            }
                        }
                        
                        if ($groupMatchId) {
                            $foundName = isset($group['subject']) ? $group['subject'] : (isset($group['name']) ? $group['name'] : 'N/A');
                            curl_close($curl);
                            log_activity("get_group_info_evolution SUCCESS (attempt " . ($index + 1) . ") - HTTP Code: {$httpCode}, Endpoint: {$attempt['desc']}, Group ID: {$groupId}, Matched ID: {$groupMatchId}, Name: {$foundName}", get_staff_user_id());
                            return $group;
                        }
                    }
                    
                    // Log first few groups for debugging
                    $sampleGroups = array_slice($groupsArray, 0, 3);
                    log_activity("get_group_info_evolution SAMPLE GROUPS - First 3 groups IDs: " . json_encode(array_map(function($g) { return $g['id'] ?? 'no-id'; }, $sampleGroups)), get_staff_user_id());
                } else {
                    // Direct group info response (single group)
                    curl_close($curl);
                    log_activity("get_group_info_evolution SUCCESS (attempt " . ($index + 1) . ") - HTTP Code: {$httpCode}, Endpoint: {$attempt['desc']}, Group ID: {$groupId}, Response: " . json_encode($result), get_staff_user_id());
                    return $result;
                }
            } else {
                log_activity("get_group_info_evolution ATTEMPT " . ($index + 1) . " FAILED - HTTP Code: {$httpCode}, Endpoint: {$attempt['desc']}, Error: {$err}, Response: " . substr($response, 0, 200), get_staff_user_id());
            }
        }

        curl_close($curl);
        log_activity("get_group_info_evolution ALL ATTEMPTS FAILED - Group ID: {$groupId}, Last HTTP Code: {$httpCode}, Error: {$err}");
        return ['error' => 'Could not fetch group info', 'status' => 'error', 'http_code' => $httpCode];
    }

    public function set_group_picture_evolution($url, $instanceName, $tokenDevice, $groupId, $imagePath)
    {
        if (!file_exists($imagePath)) {
            log_activity("set_group_picture_evolution ERROR - Image file not found: {$imagePath}");
            return ['error' => 'Image file not found', 'status' => 'error'];
        }

        // Get image mime type
        $mimeType = mime_content_type($imagePath);
        if (!in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png'])) {
            log_activity("set_group_picture_evolution ERROR - Invalid image type: {$mimeType}. Only JPEG and PNG are allowed.");
            return ['error' => 'Invalid image type. Only JPEG and PNG are allowed.', 'status' => 'error'];
        }

        // Evolution API documentation: 
        // https://doc.evolution-api.com/v1/api-reference/group-controller/update-group-picture
        // Postman collection: https://www.postman.com/agenciadgcode/evolution-api/request/3fw1y3h/update-group-picture
        // Endpoint: PUT /group/updateGroupPicture/{instance}?groupJid={groupId}
        // Uses multipart/form-data with file upload, groupJid as query parameter
        
        $curl = curl_init();
        
        // Use CURLFile for file upload (PHP 5.5+)
        if (class_exists('CURLFile')) {
            $cfile = new CURLFile($imagePath, $mimeType, basename($imagePath));
        } else {
            // Fallback for older PHP versions
            $cfile = '@' . $imagePath;
        }
        
        // According to Postman collection and docs, groupJid should be a query parameter
        // and file should be in the multipart/form-data body
        $apiUrl = $url . "group/updateGroupPicture/" . $instanceName . "?groupJid=" . urlencode($groupId);
        $postData = [
            'file' => $cfile
        ];
        
        log_activity("set_group_picture_evolution ATTEMPT 1 - Using PUT method with groupJid as query param, endpoint: group/updateGroupPicture, groupJid: {$groupId}, File: {$imagePath}");
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                "apikey: {$tokenDevice}"
                // Don't set Content-Type for multipart/form-data, let cURL set it automatically
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err) {
            log_activity("cURL Error set_group_picture_evolution: {$err}");
            return ['error' => $err, 'status' => 'error'];
        }

        $result = json_decode($response, true);
        
        // HTTP 200 (OK) and 201 (Created) are both success codes
        if ($httpCode >= 200 && $httpCode < 300) {
            log_activity("set_group_picture_evolution SUCCESS - HTTP Code: {$httpCode}, Group ID: {$groupId}");
            return $result;
        } else {
            log_activity("HTTP Error set_group_picture_evolution (attempt 1 - PUT with query param): Code {$httpCode}, Response: {$response}, Group ID: {$groupId}");
            
            // Try with groupJid in body instead of query parameter
            if ($httpCode == 404 || $httpCode == 400) {
                log_activity("set_group_picture_evolution ATTEMPT 2 - Trying with groupJid in body");
                
                $apiUrl2 = $url . "group/updateGroupPicture/" . $instanceName;
                $postData2 = [
                    'file' => $cfile,
                    'groupJid' => $groupId
                ];
                
                $curl2 = curl_init();
                curl_setopt_array($curl2, [
                    CURLOPT_URL => $apiUrl2,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 60,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "PUT",
                    CURLOPT_POSTFIELDS => $postData2,
                    CURLOPT_HTTPHEADER => [
                        "apikey: {$tokenDevice}"
                    ],
                ]);
                
                $response2 = curl_exec($curl2);
                $err2 = curl_error($curl2);
                $httpCode2 = curl_getinfo($curl2, CURLINFO_HTTP_CODE);
                curl_close($curl2);
                
                if ($httpCode2 >= 200 && $httpCode2 < 300) {
                    $result = json_decode($response2, true);
                    log_activity("set_group_picture_evolution SUCCESS (attempt 2 - groupJid in body) - HTTP Code: {$httpCode2}, Group ID: {$groupId}");
                    return $result;
                } else {
                    log_activity("HTTP Error set_group_picture_evolution (attempt 2): Code {$httpCode2}, Response: {$response2}");
                }
            }
            
            // Fallback: Try POST method with base64 image in JSON (API expects "image" property)
            if ($httpCode == 404) {
                log_activity("set_group_picture_evolution ATTEMPT 3 - Trying POST method with base64 image in JSON format");
                
                // Read image and convert to base64
                $imageData = file_get_contents($imagePath);
                $base64Image = base64_encode($imageData);
                
                // API expects "image" property (not "file") and groupJid
                $data = [
                    "groupJid" => $groupId,
                    "image" => $base64Image
                ];
                
                $data_string = json_encode($data);
                
                $apiUrl3 = $url . "group/updateGroupPicture/" . $instanceName;
                $curl3 = curl_init();
                curl_setopt_array($curl3, [
                    CURLOPT_URL => $apiUrl3,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 60,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $data_string,
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json",
                        "apikey: {$tokenDevice}"
                    ],
                ]);
                
                $response3 = curl_exec($curl3);
                $err3 = curl_error($curl3);
                $httpCode3 = curl_getinfo($curl3, CURLINFO_HTTP_CODE);
                curl_close($curl3);
                
                if ($httpCode3 >= 200 && $httpCode3 < 300) {
                    $result = json_decode($response3, true);
                    log_activity("set_group_picture_evolution SUCCESS (attempt 3 - POST with base64 JSON) - HTTP Code: {$httpCode3}, Group ID: {$groupId}");
                    return $result;
                } else {
                    log_activity("set_group_picture_evolution ERROR (attempt 3): Code {$httpCode3}, Response: {$response3}");
                    
                    // Try with data URI format (data:image/jpeg;base64,...)
                    if ($httpCode3 == 400) {
                        log_activity("set_group_picture_evolution ATTEMPT 4 - Trying with data URI format");
                        
                        $dataUri = "data:{$mimeType};base64,{$base64Image}";
                        $data2 = [
                            "groupJid" => $groupId,
                            "image" => $dataUri
                        ];
                        
                        $data_string2 = json_encode($data2);
                        
                        $curl4 = curl_init();
                        curl_setopt_array($curl4, [
                            CURLOPT_URL => $apiUrl3,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 60,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => $data_string2,
                            CURLOPT_HTTPHEADER => [
                                "Content-Type: application/json",
                                "apikey: {$tokenDevice}"
                            ],
                        ]);
                        
                        $response4 = curl_exec($curl4);
                        $err4 = curl_error($curl4);
                        $httpCode4 = curl_getinfo($curl4, CURLINFO_HTTP_CODE);
                        curl_close($curl4);
                        
                        if ($httpCode4 >= 200 && $httpCode4 < 300) {
                            $result = json_decode($response4, true);
                            log_activity("set_group_picture_evolution SUCCESS (attempt 4 - data URI) - HTTP Code: {$httpCode4}, Group ID: {$groupId}");
                            return $result;
                        } else {
                            log_activity("set_group_picture_evolution ERROR (attempt 4): Code {$httpCode4}, Response: {$response4}");
                            return ['error' => 'Group picture endpoint not available in this Evolution API version', 'status' => 'error', 'http_code' => $httpCode4];
                        }
                    } else {
                        return ['error' => 'Group picture endpoint not available in this Evolution API version', 'status' => 'error', 'http_code' => $httpCode3];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Sends a text message to a WhatsApp group via Evolution API
     *
     * @param string $url Server URL
     * @param string $instanceName Instance name
     * @param string $tokenDevice Device token
     * @param string $groupId Group ID (format: group-id@g.us)
     * @param string $message Message text
     * @param int $apiVersion API version (1 or 2, default: try both)
     * @return array API response
     */
    public function send_group_message_evolution($url, $instanceName, $tokenDevice, $groupId, $message, $apiVersion = null)
    {
        $curl = curl_init();
        $apiUrl = $url . "message/sendText/" . $instanceName;
        
        log_activity("send_group_message_evolution START - Group ID: {$groupId}, Instance: {$instanceName}, Message length: " . strlen($message) . " chars, URL: {$apiUrl}, API Version: " . ($apiVersion ?? 'auto-detect'), get_staff_user_id());
        log_activity("send_group_message_evolution MESSAGE PREVIEW - First 100 chars: " . substr($message, 0, 100) . (strlen($message) > 100 ? '...' : ''), get_staff_user_id());
        
        // Try version 2 format first (text at root level) - most common
        if ($apiVersion == 2 || $apiVersion === null) {
            $data = [
                'number' => $groupId,
                'text' => $message,
                'options' => [
                    'delay' => 1000,
                    'presence' => 'composing',
                    'linkPreview' => false
                ]
            ];
            
            $json_data = json_encode($data);
            
            log_activity("send_group_message_evolution ATTEMPT 1 - Using v2 format (text at root level)", get_staff_user_id());
            
            curl_setopt_array($curl, [
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $json_data,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "ApiKey: {$tokenDevice}"
                ],
            ]);
            
            $response = curl_exec($curl);
            $err = curl_error($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            if ($err) {
                log_activity("send_group_message_evolution cURL ERROR (v2) ✗ - Error: {$err}", get_staff_user_id());
                curl_close($curl);
                return ['error' => $err, 'status' => 'error'];
            }
            
            $result = json_decode($response, true);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                log_activity("send_group_message_evolution SUCCESS ✓ (v2) - HTTP Code: {$httpCode}, Group ID: {$groupId}, Response: " . json_encode($result), get_staff_user_id());
                curl_close($curl);
                return $result;
            } else {
                log_activity("send_group_message_evolution HTTP ERROR (v2) ✗ - Code: {$httpCode}, Response: {$response}", get_staff_user_id());
                
                // If v2 failed and we're auto-detecting, try v1 format
                if ($apiVersion === null && ($httpCode == 400 || $httpCode == 422)) {
                    log_activity("send_group_message_evolution ATTEMPT 2 - Trying v1 format (textMessage.text)", get_staff_user_id());
                    
                    // Try version 1 format (textMessage.text nested)
                    $data_v1 = [
                        'number' => $groupId,
                        'options' => [
                            'delay' => 1000,
                            'presence' => 'composing',
                            'linkPreview' => false
                        ],
                        'textMessage' => [
                            'text' => $message
                        ]
                    ];
                    
                    $json_data_v1 = json_encode($data_v1);
                    
                    curl_setopt_array($curl, [
                        CURLOPT_URL => $apiUrl,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 60,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => $json_data_v1,
                        CURLOPT_HTTPHEADER => [
                            "Content-Type: application/json",
                            "ApiKey: {$tokenDevice}"
                        ],
                    ]);
                    
                    $response_v1 = curl_exec($curl);
                    $err_v1 = curl_error($curl);
                    $httpCode_v1 = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);
                    
                    if ($err_v1) {
                        log_activity("send_group_message_evolution cURL ERROR (v1) ✗ - Error: {$err_v1}", get_staff_user_id());
                        return ['error' => $err_v1, 'status' => 'error'];
                    }
                    
                    $result_v1 = json_decode($response_v1, true);
                    
                    if ($httpCode_v1 >= 200 && $httpCode_v1 < 300) {
                        log_activity("send_group_message_evolution SUCCESS ✓ (v1) - HTTP Code: {$httpCode_v1}, Group ID: {$groupId}, Response: " . json_encode($result_v1), get_staff_user_id());
                        return $result_v1;
                    } else {
                        log_activity("send_group_message_evolution HTTP ERROR (v1) ✗ - Code: {$httpCode_v1}, Response: {$response_v1}", get_staff_user_id());
                        return ['error' => 'Failed to send message to group', 'status' => 'error', 'http_code' => $httpCode_v1, 'response' => $result_v1];
                    }
                } else {
                    curl_close($curl);
                    return ['error' => 'Failed to send message to group', 'status' => 'error', 'http_code' => $httpCode, 'response' => $result];
                }
            }
        } else {
            // Version 1 format (textMessage.text nested)
            $data = [
                'number' => $groupId,
                'options' => [
                    'delay' => 1000,
                    'presence' => 'composing',
                    'linkPreview' => false
                ],
                'textMessage' => [
                    'text' => $message
                ]
            ];
            
            $json_data = json_encode($data);
            
            log_activity("send_group_message_evolution - Using v1 format (textMessage.text)", get_staff_user_id());
            
            curl_setopt_array($curl, [
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $json_data,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "ApiKey: {$tokenDevice}"
                ],
            ]);
            
            $response = curl_exec($curl);
            $err = curl_error($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($err) {
                log_activity("send_group_message_evolution cURL ERROR ✗ - Error: {$err}, URL: {$apiUrl}, Group ID: {$groupId}", get_staff_user_id());
                return ['error' => $err, 'status' => 'error'];
            }
            
            $result = json_decode($response, true);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                log_activity("send_group_message_evolution SUCCESS ✓ - HTTP Code: {$httpCode}, Group ID: {$groupId}, Response: " . json_encode($result), get_staff_user_id());
                return $result;
            } else {
                log_activity("send_group_message_evolution HTTP ERROR ✗ - Code: {$httpCode}, Group ID: {$groupId}, Response: {$response}", get_staff_user_id());
                return ['error' => 'Failed to send message to group', 'status' => 'error', 'http_code' => $httpCode, 'response' => $result];
            }
        }
    }
}
