<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

add_option("leads_cadastro_contactcenter");
add_option("leads_cadastro_call_contactcenter");
add_option("time_contactcenter", "10:00,14:30");
add_option("leads_cadastro_engine_contactcenter");
add_option("texto_end_contactcenter", "Foi agendado com sucesso");
add_option("active_contador_contactcenter", 0);
add_option("leads_source_contador_contactcenter");
add_option("leads_status_contador_contactcenter");
add_option("staff_contador_contactcenter", 0);
add_option("active_audio_contactcenter", 0);
add_option("active_audio_contactcenter_elevenlabs", 0);
add_option("token_elevenlabs_contactcenter", "4a1b9c46c13b762f69493128f3d197e3");
add_option("active_vision", 0);
add_option("update_lead", 0);
add_option("quant_time_contactcenter", 4);
add_option("update_input_automation", 0);
add_option("agendaMinutesToAdd", 30);
add_option("contac_active_confirm_agendamento", 1);
add_option("contac_settings_sincronizacao_whatsapp_leads", 0);
add_option("contac_settings_sincronizacao_whatsapp_active", 0);
add_option("contac_title_agendamento", "Agendamento via WhatsApp");
add_option("whatsapp_msg_call", "Olá, no momento não podemos atender ligações");
add_option("historico_mgs_ai_active", 0);
add_option("contactcenter_group_chat_name_format", "AXIOM x {lead_name} ({date})");
add_option("contactcenter_group_chat_auto_add_staff", "");
add_option("contactcenter_gemini_api_key", "");
add_option("contactcenter_google_places_api_key", "");
add_option("contactcenter_ai_lead_field_mappings", "{}");


function is_partial_index($index_name, $table_name)
{
    $CI = &get_instance();
    $result = $CI->db->query("SHOW INDEX FROM $table_name WHERE Key_name = '$index_name'")->result_array();
    foreach ($result as $row) {
        if (isset($row['Sub_part']) && $row['Sub_part'] !== null) {
            return true;
        }
    }
    return false;
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_device')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_device` (
                    `dev_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `staffid` int(11) DEFAULT NULL,
                    `dev_type` int(11) DEFAULT NULL,
                    `dev_name` varchar(255) DEFAULT NULL,
                    `dev_number` varchar(255) DEFAULT NULL,
                    `dev_token` varchar(255) DEFAULT NULL,                                        
                    `dev_openai` int(11) DEFAULT NULL,
                    `dev_openai_date` datetime DEFAULT NULL,
                    `dev_voz_id` varchar(255) DEFAULT NULL,
                    `assistant_ai_id` int DEFAULT NULL,
                    `api_type` varchar(50) DEFAULT NULL,
                    `dev_instance_name` varchar(255) DEFAULT NULL,
                    `dev_engine` int(11) DEFAULT 0,
                    `status` varchar(255) DEFAULT NULL,
                    `server_url` varchar(50) NULL, 
                    `server_id` INT(11) DEFAULT NULL,
                    `chatbot_id` int(11) DEFAULT NULL, 
                    `timer_ia` varchar(500) DEFAULT NULL,  
                    `status_sinc` BOOLEAN DEFAULT 0,  
                    `currentPage` int(11) DEFAULT NULL, 
                    `totalPages` int(11) DEFAULT NULL,
                    `is_processing` int(11) DEFAULT 0, 
                    `contract_template` int(11) DEFAULT NULL, 
                    `contract_category` int(11) DEFAULT NULL, 
                    `contract_msg` TEXT DEFAULT NULL,
                    `sales_knowledge` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
                    `api_local` int(11) DEFAULT 0, 
                    `last_status` DATETIME DEFAULT NULL,       
                    `dev_date` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_message')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_message` (
                    `msg_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `staffid` int(11) DEFAULT NULL,
                    `msg_wook` varchar(255) DEFAULT NULL,
                    `msg_type` varchar(255) DEFAULT NULL,
                    `msg_fromMe` int(11) DEFAULT NULL,
                    `msg_send_id` varchar(255) DEFAULT NULL,
                    `msg_session` varchar(255) DEFAULT NULL,
                    `msg_isGroupMsg` int(11) DEFAULT NULL,
                    `msg_name` varchar(255) DEFAULT NULL,                                        
                    `msg_to` varchar(255) DEFAULT NULL,                                        
                    `msg_from` varchar(255) DEFAULT NULL,
                    `msg_title` VARCHAR(255) DEFAULT NULL,
                    `msg_content` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, 
                    `msg_url` TEXT DEFAULT NULL,
                    `msg_base64` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                                      
                    `msg_status` varchar(255) DEFAULT NULL,
                    `msg_isread` int(11) NULL DEFAULT 0,
                    `msg_date` datetime DEFAULT NULL,
                    `msg_date_update` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $CI->db->query("CREATE INDEX consultafrom ON tblcontactcenter_message (msg_from)");
    $CI->db->query("CREATE INDEX consultato ON tblcontactcenter_message (msg_to)");
    $CI->db->query("CREATE INDEX consultatoken ON tblcontactcenter_message (msg_session)");
    $CI->db->query("CREATE INDEX consulta_send_id ON tblcontactcenter_message (msg_send_id)");
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_message_ia')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_message_ia` (
                    `ia_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `ia_to` varchar(255) DEFAULT NULL,
                    `msg_id` int(11) DEFAULT NULL,
                    `staffid` int(11) DEFAULT NULL,
                    `leadid` int(11) DEFAULT NULL,
                    `ia_content` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, 
                    `ia_session` varchar(255) DEFAULT NULL,
                    `ia_date` datetime DEFAULT NULL                   
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_conversation_engine` (
                    `con_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `staffid` int(11) DEFAULT NULL,
                    `con_title` varchar(255) DEFAULT NULL,                    
                    `con_count_send` int(11) NULL DEFAULT 0,
                    `con_count_reply` int(11) NULL DEFAULT 0,
                    `con_status` int(11) NULL DEFAULT 0,
                    `con_date` datetime DEFAULT NULL,                    
                    `con_date_updade` datetime DEFAULT NULL                    
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_conversation_engine_list')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_conversation_engine_list` (
                    `list_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `con_id` int(11) DEFAULT NULL,
                    `list_text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                     
                    `list_image` varchar(255) DEFAULT NULL,                                      
                    `list_ordem` int(11) DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

if (!$CI->db->field_exists('media_type', db_prefix() . 'contactcenter_conversation_engine_list')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "contactcenter_conversation_engine_list ADD COLUMN media_type varchar(50) DEFAULT NULL ");
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_group')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_group` (
                    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `group_id` varchar(255) DEFAULT NULL,                                     
                    `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

if (!$CI->db->field_exists('vat', db_prefix() . 'leads')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "leads ADD COLUMN vat varchar(50) DEFAULT NULL ");
}

if (!$CI->db->field_exists('gpt_thread_model', db_prefix() . 'leads')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "leads ADD COLUMN gpt_thread_model varchar(50) DEFAULT NULL ");
}

if (!$CI->db->field_exists('isread_whats', db_prefix() . 'notifications')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "notifications` ADD COLUMN `isread_whats` TINYINT(1) DEFAULT 0");
}

if (!$CI->db->field_exists('ia_type', db_prefix() . 'contactcenter_message_ia')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message_ia` ADD COLUMN `ia_type` TINYINT(1) DEFAULT 0");
}

if (!$CI->db->field_exists('gpt_thread', db_prefix() . 'leads')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "leads` ADD COLUMN `gpt_thread` varchar(255) DEFAULT NULL ");
}




if (!$CI->db->field_exists('contactcenter_thumb', db_prefix() . 'leads')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "leads` ADD COLUMN `contactcenter_thumb` TEXT DEFAULT NULL ");
}

if (!$CI->db->field_exists('gpt_status', db_prefix() . 'leads')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "leads` ADD COLUMN `gpt_status` TINYINT(1) DEFAULT 0 ");
}

if (!$CI->db->field_exists('conversation_engine_id', db_prefix() . 'leads')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "leads` ADD COLUMN `conversation_engine_id` int(11) NULL");
}

if (!$CI->db->field_exists('conversation_engine_send', db_prefix() . 'leads')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "leads` ADD COLUMN `conversation_engine_send` datetime DEFAULT NULL");
}

if (!$CI->db->field_exists('conversation_engine_send_reply', db_prefix() . 'leads')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "leads` ADD COLUMN `conversation_engine_send_reply` datetime DEFAULT NULL");
}

if (!$CI->db->field_exists('msg_thumb', db_prefix() . 'contactcenter_message')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD COLUMN `msg_thumb` TEXT DEFAULT NULL");
}


if (!$CI->db->table_exists(db_prefix() . 'contactcenter_assistants_ai')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_assistants_ai` (
                    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `ai_name` varchar(255) DEFAULT NULL,                                     
                    `ai_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}




if (!$CI->db->field_exists('leads_status', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `leads_status` int(11) DEFAULT 0");
}


if (!$CI->db->field_exists('leads_create_data', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `leads_create_data` datetime DEFAULT NULL");
}

if (!$CI->db->field_exists('rel_id', db_prefix() . 'events')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "events` ADD COLUMN `rel_id` int(11) NULL");
}

if (!$CI->db->field_exists('rel_type', db_prefix() . 'events')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "events` ADD COLUMN `rel_type` varchar(10) NULL");
}

if (!$CI->db->field_exists('status', db_prefix() . 'events')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "events` ADD COLUMN `status` int(11) DEFAULT NULL");
}


if (!$CI->db->field_exists('leads_status_final', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `leads_status_final` int(11) DEFAULT 0");
}


if (!$CI->db->field_exists('leads_create_data_final', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `leads_create_data_final` datetime DEFAULT NULL");
}

// Add date filter type field
if (!$CI->db->field_exists('date_filter_type', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `date_filter_type` ENUM('creation_date', 'last_contact') DEFAULT 'creation_date'");
}

// Add last contact date fields
if (!$CI->db->field_exists('leads_last_contact_data', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `leads_last_contact_data` datetime DEFAULT NULL");
}

if (!$CI->db->field_exists('leads_last_contact_data_final', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `leads_last_contact_data_final` datetime DEFAULT NULL");
}

// Add filter fields for source, city, state
if (!$CI->db->field_exists('filter_source', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `filter_source` TEXT DEFAULT NULL");
}

if (!$CI->db->field_exists('filter_city', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `filter_city` TEXT DEFAULT NULL");
}

if (!$CI->db->field_exists('filter_state', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `filter_state` TEXT DEFAULT NULL");
}

// Add spare devices field for multi-device support
if (!$CI->db->field_exists('spare_devices', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `spare_devices` TEXT DEFAULT NULL");
}

// Add device rotation index to track which device should send next
if (!$CI->db->field_exists('device_rotation_index', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `device_rotation_index` INT(11) DEFAULT 0");
}

// Create message triggers table
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_message_triggers')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_message_triggers` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `trigger_type` ENUM('first_message', 'safe_word') NOT NULL DEFAULT 'first_message',
        `trigger_words` TEXT NOT NULL COMMENT 'Comma-separated list of words',
        `case_sensitive` TINYINT(1) DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1,
        `action_add_tag` TEXT NULL COMMENT 'Comma-separated tag IDs',
        `action_change_status` INT(11) NULL,
        `action_change_source` INT(11) NULL,
        `action_update_field` VARCHAR(255) NULL COMMENT 'Field name to update',
        `action_update_field_value` TEXT NULL COMMENT 'Value to set',
        `action_update_custom_field` INT(11) NULL COMMENT 'Custom field ID',
        `action_update_custom_field_value` TEXT NULL COMMENT 'Custom field value',
        `action_update_description` TEXT NULL COMMENT 'Description text to append',
        `action_disable_ai` TINYINT(1) DEFAULT 0,
        `action_change_owner` INT(11) NULL COMMENT 'Staff ID',
        `action_send_notification` TINYINT(1) DEFAULT 0,
        `action_notification_staff` INT(11) NULL COMMENT 'Staff ID to notify',
        `action_notification_message` TEXT NULL COMMENT 'Custom notification message',
        `datecreated` DATETIME NOT NULL,
        `dateupdated` DATETIME NULL,
        `created_by` INT(11) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `trigger_type` (`trigger_type`),
        KEY `is_active` (`is_active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
}


if (!$CI->db->field_exists('leads_sleep', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `leads_sleep` int(11) DEFAULT 0");
}

if (!$CI->db->field_exists('leads_count', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `leads_count` int(11) DEFAULT 0");
}



if (!$CI->db->table_exists(db_prefix() . 'contactcenter_group_api')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_group_api` (
        `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `group_api_id` varchar(255) DEFAULT NULL,                                     
        `group_api_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `group_api_description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `device_id` int(11) DEFAULT NULL,
        `numbers` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_atendimento_trans')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_atendimento_trans` (
        `trans_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `staffid_from` int(11) DEFAULT NULL,
        `staffid_to` int(11) DEFAULT NULL,
        `lead_id` int(11) DEFAULT NULL,
        `dev_token` varchar(255) DEFAULT NULL,
        `phonenumber` varchar(255) DEFAULT NULL,
        `trans_status` int(11) DEFAULT NULL,
        `trans_accepted` int(11) DEFAULT NULL DEFAULT 0,
        `trans_desc` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci    
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

if (!$CI->db->field_exists('trans_date', db_prefix() . 'contactcenter_atendimento_trans')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_atendimento_trans` ADD COLUMN `trans_date` datetime DEFAULT NULL");
}



// Se o índice não existir, crie-o
$query = $CI->db->query("SHOW INDEX FROM tblcontactcenter_message WHERE Key_name = 'consultafrom'");
if ($query->num_rows() == 0) {
    $CI->db->query("CREATE INDEX consultafrom ON tblcontactcenter_message (msg_from(11))");
}
$query = $CI->db->query("SHOW INDEX FROM tblcontactcenter_message WHERE Key_name = 'consultato'");
if ($query->num_rows() == 0) {
    $CI->db->query("CREATE INDEX consultato ON tblcontactcenter_message (msg_to(11))");
}
$query = $CI->db->query("SHOW INDEX FROM tblcontactcenter_message WHERE Key_name = 'consultatoken'");
if ($query->num_rows() == 0) {
    $CI->db->query("CREATE INDEX consultatoken ON tblcontactcenter_message (msg_session(11))");
}
$query = $CI->db->query("SHOW INDEX FROM tblcontactcenter_message WHERE Key_name = 'consulta_send_id'");
if ($query->num_rows() == 0) {
    $CI->db->query("CREATE INDEX consulta_send_id ON tblcontactcenter_message (msg_send_id(60))");
}


$query = $CI->db->get_where(db_prefix() . 'customfields', array('slug' => 'leads_unidade', 'fieldto' => 'leads'));
if ($query->num_rows() == 0) {
    $data = array(
        'fieldto' => 'leads',
        'name' => 'Campanhas',
        'slug' => 'leads_unidade',
        'required' => 0,
        'type' => 'input',
        'options' => '',
        'display_inline' => 0,
        'field_order' => 3,
        'active' => 1,
        'show_on_pdf' => 0,
        'show_on_ticket_form' => 0,
        'only_admin' => 0,
        'show_on_table' => 1,
        'show_on_client_portal' => 0,
        'disalow_client_to_edit' => 0,
        'bs_column' => 12,
    );
    $CI->db->insert(db_prefix() . 'customfields', $data);
}


if (!$CI->db->table_exists(db_prefix() . 'contactcenter_saas_planos')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_saas_planos` (
                    `plan_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `plan_id_saas` int(11) DEFAULT NULL,
                    `staffid` int(11) DEFAULT NULL,                                                         
                    `plan_desc` TEXT DEFAULT NULL,
                    `plan_key` varchar(255) DEFAULT NULL,
                    `plan_url` varchar(255) DEFAULT NULL,                                        
                    `plan_status` int(11) NULL DEFAULT 0,
                    `plan_key_status` varchar(255) DEFAULT 'pendente',
                    `qtd_device` int(11) DEFAULT 0,
                    `qtd_assistant` int(11) DEFAULT 0,
                    `motor_conversa` int(11) DEFAULT 0,
                    `plan_date_start` datetime DEFAULT NULL,
                    `plan_date_end` datetime DEFAULT NULL,
                    `plan_date_create` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

if (!$CI->db->field_exists('vat', db_prefix() . 'leads')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "leads` ADD COLUMN `vat` varchar(50) DEFAULT NULL ");
}

if (!$CI->db->table_exists(db_prefix() . 'leads_cont')) {
    $CI->db->query("CREATE TABLE `tblleads_cont` (
                    `cont_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `user_id` int(11) DEFAULT NULL,
                    `cont_vezes` varchar(255) DEFAULT NULL,
                    `cont_data` datetime DEFAULT NULL,
                    `grup_id` int(11) DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

if (!$CI->db->table_exists(db_prefix() . 'leads_grupo')) {
    $CI->db->query("CREATE TABLE `tblleads_grupo` (
                    `grup_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `grup_name` varchar(255) DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

if (!$CI->db->table_exists(db_prefix() . 'leads_grupo_item')) {
    $CI->db->query("CREATE TABLE `tblleads_grupo_item` (
                    `gitem_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `grup_id` int(11) DEFAULT NULL,
                    `gitem_name` varchar(255) DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}


if (!$CI->db->field_exists('next_execution_date', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `next_execution_date` datetime DEFAULT NULL");
}

if (!$CI->db->field_exists('start_time', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `start_time` TIME NOT NULL DEFAULT '08:00:00'");
}

if (!$CI->db->field_exists('end_time', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `end_time` TIME NOT NULL DEFAULT '18:00:00'");
}

if (!$CI->db->field_exists('leads_day', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `leads_day` int(11) DEFAULT 0");
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_conversation_engine_error')) {

    $CI->db->query("CREATE TABLE `tblcontactcenter_conversation_engine_error` (
                    `error_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `con_id` int(11) DEFAULT NULL,
                    `lead_id` int(11) DEFAULT NULL,
                    `lead_name` varchar(300) DEFAULT NULL,
                    `lead_phonumber` varchar(300) DEFAULT NULL,
                    `error_date` datetime DEFAULT NULL,
                    `error_message` TEXT DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    //$CI->db->query("CREATE INDEX consulta_con_id ON tblcontactcenter_conversation_engine_error (con_id(11))");
    //$CI->db->query("CREATE INDEX consulta_lead_id ON tblcontactcenter_conversation_engine_error (lead_id(11))");

    $CI->db->query("CREATE INDEX consulta_con_id ON tblcontactcenter_conversation_engine_error (con_id)");
    $CI->db->query("CREATE INDEX consulta_lead_id ON tblcontactcenter_conversation_engine_error (lead_id)");
}

if (!$CI->db->field_exists('status_whats', db_prefix() . 'leads')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "leads` ADD COLUMN `status_whats` int(11) DEFAULT 0");
}

if (!$CI->db->field_exists('device_id', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `device_id` int(11) DEFAULT NULL");
}

// Add campaign_tag_id field to conversation_engine table for tagging leads from campaigns
if (!$CI->db->field_exists('campaign_tag_id', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `campaign_tag_id` int(11) DEFAULT NULL COMMENT 'Tag to add to leads when campaign message is sent'");
}

if (!$CI->db->field_exists('invalid_number', db_prefix() . 'contactcenter_conversation_engine_error')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine_error` ADD COLUMN `invalid_number` int(11) DEFAULT 0");
}


if (!$CI->db->table_exists(db_prefix() . 'contactcenter_chat_web')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_chat_web` (
                    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `leadid` int(11) DEFAULT NULL,
                    `text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                     
                    `chat_id` int(11) DEFAULT NULL,                                      
                    `fromMe` int(11) DEFAULT NULL,
                    `date` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_chat_header')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_chat_header` (
                    `chat_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,                   
                    `chat_hash` varchar(255) DEFAULT NULL, 
                    `chat_name` varchar(255) DEFAULT NULL,
                    `chat_assitent` varchar(255) DEFAULT NULL,
                    `chat_source` int(11) DEFAULT NULL,
                    `chat_assigned` int(11) DEFAULT NULL,
                    `chat_status` int(11) DEFAULT NULL,
                    `chat_count` int(11) DEFAULT NULL,
                    `date` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Verificar e recriar índices se forem parciais
/*if (is_partial_index('consultafrom', 'tblcontactcenter_message')) {
    $CI->db->query("DROP INDEX consultafrom ON tblcontactcenter_message");
    $CI->db->query("CREATE INDEX consultafrom ON tblcontactcenter_message (msg_from)");
}

if (is_partial_index('consultato', 'tblcontactcenter_message')) {
    $CI->db->query("DROP INDEX consultato ON tblcontactcenter_message");
    $CI->db->query("CREATE INDEX consultato ON tblcontactcenter_message (msg_to)");
}

if (is_partial_index('consultatoken', 'tblcontactcenter_message')) {
    $CI->db->query("DROP INDEX consultatoken ON tblcontactcenter_message");
    $CI->db->query("CREATE INDEX consultatoken ON tblcontactcenter_message (msg_session)");
}

if (is_partial_index('consulta_send_id', 'tblcontactcenter_message')) {
    $CI->db->query("DROP INDEX consulta_send_id ON tblcontactcenter_message");
    $CI->db->query("CREATE INDEX consulta_send_id ON tblcontactcenter_message (msg_send_id)");
}

if (is_partial_index('consulta_con_id', 'tblcontactcenter_conversation_engine_error')) {
    $CI->db->query("DROP INDEX consulta_con_id ON tblcontactcenter_conversation_engine_error");
    $CI->db->query("CREATE INDEX consulta_con_id ON tblcontactcenter_conversation_engine_error (con_id)");
}

if (is_partial_index('consulta_lead_id', 'tblcontactcenter_conversation_engine_error')) {
    $CI->db->query("DROP INDEX consulta_lead_id ON tblcontactcenter_conversation_engine_error");
    $CI->db->query("CREATE INDEX consulta_lead_id ON tblcontactcenter_conversation_engine_error (lead_id)");
}*/


if (!$CI->db->field_exists('date_whats', db_prefix() . 'leads')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "leads` ADD COLUMN `date_whats` DATETIME DEFAULT NULL");
}

if (!$CI->db->field_exists('msg_conversation_number', db_prefix() . 'contactcenter_message')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD COLUMN `msg_conversation_number` varchar(255) DEFAULT NULL");

    $CI->db->query("CREATE INDEX consulta_msg_conversation_number ON tblcontactcenter_message (msg_conversation_number)");

    // percorrer todas as mensagens e preencher o campo from_to_number
    $CI->db->query("
        UPDATE " . db_prefix() . "contactcenter_message
        SET msg_conversation_number = CASE
            WHEN msg_fromMe = 1 THEN msg_to
            ELSE msg_from
        END
    ");
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_thread')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_thread` (
                    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `lead_id` int(11) DEFAULT NULL,
                    `thread_id` varchar(255) DEFAULT NULL,                                     
                    `ai_key` varchar(255) DEFAULT NULL,
                    `ai_model` varchar(255) DEFAULT NULL,
                    `date` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}




if (!$CI->db->table_exists(db_prefix() . 'contactcenter_contact')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_contact` (
                    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `staffid` int(11) DEFAULT 0,
                    `phonenumber` varchar(255) DEFAULT NULL,
                    `leadid` int(11) DEFAULT 0,                                     
                    `session` varchar(255) DEFAULT NULL,                  
                    `transferid` int(11) DEFAULT NULL,
                    `thumb` LONGTEXT DEFAULT NULL,
                    `isread` int(11) NULL DEFAULT 0,
                    `date` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $CI->db->query("CREATE INDEX contactphonenumber ON tblcontactcenter_contact (phonenumber)");
    $CI->db->query("CREATE INDEX contactstaffid ON tblcontactcenter_contact (sttafid)");
    $CI->db->query("INSERT INTO tblcontactcenter_contact (phonenumber, session, date)
                    SELECT                        
                        msg_conversation_number,
                        MAX(msg_session) as last_msg_session,
                        MAX(msg_date) as last_msg_date
                    FROM 
                        tblcontactcenter_message
                    WHERE  msg_isGroupMsg = 0    
                    GROUP BY 
                        msg_conversation_number;");

    if ($CI->db->affected_rows() > 0) {
        $CI->db->query("UPDATE tblcontactcenter_contact c 
                        JOIN tblcontactcenter_device d
                        ON c.session = d.dev_token
                        SET c.staffid = d.staffid;    
        ");
        if ($CI->db->affected_rows() > 0) {
            $CI->db->query("UPDATE tblcontactcenter_contact c
                            JOIN tblcontactcenter_atendimento_trans t
                            ON c.phonenumber = t.phonenumber
                            SET 
                                c.transferid = t.trans_id,
                                c.staffid = t.staffid_to,
                                c.leadid = t.lead_id
                            WHERE 
                                c.phonenumber = t.phonenumber AND c.session = t.dev_token;");
        }
    }
}

if (!$CI->db->field_exists('tags', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `tags` varchar(50) DEFAULT NULL");
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_meta')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_meta` (
                    `meta_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,  
                    `lead_id` int(11) DEFAULT NULL,  
                    `session` varchar(255) DEFAULT NULL,               
                    `conversionSource` varchar(255) DEFAULT NULL, 
                    `title` varchar(255) DEFAULT NULL,
                    `body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                    `mediaType` varchar(255) DEFAULT NULL,
                    `thumbnailUrl` TEXT DEFAULT NULL,
                    `sourceId` varchar(255) DEFAULT NULL,
                    `sourceUrl` varchar(255) DEFAULT NULL,                   
                    `date` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_links_custom')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_links_custom` (
                    `link_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `phonenumber` varchar(255) DEFAULT NULL,               
                    `msg` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                                  
                    `link` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                                  
                    `source` varchar(255) DEFAULT NULL,
                    `hash` varchar(255) DEFAULT NULL,
                    `count` int(11) DEFAULT 0, 
                    `date` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

if (!$CI->db->field_exists('source', db_prefix() . 'contactcenter_meta')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "contactcenter_meta ADD COLUMN source varchar(50) DEFAULT NULL ");
}

if (!$CI->db->field_exists('type', db_prefix() . 'contactcenter_meta')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "contactcenter_meta ADD COLUMN type int(11) DEFAULT 0 ");
}

if (!$CI->db->field_exists('entryPointConversionApp', db_prefix() . 'contactcenter_meta')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "contactcenter_meta ADD COLUMN entryPointConversionApp varchar(50) DEFAULT NULL ");
}


// version 1.4.1
if (!$CI->db->field_exists('msg_fromMe', db_prefix() . 'contactcenter_contact')) {

    $CI->db->query("ALTER TABLE `tblcontactcenter_contact` ADD `msg_fromMe` int(11) NOT NULL DEFAULT 0");
    $CI->db->query("ALTER TABLE `tblcontactcenter_contact`  ADD `is_locked` TINYINT(1) DEFAULT 0");

    $CI->db->query("
            UPDATE tblcontactcenter_contact c
            INNER JOIN (
                SELECT m.msg_conversation_number, m.msg_fromMe
                FROM tblcontactcenter_message m
                INNER JOIN (
                    SELECT msg_conversation_number, MAX(msg_id) as max_id
                    FROM tblcontactcenter_message
                    GROUP BY msg_conversation_number
                ) lm ON m.msg_id = lm.max_id
            ) last_msg ON c.phonenumber = last_msg.msg_conversation_number
            SET c.msg_FromMe = last_msg.msg_fromMe;
        ");

    $CI->db->query("UPDATE tblcontactcenter_contact c
        INNER JOIN tblleads l ON c.phonenumber = l.phonenumber
        SET c.leadid = l.id");

    if (!$CI->db->table_exists(db_prefix() . 'contactcenter_leads_engine')) {
        $CI->db->query("CREATE TABLE `tblcontactcenter_leads_engine` (
                            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            `staffid` int(11) DEFAULT NULL,
                            `title` varchar(255) DEFAULT NULL,                                               
                            `status` int(11) NULL DEFAULT 0,
                            `leads_status` int(11) DEFAULT 0, 
                            `leads_status_final` int(11) DEFAULT 0,             
                            `date` datetime DEFAULT NULL,
                            `start_time` TIME NOT NULL DEFAULT '08:00:00',
                            `end_time` TIME NOT NULL DEFAULT '18:00:00', 
                            `hours_since_last_contact` int(11) DEFAULT 24             
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    if (!$CI->db->table_exists(db_prefix() . 'contactcenter_leads_engine_messages')) {
        $CI->db->query("CREATE TABLE `tblcontactcenter_leads_engine_messages` (
                            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            `contactcenter_leads_engine_id` int(11) DEFAULT NULL,
                            `text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                     
                            `image` varchar(255) DEFAULT NULL,
                            `media_type` varchar(50) DEFAULT NULL,                                      
                            `ordenation` int(11) DEFAULT NULL
                          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
}

if (!$CI->db->field_exists('is_locked', db_prefix() . 'contactcenter_contact')) {
    $CI->db->query("ALTER TABLE `tblcontactcenter_contact`  ADD `is_locked` TINYINT(1) DEFAULT 0");
}

// version 1.4.2
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_drawflow')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_drawflow` (
                    `draw_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,                   
                    `title` varchar(255) DEFAULT NULL,
                    `data` longtext NOT NULL,
                    `status` int(11) NULL DEFAULT 0,
                    `date` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");



    if (!$CI->db->table_exists(db_prefix() . 'contactcenter_drawflow_group')) {
        $CI->db->query("CREATE TABLE `tblcontactcenter_drawflow_group` (
                    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,    
                    `group_id` int(11) DEFAULT NULL,
                    `draw_id` int(11) DEFAULT NULL,
                    `title` varchar(255) DEFAULT NULL,                   
                    `type` varchar(255) DEFAULT NULL,                 
                    `group_inputs` int(11) DEFAULT NULL,
                    `group_output` int(11) DEFAULT NULL,
                    `gpt_caracters` int(11) DEFAULT NULL,
                    `gpt_tag_exit` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                    
                    `gpt_prompt` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                    
                    `gpt_model` varchar(255) DEFAULT NULL,                 
                    `custom_fields` varchar(50) DEFAULT NULL,                 
                    `count` int(11) DEFAULT 0,               
                    `date` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    if (!$CI->db->table_exists(db_prefix() . 'contactcenter_drawflow_group_children')) {
        $CI->db->query("CREATE TABLE `tblcontactcenter_drawflow_group_children` (
                    `child_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,                   
                    `group_id` int(11) DEFAULT NULL,
                    `draw_id` int(11) DEFAULT NULL,
                    `input_id` varchar(255) DEFAULT NULL,
                    `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, 
                    `url` text DEFAULT NULL,
                    `link` varchar(255) DEFAULT NULL,
                    `type` varchar(255) DEFAULT NULL,
                    `operador` varchar(10) DEFAULT NULL,
                    `conexao` varchar(15) DEFAULT NULL,
                    `next` int(11) DEFAULT 0,
                    `date` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    if (!$CI->db->table_exists(db_prefix() . 'contactcenter_drawflow_static_lead')) {
        $CI->db->query("CREATE TABLE `tblcontactcenter_drawflow_static_lead` (
                    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,                   
                    `group_id` int(11) DEFAULT NULL,
                    `draw_id` int(11) DEFAULT NULL,
                    `lead_id` int(11) DEFAULT NULL,
                    `date` date DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    if (!$CI->db->field_exists('chatbot_group_id', db_prefix() . 'contactcenter_contact')) {
        $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `chatbot_group_id` INT(11) DEFAULT NULL");
    }

    if (!$CI->db->field_exists('chatbot_agenda', db_prefix() . 'contactcenter_contact')) {
        $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `chatbot_agenda` text DEFAULT NULL");
    }
}

//version 1.4.3
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_server')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_server` (
                    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,                   
                    `url` varchar(255) DEFAULT NULL,
                    `version` varchar(255) DEFAULT NULL,
                    `api_type` varchar(255) DEFAULT NULL,
                    `name` varchar(255) DEFAULT NULL                          
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // insiro a tabela de servidores
    $CI->db->query("INSERT INTO `tblcontactcenter_server` (`url`, `version`, `name`, `api_type`) VALUES
            ('http://localhost:8080/', '2', 'AXIOM Evolution - Local', 'axiom_evolution');");
}

// version 1.4.4
if (!$CI->db->field_exists('tags', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD COLUMN `tags` varchar(50) DEFAULT NULL");
}

// Add tags column to leads_engine table (same as conversation_engine)
if (!$CI->db->field_exists('tags', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD COLUMN `tags` varchar(50) DEFAULT NULL");
}

// version 1.4.5
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_meta')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_meta` (
                    `meta_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,  
                    `lead_id` int(11) DEFAULT NULL,  
                    `session` varchar(255) DEFAULT NULL,               
                    `conversionSource` varchar(255) DEFAULT NULL, 
                    `title` varchar(255) DEFAULT NULL,
                    `body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                    `mediaType` varchar(255) DEFAULT NULL,
                    `thumbnailUrl` TEXT DEFAULT NULL,
                    `sourceId` varchar(255) DEFAULT NULL,
                    `sourceUrl` varchar(255) DEFAULT NULL,                   
                    `date` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

//version 1.4.6
if (!$CI->db->field_exists('source', db_prefix() . 'contactcenter_meta')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "contactcenter_meta ADD COLUMN source varchar(50) DEFAULT NULL ");


    if (!$CI->db->field_exists('entryPointConversionApp', db_prefix() . 'contactcenter_meta')) {
        $CI->db->query("ALTER TABLE " . db_prefix() . "contactcenter_meta ADD COLUMN entryPointConversionApp varchar(50) DEFAULT NULL ");
    }

    if (!$CI->db->field_exists('type', db_prefix() . 'contactcenter_meta')) {
        $CI->db->query("ALTER TABLE " . db_prefix() . "contactcenter_meta ADD COLUMN type int(11) DEFAULT 0 ");
    }

    if (!$CI->db->table_exists(db_prefix() . 'contactcenter_links_custom')) {
        $CI->db->query("CREATE TABLE `tblcontactcenter_links_custom` (
                    `link_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `phonenumber` varchar(255) DEFAULT NULL,               
                    `msg` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                                  
                    `link` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                                  
                    `source` varchar(255) DEFAULT NULL,
                    `hash` varchar(255) DEFAULT NULL,
                    `count` int(11) DEFAULT 0, 
                    `date` datetime DEFAULT NULL
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
}

//version 1.4.7
if (!$CI->db->field_exists('value_key', db_prefix() . 'contactcenter_server')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "contactcenter_server ADD COLUMN value_key varchar(500) DEFAULT NULL");

    // insert value in key
    $CI->db->query("UPDATE `" . db_prefix() . "contactcenter_server` SET value_key = '12d7cf0603036564a574eedcf904d84b'");
}

/**
 * Copiar arquivos de um diretório para outro
 *
 * @param string $filename_source
 * @param string $filename_dest
 * @return void
 */
function copiar_arquivos(
    string $filename_source,
    string $filename_dest
) {
    if (file_exists($filename_dest)) {
        unlink($filename_dest);
    }
    copy($filename_source, $filename_dest);
}

copiar_arquivos('modules/contactcenter/views/new/my_lead.php', 'application/views/admin/leads/my_lead.php');
copiar_arquivos('modules/contactcenter/views/new/Leads_model.php', 'application/models/Leads_model.php');


if (get_option("update_lead") == 0) {

    $CI = &get_instance();
    $CI->load->model('contactcenter/contactcenter_model');
    $CI->contactcenter_model->update_lead_past();
    update_option("update_lead", 1);
}

//version 1.4.8
if (!$CI->db->field_exists('notify_by_whatsapp', db_prefix() . 'reminders')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "reminders ADD COLUMN notify_by_whatsapp int(11) DEFAULT 0 ");
}
if (!$CI->db->field_exists('isnotify_whatsapp', db_prefix() . 'reminders')) {
    $CI->db->query("ALTER TABLE " . db_prefix() . "reminders ADD COLUMN isnotify_whatsapp int(11) DEFAULT 0 ");
}

//version 1.4.8
// se ja tem o registro com essa url, nao insere



//version 1.5.0
$module = $CI->app_modules->get('contactcenter');
if ($module['installed_version'] == '1.5.0') {
    $CI->load->model('contactcenter/contactcenter_model');

    // Atualizo os devices nos servidor pra web socket
    $CI->contactcenter_model->update_web_socket();
    $CI->contactcenter_model->delete_server();
}



if (!$CI->db->field_exists('notifyWhatsapp', db_prefix() . 'events')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "events` ADD COLUMN `notifyWhatsapp` int(11) DEFAULT 0");
}


//version 1.5.7
if (!$CI->db->field_exists('name', db_prefix() . 'contactcenter_contact')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `name` varchar(255) DEFAULT NULL");
}

if (!$CI->db->field_exists('thumb', db_prefix() . 'contactcenter_contact')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `thumb` varchar(500) DEFAULT NULL");
}



if (!$CI->db->field_exists('isGroup', db_prefix() . 'contactcenter_contact')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `isGroup` BOOLEAN DEFAULT 0");
}



if (!$CI->db->field_exists('active', db_prefix() . 'contactcenter_contact')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `active` BOOLEAN DEFAULT 1");
}

// Add chat_marked_read field for manual read/unread marking
if (!$CI->db->field_exists('chat_marked_read', db_prefix() . 'contactcenter_contact')) {
    try {
        $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `chat_marked_read` TINYINT(1) DEFAULT 0 AFTER `isread`");
        log_message('debug', 'Added chat_marked_read column to contactcenter_contact table');
    } catch (Exception $e) {
        log_message('error', 'Failed to add chat_marked_read column: ' . $e->getMessage());
        // Try without AFTER clause if it fails
        try {
            $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD COLUMN `chat_marked_read` TINYINT(1) DEFAULT 0");
            log_message('debug', 'Added chat_marked_read column without AFTER clause');
        } catch (Exception $e2) {
            log_message('error', 'Failed to add chat_marked_read column (second attempt): ' . $e2->getMessage());
        }
    }
}


$query = $CI->db->query("SHOW INDEX FROM tblcontactcenter_message WHERE Key_name = 'consultDate'");
if ($query->num_rows() == 0) {
    $CI->db->query("CREATE INDEX consultDate ON tblcontactcenter_message (msg_date)");
}

$query = $CI->db->query("SHOW INDEX FROM tblcontactcenter_contact WHERE Key_name = 'consultDate'");
if ($query->num_rows() == 0) {
    $CI->db->query("CREATE INDEX consultDate ON tblcontactcenter_contact (date)");
}

/**
 * Media Library Table
 */
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_media_library')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_media_library` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `staffid` int(11) NOT NULL COMMENT 'Device owner/staff ID',
        `device_id` int(11) DEFAULT NULL COMMENT 'Device ID (can be null for global)',
        `filename` varchar(255) NOT NULL,
        `file_path` varchar(500) NOT NULL,
        `file_type` varchar(50) NOT NULL COMMENT 'audio, image, video, document',
        `file_size` bigint(20) DEFAULT NULL,
        `is_global` tinyint(1) DEFAULT 0 COMMENT '0=private, 1=global',
        `title` varchar(255) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
        `date_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `staffid` (`staffid`),
        KEY `device_id` (`device_id`),
        KEY `is_global` (`is_global`),
        KEY `file_type` (`file_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

/**
 * 1.5.8
 */
if (!$CI->db->field_exists('device_id', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD  `device_id` int(11) DEFAULT NULL");
}

// Add campaign_tag_id field to leads_engine table for tagging leads from follow-ups
if (!$CI->db->field_exists('campaign_tag_id', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `campaign_tag_id` int(11) DEFAULT NULL COMMENT 'Tag to add to leads when follow-up message is sent'");
}



/**
 * 1.5.9
 */
if (!$CI->db->field_exists('reply_participant', db_prefix() . 'contactcenter_message')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD  `reply_participant` varchar(255) DEFAULT NULL");
}

if (!$CI->db->field_exists('reply_msg', db_prefix() . 'contactcenter_message')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD  `reply_msg` varchar(255) DEFAULT NULL");
}

if (!$CI->db->field_exists('reply_id', db_prefix() . 'contactcenter_message')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD  `reply_id` varchar(255) DEFAULT NULL");
}



add_option('minutes_schedule', 60);
add_option('saturdayHours');
add_option('contactcenter_notify_whatsapp_agendamento', 1);

add_option('contactcenter_api_bearer_token', 'GdckVkHjOI48ScJGlETxIg7VozEAIuEwoIGCPfkaxEh31oj2y2XwfUPZER2GTTSAUFoTDz0CUC4CDhSdxVV3Lkh1JZLuCyP9IuHPlih');

copiar_arquivos('modules/contactcenter/views/new/my_lead.php', 'application/views/admin/leads/my_lead.php');
copiar_arquivos('modules/contactcenter/files/helpers/modules_helper.php', 'application/helpers/modules_helper.php');
copiar_arquivos('modules/contactcenter/views/new/reminder_fields.php', 'application/views/admin/includes/reminder_fields.php');
copiar_arquivos('modules/contactcenter/files/models/Misc_model.php', 'application/models/Misc_model.php');
//Helpers
copiar_arquivos('modules/contactcenter/views/new/modules_helper.php', 'application/helpers/modules_helper.php');


/**
 * 1.6.0
 */
add_option('openai_speed_send', 0);
copiar_arquivos('modules/contactcenter/files/controllers/Contract.php', 'application/controllers/Contract.php');


if (!$CI->db->field_exists('assigned', db_prefix() . 'contactcenter_contact')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD  `assigned` int(11) DEFAULT NULL");
}

if (!$CI->db->field_exists('client_id', db_prefix() . 'contactcenter_contact')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD  `client_id` int(11) DEFAULT NULL");
}

if (!$CI->db->field_exists('lead_status', db_prefix() . 'contactcenter_contact')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD  `lead_status` int(11) DEFAULT NULL");
}

if (!$CI->db->field_exists('rel_type', db_prefix() . 'contactcenter_contact')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD  `rel_type` varchar(10) DEFAULT NULL");
}

if (!$CI->db->field_exists('remoteJid', db_prefix() . 'contactcenter_message')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD  `remoteJid` varchar(100) DEFAULT NULL");
}


if (!$CI->db->table_exists(db_prefix() . 'contactcenter_message_speed')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_message_speed` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `title` varchar(255) DEFAULT NULL,               
                `content` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `device_id` int(11) DEFAULT NULL, 
                `staffid` int(11) DEFAULT NULL, 
                `restrict` int(11) DEFAULT NULL, 
                `date` DATETIME DEFAULT CURRENT_TIMESTAMP
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}


/**
 * 1.6.1
 */

if (!$CI->db->field_exists('instructions', db_prefix() . 'contactcenter_assistants_ai')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `instructions` LONGTEXT DEFAULT NULL");
}

if (!$CI->db->field_exists('model', db_prefix() . 'contactcenter_assistants_ai')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `model` varchar(100) DEFAULT NULL");
}

if (!$CI->db->field_exists('functions', db_prefix() . 'contactcenter_assistants_ai')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `functions` TEXT DEFAULT NULL");
}

if (!$CI->db->field_exists('vector_id', db_prefix() . 'contactcenter_assistants_ai')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `vector_id` varchar(255) DEFAULT NULL");
}

if (!$CI->db->field_exists('last_update', db_prefix() . 'contactcenter_assistants_ai')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `last_update` DATETIME DEFAULT NULL");
}

if (!$CI->db->field_exists('staffid', db_prefix() . 'contactcenter_assistants_ai')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `staffid` int(11) DEFAULT NULL");
}

if (!$CI->db->field_exists('staffid_update', db_prefix() . 'contactcenter_assistants_ai')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `staffid_update` int(11) DEFAULT NULL");
}

if (!$CI->db->field_exists('create_date', db_prefix() . 'contactcenter_assistants_ai')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `create_date` DATETIME DEFAULT NULL");
}

// Add visual_data field for visual builder
if (!$CI->db->field_exists('visual_data', db_prefix() . 'contactcenter_assistants_ai')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `visual_data` LONGTEXT DEFAULT NULL");
}

if (!$CI->db->field_exists('public_form_token', db_prefix() . 'contactcenter_assistants_ai')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_ai` ADD `public_form_token` VARCHAR(64) NULL DEFAULT NULL, ADD UNIQUE KEY `public_form_token` (`public_form_token`)");
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_assistant_onboarding')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_assistant_onboarding` (
        `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `assistant_id` int(11) NOT NULL,
        `form_data` LONGTEXT DEFAULT NULL,
        `submitted_at` DATETIME DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        KEY `assistant_id` (`assistant_id`),
        KEY `submitted_at` (`submitted_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

// Create assistant version history table
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_assistants_ai_versions')) {
    // Check if assistants_ai table exists first
    if ($CI->db->table_exists(db_prefix() . 'contactcenter_assistants_ai')) {
        $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_assistants_ai_versions` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `assistant_id` int(11) NOT NULL,
            `version_number` int(11) NOT NULL,
            `ai_name` varchar(255) DEFAULT NULL,
            `ai_token` varchar(255) DEFAULT NULL,
            `model` varchar(100) DEFAULT NULL,
            `instructions` LONGTEXT DEFAULT NULL,
            `functions` TEXT DEFAULT NULL,
            `vector_id` varchar(255) DEFAULT NULL,
            `active` tinyint(1) DEFAULT 0,
            `created_by` int(11) DEFAULT NULL,
            `created_at` DATETIME DEFAULT NULL,
            `change_summary` TEXT DEFAULT NULL,
            INDEX `idx_assistant_id` (`assistant_id`),
            INDEX `idx_version_number` (`assistant_id`, `version_number`),
            FOREIGN KEY (`assistant_id`) REFERENCES `" . db_prefix() . "contactcenter_assistants_ai`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } else {
        // Create without foreign key if parent table doesn't exist yet
        $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_assistants_ai_versions` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `assistant_id` int(11) NOT NULL,
            `version_number` int(11) NOT NULL,
            `ai_name` varchar(255) DEFAULT NULL,
            `ai_token` varchar(255) DEFAULT NULL,
            `model` varchar(100) DEFAULT NULL,
            `instructions` LONGTEXT DEFAULT NULL,
            `functions` TEXT DEFAULT NULL,
            `vector_id` varchar(255) DEFAULT NULL,
            `active` tinyint(1) DEFAULT 0,
            `created_by` int(11) DEFAULT NULL,
            `created_at` DATETIME DEFAULT NULL,
            `change_summary` TEXT DEFAULT NULL,
            INDEX `idx_assistant_id` (`assistant_id`),
            INDEX `idx_version_number` (`assistant_id`, `version_number`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_assistants_files')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_assistants_files` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,                
                `assist_id` varchar(255) DEFAULT NULL, 
                `files_id` varchar(255) DEFAULT NULL, 
                `vector_id` varchar(255) DEFAULT NULL, 
                `files` varchar(255) DEFAULT NULL, 
                `staffid` int(11) DEFAULT NULL, 
                `date` DATETIME DEFAULT CURRENT_TIMESTAMP
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Table for assistant media files (images, audio, video)
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_assistants_media')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_assistants_media` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,                
                `assist_id` int(11) DEFAULT NULL, 
                `file_name` varchar(255) DEFAULT NULL, 
                `file_path` varchar(500) DEFAULT NULL, 
                `file_type` varchar(50) DEFAULT NULL,
                `file_size` int(11) DEFAULT NULL,
                `variable_name` varchar(255) DEFAULT NULL,
                `staffid` int(11) DEFAULT NULL, 
                `is_library` tinyint(1) DEFAULT 0,
                `date` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_assist_id` (`assist_id`),
                INDEX `idx_is_library` (`is_library`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} else {
    // Add is_library field if table exists but column doesn't
    if (!$CI->db->field_exists('is_library', db_prefix() . 'contactcenter_assistants_media')) {
        $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_media` ADD `is_library` tinyint(1) DEFAULT 0 AFTER `staffid`");
        $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_assistants_media` ADD INDEX `idx_is_library` (`is_library`)");
    }
}



if (!$CI->db->table_exists(db_prefix() . 'contactcenter_meta_head')) {
    $CI->db->query("CREATE TABLE tblcontactcenter_meta_head (
                id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                title varchar(255) DEFAULT NULL,               
                body TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                
                device_id int(11) DEFAULT NULL, 
                sourceId varchar(255) DEFAULT NULL,               
                dev_session varchar(255) DEFAULT NULL,               
                thumbnailUrl varchar(255) DEFAULT NULL,               
                sourceUrl varchar(255) DEFAULT NULL,
                amount  decimal(12,2) DEFAULT 0,               
                date DATETIME DEFAULT CURRENT_TIMESTAMP
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}


/**
 * 1.6.3
 */
if ($CI->db->query("SELECT * FROM `tblcontactcenter_server` WHERE `url` = ''")->num_rows() == 0) {
    $CI->db->query("INSERT INTO `tblcontactcenter_server` (`url`, `version`, `name`, `api_type`, `value_key`) VALUES           
    ('', '2', 'Inexxus Evolution (server 5)', 'axiom_evolution', '12d7cf0603036564a574eedcf904d84b');");
}

/**
 * 1.6.4
 */
add_option("contac_active_link_call", 0);

/**
 * 1.6.5
 */
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_message_queue')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_message_queue` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `number` varchar(50) NOT NULL,
                `msg` text DEFAULT NULL,
                `staffid` int(11) DEFAULT NULL,
                `url` varchar(255) DEFAULT NULL,
                `type` varchar(50) DEFAULT NULL,
                `file_name` varchar(255) DEFAULT NULL,
                `status` varchar(20) DEFAULT 'pending',
                `reply_id` varchar(50) DEFAULT NULL,
                `edit_id` varchar(50) DEFAULT NULL,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

/*
* 1.6.6
*/
if (!$CI->db->field_exists('fromMe', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `fromMe` int(11) DEFAULT 1");
}
if (!$CI->db->field_exists('sincronizar', db_prefix() . 'contactcenter_contact')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD `sincronizar` int(11) DEFAULT 0");
}
if (!$CI->db->field_exists('remoteJid', db_prefix() . 'contactcenter_contact')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_contact` ADD `remoteJid` varchar(255) DEFAULT NULL");
}

/**
 * 1.6.7
 */
if (!$CI->db->field_exists('sent_source', db_prefix() . 'contactcenter_message')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD `sent_source` varchar(50) DEFAULT 'crm'");
}


if (!$CI->db->field_exists('is_whatsapp', db_prefix() . 'leads')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "leads` ADD `is_whatsapp` varchar(50) DEFAULT 'pending'");
}

if (!$CI->db->field_exists('msg_reaction', db_prefix() . 'contactcenter_message')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message` ADD `msg_reaction` TEXT DEFAULT NULL");
}

if ($CI->db->query("SELECT * FROM `tblcontactcenter_server` WHERE `url` = 'https://api2.inx.net.br/'")->num_rows() == 0) {
    $CI->db->query("INSERT INTO `tblcontactcenter_server` (`url`, `version`, `name`, `api_type`, `value_key`) VALUES           
    ('https://api2.inx.net.br/', '2', 'Inexxus Evolution (server 6)', 'axiom_evolution', '12d7cf0603036564a574eedcf904d84b');");
}

/**
 * 1.6.8
 */
if (!$CI->db->field_exists('api_local_status', db_prefix() . 'contactcenter_device')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD `api_local_status` varchar(50) DEFAULT 'close'");
}

if (!$CI->db->field_exists('api_web_status', db_prefix() . 'contactcenter_device')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD `api_web_status` varchar(50) DEFAULT 'close'");
}

if (!$CI->db->field_exists('show_messages_all_devices', db_prefix() . 'contactcenter_device')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD `show_messages_all_devices` TINYINT(1) DEFAULT 0");
}

/**
 * 1.7.1 - Contact Center Optimization (Throttling & Anti-Ban)
 */

// Add throttling fields to contactcenter_leads_engine table
if (!$CI->db->field_exists('daily_limit', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `daily_limit` INT(11) DEFAULT 1000");
}

if (!$CI->db->field_exists('batch_size', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `batch_size` INT(11) DEFAULT 5");
}

if (!$CI->db->field_exists('batch_cooldown', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `batch_cooldown` INT(11) DEFAULT 5");
}

if (!$CI->db->field_exists('message_interval_min', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `message_interval_min` INT(11) DEFAULT 1");
}

if (!$CI->db->field_exists('message_interval_max', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `message_interval_max` INT(11) DEFAULT 3");
}

if (!$CI->db->field_exists('is_warmup_active', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `is_warmup_active` TINYINT(1) DEFAULT 0");
}

if (!$CI->db->field_exists('warmup_start_date', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `warmup_start_date` DATETIME DEFAULT NULL");
}

if (!$CI->db->field_exists('stop_on_reply', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `stop_on_reply` TINYINT(1) DEFAULT 1");
}

// Add health tracking fields to contactcenter_device table
if (!$CI->db->field_exists('msgs_sent_24h', db_prefix() . 'contactcenter_device')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD `msgs_sent_24h` INT(11) DEFAULT 0");
}

if (!$CI->db->field_exists('msgs_received_24h', db_prefix() . 'contactcenter_device')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD `msgs_received_24h` INT(11) DEFAULT 0");
}

if (!$CI->db->field_exists('health_score', db_prefix() . 'contactcenter_device')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD `health_score` DECIMAL(5,2) DEFAULT 0.00");
}

if (!$CI->db->field_exists('status_mode', db_prefix() . 'contactcenter_device')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD `status_mode` ENUM('active', 'warming_up', 'cooldown') DEFAULT 'active'");
}

// Add is_active field to device table
if (!$CI->db->field_exists('is_active', db_prefix() . 'contactcenter_device')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD `is_active` tinyint(1) DEFAULT 1");
}

// Add sales_knowledge field to device table for AXIOM Intelligence
if (!$CI->db->field_exists('sales_knowledge', db_prefix() . 'contactcenter_device')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` ADD `sales_knowledge` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `contract_msg`");
}

// Add message_sender_type field to message_triggers table
if (!$CI->db->field_exists('message_sender_type', db_prefix() . 'contactcenter_message_triggers')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_message_triggers` ADD `message_sender_type` ENUM('contact', 'staff', 'both') DEFAULT 'contact'");
}

// Create maturation_scripts table for auto-chat conversations
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_maturation_scripts')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_maturation_scripts` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `category` VARCHAR(50) DEFAULT NULL,
        `dialogue_json` JSON DEFAULT NULL,
        `date_created` DATETIME DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Create table to track campaign daily progress
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_campaign_daily_progress')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_campaign_daily_progress` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `campaign_id` INT(11) DEFAULT NULL,
        `date` DATE DEFAULT NULL,
        `messages_sent` INT(11) DEFAULT 0,
        `created_at` DATETIME DEFAULT NULL,
        UNIQUE KEY `unique_campaign_date` (`campaign_id`, `date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Create table to track leads removed from campaigns (kill switch)
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_campaign_excluded_leads')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_campaign_excluded_leads` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `campaign_id` INT(11) DEFAULT NULL,
        `lead_id` INT(11) DEFAULT NULL,
        `reason` VARCHAR(255) DEFAULT NULL,
        `date_excluded` DATETIME DEFAULT NULL,
        UNIQUE KEY `unique_campaign_lead` (`campaign_id`, `lead_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Add throttling fields to contactcenter_conversation_engine table (Campaign)
if (!$CI->db->field_exists('daily_limit', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `daily_limit` INT(11) DEFAULT 1000");
}

if (!$CI->db->field_exists('batch_size', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `batch_size` INT(11) DEFAULT 5");
}

if (!$CI->db->field_exists('batch_cooldown', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `batch_cooldown` INT(11) DEFAULT 5");
}

if (!$CI->db->field_exists('current_batch_count', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `current_batch_count` INT(11) DEFAULT 0");
}

if (!$CI->db->field_exists('last_batch_time', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `last_batch_time` DATETIME NULL");
}

if (!$CI->db->field_exists('message_interval_min', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `message_interval_min` INT(11) DEFAULT 1");
}

if (!$CI->db->field_exists('message_interval_max', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `message_interval_max` INT(11) DEFAULT 3");
}

if (!$CI->db->field_exists('is_warmup_active', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `is_warmup_active` TINYINT(1) DEFAULT 0");
}

if (!$CI->db->field_exists('warmup_start_date', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `warmup_start_date` DATETIME DEFAULT NULL");
}

if (!$CI->db->field_exists('stop_on_reply', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `stop_on_reply` TINYINT(1) DEFAULT 1");
}

// Add advanced campaign features fields
if (!$CI->db->field_exists('vcard_enable', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `vcard_enable` TINYINT(1) DEFAULT 0");
}

if (!$CI->db->field_exists('vcard_name', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `vcard_name` VARCHAR(255) DEFAULT NULL");
}

if (!$CI->db->field_exists('vcard_phone', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `vcard_phone` VARCHAR(50) DEFAULT NULL");
}

if (!$CI->db->field_exists('inbound_bait_enable', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `inbound_bait_enable` TINYINT(1) DEFAULT 0");
}

if (!$CI->db->field_exists('inbound_bait_message', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `inbound_bait_message` TEXT DEFAULT NULL");
}

if (!$CI->db->field_exists('safe_groups_enable', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD `safe_groups_enable` TINYINT(1) DEFAULT 0");
}

// Same fields for conversation_engine (Campaign)
if (!$CI->db->field_exists('vcard_enable', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `vcard_enable` TINYINT(1) DEFAULT 0");
}

if (!$CI->db->field_exists('vcard_name', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `vcard_name` VARCHAR(255) DEFAULT NULL");
}

if (!$CI->db->field_exists('vcard_phone', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `vcard_phone` VARCHAR(50) DEFAULT NULL");
}

if (!$CI->db->field_exists('inbound_bait_enable', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `inbound_bait_enable` TINYINT(1) DEFAULT 0");
}

if (!$CI->db->field_exists('inbound_bait_message', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `inbound_bait_message` TEXT DEFAULT NULL");
}

if (!$CI->db->field_exists('safe_groups_enable', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `safe_groups_enable` TINYINT(1) DEFAULT 0");
}

if (!$CI->db->field_exists('backup_phone_field', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `backup_phone_field` VARCHAR(255) DEFAULT NULL");
}

if (!$CI->db->field_exists('backup_phone_country_code', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `backup_phone_country_code` VARCHAR(10) DEFAULT '55'");
}

// Add birthday campaign support
if (!$CI->db->field_exists('birthday_field', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` ADD `birthday_field` INT(11) DEFAULT NULL COMMENT 'Custom field ID containing lead birthday date'");
}
if ($CI->db->field_exists('date_filter_type', db_prefix() . 'contactcenter_conversation_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_conversation_engine` MODIFY COLUMN `date_filter_type` ENUM('creation_date', 'last_contact', 'birthday') DEFAULT 'creation_date'");
}

// Create safe groups table
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_safe_groups')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_safe_groups` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `group_id` VARCHAR(255) DEFAULT NULL,
        `group_name` VARCHAR(255) DEFAULT NULL,
        `device_id` INT(11) DEFAULT NULL,
        `is_active` TINYINT(1) DEFAULT 1,
        `date_created` DATETIME DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Create device pairs table for maturation conversations
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_device_pairs')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_device_pairs` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `device_a_id` INT(11) DEFAULT NULL,
        `device_b_id` INT(11) DEFAULT NULL,
        `is_active` TINYINT(1) DEFAULT 1,
        `priority` INT(11) DEFAULT 0,
        `date_created` DATETIME DEFAULT NULL,
        UNIQUE KEY `unique_pair` (`device_a_id`, `device_b_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

/**
 * Ads Analytics Module - Version 1.7.0
 */
// Table for uploaded media files (images, videos)
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_ads_media')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_ads_media` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `file_name` VARCHAR(255) NOT NULL,
        `file_path` VARCHAR(500) NOT NULL,
        `file_type` VARCHAR(50) NOT NULL COMMENT 'image, video',
        `file_size` INT(11) DEFAULT NULL,
        `thumbnail_path` VARCHAR(500) DEFAULT NULL,
        `staffid` INT(11) DEFAULT NULL,
        `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `date_updated` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Table for Ad Sets (groups of creatives that share a budget)
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_ads_sets')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_ads_sets` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `is_active` TINYINT(1) DEFAULT 1,
        `staffid` INT(11) DEFAULT NULL,
        `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `date_updated` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Table for creatives (campaign + media combinations)
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_ads_creatives')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_ads_creatives` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `campaign_name` VARCHAR(255) NOT NULL COMMENT 'Campaign name from custom field',
        `media_id` INT(11) NOT NULL,
        `ad_set_id` INT(11) DEFAULT NULL COMMENT 'Ad Set this creative belongs to (optional)',
        `is_active` TINYINT(1) DEFAULT 1,
        `staffid` INT(11) DEFAULT NULL,
        `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `date_updated` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`media_id`) REFERENCES `" . db_prefix() . "contactcenter_ads_media`(`id`) ON DELETE RESTRICT,
        FOREIGN KEY (`ad_set_id`) REFERENCES `" . db_prefix() . "contactcenter_ads_sets`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} else {
    // Migration: Add ad_set_id column if it doesn't exist
    $creatives_table = db_prefix() . 'contactcenter_ads_creatives';
    $columns = $CI->db->list_fields($creatives_table);
    if (!in_array('ad_set_id', $columns)) {
        // Check if ad_sets table exists before adding foreign key
        if ($CI->db->table_exists(db_prefix() . 'contactcenter_ads_sets')) {
            $CI->db->query("ALTER TABLE `{$creatives_table}` 
                ADD COLUMN `ad_set_id` INT(11) DEFAULT NULL COMMENT 'Ad Set this creative belongs to (optional)' AFTER `media_id`,
                ADD FOREIGN KEY (`ad_set_id`) REFERENCES `" . db_prefix() . "contactcenter_ads_sets`(`id`) ON DELETE SET NULL");
        } else {
            $CI->db->query("ALTER TABLE `{$creatives_table}` 
                ADD COLUMN `ad_set_id` INT(11) DEFAULT NULL COMMENT 'Ad Set this creative belongs to (optional)' AFTER `media_id`");
        }
    }
}

// Table for investment tracking per creative or ad set
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_ads_investments')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_ads_investments` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `budget_type` ENUM('creative', 'ad_set') DEFAULT 'creative' COMMENT 'Type of budget: per creative or per ad set',
        `creative_id` INT(11) DEFAULT NULL COMMENT 'Creative ID (if budget_type is creative)',
        `ad_set_id` INT(11) DEFAULT NULL COMMENT 'Ad Set ID (if budget_type is ad_set)',
        `daily_investment` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        `start_date` DATE NOT NULL,
        `end_date` DATE DEFAULT NULL,
        `is_active` TINYINT(1) DEFAULT 1,
        `staffid` INT(11) DEFAULT NULL,
        `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `date_updated` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`creative_id`) REFERENCES `" . db_prefix() . "contactcenter_ads_creatives`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`ad_set_id`) REFERENCES `" . db_prefix() . "contactcenter_ads_sets`(`id`) ON DELETE CASCADE,
        INDEX `idx_budget_type` (`budget_type`, `creative_id`, `ad_set_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} else {
    // Migration: Add budget_type and ad_set_id columns if they don't exist
    $investments_table = db_prefix() . 'contactcenter_ads_investments';
    $columns = $CI->db->list_fields($investments_table);

    if (!in_array('budget_type', $columns)) {
        $CI->db->query("ALTER TABLE `{$investments_table}` 
            ADD COLUMN `budget_type` ENUM('creative', 'ad_set') DEFAULT 'creative' COMMENT 'Type of budget: per creative or per ad set' AFTER `id`");
    }

    if (!in_array('ad_set_id', $columns)) {
        // Check if ad_sets table exists before adding foreign key
        if ($CI->db->table_exists(db_prefix() . 'contactcenter_ads_sets')) {
            $CI->db->query("ALTER TABLE `{$investments_table}` 
                ADD COLUMN `ad_set_id` INT(11) DEFAULT NULL COMMENT 'Ad Set ID (if budget_type is ad_set)' AFTER `creative_id`,
                ADD FOREIGN KEY (`ad_set_id`) REFERENCES `" . db_prefix() . "contactcenter_ads_sets`(`id`) ON DELETE CASCADE");
        } else {
            $CI->db->query("ALTER TABLE `{$investments_table}` 
                ADD COLUMN `ad_set_id` INT(11) DEFAULT NULL COMMENT 'Ad Set ID (if budget_type is ad_set)' AFTER `creative_id`");
        }
    }

    // Make creative_id nullable (it should be if budget_type is ad_set)
    $column_info = $CI->db->query("SHOW COLUMNS FROM `{$investments_table}` WHERE Field = 'creative_id'")->row();
    if ($column_info && strpos($column_info->Type, 'NULL') === false) {
        $CI->db->query("ALTER TABLE `{$investments_table}` 
            MODIFY COLUMN `creative_id` INT(11) DEFAULT NULL COMMENT 'Creative ID (if budget_type is creative)'");
    }
}

// Table to link leads to creatives (when a lead comes from an ad creative)
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_ads_leads')) {
    $CI->db->query("CREATE TABLE `tblcontactcenter_ads_leads` (
        `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `creative_id` INT(11) NOT NULL,
        `lead_id` INT(11) NOT NULL,
        `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`creative_id`) REFERENCES `" . db_prefix() . "contactcenter_ads_creatives`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`lead_id`) REFERENCES `" . db_prefix() . "leads`(`id`) ON DELETE CASCADE,
        UNIQUE KEY `unique_creative_lead` (`creative_id`, `lead_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Omni Pilot Wizard Tables
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_omni_pilot_sessions')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_omni_pilot_sessions` (
        `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `staffid` int(11) DEFAULT NULL,
        `device_id` int(11) DEFAULT NULL,
        `goal_target` int(11) DEFAULT 0,
        `goal_status_id` int(11) DEFAULT NULL,
        `deadline_date` datetime DEFAULT NULL,
        `tag_id` int(11) DEFAULT NULL,
        `campaign_id` int(11) DEFAULT NULL,
        `leads_engine_id` int(11) DEFAULT NULL,
        `status` enum('pending','importing','campaign_setup','message_setup','followup_setup','active','completed','failed','cancelled') DEFAULT 'pending',
        `progress_percentage` int(11) DEFAULT 0,
        `current_phase` varchar(255) DEFAULT NULL,
        `current_step` int(11) DEFAULT NULL,
        `step_detail` varchar(255) DEFAULT NULL,
        `error_message` TEXT DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        `completed_at` datetime DEFAULT NULL,
        INDEX `idx_staffid` (`staffid`),
        INDEX `idx_status` (`status`),
        INDEX `idx_device_id` (`device_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

if (!$CI->db->table_exists(db_prefix() . 'contactcenter_omni_pilot_steps')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_omni_pilot_steps` (
        `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `session_id` int(11) DEFAULT NULL,
        `step_number` int(11) DEFAULT NULL,
        `step_data` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `completed` tinyint(1) DEFAULT 0,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        INDEX `idx_session_id` (`session_id`),
        INDEX `idx_step_number` (`session_id`, `step_number`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// Add new columns for step-based progress if they don't exist (run this even if table already exists)
if ($CI->db->table_exists(db_prefix() . 'contactcenter_omni_pilot_sessions')) {
    $columns = $CI->db->list_fields(db_prefix() . 'contactcenter_omni_pilot_sessions');
    if (!in_array('current_step', $columns)) {
        $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_omni_pilot_sessions` ADD `current_step` DECIMAL(3,1) DEFAULT NULL AFTER `current_phase`");
    }
    if (!in_array('step_detail', $columns)) {
        $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_omni_pilot_sessions` ADD `step_detail` varchar(255) DEFAULT NULL AFTER `current_step`");
    }
}

// Omni Pilot Templates Table
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_omni_pilot_templates')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_omni_pilot_templates` (
        `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `staffid` int(11) DEFAULT NULL,
        `name` varchar(255) DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `wizard_data` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `is_active` tinyint(1) DEFAULT 1,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        INDEX `idx_staffid` (`staffid`),
        INDEX `idx_is_active` (`is_active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

// AI Interactions Log Table - for tracking AI interactions with leads
if (!$CI->db->table_exists(db_prefix() . 'contactcenter_ai_interactions')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "contactcenter_ai_interactions` (
        `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `lead_id` int(11) DEFAULT NULL,
        `device_id` int(11) DEFAULT NULL,
        `thread_id` varchar(255) DEFAULT NULL,
        `run_id` varchar(255) DEFAULT NULL,
        `interaction_type` enum('message','function_call','function_response','error','decision') DEFAULT 'message',
        `user_message` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `ai_response` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `function_name` varchar(255) DEFAULT NULL,
        `function_arguments` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `function_result` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `raw_input` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `raw_output` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `status` varchar(50) DEFAULT NULL,
        `error_message` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `context_data` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
        `created_at` datetime DEFAULT NULL,
        INDEX `idx_lead_id` (`lead_id`),
        INDEX `idx_device_id` (`device_id`),
        INDEX `idx_thread_id` (`thread_id`),
        INDEX `idx_interaction_type` (`interaction_type`),
        INDEX `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
}

if (!$CI->db->field_exists('last_run', db_prefix() . 'contactcenter_leads_engine')) {
    $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_leads_engine` ADD COLUMN `last_run` DATETIME DEFAULT NULL");
}
