<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">GST</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Combo Tables</li>
               <li class="breadcrumb-item"><a href="/combo_tables/gst">GST</a></li>
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
            <input type="hidden" id="redirect_url" value="/combo_tables/gst" />
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
                              <label for="gst_code" class="col-md-4 control-label txt-right">Code : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    name="gst_code" id="gst_code" 
                                    class="form-control w-150 req" required />
                                    <span id="code_error" class="error" style="display: none">Duplicate code disallowed</span>
                              </div>
                           </div>

                           <!-- Field: Rate -->
                           <div class="form-group row">
                              <label for="gst_rate" class="col-md-4 control-label txt-right">Rate : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="number" 
                                    name="gst_rate" id="gst_rate" 
                                    maxlength="2" 
                                    class="form-control w-80 req" required />
                              </div>
                           </div>

                           <!-- Field: Type -->
                           <div class="form-group row">
                              <label for="gst_type" class="col-md-4 control-label txt-right">Type : </label>
                              <div class="col-md-8">
                                 <select name="gst_type" id="gst_type" class="form-control w-250 req" required>
                                    <?php echo $gst_types; ?>
                                 </select>
                              </div>
                           </div>

                           <!-- Field: Description -->
                           <div class="form-group row">
                              <label for="gst_description" class="col-md-4 control-label txt-right">Description : </label>
                              <div class="col-md-8">
                                 <textarea 
                                    name="gst_description" id="gst_description" 
                                    class="form-control w-400 req" required></textarea>
                              </div>
                           </div>

                        </div>
                     </div>
                  </div>
                  <div class="card-footer">
                     <a href="/combo_tables/gst" class="btn btn-info">Back</a>                  
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

      $('#gst_code').focus();
      $('select').select2();

      $('#gst_code').focusout(function() {
         var gst_code = $('#gst_code').val();
         $('#gst_code').val(gst_code.toUpperCase());
      });

      $('#gst_code').on('input',function(e) {
         $.post('/combo_tables/ajax/double_gst', {
            gst_code: $(this).val()
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

         var url = "/combo_tables/Ajax/gst/save";
         $('#form_').attr("action", url);
         $('#form_').submit();
      });

   }); // document ends
</script>



