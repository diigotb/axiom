<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Leadfinder_model extends App_Model
{

    private $uniqueValidationFields = [];
    private $importGuidelinesMessage = ''; // <-- Nova propriedade

    public function __construct()
    {
        $uniqueValidationFields = json_decode(get_option('lead_unique_validation'));

        if (count($uniqueValidationFields) > 0) {
            $this->uniqueValidationFields = $uniqueValidationFields;
            $message = '';

            foreach ($uniqueValidationFields as $key => $field) {
                if ($key === 0) {
                    $message .= 'Com base em seus leads <b class="text-danger">validação única</b> configurada <a href="' . admin_url('settings?group=leads#unique_validation_wrapper') . '" target="_blank">opções</a>, o lead <b>não</b> será importado se:<br />';
                }

                $message .= '<br />&nbsp;&nbsp;&nbsp; - Lead <b>' . $field . '</b> já existe OU';
            }

            if ($message != '') {
                $message = substr($message, 0, -3);
            }

            $message .= '<br /><br />Se você ainda quiser importar todos os leads, desmarque todos os campos de validação exclusivos';

            // Armazena a mensagem renderizada no atributo da classe
            $this->importGuidelinesMessage = $this->addImportGuidelinesInfo($message);
        }

     
    }

    public function addImportGuidelinesInfo($message)
    {
        return '<div class="alert alert-info" role="alert">           
            <p>' . $message . '</p>
        </div>';
    }

    // Novo método para obter a mensagem
    public function getImportGuidelinesMessage()
    {
        return $this->importGuidelinesMessage;
    }

    public function search_leads($params)
    {
        $apiKey = get_option('tokenopenai_contactcenter');
        if (!$apiKey) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'API Key not found']));
        }

        $prompt = $this->build_prompt($params);

        $payload = json_encode([
            // 'model' => 'gpt-4.1',
            'model' => 'gpt-4.1-mini',
            'input' => $prompt,
            'tools' => [[
                'type' => 'web_search_preview',
                'user_location' => [
                    'type' => 'approximate',
                    'country' => $params['country'],
                    'city' => $params['city'],
                    'region' => $params['region'],
                ]
            ]],
            'tool_choice' => ['type' => 'web_search_preview']
        ]);        

        $ch = curl_init('https://api.openai.com/v1/responses');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);



        $conteudo = '';

        foreach ($data['output'] as $item) {
            if ($item['type'] === 'message') {
                foreach ($item['content'] as $content) {
                    if ($content['type'] === 'output_text') {
                        $conteudo .= $content['text'];
                    }
                }
            }
        }

        // Verifica se a resposta contém o JSON esperado
        if (!empty($conteudo)) {
            $text = $conteudo;

            // Extrai o conteúdo JSON do texto (entre ```json e ```)
            if (preg_match('/```json(.*?)```/s', $text, $matches)) {
                $jsonString = trim($matches[1]);
                $leads = json_decode($jsonString, true);
                if ($leads) {
                    return $leads;
                }
            } elseif (preg_match('/\{.*?\}/s', $text, $matches)) {
                $jsonString = trim($matches[0]);
                $leads = json_decode($jsonString, true);
                if ($leads) {
                    return $leads;
                }
            } elseif (preg_match('/```(?:json)?\s*(\[.*?\])\s*```/s', $text, $matches)) {
                $jsonString = trim($matches[0]);
                $leads = json_decode($jsonString, true);
                if ($leads) {
                    return $leads;
                }
            } else {
                return ['error' => 'Sem Resultados'];
            }
        }

        return ['error' => 'Conteúdo não encontrado na resposta.'];
    }


    private function build_prompt($p)
    {
        $prompt = 'Search the web for businesses and return a JSON array with fields name, company, phone, website, city, address, social media url ';
        $prompt .= 'the phone number should be in the format exemple: 5517991198465. ';
        if (isset($p['category'])) {
            $prompt .= 'Category: ' . $p['category'] . '. ';
        }
        $prompt .= 'Quantity: max 25 ';


        return $prompt;
    }

    public function import_leads($leads, $data)
    {
        $this->load->model('leads_model');
        $count = 0;
        foreach ($leads as $lead) {

            $crear = [
                'name' => $lead['name'] ?? '',
                'company' => $lead['company'] ?? '',
                'phonenumber' => $lead['phone'] ?? '',
                'website' => $lead['website'] ?? '',
                'city' => $lead['city'] ?? '',
                'state' => $lead['state'] ?? '',
                'address' => $lead['address'] ?? '',
                'status' => $data['status'],
                'source' => $data['source'],
                'assigned' => $data['staffid'],
            ];

            if ($this->isDuplicateLead($crear)) {
                continue;
            }


            $id = $this->leads_model->add($crear);
            if ($id) {
                $count++;
            }
        }
        return $count;
    }

    private function isDuplicateLead($data)
    {
        foreach ($this->uniqueValidationFields as $field) {
            if ((isset($data[$field]) && $data[$field] != '') && total_rows('leads', [$field => $data[$field]]) > 0) {
                return true;
            }
        }

        return false;
    }
}
