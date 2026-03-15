<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
  Module Name: Contact Center 2.0
  Description: Módulo de integração com Whatsapp
  Version: 1.9.0
  Author: AXIOM
  Author URI: https://axiom.com.br
  Requires at least: 2.3.2
 */


$CI = &get_instance();
if (!defined('MODULE_CONTACTCENTER')) {
    define('MODULE_CONTACTCENTER', basename(__DIR__));
}

hooks()->add_action('admin_init', 'contactcenter_init_menu_items');
hooks()->add_action('admin_init', 'contactcenter_ensure_birthday_field_schema');
hooks()->add_action('app_admin_head', 'contactcenter_add_head_components');
hooks()->add_action('admin_navbar_start', 'contactcenter_add_device_status_widget');

register_cron_task('contactcenter_script_autoupdate_cron');
register_cron_task('contactcenter_auto_followup_cron');
register_cron_task('contactcenter_invoice_followup_cron');

register_activation_hook(MODULE_CONTACTCENTER, 'contactcenter_module_activation_hook');

hooks()->add_action('pre_activate_module', 'active__key_contactcenter');
function active__key_contactcenter($module_name)
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');

    if ('contactcenter' == $module_name['system_name']) {

        //delete_option('contactcenter_verification_id');
        if (!option_exists('contactcenter_verification_id') || get_option('contactcenter_verification_id') == '') {
            echo $CI->load->view('contactcenter/activate', null, true);
            exit;
        }

        $plano = $CI->db->get(db_prefix() . 'contactcenter_saas_planos')->row();
        if ($plano) {
            if ($plano->plan_status == 0) {
                get_instance()->app_modules->deactivate("contactcenter");
            }
        }
    }
}



function contactcenter_ensure_birthday_field_schema()
{
    $CI = &get_instance();
    $tbl = db_prefix() . 'contactcenter_conversation_engine';
    if (!$CI->db->field_exists('birthday_field', $tbl)) {
        $CI->db->query("ALTER TABLE `{$tbl}` ADD `birthday_field` INT(11) DEFAULT NULL COMMENT 'Custom field ID containing lead birthday date'");
    }
    if ($CI->db->field_exists('date_filter_type', $tbl)) {
        $CI->db->query("ALTER TABLE `{$tbl}` MODIFY COLUMN `date_filter_type` ENUM('creation_date', 'last_contact', 'birthday') DEFAULT 'creation_date'");
    }
}

function contactcenter_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Load the module helper
 */
$CI = &get_instance();
$CI->load->helper('contactcenter/contactcenter');
$CI->load->library('contactcenter/ChatWeb');
$CI->load->library("contactcenter/Api_brasil");
$CI->load->library("contactcenter/Axiom_evolution");
$CI->load->library("contactcenter/chatbot");

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files('contactcenter', ['contactcenter']);

function contactcenter_init_menu_items()
{
    $CI = &get_instance();
    if (staff_can('view', 'contactcenter') || staff_can('view_own', 'contactcenter')) {
        if (option_exists('contactcenter_verification_id')) {
            if (!is_valid_plan()) {
                hooks()->add_action('before_start_render_content', 'is_valid_aviso');
                return;
            }
        }
        $CI->app_menu->add_sidebar_menu_item('contactcenter', [
            'name' => _l("contac_modulo_name"),
            'collapse' => true,
            'position' => 10,
            'icon_img' => 'modules/contactcenter/icon_omniU_white.png',
            'icon' => 'fa-brands fa-whatsapp',
        ]);
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'contactcenter_plano',
            'name' => _l("contac_menu_plano"),
            'href' => admin_url("contactcenter/plano"),
            'position' => 5,
            'icon' => 'fa fa-gem',
        ]);
        if (has_permission('contactcenter', '', 'create')) {
            $CI->app_menu->add_sidebar_children_item('contactcenter', [
                'slug' => 'device',
                'name' => _l("contac_device"),
                'href' => admin_url("contactcenter/device"),
                'position' => 5,
                'icon' => 'fa fa-mobile-alt',
            ]);
        }
    }


    $CI->app_menu->add_sidebar_children_item('contactcenter', [
        'slug' => 'chatall',
        'name' => _l("contac_chat_all"),
        'href' => admin_url("contactcenter/chatall"),
        'position' => 5,
        'icon' => 'fa fa-comments',
    ]);


    if (has_permission('contactcenter', '', 'engine')) {
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'conversation_engine',
            'name' => _l("contac_conversation_engine"),
            'href' => admin_url("contactcenter/conversation_engine"),
            'position' => 3,
            'icon' => 'fa fa-bullhorn',
        ]);
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'leads_engine',
            'name' => _l("leads_engine_title"),
            'href' => admin_url("contactcenter/leads_engine"),
            'position' => 4,
            'icon' => 'fa fa-paper-plane',
        ]);
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug'     => 'auto_followup',
            'name'     => _l('contac_auto_followup_menu'),
            'href'     => admin_url('contactcenter/auto_followup'),
            'position' => 5,
            'icon'     => 'fa fa-robot',
        ]);
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug'     => 'auto_followup_queue',
            'name'     => _l('contac_auto_followup_queue_menu'),
            'href'     => admin_url('contactcenter/auto_followup_queue'),
            'position' => 6,
            'icon'     => 'fa fa-clock',
        ]);
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug'     => 'invoice_followup',
            'name'     => _l('contac_invoice_followup_menu'),
            'href'     => admin_url('contactcenter/invoice_followup'),
            'position' => 7,
            'icon'     => 'fa fa-file-invoice-dollar',
        ]);
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug'     => 'invoice_followup_queue',
            'name'     => _l('contac_invoice_followup_queue_menu'),
            'href'     => admin_url('contactcenter/invoice_followup_queue'),
            'position' => 8,
            'icon'     => 'fa fa-receipt',
        ]);
    }

    if (has_permission('contactcenter', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'message_triggers',
            'name' => _l("message_triggers"),
            'href' => admin_url("contactcenter/message_triggers"),
            'position' => 5,
            'icon' => 'fa fa-bolt',
        ]);
    }

    // if (has_permission('contactcenter', '', 'grupos')) {
    //     $CI->app_menu->add_sidebar_children_item('contactcenter', [
    //         'slug' => 'group', // Required ID/slug UNIQUE for the child menu
    //         'name' => _l("contact_group"), // The name if the item
    //         'href' => admin_url("contactcenter/group"), // URL of the item
    //         'position' => 5, // The menu position
    //         'icon' => 'fa fa-money', // Font awesome icon
    //     ]);
    // }


    if (has_permission('contactcenter', '', 'import_leads')) {
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'import_leads',
            'name' => _l("import_leads"),
            'href' => admin_url("contactcenter/import_leads"),
            'position' => 5,
            'icon' => 'fa fa-file-import',
        ]);
    }


    // $CI->app_menu->add_sidebar_children_item('contactcenter', [
    //     'slug' => 'chattransfer', // Required ID/slug UNIQUE for the child menu
    //     'name' => _l("contac_page_chattransfer"), // The name if the item
    //     'href' => admin_url("contactcenter/chattransfer"), // URL of the item
    //     'position' => 5, // The menu position
    //     'icon' => 'fa fa-money', // Font awesome icon
    // ]);

    if (has_permission('contactcenter', '', 'fluxo')) {
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'drawflow',
            'name' => _l("drawflow_page"),
            'href' => admin_url("contactcenter/fluxo"),
            'position' => 5,
            'icon' => 'fa fa-project-diagram',
        ]);
    }


    // if (has_permission('contactcenter', '', 'chat_web')) {
    //     $CI->app_menu->add_sidebar_children_item('contactcenter', [
    //         'slug' => 'chat_web', // Required ID/slug UNIQUE for the child menu
    //         'name' => _l("chat_web"), // The name if the item
    //         'href' => admin_url("contactcenter/chatweb"), // URL of the item
    //         'position' => 5, // The menu position
    //         'icon' => 'fa fa-money', // Font awesome icon
    //     ]);
    // }


    if (has_permission('contactcenter', '', 'contador')) {
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'contador',
            'name' => _l("contac_page_contador"),
            'href' => admin_url("contactcenter/contador"),
            'position' => 5,
            'icon' => 'fa fa-calculator',
        ]);
    }

    if (has_permission('contactcenter', '', 'meta')) {
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'meta',
            'name' => _l("contactcenter_meta"),
            'href' => admin_url("contactcenter/meta"),
            'position' => 5,
            'icon' => 'fa fa-chart-bar',
        ]);
    }

    if (has_permission('contactcenter', '', 'ads_analytics')) {
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'ads_analytics', // Required ID/slug UNIQUE for the child menu
            'name' => _l("contactcenter_ads_analytics"), // The name if the item
            'href' => admin_url("contactcenter/ads_analytics"), // URL of the item
            'position' => 6, // The menu position
            'icon' => 'fa fa-chart-line', // Font awesome icon
        ]);

        // Dashboard/Overview
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'ads_analytics_dashboard',
            'name' => _l("ads_analytics_dashboard"),
            'href' => admin_url("contactcenter/ads_analytics_dashboard"),
            'position' => 7,
            'icon' => 'fa fa-dashboard',
        ]);

        // Reports
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'ads_analytics_reports',
            'name' => _l("ads_analytics_reports"),
            'href' => admin_url("contactcenter/ads_analytics_reports"),
            'position' => 8,
            'icon' => 'fa fa-file-excel',
        ]);

        // Comparison
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'ads_analytics_comparison',
            'name' => _l("ads_analytics_comparison"),
            'href' => admin_url("contactcenter/ads_analytics_comparison"),
            'position' => 9,
            'icon' => 'fa fa-balance-scale',
        ]);

        // AI Insights
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'ads_analytics_ai_insights',
            'name' => _l("ads_analytics_ai_insights"),
            'href' => admin_url("contactcenter/ads_analytics_ai_insights"),
            'position' => 10,
            'icon' => 'fa fa-brain',
        ]);
    }

    if (has_permission('contactcenter', '', 'links')) {
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'links',
            'name' => _l("links_personalizados"),
            'href' => admin_url("contactcenter/linkscustom"),
            'position' => 5,
            'icon' => 'fa fa-link',
        ]);
    }

    if (has_permission('contactcenter', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('contactcenter', [
            'slug' => 'number_health', // Required ID/slug UNIQUE for the child menu
            'name' => _l("number_health_title"), // The name if the item
            'href' => admin_url("contactcenter/number_health"), // URL of the item
            'position' => 5, // The menu position
            'icon' => 'fa fa-heartbeat', // Font awesome icon
        ]);
    }

    //  if (has_permission('leads', '', 'create')) {
    //     $CI->app_menu->add_sidebar_children_item('contactcenter', [
    //         'name'     => _l('leadfinder_menu'),
    //         'href'     => admin_url('contactcenter/leadfinder'),
    //         'icon'     => 'fa fa-money',
    //         'position' => 15,
    //     ]);
    // }


    //menu do config
    $CI->app_tabs->add_settings_tab('contactcenter', [
        'name' => "Contact Center 2.0",
        'view' => 'contactcenter/settings/config',
        'position' => 90,
        'icon' => 'fa-regular fa-address-card',
    ]);

    $version = '1.7.1';

    $CI->app_css->add(MODULE_CONTACTCENTER . 'controle', base_url('modules/' . MODULE_CONTACTCENTER . '/assets/css/controle.css?v=' . $version));
    $CI->app_css->add(MODULE_CONTACTCENTER . 'chat', base_url('modules/' . MODULE_CONTACTCENTER . '/assets/css/chat.css?v=' . $version));
    $CI->app_css->add(MODULE_CONTACTCENTER . 'ads_analytics_dashboard', base_url('modules/' . MODULE_CONTACTCENTER . '/assets/css/ads_analytics_dashboard.css?v=' . $version));
    $CI->app_css->add(MODULE_CONTACTCENTER . 'axiom_overlay', base_url('modules/' . MODULE_CONTACTCENTER . '/assets/css/axiom_overlay.css?v=' . $version));

    // Add Chart.js and CountUp.js for dashboard (load in head to ensure availability)
    $CI->app_scripts->add(MODULE_CONTACTCENTER . 'chartjs_dashboard', 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js', 'head');
    $CI->app_scripts->add(MODULE_CONTACTCENTER . 'countup_dashboard', 'https://cdn.jsdelivr.net/npm/countup.js@2.6.2/dist/countUp.min.js', 'head');

    $CI->app_css->add(MODULE_CONTACTCENTER . 'drawflow', base_url('modules/' . MODULE_CONTACTCENTER . '/assets/css/drawflow/drawflow.css?v=' . $version));
    $CI->app_css->add(MODULE_CONTACTCENTER . 'beautiful', base_url('modules/' . MODULE_CONTACTCENTER . '/assets/css/drawflow/beautiful.css?v=' . $version));

    $CI->app_css->add(MODULE_CONTACTCENTER . 'emojionearea', "https://cdn.jsdelivr.net/npm/emojionearea@3.4.1/dist/emojionearea.min.css");

    $CI->app_scripts->add(MODULE_CONTACTCENTER . 'drawflow', base_url('modules/' . MODULE_CONTACTCENTER . '/assets/js/drawflow/drawflow.min.js?v=' . $version));

    $CI->app_scripts->add(MODULE_CONTACTCENTER . 'emojionearea', "https://cdn.jsdelivr.net/npm/emojionearea@3.4.1/dist/emojionearea.min.js");
    $CI->app_scripts->add(MODULE_CONTACTCENTER . 'contactcenter', base_url('modules/' . MODULE_CONTACTCENTER . '/assets/js/contactcenter.js?v=' . $version));
    $CI->app_scripts->add(MODULE_CONTACTCENTER . 'contactcenter-push', base_url('modules/' . MODULE_CONTACTCENTER . '/assets/js/pusher.js?v=' . $version));
    $CI->app_scripts->add(MODULE_CONTACTCENTER . 'socket', "https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.8.0/socket.io.js");
    $CI->app_scripts->add(MODULE_CONTACTCENTER . 'ads_analytics_dashboard', base_url('modules/' . MODULE_CONTACTCENTER . '/assets/js/ads_analytics_dashboard.js?v=' . $version));
    $CI->app_scripts->add(MODULE_CONTACTCENTER . 'device-status-widget', base_url('modules/' . MODULE_CONTACTCENTER . '/assets/js/device-status-widget.js?v=' . $version));
    $CI->app_scripts->add(MODULE_CONTACTCENTER . 'axiom_overlay', base_url('modules/' . MODULE_CONTACTCENTER . '/assets/js/axiom_overlay.js?v=' . $version));
    // wavoip.js não existe neste módulo, removido para evitar erro 404
    // $CI->app_scripts->add(MODULE_CONTACTCENTER . 'wavoip', base_url('modules/' . MODULE_CONTACTCENTER . '/assets/js/wavoip.js?v=' . $version));
}

hooks()->add_action('admin_init', 'contactcenter_permissions');

function contactcenter_permissions()
{
    $config = [];
    $config['capabilities'] = [
        'create' => _l('contac_criar'),
        'edit' => _l('contac_editar'),
        'delete' => _l('contac_excluir'),
        'engine' => _l('contac_conversation_engine'),
        'grupos' => _l('contac_group_all'),
        'import_leads' => _l('contac_import_leads'),
        'chat_viwer_all' => _l('contac_chat_viwer_all'),
        'chat_web' => _l('chat_web'),
        'contador' => 'Contador',
        'fluxo' => _l('drawflow_flow'),
        'meta' => _l('contactcenter_meta'),
        'ads_analytics' => _l('contactcenter_ads_analytics'),
        'links' => _l('links_personalizados'),
        'messenger' => _l('contac_messenger'),
        'view' => _l('permission_view'),
    ];
    register_staff_capabilities('contactcenter', $config, 'Contactcenter');
}


function is_valid_aviso()
{
    echo '<div class="alert alert-danger" style="position: absolute;z-index: 1;width: 90%;top: -30px;left: 30px;">';
    echo '<strong>Aviso!</strong> Você não possui um plano <strong>AXIOM</strong> ativo, entre em contato com o suporte para ativar o seu plano.';
    echo '</div>';
}

function is_valid_plan()
{
    $CI = &get_instance();
    $CI->load->model('contactcenter/contactcenter_model');
    return $CI->contactcenter_model->is_valid_plan();
}

function is_motor_conversation()
{
    $CI = &get_instance();
    $CI->load->model('contactcenter/contactcenter_model');
    return $CI->contactcenter_model->is_motor_active();
}

/**
 * Delete all email builder options on uninstall
 */
register_uninstall_hook("contactcenter", 'contactcenter_uninstall_hook');
function contactcenter_uninstall_hook()
{

    if (file_exists('application/views/admin/leads/my_lead.php')) {
        unlink('application/views/admin/leads/my_lead.php');
    }
}




function contactcenter_add_head_components()
{
    $pusher_options = hooks()->apply_filters('pusher_options', [['disableStats' => true]]);
    if (!isset($pusher_options['cluster']) && get_option('pusher_cluster') != '') {
        $pusher_options['cluster'] = get_option('pusher_cluster');
    }
    $serve_name_pusher = "{$_SERVER["SERVER_NAME"]}_noticacao_geral";
    $pusher_options =  json_encode($pusher_options);
    echo "<input type='hidden' name='staffid' value='" . get_staff_user_id() . "' />";
    echo "<input type='hidden' name='pusher_app_key' value='" . get_option('pusher_app_key') . "' />";
    echo "<input type='hidden' name='pusher_options' value='$pusher_options' />";
    echo "<input type='hidden' name='serve_name_pusher' value='$serve_name_pusher' />";
}

hooks()->add_action('before_lead_deleted', 'contactcenter_lead_after_delete');

/**
 * Add device status widget to header navbar
 */
function contactcenter_add_device_status_widget()
{
    $CI = &get_instance();
    $CI->load->helper('contactcenter/contactcenter');

    if (function_exists('get_disconnected_devices')) {
        $disconnected_devices = get_disconnected_devices(null, is_admin());
        $my_disconnected = get_disconnected_devices(get_staff_user_id(), false);
        $total_disconnected = is_array($disconnected_devices) ? count($disconnected_devices) : 0;
        $my_disconnected_count = is_array($my_disconnected) ? count($my_disconnected) : 0;

        // Pass translations to JavaScript
        $translations = [
            'device_status' => _l('device_status'),
            'all_devices_connected' => _l('all_devices_connected'),
            'device_disconnected' => _l('device_disconnected'),
            'reconnect' => _l('reconnect'),
            'reconnecting' => _l('reconnecting'),
            'device_reconnected' => _l('device_reconnected'),
            'device_reconnect_failed' => _l('device_reconnect_failed'),
            'device_id_not_found' => _l('device_id_not_found'),
            'device' => _l('device'),
            'devices_disconnected' => _l('devices_disconnected'),
            'more_devices' => _l('more_devices'),
            'all_disconnected_devices' => _l('all_disconnected_devices'),
        ];

        // Store data in JavaScript variable for widget to use
        echo '<script>
        window.contactcenterDeviceStatus = {
            totalDisconnected: ' . $total_disconnected . ',
            myDisconnected: ' . $my_disconnected_count . ',
            devices: ' . json_encode($disconnected_devices, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) . ',
            myDevices: ' . json_encode($my_disconnected, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) . '
        };
        window.contactcenterDeviceTranslations = ' . json_encode($translations, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) . ';
        </script>';
    }
}

/**
 * Essa função deleta todos os relacionamentos do lead
 *
 * @param [type] $id
 * @return void
 */
function contactcenter_lead_after_delete($id)
{
    $CI = &get_instance();
    $CI->load->model('contactcenter/contactcenter_model');
    $CI->contactcenter_model->lead_after_delete($id);
}




hooks()->add_action('lead_created', "atualiza_phonenumber_lead");
/**
 * atualiza_phonenumber_lead
 * atualiza o telefone do lead
 * @param [type] $id
 * @return void
 */
function atualiza_phonenumber_lead($data)
{
    $lead_id = $data['lead_id'] ?? null;

    if (!$lead_id) {
        return;
    }

    $CI = &get_instance();
    $CI->load->model('contactcenter/contactcenter_model');
    $CI->contactcenter_model->atualiza_phonenumber_lead($lead_id);
}

/**
 * Get lead tabs as conversa na modal do lead
 */
hooks()->add_action('after_lead_lead_tabs',  'contactcenter_get_lead_tab');
hooks()->add_action('after_lead_tabs_content',  'contactcenter_get_lead_content');
hooks()->add_action('after_lead_lead_tabs',  'contactcenter_get_ai_interactions_tab');
hooks()->add_action('after_lead_tabs_content',  'contactcenter_get_ai_interactions_content');

// Convert contactcenter_group_chat_auto_add_staff array to comma-separated string before saving
// This hook handles the case when the value comes through as an array in the loop
hooks()->add_filter('before_single_setting_updated_in_loop', 'contactcenter_convert_staff_array_to_string');
function contactcenter_convert_staff_array_to_string($hook_data)
{
    if ($hook_data['name'] == 'contactcenter_group_chat_auto_add_staff') {
        log_activity("contactcenter_convert_staff_array_to_string - Name: {$hook_data['name']}, Value type: " . gettype($hook_data['value']) . ", Value: " . (is_array($hook_data['value']) ? json_encode($hook_data['value']) : $hook_data['value']));

        if (is_array($hook_data['value'])) {
            // Filter out empty values and convert to comma-separated string
            $filtered = array_filter($hook_data['value'], function ($val) {
                return !empty(trim($val));
            });
            $hook_data['value'] = !empty($filtered) ? implode(',', $filtered) : '';
            log_activity("contactcenter_convert_staff_array_to_string - Converted to: " . ($hook_data['value'] ?: 'empty'));
        } elseif (empty($hook_data['value']) && $hook_data['value'] !== '0') {
            // If value is empty (not set or empty string), set to empty string
            // Note: '0' is a valid value, so we check for !== '0'
            $hook_data['value'] = '';
        }

        log_activity("contactcenter_convert_staff_array_to_string - Final value: " . ($hook_data['value'] ?: 'empty'));
    } elseif ($hook_data['name'] == 'contactcenter_group_chat_auto_add_staff_sent') {
        // Skip saving the sent flag - it's just a marker
        $hook_data['name'] = ''; // Empty name will cause it to be skipped
        log_activity("contactcenter_convert_staff_array_to_string - Skipping sent flag");
    }
    return $hook_data;
}

// Handle contactcenter_group_chat_auto_add_staff when it's not in settings array (empty selection)
hooks()->add_filter('before_settings_updated', 'contactcenter_handle_staff_array_setting');
function contactcenter_handle_staff_array_setting($data)
{
    $CI = &get_instance();

    // Initialize settings array if it doesn't exist
    if (!isset($data['settings'])) {
        $data['settings'] = [];
    }

    // Get raw POST data to check for the field
    $raw_post = $CI->input->post(null, false);

    log_activity("contactcenter_handle_staff_array_setting START - Data keys: " . (isset($data['settings']) ? implode(', ', array_keys($data['settings'])) : 'no settings'));

    // Remove the sent flag from settings (it's just a marker, shouldn't be saved)
    if (isset($data['settings']['contactcenter_group_chat_auto_add_staff_sent'])) {
        unset($data['settings']['contactcenter_group_chat_auto_add_staff_sent']);
    }

    // Always unset the existing value first (whether array or string) to ensure clean state
    if (isset($data['settings']['contactcenter_group_chat_auto_add_staff'])) {
        $old_type = gettype($data['settings']['contactcenter_group_chat_auto_add_staff']);
        unset($data['settings']['contactcenter_group_chat_auto_add_staff']);
        log_activity("contactcenter_handle_staff_array_setting - Removed existing value (type: {$old_type}) from settings");
    }

    // Check if contactcenter_group_chat_auto_add_staff is in POST data
    if (isset($raw_post['settings']['contactcenter_group_chat_auto_add_staff'])) {
        $staff_ids = $raw_post['settings']['contactcenter_group_chat_auto_add_staff'];

        log_activity("contactcenter_handle_staff_array_setting - Found in POST, type: " . gettype($staff_ids) . ", Value: " . (is_array($staff_ids) ? json_encode($staff_ids) : $staff_ids));

        if (is_array($staff_ids)) {
            // Filter out empty values and convert to comma-separated string
            $filtered = array_filter($staff_ids, function ($val) {
                return !empty(trim($val));
            });
            $value = !empty($filtered) ? implode(',', $filtered) : '';
        } else {
            $value = !empty($staff_ids) ? trim($staff_ids) : '';
        }

        // Force it to be a string and set it in the settings array
        $data['settings']['contactcenter_group_chat_auto_add_staff'] = (string)$value;

        log_activity("contactcenter_handle_staff_array_setting - Set value (string): " . ($value ?: 'empty') . ", Type: " . gettype($data['settings']['contactcenter_group_chat_auto_add_staff']));
    } elseif (isset($raw_post['settings']['contactcenter_group_chat_auto_add_staff_sent'])) {
        // If the sent flag is present but the field is not, it means nothing was selected
        $data['settings']['contactcenter_group_chat_auto_add_staff'] = '';
        log_activity("contactcenter_handle_staff_array_setting - Set to empty (sent flag present but no values)");
    } else {
        // If field is not in POST at all, set to empty string
        $data['settings']['contactcenter_group_chat_auto_add_staff'] = '';
        log_activity("contactcenter_handle_staff_array_setting - Set to empty (not in POST)");
    }

    // Final verification: ensure the value is a string, not an array
    if (isset($data['settings']['contactcenter_group_chat_auto_add_staff']) && is_array($data['settings']['contactcenter_group_chat_auto_add_staff'])) {
        log_activity("contactcenter_handle_staff_array_setting WARNING - Value is still an array! Converting...");
        $data['settings']['contactcenter_group_chat_auto_add_staff'] = implode(',', array_filter($data['settings']['contactcenter_group_chat_auto_add_staff']));
    }

    // Verify it's actually in the array before returning
    $final_value = isset($data['settings']['contactcenter_group_chat_auto_add_staff']) ? $data['settings']['contactcenter_group_chat_auto_add_staff'] : 'NOT SET';
    $final_type = isset($data['settings']['contactcenter_group_chat_auto_add_staff']) ? gettype($data['settings']['contactcenter_group_chat_auto_add_staff']) : 'N/A';

    // Force set it one more time to be absolutely sure
    if (isset($raw_post['settings']['contactcenter_group_chat_auto_add_staff'])) {
        $staff_ids = $raw_post['settings']['contactcenter_group_chat_auto_add_staff'];
        if (is_array($staff_ids)) {
            $filtered = array_filter($staff_ids, function ($val) {
                return !empty(trim($val));
            });
            $value = !empty($filtered) ? implode(',', $filtered) : '';
        } else {
            $value = !empty($staff_ids) ? trim($staff_ids) : '';
        }
        $data['settings']['contactcenter_group_chat_auto_add_staff'] = (string)$value;
        log_activity("contactcenter_handle_staff_array_setting - Force set value again: " . ($value ?: 'empty'));
    }

    log_activity("contactcenter_handle_staff_array_setting END - Final value in settings: " . (isset($data['settings']['contactcenter_group_chat_auto_add_staff']) ? $data['settings']['contactcenter_group_chat_auto_add_staff'] : 'NOT SET') . ", Type: " . (isset($data['settings']['contactcenter_group_chat_auto_add_staff']) ? gettype($data['settings']['contactcenter_group_chat_auto_add_staff']) : 'N/A') . ", Array keys count: " . count($data['settings']));

    // CRITICAL: Ensure the value is definitely set before returning
    // Sometimes filters don't work as expected, so we'll also save directly as a backup
    if (isset($raw_post['settings']['contactcenter_group_chat_auto_add_staff'])) {
        $staff_ids = $raw_post['settings']['contactcenter_group_chat_auto_add_staff'];
        if (is_array($staff_ids)) {
            $filtered = array_filter($staff_ids, function ($val) {
                return !empty(trim($val));
            });
            $final_value = !empty($filtered) ? implode(',', $filtered) : '';
        } else {
            $final_value = !empty($staff_ids) ? trim($staff_ids) : '';
        }
        $data['settings']['contactcenter_group_chat_auto_add_staff'] = (string)$final_value;

        // Also save directly to database as backup in case the filter doesn't work
        update_option('contactcenter_group_chat_auto_add_staff', (string)$final_value);
        log_activity("contactcenter_handle_staff_array_setting - DIRECT SAVE to database: " . ($final_value ?: 'empty'));
    }

    // Handle contactcenter_group_chat_default_message - ensure it's preserved from raw POST
    if (isset($raw_post['settings']['contactcenter_group_chat_default_message'])) {
        $message_value = $raw_post['settings']['contactcenter_group_chat_default_message'];
        $data['settings']['contactcenter_group_chat_default_message'] = $message_value;
        log_activity("contactcenter_handle_staff_array_setting - Preserved contactcenter_group_chat_default_message from raw POST, length: " . strlen($message_value));

        // Also save directly to database as backup in case the filter doesn't work
        update_option('contactcenter_group_chat_default_message', $message_value);
        log_activity("contactcenter_handle_staff_array_setting - DIRECT SAVE contactcenter_group_chat_default_message to database, length: " . strlen($message_value));
    } elseif (isset($data['settings']['contactcenter_group_chat_default_message'])) {
        // If it's already in the data array, ensure it's preserved
        log_activity("contactcenter_handle_staff_array_setting - contactcenter_group_chat_default_message already in settings array, length: " . strlen($data['settings']['contactcenter_group_chat_default_message']));
    } else {
        log_activity("contactcenter_handle_staff_array_setting - contactcenter_group_chat_default_message NOT in POST or settings array");
    }

    // Handle contactcenter_gemini_api_key - ensure it's preserved from raw POST
    // Initialize option if it doesn't exist
    if (!option_exists('contactcenter_gemini_api_key')) {
        add_option('contactcenter_gemini_api_key', '');
    }

    if (isset($raw_post['settings']['contactcenter_gemini_api_key'])) {
        $gemini_key = $raw_post['settings']['contactcenter_gemini_api_key'];
        // Update regardless of whether it's empty or not (user might want to clear it)
        $data['settings']['contactcenter_gemini_api_key'] = trim($gemini_key);
        log_activity("contactcenter_handle_staff_array_setting - Preserved contactcenter_gemini_api_key from raw POST");

        // Also save directly to database as backup in case the filter doesn't work
        update_option('contactcenter_gemini_api_key', trim($gemini_key));
        log_activity("contactcenter_handle_staff_array_setting - DIRECT SAVE contactcenter_gemini_api_key to database");
    } elseif (isset($data['settings']['contactcenter_gemini_api_key'])) {
        // If it's already in the data array, ensure it's preserved
        log_activity("contactcenter_handle_staff_array_setting - contactcenter_gemini_api_key already in settings array");
    } else {
        // If not in POST and not in data, preserve existing value (password field wasn't changed)
        $existing_value = get_option('contactcenter_gemini_api_key');
        if ($existing_value !== false) {
            $data['settings']['contactcenter_gemini_api_key'] = $existing_value;
            log_activity("contactcenter_handle_staff_array_setting - Preserved existing contactcenter_gemini_api_key value");
        }
    }

    // Handle contactcenter_google_places_api_key - ensure it's preserved from raw POST
    // Initialize option if it doesn't exist
    if (!option_exists('contactcenter_google_places_api_key')) {
        add_option('contactcenter_google_places_api_key', '');
    }

    if (isset($raw_post['settings']['contactcenter_google_places_api_key'])) {
        $places_key = $raw_post['settings']['contactcenter_google_places_api_key'];
        // Update regardless of whether it's empty or not (user might want to clear it)
        $data['settings']['contactcenter_google_places_api_key'] = trim($places_key);
        log_activity("contactcenter_handle_staff_array_setting - Preserved contactcenter_google_places_api_key from raw POST");

        // Also save directly to database as backup in case the filter doesn't work
        update_option('contactcenter_google_places_api_key', trim($places_key));
        log_activity("contactcenter_handle_staff_array_setting - DIRECT SAVE contactcenter_google_places_api_key to database");
    } elseif (isset($data['settings']['contactcenter_google_places_api_key'])) {
        // If it's already in the data array, ensure it's preserved
        log_activity("contactcenter_handle_staff_array_setting - contactcenter_google_places_api_key already in settings array");
    } else {
        // If not in POST and not in data, preserve existing value (password field wasn't changed)
        $existing_value = get_option('contactcenter_google_places_api_key');
        if ($existing_value !== false) {
            $data['settings']['contactcenter_google_places_api_key'] = $existing_value;
            log_activity("contactcenter_handle_staff_array_setting - Preserved existing contactcenter_google_places_api_key value");
        }
    }

    // Handle field mappings - convert array to JSON
    // Initialize option if it doesn't exist
    if (!option_exists('contactcenter_ai_lead_field_mappings')) {
        add_option('contactcenter_ai_lead_field_mappings', '{}');
    }

    if (isset($raw_post['settings']['contactcenter_ai_lead_field_mappings'])) {
        $mappings = $raw_post['settings']['contactcenter_ai_lead_field_mappings'];

        // Filter out empty values (unmapped fields)
        $filtered_mappings = [];
        foreach ($mappings as $ai_field => $target_field) {
            if (!empty($target_field)) {
                $filtered_mappings[$ai_field] = $target_field;
            }
        }

        // Convert to JSON
        $mappings_json = json_encode($filtered_mappings);
        $data['settings']['contactcenter_ai_lead_field_mappings'] = $mappings_json;
        log_activity("contactcenter_handle_staff_array_setting - Preserved contactcenter_ai_lead_field_mappings from raw POST: " . $mappings_json);

        // Also save directly to database as backup in case the filter doesn't work
        update_option('contactcenter_ai_lead_field_mappings', $mappings_json);
        log_activity("contactcenter_handle_staff_array_setting - DIRECT SAVE contactcenter_ai_lead_field_mappings to database");
    } elseif (isset($data['settings']['contactcenter_ai_lead_field_mappings'])) {
        // If it's already in the data array, ensure it's JSON
        if (is_array($data['settings']['contactcenter_ai_lead_field_mappings'])) {
            $data['settings']['contactcenter_ai_lead_field_mappings'] = json_encode($data['settings']['contactcenter_ai_lead_field_mappings']);
        }
        log_activity("contactcenter_handle_staff_array_setting - contactcenter_ai_lead_field_mappings already in settings array");
    } else {
        // If not in POST and not in data, preserve existing value
        $existing_value = get_option('contactcenter_ai_lead_field_mappings');
        if ($existing_value !== false && !empty($existing_value)) {
            $data['settings']['contactcenter_ai_lead_field_mappings'] = $existing_value;
            log_activity("contactcenter_handle_staff_array_setting - Preserved existing contactcenter_ai_lead_field_mappings value");
        } else {
            // Set empty JSON object if no existing value
            $data['settings']['contactcenter_ai_lead_field_mappings'] = '{}';
            log_activity("contactcenter_handle_staff_array_setting - Set empty JSON for contactcenter_ai_lead_field_mappings");
        }
    }

    // Handle openai_speed_send - ensure it's preserved from raw POST
    if (isset($raw_post['settings']['openai_speed_send'])) {
        $speed_send_value = $raw_post['settings']['openai_speed_send'];
        $data['settings']['openai_speed_send'] = $speed_send_value;
        log_activity("contactcenter_handle_staff_array_setting - Preserved openai_speed_send from raw POST, value: {$speed_send_value}");

        // Also save directly to database as backup in case the filter doesn't work
        update_option('openai_speed_send', $speed_send_value);
        log_activity("contactcenter_handle_staff_array_setting - DIRECT SAVE openai_speed_send to database, value: {$speed_send_value}");
    } elseif (!isset($data['settings']['openai_speed_send'])) {
        // If not in POST and not in data, set to 0 (default) - checkbox/radio unchecked
        // But only if we're actually processing settings (to avoid overwriting existing value unnecessarily)
        if (isset($raw_post['settings'])) {
            $data['settings']['openai_speed_send'] = '0';
            log_activity("contactcenter_handle_staff_array_setting - openai_speed_send not in POST, setting to 0");
            update_option('openai_speed_send', '0');
        }
    }

    // CRITICAL: Ensure all radio button settings are preserved from raw POST
    // These settings must be in the settings array for Settings_model to process them
    $yes_no_options = [
        'contac_active_confirm_agendamento',
        'contac_active_link_call',
        'active_audio_contactcenter',
        'active_audio_contactcenter_elevenlabs',
        'contac_settings_sincronizacao_whatsapp_active',
        'contac_settings_sincronizacao_whatsapp_leads',
        'historico_mgs_ai_active',
        'active_contador_contactcenter'
    ];

    foreach ($yes_no_options as $option) {
        // Check raw POST first (to catch "0" values that might be filtered)
        if (isset($raw_post['settings'][$option])) {
            $value = (int)$raw_post['settings'][$option];
            if ($value != 0 && $value != 1) {
                $value = 0; // Default to 0 if invalid
            }
            $data['settings'][$option] = $value;
            // Also save directly to database as backup
            update_option($option, $value);
        } elseif (isset($data['settings'][$option])) {
            // If already in data array, ensure it's an integer
            $data['settings'][$option] = (int)$data['settings'][$option];
        } else {
            // If not in POST and not in data, preserve existing value
            $existing_value = get_option($option);
            if ($existing_value !== false) {
                $data['settings'][$option] = (int)$existing_value;
            }
        }
    }

    // Handle Lead Registration Settings and other select/text input settings
    $lead_settings = [
        'leads_cadastro_contactcenter',
        'leads_source_contactcenter',
        'leads_cadastro_call_contactcenter',
        'time_contactcenter',
        'quant_time_contactcenter',
        'agendaMinutesToAdd',
        'minutes_schedule',
        'saturdayHours',
        'contactcenter_notify_whatsapp_agendamento',
        'contac_title_agendamento',
        'leads_status_contador_contactcenter',
        'leads_source_contador_contactcenter',
        'staff_contador_contactcenter',
        'default_staff_ticket_ia',
        'default_status_ticket_ia',
        'whatsapp_msg_call',
        'contactcenter_group_chat_name_format',
        'tokenopenai_contactcenter',
        'token_elevenlabs_contactcenter'
    ];

    foreach ($lead_settings as $setting) {
        if (isset($raw_post['settings'][$setting])) {
            $value = $raw_post['settings'][$setting];
            // Allow empty values for these settings (user might want to clear them)
            $data['settings'][$setting] = $value;
            log_activity("contactcenter_handle_staff_array_setting - Preserved {$setting} from raw POST, value: " . ($value ?: 'empty'));

            // Also save directly to database as backup
            update_option($setting, $value);
            log_activity("contactcenter_handle_staff_array_setting - DIRECT SAVE {$setting} to database, value: " . ($value ?: 'empty'));
        } elseif (isset($data['settings'][$setting])) {
            // If already in data array, preserve it
            log_activity("contactcenter_handle_staff_array_setting - {$setting} already in settings array");
        } else {
            // If not in POST and not in data, preserve existing value
            $existing_value = get_option($setting);
            if ($existing_value !== false) {
                $data['settings'][$setting] = $existing_value;
                log_activity("contactcenter_handle_staff_array_setting - Preserved existing {$setting} value: " . ($existing_value ?: 'empty'));
            }
        }
    }

    // Ensure settings array structure is preserved before returning
    if (!isset($data['settings']) || !is_array($data['settings'])) {
        $data['settings'] = [];
    }

    return $data;
}

// Handle group chat default picture upload
hooks()->add_action('before_settings_updated', 'contactcenter_handle_group_picture_upload');
function contactcenter_handle_group_picture_upload($data)
{
    $CI = &get_instance();

    // Check if a new picture was uploaded
    if (
        isset($_FILES['contactcenter_group_chat_default_picture']) &&
        $_FILES['contactcenter_group_chat_default_picture']['error'] == UPLOAD_ERR_OK
    ) {

        $file = $_FILES['contactcenter_group_chat_default_picture'];

        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $file_type = mime_content_type($file['tmp_name']);

        if (!in_array($file_type, $allowed_types)) {
            set_alert('danger', _l('contac_settings_group_chat_picture_invalid_type'));
            return;
        }

        // Validate file size (max 800KB as per WhatsApp requirements)
        $max_size = 800 * 1024; // 800KB
        if ($file['size'] > $max_size) {
            set_alert('danger', _l('contac_settings_group_chat_picture_too_large'));
            return;
        }

        // Create upload directory if it doesn't exist
        $upload_path = FCPATH . 'uploads/contactcenter/group_pictures/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'group_picture_' . time() . '_' . uniqid() . '.' . $file_extension;
        $file_path = $upload_path . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Resize image if needed (WhatsApp recommends 640x640 max, square format)
            $image_info = getimagesize($file_path);
            if ($image_info) {
                $max_dimension = 640;
                $width = $image_info[0];
                $height = $image_info[1];

                // If image is larger than 640x640, resize it
                if ($width > $max_dimension || $height > $max_dimension) {
                    $CI->load->library('image_lib');

                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $file_path;
                    $config['maintain_ratio'] = true;
                    $config['width'] = $max_dimension;
                    $config['height'] = $max_dimension;
                    $config['quality'] = 90;

                    $CI->image_lib->initialize($config);
                    if (!$CI->image_lib->resize()) {
                        log_activity("Error resizing group picture: " . $CI->image_lib->display_errors());
                    }
                    $CI->image_lib->clear();
                }
            }

            // Save relative path to option
            $relative_path = 'uploads/contactcenter/group_pictures/' . $filename;
            update_option('contactcenter_group_chat_default_picture', $relative_path);

            // Delete old picture if exists and is different
            $old_picture = get_option('contactcenter_group_chat_default_picture');
            if (!empty($old_picture) && $old_picture != $relative_path && file_exists(FCPATH . $old_picture)) {
                @unlink(FCPATH . $old_picture);
            }

            log_activity("Group chat default picture uploaded: {$relative_path}");
        } else {
            set_alert('danger', _l('contac_settings_group_chat_picture_upload_failed'));
        }
    } elseif (isset($_POST['contactcenter_group_chat_default_picture_current'])) {
        // Keep current picture if no new one was uploaded
        update_option('contactcenter_group_chat_default_picture', $_POST['contactcenter_group_chat_default_picture_current']);
    }
}

function contactcenter_get_lead_tab($contact)
{
    $CI = &get_instance();
    return $CI->load->view('contactcenter/leads/lead_tab');
}
function contactcenter_get_lead_content($contact)
{
    $CI = &get_instance();
    return $CI->load->view('contactcenter/leads/lead_content');
}

function contactcenter_get_ai_interactions_tab($contact)
{
    $CI = &get_instance();

    // Always show tab if contactcenter module is active (just like WhatsApp tab)
    // Get interaction count for badge if lead ID is available
    $ai_interactions_count = 0;
    if (isset($contact) && isset($contact->id) && $contact->id) {
        $ai_interactions_table_exists = $CI->db->table_exists(db_prefix() . 'contactcenter_ai_interactions');

        if ($ai_interactions_table_exists) {
            $CI->db->where('lead_id', $contact->id);
            $ai_interactions_count = $CI->db->count_all_results(db_prefix() . 'contactcenter_ai_interactions');
        }
    }

    // Load the view and pass data (same pattern as lead_tab)
    $CI->load->view('contactcenter/leads/ai_interactions_tab', [
        'lead' => $contact ?? null,
        'ai_interactions_count' => $ai_interactions_count
    ]);
}

function contactcenter_get_ai_interactions_content($contact)
{
    $CI = &get_instance();

    // Load AI interactions if lead ID is available (same pattern as lead_content)
    $ai_interactions = [];
    $ai_interactions_table_exists = false;

    if (isset($contact) && isset($contact->id) && $contact->id) {
        $ai_interactions_table_exists = $CI->db->table_exists(db_prefix() . 'contactcenter_ai_interactions');

        if ($ai_interactions_table_exists) {
            try {
                $CI->load->model('contactcenter/contactcenter_model');
                $ai_interactions = $CI->contactcenter_model->get_ai_interactions($contact->id, 100);
            } catch (Exception $e) {
                log_message('error', 'Failed to load AI interactions for lead ' . $contact->id . ': ' . $e->getMessage());
            }
        }
    }

    // Load the view (same pattern as lead_content - no return, view echoes directly)
    $CI->load->view('contactcenter/leads/ai_interactions_content', [
        'lead' => $contact ?? null,
        'ai_interactions' => $ai_interactions,
        'ai_interactions_table_exists' => $ai_interactions_table_exists
    ]);
}



/** Não apagar  */
// hooks()->add_filter('leads_table_columns', 'contactcenter_leads_table_columns');
// hooks()->add_filter('leads_table_row_data', 'contactcenter_leads_table_columns_params', 10, 2);

// function contactcenter_leads_table_columns($_table_data)
// {

//     // Adicionar nova coluna "ChatWhats"   
//     $_table_data[] = [
//         'name'     => "chatwhats",
//         'th_attrs' => ['class' => 'toggleable', 'id' => 'th-chatwhats'],
//     ];

//     return $_table_data;
// }

// function contactcenter_leads_table_columns_params($row, $aRow)
// {  
   
//     if (isset($aRow['name'])) {
//         // Adicionar o valor no formato desejado ao $row
//         $row[] = '<span><i class="fa-brands fa-whatsapp"></i> ' . htmlspecialchars($aRow['name']) . '</span>';
//     }
 
//     return $row;
// }

if (!function_exists('contactcenter_format_hours_friendly')) {
    function contactcenter_format_hours_friendly($hours)
    {
        $hours = (float) $hours;
        if ($hours <= 0) return '0';
        if ($hours < 1) {
            $mins = round($hours * 60);
            return $mins . ' min';
        }
        if ($hours < 24) {
            return rtrim(rtrim(number_format($hours, 1), '0'), '.') . 'h';
        }
        if ($hours < 168) {
            $days = $hours / 24;
            return rtrim(rtrim(number_format($days, 1), '0'), '.') . 'd';
        }
        if ($hours < 720) {
            $weeks = $hours / 168;
            return rtrim(rtrim(number_format($weeks, 1), '0'), '.') . 'w';
        }
        $months = $hours / 720;
        return rtrim(rtrim(number_format($months, 1), '0'), '.') . 'm';
    }
}

if (!function_exists('contactcenter_call_gemini_api')) {
    function contactcenter_call_gemini_api($api_key, $prompt, $expect_json = true)
    {
        try {
            $data = [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature'    => 0.7,
                    'maxOutputTokens'=> $expect_json ? 8192 : 8192,
                ],
            ];
            if ($expect_json) {
                $data['generationConfig']['responseMimeType'] = 'application/json';
            }

            log_activity("AXIOM Gemini API (standalone): Request - expect_json=" . ($expect_json ? 'true' : 'false') . ", prompt_length=" . strlen($prompt));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $api_key);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_TIMEOUT, 180);

            $response  = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code !== 200) {
                log_activity("AXIOM Gemini API (standalone): HTTP Error {$http_code} - " . substr($response, 0, 300));
                return ['error' => 'API request failed (HTTP ' . $http_code . ')'];
            }

            $result = json_decode($response, true);
            if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                return ['error' => 'Unexpected response structure'];
            }

            $text = $result['candidates'][0]['content']['parts'][0]['text'];

            if (!$expect_json) {
                return $text;
            }

            $text = preg_replace('/```json\s*/i', '', $text);
            $text = preg_replace('/```\s*/', '', $text);
            $text = trim($text);

            $parsed = json_decode($text, true);
            if ($parsed !== null && json_last_error() === JSON_ERROR_NONE) {
                return $parsed;
            }

            $first = strpos($text, '{');
            $last  = strrpos($text, '}');
            if ($first !== false && $last !== false && $last > $first) {
                $parsed = json_decode(substr($text, $first, $last - $first + 1), true);
                if ($parsed !== null && json_last_error() === JSON_ERROR_NONE) {
                    return $parsed;
                }
            }

            log_activity("AXIOM Gemini API (standalone): JSON parse failed - " . json_last_error_msg());
            return ['error' => 'Failed to parse JSON response', 'raw_text_preview' => substr($text, 0, 500)];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}

if (!function_exists('contactcenter_script_autoupdate_cron')) {
    function contactcenter_script_autoupdate_cron($manually = false)
    {
        $CI = &get_instance();
        $CI->load->model('contactcenter/contactcenter_model');

        if (!$CI->db->table_exists(db_prefix() . 'contactcenter_assistants_ai')) {
            return;
        }

        $assistants = $CI->db->get(db_prefix() . 'contactcenter_assistants_ai')->result();
        $today = date('Y-m-d');

        foreach ($assistants as $assistant) {
            $settings_json = get_option('contactcenter_script_autoupdate_' . $assistant->id);
            if (empty($settings_json)) {
                continue;
            }

            $settings = json_decode($settings_json, true);
            if (empty($settings['enabled']) || $settings['enabled'] != '1') {
                continue;
            }

            $frequency = isset($settings['frequency_days']) ? (int) $settings['frequency_days'] : 7;
            $last_run  = isset($settings['last_run_date']) ? $settings['last_run_date'] : '';

            if (!empty($last_run)) {
                $next_run = date('Y-m-d', strtotime($last_run . ' + ' . $frequency . ' days'));
                if ($today < $next_run) {
                    continue;
                }
            }

            $CI->contactcenter_model->run_script_autoupdate($assistant->id, $settings);
        }
    }
}

if (!function_exists('contactcenter_auto_followup_cron')) {
    function contactcenter_auto_followup_cron()
    {
        $CI = &get_instance();
        $CI->load->model('contactcenter/contactcenter_model');
        $CI->contactcenter_model->cron_auto_followup_generate();
        $CI->contactcenter_model->cron_auto_followup_send();
    }
}

if (!function_exists('contactcenter_invoice_followup_cron')) {
    function contactcenter_invoice_followup_cron()
    {
        $CI = &get_instance();
        $CI->load->model('contactcenter/contactcenter_model');
        $CI->contactcenter_model->cron_invoice_followup_generate();
        $CI->contactcenter_model->cron_invoice_followup_send();
    }
}
