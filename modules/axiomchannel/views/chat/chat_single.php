<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<link rel="stylesheet" href="<?= module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css') ?>">
<div id="wrapper">
<div class="content ax-app">

  <!-- Navegação lateral -->
  <nav class="ax-nav">
    <a href="<?= admin_url('axiomchannel') ?>" class="ax-nav-logo">AX</a>
    <a href="<?= admin_url('axiomchannel/inbox') ?>" class="ax-nav-item active" title="Todas as Conversas">
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
      <div class="ax-panel-title">Todas as Conversas</div>
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
        <button id="btn-toggle-ai" class="ax-btn ax-btn-sm" onclick="axchToggleAI()" title="Ativar/Desativar IA para este contato"
          style="background:<?= !empty($contact->ai_disabled) ? 'rgba(229,62,62,.15)' : 'rgba(45,122,107,.15)' ?>;color:<?= !empty($contact->ai_disabled) ? '#E53E3E' : '#2D7A6B' ?>;border:1px solid <?= !empty($contact->ai_disabled) ? '#E53E3E' : '#2D7A6B' ?>">
          <i class="fa fa-robot"></i> IA <?= !empty($contact->ai_disabled) ? 'Off' : 'On' ?>
        </button>
        <button class="ax-btn ax-btn-navy ax-btn-sm" onclick="axchResolve()">
          <i class="fa fa-check"></i> Resolver
        </button>
      </div>
    </div>

    <!-- Barra de Pipeline CRM -->
    <?php if (!empty($pipelines)): ?>
    <div class="ax-pipeline-bar" id="ax-pipeline-bar" style="display:flex;align-items:center;gap:8px;padding:8px 16px;background:#f8f9fa;border-bottom:1px solid #e5e7eb;flex-wrap:wrap;min-height:44px">
      <?php if ($crm_lead && $pipeline): ?>
        <span style="font-size:11px;font-weight:600;color:#6b7280;white-space:nowrap;margin-right:4px"><?= htmlspecialchars($pipeline->name) ?></span>
        <?php foreach ($stages as $stage): ?>
          <?php
            $ativo = ((int)$crm_lead->stage_id === (int)$stage->id);
            $cor   = !empty($stage->color) ? htmlspecialchars($stage->color) : '#2D7A6B';
            if ($ativo) {
              $style_btn = "padding:5px 16px;border-radius:20px;font-size:12px;font-weight:700;border:2px solid {$cor};background:{$cor};color:#ffffff;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.25);white-space:nowrap;transition:all .2s";
            } else {
              $style_btn = "padding:5px 16px;border-radius:20px;font-size:12px;font-weight:600;border:2px solid {$cor};background:transparent;color:{$cor};cursor:pointer;transition:all .2s;white-space:nowrap";
            }
          ?>
          <button
            data-stage-id="<?= (int)$stage->id ?>"
            data-color="<?= $cor ?>"
            data-active="<?= $ativo ? '1' : '0' ?>"
            onclick="axchMoveStage(<?= (int)$pipeline->id ?>, <?= (int)$stage->id ?>)"
            onmouseenter="if(this.dataset.active==='0'){this.style.background=this.dataset.color;this.style.color='#fff'}"
            onmouseleave="if(this.dataset.active==='0'){this.style.background='transparent';this.style.color=this.dataset.color}"
            style="<?= $style_btn ?>"
          ><?= htmlspecialchars($stage->name) ?></button>
        <?php endforeach; ?>
      <?php else: ?>
        <span style="font-size:11px;font-weight:600;color:var(--ax-gray-500,#6b7280);white-space:nowrap">Pipeline CRM</span>
        <select id="ax-pipeline-select" style="font-size:12px;padding:3px 8px;border:1px solid var(--ax-border,#e5e7eb);border-radius:6px;background:#fff">
          <?php foreach ($pipelines as $p): ?>
            <option value="<?= (int)$p->id ?>"><?= htmlspecialchars($p->name) ?></option>
          <?php endforeach; ?>
        </select>
        <button onclick="axchAddToPipeline()" style="font-size:12px;padding:5px 14px;border-radius:20px;border:none;background:var(--ax-primary,#2563eb);color:#fff;cursor:pointer;font-weight:600">
          <i class="fa fa-plus"></i> Adicionar ao pipeline
        </button>
      <?php endif; ?>
    </div>
    <?php endif; ?>

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

  <!-- Copiloto IA -->
  <div class="ax-copilot" id="ax-copilot">
    <div class="ax-copilot-header">
      <span class="ax-copilot-title">
        <i class="fa fa-magic" style="color:var(--ax-teal);margin-right:5px"></i>Copiloto IA
      </span>
      <button class="ax-copilot-refresh" id="copilot-refresh-btn" onclick="axchCopilotRefresh()" title="Atualizar análise">
        <i class="fa fa-refresh" id="copilot-refresh-icon"></i>
      </button>
    </div>
    <div class="ax-copilot-body" id="copilot-body">
      <div class="ax-copilot-loader">
        <i class="fa fa-spinner fa-spin"></i> Analisando...
      </div>
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
const CSRF_TOKEN  = '<?= $this->security->get_csrf_hash() ?>';
const CSRF_NAME   = '<?= $this->security->get_csrf_token_name() ?>';
const CONTACT_ID  = <?= (int)$contact->id ?>;
const ADMIN_URL   = '<?= admin_url() ?>';
let lastMsgId     = <?= !empty($messages) ? (int)end($messages)->id : 0 ?>;
let isSending     = false;
let currentLeadId = <?= ($crm_lead ? (int)$crm_lead->id : 'null') ?>;

function axchMoveStage(pipeline_id, stage_id) {
  fetch(ADMIN_URL + 'axiomchannel/lead_move_from_chat', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ contact_id: CONTACT_ID, pipeline_id, stage_id, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (!data.success) return;
    currentLeadId = data.lead_id;
    document.querySelectorAll('.ax-stage-btn').forEach(btn => {
      const isActive = parseInt(btn.dataset.stageId) === parseInt(stage_id);
      const color    = btn.dataset.color;
      if (isActive) {
        btn.style.background  = color;
        btn.style.color       = '#ffffff';
        btn.style.fontWeight  = '700';
        btn.style.boxShadow   = '0 2px 8px rgba(0,0,0,.25)';
        btn.style.borderColor = color;
        btn.dataset.active    = '1';
      } else {
        btn.style.background  = 'transparent';
        btn.style.color       = color;
        btn.style.fontWeight  = '600';
        btn.style.boxShadow   = 'none';
        btn.style.borderColor = color;
        btn.dataset.active    = '0';
      }
    });
  })
  .catch(() => alert('Erro ao mover estágio'));
}

function axchAddToPipeline() {
  const pipeline_id = document.getElementById('ax-pipeline-select').value;
  fetch(ADMIN_URL + 'axiomchannel/lead_move_from_chat', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ contact_id: CONTACT_ID, pipeline_id, stage_id: 0, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => { if (data.success) window.location.reload(); })
  .catch(() => alert('Erro ao adicionar ao pipeline'));
}

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
      clearTimeout(_copilotRefreshTimer);
      _copilotRefreshTimer = setTimeout(axchCopilotLoad, 3000);
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
    body: new URLSearchParams({ contact_id: CONTACT_ID, since_id: lastMsgId, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (!data.success || !data.messages.length) return;
    data.messages.forEach(m => {
      appendMessage(m);
      lastMsgId = Math.max(lastMsgId, parseInt(m.id));
    });
    scrollBottom();
  })
  .catch(function() {});
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
setInterval(axchPoll, 3000);
setInterval(loadSidebarContacts, 10000);

// ============================================================
// COPILOTO IA
// ============================================================
let _copilotSuggestions = [];
let _copilotRefreshTimer;

function axchToggleAI() {
    fetch(ADMIN_URL + 'axiomchannel/toggle_contact_ai', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams({ contact_id: CONTACT_ID, [CSRF_NAME]: CSRF_TOKEN })
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;
        const btn = document.getElementById('btn-toggle-ai');
        if (data.ai_disabled) {
            btn.style.background = 'rgba(229,62,62,.15)';
            btn.style.color = '#E53E3E';
            btn.style.border = '1px solid #E53E3E';
            btn.innerHTML = '<i class="fa fa-robot"></i> IA Off';
        } else {
            btn.style.background = 'rgba(45,122,107,.15)';
            btn.style.color = '#2D7A6B';
            btn.style.border = '1px solid #2D7A6B';
            btn.innerHTML = '<i class="fa fa-robot"></i> IA On';
        }
    });
}

function axchCopilotRefresh() {
  const icon = document.getElementById('copilot-refresh-icon');
  icon.classList.add('fa-spin');
  axchCopilotLoad(function() { icon.classList.remove('fa-spin'); });
}

function axchCopilotLoad(cb) {
  const body = document.getElementById('copilot-body');
  body.innerHTML = '<div class="ax-copilot-loader"><i class="fa fa-spinner fa-spin"></i> Analisando...</div>';
  fetch(ADMIN_URL + 'axiomchannel/copilot_analyze', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ contact_id: CONTACT_ID, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (cb) cb();
    if (!data.success) {
      body.innerHTML = '<p style="font-size:11px;color:var(--ax-gray-400);padding:12px;text-align:center">' + esc(data.message || 'Erro na análise') + '</p>';
      return;
    }
    _renderCopilot(data);
  })
  .catch(function() {
    if (cb) cb();
    body.innerHTML = '<p style="font-size:11px;color:var(--ax-danger);padding:12px;text-align:center">Erro de conexão</p>';
  });
}

function _renderCopilot(data) {
  const body     = document.getElementById('copilot-body');
  const sEmoji   = { neutro:'😐', interessado:'😊', hesitante:'🤔', pronto:'🎯' };
  const sLabel   = { neutro:'Neutro', interessado:'Interessado', hesitante:'Hesitante', pronto:'Pronto p/ fechar' };
  const s        = data.sentiment || 'neutro';

  let html = '<div class="ax-copilot-section">'
    + '<div class="ax-copilot-label">Sentimento</div>'
    + '<div><span class="ax-sentiment ax-sentiment-' + s + '">'
    + (sEmoji[s] || '😐') + ' ' + (sLabel[s] || s)
    + '</span></div></div>';

  if (data.tags && data.tags.length) {
    html += '<div class="ax-copilot-section"><div class="ax-copilot-label">Tópicos</div><div>'
      + data.tags.map(function(t) { return '<span class="ax-tag-pill">' + esc(t) + '</span>'; }).join('')
      + '</div></div>';
  }

  if (data.suggestions && data.suggestions.length) {
    html += '<div class="ax-copilot-section"><div class="ax-copilot-label">Sugestões</div>';
    data.suggestions.forEach(function(sg, i) {
      html += '<div class="ax-sugg-card">'
        + '<div class="ax-sugg-label">' + esc(sg.label) + '</div>'
        + '<div class="ax-sugg-text">' + esc(sg.text) + '</div>'
        + '<button class="ax-sugg-btn" onclick="axchUseSuggestion(' + i + ')">'
        + '<i class="fa fa-arrow-up"></i> Usar</button></div>';
    });
    html += '</div>';
    _copilotSuggestions = data.suggestions;
  }

  body.innerHTML = html;
}

function axchUseSuggestion(i) {
  const sg = _copilotSuggestions[i];
  if (!sg) return;
  const input = document.getElementById('axch-input');
  input.value = sg.text;
  input.style.height = 'auto';
  input.style.height = Math.min(input.scrollHeight, 100) + 'px';
  input.focus();
}

// Carrega copiloto 1.5s após a página abrir
setTimeout(axchCopilotLoad, 1500);
// Atualiza copiloto a cada 30s
setInterval(axchCopilotLoad, 30000);

let searchTimer;
document.getElementById('axch-search').addEventListener('input', () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(loadSidebarContacts, 400);
});
</script>

<?php init_tail(); ?>
