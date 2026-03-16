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
      <?php if ($unread > 0): ?>
        <span class="ax-nav-badge"><?= $unread ?></span>
      <?php endif; ?>
    </a>
    <a href="<?= admin_url('axiomchannel/devices') ?>" class="ax-nav-item" title="Dispositivos">
      <i class="fa fa-mobile"></i>
    </a>
    <div class="ax-nav-bottom">
      <div class="ax-nav-avatar"><?= strtoupper(substr(get_staff_full_name(), 0, 1)) ?></div>
    </div>
  </nav>

  <!-- Painel de conversas -->
  <div class="ax-panel">
    <div class="ax-panel-header">
      <div class="ax-panel-title">
        Todas as Conversas
        <span class="ax-panel-count"><?= count($contacts) ?> conversas</span>
      </div>
      <div class="ax-search">
        <i class="fa fa-search ax-search-icon"></i>
        <input type="text" id="axch-search" placeholder="Buscar conversas...">
      </div>
      <div class="ax-filters">
        <select id="axch-filter-device" class="ax-filter">
          <option value="">Todos dispositivos</option>
          <?php foreach ($devices as $d): ?>
            <option value="<?= $d->id ?>"><?= htmlspecialchars($d->name) ?></option>
          <?php endforeach; ?>
        </select>
        <select id="axch-filter-status" class="ax-filter">
          <option value="open">Abertos</option>
          <option value="pending">Pendentes</option>
          <option value="resolved">Resolvidos</option>
          <option value="">Todos</option>
        </select>
      </div>
    </div>

    <div class="ax-contact-list" id="axch-contact-list">
      <?php if (empty($contacts)): ?>
        <div class="ax-empty-state">
          <div class="ax-empty-icon"><i class="fa fa-comments-o"></i></div>
          <p class="ax-empty-title">Nenhuma conversa ainda</p>
          <p class="ax-empty-desc">Aguarde mensagens chegarem pelo WhatsApp</p>
        </div>
      <?php else: ?>
        <?php foreach ($contacts as $c): ?>
          <a href="<?= admin_url('axiomchannel/chat/' . $c->id) ?>"
             class="ax-contact-item <?= !$c->is_read ? 'unread' : '' ?>"
             data-id="<?= $c->id ?>">
            <div class="ax-avatar">
              <div class="ax-avatar-img">
                <?php if ($c->avatar): ?>
                  <img src="<?= $c->avatar ?>" alt="">
                <?php else: ?>
                  <?= strtoupper(substr($c->name ?: $c->phone_number, 0, 1)) ?>
                <?php endif; ?>
              </div>
              <span class="ax-status-dot ax-status-<?= $c->status ?>"></span>
            </div>
            <div class="ax-contact-info">
              <div class="ax-contact-top">
                <span class="ax-contact-name ax-truncate">
                  <?php
                    $ch_icon = '';
                    if (!empty($c->channel)) {
                      if ($c->channel === 'facebook')  $ch_icon = '<i class="fa fa-facebook-square" style="color:#1877F2;font-size:11px;margin-right:3px" title="Facebook Messenger"></i>';
                      elseif ($c->channel === 'instagram') $ch_icon = '<i class="fa fa-instagram" style="color:#E1306C;font-size:11px;margin-right:3px" title="Instagram Direct"></i>';
                      else $ch_icon = '<i class="fa fa-whatsapp" style="color:#25D366;font-size:11px;margin-right:3px" title="WhatsApp"></i>';
                    }
                    echo $ch_icon;
                  ?>
                  <?= htmlspecialchars($c->name ?: $c->phone_number) ?>
                </span>
                <span class="ax-contact-time"><?= axch_format_time($c->last_message_at) ?></span>
              </div>
              <div class="ax-contact-preview">
                <?= htmlspecialchars(substr($c->last_message ?? '', 0, 50)) ?>
                <?php if (!$c->is_read): ?>
                  <span class="ax-unread-dot"></span>
                <?php endif; ?>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Área principal vazia -->
  <div class="ax-main ax-empty-state">
    <div class="ax-empty-icon"><i class="fa fa-comments"></i></div>
    <p class="ax-empty-title">Selecione uma conversa</p>
    <p class="ax-empty-desc">Escolha um contato na lista para iniciar o atendimento</p>
  </div>

</div>
</div>

<script>
const CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';
const CSRF_NAME  = '<?= $this->security->get_csrf_token_name() ?>';
const ADMIN_URL  = '<?= admin_url() ?>';
let searchTimer;

function axchLoadContacts() {
  const params = new URLSearchParams({
    search:    document.getElementById('axch-search').value,
    device_id: document.getElementById('axch-filter-device').value,
    status:    document.getElementById('axch-filter-status').value,
    [CSRF_NAME]: CSRF_TOKEN,
  });
  fetch(ADMIN_URL + 'axiomchannel/get_contacts', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: params
  })
  .then(r => r.json())
  .then(data => { if (data.success) renderContacts(data.contacts); })
  .catch(err => console.error('Erro ao buscar contatos:', err));
}

function renderContacts(contacts) {
  const list = document.getElementById('axch-contact-list');
  if (!contacts || !contacts.length) {
    list.innerHTML = '<div class="ax-empty-state"><div class="ax-empty-icon"><i class="fa fa-comments-o"></i></div><p class="ax-empty-title">Nenhuma conversa encontrada</p></div>';
    return;
  }
  list.innerHTML = contacts.map(c => {
    const inicial = (c.name || c.phone_number || '?')[0].toUpperCase();
    const nome    = esc(c.name || c.phone_number);
    const preview = esc((c.last_message || '').substring(0, 50));
    const tempo   = formatTime(c.last_message_at);
    const unread  = parseInt(c.is_read) === 0;
    return `<a href="${ADMIN_URL}axiomchannel/chat/${c.id}" class="ax-contact-item ${unread ? 'unread' : ''}" data-id="${c.id}">
      <div class="ax-avatar">
        <div class="ax-avatar-img">${inicial}</div>
        <span class="ax-status-dot ax-status-${c.status}"></span>
      </div>
      <div class="ax-contact-info">
        <div class="ax-contact-top">
          <span class="ax-contact-name ax-truncate">${nome}</span>
          <span class="ax-contact-time">${tempo}</span>
        </div>
        <div class="ax-contact-preview">
          ${preview}
          ${unread ? '<span class="ax-unread-dot"></span>' : ''}
        </div>
      </div>
    </a>`;
  }).join('');
}

function esc(str) {
  const d = document.createElement('div');
  d.textContent = str || '';
  return d.innerHTML;
}

function formatTime(dt) {
  if (!dt) return '';
  const d    = new Date(dt.replace(' ', 'T'));
  const diff = (Date.now() - d.getTime()) / 1000;
  if (diff < 60)    return 'agora';
  if (diff < 3600)  return Math.floor(diff / 60) + 'min';
  if (diff < 86400) return d.toLocaleTimeString('pt-BR', { hour:'2-digit', minute:'2-digit' });
  return d.toLocaleDateString('pt-BR', { day:'2-digit', month:'2-digit' });
}

document.getElementById('axch-search').addEventListener('input', () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(axchLoadContacts, 400);
});
document.getElementById('axch-filter-device').addEventListener('change', axchLoadContacts);
document.getElementById('axch-filter-status').addEventListener('change', axchLoadContacts);
setInterval(axchLoadContacts, 8000);
</script>

<?php init_tail(); ?>
