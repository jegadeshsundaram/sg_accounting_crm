<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Country</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Combo Tables</li>
               <li class="breadcrumb-item"><a href="/combo_tables/country">Country</a></li>
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
                              <label for="country_code" class="col-md-4 control-label txt-right">Code : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    name="country_code" id="country_code" 
                                    maxlength="4"
                                    class="form-control w-80" required />
                                    <span id="code_error" class="error" style="display: none">Duplicate code disallowed</span>
                              </div>
                           </div>

                           <!-- Field: Name -->
                           <div class="form-group row">
                              <label for="country_name" class="col-md-4 control-label txt-right">Name : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    name="country_name" id="country_name" 
                                    class="form-control w-300" required />
                              </div>
                           </div>

                        </div>
                     </div>
                  </div>
                  <div class="card-footer">
                     <a href="/combo_tables/country" class="btn btn-info">Back</a>                  
                     <button type="button" id="btn_submit" class="btn btn-warning float-right">Submit</button>
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
      $('#country_code').focus();

      $('#country_code').on('input',function(e) {
         $.post('/combo_tables/ajax/double_country', {
            country_code: $(this).val()
         }, function(data) {
            if (data == 1) {
               same_code_exists = 1;
               $('#code_error').show();
               $(this).focus();
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

         var url = "/combo_tables/Ajax/country/save";
         $('#form_').attr("action", url);
         $('#form_').submit();
      });

   }); // document ends
</script>
