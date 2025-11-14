<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Configuration</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/">System Manager</a></li>
               <li class="breadcrumb-item active">Configuration</li>
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
            <div class="card card-default">
               <div class="card-header">
                  <h5>Modules</h5>
                  <a href="/app_settings" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               <div class="card-header">
                  <a href="#" id="btn_uncheck_all" class="btn btn-outline-danger btn-sm float-right"><i class="fa-solid fa-xmark"></i> Uncheck All</a>
                  <a href="#" id="btn_check_all" class="btn btn-outline-success btn-sm float-right" style="margin-right: 10px"><i class="fa-solid fa-check-double"></i> Check All</a>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           System Manager
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="system_manager" id="system_manager" class="module_check" 
                                 value="<?php echo $modules_permission->system_manager; ?>" 
                                 <?php echo $modules_permission->system_manager == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           Quotation
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="quotation" id="quotation" class="module_check" 
                                 value="<?php echo $modules_permission->quotation; ?>" 
                                 <?php echo $modules_permission->quotation == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           Invoice
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="invoice" id="invoice" class="module_check" 
                                 value="<?php echo $modules_permission->invoice; ?>" 
                                 <?php echo $modules_permission->invoice == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           Receipt
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="receipt" id="receipt" class="module_check" 
                                 value="<?php echo $modules_permission->receipt; ?>" 
                                 <?php echo $modules_permission->receipt == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           Accounts Receivable
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="accounts_receivable" id="accounts_receivable" class="module_check" 
                                 value="<?php echo $modules_permission->accounts_receivable; ?>" 
                                 <?php echo $modules_permission->accounts_receivable == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           Stock
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="stock" id="stock" class="module_check" 
                                 value="<?php echo $modules_permission->stock; ?>" 
                                 <?php echo $modules_permission->stock == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           Payment to Supplier
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="payment_to_supplier" id="payment_to_supplier" class="module_check" 
                                 value="<?php echo $modules_permission->payment_to_supplier; ?>" 
                                 <?php echo $modules_permission->payment_to_supplier == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           Accounts Payable
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="accounts_payable" id="accounts_payable" class="module_check" 
                                 value="<?php echo $modules_permission->accounts_payable; ?>" 
                                 <?php echo $modules_permission->accounts_payable == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           Petty Cash
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="petty_cash" id="petty_cash" class="module_check" 
                                 value="<?php echo $modules_permission->petty_cash; ?>" 
                                 <?php echo $modules_permission->petty_cash == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           Foreign Bank
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="foreign_bank" id="foreign_bank" class="module_check" 
                                 value="<?php echo $modules_permission->foreign_bank; ?>" 
                                 <?php echo $modules_permission->foreign_bank == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           Bank Reconciliation
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="bank_recon" id="bank_recon" class="module_check" 
                                 value="<?php echo $modules_permission->bank_recon; ?>" 
                                 <?php echo $modules_permission->bank_recon == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           Accountant Module
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="accountant_module" id="accountant_module" class="module_check" 
                                 value="<?php echo $modules_permission->accountant_module; ?>" 
                                 <?php echo $modules_permission->accountant_module == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           GST
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="gst" id="gst" class="module_check" 
                                 value="<?php echo $modules_permission->gst; ?>" 
                                 <?php echo $modules_permission->gst == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           EZ Entry Matrix
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="ez_entry" id="ez_entry" class="module_check" 
                                 value="<?php echo $modules_permission->ez_entry; ?>" 
                                 <?php echo $modules_permission->ez_entry == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           Staff Activity
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="staff_activity" id="staff_activity" class="module_check" 
                                 value="<?php echo $modules_permission->staff_activity; ?>" 
                                 <?php echo $modules_permission->staff_activity == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           SAC Job Control
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="sac_job_control" id="sac_job_control" class="module_check" 
                                 value="<?php echo $modules_permission->sac_job_control; ?>" 
                                 <?php echo $modules_permission->sac_job_control == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                     <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="config-box">
                           Data Files Migration
                           <label class="check-container">
                              <input 
                                 type="checkbox"
                                 name="data_files_migration" id="data_files_migration" class="module_check" 
                                 value="<?php echo $modules_permission->data_files_migration; ?>" 
                                 <?php echo $modules_permission->data_files_migration == 1 ? 'checked' : ''; ?> />
                              <span class="checkmark"></span>
                           </label>
                        </div>
                     </div>

                  </div> <!-- row ends -->
               </div>
               <div class="card-footer">
                  <a href="/app_settings" class="btn btn-info">Cancel</a>
                  <button type="button" name="submitbtn" id="submitbtn" class="btn btn-warning float-right">SUBMIT</button>
               </div>
            </div>

         </div>

      </div>
   </div>   

   <form name="configuration_form" id="configuration_form" method="post" action="#">
      <input type="hidden" name="modules_permission" id="modules_permission" />
   </form>

</div>

<style>  
   .config-box {
      background: #fff;
      padding: 10px;
      border-radius: 5px;
      color: #4f5962;
      letter-spacing: 2px;
      margin-bottom: 20px;
      border: 1px solid gainsboro;
      cursor: pointer;
      user-select: none
   }
   .config-box:hover {
      background: gainsboro;
   }
</style>

<script type="text/javascript">
   $(function() {

      $(document).on("click", "#submitbtn", function() {
         var permissions = {};
         var checked = 0;
         $('.module_check').each(function () {
            //permissions.push($(this).attr("id"), $(this).attr("value"));
            permissions[$(this).attr("id")] = $(this).attr("value");
            
            if($(this).prop("checked") == true) {
               checked = checked + 1;
            }
         });

         if(checked > 0) {
            $('#confirmSubmitModal .modal-title').html("Confirm Submission");
            $('#confirmSubmitModal .modal-body').html("Click 'Yes' to Save Configuration Settings");
            $("#confirmSubmitModal").modal();
         } else {
            $('#errorAlertModal .modal-title').html("No modules marked!");
            $('#errorAlertModal .modal-body').html("Please mark atleast one module to continue...");
            $('#errorAlertModal').modal();
         }

         console.log(JSON.stringify(permissions));
         $('#modules_permission').val(JSON.stringify(permissions));
      });

      $('#btn-confirm-yes').click(function() {
         $("#confirmSubmitModal").modal('hide');

         var url = '/app_settings/save_configuration';
         $("#configuration_form").attr("action", url);
         $("#configuration_form").submit();
      });

      $(document).on("click", ".config-box", function() {
         var row_id = $(this).find('.module_check').attr("id");
         if($('#'+row_id).prop("checked") == false) {
         $('#'+row_id).prop("checked", true);
         $('#'+row_id).prop("value", "1");
         } else {
         $('#'+row_id).prop("checked", false);
         $('#'+row_id).prop("value", "0");
         }
      });

      $(document).on("click", "#btn_check_all", function() {
         $('.module_check').each(function () {
            $(this).prop("checked", true);
            var row_id = $(this).attr("id");
            $('#'+row_id).prop("value", "1");
         });
      });

      $(document).on("click", "#btn_uncheck_all", function() {
         $('.module_check').each(function () {
            $(this).prop("checked", false);
            var row_id = $(this).attr("id");
            $('#'+row_id).prop("value", "0");
         });
      });

  }); // document ends

</script>
