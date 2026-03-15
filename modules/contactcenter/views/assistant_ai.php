<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <lord-icon src="https://cdn.lordicon.com/uttrirxf.json" trigger="loop" delay="2000" colors="primary:#00e09b,secondary:#00e09b" style="width:50px;height:50px"></lord-icon>
                            <img src="<?= base_url('modules/contactcenter/logo_axiom_white.png') ?>" alt="AXIOM" style="height:28px;width:auto;object-fit:contain;margin-left:8px">
                        </h4>
                        <hr class="hr-panel-separator" />
                        <?php $aviso = $this->input->get('assistant'); ?>
                        <?php if ($aviso == "true") { ?>
                            <div class="alert alert-warning" role="alert"><?= _l("contac_assistent_aviso_limit_assistant") ?></div>
                        <?php } ?>

                        <!-- Templates section - primary focus -->
                        <div class="assistant-templates-section tw-mb-8">
                            <h5 class="tw-font-semibold tw-text-base tw-mb-1"><?= _l("contac_assistant_start_with_template"); ?></h5>
                            <p class="text-muted tw-mb-4 tw-text-sm"><?= _l("contac_assistant_templates_desc"); ?></p>
                            <div class="template-tiles-grid template-tiles-assistant-ai">
                                <?php if (empty($templates)): ?>
                                    <div class="template-empty-state-full">
                                        <p class="tw-text-neutral-600 tw-mb-3"><?= _l("contac_assistant_no_templates"); ?></p>
                                        <a href="<?= admin_url("contactcenter/assistant_template_edit") ?>" class="btn btn-success"><i class="fa fa-plus tw-mr-1"></i> <?= _l("contac_assistant_template_new"); ?></a>
                                    </div>
                                <?php endif; ?>
                                <?php
                                    $func_template_lang = [
                                        'get_lead_info' => ['contac_template_func_get_lead_info_name', 'contac_template_func_get_lead_info_desc'],
                                        'get_lead_context' => ['contac_template_func_get_lead_context_name', 'contac_template_func_get_lead_context_desc'],
                                        'manage_conversation' => ['contac_template_func_manage_conversation_name', 'contac_template_func_manage_conversation_desc'],
                                        'update_leads' => ['contac_template_func_update_leads_name', 'contac_template_func_update_leads_desc'],
                                        'get_horario_agenda' => ['contac_template_func_appointment_name', 'contac_template_func_appointment_desc'],
                                        'create_contract' => ['contac_template_func_create_contract_name', 'contac_template_func_create_contract_desc'],
                                        'get_tabela_precos' => ['contac_template_func_price_table_name', 'contac_template_func_price_table_desc'],
                                        'open_ticket' => ['contac_template_func_create_ticket_name', 'contac_template_func_create_ticket_desc'],
                                        'get_faturas_axiom' => ['contac_template_func_get_invoices_name', 'contac_template_func_get_invoices_desc'],
                                        'send_media' => ['contac_template_func_send_media_name', 'contac_template_func_send_media_desc'],
                                        'create_group_chat' => ['contac_template_func_create_group_name', 'contac_template_func_create_group_desc'],
                                    ];
                                ?>
                            <?php foreach ($templates as $tpl):
                                $tpl_display_name = $tpl->name;
                                $tpl_display_desc = $tpl->description;
                                if ($tpl->is_system) {
                                    if ($tpl->name === 'Aesthetic Clinics') {
                                        $tpl_display_name = _l('contac_template_aesthetic_clinics_name');
                                        $tpl_display_desc = _l('contac_template_aesthetic_clinics_desc');
                                    } elseif ($tpl->name === 'Franchise Sales') {
                                        $tpl_display_name = _l('contac_template_franchise_sales_name');
                                        $tpl_display_desc = _l('contac_template_franchise_sales_desc');
                                    } else {
                                        $lang_key_base = 'contac_template_' . strtolower(str_replace(' ', '_', $tpl->name));
                                        $name_trans = _l($lang_key_base . '_name');
                                        $desc_trans = _l($lang_key_base . '_desc');
                                        if (strpos($name_trans, 'contac_template_') === false) {
                                            $tpl_display_name = $name_trans;
                                        }
                                        if (strpos($desc_trans, 'contac_template_') === false) {
                                            $tpl_display_desc = $desc_trans;
                                        }
                                    }
                                }
                            ?>
                                    <div class="template-tile-card <?= $tpl->is_system ? 'template-system' : '' ?>">
                                        <div class="template-tile-visual">
                                            <?php if (!empty($tpl->image_path)): ?>
                                                <img src="<?= base_url($tpl->image_path) ?>" alt="" class="template-tile-img">
                                            <?php else: ?>
                                                <div class="template-tile-icon-wrap">
                                                    <i class="fa <?= htmlspecialchars($tpl->icon ?: 'fa-robot') ?> template-tile-icon"></i>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($tpl->is_system): ?>
                                                <span class="template-badge template-badge-system"><?= _l("contac_assistant_template_system"); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="template-tile-body">
                                            <h5 class="template-tile-title"><?= htmlspecialchars($tpl_display_name) ?></h5>
                                            <?php if (!empty($tpl_display_desc)): ?>
                                                <p class="template-tile-desc"><?= htmlspecialchars(mb_substr($tpl_display_desc, 0, 120)) ?><?= mb_strlen($tpl_display_desc) > 120 ? '...' : '' ?></p>
                                            <?php endif; ?>
                                            <?php echo form_open(admin_url('contactcenter/add_assistant'), ['class' => 'template-form-inline']); ?>
                                                <?php echo form_hidden('template_id', (int)$tpl->id); ?>
                                                <input type="text" name="ai_name" class="form-control form-control-sm tw-mb-2" placeholder="<?= _l("contact_assistant_ai_name"); ?>" value="<?= htmlspecialchars($tpl_display_name) ?>" required>
                                                <button type="submit" class="btn btn-success btn-sm btn-block">
                                                    <i class="fa fa-plus tw-mr-1"></i> <?= _l("contac_assistant_use_template"); ?>
                                                </button>
                                            <?php echo form_close(); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <a href="<?= admin_url("contactcenter/assistant_template_edit") ?>" class="template-tile-card template-tile-add">
                                    <div class="template-tile-add-inner">
                                        <i class="fa fa-plus-circle tw-text-4xl tw-text-neutral-400 tw-mb-2"></i>
                                        <span class="tw-font-medium tw-text-neutral-600"><?= _l("contac_assistant_template_new"); ?></span>
                                        <span class="tw-text-sm tw-text-neutral-500 tw-mt-1"><?= _l("contac_assistant_template_new_desc"); ?></span>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Quick actions -->
                        <div class="assistant-quick-actions tw-flex tw-flex-wrap tw-gap-3 tw-mb-6">
                            <?php if (has_permission('contractcenter', '', 'create')) { ?>
                                <button class="btn btn-default" data-toggle="modal" data-target="#modalAssistantBlank">
                                    <i class="fa fa-file-o tw-mr-1"></i> <?= _l("contac_assistant_create_blank"); ?>
                                </button>
                                <a href="<?= admin_url("contactcenter/assistant_templates") ?>" class="btn btn-default">
                                    <i class="fa fa-object-group tw-mr-1"></i> <?= _l("contac_assistant_manage_templates"); ?>
                                </a>
                                <?php if ($this->db->table_exists(db_prefix() . 'contactcenter_assistant_templates')): ?>
                                <a href="<?= admin_url("contactcenter/sync_assistant_templates_from_files") ?>" class="btn btn-info" title="<?= _l("contac_assistant_templates_sync_btn_title"); ?>">
                                    <i class="fa fa-refresh tw-mr-1"></i> <?= _l("contac_assistant_templates_sync"); ?>
                                </a>
                                <?php endif; ?>
                            <?php } ?>
                            <a href="<?= admin_url("contactcenter/device") ?>" class="btn btn-primary pull-right">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> <?= _l('contac_back'); ?>
                            </a>
                        </div>

                        <!-- Assistants list -->
                        <div class="panel_s">
                            <div class="panel-body">
                                <h5 class="tw-font-semibold tw-mb-3"><?= _l("contac_assistant_existing"); ?></h5>
                                <table id="contact_assistant" class="table" data-order-col="0" data-order-type="desc">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
                                            <th><?= _l("contact_assistant_ai_name"); ?></th>
                                            <th><?= _l("contact_assistant_ai_token"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($assistants as $assistant) {
                                            extract((array) $assistant);
                                            echo "<tr class='assistant_{$id}'>
                                                <td>{$id}</td>
                                                <td>
                                                    <div class='box_thumbTlabeCommunity'>
                                                        <div>
                                                            {$ai_name}
                                                            <div class='row-options'>
                                                                <a href='" . admin_url("contactcenter/assistant_edit/{$id}") . "'>" . _l("contac_editar") . "</a> |
                                                                <a href='javascript:void(0);' class='text-danger' onclick='delete_assistant({$id})'>" . _l("contac_excluir") . "</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>" . str_replace("asst_", "inxx_", $ai_token) . "</td>
                                            </tr>";
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Create Blank Assistant -->
<div class="modal fade" id="modalAssistantBlank" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= _l("contac_assistant_create_blank"); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open(admin_url('contactcenter/add_assistant')); ?>
                <input type="hidden" name="template_id" value="">
                <div class="form-group">
                    <label><?= _l("contact_assistant_ai_name"); ?></label>
                    <input type="text" class="form-control" name="ai_name" placeholder="<?= _l("contact_assistant_ai_name"); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-plus tw-mr-1"></i> <?= _l("contac_assistant_create_blank"); ?>
                </button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<link rel="stylesheet" href="<?= module_dir_url('contactcenter', 'assets/css/assistant_templates.css') ?>?v=<?= time() ?>">
<style>
.template-tiles-assistant-ai.template-tiles-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
.assistant-templates-section { padding: 16px 0; }
.template-form-inline .form-control { font-size: 13px; }
</style>
<script>
$(document).ready(function() {
    initDataTableInline("#contact_assistant");
    $('.modal').on('hidden.bs.modal', function() { var f = $(this).find('form')[0]; if (f) f.reset(); });
});
function delete_assistant(id) {
    if (confirm("<?= _l('contac_aviso_deleted') ?>")) {
        $.ajax({
            url: site_url + "contactcenter/delete_assistant",
            data: { id: id },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.result == true) {
                    $(".assistant_" + id).fadeOut();
                } else {
                    alert("<?= _l("contac_assistant_error") ?>");
                }
            }
        });
    }
}
</script>
</body>
</html>
