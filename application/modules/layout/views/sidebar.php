<?php
$where = ['process' => 'modules'];
$modules_permission = json_decode($this->custom->getSingleValue('configuration_master', 'permissions', $where));
?>

<!-- Main Sidebar Container -->
   <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="/dashboard" class="brand">
         TRADPAC CRM
         <span>Accounting made easy</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
         <!-- User Panel  -->
         <div class="user-panel mt-3 mb-3 d-flex">
            <div class="image">
               <img src="<?php echo IMG_PATH.'avatar5.png'; ?>" alt="User Image" />
            </div>
            <div class="info">
               <a href="javascript:void(0);" class="d-block">Hi, <?php echo ucfirst($this->session->username); ?></a>
               <a href="javascript:void(0);" class="user"><i class="fa fa-circle text-success fa-sm" aria-hidden="true"></i> <?php echo ucfirst($this->session->level); ?></a>
            </div>
         </div>

         <!-- Sidebar Menu -->
         <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-child-indent text-sm" data-widget="treeview" role="menu">
               <!-- Add icons to the links using the .nav-icon class
                  with font-awesome or any other icon font library -->
               <li class="nav-item sb-db">
                  <a href="/dashboard" class="nav-link">
                     <i class="nav-icon fa-solid fa-gauge"></i>
                     <p>Dashboard</p>
                  </a>
               </li>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->system_manager == 1) { ?>
               <li class="nav-item sb-sm">
                  <a href="javascript:void(0);" class="nav-link">
                     <i class="nav-icon fa-solid fa-screwdriver-wrench"></i>
                     <p>
                        System Manager
                        <i class="right fas fa-angle-left fa-sm"></i>
                     </p>
                  </a>
                  <ul class="nav nav-treeview">
                     <?php if ($this->ion_auth->is_admin() && $this->session->group_id == 1) { ?>
                     <li class="nav-item">
                        <a href="/app_settings/" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>App Settings</p>
                        </a>
                     </li>
                     <?php } ?>
                     
                     <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1 || $this->session->level == 'admin')) { ?>
                     <li class="nav-item">
                        <a href="/company_profile" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Company Profile</p>
                        </a>
                     </li>
                     <?php } ?>
                
                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Master Files <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/master_files/customer" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Customer</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/master_files/supplier" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Supplier</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/master_files/billing" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Billing</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/master_files/employee" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Employee</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/master_files/department" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Department</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/master_files/foreign_bank" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Foreign Bank</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/master_files/accountant" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Accountant</p>
                              </a>
                           </li>
                        </ul>
                     </li>

                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Combo Tables <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/combo_tables/currency" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Currency</p>
                              </a>
                           </li>
                           <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                           <li class="nav-item">
                              <a href="/combo_tables/gst" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>GST</p>
                              </a>
                           </li>
                           <?php } ?>
                           <li class="nav-item">
                              <a href="/combo_tables/country" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Country</p>
                              </a>
                           </li>
                        </ul>
                     </li>

                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Master Utilities <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/system_utilities/db_options" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Database</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/combo_tables/ye_revision_options" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Year End Revision</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->quotation == 1) { ?>
               <li class="nav-item sb-quot">
                  <a href="/quotation" class="nav-link">
                     <i class="nav-icon fa fa-cubes"></i>
                     <p>Quotation</p>
                  </a>
               </li>
               <?php } ?>
               
               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->invoice == 1) { ?>
               <li class="nav-item sb-inv">
                  <a href="/invoice" class="nav-link">
                     <i class="nav-icon fa-solid fa-file-invoice-dollar"></i>
                     <p>Invoice</p>
                  </a>
               </li>
               <?php } ?>
               
               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->receipt == 1) { ?>
               <li class="nav-item sb-rec">
                  <a href="/receipt" class="nav-link">
                     <i class="nav-icon fa-solid fa-receipt"></i>
                     <p>Receipt</p>
                  </a>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->accounts_receivable == 1) { ?>
               <li class="nav-item sb-ar">
                  <a href="/accounts_receivable" class="nav-link">
                     <i class="nav-icon fa-brands fa-joget"></i>
                     <p>Accounts Receivable</p>
                  </a>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->stock == 1) { ?>
               <li class="nav-item sb-stock">
                  <a href="/stock" class="nav-link">
                     <i class="nav-icon fa-solid fa-business-time"></i>
                     <p>Stock</p>
                  </a>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->payment_to_supplier == 1) { ?>
               <li class="nav-item sb-pay">
                  <a href="/payment" class="nav-link">
                     <i class="nav-icon fa-solid fa-money-check-dollar"></i>
                     <p>Payment to Supplier</p>
                  </a>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->accounts_payable == 1) { ?>
               <li class="nav-item sb-ap">
                  <a href="/accounts_payable" class="nav-link">
                     <i class="nav-icon fa fa-window-restore"></i>
                     <p>Accounts Payable</p>
                  </a>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->petty_cash == 1) { ?>
               <li class="nav-item sb-pc">
                  <a href="/petty_cash" class="nav-link">
                     <i class="nav-icon fa-solid fa-sack-dollar"></i>
                     <p>Petty Cash</p>
                  </a>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->foreign_bank == 1) { ?>
               <li class="nav-item sb-fb">
                  <a href="/foreign_bank" class="nav-link">
                     <i class="nav-icon fa-solid fa-money-bill-trend-up"></i>
                     <p>Foreign Bank</p>
                  </a>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->bank_recon == 1) { ?>
               <li class="nav-item sb-br">
                  <a href="/bank_reconciliation" class="nav-link">
                     <i class="nav-icon fa-solid fa-swatchbook"></i>
                     <p>Bank Reconciliation</p>
                  </a>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->accountant_module == 1) { ?>
               <li class="nav-item sb-am">
                  <a href="/general_ledger" class="nav-link">
                     <i class="nav-icon fa-solid fa-file-invoice-dollar"></i>
                     <p>Accountant Module</p>
                  </a>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->gst == 1) { ?>
               <li class="nav-item sb-gst">
                  <a href="/gst" class="nav-link">
                     <i class="nav-icon fa-brands fa-servicestack"></i>
                     <p>GST</p>
                  </a>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->ez_entry == 1) { ?>
               <li class="nav-item sb-tfm">
                  <a href="/ez_entry" class="nav-link">
                     <i class="nav-icon fa-solid fa-table-cells"></i>
                     <p>EZ Entry</p>
                  </a>
               <li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->staff_activity == 1) { ?>
               <li class="nav-item sb-sa">
                  <a href="/staff_activity" class="nav-link">
                     <i class="nav-icon fa-solid fa-people-carry-box"></i>
                     <p>Staff Activity</p>
                  </a>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->sac_job_control == 1) { ?>
               <li class="nav-item sb-sac">
                  <a href="/sac_job_control" class="nav-link">
                     <i class="nav-icon fa-solid fa-list-check"></i>
                     <p>SAC Job Control</p>
                  </a>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->data_files_migration == 1) { ?>
               <li class="nav-item sb-ez">
                  <a href="/data_migration/options" class="nav-link">
                     <i class="nav-icon fa-sharp fa-solid fa-file-import"></i>
                     <p>Data Files Migration</p>
                  </a>
               <li>
               <?php } ?>

               <li class="nav-item sb-logout">
                  <a href="javascript:void(0);" class="nav-link logout-btn" data-toggle="modal" data-target="#logoutModal">
                     <i class="nav-icon fa-solid fa-right-from-bracket"></i>
                     <p>Logout</p>
                  </a>
               <li>

            </ul> <!-- nav sidebar ends -->
         </nav>
         <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
   </aside>

   <div class="content-wrapper px-4 py-2" style="min-height: 600px;">