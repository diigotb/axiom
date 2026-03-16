<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<link rel="stylesheet" href="<?= module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css') ?>">
<div id="wrapper">
<div class="content" style="background:var(--ax-gray-50);min-height:calc(100vh - 65px)">

  <div style="max-width:640px;margin:0 auto;padding:32px 16px">

    <!-- Header -->
    <div style="text-align:center;margin-bottom:32px">
      <div style="font-size:36px;margin-bottom:12px">🤖</div>
      <h2 style="font-size:22px;font-weight:700;color:var(--ax-gray-800);margin-bottom:8px">Vamos criar seu pipeline</h2>
      <p style="font-size:14px;color:var(--ax-gray-400);line-height:1.6">Responda algumas perguntas e a IA cria o pipeline ideal para o seu negócio — com estágios, cores e automações já configuradas.</p>
    </div>

    <!-- Progress -->
    <div style="display:flex;align-items:center;margin-bottom:28px" id="wizard-progress">
      <div class="wiz-step active" data-step="1" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:500;color:var(--ax-navy)">
        <div style="width:26px;height:26px;border-radius:50%;background:var(--ax-navy);color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700">1</div>
        Negócio
      </div>
      <div style="flex:1;height:1px;background:var(--ax-gray-200);margin:0 8px" id="line1"></div>
      <div class="wiz-step" data-step="2" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:500;color:var(--ax-gray-400)">
        <div style="width:26px;height:26px;border-radius:50%;background:var(--ax-gray-100);color:var(--ax-gray-400);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700" id="dot2">2</div>
        Atendimento
      </div>
      <div style="flex:1;height:1px;background:var(--ax-gray-200);margin:0 8px" id="line2"></div>
      <div class="wiz-step" data-step="3" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:500;color:var(--ax-gray-400)">
        <div style="width:26px;height:26px;border-radius:50%;background:var(--ax-gray-100);color:var(--ax-gray-400);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700" id="dot3">3</div>
        IA cria
      </div>
      <div style="flex:1;height:1px;background:var(--ax-gray-200);margin:0 8px" id="line3"></div>
      <div class="wiz-step" data-step="4" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:500;color:var(--ax-gray-400)">
        <div style="width:26px;height:26px;border-radius:50%;background:var(--ax-gray-100);color:var(--ax-gray-400);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700" id="dot4">4</div>
        Pronto!
      </div>
    </div>

    <!-- STEP 1: Tipo de negócio -->
    <div id="step1" class="wiz-panel">
      <div style="background:#fff;border:1px solid var(--ax-gray-200);border-radius:16px;padding:28px">
        <h3 style="font-size:17px;font-weight:600;color:var(--ax-gray-800);margin-bottom:6px">Qual é o seu tipo de negócio?</h3>
        <p style="font-size:13px;color:var(--ax-gray-400);margin-bottom:24px">Isso ajuda a criar estágios e mensagens que fazem sentido para você</p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px">
          <?php
          $types = [
            ['Clínica / Saúde','🏥'],['Salão / Beleza','💇'],
            ['Imobiliária','🏠'],['Restaurante / Food','🍕'],
            ['Educação / Cursos','📚'],['E-commerce','🛍️'],
            ['Advocacia / Jurídico','⚖️'],['Agência / Marketing','📱'],
            ['Consultoria','💼'],['Outro','✨'],
          ];
          foreach ($types as $t): ?>
            <div class="biz-option" onclick="selectBiz(this,'<?= $t[0] ?>')"
              style="border:2px solid var(--ax-gray-200);border-radius:12px;padding:14px;cursor:pointer;transition:all .15s;display:flex;align-items:center;gap:10px">
              <span style="font-size:22px"><?= $t[1] ?></span>
              <span style="font-size:13px;font-weight:500;color:var(--ax-gray-700)"><?= $t[0] ?></span>
            </div>
          <?php endforeach; ?>
        </div>

        <div style="margin-bottom:20px">
          <label style="font-size:12px;font-weight:500;color:var(--ax-gray-600);display:block;margin-bottom:6px">Nome do pipeline</label>
          <input type="text" id="pipeline-name" class="ax-input" placeholder="Ex: Atendimento Clínica, Vendas Imóveis..." value="">
        </div>

        <?php if (!empty($devices)): ?>
        <div style="margin-bottom:20px">
          <label style="font-size:12px;font-weight:500;color:var(--ax-gray-600);display:block;margin-bottom:6px">Vincular ao dispositivo (opcional)</label>
          <select id="pipeline-device" class="ax-input">
            <option value="">Todos os dispositivos</option>
            <?php foreach ($devices as $d): ?>
              <option value="<?= $d->id ?>"><?= htmlspecialchars($d->name) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>

        <div style="display:flex;justify-content:flex-end">
          <button class="ax-btn ax-btn-primary" onclick="goStep(2)" id="btn-step1" disabled>
            Próximo →
          </button>
        </div>
      </div>
    </div>

    <!-- STEP 2: Perguntas de atendimento -->
    <div id="step2" class="wiz-panel" style="display:none">
      <div style="background:#fff;border:1px solid var(--ax-gray-200);border-radius:16px;padding:28px">
        <h3 style="font-size:17px;font-weight:600;color:var(--ax-gray-800);margin-bottom:6px">Me conta um pouco mais 🎯</h3>
        <p style="font-size:13px;color:var(--ax-gray-400);margin-bottom:24px">Quanto mais você detalhar, melhor será o pipeline criado</p>

        <div style="margin-bottom:20px">
          <label style="font-size:13px;font-weight:500;color:var(--ax-gray-600);display:block;margin-bottom:10px">Como seus clientes chegam até você?</label>
          <div style="display:flex;flex-wrap:wrap;gap:8px" id="channels">
            <?php foreach (['WhatsApp','Instagram','Google','Indicação','Site','Facebook','Anúncios'] as $c): ?>
              <div class="chip" onclick="toggleChip(this)" data-group="channels"
                style="padding:7px 14px;border:1px solid var(--ax-gray-200);border-radius:20px;font-size:12px;color:var(--ax-gray-600);cursor:pointer;background:#fff;transition:all .15s">
                <?= $c ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div style="margin-bottom:20px">
          <label style="font-size:13px;font-weight:500;color:var(--ax-gray-600);display:block;margin-bottom:10px">Qual é o maior desafio no atendimento?</label>
          <div style="display:flex;flex-wrap:wrap;gap:8px" id="challenges">
            <?php foreach (['Clientes que somem','Confirmar agendamentos','Explicar valores','Reagendar faltosos','Converter em venda','Follow-up pós-venda'] as $c): ?>
              <div class="chip" onclick="toggleChip(this)" data-group="challenges"
                style="padding:7px 14px;border:1px solid var(--ax-gray-200);border-radius:20px;font-size:12px;color:var(--ax-gray-600);cursor:pointer;background:#fff;transition:all .15s">
                <?= $c ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div style="margin-bottom:24px">
          <label style="font-size:13px;font-weight:500;color:var(--ax-gray-600);display:block;margin-bottom:10px">Quanto tempo leva do primeiro contato até a venda/serviço?</label>
          <div style="display:flex;flex-wrap:wrap;gap:8px" id="cycle">
            <?php foreach (['No mesmo dia','1 a 3 dias','1 semana','2 semanas','Mais de 1 mês'] as $c): ?>
              <div class="chip chip-single" onclick="toggleChipSingle(this,'cycle')" data-group="cycle"
                style="padding:7px 14px;border:1px solid var(--ax-gray-200);border-radius:20px;font-size:12px;color:var(--ax-gray-600);cursor:pointer;background:#fff;transition:all .15s">
                <?= $c ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div style="display:flex;justify-content:space-between">
          <button class="ax-btn" onclick="goStep(1)">← Voltar</button>
          <button class="ax-btn ax-btn-primary" onclick="generatePipeline()">
            🤖 Criar meu pipeline com IA
          </button>
        </div>
      </div>
    </div>

    <!-- STEP 3: Loading IA -->
    <div id="step3" class="wiz-panel" style="display:none">
      <div style="background:#fff;border:1px solid var(--ax-gray-200);border-radius:16px;padding:40px;text-align:center">
        <div style="font-size:48px;margin-bottom:16px;animation:spin 2s linear infinite;display:inline-block">🤖</div>
        <h3 style="font-size:17px;font-weight:600;color:var(--ax-gray-800);margin-bottom:8px">Criando seu pipeline...</h3>
        <p style="font-size:13px;color:var(--ax-gray-400);margin-bottom:24px">O assistente está analisando seu negócio</p>
        <div id="loading-steps" style="text-align:left;max-width:320px;margin:0 auto">
          <div class="l-step" style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:var(--ax-gray-50);border-radius:8px;margin-bottom:6px;font-size:12px;color:var(--ax-teal)">✓ Analisou o tipo de negócio</div>
          <div class="l-step" id="ls2" style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:var(--ax-gray-50);border-radius:8px;margin-bottom:6px;font-size:12px;color:var(--ax-gray-400)">○ Identificando padrões...</div>
          <div class="l-step" id="ls3" style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:var(--ax-gray-50);border-radius:8px;margin-bottom:6px;font-size:12px;color:var(--ax-gray-400)">○ Criando estágios</div>
          <div class="l-step" id="ls4" style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:var(--ax-gray-50);border-radius:8px;font-size:12px;color:var(--ax-gray-400)">○ Configurando automações da IA</div>
        </div>
      </div>
    </div>

    <!-- STEP 4: Resultado -->
    <div id="step4" class="wiz-panel" style="display:none">
      <div style="background:var(--ax-teal-light);border:1px solid var(--ax-teal-mid);border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:var(--ax-teal)">
        🤖 Pipeline criado! Você pode renomear ou reordenar os estágios antes de ativar.
      </div>
      <div style="background:#fff;border:1px solid var(--ax-gray-200);border-radius:16px;padding:28px">
        <h3 style="font-size:17px;font-weight:600;color:var(--ax-gray-800);margin-bottom:4px">Seu pipeline está pronto! ✨</h3>
        <p style="font-size:13px;color:var(--ax-gray-400);margin-bottom:20px" id="pipeline-subtitle"></p>
        <div id="stages-result" style="display:flex;flex-direction:column;gap:8px;margin-bottom:24px"></div>
        <div style="display:flex;justify-content:space-between">
          <button class="ax-btn" onclick="goStep(2)">← Ajustar respostas</button>
          <button class="ax-btn ax-btn-navy" onclick="savePipeline()">
            Ativar este pipeline ✓
          </button>
        </div>
      </div>
    </div>

  </div>
</div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
.biz-option:hover { border-color: var(--ax-teal-mid) !important; background: var(--ax-teal-light) !important; }
.biz-option.selected { border-color: var(--ax-teal) !important; background: var(--ax-teal-light) !important; }
.chip:hover { border-color: var(--ax-teal) !important; color: var(--ax-teal) !important; }
.chip.on { background: var(--ax-teal-light) !important; border-color: var(--ax-teal) !important; color: var(--ax-teal) !important; font-weight: 500 !important; }
</style>

<script>
const CSRF_TOKEN = '<?= $this->security->get_csrf_hash() ?>';
const CSRF_NAME  = '<?= $this->security->get_csrf_token_name() ?>';
const ADMIN_URL  = '<?= admin_url() ?>';

let selectedBiz   = '';
let generatedData = null;

function selectBiz(el, name) {
  document.querySelectorAll('.biz-option').forEach(o => o.classList.remove('selected'));
  el.classList.add('selected');
  selectedBiz = name;
  if (!document.getElementById('pipeline-name').value) {
    document.getElementById('pipeline-name').value = 'Pipeline ' + name;
  }
  document.getElementById('btn-step1').disabled = false;
}

function toggleChip(el) {
  el.classList.toggle('on');
}

function toggleChipSingle(el, group) {
  document.querySelectorAll(`[data-group="${group}"]`).forEach(c => c.classList.remove('on'));
  el.classList.add('on');
}

function getChips(group) {
  return [...document.querySelectorAll(`[data-group="${group}"].on`)].map(c => c.textContent.trim()).join(', ');
}

function goStep(n) {
  document.querySelectorAll('.wiz-panel').forEach(p => p.style.display = 'none');
  document.getElementById('step' + n).style.display = 'block';

  [1,2,3,4].forEach(i => {
    const dot = document.getElementById('dot' + i) || document.querySelector(`[data-step="${i}"] div`);
    if (!dot) return;
    if (i < n) {
      dot.style.background = 'var(--ax-teal)';
      dot.style.color = '#fff';
      dot.textContent = '✓';
      if (document.getElementById('line' + i)) document.getElementById('line' + i).style.background = 'var(--ax-teal)';
    } else if (i === n) {
      dot.style.background = 'var(--ax-navy)';
      dot.style.color = '#fff';
    } else {
      dot.style.background = 'var(--ax-gray-100)';
      dot.style.color = 'var(--ax-gray-400)';
    }
  });
}

function generatePipeline() {
  const channels   = getChips('channels');
  const challenges = getChips('challenges');
  const cycle      = getChips('cycle');

  goStep(3);

  // Anima os loading steps
  let i = 1;
  const steps = ['ls2','ls3','ls4'];
  const interval = setInterval(() => {
    if (i <= steps.length) {
      document.getElementById(steps[i-1]).style.color = 'var(--ax-teal)';
      document.getElementById(steps[i-1]).textContent = '✓ ' + document.getElementById(steps[i-1]).textContent.replace('○ ','');
      i++;
    }
  }, 800);

  fetch(ADMIN_URL + 'axiomchannel/pipeline_generate', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({
      business_type: selectedBiz,
      pipeline_name: document.getElementById('pipeline-name').value,
      channels, challenges, cycle,
      [CSRF_NAME]: CSRF_TOKEN
    })
  })
  .then(r => r.json())
  .then(data => {
    clearInterval(interval);
    if (data.success) {
      generatedData = data.pipeline;
      renderResult();
      setTimeout(() => goStep(4), 500);
    } else {
      alert(data.message || 'Erro ao gerar pipeline');
      goStep(2);
    }
  })
  .catch(() => { clearInterval(interval); alert('Erro de conexão'); goStep(2); });
}

const PALETTE = ['#E53E3E','#DD6B20','#D69E2E','#38A169','#2D7A6B','#3182CE','#5A67D8','#805AD5','#D53F8C','#1B3A4B','#4A5568','#718096','#F6AD55','#68D391','#63B3ED','#B794F4'];

function renderResult() {
  const stages = generatedData.stages;
  const name   = document.getElementById('pipeline-name').value || generatedData.pipeline_name;

  document.getElementById('pipeline-subtitle').textContent =
    `${stages.length} estágios criados para ${selectedBiz} com IA configurada em cada etapa`;

  document.getElementById('stages-result').innerHTML = stages.map((s, i) => `
    <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:var(--ax-gray-50);border:1px solid var(--ax-gray-200);border-radius:10px;position:relative">
      <div style="position:relative;flex-shrink:0">
        <div id="swatch-${i}" onclick="togglePalette(event,${i})"
          title="Clique para mudar a cor"
          style="width:22px;height:22px;border-radius:50%;background:${s.color || '#2D7A6B'};cursor:pointer;border:2px solid rgba(0,0,0,.15);flex-shrink:0;transition:transform .15s"
          onmouseenter="this.style.transform='scale(1.15)'"
          onmouseleave="this.style.transform='scale(1)'"></div>
        <div id="palette-${i}" style="display:none;position:absolute;top:28px;left:0;background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:8px;box-shadow:0 4px 20px rgba(0,0,0,.15);z-index:200;width:152px">
          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:4px">
            ${PALETTE.map(c => `<div onclick="pickColor(event,${i},'${c}')"
              style="width:28px;height:28px;border-radius:6px;background:${c};cursor:pointer;transition:transform .1s"
              onmouseenter="this.style.transform='scale(1.2)'"
              onmouseleave="this.style.transform='scale(1)'"></div>`).join('')}
          </div>
        </div>
      </div>
      <div style="flex:1">
        <div style="font-size:13px;font-weight:600;color:var(--ax-gray-800)">${s.name}</div>
        <div style="font-size:11px;color:var(--ax-gray-400);margin-top:2px">${s.ai_action || ''}</div>
      </div>
      <span style="background:var(--ax-teal-light);color:var(--ax-teal);font-size:10px;font-weight:600;padding:2px 8px;border-radius:4px;flex-shrink:0">IA ativa</span>
    </div>
  `).join('');
}

function togglePalette(e, i) {
  e.stopPropagation();
  document.querySelectorAll('[id^="palette-"]').forEach(p => {
    if (p.id !== 'palette-' + i) p.style.display = 'none';
  });
  const pal = document.getElementById('palette-' + i);
  pal.style.display = pal.style.display === 'none' ? 'block' : 'none';
}

function pickColor(e, i, color) {
  e.stopPropagation();
  generatedData.stages[i].color = color;
  document.getElementById('swatch-' + i).style.background = color;
  document.getElementById('palette-' + i).style.display = 'none';
}

document.addEventListener('click', () => {
  document.querySelectorAll('[id^="palette-"]').forEach(p => p.style.display = 'none');
});

function savePipeline() {
  const name     = document.getElementById('pipeline-name').value;
  const deviceId = document.getElementById('pipeline-device') ?
    document.getElementById('pipeline-device').value : '';

  fetch(ADMIN_URL + 'axiomchannel/pipeline_save', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: new URLSearchParams({
      name, device_id: deviceId,
      stages: JSON.stringify(generatedData.stages),
      [CSRF_NAME]: CSRF_TOKEN
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      window.location.href = ADMIN_URL + 'axiomchannel/pipeline/' + data.pipeline_id;
    } else {
      alert(data.message || 'Erro ao salvar');
    }
  });
}
</script>

<?php init_tail(); ?>
