<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
/* ============================================================
   AXIOM Dashboard — funciona dentro do layout Perfex + topbar + sidebar
   ============================================================ */
.axd-main { padding: 18px 22px 30px; }

/* Topbar da página */
.axd-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    gap: 12px;
    flex-wrap: wrap;
}
.axd-page-title { font-size: 18px; font-weight: 700; color: #fff; margin: 0; }
.axd-page-actions { display: flex; align-items: center; gap: 8px; }
.axd-btn-customize {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 7px;
    background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);
    color: rgba(255,255,255,0.7); font-size: 12px; font-weight: 600;
    cursor: pointer; transition: background .15s, color .15s;
}
.axd-btn-customize:hover { background: rgba(255,255,255,0.12); color: #fff; }

/* Banner */
.axd-banner {
    background: linear-gradient(135deg, #162D3D 0%, #1a3548 100%);
    border-radius: 10px; padding: 20px 24px; margin-bottom: 14px;
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 16px; border: 1px solid rgba(255,255,255,0.08); flex-wrap: wrap;
}
.axd-banner-left { flex: 1; min-width: 200px; }
.axd-greeting    { font-size: 22px; font-weight: 700; color: #fff; margin: 0 0 4px; }
.axd-date        { font-size: 12px; color: rgba(255,255,255,0.4); margin-bottom: 18px; }
.axd-metrics-row { display: flex; gap: 28px; flex-wrap: wrap; }
.axd-metric      { min-width: 80px; }
.axd-mv          { font-size: 24px; font-weight: 700; color: #fff; line-height: 1; }
.axd-mv.gold     { color: #F5A623; }
.axd-mv.teal     { color: #2D7A6B; }
.axd-ml          { font-size: 11px; color: rgba(255,255,255,0.38); margin-top: 4px; }
.axd-ia-box {
    background: rgba(45,122,107,0.15); border: 1px solid rgba(45,122,107,0.3);
    border-radius: 8px; padding: 14px 20px; text-align: center; min-width: 90px; flex-shrink: 0;
}
.axd-ia-num { font-size: 28px; font-weight: 700; color: #2D7A6B; line-height: 1; }
.axd-ia-lbl { font-size: 10px; color: rgba(255,255,255,0.38); margin-top: 6px; line-height: 1.4; }

/* Cards row */
.axd-cards-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px; margin-bottom: 14px;
}
/* When cards are hidden, remaining ones expand */
.axd-cards-row .axd-widget-slot[style*="display:none"],
.axd-cards-row .axd-widget-slot[style*="display: none"] { display: none !important; }

.axd-card {
    background: #162D3D; border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.07); padding: 14px 16px;
    border-top: 3px solid #2D7A6B;
}
.axd-card.cb { border-top-color: #378ADD; }
.axd-card.cy { border-top-color: #F5A623; }
.axd-card.cr { border-top-color: #E74C3C; }
.axd-card-label { font-size: 9px; text-transform: uppercase; letter-spacing: .07em; color: rgba(255,255,255,0.35); margin-bottom: 8px; }
.axd-card-val   { font-size: 28px; font-weight: 700; color: #fff; line-height: 1; }
.axd-card-sub   { font-size: 11px; margin-top: 6px; color: rgba(255,255,255,0.35); }
.axd-card-sub.cg  { color: #2D7A6B; }
.axd-card-sub.cy2 { color: #F5A623; }
.axd-card-sub.cr2 { color: #E74C3C; }
.axd-card-bar  { height: 3px; border-radius: 2px; margin-top: 10px; background: rgba(255,255,255,0.08); overflow: hidden; }
.axd-card-fill { height: 100%; border-radius: 2px; }

/* Bottom grid */
.axd-bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.axd-bottom.one-col { grid-template-columns: 1fr; }
.axd-panel {
    background: #162D3D; border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.07); padding: 16px;
}
.axd-panel-title { font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.85); margin-bottom: 3px; }
.axd-panel-sub   { font-size: 11px; color: rgba(255,255,255,0.3); margin-bottom: 14px; display: flex; align-items: center; gap: 10px; }
.axd-legend      { display: flex; align-items: center; gap: 4px; font-size: 10px; color: rgba(255,255,255,0.5); }
.axd-legend-dot  { width: 10px; height: 2px; border-radius: 1px; display: inline-block; }

/* Conversations list */
.axd-conv-row     { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
.axd-conv-row:last-child { border-bottom: none; }
.axd-conv-av      { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0; }
.axd-conv-info    { flex: 1; min-width: 0; }
.axd-conv-name    { font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.85); }
.axd-conv-msg     { font-size: 11px; color: rgba(255,255,255,0.3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.axd-tag          { font-size: 9px; font-weight: 600; padding: 2px 7px; border-radius: 4px; white-space: nowrap; flex-shrink: 0; }
.axd-tag-ia       { background: rgba(45,122,107,0.2); color: #2D7A6B; }
.axd-tag-pend     { background: rgba(245,166,35,0.15); color: #F5A623; }

/* ============================================================
   Modal de personalização
   ============================================================ */
.axd-modal-backdrop {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.7); z-index: 50000;
    align-items: center; justify-content: center;
}
.axd-modal-backdrop.open { display: flex; }
.axd-modal {
    background: #162D3D; border: 1px solid rgba(255,255,255,0.1);
    border-radius: 14px; width: 480px; max-width: 95vw;
    max-height: 85vh; overflow-y: auto; padding: 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.6);
}
.axd-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px;
}
.axd-modal-title { font-size: 16px; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 8px; }
.axd-modal-title i { color: #2D7A6B; }
.axd-modal-close {
    background: none; border: none; color: rgba(255,255,255,0.4);
    font-size: 18px; cursor: pointer; padding: 0; line-height: 1;
}
.axd-modal-close:hover { color: #fff; }

.axd-widget-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;
}
.axd-widget-toggle {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 14px; border-radius: 9px;
    background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
    cursor: pointer; transition: background .15s, border-color .15s; gap: 10px;
}
.axd-widget-toggle:hover { background: rgba(255,255,255,0.08); }
.axd-widget-toggle.active { border-color: rgba(45,122,107,0.5); background: rgba(45,122,107,0.1); }
.axd-widget-toggle-left { display: flex; align-items: center; gap: 9px; }
.axd-widget-toggle-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
.axd-widget-toggle-name { font-size: 12px; font-weight: 600; color: #fff; }
.axd-widget-toggle-desc { font-size: 10px; color: rgba(255,255,255,0.35); margin-top: 1px; }

/* Switch toggle */
.axd-switch { position: relative; width: 36px; height: 20px; flex-shrink: 0; }
.axd-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
.axd-switch-slider {
    position: absolute; inset: 0; border-radius: 20px;
    background: rgba(255,255,255,0.12); cursor: pointer;
    transition: background .2s;
}
.axd-switch-slider::before {
    content: ''; position: absolute;
    width: 14px; height: 14px; border-radius: 50%;
    background: #fff; top: 3px; left: 3px;
    transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}
.axd-switch input:checked + .axd-switch-slider { background: #2D7A6B; }
.axd-switch input:checked + .axd-switch-slider::before { transform: translateX(16px); }

.axd-modal-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: 10px;
    margin-top: 4px;
}
.axd-btn-cancel {
    padding: 9px 18px; border-radius: 7px;
    background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);
    color: rgba(255,255,255,0.6); font-size: 12px; font-weight: 600; cursor: pointer;
    transition: background .15s;
}
.axd-btn-cancel:hover { background: rgba(255,255,255,0.12); }
.axd-btn-save {
    padding: 9px 18px; border-radius: 7px;
    background: #2D7A6B; border: none;
    color: #fff; font-size: 12px; font-weight: 600; cursor: pointer;
    transition: opacity .15s;
}
.axd-btn-save:hover { opacity: 0.88; }

/* Widget visibility — hidden widgets */
.axd-widget-slot.axd-hidden { display: none !important; }

@media (max-width: 1100px) { .axd-cards-row { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 900px)  { .axd-bottom { grid-template-columns: 1fr; } .axd-banner { flex-direction: column; } .axd-metrics-row { flex-wrap: wrap; gap: 16px; } .axd-widget-grid { grid-template-columns: 1fr; } }
@media (max-width: 600px)  { .axd-cards-row { grid-template-columns: 1fr; } .axd-main { padding: 12px; } }
</style>

<div id="wrapper">
<div class="content axd-main">

  <!-- Cabeçalho da página -->
  <div class="axd-page-header">
    <h2 class="axd-page-title"><i class="fa fa-th-large" style="color:#2D7A6B;margin-right:8px"></i>Dashboard</h2>
    <div class="axd-page-actions">
      <button class="axd-btn-customize" id="axd-open-customize" onclick="axdOpenCustomize()">
        <i class="fa fa-sliders"></i> Personalizar widgets
      </button>
    </div>
  </div>

  <!-- WIDGET: Banner -->
  <div class="axd-widget-slot" id="axd-slot-banner">
    <div class="axd-banner">
      <div class="axd-banner-left">
        <p class="axd-greeting" id="axd-gt">Carregando...</p>
        <div class="axd-date"><?php echo date('l, d \d\e F'); ?></div>
        <div class="axd-metrics-row">
          <div class="axd-metric">
            <div class="axd-mv"><?php echo $conv_hoje; ?></div>
            <div class="axd-ml">Conversas hoje</div>
          </div>
          <div class="axd-metric">
            <div class="axd-mv gold">R$<?php echo $fat_atual >= 1000 ? number_format($fat_atual/1000,1).'k' : number_format($fat_atual,0,',','.'); ?></div>
            <div class="axd-ml">Faturamento mês</div>
          </div>
          <div class="axd-metric">
            <div class="axd-mv teal"><?php echo $ia_pct; ?>%</div>
            <div class="axd-ml">IA resolveu</div>
          </div>
        </div>
      </div>
      <div class="axd-ia-box">
        <div class="axd-ia-num"><?php echo $ia_hoje; ?></div>
        <div class="axd-ia-lbl">atendidos<br>pela IA hoje</div>
      </div>
    </div>
  </div>

  <!-- WIDGETS: 4 Cards (cada um individualmente ocultável) -->
  <div class="axd-cards-row" id="axd-cards-row">

    <div class="axd-widget-slot" id="axd-slot-card-conv">
      <div class="axd-card">
        <div class="axd-card-label">Conversas</div>
        <div class="axd-card-val"><?php echo $conv_hoje; ?></div>
        <div class="axd-card-sub <?php echo $conv_var >= 0 ? 'cg' : 'cr2'; ?>">
          <?php echo ($conv_var >= 0 ? '↑' : '↓').' '.abs($conv_var); ?>% vs ontem
        </div>
        <div class="axd-card-bar"><div class="axd-card-fill" style="width:<?php echo min(100,$conv_hoje*2); ?>%;background:#2D7A6B"></div></div>
      </div>
    </div>

    <div class="axd-widget-slot" id="axd-slot-card-leads">
      <div class="axd-card cb">
        <div class="axd-card-label">Leads Novos</div>
        <div class="axd-card-val"><?php echo $leads_hoje; ?></div>
        <div class="axd-card-sub cy2"><?php echo $leads_urgentes; ?> urgentes</div>
        <div class="axd-card-bar"><div class="axd-card-fill" style="width:<?php echo min(100,$leads_hoje*5); ?>%;background:#378ADD"></div></div>
      </div>
    </div>

    <div class="axd-widget-slot" id="axd-slot-card-ag">
      <div class="axd-card cy">
        <div class="axd-card-label">Agendamentos</div>
        <div class="axd-card-val"><?php echo $ag_hoje_count; ?></div>
        <div class="axd-card-sub">hoje · <?php echo $ag_semana_count; ?> semana</div>
        <div class="axd-card-bar"><div class="axd-card-fill" style="width:<?php echo min(100,$ag_hoje_count*10); ?>%;background:#F5A623"></div></div>
      </div>
    </div>

    <div class="axd-widget-slot" id="axd-slot-card-contr">
      <div class="axd-card cr">
        <div class="axd-card-label">Contratos</div>
        <div class="axd-card-val"><?php echo $contratos_pend; ?></div>
        <div class="axd-card-sub cr2">aguardando assinatura</div>
        <div class="axd-card-bar"><div class="axd-card-fill" style="width:<?php echo min(100,$contratos_pend*10); ?>%;background:#E74C3C"></div></div>
      </div>
    </div>

  </div><!-- /.axd-cards-row -->

  <!-- WIDGETS: Chart + Últimas Conversas -->
  <div class="axd-bottom" id="axd-bottom">

    <div class="axd-widget-slot" id="axd-slot-chart">
      <div class="axd-panel">
        <div class="axd-panel-title">Conversas — últimos 7 dias</div>
        <div class="axd-panel-sub">
          <span class="axd-legend"><span class="axd-legend-dot" style="background:#2D7A6B"></span>WhatsApp</span>
          <span class="axd-legend"><span class="axd-legend-dot" style="background:#378ADD"></span>Meta</span>
        </div>
        <canvas id="axd-chart" height="130"></canvas>
      </div>
    </div>

    <div class="axd-widget-slot" id="axd-slot-ultimas">
      <div class="axd-panel">
        <div class="axd-panel-title">Últimas conversas</div>
        <div class="axd-panel-sub">abertas agora</div>
        <?php
        $av_colors = ['#2D7A6B','#378ADD','#9B59B6','#E74C3C','#F5A623','#E1306C'];
        if (empty($ultimas_conversas)): ?>
        <div style="text-align:center;padding:20px;color:rgba(255,255,255,0.25);font-size:12px">
          <i class="fa fa-comments" style="display:block;font-size:24px;margin-bottom:8px;opacity:.4"></i>
          Nenhuma conversa aberta
        </div>
        <?php else: foreach ($ultimas_conversas as $i => $c):
            $ini = strtoupper(substr($c->name ?: $c->phone_number ?: '?', 0, 1));
            $col = $av_colors[$i % count($av_colors)];
            $ia  = ($c->ia_count ?? 0) > 0;
        ?>
        <div class="axd-conv-row">
          <div class="axd-conv-av" style="background:<?php echo $col; ?>"><?php echo $ini; ?></div>
          <div class="axd-conv-info">
            <div class="axd-conv-name"><?php echo htmlspecialchars($c->name ?: $c->phone_number); ?></div>
            <div class="axd-conv-msg"><?php echo htmlspecialchars(substr($c->last_message ?? '—', 0, 40)); ?></div>
          </div>
          <?php if ($ia): ?>
            <span class="axd-tag axd-tag-ia">IA</span>
          <?php else: ?>
            <span class="axd-tag axd-tag-pend">Pend.</span>
          <?php endif; ?>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

  </div><!-- /.axd-bottom -->

</div><!-- .content -->
</div><!-- #wrapper -->

<!-- ============================================================
     Modal de personalização de widgets
     ============================================================ -->
<div class="axd-modal-backdrop" id="axd-modal" onclick="if(event.target===this)axdCloseCustomize()">
  <div class="axd-modal">
    <div class="axd-modal-header">
      <div class="axd-modal-title"><i class="fa fa-sliders"></i> Personalizar dashboard</div>
      <button class="axd-modal-close" onclick="axdCloseCustomize()">&#10005;</button>
    </div>

    <p style="font-size:11px;color:rgba(255,255,255,0.4);margin-bottom:16px">
      Ative ou desative os widgets que aparecem no seu dashboard. As preferências são salvas automaticamente.
    </p>

    <div class="axd-widget-grid" id="axd-widget-grid">
      <!-- preenchido por JS -->
    </div>

    <div class="axd-modal-footer">
      <button class="axd-btn-cancel" onclick="axdCloseCustomize()">Fechar</button>
      <button class="axd-btn-save" onclick="axdSaveCustomize()"><i class="fa fa-check"></i> Aplicar</button>
    </div>
  </div>
</div>

<script>
(function () {
    'use strict';

    /* ---- Saudação GMT-3 ---- */
    var now = new Date();
    var brt = new Date(now.getTime() + now.getTimezoneOffset() * 60000 - 3 * 3600000);
    var h   = brt.getHours();
    var gr  = h >= 5 && h < 12 ? 'Bom dia' : h >= 12 && h < 18 ? 'Boa tarde' : 'Boa noite';
    var nameEl = document.getElementById('axiom-greeting-name');
    var nome   = nameEl ? nameEl.textContent.trim() : '<?php echo addslashes(htmlspecialchars(explode(' ', get_staff_full_name(get_staff_user_id()))[0])); ?>';
    var el = document.getElementById('axd-gt');
    if (el) el.textContent = gr + (nome ? ', ' + nome : '') + '! 👋';

    /* ---- Chart.js ---- */
    var labels   = <?php echo json_encode($chart_labels); ?>;
    var waData   = <?php echo json_encode($chart_wa); ?>;
    var metaData = <?php echo json_encode($chart_meta); ?>;

    function renderChart() {
        var canvas = document.getElementById('axd-chart');
        if (!canvas || !window.Chart) return;
        new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    { label:'WhatsApp', data:waData,   borderColor:'#2D7A6B', backgroundColor:'rgba(45,122,107,0.08)',  borderWidth:2, pointRadius:3, pointBackgroundColor:'#2D7A6B', tension:0.45, fill:true },
                    { label:'Meta',     data:metaData, borderColor:'#378ADD', backgroundColor:'rgba(55,138,221,0.06)', borderWidth:2, pointRadius:3, pointBackgroundColor:'#378ADD', tension:0.45, fill:true }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode:'index', intersect:false },
                plugins: { legend:{ display:false }, tooltip:{ backgroundColor:'#162D3D', borderColor:'rgba(255,255,255,0.1)', borderWidth:1, titleColor:'#fff', bodyColor:'rgba(255,255,255,0.7)' } },
                scales: {
                    x: { ticks:{color:'rgba(255,255,255,0.3)',font:{size:10}}, grid:{color:'rgba(255,255,255,0.04)'} },
                    y: { ticks:{color:'rgba(255,255,255,0.3)',font:{size:10}}, grid:{color:'rgba(255,255,255,0.04)'}, beginAtZero:true }
                }
            }
        });
    }
    if (window.Chart) { renderChart(); }
    else { var s = document.createElement('script'); s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js'; s.onload = renderChart; document.head.appendChild(s); }

    /* ================================================================
       Widget customization
       ================================================================ */
    var STORAGE_KEY = 'axiom_dash_widgets_v1';

    // Definição dos widgets disponíveis
    var WIDGETS = [
        { id:'banner',    name:'Resumo do dia',       desc:'Saudação + métricas principais',  icon:'fa fa-sun-o',     color:'#F5A623', slotId:'axd-slot-banner' },
        { id:'card-conv', name:'Conversas',           desc:'Card de conversas do dia',         icon:'fa fa-comments',  color:'#2D7A6B', slotId:'axd-slot-card-conv' },
        { id:'card-leads',name:'Leads',               desc:'Card de leads novos',              icon:'fa fa-users',     color:'#378ADD', slotId:'axd-slot-card-leads' },
        { id:'card-ag',   name:'Agendamentos',        desc:'Card de agendamentos',             icon:'fa fa-calendar',  color:'#F5A623', slotId:'axd-slot-card-ag' },
        { id:'card-contr',name:'Contratos',           desc:'Card de contratos pendentes',      icon:'fa fa-file-text', color:'#E74C3C', slotId:'axd-slot-card-contr' },
        { id:'chart',     name:'Gráfico 7 dias',      desc:'Mensagens WhatsApp vs Meta',       icon:'fa fa-bar-chart', color:'#9B59B6', slotId:'axd-slot-chart' },
        { id:'ultimas',   name:'Últimas conversas',   desc:'Lista de conversas abertas',       icon:'fa fa-clock-o',   color:'#2D7A6B', slotId:'axd-slot-ultimas' }
    ];

    // Carrega preferências salvas (default: todos ativos)
    function loadPrefs() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            if (raw) return JSON.parse(raw);
        } catch(e) {}
        var def = {};
        WIDGETS.forEach(function(w){ def[w.id] = true; });
        return def;
    }

    function savePrefs(prefs) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(prefs));
    }

    // Aplica visibilidade dos widgets
    function applyPrefs(prefs) {
        WIDGETS.forEach(function(w) {
            var slot = document.getElementById(w.slotId);
            if (!slot) return;
            if (prefs[w.id] === false) {
                slot.classList.add('axd-hidden');
            } else {
                slot.classList.remove('axd-hidden');
            }
        });
        // Ajusta grid do bottom: se só um visível, vai full width
        adjustBottomGrid(prefs);
        // Ajusta cards row: se nenhum card visível, esconde a row
        adjustCardsRow(prefs);
    }

    function adjustBottomGrid(prefs) {
        var bottom = document.getElementById('axd-bottom');
        if (!bottom) return;
        var chartOn   = prefs['chart'] !== false;
        var ultimasOn = prefs['ultimas'] !== false;
        if (chartOn && ultimasOn) { bottom.style.gridTemplateColumns = '1fr 1fr'; }
        else { bottom.style.gridTemplateColumns = '1fr'; }
    }

    function adjustCardsRow(prefs) {
        var row = document.getElementById('axd-cards-row');
        if (!row) return;
        var cardIds = ['card-conv','card-leads','card-ag','card-contr'];
        var visibleCount = cardIds.filter(function(id){ return prefs[id] !== false; }).length;
        if (visibleCount === 0) { row.style.display = 'none'; }
        else {
            row.style.display = 'grid';
            row.style.gridTemplateColumns = 'repeat(' + Math.min(4, visibleCount) + ', 1fr)';
        }
    }

    // Constrói a grade de widgets no modal
    function buildWidgetGrid(prefs) {
        var grid = document.getElementById('axd-widget-grid');
        if (!grid) return;
        grid.innerHTML = WIDGETS.map(function(w) {
            var checked = prefs[w.id] !== false;
            return '<label class="axd-widget-toggle ' + (checked ? 'active' : '') + '" id="axd-toggle-' + w.id + '">' +
                '<div class="axd-widget-toggle-left">' +
                    '<div class="axd-widget-toggle-icon" style="background:' + w.color + '22;color:' + w.color + '">' +
                        '<i class="' + w.icon + '"></i>' +
                    '</div>' +
                    '<div>' +
                        '<div class="axd-widget-toggle-name">' + w.name + '</div>' +
                        '<div class="axd-widget-toggle-desc">' + w.desc + '</div>' +
                    '</div>' +
                '</div>' +
                '<label class="axd-switch">' +
                    '<input type="checkbox" data-widget="' + w.id + '"' + (checked ? ' checked' : '') + ' onchange="axdToggleWidget(\'' + w.id + '\', this.checked)">' +
                    '<span class="axd-switch-slider"></span>' +
                '</label>' +
            '</label>';
        }).join('');
    }

    /* Chamadas globais */
    window.axdOpenCustomize = function() {
        var prefs = loadPrefs();
        buildWidgetGrid(prefs);
        document.getElementById('axd-modal').classList.add('open');
    };

    window.axdCloseCustomize = function() {
        document.getElementById('axd-modal').classList.remove('open');
    };

    window.axdToggleWidget = function(widgetId, checked) {
        // Atualiza visual do toggle label
        var label = document.getElementById('axd-toggle-' + widgetId);
        if (label) label.classList.toggle('active', checked);
        // Aplica em tempo real
        var prefs = loadPrefs();
        prefs[widgetId] = checked;
        savePrefs(prefs);
        applyPrefs(prefs);
    };

    window.axdSaveCustomize = function() {
        axdCloseCustomize();
        if (window.axiomShowToast) axiomShowToast('Widgets atualizados!');
    };

    // Aplica preferências ao carregar
    var currentPrefs = loadPrefs();
    applyPrefs(currentPrefs);

})();
</script>

<?php init_tail(); ?>
</body>
</html>
