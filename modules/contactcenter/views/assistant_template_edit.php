<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg">
                            <i class="fa fa-object-group tw-mr-2"></i>
                            <?= $template ? _l("contac_assistant_template_edit") : _l("contac_assistant_template_new"); ?>
                        </h4>
                        <hr class="hr-panel-separator" />
                        <?php echo form_open_multipart(admin_url('contactcenter/save_assistant_template'), ['id' => 'template-form']); ?>
                        <?php if ($template): ?>
                            <input type="hidden" name="id" value="<?= (int)$template->id ?>">
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= _l("contac_assistant_template_name"); ?></label>
                                    <input type="text" name="name" class="form-control" value="<?= $template ? htmlspecialchars($template->name) : '' ?>" required>
                                </div>
                                <div class="form-group">
                                    <label><?= _l("contac_assistant_template_description"); ?></label>
                                    <textarea name="description" class="form-control" rows="3"><?= $template ? htmlspecialchars($template->description) : '' ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label><?= _l("contac_assistant_template_image"); ?></label>
                                    <?php if ($template && !empty($template->image_path)): ?>
                                        <div class="tw-mb-2">
                                            <img src="<?= base_url($template->image_path) ?>" alt="" class="tw-w-20 tw-h-20 tw-object-cover tw-rounded-lg tw-border">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="file" class="form-control" accept="image/gif,image/jpeg,image/jpg,image/png">
                                    <p class="text-muted small tw-mt-1"><?= _l("contac_assistant_template_image_help"); ?></p>
                                </div>
                                <div class="form-group">
                                    <label><?= _l("contac_assistant_template_icon"); ?></label>
                                    <input type="text" name="icon" class="form-control" placeholder="fa-spa, fa-briefcase, fa-robot" value="<?= $template ? htmlspecialchars($template->icon) : 'fa-robot' ?>">
                                    <p class="text-muted small tw-mt-1"><?= _l("contac_assistant_template_icon_help"); ?></p>
                                </div>
                                <div class="form-group">
                                    <label><?= _l("contac_assistant_template_model"); ?></label>
                                    <select name="model" class="form-control">
                                        <option value="gpt-4o-mini" <?= ($template && $template->model == 'gpt-4o-mini') ? 'selected' : '' ?>>gpt-4o-mini</option>
                                        <option value="gpt-4o" <?= ($template && $template->model == 'gpt-4o') ? 'selected' : '' ?>>gpt-4o</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><?= _l("contac_assistant_template_functions"); ?></label>
                                    <div class="tw-flex tw-flex-wrap tw-gap-2">
                                        <?php
                                        $tpl_functions = $template && $template->functions ? (array)json_decode($template->functions) : [];
                                        foreach ($available_functions as $fn => $label):
                                            $checked = in_array($fn, $tpl_functions) ? 'checked' : '';
                                        ?>
                                            <label class="tw-inline-flex tw-items-center tw-gap-1">
                                                <input type="checkbox" name="functions[]" value="<?= htmlspecialchars($fn) ?>" <?= $checked ?>>
                                                <span><?= htmlspecialchars($label) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= _l("contac_assistant_template_instructions"); ?></label>
                                    <textarea name="instructions" class="form-control" rows="20" required><?= $template ? htmlspecialchars($template->instructions) : '' ?></textarea>
                                    <p class="text-muted small"><?= _l("contac_assistant_template_instructions_help"); ?></p>
                                </div>
                            </div>
                        </div>
                        <hr class="hr-panel-separator" />
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check tw-mr-1"></i> <?= _l("save"); ?>
                        </button>
                        <a href="<?= admin_url('contactcenter/assistant_templates') ?>" class="btn btn-default"><?= _l("cancel"); ?></a>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
