<?php
/**
 * ============================================================
 * AULA DE PHP — VIEWS
 * ============================================================
 * View = o HTML que o usuário vê. Mistura PHP com HTML.
 * Recebe variáveis do controller via $data[].
 *   Controller: $data['devices'] = [...]
 *   View:       $devices já está disponível diretamente
 *
 * init_head() = abre o HTML, carrega CSS do Perfex
 * init_tail() = fecha o HTML, carrega JS do Perfex
 * Sempre em par — nunca esquecer o init_tail()!
 *
 * <?= $variavel ?> = atalho para <?php echo $variavel; ?>
 */
defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<div id="wrapper">
  <div class="content" id="axch-app" style="padding:0;display:flex;height:calc(100vh - 65px);overflow:hidden">

    <!-- ===================== SIDEBAR ===================== -->
    <div class="axch-sidebar" style="width:310px;min-width:310px;background:#fff;border-right:1px solid #e8e8e8;display:flex;flex-direction:column;overflow:hidden">

      <!-- Cabeçalho da sidebar -->
      <div style="padding:16px;border-bottom:1px solid #e8e8e8;flex-shrink:0">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
          <h5 style="margin:0;font-weight:700;color:#5b5ef4">
            <i class="fa fa-comments"></i> AxiomChannel
          </h5>
          <?php if ($unread > 0): ?>
            <span style="background:#e74c3c;color:#fff;border-radius:10px;padding:2px 8px;font-size:11px;font-weight:600">
              <?= $unread ?>
            </span>
          <?php endif; ?>
        </div>

        <!-- Campo de busca -->
        <div style="position:relative;margin-bottom:10px">
          <input type="text" id="axch-search" class="form-control input-sm"
                 placeholder="Buscar conversas..." style="padding-left:30px">
          <i class="fa fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#aaa;font-size:12px"></i>
        </div>

        <!-- Filtros -->
        <div style="display:flex;gap:6px">
          <select id="axch-filter-device" class="form-control input-sm" style="flex:1">
            <option value="">Todos dispositivos</option>
            <?php foreach ($devices as $device): ?>
              <option value="<?= $device->id ?>"><?= htmlspecialchars($device->name) ?></option>
            <?php endforeach; ?>
          </select>
          <select id="axch-filter-status" class="form-control input-sm" style="flex:1">
            <option value="open">Abertos</option>
            <option value="pending">Pendentes</option>
            <option value="resolved">Resolvidos</option>
            <option value="">Todos</option>
          </select>
        </div>
      </div>

      <!-- Lista de contatos -->
      <div id="axch-contact-list" style="flex:1;overflow-y:auto">
        <?php if (empty($contacts)): ?>
          <div style="text-align:center;padding:40px 20px;color:#ccc">
            <i class="fa fa-comments-o" style="font-size:32px;display:block;margin-bottom:10px"></i>
            <p>Nenhuma conversa ainda.<br>Aguarde mensagens chegarem.</p>
          </div>
        <?php else: ?>
          <?php foreach ($contacts as $c): ?>
            <a href="<?= admin_url('axiomchannel/chat/' . $c->id) ?>"
               style="display:flex;align-items:center;gap:10px;padding:12px 16px;text-decoration:none;color:inherit;border-bottom:1px solid #f5f5f5;<?= !$c->is_read ? 'background:#f0f0ff' : '' ?>"
               data-id="<?= $c->id ?>">
              <!-- Avatar com inicial do nome -->
              <div style="position:relative;flex-shrink:0">
                <div style="width:40px;height:40px;border-radius:50%;background:#ebebff;color:#5b5ef4;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px">
                  <?= strtoupper(substr($c->name ?: $c->phone_number, 0, 1)) ?>
                </div>
                <!-- Bolinha de status: verde=aberto, amarelo=pendente, cinza=resolvido -->
                <span style="position:absolute;bottom:1px;right:1px;width:9px;height:9px;border-radius:50%;border:2px solid #fff;background:<?= $c->status === 'open' ? '#2ecc71' : ($c->status === 'pending' ? '#f39c12' : '#bdc3c7') ?>"></span>
              </div>
              <!-- Info da conversa -->
              <div style="flex:1;min-width:0">
                <div style="display:flex;justify-content:space-between;margin-bottom:2px">
                  <span style="font-size:13px;font-weight:<?= !$c->is_read ? '700' : '500' ?>;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px">
                    <?= htmlspecialchars($c->name ?: $c->phone_number) ?>
                  </span>
                  <span style="font-size:11px;color:#999;flex-shrink:0">
                    <?= axch_format_time($c->last_message_at) ?>
                  </span>
                </div>
                <div style="font-size:12px;color:#777;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                  <?= htmlspecialchars(substr($c->last_message ?? '', 0, 50)) ?>
                  <?php if (!$c->is_read): ?>
                    <span style="float:right;width:8px;height:8px;border-radius:50%;background:#5b5ef4;display:inline-block;margin-top:3px"></span>
                  <?php endif; ?>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- ===================== PAINEL VAZIO ===================== -->
    <div style="flex:1;display:flex;align-items:center;justify-content:center;background:#f9f9f9;flex-direction:column;color:#ccc">
      <i class="fa fa-comments" style="font-size:52px;margin-bottom:16px;color:#5b5ef4;opacity:0.25"></i>
      <h4 style="font-weight:400;color:#bbb">Selecione uma conversa</h4>
      <p style="font-size:13px">Escolha um contato na lista para atender</p>
    </div>

  </div>
</div>

<script>
const CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';
const CSRF_NAME  = '<?= $this->security->get_csrf_token_name() ?>';
// ============================================================
// AULA DE JS — Como funciona o polling (atualização automática)
// ============================================================
// Polling = a cada X segundos, pergunta ao servidor se tem novidade.
// É simples e funciona bem para até ~100 usuários simultâneos.
// No futuro usaremos WebSocket (tempo real de verdade).

const ADMIN_URL   = '<?= admin_url() ?>';
let searchTimer;

// Carrega a lista de contatos via AJAX com os filtros aplicados
function axchLoadContacts() {
  const params = new URLSearchParams({
    search:    document.getElementById('axch-search').value,
    device_id: document.getElementById('axch-filter-device').value,
    status:    document.getElementById('axch-filter-status').value,
  });

  fetch(ADMIN_URL + 'axiomchannel/get_contacts', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest' // isso faz is_ajax_request() retornar true
    },
    body: (() => { params.append(CSRF_NAME, CSRF_TOKEN); return params; })()
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) renderContacts(data.contacts);
  })
  .catch(err => console.error('Erro ao buscar contatos:', err));
}

function renderContacts(contacts) {
  const list = document.getElementById('axch-contact-list');

  if (!contacts || contacts.length === 0) {
    list.innerHTML = '<div style="text-align:center;padding:40px;color:#ccc"><i class="fa fa-comments-o" style="font-size:28px;display:block;margin-bottom:8px"></i><p>Nenhuma conversa encontrada</p></div>';
    return;
  }

  list.innerHTML = contacts.map(c => {
    const inicial = (c.name || c.phone_number || '?')[0].toUpperCase();
    const nome    = esc(c.name || c.phone_number);
    const preview = esc((c.last_message || '').substring(0, 50));
    const tempo   = formatTime(c.last_message_at);
    const unread  = parseInt(c.is_read) === 0;
    const statusColor = c.status === 'open' ? '#2ecc71' : c.status === 'pending' ? '#f39c12' : '#bdc3c7';

    return `<a href="${ADMIN_URL}axiomchannel/chat/${c.id}"
       style="display:flex;align-items:center;gap:10px;padding:12px 16px;text-decoration:none;color:inherit;border-bottom:1px solid #f5f5f5;${unread ? 'background:#f0f0ff' : ''}"
       data-id="${c.id}">
      <div style="position:relative;flex-shrink:0">
        <div style="width:40px;height:40px;border-radius:50%;background:#ebebff;color:#5b5ef4;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px">${inicial}</div>
        <span style="position:absolute;bottom:1px;right:1px;width:9px;height:9px;border-radius:50%;border:2px solid #fff;background:${statusColor}"></span>
      </div>
      <div style="flex:1;min-width:0">
        <div style="display:flex;justify-content:space-between;margin-bottom:2px">
          <span style="font-size:13px;font-weight:${unread ? '700' : '500'};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px">${nome}</span>
          <span style="font-size:11px;color:#999;flex-shrink:0">${tempo}</span>
        </div>
        <div style="font-size:12px;color:#777;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
          ${preview}
          ${unread ? '<span style="float:right;width:8px;height:8px;border-radius:50%;background:#5b5ef4;display:inline-block;margin-top:3px"></span>' : ''}
        </div>
      </div>
    </a>`;
  }).join('');
}

// Escapa HTML para evitar XSS (nunca colocar dado do usuário direto no HTML)
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

// Eventos dos filtros
document.getElementById('axch-search').addEventListener('input', () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(axchLoadContacts, 400); // espera 400ms antes de buscar
});
document.getElementById('axch-filter-device').addEventListener('change', axchLoadContacts);
document.getElementById('axch-filter-status').addEventListener('change', axchLoadContacts);

// Polling: atualiza a lista a cada 8 segundos
setInterval(axchLoadContacts, 8000);
</script>

<?php init_tail(); ?>
