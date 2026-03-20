<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
$type_labels = [
    'birthday'    => ['Aniversário',     '#E53E3E', 'fa-birthday-cake'],
    'invoice'     => ['Cobrança',        '#F5A623', 'fa-money'],
    'followup'    => ['Follow-up',       '#4A90D9', 'fa-refresh'],
    'inactive'    => ['Inativo',         '#805AD5', 'fa-user-times'],
    'appointment' => ['Agendamento',     '#2D7A6B', 'fa-calendar'],
    'satisfaction'=> ['Satisfação',      '#D69E2E', 'fa-star'],
];
?>
<link rel="stylesheet" href="<?= module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css') ?>">
<div id="wrapper">
<div class="content" style="padding:24px">

  <!-- Cabeçalho -->
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
    <div>
      <h4 style="margin:0;font-size:17px;font-weight:600">Automações</h4>
      <p style="margin:4px 0 0;font-size:12px;color:#6c757d">Mensagens automáticas por tipo de evento</p>
    </div>
    <div style="display:flex;gap:8px">
      <?php if (!empty($devices)): ?>
      <select class="form-control" style="width:200px;font-size:13px" onchange="location.href='<?= admin_url('axiomchannel/automations') ?>?device_id='+this.value">
        <?php foreach ($devices as $d): ?>
        <option value="<?= $d->id ?>" <?= $d->id == $device_id ? 'selected' : '' ?>><?= htmlspecialchars($d->name) ?></option>
        <?php endforeach; ?>
      </select>
      <?php endif; ?>
      <button class="btn btn-success" onclick="axAutoModal()"><i class="fa fa-plus"></i> Nova automação</button>
    </div>
  </div>

  <!-- Lista de automações -->
  <div class="panel panel-default">
    <div class="panel-body" style="padding:0">
      <?php if (empty($automations)): ?>
        <div style="padding:40px;text-align:center;color:#6c757d">
          <i class="fa fa-bolt" style="font-size:36px;opacity:.3;display:block;margin-bottom:8px"></i>
          <p>Nenhuma automação cadastrada para este dispositivo.</p>
          <button class="btn btn-success btn-sm" onclick="axAutoModal()"><i class="fa fa-plus"></i> Criar primeira automação</button>
        </div>
      <?php else: ?>
        <table class="table table-hover" style="margin:0">
          <thead>
            <tr style="background:#f8f9fa">
              <th style="font-size:12px;font-weight:600">Tipo</th>
              <th style="font-size:12px;font-weight:600">Mensagem</th>
              <th style="font-size:12px;font-weight:600">Disparar (dias)</th>
              <th style="font-size:12px;font-weight:600">Tentativas</th>
              <th style="font-size:12px;font-weight:600;text-align:center">Status</th>
              <th style="font-size:12px;font-weight:600;text-align:right">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($automations as $a):
              $info = $type_labels[$a->type] ?? [$a->type, '#2D7A6B', 'fa-bolt'];
            ?>
            <tr data-id="<?= $a->id ?>">
              <td>
                <span style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:<?= $info[1] ?>">
                  <i class="fa <?= $info[2] ?>"></i> <?= $info[0] ?>
                </span>
              </td>
              <td style="font-size:12px;color:#444;max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                <?= htmlspecialchars(substr($a->template ?? '', 0, 80)) ?>
              </td>
              <td style="font-size:12px"><?= $a->trigger_days ?> dia(s)</td>
              <td style="font-size:12px"><?= $a->max_attempts ?></td>
              <td style="text-align:center">
                <div class="ax-toggle <?= $a->is_active ? 'on' : '' ?>" data-id="<?= $a->id ?>" data-active="<?= $a->is_active ?>" onclick="axToggle(this)" style="display:inline-block;cursor:pointer;width:36px;height:20px;border-radius:10px;background:<?= $a->is_active ? '#2D7A6B' : '#ccc' ?>;position:relative;transition:background .2s">
                  <span style="position:absolute;top:3px;left:<?= $a->is_active ? '18px' : '3px' ?>;width:14px;height:14px;border-radius:50%;background:#fff;transition:left .2s"></span>
                </div>
              </td>
              <td style="text-align:right">
                <button class="btn btn-xs btn-default" onclick="axAutoEdit(<?= $a->id ?>, '<?= $a->type ?>', <?= htmlspecialchars(json_encode($a->template ?? ''), ENT_QUOTES) ?>, <?= $a->trigger_days ?>, <?= $a->max_attempts ?>)">
                  <i class="fa fa-pencil"></i>
                </button>
                <button class="btn btn-xs btn-danger" onclick="axAutoDelete(<?= $a->id ?>)">
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

</div>
</div>

<!-- Modal nova/editar automação -->
<div class="modal fade" id="axAutoModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title" id="axAutoModalTitle">Nova Automação</h4>
      </div>
      <div class="modal-body">
        <form id="axAutoForm">
          <input type="hidden" name="id" id="axAutoId" value="0">
          <input type="hidden" name="device_id" value="<?= $device_id ?>">
          <div class="form-group">
            <label style="font-size:12px;font-weight:600">Tipo de Automação</label>
            <select name="type" id="axAutoType" class="form-control" required>
              <?php foreach ($type_labels as $key => [$label, $color, $icon]): ?>
              <option value="<?= $key ?>"><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label style="font-size:12px;font-weight:600">Mensagem</label>
            <textarea name="template" id="axAutoTemplate" class="form-control" rows="4" required placeholder="Use {nome}, {data}, {link} como variáveis..."></textarea>
            <p style="font-size:11px;color:#6c757d;margin-top:4px">Variáveis: <code>{nome}</code> <code>{data}</code> <code>{link}</code> <code>{valor}</code></p>
          </div>
          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <label style="font-size:12px;font-weight:600">Disparar X dias antes/depois</label>
                <input type="number" name="trigger_days" id="axAutoTrigger" class="form-control" value="1" min="0" max="365">
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                <label style="font-size:12px;font-weight:600">Máx. tentativas</label>
                <input type="number" name="max_attempts" id="axAutoMax" class="form-control" value="3" min="1" max="10">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label style="font-size:12px;font-weight:600">
              <input type="checkbox" name="is_active" id="axAutoActive" value="1" checked> Ativa imediatamente
            </label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button class="btn btn-success" onclick="axAutoSave()"><i class="fa fa-save"></i> Salvar</button>
      </div>
    </div>
  </div>
</div>

<script>
var AX_AUTO_URL = '<?= admin_url('axiomchannel/automation_save') ?>';
var AX_DEL_URL  = '<?= admin_url('axiomchannel/automation_delete') ?>';
var AX_TOG_URL  = '<?= admin_url('axiomchannel/automation_toggle') ?>';
var CSRF_NAME   = '<?= $this->security->get_csrf_token_name() ?>';
var CSRF_TOKEN  = '<?= $this->security->get_csrf_hash() ?>';

function axAutoModal() {
    document.getElementById('axAutoModalTitle').textContent = 'Nova Automação';
    document.getElementById('axAutoId').value = '0';
    document.getElementById('axAutoForm').reset();
    $('#axAutoModal').modal('show');
}

function axAutoEdit(id, type, template, triggerDays, maxAttempts) {
    document.getElementById('axAutoModalTitle').textContent = 'Editar Automação';
    document.getElementById('axAutoId').value = id;
    document.getElementById('axAutoType').value = type;
    document.getElementById('axAutoTemplate').value = template;
    document.getElementById('axAutoTrigger').value = triggerDays;
    document.getElementById('axAutoMax').value = maxAttempts;
    document.getElementById('axAutoActive').checked = true;
    $('#axAutoModal').modal('show');
}

function axAutoSave() {
    var form = document.getElementById('axAutoForm');
    var fd = new FormData(form);
    fd.set(CSRF_NAME, CSRF_TOKEN);
    fd.set('is_active', document.getElementById('axAutoActive').checked ? 1 : 0);

    fetch(AX_AUTO_URL, {method:'POST', body: new URLSearchParams(fd)})
        .then(r => r.json()).then(d => {
            if (d.success) { location.reload(); }
        });
}

function axAutoDelete(id) {
    if (!confirm('Remover esta automação?')) return;
    fetch(AX_DEL_URL, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: CSRF_NAME + '=' + CSRF_TOKEN + '&id=' + id
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
}

function axToggle(el) {
    var id = el.dataset.id;
    var active = el.dataset.active == '1' ? 0 : 1;
    fetch(AX_TOG_URL, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: CSRF_NAME + '=' + CSRF_TOKEN + '&id=' + id + '&is_active=' + active
    }).then(r => r.json()).then(d => {
        if (d.success) {
            el.dataset.active = active;
            el.style.background = active ? '#2D7A6B' : '#ccc';
            el.querySelector('span').style.left = active ? '18px' : '3px';
        }
    });
}
</script>
<?php init_tail(); ?>
