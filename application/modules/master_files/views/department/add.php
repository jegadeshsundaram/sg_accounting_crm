<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Department</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Master Files</li>
               <li class="breadcrumb-item"><a href="/master_files/department">Department</a></li>
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
                              <label for="code" class="col-md-4 control-label txt-right">Code : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    name="code" id="code" 
                                    maxlength="12"
                                    class="form-control w-150" required />
                                    <span id="code_error" class="error" style="display: none">Duplicate code disallowed</span>
                              </div>
                           </div>

                           <!-- Field: Name -->
                           <div class="form-group row">
                              <label for="name" class="col-md-4 control-label txt-right">Name : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    name="name" id="name" 
                                    class="form-control w-400" required />
                              </div>
                           </div>

                        </div>
                     </div>
                  </div>
                  <div class="card-footer">
                     <a href="/master_files/department" class="btn btn-info">Back</a>                  
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

<script type="text/javascript">
   var same_code_exists = 0;
   $(function() {
      $('#code').focus();

      $(document).on("change", "#code", function(e) {
         $.post('/master_files/ajax/double_department', {
            code: $(this).val()
         }, function(data) {
            if (data == 1) {
               same_code_exists = 1;
               $('#code_error').show();
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

         var url = "/master_files/Ajax/department/save";
         $('#form_').attr("action", url);
         $('#form_').submit();
      });

   }); // document ends
</script>