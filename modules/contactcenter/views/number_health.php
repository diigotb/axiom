<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                            <lord-icon src="https://cdn.lordicon.com/wzrwaorf.json" trigger="loop" delay="2000" colors="primary:#00e09b,secondary:#00e09b" style="width:50px;height:50px">
                            </lord-icon>
                            <span><?php echo _l('number_health_title'); ?></span>
                        </h4>
                        <hr class="hr-panel-separator" />
                        <p class="text-muted">
                            <?php echo _l('number_health_overview'); ?> - O sistema monitora automaticamente a saúde de cada dispositivo WhatsApp baseado na taxa de mensagens enviadas/recebidas.
                        </p>
                        
                        <!-- Quick Warm-up Section -->
                        <div class="alert alert-info" style="margin-bottom: 20px;">
                            <h4 style="margin-top: 0;">
                                <i class="fa fa-fire"></i> Quick Warm-up - Aquecimento Rápido
                            </h4>
                            <p>
                                Use o botão <strong>"Quick Warm-up"</strong> na tabela abaixo para ativar o aquecimento automático de qualquer dispositivo. 
                                O sistema irá:
                            </p>
                            <ul>
                                <li>✅ Criar scripts de maturação pré-configurados automaticamente</li>
                                <li>✅ Ativar o modo de aquecimento do dispositivo</li>
                                <li>✅ Iniciar conversas automáticas entre dispositivos</li>
                                <li>✅ Monitorar e melhorar a saúde do número</li>
                            </ul>
                            <p class="text-muted">
                                <small><i class="fa fa-info-circle"></i> Os scripts de maturação são criados apenas uma vez. Após a primeira execução, você pode editá-los ou adicionar novos na seção abaixo.</small>
                            </p>
                        </div>
                        
                        <div class="panel_s">
                            <table id="number_health_table" class="table">
                                <thead>
                                    <tr>
                                        <th><?= _l("number_health_device"); ?></th>
                                        <th><?= _l("number_health_sent_24h"); ?></th>
                                        <th><?= _l("number_health_received_24h"); ?></th>
                                        <th><?= _l("number_health_ratio"); ?></th>
                                        <th><?= _l("number_health_score"); ?></th>
                                        <th><?= _l("number_health_status"); ?></th>
                                        <th><?= _l("number_health_actions"); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($devices as $device) {
                                        $msgs_sent = isset($device->msgs_sent_24h) ? (int)$device->msgs_sent_24h : 0;
                                        $msgs_received = isset($device->msgs_received_24h) ? (int)$device->msgs_received_24h : 0;
                                        
                                        $ratio = $msgs_received > 0 ? ($msgs_sent / $msgs_received) : ($msgs_sent > 0 ? 999 : 0);
                                        $health_score = isset($device->health_score) ? (float)$device->health_score : $ratio;
                                        
                                        // Determine status
                                        if ($health_score < 3.0) {
                                            $status_class = 'success';
                                            $status_text = _l('number_health_healthy');
                                        } elseif ($health_score < 5.0) {
                                            $status_class = 'warning';
                                            $status_text = _l('number_health_warning');
                                        } else {
                                            $status_class = 'danger';
                                            $status_text = _l('number_health_critical');
                                        }
                                        
                                        // Status mode
                                        $status_mode = isset($device->status_mode) ? $device->status_mode : 'active';
                                        $mode_text = '';
                                        switch($status_mode) {
                                            case 'active':
                                                $mode_text = _l('number_health_active');
                                                break;
                                            case 'warming_up':
                                                $mode_text = _l('number_health_warming_up');
                                                break;
                                            case 'cooldown':
                                                $mode_text = _l('number_health_cooldown');
                                                break;
                                        }
                                        
                                        echo "<tr>
                                            <td><strong>{$device->dev_name}</strong><br><small>{$device->dev_number}</small></td>
                                            <td>{$msgs_sent}</td>
                                            <td>{$msgs_received}</td>
                                            <td>" . number_format($ratio, 2) . "</td>
                                            <td><span class='label label-{$status_class}'>" . number_format($health_score, 2) . "</span></td>
                                            <td><span class='label label-info'>{$mode_text}</span><br><small class='text-{$status_class}'>{$status_text}</small></td>
                                            <td>
                                                <button class='btn btn-sm btn-default' onclick='generateWaMeLink({$device->dev_id})' title='" . _l('number_health_generate_link') . "'>
                                                    <i class='fa fa-link'></i>
                                                </button>
                                                <button class='btn btn-sm btn-info' onclick='quickWarmup({$device->dev_id})' title='Quick Warm-up (Ativação Rápida)'>
                                                    <i class='fa fa-fire'></i> Quick Warm-up
                                                </button>
                                                " . ($status_mode == 'active' ? 
                                                    "<button class='btn btn-sm btn-warning' onclick='pauseCampaigns({$device->dev_id})' title='" . _l('number_health_pause_campaigns') . "'>
                                                        <i class='fa fa-pause'></i>
                                                    </button>" : 
                                                    "<button class='btn btn-sm btn-success' onclick='resumeCampaigns({$device->dev_id})' title='" . _l('number_health_resume_campaigns') . "'>
                                                        <i class='fa fa-play'></i>
                                                    </button>") . "
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Safe Groups Section -->
                        <div class="panel_s" style="margin-top: 20px;">
                            <h4>Safe Groups (Grupos Seguros)</h4>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#safeGroupModal">
                                <i class="fa fa-plus"></i> Adicionar Grupo Seguro
                            </button>
                            <table id="safe_groups_table" class="table" style="margin-top: 10px;">
                                <thead>
                                    <tr>
                                        <th>Nome do Grupo</th>
                                        <th>ID do Grupo</th>
                                        <th>Dispositivo</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($safe_groups) && !empty($safe_groups)) {
                                        foreach ($safe_groups as $group) {
                                            // Find device name
                                            $device_name = 'N/A';
                                            foreach ($devices as $device) {
                                                if ($device->dev_id == $group->device_id) {
                                                    $device_name = $device->dev_name;
                                                    break;
                                                }
                                            }
                                            echo "<tr>
                                                <td>{$group->group_name}</td>
                                                <td><small>{$group->group_id}</small></td>
                                                <td>{$device_name}</td>
                                                <td><span class='label label-success'>Ativo</span></td>
                                                <td>
                                                    <button class='btn btn-sm btn-danger' onclick='deleteSafeGroup({$group->id})'>
                                                        <i class='fa fa-trash'></i>
                                                    </button>
                                                </td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>Nenhum grupo seguro cadastrado.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Device Pairs Section -->
                        <div class="panel_s" style="margin-top: 20px;">
                            <h4>
                                <i class="fa fa-users"></i> Pares de Dispositivos (Auto-Chat)
                            </h4>
                            <p class="text-muted">
                                Configure quais dispositivos podem conversar entre si durante o processo de maturação.
                            </p>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#devicePairModal">
                                <i class="fa fa-plus"></i> Adicionar Par de Dispositivos
                            </button>
                            <table id="device_pairs_table" class="table" style="margin-top: 10px;">
                                <thead>
                                    <tr>
                                        <th>Dispositivo A</th>
                                        <th>Dispositivo B</th>
                                        <th>Prioridade</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($device_pairs) && !empty($device_pairs)) {
                                        foreach ($device_pairs as $pair) {
                                            // Find device names
                                            $device_a_name = 'N/A';
                                            $device_b_name = 'N/A';
                                            foreach ($devices as $device) {
                                                if ($device->dev_id == $pair->device_a_id) {
                                                    $device_a_name = $device->dev_name;
                                                }
                                                if ($device->dev_id == $pair->device_b_id) {
                                                    $device_b_name = $device->dev_name;
                                                }
                                            }
                                            $status_class = $pair->is_active == 1 ? 'success' : 'default';
                                            $status_text = $pair->is_active == 1 ? 'Ativo' : 'Inativo';
                                            echo "<tr>
                                                <td><strong>{$device_a_name}</strong></td>
                                                <td><strong>{$device_b_name}</strong></td>
                                                <td>{$pair->priority}</td>
                                                <td><span class='label label-{$status_class}'>{$status_text}</span></td>
                                                <td>
                                                    <button class='btn btn-sm btn-danger' onclick='deleteDevicePair({$pair->id})'>
                                                        <i class='fa fa-trash'></i>
                                                    </button>
                                                    <button class='btn btn-sm btn-" . ($pair->is_active == 1 ? 'warning' : 'success') . "' onclick='toggleDevicePair({$pair->id}, " . ($pair->is_active == 1 ? 0 : 1) . ")'>
                                                        <i class='fa fa-" . ($pair->is_active == 1 ? 'pause' : 'play') . "'></i>
                                                    </button>
                                                </td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>Nenhum par configurado. O sistema usará seleção aleatória.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Maturation Scripts Section -->
                        <div class="panel_s" style="margin-top: 20px;">
                            <h4><?= _l('number_health_maturation_scripts'); ?></h4>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#scriptModal">
                                <i class="fa fa-plus"></i> <?= _l('number_health_add_script'); ?>
                            </button>
                            <table id="scripts_table" class="table" style="margin-top: 10px;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th><?= _l('number_health_script_category'); ?></th>
                                        <th>Diálogo</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($scripts) && !empty($scripts)) {
                                        foreach ($scripts as $script) {
                                            $dialogue = json_decode($script->dialogue_json, true);
                                            $dialogue_preview = is_array($dialogue) ? count($dialogue) . ' mensagens' : 'N/A';
                                            echo "<tr>
                                                <td>{$script->id}</td>
                                                <td>{$script->category}</td>
                                                <td>{$dialogue_preview}</td>
                                                <td>
                                                    <button class='btn btn-sm btn-danger' onclick='deleteScript({$script->id})'>
                                                        <i class='fa fa-trash'></i>
                                                    </button>
                                                </td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center'>Nenhum script cadastrado. Clique em 'Adicionar Script' para criar um.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Device Pair -->
<div class="modal fade" id="devicePairModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Adicionar Par de Dispositivos</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open(admin_url('contactcenter/add_device_pair')); ?>
                <div class="form-group">
                    <label>Dispositivo A</label>
                    <select name="device_a_id" class="form-control" required>
                        <option value="">Selecione...</option>
                        <?php
                        foreach ($devices as $device) {
                            echo "<option value='{$device->dev_id}'>{$device->dev_name} ({$device->dev_number})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Dispositivo B</label>
                    <select name="device_b_id" class="form-control" required>
                        <option value="">Selecione...</option>
                        <?php
                        foreach ($devices as $device) {
                            echo "<option value='{$device->dev_id}'>{$device->dev_name} ({$device->dev_number})</option>";
                        }
                        ?>
                    </select>
                    <small class="text-muted">Este dispositivo conversará com o Dispositivo A</small>
                </div>
                <div class="form-group">
                    <label>Prioridade</label>
                    <input type="number" name="priority" class="form-control" value="0" min="0" max="100">
                    <small class="text-muted">Pares com maior prioridade serão usados primeiro (0 = menor prioridade)</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Safe Group -->
<div class="modal fade" id="safeGroupModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Adicionar Grupo Seguro</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open(admin_url('contactcenter/add_safe_group')); ?>
                <div class="form-group">
                    <label>Nome do Grupo</label>
                    <input type="text" name="group_name" class="form-control" required placeholder="Nome do grupo">
                </div>
                <div class="form-group">
                    <label>ID do Grupo (WhatsApp)</label>
                    <input type="text" name="group_id" class="form-control" required placeholder="120363123456789@g.us">
                    <small class="text-muted">Formato: número@g.us (ex: 120363123456789@g.us)</small>
                </div>
                <div class="form-group">
                    <label>Dispositivo</label>
                    <select name="device_id" class="form-control" required>
                        <option value="">Selecione...</option>
                        <?php
                        foreach ($devices as $device) {
                            echo "<option value='{$device->dev_id}'>{$device->dev_name} ({$device->dev_number})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Maturation Script -->
<div class="modal fade" id="scriptModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?= _l('number_health_add_script'); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open(admin_url('contactcenter/add_maturation_script')); ?>
                <div class="form-group">
                    <label><?= _l('number_health_script_category'); ?></label>
                    <select name="category" class="form-control" required>
                        <option value="casual">Casual</option>
                        <option value="business">Business</option>
                        <option value="greetings">Greetings</option>
                        <option value="logistics">Logistics</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><?= _l('number_health_script_dialogue'); ?></label>
                    <textarea name="dialogue_json" class="form-control" rows="10" required placeholder='[{"sender": "A", "text": "Bom dia"}, {"sender": "B", "text": "Bom dia, tudo bem?"}, {"sender": "A", "text": "Tudo certo, e ai?"}]'></textarea>
                    <small class="text-muted">Formato JSON: Array de objetos com "sender" (A ou B) e "text"</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Wa.me Link -->
<div class="modal fade" id="waMeLinkModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?= _l('number_health_wa_me_link'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><?= _l('number_health_wa_me_link'); ?></label>
                    <input type="text" class="form-control" id="wa_me_link_output" readonly>
                </div>
                <div class="form-group">
                    <label>Texto pré-preenchido (opcional)</label>
                    <input type="text" class="form-control" id="wa_me_link_text" placeholder="Olá, gostaria de mais informações">
                </div>
                <button class="btn btn-primary" onclick="regenerateLink()"><?= _l('number_health_generate_link'); ?></button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="copyLink()">Copiar Link</button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    $(document).ready(function() {
        initDataTableInline("#number_health_table");
        
        // Auto-refresh every 30 seconds
        setInterval(function() {
            location.reload();
        }, 30000);
    });

    function generateWaMeLink(deviceId) {
        $('#waMeLinkModal').data('device-id', deviceId);
        $('#waMeLinkModal').modal('show');
        $('#wa_me_link_text').val('');
        generateLink(deviceId);
    }

    function generateLink(deviceId) {
        var text = $('#wa_me_link_text').val();
        $.ajax({
            url: site_url + "contactcenter/generate_wa_me_link",
            data: {
                device_id: deviceId,
                text: text
            },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $('#wa_me_link_output').val(data.link);
                } else {
                    alert(data.message || 'Erro ao gerar link');
                }
            }
        });
    }

    function regenerateLink() {
        var deviceId = $('#waMeLinkModal').data('device-id');
        generateLink(deviceId);
    }

    function copyLink() {
        var linkInput = document.getElementById('wa_me_link_output');
        linkInput.select();
        document.execCommand('copy');
        alert('Link copiado para a área de transferência!');
    }

    function pauseCampaigns(deviceId) {
        if (confirm('Deseja pausar todas as campanhas deste dispositivo?')) {
            // TODO: Implement pause campaigns
            alert('Funcionalidade em desenvolvimento');
        }
    }

    function resumeCampaigns(deviceId) {
        if (confirm('Deseja retomar todas as campanhas deste dispositivo?')) {
            // TODO: Implement resume campaigns
            alert('Funcionalidade em desenvolvimento');
        }
    }

    function deleteScript(scriptId) {
        if (confirm('Deseja realmente excluir este script?')) {
            $.ajax({
                url: site_url + "contactcenter/delete_maturation_script",
                data: { id: scriptId },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro ao excluir script');
                    }
                }
            });
        }
    }

    function deleteSafeGroup(groupId) {
        if (confirm('Deseja realmente excluir este grupo seguro?')) {
            $.ajax({
                url: site_url + "contactcenter/delete_safe_group",
                data: { id: groupId },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro ao excluir grupo');
                    }
                }
            });
        }
    }

    function deleteDevicePair(pairId) {
        if (confirm('Deseja realmente excluir este par de dispositivos?')) {
            $.ajax({
                url: site_url + "contactcenter/delete_device_pair",
                data: { id: pairId },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro ao excluir par');
                    }
                }
            });
        }
    }

    function toggleDevicePair(pairId, newStatus) {
        $.ajax({
            url: site_url + "contactcenter/toggle_device_pair",
            data: { id: pairId, is_active: newStatus },
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao alterar status');
                }
            }
        });
    }

    function quickWarmup(deviceId) {
        if (confirm('Deseja ativar o Quick Warm-up para este dispositivo?\n\nIsso irá:\n- Criar scripts de maturação pré-configurados\n- Ativar modo de aquecimento\n- Configurar parâmetros seguros')) {
            $.ajax({
                url: site_url + "contactcenter/quick_warmup",
                data: { device_id: deviceId },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        alert('Quick Warm-up ativado com sucesso!\n\n' + data.message);
                        location.reload();
                    } else {
                        alert('Erro: ' + (data.message || 'Erro ao ativar warm-up'));
                    }
                },
                error: function() {
                    alert('Erro ao comunicar com o servidor');
                }
            });
        }
    }
</script>
</body>
</html>

