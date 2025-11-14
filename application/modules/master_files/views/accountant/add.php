<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Accountant</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Master Files</li>
               <li class="breadcrumb-item"><a href="/master_files/accountant">Accountant</a></li>
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
            <input type="hidden" id="redirect_url" value="/master_files/accountant" />
            <form autocomplete="off" id="form_" method="post" action="#">
               <div class="card card-default">
                  <div class="card-header">
                     <h5>ADD</h5>
                  </div>
                  <div class="card-body">
                     
                     <!-- Field: Code -->
                     <div class="form-group row">
                        <label for="code" class="col-md-4 control-label txt-right">Code : </label>
                        <div class="col-md-8">
                           <input 
                              type="text" 
                              name="code" id="code" 
                              maxlength="12"
                              class="form-control w-150 req" required />
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
                              class="form-control w-300 req" required />
                        </div>
                     </div>

                     <!-- Field: Email -->
                     <div class="form-group row">
                        <label for="email" class="col-md-4 control-label txt-right">Email : </label>
                        <div class="col-md-8">
                           <input 
                              type="email" 
                              name="email" id="email" 
                              class="form-control w-350 req" required />
                        </div>
                     </div>

                     <!-- Field: Category -->
                     <div class="form-group row">
                        <label for="category" class="col-md-4 control-label txt-right">Category : </label>
                        <div class="col-md-8">
                           <input 
                              type="text" 
                              name="category" id="category" 
                              class="form-control w-300 req" required />
                        </div>
                     </div>

                     <!-- Field: Basic Salary -->
                     <div class="form-group row">
                        <label for="basic_salary" class="col-md-4 control-label txt-right">Basic Salary : </label>
                        <div class="col-md-8">
                           <input 
                              type="number" 
                              name="basic_salary" id="basic_salary" 
                              class="form-control w-150 req" required />
                        </div>
                     </div>

                     <!-- Field: Incentives -->
                     <div class="form-group row">
                        <label for="incentives" class="col-md-4 control-label txt-right">Incentives (%) : </label>
                        <div class="col-md-8">
                           <input 
                              type="number" 
                              name="incentives" id="incentives" 
                              maxlength="5"
                              class="form-control w-80 req" required />
                        </div>
                     </div>

                  </div>
                  <div class="card-footer">
                     <a href="/master_files/accountant" class="btn btn-info">Back</a>                  
                     <button type="button" id="btn_submit" class="btn btn-warning float-right">Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<script src="/assets/js/exit.js"></script>

<script type="text/javascript">
   var same_code_exists = 0;
   $(function() {
      $('#code').focus();
      $('#code').on('input',function(e) {
         $.post('/master_files/ajax/double_accountant', {
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

         var url = "/master_files/Ajax/accountant/save";
         $('#form_').attr("action", url);
         $('#form_').submit();
      });

   }); // document ends
</script>

