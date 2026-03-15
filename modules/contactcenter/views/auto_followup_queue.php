<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <i class="fa fa-clock tw-mr-2" style="color:#00e09b;font-size:24px;"></i>
                            <span><?= _l('contac_auto_followup_queue_title'); ?></span>
                            <?php if (!empty($followup_id)):
                                $current_fu = null;
                                foreach ($followups as $f) { if ($f->id == $followup_id) { $current_fu = $f; break; } }
                                if ($current_fu): ?>
                                <span class="label label-primary" style="margin-left:10px;"><?= htmlspecialchars($current_fu->title); ?></span>
                            <?php endif; endif; ?>
                        </h4>
                        <hr class="hr-panel-separator" />

                        <!-- Stats -->
                        <div class="row tw-mb-4">
                            <div class="col-md-3 col-sm-4">
                                <div class="panel panel-default tw-mb-0" style="border-left:3px solid #f0ad4e;">
                                    <div class="panel-body tw-p-3 tw-text-center">
                                        <h3 class="tw-mb-0 tw-font-bold" style="color:#f0ad4e;"><?= $stats['pending']; ?></h3>
                                        <small class="text-muted"><?= _l('contac_auto_followup_stat_pending'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <div class="panel panel-default tw-mb-0" style="border-left:3px solid #5cb85c;">
                                    <div class="panel-body tw-p-3 tw-text-center">
                                        <h3 class="tw-mb-0 tw-font-bold" style="color:#5cb85c;"><?= $stats['sent_today']; ?></h3>
                                        <small class="text-muted"><?= _l('contac_auto_followup_stat_sent_today'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <div class="panel panel-default tw-mb-0" style="border-left:3px solid #d9534f;">
                                    <div class="panel-body tw-p-3 tw-text-center">
                                        <h3 class="tw-mb-0 tw-font-bold" style="color:#d9534f;"><?= $stats['failed_today']; ?></h3>
                                        <small class="text-muted"><?= _l('contac_auto_followup_stat_failed_today'); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-12">
                                <a href="<?= admin_url('contactcenter/auto_followup'); ?>" class="btn btn-default btn-block" style="margin-top:10px;">
                                    <i class="fa fa-arrow-left"></i> <?= _l('contac_auto_followup_back_config'); ?>
                                </a>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="row tw-mb-3">
                            <form method="GET" action="<?= admin_url('contactcenter/auto_followup_queue' . ($followup_id ? '/' . $followup_id : '')); ?>">
                                <div class="col-md-3">
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value=""><?= _l('contac_auto_followup_filter_all_status'); ?></option>
                                        <option value="pending" <?= ($this->input->get('status') == 'pending') ? 'selected' : ''; ?>><?= _l('contac_auto_followup_status_pending'); ?></option>
                                        <option value="sent" <?= ($this->input->get('status') == 'sent') ? 'selected' : ''; ?>><?= _l('contac_auto_followup_status_sent'); ?></option>
                                        <option value="failed" <?= ($this->input->get('status') == 'failed') ? 'selected' : ''; ?>><?= _l('contac_auto_followup_status_failed'); ?></option>
                                        <option value="cancelled" <?= ($this->input->get('status') == 'cancelled') ? 'selected' : ''; ?>><?= _l('contac_auto_followup_status_cancelled'); ?></option>
                                        <option value="skipped" <?= ($this->input->get('status') == 'skipped') ? 'selected' : ''; ?>><?= _l('contac_auto_followup_status_skipped'); ?></option>
                                    </select>
                                </div>
                                <?php if (empty($followup_id)): ?>
                                <div class="col-md-3">
                                    <select name="followup_id" class="form-control" onchange="if(this.value){ window.location=admin_url+'contactcenter/auto_followup_queue/'+this.value+(window.location.search||''); } else { window.location=admin_url+'contactcenter/auto_followup_queue'+(window.location.search||''); }">
                                        <option value=""><?= _l('contac_auto_followup_filter_all_rules'); ?></option>
                                        <?php foreach ($followups as $f): ?>
                                        <option value="<?= $f->id; ?>"><?= htmlspecialchars($f->title); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endif; ?>
                            </form>
                        </div>

                        <!-- Queue Table -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= _l('contac_auto_followup_q_lead'); ?></th>
                                        <th><?= _l('contac_auto_followup_q_phone'); ?></th>
                                        <th><?= _l('contac_auto_followup_q_rule'); ?></th>
                                        <th><?= _l('contac_auto_followup_q_message'); ?></th>
                                        <th><?= _l('contac_auto_followup_q_scheduled'); ?></th>
                                        <th><?= _l('contac_phone_status'); ?></th>
                                        <th><?= _l('contac_auto_followup_q_actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($queue_items)): foreach ($queue_items as $qi): ?>
                                    <tr id="qrow_<?= $qi->id; ?>">
                                        <td><?= $qi->id; ?></td>
                                        <td>
                                            <a href="<?= admin_url('leads/index/' . $qi->lead_id); ?>" target="_blank">
                                                <?= htmlspecialchars($qi->lead_name); ?>
                                            </a>
                                        </td>
                                        <td><small><?= htmlspecialchars($qi->phone); ?></small></td>
                                        <td><small><?= htmlspecialchars($qi->followup_title ?? ''); ?></small></td>
                                        <td style="max-width:300px;">
                                            <span class="msg-preview" title="<?= htmlspecialchars($qi->message_text); ?>"><?= htmlspecialchars(mb_substr($qi->message_text, 0, 80)); ?><?= mb_strlen($qi->message_text) > 80 ? '...' : ''; ?></span>
                                            <?php if ($qi->status === 'pending'): ?>
                                            <br><a href="javascript:void(0);" onclick="open_edit_msg(<?= $qi->id; ?>)" class="text-muted" style="font-size:11px;"><i class="fa fa-edit"></i> <?= _l('contac_editar'); ?></a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= _dt($qi->scheduled_at); ?></small>
                                            <?php if ($qi->sent_at): ?>
                                            <br><small class="text-success"><?= _l('contac_auto_followup_sent_at'); ?>: <?= _dt($qi->sent_at); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = [
                                                'pending'   => 'warning',
                                                'sending'   => 'info',
                                                'sent'      => 'success',
                                                'skipped'   => 'default',
                                                'failed'    => 'danger',
                                                'cancelled' => 'default',
                                            ];
                                            $bc = isset($badge_class[$qi->status]) ? $badge_class[$qi->status] : 'default';
                                            ?>
                                            <span class="label label-<?= $bc; ?>"><?= _l('contac_auto_followup_status_' . $qi->status); ?></span>
                                            <?php if ($qi->status === 'failed' && !empty($qi->error_message)): ?>
                                            <br><small class="text-danger" title="<?= htmlspecialchars($qi->error_message); ?>"><i class="fa fa-exclamation-triangle"></i> <?= htmlspecialchars(mb_substr($qi->error_message, 0, 50)); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($qi->status === 'pending'): ?>
                                            <button class="btn btn-success btn-xs" onclick="send_now(<?= $qi->id; ?>)" title="<?= _l('contac_auto_followup_btn_send_now'); ?>">
                                                <i class="fa fa-paper-plane"></i>
                                            </button>
                                            <button class="btn btn-danger btn-xs" onclick="cancel_item(<?= $qi->id; ?>)" title="<?= _l('contac_auto_followup_btn_cancel'); ?>">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; else: ?>
                                    <tr><td colspan="8" class="text-center text-muted"><?= _l('contac_auto_followup_queue_empty'); ?></td></tr>
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

<!-- Edit Message Modal -->
<div class="modal fade" id="modalEditMsg" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-edit"></i> <?= _l('contac_auto_followup_edit_message'); ?></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_msg_id">
                <div class="form-group">
                    <label><?= _l('contac_auto_followup_q_message'); ?></label>
                    <textarea id="edit_msg_text" class="form-control" rows="6"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="button" class="btn btn-primary" onclick="save_edit_msg()"><?= _l('submit'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
(function _initQueue() {
    if (typeof jQuery === 'undefined') { return setTimeout(_initQueue, 100); }
    jQuery(function($) {

        window.send_now = function(id) {
            if (!confirm('<?= _l('contac_auto_followup_confirm_send_now'); ?>')) return;
            var btn = $('#qrow_' + id + ' .btn-success');
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
            $.post(admin_url + 'contactcenter/send_followup_now', {id: id}, function(r) {
                if (r.success) {
                    alert_float('success', r.message);
                    setTimeout(function(){ location.reload(); }, 1000);
                } else {
                    alert_float('danger', r.message || 'Error');
                    btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i>');
                }
            }, 'json').fail(function() {
                btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i>');
            });
        };

        window.cancel_item = function(id) {
            if (!confirm('<?= _l('contac_auto_followup_confirm_cancel'); ?>')) return;
            $.post(admin_url + 'contactcenter/cancel_followup_item', {id: id}, function(r) {
                if (r.success) location.reload();
            }, 'json');
        };

        window.open_edit_msg = function(id) {
            var text = $('#qrow_' + id + ' .msg-preview').attr('title');
            $('#edit_msg_id').val(id);
            $('#edit_msg_text').val(text);
            $('#modalEditMsg').modal('show');
        };

        window.save_edit_msg = function() {
            var id = $('#edit_msg_id').val();
            var msg = $('#edit_msg_text').val();
            $.post(admin_url + 'contactcenter/update_followup_message', {id: id, message_text: msg}, function(r) {
                if (r.success) {
                    $('#modalEditMsg').modal('hide');
                    location.reload();
                }
            }, 'json');
        };
    });
})();
</script>
</body>
</html>
