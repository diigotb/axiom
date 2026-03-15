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
                                <i class="fa-brands fa-facebook"></i>
                                <i class="fa-brands fa-instagram"></i>
                                <?php echo _l('contactcenter_meta'); ?>
                            </span>
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
                                <?php echo form_open(admin_url('contactcenter/meta'), ['method' => 'get', 'id' => 'meta-filters-form']); ?>
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
                                            <label><?= _l("lead_sources"); ?></label>
                                            <select name="source" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""><?= _l("all"); ?></option>
                                                <?php if (isset($sources) && !empty($sources)) { ?>
                                                    <?php foreach ($sources as $source) { ?>
                                                        <option value="<?php echo $source['id']; ?>" <?php echo (isset($filters['source']) && $filters['source'] == $source['id']) ? 'selected' : ''; ?>>
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
                                        <a href="<?= admin_url('contactcenter/meta'); ?>" class="btn btn-default">
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
                                if ($meta_analytics) {


                                    foreach ($meta_analytics as  $meta) {
                                        if ($meta->thumbnailUrl) {
                                            $thumb = site_url("/{$meta->thumbnailUrl}");
                                        } else {
                                            $thumb = site_url("modules/contactcenter/assets/image/img-not.jpeg");
                                        }

                                ?>

                                        <div class="meta_card col-md-3">
                                            <div> 
                                                <a href="<?= ($meta->sourceUrl ? $meta->sourceUrl : "#"); ?>"  <?= ($meta->sourceUrl ? "target='_blank'" : ""); ?> >
                                                    <img class="card-img-top" src="<?= $thumb; ?>" alt="Card image cap">
                                                </a>
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= $meta->title; ?></h5>
                                                    <p class="card-text"><?= substr($meta->body, 0, 150) . '...'; ?></p>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <?php if ($meta->sourceUrl) { ?>
                                                    <a href="<?= $meta->sourceUrl; ?>" target="_blank" class="btn btn-primary"><i class="fa-solid fa-bullhorn"></i> POST</a>
                                                <?php } ?>
                                                <?php 
                                                // Build URL with filters
                                                $analytics_url = admin_url("contactcenter/meta_analytics/{$meta->sourceId}");
                                                $filter_params = [];
                                                if (!empty($filters['date_from'])) $filter_params['date_from'] = $filters['date_from'];
                                                if (!empty($filters['date_to'])) $filter_params['date_to'] = $filters['date_to'];
                                                if (!empty($filters['status'])) $filter_params['status'] = $filters['status'];
                                                if (!empty($filters['source'])) $filter_params['source'] = $filters['source'];
                                                if (!empty($filters['assigned'])) $filter_params['assigned'] = $filters['assigned'];
                                                if (!empty($filter_params)) {
                                                    $analytics_url .= '?' . http_build_query($filter_params);
                                                }
                                                ?>
                                                <a href="<?= $analytics_url; ?>" class="btn btn-primary"><?= isset($meta->filtered_leads) ? $meta->filtered_leads : conta_meta_por_id($meta->sourceId); ?> Leads</a>
                                            </div>
                                        </div>

                                <?php }
                                } else {
                                    echo "<h4 class='text-center'>" . _l("contactcenter_no_meta") . "</h4>";
                                } ?>

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
    });
</script>
</body>

</html>