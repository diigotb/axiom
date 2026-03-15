<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <lord-icon src="https://cdn.lordicon.com/uttrirxf.json" trigger="loop" delay="2000" colors="primary:#00e09b,secondary:#00e09b" style="width:50px;height:50px">
                            </lord-icon>

                            <span>
                                <?php echo _l('contact_group'); ?>
                            </span>
                        </h4>
                        <hr class="hr-panel-separator" />
                        <div class="tw-mb-2 sm:tw-mb-4">
                            <div class="_buttons">
                                <?php if (has_permission('contractcenter', '', 'create')) { ?>
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalDevice">
                                        <i class="fa-regular fa-plus tw-mr-1"></i>
                                        <?php echo _l('contact_group_new'); ?>
                                    </button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="panel_s">
                            <table id="contact_device" class="table">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th><?= _l("contact_group_name"); ?></th>
                                        <th><?= _l("contact_group_description"); ?></th>
                                        <th><?= _l("contact_group_number_text"); ?></th>
                                        <th><?= _l("contact_group_device"); ?></th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($groups as $group) {
                                        extract((array) $group);  
                                        $numbers = explode(",", $group->numbers); 
                                        
                                        $htmlNumber ="";
                                        // html que percorre os numeros coloca um evento de deletar por numero
                                        foreach ($numbers as $number) {
                                            $htmlNumber .= "<div class='box_thumbTlabeCommunity'> 
                                                                <div class='number_{$number}'>    
                                                                    {$number}
                                                                    <div class='row-options'>    
                                                                        <a href='javascript:void(0);'class='text-danger' onclick='ajax_delete_participant_group({$id}, \"{$number}\")' >" . _l("contac_excluir") . "</a>
                                                                    </div>        
                                                                </div>    
                                                            </div>";
                                        }
                                        
                                        echo "<tr class='engine_{$id}'>
                                                <td>{$id}</td> 
                                                <td> 
                                                    <div class='box_thumbTlabeCommunity'> 
                                                        <div>    
                                                            {$group_api_name}
                                                            <div class='row-options'>    
                                                                <a href='javascript:void(0);'class='text-danger' onclick='delete_group({$id})' >" . _l("contac_excluir") . "</a>
                                                            </div>        
                                                        </div>    
                                                    </div>    
                                                </td> 
                                                <td>{$group_api_description}</td>
                                                <td>{$htmlNumber}</td>
                                                <td>" . get_device_name($device_id) ."</td>                            
                                             </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                    </div>



                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalDevice" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title " id="exampleModalLongTitle"><?= _l("contact_group_new"); ?></h4>
            </div>            
            <div class="modal-body">
                <?php echo form_open_multipart(admin_url('contactcenter/add_group')); ?>
                <input type="hidden" name="id" value="">
                <input type="hidden" name="group_api_id" value="">
                <div class="form-group">
                    <label><?= _l("contact_group_name"); ?></label>
                    <input type="text" class="form-control" name="group_api_name" placeholder="<?= _l("contact_group_name"); ?>" required>
                </div>

                <div class="form-group">
                    <label><?= _l("contact_group_description"); ?></label>
                    <input type="text" class="form-control" name="group_api_description" placeholder="<?= _l("contact_group_description"); ?>" required>
                </div>

                <div class="form-group">
                    <label><?= _l("contact_group_number"); ?></label>
                    <textarea class="form-control" name="numbers" placeholder="<?= _l("contact_group_number"); ?>" required></textarea>
                </div>                  

                <div class="form-group">
                    <label><?= _l("contact_group_device"); ?></label>
                    <select name="device_id" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                        <option></option>
                        <?php foreach ($devices as $device) { ?>
                            <option value="<?php echo $device->dev_id; ?>">
                                <?php echo $device->dev_name?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>




<?php init_tail(); ?>
<script>
    $(document).ready(function() {
        initDataTableInline("#contact_device");

        //Limpa os formularios das modal
        $('.modal').on('hidden.bs.modal', function(e) {
            // Reseta o formulário quando fecha modal 
            $(this).find('form')[0].reset();
        });

    });

    
    function delete_group(id) {
        $.ajax({
            url: site_url + "contactcenter/ajax_delete_group",
            data: {
                id: id
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.result) {
                    $(".engine_" + id).fadeOut();
                }

            }
        });
    }

    function ajax_delete_participant_group(id, number) {
        $.ajax({
            url: site_url + "contactcenter/ajax_delete_participant_group",
            data: {
                id: id,
                number: number
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.result) {
                    $(".number_" + number).fadeOut();
                }

            }
        });
    }

</script>
</body>

</html>