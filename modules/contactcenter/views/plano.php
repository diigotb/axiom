<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
               <div class="panel-heading">
                  <h3 class="panel-title"><?php echo _l('axiom_saas_det_plano'); ?></h3>
               </div>
               <div class="panel-body">
                  <div class="form-horizontal">                 
                     <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo _l('axiom_saas_chave_plano'); ?>:</label>
                        <div class="col-sm-8">
                           <p class="form-control-static"><?= $plano->plan_key ?></p>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo _l('axiom_saas_descricao'); ?>:</label>
                        <div class="col-sm-8">
                           <p class="form-control-static"><?= $plano->plan_desc ?></p>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo _l('axiom_saas_plan_key_status'); ?>:</label>
                        <div class="col-sm-8">
                           <p class="form-control-static">
                              <?php 
                              if($plano->plan_key_status == "Validado"){
                                 echo '<span class="label label-success">Validado</span>';
                              }else{
                                 echo '<span class="label label-danger">Não Validado</span>';
                              }
                              $plano->plan_key_status 
                              ?>

                           </p>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo _l('axiom_saas_url_plano'); ?>:</label>
                        <div class="col-sm-8">
                           <p class="form-control-static"><?= $plano->plan_url ?></p>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo _l('axiom_saas_quantidade_dispositivos'); ?>:</label>
                        <div class="col-sm-8">
                           <p class="form-control-static"><?= $plano->qtd_device ?></p>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo _l('axiom_saas_quantidade_assistentes'); ?>:</label>
                        <div class="col-sm-8">
                           <p class="form-control-static"><?= $plano->qtd_assistant ?></p>
                        </div>
                     </div>                  
                     <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo _l('axiom_saas_data_inicio'); ?>:</label>
                        <div class="col-sm-8">
                           <p class="form-control-static"><?= _dt($plano->plan_date_start) ?></p>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo _l('axiom_saas_data_termino'); ?>:</label>
                        <div class="col-sm-8">
                           <p class="form-control-static"><?= _dt($plano->plan_date_end) ?></p>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-sm-4 control-label"><?php echo _l('axiom_saas_data_criacao'); ?>:</label>
                        <div class="col-sm-8">
                           <p class="form-control-static"><?= _dt($plano->plan_date_create) ?></p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>


<?php init_tail(); ?>
</body>
</html>
