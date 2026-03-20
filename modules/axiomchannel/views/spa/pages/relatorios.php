<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<span data-csrf="<?= $csrf_token ?>"></span>
<div class="ax-page-title">Relatórios</div>
<div class="ax-page-sub">Análises e métricas do sistema</div>
<div style="text-align:center;padding:40px;color:var(--ax-text-muted)">
  <i class="fa fa-bar-chart" style="font-size:40px;display:block;margin-bottom:16px;opacity:.3"></i>
  <div style="font-size:14px;margin-bottom:8px">Relatórios em breve</div>
  <div style="font-size:12px;opacity:.7">Acesse os relatórios nativos do Perfex CRM:</div>
  <div style="margin-top:16px;display:flex;flex-direction:column;gap:8px;max-width:300px;margin-left:auto;margin-right:auto">
    <a href="<?= admin_url('reports') ?>" style="padding:10px;background:var(--ax-bg-card);border:1px solid var(--ax-border);border-radius:8px;color:var(--ax-text-primary);text-decoration:none;font-size:12px">
      <i class="fa fa-bar-chart" style="color:#2D7A6B;margin-right:8px"></i>Relatórios do CRM
    </a>
    <a href="<?= admin_url('reports/income_vs_expense') ?>" style="padding:10px;background:var(--ax-bg-card);border:1px solid var(--ax-border);border-radius:8px;color:var(--ax-text-primary);text-decoration:none;font-size:12px">
      <i class="fa fa-money" style="color:#2D7A6B;margin-right:8px"></i>Receita vs Despesa
    </a>
  </div>
</div>
