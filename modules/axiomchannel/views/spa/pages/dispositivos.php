<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<span data-csrf="<?= $csrf_token ?>"></span>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
  <div><div class="ax-page-title">Dispositivos</div><div class="ax-page-sub">Conexões WhatsApp ativas</div></div>
  <a href="<?= admin_url('axiomchannel/devices') ?>" style="background:#2D7A6B;color:#fff;border-radius:7px;padding:8px 14px;font-size:12px;font-weight:600;text-decoration:none">Gerenciar →</a>
</div>
<?php if(empty($devices)): ?>
  <div style="text-align:center;padding:40px;color:var(--ax-text-muted)">
    <i class="fa fa-mobile" style="font-size:32px;display:block;margin-bottom:12px;opacity:.4"></i>
    <div style="font-size:13px">Nenhum dispositivo</div>
    <div style="margin-top:8px"><a href="<?= admin_url('axiomchannel/devices') ?>" style="color:#2D7A6B;font-size:12px">Adicionar dispositivo →</a></div>
  </div>
<?php else: foreach($devices as $d): ?>
  <div style="display:flex;align-items:center;gap:12px;padding:14px;background:var(--ax-bg-card);border-radius:10px;border:1px solid var(--ax-border);margin-bottom:8px">
    <div style="width:10px;height:10px;border-radius:50%;background:<?= $d->status==='connected'?'#2D7A6B':'#E53E3E' ?>;flex-shrink:0"></div>
    <div style="flex:1">
      <div style="font-size:13px;font-weight:600;color:var(--ax-text-primary)"><?= htmlspecialchars($d->name) ?></div>
      <div style="font-size:11px;color:var(--ax-text-muted)"><?= htmlspecialchars($d->phone_number??$d->instance_name??'') ?></div>
    </div>
    <span style="padding:4px 10px;border-radius:6px;font-size:11px;font-weight:600;background:<?= $d->status==='connected'?'rgba(45,122,107,.2)':'rgba(229,62,62,.15)' ?>;color:<?= $d->status==='connected'?'#2D7A6B':'#E53E3E' ?>">
      <?= $d->status==='connected'?'Conectado':'Desconectado' ?>
    </span>
    <a href="<?= admin_url('axiomchannel/devices') ?>" style="color:rgba(255,255,255,.4);font-size:12px;text-decoration:none"><i class="fa fa-cog"></i></a>
  </div>
<?php endforeach; endif; ?>
