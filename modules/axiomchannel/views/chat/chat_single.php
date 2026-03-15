<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<link rel="stylesheet" href="<?php echo module_dir_url('axiomchannel', 'assets/css/axiomchannel.css'); ?>">
<div id="wrapper">
<div class="content ax-app">

  <!-- Navegação lateral -->
  <nav class="ax-nav">
    <a href="<?= admin_url('axiomchannel') ?>" class="ax-nav-logo">AX</a>
    <a href="<?= admin_url('axiomchannel/inbox') ?>" class="ax-nav-item active" title="Inbox">
      <i class="fa fa-comments"></i>
    </a>
    <a href="<?= admin_url('axiomchannel/devices') ?>" class="ax-nav-item" title="Dispositivos">
      <i class="fa fa-mobile"></i>
    </a>
    <div class="ax-nav-bottom">
      <div class="ax-nav-avatar"><?= strtoupper(substr(get_staff_full_name(), 0, 1)) ?></div>
    </div>
  </nav>

  <!-- Painel lateral com lista -->
  <div class="ax-panel">
    <div class="ax-panel-header">
      <div class="ax-panel-title">Inbox</div>
      <div class="ax-search">
        <i class="fa fa-search ax-search-icon"></i>
        <input type="text" id="axch-search" placeholder="Buscar...">
      </div>
    </div>
    <div class="ax-contact-list" id="axch-contact-list">
      <div class="ax-empty-state" style="padding:20px">
        <i class="fa fa-spinner fa-spin" style="color:var(--ax-gray-400)"></i>
      </div>
    </div>
  </div>

  <!-- Chat -->
  <div class="ax-main">

    <!-- Header -->
    <div class="ax-chat-header">
      <div class="ax-avatar">
        <div class="ax-avatar-img">
          <?php if ($contact->avatar): ?>
            <img src="<?= $contact->avatar ?>" alt="">
          <?php else: ?>
            <?= strtoupper(substr($contact->name ?: $contact->phone_number, 0, 1)) ?>
          <?php endif; ?>
        </div>
        <span class="ax-status-dot ax-status-<?= $contact->status ?>"></span>
      </div>
      <div class="ax-chat-info">
        <p class="ax-chat-name"><?= htmlspecialchars($contact->name ?: $contact->phone_number) ?></p>
        <p class="ax-chat-sub">
          <?= htmlspecialchars($contact->phone_number) ?>
          <?php if ($device): ?> · <?= htmlspecialchars($device->name) ?><?php endif; ?>
        </p>
      </div>
      <div class="ax-chat-actions">
        <button class="ax-btn ax-btn-sm" data-toggle="modal" data-target="#modal-transfer">
          <i class="fa fa-exchange"></i> Transferir
        </button>
        <button class="ax-btn ax-btn-navy ax-btn-sm" onclick="axchResolve()">
          <i class="fa fa-check"></i> Resolver
        </button>
      </div>
    </div>

    <!-- Mensagens -->
    <div class="ax-messages" id="axch-messages">
      <?php if (empty($messages)): ?>
        <div class="ax-empty-state">
          <div class="ax-empty-icon"><i class="fa fa-comments-o"></i></div>
          <p class="ax-empty-title">Nenhuma mensagem ainda</p>
        </div>
      <?php else: ?>
        <?php
        $ultimo_dia = null;
        foreach ($messages as $msg):
          $dia = date('Y-m-d', strtotime($msg->created_at));
          if ($dia !== $ultimo_dia):
            $ultimo_dia = $dia;
            if ($dia === date('Y-m-d')) $label = 'Hoje';
            elseif ($dia === date('Y-m-d', strtotime('-1 day'))) $label = 'Ontem';
            else $label = date('d/m/Y', strtotime($msg->created_at));
        ?>
          <div class="ax-day-sep"><?= $label ?></div>
        <?php endif; ?>
          <div class="ax-message <?= $msg->direction ?><?= $msg->sent_by_ai ? ' ai' : '' ?>" data-id="<?= $msg->id ?>">
            <div>
              <div class="ax-bubble">
                <?php if ($msg->type === 'image' && $msg->media_url): ?>
                  <img src="<?= $msg->media_url ?>" style="max-width:200px;border-radius:6px;display:block;margin-bottom:4px">
                <?php elseif ($msg->type === 'audio'): ?>
                  <i class="fa fa-microphone"></i> <em style="font-size:12px">Áudio</em>
                <?php elseif ($msg->type === 'document'): ?>
                  <i class="fa fa-file"></i> <?= htmlspecialchars($msg->media_filename ?? 'Documento') ?>
                <?php else: ?>
                  <?= nl2br(htmlspecialchars($msg->content ?? '')) ?>
                <?php endif; ?>
              </div>
              <div class="ax-msg-meta">
                <?= date('H:i', strtotime($msg->created_at)) ?>
                <?php if ($msg->direction === 'outbound'): ?>
                  <?php if ($msg->sent_by_ai): ?>
                    <span class="ax-ai-badge">IA</span>
                  <?php elseif ($msg->firstname): ?>
                    · <?= $msg->firstname ?>
                  <?php endif; ?>
                  <i class="fa fa-check<?= $msg->status === 'read' ? '-double' : '' ?>"></i>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Input -->
    <div class="ax-input-area">
      <div class="ax-input-wrap">
        <textarea
          id="axch-input"
          class="ax-textarea"
          rows="1"
          placeholder="Digite uma mensagem... (Enter envia, Shift+Enter nova linha)"
          onkeydown="axchHandleKey(event)"
          oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,100)+'px'"></textarea>
      </div>
      <button id="axch-send-btn" class="ax-send-btn" onclick="axchSend()">
        <i class="fa fa-paper-plane"></i>
      </button>
    </div>

  </div>
</div>
</div>

<!-- Modal transferência -->
<div class="modal fade" id="modal-transfer" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Transferir atendimento</h4>
      </div>
      <div class="modal-body">
        <div class="ax-form-group">
          <label class="ax-label">Transferir para</label>
          <select id="transfer-staff" class="ax-input">
            <?php foreach ($staff as $s): ?>
              <option value="<?= $s->staffid ?>"><?= htmlspecialchars($s->firstname . ' ' . $s->lastname) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="ax-form-group">
          <label class="ax-label">Observação (opcional)</label>
          <textarea id="transfer-note" class="ax-textarea-field" rows="2" placeholder="Ex: Cliente perguntou sobre plano X"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="ax-btn" data-dismiss="modal">Cancelar</button>
        <button class="ax-btn ax-btn-primary" onclick="axchTransfer()">Transferir</button>
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

function appendMessage(msg) {
  const wrap  = document.getElementById('axch-messages');
  const empty = wrap.querySelector('.ax-empty-state');
  if (empty) empty.remove();
  const saida = msg.direction === 'outbound';
  const hora  = new Date((msg.created_at || '').replace(' ','T')).toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'});
  const div   = document.createElement('div');
  div.className = 'ax-message ' + msg.direction + (msg.sent_by_ai ? ' ai' : '');
  div.setAttribute('data-id', msg.id || '');
  div.innerHTML = `<div>
    <div class="ax-bubble">${esc(msg.content || '')}</div>
    <div class="ax-msg-meta">${hora} ${saida ? '<i class="fa fa-check"></i>' : ''}</div>
  </div>`;
  wrap.appendChild(div);
}

function scrollBottom() {
  const w = document.getElementById('axch-messages');
  w.scrollTop = w.scrollHeight;
}

function esc(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
}

function axchPoll() {
  fetch(ADMIN_URL + 'axiomchannel/get_messages', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ contact_id: CONTACT_ID, limit: 100, offset: 0, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (!data.success) return;
    const novas = data.messages.filter(m => parseInt(m.id) > lastMsgId);
    if (novas.length) {
      novas.forEach(m => { appendMessage(m); lastMsgId = Math.max(lastMsgId, parseInt(m.id)); });
      scrollBottom();
    }
  });
}

function axchTransfer() {
  const staff = document.getElementById('transfer-staff').value;
  const note  = document.getElementById('transfer-note').value;
  fetch(ADMIN_URL + 'axiomchannel/transfer', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ contact_id: CONTACT_ID, staff_id: staff, note: note, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => { if (data.success) { $('#modal-transfer').modal('hide'); alert('Atendimento transferido!'); } });
}

function axchResolve() {
  if (!confirm('Marcar esta conversa como resolvida?')) return;
  fetch(ADMIN_URL + 'axiomchannel/update_contact_status', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ contact_id: CONTACT_ID, status: 'resolved', [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => { if (data.success) window.location.href = ADMIN_URL + 'axiomchannel'; });
}

function loadSidebarContacts() {
  const search = document.getElementById('axch-search').value;
  fetch(ADMIN_URL + 'axiomchannel/get_contacts', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ search, status: 'open', limit: 30, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (!data.success) return;
    const list = document.getElementById('axch-contact-list');
    if (!data.contacts.length) {
      list.innerHTML = '<p style="text-align:center;padding:20px;color:var(--ax-gray-400);font-size:12px">Sem conversas abertas</p>';
      return;
    }
    list.innerHTML = data.contacts.map(c => {
      const ativo = parseInt(c.id) === CONTACT_ID;
      return `<a href="${ADMIN_URL}axiomchannel/chat/${c.id}" class="ax-contact-item ${ativo ? 'active' : ''} ${!parseInt(c.is_read) ? 'unread' : ''}">
        <div class="ax-avatar ax-avatar-sm">
          <div class="ax-avatar-img">${(c.name||c.phone_number||'?')[0].toUpperCase()}</div>
        </div>
        <div class="ax-contact-info">
          <div class="ax-contact-top">
            <span class="ax-contact-name ax-truncate">${esc(c.name||c.phone_number)}</span>
          </div>
          <div class="ax-contact-preview">${esc((c.last_message||'').substring(0,40))}</div>
        </div>
      </a>`;
    }).join('');
  });
}

function axchHandleKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); axchSend(); }
}

scrollBottom();
loadSidebarContacts();
setInterval(axchPoll, 5000);
setInterval(loadSidebarContacts, 10000);

let searchTimer;
document.getElementById('axch-search').addEventListener('input', () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(loadSidebarContacts, 400);
});
</script>

<?php init_tail(); ?>
