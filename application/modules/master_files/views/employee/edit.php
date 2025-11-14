<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Employee</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Master Files</li>
               <li class="breadcrumb-item"><a href="/master_files/employee">Employee</a></li>
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
               <input name="id" value="<?php echo $employee_data->e_id; ?>" type="hidden" />
               <div class="card card-default">
                  <div class="card-header">
                     <h5>EDIT</h5>
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
                                    value="<?php echo $employee_data->code; ?>" 
                                    maxlength="10"
                                    class="form-control w-150 code" readonly />
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
                                    value="<?php echo $employee_data->name; ?>" 
                                    class="form-control w-300" required />
                              </div>
                           </div>

                           <!-- Field: Email -->
                           <div class="form-group row">
                              <label for="email" class="col-md-4 control-label txt-right">Email : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="email" 
                                    name="email" id="email" 
                                    value="<?php echo $employee_data->email; ?>" 
                                    class="form-control w-350" required />
                              </div>
                           </div>

                           <!-- Field: Department -->
                           <div class="form-group row">
                              <label for="department" class="col-md-4 control-label txt-right">Department : <a id="add_department_btn" style="cursor: pointer; background: yellow; padding: 2px 7px; border-radius: 2px; color: deeppink; font-weight: normal; letter-spacing: 1px; border: 1px solid burlywood;">Add</a></label>
                              <div class="col-md-8">
                                 <select name="department_id" id="department_id" class="form-control w-350" required>
                                    <?php echo $departments; ?>
                                 </select>
                              </div>
                           </div>

                           <!-- Field: Note -->
                           <div class="form-group row">
                              <label for="note" class="col-md-4 control-label txt-right">Note : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    name="note" id="note" 
                                    value="<?php echo $employee_data->note; ?>" 
                                    class="form-control w-350" />
                              </div>
                           </div>

                        </div>
                     </div>
                  </div>
                  <div class="card-footer">
                     <a href="/master_files/employee" class="btn btn-info">Back</a>                  
                     <button type="button" id="btn_submit" class="btn btn-warning float-right">Update</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<?php require_once APPPATH.'/modules/includes/modal/department.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="/assets/js/modal/department.js"></script>

<script type="text/javascript">
   var same_code_exists = 0;
   $(function() {

      if (window.location.href.indexOf("view_employee") > -1) {
         $('.card-header h5').html("View");
         $("input").prop('disabled', true);
         $("select").prop('disabled', true);
         $('#btn_submit').hide();
         $('.btn-info').addClass('float-right');
         return false;
      }

      $('select').select2();
   
      $(document).on("change", "#code", function(e) {
         $.post('/master_files/ajax/double_employee', {
            code: $(this).val()
         }, function(data) {
            if (data == 1) {
               $('#code_error').show();
               same_code_exists = 1;
            } else {
               $('#code_error').hide();
               same_code_exists = 0;
            }
         });
      });      

      $("#btn_submit").click(function(event) {
         if(same_code_exists > 0) {
            $('#code_error').show();
            $('#code').focus();
            return false;

         } else if(!$('#form_').valid()) {
            return false;
         }

         var update_url = "/master_files/Ajax/employee/update";
         $('#form_').attr("action", update_url);
         $('#form_').submit();
      });
   }); // document ends
</script>
