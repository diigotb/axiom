<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<span data-csrf="<?= $csrf_token ?>"></span>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
  <div>
    <div class="ax-page-title">Leads</div>
    <div class="ax-page-sub">Todos os contatos e leads do sistema</div>
  </div>
  <span style="font-size:11px;color:var(--ax-text-muted)"><?= count($contacts??[]) ?> contatos</span>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:10px">
<?php
$colors     = ['#2D7A6B','#4A90D9','#F5A623','#E1306C','#805AD5','#E53E3E'];
$chan_icons  = [
    'whatsapp'  => ['fa-whatsapp',  '#25D366'],
    'facebook'  => ['fa-facebook',  '#1877F2'],
    'instagram' => ['fa-instagram', '#E1306C'],
];
foreach ($contacts ?? [] as $c):
    $ini   = strtoupper(substr($c->name ?? $c->phone_number ?? 'C', 0, 1));
    $col   = $colors[ord($ini) % 6];
    $ch    = $c->channel ?? 'whatsapp';
    $chi   = $chan_icons[$ch] ?? ['fa-whatsapp', '#25D366'];
    $s_bg  = $c->status === 'open'    ? 'rgba(45,122,107,.2)'   : ($c->status === 'pending' ? 'rgba(245,166,35,.15)' : 'rgba(255,255,255,.07)');
    $s_col = $c->status === 'open'    ? '#2D7A6B'               : ($c->status === 'pending' ? '#F5A623'             : 'rgba(255,255,255,.4)');
    $s_lbl = $c->status === 'open'    ? 'Aberta'                : ($c->status === 'pending' ? 'Pendente'            : 'Resolvida');
?>
<div onclick="axNav('conversas')" style="background:var(--ax-bg-card);border-radius:12px;padding:14px;border:1px solid var(--ax-border);cursor:pointer;transition:all .15s;position:relative;overflow:hidden">
  <div style="position:absolute;top:-15px;right:-15px;width:60px;height:60px;border-radius:50%;background:<?= $col ?>18"></div>
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
    <div style="width:38px;height:38px;border-radius:50%;background:<?= $col ?>;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0"><?= $ini ?></div>
    <div style="flex:1;min-width:0">
      <div style="font-size:13px;font-weight:500;color:var(--ax-text-primary)"><?= htmlspecialchars($c->name ?? $c->phone_number ?? '') ?> <i class="fa <?= $chi[0] ?>" style="color:<?= $chi[1] ?>"></i></div>
      <div style="font-size:10px;color:var(--ax-text-muted)"><?= htmlspecialchars($c->phone_number ?? '') ?></div>
    </div>
    <span style="padding:3px 8px;border-radius:20px;font-size:9px;font-weight:600;background:<?= $s_bg ?>;color:<?= $s_col ?>"><?= $s_lbl ?></span>
  </div>
  <div style="font-size:11px;color:var(--ax-text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:8px"><?= htmlspecialchars(substr($c->lm ?? 'Sem mensagens', 0, 50)) ?></div>
  <div style="display:flex;align-items:center;justify-content:space-between">
    <span style="font-size:9px;color:var(--ax-text-muted)"><?= $c->last_message_at ? date('d/m H:i', strtotime($c->last_message_at)) : '' ?></span>
    <span style="font-size:9px;color:var(--ax-text-muted)"><?= (int)($c->msg_count ?? 0) ?> msgs</span>
  </div>
</div>
<?php endforeach; ?>
<?php if (empty($contacts)): ?>
  <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--ax-text-muted)">
    <i class="fa fa-users" style="font-size:32px;display:block;margin-bottom:12px;opacity:.4"></i>
    <div style="font-size:13px">Nenhum lead ainda</div>
  </div>
<?php endif; ?>
</div>
