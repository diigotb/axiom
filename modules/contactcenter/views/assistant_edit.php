<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <lord-icon
                                src="https://cdn.lordicon.com/uttrirxf.json"
                                trigger="loop"
                                delay="2000"
                                colors="primary:#00e09b,secondary:#00e09b"
                                style="width:50px;height:50px">
                            </lord-icon>

                            <span>
                                <?php echo $assistants->ai_name . " - " . str_replace("asst_", "inxx_", $assistants->ai_token);   ?>
                            </span>
                        </h4>
                        <h5><?= ($assistants->staffid ? "Created by: " . _dt($assistants->create_date) . " - " . get_staff_full_name($assistants->staffid) : "") ?> </h5>
                        <div class="tw-mb-2 sm:tw-mb-4">
                            <div class="_buttons">
                                <a href="<?= admin_url("contactcenter/assistant_ai")  ?>" class="btn btn-primary ">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    <?php echo _l('contac_back'); ?>
                                </a>
                                <a href="<?= admin_url("contactcenter/assistant_visual_builder/" . $assistants->id)  ?>" class="btn btn-success">
                                    <i class="fa-solid fa-diagram-project"></i>
                                    <?php echo _l('contac_assistant_visual_builder'); ?>
                                </a>
                                <div class="tw-inline-flex tw-flex-wrap tw-items-center tw-gap-2 tw-ml-4 tw-pl-4 tw-border-l tw-border-neutral-300">
                                    <span class="tw-text-sm tw-text-neutral-600"><?= _l('contac_assistant_onboarding_link') ?>:</span>
                                    <button type="button" class="btn btn-info btn-sm" id="btnGenOnboardingLink" data-assistant-id="<?= $assistants->id ?>">
                                        <i class="fa-solid fa-link"></i> <?= _l('contac_assistant_get_onboarding_link') ?>
                                    </button>
                                    <div id="onboardingLinkDisplay" class="tw-flex tw-items-center tw-gap-2 <?= empty($onboarding_url ?? '') ? 'tw-hidden' : '' ?>">
                                        <input type="text" id="onboardingLinkInput" class="form-control tw-max-w-md" readonly value="<?= htmlspecialchars($onboarding_url ?? '') ?>">
                                        <button type="button" class="btn btn-default btn-sm" id="btnCopyOnboardingLink" title="<?= _l('copied_to_clipboard') ?: 'Copy' ?>">
                                            <i class="fa-solid fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="hr-panel-separator" />
                        <div class="panel_s tw-mb-4" id="onboardingDataPanel">
                            <div class="panel-body">
                                <h5 class="tw-font-semibold tw-mb-3 tw-flex tw-items-center tw-justify-between">
                                    <span><i class="fa-solid fa-inbox"></i> <?= _l('contac_assistant_onboarding_data') ?></span>
                                    <button type="button" class="btn btn-default btn-sm" id="btnRefreshOnboarding" title="<?= _l('contac_assistant_onboarding_refresh') ?>">
                                        <i class="fa-solid fa-rotate-right"></i> <?= _l('contac_assistant_onboarding_refresh') ?>
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm tw-ml-2" id="btnGenerateFromOnboarding" title="<?= _l('contac_assistant_generate_from_onboarding') ?>">
                                        <i class="fa-solid fa-wand-magic-sparkles"></i> <?= _l('contac_assistant_generate_from_onboarding') ?>
                                    </button>
                                </h5>
                                <div id="onboardingDataContent">
                                <?php if (!empty($onboarding_list)): ?>
                                    <?php if (count($onboarding_list) > 1): ?>
                                    <div class="tw-mb-2">
                                        <select id="onboardingSelect" class="form-control tw-max-w-xs tw-inline-block">
                                            <?php foreach ($onboarding_list as $idx => $ob): ?>
                                            <option value="<?= $idx ?>"><?= _l('contac_assistant_onboarding_submitted') ?>: <?= _dt($ob->submitted_at) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php endif; ?>
                                    <?php foreach ($onboarding_list as $idx => $onboarding_data): ?>
                                    <div class="onboarding-item tw-bg-neutral-50 tw-rounded tw-p-4 tw-text-sm <?= $idx > 0 ? 'tw-hidden' : '' ?>" data-idx="<?= $idx ?>">
                                        <?php
                                        $fd = is_string($onboarding_data->form_data) ? json_decode($onboarding_data->form_data, true) : (array)$onboarding_data->form_data;
                                        if (is_array($fd) && !empty($fd)):
                                        $label_map = [
                                            'company_name' => 'cc_onboarding_company_name', 'company_info' => 'cc_onboarding_company_info',
                                            'tone' => 'cc_onboarding_tone', 'assistant_name' => 'cc_onboarding_assistant_name',
                                            'assistant_characteristics' => 'cc_onboarding_assistant_characteristics', 'assistant_characteristics_notes' => 'cc_onboarding_assistant_characteristics_notes',
                                            'greeting' => 'cc_onboarding_greeting',
                                            'objective' => 'cc_onboarding_objective', 'objective_notes' => 'cc_onboarding_obj_notes',
                                            'faq' => 'cc_onboarding_faq', 'has_materials' => 'cc_onboarding_has_materials',
                                            'materials_description' => 'cc_onboarding_materials_desc', 'uploaded_materials' => 'cc_onboarding_uploaded_materials',
                                            'flow_template' => 'cc_onboarding_flow_template', 'mandatory_info' => 'cc_onboarding_mandatory_info',
                                            'opening_questions' => 'cc_onboarding_opening_questions', 'service_sequence' => 'cc_onboarding_service_sequence',
                                            'flow_notes' => 'cc_onboarding_flow_notes', 'cep' => 'cc_onboarding_cep',
                                            'address' => 'cc_onboarding_address', 'address_number' => 'cc_onboarding_address_number',
                                            'phone' => 'cc_onboarding_phone', 'email' => 'cc_onboarding_email',
                                            'website' => 'cc_onboarding_website', 'business_hours' => 'cc_onboarding_business_hours',
                                            'hours_notes' => 'cc_onboarding_hours_notes', 'social_media' => 'cc_onboarding_social_media',
                                            'services_list' => 'cc_onboarding_services', 'services_extra' => 'cc_onboarding_services_extra',
                                            'service_type' => 'cc_onboarding_service_type', 'functions' => 'cc_onboarding_functions',
                                            'escalation_triggers' => 'cc_onboarding_escalation', 'decision_criteria' => 'cc_onboarding_decision_criteria',
                                        ];
                                        $value_map = [
                                            'tone' => ['casual' => 'cc_onboarding_tone_casual', 'formal' => 'cc_onboarding_tone_formal', 'technical' => 'cc_onboarding_tone_technical', 'informal' => 'cc_onboarding_tone_informal'],
                                            'assistant_characteristics' => ['friendly' => 'cc_onboarding_char_friendly', 'patient' => 'cc_onboarding_char_patient', 'proactive' => 'cc_onboarding_char_proactive', 'expert' => 'cc_onboarding_char_expert', 'empathetic' => 'cc_onboarding_char_empathetic', 'efficient' => 'cc_onboarding_char_efficient'],
                                            'flow_template' => ['qualify_first' => 'cc_onboarding_flow_qualify_first', 'inform_first' => 'cc_onboarding_flow_inform_first', 'schedule_first' => 'cc_onboarding_flow_schedule_first', 'flexible' => 'cc_onboarding_flow_flexible', 'custom' => 'cc_onboarding_flow_custom'],
                                            'has_materials' => ['yes' => 'cc_onboarding_has_materials_yes', 'no' => 'cc_onboarding_has_materials_no'],
                                            'mandatory_info' => ['name' => 'cc_onboarding_mandatory_name', 'phone' => 'cc_onboarding_mandatory_phone', 'email' => 'cc_onboarding_mandatory_email', 'service' => 'cc_onboarding_mandatory_service', 'date' => 'cc_onboarding_mandatory_date', 'budget' => 'cc_onboarding_mandatory_budget', 'other' => 'cc_onboarding_mandatory_other'],
                                            'service_type' => ['consultation' => 'cc_onboarding_svc_consultation', 'procedure' => 'cc_onboarding_svc_procedure', 'product' => 'cc_onboarding_svc_product', 'package' => 'cc_onboarding_svc_package', 'rental' => 'cc_onboarding_svc_rental', 'subscription' => 'cc_onboarding_svc_subscription', 'other' => 'cc_onboarding_svc_other'],
                                            'objective' => ['qualification' => 'cc_onboarding_obj_qualification', 'scheduling' => 'cc_onboarding_obj_scheduling', 'informing' => 'cc_onboarding_obj_informing', 'sales' => 'cc_onboarding_obj_sales'],
                                            'escalation_triggers' => ['complaint' => 'cc_onboarding_esc_complaint', 'refund' => 'cc_onboarding_esc_refund', 'complex' => 'cc_onboarding_esc_complex', 'schedule' => 'cc_onboarding_esc_schedule', 'negotiation' => 'cc_onboarding_esc_negotiation', 'technical' => 'cc_onboarding_esc_technical', 'manager' => 'cc_onboarding_esc_manager', 'other' => 'cc_onboarding_esc_other'],
                                            'decision_criteria' => ['urgency' => 'cc_onboarding_dec_urgency', 'first_come' => 'cc_onboarding_dec_first_come', 'vip' => 'cc_onboarding_dec_vip', 'fast_urgent' => 'cc_onboarding_dec_fast_urgent', 'flexible' => 'cc_onboarding_dec_flexible'],
                                        ];
                                        foreach ($fd as $k => $v):
                                            if ($k === '_restore' || (strlen($k) > 0 && $k[0] === '_')) continue;
                                            if ($v === '' || $v === null || (is_array($v) && empty($v))) continue;
                                                $label = isset($label_map[$k]) ? _l($label_map[$k]) : ucfirst(str_replace('_', ' ', $k));
                                                if ($k === 'uploaded_materials' && is_array($v)):
                                        ?>
                                        <div class="tw-mb-2"><strong><?= htmlspecialchars($label) ?>:</strong>
                                            <?php foreach ($v as $path): $url = site_url('uploads/' . $path); $name = basename($path); ?>
                                            <a href="<?= htmlspecialchars($url) ?>" download="<?= htmlspecialchars($name) ?>" title="<?= _l('contac_assistant_onboarding_download') ?>" class="btn btn-default btn-xs tw-mr-1 tw-mb-1"><i class="fa-solid fa-download"></i> <?= htmlspecialchars($name) ?></a>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php
                                                else:
                                                    if (is_array($v)) {
                                                        $translated = [];
                                                        $vm = isset($value_map[$k]) ? $value_map[$k] : [];
                                                        if ($k === 'functions') {
                                                            foreach ($v as $item) {
                                                                $translated[] = _l('cc_onboarding_cap_' . $item);
                                                            }
                                                        } else {
                                                            foreach ($v as $item) {
                                                                $translated[] = isset($vm[$item]) ? _l($vm[$item]) : $item;
                                                            }
                                                        }
                                                        $v = implode(', ', $translated);
                                                    } else {
                                                        $vm = isset($value_map[$k]) ? $value_map[$k] : [];
                                                        $v = isset($vm[$v]) ? _l($vm[$v]) : $v;
                                                    }
                                                    if (strlen((string)$v) > 200) $v = substr($v, 0, 200) . '...';
                                        ?>
                                        <div class="tw-mb-2"><strong><?= htmlspecialchars($label) ?>:</strong> <?= htmlspecialchars((string)$v) ?></div>
                                        <?php endif; endforeach; else: ?>
                                        <p class="tw-text-neutral-500"><?= _l('contac_assistant_onboarding_no_data') ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="tw-text-neutral-500 tw-mb-0"><?= _l('contac_assistant_onboarding_empty_state') ?></p>
                                <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <hr class="hr-panel-separator" />
                        <div class="clearfix"></div>
                        <div>
                            <?php echo form_open_multipart(admin_url('contactcenter/add_assistant')); ?>
                            <input type="hidden" name="id" value="<?= $assistants->id ?>" />
                            <input type="hidden" name="ai_token" value="<?= $assistants->ai_token ?>" />
                            <input type="hidden" name="ai_name" value="<?= $assistants->ai_name ?>" />
                            <input type="hidden" name="vector_id" value="<?= $assistants->vector_id ?>" />

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?= _l("contact_assistant_ai_name"); ?></label>
                                        <input type="text" class="form-control" name='ai_name' value="<?= $assistants->ai_name ?>">
                                    </div>
                                </div>
                                <?php if (is_admin()) {  ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?= _l("contac_assistent_model"); ?></label>
                                            <select class="form-control" name="model" required>
                                                <option value="gpt-4o-mini" <?= ($assistants->model == "gpt-4o-mini" ? "selected" : "") ?>>Standard</option>
                                                <option value="gpt-4o" <?= ($assistants->model == "gpt-4o" ? "selected" : "") ?>>Advanced</option>
                                                <option value="gpt-4.1" <?= ($assistants->model == "gpt-4.1" ? "selected" : "") ?>>Pro</option>
                                                <option value="gpt-4.1-mini" <?= ($assistants->model == "gpt-4.1-mini" ? "selected" : "") ?>>Lite</option>
                                                <option value="gpt-4.1-nano" <?= ($assistants->model == "gpt-4.1-nano" ? "selected" : "") ?>>Nano</option>

                                                <!-- Novos modelos -->
                                                <option value="gpt-5.1" <?= ($assistants->model == "gpt-5.1" ? "selected" : "") ?>>Next</option>
                                                <option value="gpt-5-pro" <?= ($assistants->model == "gpt-5-pro" ? "selected" : "") ?>>Ultra</option>
                                            </select>
                                        </div>
                                    </div>

                                <?php } ?>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?= _l("contac_assistent_base_knowledge"); ?></label>
                                        <input type="file" class="form-control" name="file" accept=".txt,.pdf,.docx,.csv,.json,.md" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6><?= str_replace("vs_", "inxx_", $assistants->vector_id)  ?></h6>
                                </div>
                                <div class="col-md-12 tw-flex tw-flex-wrap tw-gap-2 tw-mb-4">
                                    <?php foreach ($files as $file) { ?>
                                        <div id="file_<?= $file->id; ?>">
                                            <button type="button" onclick="delete_file_cectorstore('<?= $file->id; ?>')" class="btn btn-default" data-toggle='tooltip' data-title='<?= _l("delete"); ?>'>
                                                <?= $file->files; ?> <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h4><?= _l("contac_assistent_function"); ?></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 tw-mb-4 ">

                                    <?php
                                    $functions = json_decode($assistants->functions ?? '[]', true); // Garante que functions é um array
                                    ?>

                                    <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                        <label><?= _l("contac_assistent_function_get_lead_info"); ?></label>
                                        <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_assistent_function_get_lead_info_description"); ?>'>
                                            <input type='checkbox' name='functions[]' class='onoffswitch-checkbox' value='get_lead_info' id='get_lead_info'
                                                <?= in_array('get_lead_info', $functions) ? 'checked' : '' ?>>
                                            <label class='onoffswitch-label' for='get_lead_info'></label>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                        <label><?= _l("contac_assistent_function_get_lead_context"); ?></label>
                                        <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_assistent_function_get_lead_context_description"); ?>'>
                                            <input type='checkbox' name='functions[]' class='onoffswitch-checkbox' value='get_lead_context' id='get_lead_context'
                                                <?= in_array('get_lead_context', $functions) ? 'checked' : '' ?>>
                                            <label class='onoffswitch-label' for='get_lead_context'></label>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                        <label><?= _l("contac_assistent_function_manage_conversation"); ?></label>
                                        <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_assistent_function_manage_conversation_description"); ?>'>
                                            <input type='checkbox' name='functions[]' class='onoffswitch-checkbox' value='manage_conversation' id='manage_conversation'
                                                <?= in_array('manage_conversation', $functions) ? 'checked' : '' ?>>
                                            <label class='onoffswitch-label' for='manage_conversation'></label>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                        <label><?= _l("contac_assistent_function_update_leads"); ?></label>
                                        <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_assistent_function_update_leads"); ?>'>
                                            <input type='checkbox' name='functions[]' class='onoffswitch-checkbox' onclick="aviso_lead(this,'<?= _l('contac_assistent_aviso_update_leads') ?>')" value='update_leads' id='update_leads'
                                                <?= in_array('update_leads', $functions) ? 'checked' : '' ?>>
                                            <label class='onoffswitch-label' for='update_leads'></label>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                        <label><?= _l("contac_assistent_function_agendar"); ?></label>
                                        <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_assistent_function_agendar"); ?>'>
                                            <input type='checkbox' name='functions[]' class='onoffswitch-checkbox' value='get_horario_agenda' id='get_horario_agenda'
                                                <?= in_array('get_horario_agenda', $functions) ? 'checked' : '' ?>>
                                            <label class='onoffswitch-label' for='get_horario_agenda'></label>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                        <label><?= _l("contac_assistent_function_create_contract"); ?></label>
                                        <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_assistent_function_create_contract"); ?>'>
                                            <input type='checkbox' name='functions[]' class='onoffswitch-checkbox' value='create_contract' id='create_contract'
                                                <?= in_array('create_contract', $functions) ? 'checked' : '' ?>>
                                            <label class='onoffswitch-label' for='create_contract'></label>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                        <label><?= _l("contac_assistent_function_get_tabela_precos"); ?></label>
                                        <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_assistent_function_get_tabela_precos"); ?>'>
                                            <input type='checkbox' name='functions[]' class='onoffswitch-checkbox' value='get_tabela_precos' id='get_tabela_precos'
                                                <?= in_array('get_tabela_precos', $functions) ? 'checked' : '' ?>>
                                            <label class='onoffswitch-label' for='get_tabela_precos'></label>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                        <label><?= _l("contac_assistent_function_open_ticket"); ?></label>
                                        <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_assistent_function_open_ticket"); ?>'>
                                            <input type='checkbox' name='functions[]' class='onoffswitch-checkbox' value='open_ticket' id='open_ticket'
                                                <?= in_array('open_ticket', $functions) ? 'checked' : '' ?>>
                                            <label class='onoffswitch-label' for='open_ticket'></label>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                        <label><?= _l("contac_assistent_function_get_faturas"); ?></label>
                                        <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_assistent_function_get_faturas"); ?>'>
                                            <input type='checkbox' name='functions[]' class='onoffswitch-checkbox' value='get_faturas_axiom' id='get_faturas_axiom'
                                                <?= in_array('get_faturas_axiom', $functions) ? 'checked' : '' ?>>
                                            <label class='onoffswitch-label' for='get_faturas_axiom'></label>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                        <label><?= _l("contac_assistent_function_send_media"); ?></label>
                                        <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_assistent_function_send_media_description"); ?>'>
                                            <input type='checkbox' name='functions[]' class='onoffswitch-checkbox' value='send_media' id='send_media'
                                                <?= in_array('send_media', $functions) ? 'checked' : '' ?>>
                                            <label class='onoffswitch-label' for='send_media'></label>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-xs-6 md:tw-border-r md:tw-border-solid md:tw-border-neutral-300 last:tw-border-r-0">
                                        <label><?= _l("contac_assistent_function_create_group_chat"); ?></label>
                                        <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_assistent_function_create_group_chat_description"); ?>'>
                                            <input type='checkbox' name='functions[]' class='onoffswitch-checkbox' value='create_group_chat' id='create_group_chat'
                                                <?= in_array('create_group_chat', $functions) ? 'checked' : '' ?>>
                                            <label class='onoffswitch-label' for='create_group_chat'></label>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <?php if (get_modules_active("sankhya")) { //modulo sankhya 
                            ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4><?= _l("contac_assistent_modulo_sankhya"); ?></h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 tw-flex tw-flex-wrap tw-gap-2 tw-mb-4 ">
                                        <div>
                                            <label><?= _l("contac_assistent_function_get_faturas_sankhya"); ?></label>
                                            <div class='onoffswitch' data-toggle='tooltip' data-title='<?= _l("contac_assistent_function_get_faturas_sankhya"); ?>'>
                                                <input type='checkbox' name='functions[]' class='onoffswitch-checkbox' value='get_faturas_sankhya' id='get_faturas_sankhya'
                                                    <?= in_array('get_faturas_sankhya', $functions) ? 'checked' : '' ?>>
                                                <label class='onoffswitch-label' for='get_faturas_sankhya'></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>



                            <div class="col-md-12">
                                <div class="row available_merge_fields_container">
                                    <p class="bold text-right no-mbot"><a href="#"
                                            onclick="slideToggle('.avilable_merge_fields'); return false;"><?php echo _l('available_merge_fields'); ?></a>
                                    </p>

                                    <div class="col-md-12 avilable_merge_fields mtop15 hide">
                                        <?php
                                        $mergeLooped = [];

                                        foreach ($available_merge_fields as $field) {

                                            foreach ($field as $key => $val) {

                                                if ($key == "leads") {

                                                    echo '<div class="col-md-6 ">';
                                                    echo '<h5 class="bold tw-text-base tw-rounded-lg tw-bg-neutral-50 tw-py-2 tw-px-3">' . ucwords(str_replace(['-', '_'], ' ', $key)) . '</h5>';


                                                    foreach ($val as $_field) {

                                                        if (
                                                            count($_field['available']) == 0
                                                            && isset($_field['templates']) && in_array($template->slug, $_field['templates'])
                                                        ) {
                                                            // Fake data to simulate foreach loop and check the templates key for the available slugs
                                                            $_field['available'][] = '1';
                                                        }
                                                        foreach ($_field['available'] as $_available) {
                                                            if (($_available == "leads" || isset($_field['templates']) && in_array($template->slug, $_field['templates'])) && !in_array($template->slug, $_field['exclude'] ?? []) && !in_array($_field['name'], $mergeLooped)) {
                                                                $mergeLooped[] = $_field['name'];
                                                                echo '<p>' . $_field['name'];
                                                                echo '<span class="pull-right"><a href="#" class="add_merge_field">';
                                                                echo $_field['key'];
                                                                echo '</a>';
                                                                echo '</span>';
                                                                echo '</p>';
                                                            }
                                                        }
                                                    }
                                                    echo '</div>';
                                                }
                                            }
                                        }

                                        //status
                                        echo '<div class="col-md-6 ">';
                                        echo '<h5 class="bold tw-text-base tw-rounded-lg tw-bg-neutral-50 tw-py-2 tw-px-3">' . _l("lead_status") . '</h5>';
                                        foreach ($leads_status as $_field) {
                                            echo '<p>' . $_field['name'];
                                            echo '<span class="pull-right"><a href="#" class="add_merge_field">';
                                            echo $_field['id'];
                                            echo '</a>';
                                            echo '</span>';
                                            echo '</p>';
                                        }

                                        echo '</div>';
                                        ?>

                                        <!-- Staff Members for manage_conversation -->
                                        <?php if (isset($staff_members) && !empty($staff_members)) { ?>
                                            <div class="col-md-6">
                                                <h5 class="bold tw-text-base tw-rounded-lg tw-bg-neutral-50 tw-py-2 tw-px-3"><?= _l("contac_assistent_staff_members"); ?></h5>
                                                <p style="font-size: 11px; color: #666; margin-bottom: 10px;"><?= _l("contac_assistent_staff_members_description"); ?></p>
                                                <?php foreach ($staff_members as $staff) { ?>
                                                    <p style="margin-bottom: 5px;">
                                                        <?= get_staff_full_name($staff['staffid']); ?>
                                                        <span class="pull-right" style="font-weight: bold; color: #007bff;">
                                                            ID: <?= $staff['staffid']; ?>
                                                        </span>
                                                    </p>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>

                                    </div>
                                </div>
                            </div>

                            <!-- Interactive manage_conversation builder -->
                            <div class="row">
                                <div class="col-md-12">
                                    <?php 
                                    $is_manage_conversation_active = isset($functions) && in_array('manage_conversation', $functions);
                                    ?>
                                    <div id="mc_builder_container" style="background-color: #f8f9fa; padding: 20px; border: 1px solid #e0e0e0; border-radius: 4px; margin-top: 20px; <?= !$is_manage_conversation_active ? 'opacity: 0.6;' : ''; ?>">
                                        <h5 style="color: #333; margin-bottom: 20px;">
                                            <i class="fa fa-cog"></i> <?= _l("contac_assistent_manage_conversation_builder"); ?>
                                            <?php if (!$is_manage_conversation_active) { ?>
                                                <small style="color: #ff6b6b; margin-left: 10px;">
                                                    <i class="fa fa-exclamation-triangle"></i> <?= _l("contac_assistent_enable_function_first"); ?>
                                                </small>
                                            <?php } ?>
                                        </h5>
                                            <p style="color: #666; margin-bottom: 20px; font-size: 13px;">
                                                <?= _l("contac_assistent_manage_conversation_builder_description"); ?>
                                            </p>
                                            
                                            <div style="background-color: #ffffff; padding: 20px; border-radius: 4px; border: 1px solid #ddd;">
                                                <div class="row">
                                                    <!-- AI Control -->
                                                    <div class="col-md-6" style="margin-bottom: 20px;">
                                                        <label style="font-weight: bold; color: #333; margin-bottom: 10px; display: block;">
                                                            <i class="fa fa-robot"></i> <?= _l("contac_assistent_ai_control"); ?>
                                                        </label>
                                                        <div style="margin-bottom: 10px;">
                                                            <label style="font-weight: normal; cursor: pointer; color: #333 !important; display: inline-flex; align-items: center;">
                                                                <input type="checkbox" id="mc_disable_ai" style="margin-right: 8px;" <?= !$is_manage_conversation_active ? 'disabled' : ''; ?>>
                                                                <?= _l("contac_assistent_disable_ai"); ?>
                                                            </label>
                                                        </div>
                                                        <div>
                                                            <label style="font-weight: normal; cursor: pointer; color: #333 !important; display: inline-flex; align-items: center;">
                                                                <input type="checkbox" id="mc_enable_ai" style="margin-right: 8px;" <?= !$is_manage_conversation_active ? 'disabled' : ''; ?>>
                                                                <?= _l("contac_assistent_enable_ai"); ?>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <!-- Change Staff Owner -->
                                                    <div class="col-md-6" style="margin-bottom: 20px;">
                                                        <label style="font-weight: bold; color: #333; margin-bottom: 10px; display: block;">
                                                            <i class="fa fa-user"></i> <?= _l("contac_assistent_change_staff_owner"); ?>
                                                        </label>
                                                        <select class="form-control" id="mc_change_staff" style="width: 100%;" <?= !$is_manage_conversation_active ? 'disabled' : ''; ?>>
                                                            <option value=""><?= _l("contac_assistent_no_change"); ?></option>
                                                            <?php if (isset($staff_members) && !empty($staff_members)) { ?>
                                                                <?php foreach ($staff_members as $staff) { ?>
                                                                    <option value="<?= $staff['staffid']; ?>">
                                                                        <?= get_staff_full_name($staff['staffid']); ?> (ID: <?= $staff['staffid']; ?>)
                                                                    </option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <!-- Change Status -->
                                                    <div class="col-md-6" style="margin-bottom: 20px;">
                                                        <label style="font-weight: bold; color: #333; margin-bottom: 10px; display: block;">
                                                            <i class="fa fa-flag"></i> <?= _l("contac_assistent_change_status"); ?>
                                                        </label>
                                                        <select class="form-control" id="mc_change_status" style="width: 100%;" <?= !$is_manage_conversation_active ? 'disabled' : ''; ?>>
                                                            <option value=""><?= _l("contac_assistent_no_change"); ?></option>
                                                            <?php if (isset($leads_status) && !empty($leads_status)) { ?>
                                                                <?php foreach ($leads_status as $status) { ?>
                                                                    <option value="<?= $status['id']; ?>">
                                                                        <?= $status['name']; ?> (ID: <?= $status['id']; ?>)
                                                                    </option>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                    <!-- Notification -->
                                                    <div class="col-md-6" style="margin-bottom: 20px;">
                                                        <label style="font-weight: bold; color: #333; margin-bottom: 10px; display: block;">
                                                            <i class="fa fa-bell"></i> <?= _l("contac_assistent_notification"); ?>
                                                        </label>
                                                        <div style="margin-bottom: 10px;">
                                                            <label style="font-weight: normal; cursor: pointer; color: #333 !important; display: inline-flex; align-items: center;">
                                                                <input type="checkbox" id="mc_send_notification" style="margin-right: 8px;" <?= !$is_manage_conversation_active ? 'disabled' : ''; ?>>
                                                                <?= _l("contac_assistent_send_notification"); ?>
                                                            </label>
                                                        </div>
                                                        <div id="mc_notification_staff_container" style="display: none; margin-top: 10px;">
                                                            <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">
                                                                <?= _l("contac_assistent_notify_staff"); ?>
                                                            </label>
                                                            <select class="form-control" id="mc_notification_staff" style="width: 100%;" <?= !$is_manage_conversation_active ? 'disabled' : ''; ?>>
                                                                <option value=""><?= _l("contac_assistent_current_owner"); ?></option>
                                                                <?php if (isset($staff_members) && !empty($staff_members)) { ?>
                                                                    <?php foreach ($staff_members as $staff) { ?>
                                                                        <option value="<?= $staff['staffid']; ?>">
                                                                            <?= get_staff_full_name($staff['staffid']); ?> (ID: <?= $staff['staffid']; ?>)
                                                                        </option>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div id="mc_notification_message_container" style="display: none; margin-top: 10px;">
                                                            <label style="font-size: 12px; color: #666; margin-bottom: 5px; display: block;">
                                                                <?= _l("contac_assistent_notification_message"); ?>
                                                            </label>
                                                            <input type="text" class="form-control" id="mc_notification_message" placeholder="<?= _l("contac_assistent_notification_message_placeholder"); ?>" style="width: 100%;" <?= !$is_manage_conversation_active ? 'disabled' : ''; ?>>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div style="background-color: #f5f5f5; padding: 15px; border-radius: 4px; margin-top: 15px; margin-bottom: 15px;">
                                                            <label style="font-weight: bold; color: #333; margin-bottom: 10px; display: block;">
                                                                <i class="fa fa-code"></i> <?= _l("contac_assistent_generated_code"); ?>
                                                            </label>
                                                            <pre id="mc_generated_code" style="background-color: #ffffff; padding: 10px; border-radius: 4px; font-size: 12px; margin: 0; min-height: 100px; border: 1px solid #ddd; color: #666;"><?= _l("contac_assistent_configure_options"); ?></pre>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <button type="button" class="btn btn-primary" id="mc_insert_code_btn" style="width: 100%;" <?= !$is_manage_conversation_active ? 'disabled' : ''; ?>>
                                                            <i class="fa fa-plus-circle"></i> <?= _l("contac_assistent_insert_into_instructions"); ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <hr class="hr-panel-separator" />

                            <div class="row">
                                <div class="col-md-12">
                                    <h4 style="color: #333;"><?= _l("contac_assistent_media_files"); ?></h4>
                                    <p style="color: #666;"><?= _l("contac_assistent_media_files_description"); ?></p>
                                </div>
                            </div>

                            <!-- Media Library Section -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="background-color: #f8f9fa; padding: 20px; border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 20px;">
                                        <h5 style="color: #333; margin-bottom: 15px;">
                                            <i class="fa fa-book"></i> <?= _l("contac_assistant_media_library"); ?>
                                            <small style="color: #666; font-weight: normal;"><?= _l("contac_assistant_media_library_description"); ?></small>
                                        </h5>
                                        
                                        <!-- Library Media List -->
                                        <div id="library_media_list" class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-4" style="min-height: 50px;">
                                            <?php if (isset($library_media) && !empty($library_media)) { ?>
                                                <?php 
                                                // Get current assistant media file paths to check if already added
                                                $assistant_media_paths = [];
                                                if (isset($media_files) && !empty($media_files)) {
                                                    foreach ($media_files as $am) {
                                                        $assistant_media_paths[] = $am->file_path;
                                                    }
                                                }
                                                ?>
                                                <?php foreach ($library_media as $lib_media) { 
                                                    $is_added = in_array($lib_media->file_path, $assistant_media_paths);
                                                ?>
                                                    <div id="library_media_<?= $lib_media->id; ?>" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; background-color: <?= $is_added ? '#d4edda' : '#ffffff'; ?>; border-radius: 4px; border: 1px solid <?= $is_added ? '#c3e6cb' : '#ddd'; ?>;">
                                                        <span style="font-weight: <?= $is_added ? 'bold' : 'normal'; ?>; color: #333;">
                                                            <?= htmlspecialchars($lib_media->file_name); ?>
                                                        </span>
                                                        <span style="font-size: 11px; color: #666;">(<?= strtoupper($lib_media->file_type); ?>)</span>
                                                        <?php if (!$is_added) { ?>
                                                            <button type="button" onclick="add_library_media(<?= $assistants->id; ?>, <?= $lib_media->id; ?>)" class="btn btn-xs btn-success" data-toggle='tooltip' data-title='<?= _l("contac_assistant_add_to_assistant"); ?>'>
                                                                <i class="fa fa-plus"></i> <?= _l("add"); ?>
                                                            </button>
                                                        <?php } else { ?>
                                                            <span class="badge badge-success" style="font-size: 10px;"><?= _l("contac_assistant_already_added"); ?></span>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <p class="text-muted"><?= _l("contac_assistant_no_library_media"); ?></p>
                                            <?php } ?>
                                        </div>
                                        
                                        <!-- Upload to Library -->
                                        <div class="form-group" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                                            <label style="color: #333; font-weight: bold;"><?= _l("contac_assistant_upload_to_library"); ?></label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="file" class="form-control" id="library_media_file_input" name="library_media_file" accept="image/*,audio/*,video/*,.mp3,.wav,.ogg,.m4a,.aac,.mp4,.avi,.mov,.wmv,.flv,.webm,.mkv,.jpg,.jpeg,.png,.gif,.webp">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="library_media_file_name" placeholder="<?= _l("contac_assistent_file_name_placeholder"); ?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-info" id="upload_library_media_btn">
                                                        <i class="fa fa-upload"></i> <?= _l("upload"); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Assistant-Specific Media Section -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="background-color: #ffffff; padding: 20px; border: 1px solid #e0e0e0; border-radius: 4px;">
                                        <h5 style="color: #333; margin-bottom: 15px;">
                                            <i class="fa fa-folder"></i> <?= _l("contac_assistant_assistant_media"); ?>
                                            <small style="color: #666; font-weight: normal;"><?= _l("contac_assistant_assistant_media_description"); ?></small>
                                        </h5>
                                        
                                        <div class="form-group">
                                            <label style="color: #333;"><?= _l("contac_assistent_upload_media"); ?></label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="file" class="form-control" id="media_file_input" name="media_file" accept="image/*,audio/*,video/*,.mp3,.wav,.ogg,.m4a,.aac,.mp4,.avi,.mov,.wmv,.flv,.webm,.mkv,.jpg,.jpeg,.png,.gif,.webp">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" id="media_file_name" placeholder="<?= _l("contac_assistent_file_name_placeholder"); ?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-primary" id="upload_media_btn">
                                                        <i class="fa fa-upload"></i> <?= _l("upload"); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tw-mt-4">
                                            <h6 style="color: #333; margin-bottom: 15px;"><?= _l("contac_assistent_uploaded_files"); ?></h6>
                                            <div id="media_files_list" class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-4">
                                                <?php if (isset($media_files) && !empty($media_files)) { ?>
                                                    <?php foreach ($media_files as $media) { ?>
                                                        <div id="media_file_<?= $media->id; ?>" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; background-color: #f5f5f5; border-radius: 4px; border: 1px solid #ddd;">
                                                            <span class="tw-cursor-pointer tw-font-semibold tw-text-primary add_media_field" data-variable="<?= htmlspecialchars($media->variable_name); ?>" title="<?= _l("contac_assistent_click_to_insert"); ?>" style="color: #007bff;">
                                                                <?= htmlspecialchars($media->file_name); ?>
                                                            </span>
                                                            <span style="font-size: 11px; color: #666;">(<?= strtoupper($media->file_type); ?>)</span>
                                                            <button type="button" onclick="delete_media_file('<?= $media->id; ?>')" class="btn btn-xs btn-danger" data-toggle='tooltip' data-title='<?= _l("delete"); ?>'>
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <p class="text-muted"><?= _l("contac_assistent_no_media_files"); ?></p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="hr-panel-separator" />
                            <h5><?= _l("contac_assistent_obs"); ?></h5>
                            <h5><?= ($assistants->last_update ? "Update: " . _dt($assistants->last_update) . " - " . get_staff_full_name($assistants->staffid_update) : "") ?> </h5>
                            <div class="form-group">
                                <label><?= _l("contac_assistent_instructions"); ?></label>
                                <div class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-3">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-default instructions-mode-btn active" data-mode="guided">
                                            <i class="fa fa-th-list"></i> <?= _l("contac_instructions_mode_guided"); ?>
                                        </button>
                                        <button type="button" class="btn btn-default instructions-mode-btn" data-mode="advanced">
                                            <i class="fa fa-code"></i> <?= _l("contac_instructions_mode_advanced"); ?>
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-info btn-sm" id="btnOpenFullInstructions" title="<?= _l("contac_instructions_open_full_desc"); ?>">
                                        <i class="fa fa-expand"></i> <?= _l("contac_instructions_open_full"); ?>
                                    </button>
                                    <?php
                                    $has_update = isset($functions) && in_array('update_leads', $functions);
                                    $has_agenda_btn = isset($functions) && in_array('get_horario_agenda', $functions);
                                    $has_other = isset($functions) && (in_array('get_lead_info', $functions) || in_array('get_lead_context', $functions) || in_array('create_group_chat', $functions));
                                    $has_any = $has_update || $has_agenda_btn || $has_other;
                                    ?>
                                    <?php if ($has_update): ?>
                                    <button type="button" class="btn btn-success btn-sm btn-func-insert" data-func="update_leads" data-toggle="modal" data-target="#updateLeadsModal" title="<?= _l("contac_func_update_leads"); ?>">
                                        <i class="fa fa-user-edit"></i> <?= _l("contac_func_btn_update"); ?>
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($has_agenda_btn): ?>
                                    <button type="button" class="btn btn-success btn-sm btn-func-insert" data-func="get_horario_agenda" data-toggle="modal" data-target="#updateLeadsModal" title="<?= _l("contac_func_get_horario_agenda"); ?>">
                                        <i class="fa fa-calendar"></i> <?= _l("contac_func_btn_check_times"); ?>
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm btn-func-insert" data-func="set_horario_agenda" data-toggle="modal" data-target="#updateLeadsModal" title="<?= _l("contac_func_set_horario_agenda"); ?>">
                                        <i class="fa fa-calendar-check-o"></i> <?= _l("contac_func_btn_schedule"); ?>
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($has_other): ?>
                                    <button type="button" class="btn btn-default btn-sm btn-func-insert" data-func="" data-toggle="modal" data-target="#updateLeadsModal" title="<?= _l("contac_insert_update_leads_desc"); ?>">
                                        <i class="fa fa-ellipsis-h"></i> <?= _l("contac_insert_update_leads"); ?>
                                    </button>
                                    <?php endif; ?>
                                    <?php if (!$has_any): ?>
                                    <button type="button" class="btn btn-default btn-sm" disabled title="<?= _l("contac_func_insert_enable_first"); ?>">
                                        <i class="fa fa-database"></i> <?= _l("contac_insert_update_leads"); ?>
                                    </button>
                                    <small class="text-muted tw-ml-1">(<?= _l("contac_func_insert_enable_first"); ?>)</small>
                                    <?php endif; ?>
                                </div>

                                <!-- Guided mode: wizard-style steps -->
                                <div id="instructionsGuidedWrap" class="instructions-mode-content">
                                    <div class="inst-wizard">
                                        <div class="inst-wizard-steps mb-4">
                                            <div class="inst-step-dots" id="instStepDots"></div>
                                            <div class="inst-step-counter text-muted small mt-2" id="instStepCounter"></div>
                                        </div>
                                        <div class="inst-wizard-panels">
                                            <div class="inst-wizard-panel active" data-step="1"><h6 class="inst-step-title"><?= _l("contac_inst_section_context"); ?></h6><div class="inst-presets-container"></div><textarea class="form-control inst-section" id="inst_context" data-header="Regras de contexto e primeiro contato:" rows="12" placeholder="Quando o usuário abordar... / O agente deve seguir... / Quando o usuário interagir pela segunda vez..."></textarea></div>
                                            <div class="inst-wizard-panel" data-step="2"><h6 class="inst-step-title"><?= _l("contac_inst_section_objective"); ?></h6><div class="inst-presets-container"></div><textarea class="form-control inst-section" id="inst_objective" data-header="Objetivo:" rows="12"></textarea></div>
                                            <div class="inst-wizard-panel" data-step="3"><h6 class="inst-step-title"><?= _l("contac_inst_section_target"); ?></h6><div class="inst-presets-container"></div><textarea class="form-control inst-section" id="inst_target" data-header="Público-alvo:" rows="12"></textarea></div>
                                            <div class="inst-wizard-panel" data-step="4"><h6 class="inst-step-title"><?= _l("contac_inst_section_style"); ?></h6><div class="inst-presets-container"></div><textarea class="form-control inst-section" id="inst_style" data-header="Estilo de Comunicação:" rows="12" placeholder="Use [nome] como nome do Agente;&#10;Não ultrapasse 200 caracteres;&#10;Usar português do Brasil..."></textarea></div>
<div class="inst-wizard-panel" data-step="5">
    <h6 class="inst-step-title"><?= _l("contac_inst_section_script"); ?></h6>
    <div class="inst-presets-container"></div>
    <div class="tw-mb-3">
        <button type="button" class="btn btn-info btn-sm btn-block" id="btnGenerateScript" title="<?= _l("contac_inst_script_generate_desc"); ?>">
            <i class="fa fa-magic"></i> <?= _l("contac_inst_script_generate"); ?>
        </button>
    </div>
    <textarea class="form-control inst-section" id="inst_script" data-header="Script para o Agente:" rows="14" placeholder="1 - Saudação...&#10;2 - Perguntas...&#10;3 - Falar sobre tratamentos...&#10;4 - Oferecer agendamento..."></textarea>
</div>
                                            <div class="inst-wizard-panel" data-step="6"><h6 class="inst-step-title"><?= _l("contac_inst_section_company"); ?></h6><div class="inst-presets-container"></div><textarea class="form-control inst-section" id="inst_company" data-header="Apresentação Sobre a Empresa" rows="12"></textarea></div>
<div class="inst-wizard-panel" data-step="7">
    <h6 class="inst-step-title"><?= _l("contac_inst_section_examples"); ?></h6>
    <div class="inst-presets-container"></div>
    <div class="tw-mb-3">
        <button type="button" class="btn btn-info btn-sm btn-block" id="btnGenerateExamples" title="<?= _l("contac_inst_examples_generate_desc"); ?>">
            <i class="fa fa-magic"></i> <?= _l("contac_inst_examples_generate"); ?>
        </button>
    </div>
    <textarea class="form-control inst-section" id="inst_examples" data-header="Exemplos de interação:" rows="12" placeholder="Usuário: ...&#10;Agente: ..."></textarea>
</div>

<!-- Contact Selection Modal -->
<div class="modal fade" id="contactSelectionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= _l("contac_select_contacts_for_examples"); ?></h4>
            </div>
            <div class="modal-body">
                <p class="text-muted"><?= _l("contac_select_contacts_desc"); ?></p>
                <div class="form-group">
                    <input type="text" class="form-control" id="contactSearchInput" placeholder="<?= _l("search"); ?>...">
                </div>
                <div class="contact-list-container" style="max-height: 300px; overflow-y: auto;">
                    <div id="contactListLoading" class="text-center" style="display:none; padding: 20px;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                    </div>
                    <ul class="list-group" id="contactSelectionList">
                        <!-- Contacts will be loaded here -->
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l("close"); ?></button>
                <button type="button" class="btn btn-primary" id="btnProcessSelectedContacts"><?= _l("contac_generate_examples"); ?></button>
            </div>
        </div>
    </div>
</div>
                                            <div class="inst-wizard-panel" data-step="8"><h6 class="inst-step-title"><?= _l("contac_inst_section_restrictions"); ?></h6><div class="inst-presets-container"></div><textarea class="form-control inst-section" id="inst_restrictions" data-header="Restrições para o agente:" rows="12"></textarea></div>
                                            <div class="inst-wizard-panel" data-step="9"><h6 class="inst-step-title"><?= _l("contac_inst_section_services"); ?></h6><div class="inst-presets-container"></div><textarea class="form-control inst-section" id="inst_services" data-header="Tratamentos realizados:" rows="12" placeholder="- Item 1&#10;- Item 2"></textarea></div>
                                            <div class="inst-wizard-panel" data-step="10"><h6 class="inst-step-title"><?= _l("contac_inst_section_pricing"); ?></h6><div class="inst-presets-container"></div><textarea class="form-control inst-section" id="inst_pricing" data-header="Tabela de tratamentos e valores apenas para consulta do agente:" rows="12" placeholder="Procedimento,Valor,Pacote;"></textarea></div>
                                            <div class="inst-wizard-panel" data-step="11">
    <h6 class="inst-step-title"><?= _l("contac_inst_section_location"); ?></h6>
    <div class="inst-presets-container"></div>
    
    <div class="form-group tw-mb-2">
        <label for="inst_location_cep" class="control-label text-muted small"><?= _l('cc_onboarding_cep'); ?></label>
        <div class="input-group input-group-sm" style="max-width: 200px;">
            <input type="text" class="form-control" id="inst_location_cep" placeholder="00000-000" maxlength="9">
            <span class="input-group-addon" id="cepLoading" style="display:none;"><i class="fa fa-spinner fa-spin"></i></span>
        </div>
    </div>

    <textarea class="form-control inst-section" id="inst_location" data-header="Endereço e horário:" rows="12" placeholder="📍Endereço..."></textarea>
</div>
                                            <div class="inst-wizard-panel" data-step="12"><h6 class="inst-step-title"><?= _l("contac_inst_section_faq"); ?></h6><div class="inst-presets-container"></div><textarea class="form-control inst-section" id="inst_faq" data-header="Perguntas frequentes:" rows="12" placeholder="1. Pergunta? Resposta"></textarea></div>
                                        <?php $has_agenda = isset($functions) && (in_array('get_horario_agenda', $functions) || in_array('set_horario_agenda', $functions)); ?>
                                        <?php if ($has_agenda): ?>
                                            <div class="inst-wizard-panel" data-step="13">
                                                <h6 class="inst-step-title"><?= _l("contac_inst_section_scheduling"); ?></h6>
                                                <div class="inst-scheduling-actions tw-mb-3">
                                                    <button type="button" class="btn btn-success btn-sm" id="btnInsertSchedulingDefault" title="<?= _l("contac_inst_scheduling_insert_default"); ?>">
                                                        <i class="fa fa-file-text-o"></i> <?= _l("contac_inst_scheduling_insert_default"); ?>
                                                    </button>
                                                    <button type="button" class="btn btn-default btn-sm" id="btnClearScheduling" title="<?= _l("contac_inst_scheduling_clear"); ?>">
                                                        <i class="fa fa-eraser"></i> <?= _l("contac_inst_scheduling_clear"); ?>
                                                    </button>
                                                    <span class="text-muted small tw-ml-2"><?= _l("contac_inst_scheduling_use_default"); ?></span>
                                                </div>
                                                <div class="inst-presets-container"></div><textarea class="form-control inst-section" id="inst_scheduling" data-header="Fluxo de Agendamento:" rows="12" placeholder="Passos para get_horario_agenda, set_horario_agenda..."></textarea>
                                            </div>
                                            <div class="inst-wizard-panel" data-step="14"><h6 class="inst-step-title"><?= _l("contac_inst_section_extra"); ?></h6><div class="inst-presets-container"></div><textarea class="form-control inst-section" id="inst_extra" data-header="Instruções adicionais:" rows="12"></textarea></div>
                                        <?php else: ?>
                                            <div class="inst-wizard-panel" data-step="13"><h6 class="inst-step-title"><?= _l("contac_inst_section_extra"); ?></h6><div class="inst-presets-container"></div><textarea class="form-control inst-section" id="inst_extra" data-header="Instruções adicionais:" rows="12"></textarea></div>
                                        <?php endif; ?>
                                        </div>
                                        <div class="inst-wizard-nav mt-3 tw-flex tw-gap-2 tw-items-center">
                                            <button type="button" class="btn btn-default" id="instWizardPrev"><i class="fa fa-chevron-left"></i> <?= _l("dt_paginate_previous"); ?></button>
                                            <button type="button" class="btn btn-primary" id="instWizardNext"><?= _l("next"); ?> <i class="fa fa-chevron-right"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Advanced mode: single textarea -->
                                <div id="instructionsAdvancedWrap" class="instructions-mode-content" style="display:none">
                                    <textarea class="form-control" name="instructions" rows="25" id="instructions" required><?= $assistants->instructions ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary"><?= _l("save"); ?></button>
                        </div>
                        <?php echo form_close(); ?>

                        <!-- Version History Section -->
                        <div class="panel_s tw-mt-6">
                            <div class="panel-body">
                                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center tw-mb-4">
                                    <i class="fa fa-history tw-mr-2"></i>
                                    <?= _l('contac_assistant_version_history'); ?>
                                </h4>
                                
                                <?php if (!empty($versions)) { ?>
                                    <div class="table-responsive">
                                        <table class="table dt-table table-assistant-versions" data-order-col="0" data-order-type="desc">
                                            <thead>
                                                <tr>
                                                    <th><?= _l('contac_assistant_version_number'); ?></th>
                                                    <th><?= _l('contac_assistant_version_date'); ?></th>
                                                    <th><?= _l('contac_assistant_version_changed_by'); ?></th>
                                                    <th><?= _l('contac_assistant_version_changes'); ?></th>
                                                    <th><?= _l('contac_assistant_version_actions'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($versions as $version) { ?>
                                                    <tr>
                                                        <td>
                                                            <strong>v<?= $version->version_number; ?></strong>
                                                        </td>
                                                        <td>
                                                            <?= _dt($version->created_at); ?>
                                                        </td>
                                                        <td>
                                                            <?= get_staff_full_name($version->created_by); ?>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <?= !empty($version->change_summary) ? htmlspecialchars($version->change_summary) : _l('contac_assistant_no_changes'); ?>
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <button 
                                                                type="button" 
                                                                class="btn btn-info btn-xs restore-version-btn" 
                                                                data-assistant-id="<?= $assistants->id; ?>" 
                                                                data-version-id="<?= $version->id; ?>"
                                                                data-version-number="<?= $version->version_number; ?>"
                                                                onclick="restoreVersion(<?= $assistants->id; ?>, <?= $version->id; ?>, <?= $version->version_number; ?>)">
                                                                <i class="fa fa-undo"></i> <?= _l('contac_assistant_restore'); ?>
                                                            </button>
                                                            <button 
                                                                type="button" 
                                                                class="btn btn-default btn-xs view-version-btn" 
                                                                data-version-id="<?= $version->id; ?>"
                                                                onclick="viewVersion(<?= $version->id; ?>)">
                                                                <i class="fa fa-eye"></i> <?= _l('contac_assistant_view'); ?>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } else { ?>
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> <?= _l('contac_assistant_no_versions'); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Script Auto-Update Section -->
                        <div class="panel_s tw-mt-6" id="script-updates">
                            <div class="panel-body">
                                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center tw-mb-4">
                                    <i class="fa fa-refresh tw-mr-2"></i>
                                    <?= _l('contac_script_autoupdate_title'); ?>
                                </h4>
                                <p class="text-muted tw-mb-4"><?= _l('contac_script_autoupdate_desc'); ?></p>

                                <div class="script-autoupdate-panel">
                                    <div class="panel-heading-row">
                                        <h5><i class="fa fa-cog"></i> <?= _l('settings'); ?></h5>
                                        <?php
                                            $au = $script_autoupdate_settings;
                                            $au_last_run = !empty($au['last_run_date']) ? $au['last_run_date'] : '';
                                        ?>
                                        <small class="text-muted">
                                            <?= _l('contac_script_autoupdate_last_run'); ?>:
                                            <strong><?= $au_last_run ? _dt($au_last_run) : _l('contac_script_autoupdate_never'); ?></strong>
                                        </small>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-col" style="max-width: 60px;">
                                            <label>
                                                <input type="checkbox" id="au_enabled" value="1" <?= (!empty($au['enabled']) && $au['enabled'] == 1) ? 'checked' : ''; ?>>
                                                <?= _l('contac_script_autoupdate_enable'); ?>
                                            </label>
                                        </div>
                                    </div>

                                    <div id="au_settings_fields" style="<?= (empty($au['enabled']) || $au['enabled'] != 1) ? 'display:none;' : ''; ?>">
                                        <div class="form-row">
                                            <div class="form-col">
                                                <label><?= _l('contac_script_autoupdate_frequency'); ?></label>
                                                <div class="input-group">
                                                    <select class="form-control" id="au_frequency_days">
                                                        <?php foreach ([3, 7, 14, 30] as $d): ?>
                                                        <option value="<?= $d; ?>" <?= (isset($au['frequency_days']) && $au['frequency_days'] == $d) ? 'selected' : ''; ?>><?= $d; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <span class="input-group-addon"><?= _l('contac_script_autoupdate_frequency_days'); ?></span>
                                                </div>
                                            </div>
                                            <div class="form-col">
                                                <label><?= _l('contac_script_autoupdate_lead_count'); ?></label>
                                                <input type="number" class="form-control" id="au_lead_count" value="<?= isset($au['lead_count']) ? (int) $au['lead_count'] : 50; ?>" min="1" max="200">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-col">
                                                <label><?= _l('contac_script_autoupdate_lead_status'); ?></label>
                                                <select class="form-control" id="au_lead_status_id">
                                                    <option value="0"><?= _l('contac_script_autoupdate_lead_status_all'); ?></option>
                                                    <?php if (!empty($leads_status)): foreach ($leads_status as $st): ?>
                                                    <option value="<?= $st['id']; ?>" <?= (isset($au['lead_status_id']) && $au['lead_status_id'] == $st['id']) ? 'selected' : ''; ?>><?= $st['name']; ?></option>
                                                    <?php endforeach; endif; ?>
                                                </select>
                                            </div>
                                            <div class="form-col">
                                                <label><?= _l('contac_script_autoupdate_notify'); ?></label>
                                                <select class="form-control" id="au_notify_staff_id">
                                                    <option value="0">--</option>
                                                    <?php if (!empty($staff_members)): foreach ($staff_members as $sm): ?>
                                                    <option value="<?= $sm['staffid']; ?>" <?= (isset($au['notify_staff_id']) && $au['notify_staff_id'] == $sm['staffid']) ? 'selected' : ''; ?>><?= $sm['firstname'] . ' ' . $sm['lastname']; ?></option>
                                                    <?php endforeach; endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="btn-row">
                                            <button type="button" class="btn btn-primary btn-sm" id="btnSaveAutoUpdate">
                                                <i class="fa fa-save"></i> <?= _l('contac_script_autoupdate_save'); ?>
                                            </button>
                                            <button type="button" class="btn btn-warning btn-sm" id="btnRunAutoUpdateNow">
                                                <i class="fa fa-play"></i> <?= _l('contac_script_autoupdate_run_now'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pending Updates -->
                                <div class="tw-mt-5">
                                    <h5 class="tw-font-semibold"><i class="fa fa-bell"></i> <?= _l('contac_script_updates_pending_title'); ?></h5>
                                    <div id="pendingUpdatesContainer">
                                    <?php if (!empty($pending_script_updates)): ?>
                                        <?php foreach ($pending_script_updates as $pu): ?>
                                        <div class="script-update-card" data-update-id="<?= $pu->id; ?>">
                                            <div class="update-meta">
                                                <span><i class="fa fa-users"></i> <?= _l('contac_script_update_contacts_analyzed'); ?>: <strong><?= $pu->contacts_analyzed; ?></strong></span>
                                                <span><i class="fa fa-clock-o"></i> <?= _l('contac_script_update_created_at'); ?>: <strong><?= _dt($pu->created_at); ?></strong></span>
                                            </div>
                                            <div class="update-summary"><?= nl2br(htmlspecialchars($pu->summary)); ?></div>
                                            <div class="update-actions">
                                                <button type="button" class="btn btn-success btn-sm btn-approve-update" data-id="<?= $pu->id; ?>">
                                                    <i class="fa fa-check"></i> <?= _l('contac_script_update_approve'); ?>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm btn-reject-update" data-id="<?= $pu->id; ?>">
                                                    <i class="fa fa-times"></i> <?= _l('contac_script_update_reject'); ?>
                                                </button>
                                                <button type="button" class="btn btn-default btn-sm btn-toggle-diff" data-id="<?= $pu->id; ?>">
                                                    <i class="fa fa-exchange"></i> <?= _l('contac_script_update_view_diff'); ?>
                                                </button>
                                            </div>
                                            <div class="script-diff-container" id="diff-<?= $pu->id; ?>">
                                                <div class="diff-cols">
                                                    <div class="diff-col">
                                                        <h6><?= _l('contac_script_update_current'); ?></h6>
                                                        <div class="diff-content"><?= htmlspecialchars($pu->current_script); ?></div>
                                                    </div>
                                                    <div class="diff-col">
                                                        <h6><?= _l('contac_script_update_proposed'); ?></h6>
                                                        <div class="diff-content diff-proposed"><?= htmlspecialchars($pu->proposed_script); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted" id="noPendingMsg"><i class="fa fa-check-circle"></i> <?= _l('contac_script_update_no_pending'); ?></p>
                                    <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Update History -->
                                <?php if (!empty($script_update_history)): ?>
                                <div class="tw-mt-5">
                                    <h5 class="tw-font-semibold"><i class="fa fa-history"></i> <?= _l('contac_script_updates_history_title'); ?></h5>
                                    <?php foreach ($script_update_history as $hu): ?>
                                    <div class="script-update-card status-<?= $hu->status; ?>">
                                        <div class="update-meta">
                                            <span>
                                                <span class="update-status-badge badge-<?= $hu->status; ?>">
                                                    <?= _l('contac_script_update_status_' . $hu->status); ?>
                                                </span>
                                            </span>
                                            <span><i class="fa fa-users"></i> <?= $hu->contacts_analyzed; ?> <?= _l('contac_script_update_contacts_analyzed'); ?></span>
                                            <span><i class="fa fa-clock-o"></i> <?= _dt($hu->created_at); ?></span>
                                            <?php if ($hu->reviewed_by): ?>
                                            <span><i class="fa fa-user"></i> <?= _l('contac_script_update_reviewed_by'); ?>: <?= get_staff_full_name($hu->reviewed_by); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="update-summary"><?= nl2br(htmlspecialchars($hu->summary)); ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>

                            </div>
                        </div>

                        <!-- Version Details Modal -->
                        <div class="modal fade" id="versionDetailsModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title"><?= _l('contac_assistant_version_details'); ?> - <span id="versionNumber"></span></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong><?= _l('contact_assistant_ai_name'); ?>:</strong>
                                                <p id="versionName"></p>
                                            </div>
                                            <div class="col-md-6">
                                                <strong><?= _l('contac_assistent_model'); ?>:</strong>
                                                <p id="versionModel"></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <strong><?= _l('contac_assistent_instructions'); ?>:</strong>
                                                <pre id="versionInstructions" class="pre-scrollable" style="max-height: 300px; white-space: pre-wrap;"></pre>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <strong><?= _l('contac_assistent_function'); ?>:</strong>
                                                <pre id="versionFunctions" class="pre-scrollable" style="max-height: 200px;"></pre>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <strong><?= _l('contac_assistant_version_changes'); ?>:</strong>
                                                <p id="versionChanges" class="text-muted"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                                        <button type="button" class="btn btn-info" id="restoreFromModalBtn">
                                            <i class="fa fa-undo"></i> <?= _l('contac_assistant_restore'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Insert function block Modal -->
                        <div class="modal fade" id="updateLeadsModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><i class="fa fa-database"></i> <?= _l("contac_insert_update_leads"); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-muted small"><?= _l("contac_insert_update_leads_desc"); ?></p>
                                        <div class="form-group">
                                            <label class="bold"><?= _l("contac_func_what_to_do"); ?></label>
                                            <select class="form-control" id="funcBlockTypeSelect">
                                                <?php if (isset($functions)): ?>
                                                    <?php if (in_array('update_leads', $functions)): ?><option value="update_leads"><?= _l("contac_func_update_leads"); ?></option><?php endif; ?>
                                                    <?php if (in_array('get_lead_info', $functions)): ?><option value="get_lead_info"><?= _l("contac_func_get_lead_info"); ?></option><?php endif; ?>
                                                    <?php if (in_array('get_lead_context', $functions)): ?><option value="get_lead_context"><?= _l("contac_func_get_lead_context"); ?></option><?php endif; ?>
                                                    <?php if (in_array('get_horario_agenda', $functions)): ?><option value="get_horario_agenda"><?= _l("contac_func_get_horario_agenda"); ?></option><?php endif; ?>
                                                    <?php if (in_array('get_horario_agenda', $functions)): ?><option value="set_horario_agenda"><?= _l("contac_func_set_horario_agenda"); ?></option><?php endif; ?>
                                                    <?php if (in_array('create_group_chat', $functions)): ?><option value="create_group_chat"><?= _l("contac_func_create_group_chat"); ?></option><?php endif; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <!-- update_leads options -->
                                        <div id="funcBlockUpdateLeads" class="func-block-panel">
                                            <div class="form-group">
                                                <label><?= _l("contac_update_leads_status"); ?></label>
                                                <select class="form-control" id="updateLeadsStatusSelect">
                                                    <option value="">-- <?= _l("contac_update_leads_status"); ?> --</option>
                                                    <?php if (isset($leads_status) && !empty($leads_status)): ?>
                                                        <?php foreach ($leads_status as $st): ?>
                                                            <option value="<?= (int)$st['id']; ?>"><?= htmlspecialchars($st['name']); ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" id="updateLeadsIncludeLastStatus" checked> <?= _l("contac_update_leads_include_last_status"); ?></label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" id="updateLeadsIncludeLastcontact"> <?= _l("contac_update_leads_include_lastcontact"); ?></label>
                                            </div>
                                            <div class="form-group">
                                                <label><?= _l("contac_update_leads_save_field"); ?></label>
                                                <div class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-2">
                                                    <button type="button" class="btn btn-default btn-sm ul-preset" data-field="leads_motivo_do_investimento"><?= _l("contac_update_leads_preset_motivo"); ?></button>
                                                    <button type="button" class="btn btn-default btn-sm ul-preset" data-field="leads_ja_empreendeu"><?= _l("contac_update_leads_preset_empreendeu"); ?></button>
                                                    <button type="button" class="btn btn-default btn-sm ul-preset" data-field="leads_socio"><?= _l("contac_update_leads_preset_socio"); ?></button>
                                                    <button type="button" class="btn btn-default btn-sm ul-preset" data-field="leads_realidade_investimento"><?= _l("contac_update_leads_preset_realidade"); ?></button>
                                                    <button type="button" class="btn btn-default btn-sm ul-preset" data-field="email"><?= _l("contac_update_leads_preset_email"); ?></button>
                                                    <button type="button" class="btn btn-default btn-sm ul-preset" data-field="description"><?= _l("contac_update_leads_preset_description"); ?></button>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label><?= _l("contac_update_leads_custom_fields"); ?></label>
                                                <div id="updateLeadsCustomFieldsList"></div>
                                                <button type="button" class="btn btn-default btn-sm mt-2" id="updateLeadsAddField">
                                                    <i class="fa fa-plus"></i> <?= _l("contac_update_leads_add_field"); ?>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- get_lead_context options -->
                                        <div id="funcBlockGetLeadContext" class="func-block-panel" style="display:none">
                                            <div class="form-group">
                                                <label><?= _l("contac_func_context_how_much"); ?></label>
                                                <select class="form-control" id="contextLimit">
                                                    <option value="50"><?= _l("contac_func_context_50"); ?></option>
                                                    <option value="100"><?= _l("contac_func_context_100"); ?></option>
                                                    <option value="200"><?= _l("contac_func_context_200"); ?></option>
                                                    <option value="all"><?= _l("contac_func_context_all"); ?></option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- get_horario_agenda: check available times - from Instruction_Example.txt -->
                                        <div id="funcBlockGetHorario" class="func-block-panel" style="display:none">
                                            <div class="alert alert-info mb-3">
                                                <i class="fa fa-info-circle"></i> <?= _l("contac_func_get_horario_flow"); ?>
                                            </div>
                                            <div class="form-group">
                                                <label><?= _l("contac_func_get_horario_date"); ?></label>
                                                <input type="text" class="form-control" id="getHorarioDateInput" placeholder="2025-10-24">
                                                <p class="text-muted small mt-1 mb-0"><?= _l("contac_func_get_horario_date_help"); ?></p>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><?= _l("contac_func_get_horario_specific_hour"); ?></label>
                                                        <input type="text" class="form-control" id="getHorarioSpecificHour" placeholder="14:00">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><?= _l("contac_func_get_horario_after_hour"); ?></label>
                                                        <input type="text" class="form-control" id="getHorarioAfterHour" placeholder="14:00">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><?= _l("contac_func_get_horario_period"); ?></label>
                                                        <select class="form-control" id="getHorarioPeriod">
                                                            <option value=""><?= _l("contac_func_get_horario_period_none"); ?></option>
                                                            <option value="morning"><?= _l("contac_func_get_horario_period_morning"); ?></option>
                                                            <option value="afternoon"><?= _l("contac_func_get_horario_period_afternoon"); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label><?= _l("contac_func_get_horario_rejected"); ?></label>
                                                        <input type="text" class="form-control" id="getHorarioRejected" placeholder="14:00, 15:00">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- set_horario_agenda: no date - AI uses slot from get_horario_agenda -->
                                        <div id="funcBlockScheduleOnly" class="func-block-panel" style="display:none">
                                            <div class="alert alert-info mb-0">
                                                <i class="fa fa-info-circle"></i> <?= _l("contac_func_schedule_flow"); ?>
                                            </div>
                                        </div>
                                        <!-- create_group_chat: needs meeting date for group name -->
                                        <div id="funcBlockCreateGroup" class="func-block-panel" style="display:none">
                                            <div class="form-group">
                                                <label><?= _l("contac_func_meeting_date"); ?></label>
                                                <input type="text" class="form-control" id="meetingDateInput" placeholder="2026-01-15">
                                                <p class="text-muted small mt-1 mb-0"><?= _l("contac_func_meeting_date_help"); ?></p>
                                            </div>
                                        </div>

                                        <div class="form-group mb-0 mt-3">
                                            <label class="bold"><?= _l("contac_assistent_generated_code"); ?></label>
                                            <pre id="updateLeadsPreview" class="form-control" style="min-height:120px; font-size:12px; white-space:pre-wrap;"></pre>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                                        <button type="button" class="btn btn-success" id="updateLeadsGenerateInsert">
                                            <i class="fa fa-check"></i> <?= _l("contac_update_leads_generate_insert"); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>





<?php init_tail(); ?>
<script>
    window.SCHEDULING_FLOW_DEFAULT = <?= !empty($scheduling_flow_default) ? json_encode($scheduling_flow_default) : '""' ?>;
</script>
<script>
    function aviso_lead(element, msg) {
        if (element.checked) {
            alert(msg);
        }
    }

    // --- Instructions mode: Guided vs Advanced ---
    (function() {
        var currentInstructionsMode = 'guided';

        function buildInstructionsFromSections() {
            var parts = [];
            $('.inst-section').each(function() {
                var $el = $(this);
                var header = $el.data('header') || '';
                var content = ($el.val() || '').trim();
                if (content) {
                    parts.push(header ? (header + '\n' + content) : content);
                }
            });
            return parts.join('\n\n');
        }

        function parseInstructionsToSections(text) {
            if (!text || !text.trim()) return;
            var sections = [
                { header: 'Regras de contexto e primeiro contato:', id: 'inst_context' },
                { header: 'Objetivo:', id: 'inst_objective' },
                { header: 'Público-alvo:', id: 'inst_target' },
                { header: 'Estilo de Comunicação:', id: 'inst_style' },
                { header: 'Script para o Agente:', id: 'inst_script' },
                { header: 'Apresentação Sobre a Empresa', id: 'inst_company' },
                { header: 'Exemplos de interação:', id: 'inst_examples' },
                { header: 'Restrições para o agente:', id: 'inst_restrictions' },
                { header: 'Tratamentos realizados:', id: 'inst_services' },
                { header: 'Tabela de tratamentos e valores apenas para consulta do agente:', id: 'inst_pricing' },
                { header: 'Tabela de tratamentos e valores', id: 'inst_pricing' },
                { header: 'Endereço e horário:', id: 'inst_location' },
                { header: 'Perguntas frequentes:', id: 'inst_faq' },
                { header: 'Fluxo de Agendamento:', id: 'inst_scheduling' },
                { header: 'Instruções adicionais:', id: 'inst_extra' }
            ];
            var t = text;
            for (var i = 0; i < sections.length; i++) {
                var s = sections[i];
                var idx = t.indexOf(s.header);
                if (idx === -1) {
                    if (i === 0) {
                        var firstHeader = sections[1] ? t.indexOf(sections[1].header) : -1;
                        var firstContent = firstHeader !== -1 ? t.substring(0, firstHeader).trim() : t.trim();
                        var $f = $('#' + s.id);
                        if ($f.length && firstContent) $f.val(firstContent);
                    }
                    continue;
                }
                var nextIdx = t.length;
                for (var j = 0; j < sections.length; j++) {
                    if (j === i) continue;
                    var ni = t.indexOf(sections[j].header);
                    if (ni > idx && ni < nextIdx) nextIdx = ni;
                }
                var content = t.substring(idx + s.header.length, nextIdx).replace(/^\s*[\r\n]+/, '').trim();
                var $field = $('#' + s.id);
                if ($field.length && content) $field.val(content);
            }
        }

        function syncGuidedToAdvanced() {
            $('#instructions').val(buildInstructionsFromSections());
        }

        function switchMode(mode) {
            currentInstructionsMode = mode;
            $('.instructions-mode-btn').removeClass('active');
            $('.instructions-mode-btn[data-mode="' + mode + '"]').addClass('active');
            if (mode === 'guided') {
                parseInstructionsToSections($('#instructions').val());
                $('#instructionsGuidedWrap').show();
                $('#instructionsAdvancedWrap').hide();
                initPlaceholderHighlight();
            } else {
                syncGuidedToAdvanced();
                $('#instructionsGuidedWrap').hide();
                $('#instructionsAdvancedWrap').show();
                initPlaceholderHighlight();
            }
        }

        function getInstructionsInsertTarget() {
            if (currentInstructionsMode === 'guided') {
                var active = document.activeElement;
                if (active && $(active).hasClass('inst-section')) return active;
            }
            return document.getElementById('instructions');
        }

        function goToStep(step) {
            var $panels = $('.inst-wizard-panel');
            var total = $panels.length;
            step = Math.max(1, Math.min(step, total));
            $panels.removeClass('active').hide();
            $panels.filter('[data-step="' + step + '"]').addClass('active').show();
            $('.inst-step-item').removeClass('active').filter('[data-step="' + step + '"]').addClass('active');
            $('.inst-step-item').each(function() {
                var dotStep = parseInt($(this).data('step'), 10);
                var $panel = $panels.filter('[data-step="' + dotStep + '"]');
                var hasContent = $panel.length && $panel.find('textarea').val().trim().length > 0;
                $(this).toggleClass('filled', hasContent);
            });
            $('#instStepCounter').text(step + ' / ' + total);
            $('#instWizardPrev').prop('disabled', step <= 1);
            $('#instWizardNext').html(step >= total ? '<?= _l("contac_instructions_wizard_finish"); ?> <i class="fa fa-check"></i>' : '<?= _l("next"); ?> <i class="fa fa-chevron-right"></i>');
        }

        function initWizard() {
            var $panels = $('.inst-wizard-panel');
            var total = $panels.length;
            var $dots = $('#instStepDots');
            $dots.empty();
            for (var i = 1; i <= total; i++) {
                var $panel = $panels.filter('[data-step="' + i + '"]');
                var title = $panel.length ? $panel.find('.inst-step-title').first().text().trim() : '';
                var label = title ? title : ('Step ' + i);
                $dots.append('<span class="inst-step-item' + (i === 1 ? ' active' : '') + '" data-step="' + i + '"><span class="inst-step-num">' + i + '</span><span class="inst-step-label">' + $('<div>').text(label).html() + '</span></span>');
            }
            $('.inst-wizard').off('click.instwiz');
            $('.inst-wizard').on('click.instwiz', '.inst-step-item', function(e) {
                e.preventDefault();
                e.stopPropagation();
                goToStep(parseInt($(this).data('step'), 10));
            });
            $('.inst-wizard').on('click.instwiz', '#instWizardPrev', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if ($('#instWizardPrev').prop('disabled')) return false;
                var step = parseInt($('.inst-wizard-panel.active').data('step'), 10);
                goToStep(step - 1);
                return false;
            });
            $('.inst-wizard').on('click.instwiz', '#instWizardNext', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var step = parseInt($('.inst-wizard-panel.active').data('step'), 10);
                if (step >= $('.inst-wizard-panel').length) {
                    syncGuidedToAdvanced();
                    switchMode('advanced');
                } else {
                    goToStep(step + 1);
                }
                return false;
            });
            $('.inst-wizard-panels').off('input', '.inst-section').on('input', '.inst-section', function() {
                var $panel = $(this).closest('.inst-wizard-panel');
                var step = parseInt($panel.data('step'), 10);
                $('.inst-step-item[data-step="' + step + '"]').toggleClass('filled', $(this).val().trim().length > 0);
            });
            goToStep(1);
        }

        function maybeLoadSchedulingDefault() {
            var $ta = $('#inst_scheduling');
            if (!$ta.length || !window.SCHEDULING_FLOW_DEFAULT) return;
            var instructionsEmpty = !$('#instructions').val().trim();
            if (instructionsEmpty && !$ta.val().trim()) {
                $ta.val(window.SCHEDULING_FLOW_DEFAULT);
                $('.inst-step-item[data-step="13"]').addClass('filled');
            }
        }

        function wrapPlaceholdersWithHighlight(text) {
            if (!text) return '';
            var esc = function(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); };
            return esc(text).replace(/\[(TELEFONE_RECEPCAO|ADAPTE[^\]]*|DATA[^\]]*|HORÁRIO[^\]]*|HORARIO[^\]]*|[A-Z][A-Z0-9_]*)\]/g, '<span class="placeholder-highlight">[$1]</span>');
        }
        window.wrapPlaceholdersWithHighlight = wrapPlaceholdersWithHighlight;

        function initPlaceholderHighlight() {
            $('.inst-section, #instructions').each(function() {
                var $ta = $(this);
                if ($ta.data('placeholder-highlight-init')) return;
                $ta.data('placeholder-highlight-init', true);
                if ($ta.parent().hasClass('inst-highlight-wrap')) return;
                $ta.wrap('<div class="inst-highlight-wrap"/>');
                var $wrap = $ta.parent();
                var $mirror = $('<div class="inst-highlight-mirror"/>');
                $ta.before($mirror);
                function updateMirror() {
                    var text = $ta.val() || '';
                    $mirror.html(wrapPlaceholdersWithHighlight(text));
                    $mirror.scrollTop($ta.scrollTop()).scrollLeft($ta.scrollLeft());
                }
                $ta.on('input change keyup', function() { updateMirror(); });
                $ta.on('scroll', function() { $mirror.scrollTop($ta.scrollTop()).scrollLeft($ta.scrollLeft()); });
                updateMirror();
            });
        }

        function refreshPlaceholderHighlights() {
            $('.inst-section, #instructions').each(function() {
                var $ta = $(this);
                var $mirror = $ta.siblings('.inst-highlight-mirror');
                if ($mirror.length) {
                    var text = $ta.val() || '';
                    $mirror.html(wrapPlaceholdersWithHighlight(text));
                    $mirror.scrollTop($ta.scrollTop()).scrollLeft($ta.scrollLeft());
                }
            });
        }

        $(document).ready(function() {
            parseInstructionsToSections($('#instructions').val());
            initWizard();
            maybeLoadSchedulingDefault();
            initPlaceholderHighlight();
            $('#btnInsertSchedulingDefault').on('click', function() {
                if (window.SCHEDULING_FLOW_DEFAULT && $('#inst_scheduling').length) {
                    $('#inst_scheduling').val(window.SCHEDULING_FLOW_DEFAULT);
                    $('.inst-step-item[data-step="13"]').addClass('filled');
                    refreshPlaceholderHighlights();
                    alert_float('success', '<?= _l("contac_inst_scheduling_inserted"); ?>');
                }
            });
            $('#btnClearScheduling').on('click', function() {
                if ($('#inst_scheduling').length) {
                    $('#inst_scheduling').val('');
                    $('.inst-step-item[data-step="13"]').removeClass('filled');
                    refreshPlaceholderHighlights();
                    alert_float('info', '<?= _l("contac_inst_scheduling_cleared"); ?>');
                }
            });

            $('.instructions-mode-btn').on('click', function() {
                switchMode($(this).data('mode'));
                if (currentInstructionsMode === 'guided') initWizard();
            });

            $('#btnOpenFullInstructions').on('click', function() {
                syncGuidedToAdvanced();
                switchMode('advanced');
            });

            $('form').filter(function() { return ($(this).attr('action') || '').indexOf('add_assistant') !== -1; })
                .on('submit', function() {
                    if (currentInstructionsMode === 'guided') {
                        var built = buildInstructionsFromSections();
                        $('#instructions').val(built);
                        if (!built || !built.trim()) {
                            alert_float('danger', '<?= _l("contac_instructions_required") ?>');
                            return false;
                        }
                    }
                });
        });

        window.getInstructionsInsertTarget = getInstructionsInsertTarget;
    })();

    // Onboarding link
    $('#btnGenOnboardingLink').on('click', function() {
        var btn = $(this);
        var assistantId = btn.data('assistant-id');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        var postData = { assistant_id: assistantId };
        var csrfInput = $('form input[name*="csrf"]');
        if (csrfInput.length) { postData[csrfInput.attr('name')] = csrfInput.val(); }
        $.post('<?= admin_url("contactcenter/generate_assistant_form_link") ?>', postData, function(res) {
            btn.prop('disabled', false).html('<i class="fa-solid fa-link"></i> <?= _l("contac_assistant_get_onboarding_link") ?>');
            if (res.success && res.url) {
                $('#onboardingLinkInput').val(res.url);
                $('#onboardingLinkDisplay').removeClass('tw-hidden').addClass('tw-flex');
            } else {
                alert_float('danger', res.message || 'Error');
            }
        }, 'json');
    });
    $('#btnCopyOnboardingLink').on('click', function() {
        var inp = document.getElementById('onboardingLinkInput');
        if (inp && inp.value) {
            inp.select();
            document.execCommand('copy');
            alert_float('success', '<?= _l("copied_to_clipboard") ?: "Copied!" ?>');
        }
    });

    $('#btnRefreshOnboarding').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true).find('i').addClass('fa-spin');
        window.location.reload();
    });

    // Generate from Onboarding
    $('#btnGenerateFromOnboarding').on('click', function() {
        if (!confirm('<?= _l('contac_assistant_generate_confirm'); ?>')) return;

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?= _l('processing'); ?>');

        $.ajax({
            url: '<?= admin_url('contactcenter/generate_assistant_from_onboarding/' . $assistants->id); ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert_float('danger', response.message);
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                console.log("Response:", xhr.responseText);
                var errorMessage = '<?= _l('contac_assistant_generate_error'); ?>';
                if (xhr.responseText) {
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json.message) {
                            errorMessage += ' ' + json.message;
                        }
                    } catch (e) {
                        errorMessage += ' ' + xhr.status + ' ' + error;
                    }
                }
                alert_float('danger', errorMessage);
                btn.prop('disabled', false).html(originalText);
            }
        });
    });

    $(document).on('change', '#onboardingSelect', function() {
        var idx = $(this).val();
        $('.onboarding-item').addClass('tw-hidden');
        $('.onboarding-item[data-idx="' + idx + '"]').removeClass('tw-hidden');
    });

    $('.add_merge_field').on('click', function(e) {
        e.preventDefault();

        var mergeField = $(this).text().replace(/[{()}]/g, '');
        var textarea = window.getInstructionsInsertTarget ? window.getInstructionsInsertTarget() : document.getElementById('instructions');
        if (!textarea) return;

        // Pega a posição do cursor
        var startPos = textarea.selectionStart;
        var endPos = textarea.selectionEnd;

        // Pega o valor atual do textarea
        var currentContent = textarea.value;

        // Insere o texto na posição do cursor
        var newContent = currentContent.substring(0, startPos) + mergeField + currentContent.substring(endPos);

        // Atualiza o valor do textarea
        textarea.value = newContent;

        // Move o cursor para depois do texto inserido
        textarea.selectionStart = textarea.selectionEnd = startPos + mergeField.length;

        // Foca de novo no textarea
        textarea.focus();
    });

    // Insert update_leads helper
    (function() {
        var DATE_PLACEHOLDER = '[DATA E HORA ATUAL NO FORMATO YYYY-MM-DD HH:MM:SS]';
        var fieldIdx = 0;
        function addCustomFieldRow(field, value, label) {
            field = field || ''; value = value || ''; label = label || '';
            var row;
            if (field && label) {
                row = '<div class="tw-flex tw-gap-2 tw-mb-2 tw-items-center" data-ul-row data-field="' + String(field).replace(/"/g, '&quot;') + '">' +
                    '<span class="ul-preset-label" style="min-width:160px;font-size:13px">' + String(label).replace(/</g, '&lt;') + '</span>' +
                    '<input type="text" class="form-control ul-field-value" placeholder="<?= htmlspecialchars(_l("contac_update_leads_field_value")); ?>" value="' + (value ? String(value).replace(/"/g, '&quot;') : '') + '">' +
                    '<button type="button" class="btn btn-default btn-sm ul-remove-field"><i class="fa fa-times"></i></button></div>';
            } else {
                row = '<div class="tw-flex tw-gap-2 tw-mb-2 tw-items-center" data-ul-row>' +
                    '<input type="text" class="form-control ul-field-name" placeholder="<?= htmlspecialchars(_l("contac_update_leads_field_name")); ?>" value="' + (field ? String(field).replace(/"/g, '&quot;') : '') + '" style="min-width:140px">' +
                    '<input type="text" class="form-control ul-field-value" placeholder="<?= htmlspecialchars(_l("contac_update_leads_field_value")); ?>" value="' + (value ? String(value).replace(/"/g, '&quot;') : '') + '">' +
                    '<button type="button" class="btn btn-default btn-sm ul-remove-field"><i class="fa fa-times"></i></button></div>';
            }
            $('#updateLeadsCustomFieldsList').append(row);
        }
        function buildBlock() {
            var type = $('#funcBlockTypeSelect').val();
            if (type === 'update_leads') {
                var statusId = $('#updateLeadsStatusSelect').val();
                if (!statusId) return null;
                var params = {};
                params.status = parseInt(statusId, 10);
                if ($('#updateLeadsIncludeLastStatus').prop('checked')) params.last_status_change = DATE_PLACEHOLDER;
                if ($('#updateLeadsIncludeLastcontact').prop('checked')) params.lastcontact = DATE_PLACEHOLDER;
                $('#updateLeadsCustomFieldsList [data-ul-row]').each(function() {
                    var n = $(this).data('field') || $(this).find('.ul-field-name').val().trim();
                    var v = $(this).find('.ul-field-value').val().trim();
                    if (n) params[n] = v;
                });
                var json = JSON.stringify({ name: 'update_leads', parameters: params }, null, 2);
                return '**Chamar a função `update_leads` para atualizar o status**  \n```json\n' + json + '\n```';
            }
            if (type === 'get_lead_info') {
                var json = JSON.stringify({ name: 'get_lead_info', parameters: {} }, null, 2);
                return '**Chamar a função `get_lead_info` para obter dados do lead**  \n```json\n' + json + '\n```';
            }
            if (type === 'get_lead_context') {
                var params = {};
                var lim = $('#contextLimit').val();
                if (lim === 'all') params.include_all = true;
                else if (lim) params.limit = parseInt(lim, 10);
                var json = JSON.stringify({ name: 'get_lead_context', parameters: params }, null, 2);
                return '**Chamar a função `get_lead_context` para obter histórico**  \n```json\n' + json + '\n```';
            }
            if (type === 'get_horario_agenda') {
                var dateVal = $('#getHorarioDateInput').val().trim() || '[DATA SOLICITADA PELO LEAD - formato YYYY-MM-DD]';
                var params = { date: dateVal, rejected_hours: [] };
                var specific = $('#getHorarioSpecificHour').val().trim();
                var after = $('#getHorarioAfterHour').val().trim();
                var period = $('#getHorarioPeriod').val();
                var rejected = $('#getHorarioRejected').val().trim();
                if (specific) params.specific_hour = specific;
                if (after) params.after_some_hour = after;
                if (period) params.period_of_day = period;
                if (rejected) params.rejected_hours = rejected.split(/[\s,]+/).map(function(s){ return s.trim(); }).filter(Boolean);
                var block = '**Quando o lead pedir horários disponíveis, CHAMAR a função `get_horario_agenda`:**  \n```json\n';
                block += JSON.stringify({ name: 'get_horario_agenda', parameters: params }, null, 2) + '\n```';
                return block;
            }
            if (type === 'set_horario_agenda') {
                var dt = '[HORÁRIO CONFIRMADO PELO LEAD - usar o formato exato retornado por get_horario_agenda]';
                var block = '**Quando o lead confirmar um horário (da lista retornada por get_horario_agenda), chamar:**  \n```json\n';
                block += JSON.stringify({ name: 'set_horario_agenda', parameters: { date: dt } }, null, 2) + '\n```';
                return block;
            }
            if (type === 'create_group_chat') {
                var dt = $('#meetingDateInput').val().trim() || '[DATA DA REUNIÃO NO FORMATO YYYY-MM-DD]';
                var json = JSON.stringify({ name: 'create_group_chat', parameters: { meeting_date: dt } }, null, 2);
                return '**Chamar a função `create_group_chat` para criar grupo**  \n```json\n' + json + '\n```';
            }
            return null;
        }
        function refreshPreview() {
            var block = buildBlock();
            var fallback = '<?= htmlspecialchars(_l("contac_assistent_configure_options")); ?>';
            if (block && window.wrapPlaceholdersWithHighlight) {
                $('#updateLeadsPreview').html(window.wrapPlaceholdersWithHighlight(block));
            } else {
                $('#updateLeadsPreview').text(block || fallback);
            }
        }
        function switchPanel() {
            var type = $('#funcBlockTypeSelect').val();
            $('#funcBlockUpdateLeads').toggle(type === 'update_leads');
            $('#funcBlockGetLeadContext').toggle(type === 'get_lead_context');
            $('#funcBlockGetHorario').toggle(type === 'get_horario_agenda');
            $('#funcBlockScheduleOnly').toggle(type === 'set_horario_agenda');
            $('#funcBlockCreateGroup').toggle(type === 'create_group_chat');
            refreshPreview();
        }
        $('#updateLeadsAddField').on('click', function() { addCustomFieldRow(); refreshPreview(); });
        $(document).on('click', '.ul-preset', function() {
            addCustomFieldRow($(this).data('field'), '', $(this).text());
            refreshPreview();
        });
        $(document).on('click', '.ul-remove-field', function() {
            $(this).closest('[data-ul-row]').remove();
            refreshPreview();
        });
        $('#funcBlockTypeSelect').on('change', switchPanel);
            $(document).on('input', '.ul-field-value, .ul-field-name', refreshPreview);
            $('#updateLeadsStatusSelect, #updateLeadsIncludeLastStatus, #updateLeadsIncludeLastcontact, #contextLimit, #meetingDateInput, #getHorarioDateInput, #getHorarioSpecificHour, #getHorarioAfterHour, #getHorarioPeriod, #getHorarioRejected').on('change input', refreshPreview);
        $('#updateLeadsModal').on('show.bs.modal', function(e) {
            var presetFunc = (e.relatedTarget && $(e.relatedTarget).data('func')) || '';
            $('#updateLeadsCustomFieldsList').empty();
            $('#updateLeadsStatusSelect').val('');
            $('#updateLeadsIncludeLastStatus').prop('checked', true);
            $('#updateLeadsIncludeLastcontact').prop('checked', false);
            $('#contextLimit').val('50');
            $('#meetingDateInput').val('');
            $('#getHorarioDateInput').val('');
            $('#getHorarioSpecificHour').val('');
            $('#getHorarioAfterHour').val('');
            $('#getHorarioPeriod').val('');
            $('#getHorarioRejected').val('');
            fieldIdx = 0;
            if (presetFunc && $('#funcBlockTypeSelect option[value="' + presetFunc + '"]').length) {
                $('#funcBlockTypeSelect').val(presetFunc);
            }
            switchPanel();
        });
        $('#updateLeadsGenerateInsert').on('click', function() {
            var type = $('#funcBlockTypeSelect').val();
            var block = buildBlock();
            if (!block) {
                if (type === 'update_leads') alert_float('warning', '<?= htmlspecialchars(_l("contac_update_leads_status")); ?>: <?= htmlspecialchars(_l("form_validation_required") ?: "required"); ?>');
                return;
            }
            var textarea = window.getInstructionsInsertTarget ? window.getInstructionsInsertTarget() : document.getElementById('instructions');
            if (!textarea) return;
            var ta = textarea;
            var start = ta.selectionStart, end = ta.selectionEnd;
            var txt = ta.value;
            ta.value = txt.substring(0, start) + '\n\n' + block + '\n\n' + txt.substring(end);
            ta.selectionStart = ta.selectionEnd = start + 2 + block.length + 2;
            ta.focus();
            $('#updateLeadsModal').modal('hide');
            alert_float('success', '<?= _l("contac_assistent_code_inserted"); ?>');
        });
    })();

    // Handle media field insertion
    $(document).on('click', '.add_media_field', function(e) {
        e.preventDefault();
        var variable = $(this).data('variable');
        var textarea = window.getInstructionsInsertTarget ? window.getInstructionsInsertTarget() : document.getElementById('instructions');
        if (!textarea) return;

        // Pega a posição do cursor
        var startPos = textarea.selectionStart;
        var endPos = textarea.selectionEnd;

        // Pega o valor atual do textarea
        var currentContent = textarea.value;

        // Insere o texto na posição do cursor
        var newContent = currentContent.substring(0, startPos) + variable + currentContent.substring(endPos);

        // Atualiza o valor do textarea
        textarea.value = newContent;

        // Move o cursor para depois do texto inserido
        textarea.selectionStart = textarea.selectionEnd = startPos + variable.length;

        // Foca de novo no textarea
        textarea.focus();
    });

    // Handle library media upload
    $('#upload_library_media_btn').on('click', function() {
        var fileInput = $('#library_media_file_input')[0];
        var fileName = $('#library_media_file_name').val();
        
        if (!fileInput.files || !fileInput.files[0]) {
            alert_float('warning', '<?= _l("contac_assistant_select_file"); ?>');
            return;
        }
        
        var formData = new FormData();
        formData.append('assist_id', <?= $assistants->id; ?>);
        formData.append('media_file', fileInput.files[0]);
        formData.append('file_name', fileName || '');
        formData.append('is_library', '1'); // Upload to library
        
        // Get CSRF token from form or generate fresh one
        var csrfTokenName = '';
        var csrfTokenValue = '';
        
        // Try to get from form first
        var csrfInput = $('form input[type="hidden"][name*="csrf"]');
        if (csrfInput.length > 0) {
            csrfTokenName = csrfInput.attr('name');
            csrfTokenValue = csrfInput.val();
        } else {
            // Fallback: use helper function values
            <?php $csrf = get_csrf_for_ajax(); ?>
            csrfTokenName = '<?= $csrf['token_name']; ?>';
            csrfTokenValue = '<?= $csrf['hash']; ?>';
        }
        
        if (csrfTokenName && csrfTokenValue) {
            formData.append(csrfTokenName, csrfTokenValue);
        }
        
        $.ajax({
            url: site_url + 'contactcenter/upload_assistant_media',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function(xhr) {
                // Also set CSRF token in header as backup
                if (csrfTokenName && csrfTokenValue) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfTokenValue);
                }
            },
            success: function(response) {
                if (response.result) {
                    alert_float('success', response.message || '<?= _l("contac_assistant_library_media_uploaded"); ?>');
                    // Reload page to show new library media
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert_float('danger', response.message || '<?= _l("contac_assistant_upload_error"); ?>');
                }
            },
            error: function(xhr, status, error) {
                var errorMessage = '<?= _l("contac_assistant_upload_error"); ?>';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 403 || xhr.status === 419) {
                    errorMessage = '<?= _l("contac_assistant_csrf_error"); ?>';
                    // Suggest refresh
                    if (confirm(errorMessage + '\n\nWould you like to refresh the page?')) {
                        location.reload();
                    }
                }
                alert_float('danger', errorMessage);
            }
        });
    });

    // Add library media to assistant
    function add_library_media(assistantId, mediaId) {
        // Get CSRF token from form
        var csrfTokenName = '';
        var csrfTokenValue = '';
        var csrfInput = $('form input[type="hidden"][name*="csrf"]');
        if (csrfInput.length > 0) {
            csrfTokenName = csrfInput.attr('name');
            csrfTokenValue = csrfInput.val();
        } else {
            // Fallback
            <?php $csrf = get_csrf_for_ajax(); ?>
            csrfTokenName = '<?= $csrf['token_name']; ?>';
            csrfTokenValue = '<?= $csrf['hash']; ?>';
        }
        
        var postData = {
            assistant_id: assistantId,
            media_id: mediaId
        };
        if (csrfTokenName && csrfTokenValue) {
            postData[csrfTokenName] = csrfTokenValue;
        }
        
        $.ajax({
            url: site_url + 'contactcenter/add_library_media_to_assistant',
            type: 'POST',
            data: postData,
            dataType: 'json',
            beforeSend: function(xhr) {
                if (csrfTokenValue) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfTokenValue);
                }
            },
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message || '<?= _l("contac_assistant_library_media_added"); ?>');
                    
                    // Add to assistant media list
                    if (response.media) {
                        var media = response.media;
                        var fileType = media.file_type ? media.file_type.toUpperCase() : '';
                        var fileHtml = '<div id="media_file_' + media.id + '" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; background-color: #f5f5f5; border-radius: 4px; border: 1px solid #ddd;">' +
                            '<span class="tw-cursor-pointer tw-font-semibold tw-text-primary add_media_field" data-variable="' + (media.variable_name || '') + '" title="<?= _l("contac_assistent_click_to_insert"); ?>" style="color: #007bff;">' +
                            (media.file_name || '') + '</span>' +
                            '<span style="font-size: 11px; color: #666;">(' + fileType + ')</span>' +
                            '<button type="button" onclick="delete_media_file(\'' + media.id + '\')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-title="<?= _l("delete"); ?>">' +
                            '<i class="fa-solid fa-trash"></i></button></div>';
                        
                        $('#media_files_list').append(fileHtml);
                        
                        // Update library media button to show added
                        $('#library_media_' + mediaId).css({
                            'background-color': '#d4edda',
                            'border-color': '#c3e6cb'
                        });
                        $('#library_media_' + mediaId + ' button').replaceWith('<span class="badge badge-success" style="font-size: 10px;"><?= _l("contac_assistant_already_added"); ?></span>');
                    }
                } else {
                    alert_float('danger', response.message || '<?= _l("contac_assistant_library_media_add_error"); ?>');
                }
            },
            error: function(xhr, status, error) {
                var errorMessage = '<?= _l("contac_assistant_library_media_add_error"); ?>';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 403 || xhr.status === 419) {
                    errorMessage = '<?= _l("contac_assistant_csrf_error"); ?>';
                    if (confirm(errorMessage + '\n\nWould you like to refresh the page?')) {
                        location.reload();
                    }
                }
                alert_float('danger', errorMessage);
            }
        });
    }

    // Handle media file upload
    $('#upload_media_btn').on('click', function() {
        var fileInput = $('#media_file_input')[0];
        var fileName = $('#media_file_name').val();
        var assistId = <?= $assistants->id ?>;

        if (!fileInput.files || !fileInput.files[0]) {
            alert('<?= _l("contac_assistent_select_file"); ?>');
            return;
        }

        // Se não forneceu nome, usa o nome do arquivo sem extensão
        if (!fileName || fileName.trim() === '') {
            var uploadedFileName = fileInput.files[0].name;
            var nameWithoutExtension = uploadedFileName.replace(/\.[^/.]+$/, "");
            fileName = nameWithoutExtension;
        }

        var formData = new FormData();
        formData.append('media_file', fileInput.files[0]);
        formData.append('file_name', fileName);
        formData.append('assist_id', assistId);
        
        // Get CSRF token from form
        var csrfTokenName = '';
        var csrfTokenValue = '';
        var csrfInput = $('form input[type="hidden"][name*="csrf"]');
        if (csrfInput.length > 0) {
            csrfTokenName = csrfInput.attr('name');
            csrfTokenValue = csrfInput.val();
        } else {
            // Fallback
            <?php $csrf = get_csrf_for_ajax(); ?>
            csrfTokenName = '<?= $csrf['token_name']; ?>';
            csrfTokenValue = '<?= $csrf['hash']; ?>';
        }
        
        if (csrfTokenName && csrfTokenValue) {
            formData.append(csrfTokenName, csrfTokenValue);
        }

        // Disable button during upload
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?= _l("uploading"); ?>...');

        $.ajax({
            url: site_url + 'contactcenter/upload_assistant_media',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.result == true) {
                    // Add new file to list
                    var fileType = response.media.file_type.toUpperCase();
                    var fileHtml = '<div id="media_file_' + response.media.id + '" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; background-color: #f5f5f5; border-radius: 4px; border: 1px solid #ddd;">' +
                        '<span class="tw-cursor-pointer tw-font-semibold add_media_field" data-variable="' + response.media.variable_name + '" title="<?= _l("contac_assistent_click_to_insert"); ?>" style="color: #007bff; cursor: pointer;">' +
                        response.media.file_name + '</span>' +
                        '<span style="font-size: 11px; color: #666;">(' + fileType + ')</span>' +
                        '<button type="button" onclick="delete_media_file(' + response.media.id + ')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-title="<?= _l("delete"); ?>">' +
                        '<i class="fa-solid fa-trash"></i></button>' +
                        '</div>';
                    
                    $('#media_files_list p').remove();
                    $('#media_files_list').append(fileHtml);
                    
                    // Reset form
                    $('#media_file_input').val('');
                    $('#media_file_name').val('');
                    
                    alert('<?= _l("contac_assistent_file_uploaded_success"); ?>');
                } else {
                    alert(response.message || '<?= _l("contac_assistent_file_upload_error"); ?>');
                }
            },
            error: function(xhr, status, error) {
                var errorMsg = '<?= _l("contac_assistent_file_upload_error"); ?>';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                    } catch (e) {
                        // If not JSON, use default message
                    }
                }
                alert(errorMsg);
                console.error('Upload error:', status, error, xhr);
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-upload"></i> <?= _l("upload"); ?>');
            }
        });
    });

    // Handle media file deletion
    function delete_media_file(mediaId) {
        if (!confirm('<?= _l("contac_aviso_deleted"); ?>')) {
            return;
        }

        $.ajax({
            url: site_url + 'contactcenter/delete_assistant_media',
            type: 'POST',
            data: {
                id: mediaId
            },
            dataType: 'json',
            success: function(response) {
                if (response.result == true) {
                    $('#media_file_' + mediaId).fadeOut(function() {
                        $(this).remove();
                        if ($('#media_files_list').children().length === 0) {
                            $('#media_files_list').html('<p class="text-muted"><?= _l("contac_assistent_no_media_files"); ?></p>');
                        }
                    });
                } else {
                    alert(response.message || '<?= _l("contac_assistent_file_delete_error"); ?>');
                }
            },
            error: function() {
                alert('<?= _l("contac_assistent_file_delete_error"); ?>');
            }
        });
    }

    // Manage Conversation Builder
    function updateManageConversationCode() {
        var isFunctionActive = $('#manage_conversation').is(':checked');
        if (!isFunctionActive) {
            $('#mc_generated_code').text('<?= _l("contac_assistent_enable_function_first"); ?>');
            $('#mc_insert_code_btn').prop('disabled', true);
            return;
        }
        var params = {};
        var hasParams = false;

        // Disable AI
        if ($('#mc_disable_ai').is(':checked')) {
            params.disable_ai = true;
            hasParams = true;
        }

        // Enable AI
        if ($('#mc_enable_ai').is(':checked')) {
            params.enable_ai = true;
            hasParams = true;
        }

        // Change Staff Owner
        var changeStaff = $('#mc_change_staff').val();
        if (changeStaff) {
            params.change_staff_owner = parseInt(changeStaff);
            hasParams = true;
        }

        // Change Status
        var changeStatus = $('#mc_change_status').val();
        if (changeStatus) {
            params.change_status = parseInt(changeStatus);
            hasParams = true;
        }

        // Send Notification
        if ($('#mc_send_notification').is(':checked')) {
            params.send_notification = true;
            hasParams = true;

            var notificationStaff = $('#mc_notification_staff').val();
            var notificationMessage = $('#mc_notification_message').val().trim();

            if (notificationMessage) {
                params.notification_message = notificationMessage;
            }
        }

        // Generate code
        if (hasParams) {
            var code = 'manage_conversation({\n';
            var paramCount = 0;
            var totalParams = Object.keys(params).length;

            $.each(params, function(key, value) {
                paramCount++;
                if (typeof value === 'string') {
                    code += '  "' + key + '": "' + value + '"';
                } else if (typeof value === 'boolean') {
                    code += '  "' + key + '": ' + value;
                } else {
                    code += '  "' + key + '": ' + value;
                }
                if (paramCount < totalParams) {
                    code += ',\n';
                } else {
                    code += '\n';
                }
            });

            code += '})';
            $('#mc_generated_code').text(code);
            $('#mc_insert_code_btn').prop('disabled', false);
        } else {
            $('#mc_generated_code').text('<?= _l("contac_assistent_configure_options"); ?>');
            $('#mc_insert_code_btn').prop('disabled', true);
        }
    }

    // Update code when options change
    $('#mc_disable_ai, #mc_enable_ai, #mc_change_staff, #mc_change_status, #mc_send_notification, #mc_notification_staff, #mc_notification_message').on('change input', function() {
        // If disable_ai is checked, uncheck enable_ai
        if ($(this).attr('id') === 'mc_disable_ai' && $(this).is(':checked')) {
            $('#mc_enable_ai').prop('checked', false);
        }
        // If enable_ai is checked, uncheck disable_ai
        if ($(this).attr('id') === 'mc_enable_ai' && $(this).is(':checked')) {
            $('#mc_disable_ai').prop('checked', false);
        }
        
        updateManageConversationCode();
    });

    // Show/hide notification fields
    $('#mc_send_notification').on('change', function() {
        if ($(this).is(':checked')) {
            $('#mc_notification_staff_container').slideDown();
            $('#mc_notification_message_container').slideDown();
        } else {
            $('#mc_notification_staff_container').slideUp();
            $('#mc_notification_message_container').slideUp();
        }
        updateManageConversationCode();
    });

    // Insert code into instructions
    $('#mc_insert_code_btn').on('click', function() {
        var code = $('#mc_generated_code').text();
        if (code && code !== '<?= _l("contac_assistent_configure_options"); ?>') {
            var textarea = document.getElementById('instructions');
            var startPos = textarea.selectionStart;
            var endPos = textarea.selectionEnd;
            var currentContent = textarea.value;
            var newContent = currentContent.substring(0, startPos) + '\n\n' + code + '\n\n' + currentContent.substring(endPos);
            textarea.value = newContent;
            textarea.selectionStart = textarea.selectionEnd = startPos + code.length + 4;
            textarea.focus();
            
            // Show success message
            alert('<?= _l("contac_assistent_code_inserted"); ?>');
        }
    });

    // Listen to function checkbox changes
    $('#manage_conversation').on('change', function() {
        var isChecked = $(this).is(':checked');
        
        // Update opacity and disabled state
        if (isChecked) {
            $('#mc_builder_container').css('opacity', '1');
            $('#mc_builder_container .fa-exclamation-triangle').parent().hide();
        } else {
            $('#mc_builder_container').css('opacity', '0.6');
            $('#mc_builder_container .fa-exclamation-triangle').parent().show();
        }
        
        $('#mc_disable_ai, #mc_enable_ai, #mc_change_staff, #mc_change_status, #mc_send_notification, #mc_notification_staff, #mc_notification_message').prop('disabled', !isChecked);
        $('#mc_insert_code_btn').prop('disabled', !isChecked);
        
        if (!isChecked) {
            // Clear all fields
            $('#mc_disable_ai, #mc_enable_ai, #mc_send_notification').prop('checked', false);
            $('#mc_change_staff, #mc_change_status, #mc_notification_staff').val('');
            $('#mc_notification_message').val('');
            $('#mc_notification_staff_container, #mc_notification_message_container').hide();
        }
        
        updateManageConversationCode();
    });

    // Initialize
    var isFunctionActive = $('#manage_conversation').is(':checked');
    if (isFunctionActive) {
        $('#mc_builder_container').css('opacity', '1');
        $('#mc_builder_container .fa-exclamation-triangle').parent().hide();
    } else {
        $('#mc_builder_container').css('opacity', '0.6');
    }
    $('#mc_insert_code_btn').prop('disabled', !isFunctionActive);
    updateManageConversationCode();

    // Map GPT model names to AXIOM version names
    function getAxiomModelName(gptModel) {
        var modelMap = {
            'gpt-4o-mini': 'Standard',
            'gpt-4o': 'Advanced',
            'gpt-4.1': 'Pro',
            'gpt-4.1-mini': 'Lite',
            'gpt-4.1-nano': 'Nano',
            'gpt-5.1': 'Next',
            'gpt-5-pro': 'Ultra'
        };
        return modelMap[gptModel] || gptModel || '-';
    }

    // Version History Functions
    function viewVersion(versionId) {
        $.ajax({
            url: '<?= admin_url('contactcenter/get_assistant_versions'); ?>',
            type: 'POST',
            data: {
                assistant_id: <?= $assistants->id; ?>
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.versions) {
                    var version = response.versions.find(function(v) {
                        return v.id == versionId;
                    });
                    
                    if (version) {
                        $('#versionNumber').text('v' + version.version_number);
                        $('#versionName').text(version.ai_name || '-');
                        $('#versionModel').text(getAxiomModelName(version.model));
                        $('#versionInstructions').text(version.instructions || '-');
                        
                        var functions = version.functions ? JSON.parse(version.functions) : [];
                        $('#versionFunctions').text(functions.length > 0 ? JSON.stringify(functions, null, 2) : '-');
                        
                        $('#versionChanges').text(version.change_summary || '<?= _l('contac_assistant_no_changes'); ?>');
                        
                        $('#restoreFromModalBtn').data('version-id', version.id);
                        $('#versionDetailsModal').modal('show');
                    }
                }
            }
        });
    }

    function restoreVersion(assistantId, versionId, versionNumber) {
        if (!confirm('<?= _l('contac_assistant_restore_confirm'); ?> v' + versionNumber + '?')) {
            return;
        }

        $.ajax({
            url: '<?= admin_url('contactcenter/restore_assistant_version'); ?>',
            type: 'POST',
            data: {
                assistant_id: assistantId,
                version_id: versionId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message || '<?= _l('contac_assistant_version_restored'); ?>');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alert_float('danger', response.message || '<?= _l('contac_assistant_version_restore_error'); ?>');
                }
            },
            error: function() {
                alert_float('danger', '<?= _l('contac_assistant_version_restore_error'); ?>');
            }
        });
    }

    // Restore from modal
    $('#restoreFromModalBtn').on('click', function() {
        var versionId = $(this).data('version-id');
        var versionNumber = $('#versionNumber').text();
        $('#versionDetailsModal').modal('hide');
        restoreVersion(<?= $assistants->id; ?>, versionId, versionNumber.replace('v', ''));
    });

    // Preset Tiles Logic
    var instPresets = {
        'inst_context': [
            { label: 'Primeiro Contato', content: 'Quando o usuário abordar pela primeira vez, inicie com uma saudação amigável e se apresente como [NOME_AGENTE]. Pergunte o nome do cliente se ele não tiver dito.' },
            { label: 'Retorno', content: 'Quando o usuário interagir pela segunda vez, analise o histórico para dar continuidade ao assunto sem repetir perguntas já feitas.' },
            { label: 'Fora do Horário', content: 'Se o contato for fora do horário comercial, informe que o atendimento humano retornará no próximo dia útil, mas que você pode adiantar algumas informações.' }
        ],
        'inst_objective': [
            { label: 'Agendar Avaliação', content: 'O objetivo principal é qualificar o lead e agendar uma avaliação presencial na clínica.' },
            { label: 'Vender Produto', content: 'O objetivo é entender a necessidade do cliente e oferecer o produto mais adequado, levando ao fechamento da venda.' },
            { label: 'Tirar Dúvidas', content: 'O objetivo é responder todas as dúvidas do cliente sobre os produtos e serviços, atuando como um especialista.' },
            { label: 'Triagem', content: 'O objetivo é fazer uma triagem inicial para direcionar o cliente para o departamento correto (Vendas, Suporte ou Financeiro).' }
        ],
        'inst_style': [
            { label: 'Casual & Amigável', content: 'Tom de voz casual, amigável e prestativo. Use emojis moderadamente. Linguagem simples e direta.' },
            { label: 'Profissional & Sério', content: 'Tom de voz profissional, sério e objetivo. Evite gírias e emojis. Foco na eficiência e clareza.' },
            { label: 'Entusiasta', content: 'Tom de voz energético e entusiasta! Mostre empolgação em ajudar. Use pontos de exclamação e emojis positivos.' },
            { label: 'Empático', content: 'Tom de voz calmo, paciente e empático. Mostre que você entende o problema do cliente e quer ajudar.' }
        ],
        'inst_target': [
            { label: 'B2B (Empresas)', content: 'Público-alvo: Proprietários de empresas, gerentes e tomadores de decisão que buscam eficiência e resultados.' },
            { label: 'B2C (Consumidores)', content: 'Público-alvo: Consumidores finais que buscam resolver um problema pessoal ou realizar um desejo.' },
            { label: 'Jovens/Tech', content: 'Público-alvo: Pessoas jovens, conectadas e que valorizam agilidade e inovação.' },
            { label: 'Idosos', content: 'Público-alvo: Pessoas da terceira idade que precisam de mais atenção, paciência e explicações detalhadas.' }
        ],
        'inst_script': [
            { label: 'Fluxo Padrão', content: '1. Saudação e identificação\n2. Entender a necessidade do cliente\n3. Apresentar a solução/produto\n4. Tirar dúvidas\n5. Chamada para ação (Agendamento/Compra)' },
            { label: 'Qualificação Rápida', content: '1. Saudação breve\n2. Perguntas de qualificação (Orçamento, Prazo, Necessidade)\n3. Se qualificado: Avançar para agendamento\n4. Se não qualificado: Agradecer e encerrar' },
            { label: 'Suporte', content: '1. Identificar o problema\n2. Buscar solução na base de conhecimento\n3. Se resolvido: Confirmar satisfação\n4. Se não resolvido: Abrir ticket ou transferir para humano' }
        ],
        'inst_examples': [
            { label: 'Agendamento', content: 'Usuário: Quero marcar um horário.\nAgente: Claro! Tenho horários livres na terça à tarde. O que prefere?' },
            { label: 'Preço', content: 'Usuário: Quanto custa?\nAgente: O valor depende do pacote escolhido. Posso te explicar as opções?' },
            { label: 'Dúvida Técnica', content: 'Usuário: Funciona em iPhone?\nAgente: Sim! Nosso app é compatível com iOS e Android.' }
        ],
        'inst_restrictions': [
            { label: 'Não inventar', content: 'Nunca invente informações que não estejam nesta instrução. Se não souber, diga que vai verificar com um especialista.' },
            { label: 'Não falar preço', content: 'Não informe preços exatos. Diga que os valores variam e dependem de uma avaliação personalizada.' },
            { label: 'Não ser IA', content: 'Nunca diga explicitamente que você é uma Inteligência Artificial. Aja como um assistente virtual da equipe.' },
            { label: 'Limite caracteres', content: 'Mantenha as respostas curtas, idealmente abaixo de 200 caracteres, para facilitar a leitura no WhatsApp.' }
        ],
        'inst_faq': [
            { label: 'Preço', content: '1. Qual o preço?\nR: Os valores variam de acordo com a necessidade. Agende uma avaliação para um orçamento preciso.' },
            { label: 'Endereço', content: '2. Onde fica?\nR: Estamos localizados na [ENDEREÇO].' },
            { label: 'Horário', content: '3. Qual o horário?\nR: Funcionamos de segunda a sexta das 09h às 18h.' }
        ]
    };

    function renderPresets() {
        $('.inst-wizard-panel').each(function() {
            var $panel = $(this);
            var $textarea = $panel.find('textarea.inst-section');
            var textareaId = $textarea.attr('id');
            var $container = $panel.find('.inst-presets-container');

            if (instPresets[textareaId] && $container.length) {
                $container.empty();
                instPresets[textareaId].forEach(function(preset) {
                    var $tile = $('<div class="inst-preset-tile"><i class="fa fa-plus"></i> ' + preset.label + '</div>');
                    $tile.on('click', function() {
                        var currentVal = $textarea.val();
                        var newVal = currentVal ? currentVal + '\n' + preset.content : preset.content;
                        $textarea.val(newVal).trigger('input');
                    });
                    $container.append($tile);
                });
            }
        });
    }

    // CEP Lookup Logic
    function initCepLookup() {
        var $cepInput = $('#inst_location_cep');
        var $addressInput = $('#inst_location');
        var $loadingIcon = $('#cepLoading');

        if (!$cepInput.length) return;

        $cepInput.on('blur', function() {
            var cep = $(this).val().replace(/\D/g, '');
            if (cep.length !== 8) return;

            $loadingIcon.show();
            
            $.ajax({
                url: 'https://brasilapi.com.br/api/cep/v1/' + cep,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data && (data.street || data.city)) {
                        var parts = [];
                        if (data.street) parts.push(data.street);
                        if (data.neighborhood) parts.push(data.neighborhood);
                        if (data.city && data.state) parts.push(data.city + ' - ' + data.state);
                        else if (data.city) parts.push(data.city);
                        
                        var fullAddress = parts.join(', ');
                        var currentVal = $addressInput.val();
                        
                        // Append to existing text or replace placeholder
                        if (currentVal && currentVal.trim() !== '') {
                            $addressInput.val(currentVal + '\n📍 ' + fullAddress);
                        } else {
                            $addressInput.val('📍 ' + fullAddress);
                        }
                        $addressInput.trigger('input');
                    }
                },
                error: function() {
                    // Silent fail or optional alert
                },
                complete: function() {
                    $loadingIcon.hide();
                }
            });
        });
        
        // Mask for CEP
        $cepInput.on('input', function() {
            var v = this.value.replace(/\D/g, '');
            if (v.length > 5) v = v.slice(0, 5) + '-' + v.slice(5, 8);
            this.value = v;
        });
    }

    // Call renderPresets on init
    $(document).ready(function() {
        renderPresets();
        initCepLookup();
        
        // Generate Script Logic
        $('#btnGenerateScript').on('click', function() {
            $('#contactSelectionModal').modal('show');
            $('#contactSelectionModal').data('target-field', 'inst_script');
            $('#contactSelectionModal .modal-title').text('<?= _l("contac_select_contacts_for_script"); ?>');
            $('#btnProcessSelectedContacts').text('<?= _l("contac_generate_script"); ?>');
            loadContactsForSelection();
        });

        // Generate Examples Logic
        $('#btnGenerateExamples').on('click', function() {
            $('#contactSelectionModal').modal('show');
            $('#contactSelectionModal').data('target-field', 'inst_examples');
            $('#contactSelectionModal .modal-title').text('<?= _l("contac_select_contacts_for_examples"); ?>');
            $('#btnProcessSelectedContacts').text('<?= _l("contac_generate_examples"); ?>');
            loadContactsForSelection();
        });

        $('#contactSearchInput').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('.contact-selection-item').each(function() {
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(searchTerm) > -1);
            });
        });

        $('#btnProcessSelectedContacts').on('click', function() {
            var selectedContacts = [];
            $('.contact-selection-checkbox:checked').each(function() {
                selectedContacts.push($(this).val());
            });

            if (selectedContacts.length === 0) {
                alert_float('warning', '<?= _l("contac_select_at_least_one_contact"); ?>');
                return;
            }

            var targetField = $('#contactSelectionModal').data('target-field');
            var actionUrl = targetField === 'inst_script' 
                ? '<?= admin_url('contactcenter/generate_script_from_contacts'); ?>'
                : '<?= admin_url('contactcenter/generate_examples_from_contacts'); ?>';

            var btn = $(this);
            var originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?= _l('processing'); ?>');

            var postData = {
                contact_ids: selectedContacts,
                assistant_id: <?= $assistants->id; ?>
            };
            var csrfField = $('form input[name*="csrf"]');
            if (csrfField.length) { postData[csrfField.attr('name')] = csrfField.val(); }

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var currentVal = $('#' + targetField).val();
                        var content = response.content || response.examples;
                        var newVal = currentVal ? currentVal + '\n\n' + content : content;
                        $('#' + targetField).val(newVal).trigger('input');
                        $('#contactSelectionModal').modal('hide');
                        alert_float('success', '<?= _l("contac_generated_success"); ?>');
                    } else {
                        alert_float('danger', response.message || '<?= _l("contac_generation_error"); ?>');
                    }
                },
                error: function(xhr) {
                    console.error('Generate error:', xhr.status, xhr.responseText);
                    alert_float('danger', '<?= _l("contac_generation_error"); ?>');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    });

    function loadContactsForSelection() {
        $('#contactListLoading').show();
        $('#contactSelectionList').empty();
        
        $.ajax({
            url: '<?= admin_url('contactcenter/get_recent_contacts_for_selection'); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.contacts && response.contacts.length > 0) {
                    response.contacts.forEach(function(contact) {
                        var item = `
                            <li class="list-group-item contact-selection-item">
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" class="contact-selection-checkbox" id="contact_${contact.id}" value="${contact.id}">
                                    <label for="contact_${contact.id}">
                                        ${contact.name} <small class="text-muted">(${contact.phonenumber})</small>
                                    </label>
                                </div>
                            </li>
                        `;
                        $('#contactSelectionList').append(item);
                    });
                } else {
                    $('#contactSelectionList').html('<li class="list-group-item text-center"><?= _l("no_contacts_found"); ?></li>');
                }
            },
            complete: function() {
                $('#contactListLoading').hide();
            }
        });
    }

    // ── Script Auto-Update ──
    $(function() {
        var csrfField = $('form input[name*="csrf"]');
        var csrfData = {};
        if (csrfField.length) { csrfData[csrfField.attr('name')] = csrfField.val(); }

        $('#au_enabled').on('change', function() {
            $('#au_settings_fields').toggle(this.checked);
        });

        $('#btnSaveAutoUpdate').on('click', function() {
            var btn = $(this);
            var origHtml = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

            var data = $.extend({}, csrfData, {
                assistant_id: <?= $assistants->id; ?>,
                enabled: $('#au_enabled').is(':checked') ? 1 : 0,
                frequency_days: $('#au_frequency_days').val(),
                lead_count: $('#au_lead_count').val(),
                lead_status_id: $('#au_lead_status_id').val(),
                notify_staff_id: $('#au_notify_staff_id').val()
            });

            $.ajax({
                url: '<?= admin_url("contactcenter/save_script_autoupdate_settings"); ?>',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(r) {
                    if (r.success) {
                        alert_float('success', r.message);
                    } else {
                        alert_float('danger', r.message);
                    }
                },
                error: function(xhr) {
                    console.error('Save auto-update error:', xhr.status, xhr.responseText);
                    alert_float('danger', '<?= _l("contac_script_autoupdate_run_error"); ?>');
                },
                complete: function() { btn.prop('disabled', false).html(origHtml); }
            });
        });

        $('#btnRunAutoUpdateNow').on('click', function() {
            var btn = $(this);
            var origHtml = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?= _l("contac_script_autoupdate_running"); ?>');

            $.ajax({
                url: '<?= admin_url("contactcenter/run_script_autoupdate_manual/" . $assistants->id); ?>',
                type: 'POST',
                data: csrfData,
                dataType: 'json',
                timeout: 300000,
                success: function(r) {
                    if (r.success) {
                        alert_float('success', r.message);
                        setTimeout(function() { location.reload(); }, 1500);
                    } else {
                        alert_float('danger', r.message);
                    }
                },
                error: function(xhr) {
                    console.error('Run auto-update error:', xhr.status, xhr.responseText);
                    alert_float('danger', '<?= _l("contac_script_autoupdate_run_error"); ?>');
                },
                complete: function() { btn.prop('disabled', false).html(origHtml); }
            });
        });

        $(document).on('click', '.btn-toggle-diff', function() {
            var id = $(this).data('id');
            var diffEl = $('#diff-' + id);
            diffEl.slideToggle(200);
            var isVisible = diffEl.is(':visible');
            $(this).find('i').toggleClass('fa-exchange fa-eye-slash');
            $(this).find('span, .btn-text').remove();
        });

        $(document).on('click', '.btn-approve-update', function() {
            if (!confirm('<?= _l("contac_script_update_confirm_approve"); ?>')) return;
            reviewUpdate($(this).data('id'), 'approved', $(this));
        });

        $(document).on('click', '.btn-reject-update', function() {
            if (!confirm('<?= _l("contac_script_update_confirm_reject"); ?>')) return;
            reviewUpdate($(this).data('id'), 'rejected', $(this));
        });

        function reviewUpdate(updateId, action, btn) {
            var origHtml = btn.html();
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
            var card = btn.closest('.script-update-card');

            $.ajax({
                url: '<?= admin_url("contactcenter/review_script_update"); ?>',
                type: 'POST',
                data: $.extend({}, csrfData, { update_id: updateId, action: action }),
                dataType: 'json',
                success: function(r) {
                    if (r.success) {
                        alert_float('success', r.message);
                        card.slideUp(300, function() { $(this).remove(); });
                        if (action === 'approved') {
                            setTimeout(function() { location.reload(); }, 1200);
                        }
                    } else {
                        alert_float('danger', r.message);
                        btn.prop('disabled', false).html(origHtml);
                    }
                },
                error: function(xhr) {
                    console.error('Review error:', xhr.status, xhr.responseText);
                    alert_float('danger', '<?= _l("contac_script_update_review_error"); ?>');
                    btn.prop('disabled', false).html(origHtml);
                }
            });
        }
    });

</script>