<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
/* Oculta todos os widgets padrão do Perfex que vêm após o AXIOM dashboard */
.axd-wrap ~ * { display: none !important; }
.axd-wrap { font-family: inherit; padding: 0 0 24px; width: 100%; box-sizing: border-box; }

/* Banner */
.axd-banner {
    background: #162D3D;
    border-radius: 10px;
    padding: 20px 24px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid rgba(255,255,255,0.08);
}
.axd-banner-left { flex: 1; }
.axd-greeting { font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px; }
.axd-date     { font-size: 12px; color: rgba(255,255,255,0.4); margin-bottom: 16px; }
.axd-metrics  { display: flex; gap: 24px; }
.axd-metric-val { font-size: 22px; font-weight: 700; color: #fff; line-height: 1; }
.axd-metric-val.gold { color: #F5A623; }
.axd-metric-val.teal { color: #2D7A6B; }
.axd-metric-lbl { font-size: 11px; color: rgba(255,255,255,0.4); margin-top: 3px; }
.axd-ia-box {
    background: rgba(45,122,107,0.15);
    border: 1px solid rgba(45,122,107,0.3);
    border-radius: 8px;
    padding: 14px 20px;
    text-align: center;
    min-width: 90px;
}
.axd-ia-num { font-size: 28px; font-weight: 700; color: #2D7A6B; line-height: 1; }
.axd-ia-lbl { font-size: 10px; color: rgba(255,255,255,0.4); margin-top: 4px; }

/* Cards row */
.axd-cards { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 12px; }
.axd-card {
    background: #162D3D;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.07);
    padding: 14px 16px;
    border-top: 3px solid #2D7A6B;
}
.axd-card.c-blue   { border-top-color: #378ADD; }
.axd-card.c-yellow { border-top-color: #F5A623; }
.axd-card.c-red    { border-top-color: #E74C3C; }
.axd-card-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.06em; color: rgba(255,255,255,0.35); margin-bottom: 6px; }
.axd-card-val   { font-size: 26px; font-weight: 700; color: #fff; line-height: 1; }
.axd-card-sub   { font-size: 11px; margin-top: 6px; }
.axd-card-sub.up     { color: #2D7A6B; }
.axd-card-sub.yellow { color: #F5A623; }
.axd-card-sub.red    { color: #E74C3C; }
.axd-card-bar { height: 3px; border-radius: 2px; margin-top: 8px; background: rgba(255,255,255,0.08); }
.axd-card-bar-fill { height: 100%; border-radius: 2px; }

/* Bottom row */
.axd-bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.axd-panel {
    background: #162D3D;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.07);
    padding: 16px;
}
.axd-panel-title { font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.7); margin-bottom: 4px; }
.axd-panel-sub   { font-size: 11px; color: rgba(255,255,255,0.3); margin-bottom: 12px; }
.axd-conv-item { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
.axd-conv-item:last-child { border-bottom: none; }
.axd-conv-av { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0; }
.axd-conv-info { flex: 1; min-width: 0; }
.axd-conv-name { font-size: 12px; color: rgba(255,255,255,0.85); }
.axd-conv-msg  { font-size: 11px; color: rgba(255,255,255,0.35); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.axd-tag { font-size: 10px; padding: 2px 6px; border-radius: 3px; white-space: nowrap; }
.axd-tag.ia   { background: rgba(45,122,107,0.2); color: #2D7A6B; }
.axd-tag.pend { background: rgba(245,166,35,0.15); color: #F5A623; }

@media (max-width: 900px) {
    .axd-cards  { grid-template-columns: repeat(2,1fr); }
    .axd-bottom { grid-template-columns: 1fr; }
    .axd-banner { flex-direction: column; gap: 14px; }
}
</style>

<div class="axd-wrap">

  <!-- Banner -->
  <div class="axd-banner">
    <div class="axd-banner-left">
      <div class="axd-greeting" id="axd-greeting">Bom dia, <?= htmlspecialchars($ax_staff->firstname ?? 'Usuário') ?>! 👋</div>
      <div class="axd-date" id="axd-date"></div>
      <div class="axd-metrics">
        <div>
          <div class="axd-metric-val" id="axd-m-conversas">–</div>
          <div class="axd-metric-lbl">Conversas hoje</div>
        </div>
        <div>
          <div class="axd-metric-val gold" id="axd-m-faturamento">–</div>
          <div class="axd-metric-lbl">Faturamento mês</div>
        </div>
        <div>
          <div class="axd-metric-val teal" id="axd-m-ia">–</div>
          <div class="axd-metric-lbl">IA resolveu</div>
        </div>
      </div>
    </div>
    <div class="axd-ia-box">
      <div class="axd-ia-num" id="axd-ia-hoje">–</div>
      <div class="axd-ia-lbl">atendidos pela IA hoje</div>
    </div>
  </div>

  <!-- 4 Cards -->
  <div class="axd-cards">
    <div class="axd-card">
      <div class="axd-card-label">Conversas</div>
      <div class="axd-card-val" id="axd-c-conv">0</div>
      <div class="axd-card-sub up" id="axd-c-conv-sub">carregando...</div>
      <div class="axd-card-bar"><div class="axd-card-bar-fill" style="width:60%;background:#2D7A6B"></div></div>
    </div>
    <div class="axd-card c-blue">
      <div class="axd-card-label">Leads Novos</div>
      <div class="axd-card-val" id="axd-c-leads">0</div>
      <div class="axd-card-sub yellow" id="axd-c-leads-sub">carregando...</div>
      <div class="axd-card-bar"><div class="axd-card-bar-fill" style="width:40%;background:#378ADD"></div></div>
    </div>
    <div class="axd-card c-yellow">
      <div class="axd-card-label">Agendamentos</div>
      <div class="axd-card-val" id="axd-c-agend">0</div>
      <div class="axd-card-sub" style="color:rgba(255,255,255,0.35)" id="axd-c-agend-sub">hoje · na semana</div>
      <div class="axd-card-bar"><div class="axd-card-bar-fill" style="width:25%;background:#F5A623"></div></div>
    </div>
    <div class="axd-card c-red">
      <div class="axd-card-label">Contratos</div>
      <div class="axd-card-val" id="axd-c-contr">0</div>
      <div class="axd-card-sub red" id="axd-c-contr-sub">aguardando assinatura</div>
      <div class="axd-card-bar"><div class="axd-card-bar-fill" style="width:30%;background:#E74C3C"></div></div>
    </div>
  </div>

  <!-- Bottom: gráfico + últimas conversas -->
  <div class="axd-bottom">
    <div class="axd-panel">
      <div class="axd-panel-title">Conversas — últimos 7 dias</div>
      <div class="axd-panel-sub">WhatsApp · Facebook · Instagram</div>
      <canvas id="axd-chart" height="120"></canvas>
    </div>
    <div class="axd-panel">
      <div class="axd-panel-title">Últimas conversas</div>
      <div class="axd-panel-sub">abertas agora</div>
      <div id="axd-conv-list">
        <div style="color:rgba(255,255,255,0.3);font-size:12px">Carregando...</div>
      </div>
    </div>
  </div>

</div>

<script>
(function(){
    // Saudação GMT-3
    var now = new Date();
    var gmt3 = new Date(now.getTime() + (now.getTimezoneOffset()*60000) + (-3*3600000));
    var h = gmt3.getUTCHours();
    var greet = h < 12 ? 'Bom dia' : h < 18 ? 'Boa tarde' : 'Boa noite';
    var el = document.getElementById('axd-greeting');
    if(el) el.textContent = greet + ', <?= addslashes(htmlspecialchars($ax_staff->firstname ?? 'Usuário')) ?>! 👋';

    var dias  = ['Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado'];
    var meses = ['janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'];
    var d = document.getElementById('axd-date');
    if(d) d.textContent = dias[gmt3.getUTCDay()] + ', ' + gmt3.getUTCDate() + ' de ' + meses[gmt3.getUTCMonth()] + ' · Seu negócio está crescendo';

    // Busca métricas
    fetch('<?= admin_url('axiomchannel/get_dashboard_metrics') ?>', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
        body: JSON.stringify({widgets:['conversas_hoje','faturamento','leads','contratos_pendentes','ultimas_conversas','agendamentos']})
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
        if(!data) return;

        // Banner métricas
        var conv = data.conversas_hoje || 0;
        var fat  = data.faturamento    || 0;
        var ia   = data.ia_resolveu    || 0;

        setEl('axd-m-conversas', conv);
        setEl('axd-m-faturamento', 'R$ ' + formatNum(fat));
        setEl('axd-m-ia', ia + '%');
        setEl('axd-ia-hoje', data.ia_hoje || 0);

        // Cards
        setEl('axd-c-conv',  conv);
        setEl('axd-c-conv-sub', (data.conv_variacao || '↑ 0%') + ' vs ontem');
        setEl('axd-c-leads', data.leads || 0);
        setEl('axd-c-leads-sub', (data.leads_urgentes || 0) + ' urgentes');
        setEl('axd-c-agend', data.agendamentos || 0);
        setEl('axd-c-contr', data.contratos_pendentes || 0);

        // Gráfico
        if(data.chart_labels && data.chart_values) {
            renderChart(data.chart_labels, data.chart_values);
        }

        // Últimas conversas
        if(data.ultimas_conversas && data.ultimas_conversas.length) {
            renderConvs(data.ultimas_conversas);
        }
    })
    .catch(function(){ /* silencioso */ });

    function setEl(id, val) {
        var e = document.getElementById(id);
        if(e) e.textContent = val;
    }

    function formatNum(n) {
        n = parseFloat(n) || 0;
        if(n >= 1000) return (n/1000).toFixed(1) + 'k';
        return n.toFixed(0);
    }

    var colors = ['#2D7A6B','#378ADD','#9B59B6','#E74C3C','#F5A623'];
    function renderConvs(list) {
        var html = '';
        list.slice(0,5).forEach(function(c, i){
            var initials = ((c.name||'?').split(' ').map(function(w){ return w[0]; }).join('').substring(0,2)).toUpperCase();
            var tag = c.ia_handled ? '<span class="axd-tag ia">IA</span>' : '<span class="axd-tag pend">Pend.</span>';
            html += '<div class="axd-conv-item">'
                  + '<div class="axd-conv-av" style="background:'+colors[i%colors.length]+'">'+initials+'</div>'
                  + '<div class="axd-conv-info">'
                  + '<div class="axd-conv-name">' + (c.name||'Contato') + '</div>'
                  + '<div class="axd-conv-msg">' + (c.last_message||'...') + '</div>'
                  + '</div>' + tag + '</div>';
        });
        var el = document.getElementById('axd-conv-list');
        if(el) el.innerHTML = html || '<div style="color:rgba(255,255,255,0.3);font-size:12px">Sem conversas abertas</div>';
    }

    function renderChart(labels, values) {
        var canvas = document.getElementById('axd-chart');
        if(!canvas || !window.Chart) return;
        new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Conversas',
                    data: values,
                    borderColor: '#2D7A6B',
                    backgroundColor: 'rgba(45,122,107,0.1)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#2D7A6B',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: 'rgba(255,255,255,0.3)', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.05)' } },
                    y: { ticks: { color: 'rgba(255,255,255,0.3)', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,0.05)' } }
                }
            }
        });
    }

    // Carrega Chart.js se não existir
    if(!window.Chart) {
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js';
        document.head.appendChild(s);
    }
})();
</script>
