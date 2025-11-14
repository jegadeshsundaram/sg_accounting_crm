<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Petty Cash</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Petty Cash</li>
               <li class="breadcrumb-item">Edit</li>
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
            
            <input type="hidden" id="gst_selected_row" value="0" />

            <!-- form - starts -->
            <form autocomplete="off" id="form_" method="post">

               <div class="card card-default">
                  <div class="card-header">
                     <span class="float-left">Edit Voucher</span>
                     <a href="/petty_cash/listing" class="btn btn-info btn-sm float-right">Back</a>
                  </div>
                  <div class="card-body">

                     <div class="row">
                        <div class="col-md-5" style="margin-top: 20px">
                           <div class="form-group row">
                              <label for="pay_to" class="col-md-4 control-label">Pay to : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text"
                                    id="pay_to" name="pay_to"
                                    value="<?php echo $pay_to; ?>"
                                    class="form-control w-300" required />
                              </div>
                           </div>
                        </div>

                        <div class="col-md-3" style="margin-top: 20px">
                           <div class="form-group row">
                              <label for="pay_to" class="col-md-4 control-label">Voucher # : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text"
                                    id="document_reference" name="document_reference"
                                    value="<?php echo $document_reference; ?>" readonly
                                    class="form-control w-150" />
                              </div>
                           </div>
                        </div>

                        <div class="col-md-4" style="margin-top: 20px">
                           <div class="form-group row">
                              <label for="document_date" class="col-md-4 control-label">Date : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text"
                                    id="document_date" name="document_date"
                                    class="form-control dp_full_date w-120" 
                                    placeholder="dd-mm-yyyy"                                 
                                    value="<?php echo $document_date; ?>" required  />
                              </div>
                           </div>
                        </div>
                     </div>
                     <br /><br />
                     <div class="row">
                        <div class="col-md-12 table-responsive">
                           <table id="tbl_items" class="table table-custom" style="min-width: 1400px; width: 100%;">
                              <thead>
                                 <tr>
                                    <th class="w-350">Chart Of Account</th>
                                    <th class="w-350">Control Account</th>
                                    <th class="w-200"><?php echo $this->custom->getDefaultCurrency(); ?> $</th>
                                    <th class="w-350">Remarks</th>
                                    <th class="w-80"></th>
                                 </tr>
                              </thead>
                              <tbody>
                              <?php
                              $i = 0;
               $table = 'petty_cash_batch';
               $columns = '*';
               $where = ['ref_no' => $document_reference, 'pay_to' => $pay_to];
               $group_by = null;
               $order_by = 'doc_date';
               $query = $this->pc_model->get_tbl_data($table, $columns, $where, $group_by, $order_by);
               $list = $query->result();
               foreach ($list as $record) {
                   $coa_list = $this->custom->populateCOAByCode($record->accn);
                   if ($record->accn == 'CA001') {
                       $customer_list = $this->custom->createDropdownSelect('master_customer', ['code', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1], ['code' => $record->iden]);
                       $supplier_list = $this->custom->createDropdownSelect('master_supplier', ['code', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1]);
                   } elseif ($record->accn == 'CL001') {
                       $customer_list = $this->custom->createDropdownSelect('master_customer', ['code', 'name', 'code', 'currency_id'], 'Customer', ['( ', ') ', ''], ['active' => 1]);
                       $supplier_list = $this->custom->createDropdownSelect('master_supplier', ['code', 'name', 'code', 'currency_id'], 'Supplier', ['( ', ') ', ''], ['active' => 1], ['code' => $record->iden]);
                   }
                   ?>
                  <tr id="<?php echo $i; ?>">
                     <td>
                        <!-- Field : Entry Unique ID from DB -->
                        <input 
                           type="hidden"
                           id="batch_entry_id_<?php echo $i; ?>" name="batch_entry_id[]" 
                           value="<?php echo $record->pcb_id; ?>" />
                        
                        <!-- Field : Chart of Account -->
                        <select id="coa_<?php echo $i; ?>" name='coa[]' class="form-control coa">'
                           <?php echo $coa_list; ?>
                        </select>
                     </td>
                     
                     <td>
                        <div id="customer_ddm_<?php echo $i; ?>" style="display: <?php echo $record->accn == 'CA001' ? 'block' : 'none'; ?>">
                           <select id="customer_<?php echo $i; ?>" class="form-control customer" >
                              <?php echo $customer_list; ?>'
                           </select>
                        </div>

                        <div id="supplier_ddm_<?php echo $i; ?>" style="display: <?php echo $record->accn == 'CL001' ? 'block' : 'none'; ?>">
                           <select id="supplier_<?php echo $i; ?>" class="form-control supplier">
                              <?php echo $supplier_list; ?>'
                           </select>
                        </div>

                        <input 
                           type="hidden" 
                           id="iden_<?php echo $i; ?>" name="iden[]" 
                           value="<?php echo $record->iden; ?>" />
                     </td>
                     
                     <td>
                        <input 
                           type="number"
                           id="amount_<?php echo $i; ?>" name="amount[]" 
                           value="<?php echo $record->amount; ?>"
                           class="form-control amount" />
                     </td>
               
                     <td>
                        <input 
                           type="text" 
                           id="remarks_<?php echo $i; ?>" name="remarks[]" 
                           class="form-control remarks" 
                           value="<?php echo $record->remarks; ?>"
                           maxlength="250" />
                     </td>
                     
                     <td>
                        <input id="gst_type_<?php echo $i; ?>" name="gst_type[]" type="hidden" value="<?php echo $record->gst_type; ?>" />
                        <input id="gst_category_<?php echo $i; ?>" name="gst_category[]" type="hidden" value="<?php echo $record->gst_category; ?>" />
                        <input id="net_amount_<?php echo $i; ?>" name="net_amount[]" type="hidden" value="<?php echo $record->net_amount; ?>" />
                        <input id="gst_amount_<?php echo $i; ?>" name="gst_amount[]" type="hidden" value="<?php echo $record->gst_amount; ?>" />
                        
                        <button type="button" class="btn btn-outline-danger btn-sm btn_delete_row float-right"><i class="fa fa-trash"></i></button>
                     </td>
                  </tr>
                                 <?php
                                              ++$i;
               }
               ?>
                              </tbody>
                              <tfoot>
                                 <tr>
                                    <td style="border-top: 1px solid gainsboro; padding: .2rem 1rem">                                      
                                       <a href="#" class="btn_add_entry btn btn-success btn-sm"><i class="fa-solid fa-plus"></i> Add Entry</a>
                                    </td>
                                    <td style="text-align: right">Subtotal</td>
                                    <td>
                                       <input type="number" id="grand_total" class="form-control" value="0.00" readonly>
                                    </td>
                                    <td></td>
                                    <td></td>
                                 </tr>
                              </tfoot>
                           </table>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-6" style="margin-top: 20px">
                           <div class="form-group row">
                              <label for="received_by" class="col-md-4 control-label">Received By : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text"
                                    id="received_by" name="received_by"
                                    value="<?php echo $received_by; ?>"
                                    class="form-control w-300" required />
                              </div>
                           </div>
                        </div>

                        <div class="col-md-6" style="margin-top: 20px">
                           <div class="form-group row">
                              <label for="approved_by" class="col-md-4 control-label">Approved By : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text"
                                    id="approved_by" name="approved_by"
                                    value="<?php echo $approved_by; ?>"
                                    class="form-control w-300" required />
                              </div>
                           </div>
                        </div>
                     </div>

                  </div>
                  <div class="card-footer">
                     <a href="/petty_cash" class="btn btn-info btn-sm">Cancel</a>
                     <button type="button" id="btn_print" class="btn btn-secondary btn-sm">Print</button>
                     <button type="button" id="btn_submit" class="btn btn-warning btn-sm float-right">Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!--
  DEBIT GST (CL300) ACCOUNT
  1. INPUT TAX
-->

<div id="debitGSTOptionsModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="modal-header">
            <h3 class="modal-title" style="text-align: center">CHOOSE OPTION</h3>
         </div>
         <div class="modal-body">
            <div id="debit_gst_buttons" style="text-align: center">
               <button type="button" id="debit_gst_input_btn" class="btn bg-purple" style="width: 250px; margin: 10px;">INPUT TAX</button>
               <button type="button" id="debit_gst_abort_btn" class="gst_abort_btn btn btn-danger" style="width: 250px; margin: 10px; background: dimgray;">ABORT</button>
            </div>

            <!-- on clicking "Input Tax button", the below section will be displayed -->
            <div id="debit_gst_input_section" style="display: none; clear: both">
               <div style="padding: 10px;">
                  <label>Net Purchase $</label><br />
                  <input type="number" style="width: 130px" id="debit_gst_input_purchase_amount" name="debit_gst_input_purchase_amount" min="1" max="999999" class="form-control" />
               </div>

               <div style="padding: 10px;">
                  <label>GST Input Category</label><br />
                  <select id="debit_gst_input_category" name="debit_gst_input_category" class="form-control select2">
                     <?php echo $gst_input_category; ?>
                  </select>
                  <input type="hidden" name="debit_gst_input_rate" id="debit_gst_input_rate" value="<?php echo $std_gst_rate; ?>" />
               </div>

               <div style="padding: 10px;">
                  <label>GST Amount</label><br />
                  <input type="number" style="width: 110px" id="debit_gst_input_gst_amount" name="debit_gst_input_gst_amount" min="1" max="999999" class="form-control" />
               </div>

               <div style="padding: 10px; text-align: center">
                  <button type="button" id="debit_gst_input_submit_btn" class="btn btn-success">Confirm GST Amount</button>
                  <button type="button" id="debit_gst_input_cancel_btn" class="btn btn-danger">Cancel</button>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style type="text/css">
   .select2 {
      width: 350px !important;
   }
</style>

<script type="text/javascript">
   var row_number = 0;
   $(function() {

      update_grand_total();

      $("select").select2();
      $("#debit_gst_input_category").select2();

      $(document).on("change", "#document_date", function() {
         $('#document_date-error').hide();
      });

      $(document).on('change', '.coa', function() {
         var accn = $('option:selected', this).val();
         row_number = $(this).closest('tr').attr("id");

         $("#amount_"+row_number).val("");
         $("#remarks_"+row_number).val("");

         if(accn !== "") {
            if(accn == "CA001") {
               $("#supplier_"+row_number).val("");
               $("#supplier_ddm_"+row_number).hide();
               
               $("#customer_"+row_number).val("");
               $("#customer_"+row_number).select2();
               $("#customer_ddm_"+row_number).show();
               $("#customer_"+row_number).select2('open');

            } else if(accn == "CL001") {            
               $("#customer_"+row_number).val("");
               $("#customer_ddm_"+row_number).hide();

               $("#supplier_"+row_number).val("");
               $("#supplier_"+row_number).select2();
               $("#supplier_ddm_"+row_number).show();
               $("#supplier_"+row_number).select2('open');

            } else if(accn == "CL300") {
               $("#customer_ddm_"+row_number).hide();
               $("#supplier_ddm_"+row_number).hide();

               $('#gst_selected_row').val(row_number);
               $('#debit_gst_buttons').show();
               $('#debit_gst_input_section').hide();
               clearDebitGSTInputs();
               $('#debitGSTOptionsModal h3').html("CHOOSE OPTION");
               $('#debitGSTOptionsModal').modal();

            } else {
               $("#customer_ddm_"+row_number).hide();
               $("#supplier_ddm_"+row_number).hide();

               $("#amount_"+row_number).focus();
            }
         } else {
            $("#customer_ddm_"+row_number).hide();
            $("#supplier_ddm_"+row_number).hide();

            $("#amount_"+row_number).val("");
         }
      });

      $(document).on('change', '.customer, .supplier', function() {
         var iden = $('option:selected', this).val();
         row_number = $(this).closest('tr').attr("id");

         if(iden !== "") {
            $("#iden_"+row_number).val(iden);
            $("#amount_"+row_number).focus();
         }
      });

      $(document).on("keyup", ".amount", function(e) {
         if($.trim($(this).val()) !== "") {
            var grand_total = 0;

            $('.amount').each(function () {
               var entry_amount = $(this).val();
               grand_total += Number(entry_amount);
            });

            $('#grand_total').val(grand_total.toFixed(2));

         } else {           
            $(this).focus();
         }
      });

      $(document).on("change", ".remarks", function(e) {
         if($.trim($(this).val()) !== "") {
            row_number = $(this).closest('tr').attr("id");
            save_entry(row_number);
         }
      });

      $(document).on("change", ".amount", function(e) {
         if($.trim($(this).val()) !== "") {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);

            row_number = $(this).closest('tr').attr("id");

            save_entry(row_number);
         }
      });
    

      // Debit GST Options and Sections - Start
      // 1. Input Tax
      $("#debit_gst_input_btn").click(function() {
         $('#debitGSTOptionsModal h3').html("Debit GST Account <br /><span style='color: red; font-size: 14px'>(Input Tax)</span>");
         $("#debit_gst_buttons").hide();
         $("#debit_gst_input_section").show();

         clearDebitGSTInputs();
      });

      // 2. Settlement Tax - Removed

      // 3. Abort
      $("#debit_gst_abort_btn").click(function() {
         var current_row = $('#gst_selected_row').val();
         $('#debitGSTOptionsModal').modal('hide');
         $("#coa_"+current_row).val("");
         $("#coa_"+current_row).val(null).trigger("change");
         $("#coa_"+current_row).select2('open');
      });

      $(document).on('change', '#debit_gst_input_category', function() {
         if($('#debit_gst_input_purchase_amount').val() !== "") {
            var gst_id = $('option:selected', this).val();
            var gst_text = $('option:selected', this).text();
            $('#debit_gst_input_rate').val(0);
            $.post('/petty_cash/ajax/get_gst_details', {
               gst_id: gst_id
            }, function (data) {
               if(data !== "") {
                  var obj = $.parseJSON(data);
                  $('#debit_gst_input_rate').val(Number(obj.gst_percentage));
                  get_gst_amount($('#debit_gst_input_purchase_amount'), $('#debit_gst_input_rate'), $('#debit_gst_input_gst_amount'));
                  $('#debit_gst_input_gst_amount').focus();
               }
            });
         } else {
            $('#debit_gst_input_purchase_amount').focus();
         }
      });

      $(document).on('change', '#debit_gst_input_purchase_amount', function() {
         if($(this).val() !== "" && $('#debit_gst_input_category').val() !== "") {
            get_gst_amount($(this), $('#debit_gst_input_rate'), $('#debit_gst_input_gst_amount'));
         }
      });

      // 1. Debit GST - Input Tax Submit
      $("#debit_gst_input_submit_btn").click(function() {
         var amount = $('#debit_gst_input_purchase_amount').val();
         var gst_category = $('#debit_gst_input_category').val();
         var gst_amount = $('#debit_gst_input_gst_amount').val();     

         if(amount == "") {
            $('#debit_gst_input_purchase_amount').focus();
         } else if(gst_category == "") {
            $("#debit_gst_input_category").select2('open');
         } else if(gst_amount == "") {
            $('#debit_gst_input_gst_amount').focus();
         } else {
            $('#debitGSTOptionsModal').modal('hide');
            setGSTValues(gst_type = "I", gst_category = gst_category, net_amount = amount, gst_amount = gst_amount);
         }
      });    

      // 2. Debit GST - Input Tax Cancel
      $("#debit_gst_input_cancel_btn").click(function() {
         $('#debitGSTOptionsModal h3').html("CHOOSE OPTION");
         $("#debit_gst_reverse_output_section").hide();
         $("#debit_gst_input_section").hide();
         $("#debit_gst_buttons").show();
      });
      // Debit GST Options and Sections - End

      // delete item
      var delete_row_id = -1;
      $(document).on('click', '.btn_delete_row', function() {

         var rowCount = $('#tbl_items tbody tr').length;

         if(rowCount == 1) {
            alert("First row can not be deleted");
            return;
         }

         delete_row_id = $(this).closest('tr').attr("id");
         $('#confirmDeleteModal .modal-body').html("Click 'Yes' to delete the current item");
         $("#confirmDeleteModal").modal();
      });

      // item delete = YES
      $('#btn-confirm-delete-yes').click(function() {

         var id = $("#batch_entry_id_"+delete_row_id).val();
         if(id !== "") {
            delete_entry(id);
         }

         $('tr#'+delete_row_id).remove();

         $("#confirmDeleteModal").modal('hide');
      });

      $("#btn_print").on('click', function() {
         var entries = $('#tbl_items tbody tr').length;
         if(entries > 0) {
            $("#form_").attr("target", "_blank");
            $("#form_").attr("action", '/petty_cash/print_stage_1');
            $("#form_").submit();
         }
		});

      // btn - add new item 
      $(document).on('click', '.btn_add_entry', function() {
         
         if(!$('#form_').valid()) {
            //return;
         }
         
         if(!validate()) {
            return;
         }

         $tr = $(this).closest('table').find('tbody tr:last');

         $last_entry_id = $tr.attr('id');
         
         var allTrs = $tr.closest('table').find('tbody tr');
         var lastTr = allTrs[allTrs.length-1];
         $(lastTr).find('select').select2("destroy"); // remove select 2 before clone tr
         $new_row = $(lastTr).clone();

         // Input & Select fields id attribute value increment
         $new_row.find('input, select, button, textarea, div').each(function() {
            var id = $(this).attr('id') || null;

            if(id) {
               // Get id number from ID text
               // Ex: unit_price_123 :: last_record_number = 123
               var last_record_number = id.split("_").pop();
               console.log(">>> last_record_number" + last_record_number);
               var prefix = id.substr(0, (id.length-(last_record_number.length)));
               $(this).attr('id', prefix+(+last_record_number+1));
            }
         });      

         // empty all the input values in new row
         $new_row.find('input').val('');
         $new_row.find('select').val('');

         // Table row Id number increment
         $last_no = $new_row.attr('id');
         $new_row.attr('id', parseInt($last_no) + 1);

         // append new row to the table
         $tr.closest('table').append($new_row);

         // add select2 again

         $(lastTr).find('.coa').select2();

         console.log(">>$last_entry_id>>"+$last_entry_id);

         if($('#coa_'+$last_entry_id).val() == "CA001") {
            $("#customer_"+$last_entry_id).select2();
         } else if($('#coa_'+$last_entry_id).val() == "CL001") {
            $("#supplier_"+$last_entry_id).select2();
         }
         
         $new_row.find('.coa').select2();

         // set current row number to public variable
         processing_row_number = $new_row.attr('id');

         $('#customer_'+processing_row_number).hide();
         $('#supplier_'+processing_row_number).hide();

         // Handler for .ready() called.
         $('html, body').animate({
            scrollTop: $('#'+processing_row_number).offset().top
         }, 'slow');
      });

      // submit
      $("#btn_submit").on('click', function (e) {

         if(!$('#form_').valid()) {
            return;
         }

         if(!validate()) {
            return;
         }

         $("#form_").attr("target", "_self");
         $("#form_").attr("action", '/petty_cash/save');
         $('#form_').submit();
      });


   }); // document ends

   function save_entry(row_number) {
      var pay_to = $("#pay_to").val();
      var document_date = $("#document_date").val();
      var document_reference = $("#document_reference").val();
      var received_by = $("#received_by").val();
      var approved_by = $("#approved_by").val();

      var coa = $("#coa_"+row_number).val();
      var amount = $("#amount_"+row_number).val();
      var remarks = $("#remarks_"+row_number).val();

      var batch_entry_id = $("#batch_entry_id_"+row_number).val();

      if(pay_to !== "" && document_date !== "" && document_reference !== "" && coa !== "" && amount !== "") {
         $.post('/petty_cash/ajax/save_entry', {
            batch_entry_id: batch_entry_id,
            pay_to: pay_to,
            document_date: document_date,
            document_reference: document_reference,
            coa: coa,
            amount: amount,
            remarks: remarks,
            gst_type: $("#gst_type_"+row_number).val(),
            gst_category: $("#gst_category_"+row_number).val(),
            net_amount: $("#net_amount_"+row_number).val(),
            gst_amount: $("#gst_amount_"+row_number).val(),
            iden: $("#iden_"+row_number).val(),
            received_by: received_by,
            approved_by: approved_by
         }, function(data) {
            var obj = $.parseJSON(data);
            if(obj.batch_entry_id !== "") {
               $("#batch_entry_id_"+row_number).val(obj.batch_entry_id);
            }
         });
      }
   }

   function clearDebitGSTInputs() {
      $("#debit_gst_input_purchase_amount").val("");
      $("#debit_gst_input_category").val("TX").trigger("change");
      $("#debit_gst_input_gst_amount").val("");
   }

   function get_gst_amount(amount, gst_rate, gst_amount) {
      if($(amount).val() !== "" && $(gst_rate).val() !== "") {
         $(gst_amount).val(Math.round($(amount).val() * $(gst_rate).val()) / 100);
      } else {
         $(gst_amount).val(0);
      }
   }

   function setGSTValues(gst_type, gst_category, net_amount, gst_amount) {
      var current_row = $('#gst_selected_row').val();
      $("#gst_type_"+current_row).val(gst_type);
      $("#gst_category_"+current_row).val(gst_category);
      $("#net_amount_"+current_row).val(net_amount);
      $("#gst_amount_"+current_row).val(gst_amount);

      $("#amount_"+current_row).val(gst_amount);

      save_entry(current_row);

      update_grand_total();

      $("#amount_"+current_row).focus();
   }

   function update_grand_total() {
      var amount_total = 0;
      $('.amount').each(function () {
         var specific_amount = $(this).val();
         amount_total += Number(specific_amount);
         console.log(">>>> "+amount_total);
      });
      $('#grand_total').val(amount_total.toFixed(2));
   }

   function validate() {
      var error = [];
      var i = 0;
      var valid = true;
      var row_number = 0;

      $("#tbl_items tbody tr").each(function (i, val) {
         row_number = $(this).attr("id");

         if($("#coa_"+row_number).val() == "") {
            error[i++] = "COA";
            valid = false;
         }

         if($("#amount_"+row_number).val() == "") {
            error[i++] = "Amount";
            valid = false;
         }         

      });

      return valid;
   }
  
   function delete_entry(id) {
      $.post('/petty_cash/ajax/delete_entry', {
         entry_id: id
      }, function(data) {
         var obj = $.parseJSON(data);
         if(obj.deleted !== "") {
            console.log("Auto saved batch entry deleted. ID :: "+id);
         }
      });
   }  
</script>
