<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<link rel="stylesheet" href="<?= module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css') ?>">
<div id="wrapper">
<div class="content" style="padding:24px">

  <!-- Cabeçalho -->
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
    <div>
      <h2 style="font-size:20px;font-weight:700;color:#1e293b;margin:0">Agendamentos</h2>
      <p style="font-size:13px;color:#64748b;margin:4px 0 0">Gerencie consultas e compromissos da sua agenda</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center">
      <?php if (!empty($devices)): ?>
      <select onchange="window.location.href='<?= admin_url('axiomchannel/appointments?device_id=') ?>'+this.value"
        style="font-size:13px;padding:6px 12px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#1e293b">
        <?php foreach ($devices as $d): ?>
          <option value="<?= $d->id ?>" <?= $d->id == $device_id ? 'selected' : '' ?>><?= htmlspecialchars($d->name) ?></option>
        <?php endforeach; ?>
      </select>
      <?php endif; ?>
      <button onclick="openNewAppointment()"
        style="padding:8px 18px;background:#2D7A6B;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">
        <i class="fa fa-plus"></i> Novo agendamento
      </button>
    </div>
  </div>

  <?php if (!$device_id): ?>
    <div style="text-align:center;padding:60px;color:#94a3b8">
      <i class="fa fa-calendar" style="font-size:48px;display:block;margin-bottom:16px"></i>
      <p>Nenhum dispositivo. <a href="<?= admin_url('axiomchannel/devices') ?>">Adicionar dispositivo</a></p>
    </div>
  <?php else: ?>

  <!-- Grid: Calendário + Lista -->
  <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

    <!-- Calendário semanal -->
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden">

      <!-- Navegação da semana -->
      <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9">
        <button onclick="changeWeek(-7)" style="border:1px solid #e2e8f0;background:#fff;border-radius:8px;padding:6px 12px;cursor:pointer;color:#64748b"><i class="fa fa-chevron-left"></i></button>
        <div style="font-size:14px;font-weight:600;color:#1e293b" id="week-label"></div>
        <button onclick="changeWeek(7)"  style="border:1px solid #e2e8f0;background:#fff;border-radius:8px;padding:6px 12px;cursor:pointer;color:#64748b"><i class="fa fa-chevron-right"></i></button>
      </div>

      <!-- Grade semanal -->
      <div style="display:grid;grid-template-columns:60px repeat(7,1fr)">
        <!-- Cabeçalho dias -->
        <div style="background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:8px"></div>
        <?php
        $days_pt = ['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'];
        for ($i = 0; $i < 7; $i++):
            $day_ts  = strtotime($week_start . ' +' . $i . ' days');
            $day_str = date('Y-m-d', $day_ts);
            $is_today = $day_str === date('Y-m-d');
        ?>
        <div style="text-align:center;padding:8px 4px;border-bottom:1px solid #e2e8f0;border-left:1px solid #f1f5f9;background:<?= $is_today ? '#f0fdf9' : '#f8fafc' ?>">
          <div style="font-size:10px;color:#94a3b8;text-transform:uppercase"><?= $days_pt[$i] ?></div>
          <div style="font-size:16px;font-weight:700;color:<?= $is_today ? '#2D7A6B' : '#1e293b' ?>"><?= date('d', $day_ts) ?></div>
        </div>
        <?php endfor; ?>

        <!-- Faixas de horário -->
        <?php for ($hour = 8; $hour <= 18; $hour++): ?>
        <div style="font-size:10px;color:#94a3b8;padding:6px 6px 0;text-align:right;border-top:1px solid #f1f5f9"><?= sprintf('%02d', $hour) ?>h</div>
        <?php for ($i = 0; $i < 7; $i++):
            $day_ts  = strtotime($week_start . ' +' . $i . ' days');
            $day_str = date('Y-m-d', $day_ts);
            $hour_str = sprintf('%02d:00:00', $hour);
            $slot_dt  = $day_str . ' ' . $hour_str;
            // Verifica se há agendamento nesse slot
            $has_appt = false;
            $appt_in_slot = null;
            foreach ($appointments as $appt) {
                if (date('Y-m-d', strtotime($appt->start_datetime)) === $day_str &&
                    date('H', strtotime($appt->start_datetime)) == $hour) {
                    $has_appt = true;
                    $appt_in_slot = $appt;
                    break;
                }
            }
            $status_colors = ['pending'=>'#F6AD55','confirmed'=>'#68D391','cancelled'=>'#FC8181','completed'=>'#63B3ED'];
            $bg = $has_appt ? ($status_colors[$appt_in_slot->status] ?? '#a0aec0') : 'transparent';
        ?>
        <div style="border-top:1px solid #f1f5f9;border-left:1px solid #f1f5f9;min-height:36px;padding:2px;background:<?= date('Y-m-d', $day_ts) === date('Y-m-d') ? '#fafffe' : '#fff' ?>;position:relative"
          onclick="clickSlot('<?= $day_str ?>','<?= $hour_str ?>')">
          <?php if ($has_appt): ?>
          <div style="background:<?= $bg ?>22;border-left:3px solid <?= $bg ?>;border-radius:4px;padding:2px 5px;cursor:pointer;font-size:10px;font-weight:600;color:#1e293b;overflow:hidden;white-space:nowrap;text-overflow:ellipsis"
            onclick="event.stopPropagation();viewAppointment(<?= $appt_in_slot->id ?>)"
            title="<?= htmlspecialchars($appt_in_slot->title) ?>">
            <?= htmlspecialchars(substr($appt_in_slot->title, 0, 18)) ?>
          </div>
          <?php endif; ?>
        </div>
        <?php endfor; ?>
        <?php endfor; ?>
      </div>
    </div>

    <!-- Painel lateral -->
    <div style="display:flex;flex-direction:column;gap:16px">

      <!-- Status Google Agenda -->
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
          <i class="fa fa-google" style="font-size:18px;color:<?= $google_cal && $google_cal->is_active ? '#38A169' : '#94a3b8' ?>"></i>
          <span style="font-size:13px;font-weight:600;color:#1e293b">Google Agenda</span>
          <?php if ($google_cal && $google_cal->is_active): ?>
            <span style="font-size:10px;background:#dcfce7;color:#16a34a;padding:2px 8px;border-radius:999px;font-weight:600">Conectado</span>
          <?php else: ?>
            <span style="font-size:10px;background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:999px;font-weight:600">Desconectado</span>
          <?php endif; ?>
        </div>
        <?php if ($google_cal && $google_cal->is_active): ?>
          <p style="font-size:12px;color:#64748b;margin:0 0 10px">Conta: <strong><?= htmlspecialchars($google_cal->google_account) ?></strong></p>
        <?php endif; ?>
        <a href="<?= admin_url('axiomchannel/google_calendar_connect?device_id=' . $device_id) ?>"
          style="display:block;text-align:center;padding:8px;background:<?= $google_cal ? '#f1f5f9' : '#2D7A6B' ?>;color:<?= $google_cal ? '#64748b' : '#fff' ?>;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none">
          <i class="fa fa-<?= $google_cal ? 'refresh' : 'link' ?>"></i>
          <?= $google_cal ? 'Reconectar' : 'Conectar Google Agenda' ?>
        </a>
      </div>

      <!-- Lista do dia -->
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px">
        <div style="font-size:13px;font-weight:600;color:#1e293b;margin-bottom:12px">
          <i class="fa fa-list" style="color:#2D7A6B"></i> Próximos agendamentos
        </div>
        <?php
        $upcoming = array_filter($appointments, fn($a) => $a->status !== 'cancelled' && $a->start_datetime >= date('Y-m-d H:i:s'));
        $upcoming = array_slice(array_values($upcoming), 0, 8);
        $status_labels = ['pending'=>'Pendente','confirmed'=>'Confirmado','cancelled'=>'Cancelado','completed'=>'Concluído'];
        $status_colors = ['pending'=>'#F6AD55','confirmed'=>'#38A169','cancelled'=>'#E53E3E','completed'=>'#3182CE'];
        ?>
        <?php if (empty($upcoming)): ?>
          <p style="font-size:12px;color:#94a3b8;text-align:center;padding:20px 0">Nenhum agendamento próximo</p>
        <?php else: ?>
          <?php foreach ($upcoming as $appt): ?>
          <div style="padding:10px;border:1px solid #f1f5f9;border-radius:8px;margin-bottom:8px;cursor:pointer"
            onclick="viewAppointment(<?= $appt->id ?>)">
            <div style="display:flex;justify-content:space-between;align-items:flex-start">
              <div style="flex:1;min-width:0">
                <div style="font-size:12px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($appt->title) ?></div>
                <div style="font-size:11px;color:#64748b"><?= date('d/m H:i', strtotime($appt->start_datetime)) ?></div>
              </div>
              <span style="font-size:10px;background:<?= ($status_colors[$appt->status] ?? '#94a3b8') ?>22;color:<?= $status_colors[$appt->status] ?? '#94a3b8' ?>;padding:2px 7px;border-radius:4px;font-weight:600;white-space:nowrap">
                <?= $status_labels[$appt->status] ?? $appt->status ?>
              </span>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <?php endif; ?>
</div>
</div>

<!-- Modal novo agendamento -->
<div id="modal-appointment" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:14px;padding:24px;width:500px;max-width:95vw;max-height:90vh;overflow-y:auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0" id="modal-appt-title">Novo Agendamento</h3>
      <button onclick="closeModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#64748b">&times;</button>
    </div>

    <input type="hidden" id="appt-id" value="0">
    <div style="display:flex;flex-direction:column;gap:12px">
      <div>
        <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px">Título</label>
        <input type="text" id="appt-title" class="ax-input" placeholder="Ex: Consulta de avaliação">
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div>
          <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px">Data/hora início</label>
          <input type="datetime-local" id="appt-start" class="ax-input">
        </div>
        <div>
          <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px">Data/hora fim</label>
          <input type="datetime-local" id="appt-end" class="ax-input">
        </div>
      </div>
      <div>
        <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px">Status</label>
        <select id="appt-status" class="ax-input">
          <option value="pending">Pendente</option>
          <option value="confirmed">Confirmado</option>
          <option value="completed">Concluído</option>
          <option value="cancelled">Cancelado</option>
        </select>
      </div>
      <div>
        <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px">Descrição</label>
        <textarea id="appt-description" class="ax-textarea-field" rows="2" style="width:100%;resize:vertical" placeholder="Detalhes do agendamento..."></textarea>
      </div>
      <div>
        <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px">Observações internas</label>
        <textarea id="appt-notes" class="ax-textarea-field" rows="2" style="width:100%;resize:vertical"></textarea>
      </div>
    </div>

    <div style="display:flex;gap:8px;margin-top:20px">
      <button onclick="saveAppointment()"
        style="flex:1;padding:10px;background:#2D7A6B;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">
        Salvar
      </button>
      <button onclick="cancelAppointment()" id="btn-cancel-appt" style="display:none;padding:10px 16px;background:#fee2e2;color:#ef4444;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">
        Cancelar agendamento
      </button>
      <button onclick="closeModal()"
        style="padding:10px 16px;background:transparent;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;cursor:pointer;color:#64748b">
        Fechar
      </button>
    </div>
  </div>
</div>

<script>
const CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';
const CSRF_NAME  = '<?= $this->security->get_csrf_token_name() ?>';
const ADMIN_URL  = '<?= admin_url() ?>';
const DEVICE_ID  = <?= (int) $device_id ?>;
const APPOINTMENTS = <?= json_encode(array_map(fn($a) => [
    'id'    => $a->id,
    'title' => $a->title,
    'start' => $a->start_datetime,
    'end'   => $a->end_datetime,
    'status'=> $a->status,
], $appointments)) ?>;

let currentWeekOffset = 0;
const weekStartTs = <?= strtotime($week_start) * 1000 ?>;

function changeWeek(days) {
  const base    = new Date(weekStartTs);
  base.setDate(base.getDate() + days + currentWeekOffset);
  const y = base.getFullYear(), m = String(base.getMonth() + 1).padStart(2,'0'), d = String(base.getDate()).padStart(2,'0');
  window.location.href = ADMIN_URL + 'axiomchannel/appointments?device_id=' + DEVICE_ID + '&date=' + y + '-' + m + '-' + d;
}

document.addEventListener('DOMContentLoaded', () => {
  const ws = new Date(weekStartTs);
  const we = new Date(weekStartTs); we.setDate(we.getDate() + 6);
  const fmt = d => d.toLocaleDateString('pt-BR', {day:'2-digit',month:'short'});
  document.getElementById('week-label').textContent = fmt(ws) + ' — ' + fmt(we);
});

function openNewAppointment(date, hour) {
  document.getElementById('appt-id').value          = 0;
  document.getElementById('appt-title').value        = '';
  document.getElementById('appt-description').value  = '';
  document.getElementById('appt-notes').value        = '';
  document.getElementById('appt-status').value       = 'pending';
  document.getElementById('btn-cancel-appt').style.display = 'none';
  document.getElementById('modal-appt-title').textContent  = 'Novo Agendamento';

  if (date && hour) {
    const startVal = date + 'T' + hour.substring(0,5);
    document.getElementById('appt-start').value = startVal;
    const endTs = new Date(date + 'T' + hour).getTime() + 3600000;
    const ed = new Date(endTs);
    document.getElementById('appt-end').value = ed.toISOString().substring(0, 16);
  } else {
    document.getElementById('appt-start').value = '';
    document.getElementById('appt-end').value   = '';
  }
  document.getElementById('modal-appointment').style.display = 'flex';
}

function viewAppointment(id) {
  const appt = APPOINTMENTS.find(a => a.id == id);
  if (!appt) return;
  document.getElementById('appt-id').value          = appt.id;
  document.getElementById('appt-title').value        = appt.title;
  document.getElementById('appt-start').value        = appt.start.substring(0,16);
  document.getElementById('appt-end').value          = appt.end.substring(0,16);
  document.getElementById('appt-status').value       = appt.status;
  document.getElementById('appt-description').value  = '';
  document.getElementById('appt-notes').value        = '';
  document.getElementById('btn-cancel-appt').style.display = appt.status !== 'cancelled' ? 'block' : 'none';
  document.getElementById('modal-appt-title').textContent  = 'Editar Agendamento';
  document.getElementById('modal-appointment').style.display = 'flex';
}

function clickSlot(date, hour) {
  openNewAppointment(date, hour);
}

function closeModal() {
  document.getElementById('modal-appointment').style.display = 'none';
}

function saveAppointment() {
  const id = document.getElementById('appt-id').value;
  fetch(ADMIN_URL + 'axiomchannel/appointment_save', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: new URLSearchParams({
      id,
      device_id:       DEVICE_ID,
      contact_id:      0,
      title:           document.getElementById('appt-title').value,
      start_datetime:  document.getElementById('appt-start').value.replace('T',' ') + ':00',
      end_datetime:    document.getElementById('appt-end').value.replace('T',' ') + ':00',
      status:          document.getElementById('appt-status').value,
      description:     document.getElementById('appt-description').value,
      notes:           document.getElementById('appt-notes').value,
      [CSRF_NAME]:     CSRF_TOKEN
    })
  }).then(r => r.json()).then(res => {
    if (res.success) { closeModal(); location.reload(); }
    else alert(res.message || 'Erro ao salvar');
  });
}

function cancelAppointment() {
  const id = document.getElementById('appt-id').value;
  if (!id || !confirm('Cancelar este agendamento?')) return;
  fetch(ADMIN_URL + 'axiomchannel/appointment_cancel', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: new URLSearchParams({ id, [CSRF_NAME]: CSRF_TOKEN })
  }).then(r => r.json()).then(res => {
    if (res.success) { closeModal(); location.reload(); }
  });
}
</script>

<?php init_tail(); ?>
