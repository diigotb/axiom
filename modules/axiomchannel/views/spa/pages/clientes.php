<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<span data-csrf="<?= $csrf_token ?>"></span>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
  <div><div class="ax-page-title">Clientes</div><div class="ax-page-sub">Clientes cadastrados no CRM</div></div>
  <a href="<?= admin_url('clients') ?>" style="background:#2D7A6B;color:#fff;border-radius:7px;padding:8px 14px;font-size:12px;font-weight:600;text-decoration:none">Ver todos →</a>
</div>
<?php if(empty($clients)): ?>
  <div style="text-align:center;padding:40px;color:var(--ax-text-muted)"><i class="fa fa-users" style="font-size:32px;display:block;margin-bottom:12px;opacity:.4"></i><div>Nenhum cliente</div></div>
<?php else: ?>
  <div style="background:var(--ax-bg-card);border-radius:10px;border:1px solid var(--ax-border);overflow:hidden">
    <?php foreach($clients as $c): ?>
      <div onclick="location.href='<?= admin_url('clients/'.$c->userid) ?>'" style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-bottom:1px solid var(--ax-border);cursor:pointer">
        <div style="width:32px;height:32px;border-radius:50%;background:#2D7A6B;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0">
          <?= strtoupper(substr($c->company??'C',0,1)) ?>
        </div>
        <div style="flex:1">
          <div style="font-size:12px;font-weight:500;color:var(--ax-text-primary)"><?= htmlspecialchars($c->company??'') ?></div>
          <div style="font-size:10px;color:var(--ax-text-muted)"><?= htmlspecialchars($c->phonenumber??'') ?></div>
        </div>
        <i class="fa fa-chevron-right" style="color:rgba(255,255,255,.2);font-size:11px"></i>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
