<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<link rel="stylesheet" href="https://unpkg.com/drawflow@0.0.55/dist/drawflow.min.css" />
<script src="https://unpkg.com/drawflow@0.0.55/dist/drawflow.min.js"></script>
<link rel="stylesheet" href="<?= module_dir_url('contactcenter', 'assets/css/drawflow/beautiful.css') ?>" />

<!-- CSRF Token for AJAX -->
<?php $csrf = get_csrf_for_ajax(); ?>
<input type="hidden" name="<?php echo $csrf['token_name']; ?>" value="<?php echo $csrf['hash']; ?>" id="csrf_token_input">
<script>
// Make CSRF data available globally
var csrfData = <?php echo json_encode($csrf); ?>;
</script>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="_buttons">
          <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data">
            <a href="<?= admin_url("contactcenter/assistant_edit/" . $assistants->id) ?>" class="btn btn-default">
              <i class="fa-solid fa-arrow-left"></i> <?= _l('contac_back'); ?>
            </a>
            <a href="<?= admin_url("contactcenter/assistant_ai") ?>" class="btn btn-default">
              <i class="fa-solid fa-list"></i> <?= _l('contac_assistant_list'); ?>
            </a>
            <div class="btn-group">
              <button type="button" class="btn btn-success" onclick="saveVisualBuilder()">
                <i class="fa-solid fa-save"></i> <?= _l('save'); ?>
              </button>
              <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu pull-right" role="menu">
                <li><a href="javascript:void(0);" onclick="loadTemplate()">
                  <i class="fa-solid fa-file-code"></i> <?= _l('contac_assistant_load_template'); ?>
                </a></li>
                <li class="divider"></li>
                <li><a href="javascript:void(0);" onclick="saveVisualBuilder(true)">
                  <i class="fa-solid fa-file-export"></i> <?= _l('contac_assistant_export_visual'); ?>
                </a></li>
                <li><a href="javascript:void(0);" onclick="importVisualBuilder()">
                  <i class="fa-solid fa-file-import"></i> <?= _l('contac_assistant_import_visual'); ?>
                </a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
        
        <!-- Media Upload Section -->
        <div class="panel_s tw-mt-2 sm:tw-mt-4">
          <div class="panel-body">
            <h5 class="tw-mt-0 tw-font-semibold tw-text-base tw-mb-3">
              <i class="fa fa-upload"></i> <?= _l('contac_assistant_upload_media_for_builder'); ?>
            </h5>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><?= _l('contac_assistant_upload_to_library'); ?></label>
                  <div class="input-group">
                    <input type="file" class="form-control" id="visual_library_media_file" accept="image/*,audio/*,video/*,.mp3,.wav,.ogg,.m4a,.aac,.mp4,.avi,.mov,.wmv,.flv,.webm,.mkv,.jpg,.jpeg,.png,.gif,.webp">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-info" onclick="uploadLibraryMedia()">
                        <i class="fa fa-upload"></i> <?= _l('upload'); ?>
                      </button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><?= _l('contac_assistant_upload_to_assistant'); ?></label>
                  <div class="input-group">
                    <input type="file" class="form-control" id="visual_assistant_media_file" accept="image/*,audio/*,video/*,.mp3,.wav,.ogg,.m4a,.aac,.mp4,.avi,.mov,.wmv,.flv,.webm,.mkv,.jpg,.jpeg,.png,.gif,.webp">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-primary" onclick="uploadAssistantMedia()">
                        <i class="fa fa-upload"></i> <?= _l('upload'); ?>
                      </button>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="panel_s tw-mt-2 sm:tw-mt-4 overflow-hidden drawflow-fullscreen">
          <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center tw-mb-4">
            <lord-icon
              src="https://cdn.lordicon.com/uttrirxf.json"
              trigger="loop"
              delay="2000"
              colors="primary:#00e09b,secondary:#00e09b"
              style="width:50px;height:50px">
            </lord-icon>
            <span><?= $assistants->ai_name . " - " . _l('contac_assistant_visual_builder'); ?></span>
          </h4>

          <div class="wrapper-drawflow">
            <div class="menu-drawflow">
              <div class="form-group">
                <input type="text" name="assistant_name" class="form-control" value="<?= htmlspecialchars($assistants->ai_name); ?>" readonly>
              </div>
              
              <!-- Instructions Section -->
              <div class="title-box-menu">
                <i class="fa-solid fa-file-lines"></i> <?= _l('contac_assistant_instructions'); ?>
              </div>
              <div class="content-menu">
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="<?= _l('contac_assistant_add_instruction_node'); ?>" draggable="true" ondragstart="drag(event)" data-node="instruction">
                  <i class="fa-solid fa-file-text"></i><span><?= _l('contac_assistant_instruction_node'); ?></span>
                </div>
              </div>

              <!-- Functions Section -->
              <div class="title-box-menu">
                <i class="fa-solid fa-gear"></i> <?= _l('contac_assistant_functions'); ?>
              </div>
              <div class="content-menu">
                <?php foreach ($available_functions as $func_key => $func_label): ?>
                  <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="<?= htmlspecialchars($func_label); ?>" draggable="true" ondragstart="drag(event)" data-node="function" data-function="<?= $func_key ?>">
                    <i class="fa-solid fa-puzzle-piece"></i><span><?= htmlspecialchars($func_label); ?></span>
                  </div>
                <?php endforeach; ?>
              </div>

              <!-- Media Section -->
              <div class="title-box-menu">
                <i class="fa-solid fa-images"></i> <?= _l('contac_assistant_media'); ?>
              </div>
              <div class="content-menu">
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="<?= _l('contac_assistant_add_media_node'); ?>" draggable="true" ondragstart="drag(event)" data-node="media">
                  <i class="fa-solid fa-image"></i><span><?= _l('contac_assistant_media_node'); ?></span>
                </div>
              </div>

              <!-- Settings Section -->
              <div class="title-box-menu">
                <i class="fa-solid fa-cog"></i> <?= _l('contac_assistant_settings'); ?>
              </div>
              <div class="content-menu">
                <div class="drag-drawflow" data-toggle="tooltip" data-placement="top" data-original-title="<?= _l('contac_assistant_model_settings'); ?>" draggable="true" ondragstart="drag(event)" data-node="settings">
                  <i class="fa-solid fa-sliders"></i><span><?= _l('contac_assistant_model_node'); ?></span>
                </div>
              </div>
            </div>

            <div class="content-drawflow">
              <div id="drawflow" ondrop="drop(event)" ondragover="allowDrop(event)">
                <button id="fullscreen-btn" class="btn btn-primary">
                  <i class="fa-solid fa-expand"></i>
                </button>
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

<!-- Modal for Instruction Node -->
<div class="modal fade" id="modalInstruction" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?= _l('contac_assistant_edit_instruction'); ?></h4>
      </div>
      <div class="modal-body">
        <form id="formInstruction">
          <input type="hidden" name="node_id" id="instruction_node_id" />
          <div class="form-group">
            <label><?= _l('contac_assistant_instruction_title'); ?></label>
            <input type="text" class="form-control" name="title" id="instruction_title" placeholder="<?= _l('contac_assistant_instruction_title_placeholder'); ?>" />
          </div>
          <div class="form-group">
            <label><?= _l('contac_assistant_instruction_content'); ?></label>
            <textarea class="form-control" name="content" id="instruction_content" rows="15" placeholder="<?= _l('contac_assistant_instruction_content_placeholder'); ?>"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _l('close'); ?></button>
        <button type="button" class="btn btn-primary" onclick="saveInstructionNode()"><?= _l('save'); ?></button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Function Node -->
<div class="modal fade" id="modalFunction" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?= _l('contac_assistant_edit_function'); ?></h4>
      </div>
      <div class="modal-body">
        <form id="formFunction">
          <input type="hidden" name="node_id" id="function_node_id" />
          <input type="hidden" name="function_name" id="function_name" />
          <div class="form-group">
            <label><?= _l('contac_assistant_function_name'); ?></label>
            <input type="text" class="form-control" id="function_display_name" readonly />
          </div>
          <div class="form-group" id="function_config_container">
            <!-- Dynamic configuration based on function type -->
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _l('close'); ?></button>
        <button type="button" class="btn btn-primary" onclick="saveFunctionNode()"><?= _l('save'); ?></button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Media Node -->
<div class="modal fade" id="modalMedia" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?= _l('contac_assistant_edit_media'); ?></h4>
      </div>
      <div class="modal-body">
        <form id="formMedia">
          <input type="hidden" name="node_id" id="media_node_id" />
          <div class="form-group">
            <label><?= _l('contac_assistant_select_media'); ?></label>
            <select class="form-control" id="media_select">
              <option value=""><?= _l('contac_assistant_select_media_placeholder'); ?></option>
              <?php if (isset($media_files) && !empty($media_files)): ?>
                <?php foreach ($media_files as $media): ?>
                  <option value="<?= $media->id ?>"><?= htmlspecialchars($media->file_name) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
              <?php if (isset($library_media) && !empty($library_media)): ?>
                <?php foreach ($library_media as $media): ?>
                  <option value="library_<?= $media->id ?>">[<?= _l('contac_assistant_library'); ?>] <?= htmlspecialchars($media->file_name) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
            <small class="help-block text-muted">
              <?= _l('contac_assistant_upload_media_hint'); ?>
            </small>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _l('close'); ?></button>
        <button type="button" class="btn btn-primary" onclick="saveMediaNode()"><?= _l('save'); ?></button>
      </div>
    </div>
  </div>
</div>

<script>
var id = document.getElementById("drawflow");
const editor = new Drawflow(id);
editor.reroute = true;
editor.reroute_fix_curvature = true;
editor.force_first_input = false;

var assistantId = <?= $assistants->id ?>;
var currentNodeId = null;
var idInput = 1;

// Load existing visual data
var dataToImport = <?= $visual_data ? json_encode($visual_data) : json_encode([
  "drawflow" => [
    "Home" => [
      "data" => [
        "1" => [
          "id" => 1,
          "name" => "start",
          "data" => [],
          "class" => "start",
          "html" => '<div><div class="title-box"><i class="fa-regular fa-circle-play"></i> Start</div></div>',
          "typenode" => false,
          "inputs" => [],
          "outputs" => [
            "output_1" => ["connections" => []]
          ],
          "pos_x" => 50,
          "pos_y" => 50
        ]
      ]
    ]
  ]
]); ?>;

editor.start();
editor.import(dataToImport);

// Drag and drop handlers
function allowDrop(ev) {
  ev.preventDefault();
}

function drag(ev) {
  ev.dataTransfer.setData("node", ev.target.getAttribute('data-node'));
  ev.dataTransfer.setData("function", ev.target.getAttribute('data-function') || '');
}

function drop(ev) {
  ev.preventDefault();
  var nodeType = ev.dataTransfer.getData("node");
  var functionName = ev.dataTransfer.getData("function");
  var pos_x = ev.clientX;
  var pos_y = ev.clientY;
  
  addNodeToDrawFlow(nodeType, pos_x, pos_y, functionName);
}

function generateUniqueId() {
  idInput += 1;
  return `id_${idInput}`;
}

function addNodeToDrawFlow(name, pos_x, pos_y, functionName = '') {
  if (editor.editor_mode === 'fixed') {
    return false;
  }

  // Adjust coordinates
  pos_x = pos_x * (editor.precanvas.clientWidth / (editor.precanvas.clientWidth * editor.zoom)) -
    (editor.precanvas.getBoundingClientRect().x * (editor.precanvas.clientWidth / (editor.precanvas.clientWidth * editor.zoom)));
  pos_y = pos_y * (editor.precanvas.clientHeight / (editor.precanvas.clientHeight * editor.zoom)) -
    (editor.precanvas.getBoundingClientRect().y * (editor.precanvas.clientHeight / (editor.precanvas.clientHeight * editor.zoom)));

  let nodeHtml;
  let nodeData = {};
  let nodeId = generateUniqueId();

  switch (name) {
    case 'start':
      nodeHtml = '<div><div class="title-box"><i class="fa-regular fa-circle-play"></i> Start</div></div>';
      nodeId = editor.addNode('start', 0, 1, pos_x, pos_y, 'start', {}, nodeHtml);
      break;

    case 'instruction':
      nodeData = {
        node_id: nodeId,
        title: '<?= _l("contac_assistant_new_instruction"); ?>',
        content: ''
      };
      nodeHtml = `
        <div>
          <div class="title-box">
            <i class="fa-solid fa-file-text"></i>
            <span class="node-title"><?= _l("contac_assistant_new_instruction"); ?></span>
            <i class="fa-solid fa-edit edit-node-btn" onclick="editInstructionNode('${nodeId}')" style="float: right; cursor: pointer; margin-left: 10px;"></i>
          </div>
        </div>
      `;
      nodeId = editor.addNode('instruction', 1, 1, pos_x, pos_y, 'instruction', nodeData, nodeHtml);
      break;

    case 'function':
      nodeData = {
        node_id: nodeId,
        function_name: functionName,
        config: {}
      };
      var functionLabel = '<?= _l("contac_assistant_function"); ?>';
      <?php foreach ($available_functions as $func_key => $func_label): ?>
        if (functionName === '<?= $func_key ?>') {
          functionLabel = '<?= addslashes($func_label) ?>';
        }
      <?php endforeach; ?>
      nodeHtml = `
        <div>
          <div class="title-box">
            <i class="fa-solid fa-puzzle-piece"></i>
            <span class="node-title">${functionLabel}</span>
            <i class="fa-solid fa-edit edit-node-btn" onclick="editFunctionNode('${nodeId}')" style="float: right; cursor: pointer; margin-left: 10px;"></i>
          </div>
        </div>
      `;
      nodeId = editor.addNode('function', 1, 1, pos_x, pos_y, 'function', nodeData, nodeHtml);
      break;

    case 'media':
      nodeData = {
        node_id: nodeId,
        media_id: null
      };
      nodeHtml = `
        <div>
          <div class="title-box">
            <i class="fa-solid fa-image"></i>
            <span class="node-title"><?= _l("contac_assistant_media"); ?></span>
            <i class="fa-solid fa-edit edit-node-btn" onclick="editMediaNode('${nodeId}')" style="float: right; cursor: pointer; margin-left: 10px;"></i>
          </div>
        </div>
      `;
      nodeId = editor.addNode('media', 1, 1, pos_x, pos_y, 'media', nodeData, nodeHtml);
      break;

    case 'settings':
      nodeData = {
        node_id: nodeId,
        model: '<?= $assistants->model ?>'
      };
      nodeHtml = `
        <div>
          <div class="title-box">
            <i class="fa-solid fa-sliders"></i>
            <span class="node-title"><?= _l("contac_assistant_model_settings"); ?></span>
          </div>
        </div>
      `;
      nodeId = editor.addNode('settings', 1, 1, pos_x, pos_y, 'settings', nodeData, nodeHtml);
      break;
  }

  saveVisualBuilder();
}

function editInstructionNode(nodeId) {
  currentNodeId = nodeId;
  var node = editor.getNodeFromId(nodeId);
  $('#instruction_node_id').val(nodeId);
  $('#instruction_title').val(node.data.title || '');
  $('#instruction_content').val(node.data.content || '');
  $('#modalInstruction').modal('show');
}

function editFunctionNode(nodeId) {
  currentNodeId = nodeId;
  var node = editor.getNodeFromId(nodeId);
  $('#function_node_id').val(nodeId);
  $('#function_name').val(node.data.function_name || '');
  
  var functionLabel = '';
  <?php foreach ($available_functions as $func_key => $func_label): ?>
    if (node.data.function_name === '<?= $func_key ?>') {
      functionLabel = '<?= addslashes($func_label) ?>';
    }
  <?php endforeach; ?>
  $('#function_display_name').val(functionLabel);
  
  // Load function-specific configuration
  loadFunctionConfig(node.data.function_name, node.data.config || {});
  
  $('#modalFunction').modal('show');
}

function editMediaNode(nodeId) {
  currentNodeId = nodeId;
  var node = editor.getNodeFromId(nodeId);
  $('#media_node_id').val(nodeId);
  $('#media_select').val(node.data.media_id || '');
  $('#modalMedia').modal('show');
}

function loadFunctionConfig(functionName, config) {
  var html = '';
  
  if (functionName === 'manage_conversation') {
    html = `
      <div class="form-group">
        <label><input type="checkbox" id="func_disable_ai" ${config.disable_ai ? 'checked' : ''}> <?= _l("contac_assistent_disable_ai"); ?></label>
      </div>
      <div class="form-group">
        <label><?= _l("contac_assistent_change_staff_owner"); ?></label>
        <select class="form-control" id="func_change_staff">
          <option value=""><?= _l("contac_assistent_no_change"); ?></option>
          <?php if (isset($staff_members)): foreach ($staff_members as $staff): ?>
            <option value="<?= $staff['staffid'] ?>" ${config.change_staff_owner == <?= $staff['staffid'] ?> ? 'selected' : ''}>
              <?= get_staff_full_name($staff['staffid']) ?>
            </option>
          <?php endforeach; endif; ?>
        </select>
      </div>
      <div class="form-group">
        <label><?= _l("contac_assistent_change_status"); ?></label>
        <select class="form-control" id="func_change_status">
          <option value=""><?= _l("contac_assistent_no_change"); ?></option>
          <?php if (isset($leads_status)): foreach ($leads_status as $status): ?>
            <option value="<?= $status['id'] ?>" ${config.change_status == <?= $status['id'] ?> ? 'selected' : ''}>
              <?= htmlspecialchars($status['name']) ?>
            </option>
          <?php endforeach; endif; ?>
        </select>
      </div>
    `;
  }
  
  $('#function_config_container').html(html);
}

function saveInstructionNode() {
  var nodeId = $('#instruction_node_id').val();
  var node = editor.getNodeFromId(nodeId);
  
  node.data.title = $('#instruction_title').val();
  node.data.content = $('#instruction_content').val();
  
  // Update node HTML
  var nodeElement = document.getElementById('node-' + nodeId);
  if (nodeElement) {
    var titleElement = nodeElement.querySelector('.node-title');
    if (titleElement) {
      titleElement.textContent = node.data.title || '<?= _l("contac_assistant_new_instruction"); ?>';
    }
  }
  
  editor.updateNodeDataFromId(nodeId, node);
  $('#modalInstruction').modal('hide');
  saveVisualBuilder();
}

function saveFunctionNode() {
  var nodeId = $('#function_node_id').val();
  var node = editor.getNodeFromId(nodeId);
  
  node.data.config = {
    disable_ai: $('#func_disable_ai').is(':checked'),
    change_staff_owner: $('#func_change_staff').val(),
    change_status: $('#func_change_status').val()
  };
  
  editor.updateNodeDataFromId(nodeId, node);
  $('#modalFunction').modal('hide');
  saveVisualBuilder();
}

function saveMediaNode() {
  var nodeId = $('#media_node_id').val();
  var node = editor.getNodeFromId(nodeId);
  
  node.data.media_id = $('#media_select').val();
  
  editor.updateNodeDataFromId(nodeId, node);
  $('#modalMedia').modal('hide');
  saveVisualBuilder();
}

function saveVisualBuilder(exportOnly = false) {
  const drawflowData = editor.export();
  
  if (exportOnly) {
    // Export to JSON file
    var dataStr = JSON.stringify(drawflowData, null, 2);
    var dataBlob = new Blob([dataStr], {type: 'application/json'});
    var url = URL.createObjectURL(dataBlob);
    var link = document.createElement('a');
    link.href = url;
    link.download = 'assistant_visual_' + assistantId + '.json';
    link.click();
    return;
  }
  
  // Get CSRF token - prioritize the hidden input (most up-to-date)
  var csrfTokenName = '';
  var csrfTokenValue = '';
  
  // First try the hidden input we created (most reliable)
  var csrfInput = $('#csrf_token_input');
  if (csrfInput.length) {
    csrfTokenName = csrfInput.attr('name');
    csrfTokenValue = csrfInput.val();
  }
  // Fallback to global csrfData
  else if (typeof csrfData !== 'undefined' && csrfData.token_name && csrfData.hash) {
    csrfTokenName = csrfData.token_name;
    csrfTokenValue = csrfData.hash;
  }
  // Last resort: use PHP-generated values
  else {
    csrfTokenName = '<?php echo $csrf['token_name']; ?>';
    csrfTokenValue = '<?php echo $csrf['hash']; ?>';
  }
  
  // Debug: log CSRF token info
  if (!csrfTokenName || !csrfTokenValue) {
    console.error('CSRF token missing!', {
      csrfInput: csrfInput.length,
      csrfData: typeof csrfData !== 'undefined' ? csrfData : 'undefined',
      tokenName: csrfTokenName,
      tokenValue: csrfTokenValue ? '***' : null
    });
  }
  
  // Use form data (not JSON) so CodeIgniter CSRF works properly
  // The $.ajaxSetup() from init_head() should add CSRF automatically, but we include it explicitly too
  var formDataToSend = {
    assistant_id: assistantId,
    drawflowData: JSON.stringify(drawflowData)
  };
  
  // Add CSRF token explicitly (ajaxSetup should add it too, but this ensures it's there)
  if (csrfTokenName && csrfTokenValue) {
    formDataToSend[csrfTokenName] = csrfTokenValue;
  } else {
    console.error('CSRF token not found! Token name:', csrfTokenName, 'Value:', csrfTokenValue);
  }
  
  $.ajax({
    url: site_url + 'contactcenter/save_assistant_visual_builder',
    type: 'POST',
    data: formDataToSend,
    dataType: 'json',
    beforeSend: function(xhr) {
      // Set CSRF token header as backup
      if (csrfTokenName && csrfTokenValue) {
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfTokenValue);
      }
    },
    success: function(response) {
      var result = typeof response === 'string' ? JSON.parse(response) : response;
      if (result.success) {
        if (typeof alert_float !== 'undefined') {
          alert_float('success', result.message || '<?= _l("contac_save"); ?>');
        } else {
          alert(result.message || '<?= _l("contac_save"); ?>');
        }
      } else {
        if (typeof alert_float !== 'undefined') {
          alert_float('danger', result.message || '<?= _l("contac_save_error"); ?>');
        } else {
          alert(result.message || '<?= _l("contac_save_error"); ?>');
        }
      }
    },
    error: function(xhr, status, error) {
      var errorMessage = '<?= _l("contac_save_error"); ?>';
      var response = null;
      
      // Try to parse error response
      try {
        if (xhr.responseText) {
          response = JSON.parse(xhr.responseText);
          if (response.message) {
            errorMessage = response.message;
          }
          if (response.csrf_error) {
            errorMessage = '<?= _l("contac_assistant_csrf_error"); ?>';
          }
        }
      } catch (e) {
        // Ignore parse errors
      }
      
      // Check for CSRF error
      if (xhr.status === 403 || xhr.status === 419 || (response && response.csrf_error)) {
        errorMessage = '<?= _l("contac_assistant_csrf_error"); ?>';
        if (confirm(errorMessage + '\n\nRefresh the page?')) {
          location.reload();
          return;
        }
      }
      
      if (typeof alert_float !== 'undefined') {
        alert_float('danger', errorMessage);
      } else {
        alert(errorMessage);
      }
    }
  });
}

function importVisualBuilder() {
  var input = document.createElement('input');
  input.type = 'file';
  input.accept = '.json';
  input.onchange = function(e) {
    var file = e.target.files[0];
    var reader = new FileReader();
    reader.onload = function(e) {
      try {
        var data = JSON.parse(e.target.result);
        editor.import(data);
        saveVisualBuilder();
        if (typeof alert_float !== 'undefined') {
          alert_float('success', '<?= _l("contac_assistant_template_loaded"); ?>');
        }
      } catch (err) {
        if (typeof alert_float !== 'undefined') {
          alert_float('danger', '<?= _l("contac_assistant_import_error"); ?>');
        } else {
          alert('<?= _l("contac_assistant_import_error"); ?>');
        }
      }
    };
    reader.readAsText(file);
  };
  input.click();
}

function loadTemplate() {
  if (confirm('<?= _l("contac_assistant_load_template_confirm"); ?>')) {
    $.ajax({
      url: site_url + 'contactcenter/get_assistant_template',
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        // Import the drawflow data from template
        if (data.drawflow) {
          editor.import(data.drawflow);
          saveVisualBuilder();
          if (typeof alert_float !== 'undefined') {
            alert_float('success', '<?= _l("contac_assistant_template_loaded"); ?>');
          }
        } else {
          if (typeof alert_float !== 'undefined') {
            alert_float('warning', '<?= _l("contac_assistant_template_invalid"); ?>');
          }
        }
      },
      error: function() {
        if (typeof alert_float !== 'undefined') {
          alert_float('danger', '<?= _l("contac_assistant_template_load_error"); ?>');
        } else {
          alert('<?= _l("contac_assistant_template_load_error"); ?>');
        }
      }
    });
  }
}

// Event listeners
editor.on('nodeCreated', function(id) {
  saveVisualBuilder();
});

editor.on('nodeRemoved', function(id) {
  saveVisualBuilder();
});

editor.on('nodeSelected', function(id) {
  currentNodeId = id;
});

editor.on('connectionCreated', function(connection) {
  saveVisualBuilder();
});

editor.on('connectionRemoved', function(connection) {
  saveVisualBuilder();
});

editor.on('nodeMoved', function(id) {
  saveVisualBuilder();
});

// Upload functions
function uploadLibraryMedia() {
  var fileInput = $('#visual_library_media_file')[0];
  
  if (!fileInput.files || !fileInput.files[0]) {
    if (typeof alert_float !== 'undefined') {
      alert_float('warning', '<?= _l("contac_assistant_select_file"); ?>');
    } else {
      alert('<?= _l("contac_assistant_select_file"); ?>');
    }
    return;
  }
  
  var formData = new FormData();
  formData.append('assist_id', assistantId);
  formData.append('media_file', fileInput.files[0]);
  formData.append('is_library', '1');
  
  // Get CSRF token
  var csrfTokenName = '';
  var csrfTokenValue = '';
  
  if (typeof csrfData !== 'undefined' && csrfData.token_name && csrfData.hash) {
    csrfTokenName = csrfData.token_name;
    csrfTokenValue = csrfData.hash;
  } else {
    var csrfInput = $('#csrf_token_input');
    if (csrfInput.length) {
      csrfTokenName = csrfInput.attr('name');
      csrfTokenValue = csrfInput.val();
    }
  }
  
  if (csrfTokenName && csrfTokenValue) {
    formData.append(csrfTokenName, csrfTokenValue);
  }
  
  $.ajax({
    url: site_url + 'contactcenter/upload_assistant_media',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    beforeSend: function(xhr) {
      if (csrfTokenName && csrfTokenValue) {
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfTokenValue);
      }
    },
    success: function(response) {
      if (response.result) {
        if (typeof alert_float !== 'undefined') {
          alert_float('success', response.message || '<?= _l("contac_assistant_library_media_uploaded"); ?>');
        }
        // Reload page to refresh media list
        setTimeout(function() {
          location.reload();
        }, 1000);
      } else {
        if (typeof alert_float !== 'undefined') {
          alert_float('danger', response.message || '<?= _l("contac_assistant_upload_error"); ?>');
        }
      }
    },
    error: function(xhr, status, error) {
      var errorMessage = '<?= _l("contac_assistant_upload_error"); ?>';
      
      if (xhr.status === 403 || xhr.status === 419) {
        errorMessage = '<?= _l("contac_assistant_csrf_error"); ?>';
        if (confirm(errorMessage + '\n\n<?= _l("contac_assistant_refresh_page"); ?>')) {
          location.reload();
          return;
        }
      }
      
      if (typeof alert_float !== 'undefined') {
        alert_float('danger', errorMessage);
      } else {
        alert(errorMessage);
      }
    }
  });
}

function uploadAssistantMedia() {
  var fileInput = $('#visual_assistant_media_file')[0];
  
  if (!fileInput.files || !fileInput.files[0]) {
    if (typeof alert_float !== 'undefined') {
      alert_float('warning', '<?= _l("contac_assistant_select_file"); ?>');
    } else {
      alert('<?= _l("contac_assistant_select_file"); ?>');
    }
    return;
  }
  
  var formData = new FormData();
  formData.append('assist_id', assistantId);
  formData.append('media_file', fileInput.files[0]);
  formData.append('is_library', '0');
  
  // Get CSRF token
  var csrfTokenName = '';
  var csrfTokenValue = '';
  
  if (typeof csrfData !== 'undefined' && csrfData.token_name && csrfData.hash) {
    csrfTokenName = csrfData.token_name;
    csrfTokenValue = csrfData.hash;
  } else {
    var csrfInput = $('#csrf_token_input');
    if (csrfInput.length) {
      csrfTokenName = csrfInput.attr('name');
      csrfTokenValue = csrfInput.val();
    }
  }
  
  if (csrfTokenName && csrfTokenValue) {
    formData.append(csrfTokenName, csrfTokenValue);
  }
  
  $.ajax({
    url: site_url + 'contactcenter/upload_assistant_media',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    beforeSend: function(xhr) {
      if (csrfTokenName && csrfTokenValue) {
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfTokenValue);
      }
    },
    success: function(response) {
      if (response.result) {
        if (typeof alert_float !== 'undefined') {
          alert_float('success', response.message || '<?= _l("contac_assistant_media_uploaded"); ?>');
        }
        // Reload page to refresh media list
        setTimeout(function() {
          location.reload();
        }, 1000);
      } else {
        if (typeof alert_float !== 'undefined') {
          alert_float('danger', response.message || '<?= _l("contac_assistant_upload_error"); ?>');
        }
      }
    },
    error: function(xhr, status, error) {
      var errorMessage = '<?= _l("contac_assistant_upload_error"); ?>';
      
      if (xhr.status === 403 || xhr.status === 419) {
        errorMessage = '<?= _l("contac_assistant_csrf_error"); ?>';
        if (confirm(errorMessage + '\n\n<?= _l("contac_assistant_refresh_page"); ?>')) {
          location.reload();
          return;
        }
      }
      
      if (typeof alert_float !== 'undefined') {
        alert_float('danger', errorMessage);
      } else {
        alert(errorMessage);
      }
    }
  });
}

// Fullscreen button
$(document).ready(function() {
  $("#fullscreen-btn").click(function() {
    var element = document.querySelector(".drawflow-fullscreen");
    if (!document.fullscreenElement) {
      if (element.requestFullscreen) {
        element.requestFullscreen();
      } else if (element.webkitRequestFullscreen) {
        element.webkitRequestFullscreen();
      }
      $(this).html("<i class='fa-solid fa-compress'></i>");
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
      }
      $(this).html("<i class='fa-solid fa-expand'></i>");
    }
  });
});
</script>
