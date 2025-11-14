<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">GST Returns</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">GST</li>
               <li class="breadcrumb-item active">IRAS API</li>
            </ol>
         </div>
      </div>
   </div>
</div>

<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <div id="message_area">
               <?php get_flash_message('message'); ?>
            </div>
         </div>
      </div>

      <div class="row">
         <div class="col-lg-12">
            <form id="frm_" action="#" method="POST">
               <input type="hidden" name="d_id" id="d_id" value="<?php echo $d_id; ?>" />
               <div class="card card-default">  
                  <div class="card-header">
                     <h5>Declaration</h5>
                  </div>
                  <div class="card-body">                    

                     <div class="form-group row">
                        <label for="designation" class="col-xl-4 control-label">Designation : <span class="hint">Designation of the Tax Agent</span></label>
                        <div class="col-xl-8">
                           <input 
                              type="text" 
                              id="designation" name="designation" 
                              value="<?php echo $designation; ?>" class="form-control w-300" required />
                        </div>
                     </div>

                     <div class="form-group row">
                        <label for="tax_agent_name" class="col-xl-4 control-label">Tax Agent Name : <span class="hint">Name of the Tax Agent</span></label>
                        <div class="col-xl-8">
                           <input 
                              type="text" 
                              id="tax_agent_name" name="tax_agent_name" 
                              value="<?php echo $tax_agent_name; ?>" class="form-control w-300" required />
                        </div>
                     </div>
                     
                     <hr />

                     <div class="form-group row">
                        <div class="col-xl-12">
                           <input 
                              type="checkbox" 
                              id="declaration_item_1" 
                              name="declaration_item_1" 
                              value="<?php echo $declaration_item_1; ?>"
                              <?php echo $declaration_item_1 == 'yes' ? 'checked' : ''; ?> required /> <label for="declaration_item_1" class="control-label" style="display: initial; margin-left: 10px">I declare that the information provided in this return is true and complete.</label>
                        </div>
                     </div>

                     <hr />

                     <div class="form-group row">
                        <div class="col-xl-12">
                           <input 
                              type="checkbox" 
                              id="declaration_item_2" 
                              name="declaration_item_2" 
                              value="<?php echo $declaration_item_2; ?>" 
                              <?php echo $declaration_item_2 == 'yes' ? 'checked' : ''; ?> required /> <label for="declaration_item_2" class="control-label" style="display: initial; margin-left: 10px">I understand that penalties may be imposed for the submission of an incorrect return and/or provision of false information to the Comptroller of GST.</label>
                        </div>
                     </div>

                  </div>
                  <div class="card-footer">
                     <a href="/gst/iras_api" class="btn btn-info btn-cancel">Back</a>
                     <button type="button" id="btn_submit" class="btn btn-warning float-right">SAVE & CONTINUE</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<style>
.hint {
   display: block;
   color: brown;
   font-size: 13px;
   font-weight: normal;
   opacity: 0.8;
}
.control-label {
   padding-top: 0px !important;
}
</style>

<script>
   // document starts
   $(function() {      
      
      $("#declaration_item_1").change(function() {
         if($(this).prop('checked')) {
            $("#declaration_item_1").val("yes");
         } else {
            $("#declaration_item_1").val("no");
         }
      });

      $("#declaration_item_2").change(function() {
         if($(this).prop('checked')) {
            $("#declaration_item_2").val("yes");
         } else {
            $("#declaration_item_2").val("no");
         }
      });

      $('#btn_submit').on('click', function() {
         if(!$('#frm_').valid()) {
            return;
         }

         var url = '/gst/save_declaration';
         $("#frm_").attr("action", url);
         $("#frm_").submit();
      });

  });
</script>
