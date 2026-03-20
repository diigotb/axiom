/**
 * AXIOM Admin JS
 * Greeting (GMT-3), sidebar toggle, weather, avatar dropdown, dashboard customization
 */
(function () {
    'use strict';

    // -------------------------------------------------------
    // Greeting (GMT-3)
    // -------------------------------------------------------
    function axiomGreeting() {
        var el = document.getElementById('axiom-greeting-text');
        if (!el) return;
        var now = new Date();
        var utc = now.getTime() + now.getTimezoneOffset() * 60000;
        var brt = new Date(utc - 3 * 3600000);
        var h = brt.getHours();
        var label = h >= 5 && h < 12 ? 'Bom dia' : h >= 12 && h < 18 ? 'Boa tarde' : 'Boa noite';
        var nameEl = document.getElementById('axiom-greeting-name');
        var name = nameEl ? nameEl.textContent.trim() : '';
        el.textContent = label + (name ? ', ' + name : '') + ' 👋';
    }

    // -------------------------------------------------------
    // Sidebar toggle + localStorage
    // -------------------------------------------------------
    function axiomInitSidebar() {
        var sidebar = document.querySelector('.sidebar-nav');
        var wrapper = document.querySelector('#wrapper') || document.body;
        var STORAGE_KEY = 'axiom_sidebar_collapsed';

        // Restore state
        if (localStorage.getItem(STORAGE_KEY) === '1') {
            document.body.classList.add('axiom-collapsed');
            if (sidebar) sidebar.classList.add('axiom-collapsed');
            if (wrapper) wrapper.classList.add('axiom-collapsed');
        }

        var btn = document.getElementById('axiom-sidebar-toggle');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var collapsed = document.body.classList.toggle('axiom-collapsed');
            if (sidebar) sidebar.classList.toggle('axiom-collapsed', collapsed);
            if (wrapper) wrapper.classList.toggle('axiom-collapsed', collapsed);
            localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
        });
    }

    // -------------------------------------------------------
    // Weather widget
    // -------------------------------------------------------
    function axiomLoadWeather() {
        var el = document.getElementById('axiom-weather');
        if (!el) return;

        var cache = sessionStorage.getItem('axiom_weather_v2');
        if (cache) {
            try { renderWeatherAdmin(el, JSON.parse(cache)); return; } catch (e) {}
        }

        function fetchByCoords(lat, lon) {
            fetch('https://api.open-meteo.com/v1/forecast?latitude=' + lat + '&longitude=' + lon + '&current_weather=true')
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    var wc = d.current_weather.weathercode;
                    var icons = {0:'fa-sun-o',1:'fa-sun-o',2:'fa-cloud',3:'fa-cloud',61:'fa-umbrella',80:'fa-umbrella',95:'fa-bolt'};
                    var icon = icons[wc] || 'fa-cloud';
                    var temp = Math.round(d.current_weather.temperature);
                    var payload = {temp: temp, icon: icon, city: ''};
                    fetch('https://nominatim.openstreetmap.org/reverse?lat=' + lat + '&lon=' + lon + '&format=json')
                        .then(function (r) { return r.json(); })
                        .then(function (g) {
                            payload.city = g.address.city || g.address.town || g.address.village || '';
                            sessionStorage.setItem('axiom_weather_v2', JSON.stringify(payload));
                            renderWeatherAdmin(el, payload);
                        })
                        .catch(function () { renderWeatherAdmin(el, payload); });
                })
                .catch(function () { fetchByIP(); });
        }

        function fetchByIP() {
            fetch('https://wttr.in/?format=j1')
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    var cur = data.current_condition[0];
                    var wc = parseInt(cur.weatherCode, 10);
                    var icon = wc === 113 ? 'fa-sun-o' : wc <= 176 ? 'fa-cloud' : wc <= 299 ? 'fa-umbrella' : wc <= 399 ? 'fa-tint' : 'fa-cloud';
                    var nearest = (data.nearest_area && data.nearest_area[0] && data.nearest_area[0].areaName) ? data.nearest_area[0].areaName[0].value : '';
                    var payload = {temp: cur.temp_C, icon: icon, city: nearest};
                    sessionStorage.setItem('axiom_weather_v2', JSON.stringify(payload));
                    renderWeatherAdmin(el, payload);
                })
                .catch(function () { el.innerHTML = '<i class="fa fa-cloud"></i>'; });
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (pos) { fetchByCoords(pos.coords.latitude.toFixed(4), pos.coords.longitude.toFixed(4)); },
                function () { fetchByIP(); },
                {timeout: 5000}
            );
        } else {
            fetchByIP();
        }
    }

    function renderWeatherAdmin(el, d) {
        el.innerHTML = '<i class="fa ' + d.icon + '"></i> ' + d.temp + '°C'
            + (d.city ? ' <span style="font-size:10px;opacity:.7">' + d.city + '</span>' : '');
    }

    // -------------------------------------------------------
    // Avatar dropdown
    // -------------------------------------------------------
    function axiomInitAvatarDropdown() {
        var trigger = document.getElementById('axiom-avatar-trigger');
        var menu = document.getElementById('axiom-avatar-menu');
        if (!trigger || !menu) return;

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            menu.classList.toggle('axiom-open');
        });

        document.addEventListener('click', function () {
            menu.classList.remove('axiom-open');
        });
    }

    // -------------------------------------------------------
    // Avatar upload — preview + AJAX
    // -------------------------------------------------------
    function axiomInitAvatarUpload() {
        var input = document.getElementById('axiom-avatar-file-input');
        var preview = document.querySelector('.axiom-avatar-preview');
        if (!input) return;

        input.addEventListener('change', function () {
            var file = input.files[0];
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function (e) {
                // Update all avatar images on page
                document.querySelectorAll('.axiom-avatar-img, .axiom-topbar-avatar').forEach(function (img) {
                    img.src = e.target.result;
                });
            };
            reader.readAsDataURL(file);

            // AJAX upload
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            var csrfName = document.querySelector('meta[name="csrf-param"]') || {};
            var formData = new FormData();
            formData.append('avatar', file);
            if (csrfMeta) {
                formData.append(csrfName.content || 'csrf_axiom_token', csrfMeta.content);
            }
            fetch(window.AXIOM_BASE_URL + 'admin/axiomchannel/upload_avatar', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData,
            })
                .then(function (r) { return r.json(); })
                .then(function (resp) {
                    if (!resp.success) {
                        console.warn('Avatar upload failed:', resp.message);
                    }
                })
                .catch(function (err) {
                    console.warn('Avatar upload error:', err);
                });
        });

        // Trigger file picker when clicking avatar area
        var btn = document.getElementById('axiom-avatar-change-btn');
        if (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                input.click();
            });
        }
    }

    // -------------------------------------------------------
    // Dashboard customization modal
    // -------------------------------------------------------
    function axiomInitDashboardCustomize() {
        var openBtn = document.getElementById('axiom-customize-btn');
        var modal = document.getElementById('axiom-customize-modal');
        var closeBtn = document.getElementById('axiom-customize-close');
        var saveBtn = document.getElementById('axiom-customize-save');
        if (!openBtn || !modal) return;

        openBtn.addEventListener('click', function () {
            modal.style.display = 'flex';
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                modal.style.display = 'none';
            });
        }

        modal.addEventListener('click', function (e) {
            if (e.target === modal) modal.style.display = 'none';
        });

        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                var prefs = {};
                modal.querySelectorAll('[data-pref]').forEach(function (el) {
                    prefs[el.dataset.pref] = el.type === 'checkbox' ? el.checked : el.value;
                });
                var csrfToken = document.querySelector('meta[name="csrf-token"]');
                var csrfParam = document.querySelector('meta[name="csrf-param"]');
                var body = { preferences: prefs };
                if (csrfToken && csrfParam) {
                    body[csrfParam.content] = csrfToken.content;
                }
                fetch(window.AXIOM_BASE_URL + 'admin/axiomchannel/save_dashboard_preferences', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(body),
                })
                    .then(function (r) { return r.json(); })
                    .then(function (resp) {
                        if (resp.success) {
                            modal.style.display = 'none';
                            axiomShowToast('Preferências salvas!');
                        }
                    })
                    .catch(function (err) { console.warn(err); });
            });
        }
    }

    // -------------------------------------------------------
    // Toast notification
    // -------------------------------------------------------
    function axiomShowToast(msg, type) {
        var toast = document.createElement('div');
        toast.className = 'axiom-toast' + (type === 'error' ? ' axiom-toast-error' : '');
        toast.textContent = msg;
        toast.style.cssText =
            'position:fixed;bottom:24px;right:24px;background:' +
            (type === 'error' ? '#E53E3E' : '#2D7A6B') +
            ';color:#fff;padding:10px 18px;border-radius:8px;font-size:13px;z-index:99999;' +
            'box-shadow:0 4px 16px rgba(0,0,0,.3);animation:axiomFadeIn .2s ease;';
        document.body.appendChild(toast);
        setTimeout(function () { toast.remove(); }, 3000);
    }
    window.axiomShowToast = axiomShowToast;

    // -------------------------------------------------------
    // Dashboard metrics refresh
    // -------------------------------------------------------
    function axiomRefreshMetrics() {
        fetch(window.AXIOM_BASE_URL + 'admin/axiomchannel/get_dashboard_metrics', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                Object.keys(data).forEach(function (key) {
                    var el = document.getElementById('axiom-metric-' + key);
                    if (el) el.textContent = data[key];
                });
            })
            .catch(function () {});
    }

    // -------------------------------------------------------
    // Init
    // -------------------------------------------------------
    function axiomInit() {
        // body.axiom-admin já adicionado via PHP no after_body_start
        axiomGreeting();
        axiomInitSidebar();
        axiomLoadWeather();
        axiomInitAvatarDropdown();
        axiomInitAvatarUpload();
        axiomInitDashboardCustomize();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', axiomInit);
    } else {
        axiomInit();
    }
})();
