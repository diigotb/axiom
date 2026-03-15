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
                                <i class="fa fa-<?= !empty($creative) ? 'edit' : 'plus'; ?>"></i>
                                <?= !empty($creative) ? _l('ads_analytics_edit_creative') : _l('ads_analytics_add_creative'); ?>
                            </span>
                            <a href="<?= admin_url('contactcenter/ads_analytics'); ?>" class="btn btn-default pull-right">
                                <i class="fa fa-arrow-left"></i> <?php echo _l('ads_analytics_back_to_ads_analytics'); ?>
                            </a>
                        </h4>
                        <hr class="hr-panel-separator" />
                        
                        <?php echo form_open(admin_url('contactcenter/manage_creative' . (!empty($creative) ? '/' . $creative->id : '')), ['id' => 'manage-creative-form', 'enctype' => 'multipart/form-data']); ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name"><?php echo _l('ads_analytics_creative_name'); ?> <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control" value="<?= !empty($creative) ? htmlspecialchars($creative->name) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="campaign_name"><?php echo _l('ads_analytics_campaign'); ?> <span class="text-danger">*</span></label>
                                    <select id="campaign_name" name="campaign_name" class="selectpicker form-control" data-width="100%" data-live-search="true" required>
                                        <option value=""><?php echo _l('ads_analytics_select_campaign'); ?></option>
                                        <?php if (isset($campaigns) && !empty($campaigns)) { ?>
                                            <?php foreach ($campaigns as $campaign) { ?>
                                                <option value="<?= htmlspecialchars($campaign); ?>" <?= (!empty($creative) && $creative->campaign_name == $campaign) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($campaign); ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><?php echo _l('ads_analytics_media'); ?> <span class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select id="media_id" name="media_id" class="selectpicker form-control" data-width="100%" data-live-search="true" required>
                                                <option value=""><?php echo _l('ads_analytics_select_or_upload_media'); ?></option>
                                                <?php if (isset($media) && !empty($media)) { ?>
                                                    <?php foreach ($media as $m) { ?>
                                                        <option value="<?= $m->id; ?>" data-type="<?= $m->file_type; ?>" data-path="<?= base_url($m->file_path); ?>" data-thumb="<?= base_url($m->thumbnail_path ? $m->thumbnail_path : $m->file_path); ?>" <?= (!empty($creative) && $creative->media_id == $m->id) ? 'selected' : ''; ?>>
                                                            <?= htmlspecialchars($m->file_name); ?> (<?= strtoupper($m->file_type); ?>)
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#uploadMediaModal">
                                                <i class="fa fa-upload"></i> <?php echo _l('ads_analytics_upload_new_media'); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="media-preview" style="margin-top: 15px; display: none; clear: both;">
                                        <div style="position: relative; max-width: 100%; width: 100%; border: 1px solid #ddd; padding: 10px; background: #f9f9f9; border-radius: 4px; overflow: hidden;">
                                            <div style="position: relative; width: 100%; max-width: 500px; margin: 0 auto;">
                                                <div style="position: relative; width: 100%; padding-bottom: 56.25%; background: #000;">
                                                    <img id="preview-image" src="" alt="Preview" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; display: none;">
                                                    <video id="preview-video" src="" controls style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; display: none;"></video>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ad_set_id"><?php echo _l('ads_analytics_ad_set'); ?> (<?php echo _l('optional'); ?>)</label>
                                    <select id="ad_set_id" name="ad_set_id" class="selectpicker form-control" data-width="100%" data-live-search="true">
                                        <option value=""><?php echo _l('none'); ?> (<?php echo _l('ads_analytics_individual_budget'); ?>)</option>
                                        <?php if (isset($ad_sets) && !empty($ad_sets)) { ?>
                                            <?php foreach ($ad_sets as $ad_set) { ?>
                                                <option value="<?= $ad_set->id; ?>" <?= (!empty($creative) && $creative->ad_set_id == $ad_set->id) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($ad_set->name); ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <small class="form-text text-muted"><?php echo _l('ads_analytics_ad_set_help'); ?></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="is_active"><?php echo _l('ads_analytics_status'); ?></label>
                                    <select id="is_active" name="is_active" class="form-control">
                                        <option value="1" <?= (empty($creative) || $creative->is_active == 1) ? 'selected' : ''; ?>><?php echo _l('ads_analytics_active'); ?></option>
                                        <option value="0" <?= (!empty($creative) && $creative->is_active == 0) ? 'selected' : ''; ?>><?php echo _l('ads_analytics_inactive'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5><?php echo _l('ads_analytics_investment_settings'); ?></h5>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="budget_type"><?php echo _l('ads_analytics_budget_type'); ?></label>
                                    <select id="budget_type" name="budget_type" class="form-control">
                                        <option value="ad_set" <?= (empty($investment) || (isset($investment->budget_type) && $investment->budget_type == 'ad_set')) ? 'selected' : ''; ?>>
                                            <?php echo _l('ads_analytics_budget_type_ad_set'); ?> - <?php echo _l('ads_analytics_shared_budget'); ?>
                                        </option>
                                        <option value="creative" <?= (!empty($investment) && isset($investment->budget_type) && $investment->budget_type == 'creative') ? 'selected' : ''; ?>>
                                            <?php echo _l('ads_analytics_budget_type_creative'); ?> - <?php echo _l('ads_analytics_per_creative'); ?>
                                        </option>
                                    </select>
                                    <small class="form-text text-muted" id="budget_type_help">
                                        <?php 
                                        if (empty($investment) || (isset($investment->budget_type) && $investment->budget_type == 'ad_set')) {
                                            echo _l('ads_analytics_budget_type_help_ad_set');
                                        } else {
                                            echo _l('ads_analytics_budget_type_help_creative');
                                        }
                                        ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        
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
                        
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> <?php echo _l('ads_analytics_save_creative'); ?>
                                </button>
                                <a href="<?= admin_url('contactcenter/ads_analytics'); ?>" class="btn btn-default">
                                    <i class="fa fa-times"></i> <?php echo _l('ads_analytics_cancel'); ?>
                                </a>
                            </div>
                        </div>
                        
                        <?php if (!empty($creative)) { ?>
                            <input type="hidden" name="id" value="<?= $creative->id; ?>">
                        <?php } ?>
                        
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Media Modal -->
<div class="modal fade" id="uploadMediaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo _l('ads_analytics_upload_media'); ?></h4>
            </div>
            <div class="modal-body">
                <form id="upload-media-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="media_file"><?php echo _l('ads_analytics_select_file'); ?></label>
                        <input type="file" id="media_file" name="media_file" class="form-control" accept="image/*,video/*" required>
                        <small class="form-text text-muted"><?php echo _l('ads_analytics_upload_max_size'); ?></small>
                    </div>
                    <div id="upload-progress" style="display: none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 100%">
                                <?php echo _l('ads_analytics_uploading'); ?>
                            </div>
                        </div>
                    </div>
                    <div id="upload-result"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('ads_analytics_close'); ?></button>
                <button type="button" class="btn btn-primary" id="upload-btn"><?php echo _l('ads_analytics_upload'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(document).ready(function() {
        // Initialize selectpicker
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
        
        // Handle budget type change
        $('#budget_type').on('change', function() {
            var budgetType = $(this).val();
            var adSetId = $('#ad_set_id').val();
            
            if (budgetType == 'ad_set') {
                $('#budget_type_help').text('<?php echo _l('ads_analytics_budget_type_help_ad_set'); ?>');
                if (!adSetId) {
                    alert('<?php echo _l('ads_analytics_ad_set_required_for_budget'); ?>');
                    // Don't change the budget type, just warn - user should select an ad set
                }
            } else {
                $('#budget_type_help').text('<?php echo _l('ads_analytics_budget_type_help_creative'); ?>');
            }
        });
        
        // Handle ad set change
        $('#ad_set_id').on('change', function() {
            var adSetId = $(this).val();
            var budgetType = $('#budget_type').val();
            
            if (budgetType == 'ad_set' && !adSetId) {
                // Warn but don't force change - user might want to set ad set first
                $('#budget_type_help').html('<?php echo _l('ads_analytics_budget_type_help_ad_set'); ?><br><small class="text-warning"><?php echo _l('ads_analytics_ad_set_required_for_budget'); ?></small>');
            } else if (budgetType == 'ad_set' && adSetId) {
                $('#budget_type_help').text('<?php echo _l('ads_analytics_budget_type_help_ad_set'); ?>');
            }
        });
        
        // Initialize help text on page load
        var initialBudgetType = $('#budget_type').val();
        var initialAdSetId = $('#ad_set_id').val();
        if (initialBudgetType == 'ad_set') {
            if (!initialAdSetId) {
                $('#budget_type_help').html('<?php echo _l('ads_analytics_budget_type_help_ad_set'); ?><br><small class="text-warning"><?php echo _l('ads_analytics_ad_set_required_for_budget'); ?></small>');
            } else {
                $('#budget_type_help').text('<?php echo _l('ads_analytics_budget_type_help_ad_set'); ?>');
            }
        }
        
        // Media preview on select
        $('#media_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var type = selectedOption.data('type');
            var path = selectedOption.data('path');
            var thumb = selectedOption.data('thumb');
            
            if (type && path) {
                $('#media-preview').show();
                if (type === 'image') {
                    $('#preview-image').attr('src', thumb || path).show();
                    $('#preview-video').hide();
                } else if (type === 'video') {
                    $('#preview-video').attr('src', path).show();
                    $('#preview-image').hide();
                }
            } else {
                $('#media-preview').hide();
            }
        });
        
        // Trigger preview if media is already selected
        $('#media_id').trigger('change');
        
        // Upload media
        $('#upload-btn').on('click', function() {
            var formData = new FormData($('#upload-media-form')[0]);
            
            // Add CSRF token
            formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');
            
            $('#upload-progress').show();
            $('#upload-result').html('');
            $('#upload-btn').prop('disabled', true);
            
            $.ajax({
                url: '<?= admin_url('contactcenter/upload_ads_media'); ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    $('#upload-progress').hide();
                    $('#upload-btn').prop('disabled', false);
                    
                    if (response && response.success) {
                        $('#upload-result').html('<div class="alert alert-success">' + response.message + '</div>');
                        // Reload page after 1 second to refresh media list
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        var errorMsg = response && response.message ? response.message : '<?php echo _l('ads_analytics_error_uploading'); ?>';
                        $('#upload-result').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#upload-progress').hide();
                    $('#upload-btn').prop('disabled', false);
                    
                    var errorMsg = '<?php echo _l('ads_analytics_error_uploading'); ?>';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch(e) {
                            // Keep default error message
                        }
                    }
                    $('#upload-result').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            });
        });
    });
</script>
</body>
</html>
