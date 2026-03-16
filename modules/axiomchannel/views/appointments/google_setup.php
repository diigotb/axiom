<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<link rel="stylesheet" href="<?= module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css') ?>">
<div id="wrapper">
<div class="content" style="padding:24px;max-width:680px;margin:0 auto">

  <!-- Voltar -->
  <a href="<?= admin_url('axiomchannel/appointments') ?>"
    style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#64748b;text-decoration:none;margin-bottom:24px">
    <i class="fa fa-arrow-left"></i> Voltar para Agendamentos
  </a>

  <!-- Aviso Em breve -->
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:40px 32px;text-align:center;margin-bottom:24px">
    <div style="width:72px;height:72px;background:#f0fdf9;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px">
      📅
    </div>
    <h2 style="font-size:20px;font-weight:700;color:#1e293b;margin:0 0 10px">Integração com Google Agenda</h2>
    <p style="font-size:14px;color:#64748b;max-width:440px;margin:0 auto;line-height:1.7">
      Sincronize agendamentos criados pelo assistente IA diretamente na sua agenda Google. A conexão é segura via OAuth 2.0 — o AXIOM nunca acessa seus dados pessoais.
    </p>
  </div>

  <!-- Como vai funcionar -->
  <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;margin-bottom:20px">
    <h3 style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 18px">
      <i class="fa fa-info-circle" style="color:#3182CE"></i> Como vai funcionar
    </h3>
    <div style="display:flex;flex-direction:column;gap:14px">
      <?php
      $steps = [
        ['icon'=>'🔗','title'=>'Conectar conta Google','desc'=>'Clique em "Conectar com Google" e autorize o acesso à sua agenda — processo rápido e seguro via OAuth 2.0.'],
        ['icon'=>'📆','title'=>'Sincronização automática','desc'=>'Cada agendamento criado pelo assistente IA aparece automaticamente no seu Google Agenda com título, horário e dados do cliente.'],
        ['icon'=>'🔔','title'=>'Lembretes automáticos','desc'=>'O sistema enviará lembretes por WhatsApp 24h e 1h antes do compromisso, confirmando com o cliente.'],
        ['icon'=>'❌','title'=>'Cancelamentos sincronizados','desc'=>'Se o cliente cancelar pelo WhatsApp, o evento é removido automaticamente do Google Agenda.'],
      ];
      foreach ($steps as $s): ?>
      <div style="display:flex;gap:12px;align-items:flex-start">
        <div style="width:36px;height:36px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
          <?= $s['icon'] ?>
        </div>
        <div>
          <div style="font-size:13px;font-weight:600;color:#1e293b"><?= $s['title'] ?></div>
          <div style="font-size:12px;color:#64748b;margin-top:2px;line-height:1.5"><?= $s['desc'] ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Botão OAuth -->
  <div style="text-align:center">
    <a href="<?= admin_url('axiomchannel/google_calendar_connect?device_id=' . (int)($device_id ?? 0)) ?>"
      style="display:inline-flex;align-items:center;gap:10px;padding:14px 28px;background:#2D7A6B;color:#fff;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none">
      <i class="fa fa-google"></i> Conectar com Google Agenda
    </a>
    <p style="font-size:11px;color:#94a3b8;margin:10px 0 0">
      Você será redirecionado para a página de login do Google
    </p>
  </div>

</div>
</div>
<?php init_tail(); ?>
