<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="<?= $GLOBALS['locale'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($assistant->ai_name ?? 'AXIOM') ?> - <?= _l('cc_onboarding_title') ?></title>
    <?php $favicon = get_option('favicon'); ?>
    <link rel="shortcut icon" href="<?= $favicon ? base_url('uploads/company/' . $favicon) : base_url('modules/contactcenter/icon_axiom_w.png') ?>" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('modules/contactcenter/assets/css/assistant_onboarding_wizard.css') ?>">
</head>
<body>
<div id="assistant-onboarding-app">
    <div class="wizard-bg">
        <div class="wizard-bg-shapes"></div>
    </div>

    <div class="wizard-container">
        <header class="wizard-header">
            <div class="wizard-logo">
                <lord-icon src="https://cdn.lordicon.com/uttrirxf.json" trigger="loop" delay="2000" colors="primary:#00e09b,secondary:#00e09b" style="width:48px;height:48px"></lord-icon>
                <img src="<?= base_url('modules/contactcenter/logo_axiom_white.png') ?>" alt="AXIOM" class="wizard-logo-img">
                <h1><?= _l('cc_onboarding_title') ?></h1>
                <p class="wizard-subtitle"><?= htmlspecialchars($assistant->ai_name ?? '') ?> — <?= _l('cc_onboarding_subtitle') ?></p>
            </div>
            <div class="wizard-progress">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 10%"></div>
                </div>
                <div class="step-dots" id="stepDots"></div>
            </div>
        </header>

        <main class="wizard-content">
            <form id="onboardingForm">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

                <!-- Step 1: Company Info -->
                <section class="wizard-step active" data-step="1">
                    <div class="step-header">
                        <span class="step-icon"><i class="fa-solid fa-building"></i></span>
                        <h2><?= _l('cc_onboarding_step1_title') ?></h2>
                        <p><?= _l('cc_onboarding_step1_desc') ?></p>
                    </div>
                    <div class="step-body">
                        <div class="form-group">
                            <label for="company_name"><?= _l('cc_onboarding_company_name') ?></label>
                            <input type="text" id="company_name" name="company_name" placeholder="<?= _l('cc_onboarding_company_name_ph') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="company_info"><?= _l('cc_onboarding_company_info') ?></label>
                            <textarea id="company_info" name="company_info" rows="4" placeholder="<?= _l('cc_onboarding_company_info_ph') ?>"></textarea>
                            <small><?= _l('cc_onboarding_company_info_help') ?></small>
                        </div>
                    </div>
                </section>

                <!-- Step 2: Tone & Behavior -->
                <section class="wizard-step" data-step="2">
                    <div class="step-header">
                        <span class="step-icon"><i class="fa-solid fa-face-smile"></i></span>
                        <h2><?= _l('cc_onboarding_step2_title') ?></h2>
                        <p><?= _l('cc_onboarding_step2_desc') ?></p>
                    </div>
                    <div class="step-body">
                        <div class="card-grid" data-field="tone">
                            <label class="option-card">
                                <input type="radio" name="tone" value="casual">
                                <span class="card-inner">
                                    <i class="fa-solid fa-comments"></i>
                                    <strong><?= _l('cc_onboarding_tone_casual') ?></strong>
                                    <small><?= _l('cc_onboarding_tone_casual_desc') ?></small>
                                </span>
                            </label>
                            <label class="option-card">
                                <input type="radio" name="tone" value="formal">
                                <span class="card-inner">
                                    <i class="fa-solid fa-briefcase"></i>
                                    <strong><?= _l('cc_onboarding_tone_formal') ?></strong>
                                    <small><?= _l('cc_onboarding_tone_formal_desc') ?></small>
                                </span>
                            </label>
                            <label class="option-card">
                                <input type="radio" name="tone" value="technical">
                                <span class="card-inner">
                                    <i class="fa-solid fa-code"></i>
                                    <strong><?= _l('cc_onboarding_tone_technical') ?></strong>
                                    <small><?= _l('cc_onboarding_tone_technical_desc') ?></small>
                                </span>
                            </label>
                            <label class="option-card">
                                <input type="radio" name="tone" value="informal">
                                <span class="card-inner">
                                    <i class="fa-solid fa-hand-peace"></i>
                                    <strong><?= _l('cc_onboarding_tone_informal') ?></strong>
                                    <small><?= _l('cc_onboarding_tone_informal_desc') ?></small>
                                </span>
                            </label>
                        </div>
                    </div>
                </section>

                <!-- Step 3: Assistant Name & Identity -->
                <section class="wizard-step" data-step="3">
                    <div class="step-header">
                        <span class="step-icon"><img src="<?= base_url('modules/contactcenter/icon_axiom_w.png') ?>" alt="" class="step-icon-img"></span>
                        <h2><?= _l('cc_onboarding_step3_title') ?></h2>
                        <p><?= _l('cc_onboarding_step3_desc') ?></p>
                    </div>
                    <div class="step-body">
                        <div class="form-group">
                            <label for="assistant_name"><?= _l('cc_onboarding_assistant_name') ?></label>
                            <input type="text" id="assistant_name" name="assistant_name" placeholder="<?= _l('cc_onboarding_assistant_name_ph') ?>">
                            <small><?= _l('cc_onboarding_assistant_name_help') ?></small>
                        </div>
                        <div class="form-group tw-mt-4">
                            <label><?= _l('cc_onboarding_assistant_characteristics') ?></label>
                            <small class="tw-block tw-mb-2"><?= _l('cc_onboarding_assistant_characteristics_help') ?></small>
                            <div class="card-grid" data-field="assistant_characteristics">
                                <label class="option-card char-card">
                                    <input type="checkbox" name="assistant_characteristics[]" value="friendly">
                                    <span class="card-inner">
                                        <i class="fa-solid fa-face-smile"></i>
                                        <strong><?= _l('cc_onboarding_char_friendly') ?></strong>
                                        <small><?= _l('cc_onboarding_char_friendly_desc') ?></small>
                                    </span>
                                </label>
                                <label class="option-card char-card">
                                    <input type="checkbox" name="assistant_characteristics[]" value="patient">
                                    <span class="card-inner">
                                        <i class="fa-solid fa-clock"></i>
                                        <strong><?= _l('cc_onboarding_char_patient') ?></strong>
                                        <small><?= _l('cc_onboarding_char_patient_desc') ?></small>
                                    </span>
                                </label>
                                <label class="option-card char-card">
                                    <input type="checkbox" name="assistant_characteristics[]" value="proactive">
                                    <span class="card-inner">
                                        <i class="fa-solid fa-bolt"></i>
                                        <strong><?= _l('cc_onboarding_char_proactive') ?></strong>
                                        <small><?= _l('cc_onboarding_char_proactive_desc') ?></small>
                                    </span>
                                </label>
                                <label class="option-card char-card">
                                    <input type="checkbox" name="assistant_characteristics[]" value="expert">
                                    <span class="card-inner">
                                        <i class="fa-solid fa-graduation-cap"></i>
                                        <strong><?= _l('cc_onboarding_char_expert') ?></strong>
                                        <small><?= _l('cc_onboarding_char_expert_desc') ?></small>
                                    </span>
                                </label>
                                <label class="option-card char-card">
                                    <input type="checkbox" name="assistant_characteristics[]" value="empathetic">
                                    <span class="card-inner">
                                        <i class="fa-solid fa-heart"></i>
                                        <strong><?= _l('cc_onboarding_char_empathetic') ?></strong>
                                        <small><?= _l('cc_onboarding_char_empathetic_desc') ?></small>
                                    </span>
                                </label>
                                <label class="option-card char-card">
                                    <input type="checkbox" name="assistant_characteristics[]" value="efficient">
                                    <span class="card-inner">
                                        <i class="fa-solid fa-check-double"></i>
                                        <strong><?= _l('cc_onboarding_char_efficient') ?></strong>
                                        <small><?= _l('cc_onboarding_char_efficient_desc') ?></small>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group tw-mt-4">
                            <label for="assistant_characteristics_notes"><?= _l('cc_onboarding_assistant_characteristics_notes') ?></label>
                            <textarea id="assistant_characteristics_notes" name="assistant_characteristics_notes" rows="3" placeholder="<?= _l('cc_onboarding_assistant_characteristics_notes_ph') ?>"></textarea>
                        </div>
                    </div>
                </section>

                <!-- Step 4: Greeting -->
                <section class="wizard-step" data-step="4">
                    <div class="step-header">
                        <span class="step-icon"><i class="fa-solid fa-hand"></i></span>
                        <h2><?= _l('cc_onboarding_step4_title') ?></h2>
                        <p><?= _l('cc_onboarding_step4_desc') ?></p>
                    </div>
                    <div class="step-body">
                        <div class="card-grid greeting-options" data-field="greeting_preset">
                            <label class="option-card">
                                <input type="radio" name="greeting_preset" value="friendly" data-text="<?= htmlspecialchars(_l('cc_onboarding_greeting_friendly')) ?>">
                                <span class="card-inner">
                                    <i class="fa-solid fa-face-smile"></i>
                                    <strong><?= _l('cc_onboarding_greeting_friendly') ?></strong>
                                    <small><?= _l('cc_onboarding_greeting_friendly_desc') ?></small>
                                </span>
                            </label>
                            <label class="option-card">
                                <input type="radio" name="greeting_preset" value="professional" data-text="<?= htmlspecialchars(_l('cc_onboarding_greeting_professional')) ?>">
                                <span class="card-inner">
                                    <i class="fa-solid fa-briefcase"></i>
                                    <strong><?= _l('cc_onboarding_greeting_professional') ?></strong>
                                    <small><?= _l('cc_onboarding_greeting_professional_desc') ?></small>
                                </span>
                            </label>
                            <label class="option-card">
                                <input type="radio" name="greeting_preset" value="warm" data-text="<?= htmlspecialchars(_l('cc_onboarding_greeting_warm')) ?>">
                                <span class="card-inner">
                                    <i class="fa-solid fa-heart"></i>
                                    <strong><?= _l('cc_onboarding_greeting_warm') ?></strong>
                                    <small><?= _l('cc_onboarding_greeting_warm_desc') ?></small>
                                </span>
                            </label>
                            <label class="option-card option-card-full">
                                <input type="radio" name="greeting_preset" value="custom">
                                <span class="card-inner">
                                    <i class="fa-solid fa-pen"></i>
                                    <strong><?= _l('cc_onboarding_greeting_custom') ?></strong>
                                    <small><?= _l('cc_onboarding_greeting_custom_desc') ?></small>
                                </span>
                            </label>
                        </div>
                        <div class="form-group greeting-custom-wrap tw-mt-4" style="display:none">
                            <label for="greeting"><?= _l('cc_onboarding_greeting') ?></label>
                            <textarea id="greeting" name="greeting" rows="4" placeholder="<?= _l('cc_onboarding_greeting_ph') ?>"></textarea>
                            <small><?= _l('cc_onboarding_greeting_help') ?></small>
                        </div>
                    </div>
                </section>

                <!-- Step 5: Objective -->
                <section class="wizard-step" data-step="5">
                    <div class="step-header">
                        <span class="step-icon"><i class="fa-solid fa-bullseye"></i></span>
                        <h2><?= _l('cc_onboarding_step5_title') ?></h2>
                        <p><?= _l('cc_onboarding_step5_desc') ?></p>
                    </div>
                    <div class="step-body">
                        <div class="card-grid objective-options" data-field="objective">
                            <label class="option-card objective-card">
                                <input type="checkbox" name="objective[]" value="qualification">
                                <span class="card-inner">
                                    <i class="fa-solid fa-filter"></i>
                                    <strong><?= _l('cc_onboarding_obj_qualification') ?></strong>
                                    <small><?= _l('cc_onboarding_obj_qualification_desc') ?></small>
                                </span>
                            </label>
                            <label class="option-card objective-card">
                                <input type="checkbox" name="objective[]" value="scheduling">
                                <span class="card-inner">
                                    <i class="fa-solid fa-calendar-check"></i>
                                    <strong><?= _l('cc_onboarding_obj_scheduling') ?></strong>
                                    <small><?= _l('cc_onboarding_obj_scheduling_desc') ?></small>
                                </span>
                            </label>
                            <label class="option-card objective-card">
                                <input type="checkbox" name="objective[]" value="informing">
                                <span class="card-inner">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <strong><?= _l('cc_onboarding_obj_informing') ?></strong>
                                    <small><?= _l('cc_onboarding_obj_informing_desc') ?></small>
                                </span>
                            </label>
                            <label class="option-card objective-card">
                                <input type="checkbox" name="objective[]" value="sales">
                                <span class="card-inner">
                                    <i class="fa-solid fa-hand-holding-dollar"></i>
                                    <strong><?= _l('cc_onboarding_obj_sales') ?></strong>
                                    <small><?= _l('cc_onboarding_obj_sales_desc') ?></small>
                                </span>
                            </label>
                        </div>
                        <div class="form-group tw-mt-4">
                            <label for="objective_notes"><?= _l('cc_onboarding_obj_notes') ?></label>
                            <textarea id="objective_notes" name="objective_notes" rows="2" placeholder="<?= _l('cc_onboarding_obj_notes_ph') ?>"></textarea>
                        </div>
                    </div>
                </section>

                <!-- Step 6: FAQ -->
                <section class="wizard-step" data-step="6">
                    <div class="step-header">
                        <span class="step-icon"><i class="fa-solid fa-circle-question"></i></span>
                        <h2><?= _l('cc_onboarding_step6_title') ?></h2>
                        <p><?= _l('cc_onboarding_step6_desc') ?></p>
                    </div>
                    <div class="step-body">
                        <div class="card-grid faq-options">
                            <label class="option-card faq-card">
                                <input type="checkbox" name="faq_preset[]" value="prices" data-text="<?= htmlspecialchars(_l('cc_onboarding_faq_prices')) ?>">
                                <span class="card-inner">
                                    <i class="fa-solid fa-tags"></i>
                                    <strong><?= _l('cc_onboarding_faq_prices') ?></strong>
                                </span>
                            </label>
                            <label class="option-card faq-card">
                                <input type="checkbox" name="faq_preset[]" value="schedule" data-text="<?= htmlspecialchars(_l('cc_onboarding_faq_schedule')) ?>">
                                <span class="card-inner">
                                    <i class="fa-solid fa-calendar-check"></i>
                                    <strong><?= _l('cc_onboarding_faq_schedule') ?></strong>
                                </span>
                            </label>
                            <label class="option-card faq-card">
                                <input type="checkbox" name="faq_preset[]" value="hours" data-text="<?= htmlspecialchars(_l('cc_onboarding_faq_hours')) ?>">
                                <span class="card-inner">
                                    <i class="fa-solid fa-clock"></i>
                                    <strong><?= _l('cc_onboarding_faq_hours') ?></strong>
                                </span>
                            </label>
                            <label class="option-card faq-card">
                                <input type="checkbox" name="faq_preset[]" value="how_know" data-text="<?= htmlspecialchars(_l('cc_onboarding_faq_how_know')) ?>">
                                <span class="card-inner">
                                    <i class="fa-solid fa-lightbulb"></i>
                                    <strong><?= _l('cc_onboarding_faq_how_know') ?></strong>
                                </span>
                            </label>
                            <label class="option-card faq-card">
                                <input type="checkbox" name="faq_preset[]" value="offer" data-text="<?= htmlspecialchars(_l('cc_onboarding_faq_offer')) ?>">
                                <span class="card-inner">
                                    <i class="fa-solid fa-box"></i>
                                    <strong><?= _l('cc_onboarding_faq_offer') ?></strong>
                                </span>
                            </label>
                            <label class="option-card faq-card">
                                <input type="checkbox" name="faq_preset[]" value="payment" data-text="<?= htmlspecialchars(_l('cc_onboarding_faq_payment')) ?>">
                                <span class="card-inner">
                                    <i class="fa-solid fa-credit-card"></i>
                                    <strong><?= _l('cc_onboarding_faq_payment') ?></strong>
                                </span>
                            </label>
                            <label class="option-card faq-card">
                                <input type="checkbox" name="faq_preset[]" value="contact_human" data-text="<?= htmlspecialchars(_l('cc_onboarding_faq_contact_human')) ?>">
                                <span class="card-inner">
                                    <i class="fa-solid fa-headset"></i>
                                    <strong><?= _l('cc_onboarding_faq_contact_human') ?></strong>
                                </span>
                            </label>
                            <label class="option-card faq-card">
                                <input type="checkbox" name="faq_preset[]" value="location" data-text="<?= htmlspecialchars(_l('cc_onboarding_faq_location')) ?>">
                                <span class="card-inner">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <strong><?= _l('cc_onboarding_faq_location') ?></strong>
                                </span>
                            </label>
                            <label class="option-card faq-card">
                                <input type="checkbox" name="faq_preset[]" value="delivery" data-text="<?= htmlspecialchars(_l('cc_onboarding_faq_delivery')) ?>">
                                <span class="card-inner">
                                    <i class="fa-solid fa-truck"></i>
                                    <strong><?= _l('cc_onboarding_faq_delivery') ?></strong>
                                </span>
                            </label>
                        </div>
                        <div class="form-group tw-mt-4">
                            <label for="faq"><?= _l('cc_onboarding_faq_add_more') ?></label>
                            <textarea id="faq" name="faq" rows="4" placeholder="<?= _l('cc_onboarding_faq_add_more_ph') ?>"></textarea>
                            <small><?= _l('cc_onboarding_faq_ph') ?></small>
                        </div>
                    </div>
                </section>

                <!-- Step 7: Knowledge Materials -->
                <section class="wizard-step" data-step="7">
                    <div class="step-header">
                        <span class="step-icon"><i class="fa-solid fa-folder-open"></i></span>
                        <h2><?= _l('cc_onboarding_step7_title') ?></h2>
                        <p><?= _l('cc_onboarding_step7_desc') ?></p>
                    </div>
                    <div class="step-body">
                        <div class="card-grid card-grid-2" data-field="has_materials">
                            <label class="option-card">
                                <input type="radio" name="has_materials" value="yes">
                                <span class="card-inner">
                                    <i class="fa-solid fa-check-circle"></i>
                                    <strong><?= _l('cc_onboarding_has_materials_yes') ?></strong>
                                    <small><?= _l('cc_onboarding_has_materials_yes_desc') ?></small>
                                </span>
                            </label>
                            <label class="option-card">
                                <input type="radio" name="has_materials" value="no">
                                <span class="card-inner">
                                    <i class="fa-solid fa-times-circle"></i>
                                    <strong><?= _l('cc_onboarding_has_materials_no') ?></strong>
                                    <small><?= _l('cc_onboarding_has_materials_no_desc') ?></small>
                                </span>
                            </label>
                        </div>
                        <div class="form-group materials-details tw-mt-4" style="display:none">
                            <label for="materials_description"><?= _l('cc_onboarding_materials_desc') ?></label>
                            <textarea id="materials_description" name="materials_description" rows="3" placeholder="<?= _l('cc_onboarding_materials_desc_ph') ?>"></textarea>
                            <div class="form-group tw-mt-3">
                                <label for="materials_files"><?= _l('cc_onboarding_materials_upload') ?></label>
                                <input type="file" id="materials_files" name="materials_files[]" class="form-control" accept=".txt,.pdf,.docx,.csv,.json,.md" multiple>
                                <small><?= _l('cc_onboarding_materials_upload_help') ?></small>
                                <div id="materials_files_preview" class="tw-mt-2 tw-text-sm tw-text-gray-400"></div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Step 8: Conversation Flow -->
                <section class="wizard-step" data-step="8">
                    <div class="step-header">
                        <span class="step-icon"><i class="fa-solid fa-route"></i></span>
                        <h2><?= _l('cc_onboarding_step8_title') ?></h2>
                        <p><?= _l('cc_onboarding_step8_desc') ?></p>
                    </div>
                    <div class="step-body">
                        <div class="form-group">
                            <label><?= _l('cc_onboarding_flow_template') ?></label>
                            <div class="card-grid card-grid-flow" data-field="flow_template">
                                <label class="option-card flow-card">
                                    <input type="radio" name="flow_template" value="qualify_first">
                                    <span class="card-inner">
                                        <i class="fa-solid fa-user-check"></i>
                                        <strong><?= _l('cc_onboarding_flow_qualify_first') ?></strong>
                                        <small><?= _l('cc_onboarding_flow_qualify_first_desc') ?></small>
                                    </span>
                                </label>
                                <label class="option-card flow-card">
                                    <input type="radio" name="flow_template" value="inform_first">
                                    <span class="card-inner">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <strong><?= _l('cc_onboarding_flow_inform_first') ?></strong>
                                        <small><?= _l('cc_onboarding_flow_inform_first_desc') ?></small>
                                    </span>
                                </label>
                                <label class="option-card flow-card">
                                    <input type="radio" name="flow_template" value="schedule_first">
                                    <span class="card-inner">
                                        <i class="fa-solid fa-calendar-days"></i>
                                        <strong><?= _l('cc_onboarding_flow_schedule_first') ?></strong>
                                        <small><?= _l('cc_onboarding_flow_schedule_first_desc') ?></small>
                                    </span>
                                </label>
                                <label class="option-card flow-card">
                                    <input type="radio" name="flow_template" value="flexible">
                                    <span class="card-inner">
                                        <i class="fa-solid fa-comments"></i>
                                        <strong><?= _l('cc_onboarding_flow_flexible') ?></strong>
                                        <small><?= _l('cc_onboarding_flow_flexible_desc') ?></small>
                                    </span>
                                </label>
                                <label class="option-card flow-card option-card-full">
                                    <input type="radio" name="flow_template" value="custom">
                                    <span class="card-inner">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <strong><?= _l('cc_onboarding_flow_custom') ?></strong>
                                        <small><?= _l('cc_onboarding_flow_custom_desc') ?></small>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group tw-mt-4">
                            <label><?= _l('cc_onboarding_mandatory_info') ?></label>
                            <small class="tw-block tw-mb-2"><?= _l('cc_onboarding_mandatory_info_help') ?></small>
                            <div class="mandatory-grid">
                                <label class="mandatory-chip">
                                    <input type="checkbox" name="mandatory_info[]" value="name">
                                    <span><?= _l('cc_onboarding_mandatory_name') ?></span>
                                </label>
                                <label class="mandatory-chip">
                                    <input type="checkbox" name="mandatory_info[]" value="phone">
                                    <span><?= _l('cc_onboarding_mandatory_phone') ?></span>
                                </label>
                                <label class="mandatory-chip">
                                    <input type="checkbox" name="mandatory_info[]" value="email">
                                    <span><?= _l('cc_onboarding_mandatory_email') ?></span>
                                </label>
                                <label class="mandatory-chip">
                                    <input type="checkbox" name="mandatory_info[]" value="service">
                                    <span><?= _l('cc_onboarding_mandatory_service') ?></span>
                                </label>
                                <label class="mandatory-chip">
                                    <input type="checkbox" name="mandatory_info[]" value="date">
                                    <span><?= _l('cc_onboarding_mandatory_date') ?></span>
                                </label>
                                <label class="mandatory-chip">
                                    <input type="checkbox" name="mandatory_info[]" value="budget">
                                    <span><?= _l('cc_onboarding_mandatory_budget') ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group tw-mt-4">
                            <label for="opening_questions"><?= _l('cc_onboarding_opening_questions') ?></label>
                            <textarea id="opening_questions" name="opening_questions" rows="3" placeholder="<?= _l('cc_onboarding_opening_questions_ph') ?>"></textarea>
                        </div>
                        <div class="form-group tw-mt-4">
                            <label for="service_sequence"><?= _l('cc_onboarding_service_sequence') ?></label>
                            <textarea id="service_sequence" name="service_sequence" rows="3" placeholder="<?= _l('cc_onboarding_service_sequence_ph') ?>"></textarea>
                        </div>
                    </div>
                </section>

                <!-- Step 9: Contact & Location -->
                <section class="wizard-step" data-step="9">
                    <div class="step-header">
                        <span class="step-icon"><i class="fa-solid fa-location-dot"></i></span>
                        <h2><?= _l('cc_onboarding_step9_title') ?></h2>
                        <p><?= _l('cc_onboarding_step9_desc') ?></p>
                    </div>
                    <div class="step-body">
                        <div class="form-group">
                            <label for="cep"><?= _l('cc_onboarding_cep') ?></label>
                            <div class="cep-lookup-wrap">
                                <input type="text" id="cep" name="cep" placeholder="<?= _l('cc_onboarding_cep_ph') ?>" maxlength="9" data-mask="cep">
                                <span class="cep-loading" id="cepLoading" style="display:none"><i class="fa-solid fa-spinner fa-spin"></i></span>
                            </div>
                            <small class="cep-hint"><?= _l('cc_onboarding_cep_hint') ?></small>
                        </div>
                        <div class="form-row form-row-address">
                            <div class="form-group form-group-address">
                                <label for="address"><?= _l('cc_onboarding_address') ?></label>
                                <input type="text" id="address" name="address" placeholder="<?= _l('cc_onboarding_address_ph') ?>">
                            </div>
                            <div class="form-group form-group-number">
                                <label for="address_number"><?= _l('cc_onboarding_address_number') ?></label>
                                <input type="text" id="address_number" name="address_number" placeholder="<?= _l('cc_onboarding_address_number_ph') ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone"><?= _l('cc_onboarding_phone') ?></label>
                                <input type="text" id="phone" name="phone" placeholder="<?= _l('cc_onboarding_phone_ph') ?>" data-mask="phone">
                            </div>
                            <div class="form-group">
                                <label for="email"><?= _l('cc_onboarding_email') ?></label>
                                <input type="email" id="email" name="email" placeholder="<?= _l('cc_onboarding_email_ph') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="website"><?= _l('cc_onboarding_website') ?></label>
                            <input type="url" id="website" name="website" placeholder="<?= _l('cc_onboarding_website_ph') ?>">
                        </div>
                        <div class="form-group business-hours-block">
                            <label><?= _l('cc_onboarding_business_hours') ?></label>
                            <small class="tw-block tw-mb-2"><?= _l('cc_onboarding_business_hours_help') ?></small>
                            <div class="hours-grid">
                                <div class="hours-row">
                                    <span class="hours-label"><?= _l('cc_onboarding_hours_weekdays') ?></span>
                                    <span class="hours-times">
                                        <input type="time" id="hours_weekdays_from" name="hours_weekdays_from" value="09:00">
                                        <span class="hours-to"><?= _l('cc_onboarding_hours_to') ?></span>
                                        <input type="time" id="hours_weekdays_to" name="hours_weekdays_to" value="18:00">
                                    </span>
                                </div>
                                <div class="hours-row">
                                    <span class="hours-label"><?= _l('cc_onboarding_hours_saturday') ?></span>
                                    <span class="hours-times">
                                        <input type="time" id="hours_sat_from" name="hours_sat_from">
                                        <span class="hours-to"><?= _l('cc_onboarding_hours_to') ?></span>
                                        <input type="time" id="hours_sat_to" name="hours_sat_to">
                                    </span>
                                    <label class="hours-closed">
                                        <input type="checkbox" id="hours_sat_closed" name="hours_sat_closed" value="1">
                                        <span><?= _l('cc_onboarding_hours_closed') ?></span>
                                    </label>
                                </div>
                                <div class="hours-row">
                                    <span class="hours-label"><?= _l('cc_onboarding_hours_sunday') ?></span>
                                    <span class="hours-times">
                                        <input type="time" id="hours_sun_from" name="hours_sun_from">
                                        <span class="hours-to"><?= _l('cc_onboarding_hours_to') ?></span>
                                        <input type="time" id="hours_sun_to" name="hours_sun_to">
                                    </span>
                                    <label class="hours-closed">
                                        <input type="checkbox" id="hours_sun_closed" name="hours_sun_closed" value="1" checked>
                                        <span><?= _l('cc_onboarding_hours_closed') ?></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group tw-mt-3">
                                <input type="text" id="hours_notes" name="hours_notes" placeholder="<?= _l('cc_onboarding_hours_notes_ph') ?>" class="hours-notes-input">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="social_media"><?= _l('cc_onboarding_social_media') ?></label>
                            <textarea id="social_media" name="social_media" rows="2" placeholder="<?= _l('cc_onboarding_social_media_ph') ?>"></textarea>
                        </div>
                    </div>
                </section>

                <!-- Step 10: Services & Guidelines -->
                <section class="wizard-step" data-step="10">
                    <div class="step-header">
                        <span class="step-icon"><i class="fa-solid fa-list-check"></i></span>
                        <h2><?= _l('cc_onboarding_step10_title') ?></h2>
                        <p><?= _l('cc_onboarding_step10_desc') ?></p>
                    </div>
                    <div class="step-body">
                        <div class="form-group">
                            <label><?= _l('cc_onboarding_service_type') ?></label>
                            <div class="mandatory-grid">
                                <label class="mandatory-chip svc-chip">
                                    <input type="checkbox" name="service_type[]" value="consultation">
                                    <span><?= _l('cc_onboarding_svc_consultation') ?></span>
                                </label>
                                <label class="mandatory-chip svc-chip">
                                    <input type="checkbox" name="service_type[]" value="procedure">
                                    <span><?= _l('cc_onboarding_svc_procedure') ?></span>
                                </label>
                                <label class="mandatory-chip svc-chip">
                                    <input type="checkbox" name="service_type[]" value="product">
                                    <span><?= _l('cc_onboarding_svc_product') ?></span>
                                </label>
                                <label class="mandatory-chip svc-chip">
                                    <input type="checkbox" name="service_type[]" value="package">
                                    <span><?= _l('cc_onboarding_svc_package') ?></span>
                                </label>
                                <label class="mandatory-chip svc-chip">
                                    <input type="checkbox" name="service_type[]" value="rental">
                                    <span><?= _l('cc_onboarding_svc_rental') ?></span>
                                </label>
                                <label class="mandatory-chip svc-chip">
                                    <input type="checkbox" name="service_type[]" value="subscription">
                                    <span><?= _l('cc_onboarding_svc_subscription') ?></span>
                                </label>
                                <label class="mandatory-chip svc-chip">
                                    <input type="checkbox" name="service_type[]" value="other">
                                    <span><?= _l('cc_onboarding_svc_other') ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group tw-mt-4">
                            <label><?= _l('cc_onboarding_services') ?></label>
                            <div id="servicesList" class="services-list"></div>
                            <button type="button" class="btn-add-service" id="btnAddService"><i class="fa-solid fa-plus"></i> <?= _l('cc_onboarding_add_service') ?></button>
                            <div class="form-group tw-mt-3">
                                <label for="services_extra"><?= _l('cc_onboarding_services_extra') ?></label>
                                <textarea id="services_extra" name="services_extra" rows="2" placeholder="<?= _l('cc_onboarding_services_extra_ph') ?>"></textarea>
                            </div>
                        </div>
                        <div class="form-group tw-mt-4">
                            <label><?= _l('cc_onboarding_escalation') ?></label>
                            <small class="tw-block tw-mb-2"><?= _l('cc_onboarding_escalation_hint') ?></small>
                            <div class="mandatory-grid">
                                <label class="mandatory-chip esc-chip">
                                    <input type="checkbox" name="escalation_triggers[]" value="complaint">
                                    <span><?= _l('cc_onboarding_esc_complaint') ?></span>
                                </label>
                                <label class="mandatory-chip esc-chip">
                                    <input type="checkbox" name="escalation_triggers[]" value="refund">
                                    <span><?= _l('cc_onboarding_esc_refund') ?></span>
                                </label>
                                <label class="mandatory-chip esc-chip">
                                    <input type="checkbox" name="escalation_triggers[]" value="complex">
                                    <span><?= _l('cc_onboarding_esc_complex') ?></span>
                                </label>
                                <label class="mandatory-chip esc-chip">
                                    <input type="checkbox" name="escalation_triggers[]" value="schedule">
                                    <span><?= _l('cc_onboarding_esc_schedule') ?></span>
                                </label>
                                <label class="mandatory-chip esc-chip">
                                    <input type="checkbox" name="escalation_triggers[]" value="negotiation">
                                    <span><?= _l('cc_onboarding_esc_negotiation') ?></span>
                                </label>
                                <label class="mandatory-chip esc-chip">
                                    <input type="checkbox" name="escalation_triggers[]" value="technical">
                                    <span><?= _l('cc_onboarding_esc_technical') ?></span>
                                </label>
                                <label class="mandatory-chip esc-chip">
                                    <input type="checkbox" name="escalation_triggers[]" value="manager">
                                    <span><?= _l('cc_onboarding_esc_manager') ?></span>
                                </label>
                            </div>
                            <div class="form-group tw-mt-3">
                                <textarea id="escalation" name="escalation" rows="2" placeholder="<?= _l('cc_onboarding_escalation_ph') ?>"></textarea>
                            </div>
                        </div>
                        <div class="form-group tw-mt-4">
                            <label><?= _l('cc_onboarding_decision_criteria') ?></label>
                            <small class="tw-block tw-mb-2"><?= _l('cc_onboarding_decision_hint') ?></small>
                            <div class="mandatory-grid">
                                <label class="mandatory-chip dec-chip">
                                    <input type="checkbox" name="decision_criteria[]" value="urgency">
                                    <span><?= _l('cc_onboarding_dec_urgency') ?></span>
                                </label>
                                <label class="mandatory-chip dec-chip">
                                    <input type="checkbox" name="decision_criteria[]" value="first_come">
                                    <span><?= _l('cc_onboarding_dec_first_come') ?></span>
                                </label>
                                <label class="mandatory-chip dec-chip">
                                    <input type="checkbox" name="decision_criteria[]" value="vip">
                                    <span><?= _l('cc_onboarding_dec_vip') ?></span>
                                </label>
                                <label class="mandatory-chip dec-chip">
                                    <input type="checkbox" name="decision_criteria[]" value="fast_urgent">
                                    <span><?= _l('cc_onboarding_dec_fast_urgent') ?></span>
                                </label>
                                <label class="mandatory-chip dec-chip">
                                    <input type="checkbox" name="decision_criteria[]" value="flexible">
                                    <span><?= _l('cc_onboarding_dec_flexible') ?></span>
                                </label>
                            </div>
                            <div class="form-group tw-mt-3">
                                <textarea id="decision_criteria_extra" name="decision_criteria_extra" rows="2" placeholder="<?= _l('cc_onboarding_decision_criteria_ph') ?>"></textarea>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Step 11: Assistant Capabilities -->
                <section class="wizard-step" data-step="11">
                    <div class="step-header">
                        <span class="step-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
                        <h2><?= _l('cc_onboarding_step11_title') ?></h2>
                        <p><?= _l('cc_onboarding_step11_desc') ?></p>
                    </div>
                    <div class="step-body">
                        <div class="capability-grid" id="capabilityGrid"></div>
                    </div>
                </section>
            </form>
        </main>

        <footer class="wizard-footer">
            <button type="button" class="btn btn-outline" id="btnPrev" disabled><i class="fa-solid fa-arrow-left"></i> <?= _l('cc_onboarding_btn_back') ?></button>
            <button type="button" class="btn btn-primary" id="btnNext"><?= _l('cc_onboarding_btn_next') ?> <i class="fa-solid fa-arrow-right"></i></button>
            <button type="button" class="btn btn-success" id="btnSubmit" style="display:none"><?= _l('cc_onboarding_btn_submit') ?> <i class="fa-solid fa-check"></i></button>
        </footer>
    </div>

    <div id="successModal" class="modal-overlay" style="display:none">
        <div class="modal-card success-modal">
            <div class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
            <h3><?= _l('cc_onboarding_success_title') ?></h3>
            <p><?= _l('cc_onboarding_success_message') ?></p>
        </div>
    </div>
</div>

<script src="https://cdn.lordicon.com/lordicon-1.2.0.js"></script>
<script>
var ASSISTANT_CAPABILITIES = <?= json_encode([
    ['id' => 'get_lead_info', 'label' => _l('cc_onboarding_cap_get_lead_info'), 'desc' => _l('cc_onboarding_cap_get_lead_info_desc'), 'icon' => 'fa-user'],
    ['id' => 'get_lead_context', 'label' => _l('cc_onboarding_cap_get_lead_context'), 'desc' => _l('cc_onboarding_cap_get_lead_context_desc'), 'icon' => 'fa-clock-rotate-left'],
    ['id' => 'manage_conversation', 'label' => _l('cc_onboarding_cap_manage_conversation'), 'desc' => _l('cc_onboarding_cap_manage_conversation_desc'), 'icon' => 'fa-user-group'],
    ['id' => 'update_leads', 'label' => _l('cc_onboarding_cap_update_leads'), 'desc' => _l('cc_onboarding_cap_update_leads_desc'), 'icon' => 'fa-pen'],
    ['id' => 'get_horario_agenda', 'label' => _l('cc_onboarding_cap_get_horario_agenda'), 'desc' => _l('cc_onboarding_cap_get_horario_agenda_desc'), 'icon' => 'fa-calendar-check'],
    ['id' => 'create_contract', 'label' => _l('cc_onboarding_cap_create_contract'), 'desc' => _l('cc_onboarding_cap_create_contract_desc'), 'icon' => 'fa-file-signature'],
    ['id' => 'get_tabela_precos', 'label' => _l('cc_onboarding_cap_get_tabela_precos'), 'desc' => _l('cc_onboarding_cap_get_tabela_precos_desc'), 'icon' => 'fa-tags'],
    ['id' => 'open_ticket', 'label' => _l('cc_onboarding_cap_open_ticket'), 'desc' => _l('cc_onboarding_cap_open_ticket_desc'), 'icon' => 'fa-ticket'],
    ['id' => 'get_faturas_axiom', 'label' => _l('cc_onboarding_cap_get_faturas_axiom'), 'desc' => _l('cc_onboarding_cap_get_faturas_axiom_desc'), 'icon' => 'fa-file-invoice'],
    ['id' => 'send_media', 'label' => _l('cc_onboarding_cap_send_media'), 'desc' => _l('cc_onboarding_cap_send_media_desc'), 'icon' => 'fa-image'],
    ['id' => 'create_group_chat', 'label' => _l('cc_onboarding_cap_create_group_chat'), 'desc' => _l('cc_onboarding_cap_create_group_chat_desc'), 'icon' => 'fa-users']
]) ?>;
var SITE_URL = '<?= rtrim(site_url(), '/') ?>';
var FORM_TOKEN = '<?= htmlspecialchars($token ?? '') ?>';
var SAVED_FORM_DATA = <?= !empty($saved_data) ? json_encode($saved_data) : 'null' ?>;
var LANG_SENDING = '<?= _l('cc_onboarding_sending') ?>';
var LANG_SAVING = '<?= _l('cc_onboarding_saving') ?>';
var LANG_SAVED = '<?= _l('cc_onboarding_saved') ?>';
var LANG_SAVE_FAILED = '<?= _l('cc_onboarding_save_failed') ?>';
var LANG_SERVICE_NAME = '<?= htmlspecialchars(_l('cc_onboarding_service_name')) ?>';
var LANG_SERVICE_PRICE = '<?= htmlspecialchars(_l('cc_onboarding_service_price')) ?>';
</script>
<script src="<?= base_url('modules/contactcenter/assets/js/assistant_onboarding_wizard.js') ?>"></script>
</body>
</html>
