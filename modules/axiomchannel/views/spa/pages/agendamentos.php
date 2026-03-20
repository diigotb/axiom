<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<span data-csrf="<?= $csrf_token ?>"></span>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
  <div><div class="ax-page-title">Agendamentos</div><div class="ax-page-sub">Gerencie consultas e compromissos</div></div>
  <a href="<?= admin_url('axiomchannel/appointments') ?>" style="background:#2D7A6B;color:#fff;border-radius:7px;padding:8px 14px;font-size:12px;font-weight:600;text-decoration:none">Agenda completa →</a>
</div>
<?php if(empty($appointments)): ?>
  <div style="text-align:center;padding:40px;color:var(--ax-text-muted)">
    <i class="fa fa-calendar" style="font-size:32px;display:block;margin-bottom:12px;opacity:.4"></i>
    <div style="font-size:13px">Nenhum agendamento</div>
  </div>
<?php else: foreach($appointments as $a): ?>
  <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--ax-bg-card);border-radius:8px;border:1px solid var(--ax-border);border-left:3px solid #2D7A6B;margin-bottom:5px">
    <div style="flex-shrink:0;text-align:center;min-width:50px">
      <div style="font-size:14px;font-weight:700;color:#2D7A6B"><?= date('d',strtotime($a->start_datetime)) ?></div>
      <div style="font-size:9px;color:var(--ax-text-muted)"><?= date('M',strtotime($a->start_datetime)) ?></div>
    </div>
    <div style="flex:1">
      <div style="font-size:12px;font-weight:500;color:var(--ax-text-primary)"><?= htmlspecialchars($a->title) ?></div>
      <div style="font-size:10px;color:var(--ax-text-muted)"><?= htmlspecialchars($a->cn??'') ?> · <?= date('H:i',strtotime($a->start_datetime)) ?></div>
    </div>
    <?php $sc=['confirmed'=>['rgba(45,122,107,.2)','#2D7A6B'],'pending'=>['rgba(245,166,35,.15)','#F5A623']]; $si=$sc[$a->status??'']??['rgba(255,255,255,.07)','rgba(255,255,255,.4)']; ?>
    <span style="padding:3px 8px;border-radius:5px;font-size:10px;font-weight:600;background:<?= $si[0] ?>;color:<?= $si[1] ?>"><?= ucfirst($a->status??'') ?></span>
  </div>
<?php endforeach; endif; ?>
