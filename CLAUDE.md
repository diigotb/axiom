# AXIOM — CLAUDE.md

## Stack
- Perfex CRM (CodeIgniter 3, PHP 8.1)
- MySQL 8.0.30 (Laragon 6.0)
- Módulo: `modules/axiomchannel/`
- WhatsApp: Evolution API (instância local)
- IA: Gemini 2.5 Flash (`AxiomChannel_Gemini.php`, MAX_TOKENS=8192)

## Convenções do módulo AxiomChannel
- Prefixo de tabelas: `tblaxch_` (usar `db_prefix() . 'axch_...'` no código)
- Assets CSS: `module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css')`
- Toda view começa com `init_head()` e termina com `init_tail()`
- AJAX: sempre verificar `$this->input->is_ajax_request()` antes de responder JSON
- CSRF: incluir `[CSRF_NAME]: CSRF_TOKEN` em todos os fetch POST

## Tabelas existentes (db_axiom)
- `tblaxch_devices` — dispositivos WhatsApp
- `tblaxch_contacts` — contatos do WhatsApp
- `tblaxch_messages` — mensagens
- `tblaxch_pipelines` — pipelines do CRM
- `tblaxch_pipeline_stages` — estágios do pipeline
- `tblaxch_pipeline_leads` — leads no kanban
- `tblaxch_assistants` — config do assistente IA (+ campos appointment_* e assistant_types)
- `tblaxch_knowledge_base` — base de conhecimento da IA
- `tblaxch_knowledge_media` — biblioteca de mídia (id, assistant_id, file_type, original_name, file_path, file_size, mime_type, media_label, description, created_at)
- `tblaxch_assistant_stages` — etapas do fluxo (stage_name, action, question, media_id, media_send_position: before/with/after_message)
- `tblaxch_appointments` — agendamentos (contact_id, device_id, start/end_datetime, status, google_event_id)
- `tblaxch_google_calendar` — config OAuth Google (device_id, google_account, calendar_id, access/refresh_token)
- `tblaxch_contracts` — contratos com assinatura digital (sign_token único, signer_name/cpf/ip, document_hash)
- `tblaxch_contract_templates` — templates de contrato (device_id, name, content, variables)
- `tblaxch_automations` — automações recorrentes (type ENUM: birthday/invoice/followup/inactive/appointment/satisfaction)
- `tblaxch_automation_log` — log de disparos de automação (automation_id, contact_id, status)
- `tblaxch_staff_config` — configuração dos atendentes (staff_id UNIQUE, whatsapp, department, is_available)
- `tblaxch_meta_connections` — conexões Facebook/Instagram (device_id, page_id, page_access_token, instagram_account_id, connection_type ENUM: facebook/instagram/both)

## Meta API (Facebook + Instagram)
- Webhook URL: `http://localhost/axiom/admin/axiomchannel/meta_webhook`
- Verify Token: `axiom_meta_webhook`
- Versão API: `v18.0`
- Colunas adicionadas: `tblaxch_contacts.channel`, `tblaxch_contacts.external_id`, `tblaxch_contacts.channel_data`
- Colunas adicionadas: `tblaxch_messages.channel`, `tblaxch_messages.external_message_id`

## Menu lateral (posições)
1. Todas as Conversas
2. Dispositivos
3. CRM Pipeline
4. Assistente IA
5. Agendamentos
6. Contratos

## PHP/MySQL binários (Laragon)
- PHP: `C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe`
- MySQL: `C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root db_axiom`
- `ADD COLUMN IF NOT EXISTS` NÃO suportado nesta versão do MySQL — verificar colunas antes de adicionar

## Padrão de cores (palette)
`['#E53E3E','#DD6B20','#D69E2E','#38A169','#2D7A6B','#3182CE','#5A67D8','#805AD5','#D53F8C','#1B3A4B','#4A5568','#718096','#F6AD55','#68D391','#63B3ED','#B794F4']`
Cor primária do módulo: `#2D7A6B`
