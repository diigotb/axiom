<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-flex tw-items-center">
                     <lord-icon src="https://cdn.lordicon.com/ntcdylzc.json" trigger="loop" delay="2000" colors="primary:#00e09b,secondary:#00e09b" style="width:50px;height:50px">
                     </lord-icon>

                     <span>
                        <?php echo _l('chat_web'); ?>
                     </span>
                  </h4>
                  <hr class="hr-panel-separator" />
                  <div class="tw-mb-2 sm:tw-mb-4">
                     <div class="_buttons">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#modalDevice">
                           <i class="fa-regular fa-plus tw-mr-1"></i>
                           <?php echo _l('chat_web_new'); ?>
                        </button>
                     </div>
                  </div>
                  <div class="panel_s">
                     <table id="chatweb" class="table">
                        <thead>
                           <tr>
                              <th>#ID</th>
                              <th><?= _l("contac_conversation_name"); ?></th>
                              <th><?= _l("chat_web_status_lead"); ?></th> 
                              <th><?= _l("chat_web_source"); ?></th> 
                              <th><?= _l("chat_web_assigned"); ?></th> 
                              <th><?= _l("chat_web_count"); ?></th> 
                              <th><?= _l("chat_web_intregraca"); ?></th> 
                              <th><?= _l("chat_web_date"); ?></th> 
                        

                           </tr>
                        </thead>
                        <tbody>
                           <?php
                           foreach ($chatweb as $chat) {
                              extract((array) $chat);
                              echo "<tr>
                                                <td>{$chat_id}</td>                                                    
                                                <td> 
                                                    <div class='box_thumbTlabeCommunity'> 
                                                        <div>    
                                                            {$chat_name}
                                                            <div class='row-options'>
                                                                <a href='javascript:void(0);'class='text-danger' onclick='delete_engine({$chat_id})' >" . _l("contac_excluir") . "</a> 
                                                            </div>        
                                                        </div>    
                                                    </div>    
                                                </td>                         
                                                <td>".contactcenter_get_name_status_lead($chat_status)."</td> 
                                                <td>".contactcenter_get_name_source_lead($chat_source)."</td> 
                                                <td>".get_staff_full_name($chat_assigned)."</td> 
                                                <td>{$chat_count}</td> 
                                                <td><textarea class='form-control' readonly ><script src='".site_url("modules/contactcenter/assets/js/chatweb.js")."' rel='stylesheet' ></script><iframe  id='iframe-chatweb' width='100' height='100' style='position: fixed;bottom: 0; right: 0; height: 100px !important; z-index:2;'  src='".site_url("contactcenter/chat/axiom/{$chat_hash}")."' frameborder='0' sandbox='allow-scripts allow-forms allow-same-origin'></iframe>
                                                </textarea> </td>                                              
                                                <td>{$date}</td> 
                                                </tr>";
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


<!-- Modal -->
<div class="modal fade" id="modalDevice" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header ">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title " id="exampleModalLongTitle"><?= _l("chat_web_new"); ?></h4>
         </div>
         <div class="modal-body">
            <?php echo form_open_multipart(admin_url('contactcenter/add_chatweb')); ?>

            <div class="form-group">
               <label><?= _l("contac_conversation_name"); ?></label>
               <input type="text" class="form-control" name="chat_name" placeholder="<?= _l("contac_conversation_name"); ?>" required>
            </div>

            <div class="form-group">
               <label><?= _l("chat_web_status_lead"); ?></label>
               <select name="chat_status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true" required>
                  <option></option>
                  <?php foreach ($statuses as $status) { ?>
                     <option value="<?php echo $status['id']; ?>">
                        <?php echo $status['name'] ?>
                     </option>
                  <?php } ?>
               </select>
            </div>

            <div class="form-group">
               <label><?= _l("chat_web_assigned"); ?></label>
               <select name="chat_assigned" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                  <option></option>
                  <?php foreach ($members as $member) { ?>
                     <option value="<?php echo $member['staffid']; ?>">
                        <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                     </option>
                  <?php } ?>
               </select>
            </div>

            <div class="form-group">
               <label><?= _l("chat_web_source"); ?></label>
               <select name="chat_source" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                  <option></option>
                  <?php foreach ($leads_sources as $sources) { ?>
                     <option value="<?php echo $sources['id']; ?>">
                        <?php echo $sources['name']; ?>
                     </option>
                  <?php } ?>
               </select>
            </div>

            <div class="form-group">
               <label><?= _l("chat_web_assitente"); ?></label>
               <select name="chat_assitent" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                  <option></option>
                  <?php foreach ($assistants as $assistant) { ?>
                     <option value="<?php echo $assistant->ai_token; ?>">
                        <?php echo $assistant->ai_name ."| ". $assistant->ai_token ; ?>
                     </option>
                  <?php } ?>
               </select>
            </div>




            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
            <?php echo form_close(); ?>
         </div>
      </div>
   </div>
</div>




<?php init_tail(); ?>

<script>
   $(document).ready(function() {
      initDataTableInline("#chatweb");

      //Limpa os formularios das modal
      $('.modal').on('hidden.bs.modal', function(e) {
         // Reseta o formulário quando fecha modal 
         $(this).find('form')[0].reset();
      });

   });
</script>