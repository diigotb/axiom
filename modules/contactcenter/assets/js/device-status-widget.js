/*
 * Device Status Widget for Header
 * Injects widget after search bar to show disconnected devices
 */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    $(document).ready(function() {
        // Wait a bit for header to fully render and check if data is available
        var attempts = 0;
        var maxAttempts = 50; // Try for 5 seconds (50 * 100ms)
        
        var checkInterval = setInterval(function() {
            attempts++;
            if (typeof window.contactcenterDeviceStatus !== 'undefined' || 
                ($('#top_search').length > 0 && attempts > 2)) {
                clearInterval(checkInterval);
                setTimeout(function() {
                    injectDeviceStatusWidget();
                }, 200);
            } else if (attempts >= maxAttempts) {
                clearInterval(checkInterval);
                // Try to fetch data via AJAX if not available
                fetchDeviceStatusAndInject();
            }
        }, 100);
    });
    
    function fetchDeviceStatusAndInject() {
        var url_contactcenter = (typeof site_url !== 'undefined' ? site_url : '') + 'admin/contactcenter/';
        $.ajax({
            url: url_contactcenter + 'get_device_status_header',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response && response.success && response.data) {
                    window.contactcenterDeviceStatus = response.data;
                } else {
                    window.contactcenterDeviceStatus = {
                        totalDisconnected: 0,
                        myDisconnected: 0,
                        devices: [],
                        myDevices: []
                    };
                }
                injectDeviceStatusWidget();
            },
            error: function() {
                // Fallback to empty data
                window.contactcenterDeviceStatus = {
                    totalDisconnected: 0,
                    myDisconnected: 0,
                    devices: [],
                    myDevices: []
                };
                injectDeviceStatusWidget();
            }
        });
    }
    
    function injectDeviceStatusWidget() {
        // Check if widget already exists
        if ($('.device-status-widget-container').length > 0) {
            return;
        }
        
        // Find the quick create menu (the ul.nav.navbar-nav that contains quick create)
        var $quickCreateMenu = $('ul.nav.navbar-nav.visible-md.visible-lg');
        if ($quickCreateMenu.length === 0) {
            console.log('Device Status Widget: Quick create menu not found');
            return;
        }
        
        // Get device status data
        var deviceData = window.contactcenterDeviceStatus || {
            totalDisconnected: 0,
            myDisconnected: 0,
            devices: [],
            myDevices: []
        };
        
        // Configurable maximum number of device icons to show (before "more devices" indicator)
        var maxDeviceIcons = (typeof window.contactcenterDeviceMaxIcons !== 'undefined' && window.contactcenterDeviceMaxIcons > 0) 
            ? window.contactcenterDeviceMaxIcons 
            : 3; // Default to 3
        
        // Create widget HTML container
        var widgetHTML = '<div class="device-status-widget-container tw-flex tw-items-center tw-gap-3 ltr:tw-ml-6 rtl:tw-mr-6">';
        
        if (deviceData.totalDisconnected > 0) {
            // Show individual icons for each disconnected device
            var allDisconnectedDevices = [];
            
            // Add my devices first
            if (deviceData.myDevices && deviceData.myDevices.length > 0) {
                allDisconnectedDevices = allDisconnectedDevices.concat(deviceData.myDevices);
            }
            
            // Add other devices (admin only)
            if (deviceData.devices && deviceData.devices.length > 0) {
                deviceData.devices.forEach(function(device) {
                    // Skip if already in my devices
                    if (!deviceData.myDevices || !deviceData.myDevices.some(function(d) { return d.dev_id === device.dev_id; })) {
                        allDisconnectedDevices.push(device);
                    }
                });
            }
            
            // Limit to maxDeviceIcons devices for header display (3 by default)
            var devicesToShow = allDisconnectedDevices.slice(0, maxDeviceIcons);
            var remainingDevices = allDisconnectedDevices.slice(maxDeviceIcons);
            var hasMoreDevices = remainingDevices.length > 0;
            
            // Create individual pulsing icons for each device (max 3, then "More Devices" icon)
            devicesToShow.forEach(function(device, index) {
                var isMyDevice = deviceData.myDevices && deviceData.myDevices.some(function(d) { return d.dev_id === device.dev_id; });
                var deviceNameFallback = getTranslation('device', 'Device');
                var deviceTitle = escapeHtml(device.dev_name || deviceNameFallback) + ' - ' + escapeHtml(device.dev_number || '');
                if (!isMyDevice && device.staff_name) {
                    deviceTitle = escapeHtml(device.staff_name) + ' - ' + deviceTitle;
                }
                
                // Get device color (default to danger for my devices, warning for others)
                var deviceColor = device.device_color || (isMyDevice ? '#ef4444' : '#f59e0b');
                var borderColor = deviceColor + '80'; // Add transparency
                var bgColor = deviceColor + '15'; // Very light background
                
                widgetHTML += '<div class="device-status-icon-wrapper tw-relative device-pulse-animation" data-toggle="tooltip" title="' + 
                    deviceTitle + ' - ' + getTranslation('device_disconnected', 'Disconnected') + 
                    '" data-placement="bottom" data-device-id="' + device.dev_id + '">';
                widgetHTML += '<a href="#" class="device-status-toggle-small tw-inline-flex tw-items-center tw-justify-center tw-w-9 tw-h-9 tw-rounded-md tw-border-2 tw-border-solid ' + 
                    'tw-transition-colors device-icon-pulse" style="border-color: ' + borderColor + '; background: ' + bgColor + ';" ' +
                    'data-toggle="dropdown" data-device-id="' + device.dev_id + '">';
                
                // Show profile image if available, otherwise show icon
                if (device.staff_profile_image) {
                    widgetHTML += '<img src="' + escapeHtml(device.staff_profile_image) + '" ' +
                        'class="device-profile-image tw-w-full tw-h-full tw-rounded-md tw-object-cover" ' +
                        'onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';" />';
                    widgetHTML += '<i class="fa-solid fa-mobile-screen-button tw-text-sm tw-hidden" style="color: ' + deviceColor + ';"></i>';
                } else {
                    widgetHTML += '<i class="fa-solid fa-mobile-screen-button tw-text-sm" style="color: ' + deviceColor + ';"></i>';
                }
                
                widgetHTML += '</a>';
                widgetHTML += buildDeviceDropdownHTML(device, deviceData, isMyDevice);
                widgetHTML += '</div>';
            });
            
            // Add "more devices" indicator if there are more than 3 devices
            if (hasMoreDevices) {
                var moreDevicesTitle = getTranslation('more_devices', 'More devices') + ' (' + remainingDevices.length + ')';
                var moreDevicesId = 'more-devices-dropdown-' + Date.now();
                widgetHTML += '<div class="device-status-icon-wrapper tw-relative dropdown" data-toggle="tooltip" title="' + 
                    moreDevicesTitle + 
                    '" data-placement="bottom">';
                widgetHTML += '<a href="#" class="device-status-toggle-small tw-inline-flex tw-items-center tw-justify-center tw-w-9 tw-h-9 tw-rounded-md tw-border-2 tw-border-solid tw-border-white/30 tw-bg-white/10 tw-transition-colors dropdown-toggle" id="' + moreDevicesId + '-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                widgetHTML += '<i class="fa-solid fa-ellipsis" style="color: rgba(255, 255, 255, 0.9) !important; font-size: 14px;"></i>';
                widgetHTML += '</a>';
                widgetHTML += buildMoreDevicesDropdownHTML(remainingDevices, deviceData, allDisconnectedDevices, moreDevicesId);
                widgetHTML += '</div>';
            }
        } else {
            // All connected - show single green icon
            widgetHTML += '<div class="device-status-icon-wrapper tw-relative" data-toggle="tooltip" title="' + 
                getTranslation('all_devices_connected', 'All devices connected') + 
                '" data-placement="bottom">';
            widgetHTML += '<span class="tw-inline-flex tw-items-center tw-justify-center tw-w-7 tw-h-7 tw-rounded-md tw-border tw-border-solid tw-border-neutral-200/60 tw-bg-neutral-100/50">';
            widgetHTML += '<i class="fa-solid fa-mobile-screen-button tw-text-success tw-text-sm"></i>';
            widgetHTML += '</span>';
            widgetHTML += '</div>';
        }
        
        widgetHTML += '</div>';
        
        // Insert after quick create menu
        $quickCreateMenu.after(widgetHTML);
        
        // Initialize tooltips
        if (typeof $ !== 'undefined' && $.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }
        
        // Initialize Bootstrap dropdowns after a short delay to ensure DOM is ready
        setTimeout(function() {
            if (typeof $ !== 'undefined') {
                // Re-initialize all dropdowns
                $('[data-toggle="dropdown"]').each(function() {
                    var $toggle = $(this);
                    var $dropdown = $toggle.next('.dropdown-menu');
                    if ($dropdown.length > 0) {
                        // Ensure dropdown is properly associated
                        $toggle.attr('aria-haspopup', 'true');
                        $toggle.attr('aria-expanded', 'false');
                        $dropdown.attr('aria-labelledby', $toggle.attr('id') || '');
                    }
                });
            }
        }, 100);
    }
    
    function buildDeviceDropdownHTML(device, deviceData, isMyDevice) {
        var html = '<ul class="dropdown-menu dropdown-menu-right animated fadeIn device-status-dropdown width350 device-dropdown-' + device.dev_id + '">';
        html += '<li class="dropdown-header"><strong>' + 
            getTranslation('device_status', 'Device Status') + 
            '</strong></li>';
        
        html += '<li class="device-status-item">';
        if (isMyDevice) {
            html += '<a href="#" class="device-reconnect-link" data-device-id="' + device.dev_id + '" data-device-name="' + 
                escapeHtml(device.dev_name || '') + '">';
        } else {
            var chatUrl = (typeof site_url !== 'undefined' ? site_url : '') + 'admin/contactcenter/chatsingle/' + device.dev_id;
            html += '<a href="' + chatUrl + '">';
        }
        
        html += '<div class="tw-flex tw-items-center tw-justify-between tw-py-2">';
        html += '<div class="tw-flex tw-items-center tw-gap-3">';
        html += '<i class="fa-solid fa-mobile-screen-button ' + (isMyDevice ? 'tw-text-danger' : 'tw-text-warning') + '"></i>';
        html += '<div>';
        var deviceNameFallback = getTranslation('device', 'Device');
        html += '<div class="tw-font-medium">';
        if (!isMyDevice && device.staff_name) {
            html += '<span class="tw-font-medium">' + escapeHtml(device.staff_name) + '</span> - ';
        }
        html += escapeHtml(device.dev_name || deviceNameFallback) + '</div>';
        html += '<div class="tw-text-xs tw-font-medium">';
        html += escapeHtml(device.dev_number || '') + '</div>';
        html += '</div>';
        html += '</div>';
        html += '<button class="btn btn-xs ' + (isMyDevice ? 'btn-danger' : 'btn-warning') + ' device-reconnect-btn" data-device-id="' + device.dev_id + '">';
        html += '<i class="fa-solid fa-power-off"></i> ' + 
            getTranslation('reconnect', 'Reconnect');
        html += '</button>';
        html += '</div>';
        html += '</a>';
        html += '</li>';
        
        html += '</ul>';
        return html;
    }
    
    function buildMoreDevicesDropdownHTML(remainingDevices, deviceData, allDevices, dropdownId) {
        var html = '<ul class="dropdown-menu dropdown-menu-right animated fadeIn device-status-dropdown width350" id="' + dropdownId + '-menu" style="max-height: 500px; overflow-y: auto;">';
        html += '<li class="dropdown-header"><strong>' + 
            getTranslation('all_disconnected_devices', 'All Disconnected Devices') + 
            ' (' + allDevices.length + ')</strong></li>';
        
        // Add all devices to the dropdown
        allDevices.forEach(function(device) {
            var isMyDevice = deviceData.myDevices && deviceData.myDevices.some(function(d) { return d.dev_id === device.dev_id; });
            
            html += '<li class="device-status-item">';
            if (isMyDevice) {
                html += '<a href="#" class="device-reconnect-link" data-device-id="' + device.dev_id + '" data-device-name="' + 
                    escapeHtml(device.dev_name || '') + '">';
            } else {
                var chatUrl = (typeof site_url !== 'undefined' ? site_url : '') + 'admin/contactcenter/chatsingle/' + device.dev_id;
                html += '<a href="' + chatUrl + '">';
            }
            
            html += '<div class="tw-flex tw-items-center tw-justify-between tw-py-2">';
            html += '<div class="tw-flex tw-items-center tw-gap-3">';
            
            // Show profile image or icon
            if (device.staff_profile_image) {
                html += '<img src="' + escapeHtml(device.staff_profile_image) + '" ' +
                    'class="tw-w-8 tw-h-8 tw-rounded-md tw-object-cover tw-border-2" ' +
                    'style="border-color: ' + (device.device_color || (isMyDevice ? '#ef4444' : '#f59e0b')) + '80;" ' +
                    'onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\';" />';
                html += '<i class="fa-solid fa-mobile-screen-button ' + (isMyDevice ? 'tw-text-danger' : 'tw-text-warning') + ' tw-hidden"></i>';
            } else {
                html += '<i class="fa-solid fa-mobile-screen-button ' + (isMyDevice ? 'tw-text-danger' : 'tw-text-warning') + '"></i>';
            }
            
            html += '<div>';
            var deviceNameFallback = getTranslation('device', 'Device');
            html += '<div class="tw-font-medium">';
            if (!isMyDevice && device.staff_name) {
                html += '<span class="tw-font-medium">' + escapeHtml(device.staff_name) + '</span> - ';
            }
            html += escapeHtml(device.dev_name || deviceNameFallback) + '</div>';
            html += '<div class="tw-text-xs tw-font-medium">';
            html += escapeHtml(device.dev_number || '') + '</div>';
            html += '</div>';
            html += '</div>';
            html += '<button class="btn btn-xs ' + (isMyDevice ? 'btn-danger' : 'btn-warning') + ' device-reconnect-btn" data-device-id="' + device.dev_id + '">';
            html += '<i class="fa-solid fa-power-off"></i> ' + 
                getTranslation('reconnect', 'Reconnect');
            html += '</button>';
            html += '</div>';
            html += '</a>';
            html += '</li>';
        });
        
        html += '</ul>';
        return html;
    }
    
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
    
    // Translation helper function
    function getTranslation(key, fallback) {
        // First try window.contactcenterDeviceTranslations (from PHP)
        if (typeof window.contactcenterDeviceTranslations !== 'undefined' && window.contactcenterDeviceTranslations[key]) {
            return window.contactcenterDeviceTranslations[key];
        }
        // Then try _l() function if available
        if (typeof _l !== 'undefined') {
            return _l(key);
        }
        // Fallback to provided fallback or key
        return fallback || key;
    }
    
    // Handle reconnect button clicks (delegated event)
    $(document).on('click', '.device-reconnect-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $btn = $(this);
        var deviceId = $btn.data('device-id');
        
        if (!deviceId) {
            if (typeof alert_float !== 'undefined') {
                alert_float('danger', getTranslation('device_id_not_found', 'Device ID not found'));
            }
            return;
        }
        
        // Disable button and show loading
        $btn.prop('disabled', true);
        var originalHtml = $btn.html();
        $btn.html('<i class="fa fa-spinner fa-spin"></i> ' + 
            getTranslation('reconnecting', 'Reconnecting...'));
        
        var url_contactcenter = (typeof site_url !== 'undefined' ? site_url : '') + 'admin/contactcenter/';
        
        $.ajax({
            url: url_contactcenter + 'reconnect_device',
            type: 'POST',
            data: {
                device_id: deviceId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.qrcode) {
                    // Show QR code modal with the QR code from response
                    showQRCodeModal(response.qrcode, response.pairingCode, deviceId);
                    
                    // Re-enable button
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                } else if (response.success) {
                    // Device restarted successfully but QR code not in response
                    // Fetch QR code using get_qrcode endpoint (same as qrcode_single page)
                    fetchQRCodeAfterReconnect(deviceId, $btn, originalHtml);
                } else {
                    if (typeof alert_float !== 'undefined') {
                        alert_float('danger', response.message || 
                            getTranslation('device_reconnect_failed', 'Failed to reconnect device'));
                    }
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                }
            },
            error: function(xhr, status, error) {
                if (typeof alert_float !== 'undefined') {
                    alert_float('danger', getTranslation('device_reconnect_failed', 'Failed to reconnect device') + ': ' + error);
                }
                $btn.prop('disabled', false);
                $btn.html(originalHtml);
            }
        });
    });
    
    // Fetch QR code after reconnect (same approach as qrcode_single page)
    function fetchQRCodeAfterReconnect(deviceId, $btn, originalHtml) {
        var url_contactcenter = (typeof site_url !== 'undefined' ? site_url : '') + 'admin/contactcenter/';
        
        // Show loading modal first
        if ($('#device-qrcode-modal').length === 0) {
            showQRCodeModal(null, null, deviceId);
        } else {
            $('#qrcode-loader').show();
            $('#qrcode-display').hide();
            $('#device-qrcode-modal').modal('show');
        }
        
        // Fetch QR code using get_qrcode endpoint (same as qrcode_single page)
        $.ajax({
            url: url_contactcenter + 'get_qrcode',
            data: {
                id: deviceId
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.qrcode) {
                    // Show QR code in modal
                    // data.qrcode from get_qrcode endpoint is already a base64 data URL or image source
                    $('#qrcode-loader').hide();
                    $('#qrcode-display').show();
                    
                    // Check if qrcode already includes data:image prefix, if not add it
                    var qrcodeSrc = data.qrcode;
                    if (qrcodeSrc && !qrcodeSrc.startsWith('data:image') && !qrcodeSrc.startsWith('http')) {
                        qrcodeSrc = 'data:image/png;base64,' + qrcodeSrc;
                    }
                    $('#device-qrcode-image').attr('src', qrcodeSrc);
                    
                    if (data.pairingCode) {
                        $('#device-pairing-code').text(getTranslation('pairing_code', 'Pairing Code') + ': ' + data.pairingCode);
                    }
                    
                    // Start checking connection status
                    checkDeviceConnectionStatus(deviceId);
                    
                    // Re-enable button
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                } else {
                    // QR code not available yet, show message and retry
                    $('#qrcode-message').text(data.message || getTranslation('qrcode_search', 'Searching for QR code...'));
                    
                    // Retry after 2 seconds (max 5 retries)
                    fetchQRCodeAfterReconnect.retryCount++;
                    
                    if (fetchQRCodeAfterReconnect.retryCount < 5) {
                        setTimeout(function() {
                            fetchQRCodeAfterReconnect(deviceId, $btn, originalHtml);
                        }, 2000);
                    } else {
                        // Max retries reached - reset counter
                        fetchQRCodeAfterReconnect.retryCount = 0;
                        if (typeof alert_float !== 'undefined') {
                            alert_float('warning', getTranslation('qrcode_search', 'QR code is taking longer than expected. Please try again.'));
                        }
                        $('#device-qrcode-modal').modal('hide');
                        $btn.prop('disabled', false);
                        $btn.html(originalHtml);
                    }
                }
            },
            error: function(xhr, status, error) {
                if (typeof alert_float !== 'undefined') {
                    alert_float('danger', getTranslation('device_reconnect_failed', 'Failed to get QR code') + ': ' + error);
                }
                $('#device-qrcode-modal').modal('hide');
                $btn.prop('disabled', false);
                $btn.html(originalHtml);
            }
        });
    }
    
    // Show QR Code Modal
    function showQRCodeModal(qrcodeBase64, pairingCode, deviceId) {
        // Create modal HTML if it doesn't exist
        if ($('#device-qrcode-modal').length === 0) {
            var modalHTML = '<div class="modal fade" id="device-qrcode-modal" tabindex="-1" role="dialog">' +
                '<div class="modal-dialog modal-dialog-centered" role="document">' +
                '<div class="modal-content" style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 16px;">' +
                '<div class="modal-header" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">' +
                '<h5 class="modal-title" style="color: rgba(255, 255, 255, 0.95);">' + 
                getTranslation('device_status', 'Device Status') + '</h5>' +
                '<button type="button" class="close" data-dismiss="modal" style="color: rgba(255, 255, 255, 0.7);">&times;</button>' +
                '</div>' +
                '<div class="modal-body" style="text-align: center; padding: 30px;">' +
                '<div class="contact-box-load-code" id="qrcode-loader" style="display: none;">' +
                '<div class="loaderQrcode" id="loader-5">' +
                '<span></span><span></span><span></span><span></span>' +
                '</div>' +
                '<h3 id="qrcode-message" style="color: rgba(255, 255, 255, 0.8);">' + 
                getTranslation('qrcode_search', 'Searching for QR code...') + '</h3>' +
                '</div>' +
                '<div class="contact-box-qrcode" id="qrcode-display" style="display: none;">' +
                '<div>' +
                '<img id="device-qrcode-image" src="" style="max-width: 100%; border-radius: 8px;" />' +
                '</div>' +
                '<h3 id="device-pairing-code" style="color: rgba(255, 255, 255, 0.9); margin-top: 20px; font-size: 18px;"></h3>' +
                '</div>' +
                '</div>' +
                '<div class="modal-footer" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">' +
                '<button type="button" class="btn btn-secondary" data-dismiss="modal">' + 
                getTranslation('close', 'Close') + '</button>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';
            
            $('body').append(modalHTML);
        }
        
        // Show loader first
        $('#qrcode-loader').show();
        $('#qrcode-display').hide();
        $('#device-qrcode-modal').modal('show');
        
        // Set QR code image and pairing code
        setTimeout(function() {
            // If QR code is provided, show it; otherwise keep loader visible
            if (qrcodeBase64) {
                $('#qrcode-loader').hide();
                $('#qrcode-display').show();
                
                // Handle QR code - check if it already has data:image prefix or is base64 string
                var qrcodeSrc = qrcodeBase64;
                // If it doesn't start with data:image or http, add the prefix
                if (qrcodeSrc && !qrcodeSrc.startsWith('data:image') && !qrcodeSrc.startsWith('http')) {
                    qrcodeSrc = 'data:image/png;base64,' + qrcodeSrc;
                }
                
                // Only set src if we have a valid QR code
                if (qrcodeSrc) {
                    $('#device-qrcode-image').attr('src', qrcodeSrc);
                }
                
                if (pairingCode) {
                    $('#device-pairing-code').text(getTranslation('pairing_code', 'Pairing Code') + ': ' + pairingCode);
                }
                
                // Start checking connection status
                checkDeviceConnectionStatus(deviceId);
            } else {
                // No QR code provided, keep loader visible and fetch it
                $('#qrcode-message').text(getTranslation('qrcode_search', 'Searching for QR code...'));
            }
        }, 500);
    }
    
    // Check device connection status periodically
    function checkDeviceConnectionStatus(deviceId) {
        var checkInterval = setInterval(function() {
            var url_contactcenter = (typeof site_url !== 'undefined' ? site_url : '') + 'admin/contactcenter/';
            
            $.ajax({
                url: url_contactcenter + 'get_status_connection_device',
                type: 'POST',
                data: { id: deviceId },
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        clearInterval(checkInterval);
                        
                        // Update message
                        $('#qrcode-message').text(getTranslation('contac_conected', 'Connected!'));
                        $('#qrcode-display').hide();
                        $('#qrcode-loader').show();
                        
                        // Close modal and remove device icon after delay
                        setTimeout(function() {
                            $('#device-qrcode-modal').modal('hide');
                            
                            var $deviceIcon = $('.device-status-icon-wrapper[data-device-id="' + deviceId + '"]');
                            $deviceIcon.fadeOut(300, function() {
                                $(this).remove();
                                updateDeviceStatusWidget();
                            });
                            
                            if (typeof alert_float !== 'undefined') {
                                alert_float('success', getTranslation('device_reconnected', 'Device reconnected successfully'));
                            }
                        }, 2000);
                    }
                }
            });
        }, 4000); // Check every 4 seconds
        
        // Stop checking after 5 minutes
        setTimeout(function() {
            clearInterval(checkInterval);
        }, 300000);
    }
    
    function updateDeviceStatusWidget() {
        var remainingDevices = $('.device-status-icon-wrapper.device-pulse-animation').length;
        
        if (remainingDevices === 0) {
            // All devices reconnected - show success icon
            var $container = $('.device-status-widget-container');
            if ($container.length > 0) {
                $container.html('<div class="device-status-icon-wrapper tw-relative" data-toggle="tooltip" title="' + 
                    getTranslation('all_devices_connected', 'All devices connected') + 
                    '" data-placement="bottom">' +
                    '<span class="tw-inline-flex tw-items-center tw-justify-center tw-w-7 tw-h-7 tw-rounded-md tw-border tw-border-solid tw-border-neutral-200/60 tw-bg-neutral-100/50">' +
                    '<i class="fa-solid fa-mobile-screen-button tw-text-success tw-text-sm"></i>' +
                    '</span>' +
                    '</div>');
                
                // Reinitialize tooltips
                if (typeof $ !== 'undefined' && $.fn.tooltip) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            }
        }
    }
})();
