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
            <input type="hidden" id="module" value="general_ledger" />
            <div class="card card-default">
               <div class="card-header options">
                  <h5>Options</h5>
               </div>
               <div class="card-body opt-lnk">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#auditModal">
                           Audit Listing <span>Single and Double entry transactions from GL will be printed</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/general_ledger/opening_balance">
                           Opening Balance b/f <span>Manage opening balance transactions</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/general_ledger/reports">
                           Reports <span>Reports by Account and All, Traial Balance, PL and Balance Sheet</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/general_ledger/chart_of_account">
                           Chart of Account <span>A financial organizational tool that provides a complete listing, by category, of every account in the general ledger of a company.</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/general_ledger/tips_predefined_accounts" target="_blank">
                           Tips on Predefined Accounts <span>Tips and Tricks on using predefined accounts</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#dataPatchModal">
                           Data Patch <span>Patching Transactions which posted to GL from different source</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/general_ledger/ye_closing">
                           Year End Closing <span>Process Year End Closing</span>
                        </a>
                     </div>                     

                     <div class="col-xl-4 col-md-6">                     
                        <a data-toggle="modal" data-target="#utilityModal">
                           Utilities > Datafiles<span>Backup / Restore / Zap of GL Datafile's</span>
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
   <div class="modal-dialog modal-confirm">
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
                     <div class="col-md-12">
                        <label for="entry_type" class="control-label mb-10">Entry Type</label><br>
                        <input type="radio" id="entry_double" name="entry_type" value="D" class="radio-inp" autocomplete="off" checked="checked">
                        <label class="radio-lbl" for="entry_double">DOUBLE - Transactions with double entries</label> <br />
                        <input type="radio" id="entry_single" name="entry_type" value="S" class="radio-inp" autocomplete="off">
                        <label class="radio-lbl no_rd" for="entry_single">SINGLE - Exact input of entries</label>
                     </div>
                  </div>

                  <div class="row" style="margin-bottom: 15px">
                     <label for="transaction" class="col-md-12 control-label">Transaction : </label>
                     <div class="col-md-12">
                        <select name="transaction" id="transaction" class="form-control">
                           <option value="">-- Select --</option>
                           <option value="BTHPURC" class="se">Batch Purchases</option>
                           <option value="BTHREC" class="se">Batch Receipt</option>
                           <option value="BTHSALE" class="se">Batch Sales</option>
                           <option value="BTHSET" class="se">Batch Settlement</option>
                           <option value="INVOICE">Invoice</option>
                           <option value="OPBAL">Opening Balance</option>
                           <option value="EZADJ">Other Adjustment</option>
                           <option value="EZPAY">Other Payment</option>
                           <option value="PAYMENT">Payment</option>
                           <option value="RECEIPT">Receipt</option>
                        </select>
                        <input type="hidden" name="transaction_desc" id="transaction_desc" />
                     </div>
                  </div>
                  
                  <div class="row dv_ref_no" style="display: none; margin-bottom: 15px">
                     <label for="ref_no" class="col-md-12 control-label">Document Reference : </label>
                     <div class="col-md-12">
                        <select name="ref_no" id="ref_no" class="form-control">
                        </select>
                     </div>
                  </div>

                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-md-12">
                        <label for="order" class="control-label">Order By</label><br>
                        <input type="radio" id="order_asc" name="order" value="ASC" class="radio-inp" autocomplete="off" checked="checked">
                        <label class="radio-lbl" for="order_asc">ASC</label>
                        <input type="radio" id="order_desc" name="order" value="DESC" class="radio-inp" autocomplete="off">
                        <label class="radio-lbl no_rd" for="order_desc">DESC</label>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#auditModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_audit" style="margin-left: 10px">Print in PDF</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Statement -->
<div id="dataPatchModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_dp" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">GL Datapatch</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>GL Transactions values can be patched.</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#dataPatchModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">

                  <div class="row form-group">
                     <label for="ref" class="col-md-12 control-label">Transaction : </label>
                     <div class="col-md-12">
                        <select name="tran_type" id="tran_type" class="form-control" required>
                           <option value="">-- Select --</option>
                           <option value="BTHPURC">Credit Purchase</option>
                           <option value="BTHREC">AR Receipt</option>
                           <option value="BTHSALE">Credit Sales</option>
                           <option value="BTHSET">AP Settlement</option>
                           <!--
                           This option is made hidden becuase of complication, This is done by Jega without anyone approval 
                           <option value="EZADJ">Other Adjustment</option>--> 
                           <option value="EZPAY">Other Payment</option>
                           <option value="INVOICE">Sales Invoice</option>
                           <option value="OPBAL">Opening Balance</option>
                           <option value="PAYMENT">Payment</option>
                           <option value="PTCASH">Petty Cash</option>
                           <option value="RECEIPT">Receipt</option>
                        </select>
                     </div>
                  </div>

                  <div class="row form-group ref_field" style="display: none">
                     <label for="ref" class="col-md-12 control-label">Document Reference : </label>
                     <div class="col-md-12">
                        <select name="ref" id="ref" class="form-control" required>
                           <?php echo $ref_list; ?>
                        </select>
                     </div>
                  </div>

                  <div class="row form-group supplier_field" style="display: none">
                     <label for="supplier" class="col-md-12 control-label">Supplier : </label>
                     <div class="col-md-12">
                        <select name="supplier" id="supplier" class="form-control" required>
                           <?php echo $supplier_list; ?>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#dataPatchModal">Cancel</button>
                  <button type="button" class="btn btn-danger btn-sm float-right" id="btn_dp_proceed">Proceed</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<?php require_once APPPATH.'/modules/includes/modal/utility.php'; ?>
<script src="/assets/js/modal/utility.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
   // document starts
   $(function() {      

      $('select').select2();

      $('#auditModal').on('shown.bs.modal', function () {
         $('#transaction').select2("destroy").val('').select2();
         $('#ref_no').select2("destroy").val('').select2();
         $('.dv_ref_no').hide();
         $('#order_desc').prop("checked", false);
         $('#order_asc').prop("checked", true);
         $('#entry_single').prop("checked", false);
         $('#entry_double').prop("checked", true);

         $('#transaction option').prop('disabled', false);
      });

      $('input[type=radio][name=entry_type]').change(function() {
         $('#transaction').select2("destroy").val("").select2();
         $('#ref_no').select2("destroy").val("").select2();
         $('.dv_ref_no').hide();

         if (this.value == 'S') {
            $('#transaction option').prop('disabled', true);
            $('#transaction option[value=""]').prop('disabled', false);
            $('#transaction option[class="se"]').prop('disabled', false);
         } else {
            $('#transaction option').prop('disabled', false);
         }
      });

      $(document).on('change', '#transaction', function() {
         var type = $('option:selected', this).val();

         $('#ref_no').find('option').remove();
         $('.dv_ref_no').hide();

         if(type !== "") {
            $('#transaction_desc').val($('option:selected', this).text());

            $.post('/general_ledger/ajax/get_refs', {
               entry_type: $("input[name='entry_type']:checked").val(),
               transaction_type: type
            }, function (data) {
               var obj = $.parseJSON(data);
               if(obj.entries > 0) {
                  $('#ref_no').append(obj.options);
                  $('.dv_ref_no').show();
               }
            });
         }
      });

      $('#btn_print_audit').on('click', function() {
         if($('#transaction').val() == "") {
            $("#transaction").select2('open');
         } else  {

            var url = '/general_ledger/print_audit_double';
            if($("input[name='entry_type']:checked").val() == "S") {
               url = '/general_ledger/print_audit_single';
            }            
            $("#frm_").attr("action", url);
            $("#frm_").attr("target", "_blank");
            $("#frm_").submit();
         }
      });

      // data patch - starts
      $('#dataPatchModal').on('hidden.bs.modal', function () {
         $('#tran_type').select2("destroy").val('').select2();
         $(".ref_field").hide();
         $(".supplier_field").hide();
      });

      $(document).on('change', '#tran_type', function() {
         var tran = $('option:selected', this).val();

         $(".ref_field").hide();

         $("#ref").val("");
         $("#ref").val(null).trigger("change");
         $('#ref option').prop('disabled', false);

         if(tran !== "") {
            $(".ref_field").show();
            $('#ref option').prop('disabled', true);
            $('#ref option[value=""]').prop('disabled', false);
            $('#ref option[class="'+tran+'"]').prop('disabled', false);
            $('#ref').select2();
         }
      });

      $(document).on('change', '#ref', function() {
         var ref = $('option:selected', this).text();
         var tran_type = $('option:selected', this).attr('class');

         $(".supplier_field").hide();

         $("#supplier").val("");
         $("#supplier").val(null).trigger("change");
         $('#supplier option').prop('disabled', false);
         
         if(tran_type == "BTHPURC" || tran_type == "BTHSET") {
            $.post('/general_ledger/ajax/same_ref_transactions', {
               ref_no: ref,
               tran_type: tran_type
            }, function (refs) {
               if(refs > 1) {
                  $(".supplier_field").show();
                  $('#supplier option').prop('disabled', true);
                  $('#supplier option[value=""]').prop('disabled', false);
                  $('#supplier option[class="'+ref+'"]').prop('disabled', false);
                  $('#supplier').select2();
               }
            });
         }         
      });

      $("#btn_dp_proceed").click(function() {
         if(!$('#frm_dp').valid()) {
            return;
         }

         var save_url = "/general_ledger/data_patch";
         $("#frm_dp").attr("action", save_url);
         $("#frm_dp").submit();
      });      

   }); // document ends
</script>