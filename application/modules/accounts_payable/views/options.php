<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Accounts Payable</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">AP</li>
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
            <input type="hidden" id="module" value="accounts_payable" />
            <div class="card card-default">
               <div class="card-header options">
                  <h5>Options</h5>
               </div>
               <div class="card-body opt-lnk">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#auditModal">
                           Audit Listing <span>Audit of all the transactions</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/accounts_payable/batch_ob_listing">
                           Opening Balance <span>Manage opening balance transactions</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/accounts_payable/reports">
                           Reports <span>Report of Creditors statement / Listing / Ageing</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#offsetModal">
                           Offset <span>The settled transactions which dated on or before the cut-off date will be removed.</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#dataPatchModal">
                           Data Patch <span>Patch transaction's data posted into accounts</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#utilityModal">
                           Utilities > Datafiles<span>Backup / Restore / Zap of Receipt Datafile's</span>
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
         <form id="frm_audit" action="#" method="POST">
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
                           <option value="OPBAL">Opening Balance</option>
                           <option value="PAYMENT">Payment</option>
                           <option value="BTHPURC">Batch Purchase</option>
                           <option value="BTHSET">Batch Settlement</option>
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

<!-- Modal :: Offset -->
<div id="offsetModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm modal-sm">
      <div class="modal-content">
         <form id="frm_cutoff" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Offset</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>The settled transactions which dated on or before the cut-off date will be removed.</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#offsetModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
                     <label for="cutoff_date" class="col-4 control-label">Cut-Off : </label>
                     <div class="col-8">
                        <input 
                           type="text" name="cutoff_date" id="cutoff_date" 
                           class="form-control dp_full_date w-120"
                           value="<?php echo date('d-m-Y'); ?>" />
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#offsetModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_offset" style="margin-left: 10px">Submit</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Data Patch -->
<div id="dataPatchModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_dp" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">AP Datapatch</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>AR opening balance transactions data can be patched.</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#dataPatchModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row">
                     <label for="customer" class="col-md-12 control-label">Customer : </label>
                     <div class="col-md-12">
                        <select name="supplier" id="supplier" class="form-control">
                           <?php echo $suppliers; ?>
                        </select>
                     </div>
                  </div>
                  <div class="row mt-10 dv_ref_no" style="display: none">
                     <label for="ref_no" class="col-md-12 control-label">Document Reference : </label>
                     <div class="col-md-12">
                        <select name="ref_no" id="ref_no" class="form-control">
                        </select>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#dataPatchModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_data_patch">Proceed</button>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
   // document starts
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
            var url = '/accounts_payable/print_audit';
            $("#frm_audit").attr("action", url);
            $("#frm_audit").attr("target", "_blank");
            $("#frm_audit").submit();
         }
      });

      $('#btn_offset').on('click', function() {
         if($('#cutoff_date').val() == "") {
            $("#cutoff_date").focus();
         } else {
            $("#offsetModal").modal('hide');

            $.confirm({
               title: '<i class="fa fa-info"></i> Confirm Offset?',
               content: 'On Confirmation, all the settled transactions will be offsetted!',
               buttons: {
                  yes: {
                     btnClass: 'btn-warning',
                     action: function() {
                        var url = '/accounts_payable/offset';
                        $("#frm_cutoff").attr("action", url);
                        $("#frm_cutoff").submit();
                     }
                  },
                  no: {
                     btnClass: 'btn-dark',
                     action: function(){
                        $("#offsetModal").modal();
                     }
                  },
               }
            });
         }
      });

      $(document).on('change', '#supplier', function() {
         var code = $('option:selected', this).val();
         
         $('#ref_no').find('option').remove();
         $('.dv_ref_no').hide();

         if(code !== "") {
            $.post('/accounts_payable/ajax/get_refs', {
               supplier_code: code
            }, function (data) {
               var obj = $.parseJSON(data);
               if(obj.entries > 1) {
                  $('#ref_no').append(obj.options);
                  $('.dv_ref_no').show();
               }
            });
         }
      });

      $("#btn_data_patch").click(function() {
         if($('#supplier').val() == '' && $('ref_no').val() == '') {
            return false;
         }

         $("#frm_dp").attr("action", '/accounts_payable/data_patch');
         $("#frm_dp").attr("target", "_blank");
         $("#frm_dp").submit();
      });

   });
</script>
