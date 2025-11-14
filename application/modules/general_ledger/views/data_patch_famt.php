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
               <li class="breadcrumb-item">Data Patch</li>
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
            <!-- Page - hidden field --> 
            <input type="hidden" id="page" value="new" />

            <form autocomplete="off" id="form_" method="post" action="<?php echo $save_url; ?>">
               <div class="card card-default">
                  <div class="card-header">
                     <h5>Data Patch</h5>
                     <a href="/general_ledger/" class="btn btn-outline-dark btn-sm float-right">
                        <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                     </a>
                  </div>
                  <div class="card-body">

                     <div class="row">
                        <div class="col-6">
                           <div class="row form-group">
                              <label for="doc_date" class="control-label col-md-4">Date : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    id="doc_date" name="doc_date" 
                                    value="<?php echo date('d-m-Y', strtotime($doc_date)); ?>"
                                    class="form-control dp_full_date doc_date w-150" 
                                    placeholder="dd-mm-yyyy" required />
                              </div>
                           </div>
                        </div>

                        <div class="col-6">
                           <div class="row form-group">
                              <label for="ref_no" class="control-label col-md-4">Reference : </label><br />
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    id="ref_no" name="ref_no" 
                                    value="<?php echo $ref_no; ?>"
                                    class="form-control ref_no w-150" 
                                    maxlength="12" readonly />
                                 
                                    <input type="hidden" id="original_ref_no" value="<?php echo $ref_no; ?>" />
                                    <span id="ref_error" style="display: none; color: red;">Duplicate reference disallowed</span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <?php if ($tran_type !== 'EZPAY') { ?>
                           <div class="col-6">
                              <div class="row form-group">
                                 <label for="iden" class="control-label col-md-4">IDEN : </label><br />
                                 <div class="col-md-8">
                                    <select name="iden" id="iden" class="form_control">
                                       <?php echo $idens; ?>
                                    </select>
                                 </div>
                              </div>
                           </div>
                        <?php } ?>

                        <div class="col-6">  
                           <div class="row form-group">
                              <label for="remarks" class="control-label col-md-4">Remarks : </label><br />
                              <div class="col-md-8">
                                 <textarea id="remarks" name="remarks" class="form-control" maxlength="250"><?php echo $remarks; ?></textarea>
                                 <input type="hidden" id="tran_type" name="tran_type" value="<?php echo $tran_type; ?>" />
                              </div>
                           </div>
                        </div>
                     </div>

                     <br />
                     <div class="row form-group">
                        <div class="col-md-12 table-responsive">
                           <table id="tbl_" class="table" style="min-width: 1100px; width: 100%;">
                              <thead>
                                 <tr>
                                    <th class="w-80">Action</th>
                                    <th>Account</th>
                                    <th class="w-150 txt-right">Debit Foreign $</th>
                                    <th class="w-150 txt-right">Debit <?php echo $company_currency; ?> $</th>
                                    <th class="w-150 txt-right">Credit Foreign $</th>
                                    <th class="w-150 txt-right">Credit <?php echo $company_currency; ?> $</th>
                                 </tr>
                              </thead>
                              <tbody>
                              <?php
                                 $i = 0;
                                 if ($iden !== '') {
                                    $gl_data = $this->custom->getRows('gl', ['ref_no' => $ref_no, 'iden' => $iden]);
                                 } else {
                                    $gl_data = $this->custom->getRows('gl', ['ref_no' => $ref_no]);
                                 }
                                 foreach ($gl_data as $value) {
                                    $currency = 'SGD';
                                    
                                    $foreign_amount = $value->total_amount;
                                    $local_amount = $value->total_amount;
                                    $currency = $currency;

                                    // AR - Customer
                                    if ($value->accn == 'CA001') {
                                       $ar_data = $this->custom->getSingleRow('accounts_receivable', ['doc_ref_no' => $value->ref_no, 'doc_date' => $value->doc_date]);

                                       $foreign_amount = $ar_data->f_amt;
                                       $local_amount = $ar_data->total_amt;
                                       $currency = $ar_data->currency;

                                       // AP - Supplier
                                    } elseif ($value->accn == 'CL001') {
                                       if ($iden !== '') {
                                          $ap_data = $this->custom->getSingleRow('accounts_payable', ['doc_ref_no' => $value->ref_no, 'supplier_code' => $iden, 'doc_date' => $value->doc_date]);
                                       } else {
                                          $ap_data = $this->custom->getSingleRow('accounts_payable', ['doc_ref_no' => $value->ref_no, 'doc_date' => $value->doc_date]);
                                       }

                                       $foreign_amount = $ap_data->fa_amt;
                                       $local_amount = $ap_data->total_amt;
                                       $currency = $ap_data->currency;

                                       // FB - Foreign bank
                                    } elseif ($value->accn == 'CA110') {
                                       $fb_sb_data = $this->custom->getSingleRow('foreign_bank', ['doc_ref_no' => $value->ref_no, 'doc_date' => $value->doc_date]);

                                       $foreign_amount = $fb_sb_data->fa_amt;
                                       $local_amount = $fb_sb_data->local_amt;
                                       $currency = $fb_sb_data->currency;

                                    }

                                    $currency_rate = $this->custom->getSingleValue('ct_currency', 'rate', ['code' => $currency]);
                                    ++$i;
                                 ?>

                                 <tr id="<?php echo $i; ?>">
                                    <td style="width: 50px">
                                       <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>

                                       <!-- Hidden Fields -->
                                       <input type="hidden" id="gl_id_<?php echo $i; ?>" name="gl_id[]" value="<?php echo $currency; ?>" class="gl_id" />
                                       <input type="hidden" id="currency_<?php echo $i; ?>" name="currency[]" value="<?php echo $currency; ?>" class="currency" />
                                       <input type="hidden" id="currency_rate_<?php echo $i; ?>" name="currency_rate[]" value="<?php echo $currency_rate; ?>" class="currency_rate" />
                                       <input type="hidden" id="sign_<?php echo $i; ?>" name="sign[]" value="<?php echo $value->sign; ?>" class="sign" />
                                       <!-- Hidden Fields -->
                                    </td>

                                    <td>
                                       <?php $accn_desc = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]); ?>                                       
                                       <input 
                                          type="text" 
                                          id="coa_desc_<?php echo $i; ?>" class="form-control-dsply coa_desc" 
                                          value="<?php echo $value->accn.' : '.$accn_desc; ?>" readonly />
                                       
                                       <input 
                                          type="hidden" 
                                          id="coa_<?php echo $i; ?>" name="coa[]" 
                                          value="<?php echo $value->accn; ?>" class="coa" />
                                    </td>                                    

                                    <td>
                                       <input 
                                          type="number" 
                                          id="debit_foreign_amount_<?php echo $i; ?>" name="debit_foreign_amount[]" 
                                          value="<?php echo $value->sign == '+' ? $foreign_amount : ''; ?>"
                                          class="form-control-dsply txt-right debit_foreign_amount" readonly />
                                    </td>

                                    <td>
                                       <input 
                                          type="number" 
                                          id="debit_local_amount_<?php echo $i; ?>" name="debit_local_amount[]" 
                                          value="<?php echo $value->sign == '+' ? $local_amount : ''; ?>"
                                          class="form-control-dsply txt-right debit_local_amount" readonly />
                                    </td>

                                    <td>
                                       <input 
                                          type="number" 
                                          id="credit_foreign_amount_<?php echo $i; ?>" name="credit_foreign_amount[]" 
                                          value="<?php echo $value->sign == '-' ? $foreign_amount : ''; ?>"
                                          class="form-control-dsply txt-right credit_foreign_amount" readonly />
                                    </td>

                                    <td>
                                       <input 
                                          type="number" 
                                          id="credit_local_amount_<?php echo $i; ?>" name="credit_local_amount[]" 
                                          value="<?php echo $value->sign == '-' ? $local_amount : ''; ?>"
                                          class="form-control-dsply txt-right credit_local_amount" readonly />
                                    </td>
                                 </tr>

                                 <?php ++$i; } ?>
                              </tbody>

                              <tfoot>
                                 <tr>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: right; color: blue;">Sub Total</td>
                                    <td style="text-align: right; font-weight: bold" id="debit_grand_total">$0.00</td>
                                    <td></td>
                                    <td style="text-align: right; font-weight: bold" id="credit_grand_total">$0.00</td>
                                 </tr>
                              </tfoot>
                           </table>
                        </div>
                     </div>

                  </div>
                  <div class="card-footer">
                     <a href="/general_ledger" class="btn btn-info btn-sm">Cancel</a>
                     <button type="button" id="btn_submit" class="btn btn-warning btn-sm float-right">Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Modal :: Entry -->
<div id="entryModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-body">
               <input type="hidden" id="gl_id" />

               <div class="row mt-10">
                  <label class="col-12 control-label">Account <span class="cl-red">*</span></label>
                  <div class="col-12">
                     <select id="coa" class="form-control">
                        <?php echo $coa_options; ?>
                     </select>
                  </div>
               </div>

               <div class="row mt-10 debit_amount_field" style="display: none">
                  <div class="col-6">
                     <label class="control-label"><span id="f_curr"></span> Amount <span class="cl-red">*</span></label>
                     <input 
                        type="number" id="debit_foreign_amount" 
                        class="form-control w-180" />
                  </div>
                  <div class="col-6">
                     <label class="control-label">SGD Amount <span class="cl-red">*</span></label>
                     <input 
                        type="number" id="debit_local_amount" 
                        class="form-control w-180" />
                  </div>
               </div>

               <div class="row mt-10 credit_amount_field" style="display: none">
                  <div class="col-6">
                     <label class="control-label"><span id="f_curr"></span> Amount <span class="cl-red">*</span></label>
                     <input 
                        type="number" id="credit_foreign_amount" 
                        class="form-control w-180" />
                  </div>
                  <div class="col-6">
                     <label class="control-label">SGD Amount <span class="cl-red">*</span></label>
                     <input 
                        type="number" id="credit_local_amount" 
                        class="form-control w-180" />
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#entryModal">Cancel</button>
               <button type="button" class="btn btn-info btn-sm float-right" id="btn_save_entry">Save</button>
            </div>
         </div>
      </div>
   </div>
</div>

<div id="amountModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <span style="margin: 0; display: block;"><span style="color: red;">INVALID!</span> Total of Debit and Credit are Not Equal</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#amountModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
         </div>
         <div class="card-body">
            <table class="table" style="width: 95%; margin: auto;">
               <tr>
                  <td style="width: 60%;" align="right">Debit Total <span style="color: red; font-weight: bold;">[SGD]</span> </td>
                  <td id="debit_total"></td>
               </tr>
               <tr>
                  <td align="right">Credit Total <span style="color: red; font-weight: bold;">[SGD]</span> </td>
                  <td id="credit_total"></td>
               </tr>
               <tr>
                  <td align="right" style="color: blue;">Total Amount Difference <span style="color: red; font-weight: bold;">[SGD]</span> </td>
                  <td style="color: blue;" id="diff_amount"></td>
               </tr>
            </table>
         </div>
         <div class="card-footer">
            <button type="button" class="btn btn-outline-dark btn-sm float-right" data-toggle="modal" data-target="#amountModal">Close</button>
         </div>
      </div>
   </div>
</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<style>
   .table td {
      vertical-align: middle;
   }
</style>

<script>
   // document starts
   $(function() {
      $('select').select2();
   
      calculate_debits();
      calculate_credits();

      var double_ref = 0;
      $(document).on("change", "#ref_no", function(e) {
         var ref_no = $(this).val();
         double_ref = 0;

         if(ref_no !== "") {

            // if page is edit and user try changing different ref and again changing to same one
            if(ref_no == $('#original_ref_no').val()) {
               double_ref = 0;
               $("#ref_error").hide();
               return false;
            }

            $.post('/gst/ajax/double_dp_ob', {
               ref_no: $("#ref_no").val(),
               tran_type: $('#tran_type').val()
            }, function(ref) {
               if (ref > 0) {
                  double_ref = 1;
                  $("#ref_error").show();
               } else {
                  double_ref = 0;
                  $("#ref_error").hide();
               }
            });
         }
      });

      // EDIT
      $(document).on('click', '.dt_edit', function () {

         row_number = $(this).closest('tr').attr('id');

         $('#gl_id').val($('#gl_id_'+row_number).val());
         
         if($('#currency').val() == "SGD") {
            
         } else {

         }

         $('#edit_id').val(row_number);
         $('#entryModal').modal('show');
      });

      $(document).on('change', '.coa_select', function() {
         var accn = $('option:selected', this).val();
         row_number = $(this).closest('tr').attr('id');

         if(accn !== "") {
            accnSelectChanges(accn, row_number);

            $("#currency_text_"+row_number).hide();
            $("#currency_text_"+row_number).text("");

            calculate_debits();
            calculate_credits();

         } else {
            if($('#sign_'+row_number).val() == "+") { // debit
               $("#dr_famt_"+row_number).val("");
               $("#debit_local_amount_"+row_number).val("");
            } else if($('#sign_'+row_number).val() == "-") { // credit
               $("#cr_famt_"+row_number).val("");
               $("#credit_local_amount_"+row_number).val("");
            }
         }
      });

      $(document).on('change', '.customer_select', function() {
         var customer = $('option:selected', this).val();
         row_number = $(this).closest('tr').attr('id');

         if(customer !== "") {
            $.post('/general_ledger/ajax/get_customer_details', {
               customer_id: customer
            }, function (data) {

               var obj = $.parseJSON(data);
               $("#currency_"+row_number).val(obj.currency);
               $("#currency_rate_"+row_number).val(obj.currency_rate);
               $("#currency_text_"+row_number).text(obj.currency);
               //$("#currency_text_"+row_number).show();

               if($('#sign_'+row_number).val() == "+") { // debit
                  
                  $("#dr_famt_"+row_number).show();
                  $("#dr_famt_"+row_number).val("");
                  $("#debit_local_amount_"+row_number).val("");

                  if(obj.currency == "SGD") {
                     $("#debit_local_amount_"+row_number).attr('readonly', 'readonly');
                  } else {                    
                     $("#debit_local_amount_"+row_number).removeAttr('readonly');
                  }

                  $("#dr_famt_"+row_number).focus();

               } else if($('#sign_'+row_number).val() == "-") {  // credit

                  $("#cr_famt_"+row_number).show();
                  $("#cr_famt_"+row_number).val("");
                  $("#credit_local_amount_"+row_number).val("");

                  if(obj.currency == "SGD") {                     
                     $("#credit_local_amount_"+row_number).attr('readonly', 'readonly');
                  } else {
                     $("#credit_local_amount_"+row_number).removeAttr('readonly');                     
                  }

                  $("#cr_famt_"+row_number).focus();

               }

               calculate_debits();
               calculate_credits();

            });
         }
      });

      $(document).on('change', '.fbank_select', function() {
         var fbank = $('option:selected', this).val();
         row_number = $(this).closest('tr').attr('id');

         if(fbank !== "") {
            $.post('/general_ledger/ajax/get_fbank_details', {
               fb_id: fbank
            }, function (data) {

               var obj = $.parseJSON(data);
               $("#currency_"+row_number).val(obj.customer_currency);
               $("#currency_rate_"+row_number).val(obj.currency_amount);
               
               $("#currency_text_"+row_number).text(obj.customer_currency);
               //$("#currency_text_"+row_number).show();

               if($('#sign_'+row_number).val() == "+") { // debit

                  $("#dr_famt_"+row_number).show();
                  $("#dr_famt_"+row_number).val("");
                  $("#debit_local_amount_"+row_number).val("");

                  if($("#currency_"+row_number).val() == "SGD") {
                     $("#debit_local_amount_"+row_number).attr('readonly', 'readonly');
                  } else {
                     $("#debit_local_amount_"+row_number).removeAttr('readonly');
                  }
                  $("#dr_famt_"+row_number).focus();

               } else if($('#sign_'+row_number).val() == "-") { // credit

                  $("#cr_famt_"+row_number).show();
                  $("#cr_famt_"+row_number).val("");
                  $("#credit_local_amount_"+row_number).val("");

                  if($("#currency_"+row_number).val() == "SGD") {
                     $("#credit_local_amount_"+row_number).attr('readonly', 'readonly');
                  } else {                     
                     $("#credit_local_amount_"+row_number).removeAttr('readonly');
                  }
                  $("#cr_famt_"+row_number).focus();
               }

               calculate_debits();
               calculate_credits();

            });
         }
      });

      $(document).on('change', '.supplier_select', function() {
         var supplier = $('option:selected', this).val();
         row_number = $(this).closest('tr').attr('id');

         if(supplier !== "") {

            $.post('/general_ledger/ajax/get_supplier_details', {
               customer_id: supplier
            }, function (data) {

               var obj = $.parseJSON(data);
               
               $("#currency_"+row_number).val(obj.currency);
               $("#currency_rate_"+row_number).val(obj.currency_rate);
               $("#currency_text_"+row_number).text(obj.currency);
               //$("#currency_text_"+row_number).show();
               
               if($('#sign_'+row_number).val() == "+") { // debit

                  $("#dr_famt_"+row_number).show();
                  $("#dr_famt_"+row_number).val("");
                  $("#debit_local_amount_"+row_number).val("");

                  if(obj.currency == "SGD") {                     
                     $("#debit_local_amount_"+row_number).attr('readonly', 'readonly');                     
                  } else {                  
                     $("#debit_local_amount_"+row_number).removeAttr('readonly');                  
                  }

                  $("#dr_famt_"+row_number).focus();

               } else if($('#sign_'+row_number).val() == "-") { // credit
         
                  $("#cr_famt_"+row_number).show();
                  $("#cr_famt_"+row_number).val("");
                  $("#credit_local_amount_"+row_number).val("");

                  if(obj.currency == "SGD") {                  
                     $("#credit_local_amount_"+row_number).attr('readonly', 'readonly');
                  } else {             
                     $("#credit_local_amount_"+row_number).removeAttr('readonly');                     
                  }
                  $("#cr_famt_"+row_number).focus();
               }

               calculate_debits();
               calculate_credits();

            });
         }
      });

      $(document).on("change", ".dr_famt, .debit_local_amount", function() {
         var amount = 0;
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);
         }
         calculate_debits();
      });

      $(document).on("change", ".cr_famt, .credit_local_amount", function() {
         var amount = 0;
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);
         }
         calculate_credits();
      });

      $(document).on("keyup", ".debit_local_amount", function() {
         if($.trim($(this).val()) !== "") {
            calculate_debits();
         }
      });

      $(document).on("keyup", ".credit_local_amount", function() {
         if($.trim($(this).val()) !== "") {
            calculate_credits();
         }
      });

      $(document).on("keyup", ".dr_famt", function() {

         row_number = $(this).closest('tr').attr('id');

         if($.trim($(this).val()) !== "") {
            var currency = $("#currency_"+row_number).val();
            var currency_rate = $("#currency_rate_"+row_number).val();
            var foreign_amount = $(this).val();
            var local_amount = 0;

            if(foreign_amount !== "" && foreign_amount !== 0) {
               foreign_amount = Number(foreign_amount);
               if(currency !== "SGD" && currency_rate !== "") {
                  currency_rate = Number(currency_rate);
                  local_amount = foreign_amount / currency_rate;
                  $("#debit_local_amount_"+row_number).val(local_amount.toFixed(2));
               } else {
                  $("#debit_local_amount_"+row_number).val(foreign_amount.toFixed(2));
               }
            }

         } else {
            $("#debit_local_amount_"+row_number).val("");
         }

         calculate_debits();
         calculate_credits();
      });

      $(document).on("keyup", ".cr_famt", function(e) {
         row_number = $(this).closest('tr').attr('id');

         if($.trim($(this).val()) !== "") {
            var currency = $("#currency_"+row_number).val();
            var currency_rate = $("#currency_rate_"+row_number).val();
            var foreign_amount = $(this).val();
            var local_amount = 0;

            if(foreign_amount !== "" && foreign_amount !== 0) {
               foreign_amount = Number(foreign_amount);
               if(currency !== "SGD" && currency_rate !== "") {
                  currency_rate = Number(currency_rate);
                  local_amount = foreign_amount / currency_rate;
                  $("#credit_local_amount_"+row_number).val(local_amount.toFixed(2));
               } else {
                  $("#credit_local_amount_"+row_number).val(foreign_amount.toFixed(2));
               }
            }

         } else {
            $("#credit_local_amount_"+row_number).val("");
         }

         calculate_debits();
         calculate_credits();

      });

      $('#confirm-entry-yes').click(function() {
         $("#confirmEntryModal").modal('hide');
         var save_url = "/general_ledger/resubmit_data_patch";
         $("#data_patch_form").attr("action", save_url);
         $("#data_patch_form").submit();
      });

      $('#modal_close').click(function() {
         $('#amountModal').modal('hide');
      });

      $('#confirm-entry-no').click(function() {
         $("#confirmEntryModal").modal('hide');
      });

      $('#error-alert-no').click(function() {
         $("#errorAlertModal").modal('hide');
      });

      $('#btn_submit').on('click', function() {         

         calculate_debit_credit_amount();

         if(double_ref == 1) {
            $('#ref_no').focus();
            return false;
         }

         var url = "/general_ledger/save_patched_data";
         $("#frm_").attr("action", url);
         $("#frm_").submit();

      });
      

   }); // document ends

   function calculate_debits() {
      var debit_total = 0;

      $('.debit_local_amount').each(function() {
         var debit_current = $(this).val();
         debit_total += Number(debit_current);
      });

      $('#debit_grand_total').html(debit_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + " <span style='color: sandybrown; font-weight: bold; display: none'>DR</span>");
   }

   function calculate_credits() {
      var credit_total = 0;
      $('.credit_local_amount').each(function (index, element) {
         var credit_current = $(this).val();
         credit_total = Number(credit_total) + Number(credit_current);
      });

      $('#credit_grand_total').html(credit_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + " <span style='color: darkseagreen; font-weight: bold; display: none'>CR</span>");

   }

   function calculate_debit_credit_amount() {
      var debit_total = 0;
      var credit_total = 0;

      $('.debit_local_amount').each(function (index, element) {
         var debit_current = $(this).val();
         debit_total = Number(debit_total) + Number(debit_current);
      });

      $('.credit_local_amount').each(function (index, element) {
         var credit_current = $(this).val();
         credit_total = Number(credit_total) + Number(credit_current);
      });

      debit_total = debit_total.toFixed(2);
      credit_total = credit_total.toFixed(2);

      console.log(">>> Total Amount of DEBIT >>> "+debit_total);
      console.log(">>> Total Amount of CREDIT >>> "+credit_total);

      if(debit_total !== credit_total) {
         $('#amountModal').modal();
         $('#debit_total').html("$"+debit_total);
         $('#credit_total').html("$"+credit_total);

         var diff_amount = Number(debit_total) - Number(credit_total);
         diff_amount = Number(diff_amount);

         $('#diff_amount').html("$"+diff_amount.toFixed(2));
         $('#diff_amount').html($('#diff_amount').html().replace('-',''));

      } else {
         $('#confirmSubmitModal .modal-title').html("Confirm ALERT");
         $('#confirmSubmitModal .modal-body').html("Patching will be automatic for AR, AP & Foreign Bank. However, If you made any changes to GST Amount, You must patch GST Datafile separetely.");
         $("#confirmSubmitModal").modal();
      }
   }   

   function accnSelectChanges(accn, row_number) {
      if(accn == "CA001") {
         $("#supplier_"+row_number).val("");
         $("#customer_"+row_number).val("");
         $("#fbank_"+row_number).val("");

         $("#supplier_"+row_number).hide();
         $("#fbank_"+row_number).hide();
         $("#customer_"+row_number).show();

         $("#supplier_"+row_number).select2().next().hide();
         $("#fbank_"+row_number).select2().next().hide();
         $("#customer_"+row_number).select2();
         $("#customer_"+row_number).select2('open');

      } else if (accn == "CA110") {

         $("#supplier_"+row_number).val("");
         $("#customer_"+row_number).val("");
         $("#fbank_"+row_number).val("");

         $("#supplier_"+row_number).hide();
         $("#customer_"+row_number).hide();
         $("#fbank_"+row_number).show();

         $("#supplier_"+row_number).select2().next().hide();
         $("#customer_"+row_number).select2().next().hide();
         $("#fbank_"+row_number).select2();
         $("#fbank_"+row_number).select2('open');

      } else if (accn == "CL001") {
         $("#supplier_"+row_number).val("");
         $("#customer_"+row_number).val("");
         $("#fbank_"+row_number).val("");

         $("#customer_"+row_number).hide();
         $("#fbank_"+row_number).hide();
         $("#supplier_"+row_number).show();

         $("#customer_"+row_number).select2().next().hide();
         $("#fbank_"+row_number).select2().next().hide();
         $("#supplier_"+row_number).select2();
         $("#supplier_"+row_number).select2('open');

      } else {
         $("#supplier_"+row_number).val("");
         $("#customer_"+row_number).val("");
         $("#fbank_"+row_number).val("");

         $("#customer_"+row_number).hide();
         $("#customer_"+row_number).select2().next().hide();
         $("#supplier_"+row_number).hide();
         $("#supplier_"+row_number).select2().next().hide();
         $("#fbank_"+row_number).hide();
         $("#fbank_"+row_number).select2().next().hide();

         if($('#sign_'+row_number).val() == "+") { // debit
            $("#dr_famt_"+row_number).val("");
            $("#debit_local_amount_"+row_number).val("");
            $("#debit_local_amount_"+row_number).focus();
         } else if($('#sign_'+row_number).val() == "-") { // credit
            $("#cr_famt_"+row_number).val("");
            $("#credit_local_amount_"+row_number).val("");
            $("#credit_local_amount_"+row_number).focus();
         }
      }
   }

   function validate() {
      var error = [];
      var i = 0;
      var valid = true;

      $("#tbl_ tbody tr").each(function (i, val) {
         row_number = $(this).attr("id");

         if($("#coa_"+row_number).val() == "") {
            valid = false;
            $("#coa_"+row_number).select2('open');
         }

         if($("#coa_"+row_number).val() == "CA001" && $("#customer_"+row_number).val() == "") {
            valid = false;
            $("#customer_"+row_number).select2('open');
         }

         if($("#coa_"+row_number).val() == "CL001" && $("#supplier_"+row_number).val() == "") {
            valid = false;
            $("#supplier_"+row_number).select2('open');
         }

         if($("#coa_"+row_number).val() == "CA110" && $("#fbank_"+row_number).val() == "") {
            valid = false;
            $("#fbank_"+row_number).select2('open');
         }

         if($("#coa_"+row_number).val() == "CA001" || $("#coa_"+row_number).val() == "CL001" || $("#coa_"+row_number).val() == "CA110") {
            if($("#sign_"+row_number).val() == "+" && $("#dr_famt_"+row_number).val() == "") {
               valid = false;
               $("#dr_famt_"+row_number).focus();
            } else if($("#sign_"+row_number).val() == "-" && $("#cr_famt_"+row_number).val() == "") {
               valid = false;
               $("#cr_famt_"+row_number).focus();
            }
         } else { // other accounts
            if($("#sign_"+row_number).val() == "+" && $("#debit_local_amount_"+row_number).val() == "") {
               valid = false;
               $("#debit_local_amount_"+row_number).focus();
            } else if($("#sign_"+row_number).val() == "-" && $("#credit_local_amount_"+row_number).val() == "") {
               valid = false;
               $("#credit_local_amount_"+row_number).focus();
            }
         }

      });

      return valid;
   }
</script>