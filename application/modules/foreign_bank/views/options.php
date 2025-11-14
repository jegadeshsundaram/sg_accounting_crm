<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Foreign Bank</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Foreign Bank</li>
               <li class="breadcrumb-item active">Options</li>
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
            <input type="hidden" id="module" value="foreign_bank" />
            <div class="card card-default">
               <div class="card-header options">
                  <h5>Options</h5>
               </div>
               <div class="card-body opt-lnk">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#auditModal">
                           Audit Listing <span>Audit of Foreign Bank Transactions</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/foreign_bank/opening_balance">
                           Opening Balance <span>Opening Balance b/f for Foreign Bank Accounts</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#bankStmtModal">
                           Bank Statement by Account <span>Statements of Banks will be printed in PDF, by Account</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#bankLstngModal">
                           Bank Statement by Currency <span>Statements of Banks will be printed in PDF Format, by Currency</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#utilityModal">
                           Utilities > Datafiles<span>Backup / Restore / Zap of Foreign Bank Datafile's</span>
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


<!-- Modal :: Audit -->
<div id="auditModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm modal-sm">
      <div class="modal-content">
         <form id="frm_" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Audit Listing</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Audit of each and every transaction per transaction type.</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#auditModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
                     <label for="transaction" class="col-md-12 control-label">Transaction : </label>
                     <div class="col-md-12">
                        <select name="transaction" id="transaction" class="form-control">
                           <option value="">-- Select --</option>
                           <option value="FBOpen">Opening Balance</option>
                        </select>
                        <input type="hidden" name="transaction_desc" id="transaction_desc" />
                     </div>
                  </div>
                  <div class="row" style="margin-bottom: 15px">
                     <label for="order" class="col-md-12 control-label">Order By : </label>
                     <div class="col-md-12">
                        <select name="order" id="order" class="form-control">
                           <option value="ASC">Ascending</option>
                           <option value="DESC">Descending</option>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#auditModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print" style="margin-left: 10px">Print in PDF</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Bank Statement -->
<div id="bankStmtModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_stmt" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Bank Statement (Account)</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Statements of Banks will be printed in PDF Format, by Account</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#bankStmtModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row">
                     <label for="bank" class="col-md-12 control-label">Bank Account : </label>
                     <div class="col-md-12">
                        <select name="bank" id="bank" class="form-control" style="width: 100%" required>
                           <?php echo $bank_options; ?>
                        </select>
                     </div>
                  </div>                  
                  <div class="row" style="margin: 15px 0 15px">
                     <div class="col-6">
                        <div class="row">
                           <label for="from" class="col-md-12 control-label">From : </label>
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
                           <label for="to" class="col-md-12 control-label float-right">To : </label>
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
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#bankStmtModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_stmt">Print Statement</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Bank Listing -->
<div id="bankLstngModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_lstng" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Bank Statement (Currency)</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Statements of Banks will be printed in PDF Format, by Currency</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#bankLstngModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row">
                     <label for="currency" class="col-md-12 control-label">Currency : </label>
                     <div class="col-md-12">
                        <select name="currency" id="currency" class="form-control" style="width: 100%" required>
                           <?php echo $currency_options; ?>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#bankLstngModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_lstng">Print Listing</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<?php require_once APPPATH.'/modules/includes/modal/utility.php'; ?>
<script src="/assets/js/modal/utility.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
   $(function() {

      $('select').select2();

      $('#auditModal').on('shown.bs.modal', function () {
         $('#transaction').select2("destroy");
         $('#transaction').val('');
         $('#transaction').select2();
      });

      $(document).on('change', '#transaction', function() {
         var type = $('option:selected', this).val();
         if(type !== "") {
            $('#transaction_desc').val($('option:selected', this).text());
         }
      });

      $('#btn_print').on('click', function() {
         if($('#transaction').val() == "") {
            $("#transaction").select2('open');
         } else  {
            var url = '/foreign_bank/print_audit';
            $("#frm_").attr("action", url);
            $("#frm_").attr("target", "_blank");
            $("#frm_").submit();
         }
      });

      $('#bankStmtModal').on('shown.bs.modal', function () {
         $("#bank").val("").trigger("change");
         $('#from').val("");
         $('#to').val("");
      });

      $('#bankLstngModal').on('shown.bs.modal', function () {
         $("#currency").val("").trigger("change");
      });

      $('#btn_print_stmt').on('click', function() {
         if(!$('#frm_stmt').valid()) {
            return;
         }

         var url = '/foreign_bank/print_bank_statement';
         $("#frm_stmt").attr("action", url);
         $("#frm_stmt").attr("target", "_blank");
         $("#frm_stmt").submit();
      });

      $('#btn_print_lstng').on('click', function() {
         if(!$('#frm_lstng').valid()) {
            return;
         }

         var url = '/foreign_bank/print_bank_listing';
         $("#frm_lstng").attr("action", url);
         $("#frm_lstng").attr("target", "_blank");
         $("#frm_lstng").submit();
      });

   }); // document ends
</script>