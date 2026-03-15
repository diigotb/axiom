<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s full-mobile">
                    <div class="panel-body">
                        <div class="chat-container">
                            <div class="chat-container-header">

                                <div class="footer-bar">
                                    <div class="input-filter-chat">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                            <input id="shearch" type="text" class="form-control" placeholder="<?= _l("contac_chat_search"); ?>">
                                        </div>
                                        <div class="input-group col-md-6">
                                            <select class="form-control selectpicker" id="statuLead" data-none-selected-text="<?= _l("dropdown_non_selected_tex"); ?>" data-live-search="true" id="">
                                                <option></option>
                                                <?php foreach ($statuses as $status) { ?>
                                                    <option value="<?= $status['id']; ?>"><?= $status['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="input-group filter-contact">
                                            <div class="btn btn-primary btn-filter-chat" onclick="on_filter()">
                                                <i class="fa-solid fa-filter"></i>
                                            </div>
                                            <div class="btn btn-primary btn-filter-chat">
                                                <a href="<?= admin_url("contactcenter/messenger"); ?>"><i class="fa-solid fa-arrow-left"></i></a>
                                            </div>
                                        </div>
                                    </div>

                                    <ul>
                                        <?php if (is_admin()) { ?>
                                            <li id="onAi" class="btn btn-primary <?= (get_option('active_ia_messenger_Facebook') ? "active-ai" : "off-ai") ?>" data-toggle='tooltip' data-title='<?= _l("contac_chat_ai"); ?>' onclick="open_ai_messenger()"><i class="fa-solid fa-robot"></i></li>
                                        <?php } ?>
                                        <li class="btn btn-primary" data-toggle='tooltip' data-title='<?= _l("contac_chat_chat"); ?>' onclick="get_contact()"><i class="fa-solid fa-rotate j_close_new"></i></li>
                                        <li class="btn btn-primary" data-toggle='tooltip' data-title='<?= _l("contac_chat_desable_sound"); ?>' onclick="desable_sound(this)"><i class="fa-solid fa-volume-high"></i></li>
                                    </ul>
                                </div>
                                <?php if (is_mobile() == false) { ?>
                                    <div class="header-chat-content">
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

                                            <div class="progress-container" id="progress"> </div>

                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <article class="box-chat">

                            <section class="chat-aside">
                                <div class="chat-perfil">
                                    <div>
                                        <span class="chat-perfil-span"><?= $pages[0]['name'] ?></span>
                                        <span class="chat-perfil-span">
                                            <i class="fa-brands fa-facebook-messenger"></i>
                                            <?php if ($pages[0]['instagramId']) { ?>
                                                <i class="fa-brands fa-instagram"></i>
                                            <?php } ?>
                                        </span>
                                    </div>
                                </div>

                                <section>
                                    <div class="row" id="search_chat"></div>
                                    <table id='contaChat'>
                                        <thead style="display: none">
                                            <tr>
                                                <th>Name</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            echo monta_html_contact_messenger($LabelContacts);
                                            ?>
                                        </tbody>
                                    </table>

                                    <div class="load-get-msg w-none">
                                        <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>
                                        Loading...
                                    </div>
                                </section>
                            </section>

                            <section class="chat-body chat-body-messenger">
                                <?php if (is_mobile() == true) { ?>
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

                                        <div class="progress-container" id="progress"> </div>

                                    </div>
                                    <div class="btn-back-chat btn-filter-chat">
                                        <i class="fa-solid fa-arrow-left"></i>
                                    </div>
                                <?php } ?>

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
                                <div class="spinner-load-chat">
                                    <i class="fa-solid fa-spinner load"></i>
                                </div>
                                <section id='retorno'>
                                </section>
                                <div class="chat-text-area">
                                    <?php echo form_open_multipart("", ["name" => "sendMsgMessenger"]) ?>
                                    <input type="hidden" id="page_id" name="page_id" value="<?= $pages[0]['id'] ?>" />
                                    <input type="hidden" id="sender_id" name="sender_id" value="" />
                                    <input type="hidden" name="staffid" value="<?= get_staff_user_id() ?>" />
                                    <input type="hidden" name="action" value="text" />

                                    <div class="dropup">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa-solid fa-paperclip"></i>
                                        </button>
                                        <ul class="dropdown-menu attachment_chat">
                                            <li onclick="upload_media_chat()"><i class="fa-regular fa-image"></i> <span><?= _l("contac_chat_img"); ?></span></li>
                                        </ul>
                                    </div>

                                    <textarea id="textarea-chat" name='msg' rows="1" placeholder="<?= _l("chat_input_placeholder") ?>"></textarea>
                                    <div class="btn-submit-chat">
                                        <button id="btn_submit" disabled><i class="fa-regular fa-paper-plane spinner-load-send"></i><i class="fa-solid fa-spinner spinner-load-in"></i></button>
                                        <button id="startRecordingMessenger"><i class="fa-solid fa-microphone-lines"></i></button>
                                        <button id="stopRecording" style="display: none;"><i class="fa-solid fa-microphone-lines-slash"></i></button>
                                    </div>
                                    <?php echo form_close() ?>
                                </div>

                            </section>
                            <section class="chat-media">
                                <div>
                                    <div class="header-chat-media">
                                        <span class="close-chat-media"><i class="fa-solid fa-xmark"></i></span>
                                    </div>
                                    <div class="preview-chat-media">
                                        <img id="veiwMedia">
                                        <video id="veiwMediaVideo" controls></video>                                   
                                    </div>
                                    <div id="veiwFileName"></div>
                                    <?php echo form_open_multipart("", ["name" => "sendMediaMessenger"]) ?>                                   
                                    <input type="hidden" name="action" value="image" />
                                    <input type="hidden" id="page_id" name="page_id" value="<?= $pages[0]['id'] ?>" />
                                    <input type="hidden" id="sender_id" name="sender_id" value="" />
                                    <input type="hidden" id="staffid" name="staffid" value="<?= get_staff_user_id() ?>" />                                    
                                    <input style="display: none;" type="file" name="file" id="inputFile_chat">
                                    <textarea name='msg' rows="1" placeholder="<?= _l("chat_input_placeholder") ?>"></textarea>
                                    <div class="btn-submit-chat">
                                        <button class="btn btn-default" type="submit"><i class="fa-regular fa-paper-plane"></i></button>
                                    </div>
                                    <?php echo form_close() ?>
                                </div>
                            </section>


                        </article>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>





<script type="text/javascript">
    $(document).ready(function() {

        $("#shearch").on("keyup", function() {
            var value = $(this).val().toLowerCase(); // Obtém o valor do input e converte para minúsculas
            $("#contaChat tr").filter(function() {
                // Mostra ou oculta as TRs com base se o texto delas contém o termo de pesquisa
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });


        $('#textarea-chat').on('keydown', function(event) {
            if (event.key === "Enter") {
                if (event.shiftKey) {
                    return true;
                } else {
                    event.preventDefault();
                    $("#btn_submit").trigger('click');
                }
            }

            // Inibe todos os atalhos que usam Shift + [outra tecla] quando o editor está ativo
            if (event.shiftKey && event.key !== "Shift") {
                event.stopPropagation();
            }
            $("#btn_submit").prop("disabled", false);
            $("#btn_submit").css("background", "var(--primary)");
        });


        /**
         * Filtro de pesquisa
         */
        $('#shearch').on('keyup', function() {
            var searchTerm = $(this).val();

            var page_id = <?= $pages[0]['id'] ?>;
            //console.log('Texto pesquisado:', searchTerm);

            if (searchTerm.trim() !== '' && searchTerm.length > 2) {

                $.ajax({
                    url: site_url + 'admin/contactcenter/ajax_get_search_chat_messenger',
                    data: {
                        search: searchTerm,
                        page_id: page_id
                    },
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function() {

                    },
                    success: function(data) {
                        if (data.search.chat) {
                            $('#search_chat').html(data.search.chat);
                        }
                        if (data.search.contact) {
                            $('#contaChat tbody').html(data.search.contact);
                        }
                    }
                });

            } else if (searchTerm.trim() === '') {
                $("#search_chat").html("");
                get_contact();
            }

        });

        //fecha o imput new chat
        $(".j_close_new").click(function() {
            $(".new-chatall").fadeOut();
        });
    });



    /**
     * inpunt de pesquisa
     */
    function get_message_contact_search(id, page_id, msg_id) {
        $("input[name='sender_id']").val(id);
        paginadorChat = null;

        //view mobile
        if ($(window).width() < 661) {
            $(".chat-body").css("display", "block");
            $(".chat-body").addClass('full-mobile-chat');
            $(".chat-body").css("z-index", "100");
            $(".chat-body").css("background", "#000");
        }


        $.ajax({
            url: site_url + 'admin/contactcenter/ajax_get_message_meta',
            data: {
                sender_id: id,
                page_id: page_id
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
                    var msgId = 'msg_' + msg_id; // Certifique-se de que data.msg_id contém o ID correto
                    var targetElement = document.getElementById(msgId);

                    if (targetElement) {
                        $('#retorno').animate({
                            scrollTop: $(targetElement).offset().top - $('#retorno').offset().top + $('#retorno').scrollTop()
                        }, 1000);
                    }

                    // Adiciona a classe para piscar a borda após a rolagem
                    $(targetElement).addClass('blink-border');

                    // Remove a classe após 3 segundos (ou o tempo que desejar)
                    setTimeout(function() {
                        $(targetElement).removeClass('blink-border');
                    }, 3000);

                }
                if (data.paginadorChat) {
                    paginadorChat = data.paginadorChat;
                }
                if (data.id) {
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

                if (data.progress) {
                    $("#progress").html(data.progress);
                }


            }
        });
    }

    /**
     * busca as message via ajax
     * @param {type} id
     * @param {type} token
     * @returns {undefined}
     */
    var paginadorChat = null;
    var paginadorToken = null;

    function get_message_messenger(id, page_id) {
        $("input[name='sender_id']").val(id);
        paginadorChat = null;

        //view mobile
        if ($(window).width() < 661) {
            $(".chat-body").css("display", "block");
            $(".chat-body").addClass('full-mobile-chat');
            $(".chat-body").css("z-index", "100");
            $(".chat-body").css("background", "#000");
        }

        $.ajax({
            url: site_url + 'admin/contactcenter/ajax_get_message_meta',
            data: {
                sender_id: id,
                page_id: page_id
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

                    // tira o total de visualizações do chat
                    var row = $('tr[data-id="' + id + '"]');
                    row.find(".badge").text("");
                }
                if (data.paginadorChat) {
                    paginadorChat = data.paginadorChat;
                }
                if (data.id) {
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

                if (data.progress) {
                    $("#progress").html(data.progress);
                }


            }
        });
    }


    /**
     * click na barra de progresso para transferir o contato
     */
    $(document).on('click', '#progress .step', function(event) {
        var page_id = <?= $pages[0]['id'] ?>;
        var sender_id = $(this).attr("data-id");
        if (page_id) {
            get_message_messenger(sender_id, page_id);
        }

    });

    /**
     * Atualiza o contato quando troca o status
     */
    $(document).ready(function() {
        $("#statuLead").change(function() {
            get_contact();
        })
    });

    /**
     * atualiza o contato quando tem evento 
     * @returns {undefined}
     */
    function get_contact() {
        var page_id = <?= $pages[0]['id'] ?>;

        var statuLead = $("#statuLead").val();
        if (statuLead) {
            statuLead = statuLead;
        } else {
            statuLead = null;
        }

        $.ajax({
            url: site_url + 'admin/contactcenter/ajax_get_contact_messenger',
            data: {
                page_id: page_id,
                status: statuLead
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $(".load-get-msg").fadeIn();
                $('#contaChat tbody').html("")
                $("#search_chat").html("");
            },
            success: function(data) {
                if (data.retorno) {
                    $('#contaChat tbody').append(data.retorno);

                } else {
                    $('#contaChat tbody').html("<tr><td>Not Result</td></tr>");
                }
                $(".load-get-msg").fadeOut();
            }

        });

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





    function on_filter() {
        $(".footer-bar ul").slideToggle();
    }

    $(".btn-back-chat").click(function() {
        $(".chat-body").removeClass('full-mobile-chat');
    });


    /**
     * faz a pagina carregar mais dados
     */
    $(document).ready(function() {
        var isLoading = false;

        // Detecta o scroll da seção .chat-aside
        $('.chat-aside section').on('scroll', function() {
            var $this = $(this);

            // Verifica se o usuário chegou ao final da div
            if ($this.scrollTop() + $this.innerHeight() >= $this[0].scrollHeight) {
                if (!isLoading) {
                    isLoading = true;

                    $.ajax({
                        url: site_url + "admin/contactcenter/ajax_get_contact_chat_messenger_paginador",
                        dataType: 'json',
                        method: 'POST',
                        data: {
                            page_id: <?= $pages[0]['id'] ?>
                        },
                        beforeSend: function() {},
                        success: function(data) {
                            if (data.retorno) {
                                $('#contaChat tbody').append(data.retorno);
                            }
                            isLoading = false;
                        }
                    });
                }
            }
        });
    });

    /**
     * paginador chat
     */
    $(document).ready(function() {
        var isLoading = false;

        // Detecta o scroll da seção .chat-body
        $('.chat-body section').on('scroll', function() {
            var $this = $(this);

            // Verifica se o usuário chegou ao topo da div
            if ($this.scrollTop() === 0 && !isLoading) {
                isLoading = true;

                // Armazena a altura da div antes do prepend
                var oldHeight = $this[0].scrollHeight;

                var sender_id = $("input[name='sender_id']").val();
                var page_id = $("input[name='page_id']").val();
                $.ajax({
                    url: site_url + "admin/contactcenter/ajax_get_chat_messenger_paginador",
                    dataType: 'json',
                    method: 'POST',
                    data: {
                        sender_id: sender_id,
                        page_id: page_id,
                        paginadorId: paginadorChat, 
                    },
                    beforeSend: function() {
                        $(".spinner-load-chat").fadeIn();
                    },
                    success: function(data) {
                        $(".spinner-load-chat").fadeOut();
                        if (data.retorno) {
                            // Adiciona o conteúdo antes do existente
                            $('#retorno').prepend(data.retorno);

                            // Atualiza o valor de paginadorChat com o novo paginador recebido do servidor
                            paginadorChat = data.paginadorChat;

                            // Calcula a nova altura da div
                            var newHeight = $this[0].scrollHeight;

                            // Ajusta o scroll para a posição equivalente antes do prepend
                            $this.scrollTop(newHeight - oldHeight);
                        }
                        isLoading = false;
                    },
                    error: function() {
                        isLoading = false;
                    }
                });
            }
        });
    });
</script>