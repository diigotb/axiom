<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://js.pusher.com/5.0/pusher.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');

    * {
        margin: 0;
        padding: 0;
    }

    :root {
        --primary-color: #3683ff;
        --secondary-color: #aee800;
        --third-color: #ccc;
        --background-color: #ffffff;
        --text-color: #000000;
        --background-color-black: #000000;
    }
  

    .content_main {
        position: fixed;
        width: 300px;
        height: 100px;
        display: flex;
        flex-wrap: wrap;
        justify-content: right;
        align-items: flex-end;
        font-family: 'Roboto';
        max-width: 400px;
        right: 0px;
    }

    .content_chat {
        position: fixed;       
        width: 100%;
        height: 100%;
        max-width: 70px;
        max-height: 70px;
        background: url(<?= site_url("modules/contactcenter/assets/image/logo_chat.png") ?>) no-repeat center center / 80%, linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        background-position: center center;
        object-fit: cover;
        border-radius: 13px;
        cursor: pointer;
        transition: .5s;
        overflow: hidden;
        bottom: 0;
        margin: 10px;
    }
    

    .content_chat.animate {
        animation: explode 2s infinite;
    }

    @keyframes explode {
        0% {
            box-shadow: 0 0 0 0 rgba(174, 232, 0, 0.5);
        }

        70% {
            box-shadow: 0 0 0 20px rgba(174, 232, 0, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(174, 232, 0, 0);
        }
    }

    .close_chat {
        position: absolute;
        right: 15px;
        top: 15px;
        width: 30px;
        height: 30px;
        font-size: 30px;
        color: var(--text-color);
        cursor: pointer;
    }

    .axiom_logo {
        position: relative;
        width: 50%;
        background: #ffffff;
        border-bottom-right-radius: 15px;
        padding: 10px;
    }

    .axiom_logo img {
        width: 100%;
        object-fit: cover;
    }

    .axiom_logo::before {
        content: "";
        top: 0;
        right: 0;
        position: absolute;
        display: block;
        background: transparent;
        width: 25px;
        height: 25px;
        border-radius: 100%;
        transform: translateX(100%);
        box-shadow: -0.6rem -0.6rem 0 #ffffff;
        aspect-ratio: 1;
    }

    .axiom_logo::after {
        content: "";
        bottom: 0;
        left: 0;
        position: absolute;
        display: block;
        background: transparent;
        width: 25px;
        height: 25px;
        border-radius: 100%;
        transform: translatey(100%);
        box-shadow: -0.6rem -0.6rem 0 #ffffff;
        aspect-ratio: 1;
    }

    .box_chat {
        display: none;
    }

    .box_chat .chat {
        display: none;
        position: relative;
        background: var(--background-color);
        margin: 15px;
        border-radius: 20px;
        height: 405px;
        overflow-x: hidden;
    }

    .box_chat .text_input {
        position: absolute;
        background: var(--background-color);
        width: 100%;
        bottom: 0;
        padding: 10px 0px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .box_chat .text_input form {
        position: absolute;
        background: var(--background-color);
        width: 100%;
        bottom: 0;
        padding: 10px 0px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .box_chat .text_input button {
        border: none;
        background: var(--secondary-color);
        color: var(--text-color);
        border-radius: 100%;
        font-size: 18px;
        width: 40px;
        height: 40px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        margin-right: 10px;
        cursor: pointer;
    }

    .box_chat .chat textarea {
        width: 70%;
        height: 30px;
        margin: 0 10px;
        background: var(--third-color);
        border: none;
        font-size: 16px;
        padding: 5px;
        border-radius: 10px;
        resize: none;
        font-family: 'Roboto';
    }

    .box_chat .chat textarea::-webkit-scrollbar {
        width: 0;
    }

    .box_chat .chat textarea::-moz-scrollbar {
        width: 0;
    }

    .box_chat .chat article {
        position: relative;
        display: block;
        height: 100%;
        width: 100%;
        padding-bottom: 55px;
        box-sizing: border-box;
        overflow-y: scroll;
    }

    .box_chat .chat article::-webkit-scrollbar {
        width: 0;
    }

    .box_chat .chat article::-moz-scrollbar {
        width: 0;
    }

    .box_chat .chat article .staff {
        display: block;
        position: relative;
        max-width: 210px;
        left: 10px;
        float: left;
        margin: 10px 0;
        padding: 10px;
        border-radius: 10px;
        background: var(--third-color);
        color: var(--text-color);
    }

    .box_chat .chat article .person {
        display: block;
        position: relative;
        max-width: 210px;
        right: 10px;
        float: right;
        margin: 10px 0;
        padding: 10px;
        border-radius: 10px;
        background: var(--secondary-color);
        color: var(--text-color);
    }

    .box_chat .register {
        display: none;
        position: relative;
        background: var(--background-color);
        margin: 15px;
        border-radius: 20px;
        height: 405px;
    }

    .box_chat .register h1 {
        text-align: center;
        width: 100%;
        font-family: 'Roboto';
        color: var(--text-color);
        font-size: 20px;
        position: absolute;
        margin: 10px 0px;
    }

    .box_chat .register form {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        align-items: center;
    }

    .box_chat .register form input {
        width: 220px;
        background: transparent;
        border: none;
        border-bottom: 2px solid var(--third-color);
        padding: 5px;
        font-size: 18px;
        font-family: 'Roboto';
        display: block;
        margin: 15px 0px;
        color: var(--text-color);
    }

    .box_chat .register form button {
        background: linear-gradient(120deg, var(--primary-color), var(--secondary-color));
        border-radius: 10px;
        border: none;
        font-size: 20px;
        color: var(--text-color);
        font-family: 'Roboto';
        font-weight: 500;
        width: 220px;
        padding: 5px;
        margin-top: 30px;
        cursor: pointer;
        transition: .5s;
    }

    .box_chat .register form button:hover {
        transition: .5s;
        color: var(--primary-color);
        background: var(--third-color);
        transform: translateY(-6px);
    }

    @media (max-width: 400px) {

        .box_chat .chat article .person,
        .box_chat .chat article .staff {
            right: initial;
            left: initial;
            margin: 10px;
        }
    }
</style>



<?php
// print_r($lead);
// echo "<br>";
// print_r($chat);
?>
<input type="hidden" name="site_url" value="<?= site_url() ?>">
<article class="content_main">

    <section class="content_chat animate">
        <section class="box_chat">
            <div class="axiom_logo">
                <img src="<?= site_url("modules/contactcenter/assets/image/logo.png")  ?>">
            </div>

            <div class="close_chat">
                <i class="fa-solid fa-xmark"></i>
            </div>

            <section class="register">
                <h1>Por favor, registre-se</h1>
                <?php echo form_open_multipart("", ["id" => "register_form"]); ?>
                <div>
                    <input type="hidden" name="chat_id" value="<?= $chatweb->chat_id ?>">
                    <input type="hidden" name="cache_token" value="<?= $cache_token ?>">
                    <input type="text" name="name" id="" placeholder="Nome" required>
                    <input type="text" name="phonenumber" id="" placeholder="+5517991191234" required>
                    <input type="email" name="email" id="" placeholder="E-mail" required>
                    <button type="subimt">Next</button>
                    <p id="result"></p>
                </div>
                <?php echo form_close(); ?>
            </section>

            <section class="chat">
                <article id="retorno">
                    <?= $hmtl_chat ?>
                </article>

                <div class="text_input">
                    <?php echo form_open_multipart("", ["id" => "send_msg"]); ?>
                    <input type="hidden" name="chat_id" value="<?= $chatweb->chat_id ?>">
                    <input type="hidden" name="leadid" value="<?= $lead["id"] ?>">
                    <input type="hidden" name="thread" value="<?= $lead["gpt_thread"] ?>">
                    <input type="hidden" name="cache_token" value="<?= $cache_token ?>">
                    <input type="hidden" name="phonenumber" value="<?= $lead["phonenumber"] ?>">
                    <input type="hidden" name="chat_assitent" value="<?= $chatweb->chat_assitent ?>">
                    <textarea name="msg" id="" placeholder="Message" required></textarea>
                    <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                    <?php echo form_close(); ?>
                </div>
            </section>
        </section>
    </section>
</article>


<script>
    $(document).ready(function() {
        $('.content_chat').on('click', function() {
            if ($('.content_chat').css('max-width') != '300px') {
                $('.content_chat').css({
                    'max-width': '300px',
                    'max-height': '500px',
                    'background': 'linear-gradient(120deg, var(--primary-color), var(--secondary-color))',
                    'cursor': 'initial',
                    'border': '10px solid var(--background-color)'
                });

                $('.box_chat').show();

                var lead = $("input[name='leadid']").val();
                if (lead != "") {
                    $('.register').remove();
                    $('.chat').show();
                    var retornoDiv = document.getElementById('retorno');
                    retornoDiv.scrollTop = retornoDiv.scrollHeight;
                } else {
                    $('.box_chat .register').show();
                }
                $('.content_chat').toggleClass('animate');


            }
        });

       

    });

    $('.close_chat').on('click', function() {
        $('.content_chat').removeAttr('style');
        $('.box_chat').hide();
        $('.box_chat .register').hide();
        $('.content_chat').toggleClass('animate');
    })

  


    $(function() {


        $("#register_form").submit(function(eventos) {
            eventos.preventDefault();

            var Form = $(this);
            var formData = new FormData(Form[0]);
            var site_url = $("input[name='site_url']").val();
            var Phonenumber = $("input[name='phonenumber']").val();
            var name = Form.find("input[name='name']").val();

            if (name != '' && Phonenumber != '') {

                formData.append('phonenumber', Phonenumber);
                $.ajax({
                    url: site_url + 'contactcenter/chat/start_chat',
                    data: formData,
                    type: "POST",
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    beforeSend: function() {

                    },
                    success: function(data) {
                        if (data.lead) {
                            $('.register').remove();
                            $('.chat').show();

                            //pega o array
                            var lead = data.lead.lead;
                            var chat = data.lead.chat;

                            $("input[name='chat_id']").val(chat.chat_id);
                            $("input[name='leadid']").val(lead.id);
                            $("input[name='thread']").val(lead.gpt_thread);
                            $("input[name='phonenumber']").val(lead.phonenumber);
                            $("input[name='chat_assitent']").val(chat.chat_assitent);

                        }

                    }
                });

            } else {
                $("#result").html("Preencher todos os campos!");
            }
        });

        /**envia msg */
        $("#send_msg").submit(function(eventos) {
            eventos.preventDefault();

            var Form = $(this);
            var formData = new FormData(Form[0]);
            var site_url = $("input[name='site_url']").val();
            var msg = Form.find("textarea[name='msg']").val();

            if (msg != '') {

                $.ajax({
                    url: site_url + 'contactcenter/chat/send_msg',
                    data: formData,
                    type: "POST",
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        Form.trigger("reset");
                    },
                    success: function(data) {
                        if (data.lead) {

                        }
                        if (data.clear) {
                            Form.trigger("reset");
                        }

                    }
                });

            } else {
                $("#result").html("Preencher todos os campos!");
            }
        });


        <?php
        $pusher_options = hooks()->apply_filters('pusher_options', [['disableStats' => true]]);
        if (!isset($pusher_options['cluster']) && get_option('pusher_cluster') != '') {
            $pusher_options['cluster'] = get_option('pusher_cluster');
        }

        $device_name = "{$_SERVER["SERVER_NAME"]}_{$cache_token}";

        ?>
        var url = "<?= $device_name; ?>";
        var pusher_options = <?php echo json_encode($pusher_options); ?>;
        var pusher = new Pusher("<?php echo get_option('pusher_app_key'); ?>", pusher_options);
        var channel = pusher.subscribe(url);
        channel.bind('chatweb', function(data) {
            //console.log(data);
            $('#retorno').append(data);
            var retornoDiv = document.getElementById('retorno');
            retornoDiv.scrollTop = retornoDiv.scrollHeight;

            var site_url = $("input[name='site_url']").val();
            const audioElement = document.createElement('audio');
            // Definir a origem do arquivo de áudio usando a propriedade src
            audioElement.src = site_url + "modules/contactcenter/assets/audio/notification.mp3";

            // Executar a reprodução do som
            audioElement.play();

        });

    
        document.addEventListener('DOMContentLoaded', function() {
            var iframe = document.getElementById('iframe-chatweb');
            iframe.addEventListener('click', function() {
                console.log("ok");
                this.style.width = '330px';
                this.style.height = '580px';
            });
        });


    });
</script>