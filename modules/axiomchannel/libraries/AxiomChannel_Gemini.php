<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * AxiomChannel_Gemini
 * Integração com Google Gemini API
 * Código original — RT Marketing Estratégico
 *
 * CONCEITO PHP PARA RODRIGO:
 * Esta é uma "library" — uma classe de serviço que faz UMA coisa só:
 * conversar com a API do Gemini. Ela não sabe nada do Perfex,
 * não acessa banco, não envia WhatsApp. Só fala com o Gemini.
 * Isso se chama "separação de responsabilidades" — cada classe
 * tem uma responsabilidade única. Fica fácil de testar e trocar.
 */
class AxiomChannel_Gemini
{
    // Constantes da API Gemini
    // "const" = valor fixo que nunca muda durante a execução
    const API_URL  = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    const MAX_TOKENS = 1024;
    const TIMEOUT    = 20; // segundos

    private $api_key;

    /**
     * Construtor recebe a chave da API
     * Ex: new AxiomChannel_Gemini('AIzaSy...')
     */
    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    // ============================================================
    // MÉTODO PRINCIPAL: gerar resposta para uma mensagem
    // ============================================================

    /**
     * Gera uma resposta com base no histórico da conversa
     *
     * @param string $system_prompt  — as instruções do assistente (quem ele é)
     * @param array  $history        — array de mensagens anteriores [{role, content}]
     * @param string $user_message   — a mensagem atual do cliente
     * @return array ['success' => bool, 'text' => string, 'error' => string]
     */
    public function generate_response($system_prompt, $history, $user_message)
    {
        // Monta o array de "contents" no formato que o Gemini espera
        // Gemini usa "user" e "model" (não "assistant" como OpenAI)
        $contents = [];

        // Adiciona o histórico (últimas N mensagens para não estourar o contexto)
        // array_slice pega os últimos 20 itens do histórico
        $recent_history = array_slice($history, -20);

        foreach ($recent_history as $msg) {
            // Gemini só aceita "user" e "model" como roles
            $role = ($msg['direction'] === 'outbound') ? 'model' : 'user';

            // Pula mensagens sem conteúdo de texto
            if (empty($msg['content'])) {
                continue;
            }

            $contents[] = [
                'role'  => $role,
                'parts' => [['text' => $msg['content']]],
            ];
        }

        // Adiciona a mensagem atual do cliente
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $user_message]],
        ];

        // Monta o payload completo para a API Gemini
        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $system_prompt]],
            ],
            'contents'           => $contents,
            'generationConfig'   => [
                'maxOutputTokens' => self::MAX_TOKENS,
                'temperature'     => 0.7,
            ],
        ];

        // Faz a requisição HTTP para a API Gemini via cURL
        $url = self::API_URL . '?key=' . $this->api_key;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => self::TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Trata erros de conexão
        if ($curl_error) {
            return ['success' => false, 'text' => '', 'error' => 'cURL error: ' . $curl_error];
        }

        $data = json_decode($response, true);

        // Trata erros retornados pela API
        if ($http_code !== 200 || isset($data['error'])) {
            $error_msg = isset($data['error']['message']) ? $data['error']['message'] : 'HTTP ' . $http_code;
            return ['success' => false, 'text' => '', 'error' => $error_msg];
        }

        // Extrai o texto da resposta
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty($text)) {
            return ['success' => false, 'text' => '', 'error' => 'Resposta vazia da API Gemini'];
        }

        return ['success' => true, 'text' => trim($text), 'error' => ''];
    }
}
