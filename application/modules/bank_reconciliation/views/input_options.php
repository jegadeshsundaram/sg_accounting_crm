<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Current Month Difference's</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Bank Reconciliation</li>
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
               <div class="card-header">
                  <h5>Input Options</h5>
                  <a href="/bank_reconciliation" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a href="/bank_reconciliation/input/eo" class="btn btn-info btn-block">
                           Error's & Omission's <span>Input of error and omission items</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/bank_reconciliation/input/td" class="btn btn-info btn-block">
                           Timing Differences <span>Input of timing difference items</span>
                        </a>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <a href="/bank_reconciliation" class="btn btn-warning btn-sm float-right" style="font-size: 1rem;">
                     <i class="fa-solid fa-angles-left"></i> Main Menu
                  </a>
               </div>
            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<!-- Modal :: Cashbook -->
<div id="cashBookModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_cb" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Cashbook</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Cash Book will be printed in the PDF Format to do Reconciliation</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#cashBookModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
                     <label for="bank" class="col-md-12 control-label">Bank : </label>
                     <div class="col-md-12">
                        <select name="bank" id="bank" class="form-control" style="width: 100%" required>
                           <?php echo $bank_options; ?>
                        </select>
                     </div>
                  </div>
                  <div class="row fbank" style="display: none; margin-bottom: 15px">
                     <label for="fbank" class="col-md-12 control-label">Foreign Bank : </label>
                     <div class="col-md-12">
                        <select name="fbank" id="fbank" class="form-control" style="width: 100%" required>
                           <?php echo $fbank_options; ?>
                        </select>
                     </div>
                  </div>
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-6">
                        <div class="row">
                           <label for="start_date" class="col-md-12 control-label">From : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="start_date" name="start_date"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="row">
                           <label for="end_date" class="col-md-12 control-label float-right">To : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="end_date" name="end_date"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                  </div>                  
               </div>
               <div class="card-footer">
                  <input type="hidden" id="recon_status" name="recon_status" />
                  <input type="hidden" id="default_month" name="default_month" />
                  <input type="hidden" id="default_year" name="default_year" />

                  <input type="hidden" id="default_period" name="default_period" value="" />

                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#cashBookModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print">Print</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<div id="defaultPeriodModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="modal-header">
            <h3 class="modal-title" style="text-align: center">CONFIRM</h3>
         </div>
         <div class="modal-body">
            DEFAULT PERIOD TO DO BANK RECON <span id="default_month_name" style="text-transform: uppercase; font-weight: bold"></span>, <span id="default_year_number" style="font-weight: bold"></span>?
         </div>
         <div class="modal-footer" style="text-align: center">
            <button type="button" id="default_period_no_btn" class="btn btn-default">CONTINUE WITH SELECTED PERIOD</button> <br /><br />
            <button type="button" id="default_period_no_yes" class="btn btn-primary">CONTINUE WITH DEFAULT PERIOD</button>
         </div>
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

      $(document).on('change', '#bank', function() {
         var bank = $('option:selected', this).val();

         $('#start_date').val("");
         $('#end_date').val("");

         if(bank !== "") {
            $('#bank-error').hide();
            $('#start_date').val("");
            $('#end_date').val("");

            $("#fbank").val("").trigger("change");

            if(bank == "CA110") {
               $('#fbank').show();
            } else {
               $('#fbank').hide();
               onSelectBankAndFB();
            }
         }
      });

      $(document).on('change', '#fbank', function() {
         var fbank = $('option:selected', this).val();
         if(fbank !== "") {
            $('#fbank-error').hide();
            onSelectBankAndFB();
         }
      });

      $(document).on('change', '#start_date', function() {
         var start_date = $(this).val();
         date_arr = start_date.split("-");
         
         var lastDay = lastday(date_arr[2], date_arr[1]);
         console.log(">>> Year  >>"+date_arr[2]);
         console.log(">>> Month >>"+date_arr[1]);
         console.log(">>> Day   >>"+lastDay);

         $('#end_date').datepicker('setDate', new Date(date_arr[2], date_arr[1]-1, lastDay));
      });

      var lastday = function(y, m) {
         return new Date(y, m, 0).getDate();
      }      

      $('#btn_print').on('click', function() {
         if(!$('#frm_cb').valid()) {
            return;
         }

         var url = '/bank_reconciliation/print_cashbook';
         $("#frm_cb").attr("action", url);
         $("#frm_cb").attr("target", "_blank");
         $("#frm_cb").submit();
      });

      $('#default_period_no_btn').on('click', function() {
         var url = "/bank_reconciliation/print_cashbook";
         $('#default_period').val("no");
         $("#print_form").attr("action", url);
         $('#defaultPeriodModal').modal('hide');
         $("#print_form").submit();
      });

      $('#default_period_yes_btn').on('click', function() {
         var url = "/bank_reconciliation/print_cashbook";
         $('#default_period').val("yes");
         $("#print_form").attr("action", url);
         $('#defaultPeriodModal').modal('hide');
         $("#print_form").submit();
      });

  });

   function onSelectBankAndFB() {

      $.post('/bank_reconciliation/ajax/get_current_details', {
         bank: $('#bank').val(),
         fbank: $('#fbank').val()
      }, function(data) {
         var obj = $.parseJSON(data);
         $('#start_date').val(obj.start_date);
         $('#end_date').val(obj.end_date);

         $('#default_month').val(obj.default_month);
         $('#default_year').val(obj.default_year);
         $('#recon_status').val(obj.status);

         $('#default_month_name').text(obj.default_month_name);
         $('#default_year_number').text(obj.default_year);
      });
   }

   function check_default_recon() {
      var default_month = $('#default_month').val();
      var default_year = $('#default_year').val();
      var recon_status = $('#recon_status').val();

      var selected_date = $('#start_date').val();
      date_arr = selected_date.split("-");

      var selected_month = Number(date_arr[1]);
      var selected_year = date_arr[2];

      console.log("Recon Status   >>> "+recon_status);
      console.log("Default Month  >>> "+default_month);
      console.log("Default Year   >>> "+default_year);
      console.log("Selected Month >>> "+selected_month);
      console.log("Selected Year  >>> "+selected_year);

      if(recon_status == "RECON_COMPLETED") {     

         if(selected_year == default_year) {

         if(selected_month == default_month) {
            return true;
         } else {
            $('#defaultPeriodModal').modal();
            return false;
         }

         } else {
            $('#defaultPeriodModal').modal();
            return false;
         }

      } else {
         return true;
      }
      
   }
</script>