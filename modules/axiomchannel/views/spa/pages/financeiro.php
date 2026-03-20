<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<span data-csrf="<?= $csrf_token ?>"></span>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
  <div><div class="ax-page-title">Financeiro</div><div class="ax-page-sub">Faturas e pagamentos recentes</div></div>
  <a href="<?= admin_url('invoices') ?>" style="background:#2D7A6B;color:#fff;border-radius:7px;padding:8px 14px;font-size:12px;font-weight:600;text-decoration:none">Ver todas →</a>
</div>
<?php
$status_colors=[1=>'#F5A623',2=>'#2D7A6B',3=>'#E53E3E',4=>'#805AD5',5=>'#94a3b8',6=>'#94a3b8'];
$status_labels=[1=>'Não pago',2=>'Pago',3=>'Vencido',4=>'Rascunho',5=>'Cancelado',6=>'Reembolsado'];
if(empty($invoices)):
?>
  <div style="text-align:center;padding:40px;color:var(--ax-text-muted)"><i class="fa fa-money" style="font-size:32px;display:block;margin-bottom:12px;opacity:.4"></i><div>Nenhuma fatura</div></div>
<?php else: ?>
  <div style="background:var(--ax-bg-card);border-radius:10px;border:1px solid var(--ax-border);overflow:hidden">
    <?php foreach($invoices as $i):
      $sc=$status_colors[$i->status]??'#94a3b8';
      $sl=$status_labels[$i->status]??'?';
    ?>
      <div onclick="location.href='<?= admin_url('invoices/'.$i->id) ?>'" style="display:flex;align-items:center;gap:10px;padding:10px 14px;border-bottom:1px solid var(--ax-border);cursor:pointer">
        <div style="flex:1">
          <div style="font-size:12px;font-weight:500;color:var(--ax-text-primary)"><?= htmlspecialchars($i->company??'Cliente') ?></div>
          <div style="font-size:10px;color:var(--ax-text-muted)">#<?= $i->id ?> · <?= $i->date ? date('d/m/Y',strtotime($i->date)) : '' ?></div>
        </div>
        <div style="text-align:right">
          <div style="font-size:13px;font-weight:700;color:var(--ax-text-primary)">R$<?= number_format($i->total,2,',','.') ?></div>
          <span style="font-size:9px;font-weight:600;color:<?= $sc ?>"><?= $sl ?></span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
