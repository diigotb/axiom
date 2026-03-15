/*
 * Criado por AXIOM (https://axiom.com.br)
 * Data: 06/06/2022
 */
var url_contactcenter = site_url + "contactcenter/";

$(function () {
  //delete comunidade
  $(".j_delete_community").click(function () {
    var id = $(this).attr("data-id");
    var msg = $(this).attr("data-aviso");
    var confirmacao = confirm(msg);
    if (confirmacao) {
      $.ajax({
        url: url_contactcenter + "settings/delete_community",
        data: { id: id },
        type: "POST",
        dataType: "json",
        success: function (data) {
          if (data.redirect) {
            window.location.href = data.redirect;
          }
        },
      });
    }
  });

  //active o clique no contato
  $("#contaChat").on("click", ".chat-contato", function () {
    // Remove a classe .active-contact de todos os elementos com a classe .chat-contato dentro da tabela #contaChat
    $("#contaChat .chat-contato").removeClass("active-contact");

    // Adiciona a classe .active-contact apenas ao elemento clicado
    $(this).addClass("active-contact");
  });

  /**
   *
   * Desabilita o botão de enviar chat
   */
  function desabilitarBotao() {
    $("#btn_submit").css("background", "#ccc");
    $("#btn_submit").prop("disabled", true);
  }

  // Chamada da função para desabilitar o botão (por exemplo, quando você quiser desabilitá-lo)
  desabilitarBotao();
  //libera o botão de enviar ao digitar

  /**
   * Device Reconnect Functionality
   */
  $(document).on('click', '.device-reconnect-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var $btn = $(this);
    var deviceId = $btn.data('device-id');
    var deviceName = $btn.closest('.device-status-item').find('.device-reconnect-link').data('device-name') || 'Device';
    
    if (!deviceId) {
      alert_float('danger', 'Device ID not found');
      return;
    }
    
    // Disable button and show loading state
    $btn.prop('disabled', true);
    var originalHtml = $btn.html();
    // Use translation helper or fallback
    var reconnectingText = (typeof window.contactcenterDeviceTranslations !== 'undefined' && window.contactcenterDeviceTranslations['reconnecting']) 
        ? window.contactcenterDeviceTranslations['reconnecting'] 
        : ((typeof _l !== 'undefined') ? _l('reconnecting') : 'Reconnecting...');
    $btn.html('<i class="fa fa-spinner fa-spin"></i> ' + reconnectingText);
    
    $.ajax({
      url: url_contactcenter + 'reconnect_device',
      type: 'POST',
      data: {
        device_id: deviceId
      },
      dataType: 'json',
      success: function(response) {
        // Helper function for translations
        var getTranslation = function(key, fallback) {
          if (typeof window.contactcenterDeviceTranslations !== 'undefined' && window.contactcenterDeviceTranslations[key]) {
            return window.contactcenterDeviceTranslations[key];
          }
          if (typeof _l !== 'undefined') {
            return _l(key);
          }
          return fallback || key;
        };
        
        if (response.success) {
          var successMsg = response.message || getTranslation('device_reconnected', 'Device reconnected successfully');
          alert_float('success', successMsg);
          
          // Remove the device from the list after a short delay
          setTimeout(function() {
            $btn.closest('.device-status-item').fadeOut(300, function() {
              $(this).remove();
              
              // Update badge count
              if (typeof updateDeviceStatusBadge === 'function') {
                updateDeviceStatusBadge();
              }
              
              // If no more disconnected devices, show success message
              if ($('.device-status-item').length === 0) {
                var $dropdown = $('#device-status-dropdown');
                var allConnectedMsg = getTranslation('all_devices_connected', 'All devices connected');
                $dropdown.html('<li><div class="tw-p-4 tw-text-center"><i class="fa-solid fa-check-circle tw-text-success tw-text-2xl tw-mb-2"></i><p class="tw-text-neutral-600 tw-mb-0">' + allConnectedMsg + '</p></div></li>');
              }
            });
          }, 1000);
        } else {
          var errorMsg = response.message || getTranslation('device_reconnect_failed', 'Failed to reconnect device');
          alert_float('danger', errorMsg);
          $btn.prop('disabled', false);
          $btn.html(originalHtml);
        }
      },
      error: function(xhr, status, error) {
        alert_float('danger', _l('device_reconnect_failed') + ': ' + error);
        $btn.prop('disabled', false);
        $btn.html(originalHtml);
      }
    });
  });
  
  /**
   * Update device status badge count
   */
  function updateDeviceStatusBadge() {
    var count = $('.device-status-item').length;
    var $badge = $('.device-status-badge-small');
    
    if (count === 0) {
      $badge.fadeOut(300, function() {
        $(this).remove();
      });
      
      // Update icon to show success state
      var $iconWrapper = $('.device-status-icon-wrapper');
      if ($iconWrapper.length > 0) {
        var $icon = $iconWrapper.find('i');
        $icon.removeClass('fa-mobile-screen-button tw-text-danger').addClass('fa-mobile-screen-button tw-text-success');
        var $link = $iconWrapper.find('a');
        if ($link.length > 0) {
          $link.replaceWith('<span class="tw-inline-flex tw-items-center tw-justify-center tw-w-7 tw-h-7 tw-rounded-md tw-border tw-border-solid tw-border-neutral-200/60 tw-bg-neutral-100/50"><i class="fa-solid fa-mobile-screen-button tw-text-success tw-text-sm"></i></span>');
        }
      }
    } else {
      if ($badge.length === 0) {
        $('.device-status-toggle-small').append('<span class="device-status-badge-small tw-absolute -tw-top-1 -tw-right-1 tw-bg-danger tw-text-white tw-text-xs tw-rounded-full tw-min-w-[16px] tw-h-4 tw-inline-flex tw-items-center tw-justify-center tw-px-1">' + count + '</span>');
      } else {
        $badge.text(count);
      }
    }
  }
  
  /**
   * Refresh device status periodically (every 30 seconds)
   */
  if ($('.device-status-widget-container').length > 0) {
    setInterval(function() {
      // Only refresh if dropdown is not open
      if (!$('.device-status-dropdown').hasClass('open')) {
        // Reload the page section or make AJAX call to refresh device status
        // For now, we'll just update the badge if needed
        // You can enhance this to make an AJAX call to get updated device status
      }
    }, 30000); // 30 seconds
  }

  // envia msg para whats
  $("form[name='sendMsg']").submit(function (eventos) {
    eventos.preventDefault();

    var Form = $(this);
    var formData = new FormData(Form[0]);
    var Phonenumber = $("input[name='phonenumber']").val();
    var textArea = Form.find("textarea[name='msg']").val();
    var edit_id = Form.find("input[name='edit_id']").val();

    if (Phonenumber == "" && textArea != "") {
      alert_float(
        "warning",
        "Selecione um contato ou digite um número de telefone!"
      );
      return false;
    }

    if (textArea != "" && Phonenumber != "") {
      desabilitarBotao();
      $(".chat-card-reply").css("display", "none");

      formData.append("phonenumber", Phonenumber);
      $.ajax({
        url: site_url + "admin/contactcenter/ajax_send_msg",
        data: formData,
        type: "POST",
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function () {
          $(".spinner-load-send").css("display", "none");
          $(".spinner-load-in").css("display", "block");
          Form.trigger("reset");
          // Limpa o editor EmojiOne
          $(".emojionearea-editor").html("");
          // Se quiser também desabilitar o botão após o envio
          $("#btn_submit").prop("disabled", true);
          $("#btn_submit").css("background", "#cccccc");
          $("input[name='edit_id']").val("");
          $("#textarea-chat").html("");
        },
        success: function (data) {
          $(".spinner-load-in").css("display", "none");
          $(".spinner-load-send").css("display", "block");
          $("#btn_submit").addClass("hidden");
          if (textArea) {
            $("#startRecording").removeClass("hidden");
          }

          if (data.send) {
            if (edit_id) {
              var target = $(".msg_" + edit_id + " .msg_content");
              if (target.length) {
                target.text(textArea);
                $(".msg_" + edit_id).addClass("edit_msg");
              }
            }
          }
        },
      });
    }
  });

  // envia msg para messenger
  $("form[name='sendMsgMessenger']").submit(function (eventos) {
    eventos.preventDefault();

    var Form = $(this);
    var formData = new FormData(Form[0]);
    var sender_id = $("input[name='sender_id']").val();
    var textArea = Form.find("textarea[name='msg']").val();

    if (sender_id == "" && textArea != "") {
      alert_float("warning", "Selecione um contato!");
      return false;
    }

    if (textArea != "" && sender_id != "") {
      //desabilitarBotao();

      $.ajax({
        url: site_url + "admin/contactcenter/ajax_send_msg_messenger",
        data: formData,
        type: "POST",
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function () {
          $(".spinner-load-send").css("display", "none");
          $(".spinner-load-in").css("display", "block");
          Form.trigger("reset");
          // Se quiser também desabilitar o botão após o envio
          $("#btn_submit").prop("disabled", true);
          $("#btn_submit").css("background", "#cccccc");
        },
        success: function (data) {
          $(".spinner-load-in").css("display", "none");
          $(".spinner-load-send").css("display", "block");
          if (data.retorno) {
            $("#retorno").append(data.retorno);
            var retornoDiv = document.getElementById("retorno");
            retornoDiv.scrollTop = retornoDiv.scrollHeight;
            if (deable_sound) {
              audio_notifcation("messenger", 0);
            }
            
            // Trigger AXIOM Intelligence update when new message is received
            // Check if it's an incoming message (from lead, not from agent)
            const appendedMessage = $(data.retorno);
            if (appendedMessage.hasClass('chat-others') || appendedMessage.find('.chat-others').length > 0) {
              // Dispatch custom event for AXIOM to listen to
              if (typeof window.dispatchEvent !== 'undefined') {
                window.dispatchEvent(new CustomEvent('axiom-message-received', {
                  detail: { message: appendedMessage.text() }
                }));
              }
              // Also call refresh function directly if available
              if (typeof window.refreshAXIOMIntelligence === 'function') {
                setTimeout(function() {
                  window.refreshAXIOMIntelligence();
                }, 500);
              }
            }
          }
          if (data.error) {
            alert_float("warning", data.error);
          }
        },
      });
    }
  });

  // envia msg media para messenger
  $("form[name='sendMediaMessenger']").submit(function (eventos) {
    eventos.preventDefault();

    var Form = $(this);
    var formData = new FormData(Form[0]);
    var sender_id = $("input[name='sender_id']").val();
    var textArea = Form.find("textarea[name='msg']").val();

    if (sender_id == "" && textArea != "") {
      alert_float("warning", "Selecione um contato!");
      return false;
    }

    if (sender_id != "") {
      $.ajax({
        url: site_url + "admin/contactcenter/ajax_send_msg_messenger",
        data: formData,
        type: "POST",
        dataType: "json",
        contentType: false,
        processData: false,
        beforeSend: function () {
          $(".chat-media").fadeOut();
          $(".chat-body div form").fadeIn();
          Form = $("form[name='sendMediaMessenger']");
          Form.trigger("reset");
          $("#inputFile_chat").val("");

          $(".preview-chat-media").children().hide();
          $("#veiwMediaVideo").fadeOut();
          $("#veiwMedia").fadeOut();
        },
        success: function (data) {
          if (data.send) {
            $(".chat-media").fadeOut();
            $(".chat-body div form").fadeIn();
            Form = $("form[name='sendMediaMessenger']");
            Form.trigger("reset");
            $("#inputFile_chat").val("");
          } else {
            alert_float("warning", "Erro ao enviar a mídia, tente novamente!");
          }
          if (data.clear) {
            Form.trigger("reset");
          }
          if (data.error) {
            alert_float("warning", data.error);
          }
        },
      });
    }
  });

  /**
   * verifica se o numero é valido no whatsapp
   */
  $("input[name='phonenumber']").blur(function () {
    var Phonenumber = $(this).val();
    var token = $("input[name='token']").val();
    if (Phonenumber.length > 2) {
      $.ajax({
        url: site_url + "admin/contactcenter/ajax_verify_number_whats",
        data: { phonenumber: Phonenumber, token: token },
        type: "POST",
        dataType: "json",
        success: function (data) {
          if (data.retorno) {
            $(".fa-ban").css("display", "none");
            $(".fa-whatsapp").fadeIn();

            //abre o chat no mobile
            if ($(window).width() < 661) {
              $(".chat-body").css("display", "block");
              $(".chat-body").addClass("full-mobile-chat");
              $(".chat-body").css("z-index", "100");
              $(".chat-body").css("background", "#000");
              $(".box-chat").removeClass("contacts-panel-closed");
            }
          } else if(data.local) {
            console.log("aguardando conexão com o whatsapp");
          }else{
            $(".fa-whatsapp").css("display", "none");
            $(".fa-ban").fadeIn();
          }
        },
      });
    }
  });

  /**
   * liga  AI do usuario
   */
  $(".j_on_ai").click(function () {
    var id = $(this).attr("data-id");
    $.ajax({
      url: url_contactcenter + "ajax_off_ai_user",
      data: { id: id, status: 1 },
      type: "POST",
      dataType: "json",
      success: function (data) {
        if (data.retorno) {
          $(".j_on_ai").css("display", "none");
          $(".j_off_ai").css("display", "block");
        }
      },
    });
  });

  /**
   * desliga AI do usuario
   */
  $(".j_off_ai").click(function () {
    var id = $(this).attr("data-id");
    $.ajax({
      url: url_contactcenter + "ajax_off_ai_user",
      data: { id: id, status: 0 },
      type: "POST",
      dataType: "json",
      success: function (data) {
        if (data.retorno) {
          $(".j_off_ai").css("display", "none");
          $(".j_on_ai").css("display", "block");
        }
      },
    });
  });
});

// Toggle AI for entire device (called by #onAi button)
function open_ai() {
  var tokenUser = $("input[name='token']").val();
  var ativaAi = $("#onAi").hasClass("active-ai");
  if (ativaAi) {
    $("#onAi").removeClass("active-ai");
    $("#onAi").addClass("off-ai");
  } else {
    $("#onAi").removeClass("off-ai");
    $("#onAi").addClass("active-ai");
  }

  $.ajax({
    url: url_contactcenter + "ajax_off_ai_all",
    data: { id: tokenUser },
    type: "POST",
    dataType: "json",
    success: function (data) {},
  });
}

// Toggle AI for specific lead/contact (called by header-chat-ai-status button)
function toggle_lead_ai() {
  // Get lead ID from the j_on_ai or j_off_ai elements
  var leadId = $(".j_on_ai").attr("data-id") || $(".j_off_ai").attr("data-id");
  
  if (!leadId || leadId === "") {
    console.warn("No lead ID found for AI toggle");
    return;
  }
  
  // Determine current status based on visible badge:
  // j_off_ai visible (red) = gpt_status == 1 = AI is OFF
  // j_on_ai visible (green) = gpt_status == 0 = AI is ON
  var currentStatus = $(".j_off_ai").is(":visible") ? 1 : 0; // 1 = OFF, 0 = ON
  var newStatus = currentStatus === 1 ? 0 : 1; // Toggle: if OFF (1), turn ON (0); if ON (0), turn OFF (1)
  
  console.log("toggle_lead_ai - Lead ID:", leadId, "Current status:", currentStatus, "New status:", newStatus);
  
  // Update UI immediately for better UX
  if (newStatus === 1) {
    $(".j_on_ai").css("display", "none");
    $(".j_off_ai").css("display", "block");
  } else {
    $(".j_off_ai").css("display", "none");
    $(".j_on_ai").css("display", "block");
  }
  
  console.log("toggle_lead_ai - Sending AJAX request:", {
    leadId: leadId,
    currentStatus: currentStatus,
    newStatus: newStatus,
    url: url_contactcenter + "ajax_off_ai_user"
  });
  
  $.ajax({
    url: url_contactcenter + "ajax_off_ai_user",
    data: { 
      id: leadId,
      status: newStatus
    },
    type: "POST",
    dataType: "json",
    success: function (data) {
      console.log("toggle_lead_ai - AJAX success:", data);
      // Reload messages to reflect the change
      if (typeof get_message_contact === 'function') {
        var phoneNumber = $("input[name='phonenumber']").val();
        var token = $("input[name='token']").val();
        if (phoneNumber && token) {
          get_message_contact(phoneNumber, token);
        }
      }
    },
    error: function(xhr, status, error) {
      console.error("toggle_lead_ai - AJAX error:", {
        status: status,
        error: error,
        response: xhr.responseText
      });
      // Revert UI on error
      if (newStatus === 1) {
        $(".j_on_ai").css("display", "block");
        $(".j_off_ai").css("display", "none");
      } else {
        $(".j_off_ai").css("display", "block");
        $(".j_on_ai").css("display", "none");
      }
    }
  });
}

function open_ai_messenger() {
  var ativaAi = $("#onAi").hasClass("active-ai");
  if (ativaAi) {
    $("#onAi").removeClass("active-ai");
    $("#onAi").addClass("off-ai");
  } else {
    $("#onAi").removeClass("off-ai");
    $("#onAi").addClass("active-ai");
  }

  $.ajax({
    url: url_contactcenter + "ajax_off_ai_all_messenger",
    data: { id: 1 },
    type: "POST",
    dataType: "json",
    success: function (data) {},
  });
}

function send_webhook(json) {
  $.ajax({
    url: url_contactcenter + "ajax_insert_msg",
    data: { id: JSON.stringify(json) },
    type: "POST",
    dataType: "json",
    success: function (data) {},
  });
}

function get_connection_status(id) {
  $.ajax({
    url: url_contactcenter + "get_status_connection",
    data: { id: id },
    type: "POST",
    dataType: "json",
    success: function (data) {
      if (data.status == "inChat" || data.status == "CONNECTED") {
        $(".contact-box-qrcode").css("display", "none");
        $("#message").html(data.status);
      }
      if (data.status == "qrReadError") {
        $(".contact-box-qrcode").css("display", "none");
        $("#message").html(
          "Falha na autenticação, o código QR expirou ou é inválido."
        );
      }
      if (data.redirect) {
        window.location.href = url_contactcenter + "device";
      }
    },
  });
}

function desconnect_device(id) {
  $.ajax({
    url: url_contactcenter + "desconnect_device",
    data: { id: id },
    type: "POST",
    dataType: "json",
    success: function (data) {
      if (data.status) {
        $(".contact-box-qrcode").css("display", "none");
        $("#contact-status").html(data.status);
      }
      if (data.redirect) {
        window.location.href = url_contactcenter + "device";
      }
    },
  });
}

function edit_device(id) {
  $.ajax({
    url: url_contactcenter + "edit_device",
    data: { id: id },
    type: "POST",
    dataType: "json",
    success: function (data) {
      if (data.dev_id) {
        $("#modalDevice").modal("show");
        $("input[name='dev_id']").val(data.dev_id);
        $("input[name='dev_name']").val(data.dev_name);
        $("input[name='dev_instance_name']").val(data.dev_instance_name);
        $("input[name='dev_number']").val(data.dev_number);
        $("input[name='dev_token']").val(data.dev_token);
        $("input[name='dev_voz_id']").val(data.dev_voz_id);
        $("select[name='staffid']").val(data.staffid).selectpicker("refresh");
        $("select[name='dev_type']").val(data.dev_type).selectpicker("refresh");
        $("select[name='api_type']").val(data.api_type).selectpicker("refresh");
        $("select[name='api_local']").val(data.api_local).selectpicker("refresh");
        $("select[name='server_id']")
          .val(data.server_id)
          .selectpicker("refresh");
        $("select[name='dev_openai']")
          .val(data.dev_openai)
          .selectpicker("refresh");
        $("select[name='assistant_ai_id']")
          .val(data.assistant_ai_id)
          .selectpicker("refresh");
        $("select[name='dev_engine']")
          .val(data.dev_engine)
          .selectpicker("refresh");

        if (data.show_messages_all_devices) {
          $("#show_messages_all_devices").prop("checked", true);
        } else {
          $("#show_messages_all_devices").prop("checked", false);
        }

        if (data.chatbot_id) {
          $("#chatbot_id").css("display", "block");
          $("select[name='chatbot_id']")
            .val(data.chatbot_id)
            .selectpicker("refresh");
        }

        if (data.contract_category !== "0" || data.contract_template !== "0") {
          $("#contract").removeClass("hidden");
          $("#status_contract").attr("checked", true);
          $("select[name='contract_category']")
            .val(data.contract_category)
            .selectpicker("refresh");
          $("select[name='contract_template']")
            .val(data.contract_template)
            .selectpicker("refresh");
          $("textarea[name='contract_msg']").html(data.contract_msg);

          $("#contract select").attr("required", "required");
          $("#contract textarea").attr("required", "required");
        }

        // Populate sales_knowledge field if available
        if (data.sales_knowledge !== undefined) {
          $("textarea[name='sales_knowledge']").val(data.sales_knowledge);
        }

        $(".input-group-addon a").css("display", "none");

        /*if (data.api_type == "wpp") {
          $("#dev_instance_name").css("display", "none");
        } else {
          $("#dev_instance_name").css("display", "block");
        }*/
      }
    },
  });
}

function delete_device(id) {
  $confirmacao = confirm(
    "Deseja realmente excluir este dispositivo? isso pagara todas as conversas ativas dele!"
  );
  if ($confirmacao) {
    $.ajax({
      url: url_contactcenter + "delete_device",
      data: { dev_id: id },
      type: "POST",
      dataType: "json",
      success: function (data) {
        if (data.redirect) {
          window.location.href = url_contactcenter + "device";
        }
      },
    });
  }
}

function delete_file_cectorstore(id) {
  $confirmacao = confirm("Deseja realmente excluir este arquivo?");
  if ($confirmacao) {
    $.ajax({
      url: url_contactcenter + "delete_file_cectorstore",
      data: { id: id },
      type: "POST",
      dataType: "json",
      success: function (data) {
        if (data.result) {
          alert_float("success", "Arquivo excluído com sucesso!");
          $("#file_" + id).remove();
        } else {
          alert_float("warning", "Erro ao excluir o arquivo!");
        }
      },
    });
  }
}

function get_media_evolution(id) {
  $.ajax({
    url: url_contactcenter + "ajax_get_media_evolution",
    data: { id: id },
    type: "POST",
    dataType: "json",
    beforeSend: function () {
      $(".get_" + id + " .load-media").fadeIn();
    },
    success: function (data) {
      if (data.type == "image") {
        var html =
          "<a href='" +
          data.base64 +
          "' target='_blank' data-lightbox='task-attachment'>" +
          "<img class='reply_image' src='" +
          data.base64 +
          "' />" +
          "</a>";

        $(".get_" + id + " .icon-media i").fadeOut();
        $(".get_" + id + " .load-media").fadeOut();
        $(".get_" + id + " .icon-media ").append(html);
      } else if (data.type == "video") {
        var html =
          "<video controls>" +
          "<source src='" +
          data.base64 +
          "' />" +
          "</video>";

        $(".get_" + id + " .icon-media i").fadeOut();
        $(".get_" + id + " .load-media").fadeOut();
        $(".get_" + id + " .icon-media ").append(html);
      } else if (data.type == "ptt") {
        var html =
          "<audio controls>" +
          "<source src='" +
          data.base64 +
          "'  type='audio/mpeg' />" +
          "</audio>";

        $(".get_" + id + " .icon-media i").fadeOut();
        $(".get_" + id + " .load-media").fadeOut();
        $(".get_" + id + " .icon-media ").append(html);
      } else if (data.type == "documentPDF") {
        var html =
          "<div class='pdf-info' style='text-align: center;'>" +
          "<img src='" +
          data.thumb +
          "'  >" +
          " <span class='pdf-title' style='display: block; font-weight: bold;'>document.pdf</span>" +
          " <div class='pdf-buttons'>" +
          "<a href='" +
          data.base64 +
          "' download='document.pdf' class='btn'>Salvar como...</a>" +
          " </div>" +
          "</div>";
        $(".get_" + id + " .icon-media i").fadeOut();
        $(".get_" + id + " .load-media").fadeOut();
        $(".get_" + id + " .icon-media ").append(html);
      } else if (data.type == "documentXLSX") {
        var html = `<div class='pdf-info' style='text-align: center;'>
                      <span class='pdf-title' style='display: block; font-weight: bold;'>document.xlsx</span>                     
                      <div class='pdf-buttons'>
                          <a href='${data.base64}' download='document.xlsx' class='btn'><i class='fa-solid fa-download'></i> Salvar como...</a>
                      </div> 
                  </div>`;

        $(".get_" + id + " .icon-media i").fadeOut();
        $(".get_" + id + " .load-media").fadeOut();
        $(".get_" + id + " .icon-media ").append(html);
      } else if (data.type == "documentZIP") {
        var html = `<div class='pdf-info' style='text-align: center;'>
                        <span class='pdf-title' style='display: block; font-weight: bold;'>document.zip</span>                     
                        <div class='pdf-buttons'>
                            <a href='${data.base64}' download='document.xlsx' class='btn'><i class='fa-solid fa-download'></i> Salvar como...</a>
                        </div> 
                    </div>`;

        $(".get_" + id + " .icon-media i").fadeOut();
        $(".get_" + id + " .load-media").fadeOut();
        $(".get_" + id + " .icon-media ").append(html);
      } else if (data.type == "documentDOCX") {
        var html = `<div class='pdf-info' style='text-align: center;'>
                        <span class='pdf-title' style='display: block; font-weight: bold;'>document.docx</span>                     
                        <div class='pdf-buttons'>
                            <a href='${data.base64}' download='document.xlsx' class='btn'><i class='fa-solid fa-download'></i> Salvar como...</a>
                        </div> 
                    </div>`;

        $(".get_" + id + " .icon-media i").fadeOut();
        $(".get_" + id + " .load-media").fadeOut();
        $(".get_" + id + " .icon-media ").append(html);
      } else if (data.type == "documentPPTX") {
        var html = `<div class='pdf-info' style='text-align: center;'>
                        <span class='pdf-title' style='display: block; font-weight: bold;'>document.docx</span>                     
                        <div class='pdf-buttons'>
                            <a href='${data.base64}' download='document.xlsx' class='btn'><i class='fa-solid fa-download'></i> Salvar como...</a>
                        </div> 
                    </div>`;

        $(".get_" + id + " .icon-media i").fadeOut();
        $(".get_" + id + " .load-media").fadeOut();
        $(".get_" + id + " .icon-media ").append(html);
      } else {
        $(".get_" + id + " .icon-media ").html("");
        var html =
          "<span class='badge' style='background:red;margin-bottom:10px;display: inline-block;'>Error ao baixar</span>";
        $(".get_" + id + " .load-media").fadeOut();
        $(".get_" + id + " .icon-media ").append(html);
      }
    },
  });
}

function audio_notifcation($type = "notification", time = 500) {
  const audioElement = document.createElement("audio");
  // Definir a origem do arquivo de áudio usando a propriedade src
  if ($type == "notification") {
    audioElement.src =
      site_url + "modules/contactcenter/assets/audio/notification.mp3";
  } else if ($type == "whatsapp") {
    audioElement.src =
      site_url + "modules/contactcenter/assets/audio/whatsapp.mp3";
  } else if ($type == "whatsapp-contact") {
    audioElement.src =
      site_url + "modules/contactcenter/assets/audio/notificacao-contact.mp3";
  } else if ($type == "agendamento") {
    audioElement.src =
      site_url + "modules/contactcenter/assets/audio/agendamento.mp3";
  } else if ($type == "messenger") {
    audioElement.src =
      site_url + "modules/contactcenter/assets/audio/messenger.mp3";
  }
  setTimeout(function () {
    // Executar a reprodução do som após 500ms
    audioElement.play();
  }, time);
}

function generateGUID() {
  function s4() {
    return Math.floor((1 + Math.random()) * 0x10000)
      .toString(16)
      .substring(1);
  }
  return (
    s4() +
    s4() +
    "-" +
    s4() +
    "-" +
    s4() +
    "-" +
    s4() +
    "-" +
    s4() +
    s4() +
    s4()
  );
}

function get_guid() {
  var guid = generateGUID();
  $("input[name='dev_token']").val(guid);
}

/**
 * Funcionalidade para gravar o audio
 */
document.addEventListener("DOMContentLoaded", function () {
  let mediaRecorder;
  let audioChunks = [];

  const startButton = document.getElementById("startRecording");
  const stopButton = document.getElementById("stopRecording");
  const statusDiv = document.getElementById("status");

   // Se os botões não existem, não executa mais nada
  if (!startButton || !stopButton) return;

  startButton.addEventListener("click", async () => {
    var phonenumber = $('input[name="phonenumber"]').val();
    if (phonenumber == "") {
      alert_float(
        "warning",
        "Por favor, preencha o campo de telefone ou clique em um contato!"
      );
      return;
    }

    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      mediaRecorder = new MediaRecorder(stream);
      audioChunks = [];

      mediaRecorder.ondataavailable = (event) => {
        if (event.data.size > 0) {
          audioChunks.push(event.data);
        }
      };

      mediaRecorder.onstop = () => {
        const audioBlob = new Blob(audioChunks, {
          type: "audio/ogg; codecs=opus",
        });
        const formData = new FormData();
        formData.append("audio", audioBlob, "audio.ogg");

        var csrfToken = $('input[name="csrf_token_name"]').val();
        var staffid = $("#staffid_transfer").val();
        var phonenumber = $('input[name="phonenumber"]').val();
        formData.append("phonenumber", phonenumber);
        formData.append("staffid", staffid);
        formData.append("csrf_token_name", csrfToken);

        $.ajax({
          type: "POST",
          url: url_contactcenter + "ajax_send_audio", // URL do seu script PHP para processar o arquivo
          data: formData,
          dataType: "json",
          contentType: false,
          processData: false,
          beforeSend: function () {
            alert_float("success", "Enviando o áudio!");
          },
          success: function (data) {
            audioChunks = [];
            $("#startRecording").show();
            $("#stopRecording").hide();

            if (data.result) {
              alert_float("success", "Audio enviado!");
            } else {
              alert_float("warning", "Erro ao enviar áudio!");
            }
          },
          error: function (xhr, status, error) {
            console.error("Erro ao enviar áudio: " + error);
            audioChunks = [];
            $("#startRecording").show();
            $("#stopRecording").hide();
            alert_float("danger", "Erro ao enviar áudio: " + error);
          },
        });
      };

      mediaRecorder.start();
      startButton.style.display = "none";
      stopButton.style.display = "flex";
    } catch (err) {
      console.error("Erro ao acessar o microfone", err);
    }
  });

  stopButton.addEventListener("click", () => {
    if (mediaRecorder && mediaRecorder.state === "recording") {
      mediaRecorder.stop();
      startButton.style.display = "flex";
      stopButton.style.display = "none";
    }
  });
});

/**
 * Funcionalidade para gravar o audio messenger
 */
document.addEventListener("DOMContentLoaded", function () {
  let mediaRecorder;
  let audioChunks = [];

  const startButton = document.getElementById("startRecordingMessenger");
  const stopButton = document.getElementById("stopRecording");
  const statusDiv = document.getElementById("status");

   // Se os botões não existem, não executa mais nada
  if (!startButton || !stopButton) return;

  startButton.addEventListener("click", async () => {
    var sender_id = $('input[name="sender_id"]').val();
    if (sender_id == "") {
      alert_float("warning", "Por favor, escolha um contato!");
      return;
    }

    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      mediaRecorder = new MediaRecorder(stream);
      audioChunks = [];

      mediaRecorder.ondataavailable = (event) => {
        if (event.data.size > 0) {
          audioChunks.push(event.data);
        }
      };

      mediaRecorder.onstop = () => {
        const audioBlob = new Blob(audioChunks, {
          type: "audio/wav",
        });
        const formData = new FormData();
        formData.append("audio", audioBlob, "audio.wav");

        var csrfToken = $('input[name="csrf_token_name"]').val();
        var staffid = $("#staffid").val();
        var sender_id = $('input[name="sender_id"]').val();
        var page_id = $('input[name="page_id"]').val();
        formData.append("page_id", page_id);
        formData.append("sender_id", sender_id);
        formData.append("staffid", staffid);
        formData.append("csrf_token_name", csrfToken);
        formData.append("action", "audio");

        $.ajax({
          type: "POST",
          url: url_contactcenter + "ajax_send_msg_messenger",
          data: formData,
          dataType: "json",
          contentType: false,
          processData: false,
          beforeSend: function () {
            alert_float("success", "Enviando o áudio!");
          },
          success: function (data) {
            audioChunks = [];
            $("#startRecording").show();
            $("#stopRecording").hide();

            if (data.retorno) {
              alert_float("success", "Audio enviado!");
            } else {
              alert_float("warning", "Erro ao enviar áudio!");
            }
          },
          error: function (xhr, status, error) {
            console.error("Erro ao enviar áudio: " + error);
            audioChunks = [];
            $("#startRecording").show();
            $("#stopRecording").hide();
            alert_float("danger", "Erro ao enviar áudio: " + error);
          },
        });
      };

      mediaRecorder.start();
      startButton.style.display = "none";
      stopButton.style.display = "flex";
    } catch (err) {
      console.error("Erro ao acessar o microfone", err);
    }
  });

  stopButton.addEventListener("click", () => {
    if (mediaRecorder && mediaRecorder.state === "recording") {
      mediaRecorder.stop();
      startButton.style.display = "flex";
      stopButton.style.display = "none";
    }
  });
});

/**
 * Funcionalidade para verificar se uma imagem está disponível
 */
document.addEventListener("DOMContentLoaded", function () {
  const images = document.querySelectorAll(".card-img-top");

  images.forEach((img) => {
    const imgUrl = img.src; // Salva a URL original da imagem

    fetch(imgUrl, { method: "HEAD" }) // Verifica o status da imagem sem baixar o conteúdo
      .then((response) => {
        if (!response.ok) {
          // Se a resposta não for OK (status diferente de 200-299)
          console.log(
            `Erro ${response.status} ao carregar a imagem: ${imgUrl}`
          );
          img.src = site_url + "/assets/images/preview-not-available.jpg"; // Define a imagem padrão
        }
      })
      .catch((error) => {
        console.error(`Erro ao carregar a imagem: ${imgUrl}`, error);
        img.src = site_url + "/assets/images/preview-not-available.jpg"; // Define a imagem padrão em caso de erro na requisição
      });
  });
});

var deable_sound = true;
/**
 * função que desativa o som do chat
 */
function desable_sound(element) {
  if (deable_sound == false) {
    element.innerHTML = '<i class="fa-solid fa-volume-high"></i>';
    deable_sound = true;
  } else {
    element.innerHTML = '<i class="fa-solid fa-volume-xmark"></i>';
    deable_sound = false;
  }
}

/**
 * serve_name_pusher
 */
// function pusher_messenger(page_id = null) {
//   var serve_name_pusher = $("input[name='serve_name_pusher']").val();
//   var pusher_app_key = $("input[name='pusher_app_key']").val();
//   var pusher_options = JSON.parse($("input[name='pusher_options']").val());
//   var pusher = new Pusher(pusher_app_key, pusher_options);
//   var channel = pusher.subscribe(serve_name_pusher);

//   // Extrai apenas o SERVER_NAME, removendo caminhos e protocolo
//   var serverName = window.location.hostname;
//   var serve = serverName + "_facebookleadsintegration_messenger"; // Formato desejado

//   var channel = pusher.subscribe(serve);
//   channel.bind("messenger_chat", function (data) {
//     var page_id = $("input[name='page_id']").val();
//     var sender_id = $("input[name='sender_id']").val();

//     //adiciona a mensagem ao chat
//     if (data.chat) {
//       if (sender_id == data.sender_id && page_id == data.page_id) {
//         $("#retorno").append(data.chat);
//         var retornoDiv = document.getElementById("retorno");
//         retornoDiv.scrollTop = retornoDiv.scrollHeight;
//         if (deable_sound) {
//           audio_notifcation("messenger", 0);
//         }
//       }
//     }

//     //adiciona contato ao chat
//     if (page_id == data.page_id) {
//       cria_contato_messenger_pusher(data);
//     }
//   });
// }

// pusher_messenger();

/**
 * Cria o contato via pusher
 * @param {array} contact
 */
function cria_contato_messenger_pusher(contact) {
  var table = $("#contaChat tbody"); // A tabela

  if (contact) {
    var id = contact.sender_id;

    var row = table.find('tr[data-id="' + id + '"]');
    if (row.length) {
      table.prepend(row);
      var time = formatDatePusher(contact.date_time);
      row.find(".chat-contato-time").text(time);

      // Verifica se o contato está ativo, caso não, adiciona uma nova badge
      if (!row.find(".active-contact").length > 0) {
        // Pega o valor atual da badge e converte para número
        var badgeText = row.find(".badge").text().trim();
        var badge = parseInt(badgeText, 10);

        if (isNaN(badge)) {
          badge = 1;
        } else {
          badge += 1;
        }
        // Atualiza o valor da badge com o novo número
        row.find(".badge").text(badge);
      }
      if (deable_sound) {
        audio_notifcation("whatsapp-contact");
      }
    } else {
      console.log("não existe");
      $.ajax({
        url: site_url + "/contactcenter/get_contact_messenger_pusher",
        type: "POST",
        dataType: "json",
        data: {
          page_id: contact.page_id,
          sender_id: contact.sender_id,
        },
        success: function (data) {
          console.log(data);
          if (data.retorno) {
            table.prepend(data.retorno);
            if (deable_sound) {
              audio_notifcation("whatsapp-contact");
            }
          }
        },
      });
    }
  }
}

/**
 * conecta ao websocket
 * @param {array} data
 */
function get_websocket(data) {
  var conected = false;
  // pega o json
  if (typeof data === "string") {
    data = JSON.parse(data);
  }
  var socket;

  if (typeof socket !== "undefined" && socket) {
    socket.disconnect(); // Desconecta o socket existente
    console.log("Socket desconectado.");
  }

  function connectWebSocket() {
    var url = data.server.url.replace(/^https?:\/\//, "");

    if (data.server.version == 1) {
      socket = io("wss://" + url + data.device.dev_instance_name, {
        transports: ["websocket"],
      });
    } else {
      socket = io("wss://" + url + data.device.dev_instance_name, {
        transports: ["websocket"],
        query: {
          apikey: data.device.dev_token,
        },
      });
    }

    socket.on("connect", () => {
      conected = true;
      console.log("Conectado ao WebSocket");
    });

    // Captura erros de conexão
    socket.on("connect_error", (error) => {
      alert_float(
        "danger",
        "Erro de conexão, verifique sua instância com o servidor!"
      );
      console.error("Erro de conexão:", error.message);
      conected = false;
      retryConnection();
    });

    // Captura erros gerais
    socket.on("error", (error) => {
      console.error("Erro no WebSocket:", error.message);
    });

    // msg recebida
    socket.on("messages.upsert", (msg) => {
      var htmlChat = montaHtmlChatWebSocket(msg);
      //console.log("Mensagem recebida:", msg);
      som_agendamento(msg);
    });

    // qrcode recebido
    socket.on("qrcode.updated", (qrcode) => {
      if (qrcode.data.qrcode.base64) {
        $(".contact-box-qrcode").css("display", "flex");
        $(".loaderQrcode").fadeOut();
        $("#contact-qrcode").attr("src", qrcode.data.qrcode.base64);
        $("#contact-status").html(qrcode.data.qrcode.pairingCode);
        $("#message").html("Por favor, escaneie o código QR para conectar!");
      }
    });

    // Statu da conexão
    socket.on("connection.update", (connection) => {
      if (
        connection.data.state == "open" ||
        connection.data.state == "inChat"
      ) {
        $("#message").html("Conectado ao WhatsApp!");
        $("#contact-qrcode").fadeOut();
        $(".contact-box-back").css("display", "block");
      }
    });

    // msg enviada
    socket.on("send.message", (msg) => {
      var htmlChat = montaHtmlChatWebSocket(msg);
      //console.log("Mensagem enviada:", msg);
    });

    socket.on("messages.update", (msg) => {
      //console.log("Mensagem update:", msg);
      chat_updade_message(msg);
    });

    // socket.onAny((event, ...args) => {
    //   console.log(`Evento recebido: ${event}`, args);
    // });

    // Lidando com desconexão
    socket.on("disconnect", () => {
      console.log("Desconectado do WebSocket");
      conected = false;
      retryConnection(); // Tenta reconectar ao WebSocket após a desconexão
    });
  }

  function retryConnection() {
    if (!conected) {
      console.log("Tentando reconectar...");
      setTimeout(connectWebSocket, 30000); // Tenta reconectar após 10 segundos
    }
  }

  connectWebSocket();
  return conected;
}

function som_agendamento(response) {
  // Verificar se o caminho até singleSelectReply existe
  if (
    response &&
    response.data &&
    response.data.message &&
    response.data.message.listResponseMessage &&
    response.data.message.listResponseMessage.singleSelectReply &&
    response.data.message.listResponseMessage.singleSelectReply.selectedRowId
  ) {
    var confimacaoAgenda =
      response.data.message.listResponseMessage.singleSelectReply.selectedRowId;

    if (confimacaoAgenda) {
      var confimacaoAgenda = confimacaoAgenda.split("-");
      var idAgenda = confimacaoAgenda[0];
      if (idAgenda == "yes") {
        audio_notifcation("agendamento");
      }
    }
  }
}

function chat_updade_message(msg) {
  var class_status;

  if (msg.data.fromMe) {
    if (msg.data.status == "PENDING") {
      class_status = "sent";
    } else if (msg.data.status == "DELIVERY_ACK") {
      class_status = "received";
    } else if (msg.data.status == "READ") {
      class_status = "read";
    } else {
      class_status = "sent";
    }
    var id = msg.data.keyId;
    var element = $("#" + id + " .chat_status");
    element.removeClass("sent received read");
    element.addClass(class_status);
  }
}

/**
 * Cria o contato via websocket
 * @param {array} contact
 */
function cria_contato_websocket(contact) {
  var table = $("#contaChat tbody"); // A tabela

  // Verifica se contact e contact.data.remoteJid existem
  if (contact && contact.data && contact.data.key.remoteJid) {
    var id = contact.data.key.remoteJid.split("@")[0]; // Extrai o número antes do "@"

    var row = table.find('tr[data-id="' + id + '"]');
    if (row.length) {
      table.prepend(row);
      var time = formatDate(contact.date_time);
      row.find(".chat-contato-time").text(time);

      // Verifica se o contato está ativo, caso não, adiciona uma nova badge
      if (!row.find(".active-contact").length > 0) {
        // Pega o valor atual da badge e converte para número
        var badgeText = row.find(".badge").text().trim();
        var badge = parseInt(badgeText, 10);

        if (isNaN(badge)) {
          badge = 1;
        } else {
          badge += 1;
        }
        // Atualiza o valor da badge com o novo número
        row.find(".badge").text(badge);
      }
      if (deable_sound) {
        audio_notifcation("whatsapp-contact");
      }
    } else {
      $.ajax({
        url: site_url + "/contactcenter/get_contact_websocket",
        type: "POST",
        dataType: "json",
        data: {
          id: contact,
        },
        success: function (data) {
          if (data.contact) {
            // Verifica novamente após o retorno do AJAX
            var id = contact.data.key.remoteJid.split("@")[0];
            var existingRow = table.find('tr[data-id="' + id + '"]');

            if (existingRow.length) {
              return false; // Já existe, não adiciona
            }

            table.prepend(data.contact);

            if (deable_sound) {
              audio_notifcation("whatsapp-contact");
            }
          }
        },
      });
    }
  }
}

/**
 * Monta o html da conversa
 * @param {array} response
 * @returns
 */
function montaHtmlChatWebSocket(response) {
  // Cria o contato
  cria_contato_websocket(response);

  let ln = "";
  const load = site_url + "/modules/contactcenter/assets/image/load.gif";

  var phonenumber = response.data.key.remoteJid;

  var chatOn = $("input[name='phonenumber']").val();

  var isGroup = response.data.key.remoteJid.includes("@g.us");
  var participantName = "";

  var fromMe = response.data.key.fromMe;

  if (isGroup) {
    if (fromMe) {
      if (response.data.pushName) {
        participantName = response.data.pushName;
      }
    }
    var groupId = response.data.key.remoteJid.replace("@g.us", "");
    phonenumber = groupId;
  } else {
    phonenumber = phonenumber.split("@")[0];
  }

  if (response && phonenumber == chatOn) {
    const classChat = response.data.key.fromMe ? "chat-my" : "chat-others";

    if (classChat == "chat-my") {
      var action = "action-my";
    } else {
      var action = "";
    }

    // Certifique-se de que cada parte é uma string
    var id = response.data.key.id;
    if (response.data.key.fromMe) {
      if (response.data.status == "PENDING") {
        var class_status = "sent";
      } else if (response.data.status == "DELIVERY_ACK") {
        var class_status = "received";
      } else if (response.data.status == "READ") {
        var class_status = "read";
      } else {
        var class_status = "sent";
      }
    } else {
      var class_status = null;
    }

    //atualiza o texto editado na mensagem recebida
    if (response.data.message.editedMessage) {
      var editId =
        response.data.message.editedMessage.message.protocolMessage.key.id;
      var editMgs =
        response.data.message.editedMessage.message.protocolMessage
          .editedMessage.conversation;
      console.log("editId " + editId);
      console.log("editMgs " + editMgs);
      $(".msg_" + editId + " .msg_content").text(editMgs);
      $(".msg_" + editId).addClass("edit_msg_others");
    }

    // pega reply
    var div_reply = "";
    if (response.data.contextInfo && response.data.contextInfo.quotedMessage) {
      var reply_phonenumber =
        response.data.contextInfo.participant.split("@")[0];

      //se for imagem coloca icon de imagem
      if (response.data.contextInfo.quotedMessage.imageMessage) {
        var icon = '<i class="fa-solid fa-image"></i>';
      } else {
        var icon = "";
      }

      div_reply =
        "<div class='reply_card' id='reply_" +
        response.data.contextInfo.stanzaId +
        "'  data-id='" +
        response.data.contextInfo.stanzaId +
        "'>";
      div_reply +=
        "<span class='reply_title'>" +
        (response.data.pushName || reply_phonenumber) +
        "</span>";
      div_reply += "<br><span>" + icon + "</span>";
      div_reply +=
        "<p class='reply_msg'>" +
        (response.data.contextInfo.quotedMessage.extendedTextMessage?.text ||
          response.data.contextInfo.quotedMessage.imageMessage?.caption) +
        "</p>";
      div_reply += "</div>";
    }

    // Verifica o tipo da mensagem
    if (
      ["extendedTextMessage", "conversation"].includes(
        response.data.messageType
      )
    ) {
      ln += `<div class='${classChat} msg-div j_reply ${action}' data-hora='${formatDate(
        response.date_time
      )}' data-action='2' id='${id}' data-name='${
        response.data.pushName || phonenumber
      }'>
                      <div class='msg_${id}'>    
                          ${div_reply}   
                          <div class="msg_author_name">${participantName}</div>                   
                          <p class='msg_content'>${nl2br(
                            response.data.message.extendedTextMessage?.text ||
                              response.data.message.conversation
                          )}</p>
                          <span class='msg-time'>${formatDate(
                            response.date_time
                          )}<span class='chat_status ${class_status}'></span></span>
                      </div>
                  </div>`;
    } else if (response.data.messageType == "documentMessage") {
      if (response.data.message.documentMessage.mimetype == "application/pdf") {
        ln += `<div class='${classChat} msg-div get_${id} ${action}' data-hora='${formatDate(
          response.date_time
        )}' data-action='1' id='${id}'> 
                           ${div_reply} 
                      <div class="msg_author_name">${participantName}</div> 
                      <img class='load-media' style='width:30px;height:30px; display:none; ' src='${load}'/>
                      <div class='icon-media'>
                          <i class='fa-regular fa-file-pdf' onclick='get_media_evolution("${id}")' ></i>
                          <p style='margin-top: 5px; width: 100%;'>${nl2br(
                            response.data.message.documentMessage?.caption
                          )}</p>
                        <span class='msg-time'>${formatDate(
                          response.date_time
                        )}<span class='chat_status ${class_status}' id='${id}'></span></span> 
                      </div>
                </div>`;
      } else if (
        response.data.message.documentMessage.mimetype ==
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
      ) {
        ln += `<div class='${classChat} msg-div get_${id} ${action}' data-hora='${formatDate(
          response.date_time
        )}' data-action='1' id='${id}'> 
                             ${div_reply} 
                      <div class="msg_author_name">${participantName}</div>  
                      <img class='load-media' style='width:30px;height:30px; display:none; ' src='${load}'/>
                      <div class='icon-media'>
                          <i class='fa-regular fa-file-word' onclick='get_media_evolution("${id}")' ></i>
                          <p style='margin-top: 5px; width: 100%;'>${nl2br(
                            response.data.message.documentMessage?.caption
                          )}</p>
                        <span class='msg-time'>${formatDate(
                          response.date_time
                        )}<span class='chat_status ${class_status}'></span></span> 
                      </div>
                </div>`;
      } else if (
        response.data.message.documentMessage.mimetype ==
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
      ) {
        ln += `<div class='${classChat} msg-div get_${id} ${action}' data-hora='${formatDate(
          response.date_time
        )}' data-action='1' id='${id}'> 
                         ${div_reply}  
                      <div class="msg_author_name">${participantName}</div> 
                      <img class='load-media' style='width:30px;height:30px; display:none; ' src='${load}'/>
                      <div class='icon-media'>
                          <i class='fa-regular fa-file-excel' onclick='get_media_evolution("${id}")' ></i>
                          <p style='margin-top: 5px; width: 100%;'>${nl2br(
                            response.data.message.documentMessage?.caption
                          )}</p>
                        <span class='msg-time'>${formatDate(
                          response.date_time
                        )}<span class='chat_status ${class_status}'></span></span> 
                      </div>
                </div>`;
      } else if (
        response.data.message.documentMessage.mimetype == "application/zip"
      ) {
        ln += `<div class='${classChat} msg-div get_${id} ${action}' data-hora='${formatDate(
          response.date_time
        )}' data-action='1' id='${id}'> 
                       ${div_reply} 
                      <div class="msg_author_name">${participantName}</div>  
                      <img class='load-media' style='width:30px;height:30px; display:none; ' src='${load}'/>
                      <div class='icon-media'>
                          <i class='fa-regular fa-file-archive' onclick='get_media_evolution("${id}")' ></i>
                          <p style='margin-top: 5px; width: 100%;'>${nl2br(
                            response.data.message.documentMessage?.caption
                          )}</p>
                        <span class='msg-time'>${formatDate(
                          response.date_time
                        )}<span class='chat_status ${class_status}'></span></span> 
                      </div>
                </div>`;
      }
    } else if (response.data.messageType == "videoMessage") {
      if (response.data.message.base64) {
        ln += `<div class='${classChat} msg-div get_${id} ${action}' data-hora='${formatDate(
          response.date_time
        )}' data-action='1' id='${id}'> 
                         ${div_reply} 
                      <div class="msg_author_name">${participantName}</div>
                      <div>
                         <video controls>
                            <source src='data:video;base64,${
                              response.data.message.base64
                            }'  type='video/mp4' >   
                         </video>
                         <span>${
                           response.data.message.imageMessage?.caption || ""
                         }</span> 
                      <span class='msg-time'>${formatDate(
                        response.date_time
                      )}<span class='chat_status ${class_status}'></span></span>   
                      </div>
                  </div>`;
      } else {
        ln += `<div class='${classChat} msg-div get_${id} ${action}' data-hora='${formatDate(
          response.date_time
        )}' data-action='1' id='${id}'>  
                     ${div_reply} 
                    <div class="msg_author_name">${participantName}</div> 
                    <img class='load-media' style='width:30px;height:30px; display:none; ' src='${load}'/>
                    <div class='icon-media'>
                        <i class='fa-regular fa-file-video' onclick='get_media_evolution("${id}")' ></i>
                      <span class='msg-time'>${formatDate(
                        response.date_time
                      )}<span class='chat_status ${class_status}'></span></span> 
                    </div>
              </div>`;
      }
    } else if (response.data.messageType == "audioMessage") {
      ln += `<div class='${classChat} msg-div get_${id} ${action}' data-hora='${formatDate(
        response.date_time
      )}' data-action='1' id='${id}'>   
                 ${div_reply} 
                <div class="msg_author_name">${participantName}</div>
                <img class='load-media' style='width:30px;height:30px; display:none; ' src='${load}'/>
                <div class='icon-media'>
                    <i class='fa-solid fa-volume-high' onclick='get_media_evolution("${id}")' ></i>
                  <span class='msg-time'>${formatDate(
                    response.date_time
                  )}<span class='chat_status ${class_status}'></span></span> 
                </div>
          </div>`;
    } else if (response.data.messageType == "imageMessage") {
      if (response.data.message.base64) {
        ln += `<div class='${classChat} msg-div j_reply ${action}' id='${id}' data-hora='${formatDate(
          response.date_time
        )}' data-action='1' data-name='${
          response.data.pushName || phonenumber
        }'>                 
                <div class="msg_author_name">${participantName}</div>        
                <div class='box-chat-img'>
                    <div>
                        ${div_reply}  
                        <a href='data:image/jpeg;base64,${
                          response.data.message.base64
                        }' target='_blank' data-lightbox='task-attachment'>
                          <img src='data:image/jpeg;base64,${
                            response.data.message.base64
                          }'/>
                        </a>                   
                        <span>${
                          response.data.message.imageMessage?.caption || ""
                        }</span>
                        <span class='msg-time'>${formatDate(
                          response.date_time
                        )}<span class='chat_status ${class_status}'></span></span>
                    </div>
                </div>
            </div>`;
      } else {
        ln += `<div class='${classChat} msg-div j_reply ${action}' id='${id}' data-hora='${formatDate(
          response.date_time
        )}' data-action='1' data-name='${
          response.data.pushName || phonenumber
        }'>                  
                     ${div_reply} 
                    <div class="msg_author_name">${participantName}</div> 
                    <img class='load-media' style='width:30px;height:30px; display:none; ' src='${load}'/>
                    <div class='icon-media'>
                        <i class='fa-solid fa-photo-film' onclick='get_media_evolution("${id}")' ></i>
                      <span class='msg-time'>${formatDate(
                        response.date_time
                      )}<span class='chat_status ${class_status}'></span></span> 
                    </div>
              </div>`;
      }
    }

    if (response.data.messageType == "audioMessage") {
      setTimeout(function () {
        if (!document.getElementById(id)) {
          $("#retorno").append(ln);
          
          // Trigger AXIOM Intelligence update when new incoming message is received
          if (classChat === "chat-others" && typeof window.refreshAXIOMIntelligence === 'function') {
            // Dispatch custom event for AXIOM to listen to
            if (typeof window.dispatchEvent !== 'undefined') {
              window.dispatchEvent(new CustomEvent('axiom-message-received', {
                detail: { message: response.data.message?.conversation || response.data.message?.extendedTextMessage?.text || '' }
              }));
            }
            // Call refresh function with a small delay to ensure DOM is updated
            setTimeout(function() {
              window.refreshAXIOMIntelligence();
            }, 500);
          }
        }
        var retornoDiv = document.getElementById("retorno");
        retornoDiv.scrollTop = retornoDiv.scrollHeight;
        if (deable_sound) {
          audio_notifcation("whatsapp");
        }
      }, 1000);
    } else {
      if (!document.getElementById(id)) {
        $("#retorno").append(ln);
      }
      var retornoDiv = document.getElementById("retorno");
      retornoDiv.scrollTop = retornoDiv.scrollHeight;
      if (deable_sound) {
        audio_notifcation("whatsapp");
      }
      
      // Trigger AXIOM Intelligence update when new incoming message is received
      if (classChat === "chat-others" && typeof window.refreshAXIOMIntelligence === 'function') {
        // Dispatch custom event for AXIOM to listen to
        if (typeof window.dispatchEvent !== 'undefined') {
          window.dispatchEvent(new CustomEvent('axiom-message-received', {
            detail: { message: response.data.message?.conversation || response.data.message?.extendedTextMessage?.text || '' }
          }));
        }
        // Call refresh function with a small delay to ensure DOM is updated
        setTimeout(function() {
          window.refreshAXIOMIntelligence();
        }, 500);
      }
    }
  }
}

/**
 * Adiciona quebra de linha
 * @param {string} str
 * @returns
 */
function nl2br(str) {
  return str.replace(/\n/g, "<br>");
}

/**
 * Formata a data
 * @param {date} dateStr
 * @returns
 */
function formatDate(dateStr) {
  const date = new Date(dateStr); // Cria um objeto Date baseado na string de data

  // Formata o dia, mês, ano, horas e minutos
  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0"); // Os meses em JavaScript são de 0 a 11
  const year = date.getFullYear();

  const hours = String(date.getUTCHours()).padStart(2, "0"); // Pega a hora em UTC
  const minutes = String(date.getUTCMinutes()).padStart(2, "0"); // Pega os minutos em UTC

  return `${day}/${month}/${year} ${hours}:${minutes}`;
}

function formatDatePusher(dateTimeString) {
  const date = new Date(dateTimeString); // Converte para um objeto Date
  const day = String(date.getDate()).padStart(2, "0"); // Dia com 2 dígitos
  const month = String(date.getMonth() + 1).padStart(2, "0"); // Mês com 2 dígitos
  const year = date.getFullYear();

  // Hora e minutos ajustados
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");

  return `${day}/${month}/${year} ${hours}:${minutes}`;
}

/**
 * Carrega imagem
 */
function upload_media_chat() {
  var input = document.getElementById("inputFile_chat");
  input.setAttribute("accept", "image/*,video/*");
  input.click();
}

function upload_document_chat() {
  var input = document.getElementById("inputFile_chat");
  input.setAttribute("accept", "application/pdf");
  input.click();
}

function upload_document_zip_chat() {
  var input = document.getElementById("inputFile_chat");
  input.setAttribute("accept", "application/zip,application/x-zip-compressed");
  input.click();
}
function upload_document_xlsx_chat() {
  var input = document.getElementById("inputFile_chat");
  input.setAttribute(
    "accept",
    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
  );
  input.click();
}
function upload_document_docx_chat() {
  var input = document.getElementById("inputFile_chat");
  input.setAttribute(
    "accept",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/msword"
  );
  input.click();
}

/**
 * gera previsualização
 */
$(document).ready(function () {
  $("#inputFile_chat").change(function () {
    const file = this.files[0];
    const reader = new FileReader();

    if (file) {
      const fileType = file.type;

      reader.onload = function (e) {
        const fileName = file.name;

        if (fileType.startsWith("image/")) {
          // Se for imagem, mostra a imagem
          $("#veiwMedia").attr("src", e.target.result).show();
          $("form[name='sendMedia'] input[name='action']").val("image");
        } else if (fileType.startsWith("video/")) {
          // Se for vídeo, mostra o vídeo
          $("#veiwMediaVideo").attr("src", e.target.result).show();
          $("form[name='sendMedia'] input[name='action']").val("video");
        } else if (fileType === "application/pdf") {
          // Se for PDF, mostra no iframe
          $("#veiwMediaPdf").fadeIn();
          $("form[name='sendMedia'] input[name='action']").val("document");
        } else if (
          fileType === "application/x-zip-compressed" ||
          fileType === "application/zip"
        ) {
          $("#veiwMediaZip").fadeIn();
          $("form[name='sendMedia'] input[name='action']").val("zip");
        } else if (
          fileType ===
          "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        ) {
          $("#veiwMediaXlsx").fadeIn();
          $("form[name='sendMedia'] input[name='action']").val("xlsx");
        } else if (
          fileType ===
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
          fileType === "application/msword"
        ) {
          $("#veiwMediaDocx").fadeIn();
          $("form[name='sendMedia'] input[name='action']").val("document");
        } else {
          alert_float("danger", "Formato de arquivo inválido!");

          return false;
        }

        $("#veiwFileName").html(fileName);
        $(".chat-media").fadeIn();
        $(".chat-body div form").fadeOut();
      };

      reader.readAsDataURL(file);
    }
  });
});

/**
 * fecha previsualização e limpa o form[name='sendMedia']
 */
$(".close-chat-media").click(function () {
  $(".chat-media").fadeOut();
  $(".preview-chat-media").children().hide();
  $(".chat-body div form").fadeIn();
  Form = $("form[name='sendMedia']");
  Form.trigger("reset");
  $("#inputFile_chat").val("");
});

// envia msg media para whats
$("form[name='sendMedia']").submit(function (eventos) {
  eventos.preventDefault();
  const load = site_url + "/modules/contactcenter/assets/image/load.gif";

  var Form = $(this);
  var formData = new FormData(Form[0]);
  var Phonenumber = $("input[name='phonenumber']").val();

  if (Phonenumber == "") {
    alert_float(
      "danger",
      "Selecione um contato ou digite um número de telefone!"
    );
    return false;
  }

  if (Phonenumber != "") {
    const tempId = "msg_" + Date.now(); // ID único da mensagem temporária
    formData.append("phonenumber", Phonenumber);
    $.ajax({
      url: site_url + "admin/contactcenter/ajax_send_msg",
      data: formData,
      type: "POST",
      dataType: "json",
      contentType: false,
      processData: false,
      beforeSend: function () {
        //faz uma previsualização do arquivo
        var file = $("#inputFile_chat")[0].files[0];
        if (file) {
          var previewHTML = "";
          const fileType = file.type;
          if (fileType.startsWith("image/")) {
          }

          if (fileType.startsWith("image/")) {
            var htmlArquivo = `<img src='${URL.createObjectURL(file)}'/>`;
          } else if (fileType.startsWith("video/")) {
            // Se for vídeo, mostra o vídeo
            var htmlArquivo = `<video controls>
                            <source src='${URL.createObjectURL(
                              file
                            )}' type='video/mp4'>
                            </video>`;
          } else if (fileType === "application/pdf") {
            // Se for PDF, mostra no iframe
            var htmlArquivo = `<span><i class="fa-regular fa-file-pdf"></i></span>`;
          } else if (
            fileType === "application/x-zip-compressed" ||
            fileType === "application/zip"
          ) {
            var htmlArquivo = `<span><i class="fa-regular fa-file-zipper"></i></span>`;
          } else if (
            fileType ===
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
          ) {
            var htmlArquivo = `<span><i class="fa-regular fa-file-excel"></i></span>`;
          } else if (
            fileType ===
              "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
            fileType === "application/msword"
          ) {
            var htmlArquivo = `<span><i class="fa-regular fa-file-word"></i></span>`;
          }

          previewHTML += `<div class='chat-my' id="${tempId}" >
                        <div class='box-chat-img'>
                            <div>                                
                                ${htmlArquivo}
                                <img class='' style='width:30px;height:30px;position: absolute;bottom: 12px;right: 0px;' src='${load}'/>
                                <span class='msg-time'>${formatDate(
                                  new Date()
                                )}<span class='chat_status'></span></span>
                            </div>
                        </div>
                    </div>`;

          $("#retorno").append(previewHTML);
          if (deable_sound) {
            audio_notifcation("whatsapp");
          }
          setTimeout(function () {
            var retornoDiv = document.getElementById("retorno");
            retornoDiv.scrollTop = retornoDiv.scrollHeight;
          }, 100);
        }

        $(".chat-media").fadeOut();
        $(".chat-body div form").fadeIn();
        Form = $("form[name='sendMedia']");
        Form.trigger("reset");
        $("#inputFile_chat").val("");
        $(".preview-chat-media").children().hide();
        $("#veiwMediaDocx").fadeOut();
        $("#veiwMediaXlsx").fadeOut();
        $("#veiwMediaPdf").fadeOut();
        $("#veiwMediaZip").fadeOut();
        $("#veiwMediaVideo").fadeOut();
        $("#veiwMedia").fadeOut();
      },
      success: function (data) {
        if (data.send) {
          $(".chat-media").fadeOut();
          $(".chat-body div form").fadeIn();
          Form = $("form[name='sendMedia']");
          Form.trigger("reset");
          $("#inputFile_chat").val("");
        } else {
          alert_float("danger", "Erro ao enviar mensagem!");
        }
        $("#" + tempId).remove();
        if (data.clear) {
          Form.trigger("reset");
        }
      },
    });
  }
});

$(document).ready(function () {
  // envia msg media para whats
  $("form[name='timer-Ia']").submit(function (eventos) {
    eventos.preventDefault();
    var Form = $(this);
    var formData = new FormData(Form[0]);

    $.ajax({
      url: site_url + "admin/contactcenter/ajax_time_ia",
      data: formData,
      type: "POST",
      dataType: "json",
      contentType: false,
      processData: false,
      beforeSend: function () {},
      success: function (data) {
        if (data.message) {
          alert_float("success", data.message);
        }
      },
    });
  });
});

function timer_chat() {
  $(".timer-chat-container").slideToggle();
}

$(document).ready(function () {
  $("select[name='type_clear_threads']").change(function () {
    if ($(this).val() == 1) {
      $("#clear_threads_leads").removeClass("hidden");
    } else {
      $("#clear_threads_leads").addClass("hidden");
      $("input[name='clear_threads_leads']").val("");
    }
  });
});

function clear_threads_leads() {
  var type_clear_threads = $("select[name='type_clear_threads']").val();
  var clear_threads_leads = $("input[name='clear_threads_leads']").val();

  if (type_clear_threads == 1 && clear_threads_leads == "") {
    alert_float("warning", "Por favor, digite o número do Lead!");
    return;
  }

  if (type_clear_threads == 1) {
    if (
      !confirm(
        "Deseja realmente limpar o thread do lead " + clear_threads_leads + "?"
      )
    ) {
      return;
    }
  } else {
    if (!confirm("Deseja realmente limpar todos os threads?")) {
      return;
    }
  }

  $.ajax({
    url: site_url + "admin/contactcenter/ajax_clear_threads_leads",
    data: {
      type: type_clear_threads,
      lead_id: clear_threads_leads,
    },
    type: "POST",
    dataType: "json",
    beforeSend: function () {
      alert_float("warning", "Limpando threads, aguarde!");
      $("input[name='clear_threads_leads']").val("");
    },
    success: function (data) {
      if (data.retorno) {
        alert_float("success", "Threads limpo com sucesso!");
      } else {
        alert_float("warning", "Erro ao limpar threads!");
      }
    },
  });
}

function contactcenter_status_contract() {
  if ($("#status_contract").is(":checked")) {
    $("#contract").removeClass("hidden");
    $("#contract select").attr("required", "required");
    $("#contract textarea").attr("required", "required");
  } else {
    $("#contract").addClass("hidden");
    $("#contract select").removeAttr("required");
    $("#contract textarea").removeAttr("required");

    $("#contract").find("select").val("").selectpicker("refresh");
    $("#contract").find("textarea").val("");
  }
}

function create_variacoe_openai() {
  var text = $("textarea[name='list_text']").val();
  var quantidade = $("#variacoes").val();
  if (text == "" || quantidade == "") {
    alert_float("danger", "Por favor, digite o texto!");
    return;
  }

  if (quantidade == 0) {
    alert_float("danger", "Por favor, digite a quantidade de variações!");
    return;
  }

  if (quantidade > 15) {
    alert_float(
      "danger",
      "A quantidade de variações não pode ser maior que 15!"
    );
    return;
  }

  $.ajax({
    url: site_url + "admin/contactcenter/ajax_create_variacoe_openai",
    data: {
      text: text,
      quantidade: quantidade,
    },
    type: "POST",
    dataType: "json",
    beforeSend: function () {
      alert_float("warning", "Criando variações, aguarde!");
    },
    success: function (data) {
      if (data.retorno) {
        alert_float("success", "Variações criadas com sucesso!");
        $("textarea[name='list_text']").val(data.retorno);
      } else {
        alert_float("warning", "Erro ao criar variações!");
      }
    },
  });
}

function create_variacoe_openai_resgate() {
  var text = $("textarea[name='text']").val();
  var quantidade = $("#variacoes").val();
  if (text == "" || quantidade == "") {
    alert_float("danger", "Por favor, digite o texto!");
    return;
  }

  if (quantidade == 0) {
    alert_float("danger", "Por favor, digite a quantidade de variações!");
    return;
  }

  if (quantidade > 15) {
    alert_float(
      "danger",
      "A quantidade de variações não pode ser maior que 15!"
    );
    return;
  }

  $.ajax({
    url: site_url + "admin/contactcenter/ajax_create_variacoe_openai",
    data: {
      text: text,
      quantidade: quantidade,
    },
    type: "POST",
    dataType: "json",
    beforeSend: function () {
      alert_float("warning", "Criando variações, aguarde!");
    },
    success: function (data) {
      if (data.retorno) {
        alert_float("success", "Variações criadas com sucesso!");
        $("textarea[name='text']").val(data.retorno);
      } else {
        alert_float("warning", "Erro ao criar variações!");
      }
    },
  });
}

/**
 * faz shearch no chat pelo caracter |
 */
$(document).ready(function () {
  $(document).on("click", ".search-item-msgspeed", function () {
    var content = $(this).data("content");
    if ($("#textarea-chat")[0].emojioneArea) {
      $("#textarea-chat")[0].emojioneArea.setText(content);
    }
    $("#search_search_msgspeed").hide();
  });
});

function contactcenter_msg_speed(text) {
  // Verifica se o primeiro caractere digitado é "/"
  if (text.startsWith("/")) {
    // Remove o caractere "/"
    text = text.replace("/", "");

    // verifica se tem mais de 3 caracteres
    if (text.length > 2) {
      $.ajax({
        url: site_url + "admin/contactcenter/ajax_search_msgspeed",
        data: {
          search: text,
          device_id: $("input[name='device_id']").val(),
        },
        type: "POST",
        dataType: "json",
        success: function (data) {
          if (data.retorno && data.retorno.length > 0) {
            var resultsHtml = "";
            data.retorno.forEach(function (item) {
              resultsHtml += `<li class="search-item-msgspeed" data-content="${item.content}">
                                  <b>${item.title}</b> - ${item.content}
                              </li>`;
            });
            $("#search_search_msgspeed").html(resultsHtml).show();
          } else {
            $("#search_search_msgspeed").hide(); // Esconde se não houver resultados
          }
        },
      });
    }
  } else {
    $("#search_search_msgspeed").hide();
  }
}
