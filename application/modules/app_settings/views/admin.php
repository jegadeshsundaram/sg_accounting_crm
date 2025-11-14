<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Admin</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
                <li class="breadcrumb-item">Admin</li>
                <li class="breadcrumb-item active">Users</li>
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

      <div class="row buttons-panel">
         <div class="col-lg-12">
            <div class="card card-default">
               <div class="card-header">
                  <h5>User</h5>
                  <a href="/app_settings" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               
               <div class="card-header">
                  <button class='btn btn-primary btn-sm' id="btn_create_user">
                     <i class="fa fa-plus-circle" aria-hidden="true"></i> Create User
                  </button>
                  <button class='btn btn-warning btn-sm' id='btn_change_password'>
                     <i class='fa-solid fa-upload'></i> Change Password
                  </button>
                  <button class='btn btn-danger btn-sm' id='btn_delete_all'>
                     <i class='fa fa-trash'></i> Delete All
                  </button>
               </div>
               <div class="card-body table-responsive">
                  <div class="row">
                     <div class="col-lg-12">
                        <table id="dt_" class="table table-hover" style="min-width: 1100px; width: 100%;">
                           <thead>
                              <tr>
                                 <th></th>
                                 <th>Action</th>
                                 <th>Name</th>
                                 <th>User ID</th>
                                 <th>Email</th>
                                 <th>Created On</th>
                                 <th>Last Login</th>
                              </tr>
                           </thead>
                           <tbody></tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div> <!-- Card - ends -->

         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<!-- Create User - Modal -->
<div id="userModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-body">
               <div class="row form-group">
                  <div class="col-12">
                     <label for="emp_name" class="control-label">First & Last Name<span class="cl-red">*</span></label>
                     <input type="text" id="emp_name" class="form-control w-300" />
                     <span class="tip">Name to be displayed on the dashboard<span>
                  </div>
               </div>

               <hr />

               <div class="row form-group">
                  <div class="col-12">
                     <label for="username" class="control-label">User Name<span class="cl-red">*</span></label><br />
                     
                     <input 
                        type="text" 
                        id="username" class="form-control w-180" 
                        onblur="this.setAttribute('readonly', 'readonly');" 
                        onfocus="this.removeAttribute('readonly');" style="display: inline" readonly />
                     
                     <span class="tip">This will be used to login. <u>Minimum</u> : 6 Characters<span>
                     <span class="error-user error" style="display: none;">Duplicate user disallowed</span>
                  </div>
               </div>

               <hr />

               <div class="row form-group password_field" style="display: none">
                  <div class="col-12">
                     <label for="password" class="control-label" style="display: block">Pasword<span class="cl-red">*</span></label>
                     
                     <input 
                        type="password" 
                        id="password" class="form-control w-180" 
                        onblur="this.setAttribute('readonly', 'readonly');" 
                        onfocus="this.removeAttribute('readonly');" style="display: inline" readonly
                        style="display: inline" />
                     
                     <a class="lnk_show_password"><i class="fa fa-eye-slash"></i></a>
                     <a class="lnk_hide_password" style="display: none"><i class="fa fa-eye"></i></a>
                     <span class="tip">This will be used to login. <u>Minimum</u> : 6 Characters & <u>Maximum</u> : 15 Characters<span>
                  </div>
               </div>

               <hr class="password_field" style="display: none" />

               <div class="row form-group">
                  <div class="col-12">
                     <label for="email" class="control-label">Email Address<span class="cl-red">*</span></label>
                     <input type="text" id="email" class="form-control" />
                     <span class="tip">Email Address of the User<span>
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <input type="hidden" name="user_id" id="user_id" />
               <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#userModal">CANCEL</button>
               <button type="button" class="btn btn-primary btn-sm float-right" id="btn_submit">SUBMIT</button>
            </div>
         </div>
      </div>
   </div>
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
                           <span class="tip">Select username and update the password<span>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="row" style="margin-bottom: 15px">
                  <label for="password" class="col-12 control-label">New Password</label>
                  <div class="col-12">
                     <input 
                        type="password" 
                        id="new_password" class="form-control w-200" 
                        onblur="this.setAttribute('readonly', 'readonly');" 
                        onfocus="this.removeAttribute('readonly');" style="display: inline" readonly />
                     
                     <a class="lnk_show_password"><i class="fa fa-eye-slash"></i></a>
                     <a class="lnk_hide_password" style="display: none"><i class="fa fa-eye"></i></a>
                     <span class="tip">This will be used to login. <u>Minimum</u> : 6 Characters & <u>Maximum</u> : 15 Characters<span>
                  </div>
               </div>
               
            </div>
            <div class="card-footer">
               <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#passwordModal">CANCEL</a>
               <button type="button" id="btn_update_password" class="btn btn-warning btn-sm float-right">UPDATE</button>
            </div>
         </div>
      </div>
   </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="/assets/js/datatable.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
   .form-control[readonly] {
      background-color: #fff !important;
   }
</style>

<script type="text/javascript">
   var url = '';
   var duplicate_user = false;
   
   $(function() {
      table = $('#dt_').DataTable({
         'processing': true,
         'ajax': {
            'url': '/app_settings/ajax/populate_admins',
            'type': 'post'
         },
         'columnDefs': [
            { 'width': 120, 'targets': 1 },
            { 'width': 200, 'targets': 2 },
            { 'width': 140, 'targets': 3 },
            { 'width': 150, 'targets': 5 },
            { 'width': 150, 'targets': 6 }
         ],
         "aaSorting": []
      });
      table.column( 0 ).visible( false ); // id

      $('#passwordModal select').select2();

      $("#btn_create_user").on('click', function() {
         clear_inputs();
         $('#btn_submit').html("CREATE");
         $('.password_field').show();
         $('#userModal').modal('show');
      });

      $("#btn_change_password").on('click', function() {
         $('#identity').val("").trigger('change');
         $('#new_password').val("");

         $('#passwordModal').modal('show');
      });

      $("#btn_delete_all").on('click',function() {
         if(table.rows().eq(0).length > 0) {
            selectAllRows();

            url = '/app_settings/ajax/delete_user';
            showData("deleteAll", url);
         }
      });

      // EDIT - Single Entry
      $('#dt_').on('click', 'tbody .dt_edit', function () {
         var rowData = table.row($(this).closest('tr')).data();
         var rowID = rowData[0];
         //$(this).closest('tr').addClass('selected');

         clear_inputs();
         $('.password_field').hide();

         $('#user_id').val(rowID);
         $.post('/app_settings/ajax/get_user', {
            user_id: rowID
         }, function (user) {
            var obj = $.parseJSON(user);

            $('#emp_name').val(obj.user['emp_name']);
            $('#username').val(obj.user['username']);
            $('#email').val(obj.user['email']);

            $('#btn_submit').html("UPDATE");
            $('#userModal').modal('show');
         });
      });

      // DELETE - Single Entry
      $('#dt_').on('click', 'tbody .dt_delete', function () {
         var rowData = table.row($(this).closest('tr')).data();
         var rowID = rowData[0];
         console.log(rowID);

         $(this).closest('tr').addClass('selected');

         $.confirm({
            title: '<i class="fa fa-info"></i> Delete User',
            content: 'Are you sure to delete?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function() {
                     $.post('/app_settings/ajax/delete_user', {
                        user_id: rowID
                     }, function (res) {
                        if(res == "error") {
                           toastr.error("Delete Error!");
                        } else {
                           toastr.success("User Deleted!");
                           table.ajax.reload();
                        }
                     });
                  }
               },
               no: {
                  btnClass: 'btn-dark',
                  action: function(){
                     table.ajax.reload();
                  }
               },
            }
         });
      });
    
      $('#userModal').on('shown.bs.modal', function() {
         if($('#user_id').val() == "") {
            $('#emp_name').focus();
         }
      });

      $(document).on("change", "#username", function() {
         if($(this).val() == "") {
            return false;
         }
         duplicate_user = false;

         $(".error-user").hide();
         $.post('/app_settings/ajax/double_user', {
            username: $(this).val()
         }, function(user) {
            if (user > 0) {
               duplicate_user = true;
               $("#username").focus();
               $(".error-user").show();
            }
         });
      });

      $("#btn_update_password").on('click', function (e) {
         if(isPasswordModalValid()) {
            $.post('/app_settings/ajax/update_password', {
               user_id: $('#identity').val(),
               password: $('#new_password').val()
            }, function(res) {
               if($.trim(res) == 'updated') {
                  toastr.success("Password Updated!");
                  table.ajax.reload();
                  $('#passwordModal').modal('hide');
               }
            });
         }
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

      $("#btn_submit").on('click', function (e) {
         if(duplicate_user) {
            $('#username').focus();
            return false;
         } 

         if(isFormValid()) {
            save();
         }
      });

   }); // document ends
   

   function clear_inputs() {
      $('#user_id').val('');

      $('#emp_name').val('');
      $('#username').val('');
      $('#password').val('');
      $('#email').val('');
   }

   function isFormValid() {
      var valid = true;
      if($('#emp_name').val() == "") {
         $('#emp_name').focus();
         valid = false;      
      } else if($('#username').val() == "") {
         $("#username").focus();
         valid = false;
      } else if($('#username').val().length < 6) {
         $("#username").focus();
         valid = false;
      } else if($('#user_id').val() == "" && $('#password').val() == "") {
         $('#password').focus();
         valid = false;
      } else if($('#user_id').val() == "" && ($.trim($('#password').val()).length < 6 || $.trim($('#password').val()).length > 15)) {
         $('#password').focus();
         toastr.error("Password must be minimum 6 characters and maximum 15 characters.");
         valid = false;
      } else if($('#email').val() == "") {
         $('#email').focus();
         valid = false;
      }

      return valid;
   }

   function isPasswordModalValid() {
      var valid = true;
      if($('#identity').val() == "") {
         $("#identity").select2('open');
         valid = false;
      
      } else if($('#new_password').val() == "") {
         $('#new_password').focus();
         valid = false;
      
      } else if($.trim($('#new_password').val()).length < 6 || $.trim($('#new_password').val()).length > 15) {
         $('#password').focus();
         valid = false;
         toastr.error("Password must be minimum 6 characters and maximum 15 characters.");      
      }

      return valid;
   }

   function save() {
      $.post('/app_settings/ajax/save_user', {
         user_id: $('#user_id').val(),
         emp_name: $('#emp_name').val(),
         username: $('#username').val(),
         password: $('#password').val(),
         email: $('#email').val()
      }, function(res) {
         if($.trim(res) == '1') {
            toastr.success("User Created!");
            table.ajax.reload();
            $('#userModal').modal('hide');
         } else if($.trim(res) == 'updated') {
            toastr.success("User Updated!");
            table.ajax.reload();
            $('#userModal').modal('hide');
         } else {
            toastr.error("Save error!");
         }
      });
   }
</script>
