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
                                <i class="fa-solid fa-object-group"></i>
                                <?php echo _l('ads_analytics_manage_ad_sets'); ?> - <?php echo _l('contactcenter_ads_analytics'); ?>
                            </span>
                            <div class="pull-right">
                                <a href="<?= admin_url('contactcenter/ads_analytics'); ?>" class="btn btn-default" style="margin-right: 10px;">
                                    <i class="fa fa-chart-line"></i> <?php echo _l('contactcenter_ads_analytics'); ?>
                                </a>
                                <a href="<?= admin_url('contactcenter/manage_ad_set'); ?>" class="btn btn-info" style="margin-right: 10px;">
                                    <i class="fa fa-object-group"></i> <?php echo _l('ads_analytics_manage_ad_sets'); ?>
                                </a>
                                <a href="<?= admin_url('contactcenter/manage_creative'); ?>" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> <?php echo _l('ads_analytics_add_creative'); ?>
                                </a>
                            </div>
                        </h4>
                        <hr class="hr-panel-separator" />
                        
                        <!-- Filters Section -->
                        <div class="panel panel-default" style="margin-bottom: 20px;">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-filter"></i> <?= _l("filters"); ?>
                                </h4>
                            </div>
                            <div class="panel-body">
                                <?php echo form_open(admin_url('contactcenter/ads_analytics_sets'), ['method' => 'get', 'id' => 'ads-analytics-sets-filters-form']); ?>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><?php echo _l('ads_analytics_date_from'); ?></label>
                                            <div class="input-group">
                                                <input type="text" name="date_from" class="form-control datepicker" value="<?= isset($filters['date_from_display']) && !empty($filters['date_from_display']) ? htmlspecialchars($filters['date_from_display']) : (isset($filters['date_from']) && !empty($filters['date_from']) ? _d($filters['date_from']) : ''); ?>" placeholder="<?php echo _l('ads_analytics_date_from'); ?>">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar calendar-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><?php echo _l('ads_analytics_date_to'); ?></label>
                                            <div class="input-group">
                                                <input type="text" name="date_to" class="form-control datepicker" value="<?= isset($filters['date_to_display']) && !empty($filters['date_to_display']) ? htmlspecialchars($filters['date_to_display']) : (isset($filters['date_to']) && !empty($filters['date_to']) ? _d($filters['date_to']) : ''); ?>" placeholder="<?php echo _l('ads_analytics_date_to'); ?>">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar calendar-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><?= _l("leads_dt_status"); ?></label>
                                            <select name="status" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""><?= _l("all"); ?></option>
                                                <?php if (isset($statuses) && !empty($statuses)) { ?>
                                                    <?php foreach ($statuses as $status) { ?>
                                                        <option value="<?php echo $status['id']; ?>" <?php echo (isset($filters['status']) && $filters['status'] == $status['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($status['name']); ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><?= _l("leads_dt_assigned"); ?></label>
                                            <select name="assigned" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""><?= _l("all"); ?></option>
                                                <?php if (isset($staff_members) && !empty($staff_members)) { ?>
                                                    <?php foreach ($staff_members as $staff) { ?>
                                                        <option value="<?php echo $staff['staffid']; ?>" <?php echo (isset($filters['assigned']) && $filters['assigned'] == $staff['staffid']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($staff['firstname'] . ' ' . $staff['lastname']); ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-filter"></i> <?= _l("apply_filters"); ?>
                                        </button>
                                        <a href="<?= admin_url('contactcenter/ads_analytics_sets'); ?>" class="btn btn-default">
                                            <i class="fa fa-times"></i> <?= _l("clear_filters"); ?>
                                        </a>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                        
                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                if ($ad_sets_analytics && !empty($ad_sets_analytics)) {
                                    foreach ($ad_sets_analytics as $ad_set) {
                                        // Calculate CPL and total invested
                                        $cpl_filters = $filters;
                                        $total_invested = $this->contactcenter_model->get_ads_set_total_invested($ad_set->id, $cpl_filters);
                                        $cpl = $this->contactcenter_model->calculate_ads_set_cpl($ad_set->id, $cpl_filters, $ad_set->total_leads ?? 0);
                                        
                                        // Get investment data
                                        $ad_set_investment = $this->contactcenter_model->get_ads_set_investment($ad_set->id);
                                        
                                        // Get creatives in this ad set
                                        $creatives_in_set = $this->contactcenter_model->get_ads_set_creatives($ad_set->id);
                                ?>
                                        <div class="col-md-3" style="margin-bottom: 20px;">
                                            <div class="panel panel-default">
                                                <div class="panel-body" style="min-height: 200px;">
                                                    <h5 class="card-title" style="margin-top: 0;">
                                                        <i class="fa fa-object-group"></i> <?= htmlspecialchars($ad_set->name); ?>
                                                    </h5>
                                                    <?php if (!empty($ad_set->description)) { ?>
                                                        <p class="card-text"><small class="text-muted"><?= htmlspecialchars($ad_set->description); ?></small></p>
                                                    <?php } ?>
                                                    <p class="card-text">
                                                        <strong><?php echo _l('ads_analytics_status'); ?>:</strong> 
                                                        <?= $ad_set->is_active ? _l('ads_analytics_active') : _l('ads_analytics_inactive'); ?>
                                                    </p>
                                                    <p class="card-text">
                                                        <strong><?php echo _l('ads_analytics_creative'); ?>:</strong> 
                                                        <?= $ad_set->creatives_count ?? 0; ?>
                                                    </p>
                                                    <p class="card-text"><strong><?php echo _l('ads_analytics_leads'); ?>:</strong> <?= $ad_set->total_leads ?? 0; ?></p>
                                                    <p class="card-text"><strong><?php echo _l('ads_analytics_converted'); ?>:</strong> <?= $ad_set->converted_leads ?? 0; ?></p>
                                                    <?php if ($cpl !== null) { ?>
                                                        <p class="card-text"><strong><?php echo _l('ads_analytics_cpl'); ?>:</strong> R$ <?= number_format($cpl, 2, ',', '.'); ?></p>
                                                    <?php } else { ?>
                                                        <p class="card-text"><strong><?php echo _l('ads_analytics_cpl'); ?>:</strong> <?php echo _l('ads_analytics_cpl_na'); ?></p>
                                                    <?php } ?>
                                                    <?php if ($total_invested > 0) { ?>
                                                        <p class="card-text"><strong><?php echo _l('ads_analytics_total_invested'); ?>:</strong> R$ <?= number_format($total_invested, 2, ',', '.'); ?></p>
                                                    <?php } ?>
                                                    <?php if ($ad_set_investment) { ?>
                                                        <p class="card-text">
                                                            <small class="text-muted">
                                                                <strong><?php echo _l('ads_analytics_daily_investment'); ?>:</strong> R$ <?= number_format($ad_set_investment->daily_investment, 2, ',', '.'); ?><br>
                                                                <strong><?php echo _l('period'); ?>:</strong> <?= _d($ad_set_investment->start_date); ?> 
                                                                <?= $ad_set_investment->end_date ? ' - ' . _d($ad_set_investment->end_date) : ' - ' . _l('ongoing'); ?>
                                                            </small>
                                                        </p>
                                                    <?php } ?>
                                                </div>
                                                <div class="panel-footer">
                                                    <a href="<?= admin_url("contactcenter/manage_ad_set/{$ad_set->id}"); ?>" class="btn btn-info btn-sm">
                                                        <i class="fa fa-edit"></i> <?php echo _l('ads_analytics_edit'); ?>
                                                    </a>
                                                    <a href="<?= admin_url("contactcenter/delete_ad_set/{$ad_set->id}"); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo _l('ads_analytics_delete_ad_set_confirm'); ?>');">
                                                        <i class="fa fa-trash"></i> <?php echo _l('ads_analytics_delete'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                <?php 
                                    }
                                } else {
                                    echo "<h4 class='text-center'>" . _l('ads_analytics_no_ad_sets') . " <a href='" . admin_url('contactcenter/manage_ad_set') . "'>" . _l('ads_analytics_add_ad_set') . "</a></h4>";
                                } 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(document).ready(function() {
        // Initialize selectpicker on filter dropdowns
        $('.selectpicker').selectpicker();
        
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
</body>
</html>



