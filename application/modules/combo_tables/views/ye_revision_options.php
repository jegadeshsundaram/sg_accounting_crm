<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Year End Revision</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">YE Revision</li>
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

            <div class="card card-default">
               <div class="card-body">
                  <div class="row">
                     <div class="col-lg-4">
                        <div style="margin-bottom: 20px">
                           <a href="/combo_tables/ye_revision" class="btn btn-info btn-block">Enter Year End Exchange Rates</a>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div style="margin-bottom: 20px">
                           <button type="button" id="btn_apply_ye_revision" class="btn btn-info btn-block">Apply Year End Exchange Rates</button>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <a href="/dashboard" class="btn btn-warning btn-sm float-right">
                     <i class="fa-solid fa-angles-left"></i> Dashboard
                  </a>
               </div>
            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

   <form id="form_" method="post">
      <input type="hidden" name="update_ye_revision" value="true" />
   </form>

   <!-- YE Revision Modal - starts -->
   <div id="applyYERevisionModal" class="modal fade" data-backdrop="static">
      <div class="modal-dialog modal-confirm">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title"><i class="fa fa-info"></i> Year End Revision</h4>
            </div>
            <div class="modal-body">
               <p>
                  <span style="color: dimgray; font-style: italic">System will apply Year End Exchange Rates to the Transactions in the following Sub Ledgers : </span><br /><br />
                  <strong>Accounts Receivable</strong> <br />
                  <strong>Accounts Payable</strong><br />
                  <strong>Foreign Bank</strong><br />
               </p>
            </div>
            <div class="modal-footer justify-content-between">
               <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal" data-target="#applyYERevisionModal">Cancel</button>
               <button type="button" class="btn btn-danger btn-sm" id="btn_apply_ye_revision_proceed">Proceed</button>
            </div>
         </div>
      </div>
   </div>
   <!-- YE Revision Modal - ends -->

   <!-- YE Revision Currency Modal - starts -->
   <div id="currencyAlertModal" class="modal fade" data-backdrop="static">
      <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
               <h4 class="modal-title"><i class="fa fa-info"></i> Year End Revision</h4>
            </div>
            <div class="modal-body">
               System can not find required currencies in Year End Table.
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-danger btn-sm" id="btn_currency_alert_yes">Ok</button>
            </div>
         </div>
      </div>
   </div>
   <!-- YE Revision Currency Modal - ends -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script type="text/javascript">
   $(function() {
        
      $('#btn_apply_ye_revision').on("click", function() {
         $.post('/combo_tables/Ajax/YE_revision/currency_exist_check', {
         }, function(data) {
            if (data == 1) {
               $('#applyYERevisionModal').modal();
            } else {
               $('#currencyAlertModal').modal();
            }
         });
      });

      $('#btn_currency_alert_yes').on("click", function() {
         $('#currencyAlertModal').modal('hide');
         window.location = "/combo_tables/ye_revision";
      });

      $('#btn_apply_ye_revision_proceed').on("click", function() {
         $('#applyYERevisionModal').modal('hide');
         var url = '/combo_tables/Ajax/YE_revision/update_ye_revision';
         $("#form_").attr("action", url);
         $("#form_").submit();
      });

   }); // document ends
  
 </script>