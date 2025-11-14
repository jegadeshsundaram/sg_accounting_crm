<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">General Ledger</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">GL</li>
               <li class="breadcrumb-item active">YE Closing</li>
            </ol>
         </div>
      </div>
   </div>
</div>

<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <div class="card card-default">
               <div class="card-header">
                  <h5>Year-End Closing</h5>
                  <span id="alert" style="display: none"></span>
               </div>
               
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-3"></div>
                     <div class="col-md-6">
                        <div class="row" id="ye_closing_start_section" style="display: none;">
                           <div class="col-md-12">
                              <a class="btn btn-block start_year_end_closing_lnk" style="color: #fff; background: #226fbe;">
                                 START YEAR END CLOSING <span>Start Year end closing for last financial year</span>
                              </a>
                           </div>
                        </div>

                        <div class="row" id="ye_closing_continue_section" style="display: none">
                           <div class="col-md-12">
                              <a class="stock_costing_lnk btn btn-block" style="color: #fff; background: #226fbe;">
                                 GENERATE STOCK COSTING<span>Generate stock costing to get the closing stock amount</span>
                              </a>
                           </div>

                           <div class="col-md-12">
                              <a class="pl_and_bs_lnk btn btn-block" style="color: #fff; background: #226fbe;">
                                 PRINT P&L STATEMENT AND BALANCE SHEET<span>Print Profit and Loss & Balance Sheet </span>
                              </a>
                           </div>

                           <div class="col-md-12">
                              <a class="proceed_ye_closing_lnk btn btn-block" style="color: #fff; background: #226fbe;">
                                 PROCEED YEAR END CLOSING<span>Proceed Year End closing for the last financial year</span>
                              </a>
                           </div>
                        </div>

                        <div class="row" id="ye_closing_failed_section" style="display: none">
                           <div class="col-md-12">
                              <a class="print_trail_balance_lnk btn btn-block" style="color: #fff; background: #226fbe;">
                                 GENERATE TRAIL BALANCE<span>Generate Trail Balance before completion of Year End Closing Process</span>
                              </a>
                           </div>

                           <div class="col-md-12">
                              <a class="restore_gl_lnk btn btn-block" style="color: #fff; background: #226fbe;">
                                 RESTORE GL<span>Restore GL Datafiles</span>
                              </a>
                           </div>
                        </div>

                        <div class="row" id="ye_closing_success_section" style="display: none">
                           <div class="col-md-12">
                              <a class="print_trail_balance_lnk btn btn-block" style="color: #fff; background: #226fbe;">
                                 PRINT TRAIL BALANCE<span>Print Trail Balance after completion of Year End Closing Process</span>
                              </a>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-12">
                              <a href="/general_ledger" class="btn btn-block" style="color: #fff; background: tomato;">
                                 EXIT PROCESS <span>Exit this year end closing process</span>
                              </a>
                           </div>
                        </div>

                     </div>
                     <div class="col-md-3"></div>
                  </div>
               </div>
               <div class="card-footer">
                  <a href="/general_ledger" class="btn btn-warning btn-sm float-right" style="font-size: 1rem;">
                     <i class="fa-solid fa-angles-left"></i> Main Menu
                  </a>
               </div>
            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<form id="frm_pl" method="post" action="#">
   <input type="hidden" id="date-from" name="date-from" />
   <input type="hidden" id="date-to" name="date-to" />
   <input type="hidden" name="amount" id="amount" />
</form>

<div id="cutOffDateModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm modal-sm">
      <div class="modal-content">
         <form id="frm_" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Set Year-End Date</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Set cut off date to proceed with year end closing.</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#cutOffDateModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
         
               <div class="card-body">
                  <div class="row" style="margin-bottom: 15px">
                     <div class="col-12">
                        <div class="row">
                           <label for="cut_off_date" class="col-md-12 control-label">Cutoff Date : </label>
                           <div class="col-md-12">
                              <input 
                                 type="text"
                                 id="cut_off_date" name="cut_off_date"
                                 class="form-control dp_full_date"
                                 placeholder="dd-mm-yyyy" required />
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#cutOffDateModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_set_cod">Proceed</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<div id="stockCostingModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <span style="margin: 0; display: block;">CHOOSE STOCK COSTING</span>
               <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>Choose Stock Costing option to proceed</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#stockCostingModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
         
            <div class="card-body">
               <button type="button" id="btn_wac_costing" class="btn btn-danger">WAC</button>
               <button type="button" id="btn_fifo_costing" class="btn btn-info">FIFO</button>
            </div>
         </div>
      </div>
   </div>
</div>

<div id="noStockDataModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm modal-sm">
      <div class="modal-content">
         <form id="frm_cs" action="#" method="POST">
            <div class="card card-default" style="margin-bottom: 0rem;">
               <div class="card-header">
                  <span style="margin: 0; display: block;">Input</span>
                  <span style="margin: 0;font-size: 0.7rem;"><strong>Note: </strong>There is no data in Stock. Please input Closing Stock.</span>
                  <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#stockCostingModal"><i class="fa-solid fa-xmark"></i></button>
               </div>
               <div class="card-body">
                  <div class="row">
                     <label for="closing_stock" class="col-md-12 control-label">Closing Stock : </label>
                     <div class="col-md-12">
                        <input 
                           type="number"
                           id="closing_stock" name="closing_stock"
                           class="form-control" required />
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#noStockDataModal">Cancel</button>
                  <button type="button" class="btn btn-secondary btn-sm float-right" id="btn_save_cs" style="margin-left: 10px">Submit</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>

<div id="yeALertModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default">
            <div class="card-header">
               <h5>ALERT!</h5>
            </div>
            <div class="card-body">
               Please generate Stock Costing before proceed with printing P&L Statement and Balance Sheet.
            </div>
            <div class="card-footer" style="text-align: center">
               <button type="button" id="ok_alert_btn" class="btn btn-danger">OK</button>
            </div>
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

      populate_ye_data();      

      $('.start_year_end_closing_lnk').on("click", function() {
         $('#confirmSubmitModal .modal-title').html("<i class='fa fa-info'></i> YEAR END CLOSING");
         $('#confirmSubmitModal .modal-body').html("Please ensure that you have done a backup of data files before you proceed. <br /><br /> Proceed with Year End Closing?");
         $("#confirmSubmitModal").modal();
      });

      $('#btn-confirm-no').click(function() {
         $("#confirmSubmitModal").modal('hide');
      });

      $('#btn-confirm-yes').click(function() {
         $("#confirmSubmitModal").modal('hide');
         $("#cutOffDateModal").modal();
      });

      $('#btn_set_cod').click(function() {
         if(!$('#frm_').valid()) {
            return;
         }

         $.post('/general_ledger/ajax/backup_gl', {
            cut_off_date: $('#cut_off_date').val()
         }, function(data) {
            if (data == 1) {
               $("#cutOffDateModal").modal('hide');
               populate_ye_data();
            } else {
               alert("BACKUP ERROR");
            }
         });
      });

      $('.stock_costing_lnk').click(function() {
         $.post('/general_ledger/ajax/get_stock_values', {
         }, function(stock) {
            if(stock == 0) {
               $('#noStockDataModal').modal();
            } else {
               $('#stockCostingModal').modal();
            }
         });
      });

      // save closing stock
      $('#btn_save_cs').click(function() {
         if(!$('#frm_cs').valid()) {
            return;
         }

         $.post('/general_ledger/ajax/save_closing_stock', {
            closing_stock: $('#closing_stock').val()
         }, function(data) {
            if(data == 1) {
               $('#amount').val($('#closing_stock').val());
               $('#noStockDataModal').modal('hide');

               $('#alert').addClass('success').removeClass('error');
               $('#alert').html("Closing Stock is Saved. Please proceed to Print P&L and Balance Sheet.");
               $('#alert').show();               

            } else {
               alert("Error in Saving Closing Stock");
            }
         });
      });

      $('#btn_wac_costing').click(function() {
         $("#stockCostingModal").modal('hide');
         window.location.href = '/stock/print_wac?process=ye&cut_off_date='+$('#date-to').val();
      });

      $('#btn_fifo_costing').click(function() {
         $("#stockCostingModal").modal('hide');
         window.location.href = '/stock/print_fifo?process=ye&cut_off_date='+$('#date-to').val();
      });

      $('.pl_and_bs_lnk').on("click", function() {
         if($('#amount').val() !== null && $('#amount').val() !== "") {
            // calculate current profit / Loss to generate P&L Statment and Balance Sheet
            var url = '/pl_balance/ajax/generate_pl_statment';
            $.post(url, {
               'date-from': $('#date-from').val(),
               'date-to': $('#date-to').val(),
               'amount': $('#amount').val()
            }, function (data) {
               var obj = $.parseJSON(data);
               if(obj.current_profit_after_pl !== "") {
                  // proceed with printing profit & loss statment and balance sheet
                  var url = '/pl_balance/print';
                  $("#frm_pl").attr("action", url);
                  $("#frm_pl").submit();
               } else {
                  $('#yeALertModal').modal();
               }
            });

         } else {
            $('#yeALertModal').modal();
         }
      });

      $('.proceed_ye_closing_lnk').click(function() {
         $.post('/general_ledger/ajax/process_year_end_closing', {
            ye_closing: true
         }, function(data) {
         var obj = $.parseJSON(data);
            if(obj.sum_of_debit_and_credit == 0) {
               $('#ye_closing_continue_section').hide();
               $('#ye_closing_failed_section').hide();
               $('#ye_closing_start_section').hide();               
               $('#ye_closing_success_section').show();

               $('#alert').html("Year End Closing process is successfully completed.");
               $('#alert').addClass('success').removeClass('error');
               $('#alert').show();

            } else {
               $('#ye_closing_continue_section').hide();
               $('#ye_closing_failed_section').show();
               
               $('#alert').addClass('error').removeClass('success');
               $('#alert').html("Year End Closing process is <span style='color: red'>Failed</span>. <br />Please check trail balance and restore GL to original state.");
               $('#alert').show();
            }
         });
      });

      $('.print_trail_balance_lnk').click(function() {
         window.location.href = "/general_ledger/print_trail_balance?cut_off_date="+$('#tb_cut_off').val();
      });

      $('.restore_gl_lnk').click(function() {
         $.post('/general_ledger/ajax/restore_gl', {
            restore: true
         }, function(data) {
            if (data == 1) {
               $('#ye_closing_continue_section').hide();
               $('#ye_closing_failed_section').hide();
               $('#ye_closing_start_section').show();            

               $('#alert').addClass('success').removeClass('error');
               $('#alert').html("GL is restored");
               $('#alert').show();

            } else {
               console.log("BACKUP ERROR");
            }
         });
      });

   }); // document ends
   
   function populate_ye_data() {
      // Populate Year end closing details ye_closing.TBL based on last closing year
      $.post('/general_ledger/ajax/get_ye_values', {
      }, function(data) {
         var obj = $.parseJSON(data);
         $('#date-from').val(obj.new_fy_start_date);
         $('#date-to').val(obj.new_fy_end_date);
         $('#tb_cut_off').val(obj.tb_cut_off);
         $('#amount').val(obj.closing_stock);

         if(obj.ye_closing_status !== "" && obj.ye_closing_status == "backup") { // set "backup" to make this to work
            $('#ye_closing_start_section').hide();
            $('#ye_closing_failed_section').hide();
            $('#ye_closing_success_section').hide();            
            
            $('#ye_closing_continue_section').show();

            $('#alert').addClass('success').removeClass('error');
            $('#alert').html("Data Files backup done. Proceed with Year End Closing.");
            $('#alert').show();

         } else if(obj.ye_closing_status !== "" && obj.ye_closing_status == "failed") {
            $('#ye_closing_continue_section').hide();
            $('#ye_closing_success_section').hide();

            $('#ye_closing_failed_section').show();

            $('#alert').addClass('error').removeClass('success');
            $('#alert').html("Year End Closing process is <span style='color: red'>Failed</span>. <br />Please check trail balance and restore GL to original state.");
            $('#alert').show();
         
         } else if(obj.ye_closing_status !== "" && obj.ye_closing_status == "restored") {
            $('.alert').hide();
            $('#ye_closing_continue_section').hide();
            $('#ye_closing_failed_section').hide();
            $('#ye_closing_success_section').hide();

            $('#ye_closing_start_section').show();

         } else if(obj.ye_closing_status !== "" && obj.ye_closing_status == "success") {
            $('#ye_closing_continue_section').hide();
            $('#ye_closing_failed_section').hide();
            $('#ye_closing_start_section').hide();

            $('#ye_closing_success_section').show();

            $('#alert').addClass('success').removeClass('error');
            $('#alert').html("Year End Closing process is successfully completed.");
            $('#alert').show();
         
         } else {
            $('#ye_closing_continue_section').hide();
            $('#ye_closing_failed_section').hide();
            $('#ye_closing_success_section').hide();

            $('#ye_closing_start_section').show();
         }
      });
   }
</script>