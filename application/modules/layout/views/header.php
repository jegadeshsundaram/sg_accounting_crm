<!DOCTYPE html>
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <title class="no-print">CRM | <?php echo ucwords(str_replace('_', ' ', $this->uri->segment(1))); ?></title>

      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta http-equiv="cache-control" content="max-age=0" />
   
      <!-- Favicon -->
      <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon" />

      <!-- bootstrap 4.6.2 -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
           
      <!-- font awesome 6.4.0 -->
      <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

      <!-- theme stylesheet-->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/2.2.1/styles/overlayscrollbars.min.css">

      <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

      <link rel="stylesheet" href="/assets/css/custom.css">

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

      <script 
         src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js" 
         integrity="sha512-3j3VU6WC5rPQB4Ld1jnLV7Kd5xr+cq9avvhwqzbH/taCRNURoeEpoPBK9pDyeukwSxwRPJ8fDgvYXd6SkaZ2TA==" 
         crossorigin="anonymous" referrerpolicy="no-referrer"></script>

      <script 
         src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js" 
         integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA==" 
         crossorigin="anonymous" referrerpolicy="no-referrer"></script>

      <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

   </head>
   <!-- Sidebar Fixed and apply scroll-bar to sidebar -->
   <body class="sidebar-mini layout-fixed layout-navbar-fixed sidebar-collapse" style="height: auto;">
   
   <!--<body class="control-sidebar-slide-open" style="height: auto;">-->
      <div class="wrapper">
         <!-- Preloader -->
         <div class="preloader" style="display: none">
            <img src="/assets/images/logo.jpeg" alt="Accounting CRM" />
         </div>

         <!-- Navbar -->
         <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
               <li class="nav-item">
                  <a class="nav-link sb-menu-bar" href="javascript:void(0);" role="button">
                     <i class="fas fa-bars fa-xl" style="color: slategray;"></i>
                  </a>
               </li>

               <li class="nav-item d-none d-sm-inline-block*" style="display: none">
                  <a href="index3.html" class="nav-link">Home</a>
               </li>
               <li class="nav-item d-none d-sm-inline-block*" style="display: none">
                  <a href="javascript:void(0);" class="nav-link">Contact</a>
               </li>
               <li class="nav-item dropdown" style="display: none">
                  <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown2" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Help
                  </a>
                  <div class="dropdown-menu" aria-labelledby="navbarDropdown2">
                  <a class="dropdown-item" href="javascript:void(0);">FAQ</a>
                  <a class="dropdown-item" href="javascript:void(0);">Support</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="javascript:void(0);">Contact</a>
                  </div>
               </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">

               <li class="nav-item">
                  <a title="Full Screen" class="new nav-link" data-widget="fullscreen" href="#" role="button">
                     <i class="fas fa-expand-arrows-alt fa-xl" style="color: slategray;"></i>                     
                  </a>
               </li>

               <li class="nav-item dropdown">
                  <a class="right log" data-toggle="dropdown" href="javascript:void(0);">
                     <!--<i class="fa-solid fa-user-tie fa-xl rounded-circle"></i>-->
                     <img src="https://crm2023e10.topjeg.com/assets/images/avatar5.png" class="rounded-circle" alt="User Image" width="40" height="40">
                  </a>
                  
                  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
    
                     <p class="dropdown-header">
                        <span style="color: tomato; letter-spacing: 2px"><i>Welcome!</i></span> <br />
                        <?php echo ucfirst($this->session->username); ?> <span style="color: gray">[<?php echo ucfirst($this->session->level); ?>]</span>
                     </p>

                     <div class="dropdown-divider"></div>
                     <a href="/company_profile" class="dropdown-item">
                        <i class="fa fa-arrow-right"></i> <span>Company Profile</span>
                     </a>

                     <div class="dropdown-divider"></div>
                     <a href="/data_migration/options" class="dropdown-item">
                        <i class="fa fa-arrow-right"></i> <span>Data Files Migration</span>
                     </a>

                     <div class="dropdown-divider"></div>
                     <a href="/gst/gst_returns_api" class="dropdown-item">
                        <i class="fa fa-arrow-right"></i> <span>GST API Submission</span>
                     </a>

                     <div class="dropdown-divider"></div>
                     <a href="javascript:void(0);" class="dropdown-item dropdown-footer" data-toggle="modal" data-target="#logoutModal">Logout &nbsp; &nbsp;<i class="nav-icon fa-solid fa-right-from-bracket"></i></a>
                  </div>
               </li>
            </ul>
         </nav>
         <!-- /.navbar -->
   	
         <?php require_once 'sidebar.php'; ?>