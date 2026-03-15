<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <lord-icon src="https://cdn.lordicon.com/rqptwppx.json" trigger="loop" delay="2000" colors="primary:#00e09b,secondary:#00e09b" style="width:50px;height:50px">
                            </lord-icon>

                            <span>
                                <?php echo _l('drawflow_page'); ?>
                            </span>
                        </h4>
                        <hr class="hr-panel-separator" />
                        <div class="tw-mb-2 sm:tw-mb-4">
                            <div class="_buttons">
                                <?php if (has_permission('contractcenter', '', 'create')) { ?>
                                    <a class="btn btn-primary" href="<?php echo admin_url('contactcenter/fluxo_create'); ?>">
                                        <i class="fa-regular fa-plus tw-mr-1"></i>
                                        <?php echo _l('drawflow_new'); ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="panel_s">
                            <table id="contact_device" class="table">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th><?= _l("drawflow_flow"); ?></th>
                                        <th><?= _l("drawflow_flow_status"); ?></th>
                                        <th><?= _l("drawflow_flow_date"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($drawflow as $draw) {
                                        extract((array) $draw);

                                        echo "<tr class='fluxo_{$draw_id}'>
                                                <td>{$draw_id}</td>                                                    
                                                <td> 
                                                    <div class='box_thumbTlabeCommunity'> 
                                                        <div>    
                                                           <a class='device-name' href='" . admin_url('contactcenter/automation/' . $draw_id) . "'>{$title}</a> 
                                                            <div class='row-options'>                                                              
                                                                <a href='" . admin_url('contactcenter/automation/' . $draw_id) . "'class='text-danger' >" . _l("contac_editar") . "</a>
                                                                <a href='javascript:void(0);'class='text-danger' onclick='delete_fluxo({$draw_id})' >" . _l("contac_excluir") . "</a>
                                                            </div>        
                                                        </div>    
                                                    </div>    
                                                </td>                    
                                                <td>" . status_drawflow($status) . "</td>                                 
                                                <td>" . _dt($date) . "</td>                                                
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



<?php init_tail(); ?>
<script>
    $(document).ready(function() {
        initDataTableInline("#contact_device");

    });

    function delete_fluxo(id) {

        var r = confirm("<?= _l('contac_aviso_deleted') ?>");

        if (r == true) {
            $.ajax({
                url: site_url + "contactcenter/delete_fluxo",
                data: {
                    id: id
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if (data.result == true) {
                        $(".fluxo_" + id).fadeOut();
                    } else {
                        alert("<?= _l('contac_assistant_error') ?>");
                    }
                }
            });
        }


    }
</script>
</body>

</html>