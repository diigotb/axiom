<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">

                        <div class="conteiner-qrcode-single">

                            <div class="box-qrcode-single">
                                <div>
                                    <ul>
                                        <li><?= _l("1_tutorial_qrcode") ?></li>
                                        <li><?= _l("2_tutorial_qrcode") ?></li>
                                        <li><?= _l("3_tutorial_qrcode") ?></li>
                                        <li><?= _l("4_tutorial_qrcode") ?></li>
                                    </ul>

                                    <button class="btn btn-primary" id="restart_device" onclick="restart_device()"><?= _l("restart_device") ?></button>
                                </div>
                                <div>
                                    <div class="contact-box-load-code">
                                        <div class="loaderQrcode" id="loader-5">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                        <h3 id="message"><?= _l("qrcode_search") ?></h3>
                                    </div>
                                    <div class="contact-box-qrcode">
                                        <div>
                                            <img id="contact-qrcode" src="" />
                                        </div>
                                        <h3 id="contact-status"></h3>
                                    </div>
                                    <div class="contact-box-back">
                                        <a  href="<?= admin_url("contactcenter/chatsingle/{$device->dev_id}")  ?>" class="btn btn-success"> <i class="fa-solid fa-circle-arrow-left"></i> Voltar para o Chat</a>
                                    </div>

                                </div>
                            </div>



                        </div>
                        <div class="box-video-tutorial">
                            <div class="box-video-text"><?= _l("tutorial_qrcode_video") ?></div>
                            <div>
                                <video autoplay="" class="" controls="" controlslist="nodownload">
                                    <source src="https://static.whatsapp.net/rsrc.php/yk/r/GoZyw2bTK6s.mp4" type="video/mp4">
                                </video>
                            </div>
                        </div>




                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    get_websocket(<?= $dadoServe ?>);

    function restart_device() {
        var id = <?= $device->dev_id ?>;
        $.ajax({
            url: url_contactcenter + 'restart_device',
            data: {
                id: id
            },
            beforeSend: function() {
                // desativo o botao
                $("#restart_device").attr("disabled", true);
                $("#restart_device").html("Aguarde, reiniciando o dispositivo...");
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                // ativo o botao
                $("#restart_device").attr("disabled", false);
                if (data.result) {
                    // gerar_qrcode(id, data.token);
                    //atualiza a pagina
                    window.location.reload();
                }
            }
        });
    }
    $(document).ready(function() {

        var intervaloStatus;


        function gerar_qrcode(id, token) {
            $.ajax({
                url: url_contactcenter + 'get_qrcode',
                data: {
                    id: id
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $("#QrCode").modal("show");
                    $(".loaderQrcode").fadeIn();
                    $(".contact-box-qrcode").css("display", "none");
                    $("#message").html("<?= _l('qrcode_generator_time') ?>");
                },
                success: function(data) {
                    if (data.qrcode) {
                            $(".contact-box-qrcode").css("display", "flex");
                            $(".loaderQrcode").fadeOut();
                            $("#contact-qrcode").attr("src", data.qrcode);
                            $("#message").html(data.message);
                            $("#contact-status").html(data.pairingCode);
                    } else {
                        $("#message").html(data.message);
                    }
                    setInterval(function() {
                        reload_qrcode();
                    }, 4000);
                }
            });
        }

        // function get_qrcode(token) {

        //     <?php
                //     $pusher_options = hooks()->apply_filters('pusher_options', [['disableStats' => true]]);
                //     if (!isset($pusher_options['cluster']) && get_option('pusher_cluster') != '') {
                //         $pusher_options['cluster'] = get_option('pusher_cluster');
                //     }
                //     
                ?>
        //     var url = token;
        //     var pusher_options = <?php echo json_encode($pusher_options); ?>;
        //     var pusher = new Pusher("<?php echo get_option('pusher_app_key'); ?>", pusher_options);
        //     var channel = pusher.subscribe(url);
        //     var receivedChunks = [];
        //     channel.bind('qrcode', function(data) {
        //         receivedChunks.push(data.message);
        //         if (data.is_last) {
        //             // Se este for o último chunk, junte todos os chunks recebidos
        //             var completeData = receivedChunks.join('');

        //             $(".contact-box-qrcode").css("display", "flex");
        //             $(".loaderQrcode").fadeOut();
        //             $("#contact-qrcode").attr("src", completeData);
        //             $("#message").html("<?= _l('qrcode_generator_time_success') ?>");

        //             receivedChunks = []; // Limpa o array de chunks recebidos para a próxima mensagem

        //         }

        //     });



        // }

        var id = <?= $device->dev_id ?>;
        var token = "<?= $device->dev_token ?>";
        gerar_qrcode(id, token);

        function reload_qrcode() {
            var id = <?= $device->dev_id ?>;
            var message = "<?= _l("contac_conected") ?>";
            $(this).attr("disabled", true);
            $.ajax({
                url: url_contactcenter + 'get_status_connection_device',
                data: {
                    id: id
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $(this).attr("disabled", true);
                },
                success: function(data) {
                    if (data.status) {
                        $("#message").html(message);
                        setTimeout(function() {
                            window.location.href = url_contactcenter + 'chatsingle/' + id;
                        }, 2500);

                    } else {

                    }
                }
            });
        }



    });
</script>