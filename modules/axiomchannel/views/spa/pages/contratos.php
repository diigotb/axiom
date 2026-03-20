<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<span data-csrf="<?= $csrf_token ?>"></span>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
  <div><div class="ax-page-title">Contratos</div><div class="ax-page-sub">Contratos com assinatura digital</div></div>
  <a href="<?= admin_url('axiomchannel/contracts') ?>" style="background:#2D7A6B;color:#fff;border-radius:7px;padding:8px 14px;font-size:12px;font-weight:600;text-decoration:none">Ver todos →</a>
</div>
<?php
$sl=['draft'=>['#94a3b8','Rascunho'],'sent'=>['#3182CE','Enviado'],'viewed'=>['#805AD5','Visualizado'],'signed'=>['#38A169','Assinado'],'cancelled'=>['#E53E3E','Cancelado']];
if(empty($contracts)): ?>
  <div style="text-align:center;padding:40px;color:var(--ax-text-muted)">
    <i class="fa fa-file-text" style="font-size:32px;display:block;margin-bottom:12px;opacity:.4"></i>
    <div style="font-size:13px">Nenhum contrato</div>
  </div>
<?php else: foreach($contracts as $c): $si=$sl[$c->status??'draft']??['#94a3b8','?']; ?>
  <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--ax-bg-card);border-radius:8px;border:1px solid var(--ax-border);margin-bottom:5px">
    <i class="fa fa-file-text" style="color:<?= $si[0] ?>;font-size:16px;flex-shrink:0"></i>
    <div style="flex:1">
      <div style="font-size:12px;font-weight:500;color:var(--ax-text-primary)"><?= htmlspecialchars($c->title) ?></div>
      <div style="font-size:10px;color:var(--ax-text-muted)"><?= htmlspecialchars($c->cn??'') ?> · <?= $c->created_at ? date('d/m/Y',strtotime($c->created_at)) : '' ?></div>
    </div>
    <span style="padding:3px 8px;border-radius:5px;font-size:10px;font-weight:600;background:<?= $si[0] ?>22;color:<?= $si[0] ?>"><?= $si[1] ?></span>
  </div>
<?php endforeach; endif; ?>
