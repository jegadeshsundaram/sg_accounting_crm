<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
         </div>
         <div class="col-sm-6">
         </div>
      </div>
   </div>
</div>

<div class="content">
   <div class="container-fluid">
      <div class="row">
         
         <div class="col-lg-12">

            <div class="row">

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->system_manager == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9 dt-box" id="sm">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>System Manager</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>
               
               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->quotation == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="quot">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>Quotation</h3>
                     </div>
                     <div class="icon">
                        <i class="fa fa-cubes"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->invoice == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="inv">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>Invoice</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>
            
               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->receipt == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="rec">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>Receipt</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-solid fa-receipt"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->accounts_receivable == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="ar">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>Accounts Receivable</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-brands fa-joget"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->stock == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="stock">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>Stock</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-solid fa-business-time"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->payment_to_supplier == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="pay">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>Payment to Supplier</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-solid fa-money-check-dollar"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->accounts_payable == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="ap">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>Accounts Payable</h3>
                     </div>
                     <div class="icon">
                        <i class="fa fa-window-restore"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->petty_cash == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="pc">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>Petty Cash</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-solid fa-sack-dollar"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->foreign_bank == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="fb">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>Foreign Bank</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-solid fa-money-bill-trend-up"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->bank_recon == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="br">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>Bank Reconciliation</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-solid fa-swatchbook"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->accountant_module == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="am">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>Accountant Module</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->gst == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="gst">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>GST</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-brands fa-servicestack"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->ez_entry == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="ez">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>EZ Entry</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-solid fa-table-cells"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->staff_activity == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="st">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>Staff Activity</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-solid fa-people-carry-box"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->sac_job_control == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="sac">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>SAC Job Control</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-solid fa-list-check"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->data_files_migration == 1) { ?>
               <div class="col-xl-4 col-md-6 col-sm-9" id="tfm">
                  <div class="small-box bg-warning">
                     <div class="inner">
                        <h3>Data Files Migration</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-sharp fa-solid fa-file-import"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
               <?php } ?>

               <div class="col-xl-4 col-md-6 col-sm-9" data-toggle="modal" data-target="#logoutModal">
                  <div class="small-box bg-warning" style="background: darkgray !important">
                     <div class="inner">
                        <h3 style="color: gainsboro">Logout</h3>
                     </div>
                     <div class="icon">
                        <i class="fa-solid fa-right-from-bracket"></i>
                     </div>
                     <a href="#" class="small-box-footer">
                        Open Menu <i class="fas fa-arrow-circle-right"></i>
                     </a>
                  </div>
               </div>
            </div><!-- row ends -->
         </div>

      </div> <!-- row ends -->

   </div> <!-- container-fluid ends -->
</div> <!-- content ends -->