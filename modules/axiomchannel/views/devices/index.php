<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<link rel="stylesheet" href="<?= module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css') ?>">
<div id="wrapper">
<div class="content ax-app" style="flex-direction:column">

  <!-- Header da página -->
  <div class="ax-page-header">
    <div>
      <h4 class="ax-page-title"><i class="fa fa-mobile" style="color:var(--ax-teal);margin-right:8px"></i>Dispositivos WhatsApp</h4>
      <p class="ax-page-sub">Gerencie seus números conectados ao AxiomChannel</p>
    </div>
    <button class="ax-btn ax-btn-primary" data-toggle="modal" data-target="#modal-add-device">
      <i class="fa fa-plus"></i> Adicionar dispositivo
    </button>
  </div>

  <!-- Grid de dispositivos -->
  <div class="ax-device-grid">
    <?php if (empty($devices)): ?>
      <div class="ax-device-card ax-device-card-add" data-toggle="modal" data-target="#modal-add-device" style="grid-column:1/-1;max-width:320px">
        <i class="fa fa-plus" style="font-size:24px"></i>
        <strong style="font-size:14px">Adicionar dispositivo</strong>
        <span style="font-size:12px;text-align:center">Conecte seu primeiro número de WhatsApp</span>
      </div>
    <?php else: ?>
      <?php foreach ($devices as $d): ?>
        <div class="ax-device-card" id="device-card-<?= $d->id ?>">
          <div class="ax-flex ax-gap-3" style="justify-content:space-between;align-items:flex-start;margin-bottom:14px">
            <div class="ax-device-icon"><i class="fa fa-mobile"></i></div>
            <?php
              if ($d->status === 'connected')   { $badge = 'ax-badge-success'; $label = '● Conectado'; }
              elseif ($d->status === 'connecting') { $badge = 'ax-badge-warning'; $label = '● Conectando'; }
              else                               { $badge = 'ax-badge-danger';  $label = '● Desconectado'; }
            ?>
            <span class="ax-badge <?= $badge ?>" id="status-badge-<?= $d->id ?>"><?= $label ?></span>
          </div>
          <p class="ax-device-name"><?= htmlspecialchars($d->name) ?></p>
          <p class="ax-device-info"><i class="fa fa-hashtag"></i> <?= htmlspecialchars($d->instance_name) ?></p>
          <p class="ax-device-info"><i class="fa fa-phone"></i> <?= $d->phone_number ?: 'Número não definido' ?></p>
          <p class="ax-device-info"><i class="fa fa-server"></i> <?= htmlspecialchars($d->server_url) ?></p>
          <div class="ax-flex ax-gap-2 ax-mt-3">
            <?php if ($d->status !== 'connected'): ?>
              <button class="ax-btn ax-btn-primary ax-btn-sm ax-w-full" onclick="axchShowQR(<?= $d->id ?>)">
                <i class="fa fa-qrcode"></i> Conectar WhatsApp
              </button>
            <?php else: ?>
              <button class="ax-btn ax-btn-sm ax-w-full" onclick="axchCheckStatus(<?= $d->id ?>)">
                <i class="fa fa-refresh"></i> Verificar status
              </button>
            <?php endif; ?>
            <button class="ax-btn ax-btn-danger ax-btn-sm" onclick="axchDeleteDevice(<?= $d->id ?>)" title="Remover">
              <i class="fa fa-trash"></i>
            </button>
          </div>
          <div style="margin-top:8px">
            <a href="<?= admin_url('axiomchannel/meta_connect') ?>"
               class="ax-btn ax-btn-sm ax-w-full"
               style="background:#f0f2ff;color:#5A67D8;border:1px solid #c3c9f8;display:flex;align-items:center;justify-content:center;gap:6px;font-size:11px">
              <i class="fa fa-facebook-square" style="color:#1877F2"></i>
              <i class="fa fa-instagram" style="color:#E1306C"></i>
              Conectar Facebook/Instagram
            </a>
          </div>
        </div>
      <?php endforeach; ?>
      <div class="ax-device-card ax-device-card-add" data-toggle="modal" data-target="#modal-add-device">
        <i class="fa fa-plus" style="font-size:22px"></i>
        <span style="font-size:13px;font-weight:500">Adicionar dispositivo</span>
      </div>
    <?php endif; ?>
  </div>

</div>
</div>

<!-- Modal adicionar dispositivo -->
<div class="modal fade" id="modal-add-device" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-mobile"></i> Adicionar dispositivo WhatsApp</h4>
      </div>
      <div class="modal-body">
        <div class="ax-form-group">
          <label class="ax-label">Nome do dispositivo <span style="color:var(--ax-danger)">*</span></label>
          <input type="text" id="dev-name" class="ax-input" placeholder="Ex: Atendimento principal">
        </div>
        <div class="ax-form-group">
          <label class="ax-label">Nome da instância (Evolution API) <span style="color:var(--ax-danger)">*</span></label>
          <input type="text" id="dev-instance" class="ax-input" placeholder="Ex: axiom-atendimento">
          <p class="ax-text-sm ax-text-muted ax-mt-2">Sem espaços ou acentos. Precisa existir na sua Evolution API.</p>
        </div>
        <div class="ax-form-group">
          <label class="ax-label">URL do servidor Evolution API</label>
          <input type="text" id="dev-server" class="ax-input" value="http://localhost:8080">
        </div>
        <div class="ax-form-group">
          <label class="ax-label">Chave API <span class="ax-text-muted">(se usar autenticação)</span></label>
          <input type="text" id="dev-apikey" class="ax-input" placeholder="Deixe em branco se não usar">
        </div>
      </div>
      <div class="modal-footer">
        <button class="ax-btn" data-dismiss="modal">Cancelar</button>
        <button class="ax-btn ax-btn-primary" id="btn-save-device" onclick="axchAddDevice()">
          <i class="fa fa-save"></i> Salvar dispositivo
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="modal-qr" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Conectar WhatsApp</h4>
      </div>
      <div class="modal-body ax-qr-wrap" id="modal-qr-body">
        <i class="fa fa-spinner fa-spin fa-2x" style="color:var(--ax-teal)"></i>
        <p class="ax-text-sm ax-text-muted ax-mt-2">Carregando QR Code...</p>
      </div>
    </div>
  </div>
</div>

<script>
const CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';
const CSRF_NAME  = '<?= $this->security->get_csrf_token_name() ?>';
const ADMIN_URL  = '<?= admin_url() ?>';
let qrInterval;
let qrDeviceId;

function axchAddDevice() {
  const name     = document.getElementById('dev-name').value.trim();
  const instance = document.getElementById('dev-instance').value.trim();
  const server   = document.getElementById('dev-server').value.trim();
  const apikey   = document.getElementById('dev-apikey').value.trim();
  if (!name || !instance) { alert('Preencha o nome e o nome da instância'); return; }
  const btn = document.getElementById('btn-save-device');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Salvando...';
  fetch(ADMIN_URL + 'axiomchannel/add_device', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ name, instance_name: instance, server_url: server, api_key: apikey, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) { $('#modal-add-device').modal('hide'); window.location.reload(); }
    else { alert(data.message || 'Erro ao salvar'); btn.disabled = false; btn.innerHTML = '<i class="fa fa-save"></i> Salvar dispositivo'; }
  })
  .catch(() => { alert('Erro de conexão'); btn.disabled = false; btn.innerHTML = '<i class="fa fa-save"></i> Salvar dispositivo'; });
}

function axchShowQR(deviceId) {
  qrDeviceId = deviceId;
  document.getElementById('modal-qr-body').innerHTML = '<i class="fa fa-spinner fa-spin fa-2x" style="color:var(--ax-teal)"></i><p class="ax-text-sm ax-text-muted ax-mt-2">Carregando QR Code...</p>';
  $('#modal-qr').modal('show');
  clearInterval(qrInterval);
  fetchQR();
  qrInterval = setInterval(fetchQR, 30000);
  $('#modal-qr').off('hidden.bs.modal').on('hidden.bs.modal', () => clearInterval(qrInterval));
}

function fetchQR() {
  fetch(ADMIN_URL + 'axiomchannel/qrcode/' + qrDeviceId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
  .then(r => r.json())
  .then(data => {
    if (data.success && data.qrcode) {
      document.getElementById('modal-qr-body').innerHTML = `
        <img src="${data.qrcode}" alt="QR Code">
        <p class="ax-qr-instructions">Abra o WhatsApp no seu celular<br>Configurações → Aparelhos conectados → Conectar aparelho</p>`;
      const check = setInterval(() => axchCheckStatus(qrDeviceId, () => clearInterval(check)), 4000);
    } else {
      document.getElementById('modal-qr-body').innerHTML = '<div class="ax-warn">Erro ao carregar QR Code. Verifique se a Evolution API está rodando.</div>';
    }
  });
}

function axchCheckStatus(deviceId, onConnected) {
  fetch(ADMIN_URL + 'axiomchannel/device_status/' + deviceId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
  .then(r => r.json())
  .then(data => {
    if (!data.success) return;
    const badge = document.getElementById('status-badge-' + deviceId);
    if (badge) {
      if (data.status === 'connected') { badge.className = 'ax-badge ax-badge-success'; badge.textContent = '● Conectado'; }
      else { badge.className = 'ax-badge ax-badge-danger'; badge.textContent = '● Desconectado'; }
    }
    if (data.status === 'connected') { clearInterval(qrInterval); $('#modal-qr').modal('hide'); if (onConnected) onConnected(); }
  });
}

function axchDeleteDevice(id) {
  if (!confirm('Remover este dispositivo? As conversas serão mantidas.')) return;
  fetch(ADMIN_URL + 'axiomchannel/delete_device/' + id, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => { if (data.success) { const c = document.getElementById('device-card-' + id); if (c) c.remove(); } });
}
</script>

<?php init_tail(); ?>
