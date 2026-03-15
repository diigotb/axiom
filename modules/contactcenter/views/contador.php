<?php
defined('BASEPATH') or exit('No direct script access allowed');
init_head();

// Get CI instance for database operations
$CI = &get_instance();

function carrega_user($UserId = "")
{
    $CI = &get_instance();
    $option = "";
    $query = $CI->db->query("SELECT * FROM tblstaff ORDER BY firstname ASC");
    $FOR = $query->result_array();
    foreach ($FOR as $REG):
        extract($REG);
        $sel = ($staffid == $UserId ? "selected" : "");
        $option = $option . "<option value='{$REG['staffid']}' {$sel} >{$REG['firstname']} {$REG['lastname']}</option>";
    endforeach;
    return $option;
}
$option = carrega_user();


function carrega_grupo($GrupId = "")
{
    $CI = &get_instance();
    $option = "";
    $query = $CI->db->query("SELECT * FROM tblleads_grupo ORDER BY grup_name ASC");
    $FOR = $query->result_array();
    foreach ($FOR as $REG):
        extract($REG);
        $sel = ($grup_id == $GrupId ? "selected" : "");
        $option = $option . "<option value='{$REG['grup_id']}' {$sel} >{$REG['grup_name']}</option>";
    endforeach;
    return $option;
}
$option_grupo = carrega_grupo();


function carrega_camapanha($CampId = "")
{
    $custom_fields = get_custom_fields('leads', ['slug' => 'leads_unidade', 'active' => 1]);

    $CI = &get_instance();
    $option = "";
    // Use DISTINCT instead of GROUP BY to avoid sql_mode=only_full_group_by error
    $query = $CI->db->query("SELECT DISTINCT value FROM tblcustomfieldsvalues WHERE fieldid={$custom_fields[0]['id']} AND value != '' AND value IS NOT NULL ORDER BY value ASC");
    $FOR = $query->result_array();
    foreach ($FOR as $REG):
        extract($REG);
        $sel = ($value == $CampId ? "selected" : "");
        $option = $option . "<option value='{$REG['value']}' {$sel} >{$REG['value']}</option>";
    endforeach;
    return $option;
}
$option_campanha = carrega_camapanha();

$msg = '';

$form = filter_input_array(INPUT_POST, FILTER_DEFAULT);
date_default_timezone_set('America/Sao_Paulo');

if ($form):
    switch ($form['action']):
        case ('delete');
            unset($form['action']);
            $ContId = $form['cont_id'];
            $CI->db->where('cont_id', $ContId);
            $CI->db->delete('tblleads_cont');
            header('Location: ' . base_url() . 'contactcenter/contador');
            break;
        case ('delete_camp');
            unset($form['action']);
            $GitemId = $form['gitem_id'];
            $CI->db->where('gitem_id', $GitemId);
            $CI->db->delete('tblleads_grupo_item');
            header('Location: ' . base_url() . 'contactcenter/contador');
            break;
        case ('delete_grupo');
            unset($form['action']);
            $GrupoId = $form['grup_id'];

            $query = $CI->db->query("SELECT * FROM tblleads_cont WHERE grup_id=$GrupoId");
            if ($query->result_array()):
                $msg = AjaxErro("<span class='msg_erro'><b>ERRO AO DELETAR: </b> Você Não Pode Deletar Este Grupo, Ele Está Atrelado Em Algum Atendente!</span>");
                break;
            else:
                $CI->db->where('grup_id', $GrupoId);
                $CI->db->delete('tblleads_grupo');
                $CI->db->where('grup_id', $GrupoId);
                $CI->db->delete('tblleads_grupo_item');

            //   header('Location: '. base_url().'axiom/axiom');                       
            endif;

            break;
        case ('crear');
            unset($form['action']);
            unset($form['csrf_token_name']);
            $form['cont_vezes'] = '0';
            $form['cont_data'] = date('Y-m-d H:i:s');
            $CI->db->insert('tblleads_cont', $form);
            header('Location: ' . base_url() . 'contactcenter/contador');

            break;
        case ('zerar');
            unset($form['action']);
            unset($form['csrf_token_name']);
            $ContId = $form['cont_id'];
            unset($form['cont_id']);
            $form['cont_vezes'] = '0';
            $CI->db->where('cont_id', $ContId);
            $CI->db->update('tblleads_cont', $form);
            header('Location: ' . base_url() . 'contactcenter/contador');

            break;
        case ('grupo');
            unset($form['action']);
            unset($form['csrf_token_name']);
            $CI->db->insert('tblleads_grupo', $form);
            header('Location: ' . base_url() . 'contactcenter/contador');

            break;
        case ('campanha');
            unset($form['action']);
            unset($form['csrf_token_name']);
            $CI->db->insert('tblleads_grupo_item', $form);
            header('Location: ' . base_url() . 'contactcenter/contador');

            break;
    endswitch;
else:

endif;







?>


<!-------------------TABELA DE USUARIO ---------------------------------->
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- PAINEL  --->
                        <div class="box_titulo">
                            <span class="titulo_w">Adicionar Grupos</span>
                        </div>
                        <div class="div_form" style="padding: 15px; margin-bottom: 20px;">
                            <!--- FORM PARA CREAR grupos  -->
                            <?php echo form_open(''); ?>
                            <input type='hidden' name='action' value='grupo'>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Grupo:</strong></label>
                                        <input type="text" class="form-control" name="grup_name" placeholder="Digite o nome do grupo" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label" style="visibility: hidden;">Ação</label>
                                        <button type="submit" id="btn_grupo" class="btn btn-info btn-block">
                                            <i class="fa fa-plus tw-mr-1"></i> Adicionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                        <!-- PAINEL  --->
                        <div class="box_titulo col-md-12">
                            <span class="titulo_w">Adicionar Campanha</span>
                            <?= $msg; ?>
                        </div>
                        <div class="col-md-12 mb-4" style="margin-bottom: 20px; padding: 15px;">
                            <!--- FORM PARA CREAR camapanha  -->
                            <?php echo form_open(''); ?>
                            <input type='hidden' name='action' value='campanha'>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Grupos:</strong></label>
                                        <select class="form-control selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" name='grup_id' required>
                                            <option value=''></option>
                                            <?php echo $option_grupo; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Campanhas:</strong></label>
                                        <select class="form-control selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" name='gitem_name' required>
                                            <option value=''></option>
                                            <?php echo $option_campanha; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label" style="visibility: hidden;">Ação</label>
                                        <button type="submit" class="btn btn-info btn-block">
                                            <i class="fa fa-plus tw-mr-1"></i> Adicionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                        <div class="tetes">
                            <span></span>
                            <table class='table '>
                                <thead class='bg_azul'>
                                    <tr>
                                        <th>Nome:</th>
                                        <th>Campanha:</th>
                                        <th>Deleta:</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php


                                    //select campanha
                                    $sql = "SELECT * FROM tblleads_grupo  ORDER BY grup_id ASC ";
                                    $query = $CI->db->query($sql);
                                    $Grup = $query->result_array();

                                    foreach ($Grup as $GR):
                                        extract($GR);

                                        echo "<tr>";
                                        echo "<td>{$grup_name}</td>";
                                        echo "<td class='box_camp'>";
                                        $query = $CI->db->query("SELECT * FROM tblleads_grupo_item WHERE grup_id='{$grup_id}'  ORDER BY grup_id ASC ");
                                        $GrupI = $query->result_array();
                                        foreach ($GrupI as $GRI):
                                            extract($GRI);

                                            echo form_open('');
                                            echo "<input type='hidden' name='action' value='delete_camp'>
                        <input type='hidden' name='gitem_id' value='{$gitem_id}'>
                        <button type='submit' name='deletar' data-toggle='tooltip' data-title='Deletar {$gitem_name}?'  class='btn_camp btn btn-primary'>{$gitem_name}</button>";
                                            echo form_close();

                                        endforeach;

                                        echo "</td>";
                                        echo "<td>";
                                        echo form_open('');
                                        echo "<input type='hidden' name='action' value='delete_grupo'>
                        <input type='hidden' name='grup_id' value='{$grup_id}'>
                        <button type='submit' data-toggle='tooltip' data-title='Deletar {$grup_name}?' name='deletar'  class='btn btn-primary btn btn-danger id='{$grup_id}'><i class='icon_lixeira'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash' viewBox='0 0 16 16'>
                        <path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z'/>
                        <path fill-rule='evenodd' d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z'/>
                        </svg></i></button>";
                                        echo form_close();
                                        echo "</td>";
                                        echo "</tr>";
                                    endforeach;
                                    echo "</tbody>";
                                    echo "</table>";

                                    ?>

                        </div>

                        <!-- PAINEL  --->
                        <div class="box_titulo">
                            <span class="titulo_w">Contador de Atribuição Automático</span>
                        </div>
                        <div style="padding: 15px; margin-bottom: 20px;">
                            <!--- FORM PARA CREAR A TABELA  -->
                            <?php echo form_open(''); ?>
                            <input type='hidden' name='action' value='crear'>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Atendente:</strong></label>
                                        <select class="form-control selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" name='user_id' required>
                                            <option value=''></option>
                                            <?php echo $option; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label"><strong>Grupos:</strong></label>
                                        <select class="form-control selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" name='grup_id' required>
                                            <option value=''></option>
                                            <?php echo $option_grupo; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label" style="visibility: hidden;">Ação</label>
                                        <button type="submit" class="btn btn-info btn-block">
                                            <i class="fa fa-save tw-mr-1"></i> Salvar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                        <div class="tetes">
                            <span></span>
                            <table class='table '>
                                <thead class='bg_azul'>
                                    <tr>
                                        <th>ID:</th>
                                        <th>Atendente:</th>
                                        <th>Contador:</th>
                                        <th>Grupo:</th>
                                        <th>Ultima atribuição:</th>
                                        <th>Zerar Contador:</th>
                                        <th>Excluir:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php


                                    //select da tabela usuario mais grupo
                                    $sql = "SELECT p.firstname,p.lastname,p.staffid,g.*, e.* FROM tblleads_cont e LEFT JOIN tblstaff p on (p.staffid=e.user_id) LEFT JOIN tblleads_grupo g on (g.grup_id=e.grup_id) ORDER BY firstname ASC ";
                                    $query = $CI->db->query($sql);
                                    $atendente = $query->result_array();

                                    foreach ($atendente as $aten):
                                        extract($aten);
                                        $dtfp = ($cont_data == "" ? "" : date('d/m/Y  H:i:s', strtotime($cont_data)));
                                        echo "<tr>";
                                        echo "<td>{$staffid}</td>";
                                        echo "<td>{$firstname} {$lastname}</td>";
                                        echo "<td>{$cont_vezes}</td>";
                                        echo "<td>{$grup_name}</td>";
                                        echo "<td>{$dtfp}</td>";
                                        echo "<td>";
                                        echo form_open('');
                                        echo "<input type='hidden' name='action' value='zerar'>                          
                        <input type='hidden' name='cont_id' value='{$cont_id}'>                             
                        <button type='submit' data-toggle='tooltip' data-title='Zerar Contador do Atendente {$firstname}' class='btn btn-primary'>
                            <i class='fa fa-refresh'></i>
                        </button>";

                                        echo form_close();
                                        echo "</td>";
                                        echo "<td>";
                                        echo form_open('');
                                        echo "<input type='hidden' name='action' value='delete'>
                        <input type='hidden' name='cont_id' value='{$cont_id}'>
                        <button type='submit' data-toggle='tooltip' data-title='Deletar {$firstname}' name='deletar'  class='btn btn-primary btn btn-danger frm_$cont_id' id='{$cont_id}'><i class='icon_lixeira'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash' viewBox='0 0 16 16'>
                      <path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z'/>
                      <path fill-rule='evenodd' d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z'/>
                    </svg></i></button>";
                                        echo form_close();
                                        echo " </td>";
                                        echo "</tr>";
                                    endforeach;
                                    echo "</tbody>";
                                    echo "</table>";

                                    ?>

                        </div>

                        <!-- PAINEL - Campaign Group Management & Stuck Leads --->
                        <div class="box_titulo">
                            <span class="titulo_w">
                                <i class="fa fa-exclamation-triangle tw-mr-1" style="color: #f39c12;"></i>
                                Gerenciamento de Campanhas e Leads Bloqueados
                            </span>
                            <small class="text-muted" style="display: block; margin-top: 5px;">
                                Visualize campanhas sem grupo atribuído e leads bloqueados. Atribua campanhas a grupos para desbloquear leads.
                            </small>
                        </div>

                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="background-color: #f8f9fa;">
                                        <h4 class="panel-title">
                                            <i class="fa fa-link tw-mr-1"></i> Atribuir Campanha a Grupo
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <?php echo form_open('', ['id' => 'assign-campaign-form']); ?>
                                        <input type='hidden' name='action' value='campanha'>
                                        <div class="form-group">
                                            <label class="control-label">Grupo:</label>
                                            <select class="form-control selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" name='grup_id' id='assign_grup_id' required>
                                                <option value=''></option>
                                                <?php echo $option_grupo; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Campanha:</label>
                                            <select class="form-control selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" name='gitem_name' id='assign_gitem_name' required>
                                                <option value=''></option>
                                                <?php echo $option_campanha; ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-plus tw-mr-1"></i> Atribuir Campanha ao Grupo
                                        </button>
                                        <?php echo form_close(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div id="stuck-leads-container">
                                    <div class="panel panel-warning">
                                        <div class="panel-heading" style="background-color: #f39c12; color: white;">
                                            <h4 class="panel-title">
                                                <i class="fa fa-exclamation-triangle tw-mr-1"></i>
                                                Leads Bloqueados
                                                <span id="stuck-leads-count" class="badge" style="background-color: #fff; color: #f39c12; margin-left: 10px;">Carregando...</span>
                                            </h4>
                                        </div>
                                        <div class="panel-body">
                                            <div id="stuck-leads-loading" class="text-center" style="padding: 20px;">
                                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                                <p>Carregando leads bloqueados...</p>
                                            </div>

                                            <!-- Date Filter - Always visible -->
                                            <div id="stuck-leads-filters" style="margin-bottom: 15px; padding: 10px; background-color: #34495e; border-radius: 4px; display: none;">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label style="color: #ecf0f1; margin-bottom: 5px; display: block;">
                                                            <i class="fa fa-filter tw-mr-1"></i>Filtrar por período:
                                                        </label>
                                                        <select id="stuck-leads-date-filter" class="selectpicker" data-width="100%" data-style="btn-default">
                                                            <option value="7" selected>Últimos 7 dias</option>
                                                            <option value="1">Hoje</option>
                                                            <option value="15">Últimos 15 dias</option>
                                                            <option value="30">Últimos 30 dias</option>
                                                            <option value="all">Todos</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label style="color: #ecf0f1; margin-bottom: 5px; display: block;">
                                                            <i class="fa fa-search tw-mr-1"></i>Buscar por nome ou ID:
                                                        </label>
                                                        <input type="text" id="stuck-leads-search" class="form-control" placeholder="Digite para buscar..." style="background-color: #2c3e50; color: #ecf0f1; border-color: #4a5f7a;">
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="stuck-leads-content" style="display: none;">
                                                <p class="text-muted">
                                                    <i class="fa fa-info-circle tw-mr-1"></i>
                                                    Estes leads possuem uma campanha (unidade) atribuída, mas nenhum grupo está configurado para essa campanha.
                                                    Atribua a campanha a um grupo acima ou diretamente na tabela para desbloquear os leads.
                                                </p>

                                                <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                                                    <div id="stuck-leads-list"></div>
                                                </div>
                                            </div>
                                            <div id="stuck-leads-empty" style="display: none;" class="alert alert-success">
                                                <i class="fa fa-check-circle tw-mr-1"></i>
                                                Nenhum lead bloqueado encontrado! Todas as campanhas estão atribuídas a grupos.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12">
                                <div class="panel panel-info">
                                    <div class="panel-heading" style="background-color: #3498db; color: white;">
                                        <h4 class="panel-title">
                                            <i class="fa fa-sitemap tw-mr-1"></i>
                                            Visão Geral: Grupos e Campanhas
                                        </h4>
                                    </div>
                                    <div class="panel-body" style="background-color: #2c3e50; color: #ecf0f1;">
                                        <div id="campaign-group-overview">
                                            <?php
                                            // Get all groups with their campaigns
                                            $sql_groups = "SELECT g.*, 
                                   (SELECT COUNT(*) FROM tblleads_grupo_item WHERE grup_id = g.grup_id) as campaign_count
                                   FROM tblleads_grupo g ORDER BY g.grup_name ASC";
                                            $query = $CI->db->query($sql_groups);
                                            $all_groups = $query->result_array();

                                            // Get all campaign values from leads
                                            $custom_fields = get_custom_fields('leads', ['slug' => 'leads_unidade', 'active' => 1]);
                                            $all_campaigns = [];
                                            if (!empty($custom_fields)) {
                                                $query = $CI->db->query("SELECT DISTINCT value FROM tblcustomfieldsvalues WHERE fieldid={$custom_fields[0]['id']} AND value != '' ORDER BY value ASC");
                                                $campaign_results = $query->result_array();
                                                foreach ($campaign_results as $camp) {
                                                    $all_campaigns[] = $camp['value'];
                                                }
                                            }

                                            // Get assigned campaigns
                                            $query = $CI->db->query("SELECT DISTINCT gitem_name FROM tblleads_grupo_item");
                                            $assigned_campaigns = [];
                                            foreach ($query->result_array() as $assigned) {
                                                $assigned_campaigns[] = $assigned['gitem_name'];
                                            }

                                            // Find unassigned campaigns
                                            $unassigned_campaigns = array_diff($all_campaigns, $assigned_campaigns);
                                            ?>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5 style="color: #2ecc71;"><i class="fa fa-check-circle tw-mr-1"></i> Campanhas Atribuídas (<?php echo count($assigned_campaigns); ?>)</h5>
                                                    <div style="max-height: 300px; overflow-y: auto; overflow-x: hidden; background-color: #34495e; padding: 15px; border-radius: 4px; word-wrap: break-word;">
                                                        <?php if (!empty($all_groups)): ?>
                                                            <?php foreach ($all_groups as $grp): ?>
                                                                <div class="campaign-group-item" style="margin-bottom: 15px; padding: 10px; border-left: 3px solid #2ecc71; background-color: #3d566e; word-wrap: break-word; overflow-wrap: break-word;">
                                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                                                                        <strong style="color: #ecf0f1; flex: 1; min-width: 120px;"><i class="fa fa-users tw-mr-1"></i><?php echo htmlspecialchars($grp['grup_name']); ?></strong>
                                                                        <span class="badge" style="background-color: #2ecc71; color: #fff; flex-shrink: 0;"><?php echo $grp['campaign_count']; ?> campanha(s)</span>
                                                                    </div>

                                                                    <!-- Staff Members -->
                                                                    <div style="margin-bottom: 10px;">
                                                                        <small style="color: #95a5a6; display: block; margin-bottom: 4px;">
                                                                            <i class="fa fa-user-tie tw-mr-1"></i>Atendentes:
                                                                        </small>
                                                                        <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                                            <?php
                                                                            $query = $CI->db->query("SELECT c.*, s.firstname, s.lastname, s.staffid FROM tblleads_cont c LEFT JOIN tblstaff s ON c.user_id = s.staffid WHERE c.grup_id='{$grp['grup_id']}' ORDER BY s.firstname ASC");
                                                                            $group_staff = $query->result_array();
                                                                            if (!empty($group_staff)):
                                                                                foreach ($group_staff as $staff):
                                                                                    $staff_name = trim(($staff['firstname'] ?? '') . ' ' . ($staff['lastname'] ?? ''));
                                                                                    if (empty($staff_name)) continue;
                                                                            ?>
                                                                                    <span class="label" style="display: inline-block; background-color: #9b59b6; color: #fff; font-size: 11px; white-space: nowrap;">
                                                                                        <i class="fa fa-user tw-mr-1"></i><?php echo htmlspecialchars($staff_name); ?>
                                                                                        <small style="opacity: 0.8;">(<?php echo $staff['cont_vezes']; ?>)</small>
                                                                                    </span>
                                                                                <?php
                                                                                endforeach;
                                                                            else:
                                                                                ?>
                                                                                <span style="color: #95a5a6; font-size: 11px; font-style: italic;">Nenhum atendente atribuído</span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Campaigns -->
                                                                    <div>
                                                                        <small style="color: #95a5a6; display: block; margin-bottom: 4px;">
                                                                            <i class="fa fa-bullhorn tw-mr-1"></i>Campanhas:
                                                                        </small>
                                                                        <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                                                            <?php
                                                                            $query = $CI->db->query("SELECT * FROM tblleads_grupo_item WHERE grup_id='{$grp['grup_id']}' ORDER BY gitem_name ASC");
                                                                            $group_campaigns = $query->result_array();
                                                                            if (!empty($group_campaigns)):
                                                                                foreach ($group_campaigns as $gc):
                                                                            ?>
                                                                                    <span class="label" style="display: inline-block; background-color: #3498db; color: #fff; max-width: 100%; word-break: break-word;">
                                                                                        <?php echo htmlspecialchars($gc['gitem_name']); ?>
                                                                                    </span>
                                                                                <?php
                                                                                endforeach;
                                                                            else:
                                                                                ?>
                                                                                <span style="color: #95a5a6; font-size: 11px; font-style: italic;">Nenhuma campanha atribuída</span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <p style="color: #95a5a6;">Nenhum grupo configurado ainda.</p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5 style="color: #f39c12;"><i class="fa fa-exclamation-triangle tw-mr-1"></i> Campanhas Não Atribuídas (<?php echo count($unassigned_campaigns); ?>)</h5>
                                                    <div style="max-height: 300px; overflow-y: auto; overflow-x: hidden; background-color: #34495e; padding: 15px; border-radius: 4px; word-wrap: break-word;">
                                                        <?php if (!empty($unassigned_campaigns)): ?>
                                                            <?php foreach ($unassigned_campaigns as $unassigned): ?>
                                                                <div class="unassigned-campaign" style="margin-bottom: 8px; padding: 8px; border-left: 3px solid #f39c12; background-color: #4a5f7a;">
                                                                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                                                                        <strong style="color: #ecf0f1; flex: 1; min-width: 150px; word-break: break-word;"><?php echo htmlspecialchars($unassigned); ?></strong>
                                                                        <div style="flex-shrink: 0;">
                                                                            <select class="selectpicker quick-assign-group"
                                                                                data-campaign="<?php echo htmlspecialchars($unassigned); ?>"
                                                                                data-width="180px"
                                                                                data-style="btn-warning btn-xs"
                                                                                data-none-selected-text="Escolher grupo..."
                                                                                style="min-width: 180px;">
                                                                                <option value="">Escolher grupo...</option>
                                                                                <?php
                                                                                $query = $CI->db->query("SELECT * FROM tblleads_grupo ORDER BY grup_name ASC");
                                                                                $all_groups_for_assign = $query->result_array();
                                                                                foreach ($all_groups_for_assign as $grp_assign):
                                                                                ?>
                                                                                    <option value="<?php echo $grp_assign['grup_id']; ?>">
                                                                                        <?php echo htmlspecialchars($grp_assign['grup_name']); ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                            <button class="btn btn-xs btn-warning assign-campaign-btn"
                                                                                data-campaign="<?php echo htmlspecialchars($unassigned); ?>"
                                                                                style="display: none;">
                                                                                <i class="fa fa-plus"></i> Atribuir
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <p style="color: #2ecc71;"><i class="fa fa-check-circle tw-mr-1"></i>Todas as campanhas estão atribuídas a grupos!</p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FIM PAINEL --->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





<?php init_tail(); ?>

<style>
    .bg_azul tr {
        background-color: #546BF1;
    }

    .bg_azul tr th {
        color: #fff !important;
    }

    .ds_none {
        display: none;
    }

    .icon_lixeira {
        font-size: .8em !important;
    }

    /* Removed .select_w width restrictions - using Bootstrap grid instead */
    .btn_salvar {
        margin-top: 10px;
    }

    .box_titulo {
        margin-bottom: 10px;
        border-bottom: 1px solid #ccc;
        padding: 10px;
    }

    .titulo_w {
        font-size: 1.2em;
    }

    #btn_grupo {
        margin-top: -8px;
        margin-left: 10px;
    }

    .div_form {
        margin-bottom: 20px;
    }

    .btn_camp {
        margin: 5px;
    }

    .box_camp {
        display: flex;
        flex-wrap: wrap;
    }

    .msg_erro {
        display: block;
        background-color: red;
        color: #fff;
        padding: 10px;
    }

    /* Responsive fixes */
    @media (max-width: 768px) {
        .row {
            margin-left: -10px;
            margin-right: -10px;
        }

        .col-md-6 {
            padding-left: 10px;
            padding-right: 10px;
            margin-bottom: 15px;
        }

        .campaign-group-item {
            font-size: 13px;
        }

        .unassigned-campaign {
            font-size: 13px;
        }

        .selectpicker {
            width: 100% !important;
        }
    }

    /* Ensure tables don't break layout */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #stuck-leads-list table {
        margin-bottom: 0;
    }

    /* Fix long campaign names */
    .label {
        max-width: 100%;
        word-break: break-word;
        white-space: normal;
    }
</style>
<script>
    $(function() {
        $('.btn_del').click(function() {
            $(this).fadeOut(0);
            var id = $(this).attr('well');
            $('.frm_' + id).css('display', 'block');
        });






    });
</script>

<script>
    $(document).ready(function() {
        // Initialize selectpicker on all selects
        $('.selectpicker').selectpicker();

        // Load stuck leads
        loadStuckLeads();

        // Handle quick assign from dropdown
        $(document).on('change', '.quick-assign-group', function(e) {
            var $select = $(this);
            var campaign = $select.data('campaign');
            var groupId = $select.val();

            if (!groupId) {
                return;
            }

            // Show loading
            $select.prop('disabled', true);
            var originalHtml = $select.html();
            $select.html('<option>Processando...</option>');

            // Quick assign via AJAX
            $.ajax({
                url: '<?php echo admin_url("contactcenter/quick_assign_campaign"); ?>',
                type: 'POST',
                data: {
                    campaign: campaign,
                    group_id: groupId,
                    <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        var message = 'Campanha atribuída com sucesso!';
                        if (response.leads_updated > 0) {
                            message += ' ' + response.leads_updated + ' lead(s) desbloqueado(s) e atribuído(s).';
                        }
                        alert(message);
                        // Reload page to refresh the view
                        location.reload();
                    } else {
                        alert('Erro: ' + (response.message || 'Erro desconhecido'));
                        $select.prop('disabled', false);
                        $select.selectpicker('refresh');
                    }
                },
                error: function() {
                    alert('Erro ao atribuir campanha. Tente novamente.');
                    $select.prop('disabled', false);
                    $select.selectpicker('refresh');
                }
            });
        });

        // Handle assign campaign button from unassigned campaigns (fallback)
        $(document).on('click', '.assign-campaign-btn', function(e) {
            e.preventDefault();
            var campaign = $(this).data('campaign');
            $('#assign_gitem_name').val(campaign).selectpicker('refresh');
            $('#assign_grup_id').focus();
            $('html, body').animate({
                scrollTop: $('#assign-campaign-form').offset().top - 100
            }, 500);
        });

        // Reload stuck leads after form submission
        $('form').on('submit', function() {
            if ($(this).find('input[name="action"]').val() === 'campanha') {
                setTimeout(function() {
                    location.reload();
                }, 500);
            }
        });

        // Handle date filter change
        $(document).on('change', '#stuck-leads-date-filter', function() {
            loadStuckLeads();
        });

        // Handle search input (with debounce)
        var searchTimeout;
        $(document).on('input', '#stuck-leads-search', function() {
            clearTimeout(searchTimeout);
            var $this = $(this);
            searchTimeout = setTimeout(function() {
                loadStuckLeads();
            }, 500); // Wait 500ms after user stops typing
        });

        // Handle quick assign from table row
        $(document).on('change', '.quick-assign-lead-group', function(e) {
            var $select = $(this);
            var leadId = $select.data('lead-id');
            var campaign = $select.data('campaign');
            var groupId = $select.val();

            if (!groupId) {
                return;
            }

            if (!campaign) {
                alert('Erro: Campanha não encontrada para este lead.');
                $select.val('').selectpicker('refresh');
                return;
            }

            // Show loading
            $select.prop('disabled', true);
            var originalVal = $select.val();
            $select.selectpicker('refresh');

            // Quick assign via AJAX
            $.ajax({
                url: '<?php echo admin_url("contactcenter/quick_assign_campaign"); ?>',
                type: 'POST',
                data: {
                    campaign: campaign,
                    group_id: groupId,
                    <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        var message = 'Campanha "' + campaign + '" atribuída com sucesso ao grupo!';
                        if (response.leads_updated > 0) {
                            message += ' ' + response.leads_updated + ' lead(s) desbloqueado(s) e atribuído(s).';
                        }
                        alert(message);
                        // Reload stuck leads to refresh the table
                        loadStuckLeads();
                    } else {
                        alert('Erro: ' + (response.message || 'Erro desconhecido'));
                        $select.val('').selectpicker('refresh');
                        $select.prop('disabled', false);
                    }
                },
                error: function() {
                    alert('Erro ao atribuir campanha. Tente novamente.');
                    $select.val('').selectpicker('refresh');
                    $select.prop('disabled', false);
                }
            });
        });
    });

    function loadStuckLeads() {
        var dateFilter = $('#stuck-leads-date-filter').val() || '7';
        var searchTerm = $('#stuck-leads-search').val() || '';

        $('#stuck-leads-loading').show();
        $('#stuck-leads-content').hide();
        $('#stuck-leads-empty').hide();

        $.ajax({
            url: '<?php echo admin_url("contactcenter/get_stuck_leads"); ?>',
            type: 'GET',
            data: {
                date_filter: dateFilter,
                search: searchTerm
            },
            dataType: 'json',
            success: function(response) {
                $('#stuck-leads-loading').hide();
                $('#stuck-leads-filters').show(); // Always show filters

                if (response.success) {
                    var stuckLeads = response.stuck_leads;
                    var count = stuckLeads.length;

                    $('#stuck-leads-count').text(count);

                    if (count > 0) {
                        $('#stuck-leads-content').show();
                        $('#stuck-leads-empty').hide();

                        // Get groups for quick assign dropdowns
                        var groupsHtml = '';
                        <?php
                        $query = $CI->db->query("SELECT * FROM tblleads_grupo ORDER BY grup_name ASC");
                        $all_groups_for_quick = $query->result_array();
                        ?>
                        var groups = <?php echo json_encode($all_groups_for_quick); ?>;

                        var html = '<table class="table table-striped table-bordered" style="min-width: 1000px; width: 100%;">';
                        html += '<thead><tr>';
                        html += '<th style="min-width: 80px;">ID Lead</th>';
                        html += '<th style="min-width: 200px;">Nome</th>';
                        html += '<th style="min-width: 250px;">Campanha (Unidade)</th>';
                        html += '<th style="min-width: 150px;">Status</th>';
                        html += '<th style="min-width: 120px;">Fonte</th>';
                        html += '<th style="min-width: 200px;">Atribuir a Grupo</th>';
                        html += '<th style="min-width: 100px;">Ações</th>';
                        html += '</tr></thead>';
                        html += '<tbody>';

                        stuckLeads.forEach(function(lead) {
                            html += '<tr data-lead-id="' + lead.id + '" data-campaign="' + (lead.campaign || '').replace(/'/g, "&#39;") + '">';
                            html += '<td>' + lead.id + '</td>';
                            html += '<td style="word-break: break-word;"><a href="<?php echo admin_url("leads/index/"); ?>' + lead.id + '" target="_blank">' + (lead.name || 'Sem nome') + '</a></td>';
                            html += '<td style="word-break: break-word;"><span class="label label-warning">' + (lead.campaign || 'N/A') + '</span></td>';
                            html += '<td style="word-break: break-word;">' + (lead.status_name || 'N/A') + '</td>';
                            html += '<td style="word-break: break-word;">' + (lead.source_name || 'N/A') + '</td>';
                            html += '<td>';
                            html += '<select class="selectpicker quick-assign-lead-group" ';
                            html += 'data-lead-id="' + lead.id + '" ';
                            html += 'data-campaign="' + (lead.campaign || '').replace(/'/g, "&#39;") + '" ';
                            html += 'data-width="100%" ';
                            html += 'data-style="btn-warning btn-xs" ';
                            html += 'data-none-selected-text="Escolher grupo..." ';
                            html += 'style="min-width: 180px;">';
                            html += '<option value="">Escolher grupo...</option>';
                            groups.forEach(function(group) {
                                html += '<option value="' + group.grup_id + '">' + (group.grup_name || '') + '</option>';
                            });
                            html += '</select>';
                            html += '</td>';
                            html += '<td><a href="<?php echo admin_url("leads/index/"); ?>' + lead.id + '" target="_blank" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> Ver Lead</a></td>';
                            html += '</tr>';
                        });

                        html += '</tbody></table>';
                        $('#stuck-leads-list').html(html);

                        // Initialize selectpicker on new dropdowns
                        $('#stuck-leads-list .selectpicker').selectpicker();
                    } else {
                        $('#stuck-leads-content').hide();
                        $('#stuck-leads-empty').show();
                        // Filters remain visible even when no leads
                        $('#stuck-leads-filters .selectpicker').selectpicker();
                    }
                } else {
                    $('#stuck-leads-loading').html('<div class="alert alert-danger">Erro ao carregar leads bloqueados: ' + (response.message || 'Erro desconhecido') + '</div>');
                }
            },
            error: function() {
                $('#stuck-leads-loading').html('<div class="alert alert-danger">Erro ao carregar leads bloqueados. Tente recarregar a página.</div>');
            }
        });
    }
</script>