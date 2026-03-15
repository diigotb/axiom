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
                                <i class="fa-solid fa-link"></i>
                                <?php echo _l('links_personalizados'); ?>
                            </span>
                        </h4>

                        <div class="row">
                            <?php echo form_open(admin_url('contactcenter/linkscustom'), ['id' => 'links-form']); ?>
                            <div class="col-md-5ths">
                                <div class="form-group">
                                    <label for="phonenumber" class="control-label"><?= _l('links_phonenumber'); ?></label>
                                    <input type="text" name="phonenumber" id="phonenumber" class="form-control" value="" placeholder="5517991191234" required />
                                </div>
                            </div>
                            <div class="col-md-5ths">
                                <div class="form-group">
                                    <label for="msg" class="control-label"><?= _l('links_message'); ?></label>
                                    <input type="text" name="msg" id="msg" class="form-control" value="" placeholder="<?= _l('links_message'); ?>" />
                                </div>
                            </div>

                            <div class="col-md-5ths">
                                <div class="form-group">
                                    <label for="source" class="control-label"><?= _l('links_source'); ?></label>
                                    <input type="text" name="source" id="source" class="form-control" value="" placeholder="<?= _l('links_source_explain'); ?>" required />
                                </div>
                            </div>
                            <div class="col-md-5ths">
                                <button type="submit" class="btn btn-primary pull-left">Aplicar</button>
                            </div>
                            <?php echo form_close(); ?>
                        </div>

                        <hr class="hr-panel-separator" />
                        <div class="clearfix"></div>
                        <div>
                            <table id="meta" class="table">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th><?= _l("links_phonenumber"); ?></th>
                                        <th><?= _l("links_message"); ?></th>
                                        <th><?= _l("links_source"); ?></th>
                                        <th><?= _l("links_personalizados"); ?></th>
                                        <th><?= _l("links_click"); ?></th>
                                        <th><?= _l("links_date"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($links as $link) {
                                        $status = get_status_leads_meta($lead->status);
                                    ?>
                                        <tr>
                                            <td><?= $link->link_id; ?> </td>
                                            <td>
                                                <a href="<?= admin_url('contactcenter/meta_analytics/' . $link->hash) ?>"><?= $link->phonenumber ?></a>
                                                <div class='row-options'>
                                                    <a href='<?= admin_url('contactcenter/meta_analytics/' . $link->hash) ?>' class='text-danger'><?= _l("contac_ver") ?></a> |
                                                    <a href='javascript:void(0);' class='text-danger' onclick='delete_link(<?= $link->link_id ?>)'><?= _l("contac_excluir") ?></a>
                                                </div>
                                            </td>
                                            <td><?= ($link->msg ? $link->msg : '---'); ?> </td>
                                            <td><?= $link->source; ?> </td>
                                            <td><button onclick=" copyText(this)" class="btn btn-default" data-toggle="tooltip" data-original-title="<?= _l("links_copy") ?>" data-clipboard-text="<?= site_url('contactcenter/links/code/' . $link->hash); ?>"><i class="fa fa-link"></i></button>
                                            </td>
                                            <td><?= $link->count; ?> </td>
                                            <td><?= _dt($link->date); ?> </td>
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
    appDataTableInline("#meta", {
        supportsButtons: true,
        supportsLoading: true,
        autoWidth: false,
        order: [
            [0, 'desc']
        ]
    });



    function delete_link(id) {
        var deleteConfirmation = "<?= _l("links_delete") ?>";
        if (confirm(deleteConfirmation)) {
            $.ajax({
                url: admin_url + "contactcenter/delete_link",
                type: "POST",
                dataType: "json",
                data: {
                    id: id
                },
                success: function(data) {
                    if (data.result) {
                        window.location.reload();
                    }
                }

            })
        }
    }

    function copyText(element) {
        // Remove espaços em branco extras
        var textToCopy = $(element).attr("data-clipboard-text").trim();

        // Cria um elemento temporário de input para copiar o texto
        var tempInput = $("<input>");
        $("body").append(tempInput);

        // Define o valor do input temporário com o texto do elemento clicado
        tempInput.val(textToCopy).select();

        // Executa o comando de cópia
        document.execCommand("copy");

        // Remove o input temporário
        tempInput.remove();

        // Armazena o texto original
        var originalText = $(element).html();

        // Exibe "Copiado" no lugar do texto original
        $(element).html('<span class="copied">Copied</span>');

        // Retorna o texto original após 2 segundos
        setTimeout(function() {
            $(element).html(originalText);
        }, 2000);
    }
</script>
</body>

</html>