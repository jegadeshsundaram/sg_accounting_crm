<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Stock</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Stock</li>
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
            <input type="hidden" id="module" value="stock" />
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
                        <a href="/stock/opening_balance">
                           Opening Stock b/f <span>Manage opening balance transactions</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/stock/purchase">
                           Goods Received <span>Details about purchasing of Stock items</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/stock/adjustment">
                           Adjustment <span>Adjustment in the records so that they agree with the physical count</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/stock/reports">
                           Reports <span>Reports of Status, Card, FIFO and WAC of Stock items</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#dataPatchModal">
                           Datapatch <span>Update in already posted transactions into stock ledger</span>
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
                           <option value="Adjustment">Adjustment</option>
                           <option value="Invoice">Invoice</option>
                           <option value="OPBAL">Opening Balance</option>
                           <option value="Purchase">Purchase</option>
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

<!-- Modal :: Data Patch -->
<div id="dataPatchModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_dp" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Stock Datapatch</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Stock opening balance transactions data can be patched.</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#dataPatchModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row">
                     <label for="ref_no" class="col-md-12 control-label">Transaction Details : </label>
                     <div class="col-md-12">
                        <select name="ref_no" id="ref_no" class="form-control">
                           <?php echo $refs; ?>
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
            var url = '/stock/print_audit';
            $("#frm_").attr("action", url);
            $("#frm_").attr("target", "_blank");
            $("#frm_").submit();
         }
      });

      $('#dataPatchModal').on('hidden.bs.modal', function() {
         $('#ref_no').select2("destroy").val('').select2();
      });

      $("#btn_data_patch").click(function() {
         if($('#ref_no').val() == '') {
            $("#ref_no").select2('open');
            return false;
         }

         $("#frm_dp").attr("action", '/stock/data_patch');
         $("#frm_dp").attr("target", "_blank");
         $("#frm_dp").submit();
      });

   });
</script>