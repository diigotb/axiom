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
                                <i class="fa-solid fa-chart-line"></i>
                                <?php echo _l('contactcenter_ads_analytics'); ?>
                            </span>
                            <div class="pull-right">
                                <a href="<?= admin_url('contactcenter/ads_analytics_sets'); ?>" class="btn btn-success" style="margin-right: 10px;">
                                    <i class="fa fa-object-group"></i> <?php echo _l('ads_analytics_manage_ad_sets'); ?> - <?php echo _l('contactcenter_ads_analytics'); ?>
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
                                <?php echo form_open(admin_url('contactcenter/ads_analytics'), ['method' => 'get', 'id' => 'ads-analytics-filters-form']); ?>
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
                                            <label><?php echo _l('ads_analytics_creative'); ?></label>
                                            <select name="creative_id" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""><?= _l("all"); ?></option>
                                                <?php if (isset($creatives) && !empty($creatives)) { ?>
                                                    <?php foreach ($creatives as $creative) { ?>
                                                        <option value="<?php echo $creative->id; ?>" <?php echo (isset($filters['creative_id']) && $filters['creative_id'] == $creative->id) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($creative->name); ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><?php echo _l('ads_analytics_campaign'); ?></label>
                                            <select name="campaign_name" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""><?= _l("all"); ?></option>
                                                <?php if (isset($campaigns) && !empty($campaigns)) { ?>
                                                    <?php foreach ($campaigns as $campaign) { ?>
                                                        <option value="<?php echo htmlspecialchars($campaign); ?>" <?php echo (isset($filters['campaign_name']) && $filters['campaign_name'] == $campaign) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($campaign); ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
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
                                        <a href="<?= admin_url('contactcenter/ads_analytics'); ?>" class="btn btn-default">
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
                                if ($creatives_analytics && !empty($creatives_analytics)) {
                                    foreach ($creatives_analytics as $creative) {
                                        // Get media thumbnail or default
                                        if ($creative->thumbnail_path) {
                                            $thumb = base_url($creative->thumbnail_path);
                                        } elseif ($creative->file_path) {
                                            $thumb = base_url($creative->file_path);
                                        } else {
                                            $thumb = site_url("modules/contactcenter/assets/image/img-not.jpeg");
                                        }
                                        
                                        // Calculate CPL using the same lead count as displayed on card
                                        $cpl_filters = $filters;
                                        $total_invested = $this->contactcenter_model->get_ads_total_invested($creative->id, $cpl_filters);
                                        $cpl = $this->contactcenter_model->calculate_ads_cpl($creative->id, $cpl_filters, $creative->total_leads ?? 0);
                                        
                                        // Get investment data for replicate modal
                                        $creative_investment = $this->contactcenter_model->get_ads_creative_investment($creative->id);
                                        
                                        // Build URL with filters (use display format for URL)
                                        $detail_url = admin_url("contactcenter/ads_analytics_detail/{$creative->id}");
                                        $filter_params = [];
                                        if (!empty($filters['date_from_display'])) {
                                            $filter_params['date_from'] = $filters['date_from_display'];
                                        } elseif (!empty($filters['date_from'])) {
                                            $filter_params['date_from'] = _d($filters['date_from']);
                                        }
                                        if (!empty($filters['date_to_display'])) {
                                            $filter_params['date_to'] = $filters['date_to_display'];
                                        } elseif (!empty($filters['date_to'])) {
                                            $filter_params['date_to'] = _d($filters['date_to']);
                                        }
                                        if (!empty($filters['status'])) $filter_params['status'] = $filters['status'];
                                        if (!empty($filters['source'])) $filter_params['source'] = $filters['source'];
                                        if (!empty($filters['assigned'])) $filter_params['assigned'] = $filters['assigned'];
                                        if (!empty($filter_params)) {
                                            $detail_url .= '?' . http_build_query($filter_params);
                                        }
                                ?>
                                        <div class="col-md-3" style="margin-bottom: 20px;">
                                            <div class="panel panel-default">
                                                <div style="position: relative; width: 100%; padding-bottom: 56.25%; background: #000; overflow: hidden;">
                                                    <?php if ($creative->file_type == 'video') { ?>
                                                        <video style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;" controls>
                                                            <source src="<?= base_url($creative->file_path); ?>" type="video/<?= pathinfo($creative->file_path, PATHINFO_EXTENSION); ?>">
                                                        </video>
                                                    <?php } else { ?>
                                                        <img class="card-img-top" src="<?= $thumb; ?>" alt="<?= htmlspecialchars($creative->name); ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;">
                                                    <?php } ?>
                                                </div>
                                                <div class="panel-body">
                                                    <h5 class="card-title" style="margin-top: 0;"><?= htmlspecialchars($creative->name); ?></h5>
                                                    <p class="card-text"><strong><?php echo _l('ads_analytics_campaign'); ?>:</strong> <?= htmlspecialchars($creative->campaign_name); ?></p>
                                                    <?php if (!empty($creative->ad_set_name)) { ?>
                                                        <p class="card-text">
                                                            <strong><?php echo _l('ads_analytics_ad_set'); ?>:</strong> 
                                                            <span class="badge badge-info"><?= htmlspecialchars($creative->ad_set_name); ?></span>
                                                        </p>
                                                    <?php } ?>
                                                    <p class="card-text"><strong><?php echo _l('ads_analytics_leads'); ?>:</strong> <?= $creative->total_leads ?? 0; ?></p>
                                                    <p class="card-text"><strong><?php echo _l('ads_analytics_converted'); ?>:</strong> <?= $creative->converted_leads ?? 0; ?></p>
                                                    <?php if ($cpl !== null) { ?>
                                                        <p class="card-text"><strong><?php echo _l('ads_analytics_cpl'); ?>:</strong> R$ <?= number_format($cpl, 2, ',', '.'); ?></p>
                                                    <?php } else { ?>
                                                        <p class="card-text"><strong><?php echo _l('ads_analytics_cpl'); ?>:</strong> <?php echo _l('ads_analytics_cpl_na'); ?></p>
                                                    <?php } ?>
                                                    <?php if ($total_invested > 0) { ?>
                                                        <p class="card-text"><strong><?php echo _l('ads_analytics_total_invested'); ?>:</strong> R$ <?= number_format($total_invested, 2, ',', '.'); ?></p>
                                                    <?php } ?>
                                                </div>
                                                <div class="panel-footer">
                                                    <a href="<?= $detail_url; ?>" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-eye"></i> <?php echo _l('ads_analytics_view_details'); ?>
                                                    </a>
                                                    <a href="<?= admin_url("contactcenter/manage_creative/{$creative->id}"); ?>" class="btn btn-info btn-sm">
                                                        <i class="fa fa-edit"></i> <?php echo _l('ads_analytics_edit'); ?>
                                                    </a>
                                                    <button type="button" class="btn btn-success btn-sm" onclick="openReplicateModal(<?= $creative->id; ?>, '<?= htmlspecialchars($creative->name, ENT_QUOTES); ?>', '<?= htmlspecialchars($creative->campaign_name, ENT_QUOTES); ?>', <?= $creative->media_id; ?>, <?= $creative_investment && $creative_investment->daily_investment ? $creative_investment->daily_investment : 0; ?>, '<?= $creative_investment && $creative_investment->start_date ? _d($creative_investment->start_date) : ''; ?>', '<?= $creative_investment && $creative_investment->end_date ? _d($creative_investment->end_date) : ''; ?>')">
                                                        <i class="fa fa-copy"></i> <?php echo _l('ads_analytics_replicate'); ?>
                                                    </button>
                                                    <a href="<?= admin_url("contactcenter/delete_ads_creative/{$creative->id}"); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo _l('ads_analytics_delete_confirm'); ?>');">
                                                        <i class="fa fa-trash"></i> <?php echo _l('ads_analytics_delete'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                <?php 
                                    }
                                } else {
                                    echo "<h4 class='text-center'>" . _l('ads_analytics_no_creatives') . " <a href='" . admin_url('contactcenter/manage_creative') . "'>" . _l('ads_analytics_create_first_creative') . "</a></h4>";
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
<!-- Replicate Creative Modal -->
<div class="modal fade" id="replicateCreativeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-copy"></i> <?php echo _l('ads_analytics_replicate_creative'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <form id="replicate-creative-form">
                    <input type="hidden" id="replicate_creative_id" name="creative_id" value="">
                    <input type="hidden" id="replicate_media_id" name="media_id" value="">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="replicate_name"><?php echo _l('ads_analytics_creative_name'); ?> <span class="text-danger">*</span></label>
                                <input type="text" id="replicate_name" name="name" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="replicate_campaign_name"><?php echo _l('ads_analytics_campaigns'); ?> <span class="text-danger">*</span></label>
                                <select id="replicate_campaign_name" name="campaign_name[]" class="selectpicker form-control" data-width="100%" data-live-search="true" multiple data-actions-box="true" data-selected-text-format="count > 3" required>
                                    <?php if (isset($campaigns) && !empty($campaigns)) { ?>
                                        <?php foreach ($campaigns as $campaign) { ?>
                                            <option value="<?= htmlspecialchars($campaign); ?>">
                                                <?= htmlspecialchars($campaign); ?>
                                            </option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <small class="form-text text-muted"><?php echo _l('ads_analytics_select_campaigns'); ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h5><?php echo _l('ads_analytics_investment_settings'); ?></h5>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="replicate_daily_investment"><?php echo _l('ads_analytics_daily_investment'); ?></label>
                                <input type="number" id="replicate_daily_investment" name="daily_investment" class="form-control" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="replicate_start_date"><?php echo _l('ads_analytics_start_date'); ?></label>
                                <div class="input-group">
                                    <input type="text" id="replicate_start_date" name="start_date" class="form-control datepicker" placeholder="<?php echo _l('ads_analytics_start_date'); ?>">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar calendar-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="replicate_end_date"><?php echo _l('ads_analytics_end_date'); ?></label>
                                <div class="input-group">
                                    <input type="text" id="replicate_end_date" name="end_date" class="form-control datepicker" placeholder="<?php echo _l('ads_analytics_end_date'); ?>">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar calendar-icon"></i>
                                    </div>
                                </div>
                                <small class="form-text text-muted"><?php echo _l('ads_analytics_end_date_help'); ?></small>
                            </div>
                        </div>
                    </div>
                    
                    <div id="replicate-result" style="margin-top: 15px;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('ads_analytics_cancel'); ?></button>
                <button type="button" class="btn btn-primary" id="replicate-btn">
                    <i class="fa fa-copy"></i> <?php echo _l('ads_analytics_replicate_creative'); ?>
                </button>
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
    
    function openReplicateModal(creativeId, name, campaignName, mediaId, dailyInvestment, startDate, endDate) {
        $('#replicate_creative_id').val(creativeId);
        $('#replicate_media_id').val(mediaId);
        $('#replicate_name').val(name + ' <?php echo _l('ads_analytics_copy'); ?>');
        // Pre-select the original campaign, but allow multiple selection
        $('#replicate_campaign_name').val([campaignName]).selectpicker('refresh');
        $('#replicate_daily_investment').val(dailyInvestment || '');
        $('#replicate_start_date').val(startDate || '');
        $('#replicate_end_date').val(endDate || '');
        $('#replicate-result').html('');
        
        $('#replicateCreativeModal').modal('show');
    }
    
    $('#replicate-btn').on('click', function() {
        var formData = $('#replicate-creative-form').serialize();
        formData += '&<?php echo $this->security->get_csrf_token_name(); ?>=<?php echo $this->security->get_csrf_hash(); ?>';
        
        $('#replicate-btn').prop('disabled', true);
        $('#replicate-result').html('<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> <?php echo _l('ads_analytics_creating_replicate'); ?></div>');
        
        $.ajax({
            url: '<?= admin_url('contactcenter/replicate_creative'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                $('#replicate-btn').prop('disabled', false);
                
                if (response && response.success) {
                    $('#replicate-result').html('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(function() {
                        $('#replicateCreativeModal').modal('hide');
                        location.reload();
                    }, 1500);
                } else {
                    var errorMsg = response && response.message ? response.message : '<?php echo _l('ads_analytics_error_replicating'); ?>';
                    $('#replicate-result').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            },
            error: function(xhr, status, error) {
                $('#replicate-btn').prop('disabled', false);
                var errorMsg = '<?php echo _l('ads_analytics_error_replicating'); ?>';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#replicate-result').html('<div class="alert alert-danger">' + errorMsg + '</div>');
            }
        });
    });
</script>
</body>
</html>
