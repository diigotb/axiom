<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <span>
                                <i class="fa fa-object-group"></i>
                                <?= !empty($ad_set) ? _l('ads_analytics_edit_ad_set') : _l('ads_analytics_add_ad_set'); ?>
                            </span>
                            <div class="pull-right">
                                <a href="<?= admin_url('contactcenter/ads_analytics_sets'); ?>" class="btn btn-success" style="margin-right: 10px;">
                                    <i class="fa fa-chart-line"></i> <?php echo _l('contactcenter_ads_analytics'); ?>
                                </a>
                                <a href="<?= admin_url('contactcenter/ads_analytics'); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> <?php echo _l('ads_analytics_back_to_ads_analytics'); ?>
                                </a>
                            </div>
                        </h4>
                        <hr class="hr-panel-separator" />
                        
                        <?php echo form_open(admin_url('contactcenter/manage_ad_set' . (!empty($ad_set) ? '/' . $ad_set->id : '')), ['id' => 'manage-ad-set-form']); ?>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name"><?php echo _l('ads_analytics_ad_set_name'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control" value="<?= !empty($ad_set) ? htmlspecialchars($ad_set->name) : ''; ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description"><?php echo _l('ads_analytics_ad_set_description'); ?></label>
                                    <textarea id="description" name="description" class="form-control" rows="3"><?= !empty($ad_set) ? htmlspecialchars($ad_set->description) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active"><?php echo _l('ads_analytics_status'); ?></label>
                                    <select id="is_active" name="is_active" class="form-control">
                                        <option value="1" <?= (empty($ad_set) || $ad_set->is_active == 1) ? 'selected' : ''; ?>><?php echo _l('ads_analytics_active'); ?></option>
                                        <option value="0" <?= (!empty($ad_set) && $ad_set->is_active == 0) ? 'selected' : ''; ?>><?php echo _l('ads_analytics_inactive'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5><?php echo _l('ads_analytics_investment_settings'); ?></h5>
                        <p class="text-muted"><?php echo _l('ads_analytics_ad_set_budget_help'); ?></p>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="daily_investment"><?php echo _l('ads_analytics_daily_investment'); ?></label>
                                    <input type="number" id="daily_investment" name="daily_investment" class="form-control" step="0.01" min="0" value="<?= !empty($investment) ? $investment->daily_investment : ''; ?>" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date"><?php echo _l('ads_analytics_start_date'); ?></label>
                                    <div class="input-group">
                                        <input type="text" id="start_date" name="start_date" class="form-control datepicker" value="<?= !empty($investment) && !empty($investment->start_date) ? _d($investment->start_date) : ''; ?>" placeholder="<?php echo _l('ads_analytics_start_date'); ?>">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date"><?php echo _l('ads_analytics_end_date'); ?></label>
                                    <div class="input-group">
                                        <input type="text" id="end_date" name="end_date" class="form-control datepicker" value="<?= !empty($investment) && !empty($investment->end_date) ? _d($investment->end_date) : ''; ?>" placeholder="<?php echo _l('ads_analytics_end_date'); ?>">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted"><?php echo _l('ads_analytics_end_date_help'); ?></small>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($ad_set) && !empty($creatives)) { ?>
                            <hr>
                            <h5>Creatives in this Ad Set</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Campaign</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($creatives as $creative) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($creative->name); ?></td>
                                                <td><?= htmlspecialchars($creative->campaign_name); ?></td>
                                                <td><?= $creative->is_active ? _l('ads_analytics_active') : _l('ads_analytics_inactive'); ?></td>
                                                <td>
                                                    <a href="<?= admin_url('contactcenter/manage_creative/' . $creative->id); ?>" class="btn btn-info btn-sm">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> <?php echo _l('ads_analytics_save_ad_set'); ?>
                                </button>
                                <?php if (!empty($ad_set)) { ?>
                                    <a href="<?= admin_url('contactcenter/delete_ad_set/' . $ad_set->id); ?>" class="btn btn-danger" onclick="return confirm('<?php echo _l('ads_analytics_delete_ad_set_confirm'); ?>');">
                                        <i class="fa fa-trash"></i> <?php echo _l('ads_analytics_delete_ad_set'); ?>
                                    </a>
                                <?php } ?>
                                <a href="<?= admin_url('contactcenter/manage_ad_set'); ?>" class="btn btn-default">
                                    <i class="fa fa-times"></i> <?php echo _l('ads_analytics_cancel'); ?>
                                </a>
                            </div>
                        </div>
                        
                        <?php if (!empty($ad_set)) { ?>
                            <input type="hidden" name="id" value="<?= $ad_set->id; ?>">
                        <?php } ?>
                        
                        <?php echo form_close(); ?>
                        
                        <hr>
                        
                        <h5>All Ad Sets</h5>
                        <?php if (!empty($ad_sets)) { ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ad_sets as $set) { ?>
                                            <tr>
                                                <td><?= htmlspecialchars($set->name); ?></td>
                                                <td><?= htmlspecialchars($set->description ?? ''); ?></td>
                                                <td><?= $set->is_active ? _l('ads_analytics_active') : _l('ads_analytics_inactive'); ?></td>
                                                <td>
                                                    <a href="<?= admin_url('contactcenter/manage_ad_set/' . $set->id); ?>" class="btn btn-info btn-sm">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p><?php echo _l('ads_analytics_no_ad_sets'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(document).ready(function() {
        // Initialize datepickers
        if (typeof appDatepicker === 'function') {
            appDatepicker();
        } else {
            // Fallback initialization
            $('.datepicker').each(function() {
                var $input = $(this);
                if (typeof jQuery.fn.datetimepicker !== 'undefined') {
                    $input.datetimepicker({
                        timepicker: false,
                        format: app.options.date_format || 'd/m/Y',
                        scrollInput: false,
                        lazyInit: true
                    });
                }
            });
        }
    });
</script>

