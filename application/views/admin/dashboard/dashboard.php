<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Estilos específicos da dashboard AXIOM — complementam axiom_admin.css */
.axd-wrap { padding: 20px 24px 30px; }

/* Banner */
.axd-banner {
    background: linear-gradient(135deg, var(--axiom-surface) 0%, #1a3548 100%);
    border: 1px solid var(--axiom-border);
    border-radius: 12px;
    padding: 22px 26px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    gap: 16px;
    flex-wrap: wrap;
}
.axd-banner-title {
    font-size: 21px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 4px;
}
.axd-banner-sub {
    font-size: 12px;
    color: var(--axiom-muted);
    margin: 0;
}
.axd-banner-right {
    display: flex;
    align-items: center;
    gap: 14px;
    font-size: 12px;
    color: var(--axiom-muted);
    flex-shrink: 0;
}
.axd-banner-right span { display: flex; align-items: center; gap: 5px; }

/* Grid de cards */
.axd-metrics { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }

/* Card AXIOM dark */
.axd-card {
    background: var(--axiom-surface);
    border: 1px solid var(--axiom-border);
    border-radius: 10px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    border-left: 3px solid var(--axiom-teal);
}
.axd-card.c-blue  { border-left-color: var(--axiom-blue); }
.axd-card.c-gold  { border-left-color: var(--axiom-gold); }
.axd-card.c-teal  { border-left-color: var(--axiom-teal); }
.axd-card-label   { font-size: 10px; text-transform: uppercase; letter-spacing: .06em; color: var(--axiom-muted); }
.axd-card-value   { font-size: 28px; font-weight: 700; color: #fff; line-height: 1; min-height: 34px; }
.axd-card-sub     { font-size: 11px; color: var(--axiom-muted); }
.axd-card-icon    { font-size: 20px; color: var(--axiom-teal); align-self: flex-end; opacity: .6; margin-top: -20px; }

/* Skeleton loading */
.axd-loading {
    display: inline-block;
    width: 90px;
    height: 28px;
    border-radius: 4px;
    background: linear-gradient(90deg, rgba(255,255,255,.06) 25%, rgba(255,255,255,.12) 50%, rgba(255,255,255,.06) 75%);
    background-size: 200% 100%;
    animation: axdSkel 1.2s ease-in-out infinite;
}
@keyframes axdSkel { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Bottom row */
.axd-bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.axd-widget {
    background: var(--axiom-surface);
    border: 1px solid var(--axiom-border);
    border-radius: 10px;
    padding: 18px 20px;
}
.axd-widget-title {
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,.7);
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 7px;
}
.axd-widget-title i { color: var(--axiom-teal); font-size: 13px; }

/* Últimas conversas */
.axd-conv-list { list-style: none; margin: 0; padding: 0; }
.axd-conv-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 0;
    border-bottom: 1px solid var(--axiom-border);
}
.axd-conv-item:last-child { border-bottom: none; }
.axd-conv-av {
    width: 34px; height: 34px; border-radius: 50%;
    background: var(--axiom-teal);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; color: #fff;
    flex-shrink: 0;
}
.axd-conv-body  { flex: 1; min-width: 0; }
.axd-conv-name  { font-size: 12px; font-weight: 600; color: #fff; }
.axd-conv-msg   { font-size: 11px; color: var(--axiom-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.axd-conv-meta  { font-size: 10px; color: var(--axiom-muted); text-align: right; white-space: nowrap; }

/* Responsive */
@media (max-width: 1100px) { .axd-metrics { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 900px)  { .axd-bottom  { grid-template-columns: 1fr; } }
@media (max-width: 600px)  { .axd-metrics { grid-template-columns: 1fr; } .axd-wrap { padding: 14px; } .axd-banner { padding: 16px; } }
</style>

<div id="wrapper">
<div class="content axd-wrap">

    <!-- Banner de boas-vindas -->
    <div class="axd-banner">
        <div>
            <h2 class="axd-banner-title" id="axd-greeting">Olá 👋</h2>
            <p class="axd-banner-sub">Visão geral do seu negócio em tempo real</p>
        </div>
        <div class="axd-banner-right">
            <span><i class="fa fa-calendar-o"></i> <?php echo date('d/m/Y'); ?></span>
            <span id="axd-weather"><i class="fa fa-circle-o-notch fa-spin"></i></span>
        </div>
    </div>

    <!-- 4 Cards de métricas -->
    <div class="axd-metrics">
        <div class="axd-card c-teal">
            <div class="axd-card-label">Conversas Hoje</div>
            <div class="axd-card-value"><span id="axd-conv_hoje" class="axd-loading"></span></div>
            <div class="axd-card-sub">mensagens recebidas</div>
            <div class="axd-card-icon"><i class="fa fa-comments"></i></div>
        </div>
        <div class="axd-card c-blue">
            <div class="axd-card-label">Faturamento Mês</div>
            <div class="axd-card-value"><span id="axd-fat_atual" class="axd-loading"></span></div>
            <div class="axd-card-sub">notas pagas</div>
            <div class="axd-card-icon"><i class="fa fa-dollar"></i></div>
        </div>
        <div class="axd-card c-gold">
            <div class="axd-card-label">IA Hoje</div>
            <div class="axd-card-value"><span id="axd-ia_pct" class="axd-loading"></span></div>
            <div class="axd-card-sub">respostas automáticas</div>
            <div class="axd-card-icon"><i class="fa fa-robot"></i></div>
        </div>
        <div class="axd-card">
            <div class="axd-card-label">Leads Hoje</div>
            <div class="axd-card-value"><span id="axd-leads_hoje" class="axd-loading"></span></div>
            <div class="axd-card-sub">novos leads</div>
            <div class="axd-card-icon"><i class="fa fa-users"></i></div>
        </div>
    </div>

    <!-- Chart + Últimas conversas -->
    <div class="axd-bottom">
        <div class="axd-widget">
            <div class="axd-widget-title"><i class="fa fa-bar-chart"></i> Mensagens — Últimos 7 dias</div>
            <canvas id="axd-chart" height="200"></canvas>
        </div>
        <div class="axd-widget">
            <div class="axd-widget-title"><i class="fa fa-clock-o"></i> Últimas Conversas</div>
            <ul class="axd-conv-list" id="axd-conv-list">
                <li style="color:var(--axiom-muted);font-size:12px;padding:10px 0">Carregando...</li>
            </ul>
        </div>
    </div>

</div><!-- .content -->
</div><!-- #wrapper -->

<!-- Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
(function () {
    'use strict';

    // Saudação GMT-3
    var now = new Date();
    var brt = new Date(now.getTime() + now.getTimezoneOffset() * 60000 - 3 * 3600000);
    var h   = brt.getHours();
    var lbl = h >= 5 && h < 12 ? 'Bom dia' : h >= 12 && h < 18 ? 'Boa tarde' : 'Boa noite';
    var nameEl = document.getElementById('axiom-greeting-name'); // span injetado pela topbar
    var staffName = nameEl ? nameEl.textContent.trim() : '<?php echo addslashes(get_staff_full_name(get_staff_user_id())); ?>';
    document.getElementById('axd-greeting').textContent = lbl + (staffName ? ', ' + staffName : '') + ' 👋';

    // Weather — aproveita cache do axiom_admin.js
    function showWeather(d) {
        var el = document.getElementById('axd-weather');
        if (!el || !d) return;
        var icons = {'fa-sun-o':'☀️','fa-cloud':'☁️','fa-umbrella':'🌧️','fa-bolt':'⛈️','fa-tint':'🌧️'};
        el.innerHTML = (icons[d.icon] || '🌡️') + ' ' + d.temp + '°C'
            + (d.city ? ' <small style="opacity:.6">' + d.city + '</small>' : '');
    }
    var wCache = sessionStorage.getItem('axiom_weather_v2');
    if (wCache) { try { showWeather(JSON.parse(wCache)); } catch(e) {} }
    else {
        var att = 0;
        var wi = setInterval(function () {
            var c = sessionStorage.getItem('axiom_weather_v2');
            if (c) { try { showWeather(JSON.parse(c)); } catch(e) {} clearInterval(wi); }
            if (++att >= 5) clearInterval(wi);
        }, 2000);
    }

    // AJAX — busca dados
    var base = window.AXIOM_BASE_URL || '/axiom/';
    fetch(base + 'admin/axiomchannel/admin_dashboard_data', {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(function(r){ return r.json(); })
    .then(function(data){

        // Cards
        ['conv_hoje','fat_atual','ia_pct','leads_hoje'].forEach(function(k){
            var el = document.getElementById('axd-' + k);
            if (el) { el.textContent = data[k] !== undefined ? data[k] : '—'; el.classList.remove('axd-loading'); }
        });

        // Últimas conversas
        var list = document.getElementById('axd-conv-list');
        if (list) {
            if (data.ultimas && data.ultimas.length) {
                list.innerHTML = data.ultimas.map(function(c){
                    var ini = (c.name || '?').charAt(0).toUpperCase();
                    return '<li class="axd-conv-item">'
                        + '<div class="axd-conv-av">' + ini + '</div>'
                        + '<div class="axd-conv-body">'
                            + '<div class="axd-conv-name">' + esc(c.name) + '</div>'
                            + '<div class="axd-conv-msg">' + esc(c.body || '—') + '</div>'
                        + '</div>'
                        + '<div class="axd-conv-meta">' + esc(c.time) + '<br><small>' + esc(c.channel) + '</small></div>'
                    + '</li>';
                }).join('');
            } else {
                list.innerHTML = '<li style="color:var(--axiom-muted);font-size:12px;padding:10px 0">Nenhuma conversa ainda.</li>';
            }
        }

        // Chart
        var ctx = document.getElementById('axd-chart');
        if (ctx && data.chart_labels) {
            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data.chart_labels,
                    datasets: [
                        { label:'WhatsApp', data: data.chart_wa,   backgroundColor:'rgba(45,122,107,.75)', borderRadius:4 },
                        { label:'Meta',     data: data.chart_meta, backgroundColor:'rgba(74,144,217,.65)', borderRadius:4 }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position:'bottom', labels:{ color:'rgba(255,255,255,.5)', font:{size:11} } }
                    },
                    scales: {
                        x: { grid:{color:'rgba(255,255,255,.05)'}, ticks:{color:'rgba(255,255,255,.4)',font:{size:11}} },
                        y: { beginAtZero:true, grid:{color:'rgba(255,255,255,.05)'}, ticks:{color:'rgba(255,255,255,.4)',font:{size:11},precision:0} }
                    }
                }
            });
        }
    })
    .catch(function(){
        ['conv_hoje','fat_atual','ia_pct','leads_hoje'].forEach(function(k){
            var el = document.getElementById('axd-' + k);
            if (el) { el.textContent = '—'; el.classList.remove('axd-loading'); }
        });
    });

    function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
})();
</script>

<?php init_tail(); ?>
</body>
</html>
