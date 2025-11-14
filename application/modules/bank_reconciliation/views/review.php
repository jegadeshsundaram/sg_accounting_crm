
<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Older Recon's</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Bank Reconciliation</li>
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
                  <h5>Review</h5>
                  <a href="/bank_reconciliation" class="btn btn-outline-dark btn-sm float-right">
                    <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               
               <div class="card-body table-responsive">
                  <div class="row">
                     <div class="col-lg-12">
                        
                        <div id="bank_details"></div>

                        <table id="tbl_" class="table table-hover" style="min-width: 1400px; width: 100%;">
                           <thead>
                              <tr>
                                 <th></th>
                                 <th>Date</th>
                                 <th>Reference</th>
                                 <th>Remarks</th>
                                 <th>Amount</th>
                                 <th>Accounted</th>
                              </tr>
                           </thead>
                           <tbody></tbody>
                        </table>
                     </div>
                  </div>
               </div>

               <div class="card-footer">
                  <a href="/bank_reconciliation" class="btn btn-info btn-sm">Cancel</a>
                  <button type="button" id="btn_submit" class="btn btn-warning btn-sm float-right" style="display: none">CONFIRM</button>
               </div>
            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<form id="frm_" name="frm_" method="post" action="#">
   <input type="hidden" name="br_ids" id="br_ids" />
</form>

<style>
   .check-container {
      display: inline;
      position: relative;
      cursor: pointer;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
   }
</style>

<script>

   // document starts
   $(function() {

      $.post('/bank_reconciliation/ajax/get_previous_month_items', {
         recon_id: <?php echo $recon_id; ?>
      }, function(data) {
         var obj = $.parseJSON(data);
         $('#bank_details').html(obj.bank_details);
         $("#tbl_ tbody").html(obj.items);         
         if(obj.entries > 0) {
            $('#btn_submit').show();
         }
      });


      $(document).on("click", "#btn_submit", function() {

         var checked = 0;
         $('.accounted_check').each(function() {
            if($(this).prop("checked") == true) {
               checked += 1;
            }
         });

         if(checked > 0) {
            $('#confirmSubmitModal .modal-title').html("Confirm Account");
            $('#confirmSubmitModal .modal-body').html("Click 'Yes' to make the selected items accounted");
            $("#confirmSubmitModal").modal();
         } else {
            $('#errorAlertModal .modal-title').html("No items marked!");
            $('#errorAlertModal .modal-body').html("Please mark atleast one item to continue...");
            $('#errorAlertModal').modal();
         }

      });

      $('#btn-confirm-yes').click(function() {
         $("#confirmSubmitModal").modal('hide');

         var br_id = [];
         $('.accounted_check').each(function () {
            if($(this).prop("checked") == true) {
               br_id.push($(this).closest('tr').find('.br_id').html());
            }
         });

         var url = '/bank_reconciliation/account_items';
         $('#br_ids').val(br_id);
         $("#frm_").attr("action", url);
         $("#frm_").submit();

      });

  });

</script>
