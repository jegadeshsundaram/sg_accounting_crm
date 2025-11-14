<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Currency</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Combo Tables</li>
               <li class="breadcrumb-item"><a href="/combo_tables/currency">Currency</a></li>
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
               <input name="id" value="<?php echo $currency->currency_id; ?>" type="hidden" required />
               <div class="card card-default">
                  <div class="card-header">
                     <h5>EDIT</h5>
                  </div>
                  <div class="card-body">
                     <div class="row">
                        <div class="col-lg-12">
                           <!-- Field: Code -->
                           <div class="form-group row">
                              <label for="code" class="col-md-4 control-label txt-right">Currency : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    name="code" id="code" 
                                    value="<?php echo $currency->code; ?>"                                     
                                    maxlength="3"
                                    class="form-control w-80 req" readonly />
                                    <span id="code_error" class="error" style="display: none">Duplicate code disallowed</span>
                              </div>
                           </div>

                           <!-- Field: Description -->
                           <div class="form-group row">
                              <label for="description" class="col-md-4 control-label txt-right">Description : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    name="description" id="description" 
                                    value="<?php echo $currency->description; ?>" 
                                    class="form-control w-350 req" required />
                              </div>
                           </div>

                           <!-- Field: Currency Rate -->
                           <div class="form-group row">
                              <label for="rate" class="col-md-4 control-label txt-right">Rate : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="number" 
                                    name="rate" id="rate" 
                                    value="<?php echo $currency->rate; ?>" 
                                    class="form-control w-150 req" onKeyPress="if(this.value.length==12) return false;" required />
                              </div>
                           </div>

                        </div>
                     </div>
                  </div>
                  <div class="card-footer">
                     <a href="/combo_tables/currency" class="btn btn-info">Back</a>                  
                     <button type="button" id="btn_submit" class="btn btn-warning float-right">Update</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<script type="text/javascript">
   var same_code_exists = 0;
   $(function() {
      
      if (window.location.href.indexOf("view_currency") > -1) {
         $('.card-header h5').html("View");
         $("input").prop('disabled', true);
         $('#btn_submit').hide();
         $('.btn-info').addClass('float-right');         
         return false;
      }

      $('#code').focusout(function() {
         var currency = $('#code').val();
         $('#code').val(currency.toUpperCase());
      });

      $('#rate').focusout(function() {
         var rate = $('#rate').val();
         $('#rate').val(parseFloat(rate).toFixed(5));
      });

      $('#code').on('input',function(e) {
         $.post('/combo_tables/ajax/double_currency', {
            code: $(this).val()
         }, function(data) {
            if (data == 1) {
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

         var url = "/combo_tables/Ajax/currency/update";
         $('#form_').attr("action", url);
         $('#form_').submit();
      });

   }); // document ends
</script>
