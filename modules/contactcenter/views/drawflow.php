<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<link rel="stylesheet" href="https://unpkg.com/drawflow@x.x.xx/dist/drawflow.min.css" />
<script src="https://unpkg.com/drawflow@x.x.xx/dist/drawflow.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.14/ace.js"></script>


<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="_buttons">
          <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data">
            <div class="btn-group">
              <button type="button" class="btn btn-primary" onclick="saveEditorState(1)">Publicar</button>
              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu pull-right" role="menu">
                <li><a href="javascript:void(0);" onclick="saveEditorState(2)">Salvar como Rascunho</a></li>
                <li><a href="javascript:void(0);" onclick="saveEditorState(3)">Desativar</a></li>
                <li><a href="javascript:void(0);" onclick="exportDrawflow(<?= $drawflow->draw_id; ?>)">Exportar</a></li>
                <li><a href="javascript:void(0);" onclick="import_drawflow()">Importar</a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel_s tw-mt-2 sm:tw-mt-4 overflow-hidden drawflow-fullscreen">

          <div class="wrapper-drawflow">
            <div class="menu-drawflow">
              <div class="form-group">
                <input type="text" name="drawflow_title" class="form-control" value="<?= $drawflow->title; ?>">
              </div>
              <div class="title-box-menu"><i class="fa-solid fa-check-double"></i>Variáveis do contato e agente</div>
              <div class="content-menu">
                <div class="drag-drawflow text-center" data-toggle="tooltip" data-placement="top" data-original-title="Troca pelo o nome do contato" onclick="copyText(this)">
                  <span>{Lead-name}</span>
                </div>
                <div class="drag-drawflow text-center" data-toggle="tooltip" data-placement="top" data-original-title="Troca pelo o nome do agente" onclick="copyText(this)">
                  <span>{Agent-name}</span>
                </div>
              </div>
              <!-- inicio Bloco  -->
              <div class="title-box-menu"><i class="fa-solid fa-check-double"></i>Mensagem</div>
              <div class="content-menu">
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Cria um novo grupo, pode ser utilizado para agrupar as mensagens." draggable="true" ondragstart="drag(event)" data-node="group">
                  <i class="fa-duotone fa-solid fa-object-group"></i><span>Group</span>
                </div>
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Envia um texto" draggable="true" ondragstart="drag(event)" data-node="text">
                  <i class="fa-regular fa-message"></i><span>Texto</span>
                </div>
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Envia uma imagem" draggable="true" ondragstart="drag(event)" data-node="image">
                  <i class="fa-regular fa-image"></i><span>Image</span>
                </div>
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Envia um arquivo de video" draggable="true" ondragstart="drag(event)" data-node="video">
                  <i class="fa-solid fa-film"></i><span>Video</span>
                </div>
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Envia um arquivo PDF" draggable="true" ondragstart="drag(event)" data-node="pdf">
                  <i class="fa-regular fa-file-pdf"></i><span>PDF</span>
                </div>
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Envia uma mensagem de audio" draggable="true" ondragstart="drag(event)" data-node="audio">
                  <i class="fa-solid fa-music"></i><span>Audio</span>
                </div>
              </div>
              <!-- Fim Bloco  -->
              <!-- inicio Bloco  -->
              <div class="title-box-menu"><i class="fa-solid fa-check-double"></i> Lógica</div>
              <div class="content-menu">
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Escuta uma mensagem, conforme a resposta do usuário você pode fazer uma ação" draggable="true" ondragstart="drag(event)" data-node="condition">
                  <i class="fa-solid fa-filter"></i><span>Condições</span>
                </div>
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Escuta uma mensagem, conforme a resposta do usuário você pode fazer uma ação" draggable="true" ondragstart="drag(event)" data-node="sleep">
                  <i class="fa-regular fa-clock"></i><span>Esperar</span>
                </div>
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Essa função desativa a IA e o Fluxo do usuário" draggable="true" ondragstart="drag(event)" data-node="desactivateAi">
                  <i class="fa-solid fa-comment-slash"></i><span>Desativa</span>
                </div>
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Essa função retorna o momento do dia usado somente no inicio do fluxo" draggable="true" ondragstart="drag(event)" data-node="moment-day">
                  <i class="fa-regular fa-sun"></i><span> Momento dia</span>
                </div>
              </div>
              <!-- Fim Bloco  -->
              <!-- inicio Bloco  -->
              <div class="title-box-menu"><i class="fa-solid fa-check-double"></i> Ações</div>
              <div class="content-menu">
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Faz o agendamento" draggable="true" ondragstart="drag(event)" data-node="agenda">
                  <i class="fa-solid fa-calendar-days"></i><span>Agenda</span>
                </div>
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Transfere de status o lead" draggable="true" ondragstart="drag(event)" data-node="status-leads">
                  <i class="fa-solid fa-diagram-project"></i><span>Status Leads</span>
                </div>
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Transfere de usuário o Lead" class="drag-drawflow" draggable="true" ondragstart="drag(event)" data-node="staff">
                  <i class="fa-solid fa-people-arrows"></i><span>Usuário</span>
                </div>
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Notificar o usuário" class="drag-drawflow" draggable="true" ondragstart="drag(event)" data-node="notification">
                  <i class="fa-regular fa-bell"></i><span>Notificar</span>
                </div>
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Envia uma localização" draggable="true" ondragstart="drag(event)" data-node="location">
                  <i class="fa-solid fa-map-location-dot"></i><span>Localização</span>
                </div>

              </div>
              <!-- Fim Bloco  -->
              <!-- inicio Bloco  -->
              <div class="title-box-menu"><i class="fa-solid fa-check-double"></i> IA</div>
              <div class="content-menu">
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="IA AXIOM" draggable="true" ondragstart="drag(event)" data-node="IA">
                  <i class="fa-solid fa-robot"></i><span>AXIOM</span>
                </div>
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="OmniCore" draggable="true" ondragstart="drag(event)" data-node="IA-ChatGPT">
                  <i class="fa-solid fa-robot"></i><span>OmniCore</span>
                </div>

              </div>
              <!-- Fim Bloco  -->
              <!-- inicio Bloco  -->
              <div class="title-box-menu"><i class="fa-solid fa-check-double"></i> Inputs</div>
              <div class="content-menu">

                <?php
                foreach ($fields as $field) :
                  if ($field == 'name') {
                    $label = _l('lead_add_edit_name');
                  } elseif ($field == 'email') {
                    $label = _l('drawflow_label_email');
                  } elseif ($field == 'phonenumber') {
                    $label = _l('lead_add_edit_phonenumber');
                  } elseif ($field == 'lead_value') {
                    $label = _l('lead_add_edit_lead_value');
                    $type  = 'number';
                  } else {
                    $label = _l('lead_' . $field);
                  }
                ?>
                  <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Captura o valor de um campo" draggable="true" ondragstart="drag(event)" data-node="inputs" data-label="<?= $label ?>" data-input="<?= $field ?>">
                    <i class="fa-solid fa-bullseye"></i><span><?= $label ?></span>
                  </div>
                <?php endforeach; ?>

                <?php foreach ($custom_fields as $custom_field) : ?>
                  <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="Captura o valor de um campo" draggable="true" ondragstart="drag(event)" data-node="inputs" data-label="<?= $custom_field['name'] ?>" data-input="<?= $custom_field['id']; ?>">
                    <i class="fa-solid fa-bullseye"></i><span><?= $custom_field['name'] ?></span>
                  </div>
                <?php endforeach; ?>

              </div>
              <!-- Fim Bloco  -->
              <!-- inicio Bloco  -->
              <div class="title-box-menu"><i class="fa-solid fa-check-double"></i> Integracões</div>
              <div class="content-menu">
                <!-- <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="IA AXIOM" draggable="true" ondragstart="drag(event)" data-node="integretion-http">
                  <i class="fa-solid fa-globe"></i><span>Http requests</span>
                </div> -->

              </div>
              <!-- Fim Bloco  -->

            </div>
            <div class="content-drawflow">
              <div id="drawflow" ondrop="drop(event)" ondragover="allowDrop(event)">
                <button id="fullscreen-btn" class="btn btn-primary"><i class="fa-solid fa-expand"></i></button>

                <div class="bar-zoom">
                  <i class="fas fa-search-minus" onclick="editor.zoom_out()"></i>
                  <i class="fas fa-search" onclick="editor.zoom_reset()"></i>
                  <i class="fas fa-search-plus" onclick="editor.zoom_in()"></i>
                </div>

              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>




<!-- Modal Assistent -->
<div class="modal fade" id="modalAssistant" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header ">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title " id="exampleModalLongTitle"><?= _l("drawflow_instructions_assistent"); ?></h4>
      </div>
      <div class="modal-body">
        <?php echo form_open_multipart("", array("id" => "form_assistant")); ?>
        <input type="hidden" name="input_id" value="" />
        <input type="hidden" name="action" value="crearGpt" />
        <input type="hidden" name="draw_id" value="<?= $drawflow->draw_id; ?>" />
        <input type="hidden" name="id" value="" />

        <div class="form-group">
          <label class="control-label"><?= _l("drawflow_instructions_model"); ?></label>
          <select class="form-control selectpicker _select_input_group" name="gpt_model" required>
            <option value="gpt-3.5-turbo">OmniCore Basic</option>
            <option value="gpt-4o-mini">OmniCore Standard</option>
            <option value="gpt-4o">OmniCore Advanced</option>
          </select>
        </div>

        <div class="form-group">
          <label class="control-label"><?= _l("drawflow_instructions_caracters"); ?></label>
          <input type="number" name="gpt_caracters" class="form-control" value="1000" required>
        </div>
        <div class="form-group">
          <label class="control-label"><?= _l("drawflow_tag_assistent"); ?></label>
          <textarea class="form-control" name="gpt_tag_exit" placeholder="<?= _l("drawflow_tag_assistent"); ?>" required></textarea>
        </div>
        <div class="form-group">
          <label class="control-label"><?= _l("drawflow_instructions_assistent"); ?></label>
          <textarea class="form-control" name="gpt_prompt" placeholder="<?= _l("drawflow_instructions_assistent"); ?>" required></textarea>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><?= _l("save"); ?></button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<!-- End Modal -->

<!-- Modal http -->
<div class="modal fade" id="modalHttp" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header ">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title " id="exampleModalLongTitle"><?= _l("drawflow_instructions_assistent"); ?></h4>
      </div>
      <div class="modal-body">
        <?php echo form_open_multipart("", array("id" => "form_http")); ?>
        <input type="hidden" name="input_id" value="" />
        <input type="hidden" name="type" value="integretion-http" />
        <input type="hidden" name="draw_id" value="<?= $drawflow->draw_id; ?>" />
        <input type="hidden" name="id" value="" />

        <div class="form-group">
          <label class="control-label"><?= _l("drawflow_label_url"); ?></label>
          <input type="text" name="urlhttp" class="form-control" placeholder="https://example.com" value="" required>
        </div>

        <div class="form-group col-md-6">
          <label class="control-label"><?= _l("drawflow_label_method"); ?></label>
          <select class="form-control selectpicker _select_input_group" name="method">
            <option value="POST">POST</option>
            <option value="GET">GET</option>
          </select>
        </div>


        <div class="form-group col-md-6">
          <label class="control-label"><?= _l("drawflow_label_timeout"); ?></label>
          <input type="number" name="timeout" class="form-control" value="10" required>
        </div>
        <div class="form-group">
          <label class="control-label"><?= _l("drawflow_label_headers"); ?></label>
          <div id="editorHeader" style="height: 200px; width: 100%;"></div>
        </div>
        <div class="form-group">
          <label class="control-label"><?= _l("drawflow_label_body"); ?></label>
          <div id="editorBody" style="min-height: 200px; width: 100%;"></div>
        </div>

        <div class="row mt-3">
          <div class="col-md-12">
            <button type="button" onclick="testResponse()" class="btn btn-default btn-lg btn-block"><?= _l("drawflow_button_test_response"); ?></button>
            <div class="progress tw-mb-0 tw-mt-4 progress-bar-mini" style="display: none;">
              <div id="progressBar" class="progress-bar progress-bar-success progress-bar-striped active no-percent-text not-dynamic" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0" style="width: 0%;" data-percent="0"> </div>
            </div>
          </div>
        </div>
        <div>
          <hr class="hr-panel-separator">
          <div class="form-group">
            <label class="control-label"><?= _l("drawflow_label_response"); ?></label>
            <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("drawflow_label_response_toltip") ?>' data-original-title='' title=''>
              <input type='checkbox' value="1" name='confirmResponse' class='onoffswitch-checkbox ' id='confirmResponse'>
              <label class='onoffswitch-label' for='confirmResponse'></label>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label"><?= _l("drawflow_label_body"); ?></label>
            <div id="editor-response" style="min-height: 200px; width: 100%;"></div>
          </div>

          <div class="row mt-3">
            <div class="col-md-12 mt-3">
              <h5><?= _l("drawflow_label_response_fields"); ?></h5>
              <hr class="hr-panel-separator">
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 mt-3">
              <div id="jsonFieldsContainer"></div>
            </div>
          </div>

        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><?= _l("save"); ?></button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<!-- End Modal -->





<?php

$ln = null;
foreach ($statuses as $statuse) {
  $ln .= "<option value='{$statuse['id']}'>{$statuse['name']}</option>";
}

$staffid = null;
foreach ($members as $member) {
  $staffid .= "<option value='{$member["staffid"]}'>{$member["firstname"]} {$member["lastname"]}</option>";
}

$data_automation = isset($data_automation) && !empty($data_automation) ? $data_automation : null;
?>

<script>
  var id = document.getElementById("drawflow");
  const editor = new Drawflow(id);
  editor.reroute = true;
  editor.reroute_fix_curvature = true;
  editor.force_first_input = false;



  var dataToImport = <?= $data_automation !== null ? $data_automation : json_encode([
                        "drawflow" => [
                          "Home" => [
                            "data" => [
                              "1" => [
                                "id" => 1,
                                "name" => "group",
                                "data" => [],
                                "class" => "group",
                                "html" => "\n  <div>\n  <div class=\"title-box\"><i class=\"fa-regular fa-circle-play\"></i> Start</div>\n  </div>\n ",
                                "typenode" => false,
                                "inputs" => [],
                                "outputs" => [
                                  "output_1" => [
                                    "connections" => []
                                  ]
                                ],
                                "pos_x" => 21,
                                "pos_y" => 234
                              ]
                            ]
                          ]
                        ]
                      ]); ?>;


  editor.start();
  editor.import(dataToImport);

  var groupId = null;






  editor.on('nodeCreated', function(id) {
    saveEditorState();
    console.log("Node created " + id);
  });

  /**
   * remove node
   */
  editor.on('nodeRemoved', function(id) {
    saveEditorState();
    saveConectionBanco("", id, "nodeMoved");
    console.log("Node removed " + id);
  });

  editor.on('nodeSelected', function(id) {
    saveEditorState();
    groupId = id;
    console.log("Node selected " + id);
  });

  editor.on('moduleCreated', function(name) {
    console.log("Module Created " + name);
  });

  editor.on('moduleChanged', function(name) {
    console.log("Module Changed " + name);
  });

  editor.on('connectionCreated', function(connection) {

    //verifica se input da condição esta vazio
    var codincao = editor.getNodeFromId(connection.output_id);
    console.log(codincao.name);
    if (codincao.name == "condition") {
      var input = $("#node-" + connection.output_id + " input[data-input=" + connection.output_class + "]").val();
      if (input == "") {
        //se for igual a branco retira o nó 
        editor.removeSingleConnection(connection.output_id, connection.input_id, connection.output_class, connection.input_class);
        Trigger("<span class='trigger_alert trigger'>Selecione um valor para a condição</span>");
        setTimeout(function() {
          TriggerClose();
        }, 3000);
      } else {
        saveConectionCondition(connection.output_id, connection.output_class, connection.input_id, connection.input_class)
        saveEditorState();
      }

    } else if (codincao.name = "group") {
      // Chama a função saveConectionBanco e aguarda o resultado
      saveConectionBanco(connection.input_id, connection.output_id, "connectionCreated");
      saveEditorState();
    }


    console.log('Connection created');
    console.log(connection);

  });

  var blockRemove = 0;
  editor.on('connectionRemoved', function(connection) {

    //verifica se input da condição esta vazio
    var codincao = editor.getNodeFromId(connection.output_id);
    console.log(codincao.name);
    if (codincao.name == "condition") {
      if (blockRemove == 0) {
        saveConectionCondition(connection.output_id, connection.output_class, 0)
        saveEditorState();
      }

    } else {
      saveConectionBanco(connection.input_id, connection.output_id, "connectionRemoved");
      saveEditorState();
    }



    console.log('Connection removed');
    //console.log(connection);

  });

  editor.on('nodeMoved', function(id) {
    saveEditorState();
    //console.log("Node moved " + id);
  });

  editor.on('zoom', function(zoom) {
    // console.log('Zoom level ' + zoom);
  });

  editor.on('translate', function(position) {
    //console.log('Translate x:' + position.x + ' y:' + position.y);
  });

  editor.on('addReroute', function(id) {
    console.log("Reroute added " + id);
  });

  editor.on('removeReroute', function(id) {
    // console.log("Reroute removed " + id);
  });

  var elements = document.getElementsByClassName('drag-drawflow');
  for (var i = 0; i < elements.length; i++) {
    elements[i].addEventListener('touchend', drop, false);
    elements[i].addEventListener('touchmove', positionMobile, false);
    elements[i].addEventListener('touchstart', drag, false);
  }



  var mobile_item_selec = '';
  var mobile_last_move = null;

  function positionMobile(ev) {
    mobile_last_move = ev;
  }

  function allowDrop(ev) {
    ev.preventDefault();
  }

  function drag(ev) {
    if (ev.type === "touchstart") {
      mobile_item_selec = ev.target.closest(".drag-drawflow").getAttribute('data-node');
      mobile_custom_field = ev.target.closest(".drag-drawflow").getAttribute('data-input');
      mobile_custom_field_label = ev.target.closest(".drag-drawflow").getAttribute('data-label');
    } else {
      ev.dataTransfer.setData("node", ev.target.getAttribute('data-node'));
      ev.dataTransfer.setData("input", ev.target.getAttribute('data-input'));
      ev.dataTransfer.setData("label", ev.target.getAttribute('data-label'));
    }
  }

  function drop(ev) {
    ev.preventDefault();

    var data = ev.dataTransfer ? ev.dataTransfer.getData("node") : mobile_item_selec;
    var customField = ev.dataTransfer ? ev.dataTransfer.getData("input") : mobile_custom_field;
    var customFieldLabel = ev.dataTransfer ? ev.dataTransfer.getData("label") : mobile_custom_field;

    console.log(customField);

    var targetGroup = ev.target.closest(".box-group");

    if (targetGroup) {
      // Se soltou dentro de um grupo, adiciona o HTML ao grupo
      addNodeToGroup(targetGroup, data);
    } else {
      // Se não soltou dentro de um grupo, adiciona o nó normalmente
      var pos_x = ev.clientX || mobile_last_move.touches[0].clientX;
      var pos_y = ev.clientY || mobile_last_move.touches[0].clientY;
      addNodeToDrawFlow(data, pos_x, pos_y, customField, customFieldLabel);
    }

    mobile_item_selec = '';
    mobile_custom_field = '';
  }


  var idInput = <?= $idInput ?>;

  /**
   * gera uma string unica
   *
   * @return void
   */
  function generateUniqueId() {
    idInput += 1;
    return `id_${idInput}`;
  }

  /**
   * Adiciona um item ao grupo
   */
  function addNodeToGroup(groupElement, name) {
    let htmlContent;
    var inputs = [];
    var id = generateUniqueId();

    switch (name) {
      case 'text':
        htmlContent = `
            <div class="box-form-input">      
             <div class="title-box-group">      
               <i class="fa-regular fa-trash-can btn-trash" onclick="removeNode('${id}',this)"></i>
               <span><i class="fa-regular fa-message"></i> Texto</span>               
             </div>
              <div class="form-group delete-input-${id}">
                <textarea id="${id}" data-type="text" class="form-control" onchange="saveFormData(this)"  df-${id}>text ${id} </textarea>
              </div>
            </div>
            `;
        inputs.push({
          id: id,
        });
        break;
      case 'sleep':
        htmlContent = `
            <div class="box-form-input">      
             <div class="title-box-group">      
               <i class="fa-regular fa-trash-can btn-trash" onclick="removeNode('${id}',this)"></i>
               <span><i class="fa-regular fa-clock"></i> Esperar</span>               
             </div>
              <div class="form-group delete-input-${id}">
                <input id="${id}" data-type="sleep" class="form-control" onchange="saveFormData(this)" value="1" type="number" min="0" max="9"  df-${id}/> 
              </div>
            </div>
            `;
        inputs.push({
          id: 1,
        });
        break;

      case 'image':
        htmlContent = `
            <div class="box-form-input">      
             <div class="title-box-group">      
               <i class="fa-regular fa-trash-can btn-trash" onclick="removeNode('${id}',this)"></i>
               <span><i class="fa-regular fa-image"></i> Image</span>               
             </div>
              <div class="form-group delete-input-${id}">
                <input id="${id}" class="form-control" type="file" data-type="image" accept="image/jpeg,image/png,image/gif"  onchange="saveFormData(this)"  df-${id}/>
              </div>
              <div class="image-preview-bot preview-${id}">
                <img id="img-${id}" src="" width="100px" height="100px" />
              </div>
            </div>
            `;
        break;

      case 'video':
        htmlContent = `
            <div class="box-form-input">      
             <div class="title-box-group">      
               <i class="fa-regular fa-trash-can btn-trash" onclick="removeNode('${id}',this)"></i>
               <span><i class="fa-solid fa-film"></i> Video</span>               
             </div>
              <div class="form-group delete-input-${id}">
                <input id="${id}" class="form-control" type="file" data-type="video" accept="video/mp4"  onchange="saveFormData(this)"  df-${id}/>
              </div>
              <div class="image-preview-bot preview-${id}">
                <video id="img-${id}" src="" width="150px" height="100px" controls></video>               
              </div>
            </div>
            `;
        break;

      case 'audio':
        htmlContent = `
            <div class="box-form-input">      
             <div class="title-box-group">      
               <i class="fa-regular fa-trash-can btn-trash" onclick="removeNode('${id}',this)"></i>
               <span><i class="fa-solid fa-music"></i> Audio</span>               
             </div>
              <div class="form-group delete-input-${id}">
                <input id="${id}" class="form-control" type="file" data-type="audio" accept="audio/mp3"  onchange="saveFormData(this)"  df-${id}/>
              </div>
              <div class="image-preview-bot preview-${id}">
                <audio id="img-${id}" src="" width="150px" height="100px" controls></audio>               
              </div>
            </div>
            `;
        break;

      case 'pdf':
        htmlContent = `
            <div class="box-form-input">      
             <div class="title-box-group">      
               <i class="fa-regular fa-trash-can btn-trash" onclick="removeNode('${id}',this)"></i>
               <span> <i class="fa-regular fa-file-pdf"></i> PDF</span>               
             </div>
              <div class="form-group delete-input-${id}">
                <input id="${id}" class="form-control" type="file" data-type="document" accept="application/pdf"  onchange="saveFormData(this)"  df-${id}/>
              </div>
              <div class="image-preview-bot preview-${id}">
                <a class="btn btn-danger" id="img-${id}" href="" download ><i class="fa-regular fa-file-pdf"></i> PDF</a>               
              </div>
            </div>
            `;
        break;

      default:
        return;
    }

    // Buscar o grupo pelo ID
    var grupo = document.getElementById("node-" + groupId);

    // Atualizar o HTML do nó no editor
    const nodeId = editor.getNodeFromId(groupId).id;
    const nodeData = editor.getNodeFromId(nodeId);

    // Garantir que nodeData.data existe e é um objeto
    if (!nodeData.data) {
      nodeData.data = {};
    }

    // Mesclar novos inputs com os dados existentes dentro de data.data
    nodeData.data = {
      ...nodeData.data, // Dados existentes em data.data
      ...inputs.reduce((acc, input) => {
        acc[input.name] = input.value;
        return acc;
      }, nodeData.data) // Mesclar com novos dados
    };

    // Atualizando manualmente o Drawflow com o novo conteúdo
    editor.updateNodeDataFromId(nodeId, nodeData);

    // Inserindo o novo HTML no grupo
    groupElement.insertAdjacentHTML('beforeend', htmlContent);

    // Salvando o estado do editor
    saveEditorState();
  }



  /**
   * adiciona um nó normalmente
   * como container
   */
  function addNodeToDrawFlow(name, pos_x, pos_y, custom_fields = null, custom_fields_label = null) {
    if (editor.editor_mode === 'fixed') {
      return false;
    }

    // Ajuste das coordenadas
    pos_x = pos_x * (editor.precanvas.clientWidth / (editor.precanvas.clientWidth * editor.zoom)) -
      (editor.precanvas.getBoundingClientRect().x * (editor.precanvas.clientWidth / (editor.precanvas.clientWidth * editor.zoom)));
    pos_y = pos_y * (editor.precanvas.clientHeight / (editor.precanvas.clientHeight * editor.zoom)) -
      (editor.precanvas.getBoundingClientRect().y * (editor.precanvas.clientHeight / (editor.precanvas.clientHeight * editor.zoom)));

    let groupHtml;
    let groupId;

    switch (name) {
      case 'group':
        // Contando quantos grupos já existem
        var groupCount = Object.values(editor.drawflow.drawflow.Home.data).filter(node => node.name === 'group').length;

        // Incrementando para o próximo grupo
        var newGroupNumber = groupCount + 1;
        var title = generateUniqueId();
        groupHtml = `
                <div>
                    <div class="title-box">
                      <i class="fa-duotone fa-solid fa-object-group"></i>
                      <span>
                        <input type="text" name="title-input"   placeholder="Group ${newGroupNumber}" class="form-control"  df-${title}  value="Group ${newGroupNumber}" onchange="openEditGroupModal(this)"> 
                      </span>
                    </div>
                </div>
                <div class="box-group"></div>
            `;

        $data = {
          title: title,
        };
        // Adicionando o grupo ao Drawflow
        groupId = editor.addNode('group', 1, 1, pos_x, pos_y, 'group', $data, groupHtml);
        saveGroupBanco(groupId, name, "crear");
        break;
      case 'desactivateAi':
        // Contando quantos grupos já existem
        var groupCount = Object.values(editor.drawflow.drawflow.Home.data).filter(node => node.name === 'group').length;

        // Incrementando para o próximo grupo
        var newGroupNumber = groupCount + 1;
        var title = generateUniqueId();
        // HTML com o número do grupo
        groupHtml = `
                <div>
                  <div class="title-box">
                      <i class="fa-solid fa-comment-slash"></i>
                      <span>
                        <input type="text" name="title-input"   placeholder="Group ${newGroupNumber}" class="form-control"  df-${title}  value="Desativar IA ${newGroupNumber}" readonly> 
                      </span>
                  </div>                   
                </div>               
            `;

        $data = {
          title: title,
        };
        // Adicionando o grupo ao Drawflow
        groupId = editor.addNode('group', 1, 1, pos_x, pos_y, 'desactivateAi', $data, groupHtml);
        saveGroupBanco(groupId, name, "crear");
        break;

      case 'moment-day':
        // Contando quantos grupos já existem
        var groupCount = Object.values(editor.drawflow.drawflow.Home.data).filter(node => node.name === 'group').length;

        // Incrementando para o próximo grupo
        var newGroupNumber = groupCount + 1;
        var title = generateUniqueId();
        // HTML com o número do grupo
        groupHtml = `
                <div>                
                    <div class="title-box">
                     <i class="fa-solid fa-sun"></i>
                     <input type="text" name="title-input"   placeholder="Group ${newGroupNumber}" class="form-control"  df-${title}  value="Momento do dia" readonly" onchange="openEditGroupModal(this)" readonly> 
                    </div>
                 </div> 
                 <div class="box-group-2">
                   <div class="box-form-input"> 
                     <p>Vai retornar:</p>
                     <span onclick="copyText(this)"><i class="fa-solid fa-mug-saucer"></i>morning</span>
                     <span onclick="copyText(this)"><i class="fa-solid fa-sun"></i>afternoon</span>
                     <span onclick="copyText(this)"><i class="fa-solid fa-cloud-moon"></i>evening</span>
                     <span onclick="copyText(this)"><i class="fa-regular fa-moon"></i>night</span>
                   </div>                  
                 </div>
             `;
        $data = {
          title: title,
        };
        // Adicionando o grupo ao Drawflow
        groupId = editor.addNode('group', 1, 1, pos_x, pos_y, 'moment-day', $data, groupHtml);
        saveGroupBanco(groupId, name, "crear");
        break;

      case 'sleep':
        // Contando quantos grupos já existem
        var groupCount = Object.values(editor.drawflow.drawflow.Home.data).filter(node => node.name === 'group').length;

        // Incrementando para o próximo grupo
        var newGroupNumber = groupCount + 1;
        var id = generateUniqueId();
        var title = generateUniqueId();

        groupHtml = `
                <div>
                    <div class="title-box"><i class="fa-regular fa-clock"></i>
                      <input type="text" name="title-input"   placeholder="Esperar ${newGroupNumber}" class="form-control"  df-${title}  value="Esperar ${newGroupNumber}" onchange="openEditGroupModal(this)" readonly> 
                    </div>
                </div>
                <div class="box-group-2">
                  <div class="box-form-input">      
                    <div class="title-box-group"></div>                    
                    <div class="form-group delete-input-${id}">
                         <input id="${id}" data-type="sleep" class="form-control" onchange="saveFormData(this)" value="0" type="number" min="0" max="9"  df-${id}/>                      
                    </div>
                    <span>Tempo em Segundos</span>                    
                  </div>
                </div> 
            `;

        $data = {
          id: id,
          title: title,
        };
        groupId = editor.addNode('group', 1, 1, pos_x, pos_y, 'sleep', $data, groupHtml);
        saveGroupBanco(groupId, name, "crear");
        break;

      case 'IA':

        // Contando quantos grupos já existem
        var groupCount = Object.values(editor.drawflow.drawflow.Home.data).filter(node => node.name === 'group').length;

        // Incrementando para o próximo grupo
        var newGroupNumber = groupCount + 1;
        var id = generateUniqueId();
        var title = generateUniqueId();

        // HTML com o número do grupo
        groupHtml = `
                <div>
                    <div class="title-box"><i class="fa-solid fa-robot"></i> 
                      <input type="text" name="title-input"   placeholder="AXIOM ${newGroupNumber}" class="form-control"  df-${title}  value="AXIOM ${newGroupNumber}" onchange="openEditGroupModal(this)">
                    </div>
                  </div>
                  <div class="box-group-2">
                    <div class="box-form-input">      
                      <div class="title-box-group"></div>                    
                      <div class="form-group delete-input-${id}">
                          <textarea id="${id}" data-type="text" class="form-control" onchange="saveFormData(this)"  df-${id}>text ${id} </textarea>
                      </div>
                      <span><i class="fa-solid fa-robot"></i> Palavras de saida separada por virgula</span> 
                    </div>
                  </div> 
              `;


        $data = {
          id: id,
          title: title,
        };
        // Adicionando o grupo ao Drawflow
        groupId = editor.addNode('group', 1, 1, pos_x, pos_y, 'IA', $data, groupHtml);
        saveGroupBanco(groupId, name, "crear");
        break;
      case 'location':

        // Contando quantos grupos já existem
        var groupCount = Object.values(editor.drawflow.drawflow.Home.data).filter(node => node.name === 'group').length;

        // Incrementando para o próximo grupo
        var newGroupNumber = groupCount + 1;
        var id = generateUniqueId();
        var title = generateUniqueId();
        var latitude = generateUniqueId();
        var longitude = generateUniqueId();
        var nameLocation = generateUniqueId();
        var endress = generateUniqueId();

        // HTML com o número do grupo
        groupHtml = `
                <div>
                    <div class="title-box"><i class="fa-solid fa-robot"></i> 
                      <input type="text" name="title-input"   placeholder="AXIOM ${newGroupNumber}" class="form-control"  df-${title}  value="AXIOM ${newGroupNumber}" onchange="openEditGroupModal(this)">
                    </div>
                  </div>
                  <div class="box-group-2">
                    <div class="box-form-input">     
                      <form name="form-location" method="post">                  
                          <div class="form-group delete-input-${id}">
                              <label for="${id}" class="form-label text-white">Name Location</label>
                              <input name="name" id="${nameLocation}" data-type="text" class="form-control"   df-${nameLocation} value="name ${nameLocation}" required/>
                          </div>
                          <div class="form-group delete-input-${id}">
                              <label for="${endress}" class="form-label text-white"><i class="fa-solid fa-location-dot"></i> Endress</label>
                              <textarea name="endress" id="${endress}" data-type="text" class="form-control"   df-${endress} required>endress ${endress} </textarea>
                          </div>
                          <div class="form-group delete-input-${id}">
                              <label for="${latitude}" class="form-label text-white">Latitude</label>
                              <input name="latitude" id="${latitude}" data-type="text" class="form-control"   df-${latitude} value="Latitude ${latitude} " required/>
                          </div>
                          <div class="form-group delete-input-${id}">
                              <label for="${longitude}" class="form-label text-white">Longitude</label>
                              <input name="longitude" id="${longitude}" data-type="text" class="form-control"  df-${longitude} value="Longitude ${longitude} " required/>
                          </div>
                      </form>                    
                    </div>
                  </div> 
              `;


        $data = {
          id: id,
          title: title,
          latitude: latitude,
          longitude: longitude,
          name: name,
          endress: endress,
        };
        // Adicionando o grupo ao Drawflow
        groupId = editor.addNode('group', 1, 1, pos_x, pos_y, 'location', $data, groupHtml);
        saveGroupBanco(groupId, name, "crear");
        break;
      case 'status-leads':

        // Contando quantos grupos já existem
        var groupCount = Object.values(editor.drawflow.drawflow.Home.data).filter(node => node.name === 'group').length;

        // Incrementando para o próximo grupo
        var newGroupNumber = groupCount + 1;
        var id = generateUniqueId();
        var title = generateUniqueId();

        var statusLead = <?php echo json_encode($ln); ?>;
        // HTML com o número do grupo
        groupHtml = `
                <div>
                    <div class="title-box">
                     <i class="fa-solid fa-diagram-project"></i> 
                     <input type="text" name="title-input"   placeholder="Status Leads ${newGroupNumber}" class="form-control"  df-${title}  value="Status Leads ${newGroupNumber}" onchange="openEditGroupModal(this)">
                    </div>
                 </div>
                 <div class="box-group-2">
                   <div class="box-form-input">      
                     <div class="title-box-group"></div>                    
                     <div class="form-group delete-input-${id}">
                         <select name="${id}" data-id="${id}" id="${id}" class="form-control"  data-type="text" data-input="output_0" df-${id} onchange="saveFormData(this)">
                         <option></option>
                               ${statusLead}
                         </select>                        
                     </div>
                     <span>Escolha o status para transferir o lead</span>                    
                   </div>
                 </div> 
             `;


        $data = {
          id: id,
          title: title,
        };
        // Adicionando o grupo ao Drawflow
        groupId = editor.addNode('group', 1, 1, pos_x, pos_y, 'IA', $data, groupHtml);
        saveGroupBanco(groupId, name, "crear");
        break;
      case 'staff':

        // Contando quantos grupos já existem
        var groupCount = Object.values(editor.drawflow.drawflow.Home.data).filter(node => node.name === 'group').length;

        // Incrementando para o próximo grupo
        var newGroupNumber = groupCount + 1;
        var id = generateUniqueId();
        var title = generateUniqueId();


        var staffid = <?php echo json_encode($staffid); ?>;
        // HTML com o número do grupo
        groupHtml = `
                <div>
                    <div class="title-box">
                        <i class="fa-solid fa-diagram-project"></i> 
                        <input type="text" name="title-input"   placeholder="Usuário ${newGroupNumber}" class="form-control"  df-${title}  value="Usuário ${newGroupNumber}" onchange="openEditGroupModal(this)">
                     </div>
                 </div>
                 <div class="box-group-2">
                   <div class="box-form-input">      
                     <div class="title-box-group"></div>                    
                     <div class="form-group delete-input-${id}">
                         <select name="${id}" data-id="${id}" id="${id}" class="form-control"  data-type="staff" data-input="output_0" df-${id} onchange="saveFormData(this)">
                         <option></option>
                               ${staffid}
                         </select>                        
                     </div>                  
                     <span>Escolha o usuário para transferir o lead</span>                    
                   </div>
                 </div> 
             `;


        $data = {
          id: id,
          title: title,
        };
        // Adicionando o grupo ao Drawflow
        groupId = editor.addNode('group', 1, 1, pos_x, pos_y, 'IA', $data, groupHtml);
        saveGroupBanco(groupId, name, "crear");
        break;
      case 'notification':

        // Contando quantos grupos já existem
        var groupCount = Object.values(editor.drawflow.drawflow.Home.data).filter(node => node.name === 'group').length;

        // Incrementando para o próximo grupo
        var newGroupNumber = groupCount + 1;
        var id = generateUniqueId();
        var texteArea = generateUniqueId();
        var title = generateUniqueId();

        var staffid = <?php echo json_encode($staffid); ?>;
        // HTML com o número do grupo
        groupHtml = `
                <div>
                    <div class="title-box"><i class="fa-solid fa-diagram-project"></i>
                    <input type="text" name="title-input"   placeholder="Notificação ${newGroupNumber}" class="form-control"  df-${title}  value="Notificação ${newGroupNumber}" onchange="openEditGroupModal(this)">
                    
                    </div>
                    
                </div>
                <div class="box-group-2">
                  <div class="box-form-input">      
                    <div class="title-box-group"></div>                    
                    <div class="form-group delete-input-${id}">
                        <select name="${id}" data-id="${id}" id="${id}" class="form-control" data-input="staff" data-type="notification" data-input="output_0" df-${id} onchange="saveFormDataNotification(this)">
                        <option></option>
                              ${staffid}
                        </select>                        
                    </div>
                    <div class="form-group delete-input-${texteArea}">
                        <textarea name="${texteArea}" id="${texteArea}"  data-id="${id}" data-input="notification" data-type="notification" class="form-control" onchange="saveFormDataNotification(this)"  df-${texteArea}>Notificação ${texteArea} </textarea>
                    </div>

                    <span>Escolha o usuário para notificado</span>                    
                  </div>
                </div> 
            `;

        $data = {
          id: id,
          texteArea: texteArea,
          title: title,
        };
        // Adicionando o grupo ao Drawflow
        groupId = editor.addNode('group', 1, 1, pos_x, pos_y, 'IA', $data, groupHtml);
        saveGroupBanco(groupId, name, "crear");
        break;
      case 'IA-ChatGPT':

        // Contando quantos grupos já existem
        var groupCount = Object.values(editor.drawflow.drawflow.Home.data).filter(node => node.name === 'group').length;

        // Incrementando para o próximo grupo
        var newGroupNumber = groupCount + 1;
        var id = generateUniqueId();
        var title = generateUniqueId();

        // HTML com o número do grupo
        groupHtml = `
                <div>
                    <div class="title-box"><i class="fa-solid fa-robot"></i>
                     <input type="text" name="title-input"   placeholder="OmniCore ${newGroupNumber}" class="form-control"  df-${title}  value="OmniCore ${newGroupNumber}" onchange="openEditGroupModal(this)">

                   
                    </div>
                </div>
                <div class="box-group-2 text-center">
                   <button type="button" class="btn btn-warning" id="${id}"  onclick="openChatGPT('${id}')" >OmniCore</button>
                </div> 
            `;

        $data = {
          id: id,
          title: title,
        };
        // Adicionando o grupo ao Drawflow
        groupId = editor.addNode('group', 1, 1, pos_x, pos_y, 'IA', $data, groupHtml);
        saveGroupBanco(groupId, name, "crear");
        break;
      case 'integretion-http':

        // Contando quantos grupos já existem
        var groupCount = Object.values(editor.drawflow.drawflow.Home.data).filter(node => node.name === 'group').length;

        // Incrementando para o próximo grupo
        var newGroupNumber = groupCount + 1;
        var id = generateUniqueId();
        var title = generateUniqueId();

        // HTML com o número do grupo
        groupHtml = `
                <div>
                    <div class="title-box"><i class="fa-solid fa-robot"></i>
                     <input type="text" name="title-input"   placeholder="Http Request ${newGroupNumber}" class="form-control"  df-${title}  value="Http Request ${newGroupNumber}" onchange="openEditGroupModal(this)">                   
                    </div>
                </div>
                <div class="box-group-2 text-center">
                   <button type="button" class="btn btn-warning" id="${id}"  onclick="openHttp('${id}')" >Http Request</button>
                </div> 
            `;

        $data = {
          id: id,
          title: title,
        };
        // Adicionando o grupo ao Drawflow
        groupId = editor.addNode('group', 1, 1, pos_x, pos_y, 'integretion-http', $data, groupHtml);
        saveGroupBanco(groupId, name, "crear");
        break;

      case 'agenda':
        // Contando quantos grupos já existem
        var groupCount = Object.values(editor.drawflow.drawflow.Home.data).filter(node => node.name === 'group').length;

        // Incrementando para o próximo grupo
        var newGroupNumber = groupCount + 1;
        var title = generateUniqueId();
        // HTML com o número do grupo
        groupHtml = `
                <div>
                    <div class="title-box"><i class="fa-solid fa-calendar-days"></i>
                      <input type="text" name="title-input"   placeholder="Agenda ${newGroupNumber}" class="form-control"  df-${title}  value="Agenda ${newGroupNumber}" onchange="openEditGroupModal(this)">
                    </div>
                </div>                
                `;

        $data = {
          title: title,
        };
        // Adicionando o grupo ao Drawflow
        groupId = editor.addNode('group', 1, 1, pos_x, pos_y, 'agenda', $data, groupHtml);
        saveGroupBanco(groupId, name, "crear");
        break;

      case 'inputs':

        // HTML com o número do grupo
        groupHtml = `
                <div>
                    <div class="title-box">
                      <i class="fa-solid fa-bullseye"></i>
                          <input type="text" name="title-input"   placeholder="Inputs ${custom_fields_label}" class="form-control"  df-${title}  value="Inputs ${custom_fields_label}" onchange="openEditGroupModal(this)" readonly>
                      </div>
                  </div>                
              `;


        // Adicionando o grupo ao Drawflow
        groupId = editor.addNode('group', 1, 1, pos_x, pos_y, 'custom-fields', {}, groupHtml);
        saveGroupBanco(groupId, name, "crear", custom_fields);
        break;
      case 'condition':
        // Contando quantos grupos já existem
        var groupCount = Object.values(editor.drawflow.drawflow.Home.data).filter(node => node.name === 'group').length;

        // Incrementando para o próximo grupo
        var newGroupNumber = groupCount + 1;
        // Gerando um novo identificador sequencial
        var id = generateUniqueId();
        var input = generateUniqueId();
        var select = generateUniqueId();
        var title = generateUniqueId();
        groupHtml = `
                  <div>
                     <div class="title-box"><i class="fa-solid fa-filter"></i>
                      <input type="text" name="title-input"   placeholder="Condition ${newGroupNumber}" class="form-control"  df-${title}  value="Condition ${newGroupNumber}" onchange="openEditGroupModal(this)" >
                     </div>

                      <div class='box-condition'>
                          <div class="box-form-input">                             
                            <div class="box-condition-title" id="condition-${id}" data-input="output_1" onclick="openConditionModal(this)"> 
                              Configure                   
                            </div>

                            <span class="btn btn-primary btn-add-condition" onclick="addcondition(this)"><i class="fa-solid fa-plus"></i></span> 

                            <div class="condition-input condition-${id}">
                              <span>Contém:<br>(Digitar palavras separadas por virgula)</span>
                              <span>Igual:<br>(Apenas digitar uma frase ou palavra)</span>
                              <div class="form-group">
                                 <input name="${input}" data-id="${id}" type="text"  data-type="condition" data-input="output_1" class="form-control" value="" df-${input} onchange="saveConditions(this)" />
                              </div>
                              <div class="form-group">
                                 <select  name="${select}" data-id="${id}" class="form-control"  data-type="condition"  data-input="output_1" df-${select} onchange="saveConditions(this)">
                                    <option value="igual">Igual</option>                  
                                    <option value="contem">Contém</option>
                                 </select>
                              </div>
                              <i class="fa-solid fa-angles-right icon-maker-condition"></i>
                              </div>
                             
                          </div>

                        <div class="box-form-input else" data-input="output_2"> 
                         <input type="hidden" data-else="${id}" value="output_2">
                          Else                  
                        </div>                      
                      </div>

                  </div>
                  `;

        $data = {
          input: input,
          select: select,
          title: title,
        };
        groupId = editor.addNode('condition', 1, 2, pos_x, pos_y, 'condition', $data, groupHtml);
        saveGroupBanco(groupId, name, "condition");
        saveGroupBanco(groupId, name, "crear");
        break;
      default:
        return;
    }
  }

  /**
   * adiciona um novo bloco de condição
   */
  function addcondition(element) {
    //fecha modal de condicional
    $('.condition-input').css('display', 'none');

    const boxGroup = element.parentElement;
    const boxCondition = boxGroup.parentElement;
    const allConditions = boxCondition.querySelectorAll('.box-form-input');
    const elseBlock = boxCondition.querySelector('.box-form-input:last-child');

    var id = generateUniqueId();
    var input = generateUniqueId();
    var select = generateUniqueId();

    // Novo identificador sequencial para o novo bloco
    const newOutputNumber = allConditions.length; // Novo número é baseado na contagem existente


    const newBlock = document.createElement('div');
    newBlock.className = 'box-form-input';
    newBlock.innerHTML = `
        
        <div class="box-condition-title" id="condition-${id}" data-input="output_${newOutputNumber}" onclick="openConditionModal(this)"> 
            Configure              
        </div>
        <span class="btn btn-primary btn-add-condition" onclick="addcondition(this)"><i class="fa-solid fa-plus"></i></span> 
        <div class="condition-input condition-${id}">
            <span>Contém:<br>(Digitar palavras separadas por virgula)</span>
            <span>Igual:<br>(Apenas digitar uma frase ou palavra)</span>
            <div class="form-group">
                <input name="${input}" data-id="${id}" type="text" data-type="condition" class="form-control" value="" data-input="output_${newOutputNumber}" df-${input} onchange="saveConditions(this)" />
            </div>
            <div class="form-group">
                <select  name="${select}" data-id="${id}" class="form-control" data-type="condition" data-input="output_${newOutputNumber}" df-${select} onchange="saveConditions(this)">
                  <option value="igual">Igual</option>                  
                  <option value="contem">Contém</option>
                </select>
            </div>
            <i class="fa-solid fa-angles-right icon-maker-condition"></i>           
          </div>
        </div> 
            `;


    // Inserir o novo bloco antes do bloco "Else"
    boxCondition.insertBefore(newBlock, elseBlock);

    const nodeId = editor.getNodeFromId(groupId).id;
    const nodeData = editor.getNodeFromId(nodeId);



    const inputs = Array.from(boxGroup.querySelectorAll('input, textarea, select')).map(input => ({
      name: input.getAttribute('name'),
      value: input.value
    }));

    nodeData.data.data = inputs.reduce((acc, input) => {
      acc[input.name] = input.value;
      return acc;
    }, {});

    editor.updateNodeDataFromId(nodeId, nodeData);
    $("#node-" + groupId + " .else").attr("data-input", `output_${newOutputNumber + 1}`);
    $("#node-" + groupId + " input[data-else]").val(`output_${newOutputNumber + 1}`);

    editor.addNodeOutput(groupId); // Adiciona uma saída ao nó   

  }









  /**
   * abre a modal para configurar a condicao do grupo
   */
  function openConditionModal(element) {
    var id = $(element).attr('id');
    console.log(id);
    var verificaDisplay = $("." + id).css('display');
    if (verificaDisplay == 'none') {
      $(".condition-input").css('display', 'none');
      $("." + id).css('display', 'flex');
    } else {
      $("." + id).css('display', 'none');
    }

  }


  /**
   * adiciona um textarea dentro de um grupo
   * não esta em uso
   */
  function addTextarea(button) {
    const boxGroup = button.parentElement;
    const uniqueId1 = generateUniqueId();

    const newTextarea = document.createElement('div');
    newTextarea.classList.add('form-group');
    newTextarea.innerHTML = `
        <textarea  id='${uniqueId1}' class='form-control' name='${uniqueId1}' value=''>configure</textarea>
    `;
    boxGroup.insertBefore(newTextarea, button);

    const nodeId = editor.getNodeFromId(groupId).id;
    const nodeData = editor.getNodeFromId(nodeId);

    nodeData.data.html = sanitizeHTML(boxGroup.innerHTML);

    const inputs = Array.from(boxGroup.querySelectorAll('input, textarea')).map(input => ({
      name: input.getAttribute('name'),
      value: input.value
    }));

    nodeData.data.data = inputs.reduce((acc, input) => {
      acc[input.name] = input.value;
      return acc;
    }, {});

    editor.updateNodeDataFromId(nodeId, nodeData);

    saveEditorState();
  }

  /**
   * saveEditorState
   * Salva o estado do Drawflow
   */
  function saveEditorState(statusActive = 0) {
    const drawflowData = editor.export();

    Object.keys(drawflowData.drawflow.Home.data).forEach(nodeId => {
      const node = drawflowData.drawflow.Home.data[nodeId];
      if (node.html) {
        const nodeElement = document.getElementById(`node-${nodeId}`);
        if (nodeElement) {
          const contentNode = nodeElement.querySelector('.drawflow_content_node');
          if (contentNode) {
            node.html = contentNode.innerHTML;
          }
        }
      }
    });

    var title = $('input[name="drawflow_title"]').val();

    //console.log(JSON.stringify(drawflowData, null, 4));

    var dataToSend = {
      id: <?= $drawflow->draw_id; ?>,
      drawflowData: drawflowData,
      status: statusActive,
      title: title,
    };

    $.ajax({
      url: site_url + 'contactcenter/save_automation',
      type: 'POST',
      data: JSON.stringify(dataToSend),
      contentType: 'application/json; charset=utf-8',
      success: function(response) {
        if (statusActive == 1) {
          Trigger("<span class='trigger_success trigger'>Salvo com sucesso</span>");
        } else if (statusActive == 2) {
          Trigger("<span class='trigger_alert trigger'>Salvo como rascunho</span>");
        } else if (statusActive == 3) {
          Trigger("<span class='trigger_alert trigger'>Desativado</span>");
        }
        setTimeout(function() {
          TriggerClose();
        }, 3000);

      },
      error: function(jqXHR, textStatus, errorThrown) {
        Trigger("<span class='trigger_error trigger'>Erro ao salvar</span>");
        setTimeout(function() {
          TriggerClose();
        }, 3000);
      }
    });
  }

  /**
   * removeNode
   * Remove um elemento do Drawflow
   * @param {integer} nodeId
   * @param {this} element
   */
  function removeNode(nodeId, element) {
    // Remove o elemento específico passado
    $(element).closest('.box-form-input').remove();

    // Remove o(s) elemento(s) adicional(is) associados ao nodeId
    $(".delete-input-" + nodeId).remove();

    // Remove o item do banco
    saveGroupBanco(nodeId, "", "removeItem");

    // Salva o estado do editor após a remoção
    saveEditorState();
  }






  /**
   * saveGroupBanco
   */
  function saveGroupBanco(id, type, action, custom_fields = null) {

    if (id) {
      var title = $('#node-' + id + ' input[name="title-input"]').val();

      $.ajax({
        url: site_url + 'contactcenter/save_group_banco',
        type: 'POST',
        data: {
          id: id,
          type: type,
          draw_id: <?= $drawflow->draw_id; ?>,
          action: action,
          title: title,
          custom_fields: custom_fields
        },
        success: function(response) {
          //console.log('Estado do editor salvo com sucesso:', response);
        },
      });

    }

  }

  /**
   * Troca o titulo do grupo para um input para edição
   */
  function openEditGroupModal(element) {
    var title = $(element).val();
    console.log(title);
    if (title) {
      $.ajax({
        url: site_url + 'contactcenter/save_group_banco',
        type: 'POST',
        data: {
          id: groupId,
          title: title,
          draw_id: <?= $drawflow->draw_id; ?>,
          action: "crear",
        },
        success: function(response) {
          //console.log('Estado do editor salvo com sucesso:', response);
        },
      });

    }
  }

  /**
   * saveConectionBanco
   */
  function saveConectionBanco(input_id, output_id, action) {


    $.ajax({
      url: site_url + 'contactcenter/save_group_banco',
      type: 'POST',
      dataType: 'json',
      data: {
        id: output_id,
        group_inputs: input_id,
        group_output: output_id,
        draw_id: <?= $drawflow->draw_id; ?>,
        action: action
      },
      success: function(response) {
        if (!response.retorno) {
          // Remove a coneção         
          editor.removeSingleConnection(output_id, input_id, 'output_1', 'input_1');

        }
      }
    });


  }


  // Clique direito do mouse tira o botão de deletar
  $(document).on("contextmenu", "#node-1", function(event) {
    event.preventDefault(); // Previne o menu padrão do clique direito
    if (groupId == 1) {
      $("#node-1 .drawflow-delete").remove();
    }

  });


  /**
   * salvar os dados do formulário item do grupo
   */
  function saveFormData(element) {
    var inputValue = $(element).val();
    var type = $(element).attr('data-type');
    var inputId = $(element).attr('id');

    if (type && inputId && groupId) {
      var formData = new FormData();

      formData.append('group_id', groupId);
      formData.append('type', type);
      formData.append('input_id', inputId);
      formData.append('draw_id', <?= $drawflow->draw_id; ?>);

      if (typeof csrfData !== "undefined") {
        formData.append(csrfData["token_name"], csrfData["hash"]);
      }

      // Verifica se o elemento é um input do tipo file
      if ($(element).attr('type') === 'file') {
        var file = $(element)[0].files[0];
        formData.append('file', file);
      } else {
        formData.append('text', inputValue);
      }

      $.ajax({
        url: site_url + 'contactcenter/save_group_children',
        type: 'POST',
        dataType: 'json',
        data: formData,
        processData: false, // Necessário para enviar o FormData corretamente
        contentType: false, // Necessário para não configurar o tipo de conteúdo padrão
        success: function(response) {
          if (response.url) {
            $(".preview-" + inputId).css('display', 'block');
            $("#img-" + inputId).attr('src', site_url + response.url);
            $("#img-" + inputId).attr('href', site_url + response.url);
            $("#" + inputId).remove();
          }
          saveEditorState();
        },
        error: function(xhr, status, error) {
          // Lida com possíveis erros
        }
      });
    }
  }


  /**
   * salva conexão da condição
   */
  function saveConectionCondition(groupId, output_class, input_id, input_class = null) {
    var formData = new FormData();

    formData.append('group_id', groupId);
    formData.append('next', input_id);
    formData.append('conexao', output_class);
    formData.append('type', "conditionConection");
    formData.append('draw_id', <?= $drawflow->draw_id; ?>);

    if (typeof csrfData !== "undefined") {
      formData.append(csrfData["token_name"], csrfData["hash"]);
    }
    $.ajax({
      url: site_url + 'contactcenter/save_group_children',
      type: 'POST',
      dataType: 'json',
      data: formData,
      processData: false, // Necessário para enviar o FormData corretamente
      contentType: false, // Necessário para não configurar o tipo de conteúdo padrão
      success: function(response) {
        if (response.retorno) {
          saveEditorState();
        } else {
          // Remove a coneção
          blockRemove = 1;
          editor.removeSingleConnection(groupId, input_id, output_class, input_class);
          blockRemove = 0;
        }

      },
      error: function(xhr, status, error) {
        // Lida com possíveis erros
      }
    });

  }


  function saveFormDataNotification(element) {
    var inputValue = $(element).val();
    var type = $(element).attr('data-type');
    var dataInput = $(element).attr('data-input');
    var inputId = $(element).attr('data-id');
    var formData = new FormData();

    if (dataInput == "staff") {
      formData.append('url', inputValue);
    } else {
      formData.append('text', inputValue);
    }

    formData.append('group_id', groupId);
    formData.append('input_id', inputId);
    formData.append('type', "notification");
    formData.append('draw_id', <?= $drawflow->draw_id; ?>);

    if (typeof csrfData !== "undefined") {
      formData.append(csrfData["token_name"], csrfData["hash"]);
    }
    $.ajax({
      url: site_url + 'contactcenter/save_group_children',
      type: 'POST',
      dataType: 'json',
      data: formData,
      processData: false, // Necessário para enviar o FormData corretamente
      contentType: false, // Necessário para não configurar o tipo de conteúdo padrão
      success: function(response) {
        if (response.retorno) {
          saveEditorState();
        } else {
          // Remove a coneção
          blockRemove = 1;
          editor.removeSingleConnection(groupId, input_id, output_class, input_class);
          blockRemove = 0;
        }

      },
      error: function(xhr, status, error) {
        // Lida com possíveis erros
      }
    });

  }


  /**
   * salvar os dados do formulário condição
   */
  function saveConditions(element) {
    var type = $(element).attr('data-type');
    var inputId = $(element).attr('data-id');
    var conexao = $(element).attr('data-input');

    var inputValue = $("input[data-id='" + inputId + "']").val();
    var inputElse = $("#node-" + groupId + " input[data-else]").val();
    var selectValue = $("select[data-id='" + inputId + "']").val();


    var formData = new FormData();

    formData.append('group_id', groupId);
    formData.append('type', type);
    formData.append('input_id', inputId);
    formData.append('draw_id', <?= $drawflow->draw_id; ?>);
    formData.append('text', inputValue);
    formData.append('operador', selectValue);
    formData.append('conexao', conexao);
    formData.append('else', inputElse);


    if (typeof csrfData !== "undefined") {
      formData.append(csrfData["token_name"], csrfData["hash"]);
    }
    $.ajax({
      url: site_url + 'contactcenter/save_group_children',
      type: 'POST',
      dataType: 'json',
      data: formData,
      processData: false, // Necessário para enviar o FormData corretamente
      contentType: false, // Necessário para não configurar o tipo de conteúdo padrão
      beforeSend: function() {
        var selectText = $("select[data-id='" + inputId + "'] option:selected").text();

        $("#node-" + groupId + " div[data-input='" + conexao + "']").html(selectText);
      },
      success: function(response) {
        saveEditorState();
      },
      error: function(xhr, status, error) {
        // Lida com possíveis erros
      }
    });


  }


  function openChatGPT(id) {

    $("#modalAssistant").modal('show');
    $("input[name='input_id']").val(id);
    $("input[name='id']").val(groupId);

    $.ajax({
      url: site_url + 'contactcenter/get_group_banco',
      type: 'POST',
      dataType: 'json',
      data: {
        id: groupId,
        draw_id: <?= $drawflow->draw_id; ?>
      },
      success: function(response) {
        if (response.retorno) {
          $("input[name='gpt_caracters']").val(response.gpt_caracters);
          $("textarea[name='gpt_prompt']").val(response.gpt_prompt);
          $("textarea[name='gpt_tag_exit']").val(response.gpt_tag_exit);
          $("select[name='gpt_model']").val(response.gpt_model).selectpicker('refresh');
        }

      },
    });

  }

  /**
   * salvar os dados do formulário do assistente
   */
  $("#form_assistant").submit(function(e) {
    e.preventDefault();
    e.stopPropagation();

    var Form = $(this);
    var Data = Form.serialize();

    $.ajax({
      url: site_url + 'contactcenter/save_group_banco',
      type: 'POST',
      dataType: 'json',
      data: Data,
      beforeSend: function() {
        $("#modalAssistant").modal('hide');
      },
      success: function(response) {
        if (response.retorno) {
          Trigger("<span class='trigger_success trigger'>Salvo com sucesso</span>");
        }
        setTimeout(function() {
          TriggerClose();
        }, 3000);

      },
      error: function(xhr, status, error) {
        // Lida com possíveis erros
      }
    });

  });

  // Inicializando o Ace Editor nos elementos de código
  var editorHeader = ace.edit("editorHeader");
  editorHeader.setTheme("ace/theme/monokai");
  editorHeader.session.setMode("ace/mode/json");


  var editorBody = ace.edit("editorBody");
  editorBody.setTheme("ace/theme/monokai");
  editorBody.session.setMode("ace/mode/json");

  var editorResponse = ace.edit("editor-response");
  editorResponse.setTheme("ace/theme/monokai");
  editorResponse.session.setMode("ace/mode/json");



  function openHttp(id) {

    $("#modalHttp").modal('show');
    $("input[name='input_id']").val(id);
    $("input[name='id']").val(groupId);
    $('.delete-json-field').remove();
    // Limpa o conteúdo dos editores Ace
    editorHeader.setValue('');
    editorBody.setValue('');

    // Limpa os campos do formulário
    $("input[name='urlhttp']").val('');
    $("select[name='method']").val('').selectpicker('refresh');
    $("input[name='timeout']").val('');
    $("#confirmResponse").prop('checked', false);

    $.ajax({
      url: site_url + 'contactcenter/get_http_request',
      type: 'POST',
      dataType: 'json',
      data: {
        id: groupId,
        draw_id: <?= $drawflow->draw_id; ?>
      },
      success: function(response) {
        if (response.http_request) {
          var request = JSON.parse(response.http_request);
          $("input[name='urlhttp']").val(request.urlhttp);
          $("select[name='method']").val(request.method).selectpicker('refresh');
          $("input[name='timeout']").val(request.timeout);

          if (request.codigoHeader) {
            editorHeader.setValue(JSON.stringify(JSON.parse(request.codigoHeader), null, 4));
          }

          if (request.codigoBody) {
            editorBody.setValue(JSON.stringify(JSON.parse(request.codigoBody), null, 4));
          }

          if (request.confirmResponse && request.confirmResponse == 1) {
            $("#confirmResponse").prop('checked', true);
          }
          let html = '';
         
          if (request.jsonField && request.jsonField.length > 0) {
            for (const id in request.jsonField) {
             var key = request.jsonField[id];
              html += `<div class="form-group col-md-6 delete-json-field">
                      <label class="control-label">${key}</label>
                      <div class='onoffswitch' data-toggle='tooltip' data-title='${key}' data-original-title='' title=''>
                      <input type='checkbox' name='jsonField[]' class='onoffswitch-checkbox '  value='${key}' id='${key}' checked >
                      <label class='onoffswitch-label' for='${key}'></label>
                      </div>
                      </div>`;              
            }           
            
            $('#jsonFieldsContainer').after(html);
          }

        }

      },
    });

  }




  /**
   * Testa o envio de uma requisição HTTP
   *
   * @return void
   */
  function testResponse() {
    var url = $("input[name='urlhttp']").val();
    var method = $("select[name='method']").val();
    var timeout = $("input[name='timeout']").val();



    // Obtendo o conteúdo dos editores
    var header = editorHeader.getValue();
    var body = editorBody.getValue();

    // Convertendo o header JSON em um objeto JavaScript
    var headersObj = {};
    if (header) {
      try {
        headersObj = JSON.parse(header);
      } catch (e) {
        console.error("Erro ao analisar o cabeçalho JSON:", e);
        return;
      }
    }

    $.ajax({
      url: url,
      type: method,
      headers: headersObj,
      data: body,
      beforeSend: function() {
        $(".progress").css('display', 'block');
        $("#progressBar").attr('data-percent', 50);
        $("#progressBar").attr('aria-valuenow', 50);
        $("#progressBar").css('width', '50%');
        $('.delete-json-field').remove();
      },
      success: function(response) {
        if (response) {
          $("#progressBar").attr('data-percent', 100);
          $("#progressBar").attr('aria-valuenow', 100);
          $("#progressBar").css('width', '100%');
          setTimeout(function() {
            $(".progress").css('display', 'none');
          }, 500);


          try {
            var jsonResponse = JSON.stringify(response, null, 4);
            editorResponse.setValue(jsonResponse);

            // Renderiza os campos da resposta JSON como checkboxes
            renderJsonFields(response);

          } catch (e) {
            editorResponse.setValue(response);
          }



        }
      },
      error: function(xhr, status, error) {
        console.error("Erro na requisição:", status, error);
        editorResponse.setValue("Erro: " + error);
      }
    });
  }

  /**
   * Renderiza os campos da resposta JSON como checkboxes
   */
  function renderJsonFields(obj, parent = null, name = null) {
    let html = '';


    if (Array.isArray(obj) && obj.length > 0) {
      obj = obj[0];
    }


    // Verifica se obj é um objeto e itera sobre suas chaves
    if (typeof obj === 'object' && obj !== null) {
      for (var key in obj) {

        var value = obj[key];

        if (typeof value === 'object') {
          renderJsonFields(value, 1, key);

        } else {

          if (parent == 1) {
            key = name + '.' + key;
          }

          html += `<div class="form-group col-md-6 delete-json-field">
                      <label class="control-label">${key}</label>
                      <div class='onoffswitch' data-toggle='tooltip' data-title='${key}' data-original-title='' title=''>
                        <input type='checkbox' name='jsonField[]' class='onoffswitch-checkbox '  value='${key}' id='${key}' >
                        <label class='onoffswitch-label' for='${key}'></label>
                      </div>
                    </div>`;
        }

      }
    }
    $('#jsonFieldsContainer').after(html);

  }






  /**
   * salvar os dados do formulário do assistente
   */
  $("#form_http").submit(function(e) {
    e.preventDefault();
    e.stopPropagation();

    // Obtém os valores dos editores
    var editorContentHeader = editorHeader.getValue();
    var editorContentBody = editorBody.getValue();

    // Função para validar JSON
    function isValidJSON(jsonString) {
      try {
        JSON.parse(jsonString);
        return true;
      } catch (e) {
        return false;
      }
    }

    // Verifica se os JSONs são válidos
    if (!isValidJSON(editorContentHeader)) {
      alert("Erro no código Header: JSON inválido.");
      return false; // Bloqueia o envio do formulário
    }

    if (!isValidJSON(editorContentBody)) {
      alert("Erro no código Body: JSON inválido.");
      return false; // Bloqueia o envio do formulário
    }

    // Continua com o envio do formulário se o JSON for válido
    var Form = $(this);
    var Data = Form.serialize();

    // Codifica os valores dos editores para serem enviados
    Data += '&codigoHeader=' + encodeURIComponent(editorContentHeader);
    Data += '&codigoBody=' + encodeURIComponent(editorContentBody);
    Data += '&group_id=' + groupId;

    $.ajax({
      url: site_url + 'contactcenter/save_group_children',
      type: 'POST',
      dataType: 'json',
      data: Data,
      beforeSend: function() {
        $("#modalHttp").modal('hide');
      },
      success: function(response) {
        if (response.retorno) {
          Trigger("<span class='trigger_success trigger'>Salvo com sucesso</span>");

          // Limpa o formulário
          Form.trigger('reset');

          // Limpa o conteúdo dos editores Ace
          editorHeader.setValue('');
          editorBody.setValue('');
        }
        setTimeout(function() {
          TriggerClose();
        }, 3000);
      },
      error: function(xhr, status, error) {
        // Lida com possíveis erros
      }
    });
  });





  /**
   * Copia o texto de um elemento
   */
  function copyText(element) {
    // Remove espaços em branco extras
    var textToCopy = $(element).text().trim();

    // Cria um elemento temporário de input para copiar o texto
    var tempInput = $("<input>");
    $("body").append(tempInput);

    // Define o valor do input temporário com o texto do elemento clicado
    tempInput.val(textToCopy).select();

    // Executa o comando de cópia
    document.execCommand("copy");

    // Remove o input temporário
    tempInput.remove();

    // Armazena o texto original
    var originalText = $(element).html();

    // Exibe "Copiado" no lugar do texto original
    $(element).html('<span class="copied">Copied</span>');

    // Retorna o texto original após 2 segundos
    setTimeout(function() {
      $(element).html(originalText);
    }, 2000);
  }


  /**
   * Fullscreen Drawflow
   */
  $(document).ready(function() {
    $("#fullscreen-btn").click(function() {
      var element = document.querySelector(".drawflow-fullscreen");

      if (!document.fullscreenElement && !document.webkitFullscreenElement &&
        !document.mozFullScreenElement && !document.msFullscreenElement) {
        // Entrar em tela cheia
        if (element.requestFullscreen) {
          element.requestFullscreen();
        } else if (element.mozRequestFullScreen) { // Firefox
          element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) { // Chrome, Safari, and Opera
          element.webkitRequestFullscreen();
        } else if (element.msRequestFullscreen) { // IE/Edge
          element.msRequestFullscreen();
        }
        $(this).html("<i class='fa-solid fa-compress'></i>"); // Alterar texto do botão
      } else {
        // Sair da tela cheia
        if (document.exitFullscreen) {
          document.exitFullscreen();
        } else if (document.mozCancelFullScreen) { // Firefox
          document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) { // Chrome, Safari, and Opera
          document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) { // IE/Edge
          document.msExitFullscreen();
        }
        $(this).html("<i class='fa-solid fa-expand'></i>"); // Restaurar texto do botão
      }
    });
  });


  /**
   * Form Location
   */
  $(document).ready(function() {
    // Função para verificar se todos os campos estão preenchidos
    function areAllFieldsFilled() {
      var allFilled = true;
      $("form[name='form-location'] :input[required]").each(function() {
        if ($(this).val() === '') {
          allFilled = false;
          return false; // interrompe o loop se encontrar um campo vazio
        }
      });
      return allFilled;
    }

    // Delegação de evento para elementos criados dinamicamente
    $(document).on('input change', "form[name='form-location'] :input[required]", function() {
      if (areAllFieldsFilled()) {
        var Form = $("form[name='form-location']");
        var Data = Form.serializeArray();

        if (typeof csrfData !== "undefined") {
          Data.push({
            name: csrfData["token_name"],
            value: csrfData["hash"]
          });
          Data.push({
            name: "group_id",
            value: groupId
          });
          Data.push({
            name: "draw_id",
            value: <?= $drawflow->draw_id; ?>
          });
          Data.push({
            name: "type",
            value: "location"
          });
        }

        $.ajax({
          url: site_url + 'contactcenter/save_group_children',
          type: 'POST',
          dataType: 'json',
          data: Data,
          success: function(response) {

          },
          error: function(xhr, status, error) {

          }
        });
      }
    });
  });




  /**
   * Undocumented function
   *
   * @return void
   */
  function count_static_groups() {
    $.ajax({
      url: site_url + 'contactcenter/get_static_fluxo',
      type: 'POST',
      dataType: 'json',
      data: {
        draw_id: <?= $drawflow->draw_id; ?>
      },
      success: function(response) {
        if (response.result) {
          // Itera sobre cada objeto no array 'result'
          response.result.forEach(function(item) {
            // Remove o elemento existente com a classe 'count' para evitar duplicação
            $("#node-" + item.group_id + " .drawflow_content_node").find('.count-fluxo').remove();

            // Concatena e insere o novo HTML
            var htmlContent = '<div class="count-fluxo">' +
              '<span class="badge badge-primary" data-toggle="tooltip" data-placement="top" data-original-title="Total de leads que passaram pelo fluxo"><i class="fa-regular fa-circle-play"></i> ' + item.total_read + '</span>' +
              '<a href="javascript:void(0)" onclick="openModalCount(' + item.group_id + ',<?= $drawflow->draw_id; ?>)" class="badge badge-primary text-danger" data-toggle="tooltip" data-placement="top" data-original-title="Total de leads parado nesse grupo"><i class="fa-regular fa-circle-pause"></i> ' + item.total_lost + '</a>' +
              '</div>';

            $("#node-" + item.group_id + " .drawflow_content_node").append(htmlContent);
          });

          console.log(response.result);
        }
      },
    });
  }
  count_static_groups();



  var table = null; // Variável global para armazenar a instância da DataTable

  function openModalCount(id, draw_id) {
    $("#modalCount").modal('show');

    // Verifica se a DataTable já foi inicializada
    if (!$.fn.DataTable.isDataTable("#tableCount")) {
      var table = initDataTableInline("#tableCount");
      table = $("#tableCount").DataTable();
    } else {
      table = $("#tableCount").DataTable();
      // Limpa os dados da DataTable
      table.clear().draw();
    }

    $.ajax({
      url: site_url + 'contactcenter/get_count_leads',
      type: 'POST',
      dataType: 'json',
      data: {
        id: id,
        draw_id: draw_id
      },
      success: function(response) {
        if (response.result) {
          // Itera sobre cada objeto no array 'result'
          response.result.forEach(function(item) {
            // Adiciona nova linha à DataTable
            table.row.add([
              item.id,
              item.name,
              item.fone,
              item.date
            ]).draw(false);
          });
        }
      },
    });
  }

  /**
   * exportDrawflow - Exporta o drawflow para um arquivo
   */
  function exportDrawflow(id) {
    $.ajax({
      url: site_url + 'contactcenter/export_drawflow',
      type: 'POST',
      dataType: 'json', // Certifique-se de que o tipo de dados esperado é JSON
      data: {
        id: id,
      },
      success: function(response) {
        if (response.data) {
          // Crie um Blob com o dado criptografado
          var blob = new Blob([response.data], {
            type: 'application/octet-stream'
          });

          // Crie uma URL temporária para o Blob
          var url = window.URL.createObjectURL(blob);

          // Crie um elemento <a> para iniciar o download
          var a = document.createElement('a');
          a.href = url;
          a.download = response.filename;

          // Simula um clique para iniciar o download
          document.body.appendChild(a);
          a.click();

          // Remova o elemento <a> e revogue a URL
          document.body.removeChild(a);
          window.URL.revokeObjectURL(url);
        }
      },
      error: function(xhr, status, error) {
        console.error('Erro ao exportar drawflow:', error);
      }
    });
  }

  function import_drawflow() {
    $("#modalInportDrawflow").modal('show');
  }
</script>


<div class="modal fade" id="modalCount" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header ">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title " id="exampleModalLongTitle">Leads</h4>
      </div>
      <div class="modal-body">
        <table class="table" id="tableCount">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Fone</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal export -->
<div class="modal fade" id="modalInportDrawflow" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header ">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title " id="exampleModalLongTitle">Código de Importar</h4>
      </div>
      <div class="modal-body">
        <?php echo form_open_multipart(admin_url("contactcenter/import_drawflow"), ""); ?>
        <input type="hidden" name="draw_id" value="<?= $drawflow->draw_id; ?>" />
        <div class="form-group">
          <label class="control-label">Escolha o arquivo</label>
          <input type="file" name="file" accept=".txt" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><?= _l("save"); ?></button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>