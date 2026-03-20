<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<span data-csrf="<?= $csrf_token ?>"></span>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
  <div><div class="ax-page-title">CRM Pipeline</div><div class="ax-page-sub">Funil de vendas</div></div>
  <a href="<?= admin_url('axiomchannel/pipeline') ?>" style="background:#2D7A6B;color:#fff;border-radius:7px;padding:8px 14px;font-size:12px;font-weight:600;text-decoration:none">
    <i class="fa fa-expand"></i> Kanban completo
  </a>
</div>
<?php if(empty($stages)): ?>
  <div style="text-align:center;padding:40px;color:var(--ax-text-muted)">
    <i class="fa fa-columns" style="font-size:32px;display:block;margin-bottom:12px;opacity:.4"></i>
    <div style="font-size:13px">Nenhum pipeline criado</div>
    <div style="margin-top:8px"><a href="<?= admin_url('axiomchannel/pipeline_wizard') ?>" style="color:#2D7A6B;font-size:12px">Criar pipeline com IA →</a></div>
  </div>
<?php else: ?>
<div style="display:flex;gap:8px;overflow-x:auto;padding-bottom:8px">
  <?php foreach($stages as $s):
    $leads=$leads_by_stage[$s->id]??[];
    $cor=$s->color?:'#2D7A6B';
  ?>
  <div style="min-width:160px;background:var(--ax-bg-card);border-radius:10px;padding:12px;border:1px solid var(--ax-border);flex-shrink:0">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
      <div style="font-size:11px;font-weight:600;color:<?= $cor ?>"><?= htmlspecialchars($s->name) ?></div>
      <span style="background:rgba(255,255,255,.08);padding:1px 6px;border-radius:8px;font-size:10px;color:var(--ax-text-muted)"><?= count($leads) ?></span>
    </div>
    <?php foreach(array_slice($leads,0,3) as $l): ?>
      <div style="background:rgba(255,255,255,.04);border-radius:5px;padding:7px;margin-bottom:3px;border-left:2px solid <?= $cor ?>">
        <div style="font-size:11px;color:var(--ax-text-primary);font-weight:500"><?= htmlspecialchars($l->name??'Lead') ?></div>
        <div style="font-size:9px;color:var(--ax-text-muted)"><?= $l->created_at ? date('d/m',strtotime($l->created_at)) : '' ?></div>
      </div>
    <?php endforeach; ?>
    <?php if(count($leads)>3): ?>
      <div style="font-size:10px;color:var(--ax-text-muted);text-align:center;padding:4px">+<?= count($leads)-3 ?> mais</div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
