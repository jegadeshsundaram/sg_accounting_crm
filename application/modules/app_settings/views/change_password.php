<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Change Admin Credentials</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">System Manager</a></li>
              <li class="breadcrumb-item active">Admin Settings</li>
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
         <!--<div class="col-xl-8 col-lg-12 col-md-11">-->
         <div class="col-lg-12">
            <form id="form_" class="form-horizontal validate" method="post" action="/app_settings/change_password/save">
               <div class="card card-default">
                  <div class="card-body">
                     
                     <div class="form-group row">
                        <label for="identity" class="col-md-4 control-label">Identity : </label>
                        <div class="col-md-8 error_block">
                           <select name="identity" id="identity" class="form-control select2 iden_select">
                           <?php echo $admin_options_identity; ?>
                           </select>
                        </div>
                     </div>

                     <div class="form-group row">
                        <label for="username" class="col-md-4 control-label">Username : </label>
                        <div class="col-md-8 error_block">
                           <input class="form-control" type="text" name="username" id="username" />
                        </div>
                     </div>

                     <div class="form-group row">
                        <label for="new_password" class="col-md-4 control-label">New Password : </label>
                        <div class="col-md-8 error_block">
                           <input class="form-control" name="new_password" id="new_password" type="password" />
                        </div>
                     </div>

                     <div class="form-group row">
                        <label for="confirm_new_password" class="col-md-4 control-label">Confirm New Password : </label>
                        <div class="col-md-8 error_block">
                           <input class="form-control" name="confirm_new_password" id="confirm_new_password" type="password" />
                        </div>
                     </div>
                  </div>

                  <div class="card-footer">
                     <a href="/app_settings" class="btn btn-info">Cancel</a>          
                     <button type="submit" class="btn btn-warning float-right">Submit</button>
                  </div>
                     
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
   $(document).ready(function() {

      $('#identity').select2();

      //$('input').attr('autocomplete', 'nope');

      $.validator.addMethod("passwordMatch", function (value, element) {
         var password = $('#new_password').val();
         if(value == password) {
            return true;
         } else {
            return false;
         }
      }, "");

      $.validator.addMethod("lengthCheck", function (value, element) {
         if(value !== "") {
            if($.trim(value).length >= 6 && $.trim(value).length <= 15) {
               return true;
            } else {
               return false;
            }
         } else {
            return false;
         }
      }, "");

      var validator = $("#form_").validate({
         rules: {
            identity: {
               required: true
            },
            username: {
               required: true,
               lengthCheck: true
            },
            new_password: {
               required: true,
               lengthCheck: true
            },
            confirm_new_password: {
               required: true,
               passwordMatch: true
            }
         },

         messages: {
            identity: {
               required: "This field is required"
            },
            username: {
               required: "This field is required",
               lengthCheck: "Minimum 6 characters and Maximum 15 characters"
            },
            new_password: {
               required: "This field is required",
               lengthCheck: "Minimum 6 characters and Maximum 15 characters"
            },
            confirm_new_password: {
               required: "This field is required",
               passwordMatch: "Password does not match"
            }
         },

         highlight: function (element) {
            $(element).addClass("field-error");
         },

         unhighlight: function (element) {
            $(element).removeClass("field-error");
         },

         errorPlacement: function (error, element) {
            if (element.attr("name") == "identity") {
               $(error).insertAfter(element.closest('div').find('.select2-container'));
            } else {
               error.insertAfter(element);
            }
         }

      }); // validation ends


      $(document).on('change', '.iden_select', function() {
         var iden = $('option:selected', this).val();
         if(iden !== "") {
            $('#username').val($('option:selected', this).text());
            $('#identity-error').hide();
            $('#username').removeClass("field-error");
            $('#username').focus();
         }
      });

   }); // document ends
</script>
