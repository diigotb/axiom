<?php
/**
 * VIEW: Gestão de dispositivos WhatsApp
 */
defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">

        <!-- Cabeçalho da página -->
        <div class="page-heading" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
          <h3 style="margin:0">
            <i class="fa fa-mobile" style="color:#5b5ef4"></i> Dispositivos WhatsApp
          </h3>
          <button class="btn btn-primary" data-toggle="modal" data-target="#modal-add-device">
            <i class="fa fa-plus"></i> Adicionar dispositivo
          </button>
        </div>

        <!-- Grid de dispositivos -->
        <?php if (empty($devices)): ?>
          <div style="text-align:center;padding:80px 40px;color:#aaa;background:#fff;border-radius:8px;border:1px dashed #ddd">
            <i class="fa fa-mobile" style="font-size:52px;display:block;margin-bottom:16px;opacity:.25"></i>
            <h4 style="font-weight:400;color:#bbb">Nenhum dispositivo cadastrado</h4>
            <p>Clique em "Adicionar dispositivo" para conectar seu primeiro número de WhatsApp.</p>
            <button class="btn btn-primary" data-toggle="modal" data-target="#modal-add-device">
              <i class="fa fa-plus"></i> Adicionar agora
            </button>
          </div>
        <?php else: ?>
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px">
            <?php foreach ($devices as $d): ?>
              <div class="panel panel-default" id="device-card-<?= $d->id ?>" style="border-radius:8px;overflow:hidden">
                <div class="panel-body" style="padding:20px">
                  <!-- Nome e status -->
                  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
                    <h5 style="margin:0;font-weight:600"><?= htmlspecialchars($d->name) ?></h5>
                    <?php
                      if ($d->status === 'connected')   { $badge = 'success'; $label = 'Conectado'; }
                      elseif ($d->status === 'connecting') { $badge = 'warning'; $label = 'Conectando...'; }
                      else                               { $badge = 'danger';  $label = 'Desconectado'; }
                    ?>
                    <span class="label label-<?= $badge ?>" id="status-badge-<?= $d->id ?>"><?= $label ?></span>
                  </div>

                  <!-- Info -->
                  <p style="font-size:12px;color:#888;margin:0 0 4px">
                    <i class="fa fa-hashtag"></i> Instância: <strong><?= htmlspecialchars($d->instance_name) ?></strong>
                  </p>
                  <p style="font-size:12px;color:#888;margin:0 0 4px">
                    <i class="fa fa-phone"></i> Número: <?= $d->phone_number ?: '<em>não definido</em>' ?>
                  </p>
                  <p style="font-size:12px;color:#888;margin:0 0 16px">
                    <i class="fa fa-server"></i> <?= htmlspecialchars($d->server_url) ?>
                  </p>

                  <!-- Botões -->
                  <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <?php if ($d->status !== 'connected'): ?>
                      <button class="btn btn-primary btn-sm" onclick="axchShowQR(<?= $d->id ?>)">
                        <i class="fa fa-qrcode"></i> Conectar WhatsApp
                      </button>
                    <?php else: ?>
                      <button class="btn btn-default btn-sm" onclick="axchCheckStatus(<?= $d->id ?>)">
                        <i class="fa fa-refresh"></i> Verificar status
                      </button>
                    <?php endif; ?>
                    <button class="btn btn-danger btn-sm" onclick="axchDeleteDevice(<?= $d->id ?>)">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<!-- ========== MODAL: Adicionar dispositivo ========== -->
<div class="modal fade" id="modal-add-device" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-mobile"></i> Adicionar dispositivo WhatsApp</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Nome do dispositivo <span class="text-danger">*</span></label>
          <input type="text" id="dev-name" class="form-control" placeholder="Ex: Atendimento Clínica">
        </div>
        <div class="form-group">
          <label>Nome da instância (Evolution API) <span class="text-danger">*</span></label>
          <input type="text" id="dev-instance" class="form-control" placeholder="Ex: axiom-clinica">
          <p class="help-block">Sem espaços ou acentos. Este nome será usado na Evolution API.</p>
        </div>
        <div class="form-group">
          <label>URL do servidor Evolution API</label>
          <input type="text" id="dev-server" class="form-control" value="http://localhost:8080">
        </div>
        <div class="form-group">
          <label>Chave API <small class="text-muted">(se a sua Evolution API usar autenticação)</small></label>
          <input type="text" id="dev-apikey" class="form-control" placeholder="Deixe em branco se não usar">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" id="btn-save-device" onclick="axchAddDevice()">
          <i class="fa fa-save"></i> Salvar dispositivo
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ========== MODAL: QR Code ========== -->
<div class="modal fade" id="modal-qr" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Conectar WhatsApp</h4>
      </div>
      <div class="modal-body" id="modal-qr-body" style="text-align:center;padding:30px">
        <i class="fa fa-spinner fa-spin fa-2x" style="color:#5b5ef4"></i>
        <p style="margin-top:12px;color:#666">Carregando QR Code...</p>
      </div>
    </div>
  </div>
</div>

<script>
const ADMIN_URL = '<?= admin_url() ?>';
let qrInterval;
let qrDeviceId;

// ---- ADICIONAR DISPOSITIVO ----
// Token CSRF do Perfex — obrigatório em todas as requisições POST
const CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';
const CSRF_NAME  = '<?= $this->security->get_csrf_token_name() ?>';

function axchAddDevice() {
  const name     = document.getElementById('dev-name').value.trim();
  const instance = document.getElementById('dev-instance').value.trim();
  const server   = document.getElementById('dev-server').value.trim();
  const apikey   = document.getElementById('dev-apikey').value.trim();

  if (!name || !instance) {
    alert('Preencha o nome e o nome da instância');
    return;
  }

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
    if (data.success) {
      $('#modal-add-device').modal('hide');
      window.location.reload();
    } else {
      alert(data.message || 'Erro ao salvar');
      btn.disabled = false;
      btn.innerHTML = '<i class="fa fa-save"></i> Salvar dispositivo';
    }
  })
  .catch(() => {
    alert('Erro de conexão');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-save"></i> Salvar dispositivo';
  });
}

// ---- MOSTRAR QR CODE ----
function axchShowQR(deviceId) {
  qrDeviceId = deviceId;
  document.getElementById('modal-qr-body').innerHTML = '<i class="fa fa-spinner fa-spin fa-2x" style="color:#5b5ef4"></i><p style="margin-top:12px;color:#666">Carregando QR Code...</p>';
  $('#modal-qr').modal('show');

  clearInterval(qrInterval);
  fetchQR(); // busca imediatamente
  qrInterval = setInterval(fetchQR, 30000); // atualiza a cada 30s (QR expira)

  // Quando fechar o modal, para o intervalo
  $('#modal-qr').off('hidden.bs.modal').on('hidden.bs.modal', () => clearInterval(qrInterval));
}

function fetchQR() {
  fetch(ADMIN_URL + 'axiomchannel/qrcode/' + qrDeviceId, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(data => {
    if (data.success && data.qrcode) {
      document.getElementById('modal-qr-body').innerHTML = `
        <img src="${data.qrcode}" style="max-width:240px;border-radius:6px;border:1px solid #eee"><br>
        <small style="color:#666;display:block;margin-top:10px">
          WhatsApp → Configurações → Aparelhos conectados → Conectar aparelho
        </small>`;
      // Verifica se conectou a cada 4 segundos
      const checkConn = setInterval(() => {
        axchCheckStatus(qrDeviceId, () => clearInterval(checkConn));
      }, 4000);
    } else {
      document.getElementById('modal-qr-body').innerHTML = `<div class="alert alert-danger">${data.message || 'Erro ao carregar QR Code'}</div>`;
    }
  });
}

// ---- VERIFICAR STATUS ----
function axchCheckStatus(deviceId, onConnected) {
  fetch(ADMIN_URL + 'axiomchannel/device_status/' + deviceId, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(data => {
    if (!data.success) return;
    const badge = document.getElementById('status-badge-' + deviceId);
    if (badge) {
      badge.className = 'label label-' + (data.status === 'connected' ? 'success' : 'danger');
      badge.textContent = data.status === 'connected' ? 'Conectado' : 'Desconectado';
    }
    if (data.status === 'connected') {
      clearInterval(qrInterval);
      $('#modal-qr').modal('hide');
      if (onConnected) onConnected();
    }
  });
}

// ---- DELETAR DISPOSITIVO ----
function axchDeleteDevice(id) {
  if (!confirm('Remover este dispositivo? As conversas serão mantidas.')) return;
  fetch(ADMIN_URL + 'axiomchannel/delete_device/' + id, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const card = document.getElementById('device-card-' + id);
      if (card) card.remove();
    }
  });
}
</script>

<?php init_tail(); ?>
