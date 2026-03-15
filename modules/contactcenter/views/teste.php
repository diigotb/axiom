<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">

          <pre id="saida"></pre>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>

<?php
// Exemplo de envio via HTTP para o servidor Node.js WebSocket

$this->load->model('notification_websocket_model');

$data = [
  'data' => '2023-10-012222',  
  'to' => ['5517991198467'],
  // 'type' => 'media',
  // 'body' => '', 
  // 'url' => 'https://letsenhance.io/static/73136da51c245e80edc6ccfe44888a99/1015f/MainBefore.jpg',
  'apikey' => '9184f0a7-6360-33d9-3f28-e488b6a5df68',
];

$result = $this->notification_websocket_model->send("campanha:verifyNumber", $data);


echo $result;
?>




<script>



  socketAxiom.onmessage = (event) => {
    console.log(event.data);
    const recebido = JSON.parse(event.data);

    const saida = document.getElementById("saida");
    saida.textContent += "\n📥 " + JSON.stringify(recebido, null, 2);

    // Faz o scroll automático para o final
    saida.scrollTop = saida.scrollHeight;
  };
</script>