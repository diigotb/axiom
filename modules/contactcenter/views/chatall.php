<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- inicio painel -->
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <lord-icon src="https://cdn.lordicon.com/wzrwaorf.json" trigger="loop" delay="2000" colors="primary:#00e09b,secondary:#00e09b" style="width:50px;height:50px">
                            </lord-icon>

                            <span>
                                <?php echo _l('contac_chat_all'); ?>
                            </span>
                        </h4>
                        <hr class="hr-panel-separator" />
                        <div class="clearfix"></div>

                        <div class="col-md-12">
                            <ul class="container-chatll">

                                <?php
                                foreach ($device as $dev) {
                                    // Skip inactive devices
                                    $is_active = isset($dev->is_active) ? $dev->is_active : 1;
                                    if ($is_active != 1) {
                                        continue;
                                    }
                                    
                                    if ($dev->dev_type == 3 || $dev->staffid == get_staff_user_id() || has_permission('contactcenter', '', 'chat_viwer_all')) {
                                        $url = "href='" . admin_url("contactcenter/chatsingle/{$dev->dev_id}") . "'";
                                        $bg = "";
                                    } else {
                                        $url = "";
                                        $bg = "style='background-color: #8d8c8c5e;'";
                                    }

                                ?>

                                    <li class="card-chatll" <?php echo $bg; ?>>
                                        <a <?php echo $url; ?>>
                                            <div class="">
                                                <div class='card-chatll-thumb'>
                                                    <img src="<?php echo staff_profile_image_url($dev->staffid); ?>" />
                                                </div>
                                                <div class="card-chatll-name">
                                                    <h4><?= substr($dev->dev_name, 0, 20) ?></h4> 
                                                    <h6><?= ($dev->staffid ? get_staff_full_name($dev->staffid) : ""); ?></h6>
                                                    <h6><span><?= label_status_device($dev->status); ?></span></h6>
                                                    <?php if ($dev->status != "open" && $dev->status != "inChat" && $dev->status != "connecting") { ?>
                                                        <button class="btn btn-xs btn-danger card-chatll-reconnect-btn" 
                                                                data-device-id="<?= $dev->dev_id ?>" 
                                                                data-device-name="<?= htmlspecialchars($dev->dev_name) ?>"
                                                                onclick="event.preventDefault(); event.stopPropagation(); reconnectDeviceFromChatall(<?= $dev->dev_id ?>);"
                                                                style="margin-top: 8px;">
                                                            <i class="fa-solid fa-power-off"></i> <?= _l('reconnect') ?>
                                                        </button>
                                                    <?php } ?>
                                                </div>
                                                <div class='card-chatll-dados'>
                                                    <?php

                                                    $totalUnreadCount = 0;
                                                    $ContatosDevice = count_contact_isread($dev->dev_token);
                                                    $CountContact = count_contact($dev->dev_token);
                                                    if ($ContatosDevice->unread_count > 0) {
                                                        $count = "<i class='badge badge-primary'>{$ContatosDevice->unread_count} </i>";
                                                    } else {
                                                        $count = 0;
                                                    }
                                                    ?>
                                                    <span><?= _l("contac_chat_contact") . " " . $CountContact; ?></span>
                                                    <span><?= _l("contac_chat_not_resp") ?> <?= $count ?></span>
                                                </div>
                                                <div class='card-chatall-departaments'>
                                                    <?php foreach (get_members_departments_staffid($dev->staffid) as $index => $departaments) { ?>
                                                        <span class="badge badge-info"><?= $departaments->name; ?></span>
                                                    <?php  } ?>
                                                </div>

                                            </div>
                                        </a>
                                    </li>



                                <?php } ?>
                            </ul>


                        </div>

                        <!-- fim painel -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    // Reuse QR code modal functions from device-status-widget.js
    // If the functions don't exist, define them here
    
    function reconnectDeviceFromChatall(deviceId) {
        var $btn = $('.card-chatll-reconnect-btn[data-device-id="' + deviceId + '"]');
        
        if (!$btn.length) {
            if (typeof alert_float !== 'undefined') {
                alert_float('danger', (typeof _l !== 'undefined' ? _l('device_id_not_found') : 'Device ID not found'));
            }
            return;
        }
        
        // Disable button and show loading
        $btn.prop('disabled', true);
        var originalHtml = $btn.html();
        $btn.html('<i class="fa fa-spinner fa-spin"></i> ' + 
            (typeof _l !== 'undefined' ? _l('reconnecting') : 'Reconnecting...'));
        
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
                    // Show QR code modal
                    showQRCodeModalChatall(response.qrcode, response.pairingCode, deviceId);
                    
                    // Re-enable button
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                } else if (response.success) {
                    // Success but no QR code
                    if (typeof alert_float !== 'undefined') {
                        alert_float('success', response.message || 
                            (typeof _l !== 'undefined' ? _l('device_reconnected') : 'Device reconnected successfully'));
                    }
                    // Reload page to update device status
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    if (typeof alert_float !== 'undefined') {
                        alert_float('danger', response.message || 
                            (typeof _l !== 'undefined' ? _l('device_reconnect_failed') : 'Failed to reconnect device'));
                    }
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                }
            },
            error: function(xhr, status, error) {
                if (typeof alert_float !== 'undefined') {
                    alert_float('danger', (typeof _l !== 'undefined' ? _l('device_reconnect_failed') : 'Failed to reconnect device') + ': ' + error);
                }
                $btn.prop('disabled', false);
                $btn.html(originalHtml);
            }
        });
    }
    
    // Show QR Code Modal (same as device-status-widget.js)
    function showQRCodeModalChatall(qrcodeBase64, pairingCode, deviceId) {
        // Create modal HTML if it doesn't exist
        if ($('#device-qrcode-modal').length === 0) {
            var getTranslation = function(key, fallback) {
                if (typeof window.contactcenterDeviceTranslations !== 'undefined' && window.contactcenterDeviceTranslations[key]) {
                    return window.contactcenterDeviceTranslations[key];
                }
                if (typeof _l !== 'undefined') {
                    return _l(key);
                }
                return fallback || key;
            };
            
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
        
        // Helper function for translations
        var getTranslation = function(key, fallback) {
            if (typeof window.contactcenterDeviceTranslations !== 'undefined' && window.contactcenterDeviceTranslations[key]) {
                return window.contactcenterDeviceTranslations[key];
            }
            if (typeof _l !== 'undefined') {
                return _l(key);
            }
            return fallback || key;
        };
        
        // Show loader first
        $('#qrcode-loader').show();
        $('#qrcode-display').hide();
        $('#device-qrcode-modal').modal('show');
        
        // Set QR code image and pairing code
        setTimeout(function() {
            $('#qrcode-loader').hide();
            $('#qrcode-display').show();
            $('#device-qrcode-image').attr('src', 'data:image/png;base64,' + qrcodeBase64);
            if (pairingCode) {
                $('#device-pairing-code').text(getTranslation('pairing_code', 'Pairing Code') + ': ' + pairingCode);
            }
            
            // Start checking connection status
            checkDeviceConnectionStatusChatall(deviceId);
        }, 500);
    }
    
    // Check device connection status periodically
    function checkDeviceConnectionStatusChatall(deviceId) {
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
                        var getTranslation = function(key, fallback) {
                            if (typeof window.contactcenterDeviceTranslations !== 'undefined' && window.contactcenterDeviceTranslations[key]) {
                                return window.contactcenterDeviceTranslations[key];
                            }
                            if (typeof _l !== 'undefined') {
                                return _l(key);
                            }
                            return fallback || key;
                        };
                        
                        $('#qrcode-message').text(getTranslation('contac_conected', 'Connected!'));
                        $('#qrcode-display').hide();
                        $('#qrcode-loader').show();
                        
                        // Close modal and reload page after delay
                        setTimeout(function() {
                            $('#device-qrcode-modal').modal('hide');
                            
                            if (typeof alert_float !== 'undefined') {
                                alert_float('success', getTranslation('device_reconnected', 'Device reconnected successfully'));
                            }
                            
                            // Reload page to update device status
                            window.location.reload();
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
</script>
</body>

</html>