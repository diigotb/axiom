<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head(); ?>
<link rel="stylesheet" href="<?= module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css') ?>">
<style>
body,#wrapper,.content-wrapper,#page-wrapper{background:#0a0f1a!important}
.navbar-static-top,.footer{display:none!important}
#page-wrapper{margin:0!important;padding:0!important;min-height:100vh}
aside#menu{display:none!important}
.axd-header{position:fixed;top:0;left:0;right:0;height:54px;background:linear-gradient(90deg,#1B3A4B,#0a1520);display:flex;align-items:center;padding:0 20px;gap:12px;border-bottom:1px solid rgba(45,122,107,.25);z-index:1000}
.axd-logo{display:flex;align-items:center;gap:8px}
.axd-logo-text{font-size:15px;font-weight:700;color:#fff;letter-spacing:.1em}
.axd-right{margin-left:auto;display:flex;align-items:center;gap:6px}
.axd-btn{width:34px;height:34px;border-radius:7px;background:rgba(255,255,255,.06);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.6);font-size:13px;transition:all .15s;text-decoration:none}
.axd-btn:hover{background:rgba(45,122,107,.25);color:#fff}
.axd-div{width:1px;height:22px;background:rgba(255,255,255,.08)}
.axd-av{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#2D7A6B,#1B3A4B);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;border:2px solid rgba(45,122,107,.4)}
.axd-uname{font-size:11px;font-weight:600;color:#fff}
.axd-urole{font-size:9px;color:rgba(255,255,255,.35)}
.axd-body{display:flex;margin-top:54px;min-height:calc(100vh - 54px)}
.axd-sidebar{width:200px;min-width:200px;background:linear-gradient(180deg,#1B3A4B,#0d1e2b);position:fixed;top:54px;left:0;bottom:0;display:flex;flex-direction:column;overflow-y:auto;border-right:1px solid rgba(255,255,255,.05);z-index:100}
.axd-sidebar::-webkit-scrollbar{width:0}
.axd-ss{padding:12px 12px 3px;font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.25)}
.axd-si{display:flex;align-items:center;gap:8px;padding:9px 14px;font-size:12px;color:rgba(255,255,255,.55);transition:all .12s;border-radius:0 20px 20px 0;margin-right:10px;text-decoration:none}
.axd-si:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.85)}
.axd-si.active{background:rgba(45,122,107,.2);color:#fff;border-left:2px solid #2D7A6B}
.axd-si i{width:15px;text-align:center;font-size:13px}
.axd-sb-bot{margin-top:auto;padding:10px 0;border-top:1px solid rgba(255,255,255,.06)}
.axd-main{flex:1;margin-left:200px;padding:16px;background:#0f1923;min-height:calc(100vh - 54px)}
.axd-page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.axd-page-header h3{font-size:16px;font-weight:600;color:#fff}
.axd-page-header p{font-size:11px;color:rgba(255,255,255,.35);margin-top:2px}
.axd-back{background:rgba(255,255,255,.06);color:rgba(255,255,255,.6);border:none;border-radius:7px;padding:7px 13px;font-size:11px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;text-decoration:none;transition:all .15s}
.axd-back:hover{background:rgba(45,122,107,.2);color:#fff}
.axd-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:12px}
.axd-stat{background:#162030;border-radius:10px;padding:14px;border:1px solid rgba(255,255,255,.06);position:relative;overflow:hidden}
.axd-stat-acc{position:absolute;top:0;left:0;right:0;height:2px}
.axd-stat-n{font-size:24px;font-weight:700;margin:6px 0 3px}
.axd-stat-l{font-size:10px;color:rgba(255,255,255,.4)}
.axd-stat-t{font-size:10px;font-weight:500;margin-top:5px}
.axd-grid2{display:grid;grid-template-columns:1.5fr 1fr;gap:10px;margin-bottom:10px}
.axd-widget{background:#162030;border-radius:10px;padding:14px;border:1px solid rgba(255,255,255,.06)}
.axd-wt{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:rgba(255,255,255,.3);margin-bottom:10px;display:flex;align-items:center;justify-content:space-between}
.axd-wt a{font-size:10px;color:#2D7A6B;font-weight:400;text-decoration:none;text-transform:none;letter-spacing:0}
.axd-li{display:flex;align-items:center;gap:8px;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.04)}
.axd-li:last-child{border:none}
.axd-av-sm{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;flex-shrink:0}
.axd-badge{padding:2px 7px;border-radius:4px;font-size:9px;font-weight:600;white-space:nowrap}
.b-g{background:rgba(45,122,107,.2);color:#2D7A6B}
.b-a{background:rgba(245,166,35,.2);color:#F5A623}
.b-b{background:rgba(74,144,217,.2);color:#4A90D9}
.b-r{background:rgba(229,62,62,.15);color:#E53E3E}
.b-p{background:rgba(128,90,213,.2);color:#805AD5}
.axd-pipe{display:flex;align-items:center;gap:8px;padding:4px 0}
.axd-pipe-lbl{font-size:10px;color:rgba(255,255,255,.4);min-width:110px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.axd-pipe-bar{flex:1;height:4px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden}
.axd-pipe-fill{height:100%;border-radius:2px}
.axd-pipe-n{font-size:10px;font-weight:600;color:#fff;min-width:20px;text-align:right}
.axd-dev{display:flex;align-items:center;gap:8px;padding:8px 10px;background:rgba(255,255,255,.03);border-radius:7px;margin-bottom:5px}
.axd-dev-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.axd-canal{display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.04)}
.axd-canal:last-child{border:none}
.axd-ia-item{display:flex;align-items:center;gap:8px;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.04)}
.axd-ia-item:last-child{border:none}
.axd-ia-icon{width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0}
.axd-auto-item{display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.04)}
.axd-auto-item:last-child{border:none}
</style>

<div class="axd-header">
  <div class="axd-logo">
    <svg width="28" height="28" viewBox="0 0 100 100" fill="none">
      <polygon points="50,5 95,85 5,85" fill="none" stroke="#4A90D9" stroke-width="8"/>
      <polygon points="50,20 82,78 18,78" fill="#1B3A4B"/>
      <polygon points="50,5 72,47 28,47" fill="#4A90D9" opacity=".9"/>
      <polygon points="50,47 72,47 61,68" fill="#F5A623" opacity=".95"/>
      <polygon points="50,47 28,47 39,68" fill="#2D7A6B" opacity=".85"/>
    </svg>
    <span class="axd-logo-text">AXIOM</span>
  </div>
  <div style="font-size:11px;color:rgba(255,255,255,.4);margin-left:4px">/ AxiomChannel</div>
  <div class="axd-right">
    <a href="<?= admin_url('axiomchannel/inbox') ?>" class="axd-btn" title="Conversas"><i class="fa fa-comments"></i></a>
    <a href="<?= admin_url('settings') ?>" class="axd-btn" title="Configurações"><i class="fa fa-cog"></i></a>
    <div class="axd-div"></div>
    <div class="axd-av"><?= strtoupper(substr(get_staff_full_name(), 0, 2)) ?></div>
    <div>
      <div class="axd-uname"><?= get_staff_full_name() ?></div>
      <div class="axd-urole">Administrador</div>
    </div>
  </div>
</div>

<div class="axd-body">
  <div class="axd-sidebar">
    <div class="axd-ss">AxiomChannel</div>
    <a href="<?= admin_url('axiomchannel/dashboard') ?>" class="axd-si active"><i class="fa fa-th-large"></i> Dashboard</a>
    <a href="<?= admin_url('axiomchannel/inbox') ?>" class="axd-si"><i class="fa fa-comments"></i> Conversas</a>
    <a href="<?= admin_url('axiomchannel/pipeline') ?>" class="axd-si"><i class="fa fa-columns"></i> CRM Pipeline</a>
    <div class="axd-ss">IA & Automação</div>
    <a href="<?= admin_url('axiomchannel/assistant') ?>" class="axd-si"><i class="fa fa-robot"></i> Assistente IA</a>
    <a href="<?= admin_url('axiomchannel/automations') ?>" class="axd-si"><i class="fa fa-bolt"></i> Automações</a>
    <div class="axd-ss">Operacional</div>
    <a href="<?= admin_url('axiomchannel/appointments') ?>" class="axd-si"><i class="fa fa-calendar"></i> Agendamentos</a>
    <a href="<?= admin_url('axiomchannel/contracts') ?>" class="axd-si"><i class="fa fa-file-text"></i> Contratos</a>
    <a href="<?= admin_url('axiomchannel/devices') ?>" class="axd-si"><i class="fa fa-mobile"></i> Dispositivos</a>
    <div class="axd-sb-bot">
      <a href="<?= admin_url('axiom-dashboard') ?>" class="axd-si"><i class="fa fa-arrow-left"></i> Dashboard Geral</a>
    </div>
  </div>

  <div class="axd-main">
    <div class="axd-page-header">
      <div>
        <h3>Dashboard AxiomChannel</h3>
        <p id="axch-date">Carregando...</p>
      </div>
      <a href="<?= admin_url('axiom-dashboard') ?>" class="axd-back">
        <i class="fa fa-arrow-left"></i> Dashboard Geral
      </a>
    </div>

    <?php
    // Organiza canais por tipo
    $canal_map = ['whatsapp' => 0, 'facebook' => 0, 'instagram' => 0];
    foreach ($canais as $c) {
        $key = $c->channel;
        if ($key === 'facebook' || $key === 'instagram') {
            $canal_map['facebook'] = ($canal_map['facebook'] ?? 0) + ($key === 'facebook' ? $c->total : 0);
            $canal_map['instagram'] = ($canal_map['instagram'] ?? 0) + ($key === 'instagram' ? $c->total : 0);
        } else {
            $canal_map[$key] = $c->total;
        }
    }
    $fb_ig = ($canal_map['facebook'] ?? 0) + ($canal_map['instagram'] ?? 0);
    ?>

    <div class="axd-stats">
      <div class="axd-stat">
        <div class="axd-stat-acc" style="background:#25D366"></div>
        <div class="axd-stat-l">WhatsApp hoje</div>
        <div class="axd-stat-n" style="color:#25D366"><?= $canal_map['whatsapp'] ?? 0 ?></div>
        <div class="axd-stat-t" style="color:rgba(255,255,255,.3)">conversas ativas</div>
      </div>
      <div class="axd-stat">
        <div class="axd-stat-acc" style="background:#4A90D9"></div>
        <div class="axd-stat-l">Facebook + Instagram</div>
        <div class="axd-stat-n" style="color:#4A90D9"><?= $fb_ig ?></div>
        <div class="axd-stat-t" style="color:rgba(255,255,255,.3)"><?= $canal_map['facebook'] ?? 0 ?> FB · <?= $canal_map['instagram'] ?? 0 ?> IG</div>
      </div>
      <div class="axd-stat">
        <div class="axd-stat-acc" style="background:#2D7A6B"></div>
        <div class="axd-stat-l">IA resolveu</div>
        <div class="axd-stat-n" style="color:#2D7A6B"><?= $ia_pct ?>%</div>
        <div class="axd-stat-t" style="color:rgba(255,255,255,.3)"><?= $ia_total ?> msgs · <?= $ia_transferiu ?> transferências</div>
      </div>
      <div class="axd-stat">
        <div class="axd-stat-acc" style="background:#F5A623"></div>
        <div class="axd-stat-l">Tempo médio resposta</div>
        <div class="axd-stat-n" style="color:#F5A623"><?= $tempo < 60 ? $tempo . 's' : round($tempo / 60) . 'min' ?></div>
        <div class="axd-stat-t" style="color:rgba(255,255,255,.3)">pela IA hoje</div>
      </div>
    </div>

    <div class="axd-grid2">
      <!-- COLUNA ESQUERDA -->
      <div style="display:flex;flex-direction:column;gap:10px">

        <div class="axd-widget">
          <div class="axd-wt">Canais ativos hoje</div>
          <?php
          $canal_labels = ['whatsapp' => ['WhatsApp', '#25D366'], 'facebook' => ['Facebook Messenger', '#4A90D9'], 'instagram' => ['Instagram Direct', '#E53E3E']];
          $total_canais = max(array_sum(array_column((array)$canais, 'total')), 1);
          foreach ($canal_labels as $key => [$label, $color]):
            $val = $canal_map[$key] ?? 0;
            $pct = round(($val / $total_canais) * 100);
          ?>
            <div class="axd-canal">
              <div style="display:flex;align-items:center;gap:8px">
                <div style="width:8px;height:8px;border-radius:50%;background:<?= $color ?>"></div>
                <span style="font-size:11px;color:rgba(255,255,255,.7)"><?= $label ?></span>
              </div>
              <div style="display:flex;align-items:center;gap:8px">
                <div style="width:80px;height:4px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden">
                  <div style="width:<?= $pct ?>%;height:100%;background:<?= $color ?>;border-radius:2px"></div>
                </div>
                <span style="font-size:11px;font-weight:600;color:#fff;min-width:20px;text-align:right"><?= $val ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="axd-widget">
          <div class="axd-wt">Performance da IA hoje</div>
          <div class="axd-ia-item">
            <div class="axd-ia-icon" style="background:rgba(45,122,107,.15)"><i class="fa fa-robot" style="color:#2D7A6B"></i></div>
            <div style="flex:1">
              <div style="font-size:11px;color:rgba(255,255,255,.7)">Mensagens enviadas pela IA</div>
            </div>
            <span style="font-size:14px;font-weight:700;color:#2D7A6B"><?= $ia_total ?></span>
          </div>
          <div class="axd-ia-item">
            <div class="axd-ia-icon" style="background:rgba(245,166,35,.15)"><i class="fa fa-calendar-check-o" style="color:#F5A623"></i></div>
            <div style="flex:1">
              <div style="font-size:11px;color:rgba(255,255,255,.7)">Agendamentos criados pela IA</div>
            </div>
            <span style="font-size:14px;font-weight:700;color:#F5A623"><?= $ag_ia ?></span>
          </div>
          <div class="axd-ia-item">
            <div class="axd-ia-icon" style="background:rgba(74,144,217,.15)"><i class="fa fa-file-text" style="color:#4A90D9"></i></div>
            <div style="flex:1">
              <div style="font-size:11px;color:rgba(255,255,255,.7)">Contratos enviados hoje</div>
            </div>
            <span style="font-size:14px;font-weight:700;color:#4A90D9"><?= $contratos_ia ?></span>
          </div>
          <div class="axd-ia-item">
            <div class="axd-ia-icon" style="background:rgba(229,62,62,.1)"><i class="fa fa-exchange" style="color:#E53E3E"></i></div>
            <div style="flex:1">
              <div style="font-size:11px;color:rgba(255,255,255,.7)">Transferências para humano</div>
            </div>
            <span style="font-size:14px;font-weight:700;color:#E53E3E"><?= $ia_transferiu ?></span>
          </div>
        </div>

        <div class="axd-widget">
          <div class="axd-wt">Conversas abertas recentes <a href="<?= admin_url('axiomchannel/inbox') ?>">Ver todas</a></div>
          <?php if (empty($recentes)): ?>
            <div style="text-align:center;padding:10px;font-size:11px;color:rgba(255,255,255,.3)">Nenhuma conversa aberta</div>
          <?php else: foreach ($recentes as $r): ?>
            <div class="axd-li">
              <div class="axd-av-sm" style="background:linear-gradient(135deg,#2D7A6B,#1B3A4B)"><?= strtoupper(substr($r->name ?? 'C', 0, 1)) ?></div>
              <div style="flex:1;min-width:0">
                <div style="font-size:11px;font-weight:500;color:#fff"><?= htmlspecialchars($r->name ?? 'Contato') ?></div>
                <div style="font-size:10px;color:rgba(255,255,255,.3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars(substr($r->last_msg ?? '', 0, 50)) ?></div>
              </div>
              <?php
              $ch = $r->channel ?? 'whatsapp';
              $ch_colors = ['whatsapp' => 'b-g', 'facebook' => 'b-b', 'instagram' => 'b-r'];
              ?>
              <span class="axd-badge <?= $ch_colors[$ch] ?? 'b-g' ?>"><?= ucfirst($ch) ?></span>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>

      <!-- COLUNA DIREITA -->
      <div style="display:flex;flex-direction:column;gap:10px">

        <div class="axd-widget">
          <div class="axd-wt">Pipeline de vendas <a href="<?= admin_url('axiomchannel/pipeline') ?>">Kanban</a></div>
          <?php if (empty($pipeline)): ?>
            <div style="text-align:center;padding:10px;font-size:11px;color:rgba(255,255,255,.3)">Nenhum pipeline</div>
          <?php else:
            $max_p = max(array_column($pipeline, 'total')) ?: 1;
            foreach ($pipeline as $s): $pct_p = ($s['total'] / $max_p) * 100; ?>
            <div class="axd-pipe">
              <div class="axd-pipe-lbl"><?= htmlspecialchars($s['name']) ?></div>
              <div class="axd-pipe-bar"><div class="axd-pipe-fill" style="width:<?= $pct_p ?>%;background:<?= $s['color'] ?: '#2D7A6B' ?>"></div></div>
              <div class="axd-pipe-n"><?= $s['total'] ?></div>
            </div>
          <?php endforeach; endif; ?>
        </div>

        <div class="axd-widget">
          <div class="axd-wt">Automações disparadas hoje</div>
          <?php
          $auto_labels = [
            'birthday'     => ['Aniversário',     '#E53E3E', 'fa-birthday-cake'],
            'invoice'       => ['Cobrança',         '#F5A623', 'fa-money'],
            'followup'      => ['Follow-up',        '#4A90D9', 'fa-refresh'],
            'inactive'      => ['Inativo',          '#805AD5', 'fa-user-times'],
            'appointment'   => ['Agendamento',      '#2D7A6B', 'fa-calendar'],
            'satisfaction'  => ['Satisfação',       '#D69E2E', 'fa-star'],
          ];
          if (empty($automacoes)): ?>
            <div style="text-align:center;padding:10px;font-size:11px;color:rgba(255,255,255,.3)">Nenhuma automação ativa</div>
          <?php else: foreach ($automacoes as $a):
            $info = $auto_labels[$a->type] ?? [$a->type, '#2D7A6B', 'fa-bolt'];
          ?>
            <div class="axd-auto-item">
              <div style="display:flex;align-items:center;gap:7px">
                <div style="width:26px;height:26px;border-radius:6px;background:<?= $info[1] ?>22;display:flex;align-items:center;justify-content:center">
                  <i class="fa <?= $info[2] ?>" style="color:<?= $info[1] ?>;font-size:11px"></i>
                </div>
                <span style="font-size:11px;color:rgba(255,255,255,.7)"><?= $info[0] ?></span>
              </div>
              <span style="font-size:13px;font-weight:700;color:<?= $info[1] ?>"><?= $a->total ?></span>
            </div>
          <?php endforeach; endif; ?>
        </div>

        <div class="axd-widget">
          <div class="axd-wt">Dispositivos <a href="<?= admin_url('axiomchannel/devices') ?>">Gerenciar</a></div>
          <?php if (empty($devices)): ?>
            <div style="text-align:center;padding:10px;font-size:11px;color:rgba(255,255,255,.3)">Nenhum dispositivo</div>
          <?php else: foreach ($devices as $d): ?>
            <div class="axd-dev">
              <div class="axd-dev-dot" style="background:<?= $d->status === 'connected' ? '#2D7A6B' : '#E53E3E' ?>"></div>
              <div style="flex:1;font-size:12px;color:#fff;font-weight:500"><?= htmlspecialchars($d->name) ?></div>
              <span class="axd-badge <?= $d->status === 'connected' ? 'b-g' : 'b-r' ?>">
                <?= $d->status === 'connected' ? 'Online' : 'Offline' ?>
              </span>
            </div>
          <?php endforeach; endif; ?>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
(function () {
  var days = ['Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado'];
  var months = ['janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'];
  var d = new Date();
  document.getElementById('axch-date').textContent = days[d.getDay()] + ', ' + d.getDate() + ' de ' + months[d.getMonth()] + ' de ' + d.getFullYear();
})();
</script>
<?php init_tail(); ?>
