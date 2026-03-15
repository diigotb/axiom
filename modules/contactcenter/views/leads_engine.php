<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <lord-icon src="https://cdn.lordicon.com/uttrirxf.json" trigger="loop" delay="2000" colors="primary:#00e09b,secondary:#00e09b" style="width:50px;height:50px">
                            </lord-icon>

                            <span>
                                <?php echo _l('leads_engine_title'); ?>
                            </span>
                        </h4>
                        <hr class="hr-panel-separator" />
                        <div class="tw-mb-2 sm:tw-mb-4">
                            <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-3">
                                <div class="_buttons">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalDevice">
                                        <i class="fa-regular fa-plus tw-mr-1"></i>
                                        <?php echo _l('leads_engine_new'); ?>
                                    </button>
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-3 tw-flex-wrap">
                                    <div class="panel panel-default tw-mb-0" style="border: 1px solid #e0e0e0; border-radius: 4px; padding: 10px 15px; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        <div class="tw-flex tw-items-center tw-gap-2">
                                            <div class="checkbox tw-mb-0" style="margin: 0;">
                                                <input type="checkbox" id="filter_show_inactive" <?php echo isset($show_inactive) && $show_inactive ? 'checked' : ''; ?> onchange="update_campaign_filters()" style="margin-top: 0;">
                                                <label for="filter_show_inactive" style="font-weight: 500; margin-bottom: 0; cursor: pointer; color: #333;">
                                                    <i class="fa fa-filter tw-mr-1" style="color: #666;"></i>
                                                    <?php echo _l('show_inactive_campaigns'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tw-text-sm" style="color: #6c757d; max-width: 350px; line-height: 1.5;">
                                        <i class="fa fa-info-circle tw-mr-1" style="color: #17a2b8;"></i>
                                        <span><?php echo _l('filter_help_text'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tw-mb-3" id="bulk-actions-container" style="display: none;">
                            <div class="alert alert-info tw-flex tw-items-center tw-justify-between tw-p-3">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <span id="selected-count">0</span> <?php echo _l('campaigns_selected'); ?>
                                </div>
                                <div class="tw-flex tw-gap-2">
                                    <button class="btn btn-primary btn-sm" id="bulk-edit-selected" onclick="open_mass_edit_modal()">
                                        <i class="fa fa-edit tw-mr-1"></i> <?php echo _l('mass_edit'); ?>
                                    </button>
                                    <button class="btn btn-success btn-sm" id="bulk-start-selected" onclick="bulk_start_selected()">
                                        <i class="fa fa-play tw-mr-1"></i> <?php echo _l('start_selected'); ?>
                                    </button>
                                    <button class="btn btn-danger btn-sm" id="bulk-stop-selected" onclick="bulk_stop_selected()">
                                        <i class="fa fa-stop tw-mr-1"></i> <?php echo _l('stop_selected'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="tw-mb-3">
                            <div class="tw-flex tw-gap-2 tw-flex-wrap">
                                <button class="btn btn-success" onclick="bulk_start_all()">
                                    <i class="fa fa-play tw-mr-1"></i> <?php echo _l('start_all_campaigns'); ?>
                                </button>
                                <button class="btn btn-danger" onclick="bulk_stop_all()">
                                    <i class="fa fa-stop tw-mr-1"></i> <?php echo _l('stop_all_campaigns'); ?>
                                </button>
                            </div>
                        </div>
                        <div class="panel_s">
                            <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
                                <table id="contact_device" class="table" style="min-width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">
                                                <input type="checkbox" id="select-all-campaigns" onchange="toggle_all_campaigns(this)">
                                            </th>
                                            <th>#ID</th>
                                            <th class="text-center" style="font-weight: 600; color: #0066cc;">
                                                <i class="fa fa-comments tw-mr-1"></i><?= _l("messages"); ?>
                                            </th>
                                        <th><?= _l("leads_engine_name"); ?></th>                                       
                                        <th><?= _l("contac_conversation_not_send"); ?></th>
                                        <th><?= _l("contac_phone_status"); ?></th>
                                        <th><?= _l("contact_engine_lead_status"); ?></th>  
                                        <th><?= _l("contact_engine_lead_status_final"); ?></th>
                                        <th><?= _l("contac_conversation_start_time"); ?></th>
                                        <th><?= _l("contac_conversation_end_time"); ?></th>
                                        <th><?= _l("leads_engine_hour"); ?></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($leads_engines as $con) {
                                        extract((array) $con);
                                        echo "<tr class='engine_{$id}'>
                                                <td>
                                                    <input type='checkbox' class='campaign-checkbox' value='{$id}' onchange='update_bulk_actions()'>
                                                </td>
                                                <td>{$id} </a></td>
                                                <td class='text-center' style='vertical-align: middle;'>
                                                    <a href='" . admin_url("contactcenter/leads_engine_messages/{$id}") . "' 
                                                       class='btn btn-primary btn-sm' 
                                                       style='min-width: 100px; font-weight: 500;'
                                                       title='" . _l("view_messages") . "'>
                                                        <i class='fa fa-comments tw-mr-1'></i>
                                                        " . _l("messages") . "
                                                    </a>
                                                </td>                                                    
                                                <td> 
                                                    <div class='box_thumbTlabeCommunity'> 
                                                        <div>    
                                                            {$title}
                                                            <div class='row-options'>                                                                                                                                                           
                                                                <a href='javascript:void(0);' onclick='edit_engine({$id})'>" . _l("contac_editar") . "</a> |                         
                                                                <a href='javascript:void(0);'class='text-danger' onclick='delete_engine({$id})' >" . _l("contac_excluir") . "</a> |
                                                                <a href='javascript:void(0);'class='text-danger' onclick='duplicate_engine({$id})' >" . _l("contac_duplicate") . "</a> |
                                                                <a href='javascript:void(0);'class='text-info' onclick='replicate_engine({$id})' >" . _l("replicate") . "</a>
                                                            </div>        
                                                        </div>    
                                                    </div>    
                                                </td>                                                                                                     
                                                
                                                <td>
                                                    <div class='box-button-erros'>
                                                        <button class='btn btn-warning ' data-toggle='tooltip' data-title='Verificar se tem Leads a serem enviados' onclick='show_leads({$id})'> " . _l("contac_leads") . " </button>                                                 
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='onoffswitch' data-toggle='tooltip' data-title='" . _l("contac_public") . "' data-original-title='' title=''>
                                                        <input type='checkbox' name='status' class='onoffswitch-checkbox 'onclick='status_engine(this,{$id})'  id='{$id}' " . ($status == 1 ? 'checked' : '') . ">
                                                        <label class='onoffswitch-label' for='{$id}'></label>
                                                    </div> 
                                                </td> 
                                                <td>" . contactcenter_get_name_status_lead($leads_status) . "</td>  
                                                <td>" . contactcenter_get_name_status_lead($leads_status_final) . "</td>  
                                                <td>{$start_time}</td>
                                                <td>{$end_time}</td>
                                                <td>" . contactcenter_format_hours_friendly($hours_since_last_contact) . "</td>    
                                                                             
                                                                                                   
                                             </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                    </div>



                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalDevice" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title " id="exampleModalLongTitle"><?= _l("leads_engine_new"); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart(admin_url('contactcenter/add_leads_engine')); ?>
                <input type="hidden" name="id" value="">
                <div class="form-group">
                    <label><?= _l("contac_conversation_name"); ?></label>
                    <input type="text" class="form-control" name="title" placeholder="<?= _l("contac_conversation_name"); ?>" required>
                </div>

                <div class="form-group">
                    <label><?= _l("contact_engine_lead_status"); ?> <small class="text-muted">(<?= _l("optional"); ?>)</small></label>
                    <select name="leads_status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                        <option value=""><?= _l("all_statuses"); ?></option>
                        <?php foreach ($statuses as $status) { ?>
                            <option value="<?php echo $status['id']; ?>">
                                <?php echo $status['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                    <small class="text-muted"><?= _l("leads_engine_status_help"); ?></small>
                </div>

                <div class="form-group">
                    <label><?= _l("contact_engine_lead_status_final"); ?> <small class="text-muted">(<?= _l("optional"); ?>)</small></label>
                    <select name="leads_status_final" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                        <option value=""><?= _l("keep_current_status"); ?></option>
                        <?php foreach ($statuses as $status) { ?>
                            <option value="<?php echo $status['id']; ?>">
                                <?php echo $status['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                    <small class="text-muted"><?= _l("leads_engine_status_final_help"); ?></small>
                </div>

                <div class="form-group">
                    <label><?= _l("campaign_tag_for_leads"); ?></label>
                    <div class="input-group">
                        <select name="campaign_tag_id" id="campaign_tag_id_followup" class="form-control selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                            <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                            <?php foreach ($tagsArray as $tag) { ?>
                                <option value="<?php echo $tag['id']; ?>">
                                    <?php echo htmlspecialchars($tag['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createFollowupTagModal" title="<?php echo _l('create_new_tag'); ?>">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <small class="text-muted"><?= _l("campaign_tag_for_leads_help"); ?></small>
                </div>

                <div class="form-group">
                    <label><?= _l("tags"); ?></label>
                    <select multiple name="tags[]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">                        
                        <?php foreach ($tagsArray as $tag) { ?>
                            <option value="<?php echo $tag['id']; ?>">
                                <?php echo $tag['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><?= _l("contact"); ?></label>
                    <select name="fromMe" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" required>                       
                        <option value="1"><?= _l("contac_conversation_last_contact"); ?></option>
                        <option value="0"><?= _l("contac_conversation_last_contact_lead"); ?></option>
                    </select>
                </div>
             

                <div class="form-group">
                    <label><?= _l("contac_conversation_start_time_hour"); ?></label>
                    <input type="time" class="form-control" name="start_time" value="08:00" required>
                </div>

                <div class="form-group">
                    <label><?= _l("contac_conversation_end_time_hour"); ?></label>
                    <input type="time" class="form-control" name="end_time" value="18:00" required>
                </div>

                <div class="form-group">
                    <label><?= _l("leads_engine_hour"); ?></label>
                    <div class="row">
                        <div class="col-xs-6">
                            <input type="number" class="form-control" id="time_amount" value="1" min="1" step="1" required>
                        </div>
                        <div class="col-xs-6">
                            <select class="form-control" id="time_unit">
                                <option value="0.0166667"><?= _l("leads_engine_unit_minutes"); ?></option>
                                <option value="1"><?= _l("leads_engine_unit_hours"); ?></option>
                                <option value="24" selected><?= _l("leads_engine_unit_days"); ?></option>
                                <option value="168"><?= _l("leads_engine_unit_weeks"); ?></option>
                                <option value="720"><?= _l("leads_engine_unit_months"); ?></option>
                                <option value="8760"><?= _l("leads_engine_unit_years"); ?></option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="hours_since_last_contact" id="hours_since_last_contact_hidden" value="24">
                    <small class="text-muted" id="time_conversion_hint">= 24 <?= _l("leads_engine_unit_hours"); ?></small>
                </div>

                <!-- Safety Settings Section -->
                <hr>
                <h4 class="tw-mt-0 tw-font-semibold tw-text-base tw-mb-3">
                    <i class="fa fa-shield-alt"></i> <?= _l("safety_settings_title"); ?>
                </h4>
                
                <div class="form-group">
                    <label><?= _l("safety_settings_daily_limit"); ?> <small class="text-muted"><?= _l("safety_settings_daily_limit_desc"); ?></small></label>
                    <input type="number" class="form-control" name="daily_limit" value="1000" min="1" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?= _l("safety_settings_batch_size"); ?> <small class="text-muted"><?= _l("safety_settings_batch_size_desc"); ?></small></label>
                            <input type="number" class="form-control" name="batch_size" value="5" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?= _l("safety_settings_batch_cooldown"); ?> <small class="text-muted"><?= _l("safety_settings_batch_cooldown_desc"); ?></small></label>
                            <input type="number" class="form-control" name="batch_cooldown" value="5" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?= _l("safety_settings_message_interval_min"); ?></label>
                            <input type="number" class="form-control" name="message_interval_min" value="1" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?= _l("safety_settings_message_interval_max"); ?></label>
                            <input type="number" class="form-control" name="message_interval_max" value="3" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <small class="text-muted"><?= _l("safety_settings_message_interval_desc"); ?></small>
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" name="is_warmup_active" id="is_warmup_active" value="1">
                        <label for="is_warmup_active">
                            <strong><?= _l("safety_settings_warmup_active"); ?></strong>
                            <br><small class="text-muted"><?= _l("safety_settings_warmup_desc"); ?></small>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" name="stop_on_reply" id="stop_on_reply" value="1" checked>
                        <label for="stop_on_reply">
                            <strong><?= _l("safety_settings_stop_on_reply"); ?></strong>
                            <br><small class="text-muted"><?= _l("safety_settings_stop_on_reply_desc"); ?></small>
                        </label>
                    </div>
                </div>

                <div class="alert alert-info" data-toggle="tooltip" data-placement="top" title="Exemplo: {Olá|Oi|Bom dia}, temos uma {oferta especial|promoção|oportunidade única} para você! O sistema escolherá aleatoriamente uma opção de cada conjunto entre chaves, gerando variações como: 'Olá, temos uma oferta especial para você!' ou 'Oi, temos uma promoção para você!'">
                    <i class="fa fa-info-circle"></i> <strong><?= _l("safety_settings_spintax_support"); ?>:</strong> <?= _l("safety_settings_spintax_desc"); ?>
                    <i class="fa fa-question-circle" style="cursor: help; margin-left: 5px;" data-toggle="tooltip" data-placement="top" title="Exemplo: {Olá|Oi|Bom dia}, temos uma {oferta especial|promoção|oportunidade única} para você! O sistema escolherá aleatoriamente uma opção de cada conjunto entre chaves, gerando variações como: 'Olá, temos uma oferta especial para você!' ou 'Oi, temos uma promoção para você!'"></i>
                </div>

                <hr>
                <h4 class="tw-mt-0 tw-font-semibold tw-text-base tw-mb-3">
                    <i class="fa fa-shield-alt"></i> Estratégias Avançadas Anti-Ban
                </h4>

                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" name="vcard_enable" id="vcard_enable" value="1">
                        <label for="vcard_enable">
                            <strong><?= _l("campaign_vcard_enable"); ?></strong>
                            <br><small class="text-muted"><?= _l("campaign_vcard_desc"); ?></small>
                        </label>
                    </div>
                </div>

                <div id="vcard_fields" style="display: none; margin-left: 20px;">
                    <div class="form-group">
                        <label><?= _l("campaign_vcard_name"); ?></label>
                        <input type="text" class="form-control" name="vcard_name" placeholder="Nome da Empresa">
                    </div>
                    <div class="form-group">
                        <label><?= _l("campaign_vcard_phone"); ?></label>
                        <input type="text" class="form-control" name="vcard_phone" placeholder="5511999999999">
                        <small class="text-muted">Formato: Código do país + DDD + Número (ex: 5511999999999)</small>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" name="inbound_bait_enable" id="inbound_bait_enable" value="1">
                        <label for="inbound_bait_enable">
                            <strong><?= _l("campaign_inbound_bait_enable"); ?></strong>
                            <br><small class="text-muted"><?= _l("campaign_inbound_bait_desc"); ?></small>
                        </label>
                    </div>
                </div>

                <div id="inbound_bait_fields" style="display: none; margin-left: 20px;">
                    <div class="form-group">
                        <label><?= _l("campaign_inbound_bait_message"); ?></label>
                        <input type="text" class="form-control" name="inbound_bait_message" placeholder="Oi, é o Carlos?">
                        <small class="text-muted"><?= _l("campaign_inbound_bait_example"); ?></small>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" name="safe_groups_enable" id="safe_groups_enable" value="1">
                        <label for="safe_groups_enable">
                            <strong><?= _l("campaign_safe_groups_enable"); ?></strong>
                            <br><small class="text-muted"><?= _l("campaign_safe_groups_desc"); ?></small>
                        </label>
                    </div>
                </div>

                <hr>
                
                <div class="form-group">
                    <label><?= _l("contact_group_device"); ?></label>
                    <select name="device_id" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" required>
                        <option></option>
                        <?php foreach ($devices as $device) { ?>
                            <option value="<?php echo $device->dev_id; ?>">
                                <?php echo $device->dev_name ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
              
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="leadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="width:70%">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title " id="engine_name"></h4>
            </div>
            <div class="modal-body">
                <div class="load-get-msg" style="display: none;">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <table id="engineLeads" class="table">
                    <thead>
                        <tr>
                            <th><?= _l("contactcenter_lead_id"); ?></th>
                            <th><?= _l("contactcenter_lead_name"); ?></th>
                            <th><?= _l("contactcenter_lead_phonenumber"); ?></th>
                            <th><?= _l("contac_phone_user"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Mass Edit Modal -->
<div class="modal fade" id="massEditModal" tabindex="-1" role="dialog" aria-labelledby="massEditModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="massEditModalTitle"><?= _l("mass_edit_campaigns"); ?></h4>
            </div>
            <div class="modal-body">
                <p class="text-muted"><?= _l("mass_edit_desc"); ?>: <strong id="mass-edit-count">0</strong> <?= _l("campaigns"); ?></p>
                <hr>
                <form id="mass-edit-form">
                    <div class="form-group">
                        <label><?= _l("contact_group_device"); ?></label>
                        <select name="device_id" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('keep_current_value'); ?>">
                            <option value=""><?php echo _l('keep_current_value'); ?></option>
                            <?php foreach ($devices as $device) { ?>
                                <option value="<?php echo $device->dev_id; ?>">
                                    <?php echo $device->dev_name ?>
                                </option>
                            <?php } ?>
                        </select>
                        <small class="text-muted"><?= _l("mass_edit_device_desc"); ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= _l("contac_conversation_start_time_hour"); ?></label>
                        <input type="time" class="form-control" name="start_time" placeholder="<?= _l('keep_current_value'); ?>">
                        <small class="text-muted"><?= _l("mass_edit_time_desc"); ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= _l("contac_conversation_end_time_hour"); ?></label>
                        <input type="time" class="form-control" name="end_time" placeholder="<?= _l('keep_current_value'); ?>">
                        <small class="text-muted"><?= _l("mass_edit_time_desc"); ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= _l("leads_engine_hour"); ?></label>
                        <div class="row">
                            <div class="col-xs-6">
                                <input type="number" class="form-control mass-time-amount" min="1" step="1" placeholder="<?= _l('keep_current_value'); ?>">
                            </div>
                            <div class="col-xs-6">
                                <select class="form-control mass-time-unit">
                                    <option value="0.0166667"><?= _l("leads_engine_unit_minutes"); ?></option>
                                    <option value="1"><?= _l("leads_engine_unit_hours"); ?></option>
                                    <option value="24" selected><?= _l("leads_engine_unit_days"); ?></option>
                                    <option value="168"><?= _l("leads_engine_unit_weeks"); ?></option>
                                    <option value="720"><?= _l("leads_engine_unit_months"); ?></option>
                                    <option value="8760"><?= _l("leads_engine_unit_years"); ?></option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="hours_since_last_contact" class="mass-hours-hidden">
                        <small class="text-muted"><?= _l("mass_edit_hours_desc"); ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= _l("tags"); ?></label>
                        <select multiple name="tags[]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                            <?php foreach ($tagsArray as $tag) { ?>
                                <option value="<?php echo $tag['id']; ?>">
                                    <?php echo $tag['name'] ?>
                                </option>
                            <?php } ?>
                        </select>
                        <small class="text-muted"><?= _l("mass_edit_tags_desc"); ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= _l("contact_engine_lead_status"); ?></label>
                        <select name="leads_status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('keep_current_value'); ?>">
                            <option value=""><?php echo _l('keep_current_value'); ?></option>
                            <?php foreach ($statuses as $status) { ?>
                                <option value="<?php echo $status['id']; ?>">
                                    <?php echo $status['name'] ?>
                                </option>
                            <?php } ?>
                        </select>
                        <small class="text-muted"><?= _l("mass_edit_status_desc"); ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= _l("contact_engine_lead_status_final"); ?></label>
                        <select name="leads_status_final" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('keep_current_value'); ?>">
                            <option value=""><?php echo _l('keep_current_value'); ?></option>
                            <?php foreach ($statuses as $status) { ?>
                                <option value="<?php echo $status['id']; ?>">
                                    <?php echo $status['name'] ?>
                                </option>
                            <?php } ?>
                        </select>
                        <small class="text-muted"><?= _l("mass_edit_status_desc"); ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= _l("contact"); ?></label>
                        <select name="fromMe" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('keep_current_value'); ?>">
                            <option value=""><?php echo _l('keep_current_value'); ?></option>
                            <option value="1"><?= _l("contac_conversation_last_contact"); ?></option>
                            <option value="0"><?= _l("contac_conversation_last_contact_lead"); ?></option>
                        </select>
                        <small class="text-muted"><?= _l("mass_edit_fromme_desc"); ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= _l("safety_settings_daily_limit"); ?></label>
                        <input type="number" class="form-control" name="daily_limit" min="1" placeholder="<?= _l('keep_current_value'); ?>">
                        <small class="text-muted"><?= _l("mass_edit_daily_limit_desc"); ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= _l("safety_settings_batch_size"); ?></label>
                        <input type="number" class="form-control" name="batch_size" min="1" placeholder="<?= _l('keep_current_value'); ?>">
                        <small class="text-muted"><?= _l("mass_edit_batch_size_desc"); ?></small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _l("close"); ?></button>
                <button type="button" class="btn btn-primary" onclick="submit_mass_edit()"><?= _l("save_changes"); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<style>
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }
    
    .table-responsive table {
        min-width: 100%;
        width: auto;
    }
    
    #bulk-actions-container {
        border-radius: 4px;
    }
    
    #bulk-actions-container .alert {
        margin-bottom: 0;
    }
</style>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        initDataTableInline("#contact_device");
        
        // Configure checkbox column after DataTable initialization
        setTimeout(function() {
            if ($.fn.DataTable.isDataTable('#contact_device')) {
                var dt = $('#contact_device').DataTable();
                // Disable sorting on checkbox column using columnDefs
                dt.settings()[0].aoColumns[0].bSortable = false;
            }
        }, 100);
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        //Limpa os formularios das modal
        $('.modal').on('hidden.bs.modal', function(e) {
            // Reseta o formulário quando fecha modal 
            $(this).find('form')[0].reset();
        });

        // Toggle vCard fields
        $('input[name="vcard_enable"]').on('change', function() {
            toggleVCardFields();
        });

        // Toggle Inbound Bait fields
        $('input[name="inbound_bait_enable"]').on('change', function() {
            toggleInboundBaitFields();
        });
    });

    function toggleVCardFields() {
        if ($('input[name="vcard_enable"]').is(':checked')) {
            $('#vcard_fields').show();
        } else {
            $('#vcard_fields').hide();
        }
    }

    function toggleInboundBaitFields() {
        if ($('input[name="inbound_bait_enable"]').is(':checked')) {
            $('#inbound_bait_fields').show();
        } else {
            $('#inbound_bait_fields').hide();
        }
    }
   

    function show_leads(id) {
        $.ajax({
            url: site_url + "contactcenter/ajax_leads_engine_show",
            data: {
                id: id
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {

                $(".load-get-msg").fadeIn();
                if ($.fn.DataTable.isDataTable('#engineLeads')) {
                    $('#engineLeads').DataTable().destroy();
                    $('#engineLeads tbody').html("")
                }
            },
            success: function(data) {

                $('#leadModal').modal('show');
                if (data.retorno) {

                    if ($.fn.DataTable.isDataTable('#engineLeads')) {
                        $('#engineLeads').DataTable().destroy();
                    }
                    // Atualiza o conteúdo da tabela
                    $('#engineLeads tbody').html(data.retorno);
                    // Recria a DataTable com os dados atualizados
                    appDataTableInline("#engineLeads", {
                        supportsButtons: false,
                        supportsLoading: true
                    });
                } else {
                    $('#engineLeads tbody').html("<tr><td>Not Result</td></tr>");
                }
            }
        });
    }
    

    function delete_engine(id) {
        $comfirmar = confirm("Deseja realmente excluir este motor?");
        if ($comfirmar) {
            $.ajax({
                url: site_url + "contactcenter/ajax_delete_leads_engine",
                data: {
                    id: id
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if (data.result) {
                        $(".engine_" + id).fadeOut();
                    }

                }
            });
        }
    }

    function status_engine(element, id) {
        var status = $(element).prop("checked");
        var valor = status ? 1 : 0;
        $.ajax({
            url: site_url + "contactcenter/ajax_leads_engine_status",
            data: {
                id: id,
                status: valor
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (!data.result) {
                    alert("Não é possível ativar o status, pois não há mensagens criadas.");
                    $(element).prop("checked", false);
                }
            }
        });
    }


    function edit_engine(id) {
        $.ajax({
            url: url_contactcenter + 'edit_leads_engine',
            data: {
                id: id
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.id) {
                    $('#modalDevice').modal('show');
                    $("input[name='id']").val(data.id);
                    $("input[name='title']").val(data.title);
                    // Handle optional status fields - set to empty string if null
                    $("select[name='leads_status']").val(data.leads_status || '').selectpicker('refresh');   
                    $("select[name='leads_status_final']").val(data.leads_status_final || '').selectpicker('refresh');
                    if (data.tags && data.tags.length > 0) {
                        $("select[name='tags[]']").val(data.tags).selectpicker('refresh');
                    } else {
                        $("select[name='tags[]']").val('').selectpicker('refresh');
                    }
                    $("select[name='device_id']").val(data.device_id).selectpicker('refresh');
                    $("select[name='campaign_tag_id']").val(data.campaign_tag_id || '').selectpicker('refresh');                                   
                    $("select[name='fromMe']").val(data.fromMe).selectpicker('refresh');                                   
                    $("input[name='start_time']").val(data.start_time);
                    $("input[name='end_time']").val(data.end_time);
                    setTimeFromHours(parseFloat(data.hours_since_last_contact) || 24);
                    
                    // Safety Settings fields
                    $("input[name='daily_limit']").val(data.daily_limit || 1000);
                    $("input[name='batch_size']").val(data.batch_size || 5);
                    $("input[name='batch_cooldown']").val(data.batch_cooldown || 5);
                    $("input[name='message_interval_min']").val(data.message_interval_min || 1);
                    $("input[name='message_interval_max']").val(data.message_interval_max || 3);
                    $("input[name='is_warmup_active']").prop('checked', data.is_warmup_active == 1);
                    $("input[name='stop_on_reply']").prop('checked', data.stop_on_reply != 0);
                    
                    // Advanced features
                    $("input[name='vcard_enable']").prop('checked', data.vcard_enable == 1);
                    $("input[name='vcard_name']").val(data.vcard_name || '');
                    $("input[name='vcard_phone']").val(data.vcard_phone || '');
                    $("input[name='inbound_bait_enable']").prop('checked', data.inbound_bait_enable == 1);
                    $("input[name='inbound_bait_message']").val(data.inbound_bait_message || '');
                    $("input[name='safe_groups_enable']").prop('checked', data.safe_groups_enable == 1);
                    
                    // Toggle fields visibility
                    toggleVCardFields();
                    toggleInboundBaitFields();
                }

            }
        });
    }

    function duplicate_engine(id) {
        $.ajax({
            url: url_contactcenter + 'ajax_duplicate_leads_engine',
            data: {
                id: id
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.retorno) {
                    location.reload();
                }
            }
        });
    }

    function replicate_engine(id) {
        $('#replicate_engine_id').val(id);
        $('#replicate_devices').val('').selectpicker('refresh');
        update_select_all_button();
        $('#replicateModal').modal('show');
    }

    function submit_replicate() {
        var engine_id = $('#replicate_engine_id').val();
        var devices = $('#replicate_devices').val();
        
        if (!devices || devices.length === 0) {
            alert('<?php echo _l("please_select_at_least_one_device"); ?>');
            return;
        }
        
        if (!confirm('<?php echo _l("confirm_replicate_campaign"); ?>')) {
            return;
        }
        
        $.ajax({
            url: url_contactcenter + 'ajax_replicate_leads_engine',
            data: {
                id: engine_id,
                devices: devices
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $('#replicate_submit_btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?php echo _l("replicating"); ?>...');
            },
            success: function(data) {
                if (data.success) {
                    alert('<?php echo _l("campaign_replicated_successfully"); ?>: ' + data.count + ' <?php echo _l("campaigns_created"); ?>');
                    $('#replicateModal').modal('hide');
                    location.reload();
                } else {
                    alert('<?php echo _l("replication_failed"); ?>: ' + (data.message || '<?php echo _l("unknown_error"); ?>'));
                    $('#replicate_submit_btn').prop('disabled', false).html('<?php echo _l("replicate"); ?>');
                }
            },
            error: function() {
                alert('<?php echo _l("error_occurred"); ?>');
                $('#replicate_submit_btn').prop('disabled', false).html('<?php echo _l("replicate"); ?>');
            }
        });
    }

    function toggle_select_all_devices() {
        var $select = $('#replicate_devices');
        var allValues = [];
        var isAllSelected = $select.val() && $select.val().length === $select.find('option').length;
        
        if (isAllSelected) {
            // Deselect all
            $select.val('').selectpicker('refresh');
        } else {
            // Select all
            $select.find('option').each(function() {
                allValues.push($(this).val());
            });
            $select.val(allValues).selectpicker('refresh');
        }
        update_select_all_button();
    }

    function update_select_all_button() {
        var $select = $('#replicate_devices');
        var totalOptions = $select.find('option').length;
        var selectedCount = $select.val() ? $select.val().length : 0;
        var $btn = $('#select_all_devices');
        
        if (selectedCount === totalOptions && totalOptions > 0) {
            $btn.html('<i class="fa fa-square"></i> <?php echo _l('deselect_all'); ?>');
            $btn.attr('title', '<?php echo _l('deselect_all'); ?>');
        } else {
            $btn.html('<i class="fa fa-check-square"></i> <?php echo _l('select_all'); ?>');
            $btn.attr('title', '<?php echo _l('select_all'); ?>');
        }
    }

    function update_campaign_filters() {
        var show_inactive = $('#filter_show_inactive').is(':checked') ? 1 : 0;
        
        $.ajax({
            url: url_contactcenter + 'update_leads_engine_filters',
            data: {
                show_inactive_campaigns: show_inactive
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    location.reload();
                }
            }
        });
    }

    function toggle_all_campaigns(checkbox) {
        $('.campaign-checkbox').prop('checked', checkbox.checked);
        update_bulk_actions();
    }

    function update_bulk_actions() {
        var selected = $('.campaign-checkbox:checked').length;
        $('#selected-count').text(selected);
        if (selected > 0) {
            $('#bulk-actions-container').show();
        } else {
            $('#bulk-actions-container').hide();
        }
        $('#select-all-campaigns').prop('checked', selected > 0 && selected === $('.campaign-checkbox').length);
    }

    function get_selected_campaigns() {
        var selected = [];
        $('.campaign-checkbox:checked').each(function() {
            selected.push($(this).val());
        });
        return selected;
    }

    function bulk_start_all() {
        if (!confirm('<?php echo _l("confirm_start_all_campaigns"); ?>')) {
            return;
        }
        
        $.ajax({
            url: site_url + "contactcenter/ajax_bulk_leads_engine_status",
            data: {
                ids: 'all',
                status: 1
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $('body').append('<div class="dt-loader"></div>');
            },
            success: function(data) {
                $('.dt-loader').remove();
                if (data.success) {
                    alert(data.message || '<?php echo _l("campaigns_started_successfully"); ?>');
                    location.reload();
                } else {
                    alert(data.message || '<?php echo _l("error_starting_campaigns"); ?>');
                }
            },
            error: function() {
                $('.dt-loader').remove();
                alert('<?php echo _l("error_starting_campaigns"); ?>');
            }
        });
    }

    function bulk_stop_all() {
        if (!confirm('<?php echo _l("confirm_stop_all_campaigns"); ?>')) {
            return;
        }
        
        $.ajax({
            url: site_url + "contactcenter/ajax_bulk_leads_engine_status",
            data: {
                ids: 'all',
                status: 0
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $('body').append('<div class="dt-loader"></div>');
            },
            success: function(data) {
                $('.dt-loader').remove();
                if (data.success) {
                    alert(data.message || '<?php echo _l("campaigns_stopped_successfully"); ?>');
                    location.reload();
                } else {
                    alert(data.message || '<?php echo _l("error_stopping_campaigns"); ?>');
                }
            },
            error: function() {
                $('.dt-loader').remove();
                alert('<?php echo _l("error_stopping_campaigns"); ?>');
            }
        });
    }

    function bulk_start_selected() {
        var selected = get_selected_campaigns();
        if (selected.length === 0) {
            alert('<?php echo _l("no_campaigns_selected"); ?>');
            return;
        }
        
        if (!confirm('<?php echo _l("confirm_start_selected_campaigns"); ?>')) {
            return;
        }
        
        $.ajax({
            url: site_url + "contactcenter/ajax_bulk_leads_engine_status",
            data: {
                ids: selected,
                status: 1
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $('body').append('<div class="dt-loader"></div>');
            },
            success: function(data) {
                $('.dt-loader').remove();
                if (data.success) {
                    alert(data.message || '<?php echo _l("campaigns_started_successfully"); ?>');
                    location.reload();
                } else {
                    alert(data.message || '<?php echo _l("error_starting_campaigns"); ?>');
                }
            },
            error: function() {
                $('.dt-loader').remove();
                alert('<?php echo _l("error_starting_campaigns"); ?>');
            }
        });
    }

    function bulk_stop_selected() {
        var selected = get_selected_campaigns();
        if (selected.length === 0) {
            alert('<?php echo _l("no_campaigns_selected"); ?>');
            return;
        }
        
        if (!confirm('<?php echo _l("confirm_stop_selected_campaigns"); ?>')) {
            return;
        }
        
        $.ajax({
            url: site_url + "contactcenter/ajax_bulk_leads_engine_status",
            data: {
                ids: selected,
                status: 0
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $('body').append('<div class="dt-loader"></div>');
            },
            success: function(data) {
                $('.dt-loader').remove();
                if (data.success) {
                    alert(data.message || '<?php echo _l("campaigns_stopped_successfully"); ?>');
                    location.reload();
                } else {
                    alert(data.message || '<?php echo _l("error_stopping_campaigns"); ?>');
                }
            },
            error: function() {
                $('.dt-loader').remove();
                alert('<?php echo _l("error_stopping_campaigns"); ?>');
            }
        });
    }

    function open_mass_edit_modal() {
        var selected = get_selected_campaigns();
        if (selected.length === 0) {
            alert('<?php echo _l("no_campaigns_selected"); ?>');
            return;
        }
        
        $('#mass-edit-count').text(selected.length);
        $('#mass-edit-form')[0].reset();
        $('.selectpicker').selectpicker('refresh');
        $('#massEditModal').modal('show');
    }

    function submit_mass_edit() {
        var selected = get_selected_campaigns();
        if (selected.length === 0) {
            alert('<?php echo _l("no_campaigns_selected"); ?>');
            return;
        }

        var formData = {};
        $('#mass-edit-form').serializeArray().forEach(function(item) {
            if (item.value !== '') {
                formData[item.name] = item.value;
            }
        });

        // Check if at least one field is being updated
        if (Object.keys(formData).length === 0) {
            alert('<?php echo _l("mass_edit_no_fields"); ?>');
            return;
        }

        formData.ids = selected;

        if (!confirm('<?php echo _l("confirm_mass_edit"); ?>')) {
            return;
        }

        $.ajax({
            url: site_url + "contactcenter/ajax_mass_edit_leads_engine",
            data: formData,
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $('body').append('<div class="dt-loader"></div>');
            },
            success: function(data) {
                $('.dt-loader').remove();
                if (data.success) {
                    alert(data.message || '<?php echo _l("mass_edit_success"); ?>');
                    $('#massEditModal').modal('hide');
                    location.reload();
                } else {
                    alert(data.message || '<?php echo _l("mass_edit_error"); ?>');
                }
            },
            error: function() {
                $('.dt-loader').remove();
                alert('<?php echo _l("mass_edit_error"); ?>');
            }
        });
    }

    // ── Time Unit Converter ──
    var timeUnits = [
        { value: 0.0166667, max: 1,     label: '<?= _l("leads_engine_unit_minutes"); ?>' },
        { value: 1,         max: 24,    label: '<?= _l("leads_engine_unit_hours"); ?>' },
        { value: 24,        max: 168,   label: '<?= _l("leads_engine_unit_days"); ?>' },
        { value: 168,       max: 720,   label: '<?= _l("leads_engine_unit_weeks"); ?>' },
        { value: 720,       max: 8760,  label: '<?= _l("leads_engine_unit_months"); ?>' },
        { value: 8760,      max: Infinity, label: '<?= _l("leads_engine_unit_years"); ?>' }
    ];

    function updateHoursHidden() {
        var amount = parseFloat($('#time_amount').val()) || 0;
        var multiplier = parseFloat($('#time_unit').val()) || 1;
        var totalHours = Math.round(amount * multiplier * 100) / 100;
        $('#hours_since_last_contact_hidden').val(totalHours);
        $('#time_conversion_hint').text('= ' + totalHours + ' <?= _l("leads_engine_unit_hours"); ?>');
    }

    function setTimeFromHours(hours) {
        var bestUnit = 1;
        var bestAmount = hours;
        for (var i = timeUnits.length - 1; i >= 0; i--) {
            var u = timeUnits[i];
            var converted = hours / u.value;
            if (converted >= 1 && converted === Math.floor(converted)) {
                bestUnit = u.value;
                bestAmount = converted;
                break;
            }
        }
        if (bestAmount !== Math.floor(bestAmount)) {
            bestUnit = 1;
            bestAmount = hours;
        }
        $('#time_amount').val(bestAmount);
        $('#time_unit').val(bestUnit);
        updateHoursHidden();
    }

    $(function() {
        $('#time_amount, #time_unit').on('input change', updateHoursHidden);
        updateHoursHidden();

        $('#modalDevice form').on('submit', function() {
            updateHoursHidden();
        });

        $('.mass-time-amount, .mass-time-unit').on('input change', function() {
            var amount = parseFloat($('.mass-time-amount').val()) || 0;
            var multiplier = parseFloat($('.mass-time-unit').val()) || 1;
            $('.mass-hours-hidden').val(Math.round(amount * multiplier * 100) / 100);
        });
    });
    
</script>

<!-- Replicate Modal -->
<div class="modal fade" id="replicateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo _l('replicate_campaign'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><?php echo _l('select_devices'); ?> <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select class="form-control selectpicker" id="replicate_devices" multiple data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <?php foreach ($devices as $device) { ?>
                                <option value="<?php echo $device->dev_id; ?>">
                                    <?php echo htmlspecialchars($device->dev_name); ?>
                                </option>
                            <?php } ?>
                        </select>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default" id="select_all_devices" onclick="toggle_select_all_devices()" title="<?php echo _l('select_all'); ?>">
                                <i class="fa fa-check-square"></i> <?php echo _l('select_all'); ?>
                            </button>
                        </div>
                    </div>
                    <small class="text-muted"><?php echo _l('replicate_help_text'); ?></small>
                </div>
                <input type="hidden" id="replicate_engine_id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-primary" id="replicate_submit_btn" onclick="submit_replicate()"><?php echo _l('replicate'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Create Follow-up Tag Modal -->
<div class="modal fade" id="createFollowupTagModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo _l('create_new_tag'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="new_followup_tag_name"><?php echo _l('tag_name'); ?> <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="new_followup_tag_name" placeholder="<?php echo _l('enter_tag_name'); ?>">
                    <small class="text-muted"><?php echo _l('tag_name_help'); ?></small>
                </div>
                <div id="followup_tag_creation_message" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-primary" id="create_followup_tag_btn"><?php echo _l('create_tag'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Create new follow-up tag functionality
    $('#create_followup_tag_btn').on('click', function() {
        var tagName = $('#new_followup_tag_name').val().trim();
        
        if (!tagName) {
            alert('<?php echo _l('tag_name_required'); ?>');
            return;
        }
        
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?php echo _l('creating'); ?>...');
        
        $.ajax({
            url: admin_url + 'contactcenter/ajax_create_tag',
            type: 'POST',
            data: {
                tag_name: tagName
            },
            success: function(response) {
                var data = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (data.success) {
                    // Add new tag to selectpicker
                    var $select = $('#campaign_tag_id_followup');
                    var option = $('<option></option>').attr('value', data.tag_id).text(data.tag_name);
                    $select.append(option);
                    $select.selectpicker('refresh');
                    
                    // Select the newly created tag
                    $select.selectpicker('val', data.tag_id);
                    
                    // Show success message
                    $('#followup_tag_creation_message')
                        .removeClass('alert-danger')
                        .addClass('alert alert-success')
                        .html('<i class="fa fa-check"></i> ' + (data.message || '<?php echo _l('tag_created_successfully'); ?>'))
                        .show();
                    
                    // Clear input
                    $('#new_followup_tag_name').val('');
                    
                    // Close modal after 1.5 seconds
                    setTimeout(function() {
                        $('#createFollowupTagModal').modal('hide');
                        $('#followup_tag_creation_message').hide().removeClass('alert-success');
                    }, 1500);
                } else {
                    $('#followup_tag_creation_message')
                        .removeClass('alert-success')
                        .addClass('alert alert-danger')
                        .html('<i class="fa fa-exclamation-triangle"></i> ' + (data.message || '<?php echo _l('tag_creation_failed'); ?>'))
                        .show();
                }
                
                $btn.prop('disabled', false).html('<?php echo _l('create_tag'); ?>');
            },
            error: function() {
                $('#followup_tag_creation_message')
                    .removeClass('alert-success')
                    .addClass('alert alert-danger')
                    .html('<i class="fa fa-exclamation-triangle"></i> <?php echo _l('error_occurred'); ?>')
                    .show();
                $btn.prop('disabled', false).html('<?php echo _l('create_tag'); ?>');
            }
        });
    });
    
    // Allow Enter key to create tag
    $('#new_followup_tag_name').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#create_followup_tag_btn').click();
        }
    });
    
    // Clear message when modal is closed
    $('#createFollowupTagModal').on('hidden.bs.modal', function() {
        $('#new_followup_tag_name').val('');
        $('#followup_tag_creation_message').hide().removeClass('alert-success alert-danger');
    });
});
</script>

</body>

</html>