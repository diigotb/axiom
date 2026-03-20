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
hooks()->add_action('admin_init',     'axiomchannel_init_menu');
hooks()->add_action('app_admin_head', 'axiomchannel_add_assets');

// Admin layout — topbar
hooks()->add_action('after_body_start', 'axiomchannel_inject_topbar');

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

    // Corrige o link do Dashboard para apontar para admin/dashboard (Axiom_dashboard::index)
    $CI->app_menu->add_sidebar_menu_item('dashboard', [
        'name'     => 'Dashboard',
        'href'     => admin_url('dashboard'),
        'position' => 1,
        'icon'     => 'fa fa-home',
    ]);

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
// Assets (CSS + JS + body class)
// -------------------------------------------------------
function axiomchannel_add_assets()
{
    static $loaded = false;
    if ($loaded) return;
    $loaded = true;

    $css_spa   = module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css');
    $css_admin = module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiom_admin.css');
    $js_admin  = module_dir_url(AXIOMCHANNEL_MODULE, 'assets/js/axiom_admin.js');
    $base_url  = base_url();
    $v         = @filemtime(module_dir_path(AXIOMCHANNEL_MODULE, 'assets/js/axiom_admin.js')) ?: date('YmdHis');

    echo '<link rel="stylesheet" href="' . $css_spa   . '?v=' . $v . '">';
    echo '<link rel="stylesheet" href="' . $css_admin . '?v=' . $v . '">';
    echo '<script>window.AXIOM_BASE_URL = ' . json_encode($base_url) . ';</script>';
    echo '<script defer src="' . $js_admin . '?v=' . $v . '"></script>';
}

// -------------------------------------------------------
// Topbar — injetada via after_body_start (primeiro filho do <body>)
// -------------------------------------------------------
function axiomchannel_inject_topbar()
{
    if (!is_staff_logged_in()) return;
    $view_file = module_dir_path(AXIOMCHANNEL_MODULE, 'views/admin_layout/axiom_header.php');
    if (!file_exists($view_file)) return;

    // Adiciona axiom-admin ao body imediatamente (após <body> — document.body já existe)
    echo '<script>document.body.classList.add("axiom-admin");</script>';

    $CI = &get_instance();
    $staff_id   = get_staff_user_id();
    $staff      = $CI->db->get_where('tblstaff', ['staffid' => $staff_id])->row();
    $avatar_url = '';
    if ($staff) {
        $avatar_path = FCPATH . 'uploads/staff_profile_images/' . $staff_id . '.jpg';
        $avatar_url  = file_exists($avatar_path)
            ? base_url('uploads/staff_profile_images/' . $staff_id . '.jpg') . '?v=' . filemtime($avatar_path)
            : base_url('assets/images/avatar.png');
    }

    extract(['staff' => $staff, 'avatar_url' => $avatar_url]);
    include $view_file;
}

// -------------------------------------------------------
// Dashboard widgets — injetados no início do dashboard
// -------------------------------------------------------
function axiomchannel_inject_dashboard_widgets()
{
    if (!is_staff_logged_in()) return;
    $view_file = module_dir_path(AXIOMCHANNEL_MODULE, 'views/admin_layout/axiom_dashboard.php');
    if (!file_exists($view_file)) return;

    $CI = &get_instance();
    $staff_id = get_staff_user_id();

    // Métricas rápidas (sem escopo — dashboard geral)
    $metrics = [
        'contacts'      => (int) $CI->db->count_all_results(db_prefix() . 'axch_contacts'),
        'open'          => (int) $CI->db->get_where(db_prefix() . 'axch_contacts', ['status' => 'open'])->num_rows(),
        'pending'       => (int) $CI->db->get_where(db_prefix() . 'axch_contacts', ['status' => 'pending'])->num_rows(),
        'messages_today'=> (int) $CI->db->where('DATE(created_at)', date('Y-m-d'))->count_all_results(db_prefix() . 'axch_messages'),
        'devices'       => (int) $CI->db->count_all_results(db_prefix() . 'axch_devices'),
        'leads'         => (int) $CI->db->count_all_results(db_prefix() . 'axch_crm_leads'),
        'contracts'     => (int) $CI->db->where('MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())', null, false)->count_all_results(db_prefix() . 'axch_contracts'),
        'automations'   => (int) $CI->db->get_where(db_prefix() . 'axch_automations', ['is_active' => 1])->num_rows(),
    ];

    // Preferências do usuário
    $prefs_row = $CI->db->get_where(db_prefix() . 'axch_staff_preferences', ['staff_id' => $staff_id])->row();
    $prefs = $prefs_row ? json_decode($prefs_row->preferences ?? '{}', true) : [];

    // Nome do staff
    $staff = $CI->db->get_where('tblstaff', ['staffid' => $staff_id])->row();

    $data = [
        'ax_metrics' => $metrics,
        'ax_prefs'   => $prefs,
        'staff'      => $staff,
    ];
    extract($data);
    include $view_file;
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

