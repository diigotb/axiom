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