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
                                <?php echo _l('contac_conversation_engine'); ?>
                            </span>
                        </h4>
                        <hr class="hr-panel-separator" />
                        <div class="tw-mb-2 sm:tw-mb-4">
                            <div class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-3">
                                <div class="_buttons tw-flex tw-gap-2">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalDevice">
                                        <i class="fa-regular fa-plus tw-mr-1"></i>
                                        <?php echo _l('contac_conversation_new'); ?>
                                    </button>
                                    <button class="btn btn-default" data-toggle="modal" data-target="#modalCampaignTemplates" style="background:#1a1f25;color:#00e09b;border-color:rgba(0,224,155,0.3);font-weight:600;">
                                        <i class="fa fa-layer-group tw-mr-1"></i>
                                        <?php echo _l('contac_campaign_tpl_use'); ?>
                                    </button>
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-3 tw-flex-wrap">
                                    <div class="panel panel-default tw-mb-0" style="border: 1px solid #e0e0e0; border-radius: 4px; padding: 10px 15px; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        <div class="tw-flex tw-items-center tw-gap-2">
                                            <div class="checkbox tw-mb-0" style="margin: 0;">
                                                <input type="checkbox" id="filter_show_inactive" <?php echo $show_inactive ? 'checked' : ''; ?> onchange="update_campaign_filters()" style="margin-top: 0;">
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
                                        <th><?= _l("contac_conversation_name"); ?></th>
                                        <th><?= _l("contac_conversation_send"); ?></th>
                                        <th><?= _l("contac_conversation_not_send"); ?></th>
                                        <th><?= _l("contac_phone_status"); ?></th>
                                        <th><?= _l("contact_engine_lead_status"); ?></th>
                                        <th><?= _l("contact_engine_lead_status_final"); ?></th>
                                        <th><?= _l("contact_engine_lead_per_day"); ?></th>
                                        <th><?= _l("contact_engine_lead_create_date"); ?></th>
                                        <th><?= _l("contact_engine_lead_create_date_final"); ?></th>
                                        <th><?= _l("contac_conversation_start_time"); ?></th>
                                        <th><?= _l("contac_conversation_end_time"); ?></th>
                                        <th><?= _l("contact_group_device"); ?></th>
                                        <th><?= _l("Tags"); ?></th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($conversation as $con) {
                                        extract((array) $con);
                                        echo "<tr class='engine_{$con_id}'>
                                                <td>
                                                    <input type='checkbox' class='campaign-checkbox' value='{$con_id}' onchange='update_bulk_actions()'>
                                                </td>
                                                <td>{$con_id} </a></td>
                                                <td class='text-center' style='vertical-align: middle;'>
                                                    <a href='" . admin_url("contactcenter/conversation_list/{$con_id}") . "' 
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
                                                            {$con_title}
                                                            <div class='row-options'>                                                                                                                                                           
                                                                <a href='javascript:void(0);' onclick='edit_engine({$con_id})'>" . _l("contac_editar") . "</a> |                         
                                                                <a href='javascript:void(0);'class='text-danger' onclick='delete_engine({$con_id})' >" . _l("contac_excluir") . "</a> |
                                                                <a href='javascript:void(0);'class='text-danger' onclick='duplicate_engine({$con_id})' >" . _l("contac_duplicate") . "</a> |
                                                                <a href='javascript:void(0);'class='text-info' onclick='replicate_engine({$con_id})' >" . _l("replicate") . "</a>
                                                            </div>        
                                                        </div>    
                                                    </div>    
                                                </td>                         
                                                <td>{$con_count_send}</td>                                 
                                                
                                                <td>
                                                    <div class='box-button-erros'>
                                                        <button class='btn btn-danger ' data-toggle='tooltip' data-title='Verificar erros' onclick='show_erro({$con_id})'>" . get_count_error_engine_conversation($con_id) . "  </button>                                                 
                                                        <button class='btn btn-success ' data-toggle='tooltip' data-title='Validar Motor' onclick='valid_if_engine_will_run({$con_id})'> " . _l("contact_valid") . " </button>                                                 
                                                        <button class='btn btn-warning ' data-toggle='tooltip' data-title='Verificar se tem Leads a serem enviados' onclick='show_leads({$con_id})'> " . _l("contac_leads") . " </button>                                                 
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class='onoffswitch' data-toggle='tooltip' data-title='" . _l("contac_public") . "' data-original-title='' title=''>
                                                        <input type='checkbox' name='status' class='onoffswitch-checkbox 'onclick='status_engine(this,{$con_id})'  id='{$con_id}' " . ($con_status == 1 ? 'checked' : '') . ">
                                                        <label class='onoffswitch-label' for='{$con_id}'></label>
                                                    </div> 
                                                </td> 
                                                <td>" . contactcenter_get_name_status_lead($leads_status) . "</td> 
                                                <td>" . contactcenter_get_name_status_lead($leads_status_final) . "</td>
                                                <td>" . ($leads_day == 0 ? 'Ilimitado' : $leads_day) . "</td>
                                                <td>" . _dt($leads_create_data) . "</td>  
                                                <td>" . _dt($leads_create_data_final) . "</td>  
                                                <td>{$start_time}</td>
                                                <td>{$end_time}</td>
                                                <td>" . get_device_name($device_id) . "</td>
                                                 <td>" . get_tags_name($tags) . "</td>    
                                                                             
                                                                                                   
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
                <h4 class="modal-title " id="exampleModalLongTitle"><?= _l("contac_conversation_new"); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart(admin_url('contactcenter/add_conversation_engine'), array('id' => 'conversation_engine_form')); ?>
                <input type="hidden" name="con_id" value="">
                <div class="form-group">
                    <label><?= _l("contac_conversation_name"); ?></label>
                    <input type="text" class="form-control" name="con_title" placeholder="<?= _l("contac_conversation_name"); ?>" required>
                </div>

                <div class="form-group">
                    <label><?= _l("contact_engine_lead_status"); ?></label>
                    <select name="leads_status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" required>
                        <option></option>
                        <?php foreach ($statuses as $status) { ?>
                            <option value="<?php echo $status['id']; ?>">
                                <?php echo $status['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><?= _l("contact_engine_lead_status_final"); ?></label>
                    <select name="leads_status_final" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" required>
                        <option></option>
                        <?php foreach ($statuses as $status) { ?>
                            <option value="<?php echo $status['id']; ?>">
                                <?php echo $status['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><?= _l("campaign_tag_for_leads"); ?></label>
                    <div class="input-group">
                        <select name="campaign_tag_id" id="campaign_tag_id" class="form-control selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                            <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                            <?php foreach ($tagsArray as $tag) { ?>
                                <option value="<?php echo $tag['id']; ?>">
                                    <?php echo htmlspecialchars($tag['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createCampaignTagModal" title="<?php echo _l('create_new_tag'); ?>">
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
                    <label><?= _l("date_filter_type"); ?></label>
                    <select name="date_filter_type" id="date_filter_type" class="form-control" required>
                        <option value="creation_date"><?= _l("filter_by_creation_date"); ?></option>
                        <option value="last_contact"><?= _l("filter_by_last_contact"); ?></option>
                        <option value="birthday"><?= _l("filter_by_birthday"); ?></option>
                    </select>
                    <small class="text-muted"><?= _l("date_filter_type_desc"); ?></small>
                </div>

                <div id="birthday_fields" style="display: none;">
                    <div class="form-group">
                        <label><?= _l("campaign_birthday_field"); ?></label>
                        <select name="birthday_field" id="birthday_field" class="form-control selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                            <option value=""><?= _l("campaign_birthday_field_none"); ?></option>
                            <?php if (isset($birthday_custom_fields) && !empty($birthday_custom_fields)) { ?>
                                <?php foreach ($birthday_custom_fields as $cf) { ?>
                                    <option value="<?php echo $cf['id']; ?>" data-slug="<?php echo $cf['slug']; ?>">
                                        <?php echo htmlspecialchars($cf['name']); ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        <small class="text-muted"><?= _l("campaign_birthday_field_desc"); ?></small>
                    </div>
                </div>

                <div id="creation_date_fields">
                    <div class="form-group">
                        <div class="tw-shrink-0<?php echo $task->status != 5 ? ' tw-grow' : ''; ?>">
                            <i class="fa-regular fa-calendar fa-fw fa-lg fa-margin task-info-icon pull-left tw-mt-2"></i>
                            <?php echo _l('contact_engine_lead_create_date'); ?>:
                        </div>
                        <input type="datetime-local" name="leads_create_data" id="leads_create_data" class="form-control">
                    </div>

                    <div class="form-group">
                        <div class="tw-shrink-0<?php echo $task->status != 5 ? ' tw-grow' : ''; ?>">
                            <i class="fa-regular fa-calendar fa-fw fa-lg fa-margin task-info-icon pull-left tw-mt-2"></i>
                            <?php echo _l('contact_engine_lead_create_date_final'); ?>:
                        </div>
                        <input type="datetime-local" name="leads_create_data_final" id="leads_create_data_final" class="form-control">
                    </div>
                </div>

                <div id="last_contact_fields" style="display: none;">
                    <div class="form-group">
                        <div class="tw-shrink-0<?php echo $task->status != 5 ? ' tw-grow' : ''; ?>">
                            <i class="fa-regular fa-calendar fa-fw fa-lg fa-margin task-info-icon pull-left tw-mt-2"></i>
                            <?php echo _l('contact_engine_lead_last_contact_date'); ?>:
                        </div>
                        <input type="datetime-local" name="leads_last_contact_data" id="leads_last_contact_data" class="form-control">
                    </div>

                    <div class="form-group">
                        <div class="tw-shrink-0<?php echo $task->status != 5 ? ' tw-grow' : ''; ?>">
                            <i class="fa-regular fa-calendar fa-fw fa-lg fa-margin task-info-icon pull-left tw-mt-2"></i>
                            <?php echo _l('contact_engine_lead_last_contact_date_final'); ?>:
                        </div>
                        <input type="datetime-local" name="leads_last_contact_data_final" id="leads_last_contact_data_final" class="form-control">
                    </div>
                </div>

                <hr>
                <h4 class="tw-mt-0 tw-font-semibold tw-text-base tw-mb-3">
                    <i class="fa fa-filter"></i> <?= _l("additional_filters"); ?>
                </h4>

                <div class="form-group">
                    <label><?= _l("lead_source"); ?></label>
                    <select name="filter_source[]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" multiple>
                        <?php foreach ($sources as $source) { ?>
                            <option value="<?php echo $source['id']; ?>">
                                <?php echo $source['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                    <small class="text-muted"><?= _l("filter_source_desc"); ?></small>
                </div>

                <div class="form-group">
                    <label><?= _l("clients_city"); ?></label>
                    <select name="filter_city[]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" multiple>
                        <?php foreach ($cities as $city) { ?>
                            <option value="<?php echo htmlspecialchars($city['city']); ?>">
                                <?php echo htmlspecialchars($city['city']); ?>
                            </option>
                        <?php } ?>
                    </select>
                    <small class="text-muted"><?= _l("filter_city_desc"); ?></small>
                </div>

                <div class="form-group">
                    <label><?= _l("clients_state"); ?></label>
                    <select name="filter_state[]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" multiple>
                        <?php foreach ($states as $state) { ?>
                            <option value="<?php echo htmlspecialchars($state['state']); ?>">
                                <?php echo htmlspecialchars($state['state']); ?>
                            </option>
                        <?php } ?>
                    </select>
                    <small class="text-muted"><?= _l("filter_state_desc"); ?></small>
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
                    <label><?= _l("contact_engine_lead_per_day"); ?> (<?= _l("contact_engine_lead_per_day_message"); ?>)</label>
                    <input type="number" min="0" step="1" class="form-control" name="leads_day" required value=0>
                </div>

                <div class="form-group">
                    <label><?= _l("campaign_backup_phone_field"); ?></label>
                    <select name="backup_phone_field" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                        <option value=""><?= _l("campaign_backup_phone_field_none"); ?></option>
                        <?php if (isset($custom_fields) && !empty($custom_fields)) { ?>
                            <?php foreach ($custom_fields as $custom_field) { ?>
                                <option value="<?php echo $custom_field['id']; ?>" data-slug="<?php echo $custom_field['slug']; ?>">
                                    <?php echo htmlspecialchars($custom_field['name']); ?>
                                </option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <small class="text-muted"><?= _l("campaign_backup_phone_field_desc"); ?></small>
                </div>
                
                <div class="form-group">
                    <label><?= _l("campaign_backup_phone_country"); ?></label>
                    <select name="backup_phone_country_code" class="selectpicker" data-width="100%" data-live-search="true">
                        <?php if (isset($countries) && !empty($countries)) { ?>
                            <?php foreach ($countries as $country) { ?>
                                <option value="<?php echo htmlspecialchars($country['calling_code'] ?? ''); ?>" <?php echo (($country['calling_code'] ?? '') == '55') ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($country['short_name'] ?? ''); ?> (<?php echo htmlspecialchars($country['calling_code'] ?? ''); ?>)
                                </option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <small class="text-muted"><?= _l("campaign_backup_phone_country_desc"); ?></small>
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
                        <input type="checkbox" name="is_warmup_active" id="is_warmup_active_campaign" value="1">
                        <label for="is_warmup_active_campaign">
                            <strong><?= _l("safety_settings_warmup_active"); ?></strong>
                            <br><small class="text-muted"><?= _l("safety_settings_warmup_desc"); ?></small>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" name="stop_on_reply" id="stop_on_reply_campaign" value="1" checked>
                        <label for="stop_on_reply_campaign">
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
                        <input type="checkbox" name="vcard_enable" id="vcard_enable_campaign" value="1">
                        <label for="vcard_enable_campaign">
                            <strong><?= _l("campaign_vcard_enable"); ?></strong>
                            <br><small class="text-muted"><?= _l("campaign_vcard_desc"); ?></small>
                        </label>
                    </div>
                </div>

                <div id="vcard_fields_campaign" style="display: none; margin-left: 20px;">
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
                        <input type="checkbox" name="inbound_bait_enable" id="inbound_bait_enable_campaign" value="1">
                        <label for="inbound_bait_enable_campaign">
                            <strong><?= _l("campaign_inbound_bait_enable"); ?></strong>
                            <br><small class="text-muted"><?= _l("campaign_inbound_bait_desc"); ?></small>
                        </label>
                    </div>
                </div>

                <div id="inbound_bait_fields_campaign" style="display: none; margin-left: 20px;">
                    <div class="form-group">
                        <label><?= _l("campaign_inbound_bait_message"); ?></label>
                        <input type="text" class="form-control" name="inbound_bait_message" placeholder="Oi, é o Carlos?">
                        <small class="text-muted"><?= _l("campaign_inbound_bait_example"); ?></small>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" name="safe_groups_enable" id="safe_groups_enable_campaign" value="1">
                        <label for="safe_groups_enable_campaign">
                            <strong><?= _l("campaign_safe_groups_enable"); ?></strong>
                            <br><small class="text-muted"><?= _l("campaign_safe_groups_desc"); ?></small>
                        </label>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label><?= _l("contact_group_device"); ?> <small class="text-muted"><?= _l("primary_device_desc"); ?></small></label>
                    <select name="device_id" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" required>
                        <option></option>
                        <?php foreach ($devices as $device) { ?>
                            <option value="<?php echo $device->dev_id; ?>">
                                <?php echo $device->dev_name ?>
                            </option>
                        <?php } ?>
                    </select>
                    <small class="text-muted"><?= _l("primary_device_help"); ?></small>
                </div>

                <div class="form-group">
                    <label><?= _l("spare_devices"); ?> <small class="text-muted"><?= _l("spare_devices_desc"); ?></small></label>
                    <select name="spare_devices[]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" multiple>
                        <?php foreach ($devices as $device) { ?>
                            <option value="<?php echo $device->dev_id; ?>">
                                <?php echo $device->dev_name ?>
                            </option>
                        <?php } ?>
                    </select>
                    <small class="text-muted"><?= _l("spare_devices_help"); ?></small>
                </div>

                <!--<div class="form-group">
                    <label><?= _l("contact_engine_lead_sleep"); ?></label>
                    <input type="number" min="0" step="1" class="form-control" name="leads_sleep" required>
                </div>-->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" onclick="return validateConversationEngineForm()">Save changes</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="errorModalEngine" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                <table id="errorEngine" class="table">
                    <thead>
                        <tr>
                            <th><?= _l("contactcenter_lead_id"); ?></th>
                            <th><?= _l("contactcenter_lead_name"); ?></th>
                            <th><?= _l("contactcenter_lead_phonenumber"); ?></th>
                            <th><?= _l("contactcenter_message_error"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="validModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="width:70%">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title " id="engine_name"></h4>
            </div>
            <div class="modal-body">
                <div id="valid_msg"></div>
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
                        <label><?= _l("contact_engine_lead_per_day"); ?></label>
                        <input type="number" min="0" step="1" class="form-control" name="leads_day" placeholder="<?= _l('keep_current_value'); ?>">
                        <small class="text-muted"><?= _l("mass_edit_leads_day_desc"); ?></small>
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
    // Helper function to get CSRF token
    function getCSRFToken() {
        var tokenName = $('input[name*="csrf"]').attr('name');
        var tokenValue = $('input[name*="csrf"]').val();
        if (!tokenName || !tokenValue) {
            // Try alternative selectors
            tokenName = $('input[name*="CSRF"]').attr('name');
            tokenValue = $('input[name*="CSRF"]').val();
        }
        if (!tokenName || !tokenValue) {
            // Try to get from form
            var form = $('#conversation_engine_form');
            if (form.length) {
                tokenName = form.find('input[type="hidden"][name*="csrf"]').attr('name');
                tokenValue = form.find('input[type="hidden"][name*="csrf"]').val();
            }
        }
        var csrfData = {};
        if (tokenName && tokenValue) {
            csrfData[tokenName] = tokenValue;
        }
        return csrfData;
    }

    function update_campaign_filters() {
        var show_inactive = $('#filter_show_inactive').is(':checked') ? 1 : 0;
        var csrfData = getCSRFToken();
        
        $.ajax({
            url: url_contactcenter + 'update_conversation_engine_filters',
            data: $.extend({
                show_inactive: show_inactive
            }, csrfData),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    location.reload();
                }
            }
        });
    }

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
            $('#vcard_fields_campaign').toggle($(this).is(':checked'));
        });

        // Toggle Inbound Bait fields
        $('input[name="inbound_bait_enable"]').on('change', function() {
            $('#inbound_bait_fields_campaign').toggle($(this).is(':checked'));
        });

        // Toggle date filter type fields
        $('#date_filter_type').on('change', function() {
            var filterType = $(this).val();
            if (filterType === 'birthday') {
                $('#creation_date_fields').hide();
                $('#last_contact_fields').hide();
                $('#birthday_fields').show();
                $('#leads_create_data, #leads_create_data_final').prop('required', false);
                $('#leads_last_contact_data, #leads_last_contact_data_final').prop('required', false);
                $('#birthday_field').prop('required', true);
            } else if (filterType === 'creation_date') {
                $('#creation_date_fields').show();
                $('#last_contact_fields').hide();
                $('#birthday_fields').hide();
                $('#leads_create_data, #leads_create_data_final').prop('required', true);
                $('#leads_last_contact_data, #leads_last_contact_data_final').prop('required', false);
                $('#birthday_field').prop('required', false);
            } else {
                $('#creation_date_fields').hide();
                $('#last_contact_fields').show();
                $('#birthday_fields').hide();
                $('#leads_create_data, #leads_create_data_final').prop('required', false);
                $('#leads_last_contact_data, #leads_last_contact_data_final').prop('required', true);
                $('#birthday_field').prop('required', false);
            }
        });

        // Trigger on page load to set initial state
        $('#date_filter_type').trigger('change');
    });

    function validateConversationEngineForm() {
        var filterType = $('#date_filter_type').val();
        var isValid = true;
        
        if (filterType === 'birthday') {
            if (!$('#birthday_field').val()) {
                alert('<?php echo addslashes(_l("campaign_birthday_field")); ?> is required for birthday campaigns');
                isValid = false;
            }
        } else if (filterType === 'creation_date') {
            if (!$('#leads_create_data').val()) {
                alert('<?php echo _l("contact_engine_lead_create_date"); ?> is required');
                isValid = false;
            }
            if (!$('#leads_create_data_final').val()) {
                alert('<?php echo _l("contact_engine_lead_create_date_final"); ?> is required');
                isValid = false;
            }
        } else {
            if (!$('#leads_last_contact_data').val()) {
                alert('<?php echo _l("contact_engine_lead_last_contact_date"); ?> is required');
                isValid = false;
            }
            if (!$('#leads_last_contact_data_final').val()) {
                alert('<?php echo _l("contact_engine_lead_last_contact_date_final"); ?> is required');
                isValid = false;
            }
        }
        
        return isValid;
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
            url: site_url + "contactcenter/ajax_bulk_engine_status",
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
            url: site_url + "contactcenter/ajax_bulk_engine_status",
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
            url: site_url + "contactcenter/ajax_bulk_engine_status",
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
            url: site_url + "contactcenter/ajax_bulk_engine_status",
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

    function valid_if_engine_will_run(id) {
        $.ajax({
            url: site_url + "contactcenter/ajax_valid_if_engine_will_run",
            data: {
                id: id
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.retorno) {
                    $('#validModal').modal('show');
                    $('#valid_msg').html(data.retorno);
                }
            }
        });
    }


    function show_leads(id) {
        $.ajax({
            url: site_url + "contactcenter/ajax_engine_show_leads",
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

    function show_erro(id) {

        $.ajax({
            url: site_url + "contactcenter/ajax_get_error_engine",
            data: {
                id: id
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {

                $(".load-get-msg").fadeIn();
                if ($.fn.DataTable.isDataTable('#errorEngine')) {
                    $('#errorEngine').DataTable().destroy();
                    $('#errorEngine tbody').html("")
                }
            },
            success: function(data) {

                $('#errorModalEngine').modal('show');
                if (data.retorno) {



                    if ($.fn.DataTable.isDataTable('#contaChat')) {
                        $('#errorEngine').DataTable().destroy();
                    }
                    // Atualiza o conteúdo da tabela
                    $('#errorEngine tbody').html(data.retorno);
                    // Recria a DataTable com os dados atualizados
                    appDataTableInline("#errorEngine", {
                        supportsButtons: false,
                        supportsLoading: true
                    });
                } else {
                    $('#contaChat tbody').html("<tr><td>Not Result</td></tr>");
                }
            }
        });

    }

    function delete_engine(id) {
        $comfirmar = confirm("Deseja realmente excluir este motor?");
        if ($comfirmar) {
            $.ajax({
                url: site_url + "contactcenter/ajax_engine",
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
            url: site_url + "contactcenter/ajax_engine_status",
            data: {
                id: id,
                con_status: valor
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
            url: url_contactcenter + 'edit_engine',
            data: {
                id: id
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.con_id) {
                    $('#modalDevice').modal('show');
                    $("input[name='con_id']").val(data.con_id);
                    $("input[name='con_title']").val(data.con_title);
                    $("select[name='leads_status']").val(data.leads_status).selectpicker('refresh');
                    // Date filter type
                    if (data.date_filter_type) {
                        $("select[name='date_filter_type']").val(data.date_filter_type).trigger('change');
                    }
                    $("select[name='birthday_field']").val(data.birthday_field || '').selectpicker('refresh');
                    
                    $("input[name='leads_create_data']").val(data.leads_create_data);
                    $("select[name='leads_status_final']").val(data.leads_status_final).selectpicker('refresh');
                    $("input[name='leads_create_data_final']").val(data.leads_create_data_final);
                    $("input[name='leads_last_contact_data']").val(data.leads_last_contact_data);
                    $("input[name='leads_last_contact_data_final']").val(data.leads_last_contact_data_final);
                    
                    // Filter fields
                    if (data.filter_source) {
                        var sources = data.filter_source.split(',');
                        $("select[name='filter_source[]']").val(sources).selectpicker('refresh');
                    }
                    if (data.filter_city) {
                        var cities = data.filter_city.split(',');
                        $("select[name='filter_city[]']").val(cities).selectpicker('refresh');
                    }
                    if (data.filter_state) {
                        var states = data.filter_state.split(',');
                        $("select[name='filter_state[]']").val(states).selectpicker('refresh');
                    }
                    if (data.spare_devices && Array.isArray(data.spare_devices)) {
                        $("select[name='spare_devices[]']").val(data.spare_devices).selectpicker('refresh');
                    }
                    //$("input[name='leads_sleep']").val(data.leads_sleep);
                    $("input[name='leads_day']").val(data.leads_day);
                    $("input[name='start_time']").val(data.start_time);
                    $("input[name='end_time']").val(data.end_time);
                    $("select[name='device_id']").val(data.device_id).selectpicker('refresh');
                    $("select[name='tags[]']").val(data.tags).selectpicker('refresh');
                    $("select[name='campaign_tag_id']").val(data.campaign_tag_id || '').selectpicker('refresh');
                    $("select[name='backup_phone_field']").val(data.backup_phone_field || '').selectpicker('refresh');
                    $("select[name='backup_phone_country_code']").val(data.backup_phone_country_code || '55').selectpicker('refresh');
                    
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
                    $('#vcard_fields_campaign').toggle(data.vcard_enable == 1);
                    $('#inbound_bait_fields_campaign').toggle(data.inbound_bait_enable == 1);
                }

            }
        });
    }

    function duplicate_engine(id) {
        $.ajax({
            url: url_contactcenter + 'ajax_duplicate_engine',
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
            url: url_contactcenter + 'ajax_replicate_engine',
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

    function change_phone(id, lead_id, con_id) {

        var phone = $("input[name='phone']").val();

        $.ajax({
            url: url_contactcenter + 'ajax_change_phone',
            data: {
                id: id,
                lead_id: lead_id,
                con_id: con_id,
                phone: phone
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                $("#engine_name").html("Aguarde...");
            },
            success: function(data) {
                if (data.retorno) {
                    if (data.retorno["error"] == true) {
                        $("#engine_name").html(data.retorno["message"]);
                    } else {
                        $(".engine_error_" + id).fadeOut();
                        $("#engine_name").html(data.retorno["message"]);
                    }
                } else {
                    alert("Erro ao alterar o telefone");
                }

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
            url: site_url + "contactcenter/ajax_mass_edit_engine",
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

    function replicate_engine(id) {
        $('#replicate_engine_id').val(id);
        $('#replicate_devices').val('').selectpicker('refresh');
        update_select_all_button();
        $('#replicateModal').modal('show');
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

    // Update button text when selection changes
    $(document).on('changed.bs.select', '#replicate_devices', function() {
        update_select_all_button();
    });

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
            url: url_contactcenter + 'ajax_replicate_engine',
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
</script>

<!-- Create Campaign Tag Modal -->
<div class="modal fade" id="createCampaignTagModal" tabindex="-1" role="dialog">
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
                    <label for="new_campaign_tag_name"><?php echo _l('tag_name'); ?> <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="new_campaign_tag_name" placeholder="<?php echo _l('enter_tag_name'); ?>">
                    <small class="text-muted"><?php echo _l('tag_name_help'); ?></small>
                </div>
                <div id="campaign_tag_creation_message" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-primary" id="create_campaign_tag_btn"><?php echo _l('create_tag'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Create new campaign tag functionality
    $('#create_campaign_tag_btn').on('click', function() {
        var tagName = $('#new_campaign_tag_name').val().trim();
        
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
                    var $select = $('#campaign_tag_id');
                    var option = $('<option></option>').attr('value', data.tag_id).text(data.tag_name);
                    $select.append(option);
                    $select.selectpicker('refresh');
                    
                    // Select the newly created tag
                    $select.selectpicker('val', data.tag_id);
                    
                    // Show success message
                    $('#campaign_tag_creation_message')
                        .removeClass('alert-danger')
                        .addClass('alert alert-success')
                        .html('<i class="fa fa-check"></i> ' + (data.message || '<?php echo _l('tag_created_successfully'); ?>'))
                        .show();
                    
                    // Clear input
                    $('#new_campaign_tag_name').val('');
                    
                    // Close modal after 1.5 seconds
                    setTimeout(function() {
                        $('#createCampaignTagModal').modal('hide');
                        $('#campaign_tag_creation_message').hide().removeClass('alert-success');
                    }, 1500);
                } else {
                    $('#campaign_tag_creation_message')
                        .removeClass('alert-success')
                        .addClass('alert alert-danger')
                        .html('<i class="fa fa-exclamation-triangle"></i> ' + (data.message || '<?php echo _l('tag_creation_failed'); ?>'))
                        .show();
                }
                
                $btn.prop('disabled', false).html('<?php echo _l('create_tag'); ?>');
            },
            error: function() {
                $('#campaign_tag_creation_message')
                    .removeClass('alert-success')
                    .addClass('alert alert-danger')
                    .html('<i class="fa fa-exclamation-triangle"></i> <?php echo _l('error_occurred'); ?>')
                    .show();
                $btn.prop('disabled', false).html('<?php echo _l('create_tag'); ?>');
            }
        });
    });
    
    // Allow Enter key to create tag
    $('#new_campaign_tag_name').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#create_campaign_tag_btn').click();
        }
    });
    
    // Clear message when modal is closed
    $('#createCampaignTagModal').on('hidden.bs.modal', function() {
        $('#new_campaign_tag_name').val('');
        $('#campaign_tag_creation_message').hide().removeClass('alert-success alert-danger');
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

<!-- Campaign Templates Modal -->
<style>
#modalCampaignTemplates .modal-content{background:#1a1f25;color:#fff;border:1px solid rgba(0,224,155,0.15);}
#modalCampaignTemplates .modal-header{border-bottom:1px solid rgba(0,224,155,0.12);padding:18px 20px 14px;}
#modalCampaignTemplates .modal-title{color:#fff;}
#modalCampaignTemplates .modal-footer{background:rgba(0,0,0,0.15);border-top:1px solid rgba(255,255,255,0.06);}
#modalCampaignTemplates .nav-tabs{background:transparent;border-bottom:1px solid rgba(255,255,255,0.08)!important;}
#modalCampaignTemplates .nav-tabs>li>a{color:rgba(255,255,255,0.55);border:none;border-radius:0;padding:10px 16px;}
#modalCampaignTemplates .nav-tabs>li>a:hover{color:#fff;background:rgba(255,255,255,0.04);border:none;}
#modalCampaignTemplates .nav-tabs>li.active>a,#modalCampaignTemplates .nav-tabs>li.active>a:focus,#modalCampaignTemplates .nav-tabs>li.active>a:hover{color:#00e09b;background:transparent;border:none;border-bottom:2px solid #00e09b;}
#modalCampaignTemplates .tab-content{max-height:60vh;overflow-y:auto;}
#modalCampaignTemplates .close{color:#fff;opacity:.6;text-shadow:none;}
#modalCampaignTemplates .close:hover{opacity:1;}
#modalCampaignTemplates .btn-default{background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.12);color:#fff;}
.ctpl-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;}
.ctpl-card{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:10px;padding:16px;transition:.15s;min-height:120px;display:flex;flex-direction:column;}
.ctpl-card:hover{border-color:#00e09b;background:rgba(0,224,155,0.06);}
.ctpl-card h5{margin:0 0 4px;font-size:14px;font-weight:600;color:#fff;}
.ctpl-card .ctpl-desc{font-size:12px;color:rgba(255,255,255,0.45);flex:1;}
.ctpl-card .ctpl-badge{display:inline-block;font-size:11px;padding:2px 8px;border-radius:10px;background:rgba(0,224,155,0.15);color:#00e09b;font-weight:600;}
</style>

<div class="modal fade" id="modalCampaignTemplates" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width:880px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-layer-group" style="color:#00e09b;"></i> <?= _l('contac_campaign_tpl_title'); ?></h4>
                <p style="margin:4px 0 0;font-size:13px;color:rgba(255,255,255,0.5);"><?= _l('contac_campaign_tpl_desc'); ?></p>
            </div>
            <div class="modal-body" style="padding:0;">
                <ul class="nav nav-tabs" style="padding:0 20px;margin:0;">
                    <li class="active"><a data-toggle="tab" href="#ctpl_sales" style="font-weight:600;font-size:13px;"><i class="fa fa-chart-line" style="color:#f0ad4e;"></i> <?= _l('contac_campaign_tpl_tab_sales'); ?></a></li>
                    <li><a data-toggle="tab" href="#ctpl_nurture" style="font-weight:600;font-size:13px;"><i class="fa fa-seedling" style="color:#5bc0de;"></i> <?= _l('contac_campaign_tpl_tab_nurture'); ?></a></li>
                    <li><a data-toggle="tab" href="#ctpl_special" style="font-weight:600;font-size:13px;"><i class="fa fa-star" style="color:#d9534f;"></i> <?= _l('contac_campaign_tpl_tab_special'); ?></a></li>
                </ul>
                <div class="tab-content" style="padding:18px 20px;">
                    <div id="ctpl_sales" class="tab-pane active"></div>
                    <div id="ctpl_nurture" class="tab-pane"></div>
                    <div id="ctpl_special" class="tab-pane"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="ctpl_progress" style="display:none;float:left;">
                    <i class="fa fa-spinner fa-spin" style="color:#00e09b;"></i>
                    <span id="ctpl_progress_text" style="color:rgba(255,255,255,0.7);font-size:13px;"></span>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
var _campaignTemplates = {
    sales: [
        {
            name: '<?= _l('contac_campaign_tpl_welcome'); ?>',
            icon: 'fa-hand',
            desc: '<?= _l('contac_campaign_tpl_welcome_desc'); ?>',
            config: {
                con_title: '<?= _l('contac_campaign_tpl_welcome'); ?>',
                date_filter_type: 'creation_date',
                start_time: '08:00',
                end_time: '18:00',
                leads_day: 50,
                daily_limit: 200,
                batch_size: 5,
                batch_cooldown: 5,
                message_interval_min: 2,
                message_interval_max: 5,
                stop_on_reply: 1
            },
            messages: [
                'Olá {name}! 👋 Tudo bem? Eu sou da [EMPRESA]. Vi que você demonstrou interesse em nossos serviços e gostaria de me apresentar. Estou aqui para ajudar no que precisar!',
                'Oi {name}, passando para compartilhar um pouco do que fazemos. [DESCREVA BREVEMENTE O VALOR/DIFERENCIAL]. Nossos clientes têm alcançado ótimos resultados!',
                '{name}, não quero tomar seu tempo, mas acredito que podemos te ajudar. Posso te enviar mais informações ou agendar uma conversa rápida? Fico à disposição! 😊'
            ]
        },
        {
            name: '<?= _l('contac_campaign_tpl_appointment'); ?>',
            icon: 'fa-calendar-check',
            desc: '<?= _l('contac_campaign_tpl_appointment_desc'); ?>',
            config: {
                con_title: '<?= _l('contac_campaign_tpl_appointment'); ?>',
                date_filter_type: 'creation_date',
                start_time: '09:00',
                end_time: '17:00',
                leads_day: 40,
                daily_limit: 150,
                batch_size: 5,
                batch_cooldown: 5,
                message_interval_min: 3,
                message_interval_max: 6,
                stop_on_reply: 1
            },
            messages: [
                'Oi {name}! 📅 Gostaria de agendar uma conversa rápida para entender melhor suas necessidades e mostrar como podemos te ajudar. Temos horários disponíveis essa semana, qual o melhor para você?',
                '{name}, sei que sua agenda é corrida, mas tenho certeza que 15 minutinhos serão suficientes para eu te mostrar algo que pode fazer diferença para você. Posso reservar um horário amanhã?',
                'Última chamada, {name}! 🔔 Reservei um horário especial para conversarmos. Se não for possível essa semana, me diga a melhor data e eu me organizo. Espero seu retorno!'
            ]
        },
        {
            name: '<?= _l('contac_campaign_tpl_proposal_followup'); ?>',
            icon: 'fa-file-alt',
            desc: '<?= _l('contac_campaign_tpl_proposal_followup_desc'); ?>',
            config: {
                con_title: '<?= _l('contac_campaign_tpl_proposal_followup'); ?>',
                date_filter_type: 'creation_date',
                start_time: '09:00',
                end_time: '18:00',
                leads_day: 40,
                daily_limit: 150,
                batch_size: 5,
                batch_cooldown: 5,
                message_interval_min: 3,
                message_interval_max: 6,
                stop_on_reply: 1
            },
            messages: [
                'Oi {name}! 📄 Passando para saber se você teve chance de analisar nossa proposta. Posso tirar alguma dúvida ou agendar uma conversa rápida para explicar melhor algum ponto?',
                '{name}, entendo que decisões levam tempo. Se precisar de ajustes na proposta ou mais informações, estou à disposição. O que acha de conversarmos brevemente?',
                '{name}, última vez que passo por aqui sobre a proposta. Se quiser seguir em frente ou tiver alguma objeção, me conte! Estou aqui para ajudar no que precisar. 😊'
            ]
        },
        {
            name: '<?= _l('contac_campaign_tpl_promotion'); ?>',
            icon: 'fa-tags',
            desc: '<?= _l('contac_campaign_tpl_promotion_desc'); ?>',
            config: {
                con_title: '<?= _l('contac_campaign_tpl_promotion'); ?>',
                date_filter_type: 'creation_date',
                start_time: '09:00',
                end_time: '20:00',
                leads_day: 100,
                daily_limit: 500,
                batch_size: 10,
                batch_cooldown: 3,
                message_interval_min: 1,
                message_interval_max: 3,
                stop_on_reply: 1
            },
            messages: [
                '🔥 {name}, tenho uma oferta IMPERDÍVEL para você! Por tempo limitado, estamos com [DESCREVA A OFERTA/DESCONTO]. Essa é a chance perfeita para [BENEFÍCIO]. Quer saber mais?',
                '⏰ {name}, só lembrando que a promoção especial termina em breve! Não deixe essa oportunidade passar. Responda aqui e garanta já a sua!'
            ]
        },
        {
            name: '<?= _l('contac_campaign_tpl_reengagement'); ?>',
            icon: 'fa-sync-alt',
            desc: '<?= _l('contac_campaign_tpl_reengagement_desc'); ?>',
            config: {
                con_title: '<?= _l('contac_campaign_tpl_reengagement'); ?>',
                date_filter_type: 'last_contact',
                start_time: '09:00',
                end_time: '18:00',
                leads_day: 30,
                daily_limit: 100,
                batch_size: 5,
                batch_cooldown: 5,
                message_interval_min: 3,
                message_interval_max: 8,
                stop_on_reply: 1
            },
            messages: [
                'Oi {name}, quanto tempo! 😊 Passando para saber como você está. Temos muitas novidades por aqui e lembrei de você. Posso te contar?',
                '{name}, temos algo novo que pode ser do seu interesse: [NOVIDADE/OFERTA ESPECIAL]. Muitos clientes como você já estão aproveitando. Quer saber mais detalhes?',
                '{name}, é a última vez que passo por aqui. Sei que a vida é corrida, mas se em algum momento precisar, estou à disposição. Te desejo tudo de bom! 🙏'
            ]
        },
        {
            name: '<?= _l('contac_campaign_tpl_postsale'); ?>',
            icon: 'fa-handshake',
            desc: '<?= _l('contac_campaign_tpl_postsale_desc'); ?>',
            config: {
                con_title: '<?= _l('contac_campaign_tpl_postsale'); ?>',
                date_filter_type: 'creation_date',
                start_time: '09:00',
                end_time: '17:00',
                leads_day: 30,
                daily_limit: 100,
                batch_size: 5,
                batch_cooldown: 5,
                message_interval_min: 3,
                message_interval_max: 6,
                stop_on_reply: 1
            },
            messages: [
                'Oi {name}! 🎉 Queria agradecer por ter escolhido a gente. Espero que esteja tendo uma ótima experiência! Se precisar de qualquer coisa, pode contar comigo.',
                '{name}, como está sendo sua experiência até aqui? Adoraria ouvir seu feedback! Sua opinião nos ajuda a melhorar cada vez mais. Pode me contar o que achou? ⭐',
                '{name}, obrigado pelo feedback! Sabia que você pode indicar amigos e ganhar benefícios? Fale com alguém especial sobre a gente e ambos ganham [BENEFÍCIO DA INDICAÇÃO]! 🤝'
            ]
        }
    ],
    nurture: [
        {
            name: '<?= _l('contac_campaign_tpl_educational'); ?>',
            icon: 'fa-graduation-cap',
            desc: '<?= _l('contac_campaign_tpl_educational_desc'); ?>',
            config: {
                con_title: '<?= _l('contac_campaign_tpl_educational'); ?>',
                date_filter_type: 'creation_date',
                start_time: '08:00',
                end_time: '18:00',
                leads_day: 40,
                daily_limit: 150,
                batch_size: 5,
                batch_cooldown: 5,
                message_interval_min: 3,
                message_interval_max: 6,
                stop_on_reply: 1
            },
            messages: [
                '💡 Dica #1 para você, {name}! Sabia que [DICA/INFORMAÇÃO ÚTIL RELACIONADA AO SEU NICHO]? Isso pode fazer toda a diferença nos seus resultados!',
                '📚 Dica #2, {name}! Outro ponto importante: [SEGUNDA DICA DE VALOR]. Nossos clientes que aplicam isso têm visto [RESULTADO POSITIVO].',
                '🎯 Dica #3, {name}! Para completar: [TERCEIRA DICA]. Combinando tudo isso, os resultados são ainda melhores!',
                '{name}, espero que as dicas tenham sido úteis! Se quiser se aprofundar, temos [MATERIAL/SERVIÇO] que pode te ajudar a ir ainda mais longe. Posso te enviar? 😊'
            ]
        },
        {
            name: '<?= _l('contac_campaign_tpl_event'); ?>',
            icon: 'fa-calendar',
            desc: '<?= _l('contac_campaign_tpl_event_desc'); ?>',
            config: {
                con_title: '<?= _l('contac_campaign_tpl_event'); ?>',
                date_filter_type: 'creation_date',
                start_time: '09:00',
                end_time: '20:00',
                leads_day: 80,
                daily_limit: 300,
                batch_size: 10,
                batch_cooldown: 3,
                message_interval_min: 1,
                message_interval_max: 3,
                stop_on_reply: 1
            },
            messages: [
                '🎤 {name}, você está convidado(a)! Teremos um evento especial: [NOME DO EVENTO] no dia [DATA] às [HORA]. Vai ser incrível, com [DESTAQUES]. Garanta sua vaga!',
                '⏰ {name}, só lembrando do nosso evento [NOME] que acontece em breve! As vagas são limitadas. Posso confirmar sua presença? Responda SIM para garantir! 🎯'
            ]
        },
        {
            name: '<?= _l('contac_campaign_tpl_feedback'); ?>',
            icon: 'fa-star',
            desc: '<?= _l('contac_campaign_tpl_feedback_desc'); ?>',
            config: {
                con_title: '<?= _l('contac_campaign_tpl_feedback'); ?>',
                date_filter_type: 'last_contact',
                start_time: '10:00',
                end_time: '17:00',
                leads_day: 30,
                daily_limit: 100,
                batch_size: 5,
                batch_cooldown: 5,
                message_interval_min: 3,
                message_interval_max: 6,
                stop_on_reply: 1
            },
            messages: [
                'Oi {name}! 😊 Gostaríamos muito de saber sua opinião sobre nosso atendimento/serviço. De 1 a 10, como você avaliaria sua experiência conosco? Seu feedback é muito importante!',
                '{name}, sua opinião conta muito para nós! Se puder responder rapidinho, ficaremos muito gratos. Também aceitamos sugestões de melhoria! ⭐'
            ]
        },
        {
            name: '<?= _l('contac_campaign_tpl_upsell'); ?>',
            icon: 'fa-chart-line',
            desc: '<?= _l('contac_campaign_tpl_upsell_desc'); ?>',
            config: {
                con_title: '<?= _l('contac_campaign_tpl_upsell'); ?>',
                date_filter_type: 'creation_date',
                start_time: '09:00',
                end_time: '18:00',
                leads_day: 30,
                daily_limit: 100,
                batch_size: 5,
                batch_cooldown: 5,
                message_interval_min: 3,
                message_interval_max: 6,
                stop_on_reply: 1
            },
            messages: [
                'Oi {name}! 🚀 Como você já é nosso cliente, queria te apresentar algo que pode complementar perfeitamente o que você já tem: [PRODUTO/SERVIÇO COMPLEMENTAR]. Interessado(a)?',
                '{name}, clientes que combinam [PRODUTO ATUAL] com [PRODUTO NOVO] têm alcançado [BENEFÍCIO ESPECÍFICO]. E temos uma condição especial para quem já é cliente! Quer saber mais?',
                '{name}, preparamos uma proposta personalizada para você! Com uma condição exclusiva de [DESCONTO/BENEFÍCIO]. Essa oferta é válida por tempo limitado. Posso enviar os detalhes? 😊'
            ]
        }
    ],
    special: [
        {
            name: '<?= _l('contac_campaign_tpl_birthday'); ?>',
            icon: 'fa-gift',
            desc: '<?= _l('contac_campaign_tpl_birthday_desc'); ?>',
            config: {
                con_title: '🎂 <?= _l('contac_campaign_tpl_birthday'); ?>',
                date_filter_type: 'birthday',
                start_time: '08:00',
                end_time: '20:00',
                leads_day: 50,
                daily_limit: 200,
                batch_size: 10,
                batch_cooldown: 3,
                message_interval_min: 1,
                message_interval_max: 3,
                stop_on_reply: 0
            },
            messages: [
                '🎂🎉 Parabéns, {name}! Hoje é seu dia especial e não poderíamos deixar de celebrar com você! Desejamos muita saúde, felicidade e sucesso. Aproveite muito! 🥳',
                '🎁 E como presente de aniversário, {name}, preparamos algo especial para você: [OFERTA/DESCONTO EXCLUSIVO DE ANIVERSÁRIO]. Válido por [PRAZO]. Aproveite! 😊'
            ]
        },
        {
            name: '<?= _l('contac_campaign_tpl_seasonal'); ?>',
            icon: 'fa-calendar',
            desc: '<?= _l('contac_campaign_tpl_seasonal_desc'); ?>',
            config: {
                con_title: '<?= _l('contac_campaign_tpl_seasonal'); ?>',
                date_filter_type: 'creation_date',
                start_time: '09:00',
                end_time: '20:00',
                leads_day: 100,
                daily_limit: 500,
                batch_size: 10,
                batch_cooldown: 3,
                message_interval_min: 1,
                message_interval_max: 3,
                stop_on_reply: 1
            },
            messages: [
                '🎄 {name}, [SAUDAÇÃO DA DATA COMEMORATIVA]! Aproveite para conferir nossas ofertas especiais de [NOME DA DATA]. Condições imperdíveis por tempo limitado!',
                '⏰ {name}, última chance! As ofertas de [NOME DA DATA] estão acabando. Não perca a oportunidade de [BENEFÍCIO]. Responda para garantir a sua! 🎯'
            ]
        }
    ]
};

function _ctplRenderCards(category, containerId) {
    var templates = _campaignTemplates[category];
    if (!templates || !templates.length) return;
    var html = '<div class="ctpl-grid">';
    templates.forEach(function(tpl, i) {
        html += '<div class="ctpl-card">'
            + '<div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">'
            + '<div style="width:36px;height:36px;border-radius:8px;background:rgba(0,224,155,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fa-solid ' + tpl.icon + '" style="color:#00b87a;font-size:15px;"></i></div>'
            + '<div><h5>' + tpl.name + '</h5>'
            + '<span class="ctpl-badge">' + tpl.messages.length + ' <?= _l('contac_campaign_tpl_messages'); ?></span></div>'
            + '</div>'
            + '<div class="ctpl-desc">' + tpl.desc + '</div>'
            + '<div style="margin-top:10px;text-align:right;"><button class="btn btn-sm" style="background:#00e09b;color:#0f1419;font-weight:600;border:none;border-radius:6px;padding:5px 16px;" onclick="apply_campaign_template(\'' + category + '\',' + i + ')"><i class="fa fa-check tw-mr-1"></i> <?= _l('contac_campaign_tpl_apply'); ?></button></div>'
            + '</div>';
    });
    html += '</div>';
    document.getElementById(containerId).innerHTML = html;
}

jQuery(function($){
    $('#modalCampaignTemplates').on('shown.bs.modal', function() {
        _ctplRenderCards('sales', 'ctpl_sales');
        _ctplRenderCards('nurture', 'ctpl_nurture');
        _ctplRenderCards('special', 'ctpl_special');
    });
});

function apply_campaign_template(category, index) {
    var $ = jQuery;
    var tpl = _campaignTemplates[category][index];
    if (!tpl) { alert('Template not found'); return; }

    var label = _ctplHtmlDec('<?= _l('contac_campaign_tpl_apply_confirm'); ?>');
    if (!confirm(label + ' "' + tpl.name + '"? (' + tpl.messages.length + ' <?= _l('contac_campaign_tpl_messages'); ?>)')) return;

    $('#ctpl_progress').show();
    $('#ctpl_progress_text').text('<?= _l('contac_campaign_tpl_creating'); ?>');

    $.ajax({
        url: admin_url + 'contactcenter/add_conversation_engine_template',
        method: 'POST',
        data: {
            config: tpl.config,
            messages: tpl.messages
        },
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#ctpl_progress_text').html('<span style="color:#00e09b;"><?= _l('contac_campaign_tpl_success'); ?></span>');
                setTimeout(function(){ location.reload(); }, 1000);
            } else {
                $('#ctpl_progress_text').html('<span style="color:#f44336;">' + (res.error || '<?= _l('contac_campaign_tpl_error'); ?>') + '</span>');
            }
        },
        error: function() {
            $('#ctpl_progress_text').html('<span style="color:#f44336;"><?= _l('contac_campaign_tpl_error'); ?></span>');
        }
    });
}

function _ctplHtmlDec(s) {
    if (!s) return '';
    var t = document.createElement('textarea');
    t.innerHTML = s;
    return t.value;
}
</script>
</body>

</html>