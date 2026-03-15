<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <i class="fa fa-object-group tw-mr-2"></i>
                            <?= _l("contac_assistant_templates"); ?>
                        </h4>
                        <p class="text-muted tw-mb-4"><?= _l("contac_assistant_templates_desc"); ?></p>
                        <div class="_buttons tw-mb-4">
                            <a href="<?= admin_url("contactcenter/assistant_template_edit") ?>" class="btn btn-success">
                                <i class="fa fa-plus tw-mr-1"></i> <?= _l("contac_assistant_template_new"); ?>
                            </a>
                            <?php if (!empty($templates_table_exists)): ?>
                            <a href="<?= admin_url("contactcenter/sync_assistant_templates_from_files") ?>" class="btn btn-info" title="<?= _l("contac_assistant_templates_sync_btn_title"); ?>">
                                <i class="fa fa-refresh tw-mr-1"></i> <?= _l("contac_assistant_templates_sync"); ?>
                            </a>
                            <?php endif; ?>
                            <a href="<?= admin_url("contactcenter/assistant_ai") ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left tw-mr-1"></i> <?= _l("contac_back"); ?>
                            </a>
                        </div>
                        <div class="template-tiles-grid">
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
                                        
                                        <form method="post" action="<?= admin_url('contactcenter/add_assistant') ?>" class="tw-mb-2">
                                            <input type="hidden" name="template_id" value="<?= (int)$tpl->id ?>">
                                            <input type="text" name="ai_name" class="form-control form-control-sm tw-mb-2" placeholder="<?= _l("contact_assistant_ai_name"); ?>" value="<?= htmlspecialchars($tpl_display_name) ?>" required>
                                            <button type="submit" class="btn btn-success btn-sm btn-block" title="<?= _l("contac_assistant_use_template"); ?>">
                                                <i class="fa fa-plus tw-mr-1"></i> <?= _l("contac_assistant_use_template"); ?>
                                            </button>
                                        </form>

                                        <?php if (!$tpl->is_system): ?>
                                            <div class="template-tile-actions tw-justify-end">
                                                <a href="<?= admin_url("contactcenter/assistant_template_edit/" . $tpl->id) ?>" class="btn btn-default btn-sm" title="<?= _l("contac_assistant_template_edit"); ?>">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="<?= admin_url("contactcenter/delete_assistant_template/" . $tpl->id) ?>" class="btn btn-danger btn-sm" title="<?= _l("delete"); ?>" onclick="return confirm('<?= _l("contac_aviso_deleted") ?>');">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        <?php endif; ?>
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
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<link rel="stylesheet" href="<?= module_dir_url('contactcenter', 'assets/css/assistant_templates.css') ?>?v=<?= time() ?>">
