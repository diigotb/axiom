<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

/**
 * Parse Spintax syntax to randomize message content
 * Supports nested brackets: {Hello|Hi|Hey}, {offer|deal|promotion}
 * Example: "{Hello|Hi|Hey}, check out our {offer|deal|promotion}."
 * 
 * @param string $text The text containing Spintax syntax
 * @return string The randomized text
 */
function parse_spintax($text)
{
    // Find all Spintax patterns {Option A|Option B|Option C}
    while (preg_match('/\{([^{}]+)\}/', $text, $matches)) {
        $options = explode('|', $matches[1]);
        // Remove empty options
        $options = array_filter($options, function($option) {
            return trim($option) !== '';
        });
        
        if (count($options) > 0) {
            // Randomly select one option
            $selected = $options[array_rand($options)];
            // Replace the pattern with the selected option
            $text = str_replace($matches[0], trim($selected), $text);
        } else {
            // If no valid options, remove the pattern
            $text = str_replace($matches[0], '', $text);
        }
    }
    
    return $text;
}

/**
 * Pega o status de conexão do device 
 * @param type $tokenDevice
 * @return type
 */
function get_status_device($tokenDevice)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://gateway.apibrasil.io/api/v2/whatsapp/getConnectionStatus',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer " . get_option("tokenBearer_contactcenter"),
            "DeviceToken: {$tokenDevice}"
        ),
    ));
    $response = curl_exec($curl);
    $status = json_decode($response, true);
    curl_close($curl);

    return $status["device"]["status"];
}



/**
 * Pega nome do grupo pegando uma conversa
 * @param type $GroupId
 */
function get_name_group_whats($GroupId, $token)
{
    if ($GroupId) {
        $CI = &get_instance();
        $CI->db->where('group_id', $GroupId);
        $Group = $CI->db->get(db_prefix() . 'contactcenter_group')->row();
        if ($Group) {
            return $Group->group_name;
        } else {
            $name = get_chat_single($GroupId, $token);
            $data["group_name"] = $name["name"];
            $data["group_id"] = $GroupId;
            $CI->db->insert(db_prefix() . 'contactcenter_group', $data);
            $insert_id = $CI->db->insert_id();
            if ($insert_id) {
                return $name["name"];
            }
        }
    }
    return false;
}

/**
 * Conta quantos msg não lindas por device
 * @param type $token
 * @return type
 */
function count_contact_isread($token)
{
    if ($token) {
        $CI = get_instance();
        $CI->db->select('SUM(isread) AS unread_count');
        $CI->db->where('session', $token);
        return $CI->db->get(db_prefix() . 'contactcenter_contact')->row();
    }
}



function count_contact($token)
{
    if ($token) {
        $CI = get_instance();
        $CI->db->select("COUNT(id) AS total");
        $CI->db->where('session', $token);
        $result = $CI->db->get(db_prefix() . 'contactcenter_contact')->row();

        if ($result) {
            return $result->total;
        } else {
            return 0; // Retorna 0 se não houver resultados
        }
    }
}

/**
 * Pega os dados do leads pelo o telefone 
 * @param type $phoneNumber
 * @return type
 */
function get_dados_leads_phone($phoneNumber)
{
    if ($phoneNumber) {
        $CI = &get_instance();
        $CI->db->where('phonenumber', $phoneNumber);
        $lead = $CI->db->get(db_prefix() . 'leads')->row();
        if ($lead) {
            return $lead;
        }
    }
}


function contactcenter_get_contact($phoneNumber)
{
    $CI = &get_instance();
    $CI->db->where('phonenumber', $phoneNumber);
    return $CI->db->get(db_prefix() . 'contactcenter_contact')->row();
}

function get_contactcenter_staff_name($staffid)
{
    if ($staffid) {
        $CI = &get_instance();
        $CI->db->select('firstname');
        $CI->db->where('staffid', $staffid);
        $result = $CI->db->get(db_prefix() . 'staff')->row();
        if ($result) {
            return $result->firstname;
        } else {
            return ''; // Ou outra indicação de que o nome não foi encontrado
        }
    }
    return '';
}

function get_lead_span($lead)
{



    if ($lead->rel_type == "customer" && $lead->leadid) {
        return "<span class='chat-codigo-contact'>" . _l("contac_whats_confere2") . " Id: {$lead->client_id}</span>";
    }

    if ($lead->rel_type == "staff" && $lead->leadid) {
        return "<span class='chat-codigo-contact'>" . _l("contac_whats_confere3") . "</span>";
    }

    if ($lead->rel_type == "lead" && $lead->leadid) {
        return "<span class='chat-codigo-contact'>" . _l("contac_whats_confere1") . " Id: {$lead->leadid}</span>";
    }

    if (!$lead->rel_type && $lead->leadid) {
        return "<span class='chat-codigo-contact'>" . _l("contac_whats_confere1") . " Id: {$lead->leadid}</span>";
    }

    if (!$lead->rel_type && $lead->isGroup) {
        return "<span class='text-success'>" . _l("customer_groups") . "</span>";
    }


    return "<span class='text-danger'>" . _l("contac_whats_confere4") . "</span>";
}

function get_not_lead()
{
    return "<span class='text-danger'>" . _l("contac_whats_confere4") . "</span>";
}

/**
 * Verifica se contato é lead clients ou equipe
 * @param type $phoneNumber
 * @return type
 */
function check_client_leads_whtas($phoneNumber, $validateLead = true)
{
    $CI = &get_instance();
    if ($phoneNumber) {

        if ($validateLead) {
            $CI->db->where('phonenumber', $phoneNumber);
            $CI->db->where('client_id', 0);
            $lead = $CI->db->get(db_prefix() . 'leads')->row();
            if ($lead) {
                return "<span>" . _l("contac_whats_confere1") . " Id: {$lead->id}</span>";
            }
        }


        $CI->db->where('phonenumber', $phoneNumber);
        $client = $CI->db->get(db_prefix() . 'clients')->row();
        if ($client) {
            return "<span>" . _l("contac_whats_confere2") . " Id: {$client->userid}</span>";
        }
        $CI->db->where('phonenumber', $phoneNumber);
        $staff = $CI->db->get(db_prefix() . 'staff')->row();
        if ($staff) {
            return "<span>" . _l("contac_whats_confere3") . "</span>";
        }
        return "<span class='text-danger'>" . _l("contac_whats_confere4") . "</span>";
    }
}

/**
 * Retorna imagem para não fica sem 
 * @param type $thumb
 * @return string
 */
function get_thumb_profile_whats($thumb = null)
{
    if ($thumb) {
        return $thumb;
    } else {
        return site_url("/assets/images/user-placeholder.jpg");
    }
}

/**
 * Pega todas as conveças iniciadas
 * @param type $tokenDevice
 * @return type
 */
function get_all_labels($tokenDevice)
{
    if ($tokenDevice) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://cluster.apigratis.com/api/v2/whatsapp/getAllChats',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . get_option("tokenBearer_contactcenter"),
                "DeviceToken: {$tokenDevice}"
            ),
        ));
        $response = curl_exec($curl);
        $thumb = json_decode($response, true);
        curl_close($curl);
        if (isset($thumb["response"]["contacts"]) && !empty($thumb["response"]["contacts"])) {
            // Limita o processamento dos dados, por exemplo, exibir apenas os primeiros 100 registros
            return array_slice($thumb["response"]["contacts"], 0, 200);
        }
    }
}

function normalize_to_object_array($data)
{
    // Se o dado for um único objeto, encapsula em um array
    if (is_object($data)) {
        return [$data];
    }

    // Se o dado for um array associativo, converte para um objeto encapsulado em um array
    if (is_array($data) && array_keys($data) !== range(0, count($data) - 1)) {
        return [(object)$data];
    }

    // Para arrays indexados, converte cada elemento para objeto, caso não seja já
    return array_map(function ($item) {
        return is_object($item) ? $item : (object)$item;
    }, $data);
}

/**
 * Monta o html do chat 
 * @param type $data
 * @return string
 */
function monta_html_chat_messenger($data)
{
    $ln = null;


    if ($data) {
        // Garante que `$data` seja sempre um array de objetos
        $data = normalize_to_object_array($data);

        foreach ($data as $msg) {
            $class_status = null;
            if ($msg->fromMe) {
                $class = "chat-my chat-my-messenger";

                if ($msg->is_read == 0) {
                    $class_status = "received";
                } else if ($msg->is_read == 1) {
                    $class_status = "read";
                } else {
                    $class_status = "sent";
                }
            } else {
                $class = "chat-others";
            }
            if ($msg->type == "text") {
                $ln .= "<div class='{$class} msg-div' id='{$msg->messenger_id}'>
                                <div id='msg_{$msg->messenger_id}'>
                                    <p>" . nl2br($msg->message) . "</p>
                                    <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->date)) . "<span class='chat_status {$class_status}'></span></span> 
                                </div>
                            </div>";
            } elseif ($msg->type == "audio") {
                $ln .= "<div class='{$class} msg-div' id='{$msg->messenger_id}'>
                            <div>
                               <audio controls>
                                 <source src='{$msg->url}' type='audio/mpeg' >
                               </audio>
                               <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->date)) . "<span class='chat_status {$class_status}'></span></span>
                            </div>
                         </div>";
            } elseif ($msg->type == "video") {

                $ln .= "<div class='{$class} msg-div' id='{$msg->messenger_id}'>
                                <div>
                                    <video controls>
                                         <source src='{$msg->url}'  type='video/mp4' >   
                                    </video>    
                                </div>
                                <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->date)) . "<span class='chat_status {$class_status}'></span></span>
                         </div>";
            } elseif ($msg->type == "image") {

                $ln .= "<div class='{$class} msg-div' id='{$msg->messenger_id}'>
                            <div class='box-chat-img'>
                                <div>
                                  <a href='{$msg->url}' target='_blank' data-lightbox='task-attachment' >
                                    <img src='{$msg->url}' />
                                  </a>     
                                  <span>" . nl2br($msg->msg_content) . "</span> 
                                  <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->date)) . "<span class='chat_status {$class_status}'></span></span>
                                </div>
                            </div>
                         </div>";
            }
        }
        return $ln;
    } else {
        return $ln = "<div class='chat-others msg-div'>
                        <div class='no-results'>
                            <p><i class='fa-solid fa-comments' style='font-size: 48px; color: rgba(255, 255, 255, 0.3); margin-bottom: 16px;'></i></p>
                            <p style='font-size: 18px; font-weight: 500; margin-bottom: 8px;'>" . _l("chat_no_messages") . "</p>
                            <p style='font-size: 14px;'>" . _l("chat_no_messages_subtitle") . "</p>
                        </div>
                    </div>";
    }
}

function monta_html_chat($data, $show_device_badge = false)
{
    $ln = null;
    $lastDate = null;
    if ($data) {
        //  print_r($data);
        foreach ($data as $msg) {
            $class_status = null;
            $class_status_delete = null;
            if ($msg->msg_fromMe) {
                $class = "chat-my msg-div";
                $j_reply = "j_reply";

                if ($msg->msg_status == "PENDING") {
                    $class_status = "sent";
                } else if ($msg->msg_status == "DELIVERY_ACK") {
                    $class_status = "received";
                } else if ($msg->msg_status == "READ") {
                    $class_status = "read";
                } else if ($msg->msg_status == "DELIVERY_DELETE") {
                    $class_status_delete = "deleted_mgs_chat";
                    $j_reply = "";
                } else if ($msg->msg_status == "MESSAGE_EDIT") {
                    $class_status_delete = "edit_msg";
                    $class_status = "read";
                } else {
                    $class_status = "sent";
                }
            } else {
                $class = "chat-others msg-div";
                if ($msg->msg_status == "MESSAGE_EDIT") {
                    $class_status_delete = "edit_msg_others";
                } else if ($msg->msg_status == "DELIVERY_DELETE") {
                    $class_status_delete = "deleted_mgs_chat";
                }
            }


            if ($msg->msg_isGroupMsg == 1) {
                $participantsNames = "<h6>{$msg->msg_name}</h6>";
            } else {
                $participantsNames = "";
            }

            // div da mensagem de remake
            if ($msg->reply_id) {
                $div_reply = "<div class='reply_card' id='reply_{$msg->reply_id}' data-id='{$msg->reply_id}'>
                                    <span class='reply_title' >{$msg->reply_participant}</span>
                                    <p class='reply_msg' >{$msg->reply_msg}</p>
                                </div>";
            } else {
                $div_reply = "";
            }

            //verifica se a mensagem é send e coloca a classe de ação
            if ($msg->msg_fromMe) {
                $action = "action-my";
            } else {
                $action = "";
            }


            $msgDate = date('d/m/Y', strtotime($msg->msg_date)); // só o dia

            // Se for uma nova data, adiciona separador
            if ($msgDate !== $lastDate) {
                $ln .= "<div class='date-separator-chat'>
                         <span>$msgDate</span>
                         <hr>
                    </div>";
                $lastDate = $msgDate;
            }

            $msgSource = "";
            $reactionsHtml = "";

            if ($msg->msg_fromMe) {
                $action = "action-my";
            } else {
                $action = "";
            }

            $msgSource = "";
            $reactionsHtml = "";

            // Monta as reações se existirem
            if ($msg->msg_reaction) {
                $reactions = json_decode($msg->msg_reaction, true);
                if ($reactions && is_array($reactions)) {
                    foreach ($reactions as $sender => $reaction) {
                        if ($reaction) {
                            $reactionsHtml .= "<span class='msg_reaction' title='{$sender}'>{$reaction}</span> ";
                        }
                    }
                }
            }

            // Device badge - show which device the message came from (when showing messages from all devices)
            $deviceBadge = "";
            if ($show_device_badge && !empty($msg->device_dev_name)) {
                $deviceLabel = htmlspecialchars($msg->device_dev_name, ENT_QUOTES, 'UTF-8');
                if (!empty($msg->device_dev_number)) {
                    $deviceLabel .= ' (' . htmlspecialchars($msg->device_dev_number, ENT_QUOTES, 'UTF-8') . ')';
                }
                $deviceBadge = "<span class='msg_device_badge' title='" . _l('chat_message_from_device') . ": {$deviceLabel}'><i class='fa-solid fa-mobile-screen-button'></i> {$deviceLabel}</span>";
            } elseif ($show_device_badge && !empty($msg->device_dev_number)) {
                $deviceLabel = htmlspecialchars($msg->device_dev_number, ENT_QUOTES, 'UTF-8');
                $deviceBadge = "<span class='msg_device_badge' title='" . _l('chat_message_from_device') . ": {$deviceLabel}'><i class='fa-solid fa-mobile-screen-button'></i> {$deviceLabel}</span>";
            }

            // Define o ícone da fonte ou informações do cliente
            $icon = "";
            $tooltipTitle = "";
            $clientInfo = "";

            if ($msg->msg_fromMe) {
                // Mensagens enviadas pelo sistema (admin/staff side) - mostrar ícones baseados no tipo
                if ($msg->sent_source) {
                    if (in_array($msg->sent_source, ["webhook_local", "webhook_local_ack", "webhook_local_reaction"])) {
                        $tooltipTitle = _l("contac_whats_webhook_local_tooltip");
                        $icon = "<i class='fa-solid fa-display'></i>";
                    } elseif ($msg->sent_source == "crm" || $msg->sent_source == "crm_user") {
                        // Manual user sent
                        $tooltipTitle = _l("contac_whats_webhook_system_tooltip");
                        $icon = "<i class='fa-solid fa-user'></i>";
                    } elseif ($msg->sent_source == "AI") {
                        // AI assistant sent
                        $tooltipTitle = _l("contac_whats_webhook_ai_tooltip");
                        $icon = "<i class='fa-solid fa-robot'></i>";
                    } elseif ($msg->sent_source == "campaign" || stripos($msg->sent_source, "campaign") !== false) {
                        // Campaign sent
                        $tooltipTitle = _l("contac_message_sent_by_campaign") ?: "Sent by Campaign";
                        $icon = "<i class='fa-solid fa-bullhorn'></i>";
                    } elseif ($msg->sent_source == "followup" || $msg->sent_source == "follow-up" || stripos($msg->sent_source, "follow") !== false) {
                        // Follow-up sent
                        $tooltipTitle = _l("contac_message_sent_by_followup") ?: "Sent by Follow-up";
                        $icon = "<i class='fa-solid fa-clock-rotate-left'></i>";
                    } else {
                        // Default fallback
                        $tooltipTitle = _l("contac_whats_webhook_crm_tooltip");
                        $icon = '<i class="fa-solid fa-gear"></i>';
                    }
                } else {
                    // No source specified - assume manual
                    $tooltipTitle = _l("contac_message_sent_manually") ?: "Sent manually";
                    $icon = "<i class='fa-solid fa-user'></i>";
                }
                
                // Se foi enviado pelo sistema (lado direito), reações à esquerda
                $msgSource = "<p class='msg_source' title='{$tooltipTitle}'>{$reactionsHtml} {$icon} {$deviceBadge}</p>";
            } else {
                // Mensagens recebidas do cliente - nome será mostrado dentro da mensagem
                $contactName = $msg->msg_name ? $msg->msg_name : ($msg->msg_conversation_number ? $msg->msg_conversation_number : "");
                $contactNameEscaped = htmlspecialchars($contactName, ENT_QUOTES, 'UTF-8');
                
                // Store contact name for use inside message bubble - show device badge if present
                $msgSource = "<p class='msg_source'" . ($deviceBadge ? "" : " style='display: none;'") . ">{$reactionsHtml} {$deviceBadge}</p>";
                // Contact name will be added inside the message div
            }




            if ($msg->msg_type == "text" || $msg->msg_type == "chat" || $msg->msg_type == "list_respon") {


                if ($msg->msg_isGroupMsg == 1) {
                    $ln .= "<div class='{$class} msg-div {$action} {$j_reply} msg-div' id='{$msg->msg_send_id}' data-action='2' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-msgid='{$msg->msg_send_id}' data-name='" . ($msg->msg_name ? $msg->msg_name : $msg->msg_conversation_number) . "'>
                                $msgSource
                                <div id='msg_{$msg->msg_id}' class='msg_{$msg->msg_send_id} {$class_status_delete}' >
                                    $div_reply
                                    $participantsNames
                                    <p class='msg_content'>" . nl2br(($msg->msg_content ? $msg->msg_content : $msg->msg_title)) . "</p>
                                    <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>  
                                </div>
                            </div>";
                } else {
                    // For received messages, add contact name inside the message bubble
                    $contactNameDisplay = "";
                    if (!$msg->msg_fromMe && $msg->msg_name) {
                        $contactNameDisplay = "<div class='msg_author_name'>" . htmlspecialchars($msg->msg_name, ENT_QUOTES, 'UTF-8') . "</div>";
                    }
                    
                    $ln .= "<div class='{$class} msg-div {$action} {$j_reply} msg-div' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='2' data-msgid='{$msg->msg_id}' data-name='" . ($msg->msg_name ? $msg->msg_name : $msg->msg_conversation_number) . "'>
                                 $msgSource
                                <div id='msg_{$msg->msg_id}' class='msg_{$msg->msg_send_id} {$class_status_delete}'>
                                    $contactNameDisplay
                                    $div_reply
                                    <p class='msg_content'>" . nl2br(($msg->msg_content ? $msg->msg_content : $msg->msg_title)) . "</p>
                                    <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span> 
                                </div>
                            </div>";
                }
            } elseif ($msg->msg_type == "link") {
                $ln .= "<div class='{$class} msg-div {$action} {$j_reply}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='2' data-name='" . ($msg->msg_name ? $msg->msg_name : $msg->msg_conversation_number) . "'>
                         $msgSource
                        <div id='msg_{$msg->msg_id}'>
                            $div_reply
                            $participantsNames
                            <p>" . nl2br(($msg->msg_url ? $msg->msg_url : $msg->msg_content)) . "</p>
                            <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                        </div>
                    </div>";
            } elseif ($msg->msg_type == "ptt") {

                if (strpos($msg->msg_base64, 'data:audio') === 0) {
                    $src = $msg->msg_base64;
                } else {
                    $src = site_url("{$msg->msg_base64}");
                }

                $load = site_url("/modules/contactcenter/assets/image/load.gif");
                $ln .= "<div class='{$class} msg-div msg-div {$action} get_{$msg->msg_send_id}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-msgid='{$msg->msg_send_id}'>
                             $msgSource
                            <img class='load-media' style='width:30px;height:30px; display:none; ' src='$load'/>
                            <div class='icon-media {$class_status_delete}'>
                                <i class='fa-solid fa-music' onclick='get_media_evolution(\"{$msg->msg_send_id}\")' ></i>
                                <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                            </div> 
                        </div>";


                // $ln .= "<div class='{$class} msg-div' id='{$msg->msg_send_id}'>                            
                //             <div>
                //                <audio controls>
                //                  <source src='{$src}' type='audio/mpeg' >
                //                </audio>
                //                $participantsNames
                //                <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                //             </div>
                //          </div>";
            } elseif ($msg->msg_type == "video") {
                // if ($msg->msg_base64) {
                //     $ln .= "<div class='{$class} msg-div' id='{$msg->msg_send_id}'>
                //                 <div>
                //                     <video controls>
                //                          <source src='{$msg->msg_base64}'  type='video/mp4' >   
                //                     </video>    
                //                 </div>
                //                 $participantsNames
                //                 <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                //          </div>";
                // } else {
                // }
                $load = site_url("/modules/contactcenter/assets/image/load.gif");
                $ln .= "<div class='{$class} msg-div {$action} get_{$msg->msg_send_id}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-msgid='{$msg->msg_send_id}'>
                             $msgSource
                            <img class='load-media' style='width:30px;height:30px; display:none; ' src='$load'/>
                            <div class='icon-media'>
                                <i class='fa-regular fa-file-video' onclick='get_media_evolution(\"{$msg->msg_send_id}\")' ></i>
                                <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                            </div> 
                        </div>";
            } elseif ($msg->msg_type == "image") {

                if ($msg->sent_source == "webhook_local_ack" || $msg->msg_status == "DELIVERY_ACK") {
                    $style = "style='width: 130px;'";
                }

                if ($msg->msg_url) {

                    $ln .= "<div class='{$class} msg-div j_reply {$action}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-name='" . ($msg->msg_name ? $msg->msg_name : $msg->msg_conversation_number) . "'>
                            $msgSource
                                <div class='box-chat-img msg_{$msg->msg_send_id} {$class_status_delete}' id='msg_{$msg->msg_id}'>
                                <div>
                                     $div_reply
                                  <a href='" . site_url("uploads/{$msg->msg_url}") . "' target='_blank' data-lightbox='task-attachment' >
                                    <img class='reply_image'{$style} src='" . site_url("uploads/{$msg->msg_url}") . "'  />
                                  </a>     
                                  $participantsNames
                                  <span class='msg_content'>" . nl2br($msg->msg_content) . "</span> 
                                  <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                                </div>
                            </div>
                         </div>";
                } else {
                    $load = site_url("/modules/contactcenter/assets/image/load.gif");
                    $ln .= "<div class='{$class} msg-div {$action} get_{$msg->msg_send_id}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-msgid='{$msg->msg_send_id}'>
                             $msgSource  
                            <img class='load-media' style='width:30px;height:30px; display:none; ' src='$load'/>
                              <div class='box-chat-img'>
                                <div class='icon-media'>
                                   <i class='fa-solid fa-photo-film' onclick='get_media_evolution(\"{$msg->msg_send_id}\")'></i>
                                   $participantsNames
                                   <span class='msg_content'>" . nl2br($msg->msg_content) . "</span> 
                                   <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                                </div>
                              </div>
                            </div>";
                }
            } elseif ($msg->msg_type == "documentPDF") {
                if ($msg->msg_url) {
                    // if($msg->msg_thumb) {
                    //     $thumbBase64 = "<img src='{$msg->msg_thumb}' style='width: 100%; height: 200px; overflow: hidden;' />";
                    // } else {
                    //     // $thumbBase64 ="<iframe src='{$msg->msg_base64}' 
                    //     //                     style='border: none; width: 100%; height: 200px; overflow: hidden;' 
                    //     //                     scrolling='no'></iframe>";
                    //     $thumbBase64 = "";
                    // }
                    $url = ($msg->msg_url ? site_url("uploads/{$msg->msg_url}") : $msg->msg_base64);
                    $ln .= "<div class='{$class} msg-div {$action} get_{$msg->msg_send_id}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-msgid='{$msg->msg_send_id}'>
                                 $msgSource
                                <div class='pdf-info {$class_status_delete}' style='text-align: center;'>
                                             
                                    <span class='pdf-title' style='display: block; font-weight: bold;'>document.pdf</span>
                                    <span class='pdf-description' style='display: block;'>" . nl2br($msg->msg_content) . "</span>
                                    <div class='pdf-buttons'>
                                        <a href='{$url}' download='document.pdf' class='btn'><i class='fa-solid fa-download'></i> Salvar como...</a>
                                    </div>
                                    $participantsNames
                                    <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span> 
                                </div> 
                            </div>";
                } else {
                    $load = site_url("/modules/contactcenter/assets/image/load.gif");
                    $ln .= "<div class='{$class} msg-div {$action} get_{$msg->msg_send_id}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-msgid='{$msg->msg_send_id}'>
                               $msgSource
                              <img class='load-media' style='width:30px;height:30px; display:none; ' src='$load'/>
                              <div class='box-chat-img'>
                                <div class='icon-media {$class_status_delete}'>
                                   <i class='fa-solid fa-photo-film' onclick='get_media_evolution(\"{$msg->msg_send_id}\")'></i>
                                   <span>" . nl2br($msg->msg_content) . "</span> 
                                   <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                                </div>
                              </div>
                            </div>";
                }
            } elseif ($msg->msg_type == "documentZIP") {
                if ($msg->msg_url) {
                    $url = ($msg->msg_url ? site_url("uploads/{$msg->msg_url}") : $msg->msg_base64);

                    $ln .= "<div class='{$class} msg-div {$action} get_{$msg->msg_send_id}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-msgid='{$msg->msg_send_id}'>
                                 $msgSource
                                <div class='pdf-info {$class_status_delete}' style='text-align: center;'>
                                    <span class='pdf-title' style='display: block; font-weight: bold;'>document.zip</span>
                                     <span class='pdf-description' style='display: block;'>" . nl2br($msg->msg_content) . "</span>
                                    <div class='pdf-buttons'>
                                        <a href='{$url}' download='document.zip' class='btn'><i class='fa-solid fa-download'></i> Salvar como...</a>
                                    </div> 
                                    $participantsNames
                                    <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                                </div> 
                            </div>";
                } else {
                    $load = site_url("/modules/contactcenter/assets/image/load.gif");
                    $ln .= "<div class='{$class} msg-div {$action} get_{$msg->msg_send_id}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-msgid='{$msg->msg_send_id}'>
                             $msgSource  
                             <img class='load-media' style='width:30px;height:30px; display:none; ' src='$load'/>
                              <div class='box-chat-img'>
                                <div class='icon-media {$class_status_delete}'>
                                   <i class='fa-regular fa-file-zipper' onclick='get_media_evolution(\"{$msg->msg_send_id}\")'></i>
                                   <span>" . nl2br($msg->msg_content) . "</span> 
                                   <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                                </div>
                              </div>
                            </div>";
                }
            } elseif ($msg->msg_type == "documentXLSX") {
                if ($msg->msg_url) {
                    $url = ($msg->msg_url ? site_url("uploads/{$msg->msg_url}") : $msg->msg_base64);
                    $ln .= "<div class='{$class} msg-div {$action} get_{$msg->msg_send_id}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-msgid='{$msg->msg_send_id}'>
                                 $msgSource
                                <div class='pdf-info {$class_status_delete}' style='text-align: center;'>
                                    <span class='pdf-title' style='display: block; font-weight: bold;'>document.xlsx</span>
                                     <span class='pdf-description' style='display: block;'>" . nl2br($msg->msg_content) . "</span>
                                    <div class='pdf-buttons'>
                                        <a href='{$url}' download='document.xlsx' class='btn'><i class='fa-solid fa-download'></i> Salvar como...</a>
                                    </div> 
                                    $participantsNames
                                    <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                                </div> 
                            </div>";
                } else {
                    $load = site_url("/modules/contactcenter/assets/image/load.gif");
                    $ln .= "<div class='{$class} msg-div {$action} get_{$msg->msg_send_id}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-msgid='{$msg->msg_send_id}'>
                              $msgSource 
                             <img class='load-media' style='width:30px;height:30px; ' src='$load'/>
                              <div class='box-chat-img'>
                                <div class='icon-media {$class_status_delete}'>
                                   <i class='fa-regular fa-file-excel' onclick='get_media_evolution(\"{$msg->msg_send_id}\")'></i>
                                   <span>" . nl2br($msg->msg_content) . "</span> 
                                   <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                                </div>
                              </div>
                            </div>";
                }
            } elseif ($msg->msg_type == "documentDOCX") {
                if ($msg->msg_url) {
                    $url = ($msg->msg_url ? site_url("uploads/{$msg->msg_url}") : $msg->msg_base64);
                    $ln .= "<div class='{$class} msg-div {$action} get_{$msg->msg_send_id}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-msgid='{$msg->msg_send_id}'>
                                 $msgSource
                                <div class='pdf-info {$class_status_delete}' style='text-align: center;'>
                                    <span class='pdf-title' style='display: block; font-weight: bold;'>document.docx</span>
                                    <span class='pdf-description' style='display: block;'>" . nl2br($msg->msg_content) . "</span>
                                    <div class='pdf-buttons'>
                                        <a href='{$url}' download='document.docx' class='btn'><i class='fa-solid fa-download'></i> Salvar como...</a>
                                    </div> 
                                    $participantsNames
                                     <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                                </div> 
                            </div>";
                } else {
                    $load = site_url("/modules/contactcenter/assets/image/load.gif");
                    $ln .= "<div class='{$class} msg-div {$action} get_{$msg->msg_send_id}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-msgid='{$msg->msg_send_id}'>
                             $msgSource  
                            <img class='load-media' style='width:30px;height:30px; display:none; ' src='$load'/>
                              <div class='box-chat-img'>
                                <div class='icon-media {$class_status_delete}'>
                                   <i class='fa-regular fa-file-word' onclick='get_media_evolution(\"{$msg->msg_send_id}\")'></i>
                                   $participantsNames
                                   <span>" . nl2br($msg->msg_content) . "</span> 
                                   <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                                </div>
                              </div>
                            </div>";
                }
            } elseif ($msg->msg_type == "documentPPTX") {
                if ($msg->msg_url) {
                    $url = ($msg->msg_url ? site_url("uploads/{$msg->msg_url}") : $msg->msg_base64);
                    $ln .= "<div class='{$class} msg-div {$action} get_{$msg->msg_send_id}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-msgid='{$msg->msg_send_id}'>
                                 $msgSource
                                <div class='pdf-info {$class_status_delete}' style='text-align: center;'>
                                    <span class='pdf-title' style='display: block; font-weight: bold;'>document.pptx</span>
                                    <span class='pdf-description' style='display: block;'>" . nl2br($msg->msg_content) . "</span>
                                    <div class='pdf-buttons'>
                                        <a href='{$url}' download='document.docx' class='btn'><i class='fa-solid fa-download'></i> Salvar como...</a>
                                    </div> 
                                    $participantsNames
                                     <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                                </div> 
                            </div>";
                } else {
                    $load = site_url("/modules/contactcenter/assets/image/load.gif");
                    $ln .= "<div class='{$class} msg-div {$action} get_{$msg->msg_send_id}' id='{$msg->msg_send_id}' data-hora='" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "' data-action='1' data-msgid='{$msg->msg_send_id}'>
                               $msgSource
                              <img class='load-media' style='width:30px;height:30px; display:none; ' src='$load'/>
                              <div class='box-chat-img'>
                                <div class='icon-media {$class_status_delete}'>
                                   <i class='fa-regular fa-file-word' onclick='get_media_evolution(\"{$msg->msg_send_id}\")'></i>
                                   $participantsNames
                                   <span>" . nl2br($msg->msg_content) . "</span> 
                                   <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($msg->msg_date)) . "<span class='chat_status {$class_status}'></span></span>
                                </div>
                              </div>
                            </div>";
                }
            }
        }
        return $ln;
    } else {
        return $ln = "<div class='chat-others msg-div'>
                        <div class='no-results'>
                            <p><i class='fa-solid fa-comments' style='font-size: 48px; color: rgba(255, 255, 255, 0.3); margin-bottom: 16px;'></i></p>
                            <p style='font-size: 18px; font-weight: 500; margin-bottom: 8px;'>" . _l("chat_no_messages") . "</p>
                            <p style='font-size: 14px;'>" . _l("chat_no_messages_subtitle") . "</p>
                        </div>
                    </div>";
    }
}

function monta_html_chat_pusher($data)
{
    $ln = null;
    if ($data) {
        $load = site_url("/modules/contactcenter/assets/image/load.gif");
        if ($data["msg_fromMe"]) {
            $class = "chat-my msg-div";
        } else {
            $class = "chat-others msg-div";
        }

        if ($data["msg_type"] == "text" || $data["msg_type"] == "chat" || $data["msg_type"] == "list_respon") {

            if ($data["msg_isGroupMsg"] == 1) {
                $ln .= "<div class='{$class} msg-div'>
                        <div>
                            <h6>{$data["msg_name"]}</h6>
                            <p>" . nl2br(($data["msg_content"] ? $data["msg_content"] : $data["msg_title"])) . "</p>
                            <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($data["msg_date"])) . "</span>  
                        </div>
                    </div>";
            } else {
                $ln .= "<div class='{$class} msg-div'>
                        <div>
                            <p>" . nl2br(($data["msg_content"] ? $data["msg_content"] : $data["msg_title"])) . "</p>
                            <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($data["msg_date"])) . "</span>  
                        </div>
                    </div>";
            }
        } elseif ($data["msg_type"] == "link") {
            $ln .= "<div class='{$class} msg-div'>
                        <div>
                            <p>" . nl2br(($data["msg_url"] ? $data["msg_url"] : $data["msg_content"])) . "</p>
                            <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($data["msg_date"])) . "</span>  
                        </div>
                    </div>";
        } elseif ($data["msg_type"] == "ptt") {
            $ln .= "<div class='{$class} msg-div  get_{$data["msg_id"]}'> 
                                <img class='load-media' style='width:30px;height:30px; display:none; ' src='$load'/>
                                <div class='icon-media'>
                                   <i class='fa-solid fa-volume-high' onclick='get_media_evolution(\"{$data["msg_id"]}\")' ></i>
                                   <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($data["msg_date"])) . "</span>
                                </div>
                             </div>";
        } elseif ($data["msg_type"] == "video") {

            $ln .= "<div class='{$class} msg-div get_{$data["msg_id"]}'>
                                <img class='load-media' style='width:30px;height:30px; display:none; ' src='$load'/>
                                <div class='icon-media'>
                                    <i class='fa-regular fa-file-video' onclick='get_media_evolution(\"{$data["msg_id"]}\")' ></i>
                                    <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($data["msg_date"])) . "</span>
                                </div> 
                            </div>";
        } elseif ($data["msg_type"] == "image") {
            $ln .= "<div class='{$class} msg-div get_{$data["msg_id"]}'>
                              <img class='load-media' style='width:30px;height:30px; display:none; ' src='$load'/>
                              <div class='box-chat-img'>
                                <div class='icon-media'>
                                   <i class='fa-solid fa-photo-film' onclick='get_media_evolution(\"{$data["msg_id"]}\")'></i>
                                   <span>" . nl2br($data["msg_content"]) . "</span> 
                                   <span class='msg-time'>" . date('d/m/Y H:i:s', strtotime($data["msg_date"])) . "</span>
                                </div>
                              </div>
                            </div>";
        }
        $result["chat"] = $ln;
        $result["msg_to"] = $data["msg_to"];
        $result["msg_from"] = $data["msg_from"];
        $result["msg_isGroupMsg"] = $data["msg_isGroupMsg"];
        return $result;
    }
}



/**
 * Monta o html do contatos
 * @param type $devicetoken
 * @return string
 */
function monta_html_contact_banco($dados)
{
    if (!$dados || !is_array($dados)) {
        return '';
    }
    $ln = null;
    $contIsRead = null;
    foreach ($dados as $index => $contactUser) {
        if ($contactUser->isread > 0) {
            $contIsRead = "<span class='badge badge-primary chat-contato-unread'>{$contactUser->isread}</span>";
        } else {
            $contIsRead = "<span class='badge badge-primary chat-contato-unread'></span>";
        }

        $number = $contactUser->phonenumber;
        //pega os dados do lead
        //$lead = get_dados_leads_phone($number);

        // For groups, prioritize group_name from join, then contact name
        $displayName = $contactUser->name;
        if (!empty($contactUser->isGroup) && ($contactUser->isGroup == 1 || $contactUser->isGroup === "1" || $contactUser->isGroup === true)) {
            // For groups, always use group_name if available (from join), otherwise use contact name
            if (!empty($contactUser->group_name)) {
                $displayName = $contactUser->group_name;
            } elseif (!empty($contactUser->name) && $contactUser->name != $number) {
                // Use contact name if it's not just the phone number
                $displayName = $contactUser->name;
            } else {
                // Fallback to phone number if nothing else
                $displayName = $number;
            }
        }

        //se o nome for maior que 30 caracteres, corta o nome para mostrar apenas 30 caracteres
        if (strlen($displayName) > 30) {
            $displayName = substr($displayName, 0, 30) . '...';
        }
        $icon = "";
        if ($contactUser->leadid > 0) {
            $camaping = get_soucer_campaign($contactUser->leadid, $contactUser->session);
            if ($camaping && isset($camaping->entryPointConversionApp)) {
            if ($camaping->entryPointConversionApp == "whatsapp") {
                    $icon = "<a href='" . ($camaping->sourceUrl ?? '#') . "' target='_blank' data-toggle='tooltip' data-title='" . ($camaping->title ?? 'WhatsApp') . "'><i class='fa-brands fa-whatsapp'></i></a>";
            } else if ($camaping->entryPointConversionApp == "facebook") {
                    $icon = "<a href='" . ($camaping->sourceUrl ?? '#') . "' target='_blank' data-toggle='tooltip' data-title='" . ($camaping->title ?? 'Facebook') . "'><i class='fa-brands fa-facebook'></i></a>";
            } else if ($camaping->entryPointConversionApp == "instagram") {
                    $icon = "<a href='" . ($camaping->sourceUrl ?? '#') . "' target='_blank' data-toggle='tooltip' data-title='" . ($camaping->title ?? 'Instagram') . "'><i class='fa-brands fa-instagram'></i></a>";
                }
            }
        }


        // Check if chat_marked_read field exists (might be 0 or null if not set)
        $isMarkedRead = isset($contactUser->chat_marked_read) && $contactUser->chat_marked_read == 1;
        $marked_read_class = $isMarkedRead ? 'chat-marked-read' : '';
        $marked_read_icon = '';
        if ($isMarkedRead) {
            $marked_read_icon = '<i class="fa-solid fa-check-circle text-success" title="' . _l("chat_read") . '"></i> ';
        } else if (isset($contactUser->chat_marked_read) && $contactUser->chat_marked_read == 0) {
            // Explicitly marked as unread
            $marked_read_icon = '<i class="fa-solid fa-envelope fa-unread-indicator text-warning" title="' . _l("chat_unread") . '"></i> ';
        }
        
        $iconHtml = !empty($icon) ? "<div class='font-leads-contact'>{$icon}</div>" : "";

        $ln .= "<tr class='contact_{$number} {$marked_read_class}' data-token='{$contactUser->session}' data-id='{$number}' data-lead-id='{$contactUser->leadid}' data-contact-id='{$contactUser->id}' onclick='get_message_contact(\"{$number}\", \"{$contactUser->session}\",\"{$contactUser->isGroup}\")' >
                        <td>                  
                            <span class='chat-contato'>
                                <div>
                                    {$iconHtml}
                                    <img src='" . get_thumb_profile_whats($contactUser->thumb) . "'>
                                </div>
                                <div>
                                    <h1 " . ($contactUser->leadid ? "onclick='init_lead({$contactUser->leadid});return false;'" : "onclick='edit_contact(this,{$contactUser->id});return false;'") . ">
                                        {$marked_read_icon}" . ((!empty($contactUser->isGroup) && ($contactUser->isGroup == 1 || $contactUser->isGroup === "1")) ? $displayName : (!empty($contactUser->leadname) ? $contactUser->leadname : (!empty($displayName) ? $displayName : $number))) . "
                                    </h1>
                                    <h6>" . ($contactUser->isGroup ? "" : $number) . "</h6>
                                    
                                    " . get_last_message_preview($contactUser) . "

                                    " . get_lead_span($contactUser) . "  
                                    " . ($contactUser->assigned ? "<h6><i class='fa-solid fa-user'></i> " . get_staff_full_name($contactUser->assigned) : "")  . "
                                    " . ($contactUser->isGroup ? "<h6><i class='fa-solid fa-people-group'></i> " : "")  . "
                                    </h6>
                                </div>
                                <span class='chat-contato-time'>" . (!empty($contactUser->last_msg_date) ? date('d/m/Y H:i', strtotime($contactUser->last_msg_date)) : date('d/m/Y H:i', strtotime($contactUser->date))) . "</span>                                                            
                                    {$contIsRead}
                        </span>
                    </td>              
                    </tr>";
    }

    return $ln;
}

/**
 * Get last message preview for contact list (WhatsApp style)
 * @param type $contactUser
 * @return string
 */
function get_last_message_preview($contactUser)
{
    // Check if last message fields exist (they may not be loaded for performance)
    if (empty($contactUser->last_msg_date) && !isset($contactUser->last_msg_content)) {
        return '';
    }
    
    // If last_msg_date is not set, assume last messages are not loaded
    if (!isset($contactUser->last_msg_date)) {
        return '';
    }
    
    $preview = '';
    $senderPrefix = '';
    $messageContent = '';
    $messageIcon = '';
    
    // Determine sender prefix
    $isFromStaff = isset($contactUser->last_msg_fromMe) && ($contactUser->last_msg_fromMe == 1 || $contactUser->last_msg_fromMe === "1");
    
    if ($isFromStaff) {
        $senderPrefix = '<i class="fa-solid fa-check text-muted" style="font-size: 10px;"></i> You: ';
    } elseif (!empty($contactUser->last_msg_name)) {
        // For group messages, show sender name
        if (!empty($contactUser->isGroup) && ($contactUser->isGroup == 1 || $contactUser->isGroup === "1")) {
            $senderPrefix = htmlspecialchars($contactUser->last_msg_name) . ': ';
        }
    }
    
    // Handle different message types
    $msgType = isset($contactUser->last_msg_type) ? $contactUser->last_msg_type : 'text';
    
    switch ($msgType) {
        case 'image':
            $messageContent = '<i class="fa-solid fa-photo-film"></i> Photo';
            break;
        case 'video':
            $messageContent = '<i class="fa-regular fa-file-video"></i> Video';
            break;
        case 'audio':
        case 'ptt':
            $messageContent = '<i class="fa-solid fa-microphone"></i> Audio';
            break;
        case 'document':
        case 'file':
            $messageContent = '<i class="fa-regular fa-file"></i> Document';
            break;
        case 'sticker':
            $messageContent = '<i class="fa-solid fa-smile"></i> Sticker';
            break;
        case 'location':
            $messageContent = '<i class="fa-solid fa-location-dot"></i> Location';
            break;
        default:
            // Text message or other
            if (!empty($contactUser->last_msg_content)) {
                // Truncate long messages
                $content = htmlspecialchars($contactUser->last_msg_content);
                if (strlen($content) > 50) {
                    $content = substr($content, 0, 50) . '...';
                }
                // Remove line breaks for preview
                $content = str_replace(["\n", "\r"], ' ', $content);
                $messageContent = $content;
            } else {
                $messageContent = '';
            }
            break;
    }
    
    if (!empty($messageContent)) {
        // Format timestamp (relative time like WhatsApp)
        $msgDate = strtotime($contactUser->last_msg_date);
        $now = time();
        $diff = $now - $msgDate;
        
        $timeDisplay = '';
        if ($diff < 60) {
            $timeDisplay = 'Just now';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            $timeDisplay = $minutes . 'm';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            $timeDisplay = $hours . 'h';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            $timeDisplay = $days . 'd';
        } else {
            // Show date if older than a week
            $timeDisplay = date('d/m/Y', $msgDate);
        }
        
        $preview = '<div class="chat-last-message" style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; font-size: 12px; color: #999;">';
        $preview .= '<span class="chat-message-preview" style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">';
        $preview .= $senderPrefix . $messageContent;
        $preview .= '</span>';
        $preview .= '<span class="chat-message-time" style="margin-left: 8px; color: #666; font-size: 11px; white-space: nowrap;">' . $timeDisplay . '</span>';
        $preview .= '</div>';
    }
    
    return $preview;
}

// function monta_html_contact_banco($dados)
// {

//     $ln = null;
//     $contIsRead = null;
//     foreach ($dados as $index => $contactUser) {
//         if ($contactUser->isread > 0) {
//             $contIsRead = "<span class='badge badge-primary chat-contato-unread'>{$contactUser->isread}</span>";
//         } else {
//             $contIsRead = "<span class='badge badge-primary chat-contato-unread'></span>";
//         }

//         $number = $contactUser->phonenumber;
//         //pega os dados do lead
//         //$lead = get_dados_leads_phone($number);

//         //se o nome for maior que 30 caracteres, corta o nome para mostrar apenas 30 caracteres
//         if (strlen($contactUser->name) > 30) {
//             $contactUser->name = substr($contactUser->name, 0, 30) . '...';
//         }

//             $camaping = get_soucer_campaign($contactUser->leadid, $contactUser->session);

//             if ($camaping->entryPointConversionApp == "whatsapp") {
//                 $icon = "<a href='{$camaping->sourceUrl}' target='_blank' data-toggle='tooltip' data-title='{$camaping->title}'><i class='fa-brands fa-whatsapp'></i></a>";
//             } else if ($camaping->entryPointConversionApp == "facebook") {
//                 $icon = "<a href='{$camaping->sourceUrl}' target='_blank' data-toggle='tooltip' data-title='{$camaping->title}'><i class='fa-brands fa-facebook'></i></a>";
//             } else if ($camaping->entryPointConversionApp == "instagram") {
//                 $icon = "<a href='{$camaping->sourceUrl}' target='_blank' data-toggle='tooltip' data-title='{$camaping->title}' ><i class='fa-brands fa-instagram'></i></a>";
//             } else {
//                 $icon = "";
//             }

//             $ln .= "<tr class='contact_{$number}' data-token='{$contactUser->session}' data-id='{$number}' data-lead-id='{$contactUser->leadid}'  onclick='get_message_contact(\"{$number}\", \"{$contactUser->session}\",\"{$contactUser->isGroup}\")' >
//                 <td>                  
//                     <span class='chat-contato'>
//                         <div>
//                             <div class='font-leads-contact'>                                                              
//                                   $icon                              
//                             </div>
//                             <img src='" . get_thumb_profile_whats(($contactUser->contactthumb ? $contactUser->contactthumb : $contactUser->contactcenter_thumb)) . "'>
//                         </div>
//                         <div>
//                             <h1 ".($contactUser->leadid ? "onclick='init_lead({$contactUser->leadid});return false;'" : "" )."  >" . ($contactUser->name ? $contactUser->name : $contactUser->contactname) . "</h1>
//                             <h6>{$number}</h6>

//                             " . get_lead_span($contactUser) . "  
//                             " .($contactUser->assigned ? "<h6><i class='fa-solid fa-user'></i> ".get_staff_full_name($contactUser->assigned) : "<i class='fa-solid fa-people-group'></i>" )  . "</h6>
//                         </div>
//                         <span class='chat-contato-time'>" . date('d/m/Y H:i', strtotime($contactUser->date)) . "</span>                                                            
//                             {$contIsRead}
//                    </span>
//                </td>              
//             </tr>";

//     }
//     return $ln;
// }




function monta_html_contact_messenger($dados)
{
    $ln = null;
    $contIsRead = null;
    foreach ($dados as $index => $contactUser) {

        if ($contactUser->count > 0) {
            $contIsRead = "<span class='badge badge-primary chat-contato-unread'>{$contactUser->count}</span>";
        } else {
            $contIsRead = "<span class='badge badge-primary chat-contato-unread'></span>";
        }


        //se o nome for maior que 30 caracteres, corta o nome para mostrar apenas 30 caracteres
        if (strlen($contactUser->name) > 30) {
            $contactUser->name = substr($contactUser->name, 0, 30) . '...';
        }

        if ($contactUser->source == "facebook") {
            $icon = "<i class='fa-brands fa-facebook'></i>";
        } else if ($contactUser->source == "instagram") {
            $icon = "<i class='fa-brands fa-instagram'></i>";
        } else {
            $icon = "";
        }

        $number = $contactUser->sender_id;
        $ln .= "<tr class='contact_{$number}'  data-id='{$number}' data-lead-id='{$contactUser->leadid}'  onclick='get_message_messenger(\"{$number}\",\"{$contactUser->page_id}\")' >
                <td>                  
                    <span class='chat-contato'>
                        <div>
                            <div class='font-leads-contact'>                                                              
                                  $icon                              
                            </div>
                            <img src='" . get_thumb_profile_whats($contactUser->contactcenter_thumb) . "'>
                        </div>
                        <div>
                            <h1 onclick='init_lead({$contactUser->leadid});return false;' >" . ($contactUser->name ? $contactUser->name : $number) . "</h1> 
                             " . get_lead_span($contactUser) . " 
                            <h6 class='chat-contato-source'> {$contactUser->source}                                    </h6>
                                </div>
                                <span class='chat-contato-time'>" . (!empty($contactUser->last_msg_date) ? date('H:i', strtotime($contactUser->last_msg_date)) : date('d/m/Y H:i', strtotime($contactUser->date))) . "</span>                                                            
                                    {$contIsRead}
                   </span>
               </td>              
            </tr>";
    }
    return $ln;
}

function get_soucer_campaign($lead_id = null, $session = null)
{
    $CI = &get_instance();
    $CI->db->where('lead_id', $lead_id);
    $CI->db->where('session', $session);
    $lead = $CI->db->get(db_prefix() . 'contactcenter_meta')->row();
    if ($lead) {
        return $lead;
    }
    return false;
}

function get_transferir_accepted($trans_id = null)
{
    if ($trans_id) {
        $CI = &get_instance();
        $CI->db->where('trans_id', $trans_id);
        $CI->db->where('trans_accepted', 1);
        return $CI->db->get(db_prefix() . 'contactcenter_atendimento_trans')->row();
    } else {
        return false;
    }
}


/**
 * Monta o contato via websocket 
 * @param type $dados
 * @return string
 */
function monta_html_contact_websocket($dados)
{
    $ln = null;



    $timestamp = $dados['date_time'];
    $numero_telefone = null;
    $formattedDate = str_replace("T", " ", $timestamp);
    $formattedDate = str_replace("Z", "", $formattedDate);
    $date = date('d-m-Y H:i', strtotime($formattedDate));


    //verifica se é grupo ou individual
    if (str_ends_with($dados["data"]["key"]["remoteJid"], '@g.us')) {
        $isGroupMsg = true;
        $groupId = str_replace('@g.us', '', $dados["data"]["key"]["remoteJid"]);
    } else {
        $isGroupMsg = false;
        $groupId = null;
        //limpa o numero do telefone
        preg_match('/(\d+)/', $dados["data"]["key"]["remoteJid"], $matches);
        $numero_telefone = $matches[0];
    }


    if ($dados) {

        $number = $numero_telefone;
        //pega os dados do lead
        if (!$isGroupMsg) {
            $lead = get_dados_leads_phone($number);
        } else {
            $lead = "";
        }




        if ($lead  && !$isGroupMsg) {
            // Get contact info for chat_marked_read status
            $CI = &get_instance();
            $CI->db->where('phonenumber', $number);
            $CI->db->where('session', $dados["apikey"]);
            $contact = $CI->db->get(db_prefix() . 'contactcenter_contact')->row();
            
            // If contact doesn't exist, create it
            if (!$contact) {
                $CI->db->insert(db_prefix() . 'contactcenter_contact', [
                    'phonenumber' => $number,
                    'session' => $dados["apikey"],
                    'name' => $lead->name ? $lead->name : '',
                    'leadid' => $lead->id,
                    'date' => date('Y-m-d H:i:s'),
                    'chat_marked_read' => 0
                ]);
                $contactId = $CI->db->insert_id();
            } else {
                $contactId = (int)$contact->id;
            }
            
            $isMarkedRead = $contact && isset($contact->chat_marked_read) && $contact->chat_marked_read == 1;
            $marked_read_class = $isMarkedRead ? 'chat-marked-read' : '';
            $marked_read_icon = '';
            if ($isMarkedRead) {
                $marked_read_icon = '<i class="fa-solid fa-check-circle text-success" title="' . _l("chat_read") . '"></i> ';
            } else if ($contact && isset($contact->chat_marked_read) && $contact->chat_marked_read == 0) {
                // Explicitly marked as unread
                $marked_read_icon = '<i class="fa-solid fa-envelope fa-unread-indicator text-warning" title="' . _l("chat_unread") . '"></i> ';
            }
            
            $ln .= "<tr class='contact_{$number} {$marked_read_class}' data-id='{$number}' data-contact-id='{$contactId}'>
                    <td>                
                        <div class='contact_{$number}' data-token='{$dados["apikey"]}' data-lead-id='{$lead->id}' data-id='{$number}' onclick='get_message_contact(\"{$number}\", \"{$dados["apikey"]}\",\"0\")'>
                            <span class='chat-contato'>
                                <div>
                                    <img src='" . get_thumb_profile_whats() . "'>
                                </div>
                                <div>
                                    <h1 onclick='init_lead({$lead->id});return false;' >{$marked_read_icon}" . ($lead->name ? $lead->name : "") . "</h1>
                                    <h6>{$number}</h6>
                                    " . check_client_leads_whtas($number) . "
                                </div>
                                <span class='chat-contato-time'>" . $date . "</span>                                                            
                                <span class='badge badge-primary chat-contato-unread'>1</span>
                                <h6><i class='fa-solid fa-user'></i> " . get_staff_full_name($lead->assigned) . "</h6>
                        </span>
                        </div>
                     </td>           
                </tr>";
        } elseif (!$lead  && $isGroupMsg) {
            $group =  contactcenter_get_contact($groupId);
            $number = $groupId;


            $ln .= "<tr class='contact_{$number}' data-token='{$group->session}' data-id='{$number}'  onclick='get_message_contact(\"{$number}\", \"{$group->session}\",\"1\")' >
                    <td>                  
                        <span class='chat-contato'>
                            <div>                               
                                <img src='" . get_thumb_profile_whats($group->thumb) . "'>
                            </div>
                            <div>
                                <h1>" . ($group->name ? $group->name : $dados["data"]["pushName"]) . "</h1> 
                                <span class='text-success'>" . _l("customer_groups") . "</span>
                            </div>
                            <span class='chat-contato-time'>" . date('d/m/Y H:i', strtotime($group->date)) . "</span>                                                            
                            <span class='badge badge-primary chat-contato-unread'>1</span>
                        </span>
                    </td>              
                </tr>";
        } else {
            // Get contact info for chat_marked_read status
            $CI = &get_instance();
            $CI->db->where('phonenumber', $number);
            $CI->db->where('session', $dados["apikey"]);
            $contact = $CI->db->get(db_prefix() . 'contactcenter_contact')->row();
            
            // If contact doesn't exist, create it
            if (!$contact) {
                $CI->db->insert(db_prefix() . 'contactcenter_contact', [
                    'phonenumber' => $number,
                    'session' => $dados["apikey"],
                    'name' => $dados["data"]["pushName"] ? $dados["data"]["pushName"] : '',
                    'date' => date('Y-m-d H:i:s'),
                    'chat_marked_read' => 0
                ]);
                $contactId = $CI->db->insert_id();
            } else {
                $contactId = (int)$contact->id;
            }
            
            $isMarkedRead = $contact && isset($contact->chat_marked_read) && $contact->chat_marked_read == 1;
            $marked_read_class = $isMarkedRead ? 'chat-marked-read' : '';
            $marked_read_icon = '';
            if ($isMarkedRead) {
                $marked_read_icon = '<i class="fa-solid fa-check-circle text-success" title="' . _l("chat_read") . '"></i> ';
            } else if ($contact && isset($contact->chat_marked_read) && $contact->chat_marked_read == 0) {
                // Explicitly marked as unread
                $marked_read_icon = '<i class="fa-solid fa-envelope fa-unread-indicator text-warning" title="' . _l("chat_unread") . '"></i> ';
            }
            
            $ln .= "<tr class='contact_{$number} {$marked_read_class}' data-id='{$number}' data-contact-id='{$contactId}'>
                        <td>                            
                            <div class='contact_{$number}' data-token='{$dados["apikey"]}' data-id='{$number}' onclick='get_message_contact(\"{$number}\", \"{$dados["apikey"]}\",\"0\")'>
                                <span class='chat-contato'>
                                    <div>
                                        <img src='" . get_thumb_profile_whats() . "'>
                                    </div>
                                    <div>
                                        <h1>{$marked_read_icon}" . ($dados["data"]["pushName"] ? $dados["data"]["pushName"] : "") . "</h1>
                                        <h6>{$number}</h6>
                                        " . check_client_leads_whtas($number) . "
                                    </div>
                                    <span class='chat-contato-time'>" . $date . "</span>                                                            
                                </span>
                            </div>
                        </td>           
                    </tr>";
        }

        return $ln;
    }
}

/**
 * Monta o contato via pusher 
 * @param type $dados
 * @return string
 */
function monta_html_contact_pusher($dados)
{
    $ln = null;

    if ($dados) {

        $number = $dados->phonenumber;
        //pega os dados do lead
        $lead = get_dados_leads_phone($number);
        if ($dados->isread > 0) {
            $contIsRead = "<span class='badge badge-primary chat-contato-unread'>{$dados->isread}</span>";
        } else {
            $contIsRead = null;
        }

        if ($dados->transferid) {
            $transfer = get_transferir_accepted($dados->transferid);
            $isonTransfer = "<span class='badge badge-primary chat-contato-unread' style='right: 35px;' data-toggle='tooltip' data-title='{$transfer->trans_desc}' ><i class='fa-solid fa-right-left'></i></span>";

            if ($transfer->trans_status == 2) {
                $bg_status = "style='background-color: rgb(255, 255, 0,0.5);'";
            } else if ($transfer->trans_status == 3) {
                $bg_status = "style='background-color: rgb(255,0,0,0.5);'";
            } else {
                $bg_status = "";
            }

            if ($transfer->trans_accepted == 0) {
                $accept = "j_accept";
                $onclick = '';
            } else {
                $onclick = 'onclick="get_message_contact(\'' . $number . '\', \'' . $transfer->dev_token . '\', \'0\', \'' . $transfer->staffid_from . '\')"';
                $accept = "";
            }

            $ln .= "<tr class='{$accept}'  data-token='{$dados->session}' data-transid='{$transfer->trans_id}' data-from='{$dados->staffid_from}' data-lead-id='{$lead->id}' data-id='{$number}'  >
                            <td>
                                <span style='display: none'>{$dados->date}</span>
                                <div class='contact_{$number}' $onclick'>
                                    <span class='chat-contato' $bg_status>
                                        <div>
                                            <img src='" . get_thumb_profile_whats() . "'>
                                        </div>
                                        <div>
                                            <h1 onclick='init_lead({$lead->id});return false;' >" . ($lead->name ? $lead->name : "") . "</h1>
                                            <h6>{$number}</h6>
                                            " . check_client_leads_whtas($number) . "  
                                            <h6><i class='fa-solid fa-triangle-exclamation'></i> " . contactcenter_status_transferir($transfer->trans_status)["label"] . "</h6>                          
                                        </div>
                                        <span class='chat-contato-time'>" . date('d/m/Y H:i', strtotime($dados->date)) . "</span> 
                                        {$isonTransfer}
                                        {$contIsRead}
                                </span>
                                </div>
                            </td>                          
                        </tr>";
        } else {

            $contIsRead = "<span class='badge badge-primary chat-contato-unread'>{$dados->isread}</span>";
            $ln .= "<tr>
                <td>
                <span style='display: none'>{$dados->date}</span>
                <div class='contact_{$number}' data-token='{$dados->session}' data-lead-id='{$lead->id}' data-id='{$number}' onclick='get_message_contact(\"{$number}\", \"{$dados->session}\",\"0\")'>
                    <span class='chat-contato'>
                        <div>
                            <img src='" . ($lead->contactcenter_thumb ? $lead->contactcenter_thumb : get_thumb_profile_whats()) . "'>
                        </div>
                        <div>
                            <h1 onclick='init_lead({$lead->id});return false;' >" . ($lead->name ? $lead->name : "") . "</h1>
                            <h6>{$number}</h6>
                            " . check_client_leads_whtas($number) . "
                        </div>
                        <span class='chat-contato-time'>" . date('d/m/Y H:i', strtotime($dados->date)) . "</span>                                                            
                            {$contIsRead}
                </span>
                </div>
            </td>           
            </tr>";
        }





        $result["msg_fromMe"] = $dados->msg_fromMe;
        $result["contact"] = $ln;
        $result["contact_id"] = "contact_{$number}";
        $result["contact_onclick"] = "get_message_contact(\"{$number}\", \"{$dados->session}\",\"0\")";
        return $result;
    }
}

/**
 * Monta o contato via pusher 
 * @param type $dados
 * @return string
 */
function monta_html_contact_transferido($dados)
{
    $ln = null;

    foreach ($dados as $index => $contactUser) {
        $contIsRead = "<span class='badge badge-primary chat-contato-unread' data-toggle='tooltip' data-title='{$contactUser->trans_desc}' ><i class='fa-solid fa-right-left'></i></span>";

        $number = $contactUser->phonenumber;
        //pega os dados do lead
        $lead = get_dados_leads_phone($number);

        if ($contactUser->trans_status == 2) {
            $bg_status = "style='background-color: rgb(255, 255, 0,0.5);'";
        } else if ($contactUser->trans_status == 3) {
            $bg_status = "style='background-color: rgb(255,0,0,0.5);'";
        } else {
            $bg_status = "";
        }
        if ($contactUser->trans_accepted == 0) {
            $accept = "j_accept";
            $onclick = '';
        } else {
            $onclick = 'onclick="get_message_contact(\'' . $number . '\', \'' . $contactUser->dev_token . '\', \'0\', \'' . $contactUser->staffid_from . '\')"';
            $accept = "";
        }

        $ln .= "<tr class='{$accept}'  data-token='{$contactUser->dev_token}' data-transid='{$contactUser->trans_id}' data-from='{$contactUser->staffid_from}' data-lead-id='{$lead->id}' data-id='{$number}'  >
                    <td>
                        <div id='contact_{$number}' $onclick'>
                            <span style='display: none'>{$contactUser->trans_date}</span>
                            <span class='chat-contato' $bg_status>
                                <div>
                                    <img src='" . get_thumb_profile_whats() . "'>
                                </div>
                                <div>
                                    <h1 onclick='init_lead({$lead->id});return false;' >" . ($lead->name ? $lead->name : "") . "</h1>
                                    <h6>{$number}</h6>
                                    " . check_client_leads_whtas($number) . "  
                                    <h6><i class='fa-solid fa-triangle-exclamation'></i> " . contactcenter_status_transferir($contactUser->trans_status)["label"] . "</h6>                          
                                </div>
                                <span class='chat-contato-time'>" . date('d/m/Y H:i', strtotime($contactUser->trans_date)) . "</span> 
                                {$contIsRead}
                        </span>
                        </div>
                    </td>
                </tr>";
    }



    return $ln;
}

/**
 * mosta o html do gupos no contato 
 * @param type $devicetoken
 * @return string
 */
function monta_html_all_group($dados)
{
    $ln = null;
    $contIsRead = null;
    foreach ($dados as $index => $contactUser) {
        //pega uma conversa para pegar a foto e name do grupo 
        // $group = get_chat_single($contactUser["id"], $devicetoken);

        if ($contactUser->unread_count >= 1) {
            $contIsRead = "<span class='badge badge-primary chat-contato-unread'>{$contactUser->unread_count}</span>";
        } else {
            $contIsRead = null;
        }
        $number = ($contactUser->msg_fromMe ? $contactUser->msg_to : $contactUser->msg_from);

        $group = get_name_group_whats($number, $contactUser->msg_session);
        $ln .= "<tr onclick='get_message_contact(\"{$number}\", \"{$contactUser->msg_session}\",\"1\")' >
                <td>
                    <span style='display: none'>{$contactUser->msg_date}</span>
                    <span class='chat-contato'>
                        <div>
                            <img src='" . get_thumb_profile_whats() . "'>
                        </div>
                        <div>                          
                            <h6>" . ($group ? $group : "ID: " . $number) . "</h6>                           
                        </div> 
                            {$contIsRead}
                   </span>
               </td>
            </tr>";
    }
    return $ln;
}

/**
 * Verifica se a conversa já foi lida 
 * @param type $phoneNumber
 * @param type $token
 * @return type
 */
function get_isread_whats_all($phoneNumber, $token)
{
    $CI = &get_instance();
    $CI->db->select("COUNT(msg_id) as total");
    $CI->db->where('msg_isread', 0);
    $CI->db->where('msg_session', $token);
    $CI->db->like('msg_send_id', $phoneNumber);
    $Isread = $CI->db->get(db_prefix() . 'contactcenter_message')->row();
    return $Isread->total;
}

function get_thumb_whats_online($phoneNumber, $tokenDevice)
{
    $curl = curl_init();

    $data = array(
        'number' => $phoneNumber
    );

    $json_data = json_encode($data);
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://cluster.apigratis.com/api/v2/whatsapp/getProfilePic",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $json_data,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer " . get_option("tokenBearer_contactcenter"),
            "DeviceToken: {$tokenDevice}"
        ),
    ));

    $response = curl_exec($curl);
    $thumb = json_decode($response, true);
    curl_close($curl);
    return $thumb;
}

function get_chat_single($phoneNumber, $tokenDevice)
{
    $curl = curl_init();

    $data = array(
        'number' => $phoneNumber,
        "count" => 1
    );

    $json_data = json_encode($data);
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://cluster.apigratis.com/api/v2/whatsapp/getChat",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $json_data,
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer " . get_option("tokenBearer_contactcenter"),
            "DeviceToken: {$tokenDevice}"
        ),
    ));

    $response = curl_exec($curl);
    $thumb = json_decode($response, true);
    curl_close($curl);
    return $thumb["response"]["data"];
}

/**
 * faz verificação de dias 
 * @param type $data
 * @return type
 */
function adicionarDiaUtil($data)
{
    $nova_data = $data;
    // Verificar se a nova data é um fim de semana (sábado ou domingo)
    while (verificarFimDeSemana($nova_data)) {
        // Se for fim de semana, adicionar mais 1 dia
        $nova_data = date('Y-m-d H:i', strtotime($nova_data . ' +1 day'));
    }
    // Se a nova data cair em um sábado, adicionar mais 2 dias para que o agendamento seja na próxima segunda-feira
    if (date('w', strtotime($nova_data)) == 6) { // 6 = sábado
        $nova_data = date('Y-m-d H:i', strtotime($nova_data . ' +2 days'));
    }
    // Se a nova data cair em um domingo, adicionar mais 1 dia para que o agendamento seja na próxima segunda-feira
    if (date('w', strtotime($nova_data)) == 0) { // 0 = domingo
        $nova_data = date('Y-m-d H:i', strtotime($nova_data . ' +1 day'));
    }
    return $nova_data;
}

/**
 * Verifica de o Dia se de sabado ou domingo 
 * @param type $data
 * @return type
 */
function verificarFimDeSemana($data)
{
    // Obter o dia da semana da data fornecida (0 = domingo, 6 = sábado)
    $dia_da_semana = date('w', strtotime($data));
    // Verificar se é sábado (6) ou domingo (0)
    return $dia_da_semana == 0 || $dia_da_semana == 6;
}

/**
 * Notifica o staff que o lead precisa de uma atendimento
 * @param type $phonenumberLead
 * @param type $staffid
 * @return type
 */
function notification_atendente_ai_whatsapp($phonenumberLead, $staffid)
{

    $CI = &get_instance();
    if ($phonenumberLead) {
        $CI->db->like('phonenumber', $phonenumberLead);
        $lead = $CI->db->get(db_prefix() . 'leads')->row();
    }

    if ($staffid) {
        $CI->db->like('staffid', $staffid);
        $user = $CI->db->get(db_prefix() . 'staff')->row();
    }
    $aviso = "Olá {$user->firstname} {$user->lastname} o lead: {$lead->id} {$lead->name} precisa de um horário especial por favor, assumir o atendimento!";
    $sucesso = [
        "aviso" => $aviso,
        "phonenumber" => $user->phonenumber,
    ];
    return $sucesso;
}

function get_name_assistant($id)
{
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $result = $CI->db->get(db_prefix() . 'contactcenter_assistants_ai')->row();

    return $result->ai_name;
}

function contactcenter_get_name_status_lead($status)
{
    $CI = &get_instance();
    //$CI->db->where('id', $status);
    $result = $CI->leads_model->get_status($status);

    return $result->name;
}

function contactcenter_get_name_source_lead($source)
{
    $CI = &get_instance();
    $result = $CI->leads_model->get_source($source);
    return $result->name;
}

function get_count_error_engine_conversation($id)
{
    $CI = &get_instance();
    $CI->db->where('con_id', $id);
    $result = $CI->db->get(db_prefix() . 'contactcenter_conversation_engine_error');

    return $result->num_rows();
}

function get_device_name($id)
{
    $CI = &get_instance();
    $CI->db->where('dev_id', $id);
    $result = $CI->db->get(db_prefix() . 'contactcenter_device')->row();

    return $result->dev_name;
}

function contactcenter_status_transferir($status_id = null)
{
    $status = [
        "1" => [
            "label" => _l("contact_trans_prioridade_normal"),
            "color" => "rgba(0, 0, 0, 0)"
        ],
        "2" => [
            "label" => _l("contact_trans_prioridade_baixa"),
            "color" => "#FFFF00"
        ],
        "3" => [
            "label" => _l("contact_trans_prioridade_urgente"),
            "color" => "#FF0000" // Cor para o status 3 (vermelho)
        ]
    ];

    if ($status_id !== null && isset($status[$status_id])) {
        return $status[$status_id];
    }

    return $status;
}

function label_status_device($status)
{

    if ($status == "inChat" || $status == "open") {
        $div = "<span class='label s-status label-success '>" . _l("device_conectado") . "</span>";
    } else {
        $div = "<span class='label label-danger status-reconect-device s-status'>" . _l("device_reconectar") . "</span>";
    }
    return $div;
}

function label_status_device_online($status)
{

    if ($status == "inChat" || $status == "open") {
        $div = "<span class='label s-status label-success'><i class='fas fa-satellite-dish'></i></span>";
    } else {
        $div = "<span class='label label-danger status-reconect-device s-status'><i class='fa-regular fa-circle-xmark'></i></span>";
    }
    return $div;
}

/**
 * Get disconnected devices for header widget
 * @param int $staffid Staff ID (null for current user)
 * @param bool $admin_view If true, get all devices (admin only)
 * @return array Array of disconnected devices
 */
function get_disconnected_devices($staffid = null, $admin_view = false)
{
    $CI = &get_instance();
    $CI->load->model('contactcenter/contactcenter_model');
    
    // Get devices
    if ($admin_view && is_admin()) {
        $devices = $CI->contactcenter_model->get_device();
    } else {
        $staffid = $staffid ? $staffid : get_staff_user_id();
        $devices = $CI->contactcenter_model->get_device(null, $staffid);
    }
    
    $disconnected = [];
    if ($devices) {
        foreach ($devices as $device) {
            // Check if device is disconnected (not "open" or "inChat")
            // AND only include if device is active (exclude inactive devices when disconnected)
            $is_active = isset($device->is_active) ? $device->is_active : 1;
            if ($device->status != "open" && $device->status != "inChat" && $device->status != "connecting" && $is_active == 1) {
                // Add staff name and profile image
                if (isset($device->staffid) && $device->staffid) {
                    $device->staff_name = get_staff_full_name($device->staffid);
                    // Get profile image URL
                    if (function_exists('staff_profile_image_url')) {
                        $device->staff_profile_image = staff_profile_image_url($device->staffid, 'small');
                    } else {
                        $device->staff_profile_image = base_url('assets/images/user-placeholder.jpg');
                    }
                } else {
                    $device->staff_profile_image = base_url('assets/images/user-placeholder.jpg');
                }
                
                // Add device color based on device ID for visual distinction
                $device->device_color = generate_device_color($device->dev_id);
                
                $disconnected[] = $device;
            }
        }
    }
    
    return $disconnected;
}

/**
 * Generate a consistent color for a device based on its ID
 * @param int $device_id Device ID
 * @return string Hex color code
 */
function generate_device_color($device_id)
{
    // Generate a color based on device ID for consistency
    $colors = [
        '#ef4444', // red-500
        '#f59e0b', // amber-500
        '#10b981', // emerald-500
        '#3b82f6', // blue-500
        '#8b5cf6', // violet-500
        '#ec4899', // pink-500
        '#06b6d4', // cyan-500
        '#f97316', // orange-500
    ];
    
    $index = ($device_id - 1) % count($colors);
    return $colors[$index];
}

function status_drawflow($status)
{

    if ($status == 1) {
        $div = "<span class='label s-status label-success '>" . _l("drawflow_flow_active") . "</span>";
    } else {
        $div = "<span class='label label-warning'>" . _l("drawflow_flow_sketch") . "</span>";
    }
    return $div;
}

function monta_html_engine_error($dados)
{
    $ln = null;

    foreach ($dados as $index => $engine_error) {

        $tdPhoneNumber = "<td>{$engine_error->lead_phonumber}</td>";
        if ($engine_error->invalid_number == 1) {
            $tdPhoneNumber = "<td>
                                <div class='box_thumbTlabeCommunity'> 
                                    <div>    
                                        {$engine_error->lead_phonumber}
                                        <div class='row-options'>   
                                            <input type='text' class='form-control' name='phone' placeholder='Digite o novo número'> <button class='btn btn-primary' onclick='change_phone({$engine_error->error_id},{$engine_error->lead_id},{$engine_error->con_id} )'>" . _l("contac_alterar") . "</button>  
                                        </div>        
                                    </div>    
                                </div>    
                            </td>";
        }

        $ln .= "<tr class='engine_error_{$engine_error->error_id}'>
                <td>{$engine_error->lead_id}</td>
                <td>{$engine_error->lead_name}</td>
                $tdPhoneNumber
                <td>{$engine_error->error_message}</td>
            </tr>";
    }
    return $ln;
}

function base64ToImage($base64_string, $output_file)
{
    // Abre o arquivo para escrita (modo binário)
    $ifp = fopen($output_file, 'wb');

    // Divida a string base64 em suas partes
    $data = explode(',', $base64_string);

    // Decodifique a parte que contém a base64
    fwrite($ifp, base64_decode($data[1]));

    // Fecha o arquivo
    fclose($ifp);

    return $output_file;
}

function monta_html_engine_leas($dados)
{
    $ln = null;

    foreach ($dados as $index => $lead) {

        $ln .= "<tr class='engine_lead_{$lead->id}'>
                <td>{$lead->id}</td>
                <td>{$lead->name}</td>
                <td>{$lead->phonenumber}</td>
               
            </tr>";
    }
    return $ln;
}

function monta_html_valid_engine($messages)
{
    $ln = null;

    foreach ($messages as $index => $message) {

        $ln .= "<div>
                <p>{$message}</p>
            </div>";
    }

    if ($ln == null) {
        $ln = "<div>
                <p>" . _l("contact_valid_engine") . "</p>
            </div>";
    }

    return $ln;
}

function get_currencies_default()
{
    $CI = &get_instance();
    $CI->db->where('isdefault', 1);
    $result = $CI->db->get(db_prefix() . 'currencies')->row();
    return $result->symbol;
}



function get_sesson_leads_chat($leadid)
{
    // if ($leadid) {
    //     $CI = &get_instance();
    //     $CI->db->select('session, ANY_VALUE(id) as id, ANY_VALUE(phonenumber) as phonenumber');
    //     $CI->db->where('leadid', $leadid);
    //     $CI->db->group_by('session');
    //     return $CI->db->get(db_prefix() . 'contactcenter_contact')->result();
    // }
    $CI = &get_instance();

    $sql = "
            SELECT 
                c.session, 
                MIN(c.id) AS id, 
                MIN(c.phonenumber) AS phonenumber,
                d.staffid
            FROM " . db_prefix() . "contactcenter_contact c
            LEFT JOIN " . db_prefix() . "contactcenter_device d ON c.session = d.dev_token
            WHERE c.leadid = " . (int) $leadid . "
            GROUP BY c.session, d.staffid
        ";
    $query = $CI->db->query($sql);
    return $query->result();
}



function get_device_token($token)
{
    $CI = &get_instance();
    $CI->db->where('dev_token', $token);
    return $CI->db->get(db_prefix() . 'contactcenter_device')->row();
}

function get_conversas_leads($phonenunber, $session)
{
    if ($phonenunber) {
        $CI = &get_instance();
        $CI->db->where('msg_isGroupMsg', 0);
        $CI->db->where('msg_session', $session);
        $CI->db->where('msg_conversation_number', $phonenunber);
        $CI->db->order_by('msg_id', "DESC");
        $CI->db->limit(100);
        return $CI->db->get(db_prefix() . 'contactcenter_message')->result();
    }
}


/**
 * get_status_leads_chat 
 *
 * @param [int] $phoneNumber
 * @param [int] $token
 * @return void
 */
function get_status_leads_chat($phoneNumber, $token)
{

    if ($phoneNumber) {
        /**
         * pega os status
         */
        $result = get_status_leads_by_id();
        $ln = null;

        /**
         * pega os leads
         */
        $CI = &get_instance();
        $CI->db->select("status");
        $CI->db->select("id");
        $CI->db->where("phonenumber", $phoneNumber);
        $lead = $CI->db->get(db_prefix() . 'leads')->row();

        $lead_status = get_status_leads_by_id($lead->status);
        if ($result) {
            foreach ($result as  $status) {

                if ($lead_status->id == $status->id) {
                    $class = "active";
                } else {
                    if ($status->statusorder < $lead_status->statusorder) {
                        $class = "completed";
                    } else {
                        $class = "";
                        $onclick = "onclick='lead_mark_as(\"{$status->id}\",\"{$lead->id}\");'";
                        $datatoken = "data-token='{$token}' data-id='{$phoneNumber}'";
                    }
                }
                $ln .= "<div class='step {$class}' {$onclick} {$datatoken} data-toggle='tooltip' data-title='{$status->name}'>{$status->name}</div>";
            }
        }

        return $ln;
    }
}
/**
 * get_status_leads_chat
 */
function get_status_leads_messenger($sender_id)
{

    if ($sender_id) {
        /**
         * pega os status
         */
        $result = get_status_leads_by_id();
        $ln = null;

        /**
         * pega os leads
         */
        $CI = &get_instance();
        $CI->db->select("status");
        $CI->db->select("id");
        $CI->db->select("lead_messenger_id");
        $CI->db->where("lead_messenger_id", $sender_id);
        $lead = $CI->db->get(db_prefix() . 'leads')->row();

        $lead_status = get_status_leads_by_id($lead->status);
        if ($result) {
            foreach ($result as  $status) {

                if ($lead_status->id == $status->id) {
                    $class = "active";
                } else {
                    if ($status->statusorder < $lead_status->statusorder) {
                        $class = "completed";
                    } else {
                        $class = "";
                        $onclick = "onclick='lead_mark_as(\"{$status->id}\",\"{$lead->id}\");'";
                        $datatoken = "data-id='{$lead->lead_messenger_id}'";
                    }
                }
                $ln .= "<div class='step {$class}' {$onclick} {$datatoken} data-toggle='tooltip' data-title='{$status->name}'>{$status->name}</div>";
            }
        }

        return $ln;
    }
}

/**
 * get_status_leads_by_id 
 *
 * @param [int] $id
 * @return void
 */
function get_status_leads_by_id($id = null)
{
    if ($id) {
        $CI = &get_instance();
        $CI->db->where("id", $id);
        $CI->db->where("active", 1);
        $CI->db->order_by("statusorder", "ASC");
        return $CI->db->get(db_prefix() . 'leads_status')->row();
    } else {
        $CI = &get_instance();
        $CI->db->where("active", 1);
        $CI->db->order_by("statusorder", "ASC");
        return $CI->db->get(db_prefix() . 'leads_status')->result();
    }
}

/**
 * notication_geral 
 * success
 * info
 * alert
 * error
 * @param [string] $mensagem
 * @param [string] $class
 * @param [int] $staffid
 * @return void
 */
function notication_geral($mensagem, $class = null, $staffid = null)
{
    if ($mensagem) {
        $CI = &get_instance();
        $CI->load->library('app_pusher');
        $name = "{$_SERVER["SERVER_NAME"]}_noticacao_geral";

        if (!$class) {
            $class = "success";
        }

        $msg = "<div class='trigger_{$class} trigger_alert_notification trigger_ajax'>$mensagem</div>";
        if ($staffid) {
            $noticacao_staff = "noticacao_staff_{$staffid}";
            $CI->app_pusher->trigger($name, $noticacao_staff, $msg);
        } else {
            $CI->app_pusher->trigger($name, 'noticacao_geral', $msg);
        }
        return true;
    }
}


/**
 * formatPhoneNumber
 * Remove qualquer caractere que não seja número e verifica se tem o numero 9 no inicio
 * @param [type] $number
 * @return void
 */
function formatPhoneNumber($number, $isBrasil = false, $country_code = null)
{
    // Remove qualquer caractere que não seja número
    $number = preg_replace('/\D/', '', $number);
    
    // If country code is provided, use it for formatting
    if ($country_code !== null && !empty($country_code)) {
        $country_code = preg_replace('/[^0-9]/', '', $country_code);
        
        // Check if number already starts with country code
        if (substr($number, 0, strlen($country_code)) != $country_code) {
            // Add country code if not present
            $number = $country_code . $number;
        }
        
        // Special formatting for Brazil (55)
        if ($country_code == '55') {
            // Remove the country code temporarily for processing
            if (substr($number, 0, 2) == '55') {
                $number_without_code = substr($number, 2);
                
                // Separate DDD and the rest
                $ddd = substr($number_without_code, 0, 2);
                $rest = substr($number_without_code, 2);
                
                // Check if DDD is greater than 30
                if ($ddd > 30) {
                    // Remove digit 9 if present and if length is 9
                    if (strlen($rest) == 9 && $rest[0] == '9') {
                        $rest = substr($rest, 1);
                    }
                }
                
                // Return formatted number with country code
                return "55" . $ddd . $rest;
            }
        }
        
        // For other countries, just ensure country code is present
        return $number;
    }

    // Legacy behavior for Brazil
    if (substr($number, 0, 2) != '55' && $isBrasil) {
        $number = '55' . $number;
    }

    // Verifica se o número tem o código do país (55) no início
    if (substr($number, 0, 2) == '55') {
        // Remove o código do país
        $number = substr($number, 2);

        // Separa o DDD e o restante do número
        $ddd = substr($number, 0, 2);
        $rest = substr($number, 2);

        // Verifica se o DDD é maior que 30
        if ($ddd > 30) {
            // Remove o dígito 9 se presente e se
            if (strlen($rest) == 9 && $rest[0] == '9') {
                $rest = substr($rest, 1);
            }
        }

        // Retorna o número formatado com o DDD e o número formatado
        return "55" . $ddd . $rest;
    } else {
        // Retorna o número original se o DDI não for 55
        return $number;
    }
}


function monta_html_leads_engine($dados)
{
    $ln = null;

    foreach ($dados as $index => $lead) {

        $ln .= "<tr class='engine_lead_{$lead->id}'>
                <td>{$lead->id}</td>
                <td>{$lead->name}</td>
                <td>{$lead->phonenumber}</td>
                <td>" . get_staff_full_name($lead->staffid) . "</td>
               
            </tr>";
    }
    return $ln;
}

function contactcenter_remove_acentos($string)
{
    return preg_replace(
        '/[^A-Za-z0-9\-]/',
        '',
        iconv('UTF-8', 'ASCII//TRANSLIT', $string)
    );
}

function get_tags_name($tags)
{
    $CI = &get_instance();

    // busco o nome da tag
    if ($tags) {

        $tagsArray = explode(",", $tags);

        $CI->db->where_in('id', $tagsArray);
        $result = $CI->db->get(db_prefix() . 'tags')->result();

        foreach ($result as $tag) {
            $tags_name[] = $tag->name;
        }

        $names = implode(",", $tags_name);
        return $names;
    }

    return "";
}




/**
 * conta quantos metas tem por sourceId
 * @return void
 */
function conta_meta_por_id($sourceId)
{
    $CI = &get_instance();
    $CI->db->where('sourceId', $sourceId);
    $CI->db->from(db_prefix() . 'contactcenter_meta');
    return $CI->db->count_all_results();
}

function get_status_leads_meta($id)
{
    if ($id) {
        $CI = &get_instance();
        $CI->db->where("id", $id);
        return $CI->db->get(db_prefix() . 'leads_status')->row();
    }
}

function count_source_leads_meta($sourceId, $entryPointConversionApp)
{

    $CI = &get_instance();
    $CI->db->select('COUNT(meta_id) AS total');

    if (!empty($sourceId)) {
        $CI->db->where('sourceId', $sourceId);
    } else {
        $CI->db->where('sourceId IS NULL');
    }

    $CI->db->where("entryPointConversionApp", $entryPointConversionApp);
    $result = $CI->db->get(db_prefix() . 'contactcenter_meta')->row();
    if ($result) {
        return $result->total;
    } else {
        return 0;
    }
}

function count_source_leads_app($sourceId, $entryPointConversionApp)
{

    $CI = &get_instance();
    $CI->db->select('COUNT(meta_id) AS total');
    if (!empty($sourceId)) {
        $CI->db->where('sourceId', $sourceId);
    } else {
        $CI->db->where('sourceId IS NULL');
    }
    $CI->db->where("source", $entryPointConversionApp);
    $result = $CI->db->get(db_prefix() . 'contactcenter_meta')->row();
    if ($result) {
        return $result->total;
    } else {
        return 0;
    }
}

/**
 * Retorna o nome do dia da semana
 * @param  [int] $diasemana [description]
 * @return [type]            [description]
 */
function dias_semanas($diasemana = null)
{

    $dias = [
        1 => _l("contac_chat_time_day_1"),
        2 => _l("contac_chat_time_day_2"),
        3 => _l("contac_chat_time_day_3"),
        4 => _l("contac_chat_time_day_4"),
        5 => _l("contac_chat_time_day_5"),
        6 => _l("contac_chat_time_day_6"),
        0 => _l("contac_chat_time_day_0")
    ];

    if ($diasemana) {
        return $dias[$diasemana];
    } else {
        return $dias;
    }
}


function get_members_departments_staffid($staffid)
{

    $CI = &get_instance();
    $CI->db->select('d.name');
    $CI->db->from(db_prefix() . 'departments as d');
    $CI->db->join(db_prefix() . 'staff_departments s', 'd.departmentid = s.departmentid', 'inner');
    $CI->db->where('s.staffid', $staffid);
    $dep = $CI->db->get()->result();
    if ($dep) {
        return $dep;
    } else {
        return false;
    }
}


function createThumbnailImage($base64, $width = 150, $height = 150)
{
    $imageData = base64_decode($base64);
    $source = imagecreatefromstring($imageData);

    // Redimensionar
    $thumb = imagescale($source, $width, $height);
    ob_start();
    imagejpeg($thumb, null, 75); // Qualidade de 75
    $thumbnailData = ob_get_clean();

    imagedestroy($source);
    imagedestroy($thumb);

    return base64_encode($thumbnailData);
}

function contactcenter_get_agenda_leads($lead_id)
{
    $CI = &get_instance();
    $CI->db->where('rel_id', $lead_id);
    $CI->db->where('rel_type', 'lead');
    $CI->db->order_by('start', 'DESC');
    return $CI->db->get(db_prefix() . 'events')->result();
}


function contactcenter_base64ToFile($base64_string, $upload_dir, $filename = null)
{
    $upload_path = FCPATH . "uploads/{$upload_dir}/";

    // Cria o diretório se não existir
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0755, true);
    }

    // Divide metadados do conteúdo Base64
    if (preg_match('/^data:(.*?);base64,(.*)$/', $base64_string, $matches)) {
        $mime_type = $matches[1];
        $base64_data = $matches[2];

        // Mapeamento de tipos MIME para extensões
        $mime_map = [
            // Imagens
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/bmp' => 'bmp',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/tiff' => 'tiff',
            'image/x-icon' => 'ico',
            'image/vnd.microsoft.icon' => 'ico',
            'image/heic' => 'heic',

            // Documentos
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/zip' => 'zip',
            'text/plain' => 'txt',
            'application/json' => 'json',

            // Vídeos
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'video/ogg' => 'ogv',
            'video/x-msvideo' => 'avi',
            'video/quicktime' => 'mov',
            'video/mpeg' => 'mpeg',
            'video/3gpp' => '3gp',
        ];


        $extension = isset($mime_map[$mime_type]) ? $mime_map[$mime_type] : null;

        if (!$extension) {
            return false; // Tipo de arquivo não suportado
        }

        // Gera nome do arquivo se não for informado
        if (!$filename) {
            $filename = uniqid('file_') . '.' . $extension;
        }

        $file_path = $upload_path . $filename;

        // Decodifica e salva
        file_put_contents($file_path, base64_decode($base64_data));

        // Verifica se o arquivo foi salvo com sucesso
        if (!file_exists($file_path)) {
            return false;
        }

        // Retorna caminho relativo (ajuste conforme necessário)
        return $upload_dir . '/' . $filename;
    }

    return false; // Formato inválido
}
