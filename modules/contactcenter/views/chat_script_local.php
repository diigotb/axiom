<script>
    socketAxiom.onmessage = (event) => {
        //console.log(event.data);
        const recebido = JSON.parse(event.data);
        if (recebido.token == "<?= $device->dev_token ?>") {
            switch (recebido.event) {
                case "whatsapp:verifyNumber":

                    if( recebido.status == "registered") {
                        $(".fa-ban").css("display", "none");
                        $(".fa-whatsapp").fadeIn();
                    } else {
                        $(".fa-whatsapp").css("display", "none");
                        $(".fa-ban").fadeIn();
                    }
                   
                    break;
                default:
                    console.log("Evento desconhecido: ", recebido.event);
            }
        }


    };
</script>