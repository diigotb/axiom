<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * AxiomChannel_Evolution
 * Biblioteca de integração com Evolution API v2
 * Código original — RT Marketing Estratégico
 */
class AxiomChannel_Evolution
{
    private $server_url;
    private $api_key;
    private $instance;

    public function __construct($server_url, $instance, $api_key = null)
    {
        $this->server_url = rtrim($server_url, '/');
        $this->instance   = $instance;
        $this->api_key    = $api_key;
    }

    // -------------------------------------------------------
    // Instância
    // -------------------------------------------------------

    public function get_qrcode()
    {
        return $this->request('GET', "/instance/connect/{$this->instance}");
    }

    public function get_status()
    {
        return $this->request('GET', "/instance/connectionState/{$this->instance}");
    }

    public function create_instance($webhook_url = null)
    {
        $payload = [
            'instanceName' => $this->instance,
            'qrcode'       => true,
        ];
        if ($webhook_url) {
            $payload['webhook'] = [
                'url'     => $webhook_url,
                'enabled' => true,
                'events'  => ['MESSAGES_UPSERT', 'CONNECTION_UPDATE', 'MESSAGES_UPDATE'],
            ];
        }
        return $this->request('POST', '/instance/create', $payload);
    }

    public function delete_instance()
    {
        return $this->request('DELETE', "/instance/delete/{$this->instance}");
    }

    public function logout_instance()
    {
        return $this->request('DELETE', "/instance/logout/{$this->instance}");
    }

    // -------------------------------------------------------
    // Envio de mensagens
    // -------------------------------------------------------

    public function send_text($phone, $message, $delay = 1000)
    {
        return $this->request('POST', "/message/sendText/{$this->instance}", [
            'number'  => $this->format_phone($phone),
            'text'    => $message,
            'delay'   => $delay,
        ]);
    }

    public function send_image($phone, $url, $caption = '')
    {
        return $this->request('POST', "/message/sendMedia/{$this->instance}", [
            'number'  => $this->format_phone($phone),
            'mediatype' => 'image',
            'media'   => $url,
            'caption' => $caption,
        ]);
    }

    public function send_document($phone, $url, $filename)
    {
        return $this->request('POST', "/message/sendMedia/{$this->instance}", [
            'number'    => $this->format_phone($phone),
            'mediatype' => 'document',
            'media'     => $url,
            'fileName'  => $filename,
        ]);
    }

    public function send_audio($phone, $url)
    {
        return $this->request('POST', "/message/sendWhatsAppAudio/{$this->instance}", [
            'number' => $this->format_phone($phone),
            'audio'  => $url,
        ]);
    }

    public function send_reaction($phone, $message_id, $emoji)
    {
        return $this->request('POST', "/message/sendReaction/{$this->instance}", [
            'key'      => ['remoteJid' => $this->format_phone($phone), 'id' => $message_id],
            'reaction' => $emoji,
        ]);
    }

    // -------------------------------------------------------
    // Webhook
    // -------------------------------------------------------

    public function set_webhook($url, $events = [])
    {
        if (empty($events)) {
            $events = ['MESSAGES_UPSERT', 'CONNECTION_UPDATE', 'MESSAGES_UPDATE', 'QRCODE_UPDATED'];
        }
        return $this->request('POST', "/webhook/set/{$this->instance}", [
            'url'     => $url,
            'enabled' => true,
            'events'  => $events,
        ]);
    }

    // -------------------------------------------------------
    // Contatos
    // -------------------------------------------------------

    public function check_number($phone)
    {
        return $this->request('GET', "/chat/whatsappNumbers/{$this->instance}?numbers[]=" . $this->format_phone($phone));
    }

    public function get_profile_picture($phone)
    {
        return $this->request('GET', "/chat/fetchProfilePictureUrl/{$this->instance}?number=" . $this->format_phone($phone));
    }

    // -------------------------------------------------------
    // Utilitários
    // -------------------------------------------------------

    private function format_phone($phone)
    {
        // Remove tudo que não é número
        $clean = preg_replace('/\D/', '', $phone);
        // Garante DDI 55 para números BR
        if (strlen($clean) <= 11 && substr($clean, 0, 2) !== '55') {
            $clean = '55' . $clean;
        }
        return $clean . '@s.whatsapp.net';
    }

    private function request($method, $endpoint, $payload = null)
    {
        $url = $this->server_url . $endpoint;
        $ch  = curl_init();

        $headers = ['Content-Type: application/json'];
        if ($this->api_key) {
            $headers[] = 'apikey: ' . $this->api_key;
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error     = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'error' => $error];
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'error' => 'Invalid JSON response', 'raw' => $response];
        }

        return ['success' => ($http_code >= 200 && $http_code < 300), 'data' => $decoded, 'http_code' => $http_code];
    }
}
