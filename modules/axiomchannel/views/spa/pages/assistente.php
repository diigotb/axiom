<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<span data-csrf="<?= $csrf_token ?>"></span>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
  <div><div class="ax-page-title">Assistente IA</div><div class="ax-page-sub">Configure a inteligência artificial</div></div>
  <a href="<?= admin_url('axiomchannel/assistant') ?>" style="background:#2D7A6B;color:#fff;border-radius:7px;padding:8px 14px;font-size:12px;font-weight:600;text-decoration:none">
    <i class="fa fa-cog"></i> Configuração completa
  </a>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
  <div style="background:var(--ax-bg-card);border-radius:10px;padding:14px;border:1px solid var(--ax-border)">
    <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--ax-text-muted);margin-bottom:12px">Status por dispositivo</div>
    <?php foreach($devices??[] as $d):
      $ast=$this->axiomchannel_model->get_assistant($d->id);
    ?>
      <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:rgba(255,255,255,.03);border-radius:8px;margin-bottom:6px">
        <div style="width:8px;height:8px;border-radius:50%;background:<?= ($ast&&$ast->is_active)?'#2D7A6B':'#E53E3E' ?>"></div>
        <div style="flex:1">
          <div style="font-size:12px;font-weight:500;color:var(--ax-text-primary)"><?= htmlspecialchars($d->name) ?></div>
          <div style="font-size:10px;color:var(--ax-text-muted)"><?= $ast ? htmlspecialchars($ast->name) : 'Sem assistente' ?></div>
        </div>
        <span style="padding:3px 8px;border-radius:5px;font-size:10px;font-weight:600;background:<?= ($ast&&$ast->is_active)?'rgba(45,122,107,.2)':'rgba(229,62,62,.15)' ?>;color:<?= ($ast&&$ast->is_active)?'#2D7A6B':'#E53E3E' ?>">
          <?= ($ast&&$ast->is_active)?'Ativa':'Inativa' ?>
        </span>
      </div>
    <?php endforeach; ?>
    <?php if(empty($devices)): ?>
      <div style="text-align:center;padding:16px;font-size:11px;color:var(--ax-text-muted)">Nenhum dispositivo</div>
    <?php endif; ?>
  </div>
  <div style="background:var(--ax-bg-card);border-radius:10px;padding:14px;border:1px solid var(--ax-border)">
    <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--ax-text-muted);margin-bottom:12px">Base de conhecimento</div>
    <?php
    $cats=['product'=>'Produto/Serviço','faq'=>'Perguntas Frequentes','objection'=>'Objeções','info'=>'Informações','sales_tip'=>'Dicas de Venda'];
    $cat_counts=[];
    foreach($knowledge??[] as $k) $cat_counts[$k->category]=($cat_counts[$k->category]??0)+1;
    foreach($cats as $key=>$label):
      $count=$cat_counts[$key]??0;
    ?>
      <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--ax-border);font-size:11px">
        <span style="color:var(--ax-text-secondary)"><?= $label ?></span>
        <span style="color:<?= $count>0?'#2D7A6B':'var(--ax-text-muted)' ?>;font-weight:600"><?= $count ?> itens</span>
      </div>
    <?php endforeach; ?>
    <div style="margin-top:10px">
      <a href="<?= admin_url('axiomchannel/assistant') ?>" style="display:block;text-align:center;padding:8px;background:rgba(45,122,107,.15);color:#2D7A6B;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none">
        Gerenciar base de conhecimento →
      </a>
    </div>
  </div>
</div>
