<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
function _get_invoice_templates_js() {
    $tpls = [];

    // Intensive (hours-based, same-day)
    $tpls['cobranca_intensiva_2h'] = [
        ['title' => 'Cobrança intensiva - 2h após vencimento', 'objective' => 'payment_reminder', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 2, 'time_unit' => 'hours', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 80, 'generation_window_hours' => 1, 'status' => 0],
        ['title' => 'Cobrança intensiva - 4h após vencimento', 'objective' => 'payment_reminder', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 4, 'time_unit' => 'hours', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 80, 'generation_window_hours' => 1, 'status' => 0],
        ['title' => 'Cobrança intensiva - 8h após vencimento', 'objective' => 'overdue_collection', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 8, 'time_unit' => 'hours', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 80, 'generation_window_hours' => 1, 'status' => 0],
    ];

    $tpls['pressao_diaria'] = [
        ['title' => 'Pressão diária - 1h após vencimento', 'objective' => 'payment_reminder', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 1, 'time_unit' => 'hours', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 100, 'generation_window_hours' => 1, 'status' => 0],
        ['title' => 'Pressão diária - 3h após vencimento', 'objective' => 'payment_reminder', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 3, 'time_unit' => 'hours', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 100, 'generation_window_hours' => 1, 'status' => 0],
        ['title' => 'Pressão diária - 6h após vencimento', 'objective' => 'overdue_collection', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 6, 'time_unit' => 'hours', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 100, 'generation_window_hours' => 1, 'status' => 0],
        ['title' => 'Pressão diária - 12h após vencimento', 'objective' => 'overdue_collection', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 12, 'time_unit' => 'hours', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 100, 'generation_window_hours' => 1, 'status' => 0],
        ['title' => 'Pressão diária - 24h após vencimento', 'objective' => 'final_notice', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 24, 'time_unit' => 'hours', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 100, 'generation_window_hours' => 2, 'status' => 0],
    ];

    $tpls['blitz_vencimento'] = [
        ['title' => 'Blitz - no vencimento', 'objective' => 'payment_reminder', 'invoice_statuses' => ['1','3'], 'time_amount' => 0, 'time_unit' => 'hours', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 100, 'generation_window_hours' => 1, 'status' => 0],
        ['title' => 'Blitz - 2h após', 'objective' => 'payment_reminder', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 2, 'time_unit' => 'hours', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 100, 'generation_window_hours' => 1, 'status' => 0],
        ['title' => 'Blitz - 6h após', 'objective' => 'overdue_collection', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 6, 'time_unit' => 'hours', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 100, 'generation_window_hours' => 1, 'status' => 0],
        ['title' => 'Blitz - 12h após', 'objective' => 'overdue_collection', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 12, 'time_unit' => 'hours', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 100, 'generation_window_hours' => 1, 'status' => 0],
        ['title' => 'Blitz - 1 dia após', 'objective' => 'final_notice', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 1, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 100, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Blitz - 3 dias após', 'objective' => 'final_notice', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 3, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 100, 'generation_window_hours' => 2, 'status' => 0],
    ];

    $tpls['lembrete_amigavel'] = [
        ['title' => 'Lembrete pré-vencimento (1 dia antes)', 'objective' => 'friendly_reminder', 'invoice_statuses' => ['1'], 'time_amount' => 0, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Lembrete no vencimento', 'objective' => 'payment_reminder', 'invoice_statuses' => ['1','3'], 'time_amount' => 0, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Lembrete pós-vencimento (1 dia)', 'objective' => 'payment_reminder', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 1, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
    ];

    $tpls['cobranca_rapida'] = [
        ['title' => 'Cobrança - no vencimento', 'objective' => 'payment_reminder', 'invoice_statuses' => ['1','3'], 'time_amount' => 0, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Cobrança - 3 dias', 'objective' => 'overdue_collection', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 3, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Cobrança - 7 dias', 'objective' => 'overdue_collection', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 7, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
    ];

    $tpls['cobranca_progressiva'] = [
        ['title' => 'Progressiva - 1 dia', 'objective' => 'payment_reminder', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 1, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Progressiva - 3 dias', 'objective' => 'payment_reminder', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 3, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Progressiva - 7 dias', 'objective' => 'overdue_collection', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 7, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Progressiva - 15 dias', 'objective' => 'overdue_collection', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 15, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Progressiva - 30 dias', 'objective' => 'final_notice', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 30, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
    ];

    $tpls['recuperacao_suave'] = [
        ['title' => 'Recuperação suave - 3 dias', 'objective' => 'friendly_reminder', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 3, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Recuperação suave - 7 dias', 'objective' => 'payment_reminder', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 7, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Recuperação suave - 14 dias', 'objective' => 'overdue_collection', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 14, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Recuperação suave - 21 dias', 'objective' => 'overdue_collection', 'invoice_statuses' => ['1','3','4'], 'time_amount' => 21, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 50, 'generation_window_hours' => 2, 'status' => 0],
    ];

    $tpls['recuperacao_inadimplentes'] = [
        ['title' => 'Inadimplência - 30 dias', 'objective' => 'overdue_collection', 'invoice_statuses' => ['1','4'], 'time_amount' => 30, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 30, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Inadimplência - 60 dias', 'objective' => 'final_notice', 'invoice_statuses' => ['1','4'], 'time_amount' => 60, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 30, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Inadimplência - 90 dias', 'objective' => 'final_notice', 'invoice_statuses' => ['1','4'], 'time_amount' => 90, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 30, 'generation_window_hours' => 2, 'status' => 0],
    ];

    $tpls['reativacao'] = [
        ['title' => 'Reativação - 60 dias', 'objective' => 'payment_reminder', 'invoice_statuses' => ['1','4'], 'time_amount' => 60, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 20, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Reativação - 120 dias', 'objective' => 'overdue_collection', 'invoice_statuses' => ['1','4'], 'time_amount' => 120, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 20, 'generation_window_hours' => 2, 'status' => 0],
        ['title' => 'Reativação - 180 dias', 'objective' => 'final_notice', 'invoice_statuses' => ['1','4'], 'time_amount' => 180, 'time_unit' => 'days', 'start_time' => '08:00', 'end_time' => '18:00', 'daily_limit' => 20, 'generation_window_hours' => 2, 'status' => 0],
    ];

    return $tpls;
}
?>
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
.tpl-card{background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:16px;margin-bottom:12px;transition:.15s;}
.tpl-card:hover{border-color:#00e09b;background:rgba(0,224,155,0.06);}
.tpl-card h5{margin:0 0 4px;font-size:14px;font-weight:600;color:#fff;}
.tpl-card p{margin:0;font-size:12px;color:rgba(255,255,255,0.55);}
#modalGenerateNow .modal-content{background:#1a1f25;color:#fff;border:1px solid rgba(0,224,155,0.15);}
#modalGenerateNow .modal-header{border-bottom:1px solid rgba(0,224,155,0.12);}
#modalGenerateNow .modal-title{color:#fff;}
#modalGenerateNow .close{color:#fff;opacity:.6;text-shadow:none;}
#modalTestInv .modal-content{background:#1a1f25;color:#fff;border:1px solid rgba(0,224,155,0.15);}
#modalTestInv .modal-header{border-bottom:1px solid rgba(0,224,155,0.12);}
#modalTestInv .modal-title{color:#fff;}
#modalTestInv .close{color:#fff;opacity:.6;text-shadow:none;}
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <i class="fa fa-file-invoice-dollar tw-mr-2" style="color:#00e09b;font-size:28px;"></i>
                            <span><?= _l('contac_invoice_followup_title'); ?></span>
                        </h4>
                        <p class="text-muted"><?= _l('contac_invoice_followup_subtitle'); ?></p>
                        <hr class="hr-panel-separator" />

                        <div class="tw-mb-2 tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-gap-3">
                            <div>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#modalInvFollowup" onclick="reset_form()">
                                    <i class="fa-regular fa-plus tw-mr-1"></i> <?= _l('contac_invoice_followup_add'); ?>
                                </button>
                                <button class="btn btn-info" data-toggle="modal" data-target="#modalTemplates">
                                    <i class="fa fa-layer-group tw-mr-1"></i> <?= _l('contac_invoice_followup_templates'); ?>
                                </button>
                                <a href="<?= admin_url('contactcenter/invoice_followup_queue'); ?>" class="btn btn-default">
                                    <i class="fa fa-clock tw-mr-1"></i> <?= _l('contac_invoice_followup_queue_view'); ?>
                                </a>
                            </div>
                            <div class="tw-flex tw-items-center tw-gap-2">
                                <div class="checkbox tw-mb-0" style="margin:0;">
                                    <input type="checkbox" id="filter_show_inactive" <?= isset($show_inactive) && $show_inactive ? 'checked' : ''; ?>>
                                    <label for="filter_show_inactive" style="font-weight:500;margin-bottom:0;cursor:pointer;">
                                        <i class="fa fa-filter tw-mr-1"></i> <?= _l('contac_invoice_followup_show_inactive'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive" style="margin-top:15px;">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= _l('contac_invoice_followup_rule_title'); ?></th>
                                        <th><?= _l('contac_invoice_followup_objective'); ?></th>
                                        <th><?= _l('contac_invoice_followup_invoice_statuses'); ?></th>
                                        <th><?= _l('contac_invoice_followup_time_after_duedate'); ?></th>
                                        <th><?= _l('contac_invoice_followup_daily_limit'); ?></th>
                                        <th><?= _l('contac_invoice_followup_send_window'); ?></th>
                                        <th><?= _l('contac_phone_status'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($followups)): foreach ($followups as $fu):
                                        $inv_statuses = [];
                                        if (!empty($fu->invoice_statuses)) {
                                            $map = ['1' => _l('contac_invoice_followup_inv_unpaid'), '3' => _l('contac_invoice_followup_inv_partial'), '4' => _l('contac_invoice_followup_inv_overdue')];
                                            foreach (explode(',', $fu->invoice_statuses) as $s) { if (isset($map[trim($s)])) $inv_statuses[] = $map[trim($s)]; }
                                        }
                                    ?>
                                    <tr>
                                        <td><?= $fu->id; ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($fu->title); ?></strong>
                                            <div class="row-options">
                                                <a href="javascript:void(0);" onclick="edit_followup(<?= $fu->id; ?>)"><?= _l('contac_editar'); ?></a> |
                                                <a href="<?= admin_url('contactcenter/invoice_followup_queue/' . $fu->id); ?>"><?= _l('contac_invoice_followup_queue_view'); ?></a> |
                                                <a href="javascript:void(0);" onclick="test_generation(<?= $fu->id; ?>)" class="text-info"><?= _l('contac_invoice_followup_test'); ?></a> |
                                                <a href="javascript:void(0);" onclick="start_generation(<?= $fu->id; ?>)" class="text-success"><i class="fa fa-play"></i> <?= _l('contac_invoice_followup_generate'); ?></a> |
                                                <a href="javascript:void(0);" class="text-danger" onclick="delete_followup(<?= $fu->id; ?>)"><?= _l('contac_excluir'); ?></a>
                                            </div>
                                        </td>
                                        <td><span class="label label-default"><?= _l('contac_invoice_followup_obj_' . $fu->objective); ?></span></td>
                                        <td><small><?= implode(', ', $inv_statuses); ?></small></td>
                                        <td><?= $fu->time_amount . ' ' . _l('contac_time_unit_' . $fu->time_unit); ?></td>
                                        <td><?= $fu->daily_limit; ?></td>
                                        <td><?= substr($fu->start_time, 0, 5) . ' - ' . substr($fu->end_time, 0, 5); ?></td>
                                        <td>
                                            <div class="onoffswitch">
                                                <input type="checkbox" class="onoffswitch-checkbox" id="fu_status_<?= $fu->id; ?>"
                                                       data-id="<?= $fu->id; ?>" <?= $fu->status == 1 ? 'checked' : ''; ?>
                                                       onchange="toggle_status(<?= $fu->id; ?>, this.checked ? 1 : 0)">
                                                <label class="onoffswitch-label" for="fu_status_<?= $fu->id; ?>"></label>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?php endforeach; else: ?>
                                    <tr><td colspan="9" class="text-center text-muted"><?= _l('contac_invoice_followup_no_rules'); ?></td></tr>
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
<div class="modal fade" id="modalInvFollowup" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?= form_open(admin_url('contactcenter/add_invoice_followup'), ['id' => 'inv_followup_form']); ?>
            <input type="hidden" name="id" id="inv_fu_id" value="">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-file-invoice-dollar"></i> <?= _l('contac_invoice_followup_add'); ?></h4>
            </div>
            <div class="modal-body" style="padding:20px 25px;">

                <!-- Section: General -->
                <div style="background:rgba(0,224,155,0.06);border:1px solid rgba(0,224,155,0.18);border-radius:8px;padding:16px 18px;margin-bottom:18px;">
                    <h5 style="margin:0 0 14px;font-weight:600;font-size:14px;">
                        <i class="fa fa-bullseye" style="color:#00e09b;margin-right:6px;"></i> <?= _l('contac_invoice_followup_section_general'); ?>
                    </h5>
                    <div class="form-group" style="margin-bottom:12px;">
                        <label style="font-weight:500;"><?= _l('contac_invoice_followup_rule_title'); ?> <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="inv_fu_title" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom:12px;">
                                <label style="font-weight:500;"><?= _l('contac_invoice_followup_objective'); ?></label>
                                <select name="objective" id="inv_fu_objective" class="form-control" onchange="toggle_custom_obj()">
                                    <option value="payment_reminder"><?= _l('contac_invoice_followup_obj_payment_reminder'); ?></option>
                                    <option value="overdue_collection"><?= _l('contac_invoice_followup_obj_overdue_collection'); ?></option>
                                    <option value="partial_payment"><?= _l('contac_invoice_followup_obj_partial_payment'); ?></option>
                                    <option value="friendly_reminder"><?= _l('contac_invoice_followup_obj_friendly_reminder'); ?></option>
                                    <option value="final_notice"><?= _l('contac_invoice_followup_obj_final_notice'); ?></option>
                                    <option value="custom"><?= _l('contac_invoice_followup_obj_custom'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom:12px;">
                                <label style="font-weight:500;"><?= _l('contac_invoice_followup_invoice_statuses'); ?></label>
                                <select name="invoice_statuses[]" id="inv_fu_statuses" class="selectpicker" multiple data-width="100%">
                                    <option value="1" selected><?= _l('contac_invoice_followup_inv_unpaid'); ?></option>
                                    <option value="3" selected><?= _l('contac_invoice_followup_inv_partial'); ?></option>
                                    <option value="4" selected><?= _l('contac_invoice_followup_inv_overdue'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="inv_custom_obj_group" style="display:none;margin-bottom:12px;">
                        <label style="font-weight:500;"><?= _l('contac_invoice_followup_custom_objective'); ?></label>
                        <textarea name="custom_objective" id="inv_fu_custom_objective" class="form-control" rows="2"></textarea>
                    </div>
                </div>

                <!-- Section: Timing -->
                <div style="background:rgba(0,224,155,0.06);border:1px solid rgba(0,224,155,0.18);border-radius:8px;padding:16px 18px;margin-bottom:18px;">
                    <h5 style="margin:0 0 14px;font-weight:600;font-size:14px;">
                        <i class="fa fa-clock" style="color:#00e09b;margin-right:6px;"></i> <?= _l('contac_invoice_followup_section_timing'); ?>
                    </h5>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group" style="margin-bottom:12px;">
                                <label style="font-weight:500;"><?= _l('contac_invoice_followup_time_amount'); ?></label>
                                <input type="number" name="time_amount" id="inv_fu_time_amount" class="form-control" value="3" min="1">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group" style="margin-bottom:12px;">
                                <label style="font-weight:500;"><?= _l('contac_invoice_followup_time_unit'); ?></label>
                                <select name="time_unit" id="inv_fu_time_unit" class="form-control">
                                    <option value="minutes"><?= _l('contac_time_unit_minutes'); ?></option>
                                    <option value="hours"><?= _l('contac_time_unit_hours'); ?></option>
                                    <option value="days" selected><?= _l('contac_time_unit_days'); ?></option>
                                    <option value="weeks"><?= _l('contac_time_unit_weeks'); ?></option>
                                    <option value="months"><?= _l('contac_time_unit_months'); ?></option>
                                    <option value="years"><?= _l('contac_time_unit_years'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group" style="margin-bottom:12px;">
                                <label style="font-weight:500;"><?= _l('contac_invoice_followup_gen_window'); ?></label>
                                <input type="number" name="generation_window_hours" id="inv_fu_gen_window" class="form-control" value="2" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom:0;">
                                <label style="font-weight:500;"><?= _l('contac_invoice_followup_send_window'); ?></label>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <input type="time" name="start_time" id="inv_fu_start" class="form-control" value="08:00">
                                    </div>
                                    <div class="col-xs-6">
                                        <input type="time" name="end_time" id="inv_fu_end" class="form-control" value="18:00">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom:0;">
                                <label style="font-weight:500;"><?= _l('contac_invoice_followup_daily_limit'); ?></label>
                                <input type="number" name="daily_limit" id="inv_fu_daily_limit" class="form-control" value="50" min="1">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Settings -->
                <div style="background:rgba(0,224,155,0.06);border:1px solid rgba(0,224,155,0.18);border-radius:8px;padding:16px 18px;margin-bottom:0;">
                    <h5 style="margin:0 0 14px;font-weight:600;font-size:14px;">
                        <i class="fa fa-cog" style="color:#00e09b;margin-right:6px;"></i> <?= _l('contac_invoice_followup_section_settings'); ?>
                    </h5>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom:12px;">
                                <label style="font-weight:500;"><?= _l('contac_invoice_followup_device'); ?></label>
                                <select name="device_id" id="inv_fu_device" class="form-control">
                                    <option value="">—</option>
                                    <?php if (!empty($devices)): foreach ($devices as $dev): ?>
                                    <option value="<?= $dev->dev_id; ?>"><?= htmlspecialchars($dev->dev_nome); ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom:12px;">
                                <label style="font-weight:500;"><?= _l('contac_invoice_followup_staff'); ?></label>
                                <select name="staffid" id="inv_fu_staff" class="form-control">
                                    <option value="">—</option>
                                    <?php if (!empty($staff)): foreach ($staff as $s): ?>
                                    <option value="<?= $s['staffid']; ?>"><?= htmlspecialchars($s['firstname'] . ' ' . $s['lastname']); ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
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

<!-- Templates Modal -->
<div class="modal fade" id="modalTemplates" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-layer-group"></i> <?= _l('contac_invoice_followup_templates_title'); ?></h4>
            </div>
            <div class="modal-body" style="padding:0;">
                <ul class="nav nav-tabs" style="padding:0 15px;">
                    <li class="active"><a data-toggle="tab" href="#tpl_intensive"><?= _l('contac_invoice_followup_tpl_intensive'); ?></a></li>
                    <li><a data-toggle="tab" href="#tpl_short"><?= _l('contac_invoice_followup_tpl_short'); ?></a></li>
                    <li><a data-toggle="tab" href="#tpl_medium"><?= _l('contac_invoice_followup_tpl_medium'); ?></a></li>
                    <li><a data-toggle="tab" href="#tpl_long"><?= _l('contac_invoice_followup_tpl_long'); ?></a></li>
                </ul>
                <div class="tab-content" style="padding:18px;">
                    <div id="tpl_intensive" class="tab-pane active">
                        <div class="tpl-card" data-tpl="cobranca_intensiva_2h">
                            <h5><i class="fa fa-fire" style="color:#ff5722;margin-right:6px;"></i> <?= _l('contac_invoice_followup_tpl_intensiva_2h'); ?></h5>
                            <p><?= _l('contac_invoice_followup_tpl_intensiva_2h_desc'); ?></p>
                            <button type="button" class="btn btn-sm btn-primary tw-mt-2" onclick="apply_template('cobranca_intensiva_2h')"><?= _l('contac_invoice_followup_tpl_apply'); ?></button>
                        </div>
                        <div class="tpl-card" data-tpl="pressao_diaria">
                            <h5><i class="fa fa-gauge-high" style="color:#ff9800;margin-right:6px;"></i> <?= _l('contac_invoice_followup_tpl_pressao_diaria'); ?></h5>
                            <p><?= _l('contac_invoice_followup_tpl_pressao_diaria_desc'); ?></p>
                            <button type="button" class="btn btn-sm btn-primary tw-mt-2" onclick="apply_template('pressao_diaria')"><?= _l('contac_invoice_followup_tpl_apply'); ?></button>
                        </div>
                        <div class="tpl-card" data-tpl="blitz_vencimento">
                            <h5><i class="fa fa-bolt-lightning" style="color:#ffc107;margin-right:6px;"></i> <?= _l('contac_invoice_followup_tpl_blitz'); ?></h5>
                            <p><?= _l('contac_invoice_followup_tpl_blitz_desc'); ?></p>
                            <button type="button" class="btn btn-sm btn-primary tw-mt-2" onclick="apply_template('blitz_vencimento')"><?= _l('contac_invoice_followup_tpl_apply'); ?></button>
                        </div>
                    </div>
                    <div id="tpl_short" class="tab-pane">
                        <div class="tpl-card" data-tpl="lembrete_amigavel">
                            <h5><i class="fa fa-heart" style="color:#e91e63;margin-right:6px;"></i> <?= _l('contac_invoice_followup_tpl_lembrete_amigavel'); ?></h5>
                            <p><?= _l('contac_invoice_followup_tpl_lembrete_amigavel_desc'); ?></p>
                            <button type="button" class="btn btn-sm btn-primary tw-mt-2" onclick="apply_template('lembrete_amigavel')"><?= _l('contac_invoice_followup_tpl_apply'); ?></button>
                        </div>
                        <div class="tpl-card" data-tpl="cobranca_rapida">
                            <h5><i class="fa fa-bolt" style="color:#ff9800;margin-right:6px;"></i> <?= _l('contac_invoice_followup_tpl_cobranca_rapida'); ?></h5>
                            <p><?= _l('contac_invoice_followup_tpl_cobranca_rapida_desc'); ?></p>
                            <button type="button" class="btn btn-sm btn-primary tw-mt-2" onclick="apply_template('cobranca_rapida')"><?= _l('contac_invoice_followup_tpl_apply'); ?></button>
                        </div>
                    </div>
                    <div id="tpl_medium" class="tab-pane">
                        <div class="tpl-card" data-tpl="cobranca_progressiva">
                            <h5><i class="fa fa-chart-line" style="color:#03a9f4;margin-right:6px;"></i> <?= _l('contac_invoice_followup_tpl_cobranca_progressiva'); ?></h5>
                            <p><?= _l('contac_invoice_followup_tpl_cobranca_progressiva_desc'); ?></p>
                            <button type="button" class="btn btn-sm btn-primary tw-mt-2" onclick="apply_template('cobranca_progressiva')"><?= _l('contac_invoice_followup_tpl_apply'); ?></button>
                        </div>
                        <div class="tpl-card" data-tpl="recuperacao_suave">
                            <h5><i class="fa fa-hand-holding-heart" style="color:#8bc34a;margin-right:6px;"></i> <?= _l('contac_invoice_followup_tpl_recuperacao_suave'); ?></h5>
                            <p><?= _l('contac_invoice_followup_tpl_recuperacao_suave_desc'); ?></p>
                            <button type="button" class="btn btn-sm btn-primary tw-mt-2" onclick="apply_template('recuperacao_suave')"><?= _l('contac_invoice_followup_tpl_apply'); ?></button>
                        </div>
                    </div>
                    <div id="tpl_long" class="tab-pane">
                        <div class="tpl-card" data-tpl="recuperacao_inadimplentes">
                            <h5><i class="fa fa-exclamation-triangle" style="color:#f44336;margin-right:6px;"></i> <?= _l('contac_invoice_followup_tpl_recuperacao_inadimplentes'); ?></h5>
                            <p><?= _l('contac_invoice_followup_tpl_recuperacao_inadimplentes_desc'); ?></p>
                            <button type="button" class="btn btn-sm btn-primary tw-mt-2" onclick="apply_template('recuperacao_inadimplentes')"><?= _l('contac_invoice_followup_tpl_apply'); ?></button>
                        </div>
                        <div class="tpl-card" data-tpl="reativacao">
                            <h5><i class="fa fa-redo" style="color:#9c27b0;margin-right:6px;"></i> <?= _l('contac_invoice_followup_tpl_reativacao'); ?></h5>
                            <p><?= _l('contac_invoice_followup_tpl_reativacao_desc'); ?></p>
                            <button type="button" class="btn btn-sm btn-primary tw-mt-2" onclick="apply_template('reativacao')"><?= _l('contac_invoice_followup_tpl_apply'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Generate Now Modal -->
<div class="modal fade" id="modalGenerateNow" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-cogs"></i> <?= _l('contac_invoice_followup_gen_title'); ?></h4>
            </div>
            <div class="modal-body" style="padding:20px;">
                <div id="gen_status" class="text-center" style="padding:20px;">
                    <i class="fa fa-spinner fa-spin" style="font-size:28px;color:#00e09b;"></i>
                    <p class="tw-mt-3" id="gen_status_text"><?= _l('contac_invoice_followup_gen_counting'); ?></p>
                </div>
                <div id="gen_progress_bar" style="display:none;">
                    <div class="progress" style="height:24px;border-radius:6px;margin-top:12px;">
                        <div id="gen_bar" class="progress-bar progress-bar-success" style="width:0%;line-height:24px;font-weight:600;transition:.3s;">0%</div>
                    </div>
                    <p class="tw-mt-2 text-center" id="gen_progress_text"></p>
                </div>
                <div id="gen_debug_panel" style="display:none;margin-top:15px;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:14px;font-size:12px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="button" class="btn btn-success" id="btnStartGen" style="display:none;" onclick="runGenBatch()">
                    <i class="fa fa-play"></i> <?= _l('contac_invoice_followup_gen_start'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Test Modal -->
<div class="modal fade" id="modalTestInv" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-flask"></i> <?= _l('contac_invoice_followup_test_title'); ?></h4>
            </div>
            <div class="modal-body" style="padding:20px;">
                <div id="test_loading" class="text-center" style="padding:30px;">
                    <i class="fa fa-spinner fa-spin" style="font-size:28px;color:#00e09b;"></i>
                    <p class="tw-mt-3"><?= _l('contac_invoice_followup_gen_counting'); ?></p>
                </div>
                <div id="test_results" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
var _invTplData = <?= json_encode(_get_invoice_templates_js()); ?>;
var _invGenState = {};

function _invHtmlDec(s){
    if(!s) return '';
    var t = document.createElement('textarea'); t.innerHTML = s; return t.value;
}

function reset_form(){
    var $ = jQuery;
    $('#inv_fu_id').val('');
    $('#inv_followup_form')[0].reset();
    $('#inv_fu_statuses').selectpicker('val', ['1','3','4']);
    toggle_custom_obj();
    $('.modal-title', '#modalInvFollowup').html('<i class="fa fa-file-invoice-dollar"></i> <?= _l('contac_invoice_followup_add'); ?>');
}

function toggle_custom_obj(){
    var $ = jQuery;
    var v = $('#inv_fu_objective').val();
    $('#inv_custom_obj_group').toggle(v === 'custom');
}

function edit_followup(id){
    var $ = jQuery;
    $.post(admin_url + 'contactcenter/get_invoice_followup_data', {id: id}, function(res){
        if(!res.success) return;
        var d = res.data;
        $('#inv_fu_id').val(d.id);
        $('#inv_fu_title').val(d.title);
        $('#inv_fu_objective').val(d.objective);
        $('#inv_fu_custom_objective').val(d.custom_objective || '');
        if(d.invoice_statuses){
            $('#inv_fu_statuses').selectpicker('val', d.invoice_statuses.split(','));
        }
        $('#inv_fu_time_amount').val(d.time_amount);
        $('#inv_fu_time_unit').val(d.time_unit);
        $('#inv_fu_gen_window').val(d.generation_window_hours);
        $('#inv_fu_start').val((d.start_time||'08:00:00').substring(0,5));
        $('#inv_fu_end').val((d.end_time||'18:00:00').substring(0,5));
        $('#inv_fu_daily_limit').val(d.daily_limit);
        $('#inv_fu_device').val(d.device_id || '');
        $('#inv_fu_staff').val(d.staffid || '');
        toggle_custom_obj();
        $('.modal-title', '#modalInvFollowup').html('<i class="fa fa-file-invoice-dollar"></i> <?= _l('contac_invoice_followup_edit'); ?>');
        $('#modalInvFollowup').modal('show');
    }, 'json');
}

function toggle_status(id, status){
    jQuery.post(admin_url + 'contactcenter/toggle_invoice_followup_status', {id: id, status: status});
}

function delete_followup(id){
    if(!confirm('<?= _l('contac_invoice_followup_confirm_delete'); ?>')) return;
    var $ = jQuery;
    var f = $('<form method="post" action="'+admin_url+'contactcenter/delete_invoice_followup"><input name="id" value="'+id+'"><input name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>"></form>');
    $('body').append(f); f.submit();
}

function apply_template(tpl){
    var $ = jQuery;
    var rules = _invTplData[tpl];
    if(!rules || !rules.length){ alert('Template not found'); return; }

    var label = _invHtmlDec('<?= _l('contac_invoice_followup_tpl_apply'); ?>');
    if(!confirm(label + '? ' + rules.length + ' rules will be created.')) return;

    var created = 0, total = rules.length;
    rules.forEach(function(r){
        $.ajax({
            url: admin_url + 'contactcenter/add_invoice_followup',
            method: 'POST',
            data: r,
            dataType: 'json',
            success: function(){ created++; if(created >= total){ $('#modalTemplates').modal('hide'); location.reload(); } }
        });
    });
}

function start_generation(id){
    var $ = jQuery;
    _invGenState = {id: id, total: 0, processed: 0, queued: 0, skipped: 0};
    $('#gen_status').show();
    $('#gen_progress_bar, #btnStartGen').hide();
    $('#gen_debug_panel').hide().html('');
    $('#gen_status_text').text('<?= _l('contac_invoice_followup_gen_counting'); ?>');
    $('#modalGenerateNow').modal('show');

    $.post(admin_url + 'contactcenter/count_invoice_followup_invoices', {id: id}, function(res){
        if(!res.success){ _invShowGenError(res.error || 'Error'); return; }
        _invGenState.total = res.total;
        var msg = '<?= _l('contac_invoice_followup_gen_found'); ?>';
        $('#gen_status_text').html('<strong>'+msg.replace('%d', res.total)+'</strong>');
        if(res.debug) _invRenderDebug(res.debug);
        if(res.total > 0){
            $('#gen_progress_bar').show();
            $('#btnStartGen').show();
        } else {
            $('#gen_status').find('i').removeClass('fa-spinner fa-spin').addClass('fa-info-circle').css('color','#ff9800');
        }
    }, 'json');
}

function runGenBatch(){
    var $ = jQuery;
    $('#btnStartGen').hide();
    $('#gen_status').find('i').addClass('fa-spinner fa-spin').css('color','#00e09b');
    $.post(admin_url + 'contactcenter/generate_invoice_followup_batch', {id: _invGenState.id, batch_size: 20}, function(res){
        if(!res.success){ _invShowGenError(res.error || 'Error'); return; }
        _invGenState.processed += res.processed;
        _invGenState.queued    += res.queued;
        _invGenState.skipped   += res.skipped;
        var pct = _invGenState.total > 0 ? Math.min(100, Math.round((_invGenState.processed / _invGenState.total) * 100)) : 100;
        $('#gen_bar').css('width', pct+'%').text(pct+'%');
        var prog = '<?= _l('contac_invoice_followup_gen_progress'); ?>';
        prog = prog.replace('%d', _invGenState.processed).replace('%d', _invGenState.total).replace('%d', _invGenState.queued).replace('%d', _invGenState.skipped);
        $('#gen_progress_text').text(prog);
        if(res.remaining > 0){
            setTimeout(runGenBatch, 500);
        } else {
            var done = '<?= _l('contac_invoice_followup_gen_done'); ?>';
            done = done.replace('%d', _invGenState.queued).replace('%d', _invGenState.skipped);
            $('#gen_status').find('i').removeClass('fa-spinner fa-spin').addClass('fa-check-circle').css('color','#00e09b');
            $('#gen_status_text').html('<strong>'+done+'</strong>');
            $('#gen_bar').css('width','100%').text('100%');
        }
    }, 'json');
}

function _invShowGenError(msg){
    var $ = jQuery;
    $('#gen_status').find('i').removeClass('fa-spinner fa-spin').addClass('fa-exclamation-triangle').css('color','#f44336');
    $('#gen_status_text').html('<span class="text-danger">'+msg+'</span>');
}

function _invRenderDebug(d){
    var $ = jQuery;
    var h = '<strong><?= _l('contac_invoice_followup_debug_title'); ?></strong><br>';
    h += '<?= _l('contac_invoice_followup_debug_interval'); ?>: <b>'+ d.hours_equivalent +'h</b><br>';
    h += '<?= _l('contac_invoice_followup_debug_cutoff'); ?>: <b>'+ (d.cutoff_date||'-') +'</b><br>';
    h += '<?= _l('contac_invoice_followup_debug_total_invoices'); ?>: <b>'+ d.total_invoices +'</b><br>';
    h += '<?= _l('contac_invoice_followup_debug_match_time'); ?>: <b>'+ d.match_time_filter +'</b><br>';
    h += '<?= _l('contac_invoice_followup_debug_has_cc'); ?>: <b style="color:#4caf50">'+ d.has_cc_messages +'</b><br>';
    h += '<?= _l('contac_invoice_followup_debug_no_cc'); ?>: <b style="color:#ff9800">'+ d.no_cc_messages +'</b><br>';
    h += '<?= _l('contac_invoice_followup_debug_in_queue'); ?>: <b>'+ d.already_in_queue +'</b><br>';
    h += '<?= _l('contac_invoice_followup_debug_final'); ?>: <b style="color:#00e09b">'+ d.final_eligible +'</b>';
    $('#gen_debug_panel').html(h).show();
}

function test_generation(id){
    var $ = jQuery;
    $('#test_loading').show();
    $('#test_results').hide().html('');
    $('#modalTestInv').modal('show');
    $.post(admin_url + 'contactcenter/test_invoice_followup_generation', {id: id}, function(res){
        $('#test_loading').hide();
        if(!res.success){ $('#test_results').html('<div class="alert alert-warning">'+(res.error||'Error')+'</div>').show(); return; }
        var html = '<h5><strong>'+_invHtmlDec(res.rule_title)+'</strong> — '+_invHtmlDec(res.objective)+'</h5>';
        res.samples.forEach(function(s){
            html += '<div style="background:rgba(0,224,155,0.06);border:1px solid rgba(0,224,155,0.15);border-radius:8px;padding:14px;margin-bottom:12px;">';
            html += '<div class="row"><div class="col-sm-6">';
            html += '<strong><?= _l('contac_invoice_followup_test_client'); ?>:</strong> '+_invHtmlDec(s.client_name)+'<br>';
            html += '<strong><?= _l('contac_invoice_followup_test_invoice'); ?>:</strong> #'+_invHtmlDec(s.invoice_number)+'<br>';
            html += '<strong><?= _l('contac_invoice_followup_test_amount'); ?>:</strong> R$ '+parseFloat(s.invoice_total).toFixed(2)+'<br>';
            html += '<strong><?= _l('contac_invoice_followup_test_duedate'); ?>:</strong> '+s.duedate+'<br>';
            html += '<strong><?= _l('contac_invoice_followup_test_days_overdue'); ?>:</strong> '+s.days_overdue;
            html += '</div><div class="col-sm-6">';
            html += '<strong><?= _l('contac_invoice_followup_test_chat'); ?>:</strong><pre style="font-size:11px;max-height:120px;overflow:auto;background:rgba(0,0,0,0.15);color:#ccc;padding:8px;border-radius:4px;">'+_invHtmlDec(s.chat_preview)+'</pre>';
            html += '</div></div>';
            html += '<div style="margin-top:8px;"><strong><?= _l('contac_invoice_followup_test_generated'); ?>:</strong>';
            html += '<div style="background:rgba(0,0,0,0.15);padding:10px;border-radius:6px;margin-top:4px;white-space:pre-wrap;font-size:13px;">'+_invHtmlDec(s.generated_msg)+'</div>';
            html += '</div></div>';
        });
        $('#test_results').html(html).show();
    }, 'json');
}

jQuery(function($){
    $('#filter_show_inactive').on('change', function(){
        $.post(admin_url + 'contactcenter/invoice_followup_filter_inactive', {show: this.checked ? 1 : 0}, function(){ location.reload(); });
    });
});
</script>
</body>
</html>
