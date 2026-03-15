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
                                <?= htmlspecialchars($creative->name); ?> - <?php echo _l('ads_analytics_analytics_detail'); ?>
                            </span>
                            <div class="pull-right">
                                <button type="button" class="btn btn-success" onclick="openReplicateModal(<?= $creative->id; ?>, '<?= htmlspecialchars($creative->name, ENT_QUOTES); ?>', '<?= htmlspecialchars($creative->campaign_name, ENT_QUOTES); ?>', <?= $creative->media_id; ?>, <?= isset($investment) && $investment ? $investment->daily_investment : 0; ?>, '<?= isset($investment) && $investment && !empty($investment->start_date) ? _d($investment->start_date) : ''; ?>', '<?= isset($investment) && $investment && !empty($investment->end_date) ? _d($investment->end_date) : ''; ?>')">
                                    <i class="fa fa-copy"></i> <?php echo _l('ads_analytics_replicate'); ?>
                                </button>
                                <a href="<?= admin_url('contactcenter/ads_analytics'); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> <?php echo _l('ads_analytics_back_to_ads_analytics'); ?>
                                </a>
                            </div>
                        </h4>
                        <hr class="hr-panel-separator" />
                        
                        <!-- Creative Info -->
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-3">
                                <div style="position: relative; width: 100%; padding-bottom: 56.25%; background: #000; border-radius: 4px; overflow: hidden;">
                                    <?php if ($creative->file_type == 'video') { ?>
                                        <video style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;" controls>
                                            <source src="<?= base_url($creative->file_path); ?>" type="video/<?= pathinfo($creative->file_path, PATHINFO_EXTENSION); ?>">
                                        </video>
                                    <?php } else { ?>
                                        <img src="<?= base_url($creative->thumbnail_path ? $creative->thumbnail_path : $creative->file_path); ?>" alt="<?= htmlspecialchars($creative->name); ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain;">
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <h5><?= htmlspecialchars($creative->name); ?></h5>
                                <p><strong><?php echo _l('ads_analytics_campaign'); ?>:</strong> <?= htmlspecialchars($creative->campaign_name); ?></p>
                                <p><strong><?php echo _l('ads_analytics_media_type'); ?>:</strong> <?= strtoupper($creative->file_type); ?></p>
                                <p><strong><?php echo _l('ads_analytics_status'); ?>:</strong> <?= $creative->is_active ? _l('ads_analytics_active') : _l('ads_analytics_inactive'); ?></p>
                                <?php if ($investment) { ?>
                                    <p><strong><?php echo _l('ads_analytics_daily_investment'); ?>:</strong> R$ <?= number_format($investment->daily_investment, 2, ',', '.'); ?></p>
                                    <p><strong><?php echo _l('period'); ?>:</strong> <?= _d($investment->start_date); ?> 
                                        <?= $investment->end_date ? ' - ' . _d($investment->end_date) : ' - ' . _l('ongoing'); ?>
                                    </p>
                                <?php } ?>
                                <hr>
                                <h5><?php echo _l('ads_analytics_statistics'); ?></h5>
                                <p><strong><?php echo _l('ads_analytics_total_leads'); ?>:</strong> <?= count($leads); ?></p>
                                <?php if ($total_invested > 0) { ?>
                                    <p><strong><?php echo _l('ads_analytics_total_invested'); ?>:</strong> R$ <?= number_format($total_invested, 2, ',', '.'); ?></p>
                                <?php } ?>
                                <?php if ($cpl !== null) { ?>
                                    <p><strong><?php echo _l('ads_analytics_cost_per_lead'); ?>:</strong> R$ <?= number_format($cpl, 2, ',', '.'); ?></p>
                                <?php } else { ?>
                                    <p><strong><?php echo _l('ads_analytics_cpl'); ?>:</strong> <?php echo _l('ads_analytics_cpl_na'); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <!-- Filters Section -->
                        <div class="panel panel-default" style="margin-bottom: 20px;">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-filter"></i> <?= _l("filters"); ?>
                                    <?php if (!empty(array_filter($filters))) { ?>
                                        <span class="badge" style="background-color: #2ecc71; margin-left: 10px;">
                                            <?= _l("filters_active"); ?>
                                        </span>
                                    <?php } ?>
                                </h4>
                            </div>
                            <div class="panel-body">
                                <?php 
                                $filter_url = admin_url("contactcenter/ads_analytics_detail/{$creative_id}");
                                echo form_open($filter_url, ['method' => 'get', 'id' => 'ads-analytics-detail-filters-form']); 
                                ?>
                                <div class="row">
                                    <div class="col-md-3">
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
                                    <div class="col-md-3">
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
                                                        <option value="<?php echo $status['id']; ?>" <?php echo (isset($filters['status']) && (string)$filters['status'] == (string)$status['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($status['name']); ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><?= _l("lead_sources"); ?></label>
                                            <select name="source" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""><?= _l("all"); ?></option>
                                                <?php if (isset($sources) && !empty($sources)) { ?>
                                                    <?php foreach ($sources as $source) { ?>
                                                        <option value="<?php echo $source['id']; ?>" <?php echo (isset($filters['source']) && (string)$filters['source'] == (string)$source['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($source['name']); ?>
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
                                                        <option value="<?php echo $staff['staffid']; ?>" <?php echo (isset($filters['assigned']) && (string)$filters['assigned'] == (string)$staff['staffid']) ? 'selected' : ''; ?>>
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
                                        <a href="<?= admin_url("contactcenter/ads_analytics_detail/{$creative_id}"); ?>" class="btn btn-default">
                                            <i class="fa fa-times"></i> <?= _l("clear_filters"); ?>
                                        </a>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>

                        <!-- Charts -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><?php echo _l('ads_analytics_leads_by_day'); ?></h4>
                                    </div>
                                    <div class="panel-body">
                                        <canvas id="chart_leads_day" style="height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title"><?php echo _l('ads_analytics_leads_by_status'); ?></h4>
                                    </div>
                                    <div class="panel-body">
                                        <canvas id="chart_leads_status" style="height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="hr-panel-separator" />
                        <div class="clearfix"></div>
                        
                        <!-- Leads Table -->
                        <div>
                            <table id="ads_leads_table" class="table">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th><?= _l("leads_dt_name"); ?></th>
                                        <th><?= _l("leads_dt_phonenumber"); ?></th>
                                        <th><?= _l("leads_dt_assigned"); ?></th>
                                        <th><?= _l("leads_dt_status"); ?></th>
                                        <th><?= _l("lead_sources"); ?></th>
                                        <th><?php echo _l('ads_analytics_date_added'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($leads)) {
                                        foreach ($leads as $lead) {
                                            // Get status name
                                            $status_name = 'N/A';
                                            if ($lead->status) {
                                                $statuses_list = $this->leads_model->get_status();
                                                foreach ($statuses_list as $s) {
                                                    if ($s['id'] == $lead->status) {
                                                        $status_name = $s['name'];
                                                        break;
                                                    }
                                                }
                                            }
                                            
                                            // Get source name
                                            $source_name = 'N/A';
                                            if ($lead->source) {
                                                $sources_list = $this->leads_model->get_source();
                                                foreach ($sources_list as $src) {
                                                    if ($src['id'] == $lead->source) {
                                                        $source_name = $src['name'];
                                                        break;
                                                    }
                                                }
                                            }
                                    ?>
                                        <tr>
                                            <td><a href="<?= admin_url('leads/index/' . $lead->id) ?>" onclick="init_lead(<?= $lead->id ?>);return false;"><?= $lead->id ?></a></td>
                                            <td><a href="<?= admin_url('leads/index/' . $lead->id) ?>" onclick="init_lead(<?= $lead->id ?>);return false;"><?= htmlspecialchars($lead->name) ?></a></td>
                                            <td><?= htmlspecialchars($lead->phonenumber) ?></td>
                                            <td><?= get_staff_full_name($lead->assigned); ?></td>
                                            <td><?= htmlspecialchars($status_name); ?></td>
                                            <td><?= htmlspecialchars($source_name); ?></td>
                                            <td><?= _dt($lead->dateadded); ?></td>
                                        </tr>
                                    <?php 
                                        }
                                    } else {
                                    ?>
                                        <tr>
                                            <td colspan="7" class="text-center"><?php echo _l('ads_analytics_no_leads_found'); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Prepare chart data
$chart_labels = [];
$chart_data = [];
if (!empty($chart_leads_daily)) {
    foreach ($chart_leads_daily as $day) {
        $chart_labels[] = _d($day->date);
        $chart_data[] = (int)$day->count;
    }
} else {
    $chart_labels[] = 'No data';
    $chart_data[] = 0;
}

$chart_status_labels = [];
$chart_status_data = [];
if (!empty($chart_leads_status)) {
    foreach ($chart_leads_status as $status) {
        $chart_status_labels[] = $status->status_name ?: 'Unknown';
        $chart_status_data[] = (int)$status->count;
    }
} else {
    $chart_status_labels[] = 'No data';
    $chart_status_data[] = 0;
}
?>

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
        
        // Initialize DataTable
        appDataTableInline("#ads_leads_table", {
            supportsButtons: true,
            supportsLoading: true,
            autoWidth: false,
            order: [[0, 'desc']]
        });
        
        // Chart: Leads by Day
        var ctxDay = document.getElementById("chart_leads_day").getContext('2d');
        var gradientStroke1 = ctxDay.createLinearGradient(0, 0, 0, 300);
        gradientStroke1.addColorStop(0, '#00e09b');
        gradientStroke1.addColorStop(1, '#17c5ea');
        
        new Chart(ctxDay, {
            type: 'line',
            data: {
                labels: <?= json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Leads per Day',
                    data: <?= json_encode($chart_data); ?>,
                    backgroundColor: gradientStroke1,
                    borderColor: gradientStroke1,
                    borderWidth: 2,
                    pointRadius: 3,
                    fill: false,
                    tension: 0.5,
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // Chart: Leads by Status
        var ctxStatus = document.getElementById("chart_leads_status").getContext('2d');
        var colors = ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6', '#1abc9c', '#34495e'];
        var statusColors = [];
        for (var i = 0; i < <?= count($chart_status_labels); ?>; i++) {
            statusColors.push(colors[i % colors.length]);
        }
        
        new Chart(ctxStatus, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chart_status_labels); ?>,
                datasets: [{
                    label: '<?php echo _l('ads_analytics_leads_by_status'); ?>',
                    data: <?= json_encode($chart_status_data); ?>,
                    backgroundColor: statusColors,
                    borderColor: statusColors,
                    borderWidth: 2,
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
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
        $('#replicate-result').html('<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Creating replicate...</div>');
        
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
                        window.location.href = '<?= admin_url('contactcenter/ads_analytics'); ?>';
                    }, 1500);
                } else {
                    var errorMsg = response && response.message ? response.message : 'Error replicating creative. Please try again.';
                    $('#replicate-result').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            },
            error: function(xhr, status, error) {
                $('#replicate-btn').prop('disabled', false);
                var errorMsg = 'Error replicating creative. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#replicate-result').html('<div class="alert alert-danger">' + errorMsg + '</div>');
            }
        });
    });
</script>

<!-- Replicate Creative Modal -->
<div class="modal fade" id="replicateCreativeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-copy"></i> Replicate Creative
                </h4>
            </div>
            <div class="modal-body">
                <form id="replicate-creative-form">
                    <input type="hidden" id="replicate_creative_id" name="creative_id" value="">
                    <input type="hidden" id="replicate_media_id" name="media_id" value="">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="replicate_name">Creative Name <span class="text-danger">*</span></label>
                                <input type="text" id="replicate_name" name="name" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="replicate_campaign_name">Campaign(s) <span class="text-danger">*</span></label>
                                <select id="replicate_campaign_name" name="campaign_name[]" class="selectpicker form-control" data-width="100%" data-live-search="true" multiple data-actions-box="true" data-selected-text-format="count > 3" required>
                                    <?php if (isset($campaigns) && !empty($campaigns)) { ?>
                                        <?php foreach ($campaigns as $campaign) { ?>
                                            <option value="<?= htmlspecialchars($campaign); ?>">
                                                <?= htmlspecialchars($campaign); ?>
                                            </option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                                <small class="form-text text-muted">Select one or more campaigns. A creative will be created for each selected campaign.</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h5>Investment Settings</h5>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="replicate_daily_investment">Daily Investment (R$)</label>
                                <input type="number" id="replicate_daily_investment" name="daily_investment" class="form-control" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="replicate_start_date">Start Date</label>
                                <input type="date" id="replicate_start_date" name="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="replicate_end_date">End Date (Optional)</label>
                                <input type="date" id="replicate_end_date" name="end_date" class="form-control">
                                <small class="form-text text-muted">Leave empty for ongoing investment</small>
                            </div>
                        </div>
                    </div>
                    
                    <div id="replicate-result" style="margin-top: 15px;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="replicate-btn">
                    <i class="fa fa-copy"></i> Replicate Creative
                </button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
