<?php defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>
<link rel="stylesheet" href="<?= module_dir_url(AXIOMCHANNEL_MODULE, 'assets/css/axiomchannel.css') ?>">
<div id="wrapper">
<div class="content" style="padding:24px">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div>
      <h2 style="font-size:20px;font-weight:700;color:#1e293b;margin:0">Assistente IA</h2>
      <p style="font-size:13px;color:#64748b;margin:4px 0 0">Configure a inteligência artificial do seu atendimento</p>
    </div>
    <?php if (!empty($devices)): ?>
    <select onchange="window.location.href='<?= admin_url('axiomchannel/assistant/') ?>'+this.value"
      style="font-size:13px;padding:6px 12px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#1e293b">
      <?php foreach ($devices as $d): ?>
        <option value="<?= $d->id ?>" <?= $d->id == $device_id ? 'selected' : '' ?>>
          <?= htmlspecialchars($d->name) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <?php endif; ?>
  </div>

  <?php if (!$device_id): ?>
    <div style="text-align:center;padding:60px 20px;color:#94a3b8">
      <i class="fa fa-robot" style="font-size:48px;margin-bottom:16px;display:block"></i>
      <p style="font-size:15px">Nenhum dispositivo cadastrado. <a href="<?= admin_url('axiomchannel/devices') ?>">Adicionar dispositivo</a></p>
    </div>
  <?php else: ?>

  <div style="display:grid;grid-template-columns:380px 1fr;gap:20px;align-items:start">

    <!-- ======================================================
         COLUNA ESQUERDA — Configurações gerais
    ====================================================== -->
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px">

      <!-- Toggle ativar/desativar -->
      <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:#f8fafc;border-radius:10px;margin-bottom:20px">
        <div>
          <div style="font-size:14px;font-weight:600;color:#1e293b">IA Ativa</div>
          <div style="font-size:12px;color:#64748b">Responde automaticamente</div>
        </div>
        <label style="position:relative;display:inline-block;width:46px;height:26px;cursor:pointer">
          <input type="checkbox" id="chk-active" <?= ($assistant && $assistant->is_active) ? 'checked' : '' ?>
            style="opacity:0;width:0;height:0" onchange="toggleActive()">
          <span id="toggle-track" style="position:absolute;top:0;left:0;right:0;bottom:0;border-radius:13px;transition:.2s;
            background:<?= ($assistant && $assistant->is_active) ? '#2D7A6B' : '#cbd5e1' ?>">
            <span style="position:absolute;top:3px;left:<?= ($assistant && $assistant->is_active) ? '23px' : '3px' ?>;
              width:20px;height:20px;background:#fff;border-radius:50%;transition:.2s;box-shadow:0 1px 3px rgba(0,0,0,.2)"
              id="toggle-thumb"></span>
          </span>
        </label>
      </div>

      <input type="hidden" id="f-device-id" value="<?= (int)$device_id ?>">
      <input type="hidden" id="f-assistant-id" value="<?= $assistant ? (int)$assistant->id : 0 ?>">

      <div style="display:flex;flex-direction:column;gap:14px">

        <div>
          <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Nome do assistente</label>
          <input type="text" id="f-name" class="ax-input"
            value="<?= htmlspecialchars($assistant->name ?? 'Assistente IA') ?>"
            placeholder="Ex: Ana, Max, Assistente RT...">
        </div>

        <div>
          <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Nome do negócio</label>
          <input type="text" id="f-business-name" class="ax-input"
            value="<?= htmlspecialchars($assistant->business_name ?? '') ?>"
            placeholder="Ex: Clínica Sorrisos, RT Marketing...">
        </div>

        <div>
          <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Tipo de negócio</label>
          <input type="text" id="f-business-type" class="ax-input"
            value="<?= htmlspecialchars($assistant->business_type ?? '') ?>"
            placeholder="Ex: Clínica odontológica, Agência digital...">
        </div>

        <div>
          <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Tom de voz</label>
          <select id="f-tone" class="ax-input">
            <?php foreach (['profissional' => 'Profissional', 'descontraido' => 'Descontraído', 'persuasivo' => 'Persuasivo', 'tecnico' => 'Técnico'] as $val => $label): ?>
              <option value="<?= $val ?>" <?= ($assistant && $assistant->tone_of_voice === $val) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Uso de emojis</label>
          <select id="f-emoji" class="ax-input">
            <option value="1" <?= ($assistant && !empty($assistant->emoji_enabled)) ? 'selected' : '' ?>>Habilitado</option>
            <option value="0" <?= ($assistant && empty($assistant->emoji_enabled)) ? 'selected' : '' ?>>Desabilitado</option>
          </select>
        </div>

        <div>
          <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Mensagem de saudação</label>
          <textarea id="f-greeting" class="ax-textarea-field" rows="3"
            placeholder="Ex: Olá! Sou a Ana, assistente virtual da Clínica Sorrisos. Como posso te ajudar hoje? 😊"
            style="width:100%;resize:vertical"><?= htmlspecialchars($assistant->greeting_message ?? '') ?></textarea>
        </div>

        <div>
          <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Palavras que transferem para humano</label>
          <input type="text" id="f-transfer-kw" class="ax-input"
            value="<?= htmlspecialchars($assistant->transfer_keywords ?? '') ?>"
            placeholder="falar com humano, quero atendente, atendimento humano">
          <p style="font-size:11px;color:#94a3b8;margin:4px 0 0">Separe por vírgula. Quando o cliente usar, transfere ao humano.</p>
        </div>

        <div>
          <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px">Horário de funcionamento</label>
          <div style="display:flex;align-items:center;gap:8px">
            <input type="time" id="f-hours-start" class="ax-input" style="flex:1"
              value="<?= $assistant->working_hours_start ?? '08:00' ?>">
            <span style="color:#94a3b8;font-size:12px">até</span>
            <input type="time" id="f-hours-end" class="ax-input" style="flex:1"
              value="<?= $assistant->working_hours_end ?? '18:00' ?>">
          </div>
        </div>

        <!-- ── Tipo de assistente ── -->
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px">
          <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px">Tipo de assistente</label>
          <?php
          $asst_types_saved = $assistant ? json_decode($assistant->assistant_types ?? '[]', true) : [];
          $asst_types = [
            'lead_qualification' => 'Qualificação de leads',
            'scheduling'         => 'Agendamento automático',
            'contract'           => 'Geração de contrato',
            'billing'            => 'Cobrança / fatura',
            'general'            => 'Atendimento geral',
            'satisfaction'       => 'Pesquisa de satisfação',
          ];
          ?>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
            <?php foreach ($asst_types as $val => $label): ?>
            <label style="display:flex;align-items:center;gap:7px;font-size:12px;color:#1e293b;cursor:pointer;padding:5px 8px;border:1px solid #e2e8f0;border-radius:6px;background:#fff">
              <input type="checkbox" class="asst-type-cb" value="<?= $val ?>"
                <?= in_array($val, (array)$asst_types_saved) ? 'checked' : '' ?>
                onchange="onTypeChange()">
              <?= $label ?>
            </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- ── Seção condicional: Agendamento ── -->
        <div id="section-scheduling" style="display:<?= in_array('scheduling', (array)$asst_types_saved) ? 'block' : 'none' ?>;background:#f0fdf9;border:1px solid #a7f3d0;border-radius:10px;padding:14px">
          <div style="font-size:12px;font-weight:700;color:#2D7A6B;margin-bottom:12px"><i class="fa fa-calendar"></i> Configuração de Agendamento</div>
          <div style="display:flex;flex-direction:column;gap:10px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div>
                <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:3px">Duração (min)</label>
                <input type="number" id="f-appt-duration" class="ax-input" min="15" max="480" step="15"
                  value="<?= (int)($assistant->appointment_duration ?? 60) ?>">
              </div>
              <div>
                <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:3px">Intervalo entre slots (min)</label>
                <input type="number" id="f-appt-interval" class="ax-input" min="0" max="120" step="5"
                  value="<?= (int)($assistant->appointment_interval ?? 30) ?>">
              </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div>
                <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:3px">Início disponível</label>
                <input type="time" id="f-appt-start" class="ax-input"
                  value="<?= substr($assistant->appointment_start ?? '08:00:00', 0, 5) ?>">
              </div>
              <div>
                <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:3px">Fim disponível</label>
                <input type="time" id="f-appt-end" class="ax-input"
                  value="<?= substr($assistant->appointment_end ?? '18:00:00', 0, 5) ?>">
              </div>
            </div>
            <div>
              <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:6px">Dias da semana ativos</label>
              <?php
              $days_saved = explode(',', $assistant->appointment_days ?? '1,2,3,4,5');
              $days_list  = ['1'=>'Seg','2'=>'Ter','3'=>'Qua','4'=>'Qui','5'=>'Sex','6'=>'Sáb','7'=>'Dom'];
              ?>
              <div style="display:flex;gap:5px;flex-wrap:wrap">
                <?php foreach ($days_list as $dv => $dn): ?>
                <label style="display:flex;align-items:center;gap:4px;font-size:11px;font-weight:600;cursor:pointer;padding:4px 9px;border:1px solid #e2e8f0;border-radius:5px;background:#fff;color:#1e293b">
                  <input type="checkbox" class="appt-day-cb" value="<?= $dv ?>"
                    <?= in_array($dv, $days_saved) ? 'checked' : '' ?>>
                  <?= $dn ?>
                </label>
                <?php endforeach; ?>
              </div>
            </div>
            <div>
              <?php $gcal = $device_id ? $this->axiomchannel_model->get_google_calendar($device_id) : null; ?>
              <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px">Google Agenda</label>
              <div style="display:flex;align-items:center;gap:10px">
                <span style="font-size:12px;color:<?= $gcal && $gcal->is_active ? '#38A169' : '#94a3b8' ?>">
                  <i class="fa fa-circle"></i> <?= $gcal && $gcal->is_active ? 'Conectado' : 'Desconectado' ?>
                </span>
                <a href="<?= admin_url('axiomchannel/google_calendar_connect?device_id=' . (int)$device_id) ?>"
                  style="font-size:12px;color:#2D7A6B;font-weight:600;text-decoration:none">
                  <?= $gcal && $gcal->is_active ? 'Reconectar' : 'Conectar →' ?>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- ── Seção condicional: Contrato ── -->
        <div id="section-contract" style="display:<?= in_array('contract', (array)$asst_types_saved) ? 'block' : 'none' ?>;background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:14px">
          <?php $templates = $this->axiomchannel_model->get_contract_templates($device_id); ?>
          <div style="font-size:12px;font-weight:700;color:#0369a1;margin-bottom:12px"><i class="fa fa-file-text"></i> Configuração de Contrato</div>
          <div>
            <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px">Template padrão</label>
            <select id="f-contract-template" class="ax-input">
              <option value="">— Nenhum —</option>
              <?php foreach ($templates as $t): ?>
                <option value="<?= $t->id ?>" <?= ($assistant && $assistant->default_contract_template_id == $t->id) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($t->name) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <a href="<?= admin_url('axiomchannel/contract_templates?device_id=' . (int)$device_id) ?>"
              style="display:block;font-size:11px;color:#2D7A6B;margin-top:6px;text-decoration:none">
              Gerenciar templates de contrato →
            </a>
          </div>
        </div>

        <button onclick="saveAssistant()" id="btn-save"
          style="padding:10px 20px;background:#2D7A6B;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background .2s;margin-top:4px"
          onmouseenter="this.style.background='#25664f'" onmouseleave="this.style.background='#2D7A6B'">
          <i class="fa fa-save"></i> Salvar configurações
        </button>

      </div>
    </div>

    <!-- ======================================================
         COLUNA DIREITA — Abas
    ====================================================== -->
    <div>

      <!-- Abas -->
      <div style="display:flex;gap:0;border-bottom:2px solid #e2e8f0;margin-bottom:20px">
        <button class="ast-tab ast-tab--active" onclick="showTab('knowledge')" id="tab-knowledge"
          style="padding:10px 20px;border:none;background:transparent;font-size:13px;font-weight:600;cursor:pointer;color:#2D7A6B;border-bottom:2px solid #2D7A6B;margin-bottom:-2px">
          <i class="fa fa-book"></i> Base de conhecimento
        </button>
        <button class="ast-tab" onclick="showTab('flow')" id="tab-flow"
          style="padding:10px 20px;border:none;background:transparent;font-size:13px;font-weight:600;cursor:pointer;color:#94a3b8;border-bottom:2px solid transparent;margin-bottom:-2px">
          <i class="fa fa-sitemap"></i> Fluxo de qualificação
        </button>
        <button class="ast-tab" onclick="showTab('media')" id="tab-media"
          style="padding:10px 20px;border:none;background:transparent;font-size:13px;font-weight:600;cursor:pointer;color:#94a3b8;border-bottom:2px solid transparent;margin-bottom:-2px">
          <i class="fa fa-photo"></i> Biblioteca de Mídia
        </button>
      </div>

      <!-- ABA: Base de conhecimento -->
      <div id="panel-knowledge">

        <?php if (!$assistant): ?>
          <div style="text-align:center;padding:40px;color:#94a3b8;background:#f8fafc;border-radius:12px">
            <i class="fa fa-info-circle" style="font-size:32px;margin-bottom:10px;display:block"></i>
            <p style="font-size:13px">Salve as configurações do assistente primeiro para adicionar conhecimento.</p>
          </div>
        <?php else: ?>

          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <p style="font-size:13px;color:#64748b;margin:0">Ensine o que a IA deve saber sobre seu negócio.</p>
            <button onclick="openKnowledgeForm()" id="btn-add-knowledge"
              style="padding:7px 14px;background:#2D7A6B;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
              <i class="fa fa-plus"></i> Adicionar item
            </button>
          </div>

          <!-- Form inline (oculto por padrão) -->
          <div id="knowledge-form" style="display:none;background:#f0fdf9;border:1px solid #a7f3d0;border-radius:10px;padding:16px;margin-bottom:14px">
            <input type="hidden" id="kf-id" value="0">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px">
              <div>
                <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px">Categoria</label>
                <select id="kf-category" class="ax-input">
                  <option value="product">Produto / Serviço</option>
                  <option value="faq">Pergunta Frequente</option>
                  <option value="objection">Como tratar objeção</option>
                  <option value="info">Informação geral</option>
                  <option value="sales_tip">Dica de vendas</option>
                </select>
              </div>
              <div>
                <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px">Título</label>
                <input type="text" id="kf-title" class="ax-input" placeholder="Ex: Preço da consulta">
              </div>
            </div>
            <div style="margin-bottom:10px">
              <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px">Conteúdo</label>
              <textarea id="kf-content" class="ax-textarea-field" rows="3" style="width:100%;resize:vertical"
                placeholder="Ex: A consulta inicial custa R$150 e inclui avaliação completa e raio-x panorâmico."></textarea>
            </div>
            <div style="display:flex;gap:8px">
              <button onclick="saveKnowledge()"
                style="padding:7px 16px;background:#2D7A6B;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer">
                Salvar
              </button>
              <button onclick="closeKnowledgeForm()"
                style="padding:7px 14px;background:transparent;border:1px solid #cbd5e1;border-radius:6px;font-size:12px;cursor:pointer;color:#64748b">
                Cancelar
              </button>
            </div>
          </div>

          <!-- Lista de itens -->
          <div id="knowledge-list">
            <?php if (empty($knowledge)): ?>
              <div id="knowledge-empty" style="text-align:center;padding:40px;color:#94a3b8;background:#f8fafc;border-radius:12px">
                <i class="fa fa-book" style="font-size:32px;margin-bottom:10px;display:block"></i>
                <p style="font-size:13px">Nenhum item cadastrado ainda. Adicione o que a IA deve saber.</p>
              </div>
            <?php else: ?>
              <?php
              $cat_labels = ['product'=>'Produto/Serviço','faq'=>'FAQ','objection'=>'Objeção','info'=>'Info','sales_tip'=>'Dica de Vendas'];
              $cat_colors = ['product'=>'#3182CE','faq'=>'#38A169','objection'=>'#E53E3E','info'=>'#805AD5','sales_tip'=>'#D69E2E'];
              foreach ($knowledge as $item): ?>
                <div class="k-item" id="ki-<?= $item->id ?>" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px;margin-bottom:8px">
                  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px">
                    <div style="flex:1">
                      <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                        <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;background:<?= $cat_colors[$item->category] ?? '#64748b' ?>22;color:<?= $cat_colors[$item->category] ?? '#64748b' ?>">
                          <?= $cat_labels[$item->category] ?? $item->category ?>
                        </span>
                        <span style="font-size:13px;font-weight:600;color:#1e293b"><?= htmlspecialchars($item->title) ?></span>
                      </div>
                      <p style="font-size:12px;color:#64748b;margin:0;line-height:1.5"><?= nl2br(htmlspecialchars(substr($item->content, 0, 150))) ?><?= strlen($item->content) > 150 ? '...' : '' ?></p>
                    </div>
                    <div style="display:flex;gap:6px;flex-shrink:0">
                      <button onclick="editKnowledge(<?= $item->id ?>,'<?= addslashes($item->category) ?>','<?= addslashes(htmlspecialchars($item->title)) ?>','<?= addslashes(htmlspecialchars($item->content)) ?>')"
                        style="border:1px solid #e2e8f0;background:#fff;border-radius:6px;padding:4px 8px;cursor:pointer;color:#64748b;font-size:11px">
                        <i class="fa fa-pencil"></i>
                      </button>
                      <button onclick="deleteKnowledge(<?= $item->id ?>)"
                        style="border:1px solid #fecaca;background:#fff;border-radius:6px;padding:4px 8px;cursor:pointer;color:#ef4444;font-size:11px">
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

        <?php endif; ?>
      </div>

      <!-- ABA: Fluxo de qualificação -->
      <div id="panel-flow" style="display:none">

        <?php if (!$assistant): ?>
          <div style="text-align:center;padding:40px;color:#94a3b8;background:#f8fafc;border-radius:12px">
            <i class="fa fa-sitemap" style="font-size:32px;margin-bottom:10px;display:block"></i>
            <p style="font-size:13px">Salve as configurações primeiro.</p>
          </div>
        <?php else: ?>

          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <p style="font-size:13px;color:#64748b;margin:0">Defina as perguntas e ações em sequência.</p>
            <button onclick="openStageForm()"
              style="padding:7px 14px;background:#2D7A6B;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
              <i class="fa fa-plus"></i> Nova etapa
            </button>
          </div>

          <!-- Form de etapa (oculto) -->
          <div id="stage-form" style="display:none;background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:16px;margin-bottom:14px">
            <input type="hidden" id="sf-id" value="0">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px">
              <div>
                <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px">Nome da etapa</label>
                <input type="text" id="sf-name" class="ax-input" placeholder="Ex: Qualificar interesse">
              </div>
              <div>
                <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px">Ação</label>
                <select id="sf-action" class="ax-input">
                  <option value="ask">Fazer pergunta</option>
                  <option value="inform">Informar</option>
                  <option value="qualify">Qualificar lead</option>
                  <option value="close">Fechar venda</option>
                  <option value="transfer">Transferir para humano</option>
                </select>
              </div>
            </div>
            <div style="margin-bottom:10px">
              <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px">Pergunta / mensagem da IA nesta etapa</label>
              <textarea id="sf-question" class="ax-textarea-field" rows="2" style="width:100%;resize:vertical"
                placeholder="Ex: Qual é o principal problema que você quer resolver?"></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px">
              <div>
                <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px">Campo do lead para salvar resposta</label>
                <input type="text" id="sf-save-field" class="ax-input" placeholder="Ex: notes, phone, name">
              </div>
              <div>
                <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px">Mover para estágio do pipeline</label>
                <select id="sf-pipeline-stage" class="ax-input">
                  <option value="">— Nenhum —</option>
                  <?php foreach ($pipeline_stages as $ps): ?>
                    <option value="<?= $ps->id ?>"><?= htmlspecialchars($ps->name) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Mídia vinculada à etapa -->
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:12px;margin-bottom:10px">
              <div style="font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
                <i class="fa fa-photo" style="color:#2D7A6B"></i> Mídia desta etapa
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                <div>
                  <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px">Arquivo</label>
                  <select id="sf-media-id" class="ax-input">
                    <option value="">— Nenhuma —</option>
                    <?php if ($assistant):
                      $all_media = $this->axiomchannel_model->get_media_by_assistant($assistant->id);
                      foreach ($all_media as $m):
                        $display = $m->media_label ?: $m->original_name;
                    ?>
                      <option value="<?= $m->id ?>"><?= htmlspecialchars($display) ?></option>
                    <?php endforeach; endif; ?>
                  </select>
                </div>
                <div>
                  <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px">Quando enviar</label>
                  <select id="sf-media-position" class="ax-input">
                    <option value="before_message">Antes da mensagem</option>
                    <option value="with_message" selected>Junto com a mensagem</option>
                    <option value="after_message">Após a mensagem</option>
                  </select>
                </div>
              </div>
              <p style="font-size:11px;color:#94a3b8;margin:6px 0 0">
                <i class="fa fa-info-circle"></i>
                A mídia será enviada ao cliente quando o fluxo chegar nesta etapa.
                Adicione arquivos na aba <strong>Biblioteca de Mídia</strong> primeiro.
              </p>
            </div>

            <div style="display:flex;gap:8px">
              <button onclick="saveStageItem()"
                style="padding:7px 16px;background:#2D7A6B;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer">
                Salvar etapa
              </button>
              <button onclick="closeStageForm()"
                style="padding:7px 14px;background:transparent;border:1px solid #cbd5e1;border-radius:6px;font-size:12px;cursor:pointer;color:#64748b">
                Cancelar
              </button>
            </div>
          </div>

          <!-- Lista de etapas -->
          <div id="stage-list">
            <?php if (empty($ast_stages)): ?>
              <div id="stage-empty" style="text-align:center;padding:40px;color:#94a3b8;background:#f8fafc;border-radius:12px">
                <i class="fa fa-sitemap" style="font-size:32px;margin-bottom:10px;display:block"></i>
                <p style="font-size:13px">Nenhuma etapa definida. Adicione o fluxo de qualificação.</p>
              </div>
            <?php else: ?>
              <?php
              $action_labels = ['ask'=>'Pergunta','inform'=>'Informa','qualify'=>'Qualifica','close'=>'Fecha venda','transfer'=>'Transfere'];
              $action_colors = ['ask'=>'#3182CE','inform'=>'#805AD5','qualify'=>'#D69E2E','close'=>'#38A169','transfer'=>'#E53E3E'];
              foreach ($ast_stages as $idx => $stage): ?>
                <div class="s-item" id="si-<?= $stage->id ?>"
                  style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px;margin-bottom:8px;display:flex;gap:10px;align-items:flex-start">
                  <div style="display:flex;flex-direction:column;gap:4px;padding-top:2px">
                    <button onclick="moveStageItem(<?= $stage->id ?>,-1)" title="Subir"
                      style="border:1px solid #e2e8f0;background:#f8fafc;border-radius:4px;padding:2px 6px;cursor:pointer;color:#64748b;font-size:10px"><?php echo '&#9650;'; ?></button>
                    <button onclick="moveStageItem(<?= $stage->id ?>,1)" title="Descer"
                      style="border:1px solid #e2e8f0;background:#f8fafc;border-radius:4px;padding:2px 6px;cursor:pointer;color:#64748b;font-size:10px"><?php echo '&#9660;'; ?></button>
                  </div>
                  <div style="width:24px;height:24px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#64748b;flex-shrink:0;margin-top:2px">
                    <?= $idx + 1 ?>
                  </div>
                  <div style="flex:1">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap">
                      <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;background:<?= $action_colors[$stage->action] ?? '#64748b' ?>22;color:<?= $action_colors[$stage->action] ?? '#64748b' ?>">
                        <?= $action_labels[$stage->action] ?? $stage->action ?>
                      </span>
                      <span style="font-size:13px;font-weight:600;color:#1e293b"><?= htmlspecialchars($stage->stage_name) ?></span>
                      <?php if (!empty($stage->media_id)): ?>
                        <span style="font-size:10px;background:#f0fdf9;color:#2D7A6B;border:1px solid #a7f3d0;border-radius:4px;padding:1px 7px;font-weight:600">
                          <i class="fa fa-photo"></i> mídia
                        </span>
                      <?php endif; ?>
                    </div>
                    <?php if ($stage->question): ?>
                      <p style="font-size:12px;color:#64748b;margin:0;line-height:1.5;font-style:italic">"<?= htmlspecialchars(substr($stage->question, 0, 100)) ?><?= strlen($stage->question) > 100 ? '...' : '' ?>"</p>
                    <?php endif; ?>
                  </div>
                  <div style="display:flex;gap:6px;flex-shrink:0">
                    <button onclick="editStageItem(<?= $stage->id ?>,'<?= addslashes(htmlspecialchars($stage->stage_name)) ?>','<?= addslashes(htmlspecialchars($stage->question ?? '')) ?>','<?= $stage->action ?>','<?= addslashes($stage->save_field ?? '') ?>',<?= (int)$stage->pipeline_stage_id ?>,<?= (int)($stage->media_id ?? 0) ?>,'<?= $stage->media_send_position ?? 'with_message' ?>')"
                      style="border:1px solid #e2e8f0;background:#fff;border-radius:6px;padding:4px 8px;cursor:pointer;color:#64748b;font-size:11px">
                      <i class="fa fa-pencil"></i>
                    </button>
                    <button onclick="deleteStageItem(<?= $stage->id ?>)"
                      style="border:1px solid #fecaca;background:#fff;border-radius:6px;padding:4px 8px;cursor:pointer;color:#ef4444;font-size:11px">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

        <?php endif; ?>
      </div>

      <!-- ABA: Biblioteca de Mídia -->
      <div id="panel-media" style="display:none">

        <?php if (!$assistant): ?>
          <div style="text-align:center;padding:40px;color:#94a3b8;background:#f8fafc;border-radius:12px">
            <i class="fa fa-photo" style="font-size:32px;margin-bottom:10px;display:block"></i>
            <p style="font-size:13px">Salve as configurações do assistente primeiro.</p>
          </div>
        <?php else: ?>

          <!-- Instrução de uso -->
          <div style="background:#f0fdf9;border:1px solid #a7f3d0;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:12px;color:#1e293b">
            <i class="fa fa-info-circle" style="color:#2D7A6B"></i>
            <strong>Repositório de arquivos.</strong>
            Faça upload dos arquivos que o assistente pode enviar. Para vinculá-los ao fluxo de atendimento, edite uma etapa na aba <strong>Fluxo de qualificação</strong> e selecione o arquivo no campo "Mídia desta etapa".
          </div>

          <!-- Painel de upload simplificado -->
          <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:18px;margin-bottom:20px">

            <!-- Drop zone -->
            <div id="media-dropzone"
              style="border:2px dashed #cbd5e1;border-radius:10px;padding:22px;text-align:center;cursor:pointer;background:#fff;margin-bottom:14px;transition:.2s"
              onclick="document.getElementById('media-file-input').click()"
              ondragover="event.preventDefault();this.style.borderColor='#2D7A6B';this.style.background='#f0fdf9'"
              ondragleave="this.style.borderColor='#cbd5e1';this.style.background='#fff'"
              ondrop="handleMediaDrop(event)">
              <i class="fa fa-cloud-upload" style="font-size:28px;color:#94a3b8;display:block;margin-bottom:6px"></i>
              <p style="font-size:13px;color:#64748b;margin:0">Arraste arquivos aqui ou <strong style="color:#2D7A6B">clique para selecionar</strong></p>
              <p style="font-size:11px;color:#94a3b8;margin:4px 0 0">Imagens, vídeos, áudios, PDF — máx. 20 MB</p>
              <input type="file" id="media-file-input" style="display:none"
                accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx"
                onchange="uploadMediaFile(this.files[0])">
            </div>

            <!-- Apenas nome/descrição -->
            <div>
              <label style="font-size:11px;font-weight:600;color:#475569;display:block;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px">Nome / Descrição do arquivo</label>
              <input type="text" id="media-label" class="ax-input" placeholder="Ex: Resultado botox antes e depois, tabela de preços, contrato modelo...">
              <p style="font-size:11px;color:#94a3b8;margin:4px 0 0">Este nome aparecerá no select das etapas para facilitar a identificação.</p>
            </div>

          </div>

          <!-- Barra de progresso (oculta) -->
          <div id="media-progress-bar" style="display:none;background:#e2e8f0;border-radius:8px;height:6px;margin-bottom:14px">
            <div id="media-progress-fill" style="background:#2D7A6B;height:100%;border-radius:8px;width:0%;transition:width .3s"></div>
          </div>

          <!-- Grid de mídia -->
          <div id="media-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(148px,1fr));gap:12px">
            <!-- preenchido via loadMediaGrid() no JS -->
            <div id="media-empty-state" style="grid-column:1/-1;text-align:center;padding:40px;color:#94a3b8">
              <i class="fa fa-photo" style="font-size:32px;margin-bottom:10px;display:block"></i>
              <p style="font-size:13px">Nenhuma mídia ainda.</p>
            </div>
          </div>

        <?php endif; ?>
      </div>

    </div><!-- coluna direita -->
  </div><!-- grid -->

  <?php endif; ?>

</div>
</div>

<script>
const CSRF_TOKEN    = '<?= $this->security->get_csrf_hash() ?>';
const CSRF_NAME     = '<?= $this->security->get_csrf_token_name() ?>';
const ADMIN_URL     = '<?= admin_url() ?>';
const ASSISTANT_ID  = <?= $assistant ? (int)$assistant->id : 0 ?>;
const DEVICE_ID     = <?= (int)$device_id ?>;

const CAT_LABELS = {product:'Produto/Serviço',faq:'FAQ',objection:'Objeção',info:'Info',sales_tip:'Dica de Vendas'};
const CAT_COLORS = {product:'#3182CE',faq:'#38A169',objection:'#E53E3E',info:'#805AD5',sales_tip:'#D69E2E'};
const ACT_LABELS = {ask:'Pergunta',inform:'Informa',qualify:'Qualifica',close:'Fecha venda',transfer:'Transfere'};
const ACT_COLORS = {ask:'#3182CE',inform:'#805AD5',qualify:'#D69E2E',close:'#38A169',transfer:'#E53E3E'};

// ---- ABAS ----
function showTab(tab) {
  ['knowledge','flow','media'].forEach(t => {
    document.getElementById('panel-' + t).style.display = tab === t ? 'block' : 'none';
    const btn = document.getElementById('tab-' + t);
    btn.style.color            = tab === t ? '#2D7A6B' : '#94a3b8';
    btn.style.borderBottomColor = tab === t ? '#2D7A6B' : 'transparent';
  });
  if (tab === 'media') loadMediaGrid();
}

// ---- TOGGLE ATIVO ----
function toggleActive() {
  const on = document.getElementById('chk-active').checked;
  document.getElementById('toggle-track').style.background = on ? '#2D7A6B' : '#cbd5e1';
  document.getElementById('toggle-thumb').style.left       = on ? '23px' : '3px';
}

// ---- TIPO DE ASSISTENTE ----
function onTypeChange() {
  const types = [...document.querySelectorAll('.asst-type-cb:checked')].map(c => c.value);
  document.getElementById('section-scheduling').style.display = types.includes('scheduling') ? 'block' : 'none';
  document.getElementById('section-contract').style.display   = types.includes('contract')   ? 'block' : 'none';
}

// ---- SALVAR ASSISTENTE ----
function saveAssistant() {
  const btn = document.getElementById('btn-save');
  btn.disabled = true; btn.textContent = 'Salvando...';

  const types    = [...document.querySelectorAll('.asst-type-cb:checked')].map(c => c.value);
  const apptDays = [...document.querySelectorAll('.appt-day-cb:checked')].map(c => c.value);

  const params = new URLSearchParams({
    device_id:           DEVICE_ID,
    name:                document.getElementById('f-name').value,
    business_name:       document.getElementById('f-business-name').value,
    business_type:       document.getElementById('f-business-type').value,
    tone_of_voice:       document.getElementById('f-tone').value,
    emoji_enabled:       document.getElementById('f-emoji').value,
    greeting_message:    document.getElementById('f-greeting').value,
    transfer_keywords:   document.getElementById('f-transfer-kw').value,
    working_hours_start: document.getElementById('f-hours-start').value,
    working_hours_end:   document.getElementById('f-hours-end').value,
    is_active:           document.getElementById('chk-active').checked ? 1 : 0,
    assistant_types:     JSON.stringify(types),
    appointment_duration:document.getElementById('f-appt-duration')?.value || 60,
    appointment_start:   document.getElementById('f-appt-start')?.value || '08:00',
    appointment_end:     document.getElementById('f-appt-end')?.value   || '18:00',
    appointment_days:    apptDays.join(','),
    appointment_interval:document.getElementById('f-appt-interval')?.value || 30,
    default_contract_template_id: document.getElementById('f-contract-template')?.value || '',
    [CSRF_NAME]: CSRF_TOKEN
  });

  fetch(ADMIN_URL + 'axiomchannel/assistant_save', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: params
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      document.getElementById('f-assistant-id').value = data.assistant_id;
      btn.textContent = '✓ Salvo!';
      btn.style.background = '#38A169';
      setTimeout(() => { btn.textContent = '💾 Salvar configurações'; btn.style.background = '#2D7A6B'; btn.disabled = false; }, 2000);
      if (!ASSISTANT_ID) setTimeout(() => location.reload(), 1500);
    } else {
      alert(data.message || 'Erro ao salvar');
      btn.disabled = false; btn.textContent = '💾 Salvar configurações';
    }
  })
  .catch(() => { alert('Erro de conexão'); btn.disabled = false; btn.textContent = '💾 Salvar configurações'; });
}

// ---- KNOWLEDGE BASE ----
let kItems = [];

function openKnowledgeForm() {
  document.getElementById('kf-id').value      = 0;
  document.getElementById('kf-category').value = 'product';
  document.getElementById('kf-title').value    = '';
  document.getElementById('kf-content').value  = '';
  document.getElementById('knowledge-form').style.display = 'block';
  document.getElementById('kf-title').focus();
}

function closeKnowledgeForm() {
  document.getElementById('knowledge-form').style.display = 'none';
}

function editKnowledge(id, cat, title, content) {
  document.getElementById('kf-id').value       = id;
  document.getElementById('kf-category').value = cat;
  document.getElementById('kf-title').value    = title;
  document.getElementById('kf-content').value  = content;
  document.getElementById('knowledge-form').style.display = 'block';
  document.getElementById('kf-title').focus();
}

function saveKnowledge() {
  const aid = document.getElementById('f-assistant-id').value || ASSISTANT_ID;
  if (!aid) { alert('Salve o assistente primeiro'); return; }

  fetch(ADMIN_URL + 'axiomchannel/knowledge_save', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: new URLSearchParams({
      id:           document.getElementById('kf-id').value,
      assistant_id: aid,
      category:     document.getElementById('kf-category').value,
      title:        document.getElementById('kf-title').value,
      content:      document.getElementById('kf-content').value,
      [CSRF_NAME]:  CSRF_TOKEN
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      closeKnowledgeForm();
      location.reload();
    } else {
      alert(data.message || 'Erro ao salvar');
    }
  });
}

function deleteKnowledge(id) {
  if (!confirm('Remover este item?')) return;
  fetch(ADMIN_URL + 'axiomchannel/knowledge_delete', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: new URLSearchParams({ id, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const el = document.getElementById('ki-' + id);
      if (el) el.remove();
    }
  });
}

// ---- FLUXO DE QUALIFICAÇÃO ----
function openStageForm() {
  document.getElementById('sf-id').value              = 0;
  document.getElementById('sf-name').value            = '';
  document.getElementById('sf-action').value          = 'ask';
  document.getElementById('sf-question').value        = '';
  document.getElementById('sf-save-field').value      = '';
  document.getElementById('sf-pipeline-stage').value  = '';
  document.getElementById('sf-media-id').value        = '';
  document.getElementById('sf-media-position').value  = 'with_message';
  document.getElementById('stage-form').style.display = 'block';
  document.getElementById('sf-name').focus();
}

function closeStageForm() {
  document.getElementById('stage-form').style.display = 'none';
}

function editStageItem(id, name, question, action, saveField, pipelineStageId, mediaId, mediaPosition) {
  document.getElementById('sf-id').value              = id;
  document.getElementById('sf-name').value            = name;
  document.getElementById('sf-question').value        = question;
  document.getElementById('sf-action').value          = action;
  document.getElementById('sf-save-field').value      = saveField;
  document.getElementById('sf-pipeline-stage').value  = pipelineStageId || '';
  document.getElementById('sf-media-id').value        = mediaId || '';
  document.getElementById('sf-media-position').value  = mediaPosition || 'with_message';
  document.getElementById('stage-form').style.display = 'block';
  document.getElementById('sf-name').focus();
}

function saveStageItem() {
  const aid = document.getElementById('f-assistant-id').value || ASSISTANT_ID;
  if (!aid) { alert('Salve o assistente primeiro'); return; }

  fetch(ADMIN_URL + 'axiomchannel/stage_save', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: new URLSearchParams({
      id:                  document.getElementById('sf-id').value,
      assistant_id:        aid,
      stage_name:          document.getElementById('sf-name').value,
      action:              document.getElementById('sf-action').value,
      question:            document.getElementById('sf-question').value,
      save_field:          document.getElementById('sf-save-field').value,
      pipeline_stage_id:   document.getElementById('sf-pipeline-stage').value,
      media_id:            document.getElementById('sf-media-id').value || '',
      media_send_position: document.getElementById('sf-media-position').value,
      [CSRF_NAME]:         CSRF_TOKEN
    })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      closeStageForm();
      location.reload();
    } else {
      alert(data.message || 'Erro ao salvar etapa');
    }
  });
}

function deleteStageItem(id) {
  if (!confirm('Remover esta etapa?')) return;
  fetch(ADMIN_URL + 'axiomchannel/stage_delete', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: new URLSearchParams({ id, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const el = document.getElementById('si-' + id);
      if (el) el.remove();
    }
  });
}

function moveStageItem(id, direction) {
  const el    = document.getElementById('si-' + id);
  const list  = document.getElementById('stage-list');
  const items = [...list.querySelectorAll('.s-item')];
  const idx   = items.indexOf(el);
  const swap  = items[idx + direction];
  if (!swap) return;

  if (direction === -1) list.insertBefore(el, swap);
  else list.insertBefore(swap, el);

  const newOrder = [...list.querySelectorAll('.s-item')].map((el, i) => ({
    id: el.id.replace('si-', ''), position: i
  }));

  fetch(ADMIN_URL + 'axiomchannel/knowledge_reorder', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: new URLSearchParams({ items: JSON.stringify(newOrder), [CSRF_NAME]: CSRF_TOKEN })
  });
}

// ---- BIBLIOTECA DE MÍDIA ----
const FILE_ICONS = {
  image:    'fa-file-image-o',
  video:    'fa-file-video-o',
  audio:    'fa-file-audio-o',
  pdf:      'fa-file-pdf-o',
  document: 'fa-file-word-o',
};

function handleMediaDrop(e) {
  e.preventDefault();
  document.getElementById('media-dropzone').style.borderColor = '#cbd5e1';
  document.getElementById('media-dropzone').style.background  = '#fff';
  const file = e.dataTransfer.files[0];
  if (file) uploadMediaFile(file);
}

function uploadMediaFile(file) {
  if (!file) return;
  const aid = ASSISTANT_ID || document.getElementById('f-assistant-id').value;
  if (!aid) { alert('Salve o assistente primeiro'); return; }

  const label = document.getElementById('media-label').value;

  const fd = new FormData();
  fd.append('file',         file);
  fd.append('assistant_id', aid);
  fd.append('media_label',  label);
  fd.append(CSRF_NAME,      CSRF_TOKEN);

  const bar  = document.getElementById('media-progress-bar');
  const fill = document.getElementById('media-progress-fill');
  bar.style.display = 'block';
  fill.style.width  = '10%';

  const xhr = new XMLHttpRequest();
  xhr.open('POST', ADMIN_URL + 'axiomchannel/media_upload');
  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

  xhr.upload.onprogress = e => {
    if (e.lengthComputable) fill.style.width = Math.round(e.loaded / e.total * 90) + '%';
  };

  xhr.onload = () => {
    fill.style.width = '100%';
    setTimeout(() => { bar.style.display = 'none'; fill.style.width = '0%'; }, 600);

    let res;
    try { res = JSON.parse(xhr.responseText); } catch(err) { alert('Erro ao processar resposta'); return; }

    if (res.success) {
      loadMediaGrid();
      document.getElementById('media-file-input').value = '';
      document.getElementById('media-label').value       = '';
    } else {
      alert(res.message || 'Erro no upload');
    }
  };

  xhr.onerror = () => { bar.style.display = 'none'; alert('Erro de conexão'); };
  xhr.send(fd);
}

function loadMediaGrid() {
  const aid = ASSISTANT_ID || document.getElementById('f-assistant-id').value;
  if (!aid) return;

  fetch(ADMIN_URL + 'axiomchannel/media_list/' + aid, {
    headers: {'X-Requested-With': 'XMLHttpRequest'}
  })
  .then(r => r.json())
  .then(res => {
    const grid = document.getElementById('media-grid');
    if (!res.success) return;

    grid.innerHTML = '';

    if (!res.data.length) {
      grid.innerHTML = '<div id="media-empty-state" style="grid-column:1/-1;text-align:center;padding:40px;color:#94a3b8">' +
        '<i class="fa fa-photo" style="font-size:32px;margin-bottom:10px;display:block"></i>' +
        '<p style="font-size:13px">Nenhuma mídia ainda. Faça upload acima.</p></div>';
      return;
    }

    res.data.forEach(m => {
      const isImage  = m.file_type === 'image';
      const icon     = FILE_ICONS[m.file_type] || 'fa-file-o';
      const dispName = m.media_label || m.original_name;

      const card = document.createElement('div');
      card.id    = 'media-' + m.id;
      card.style.cssText = 'background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;position:relative;';

      card.innerHTML = (isImage
        ? '<div style="height:80px;overflow:hidden;background:#f1f5f9"><img src="' + m.url + '" style="width:100%;height:100%;object-fit:cover"></div>'
        : '<div style="height:80px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;font-size:28px;color:#94a3b8"><i class="fa ' + icon + '"></i></div>'
      ) +
      '<div style="padding:8px 8px 6px">' +
        '<p style="font-size:11px;color:#1e293b;font-weight:600;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="' + dispName + '">' + dispName + '</p>' +
        '<p style="font-size:10px;color:#94a3b8;margin:2px 0 0">' + m.file_type + '</p>' +
      '</div>' +
      '<button onclick="deleteMedia(' + m.id + ')" title="Remover" ' +
        'style="position:absolute;top:4px;right:4px;background:rgba(239,68,68,.85);color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:10px;cursor:pointer;line-height:22px;text-align:center;padding:0">' +
        '<i class="fa fa-times"></i></button>';

      grid.appendChild(card);
    });
  });
}

function deleteMedia(id) {
  if (!confirm('Remover este arquivo?')) return;
  fetch(ADMIN_URL + 'axiomchannel/media_delete', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body: new URLSearchParams({ id, [CSRF_NAME]: CSRF_TOKEN })
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) {
      const el = document.getElementById('media-' + id);
      if (el) el.remove();
    }
  });
}
</script>

<?php init_tail(); ?>
