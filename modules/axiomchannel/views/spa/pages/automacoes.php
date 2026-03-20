<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<span data-csrf="<?= $csrf_token ?>"></span>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
  <div><div class="ax-page-title">Automações</div><div class="ax-page-sub">Mensagens automáticas por evento</div></div>
  <a href="<?= admin_url('axiomchannel/automations') ?>" style="background:#2D7A6B;color:#fff;border-radius:7px;padding:8px 14px;font-size:12px;font-weight:600;text-decoration:none">Gerenciar →</a>
</div>
<?php
$tl=['birthday'=>['Aniversário','#E53E3E','fa-birthday-cake'],'invoice'=>['Cobrança','#F5A623','fa-money'],
     'followup'=>['Follow-up','#4A90D9','fa-refresh'],'inactive'=>['Inativo','#805AD5','fa-user-times'],
     'appointment'=>['Agendamento','#2D7A6B','fa-calendar'],'satisfaction'=>['Satisfação','#D69E2E','fa-star']];
if(empty($automations)): ?>
  <div style="text-align:center;padding:40px;color:var(--ax-text-muted)"><i class="fa fa-bolt" style="font-size:32px;display:block;margin-bottom:12px;opacity:.4"></i><div>Nenhuma automação</div></div>
<?php else: foreach($automations as $a): $info=$tl[$a->type]??[$a->type,'#2D7A6B','fa-bolt']; ?>
  <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--ax-bg-card);border-radius:8px;border:1px solid var(--ax-border);margin-bottom:5px">
    <div style="width:30px;height:30px;border-radius:7px;background:<?= $info[1] ?>22;display:flex;align-items:center;justify-content:center">
      <i class="fa <?= $info[2] ?>" style="color:<?= $info[1] ?>;font-size:13px"></i>
    </div>
    <div style="flex:1">
      <div style="font-size:12px;font-weight:500;color:var(--ax-text-primary)"><?= $info[0] ?></div>
      <div style="font-size:10px;color:var(--ax-text-muted)">Disparar: <?= $a->trigger_days ?> dia(s) · Máx: <?= $a->max_attempts ?> tentativas</div>
    </div>
    <span style="padding:3px 8px;border-radius:5px;font-size:10px;font-weight:600;background:<?= $a->is_active?'rgba(45,122,107,.2)':'rgba(229,62,62,.15)' ?>;color:<?= $a->is_active?'#2D7A6B':'#E53E3E' ?>">
      <?= $a->is_active?'Ativa':'Inativa' ?>
    </span>
  </div>
<?php endforeach; endif; ?>
