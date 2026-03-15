<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-text-lg tw-text-neutral-700"><?php echo _l('leadfinder_title'); ?></h4>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-mb-6">
                            <?php echo $info; ?>
                        </div>

                        <?php echo form_open(admin_url('contactcenter/leadfinder/index'), ['method' => 'post', 'id' => 'verify-form-search']); ?>
                        <div class="row">
                            <?php echo render_input('category', 'leadfinder_category', (isset($params['category']) ? $params['category'] : ''), '', ['required' => true], '', 'col-md-3'); ?>
                            <?php echo render_input('city', 'lead_city', (isset($params['city']) ? $params['city'] : ''), '', ['required' => true], '', 'col-md-3'); ?>
                            <?php echo render_input('region', 'leadfinder_region', (isset($params['region']) ? $params['region'] : ''), '', ['required' => true], '', 'col-md-3'); ?>
                            <div class="form-group col-md-3">
                                <label for="lastname"><?php echo _l('clients_country'); ?></label>
                                <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                    data-live-search="true" name="country" class="form-control selectpicker" id="country">
                                    <option value=""></option>
                                    <?php foreach (get_all_countries() as $country) { ?>
                                        <?php
                                             $selected = ($country['iso2'] == $params['country']) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $country['iso2']; ?>" <?php echo $selected; ?>  >
                                            <?php echo $country['short_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" data-form="#verify-form-search" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-primary"><?php echo _l('leadfinder_search'); ?></button>
                        <?php echo form_close(); ?>
                        <?php if (isset($error)) { ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php } ?>

                        <?php if (isset($results) && !isset($results['error'])) { ?>
                            <hr />
                            <?php echo form_open(admin_url('contactcenter/leadfinder/import'), ['id' => 'import']); ?>

                            <div class="row">
                                <div class="col-md-4">
                                    <?php
                                    echo render_leads_status_select($statuses, (isset($params['status']) ? $params['status'] : get_option('leads_default_status')), _l('lead_import_status'), 'status', ["required" => true]);
                                    ?>
                                </div>
                                <div class="col-md-4">
                                    <?php
                                    echo render_leads_source_select($sources, (isset($params['source']) ? $params['source'] : get_option('leads_default_source')), _l('lead_import_source'), 'source', ["required" => true]);
                                    ?>
                                </div>
                                <div class="col-md-4">
                                    <?php
                                    echo render_select('staffid', $members, ['staffid', ['firstname', 'lastname']], 'leads_import_assignee', get_staff_user_id(), ["required" => true]);
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th><?php echo _l('lead_company'); ?></th>
                                            <th><?php echo _l('leads_dt_name'); ?></th>
                                            <th><?php echo _l('leads_dt_phonenumber'); ?></th>
                                            <th><?php echo _l('lead_address'); ?></th>
                                            <th><?php echo _l('lead_city'); ?></th>
                                            <th><?php echo _l('lead_website'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($results['name'])) {
                                            $results = [$results];
                                        }
                                        foreach ($results as $idx => $row) { ?>
                                            <tr>
                                                <td><input type="checkbox" name="selected[]" value="<?php echo $idx; ?>" checked></td>
                                                <td><?php echo $row['company']; ?></td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['phone']; ?></td>
                                                <td><?php echo $row['address']; ?></td>
                                                <td><?php echo $row['city']; ?></td>
                                                <td><?php echo $row['website']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="results_json" value='<?php echo json_encode($results); ?>'>
                            <button type="submit" data-form="#import" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-success"><?php echo _l('leadfinder_import'); ?></button>
                            <?php echo form_close(); ?>
                        <?php } ?>




                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
    appValidateForm($('#verify-form-search'), {
        purchase_key: 'required'
    });
    appValidateForm($('#import'), {
        purchase_key: 'required'
    });
</script>