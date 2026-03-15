<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <lord-icon
                                src="https://cdn.lordicon.com/wzrwaorf.json"
                                trigger="loop"
                                delay="2000"
                                colors="primary:#00e09b,secondary:#00e09b"
                                style="width:50px;height:50px">
                            </lord-icon>

                            <span>
                                <?php echo _l('contac_conversation_list'); ?>
                            </span>
                        </h4>
                        <hr class="hr-panel-separator" />
                        <div class="tw-mb-2 sm:tw-mb-4">
                            <div class="_buttons">                              
                                    <button  class="btn btn-primary" data-toggle="modal" data-target="#modalDevice">
                                        <i class="fa-regular fa-plus tw-mr-1"></i>
                                        <?php echo _l('contac_conversation_list_new'); ?>
                                    </button>                       
                              
                                    <a  href="<?= admin_url("contactcenter/conversation_engine")  ?>" class="btn btn-primary pull-right">
                                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                        <?php echo _l('contac_back'); ?>
                                    </a >
                            </div>
                        </div>  

                        <div class="panel_s">
                            <table id="contact_conversation_list" class="table" >
                                <thead>
                                    <tr>
                                        <th>#ID</th>  
                                        <th><?= _l("contac_conversation_text"); ?></th>     
                                        <th><?= _l("contac_conversation_image"); ?></th>
                                        <th><?= _l("contac_editar"); ?></th>
                                        <th><?= _l("contac_excluir"); ?></th>                                       
                                    </tr>
                                </thead>
                                <tbody>                                       
                                    <?php
                                 
                                    foreach ($list as $con) {
                                        extract((array) $con);

                                       
                                           if($media_type == "video"){                                            
                                               $media = "<video  class='conversation_video' controls src='" . site_url("uploads/{$list_image}") . "'></video>";
                                           }elseif($media_type && $media_type == "image"){
                                               $media = "<img class='conversation_image' src='" . site_url("uploads/{$list_image}") . "'>";
                                            }elseif($media_type && $media_type == "audio"){    
                                               $media = "<audio class='conversation_audio' controls src='" . site_url("uploads/{$list_image}") . "'></audio>";
                                           }else{
                                               $media = "";
                                           }
                                       

                                        $list_text_escaped = htmlspecialchars($list_text, ENT_QUOTES, 'UTF-8');
                                        $list_image_escaped = htmlspecialchars($list_image, ENT_QUOTES, 'UTF-8');
                                        $media_type_escaped = htmlspecialchars($media_type, ENT_QUOTES, 'UTF-8');
                                        
                                        echo "<tr class='contact_draganddrop list_{$list_id}' data-id='{$list_id}'>
                                                <td>{$list_ordem}</td>                                                    
                                                <td>".($list_text ? $list_text : _l("contac_conversation_not_image"))."</td>                                              
                                                <td>{$media}</td>
                                                <td><a href='javascript:void(0);' onclick='edit_conversation_list({$list_id}, " . json_encode($list_text) . ", " . json_encode($list_image) . ", " . json_encode($media_type) . ")'><i class='fa fa-edit'></i></a></td>
                                                <td><a href='javascript:void(0);' onclick='delete_conversation_list({$list_id})'><i class='fa-solid fa-trash-can'></i></a></td>                                                    
                                             </tr>";
                                    }
                                    ?>
                                </tbody>                         
                            </table>
                        </div> 

                    </div>               



                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalDevice" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title " id="exampleModalLongTitle"><?= _l("contac_conversation_list_new"); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart(admin_url('contactcenter/add_conversation_engine_list'), ['id' => 'form_conversation_list']); ?> 
                <input type="hidden" name="con_id" value="<?= $conversation->con_id ?>" >
                <input type="hidden" name="list_id" id="list_id" value="" >
                
                <div class="form-group">
                    <label><strong><?php echo _l('available_merge_fields'); ?></strong></label>
                    <div class="merge-fields-container" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 style="margin-top: 0;"><?php echo _l('lead_merge_fields'); ?></h5>
                                <?php 
                                // Legacy fields for backward compatibility
                                ?>
                                <span class="btn btn-sm btn-info" onclick="insert_text('FirstName')" style="margin: 2px;">{FirstName}</span>
                                <span class="btn btn-sm btn-info" onclick="insert_text('Lead')" style="margin: 2px;">{Lead}</span>
                                <span class="btn btn-sm btn-info" onclick="insert_text('Agente')" style="margin: 2px;">{Agente}</span>
                                <br><br>
                                
                                <?php 
                                // Display all lead merge fields
                                if (isset($merge_fields) && is_array($merge_fields)) {
                                    foreach ($merge_fields as $field) {
                                        if (isset($field['key']) && !empty($field['key'])) {
                                            $field_name = isset($field['name']) ? $field['name'] : str_replace(['{', '}'], '', $field['key']);
                                            // Pass the full key including braces
                                            $field_key_js = htmlspecialchars($field['key'], ENT_QUOTES);
                                            echo '<span class="btn btn-sm btn-default" onclick="insert_text(\'' . $field_key_js . '\')" style="margin: 2px;" title="' . htmlspecialchars($field_name) . '">' . htmlspecialchars($field['key']) . '</span>';
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted"><?php echo _l('merge_fields_help_text'); ?></small>
                </div>
                <hr>
               
                <div class="form-group">
                    <label><?= _l("contac_conversation_list_new"); ?></label>
                    <div  class="pull-right"   style="width:180px; display: flex; justify-content: space-between;">
                        <a href="javascript:void(0);" class="btn btn-default" onclick="create_variacoe_openai()"><?= _l("contac_conversation_text_variacoes"); ?></a>
                        <input type="number" style="width: 70px;"  class="form-control" id="variacoes" max="15" min='1' value="10">
                    </div>

                    <div class="clearfix tw-mb-2"></div>
                    <textarea id="list_text" type="text" class="form-control" name="list_text" rows="5" placeholder="<?= _l("contac_conversation_list_new"); ?>" ></textarea>
                </div> 
                <div class="form-group">
                    <label for="image" class="profile-image"><?= _l("contac_conversation_image"); ?></label>
                    <input type="file" name="file" accept="image/gif,image/jpeg,image/png,video/mp4,audio/mp3" class="form-control" id="image">
                    <div id="current_media" style="margin-top: 10px; display: none;">
                        <small class="text-muted">Current media: <span id="current_media_name"></span></small>
                        <br>
                        <small class="text-info">Leave empty to keep current media</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                <?php echo form_close(); ?> 
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(document).ready(function () {
        initDataTableInline("#contact_conversation_list");

        //Limpa os formularios das modal
        $('.modal').on('hidden.bs.modal', function (e) {
            // Reseta o formulário quando fecha modal 
            $(this).find('form')[0].reset();
            $('#list_id').val('');
            $('#current_media').hide();
            $('#current_media_name').text('');
            $('#exampleModalLongTitle').text('<?= _l("contac_conversation_list_new"); ?>');
            $('#form_conversation_list').attr('action', '<?= admin_url('contactcenter/add_conversation_engine_list') ?>');
        });
    });

    function insert_text(text){
        // If text already contains braces, use it as-is, otherwise wrap it
        if (text.indexOf('{') === 0 && text.lastIndexOf('}') === text.length - 1) {
            $("#list_text").val($("#list_text").val() + text);
        } else {
            $("#list_text").val($("#list_text").val() + "{" + text + "}");
        }
    }

    $(document).ready(function () {
        // Array para armazenar os IDs dos elementos
        var id = [];
        $('.contact_draganddrop').attr('draggable', true);

        // DRAG START
        $("html").on("dragstart", ".contact_draganddrop", function (event) {
            // Pega o ID do elemento
            id = $(this).data('id');
        });

        // DROP EVENT
        $("html").on("drop", ".contact_draganddrop", function (event) {
            event.preventDefault();
            event.stopPropagation();

            // Pega o ID e a ordem do elemento de onde foi solto
            var wcDropElementId = $(this).data('id');
            var wcDropElementOrder = $(this).index() + 1; // Index começa em 0, então adicionamos 1 para começar de 1

            // Verifica se o elemento está sendo solto acima ou abaixo do elemento arrastado
            var mouseY = event.originalEvent.pageY;
            var dropElementY = $(this).offset().top;
            var diffY = mouseY - dropElementY;

            // Move o elemento para a posição correta
            if (diffY < $(this).height() / 2) {
                $(this).before($(".contact_draganddrop[data-id='" + id + "']"));
            } else {
                $(this).after($(".contact_draganddrop[data-id='" + id + "']"));
            }

            // Atualiza a ordem dos elementos
            var reorder = [];
            $(".contact_draganddrop").each(function (index) {
                var id = $(this).data('id');
                var order = index + 1; // Index começa em 0, então adicionamos 1 para começar de 1
                reorder.push({id: id, order: order});
            });

            // AJAX é chamado após a mudança de posição dos elementos
            $.post(site_url + "contactcenter/ajax_conversation_list_order", {data: reorder}, function (response) {
                // Lida com a resposta do servidor aqui, se necessário
                console.log(response);
            });
        });

        // Evita comportamento padrão do drop
        $("html").on("dragover", ".contact_draganddrop", function (event) {
            event.preventDefault();
            event.stopPropagation();
        });
    });








    function delete_conversation_list(id) {
        $.ajax({
            url: site_url + "contactcenter/ajax_conversation_list",
            data: {id: id},
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                if (data.result) {
                    $(".list_" + id).fadeOut();
                }

            }
        });
    }

    function edit_conversation_list(id, text, image, media_type) {
        $('#list_id').val(id);
        $('#list_text').val(text);
        $('#exampleModalLongTitle').text('<?= _l("contac_editar"); ?>');
        $('#form_conversation_list').attr('action', '<?= admin_url('contactcenter/update_conversation_engine_list') ?>');
        
        if (image && image !== '') {
            $('#current_media').show();
            $('#current_media_name').text(image);
        } else {
            $('#current_media').hide();
        }
        
        $('#modalDevice').modal('show');
    }









</script>
</body>
</html>
