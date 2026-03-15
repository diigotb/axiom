<?php
/**
 * VIEW: Chat individual
 * Recebe do controller: $contact, $device, $messages, $staff
 */
defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<div id="wrapper">
  <div class="content" style="padding:0;display:flex;height:calc(100vh - 65px);overflow:hidden">

    <!-- ========== SIDEBAR DE CONTATOS ========== -->
    <div style="width:310px;min-width:310px;background:#fff;border-right:1px solid #e8e8e8;display:flex;flex-direction:column;overflow:hidden">
      <div style="padding:14px 16px;border-bottom:1px solid #e8e8e8;flex-shrink:0">
        <a href="<?= admin_url('axiomchannel') ?>" style="font-weight:700;color:#5b5ef4;text-decoration:none;font-size:15px">
          <i class="fa fa-comments"></i> AxiomChannel
        </a>
        <div style="position:relative;margin-top:10px">
          <input type="text" id="axch-search" class="form-control input-sm" placeholder="Buscar..." style="padding-left:28px">
          <i class="fa fa-search" style="position:absolute;left:9px;top:50%;transform:translateY(-50%);color:#aaa;font-size:11px"></i>
        </div>
      </div>
      <div id="axch-contact-list" style="flex:1;overflow-y:auto">
        <div style="text-align:center;padding:20px;color:#ccc">
          <i class="fa fa-spinner fa-spin"></i>
        </div>
      </div>
    </div>

    <!-- ========== PAINEL DO CHAT ========== -->
    <div style="flex:1;display:flex;flex-direction:column;overflow:hidden;background:#f9f9f9">

      <!-- Header do chat -->
      <div style="background:#fff;border-bottom:1px solid #e8e8e8;padding:0 20px;height:58px;display:flex;align-items:center;gap:12px;flex-shrink:0">
        <!-- Avatar -->
        <div style="width:38px;height:38px;border-radius:50%;background:#ebebff;color:#5b5ef4;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0">
          <?= strtoupper(substr($contact->name ?: $contact->phone_number, 0, 1)) ?>
        </div>
        <!-- Nome e número -->
        <div style="flex:1">
          <div style="font-weight:600;font-size:14px"><?= htmlspecialchars($contact->name ?: $contact->phone_number) ?></div>
          <div style="font-size:11px;color:#999">
            <?= htmlspecialchars($contact->phone_number) ?>
            <?php if ($device): ?> · <?= htmlspecialchars($device->name) ?><?php endif; ?>
          </div>
        </div>
        <!-- Botões de ação -->
        <div style="display:flex;gap:8px">
          <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-transfer">
            <i class="fa fa-exchange"></i> Transferir
          </button>
          <button class="btn btn-success btn-sm" onclick="axchResolve()">
            <i class="fa fa-check"></i> Resolver
          </button>
        </div>
      </div>

      <!-- Área de mensagens -->
      <div id="axch-messages" style="flex:1;overflow-y:auto;padding:20px;display:flex;flex-direction:column;gap:6px">
        <?php if (empty($messages)): ?>
          <div style="text-align:center;padding:40px;color:#ccc">
            <i class="fa fa-comments-o" style="font-size:36px;display:block;margin-bottom:10px"></i>
            <p>Nenhuma mensagem ainda</p>
          </div>
        <?php else: ?>
          <?php
          $ultimo_dia = null;
          foreach ($messages as $msg):
            $dia_msg = date('Y-m-d', strtotime($msg->created_at));
            // Cabeçalho de data (Hoje, Ontem, ou a data)
            if ($dia_msg !== $ultimo_dia):
              $ultimo_dia = $dia_msg;
              if ($dia_msg === date('Y-m-d'))            $label = 'Hoje';
              elseif ($dia_msg === date('Y-m-d', strtotime('-1 day'))) $label = 'Ontem';
              else $label = date('d/m/Y', strtotime($msg->created_at));
          ?>
            <div style="text-align:center;font-size:11px;color:#aaa;margin:10px 0;position:relative">
              <span style="background:#f9f9f9;padding:0 10px"><?= $label ?></span>
              <hr style="position:absolute;top:50%;left:0;right:0;margin:0;border-color:#e8e8e8;z-index:-1">
            </div>
          <?php endif; ?>

          <!-- Bolha de mensagem -->
          <?php $saida = ($msg->direction === 'outbound'); ?>
          <div style="display:flex;justify-content:<?= $saida ? 'flex-end' : 'flex-start' ?>" data-msg-id="<?= $msg->id ?>">
            <div style="max-width:65%">
              <div style="padding:8px 12px;border-radius:12px;<?= $saida ? 'background:#5b5ef4;color:#fff;border-bottom-right-radius:3px' : 'background:#fff;color:#333;border-bottom-left-radius:3px;box-shadow:0 1px 2px rgba(0,0,0,.08)' ?>;font-size:13px;line-height:1.5;word-break:break-word">
                <?php if ($msg->type === 'image' && $msg->media_url): ?>
                  <img src="<?= $msg->media_url ?>" style="max-width:200px;border-radius:6px;display:block;margin-bottom:4px">
                <?php endif; ?>
                <?php if ($msg->type === 'audio'): ?>
                  <i class="fa fa-microphone"></i> <em style="font-size:12px">Áudio</em>
                <?php elseif ($msg->type === 'document'): ?>
                  <i class="fa fa-file"></i> <?= htmlspecialchars($msg->media_filename ?? 'Documento') ?>
                <?php else: ?>
                  <?= nl2br(htmlspecialchars($msg->content ?? '')) ?>
                <?php endif; ?>
              </div>
              <!-- Horário e status -->
              <div style="font-size:10px;color:#999;margin-top:2px;<?= $saida ? 'text-align:right' : '' ?>">
                <?= date('H:i', strtotime($msg->created_at)) ?>
                <?php if ($saida): ?>
                  <?php if ($msg->sent_by_ai): ?><i class="fa fa-bolt" title="IA"></i><?php endif; ?>
                  <i class="fa fa-check<?= $msg->status === 'read' ? '-double' : '' ?>"></i>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Área de input -->
      <div style="background:#fff;border-top:1px solid #e8e8e8;padding:12px 16px;display:flex;align-items:flex-end;gap:10px;flex-shrink:0">
        <div style="flex:1;background:#f5f5f5;border-radius:20px;padding:8px 14px;display:flex;align-items:flex-end;gap:8px">
          <textarea id="axch-input" rows="1"
            placeholder="Digite uma mensagem... (Enter envia, Shift+Enter nova linha)"
            style="flex:1;border:none;background:transparent;resize:none;outline:none;font-size:13px;line-height:1.5;max-height:100px;overflow-y:auto;font-family:inherit"
            onkeydown="axchHandleKey(event)"
            oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,100)+'px'"></textarea>
        </div>
        <button id="axch-send-btn" onclick="axchSend()"
          style="width:40px;height:40px;border-radius:50%;background:#5b5ef4;color:#fff;border:none;cursor:pointer;flex-shrink:0;font-size:15px">
          <i class="fa fa-paper-plane"></i>
        </button>
      </div>

    </div>
  </div>
</div>

<!-- Modal de Transferência -->
<div class="modal fade" id="modal-transfer" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Transferir atendimento</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Transferir para:</label>
          <select id="transfer-staff" class="form-control">
            <?php foreach ($staff as $s): ?>
              <option value="<?= $s->staffid ?>"><?= htmlspecialchars($s->firstname . ' ' . $s->lastname) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Observação (opcional):</label>
          <textarea id="transfer-note" class="form-control" rows="2" placeholder="Ex: Cliente perguntou sobre plano X"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" onclick="axchTransfer()">Transferir</button>
      </div>
    </div>
  </div>
</div>

<script>
const CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';
const CSRF_NAME  = '<?= $this->security->get_csrf_token_name() ?>';
const CONTACT_ID = <?= (int)$contact->id ?>;
const ADMIN_URL  = '<?= admin_url() ?>';
let lastMsgId    = <?= !empty($messages) ? (int)end($messages)->id : 0 ?>;
let isSending    = false;

// ---- ENVIAR MENSAGEM ----
function axchSend() {
  if (isSending) return;
  const input = document.getElementById('axch-input');
  const msg   = input.value.trim();
  if (!msg) return;

  isSending = true;
  document.getElementById('axch-send-btn').disabled = true;

  fetch(ADMIN_URL + 'axiomchannel/send_message', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ contact_id: CONTACT_ID, message: msg, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      input.value = '';
      input.style.height = 'auto';
      appendMessage({ direction:'outbound', content:msg, created_at:new Date().toISOString().replace('T',' ').slice(0,19), status:'sent', type:'text', sent_by_ai:0 });
      scrollBottom();
      if (data.message_id) lastMsgId = Math.max(lastMsgId, data.message_id);
    } else {
      alert(data.message || 'Erro ao enviar mensagem');
    }
  })
  .catch(() => alert('Erro de conexão'))
  .finally(() => {
    isSending = false;
    document.getElementById('axch-send-btn').disabled = false;
  });
}

// ---- ADICIONAR BOLHA DE MENSAGEM NO DOM ----
function appendMessage(msg) {
  const wrap   = document.getElementById('axch-messages');
  // Remove o estado vazio se existir
  const empty  = wrap.querySelector('[style*="Nenhuma mensagem"]');
  if (empty) empty.parentElement.remove();

  const saida  = msg.direction === 'outbound';
  const hora   = new Date(msg.created_at.replace(' ','T')).toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'});
  const div    = document.createElement('div');
  div.style    = `display:flex;justify-content:${saida ? 'flex-end' : 'flex-start'}`;
  div.setAttribute('data-msg-id', msg.id || '');
  div.innerHTML = `
    <div style="max-width:65%">
      <div style="padding:8px 12px;border-radius:12px;${saida ? 'background:#5b5ef4;color:#fff;border-bottom-right-radius:3px' : 'background:#fff;color:#333;border-bottom-left-radius:3px;box-shadow:0 1px 2px rgba(0,0,0,.08)'};font-size:13px;line-height:1.5;word-break:break-word">
        ${esc(msg.content || '')}
      </div>
      <div style="font-size:10px;color:#999;margin-top:2px;${saida ? 'text-align:right' : ''}">
        ${hora} ${saida ? '<i class="fa fa-check"></i>' : ''}
      </div>
    </div>`;
  wrap.appendChild(div);
}

function scrollBottom() {
  const w = document.getElementById('axch-messages');
  w.scrollTop = w.scrollHeight;
}

function esc(str) {
  // Converte < > & para evitar HTML injetado
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
}

// ---- POLLING: verifica novas mensagens a cada 5s ----
function axchPoll() {
  fetch(ADMIN_URL + 'axiomchannel/get_messages', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ contact_id: CONTACT_ID, limit: 100, offset: 0, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (!data.success) return;
    // Filtra só mensagens novas (id maior que o último que já temos)
    const novas = data.messages.filter(m => parseInt(m.id) > lastMsgId);
    if (novas.length > 0) {
      novas.forEach(m => {
        appendMessage(m);
        lastMsgId = Math.max(lastMsgId, parseInt(m.id));
      });
      scrollBottom();
    }
  });
}

// ---- TRANSFERIR ----
function axchTransfer() {
  const staff = document.getElementById('transfer-staff').value;
  const note  = document.getElementById('transfer-note').value;
  fetch(ADMIN_URL + 'axiomchannel/transfer', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ contact_id: CONTACT_ID, staff_id: staff, note: note, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      $('#modal-transfer').modal('hide');
      alert('Atendimento transferido!');
    }
  });
}

// ---- RESOLVER ----
function axchResolve() {
  if (!confirm('Marcar esta conversa como resolvida?')) return;
  fetch(ADMIN_URL + 'axiomchannel/update_contact_status', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ contact_id: CONTACT_ID, status: 'resolved', [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) window.location.href = ADMIN_URL + 'axiomchannel';
  });
}

// ---- SIDEBAR DE CONTATOS ----
function loadSidebarContacts() {
  const search = document.getElementById('axch-search').value;
  fetch(ADMIN_URL + 'axiomchannel/get_contacts', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ search: search, status: 'open', limit: 30, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (!data.success) return;
    const list = document.getElementById('axch-contact-list');
    if (!data.contacts.length) { list.innerHTML = '<p style="text-align:center;padding:20px;color:#ccc;font-size:12px">Sem conversas</p>'; return; }
    list.innerHTML = data.contacts.map(c => {
      const ativo = parseInt(c.id) === CONTACT_ID;
      return `<a href="${ADMIN_URL}axiomchannel/chat/${c.id}"
        style="display:flex;align-items:center;gap:8px;padding:10px 14px;text-decoration:none;color:inherit;border-bottom:1px solid #f5f5f5;${ativo ? 'background:#ebebff' : (!parseInt(c.is_read) ? 'background:#f5f5ff' : '')}">
        <div style="width:36px;height:36px;border-radius:50%;background:#ebebff;color:#5b5ef4;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0">
          ${(c.name || c.phone_number || '?')[0].toUpperCase()}
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-size:12px;font-weight:${!parseInt(c.is_read) ? '700' : '500'};white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${esc(c.name || c.phone_number)}</div>
          <div style="font-size:11px;color:#999;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${esc((c.last_message||'').substring(0,40))}</div>
        </div>
      </a>`;
    }).join('');
  });
}

// ---- TECLA ENTER ----
function axchHandleKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    axchSend();
  }
}

// ---- INIT ----
scrollBottom();
loadSidebarContacts();
setInterval(axchPoll, 5000);          // polling de mensagens
setInterval(loadSidebarContacts, 10000); // atualiza sidebar

let searchTimer;
document.getElementById('axch-search').addEventListener('input', () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(loadSidebarContacts, 400);
});
</script>

<?php init_tail(); ?>
