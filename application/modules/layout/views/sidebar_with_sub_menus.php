<?php
$where = ['process' => 'modules'];
$modules_permission = json_decode($this->custom->getSingleValue('configuration_master', 'permissions', $where));
?>

<!-- Main Sidebar Container -->
   <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="/dashboard" class="brand-link">
         <span class="brand-text font-weight-light">TRADPAC ACCOUNTING</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
         <!-- User Panel  -->
         <div class="user-panel mt-3 pb-3 mb-3 d-flex">
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
                        <a href="/app_settings/change_password" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Change Admin Credentials</p>
                        </a>
                     </li>
                     <?php } ?>
                     
                     <?php if ($this->ion_auth->is_admin() && $this->session->group_id == 1) { ?>
                     <li class="nav-item">
                        <a href="/app_settings/configuration" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Configuration</p>
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
                  <a href="javascript:void(0);" class="nav-link">
                     <i class="nav-icon fa fa-cubes"></i>
                     <p>
                        Quotation
                        <i class="right fas fa-angle-left fa-sm"></i>
                     </p>
                  </a>
                  <ul class="nav nav-treeview">
                     <li class="nav-item">
                        <a href="/quotation/settings" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Settings</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/quotation" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Create</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/quotation/qt_listing_options" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Listing</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/quotation/qt_report_options" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Reports</p>
                        </a>
                     </li>
                     
                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Utilities <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/quotation/df_options" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Files</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
               </li>
               <?php } ?>
               
               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->invoice == 1) { ?>
               <li class="nav-item sb-inv">
                  <a href="javascript:void(0);" class="nav-link">
                     <i class="nav-icon fa-solid fa-file-invoice-dollar"></i>
                     <p>
                        Invoice
                        <i class="right fas fa-angle-left fa-sm"></i>
                     </p>
                  </a>
                  <ul class="nav nav-treeview">
                     <li class="nav-item">
                        <a href="/invoice/settings" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Settings</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/invoice/inv_create_options" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Create</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/invoice/inv_listing_options" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Listing</p>
                        </a>
                     </li>
                     
                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Utilities <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/invoice/customer_price" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Customer Special Price</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/invoice/df_options" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Files</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
               </li>
               <?php } ?>
               
               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->receipt == 1) { ?>
               <li class="nav-item sb-rec">
                  <a href="javascript:void(0);" class="nav-link">
                     <i class="nav-icon fa-solid fa-receipt"></i>
                     <p>
                        Receipt
                        <i class="right fas fa-angle-left fa-sm"></i>
                     </p>
                  </a>
                  <ul class="nav nav-treeview">
                     <li class="nav-item">
                        <a href="/receipt/settings" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Settings</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/receipt" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Create</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/receipt/listing_options" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Listing</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Utilities <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/receipt/df_options" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Files</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->accounts_receivable == 1) { ?>
               <li class="nav-item sb-ar">
                  <a href="javascript:void(0);" class="nav-link">
                     <i class="nav-icon fa-brands fa-joget"></i>
                     <p>
                        Accounts Receivable
                        <i class="right fas fa-angle-left fa-sm"></i>
                     </p>
                  </a>
                  <ul class="nav nav-treeview">
                     <li class="nav-item">
                        <a href="/accounts_receivable/audit_listing" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Audit Listing</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/accounts_receivable/batch_ob_options" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Opening Balance b/f</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/accounts_receivable/debtor_options" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Debtors</p>
                        </a>
                     </li>
                     
                     <li class="nav-item">
                        <a href="/accounts_receivable/offset" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Remove Settled Transactions</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Utilities <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/accounts_receivable/ar_ob_listing" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Patch</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/accounts_receivable/df_options" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Files</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->stock == 1) { ?>
               <li class="nav-item sb-stock">
                  <a href="javascript:void(0);" class="nav-link">
                     <i class="nav-icon fa-solid fa-business-time"></i>
                     <p>
                        Stock
                        <i class="right fas fa-angle-left fa-sm"></i>
                     </p>
                  </a>
                  <ul class="nav nav-treeview">
                     <li class="nav-item">
                        <a href="/stock/audit_listing" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Audit Listing</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/stock/ob" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Opening Stock b/f</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/stock/purchase" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Goods Received</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/stock/adjustment" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Adjustment</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/stock/reports" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Reports</p>
                        </a>
                     </li>
                     
                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Utilities <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/stock/dp_listing" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Patch</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/stock/df_options" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Files</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->payment_to_supplier == 1) { ?>
               <li class="nav-item sb-pay">
                  <a href="javascript:void(0);" class="nav-link">
                     <i class="nav-icon fa-solid fa-money-check-dollar"></i>
                     <p>
                        Payment to Supplier
                        <i class="right fas fa-angle-left fa-sm"></i>
                     </p>
                  </a>
                  <ul class="nav nav-treeview">
                     <li class="nav-item">
                        <a href="/payment/settings" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Settings</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/payment" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Create</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/payment/listing_options" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Listing</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Utilities <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/payment/df_options" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Files</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->accounts_payable == 1) { ?>
               <li class="nav-item sb-ap">
                  <a href="javascript:void(0);" class="nav-link">
                     <i class="nav-icon fa fa-window-restore"></i>
                     <p>
                        Accounts Payable
                        <i class="right fas fa-angle-left fa-sm"></i>
                     </p>
                  </a>
                  <ul class="nav nav-treeview">
                     <li class="nav-item">
                        <a href="/accounts_payable/audit_listing" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Audit Listing</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/accounts_payable/batch_ob_options" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Opening Balance b/f</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/accounts_payable/creditor_options" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Creditors</p>
                        </a>
                     </li>

                     <li class="nav-item">
                        <a href="/accounts_payable/offset" class="nav-link">
                        <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Remove Settled Transactions</p>
                        </a>
                     </li>

                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Utilities <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/accounts_payable/ap_ob_listing" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Patch</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/accounts_payable/df_options" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Files</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->petty_cash == 1) { ?>
               <li class="nav-item sb-pc">
                  <a href="javascript:void(0);" class="nav-link">
                     <i class="nav-icon fa-solid fa-sack-dollar"></i>
                     <p>
                        Petty Cash
                        <i class="right fas fa-angle-left fa-sm"></i>
                     </p>
                  </a>
                  <ul class="nav nav-treeview">
                     <li class="nav-item">
                        <a href="/petty_cash/settings" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Settings</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/petty_cash/create" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Petty Cash Voucher</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Utilities <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/petty_cash/df_options" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Files</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->foreign_bank == 1) { ?>
               <li class="nav-item sb-fb">
                  <a href="javascript:void(0);" class="nav-link">
                     <i class="nav-icon fa-solid fa-money-bill-trend-up"></i>
                     <p>
                        Foreign Bank
                        <i class="right fas fa-angle-left fa-sm"></i>
                     </p>
                  </a>
                  <ul class="nav nav-treeview">
                     <li class="nav-item">
                        <a href="/foreign_bank/audit_listing" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Audit Listing</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/foreign_bank/ob" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Opening Balance b/f</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/foreign_bank/reports" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Reports</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Utilities <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/foreign_bank/df_options" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Files</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
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
                  <a href="javascript:void(0);" class="nav-link">
                     <i class="nav-icon fa-solid fa-file-invoice-dollar"></i>
                     <p>
                        Accountant Module
                        <i class="right fas fa-angle-left fa-sm"></i>
                     </p>
                  </a>
                  <ul class="nav nav-treeview">
                     <li class="nav-item">
                        <a href="/general_ledger/audit_listing" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Audit Listing</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/general_ledger/ob" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Opening Balance b/f</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/general_ledger/reports" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Reports</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Tips & Tricks <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/general_ledger/tips_predefined_accounts" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Predefined Accounts</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Utilities <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/general_ledger/chart_of_account" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Chart of Account</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/general_ledger?page=dp" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Patch</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/general_ledger/df_options" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Files</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/general_ledger/ye_closing" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Year End Closing</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
               </li>
               <?php } ?>

               <?php if (($this->ion_auth->is_admin() && $this->session->group_id == 1) || $modules_permission->gst == 1) { ?>
               <li class="nav-item sb-gst">
                  <a href="javascript:void(0);" class="nav-link">
                     <i class="nav-icon fa-brands fa-servicestack"></i>
                     <p>
                        GST
                        <i class="right fas fa-angle-left fa-sm"></i>
                     </p>
                  </a>
                  <ul class="nav nav-treeview">
                     <li class="nav-item">
                        <a href="/gst/audit_listing" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Audit Listing</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/gst/ob" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Opening Balance b/f</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/gst/reports" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Reports</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/gst/iras_api" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>IRAS API Submission</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Utilities <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="/gst?page=dp" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Patch</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/gst?page=rs" class="nav-link gst_revenue_setting">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Revenue Setting</p>
                              </a>
                           </li>  
                           <li class="nav-item">
                              <a href="/gst/df_options" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Data Files</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
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
                  <a href="javascript:void(0);" class="nav-link">
                     <i class="nav-icon fa-solid fa-list-check"></i>
                     <p>
                        SAC Job Control
                        <i class="right fas fa-angle-left fa-sm"></i>
                     </p>
                  </a>
                  <ul class="nav nav-treeview">
                     <li class="nav-item">
                        <a href="/sac_job_control/job/create" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Create New Job</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/sac_job_control/job/list/edit" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Edit Job</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/sac_job_control/job/list/delete" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Delete Job</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="/sac_job_control/job/reports" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Reports</p>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link">
                           <i class="fa fa-arrow-right nav-icon"></i>
                           <p>Utilities <i class="right fas fa-angle-left fa-sm"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                           <li class="nav-item">
                              <a href="javascript:void(0);" class="nav-link backup_sac_job">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Backup</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="/sac_job_control/job/restore_sac_job" class="nav-link">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Restore</p>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="javascript:void(0);" class="nav-link zap_sac_job">
                                 <i class="fa-solid fa-angles-right fa-2xs nav-icon"></i>
                                 <p>Zap</p>
                              </a>
                           </li>
                        </ul>
                     </li>
                  </ul>
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