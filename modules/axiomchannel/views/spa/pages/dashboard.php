<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<span data-csrf="<?= $csrf_token ?>"></span>
<div class="ax-page-title" id="ax-greet-title" style="display:flex;align-items:center;gap:8px">
  Olá! 👋
  <?php if(!empty($is_admin)): ?>
    <span style="background:rgba(45,122,107,.15);color:#2D7A6B;border:1px solid rgba(45,122,107,.3);border-radius:20px;padding:2px 10px;font-size:10px;font-weight:600">&#x1F451; Admin</span>
  <?php else: ?>
    <span style="background:rgba(74,144,217,.12);color:#4A90D9;border:1px solid rgba(74,144,217,.25);border-radius:20px;padding:2px 10px;font-size:10px;font-weight:600">&#x1F4F1; Atendente</span>
  <?php endif; ?>
</div>
<div class="ax-page-sub" id="ax-greet-sub">Carregando...</div>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:14px">
  <?php
  $stats = [
    ['Faturamento do mês', 'R$'.number_format($fat_atual??0,0,',','.'), '#2D7A6B', (($fat_pct??0)>=0?'↑':'↓').abs($fat_pct??0).'% vs mês anterior'],
    ['Conversas hoje', $conv_hoje??0, '#4A90D9', ($ia_hoje??0).' pela IA'],
    ['Agendamentos hoje', count($ag_hoje??[]), '#F5A623', count($ag_hoje??[]).' hoje'],
    ['Leads novos hoje', $leads_hoje??0, '#E53E3E', 'Hoje'],
  ];
  foreach($stats as $s): ?>
  <div style="background:var(--ax-bg-card);border-radius:10px;padding:14px;border:1px solid var(--ax-border);position:relative;overflow:hidden">
    <div style="position:absolute;top:0;left:0;right:0;height:2px;background:<?= $s[2] ?>"></div>
    <div style="font-size:10px;color:var(--ax-text-muted)"><?= $s[0] ?></div>
    <div style="font-size:24px;font-weight:700;color:<?= $s[2] ?>;margin:6px 0 3px"><?= $s[1] ?></div>
    <div style="font-size:10px;color:var(--ax-text-muted)"><?= $s[3] ?></div>
  </div>
  <?php endforeach; ?>
</div>

<div style="display:grid;grid-template-columns:1.4fr 1fr;gap:10px">
  <div>
    <div style="background:var(--ax-bg-card);border-radius:10px;padding:14px;border:1px solid var(--ax-border);margin-bottom:10px">
      <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--ax-text-muted);margin-bottom:10px;display:flex;align-items:center;justify-content:space-between">
        Conversas recentes
        <span style="color:#2D7A6B;cursor:pointer;font-weight:400;text-transform:none;letter-spacing:0" onclick="axNav('conversas')">Ver todas →</span>
      </div>
      <?php if(empty($recentes)): ?>
        <div style="text-align:center;padding:16px;font-size:12px;color:var(--ax-text-muted)">Nenhuma conversa ainda</div>
      <?php else: foreach($recentes as $c):
        $chan_icons=['whatsapp'=>'fa-whatsapp','facebook'=>'fa-facebook','instagram'=>'fa-instagram'];
        $chan_colors=['whatsapp'=>'#25D366','facebook'=>'#1877F2','instagram'=>'#E1306C'];
        $ch=$c->channel??'whatsapp';
        $ini=strtoupper(substr($c->name??$c->phone_number??'C',0,1));
        $colors=['#2D7A6B','#4A90D9','#F5A623','#E1306C','#805AD5'];
        $col=$colors[ord($ini)%5];
      ?>
        <div onclick="location.href='<?= admin_url('axiomchannel/chat/'.$c->id) ?>'" style="display:flex;align-items:center;gap:8px;padding:7px 0;border-bottom:1px solid var(--ax-border);cursor:pointer">
          <div style="width:28px;height:28px;border-radius:50%;background:<?= $col ?>;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;flex-shrink:0"><?= $ini ?></div>
          <div style="flex:1;min-width:0">
            <div style="font-size:12px;font-weight:500;color:var(--ax-text-primary)"><?= htmlspecialchars($c->name??$c->phone_number??'') ?> <i class="fa <?= $chan_icons[$ch]??'fa-whatsapp' ?>" style="color:<?= $chan_colors[$ch]??'#25D366' ?>"></i></div>
            <div style="font-size:10px;color:var(--ax-text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars(substr($c->lm??'',0,50)) ?></div>
          </div>
          <?php if($c->status==='open'): ?>
            <span style="background:rgba(45,122,107,.2);color:#2D7A6B;padding:2px 6px;border-radius:3px;font-size:9px;font-weight:600">Aberta</span>
          <?php endif; ?>
        </div>
      <?php endforeach; endif; ?>
    </div>

    <div style="background:var(--ax-bg-card);border-radius:10px;padding:14px;border:1px solid var(--ax-border)">
      <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--ax-text-muted);margin-bottom:10px;display:flex;align-items:center;justify-content:space-between">
        Pipeline
        <span style="color:#2D7A6B;cursor:pointer;font-weight:400;text-transform:none;letter-spacing:0" onclick="axNav('pipeline')">Ver →</span>
      </div>
      <?php if(empty($pipeline_stages)): ?>
        <div style="font-size:11px;color:var(--ax-text-muted);text-align:center;padding:10px">Nenhum pipeline configurado</div>
      <?php else:
        $max_t = max(array_map(fn($s)=>$s->total, $pipeline_stages)) ?: 1;
        foreach($pipeline_stages as $s): $pct=($s->total/$max_t)*100; ?>
        <div style="display:flex;align-items:center;gap:8px;padding:4px 0">
          <div style="font-size:10px;color:var(--ax-text-muted);min-width:100px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($s->name) ?></div>
          <div style="flex:1;height:4px;background:rgba(255,255,255,.06);border-radius:2px;overflow:hidden">
            <div style="width:<?= $pct ?>%;height:100%;background:<?= $s->color?:'#2D7A6B' ?>;border-radius:2px"></div>
          </div>
          <div style="font-size:10px;font-weight:600;color:var(--ax-text-primary);min-width:20px;text-align:right"><?= $s->total ?></div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:10px">
    <div style="background:var(--ax-bg-card);border-radius:10px;padding:14px;border:1px solid var(--ax-border)">
      <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--ax-text-muted);margin-bottom:10px;display:flex;align-items:center;justify-content:space-between">
        Agenda hoje
        <span style="color:#2D7A6B;cursor:pointer;font-weight:400;text-transform:none;letter-spacing:0" onclick="axNav('agendamentos')">Ver →</span>
      </div>
      <?php if(empty($ag_hoje)): ?>
        <div style="text-align:center;padding:10px;font-size:11px;color:var(--ax-text-muted)">Sem agendamentos hoje</div>
      <?php else: foreach($ag_hoje as $ag): ?>
        <div style="background:rgba(255,255,255,.03);border-radius:7px;padding:8px 10px;margin-bottom:5px;border-left:3px solid #2D7A6B">
          <div style="font-size:9px;font-weight:600;color:var(--ax-text-muted)"><?= date('H\hi',strtotime($ag->start_datetime)) ?></div>
          <div style="font-size:12px;font-weight:500;color:var(--ax-text-primary)"><?= htmlspecialchars($ag->title) ?></div>
          <div style="font-size:10px;color:var(--ax-text-muted)"><?= htmlspecialchars($ag->cn??'') ?></div>
        </div>
      <?php endforeach; endif; ?>
    </div>

    <div style="background:var(--ax-bg-card);border-radius:10px;padding:14px;border:1px solid var(--ax-border)">
      <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--ax-text-muted);margin-bottom:10px">Dispositivos</div>
      <?php foreach($devices??[] as $d): ?>
        <div style="display:flex;align-items:center;gap:8px;padding:7px 8px;background:rgba(255,255,255,.03);border-radius:6px;margin-bottom:4px">
          <div style="width:7px;height:7px;border-radius:50%;background:<?= $d->status==='connected'?'#2D7A6B':'#E53E3E' ?>"></div>
          <div style="flex:1;font-size:12px;color:var(--ax-text-primary);font-weight:500"><?= htmlspecialchars($d->name) ?></div>
          <span style="padding:2px 7px;border-radius:3px;font-size:9px;font-weight:600;background:<?= $d->status==='connected'?'rgba(45,122,107,.2)':'rgba(229,62,62,.15)' ?>;color:<?= $d->status==='connected'?'#2D7A6B':'#E53E3E' ?>">
            <?= $d->status==='connected'?'Online':'Offline' ?>
          </span>
        </div>
      <?php endforeach; ?>
      <?php if(empty($devices)): ?>
        <div style="font-size:11px;color:var(--ax-text-muted);text-align:center;padding:8px">Nenhum dispositivo</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
(function(){
  var h=new Date().getHours();
  var n='<?= explode(' ',get_staff_full_name())[0] ?>';
  var g=h<12?'Bom dia':h<18?'Boa tarde':'Boa noite';
  var t=document.getElementById('ax-greet-title');
  if(t)t.textContent=g+', '+n+'! 👋';
  var days=['Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado'];
  var months=['janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'];
  var d=new Date();
  var s=document.getElementById('ax-greet-sub');
  if(s)s.textContent=days[d.getDay()]+', '+d.getDate()+' de '+months[d.getMonth()]+' de '+d.getFullYear();
})();
</script>
