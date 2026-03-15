<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="context-menu" class="context-menu list-group-flush">
    <ul>
        <li class="list-group-item edit edit_action" data-id="1" onclick="edit_msg_whats(this)"><i class="fa-solid fa-marker"></i> <?= _l("edit") ?></li>
        <li class="list-group-item delete" onclick="delete_msg_whats()"><i class="fa-solid fa-trash"></i></i> <?= _l("delete") ?></li>
        <li class="list-group-item j_reply_action" id=""><i class="fa-solid fa-reply-all"></i> <?= _l("contac_chat_msg_responder") ?></li>
    </ul>
</div>
<div id="context-menu-contact" class="context-menu list-group-flush" style="display: none;">
    <ul>
        <li class="list-group-item mark-read" data-marked-read="1"><i class="fa-solid fa-check"></i> <?= _l("chat_mark_as_read"); ?></li>
        <li class="list-group-item mark-unread" data-marked-read="0"><i class="fa-solid fa-envelope"></i> <?= _l("chat_mark_as_unread"); ?></li>
    </ul>
</div>

<input type="hidden" name="device_id" value="<?= $device->dev_id; ?>">
<div id="wrapper" theme="<?= $theme ?>">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s full-mobile ">

                    <div class="panel-body ">
                        <div class="chat-container ">
                            <div class="chat-container-header ">
                                <?php if (!is_mobile()) { ?>
                                    <div class="timer-chat-container">
                                        <h4 class="tw-mt-0 tw-mb-3 tw-font-semibold tw-text-lg"><?= _l("contac_chat_time_title"); ?></h4>
                                        <div class="time-ia">
                                            <?php echo form_open_multipart('', ['name' => 'timer-Ia']); ?>
                                            <input type="hidden" name="device_id" value="<?= $device->dev_id; ?>">

                                            <?php
                                            foreach (dias_semanas() as $num => $dia) {
                                                $timer = json_decode($device->timer_ia);
                                                $startValue = isset($timer->$num->start) ? $timer->$num->start : '';
                                                $endValue = isset($timer->$num->end) ? $timer->$num->end : '';
                                            ?>

                                                <div class="container-time">
                                                    <div class="card-time-chat">
                                                        <h4 class="tw-mt-0 tw-mb-3 tw-font-semibold tw-text-lg"><?= $dia; ?></h4>
                                                        <div>
                                                            <div class="form-group">
                                                                <label for="<?= $num; ?>" class="control-label"><?= _l("contac_chat_time_start"); ?></label>
                                                                <input type="time" name="start_<?= $num; ?>" id="<?= $num; ?>" value="<?= $startValue; ?>">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="form-group">
                                                                <label for="<?= $num; ?>" class="control-label"><?= _l("contac_chat_time_end"); ?></label>
                                                                <input type="time" name="end_<?= $num; ?>" id="<?= $num; ?>" value="<?= $endValue; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="container-time">
                                                <div class="card-time-chat">
                                                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> <?= _l("save"); ?></button>
                                                </div>
                                            </div>
                                            <?php echo form_close(); ?>
                                            <span class="text-danger"><?= _l("contac_chat_time_aviso"); ?></span>
                                            <hr class="hr-panel-separator" />
                                        </div>
                                    </div>

                                <?php } ?>
                                <div class="footer-bar" style="display: flex; flex-direction: column; gap: 10px;">
                                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                        <!-- Omni Pilot Progress Indicator -->
                                        <div id="omniPilotProgressContainer" class="bulk-send-progress-container" style="display: none; min-width: 200px; max-width: 300px; flex-shrink: 0; position: relative;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                                            <div style="display: flex; align-items: center;">
                                                <i class="fa fa-rocket" style="margin-right: 8px; color: var(--primary, #00e09b);"></i>
                                                <strong id="omniPilotProgressName"><?= _l("omni_pilot_progress"); ?></strong>
                                            </div>
                                            <button id="omniPilotStopBtn" class="btn btn-xs btn-danger" style="padding: 2px 8px; font-size: 11px; display: none;" title="<?= _l('omni_pilot_stop'); ?>">
                                                <i class="fa fa-stop"></i> <?= _l('omni_pilot_stop'); ?>
                                            </button>
                                        </div>
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                                            <span id="omniPilotProgressText" class="bulk-progress-text">
                                                <i id="omniPilotSpinner" class="fa fa-spinner fa-spin" style="margin-right: 6px; display: none; color: var(--primary, #00e09b); visibility: hidden;"></i>
                                                <span id="omniPilotCurrentPhase">-</span>
                                            </span>
                                        </div>
                                        <div class="progress progress-bar-mini" style="height: 8px; margin-bottom: 3px; background-color: rgba(255, 255, 255, 0.1); border-radius: 4px; overflow: hidden;">
                                            <div id="omniPilotProgressBar" class="progress-bar progress-bar-success progress-bar-striped active no-percent-text not-dynamic" 
                                                role="progressbar" 
                                                aria-valuenow="0" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100" 
                                                style="width: 0%; min-width: 2px; transition: width 0.3s ease;" 
                                                data-percent="0">
                                            </div>
                                        </div>
                                        <div style="margin-top: 4px;">
                                            <span class="bulk-progress-small" id="omniPilotGoalProgress"><?= _l("omni_pilot_goal"); ?>: 0/0</span>
                                        </div>
                                    </div>
                                    
                                        <!-- Campaign Queue Progress Indicator -->
                                        <div id="campaignQueueProgressContainer" class="bulk-send-progress-container" style="display: none; min-width: 200px; max-width: 300px; flex-shrink: 0;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                                            <div style="display: flex; align-items: center;">
                                                <i class="fa-solid fa-bullhorn" style="margin-right: 8px;"></i>
                                                <strong id="campaignQueueName"><?= _l("active_campaign"); ?></strong>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                                            <span id="campaignQueueText" class="bulk-progress-text">0 <?= _l("pending_messages"); ?></span>
                                        </div>
                                        <div class="progress progress-bar-mini" style="height: 8px; margin-bottom: 3px;">
                                            <div id="campaignQueueBar" class="progress-bar progress-bar-info progress-bar-striped active no-percent-text not-dynamic" 
                                                role="progressbar" 
                                                aria-valuenow="0" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100" 
                                                style="width: 100%;" 
                                                data-percent="100">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Follow-up Queue Progress Indicator -->
                                    <div id="followupQueueProgressContainer" class="bulk-send-progress-container" style="display: none; min-width: 200px; max-width: 300px; flex-shrink: 0;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                                            <div style="display: flex; align-items: center;">
                                                <i class="fa-solid fa-bell" style="margin-right: 8px;"></i>
                                                <strong id="followupQueueName"><?= _l("active_followup"); ?></strong>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                                            <span id="followupQueueText" class="bulk-progress-text">0 <?= _l("pending_leads"); ?></span>
                                        </div>
                                        <div class="progress progress-bar-mini" style="height: 8px; margin-bottom: 3px;">
                                            <div id="followupQueueBar" class="progress-bar progress-bar-warning progress-bar-striped active no-percent-text not-dynamic" 
                                                role="progressbar" 
                                                aria-valuenow="0" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100" 
                                                style="width: 100%;" 
                                                data-percent="100">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Bulk Send Progress Indicator -->
                                    <div id="bulkSendProgressContainer" class="bulk-send-progress-container" style="display: none; min-width: 200px; max-width: 300px; flex-shrink: 0;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                                            <div style="display: flex; align-items: center;">
                                                <i class="fa fa-spinner fa-spin" style="margin-right: 8px;"></i>
                                                <strong><?= _l("sending_media_to_multiple"); ?></strong>
                                            </div>
                                            <button type="button" class="btn btn-danger btn-xs" onclick="cancel_bulk_send()" style="padding: 2px 8px; font-size: 10px;" title="<?= _l("ads_analytics_cancel"); ?>">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                                            <span id="bulkProgressText" class="bulk-progress-text">0/0</span>
                                            <span id="bulkProgressPercent" class="bulk-progress-percent">0%</span>
                                        </div>
                                        <div class="progress progress-bar-mini" style="height: 8px; margin-bottom: 3px;">
                                            <div id="bulkProgressBar" class="progress-bar progress-bar-success progress-bar-striped active no-percent-text not-dynamic" 
                                                role="progressbar" 
                                                aria-valuenow="0" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100" 
                                                style="width: 0%;" 
                                                data-percent="0">
                                            </div>
                                        </div>
                                        <small class="bulk-progress-small"><?= _l("sending_one_per_minute"); ?></small>
                                    </div>
                                    
                                    <div class="input-filter-chat" style="flex: 1; display: flex; align-items: center; gap: 8px; flex-wrap: nowrap;">
                                        <!-- Status Filter Badge -->
                                        <div class="chat-filter-badge-wrapper" style="flex-shrink: 0;">
                                            <select class="form-control selectpicker chat-filter-select" id="statuLead" data-none-selected-text="<?= _l("contac_aviso_ecolha_status"); ?>" data-live-search="true" data-width="fit" data-size="8" data-style="btn-sm chat-filter-badge-select">
                                                <option></option>
                                                <?php foreach ($statuses as $status) { ?>
                                                    <option value="<?= $status['id']; ?>"><?= $status['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        
                                        <!-- Read/Unread Filter Badge -->
                                        <div class="chat-filter-badge-wrapper" style="flex-shrink: 0;">
                                            <select class="form-control selectpicker chat-filter-select" id="chatMarkedRead" data-width="fit" data-size="5" data-style="btn-sm chat-filter-badge-select">
                                                <option value=""><?= _l("all"); ?></option>
                                                <option value="0" <?= (isset($chat_marked_read_filter) && $chat_marked_read_filter == 0) ? 'selected' : ''; ?>><?= _l("chat_unread"); ?></option>
                                                <option value="1" <?= (isset($chat_marked_read_filter) && $chat_marked_read_filter == 1) ? 'selected' : ''; ?>><?= _l("chat_read"); ?></option>
                                            </select>
                                        </div>
                                        
                                        <?php if ($device->dev_type == 3) { ?>
                                            <!-- Assign Lead Filter Badge -->
                                            <div class="chat-filter-badge-wrapper" style="flex-shrink: 0;">
                                                <select class="form-control selectpicker chat-filter-select" id="assignLead" data-none-selected-text="<?= _l("contac_aviso_ecolha_user"); ?>" data-live-search="true" data-width="fit" data-size="8" data-style="btn-sm chat-filter-badge-select">
                                                    <option></option>
                                                    <?php foreach ($members as $assignLead) { ?>
                                                        <option value="<?= $assignLead['staffid']; ?>"><?= $assignLead['firstname'] . ' ' . $assignLead['lastname']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        <?php } ?>
                                        
                                        <!-- Search Input - moved after filters -->
                                        <div class="input-group" style="flex: 1; min-width: 200px;">
                                            <span class="input-group-addon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                            <input id="shearch" type="text" class="form-control" placeholder="<?= _l("contac_chat_search"); ?>">
                                        </div>

                                        <div class="input-group filter-contact">
                                            <div class="btn btn-primary btn-filter-chat" onclick="on_filter()">
                                                <i class="fa-solid fa-filter"></i>
                                            </div>
                                            <div class="btn btn-primary btn-filter-chat">
                                                <a href="<?= admin_url("contactcenter/chatall"); ?>"><i class="fa-solid fa-arrow-left"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                    <ul style="width: 100%; margin-top: 0;">
                                        <?php hooks()->do_action('contactcenter_buttom_chatsingle'); ?>
                                        <!-- Omni Pilot Icon -->
                                        <li class="btn btn-primary omni-pilot-icon <?php echo (isset($has_active_omni_pilot) && !$has_active_omni_pilot) ? 'omni-pilot-pulse' : ''; ?>" onclick="openOmniPilotWizard(<?= $device->dev_id; ?>)" data-toggle='tooltip' data-title='<?= _l("omni_pilot_title"); ?>'>
                                            <i class="fa fa-rocket"></i>
                                        </li>
                                        <!-- Shortcut Icons -->
                                        <a href="<?= admin_url("contactcenter/conversation_engine"); ?>">
                                            <li class="btn btn-primary" data-toggle='tooltip' data-title='<?= _l("contac_conversation_engine"); ?>'><i class="fa-solid fa-bullhorn"></i></li>
                                        </a>
                                        <a href="<?= admin_url("contactcenter/leads_engine"); ?>">
                                            <li class="btn btn-primary" data-toggle='tooltip' data-title='<?= _l("leads_engine_title"); ?>'><i class="fa-solid fa-bell"></i></li>
                                        </a>
                                        <?php if (is_admin()) { ?>
                                            <?php if ($device->assistant_ai_id) { ?>
                                                <a href="<?= admin_url("contactcenter/assistant_edit/{$device->assistant_ai_id}"); ?>">
                                                    <li class="btn btn-primary" data-toggle='tooltip' data-title='<?= _l("contac_editar"); ?> <?= _l("contact_assistant_ai_page"); ?>'><i class="fa-solid fa-brain"></i></li>
                                                </a>
                                            <?php } ?>
                                            <li class="btn btn-primary" onclick="edit_device(<?= $device->dev_id; ?>)" data-toggle='tooltip' data-title='<?= _l("contac_editar"); ?> <?= _l("contac_device"); ?>'><i class="fa-solid fa-mobile-screen-button"></i></li>
                                        <?php } ?>
                                        
                                        <li onclick="open_media_library()" class="btn btn-primary" data-toggle='tooltip' data-title='<?= _l("media_library"); ?>'><i class="fa-solid fa-music"></i></li>
                                        
                                        <li onclick="new_chat()" class="btn btn-primary" data-toggle='tooltip' data-title='<?= _l("contac_chat_new"); ?>'><i class="fa-solid fa-comment-medical"></i></li>
                                        <a href="<?= admin_url("contactcenter/msgspeed/{$device->dev_id}"); ?>">
                                            <li class="btn btn-primary" data-toggle='tooltip' data-title='<?= _l("contac_chat_msg_speed"); ?>'><i class="fa-solid fa-reply-all"></i></li>
                                        </a>

                                        <li class="btn btn-primary" id="checkBtn" data-toggle='tooltip' data-title='<?= _l("contac_only_answered"); ?>'><input id="msg_fromMe" type="checkbox" value="1"><i class="fa-solid fa-comment-dots"></i></li>

                                        <li class="btn btn-primary" data-toggle='tooltip' data-title='<?= _l("contac_chat_chat"); ?>' onclick="get_contact()"><i class="fa-solid fa-rotate j_close_new"></i></li>
                                        <li class="btn btn-primary" data-toggle='tooltip' data-title='<?= _l("contac_chat_time_tooltip"); ?>' onclick="timer_chat()"><i class="fa-regular fa-clock"></i></li>
                                        <li class="btn btn-primary" data-toggle='tooltip' data-title='<?= _l("contac_chat_desable_sound"); ?>' onclick="desable_sound(this)"><i class="fa-solid fa-volume-high"></i></li>
                                        <?php if (is_admin()) { ?>
                                            <li id="onAi" class="btn btn-primary <?= ($device->dev_openai ? "active-ai" : "off-ai") ?>" data-toggle='tooltip' data-title='<?= _l("contac_chat_ai"); ?>' onclick="open_ai()"><i class="fa-solid fa-robot"></i></li>
                                        <?php } ?>
                                        
                                        <a href="<?= admin_url("contactcenter/qrcode_single/{$device->dev_id}"); ?>">
                                            <li class="btn btn-primary" data-toggle='tooltip' data-title='<?= _l("contac_chat_qrcode"); ?>'><i class="fa-solid fa-qrcode"></i></li>
                                        </a>
                                          
                                        <a href="<?= admin_url("contactcenter/themes_change/{$device->dev_id}"); ?>">
                                            <li class="btn btn-primary" data-toggle='tooltip' data-title='<?= _l("contac_chat_theme"); ?>'><i class="fa-solid fa-palette"></i></li>
                                        </a>
                                    </ul>
                                </div>
                                <?php if (is_mobile() == false) { ?>
                                    <div class="header-chat-content" style="display: none;">
                                        <div class="header-chat-modern">
                                            <div class="header-chat-ai-status" onclick="toggle_lead_ai()" data-toggle="tooltip" data-title="<?= _l("contac_chat_ai"); ?>" style="cursor: pointer;">
                                                <div class="j_on_ai" data-id="">
                                                    <lord-icon src="https://cdn.lordicon.com/kcegqely.json" trigger="loop" colors="primary:#ffffff,secondary:#00e09b" style="width:45px;height:45px">
                                                    </lord-icon>
                                                </div>
                                                <div class="j_off_ai" data-id="">
                                                    <lord-icon src="https://cdn.lordicon.com/kcegqely.json" trigger="loop" colors="primary:#ffffff,secondary:#e83a30" style="width:45px;height:45px">
                                                    </lord-icon>
                                                </div>
                                            </div>
                                            <div class="header-chat-info">
                                                <div class="box-dados-chat-modern">
                                                    <h5 id="lead-name" class="lead-name-modern"></h5>
                                                    <span id="lead-phone" class="lead-phone-modern"></span>
                                            </div>
                                                <div class="progress-container-modern" id="progress"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>




                        <article class="box-chat destacar">

                            <section class="chat-aside">
                                <div class="chat-perfil" style="position: relative;">
                                    <i class="fa-solid fa-expand" id="destacar"></i>
                                    <div>
                                        <img src="<?= staff_profile_image_url($device->staffid) ?>">
                                    </div>
                                    <div>
                                        <?php if ($device->dev_type == 3) { ?>
                                            <span class="chat-perfil-span"> <?= _l("contac_phone_type_multiple"); ?></span>
                                        <?php } else { ?>
                                            <span class="chat-perfil-span"><?= ($device->staffid ? get_staff_full_name($device->staffid) : $device->dev_name) ?></span>
                                        <?php } ?>
                                    </div>
                                    <span class="chat-perfil-span" ><i class="fa-solid fa-robot"></i> <?= get_name_assistant($device->assistant_ai_id); ?></span>
                                    <span class="chat-perfil-span"><i class="fa-solid fa-mobile-screen"></i> <?= $device->dev_number ?></span>
                                    <h6 class="" data-toggle='tooltip' data-title='<?= _l("contac_whats_connection_last_status") ." - "._d($device->last_status) ; ?>'><?= label_status_device($device->status); ?></h6>
                                   
                                    <!-- <?php
                                    // Calcule a porcentagem
                                    $percent = ($device->totalPages > 0) ? ($device->currentPage / $device->totalPages) * 100 : 0;
                                    if ($device_server == 2) {
                                    ?>
                                        <div class="card-chatsingle-departaments <?= ($device->status_sinc == 1 && $device->status == "open" && $device->currentPage ? "" : "hidden") ?>" id="progrWhats">
                                            <div>
                                                <p><?= _l("contac_sincronizar_msg_open") ?>&nbsp</p>
                                                <p id="progressoWhats"><?= $device->currentPage . "/" . $device->totalPages ?></p>
                                            </div>
                                            <div class="progress tw-mb-2 tw-mt-2 progress-bar-mini">
                                                <div
                                                    class="progress-bar progress-bar-success progress-bar-striped active no-percent-text not-dynamic"
                                                    role="progressbar"
                                                    aria-valuenow="<?= round($percent, 2) ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100"
                                                    style="width: <?= round($percent, 2) ?>%;"
                                                    data-percent="<?= round($percent, 2) ?>">
                                                    <?= round($percent, 2) ?>%
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?> -->


                                    <?php if ($device->dev_type == 3) { ?>
                                        <div class='card-chatsingle-departaments'>
                                            <?php foreach (get_members_departments_staffid($device->staffid) as $index => $departaments) { ?>
                                                <h6 class="badge badge-info"><?= $departaments->name; ?></h6>
                                            <?php  } ?>
                                        </div>
                                    <?php } ?>

                                    <div class="new-chatall">
                                        <i class="fa-solid fa-mobile-screen"></i>
                                        <input type="text" id="whatsappid" name='phonenumber' autocomplete="off" value='<?= $hook_number ?>' />
                                        <i class="fa-brands fa-whatsapp w-none"></i>
                                        <i class="fa-solid fa-ban w-none"></i>
                                    </div>

                                </div>
                                <div class="load-get-msg w-none">
                                    <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>
                                    Loading...
                                </div>
                                <section>

                                    <input type="hidden" name='token' value='<?= $devicetoken ?>' />
                                    <input type="hidden" name='group_type' value='' />

                                    <table id='contaChat'>
                                        <thead style="display: none">
                                            <tr>
                                                <th>Name</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            echo monta_html_contact_banco($LabelContacts);
                                            ?>
                                        </tbody>
                                    </table>
                                    <div class="row" id="search_chat"></div>
                                </section>
                            </section>

                            <section class="chat-body">
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
                                <!-- section para mgs rapida -->
                                <div id="search_search_msgspeed"></div>

                                <div class="chat-card-reply">
                                    <div class='reply_card' id='reply_{$msg->reply_id}'>
                                        <div>
                                            <span id="reply_title"></span>
                                            <p id="reply_msg"></p>
                                        </div>
                                        <div class='reply_close'>
                                            <i class='btn btn-default btn-icon' onclick='cancel_reply()'><i class='fa-solid fa-xmark'></i></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="chat-text-area">
                                    <?php echo form_open_multipart("", ["name" => "sendMsg"]) ?>
                                    <input type="hidden" id="staffid_transfer" name="staffid" value="<?= $device->staffid ?>" />
                                    <input type="hidden" name="action" value="text" />
                                    <input type="hidden" name="reply_id" value="" />
                                    <input type="hidden" name="edit_id" value="" />
                                    <div class="dropup">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa-solid fa-paperclip"></i>
                                        </button>
                                        <ul class="dropdown-menu attachment_chat">
                                            <li onclick="upload_media_chat()"><i class="fa-regular fa-image"></i> <span><?= _l("contac_chat_img"); ?></span></li>
                                            <li onclick="upload_document_chat()"><i class="fa-regular fa-file-pdf"></i> <span><?= _l("contac_chat_pdf"); ?></span></li>
                                            <li onclick="upload_document_zip_chat()"><i class="fa-regular fa-file-zipper"></i> <span><?= _l("contac_chat_zip"); ?></span></li>
                                            <li onclick="upload_document_xlsx_chat()"><i class="fa-regular fa-file-excel"></i> <span><?= _l("contac_chat_xlsx"); ?></span></li>
                                            <li onclick="upload_document_docx_chat()"><i class="fa-regular fa-file-word"></i> <span><?= _l("contac_chat_docx"); ?></span></li>
                                        </ul>
                                    </div>

                                    <textarea id="textarea-chat" name='msg' rows="1" placeholder="<?= _l("chat_input_placeholder") ?>"></textarea>
                                    <div class="btn-submit-chat">
                                        <button id="btn_submit" class="hidden"><i class="fa-regular fa-paper-plane spinner-load-send"></i><i class="fa-solid fa-spinner spinner-load-in"></i></button>
                                        <button id="startRecording"><i class="fa-solid fa-microphone-lines"></i></button>
                                        <button id="stopRecording" style="display: none;"><i class="fa-solid fa-microphone-lines-slash"></i></button>
                                    </div>
                                    <?php echo form_close() ?>
                                </div>

                                <!-- section de arquivos -->
                                <section class="chat-files">
                                    <article class="chat-files-list">
                                        <ul class="nav nav-tabs">
                                            <li role="presentation" class="active"><a href="#chat-files-list-media" data-toggle="tab"><?= _l("contac_chat_file_media"); ?></a></li>
                                            <li role="presentation" class=""><a href="#chat-files-list-docs" data-toggle="tab"></i><?= _l("contac_chat_file_docs"); ?></a></li>
                                        </ul>

                                        <div class="tab-content" style="height: 100%;">
                                            <div role="tabpanel" class="tab-pane active" id="chat-files-list-media">
                                                <article class="chat-files-list-content ">

                                                </article>
                                            </div>
                                            <div role="tabpanel" class="tab-pane" id="chat-files-list-docs">
                                                <article class="chat-files-list-content">

                                                </article>
                                            </div>
                                        </div>
                                        <i class="fa-solid fa-xmark chat-files-close"></i>
                                    </article>
                                </section>

                                <i class="fa-solid fa-file-image chat-files-open"></i>
                            </section>
                            <section class="chat-media">
                                <div>
                                    <div class="header-chat-media">
                                        <span class="close-chat-media"><i class="fa-solid fa-xmark"></i></span>
                                    </div>
                                    <div class="preview-chat-media">
                                        <img id="veiwMedia">
                                        <video id="veiwMediaVideo" controls></video>
                                        <span id="veiwMediaPdf"><i class="fa-regular fa-file-pdf"></i></span>
                                        <span id="veiwMediaZip"><i class="fa-regular fa-file-zipper"></i></span>
                                        <span id="veiwMediaXlsx"><i class="fa-regular fa-file-excel"></i></span>
                                        <span id="veiwMediaDocx"><i class="fa-regular fa-file-word"></i></span>
                                    </div>
                                    <div id="veiwFileName"></div>
                                    <?php echo form_open_multipart("", ["name" => "sendMedia"]) ?>
                                    <input type="hidden" id="staffid_transfer" name="staffid" value="<?= $device->staffid ?>" />
                                    <input type="hidden" name="action" value="image" />
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


<?php if ($device->api_local == 1) { ?>
   <!-- Include script api local -->
    <?php require('modules/contactcenter/views/chat_script_local.php'); ?>
<?php } ?>

<script type="text/javascript">

    var paginadorChat = null;
    var paginadorToken = null;
    let ignoreNextScrollTop = false;

    <?php if ($hook_number != "") { ?>
        // abrir chat automatico
        get_message_contact("<?= $hook_number ?>", "<?= $device->dev_token ?>", 0, staffid_transfer = null);
        $(".contact_" + <?= $hook_number ?> + " .chat-contato").addClass('active-contact');
    <?php } ?>

    <?php if ($device->api_local != 1) { ?>
        // conexão websocket
        get_websocket(<?= $dadoServe ?>); 
    <?php } ?>

    // Ensure spinner is hidden on page load
    $(".spinner-load-chat").hide();


    let ajaxRequest = null;


    $(document).ready(function() {
        // Resume bulk send on page load if active
        resume_bulk_send();
        
        // Load and start campaign queue status monitoring
        load_campaign_queue_status();
        // Refresh every 10 seconds
        setInterval(load_campaign_queue_status, 10000);
        
        // Load and start follow-up queue status monitoring
        load_followup_queue_status();
        // Refresh every 10 seconds
        setInterval(load_followup_queue_status, 10000);
        
        // Omni Pilot translations (for stop button and other features)
        if (typeof window.omniPilotTranslations === 'undefined') {
            window.omniPilotTranslations = {};
        }
        // Add stop-related translations
        window.omniPilotTranslations.omni_pilot_stop_confirm = '<?php echo addslashes(_l('omni_pilot_stop_confirm')); ?>';
        window.omniPilotTranslations.omni_pilot_stopping = '<?php echo addslashes(_l('omni_pilot_stopping')); ?>';
        window.omniPilotTranslations.omni_pilot_stopped_successfully = '<?php echo addslashes(_l('omni_pilot_stopped_successfully')); ?>';
        window.omniPilotTranslations.omni_pilot_stop_failed = '<?php echo addslashes(_l('omni_pilot_stop_failed')); ?>';
        window.omniPilotTranslations.omni_pilot_error_stopping = '<?php echo addslashes(_l('omni_pilot_error_stopping')); ?>';
        window.omniPilotTranslations.omni_pilot_no_active_session = '<?php echo addslashes(_l('omni_pilot_no_active_session')); ?>';
        window.omniPilotTranslations.omni_pilot_phase_cancelled = '<?php echo addslashes(_l('omni_pilot_phase_cancelled')); ?>';
        window.omniPilotTranslations.omni_pilot_phase = '<?php echo addslashes(_l('omni_pilot_phase')); ?>';
        window.omniPilotTranslations.omni_pilot_page_expired = '<?php echo addslashes(_l('omni_pilot_page_expired')); ?>';
        window.omniPilotTranslations.omni_pilot_phase_searching_leads = '<?php echo addslashes(_l('omni_pilot_phase_searching_leads')); ?>';
        window.omniPilotTranslations.omni_pilot_completed = '<?php echo addslashes(_l('omni_pilot_completed')); ?>';
        
        // Restore Omni Pilot progress on page load
        restoreOmniPilotProgress();
        
        // Stop button handler
        $(document).on('click', '#omniPilotStopBtn', function() {
            stopOmniPilot();
        });

        $("#shearch").on("keyup", function() {
            var value = $(this).val().toLowerCase(); // Obtém o valor do input e converte para minúsculas
            $("#contaChat tr").filter(function() {
                // Mostra ou oculta as TRs com base se o texto delas contém o termo de pesquisa
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        $('.chat-files-open').css('display', 'none');
        $('.chat-files-open').on('click', function() {
            $('.chat-files').css('display', 'flex');
        })

        $('.chat-files-close').on('click', function() {
            $('.chat-files').hide();
        })
        //quando clicar fora chat-files-list fecha chat-files
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.chat-files-list').length && !$(event.target).closest('.chat-files-open').length) {
                $('.chat-files').hide();
            }
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

        });


        $('#textarea-chat').on('input', function() {
            var value = $(this).val();
            if (value.trim() !== '') {
                $("#btn_submit").removeClass("hidden");
                $("#startRecording").addClass("hidden");
                $("#btn_submit").prop("disabled", false);
                $("#btn_submit").css("background", "var(--primary)");

            } else {
                $("#btn_submit").addClass("hidden");
                $("#startRecording").removeClass("hidden");
                $("#btn_submit").prop("disabled", true);
                $("#btn_submit").css("background", "var(--gray)");
            }
        });


        /**
         * Filtro de pesquisa
         */
        var bloqueiaScroll = false;
        $('#shearch').on('keyup', function() {
            var searchTerm = $(this).val();

            var tokenUser = $("input[name='token']").val();
            //console.log('Texto pesquisado:', searchTerm);

            if (searchTerm.trim() !== '' && searchTerm.length > 2) {


                if (ajaxRequest !== null) {
                    ajaxRequest.abort();
                }

                ajaxRequest = $.ajax({
                    url: site_url + 'admin/contactcenter/ajax_get_search_chat',
                    data: {
                        search: searchTerm,
                        token: tokenUser
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


  
    function get_message_contact_search(id, token, group = 0, msg_id, staffid_transfer = null) {
        
        $("input[name='phonenumber']").val(id);
        $("input[name='group_type']").val(group);

        if (staffid_transfer != null) {
            $("#staffid_transfer").val(staffid_transfer);
        } else {
            $("#staffid_transfer").val(<?= $device->staffid ?>);
        }

        if (ajaxRequest !== null) {
            ajaxRequest.abort();
        }

        ajaxRequest = $.ajax({
            url: site_url + 'admin/contactcenter/ajax_get_messages_chat',
            data: {
                token: token,
                phonenumber: id,
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
                    $(".spinner-load-chat").fadeOut();
                    $('#retorno').html(data.retorno);
                    // Remove no-results div only if there are actual messages
                    setTimeout(function() {
                        // Check if there are actual message bubbles (not just no-results)
                        // Look for chat-my divs or chat-others divs that are NOT no-results
                        var hasMessages = $('#retorno .chat-my').length > 0 || 
                                         $('#retorno .chat-others div:not(.no-results)').length > 0;
                        
                        // Only remove no-results if we have actual messages
                        if (hasMessages) {
                            $('#retorno .no-results').remove();
                            $('#retorno .chat-others .no-results').remove();
                            $('#retorno .chat-others > div.no-results').remove();
                        }
                    }, 100);
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

                } else {
                    // Hide spinner even if no messages returned
                    $(".spinner-load-chat").fadeOut();
                }
                if (data.phonenumber) {
                    $('.header-chat-modern, .header-chat-content').css("display", "flex");
                    $('#lead-name').html(data.name);
                    $('#lead-phone').html(data.phonenumber);
                    $(".j_on_ai").attr("data-id", data.id);
                    $(".j_off_ai").attr("data-id", data.id);
                } else {
                    $('.header-chat-modern, .header-chat-content').css("display", "none");
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


            },
            error: function() {
                // Hide spinner on error
                $("#load").css("display", "none");
                $(".spinner-load-chat").fadeOut();
            }
        });
    }



    /**
     * busca as message via ajax
     * @param {type} id
     * @param {type} token
     * @returns {undefined}
     */
    function get_message_contact(id, token, group = 0, staffid_transfer = null) {
        cancel_reply();
        $('.chat-files-open').css('display', 'flex');


        $('.chat-body section').off('scroll.chatScroll');
        $("input[name='phonenumber']").val(id);
        $("input[name='group_type']").val(group);
        paginadorChat = null;
        paginadorToken = token;
        
        // Reset submit button state and clear textarea when switching contacts
        // Clear the textarea (both regular and emojioneArea)
        if ($("#textarea-chat")[0] && $("#textarea-chat")[0].emojioneArea) {
            $("#textarea-chat")[0].emojioneArea.setText("");
        } else {
            $("#textarea-chat").val("");
        }
        $("textarea[name='msg']").val("");
        
        // Reset button to default state (hidden, disabled, showing microphone)
        $("#btn_submit").addClass("hidden");
        $("#btn_submit").prop("disabled", true);
        $("#btn_submit").css("background", "var(--gray)");
        $("#startRecording").removeClass("hidden");


        if (staffid_transfer != null) {
            $("#staffid_transfer").val(staffid_transfer);
        } else {
            $("#staffid_transfer").val(<?= $device->staffid ?>);
        }

        //view mobile
        if ($(window).width() < 661) {
            $(".chat-body").css("display", "block");
            $(".chat-body").addClass('full-mobile-chat');
            $(".chat-body").css("z-index", "100");
            $(".chat-body").css("background", "#000");
        }
        get_file_contact(token, id);

        $.ajax({
            url: site_url + 'admin/contactcenter/ajax_get_messages_chat',
            data: {
                token: token,
                phonenumber: id,
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
                    $(".spinner-load-chat").fadeOut();
                    $('#retorno').html(data.retorno);
                    // Remove no-results div only if there are actual messages
                    setTimeout(function() {
                        // Check if there are actual message bubbles (not just no-results)
                        // Look for chat-my divs or chat-others divs that are NOT no-results
                        var hasMessages = $('#retorno .chat-my').length > 0 || 
                                         $('#retorno .chat-others div:not(.no-results)').length > 0;
                        
                        // Only remove no-results if we have actual messages
                        if (hasMessages) {
                            $('#retorno .no-results').remove();
                            $('#retorno .chat-others .no-results').remove();
                            $('#retorno .chat-others > div.no-results').remove();
                        }
                    }, 100);

                    var retornoDiv = document.getElementById('retorno');
                    retornoDiv.scrollTop = retornoDiv.scrollHeight;

                    setTimeout(function() {
                        attachScrollListener(); // reanexa scroll
                    }, 100);



                    // tira o total de visualizações do chat
                    var row = $('tr[data-id="' + id + '"]');
                    row.find(".badge").text("");
                    
                    // Mark chat as read when opened
                    var contactId = row.attr('data-contact-id') || row.data('contact-id');
                    var phoneNumber = id; // The id parameter is the phone number
                    var chatToken = token; // The token parameter
                    
                    // Ensure the row has the token attribute so mark_chat_read_unread can find it
                    if (chatToken && !row.attr('data-token')) {
                        row.attr('data-token', chatToken);
                    }
                    
                    // Only mark as read if not already marked as read
                    if (!row.hasClass('chat-marked-read')) {
                        if (contactId && contactId != '0' && contactId != 0) {
                            // Mark as read (marked_read = 1) - pass row element, contactId, and marked_read
                            mark_chat_read_unread(row[0], contactId, 1);
                        } else if (phoneNumber && chatToken) {
                            // If no contact ID, try to mark by phone number and token
                            // The function will extract phoneNumber and token from the row element
                            mark_chat_read_unread(row[0], null, 1);
                        }
                    }
                } else {
                    // Hide spinner even if no messages returned
                    $(".spinner-load-chat").fadeOut();
                }
                if (data.paginadorChat) {
                    paginadorChat = data.paginadorChat;
                }
                if (data.phonenumber) {
                    $('.header-chat-modern, .header-chat-content').css("display", "flex");
                    $('#lead-name').html(data.name);
                    $('#lead-phone').html(data.phonenumber);
                    $(".j_on_ai").attr("data-id", data.id);
                    $(".j_off_ai").attr("data-id", data.id);
                } else {
                    $('.header-chat-modern, .header-chat-content').css("display", "none");
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


            },
            error: function() {
                // Hide spinner on error
                $("#load").css("display", "none");
                $(".spinner-load-chat").fadeOut();
            }
        });
    }


    /**
     * click na barra de progresso para transferir o contato
     */
    $(document).on('click', '#progress .step', function(event) {
        var token = $(this).attr("data-token");
        var phonenunber = $(this).attr("data-id");
        if (token) {
            get_message_contact(phonenunber, token, 0, staffid_transfer = null)
        }

    });

    /**
     * Atualiza o contato quando troca o status
     */
    $(document).ready(function() {
        // Initialize selectpickers for badge filters
        if ($('#statuLead').length) {
            $('#statuLead').selectpicker();
        }
        if ($('#chatMarkedRead').length) {
            $('#chatMarkedRead').selectpicker();
        }
        if ($('#assignLead').length) {
            $('#assignLead').selectpicker();
        }
        
        // Close other dropdowns when one opens
        $(document).on('click', '.chat-filter-badge-wrapper .bootstrap-select .dropdown-toggle', function(e) {
            var $currentSelect = $(this).closest('.bootstrap-select').prev('.chat-filter-select');
            $('.chat-filter-select').not($currentSelect).each(function() {
                var $select = $(this);
                var $picker = $select.next('.bootstrap-select');
                if ($picker.length && $picker.hasClass('open')) {
                    $select.selectpicker('toggle');
                }
            });
        });
        
        // Keep original handlers for compatibility
        $("#statuLead").change(function() {
            get_contact();
        });

        $("#chatMarkedRead").change(function() {
            get_contact();
        });

        $("#assignLead").change(function() {
            get_contact();
        });
        
        // Update badge text on initial load
        if ($('#chatMarkedRead').val() !== '') {
            var selectedReadOption = $('#chatMarkedRead option:selected').text();
            $('#readFilterText').text(selectedReadOption);
        }
    });

    /**
     * atualiza o contato quando tem evento 
     * @returns {undefined}
     */
    function get_contact() {
        var tokenUser = <?= $device->staffid ?>;
        cancel_reply();
        // Esconde a seção de arquivos
        $('.chat-files-open').css('display', 'none');
        // limpa o retorno
        $('#retorno').html("");
        // limpa o header chat
        $('.header-chat').css("display", "none");
        // limpa o campo de pesquisa
        $('#shearch').val("");
        // limpa o campo token
        $("input[name='phonenumber']").val("");


        // pego o valor do input msg_fromMe
        var msg_fromMe = $("#msg_fromMe").is(":checked") ? 0 : null;

        var statuLead = $("#statuLead").val();
        if (statuLead) {
            statuLead = statuLead;
        } else {
            statuLead = null;
        }

        var assignLead = $("#assignLead").val();
        if (assignLead) {
            assignLead = assignLead;
        } else {
            assignLead = null;
        }

        var chatMarkedRead = $("#chatMarkedRead").val();
        if (chatMarkedRead !== "") {
            chatMarkedRead = chatMarkedRead;
        } else {
            chatMarkedRead = null;
        }

        if (ajaxRequest !== null) {
            ajaxRequest.abort();
        }

        ajaxRequest = $.ajax({
            url: site_url + 'admin/contactcenter/ajax_get_contact_chat',
            data: {
                token: tokenUser,
                msg_fromMe: msg_fromMe,
                statuLead: statuLead,
                assignLead: assignLead,
                chat_marked_read: chatMarkedRead
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
                    // Atualiza o conteúdo da tabela                 
                    $('#contaChat tbody').append(data.retorno);
                    
                    // Debug: Check contact IDs
                    $('#contaChat tbody tr[data-contact-id]').each(function() {
                        var contactId = $(this).data('contact-id') || $(this).attr('data-contact-id');
                        if (!contactId || contactId == 0 || contactId == '0') {
                            console.warn('Contact row found without valid contact ID:', $(this).attr('data-id'));
                        }
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

                } else {
                    $('#contaChat tbody').html("<tr><td>Not Result</td></tr>");
                }
                $(".load-get-msg").fadeOut();
            }

        });
    }

    function new_chat() {
        // Open modal instead of showing the old input
        $('#modalNewChat').modal('show');
        $('#newChatSearchLead').val('');
        $('#newChatSearchClient').val('');
        $('#newChatPhoneDirect').val('');
        $('#newChatMessage').val('');
        $('#newChatResultsLead').html('');
        $('#newChatResultsClient').html('');
        $('#newChatType').val('lead'); // Default to lead
        $('#newChatSelectedMedia').hide();
        $('#newChatMediaFile').val('');
        $('#newChatMediaId').val('');
        $('#newChatMediaPath').val('');
        $('#newChatMediaType').val('');
        
        // Reset standard message buttons
        $('.standard-msg-btn').removeClass('btn-primary').addClass('btn-default');
        
        // Reset tabs
        $('#newChatTabLead').parent().addClass('active');
        $('#newChatTabClient').parent().removeClass('active');
        $('#newChatTabLeadContent').addClass('active in');
        $('#newChatTabClientContent').removeClass('active in');
        
        setTimeout(function() {
            $('#newChatSearchLead').focus();
        }, 500);
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
                    phonenumber: dataPhonenumber,
                    token: dataToken
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




    /**
     * Filtrar
     */
    $('#checkBtn').on('click', function() {
        var checkbox = $('#msg_fromMe');
        var checked = checkbox.prop('checked', !checkbox.prop('checked'));
        if (checkbox.prop('checked')) {
            $(this).css("background", "var(--primary)");
        } else {
            $(this).css("background", "#272727");
        }
        get_contact();
    });

    function on_filter() {
        $(".footer-bar ul").slideToggle();

    }

    $(".btn-back-chat").click(function() {
        $(".chat-body").removeClass('full-mobile-chat');
    });




    /**
     * faz a pagina carregar mais dados contact
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

                    var msg_fromMe = $("#msg_fromMe").is(":checked") ? 0 : null;

                    var statuLead = $("#statuLead").val();
                    if (statuLead) {
                        statuLead = statuLead;
                    } else {
                        statuLead = null;
                    }

                    var assignLead = $("#assignLead").val();
                    if (assignLead) {
                        assignLead = assignLead;
                    } else {
                        assignLead = null;
                    }


                    $.ajax({
                        url: site_url + "admin/contactcenter/ajax_get_contact_chat_paginador",
                        dataType: 'json',
                        method: 'POST',
                        data: {
                            staff: <?= $device->staffid ?>,
                            msg_fromMe: msg_fromMe,
                            statuLead: statuLead,
                            assignLead: assignLead,
                            chat_marked_read: chatMarkedRead
                        },
                        beforeSend: function() {
                            $(".load-get-msg").fadeIn();
                        },
                        success: function(data) {
                            if (data.retorno) {
                                $('#contaChat tbody').append(data.retorno);
                            }
                            isLoading = false;
                            $(".load-get-msg").fadeOut();
                        },
                        error: function() {
                            $(".load-get-msg").fadeOut();
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
    function attachScrollListener() {
        $(document).ready(function() {
            var isLoading = false;

            // Detecta o scroll da seção .chat-body
            $('.chat-body section').off('scroll.chatScroll').on('scroll.chatScroll', function() {
                var $this = $(this);

                if (ignoreNextScrollTop) return;

                // Verifica se o usuário chegou ao topo da div
                if ($this.scrollTop() === 0 && !isLoading) {
                    isLoading = true;

                    // Armazena a altura da div antes do prepend
                    var oldHeight = $this[0].scrollHeight;

                    var isGroup = $("input[name='group_type']").val();
                    var phonenumber = $("input[name='phonenumber']").val();
                    $.ajax({
                        url: site_url + "admin/contactcenter/ajax_get_chat_paginador",
                        dataType: 'json',
                        method: 'POST',
                        data: {
                            phonenumber: phonenumber,
                            group: isGroup,
                            token: paginadorToken,
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
                                // Remove no-results div only if there are actual messages
                                setTimeout(function() {
                                    // Check if there are actual message bubbles (not just no-results)
                                    var hasMessages = $('#retorno .chat-my').length > 0 || 
                                                     $('#retorno .chat-others div:not(.no-results)').length > 0;
                                    
                                    // Only remove no-results if we have actual messages
                                    if (hasMessages) {
                                        $('#retorno .no-results').remove();
                                        $('#retorno .chat-others .no-results').remove();
                                        $('#retorno .chat-others > div.no-results').remove();
                                    }
                                }, 100);

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
    }







    // Função para abrir o reply desktop
    $(document).on('dblclick', '.j_reply', function() {
        var id = $(this).attr("id");
        var text = $("#" + id + " .msg_content").text();
        var name = $(this).attr("data-name");
        var image = $("#" + id + " .reply_image").attr("src");

        //limita a quantidade de catacteres do texto 
        if (text.length > 50) {
            text = text.substring(0, 50) + "...";
        }
        if (image) {
            text = "<img src='" + image + "' style='width: 50px; height: 50px; object-fit: cover; border-radius: 50%; margin-right: 10px;'>" + text;
        }

        $("input[name='reply_id']").val(id);
        $("#reply_title").text(name);
        $("#reply_msg").html(text);
        $(".chat-card-reply").css("display", "block");
    });


    let lastTap = 0;

    function j_reply(element) {
        var id = $(element).attr("id");
        if (!id) {
            id = $(element).attr("data-id");
        }

        var text = $("#" + id + " .msg_content").text();
        var image = $("#" + id + " .reply_image").attr("src");
        var name = $(element).attr("data-name");

        // Limita a quantidade de caracteres do texto
        if (text.length > 50) {
            text = text.substring(0, 50) + "...";
        }

        if (image) {
            text = "<img src='" + image + "' style='width: 50px; height: 50px; object-fit: cover; border-radius: 50%; margin-right: 10px;'>" + text;
        }

        $("input[name='reply_id']").val(id);
        $("#reply_title").text(name);
        $("#reply_msg").html(text);
        $(".chat-card-reply").css("display", "block");
    }

    // Detecta duplo clique/touch na classe .j_reply
    $(document).on('dblclick touchstart', '.j_reply', function(event) {
        event.preventDefault(); // Impede o zoom no mobile

        let now = new Date().getTime();
        let timeSince = now - lastTap;

        if (timeSince < 300 && timeSince > 0) { // Se for um toque duplo
            j_reply(this);
        }

        lastTap = now; // Atualiza o tempo do último toque
    });

    // Adiciona a função para um clique em um <li>
    $(document).on('click', 'li.j_reply_action', function() {
        j_reply(this);
    });



    $(document).on('click', '.reply_card', function() {
        var msgId = $(this).attr("data-id");
        var targetElement = document.getElementById(msgId);

        if (targetElement) {
            $('#retorno').animate({
                scrollTop: $(targetElement).offset().top - $('#retorno').offset().top + $('#retorno').scrollTop()
            }, 1000);

            // Adiciona a classe para piscar a borda após a rolagem
            $(targetElement).addClass('blink-border');

            // Remove a classe após 3 segundos (ou o tempo que desejar)
            setTimeout(function() {
                $(targetElement).removeClass('blink-border');
            }, 3000);
        }
    });

    function cancel_reply() {
        $("#reply_title").text("");
        $("#reply_msg").text("");
        $("input[name='reply_id']").val("");
        $("input[name='edit_id']").val("");
        $("#textarea-chat").html("");
        $(".chat-card-reply").css("display", "none");
    }


    var dataid = null;
    $(document).ready(function() {
        // abre o menu de contexto com o clique direito
        $(document).on('contextmenu', '.action-my', function(e) {
            e.stopPropagation(); // Impede múltiplos disparos
            e.preventDefault(); // Impede o menu de contexto padrão do navegador
            menu_contexto(e);
        });



        /**
         * Long Press para dispositivos móveis menu de contexto
         */
        let touchTimer;
        $(document).on('touchstart', '.action-my', function(e) {
            e.stopPropagation();

            touchTimer = setTimeout(function() {
                menu_contexto(e);
            }, 600); // 600ms é um bom tempo para considerar long press
        });

        $(document).on('touchend touchcancel', '.action-my', function() {
            clearTimeout(touchTimer); // Cancela se o dedo for retirado antes do tempo
        });

        /**
         * Menu de contexto
         */
        function menu_contexto(e) {
            var action = null;

            // Usa closest para garantir que o elemento correto seja selecionado
            var targetElement = $(e.target).closest('.action-my')[0]; // Encontra o elemento com a classe 'action-my'


            // Obtém os atributos do elemento correto
            if (targetElement) {
                dataid = targetElement.getAttribute('id');
                action = targetElement.getAttribute('data-action');
                var dataHora = targetElement.getAttribute('data-hora');
            }


            var contextMenu = document.getElementById('context-menu');
            var editOption = document.querySelector('.context-menu .edit');
            var deleteOption = document.querySelector('.context-menu .delete');

            // Obtém as coordenadas do clique (corrigido)
            var clickX = e.pageX;
            var clickY = e.pageY;

            // Define a posição do menu
            contextMenu.style.top = clickY + 'px';
            contextMenu.style.left = clickX + 'px';

            // Verifica se o elemento da mensagem tem a classe 'deleted_mgs_chat'
            var msgElement = document.querySelector('.msg_' + dataid);
            if (msgElement && msgElement.classList.contains('deleted_mgs_chat')) {
                dataid = null;
                contextMenu.style.display = 'none';
                return;
            }

            //coloca o id na li .j_reply_action para resposta rapida
            $(".j_reply_action").attr("data-id", dataid);
            $(".edit_action").attr("data-id", dataid);


            // **Verifica se a mensagem tem mais de 5 minutos**
            if (dataHora) {
                // Converte o formato "dd/mm/yyyy hh:mm:ss" para "yyyy-mm-dd hh:mm:ss"
                var parts = dataHora.split(' '); // Separa data e hora
                var dateParts = parts[0].split('/'); // Separa a data
                var timeParts = parts[1].split(':'); // Separa a hora

                var formattedDate = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]} ${timeParts[0]}:${timeParts[1]}:${timeParts[2] || "00"}`;

                // Converte para objeto Date
                var dataMensagem = new Date(formattedDate);
                var agora = new Date();
                var diffMinutos = (agora - dataMensagem) / 60000; // Diferença em minutos

                if (diffMinutos < 5) {
                    // Exibe as opções conforme o 'action'
                    if (action == 1) {
                        editOption.style.display = 'none';
                    } else {
                        editOption.style.display = 'block';
                        deleteOption.style.display = 'block';
                    }
                } else {
                    editOption.style.display = 'none';
                    deleteOption.style.display = 'none';
                }
            }


            contextMenu.style.display = 'block';
        }

        // Fechar o menu de contexto se clicar em outro lugar
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#context-menu').length) {
                document.getElementById('context-menu').style.display = 'none';
            }
        });



    });




    function delete_msg_whats() {
        var userConfirm = confirm('<?= _l('contac_aviso_deleted') ?>');
        if (userConfirm) {
            $.ajax({
                url: site_url + 'admin/contactcenter/ajax_delete_msg_whats',
                data: {
                    msg_id: dataid
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if (data.retorno) {
                        $(".msg_" + dataid).addClass('deleted_mgs_chat');
                        $("#" + dataid + " .box-chat-img").addClass('deleted_mgs_chat');
                        $(".get_" + dataid + " .icon-media").addClass('deleted_mgs_chat');

                        //remove o atributo onclick
                        $(".get_" + dataid + " .icon-media i").removeAttr("onclick");
                    }
                }
            });
        }
    }

    function edit_msg_whats(element) {
        var id = $(element).attr("data-id");
        var text = $("#" + id + " .msg_content").text();
        //coloca o texto no emojioneArea

        // Coloca o texto no emojioneArea corretamente
        if ($("#textarea-chat")[0].emojioneArea) {
            $("#textarea-chat")[0].emojioneArea.setText(text);
        }

        $("input[name='edit_id']").val(id);
        $("#reply_msg").html(text);
        $("#textarea-chat").val(text);
        $(".chat-card-reply").css("display", "block");

        if (text) {
            $("#btn_submit").removeClass("hidden");
            $("#startRecording").addClass("hidden");
            $("#btn_submit").prop("disabled", false);
            $("#btn_submit").css("background", "var(--primary)");
        }
    }

    /**
     * emojioneArea
     */
    $(document).ready(function() {
        var emojioneAreaInstance = $("#textarea-chat").emojioneArea({
            pickerPosition: "top",
            tonesStyle: "bullet",
            events: {
                keydown: function(editor, event) {
                    if (event.key === "Enter" && !event.shiftKey) {
                        event.preventDefault();

                        var value = emojioneAreaInstance[0].emojioneArea.getText(); // Captura o texto sem formatação
                        $("textarea[name='msg']").val(value);


                        // Apenas clique manualmente se o botão estiver visível e habilitado
                        if (!$("#btn_submit").hasClass("hidden") && !$("#btn_submit").prop("disabled")) {
                            $("#btn_submit").trigger('click');
                        }
                    }
                    if (event.shiftKey && event.key !== "Shift") {
                        event.stopPropagation(); // Impede a propagação do evento
                    }
                },
                keyup: function(editor, event) {
                    var value = emojioneAreaInstance[0].emojioneArea.getText(); // Obtém o texto sem formatação                   

                    contactcenter_msg_speed(value.trim());

                    if (value.trim() !== '') {
                        $("#btn_submit").removeClass("hidden");
                        $("#startRecording").addClass("hidden");
                        $("#btn_submit").prop("disabled", false);
                        $("#btn_submit").css("background", "var(--primary)");
                    } else {
                        $("#btn_submit").addClass("hidden");
                        $("#startRecording").removeClass("hidden");
                        $("#btn_submit").css("background", "var(--gray)");
                    }
                }
            }
        });


        /**
         * sistema de copiar e colar input
         */
        setTimeout(() => {

            const editor = emojioneAreaInstance[0].emojioneArea.editor;
            editor.on("paste", function(e) {

                const items = (e.originalEvent || e).clipboardData.items;
                let fileFound = false;

                for (let i = 0; i < items.length; i++) {
                    const item = items[i];

                    if (item.kind === "file") {
                        if (fileFound) return; // Só 1 arquivo por vez
                        const file = item.getAsFile();
                        if (!file) return;
                        fileFound = true;

                        // Se for imagem ou outro arquivo
                        if (file.type.startsWith("image/") || file.type) {

                            const reader = new FileReader();
                            reader.onload = function(event) {

                                if (file.type.startsWith("image/")) {
                                    // Se for imagem, mostra a imagem
                                    $("#veiwMedia").attr("src", event.target.result).show();
                                    $("form[name='sendMedia'] input[name='action']").val("image");
                                } else if (file.type.startsWith("video/")) {
                                    // Se for vídeo, mostra o vídeo
                                    $("#veiwMediaVideo").attr("src", event.target.result).show();
                                    $("form[name='sendMedia'] input[name='action']").val("video");
                                } else if (file.type === "application/pdf") {
                                    // Se for PDF, mostra no iframe
                                    $("#veiwMediaPdf").fadeIn();
                                    $("form[name='sendMedia'] input[name='action']").val("document");
                                } else if (
                                    file.type === "application/x-zip-compressed" ||
                                    file.type === "application/zip"
                                ) {
                                    $("#veiwMediaZip").fadeIn();
                                    $("form[name='sendMedia'] input[name='action']").val("zip");
                                } else if (
                                    file.type ===
                                    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                ) {
                                    $("#veiwMediaXlsx").fadeIn();
                                    $("form[name='sendMedia'] input[name='action']").val("xlsx");
                                } else if (
                                    file.type ===
                                    "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
                                    file.type === "application/msword"
                                ) {
                                    $("#veiwMediaDocx").fadeIn();
                                    $("form[name='sendMedia'] input[name='action']").val("document");
                                } else {
                                    alert_float("danger", "Formato de arquivo inválido!");

                                    return false;
                                }

                                $("#veiwFileName").html(file.name);
                                $(".chat-media").fadeIn();
                                $(".chat-body div form").fadeOut();

                                // 👇 Atribui o arquivo colado ao input escondido
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(file);
                                document.getElementById("inputFile_chat").files = dataTransfer.files;

                            };
                            reader.readAsDataURL(file);

                            e.preventDefault(); // Impede colar como texto
                        }
                    }
                }
            });
        }, 1000);

    });

    /**
     * Função para editar o contato
     */
    function edit_contact(element, id) {

        $("#modalEditContact").modal("show");
        $("#contact_id").val(id);
        var name = $(element).text().trim();
        $("#contact_name").val(name);

        //subimit no formulário
        $("#formEditContact").off("submit").on("submit", function(e) {
            e.preventDefault();
            var Form = $(this);
            var formData = Form.serialize();
            var contact_name = Form.find("input[name='name']").val();
            if (!contact_name) {
                alert_float("danger", "O nome do contato é obrigatório.");
                return false;
            }

            $.ajax({
                url: site_url + 'admin/contactcenter/edit_contact',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(data) {
                    if (data.retorno) {
                        $("#modalEditContact").modal("hide");
                        alert_float("success", data.message);
                        $(element).text(contact_name);
                    } else {
                        alert_float("danger", data.message);
                    }
                }
            });
        });

    }




    let destacado = false;

    $('#destacar').on('click', function() {
        const $div = $('.destacar');
        const $body = $('body');
        if (!destacado) {
            $div.css({
                position: 'fixed',
                top: 0,
                left: 0,
                width: '100vw',
                height: '100vh',
                zIndex: 9999,
                overflow: 'auto',
                backgroundColor: '#000'
            });
            destacado = true;
            $('#destacar').removeClass('fa-expand');
            $('#destacar').addClass('fa-compress');
            $body.css('overflow', 'hidden'); // Desabilita o scroll do body
        } else {
            $div.removeAttr('style');
            $('#destacar').removeClass('fa-compress');
            $('#destacar').addClass('fa-expand');

            destacado = false;
            $body.css('overflow', ''); // Reabilita o scroll do body

        }
    });


    function get_file_contact(token, phonenumber) {
        if (token && phonenumber) {
            $.ajax({
                url: site_url + 'admin/contactcenter/ajax_get_file_contact',
                data: {
                    token: token,
                    phonenumber: phonenumber
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    $('#chat-files-list-media .chat-files-list-content').html("<i class='fa fa-spinner fa-pulse'></i>");
                    $('#chat-files-list-docs .chat-files-list-content').html("<i class='fa fa-spinner fa-pulse'></i>");
                },
                success: function(data) {
                    var html = "";
                    if (data.retorno.media) {
                        data.retorno.media.forEach(function(media) {
                            var iconClass = "";
                            var  link = site_url + "uploads/" + media.msg_url;
                            if (media.msg_type === "image") {
                                iconClass += "<img src='" + site_url + "uploads/" + media.msg_url + "'>";                               
                            } else if (media.msg_type === "video") {
                                iconClass = "<i class='fa-solid fa-file-video'></i>";
                            } else if (media.msg_type === "ptt") {
                                iconClass = "<i class='fa-solid fa-music'></i>";
                                link = site_url + "" + media.msg_url;
                            }else{
                                iconClass = "<i class='fa-solid fa-file'></i>";
                            }


                            html += "<a href='" + link +"' target='_blank'>";
                            html += "<div class='chat-files-card'>";
                            html += iconClass;
                            html += "</div>";
                            html += "</a>";
                        });

                        $('#chat-files-list-media .chat-files-list-content').html(html);

                    }else{
                        $('#chat-files-list-media .chat-files-list-content').html("<p class='text-muted'><?= _l('contact_no_files') ?></p>");
                    }
                    if (data.retorno.docs) {
                        var htmlDocs = "";
                        data.retorno.docs.forEach(function(doc) {

                            if (doc.msg_type === "documentPDF") {
                                iconClass = "<i class='fa-solid fa-file-pdf'></i>";
                            } else if (doc.msg_type === "documentZIP") {
                                iconClass = "<i class='fa-solid fa-file-zipper'></i>";
                            } else if (doc.msg_type === "documentXLSX") {
                                iconClass = "<i class='fa-solid fa-file-excel'></i>";
                            } else if (doc.msg_type === "documentDOCX") {
                                iconClass = "<i class='fa-regular fa-file-word'></i>";
                            }else{
                                iconClass = "<i class='fa-solid fa-file'></i>";
                            }

                            htmlDocs += "<a href='" + site_url + "uploads/" + doc.msg_url + "' target='_blank'>";
                            htmlDocs += "<div class='chat-files-card'>";
                            htmlDocs += iconClass;
                            htmlDocs += "</div>";
                            htmlDocs += "</a>";
                        });
                        $('#chat-files-list-docs .chat-files-list-content').html(htmlDocs);

                    }else{
                        $('#chat-files-list-docs .chat-files-list-content').html("<p class='text-muted'><?= _l('contact_no_files') ?></p>");
                    }
                }
            });
        }
    }

    // Mark chat as read/unread
    function mark_chat_read_unread(element, contactId, marked_read) {
        var contextMenu = $('#context-menu-contact');
        var $row = null;
        var phoneNumber = null;
        var token = null;
        
        // First, try to get from the element itself (menu items have data stored on them)
        if (element) {
            var $el = $(element);
            phoneNumber = $el.data('id') || $el.data('phonenumber');
            token = $el.data('token');
            contactId = contactId || $el.data('contact-id');
            
            // If element is a menu item, try to get from context menu
            if ((!phoneNumber || !token) && $el.closest('#context-menu-contact').length) {
                phoneNumber = phoneNumber || contextMenu.data('id') || contextMenu.data('phonenumber');
                token = token || contextMenu.data('token');
                contactId = contactId || contextMenu.data('contact-id');
                $row = contextMenu.data('row'); // Get stored row reference
            }
        }
        
        // If still don't have data, try from context menu directly
        if ((!phoneNumber || !token)) {
            phoneNumber = contextMenu.data('id') || contextMenu.data('phonenumber');
            token = contextMenu.data('token');
            contactId = contactId || contextMenu.data('contact-id');
            $row = contextMenu.data('row');
        }
        
        // If we have the row reference, get data from it
        if ($row && $row.length) {
            phoneNumber = phoneNumber || ($row.attr('data-id') || $row.data('id'));
            token = token || ($row.attr('data-token') || $row.data('token'));
            contactId = contactId || ($row.attr('data-contact-id') || $row.data('contact-id'));
        } else if ((!phoneNumber || !token) && element) {
            // Try to find row by traversing up from element
            $row = $(element).closest('tr');
            if ($row.length) {
                phoneNumber = phoneNumber || ($row.attr('data-id') || $row.data('id'));
                token = token || ($row.attr('data-token') || $row.data('token'));
                contactId = contactId || ($row.attr('data-contact-id') || $row.data('contact-id'));
                
                // If token is still missing, try to get it from paginadorToken (global variable set by get_message_contact)
                if (!token && typeof paginadorToken !== 'undefined' && paginadorToken) {
                    token = paginadorToken;
                }
            }
        }
        
        // If still don't have, try to find by contactId
        if ((!phoneNumber || !token) && contactId) {
            $row = $('tr[data-contact-id="' + contactId + '"]');
            if ($row.length) {
                phoneNumber = phoneNumber || ($row.attr('data-id') || $row.data('id'));
                token = token || ($row.attr('data-token') || $row.data('token'));
            }
        }
        
        // If marked_read is not provided, determine based on current state
        if (marked_read === undefined || marked_read === null) {
            var $row = $('tr[data-id="' + phoneNumber + '"]');
            if (!$row.length && contextContactId) {
                $row = $('tr[data-contact-id="' + contextContactId + '"]');
            }
            if (!$row.length && element) {
                $row = $(element).closest('tr');
            }
            
            if ($row.length) {
                var currentState = $row.hasClass('chat-marked-read');
                marked_read = currentState ? 0 : 1; // Toggle: if read, mark as unread, otherwise mark as read
            } else {
                marked_read = 1; // Default to mark as read
            }
        }
        
        // Convert contactId to integer
        contactId = parseInt(contactId);
        if (isNaN(contactId) || contactId <= 0) {
            contactId = 0; // Backend will find contact by phone/token
        }
        
        // Validate we have at least phoneNumber and token (like get_message_contact does)
        if (!phoneNumber || !token) {
            // Try one more time to get from the visible context menu's parent row
            if (contextMenu.is(':visible')) {
                // Find which row was right-clicked by finding rows near the menu position
                var menuPos = contextMenu.offset();
                var $allRows = $('#contaChat tbody tr');
                $allRows.each(function() {
                    var $row = $(this);
                    var rowPos = $row.offset();
                    // If row is near the menu, use it
                    if (Math.abs(rowPos.top - menuPos.top) < 100) {
                        phoneNumber = phoneNumber || ($row.attr('data-id') || $row.data('id'));
                        token = token || ($row.attr('data-token') || $row.data('token'));
                        contactId = contactId || ($row.attr('data-contact-id') || $row.data('contact-id'));
                        return false; // break
                    }
                });
            }
        }
        
        // Final validation - try to get token from multiple sources if still missing
        if (!token) {
            // Try global paginadorToken variable (set when chat is opened)
            if (typeof paginadorToken !== 'undefined' && paginadorToken) {
                token = paginadorToken;
            }
            // Try hidden input field
            else if ($('input[name="token"]').length && $('input[name="token"]').val()) {
                token = $('input[name="token"]').val();
            }
        }
        
        // Final validation
        if (!phoneNumber || !token) {
            console.error('Missing phoneNumber or token. Phone:', phoneNumber, 'Token:', token);
            console.error('Context Menu Data:', {
                phonenumber: contextMenu.data('phonenumber'),
                id: contextMenu.data('id'),
                token: contextMenu.data('token'),
                contactId: contextMenu.data('contact-id'),
                visible: contextMenu.is(':visible'),
                offset: contextMenu.offset()
            });
            console.error('Element:', element);
            console.error('paginadorToken:', typeof paginadorToken !== 'undefined' ? paginadorToken : 'undefined');
            console.error('Input token:', $('input[name="token"]').val());
            alert_float('danger', '<?= _l("invalid_contact_id"); ?>');
            return;
        }
        
        console.log('Mark chat status:', { contactId: contactId, phoneNumber: phoneNumber, token: token, marked_read: marked_read });

        // Show loading state - element might be a li from context menu or a button
        var $button = null;
        var originalHtml = '';
        if (element) {
            $button = $(element);
            originalHtml = $button.html();
            $button.html('<i class="fa fa-spinner fa-spin"></i>');
        }
        
        var ajaxData = {
            contact_id: contactId,
            marked_read: marked_read
        };
        
        // Always include phoneNumber and token - backend will use them if contactId is invalid
        if (phoneNumber) ajaxData.phonenumber = phoneNumber;
        if (token) ajaxData.token = token;
        
        $.ajax({
            url: site_url + 'admin/contactcenter/ajax_mark_chat_read_status',
            data: ajaxData,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('Mark chat status response:', response);
                
                if ($button && $button.length) {
                    $button.html(originalHtml); // Restore original button
                }
                
                if (response.success) {
                    // Hide context menu if open
                    $('#context-menu-contact').hide();
                    
                    // Update the UI - find row by phoneNumber/token or contactId
                    var $row = null;
                    
                    // Try by contact ID first
                    if (contactId > 0) {
                        $row = $('tr[data-contact-id="' + contactId + '"]');
                    }
                    
                    // If not found, try from element
                    if ((!$row || !$row.length) && element) {
                        $row = $(element).closest('tr');
                    }
                    
                    // If still not found, try finding by phone number
                    if ((!$row || !$row.length) && phoneNumber) {
                        $row = $('tr[data-id="' + phoneNumber + '"]');
                    }
                    
                    // Update contact ID if we got it from response
                    if (response.contact_id) {
                        var newContactId = parseInt(response.contact_id);
                        if (newContactId > 0) {
                            contactId = newContactId;
                        }
                    }
                    
                    // If still don't have row, try finding by contact ID from response
                    if ((!$row || !$row.length) && response.contact_id) {
                        $row = $('tr[data-contact-id="' + response.contact_id + '"]');
                    }
                    
                    if ($row && $row.length) {
                        // Update contact ID on row if we got it from response
                        if (response.contact_id) {
                            var newContactId = parseInt(response.contact_id);
                            if (newContactId > 0) {
                                $row.attr('data-contact-id', newContactId);
                            }
                        }
                        
                        if (marked_read == 1) {
                            // Marked as read - add checkmark icon and class
                            $row.addClass('chat-marked-read');
                            var $name = $row.find('h1');
                            // Remove unread icon if exists
                            $name.find('.fa-envelope.fa-unread-indicator').remove();
                            // Add checkmark icon if not exists
                            if ($name.length && !$name.find('.fa-check-circle').length) {
                                $name.prepend('<i class="fa-solid fa-check-circle text-success" title="<?= _l("chat_read"); ?>"></i> ');
                            }
                            // Don't remove the unread badge - it shows message count, not read status
                        } else {
                            // Marked as unread - remove checkmark icon and class, add unread indicator
                            $row.removeClass('chat-marked-read');
                            var $name = $row.find('h1');
                            // Remove checkmark icon
                            $name.find('.fa-check-circle').remove();
                            // Add unread envelope icon if not exists
                            if ($name.length && !$name.find('.fa-envelope.fa-unread-indicator').length) {
                                $name.prepend('<i class="fa-solid fa-envelope fa-unread-indicator text-warning" title="<?= _l("chat_unread"); ?>"></i> ');
                            }
                            // Don't modify the unread badge - it shows message count, not read status
                        }
                    } else {
                        console.warn('Could not find row to update. Phone:', phoneNumber, 'ContactId:', contactId, 'Response ContactId:', response.contact_id);
                    }
                    
                    // Show success message
                    alert_float('success', response.message || '<?= _l("chat_status_updated"); ?>');
                } else {
                    alert_float('danger', response.message || '<?= _l("error_updating_chat_status"); ?>');
                }
            },
            error: function() {
                if ($button && $button.length) {
                    $button.html(originalHtml); // Restore original button on error
                }
                alert_float('danger', '<?= _l("error_updating_chat_status"); ?>');
            }
        });
    }

    // Show contact context menu on right-click (make it global)
    window.show_contact_context_menu = function(event, contactId) {
        event.preventDefault();
        event.stopPropagation();
        
        // Hide any existing context menus
        $('#context-menu').hide();
        $('#context-menu-contact').hide();
        
        var contextMenu = $('#context-menu-contact');
        contextMenu.data('contact-id', contactId);
        
        // Get the position relative to the viewport
        var x = event.clientX;
        var y = event.clientY;
        
        // Position the menu at cursor
        contextMenu.css({
            display: 'block',
            position: 'fixed',
            left: x + 'px',
            top: y + 'px',
            zIndex: 10000
        });
        
        // Hide menu when clicking outside or after a delay
        setTimeout(function() {
            $(document).one('click contextmenu', function(e) {
                if (!$(e.target).closest('#context-menu-contact').length) {
                    $('#context-menu-contact').hide();
                }
            });
        }, 100);
    };

    // Handle chat marked read filter change
    $(document).on('change', '#chatMarkedRead', function() {
        get_contact();
    });

    // Prevent browser default context menu on contact rows
    // Handle clicks on context menu items
    $(document).on('click', '#context-menu-contact li', function(e) {
        e.stopPropagation();
        var marked_read = $(this).data('marked-read');
        mark_chat_read_unread(this, null, marked_read);
        return false;
    });
    
    $(document).on('contextmenu', '#contaChat tbody tr', function(e) {
        // Prevent default browser context menu
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        var $row = $(this);
        // Get data from row attributes - same way get_message_contact does
        var contactId = $row.attr('data-contact-id') || $row.data('contact-id');
        var phoneNumber = $row.attr('data-id') || $row.data('id'); // This is the phone number
        var token = $row.attr('data-token') || $row.data('token');
        
        // If not found on tr, check inner div
        if ((!contactId || contactId == 0 || contactId == '0') || !phoneNumber || !token) {
            var $innerDiv = $row.find('div[data-id]').first();
            if ($innerDiv.length) {
                phoneNumber = phoneNumber || ($innerDiv.attr('data-id') || $innerDiv.data('id'));
                token = token || ($innerDiv.attr('data-token') || $innerDiv.data('token'));
            }
        }
        
        // Convert to integer to check if valid
        contactId = parseInt(contactId);
        if (isNaN(contactId)) {
            contactId = 0;
        }
        
        // Store in context menu - use both 'id' and 'phonenumber' for compatibility
        var contextMenu = $('#context-menu-contact');
        contextMenu.data('contact-id', contactId);
        contextMenu.data('phonenumber', phoneNumber);
        contextMenu.data('id', phoneNumber); // Also store as 'id' for compatibility
        contextMenu.data('token', token);
        contextMenu.data('row', $row); // Store reference to the row
        
        // Also store data directly on menu items for easy access
        contextMenu.find('li').each(function() {
            $(this).data('contact-id', contactId);
            $(this).data('phonenumber', phoneNumber);
            $(this).data('id', phoneNumber);
            $(this).data('token', token);
        });
        
        // Hide any other context menus
        $('#context-menu').hide();
        
        // Get mouse position
        var x = e.clientX || (e.pageX - $(window).scrollLeft());
        var y = e.clientY || (e.pageY - $(window).scrollTop());
        
        // Position the menu at cursor
        contextMenu.css({
            display: 'block',
            position: 'fixed',
            left: x + 'px',
            top: y + 'px',
            zIndex: 10000
        });
        
        // Hide menu when clicking outside
        setTimeout(function() {
            $(document).one('click', function(ev) {
                if (!$(ev.target).closest('#context-menu-contact').length) {
                    $('#context-menu-contact').hide();
                }
            });
        }, 10);
        
        return false;
    });
    
    // Also handle it at the table level to catch it early
    $(document).on('contextmenu', '#contaChat', function(e) {
        var $target = $(e.target);
        var $row = $target.closest('tr');
        
        if ($row.length && !$target.closest('#context-menu-contact').length) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            var contactId = $row.attr('data-contact-id') || $row.data('contact-id');
            var phoneNumber = $row.attr('data-id') || $row.data('id'); // This is the phone number
            var token = $row.attr('data-token') || $row.data('token');
            
            // If not found on tr, check inner div
            if ((!contactId || contactId == 0 || contactId == '0') || !phoneNumber || !token) {
                var $innerDiv = $row.find('div[data-id]').first();
                if ($innerDiv.length) {
                    phoneNumber = phoneNumber || ($innerDiv.attr('data-id') || $innerDiv.data('id'));
                    token = token || ($innerDiv.attr('data-token') || $innerDiv.data('token'));
                }
            }
            
            contactId = parseInt(contactId);
            if (isNaN(contactId)) {
                contactId = 0;
            }
            
            // Always show menu if we have phoneNumber and token
            if (phoneNumber && token) {
                var contextMenu = $('#context-menu-contact');
                contextMenu.data('contact-id', contactId);
                contextMenu.data('phonenumber', phoneNumber);
                contextMenu.data('id', phoneNumber); // Also store as 'id' for compatibility
                contextMenu.data('token', token);
                contextMenu.data('row', $row); // Store reference to the row
                
                // Also store data directly on menu items for easy access
                contextMenu.find('li').each(function() {
                    $(this).data('contact-id', contactId);
                    $(this).data('phonenumber', phoneNumber);
                    $(this).data('id', phoneNumber);
                    $(this).data('token', token);
                });
                $('#context-menu').hide();
                
                var x = e.clientX || (e.pageX - $(window).scrollLeft());
                var y = e.clientY || (e.pageY - $(window).scrollTop());
                
                contextMenu.css({
                    display: 'block',
                    position: 'fixed',
                    left: x + 'px',
                    top: y + 'px',
                    zIndex: 10000
                });
                
                setTimeout(function() {
                    $(document).one('click', function(ev) {
                        if (!$(ev.target).closest('#context-menu-contact').length) {
                            $('#context-menu-contact').hide();
                        }
                    });
                }, 10);
            }
            
            return false;
        }
    });
</script>

<style>
    /* Unread contacts - green background */
    tr:not(.chat-marked-read) .chat-contato {
        background: rgba(34, 197, 94, 0.15) !important; /* Green background for unread */
        border-left: 3px solid rgba(34, 197, 94, 0.6);
    }
    tr:not(.chat-marked-read) .chat-contato h1 {
        color: rgba(255, 255, 255, 1);
        font-weight: 500;
    }
    tr:not(.chat-marked-read) .chat-contato h6 {
        color: rgba(255, 255, 255, 0.9);
    }
    tr:not(.chat-marked-read) .chat-contato:hover {
        background: rgba(34, 197, 94, 0.25) !important;
        border-left-color: rgba(34, 197, 94, 0.9);
    }
    
    /* Read contacts - subtle styling */
    .chat-marked-read {
        /* Remove opacity to keep colors vibrant */
        opacity: 1;
    }
    .chat-marked-read .chat-contato {
        /* Slightly reduce background brightness instead of overall opacity */
        background: rgba(255, 255, 255, 0.05);
        border-left: 3px solid transparent;
    }
    .chat-marked-read .chat-contato h1 {
        /* Keep text vibrant but slightly softer */
        color: rgba(255, 255, 255, 0.85);
        font-weight: normal;
    }
    .chat-marked-read .chat-contato h6 {
        /* Keep secondary text readable */
        color: rgba(255, 255, 255, 0.75);
    }
    .chat-marked-read .chat-contato-time {
        /* Keep time readable */
        color: rgba(255, 255, 255, 0.7);
    }
    .chat-marked-read .chat-contato:hover {
        /* On hover, restore full brightness */
        background: var(--primary);
    }
    .chat-marked-read .chat-contato:hover h1,
    .chat-marked-read .chat-contato:hover h6 {
        /* On hover, restore full text brightness */
        color: rgba(255, 255, 255, 1);
    }
    #context-menu-contact {
        position: fixed;
        z-index: 10000;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        min-width: 200px;
        padding: 5px 0;
    }
    #context-menu-contact ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    #context-menu-contact li {
        padding: 8px 15px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }
    #context-menu-contact li:last-child {
        border-bottom: none;
    }
    #context-menu-contact li:hover {
        background-color: #f5f5f5;
    }
    #context-menu-contact li i {
        margin-right: 8px;
    }
</style>

<div class="modal fade" id="modalEditContact" tabindex="-1" role="dialog" aria-labelledby="modalEditContactLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditContactLabel"><?= _l('contact_contact_edit') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= _l('close') ?>">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulário de edição de contato -->
                <?php echo form_open('admin/contactcenter/edit_contact', ['id' => 'formEditContact']); ?>
                <input type="hidden" name="id" id="contact_id" value="">
                <div class="form-group">
                    <label for="name"><?= _l('contact_name') ?></label>
                    <input type="text" class="form-control" name="name" id="contact_name" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _l('close') ?></button>
                    <button type="submit" class="btn btn-primary"><?= _l('save') ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<!-- New Chat Modal -->
<div class="modal fade" id="modalNewChat" tabindex="-1" role="dialog" aria-labelledby="modalNewChatLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalNewChatLabel"><?= _l("contac_chat_new"); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tabs for Lead/Client/Staff -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="active" role="presentation">
                        <a href="#newChatTabLeadContent" id="newChatTabLead" aria-controls="newChatTabLeadContent" role="tab" data-toggle="tab">
                            <i class="fa-solid fa-user-plus"></i> <?= _l("lead"); ?>
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#newChatTabClientContent" id="newChatTabClient" aria-controls="newChatTabClientContent" role="tab" data-toggle="tab">
                            <i class="fa-solid fa-user-tie"></i> <?= _l("client"); ?>
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#newChatTabStaffContent" id="newChatTabStaff" aria-controls="newChatTabStaffContent" role="tab" data-toggle="tab">
                            <i class="fa-solid fa-users"></i> <?= _l("staff"); ?>
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Lead Tab -->
                    <div role="tabpanel" class="tab-pane fade in active" id="newChatTabLeadContent">
                        <div class="form-group">
                            <label><?= _l("lead_name"); ?> / <?= _l("phone_number"); ?> / <?= _l("email"); ?></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" id="newChatSearchLead" class="form-control" placeholder="<?= _l('search_lead_placeholder'); ?>" autocomplete="off">
                                <input type="hidden" id="newChatType" value="lead">
                            </div>
                        </div>
                        <div id="newChatResultsLead" class="search-results" style="max-height: 400px; overflow-y: auto;"></div>
                    </div>

                    <!-- Client Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="newChatTabClientContent">
                        <div class="form-group">
                            <label><?= _l("client"); ?> / <?= _l("phone_number"); ?> / <?= _l("email"); ?></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" id="newChatSearchClient" class="form-control" placeholder="<?= _l('search_client_placeholder'); ?>" autocomplete="off">
                            </div>
                        </div>
                        <div id="newChatResultsClient" class="search-results" style="max-height: 400px; overflow-y: auto;"></div>
                    </div>

                    <!-- Staff Tab -->
                    <div role="tabpanel" class="tab-pane fade" id="newChatTabStaffContent">
                        <div class="form-group">
                            <label><?= _l("staff"); ?> / <?= _l("phone_number"); ?> / <?= _l("email"); ?></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" id="newChatSearchStaff" class="form-control" placeholder="<?= _l('search_staff'); ?>..." autocomplete="off">
                            </div>
                        </div>
                        <div id="newChatResultsStaff" class="search-results" style="max-height: 400px; overflow-y: auto;"></div>
                    </div>
                </div>

                <!-- Direct Phone Input Option -->
                <hr>
                <div class="form-group">
                    <label><?= _l("phone_number"); ?> (<?= _l("or_enter_phone_directly"); ?>)</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa-solid fa-mobile-screen"></i></span>
                        <input type="text" id="newChatPhoneDirect" class="form-control" placeholder="<?= _l('phone_number'); ?>" autocomplete="off">
                    </div>
                </div>

                <!-- Message Section -->
                <hr>
                <div class="form-group">
                    <label><?= _l("message"); ?> (<?= _l("optional"); ?>)</label>
                    
                    <!-- Standard Messages -->
                    <div class="btn-group btn-group-justified mb-2" style="display: flex; gap: 5px; margin-bottom: 10px;">
                        <button type="button" class="btn btn-default btn-sm standard-msg-btn" data-msg="Oi!">Oi!</button>
                        <button type="button" class="btn btn-default btn-sm standard-msg-btn" data-msg="Bom dia"><?= _l("good_morning"); ?></button>
                        <button type="button" class="btn btn-default btn-sm standard-msg-btn" data-msg="Boa tarde"><?= _l("good_afternoon"); ?></button>
                        <button type="button" class="btn btn-default btn-sm standard-msg-btn" data-msg="Boa noite"><?= _l("good_evening"); ?></button>
                    </div>
                    
                    <!-- Custom Message Input -->
                    <textarea id="newChatMessage" class="form-control" rows="3" placeholder="<?= _l("type_custom_message"); ?>"></textarea>
                    <small class="text-muted"><?= _l("select_standard_or_type_custom"); ?></small>
                </div>

                <!-- Media Section -->
                <hr>
                <div class="form-group">
                    <label><?= _l("media"); ?> (<?= _l("optional"); ?>)</label>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-default btn-block" onclick="open_media_library_for_new_chat()">
                                <i class="fa-solid fa-music"></i> <?= _l("select_from_library"); ?>
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success btn-block" onclick="$('#newChatMediaFile').click()">
                                <i class="fa-solid fa-upload"></i> <?= _l("upload_media"); ?>
                            </button>
                            <input type="file" id="newChatMediaFile" style="display: none;" accept="image/*,audio/*,video/*,.pdf,.doc,.docx,.xls,.xlsx">
                        </div>
                    </div>
                    <div id="newChatSelectedMedia" class="mt-2" style="display: none;">
                        <div class="alert alert-info" style="margin-bottom: 0; display: flex; align-items: center; justify-content: space-between;">
                            <button type="button" class="btn btn-danger btn-sm" onclick="clear_new_chat_media()" style="margin-right: 10px;">
                                <i class="fa-solid fa-times"></i> <?= _l("close"); ?>
                            </button>
                            <span style="flex: 1;">
                                <i class="fa-solid fa-file"></i> <span id="newChatMediaFileName"></span>
                            </span>
                        </div>
                    </div>
                    <input type="hidden" id="newChatMediaId" value="">
                    <input type="hidden" id="newChatMediaPath" value="">
                    <input type="hidden" id="newChatMediaType" value="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _l("cancel"); ?></button>
                <button type="button" class="btn btn-primary" id="btnStartChatDirect" onclick="start_chat_with_message()">
                    <i class="fa-solid fa-paper-plane"></i> <?= _l("start_chat"); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.search-results {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    background: #f9f9f9;
}
.search-result-item {
    padding: 12px;
    margin-bottom: 8px;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid #e0e0e0;
    color: #333;
}
.search-result-item:hover {
    background: var(--primary, #0066cc);
    color: white !important;
    border-color: var(--primary, #0066cc);
    transform: translateX(5px);
}
.search-result-item h5 {
    margin: 0 0 5px 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
}
.search-result-item:hover h5 {
    color: white !important;
}
.search-result-item .result-info {
    font-size: 12px;
    color: #666;
}
.search-result-item:hover .result-info {
    color: rgba(255,255,255,0.9) !important;
}
.search-result-item i {
    margin-right: 8px;
    color: var(--primary, #0066cc);
}
.search-result-item:hover i {
    color: white !important;
}
.search-results-empty {
    text-align: center;
    padding: 30px;
    color: #999;
}
#modalNewChat .nav-tabs .nav-link {
    cursor: pointer;
}
.standard-msg-btn {
    flex: 1;
    white-space: nowrap;
}
.standard-msg-btn.btn-primary {
    background-color: var(--primary, #0066cc);
    border-color: var(--primary, #0066cc);
    color: white;
}
#newChatMessage {
    resize: vertical;
    min-height: 80px;
}

/* Old bulk send styles removed - replaced by modern styles below */

@media (max-width: 768px) {
    .bulk-send-progress-container {
        min-width: 100%;
        max-width: 100%;
        margin-bottom: 10px;
    }
}

/* Chat Filter Badge Styles */
.chat-filter-badge {
    transition: all 0.2s ease;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
}

.chat-filter-badge:hover {
    background: #e9ecef !important;
    border-color: #adb5bd !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.chat-filter-badge.active {
    background: #007bff !important;
    border-color: #007bff !important;
    color: #fff !important;
}

.chat-filter-select {
    position: relative !important;
    width: auto !important;
    min-width: 150px;
}

/* Style the bootstrap-select to look like a badge */
.chat-filter-badge-wrapper {
    display: inline-block;
}

.chat-filter-badge-wrapper .bootstrap-select {
    width: auto !important;
}

.chat-filter-badge-wrapper .bootstrap-select .dropdown-toggle.chat-filter-badge-select {
    border-radius: 20px !important;
    padding: 5px 12px !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    border: 1px solid #ddd !important;
    background: #f8f9fa !important;
    color: #495057 !important;
    height: 32px !important;
    line-height: 20px !important;
    white-space: nowrap !important;
}

.chat-filter-badge-wrapper .bootstrap-select .dropdown-toggle.chat-filter-badge-select:focus,
.chat-filter-badge-wrapper .bootstrap-select .dropdown-toggle.chat-filter-badge-select:hover {
    background: #e9ecef !important;
    border-color: #adb5bd !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.chat-filter-badge-wrapper .bootstrap-select .dropdown-toggle .filter-option {
    display: inline-flex !important;
    align-items: center !important;
    white-space: nowrap !important;
}

.chat-filter-badge-wrapper .bootstrap-select .dropdown-menu {
    margin-top: 5px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border: 1px solid #e0e0e0;
    min-width: 200px;
}

/* AI Button Styling - Red when off, normal when on */
#onAi.btn-primary.off-ai {
    background: rgba(220, 38, 38, 0.8) !important; /* Red background when AI is off */
    border-color: rgba(220, 38, 38, 0.9) !important;
}

#onAi.btn-primary.off-ai:hover {
    background: rgba(220, 38, 38, 1) !important; /* Darker red on hover */
    border-color: rgba(220, 38, 38, 1) !important;
}

#onAi.btn-primary.off-ai i.fa-robot {
    color: rgba(255, 255, 255, 1) !important; /* White icon for visibility */
}

#onAi.btn-primary.active-ai {
    background: var(--primary) !important; /* Normal primary color when AI is on */
}

#onAi.btn-primary.active-ai i.fa-robot {
    color: rgba(255, 255, 255, 1) !important; /* White icon */
}

/* Ensure all icons in btn-primary have consistent color */
.btn-primary i {
    color: inherit;
}

/* Modern Header Chat Styles */
.header-chat-modern {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px 16px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 10px;
    transition: all 0.3s ease;
    width: 100%;
}

#wrapper[theme="dark"] .header-chat-modern,
#wrapper[theme="1"] .header-chat-modern {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.03) 100%);
    border-color: rgba(255, 255, 255, 0.15);
}

.header-chat-ai-status {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.header-chat-ai-status:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.25);
    transform: scale(1.08);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.header-chat-ai-status:active {
    transform: scale(1.02);
}

.header-chat-ai-status::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.05);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.header-chat-ai-status:hover::after {
    opacity: 1;
}

.header-chat-ai-status lord-icon {
    cursor: pointer;
    transition: transform 0.3s ease;
}

.header-chat-ai-status:hover lord-icon {
    transform: scale(1.1);
}

.header-chat-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-width: 0;
}

.box-dados-chat-modern {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.box-dados-chat-modern * {
    color: #ffffff !important;
    opacity: 1 !important;
}

.lead-name-modern {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #ffffff !important;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    opacity: 1 !important;
}

.lead-phone-modern {
    font-size: 13px;
    color: #ffffff !important;
    font-weight: 400;
    display: flex;
    align-items: center;
    gap: 6px;
    opacity: 1 !important;
}

.lead-phone-modern::before {
    content: "\f095";
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    font-size: 11px;
    opacity: 0.7;
}

.progress-container-modern {
    display: flex;
    align-items: center;
    gap: 4px;
    overflow-x: auto;
    padding: 4px 0;
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
}

.progress-container-modern::-webkit-scrollbar {
    height: 4px;
}

.progress-container-modern::-webkit-scrollbar-track {
    background: transparent;
}

.progress-container-modern::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 2px;
}

#wrapper[theme="dark"] .progress-container-modern::-webkit-scrollbar-thumb,
#wrapper[theme="1"] .progress-container-modern::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
}

.progress-container-modern .step {
    flex-shrink: 0;
    padding: 6px 14px;
    font-size: 11px;
    font-weight: 500;
    border-radius: 20px;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.2);
    opacity: 1 !important;
}

.progress-container-modern .step:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    background: rgba(255, 255, 255, 0.15);
    opacity: 1;
}

.progress-container-modern .step.completed {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: #ffffff !important;
    border-color: #28a745;
    box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
    opacity: 1 !important;
}

.progress-container-modern .step.active {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: #ffffff !important;
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.4);
    font-weight: 600;
    position: relative;
    opacity: 1 !important;
}

.progress-container-modern .step.active::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    border-radius: 22px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    opacity: 0.3;
    z-index: -1;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 0.3;
        transform: scale(1);
    }
    50% {
        opacity: 0.5;
        transform: scale(1.02);
    }
}

.progress-container-modern .step:not(.completed):not(.active) {
    color: #ffffff !important;
    opacity: 1 !important;
}

.progress-container-modern .step:not(.completed):not(.active):hover {
    opacity: 1 !important;
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
}

@media (max-width: 768px) {
    .header-chat-modern {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .header-chat-ai-status {
        align-self: flex-start;
    }
    
    .progress-container-modern {
        width: 100%;
    }
}

/* Modern Footer Bar Styles */
.footer-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: 10px;
    flex-wrap: wrap;
}

#wrapper[theme="dark"] .footer-bar,
#wrapper[theme="1"] .footer-bar {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.03) 100%);
    border-color: rgba(255, 255, 255, 0.15);
}

/* Modern Progress Container Styles */
.bulk-send-progress-container {
    min-width: 200px;
    max-width: 300px;
    flex-shrink: 0;
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    transition: all 0.3s ease;
}

#wrapper[theme="dark"] .bulk-send-progress-container,
#wrapper[theme="1"] .bulk-send-progress-container {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.15);
}

.bulk-send-progress-container:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

#wrapper[theme="dark"] .bulk-send-progress-container:hover,
#wrapper[theme="1"] .bulk-send-progress-container:hover {
    background: rgba(255, 255, 255, 0.12);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.bulk-send-progress-container > div:first-child {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}

.bulk-send-progress-container > div:first-child > div {
    display: flex;
    align-items: center;
    gap: 8px;
}

.bulk-send-progress-container > div:first-child i {
    color: #ffffff;
    font-size: 14px;
}

.bulk-send-progress-container > div:first-child strong {
    color: #ffffff !important;
    font-size: 13px;
    font-weight: 600;
    opacity: 1 !important;
}

.bulk-send-progress-container .bulk-progress-text {
    color: #ffffff !important;
    font-size: 12px;
    opacity: 0.9 !important;
    margin-bottom: 6px;
    display: block;
}

.bulk-send-progress-container .bulk-progress-percent {
    color: #ffffff !important;
    font-size: 12px;
    font-weight: 600;
    opacity: 1 !important;
}

.bulk-send-progress-container .bulk-progress-small {
    color: #ffffff !important;
    font-size: 10px;
    opacity: 0.8 !important;
    margin-top: 4px;
    display: block;
}

.bulk-send-progress-container .progress {
    height: 8px;
    margin-bottom: 4px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    overflow: hidden;
}

.bulk-send-progress-container .progress-bar {
    border-radius: 4px;
}

.bulk-send-progress-container .btn-danger {
    padding: 2px 8px;
    font-size: 10px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.bulk-send-progress-container .btn-danger:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 6px rgba(220, 53, 69, 0.4);
}

/* Modern Search and Filter Section */
.input-filter-chat {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    min-width: 0;
}

.input-filter-chat .input-group {
    flex: 1;
    min-width: 200px;
}

.input-filter-chat .input-group-addon {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-right: none;
    color: #ffffff;
    border-radius: 8px 0 0 8px;
}

.input-filter-chat .form-control {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-left: none;
    color: #ffffff;
    border-radius: 0 8px 8px 0;
    transition: all 0.3s ease;
}

.input-filter-chat .form-control:focus {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.25);
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    color: #ffffff;
}

.input-filter-chat .form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.input-filter-chat .filter-contact {
    display: flex;
    gap: 6px;
}

.input-filter-chat .btn-filter-chat {
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.input-filter-chat .btn-filter-chat:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.input-filter-chat .btn-filter-chat a {
    color: inherit;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Footer Icons List */
.footer-bar > ul {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
    flex-wrap: wrap;
}

.footer-bar > ul > li,
.footer-bar > ul > a {
    display: flex;
    align-items: center;
    justify-content: center;
}

.footer-bar > ul .btn-primary {
    width: 40px;
    height: 40px;
    padding: 0;
    border-radius: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.footer-bar > ul .btn-primary:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.footer-bar > ul .btn-primary i {
    color: #ffffff;
    font-size: 16px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.footer-bar > ul .btn-primary input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    margin: 0;
    cursor: pointer;
    z-index: 1;
}

.footer-bar > ul .btn-primary input[type="checkbox"] + i {
    position: relative;
    z-index: 0;
}

.footer-bar > ul .btn-primary.active-ai i {
    color: #00e09b;
}

.footer-bar > ul .btn-primary.off-ai i {
    color: #e83a30;
}

@media (max-width: 768px) {
    .footer-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .bulk-send-progress-container {
        width: 100%;
        max-width: 100%;
    }
    
    .input-filter-chat {
        width: 100%;
    }
    
    .footer-bar > ul {
        width: 100%;
        justify-content: center;
    }
}

/* Modern Box Chat Destacar Styles */
.box-chat.destacar {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.box-chat.destacar:hover {
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.25);
}

/* Modern Chat Aside */
.chat-aside {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

#wrapper[theme="dark"] .chat-aside,
#wrapper[theme="1"] .chat-aside {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.03) 100%);
    border-right-color: rgba(255, 255, 255, 0.15);
}

.chat-aside section {
    padding: 10px;
    width: 100%;
    box-sizing: border-box;
}

.chat-aside section::-webkit-scrollbar {
    width: 6px;
}

.chat-aside section::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}

.chat-aside section::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
}

.chat-aside section::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Modern Chat Profile Section */
.chat-perfil {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.03) 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    filter: drop-shadow(0px 4px 12px rgba(0, 0, 0, 0.15));
    transition: all 0.3s ease;
}

#wrapper[theme="dark"] .chat-perfil,
#wrapper[theme="1"] .chat-perfil {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0.05) 100%);
    border-color: rgba(255, 255, 255, 0.15);
}

.chat-perfil:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0.06) 100%);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

#wrapper[theme="dark"] .chat-perfil:hover,
#wrapper[theme="1"] .chat-perfil:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.16) 0%, rgba(255, 255, 255, 0.08) 100%);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
}

.chat-perfil #destacar {
    position: absolute;
    top: 12px;
    right: 12px;
    color: #ffffff;
    font-size: 16px;
    cursor: pointer;
    padding: 6px;
    border-radius: 6px;
    transition: all 0.2s ease;
    opacity: 0.7;
}

.chat-perfil #destacar:hover {
    background: rgba(255, 255, 255, 0.1);
    opacity: 1;
    transform: scale(1.1);
}

.chat-perfil img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.chat-perfil:hover img {
    border-color: rgba(255, 255, 255, 0.4);
    transform: scale(1.05);
}

.chat-perfil div div {
    margin-top: 12px;
    margin-bottom: 8px;
}

.chat-perfil div span {
    color: #ffffff !important;
    font-size: 16px;
    font-weight: 600;
    opacity: 1 !important;
    text-align: center;
    display: block;
    margin: 8px 0;
}

.chat-perfil-span {
    color: #ffffff !important;
    margin: 6px 0;
    font-size: 12px;
    font-weight: 400;
    width: 100%;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 6px 12px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.2s ease;
    opacity: 0.9 !important;
}

.chat-perfil-span:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateX(2px);
}

.chat-perfil-span i {
    color: #ffffff;
    font-size: 14px;
    opacity: 0.9;
}

.chat-perfil h6 {
    color: #ffffff !important;
    font-size: 13px;
    font-weight: 500;
    text-align: center;
    margin-top: 12px;
    padding: 0;
    background: transparent;
    border: none;
    opacity: 1 !important;
    transition: all 0.2s ease;
}

.chat-perfil h6:hover {
    transform: scale(1.02);
}

/* Modern Status Label Styles */
.chat-perfil h6 .label,
.chat-perfil h6 .s-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    border: none;
    white-space: nowrap;
}

.chat-perfil h6 .label-success,
.chat-perfil h6 .s-status.label-success {
    background: linear-gradient(135deg, #00e09b 0%, #00b87a 100%);
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(0, 224, 155, 0.3);
}

.chat-perfil h6 .label-success:hover,
.chat-perfil h6 .s-status.label-success:hover {
    background: linear-gradient(135deg, #00f0a5 0%, #00c884 100%);
    box-shadow: 0 4px 12px rgba(0, 224, 155, 0.4);
    transform: translateY(-2px);
}

.chat-perfil h6 .label-danger,
.chat-perfil h6 .s-status.label-danger,
.chat-perfil h6 .status-reconect-device {
    background: linear-gradient(135deg, #e83a30 0%, #c62828 100%);
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(232, 58, 48, 0.3);
}

.chat-perfil h6 .label-danger:hover,
.chat-perfil h6 .s-status.label-danger:hover,
.chat-perfil h6 .status-reconect-device:hover {
    background: linear-gradient(135deg, #f04a40 0%, #d32f2f 100%);
    box-shadow: 0 4px 12px rgba(232, 58, 48, 0.4);
    transform: translateY(-2px);
}

/* Status label icons */
.chat-perfil h6 .label i,
.chat-perfil h6 .s-status i {
    font-size: 11px;
    margin: 0;
}

/* Modern Department Cards */
.card-chatsingle-departaments {
    width: 100%;
    margin-top: 12px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
}

.card-chatsingle-departaments h6 {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff !important;
    transition: all 0.2s ease;
    opacity: 1 !important;
}

.card-chatsingle-departaments h6:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Modern New Chat Input */
.new-chatall {
    margin-top: 12px;
    width: 100%;
    display: none;
    padding: 12px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.new-chatall:focus-within {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.25);
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
}

.new-chatall i {
    color: #ffffff;
    font-size: 16px;
    margin-right: 8px;
    opacity: 0.8;
}

.new-chatall input {
    color: #ffffff !important;
    background: transparent;
    border: none;
    outline: none;
    flex: 1;
    font-size: 14px;
    padding: 8px 0;
}

.new-chatall input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.new-chatall .fa-whatsapp {
    color: #25d366;
    opacity: 1;
}

.new-chatall .fa-ban {
    color: #e83a30;
    opacity: 1;
    cursor: pointer;
    transition: all 0.2s ease;
}

.new-chatall .fa-ban:hover {
    transform: scale(1.2);
}

@media (max-width: 768px) {
    .chat-perfil {
        padding: 12px;
    }
    
    .chat-perfil img {
        width: 50px;
        height: 50px;
    }
    
    .chat-perfil-span {
        font-size: 11px;
        padding: 5px 10px;
    }
}

/* Modern Contact Rows Styles */
#contaChat {
    width: 100%;
    display: block;
}

#contaChat tbody {
    display: block;
    width: 100%;
}

#contaChat tbody tr {
    margin-bottom: 8px;
    display: block;
    width: 100%;
    transition: all 0.2s ease;
}

#contaChat tbody tr:hover {
    transform: translateX(4px);
}

#contaChat tbody tr td {
    display: block;
    width: 100%;
    padding: 0;
}

.chat-contato {
    width: 100%;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.03) 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    padding: 12px 16px;
    margin: 4px 0;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
    gap: 12px;
    box-sizing: border-box;
}

#wrapper[theme="dark"] .chat-contato,
#wrapper[theme="1"] .chat-contato {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0.05) 100%);
    border-color: rgba(255, 255, 255, 0.15);
}

.chat-contato:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.08) 100%);
    border-color: rgba(255, 255, 255, 0.25);
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

#wrapper[theme="dark"] .chat-contato:hover,
#wrapper[theme="1"] .chat-contato:hover {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0.1) 100%);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.chat-contato.active-contact {
    background: linear-gradient(135deg, rgba(0, 224, 155, 0.2) 0%, rgba(0, 224, 155, 0.1) 100%);
    border-color: rgba(0, 224, 155, 0.4);
    box-shadow: 0 4px 12px rgba(0, 224, 155, 0.2);
}

.chat-contato.active-contact:hover {
    background: linear-gradient(135deg, rgba(0, 224, 155, 0.25) 0%, rgba(0, 224, 155, 0.15) 100%);
    border-color: rgba(0, 224, 155, 0.5);
}

.chat-contato > div:first-child {
    position: relative;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-contato img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.chat-contato:hover img {
    border-color: rgba(255, 255, 255, 0.4);
    transform: scale(1.05);
}

.chat-contato.active-contact img {
    border-color: rgba(0, 224, 155, 0.5);
}

.chat-contato > div:last-child {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
    width: 100%;
    max-width: calc(100% - 60px);
}

.chat-contato h1 {
    color: #ffffff !important;
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    opacity: 1 !important;
    max-width: 100%;
}

.chat-contato h1:hover {
    color: #ffffff !important;
}

.chat-contato h1 i {
    font-size: 12px;
    flex-shrink: 0;
}

.chat-contato h6 {
    color: rgba(255, 255, 255, 0.8) !important;
    margin: 0;
    font-size: 11px;
    font-weight: 400;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    opacity: 0.9 !important;
}

.chat-codigo-contact {
    color: rgba(255, 255, 255, 0.7) !important;
    font-size: 10px;
    font-weight: 400;
    margin-top: 2px;
    opacity: 0.8 !important;
}

.chat-contato-time {
    position: absolute;
    top: 12px;
    right: 16px;
    font-size: 10px;
    color: rgba(255, 255, 255, 0.7) !important;
    font-weight: 400;
    opacity: 0.8 !important;
    white-space: nowrap;
    flex-shrink: 0;
}

.chat-contato-unread {
    position: absolute;
    top: 8px;
    right: 16px;
    background: #00e09b;
    color: #ffffff;
    border-radius: 12px;
    padding: 4px 8px;
    font-size: 10px;
    font-weight: 600;
    min-width: 20px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0, 224, 155, 0.4);
    z-index: 10;
    white-space: nowrap;
    flex-shrink: 0;
}

.chat-contato:hover .chat-contato-unread {
    background: #00e09b;
    box-shadow: 0 2px 8px rgba(0, 224, 155, 0.5);
    transform: scale(1.1);
}

.chat-contato.active-contact .chat-contato-unread {
    background: #ffffff;
    color: #00e09b;
    box-shadow: 0 2px 8px rgba(255, 255, 255, 0.3);
}

.font-leads-contact {
    position: absolute;
    top: 2px;
    left: 2px;
    z-index: 10;
    background: rgba(0, 0, 0, 0.8);
    border-radius: 6px;
    padding: 4px 6px;
    font-size: 11px;
    color: #ffffff !important;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    min-height: 24px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
    transition: all 0.2s ease;
    line-height: 1;
}

/* Hide when empty or contains only whitespace */
.font-leads-contact:empty {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    width: 0 !important;
    height: 0 !important;
    min-width: 0 !important;
    min-height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Also hide if it only contains whitespace or empty anchor */
.font-leads-contact:has(a:empty),
.font-leads-contact:has(a:not(:has(*))) {
    display: none !important;
}

.font-leads-contact:hover {
    background: rgba(0, 0, 0, 0.95);
    transform: scale(1.15);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.5);
}

.font-leads-contact a {
    color: #ffffff !important;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    line-height: 1;
}

.font-leads-contact a:hover {
    color: #ffffff !important;
    text-decoration: none;
}

.font-leads-contact a i,
.font-leads-contact i {
    font-size: 14px !important;
    color: #ffffff !important;
    line-height: 1;
    display: inline-block;
    width: auto;
    height: auto;
}

/* Specific icon colors for better visibility */
.font-leads-contact .fa-whatsapp {
    color: #25d366 !important;
}

.font-leads-contact .fa-facebook {
    color: #1877f2 !important;
}

.font-leads-contact .fa-instagram {
    color: #e4405f !important;
}

/* Mark buttons styling */
.chat-mark-buttons {
    position: absolute !important;
    right: 8px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    opacity: 0 !important;
    transition: opacity 0.2s ease !important;
    z-index: 10000 !important;
    pointer-events: auto !important;
    display: inline-block !important;
    visibility: visible !important;
    width: auto !important;
}

.chat-contato:hover .chat-mark-buttons,
.chat-mark-buttons:hover {
    opacity: 1 !important;
}

.chat-mark-toggle {
    cursor: pointer !important;
    padding: 6px !important;
    border-radius: 50% !important;
    background: rgba(255, 255, 255, 0.95) !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: transform 0.2s, background 0.2s !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
    width: 28px !important;
    height: 28px !important;
}

.chat-mark-toggle:hover {
    transform: scale(1.1) !important;
    background: rgba(255, 255, 255, 1) !important;
}

@media (max-width: 768px) {
    .chat-contato {
        padding: 10px;
        gap: 10px;
    }
    
    .chat-contato img {
        width: 40px;
        height: 40px;
    }
    
    .chat-contato h1 {
        font-size: 13px;
    }
    
    .chat-contato h6 {
        font-size: 10px;
    }
}

@media (max-width: 768px) {
    .input-filter-chat {
        flex-direction: column !important;
    }
    
    .chat-filter-badge-wrapper {
        width: 100%;
    }
    
    .chat-filter-badge {
        width: 100%;
        justify-content: space-between;
    }
}
</style>

<script>
$(document).ready(function() {
    let searchTimeoutLead, searchTimeoutClient, searchTimeoutStaff;

    // Handle tab switching
    $('#newChatTabLead').on('click', function() {
        $('#newChatType').val('lead');
        setTimeout(function() {
            $('#newChatSearchLead').focus();
        }, 100);
    });
    $('#newChatTabClient').on('click', function() {
        $('#newChatType').val('client');
        setTimeout(function() {
            $('#newChatSearchClient').focus();
        }, 100);
    });
    $('#newChatTabStaff').on('click', function() {
        $('#newChatType').val('staff');
        setTimeout(function() {
            $('#newChatSearchStaff').focus();
            // Trigger initial search when tab is clicked
            searchStaff('');
        }, 100);
    });
    
    // Bootstrap tab events
    $('#newChatTabLeadContent, #newChatTabClientContent, #newChatTabStaffContent').on('shown.bs.tab', function(e) {
        var target = $(e.target).attr('href');
        if (target === '#newChatTabLeadContent') {
            $('#newChatType').val('lead');
            $('#newChatSearchLead').focus();
        } else if (target === '#newChatTabClientContent') {
            $('#newChatType').val('client');
            $('#newChatSearchClient').focus();
        } else if (target === '#newChatTabStaffContent') {
            $('#newChatType').val('staff');
            $('#newChatSearchStaff').focus();
            // Trigger initial search when tab is shown
            searchStaff('');
        }
    });

    // Search leads with debounce
    $('#newChatSearchLead').on('input', function() {
        clearTimeout(searchTimeoutLead);
        var query = $(this).val().trim();
        
        // If empty or only spaces, show all results
        if (query.length === 0) {
            searchTimeoutLead = setTimeout(function() {
                searchLeads(''); // Empty string to fetch all
            }, 300);
            return;
        }

        searchTimeoutLead = setTimeout(function() {
            searchLeads(query);
        }, 300);
    });

    // Search clients with debounce
    $('#newChatSearchClient').on('input', function() {
        clearTimeout(searchTimeoutClient);
        var query = $(this).val().trim();
        
        // If empty or only spaces, show all results
        if (query.length === 0) {
            searchTimeoutClient = setTimeout(function() {
                searchClients(''); // Empty string to fetch all
            }, 300);
            return;
        }

        searchTimeoutClient = setTimeout(function() {
            searchClients(query);
        }, 300);
    });

    // Search staff with debounce
    $('#newChatSearchStaff').on('input', function() {
        clearTimeout(searchTimeoutStaff);
        var query = $(this).val().trim();
        
        // If empty or only spaces, show all results
        if (query.length === 0) {
            searchTimeoutStaff = setTimeout(function() {
                searchStaff(''); // Empty string to fetch all
            }, 300);
            return;
        }

        searchTimeoutStaff = setTimeout(function() {
            searchStaff(query);
        }, 300);
    });

    // Allow Enter key on phone input (but not in textarea)
    $('#newChatPhoneDirect').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            start_chat_from_phone();
        }
    });
    
    // Handle standard message buttons
    $('.standard-msg-btn').on('click', function() {
        $('.standard-msg-btn').removeClass('btn-primary').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-primary');
        $('#newChatMessage').val($(this).data('msg'));
    });
});

function searchLeads(query) {
    $('#newChatResultsLead').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> <?= _l("searching"); ?>...</div>');
    
    $.ajax({
        url: '<?= admin_url("contactcenter/ajax_search_leads"); ?>',
        method: 'POST',
        data: { search: query },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.results && response.results.length > 0) {
                var html = '';
                response.results.forEach(function(lead) {
                    var phone = lead.phonenumber || lead.phone || '-';
                    var email = lead.email || '-';
                    var name = lead.name || lead.company || '-';
                    html += '<div class="search-result-item" onclick="start_chat_from_lead(' + lead.id + ', \'' + phone.replace(/'/g, "\\'") + '\')">';
                    html += '<h5><i class="fa-solid fa-user-plus"></i>' + escapeHtml(name) + '</h5>';
                    html += '<div class="result-info">';
                    html += '<i class="fa-solid fa-phone"></i> ' + escapeHtml(phone) + ' ';
                    html += '<i class="fa-solid fa-envelope"></i> ' + escapeHtml(email);
                    html += '</div>';
                    html += '</div>';
                });
                $('#newChatResultsLead').html(html);
            } else {
                $('#newChatResultsLead').html('<div class="search-results-empty"><i class="fa-solid fa-search"></i><br><?= _l("no_results_found"); ?></div>');
            }
        },
        error: function() {
            $('#newChatResultsLead').html('<div class="search-results-empty"><i class="fa-solid fa-exclamation-triangle"></i><br><?= _l("error_searching"); ?></div>');
        }
    });
}

function searchClients(query) {
    $('#newChatResultsClient').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> <?= _l("searching"); ?>...</div>');
    
    $.ajax({
        url: '<?= admin_url("contactcenter/ajax_search_clients"); ?>',
        method: 'POST',
        data: { search: query },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                if (response.results && response.results.length > 0) {
                    var html = '';
                    response.results.forEach(function(client) {
                        var phone = client.phonenumber || client.phone || '-';
                        if (phone === '-') return; // Skip clients without phone
                        
                        var email = client.email || '-';
                        var name = client.company || client.fullname || (client.firstname + ' ' + client.lastname) || '-';
                        if (!name || name.trim() === '') name = 'Cliente #' + client.userid;
                        
                        html += '<div class="search-result-item" onclick="start_chat_from_client(' + client.userid + ', \'' + phone.replace(/'/g, "\\'") + '\')">';
                        html += '<h5><i class="fa-solid fa-user-tie"></i> ' + escapeHtml(name) + '</h5>';
                        html += '<div class="result-info">';
                        html += '<i class="fa-solid fa-phone"></i> ' + escapeHtml(phone) + ' ';
                        html += '<i class="fa-solid fa-envelope"></i> ' + escapeHtml(email);
                        html += '</div>';
                        html += '</div>';
                    });
                    $('#newChatResultsClient').html(html);
                } else {
                    $('#newChatResultsClient').html('<div class="search-results-empty"><i class="fa-solid fa-search"></i><br><?= _l("no_results_found"); ?></div>');
                }
            } else {
                var errorMsg = (response && response.error) ? response.error : '<?= _l("error_searching"); ?>';
                $('#newChatResultsClient').html('<div class="search-results-empty"><i class="fa-solid fa-exclamation-triangle"></i><br>' + escapeHtml(errorMsg) + '</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Client search error:', status, error);
            $('#newChatResultsClient').html('<div class="search-results-empty"><i class="fa-solid fa-exclamation-triangle"></i><br><?= _l("error_searching"); ?></div>');
        }
    });
}

function searchStaff(query) {
    $('#newChatResultsStaff').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> <?= _l("searching"); ?>...</div>');
    
    var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    var postData = {
        search: query
    };
    postData[csrfTokenName] = csrfHash;
    
    $.ajax({
        url: '<?= admin_url("contactcenter/ajax_search_staff"); ?>',
        method: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                if (response.results && response.results.length > 0) {
                    var html = '';
                    response.results.forEach(function(staff) {
                        var phone = staff.phonenumber || staff.phone || '-';
                        if (phone === '-') return; // Skip staff without phone
                        
                        var email = staff.email || '-';
                        var name = staff.fullname || (staff.firstname + ' ' + staff.lastname) || '-';
                        if (!name || name.trim() === '') name = 'Staff #' + staff.id;
                        
                        html += '<div class="search-result-item" onclick="start_chat_from_staff(' + staff.id + ', \'' + phone.replace(/'/g, "\\'") + '\')">';
                        html += '<h5><i class="fa-solid fa-users"></i> ' + escapeHtml(name) + '</h5>';
                        html += '<div class="result-info">';
                        html += '<i class="fa-solid fa-phone"></i> ' + escapeHtml(phone) + ' ';
                        html += '<i class="fa-solid fa-envelope"></i> ' + escapeHtml(email);
                        html += '</div>';
                        html += '</div>';
                    });
                    $('#newChatResultsStaff').html(html);
                } else {
                    $('#newChatResultsStaff').html('<div class="search-results-empty"><i class="fa-solid fa-search"></i><br><?= _l("no_results_found"); ?></div>');
                }
            } else {
                var errorMsg = (response && response.error) ? response.error : '<?= _l("error_searching"); ?>';
                $('#newChatResultsStaff').html('<div class="search-results-empty"><i class="fa-solid fa-exclamation-triangle"></i><br>' + escapeHtml(errorMsg) + '</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Staff search error:', status, error);
            $('#newChatResultsStaff').html('<div class="search-results-empty"><i class="fa-solid fa-exclamation-triangle"></i><br><?= _l("error_searching"); ?></div>');
        }
    });
}

function start_chat_from_lead(leadId, phoneNumber) {
    if (!phoneNumber || phoneNumber === '-') {
        alert_float('warning', '<?= _l("phone_number_required"); ?>');
        return;
    }
    
    // Clean phone number (remove spaces, dashes, etc)
    phoneNumber = phoneNumber.replace(/\D/g, '');
    
    // Set phone number in the field (keep modal open so user can type/select message)
    $("input[name='phonenumber']").val(phoneNumber);
    $('#newChatPhoneDirect').val(phoneNumber);
    
    // Focus on message textarea so user can type or select a message
    setTimeout(function() {
        $('#newChatMessage').focus();
    }, 100);
}

function start_chat_from_client(clientId, phoneNumber) {
    if (!phoneNumber || phoneNumber === '-') {
        alert_float('warning', '<?= _l("phone_number_required"); ?>');
        return;
    }
    
    // Clean phone number
    phoneNumber = phoneNumber.replace(/\D/g, '');
    
    // Set phone number in the field (keep modal open so user can type/select message)
    $("input[name='phonenumber']").val(phoneNumber);
    $('#newChatPhoneDirect').val(phoneNumber);
    
    // Focus on message textarea so user can type or select a message
    setTimeout(function() {
        $('#newChatMessage').focus();
    }, 100);
}

function start_chat_from_staff(staffId, phoneNumber) {
    if (!phoneNumber || phoneNumber === '-') {
        alert_float('warning', '<?= _l("phone_number_required"); ?>');
        return;
    }
    
    // Clean phone number
    phoneNumber = phoneNumber.replace(/\D/g, '');
    
    // Set phone number in the field (keep modal open so user can type/select message)
    $("input[name='phonenumber']").val(phoneNumber);
    $('#newChatPhoneDirect').val(phoneNumber);
    
    // Focus on message textarea so user can type or select a message
    setTimeout(function() {
        $('#newChatMessage').focus();
    }, 100);
}

function start_chat_from_phone() {
    var phoneNumber = $('#newChatPhoneDirect').val().trim();
    
    if (!phoneNumber) {
        alert_float('warning', '<?= _l("phone_number_required"); ?>');
        return;
    }
    
    // Clean phone number
    phoneNumber = phoneNumber.replace(/\D/g, '');
    
    if (phoneNumber.length < 10) {
        alert_float('warning', '<?= _l("invalid_phone_number"); ?>');
        return;
    }
    
    // Set phone number in the field (keep modal open so user can type/select message)
    $("input[name='phonenumber']").val(phoneNumber);
    
    // Focus on message textarea so user can type or select a message
    setTimeout(function() {
        $('#newChatMessage').focus();
    }, 100);
}

function start_chat_with_message() {
    // Get phone number from either the direct input or the hidden field
    var phoneNumber = $('#newChatPhoneDirect').val().trim() || $("input[name='phonenumber']").val().trim();
    
    if (!phoneNumber) {
        alert_float('warning', '<?= _l("phone_number_required"); ?>');
        return;
    }
    
    // Clean phone number
    phoneNumber = phoneNumber.replace(/\D/g, '');
    
    if (phoneNumber.length < 10) {
        alert_float('warning', '<?= _l("invalid_phone_number"); ?>');
        return;
    }
    
    // Get message from textarea
    var message = $('#newChatMessage').val().trim();
    
    // Get selected media - ensure elements exist first
    var mediaIdEl = $('#newChatMediaId');
    var mediaPathEl = $('#newChatMediaPath');
    var mediaTypeEl = $('#newChatMediaType');
    
    // Create hidden inputs if they don't exist
    if (mediaIdEl.length === 0) {
        $('#modalNewChat .modal-body').append('<input type="hidden" id="newChatMediaId" value="">');
        mediaIdEl = $('#newChatMediaId');
    }
    if (mediaPathEl.length === 0) {
        $('#modalNewChat .modal-body').append('<input type="hidden" id="newChatMediaPath" value="">');
        mediaPathEl = $('#newChatMediaPath');
    }
    if (mediaTypeEl.length === 0) {
        $('#modalNewChat .modal-body').append('<input type="hidden" id="newChatMediaType" value="">');
        mediaTypeEl = $('#newChatMediaType');
    }
    
    var mediaId = mediaIdEl.val() || '';
    var mediaPath = mediaPathEl.val() || '';
    var mediaType = mediaTypeEl.val() || '';
    
    // Debug: Log media values
    console.log('New chat - Media values:', {
        mediaId: mediaId, 
        mediaPath: mediaPath, 
        mediaType: mediaType, 
        message: message,
        mediaIdElement: mediaIdEl.length,
        mediaPathElement: mediaPathEl.length,
        mediaTypeElement: mediaTypeEl.length,
        mediaIdValue: mediaIdEl.val(),
        mediaPathValue: mediaPathEl.val(),
        mediaTypeValue: mediaTypeEl.val()
    });
    
    // Check if we have either message or media
    // Media is valid if we have mediaPath (required) and mediaType (required)
    var hasMedia = mediaPath && mediaPath.trim() !== '' && mediaType && mediaType.trim() !== '';
    
    console.log('New chat - Validation check:', {hasMessage: !!message, hasMedia: hasMedia, mediaPath: mediaPath, mediaType: mediaType});
    
    if (!message && !hasMedia) {
        alert_float('warning', '<?= _l("message_or_media_required"); ?>');
        return;
    }
    
    // Set phone number in the field
    $("input[name='phonenumber']").val(phoneNumber);
    
    // Close modal
    $('#modalNewChat').modal('hide');
    
    // Open the chat
    if (typeof get_message_contact_search === 'function') {
        get_message_contact_search(phoneNumber, '<?= $devicetoken; ?>', 0, null);
        
        // Wait longer for chat to fully load before sending
        // The chat needs time to render before we can send
        setTimeout(function() {
            console.log('Attempting to send after chat load:', {phoneNumber: phoneNumber, message: message, mediaId: mediaId, mediaPath: mediaPath});
            
            // If media is selected, send media first
            var hasMedia = mediaPath && mediaPath.trim() !== '' && mediaType && mediaType.trim() !== '';
            if (hasMedia) {
                send_media_from_new_chat(phoneNumber, mediaId, mediaPath, mediaType, message);
            } else if (message) {
                // Otherwise send text message
                send_initial_message(phoneNumber, message);
            }
        }, 2000); // Increased to 2 seconds
    } else {
        $(".new-chatall").fadeIn();
        $("input[name='phonenumber']").focus();
        
        // Send the message or media
        setTimeout(function() {
            var hasMedia = mediaPath && mediaPath.trim() !== '' && mediaType && mediaType.trim() !== '';
            if (hasMedia) {
                send_media_from_new_chat(phoneNumber, mediaId, mediaPath, mediaType, message);
            } else if (message) {
                send_initial_message(phoneNumber, message);
            }
        }, 2000);
    }
}

// Prevent duplicate sends
var sendingMessage = false;

function send_initial_message(phoneNumber, message) {
    if (!message || !phoneNumber) {
        console.log('send_initial_message: Missing message or phoneNumber', {message: message, phoneNumber: phoneNumber});
        return;
    }
    
    // Prevent duplicate sends
    if (sendingMessage) {
        console.log('send_initial_message: Already sending, skipping duplicate call');
        return;
    }
    
    sendingMessage = true;
    console.log('send_initial_message: Starting send process', {phoneNumber: phoneNumber, message: message});
    
    // Wait for chat interface to be ready
    var attempts = 0;
    var maxAttempts = 15;
    
    var trySend = function() {
        attempts++;
        console.log('send_initial_message: Attempt', attempts, 'of', maxAttempts);
        
        // Check if chat is loaded - need form and textarea to exist
        var chatReady = $("form[name='sendMsg']").length > 0 && $("#textarea-chat").length > 0;
        console.log('send_initial_message: Chat ready?', chatReady, {
            form: $("form[name='sendMsg']").length,
            textarea: $("#textarea-chat").length
        });
        
        if (chatReady || attempts >= maxAttempts) {
            // Use the same method as the normal send - fill form fields and submit
            console.log('send_initial_message: Setting form values and submitting');
            
            // Set phone number (should already be set, but make sure)
            $("input[name='phonenumber']").val(phoneNumber);
            
            // Set message in textarea
            var textarea = $("#textarea-chat");
            if (textarea[0] && textarea[0].emojioneArea) {
                // If using emojione editor
                textarea[0].emojioneArea.setText(message);
            } else {
                textarea.val(message);
            }
            
            // Trigger input event to show send button
            textarea.trigger('input');
            
            // Wait a bit for the button to be enabled, then submit
            setTimeout(function() {
                // Check if button is ready
                var submitBtn = $("#btn_submit");
                if (!submitBtn.hasClass("hidden") && !submitBtn.prop("disabled")) {
                    console.log('send_initial_message: Submitting form');
                    // Trigger form submission
                    $("form[name='sendMsg']").submit();
                    sendingMessage = false; // Reset flag after submission
                } else {
                    console.error('send_initial_message: Submit button not ready');
                    sendingMessage = false;
                    alert_float('warning', '<?= _l("error_sending_message"); ?>');
                }
            }, 500);
        } else {
            // Wait 300ms and try again
            setTimeout(trySend, 300);
        }
    };
    
    trySend();
}

// Function to open media library for new chat (different from regular media library)
function open_media_library_for_new_chat() {
    // Open the media library modal
    $('#modalMediaLibrary').modal('show');
    $('#modalMediaLibrary').data('new-chat-mode', true); // Set flag
    load_media_library();
}

// Function to select media for new chat
function select_media_for_new_chat(mediaId, filePath, fileName, fileType) {
    console.log('select_media_for_new_chat called:', {mediaId: mediaId, filePath: filePath, fileName: fileName, fileType: fileType});
    
    // Ensure hidden inputs exist, create them if they don't
    if ($('#newChatMediaId').length === 0) {
        $('#modalNewChat .modal-body').append('<input type="hidden" id="newChatMediaId" value="">');
    }
    if ($('#newChatMediaPath').length === 0) {
        $('#modalNewChat .modal-body').append('<input type="hidden" id="newChatMediaPath" value="">');
    }
    if ($('#newChatMediaType').length === 0) {
        $('#modalNewChat .modal-body').append('<input type="hidden" id="newChatMediaType" value="">');
    }
    
    // Store media info in hidden fields
    $('#newChatMediaId').val(mediaId || '');
    $('#newChatMediaPath').val(filePath || '');
    $('#newChatMediaType').val(fileType || '');
    $('#newChatMediaFileName').text(fileName || '');
    
    // Debug: Verify values were set
    console.log('select_media_for_new_chat - Values set:', {
        mediaId: $('#newChatMediaId').val(),
        mediaPath: $('#newChatMediaPath').val(),
        mediaType: $('#newChatMediaType').val(),
        fileName: $('#newChatMediaFileName').text()
    });
    
    // Update the display with proper button layout (red close button on left)
    $('#newChatSelectedMedia').html('<div class="alert alert-info" style="margin-bottom: 0; display: flex; align-items: center; justify-content: space-between;"><button type="button" class="btn btn-danger btn-sm" onclick="clear_new_chat_media()" style="margin-right: 10px;"><i class="fa-solid fa-times"></i> <?= _l("close"); ?></button><span style="flex: 1;"><i class="fa-solid fa-file"></i> ' + escapeHtml(fileName) + '</span></div>');
    $('#newChatSelectedMedia').show();
    
    // Close media library modal
    $('#modalMediaLibrary').modal('hide');
    $('#modalMediaLibrary').data('new-chat-mode', false); // Reset flag
}

// Function to clear selected media
function clear_new_chat_media() {
    $('#newChatMediaId').val('');
    $('#newChatMediaPath').val('');
    $('#newChatMediaType').val('');
    $('#newChatSelectedMedia').hide();
}

// Function to send media from new chat
function send_media_from_new_chat(phoneNumber, mediaId, filePath, fileType, textMessage) {
    var action = fileType || 'document';
    
    var formData = new FormData();
    
    formData.append('phonenumber', phoneNumber);
    formData.append('action', action);
    formData.append('staffid', <?= get_staff_user_id(); ?>);
    formData.append('device_id', <?= $device->dev_id; ?>);
    formData.append('media_file_path', filePath);
    
    // Add CSRF token
    var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    formData.append(csrfTokenName, csrfHash);
    
    console.log('New chat: Sending media', {mediaId: mediaId, filePath: filePath, fileType: fileType, phoneNumber: phoneNumber});

    // Show loading indicator in chat area
    var loadingHtml = '<div class="chat-message loading-message" style="padding: 15px; margin: 10px 0; background: #e3f2fd; border-left: 4px solid #2196F3; border-radius: 5px; text-align: center; clear: both;">' +
        '<i class="fa fa-spinner fa-spin" style="margin-right: 8px; color: #2196F3; font-size: 16px;"></i>' +
        '<span style="color: #1976D2; font-weight: 500;"><?= _l("sending_media"); ?>...</span>' +
        '</div>';
    
    // Find chat messages container - use the actual chat messages area
    var chatContainer = $('#retorno');
    if (chatContainer.length === 0) {
        // Fallback to chat-body section
        chatContainer = $('.chat-body section, .chat-body #retorno');
    }
    if (chatContainer.length === 0) {
        // Last fallback
        chatContainer = $('.chat-body');
    }
    
    if (chatContainer.length > 0) {
        chatContainer.append(loadingHtml);
        // Scroll to bottom to show loading indicator
        var scrollContainer = chatContainer.closest('.chat-body');
        if (scrollContainer.length > 0) {
            scrollContainer.scrollTop(scrollContainer[0].scrollHeight);
        } else {
            chatContainer.scrollTop(chatContainer[0].scrollHeight);
        }
    } else {
        // Fallback: show alert
        alert_float('info', '<?= _l("sending_media"); ?>...');
    }

    $.ajax({
        url: site_url + 'admin/contactcenter/ajax_send_media_from_library',
        data: formData,
        type: 'POST',
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function(data) {
            // Remove loading indicator
            $('.loading-message').remove();
            console.log('New chat: Media sent response', data);
            
            if (data.send) {
                alert_float('success', '<?= _l("media_sent_successfully"); ?>');
                
                // If there's also a text message, send it after media
                if (textMessage && textMessage.trim()) {
                    setTimeout(function() {
                        send_initial_message(phoneNumber, textMessage);
                    }, 1000);
                } else {
                    // Refresh chat to show the media
                    if (typeof get_message_contact_search === 'function') {
                        setTimeout(function() {
                            get_message_contact_search(phoneNumber, '<?= $devicetoken; ?>', 0, null);
                        }, 500);
                    }
                }
            } else {
                var errorMsg = data.error || '<?= _l("error_sending_media"); ?>';
                alert_float('warning', errorMsg);
            }
        },
        error: function(xhr, status, error) {
            // Remove loading indicator on error
            $('.loading-message').remove();
            console.error('New chat: Error sending media', {xhr: xhr, status: status, error: error});
            alert_float('danger', '<?= _l("error_sending_media"); ?>');
        }
    });
}

function escapeHtml(text) {
    if (!text) return '';
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>

<!-- Media Library Modal -->
<div class="modal fade" id="modalMediaLibrary" tabindex="-1" role="dialog" aria-labelledby="modalMediaLibraryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center;">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-times"></i> <?= _l("close"); ?>
                </button>
                <h4 class="modal-title" id="modalMediaLibraryLabel" style="flex: 1; text-align: center; margin: 0;"><?= _l("media_library"); ?></h4>
                <div style="width: 100px;"></div> <!-- Spacer for alignment -->
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tabMediaLibrary" aria-controls="tabMediaLibrary" role="tab" data-toggle="tab">
                                    <i class="fa-solid fa-list"></i> <?= _l("media_library"); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tabUploadMedia" aria-controls="tabUploadMedia" role="tab" data-toggle="tab">
                                    <i class="fa-solid fa-upload"></i> <?= _l("upload_media"); ?>
                                </a>
                            </li>
                        </ul>
                        
                        <div class="tab-content" style="margin-top: 20px;">
                            <!-- Media Library Tab -->
                            <div role="tabpanel" class="tab-pane active" id="tabMediaLibrary">
                                <div class="form-group">
                                    <label><?= _l("filter_by_type"); ?>:</label>
                                    <select id="mediaLibraryFilter" class="form-control">
                                        <option value=""><?= _l("all_types"); ?></option>
                                        <option value="audio"><?= _l("audio"); ?></option>
                                        <option value="image"><?= _l("image"); ?></option>
                                        <option value="video"><?= _l("video"); ?></option>
                                        <option value="document"><?= _l("document"); ?></option>
                                    </select>
                                </div>
                                <div id="mediaLibraryList" style="max-height: 500px; overflow-y: auto;">
                                    <div class="text-center"><i class="fa fa-spinner fa-spin"></i> <?= _l("loading"); ?>...</div>
                                </div>
                            </div>
                            
                            <!-- Upload Tab -->
                            <div role="tabpanel" class="tab-pane" id="tabUploadMedia">
                                <?php echo form_open_multipart("", ["id" => "formUploadMedia", "name" => "formUploadMedia"]); ?>
                                    <input type="hidden" name="device_id" value="<?= $device->dev_id; ?>">
                                    
                                    <div class="form-group">
                                        <label><?= _l("select_file"); ?>:</label>
                                        <input type="file" name="media_file" id="mediaFileInput" class="form-control" accept="audio/*,image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar" required>
                                        <small class="help-block"><?= _l("supported_formats"); ?>: JPG, PNG, GIF, MP3, WAV, OGG, MP4, PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR (<?= _l("max_size"); ?>: 100MB)</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label><?= _l("title"); ?> (<?= _l("optional"); ?>):</label>
                                        <input type="text" name="title" id="mediaTitle" class="form-control" placeholder="<?= _l("enter_title"); ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label><?= _l("description"); ?> (<?= _l("optional"); ?>):</label>
                                        <textarea name="description" id="mediaDescription" class="form-control" rows="3" placeholder="<?= _l("enter_description"); ?>"></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="is_global" id="mediaIsGlobal" value="1" <?= is_admin() ? '' : 'disabled'; ?>>
                                                <?= _l("make_global"); ?>
                                                <?php if (!is_admin()) { ?>
                                                    <small class="text-muted">(<?= _l("admin_only"); ?>)</small>
                                                <?php } ?>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group" style="display: flex; justify-content: flex-end;">
                                        <button type="submit" class="btn btn-success" style="background-color: #5cb85c !important; border-color: #4cae4c !important;">
                                            <i class="fa-solid fa-upload"></i> <?= _l("upload"); ?>
                                        </button>
                                    </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Send Media Modal -->
<div class="modal fade" id="modalBulkSendMedia" tabindex="-1" role="dialog" aria-labelledby="modalBulkSendMediaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center;">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-times"></i> <?= _l("close"); ?>
                </button>
                <h4 class="modal-title" id="modalBulkSendMediaLabel" style="flex: 1; text-align: center; margin: 0;"><?= _l("send_to_multiple_contacts"); ?></h4>
                <div style="width: 100px;"></div>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><?= _l("select_contacts"); ?>:</label>
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="active" role="presentation">
                            <a href="#bulkTabChats" aria-controls="bulkTabChats" role="tab" data-toggle="tab">
                                <i class="fa-solid fa-comments"></i> <?= _l("active_chats"); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#bulkTabLeads" aria-controls="bulkTabLeads" role="tab" data-toggle="tab">
                                <i class="fa-solid fa-user-plus"></i> <?= _l("leads"); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#bulkTabClients" aria-controls="bulkTabClients" role="tab" data-toggle="tab">
                                <i class="fa-solid fa-user-tie"></i> <?= _l("clients"); ?>
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#bulkTabStaff" aria-controls="bulkTabStaff" role="tab" data-toggle="tab">
                                <i class="fa-solid fa-users"></i> <?= _l("staff"); ?>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade in active" id="bulkTabChats">
                            <div class="form-group">
                                <input type="text" id="bulkSearchChats" class="form-control" placeholder="<?= _l("search_contacts"); ?>..." style="margin-bottom: 10px;">
                                <div id="bulkChatsList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                    <div class="text-center"><i class="fa fa-spinner fa-spin"></i> <?= _l("loading"); ?>...</div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="bulkTabLeads">
                            <div class="form-group">
                                <input type="text" id="bulkSearchLeads" class="form-control" placeholder="<?= _l("search_leads"); ?>..." style="margin-bottom: 10px;">
                                <div id="bulkLeadsList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                    <div class="text-center"><i class="fa fa-spinner fa-spin"></i> <?= _l("loading"); ?>...</div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="bulkTabClients">
                            <div class="form-group">
                                <input type="text" id="bulkSearchClients" class="form-control" placeholder="<?= _l("search_clients"); ?>..." style="margin-bottom: 10px;">
                                <div id="bulkClientsList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                    <div class="text-center"><i class="fa fa-spinner fa-spin"></i> <?= _l("loading"); ?>...</div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="bulkTabStaff">
                            <div class="form-group">
                                <input type="text" id="bulkSearchStaff" class="form-control" placeholder="<?= _l("search_staff"); ?>..." style="margin-bottom: 10px;">
                                <div id="bulkStaffList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                    <div class="text-center"><i class="fa fa-spinner fa-spin"></i> <?= _l("loading"); ?>...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><?= _l("selected_contacts"); ?> (<span id="bulkSelectedCount">0</span>):</label>
                    <div id="bulkSelectedContacts" style="min-height: 50px; max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px; background: #f9f9f9;">
                        <div class="text-muted text-center"><?= _l("no_contacts_selected"); ?></div>
                    </div>
                </div>
                
                <input type="hidden" id="bulkMediaId" value="">
                <input type="hidden" id="bulkMediaPath" value="">
                <input type="hidden" id="bulkMediaType" value="">
            </div>
            <div class="modal-footer" style="display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-success" id="btnBulkSend" onclick="start_bulk_send()" disabled>
                    <i class="fa-solid fa-paper-plane"></i> <?= _l("send_to_selected"); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.media-item {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: background-color 0.2s;
}
.media-item:hover {
    background-color: #f5f5f5;
}
.media-item.selected {
    background-color: #e3f2fd;
    border-color: #2196F3;
}
.media-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}
.media-item-title {
    font-weight: bold;
    font-size: 14px;
}
.media-item-actions {
    display: flex;
    gap: 5px;
}
.media-item-preview {
    margin-top: 10px;
    text-align: center;
}
.media-item-preview img {
    max-width: 100%;
    max-height: 150px;
    border-radius: 4px;
}
.media-item-preview audio {
    width: 100%;
}
.media-item-type-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: bold;
    margin-right: 5px;
}
.media-item-type-badge.audio { background-color: #4CAF50; color: white; }
.media-item-type-badge.image { background-color: #2196F3; color: white; }
.media-item-type-badge.video { background-color: #FF9800; color: white; }
.media-item-type-badge.document { background-color: #9E9E9E; color: white; }
.media-item-global-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 11px;
    background-color: #FFC107;
    color: #000;
    margin-left: 5px;
}
</style>

<script>
var mediaLibraryDeviceId = <?= $device->dev_id; ?>;

function open_media_library() {
    $('#modalMediaLibrary').modal('show');
    $('#modalMediaLibrary').data('new-chat-mode', false); // Regular mode
    load_media_library();
}

function load_media_library() {
    var fileType = $('#mediaLibraryFilter').val();
    
    $('#mediaLibraryList').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> <?= _l("loading"); ?>...</div>');
    
    // Get CSRF token
    var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    var postData = {
        device_id: mediaLibraryDeviceId,
        file_type: fileType
    };
    postData[csrfTokenName] = csrfHash;
    
    $.ajax({
        url: '<?= admin_url("contactcenter/ajax_get_media_library"); ?>',
        method: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.media && response.media.length > 0) {
                var html = '';
                response.media.forEach(function(media) {
                    var fileUrl = site_url + media.file_path;
                    var typeBadgeClass = 'media-item-type-badge ' + media.file_type;
                    var previewHtml = '';
                    
                    if (media.file_type === 'image') {
                        previewHtml = '<div class="media-item-preview"><img src="' + fileUrl + '" alt="' + escapeHtml(media.title) + '"></div>';
                    } else if (media.file_type === 'audio') {
                        previewHtml = '<div class="media-item-preview"><audio controls><source src="' + fileUrl + '"></audio></div>';
                    } else {
                        previewHtml = '<div class="media-item-preview"><i class="fa-solid fa-file fa-3x"></i></div>';
                    }
                    
                    html += '<div class="media-item" data-media-id="' + media.id + '" data-media-path="' + media.file_path + '" data-media-type="' + media.file_type + '">';
                    html += '<div class="media-item-header">';
                    html += '<div>';
                    html += '<span class="media-item-type-badge ' + media.file_type + '">' + media.file_type.toUpperCase() + '</span>';
                    html += '<span class="media-item-title">' + escapeHtml(media.title || media.filename) + '</span>';
                    if (media.is_global == 1) {
                        html += '<span class="media-item-global-badge">GLOBAL</span>';
                    }
                    html += '</div>';
                    html += '<div class="media-item-actions">';
                    // Check if we're in "new chat" mode
                    var isNewChatMode = $('#modalMediaLibrary').data('new-chat-mode');
                    if (isNewChatMode) {
                        html += '<button class="btn btn-xs btn-primary" onclick="select_media_for_new_chat(' + media.id + ', \'' + media.file_path.replace(/'/g, "\\'") + '\', \'' + (media.title || media.filename).replace(/'/g, "\\'") + '\', \'' + media.file_type + '\'); event.stopPropagation();" title="<?= _l("select_media"); ?>">';
                        html += '<i class="fa-solid fa-check"></i>';
                        html += '</button>';
                    } else {
                        html += '<button class="btn btn-xs btn-primary" onclick="send_media_to_chat(' + media.id + ', \'' + media.file_path.replace(/'/g, "\\'") + '\', \'' + media.file_type + '\'); event.stopPropagation();" title="<?= _l("send_to_chat"); ?>">';
                        html += '<i class="fa-solid fa-paper-plane"></i>';
                        html += '</button>';
                        html += '<button class="btn btn-xs btn-info" onclick="open_bulk_send_modal(' + media.id + ', \'' + media.file_path.replace(/'/g, "\\'") + '\', \'' + media.file_type + '\'); event.stopPropagation();" title="<?= _l("send_to_multiple"); ?>">';
                        html += '<i class="fa-solid fa-users"></i>';
                        html += '</button>';
                    }
                    if (media.staffid == <?= get_staff_user_id(); ?> || <?= is_admin() ? 'true' : 'false'; ?>) {
                        html += '<button class="btn btn-xs btn-warning" onclick="toggle_media_visibility(' + media.id + ', ' + (media.is_global == 1 ? 0 : 1) + '); event.stopPropagation();" title="<?= _l("toggle_visibility"); ?>">';
                        html += '<i class="fa-solid fa-' + (media.is_global == 1 ? 'lock' : 'globe') + '"></i>';
                        html += '</button>';
                        html += '<button class="btn btn-xs btn-danger" onclick="delete_media(' + media.id + '); event.stopPropagation();" title="<?= _l("delete"); ?>">';
                        html += '<i class="fa-solid fa-trash"></i>';
                        html += '</button>';
                    }
                    html += '</div>';
                    html += '</div>';
                    if (media.description) {
                        html += '<div><small>' + escapeHtml(media.description) + '</small></div>';
                    }
                    html += previewHtml;
                    html += '</div>';
                });
                $('#mediaLibraryList').html(html);
            } else {
                $('#mediaLibraryList').html('<div class="text-center" style="padding: 40px;"><i class="fa-solid fa-inbox fa-3x" style="opacity: 0.3;"></i><br><br><?= _l("no_media_found"); ?></div>');
            }
        },
        error: function() {
            $('#mediaLibraryList').html('<div class="text-center text-danger"><i class="fa-solid fa-exclamation-triangle"></i><br><?= _l("error_loading_media"); ?></div>');
        }
    });
}

function send_media_to_chat(mediaId, filePath, fileType) {
    var phoneNumber = $("input[name='phonenumber']").val();
    
    if (!phoneNumber) {
        alert_float('warning', '<?= _l("select_contact_first"); ?>');
        $('#modalMediaLibrary').modal('hide');
        return;
    }
    
    // Note: The action parameter is used for determining media type on backend
    // But the actual media_type sent to API must be one of: image, document, video, audio
    // We pass the fileType directly and let the backend determine the correct enum value
    var action = fileType || 'document';
    
    // Close modal
    $('#modalMediaLibrary').modal('hide');
    
    // Show loading indicator in chat area
    var loadingHtml = '<div class="chat-message loading-message" style="padding: 15px; margin: 10px 0; background: #e3f2fd; border-left: 4px solid #2196F3; border-radius: 5px; text-align: center; clear: both;">' +
        '<i class="fa fa-spinner fa-spin" style="margin-right: 8px; color: #2196F3; font-size: 16px;"></i>' +
        '<span style="color: #1976D2; font-weight: 500;"><?= _l("sending_media"); ?>...</span>' +
        '</div>';
    
    // Find chat messages container - use the actual chat messages area
    var chatContainer = $('#retorno');
    if (chatContainer.length === 0) {
        // Fallback to chat-body section
        chatContainer = $('.chat-body section, .chat-body #retorno');
    }
    if (chatContainer.length === 0) {
        // Last fallback
        chatContainer = $('.chat-body');
    }
    
    if (chatContainer.length > 0) {
        chatContainer.append(loadingHtml);
        // Scroll to bottom to show loading indicator
        var scrollContainer = chatContainer.closest('.chat-body');
        if (scrollContainer.length > 0) {
            scrollContainer.scrollTop(scrollContainer[0].scrollHeight);
        } else {
            chatContainer.scrollTop(chatContainer[0].scrollHeight);
        }
    } else {
        // Fallback: show alert
        alert_float('info', '<?= _l("sending_media"); ?>...');
    }
    
    // Send media file using the chat form
    var formData = new FormData();
    
    formData.append('phonenumber', phoneNumber);
    formData.append('action', action);
    formData.append('staffid', <?= get_staff_user_id(); ?>);
    formData.append('device_id', <?= $device->dev_id; ?>); // Use current device ID
    formData.append('media_file_path', filePath);
    
    // Add CSRF token
    var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    formData.append(csrfTokenName, csrfHash);
    
    // Log for debugging
    console.log('Media library: Sending media to chat', {
        mediaId: mediaId,
        filePath: filePath,
        fileType: fileType,
        phoneNumber: phoneNumber,
        action: action
    });
    
    // Use AJAX to send the media
    $.ajax({
        url: site_url + 'admin/contactcenter/ajax_send_media_from_library',
        data: formData,
        type: 'POST',
        dataType: 'json',
        contentType: false,
        processData: false,
        beforeSend: function() {
            console.log('Media library: AJAX request starting...');
        },
        success: function(data) {
            // Remove loading indicator
            $('.loading-message').remove();
            
            console.log('Media library: AJAX response received');
            console.log('Media library: Full response JSON:', JSON.stringify(data, null, 2));
            
            if (data.send) {
                console.log('Media library: Media sent successfully!');
                alert_float('success', '<?= _l("media_sent_successfully"); ?>');
                // Refresh chat
                if (typeof get_message_contact_search === 'function') {
                    setTimeout(function() {
                        get_message_contact_search(phoneNumber, '<?= $devicetoken; ?>', 0, null);
                    }, 500);
                }
            } else {
                console.error('Media library: Send failed');
                console.error('Media library: Full error data JSON:', JSON.stringify(data, null, 2));
                console.error('Media library: Error message:', data.error);
                
                if (data.debug) {
                    console.error('Media library: Debug info JSON:', JSON.stringify(data.debug, null, 2));
                    
                    if (data.debug.send_result) {
                        console.error('Media library: Send result from API JSON:', JSON.stringify(data.debug.send_result, null, 2));
                        
                        // Try to extract more specific error from send_result
                        if (data.debug.send_result.response) {
                            console.error('Media library: API response JSON:', JSON.stringify(data.debug.send_result.response, null, 2));
                            
                            // Try to get error message from response
                            if (data.debug.send_result.response.message) {
                                var apiError = Array.isArray(data.debug.send_result.response.message) 
                                    ? data.debug.send_result.response.message.join(', ') 
                                    : data.debug.send_result.response.message;
                                console.error('Media library: API error message:', apiError);
                            }
                        }
                    }
                }
                
                // Show more detailed error if available
                var errorMsg = data.error || '<?= _l("error_sending_media"); ?>';
                alert_float('warning', errorMsg);
            }
        },
        error: function(xhr, status, error) {
            console.error('Media library: AJAX error', {
                xhr: xhr,
                status: status,
                error: error,
                responseText: xhr.responseText,
                responseJSON: xhr.responseJSON
            });
            // Remove loading indicator on error
            $('.loading-message').remove();
            if (xhr.responseJSON && xhr.responseJSON.error) {
                alert_float('danger', xhr.responseJSON.error);
            } else {
                alert_float('danger', '<?= _l("error_sending_media"); ?>');
            }
        }
    });
}

function toggle_media_visibility(mediaId, newVisibility) {
    // Get CSRF token
    var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    var postData = {
        media_id: mediaId,
        device_id: mediaLibraryDeviceId,
        is_global: newVisibility
    };
    postData[csrfTokenName] = csrfHash;
    
    $.ajax({
        url: '<?= admin_url("contactcenter/ajax_toggle_media_visibility"); ?>',
        method: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', '<?= _l("visibility_updated"); ?>');
                load_media_library();
            } else {
                alert_float('warning', response.error || '<?= _l("error_updating_visibility"); ?>');
            }
        },
        error: function() {
            alert_float('danger', '<?= _l("error_updating_visibility"); ?>');
        }
    });
}

function delete_media(mediaId) {
    if (!confirm('<?= _l("confirm_delete_media"); ?>')) {
        return;
    }
    
    // Get CSRF token
    var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    var postData = {
        media_id: mediaId,
        device_id: mediaLibraryDeviceId
    };
    postData[csrfTokenName] = csrfHash;
    
    $.ajax({
        url: '<?= admin_url("contactcenter/ajax_delete_media_library"); ?>',
        method: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert_float('success', '<?= _l("media_deleted_successfully"); ?>');
                load_media_library();
            } else {
                alert_float('warning', response.error || '<?= _l("error_deleting_media"); ?>');
            }
        },
        error: function() {
            alert_float('danger', '<?= _l("error_deleting_media"); ?>');
        }
    });
}

// Handle filter change
$('#mediaLibraryFilter').on('change', function() {
    load_media_library();
});

// Handle file upload for new chat
$('#newChatMediaFile').on('change', function() {
    var file = this.files[0];
    if (!file) {
        return;
    }
    
    // Show file name
    $('#newChatMediaFileName').text(file.name);
    $('#newChatSelectedMedia').show();
    
    // Upload file immediately
    var formData = new FormData();
    formData.append('media_file', file);
    formData.append('title', file.name);
    formData.append('description', '');
    formData.append('is_global', 0);
    
    // Add CSRF token
    var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    formData.append(csrfTokenName, csrfHash);
    
    $.ajax({
        url: '<?= admin_url("contactcenter/ajax_upload_media_library"); ?>',
        data: formData,
        type: 'POST',
        dataType: 'json',
        contentType: false,
        processData: false,
        beforeSend: function() {
            $('#newChatSelectedMedia').html('<div class="alert alert-info" style="margin-bottom: 0;"><i class="fa fa-spinner fa-spin"></i> <?= _l("uploading"); ?>...</div>');
        },
        success: function(response) {
            console.log('New chat - File upload response:', response);
            if (response.success && response.media) {
                // Store media info
                $('#newChatMediaId').val(response.media.id || '');
                $('#newChatMediaPath').val(response.media.file_path || '');
                $('#newChatMediaType').val(response.media.file_type || '');
                
                // Debug: Log the values being set
                console.log('New chat - Media uploaded and set:', {
                    mediaId: $('#newChatMediaId').val(),
                    mediaPath: $('#newChatMediaPath').val(),
                    mediaType: $('#newChatMediaType').val()
                });
                
                $('#newChatSelectedMedia').html('<div class="alert alert-success" style="margin-bottom: 0; display: flex; align-items: center; justify-content: space-between;"><button type="button" class="btn btn-danger btn-sm" onclick="clear_new_chat_media()" style="margin-right: 10px;"><i class="fa-solid fa-times"></i> <?= _l("close"); ?></button><span style="flex: 1;"><i class="fa-solid fa-file"></i> ' + escapeHtml(file.name) + '</span></div>');
                alert_float('success', '<?= _l("media_uploaded_successfully"); ?>');
            } else {
                $('#newChatSelectedMedia').hide();
                $('#newChatMediaId').val('');
                $('#newChatMediaPath').val('');
                $('#newChatMediaType').val('');
                console.error('New chat - Upload failed:', response);
                alert_float('warning', response.error || '<?= _l("error_uploading_media"); ?>');
            }
        },
        error: function() {
            $('#newChatSelectedMedia').hide();
            alert_float('danger', '<?= _l("error_uploading_media"); ?>');
        }
    });
});

// Handle upload form
$('#formUploadMedia').on('submit', function(e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    
    $.ajax({
        url: '<?= admin_url("contactcenter/ajax_upload_media_library"); ?>',
        data: formData,
        type: 'POST',
        dataType: 'json',
        contentType: false,
        processData: false,
        beforeSend: function() {
            $('#formUploadMedia button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?= _l("uploading"); ?>...');
        },
        success: function(response) {
            if (response.success) {
                alert_float('success', '<?= _l("media_uploaded_successfully"); ?>');
                $('#formUploadMedia')[0].reset();
                // Switch to library tab and reload
                $('a[href="#tabMediaLibrary"]').tab('show');
                load_media_library();
            } else {
                alert_float('warning', response.error || '<?= _l("error_uploading_media"); ?>');
            }
        },
        error: function() {
            alert_float('danger', '<?= _l("error_uploading_media"); ?>');
        },
        complete: function() {
            $('#formUploadMedia button[type="submit"]').prop('disabled', false).html('<i class="fa-solid fa-upload"></i> <?= _l("upload"); ?>');
        }
    });
});

// Bulk Send Media Functions
var bulkSendQueue = [];
var bulkSendInterval = null;
var bulkSendProgress = { current: 0, total: 0 };

// LocalStorage keys for persistence
var BULK_SEND_STORAGE_KEY = 'contactcenter_bulk_send_queue';
var BULK_SEND_PROGRESS_KEY = 'contactcenter_bulk_send_progress';

// Save bulk send state to localStorage
function save_bulk_send_state() {
    try {
        localStorage.setItem(BULK_SEND_STORAGE_KEY, JSON.stringify(bulkSendQueue));
        localStorage.setItem(BULK_SEND_PROGRESS_KEY, JSON.stringify(bulkSendProgress));
    } catch (e) {
        console.error('Error saving bulk send state:', e);
    }
}

// Load bulk send state from localStorage
function load_bulk_send_state() {
    try {
        var savedQueue = localStorage.getItem(BULK_SEND_STORAGE_KEY);
        var savedProgress = localStorage.getItem(BULK_SEND_PROGRESS_KEY);
        
        if (savedQueue && savedProgress) {
            bulkSendQueue = JSON.parse(savedQueue);
            bulkSendProgress = JSON.parse(savedProgress);
            return true;
        }
    } catch (e) {
        console.error('Error loading bulk send state:', e);
    }
    return false;
}

// Clear bulk send state from localStorage
function clear_bulk_send_state() {
    try {
        localStorage.removeItem(BULK_SEND_STORAGE_KEY);
        localStorage.removeItem(BULK_SEND_PROGRESS_KEY);
        localStorage.removeItem('contactcenter_bulk_send_last_update');
    } catch (e) {
        console.error('Error clearing bulk send state:', e);
    }
}

// Cancel bulk send
function cancel_bulk_send() {
    if (bulkSendInterval) {
        clearInterval(bulkSendInterval);
        bulkSendInterval = null;
    }
    bulkSendQueue = [];
    bulkSendProgress = { current: 0, total: 0 };
    clear_bulk_send_state();
    hide_bulk_send_progress();
    alert_float('info', '<?= _l("bulk_send_cancelled"); ?>');
}

// Resume bulk send on page load
function resume_bulk_send() {
    if (load_bulk_send_state() && bulkSendQueue.length > 0) {
        console.log('Resuming bulk send:', bulkSendProgress.current + '/' + bulkSendProgress.total);
        
        // Show progress indicator
        show_bulk_send_progress();
        
        // Calculate time until next send (if we just sent one, wait the full minute)
        // Otherwise, send immediately
        var timeSinceLastUpdate = 0;
        try {
            var lastUpdate = localStorage.getItem('contactcenter_bulk_send_last_update');
            if (lastUpdate) {
                timeSinceLastUpdate = Date.now() - parseInt(lastUpdate);
            }
        } catch (e) {
            console.error('Error checking last update time:', e);
        }
        
        // If less than a minute has passed, wait the remainder
        var waitTime = Math.max(0, 60000 - timeSinceLastUpdate);
        
        setTimeout(function() {
            // Send immediately, then continue with interval
            send_next_bulk_media();
            bulkSendInterval = setInterval(send_next_bulk_media, 60000); // 1 minute = 60000ms
        }, waitTime);
    }
}

function open_bulk_send_modal(mediaId, filePath, fileType) {
    // Close media library modal first
    $('#modalMediaLibrary').modal('hide');
    
    $('#bulkMediaId').val(mediaId);
    $('#bulkMediaPath').val(filePath);
    $('#bulkMediaType').val(fileType);
    $('#bulkSelectedContacts').html('<div class="text-muted text-center"><?= _l("no_contacts_selected"); ?></div>');
    $('#bulkSelectedCount').text('0');
    $('#btnBulkSend').prop('disabled', true);
    $('#modalBulkSendMedia').modal('show');
    
    // Load contacts
    load_bulk_chats();
    load_bulk_leads();
    load_bulk_clients();
    load_bulk_staff();
}

function load_bulk_chats() {
    // Get active chats from the contact list
    var chats = [];
    $('#contaChat tbody tr').each(function() {
        var $row = $(this);
        
        // Get phone from data-id attribute (most reliable)
        var phone = $row.attr('data-id') || $row.data('id') || '';
        
        // Clean phone number (remove non-digits)
        if (phone) {
            phone = phone.replace(/\D/g, '');
        }
        
        // Get name from h1 tag inside the first td (this is the contact/lead name)
        var $firstTd = $row.find('td').first();
        var $h1 = $firstTd.find('h1');
        var name = '';
        
        if ($h1.length > 0) {
            // Clone the h1 element to preserve structure, then remove icons/badges
            var $h1Clone = $h1.clone();
            // Remove icon elements
            $h1Clone.find('i, .fa, .badge, span[class*="icon"]').remove();
            name = $h1Clone.text().trim();
        }
        
        // If no name found, try to get from lead span or use phone as fallback
        if (!name || name.length === 0) {
            // Try to get lead name from lead span
            var leadSpan = $firstTd.find('.lead-name, [class*="lead"]').text().trim();
            if (leadSpan) {
                name = leadSpan;
            } else {
                name = 'Contact ' + phone;
            }
        }
        
        // Get lead ID for additional info
        var leadId = $row.attr('data-lead-id') || $row.data('lead-id') || '';
        var contactId = $row.attr('data-contact-id') || $row.data('contact-id') || '';
        
        // Build display name with lead info if available
        var displayName = name;
        if (leadId && leadId !== '0' && leadId !== '') {
            displayName = name + ' (Lead Id: ' + leadId + ')';
        }
        
        if (phone && phone.length >= 10 && phone !== '-') {
            chats.push({
                phone: phone,
                name: displayName,
                type: 'chat',
                contactId: contactId,
                leadId: leadId
            });
        }
    });
    
    if (chats.length === 0) {
        $('#bulkChatsList').html('<div class="text-center text-muted"><?= _l("no_items_found"); ?></div>');
    } else {
        render_bulk_list(chats, 'bulkChatsList', 'chat');
    }
}

function load_bulk_leads() {
    // Search leads via AJAX
    var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    var postData = {
        search: '',
        limit: 100
    };
    postData[csrfTokenName] = csrfHash;
    
    $.ajax({
        url: site_url + 'admin/contactcenter/ajax_search_leads',
        method: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.results && response.results.length > 0) {
                var leads = response.results.map(function(lead) {
                    return {
                        phone: lead.phonenumber ? lead.phonenumber.replace(/\D/g, '') : '',
                        name: lead.name || lead.company || 'Lead #' + lead.id,
                        type: 'lead',
                        id: lead.id
                    };
                }).filter(function(item) { 
                    return item.phone && item.phone.length >= 10; 
                });
                
                if (leads.length > 0) {
                    render_bulk_list(leads, 'bulkLeadsList', 'lead');
                } else {
                    $('#bulkLeadsList').html('<div class="text-center text-muted"><?= _l("no_leads_found"); ?></div>');
                }
            } else {
                $('#bulkLeadsList').html('<div class="text-center text-muted"><?= _l("no_leads_found"); ?></div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading leads:', xhr, status, error);
            $('#bulkLeadsList').html('<div class="text-center text-danger"><?= _l("error_loading_leads"); ?></div>');
        }
    });
}

function load_bulk_clients() {
    // Search clients via AJAX
    var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    var postData = {
        search: '',
        limit: 100
    };
    postData[csrfTokenName] = csrfHash;
    
    $.ajax({
        url: site_url + 'admin/contactcenter/ajax_search_clients',
        method: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.results && response.results.length > 0) {
                var clients = [];
                response.results.forEach(function(item) {
                    // The API returns formatted results with phonenumber, fullname, etc.
                    if (item.phonenumber) {
                        var phone = item.phonenumber.replace(/\D/g, '');
                        if (phone.length >= 10) {
                            clients.push({
                                phone: phone,
                                name: item.fullname || item.company || (item.firstname + ' ' + item.lastname).trim() || 'Client',
                                type: 'client',
                                id: item.userid || item.contact_id
                            });
                        }
                    }
                });
                
                if (clients.length > 0) {
                    render_bulk_list(clients, 'bulkClientsList', 'client');
                } else {
                    $('#bulkClientsList').html('<div class="text-center text-muted"><?= _l("no_clients_found"); ?></div>');
                }
            } else {
                $('#bulkClientsList').html('<div class="text-center text-muted"><?= _l("no_clients_found"); ?></div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading clients:', xhr, status, error);
            $('#bulkClientsList').html('<div class="text-center text-danger"><?= _l("error_loading_clients"); ?></div>');
        }
    });
}

function load_bulk_staff() {
    // Get staff members - they should have phone numbers in their profile
    var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    var postData = {
        search: ''
    };
    postData[csrfTokenName] = csrfHash;
    
    $.ajax({
        url: site_url + 'admin/contactcenter/ajax_search_staff',
        method: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.results && response.results.length > 0) {
                var staff = [];
                response.results.forEach(function(item) {
                    if (item.phonenumber) {
                        var phone = item.phonenumber.replace(/\D/g, '');
                        if (phone.length >= 10) {
                            staff.push({
                                phone: phone,
                                name: item.firstname + ' ' + item.lastname,
                                type: 'staff',
                                id: item.staffid
                            });
                        }
                    }
                });
                
                if (staff.length > 0) {
                    render_bulk_list(staff, 'bulkStaffList', 'staff');
                } else {
                    $('#bulkStaffList').html('<div class="text-center text-muted"><?= _l("no_staff_found"); ?></div>');
                }
            } else {
                $('#bulkStaffList').html('<div class="text-center text-muted"><?= _l("no_staff_found"); ?></div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading staff:', xhr, status, error);
            $('#bulkStaffList').html('<div class="text-center text-danger"><?= _l("error_loading_staff"); ?></div>');
        }
    });
}

function render_bulk_list(items, containerId, type) {
    var html = '';
    if (items.length === 0) {
        html = '<div class="text-center text-muted"><?= _l("no_items_found"); ?></div>';
    } else {
        items.forEach(function(item) {
            var key = type + '_' + item.phone;
            html += '<div class="bulk-contact-item" data-key="' + key + '" data-phone="' + item.phone + '" data-name="' + escapeHtml(item.name) + '" style="padding: 8px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; background: white;">';
            html += '<label style="margin: 0; cursor: pointer; width: 100%; color: #333;">';
            html += '<input type="checkbox" class="bulk-contact-checkbox" data-key="' + key + '" data-phone="' + item.phone + '" data-name="' + escapeHtml(item.name) + '" onchange="update_bulk_selected()" style="margin-right: 8px;">';
            html += '<strong style="color: #333;">' + escapeHtml(item.name) + '</strong><br>';
            html += '<small style="color: #666;">' + item.phone + '</small>';
            html += '</label>';
            html += '</div>';
        });
    }
    $('#' + containerId).html(html);
}

function update_bulk_selected() {
    var selected = [];
    $('.bulk-contact-checkbox:checked').each(function() {
        selected.push({
            phone: $(this).data('phone'),
            name: $(this).data('name')
        });
    });
    
    $('#bulkSelectedCount').text(selected.length);
    $('#btnBulkSend').prop('disabled', selected.length === 0);
    
    if (selected.length === 0) {
        $('#bulkSelectedContacts').html('<div class="text-muted text-center"><?= _l("no_contacts_selected"); ?></div>');
    } else {
        var html = '';
        selected.forEach(function(contact) {
            html += '<span class="badge badge-info" style="margin: 3px; padding: 5px 10px;">' + escapeHtml(contact.name) + ' (' + contact.phone + ')</span>';
        });
        $('#bulkSelectedContacts').html(html);
    }
}

function start_bulk_send() {
    var selected = [];
    $('.bulk-contact-checkbox:checked').each(function() {
        selected.push({
            phone: $(this).data('phone'),
            name: $(this).data('name')
        });
    });
    
    if (selected.length === 0) {
        alert_float('warning', '<?= _l("select_at_least_one_contact"); ?>');
        return;
    }
    
    var mediaId = $('#bulkMediaId').val();
    var filePath = $('#bulkMediaPath').val();
    var fileType = $('#bulkMediaType').val();
    
    // Initialize queue
    bulkSendQueue = selected.map(function(contact) {
        return {
            phone: contact.phone,
            name: contact.name,
            mediaId: mediaId,
            filePath: filePath,
            fileType: fileType
        };
    });
    
    bulkSendProgress.current = 0;
    bulkSendProgress.total = bulkSendQueue.length;
    
    // Save state to localStorage
    save_bulk_send_state();
    
    // Close modals
    $('#modalBulkSendMedia').modal('hide');
    $('#modalMediaLibrary').modal('hide');
    
    // Show progress indicator
    show_bulk_send_progress();
    
    // Start sending (one per minute = 60000ms)
    send_next_bulk_media();
    bulkSendInterval = setInterval(send_next_bulk_media, 60000); // 1 minute = 60000ms
}

function send_next_bulk_media() {
    if (bulkSendQueue.length === 0) {
        // All sent
        clearInterval(bulkSendInterval);
        bulkSendInterval = null;
        update_bulk_send_progress(bulkSendProgress.total, bulkSendProgress.total);
        
        // Clear localStorage
        clear_bulk_send_state();
        
        setTimeout(function() {
            hide_bulk_send_progress();
            alert_float('success', '<?= _l("all_media_sent_successfully"); ?>');
        }, 1000);
        return;
    }
    
    var item = bulkSendQueue.shift();
    bulkSendProgress.current++;
    
    // Save state to localStorage
    save_bulk_send_state();
    try {
        localStorage.setItem('contactcenter_bulk_send_last_update', Date.now().toString());
    } catch (e) {
        console.error('Error saving last update time:', e);
    }
    
    // Update progress
    update_bulk_send_progress(bulkSendProgress.current, bulkSendProgress.total);
    
    // Send media
    var formData = new FormData();
    formData.append('phonenumber', item.phone);
    formData.append('action', item.fileType);
    formData.append('staffid', <?= get_staff_user_id(); ?>);
    formData.append('device_id', <?= $device->dev_id; ?>);
    formData.append('media_file_path', item.filePath);
    
    // Add CSRF token
    var csrfTokenName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    formData.append(csrfTokenName, csrfHash);
    
    $.ajax({
        url: site_url + 'admin/contactcenter/ajax_send_media_from_library',
        data: formData,
        type: 'POST',
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function(data) {
            if (!data.send) {
                console.error('Bulk send failed for', item.phone, data.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Bulk send error for', item.phone, error);
        }
    });
}

function show_bulk_send_progress() {
    // Ensure bulkSendProgress is initialized
    if (typeof bulkSendProgress === 'undefined' || !bulkSendProgress) {
        bulkSendProgress = { current: 0, total: 0 };
    }
    
    // Show the progress container in the footer bar
    $('#bulkSendProgressContainer').show();
    // Reset progress - ensure total is a valid number
    var total = parseInt(bulkSendProgress.total) || 0;
    update_bulk_send_progress(0, total);
}

function update_bulk_send_progress(current, total) {
    // Ensure we have valid numbers
    current = parseInt(current) || 0;
    total = parseInt(total) || 0;
    
    if (isNaN(current) || isNaN(total)) {
        console.error('update_bulk_send_progress called with invalid values:', {current: current, total: total});
        return;
    }
    
    var percentage = total > 0 ? Math.round((current / total) * 100) : 0;
    
    // Update progress bar width and data attributes (like dashboard)
    $('#bulkProgressBar').css('width', percentage + '%');
    $('#bulkProgressBar').attr('aria-valuenow', percentage);
    $('#bulkProgressBar').attr('data-percent', percentage);
    
    // Update text displays (outside the bar, like dashboard)
    $('#bulkProgressText').text(current + '/' + total);
    $('#bulkProgressPercent').text(percentage + '%');
}

function hide_bulk_send_progress() {
    $('#bulkSendProgressContainer').fadeOut(300, function() {
        // Reset progress text
        $('#bulkProgressBar').css('width', '0%');
        $('#bulkProgressText').text('0/0');
    });
}

// Campaign Queue Status Functions
function load_campaign_queue_status() {
    var deviceId = $('input[name="device_id"]').val() || <?= $device->dev_id; ?>;
    
    if (!deviceId) {
        return;
    }
    
    $.ajax({
        url: site_url + 'admin/contactcenter/ajax_get_campaign_queue_status',
        method: 'POST',
        data: {
            device_id: deviceId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (response.has_campaign) {
                    // Show campaign queue indicator
                    $('#campaignQueueName').text(response.campaign_name || '<?= _l("active_campaign"); ?>');
                    $('#campaignQueueText').text(response.pending_count + ' <?= _l("pending_messages"); ?>');
                    $('#campaignQueueProgressContainer').show();
                } else {
                    // Hide if no active campaign
                    $('#campaignQueueProgressContainer').hide();
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading campaign queue status:', error);
            // Don't show error to user, just hide the indicator
            $('#campaignQueueProgressContainer').hide();
        }
    });
}

// Follow-up Queue Status Functions
function load_followup_queue_status() {
    var deviceId = $('input[name="device_id"]').val() || <?= $device->dev_id; ?>;
    
    if (!deviceId) {
        return;
    }
    
    $.ajax({
        url: site_url + 'admin/contactcenter/ajax_get_followup_queue_status',
        method: 'POST',
        data: {
            device_id: deviceId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (response.has_followup) {
                    // Show follow-up queue indicator
                    $('#followupQueueName').text('<?= _l("active_followup"); ?>');
                    $('#followupQueueText').text(response.pending_count + ' <?= _l("pending_leads"); ?>');
                    $('#followupQueueProgressContainer').show();
                } else {
                    // Hide if no active follow-up
                    $('#followupQueueProgressContainer').hide();
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading follow-up queue status:', error);
            // Don't show error to user, just hide the indicator
            $('#followupQueueProgressContainer').hide();
        }
    });
}

// Search filters for bulk send
$('#bulkSearchChats').on('input', function() {
    var search = $(this).val().toLowerCase();
    $('.bulk-contact-item').each(function() {
        var text = $(this).text().toLowerCase();
        $(this).toggle(text.indexOf(search) !== -1);
    });
});

$('#bulkSearchLeads').on('input', function() {
    var search = $(this).val().toLowerCase();
    $('.bulk-contact-item').each(function() {
        var text = $(this).text().toLowerCase();
        $(this).toggle(text.indexOf(search) !== -1);
    });
});

$('#bulkSearchClients').on('input', function() {
    var search = $(this).val().toLowerCase();
    $('#bulkClientsList .bulk-contact-item').each(function() {
        var text = $(this).text().toLowerCase();
        $(this).toggle(text.indexOf(search) !== -1);
    });
});

$('#bulkSearchStaff').on('input', function() {
    var search = $(this).val().toLowerCase();
    $('#bulkStaffList .bulk-contact-item').each(function() {
        var text = $(this).text().toLowerCase();
        $(this).toggle(text.indexOf(search) !== -1);
    });
});
</script>

<?php if (is_admin()) { ?>
<!-- Device Modal -->
<div class="modal fade" id="modalDevice" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title " id="exampleModalLongTitle"><?= _l("contac_device_new"); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart(admin_url('contactcenter/add_device'), ["id" => "form_add_device"]); ?>
                <input type="hidden" name="dev_id" value="" />
                <div class="form-group">
                    <label><?= _l("contac_device_name"); ?></label>
                    <input type="text" class="form-control" name="dev_name" placeholder="<?= _l("contac_device_name"); ?>" required>
                </div>
                <div class="form-group">
                    <label><?= _l("contac_number_phone"); ?></label>
                    <input type="text" class="form-control" name="dev_number" placeholder="+5517991191234" required>
                </div>
                <div class="form-group">
                    <label><?= _l("contac_phone_type"); ?></label>
                    <select name="dev_type" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value="2"><?= _l("contac_phone_type_individual"); ?></option>
                        <option value="1"><?= _l("contac_phone_type_system"); ?></option>
                        <option value="3"><?= _l("contac_phone_type_multiple"); ?></option>
                        <option value="4"><?= _l("contac_phone_type_api"); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label><?= _l("contac_on_ai"); ?></label>
                    <select id="dev_openai" name="dev_openai" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value="1"><?= _l("drawflow_ia"); ?></option>
                        <option value="2"><?= _l("drawflow_ia_fluxo"); ?></option>
                        <option value="0"><?= _l("contac_no"); ?></option>
                    </select>
                </div>

                <div class="form-group" id="chatbot_id">
                    <label><?= _l("drawflow_flow"); ?></label>
                    <select name="chatbot_id" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php if (isset($drawflow) && is_array($drawflow)) { ?>
                            <?php foreach ($drawflow as $chatbot) { ?>
                                <option value="<?= $chatbot->draw_id  ?>"><?= $chatbot->title  ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><?= _l("contac_phone_user"); ?></label>
                    <select name="staffid" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                        <option></option>
                        <?php if (isset($members) && is_array($members)) { ?>
                            <?php foreach ($members as $member) { ?>
                                <option value="<?php echo $member['staffid']; ?>">
                                    <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                </option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>API Server</label>
                    <select name="server_id" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                        <option></option>
                        <?php if (isset($servers) && is_array($servers)) { ?>
                            <?php foreach ($servers as $server) {  ?>
                                <option value="<?php echo $server->id; ?>">
                                    <?php echo $server->name . ' - v' . $server->version; ?>
                                </option>
                            <?php  } ?>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group" id="dev_instance_name">
                    <label><?= _l("contac_name_instance_device"); ?></label>
                    <input type="text" class="form-control" name="dev_instance_name" placeholder="<?= _l("contac_name_instance_device"); ?>" required>
                </div>

                <div class="form-group">
                    <label><?= _l("contac_number_token"); ?></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="dev_token" placeholder="<?= _l("contac_number_token"); ?>" required>
                        <span class="input-group-addon" data-toggle="tooltip" data-title="<?= _l("contac_generate_token"); ?>">
                            <a href="#" class="generate_password" onclick="get_guid();return false;"><i class="fa fa-refresh"></i></a>
                        </span>
                    </div>
                </div>

                <?php if (get_option("active_audio_contactcenter_elevenlabs") == 1) { ?>
                    <div class="form-group">
                        <label><?= _l("contac_token_voz_id"); ?></label>
                        <input type="text" class="form-control" name="dev_voz_id" placeholder="ASDACQWDAASD" required>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <label><?= _l("contact_assistant_ai_show"); ?></label>
                    <select name="assistant_ai_id" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" required>
                        <option></option>
                        <?php if (isset($assistants) && is_array($assistants)) { ?>
                            <?php foreach ($assistants as $assistant) { ?>
                                <option value="<?php echo $assistant->id; ?>">
                                    <?php echo $assistant->ai_name . ' ' . $assistant->ai_token; ?>
                                </option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><?= _l("contac_on_motor"); ?></label>
                    <select name="dev_engine" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required>
                        <option value="1"><?= _l("contac_yes"); ?></option>
                        <option value="0"><?= _l("contac_no"); ?></option>
                    </select>
                </div>

                <hr />
                <div class="form-group">
                    <label><?= _l("contac_open_contract"); ?></label>
                    <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_open_contract") ?>' data-original-title='' title=''>
                        <input type='checkbox' class='onoffswitch-checkbox ' onclick="contactcenter_status_contract()" id="status_contract">
                        <label class='onoffswitch-label' for='status_contract'></label>
                    </div>
                </div>

                <div id="contract" class="hidden">
                    <div class="form-group">
                        <label><?= _l("contac_category_contract"); ?></label>
                        <select name="contract_category" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                            <option></option>
                            <?php if (isset($category_contract) && is_array($category_contract)) { ?>
                                <?php foreach ($category_contract as $category) { ?>
                                    <option value="<?php echo $category->id; ?>">
                                        <?php echo $category->name; ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= _l("contac_model_contract"); ?></label>
                        <select name="contract_template" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                            <option></option>
                            <?php if (isset($models_contract) && is_array($models_contract)) { ?>
                                <?php foreach ($models_contract as $models) { ?>
                                    <option value="<?php echo $models->id; ?>">
                                        <?php echo $models->name; ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= _l("contac_label_msg_contract"); ?></label>
                        <textarea class="form-control" name="contract_msg" cols="5" rows="5"></textarea>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
    // Initialize device modal functionality
    $(document).ready(function() {
        // Initialize selectpickers in modal when it's shown
        $('#modalDevice').on('shown.bs.modal', function() {
            $(this).find('.selectpicker').selectpicker('refresh');
        });

        // Handle dev_openai change
        $('#modalDevice #dev_openai').on('change', function() {
            if ($(this).val() == 2) {
                $("#modalDevice #chatbot_id").css("display", "block");
                $("#modalDevice select[name='chatbot_id']").attr("required", true);
            } else {
                $("#modalDevice #chatbot_id").css("display", "none");
                $("#modalDevice select[name='chatbot_id']").attr("required", false);
            }
        });
        $("#modalDevice #chatbot_id").css("display", "none");
    });

    // Function to handle contract status toggle
    function contactcenter_status_contract() {
        if ($("#status_contract").is(":checked")) {
            $("#contract").removeClass("hidden");
        } else {
            $("#contract").addClass("hidden");
        }
    }
</script>

<!-- Omni Pilot Wizard -->
<?php $this->load->view('omni_pilot_wizard'); ?>
<link rel="stylesheet" href="<?php echo module_dir_url('contactcenter', 'assets/css/omni_pilot_wizard.css'); ?>?v=<?php echo time(); ?>">
<script src="<?php echo module_dir_url('contactcenter', 'assets/js/omni_pilot_wizard.js'); ?>?v=<?php echo time(); ?>"></script>

<?php } ?>