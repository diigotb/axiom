<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="context-menu" class="context-menu list-group-flush">
    <ul>
        <li class="list-group-item disabled">Transferência automática</li>
        <li class="list-group-item">
            Trasnsferir para:
            <ul class="dropdown-menu">
                <?php foreach ($members as $member) { ?>
                    <li class="list-group-item" onclick="transfer_lead('<?= $member['staffid'] ?>',)"> <?php echo $member['firstname'] . ' ' . $member['lastname'];  ?></li>
                <?php  } ?>
            </ul>
        </li>
    </ul>
</div>


<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">

                        <article class="box-chat">
                            <!-- <div class="footer-bar">
                                <ul>
                                    <li><i class="fa-solid fa-circle-plus" data-toggle='tooltip' data-title='<?= _l("contac_chat_new"); ?>' onclick="new_chat()"></i></li>
                                    <li><i class="fa-solid fa-user-group j_close_new" data-toggle='tooltip' data-title='<?= _l("contac_chat_group"); ?>' onclick="get_all_group('<?= $devicetoken ?>');"></i></li>
                                    <li><i class="fa-regular fa-comments j_close_new" data-toggle='tooltip' data-title='<?= _l("contac_chat_chat"); ?>' onclick="get_contact()"></i></li>
                                    <li><i id="onAi" class="fa-solid fa-robot <?= ($device->dev_openai ? "active-ai" : "off-ai") ?>" data-toggle='tooltip' data-title='<?= _l("contac_chat_ai"); ?>' onclick="open_ai()"></i></li>
                                    <li><a href="<?= admin_url("contactcenter/qrcode_single/{$device->dev_id}"); ?>"><i class="fa-solid fa-qrcode"></i></a></li>
                                </ul> 
                            </div> -->
                            <section class="chat-aside">
                                <!-- <div class="chat-perfil">
                                    <div>
                                        <img src="<?= staff_profile_image_url($device->staffid) ?>">
                                    </div>
                                    <div>
                                        <span class="chat-perfil-span"><?= ($device->staffid ? get_staff_full_name($device->staffid) : $device->dev_name) ?></span>
                                    </div>
                                    <span class="chat-perfil-span"><?= $device->dev_number ?></span>
                                    <h6 class=""><?= label_status_device($device->status); ?></h6>
                                    <div class="new-chatall">
                                        <i class="fa-solid fa-mobile-screen"></i>
                                        <input type="text" name='phonenumber' autocomplete="off" value='' />
                                        <i class="fa-brands fa-whatsapp w-none"></i>
                                        <i class="fa-solid fa-ban w-none"></i>
                                    </div>

                                </div> -->

                                <section>

                                    <input type="hidden" name='token' value='<?= $devicetoken ?>' />
                                    <input type="hidden" name='group_type' value='' />
                                    <table id='contaChat' class='display' style='width:100%'>
                                        <thead style="display: none">
                                            <tr>
                                                <th>Name</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            echo $contact_transfer;
                                            echo $contact_accepted;
                                            ?>
                                        </tbody>
                                    </table>

                                    <div class="load-get-msg w-none">
                                        <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>
                                        Loading...
                                    </div>
                                </section>
                            </section>

                            <section class="chat-body">
                                <div class="header-chat">
                                    <div>
                                        <div class="j_on_ai" data-id="">
                                            <lord-icon src="https://cdn.lordicon.com/kcegqely.json" trigger="loop" colors="primary:#ffffff,secondary:#00e09b" style="width:50px;height:50px">
                                            </lord-icon>
                                        </div>
                                        <div class="j_off_ai" data-id="">
                                            <lord-icon src="https://cdn.lordicon.com/kcegqely.json" trigger="loop" colors="primary:#ffffff,secondary:#e83a30" style="width:50px;height:50px">
                                            </lord-icon>
                                        </div>
                                    </div>
                                    <div class="box-dados-chat">
                                        <h6 id="lead-name"></h6>
                                        <h6 id="lead-phone"></h6>
                                    </div>

                                </div>
                                <div id="load" class="load-chat" style="display: none">
                                    <div class="contact-box-load-code">
                                        <div class="loaderQrcode" id="loader-5">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                        <h3 id="message"><?= _l("contac_whats_get_msg"); ?></h3>
                                    </div>
                                </div>
                                <section id='retorno'>


                                </section>
                                <div class="chat-text-area">
                                    <?php echo form_open_multipart("", ["name" => "sendMsg"]) ?>
                                    <input type="hidden" id="staffid_transfer" name="staffid" value="<?= $device->staffid ?>" />
                                    <textarea name='msg' rows="1" placeholder="Envie uma mensagem!"></textarea>
                                    <button id="btn_submit" disabled><i class="fa-solid fa-caret-right"></i></button>
                                    <?php echo form_close() ?>
                                </div>
                            </section>
                            <!--
                            <div>
                                <div id="status"></div>
                                <button id="startRecording"><i class="fa-solid fa-microphone-lines"></i></button>
                                <button id="stopRecording" style="display: none;"><i class="fa-solid fa-microphone-lines-slash"></i></button>
                            </div>  -->

                        </article>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTrans" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title " id="exampleModalLongTitle"><?= _l("contact_trans_modal"); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart(admin_url('contactcenter/transferir_leads')); ?>
                <input type="hidden" name="staffid_from" value="<?= $device->staffid ?>" />
                <input type="hidden" name="dev_id" value="<?= $device->dev_id ?>" />
                <input type="hidden" name="lead_id" value="" />
                <input type="hidden" name="dev_token" value="" />
                <input type="hidden" name="trans_id" value="" />
                <input type="hidden" id="phonenumber_trans" name="phonenumber" value="" />
                <input type="hidden" name="staffid_to" value="" />
                <div class="form-group">
                    <?php echo render_textarea("trans_desc", "Observação") ?>
                </div>
                <div class="form-group">
                    <label><?= _l("contact_trans_prioridade"); ?></label>
                    <select name="trans_status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required>
                        <?php foreach (contactcenter_status_transferir() as $status_id => $status_label) { ?>
                            <option value="<?= $status_id; ?>"><?= $status_label["label"]; ?></option>
                        <?php } ?>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Transferir</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<?php init_tail(); ?>

<script type="text/javascript">
    var msg_id;
    var channel_contact;

    // Enable pusher logging - don't include this in production
    // Pusher.logToConsole = true;
    <?php
    $pusher_options = hooks()->apply_filters('pusher_options', [['disableStats' => true]]);
    if (!isset($pusher_options['cluster']) && get_option('pusher_cluster') != '') {
        $pusher_options['cluster'] = get_option('pusher_cluster');
    }
    $device_name = "{$_SERVER["SERVER_NAME"]}_{$devicetoken}";
    $serve_name = "{$_SERVER["SERVER_NAME"]}_";
    ?>
    var serve_name = "<?= $serve_name; ?>";
    var url = "<?= $device_name; ?>"
    var pusher_options = <?php echo json_encode($pusher_options); ?>;


    function pursher_chat(pusher_options, token_device) {
        url = serve_name + token_device
        var pusher = new Pusher("<?php echo get_option('pusher_app_key'); ?>", pusher_options);
        var channel = pusher.subscribe(url);
        channel.bind('chat', function(data) {
            // console.log(data);

            var to = data.msg_to;
            var from = data.msg_from;
            var isGroupMsg = data.msg_isGroupMsg;

            var tokenUser = $("input[name='token']").val();
            var phoneNumber = $("input[name='phonenumber']").val();
            var group_type = $("input[name='group_type']").val();
            //verifica se os eventos so do usuario online 
            if (phoneNumber == from || phoneNumber == to && group_type == isGroupMsg) {
                $('#retorno').append(data.chat);
                var retornoDiv = document.getElementById('retorno');
                retornoDiv.scrollTop = retornoDiv.scrollHeight;
            }
        });
    }




    var pusher = new Pusher("<?php echo get_option('pusher_app_key'); ?>", pusher_options);
    channel = pusher.subscribe(url);
    channel.bind('contact', function(data) {

        var table = $('#contaChat').DataTable();
        if (msg_id !== undefined && msg_id !== null) {
            if (msg_id != data.contact_id) {
                $("#" + data.contact_id).fadeOut();
                var newRow = table.row.add([
                    data.contact,
                ]).draw(false).node();

            }
        } else {
            $("#" + data.contact_id).fadeOut();
            var newRow = table.row.add([
                data.contact,
            ]).draw(false).node();
        }
        msg_id = data.contact_id;
        $('#contaChat').DataTable().order([0, 'asc']).draw();


    });




    $(document).ready(function() {
        //Monta a tabela de contato
        appDataTableInline("#contaChat", {
            supportsButtons: false,
            supportsLoading: true,
            autoWidth: true,
            paging: false,
            scrollCollapse: true,
            scrollY: '400%',
            info: false,
            /*
            order: [
                [0, 'desc']
            ],*/

        });



        document.addEventListener('keydown', function(evento) {
            if (evento.key === 'Enter' && !evento.shiftKey) {
                evento.preventDefault(); // Evita a quebra de linha padrão ao pressionar Enter
                $("#btn_submit").click();
            } else if (evento.key === 'Enter' && evento.shiftKey) {
                // Mantenha a quebra de linha padrão quando Shift + Enter for pressionado
            }
        });

        //fecha o imput new chat
        $(".j_close_new").click(function() {
            $(".new-chatall").fadeOut();
        });
    });


    /**
     * busca as message via ajax
     * @param {type} id
     * @param {type} token
     * @returns {undefined}
     */
    function get_message_contact(id, token, group, staffid_transfer = null) {
        $("input[name='phonenumber']").val(id);
        $("input[name='group_type']").val(0);


        if (staffid_transfer != null) {
            $("#staffid_transfer").val(staffid_transfer);
        } else {
            $("#staffid_transfer").val(<?= $device->staffid ?>);
        }

        /**
         * renova o pusher para o chat
         */
        pursher_chat(pusher_options, token);


        $.ajax({
            url: site_url + 'admin/contactcenter/ajax_get_messages_chat',
            data: {
                token: token,
                phonenunber: id,
                group: group
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $('#retorno').html("");
                $("#load").fadeIn();
            },
            success: function(data) {
                if (data.retorno) {
                    $("#load").css("display", "none");
                    $('#retorno').html(data.retorno);
                    // Rola a div para baixo
                    var retornoDiv = document.getElementById('retorno');
                    retornoDiv.scrollTop = retornoDiv.scrollHeight;
                }
                if (data.phonenumber) {
                    $('.header-chat').css("display", "flex");
                    $('#lead-name').html(data.name);
                    $('#lead-phone').html(data.phonenumber);
                    $(".j_on_ai").attr("data-id", data.id);
                    $(".j_off_ai").attr("data-id", data.id);
                } else {
                    $('.header-chat').css("display", "none");
                }
                if (data.gpt_status) {
                    if (data.gpt_status == 1) {
                        $(".j_on_ai").css("display", "none");
                        $(".j_off_ai").css("display", "block");
                    } else {
                        $(".j_off_ai").css("display", "none");
                        $(".j_on_ai").css("display", "block");
                    }
                } else {
                    $(".j_off_ai").css("display", "none");
                    $(".j_on_ai").css("display", "none");
                    $('#lead-name').html("");
                    $('#lead-phone').html("");
                }


            }
        });
    }

    /**
     * atualiza o contato quando tem evento 
     * @returns {undefined}
     */
    function get_contact() {
        var tokenUser = $("input[name='token']").val();
        $.ajax({
            url: site_url + 'admin/contactcenter/ajax_get_contact_chat',
            data: {
                token: tokenUser
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(".load-get-msg").fadeIn();
                if ($.fn.DataTable.isDataTable('#contaChat')) {
                    $('#contaChat').DataTable().destroy();
                    $('#contaChat tbody').html("")
                }
            },
            success: function(data) {
                if (data.retorno) {
                    if ($.fn.DataTable.isDataTable('#contaChat')) {
                        $('#contaChat').DataTable().destroy();
                    }
                    // Atualiza o conteúdo da tabela
                    $('#contaChat tbody').append(data.contact_transfer);
                    $('#contaChat tbody').append(data.retorno);
                    // Recria a DataTable com os dados atualizados
                    appDataTableInline("#contaChat", {
                        supportsButtons: false,
                        supportsLoading: true,
                        autoWidth: true,
                        paging: false,
                        scrollCollapse: true,
                        scrollY: '600px',
                        info: false,
                        // order: [
                        //     [0, 'desc']
                        // ],
                    });
                } else {
                    $('#contaChat tbody').html("<tr><td>Not Result</td></tr>");
                }
                $(".load-get-msg").fadeOut();
            }

        });

    }

    function get_all_group(token) {
        var tokenUser = $("input[name='token']").val();
        $("input[name='group_type']").val(1);
        $.ajax({
            url: site_url + 'admin/contactcenter/ajax_get_all_group',
            data: {
                token: tokenUser
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(".load-get-msg").fadeIn();
                if ($.fn.DataTable.isDataTable('#contaChat')) {
                    $('#contaChat').DataTable().destroy();
                    $('#contaChat tbody').html("")
                }
            },
            success: function(data) {
                if (data.retorno) {
                    if ($.fn.DataTable.isDataTable('#contaChat')) {
                        $('#contaChat').DataTable().destroy();
                    }
                    // Atualiza o conteúdo da tabela
                    $('#contaChat tbody').html(data.retorno);
                    // Recria a DataTable com os dados atualizados
                    appDataTableInline("#contaChat", {
                        supportsButtons: false,
                        supportsLoading: true,
                        autoWidth: true,
                        paging: false,
                        scrollCollapse: true,
                        scrollY: '600px',
                        info: false,
                        order: [
                            [0, 'desc']
                        ],
                    });
                } else {
                    $('#contaChat tbody').html("<tr><td>Not Result</td></tr>");
                }
                $(".load-get-msg").fadeOut();
            }

        });
    }

    function new_chat() {
        var newChat = $(".new-chatall").css("display");
        if (newChat == "none") {
            $(".new-chatall").fadeIn();
            $("input[name='phonenumber']").val("");
            $('#retorno').html("");
        } else {
            $(".new-chatall").fadeOut();
            $("input[name='phonenumber']").val("");
            $('#retorno').html("");
        }

    }

    $(".footer-bar ul li i").click(function() {
        var classe = $(this).hasClass("active-icon");
        if (classe) {
            $(this).removeClass("active-icon");
        } else {
            $(".footer-bar").find(".active-icon").removeClass("active-icon");
            $(this).addClass("active-icon");
        }
    });

    /**
     * Clique direito do mouse
     */
    var dataToken = null;
    var dataid = null;
    var data_lead_id = null;
    var data_trans_id = null;
    document.addEventListener('contextmenu', function(e) {
        var contaChat = document.getElementById('contaChat');

        if (contaChat.contains(e.target)) {
            e.preventDefault();

            var contextMenu = document.getElementById('context-menu');

            // Get the click coordinates
            var clickX = e.clientX;
            var clickY = e.clientY;

            // Set the menu position
            contextMenu.style.top = clickY + 'px';
            contextMenu.style.left = clickX + 'px';

            // Show the menu
            contextMenu.style.display = 'block';


            var trElement = e.target.closest('tr');
            var divElement = e.target.closest('div');

            if (trElement) {
                dataToken = trElement.getAttribute('data-token');
                dataid = trElement.getAttribute('data-id');
                data_lead_id = trElement.getAttribute('data-lead-id');
                data_trans_id = trElement.getAttribute('data-transid');
            } else if (divElement) {
                dataToken = divElement.getAttribute('data-token');
                dataid = divElement.getAttribute('data-id');
                data_lead_id = divElement.getAttribute('data-lead-id');
                data_trans_id = divElement.getAttribute('data-transid');
            }




        } else {
            var contextMenu = document.getElementById('context-menu');
            contextMenu.style.display = 'none';
        }

        console.log(dataToken);
        console.log(dataid);


    });

    function transfer_lead(staffid_to) {
        $("input[name='dev_token']").val(dataToken);
        $("input[name='staffid_to']").val(staffid_to);
        $("input[name='lead_id']").val(data_lead_id);
        $("input[name='trans_id']").val(data_trans_id);
        $("#phonenumber_trans").val(dataid);
        $("#modalTrans").modal("show");

    }
    document.addEventListener('click', function(e) {
        // Hide the menu if clicked outside of it
        var contextMenu = document.getElementById('context-menu');
        if (contextMenu.style.display === 'block') {
            contextMenu.style.display = 'none';
        }
    });


    /**
     * ajax para dar accept na msg
     */
    $(".j_accept").click(function() {

        var click = $(this);
        var dataToken = $(this).attr("data-token");
        var dataPhonenumber = $(this).attr("data-id");
        var dataid = $(this).attr("data-transid");
        var datafrom = $(this).attr("data-from");
        var leadid = $(this).attr("data-lead-id");

        var accepetd = confirm("Aceitar o contato?");
        if (accepetd) {
            $.ajax({
                url: site_url + 'admin/contactcenter/ajax_accept_contact',
                data: {
                    trans_id: dataid,
                    leadid: leadid,
                    datafrom: datafrom,
                    phonenumber:dataPhonenumber
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if (data.result) {
                        click.off("click");
                        get_message_contact(dataPhonenumber, dataToken, 0, datafrom);                        
                        click.click(function() {
                            get_message_contact(dataPhonenumber, dataToken, 0, datafrom);
                        });
                        click.removeClass("j_accept");
                    }
                }
            });
        }
    });
</script>