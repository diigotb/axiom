<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
  Module Name: AxiomChannel
  Description: Módulo de comunicação omnichannel — WhatsApp, chat ao vivo e automações
  Version: 1.0.0
  Author: RT Marketing Estratégico
  Author URI: https://rtmarketing.com.br
  Requires at least: 2.3.2
 */

$CI = &get_instance();

if (!defined('AXIOMCHANNEL_MODULE')) {
    define('AXIOMCHANNEL_MODULE', basename(__DIR__));
}

// -------------------------------------------------------
// Hooks principais
// -------------------------------------------------------
hooks()->add_action('admin_init', 'axiomchannel_init_menu');
hooks()->add_action('app_admin_head', 'axiomchannel_add_assets');
hooks()->add_action('admin_head', 'axiomchannel_add_assets');

// Cron
register_cron_task('axiomchannel_process_queue');

// Hooks de ativação/desativação
register_activation_hook(AXIOMCHANNEL_MODULE, 'axiomchannel_activation_hook');
register_deactivation_hook(AXIOMCHANNEL_MODULE, 'axiomchannel_deactivation_hook');

// -------------------------------------------------------
// Menu lateral
// -------------------------------------------------------
function axiomchannel_init_menu()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('axiomchannel', [
        'name'     => 'AxiomChannel',
        'href'     => admin_url('axiomchannel'),
        'icon'     => 'fa fa-comments',
        'position' => 10,
    ]);

    $CI->app_menu->add_sidebar_children_item('axiomchannel', [
        'slug'     => 'axiomchannel-inbox',
        'name'     => 'Todas as Conversas',
        'href'     => admin_url('axiomchannel/inbox'),
        'icon'     => 'fa fa-inbox',
        'position' => 1,
    ]);

    $CI->app_menu->add_sidebar_children_item('axiomchannel', [
        'slug'     => 'axiomchannel-pipeline',
        'name'     => 'CRM Pipeline',
        'href'     => admin_url('axiomchannel/pipeline'),
        'icon'     => 'fa fa-columns',
        'position' => 3,
    ]);

    $CI->app_menu->add_sidebar_children_item('axiomchannel', [
        'slug'     => 'axiomchannel-devices',
        'name'     => 'Dispositivos',
        'href'     => admin_url('axiomchannel/devices'),
        'icon'     => 'fa fa-mobile',
        'position' => 2,
    ]);

    $CI->app_menu->add_sidebar_children_item('axiomchannel', [
        'slug'     => 'axiomchannel-assistant',
        'name'     => 'Assistente IA',
        'href'     => admin_url('axiomchannel/assistant'),
        'icon'     => 'fa fa-robot',
        'position' => 4,
    ]);

    $CI->app_menu->add_sidebar_children_item('axiomchannel', [
        'slug'     => 'axiomchannel-appointments',
        'name'     => 'Agendamentos',
        'href'     => admin_url('axiomchannel/appointments'),
        'icon'     => 'fa fa-calendar',
        'position' => 5,
    ]);

    $CI->app_menu->add_sidebar_children_item('axiomchannel', [
        'slug'     => 'axiomchannel-contracts',
        'name'     => 'Contratos',
        'href'     => admin_url('axiomchannel/contracts'),
        'icon'     => 'fa fa-file-text',
        'position' => 6,
    ]);
}

// -------------------------------------------------------
// Assets (CSS)
// -------------------------------------------------------
function axiomchannel_add_assets()
{
    static $loaded = false;
    if ($loaded) return;
    $loaded = true;
    $uri = module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css');
    echo '<link rel="stylesheet" href="' . $uri . '?v=' . time() . '">';
}

// -------------------------------------------------------
// Ativação — cria tabelas
// -------------------------------------------------------
function axiomchannel_activation_hook()
{
    $CI = &get_instance();
    require_once(module_dir_path(AXIOMCHANNEL_MODULE, 'install.php'));
}

function axiomchannel_deactivation_hook()
{
    // Mantém os dados ao desativar
}
