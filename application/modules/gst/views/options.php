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
            <input type="hidden" id="module" value="gst" />
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
                        <a href="/gst/opening_balance">
                           Opening Balance B/F <span>opening balance bf of gst transactions</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/gst/reports">
                           Reports <span>all the gst reports can be printed in the pdf format</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/gst/iras_api">
                           IRAS API <span>generating gst returns json file to submit to IRAS</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#revenueSettingModal">
                           Revenue Setting <span>Settings of Revenue amount to be calculated from GST or GL</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#dataPatchModal">
                           Data Patch <span>Manage opening balance transactions</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#utilityModal">
                           Utilities > Datafiles<span>Backup / Restore / Zap of GST Datafile's</span>
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
                     <label for="transaction" class="col-md-12 control-label">Transaction : </label>
                     <div class="col-md-12">
                        <select name="transaction" id="transaction" class="form-control">
                           <option value="">-- Select --</option>
                           <option value="BTHPURC">Batch Purchases</option>
                           <option value="BTHSALE">Batch Sales</option>
                           <option value="INVOICE">Invoice</option>
                           <option value="OPBAL">Opening Balance</option>
                        </select>
                        <input type="hidden" name="transaction_desc" id="transaction_desc" />
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

<!-- Modal :: Revenue Setting -->
<div id="revenueSettingModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Revenue Setting</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Setting of Revenue amount to be calculated from GST or GL</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#revenueSettingModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-12" style="color: red; font-style: italic; letter-spacing: 1px; font-size: 1.2rem;">
                        GST Revenue from GL?
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-info btn-sm" id="btn_rev_gst">NO</button>
                  <button type="button" class="btn btn-primary btn-sm float-right" id="btn_rev_gl">YES</button>
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
                  <span style="margin: 0; display: block;">GST Datapatch</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>GST Transactions values can be patched.</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#dataPatchModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">                  
                  <div class="row">
                     <label for="ref" class="col-md-12 control-label">Document Reference : </label>
                     <div class="col-md-12">
                        <select name="ref" id="ref" class="form-control" style="width: 100%" required>
                           <?php echo $ref_list; ?>
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

   var getUrlParameter = function getUrlParameter(sParam) {
      var sPageURL = window.location.search.substring(1),
         sURLVariables = sPageURL.split('&'),
         sParameterName,
         i;

      for (i = 0; i < sURLVariables.length; i++) {
         sParameterName = sURLVariables[i].split('=');

         if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
         }
      }
      return false;
   };

   var supplier = false;

   // document starts
   $(function() {

      if(getUrlParameter('page') == "dp") {
         $("#dataPatchModal").modal();
      }

      if(getUrlParameter('page') == "rs") {
         $("#revenueSettingModal").modal();
      }

      $('select').select2();

      $('#auditModal').on('shown.bs.modal', function () {
         $('#transaction').select2("destroy").val('').select2();
         $('#order_desc').prop("checked", false);
         $('#order_asc').prop("checked", true);
      });

      $(document).on('change', '#transaction', function() {
         var type = $('option:selected', this).val();

         if(type !== "") {
            $('#transaction_desc').val($('option:selected', this).text());
         }
      });

      $('#btn_print_audit').on('click', function() {
         if($('#transaction').val() == "") {
            $("#transaction").select2('open');
         } else  {
            $("#frm_").attr("action", '/gst/print_audit');
            $("#frm_").attr("target", "_blank");
            $("#frm_").submit();
         }
      });

      $("#btn_dp_proceed").click(function() {
         if(!$('#frm_dp').valid()) {
            return;
         }

         window.location.href = "/gst/data_patch/"+ $('#ref').val();
      });

      $("#btn_rev_gst").click(function() {
         set_revenue("gst");
      });

      $("#btn_rev_gl").click(function() {
         set_revenue("gl");
      });

   }); // document ends

   function set_revenue(tbl) {
      $.post('/gst/ajax/set_revenue', {
         tbl: tbl
      }, function(status) {
         if (status == "error") {
            toastr.error('Error Saving in Revenue Settings');
         } else {
            toastr.success('Revenue Settings Saved');
         }

         $("#revenueSettingModal").modal('hide');
      });
   }
</script>