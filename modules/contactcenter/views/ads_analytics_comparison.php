<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg">
                            <i class="fa fa-balance-scale"></i> <?php echo _l('ads_analytics_comparison'); ?>
                        </h4>
                        <hr class="hr-panel-separator" />
                        
                        <form method="get" action="<?= admin_url('contactcenter/ads_analytics_comparison'); ?>">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Selecione Criativos para Comparar (2-4)</label>
                                        <select name="creative_ids[]" class="selectpicker" data-width="100%" multiple data-max-options="4" data-live-search="true">
                                            <?php if (isset($creatives) && !empty($creatives)) { ?>
                                                <?php foreach ($creatives as $creative) { ?>
                                                    <option value="<?php echo $creative->id; ?>" <?php echo (isset($selected_creative_ids) && in_array($creative->id, $selected_creative_ids)) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($creative->name); ?>
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
                                            <i class="fa fa-search"></i> Comparar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <?php if (isset($comparison_data) && !empty($comparison_data)) { ?>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Comparação de Métricas</h4>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table comparison-table">
                                            <thead>
                                                <tr>
                                                    <th>Métrica</th>
                                                    <?php foreach ($comparison_data as $data) { ?>
                                                        <th><?php echo htmlspecialchars($data['name']); ?></th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong>Leads</strong></td>
                                                    <?php foreach ($comparison_data as $data) { ?>
                                                        <td><?php echo $data['leads']; ?></td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td><strong>Convertidos</strong></td>
                                                    <?php foreach ($comparison_data as $data) { ?>
                                                        <td><?php echo $data['converted']; ?></td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td><strong>Taxa de Conversão</strong></td>
                                                    <?php foreach ($comparison_data as $data) { ?>
                                                        <td><?php echo number_format($data['conversion_rate'], 2); ?>%</td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td><strong>CPL</strong></td>
                                                    <?php foreach ($comparison_data as $data) { ?>
                                                        <td><?php echo $data['cpl'] !== null ? 'R$ ' . number_format($data['cpl'], 2, ',', '.') : 'N/A'; ?></td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td><strong>Investimento Total</strong></td>
                                                    <?php foreach ($comparison_data as $data) { ?>
                                                        <td>R$ <?php echo number_format($data['total_invested'], 2, ',', '.'); ?></td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <td><strong>ROI</strong></td>
                                                    <?php foreach ($comparison_data as $data) { ?>
                                                        <td><?php echo number_format($data['roi'], 2); ?>%</td>
                                                    <?php } ?>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
