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
                                <i class="fa-brands fa-whatsapp"></i>
                                <i class="fa-brands fa-facebook"></i>
                                <i class="fa-brands fa-instagram"></i>
                                <?php echo _l('contactcenter_meta'); ?>
                            </span>
                        </h4>
                        
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
                                $filter_url = admin_url("contactcenter/meta_analytics/{$meta_analytics_id}");
                                echo form_open($filter_url, ['method' => 'get', 'id' => 'meta-analytics-filters-form']); 
                                ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?= _l("date_from"); ?></label>
                                            <input type="date" name="date_from" class="form-control" value="<?= isset($filters['date_from']) ? htmlspecialchars($filters['date_from']) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?= _l("date_to"); ?></label>
                                            <input type="date" name="date_to" class="form-control" value="<?= isset($filters['date_to']) ? htmlspecialchars($filters['date_to']) : ''; ?>">
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
                                        <a href="<?= admin_url("contactcenter/meta_analytics/{$meta_analytics_id}"); ?>" class="btn btn-default">
                                            <i class="fa fa-times"></i> <?= _l("clear_filters"); ?>
                                        </a>
                                    </div>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="chart_meta_dia" class="animated fadeIn chart_meta"></canvas>
                            </div>
                            <div class="col-md-6">
                                <canvas id="chart_meta_status" class="animated fadeIn chart_meta"></canvas>
                            </div>
                        </div>

                        <div class="row mtop15">
                            <div class="col-md-6 content-card-meta">
                                <div class="card  mb-3 card-source-meta meta-instagram col-md-3" style="max-width: 18rem;" onclick="filterByStatus('instagram')">
                                    <div class="card-header">
                                        <i class="fa-brands fa-instagram"></i>
                                        instagram
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= count_source_leads_meta($meta_analytics_id, "instagram"); ?></h5>
                                    </div>
                                </div>
                                <div class="card mb-3 card-source-meta meta-facebook col-md-3" style="max-width: 18rem;" onclick="filterByStatus('facebook')">
                                    <div class="card-header">
                                        <i class="fa-brands fa-facebook"></i>
                                        facebook
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= count_source_leads_meta($meta_analytics_id, "facebook"); ?></h5>
                                    </div>
                                </div>
                                <div class="card  mb-3 card-source-meta meta-whatsapp col-md-3" style="max-width: 18rem;" onclick="filterByStatus('whatsapp')">
                                    <div class="card-header">
                                        <i class="fa-brands fa-whatsapp"></i>
                                        whatsapp
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= count_source_leads_meta($meta_analytics_id, "whatsapp"); ?></h5>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 content-card-meta">
                                <div class="card  mb-3 card-source-meta meta-android col-md-3" style="max-width: 18rem;" onclick="filterByStatus('android')">
                                    <div class="card-header">
                                    <i class="fa-brands fa-android"></i>
                                        Android
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= count_source_leads_app($meta_analytics_id, "android"); ?></h5>
                                    </div>
                                </div>
                                <div class="card mb-3 card-source-meta meta-ios col-md-3" style="max-width: 18rem;" onclick="filterByStatus('ios')">
                                    <div class="card-header">
                                        <i class="fa-brands fa-apple"></i>
                                        IOS
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= count_source_leads_app($meta_analytics_id, "ios"); ?></h5>
                                    </div>
                                </div>
                                <div class="card  mb-3 card-source-meta meta-web col-md-3" style="max-width: 18rem;" onclick="filterByStatus('web')">
                                    <div class="card-header">
                                    <i class="fa-solid fa-globe"></i>
                                        web
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= count_source_leads_app($meta_analytics_id, "web"); ?></h5>
                                    </div>
                                </div>
                            </div>
                            
                        </div>



                        <hr class="hr-panel-separator" />
                        <div class="clearfix"></div>
                        <div>
                            <table id="meta" class="table ">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th><?= _l("leads_dt_name"); ?></th>
                                        <th><?= _l("leads_dt_phonenumber"); ?></th>
                                        <th><?= _l("leads_dt_assigned"); ?></th>
                                        <th><?= _l("leads_dt_status"); ?></th>
                                        <th><?= _l("lead_sources"); ?></th>
                                        <th><?= _l("links_dispositivos"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($meta_analytics as $lead) {
                                        $status = get_status_leads_meta($lead->status);
                                    ?>
                                        <tr>
                                            <td><a href="<?= admin_url('leads/index/' . $lead->id) ?>" onclick="init_lead(<?= $lead->id ?>);return false;"><?= $lead->id ?></a></td>
                                            <td><a href="<?= admin_url('leads/index/' . $lead->id) ?>" onclick="init_lead(<?= $lead->id ?>);return false;"><?= $lead->name ?></a></td>
                                            <td><?= $lead->phonenumber ?></td>
                                            <td><?= get_staff_full_name($lead->assigned); ?></td>
                                            <td><?= $status->name; ?> </td>
                                            <td><?= ($lead->entryPointConversionApp ? $lead->entryPointConversionApp : '---'); ?> </td>
                                            <td><?= ($lead->meta_source ? $lead->meta_source : '---'); ?> </td>
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

$chartStatusLabel = json_encode(array_column($chart_meta_status, 'name'));
$chartStatusData = json_encode(array_column($chart_meta_status, 'total'));
$chartStatusColor = json_encode(array_column($chart_meta_status, 'color'));

$chart_labels = json_encode(array_column($chart_meta_dia, 'date'));
$chart_data = json_encode(array_column($chart_meta_dia, 'quantity'));

?>

<?php init_tail(); ?>
<script>
    $(document).ready(function() {
        // Initialize selectpicker on filter dropdowns
        $('.selectpicker').selectpicker();
    });
</script>
<script>
    appDataTableInline("#meta", {
        supportsButtons: true,
        supportsLoading: true,
        autoWidth: false,
        order: [
            [0, 'desc']
        ]
    });

    function filterByStatus(status) {
        $('#meta').DataTable().search(status).draw();
    }

    $(document).ready(function() {
        var labels = <?php echo $chart_labels; ?>;
        var data = <?php echo $chart_data; ?>;

        var ctx = document.getElementById("chart_meta_dia").getContext('2d');
        var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
        gradientStroke1.addColorStop(0, '#00e09b');
        gradientStroke1.addColorStop(1, '#17c5ea');

        const mixedChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Registros diário',
                    data: data,
                    backgroundColor: gradientStroke1,
                    borderColor: gradientStroke1,
                    borderWidth: 2,
                    pointRadius: 2,
                    fill: false,
                    tension: 0.5,
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                legend: {
                    display: false,
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        });

        var ctxStatus = document.getElementById("chart_meta_status").getContext('2d');
        var labelsStatus = <?php echo $chartStatusLabel; ?>;
        var dataStatus = <?php echo $chartStatusData; ?>;
        var dataColor = <?php echo $chartStatusColor; ?>;
        const ChartStatus = new Chart(ctxStatus, {
            type: 'bar',
            data: {
                labels: labelsStatus,
                datasets: [{
                    label: 'Status',
                    data: dataStatus,
                    backgroundColor: dataColor,
                    borderColor: dataColor,
                    borderWidth: 2,
                    pointRadius: 2,
                    fill: false,
                    tension: 0.5,
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                legend: {
                    display: false,
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        });
    });
</script>
</body>

</html>