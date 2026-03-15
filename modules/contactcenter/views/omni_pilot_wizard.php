<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
// CSRF token for AJAX requests
var omniPilotCsrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
var omniPilotCsrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

// Get current user name for campaign naming
var omniPilotCurrentUserName = '<?php echo addslashes(get_staff_full_name(get_staff_user_id())); ?>';
// Clean and limit user name (same as backend)
if (omniPilotCurrentUserName.length > 20) {
    omniPilotCurrentUserName = omniPilotCurrentUserName.substring(0, 20);
}
omniPilotCurrentUserName = omniPilotCurrentUserName.trim();

// Omni Pilot translations
window.omniPilotTranslations = <?php echo json_encode([
    'omni_pilot_phase' => _l('omni_pilot_phase'),
    'omni_pilot_goal' => _l('omni_pilot_goal'),
    'omni_pilot_starting' => _l('omni_pilot_starting'),
    'omni_pilot_phase_importing_leads' => _l('omni_pilot_phase_importing_leads'),
    'omni_pilot_phase_searching_leads' => _l('omni_pilot_phase_searching_leads'),
    'omni_pilot_completed' => _l('omni_pilot_completed'),
    'omni_pilot_step_1_creating_leads' => _l('omni_pilot_step_1_creating_leads'),
    'omni_pilot_step_2_creating_campaign' => _l('omni_pilot_step_2_creating_campaign'),
    'omni_pilot_step_2_1_sending_campaign' => _l('omni_pilot_step_2_1_sending_campaign'),
    'omni_pilot_step_3_interacting' => _l('omni_pilot_step_3_interacting'),
    'omni_pilot_step_4_followup' => _l('omni_pilot_step_4_followup'),
    'omni_pilot_searching_leads' => _l('omni_pilot_searching_leads'),
    'omni_pilot_importing_leads' => _l('omni_pilot_importing_leads'),
    'omni_pilot_waiting_first_followup' => _l('omni_pilot_waiting_first_followup'),
    'omni_pilot_sent' => _l('omni_pilot_sent'),
    'omni_pilot_leads_found' => _l('omni_pilot_leads_found'),
    'omni_pilot_enriching_leads' => _l('omni_pilot_enriching_leads'),
    'omni_pilot_phase_setting_up_campaign' => _l('omni_pilot_phase_setting_up_campaign'),
    'omni_pilot_phase_campaign_created' => _l('omni_pilot_phase_campaign_created'),
    'omni_pilot_phase_setting_up_messages' => _l('omni_pilot_phase_setting_up_messages'),
    'omni_pilot_phase_messages_configured' => _l('omni_pilot_phase_messages_configured'),
    'omni_pilot_phase_setting_up_followup' => _l('omni_pilot_phase_setting_up_followup'),
    'omni_pilot_phase_followup_configured' => _l('omni_pilot_phase_followup_configured'),
    'omni_pilot_phase_activating' => _l('omni_pilot_phase_activating'),
    'omni_pilot_phase_active_running' => _l('omni_pilot_phase_active_running'),
    'omni_pilot_phase_cancelled' => _l('omni_pilot_phase_cancelled'),
    'omni_pilot_stop_confirm' => _l('omni_pilot_stop_confirm'),
    'omni_pilot_stopping' => _l('omni_pilot_stopping'),
    'omni_pilot_stopped_successfully' => _l('omni_pilot_stopped_successfully'),
    'omni_pilot_stop_failed' => _l('omni_pilot_stop_failed'),
    'omni_pilot_error_stopping' => _l('omni_pilot_error_stopping'),
    'omni_pilot_no_active_session' => _l('omni_pilot_no_active_session'),
    'omni_pilot_page_expired' => _l('omni_pilot_page_expired')
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

// Helper function to get translations
function omniPilot_l(key) {
    if (window.omniPilotTranslations && window.omniPilotTranslations[key]) {
        return window.omniPilotTranslations[key];
    }
    // Fallback to app.lang if available
    if (typeof app !== 'undefined' && app.lang && app.lang[key]) {
        return app.lang[key];
    }
    // Fallback to global _l if available
    if (typeof _l === 'function') {
        return _l(key);
    }
    return key;
}
</script>

<!-- Omni Pilot Wizard Modal -->
<div class="modal fade" id="omniPilotWizardModal" tabindex="-1" role="dialog" aria-labelledby="omniPilotWizardModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 900px;">
        <div class="modal-content" style="border-radius: 8px; overflow: hidden;">
            <!-- Modal Header -->
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h4 class="modal-title" id="omniPilotWizardModalLabel" style="display: flex; align-items: center; gap: 10px; margin: 0;">
                    <i class="fa fa-rocket" style="font-size: 24px;"></i>
                    <span><?php echo _l('omni_pilot_title'); ?></span>
                </h4>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <div class="btn-group">
                        <button type="button" class="btn btn-xs btn-default" id="omni_load_template_btn" title="<?php echo _l('omni_pilot_load_template'); ?>">
                            <i class="fa fa-folder-open"></i> <?php echo _l('omni_pilot_load_template'); ?>
                        </button>
                        <button type="button" class="btn btn-xs btn-default" id="omni_save_template_btn" title="<?php echo _l('omni_pilot_save_template'); ?>">
                            <i class="fa fa-save"></i> <?php echo _l('omni_pilot_save_template'); ?>
                        </button>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <!-- Progress Indicator -->
            <div class="omni-pilot-progress-bar" style="padding: 15px 20px;">
                <div class="progress-steps" style="display: flex; justify-content: space-between; align-items: center;">
                    <?php for ($i = 0; $i <= 6; $i++): ?>
                        <div class="step-indicator" data-step="<?php echo $i; ?>" style="flex: 1; text-align: center; position: relative;">
                            <div class="step-circle" style="display: inline-flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 5px; position: relative; z-index: 2;">
                                <?php echo $i; ?>
                            </div>
                            <div class="step-label" style="font-size: 11px;">
                                <?php 
                                $labels = [
                                    0 => _l('omni_pilot_step_0_goal'),
                                    1 => _l('omni_pilot_step_1_supply'),
                                    2 => _l('omni_pilot_step_2_campaign'),
                                    3 => _l('omni_pilot_step_3_message'),
                                    4 => _l('omni_pilot_step_4_assistant'),
                                    5 => _l('omni_pilot_step_5_followup'),
                                    6 => _l('omni_pilot_step_6_launch')
                                ];
                                echo $labels[$i] ?? '';
                                ?>
                            </div>
                            <?php if ($i < 6): ?>
                                <div class="step-connector" style="position: absolute; top: 17px; left: 50%; width: 100%; height: 2px; z-index: 1;"></div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="padding: 30px; max-height: 70vh; overflow-y: auto;">
                <form id="omniPilotWizardForm">
                    <!-- Step 0: Goal Configuration -->
                    <div class="wizard-step" data-step="0" style="display: none;">
                        <h5 style="margin-bottom: 20px;">
                            <i class="fa fa-bullseye" style="color: var(--primary, #00e09b);"></i> <?php echo _l('omni_pilot_step_0_goal'); ?>
                        </h5>
                        <div class="form-group">
                            <label><?php echo _l('omni_pilot_goal_target'); ?> <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="goal_target" id="goal_target" value="10" min="1" required>
                            <small class="text-muted"><?php echo _l('omni_pilot_goal_target_desc'); ?></small>
                        </div>
                        <div class="form-group">
                            <label><?php echo _l('omni_pilot_goal_status'); ?> <span class="text-danger">*</span></label>
                            <select name="goal_status_id" id="goal_status_id" class="selectpicker" data-width="100%" data-live-search="true" required>
                                <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?php echo $status['id']; ?>"><?php echo $status['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted"><?php echo _l('omni_pilot_goal_status_desc'); ?></small>
                        </div>
                        <div class="form-group">
                            <label><?php echo _l('omni_pilot_deadline'); ?> <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="deadline_date" id="deadline_date" required>
                            <small class="text-muted"><?php echo _l('omni_pilot_deadline_desc'); ?></small>
                        </div>
                        <div class="form-group">
                            <label><?php echo _l('omni_pilot_product_company'); ?> <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="product_company" id="product_company" rows="3" required placeholder="<?php echo _l('omni_pilot_product_company_placeholder'); ?>"></textarea>
                            <small class="text-muted"><?php echo _l('omni_pilot_product_company_desc'); ?></small>
                        </div>
                        <div class="form-group">
                            <label><?php echo _l('omni_pilot_approach'); ?> <span class="text-danger">*</span></label>
                            <div class="omni-approach-options" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 10px;">
                                <div class="omni-approach-card" data-approach="direct_sales" style="cursor: pointer; padding: 20px; border: 2px solid rgba(255,255,255,0.2); border-radius: 12px; background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); transition: all 0.3s ease; text-align: center;">
                                    <div style="font-size: 48px; margin-bottom: 10px; color: var(--primary, #00e09b);">
                                        <i class="fa fa-handshake"></i>
                                    </div>
                                    <h6 style="margin: 10px 0 5px; font-weight: 600; color: var(--primary-text, #FFFFFF);"><?php echo _l('omni_pilot_approach_direct_sales'); ?></h6>
                                    <p style="font-size: 12px; color: rgba(255,255,255,0.7); margin: 0;"><?php echo _l('omni_pilot_approach_direct_sales_desc'); ?></p>
                                </div>
                                <div class="omni-approach-card" data-approach="educational" style="cursor: pointer; padding: 20px; border: 2px solid rgba(255,255,255,0.2); border-radius: 12px; background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); transition: all 0.3s ease; text-align: center;">
                                    <div style="font-size: 48px; margin-bottom: 10px; color: var(--primary, #00e09b);">
                                        <i class="fa fa-graduation-cap"></i>
                                    </div>
                                    <h6 style="margin: 10px 0 5px; font-weight: 600; color: var(--primary-text, #FFFFFF);"><?php echo _l('omni_pilot_approach_educational'); ?></h6>
                                    <p style="font-size: 12px; color: rgba(255,255,255,0.7); margin: 0;"><?php echo _l('omni_pilot_approach_educational_desc'); ?></p>
                                </div>
                                <div class="omni-approach-card" data-approach="relationship" style="cursor: pointer; padding: 20px; border: 2px solid rgba(255,255,255,0.2); border-radius: 12px; background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); transition: all 0.3s ease; text-align: center;">
                                    <div style="font-size: 48px; margin-bottom: 10px; color: var(--primary, #00e09b);">
                                        <i class="fa fa-heart"></i>
                                    </div>
                                    <h6 style="margin: 10px 0 5px; font-weight: 600; color: var(--primary-text, #FFFFFF);"><?php echo _l('omni_pilot_approach_relationship'); ?></h6>
                                    <p style="font-size: 12px; color: rgba(255,255,255,0.7); margin: 0;"><?php echo _l('omni_pilot_approach_relationship_desc'); ?></p>
                                </div>
                                <div class="omni-approach-card" data-approach="promotional" style="cursor: pointer; padding: 20px; border: 2px solid rgba(255,255,255,0.2); border-radius: 12px; background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); transition: all 0.3s ease; text-align: center;">
                                    <div style="font-size: 48px; margin-bottom: 10px; color: var(--primary, #00e09b);">
                                        <i class="fa fa-tag"></i>
                                    </div>
                                    <h6 style="margin: 10px 0 5px; font-weight: 600; color: var(--primary-text, #FFFFFF);"><?php echo _l('omni_pilot_approach_promotional'); ?></h6>
                                    <p style="font-size: 12px; color: rgba(255,255,255,0.7); margin: 0;"><?php echo _l('omni_pilot_approach_promotional_desc'); ?></p>
                                </div>
                                <div class="omni-approach-card" data-approach="consultation" style="cursor: pointer; padding: 20px; border: 2px solid rgba(255,255,255,0.2); border-radius: 12px; background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); transition: all 0.3s ease; text-align: center;">
                                    <div style="font-size: 48px; margin-bottom: 10px; color: var(--primary, #00e09b);">
                                        <i class="fa fa-clipboard"></i>
                                    </div>
                                    <h6 style="margin: 10px 0 5px; font-weight: 600; color: var(--primary-text, #FFFFFF);"><?php echo _l('omni_pilot_approach_consultation'); ?></h6>
                                    <p style="font-size: 12px; color: rgba(255,255,255,0.7); margin: 0;"><?php echo _l('omni_pilot_approach_consultation_desc'); ?></p>
                                </div>
                                <div class="omni-approach-card" data-approach="followup" style="cursor: pointer; padding: 20px; border: 2px solid rgba(255,255,255,0.2); border-radius: 12px; background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); transition: all 0.3s ease; text-align: center;">
                                    <div style="font-size: 48px; margin-bottom: 10px; color: var(--primary, #00e09b);">
                                        <i class="fa fa-refresh"></i>
                                    </div>
                                    <h6 style="margin: 10px 0 5px; font-weight: 600; color: var(--primary-text, #FFFFFF);"><?php echo _l('omni_pilot_approach_followup'); ?></h6>
                                    <p style="font-size: 12px; color: rgba(255,255,255,0.7); margin: 0;"><?php echo _l('omni_pilot_approach_followup_desc'); ?></p>
                                </div>
                            </div>
                            <input type="hidden" name="approach" id="approach" required>
                            <small class="text-muted" style="display: block; margin-top: 10px;"><?php echo _l('omni_pilot_approach_desc'); ?></small>
                        </div>
                        <div class="form-group">
                            <label><?php echo _l('omni_pilot_language'); ?> <span class="text-danger">*</span></label>
                            <select name="language" id="omni_language" class="selectpicker" data-width="100%" required>
                                <option value="pt-BR"><?php echo _l('portuguese'); ?></option>
                                <option value="en-US"><?php echo _l('english'); ?></option>
                                <option value="es-ES"><?php echo _l('spanish'); ?></option>
                            </select>
                            <small class="text-muted"><?php echo _l('omni_pilot_language_desc'); ?></small>
                        </div>
                    </div>

                    <!-- Step 1: Supply (Leads) -->
                    <div class="wizard-step" data-step="1" style="display: none;">
                        <h5 style="margin-bottom: 20px;">
                            <i class="fa fa-users" style="color: var(--primary, #00e09b);"></i> <?php echo _l('omni_pilot_step_1_supply'); ?>
                        </h5>
                        
                        <!-- Import Method Selection -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label><?php echo _l('omni_pilot_import_method'); ?></label>
                            <div>
                                <label class="radio-inline" style="margin-right: 20px;">
                                    <input type="radio" name="import_method" value="ai" checked> 
                                    <i class="fa fa-robot"></i> <?php echo _l('omni_pilot_ai_finder'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="import_method" value="file"> 
                                    <i class="fa fa-upload"></i> <?php echo _l('omni_pilot_file_upload'); ?>
                                </label>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 20px;">
                            <li role="presentation" class="active">
                                <a href="#omni-ai-finder" aria-controls="omni-ai-finder" role="tab" data-toggle="tab">
                                    <i class="fa fa-robot"></i> <?php echo _l('omni_pilot_ai_finder'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#omni-file-upload" aria-controls="omni-file-upload" role="tab" data-toggle="tab">
                                    <i class="fa fa-upload"></i> <?php echo _l('omni_pilot_file_upload'); ?>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Tab 1: AI Auto-Finder -->
                            <div role="tabpanel" class="tab-pane active" id="omni-ai-finder">
                                <div class="form-group">
                                    <label><?php echo _l('clients_country'); ?></label>
                                    <select name="ai_country" id="omni_ai_country" class="selectpicker" data-width="100%" data-live-search="true">
                                        <option value="Brazil" selected>Brazil</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><?php echo _l('lead_state'); ?></label>
                                    <select name="ai_state" id="omni_ai_state" class="selectpicker" data-width="100%" data-live-search="true">
                                        <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                        <?php 
                                        $brazilian_states = [
                                            'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
                                            'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
                                            'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
                                            'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
                                            'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
                                            'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
                                            'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
                                        ];
                                        foreach ($brazilian_states as $code => $name): 
                                        ?>
                                            <option value="<?php echo $code; ?>"><?php echo $name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><?php echo _l('lead_city'); ?></label>
                                    <select name="ai_city" id="omni_ai_city" class="selectpicker" data-width="100%" data-live-search="true" disabled>
                                        <option value=""><?php echo _l('import_leads_select_state_first'); ?></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><?php echo _l('import_leads_category'); ?></label>
                                    <select name="ai_category" id="omni_ai_category" class="selectpicker" data-width="100%">
                                        <option value=""><?php echo _l('import_leads_select_category'); ?></option>
                                        <?php foreach ($categories as $key => $label): ?>
                                            <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><?php echo _l('import_leads_quantity'); ?></label>
                                    <input type="number" name="ai_quantity" id="omni_ai_quantity" class="form-control" value="100" min="1" max="100">
                                </div>
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary">
                                        <input type="checkbox" name="enable_gemini_enrichment" id="omni_enable_enrichment" value="1" checked>
                                        <label for="omni_enable_enrichment">
                                            <i class="fa fa-robot"></i> <?php echo _l('import_leads_enable_gemini_enrichment'); ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary" id="omni_search_leads_btn">
                                        <i class="fa fa-search"></i> <?php echo _l('import_leads_search'); ?>
                                    </button>
                                </div>
                                <div id="omni_leads_preview" style="display: none; margin-top: 20px;">
                                    <h6><?php echo _l('omni_pilot_leads_preview'); ?></h6>
                                    <div id="omni_leads_count" class="text-muted"></div>
                                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto; margin-top: 10px;">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th width="30"><input type="checkbox" id="omni_select_all_leads"></th>
                                                    <th><?php echo _l('lead_company'); ?></th>
                                                    <th><?php echo _l('leads_dt_name'); ?></th>
                                                    <th><?php echo _l('leads_dt_phonenumber'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody id="omni_leads_tbody"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: File Upload -->
                            <div role="tabpanel" class="tab-pane" id="omni-file-upload">
                                <div class="form-group">
                                    <label><?php echo _l('chose_file'); ?></label>
                                    <div class="file-upload-area" style="border-radius: 8px; padding: 40px; text-align: center; cursor: pointer;">
                                        <i class="fa fa-cloud-upload" style="font-size: 48px; margin-bottom: 10px; color: var(--primary, #00e09b);"></i>
                                        <p><?php echo _l('omni_pilot_drag_drop_file'); ?></p>
                                        <input type="file" name="file_csv" id="omni_file_csv" accept=".csv,.xlsx,.xls" style="display: none;">
                                        <button type="button" class="btn btn-primary" onclick="$('#omni_file_csv').click();">
                                            <i class="fa fa-folder-open"></i> <?php echo _l('chose_file'); ?>
                                        </button>
                                    </div>
                                    <div id="omni_file_info" style="display: none; margin-top: 10px;">
                                        <div class="alert alert-info">
                                            <i class="fa fa-file"></i> <span id="omni_file_name"></span>
                                        </div>
                                    </div>
                                </div>
                                <div id="omni_field_mapping" style="display: none; margin-top: 20px;">
                                    <h6><?php echo _l('omni_pilot_field_mapping'); ?></h6>
                                    <p class="text-muted"><?php echo _l('omni_pilot_field_mapping_desc'); ?></p>
                                    <div id="omni_mapping_fields"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Import Settings (common for both tabs) -->
                        <div class="row" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                            <div class="col-md-3">
                                <?php echo render_leads_status_select($statuses, get_option('leads_default_status'), _l('lead_import_status'), 'ai_import_status', ["required" => true], '', 'form-group'); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_leads_source_select($sources, get_option('leads_default_source'), _l('lead_import_source'), 'ai_import_source', ["required" => true], '', 'form-group'); ?>
                            </div>
                            <div class="col-md-3">
                                <?php echo render_select('ai_import_staffid', $members, ['staffid', ['firstname', 'lastname']], 'leads_import_assignee', get_staff_user_id(), ["required" => true], '', 'form-group'); ?>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php echo _l('chose_file_country'); ?> <span class="text-danger">*</span></label>
                                    <select name="ai_import_country" id="ai_import_country" class="selectpicker" data-width="100%" data-live-search="true" required>
                                        <option value=""></option>
                                        <?php 
                                        $this->load->helper('countries');
                                        foreach (get_all_countries() as $country): 
                                            $selected = ($country['calling_code'] == '55') ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $country['calling_code']; ?>" <?php echo $selected; ?>>
                                                <?php echo $country['short_name']; ?> (<?php echo $country['calling_code']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('contac_import_ia_status'); ?></label>
                                    <select name="ai_import_gpt_status" id="ai_import_gpt_status" class="selectpicker" data-width="100%">
                                        <option value="0"><?php echo _l('contac_import_ia_on'); ?></option>
                                        <option value="1"><?php echo _l('contac_import_ia_off'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Campaign Strategy -->
                    <div class="wizard-step" data-step="2" style="display: none;">
                        <h5 style="margin-bottom: 20px;">
                            <i class="fa fa-bullhorn" style="color: var(--primary, #00e09b);"></i> <span style="color: var(--primary-text, #FFFFFF);"><?php echo _l('omni_pilot_step_2_campaign'); ?></span>
                        </h5>
                        <div class="form-group">
                            <label><?php echo _l('contact_group_device'); ?> <span class="text-danger">*</span></label>
                            <select name="device_id" id="omni_device_id" class="selectpicker" data-width="100%" data-live-search="true" required>
                                <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                <?php foreach ($devices as $device_item): 
                                    $status_class = ($device_item->status == 'open') ? 'text-success' : 'text-danger';
                                    $status_text = ($device_item->status == 'open') ? _l('online') : _l('offline');
                                    $selected = (isset($initial_device_id) && $device_item->dev_id == $initial_device_id) ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $device_item->dev_id; ?>" data-status="<?php echo $device_item->status; ?>" <?php echo $selected; ?>>
                                        <?php echo $device_item->dev_name; ?> 
                                        <span class="<?php echo $status_class; ?>">(<?php echo $status_text; ?>)</span>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted"><?php echo _l('omni_pilot_device_desc'); ?></small>
                        </div>
                        <div class="form-group">
                            <label><?php echo _l('contac_conversation_name'); ?></label>
                            <input type="text" class="form-control" name="campaign_name" id="omni_campaign_name" readonly>
                            <small class="text-muted"><?php echo _l('omni_pilot_campaign_name_desc'); ?></small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('omni_pilot_campaign_import_status'); ?> <span class="text-danger">*</span></label>
                                    <select name="campaign_import_status" id="campaign_import_status" class="selectpicker" data-width="100%" data-live-search="true" required>
                                        <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                        <?php foreach ($statuses as $status): ?>
                                            <option value="<?php echo $status['id']; ?>"><?php echo $status['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted"><?php echo _l('omni_pilot_campaign_import_status_desc'); ?></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('omni_pilot_campaign_final_status'); ?> <span class="text-danger">*</span></label>
                                    <select name="campaign_final_status" id="campaign_final_status" class="selectpicker" data-width="100%" data-live-search="true" required>
                                        <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                        <?php foreach ($statuses as $status): ?>
                                            <option value="<?php echo $status['id']; ?>"><?php echo $status['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted"><?php echo _l('omni_pilot_campaign_final_status_desc'); ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('contac_conversation_start_time_hour'); ?></label>
                                    <input type="time" class="form-control" name="start_time" value="08:00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo _l('contac_conversation_end_time_hour'); ?></label>
                                    <input type="time" class="form-control" name="end_time" value="18:00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: First Strike (Initial Message) -->
                    <div class="wizard-step" data-step="3" style="display: none;">
                        <h5 style="margin-bottom: 20px;">
                            <i class="fa fa-comment" style="color: var(--primary, #00e09b);"></i> <?php echo _l('omni_pilot_step_3_message'); ?>
                        </h5>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" id="omni_generate_messages_btn">
                                <i class="fa fa-magic"></i> <?php echo _l('omni_pilot_generate_messages'); ?>
                            </button>
                            <button type="button" class="btn btn-default" id="omni_manual_message_btn" style="margin-left: 10px;">
                                <i class="fa fa-edit"></i> <?php echo _l('omni_pilot_manual_message'); ?>
                            </button>
                        </div>
                        <div id="omni_message_variations" style="display: none;">
                            <h6><?php echo _l('omni_pilot_select_message'); ?></h6>
                            <div id="omni_message_cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;"></div>
                        </div>
                        <div id="omni_message_editor" style="display: none; margin-top: 20px;">
                            <div class="form-group">
                                <label><?php echo _l('omni_pilot_edit_message'); ?></label>
                                <textarea class="form-control" id="omni_selected_message_text" rows="4"></textarea>
                                <div style="margin-top: 10px;">
                                    <button type="button" class="btn btn-sm btn-default" onclick="insertPlaceholder('{Lead}')"><?php echo _l('lead'); ?></button>
                                    <button type="button" class="btn btn-sm btn-default" onclick="insertPlaceholder('{FirstName}')"><?php echo _l('firstname'); ?></button>
                                    <button type="button" class="btn btn-sm btn-default" onclick="insertPlaceholder('{Agente}')"><?php echo _l('agent'); ?></button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php echo _l('contac_conversation_image'); ?></label>
                                <input type="file" name="message_media" id="omni_message_media" accept="image/*,video/*,audio/*" class="form-control">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-info" id="omni_rewrite_message_btn">
                                    <i class="fa fa-magic"></i> <?php echo _l('omni_pilot_ai_rewrite'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: The Assistant -->
                    <div class="wizard-step" data-step="4" style="display: none;">
                        <h5 style="margin-bottom: 20px;">
                            <i class="fa fa-brain" style="color: var(--primary, #00e09b);"></i> <?php echo _l('omni_pilot_step_4_assistant'); ?>
                        </h5>
                        <div id="omni_assistant_info">
                            <div style="text-align: center; padding: 20px;">
                                <i class="fa fa-spinner fa-spin" style="font-size: 24px; color: var(--primary, #00e09b);"></i>
                                <p style="margin-top: 10px;"><?php echo _l('loading'); ?>...</p>
                            </div>
                        </div>
                        <div id="omni_assistant_selector" style="display: none;">
                            <div class="form-group">
                                <label><?php echo _l('omni_pilot_select_assistant'); ?> <span class="text-danger">*</span></label>
                                <select name="assistant_ai_id" id="omni_assistant_ai_id" class="selectpicker" data-width="100%" data-live-search="true">
                                    <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                    <?php 
                                    $assistants = $this->contactcenter_model->get_assistants_ai();
                                    if ($assistants) {
                                        foreach ($assistants as $assistant): ?>
                                            <option value="<?php echo $assistant->id; ?>"><?php echo htmlspecialchars(!empty($assistant->ai_name) ? $assistant->ai_name : ('Assistant ' . $assistant->id)); ?></option>
                                        <?php endforeach;
                                    }
                                    ?>
                                </select>
                                <small class="text-muted"><?php echo _l('omni_pilot_assistant_selector_desc'); ?></small>
                            </div>
                            <div id="omni_current_assistant_info" style="margin-top: 15px;"></div>
                        </div>
                    </div>

                    <!-- Step 5: Persistence (Follow-up) -->
                    <div class="wizard-step" data-step="5" style="display: none;">
                        <h5 style="margin-bottom: 20px;">
                            <i class="fa fa-redo" style="color: var(--primary, #00e09b);"></i> <?php echo _l('omni_pilot_step_5_followup'); ?>
                        </h5>
                        <p class="text-muted"><?php echo _l('omni_pilot_followup_desc'); ?></p>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" id="omni_generate_followups_btn">
                                <i class="fa fa-magic"></i> <?php echo _l('omni_pilot_generate_followups'); ?>
                            </button>
                            <button type="button" class="btn btn-success" id="omni_add_followup_btn" style="margin-left: 10px;">
                                <i class="fa fa-plus"></i> <?php echo _l('omni_pilot_add_followup'); ?>
                            </button>
                        </div>
                        <div id="omni_followup_timeline" style="margin-top: 20px;">
                            <?php 
                            $followup_hours = [1, 8, 24, 48, 168];
                            $hour_labels = [
                                1 => _l('omni_pilot_followup_1h'),
                                8 => _l('omni_pilot_followup_8h'),
                                24 => _l('omni_pilot_followup_24h'),
                                48 => _l('omni_pilot_followup_48h'),
                                168 => _l('omni_pilot_followup_1week')
                            ];
                            foreach ($followup_hours as $index => $hours): 
                            ?>
                                <div class="followup-slot" data-hours="<?php echo $hours; ?>" style="border-radius: 5px; padding: 15px; margin-bottom: 15px; position: relative;">
                                    <button type="button" class="btn btn-sm btn-danger omni-remove-followup" style="position: absolute; top: 10px; right: 10px; padding: 2px 8px;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-right: 40px;">
                                        <strong><?php echo $hour_labels[$hours]; ?></strong>
                                        <span class="badge badge-info"><?php echo _l('omni_pilot_message'); ?> <?php echo $index + 1; ?></span>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 10px;">
                                        <textarea class="form-control followup-message-text" rows="2" placeholder="<?php echo _l('omni_pilot_followup_message_placeholder'); ?>"></textarea>
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <input type="file" class="form-control followup-media" accept="image/*,video/*,audio/*" style="font-size: 12px;">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Step 6: Launch -->
                    <div class="wizard-step" data-step="6" style="display: none;">
                        <h5 style="margin-bottom: 20px;">
                            <i class="fa fa-rocket" style="color: var(--primary, #00e09b);"></i> <span style="color: var(--primary-text, #FFFFFF);"><?php echo _l('omni_pilot_step_6_launch'); ?></span>
                        </h5>
                        <div class="alert alert-success" style="text-align: center; padding: 30px; background: rgba(0, 224, 155, 0.15); border-color: var(--primary, #00e09b);">
                            <i class="fa fa-check-circle" style="font-size: 48px; margin-bottom: 15px; color: var(--primary, #00e09b);"></i>
                            <h5 style="color: var(--primary-text, #FFFFFF);"><?php echo _l('omni_pilot_ready_to_launch'); ?></h5>
                            <p style="color: rgba(255,255,255,0.8);"><?php echo _l('omni_pilot_launch_desc'); ?></p>
                            <div id="omni_launch_summary" style="text-align: left; margin-top: 20px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); padding: 15px; border-radius: 5px; border: 1px solid rgba(255,255,255,0.2);">
                                <ul style="list-style: none; padding: 0;">
                                    <li style="color: var(--primary-text, #FFFFFF); margin-bottom: 8px;"><i class="fa fa-check" style="color: var(--primary, #00e09b); margin-right: 8px;"></i> <span id="summary_goal"></span></li>
                                    <li style="color: var(--primary-text, #FFFFFF); margin-bottom: 8px;"><i class="fa fa-check" style="color: var(--primary, #00e09b); margin-right: 8px;"></i> <span id="summary_leads"></span></li>
                                    <li style="color: var(--primary-text, #FFFFFF); margin-bottom: 8px;"><i class="fa fa-check" style="color: var(--primary, #00e09b); margin-right: 8px;"></i> <span id="summary_campaign"></span></li>
                                    <li style="color: var(--primary-text, #FFFFFF); margin-bottom: 8px;"><i class="fa fa-check" style="color: var(--primary, #00e09b); margin-right: 8px;"></i> <span id="summary_message"></span></li>
                                    <li style="color: var(--primary-text, #FFFFFF); margin-bottom: 8px;"><i class="fa fa-check" style="color: var(--primary, #00e09b); margin-right: 8px;"></i> <span id="summary_followup"></span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer" style="padding: 15px 20px;">
                <button type="button" class="btn btn-primary" id="omni_prev_btn" style="display: none;">
                    <i class="fa fa-arrow-left"></i> <?php echo _l('previous'); ?>
                </button>
                <button type="button" class="btn btn-primary" id="omni_next_btn">
                    <?php echo _l('next'); ?> <i class="fa fa-arrow-right"></i>
                </button>
                <button type="button" class="btn btn-success" id="omni_launch_btn" style="display: none;">
                    <i class="fa fa-rocket"></i> <?php echo _l('omni_pilot_start'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Progress Badge -->
<div id="omniPilotProgressBadge" style="display: none; position: fixed; bottom: 20px; right: 20px; border-radius: 8px; padding: 20px; min-width: 300px; z-index: 9999;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h6 style="margin: 0;">
            <i class="fa fa-rocket" style="color: var(--primary, #00e09b);"></i> <?php echo _l('omni_pilot_progress'); ?>
        </h6>
        <button type="button" class="close" onclick="$('#omniPilotProgressBadge').fadeOut();" style="opacity: 0.5;">&times;</button>
    </div>
    <div class="progress" style="height: 8px; margin-bottom: 10px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" id="omni_progress_bar" style="width: 0%;"></div>
    </div>
    <div id="omni_progress_details">
        <div><strong><?php echo _l('omni_pilot_goal'); ?>:</strong> <span id="omni_goal_progress">0/0</span></div>
        <div style="margin-top: 5px;"><strong><?php echo _l('omni_pilot_phase'); ?>:</strong> <span id="omni_current_phase">-</span></div>
        <div style="margin-top: 5px;"><strong><?php echo _l('omni_pilot_campaign'); ?>:</strong> <span id="omni_campaign_status">-</span></div>
    </div>
</div>
