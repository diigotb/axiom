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
                                <i class="fa fa-file-excel"></i>
                                <?php echo _l('ads_analytics_reports'); ?>
                            </span>
                            <div class="pull-right">
                                <a href="<?= admin_url('contactcenter/ads_analytics_dashboard'); ?>" class="btn btn-default" style="margin-right: 10px;">
                                    <i class="fa fa-dashboard"></i> <?php echo _l('ads_analytics_dashboard'); ?>
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
                                <?php echo form_open(admin_url('contactcenter/ads_analytics_reports'), ['method' => 'get', 'id' => 'reports-filters-form']); ?>
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
                                            <select name="creative_id" class="selectpicker" data-width="100%" data-live-search="true">
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
                                            <select name="campaign_name" class="selectpicker" data-width="100%" data-live-search="true">
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
                        
                        <!-- Summary Cards -->
                        <?php if (isset($summary) && $summary) { ?>
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-2">
                                <div class="panel panel-primary fade-in">
                                    <div class="panel-body text-center" style="padding: 15px;">
                                        <h3 class="no-margin" style="color: #337ab7; font-weight: bold;"><?= number_format($summary['total_leads'] ?? 0); ?></h3>
                                        <p class="text-muted no-margin"><?= _l('leads'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="panel panel-success fade-in">
                                    <div class="panel-body text-center" style="padding: 15px;">
                                        <h3 class="no-margin" style="color: #5cb85c; font-weight: bold;"><?= number_format($summary['converted_leads'] ?? 0); ?></h3>
                                        <p class="text-muted no-margin">Convertidos</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="panel panel-info fade-in">
                                    <div class="panel-body text-center" style="padding: 15px;">
                                        <h3 class="no-margin" style="color: #5bc0de; font-weight: bold;">R$ <?= number_format($summary['total_invested'] ?? 0, 2, ',', '.'); ?></h3>
                                        <p class="text-muted no-margin"><?= _l('ads_analytics_total_invested'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="panel panel-warning fade-in">
                                    <div class="panel-body text-center" style="padding: 15px;">
                                        <h3 class="no-margin" style="color: #f0ad4e; font-weight: bold;">
                                            <?php 
                                            $cpl = $summary['avg_cpl'] ?? null;
                                            echo $cpl !== null ? 'R$ ' . number_format($cpl, 2, ',', '.') : 'N/A';
                                            ?>
                                        </h3>
                                        <p class="text-muted no-margin"><?= _l('ads_analytics_cpl'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="panel panel-default fade-in">
                                    <div class="panel-body text-center" style="padding: 15px;">
                                        <h3 class="no-margin" style="font-weight: bold;"><?= number_format($summary['conversion_rate'] ?? 0, 2); ?>%</h3>
                                        <p class="text-muted no-margin"><?= _l('ads_analytics_conversion_rate'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="panel panel-default fade-in">
                                    <div class="panel-body text-center" style="padding: 15px;">
                                        <h3 class="no-margin" style="font-weight: bold;"><?= number_format($summary['roi'] ?? 0, 2); ?>%</h3>
                                        <p class="text-muted no-margin">ROI</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <!-- Export Section -->
                        <div class="panel panel-default" style="margin-bottom: 20px;">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-download"></i> Exportar Relatório
                                </h4>
                            </div>
                            <div class="panel-body">
                                <form method="get" action="<?= admin_url('contactcenter/ads_analytics_export'); ?>" id="export-form">
                                    <input type="hidden" name="format" value="csv" id="export-format">
                                    <input type="hidden" name="date_from" value="<?= htmlspecialchars($filters['date_from_display'] ?? ''); ?>">
                                    <input type="hidden" name="date_to" value="<?= htmlspecialchars($filters['date_to_display'] ?? ''); ?>">
                                    <input type="hidden" name="creative_id" value="<?= htmlspecialchars($filters['creative_id'] ?? ''); ?>">
                                    <input type="hidden" name="campaign_name" value="<?= htmlspecialchars($filters['campaign_name'] ?? ''); ?>">
                                    <input type="hidden" name="status" value="<?= htmlspecialchars($filters['status'] ?? ''); ?>">
                                    <input type="hidden" name="assigned" value="<?= htmlspecialchars($filters['assigned'] ?? ''); ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Formato de Exportação</label>
                                                <select id="format-select" class="form-control">
                                                    <option value="csv">CSV</option>
                                                    <option value="excel">Excel</option>
                                                    <option value="pdf">PDF</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-success btn-block">
                                                    <i class="fa fa-download"></i> Exportar Relatório Completo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Creatives Performance Table -->
                        <div class="panel panel-default fade-in">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-table"></i> Desempenho por Criativo
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="creatives-report-table">
                                        <thead>
                                            <tr>
                                                <th><?= _l('ads_analytics_creative'); ?></th>
                                                <th><?= _l('ads_analytics_campaign'); ?></th>
                                                <th><?= _l('leads'); ?></th>
                                                <th>Convertidos</th>
                                                <th><?= _l('ads_analytics_conversion_rate'); ?></th>
                                                <th><?= _l('ads_analytics_total_invested'); ?></th>
                                                <th><?= _l('ads_analytics_cpl'); ?></th>
                                                <th>ROI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (isset($creatives_data) && !empty($creatives_data)) { ?>
                                                <?php foreach ($creatives_data as $creative) { 
                                                    $total_invested = $this->contactcenter_model->get_ads_total_invested($creative->id, $filters);
                                                    $cpl = $this->contactcenter_model->calculate_ads_cpl($creative->id, $filters, $creative->total_leads ?? 0);
                                                    $converted = $creative->converted_leads ?? 0;
                                                    $total_leads = $creative->total_leads ?? 0;
                                                    $conversion_rate = $total_leads > 0 ? ($converted / $total_leads) * 100 : 0;
                                                    $roi = $total_invested > 0 && $converted > 0 ? (($converted * 100 - $total_invested) / $total_invested) * 100 : 0;
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <a href="<?= admin_url('contactcenter/ads_analytics_detail/' . $creative->id); ?>">
                                                                <?= htmlspecialchars($creative->name ?? ''); ?>
                                                            </a>
                                                        </td>
                                                        <td><?= htmlspecialchars($creative->campaign_name ?? ''); ?></td>
                                                        <td class="text-center"><?= number_format($total_leads); ?></td>
                                                        <td class="text-center"><?= number_format($converted); ?></td>
                                                        <td class="text-center"><?= number_format($conversion_rate, 2); ?>%</td>
                                                        <td class="text-right">R$ <?= number_format($total_invested, 2, ',', '.'); ?></td>
                                                        <td class="text-right">
                                                            <?= $cpl !== null ? 'R$ ' . number_format($cpl, 2, ',', '.') : 'N/A'; ?>
                                                        </td>
                                                        <td class="text-right">
                                                            <span class="label label-<?= $roi >= 0 ? 'success' : 'danger'; ?>">
                                                                <?= number_format($roi, 2); ?>%
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">
                                                        <i class="fa fa-info-circle"></i> Nenhum dado encontrado para os filtros selecionados.
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Campaign Performance Table -->
                        <?php if (isset($campaign_performance) && !empty($campaign_performance)) { ?>
                        <div class="panel panel-default fade-in" style="margin-top: 20px;">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-line-chart"></i> Desempenho por Campanha
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Campanha</th>
                                                <th>Leads</th>
                                                <th>Convertidos</th>
                                                <th>Taxa de Conversão</th>
                                                <th>Investimento Total</th>
                                                <th>CPL Médio</th>
                                                <th>ROI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($campaign_performance as $campaign) { ?>
                                                <tr>
                                                    <td><strong><?= htmlspecialchars($campaign['campaign_name'] ?? ''); ?></strong></td>
                                                    <td class="text-center"><?= number_format($campaign['total_leads'] ?? 0); ?></td>
                                                    <td class="text-center"><?= number_format($campaign['converted_leads'] ?? 0); ?></td>
                                                    <td class="text-center"><?= number_format($campaign['conversion_rate'] ?? 0, 2); ?>%</td>
                                                    <td class="text-right">R$ <?= number_format($campaign['total_invested'] ?? 0, 2, ',', '.'); ?></td>
                                                    <td class="text-right">
                                                        <?php 
                                                        $campaign_cpl = $campaign['avg_cpl'] ?? null;
                                                        echo $campaign_cpl !== null ? 'R$ ' . number_format($campaign_cpl, 2, ',', '.') : 'N/A';
                                                        ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="label label-<?= ($campaign['roi'] ?? 0) >= 0 ? 'success' : 'danger'; ?>">
                                                            <?= number_format($campaign['roi'] ?? 0, 2); ?>%
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <!-- Top Performers -->
                        <?php if (isset($top_performers) && !empty($top_performers)) { ?>
                        <div class="panel panel-default fade-in" style="margin-top: 20px;">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-trophy"></i> Top Performers
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Posição</th>
                                                <th>Criativo</th>
                                                <th>Campanha</th>
                                                <th>Leads</th>
                                                <th>CPL</th>
                                                <th>Score</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($top_performers as $index => $performer) { 
                                                $creative = is_array($performer['creative']) ? (object)$performer['creative'] : $performer['creative'];
                                                $creative_id = isset($creative->id) ? $creative->id : (isset($creative->creative_id) ? $creative->creative_id : '');
                                                $creative_name = isset($creative->name) ? $creative->name : (isset($creative->creative_name) ? $creative->creative_name : '');
                                                $campaign_name = isset($creative->campaign_name) ? $creative->campaign_name : '';
                                                $total_leads = isset($creative->total_leads) ? $creative->total_leads : 0;
                                                $cpl = $performer['cpl'] ?? null;
                                                $score = $performer['score'] ?? 0;
                                            ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <span class="badge badge-<?= $index < 3 ? 'success' : 'default'; ?>" style="font-size: 14px; padding: 5px 10px;">
                                                            #<?= $index + 1; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($creative_id)) { ?>
                                                            <a href="<?= admin_url('contactcenter/ads_analytics_detail/' . $creative_id); ?>">
                                                                <?= htmlspecialchars($creative_name); ?>
                                                            </a>
                                                        <?php } else { ?>
                                                            <?= htmlspecialchars($creative_name); ?>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($campaign_name); ?></td>
                                                    <td class="text-center"><?= number_format($total_leads); ?></td>
                                                    <td class="text-right">
                                                        <?php 
                                                        echo $cpl !== null ? 'R$ ' . number_format($cpl, 2, ',', '.') : 'N/A';
                                                        ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="label label-<?= $score >= 70 ? 'success' : ($score >= 50 ? 'warning' : 'danger'); ?>">
                                                            <?= number_format($score, 1); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
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
(function() {
    'use strict';
    
    // Wait for jQuery to be available
    function initReportsPage() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            setTimeout(initReportsPage, 100);
            return;
        }
        
        var $ = jQuery;
        
        $(document).ready(function() {
            // Initialize datepickers (same pattern as ads_analytics.php)
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
            
            // Initialize DataTable for creatives report
            if (typeof $.fn.DataTable !== 'undefined' && $('#creatives-report-table').length) {
                $('#creatives-report-table').DataTable({
                    "order": [[2, "desc"]], // Sort by leads descending
                    "pageLength": 25,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
                    }
                });
            }
            
            // Format select change handler
            $('#format-select').on('change', function() {
                $('#export-format').val($(this).val());
            });
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReportsPage);
    } else {
        initReportsPage();
    }
})();
</script>
<style>
.fade-in {
    animation: fadeIn 0.5s ease-in;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>