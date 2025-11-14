<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Company Profile</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item active">Company Profile</li>
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
            <form id="form_" autocomplete="off" method="post" enctype="multipart/form-data" action="<?php echo $save_url; ?>">
               <div class="card card-default">
                  <div class="card-body">
                     <!-- Company Name -->
                     <div class="form-group row">
                        <label for="company_name" class="col-md-4 control-label">Company Name : </label>
                        <div class="col-md-8">
                           <input 
                              type="text" 
                              name="company_name" id="company_name" 
                              value="<?php echo $company_name; ?>" 
                              class="form-control" required <?php echo $readonly; ?> />
                        </div>
                     </div>

                     <!-- Logo -->
                     <div class="form-group row">
                        <label class="col-md-4 control-label">Company Logo : </label>
                        <div class="col-md-8">
                           
                           <img src="<?php echo UPLOAD_PATH.'site/'.$company_logo; ?>" class='img img-thumbnail logo' height="150px" width="150px" style="display: <?php echo $company_logo !== '' ? 'block' : 'none'; ?>" />

                           <?php if ($company_logo !== '') { ?>
                              <button type="button" id="btn_exclude_logo" class="btn btn-outline-secondary btn-sm"><i class="fa fa-edit" aria-hidden="true"></i> Exclude</button>
                           <?php } ?>
                           
                           <label for="db_file" class="btn btn-outline-secondary btn-sm btn_upload_logo" style="font-weight: 400; margin-top: 7px"><i class="fa fa-plus-circle" aria-hidden="true"></i> <?php echo $company_logo !== '' ? 'Change' : 'Insert'; ?></label>

                           <input 
                              type="file" 
                              name="company_logo" id="db_file"
                              class="form-control inputfile ignore" />

                           <input 
                              type="hidden" 
                              name="company_logo" id="company_logo" 
                              value="<?php echo $company_logo; ?>" />
                        </div>
                     </div>

                     <!-- Address -->
                     <div class="form-group row">
                        <label for="company_address" class="col-md-4 control-label">Company Address : </label>
                        <div class="col-md-8">
                           <textarea class="form-control" name="company_address" id="company_address" rows="2"><?php echo $company_address; ?></textarea>
                        </div>
                     </div>

                     <!-- GST Registration Number -->
                     <div class="form-group row">
                        <label for="gst_reg_no" class="col-md-4 control-label">GST Register Number : </label>
                        <div class="col-md-8">
                           <input 
                              type="text"
                              name="gst_reg_no" id="gst_reg_no" 
                              value="<?php echo $gst_reg_no; ?>" 
                              class="form-control w-180" <?php echo $readonly; ?> />
                        </div>
                     </div>
                     
                     <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                     <!-- GST Standard Rate -->                     
                     <div class="form-group row">
                        <label for="gst_std_rate" class="col-md-4 control-label">GST Standard Rate : </label>
                        <div class="col-md-8">
                           <input 
                              type="number"
                              name="gst_std_rate" id="gst_std_rate" 
                              value="<?php echo $gst_std_rate; ?>" maxlength="1"
                              class="form-control w-80" <?php echo $readonly; ?> />
                        </div>
                     </div>
                     <?php } ?>

                     <!-- UEN Number -->
                     <div class="form-group row">
                        <label for="uen_no" class="col-md-4 control-label">UEN No. : </label>
                        <div class="col-md-8">
                           <input 
                              type="text"
                              name="uen_no" id="uen_no"
                              value="<?php echo $uen_no; ?>" 
                              class="form-control w-180" <?php echo $readonly; ?> />
                        </div>
                     </div>                     
                     
                     <!-- Phone -->
                     <div class="form-group row">
                        <label for="phone" class="col-md-4 control-label">Phone : </label>
                        <div class="col-md-8">
                           <input 
                              type="text"
                              name="phone" id="phone"
                              value="<?php echo $phone; ?>" 
                              class="form-control w-180" />
                        </div>
                     </div>

                     <!-- Fax -->
                     <div class="form-group row">
                        <label for="fax" class="col-md-4 control-label">Fax : </label>
                        <div class="col-md-8">
                           <input 
                              type="text"
                              name="fax" id="fax" 
                              value="<?php echo $fax; ?>" 
                              class="form-control w-180" />
                        </div>
                     </div>

                     <!-- Email -->
                     <div class="form-group row">
                        <label for="company_email" class="col-md-4 control-label">E-Mail : </label>
                        <div class="col-md-8">
                           <input 
                              type="text"
                              name="company_email" id="company_email" 
                              value="<?php echo $company_email; ?>" 
                              class="form-control" />
                        </div>
                     </div>

                     <!-- Accounting Currency -->
                     <div class="form-group row">
                        <label for="default_currency" class="col-md-4 control-label">Currency : </label>
                        <div class="col-md-8">
                           <select class="form-control" name="default_currency" id="default_currency" required>
                              <?php echo $currency_options; ?>
                           </select>
                        </div>
                     </div>
                  </div>

                  <div class="card-footer">
                     <a href="/dashboard" class="btn btn-info">Back</a>                  
                     <button type="button" id="btn_submit" class="btn btn-warning float-right">Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>

   </div><!-- container-fluid ends -->
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
 .form-control {
   max-width: 350px;
 }
 .inputfile {
      width: 0.1px;
      height: 0.1px;
      opacity: 0;
      overflow: hidden;
      position: absolute;
      z-index: -1;
   }
</style>

<script>
   $(function() {     
      
      <?php if ($user == 'admin') { ?>
         $('#default_currency').prop("disabled", true);
      <?php } ?>

      $('#default_currency').select2();
      
      $('#db_file').change(function() {
         var ext = $(this).val().split('.').pop().toLowerCase();
         if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
            //alert('Invalid Format! Only GIF, PNG, JPG & JPEG are allowed.');
            $('#errorAlertModal .modal-title').html("Invalid Format!");
            $('#errorAlertModal .modal-body').html("Allowed Formats: gif | jpg | png | bmp | jpeg");
            $('#errorAlertModal').modal();
         } else {
            var file = this.files[0].name;
            $('#company_logo').val(file);
            $(".logo").attr("src", URL.createObjectURL(this.files[0]));
            $(".logo").show();

            $('#btn_exclude_logo').show();
            $('.btn_upload_logo').html("Change");
         }
      });

      var process = "";
      $('#btn_exclude_logo').click(function() {
         process = "exclude_logo";
         $('#confirmSubmitModal .modal-title').html("Exclude Logo?");
         $('#confirmSubmitModal .modal-body').html("Click 'Yes' to remove logo from company profile");
         $('#confirmSubmitModal').modal();
      });

      $(document).on("click", "#btn_submit", function() {
         if($('#form_').valid()) {
            process = "submit";

            $('#confirmSubmitModal .modal-title').html("Confirm Submission");
            $('#confirmSubmitModal .modal-body').html("Click 'Yes' to Save Profile Settings");
            $("#confirmSubmitModal").modal();
         }         
      });

      $('#btn-confirm-yes').click(function() {
         if(process == "exclude_logo") {
            $('#company_logo').val("");
            $(".logo").attr("src", "");
            $(".logo").hide();

            $('#btn_exclude_logo').hide();
            $('.btn_upload_logo').html("Insert");

            $("#confirmSubmitModal").modal('hide');
         } else if(process == "submit") {
            $("#confirmSubmitModal").modal('hide');
            $("#form_").submit();
         }
      });
   });
</script>

