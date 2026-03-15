<div role="tabpanel" class="tab-pane" id="lead_ai_interactions">
    <div class="panel-body">
        <?php if (isset($ai_interactions) && count($ai_interactions) > 0) { ?>
        <div class="ai-interactions-list">
            <?php foreach ($ai_interactions as $interaction) { ?>
            <div class="ai-interaction-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #e0e0e0; border-radius: 4px; background-color: #ffffff;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="ai-interaction-header" style="margin-bottom: 10px;">
                            <span class="badge badge-<?php 
                                echo $interaction->interaction_type == 'message' ? 'info' : 
                                    ($interaction->interaction_type == 'function_call' ? 'warning' : 
                                    ($interaction->interaction_type == 'function_response' ? 'success' : 
                                    ($interaction->interaction_type == 'error' ? 'danger' : 'default'))); 
                            ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $interaction->interaction_type)); ?>
                            </span>
                            <?php if ($interaction->function_name) { ?>
                            <span class="badge badge-secondary">
                                <?php echo $interaction->function_name; ?>
                            </span>
                            <?php } ?>
                            <?php if ($interaction->status) { ?>
                            <span class="badge badge-light">
                                <?php echo ucfirst($interaction->status); ?>
                            </span>
                            <?php } ?>
                            <small class="text-muted pull-right">
                                <?php echo _dt($interaction->created_at); ?>
                            </small>
                        </div>
                        
                        <?php if ($interaction->user_message) { ?>
                        <div class="ai-interaction-user-message" style="margin-bottom: 10px; padding: 10px; background-color: #f5f5f5; border-left: 3px solid #007bff; border-radius: 4px;">
                            <strong style="color: #333;"><?php echo _l('lead_ai_interactions_user_message'); ?></strong>
                            <div style="margin-top: 5px; color: #333;"><?php echo nl2br(htmlspecialchars($interaction->user_message)); ?></div>
                        </div>
                        <?php } ?>
                        
                        <?php if ($interaction->ai_response) { ?>
                        <div class="ai-interaction-ai-response" style="margin-bottom: 10px; padding: 10px; background-color: #e7f3ff; border-left: 3px solid #17a2b8; border-radius: 4px;">
                            <strong style="color: #333;"><?php echo _l('lead_ai_interactions_ai_response'); ?></strong>
                            <div style="margin-top: 5px; color: #333;"><?php echo nl2br(htmlspecialchars($interaction->ai_response)); ?></div>
                        </div>
                        <?php } ?>
                        
                        <?php if ($interaction->function_arguments) { ?>
                        <div class="ai-interaction-function-args" style="margin-bottom: 10px;">
                            <strong style="color: #333;"><?php echo _l('lead_ai_interactions_function_arguments'); ?></strong>
                            <pre style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; max-height: 200px; overflow-y: auto; font-size: 12px; color: #333; border: 1px solid #dee2e6;"><?php 
                                $args = json_decode($interaction->function_arguments, true);
                                echo htmlspecialchars(json_encode($args, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); 
                            ?></pre>
                        </div>
                        <?php } ?>
                        
                        <?php if ($interaction->function_result) { ?>
                        <div class="ai-interaction-function-result" style="margin-bottom: 10px;">
                            <strong style="color: #333;"><?php echo _l('lead_ai_interactions_function_result'); ?></strong>
                            <pre style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; max-height: 200px; overflow-y: auto; font-size: 12px; color: #333; border: 1px solid #dee2e6;"><?php 
                                $result = json_decode($interaction->function_result, true);
                                if ($result !== null) {
                                    echo htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); 
                                } else {
                                    echo htmlspecialchars($interaction->function_result);
                                }
                            ?></pre>
                        </div>
                        <?php } ?>
                        
                        <?php if ($interaction->raw_input) { ?>
                        <div class="ai-interaction-raw-input" style="margin-bottom: 10px;">
                            <a href="#" class="toggle-raw-data" data-target="raw-input-<?php echo $interaction->id; ?>" style="color: #007bff; text-decoration: none;">
                                <i class="fa fa-chevron-down"></i> <strong><?php echo _l('lead_ai_interactions_raw_input'); ?></strong>
                            </a>
                            <pre id="raw-input-<?php echo $interaction->id; ?>" style="display: none; background-color: #f8f9fa; padding: 10px; border-radius: 4px; max-height: 300px; overflow-y: auto; font-size: 11px; margin-top: 5px; color: #333; border: 1px solid #dee2e6;"><?php 
                                $raw = json_decode($interaction->raw_input, true);
                                if ($raw !== null) {
                                    echo htmlspecialchars(json_encode($raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); 
                                } else {
                                    echo htmlspecialchars($interaction->raw_input);
                                }
                            ?></pre>
                        </div>
                        <?php } ?>
                        
                        <?php if ($interaction->raw_output) { ?>
                        <div class="ai-interaction-raw-output" style="margin-bottom: 10px;">
                            <a href="#" class="toggle-raw-data" data-target="raw-output-<?php echo $interaction->id; ?>" style="color: #007bff; text-decoration: none;">
                                <i class="fa fa-chevron-down"></i> <strong><?php echo _l('lead_ai_interactions_raw_output'); ?></strong>
                            </a>
                            <pre id="raw-output-<?php echo $interaction->id; ?>" style="display: none; background-color: #f8f9fa; padding: 10px; border-radius: 4px; max-height: 300px; overflow-y: auto; font-size: 11px; margin-top: 5px; color: #333; border: 1px solid #dee2e6;"><?php 
                                $raw = json_decode($interaction->raw_output, true);
                                if ($raw !== null) {
                                    echo htmlspecialchars(json_encode($raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); 
                                } else {
                                    echo htmlspecialchars($interaction->raw_output);
                                }
                            ?></pre>
                        </div>
                        <?php } ?>
                        
                        <?php if ($interaction->error_message) { ?>
                        <div class="ai-interaction-error" style="margin-bottom: 10px; padding: 10px; background-color: #f8d7da; border-left: 3px solid #dc3545;">
                            <strong><?php echo _l('lead_ai_interactions_error'); ?></strong>
                            <div style="margin-top: 5px; color: #721c24;"><?php echo nl2br(htmlspecialchars($interaction->error_message)); ?></div>
                        </div>
                        <?php } ?>
                        
                        <div class="ai-interaction-meta" style="margin-top: 10px; font-size: 11px; color: #6c757d;">
                            <?php if ($interaction->thread_id) { ?>
                            <span><?php echo _l('lead_ai_interactions_thread'); ?> <?php echo htmlspecialchars($interaction->thread_id); ?></span>
                            <?php } ?>
                            <?php if ($interaction->run_id) { ?>
                            <span style="margin-left: 10px;"><?php echo _l('lead_ai_interactions_run'); ?> <?php echo htmlspecialchars($interaction->run_id); ?></span>
                            <?php } ?>
                            <?php if ($interaction->device_id) { ?>
                            <span style="margin-left: 10px;"><?php echo _l('lead_ai_interactions_device'); ?> <?php echo $interaction->device_id; ?></span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } else { ?>
        <div class="text-center" style="padding: 40px;">
            <i class="fa fa-robot fa-3x" style="color: #ccc; margin-bottom: 20px;"></i>
            <h4 style="color: #999;"><?php echo _l('lead_ai_interactions_no_data'); ?></h4>
            <p style="color: #999;"><?php echo _l('lead_ai_interactions_no_data_desc'); ?></p>
            <?php if (isset($ai_interactions_table_exists) && !$ai_interactions_table_exists) { ?>
            <p style="color: #ff9800; margin-top: 20px;">
                <i class="fa fa-info-circle"></i> <?php echo _l('lead_ai_interactions_table_missing'); ?>
            </p>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle raw data visibility
    $(document).on('click', '.toggle-raw-data', function(e) {
        e.preventDefault();
        var target = $(this).data('target');
        var $target = $('#' + target);
        var $icon = $(this).find('i');
        
        if ($target.is(':visible')) {
            $target.slideUp();
            $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            $target.slideDown();
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    });
});
</script>
