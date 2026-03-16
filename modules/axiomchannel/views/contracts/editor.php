<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<link rel="stylesheet" href="<?= module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css') ?>">
<div id="wrapper">
<div class="content" style="padding:24px">

  <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
    <a href="<?= admin_url('axiomchannel/contracts?device_id=' . $device_id) ?>"
      style="color:#64748b;text-decoration:none;font-size:13px"><i class="fa fa-arrow-left"></i> Contratos</a>
    <span style="color:#e2e8f0">/</span>
    <h2 style="font-size:18px;font-weight:700;color:#1e293b;margin:0"><?= $contract ? 'Editar Contrato' : 'Novo Contrato' ?></h2>
  </div>

  <div style="display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:start">

    <!-- Editor principal -->
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px">
      <input type="hidden" id="contract-id" value="<?= $contract ? (int)$contract->id : 0 ?>">
      <input type="hidden" id="contract-device" value="<?= (int)$device_id ?>">
      <input type="hidden" id="contract-contact" value="<?= $contract ? (int)$contract->contact_id : 0 ?>">

      <div style="margin-bottom:16px">
        <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Título do contrato</label>
        <input type="text" id="contract-title" class="ax-input"
          value="<?= htmlspecialchars($contract->title ?? '') ?>"
          placeholder="Ex: Contrato de Prestação de Serviços">
      </div>

      <div style="margin-bottom:16px">
        <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Contato</label>
        <select id="contract-contact-sel" class="ax-input" onchange="document.getElementById('contract-contact').value=this.value">
          <option value="0">— Selecione o contato —</option>
          <?php foreach ($contacts as $ct): ?>
            <option value="<?= $ct->id ?>" <?= ($contract && $contract->contact_id == $ct->id) ? 'selected' : '' ?>>
              <?= htmlspecialchars($ct->name ?: $ct->phone_number) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="margin-bottom:8px">
        <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Conteúdo do contrato</label>
      </div>

      <!-- Editor TinyMCE (usa o que o Perfex já carrega) -->
      <textarea id="contract-content" name="contract-content" style="width:100%;min-height:420px;display:none"><?= htmlspecialchars($contract->content ?? '') ?></textarea>
      <div id="contract-editor" contenteditable="true"
        style="border:1px solid #e2e8f0;border-radius:8px;padding:16px;min-height:420px;font-size:14px;line-height:1.7;color:#1e293b;outline:none;white-space:pre-wrap"
        oninput="document.getElementById('contract-content').value=this.innerText"><?= $contract ? htmlspecialchars($contract->content) : '' ?></div>

      <div style="display:flex;gap:8px;margin-top:16px">
        <button onclick="saveContract('draft')"
          style="padding:10px 20px;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">
          <i class="fa fa-save"></i> Salvar rascunho
        </button>
        <button onclick="saveContract('send')"
          style="padding:10px 20px;background:#2D7A6B;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">
          <i class="fa fa-whatsapp"></i> Salvar e enviar
        </button>
      </div>
    </div>

    <!-- Painel lateral -->
    <div style="display:flex;flex-direction:column;gap:16px">

      <!-- Templates -->
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px">
        <div style="font-size:13px;font-weight:600;color:#1e293b;margin-bottom:12px">Templates</div>
        <?php if (!empty($templates)): ?>
        <select id="tmpl-sel" class="ax-input" style="margin-bottom:10px">
          <option value="">— Selecione um template —</option>
          <?php foreach ($templates as $t): ?>
            <option value="<?= $t->id ?>" data-content="<?= htmlspecialchars($t->content) ?>" data-name="<?= htmlspecialchars($t->name) ?>">
              <?= htmlspecialchars($t->name) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button onclick="applyTemplate()"
          style="width:100%;padding:8px;background:#2D7A6B;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer">
          Aplicar template
        </button>
        <?php else: ?>
          <p style="font-size:12px;color:#94a3b8">Nenhum template disponível.</p>
        <?php endif; ?>
        <a href="<?= admin_url('axiomchannel/contract_templates?device_id=' . $device_id) ?>"
          style="display:block;text-align:center;margin-top:10px;font-size:12px;color:#2D7A6B;text-decoration:none">
          Gerenciar templates →
        </a>
      </div>

      <!-- Variáveis -->
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px">
        <div style="font-size:13px;font-weight:600;color:#1e293b;margin-bottom:10px">Variáveis disponíveis</div>
        <p style="font-size:11px;color:#64748b;margin:0 0 10px">Clique para inserir no cursor:</p>
        <?php $vars = ['[nome]','[cpf]','[empresa]','[data]','[valor]']; ?>
        <?php foreach ($vars as $v): ?>
          <button onclick="insertVar('<?= $v ?>')"
            style="display:block;width:100%;text-align:left;padding:5px 8px;margin-bottom:4px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:5px;font-size:12px;font-family:monospace;cursor:pointer;color:#1e293b">
            <?= $v ?>
          </button>
        <?php endforeach; ?>
      </div>

    </div>
  </div>

</div>
</div>

<script>
const CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';
const CSRF_NAME  = '<?= $this->security->get_csrf_token_name() ?>';
const ADMIN_URL  = '<?= admin_url() ?>';

function applyTemplate() {
  const sel = document.getElementById('tmpl-sel');
  const opt = sel.options[sel.selectedIndex];
  if (!opt || !opt.value) return;
  const content = opt.getAttribute('data-content');
  const editor  = document.getElementById('contract-editor');
  editor.innerText = content;
  document.getElementById('contract-content').value = content;
  if (!document.getElementById('contract-title').value) {
    document.getElementById('contract-title').value = opt.getAttribute('data-name');
  }
}

function insertVar(v) {
  const editor = document.getElementById('contract-editor');
  editor.focus();
  const sel = window.getSelection();
  if (sel.rangeCount) {
    const range = sel.getRangeAt(0);
    range.deleteContents();
    range.insertNode(document.createTextNode(v));
    range.collapse(false);
  } else {
    editor.innerText += v;
  }
  document.getElementById('contract-content').value = editor.innerText;
}

function saveContract(action) {
  const id      = document.getElementById('contract-id').value;
  const contact = document.getElementById('contract-contact').value;
  const title   = document.getElementById('contract-title').value;
  const content = document.getElementById('contract-editor').innerText;

  if (!title) { alert('Informe o título do contrato'); return; }
  if (!content.trim()) { alert('O contrato não pode estar vazio'); return; }

  fetch(ADMIN_URL + 'axiomchannel/contract_save', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: new URLSearchParams({
      id, contact_id: contact,
      device_id: document.getElementById('contract-device').value,
      title, content, [CSRF_NAME]: CSRF_TOKEN
    })
  })
  .then(r => r.json())
  .then(res => {
    if (!res.success) { alert(res.message || 'Erro ao salvar'); return; }
    document.getElementById('contract-id').value = res.id;

    if (action === 'send') {
      if (!contact || contact == 0) { alert('Selecione um contato antes de enviar'); return; }
      fetch(ADMIN_URL + 'axiomchannel/contract_send', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body: new URLSearchParams({ id: res.id, [CSRF_NAME]: CSRF_TOKEN })
      })
      .then(r => r.json())
      .then(r2 => {
        if (r2.success) {
          alert('Contrato enviado!\nLink: ' + r2.sign_url);
          window.location.href = ADMIN_URL + 'axiomchannel/contracts?device_id=' + document.getElementById('contract-device').value;
        } else {
          alert(r2.message || 'Erro ao enviar');
        }
      });
    } else {
      alert('Rascunho salvo!');
    }
  });
}
</script>

<?php init_tail(); ?>
