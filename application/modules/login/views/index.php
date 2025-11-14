<!doctype html>
<html>
<head>   
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
   <!-- Apple devices fullscreen -->
   <meta name="apple-mobile-web-app-capable" content="yes"/>
   <!-- Apple devices fullscreen -->
   <meta names="apple-mobile-web-app-status-bar-style" content="black-translucent"/>

   <!-- Favicon -->
   <link rel="icon" href="<?php echo ASSET_PATH.'images/favicon.ico'; ?>" type="image/x-icon" />

   <!-- bootstrap 4.6.2 -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

   <!-- font awesome 6.4.0 -->
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

   <!-- theme stylesheet-->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

   <!-- custom stylesheet -->
   <link rel="stylesheet" href="<?php echo CSS_PATH.'custom.css'; ?>" />

   <title>TRADPAC :: Sign in</title>
</head>
<body class='login-page' style="background-image: url('<?php echo ASSET_PATH.'images/login-bg-1.webp'; ?>'); background-size: cover;">

   <div class="login-box">

      <div class="card">
         
         <div class="card-header text-center">
            <img src="<?php echo ASSET_PATH.'images/logo.jpeg'; ?>" style="max-width: 70%;" />
            <?php echo $this->session->flashdata('info_msg'); ?>
         </div>

         <div class="card-body">
            <p class="login-box-msg">Sign in to start your session</p>
            <form method="post" id="login_form">
               <div class="input-group mb-3">
                  <input 
                     type="text" 
                     id="username" name="username" 
                     class="form-control inp" placeholder="Username" />

                  <div class="input-group-append" id="first_inp">
                     <div class="input-group-text">
                        <span class="fas fa-user"></span>
                     </div>
                  </div>
               </div>

               <div class="input-group mb-3" id="pwd_section">
                  <input 
                     type="password" 
                     id="password" name="password" 
                     class="form-control inp" placeholder="Password" />

                  <div class="input-group-append">
                     <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-7">
                     <a href="javascript:void(0);" id="fpwd_lnk" style="float: left">Forgot Password?</a>
                     <a href="javascript:void(0);" id="login_lnk" style="display: none; float: left">Go to Login</a>
                  </div>

                  <div class="col-5">
                     <button type="button" id="login_btn" class="btn" style="float: right; color: gainsboro; background-color: slategrey;">Sign In <i class="fa fa-angle-double-right pl-2" aria-hidden="true"></i></button>
                     <button type="button" id="fpwd_btn" class="btn" style="display: none; float: right; background-color: indianred; color: gainsboro">Submit <i class="fa fa-angle-double-right pl-2" aria-hidden="true"></i></button>
                  </div>

               </div>
            </form>
        
         </div>

         <div class="card-footer">
            Proudly Managed By <br>TRUELINE INFOTECH &copy; <?php echo date('Y'); ?>
         </div>

      </div>

   </div>  
    
   <!-- bootstrap and jquery -->
   <script
      src="https://code.jquery.com/jquery-3.7.0.min.js"
      integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g="
      crossorigin="anonymous"></script>

   <script 
      src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" 
      integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" 
      crossorigin="anonymous"></script>

   <script
      src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"
      integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0="
      crossorigin="anonymous"></script>
   
   <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

   <script 
      src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js" 
      integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA==" 
      crossorigin="anonymous" referrerpolicy="no-referrer"></script>

   <script 
      src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" 
      integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww==" 
      crossorigin="anonymous" referrerpolicy="no-referrer"></script>      

   <!-- Toastr -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
   <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

   <script src="<?php echo JS_PATH.'custom.js'; ?>"></script>
      
   <script type="text/javascript">
      window.onload = function() {
         document.getElementById("username").focus();
      };

      $(document).ready(function () {         

         $(document).on('keypress', '.inp', function(e) {
            var key = e.which;
            if(key == 13)  // the enter key code
            {
               $('#login_btn').click();
               return false;  
            }            
         });

         $("#login_btn").click(function (e) {
            var username = $('#username').val();
            var password = $('#password').val();

            if(username == "") {
               $('#username').focus();
            } else if(password == "") {
               $('#password').focus();
            } else {
               $.post('/login/login_check', {
                  username: username,
                  password: password
               }, function(data) {
                  data = $.trim(data);
                  if (data == "Fail") {
                     toastr.error('Check your credentials', 'Login Failed');
                  } else {                
                     toastr.success('Redirecting to Dashboard', 'Login Success');
                     window.location.href = "/dashboard";
                  }
               });
            }
         });

         $("#fpwd_lnk").click(function (event) {
            $("#pwd_section").slideUp();
            $("#username").attr('placeholder', 'Enter Your Registered Email ');
            $("#username").attr('type', 'email');
            $("#first_inp span").attr('class', 'fa-solid fa-envelope');

            $('.login-box-msg').text("Reset your password");
            
            $("#fpwd_lnk").hide();
            $("#login_lnk").show();

            $("#login_btn").hide();
            $("#fpwd_btn").show();

            $('#username').val("");
            $("#username").focus();
         });

         $("#login_lnk").click(function (event) {
            $("#pwd_section").slideDown();
            $("#username").attr('placeholder', 'Username');
            $("#username").attr('type', 'text');
            $("#first_inp span").attr('class', 'fas fa-user');

            $('.login-box-msg').text("Sign in to start your session");
            
            $("#login_lnk").hide();
            $("#fpwd_lnk").show();

            $("#fpwd_btn").hide();
            $("#login_btn").show();

            $('#username').val("");
            $("#username").focus();
         });

         $("#fpwd_btn").click(function (e) {
            var username = $('#username').val();

            if(username == "") {
               $('#username').focus();
            } else {
               $.post('/login/forgot_password', {
                  username: username
               }, function(data) {
                  data = $.trim(data);
                  if(data.length > 100) {
                     toastr.success('Email Sent', 'Activation Link');
                  } else {
                     toastr.error(data);
                  }
               });
            }
         });
         
      }); // document ends
   </script>

</body>
</html>
