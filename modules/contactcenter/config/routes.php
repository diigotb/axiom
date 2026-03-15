<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

$route['contactcenter/webhook/qrcode-updated'] = 'webhook/qrcode_updated';
$route['contactcenter/webhook/connection-update'] = 'webhook/connection_update';
$route['contactcenter/webhook/send-message'] = 'webhook/send_message';
$route['contactcenter/webhook/messages-upsert'] = 'webhook/messages_upsert';
$route['contactcenter/webhook/contacts-update'] = 'webhook/contacts_update';
$route['contactcenter/webhook/messages-update'] = 'webhook/messages_update';
$route['contactcenter/webhook/messages-set'] = 'webhook/messages_set';
$route['contactcenter/webhook/messages-delete'] = 'webhook/messages_delete';
$route['contactcenter/webhook/extensao_whatsapp'] = 'webhook/extensao_whatsapp';

$route['contactcenter/swagger/json'] = 'swagger/json';
$route['contactcenter/docs'] = 'webhook/docs';
