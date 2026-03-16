<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['axiomchannel']                              = 'axiomchannel/index';
$route['axiomchannel/inbox']                        = 'axiomchannel/inbox';
$route['axiomchannel/chat/(:num)']                  = 'axiomchannel/chat/$1';
$route['axiomchannel/devices']                      = 'axiomchannel/devices';
$route['axiomchannel/add_device']                   = 'axiomchannel/add_device';
$route['axiomchannel/delete_device/(:num)']         = 'axiomchannel/delete_device/$1';
$route['axiomchannel/qrcode/(:num)']                = 'axiomchannel/qrcode/$1';
$route['axiomchannel/device_status/(:num)']         = 'axiomchannel/device_status/$1';
$route['axiomchannel/send_message']                 = 'axiomchannel/send_message';
$route['axiomchannel/get_messages']                 = 'axiomchannel/get_messages';
$route['axiomchannel/get_contacts']                 = 'axiomchannel/get_contacts';
$route['axiomchannel/transfer']                     = 'axiomchannel/transfer';
$route['axiomchannel/update_contact_status']        = 'axiomchannel/update_contact_status';
$route['axiomchannel/webhook/(:any)']               = 'axiomwebhook/receive/$1';
$route['axiomchannel/pipeline']                  = 'axiomchannel/pipeline';
$route['axiomchannel/pipeline/(:num)']           = 'axiomchannel/pipeline/$1';
$route['axiomchannel/pipeline_wizard']           = 'axiomchannel/pipeline_wizard';
$route['axiomchannel/pipeline_generate']         = 'axiomchannel/pipeline_generate';
$route['axiomchannel/pipeline_save']             = 'axiomchannel/pipeline_save';
$route['axiomchannel/lead_move']                 = 'axiomchannel/lead_move';
$route['axiomchannel/lead_reorder']              = 'axiomchannel/lead_reorder';
$route['axiomchannel/lead_create']               = 'axiomchannel/lead_create';
$route['axiomchannel/lead_update']               = 'axiomchannel/lead_update';
$route['axiomchannel/lead_move_from_chat']       = 'axiomchannel/lead_move_from_chat';
$route['axiomchannel/update_stage']             = 'axiomchannel/update_stage';
$route['axiomchannel/assistant']                = 'axiomchannel/assistant';
$route['axiomchannel/assistant/(:num)']         = 'axiomchannel/assistant/$1';
$route['axiomchannel/assistant_save']           = 'axiomchannel/assistant_save';
$route['axiomchannel/knowledge_save']           = 'axiomchannel/knowledge_save';
$route['axiomchannel/knowledge_delete']         = 'axiomchannel/knowledge_delete';
$route['axiomchannel/knowledge_reorder']        = 'axiomchannel/knowledge_reorder';
$route['axiomchannel/stage_save']               = 'axiomchannel/stage_save';
$route['axiomchannel/stage_delete']             = 'axiomchannel/stage_delete';
$route['axiomchannel/media_upload']             = 'axiomchannel/media_upload';
$route['axiomchannel/media_delete']             = 'axiomchannel/media_delete';
$route['axiomchannel/media_list/(:num)']        = 'axiomchannel/media_list/$1';

// APPOINTMENTS
$route['axiomchannel/appointments']                = 'axiomchannel/appointments';
$route['axiomchannel/appointment_save']            = 'axiomchannel/appointment_save';
$route['axiomchannel/appointment_cancel']          = 'axiomchannel/appointment_cancel';
$route['axiomchannel/appointment_slots']           = 'axiomchannel/appointment_slots';
$route['axiomchannel/google_calendar_connect']     = 'axiomchannel/google_calendar_connect';
$route['axiomchannel/google_calendar_callback']    = 'axiomchannel/google_calendar_callback';
$route['axiomchannel/google_calendar_save']        = 'axiomchannel/google_calendar_save';

// META — Facebook + Instagram
$route['axiomchannel/meta_connect']     = 'axiomchannel/meta_connect';
$route['axiomchannel/meta_save']        = 'axiomchannel/meta_save';
$route['axiomchannel/meta_webhook']     = 'axiomchannel/meta_webhook';
$route['axiomchannel/meta_send_message'] = 'axiomchannel/meta_send_message';

// CONTRACTS
$route['axiomchannel/contracts']                   = 'axiomchannel/contracts';
$route['axiomchannel/contract_new']                = 'axiomchannel/contract_new';
$route['axiomchannel/contract_save']               = 'axiomchannel/contract_save';
$route['axiomchannel/contract_send']               = 'axiomchannel/contract_send';
$route['axiomchannel/contract_sign/(:any)']        = 'axiomchannel/contract_sign/$1';
$route['axiomchannel/contract_sign_submit']        = 'axiomchannel/contract_sign_submit';
$route['axiomchannel/contract_pdf/(:num)']         = 'axiomchannel/contract_pdf/$1';
$route['axiomchannel/contract_templates']          = 'axiomchannel/contract_templates';
$route['axiomchannel/contract_template_save']      = 'axiomchannel/contract_template_save';
