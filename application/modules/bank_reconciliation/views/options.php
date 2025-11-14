<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Bank Reconciliation</h1>
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
            <input type="hidden" id="module" value="bank_reconciliation" />
            <div class="card card-default">
               <div class="card-header options">
                  <h5>Options</h5>
               </div>
               <div class="card-body opt-lnk">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#cashBookModal">
                           Print Cashbook <span>Cash Book will be printed in the PDF Format to do Reconciliation</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/bank_reconciliation/review">
                           Review Previous Month Recon <span>Items from previous month's will be reviewed</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/bank_reconciliation/input_options">
                           Current Month Difference's <span>Input of Current month errors, omission and timing differences</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/bank_reconciliation/other_adjustment">
                           Other Adjustment <span>Input of adjustment items</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#statementModal">
                           Print Statement <span>Reconcilation Statement will be printed in PDF Format</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#utilityModal">
                           Utilities > Datafiles<span>Backup / Restore / Zap of Bank Recon Datafile's</span>
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
                     <label for="cb_bank" class="col-md-12 control-label">Bank : </label>
                     <div class="col-md-12">
                        <select name="cb_bank" id="cb_bank" class="form-control" style="width: 100%" required>
                           <?php echo $cb_bank_options; ?>
                        </select>
                     </div>
                  </div>
                  <div class="row cb_fbank" style="display: none; margin-bottom: 15px">
                     <label for="cb_fbank" class="col-md-12 control-label">Foreign Bank : </label>
                     <div class="col-md-12">
                        <select name="cb_fbank" id="cb_fbank" class="form-control" style="width: 100%" required>
                           <?php echo $cb_fbank_options; ?>
                        </select>
                     </div>
                  </div>
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-6">
                        <div class="row">
                           <label for="cb_start_date" class="col-md-12 control-label">From : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="cb_start_date" name="cb_start_date"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="row">
                           <label for="cb_end_date" class="col-md-12 control-label float-right">To : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="cb_end_date" name="cb_end_date"
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

                  <input type="hidden" id="default_period" name="default_period" />

                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#cashBookModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_cb">Print</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal :: Statement -->
<div id="statementModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form id="frm_st" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Reconciliation Statement</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Statement will be printed in the PDF Format to do Reconciliation</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#cashBookModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <!-- statement section starts -->
               <div class="card-body dv_st">
                  <div class="row" style="margin-bottom: 15px">
                     <label for="st_bank" class="col-md-12 control-label">Bank : </label>
                     <div class="col-md-12">
                        <select name="st_bank" id="st_bank" class="form-control" style="width: 100%" required>
                           <?php echo $st_bank_options; ?>
                        </select>
                     </div>
                  </div>
                  <div class="row st_fbank" style="display: none; margin-bottom: 15px">
                     <label for="st_fbank" class="col-md-12 control-label">Foreign Bank : </label>
                     <div class="col-md-12">
                        <select name="st_fbank" id="st_fbank" class="form-control" style="width: 100%" required>
                           <?php echo $st_fbank_options; ?>
                        </select>
                     </div>
                  </div>
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-6">
                        <div class="row">
                           <label for="st_start_date" class="col-md-12 control-label">From : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="st_start_date" name="st_start_date"
                                 value="<?php echo $start_date; ?>"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                     <div class="col-6">
                        <div class="row">
                           <label for="st_end_date" class="col-md-12 control-label float-right">To : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="st_end_date" name="st_end_date"
                                 value="<?php echo $end_date; ?>"
                                 class="form-control dp_full_date w-120"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-footer dv_st">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#statementModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_print_st">Print</button>
               </div>
               <!-- statement section ends -->

               <!-- complete recon section starts -->
               <div class="card-body dv_rc" style="display: none; color: red">
                  BANK RECONCILIATION FOR <span style="font-style: italic; font-weight: bold; text-transform: uppercase"><?php echo $month.', '.$year; ?></span> COMPLETED?
               </div>

               <div class="card-footer dv_rc" style="display: none">
                  <button type="button" class="btn btn-outline-dark btn-sm" id="btn_recon_cancel">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_complete_recon">CONFIRM</button>
               </div>
               <!-- complete recon section starts -->
            </div>
         </form>
      </div>
   </div>
</div>

<div id="defaultPeriodModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <span style="margin: 0; display: block;">CONFIRM</span>
               <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Confirm the period before doing bank reconciliation</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#defaultPeriodModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
         </div>
         <div class="card-body">
            DEFAULT PERIOD TO DO BANK RECON <span id="default_month_name" style="text-transform: uppercase; font-weight: bold"></span>, <span id="default_year_number" style="font-weight: bold"></span>?
         </div>
         <div class="card-footer">
            <button type="button" id="default_period_no_btn" class="btn btn-outline-dark btn-sm">CONTINUE WITH SELECTED PERIOD</button>
            <button type="button" id="default_period_no_yes" class="btn btn-secondary btn-sm float-right">CONTINUE WITH DEFAULT PERIOD</button>
         </div>
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

      // cash book starts
      $(document).on('change', '#cb_bank', function() {
         var bank = $('option:selected', this).val();

         $('#cb_start_date').val("");
         $('#cb_end_date').val("");

         if(bank !== "") {
            $('#bank-error').hide();
            $('#cb_start_date').val("");
            $('#cb_end_date').val("");

            $("#cb_fbank").val("").trigger("change");

            if(bank == "CA110") {
               $('#cb_fbank').show();
            } else {
               $('#cb_fbank').hide();
               onSelectBankAndFB();
            }
         }
      });

      $(document).on('change', '#cb_fbank', function() {
         var fbank = $('option:selected', this).val();
         if(fbank !== "") {
            $('#cb_fbank-error').hide();
            onSelectBankAndFB();
         }
      });

      $(document).on('change', '#cb_start_date', function() {
         var start_date = $(this).val();
         date_arr = start_date.split("-");
         
         var lastDay = lastday(date_arr[2], date_arr[1]);
         console.log(">>> Year  >>"+date_arr[2]);
         console.log(">>> Month >>"+date_arr[1]);
         console.log(">>> Day   >>"+lastDay);

         $('#cb_end_date').datepicker('setDate', new Date(date_arr[2], date_arr[1]-1, lastDay));
      });

      var lastday = function(y, m) {
         return new Date(y, m, 0).getDate();
      }      

      $('#btn_print_cb').on('click', function() {
         if(!$('#frm_cb').valid()) {
            return;
         }

         var df_prd = check_default_recon();
         if(df_prd) {
            var url = '/bank_reconciliation/print_cashbook';
            $("#frm_cb").attr("action", url);
            $("#frm_cb").attr("target", "_blank");
            $("#frm_cb").submit();
         }
      });

      $('#default_period_no_btn').on('click', function() {
         var url = "/bank_reconciliation/print_cashbook";
         $('#default_period').val("no");
         $("#frm_cb").attr("action", url);
         $('#defaultPeriodModal').modal('hide');
         $("#frm_cb").submit();
      });

      $('#default_period_yes_btn').on('click', function() {
         var url = "/bank_reconciliation/print_cashbook";
         $('#default_period').val("yes");
         $("#frm_cb").attr("action", url);
         $('#defaultPeriodModal').modal('hide');
         $("#frm_cb").submit();
      });

   // reconciliation statement starts
      $(document).on('change', '#st_bank', function() {
         var bank = $('option:selected', this).val();
         if(bank !== "") {
            $("#st_fbank").val("").trigger("change");

            if(bank == "CA110") {
               $('#st_start_date').val("");
               $('#st_end_date').val("");
               $('#st_fbank').show();
            } else {
               $('#st_fbank').hide();
               onSelectBankAndFB_st();
            }
         }
      });

      $(document).on('change', '#st_fbank', function() {
         var fbank = $('option:selected', this).val();
         if(fbank !== "") {
            onSelectBankAndFB_st();
         }
      });

      $('#btn_print_st').on('click', function() {
         if(!$('#frm_st').valid()) {
            return;
         }

         var url = "/bank_reconciliation/print_statement";
         $("#frm_st").attr("action", url);
         $("#frm_st").attr("target", "_blank");

         $('.dv_st').hide(); // statement - hides
         $('.dv_rc').show(); // complete recon - shows

         $("#frm_st").submit();
      });

      $('#statementModal').on('shown.bs.modal', function () {
         $('.dv_st').show(); // statement - hides
         $('.dv_rc').hide(); // complete recon - shows
      });
    
      $('#btn_complete_recon').on('click', function() {
         $.confirm({
            title: '<i class="fa fa-info"></i> Confirm ?',
            content: "Click 'YES' to complete this reconcilation process",
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function() {
                     url = "/bank_reconciliation/complete_recon";
                     $("#frm_st").attr("action", url);
                     $("#frm_st").removeAttr("target");
                     $("#frm_st").submit();
                  }
               },
               no: {
                  btnClass: 'btn-dark',
                  action: function() {
                  }
               },
            }
         });
      });

      $('#btn_recon_cancel').on('click', function() {
         $('#statementModal').modal('hide');
         $('.dv_st').show(); // statement - show
         $('.dv_rc').hide(); // complete recon - hides
      });
      // reconciliation statement ends

   }); // document ends

   // cashbook
   function onSelectBankAndFB() {
      $.post('/bank_reconciliation/ajax/get_current_details', {
         bank: $('#cb_bank').val(),
         fbank: $('#cb_fbank').val()
      }, function(data) {
         var obj = $.parseJSON(data);
         $('#cb_start_date').val(obj.start_date);
         $('#cb_end_date').val(obj.end_date);

         $('#default_month').val(obj.default_month);
         $('#default_year').val(obj.default_year);
         $('#recon_status').val(obj.status);

         $('#default_month_name').text(obj.default_month_name);
         $('#default_year_number').text(obj.default_year);
      });
   }

   // statement
   function onSelectBankAndFB_st() {
      $.post('/bank_reconciliation/ajax/get_current_details', {
         bank: $('#st_bank').val(),
         fbank: $('#st_fbank').val()
      }, function(data) {
         var obj = $.parseJSON(data);
         $('#st_start_date').val(obj.start_date);
         $('#st_end_date').val(obj.end_date);
      });
   }

   function check_default_recon() {
      var default_month = $('#default_month').val();
      var default_year = $('#default_year').val();
      var recon_status = $('#recon_status').val();

      var selected_date = $('#cb_start_date').val();
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