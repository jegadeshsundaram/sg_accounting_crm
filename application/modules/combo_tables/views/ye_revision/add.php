<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Year End Revision</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item"><a href="/combo_tables/ye_revision">YE Revision</a></li>
               <li class="breadcrumb-item active">New</li>
            </ol>
         </div>
      </div>
   </div>
</div>

<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <form autocomplete="off" id="form_" method="post" action="#">
               <div class="card card-default">
                  <div class="card-body">
                     <div class="row">
                        <div class="col-lg-12">
                           <!-- Field: Cutoff Date -->
                           <div class="form-group row">
                              <label for="cutoff_date" class="col-md-4 control-label">Cutoff Date : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    name="cutoff_date" id="cutoff_date" 
                                    value="<?php echo date('d-m-Y', strtotime('Dec 31')); ?>"
                                    class="form-control w-150 req dp_full_date" required />
                              </div>
                           </div>

                           <!-- Field: Description -->
                           <div class="form-group row">
                              <label for="currency" class="col-md-4 control-label">Currency : </label>
                              <div class="col-md-8">
                                 <select name="currency" id="currency" class="form-control req" required>
                                    <?php echo $currency_list; ?>
                                 </select>
                              </div>
                           </div>

                           <!-- Field: Currency Rate -->
                           <div class="form-group row">
                              <label for="rate" class="col-md-4 control-label">Exchange Rate : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="number" 
                                    name="rate" id="rate" 
                                    class="form-control req" onKeyPress="if(this.value.length==12) return false;" required />
                              </div>
                           </div>

                        </div>
                     </div>
                  </div>
                  <div class="card-footer">
                     <a href="/combo_tables/ye_revision" class="btn btn-info">Back</a>                  
                     <button type="button" id="btn_submit" class="btn btn-warning float-right">Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script type="text/javascript">
   var same_code_exists = 0;
   $(function() {
      $('select').select2();      

      $('#currency_rate').focusout(function() {
         var rate = $('#currency_rate').val();
         $('#currency_rate').val(parseFloat(rate).toFixed(5));
      });

       $('#btn_submit').click(function() {
         if($('#form_').valid()) {
            $('#confirmSubmitModal .modal-title').html("<i class='fa fa-info'></i> CONFIRM");
            $('#confirmSubmitModal .modal-body').html("Click 'Submit' to save this currency...");
            $("#confirmSubmitModal").modal();
         }
      });

      $('#btn-confirm-yes').click(function() {
         $("#confirmSubmitModal").modal('hide');

         var url = "/combo_tables/Ajax/YE_revision/save";
         $('#form_').attr("action", url);
         $('#form_').submit();
      });

   }); // document ends
</script>
