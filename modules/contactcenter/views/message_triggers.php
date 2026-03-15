<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <i class="fa fa-bolt tw-mr-2"></i>
                            <span><?php echo _l('message_triggers'); ?></span>
                        </h4>
                        <hr class="hr-panel-separator" />
                        <div class="tw-mb-2 sm:tw-mb-4">
                            <div class="_buttons">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#triggerModal" onclick="openTriggerModal()">
                                    <i class="fa-regular fa-plus tw-mr-1"></i>
                                    <?php echo _l('add_message_trigger'); ?>
                                </button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table dt-table table-message-triggers" data-order-col="1" data-order-type="asc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('name'); ?></th>
                                        <th><?php echo _l('trigger_type'); ?></th>
                                        <th><?php echo _l('message_sender_type'); ?></th>
                                        <th><?php echo _l('trigger_words'); ?></th>
                                        <th><?php echo _l('status'); ?></th>
                                        <th><?php echo _l('date_created'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($triggers as $trigger) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($trigger->name); ?></td>
                                            <td>
                                                <?php if ($trigger->trigger_type == 'first_message') { ?>
                                                    <span class="label label-info"><?php echo _l('first_message'); ?></span>
                                                <?php } else { ?>
                                                    <span class="label label-warning"><?php echo _l('safe_word'); ?></span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $sender_type = isset($trigger->message_sender_type) ? $trigger->message_sender_type : 'contact';
                                                if ($sender_type == 'contact') {
                                                    echo '<span class="label label-primary">' . _l('message_from_contact') . '</span>';
                                                } elseif ($sender_type == 'staff') {
                                                    echo '<span class="label label-success">' . _l('message_from_staff') . '</span>';
                                                } else {
                                                    echo '<span class="label label-default">' . _l('message_from_both') . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($trigger->trigger_words); ?></td>
                                            <td>
                                                <?php if ($trigger->is_active) { ?>
                                                    <span class="label label-success"><?php echo _l('active'); ?></span>
                                                <?php } else { ?>
                                                    <span class="label label-default"><?php echo _l('inactive'); ?></span>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo _d($trigger->datecreated); ?></td>
                                            <td>
                                                <a href="#" class="btn btn-default btn-icon" onclick="editTrigger(<?php echo $trigger->id; ?>); return false;" data-toggle="tooltip" title="<?php echo _l('edit'); ?>">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                </a>
                                                <a href="<?php echo admin_url('contactcenter/toggle_message_trigger/' . $trigger->id); ?>" class="btn btn-default btn-icon" data-toggle="tooltip" title="<?php echo $trigger->is_active ? _l('deactivate') : _l('activate'); ?>">
                                                    <i class="fa fa-<?php echo $trigger->is_active ? 'pause' : 'play'; ?>"></i>
                                                </a>
                                                <a href="<?php echo admin_url('contactcenter/delete_message_trigger/' . $trigger->id); ?>" class="btn btn-danger btn-icon _delete" data-toggle="tooltip" title="<?php echo _l('delete'); ?>">
                                                    <i class="fa fa-remove"></i>
                                                </a>
                                            </td>
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

<!-- Trigger Modal -->
<div class="modal fade" id="triggerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="triggerModalTitle"><?php echo _l('add_message_trigger'); ?></h4>
            </div>
            <?php echo form_open(admin_url('contactcenter/add_message_trigger'), array('id' => 'triggerForm')); ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="trigger_id">
                    
                    <div class="form-group">
                        <label for="name"><?php echo _l('name'); ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="trigger_type"><?php echo _l('trigger_type'); ?> <span class="text-danger">*</span></label>
                        <select class="form-control selectpicker" name="trigger_type" id="trigger_type" required>
                            <option value="first_message"><?php echo _l('first_message'); ?></option>
                            <option value="safe_word"><?php echo _l('safe_word'); ?></option>
                        </select>
                        <small class="text-muted"><?php echo _l('trigger_type_help'); ?></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="message_sender_type"><?php echo _l('message_sender_type'); ?> <span class="text-danger">*</span></label>
                        <select class="form-control selectpicker" name="message_sender_type" id="message_sender_type" required>
                            <option value="contact"><?php echo _l('message_from_contact'); ?></option>
                            <option value="staff"><?php echo _l('message_from_staff'); ?></option>
                            <option value="both"><?php echo _l('message_from_both'); ?></option>
                        </select>
                        <small class="text-muted"><?php echo _l('message_sender_type_help'); ?></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="trigger_words"><?php echo _l('trigger_words'); ?> <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="trigger_words" id="trigger_words" rows="3" placeholder="<?php echo _l('trigger_words_placeholder'); ?>" required></textarea>
                        <small class="text-muted"><?php echo _l('trigger_words_help'); ?></small>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox">
                            <input type="checkbox" name="case_sensitive" id="case_sensitive" value="1">
                            <label for="case_sensitive"><?php echo _l('case_sensitive'); ?></label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                            <label for="is_active"><?php echo _l('active'); ?></label>
                        </div>
                    </div>
                    
                    <hr>
                    <h5><?php echo _l('actions'); ?></h5>
                    
                    <div class="form-group">
                        <label for="action_add_tag"><?php echo _l('add_tags'); ?></label>
                        <div class="input-group">
                            <select class="form-control selectpicker" name="action_add_tag[]" id="action_add_tag" multiple data-live-search="true">
                                <?php foreach ($tags as $tag) { ?>
                                    <option value="<?php echo is_object($tag) ? $tag->id : $tag['id']; ?>"><?php echo htmlspecialchars(is_object($tag) ? $tag->name : $tag['name']); ?></option>
                                <?php } ?>
                            </select>
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createTagModal" title="<?php echo _l('create_new_tag'); ?>">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted"><?php echo _l('tags_help_text'); ?></small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="action_change_status"><?php echo _l('change_status'); ?></label>
                                <select class="form-control selectpicker" name="action_change_status" id="action_change_status">
                                    <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                    <?php foreach ($lead_statuses as $status) { ?>
                                        <option value="<?php echo $status['id']; ?>"><?php echo htmlspecialchars($status['name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="action_change_source"><?php echo _l('change_source'); ?></label>
                                <select class="form-control selectpicker" name="action_change_source" id="action_change_source">
                                    <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                    <?php foreach ($lead_sources as $source) { ?>
                                        <option value="<?php echo $source['id']; ?>"><?php echo htmlspecialchars($source['name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="action_update_field"><?php echo _l('update_field'); ?></label>
                                <select class="form-control" name="action_update_field" id="action_update_field">
                                    <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                    <option value="title"><?php echo _l('title'); ?></option>
                                    <option value="email"><?php echo _l('email'); ?></option>
                                    <option value="phonenumber"><?php echo _l('phonenumber'); ?></option>
                                    <option value="company"><?php echo _l('company'); ?></option>
                                    <option value="address"><?php echo _l('address'); ?></option>
                                    <option value="city"><?php echo _l('city'); ?></option>
                                    <option value="state"><?php echo _l('state'); ?></option>
                                    <option value="zip"><?php echo _l('zip'); ?></option>
                                    <option value="country"><?php echo _l('country'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="action_update_field_value"><?php echo _l('field_value'); ?></label>
                                <input type="text" class="form-control" name="action_update_field_value" id="action_update_field_value">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="action_update_custom_field"><?php echo _l('update_custom_field'); ?></label>
                                <select class="form-control selectpicker" name="action_update_custom_field" id="action_update_custom_field">
                                    <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                    <?php foreach ($custom_fields as $field) { ?>
                                        <option value="<?php echo $field['id']; ?>"><?php echo htmlspecialchars($field['name']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="action_update_custom_field_value"><?php echo _l('custom_field_value'); ?></label>
                                <input type="text" class="form-control" name="action_update_custom_field_value" id="action_update_custom_field_value">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="action_update_description"><?php echo _l('append_description'); ?></label>
                        <textarea class="form-control" name="action_update_description" id="action_update_description" rows="3"></textarea>
                        <small class="text-muted"><?php echo _l('append_description_help'); ?></small>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox">
                            <input type="checkbox" name="action_disable_ai" id="action_disable_ai" value="1">
                            <label for="action_disable_ai"><?php echo _l('disable_ai'); ?></label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="action_change_owner"><?php echo _l('change_owner'); ?></label>
                        <select class="form-control selectpicker" name="action_change_owner" id="action_change_owner" data-live-search="true">
                            <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                            <?php foreach ($staff_members as $staff) { ?>
                                <option value="<?php echo $staff['staffid']; ?>"><?php echo htmlspecialchars($staff['firstname'] . ' ' . $staff['lastname']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <hr>
                    <h5><?php echo _l('notifications'); ?></h5>
                    
                    <div class="form-group">
                        <div class="checkbox">
                            <input type="checkbox" name="action_send_notification" id="action_send_notification" value="1">
                            <label for="action_send_notification"><?php echo _l('send_notification'); ?></label>
                        </div>
                    </div>
                    
                    <div class="form-group" id="notification_fields" style="display: none;">
                        <label for="action_notification_staff"><?php echo _l('notify_staff'); ?></label>
                        <select class="form-control selectpicker" name="action_notification_staff" id="action_notification_staff" data-live-search="true">
                            <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                            <?php foreach ($staff_members as $staff) { ?>
                                <option value="<?php echo $staff['staffid']; ?>"><?php echo htmlspecialchars($staff['firstname'] . ' ' . $staff['lastname']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="form-group" id="notification_message_field" style="display: none;">
                        <label for="action_notification_message"><?php echo _l('notification_message'); ?></label>
                        <textarea class="form-control" name="action_notification_message" id="action_notification_message" rows="2"></textarea>
                        <small class="text-muted"><?php echo _l('notification_message_help'); ?></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Create Tag Modal -->
<div class="modal fade" id="createTagModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo _l('create_new_tag'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="new_tag_name"><?php echo _l('tag_name'); ?> <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="new_tag_name" placeholder="<?php echo _l('enter_tag_name'); ?>">
                    <small class="text-muted"><?php echo _l('tag_name_help'); ?></small>
                </div>
                <div id="tag_creation_message" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-primary" id="create_tag_btn"><?php echo _l('create_tag'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
$(document).ready(function() {
    initDataTable('.table-message-triggers', admin_url + 'contactcenter/message_triggers', undefined, undefined, undefined, [5]);
    
    $('.selectpicker').selectpicker();
    
    $('#action_send_notification').on('change', function() {
        if ($(this).is(':checked')) {
            $('#notification_fields, #notification_message_field').show();
        } else {
            $('#notification_fields, #notification_message_field').hide();
        }
    });
    
    // Create new tag functionality
    $('#create_tag_btn').on('click', function() {
        var tagName = $('#new_tag_name').val().trim();
        
        if (!tagName) {
            alert('<?php echo _l('tag_name_required'); ?>');
            return;
        }
        
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?php echo _l('creating'); ?>...');
        
        $.ajax({
            url: admin_url + 'contactcenter/ajax_create_tag',
            type: 'POST',
            data: {
                tag_name: tagName
            },
            success: function(response) {
                var data = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (data.success) {
                    // Add new tag to selectpicker
                    var $select = $('#action_add_tag');
                    var option = $('<option></option>').attr('value', data.tag_id).text(data.tag_name);
                    $select.append(option);
                    $select.selectpicker('refresh');
                    
                    // Select the newly created tag
                    $select.selectpicker('val', [data.tag_id]);
                    
                    // Show success message
                    $('#tag_creation_message')
                        .removeClass('alert-danger')
                        .addClass('alert alert-success')
                        .html('<i class="fa fa-check"></i> ' + (data.message || '<?php echo _l('tag_created_successfully'); ?>'))
                        .show();
                    
                    // Clear input
                    $('#new_tag_name').val('');
                    
                    // Close modal after 1.5 seconds
                    setTimeout(function() {
                        $('#createTagModal').modal('hide');
                        $('#tag_creation_message').hide().removeClass('alert-success');
                    }, 1500);
                } else {
                    $('#tag_creation_message')
                        .removeClass('alert-success')
                        .addClass('alert alert-danger')
                        .html('<i class="fa fa-exclamation-triangle"></i> ' + (data.message || '<?php echo _l('tag_creation_failed'); ?>'))
                        .show();
                }
                
                $btn.prop('disabled', false).html('<?php echo _l('create_tag'); ?>');
            },
            error: function() {
                $('#tag_creation_message')
                    .removeClass('alert-success')
                    .addClass('alert alert-danger')
                    .html('<i class="fa fa-exclamation-triangle"></i> <?php echo _l('error_occurred'); ?>')
                    .show();
                $btn.prop('disabled', false).html('<?php echo _l('create_tag'); ?>');
            }
        });
    });
    
    // Allow Enter key to create tag
    $('#new_tag_name').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#create_tag_btn').click();
        }
    });
    
    // Clear message when modal is closed
    $('#createTagModal').on('hidden.bs.modal', function() {
        $('#new_tag_name').val('');
        $('#tag_creation_message').hide().removeClass('alert-success alert-danger');
    });
});

function openTriggerModal() {
    $('#triggerForm')[0].reset();
    $('#trigger_id').val('');
    $('#triggerModalTitle').text('<?php echo _l('add_message_trigger'); ?>');
    $('.selectpicker').selectpicker('refresh');
    $('#notification_fields, #notification_message_field').hide();
}

function editTrigger(id) {
    $.get(admin_url + 'contactcenter/get_message_trigger/' + id, function(data) {
        var trigger = JSON.parse(data);
        
        $('#trigger_id').val(trigger.id);
        $('#name').val(trigger.name);
        $('#trigger_type').val(trigger.trigger_type);
        $('#message_sender_type').val(trigger.message_sender_type || 'contact');
        $('#trigger_words').val(trigger.trigger_words);
        $('#case_sensitive').prop('checked', trigger.case_sensitive == 1);
        $('#is_active').prop('checked', trigger.is_active == 1);
        
        // Actions
        if (trigger.action_add_tag) {
            var tags = trigger.action_add_tag.split(',');
            $('#action_add_tag').val(tags);
        }
        $('#action_change_status').val(trigger.action_change_status || '');
        $('#action_change_source').val(trigger.action_change_source || '');
        $('#action_update_field').val(trigger.action_update_field || '');
        $('#action_update_field_value').val(trigger.action_update_field_value || '');
        $('#action_update_custom_field').val(trigger.action_update_custom_field || '');
        $('#action_update_custom_field_value').val(trigger.action_update_custom_field_value || '');
        $('#action_update_description').val(trigger.action_update_description || '');
        $('#action_disable_ai').prop('checked', trigger.action_disable_ai == 1);
        $('#action_change_owner').val(trigger.action_change_owner || '');
        
        // Notifications
        $('#action_send_notification').prop('checked', trigger.action_send_notification == 1);
        if (trigger.action_send_notification == 1) {
            $('#notification_fields, #notification_message_field').show();
        }
        $('#action_notification_staff').val(trigger.action_notification_staff || '');
        $('#action_notification_message').val(trigger.action_notification_message || '');
        
        $('.selectpicker').selectpicker('refresh');
        $('#triggerModalTitle').text('<?php echo _l('edit_message_trigger'); ?>');
        $('#triggerModal').modal('show');
    });
}
</script>

