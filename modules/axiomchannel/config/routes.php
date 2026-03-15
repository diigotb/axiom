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
$route['axiomchannel/webhook/(:any)']               = 'axiomchannel/webhook/$1';
