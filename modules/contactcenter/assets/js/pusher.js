// const serverName = window.location.hostname;
// var staffid = $("input[name='staffid']").val();
// var serve_name_pusher = $("input[name='serve_name_pusher']").val();
// var pusher_app_key = $("input[name='pusher_app_key']").val();
// var pusher_options = JSON.parse($("input[name='pusher_options']").val());
// var pusher = new Pusher(pusher_app_key, pusher_options);
// var channel = pusher.subscribe(serve_name_pusher);

// channel.bind("noticacao_staff_" + staffid, function (data) {
//   Trigger(data);
// });

// channel.bind("noticacao_geral", function (data) {
//   Trigger(data);
// });

// function Trigger(Message) {
//   $(".trigger_ajax").fadeOut("fast", function () {
//     $(this).remove();
//   });
//   $("body").before(
//     "<div class='trigger_notification'>" +
//       Message +
//       "</div>"
//   );
//   $('.trigger_ajax').fadeIn();
  
// }

// function TriggerClose() {
//   $('.trigger_notification').fadeOut('fast', function () {
//       $(this).remove();
//   });
// }

// $(document).on('click', '.trigger_notification', function () {
//   $('.trigger_notification').fadeOut('fast', function () {
//       $(this).remove();
//   });
// });

