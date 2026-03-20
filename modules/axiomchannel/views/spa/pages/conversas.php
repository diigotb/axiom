<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<span data-csrf="<?= $csrf_token ?>"></span>
<div class="ax-page-title">Todas as Conversas</div>
<div class="ax-page-sub">Gerencie todos os atendimentos</div>
<?php
$chan_icons=['whatsapp'=>'fa-whatsapp','facebook'=>'fa-facebook','instagram'=>'fa-instagram'];
$chan_colors=['whatsapp'=>'#25D366','facebook'=>'#1877F2','instagram'=>'#E1306C'];
$colors=['#2D7A6B','#4A90D9','#F5A623','#E1306C','#805AD5'];
if(empty($contacts)):
?>
  <div style="text-align:center;padding:40px;color:var(--ax-text-muted)">
    <i class="fa fa-comments" style="font-size:32px;display:block;margin-bottom:12px;opacity:.4"></i>
    <div style="font-size:13px">Nenhuma conversa ainda</div>
  </div>
<?php else: foreach($contacts as $c):
  $ini=strtoupper(substr($c->name??$c->phone_number??'C',0,1));
  $col=$colors[ord($ini)%5];
  $ch=$c->channel??'whatsapp';
?>
  <div onclick="location.href='<?= admin_url('axiomchannel/chat/'.$c->id) ?>'"
       style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--ax-bg-card);border-radius:8px;margin-bottom:5px;cursor:pointer;border:1px solid var(--ax-border)">
    <div style="width:36px;height:36px;border-radius:50%;background:<?= $col ?>;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0"><?= $ini ?></div>
    <div style="flex:1;min-width:0">
      <div style="font-size:12px;font-weight:500;color:var(--ax-text-primary)">
        <?= htmlspecialchars($c->name??$c->phone_number??'') ?>
        <i class="fa <?= $chan_icons[$ch]??'fa-whatsapp' ?>" style="font-size:10px;color:<?= $chan_colors[$ch]??'#25D366' ?>"></i>
      </div>
      <div style="font-size:11px;color:var(--ax-text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars(substr($c->lm??'',0,60)) ?></div>
    </div>
    <div style="text-align:right;flex-shrink:0">
      <div style="font-size:9px;color:var(--ax-text-muted);margin-bottom:4px"><?= $c->last_message_at ? date('H:i',strtotime($c->last_message_at)) : '' ?></div>
      <?php
      $sc=['open'=>['rgba(45,122,107,.2)','#2D7A6B','Aberta'],'pending'=>['rgba(245,166,35,.15)','#F5A623','Pendente']];
      $si=$sc[$c->status??'']??['rgba(255,255,255,.07)','rgba(255,255,255,.4)','Resolvida'];
      ?>
      <span style="background:<?= $si[0] ?>;color:<?= $si[1] ?>;padding:2px 6px;border-radius:3px;font-size:8px;font-weight:600"><?= $si[2] ?></span>
    </div>
  </div>
<?php endforeach; endif; ?>
