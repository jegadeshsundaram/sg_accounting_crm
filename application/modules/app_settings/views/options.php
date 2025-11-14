<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Application</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item active">App</li>
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
            <div class="card card-default">
               <div class="card-header options">
                  <h5>Settings</h5>
               </div>
               <div class="card-body opt-lnk">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a href="/app_settings/admin">
                           Manage Admin Users <span>Super user can create / Edit Admin users</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/app_settings/configuration">
                           Module Configuration <span>Configure Modules for admin users</span>
                        </a>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <a href="/dashboard" class="btn btn-warning btn-sm float-right" style="font-size: 1rem;">
                     <i class="fa-solid fa-angles-left"></i> Dashboard
                  </a>
               </div>
            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<div id="passwordModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <span style="margin: 0; display: block;">Change Password</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute; top: 15px; right: 20px;" data-toggle="modal" data-target="#passwordModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body">
               <div class="row" style="margin-bottom: 15px">
                  <div class="col-12">
                     <div class="row">
                        <label for="identity" class="col-12 control-label float-right">User Name <span class="req">*</span></label>
                        <div class="col-12">
                           <select id="identity" class="form-control">
                              <?php echo $admins; ?>
                           </select>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="row" style="margin-bottom: 15px">
                  <label for="password" class="col-12 control-label">New Password</label>
                  <div class="col-12">
                     <input 
                        type="password" 
                        id="password" class="form-control w-200" 
                        onblur="this.setAttribute('readonly', 'readonly');" 
                        onfocus="this.removeAttribute('readonly');" style="display: inline" readonly />
                     
                     <a class="lnk_show_password"><i class="fa fa-eye"></i></a>
                     <a class="lnk_hide_password" style="display: none"><i class="fa fa-eye-slash"></i></a>
                  </div>
               </div>

               <div class="row" style="margin-bottom: 15px">
                  <label for="confirm_password" class="col-12 control-label">Confirm Password</label>
                  <div class="col-12">
                     <input 
                        type="password" 
                        id="confirm_password" class="form-control w-200" 
                        onblur="this.setAttribute('readonly', 'readonly');" 
                        onfocus="this.removeAttribute('readonly');" style="display: inline" readonly />
                     
                     <a class="lnk_show_password"><i class="fa fa-eye"></i></a>
                     <a class="lnk_hide_password" style="display: none"><i class="fa fa-eye-slash"></i></a>
                  </div>
               </div>

               <div class="row" style="margin-bottom: 15px">
                  <div class="col-12" style="font-size: 0.8rem">
                     <strong>Note:</strong> Password must be minimum 6 characters and maximum 15 characters.
                  </div>
               </div>
               
            </div>
            <div class="card-footer">
               <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#passwordModal">CANCEL</a>
               <button type="button" id="btn_change_password" class="btn btn-warning btn-sm float-right">SUBMIT</button>
            </div>
         </div>
      </div>
   </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
   .form-control[readonly] {
      background-color: #fff !important;
   }
</style>

<script type="text/javascript">

   $(function() {

      $('#identity').select2();

      $('#passwordModal').on('shown.bs.modal', function() {
         $('#identity').select2("destroy");
         $('#identity').val("");
         $('#identity').select2();

         $('#password').val("");
         $('#confirm_password').val("");
      });

      $(".lnk_show_password, .lnk_hide_password").on('click', function() {
         var password_field = $(this).closest('div').find('input').attr('id');
         
         if ($(this).hasClass('lnk_show_password')) {
            $("#" + password_field).attr("type", "text");
            $(this).closest('div').find(".lnk_show_password").hide();
            $(this).closest('div').find(".lnk_hide_password").show();
         } else {
            $("#" + password_field).attr("type", "password");
            $(this).closest('div').find(".lnk_hide_password").hide();
            $(this).closest('div').find(".lnk_show_password").show();
         }
      });

      $("#btn_change_password").on('click', function (e) {
         if(isPasswordModalValid()) {
            $.post('/app_settings/ajax/update_password', {
               identity: $('#identity').val(),
               username:
            }, function(res) {
               if($.trim(res) == '1') {
                  toastr.success("Receipt Created!");
                  table.ajax.reload();
                  $('#entryModal').modal('hide');
               }
            });
         }
      });

   });

   function isPasswordModalValid() {
      var valid = true;
      if($('#identity').val() == "") {
         $("#identity").select2('open');
         valid = false;
      
      } else if($('#password').val() == "") {
         $('#password').focus();
         valid = false;
      
      } else if($.trim($('#password').val()).length < 6 || $.trim($('#password').val()).length > 15) {
         $('#password').focus();
         valid = false;
         toastr.error("Password must be minimum 6 characters and maximum 15 characters.");
      
      } else if($('#confirm_password').val() == "") {
         $('#confirm_password').focus();
         valid = false;
      
      } else if($('#password').val() !== $('#confirm_password').val()) {
         $('#confirm_password').focus();
         valid = false;
         toastr.error("Password does not match!");
      }

      return valid;
   }

</script>