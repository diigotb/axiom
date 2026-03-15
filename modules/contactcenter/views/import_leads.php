<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-justify-between tw-items-center">
                    <?php echo _l('import_leads'); ?>
                    <a class="btn btn-primary" href="<?= site_url("modules/contactcenter/assets/image/sample.xlsx"); ?>" download><?= _l("chose_file_sample"); ?></a>
                </h4>
                
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 20px;">
                    <li role="presentation" class="active">
                        <a href="#manual-import" aria-controls="manual-import" role="tab" data-toggle="tab">
                            <i class="fa fa-upload"></i> <?php echo _l('import_leads_manual'); ?>
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#ai-search" aria-controls="ai-search" role="tab" data-toggle="tab">
                            <i class="fa fa-robot"></i> <?php echo _l('import_leads_ai_search'); ?>
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Manual Import Tab -->
                    <div role="tabpanel" class="tab-pane active" id="manual-import">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="tw-mt-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <?php echo form_open_multipart(admin_url('contactcenter/add_import_leads'), ['id' => 'import_form']); ?>
                                            <?php echo form_hidden('leads_import', 'true'); ?>
                                            <?php echo render_input('file_csv', 'chose_file', '', 'file'); ?>
                                            <?php
                                            echo render_leads_status_select($statuses, ($this->input->post('status') ? $this->input->post('status') : get_option('leads_default_status')), _l('lead_import_status') . ' (fallback)', 'status', [], true);
                                            echo render_leads_source_select($sources, ($this->input->post('source') ? $this->input->post('source') : get_option('leads_default_source')), _l('lead_import_source') . ' (fallback)');
                                            ?>
                                            <?php echo render_select('responsible', $members, ['staffid', ['firstname', 'lastname']], 'leads_import_assignee', $this->input->post('responsible'), ["required" => true]); ?>
                                            <div class="form-group ">
                                                <label class="control-label" for="lastname"><small class="req text-danger">* </small><?php echo _l('chose_file_country'); ?></label>
                                                <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" name="country" class="form-control selectpicker" id="country" required>
                                                    <option value=""></option>
                                                    <?php foreach (get_all_countries() as $country) { ?>
                                                        <option value="<?php echo $country['calling_code']; ?>" <?php echo set_select('country', $country['country_id']); ?>><?php echo $country['short_name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="form-group ">
                                                <label class="control-label" for="lastname"><small class="req text-danger">* </small><?php echo _l('contac_import_ia_status'); ?></label>
                                                <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" name="gpt_status" class="form-control selectpicker" id="language" required>                                           
                                                    <option value="0"><?= _l("contac_import_ia_on"); ?></option>
                                                    <option value="1"><?= _l("contac_import_ia_off"); ?></option>                                        
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <button type="button" class="btn btn-primary import btn-import-submit"><?php echo _l('import'); ?></button>
                                            </div>
                                            <?php echo form_close(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Search Tab -->
                    <div role="tabpanel" class="tab-pane" id="ai-search">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> 
                                    <?php echo _l('import_leads_ai_info'); ?>
                                </div>
                                
                                <!-- Search Form -->
                                <form id="ai-search-form">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label"><small class="req text-danger">* </small><?php echo _l('clients_country'); ?></label>
                                                <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" 
                                                    data-live-search="true" 
                                                    name="ai_country" 
                                                    class="form-control selectpicker" 
                                                    id="ai_country" 
                                                    required>
                                                    <option value=""></option>
                                                    <?php 
                                                    $brazil_selected = false;
                                                    foreach (get_all_countries() as $country) { 
                                                        $selected = '';
                                                        if ($country['short_name'] == 'Brazil' && !$brazil_selected) {
                                                            $selected = 'selected';
                                                            $brazil_selected = true;
                                                        }
                                                    ?>
                                                        <option value="<?php echo $country['short_name']; ?>" <?php echo $selected; ?>><?php echo $country['short_name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label"><small class="req text-danger">* </small><?php echo _l('lead_state'); ?></label>
                                                <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" 
                                                    data-live-search="true" 
                                                    name="ai_state" 
                                                    class="form-control selectpicker" 
                                                    id="ai_state" 
                                                    disabled>
                                                    <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                                    <option value="AC">Acre (AC)</option>
                                                    <option value="AL">Alagoas (AL)</option>
                                                    <option value="AP">Amapá (AP)</option>
                                                    <option value="AM">Amazonas (AM)</option>
                                                    <option value="BA">Bahia (BA)</option>
                                                    <option value="CE">Ceará (CE)</option>
                                                    <option value="DF">Distrito Federal (DF)</option>
                                                    <option value="ES">Espírito Santo (ES)</option>
                                                    <option value="GO">Goiás (GO)</option>
                                                    <option value="MA">Maranhão (MA)</option>
                                                    <option value="MT">Mato Grosso (MT)</option>
                                                    <option value="MS">Mato Grosso do Sul (MS)</option>
                                                    <option value="MG">Minas Gerais (MG)</option>
                                                    <option value="PA">Pará (PA)</option>
                                                    <option value="PB">Paraíba (PB)</option>
                                                    <option value="PR">Paraná (PR)</option>
                                                    <option value="PE">Pernambuco (PE)</option>
                                                    <option value="PI">Piauí (PI)</option>
                                                    <option value="RJ">Rio de Janeiro (RJ)</option>
                                                    <option value="RN">Rio Grande do Norte (RN)</option>
                                                    <option value="RS">Rio Grande do Sul (RS)</option>
                                                    <option value="RO">Rondônia (RO)</option>
                                                    <option value="RR">Roraima (RR)</option>
                                                    <option value="SC">Santa Catarina (SC)</option>
                                                    <option value="SP">São Paulo (SP)</option>
                                                    <option value="SE">Sergipe (SE)</option>
                                                    <option value="TO">Tocantins (TO)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label"><small class="req text-danger">* </small><?php echo _l('lead_city'); ?></label>
                                                <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" 
                                                    data-live-search="true" 
                                                    name="ai_city" 
                                                    class="form-control selectpicker" 
                                                    id="ai_city" 
                                                    required
                                                    disabled>
                                                    <option value=""><?php echo _l('import_leads_select_state_first'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label"><small class="req text-danger">* </small><?php echo _l('import_leads_category'); ?></label>
                                                <select name="ai_category" class="form-control selectpicker" id="ai_category" required>
                                                    <option value=""><?php echo _l('import_leads_select_category'); ?></option>
                                                    <option value="restaurants"><?php echo _l('import_leads_category_restaurants'); ?></option>
                                                    <option value="retail stores"><?php echo _l('import_leads_category_retail'); ?></option>
                                                    <option value="services"><?php echo _l('import_leads_category_services'); ?></option>
                                                    <option value="healthcare"><?php echo _l('import_leads_category_healthcare'); ?></option>
                                                    <option value="automotive"><?php echo _l('import_leads_category_automotive'); ?></option>
                                                    <option value="real estate"><?php echo _l('import_leads_category_real_estate'); ?></option>
                                                    <option value="beauty salons"><?php echo _l('import_leads_category_beauty'); ?></option>
                                                    <option value="gyms"><?php echo _l('import_leads_category_gyms'); ?></option>
                                                    <option value="hotels"><?php echo _l('import_leads_category_hotels'); ?></option>
                                                    <option value="law firms"><?php echo _l('import_leads_category_law'); ?></option>
                                                    <option value="accounting"><?php echo _l('import_leads_category_accounting'); ?></option>
                                                    <option value="custom"><?php echo _l('import_leads_category_custom'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group" id="custom-category-group" style="display: none;">
                                                <?php echo render_input('ai_custom_category', 'import_leads_custom_category', '', 'text', ['placeholder' => _l('import_leads_custom_category_placeholder')], '', 'form-group'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label"><?php echo _l('import_leads_quantity'); ?></label>
                                                <input type="number" name="ai_quantity" id="ai_quantity" class="form-control" value="100" min="1" max="100">
                                                <small class="help-block"><?php echo _l('import_leads_quantity_help'); ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="checkbox checkbox-primary" style="margin-top: 25px;">
                                                    <input type="checkbox" name="enable_gemini_enrichment" id="enable_gemini_enrichment" value="1">
                                                    <label for="enable_gemini_enrichment">
                                                        <i class="fa fa-robot"></i> <?php echo _l('import_leads_enable_gemini_enrichment'); ?>
                                                    </label>
                                                    <small class="help-block"><?php echo _l('import_leads_gemini_enrichment_help'); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" style="margin-top: 25px;">
                                                <button type="button" id="btn-ai-search" class="btn btn-primary">
                                                    <i class="fa fa-search"></i> <?php echo _l('import_leads_search'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!-- Loading Indicator -->
                                <div id="ai-search-loading" style="display: none; text-align: center; padding: 40px;">
                                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                                    <p style="margin-top: 20px;" id="loading-message"><?php echo _l('import_leads_searching'); ?></p>
                                    <div id="enrichment-progress" style="display: none; margin-top: 20px;">
                                        <div class="progress" style="width: 100%; max-width: 500px; margin: 0 auto;">
                                            <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 0%">
                                                <span id="enrichment-progress-text">0%</span>
                                            </div>
                                        </div>
                                        <p style="margin-top: 10px; font-size: 12px; color: #666;" id="enrichment-status"></p>
                                    </div>
                                </div>

                                <!-- Error Message -->
                                <div id="ai-search-error" class="alert alert-danger" style="display: none;"></div>

                                <!-- Results Preview -->
                                <div id="ai-search-results" style="display: none;">
                                    <hr>
                                    <h5><?php echo _l('import_leads_preview_results'); ?></h5>
                                    <p id="ai-results-count" class="text-muted"></p>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="ai-leads-table">
                                            <thead>
                                                <tr>
                                                    <th width="30">
                                                        <input type="checkbox" id="select-all-leads">
                                                    </th>
                                                    <th><?php echo _l('lead_company'); ?></th>
                                                    <th><?php echo _l('leads_dt_name'); ?></th>
                                                    <th><?php echo _l('leads_dt_phonenumber'); ?></th>
                                                    <th><?php echo _l('import_leads_whatsapp'); ?></th>
                                                    <th><?php echo _l('lead_email'); ?></th>
                                                    <th><?php echo _l('lead_website'); ?></th>
                                                    <th><?php echo _l('import_leads_social_media'); ?></th>
                                                    <th><?php echo _l('lead_address'); ?></th>
                                                    <th><?php echo _l('lead_city'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody id="ai-leads-tbody">
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Field Mapping Confirmation -->
                                    <div class="row" style="margin-top: 20px; display: none;" id="field-mapping-section">
                                        <div class="col-md-12">
                                            <h5><?php echo _l('import_leads_field_mapping_confirmation'); ?></h5>
                                            <p class="text-muted"><?php echo _l('import_leads_field_mapping_confirmation_help'); ?></p>
                                            <p class="text-info">
                                                <i class="fa fa-info-circle"></i> 
                                                <a href="<?php echo admin_url('contactcenter/settings?group=contactcenter'); ?>" target="_blank">
                                                    <?php echo _l('import_leads_edit_mappings_in_settings'); ?>
                                                </a>
                                            </p>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped" id="field-mapping-table">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo _l('import_leads_ai_field'); ?></th>
                                                            <th><?php echo _l('import_leads_sample_value'); ?></th>
                                                            <th><?php echo _l('import_leads_map_to'); ?></th>
                                                            <th><?php echo _l('import_leads_status'); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="field-mapping-tbody">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Import Settings -->
                                    <div class="row" style="margin-top: 20px;">
                                        <div class="col-md-12">
                                            <h5><?php echo _l('import_leads_import_settings'); ?></h5>
                                        </div>
                                        <div class="col-md-3">
                                            <?php
                                            echo render_leads_status_select($statuses, get_option('leads_default_status'), _l('lead_import_status'), 'ai_import_status', ["required" => true], '', 'form-group');
                                            ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?php
                                            echo render_leads_source_select($sources, get_option('leads_default_source'), _l('lead_import_source'), 'ai_import_source', ["required" => true], '', 'form-group');
                                            ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?php echo render_select('ai_import_staffid', $members, ['staffid', ['firstname', 'lastname']], 'leads_import_assignee', get_staff_user_id(), ["required" => true], '', 'form-group'); ?>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label"><small class="req text-danger">* </small><?php echo _l('chose_file_country'); ?></label>
                                                <select data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" 
                                                    data-live-search="true" 
                                                    name="ai_import_country" 
                                                    class="form-control selectpicker" 
                                                    id="ai_import_country" 
                                                    required>
                                                    <option value=""></option>
                                                    <?php foreach (get_all_countries() as $country) { ?>
                                                        <option value="<?php echo $country['calling_code']; ?>"><?php echo $country['short_name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label"><small class="req text-danger">* </small><?php echo _l('contac_import_ia_status'); ?></label>
                                                <select name="ai_import_gpt_status" class="form-control selectpicker" id="ai_import_gpt_status" required>                                           
                                                    <option value="0"><?= _l("contac_import_ia_on"); ?></option>
                                                    <option value="1"><?= _l("contac_import_ia_off"); ?></option>                                        
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="button" id="btn-ai-import" class="btn btn-success" disabled>
                                            <i class="fa fa-download"></i> <?php echo _l('import_leads_import_selected'); ?>
                                        </button>
                                        <span id="ai-import-status" class="text-muted" style="margin-left: 10px;"></span>
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
<style>
    /* Improve readability for AI leads table */
    #ai-leads-table tr.success {
        background-color: #d4edda !important;
    }
    
    #ai-leads-table tr.success td {
        color: #155724 !important;
    }
    
    #ai-leads-table tr.success a {
        color: #0066cc !important;
        font-weight: 600 !important;
        text-decoration: underline !important;
    }
    
    #ai-leads-table tr.success a:hover {
        color: #0052a3 !important;
    }
    
    /* Ensure website links are readable */
    #ai-leads-table td a[href^="http"] {
        color: #0066cc !important;
        font-weight: 600 !important;
        text-decoration: underline !important;
    }
    
    #ai-leads-table tr.success td a[href^="http"] {
        color: #004499 !important;
        font-weight: 700 !important;
    }
    
    /* Improve confidence badge visibility */
    #ai-leads-table .label {
        font-weight: bold !important;
        padding: 4px 8px !important;
        font-size: 11px !important;
        text-shadow: none !important;
    }
    
    #ai-leads-table tr.success .label-success {
        background-color: #28a745 !important;
        color: #fff !important;
    }
    
    #ai-leads-table tr.success .label-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }
    
    #ai-leads-table tr.success .label-danger {
        background-color: #dc3545 !important;
        color: #fff !important;
    }
    
    /* Improve "Not found" text visibility */
    #ai-leads-table tr.success span[style*="color: #666"],
    #ai-leads-table tr.success span[style*="color: #999"] {
        color: #666 !important;
        font-weight: 600 !important;
    }
    
    /* Ensure WhatsApp numbers are readable */
    #ai-leads-table tr.success span[style*="color: #25D366"] {
        color: #25D366 !important;
        font-weight: bold !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;
    }
    
    /* Ensure social media links are readable */
    #ai-leads-table tr.success a[style*="color: #E4405F"] {
        color: #E4405F !important;
        font-weight: 600 !important;
    }
    
    /* Improve summary text readability */
    #ai-results-count {
        font-size: 14px !important;
        font-weight: 500 !important;
    }
    
    #ai-results-count span {
        font-weight: 600 !important;
    }
</style>
<script>
    $(function() {
        appValidateForm($('#import_form'), {
            file_csv: {
                required: true,
                extension: "xlsx|xls"
            },
            source: 'required',
            status: 'required'
        });

        // Initialize selectpickers
        if (typeof $().selectpicker === 'function') {
            $('.selectpicker').selectpicker();
        }

        // Initialize: If Brazil is pre-selected, enable state dropdown
        var initialCountry = $('#ai_country').val();
        if (initialCountry && initialCountry.toLowerCase() === 'brazil') {
            $('#ai_state').prop('disabled', false).prop('required', true);
            if ($('#ai_state').hasClass('selectpicker')) {
                $('#ai_state').selectpicker('refresh');
            }
        }

        // Store found leads
        var foundLeads = [];

        // Handle custom category toggle
        $('#ai_category').on('change', function() {
            if ($(this).val() === 'custom') {
                $('#custom-category-group').show();
                $('#ai_custom_category').prop('required', true);
            } else {
                $('#custom-category-group').hide();
                $('#ai_custom_category').prop('required', false);
            }
        });

        // Handle country change - enable/disable state dropdown for Brazil
        $('#ai_country').on('change', function() {
            var country = $(this).val();
            
            if (country && country.toLowerCase() === 'brazil') {
                // Enable state dropdown for Brazil
                $('#ai_state').prop('disabled', false);
                $('#ai_state').prop('required', true);
                if ($('#ai_state').hasClass('selectpicker')) {
                    $('#ai_state').selectpicker('refresh');
                }
            } else {
                // Disable state dropdown and reset for other countries
                $('#ai_state').val('').prop('disabled', true).prop('required', false);
                $('#ai_city').html('<option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>');
                $('#ai_city').prop('disabled', false).prop('required', true);
                if ($('#ai_state').hasClass('selectpicker')) {
                    $('#ai_state').selectpicker('refresh');
                }
                if ($('#ai_city').hasClass('selectpicker')) {
                    $('#ai_city').selectpicker('refresh');
                }
            }
        });

        // Handle state change - load cities for Brazil
        $('#ai_state').on('change', function() {
            var state = $(this).val();
            var country = $('#ai_country').val();
            
            if (state && country && country.toLowerCase() === 'brazil') {
                loadCitiesForState(state);
            } else {
                // Reset city dropdown
                $('#ai_city').html('<option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>');
                $('#ai_city').prop('disabled', true);
                if ($('#ai_city').hasClass('selectpicker')) {
                    $('#ai_city').selectpicker('refresh');
                }
            }
        });

        // Function to load cities for a Brazilian state
        function loadCitiesForState(state) {
            $('#ai_city').prop('disabled', true);
            $('#ai_city').html('<option value=""><?php echo _l('import_leads_loading_cities'); ?>...</option>');
            if ($('#ai_city').hasClass('selectpicker')) {
                $('#ai_city').selectpicker('refresh');
            }

            $.ajax({
                url: '<?php echo admin_url('contactcenter/ajax_get_brazilian_cities'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    state: state
                },
                success: function(response) {
                    if (response.success && response.cities && response.cities.length > 0) {
                        var options = '<option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>';
                        $.each(response.cities, function(index, city) {
                            options += '<option value="' + city.value + '">' + city.label + '</option>';
                        });
                        $('#ai_city').html(options);
                        $('#ai_city').prop('disabled', false);
                    } else {
                        $('#ai_city').html('<option value=""><?php echo _l('import_leads_no_cities_found'); ?></option>');
                        $('#ai_city').prop('disabled', true);
                    }
                    if ($('#ai_city').hasClass('selectpicker')) {
                        $('#ai_city').selectpicker('refresh');
                    }
                },
                error: function(xhr, status, error) {
                    $('#ai_city').html('<option value=""><?php echo _l('import_leads_error_loading_cities'); ?></option>');
                    $('#ai_city').prop('disabled', true);
                    if ($('#ai_city').hasClass('selectpicker')) {
                        $('#ai_city').selectpicker('refresh');
                    }
                }
            });
        }

        // AI Search button click
        $('#btn-ai-search').on('click', function() {
            var $btn = $(this);
            var country = $('#ai_country').val();
            var state = $('#ai_state').val();
            var city = $('#ai_city').val();
            var category = $('#ai_category').val();
            var customCategory = $('#ai_custom_category').val();
            var quantity = $('#ai_quantity').val() || 100;

            // Validate
            if (!country) {
                alert('<?php echo _l('import_leads_validation_country'); ?>');
                return;
            }
            
            // For Brazil, state and city are required
            if (country.toLowerCase() === 'brazil') {
                if (!state) {
                    alert('<?php echo _l('import_leads_validation_state'); ?>');
                    return;
                }
                if (!city) {
                    alert('<?php echo _l('import_leads_validation_city'); ?>');
                    return;
                }
            } else {
                if (!city) {
                    alert('<?php echo _l('import_leads_validation_city'); ?>');
                    return;
                }
            }

            if (!category) {
                alert('<?php echo _l('import_leads_validation_category'); ?>');
                return;
            }

            if (category === 'custom' && !customCategory) {
                alert('<?php echo _l('import_leads_validation_custom_category'); ?>');
                return;
            }

            // Use custom category if selected
            var searchCategory = category === 'custom' ? customCategory : category;
            
            // Get Gemini enrichment setting
            var enableGeminiEnrichment = $('#enable_gemini_enrichment').is(':checked');

            // Show loading, hide results and errors
            $('#ai-search-loading').show();
            $('#ai-search-results').hide();
            $('#ai-search-error').hide();
            $('#enrichment-progress').hide();
            $btn.prop('disabled', true);
            
            // Update loading message
            if (enableGeminiEnrichment) {
                $('#loading-message').html('<?php echo _l('import_leads_searching'); ?><br><small style="color: #666;"><?php echo _l('import_leads_enrichment_will_start'); ?></small>');
            } else {
                $('#loading-message').html('<?php echo _l('import_leads_searching'); ?>');
            }

            // Make AJAX request
            $.ajax({
                url: '<?php echo admin_url('contactcenter/ajax_search_leads_ai'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    country: country,
                    state: state,
                    state_code: state, // Send state code for Brazil
                    city: city,
                    category: searchCategory,
                    quantity: quantity,
                    batch_size: 100,
                    enable_gemini_enrichment: enableGeminiEnrichment
                },
                success: function(response) {
                    if (response.success) {
                        foundLeads = response.leads || [];
                        
                        // Show results with enrichment stats
                        $('#ai-search-loading').hide();
                        $btn.prop('disabled', false);
                        displayLeads(foundLeads, response.enrichment_stats);
                        $('#ai-search-results').show();
                        
                        // Log enrichment stats to console for debugging
                        if (response.enrichment_stats) {
                            console.log('Enrichment Stats:', response.enrichment_stats);
                            console.log('WhatsApp found:', response.enrichment_stats.whatsapp_found);
                            console.log('Social Media found:', response.enrichment_stats.social_media_found);
                            console.log('Total enriched:', response.enrichment_stats.enriched);
                        }
                    } else {
                        $('#ai-search-loading').hide();
                        $btn.prop('disabled', false);
                        $('#ai-search-error').text(response.error || '<?php echo _l('import_leads_search_error'); ?>').show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#ai-search-loading').hide();
                    $btn.prop('disabled', false);
                    $('#ai-search-error').text('<?php echo _l('import_leads_search_error'); ?>: ' + error).show();
                    console.error('AI Search Error:', xhr.responseText);
                }
            });
        });

        // Display leads in table
        function displayLeads(leads, enrichmentStats) {
            var tbody = $('#ai-leads-tbody');
            tbody.empty();
            
            if (leads.length === 0) {
                tbody.html('<tr><td colspan="10" class="text-center"><?php echo _l('import_leads_no_results'); ?></td></tr>');
                $('#ai-results-count').text('<?php echo _l('import_leads_no_results'); ?>');
                return;
            }

            var enrichedCount = 0;
            var whatsappFound = 0;
            var socialMediaFound = 0;

            $.each(leads, function(index, lead) {
                var whatsapp = lead.whatsapp_number || lead.whatsapp_enriched || null;
                var socialMedia = lead.social_media || null;
                var confidence = lead.enrichment_confidence || '';
                var isEnriched = whatsapp || socialMedia;
                
                if (isEnriched) enrichedCount++;
                if (whatsapp) whatsappFound++;
                if (socialMedia) socialMediaFound++;
                
                var confidenceBadge = '';
                if (confidence) {
                    var badgeClass = confidence === 'high' ? 'success' : confidence === 'medium' ? 'warning' : 'danger';
                    var confidenceText = '';
                    if (confidence === 'high') {
                        confidenceText = '<?php echo _l('import_leads_confidence_high'); ?>';
                    } else if (confidence === 'medium') {
                        confidenceText = '<?php echo _l('import_leads_confidence_medium'); ?>';
                    } else {
                        confidenceText = '<?php echo _l('import_leads_confidence_low'); ?>';
                    }
                    confidenceBadge = ' <span class="label label-' + badgeClass + '" style="font-weight: bold; color: #fff;" title="<?php echo _l('import_leads_confidence_label'); ?>: ' + confidenceText + '">' + confidenceText + '</span>';
                }
                
                var whatsappCell = '-';
                if (whatsapp) {
                    whatsappCell = '<span style="color: #25D366; font-weight: bold;"><i class="fa fa-phone"></i> ' + whatsapp + '</span>' + confidenceBadge;
                } else if (lead.enrichment_attempted) {
                    whatsappCell = '<span style="color: #999; font-style: italic;">Not found</span>';
                }
                
                var socialMediaCell = '-';
                if (socialMedia) {
                    // Check if it's Instagram or Facebook
                    var socialIcon = 'fa-globe';
                    if (socialMedia.indexOf('instagram.com') !== -1) {
                        socialIcon = 'fa-camera';
                    } else if (socialMedia.indexOf('facebook.com') !== -1) {
                        socialIcon = 'fa-facebook';
                    }
                    socialMediaCell = '<a href="' + socialMedia + '" target="_blank" style="color: #E4405F;"><i class="fa ' + socialIcon + '"></i> ' + socialMedia + '</a>';
                } else if (lead.enrichment_attempted) {
                    socialMediaCell = '<span style="color: #999; font-style: italic;">Not found</span>';
                }
                
                var rowClass = isEnriched ? 'success' : '';
                var enrichmentIcon = isEnriched ? ' <i class="fa fa-check-circle text-success" title="Enriched with AI"></i>' : '';
                
                var row = '<tr data-lead-index="' + index + '" class="' + rowClass + '">' +
                    '<td><input type="checkbox" class="lead-checkbox" checked></td>' +
                    '<td>' + (lead.company || '-') + enrichmentIcon + '</td>' +
                    '<td>' + (lead.name || '-') + '</td>' +
                    '<td>' + (lead.phone || '-') + '</td>' +
                    '<td>' + whatsappCell + '</td>' +
                    '<td>' + (lead.email || '-') + '</td>' +
                    '<td>' + (lead.website ? '<a href="' + lead.website + '" target="_blank" style="color: #0066cc; font-weight: 500; text-decoration: underline;">' + lead.website + '</a>' : '-') + '</td>' +
                    '<td>' + socialMediaCell + '</td>' +
                    '<td>' + (lead.address || '-') + '</td>' +
                    '<td>' + (lead.city || '-') + '</td>' +
                    '</tr>';
                tbody.append(row);
            });

            var countText = '<?php echo _l('import_leads_found'); ?>: ' + leads.length;
            if (enrichmentStats) {
                countText += ' | <span style="color: #25D366; font-weight: 600;"><i class="fa fa-phone"></i> <?php echo _l('import_leads_whatsapp_label'); ?>: ' + whatsappFound + '</span>';
                countText += ' | <span style="color: #E4405F; font-weight: 600;"><i class="fa fa-globe"></i> <?php echo _l('import_leads_social_label'); ?>: ' + socialMediaFound + '</span>';
                countText += ' | <span style="color: #5cb85c; font-weight: 600;"><i class="fa fa-check-circle"></i> <?php echo _l('import_leads_enriched_label'); ?>: ' + enrichedCount + '/' + leads.length + '</span>';
            }
            $('#ai-results-count').html(countText);
            updateImportButton();
            
            // Show field mapping section if we have enriched data
            if (enrichmentStats && (enrichmentStats.whatsapp_found > 0 || enrichmentStats.social_media_found > 0)) {
                displayFieldMapping(leads);
            }
        }
        
        // Display field mapping confirmation
        function displayFieldMapping(leads) {
            // Get field mappings from server
            $.ajax({
                url: '<?php echo admin_url('contactcenter/ajax_get_field_mappings'); ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.mappings) {
                        var mappings = response.mappings;
                        var tbody = $('#field-mapping-tbody');
                        tbody.empty();
                        
                        // Find sample values from leads
                        var sampleLead = null;
                        for (var i = 0; i < leads.length; i++) {
                            if (leads[i].whatsapp_number || leads[i].whatsapp_enriched || leads[i].social_media || leads[i].rating) {
                                sampleLead = leads[i];
                                break;
                            }
                        }
                        if (!sampleLead && leads.length > 0) {
                            sampleLead = leads[0];
                        }
                        
                        if (!sampleLead) return;
                        
                        // AI fields that might need mapping
                        var aiFields = [
                            { key: 'whatsapp_number', label: '<?php echo _l('import_leads_whatsapp'); ?>', sample: sampleLead.whatsapp_number || sampleLead.whatsapp_enriched || null },
                            { key: 'social_media', label: '<?php echo _l('import_leads_social_media'); ?>', sample: sampleLead.social_media || null },
                            { key: 'rating', label: '<?php echo _l('import_leads_rating'); ?>', sample: sampleLead.rating || null }
                        ];
                        
                        var hasMappings = false;
                        var rawMappings = response.raw_mappings || {};
                        
                        aiFields.forEach(function(field) {
                            if (field.sample) {
                                var mapping = mappings[field.key] || '';
                                var rawMapping = rawMappings[field.key] || '';
                                var sampleText = String(field.sample);
                                if (sampleText.length > 50) {
                                    sampleText = sampleText.substring(0, 50) + '...';
                                }
                                
                                var statusHtml = '';
                                var statusClass = '';
                                if (mapping) {
                                    statusHtml = '<span class="label label-success"><i class="fa fa-check"></i> <?php echo _l('import_leads_mapped'); ?></span>';
                                    statusClass = 'success';
                                } else {
                                    statusHtml = '<span class="label label-warning"><i class="fa fa-exclamation-triangle"></i> <?php echo _l('import_leads_not_mapped'); ?></span>';
                                    statusClass = 'warning';
                                }
                                
                                var row = '<tr class="' + statusClass + '">' +
                                    '<td><strong>' + field.label + '</strong></td>' +
                                    '<td><code>' + sampleText + '</code></td>' +
                                    '<td>' + (mapping ? '<span class="label label-info">' + mapping + '</span>' : '<span class="text-muted"><?php echo _l('import_leads_not_mapped'); ?></span>') + '</td>' +
                                    '<td>' + statusHtml + '</td>' +
                                    '</tr>';
                                tbody.append(row);
                                hasMappings = true;
                            }
                        });
                        
                        if (hasMappings) {
                            $('#field-mapping-section').show();
                        }
                    }
                },
                error: function() {
                    console.log('Error loading field mappings');
                }
            });
        }
        
        // Poll for enrichment status (simplified - in real implementation, use server-side status tracking)
        function pollEnrichmentStatus(searchId) {
            // This is a placeholder - in production, you'd poll a status endpoint
            // For now, we'll just show the results after a delay
            setTimeout(function() {
                $('#ai-search-loading').hide();
                $btn.prop('disabled', false);
                displayLeads(foundLeads);
                $('#ai-search-results').show();
            }, 2000);
        }
        
        // Update enrichment progress bar
        function updateEnrichmentProgress(status) {
            if (status && status.current && status.total) {
                var percent = Math.round((status.current / status.total) * 100);
                $('.progress-bar').css('width', percent + '%');
                $('#enrichment-progress-text').text(percent + '%');
                $('#enrichment-status').text('Enriching lead ' + status.current + ' of ' + status.total + '...');
            }
        }

        // Select all checkbox
        $('#select-all-leads').on('change', function() {
            $('.lead-checkbox').prop('checked', $(this).prop('checked'));
            updateImportButton();
        });

        // Individual checkbox change
        $(document).on('change', '.lead-checkbox', function() {
            updateImportButton();
            // Update select all checkbox state
            var total = $('.lead-checkbox').length;
            var checked = $('.lead-checkbox:checked').length;
            $('#select-all-leads').prop('checked', total === checked);
        });

        // Update import button state
        function updateImportButton() {
            var checked = $('.lead-checkbox:checked').length;
            $('#btn-ai-import').prop('disabled', checked === 0);
            if (checked > 0) {
                $('#ai-import-status').text('<?php echo _l('import_leads_selected'); ?>: ' + checked);
            } else {
                $('#ai-import-status').text('');
            }
        }

        // Import selected leads
        $('#btn-ai-import').on('click', function() {
            var $btn = $(this);
            var checkedBoxes = $('.lead-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                alert('<?php echo _l('import_leads_select_at_least_one'); ?>');
                return;
            }

            // Get selected leads
            var selectedLeads = [];
            checkedBoxes.each(function() {
                var index = $(this).closest('tr').data('lead-index');
                if (foundLeads[index]) {
                    selectedLeads.push(foundLeads[index]);
                }
            });

            // Validate import settings
            var status = $('#ai_import_status').val();
            var source = $('#ai_import_source').val();
            var staffid = $('#ai_import_staffid').val();
            var country = $('#ai_import_country').val();
            var gptStatus = $('#ai_import_gpt_status').val();

            if (!status || !source || !staffid || !country) {
                alert('<?php echo _l('import_leads_validation_import_settings'); ?>');
                return;
            }

            // Confirm import
            if (!confirm('<?php echo _l('import_leads_confirm_import'); ?> ' + selectedLeads.length + ' <?php echo _l('import_leads_leads'); ?>?')) {
                return;
            }

            // Disable button and show loading
            $btn.prop('disabled', true);
            $('#ai-import-status').html('<i class="fa fa-spinner fa-spin"></i> <?php echo _l('import_leads_importing'); ?>...');

            // Make import request
            $.ajax({
                url: '<?php echo admin_url('contactcenter/ajax_import_ai_leads'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    leads_json: JSON.stringify(selectedLeads),
                    status: status,
                    source: source,
                    staffid: staffid,
                    country: country,
                    gpt_status: gptStatus
                },
                success: function(response) {
                    if (response.success) {
                        $('#ai-import-status').html('<span class="text-success"><i class="fa fa-check"></i> <?php echo _l('import_leads_imported_success'); ?>: ' + response.imported + ' / ' + response.total + '</span>');
                        
                        // Remove imported leads from table
                        checkedBoxes.closest('tr').fadeOut(function() {
                            $(this).remove();
                            updateImportButton();
                            
                            // Update count
                            var remaining = $('#ai-leads-tbody tr').length;
                            if (remaining === 0) {
                                $('#ai-search-results').hide();
                            } else {
                                $('#ai-results-count').text('<?php echo _l('import_leads_found'); ?>: ' + remaining);
                            }
                        });

                        // Show success message
                        alert('<?php echo _l('import_leads_imported_success'); ?>: ' + response.imported + ' / ' + response.total);
                        
                        // Reload page after 2 seconds to refresh the page
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    } else {
                        $('#ai-import-status').html('<span class="text-danger"><?php echo _l('import_leads_import_error'); ?>: ' + (response.error || 'Unknown error') + '</span>');
                        $btn.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    $('#ai-import-status').html('<span class="text-danger"><?php echo _l('import_leads_import_error'); ?>: ' + error + '</span>');
                    $btn.prop('disabled', false);
                    console.error('Import Error:', xhr.responseText);
                }
            });
        });
    });
</script>
</body>

</html>