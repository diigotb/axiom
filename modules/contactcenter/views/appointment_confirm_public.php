<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="<?= $GLOBALS['locale'] ?? 'pt' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= _l('contac_confirm_public_page_title') ?></title>
    <?php $favicon = get_option('favicon'); ?>
    <link rel="shortcut icon" href="<?= $favicon ? base_url('uploads/company/' . $favicon) : base_url('modules/contactcenter/icon_axiom_w.png') ?>" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00e09b;
            --primary-dark: #00b87a;
            --primary-light: rgba(0, 224, 155, 0.15);
            --danger: #ff4757;
            --danger-dark: #e84142;
            --danger-light: rgba(255, 71, 87, 0.15);
            --bg-dark: #0f1419;
            --bg-card: rgba(255, 255, 255, 0.05);
            --border: rgba(255, 255, 255, 0.1);
            --text: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.6);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .bg-shapes {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }
        .bg-shapes::before,
        .bg-shapes::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
        }
        .bg-shapes::before {
            width: 350px;
            height: 350px;
            background: var(--primary);
            top: -80px;
            right: -80px;
            animation: floatShape 15s ease-in-out infinite;
        }
        .bg-shapes::after {
            width: 280px;
            height: 280px;
            background: var(--primary);
            bottom: -50px;
            left: -50px;
            animation: floatShape 12s ease-in-out infinite reverse;
        }
        @keyframes floatShape {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-30px, 30px) scale(1.05); }
        }
        .confirm-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            padding: 24px 16px;
        }
        .confirm-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px 24px;
            backdrop-filter: blur(20px);
            text-align: center;
        }
        .confirm-logo {
            margin-bottom: 20px;
        }
        .confirm-logo img {
            height: 32px;
            width: auto;
            object-fit: contain;
        }
        .confirm-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 28px;
        }
        .confirm-icon.pending {
            background: var(--primary-light);
            color: var(--primary);
        }
        .confirm-icon.confirmed {
            background: var(--primary-light);
            color: var(--primary);
        }
        .confirm-icon.cancelled {
            background: var(--danger-light);
            color: var(--danger);
        }
        .confirm-icon.error {
            background: rgba(255, 165, 0, 0.15);
            color: #ffa500;
        }
        .confirm-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .confirm-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .event-details {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 28px;
            text-align: left;
        }
        .event-detail-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
        }
        .event-detail-row + .event-detail-row {
            border-top: 1px solid var(--border);
        }
        .event-detail-row i {
            color: var(--primary);
            width: 20px;
            text-align: center;
            font-size: 0.9rem;
        }
        .event-detail-row .detail-label {
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .event-detail-row .detail-value {
            font-weight: 600;
            font-size: 0.95rem;
        }
        .event-detail-content {
            flex: 1;
        }
        .btn-row {
            display: flex;
            gap: 12px;
            margin-top: 8px;
        }
        .btn-confirm, .btn-cancel-action {
            flex: 1;
            padding: 14px 20px;
            border: none;
            border-radius: 12px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-confirm {
            background: var(--primary);
            color: #0f1419;
        }
        .btn-confirm:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 224, 155, 0.3);
        }
        .btn-cancel-action {
            background: var(--danger);
            color: #fff;
        }
        .btn-cancel-action:hover {
            background: var(--danger-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3);
        }
        .btn-confirm:disabled, .btn-cancel-action:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .status-badge.confirmed {
            background: var(--primary-light);
            color: var(--primary);
        }
        .status-badge.cancelled {
            background: var(--danger-light);
            color: var(--danger);
        }
        .company-footer {
            margin-top: 24px;
            color: var(--text-muted);
            font-size: 0.8rem;
        }
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .loading .spinner { display: inline-block; }
        .loading .btn-text { display: none; }

        @media (max-width: 380px) {
            .confirm-card { padding: 24px 16px; }
            .btn-row { flex-direction: column; }
            .confirm-title { font-size: 1.2rem; }
        }
    </style>
</head>
<body>
    <div class="bg-shapes"></div>

    <div class="confirm-container">
        <div class="confirm-card">
            <div class="confirm-logo">
                <img src="<?= base_url('modules/contactcenter/logo_axiom_white.png') ?>" alt="AXIOM">
            </div>

            <?php if (!empty($error) && empty($record)): ?>
                <!-- Invalid / expired token -->
                <div class="confirm-icon error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h1 class="confirm-title"><?= _l('contac_confirm_public_invalid_title') ?></h1>
                <p class="confirm-subtitle"><?= isset($message) ? htmlspecialchars($message) : _l('contac_confirm_public_invalid') ?></p>

            <?php elseif (!empty($already_used)): ?>
                <?php
                    $icon_class = ($previous_status === 'confirmed') ? 'confirmed' : 'cancelled';
                    $icon = ($previous_status === 'confirmed') ? 'fa-circle-check' : 'fa-circle-xmark';
                    $status_text = ($previous_status === 'confirmed')
                        ? _l('contac_confirm_public_already_confirmed')
                        : _l('contac_confirm_public_already_cancelled');

                    $result_msg = '';
                    if (isset($process_result)) {
                        $result_msg = ($process_result['success'])
                            ? (($previous_status === 'confirmed') ? _l('contac_confirm_public_success') : _l('contac_confirm_public_cancelled'))
                            : '';
                    }
                ?>
                <div class="confirm-icon <?= $icon_class ?>">
                    <i class="fa-solid <?= $icon ?>"></i>
                </div>

                <?php if (!empty($result_msg)): ?>
                    <h1 class="confirm-title"><?= $result_msg ?></h1>
                    <p class="confirm-subtitle"><?= _l('contac_confirm_public_thank_you') ?></p>
                <?php else: ?>
                    <h1 class="confirm-title"><?= _l('contac_confirm_public_already_title') ?></h1>
                    <p class="confirm-subtitle"><?= $status_text ?></p>
                <?php endif; ?>

                <?php if (!empty($record)): ?>
                <div class="event-details">
                    <?php if (!empty($contact_name)): ?>
                    <div class="event-detail-row">
                        <i class="fa-solid fa-user"></i>
                        <div class="event-detail-content">
                            <div class="detail-label"><?= _l('contac_confirm_public_name') ?></div>
                            <div class="detail-value"><?= htmlspecialchars($contact_name) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="event-detail-row">
                        <i class="fa-solid fa-calendar"></i>
                        <div class="event-detail-content">
                            <div class="detail-label"><?= _l('contac_confirm_public_date') ?></div>
                            <div class="detail-value"><?= htmlspecialchars($event_date) ?></div>
                        </div>
                    </div>
                </div>
                <div>
                    <span class="status-badge <?= $icon_class ?>">
                        <i class="fa-solid <?= $icon ?>"></i>
                        <?= ($previous_status === 'confirmed') ? _l('contac_confirm_public_status_confirmed') : _l('contac_confirm_public_status_cancelled') ?>
                    </span>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- Pending confirmation -->
                <div class="confirm-icon pending">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <h1 class="confirm-title"><?= _l('contac_confirm_public_page_title') ?></h1>
                <p class="confirm-subtitle"><?= _l('contac_confirm_public_description') ?></p>

                <div class="event-details">
                    <?php if (!empty($contact_name)): ?>
                    <div class="event-detail-row">
                        <i class="fa-solid fa-user"></i>
                        <div class="event-detail-content">
                            <div class="detail-label"><?= _l('contac_confirm_public_name') ?></div>
                            <div class="detail-value"><?= htmlspecialchars($contact_name) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($event_title)): ?>
                    <div class="event-detail-row">
                        <i class="fa-solid fa-bookmark"></i>
                        <div class="event-detail-content">
                            <div class="detail-label"><?= _l('contac_confirm_public_event') ?></div>
                            <div class="detail-value"><?= htmlspecialchars($event_title) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="event-detail-row">
                        <i class="fa-solid fa-calendar"></i>
                        <div class="event-detail-content">
                            <div class="detail-label"><?= _l('contac_confirm_public_date') ?></div>
                            <div class="detail-value"><?= htmlspecialchars($event_date) ?></div>
                        </div>
                    </div>
                    <?php if (!empty($company_name)): ?>
                    <div class="event-detail-row">
                        <i class="fa-solid fa-building"></i>
                        <div class="event-detail-content">
                            <div class="detail-label"><?= _l('contac_confirm_public_company') ?></div>
                            <div class="detail-value"><?= htmlspecialchars($company_name) ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="btn-row">
                    <form method="POST" action="<?= site_url('contactcenter/appointment_confirm_public/process/' . $token) ?>" id="confirmForm" style="flex:1;display:flex;">
                        <input type="hidden" name="action" value="confirm">
                        <button type="submit" class="btn-confirm" id="btnConfirm" style="flex:1;">
                            <span class="btn-text"><i class="fa-solid fa-check"></i> <?= _l('contac_confirm_public_btn_confirm') ?></span>
                            <span class="spinner"></span>
                        </button>
                    </form>
                    <form method="POST" action="<?= site_url('contactcenter/appointment_confirm_public/process/' . $token) ?>" id="cancelForm" style="flex:1;display:flex;">
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" class="btn-cancel-action" id="btnCancel" style="flex:1;">
                            <span class="btn-text"><i class="fa-solid fa-xmark"></i> <?= _l('contac_confirm_public_btn_cancel') ?></span>
                            <span class="spinner"></span>
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if (!empty($company_name)): ?>
            <div class="company-footer">
                <?= htmlspecialchars($company_name) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var forms = document.querySelectorAll('#confirmForm, #cancelForm');
        forms.forEach(function(form) {
            form.addEventListener('submit', function() {
                var btn = form.querySelector('button');
                btn.classList.add('loading');
                btn.disabled = true;
                document.querySelectorAll('.btn-confirm, .btn-cancel-action').forEach(function(b) {
                    b.disabled = true;
                });
            });
        });
    });
    </script>
</body>
</html>
