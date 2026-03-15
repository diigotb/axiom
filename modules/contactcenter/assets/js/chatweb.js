$(document).ready(function () {
    // Registra o evento de load no iframe
    $("#iframe-chatweb").on("load", function () {
      console.log("Iframe loaded");
      var iframe = $(this).contents();
  
      // Registra o evento de clique na classe .close_chat dentro do iframe
      iframe.find(".close_chat").on("click", function () {
        console.log("Close chat clicked");
        $("#iframe-chatweb").css({
            width: "100px",
            height: "100px",
          });
      
      });
    });
  
    // Opcional: Código para manipular hover do iframe
    $("#iframe-chatweb").hover(
      function () {
        $(this).css({
          width: "340px",
          height: "540px",
        });
      },
      function () {
        
      }
    );
  });
  