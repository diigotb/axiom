<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head(); ?>
<link rel="stylesheet" href="<?= module_dir_url('axiomchannel','assets/css/axiomchannel.css') ?>">
<style>
/* RESET PERFEX */
body,#wrapper,.content-wrapper,#page-wrapper{background:#0a0f1a!important;margin:0!important;padding:0!important}
.navbar-static-top,.footer,aside#menu,#menu,#setup-menu-wrapper{display:none!important}
#page-wrapper{margin:0!important;padding:0!important;min-height:100vh;width:100%!important}
.content{padding:0!important;margin:0!important}

/* APP SHELL */
#axiom-app{display:flex;flex-direction:column;height:100vh;overflow:hidden;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif}

/* CSS VARS */
#axiom-app{
  --ax-accent:#2D7A6B;
  --ax-header-bg:linear-gradient(90deg,#1B3A4B,#0a1520);
  --ax-sidebar-bg:linear-gradient(180deg,#1B3A4B,#0d1e2b);
  --ax-bg-primary:#0a0f1a;
  --ax-bg-secondary:#0f1923;
  --ax-bg-card:#162030;
  --ax-text-primary:#ffffff;
  --ax-text-secondary:rgba(255,255,255,.7);
  --ax-text-muted:rgba(255,255,255,.35);
  --ax-border:rgba(255,255,255,.06);
  --ax-sidebar-item:rgba(255,255,255,.55);
  --ax-sidebar-active-bg:rgba(45,122,107,.2);
}

/* HEADER */
#ax-header{height:54px;background:var(--ax-header-bg);display:flex;align-items:center;padding:0 18px;gap:12px;border-bottom:1px solid rgba(45,122,107,.2);flex-shrink:0;position:relative;z-index:1000}
#ax-header .ax-logo{display:flex;align-items:center;gap:8px;cursor:pointer;text-decoration:none}
#ax-header .ax-logo-text{font-size:15px;font-weight:700;color:#fff;letter-spacing:.1em}
#ax-search{flex:1;max-width:280px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);border-radius:7px;padding:6px 12px;display:flex;align-items:center;gap:8px;cursor:text}
#ax-search span{font-size:12px;color:rgba(255,255,255,.3);flex:1}
#ax-search kbd{font-size:9px;color:rgba(255,255,255,.2);background:rgba(255,255,255,.06);padding:1px 5px;border-radius:3px;border:1px solid rgba(255,255,255,.1)}
.ax-hright{margin-left:auto;display:flex;align-items:center;gap:6px}
.ax-hbtn{width:34px;height:34px;border-radius:7px;background:rgba(255,255,255,.06);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.6);font-size:13px;position:relative;transition:all .15s;text-decoration:none}
.ax-hbtn:hover,.ax-hbtn.on{background:rgba(45,122,107,.25);color:#2D7A6B}
.ax-nbadge{position:absolute;top:5px;right:5px;width:7px;height:7px;border-radius:50%;background:#E53E3E;border:1.5px solid #1B3A4B;display:none}
.ax-hdiv{width:1px;height:22px;background:rgba(255,255,255,.08)}
.ax-hav{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#2D7A6B,#1B3A4B);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;border:2px solid rgba(45,122,107,.4);cursor:pointer}
.ax-huname{font-size:11px;font-weight:600;color:#fff}
.ax-hurole{font-size:9px;color:rgba(255,255,255,.35)}

/* NOTIF PANEL */
#ax-notif{display:none;position:fixed;top:58px;right:82px;width:290px;background:#162030;border:1px solid rgba(45,122,107,.3);border-radius:12px;overflow:hidden;z-index:2000;box-shadow:0 12px 40px rgba(0,0,0,.6)}
#ax-notif.open{display:block}
.ax-nh{padding:11px 14px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;justify-content:space-between}
.ax-ni{padding:9px 13px;border-bottom:1px solid rgba(255,255,255,.04);display:flex;gap:9px;align-items:flex-start;cursor:pointer;transition:background .12s}
.ax-ni:hover{background:rgba(255,255,255,.03)}
.ax-ni:last-child{border:none}
.ax-ni-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0}
.ax-ni-dot{width:6px;height:6px;border-radius:50%;background:#2D7A6B;flex-shrink:0;margin-top:4px}

/* THEME PANEL */
#ax-theme{display:none;position:fixed;top:54px;right:0;bottom:0;width:260px;background:#1a2535;border-left:1px solid rgba(45,122,107,.2);z-index:1500;padding:16px;overflow-y:auto;box-shadow:-8px 0 30px rgba(0,0,0,.4)}
#ax-theme.open{display:block}
.ax-tp-section{font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:rgba(255,255,255,.3);margin:12px 0 7px}
.ax-theme-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:6px}
.ax-th-opt{border-radius:6px;overflow:hidden;cursor:pointer;border:2px solid transparent;transition:all .12s}
.ax-th-opt.sel{border-color:#2D7A6B}
.ax-th-preview{height:44px;display:flex}
.ax-th-sb{width:28%;display:flex;flex-direction:column;padding:5px 3px;gap:2px}
.ax-th-dot{height:3px;border-radius:1px;width:75%}
.ax-th-dot.a{width:100%}
.ax-th-main{flex:1;padding:4px;display:flex;flex-direction:column;gap:2px}
.ax-th-card{border-radius:2px;height:8px}
.ax-th-lbl{font-size:10px;font-weight:500;padding:4px 6px;display:flex;align-items:center;justify-content:space-between}
.ax-th-check{width:12px;height:12px;border-radius:50%;border:1.5px solid rgba(255,255,255,.3);display:flex;align-items:center;justify-content:center;font-size:7px}
.ax-th-check.on{background:#2D7A6B;border-color:#2D7A6B;color:#fff}
.ax-accent-row{display:flex;gap:5px}
.ax-ac{width:24px;height:24px;border-radius:50%;cursor:pointer;border:2px solid transparent;transition:all .12s}
.ax-ac.sel{border-color:#fff;transform:scale(1.15)}
.ax-save-btn{width:100%;padding:9px;background:#2D7A6B;color:#fff;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;margin-top:12px}
.ax-save-btn:hover{background:#256a5d}
.ax-close-btn{width:100%;padding:8px;background:rgba(255,255,255,.06);color:rgba(255,255,255,.6);border:none;border-radius:7px;font-size:12px;cursor:pointer;margin-top:5px}

/* BODY */
#ax-body{display:flex;flex:1;overflow:hidden;position:relative}

/* ICON SIDEBAR */
#ax-sidebar{width:58px;min-width:58px;background:var(--ax-sidebar-bg);display:flex;flex-direction:column;align-items:center;padding:12px 0;gap:3px;border-right:1px solid rgba(255,255,255,.05);flex-shrink:0;overflow:visible}
.ax-isb-logo{width:36px;height:36px;cursor:pointer;margin-bottom:14px;flex-shrink:0}
.ax-ico{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.35);font-size:15px;cursor:pointer;transition:all .15s;position:relative;flex-shrink:0}
.ax-ico:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.8)}
.ax-ico.active{background:rgba(45,122,107,.2);color:var(--ax-accent)}
.ax-tip{display:none;position:fixed;left:68px;background:#1e2d3d;color:#fff;font-size:11px;font-weight:500;padding:5px 10px;border-radius:6px;white-space:nowrap;z-index:9999;border:1px solid rgba(255,255,255,.08);pointer-events:none;box-shadow:0 4px 12px rgba(0,0,0,.4)}
.ax-ico:hover .ax-tip{display:block}
.ax-ico-badge{position:absolute;top:4px;right:4px;background:#E53E3E;color:#fff;border-radius:8px;padding:1px 4px;font-size:8px;font-weight:700;line-height:1.2}
.ax-isb-group{display:flex;flex-direction:column;align-items:center;gap:3px;flex:1;width:100%;padding:0 10px}
.ax-isb-bot{display:flex;flex-direction:column;align-items:center;gap:3px;padding:10px 10px 0;border-top:1px solid rgba(255,255,255,.05);width:100%}
.ax-isb-av{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#2D7A6B,#1B3A4B);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;border:2px solid rgba(45,122,107,.4);cursor:pointer;margin-top:6px}

/* MAIN */
#ax-main{flex:1;overflow:hidden;position:relative;background:var(--ax-bg-secondary)}
.ax-loader{position:absolute;inset:0;background:var(--ax-bg-secondary);display:flex;align-items:center;justify-content:center;z-index:10;opacity:0;pointer-events:none;transition:opacity .15s}
.ax-loader.show{opacity:1;pointer-events:all}
.ax-spinner{width:28px;height:28px;border:2px solid rgba(45,122,107,.2);border-top-color:var(--ax-accent);border-radius:50%;animation:axspin .6s linear infinite}
@keyframes axspin{to{transform:rotate(360deg)}}
#ax-content{height:100%;overflow-y:auto}
#ax-content::-webkit-scrollbar{width:4px}
#ax-content::-webkit-scrollbar-thumb{background:rgba(45,122,107,.3);border-radius:2px}
.ax-content-inner{padding:18px}
.ax-page-title{font-size:16px;font-weight:600;color:var(--ax-text-primary);margin-bottom:3px}
.ax-page-sub{font-size:11px;color:var(--ax-text-muted);margin-bottom:16px}

/* MOBILE */
@media (max-width:768px){
  #ax-header #ax-search,#ax-header #ax-weather{display:none}
  #ax-body{margin-bottom:60px}
  #ax-sidebar{display:none!important}
  #ax-bottom-nav{display:flex!important;position:fixed;bottom:0;left:0;right:0;height:60px;background:#0d1520;border-top:1px solid rgba(255,255,255,.06);z-index:1000;align-items:center;justify-content:space-around;padding:0 8px}
  .ax-bnav-item{display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 10px;border-radius:10px;cursor:pointer;transition:all .15s;min-width:44px}
  .ax-bnav-item i{font-size:18px;color:rgba(255,255,255,.35)}
  .ax-bnav-item span{font-size:9px;color:rgba(255,255,255,.35)}
  .ax-bnav-item.active i,.ax-bnav-item.active span{color:#2D7A6B}
  .ax-bnav-item.active{background:rgba(45,122,107,.12)}
  #ax-main{width:100%}
  #ax-theme{width:100%!important;top:auto;bottom:60px;right:0;height:70vh;border-radius:20px 20px 0 0}
}
@media (min-width:769px){#ax-bottom-nav{display:none!important}}
</style>

<div id="axiom-app">

  <!-- HEADER -->
  <div id="ax-header">
    <div class="ax-logo" onclick="axNav('dashboard')">
      <svg width="28" height="28" viewBox="0 0 100 100" fill="none">
        <polygon points="50,5 95,85 5,85" fill="none" stroke="#4A90D9" stroke-width="8"/>
        <polygon points="50,20 82,78 18,78" fill="#1B3A4B"/>
        <polygon points="50,5 72,47 28,47" fill="#4A90D9" opacity=".9"/>
        <polygon points="50,47 72,47 61,68" fill="#F5A623" opacity=".95"/>
        <polygon points="50,47 28,47 39,68" fill="#2D7A6B" opacity=".85"/>
      </svg>
      <span class="ax-logo-text">AXIOM</span>
    </div>

    <div id="ax-search">
      <i class="fa fa-search" style="color:rgba(255,255,255,.3);font-size:11px"></i>
      <span>Buscar clientes, conversas, leads...</span>
      <kbd>⌘K</kbd>
    </div>

    <div id="ax-weather">
      <span id="ax-wi" style="font-size:14px">🌡️</span>
      <div>
        <div style="font-size:12px;font-weight:600;color:#fff" id="ax-temp">--°C</div>
        <div style="font-size:9px;color:rgba(255,255,255,.35)" id="ax-loc">Carregando...</div>
      </div>
    </div>

    <div class="ax-hright">
      <button class="ax-hbtn" id="ax-nbt" onclick="axToggleNotif()">
        <i class="fa fa-bell"></i>
        <div class="ax-nbadge" id="ax-nb"></div>
      </button>
      <button class="ax-hbtn" id="ax-tbt" onclick="axToggleTheme()" title="Personalizar tema">
        <i class="fa fa-paint-brush"></i>
      </button>
      <div class="ax-hdiv"></div>
      <div class="ax-hav"><?= strtoupper(substr(get_staff_full_name(),0,2)) ?></div>
      <div>
        <div class="ax-huname"><?= get_staff_full_name() ?></div>
        <div class="ax-hurole">Administrador</div>
      </div>
    </div>

    <!-- NOTIF DROPDOWN -->
    <div id="ax-notif">
      <div class="ax-nh">
        <div style="font-size:12px;font-weight:600;color:#fff">
          Notificações
          <span id="ax-nc" style="background:#E53E3E;color:#fff;border-radius:10px;padding:1px 6px;font-size:9px;font-weight:700;margin-left:4px">0</span>
        </div>
        <span style="font-size:10px;color:#2D7A6B;cursor:pointer" onclick="axClearNotifs()">Marcar lidas</span>
      </div>
      <div id="ax-nl">
        <div style="padding:16px;text-align:center;font-size:11px;color:rgba(255,255,255,.3)">
          <i class="fa fa-spinner fa-spin"></i>
        </div>
      </div>
      <div style="padding:9px;text-align:center;border-top:1px solid rgba(255,255,255,.05)">
        <span style="font-size:11px;color:#2D7A6B;cursor:pointer" onclick="axNav('notificacoes')">Ver todas</span>
      </div>
    </div>
  </div>

  <!-- BODY -->
  <div id="ax-body">

    <!-- ICON SIDEBAR -->
    <div id="ax-sidebar">
      <div class="ax-isb-logo" onclick="axNav('dashboard')">
        <svg width="36" height="36" viewBox="0 0 100 100" fill="none">
          <polygon points="50,5 95,85 5,85" fill="none" stroke="#4A90D9" stroke-width="8"/>
          <polygon points="50,20 82,78 18,78" fill="#1B3A4B"/>
          <polygon points="50,5 72,47 28,47" fill="#4A90D9" opacity=".9"/>
          <polygon points="50,47 72,47 61,68" fill="#F5A623" opacity=".95"/>
          <polygon points="50,47 28,47 39,68" fill="#2D7A6B" opacity=".85"/>
        </svg>
      </div>
      <div class="ax-isb-group">
        <div class="ax-ico active" data-page="dashboard" onclick="axNav('dashboard')">
          <i class="fa fa-th-large"></i><span class="ax-tip">Dashboard</span>
        </div>
        <div class="ax-ico" data-page="conversas" onclick="axNav('conversas')">
          <i class="fa fa-comments"></i><span class="ax-tip">Todas as Conversas</span>
          <span class="ax-ico-badge" id="sb-conv-badge" style="display:none">0</span>
        </div>
        <div class="ax-ico" data-page="pipeline" onclick="axNav('pipeline')">
          <i class="fa fa-columns"></i><span class="ax-tip">CRM Pipeline</span>
        </div>
        <div class="ax-ico" data-page="assistente" onclick="axNav('assistente')">
          <i class="fa fa-robot"></i><span class="ax-tip">Assistente IA</span>
        </div>
        <div class="ax-ico" data-page="automacoes" onclick="axNav('automacoes')">
          <i class="fa fa-bolt"></i><span class="ax-tip">Automações</span>
        </div>
        <div class="ax-ico" data-page="agendamentos" onclick="axNav('agendamentos')">
          <i class="fa fa-calendar"></i><span class="ax-tip">Agendamentos</span>
        </div>
        <div class="ax-ico" data-page="contratos" onclick="axNav('contratos')">
          <i class="fa fa-file-text"></i><span class="ax-tip">Contratos</span>
        </div>
        <div class="ax-ico" data-page="dispositivos" onclick="axNav('dispositivos')">
          <i class="fa fa-mobile"></i><span class="ax-tip">Dispositivos</span>
        </div>
      </div>
      <div class="ax-isb-bot">
        <div class="ax-ico" onclick="axToggleTheme()">
          <i class="fa fa-paint-brush"></i><span class="ax-tip">Personalizar tema</span>
        </div>
        <div class="ax-ico" data-page="clientes" onclick="axNav('clientes')">
          <i class="fa fa-users"></i><span class="ax-tip">Clientes</span>
        </div>
        <div class="ax-ico" data-page="financeiro" onclick="axNav('financeiro')">
          <i class="fa fa-money"></i><span class="ax-tip">Financeiro</span>
        </div>
        <div class="ax-isb-av" title="Meu perfil"><?= strtoupper(substr(get_staff_full_name(),0,2)) ?></div>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <div id="ax-main">
      <div class="ax-loader" id="ax-loader">
        <div class="ax-spinner"></div>
      </div>
      <div id="ax-content">
        <div class="ax-content-inner" style="text-align:center;padding:40px;color:rgba(255,255,255,.3)">
          <div class="ax-spinner" style="margin:0 auto 12px"></div>
          <div style="font-size:12px">Carregando...</div>
        </div>
      </div>
    </div>

    <!-- THEME PANEL -->
    <div id="ax-theme">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
        <div style="font-size:13px;font-weight:600;color:#fff">Personalizar</div>
        <button onclick="axToggleTheme()" style="background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;font-size:16px">&#x2715;</button>
      </div>
      <div class="ax-tp-section">Tema</div>
      <div class="ax-theme-grid">
        <div class="ax-th-opt sel" id="th-dark" onclick="axSelectTheme(this,'dark')">
          <div class="ax-th-preview" style="background:#0a0f1a">
            <div class="ax-th-sb" style="background:#1B3A4B"><div class="ax-th-dot a" style="background:rgba(45,122,107,.8)"></div><div class="ax-th-dot" style="background:rgba(255,255,255,.2)"></div></div>
            <div class="ax-th-main" style="background:#0f1923"><div class="ax-th-card" style="background:#162030"></div><div class="ax-th-card" style="background:#162030"></div></div>
          </div>
          <div class="ax-th-lbl" style="background:#1B3A4B;color:#fff">Escuro<div class="ax-th-check on">&#x2713;</div></div>
        </div>
        <div class="ax-th-opt" id="th-mid" onclick="axSelectTheme(this,'mid')">
          <div class="ax-th-preview" style="background:#f0f4f8">
            <div class="ax-th-sb" style="background:#fff;border-right:1px solid #e2e8f0"><div class="ax-th-dot a" style="background:#2D7A6B"></div><div class="ax-th-dot" style="background:#e2e8f0"></div></div>
            <div class="ax-th-main" style="background:#f8fafc"><div class="ax-th-card" style="background:#fff;border:1px solid #e2e8f0"></div><div class="ax-th-card" style="background:#fff;border:1px solid #e2e8f0"></div></div>
          </div>
          <div class="ax-th-lbl" style="background:#1B3A4B;color:#fff">Médio<div class="ax-th-check">&#x25CB;</div></div>
        </div>
        <div class="ax-th-opt" id="th-light" onclick="axSelectTheme(this,'light')">
          <div class="ax-th-preview" style="background:#fff">
            <div class="ax-th-sb" style="background:#f8fafc;border-right:1px solid #e2e8f0"><div class="ax-th-dot a" style="background:#2D7A6B"></div><div class="ax-th-dot" style="background:#e2e8f0"></div></div>
            <div class="ax-th-main" style="background:#fff"><div class="ax-th-card" style="background:#f8fafc;border:1px solid #e2e8f0"></div><div class="ax-th-card" style="background:#f8fafc;border:1px solid #e2e8f0"></div></div>
          </div>
          <div class="ax-th-lbl" style="background:#f8fafc;color:#1B3A4B;border-top:1px solid #e2e8f0">Claro<div class="ax-th-check" style="border-color:#cbd5e0">&#x25CB;</div></div>
        </div>
      </div>
      <div class="ax-tp-section">Cor de destaque</div>
      <div class="ax-accent-row">
        <div class="ax-ac sel" style="background:#2D7A6B" onclick="axSelectAccent(this,'#2D7A6B')" title="Verde RT"></div>
        <div class="ax-ac" style="background:#1B3A4B" onclick="axSelectAccent(this,'#1B3A4B')" title="Marinho"></div>
        <div class="ax-ac" style="background:#3182CE" onclick="axSelectAccent(this,'#3182CE')" title="Azul"></div>
        <div class="ax-ac" style="background:#805AD5" onclick="axSelectAccent(this,'#805AD5')" title="Roxo"></div>
        <div class="ax-ac" style="background:#D69E2E" onclick="axSelectAccent(this,'#D69E2E')" title="Dourado"></div>
        <div class="ax-ac" style="background:#E53E3E" onclick="axSelectAccent(this,'#E53E3E')" title="Vermelho"></div>
      </div>
      <button class="ax-save-btn" onclick="axSaveTheme()">&#x2713; Aplicar tema</button>
      <button class="ax-close-btn" onclick="axToggleTheme()">Fechar</button>
    </div>

    <!-- MOBILE BOTTOM NAV -->
    <div id="ax-bottom-nav" style="display:none">
      <div class="ax-bnav-item active" data-page="dashboard" onclick="axNavMobile('dashboard',this)">
        <i class="fa fa-th-large"></i><span>Painel</span>
      </div>
      <div class="ax-bnav-item" data-page="conversas" onclick="axNavMobile('conversas',this)">
        <i class="fa fa-comments"></i><span>Chats</span>
      </div>
      <div class="ax-bnav-item" data-page="pipeline" onclick="axNavMobile('pipeline',this)">
        <i class="fa fa-columns"></i><span>Pipeline</span>
      </div>
      <div class="ax-bnav-item" data-page="agendamentos" onclick="axNavMobile('agendamentos',this)">
        <i class="fa fa-calendar"></i><span>Agenda</span>
      </div>
      <div class="ax-bnav-item" onclick="axToggleTheme()">
        <i class="fa fa-ellipsis-h"></i><span>Mais</span>
      </div>
    </div>

  </div><!-- /ax-body -->
</div><!-- /axiom-app -->

<script>
const ADMIN_URL  = '<?= admin_url() ?>';
const CSRF_NAME  = '<?= $this->security->get_csrf_token_name() ?>';
var   CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';

var axCurrentPage = '';
var axTheme = 'dark';
var axAccent = '#2D7A6B';

var AX_THEMES = {
  dark:{
    '--ax-header-bg':'linear-gradient(90deg,#1B3A4B,#0a1520)',
    '--ax-sidebar-bg':'linear-gradient(180deg,#1B3A4B,#0d1e2b)',
    '--ax-bg-primary':'#0a0f1a','--ax-bg-secondary':'#0f1923','--ax-bg-card':'#162030',
    '--ax-text-primary':'#ffffff','--ax-text-secondary':'rgba(255,255,255,.7)',
    '--ax-text-muted':'rgba(255,255,255,.35)','--ax-border':'rgba(255,255,255,.06)',
    '--ax-sidebar-item':'rgba(255,255,255,.55)','--ax-sidebar-active-bg':'rgba(45,122,107,.2)'
  },
  mid:{
    '--ax-header-bg':'linear-gradient(90deg,#1B3A4B,#2D4A5B)',
    '--ax-sidebar-bg':'#ffffff','--ax-bg-primary':'#f0f4f8','--ax-bg-secondary':'#f8fafc',
    '--ax-bg-card':'#ffffff','--ax-text-primary':'#1B3A4B','--ax-text-secondary':'#334155',
    '--ax-text-muted':'#94a3b8','--ax-border':'#e2e8f0',
    '--ax-sidebar-item':'#64748b','--ax-sidebar-active-bg':'#E8F5F2'
  },
  light:{
    '--ax-header-bg':'#ffffff','--ax-sidebar-bg':'#f8fafc',
    '--ax-bg-primary':'#ffffff','--ax-bg-secondary':'#f8fafc','--ax-bg-card':'#f1f5f9',
    '--ax-text-primary':'#1B3A4B','--ax-text-secondary':'#334155',
    '--ax-text-muted':'#94a3b8','--ax-border':'#e2e8f0',
    '--ax-sidebar-item':'#64748b','--ax-sidebar-active-bg':'#E8F5F2'
  }
};

function axApplyTheme(theme, accent) {
  var themes = {
    dark: {
      '--ax-header-bg':'linear-gradient(90deg,#1B3A4B,#0a1520)',
      '--ax-sidebar-bg':'linear-gradient(180deg,#1B3A4B,#0d1e2b)',
      '--ax-bg-primary':'#0a0f1a','--ax-bg-secondary':'#0f1923','--ax-bg-card':'#162030',
      '--ax-text-primary':'#ffffff','--ax-text-secondary':'rgba(255,255,255,.7)',
      '--ax-text-muted':'rgba(255,255,255,.35)','--ax-border':'rgba(255,255,255,.06)',
      '--ax-sidebar-item':'rgba(255,255,255,.55)','--ax-sidebar-active-bg':'rgba(45,122,107,.2)',
      '--ax-body-bg':'#0a0f1a'
    },
    mid: {
      '--ax-header-bg':'linear-gradient(90deg,#1B3A4B,#2D4A5B)',
      '--ax-sidebar-bg':'#1B3A4B',
      '--ax-bg-primary':'#f0f4f8','--ax-bg-secondary':'#f8fafc','--ax-bg-card':'#ffffff',
      '--ax-text-primary':'#1B3A4B','--ax-text-secondary':'#334155',
      '--ax-text-muted':'#94a3b8','--ax-border':'#e2e8f0',
      '--ax-sidebar-item':'rgba(255,255,255,.7)','--ax-sidebar-active-bg':'rgba(255,255,255,.15)',
      '--ax-body-bg':'#f0f4f8'
    },
    light: {
      '--ax-header-bg':'#ffffff',
      '--ax-sidebar-bg':'#f8fafc',
      '--ax-bg-primary':'#ffffff','--ax-bg-secondary':'#f8fafc','--ax-bg-card':'#f1f5f9',
      '--ax-text-primary':'#1B3A4B','--ax-text-secondary':'#334155',
      '--ax-text-muted':'#94a3b8','--ax-border':'#e2e8f0',
      '--ax-sidebar-item':'#64748b','--ax-sidebar-active-bg':'#E8F5F2',
      '--ax-body-bg':'#ffffff'
    }
  };
  var t    = themes[theme] || themes.dark;
  var root = document.getElementById('axiom-app');
  var ac   = accent || '#2D7A6B';
  Object.entries(t).forEach(function(entry){ root.style.setProperty(entry[0], entry[1]); });
  root.style.setProperty('--ax-accent', ac);
  document.body.style.background = t['--ax-body-bg'];
  var header  = document.getElementById('ax-header');
  var sidebar = document.getElementById('ax-sidebar');
  var main    = document.getElementById('ax-main');
  if (header)  header.style.background  = t['--ax-header-bg'];
  if (sidebar) sidebar.style.background = t['--ax-sidebar-bg'];
  if (main)    main.style.background    = t['--ax-bg-secondary'];
  axTheme = theme;
  axAccent = ac;
}

var AX_ROUTES = {
  dashboard:   'axiomchannel/spa_page/dashboard',
  conversas:   'axiomchannel/spa_page/conversas',
  pipeline:    'axiomchannel/spa_page/pipeline',
  assistente:  'axiomchannel/spa_page/assistente',
  automacoes:  'axiomchannel/spa_page/automacoes',
  agendamentos:'axiomchannel/spa_page/agendamentos',
  contratos:   'axiomchannel/spa_page/contratos',
  dispositivos:'axiomchannel/spa_page/dispositivos',
  clientes:    'axiomchannel/spa_page/clientes',
  financeiro:  'axiomchannel/spa_page/financeiro',
  relatorios:  'axiomchannel/spa_page/relatorios',
  leads:       'axiomchannel/spa_page/leads'
};

function axNav(page) {
  if (page === axCurrentPage) return;
  document.querySelectorAll('.ax-ico,.ax-bnav-item').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('[data-page="'+page+'"]').forEach(s => s.classList.add('active'));
  document.getElementById('ax-notif').classList.remove('open');
  document.getElementById('ax-theme').classList.remove('open');
  document.getElementById('ax-nbt').classList.remove('on');
  document.getElementById('ax-tbt').classList.remove('on');
  var loader = document.getElementById('ax-loader');
  loader.classList.add('show');
  var url = ADMIN_URL + (AX_ROUTES[page] || 'axiomchannel/spa_page/'+page);
  fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest','X-AXIOM-SPA':'1'}})
  .then(r => r.text())
  .then(html => {
    document.getElementById('ax-content').innerHTML = '<div class="ax-content-inner">'+html+'</div>';
    axCurrentPage = page;
    loader.classList.remove('show');
    var m = html.match(/data-csrf="([^"]+)"/);
    if (m) CSRF_TOKEN = m[1];
  })
  .catch(() => {
    loader.classList.remove('show');
    document.getElementById('ax-content').innerHTML = '<div class="ax-content-inner"><div style="text-align:center;padding:40px;color:rgba(255,255,255,.4)"><i class="fa fa-exclamation-circle" style="font-size:24px;display:block;margin-bottom:8px"></i>Erro ao carregar. Tente novamente.</div></div>';
  });
  axCurrentPage = page;
  window.history.pushState({page}, '', '?p='+page);
}

function axNavMobile(page, el) {
  axNav(page);
  document.querySelectorAll('.ax-bnav-item').forEach(i => i.classList.remove('active'));
  if (el) el.classList.add('active');
}

function axToggleNotif() {
  var p = document.getElementById('ax-notif');
  document.getElementById('ax-theme').classList.remove('open');
  document.getElementById('ax-tbt').classList.remove('on');
  p.classList.toggle('open');
  document.getElementById('ax-nbt').classList.toggle('on');
  if (p.classList.contains('open')) axLoadNotifs();
}

function axLoadNotifs() {
  fetch(ADMIN_URL + 'notifications/get_notifications', {headers:{'X-Requested-With':'XMLHttpRequest'}})
  .then(r => r.json())
  .then(data => {
    var list = document.getElementById('ax-nl');
    if (!data || !data.length) {
      list.innerHTML = '<div style="padding:16px;text-align:center;font-size:11px;color:rgba(255,255,255,.3)">Sem notificações</div>';
      document.getElementById('ax-nb').style.display = 'none';
      document.getElementById('ax-nc').textContent = '0';
      return;
    }
    var unread = data.filter(n => !n.isread).length;
    document.getElementById('ax-nc').textContent = unread;
    if (unread > 0) document.getElementById('ax-nb').style.display = 'block';
    list.innerHTML = data.slice(0,6).map(n => '<div class="ax-ni" onclick="location.href=\''+(n.link||'#')+'\'">'
      +'<div class="ax-ni-icon" style="background:rgba(45,122,107,.15)"><i class="fa fa-bell" style="color:#2D7A6B"></i></div>'
      +'<div style="flex:1"><div style="font-size:11px;color:rgba(255,255,255,.75);line-height:1.4">'+(n.description||n.title||'')+'</div>'
      +'<div style="font-size:9px;color:rgba(255,255,255,.25);margin-top:2px">'+(n.date||'')+'</div></div>'
      +(!n.isread ? '<div class="ax-ni-dot"></div>' : '')+'</div>').join('');
  })
  .catch(() => {
    document.getElementById('ax-nl').innerHTML = '<div style="padding:16px;text-align:center;font-size:11px;color:rgba(255,255,255,.3)">Erro ao carregar</div>';
  });
}

function axClearNotifs() {
  document.querySelectorAll('.ax-ni-dot').forEach(d => d.remove());
  document.getElementById('ax-nc').textContent = '0';
  document.getElementById('ax-nb').style.display = 'none';
}

function axToggleTheme() {
  var tp = document.getElementById('ax-theme');
  var np = document.getElementById('ax-notif');
  if (!tp) return;
  np.classList.remove('open');
  document.getElementById('ax-nbt').classList.remove('on');
  tp.classList.toggle('open');
  document.getElementById('ax-tbt').classList.toggle('on');
}

function axSelectTheme(el, theme) {
  document.querySelectorAll('.ax-th-opt').forEach(function(o) {
    o.classList.remove('sel');
    var c = o.querySelector('.ax-th-check');
    if(c){c.className='ax-th-check';c.textContent='○';}
  });
  el.classList.add('sel');
  var c = el.querySelector('.ax-th-check');
  if(c){c.className='ax-th-check on';c.textContent='✓';}
  axApplyTheme(theme, axAccent);
}

function axSelectAccent(el, color) {
  document.querySelectorAll('.ax-ac').forEach(function(o){ o.classList.remove('sel'); });
  el.classList.add('sel');
  document.getElementById('axiom-app').style.setProperty('--ax-accent', color);
  axAccent = color;
}

function axSaveTheme() {
  fetch(ADMIN_URL + 'axiomchannel/save_theme', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: new URLSearchParams({theme:axTheme, accent_color:axAccent, [CSRF_NAME]:CSRF_TOKEN})
  })
  .then(r => r.json())
  .then(() => {
    var btn = document.querySelector('.ax-save-btn');
    btn.textContent = '✓ Salvo!';
    btn.style.background = '#1B3A4B';
    setTimeout(() => { btn.textContent = '✓ Aplicar tema'; btn.style.background = '#2D7A6B'; axToggleTheme(); }, 1500);
  });
}

document.addEventListener('click', function(e) {
  var np = document.getElementById('ax-notif');
  if (np.classList.contains('open') && !np.contains(e.target) && !document.getElementById('ax-nbt').contains(e.target)) {
    np.classList.remove('open');
    document.getElementById('ax-nbt').classList.remove('on');
  }
});

(function(){
  if (!navigator.geolocation) return;
  navigator.geolocation.getCurrentPosition(function(pos) {
    var lat = pos.coords.latitude.toFixed(4), lon = pos.coords.longitude.toFixed(4);
    fetch('https://api.open-meteo.com/v1/forecast?latitude='+lat+'&longitude='+lon+'&current_weather=true')
    .then(r => r.json()).then(d => {
      document.getElementById('ax-temp').textContent = Math.round(d.current_weather.temperature)+'°C';
      var wi = {0:'☀️',1:'🌤️',2:'⛅',3:'☁️',61:'🌧️',80:'🌦️'};
      document.getElementById('ax-wi').textContent = wi[d.current_weather.weathercode] || '🌡️';
      fetch('https://nominatim.openstreetmap.org/reverse?lat='+lat+'&lon='+lon+'&format=json')
      .then(r => r.json()).then(g => {
        document.getElementById('ax-loc').textContent = g.address.city || g.address.town || g.address.village || '';
      }).catch(()=>{});
    }).catch(()=>{});
  }, ()=>{}, {timeout:5000});
})();

// BLOCO 5 — Intercepta links internos do Perfex para navegar no SPA
document.addEventListener('click', function(e) {
  var link = e.target.closest('a[href]');
  if (!link) return;
  var href = link.getAttribute('href');
  if (!href || href === '#' || href.startsWith('javascript')) return;
  if (href.startsWith('http') && href.indexOf(window.location.hostname) === -1) return;
  var pageMap = {
    'axiomchannel/inbox':       'conversas',
    'axiomchannel/chat':        'conversas',
    'axiomchannel/pipeline':    'pipeline',
    'axiomchannel/assistant':   'assistente',
    'axiomchannel/automations': 'automacoes',
    'axiomchannel/appointments':'agendamentos',
    'axiomchannel/contracts':   'contratos',
    'axiomchannel/devices':     'dispositivos',
    'axiomchannel/dashboard':   'dashboard',
    'axiomchannel/spa':         'dashboard',
    'admin/clients':            'clientes',
    'admin/invoices':           'financeiro',
    'admin/leads':              'leads',
    'admin/reports':            'relatorios',
    'axiom-dashboard':          'dashboard'
  };
  for (var key in pageMap) {
    if (href.indexOf(key) !== -1) {
      e.preventDefault();
      e.stopPropagation();
      axNav(pageMap[key]);
      return;
    }
  }
}, true);

document.addEventListener('DOMContentLoaded', function() {
  fetch(ADMIN_URL + 'axiomchannel/get_theme', {headers:{'X-Requested-With':'XMLHttpRequest'}})
  .then(r => r.json())
  .then(pref => {
    axTheme = pref.theme || 'dark';
    axAccent = pref.accent_color || '#2D7A6B';
    axApplyTheme(axTheme, axAccent);
    var sel = document.getElementById('th-'+axTheme);
    if (sel) axSelectTheme(sel, axTheme);
    var ac = document.querySelector('.ax-ac[style*="'+axAccent.replace('#','')+'"]');
    if (ac) axSelectAccent(ac, axAccent);
  })
  .catch(() => axApplyTheme('dark', '#2D7A6B'));
  var startPage = new URLSearchParams(window.location.search).get('p') || 'dashboard';
  axNav(startPage);
  window.onpopstate = function(e) { if (e.state && e.state.page) axNav(e.state.page); };
});
</script>
<?php init_tail(); ?>
