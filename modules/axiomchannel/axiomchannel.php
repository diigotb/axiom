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
hooks()->add_action('admin_init',      'axiomchannel_init_menu');
hooks()->add_action('admin_init',      'axiomchannel_intercept_perfex_dashboard');
hooks()->add_action('app_admin_head',  'axiomchannel_add_assets');
hooks()->add_action('admin_head',      'axiomchannel_add_assets');
hooks()->add_action('app_admin_head',  'axiomchannel_hide_perfex_ui');
hooks()->add_action('app_admin_footer','axiomchannel_spa_redirect');

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
        'href'     => admin_url('axiomchannel/spa'),
        'icon'     => 'fa fa-comments',
        'position' => 10,
    ]);

    $CI->app_menu->add_sidebar_children_item('axiomchannel', [
        'slug'     => 'axiomchannel-dashboard',
        'name'     => 'Dashboard',
        'href'     => admin_url('axiomchannel/spa'),
        'icon'     => 'fa fa-th-large',
        'position' => 0,
    ]);

    $CI->app_menu->add_sidebar_children_item('axiomchannel', [
        'slug'     => 'axiomchannel-inbox',
        'name'     => 'Todas as Conversas',
        'href'     => admin_url('axiomchannel/spa?p=conversas'),
        'icon'     => 'fa fa-inbox',
        'position' => 1,
    ]);

    $CI->app_menu->add_sidebar_children_item('axiomchannel', [
        'slug'     => 'axiomchannel-pipeline',
        'name'     => 'CRM Pipeline',
        'href'     => admin_url('axiomchannel/spa?p=pipeline'),
        'icon'     => 'fa fa-columns',
        'position' => 3,
    ]);

    $CI->app_menu->add_sidebar_children_item('axiomchannel', [
        'slug'     => 'axiomchannel-devices',
        'name'     => 'Dispositivos',
        'href'     => admin_url('axiomchannel/spa?p=dispositivos'),
        'icon'     => 'fa fa-mobile',
        'position' => 2,
    ]);

    $CI->app_menu->add_sidebar_children_item('axiomchannel', [
        'slug'     => 'axiomchannel-assistant',
        'name'     => 'Assistente IA',
        'href'     => admin_url('axiomchannel/spa?p=assistente'),
        'icon'     => 'fa fa-robot',
        'position' => 4,
    ]);

    $CI->app_menu->add_sidebar_children_item('axiomchannel', [
        'slug'     => 'axiomchannel-appointments',
        'name'     => 'Agendamentos',
        'href'     => admin_url('axiomchannel/spa?p=agendamentos'),
        'icon'     => 'fa fa-calendar',
        'position' => 5,
    ]);

    $CI->app_menu->add_sidebar_children_item('axiomchannel', [
        'slug'     => 'axiomchannel-contracts',
        'name'     => 'Contratos',
        'href'     => admin_url('axiomchannel/spa?p=contratos'),
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

// -------------------------------------------------------
// BLOCO 1 — Esconde UI padrão do Perfex em todo o admin
// -------------------------------------------------------
function axiomchannel_hide_perfex_ui()
{
    if (!is_admin_logged_in()) return;
    echo '
    <style>
    .navbar-static-top  { display: none !important; }
    aside#menu          { display: none !important; }
    #menu               { display: none !important; }
    #setup-menu-wrapper { display: none !important; }
    .footer             { display: none !important; }
    #page-wrapper       { margin: 0 !important; padding: 0 !important; width: 100% !important; min-height: 100vh !important; }
    .content-wrapper    { padding: 0 !important; margin: 0 !important; background: transparent !important; }
    #wrapper            { background: #0a0f1a !important; }
    body                { background: #0a0f1a !important; overflow-x: hidden; }
    .page-title-area    { display: none !important; }
    .row.page-title     { display: none !important; }
    </style>
    ';
}

// -------------------------------------------------------
// BLOCO 2 — Redireciona via JS qualquer página do admin para o SPA
// -------------------------------------------------------
function axiomchannel_spa_redirect()
{
    if (!is_admin_logged_in()) return;

    $CI      = &get_instance();
    $current = $CI->uri->uri_string();

    $excluded = [
        'axiomchannel/spa',
        'axiomchannel/spa_page',
        'axiomchannel/webhook',
        'axiomchannel/meta_webhook',
        'axiomchannel/contract_sign',
        'axiomchannel/google_calendar_callback',
        'authentication',
        'clients/client_portal',
    ];

    foreach ($excluded as $exc) {
        if (strpos($current, $exc) !== false) return;
    }

    $spa_url = admin_url('axiomchannel/spa');

    echo '<script>
(function(){
    if (window.location.href.indexOf("axiomchannel/spa") !== -1) return;
    var url  = window.location.href;
    var page = "dashboard";
    if      (url.indexOf("axiomchannel/inbox")       > -1 ||
             url.indexOf("axiomchannel/chat")         > -1) page = "conversas";
    else if (url.indexOf("axiomchannel/pipeline")     > -1) page = "pipeline";
    else if (url.indexOf("axiomchannel/assistant")    > -1) page = "assistente";
    else if (url.indexOf("axiomchannel/automations")  > -1) page = "automacoes";
    else if (url.indexOf("axiomchannel/appointments") > -1) page = "agendamentos";
    else if (url.indexOf("axiomchannel/contracts")    > -1) page = "contratos";
    else if (url.indexOf("axiomchannel/devices")      > -1) page = "dispositivos";
    else if (url.indexOf("/clients")                  > -1) page = "clientes";
    else if (url.indexOf("/invoices")                 > -1) page = "financeiro";
    else if (url.indexOf("/reports")                  > -1) page = "relatorios";
    else if (url.indexOf("/leads")                    > -1) page = "leads";
    window.location.replace("' . $spa_url . '?p=" + page);
})();
</script>';
}

// -------------------------------------------------------
// Intercepta o dashboard padrão do Perfex
// Redireciona Dashboard::index() → Axiom_dashboard
// -------------------------------------------------------
function axiomchannel_intercept_perfex_dashboard()
{
    $CI = &get_instance();
    $class  = strtolower($CI->router->fetch_class());
    $method = strtolower($CI->router->fetch_method());
    if ($class === 'dashboard' && $method === 'index') {
        redirect(admin_url('axiomchannel/spa'));
    }
}
