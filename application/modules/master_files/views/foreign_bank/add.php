<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Foreign Bank</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Master Files</li>
               <li class="breadcrumb-item"><a href="/master_files/foreign_bank">Foreign Bank</a></li>
               <li class="breadcrumb-item active">New</li>
            </ol>
         </div>
      </div>
   </div>
</div>

<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <input type="hidden" id="redirect_url" value="/master_files/foreign_bank" />
            <form autocomplete="off" id="form_" method="post" action="#">
               <div class="card card-default">
                  <div class="card-header">
                     <h5>ADD</h5>
                  </div>
                  <div class="card-body">
                     <div class="row">
                        <div class="col-lg-6">
                           <!-- Field: Code -->
                           <div class="form-group row">
                              <label for="code" class="col-md-12 col-lg-12 col-xl-4 control-label">Code : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="fb_code" id="fb_code" 
                                    maxlength="12"
                                    class="form-control w-150 req" required />
                                    <span id="code_error" class="error" style="display: none">Duplicate code disallowed</span>
                              </div>
                           </div>

                           <!-- Field: Name -->
                           <div class="form-group row">
                              <label for="fb_name" class="col-md-12 col-lg-12 col-xl-4 control-label">Name : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="fb_name" id="fb_name" 
                                    class="form-control w-300 req" required />
                              </div>
                           </div>

                           <!-- Field: Contact Person -->
                           <div class="form-group row">
                              <label for="contact_person" class="col-md-12 col-lg-12 col-xl-4 control-label">Contact Person : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="contact_person" id="contact_person" 
                                    class="form-control w-300 req" required />
                              </div>
                           </div>

                           <!-- Field: BLDG NO -->
                           <div class="form-group row">
                              <label for="bldg_number" class="col-md-12 col-lg-12 col-xl-4 control-label">Bldg No : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="bldg_number" id="bldg_number" 
                                    class="form-control w-120" />
                              </div>
                           </div>

                           <!-- Field: Street Name &amp; Unit No -->
                           <div class="form-group row">
                              <label for="street_name" class="col-md-12 col-lg-12 col-xl-4 control-label">Street Name &amp; Unit No : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="street_name" id="street_name" 
                                    class="form-control w-300" />
                              </div>
                           </div>                        

                           <!-- Field: Address Line 2 -->
                           <div class="form-group row">
                              <label for="address_line_2" class="col-md-12 col-lg-12 col-xl-4 control-label">Address Line 2 : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="address_line_2" id="address_line_2" 
                                    class="form-control w-300" />
                              </div>
                           </div>

                        </div>
                        <div class="col-lg-6">

                           <!-- Field: Postal code -->
                           <div class="form-group row">
                              <label for="postal_code" class="col-md-12 col-lg-12 col-xl-4 control-label">Postal code : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="postal_code" id="postal_code" 
                                    class="form-control w-120" />
                              </div>
                           </div>

                           <!-- Field: Phone -->
                           <div class="form-group row">
                              <label for="phone" class="col-md-12 col-lg-12 col-xl-4 control-label">Phone : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="phone" id="phone" 
                                    class="form-control w-150" />
                              </div>
                           </div>

                           <!-- Field: Fax -->
                           <div class="form-group row">
                              <label for="fax" class="col-md-12 col-lg-12 col-xl-4 control-label">Fax : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="fax" id="fax" 
                                    class="form-control w-150" />
                              </div>
                           </div>

                           <!-- Field: Email -->
                           <div class="form-group row">
                              <label for="email" class="col-md-12 col-lg-12 col-xl-4 control-label">Email : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="email" 
                                    name="email" id="email" 
                                    class="form-control w-350 req" required />
                              </div>
                           </div>

                           <!-- Field: Currency -->
                           <div class="form-group row">
                              <label for="email" class="col-md-12 col-lg-12 col-xl-4 control-label">Currency : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <select name="currency_id" id="currency_id" class="form-control w-350 req" required>
                                    <?php echo $currency_options; ?>
                                 </select>
                              </div>
                           </div>

                        </div>
                     </div>
                  </div>
                  <div class="card-footer">
                     <a href="/master_files/foreign_bank" class="btn btn-info">Back</a>                  
                     <button type="button" id="btn_submit" class="btn btn-warning float-right">Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<?php require_once APPPATH.'/modules/includes/modal/currency.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="/assets/js/exit.js"></script>
<script src="/assets/js/modal/currency.js"></script>

<script type="text/javascript">
   var same_code_exists = 0;
   $(function() {
      
      $('#fb_code').focus();
      $('select').select2();

      $('#fb_code').on('input',function(e) {
         $.post('/master_files/ajax/double_fb', {
            fb_code: $(this).val()
         }, function(data) {
            if (data == 1) {
               same_code_exists = 1;
               $('#code_error').show();
               $('#fb_code').focus();
            } else {
               same_code_exists = 0;
               $('#code_error').hide();
            }
         });
      });

      $('#btn_submit').click(function() {
         if(same_code_exists > 0) {
            $('#code_error').show();
            $('#fb_code').focus();
            return false;

         } else if(!$('#form_').valid()) {
            return false;
         }

         var url = "/master_files/Ajax/foreign_bank/save";
         $('#form_').attr("action", url);
         $('#form_').submit();
      });

   }); // document ends
</script>