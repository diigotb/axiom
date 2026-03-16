<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
$status_labels = ['draft'=>'Rascunho','sent'=>'Enviado','viewed'=>'Visualizado','signed'=>'Assinado','cancelled'=>'Cancelado'];
$status_colors = ['draft'=>'#94a3b8','sent'=>'#3182CE','viewed'=>'#805AD5','signed'=>'#38A169','cancelled'=>'#E53E3E'];
?>
<link rel="stylesheet" href="<?= module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css') ?>">
<div id="wrapper">
<div class="content" style="padding:24px">

  <!-- Cabeçalho -->
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
    <div>
      <h2 style="font-size:20px;font-weight:700;color:#1e293b;margin:0">Contratos</h2>
      <p style="font-size:13px;color:#64748b;margin:4px 0 0">Crie, envie e monitore contratos com assinatura digital</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center">
      <?php if (!empty($devices)): ?>
      <select onchange="window.location.href='<?= admin_url('axiomchannel/contracts?device_id=') ?>'+this.value"
        style="font-size:13px;padding:6px 12px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#1e293b">
        <?php foreach ($devices as $d): ?>
          <option value="<?= $d->id ?>" <?= $d->id == $device_id ? 'selected' : '' ?>><?= htmlspecialchars($d->name) ?></option>
        <?php endforeach; ?>
      </select>
      <?php endif; ?>
      <a href="<?= admin_url('axiomchannel/contract_new?device_id=' . $device_id) ?>"
        style="padding:8px 18px;background:#2D7A6B;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">
        <i class="fa fa-plus"></i> Novo contrato
      </a>
    </div>
  </div>

  <!-- Filtros de status -->
  <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap">
    <?php $all_statuses = [''=>'Todos'] + $status_labels; ?>
    <?php foreach ($all_statuses as $sv => $sl): ?>
    <a href="<?= admin_url('axiomchannel/contracts?device_id=' . $device_id . ($sv ? '&status=' . $sv : '')) ?>"
      style="padding:6px 14px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;
        background:<?= $status === $sv ? '#1e293b' : '#fff' ?>;
        color:<?= $status === $sv ? '#fff' : '#64748b' ?>;
        border:1px solid <?= $status === $sv ? '#1e293b' : '#e2e8f0' ?>">
      <?= $sl ?>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Lista de contratos -->
  <?php if (empty($contracts)): ?>
    <div style="text-align:center;padding:60px;color:#94a3b8;background:#fff;border:1px solid #e2e8f0;border-radius:14px">
      <i class="fa fa-file-text" style="font-size:48px;display:block;margin-bottom:16px"></i>
      <p style="font-size:14px">Nenhum contrato encontrado.</p>
      <a href="<?= admin_url('axiomchannel/contract_new?device_id=' . $device_id) ?>"
        style="display:inline-block;margin-top:12px;padding:10px 20px;background:#2D7A6B;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">
        Criar primeiro contrato
      </a>
    </div>
  <?php else: ?>
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden">
      <table style="width:100%;border-collapse:collapse">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0">
            <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.5px">Título</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.5px">Status</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.5px">Criado em</th>
            <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.5px">Assinado por</th>
            <th style="padding:12px 16px;text-align:center;font-size:11px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.5px">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($contracts as $c): ?>
          <tr style="border-bottom:1px solid #f1f5f9" onmouseenter="this.style.background='#f8fafc'" onmouseleave="this.style.background=''">
            <td style="padding:12px 16px">
              <div style="font-size:13px;font-weight:600;color:#1e293b"><?= htmlspecialchars($c->title) ?></div>
              <?php if ($c->signed_at): ?>
                <div style="font-size:11px;color:#64748b">Assinado em <?= date('d/m/Y H:i', strtotime($c->signed_at)) ?></div>
              <?php endif; ?>
            </td>
            <td style="padding:12px 16px">
              <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:4px;background:<?= ($status_colors[$c->status] ?? '#94a3b8') ?>22;color:<?= $status_colors[$c->status] ?? '#94a3b8' ?>">
                <?= $status_labels[$c->status] ?? $c->status ?>
              </span>
            </td>
            <td style="padding:12px 16px;font-size:12px;color:#64748b"><?= date('d/m/Y', strtotime($c->created_at)) ?></td>
            <td style="padding:12px 16px;font-size:12px;color:#64748b"><?= $c->signer_name ? htmlspecialchars($c->signer_name) : '—' ?></td>
            <td style="padding:12px 16px;text-align:center">
              <div style="display:flex;justify-content:center;gap:6px">
                <a href="<?= admin_url('axiomchannel/contract_pdf/' . $c->id) ?>" target="_blank"
                  title="Baixar PDF"
                  style="border:1px solid #e2e8f0;background:#fff;border-radius:6px;padding:5px 9px;color:#64748b;text-decoration:none;font-size:12px">
                  <i class="fa fa-file-pdf-o"></i>
                </a>
                <?php if (in_array($c->status, ['draft','sent','viewed'])): ?>
                <button onclick="sendContract(<?= $c->id ?>)" title="Enviar por WhatsApp"
                  style="border:1px solid #a7f3d0;background:#f0fdf9;border-radius:6px;padding:5px 9px;color:#2D7A6B;cursor:pointer;font-size:12px">
                  <i class="fa fa-whatsapp"></i>
                </button>
                <?php endif; ?>
                <a href="<?= base_url('axiomchannel/contract_sign/' . $c->sign_token) ?>" target="_blank"
                  title="Ver link de assinatura"
                  style="border:1px solid #e2e8f0;background:#fff;border-radius:6px;padding:5px 9px;color:#64748b;text-decoration:none;font-size:12px">
                  <i class="fa fa-external-link"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

</div>
</div>

<script>
const CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';
const CSRF_NAME  = '<?= $this->security->get_csrf_token_name() ?>';
const ADMIN_URL  = '<?= admin_url() ?>';

function sendContract(id) {
  if (!confirm('Enviar este contrato por WhatsApp?')) return;
  fetch(ADMIN_URL + 'axiomchannel/contract_send', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: new URLSearchParams({ id, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) {
      alert('Contrato enviado! Link: ' + res.sign_url);
      location.reload();
    } else {
      alert(res.message || 'Erro ao enviar');
    }
  });
}
</script>

<?php init_tail(); ?>
