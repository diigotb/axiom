/**
 * Ads Analytics Dashboard JavaScript
 */

(function($) {
    'use strict';
    
    var AdsAnalyticsDashboard = {
        init: function() {
            this.initCountUp();
            this.initCharts();
            this.initFilters();
            this.initAIInsights();
        },
        
        initCountUp: function() {
            // Initialize CountUp animations for KPI cards
            if (typeof CountUp !== 'undefined') {
                $('.kpi-value').each(function() {
                    var $this = $(this);
                    var value = $this.text().replace(/[^\d,.]/g, '').replace(/\./g, '').replace(',', '.');
                    var numValue = parseFloat(value);
                    
                    if (!isNaN(numValue)) {
                        try {
                            var countUp = new CountUp(this, numValue, {
                                separator: '.',
                                decimal: ',',
                                duration: 2
                            });
                            if (countUp.error) {
                                console.log('CountUp error:', countUp.error);
                            } else {
                                countUp.start();
                            }
                        } catch(e) {
                            console.log('CountUp initialization error:', e);
                        }
                    }
                });
            }
        },
        
        initCharts: function() {
            // Charts are initialized in view-specific scripts
            // This is a placeholder for common chart functionality
        },
        
        initFilters: function() {
            // Quick date presets
            $('.date-preset-btn').on('click', function(e) {
                e.preventDefault();
                var days = $(this).data('days');
                var dateFrom = new Date();
                dateFrom.setDate(dateFrom.getDate() - days);
                var dateTo = new Date();
                
                var formatDate = function(date) {
                    var day = String(date.getDate()).padStart(2, '0');
                    var month = String(date.getMonth() + 1).padStart(2, '0');
                    var year = date.getFullYear();
                    return day + '/' + month + '/' + year;
                };
                
                $('input[name="date_from"]').val(formatDate(dateFrom));
                $('input[name="date_to"]').val(formatDate(dateTo));
                $('#dashboard-filters-form').submit();
            });
        },
        
        initAIInsights: function() {
            // Generate AI Insights
            $('#generate-insights-btn').on('click', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var originalText = $btn.html();
                
                $btn.prop('disabled', true);
                $btn.html('<i class="fa fa-spinner fa-spin"></i> Gerando Insights...');
                
                var filters = {
                    date_from: $('input[name="date_from"]').val(),
                    date_to: $('input[name="date_to"]').val(),
                    status: $('select[name="status"]').val(),
                    assigned: $('select[name="assigned"]').val()
                };
                
                $.ajax({
                    url: admin_url + 'contactcenter/ads_analytics_generate_ai_insights',
                    type: 'POST',
                    data: filters,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            AdsAnalyticsDashboard.displayInsights(response.insights);
                        } else {
                            alert_float('danger', response.message || 'Erro ao gerar insights');
                        }
                    },
                    error: function() {
                        alert_float('danger', 'Erro ao conectar com o servidor');
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btn.html(originalText);
                    }
                });
            });
        },
        
        displayInsights: function(insights) {
            var $container = $('#ai-insights-container');
            if (!$container.length) {
                $container = $('<div id="ai-insights-container"></div>');
                $('.panel-body').first().after($container);
            }
            
            $container.empty();
            
            // Performance Score
            if (insights.performance_score !== undefined) {
                var scoreClass = insights.performance_score >= 70 ? 'success' : 
                                insights.performance_score >= 50 ? 'warning' : 'danger';
                var scoreHtml = '<div class="panel panel-' + scoreClass + '">' +
                    '<div class="panel-heading">' +
                    '<h4 class="panel-title">Score de Performance: ' + insights.performance_score + '/100</h4>' +
                    '</div>' +
                    '<div class="panel-body">' +
                    '<p>' + (insights.assessment || '') + '</p>' +
                    '</div>' +
                    '</div>';
                $container.append(scoreHtml);
            }
            
            // Optimization Opportunities
            if (insights.optimization_opportunities && insights.optimization_opportunities.length > 0) {
                var oppsHtml = '<div class="panel panel-default">' +
                    '<div class="panel-heading">' +
                    '<h4 class="panel-title">Oportunidades de Otimização</h4>' +
                    '</div>' +
                    '<div class="panel-body">';
                
                insights.optimization_opportunities.forEach(function(opp) {
                    var priorityClass = opp.priority === 'High' ? 'high' : 
                                       opp.priority === 'Medium' ? 'medium' : 'low';
                    oppsHtml += '<div class="ai-insight-card">' +
                        '<div class="ai-insight-header">' +
                        '<h5 class="ai-insight-title">' + (opp.title || '') + '</h5>' +
                        '<span class="priority-badge priority-' + priorityClass + '">' + opp.priority + '</span>' +
                        '</div>' +
                        '<div class="ai-insight-content">' +
                        '<p><strong>Descrição:</strong> ' + (opp.description || '') + '</p>' +
                        '<p><strong>Impacto:</strong> ' + (opp.impact || '') + '</p>' +
                        '<p><strong>Ação Recomendada:</strong> ' + (opp.action || '') + '</p>' +
                        '</div>' +
                        '</div>';
                });
                
                oppsHtml += '</div></div>';
                $container.append(oppsHtml);
            }
            
            // Next Steps
            if (insights.next_steps && insights.next_steps.length > 0) {
                var stepsHtml = '<div class="panel panel-info">' +
                    '<div class="panel-heading">' +
                    '<h4 class="panel-title">Próximos Passos</h4>' +
                    '</div>' +
                    '<div class="panel-body">' +
                    '<ul>';
                
                insights.next_steps.forEach(function(step) {
                    stepsHtml += '<li>' + step + '</li>';
                });
                
                stepsHtml += '</ul></div></div>';
                $container.append(stepsHtml);
            }
            
            $container.slideDown();
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        AdsAnalyticsDashboard.init();
    });
    
})(jQuery);
