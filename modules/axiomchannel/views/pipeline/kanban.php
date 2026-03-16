<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<link rel="stylesheet" href="<?= module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css') ?>">
<style>
.kanban-wrap{display:flex;gap:14px;overflow-x:auto;padding:0 0 20px;flex:1;align-items:flex-start}
.kanban-col{min-width:260px;max-width:260px;flex-shrink:0}
.kanban-col-header{padding:10px 14px;border-radius:10px 10px 0 0;display:flex;align-items:center;justify-content:space-between}
.kanban-col-body{background:#fff;border:1px solid var(--ax-gray-200);border-top:none;border-radius:0 0 10px 10px;padding:10px;min-height:200px}
.kanban-card{background:#fff;border:1px solid var(--ax-gray-200);border-radius:8px;padding:10px 12px;margin-bottom:8px;cursor:grab;transition:all .15s;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.kanban-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.08);transform:translateY(-1px)}
.kanban-card.dragging{opacity:.5;cursor:grabbing}
.kanban-card-name{font-size:13px;font-weight:500;color:var(--ax-gray-800);margin-bottom:4px}
.kanban-card-phone{font-size:11px;color:var(--ax-gray-400);display:flex;align-items:center;gap:4px}
.kanban-card-value{font-size:11px;font-weight:600;color:var(--ax-teal);margin-top:4px}
.kanban-card-footer{display:flex;align-items:center;justify-content:space-between;margin-top:8px}
.kanban-card-ai{background:var(--ax-teal-light);color:var(--ax-teal);font-size:9px;font-weight:600;padding:1px 6px;border-radius:3px}
.kanban-add{width:100%;padding:8px;border:1px dashed var(--ax-gray-200);border-radius:6px;background:transparent;color:var(--ax-gray-400);font-size:12px;cursor:pointer;margin-top:4px;transition:all .15s}
.kanban-add:hover{border-color:var(--ax-teal);color:var(--ax-teal);background:var(--ax-teal-light)}
.drop-zone{border:2px dashed var(--ax-teal);border-radius:6px;padding:10px;margin-bottom:8px;display:none;text-align:center;font-size:11px;color:var(--ax-teal)}
.drop-zone.active{display:block}
.col-count{background:rgba(0,0,0,.12);color:#fff;border-radius:10px;padding:1px 8px;font-size:10px;font-weight:600}
</style>

<div id="wrapper">
<div class="content ax-app" style="flex-direction:column">

  <!-- Header -->
  <div class="ax-page-header">
    <div style="display:flex;align-items:center;gap:12px">
      <!-- Seletor de pipeline -->
      <select id="pipeline-select" class="ax-input" style="width:auto;font-weight:600;color:var(--ax-navy)"
        onchange="window.location.href='<?= admin_url('axiomchannel/pipeline/') ?>'+this.value">
        <?php foreach ($pipelines as $p): ?>
          <option value="<?= $p->id ?>" <?= $p->id == $pipeline->id ? 'selected' : '' ?>>
            <?= htmlspecialchars($p->name) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div>
        <h4 class="ax-page-title"><?= htmlspecialchars($pipeline->name) ?></h4>
        <p class="ax-page-sub"><?= count($stages) ?> estágios · <?= array_sum(array_map('count', $leads_by_stage)) ?> leads</p>
      </div>
    </div>
    <div style="display:flex;gap:8px">
      <a href="<?= admin_url('axiomchannel/pipeline_wizard') ?>" class="ax-btn ax-btn-sm">
        <i class="fa fa-plus"></i> Novo pipeline
      </a>
      <button class="ax-btn ax-btn-primary ax-btn-sm" onclick="openAddLead()">
        <i class="fa fa-user-plus"></i> Adicionar lead
      </button>
    </div>
  </div>

  <!-- Kanban -->
  <div style="flex:1;overflow:hidden;padding:20px">
    <div class="kanban-wrap" id="kanban">

      <?php foreach ($stages as $stage):
        $leads = $leads_by_stage[$stage->id] ?? [];
        // Gera cor de fundo mais clara baseada na cor do estágio
      ?>
      <div class="kanban-col" data-stage="<?= $stage->id ?>">
        <div class="kanban-col-header" id="hdr-<?= $stage->id ?>" style="background:<?= $stage->color ?>22;border:1px solid <?= $stage->color ?>44">
          <div style="display:flex;align-items:center;gap:8px">
            <div class="stage-dot" style="width:10px;height:10px;border-radius:50%;background:<?= $stage->color ?>"></div>
            <span class="stage-name" style="font-size:12px;font-weight:600;color:var(--ax-gray-800)"><?= htmlspecialchars($stage->name) ?></span>
          </div>
          <div style="display:flex;align-items:center;gap:6px">
            <span class="col-count" style="background:<?= $stage->color ?>"><?= count($leads) ?></span>
            <button
              onclick="openEditStage(<?= $stage->id ?>,'<?= addslashes(htmlspecialchars($stage->name)) ?>','<?= $stage->color ?>')"
              title="Editar estágio"
              style="background:transparent;border:none;cursor:pointer;color:rgba(0,0,0,.35);padding:2px 4px;line-height:1;border-radius:4px;transition:color .15s"
              onmouseenter="this.style.color='rgba(0,0,0,.7)'"
              onmouseleave="this.style.color='rgba(0,0,0,.35)'"
            ><i class="fa fa-pencil" style="font-size:11px"></i></button>
          </div>
        </div>
        <div class="kanban-col-body" id="col-<?= $stage->id ?>"
          ondragover="allowDrop(event)" ondrop="dropLead(event,<?= $stage->id ?>)">

          <div class="drop-zone" id="dz-<?= $stage->id ?>">Soltar aqui</div>

          <?php foreach ($leads as $lead): ?>
            <div class="kanban-card" draggable="true"
              data-id="<?= $lead->id ?>" data-stage="<?= $stage->id ?>"
              ondragstart="dragStart(event)" ondragend="dragEnd(event)"
              onclick="openLead(<?= $lead->id ?>)">
              <div class="kanban-card-name"><?= htmlspecialchars($lead->name ?: 'Lead #' . $lead->id) ?></div>
              <div class="kanban-card-phone">
                <i class="fa fa-phone" style="font-size:10px"></i>
                <?= htmlspecialchars($lead->phone ?: 'Sem telefone') ?>
              </div>
              <?php if ($lead->value > 0): ?>
                <div class="kanban-card-value">R$ <?= number_format($lead->value, 2, ',', '.') ?></div>
              <?php endif; ?>
              <div class="kanban-card-footer">
                <?php if ($stage->auto_move): ?>
                  <span class="kanban-card-ai">🤖 IA ativa</span>
                <?php else: ?>
                  <span></span>
                <?php endif; ?>
                <?php if ($lead->firstname): ?>
                  <span style="font-size:10px;color:var(--ax-gray-400)"><?= $lead->firstname ?></span>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>

          <button class="kanban-add" onclick="openAddLead(<?= $stage->id ?>)">
            + Adicionar lead
          </button>
        </div>
      </div>
      <?php endforeach; ?>

    </div>
  </div>

</div>
</div>

<!-- Modal: Adicionar lead -->
<div class="modal fade" id="modal-add-lead" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Adicionar lead</h4>
      </div>
      <div class="modal-body">
        <div class="ax-form-group">
          <label class="ax-label">Nome</label>
          <input type="text" id="lead-name" class="ax-input" placeholder="Nome do contato">
        </div>
        <div class="ax-form-group">
          <label class="ax-label">Telefone</label>
          <input type="text" id="lead-phone" class="ax-input" placeholder="(17) 99999-9999">
        </div>
        <div class="ax-form-group">
          <label class="ax-label">Valor estimado (R$)</label>
          <input type="number" id="lead-value" class="ax-input" placeholder="0,00" value="0">
        </div>
        <div class="ax-form-group">
          <label class="ax-label">Estágio inicial</label>
          <select id="lead-stage" class="ax-input">
            <?php foreach ($stages as $s): ?>
              <option value="<?= $s->id ?>"><?= htmlspecialchars($s->name) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="ax-btn" data-dismiss="modal">Cancelar</button>
        <button class="ax-btn ax-btn-primary" onclick="saveLead()">Adicionar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Editar estágio -->
<div class="modal fade" id="modal-edit-stage" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Editar estágio</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit-stage-id">
        <div class="ax-form-group">
          <label class="ax-label">Nome do estágio</label>
          <input type="text" id="edit-stage-name" class="ax-input">
        </div>
        <div class="ax-form-group">
          <label class="ax-label">Cor</label>
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
            <div id="edit-stage-swatch" style="width:28px;height:28px;border-radius:50%;border:2px solid rgba(0,0,0,.12);flex-shrink:0"></div>
            <span id="edit-stage-color-val" style="font-size:12px;color:#6b7280;font-family:monospace"></span>
          </div>
          <div id="edit-stage-palette" style="display:grid;grid-template-columns:repeat(8,1fr);gap:5px"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="ax-btn" data-dismiss="modal">Cancelar</button>
        <button class="ax-btn ax-btn-primary" onclick="saveStage()">Salvar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Ver lead -->
<div class="modal fade" id="modal-lead" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="modal-lead-title">Lead</h4>
      </div>
      <div class="modal-body" id="modal-lead-body"></div>
      <div class="modal-footer">
        <button class="ax-btn" data-dismiss="modal">Fechar</button>
        <button class="ax-btn ax-btn-primary" onclick="updateLead()">Salvar</button>
      </div>
    </div>
  </div>
</div>

<script>
const CSRF_TOKEN   = '<?= $this->security->get_csrf_hash() ?>';
const CSRF_NAME    = '<?= $this->security->get_csrf_token_name() ?>';
const ADMIN_URL    = '<?= admin_url() ?>';
const PIPELINE_ID  = <?= (int)$pipeline->id ?>;
const PALETTE      = ['#E53E3E','#DD6B20','#D69E2E','#38A169','#2D7A6B','#3182CE','#5A67D8','#805AD5','#D53F8C','#1B3A4B','#4A5568','#718096','#F6AD55','#68D391','#63B3ED','#B794F4'];

let draggedId     = null;
let draggedStage  = null;
let addStageId    = null;
let currentLeadId = null;
let editStageColor = '';

function openEditStage(id, name, color) {
  document.getElementById('edit-stage-id').value   = id;
  document.getElementById('edit-stage-name').value = name;
  editStageColor = color || '#2D7A6B';
  _updateEditSwatch(editStageColor);

  document.getElementById('edit-stage-palette').innerHTML = PALETTE.map(c => `
    <div onclick="selectEditColor('${c}')"
      id="epal-${c.replace('#','')}"
      style="width:28px;height:28px;border-radius:6px;background:${c};cursor:pointer;border:3px solid ${c === editStageColor ? '#1a202c' : 'transparent'};box-sizing:border-box;transition:transform .1s"
      onmouseenter="this.style.transform='scale(1.2)'"
      onmouseleave="this.style.transform='scale(1)'"></div>
  `).join('');

  $('#modal-edit-stage').modal('show');
}

function _updateEditSwatch(color) {
  document.getElementById('edit-stage-swatch').style.background = color;
  document.getElementById('edit-stage-color-val').textContent   = color;
  editStageColor = color;
}

function selectEditColor(color) {
  PALETTE.forEach(c => {
    const el = document.getElementById('epal-' + c.replace('#',''));
    if (el) el.style.borderColor = c === color ? '#1a202c' : 'transparent';
  });
  _updateEditSwatch(color);
}

function saveStage() {
  const id   = document.getElementById('edit-stage-id').value;
  const name = document.getElementById('edit-stage-name').value.trim();
  if (!name) { alert('Preencha o nome'); return; }

  fetch(ADMIN_URL + 'axiomchannel/update_stage', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ stage_id: id, name, color: editStageColor, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (!data.success) { alert('Erro ao salvar estágio'); return; }
    $('#modal-edit-stage').modal('hide');

    // Atualiza visualmente sem recarregar
    const hdr = document.getElementById('hdr-' + id);
    if (hdr) {
      hdr.style.background   = editStageColor + '22';
      hdr.style.borderColor  = editStageColor + '44';
      hdr.querySelector('.stage-dot').style.background  = editStageColor;
      hdr.querySelector('.stage-name').textContent      = name;
      hdr.querySelector('.col-count').style.background  = editStageColor;
    }
  })
  .catch(() => alert('Erro de conexão'));
}

// ---- DRAG AND DROP ----
function dragStart(e) {
  draggedId    = e.currentTarget.dataset.id;
  draggedStage = e.currentTarget.dataset.stage;
  e.currentTarget.classList.add('dragging');
  document.querySelectorAll('.drop-zone').forEach(z => z.classList.add('active'));
}

function dragEnd(e) {
  e.currentTarget.classList.remove('dragging');
  document.querySelectorAll('.drop-zone').forEach(z => z.classList.remove('active'));
}

function allowDrop(e) { e.preventDefault(); }

function dropLead(e, stageId) {
  e.preventDefault();
  if (!draggedId || draggedStage == stageId) return;

  // Move no DOM
  const card = document.querySelector(`[data-id="${draggedId}"]`);
  const col  = document.getElementById('col-' + stageId);
  const btn  = col.querySelector('.kanban-add');
  col.insertBefore(card, btn);
  card.dataset.stage = stageId;

  // Atualiza contadores
  updateCounts();

  // Salva no servidor
  fetch(ADMIN_URL + 'axiomchannel/lead_move', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({ lead_id: draggedId, stage_id: stageId, [CSRF_NAME]: CSRF_TOKEN })
  });

  draggedId = null;
  draggedStage = null;
}

function updateCounts() {
  document.querySelectorAll('.kanban-col').forEach(col => {
    const stageId = col.dataset.stage;
    const count   = col.querySelectorAll('.kanban-card').length;
    const badge   = col.querySelector('.col-count');
    if (badge) badge.textContent = count;
  });
}

// ---- ADICIONAR LEAD ----
function openAddLead(stageId) {
  addStageId = stageId || <?= !empty($stages) ? $stages[0]->id : 0 ?>;
  if (stageId) {
    document.getElementById('lead-stage').value = stageId;
  }
  $('#modal-add-lead').modal('show');
}

function saveLead() {
  const name  = document.getElementById('lead-name').value.trim();
  const phone = document.getElementById('lead-phone').value.trim();
  const value = document.getElementById('lead-value').value;
  const stage = document.getElementById('lead-stage').value;

  if (!name) { alert('Preencha o nome'); return; }

  fetch(ADMIN_URL + 'axiomchannel/lead_create', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({
      contact_id: 0, pipeline_id: PIPELINE_ID,
      stage_id: stage, name, phone, value,
      [CSRF_NAME]: CSRF_TOKEN
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      $('#modal-add-lead').modal('hide');
      window.location.reload();
    } else {
      alert('Erro ao adicionar lead');
    }
  });
}

// ---- VER LEAD ----
function openLead(id) {
  currentLeadId = id;
  // Busca dados do card
  const card = document.querySelector(`[data-id="${id}"]`);
  const name  = card.querySelector('.kanban-card-name').textContent;

  document.getElementById('modal-lead-title').textContent = name;
  document.getElementById('modal-lead-body').innerHTML = `
    <div class="ax-form-group">
      <label class="ax-label">Nome</label>
      <input type="text" id="edit-name" class="ax-input" value="${name}">
    </div>
    <div class="ax-form-group">
      <label class="ax-label">Notas</label>
      <textarea class="ax-textarea-field" id="edit-notes" rows="3" placeholder="Observações sobre este lead..."></textarea>
    </div>
    <div class="ax-form-group">
      <label class="ax-label">Valor (R$)</label>
      <input type="number" id="edit-value" class="ax-input" value="0">
    </div>
  `;
  $('#modal-lead').modal('show');
}

function updateLead() {
  fetch(ADMIN_URL + 'axiomchannel/lead_update', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({
      id: currentLeadId,
      name: document.getElementById('edit-name').value,
      notes: document.getElementById('edit-notes').value,
      value: document.getElementById('edit-value').value,
      [CSRF_NAME]: CSRF_TOKEN
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      $('#modal-lead').modal('hide');
      // Atualiza nome no card
      const card = document.querySelector(`[data-id="${currentLeadId}"] .kanban-card-name`);
      if (card) card.textContent = document.getElementById('edit-name').value;
    }
  });
}
</script>

<?php init_tail(); ?>
