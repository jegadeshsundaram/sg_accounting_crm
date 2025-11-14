<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Customer</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Master Files</li>
               <li class="breadcrumb-item"><a href="/master_files/customer">Customer</a></li>
               <li class="breadcrumb-item active">Edit</li>
            </ol>
         </div>
      </div>
   </div>
</div>

<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <form autocomplete="off" id="form_" method="post" action="#">
               <input type="hidden" name="id" value="<?php echo $customer->customer_id; ?>" />
               <div class="card card-default">
                  <div class="card-header">
                     <h5>EDIT</h5>
                  </div>
                  <div class="card-body">
                     <div class="row">
                        <div class="col-md-6">
                           <!-- Field: Code -->
                           <div class="form-group row">
                              <label for="code" class="col-md-12 col-lg-12 col-xl-4 control-label">Code : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="code" id="code" 
                                    value="<?php echo $customer->code; ?>" 
                                    class="form-control w-120" maxlength="12" required />
                                    <span id="code_error" class="error" style="display: none" required>Duplicate code disallowed</span>
                              </div>
                           </div>

                           <!-- Field: Name -->
                           <div class="form-group row">
                              <label for="name" class="col-md-12 col-lg-12 col-xl-4 control-label">Name : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="name" id="name" 
                                    value="<?php echo $customer->name; ?>" 
                                    class="form-control w-300" required />
                              </div>
                           </div>

                           <!-- Field: Contact Person -->
                           <div class="form-group row">
                              <label for="contact_person" class="col-md-12 col-lg-12 col-xl-4 control-label">Contact Person : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="contact_person" id="contact_person" 
                                    value="<?php echo $customer->contact_person; ?>" 
                                    class="form-control w-300" />
                              </div>
                           </div>

                           <!-- Field: BLDG NO -->
                           <div class="form-group row">
                              <label for="bldg_number" class="col-md-12 col-lg-12 col-xl-4 control-label">Bldg No : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="bldg_number" id="bldg_number" 
                                    value="<?php echo $customer->bldg_number; ?>" 
                                    class="form-control w-120" />
                              </div>
                           </div>

                           <!-- Field: Street Name & Unit No -->
                           <div class="form-group row">
                              <label for="street_name" class="col-md-12 col-lg-12 col-xl-4 control-label">Street Name &amp; Unit No : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="street_name" id="street_name" 
                                    value="<?php echo $customer->street_name; ?>" 
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
                                    value="<?php echo $customer->address_line_2; ?>" 
                                    class="form-control w-300" />
                              </div>
                           </div>

                           <!-- Field: Postal code -->
                           <div class="form-group row">
                              <label for="postal_code" class="col-md-12 col-lg-12 col-xl-4 control-label">Postal code : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="postal_code" id="postal_code" 
                                    value="<?php echo $customer->postal_code; ?>" 
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
                                    value="<?php echo $customer->phone; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <!-- Field: Fax -->
                           <div class="form-group row">
                              <label for="fax" class="col-md-12 col-lg-12 col-xl-4 control-label">Fax : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="fax" id="fax" 
                                    value="<?php echo $customer->fax; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                        </div>
                        <div class="col-md-6">

                           <!-- Field: Email -->
                           <div class="form-group row">
                              <label for="email" class="col-md-12 col-lg-12 col-xl-4 control-label">Email : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="email" 
                                    name="email" id="email" 
                                    value="<?php echo $customer->email; ?>" 
                                    class="form-control w-350" required />
                              </div>
                           </div>

                           <!-- Field: Credit limit -->
                           <div class="form-group row">
                              <label for="credit_limit" class="col-md-12 col-lg-12 col-xl-4 control-label">Credit limit : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="credit_limit" id="credit_limit" 
                                    value="<?php echo $customer->credit_limit; ?>" 
                                    class="form-control w-150" />
                              </div>
                           </div>

                           <!-- Field: Credit Terms in Days -->
                           <div class="form-group row">
                              <label for="credit_term_days" class="col-md-12 col-lg-12 col-xl-4 control-label">Credit Terms in Days : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="credit_term_days" id="credit_term_days" 
                                    value="<?php echo $customer->credit_term_days; ?>" 
                                    oninput="validate_creditDays(this)" class="form-control w-80" />
                              </div>
                           </div>

                           <!-- Field: Currency -->
                           <div class="form-group row">
                              <label for="currency_id" class="col-md-12 col-lg-12 col-xl-4 control-label">Currency</label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <select class="form-control w-350" name="currency_id" id="currency_id" required>
                                    <?php echo $currency_options; ?>
                                 </select>
                              </div>
                           </div>

                           <!-- Field: UEN no -->
                           <div class="form-group row">
                              <label for="uen_no" class="col-md-12 col-lg-12 col-xl-4 control-label">UEN no : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="uen_no" id="uen_no" 
                                    value="<?php echo $customer->uen_no; ?>" 
                                    maxlength="12" class="form-control w-200" />
                              </div>
                           </div>

                           <!-- Field: GST Reg No -->
                           <div class="form-group row">
                              <label for="gst_number" class="col-md-12 col-lg-12 col-xl-4 control-label">GST Reg No : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="gst_number" id="gst_number" 
                                    value="<?php echo $customer->gst_number; ?>" 
                                    maxlength="12" class="form-control w-200" />
                              </div>
                           </div>

                           <!-- Field: Rating -->
                           <div class="form-group row">
                              <label for="rating" class="col-md-12 col-lg-12 col-xl-4 control-label">Rating : </label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <input 
                                    type="text" 
                                    name="rating" id="rating" 
                                    value="<?php echo $customer->rating; ?>" 
                                    class="form-control w-80" />
                              </div>
                           </div>

                           <!-- Field: Country -->
                           <div class="form-group row">
                              <label for="country_id" class="col-md-12 col-lg-12 col-xl-4 control-label">Country</label>
                              <div class="col-md-12 col-lg-12 col-xl-8">
                                 <select class="form-control w-350" name="country_id" id="country_id">
                                    <?php echo $country_options; ?>
                                 </select>
                              </div>
                           </div>

                        </div>
                     </div>
                  </div>
                  <div class="card-footer">
                     <a href="/master_files/customer" class="btn btn-info">Back</a>
                     <button type="button" name="btn_submit" id="btn_submit" class="btn btn-warning float-right">Update</button>
                  </div>
               </div> <!-- card - ends -->
            </form>
         </div>
      </div>

   </div>
</div>

<?php require_once APPPATH.'/modules/includes/modal/currency.php'; ?>
<?php require_once APPPATH.'/modules/includes/modal/country.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="/assets/js/modal/currency.js"></script>
<script src="/assets/js/modal/country.js"></script>

<script type="text/javascript">
   var code = '';
   var same_code_exists = 0;
   $(function() {
      if (window.location.href.indexOf("view_customer") > -1) {
         $('.card-header h5').html("View");
         $("input").prop('disabled', true);
         $("select").prop('disabled', true);
         $('#btn_submit').hide();
         $('.btn-info').addClass('float-right');
         return false;
      }

      // System checks whether this customer is already used in Quotation, Invoice, Receipt and any other modules
      // If Yes, then code input will be disabled
      // If No, then code input will be enabled and User can do edit if needed
      code = $('#code').val();
      $.post('/master_files/ajax/consultCustomer', {
         code: code
      }, function (data) {
         if(data == 1) {
            $('#code').prop("disabled", true);
         }
      });

      $('select').select2();
   
      $('#code').on('input',function() {
         $.post('/master_files/ajax/double_customer', {
            code: $(this).val()
         }, function(data) {
            if (data == 1 && $('#code').val().toUpperCase() != code.toUpperCase()) {
               same_code_exists = 1;
               $('#code_error').show();
               $('#code').focus();
            } else {
               same_code_exists = 0;
               $('#code_error').hide();
            }
         });
      });

      $('#btn_submit').click(function() {
         if(same_code_exists > 0) {
            $('#code_error').show();
            $('#code').focus();
            return false;

         } else if(!$('#form_').valid()) {
            return false;
         }

         var update_url = "/master_files/Ajax/customer/update";
         $('#form_').attr("action", update_url);
         $('#form_').submit();
      });

   }); // document ends


   function validate_creditDays(data) {
      var str = Number(data.value);
      console.log(str);
      if (str < 0 || !Number.isInteger(str)) {
         data.value = 0;
      }

      if (str > 99 ) {
         data.value = 99;
      }
   }
</script>
