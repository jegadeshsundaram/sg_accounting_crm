<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">General Ledger</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">GL</li>
               <li class="breadcrumb-item active">Reports</li>
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
               <div class="card-header">
                  <h5>Reports</h5>
                  <a href="/general_ledger/" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               
               <div class="card-body">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#stmtModal" class="btn btn-block" style="color: #fff; background: cadetblue;">
                           Transactions <span>Transactions will be printed based on the account selected or all accounts for the selected period. Reports can be downloaded in PDF as well as Excelsheet formats.</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#tbModal" class="btn btn-block" style="color: #fff; background: cadetblue;">
                           Trial Balance<span>A Trial balance is a financial report showing the closing balances of all accounts in the general ledger based on the cut-off date selected.</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#plModal" class="btn btn-block" style="color: #fff; background: cadetblue;">
                           Profit and Loss & Balance Sheet<span>The profit and loss (P&L) statement is a financial statement that summarizes the revenues, costs, and expenses incurred during a specified period</span>
                        </a>
                     </div>

                  </div>
               </div>
               <div class="card-footer">
                  <a href="/general_ledger" class="btn btn-warning btn-sm float-right" style="font-size: 1rem;">
                     <i class="fa-solid fa-angles-left"></i> Main Menu
                  </a>
               </div>
            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<!-- Modal :: Statement -->
<div id="stmtModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_stmt" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Transactions</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Transactions of any account or all accounts</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#stmtModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-12">
                        <div class="row">
                           <label class="col-md-12 control-label">Report Date : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="report_date" name="report_date"
                                 class="form-control dp_full_date w-120"
                                 value="<?php echo date('d-m-Y'); ?>"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-6">
                        <div class="row">
                           <label class="col-md-12 control-label">From : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="from" name="from"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="row">
                           <label class="col-md-12 control-label float-right">To : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="to" name="to"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <label for="accn" class="col-md-12 control-label">Account : </label>
                     <div class="col-md-12">
                        <select name="accn" id="accn" class="form-control">
                           <?php echo $coa_list; ?>
                        </select>
                     </div>
                  </div>                  

               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#stmtModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_stmt" style="margin-left: 10px">Print in PDF</button>
                  <button type="button" class="btn btn-danger btn-sm float-right" id="btn_export_exl">Export in Excel</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Trail Balance -->
<div id="tbModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_tb" action="#" method="GET">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Trail Balance</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Financial report showing the closing balances of all accounts in the general ledger</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#tbModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-12">
                        <div class="row">
                           <label class="col-md-12 control-label">Cutoff Date : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="cut_off_date" name="cut_off_date"
                                 class="form-control dp_full_date w-120" 
                                 value="<?php echo date('d-m-Y'); ?>"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                  </div>                 

                  <div class="row">
                     <label for="sort_by" class="col-md-12 control-label">Sort By : </label>
                     <div class="col-md-12">
                        <select name="sort_by" id="sort_by" class="form-control" style="width: 100%" required>
                           <option value="code">Account Code</option>
                           <option value="desc">Account Description</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#tbModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_tb" style="margin-left: 10px">Print in PDF</button>
                  <button type="button" class="btn btn-danger btn-sm float-right" id="btn_export_exl_tb">Export in Excel</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: PL & Balance Sheet -->
<div id="plModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_pl" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">P&L Statement and Balance Sheet</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Financial statement that summarizes the revenues, costs, and expenses incurred during a specified period</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#plModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">                  
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-6">
                        <div class="row">
                           <label for="date-from" class="col-md-12 control-label">From : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="date-from" name="date-from"
                                 value="<?php if ($start_date == null || $start_date == '' || $start_date == '01-01-1970') {
                                     echo '';
                                 } else {
                                     echo $start_date;
                                 } ?>"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="row">
                           <label for="to" class="col-md-12 control-label float-right">To : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="date-to" name="date-to"
                                 value="<?php if ($end_date == null || $end_date == '' || $end_date == '01-01-1970') {
                                     echo '';
                                 } else {
                                     echo $end_date;
                                 } ?>"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <label for="accn" class="col-md-12 control-label">Closing Stock Amount : </label>
                     <div class="col-md-12">
                        <input 
                           type="number" 
                           name="amount" id="amount" 
                           value="<?php if ($closing_stock == null || $end_date == '') {
                               echo '';
                           } else {
                               echo $closing_stock;
                           } ?>"
                           class="form-control" style="width: 250px;" autocomplete="off" required />
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#plModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_pl" style="margin-left: 10px">Print in PDF</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
   // document starts
   $(function() {
      $('select').select2();

      $('#btn_print_stmt').on('click', function() {
         if(!$('#frm_stmt').valid()) {
            return;
         }

         var url = '/general_ledger/print_statement';
         $("#frm_stmt").attr("action", url);
         $("#frm_stmt").attr("target", "_blank");
         $("#frm_stmt").submit();
      });

      $('#stmtModal').on('shown.bs.modal', function () {
      });

      $('#btn_export_exl').on('click', function() {
         if(!$('#frm_stmt').valid()) {
            return;
         }

         var url = '/general_ledger/export_stmt_in_excel';
         $("#frm_stmt").attr("action", url);
         //$("#frm_stmt").attr("target", "_blank");
         $("#frm_stmt").submit();
      });

      $('#btn_print_tb').on('click', function() {
         if(!$('#frm_tb').valid()) {
            return;
         }

         var url = '/general_ledger/print_trail_balance';
         $("#frm_tb").attr("action", url);
         $("#frm_tb").attr("target", "_blank");
         $("#frm_tb").submit();
      });

      $('#tbModal').on('shown.bs.modal', function () {
      });

      $('#btn_export_exl_tb').on('click', function() {
         if(!$('#frm_tb').valid()) {
            return;
         }

         var url = '/general_ledger/export_tb_in_excel';
         $("#frm_tb").attr("action", url);
         $("#frm_tb").attr("target", "_blank");
         $("#frm_tb").submit();
      });      

      $('#btn_print_pl').on('click', function() {
         if(!$('#frm_pl').valid()) {
            return;
         }

         var url = '/pl_balance/print';
         $("#frm_pl").attr("action", url);
         $("#frm_pl").attr("target", "_blank");
         $("#frm_pl").submit();
      });

      $('#plModal').on('shown.bs.modal', function () {
      });

   });
</script>