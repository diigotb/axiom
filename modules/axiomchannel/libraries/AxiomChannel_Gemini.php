<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * AxiomChannel_Gemini
 * Integração com Google Gemini API
 * Código original — RT Marketing Estratégico
 */
class AxiomChannel_Gemini
{
    const API_URL    = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    const MAX_TOKENS = 8192;
    const TIMEOUT    = 60;

    private $api_key;

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    /**
     * Gera uma resposta com base no histórico da conversa
     *
     * @param string $system_prompt  — instruções do assistente
     * @param array  $history        — mensagens anteriores [{direction, content}]
     * @param string $user_message   — mensagem atual
     * @return array ['success' => bool, 'text' => string, 'error' => string]
     */
    public function generate_response($system_prompt, $history, $user_message)
    {
        $contents = [];

        // System prompt como primeira mensagem do usuário (Gemini não tem campo system nativo no v1beta)
        if (!empty($system_prompt)) {
            $contents[] = [
                'role'  => 'user',
                'parts' => [['text' => $system_prompt]]
            ];
            $contents[] = [
                'role'  => 'model',
                'parts' => [['text' => 'Entendido. Vou seguir essas instruções.']]
            ];
        }

        // Histórico das últimas 20 mensagens
        $recent_history = array_slice($history, -20);
        foreach ($recent_history as $msg) {
            if (empty($msg['content'])) continue;
            $role = ($msg['direction'] === 'outbound') ? 'model' : 'user';
            $contents[] = [
                'role'  => $role,
                'parts' => [['text' => $msg['content']]]
            ];
        }

        // Mensagem atual
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $user_message]]
        ];

        $body = json_encode([
            'contents'         => $contents,
            'generationConfig' => [
                'maxOutputTokens' => self::MAX_TOKENS,
                'temperature'     => 0.7,
            ],
        ]);

        $url = self::API_URL . '?key=' . $this->api_key;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_TIMEOUT        => self::TIMEOUT,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($body),
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        if ($curl_err) {
            return ['success' => false, 'text' => '', 'error' => 'cURL error: ' . $curl_err];
        }

        $decoded = json_decode($response, true);

        if ($http_code !== 200) {
            $api_err = isset($decoded['error']['message']) ? $decoded['error']['message'] : $response;
            return ['success' => false, 'text' => '', 'error' => 'API error ' . $http_code . ': ' . $api_err];
        }

        $text = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty($text)) {
            return ['success' => false, 'text' => '', 'error' => 'Resposta vazia do Gemini'];
        }

        return ['success' => true, 'text' => $text, 'error' => ''];
    }
}
