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
                                <i class="fa fa-dashboard"></i>
                                <?php echo _l('ads_analytics_dashboard'); ?>
                            </span>
                            <div class="pull-right">
                                <a href="<?= admin_url('contactcenter/ads_analytics'); ?>" class="btn btn-default" style="margin-right: 10px;">
                                    <i class="fa fa-list"></i> <?php echo _l('contactcenter_ads_analytics'); ?>
                                </a>
                                <a href="<?= admin_url('contactcenter/ads_analytics_ai_insights'); ?>" class="btn btn-info" style="margin-right: 10px;">
                                    <i class="fa fa-brain"></i> <?php echo _l('ads_analytics_ai_insights'); ?>
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
                                <?php echo form_open(admin_url('contactcenter/ads_analytics_dashboard'), ['method' => 'get', 'id' => 'dashboard-filters-form']); ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?php echo _l('ads_analytics_date_from'); ?></label>
                                            <div class="input-group">
                                                <input type="text" name="date_from" class="form-control datepicker" value="<?= isset($filters['date_from_display']) && !empty($filters['date_from_display']) ? htmlspecialchars($filters['date_from_display']) : ''; ?>" placeholder="<?php echo _l('ads_analytics_date_from'); ?>">
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
                                                <input type="text" name="date_to" class="form-control datepicker" value="<?= isset($filters['date_to_display']) && !empty($filters['date_to_display']) ? htmlspecialchars($filters['date_to_display']) : ''; ?>" placeholder="<?php echo _l('ads_analytics_date_to'); ?>">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar calendar-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><?= _l("leads_dt_status"); ?></label>
                                            <select name="status" class="selectpicker" data-width="100%" data-live-search="true">
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
                                            <select name="assigned" class="selectpicker" data-width="100%" data-live-search="true">
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
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fa fa-search"></i> <?= _l("filter"); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                        
                        <!-- KPI Cards Section -->
                        <div class="row" id="kpi-cards-section">
                            <div class="col-md-2 col-sm-6">
                                <div class="kpi-card" data-kpi="total_leads">
                                    <div class="kpi-icon kpi-bg-primary">
                                        <i class="fa fa-users"></i>
                                    </div>
                                    <div class="kpi-content">
                                        <div class="kpi-value" id="kpi-total-leads">
                                            <?php echo number_format($summary['total_leads'] ?? 0, 0, ',', '.'); ?>
                                        </div>
                                        <div class="kpi-label"><?php echo _l('ads_analytics_leads'); ?></div>
                                        <div class="kpi-trend" id="kpi-total-leads-trend"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-2 col-sm-6">
                                <div class="kpi-card" data-kpi="converted">
                                    <div class="kpi-icon kpi-bg-success">
                                        <i class="fa fa-check-circle"></i>
                                    </div>
                                    <div class="kpi-content">
                                        <div class="kpi-value" id="kpi-converted">
                                            <?php echo number_format($summary['converted_leads'] ?? 0, 0, ',', '.'); ?>
                                        </div>
                                        <div class="kpi-label"><?php echo _l('ads_analytics_converted'); ?></div>
                                        <div class="kpi-badge">
                                            <?php echo number_format($summary['conversion_rate'] ?? 0, 1); ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-2 col-sm-6">
                                <div class="kpi-card" data-kpi="invested">
                                    <div class="kpi-icon kpi-bg-info">
                                        <i class="fa fa-dollar-sign"></i>
                                    </div>
                                    <div class="kpi-content">
                                        <div class="kpi-value" id="kpi-invested">
                                            R$ <?php echo number_format($summary['total_invested'] ?? 0, 0, ',', '.'); ?>
                                        </div>
                                        <div class="kpi-label"><?php echo _l('ads_analytics_total_invested'); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-2 col-sm-6">
                                <div class="kpi-card" data-kpi="cpl">
                                    <div class="kpi-icon kpi-bg-warning">
                                        <i class="fa fa-tag"></i>
                                    </div>
                                    <div class="kpi-content">
                                        <div class="kpi-value" id="kpi-cpl">
                                            <?php 
                                            if (isset($summary['avg_cpl']) && $summary['avg_cpl'] !== null) {
                                                echo 'R$ ' . number_format($summary['avg_cpl'], 2, ',', '.');
                                            } else {
                                                echo _l('ads_analytics_cpl_na');
                                            }
                                            ?>
                                        </div>
                                        <div class="kpi-label"><?php echo _l('ads_analytics_avg_cpl'); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-2 col-sm-6">
                                <div class="kpi-card" data-kpi="roi">
                                    <div class="kpi-icon kpi-bg-accent">
                                        <i class="fa fa-chart-line"></i>
                                    </div>
                                    <div class="kpi-content">
                                        <div class="kpi-value" id="kpi-roi">
                                            <?php echo number_format($summary['roi'] ?? 0, 1); ?>%
                                        </div>
                                        <div class="kpi-label"><?php echo _l('ads_analytics_roi'); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-2 col-sm-6">
                                <div class="kpi-card" data-kpi="creatives">
                                    <div class="kpi-icon kpi-bg-secondary">
                                        <i class="fa fa-palette"></i>
                                    </div>
                                    <div class="kpi-content">
                                        <div class="kpi-value" id="kpi-creatives">
                                            <?php echo number_format($summary['creatives_count'] ?? 0, 0, ',', '.'); ?>
                                        </div>
                                        <div class="kpi-label"><?php echo _l('ads_analytics_creatives'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Charts Section -->
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <i class="fa fa-line-chart"></i> <?php echo _l('ads_analytics_leads_over_time'); ?>
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <canvas id="leads-trend-chart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <i class="fa fa-bar-chart"></i> <?php echo _l('ads_analytics_performance_by_campaign'); ?>
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <canvas id="campaign-performance-chart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <i class="fa fa-chart-bar"></i> <?php echo _l('ads_analytics_cpl_distribution'); ?>
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <canvas id="cpl-distribution-chart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <i class="fa fa-scatter-chart"></i> <?php echo _l('ads_analytics_investment_vs_leads'); ?>
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <canvas id="investment-vs-leads-chart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Top Performers Section -->
                        <?php if (isset($top_performers) && !empty($top_performers)) { ?>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-6">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <i class="fa fa-trophy"></i> <?php echo _l('ads_analytics_top_5_performers'); ?>
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="list-group">
                                            <?php foreach (array_slice($top_performers['top'] ?? [], 0, 5) as $index => $performer) { ?>
                                                <div class="list-group-item">
                                                    <h5 class="list-group-item-heading">
                                                        #<?php echo $index + 1; ?> - <?php echo htmlspecialchars($performer['creative']->name ?? ''); ?>
                                                        <span class="pull-right label label-success">Score: <?php echo $performer['score'] ?? 0; ?></span>
                                                    </h5>
                                                    <p class="list-group-item-text">
                                                        <?php echo _l('ads_analytics_leads'); ?>: <?php echo $performer['creative']->total_leads ?? 0; ?> | 
                                                        <?php echo _l('ads_analytics_cpl'); ?>: <?php echo $performer['cpl'] !== null ? 'R$ ' . number_format($performer['cpl'], 2, ',', '.') : _l('ads_analytics_cpl_na'); ?> |
                                                        Conversão: <?php echo number_format($performer['conversion_rate'] ?? 0, 1); ?>%
                                                    </p>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="panel panel-danger">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <i class="fa fa-exclamation-triangle"></i> <?php echo _l('ads_analytics_worst_5_performers'); ?>
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="list-group">
                                            <?php foreach (array_slice($top_performers['worst'] ?? [], 0, 5) as $index => $performer) { ?>
                                                <div class="list-group-item">
                                                    <h5 class="list-group-item-heading">
                                                        #<?php echo $index + 1; ?> - <?php echo htmlspecialchars($performer['creative']->name ?? ''); ?>
                                                        <span class="pull-right label label-danger">Score: <?php echo $performer['score'] ?? 0; ?></span>
                                                    </h5>
                                                    <p class="list-group-item-text">
                                                        <?php echo _l('ads_analytics_leads'); ?>: <?php echo $performer['creative']->total_leads ?? 0; ?> | 
                                                        <?php echo _l('ads_analytics_cpl'); ?>: <?php echo $performer['cpl'] !== null ? 'R$ ' . number_format($performer['cpl'], 2, ',', '.') : _l('ads_analytics_cpl_na'); ?> |
                                                        Conversão: <?php echo number_format($performer['conversion_rate'] ?? 0, 1); ?>%
                                                    </p>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
(function($) {
    $(document).ready(function() {
        // Initialize datepickers
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            language: 'pt-BR'
        });
        
        // Initialize CountUp animations - wait for library to load
        setTimeout(function() {
            <?php if (isset($summary['total_leads'])) { ?>
            if (typeof CountUp !== 'undefined') {
                try {
                    new CountUp('kpi-total-leads', <?php echo (int)($summary['total_leads'] ?? 0); ?>, {
                        separator: '.',
                        duration: 2
                    }).start();
                } catch(e) {
                    console.log('CountUp error:', e);
                }
            }
            <?php } ?>
        }, 500);
        
        // Leads Trend Chart
        <?php if (isset($leads_trend) && !empty($leads_trend)) { ?>
        var leadsTrendCtx = document.getElementById('leads-trend-chart');
        if (leadsTrendCtx && typeof Chart !== 'undefined') {
            new Chart(leadsTrendCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_map(function($item) { return _d($item->date); }, $leads_trend)); ?>,
                    datasets: [{
                        label: '<?php echo _l('ads_analytics_leads'); ?>',
                        data: <?php echo json_encode(array_column($leads_trend, 'leads_count')); ?>,
                        borderColor: 'rgba(0, 224, 155, 1)',
                        backgroundColor: 'rgba(0, 224, 155, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        <?php } else { ?>
        $('#leads-trend-chart').closest('.panel-body').html('<p class="text-muted text-center">Sem dados para exibir</p>');
        <?php } ?>
        
        // Campaign Performance Chart
        <?php if (isset($campaign_performance) && !empty($campaign_performance)) { ?>
        var campaignCtx = document.getElementById('campaign-performance-chart');
        if (campaignCtx && typeof Chart !== 'undefined') {
            new Chart(campaignCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_map(function($item) { return is_array($item) ? $item['campaign_name'] : $item->campaign_name; }, $campaign_performance)); ?>,
                    datasets: [{
                        label: '<?php echo _l('ads_analytics_leads'); ?>',
                        data: <?php 
                            $leads_data = [];
                            foreach ($campaign_performance as $item) {
                                $leads_data[] = is_array($item) ? ($item['total_leads'] ?? $item['leads_count'] ?? 0) : ($item->leads_count ?? 0);
                            }
                            echo json_encode($leads_data);
                        ?>,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)'
                    }, {
                        label: '<?php echo _l('ads_analytics_converted'); ?>',
                        data: <?php 
                            $converted_data = [];
                            foreach ($campaign_performance as $item) {
                                $converted_data[] = is_array($item) ? ($item['converted_leads'] ?? $item['converted_count'] ?? 0) : ($item->converted_count ?? 0);
                            }
                            echo json_encode($converted_data);
                        ?>,
                        backgroundColor: 'rgba(16, 185, 129, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        <?php } else { ?>
        $('#campaign-performance-chart').closest('.panel-body').html('<p class="text-muted text-center">Sem dados para exibir</p>');
        <?php } ?>
        
        // CPL Distribution Chart
        <?php 
        if (isset($cpl_distribution) && !empty($cpl_distribution)) { 
            $top_cpl = array_slice($cpl_distribution, 0, 10);
        ?>
        var cplCtx = document.getElementById('cpl-distribution-chart');
        if (cplCtx && typeof Chart !== 'undefined') {
            new Chart(cplCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_column($top_cpl, 'creative_name')); ?>,
                    datasets: [{
                        label: 'CPL',
                        data: <?php echo json_encode(array_column($top_cpl, 'cpl')); ?>,
                        backgroundColor: 'rgba(245, 158, 11, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        <?php } else { ?>
        $('#cpl-distribution-chart').closest('.panel-body').html('<p class="text-muted text-center">Sem dados para exibir</p>');
        <?php } ?>
        
        // Investment vs Leads Chart
        <?php if (isset($investment_vs_leads) && !empty($investment_vs_leads)) { ?>
        var investmentCtx = document.getElementById('investment-vs-leads-chart');
        if (investmentCtx && typeof Chart !== 'undefined') {
            new Chart(investmentCtx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'Investimento vs Leads',
                        data: <?php echo json_encode($investment_vs_leads); ?>,
                        backgroundColor: 'rgba(0, 224, 155, 0.6)',
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'linear',
                            position: 'bottom',
                            title: {
                                display: true,
                                text: 'Investimento (R$)'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Leads'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        <?php } else { ?>
        $('#investment-vs-leads-chart').closest('.panel-body').html('<p class="text-muted text-center">Sem dados para exibir</p>');
        <?php } ?>
    });
})(jQuery);
</script>
