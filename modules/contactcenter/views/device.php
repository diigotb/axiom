<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <lord-icon src="https://cdn.lordicon.com/uttrirxf.json" trigger="loop" delay="2000" colors="primary:#00e09b,secondary:#00e09b" style="width:50px;height:50px">
                            </lord-icon>

                            <span>
                                <?php echo _l('contac_device'); ?>
                            </span>
                        </h4>
                        <hr class="hr-panel-separator" />
                        <div class="tw-mb-2 sm:tw-mb-4">
                            <div class="col-md-12">
                                <?php
                                //pega o parametro da url
                                $aviso = $this->input->get('device');
                                if ($aviso == "true") {
                                    echo '<div class="alert alert-warning " role="alert">' . _l("contac_assistent_aviso_limit_device") . '</div>';
                                }
                                ?>
                            </div>
                            <div class="_buttons">
                                <?php if (has_permission('contractcenter', '', 'create')) { ?>
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalDevice">
                                        <i class="fa-regular fa-plus tw-mr-1"></i>
                                        <?php echo _l('contac_device_new'); ?>
                                    </button>

                                    <button class="btn btn-primary" onclick="redirectToAssistantPage()">
                                        <i class="fa-regular fa-plus tw-mr-1"></i>
                                        <img src="<?= base_url('modules/contactcenter/logo_axiom_white.png') ?>" alt="AXIOM" class="btn-logo-img" style="height:20px;width:auto;object-fit:contain;vertical-align:middle">
                                    </button>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="panel_s">
                            <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
                                <table id="contact_device" class="table" style="min-width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
                                            <th><?= _l("contac_device"); ?></th>
                                            <th><?= _l("contac_phone_user"); ?></th>
                                            <th><?= _l("contac_number_phone"); ?></th>
                                            <th><?= _l("contac_phone_type"); ?></th>
                                            <th><?= _l("contact_conexao_type"); ?></th>
                                            <th><?= _l("contac_number_token"); ?></th>
                                            <th>AI</th>
                                            <th><?= _l("contac_phone_status"); ?></th>
                                            <th><?= _l("contac_active"); ?></th>
                                            <th><i class="fas fa-sync-alt"></i></th>
                                            <th><?= _l("contact_conexao_type_system"); ?></th>
                                            <th><?= _l("contact_conexao_type_local"); ?></th>
                                            <th><?= _l("contac_phone_date"); ?></th>
                                            <th><?= _l("contact_assistant_ai_show"); ?></th>
                                            <th><?= _l("contac_conversation_engine"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                    foreach ($device as $dev) {
                                        // Get is_active before extract
                                        $is_active_device = isset($dev->is_active) ? $dev->is_active : 1;
                                        extract((array) $dev);


                                        if ($dev_openai == 1) {
                                            $AI = _l("contac_yes");
                                        } else {
                                            $AI = _l("contac_no");
                                        }

                                        if ($dev_engine == 1) {
                                            $motor = _l("contac_yes");
                                        } else {
                                            $motor = _l("contac_no");
                                        }

                                        if ($dev_type == 1) {
                                            $type = _l("contac_phone_type_system");
                                        } else if ($dev_type == 2) {
                                            $type = _l("contac_phone_type_individual");
                                        } else if ($dev_type == 3) {
                                            $type = _l("contac_phone_type_multiple");
                                        } else {
                                            $type = _l("contac_phone_type_api");
                                        }

                                        if ($api_local == 0) {
                                            $api_title = _l("contact_conexao_type_system");
                                        } else {
                                            $api_title = _l("contact_conexao_type_local");
                                        }

                                        echo "<tr>
                                                <td>{$dev_id}</td>                                                    
                                                <td> 
                                                    <div class='box_thumbTlabeCommunity'> 
                                                        <div>    
                                                           <a class='device-name' href='" . admin_url('contactcenter/chatsingle/' . $dev_id) . "'>{$dev_name}</a> 
                                                            <div class='row-options'>";

                                        echo "<a href='" . admin_url("contactcenter/qrcode_single/{$dev_id}") . "' >" . _l("contac_qrcode") . "</a> |                                                             
                                                                <a href='javascript:void(0);'class='text-danger' onclick='desconnect_device({$dev_id})' >" . _l("contac_disconnected") . "</a> |";

                                        echo "<a href='javascript:void(0);' onclick='edit_device({$dev_id})'>" . _l("contac_editar") . "</a> |  
                                                                <a href='javascript:void(0);'class='text-danger' onclick='delete_device({$dev_id})' >" . _l("contac_excluir") . "</a>
                                                            </div>        
                                                        </div>    
                                                    </div>    
                                                </td>              
                                                <td>" . ($staffid ? get_staff_full_name($staffid) : "") . "</td>                                 
                                                <td>{$dev_number}</td>                                 
                                                <td>{$type}</td>                                 
                                                <td>{$api_title}</td>                                 
                                                <td>{$dev_token}</td>                                 
                                                <td>{$AI}</td>                                 
                                                <td>" . label_status_device_online($status) . "</td>                                 
                                                <td>";
                                        
                                        // Use the is_active_device variable set before extract
                                        $active_checked = $is_active_device ? 'checked' : '';
                                        echo "<div class='onoffswitch' data-toggle='tooltip' data-title='Toggle device active status'>
                                                    <input type='checkbox' class='onoffswitch-checkbox' id='device_active_{$dev_id}' {$active_checked} onchange='toggle_device_active({$dev_id}, this.checked)'>
                                                    <label class='onoffswitch-label' for='device_active_{$dev_id}'></label>
                                                </div>";
                                        
                                        echo "</td>
                                                <td>
                                                    <button type='button' class='btn btn-info btn-sm' id='reload_device_{$dev_id}' onclick='reload_device({$dev_id})'>
                                                        <i class='fa fa-sync-alt'></i>
                                                    </button>
                                                </td>                                 
                                                <td>" . label_status_device($api_web_status) . "</td>                                 
                                                <td>" . label_status_device($api_local_status) . "</td>                                 
                                                <td>" . _dt($dev_date) . "</td>  
                                                <td>" . ($assistant_ai_id ? get_name_assistant($assistant_ai_id) : "") . "</td>    
                                                <td>{$motor}</td>
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
</div>

<!-- Modal -->
<div class="modal fade" id="modalDevice" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title " id="exampleModalLongTitle"><?= _l("contac_device_new"); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart(admin_url('contactcenter/add_device'), ["id" => "form_add_device"]); ?>
                <input type="hidden" name="dev_id" value="" />
                <div class="form-group">
                    <label><?= _l("contac_device_name"); ?></label>
                    <input type="text" class="form-control" name="dev_name" placeholder="<?= _l("contac_device_name"); ?>" required>
                </div>
                <div class="form-group">
                    <label><?= _l("contac_number_phone"); ?></label>
                    <input type="text" class="form-control" name="dev_number" placeholder="+5517991191234" required>
                </div>
                <div class="form-group">
                    <label><?= _l("contac_phone_type"); ?></label>
                    <select name="dev_type" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value="2"><?= _l("contac_phone_type_individual"); ?></option>
                        <option value="1"><?= _l("contac_phone_type_system"); ?></option>
                        <option value="3"><?= _l("contac_phone_type_multiple"); ?></option>
                        <option value="4"><?= _l("contac_phone_type_api"); ?></option>
                    </select>
                </div>

                <!-- <div class="form-group">
                    <label><?= _l("contact_conexao_type"); ?></label>
                    <select name="api_local" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required>
                        <option value="0"><?= _l("contact_conexao_type_system"); ?></option>
                        <option value="1"><?= _l("contact_conexao_type_local"); ?></option>                       
                    </select>
                </div> -->

                <div class="form-group">
                    <label><?= _l("contac_on_ai"); ?></label>
                    <select id="dev_openai" name="dev_openai" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value="1"><?= _l("drawflow_ia"); ?></option>
                        <option value="2"><?= _l("drawflow_ia_fluxo"); ?></option>
                        <option value="0"><?= _l("contac_no"); ?></option>
                    </select>
                </div>


                <div class="form-group" id="chatbot_id">
                    <label><?= _l("drawflow_flow"); ?></label>
                    <select name="chatbot_id" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

                        <?php foreach ($drawflow as $chatbot) { ?>
                            <option value="<?= $chatbot->draw_id  ?>"><?= $chatbot->title  ?></option>
                        <?php } ?>

                    </select>
                </div>


                <div class="form-group">
                    <label><?= _l("contac_phone_user"); ?></label>
                    <select name="staffid" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                        <option></option>
                        <?php foreach ($members as $member) { ?>
                            <option value="<?php echo $member['staffid']; ?>">
                                <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>API Server</label>
                    <select name="server_id" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                        <option></option>
                        <?php foreach ($servers as $server) {  ?>
                            <option value="<?php echo $server->id; ?>">
                                <?php echo $server->name . ' - v' . $server->version; ?>
                            </option>
                        <?php  } ?>
                    </select>
                </div>



                <div class="form-group" id="dev_instance_name">
                    <label><?= _l("contac_name_instance_device"); ?></label>
                    <input type="text" class="form-control" name="dev_instance_name" placeholder="<?= _l("contac_name_instance_device"); ?>" required>
                </div>

                <div class="form-group">
                    <label><?= _l("contac_number_token"); ?></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="dev_token" placeholder="<?= _l("contac_number_token"); ?>" required>
                        <span class="input-group-addon" data-toggle="tooltip" data-title="<?= _l("contac_generate_token"); ?>">
                            <a href="#" class="generate_password" onclick="get_guid();return false;"><i class="fa fa-refresh"></i></a>
                        </span>
                    </div>
                </div>



                <?php if (get_option("active_audio_contactcenter_elevenlabs") == 1) { ?>
                    <div class="form-group">
                        <label><?= _l("contac_token_voz_id"); ?></label>
                        <input type="text" class="form-control" name="dev_voz_id" placeholder="ASDACQWDAASD" required>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <label><?= _l("contact_assistant_ai_show"); ?></label>
                    <select name="assistant_ai_id" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" required>
                        <option></option>
                        <?php foreach ($assistants as $assistant) { ?>
                            <option value="<?php echo $assistant->id; ?>">
                                <?php echo $assistant->ai_name . ' ' . $assistant->ai_token; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>


                <div class="form-group">
                    <label><?= _l("contac_on_motor"); ?></label>
                    <select name="dev_engine" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required>
                        <option value="1"><?= _l("contac_yes"); ?></option>
                        <option value="0"><?= _l("contac_no"); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label><?= _l("chat_show_messages_all_devices"); ?></label>
                    <div class="onoffswitch" data-toggle="tooltip" data-title="<?= _l("chat_show_messages_all_devices_help"); ?>">
                        <input type="checkbox" class="onoffswitch-checkbox" name="show_messages_all_devices" id="show_messages_all_devices" value="1">
                        <label class="onoffswitch-label" for="show_messages_all_devices"></label>
                    </div>
                    <small class="form-text text-muted"><?= _l("chat_show_messages_all_devices_help"); ?></small>
                </div>

                <hr />
                <div class="form-group">
                    <label><?= _l("contac_open_contract"); ?></label>
                    <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_open_contract") ?>' data-original-title='' title=''>
                        <input type='checkbox' class='onoffswitch-checkbox ' onclick="contactcenter_status_contract()" id="status_contract">
                        <label class='onoffswitch-label' for='status_contract'></label>
                    </div>
                </div>

                <div id="contract" class="hidden">
                    <div class="form-group">
                        <label><?= _l("contac_category_contract"); ?></label>
                        <select name="contract_category" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                            <option></option>
                            <?php foreach ($category_contract as $category) { ?>
                                <option value="<?php echo $category->id; ?>">
                                    <?php echo $category->name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= _l("contac_model_contract"); ?></label>
                        <select name="contract_template" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                            <option></option>
                            <?php foreach ($models_contract as $models) { ?>
                                <option value="<?php echo $models->id; ?>">
                                    <?php echo $models->name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= _l("contac_label_msg_contract"); ?></label>
                        <textarea class="form-control" name="contract_msg" cols="5" rows="5"></textarea>
                    </div>
                </div>

                <hr />
                <div class="form-group">
                    <label><?= _l("contac_sales_knowledge"); ?></label>
                    <div style="position: relative;">
                        <textarea class="form-control" name="sales_knowledge" id="sales_knowledge" cols="10" rows="8" placeholder="<?= _l("contac_sales_knowledge_placeholder"); ?>"></textarea>
                        <button type="button" class="btn btn-info" id="fill_sales_knowledge_ai" onclick="fillSalesKnowledgeWithAI()" style="position: absolute; top: 5px; right: 5px; z-index: 10;">
                            <i class="fa fa-magic"></i> <?= _l("contac_fill_with_ai"); ?>
                        </button>
                    </div>
                    <small class="form-text text-muted"><?= _l("contac_sales_knowledge_help"); ?></small>
                    <div id="fill_ai_loading" class="text-info" style="display: none; margin-top: 5px;">
                        <i class="fa fa-spinner fa-spin"></i> <?= _l("contac_extracting_information"); ?>...
                    </div>
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

<!-- Modal -->
<div class="modal fade" id="QrCode" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title " id="exampleModalLongTitle"><?= _l("contac_get_qrcode"); ?></h4>
            </div>
            <div class="modal-body">
                <div class="contact-box-load-code">
                    <div class="loaderQrcode" id="loader-5">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <h3 id="message">Buscando Qrcode</h3>
                </div>
                <div class="contact-box-qrcode">
                    <div>
                        <img id="contact-qrcode" src="" />
                    </div>
                    <h3 id="contact-status"></h3>
                </div>

            </div>
        </div>
    </div>
</div>




<?php init_tail(); ?>

<script>
    $(document).ready(function() {
        initDataTableInline("#contact_device");

        var vRules = {};
        appValidateForm($("#form_add_device"), vRules);

        //Limpa os formularios das modal
        $('.modal').on('hidden.bs.modal', function(e) {
            // Reseta o formulário quando fecha modal 
            $(this).find('form')[0].reset();
            $("#chatbot_id").css("display", "none");
            $(this).find("select").val("").selectpicker("refresh");
            $("#contract").addClass("hidden");
            $("#status_contract").attr("checked", false);
        });
        $('.modal').on('show.bs.modal', function(e) {
            $(".input-group-addon a").css("display", "block");
        });


        /**
         * esconde input dev_instance_name se o api_type for wpp
         */

        /*$("#dev_api_type").on("change", function() {    
            if ($(this).val() == "wpp") {
                $("#dev_instance_name").css("display", "none");
                $("input[name='dev_instance_name']").attr("required", false);
            } else {
                $("#dev_instance_name").css("display", "block");
                $("input[name='dev_instance_name']").attr("required", true);
            }
        });

        $("#dev_instance_name").css("display", "none");*/



        $("#dev_openai").on("change", function() {
            if ($(this).val() == 2) {
                $("#chatbot_id").css("display", "block");
                $("select[name='chatbot_id']").attr("required", true);
            } else {
                $("#chatbot_id").css("display", "none");
                $("select[name='chatbot_id']").attr("required", false);
            }
        });
        $("#chatbot_id").css("display", "none");
    });




    function redirectToAssistantPage() {
        window.location.href = '<?= admin_url('contactcenter/assistant_ai') ?>';
    }

    /**
     * Fill Sales Knowledge with AI from Assistant Instructions
     */
    function fillSalesKnowledgeWithAI() {
        const assistantId = $("select[name='assistant_ai_id']").val();
        const salesKnowledgeField = $("#sales_knowledge");
        const fillButton = $("#fill_sales_knowledge_ai");
        const loadingDiv = $("#fill_ai_loading");

        if (!assistantId) {
            alert_float('warning', '<?= _l("contac_select_assistant_first"); ?>');
            return;
        }

        // Show loading state
        fillButton.prop('disabled', true);
        loadingDiv.show();

        $.ajax({
            url: '<?= admin_url('contactcenter/ajax_axiom_fill_sales_knowledge'); ?>',
            method: 'POST',
            data: { assistant_id: assistantId },
            dataType: 'json',
            success: function(response) {
                loadingDiv.hide();
                fillButton.prop('disabled', false);

                if (response.success && response.sales_knowledge) {
                    salesKnowledgeField.val(response.sales_knowledge);
                    alert_float('success', '<?= _l("contac_sales_knowledge_filled"); ?>');
                } else {
                    alert_float('danger', response.message || '<?= _l("contac_error_extracting_knowledge"); ?>');
                }
            },
            error: function(xhr, status, error) {
                loadingDiv.hide();
                fillButton.prop('disabled', false);
                console.error('Fill Sales Knowledge Error:', error);
                alert_float('danger', '<?= _l("contac_error_extracting_knowledge"); ?>: ' + error);
            }
        });
    }



    function gerar_qrcode(id, token) {
        $.ajax({
            url: url_contactcenter + 'get_qrcode',
            data: {
                id: id
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                get_qrcode(token);
                $("#QrCode").modal("show");
                $(".loaderQrcode").fadeIn();
                $(".contact-box-qrcode").css("display", "none");
                $("#message").html("Aguarde pode demorar até 60 segundos!");
            },
            success: function(data) {
                if (data.qrcode) {
                    $(".contact-box-qrcode").css("display", "flex");
                    $(".loaderQrcode").fadeOut();
                    $("#contact-qrcode").attr("src", data.qrcode);
                    $("#message").html(data.message);
                    $("#contact-status").html(data.pairingCode);
                } else if (data.message == null) {
                    $("#message").html("Aguarde pode demorar até 60 segundos!");
                }


            }
        });
    }

    function get_qrcode(token) {

        <?php
        $pusher_options = hooks()->apply_filters('pusher_options', [['disableStats' => true]]);
        if (!isset($pusher_options['cluster']) && get_option('pusher_cluster') != '') {
            $pusher_options['cluster'] = get_option('pusher_cluster');
        }
        ?>
        var url = token;
        var pusher_options = <?php echo json_encode($pusher_options); ?>;
        var pusher = new Pusher("<?php echo get_option('pusher_app_key'); ?>", pusher_options);
        var channel = pusher.subscribe(url);
        var receivedChunks = [];
        channel.bind('qrcode', function(data) {
            receivedChunks.push(data.message);
            if (data.is_last) {
                // Se este for o último chunk, junte todos os chunks recebidos
                var completeData = receivedChunks.join('');

                $(".contact-box-qrcode").css("display", "flex");
                $(".loaderQrcode").fadeOut();
                $("#contact-qrcode").attr("src", completeData);
                $("#message").html("Qrcode gerado com sucesso!");

                receivedChunks = []; // Limpa o array de chunks recebidos para a próxima mensagem
            }

        });



    }


    function reload_device(id) {
        //desabilita o botao
        $("#reload_device_" + id).prop("disabled", true);
        //adiciona classe de rotação no ícone
        $("#reload_device_" + id + " i").addClass("fa-spin");
        $.ajax({
            url: url_contactcenter + 'reload_device',
            data: {
                id: id
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                //remove a classe de rotação            

                if (data.success) {
                    $("#reload_device_" + id + " i").removeClass("fa-spin");
                    
                    // Inicia contagem regressiva
                    var countdown = 15;
                    var originalHtml = $("#reload_device_" + id).html();
                    
                    var timer = setInterval(function() {
                        $("#reload_device_" + id).html('<i class="fa fa-clock-o"></i> ' + countdown + 's');
                        countdown--;
                        
                        if (countdown < 0) {
                            clearInterval(timer);
                            location.reload();
                        }
                    }, 1000);
                    
                    alert_float('success', data.message + ' - Aguarde 15 segundos');
                } else {
                    $("#reload_device_" + id + " i").removeClass("fa-spin");
                    $("#reload_device_" + id).prop("disabled", false);
                    alert_float('danger', data.message);
                }
            },
            error: function() {
                //remove a classe de rotação em caso de erro
                $("#reload_device_" + id + " i").removeClass("fa-spin");
                $("#reload_device_" + id).prop("disabled", false);
                alert_float('danger', 'Erro ao recarregar o dispositivo');
            }
        });
    }

    function toggle_device_active(device_id, is_active) {
        $.ajax({
            url: url_contactcenter + 'toggle_device_active',
            data: {
                device_id: device_id,
                is_active: is_active ? 1 : 0
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    alert_float('success', data.message);
                } else {
                    // Revert checkbox if failed
                    $('#device_active_' + device_id).prop('checked', !is_active);
                    alert_float('danger', data.message || 'Erro ao alterar status do dispositivo');
                }
            },
            error: function() {
                // Revert checkbox on error
                $('#device_active_' + device_id).prop('checked', !is_active);
                alert_float('danger', 'Erro ao alterar status do dispositivo');
            }
        });
    }
</script>