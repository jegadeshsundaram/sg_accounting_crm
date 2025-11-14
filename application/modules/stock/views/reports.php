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
                  <a href="/stock/" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               
               <div class="card-body">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#stockCardModal" class="btn btn-block" style="color: #fff; background: cadetblue;">
                           Stock Card <span>In & Out of any product in any period</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#stockStatusModal" class="btn btn-block" style="color: #fff; background: cadetblue;">
                           Stock Status <span>Quantity in balance of every product for the cut-off period</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a id="lnk_fifo_valuation" class="btn btn-block" style="color: #fff; background: cadetblue;">
                           FIFO Valuation<span>First In First Out Valuation of each and every stock in any period</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a id="lnk_fifo_support" data-toggle="modal" data-target="#fifoModal" class="btn btn-block" style="color: #fff; background: cadetblue;">
                           FIFO Valuation Supporting Guide<span>First In First Out Valuation Supporting Guide of each and every stock in any period</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#wacModal" class="btn btn-block" style="color: #fff; background: cadetblue;">
                           WAC <span>Weighted Average Cost of each and every stock in any period</span>
                        </a>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <a href="/stock" class="btn btn-warning btn-sm float-right" style="font-size: 1rem;">
                     <i class="fa-solid fa-angles-left"></i> Main Menu
                  </a>
               </div>
            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<!-- Modal :: Stock Card -->
<div id="stockCardModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_st_card" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Stock Card</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>In & Out of any stock item for any period</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#stockCardModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
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

                  <div class="row">
                     <label for="product_id" class="col-md-12 control-label">Creditor : </label>
                     <div class="col-md-12">
                        <select name="product_id" id="product_id" class="form-control" style="width: 100%" required>
                           <?php echo $product_options; ?>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#stockCardModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_card">Print Statement</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Stock Status -->
<div id="stockStatusModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_st_status" action="#" method="GET">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Stock Status</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Quantity in balance of every product for the cut-off period</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#stockStatusModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-md-12">
                        <div class="row">
                           <label for="cut_off_date" class="col-md-4 control-label">Cut-Off : </label>
                           <div class="col-md-8">
                              <input 
                                 type="text"
                                 id="cut_off_date" name="cut_off_date"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#stockStatusModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_status">Print Statement</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: FIFO Stock Valuation -->
<div id="fifoModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <input type="hidden" id="process" />
         <form id="frm_st_fifo" action="#" method="GET">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span id="fifo_header" style="margin: 0; display: block;">FIFO Valuation & Supporting Schedule</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>First In First Out Valuation of each and every stock in any period</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#fifoModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-md-12">
                        <div class="row">
                           <label for="fifo_cutoff" class="col-md-4 control-label">Cut-Off : </label>
                           <div class="col-md-8">
                              <input 
                                 type="text"
                                 id="fifo_cutoff" name="fifo_cutoff"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#fifoModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_fifo">Print</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: WAC Stock Valuation -->
<div id="wacModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_st_wac" action="#" method="GET">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">WAC Stock Valuation</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Weighted Average Cost of each and every stock in any period</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#wacModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-md-12">
                        <div class="row">
                           <label for="wac_cutoff" class="col-md-4 control-label">Cut-Off : </label>
                           <div class="col-md-8">
                              <input 
                                 type="text"
                                 id="wac_cutoff" name="wac_cutoff"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#wacModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_wac">Print</button>
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

      $('#btn_print_card').on('click', function() {
         if(!$('#frm_st_card').valid()) {
            return;
         }

         var url = '/stock/print_stock_card';
         $("#frm_st_card").attr("action", url);
         $("#frm_st_card").attr("target", "_blank");
         $("#frm_st_card").submit();
      });

      $('#btn_print_status').on('click', function() {
         if(!$('#frm_st_status').valid()) {
            return;
         }

         var url = '/stock/print_stock_status';
         $("#frm_st_status").attr("action", url);
         $("#frm_st_status").attr("target", "_blank");
         $("#frm_st_status").submit();
      });

      $('#fifoModal').on('shown.bs.modal', function () {
         $('#fifo_cutoff').val("");
      });

      $('#lnk_fifo_valuation').on('click', function() {
         $('#fifo_header').text("FIFO Valuation");
         $('#process').val("fifo_val");
         $('#fifoModal').modal();
      });

      $('#lnk_fifo_support').on('click', function() {
         $('#fifo_header').text("FIFO Supporting Guide");
         $('#process').val("fifo_support");
         $('#fifoModal').modal();
      });

      $('#btn_print_fifo').on('click', function() {
         if(!$('#frm_st_fifo').valid()) {
            return;
         }

         var url = '/stock/print_fifo';
         if($('#process').val() == "fifo_val") {
            url = '/stock/print_fifo';
         } else if($('#process').val() == "fifo_support") {
            url = '/stock/print_fifo_support';
         }
         
         $("#frm_st_fifo").attr("action", url);
         $("#frm_st_fifo").attr("target", "_blank");
         $("#frm_st_fifo").submit();
      });

      $('#btn_print_wac').on('click', function() {
         if(!$('#frm_st_wac').valid()) {
            return;
         }

         var url = '/stock/print_wac';         
         $("#frm_st_wac").attr("action", url);
         $("#frm_st_wac").attr("target", "_blank");
         $("#frm_st_wac").submit();
      });

   });
</script>