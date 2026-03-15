<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Openai_api
{
    public function __construct() {}

    function callApi($url, $tokenOpenai, $device_id, $leadid, $device_token, $model)
    {
        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer $tokenOpenai",
            "OpenAI-Beta: assistants=v2"
        ];

        $maxRetries = 15;
        $waitTime = 2; // espera inicial em segundos

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, true);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                $dataError = [
                    "device_id" => $device_id,
                    "lead_id" => $leadid,
                    "device_token" => $device_token,
                    "model" => $model,
                    "error" => curl_error($ch)
                ];
                log_activity("Erro na requisição com cabeçalhos: " . json_encode($dataError), get_staff_user_id());
                curl_close($ch);
                return false;
            }

            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body   = substr($response, $header_size);
            curl_close($ch);

            $responseArray = json_decode($body, true);

            if (isset($responseArray["error"])) {
                if ($responseArray["error"]["code"] === "rate_limit_exceeded") {
                    // tenta extrair o header x-ratelimit-reset-requests
                    preg_match('/x-ratelimit-reset-requests:\s*([0-9]+)(ms|s)/i', $header, $matches);
                    if (!empty($matches)) {
                        $timeValue = (int) $matches[1];
                        $timeUnit  = strtolower($matches[2]);

                        if ($timeUnit === 'ms') {
                            usleep($timeValue * 1000);
                        } else {
                            sleep($timeValue);
                        }
                    } else {
                        sleep($waitTime); // fallback
                        $waitTime = min($waitTime * 2, 60); // backoff até 60s
                    }

                    $dataError = [
                        "device_id"   => $device_id,
                        "lead_id"     => $leadid,
                        "device_token" => $device_token,
                        "model"       => $model,
                        "request"     => $url,
                        "waitTime"    => isset($timeValue) ? $timeValue . $timeUnit : $waitTime . "s",
                    ];

                    log_activity("(callApi) Tentativa de executar novamente por rate_limit_exceeded: " . json_encode($dataError) . "\nCabeçalhos: " . $header, get_staff_user_id());
                    continue;
                }

                // outros erros
                $dataError = [
                    "device_id" => $device_id,
                    "lead_id"   => $leadid,
                    "device_token" => $device_token,
                    "model"     => $model,
                    "error"     => $responseArray["error"]
                ];
                return false;
            }

            return $responseArray; // sucesso
        }

        $dataError = [
            "device_id" => $device_id,
            "lead_id"   => $leadid,
            "device_token" => $device_token,
            "model"     => $model,
            "error"     => "rate_limit_exceeded após $maxRetries tentativas"
        ];
        log_activity("Erro final após $maxRetries tentativas: " . json_encode($dataError) . "\nCabeçalhos: " . $header, get_staff_user_id());
        return false;
    }


    // function callApi($url, $tokenOpenai, $device_id, $leadid, $device_token, $model)
    // {
    //     // Cabeçalhos da requisição
    //     $headers = array(
    //         "Content-Type: application/json",
    //         "Authorization: Bearer $tokenOpenai",
    //         "OpenAI-Beta: assistants=v2"
    //     );

    //     // Número máximo de tentativas
    //     $maxRetries = 15;
    //     // Tempo inicial de espera em segundos
    //     $initialWaitTime = 2;

    //     for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
    //         // Configuração da requisição cURL
    //         $ch = curl_init($url);
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //         curl_setopt($ch, CURLOPT_HEADER, true);  // Inclui os cabeçalhos na resposta

    //         // Executa a requisição e obtém a resposta
    //         $response = curl_exec($ch);

    //         // Verifica por erros cURL
    //         if (curl_errno($ch)) {
    //             $dataError = [
    //                 "device_id" => $device_id,
    //                 "lead_id" => $leadid,
    //                 "device_token" => $device_token,
    //                 "model" => $model,
    //                 "error" => curl_error($ch)
    //             ];

    //             // Salva o erro e cabeçalhos no log
    //             log_activity("Erro na requisição com cabeçalhos: " . json_encode($dataError) . "\nCabeçalhos: " . substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE)), get_staff_user_id());
    //             curl_close($ch);
    //             return false;
    //         }

    //         // Obtém os cabeçalhos de resposta
    //         $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    //         $header = substr($response, 0, $header_size);
    //         $body = substr($response, $header_size);

    //         curl_close($ch);

    //         // Decodifica a resposta
    //         $responseArray = json_decode($body, true);

    //         // Verifica se há erros na resposta
    //         if (isset($responseArray["error"])) {
    //             if (isset($responseArray["error"]["code"]) && $responseArray["error"]["code"] == "rate_limit_exceeded") {
    //                 // Extrair o valor de x-ratelimit-reset-requests do cabeçalho
    //                 preg_match('/x-ratelimit-reset-requests: (\d+)(ms|s)/', $header, $matches);
    //                 if (isset($matches[1]) && isset($matches[2])) {
    //                     $timeValue = (int) $matches[1]; // O valor numérico
    //                     $timeUnit = $matches[2]; // A unidade (s ou ms)

    //                     if ($timeUnit == 'ms') {
    //                         usleep($timeValue * 1000); // Converte milissegundos para microsegundos
    //                     } elseif ($timeUnit == 's') {
    //                         sleep($timeValue); // Usa sleep para segundos
    //                     }
    //                 } else {
    //                     sleep($initialWaitTime); // Tempo padrão de espera se o cabeçalho não estiver presente
    //                 }

    //                 // Log do rate limit excedido
    //                 $dataError = [
    //                     "device_id" => $device_id,
    //                     "lead_id" => $leadid,
    //                     "device_token" => $device_token,
    //                     "model" => $model,
    //                     "request" => $url,
    //                     "waitTime" => isset($timeValue) ? $timeValue . $timeUnit : $initialWaitTime . "s",
    //                 ];

    //                 log_activity("(callApi) Tentativa de executar novamente por rate_limit_exceeded: " . json_encode($dataError) . "\nCabeçalhos: " . $header, get_staff_user_id());


    //                 continue; // Tenta novamente
    //             }

    //             // Para outros erros, salva no log e retorna falso
    //             $dataError = [
    //                 "device_id" => $device_id,
    //                 "lead_id" => $leadid,
    //                 "device_token" => $device_token,
    //                 "model" => $model,
    //                 "error" => $responseArray["error"]
    //             ];

    //             return false;
    //         }


    //         return $responseArray; // Retorna a resposta padrão
    //     }

    //     // Se todas as tentativas falharem, registra um erro final
    //     $dataError = [
    //         "device_id" => $device_id,
    //         "lead_id" => $leadid,
    //         "device_token" => $device_token,
    //         "model" => $model,
    //         "error" => "rate_limit_exceeded após $maxRetries tentativas"
    //     ];

    //     log_activity("Erro na requisição após $maxRetries tentativas: " . json_encode($dataError) . "\nCabeçalhos: " . $header, get_staff_user_id());
    //     return false;
    // }


    // OpenAiApiCaller.php

function calApiPost($url, $tokenOpenai, $device_id, $leadid, $device_token, $model, $data)
    {
        // Cabeçalhos da requisição
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer $tokenOpenai",
            "OpenAI-Beta: assistants=v2"
        );

        // Número máximo de tentativas
        $maxRetries = 15;
        // Tempo inicial de espera em segundos
        $initialWaitTime = 2;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            // Configuração da requisição cURL
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Envia os dados em JSON

            // Executa a requisição e obtém a resposta
            $response = curl_exec($ch);

            // Verifica por erros cURL
            if (curl_errno($ch)) {
                $dataError = [
                    "device_id" => $device_id,
                    "lead_id" => $leadid,
                    "device_token" => $device_token,
                    "model" => $model,
                    "request" => json_encode($data),
                    "error" => curl_error($ch)
                ];

                // Salva o erro e cabeçalhos no log
                curl_close($ch);
                return false;
            }

            // Obtém os cabeçalhos de resposta
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);

            curl_close($ch);

            // Decodifica a resposta
            $responseArray = json_decode($response, true);

            // Verifica se há erros na resposta
            if (isset($responseArray["error"])) {
                if (isset($responseArray["error"]["code"]) && $responseArray["error"]["code"] == "rate_limit_exceeded") {
                    // Extrair o valor de x-ratelimit-reset-requests do cabeçalho
                    preg_match('/x-ratelimit-reset-requests: (\d+)(ms|s)/', $header, $matches);
                    if (isset($matches[1]) && isset($matches[2])) {
                        $timeValue = (int) $matches[1]; // O valor numérico
                        $timeUnit = $matches[2]; // A unidade (s ou ms)

                        if ($timeUnit == 'ms') {
                            usleep($timeValue * 1000); // Converte milissegundos para microsegundos
                        } elseif ($timeUnit == 's') {
                            sleep($timeValue); // Usa sleep para segundos
                        }
                    } else {
                        sleep($initialWaitTime); // Tempo padrão de espera se o cabeçalho não estiver presente
                    }

                    // Log do rate limit excedido
                    $dataError = [
                        "device_id" => $device_id,
                        "lead_id" => $leadid,
                        "device_token" => $device_token,
                        "model" => $model,
                        "request" => json_encode($data),
                        "waitTime" => isset($timeValue) ? $timeValue . $timeUnit : $initialWaitTime . "s",
                    ];
                    log_activity("(calApiPost) Tentativa de executar novamente por rate_limit_exceeded: " . json_encode($dataError) . "\nCabeçalhos: " . $header, get_staff_user_id());

                    continue; // Tenta novamente
                }

                // Para outros erros, salva no log e retorna falso
                $dataError = [
                    "device_id" => $device_id,
                    "lead_id" => $leadid,
                    "device_token" => $device_token,
                    "model" => $model,
                    "request" => json_encode($data),
                    "error" => $responseArray["error"]
                ];

                return false;
            }

            return $responseArray; // Retorna a resposta padrão
        }

        // Se todas as tentativas falharem, registra um erro final
        $dataError = [
            "device_id" => $device_id,
            "lead_id" => $leadid,
            "device_token" => $device_token,
            "model" => $model,
            "request" => json_encode($data),
            "error" => "rate_limit_exceeded após $maxRetries tentativas"
        ];

        log_activity("Erro na requisição após $maxRetries tentativas: " . json_encode($dataError) . "\nCabeçalhos: " . $header, get_staff_user_id());
        return false;
    }
}
