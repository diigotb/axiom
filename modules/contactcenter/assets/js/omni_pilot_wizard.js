/**
 * Omni Pilot Wizard JavaScript
 */

// Ensure url_contactcenter is defined
if (typeof url_contactcenter === 'undefined') {
    var url_contactcenter = (typeof site_url !== 'undefined' ? site_url : '') + 'admin/contactcenter/';
}

var omniPilotCurrentStep = 0;
var omniPilotWizardData = {
    step_0: {},
    step_1: {},
    step_2: {},
    step_3: {},
    step_4: {},
    step_5: {}
};
var omniPilotSessionId = null;
var omniPilotProgressInterval = null;

/**
 * Get translation for Omni Pilot (prioritizes window.omniPilotTranslations)
 */
function omniPilotGetTranslation(key, fallback) {
    // Try window.omniPilotTranslations first (most reliable)
    if (typeof window !== 'undefined' && window.omniPilotTranslations && window.omniPilotTranslations[key]) {
        return window.omniPilotTranslations[key];
    }
    // Try omniPilot_l helper function
    if (typeof omniPilot_l === 'function') {
        var translated = omniPilot_l(key);
        if (translated && translated !== key) {
            return translated;
        }
    }
    // Try app.lang
    if (typeof app !== 'undefined' && app.lang && app.lang[key]) {
        return app.lang[key];
    }
    // Try global _l function
    if (typeof _l === 'function') {
        try {
            var translated = _l(key);
            if (translated && translated !== key) {
                return translated;
            }
        } catch(e) {
            // _l might not work in this context
        }
    }
    return fallback || key;
}

/**
 * Get CSRF token data for AJAX requests
 */
function getCsrfData() {
    var csrfData = {};
    
    // First try to get from global variables set in the view
    if (typeof omniPilotCsrfTokenName !== 'undefined' && typeof omniPilotCsrfHash !== 'undefined') {
        csrfData[omniPilotCsrfTokenName] = omniPilotCsrfHash;
    } 
    // Try to get from hidden input field
    else {
        var csrfInput = $('input[name="csrf_token_name"]');
        if (csrfInput.length && csrfInput.val()) {
            csrfData['csrf_token_name'] = csrfInput.val();
        } 
        // Try to get from cookie (CodeIgniter stores CSRF in cookie)
        else {
            // CodeIgniter CSRF cookie name is typically 'csrf_cookie_name'
            var csrfCookieName = 'csrf_cookie_name';
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = cookies[i].trim();
                if (cookie.indexOf(csrfCookieName + '=') === 0) {
                    var tokenValue = cookie.substring((csrfCookieName + '=').length);
                    csrfData['csrf_token_name'] = tokenValue;
                    break;
                }
            }
        }
    }
    
    return csrfData;
}

/**
 * Open Omni Pilot Wizard
 */
function openOmniPilotWizard(deviceId) {
    // Remove pulse animation on first click
    $('.omni-pilot-icon').removeClass('omni-pilot-pulse');
    
    // Load wizard
    $.ajax({
        url: url_contactcenter + 'omni_pilot_wizard',
        type: 'GET',
        data: { device_id: deviceId },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.html) {
                // Remove existing modal if present
                $('#omniPilotWizardModal').remove();
                
                // Append wizard HTML
                $('body').append(response.html);
                
                // Store categories for random selection before initialization
                if (response.categories) {
                    omniPilotWizardData.categories = response.categories;
                }
                
                // Initialize wizard
                initOmniPilotWizard();
                
                // Show modal
                $('#omniPilotWizardModal').modal('show');
                
                // Pre-fill random values for Step 1
                prefillRandomValues();
            } else {
                var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_failed_to_load')) ? _l('omni_pilot_failed_to_load') : 'Failed to load wizard';
                alert(response.message || errorMsg);
            }
        },
        error: function() {
            var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_error_loading')) ? _l('omni_pilot_error_loading') : 'Error loading Omni Pilot wizard';
            alert(errorMsg);
        }
    });
}

/**
 * Initialize Omni Pilot Wizard
 */
function initOmniPilotWizard() {
    omniPilotCurrentStep = 0;
    showStep(0);
    
    // Set default deadline to 30 days from now
    var deadline = new Date();
    deadline.setDate(deadline.getDate() + 30);
    // Format as YYYY-MM-DD for input type="date" (browser will display according to locale)
    var day = String(deadline.getDate()).padStart(2, '0');
    var month = String(deadline.getMonth() + 1).padStart(2, '0');
    var year = deadline.getFullYear();
    $('#deadline_date').val(year + '-' + month + '-' + day);
    
    // Add locale attribute to help browser display correct format
    $('#deadline_date').attr('lang', (typeof app !== 'undefined' && app.locale) ? app.locale : 'pt-BR');
    
    // Initialize selectpickers
    if (typeof $().selectpicker === 'function') {
        $('.selectpicker').selectpicker();
    }
    
    // Store original button texts
    $('#omni_generate_messages_btn').data('original-text', $('#omni_generate_messages_btn').html());
    $('#omni_rewrite_message_btn').data('original-text', $('#omni_rewrite_message_btn').html());
    
    // Step 0: Approach card selection
    $('.omni-approach-card').on('click', function() {
        var approach = $(this).data('approach');
        $('.omni-approach-card').removeClass('selected');
        $(this).addClass('selected');
        $('#approach').val(approach);
    });
    
    // Event handlers
    $('#omni_next_btn').on('click', function() {
        if (validateCurrentStep()) {
            saveCurrentStep();
            if (omniPilotCurrentStep < 6) {
                showStep(omniPilotCurrentStep + 1);
            }
        }
    });
    
    $('#omni_prev_btn').on('click', function() {
        if (omniPilotCurrentStep > 0) {
            saveCurrentStep();
            showStep(omniPilotCurrentStep - 1);
        }
    });
    
    $('#omni_launch_btn').on('click', function() {
        startOmniPilot();
    });
    
    // Step 1: AI Finder
    $('#omni_ai_state').on('change', function() {
        var state = $(this).val();
        if (state) {
            loadCitiesForState(state);
        }
    });
    
    $('#omni_search_leads_btn').on('click', function() {
        searchLeadsAI();
    });
    
    // Step 1: Import method selection
    $('input[name="import_method"]').on('change', function() {
        var method = $(this).val();
        if (method === 'ai') {
            $('#omni-ai-finder').tab('show');
        } else {
            $('#omni-file-upload').tab('show');
        }
    });
    
    // Step 1: File Upload
    $('#omni_file_csv').on('change', function() {
        handleFileUpload(this.files[0]);
    });
    
    // Tab change handler
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var target = $(e.target).attr('href');
        if (target === '#omni-ai-finder') {
            $('input[name="import_method"][value="ai"]').prop('checked', true);
        } else if (target === '#omni-file-upload') {
            $('input[name="import_method"][value="file"]').prop('checked', true);
        }
    });
    
    // Step 3: Generate Messages
    $('#omni_generate_messages_btn').on('click', function() {
        generateAIMessages();
    });
    
    // Step 3: Manual Message Editor
    $('#omni_manual_message_btn').on('click', function() {
        // Show editor even without generating messages
        $('#omni_message_editor').show();
        // Initialize with empty message if not already set
        if (!$('#omni_selected_message_text').val()) {
            $('#omni_selected_message_text').val('');
        }
        // Store empty message in wizard data
        if (!omniPilotWizardData.step_3.selected_message) {
            omniPilotWizardData.step_3.selected_message = {
                text: '',
                index: -1 // -1 indicates manual entry
            };
        }
    });
    
    $('#omni_rewrite_message_btn').on('click', function() {
        rewriteMessage();
    });
    
    // Step 5: Generate Follow-ups
    $('#omni_generate_followups_btn').on('click', function() {
        generateFollowupMessages();
    });
    
    // Step 5: Add Follow-up
    $('#omni_add_followup_btn').on('click', function() {
        addFollowupSlot();
    });
    
    // Step 5: Remove Follow-up (delegate to handle dynamically added elements)
    $(document).on('click', '.omni-remove-followup', function() {
        $(this).closest('.followup-slot').fadeOut(300, function() {
            $(this).remove();
        });
    });
    
    // Step 2: Auto-generate campaign name
    $('#omni_ai_category, #goal_status_id').on('change', function() {
        updateCampaignName();
    });
    
    // Step 2: Device selection change
    $('#omni_device_id').on('change', function() {
        updateCampaignName();
    });
    
    // Template management buttons
    $('#omni_save_template_btn').on('click', function() {
        saveOmniPilotTemplate();
    });
    
    $('#omni_load_template_btn').on('click', function() {
        loadOmniPilotTemplates();
    });
    
    // Modal cleanup
    $('#omniPilotWizardModal').on('hidden.bs.modal', function() {
        if (omniPilotProgressInterval) {
            clearInterval(omniPilotProgressInterval);
            omniPilotProgressInterval = null;
        }
    });
}

/**
 * Show wizard step
 */
function showStep(step) {
    // Hide all steps
    $('.wizard-step').hide();
    
    // Show current step
    $('.wizard-step[data-step="' + step + '"]').show();
    
    // Update step indicators - use CSS classes instead of inline styles
    $('.step-indicator').each(function() {
        var stepNum = parseInt($(this).data('step'));
        var indicator = $(this);
        
        // Remove all state classes
        indicator.removeClass('active completed');
        
        if (stepNum < step) {
            indicator.addClass('completed');
        } else if (stepNum === step) {
            indicator.addClass('active');
        }
    });
    
    // Update buttons
    $('#omni_prev_btn').toggle(step > 0);
    $('#omni_next_btn').toggle(step < 6);
    $('#omni_launch_btn').toggle(step === 6);
    
    omniPilotCurrentStep = step;
    
    // Restore saved values when showing a step
    if (step === 0 && omniPilotWizardData.step_0.approach) {
        // Restore selected approach card
        $('.omni-approach-card').removeClass('selected');
        $('.omni-approach-card[data-approach="' + omniPilotWizardData.step_0.approach + '"]').addClass('selected');
        $('#approach').val(omniPilotWizardData.step_0.approach);
    }
    
    // Refresh selectpicker and update campaign name when showing Step 2
    if (step === 2) {
        // Refresh selectpicker to show the pre-selected device
        if (typeof $().selectpicker === 'function') {
            $('#omni_device_id').selectpicker('refresh');
        }
        // Update campaign name if device is selected
        if ($('#omni_device_id').val()) {
            updateCampaignName();
        }
    }
    
    // Load assistant info when showing Step 4
    if (step === 4) {
        loadAssistantInfo();
    }
    
    // Special handling for Step 6
    if (step === 6) {
        updateLaunchSummary();
    }
}

/**
 * Validate current step
 */
function validateCurrentStep() {
    var isValid = true;
    var errorMsg = '';
    
    switch (omniPilotCurrentStep) {
        case 0:
            if (!$('#goal_target').val() || !$('#goal_status_id').val() || !$('#deadline_date').val() || 
                !$('#product_company').val() || !$('#approach').val() || !$('#omni_language').val()) {
                isValid = false;
                errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_validation_step_0')) ? _l('omni_pilot_validation_step_0') : 'Please fill all goal configuration fields including product/company, approach, and language';
            }
            break;
            
        case 1:
            var importMethod = $('input[name="import_method"]:checked').val() || 'ai';
            if (importMethod === 'ai') {
                // Check if leads were searched and selected
                if (!$('#omni_leads_preview').is(':visible') || omniPilotWizardData.step_1.selected_leads === undefined || omniPilotWizardData.step_1.selected_leads.length === 0) {
                    if (!$('#omni_ai_state').val() || !$('#omni_ai_city').val() || !$('#omni_ai_category').val()) {
                        isValid = false;
                        errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_validation_step_1_ai')) ? _l('omni_pilot_validation_step_1_ai') : 'Please fill all AI finder fields and search for leads';
                    }
                }
            } else if (importMethod === 'file' && !$('#omni_file_csv').val()) {
                isValid = false;
                errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_validation_step_1_file')) ? _l('omni_pilot_validation_step_1_file') : 'Please upload a file';
            }
            break;
            
        case 2:
            if (!$('#omni_device_id').val()) {
                isValid = false;
                errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_validation_step_2')) ? _l('omni_pilot_validation_step_2') : 'Please select a device';
            }
            break;
            
        case 3:
            // Allow manual message entry even without AI generation
            var messageText = $('#omni_selected_message_text').val();
            if (!messageText || !messageText.trim()) {
                isValid = false;
                errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_validation_step_3')) ? _l('omni_pilot_validation_step_3') : 'Please enter or select a message';
            }
            break;
            
        case 5:
            var hasMessages = false;
            $('.followup-message-text').each(function() {
                if ($(this).val().trim()) {
                    hasMessages = true;
                    return false;
                }
            });
            if (!hasMessages) {
                isValid = false;
                errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_validation_step_5')) ? _l('omni_pilot_validation_step_5') : 'Please add at least one follow-up message';
            }
            break;
    }
    
    if (!isValid && errorMsg) {
        alert(errorMsg);
    }
    
    return isValid;
}

/**
 * Save current step data
 */
function saveCurrentStep() {
    var stepData = {};
    
    switch (omniPilotCurrentStep) {
        case 0:
            stepData = {
                goal_target: $('#goal_target').val(),
                goal_status_id: $('#goal_status_id').val(),
                deadline_date: $('#deadline_date').val(),
                product_company: $('#product_company').val(),
                approach: $('#approach').val(),
                language: $('#omni_language').val()
            };
            break;
            
        case 1:
            var importMethod = $('input[name="import_method"]:checked').val() || 'ai';
            stepData = {
                import_method: importMethod
            };
            
            if (importMethod === 'ai') {
                stepData.ai_country = $('#omni_ai_country').val();
                stepData.ai_state = $('#omni_ai_state').val();
                stepData.ai_city = $('#omni_ai_city').val();
                stepData.ai_category = $('#omni_ai_category').val();
                stepData.ai_quantity = $('#omni_ai_quantity').val();
                stepData.enable_gemini_enrichment = $('#omni_enable_enrichment').is(':checked') ? 1 : 0;
                stepData.selected_leads = omniPilotWizardData.step_1.selected_leads || [];
            stepData.ai_import_status = $('#ai_import_status').val();
            stepData.ai_import_source = $('#ai_import_source').val();
            stepData.ai_import_staffid = $('#ai_import_staffid').val();
            stepData.ai_import_country = $('#ai_import_country').val() || '55';
            stepData.ai_import_gpt_status = $('#ai_import_gpt_status').val() || '0';
            } else {
                stepData.file_uploaded = $('#omni_file_csv').val() ? true : false;
                stepData.field_mapping = getFieldMapping();
            }
            break;
            
            case 2:
                stepData = {
                    device_id: $('#omni_device_id').val(),
                    campaign_name: $('#omni_campaign_name').val(),
                    campaign_import_status: $('#campaign_import_status').val(),
                    campaign_final_status: $('#campaign_final_status').val(),
                    start_time: $('input[name="start_time"]').val(),
                    end_time: $('input[name="end_time"]').val()
                };
                break;
            
            case 4:
                stepData = {
                    assistant_ai_id: $('#omni_assistant_ai_id').val() || null
                };
                break;
            
        case 3:
            var mediaFile = $('#omni_message_media')[0].files[0];
            var mediaData = null;
            if (mediaFile) {
                // Store file info - actual upload will happen on execute
                mediaData = {
                    name: mediaFile.name,
                    type: mediaFile.type,
                    size: mediaFile.size
                };
            }
            stepData = {
                selected_message: {
                    text: $('#omni_selected_message_text').val() || omniPilotWizardData.step_3.selected_message_text || '',
                    media: mediaData,
                    media_type: mediaData ? getMediaType(mediaData.type) : null
                }
            };
            break;
            
        case 4:
            stepData = {
                assistant_ai_id: $('#omni_assistant_ai_id').val() || null
            };
            break;
            
        case 5:
            var followupMessages = [];
            $('.followup-slot').each(function() {
                var hours = $(this).data('hours');
                var text = $(this).find('.followup-message-text').val();
                var media = $(this).find('.followup-media').val();
                
                if (text.trim()) {
                    followupMessages.push({
                        hours: hours,
                        text: text,
                        media: media,
                        media_type: getMediaType(media)
                    });
                }
            });
            stepData.followup_messages = followupMessages;
            break;
    }
    
    omniPilotWizardData['step_' + omniPilotCurrentStep] = stepData;
}

/**
 * Pre-fill random values for Step 1
 */
function prefillRandomValues() {
    // Random Brazilian state
    var states = ['SP', 'RJ', 'MG', 'RS', 'PR', 'SC', 'BA', 'GO', 'PE', 'CE'];
    var randomState = states[Math.floor(Math.random() * states.length)];
    $('#omni_ai_state').val(randomState).selectpicker('refresh');
    $('#omni_ai_state').trigger('change');
    
    // Random category - wait for categories to be loaded
    setTimeout(function() {
        var categories = Object.keys(omniPilotWizardData.categories || {});
        if (categories.length > 0) {
            var randomCategory = categories[Math.floor(Math.random() * categories.length)];
            $('#omni_ai_category').val(randomCategory).selectpicker('refresh');
            updateCampaignName(); // Update campaign name when category is selected
        }
    }, 100);
}

/**
 * Load cities for Brazilian state
 */
function loadCitiesForState(state) {
    var loadingText = (typeof _l !== 'undefined' && _l('loading')) ? _l('loading') : 'Loading...';
    $('#omni_ai_city').prop('disabled', true).html('<option value="">' + loadingText + '...</option>').selectpicker('refresh');
    
    // Prepare data object properly
    var postData = { state: state };
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = csrfData[key];
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_get_brazilian_cities',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.cities) {
                var selectCityText = (typeof _l !== 'undefined' && _l('dropdown_non_selected_tex')) ? _l('dropdown_non_selected_tex') : 'Select City';
                var options = '<option value="">' + selectCityText + '</option>';
                $.each(response.cities, function(i, city) {
                    options += '<option value="' + city.value + '">' + city.label + '</option>';
                });
                $('#omni_ai_city').html(options).prop('disabled', false).selectpicker('refresh');
                
                // Select random city
                if (response.cities.length > 0) {
                    var randomCity = response.cities[Math.floor(Math.random() * Math.min(5, response.cities.length))].value;
                    $('#omni_ai_city').val(randomCity).selectpicker('refresh');
                }
            }
        },
        error: function() {
            $('#omni_ai_city').html('<option value="">Error loading cities</option>').prop('disabled', true).selectpicker('refresh');
        }
    });
}

/**
 * Search leads using AI
 */
function searchLeadsAI() {
    var $btn = $('#omni_search_leads_btn');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Searching...');
    
    // Prepare data object properly
    var postData = {
        country: $('#omni_ai_country').val(),
        state: $('#omni_ai_state').val(),
        state_code: $('#omni_ai_state').val(),
        city: $('#omni_ai_city').val(),
        category: $('#omni_ai_category').val(),
        quantity: $('#omni_ai_quantity').val() || 100,
        batch_size: 100,
        enable_gemini_enrichment: $('#omni_enable_enrichment').is(':checked')
    };
    
    // Add CSRF token data
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = csrfData[key];
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_search_leads_ai',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            $btn.prop('disabled', false).html('<i class="fa fa-search"></i> Search');
            
            if (response.success && response.leads) {
                displayLeadsPreview(response.leads);
                omniPilotWizardData.step_1.selected_leads = response.leads;
            } else {
                var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_failed_to_search_leads')) ? _l('omni_pilot_failed_to_search_leads') : 'Failed to search leads';
                alert(response.error || errorMsg);
            }
        },
        error: function() {
            $btn.prop('disabled', false).html('<i class="fa fa-search"></i> Search');
            var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_error_searching_leads')) ? _l('omni_pilot_error_searching_leads') : 'Error searching leads';
            alert(errorMsg);
        }
    });
}

/**
 * Display leads preview
 */
function displayLeadsPreview(leads) {
    var tbody = $('#omni_leads_tbody');
    tbody.empty();
    
    $.each(leads, function(i, lead) {
        var row = '<tr>' +
            '<td><input type="checkbox" class="lead-checkbox" checked data-index="' + i + '"></td>' +
            '<td>' + (lead.company || '-') + '</td>' +
            '<td>' + (lead.name || '-') + '</td>' +
            '<td>' + (lead.phone || '-') + '</td>' +
            '</tr>';
        tbody.append(row);
    });
    
    $('#omni_leads_count').text('Found: ' + leads.length + ' leads');
    $('#omni_leads_preview').show();
    
    // Select all checkbox
    $('#omni_select_all_leads').on('change', function() {
        $('.lead-checkbox').prop('checked', $(this).prop('checked'));
    });
}

/**
 * Handle file upload
 */
function handleFileUpload(file) {
    if (!file) return;
    
    $('#omni_file_name').text(file.name);
    $('#omni_file_info').show();
    
    // TODO: Parse file and show field mapping interface
    // For now, just mark as uploaded
    $('input[name="import_method"]').filter('[value="file"]').prop('checked', true);
}

/**
 * Get field mapping
 */
function getFieldMapping() {
    // TODO: Implement field mapping logic
    return {};
}

/**
 * Load assistant info for Step 4
 */
function loadAssistantInfo() {
    var deviceId = $('#omni_device_id').val() || omniPilotWizardData.step_2.device_id;
    
    if (!deviceId) {
        var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_select_device_first')) ? _l('omni_pilot_select_device_first') : 'Please select a device in Step 2 first.';
        $('#omni_assistant_info').html(
            '<div class="alert alert-warning">' +
            '<i class="fa fa-exclamation-triangle"></i> ' + errorMsg +
            '</div>'
        );
        $('#omni_assistant_selector').hide();
        return;
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_get_assistant',
        type: 'POST',
        data: (function() {
            var postData = { device_id: deviceId };
            var csrfData = getCsrfData();
            for (var key in csrfData) {
                if (csrfData.hasOwnProperty(key)) {
                    postData[key] = csrfData[key];
                }
            }
            return postData;
        })(),
        dataType: 'json',
        success: function(response) {
            // Hide loading, show selector
            $('#omni_assistant_info').hide();
            $('#omni_assistant_selector').show();
            
            // Populate assistants dropdown
            var $select = $('#omni_assistant_ai_id');
            $select.empty();
            $select.append('<option value="">' + (typeof _l !== 'undefined' ? _l('dropdown_non_selected_tex') : 'Select...') + '</option>');
            
            if (response.assistants && response.assistants.length > 0) {
                $.each(response.assistants, function(i, assistant) {
                    $select.append('<option value="' + assistant.id + '">' + escapeHtml(assistant.name) + '</option>');
                });
            }
            
            // Set current assistant if exists
            if (response.current_assistant_id) {
                $select.val(response.current_assistant_id);
            }
            
            // Refresh selectpicker
            if (typeof $().selectpicker === 'function') {
                $select.selectpicker('refresh');
            }
            
            // Show current assistant info
            updateAssistantInfo(response);
            
            // Store response for later use
            $select.data('assistants-response', response);
            
            // Handle assistant change
            $select.off('change.assistant').on('change.assistant', function() {
                var selectedId = $(this).val();
                var storedResponse = $(this).data('assistants-response');
                var assistant = storedResponse.assistants.find(function(a) { return a.id == selectedId; });
                if (assistant) {
                    updateAssistantInfoDisplay(assistant);
                } else {
                    updateAssistantInfoDisplay(null, storedResponse.message);
                }
                // Save to device
                saveAssistantToDevice(deviceId, selectedId);
            });
        },
        error: function() {
            var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_error_loading_assistant')) ? _l('omni_pilot_error_loading_assistant') : 'Error loading assistant information.';
            $('#omni_assistant_info').html(
                '<div class="alert alert-danger">' +
                '<i class="fa fa-exclamation-triangle"></i> ' + errorMsg +
                '</div>'
            );
            $('#omni_assistant_selector').hide();
        }
    });
}

/**
 * Update assistant info display
 */
function updateAssistantInfo(response) {
    var assistantId = $('#omni_assistant_ai_id').val() || response.current_assistant_id;
    var assistant = null;
    
    if (assistantId && response.assistants) {
        assistant = response.assistants.find(function(a) { return a.id == assistantId; });
    }
    
    updateAssistantInfoDisplay(assistant, response.message);
}

/**
 * Update assistant info display (helper)
 */
function updateAssistantInfoDisplay(assistant, message) {
    var html = '';
    
    // Get language texts
    var sampleInteractionText = (typeof _l !== 'undefined' && _l('omni_pilot_sample_interaction')) ? _l('omni_pilot_sample_interaction') : 'Sample Interaction';
    var sampleInteractionDesc = (typeof _l !== 'undefined' && _l('omni_pilot_sample_interaction_text')) ? _l('omni_pilot_sample_interaction_text') : 'When a lead responds, the assistant will automatically reply based on the configured settings and knowledge base.';
    var noAssistantMsg = (typeof _l !== 'undefined' && _l('omni_pilot_no_assistant')) ? _l('omni_pilot_no_assistant') : (message || 'No assistant is configured for this device.');
    
    if (assistant) {
        html = '<div class="alert alert-info" style="background: rgba(0, 224, 155, 0.15); border-color: var(--primary, #00e09b); margin-top: 15px;">' +
            '<h6><i class="fa fa-user-circle"></i> ' + escapeHtml(assistant.name) + '</h6>';
        if (assistant.desc) {
            html += '<p style="margin-top: 10px; color: rgba(255,255,255,0.8);">' + escapeHtml(assistant.desc) + '</p>';
        }
        html += '<div class="sample-interaction" style="padding: 15px; border-radius: 5px; margin-top: 10px; background: rgba(255,255,255,0.05); backdrop-filter: blur(10px);">' +
            '<strong>' + escapeHtml(sampleInteractionText) + ':</strong>' +
            '<p style="margin-top: 10px; font-style: italic; color: rgba(255,255,255,0.8);">' +
            sampleInteractionDesc.replace('{assistant}', escapeHtml(assistant.name)) +
            '</p>' +
            '</div>' +
            '</div>';
    } else {
        html = '<div class="alert alert-warning" style="background: rgba(255, 193, 7, 0.15); border-color: rgba(255, 193, 7, 0.5); margin-top: 15px;">' +
            '<i class="fa fa-exclamation-triangle"></i> ' + escapeHtml(noAssistantMsg) +
            '</div>';
    }
    
    $('#omni_current_assistant_info').html(html);
}

/**
 * Save assistant to device
 */
function saveAssistantToDevice(deviceId, assistantId) {
    // Build data object properly to avoid serialization issues
    var postData = {
        device_id: String(deviceId),
        assistant_ai_id: String(assistantId || '')
    };
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = String(csrfData[key]);
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_update_assistant',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Reload assistant info to show updated selection
                loadAssistantInfo();
            } else {
                var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_error_updating_assistant')) ? _l('omni_pilot_error_updating_assistant') : 'Error updating assistant';
                alert(response.message || errorMsg);
            }
        },
        error: function() {
            var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_error_updating_assistant')) ? _l('omni_pilot_error_updating_assistant') : 'Error updating assistant';
            alert(errorMsg);
        }
    });
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
}

/**
 * Generate AI messages
 */
function generateAIMessages() {
    var $btn = $('#omni_generate_messages_btn');
    if (!$btn.data('original-text')) {
        $btn.data('original-text', $btn.html());
    }
    var generatingText = (typeof _l !== 'undefined' && _l('omni_pilot_generating')) ? _l('omni_pilot_generating') : 'Generating...';
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + generatingText);
    
    var category = $('#omni_ai_category').val() || 'business';
    var productCompany = $('#product_company').val() || '';
    var approach = $('#approach').val() || '';
    var language = $('#omni_language').val() || 'pt-BR';
    var state = $('#omni_ai_state').val() || '';
    var city = $('#omni_ai_city').val() || '';
    
    // Build context from Step 0 and Step 1 data
    var context = '';
    if (productCompany) {
        context += 'Product/Company: ' + productCompany + '. ';
    }
    if (approach) {
        // Translate approach code to descriptive text
        var approachNames = {
            'direct_sales': 'Direct Sales approach - focus on presenting products/services and closing sales quickly',
            'educational': 'Educational/Informative approach - share knowledge and educate about problems and solutions',
            'relationship': 'Relationship Building approach - create connection and trust before commercial offers',
            'promotional': 'Promotional/Offer approach - highlight promotions, discounts and special offers',
            'consultation': 'Consultation/Assessment approach - offer personalized analysis and consultation',
            'followup': 'Re-engagement/Follow-up approach - re-engage leads who did not respond previously'
        };
        context += 'Approach: ' + (approachNames[approach] || approach) + '. ';
    }
    if (state || city) {
        context += 'Location: ' + (city ? city + ', ' : '') + (state || '') + '. ';
    }
    context += 'Language: ' + language + '. ';
    context += 'Category: ' + category + '.';
    
    // Prepare data object properly
    var postData = {
        category: category,
        context: context,
        language: language
    };
    
    // Add CSRF token data
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = csrfData[key];
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_generate_messages',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            var originalText = $btn.data('original-text') || '<i class="fa fa-magic"></i> Generate Messages';
            $btn.prop('disabled', false).html(originalText);
            
            if (response.success && response.messages) {
                displayMessageVariations(response.messages);
            } else {
                var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_failed_to_generate_messages')) ? _l('omni_pilot_failed_to_generate_messages') : 'Failed to generate messages';
                alert(response.message || errorMsg);
            }
        },
        error: function() {
            var originalText = $btn.data('original-text') || '<i class="fa fa-magic"></i> Generate Messages';
            $btn.prop('disabled', false).html(originalText);
            var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_error_generating_messages')) ? _l('omni_pilot_error_generating_messages') : 'Error generating messages';
            alert(errorMsg);
        }
    });
}

/**
 * Display message variations
 */
function displayMessageVariations(messages) {
    var container = $('#omni_message_cards');
    container.empty();
    
    // Store messages in wizard data
    omniPilotWizardData.step_3.generated_messages = messages;
    
    // Get language text for "Option"
    var optionText = (typeof _l !== 'undefined' && _l('omni_pilot_option')) ? _l('omni_pilot_option') : 'Option';
    
    $.each(messages, function(i, msg) {
        var card = '<div class="message-card" data-index="' + i + '">' +
            '<div style="font-size: 13px; margin-bottom: 8px; opacity: 0.8;">' + optionText + ' ' + (i + 1) + '</div>' +
            '<div style="font-size: 14px;">' + escapeHtml(msg.text) + '</div>' +
            '</div>';
        container.append(card);
    });
    
    // Add click handlers
    $('.message-card').on('click', function() {
        var index = $(this).data('index');
        var msg = messages[index];
        selectMessage(index, msg.text);
    });
    
    $('#omni_message_variations').show();
}

/**
 * Select message
 */
function selectMessage(index, text) {
    // Store selected message index
    omniPilotWizardData.step_3.selected_message_index = index;
    omniPilotWizardData.step_3.selected_message_text = text;
    
    // Update editor
    $('#omni_selected_message_text').val(text);
    $('#omni_message_editor').show();
    
    // Update card styles
    $('.message-card').removeClass('selected');
    $('.message-card').eq(index).addClass('selected');
    
    // Scroll to editor
    $('html, body').animate({
        scrollTop: $('#omni_message_editor').offset().top - 100
    }, 300);
}

/**
 * Rewrite message with AI
 */
function rewriteMessage() {
    var currentText = $('#omni_selected_message_text').val();
    if (!currentText) {
        var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_please_enter_message')) ? _l('omni_pilot_please_enter_message') : 'Please enter a message first';
        alert(errorMsg);
        return;
    }
    
    var $btn = $('#omni_rewrite_message_btn');
    if (!$btn.data('original-text')) {
        $btn.data('original-text', $btn.html());
    }
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Rewriting...');
    
    var category = $('#omni_ai_category').val() || 'business';
    var productCompany = $('#product_company').val() || '';
    var approach = $('#approach').val() || '';
    var language = $('#omni_language').val() || 'pt-BR';
    var state = $('#omni_ai_state').val() || '';
    var city = $('#omni_ai_city').val() || '';
    
    var context = 'Rewrite this message: ' + currentText + '. ';
    if (productCompany) context += 'Product/Company: ' + productCompany + '. ';
    if (approach) {
        // Translate approach code to descriptive text
        var approachNames = {
            'direct_sales': 'Direct Sales approach - focus on presenting products/services and closing sales quickly',
            'educational': 'Educational/Informative approach - share knowledge and educate about problems and solutions',
            'relationship': 'Relationship Building approach - create connection and trust before commercial offers',
            'promotional': 'Promotional/Offer approach - highlight promotions, discounts and special offers',
            'consultation': 'Consultation/Assessment approach - offer personalized analysis and consultation',
            'followup': 'Re-engagement/Follow-up approach - re-engage leads who did not respond previously'
        };
        context += 'Approach: ' + (approachNames[approach] || approach) + '. ';
    }
    if (state || city) context += 'Location: ' + (city ? city + ', ' : '') + (state || '') + '. ';
    context += 'Category: ' + category + '.';
    
    // Prepare data object properly
    var postData = {
        category: category,
        context: context,
        language: language
    };
    
    // Add CSRF token data
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = csrfData[key];
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_generate_messages',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            $btn.prop('disabled', false).html('<i class="fa fa-magic"></i> AI Rewrite');
            
            if (response.success && response.messages && response.messages.length > 0) {
                $('#omni_selected_message_text').val(response.messages[0].text);
            } else {
                var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_failed_to_rewrite')) ? _l('omni_pilot_failed_to_rewrite') : 'Failed to rewrite message';
                alert(errorMsg);
            }
        },
        error: function() {
            $btn.prop('disabled', false).html('<i class="fa fa-magic"></i> AI Rewrite');
            var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_error_rewriting')) ? _l('omni_pilot_error_rewriting') : 'Error rewriting message';
            alert(errorMsg);
        }
    });
}

/**
 * Generate follow-up messages
 */
function generateFollowupMessages() {
    var $btn = $('#omni_generate_followups_btn');
    var originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + ((typeof _l !== 'undefined' && _l('omni_pilot_generating')) ? _l('omni_pilot_generating') : 'Generating...'));
    
    var initialMessage = $('#omni_selected_message_text').val() || 'Hello';
    var category = $('#omni_ai_category').val() || 'business';
    
    // Prepare data object properly
    var postData = {
        initial_message: initialMessage,
        category: category
    };
    
    // Add CSRF token data
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = csrfData[key];
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_generate_followups',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            $btn.prop('disabled', false).html(originalText);
            
            if (response.success && response.messages) {
                $.each(response.messages, function(i, msg) {
                    $('.followup-slot[data-hours="' + msg.hours + '"]').find('.followup-message-text').val(msg.text);
                });
            } else {
                alert(response.message || ((typeof _l !== 'undefined' && _l('omni_pilot_followup_generation_failed')) ? _l('omni_pilot_followup_generation_failed') : 'Failed to generate follow-up messages'));
            }
        },
        error: function() {
            $btn.prop('disabled', false).html(originalText);
            alert((typeof _l !== 'undefined' && _l('omni_pilot_error_generating_followups')) ? _l('omni_pilot_error_generating_followups') : 'Error generating follow-up messages');
        }
    });
}

/**
 * Add a new follow-up slot dynamically
 */
function addFollowupSlot() {
    // Get language texts
    var hoursText = (typeof _l !== 'undefined' && _l('omni_pilot_followup_hours')) ? _l('omni_pilot_followup_hours') : 'Hours';
    var messageText = (typeof _l !== 'undefined' && _l('omni_pilot_message')) ? _l('omni_pilot_message') : 'Message';
    var placeholderText = (typeof _l !== 'undefined' && _l('omni_pilot_followup_message_placeholder')) ? _l('omni_pilot_followup_message_placeholder') : 'Enter follow-up message...';
    
    // Prompt for hours
    var hours = prompt((typeof _l !== 'undefined' && _l('omni_pilot_enter_followup_hours')) ? _l('omni_pilot_enter_followup_hours') : 'Enter number of hours for this follow-up:', '24');
    if (!hours || isNaN(hours) || hours <= 0) {
        return;
    }
    
    hours = parseInt(hours);
    var slotCount = $('.followup-slot').length + 1;
    
    // Format hours label
    var hoursLabel = '';
    if (hours < 24) {
        hoursLabel = '+' + hours + ' ' + ((typeof _l !== 'undefined' && _l('omni_pilot_hours')) ? _l('omni_pilot_hours') : 'Hours');
    } else if (hours === 24) {
        hoursLabel = (typeof _l !== 'undefined' && _l('omni_pilot_followup_24h')) ? _l('omni_pilot_followup_24h') : '+24 Hours';
    } else if (hours < 168) {
        var days = Math.floor(hours / 24);
        hoursLabel = '+' + days + ' ' + ((typeof _l !== 'undefined' && _l('omni_pilot_days')) ? _l('omni_pilot_days') : 'Days');
    } else {
        var weeks = Math.floor(hours / 168);
        hoursLabel = '+' + weeks + ' ' + ((typeof _l !== 'undefined' && _l('omni_pilot_weeks')) ? _l('omni_pilot_weeks') : 'Weeks');
    }
    
    var slotHtml = '<div class="followup-slot" data-hours="' + hours + '" style="border-radius: 5px; padding: 15px; margin-bottom: 15px; position: relative; background: rgba(255,255,255,0.08); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.15);">' +
        '<button type="button" class="btn btn-sm btn-danger omni-remove-followup" style="position: absolute; top: 10px; right: 10px; padding: 2px 8px;">' +
        '<i class="fa fa-times"></i>' +
        '</button>' +
        '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-right: 40px;">' +
        '<strong style="color: var(--primary-text, #FFFFFF);">' + hoursLabel + '</strong>' +
        '<span class="badge badge-info">' + messageText + ' ' + slotCount + '</span>' +
        '</div>' +
        '<div class="form-group" style="margin-bottom: 10px;">' +
        '<textarea class="form-control followup-message-text" rows="2" placeholder="' + placeholderText + '"></textarea>' +
        '</div>' +
        '<div class="form-group" style="margin-bottom: 0;">' +
        '<input type="file" class="form-control followup-media" accept="image/*,video/*,audio/*" style="font-size: 12px;">' +
        '</div>' +
        '</div>';
    
    $('#omni_followup_timeline').append(slotHtml);
}

/**
 * Update campaign name
 */
function updateCampaignName() {
    var categoryKey = $('#omni_ai_category').val();
    var categoryLabel = categoryKey;
    
    // Get category label from stored categories
    if (categoryKey && omniPilotWizardData.categories && omniPilotWizardData.categories[categoryKey]) {
        categoryLabel = omniPilotWizardData.categories[categoryKey];
    } else if (categoryKey) {
        // Fallback: get from select option text
        categoryLabel = $('#omni_ai_category option:selected').text() || categoryKey;
    }
    
    var statusId = $('#goal_status_id').val();
    var goalText = (typeof _l !== 'undefined' && _l('omni_pilot_goal')) ? _l('omni_pilot_goal') : 'Goal';
    
    // Get user name (from global variable set in view, or fallback)
    var userName = (typeof omniPilotCurrentUserName !== 'undefined' && omniPilotCurrentUserName) ? omniPilotCurrentUserName : '';
    
    if (statusId) {
        // Get status name
        var statusName = $('#goal_status_id option:selected').text();
        if (userName) {
            $('#omni_campaign_name').val('Omni-' + userName + '-' + categoryLabel + '-' + statusName);
        } else {
            $('#omni_campaign_name').val('Omni-' + categoryLabel + '-' + statusName);
        }
    } else {
        if (userName) {
            $('#omni_campaign_name').val('Omni-' + userName + '-' + categoryLabel + '-' + goalText);
        } else {
            $('#omni_campaign_name').val('Omni-' + categoryLabel + '-' + goalText);
        }
    }
}

/**
 * Update launch summary
 */
function updateLaunchSummary() {
    // Get language texts
    var goalText = (typeof _l !== 'undefined' && _l('omni_pilot_goal')) ? _l('omni_pilot_goal') : 'Goal';
    var leadsText = (typeof _l !== 'undefined' && _l('omni_pilot_leads')) ? _l('omni_pilot_leads') : 'Leads';
    var campaignText = (typeof _l !== 'undefined' && _l('omni_pilot_campaign')) ? _l('omni_pilot_campaign') : 'Campaign';
    var messageText = (typeof _l !== 'undefined' && _l('omni_pilot_message')) ? _l('omni_pilot_message') : 'Message';
    var followupText = (typeof _l !== 'undefined' && _l('omni_pilot_followup')) ? _l('omni_pilot_followup') : 'Follow-up';
    var readyToImportText = (typeof _l !== 'undefined' && _l('omni_pilot_ready_to_import')) ? _l('omni_pilot_ready_to_import') : 'Ready to import';
    var configuredText = (typeof _l !== 'undefined' && _l('omni_pilot_configured')) ? _l('omni_pilot_configured') : 'Configured';
    var messagesText = (typeof _l !== 'undefined' && _l('omni_pilot_messages')) ? _l('omni_pilot_messages') : 'messages';
    
    var goalTarget = $('#goal_target').val() || 0;
    var statusName = $('#goal_status_id option:selected').text() || goalText;
    var followupCount = $('.followup-message-text').filter(function() { return $(this).val().trim(); }).length;
    
    $('#summary_goal').text(goalText + ': ' + goalTarget + ' ' + statusName);
    $('#summary_leads').text(leadsText + ': ' + readyToImportText);
    $('#summary_campaign').text(campaignText + ': ' + $('#omni_campaign_name').val());
    $('#summary_message').text(messageText + ': ' + configuredText);
    $('#summary_followup').text(followupText + ': ' + followupCount + ' ' + messagesText);
}

/**
 * Start Omni Pilot
 */
function startOmniPilot() {
    var confirmText = (typeof _l !== 'undefined' && _l('omni_pilot_start_confirm')) ? _l('omni_pilot_start_confirm') : 'Start Omni Pilot? This will begin importing leads and setting up your campaign.';
    if (!confirm(confirmText)) {
        return;
    }
    
    // Save all step data
    saveCurrentStep();
    
    // Prepare wizard data
    var wizardData = omniPilotWizardData;
    
    // Add import method selection
    if (!wizardData.step_1.import_method) {
        wizardData.step_1.import_method = 'ai';
    }
    
    var $btn = $('#omni_launch_btn');
    var startingText = (typeof _l !== 'undefined' && _l('omni_pilot_starting')) ? _l('omni_pilot_starting') : 'Starting...';
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + startingText);
    
    // Show a temporary loading message in the progress container (if it exists)
    var $progressContainer = $('#omniPilotProgressContainer');
    if ($progressContainer.length > 0) {
        $progressContainer.css('display', 'flex').show();
        $('#omniPilotProgressText').html('<span>' + startingText + '...</span>');
    }
    
    // Prepare FormData for file upload
    var formData = new FormData();
    formData.append('wizard_data', JSON.stringify(wizardData));
    
    // Add CSRF token
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            formData.append(key, csrfData[key]);
        }
    }
    
    // Add media file if exists
    var mediaFile = $('#omni_message_media')[0].files[0];
    if (mediaFile) {
        formData.append('message_media', mediaFile);
    }
    
    // Show loading indicator immediately
    console.log('Starting Omni Pilot...');
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_execute',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        timeout: 60000, // 60 second timeout
        success: function(response) {
            console.log('Omni Pilot start response:', response);
            if (response.success && response.session_id) {
                omniPilotSessionId = response.session_id;
                
                // Store session ID in localStorage for persistence across page reloads
                if (typeof Storage !== 'undefined') {
                    localStorage.setItem('omniPilotSessionId', response.session_id);
                    localStorage.setItem('omniPilotDeviceId', $('#omni_device_id').val() || '');
                }
                
                // Remove pulse animation since Omni Pilot is starting
                $('.omni-pilot-icon').removeClass('omni-pilot-pulse');
                
                // Show progress badge BEFORE closing wizard (so user sees it immediately)
                console.log('Showing progress badge for session:', response.session_id);
                showProgressBadge(response.session_id);
                
                // Ensure container is visible
                $('#omniPilotProgressContainer').css('display', 'flex').show();
                
                // Close wizard after a short delay to ensure badge is visible
                setTimeout(function() {
                    $('#omniPilotWizardModal').modal('hide');
                }, 500);
                
                // Start executing steps sequentially
                console.log('Starting step execution...');
                executeOmniPilotSteps(response.session_id);
                
                // Start polling progress immediately
                console.log('Starting progress polling...');
                startProgressPolling(response.session_id);
            } else {
                var errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_failed_to_start')) ? _l('omni_pilot_failed_to_start') : 'Failed to start Omni Pilot';
                var startText = (typeof _l !== 'undefined' && _l('omni_pilot_start')) ? _l('omni_pilot_start') : 'START OMNI PILOT';
                console.error('Failed to start Omni Pilot:', response);
                alert(response.message || errorMsg);
                $btn.prop('disabled', false).html('<i class="fa fa-rocket"></i> ' + startText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error starting Omni Pilot:', xhr, status, error);
            var errorMsg = '';
            var startText = (typeof _l !== 'undefined' && _l('omni_pilot_start')) ? _l('omni_pilot_start') : 'START OMNI PILOT';
            
            if (xhr.status === 419) {
                // CSRF token expired
                errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_page_expired')) ? _l('omni_pilot_page_expired') : 'Page expired. Please refresh the page and try again.';
                alert(errorMsg);
                // Optionally refresh the page after a delay
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            } else {
                errorMsg = (typeof _l !== 'undefined' && _l('omni_pilot_error_starting')) ? _l('omni_pilot_error_starting') : 'Error starting Omni Pilot';
                alert(errorMsg);
            }
            $btn.prop('disabled', false).html('<i class="fa fa-rocket"></i> ' + startText);
        }
    });
}

/**
 * Execute Omni Pilot steps sequentially
 */
function executeOmniPilotSteps(sessionId) {
    console.log('Starting Omni Pilot execution for session:', sessionId);
    
    var steps = ['import', 'campaign', 'message', 'followup', 'activate'];
    var currentStepIndex = 0;
    
    function executeNextStep() {
        if (currentStepIndex >= steps.length) {
            // All steps completed
            console.log('All Omni Pilot steps completed');
            return;
        }
        
        var step = steps[currentStepIndex];
        var stepName = step.charAt(0).toUpperCase() + step.slice(1);
        console.log('Executing step:', step, '(' + (currentStepIndex + 1) + '/' + steps.length + ')');
        
        // Build data object properly to avoid serialization issues
        var postData = {
            session_id: String(sessionId),
            step: String(step)
        };
        var csrfData = getCsrfData();
        for (var key in csrfData) {
            if (csrfData.hasOwnProperty(key)) {
                postData[key] = String(csrfData[key]);
            }
        }
        
        $.ajax({
            url: url_contactcenter + 'ajax_omni_pilot_execute_step',
            type: 'POST',
            data: postData,
            dataType: 'json',
            timeout: 300000, // 5 minutes timeout for long operations
            success: function(response) {
                console.log('Step', step, 'completed:', response);
                
                // Validate response
                if (!response) {
                    console.error('Step', step, 'returned empty response');
                    alert('Empty response from server for step: ' + step);
                    if (omniPilotProgressInterval) {
                        clearInterval(omniPilotProgressInterval);
                        omniPilotProgressInterval = null;
                    }
                    return;
                }
                
                // Check for success - be more lenient with truthy values
                var isSuccess = response.success === true || response.success === 'true' || response.success === 1 || 
                               (response.success !== false && response.success !== 'false' && response.success !== 0 && 
                                response.result && (response.result.success !== false));
                
                if (isSuccess) {
                    // Step completed successfully, move to next
                    console.log('Step', step, 'succeeded, moving to next step. Response:', JSON.stringify(response));
                    currentStepIndex++;
                    // Wait a bit before next step to show progress
                    setTimeout(function() {
                        if (currentStepIndex < steps.length) {
                            console.log('Executing next step, index:', currentStepIndex, 'step:', steps[currentStepIndex]);
                            executeNextStep();
                        } else {
                            console.log('All steps completed!');
                        }
                    }, 1000); // Increased delay to allow progress updates to be visible
                } else {
                    // Step failed
                    var errorMsg = response.message || 'Failed to execute step: ' + step;
                    console.error('Step failed:', step, errorMsg, 'Full response:', JSON.stringify(response));
                    
                    // Check if result has success flag
                    if (response.result && response.result.success === true) {
                        // Result says success, but response.success is false - treat as success
                        console.warn('Response.success is false but result.success is true - treating as success');
                        currentStepIndex++;
                        setTimeout(function() {
                            if (currentStepIndex < steps.length) {
                                console.log('Executing next step after result check, index:', currentStepIndex, 'step:', steps[currentStepIndex]);
                                executeNextStep();
                            }
                        }, 1000);
                    } else {
                        alert(errorMsg);
                        // Stop execution
                        if (omniPilotProgressInterval) {
                            clearInterval(omniPilotProgressInterval);
                            omniPilotProgressInterval = null;
                        }
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error executing step:', step, xhr, status, error);
                var errorMsg = 'Error executing step: ' + step;
                if (xhr.status === 419) {
                    errorMsg = omniPilotGetTranslation('omni_pilot_page_expired', 'Page expired. Please refresh the page and try again.');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                } else if (xhr.status === 0 || status === 'timeout') {
                    errorMsg = 'Request timeout. The operation may still be running in the background.';
                }
                alert(errorMsg);
                // Stop execution
                if (omniPilotProgressInterval) {
                    clearInterval(omniPilotProgressInterval);
                    omniPilotProgressInterval = null;
                }
            }
        });
    }
    
    // Start executing steps after a short delay to ensure UI is ready
    setTimeout(function() {
        executeNextStep();
    }, 500);
}

/**
 * Show progress badge in footer-bar
 */
function showProgressBadge(sessionId) {
    // Ensure container exists and is visible
    var $container = $('#omniPilotProgressContainer');
    if ($container.length === 0) {
        console.error('Omni Pilot progress container not found!');
        return;
    }
    
    // Show in footer-bar instead of floating badge
    $container.css('display', 'flex').fadeIn();
    $('#omniPilotStopBtn').show(); // Show stop button when progress badge is shown
    
    // Get goal target from wizard data or set default
    var goalTarget = omniPilotWizardData.step_0 ? (omniPilotWizardData.step_0.goal_target || 0) : 0;
    var goalProgress = 0;
    
    var startingText = omniPilotGetTranslation('omni_pilot_starting', 'Starting...');
    // Show spinner immediately
    $('#omniPilotSpinner').show();
    updateProgressBadge({ 
        progress_percentage: 0, 
        current_phase: startingText, 
        current_step: 1,
        step_detail: null,
        goal_progress: goalProgress, 
        goal_target: goalTarget, 
        status: 'pending' 
    });
    
    console.log('Omni Pilot progress badge shown for session:', sessionId);
}

/**
 * Start progress polling
 */
function startProgressPolling(sessionId) {
    console.log('Starting progress polling for session:', sessionId);
    
    if (omniPilotProgressInterval) {
        clearInterval(omniPilotProgressInterval);
    }
    
    // Initial poll immediately
    pollProgress(sessionId);
    
    // Then poll every 2 seconds
    omniPilotProgressInterval = setInterval(function() {
        pollProgress(sessionId);
    }, 2000); // Poll every 2 seconds
}

/**
 * Poll progress
 */
function pollProgress(sessionId) {
    if (!sessionId) {
        console.warn('No session ID provided for polling');
        return;
    }
    
    // Ensure container is visible before polling
    var $container = $('#omniPilotProgressContainer');
    if ($container.length > 0) {
        $container.css('display', 'flex').show();
    }
    
    // Prepare data object properly
    var postData = {
        session_id: sessionId
    };
    
    // Add CSRF token data
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = csrfData[key];
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_progress',
        type: 'POST',
        data: postData,
        dataType: 'json',
        timeout: 10000, // 10 second timeout
        success: function(response) {
            if (response.success && response.progress) {
                // Ensure container is visible
                var $container = $('#omniPilotProgressContainer');
                if ($container.length > 0) {
                    $container.css('display', 'flex').show();
                }
                
                updateProgressBadge(response.progress);
                
                // Stop polling only if completed, failed, or cancelled (keep polling if active/pending/importing/etc)
                if (response.progress.status === 'completed' || response.progress.status === 'failed' || response.progress.status === 'cancelled') {
                    if (omniPilotProgressInterval) {
                        clearInterval(omniPilotProgressInterval);
                        omniPilotProgressInterval = null;
                    }
                    // Clear localStorage when completed/failed/cancelled
                    if (typeof Storage !== 'undefined') {
                        localStorage.removeItem('omniPilotSessionId');
                        localStorage.removeItem('omniPilotDeviceId');
                    }
                    // Hide stop button
                    $('#omniPilotStopBtn').hide();
                    // Re-add pulse animation since there's no active Omni Pilot now
                    checkAndUpdateOmniPilotPulse();
                }
            } else {
                // Session not found, stop polling and clear localStorage
                console.warn('Session not found or no progress data:', response);
                if (omniPilotProgressInterval) {
                    clearInterval(omniPilotProgressInterval);
                    omniPilotProgressInterval = null;
                }
                if (typeof Storage !== 'undefined') {
                    localStorage.removeItem('omniPilotSessionId');
                    localStorage.removeItem('omniPilotDeviceId');
                }
                // Re-add pulse animation since there's no active Omni Pilot now
                checkAndUpdateOmniPilotPulse();
                // Don't hide container immediately - might be a temporary issue
                // $('#omniPilotProgressContainer').fadeOut();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error polling progress:', xhr, status, error);
            // Don't stop polling on error - might be temporary network issue
            // But ensure container stays visible
            $('#omniPilotProgressContainer').css('display', 'flex').show();
        }
    });
}

/**
 * Translate phase text from English to current language
 */
function translatePhaseText(phaseText) {
    if (!phaseText || typeof phaseText !== 'string') {
        return phaseText || '-';
    }
    
    // Trim whitespace
    var trimmedPhase = phaseText.trim();
    
    // Map English phase texts to language keys (exact match first)
    var phaseMap = {
        'Importing leads...': 'omni_pilot_phase_importing_leads',
        'Setting up campaign...': 'omni_pilot_phase_setting_up_campaign',
        'Campaign created successfully': 'omni_pilot_phase_campaign_created',
        'Setting up messages...': 'omni_pilot_phase_setting_up_messages',
        'Messages configured': 'omni_pilot_phase_messages_configured',
        'Setting up follow-up automation...': 'omni_pilot_phase_setting_up_followup',
        'Follow-up automation configured': 'omni_pilot_phase_followup_configured',
        'Activating campaign and follow-up...': 'omni_pilot_phase_activating',
        'Omni Pilot is active and running': 'omni_pilot_phase_active_running',
        'Starting...': 'omni_pilot_starting'
    };
    
    // Check exact match first
    if (phaseMap[trimmedPhase]) {
        var translated = omniPilotGetTranslation(phaseMap[trimmedPhase], null);
        if (translated && translated !== phaseMap[trimmedPhase]) {
            return translated;
        }
    }
    
    // Check partial matches (contains) - check if phase text contains English text
    for (var englishText in phaseMap) {
        if (trimmedPhase.toLowerCase().indexOf(englishText.toLowerCase()) !== -1) {
            var translated = omniPilotGetTranslation(phaseMap[englishText], null);
            if (translated && translated !== phaseMap[englishText]) {
                return translated;
            }
        }
    }
    
    // Also check reverse - if English text contains phase text (for partial matches)
    for (var englishText in phaseMap) {
        if (englishText.toLowerCase().indexOf(trimmedPhase.toLowerCase()) !== -1) {
            var translated = omniPilotGetTranslation(phaseMap[englishText], null);
            if (translated && translated !== phaseMap[englishText]) {
                return translated;
            }
        }
    }
    
    // Return original text if no translation found
    return phaseText;
}

/**
 * Update progress badge in footer-bar
 */
function updateProgressBadge(progress) {
    // Update footer-bar progress container
    var currentStep = progress.current_step || null;
    var stepDetail = progress.step_detail || null;
    var currentPhase = progress.current_phase || '-';
    var goalProgress = progress.goal_progress || 0;
    var goalTarget = progress.goal_target || 0;
    var campaignSent = progress.campaign_sent || 0;
    var campaignTotal = progress.campaign_total || 0;
    var followupCurrent = progress.followup_current || 0;
    var followupTotal = progress.followup_total || 0;
    
    // Build step display text
    var stepDisplayText = '';
    
    if (currentStep !== null) {
        // Format step number (handle decimal steps like 2.1)
        var stepNum = parseFloat(currentStep);
        var stepInt = Math.floor(stepNum);
        var stepDec = stepNum - stepInt;
        
        if (stepDec > 0) {
            // Decimal step (e.g., 2.1)
            stepDisplayText = 'Step ' + stepNum + ' - ';
        } else {
            // Integer step
            stepDisplayText = 'Step ' + stepInt + ' - ';
        }
        
        // Use phase text (campaign and follow-up progress shown in other badges)
        stepDisplayText += translatePhaseText(currentPhase);
    } else {
        // Fallback to phase text if no step number
        stepDisplayText = translatePhaseText(currentPhase);
    }
    
    // Update progress text with spinner
    var $spinner = $('#omniPilotSpinner');
    var $currentPhase = $('#omniPilotCurrentPhase');
    
    // Show spinner if process is active (not completed/failed/cancelled)
    // Check multiple possible status values
    var status = (progress.status || '').toLowerCase();
    var isActive = status !== 'completed' && status !== 'failed' && status !== 'cancelled' && status !== '';
    
    // Always show spinner if we have a step detail or if status indicates activity
    // Also show if we're in any step (1-4) or have progress
    if (isActive || stepDetail || currentStep !== null || progressPercent > 0) {
        $spinner.css({
            'display': 'inline-block',
            'visibility': 'visible',
            'opacity': '1'
        }).show();
        console.log('Showing spinner - isActive:', isActive, 'stepDetail:', stepDetail, 'currentStep:', currentStep, 'status:', status);
    } else {
        $spinner.hide();
        console.log('Hiding spinner - status:', status);
    }
    
    // Update text content (escape HTML for safety)
    $currentPhase.text(stepDisplayText);
    
    // Update goal progress
    var goalText = omniPilotGetTranslation('omni_pilot_goal', 'Goal');
    $('#omniPilotGoalProgress').text(goalText + ': ' + goalProgress + '/' + goalTarget);
    
    // Update progress bar based on step (for visual feedback)
    var progressPercent = 0;
    if (currentStep !== null) {
        var stepNum = parseFloat(currentStep);
        if (stepNum === 1) progressPercent = 20;
        else if (stepNum === 2) progressPercent = 40;
        else if (stepNum === 2.1) progressPercent = 50; // Campaign sending (progress shown in campaign badge)
        else if (stepNum === 3) progressPercent = 60;
        else if (stepNum === 4) progressPercent = 80; // Follow-up (progress shown in follow-up badge)
    }
    
    // Ensure progress bar is visible and animated
    var $bar = $('#omniPilotProgressBar');
    $bar.css({
        'width': progressPercent + '%',
        'min-width': progressPercent > 0 ? '2px' : '0px'
    });
    $bar.attr('aria-valuenow', progressPercent);
    $bar.attr('data-percent', progressPercent);
    
    // Add/remove active and striped classes for animation
    if (isActive && progressPercent > 0) {
        $bar.addClass('active progress-bar-striped');
        // Force animation by ensuring background-image is set
        $bar.css({
            'background-image': 'linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent)',
            'background-size': '40px 40px',
            'animation': 'progress-bar-stripes 1s linear infinite'
        });
        console.log('Progress bar animated - percent:', progressPercent, 'isActive:', isActive);
    } else {
        $bar.removeClass('active');
        // Keep striped but remove animation
        if (!isActive) {
            $bar.css({
                'background-image': 'none',
                'animation': 'none'
            });
        }
    }
    
    // Show/hide stop button based on status
    if (progress.status === 'completed' || progress.status === 'failed' || progress.status === 'cancelled') {
        $('#omniPilotStopBtn').hide();
    } else {
        $('#omniPilotStopBtn').show();
    }
    
    // Hide container if completed or failed (after delay)
    if (progress.status === 'completed' || progress.status === 'failed' || progress.status === 'cancelled') {
        setTimeout(function() {
            $('#omniPilotProgressContainer').fadeOut();
            // Clear localStorage when completed
            if (typeof Storage !== 'undefined') {
                localStorage.removeItem('omniPilotSessionId');
                localStorage.removeItem('omniPilotDeviceId');
            }
        }, 5000); // Hide after 5 seconds
    }
}

/**
 * Stop Omni Pilot session
 */
function stopOmniPilot() {
    var sessionId = omniPilotSessionId || (typeof Storage !== 'undefined' ? localStorage.getItem('omniPilotSessionId') : null);
    
    if (!sessionId) {
        alert(omniPilotGetTranslation('omni_pilot_no_active_session', 'No active session found'));
        return;
    }
    
    var confirmText = omniPilotGetTranslation('omni_pilot_stop_confirm', 'Are you sure you want to stop this Omni Pilot session?');
    if (!confirm(confirmText)) {
        return;
    }
    
    var $btn = $('#omniPilotStopBtn');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> ' + omniPilotGetTranslation('omni_pilot_stopping', 'Stopping...'));
    
    // Prepare data object properly
    var postData = {
        session_id: sessionId
    };
    
    // Add CSRF token data
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = csrfData[key];
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_stop',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Stop polling
                if (omniPilotProgressInterval) {
                    clearInterval(omniPilotProgressInterval);
                    omniPilotProgressInterval = null;
                }
                
                // Clear localStorage
                if (typeof Storage !== 'undefined') {
                    localStorage.removeItem('omniPilotSessionId');
                    localStorage.removeItem('omniPilotDeviceId');
                }
                
                // Update badge to show cancelled status
                var cancelledText = omniPilotGetTranslation('omni_pilot_phase_cancelled', 'Cancelled');
                $('#omniPilotProgressText').html(omniPilotGetTranslation('omni_pilot_phase', 'Phase') + ': <span>' + cancelledText + '</span>');
                $('#omniPilotStopBtn').hide();
                
                // Hide after delay
                setTimeout(function() {
                    $('#omniPilotProgressContainer').fadeOut();
                    // Re-add pulse animation since there's no active Omni Pilot now
                    checkAndUpdateOmniPilotPulse();
                }, 3000);
            } else {
                alert(response.message || omniPilotGetTranslation('omni_pilot_stop_failed', 'Failed to stop Omni Pilot'));
                $btn.prop('disabled', false).html('<i class="fa fa-stop"></i> ' + omniPilotGetTranslation('omni_pilot_stop', 'Stop'));
            }
        },
        error: function(xhr, status, error) {
            var errorMsg = omniPilotGetTranslation('omni_pilot_error_stopping', 'Error stopping Omni Pilot');
            if (xhr.status === 419) {
                errorMsg = omniPilotGetTranslation('omni_pilot_page_expired', 'Page expired. Please refresh the page and try again.');
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            }
            alert(errorMsg);
            $btn.prop('disabled', false).html('<i class="fa fa-stop"></i> ' + omniPilotGetTranslation('omni_pilot_stop', 'Stop'));
        }
    });
}

/**
 * Check if there's an active Omni Pilot and update pulse animation accordingly
 */
function checkAndUpdateOmniPilotPulse() {
    // Get current device ID from the page
    var deviceId = $('input[name="device_id"]').val() || '';
    
    if (!deviceId) {
        // If we can't determine device ID, don't change pulse state
        return;
    }
    
    // Check for active session via AJAX
    // Build data object properly to avoid serialization issues
    var postData = {
        device_id: String(deviceId)
    };
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = String(csrfData[key]);
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_get_active_session',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.session_id) {
                // There's an active session, remove pulse
                $('.omni-pilot-icon').removeClass('omni-pilot-pulse');
            } else {
                // No active session, add pulse
                $('.omni-pilot-icon').addClass('omni-pilot-pulse');
            }
        },
        error: function() {
            // On error, assume no active session and add pulse
            $('.omni-pilot-icon').addClass('omni-pilot-pulse');
        }
    });
}

/**
 * Restore Omni Pilot progress on page load
 */
function restoreOmniPilotProgress() {
    // Check localStorage for session ID
    if (typeof Storage !== 'undefined') {
        var savedSessionId = localStorage.getItem('omniPilotSessionId');
        var savedDeviceId = localStorage.getItem('omniPilotDeviceId');
        var currentDeviceId = $('input[name="device_id"]').val() || '';
        
        // If we have a saved session and it's for the current device, try to restore it
        if (savedSessionId && savedDeviceId === currentDeviceId) {
            // Verify session is still active
            // Prepare data object properly to avoid serialization issues
            var postData = {
                session_id: String(savedSessionId)
            };
            
            // Add CSRF token data
            var csrfData = getCsrfData();
            for (var key in csrfData) {
                if (csrfData.hasOwnProperty(key)) {
                    postData[key] = String(csrfData[key]);
                }
            }
            
            $.ajax({
                url: url_contactcenter + 'ajax_omni_pilot_progress',
                type: 'POST',
                data: postData,
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.progress) {
                        // Session is still active, restore progress
                        omniPilotSessionId = savedSessionId;
                        showProgressBadge(savedSessionId);
                        updateProgressBadge(response.progress);
                        startProgressPolling(savedSessionId);
                    } else {
                        // Session not found or completed, clear localStorage
                        localStorage.removeItem('omniPilotSessionId');
                        localStorage.removeItem('omniPilotDeviceId');
                        // Check and update pulse animation
                        checkAndUpdateOmniPilotPulse();
                    }
                },
                error: function() {
                    // Error checking session, try to get active session from backend
                    checkActiveOmniPilotSession();
                }
            });
        } else {
            // No saved session or different device, check backend for active session
            checkActiveOmniPilotSession();
        }
    } else {
        // No localStorage support, check backend
        checkActiveOmniPilotSession();
    }
}

/**
 * Check for active Omni Pilot session from backend
 */
function checkActiveOmniPilotSession() {
    // This function also checks and updates pulse
    checkAndUpdateOmniPilotPulse();
    var deviceId = $('input[name="device_id"]').val() || '';
    if (!deviceId) {
        return;
    }
    
    // Prepare data object properly to avoid serialization issues
    var postData = {
        device_id: String(deviceId)
    };
    
    // Add CSRF token data
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = String(csrfData[key]);
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_get_active_session',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.session_id && response.progress) {
                // Found active session, restore it
                omniPilotSessionId = response.session_id;
                
                // Save to localStorage
                if (typeof Storage !== 'undefined') {
                    localStorage.setItem('omniPilotSessionId', response.session_id);
                    localStorage.setItem('omniPilotDeviceId', deviceId);
                }
                
                // Show and update progress
                showProgressBadge(response.session_id);
                updateProgressBadge(response.progress);
                startProgressPolling(response.session_id);
                // Remove pulse since there's an active session
                $('.omni-pilot-icon').removeClass('omni-pilot-pulse');
            } else {
                // No active session, check and update pulse
                checkAndUpdateOmniPilotPulse();
            }
        },
        error: function() {
            // Silently fail - no active session, check and update pulse
            checkAndUpdateOmniPilotPulse();
        }
    });
}

/**
 * Insert placeholder in message
 */
function insertPlaceholder(placeholder) {
    var $textarea = $('#omni_selected_message_text');
    var cursorPos = $textarea.prop('selectionStart');
    var text = $textarea.val();
    var newText = text.substring(0, cursorPos) + placeholder + text.substring(cursorPos);
    $textarea.val(newText);
    $textarea.focus();
}

/**
 * Get media type from filename
 */
function getMediaType(filenameOrType) {
    if (!filenameOrType) return null;
    
    // If it's a MIME type (contains '/')
    if (filenameOrType.indexOf('/') !== -1) {
        if (filenameOrType.indexOf('image/') === 0) return 'image';
        if (filenameOrType.indexOf('video/') === 0) return 'video';
        if (filenameOrType.indexOf('audio/') === 0) return 'audio';
        if (filenameOrType.indexOf('application/pdf') === 0) return 'document';
        return 'file';
    }
    
    // Otherwise treat as filename
    var ext = filenameOrType.split('.').pop().toLowerCase();
    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].indexOf(ext) !== -1) return 'image';
    if (['mp4', 'avi', 'mov', 'webm', 'mkv'].indexOf(ext) !== -1) return 'video';
    if (['mp3', 'wav', 'ogg', 'm4a'].indexOf(ext) !== -1) return 'audio';
    if (['pdf', 'doc', 'docx'].indexOf(ext) !== -1) return 'document';
    return 'file';
}

/**
 * Escape HTML
 */
function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

/**
 * Save Omni Pilot template
 */
function saveOmniPilotTemplate() {
    // Save current step data first
    saveCurrentStep();
    
    // Get template name from user
    var templateName = prompt(omniPilotGetTranslation('omni_pilot_template_name_prompt', 'Enter a name for this template:'));
    if (!templateName || !templateName.trim()) {
        return;
    }
    
    var templateDescription = prompt(omniPilotGetTranslation('omni_pilot_template_description_prompt', 'Enter a description (optional):')) || '';
    
    // Prepare wizard data
    var wizardData = omniPilotWizardData;
    
    // Build data object properly to avoid serialization issues
    var postData = {
        name: String(templateName.trim()),
        description: String(templateDescription.trim()),
        wizard_data: JSON.stringify(wizardData)
    };
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = String(csrfData[key]);
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_save_template',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(omniPilotGetTranslation('omni_pilot_template_saved', 'Template saved successfully!'));
            } else {
                alert(response.message || omniPilotGetTranslation('omni_pilot_template_save_failed', 'Failed to save template'));
            }
        },
        error: function(xhr, status, error) {
            var errorMsg = omniPilotGetTranslation('omni_pilot_error_saving_template', 'Error saving template');
            if (xhr.status === 419) {
                errorMsg = omniPilotGetTranslation('omni_pilot_page_expired', 'Page expired. Please refresh the page and try again.');
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            }
            alert(errorMsg);
        }
    });
}

/**
 * Load Omni Pilot templates
 */
function loadOmniPilotTemplates() {
    // Prepare data object properly
    var postData = {};
    
    // Add CSRF token data
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = csrfData[key];
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_get_templates',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.templates && response.templates.length > 0) {
                showTemplateSelectionModal(response.templates);
            } else {
                alert(omniPilotGetTranslation('omni_pilot_no_templates', 'No saved templates found'));
            }
        },
        error: function(xhr, status, error) {
            var errorMsg = omniPilotGetTranslation('omni_pilot_error_loading_templates', 'Error loading templates');
            if (xhr.status === 419) {
                errorMsg = omniPilotGetTranslation('omni_pilot_page_expired', 'Page expired. Please refresh the page and try again.');
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            }
            alert(errorMsg);
        }
    });
}

/**
 * Show template selection modal
 */
function showTemplateSelectionModal(templates) {
    var modalHtml = '<div class="modal fade" id="omniTemplateModal" tabindex="-1" role="dialog">' +
        '<div class="modal-dialog" role="document">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        '<h4 class="modal-title">' + omniPilotGetTranslation('omni_pilot_select_template', 'Select Template') + '</h4>' +
        '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
        '</div>' +
        '<div class="modal-body">' +
        '<div class="list-group">';
    
    templates.forEach(function(template) {
        modalHtml += '<a href="#" class="list-group-item template-item" data-template-id="' + template.id + '">' +
            '<h5 class="list-group-item-heading">' + escapeHtml(template.name) + '</h5>' +
            (template.description ? '<p class="list-group-item-text">' + escapeHtml(template.description) + '</p>' : '') +
            '<small class="text-muted">' + omniPilotGetTranslation('omni_pilot_created', 'Created') + ': ' + 
            (template.created_at ? new Date(template.created_at).toLocaleDateString() : '-') + '</small>' +
            '<div style="margin-top: 8px;">' +
            '<button class="btn btn-xs btn-primary load-template-btn" data-template-id="' + template.id + '">' +
            omniPilotGetTranslation('omni_pilot_load', 'Load') + '</button> ' +
            '<button class="btn btn-xs btn-danger delete-template-btn" data-template-id="' + template.id + '">' +
            omniPilotGetTranslation('omni_pilot_delete', 'Delete') + '</button>' +
            '</div>' +
            '</a>';
    });
    
    modalHtml += '</div></div></div></div></div>';
    
    // Remove existing modal if any
    $('#omniTemplateModal').remove();
    
    // Add modal to body
    $('body').append(modalHtml);
    
    // Show modal
    $('#omniTemplateModal').modal('show');
    
    // Load template handler
    $(document).off('click', '.load-template-btn').on('click', '.load-template-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var templateId = $(this).data('template-id');
        loadTemplate(templateId);
    });
    
    // Delete template handler
    $(document).off('click', '.delete-template-btn').on('click', '.delete-template-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var templateId = $(this).data('template-id');
        deleteTemplate(templateId);
    });
    
    // Click on template item to load
    $(document).off('click', '.template-item').on('click', '.template-item', function(e) {
        e.preventDefault();
        if (!$(e.target).closest('button').length) {
            var templateId = $(this).data('template-id');
            loadTemplate(templateId);
        }
    });
    
    // Cleanup on modal close
    $('#omniTemplateModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

/**
 * Load template into wizard
 */
function loadTemplate(templateId) {
    // Build data object properly to avoid serialization issues
    var postData = {
        template_id: String(templateId)
    };
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = String(csrfData[key]);
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_get_template',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.template && response.template.wizard_data) {
                // Load wizard data
                omniPilotWizardData = response.template.wizard_data;
                
                // Populate wizard steps with template data
                populateWizardFromTemplate(omniPilotWizardData);
                
                // Close template modal
                $('#omniTemplateModal').modal('hide');
                
                // Reset to step 0
                omniPilotCurrentStep = 0;
                showStep(0);
                
                alert(omniPilotGetTranslation('omni_pilot_template_loaded', 'Template loaded successfully!'));
            } else {
                alert(response.message || omniPilotGetTranslation('omni_pilot_template_load_failed', 'Failed to load template'));
            }
        },
        error: function(xhr, status, error) {
            var errorMsg = omniPilotGetTranslation('omni_pilot_error_loading_template', 'Error loading template');
            if (xhr.status === 419) {
                errorMsg = omniPilotGetTranslation('omni_pilot_page_expired', 'Page expired. Please refresh the page and try again.');
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            }
            alert(errorMsg);
        }
    });
}

/**
 * Populate wizard from template data
 */
function populateWizardFromTemplate(templateData) {
    // Step 0: Goal
    if (templateData.step_0) {
        if (templateData.step_0.goal_target) $('#goal_target').val(templateData.step_0.goal_target);
        if (templateData.step_0.goal_status_id) $('#goal_status_id').val(templateData.step_0.goal_status_id).trigger('change');
        if (templateData.step_0.deadline_date) {
            var date = new Date(templateData.step_0.deadline_date);
            var year = date.getFullYear();
            var month = String(date.getMonth() + 1).padStart(2, '0');
            var day = String(date.getDate()).padStart(2, '0');
            $('#deadline_date').val(year + '-' + month + '-' + day);
        }
        if (templateData.step_0.product_company) $('#product_company').val(templateData.step_0.product_company);
        if (templateData.step_0.approach) {
            $('#approach').val(templateData.step_0.approach);
            $('.omni-approach-card').removeClass('selected');
            $('.omni-approach-card[data-approach="' + templateData.step_0.approach + '"]').addClass('selected');
        }
        if (templateData.step_0.language) $('#omni_language').val(templateData.step_0.language).trigger('change');
    }
    
    // Step 1: Supply
    if (templateData.step_1) {
        if (templateData.step_1.import_method) {
            $('input[name="import_method"][value="' + templateData.step_1.import_method + '"]').prop('checked', true).trigger('change');
            if (templateData.step_1.import_method === 'ai') {
                if (templateData.step_1.ai_state) {
                    $('#omni_ai_state').val(templateData.step_1.ai_state).trigger('change');
                    setTimeout(function() {
                        if (templateData.step_1.ai_city) $('#omni_ai_city').val(templateData.step_1.ai_city).trigger('change');
                    }, 500);
                }
                if (templateData.step_1.ai_category) $('#omni_ai_category').val(templateData.step_1.ai_category).trigger('change');
                if (templateData.step_1.ai_quantity) $('#omni_ai_quantity').val(templateData.step_1.ai_quantity);
                if (templateData.step_1.enable_gemini_enrichment !== undefined) {
                    $('#omni_enable_enrichment').prop('checked', templateData.step_1.enable_gemini_enrichment == 1);
                }
            }
        }
    }
    
    // Step 2: Campaign
    if (templateData.step_2) {
        if (templateData.step_2.device_id) $('#omni_device_id').val(templateData.step_2.device_id).trigger('change');
        if (templateData.step_2.campaign_name) $('#omni_campaign_name').val(templateData.step_2.campaign_name);
    }
    
    // Step 3: Message
    if (templateData.step_3) {
        if (templateData.step_3.selected_message) {
            // Message will be regenerated or user can edit
            omniPilotWizardData.step_3 = templateData.step_3;
        }
    }
    
    // Step 4: Assistant
    if (templateData.step_4) {
        omniPilotWizardData.step_4 = templateData.step_4;
    }
    
    // Step 5: Follow-up
    if (templateData.step_5) {
        omniPilotWizardData.step_5 = templateData.step_5;
        // Follow-ups will be loaded when user reaches step 5
    }
    
    // Refresh selectpickers
    if (typeof $().selectpicker === 'function') {
        $('.selectpicker').selectpicker('refresh');
    }
}

/**
 * Delete template
 */
function deleteTemplate(templateId) {
    var confirmText = omniPilotGetTranslation('omni_pilot_delete_template_confirm', 'Are you sure you want to delete this template?');
    if (!confirm(confirmText)) {
        return;
    }
    
    // Prepare data object properly to avoid serialization issues
    var postData = {
        template_id: String(templateId)
    };
    
    // Add CSRF token data
    var csrfData = getCsrfData();
    for (var key in csrfData) {
        if (csrfData.hasOwnProperty(key)) {
            postData[key] = String(csrfData[key]);
        }
    }
    
    $.ajax({
        url: url_contactcenter + 'ajax_omni_pilot_delete_template',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Remove from modal
                $('.template-item[data-template-id="' + templateId + '"]').fadeOut(300, function() {
                    $(this).remove();
                    // If no templates left, close modal
                    if ($('.template-item').length === 0) {
                        $('#omniTemplateModal').modal('hide');
                        alert(omniPilotGetTranslation('omni_pilot_no_templates', 'No saved templates found'));
                    }
                });
            } else {
                alert(response.message || omniPilotGetTranslation('omni_pilot_template_delete_failed', 'Failed to delete template'));
            }
        },
        error: function(xhr, status, error) {
            var errorMsg = omniPilotGetTranslation('omni_pilot_error_deleting_template', 'Error deleting template');
            if (xhr.status === 419) {
                errorMsg = omniPilotGetTranslation('omni_pilot_page_expired', 'Page expired. Please refresh the page and try again.');
                setTimeout(function() {
                    window.location.reload();
                }, 2000);
            }
            alert(errorMsg);
        }
    });
}
