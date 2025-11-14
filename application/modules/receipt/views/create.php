<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Receipt</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Receipt</li>
               <li class="breadcrumb-item">View</li>
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

            <input type="hidden" id="redirect_url" value="/receipt" />

            <!-- form - starts -->
            <form autocomplete="off" id="form_" method="post">
               
               <!-- refernce number - hidden field -->
               <input
                  type='hidden'
                  name='receipt_ref_no' id="receipt_ref_no"
                  value="<?php echo $receipt_ref_no; ?>" />

               <div class="card card-default">
                  <div class="card-header">
                     <span class="float-left"><?php echo $receipt_ref_no; ?></span>
                     <a href="/receipt" class="btn btn-info btn-sm float-right">Back</a>
                  </div>
                  <div class="card-body">

                     <div class="row">
                        <div class="col-md-5">
                           <!-- Customer dropdown list -->
                           <label for="customer_id" class="control-label f-bold">To,</label><br />
                           <select name="customer_id" id="customer_id" class="form-control req" required>
                              <?php echo $customer_options; ?>
                           </select>
                           <br />

                           <!-- Display customer details upon selection -->
                           <div class="dsply_customer_details" style="display: none;">
                           </div>
                        </div>

                        <div class="col-md-3"></div>

                        <div class="col-md-4" style="margin-top: 20px">
                           <div class="form-group row">
                              <label for="created_on" class="col-md-4 control-label">Date : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text"
                                    id="created_on" name="created_on"
                                    class="form-control dp_full_date req w-120"
                                    placeholder="dd-mm-yyyy"                                 
                                    value="<?php echo date('d-m-Y'); ?>" required />
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-3" style="margin-top: 20px">
                           <label for="bank" class="control-label">Bank Details :</label>
                           <input 
                              type="text" 
                              id="bank" name="bank" 
                              class="form-control req" required />
                        </div>
                        
                        <div class="col-md-1"></div>

                        <div class="col-md-3" style="margin-top: 20px">
                           <label for="Cheque" class="control-label">Cheque # :</label>
                           <input 
                              type="text" 
                              id="cheque" name="cheque" 
                              class="form-control req" required />
                        </div>

                        <div class="col-md-1"></div>

                        <div class="col-md-3" style="margin-top: 20px">
                           <label for="amount" class="control-label">Amount $ :</label>
                           <input 
                              type="number" 
                              id="amount" name="amount" 
                              class="form-control req w-200" required />
                        </div>

                        <div class="col-md-1"></div>
                     </div>

                     <div class="row">
                        <div class="col-md-10" style="margin: 15px 0 30px">
                           <label for="other_reference" class="control-label">Remarks :</label>
                           <textarea id="other_reference" name="other_reference" class="form-control" placeholder="Optional! Remarks about the receipt"></textarea>
                        </div>
                        <div class="col-md-2"></div>
                     </div>

                     <div class="row" id="dv_confirm_contra" style="display: none">
                        <div class="col-md-12" style="text-align: center; margin: 15px 0 30px">
                           <div class="contra_box">
                           <label for="other_reference" class="control-label" style="font-size: 1.3rem">Proceed to CONTRA?</label>
                              <button type="button" id="btn_contra_yes" class="btn btn-success btn-sm"><i class="fa-solid fa-check"></i> Yes</button>
                              <button type="button" id="btn_contra_no" class="btn btn-danger btn-sm"><i class="fa-solid fa-xmark"></i> No</button>
                           </div>
                        </div>
                     </div>

                     <div class="row" id="dv_contra" style="display: none">
                        <div class="col-md-12 table-responsive" style="margin: 15px 0 30px">
                           <!-- Contra Balance Table -->
                           <table id="tbl_contra" class="tbl-main" align="center" style="width: 600px; margin-top: 10px;">
                              <thead>
                                 <tr>
                                    <th class="header" colspan="4">Contra Receipt</th>
                                 </tr>
                                 <tr>
                                    <th style="width: 100px;">Date</th>
                                    <th style="width: 120px;">Reference</th>
                                    <th style="width: 180px; text-align: right">Debit</th>
                                    <th style="width: 180px; text-align: right">Credit</th>
                                 </tr>
                              </thead>

                              <tbody></tbody>

                              <tfoot>
                                 <tr>
                                    <td colspan="4">
                                       <span id="balance_entry_reference"></span>
                                       <span id="final_balance_amount_display"></span>
                                    </td>                                    
                                 </tr>
                              </tfoot>
                           </table>
                           <span id="balance_entry_amount" style="display: none"></span>
                        </div>
                     </div>

                     <div class="row" id="dv_debits_credits" style="display: none">
                        <div class="col-md-6 table-responsive">

                           <!-- Credit Entry Table -->
                           <table id="tbl_credits" align="center" class="tbl-sub" style="width: 500px !important; margin-top: 30px">
                              <thead>
                                 <tr>
                                    <th class="header" colspan="4">Credit Reference's</th>
                                 </tr>
                                 <tr>
                                    <th style="width: 80px; text-align: left;">Date</th>
                                    <th style="width: 100px; text-align: left;">Reference</th>
                                    <th style="width: 120px; text-align: right;">Amount</th>
                                    <th style="width: 50px; text-align: center;"></th>
                                 </tr>
                              </thead>
                              <tbody></tbody>
                           </table>

                        </div>
                        <div class="col-md-6 table-responsive">

                           <!-- Debit Entry Table -->
                           <table id="tbl_debits" align="center" class="tbl-sub" style="width: 500px !important; margin-top: 30px">
                              <thead>
                                 <tr>
                                    <th class="header" colspan="4">Debit Reference's</th>
                                 </tr>
                                 <tr>
                                    <th style="width: 75px; text-align: left;">Date</th>
                                    <th style="width: 90px; text-align: left;">Reference</th>
                                    <th style="width: 120px; text-align: right;">Amount</th>
                                    <th style="width: 50px"></th>
                                 </tr>
                              </thead>
                              <tbody></tbody>
                           </table>

                        </div>
                     </div>

                  </div> <!-- card body ends -->
                  <div class="card-footer">
                     <button type="button" id="btn_print" class="btn btn-secondary btn-sm">Print Receipt</button>
                     <button type="button" id="btn_reset_contra" class="btn bg-maroon btn-sm" style="display: none"><i class="fa fa-refresh" aria-hidden="true"></i> Reset Contra</button>
                     <button type="button" id="btn_submit" class="btn btn-warning btn-sm float-right">Submit</button>

                     <!-- Hidden Fields -->
                     <input type="hidden" name="currency" id="currency" />
                     <input type="hidden" name="ar_ids" id="ar_ids" />
                     <input type="hidden" name="final_balance_entry_id" id="final_balance_entry_id" />
                     <input type="hidden" name="final_balance_entry_reference" id="final_balance_entry_reference" />
                     <input type="hidden" name="final_balance_total" id="final_balance_total" />

                     <input type="hidden" name="bank_accn" id="bank_accn" value="<?php echo $bank_accn; ?>" />
                     <input type="hidden" name="fb_accn" id="fb_accn" value="<?php echo $fb_accn; ?>" />

                  </div>
               </div>
            </form>
         </div>
      </div>

   </div>
</div>

<div id="bankModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="modal-header">
            <h3 class="modal-title" style="text-align: center">Confirm Bank & Submit Receipt</h3>
         </div>
         <div class="modal-body">
            <div class="form-group row">
               <label for="ddl_bank_accn" class="col-md-12">Bank</label>
               <div class="col-md-12">
                  <select id="ddl_bank_accn" name="ddl_bank_accn" class="form-control">
                     <?php echo $bank_accn_list; ?>
                  </select>
               </div>
            </div>

            <div class="form-group row" id="dv_ddl_fb_accn" style="display: <?php if ($bank_accn == 'CA110') {
                echo 'block';
            } else {
                echo 'none';
            } ?>">
               <label for="ddl_fb_accn" class="col-md-12">FB Account</label>
               <div class="col-md-12">
                  <select id="ddl_fb_accn" name="ddl_fb_accn" class="form-control">
                     <?php echo $fb_accn_list; ?>
                  </select>
               </div>
            </div>
         </div>
         <div class="modal-footer justify-content-between">
            <button type="button" id="btn-cancel-receipt" class="btn btn-info btn-sm">Cancel</button>
            <button type="button" id="btn-submit-receipt" class="btn btn-warning btn-sm float-right">Submit Receipt</button>
         </div>
      </div>
   </div>
</div>

<style>
   .dsply_customer_details {
      padding: 10px 10px 10px 20px;
      color: dimgray;
      border-radius: 5px;
   }
   table td {
      color: #263544;
      letter-spacing: 1px;
   }
   .tbl-main .header {
      background: gray !important;
      font-size: 1.1rem;
      color: #fff;
      padding: 8px 10px;
      letter-spacing: 1px;
   }
   .tbl-sub .header {
      background: lavender !important;
      font-size: 1rem;
      color: #000;
      padding: 8px 10px;
      letter-spacing: 1px;
      border: 1px solid lavender;
   }
   #balance_entry_reference {   
      color: dimgray;
      font-style: italic;
      font-weight: 600;
   }
   #final_balance_amount_display {
      color: black;
      font-weight: 500;
      margin-left: 25px;
   }
   .tbl-main th {
      background: gainsboro;
      padding: 8px 10px;
   }
   .tbl-sub th {
      border: 1px solid gainsboro;
      padding: 8px 10px;
   }
   .tbl-main tr td, .tbl-sub tr td {
      border: 1px solid gainsboro;      
      padding: 10px;
   }
   tfoot td {
      border: none !important;
      border-top: 2px solid gainsboro !important;
      border-bottom: 2px solid gainsboro !important;
   }
   .check-container {
      cursor: pointer;
   }
   .checkmark {
      position: absolute;
      top: -3px;
      left: 10px;
      height: 30px;
      width: 30px;
      background-color: skyblue;
      border-radius: 3px;
   }
   .check-container .checkmark::after {
      left: 11px;
      top: 2px;
      width: 10px;
      height: 18px;
   }
   .credit_amount, .debit_amount {
      text-align: right;
   }
   .entry_amount {
      text-align: right;
   }
   .tbl-disable {
      background: gainsboro;
      opacity: 0.5;
      user-select: none;
      cursor: not-allowed;
   }

   .disabled {
      background: gray !important;
      opacity: 0.5;
      cursor: not-allowed;
   }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<script src="/assets/js/exit.js"></script>

<script type="text/javascript">
   var ref_no = "";
   // document starts
   $(function() {

      // reference check
      ref_no = $('#receipt_ref_no').val();
      var ref_parts = ref_no.split('.');
      $.post('/receipt/ajax/double_reference', {
         text_prefix: ref_parts[0],
         number_suffix: ref_parts[1]
      }, function (data) {
         if (data > 0) {
            window.location.href = "/receipt/settings";
         }
      });

      $('select').select2();      

      $("#customer_id").change(function(event) {
         customer_id = $("#customer_id option:selected").val();
         if (customer_id !== "") {
            $.post('/receipt/ajax/get_receipt_details', {
               customer_id: customer_id
            }, function (data) {
               var obj = $.parseJSON(data);

               if($('#amount').val() !== "") {
                  $('#dv_contra').hide();
                  $('#tbl_contra tbody').empty();
                  $('#balance_entry_reference').text("");
                  $('#final_balance_amount_display').text("");
                  $('#dv_debits_credits').hide();
                  $("#dv_confirm_contra").fadeIn(700);

                  $('.entry_check').prop('disabled', false);
                  $('.entry_check').parents("tr").find(".checkmark").removeClass("disabled");
                  $('.entry_check').prop('checked', false);
               }

               // address details, phone, email & currency
               $(".dsply_customer_details").html(obj.customer_address);
               $(".dsply_customer_details").show();

               // hidden fields
               $('#currency').val(obj.customer_currency);

               $("#tbl_debits tbody").html(obj.debit_entries);
               $("#tbl_credits tbody").html(obj.credit_entries);

               $('#bank').focus();
            });
         } else {
            // address details, phone, email & currency
            $(".dsply_customer_details").html('');
            $(".dsply_customer_details").hide();
         }
      });

      $("#bank").on('input', function () {
         validateInputFields();
      });

      $("#cheque").on('input', function () {
         validateInputFields();
      });

      $("#amount").on('input', function () {
         if($(this).val() !== "") {
            $('#dv_contra').hide();
            $('#tbl_contra tbody').empty();
            $('#balance_entry_reference').text("");
            $('#final_balance_amount_display').text("");
            $('#dv_debits_credits').hide();
            $("#dv_confirm_contra").fadeIn(700);

            $('.entry_check').prop('disabled', false);
            $('.entry_check').parents("tr").find(".checkmark").removeClass("disabled");
            $('.entry_check').prop('checked', false);
         }

         validateInputFields();
      });

      // on clicking NO for Contra
      $("#btn_contra_no").click(function () {
         $('#ar_ids').val("");

         $('#dv_contra').hide();
         $('#dv_debits_credits').hide();

         $("#tbl_contra tbody").empty();

         $("#dv_confirm_contra").fadeOut(700);
      });

      // on clicking YES for Contra
      $("#btn_contra_yes").click(function() {

         if(!$('#form_').valid()) {
            return false;
         }

         add_receipt_entry();

         $('#dv_contra').show();
         $('#dv_debits_credits').show();

         calculate_final_balance();

         $("#dv_confirm_contra").fadeOut(700);
      });

      // On check - Debit or Credit Entries
      $(document).on("click", ".entry_check", function() {
         var entry_id = $(this).closest('tr').find(".entry_id").text();
         var entry_type = $(this).closest('tr').find(".entry_type").text();

         if($(this).prop("checked") == true) {
            if(entry_type == "DR") {
               $('#btn_reset_contra').show();
               add_debit_entry(entry_id);
            } else if(entry_type == "CR") {
               $('#btn_reset_contra').show();
               add_credit_entry(entry_id);
            } else {
               alert("Entry Type Empty");
            }

            $(this).prop('disabled', true);
            $(this).parents("tr").find(".checkmark").addClass("disabled");

         } else {
            remove_entry_from_contra(entry_id, entry_type);
         }
      });

      $('#btn_reset_contra').on('click', function() {
         $.confirm({
            title: '<i class="fa fa-info"></i> Reset Confirmation',
            content: 'Are you sure to Reset ?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function(){
                     reset_contra();
                  }                    
               },
               no: {
                  btnClass: 'btn-dark',
                  action: function(){}
               },
            }
         });
      });

      var save_form_action = '/receipt/save';
      var print_form_action = '/receipt/print_stage_1';

      $("#btn_print").on('click', function() {
         $('#ar_ids').val(contra_transaction_ids);
         $("#form_").attr("action", print_form_action);
         $("#form_").attr("target", "_blank");
         $('#form_').submit();
      });

      $("#abort_btn").on('click', function() {
         $.confirm({
            title: '<i class="fa fa-info"></i> Abort Confirmation',
            content: 'Are you sure to abort ?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function(){
                     window.location.href = "/receipt";
                  }
               },
               no: {
                  btnClass: 'btn-dark',
                  action: function(){}
               },
            }
         });
      });

      $('#btn_submit').click(function (e) {
         if(!$('#form_').valid()) {
            return false;
         }

         $('#ar_ids').val(contra_transaction_ids);
         $("#form_").removeAttr("target");
         $("#form_").attr("action", save_form_action);
         $('#bankModal').modal();

         if($('#currency').val() == "SGD") {
            $('#ddl_bank_accn').find('option:contains("CA110 : FOREIGN BANK CONTROL ACCOUNT")').attr("disabled", "disabled");
            $('#ddl_bank_accn').val("CA101").trigger('change');
            $('#ddl_fb_accn').val("").trigger('change');
            $('#dv_ddl_fb_accn').hide();
         }

         $('#ddl_bank_accn').select2();
      });

      // Bank Account - On Change
      $(document).on('change', '#ddl_bank_accn', function() {
         $('#ddl_fb_accn').val("").trigger('change');
         var accn = $('option:selected', this).val();
         if(accn !== "") {
            $('#bank_accn').val(accn);
            if(accn == "CA110") {
               $('#fb_accn').select2();
               $('#dv_ddl_fb_accn').show();
            } else {
               $('#dv_ddl_fb_accn').hide();
            }
         } else {
            $('#bank_accn').val("");
         }
      });

      // FB Account - On Change
      $(document).on('change', '#ddl_fb_accn', function() {
         var accn = $('option:selected', this).val();
         if(accn !== "") {
            $('#fb_accn').val(accn);
         } else {
            $('#fb_accn').val("");
         }
      });

      $('#btn-submit-receipt').click(function() {
         if($("#bank_accn").val() == "") {
            console.log("Bank DDM is not selected");

         } else if($("#bank_accn").val() == "CA110" && $("#fb_accn").val() == "") {
            console.log("FB Bank DDM is not selected");
            
         } else {
            // In any case the current reference is used by other source, then
            // this will update the new reference to avoid more than one with same reference
            $.post('/receipt/ajax/double_receipt_inc', {
               ref_no: $('#receipt_ref_no').val()
            }, function (ref_no) {
               $('#receipt_ref_no').val(ref_no.trim());
               $('#bankModal').modal('hide');
               $('#form_').submit();
            });
         }
      });

      $('#btn-cancel-receipt').click(function() {
         $('#bankModal').modal('hide');
      });

   }); // document ends

   function validateInputFields() {
      if($("#bank").val() == "" || $("#cheque").val() == "" || $('#amount').val() == "" || $('#amount').val() <= 0) {
         $("#dv_confirm_contra").fadeOut(700);
      } else {
         $("#dv_confirm_contra").fadeIn(700);
      }
   }

   function enable_tbl(tbl) {
      if(tbl == "dr_cr") {

         $('.entry_check').prop('disabled', true);
         $('.entry_check').parents("tr").find(".checkmark").addClass("disabled");

         $('#tbl_debits').addClass('tbl-disable');
         $('#tbl_credits').addClass('tbl-disable');

      } else if(tbl == "dr") {

         // If already checked, then disable it always
         $('#tbl_debits tbody tr').each(function() {
            $(this).find('.entry_check').each(function() {
               if($(this).prop("checked") == true) {
                  $(this).prop('disabled', true);
                  $(this).parents("tr").find(".checkmark").addClass("disabled");
               } else {
                  $(this).prop('disabled', false);
                  $(this).parents("tr").find(".checkmark").removeClass("disabled");
               }
            }); 
         });

         $('#tbl_credits .entry_check').prop('disabled', true);
         $('#tbl_credits .entry_check').parents("tr").find(".checkmark").addClass("disabled");

         $('#tbl_debits').removeClass('tbl-disable');         
         $('#tbl_credits').addClass('tbl-disable');

      } else if(tbl == "cr") {

         // If already checked, then disable it always
         $('#tbl_credits tbody tr').each(function() { 
            $(this).find('.entry_check').each(function() {
               if($(this).prop("checked") == true) {
                  $(this).prop('disabled', true);
                  $(this).parents("tr").find(".checkmark").addClass("disabled");
               } else {
                  $(this).prop('disabled', false);
                  $(this).parents("tr").find(".checkmark").removeClass("disabled");
               }
            }); 
         });

         $('#tbl_debits .entry_check').prop('disabled', true);
         $('#tbl_debits .entry_check').parents("tr").find(".checkmark").addClass("disabled");

         $('#tbl_credits').removeClass('tbl-disable');
         $('#tbl_debits').addClass('tbl-disable');
      }
   }

   var contra_transaction_ids = [];
   function add_debit_entry(entry_id) {

      contra_transaction_ids.push(entry_id);

      entry_date = $(".entry_"+entry_id).find("td:eq(0)").text();
      entry_reference = $(".entry_"+entry_id).find("td:eq(1)").text();
      entry_amount = $(".entry_"+entry_id).find("td:eq(2)").text();

      if ($('#tbl_contra tbody tr').length == 0) {
         add_entry_to_contra(entry_id, entry_date, "dr_entry", entry_reference, entry_amount, "", "1");
         $('#contra_'+entry_id).prop('disabled', true);
         $('#contra_'+entry_id).parents("tr").find(".checkmark").addClass("disabled");
         calculate_final_balance();
         $('#btn_reset_contra').show();
         return;
      }

      // selected invoice details
      var balance_amount = 0;
      var settled_amount = 0;
      var un_settled_amount = 0;
      var last_entry_id = "";
      var same_entries = "";

      // net balance
      var net_balance = parseFloat($('#balance_entry_amount').text().replace(new RegExp(',', 'g'), ''));

      // Selected entry amount
      var entry_amount = parseFloat(entry_amount.replace(new RegExp(',', 'g'), ''));

      // balance amount after contra
      balance_amount = Number(net_balance) + Number(entry_amount);

      console.log("\n**** Add ENTRY - START ****");
      console.log("1. Net Balance >>>> "+net_balance);
      console.log("2. Entry Amount >>>> "+entry_amount);
      console.log("3. Balance Amount >>>> "+balance_amount);

      if(balance_amount == 0) { // Balance = 0

         console.log("\nDebit Entry - Balance = 0\n");

         last_entry_id = $('#tbl_contra tbody tr:last').attr('id');

         add_entry_to_contra(entry_id, entry_date, "dr_entry", entry_reference, entry_amount, "", "1");

         same_entries = $('.'+last_entry_id).length;

         if(same_entries > 1) {
            var item_date;
            var item_reference;
            var item_value = 0;
            var item_total = 0;
            $("tr."+last_entry_id).each(function() {
               balance_type_split = $(this).attr('class').split(' ')[1];
               item_date = $(this).find('.entry_date').html();
               item_reference = $(this).find('.entry_reference').html();

               if(balance_type_split == "dr_entry") {
                  item_value = $(this).find('.debit_amount').html();
                  item_total += parseFloat(item_value.replace(new RegExp(',', 'g'), ''));
               } else if(balance_type_split == "cr_entry") {
                  item_value = $(this).find('.credit_amount').html();
                  item_value = item_value.replace(new RegExp(',', 'g'), '');
                  item_total += parseFloat(-1 * item_value);
               }

               $(this).remove();
            });

            var arr = last_entry_id.split('_');
            var single_entry_id = arr[2];

            if(balance_type_split == "dr_entry") {
               add_entry_to_contra(single_entry_id, item_date, "dr_entry", item_reference, item_total, "", "1");
            } else if(balance_type_split == "cr_entry") {
               add_entry_to_contra(single_entry_id, item_date, "cr_entry", item_reference, "", item_total, "1");
            }
         }

      } else if(balance_amount > 0) { // *** POSITIVE Balance amount *** Split Debit Entry

         console.log("\nDebit Entry - Balance > 0\n");

         settled_amount = entry_amount - balance_amount;
         un_settled_amount = balance_amount;

         console.log("4. Settled Amount >>>> "+settled_amount);
         console.log("5. Un-Settled Amount >>>> "+un_settled_amount);

         // start - Remove splitted records and create one settled record
         last_entry_id = $('#tbl_contra tbody tr:last').attr('id');
         same_entries = $('.'+last_entry_id).length;
         if(same_entries > 1) {
            var item_date;
            var item_reference;
            var item_value = 0;
            var item_total = 0;
            $("tr."+last_entry_id).each(function() {
               balance_type_split = $(this).attr('class').split(' ')[1];
               item_date = $(this).find('.entry_date').html();
               item_reference = $(this).find('.entry_reference').html();

               if(balance_type_split == "dr_entry") {
                  item_value = $(this).find('.debit_amount').html();
                  item_total += parseFloat(item_value.replace(new RegExp(',', 'g'), ''));
               } else if(balance_type_split == "cr_entry") {
                  item_value = $(this).find('.credit_amount').html();
                  item_value = item_value.replace(new RegExp(',', 'g'), '');
                  item_total += parseFloat(-1 * item_value);
               }

               $(this).remove();
            });

            var arr = last_entry_id.split('_');
            var single_entry_id = arr[2];

            if(balance_type_split == "dr_entry") {
               add_entry_to_contra(single_entry_id, item_date, "dr_entry", item_reference, item_total, "", "1");
            } else if(balance_type_split == "cr_entry") {
               add_entry_to_contra(single_entry_id, item_date, "cr_entry", item_reference, "", item_total, "1");
            }

         }
         // end

         // add debit settled row
         add_entry_to_contra(entry_id, entry_date, "dr_entry", entry_reference, settled_amount, "", "1");

         // add debit un-settled row
         add_entry_to_contra(entry_id, entry_date, "dr_entry", entry_reference, un_settled_amount, "", "0");

      } else if(balance_amount < 0) { // *** NEGATIVE Balance amount *** Split Credit Entry

         console.log("\nDebit Entry - Balance < 0\n");

         settled_amount = net_balance + ((-1) * balance_amount);
         un_settled_amount = balance_amount;

         console.log("4. Settled Amount >>>> "+settled_amount);
         console.log("5. Un-Settled Amount >>>> "+un_settled_amount);

         // update top entry amount with settled amount
         if(settled_amount < 0) {
            $("#tbl_contra tbody tr:last").find("td:eq(3)").text((-1 * settled_amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
         } else {
            $("#tbl_contra tbody tr:last").find("td:eq(3)").text(settled_amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
         }

         last_entry_id = $('#tbl_contra tbody tr:last').attr('id');

         // add the settled debit entry first
         add_entry_to_contra(entry_id, entry_date, "dr_entry", entry_reference, (-1 * settled_amount), "", "1");

         // Combine Splitted Records by Removing splitted records and creating one record
         same_entries = $('.'+last_entry_id).length;
         if(same_entries > 1) {
            var item_date;
            var item_reference;
            var item_value = 0;
            var item_total = 0;
            $("tr."+last_entry_id).each(function() {
               balance_type_split = $(this).attr('class').split(' ')[1];
               item_date = $(this).find('.entry_date').html();
               item_reference = $(this).find('.entry_reference').html();

               if(balance_type_split == "dr_entry") {
                  item_value = $(this).find('.debit_amount').html();
                  item_total += parseFloat(item_value.replace(new RegExp(',', 'g'), ''));
               } else if(balance_type_split == "cr_entry") {
                  item_value = $(this).find('.credit_amount').html();
                  item_value = item_value.replace(new RegExp(',', 'g'), '');
                  item_total += parseFloat(-1 * item_value);
               }

               $(this).remove();
            });

            var arr = last_entry_id.split('_');
            var single_entry_id = arr[2];

            // sum all the splitted settled items and create one record
            add_entry_to_contra(single_entry_id, item_date, balance_type_split, item_reference, "", item_total, "1");

            // add Credit Unsettled record
            add_entry_to_contra(single_entry_id, item_date, balance_type_split, item_reference, "", un_settled_amount, "0");

         }
         // end

         // on splitting very first row, its reference and id should be added in unsettled entry
         last_entry_reference = $('#'+last_entry_id).find('.entry_reference').text();
         last_entry_date = $('#'+last_entry_id).find('.entry_date').text();
         var entry_array = last_entry_id.split('_');
         unsetted_entry_id = entry_array[2];

         // remove the credit row and then add 2 rows for partially settled credit entry and balance credit entry
         if(same_entries == 1) {
            $("#contra_entry_"+unsetted_entry_id).remove();
            add_entry_to_contra(unsetted_entry_id, last_entry_date, "cr_entry", last_entry_reference, "", settled_amount, "1");
            add_entry_to_contra(unsetted_entry_id, last_entry_date, "cr_entry", last_entry_reference, "", un_settled_amount, "0");
         }

      }

      $('#contra_'+entry_id).prop('disabled', true);
      $('#contra_'+entry_id).parents("tr").find(".checkmark").addClass("disabled");

      calculate_final_balance();
   }

   function add_credit_entry(entry_id) {

      $('#tbl_contra').show();

      contra_transaction_ids.push(entry_id);

      entry_date = $(".entry_"+entry_id).find("td:eq(0)").text();
      entry_reference = $(".entry_"+entry_id).find("td:eq(1)").text();
      entry_amount = $(".entry_"+entry_id).find("td:eq(2)").text();

      if ($('#tbl_contra tbody tr').length == 0) {
         add_entry_to_contra(entry_id, entry_date, "cr_entry", entry_reference, "", (entry_amount * -1), "1");
         $('#contra_'+entry_id).prop('disabled', true);
         $('#contra_'+entry_id).parents("tr").find(".checkmark").addClass("disabled");
         calculate_final_balance();
         $('#btn_reset_contra').show();
         return;
      }

      // selected invoice details
      var balance_amount = 0;
      var settled_amount = 0;
      var un_settled_amount = 0;
      var last_entry_id = "";
      var same_entries = ""

      // net balance
      var net_balance = parseFloat($('#balance_entry_amount').text().replace(new RegExp(',', 'g'), ''));

      // Selected entry amount
      var entry_amount = parseFloat(entry_amount.replace(new RegExp(',', 'g'), ''));

      // balance amount after contra
      balance_amount = ((-1) * entry_amount) + net_balance;

      console.log("\n**** Add ENTRY - START ****");
      console.log("1. Net Balance >>>> "+net_balance);
      console.log("2. Entry Amount >>>> "+entry_amount);
      console.log("3. Balance Amount >>>> "+balance_amount);

      if(balance_amount == 0) { // Balance = 0

         console.log("\nCredit Entry - Balance = 0\n");

         last_entry_id = $('#tbl_contra tbody tr:last').attr('id');

         add_entry_to_contra(entry_id, entry_date, "cr_entry", entry_reference, "", (entry_amount * -1), "1");

         same_entries = $('.'+last_entry_id).length;

         if(same_entries > 1) {
            var item_date;
            var item_reference;
            var item_value = 0;
            var item_total = 0;

            $("tr."+last_entry_id).each(function() {
               balance_type_split = $(this).attr('class').split(' ')[1];
               item_date = $(this).find('.entry_date').html();
               item_reference = $(this).find('.entry_reference').html();

               if(balance_type_split == "dr_entry") {
                  item_value = $(this).find('.debit_amount').html();
                  item_total += parseFloat(item_value.replace(new RegExp(',', 'g'), ''));
               } else if(balance_type_split == "cr_entry") {
                  item_value = $(this).find('.credit_amount').html();
                  item_value = item_value.replace(new RegExp(',', 'g'), '');
                  item_total += parseFloat(-1 * item_value);
               }

               $(this).remove();
            });

            var arr = last_entry_id.split('_');
            var single_entry_id = arr[2];

            if(balance_type_split == "dr_entry") {
               add_entry_to_contra(single_entry_id, item_date, balance_type_split, item_reference, item_total, "", "1");
            } else if(balance_type_split == "cr_entry") {
               add_entry_to_contra(single_entry_id, item_date, balance_type_split, item_reference, "", item_total, "1");
            }

         } // end

      } else if(balance_amount > 0) { // *** POSITIVE Balance amount *** Split Debit Entry

         console.log("\nCredit Entry - Balance > 0\n");

         settled_amount = net_balance - balance_amount;
         un_settled_amount = balance_amount;

         console.log("4. Settled Amount >>>> "+settled_amount);
         console.log("5. Un-Settled Amount >>>> "+un_settled_amount);

         // update top entry amount with settled amount
         $("#tbl_contra tbody tr:last").find("td:eq(2)").text(settled_amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

         last_entry_id = $('#tbl_contra tbody tr:last').attr('id');

         // add credit settled row
         add_entry_to_contra(entry_id, entry_date, "cr_entry", entry_reference, "", (settled_amount * -1), "1");

         // start - Remove splitted records and create one settled record
         same_entries = $('.'+last_entry_id).length;
         if(same_entries > 1) {
            var item_value = 0;
            var item_total = 0;
            var item_reference = "";
            var item_date = ""
            $("tr."+last_entry_id).each(function() {
               balance_type_split = $(this).attr('class').split(' ')[1];
               item_date = $(this).find('.entry_date').html();
               item_reference = $(this).find('.entry_reference').html();

               if(balance_type_split == "dr_entry") {
                  item_value = $(this).find('.debit_amount').html();
               } else if(balance_type_split == "cr_entry") {
                  item_value = $(this).find('.credit_amount').html();
               }

               item_total += parseFloat(item_value.replace(new RegExp(',', 'g'), ''));
               $(this).remove();
            });

            var arr = last_entry_id.split('_');
            var single_entry_id = arr[2];

            // add all the splitted settled DEBIT items and create one record
            add_entry_to_contra(single_entry_id, item_date, balance_type_split, item_reference, item_total, "", "1");

            // add DEBIT Unsettled record
            add_entry_to_contra(single_entry_id, item_date, balance_type_split, item_reference, un_settled_amount, "", "0");

         }
         // end

         last_entry_reference = $('#'+last_entry_id).find('.entry_reference').text();
         last_entry_date = $('#'+last_entry_id).find('.entry_date').text();
         var entry_array = last_entry_id.split('_');
         unsetted_entry_id = entry_array[2];

         // remove the debit row and then add 2 rows for partially settled debit entry and balance debit entry
         if(same_entries == 1) {
            $("#contra_entry_"+unsetted_entry_id).remove();
            add_entry_to_contra(unsetted_entry_id, last_entry_date, "dr_entry", last_entry_reference, settled_amount, "", "1");
            add_entry_to_contra(unsetted_entry_id, last_entry_date, "dr_entry", last_entry_reference, un_settled_amount, "", "0");
         }

      } else if(balance_amount < 0) { // *** NEGATIVE Balance amount *** Split Credit Entry

         console.log("\nCredit Entry - Balance < 0\n");
         settled_amount = balance_amount + entry_amount;
         un_settled_amount = balance_amount;

         console.log("4. Settled Amount >>>> "+settled_amount);
         console.log("5. Un-Settled Amount >>>> "+un_settled_amount);

         last_entry_id = $('#tbl_contra tbody tr:last').attr('id');
         same_entries = $('.'+last_entry_id).length;

         // Combine Splitted Records by Removing splitted records and creating one record
         if(same_entries > 1) {
            var item_value = 0;
            var item_total = 0;
            var item_reference = "";
            var item_date = "";
            $("tr."+last_entry_id).each(function() {
               balance_type_split = $(this).attr('class').split(' ')[1];
               item_date = $(this).find('.entry_date').html();
               item_reference = $(this).find('.entry_reference').html();

               if(balance_type_split == "dr_entry") {
                  item_value = $(this).find('.debit_amount').html();
               } else if(balance_type_split == "cr_entry") {
                  item_value = $(this).find('.credit_amount').html();
               }

               item_total += parseFloat(item_value.replace(new RegExp(',', 'g'), ''));
               $(this).remove();
            });

            var arr = last_entry_id.split('_');
            var single_entry_id = arr[2];

            if(balance_type_split == "dr_entry") {
               add_entry_to_contra(single_entry_id, item_date, "dr_entry", item_reference, item_total, "", "1");
            } else if(balance_type_split == "cr_entry") {
               add_entry_to_contra(single_entry_id, item_date, "cr_entry", item_reference, "", item_total, "1");
            }

         }
         // end

         // add credit settled row
         add_entry_to_contra(entry_id, entry_date, "cr_entry", entry_reference, "", (settled_amount * -1), "1");

         // add credit un-settled row
         add_entry_to_contra(entry_id, entry_date, "cr_entry", entry_reference, "", un_settled_amount, "0");
      }

      $('#contra_'+entry_id).prop('disabled', true);
      $('#contra_'+entry_id).parents("tr").find(".checkmark").addClass("disabled");

      calculate_final_balance();
   }

   function add_entry_to_contra(entry_id, entry_date, entry_type, entry_reference, debit_amount, credit_amount, display) {
      var entry_data = '';

      var display;
      if(display == "0") {
         display = 'style="display: none"';
      } else {
         display = '';
      }

      entry_data += '<tr id="contra_entry_' + entry_id +'" class="contra_entry_' + entry_id + ' ' + entry_type + '" '+display+'>';
      entry_data += '<td class="entry_date">' + entry_date + '</td>';
      entry_data += '<td class="entry_reference">' + entry_reference + '</td>';

      if(debit_amount !== "") {
         debit_amount = Number(debit_amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
      } else {
         debit_amount = "";
      }
      entry_data += '<td class="debit_amount">' + debit_amount + '</td>';

      if(credit_amount !== "") {
         credit_amount = Number((-1) * credit_amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
      } else {
         credit_amount = "";
      }

      entry_data += '<td class="credit_amount">' + credit_amount + '</td>';
      entry_data += '<td style="display: none" class="entry_balance">' + 0.00 + '</td>';
      entry_data += '</tr>';

      $('#tbl_contra tbody').append(entry_data);
   }

   function calculate_final_balance() {
      var debit_entries_total = 0;
      var credit_entries_total = 0;
      var final_balance = 0;

      $('tr.dr_entry').each(function() {
         dr_amount = $(this).find(".debit_amount").text();
         debit_entries_total += parseFloat(dr_amount.replace(new RegExp(',', 'g'), ''));
      });

      $('tr.cr_entry').each(function() {
         cr_amount = $(this).find(".credit_amount").text();
         credit_entries_total += parseFloat(cr_amount.replace(new RegExp(',', 'g'), ''));
      });

      final_balance = debit_entries_total - credit_entries_total;

      // hidden field
      $('#final_balance_total').val(final_balance);
      if(final_balance < 0) {
         $('#final_balance_total').val((-1) * final_balance);
      }

      $('#balance_entry_amount').html(final_balance.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

      if(final_balance < 0) {
         $('#final_balance_amount_display').text("(" + (-1 * final_balance).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ")");
      } else {
         $('#final_balance_amount_display').text(final_balance.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
      }

      if (final_balance == 0) {
         $('#final_balance_entry_id').val("");
         $('#final_balance_entry_reference').val("");
         $('#balance_entry_reference').text("BALANCE");

         //$('#tbl_debits').hide();
         //$('#tbl_credits').hide();
         enable_tbl("dr_cr");

      } else if (final_balance > 0) {
         //$('#tbl_debits').hide();
         //$('#tbl_credits').show();
         enable_tbl("cr");

         // this is to split only entry which has balance when inserting into AR TBL
         var last_entry_id = $('#tbl_contra tbody tr:last').attr('id');
         var arr = last_entry_id.split('_');
         var ar_id = arr[2];
         $('#final_balance_entry_id').val(ar_id);

         var reference = $('#tbl_contra tbody tr:last').find('.entry_reference').html();
         $('#final_balance_entry_reference').val(reference);
         $('#balance_entry_reference').text(reference);

      } else if (final_balance < 0) {

         //$('#tbl_credits').hide();
         //$('#tbl_debits').show();
         enable_tbl("dr");

         // this is to split only entry which has balance when inserting into AR TBL
         var last_entry_id = $('#tbl_contra tbody tr:last').attr('id');
         var arr = last_entry_id.split('_');
         var ar_id = arr[2];
         $('#final_balance_entry_id').val(ar_id);

         var reference = $('#tbl_contra tbody tr:last').find('.entry_reference').html();
         $('#final_balance_entry_reference').val(reference);
         $('#balance_entry_reference').text(reference);
      }

      // Balance column
      var balance = 0;
      $("#tbl_contra tbody tr").each(function() {
         debit_value = 0;
         credit_value = 0;
         balance_display = "";

         if ($(this).hasClass('dr_entry')) {
            debit_value = $(this).find("td:eq(2)").text();
            balance += parseFloat(debit_value.replace(new RegExp(',', 'g'), ''));
         }

         if ($(this).hasClass('cr_entry')) {
            credit_value = $(this).find("td:eq(3)").text();
            balance -= parseFloat(credit_value.replace(new RegExp(',', 'g'), ''));
         }

         if(balance < 0) {
            balance_display = (-1) * balance;
            $(this).find("td:eq(4)").text("(" + balance_display.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ")");
         } else {
            balance_display = balance;
            $(this).find("td:eq(4)").text(balance_display.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
         }
      })
   }

   function add_receipt_entry() {
      // get reference
      var ref_parts = ref_no.split('.');
      var entry_id = ref_parts[1];
      var entry_amount = $('#amount').val();
      var entry_date = $('#created_on').val();

      add_entry_to_contra(entry_id, entry_date, "cr_entry", ref_no, "", ((-1) * entry_amount), "1");
   }

   function reset_contra() {
      contra_transaction_ids = [];
      $("#tbl_contra > tbody").html("");

      add_receipt_entry();

      $('#tbl_contra').show();

      $('.entry_check').prop('disabled', false);
      $('.entry_check').parents("tr").find(".checkmark").removeClass("disabled");
      $('.entry_check').prop('checked', false);
      $("#tbl_debits").show();
      $("#tbl_credits").show();
      $('#btn_reset_contra').hide();

      calculate_final_balance();
   }
</script>
