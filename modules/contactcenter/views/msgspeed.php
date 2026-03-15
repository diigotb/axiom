<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="<?= admin_url("contactcenter/chatsingle/{$device_id}")  ?>" class="btn btn-primary pull-right">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <?php echo _l('contac_back'); ?>
                        </a>
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="no-margin"><i class="fa-solid fa-reply-all"></i> <?php echo _l('contac_chat_msg_speed'); ?></h4>
                                <hr />
                            </div>
                        </div>

                        <?php echo form_open_multipart(admin_url('contactcenter/save_msgspeed'), array('id' => 'msgspeed-form')); ?>
                        <input type="hidden" name="device_id" value="<?= $device_id ?>">
                        <div class="row msgspeed-form">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label"><?php echo _l('contac_chat_msg_speed_label_title'); ?></label>
                                    <input type="text" name="title" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label"><?php echo _l('contac_chat_msg_speed_label'); ?></label>
                                    <input type="text" name="content" class="form-control" value="">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label"><?php echo _l('contac_chat_msg_speed_label_type'); ?></label>
                                    <select name="restrict" class="form-control">
                                        <option value="1"><?php echo _l('contac_chat_msg_speed_restrict'); ?></option>
                                        <option value="0"><?php echo _l('contac_chat_msg_speed_public'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary pull-left"><?= _l("save") ?></button>
                            </div>
                        </div>
                        <?php echo form_close(); ?>


                        <div class="row">
                            <div class="col-md-12">
                                <hr />
                                <table class="table dt-table table-items" data-order-col="0" data-order-type="asc">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
                                            <th><?php echo _l('contac_chat_msg_speed_label_title'); ?></th>
                                            <th><?php echo _l('contac_chat_msg_speed_label'); ?></th>
                                            <th><?php echo _l('contac_chat_msg_speed_label_type'); ?></th>
                                            <th><?php echo _l('date'); ?></th>
                                            <th><?php echo _l('options'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($msgspeed as $msg) { ?>
                                            <tr>
                                                <td><?= $msg->id ?></td>
                                                <td><?= $msg->title ?></td>
                                                <td><?= $msg->content ?></td>

                                                <?php
                                                if ($msg->restrict == 1) {
                                                    $msg->restrict = "<span class='label label-danger'>" . _l('contac_chat_msg_speed_restrict') . "</span>";
                                                } else {
                                                    $msg->restrict = "<span class='label label-success'>" . _l('contac_chat_msg_speed_public') . "</span>";
                                                }
                                                ?>

                                                <td><?= $msg->restrict  ?></td>
                                                <td><?= _d($msg->date) ?></td>
                                                <td>
                                                    <a href="javascript:void(0);" onclick="delete_msgspeed('<?= $msg->id ?>')" class="btn btn-danger btn-icon"><i class="fa fa-trash"></i></a>
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
    <?php init_tail(); ?>

    <script>
        function delete_msgspeed(id) {
            // Faz a confirmação
            var userConfirm = confirm('<?= _l('contac_aviso_deleted') ?>');
            if (userConfirm) {
                $.ajax({
                    url: '<?= admin_url('contactcenter/delete_msgspeed') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.retorno == true) {
                            window.location.reload();
                        }
                    }
                });
            }
        }
    </script>