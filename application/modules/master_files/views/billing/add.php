<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Billing</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Master Files</li>
               <li class="breadcrumb-item"><a href="/master_files/billing">Billing</a></li>
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
            <input type="hidden" id="redirect_url" value="/master_files/billing" />
            <form autocomplete="off" id="form_" method="post" action="#">
               <div class="card card-default">
                  <div class="card-header">
                     <h5>ADD</h5>
                  </div>
                  <div class="card-body">
                     <div class="row">
                        <div class="col-lg-12">
                           <!-- Field: Code -->
                           <div class="form-group row">
                              <label for="stock_code" class="col-md-4 control-label txt-right">Code : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    name="stock_code" id="stock_code" 
                                    maxlength="25"
                                    class="form-control w-150" required />
                                    <span id="code_error" class="error" style="display: none">Duplicate code disallowed</span>
                              </div>
                           </div>

                           <!-- Field: Description -->
                           <div class="form-group row">
                              <label for="billing_description" class="col-md-4 control-label txt-right">Description : </label>
                              <div class="col-md-8">
                                 <textarea 
                                    type="text" 
                                    name="billing_description" id="billing_description" 
                                    class="form-control" required></textarea>
                              </div>
                           </div>

                           <!-- Field: Type -->
                           <div class="form-group row">
                              <label for="billing_type" class="col-md-4 control-label txt-right">Billing Type : </label>
                              <div class="col-md-8">
                                 <div class="billing_type_btns">
                                    <button type="button" class="btn bg-purple btn-sm" id="product_btn">Product</button>
                                    <button type="button" class="btn bg-maroon btn-sm" id="service_btn">Service</button>
                                 </div>
                                 <div class="billing_type_text" style="display: none">
                                    <span style="line-height: 33px; color: blue; letter-spacing: 1px;"></span>
                                    <a id="billing_type_change" class="btn btn-outline-secondary btn-sm">Change</a>
                                 </div>
                                 <span id="billing_type_error" class="error" style="display: none">This field is required</span>
                              </div>
                           </div>

                           <!-- Field: UOM -->
                           <div class="form-group row uom_field" style="display: none">
                              <label for="customer_code" class="col-md-4 control-label txt-right">UOM : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    name="billing_uom" id="billing_uom" 
                                    maxlength="10" oninput='onlyText(this)'
                                    class="form-control w-150" />
                              </div>
                           </div>

                           <!-- Field: Price -->
                           <div class="form-group row price_field" style="display: none">
                              <label for="customer_code" class="col-md-4 control-label txt-right">Price per UOM : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="number" 
                                    name="billing_price_per_uom" id="billing_price_per_uom" 
                                    maxlength="12" onKeyPress="if(this.value.length == 12) return false;"
                                    class="form-control w-150" />
                              </div>
                           </div>

                           <!-- Field: Update Stock -->
                           <div class="form-group row stock_field" style="display: none">
                              <label for="customer_code" class="col-md-4 control-label txt-right">Update Stock : </label>
                              <div class="col-md-8">
                                 <select class="form-control w-150" name="billing_update_stock" id="billing_update_stock">
                                    <?php echo $stock_options; ?>
                                 </select>
                              </div>
                           </div>

                        </div>
                     </div>
                  </div>
                  <div class="card-footer">
                     <input type="hidden" name="billing_type" id="billing_type" value="" />
                     <a href="/master_files/billing" class="btn btn-info">Back</a>                  
                     <button type="button" id="btn_submit" class="btn btn-warning float-right">Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="/assets/js/exit.js"></script>

<script type="text/javascript">
   var same_code_exists = 0;

   $(function() {     

      $('select').select2();
      $('#stock_code').focus();

      $(document).on("keyup", "#stock_code", function(e) {
         $.post('/master_files/ajax/double_billing', {
            code: $(this).val()
         }, function(data) {
            if (data == 1) {
               same_code_exists = 1;
               $('#code_error').show();
               $('#stock_code').focus();
            } else {
               same_code_exists = 0;
               $('#code_error').hide();
            }
         });
      });

      $("#billing_type_change").click(function() {
        $('.billing_type_text').hide();
        $(".uom_field, .price_field, .stock_field").hide();
        $('.uom_field, .price_field').removeClass("has-error");
        $('.billing_type_btns').show();

        $('#billing_uom').val("");
        $('#billing_price_per_uom').val("");
        $("#billing_update_stock").val("").trigger("change");        
        $('#billing_type').val("");
      });

      $("#product_btn").click(function() {
         $("#billing_type_error").hide();
        $('#billing_uom').prop('required', true);
        $('#billing_price_per_uom').prop('required', true);
        $('#select2-billing_update_stock-container').prop('title', 'YES').text('YES');
        $('#billing_update_stock').prop('disabled', true);
        $('#billing_update_stock').val('YES');

        $('.billing_type_btns').hide();
        $('.billing_type_text span').text("Product");
        $('.billing_type_text').show();
        $(".uom_field, .price_field, .stock_field").show();

        $('#billing_type').val("Product");
      });

      $("#service_btn").click(function() {
         $("#billing_type_error").hide();
        $('#billing_uom').prop('required', false);
        $('#billing_price_per_uom').prop('required', false);
        $('#select2-billing_update_stock-container').prop('title', 'NO').text('NO');
        $('#billing_update_stock').prop('disabled', true);
        $('#billing_update_stock').val('NO');

        $('.billing_type_btns').hide();
        $('.billing_type_text span').text("Service");
        $('.billing_type_text').show();
        $(".uom_field, .price_field").show();

        $('#billing_type').val("Service");
      });

      $("#btn_submit").click(function() {
         if(same_code_exists > 0) {
            $('#code_error').show();
            $('#code').focus();
            return false;

         } else if(!$('#form_').valid()) {
            return false;

         } else if($('#billing_type').val() == "") {
            $("#billing_type_error").show();
            return false;
         }

         var save_url = "/master_files/Ajax/billing/save";
         $("#form_").attr("action", save_url);
         $("#form_").submit();
      });

   }); // document ends
</script>


