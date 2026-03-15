<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>




<h4><?php echo htmlspecialchars(_l('contac_settings_api')); ?></h4>
<br>
<?php
$staff = $this->staff_model->get('', ['active' => 1]);
$attrs = isset($attrs) ? $attrs : [];
//echo render_input('settings[tokenBearer_contactcenter]', 'Bearer Token (Whatsapp)', get_option('tokenBearer_contactcenter'), 'password', $attrs);
//echo render_input('settings[tokenprofileid_contactcenter]', 'Profile Id (Whatsapp)', get_option('tokenprofileid_contactcenter'), 'password', $attrs);
echo render_input('settings[tokenopenai_contactcenter]', 'Token (IA)', get_option('tokenopenai_contactcenter'), 'password', $attrs);
echo render_input('settings[token_elevenlabs_contactcenter]', 'Token Clone Voz', get_option('token_elevenlabs_contactcenter'), 'password', $attrs);
?>
<br>
<h5><?php echo _l('ads_analytics_ai_insights'); ?></h5>
<div class="row">
    <div class="col-md-8">
        <?php
        echo render_input('settings[contactcenter_gemini_api_key]', _l('ads_analytics_gemini_api_key'), get_option('contactcenter_gemini_api_key'), 'password', $attrs);
        ?>
        <small class="text-muted">
            <?php echo _l('ads_analytics_gemini_api_key_help'); ?>
        </small>
    </div>
    <div class="col-md-4">
        <label>&nbsp;</label>
        <a href="https://aistudio.google.com/app/apikey" target="_blank" class="btn btn-default btn-block">
            <i class="fa fa-external-link"></i> <?php echo _l('ads_analytics_get_gemini_api_key'); ?>
        </a>
    </div>
</div>
<p class="text-muted">
    <i class="fa fa-info-circle"></i> <?php echo _l('ads_analytics_gemini_api_key_note'); ?>
</p>
<br>
<h5><?php echo _l('import_leads_google_places_api'); ?></h5>
<div class="row">
    <div class="col-md-8">
        <?php
        echo render_input('settings[contactcenter_google_places_api_key]', _l('import_leads_google_places_api_key'), get_option('contactcenter_google_places_api_key'), 'password', $attrs);
        ?>
        <small class="text-muted">
            <?php echo _l('import_leads_google_places_api_key_help'); ?>
        </small>
    </div>
    <div class="col-md-4">
        <label>&nbsp;</label>
        <a href="https://console.cloud.google.com/google/maps-apis/credentials" target="_blank" class="btn btn-default btn-block">
            <i class="fa fa-external-link"></i> <?php echo _l('import_leads_get_google_places_api_key'); ?>
        </a>
    </div>
</div>
<p class="text-muted">
    <i class="fa fa-info-circle"></i> <?php echo _l('import_leads_google_places_api_key_note'); ?>
</p>
<br>
<h5><?php echo _l('import_leads_field_mapping'); ?></h5>
<p class="text-muted"><?php echo _l('import_leads_field_mapping_help'); ?></p>
<?php
// Get all custom fields for leads
$custom_fields = get_custom_fields('leads');
$field_mappings = json_decode(get_option('contactcenter_ai_lead_field_mappings'), true);
if (!is_array($field_mappings)) {
    $field_mappings = [];
}

// Available AI fields
$ai_fields = [
    'whatsapp_number' => _l('import_leads_whatsapp'),
    'social_media' => _l('import_leads_social_media'),
    'rating' => _l('import_leads_rating'),
    'description' => _l('import_leads_description')
];

// Standard lead fields
$standard_fields = [
    'name' => _l('leads_dt_name'),
    'company' => _l('lead_company'),
    'phonenumber' => _l('leads_dt_phonenumber'),
    'email' => _l('lead_email'),
    'website' => _l('lead_website'),
    'address' => _l('lead_address'),
    'city' => _l('lead_city'),
    'state' => _l('lead_state'),
    'country' => _l('clients_country')
];
?>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><?php echo _l('import_leads_ai_field'); ?></th>
                    <th><?php echo _l('import_leads_map_to'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ai_fields as $ai_field => $ai_label) { 
                    $current_mapping = isset($field_mappings[$ai_field]) ? $field_mappings[$ai_field] : '';
                ?>
                    <tr>
                        <td><strong><?php echo $ai_label; ?></strong></td>
                        <td>
                            <select name="settings[contactcenter_ai_lead_field_mappings][<?php echo $ai_field; ?>]" class="form-control selectpicker" data-width="100%">
                                <option value=""><?php echo _l('import_leads_map_to_none'); ?></option>
                                <optgroup label="<?php echo _l('import_leads_standard_fields'); ?>">
                                    <?php foreach ($standard_fields as $std_field => $std_label) { 
                                        $selected = ($current_mapping === $std_field) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $std_field; ?>" <?php echo $selected; ?>><?php echo $std_label; ?></option>
                                    <?php } ?>
                                </optgroup>
                                <?php if (!empty($custom_fields)) { ?>
                                    <optgroup label="<?php echo _l('import_leads_custom_fields'); ?>">
                                        <?php foreach ($custom_fields as $cf) { 
                                            $cf_value = 'custom_field_' . $cf['id'];
                                            $selected = ($current_mapping === $cf_value) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $cf_value; ?>" <?php echo $selected; ?>><?php echo $cf['name']; ?> (<?php echo _l('custom_field'); ?>)</option>
                                        <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <small class="text-muted">
            <i class="fa fa-info-circle"></i> <?php echo _l('import_leads_field_mapping_note'); ?>
        </small>
    </div>
</div>
<?php

?>
<br>
<h4>Mensagens de quando rejeitar a ligação</h4>
<br>
<?php
echo render_input('settings[whatsapp_msg_call]', 'Mensagem de envio call', get_option('whatsapp_msg_call'), 'text', $attrs);
?>
<h4><?php echo htmlspecialchars(_l('contac_settings_cadastro')); ?></h4>
<br>
<?php
echo render_select('settings[leads_cadastro_contactcenter]', $leads_statuses, array('id', 'name'), 'Status de cadastro do Leads pela AI', get_option('leads_cadastro_contactcenter'));
echo render_select('settings[leads_source_contactcenter]', $leads_sources, array('id', 'name'), 'leads_default_source', get_option('leads_source_contactcenter'));
?>
<hr />
<br>
<h4><?php echo htmlspecialchars(_l('contac_settings_agendamento')); ?></h4>
<br>
<?php
echo render_select('settings[leads_cadastro_call_contactcenter]', $leads_statuses, array('id', 'name'), 'Status de agendamento pela AI', get_option('leads_cadastro_call_contactcenter'));
echo render_input('settings[time_contactcenter]', 'Horário de agendamento separadodo por vírgula exp: 10:00,14:30', get_option('time_contactcenter'), 'text', $attrs);
echo render_input('settings[quant_time_contactcenter]', 'Quantidade de horários que serão apresentados.', get_option('quant_time_contactcenter'), 'number', $attrs);
echo render_input('settings[agendaMinutesToAdd]', 'Minutos para adicionar no horário. Se agora for 9 horas e tiver agendamento paras 9:30 e estiver configurado 60, nao vai trazer esse horario das 9:30', get_option('agendaMinutesToAdd'), 'number', $attrs);
echo render_input('settings[minutes_schedule]', 'Minutos que dura uma reunião. Exemplo: 1 hora é 60 minutos', get_option('minutes_schedule'), 'number', $attrs);
echo render_input('settings[saturdayHours]', 'Horário de agendamento dos SABADOS separados por vírgula exp: 10:00,14:30', get_option('saturdayHours'), 'text', $attrs);
echo render_input('settings[contactcenter_notify_whatsapp_agendamento]', _l("contac_tempo_confirmar_agendamento"), get_option('contactcenter_notify_whatsapp_agendamento'), 'number', $attrs);
echo render_input('settings[contac_title_agendamento]', _l("contac_confirm_agendamento_title"), get_option('contac_title_agendamento'), 'text', $attrs);
echo render_yes_no_option('contac_active_confirm_agendamento', 'contac_confirm_agendamento');
?>
<div id="confirm_agendamento_texts" style="<?= get_option('contac_active_confirm_agendamento') == 0 ? 'display:none;' : ''; ?>">
    <p class="text-muted"><i class="fa fa-info-circle"></i> <?= _l('contac_confirm_template_help'); ?></p>
    <?php
    $default_template = "*" . _l('contac_notification_title_whats_list') . "*\n\n"
        . _l('contac_notification_description_whats_saudacao_list') . " {name}, " . _l('contac_notification_description_whats_list') . " {date}\n\n"
        . _l('contac_confirm_link_label') . " {link}\n\n"
        . _l('contac_confirm_keyword_instructions');
    echo render_textarea('settings[contac_confirm_msg_template]', _l('contac_confirm_msg_template_label'), get_option('contac_confirm_msg_template'), ['rows' => 6, 'placeholder' => $default_template]);
    ?>
    <small class="text-muted"><?= _l('contac_confirm_template_placeholders'); ?></small>
    <hr style="border-color: rgba(0,0,0,0.1); margin: 15px 0;">
    <p class="text-muted" style="font-size:12px;"><i class="fa fa-reply"></i> <?= _l('contac_confirm_response_texts_title'); ?></p>
    <?php
    echo render_input('settings[contac_confirm_msg_success]', _l('contac_confirm_msg_success_label'), get_option('contac_confirm_msg_success'), 'text', ['placeholder' => _l('contac_notification_agendamento_success')]);
    echo render_input('settings[contac_confirm_msg_cancel]', _l('contac_confirm_msg_cancel_label'), get_option('contac_confirm_msg_cancel'), 'text', ['placeholder' => _l('contac_notification_agendamento_cancel')]);
    ?>
    <hr style="border-color: rgba(0,0,0,0.1); margin: 15px 0;">
    <div class="panel panel-default" style="border-left: 3px solid #03a9f4;">
        <div class="panel-body">
            <h5 style="margin-top:0;"><i class="fa fa-flask"></i> <?= _l('contac_confirm_test_title'); ?></h5>
            <p class="text-muted" style="font-size:12px;"><?= _l('contac_confirm_test_description'); ?></p>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?= _l('contac_confirm_test_phone'); ?></label>
                        <input type="text" id="confirm_test_phone" class="form-control" placeholder="5511999999999">
                    </div>
                </div>
                <div class="col-md-6" style="padding-top:24px;">
                    <button type="button" id="btnGenerateConfirmLink" class="btn btn-default" style="margin-right:6px;">
                        <i class="fa fa-link"></i> <?= _l('contac_confirm_test_generate_link'); ?>
                    </button>
                    <button type="button" id="btnSendConfirmTest" class="btn btn-info">
                        <i class="fa fa-paper-plane"></i> <?= _l('contac_confirm_test_send'); ?>
                    </button>
                </div>
            </div>
            <div id="confirm_test_result" style="display:none; margin-top:10px;">
                <div class="alert alert-success" id="confirm_test_success" style="display:none;"></div>
                <div class="alert alert-danger" id="confirm_test_error" style="display:none;"></div>
                <div id="confirm_test_preview" style="display:none;">
                    <label><?= _l('contac_confirm_test_link'); ?></label>
                    <div class="input-group" style="margin-bottom:8px;">
                        <input type="text" id="confirm_test_link_url" class="form-control" readonly style="font-size:12px;">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" onclick="var v=document.getElementById('confirm_test_link_url').value;if(v){navigator.clipboard.writeText(v);}" title="Copy"><i class="fa fa-copy"></i></button>
                        </span>
                    </div>
                    <a id="confirm_test_link_open" href="#" target="_blank" class="btn btn-success btn-block" style="margin-bottom:14px;">
                        <i class="fa fa-external-link"></i> <?= _l('contac_confirm_test_open_page'); ?>
                    </a>
                    <label><?= _l('contac_confirm_test_message_preview'); ?></label>
                    <pre id="confirm_test_message_text" style="background:#f5f5f5; padding:12px; border-radius:6px; white-space:pre-wrap; font-size:13px; max-height:250px; overflow-y:auto;"></pre>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function _initConfirmTest() {
    if (typeof jQuery === 'undefined') { return setTimeout(_initConfirmTest, 100); }
    jQuery(function($) {
        $('input[name="settings[contac_active_confirm_agendamento]"]').on('change', function() {
            $('#confirm_agendamento_texts').toggle($(this).val() == '1');
        });

        function runConfirmTest(sendWhatsapp) {
            var phone = $('#confirm_test_phone').val().trim();
            if (!phone && sendWhatsapp) {
                alert('<?= _l('contac_confirm_test_phone_required'); ?>');
                return;
            }
            var btn = sendWhatsapp ? $('#btnSendConfirmTest') : $('#btnGenerateConfirmLink');
            var origHtml = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ...');
            $('#confirm_test_result').hide();
            $('#confirm_test_success, #confirm_test_error, #confirm_test_preview').hide();

            $.ajax({
                url: admin_url + 'contactcenter/send_confirmation_test',
                type: 'POST',
                data: {
                    phone: phone || '0',
                    generate_only: sendWhatsapp ? 0 : 1,
                    <?= $this->security->get_csrf_token_name(); ?>: '<?= $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    $('#confirm_test_result').show();
                    if (response.success) {
                        $('#confirm_test_success').show().html('<i class="fa fa-check"></i> ' + response.message);
                        if (response.link) {
                            $('#confirm_test_link_url').val(response.link);
                            $('#confirm_test_link_open').attr('href', response.link);
                            $('#confirm_test_message_text').text(response.text_message);
                            $('#confirm_test_preview').show();
                        }
                    } else {
                        $('#confirm_test_error').show().text(response.message || 'Error');
                    }
                },
                error: function(xhr) {
                    $('#confirm_test_result').show();
                    $('#confirm_test_error').show().text('Request failed: ' + xhr.status + ' ' + xhr.statusText);
                },
                complete: function() {
                    btn.prop('disabled', false).html(origHtml);
                }
            });
        }

        $('#btnGenerateConfirmLink').on('click', function() { runConfirmTest(false); });
        $('#btnSendConfirmTest').on('click', function() { runConfirmTest(true); });
    });
})();
</script>
<?php
echo render_yes_no_option('contac_active_link_call', 'contac_active_link_call');
?>
<hr />
<br>
<h4><?php echo htmlspecialchars(_l('contac_settings_group_chat')); ?></h4>
<br>
<?php
$group_format_help = _l('contac_settings_group_chat_name_format_help');
echo render_input('settings[contactcenter_group_chat_name_format]', _l('contac_settings_group_chat_name_format'), get_option('contactcenter_group_chat_name_format', 'AXIOM x {lead_name} ({date})'), 'text', $attrs);
?>
<p class="text-muted">
    <?php echo htmlspecialchars($group_format_help); ?><br>
    <strong><?php echo _l('contac_settings_group_chat_placeholders'); ?>:</strong><br>
    • <code>{lead_name}</code> - <?php echo _l('contac_settings_group_chat_placeholder_lead_name'); ?><br>
    • <code>{date}</code> - <?php echo _l('contac_settings_group_chat_placeholder_date'); ?> (DD/MM/YYYY)<br>
    • <code>{date_iso}</code> - <?php echo _l('contac_settings_group_chat_placeholder_date_iso'); ?> (YYYY-MM-DD)<br>
    <strong><?php echo _l('contac_settings_group_chat_example'); ?>:</strong> <code>AXIOM x {lead_name} ({date})</code> → "AXIOM x João Silva (15/01/2026)"
</p>
<br>
<?php
$selected_staff = get_option('contactcenter_group_chat_auto_add_staff');
$selected_staff_array = !empty($selected_staff) ? explode(',', $selected_staff) : [];
?>
<div class="form-group">
    <label><?php echo _l('contac_settings_group_chat_auto_add_staff'); ?></label>
    <select name="settings[contactcenter_group_chat_auto_add_staff][]" id="contactcenter_group_chat_auto_add_staff" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" multiple>
        <?php foreach ($staff as $staff_member) { 
            $is_selected = in_array($staff_member['staffid'], $selected_staff_array);
        ?>
            <option value="<?php echo $staff_member['staffid']; ?>" <?php echo $is_selected ? 'selected' : ''; ?>>
                <?php echo $staff_member['firstname'] . ' ' . $staff_member['lastname']; ?>
            </option>
        <?php } ?>
    </select>
    <small class="text-muted"><?php echo _l('contac_settings_group_chat_auto_add_staff_help'); ?></small>
    <!-- Hidden field to ensure the field is always sent, even when empty -->
    <input type="hidden" name="settings[contactcenter_group_chat_auto_add_staff_sent]" value="1">
</div>
<br>
<?php
$default_group_picture = get_option('contactcenter_group_chat_default_picture');
$picture_url = '';
if (!empty($default_group_picture) && file_exists(FCPATH . $default_group_picture)) {
    $picture_url = base_url($default_group_picture);
}
?>
<div class="form-group">
    <label><?php echo _l('contac_settings_group_chat_default_picture'); ?></label>
    <?php if (!empty($picture_url)) { ?>
        <div class="mb-3">
            <img src="<?php echo $picture_url; ?>" alt="Current group picture" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
            <br><small class="text-muted"><?php echo _l('contac_settings_group_chat_current_picture'); ?></small>
        </div>
    <?php } ?>
    <input type="file" name="contactcenter_group_chat_default_picture" id="contactcenter_group_chat_default_picture" accept="image/jpeg,image/jpg,image/png" class="form-control">
    <small class="text-muted"><?php echo _l('contac_settings_group_chat_default_picture_help'); ?></small>
    <?php if (!empty($default_group_picture)) { ?>
        <input type="hidden" name="contactcenter_group_chat_default_picture_current" value="<?php echo htmlspecialchars($default_group_picture); ?>">
    <?php } ?>
</div>
<br>
<?php
$default_group_message = get_option('contactcenter_group_chat_default_message');
$group_message_help = _l('contac_settings_group_chat_default_message_help');
echo render_textarea('settings[contactcenter_group_chat_default_message]', _l('contac_settings_group_chat_default_message'), $default_group_message, ['rows' => 4]);
?>
<p class="text-muted">
    <?php echo htmlspecialchars($group_message_help); ?><br>
    <strong><?php echo _l('contac_settings_group_chat_placeholders'); ?>:</strong><br>
    • <code>{lead_name}</code> - <?php echo _l('contac_settings_group_chat_placeholder_lead_name'); ?><br>
    • <code>{date}</code> - <?php echo _l('contac_settings_group_chat_placeholder_date'); ?> (DD/MM/YYYY)<br>
    • <code>{date_iso}</code> - <?php echo _l('contac_settings_group_chat_placeholder_date_iso'); ?> (YYYY-MM-DD)<br>
    • <code>{google_meet_link}</code> - <?php echo _l('contac_settings_group_chat_placeholder_google_meet'); ?><br>
    <strong><?php echo _l('contac_settings_group_chat_example'); ?>:</strong> <code>Olá {lead_name}! Reunião agendada para {date}. Link: {google_meet_link}</code>
</p>
<hr />
<br>
<h4><?php echo htmlspecialchars(_l('contac_settings_audio')); ?></h4>
<br>
<?php
echo render_yes_no_option('active_audio_contactcenter', 'contac_active_audio');
echo render_yes_no_option('active_audio_contactcenter_elevenlabs', 'contac_active_audio_elevenlabs');

?>
<hr />
<br>
<h4><?php echo htmlspecialchars(_l('contac_settings_sincronizacao_whatsapp')); ?></h4>
<br>
<?php
echo render_yes_no_option('contac_settings_sincronizacao_whatsapp_active', 'contac_settings_sincronizacao_whatsapp_active');
echo render_yes_no_option('contac_settings_sincronizacao_whatsapp_leads', 'contac_settings_sincronizacao_whatsapp_leads');

?>
<hr />
<br>
<h4><?php echo htmlspecialchars(_l('openai_speed_send_label')); ?></h4>
<br>
<?php
echo render_yes_no_option('openai_speed_send', 'openai_speed_send');
?>
<hr />
<br>
<h4><?php echo htmlspecialchars(_l('contac_historico_mgs_ai_label')); ?></h4>
<br>
<?php
echo render_yes_no_option('historico_mgs_ai_active', 'contac_historico_mgs_ai');
?>
<hr />
<br>
<h4><?php echo htmlspecialchars(_l('contac_settings_contador')); ?></h4>
<br>
<?php echo render_yes_no_option('active_contador_contactcenter', 'contac_active_contador'); ?>
<p><?php echo htmlspecialchars(_l('contac_settings_contador_label')); ?></p>
<div class="row">
    <div class="col-md-4 leads-filter-column">
        <?php echo render_select('settings[staff_contador_contactcenter]', $staff, array('staffid', 'firstname', 'lastname'), 'Staff', get_option('staff_contador_contactcenter')); ?>
    </div>
    <div class="col-md-4 leads-filter-column">
        <?php echo render_select('settings[leads_status_contador_contactcenter]', $leads_statuses, array('id', 'name'), 'Status', get_option('leads_status_contador_contactcenter')); ?>
    </div>
    <div class="col-md-4 leads-filter-column">
        <?php echo render_select('settings[leads_source_contador_contactcenter]', $leads_sources, array('id', 'name'), 'leads_default_source', get_option('leads_source_contador_contactcenter')); ?>
    </div>
</div>
<hr />
<br>
<h4><?php echo htmlspecialchars(_l('default_status_ticket_ia_label')); ?></h4>
<br>

<div class="row">
    <div class="col-md-4 leads-filter-column">
        <?php echo render_select('settings[default_staff_ticket_ia]', $staff, array('staffid', 'firstname', 'lastname'), 'Staff', get_option('default_staff_ticket_ia')); ?>
    </div>
    <div class="col-md-4 leads-filter-column">
        <?php
        $this->load->model('tickets_model');
        $statuses                       = $this->tickets_model->get_ticket_status();
        $statuses['callback_translate'] = 'ticket_status_translate';
        echo render_select('settings[default_status_ticket_ia]', $statuses, ['ticketstatusid', 'name'], 'default_status_ticket_ia', get_option('default_status_ticket_ia'), [], [], '', '', false);
        ?>
    </div>
</div>




<br>
<h4><?php echo htmlspecialchars(_l('contac_settings_clear_theads')); ?></h4>
<br>
<?php

$select = array(
    '1' => array('id' => '1', 'name' => _l('contac_settings_clear_theads_label_leads')),
    '2' => array('id' => '2', 'name' => _l('contac_settings_clear_theads_label_all')),
);

echo render_select('type_clear_threads', $select, array('id', 'name'), 'contac_settings_clear_theads_label', '2');

echo render_input('clear_threads_leads', _l("contac_settings_clear_theads_label_leads_id"), '', 'number', $attrs, ["id" => "clear_threads_leads"], "hidden", '');


?>
<a onclick="clear_threads_leads()" class="btn btn-danger"><?= _l("contac_settings_clear_theads_label_buttom")  ?></a>



<hr />
<br>
<h4><?php echo htmlspecialchars(_l('contac_settings_cron')); ?></h4>
<br>
<p><?php echo htmlspecialchars(_l('contac_settings_cron_label')); ?></p>
<br>
<p><?php echo htmlspecialchars(_l('contac_settings_cron_index')); ?></p>
<input type="text" class="form-control" disabled value="<?php echo htmlspecialchars(base_url()); ?>contactcenter/cron/index">
<br>
<p><?php echo htmlspecialchars(_l('contac_settings_cron_ia')); ?></p>
<input type="text" class="form-control" disabled value="<?php echo htmlspecialchars(base_url()); ?>contactcenter/cron/ia">
<br>
<p><?php echo htmlspecialchars(_l('contac_settings_cron_whats')); ?></p>
<input type="text" class="form-control" disabled value="<?php echo htmlspecialchars(base_url()); ?>contactcenter/cron/whats">

<hr />
<br>
<h4><?php echo htmlspecialchars(_l('contac_settings_webhook')); ?></h4>
<br>
<p><?php echo htmlspecialchars(_l('contac_settings_webhook_message')); ?></p>
<input type="text" class="form-control" disabled value="<?php echo htmlspecialchars(base_url()); ?>contactcenter/webhook">
<br>
<p><?php echo htmlspecialchars(_l('contac_settings_webhook_status')); ?></p>
<input type="text" class="form-control" disabled value="<?php echo htmlspecialchars(base_url()); ?>contactcenter/webhook/get_device_status">
<br>
<p><?php echo htmlspecialchars(_l('contac_settings_webhook_qrcode')); ?></p>
<input type="text" class="form-control" disabled value="<?php echo htmlspecialchars(base_url()); ?>contactcenter/webhook/qrcode">