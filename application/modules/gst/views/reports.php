<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">GST</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">GST</li>
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
                  <a href="/gst/" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               
               <div class="card-body">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#detailedModal" class="btn btn-info btn-block">
                           Detailed <span>Detailed Report on each and every gst categories for the selected period will be printed in PDF format.</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#summaryModal" class="btn btn-info btn-block">
                           Summary<span>Summary Report on each and every gst categories for the selected period will be printed in PDF Format.</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#ioModal" class="btn btn-info btn-block">
                           Input & Output Tax<span>Transactions will be categorized as Input and Output and printed in PDF Format</span>
                        </a>
                     </div>

                  </div>
               </div>
               <div class="card-footer">
                  <a href="/gst" class="btn btn-warning btn-sm float-right" style="font-size: 1rem;">
                     <i class="fa-solid fa-angles-left"></i> Main Menu
                  </a>
               </div>
            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<!-- Modal :: Detailed -->
<div id="detailedModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_dtld" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Detailed GST Report</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Transactions of of each and every GST Account will be printed</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#detailedModal"><i class="fa-solid fa-xmark"></i></button>
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
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#detailedModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_dtld" style="margin-left: 10px">Print in PDF</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Summary -->
<div id="summaryModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_sr" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Summary GST Report</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Transactions of of each and every GST Account will be printed</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#summaryModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">                  
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-6">
                        <div class="row">
                           <label for="from" class="col-md-12 control-label">From : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="from_sr" name="from_sr"
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
                                 id="to_sr" name="to_sr"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#summaryModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_sr" style="margin-left: 10px">Print in PDF</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Input & Output Tax Listing -->
<div id="ioModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_io" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Input & Output Tax</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Transactions of of each and every GST Account will be printed</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#ioModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">                  
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-6">
                        <div class="row">
                           <label for="from_io" class="col-md-12 control-label">From : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="from_io" name="from_io"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="row">
                           <label for="to_io" class="col-md-12 control-label float-right">To : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="to_io" name="to_io"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row" style="margin-bottom: 15px">
                     <label for="gst_type" class="col-md-12 control-label">GST Type : </label>
                     <div class="col-md-12">
                        <select name="gst_type" id="gst_type" class="form-control" style="width: 100%" required>
                           <option value="">Select</option>
                           <option value="I">Input Tax</option>
                           <option value="O">Output Tax</option>
                           <option value="B">Both</option>
                        </select>
                     </div>
                  </div>

                  <div class="row input" style="display: none">
                     <label for="input_tax" class="col-md-12 control-label">Input Tax Categories : </label>
                     <div class="col-md-12">
                        <select name="input_tax" id="input_tax" class="form-control" style="width: 100%">
                           <?php echo $input_categories; ?>
                        </select>
                     </div>
                  </div>

                  <div class="row output" style="display: none">
                     <label for="output_tax" class="col-md-12 control-label">Output Tax Categories : </label>
                     <div class="col-md-12">
                        <select name="output_tax" id="output_tax" class="form-control" style="width: 100%">
                           <?php echo $output_categories; ?>
                        </select>
                     </div>
                  </div>

               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#ioModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_io" style="margin-left: 10px">Print in PDF</button>
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

      $('#btn_print_dtld').on('click', function() {
         if(!$('#frm_dtld').valid()) {
            return;
         }

         var url = '/gst/print_detailed';
         $("#frm_dtld").attr("action", url);
         $("#frm_dtld").attr("target", "_blank");
         $("#frm_dtld").submit();
      });

      $('#detailedModal').on('shown.bs.modal', function () {
      });

      $('#btn_print_sr').on('click', function() {
         if(!$('#frm_sr').valid()) {
            return;
         }

         var url = '/gst/print_summary';
         $("#frm_sr").attr("action", url);
         $("#frm_sr").attr("target", "_blank");
         $("#frm_sr").submit();
      });

      $('#summaryModal').on('shown.bs.modal', function () {
      });

      $(document).on('change', '#from_io', function() {
         if($(this).val() !== "") {
            $(this).removeClass('error');
            $('#from_io-error').hide();
         }
      });

      $(document).on('change', '#to_io', function() {
         if($(this).val() !== "") {
            $(this).removeClass('error');
            $('#to_io-error').hide();
         }
      });

      $(document).on('change', '#gst_type', function() {
         $("#input_tax").val('').trigger('change')
         $("#output_tax").val('').trigger('change')
         var gst_type = $('option:selected', this).val();

         if(gst_type == "I") {
            $('#type-error').hide();
            $('.output').hide();
            $('.input').show();
            
         } else if(gst_type == "O") {
            $('#type-error').hide();
            $('.input').hide();
            $('.output').show();

         } else {
            $('.input').hide();
            $('.output').hide();
         }
      });

      $('#btn_print_io').on('click', function() {
         if(!$('#frm_io').valid()) {
            return;
         }

         var url = '/gst/print_io_tax';
         $("#frm_io").attr("action", url);
         $("#frm_io").attr("target", "_blank");
         $("#frm_io").submit();
      });

      $('#ioModal').on('shown.bs.modal', function () {
      });

   }); // document ends
</script>