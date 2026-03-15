<?php

$config = [
	'contactcenter/assistant_form_public.*',  // Must match index/token and save (regex: .* matches /save, /index/xxx)
	'contactcenter/appointment_confirm_public.*',
	'contactcenter/webhook',	
	'contactcenter/webhook/active_module',	
	'contactcenter/webhook/get_device_status',	
	'contactcenter/webhook/get_device_status',	
	'contactcenter/webhook/qrcode',
	'contactcenter/chat/start_chat',
	'contactcenter/chat/send_msg',
	'contactcenter/webhook/qrcode-updated',	
	'contactcenter/webhook/connection-update',	
	'contactcenter/webhook/send-message',	
	'contactcenter/webhook/messages-upsert',	
	'contactcenter/webhook/contacts-update',	
	'contactcenter/webhook/messages-update',	
	'contactcenter/webhook/messages-set',	
	'contactcenter/webhook/update_module',	
	'contactcenter/save_automation',
	'contactcenter/save_assistant_visual_builder',
	'contactcenter/api/text',
	'contactcenter/api/media',	
	'contactcenter/webhook/extensao_whatsapp',	
	'contactcenter/webhook/messages-delete',	
	'contactcenter/webhook/messages_local',	
	'contactcenter/webhook/messages_local_status',	
	'contactcenter/webhook/local_verify_whats',	
	'contactcenter/webhook/messages_logs',	
];

return  $config;
