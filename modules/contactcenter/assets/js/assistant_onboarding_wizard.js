/**
 * Assistant Onboarding Wizard - Public Form
 * Animated multi-step wizard
 */
(function() {
    var currentStep = 1;
    var totalSteps = 11;
    var steps = [];
    var capabilityCheckboxes = [];
    var autoSaveTimer = null;
    var AUTO_SAVE_DEBOUNCE_MS = 500;

    function init() {
        steps = document.querySelectorAll('.wizard-step');
        renderStepDots();
        renderCapabilities();
        bindEvents();
        initAutoSave();
        restoreSavedData();
        updateUI();
    }

    function renderStepDots() {
        var container = document.getElementById('stepDots');
        if (!container) return;
        container.innerHTML = '';
        for (var i = 1; i <= totalSteps; i++) {
            var dot = document.createElement('span');
            dot.className = 'step-dot' + (i === 1 ? ' active' : '');
            dot.dataset.step = i;
            container.appendChild(dot);
        }
    }

    function renderCapabilities() {
        var grid = document.getElementById('capabilityGrid');
        if (!grid || typeof ASSISTANT_CAPABILITIES === 'undefined') return;
        grid.innerHTML = '';
        ASSISTANT_CAPABILITIES.forEach(function(cap) {
            var label = document.createElement('label');
            label.className = 'capability-card';
            label.innerHTML = '<input type="checkbox" name="functions[]" value="' + cap.id + '">' +
                '<i class="fa-solid ' + cap.icon + '"></i>' +
                '<span class="cap-text"><strong>' + cap.label + '</strong><small>' + cap.desc + '</small></span>';
            grid.appendChild(label);
        });
    }

    function bindEvents() {
        var btnNext = document.getElementById('btnNext');
        var btnPrev = document.getElementById('btnPrev');
        var btnSubmit = document.getElementById('btnSubmit');

        if (btnNext) btnNext.addEventListener('click', goNext);
        if (btnPrev) btnPrev.addEventListener('click', goPrev);
        if (btnSubmit) btnSubmit.addEventListener('click', submitForm);

        document.querySelectorAll('.option-card').forEach(function(card) {
            card.addEventListener('click', function(e) {
                var inp = this.querySelector('input');
                if (!inp) return;
                if (inp.type === 'checkbox') {
                    e.preventDefault();
                    inp.checked = !inp.checked;
                    this.classList.toggle('selected', inp.checked);
                } else {
                    var name = inp.name;
                    if (name) {
                        document.querySelectorAll('.option-card input[name="' + name + '"]').forEach(function(i) {
                            i.checked = false;
                        });
                        inp.checked = true;
                        inp.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
                scheduleAutoSave();
            });
        });

        document.querySelectorAll('input[name="has_materials"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                var details = document.querySelector('.materials-details');
                if (details) details.style.display = this.value === 'yes' ? 'block' : 'none';
            });
        });

        var materialsInput = document.getElementById('materials_files');
        if (materialsInput) {
            materialsInput.addEventListener('change', function() {
                var preview = document.getElementById('materials_files_preview');
                if (!preview) return;
                var files = this.files;
                if (files.length === 0) { preview.innerHTML = ''; return; }
                var html = [];
                for (var i = 0; i < files.length; i++) {
                    html.push('<span class="tw-mr-2"><i class="fa-solid fa-file"></i> ' + (files[i].name || 'file') + '</span>');
                }
                preview.innerHTML = html.join('');
            });
        }

        document.querySelectorAll('input[name="greeting_preset"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                var wrap = document.querySelector('.greeting-custom-wrap');
                if (wrap) wrap.style.display = this.value === 'custom' ? 'block' : 'none';
                if (this.value !== 'custom') {
                    var ta = document.getElementById('greeting');
                    if (ta) ta.value = '';
                }
            });
        });

        document.querySelectorAll('.capability-card').forEach(function(card) {
            card.addEventListener('click', function() {
                var cb = this.querySelector('input[type="checkbox"]');
                if (cb) {
                    cb.checked = !cb.checked;
                    this.classList.toggle('selected', cb.checked);
                    scheduleAutoSave();
                }
            });
        });

        document.querySelectorAll('.flow-card').forEach(function(card) {
            card.addEventListener('click', function() {
                var radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    document.querySelectorAll('.flow-card').forEach(function(c) { c.classList.remove('selected'); });
                    this.classList.add('selected');
                    scheduleAutoSave();
                }
            });
        });

        document.querySelectorAll('.mandatory-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                var cb = this.querySelector('input[type="checkbox"]');
                if (cb) {
                    cb.checked = !cb.checked;
                    this.classList.toggle('selected', cb.checked);
                    scheduleAutoSave();
                }
            });
        });

        initMasks();
        initCepLookup();
        initHoursClosed();
        initServicesList();
    }

    function initAutoSave() {
        var form = document.getElementById('onboardingForm');
        if (!form) return;

        form.addEventListener('focusout', function(e) {
            if (e.target.matches('input, textarea, select') && !e.target.matches('[type="file"]')) {
                scheduleAutoSave();
            }
        });

        form.addEventListener('change', function(e) {
            if (e.target.matches('input, textarea, select') && !e.target.matches('[type="file"]')) {
                scheduleAutoSave();
            }
        });

        form.addEventListener('input', function(e) {
            if (e.target.matches('input, textarea') && !e.target.matches('[type="file"]') && !e.target.matches('[type="hidden"]')) {
                scheduleAutoSave();
            }
        });
    }

    function scheduleAutoSave() {
        if (autoSaveTimer) clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function() {
            autoSaveTimer = null;
            performAutoSave();
        }, AUTO_SAVE_DEBOUNCE_MS);
    }

    function showSavingIndicator() {
        var existing = document.getElementById('autoSaveIndicator');
        if (existing) existing.remove();
        var el = document.createElement('span');
        el.id = 'autoSaveIndicator';
        el.className = 'autosave-indicator autosave-saving';
        el.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + (typeof LANG_SAVING !== 'undefined' ? LANG_SAVING : 'Saving...');
        var footer = document.querySelector('.wizard-footer');
        if (footer) footer.appendChild(el);
    }

    function showFailedIndicator() {
        var existing = document.getElementById('autoSaveIndicator');
        if (existing) existing.remove();
        var el = document.createElement('span');
        el.id = 'autoSaveIndicator';
        el.className = 'autosave-indicator autosave-failed';
        el.innerHTML = '<i class="fa-solid fa-exclamation-triangle"></i> ' + (typeof LANG_SAVE_FAILED !== 'undefined' ? LANG_SAVE_FAILED : 'Save failed');
        var footer = document.querySelector('.wizard-footer');
        if (footer) {
            footer.appendChild(el);
            setTimeout(function() { el.remove(); }, 4000);
        }
    }

    function performAutoSave() {
        var data = collectFormData();
        var formData = new FormData();
        formData.append('token', typeof FORM_TOKEN !== 'undefined' ? FORM_TOKEN : '');
        formData.append('form_data', JSON.stringify(data));

        showSavingIndicator();
        var xhr = new XMLHttpRequest();
        var url = (typeof SITE_URL !== 'undefined' ? SITE_URL : '') + '/contactcenter/assistant_form_public/save';
        xhr.open('POST', url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.timeout = 15000;
        xhr.onload = function() {
            try {
                if (xhr.status !== 200) {
                    showFailedIndicator();
                    return;
                }
                var res = JSON.parse(xhr.responseText);
                if (res.success) showSavedIndicator();
                else showFailedIndicator();
            } catch (e) {
                showFailedIndicator();
            }
        };
        xhr.onerror = function() { showFailedIndicator(); };
        xhr.ontimeout = function() { showFailedIndicator(); };
        xhr.send(formData);
    }

    function performAutoSaveSync() {
        return new Promise(function(resolve) {
            var data = collectFormData();
            var formData = new FormData();
            formData.append('token', typeof FORM_TOKEN !== 'undefined' ? FORM_TOKEN : '');
            formData.append('form_data', JSON.stringify(data));

            showSavingIndicator();
            var xhr = new XMLHttpRequest();
            var url = (typeof SITE_URL !== 'undefined' ? SITE_URL : '') + '/contactcenter/assistant_form_public/save';
            xhr.open('POST', url);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.timeout = 15000;
            xhr.onload = function() {
                try {
                    if (xhr.status === 200) {
                        var res = JSON.parse(xhr.responseText);
                        if (res.success) showSavedIndicator();
                        else showFailedIndicator();
                    } else showFailedIndicator();
                } catch (e) { showFailedIndicator(); }
                resolve();
            };
            xhr.onerror = function() { showFailedIndicator(); resolve(); };
            xhr.ontimeout = function() { showFailedIndicator(); resolve(); };
            xhr.send(formData);
        });
    }

    function showSavedIndicator() {
        var existing = document.getElementById('autoSaveIndicator');
        if (existing) existing.remove();
        var el = document.createElement('span');
        el.id = 'autoSaveIndicator';
        el.className = 'autosave-indicator';
        el.innerHTML = '<i class="fa-solid fa-check"></i> ' + (typeof LANG_SAVED !== 'undefined' ? LANG_SAVED : 'Saved');
        var footer = document.querySelector('.wizard-footer');
        if (footer) {
            footer.appendChild(el);
            setTimeout(function() {
                el.classList.add('fade-out');
                setTimeout(function() { el.remove(); }, 300);
            }, 2000);
        }
    }

    function initHoursClosed() {
        function setupClosed(closedId, fromId, toId) {
            var closed = document.getElementById(closedId);
            var fromEl = document.getElementById(fromId);
            var toEl = document.getElementById(toId);
            if (!closed || !fromEl || !toEl) return;
            function update() {
                var disabled = closed.checked;
                fromEl.disabled = disabled;
                toEl.disabled = disabled;
            }
            closed.addEventListener('change', update);
            update();
        }
        setupClosed('hours_sat_closed', 'hours_sat_from', 'hours_sat_to');
        setupClosed('hours_sun_closed', 'hours_sun_from', 'hours_sun_to');
    }

    function initServicesList() {
        var btn = document.getElementById('btnAddService');
        var list = document.getElementById('servicesList');
        if (!btn || !list) return;

        btn.addEventListener('click', function() {
            var row = document.createElement('div');
            row.className = 'service-row';
            row.innerHTML = '<input type="text" name="service_name[]" placeholder="' + (typeof LANG_SERVICE_NAME !== 'undefined' ? LANG_SERVICE_NAME : 'Service name') + '" class="service-name-input">' +
                '<input type="text" name="service_price[]" placeholder="' + (typeof LANG_SERVICE_PRICE !== 'undefined' ? LANG_SERVICE_PRICE : 'Price') + '" class="service-price-input">' +
                '<button type="button" class="btn-remove-service" aria-label="Remove"><i class="fa-solid fa-times"></i></button>';
            var removeBtn = row.querySelector('.btn-remove-service');
            removeBtn.addEventListener('click', function() { row.remove(); });
            list.appendChild(row);
        });
    }

    function initMasks() {
        var cepEl = document.getElementById('cep');
        if (cepEl) {
            cepEl.addEventListener('input', function() {
                var v = this.value.replace(/\D/g, '');
                if (v.length > 5) v = v.slice(0, 5) + '-' + v.slice(5, 8);
                this.value = v;
            });
        }
        var phoneEl = document.getElementById('phone');
        if (phoneEl) {
            phoneEl.addEventListener('input', function() {
                var v = this.value.replace(/\D/g, '');
                if (v.length > 11) v = v.slice(0, 11);
                if (v.length <= 2) {
                    this.value = v ? '(' + v : '';
                } else if (v.length <= 6) {
                    this.value = '(' + v.slice(0, 2) + ') ' + v.slice(2);
                } else if (v.length <= 10) {
                    this.value = '(' + v.slice(0, 2) + ') ' + v.slice(2, 6) + '-' + v.slice(6);
                } else {
                    this.value = '(' + v.slice(0, 2) + ') ' + v.slice(2, 7) + '-' + v.slice(7, 11);
                }
            });
        }
    }

    function initCepLookup() {
        var cepEl = document.getElementById('cep');
        var addressEl = document.getElementById('address');
        var loadingEl = document.getElementById('cepLoading');
        if (!cepEl || !addressEl) return;

        cepEl.addEventListener('blur', function() {
            var cep = this.value.replace(/\D/g, '');
            if (cep.length !== 8) return;

            if (loadingEl) loadingEl.style.display = 'inline';
            fetch('https://brasilapi.com.br/api/cep/v1/' + cep)
                .then(function(r) {
                    if (!r.ok) return null;
                    return r.json();
                })
                .then(function(data) {
                    if (data && (data.street || data.city)) {
                        var parts = [];
                        if (data.street) parts.push(data.street);
                        if (data.neighborhood) parts.push(data.neighborhood);
                        if (data.city && data.state) parts.push(data.city + ' - ' + data.state);
                        else if (data.city) parts.push(data.city);
                        addressEl.value = parts.join(', ');
                    }
                })
                .catch(function() {})
                .finally(function() {
                    if (loadingEl) loadingEl.style.display = 'none';
                });
        });
    }

    function goNext() {
        if (!validateStep(currentStep)) return;
        performAutoSaveSync().then(function() {
            if (currentStep < totalSteps) {
                steps[currentStep - 1].classList.remove('active');
                currentStep++;
                steps[currentStep - 1].classList.add('active');
                updateUI();
                document.querySelector('.wizard-content').scrollTop = 0;
            }
        });
    }

    function goPrev() {
        if (currentStep > 1) {
            steps[currentStep - 1].classList.remove('active');
            currentStep--;
            steps[currentStep - 1].classList.add('active');
            updateUI();
            document.querySelector('.wizard-content').scrollTop = 0;
        }
    }

    function validateStep(step) {
        var section = document.querySelector('.wizard-step[data-step="' + step + '"]');
        if (!section) return true;
        if (step === 4) {
            var preset = section.querySelector('input[name="greeting_preset"]:checked');
            if (!preset) return true;
            if (preset.value === 'custom') {
                var greeting = document.getElementById('greeting');
                if (greeting && (!greeting.value || !greeting.value.trim())) {
                    greeting.focus();
                    greeting.style.borderColor = '#e74c3c';
                    setTimeout(function() { greeting.style.borderColor = ''; }, 2000);
                    return false;
                }
            }
            return true;
        }
        var required = section.querySelectorAll('[required]');
        for (var i = 0; i < required.length; i++) {
            var el = required[i];
            if (!el.value || !el.value.trim()) {
                el.focus();
                el.style.borderColor = '#e74c3c';
                setTimeout(function() { el.style.borderColor = ''; }, 2000);
                return false;
            }
        }
        return true;
    }

    function updateUI() {
        var progress = (currentStep / totalSteps) * 100;
        var progressFill = document.getElementById('progressFill');
        if (progressFill) progressFill.style.width = progress + '%';

        document.querySelectorAll('.step-dot').forEach(function(dot, i) {
            var stepNum = i + 1;
            dot.classList.remove('active', 'completed');
            if (stepNum === currentStep) dot.classList.add('active');
            else if (stepNum < currentStep) dot.classList.add('completed');
        });

        var btnPrev = document.getElementById('btnPrev');
        var btnNext = document.getElementById('btnNext');
        var btnSubmit = document.getElementById('btnSubmit');

        if (btnPrev) btnPrev.disabled = currentStep <= 1;
        if (btnNext) btnNext.style.display = currentStep < totalSteps ? 'inline-flex' : 'none';
        if (btnSubmit) btnSubmit.style.display = currentStep === totalSteps ? 'inline-flex' : 'none';

        document.querySelectorAll('.capability-card').forEach(function(card) {
            var cb = card.querySelector('input[type="checkbox"]');
            card.classList.toggle('selected', cb && cb.checked);
        });
        document.querySelectorAll('.objective-card').forEach(function(card) {
            var cb = card.querySelector('input[type="checkbox"]');
            card.classList.toggle('selected', cb && cb.checked);
        });
        document.querySelectorAll('.faq-card').forEach(function(card) {
            var cb = card.querySelector('input[type="checkbox"]');
            card.classList.toggle('selected', cb && cb.checked);
        });
        document.querySelectorAll('.flow-card').forEach(function(card) {
            var radio = card.querySelector('input[type="radio"]');
            card.classList.toggle('selected', radio && radio.checked);
        });
        document.querySelectorAll('.char-card').forEach(function(card) {
            var cb = card.querySelector('input[type="checkbox"]');
            card.classList.toggle('selected', cb && cb.checked);
        });
        document.querySelectorAll('.mandatory-chip').forEach(function(chip) {
            var cb = chip.querySelector('input[type="checkbox"]');
            chip.classList.toggle('selected', cb && cb.checked);
        });
    }

    function collectFormData() {
        var form = document.getElementById('onboardingForm');
        if (!form) return {};
        var fd = new FormData(form);
        var data = {};
        fd.forEach(function(val, key) {
            if (key === 'token') return;
            if (key === 'materials_files[]') return;
            if (key === 'functions[]') {
                if (!data.functions) data.functions = [];
                data.functions.push(val);
            } else if (key === 'assistant_characteristics[]') {
                if (!data.assistant_characteristics) data.assistant_characteristics = [];
                data.assistant_characteristics.push(val);
            } else if (key === 'objective[]') {
                if (!data.objective) data.objective = [];
                data.objective.push(val);
            } else if (key === 'faq_preset[]') {
                if (!data.faq_preset) data.faq_preset = [];
                data.faq_preset.push(val);
            } else if (key === 'mandatory_info[]') {
                if (!data.mandatory_info) data.mandatory_info = [];
                data.mandatory_info.push(val);
            } else if (key === 'service_type[]') {
                if (!data.service_type) data.service_type = [];
                data.service_type.push(val);
            } else if (key === 'escalation_triggers[]') {
                if (!data.escalation_triggers) data.escalation_triggers = [];
                data.escalation_triggers.push(val);
            } else if (key === 'decision_criteria[]') {
                if (!data.decision_criteria) data.decision_criteria = [];
                data.decision_criteria.push(val);
            } else {
                data[key] = val;
            }
        });
        var preset = form.querySelector('input[name="greeting_preset"]:checked');
        if (preset) {
            data.greeting_preset = preset.value;
            if (preset.value === 'custom') {
                var ta = document.getElementById('greeting');
                data.greeting = ta ? ta.value.trim() : '';
            } else {
                data.greeting = preset.getAttribute('data-text') || '';
            }
        }
        delete data.greeting_preset;
        var faqParts = [];
        var faqPresets = form.querySelectorAll('input[name="faq_preset[]"]:checked');
        faqPresets.forEach(function(inp) {
            var text = inp.getAttribute('data-text');
            if (text) faqParts.push(text);
        });
        var faqCustom = document.getElementById('faq');
        if (faqCustom && faqCustom.value.trim()) {
            faqCustom.value.trim().split(/\n/).forEach(function(line) {
                if (line.trim()) faqParts.push(line.trim());
            });
        }
        data.faq = faqParts.join('\n');
        delete data.faq_preset;

        var servicesList = collectServicesList();
        if (servicesList.length) data.services_list = servicesList;

        data.business_hours = buildBusinessHours();
        var greetingPreset = form.querySelector('input[name="greeting_preset"]:checked');
        data._restore = {
            current_step: currentStep,
            greeting_preset: greetingPreset ? greetingPreset.value : '',
            hours_weekdays_from: (document.getElementById('hours_weekdays_from') || {}).value || '',
            hours_weekdays_to: (document.getElementById('hours_weekdays_to') || {}).value || '',
            hours_sat_from: (document.getElementById('hours_sat_from') || {}).value || '',
            hours_sat_to: (document.getElementById('hours_sat_to') || {}).value || '',
            hours_sat_closed: document.getElementById('hours_sat_closed') ? document.getElementById('hours_sat_closed').checked : false,
            hours_sun_from: (document.getElementById('hours_sun_from') || {}).value || '',
            hours_sun_to: (document.getElementById('hours_sun_to') || {}).value || '',
            hours_sun_closed: document.getElementById('hours_sun_closed') ? document.getElementById('hours_sun_closed').checked : false,
            hours_notes: data.hours_notes || ''
        };
        ['hours_weekdays_from', 'hours_weekdays_to', 'hours_sat_from', 'hours_sat_to', 'hours_sun_from', 'hours_sun_to', 'hours_sat_closed', 'hours_sun_closed'].forEach(function(k) { delete data[k]; });
        if (data.hours_notes) {
            data.business_hours = (data.business_hours ? data.business_hours + '. ' : '') + data.hours_notes;
        }
        delete data.hours_notes;

        return data;
    }

    function buildBusinessHours() {
        var parts = [];
        var wFrom = document.getElementById('hours_weekdays_from');
        var wTo = document.getElementById('hours_weekdays_to');
        if (wFrom && wTo && wFrom.value && wTo.value) {
            parts.push('Mon-Fri ' + formatTime(wFrom.value) + '-' + formatTime(wTo.value));
        }
        var satClosed = document.getElementById('hours_sat_closed');
        if (satClosed && !satClosed.checked) {
            var sFrom = document.getElementById('hours_sat_from');
            var sTo = document.getElementById('hours_sat_to');
            if (sFrom && sTo && sFrom.value && sTo.value) parts.push('Sat ' + formatTime(sFrom.value) + '-' + formatTime(sTo.value));
        } else if (satClosed && satClosed.checked) {
            parts.push('Sat closed');
        }
        var sunClosed = document.getElementById('hours_sun_closed');
        if (sunClosed && !sunClosed.checked) {
            var uFrom = document.getElementById('hours_sun_from');
            var uTo = document.getElementById('hours_sun_to');
            if (uFrom && uTo && uFrom.value && uTo.value) parts.push('Sun ' + formatTime(uFrom.value) + '-' + formatTime(uTo.value));
        } else if (sunClosed && sunClosed.checked) {
            parts.push('Sun closed');
        }
        return parts.join('; ');
    }

    function restoreSavedData() {
        var saved = typeof SAVED_FORM_DATA !== 'undefined' && SAVED_FORM_DATA ? SAVED_FORM_DATA : null;
        if (!saved || typeof saved !== 'object') return;

        var restore = saved._restore || {};
        var form = document.getElementById('onboardingForm');
        if (!form) return;

        function setVal(id, val) {
            if (!id || val === undefined || val === null) return;
            var el = document.getElementById(id) || form.querySelector('[name="' + id + '"]');
            if (el) el.value = val;
        }
        function setRadio(name, val) {
            if (!name || !val) return;
            var el = form.querySelector('input[name="' + name + '"][value="' + val + '"]');
            if (el) {
                el.checked = true;
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
        function setCheckboxes(name, vals) {
            if (!name || !Array.isArray(vals)) return;
            form.querySelectorAll('input[name="' + name + '"]').forEach(function(cb) {
                cb.checked = vals.indexOf(cb.value) !== -1;
            });
        }

        setVal('company_name', saved.company_name);
        setVal('company_info', saved.company_info);
        setRadio('tone', saved.tone);
        setVal('assistant_name', saved.assistant_name);
        setCheckboxes('assistant_characteristics[]', saved.assistant_characteristics || []);
        setVal('assistant_characteristics_notes', saved.assistant_characteristics_notes);
        setRadio('greeting_preset', restore.greeting_preset || saved.greeting_preset);
        if (saved.greeting) setVal('greeting', saved.greeting);
        setCheckboxes('objective[]', saved.objective || []);
        setVal('objective_notes', saved.objective_notes);
        setVal('faq', saved.faq);
        setRadio('has_materials', saved.has_materials);
        setVal('materials_description', saved.materials_description);
        setRadio('flow_template', saved.flow_template);
        setCheckboxes('mandatory_info[]', saved.mandatory_info || []);
        setVal('opening_questions', saved.opening_questions);
        setVal('service_sequence', saved.service_sequence || saved.flow_notes);
        setVal('services_extra', saved.services_extra);
        setCheckboxes('service_type[]', saved.service_type || []);
        setCheckboxes('escalation_triggers[]', saved.escalation_triggers || []);
        setCheckboxes('decision_criteria[]', saved.decision_criteria || []);
        setVal('cep', saved.cep);
        setVal('address', saved.address);
        setVal('address_number', saved.address_number);
        setVal('phone', saved.phone);
        setVal('email', saved.email);
        setVal('website', saved.website);
        setVal('social_media', saved.social_media);
        setVal('hours_notes', restore.hours_notes);
        setCheckboxes('functions[]', saved.functions || []);

        var h = restore;
        var wFrom = document.getElementById('hours_weekdays_from');
        var wTo = document.getElementById('hours_weekdays_to');
        if (wFrom && h.hours_weekdays_from) wFrom.value = h.hours_weekdays_from;
        if (wTo && h.hours_weekdays_to) wTo.value = h.hours_weekdays_to;
        var satClosed = document.getElementById('hours_sat_closed');
        if (satClosed) satClosed.checked = !!h.hours_sat_closed;
        var satFrom = document.getElementById('hours_sat_from');
        var satTo = document.getElementById('hours_sat_to');
        if (satFrom && h.hours_sat_from) satFrom.value = h.hours_sat_from;
        if (satTo && h.hours_sat_to) satTo.value = h.hours_sat_to;
        var sunClosed = document.getElementById('hours_sun_closed');
        if (sunClosed) sunClosed.checked = !!h.hours_sun_closed;
        var sunFrom = document.getElementById('hours_sun_from');
        var sunTo = document.getElementById('hours_sun_to');
        if (sunFrom && h.hours_sun_from) sunFrom.value = h.hours_sun_from;
        if (sunTo && h.hours_sun_to) sunTo.value = h.hours_sun_to;
        if (document.getElementById('hours_notes') && h.hours_notes) document.getElementById('hours_notes').value = h.hours_notes;
        [satClosed, sunClosed].forEach(function(el) {
            if (el) el.dispatchEvent(new Event('change', { bubbles: true }));
        });

        var svcList = saved.services_list;
        if (Array.isArray(svcList) && svcList.length > 0) {
            var list = document.getElementById('servicesList');
            var btn = document.getElementById('btnAddService');
            if (list) {
                list.innerHTML = '';
                svcList.forEach(function(item) {
                    var row = document.createElement('div');
                    row.className = 'service-row';
                    row.innerHTML = '<input type="text" name="service_name[]" placeholder="' + (typeof LANG_SERVICE_NAME !== 'undefined' ? LANG_SERVICE_NAME : '') + '" class="service-name-input" value="' + (item.name || '').replace(/"/g, '&quot;') + '">' +
                        '<input type="text" name="service_price[]" placeholder="' + (typeof LANG_SERVICE_PRICE !== 'undefined' ? LANG_SERVICE_PRICE : '') + '" class="service-price-input" value="' + (item.price || '').replace(/"/g, '&quot;') + '">' +
                        '<button type="button" class="btn-remove-service" aria-label="Remove"><i class="fa-solid fa-times"></i></button>';
                    var removeBtn = row.querySelector('.btn-remove-service');
                    if (removeBtn) removeBtn.addEventListener('click', function() { row.remove(); });
                    list.appendChild(row);
                });
            }
        }

        var materialsDetails = document.querySelector('.materials-details');
        if (saved.has_materials === 'yes' && materialsDetails) materialsDetails.style.display = 'block';
        var greetingWrap = document.querySelector('.greeting-custom-wrap');
        if (restore.greeting_preset === 'custom' && greetingWrap) greetingWrap.style.display = 'block';

        if (restore.current_step && restore.current_step >= 1 && restore.current_step <= totalSteps) {
            currentStep = restore.current_step;
            steps.forEach(function(s, i) {
                s.classList.remove('active');
                if (i + 1 === currentStep) s.classList.add('active');
            });
        }
    }

    function formatTime(t) {
        if (!t) return '';
        var m = t.match(/^(\d{1,2}):(\d{2})$/);
        if (!m) return t;
        var h = parseInt(m[1], 10);
        var min = m[2];
        if (min === '00') return h + 'h';
        return h + ':' + min;
    }

    function collectServicesList() {
        var list = [];
        document.querySelectorAll('.service-row').forEach(function(row) {
            var nameInp = row.querySelector('input[name="service_name[]"]');
            var priceInp = row.querySelector('input[name="service_price[]"]');
            var name = nameInp ? nameInp.value.trim() : '';
            if (name) {
                list.push({ name: name, price: priceInp ? priceInp.value.trim() : '' });
            }
        });
        return list;
    }

    function submitForm() {
        var data = collectFormData();
        var btn = document.getElementById('btnSubmit');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + (typeof LANG_SENDING !== 'undefined' ? LANG_SENDING : 'Sending...');
        }

        var formData = new FormData();
        formData.append('token', typeof FORM_TOKEN !== 'undefined' ? FORM_TOKEN : '');
        formData.append('form_data', JSON.stringify(data));

        var materialsInput = document.getElementById('materials_files');
        if (materialsInput && materialsInput.files && materialsInput.files.length > 0) {
            for (var i = 0; i < materialsInput.files.length; i++) {
                formData.append('materials_files[]', materialsInput.files[i]);
            }
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', (typeof SITE_URL !== 'undefined' ? SITE_URL : '') + '/contactcenter/assistant_form_public/save');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function() {
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.success) {
                    document.getElementById('successModal').style.display = 'flex';
                } else {
                    alert(res.message || 'Error saving');
                    if (btn) { btn.disabled = false; btn.innerHTML = 'Submit <i class="fa-solid fa-check"></i>'; }
                }
            } catch (e) {
                alert('An error occurred');
                if (btn) { btn.disabled = false; btn.innerHTML = 'Submit <i class="fa-solid fa-check"></i>'; }
            }
        };
        xhr.onerror = function() {
            alert('Network error. Please try again.');
            if (btn) { btn.disabled = false; btn.innerHTML = 'Submit <i class="fa-solid fa-check"></i>'; }
        };
        xhr.send(formData);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
