<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
#modalTemplates .modal-content{background:#1a1f25;color:#fff;border:1px solid rgba(0,224,155,0.15);}
#modalTemplates .modal-header{border-bottom:1px solid rgba(0,224,155,0.12);padding:18px 20px 14px;}
#modalTemplates .modal-title{color:#fff;}
#modalTemplates .modal-footer{background:rgba(0,0,0,0.15);}
#modalTemplates .nav-tabs{background:transparent;border-bottom:1px solid rgba(255,255,255,0.08)!important;}
#modalTemplates .nav-tabs>li>a{color:rgba(255,255,255,0.55);border:none;border-radius:0;padding:10px 16px;}
#modalTemplates .nav-tabs>li>a:hover{color:#fff;background:rgba(255,255,255,0.04);border:none;}
#modalTemplates .nav-tabs>li.active>a,#modalTemplates .nav-tabs>li.active>a:focus,#modalTemplates .nav-tabs>li.active>a:hover{color:#00e09b;background:transparent;border:none;border-bottom:2px solid #00e09b;}
#modalTemplates .tab-content{max-height:55vh;overflow-y:auto;}
#modalTemplates .close{color:#fff;opacity:.6;text-shadow:none;}
#modalTemplates .close:hover{opacity:1;}
#modalTemplates .btn-default{background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.12);color:#fff;}
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <i class="fa fa-robot tw-mr-2" style="color:#00e09b;font-size:28px;"></i>
                            <span><?= _l('contac_auto_followup_title'); ?></span>
                        </h4>
                        <p class="text-muted"><?= _l('contac_auto_followup_description'); ?></p>
                        <hr class="hr-panel-separator" />

                        <div class="tw-mb-2 tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-3">
                            <div>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#modalFollowup" onclick="reset_form()">
                                    <i class="fa-regular fa-plus tw-mr-1"></i> <?= _l('contac_auto_followup_new'); ?>
                                </button>
                                <button class="btn btn-info" data-toggle="modal" data-target="#modalTemplates">
                                    <i class="fa fa-layer-group tw-mr-1"></i> <?= _l('contac_auto_followup_use_template'); ?>
                                </button>
                                <a href="<?= admin_url('contactcenter/auto_followup_queue'); ?>" class="btn btn-default">
                                    <i class="fa fa-clock tw-mr-1"></i> <?= _l('contac_auto_followup_view_queue'); ?>
                                </a>
                            </div>
                            <div class="tw-flex tw-items-center tw-gap-2">
                                <div class="checkbox tw-mb-0" style="margin:0;">
                                    <input type="checkbox" id="filter_show_inactive" <?= isset($show_inactive) && $show_inactive ? 'checked' : ''; ?>>
                                    <label for="filter_show_inactive" style="font-weight:500;margin-bottom:0;cursor:pointer;">
                                        <i class="fa fa-filter tw-mr-1"></i> <?= _l('show_inactive_campaigns'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Sync Last Contact -->
                        <div id="sync_panel" style="margin-top:12px;padding:12px 16px;background:rgba(3,169,244,0.06);border:1px solid rgba(3,169,244,0.18);border-radius:8px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <i class="fa fa-sync" style="color:#03a9f4;font-size:18px;"></i>
                                <div>
                                    <strong style="font-size:13px;"><?= _l('contac_sync_lastcontact_title'); ?></strong>
                                    <div style="font-size:11px;color:#888;"><?= _l('contac_sync_lastcontact_desc'); ?></div>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                <span id="sync_status" style="font-size:12px;color:#888;"></span>
                                <select id="sync_limit" class="form-control" style="width:auto;height:30px;padding:2px 8px;font-size:12px;display:inline-block;">
                                    <option value="100"><?= _l('contac_sync_last_n', '100'); ?></option>
                                    <option value="500" selected><?= _l('contac_sync_last_n', '500'); ?></option>
                                    <option value="1000"><?= _l('contac_sync_last_n', '1.000'); ?></option>
                                    <option value="5000"><?= _l('contac_sync_last_n', '5.000'); ?></option>
                                    <option value="0"><?= _l('contac_sync_all_leads'); ?></option>
                                </select>
                                <button type="button" id="btnSyncLastContact" class="btn btn-sm" style="background:#03a9f4;color:#fff;border:none;border-radius:6px;font-weight:600;padding:5px 16px;">
                                    <i class="fa fa-sync" id="sync_icon"></i> <?= _l('contac_sync_lastcontact_btn'); ?>
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive" style="margin-top:15px;">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= _l('contac_auto_followup_col_title'); ?></th>
                                        <th><?= _l('contac_auto_followup_col_objective'); ?></th>
                                        <th><?= _l('contac_auto_followup_col_interval'); ?></th>
                                        <th><?= _l('contac_auto_followup_col_daily_limit'); ?></th>
                                        <th><?= _l('contac_auto_followup_col_window'); ?></th>
                                        <th><?= _l('contac_phone_status'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($followups)): foreach ($followups as $fu): ?>
                                    <tr>
                                        <td><?= $fu->id; ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($fu->title); ?></strong>
                                            <div class="row-options">
                                                <a href="javascript:void(0);" onclick="edit_followup(<?= $fu->id; ?>)"><?= _l('contac_editar'); ?></a> |
                                                <a href="<?= admin_url('contactcenter/auto_followup_queue/' . $fu->id); ?>"><?= _l('contac_auto_followup_view_queue'); ?></a> |
                                                <a href="javascript:void(0);" onclick="test_generation(<?= $fu->id; ?>)" class="text-info"><?= _l('contac_auto_followup_test_generate'); ?></a> |
                                                <a href="javascript:void(0);" onclick="start_generation(<?= $fu->id; ?>)" class="text-success"><i class="fa fa-play"></i> <?= _l('contac_auto_followup_generate_now'); ?></a> |
                                                <a href="javascript:void(0);" class="text-danger" onclick="delete_followup(<?= $fu->id; ?>)"><?= _l('contac_excluir'); ?></a>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="label label-default"><?= _l('contac_auto_followup_obj_' . $fu->objective); ?></span>
                                        </td>
                                        <td><?= $fu->time_amount . ' ' . _l('contac_time_unit_' . $fu->time_unit); ?></td>
                                        <td><?= $fu->daily_limit; ?></td>
                                        <td><?= substr($fu->start_time, 0, 5) . ' - ' . substr($fu->end_time, 0, 5); ?></td>
                                        <td>
                                            <div class="onoffswitch">
                                                <input type="checkbox" class="onoffswitch-checkbox" id="fu_status_<?= $fu->id; ?>"
                                                       data-id="<?= $fu->id; ?>" <?= $fu->status == 1 ? 'checked' : ''; ?>
                                                       onchange="toggle_followup_status(<?= $fu->id; ?>, this.checked ? 1 : 0)">
                                                <label class="onoffswitch-label" for="fu_status_<?= $fu->id; ?>"></label>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?php endforeach; else: ?>
                                    <tr><td colspan="8" class="text-center text-muted"><?= _l('contac_auto_followup_empty'); ?></td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="modalFollowup" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?= form_open(admin_url('contactcenter/add_auto_followup'), ['id' => 'followup_form']); ?>
            <input type="hidden" name="id" id="fu_id" value="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-robot"></i> <?= _l('contac_auto_followup_new'); ?></h4>
            </div>
            <div class="modal-body" style="padding:20px 25px;">

                <!-- Section: General -->
                <div style="background:rgba(0,224,155,0.06);border:1px solid rgba(0,224,155,0.18);border-radius:8px;padding:16px 18px;margin-bottom:18px;">
                    <h5 style="margin:0 0 14px;font-weight:600;font-size:14px;color:#fff;">
                        <i class="fa fa-bullseye" style="color:#00e09b;margin-right:6px;"></i> <?= _l('contac_auto_followup_section_general'); ?>
                    </h5>
                    <div class="form-group" style="margin-bottom:12px;">
                        <label style="font-weight:500;"><?= _l('contac_auto_followup_field_title'); ?> <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="fu_title" class="form-control" placeholder="<?= _l('contac_auto_followup_title_placeholder'); ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom:12px;">
                                <label style="font-weight:500;"><?= _l('contac_auto_followup_field_objective'); ?></label>
                                <select name="objective" id="fu_objective" class="form-control" onchange="toggle_custom_obj()">
                                    <option value="lead_warmer"><?= _l('contac_auto_followup_obj_lead_warmer'); ?></option>
                                    <option value="appointment"><?= _l('contac_auto_followup_obj_appointment'); ?></option>
                                    <option value="reactivation"><?= _l('contac_auto_followup_obj_reactivation'); ?></option>
                                    <option value="feedback"><?= _l('contac_auto_followup_obj_feedback'); ?></option>
                                    <option value="upsell"><?= _l('contac_auto_followup_obj_upsell'); ?></option>
                                    <option value="custom"><?= _l('contac_auto_followup_obj_custom'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom:12px;">
                                <label style="font-weight:500;"><?= _l('contac_auto_followup_field_tags'); ?></label>
                                <select name="tags[]" id="fu_tags" class="selectpicker" multiple data-live-search="true" data-width="100%">
                                    <?php foreach ($tagsArray as $tag): ?>
                                    <option value="<?= $tag['id']; ?>"><?= htmlspecialchars($tag['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted" style="font-size:11px;"><?= _l('contac_auto_followup_tags_help'); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="custom_obj_group" style="display:none;margin-bottom:12px;">
                        <label style="font-weight:500;"><?= _l('contac_auto_followup_field_custom_obj'); ?></label>
                        <textarea name="custom_objective" id="fu_custom_objective" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label style="font-weight:500;"><?= _l('contac_auto_followup_field_lead_status'); ?> <small class="text-muted">(<?= _l('optional'); ?>)</small></label>
                        <select name="lead_statuses[]" id="fu_lead_statuses" class="selectpicker" multiple data-live-search="true" data-width="100%" data-none-selected-text="<?= _l('contac_auto_followup_all_statuses'); ?>">
                            <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status['id']; ?>"><?= htmlspecialchars($status['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted" style="font-size:11px;"><?= _l('contac_auto_followup_status_help'); ?></small>
                    </div>
                </div>

                <!-- Section: Timing -->
                <div style="background:rgba(0,224,155,0.06);border:1px solid rgba(0,224,155,0.18);border-radius:8px;padding:16px 18px;margin-bottom:18px;">
                    <h5 style="margin:0 0 14px;font-weight:600;font-size:14px;color:#fff;">
                        <i class="fa fa-clock" style="color:#00e09b;margin-right:6px;"></i> <?= _l('contac_auto_followup_section_timing'); ?>
                    </h5>
                    <div class="row">
                        <div class="col-xs-6 col-sm-3">
                            <div class="form-group" style="margin-bottom:10px;">
                                <label style="font-weight:500;font-size:12px;"><?= _l('contac_auto_followup_field_time_amount'); ?></label>
                                <input type="number" name="time_amount" id="fu_time_amount" class="form-control" value="1" min="1" required>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <div class="form-group" style="margin-bottom:10px;">
                                <label style="font-weight:500;font-size:12px;"><?= _l('contac_auto_followup_field_time_unit'); ?></label>
                                <select name="time_unit" id="fu_time_unit" class="form-control">
                                    <option value="minutes"><?= _l('contac_time_unit_minutes'); ?></option>
                                    <option value="hours"><?= _l('contac_time_unit_hours'); ?></option>
                                    <option value="days" selected><?= _l('contac_time_unit_days'); ?></option>
                                    <option value="weeks"><?= _l('contac_time_unit_weeks'); ?></option>
                                    <option value="months"><?= _l('contac_time_unit_months'); ?></option>
                                    <option value="years"><?= _l('contac_time_unit_years'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <div class="form-group" style="margin-bottom:10px;">
                                <label style="font-weight:500;font-size:12px;"><?= _l('contac_auto_followup_field_window_from'); ?></label>
                                <input type="time" name="start_time" id="fu_start_time" class="form-control" value="08:00">
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-3">
                            <div class="form-group" style="margin-bottom:10px;">
                                <label style="font-weight:500;font-size:12px;"><?= _l('contac_auto_followup_field_window_to'); ?></label>
                                <input type="time" name="end_time" id="fu_end_time" class="form-control" value="18:00">
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info" style="padding:8px 12px;font-size:12px;margin:4px 0 0;border-radius:6px;" id="timing_help_alert">
                        <i class="fa fa-info-circle"></i> <span id="timing_help_text"><?= _l('contac_auto_followup_timing_help_default'); ?></span>
                    </div>
                </div>

                <!-- Section: Settings -->
                <div style="background:rgba(0,224,155,0.06);border:1px solid rgba(0,224,155,0.18);border-radius:8px;padding:16px 18px;">
                    <h5 style="margin:0 0 14px;font-weight:600;font-size:14px;color:#fff;">
                        <i class="fa fa-cog" style="color:#00e09b;margin-right:6px;"></i> <?= _l('contac_auto_followup_section_settings'); ?>
                    </h5>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group" style="margin-bottom:10px;">
                                <label style="font-weight:500;font-size:12px;"><?= _l('contac_auto_followup_field_device'); ?></label>
                                <select name="device_id" id="fu_device_id" class="form-control">
                                    <option value=""><?= _l('contac_auto_followup_device_system'); ?></option>
                                    <?php if (!empty($devices)): foreach ($devices as $dev): ?>
                                    <option value="<?= $dev->dev_id; ?>"><?= htmlspecialchars($dev->dev_name); ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group" style="margin-bottom:10px;">
                                <label style="font-weight:500;font-size:12px;"><?= _l('contac_auto_followup_field_daily_limit'); ?></label>
                                <input type="number" name="daily_limit" id="fu_daily_limit" class="form-control" value="50" min="1">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group" style="margin-bottom:0;">
                                <label style="font-weight:500;font-size:12px;"><?= _l('contac_auto_followup_field_gen_window'); ?></label>
                                <input type="number" name="generation_window_hours" id="fu_gen_window" class="form-control" value="2" min="1">
                                <small class="text-muted" style="font-size:11px;"><?= _l('contac_auto_followup_gen_window_help'); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<!-- Generate Now Modal -->
<div class="modal fade" id="modalGenerateNow" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="max-width:600px;">
        <div class="modal-content" style="background:#1a1f25;color:#fff;border:1px solid rgba(0,224,155,0.15);">
            <div class="modal-header" style="border-bottom:1px solid rgba(0,224,155,0.12);">
                <button type="button" class="close gen-close-btn" data-dismiss="modal" style="color:#fff;opacity:.6;text-shadow:none;"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-play-circle" style="color:#00e09b;"></i> <?= _l('contac_auto_followup_generate_now'); ?></h4>
            </div>
            <div class="modal-body">
                <div id="gen_step_count" style="text-align:center;padding:30px;">
                    <i class="fa fa-spinner fa-spin fa-2x" style="color:#00e09b;"></i>
                    <p style="margin-top:12px;color:rgba(255,255,255,0.6);"><?= _l('contac_auto_followup_gen_counting'); ?></p>
                </div>
                <div id="gen_debug_panel" style="display:none;"></div>
                <div id="gen_step_confirm" style="display:none;text-align:center;padding:20px;">
                    <div style="font-size:48px;color:#00e09b;margin-bottom:10px;"><i class="fa fa-users"></i></div>
                    <h3 id="gen_lead_count" style="color:#fff;margin:0 0 6px;"></h3>
                    <p id="gen_rule_name" style="color:rgba(255,255,255,0.5);margin-bottom:16px;"></p>
                    <p style="color:rgba(255,255,255,0.45);font-size:13px;"><?= _l('contac_auto_followup_gen_confirm_desc'); ?></p>
                    <button class="btn btn-lg" id="gen_start_btn" style="background:#00e09b;color:#0f1419;font-weight:600;border:none;border-radius:8px;padding:10px 40px;margin-top:10px;">
                        <i class="fa fa-bolt"></i> <?= _l('contac_auto_followup_gen_start'); ?>
                    </button>
                </div>
                <div id="gen_step_progress" style="display:none;padding:10px 0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                        <span style="font-size:13px;color:rgba(255,255,255,0.6);"><i class="fa fa-cog fa-spin" style="color:#00e09b;"></i> <?= _l('contac_auto_followup_gen_in_progress'); ?></span>
                        <span id="gen_progress_pct" style="font-weight:600;color:#00e09b;font-size:15px;">0%</span>
                    </div>
                    <div style="background:rgba(255,255,255,0.08);border-radius:6px;height:10px;overflow:hidden;margin-bottom:12px;">
                        <div id="gen_progress_bar" style="width:0%;height:100%;background:linear-gradient(90deg,#00e09b,#00b87a);border-radius:6px;transition:width 0.4s;"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:12px;color:rgba(255,255,255,0.4);margin-bottom:14px;">
                        <span><i class="fa fa-check" style="color:#00e09b;"></i> <?= _l('contac_auto_followup_gen_queued'); ?>: <strong id="gen_stat_queued" style="color:#fff;">0</strong></span>
                        <span><i class="fa fa-forward-step"></i> <?= _l('contac_auto_followup_gen_skipped'); ?>: <strong id="gen_stat_skipped" style="color:#fff;">0</strong></span>
                        <span><i class="fa fa-hourglass-half"></i> <?= _l('contac_auto_followup_gen_remaining'); ?>: <strong id="gen_stat_remaining" style="color:#fff;">0</strong></span>
                    </div>
                    <div id="gen_samples" style="max-height:220px;overflow-y:auto;"></div>
                </div>
                <div id="gen_step_done" style="display:none;text-align:center;padding:24px;">
                    <div style="font-size:48px;color:#00e09b;margin-bottom:10px;"><i class="fa fa-circle-check"></i></div>
                    <h3 style="color:#fff;margin:0 0 6px;"><?= _l('contac_auto_followup_gen_complete'); ?></h3>
                    <p id="gen_done_summary" style="color:rgba(255,255,255,0.5);margin-bottom:16px;"></p>
                    <a id="gen_view_queue_link" href="#" class="btn" style="background:#00e09b;color:#0f1419;font-weight:600;border:none;border-radius:8px;padding:8px 30px;">
                        <i class="fa fa-clock"></i> <?= _l('contac_auto_followup_view_queue'); ?>
                    </a>
                </div>
                <div id="gen_step_error" style="display:none;padding:20px;"></div>
            </div>
            <div class="modal-footer" style="background:rgba(0,0,0,0.15);border-top:1px solid rgba(0,224,155,0.12);">
                <button type="button" class="btn btn-default gen-close-btn" data-dismiss="modal" style="background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.12);color:#fff;"><?= _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Test Generation Modal -->
<div class="modal fade" id="modalTestGeneration" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width:800px;">
        <div class="modal-content" style="background:#1a1f25;color:#fff;border:1px solid rgba(0,224,155,0.15);">
            <div class="modal-header" style="border-bottom:1px solid rgba(0,224,155,0.12);">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.6;text-shadow:none;"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-flask" style="color:#00e09b;"></i> <?= _l('contac_auto_followup_test_title'); ?></h4>
                <p class="text-muted" style="margin:4px 0 0;font-size:12px;"><?= _l('contac_auto_followup_test_desc'); ?></p>
            </div>
            <div class="modal-body" style="max-height:65vh;overflow-y:auto;">
                <div id="test_loading" style="text-align:center;padding:40px;">
                    <i class="fa fa-spinner fa-spin fa-2x" style="color:#00e09b;"></i>
                    <p style="margin-top:12px;color:rgba(255,255,255,0.6);"><?= _l('contac_auto_followup_test_loading'); ?></p>
                </div>
                <div id="test_error" style="display:none;"></div>
                <div id="test_results" style="display:none;"></div>
            </div>
            <div class="modal-footer" style="background:rgba(0,0,0,0.15);border-top:1px solid rgba(0,224,155,0.12);">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.12);color:#fff;"><?= _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Templates Modal -->
<div class="modal fade" id="modalTemplates" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" style="max-width:820px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-layer-group" style="color:#00e09b;"></i> <?= _l('contac_auto_followup_templates_title'); ?></h4>
                <p class="text-muted" style="margin:4px 0 0;font-size:13px;"><?= _l('contac_auto_followup_templates_desc'); ?></p>
            </div>
            <div class="modal-body" style="padding:0;">
                <!-- Tabs -->
                <ul class="nav nav-tabs" style="padding:0 20px;margin:0;border-bottom:1px solid #e8e8e8;">
                    <li class="active"><a data-toggle="tab" href="#tpl_short" style="font-weight:600;font-size:13px;"><i class="fa fa-bolt" style="color:#f0ad4e;"></i> <?= _l('contac_auto_followup_tpl_short'); ?></a></li>
                    <li><a data-toggle="tab" href="#tpl_medium" style="font-weight:600;font-size:13px;"><i class="fa fa-calendar-week" style="color:#5bc0de;"></i> <?= _l('contac_auto_followup_tpl_medium'); ?></a></li>
                    <li><a data-toggle="tab" href="#tpl_long" style="font-weight:600;font-size:13px;"><i class="fa fa-calendar-alt" style="color:#d9534f;"></i> <?= _l('contac_auto_followup_tpl_long'); ?></a></li>
                </ul>
                <div class="tab-content" style="padding:18px 20px;" id="tpl_tab_content">
                    <div id="tpl_short" class="tab-pane active"></div>
                    <div id="tpl_medium" class="tab-pane"></div>
                    <div id="tpl_long" class="tab-pane"></div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e8e8e8;">
                <div id="tpl_progress" style="display:none;" class="text-left">
                    <i class="fa fa-spinner fa-spin" style="color:#00e09b;"></i>
                    <span id="tpl_progress_text"></span>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
(function _initAutoFollowup() {
    if (typeof jQuery === 'undefined') { return setTimeout(_initAutoFollowup, 100); }
    jQuery(function($) {

        $('#filter_show_inactive').on('change', function() {
            $.post(admin_url + 'contactcenter/auto_followup_filter_inactive', {
                show_inactive: this.checked ? 1 : 0
            }, function() { location.reload(); });
        });

        window.toggle_custom_obj = function() {
            $('#custom_obj_group').toggle($('#fu_objective').val() === 'custom');
        };

        var _unitDefaults = {minutes: 30, hours: 24, days: 1, weeks: 1, months: 1, years: 1};
        var _hoursMultiplier = {minutes: 1/60, hours: 1, days: 24, weeks: 168, months: 720, years: 8760};
        var _userEditedAmount = false;
        var _userEditedWindow = false;

        function updateTimingHelp() {
            var amount = parseInt($('#fu_time_amount').val()) || 1;
            var unit = $('#fu_time_unit').val();
            var totalHours = amount * (_hoursMultiplier[unit] || 1);
            var txt = '';
            if (totalHours < 1) {
                txt = '<?= _l('contac_auto_followup_timing_help_short'); ?>';
            } else if (totalHours <= 24) {
                txt = '<?= _l('contac_auto_followup_timing_help_medium'); ?>';
            } else {
                txt = '<?= _l('contac_auto_followup_timing_help_long'); ?>';
            }
            $('#timing_help_text').text(txt);

            if (!_userEditedWindow) {
                var suggestedWindow = Math.max(1, Math.min(Math.ceil(totalHours * 0.5), 24));
                if (totalHours < 1) suggestedWindow = 1;
                $('#fu_gen_window').val(suggestedWindow);
            }
        }

        $('#fu_time_amount').on('input', function() { _userEditedAmount = true; updateTimingHelp(); });
        $('#fu_time_unit').on('change', function() {
            if (!_userEditedAmount) {
                $('#fu_time_amount').val(_unitDefaults[$(this).val()] || 1);
            }
            updateTimingHelp();
        });
        $('#fu_gen_window').on('input', function() { _userEditedWindow = true; });

        updateTimingHelp();

        window.reset_form = function() {
            $('#fu_id').val('');
            $('#followup_form')[0].reset();
            $('#fu_tags').selectpicker('deselectAll');
            $('#fu_lead_statuses').selectpicker('deselectAll');
            $('#custom_obj_group').hide();
            _userEditedAmount = false;
            _userEditedWindow = false;
            updateTimingHelp();
            $('.modal-title').html('<i class="fa fa-robot"></i> <?= _l('contac_auto_followup_new'); ?>');
        };

        window.edit_followup = function(id) {
            $.get(admin_url + 'contactcenter/get_auto_followup_data', {id: id}, function(data) {
                if (!data) return;
                $('#fu_id').val(data.id);
                $('#fu_title').val(data.title);
                $('#fu_objective').val(data.objective);
                toggle_custom_obj();
                $('#fu_custom_objective').val(data.custom_objective || '');
                $('#fu_time_amount').val(data.time_amount);
                $('#fu_time_unit').val(data.time_unit);
                $('#fu_start_time').val((data.start_time || '08:00:00').substring(0, 5));
                $('#fu_end_time').val((data.end_time || '18:00:00').substring(0, 5));
                $('#fu_device_id').val(data.device_id || '');
                $('#fu_daily_limit').val(data.daily_limit);
                $('#fu_gen_window').val(data.generation_window_hours);
                if (data.tags) {
                    var tagArr = data.tags.split(',');
                    $('#fu_tags').selectpicker('val', tagArr);
                } else {
                    $('#fu_tags').selectpicker('deselectAll');
                }
                if (data.lead_statuses) {
                    var statusArr = data.lead_statuses.split(',');
                    $('#fu_lead_statuses').selectpicker('val', statusArr);
                } else {
                    $('#fu_lead_statuses').selectpicker('deselectAll');
                }
                $('.modal-title').html('<i class="fa fa-robot"></i> <?= _l('contac_editar'); ?>');
                $('#modalFollowup').modal('show');
            }, 'json');
        };

        window.delete_followup = function(id) {
            if (!confirm('<?= _l('contac_auto_followup_delete_confirm'); ?>')) return;
            $.post(admin_url + 'contactcenter/delete_auto_followup', {id: id}, function(r) {
                if (r.success) location.reload();
            }, 'json');
        };

        window.toggle_followup_status = function(id, status) {
            $.post(admin_url + 'contactcenter/toggle_auto_followup_status', {id: id, status: status}, function() {}, 'json');
        };

        window.test_generation = function(id) {
            $('#test_loading').show();
            $('#test_error, #test_results').hide().empty();
            $('#modalTestGeneration').modal('show');

            $.post(admin_url + 'contactcenter/test_followup_generation', {id: id}, function(resp) {
                $('#test_loading').hide();
                if (!resp.success) {
                    $('#test_error').html('<div class="alert" style="background:rgba(217,83,79,0.15);border:1px solid rgba(217,83,79,0.3);color:#e88;border-radius:8px;"><i class="fa fa-exclamation-triangle"></i> ' + (resp.error || 'Unknown error') + '</div>').show();
                    return;
                }

                var html = '<div style="margin-bottom:14px;padding:10px 14px;background:rgba(0,224,155,0.08);border-radius:8px;border:1px solid rgba(0,224,155,0.15);">'
                    + '<strong style="color:#00e09b;">' + resp.rule_title + '</strong>'
                    + '<span style="color:rgba(255,255,255,0.4);margin-left:10px;font-size:12px;">' + resp.objective + '</span>'
                    + '</div>';

                resp.samples.forEach(function(s, i) {
                    html += '<div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:16px;margin-bottom:14px;">'
                        + '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">'
                        + '<div><strong style="font-size:14px;">' + s.lead_name + '</strong>'
                        + '<span style="color:rgba(255,255,255,0.35);font-size:12px;margin-left:8px;">' + s.phone + '</span></div>'
                        + '<span style="font-size:11px;color:rgba(255,255,255,0.3);"><?= _l('contac_auto_followup_test_last_activity'); ?>: ' + s.last_activity + '</span>'
                        + '</div>'
                        + '<div style="margin-bottom:12px;">'
                        + '<label style="font-size:11px;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;"><?= _l('contac_auto_followup_test_chat_preview'); ?></label>'
                        + '<pre style="background:rgba(0,0,0,0.3);color:rgba(255,255,255,0.6);padding:10px;border-radius:6px;font-size:11px;white-space:pre-wrap;max-height:120px;overflow-y:auto;border:1px solid rgba(255,255,255,0.05);">' + $('<div/>').text(s.chat_preview).html() + '</pre>'
                        + '</div>'
                        + '<div>'
                        + '<label style="font-size:11px;color:#00e09b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;"><i class="fa fa-robot"></i> <?= _l('contac_auto_followup_test_generated_msg'); ?></label>'
                        + '<div style="background:rgba(0,224,155,0.06);border:1px solid rgba(0,224,155,0.15);padding:12px 14px;border-radius:8px;font-size:13px;line-height:1.5;color:#fff;white-space:pre-wrap;">' + $('<div/>').text(s.generated_msg).html() + '</div>'
                        + '</div>'
                        + '</div>';
                });

                html += '<p style="text-align:center;font-size:11px;color:rgba(255,255,255,0.3);margin-top:6px;"><i class="fa fa-info-circle"></i> <?= _l('contac_auto_followup_test_note'); ?></p>';

                $('#test_results').html(html).show();
            }, 'json').fail(function() {
                $('#test_loading').hide();
                $('#test_error').html('<div class="alert" style="background:rgba(217,83,79,0.15);border:1px solid rgba(217,83,79,0.3);color:#e88;border-radius:8px;"><i class="fa fa-exclamation-triangle"></i> Request failed. Check the server logs.</div>').show();
            });
        };

        // ── Sync Last Contact ──
        (function initSync() {
            var $btn = $('#btnSyncLastContact');
            var $status = $('#sync_status');
            var $icon = $('#sync_icon');
            var $limit = $('#sync_limit');
            var _syncing = false;
            var _totalLeads = 0;
            var _targetLeads = 0;
            var _totalUpdated = 0;
            var _totalScanned = 0;
            var _offset = 0;

            $.post(admin_url + 'contactcenter/sync_lastcontact_count', {}, function(r) {
                if (r && r.success) {
                    _totalLeads = r.total;
                    $status.html(r.total + ' <?= _l('contac_sync_lastcontact_total_leads'); ?>');
                }
            }, 'json');

            $btn.on('click', function() {
                if (_syncing) return;
                _syncing = true;
                _totalUpdated = 0;
                _totalScanned = 0;
                _offset = 0;
                _targetLeads = parseInt($limit.val()) || _totalLeads;
                if (_targetLeads === 0) _targetLeads = _totalLeads;
                $icon.addClass('fa-spin');
                $btn.prop('disabled', true);
                $limit.prop('disabled', true);
                runSyncBatch();
            });

            function runSyncBatch() {
                $.post(admin_url + 'contactcenter/sync_lastcontact_run', {offset: _offset, max_leads: _targetLeads}, function(r) {
                    if (!r.success) { finishSync(r.error || 'Error'); return; }

                    _totalUpdated += r.updated;
                    _totalScanned += r.scanned;
                    _offset = r.offset;

                    var displayTotal = _targetLeads < _totalLeads ? _targetLeads : _totalLeads;
                    var pct = displayTotal > 0 ? Math.min(Math.round((_totalScanned / displayTotal) * 100), 100) : 0;
                    $status.html(
                        '<span style="color:#03a9f4;font-weight:600;">' + pct + '%</span> '
                        + '<?= _l('contac_sync_lastcontact_scanned'); ?> ' + _totalScanned + '/' + displayTotal
                        + ' &middot; <span style="color:#00e09b;font-weight:600;">' + _totalUpdated + '</span> <?= _l('contac_sync_lastcontact_leads_updated'); ?>'
                    );

                    if (!r.done && _totalScanned < _targetLeads) {
                        runSyncBatch();
                    } else {
                        finishSync();
                    }
                }, 'json').fail(function() { finishSync('Request failed'); });
            }

            function finishSync(err) {
                _syncing = false;
                $icon.removeClass('fa-spin');
                $btn.prop('disabled', false);
                $limit.prop('disabled', false);
                if (err) {
                    $status.html('<span style="color:#d9534f;"><i class="fa fa-exclamation-triangle"></i> ' + err + '</span>');
                } else {
                    $status.html(
                        '<i class="fa fa-check" style="color:#00e09b;"></i> <?= _l('contac_sync_lastcontact_done'); ?> '
                        + '<strong>' + _totalUpdated + '</strong> <?= _l('contac_sync_lastcontact_leads_updated'); ?>'
                        + ' (<?= _l('contac_sync_lastcontact_scanned'); ?> ' + _totalScanned + ' leads)'
                    );
                }
            }
        })();

        // ── Generate Now ──
        var _genState = { id: 0, total: 0, queued: 0, skipped: 0, running: false };

        window.start_generation = function(id) {
            _genState = { id: id, total: 0, queued: 0, skipped: 0, running: false };
            $('#gen_step_count').show();
            $('#gen_step_confirm, #gen_step_progress, #gen_step_done, #gen_step_error').hide();
            $('#gen_debug_panel').hide().empty();
            $('#gen_samples').empty();
            $('#modalGenerateNow').modal('show');

            $.post(admin_url + 'contactcenter/count_followup_leads', {id: id}, function(resp) {
                if (!resp.success) {
                    showGenError(resp.error || 'Unknown error');
                    return;
                }
                _genState.total = resp.total;
                $('#gen_step_count').hide();

                if (resp.total === 0) {
                    showGenError('<?= _l('contac_auto_followup_gen_no_leads'); ?>');
                    if (resp.debug) { renderDebugInfo(resp.debug); }
                    return;
                }

                $('#gen_lead_count').text(resp.total + ' <?= _l('contac_auto_followup_gen_leads_found'); ?>');
                $('#gen_rule_name').text(resp.rule_title);
                $('#gen_view_queue_link').attr('href', admin_url + 'contactcenter/auto_followup_queue/' + id);
                if (resp.debug) { renderDebugInfo(resp.debug); }
                $('#gen_step_confirm').show();
            }, 'json').fail(function() { showGenError('Request failed'); });
        };

        $('#gen_start_btn').on('click', function() {
            $('#gen_step_confirm').hide();
            $('#gen_step_progress').show();
            $('#gen_stat_remaining').text(_genState.total);
            _genState.running = true;
            runGenBatch();
        });

        function runGenBatch() {
            if (!_genState.running) return;

            $.post(admin_url + 'contactcenter/generate_followup_batch', {id: _genState.id, batch_size: 20}, function(resp) {
                if (!resp.success) {
                    showGenError(resp.error || 'Generation failed');
                    return;
                }

                _genState.queued  += resp.queued;
                _genState.skipped += resp.skipped;

                var done = _genState.queued + _genState.skipped;
                var pct  = _genState.total > 0 ? Math.min(Math.round((done / _genState.total) * 100), 100) : 0;

                $('#gen_progress_bar').css('width', pct + '%');
                $('#gen_progress_pct').text(pct + '%');
                $('#gen_stat_queued').text(_genState.queued);
                $('#gen_stat_skipped').text(_genState.skipped);
                $('#gen_stat_remaining').text(resp.remaining);

                if (resp.samples && resp.samples.length) {
                    resp.samples.forEach(function(s) {
                        $('#gen_samples').prepend(
                            '<div style="background:rgba(0,224,155,0.06);border:1px solid rgba(0,224,155,0.1);border-radius:6px;padding:8px 10px;margin-bottom:6px;font-size:12px;">'
                            + '<strong style="color:#fff;">' + s.lead_name + '</strong>'
                            + '<span style="color:rgba(255,255,255,0.3);margin-left:6px;">' + s.phone + '</span>'
                            + '<span style="float:right;color:rgba(255,255,255,0.25);font-size:11px;">' + s.scheduled + '</span>'
                            + '<div style="color:rgba(255,255,255,0.55);margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + $('<span/>').text(s.message).html() + '</div>'
                            + '</div>'
                        );
                    });
                }

                if (resp.remaining > 0 && resp.processed > 0) {
                    runGenBatch();
                } else {
                    _genState.running = false;
                    $('#gen_progress_bar').css('width', '100%');
                    $('#gen_progress_pct').text('100%');
                    setTimeout(function() {
                        $('#gen_step_progress').hide();
                        $('#gen_done_summary').text(_genState.queued + ' <?= _l('contac_auto_followup_gen_msgs_queued'); ?>' + (_genState.skipped > 0 ? ' (' + _genState.skipped + ' <?= _l('contac_auto_followup_gen_skipped'); ?>)' : ''));
                        $('#gen_step_done').show();
                    }, 600);
                }
            }, 'json').fail(function() {
                _genState.running = false;
                showGenError('Request failed. Check server logs.');
            });
        }

        $('.gen-close-btn').on('click', function() {
            _genState.running = false;
        });

        function showGenError(msg) {
            $('#gen_step_count, #gen_step_confirm, #gen_step_progress, #gen_step_done').hide();
            $('#gen_step_error').html('<div style="text-align:center;padding:20px;"><div style="font-size:40px;color:#d9534f;margin-bottom:10px;"><i class="fa fa-circle-xmark"></i></div><p style="color:#e88;">' + msg + '</p></div>').show();
        }

        function renderDebugInfo(d) {
            var h = '<div style="margin:0 0 10px;padding:12px 14px;background:rgba(3,169,244,0.08);border:1px solid rgba(3,169,244,0.2);border-radius:8px;font-size:12px;text-align:left;">'
                + '<strong style="color:#03a9f4;"><i class="fa fa-search"></i> <?= _l('contac_auto_followup_debug_title'); ?></strong>'
                + '<div style="margin-top:8px;display:grid;grid-template-columns:1fr 1fr;gap:4px 16px;color:rgba(255,255,255,0.7);">'
                + '<div><?= _l('contac_auto_followup_debug_interval'); ?>: <strong>' + d.hours_equivalent + 'h</strong></div>'
                + '<div><?= _l('contac_auto_followup_debug_cutoff'); ?>: <strong>' + d.cutoff_date + '</strong></div>'
                + '<div><?= _l('contac_auto_followup_debug_total_phone'); ?>: <strong>' + d.total_leads_with_phone + '</strong></div>'
                + '<div><?= _l('contac_auto_followup_debug_has_lastcontact'); ?>: <strong>' + d.has_lastcontact + '</strong></div>'
                + '<div><?= _l('contac_auto_followup_debug_match_time'); ?>: <strong>' + d.match_time_filter + '</strong></div>'
                + '<div><?= _l('contac_auto_followup_debug_has_cc_messages'); ?>: <strong style="color:#00e09b;">' + d.has_cc_messages + '</strong></div>'
                + '<div><?= _l('contac_auto_followup_debug_no_cc_messages'); ?>: <strong style="color:#f0ad4e;">' + d.no_cc_messages + '</strong> <span style="color:rgba(255,255,255,0.35);">(<?= _l('contac_auto_followup_debug_excluded'); ?>)</span></div>'
                + '<div><?= _l('contac_auto_followup_debug_in_queue'); ?>: <strong>' + d.already_in_queue + '</strong></div>'
                + '<div><?= _l('contac_auto_followup_debug_final'); ?>: <strong style="color:#00e09b;font-size:14px;">' + d.final_eligible + '</strong></div>'
                + '</div></div>';
            $('#gen_debug_panel').html(h).show();
        }

        // ── Batch Templates ──
        var _tplData = {
            short: [
                {
                    name: '<?= _l('contac_tpl_quick_response'); ?>',
                    icon: 'fa-bolt', desc: '<?= _l('contac_tpl_quick_response_desc'); ?>',
                    objective: 'lead_warmer',
                    points: [{a:30,u:'minutes'},{a:2,u:'hours'},{a:6,u:'hours'}]
                },
                {
                    name: '<?= _l('contac_tpl_speed_to_lead'); ?>',
                    icon: 'fa-gauge-high', desc: '<?= _l('contac_tpl_speed_to_lead_desc'); ?>',
                    objective: 'appointment',
                    points: [{a:30,u:'minutes'},{a:1,u:'hours'},{a:2,u:'hours'},{a:4,u:'hours'},{a:6,u:'hours'}]
                },
                {
                    name: '<?= _l('contac_tpl_standard_5point'); ?>',
                    icon: 'fa-list-ol', desc: '<?= _l('contac_tpl_standard_5point_desc'); ?>',
                    objective: 'lead_warmer',
                    points: [{a:1,u:'hours'},{a:4,u:'hours'},{a:8,u:'hours'},{a:16,u:'hours'},{a:24,u:'hours'}]
                },
                {
                    name: '<?= _l('contac_tpl_aggressive_sprint'); ?>',
                    icon: 'fa-fire', desc: '<?= _l('contac_tpl_aggressive_sprint_desc'); ?>',
                    objective: 'lead_warmer',
                    points: [{a:30,u:'minutes'},{a:1,u:'hours'},{a:3,u:'hours'},{a:6,u:'hours'},{a:12,u:'hours'},{a:1,u:'days'},{a:2,u:'days'}]
                },
                {
                    name: '<?= _l('contac_tpl_appointment_push'); ?>',
                    icon: 'fa-calendar-check', desc: '<?= _l('contac_tpl_appointment_push_desc'); ?>',
                    objective: 'appointment',
                    points: [{a:2,u:'hours'},{a:8,u:'hours'},{a:1,u:'days'},{a:3,u:'days'}]
                }
            ],
            medium: [
                {
                    name: '<?= _l('contac_tpl_weekly_nurture'); ?>',
                    icon: 'fa-seedling', desc: '<?= _l('contac_tpl_weekly_nurture_desc'); ?>',
                    objective: 'lead_warmer',
                    points: [{a:1,u:'days'},{a:3,u:'days'},{a:1,u:'weeks'},{a:2,u:'weeks'},{a:1,u:'months'}]
                },
                {
                    name: '<?= _l('contac_tpl_reactivation_drip'); ?>',
                    icon: 'fa-rotate', desc: '<?= _l('contac_tpl_reactivation_drip_desc'); ?>',
                    objective: 'reactivation',
                    points: [{a:1,u:'days'},{a:3,u:'days'},{a:1,u:'weeks'},{a:2,u:'weeks'}]
                },
                {
                    name: '<?= _l('contac_tpl_feedback_collector'); ?>',
                    icon: 'fa-star', desc: '<?= _l('contac_tpl_feedback_collector_desc'); ?>',
                    objective: 'feedback',
                    points: [{a:1,u:'days'},{a:3,u:'days'},{a:1,u:'weeks'}]
                },
                {
                    name: '<?= _l('contac_tpl_gentle_reminder'); ?>',
                    icon: 'fa-hand-holding-heart', desc: '<?= _l('contac_tpl_gentle_reminder_desc'); ?>',
                    objective: 'lead_warmer',
                    points: [{a:3,u:'days'},{a:1,u:'weeks'},{a:1,u:'months'}]
                },
                {
                    name: '<?= _l('contac_tpl_upsell_sequence'); ?>',
                    icon: 'fa-arrow-trend-up', desc: '<?= _l('contac_tpl_upsell_sequence_desc'); ?>',
                    objective: 'upsell',
                    points: [{a:3,u:'days'},{a:1,u:'weeks'},{a:2,u:'weeks'},{a:1,u:'months'}]
                }
            ],
            long: [
                {
                    name: '<?= _l('contac_tpl_quarterly_checkin'); ?>',
                    icon: 'fa-binoculars', desc: '<?= _l('contac_tpl_quarterly_checkin_desc'); ?>',
                    objective: 'lead_warmer',
                    points: [{a:1,u:'weeks'},{a:1,u:'months'},{a:3,u:'months'},{a:6,u:'months'}]
                },
                {
                    name: '<?= _l('contac_tpl_long_nurture'); ?>',
                    icon: 'fa-hourglass-half', desc: '<?= _l('contac_tpl_long_nurture_desc'); ?>',
                    objective: 'lead_warmer',
                    points: [{a:3,u:'days'},{a:1,u:'weeks'},{a:2,u:'weeks'},{a:1,u:'months'},{a:2,u:'months'},{a:3,u:'months'}]
                },
                {
                    name: '<?= _l('contac_tpl_seasonal_reactivation'); ?>',
                    icon: 'fa-calendar-days', desc: '<?= _l('contac_tpl_seasonal_reactivation_desc'); ?>',
                    objective: 'reactivation',
                    points: [{a:1,u:'months'},{a:3,u:'months'},{a:6,u:'months'},{a:1,u:'years'}]
                },
                {
                    name: '<?= _l('contac_tpl_post_sale'); ?>',
                    icon: 'fa-handshake', desc: '<?= _l('contac_tpl_post_sale_desc'); ?>',
                    objective: 'feedback',
                    points: [{a:1,u:'weeks'},{a:1,u:'months'},{a:2,u:'months'},{a:3,u:'months'},{a:6,u:'months'}]
                },
                {
                    name: '<?= _l('contac_tpl_vip_longterm'); ?>',
                    icon: 'fa-crown', desc: '<?= _l('contac_tpl_vip_longterm_desc'); ?>',
                    objective: 'upsell',
                    points: [{a:1,u:'weeks'},{a:1,u:'months'},{a:2,u:'months'},{a:3,u:'months'},{a:6,u:'months'},{a:1,u:'years'}]
                }
            ]
        };

        var _unitLabels = {minutes:'m', hours:'h', days:'d', weeks:'w', months:'mo', years:'y'};
        var _objLabels = {
            lead_warmer:'<?= _l('contac_auto_followup_obj_lead_warmer'); ?>',
            appointment:'<?= _l('contac_auto_followup_obj_appointment'); ?>',
            reactivation:'<?= _l('contac_auto_followup_obj_reactivation'); ?>',
            feedback:'<?= _l('contac_auto_followup_obj_feedback'); ?>',
            upsell:'<?= _l('contac_auto_followup_obj_upsell'); ?>'
        };

        function renderTemplateCards(category, containerId) {
            var html = '<div class="row">';
            _tplData[category].forEach(function(tpl, i) {
                var timeline = tpl.points.map(function(p){ return p.a + _unitLabels[p.u]; }).join('  <i class="fa fa-chevron-right" style="font-size:9px;color:#aaa;"></i>  ');
                html += '<div class="col-sm-6" style="margin-bottom:14px;">'
                    + '<div style="background:rgba(0,224,155,0.06);border:1px solid rgba(0,224,155,0.18);border-radius:10px;padding:14px 16px;height:100%;display:flex;flex-direction:column;">'
                    + '<div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">'
                    + '<div style="width:36px;height:36px;border-radius:8px;background:rgba(0,224,155,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fa-solid ' + tpl.icon + '" style="color:#00b87a;font-size:15px;"></i></div>'
                    + '<div><strong style="font-size:14px;color:#fff;">' + tpl.name + '</strong>'
                    + '<div style="font-size:11px;color:rgba(255,255,255,0.5);">' + tpl.points.length + ' touchpoints &middot; ' + _objLabels[tpl.objective] + '</div></div>'
                    + '</div>'
                    + '<div style="font-size:12px;color:#00e09b;font-weight:500;margin-bottom:8px;letter-spacing:0.3px;">' + timeline + '</div>'
                    + '<div style="font-size:12px;color:rgba(255,255,255,0.45);flex:1;">' + tpl.desc + '</div>'
                    + '<div style="margin-top:10px;text-align:right;"><button class="btn btn-sm" style="background:#00e09b;color:#0f1419;font-weight:600;border:none;border-radius:6px;padding:5px 16px;" onclick="applyTemplate(\'' + category + '\',' + i + ')"><i class="fa fa-check tw-mr-1"></i> <?= _l('contac_auto_followup_tpl_apply'); ?></button></div>'
                    + '</div></div>';
            });
            html += '</div>';
            $('#' + containerId).html(html);
        }

        $('#modalTemplates').on('shown.bs.modal', function() {
            renderTemplateCards('short', 'tpl_short');
            renderTemplateCards('medium', 'tpl_medium');
            renderTemplateCards('long', 'tpl_long');
        });

        window.applyTemplate = function(category, index) {
            var tpl = _tplData[category][index];
            var _msg = $('<textarea/>').html('<?= _l('contac_auto_followup_tpl_confirm'); ?>').text();
            if (!confirm(_msg.replace('{name}', tpl.name).replace('{count}', tpl.points.length))) return;

            var $prog = $('#tpl_progress').show();
            var total = tpl.points.length;
            var done = 0;

            function sendNext() {
                if (done >= total) {
                    location.reload();
                    return;
                }
                var p = tpl.points[done];
                var title = tpl.name + ' #' + (done + 1) + ' (' + p.a + _unitLabels[p.u] + ')';
                $('#tpl_progress_text').text('<?= _l('contac_auto_followup_tpl_creating'); ?> ' + (done + 1) + '/' + total + '...');

                $.post(admin_url + 'contactcenter/add_auto_followup', {
                    title: title,
                    objective: tpl.objective,
                    time_amount: p.a,
                    time_unit: p.u,
                    start_time: '08:00',
                    end_time: '18:00',
                    daily_limit: 50,
                    generation_window_hours: 2,
                    status: 0
                }, function() {
                    done++;
                    sendNext();
                }).fail(function(){ done++; sendNext(); });
            }
            sendNext();
        };
    });
})();
</script>
</body>
</html>
