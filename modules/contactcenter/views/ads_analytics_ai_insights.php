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
                                <i class="fa fa-brain"></i>
                                <?php echo _l('ads_analytics_ai_insights'); ?>
                            </span>
                            <div class="pull-right">
                                <button type="button" id="generate-insights-btn" class="btn btn-primary generate-insights-btn">
                                    <i class="fa fa-magic"></i> <?php echo _l('ads_analytics_generate_insights'); ?>
                                </button>
                            </div>
                        </h4>
                        <hr class="hr-panel-separator" />
                        
                        <!-- Filters Section -->
                        <div class="panel panel-default" style="margin-bottom: 20px;">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <i class="fa fa-filter"></i> <?= _l("filters"); ?>
                                </h4>
                            </div>
                            <div class="panel-body">
                                <form method="get" action="<?= admin_url('contactcenter/ads_analytics_ai_insights'); ?>" id="ai-insights-filters-form">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?php echo _l('ads_analytics_date_from'); ?></label>
                                            <div class="input-group">
                                                <input type="text" name="date_from" class="form-control datepicker" value="<?= isset($filters['date_from_display']) && !empty($filters['date_from_display']) ? htmlspecialchars($filters['date_from_display']) : (isset($filters['date_from']) && !empty($filters['date_from']) ? _d($filters['date_from']) : ''); ?>" placeholder="<?php echo _l('ads_analytics_date_from'); ?>">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar calendar-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?php echo _l('ads_analytics_date_to'); ?></label>
                                            <div class="input-group">
                                                <input type="text" name="date_to" class="form-control datepicker" value="<?= isset($filters['date_to_display']) && !empty($filters['date_to_display']) ? htmlspecialchars($filters['date_to_display']) : (isset($filters['date_to']) && !empty($filters['date_to']) ? _d($filters['date_to']) : ''); ?>" placeholder="<?php echo _l('ads_analytics_date_to'); ?>">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar calendar-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><?= _l("leads_dt_status"); ?></label>
                                            <select name="status" class="selectpicker" data-width="100%">
                                                <option value=""><?= _l("all"); ?></option>
                                                <?php if (isset($statuses) && !empty($statuses)) { ?>
                                                    <?php foreach ($statuses as $status) { ?>
                                                        <option value="<?php echo $status['id']; ?>" <?php echo (isset($filters['status']) && $filters['status'] == $status['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($status['name']); ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><?= _l("leads_dt_assigned"); ?></label>
                                            <select name="assigned" class="selectpicker" data-width="100%">
                                                <option value=""><?= _l("all"); ?></option>
                                                <?php if (isset($staff_members) && !empty($staff_members)) { ?>
                                                    <?php foreach ($staff_members as $staff) { ?>
                                                        <option value="<?php echo $staff['staffid']; ?>" <?php echo (isset($filters['assigned']) && $filters['assigned'] == $staff['staffid']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($staff['firstname'] . ' ' . $staff['lastname']); ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fa fa-search"></i> <?= _l("filter"); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- AI Insights Container -->
                        <div id="ai-insights-container">
                            <?php if (isset($cached_insights) && $cached_insights && !empty($cached_insights)) { ?>
                                <div class="alert alert-info" style="margin-bottom: 20px;">
                                    <i class="fa fa-info-circle"></i> Insights em cache (gerados há menos de 1 hora)
                                </div>
                                <script>
                                    // Store cached insights for JavaScript to display on page load
                                    var cachedInsightsOnLoad = <?php echo json_encode($cached_insights, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>;
                                </script>
                            <?php } else { ?>
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i> Clique em "Gerar Insights" para obter análises inteligentes dos seus dados.
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
(function($) {
    $(document).ready(function() {
        // Initialize datepickers (same pattern as ads_analytics.php)
        if (typeof appDatepicker === 'function') {
            appDatepicker();
        } else {
            // Fallback initialization
            $('.datepicker').each(function() {
                var $input = $(this);
                if (typeof jQuery.fn.datetimepicker !== 'undefined') {
                    $input.datetimepicker({
                        timepicker: false,
                        format: app.options.date_format || 'd/m/Y',
                        scrollInput: false,
                        lazyInit: true
                    });
                }
            });
        }
        
        // Display cached insights on page load if available
        if (typeof cachedInsightsOnLoad !== 'undefined' && cachedInsightsOnLoad) {
            console.log('=== DISPLAYING CACHED INSIGHTS ON PAGE LOAD ===');
            console.log('Cached insights:', cachedInsightsOnLoad);
            displayAIInsights(cachedInsightsOnLoad);
        }
        
        // Generate insights button
        console.log('AI Insights page loaded - setting up button click handler');
        console.log('Button element found:', $('#generate-insights-btn').length > 0);
        
        $('#generate-insights-btn').on('click', function(e) {
            console.log('=== BUTTON CLICKED ===');
            e.preventDefault();
            var $btn = $(this);
            var originalText = $btn.html();
            
            // Check if button is already disabled (prevent double clicks)
            if ($btn.prop('disabled')) {
                console.log('Button already disabled, ignoring click');
                return;
            }
            
            console.log('Button clicked - disabling and updating text');
            $btn.prop('disabled', true);
            $btn.html('<i class="fa fa-spinner fa-spin"></i> <?php echo _l('ads_analytics_generating_insights'); ?>...');
            
            var filters = {
                date_from: $('input[name="date_from"]').val() || '',
                date_to: $('input[name="date_to"]').val() || '',
                status: $('select[name="status"]').val() || '',
                assigned: $('select[name="assigned"]').val() || ''
            };
            
            console.log('=== FILTERS COLLECTED ===');
            console.log('Date From:', filters.date_from);
            console.log('Date To:', filters.date_to);
            console.log('Status:', filters.status);
            console.log('Assigned:', filters.assigned);
            
            var ajaxUrl = '<?php echo admin_url('contactcenter/ads_analytics_generate_ai_insights'); ?>';
            console.log('=== AJAX REQUEST STARTING ===');
            console.log('URL:', ajaxUrl);
            console.log('Method: POST');
            console.log('Data:', $.extend({}, filters, {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            }));
            
            var requestStartTime = new Date().getTime();
            
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: $.extend(filters, {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                }),
                dataType: 'json',
                beforeSend: function(xhr) {
                    console.log('=== AJAX BEFORE SEND ===');
                    console.log('XHR Object:', xhr);
                    console.log('Request Headers:', xhr.getAllResponseHeaders ? 'Available' : 'N/A');
                },
                success: function(response, textStatus, xhr) {
                    var requestTime = new Date().getTime() - requestStartTime;
                    console.log('=== AJAX SUCCESS ===');
                    console.log('Request took:', requestTime, 'ms');
                    console.log('Status:', textStatus);
                    console.log('HTTP Status Code:', xhr.status);
                    console.log('Response Type:', typeof response);
                    console.log('Full Response:', JSON.stringify(response, null, 2));
                    
                    if (response && response.success) {
                        console.log('Response indicates success');
                        console.log('Insights data:', response.insights);
                        $('#ai-insights-container').html('');
                        displayAIInsights(response.insights);
                        console.log('Insights displayed successfully');
                        alert_float('success', 'Insights gerados com sucesso!');
                    } else {
                        console.log('Response indicates failure');
                        var errorMsg = (response && response.message) ? response.message : 'Erro ao gerar insights';
                        console.log('Error message:', errorMsg);
                        alert_float('danger', errorMsg);
                    }
                },
                error: function(xhr, status, error) {
                    var requestTime = new Date().getTime() - requestStartTime;
                    console.error('=== AJAX ERROR ===');
                    console.error('Request took:', requestTime, 'ms');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('HTTP Status Code:', xhr.status);
                    console.error('Response Text (first 1000 chars):', xhr.responseText ? xhr.responseText.substring(0, 1000) : 'No response text');
                    console.error('Response Headers:', xhr.getAllResponseHeaders ? xhr.getAllResponseHeaders() : 'N/A');
                    
                    var errorMsg = 'Erro ao conectar com o servidor (HTTP ' + xhr.status + ')';
                    try {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                            console.log('Found error message in responseJSON:', errorMsg);
                        } else if (xhr.responseText) {
                            console.log('Attempting to parse responseText as JSON');
                            var parsed = JSON.parse(xhr.responseText);
                            if (parsed && parsed.message) {
                                errorMsg = parsed.message;
                                console.log('Found error message in parsed response:', errorMsg);
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        console.error('Response text that failed to parse:', xhr.responseText);
                    }
                    console.log('Displaying error to user:', errorMsg);
                    alert_float('danger', errorMsg);
                },
                complete: function(xhr, textStatus) {
                    console.log('=== AJAX COMPLETE ===');
                    console.log('Final Status:', textStatus);
                    console.log('Final HTTP Status:', xhr.status);
                    $btn.prop('disabled', false);
                    $btn.html(originalText);
                    console.log('Button re-enabled');
                }
            });
            
            console.log('AJAX request initiated, waiting for response...');
        });
        
        console.log('Button click handler attached successfully');
        
        function displayAIInsights(insights) {
            console.log('=== DISPLAYING INSIGHTS ===');
            console.log('Insights type:', typeof insights);
            console.log('Insights:', insights);
            console.log('Insights keys:', insights ? Object.keys(insights) : 'null');
            
            var $container = $('#ai-insights-container');
            var html = '';
            
            // Helper function to escape HTML
            function escapeHtml(text) {
                if (!text) return '';
                var map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
            }
            
            if (!insights || typeof insights !== 'object') {
                console.error('Invalid insights format:', insights);
                html = '<div class="alert alert-danger">Erro: Formato de dados inválido recebido. Tipo: ' + typeof insights + '</div>';
                $container.html(html);
                return;
            }
            
            // Check if this looks like a nested object (missing root keys)
            if (!insights.hasOwnProperty('performance_score') && !insights.hasOwnProperty('assessment') && !insights.hasOwnProperty('optimization_opportunities')) {
                console.warn('WARNING: Insights object appears to be a nested object, not the root structure');
                console.warn('Received keys:', Object.keys(insights));
                html = '<div class="alert alert-warning">Aviso: Estrutura de dados incompleta recebida. Chaves encontradas: ' + Object.keys(insights).join(', ') + '</div>';
                $container.html(html);
                return;
            }
            
            // Performance Score
            if (insights.performance_score !== undefined && insights.performance_score !== null) {
                var scoreClass = insights.performance_score >= 70 ? 'success' : insights.performance_score >= 50 ? 'warning' : 'danger';
                html += '<div class="panel panel-' + scoreClass + ' fade-in">' +
                    '<div class="panel-heading"><h4>Score de Performance: ' + escapeHtml(insights.performance_score) + '/100</h4></div>' +
                    '<div class="panel-body"><p>' + escapeHtml(insights.assessment || '') + '</p></div></div>';
            }
            
            // Optimization Opportunities
            if (insights.optimization_opportunities && Array.isArray(insights.optimization_opportunities) && insights.optimization_opportunities.length > 0) {
                html += '<div class="panel panel-default fade-in" style="margin-top: 20px;"><div class="panel-heading"><h4>Oportunidades de Otimização</h4></div><div class="panel-body">';
                insights.optimization_opportunities.forEach(function(opp) {
                    var priorityClass = (opp.priority === 'High' || opp.priority === 'Alta') ? 'high' : (opp.priority === 'Medium' || opp.priority === 'Média') ? 'medium' : 'low';
                    html += '<div class="ai-insight-card slide-up" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">' +
                        '<div class="ai-insight-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">' +
                        '<h5 class="ai-insight-title" style="margin: 0;">' + escapeHtml(opp.title || '') + '</h5>' +
                        '<span class="priority-badge priority-' + priorityClass + '" style="padding: 5px 10px; border-radius: 3px; background: ' + 
                        (priorityClass === 'high' ? '#d9534f' : priorityClass === 'medium' ? '#f0ad4e' : '#5cb85c') + 
                        '; color: white; font-size: 12px;">' + escapeHtml(opp.priority || '') + '</span>' +
                        '</div>' +
                        '<div class="ai-insight-content">' +
                        '<p><strong>Descrição:</strong> ' + escapeHtml(opp.description || '') + '</p>' +
                        '<p><strong>Impacto:</strong> ' + escapeHtml(opp.impact || '') + '</p>' +
                        '<p><strong>Ação:</strong> ' + escapeHtml(opp.action || '') + '</p>' +
                        '</div></div>';
                });
                html += '</div></div>';
            }
            
            // Next Steps
            if (insights.next_steps && Array.isArray(insights.next_steps) && insights.next_steps.length > 0) {
                console.log('Processing next_steps array, length:', insights.next_steps.length);
                console.log('Next steps data:', JSON.stringify(insights.next_steps, null, 2));
                html += '<div class="panel panel-info fade-in" style="margin-top: 20px;"><div class="panel-heading"><h4>Próximos Passos</h4></div><div class="panel-body"><ul>';
                insights.next_steps.forEach(function(step, index) {
                    console.log('Processing next_steps[' + index + ']:', typeof step, step);
                    var stepText = '';
                    if (typeof step === 'string') {
                        stepText = step;
                    } else if (typeof step === 'object' && step !== null) {
                        // Extract text from object - try multiple possible property names
                        stepText = step.text || step.description || step.title || step.message || step.step || step.content || step.action;
                        // If still empty, try to get any string value from the object
                        if (!stepText) {
                            for (var key in step) {
                                if (step.hasOwnProperty(key) && typeof step[key] === 'string' && step[key].trim() !== '') {
                                    stepText = step[key];
                                    break;
                                }
                            }
                        }
                        // Last resort: format as readable text
                        if (!stepText) {
                            var parts = [];
                            if (step.title) parts.push(step.title);
                            if (step.description) parts.push(step.description);
                            stepText = parts.join(': ');
                        }
                    } else {
                        stepText = String(step);
                    }
                    if (stepText && stepText.trim() !== '' && stepText !== '{}' && stepText !== '[object Object]') {
                        html += '<li>' + escapeHtml(stepText) + '</li>';
                    }
                });
                html += '</ul></div></div>';
            }
            
            // Underperforming Areas
            if (insights.underperforming_areas && Array.isArray(insights.underperforming_areas) && insights.underperforming_areas.length > 0) {
                console.log('Processing underperforming_areas array, length:', insights.underperforming_areas.length);
                console.log('Underperforming areas data:', JSON.stringify(insights.underperforming_areas, null, 2));
                html += '<div class="panel panel-warning fade-in" style="margin-top: 20px;"><div class="panel-heading"><h4>Áreas de Baixo Desempenho</h4></div><div class="panel-body"><ul>';
                insights.underperforming_areas.forEach(function(area, index) {
                    console.log('Processing underperforming_areas[' + index + ']:', typeof area, area);
                    var areaText = '';
                    if (typeof area === 'string') {
                        areaText = area;
                    } else if (typeof area === 'object' && area !== null) {
                        // Extract text from object - try multiple possible property names
                        areaText = area.description || area.title || area.message || area.text || area.name || area.area || area.issue;
                        // If still empty, try to get any string value from the object
                        if (!areaText) {
                            for (var key in area) {
                                if (area.hasOwnProperty(key) && typeof area[key] === 'string' && area[key].trim() !== '') {
                                    areaText = area[key];
                                    break;
                                }
                            }
                        }
                        // Last resort: format as readable text
                        if (!areaText) {
                            var parts = [];
                            if (area.title) parts.push(area.title);
                            if (area.description) parts.push(area.description);
                            areaText = parts.join(': ');
                        }
                    } else {
                        areaText = String(area);
                    }
                    if (areaText && areaText.trim() !== '' && areaText !== '{}' && areaText !== '[object Object]') {
                        html += '<li>' + escapeHtml(areaText) + '</li>';
                    }
                });
                html += '</ul></div></div>';
            }
            
            // Budget Recommendations
            if (insights.budget_recommendations && Array.isArray(insights.budget_recommendations) && insights.budget_recommendations.length > 0) {
                html += '<div class="panel panel-default fade-in" style="margin-top: 20px;"><div class="panel-heading"><h4>Recomendações de Orçamento</h4></div><div class="panel-body"><ul>';
                insights.budget_recommendations.forEach(function(rec) {
                    var recText = '';
                    if (typeof rec === 'string') {
                        recText = rec;
                    } else if (typeof rec === 'object' && rec !== null) {
                        // Extract text from object
                        recText = rec.description || rec.title || rec.message || rec.text || rec.recommendation || JSON.stringify(rec);
                    } else {
                        recText = String(rec);
                    }
                    if (recText && recText.trim() !== '' && recText !== '{}') {
                        html += '<li>' + escapeHtml(recText) + '</li>';
                    }
                });
                html += '</ul></div></div>';
            }
            
            // Creative Insights
            if (insights.creative_insights && Array.isArray(insights.creative_insights) && insights.creative_insights.length > 0) {
                html += '<div class="panel panel-default fade-in" style="margin-top: 20px;"><div class="panel-heading"><h4>Insights de Criativos</h4></div><div class="panel-body"><ul>';
                insights.creative_insights.forEach(function(insight) {
                    var insightText = '';
                    if (typeof insight === 'string') {
                        insightText = insight;
                    } else if (typeof insight === 'object' && insight !== null) {
                        // Extract text from object
                        insightText = insight.description || insight.title || insight.message || insight.text || insight.insight || JSON.stringify(insight);
                    } else {
                        insightText = String(insight);
                    }
                    if (insightText && insightText.trim() !== '' && insightText !== '{}') {
                        html += '<li>' + escapeHtml(insightText) + '</li>';
                    }
                });
                html += '</ul></div></div>';
            }
            
            // If no structured data but raw analysis exists
            if (html === '' && insights.raw_analysis) {
                html = '<div class="panel panel-default fade-in"><div class="panel-heading"><h4>Análise</h4></div><div class="panel-body"><pre style="white-space: pre-wrap;">' + escapeHtml(insights.raw_analysis) + '</pre></div></div>';
            }
            
            // If still empty, show a message
            if (html === '') {
                html = '<div class="alert alert-info">Insights gerados, mas nenhum dado estruturado encontrado. Verifique o console do navegador para mais detalhes.</div>';
            }
            
            $container.html(html);
        }
    });
})(jQuery);
</script>
