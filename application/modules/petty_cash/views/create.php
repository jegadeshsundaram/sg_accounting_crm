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
               <li class="breadcrumb-item">Create</li>
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
            <input type="hidden" id="process" />
            <input type="hidden" id="edit_id" />
            <input type="hidden" id="redirect_url" value="/petty_cash" />

            <!-- form - starts -->
            <form autocomplete="off" id="form_" method="post">

               <div class="card card-default">
                  <div class="card-header">
                     <h5>Issue Voucher</h5>
                     <a href="/petty_cash/listing" class="btn btn-outline-dark btn-sm float-right">
                        <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                     </a>
                  </div>

                  <div class="card-body">

                     <div class="row form-group">
                        <label for="pay_to" class="col-md-3 control-label">Voucher # : </label>
                        <div class="col-md-3">
                           <input 
                              type="text"
                              id="ref_no" name="ref_no"
                              value="<?php echo $pc_voucher_number; ?>" readonly
                              class="form-control w-150" />
                        </div>
                     </div>

                     <div class="row form-group">
                        <label for="pay_to" class="col-md-3 control-label">Pay to : </label>
                        <div class="col-md-3">
                           <input 
                              type="text"
                              id="pay_to" name="pay_to"
                              class="form-control w-300" />
                        </div>
                     </div>

                     <div class="row form-group">
                        <label for="pay_to" class="col-md-3 control-label">Date : </label>
                        <div class="col-md-3">
                           <input 
                              type="text"
                              id="doc_date" name="doc_date"
                              class="form-control dp_full_date w-120" 
                              placeholder="dd-mm-yyyy" 
                              value="<?php echo date('d-m-Y'); ?>" />
                        </div>
                     </div>
                     
                     <br /><br />

                     <div class="row">
                        <div class="col-md-12 table-responsive">
                           <table id="tbl_items" class="table table-custom" style="min-width: 1400px; width: 100%; display: none">
                              <thead>
                                 <tr>
                                    <th class="w-150">Action</th>
                                    <th class="w-350">Chart Of Account</th>
                                    <th class="w-350 ca_row" style="display: none">Control Account</th>
                                    <th class="w-200">Amount</th>
                                    <th class="w-350">Remarks</th>
                                 </tr>
                              </thead>
                              
                              <tbody></tbody>

                              <tfoot>
                                 <tr>
                                    <td></td>
                                    <td class="ca_row" style="display: none;"></td>
                                    <td style="text-align: right">Subtotal</td>
                                    <td>
                                       <input type="number" id="grand_total" class="form-control" value="0.00" readonly />
                                    </td>
                                    <td></td>
                                 </tr>
                              </tfoot>
                           </table>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-12">
                           <a class="btn_add_item btn btn-outline-danger btn-sm" style="margin-right: 10px;"><i class="fa-solid fa-plus"></i> ADD ENTRY</a>
                        </div>
                     </div>

                     <div class="row ft_input" style="display: none">
                        <div class="col-md-6" style="margin-top: 20px">
                           <div class="form-group row">
                              <label for="received_by" class="col-md-4 control-label">Received By : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text"
                                    id="received_by" name="received_by"
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
                                    class="form-control w-300" required />
                              </div>
                           </div>
                        </div>
                     </div>

                  </div>
                  <div class="card-footer">
                     <a href="/petty_cash/listing" class="btn btn-info btn-sm">Cancel</a>
                     <button type="button" id="btn_print" class="btn btn-secondary btn-sm" style="display: none">Print</button>
                     <button type="button" id="btn_submit" class="btn btn-warning btn-sm float-right">Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Modal :: Clone New Row -->
<table id="tbl_clone" style="display: none">
   <tbody>
      <tr id="0">
         <td>
            <input 
               type="hidden" 
               id="entry_id_0" name="entry_id[]" class="entry_id" />

            <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
            <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
         </td>
         
         <td>
            <input 
               type="text" 
               id="coa_desc_0" 
               class="form-control coa_desc" readonly />
            
            <input 
               type="hidden" 
               id="coa_0" name="coa[]" class="coa" />
         </td>

         <td class="ca_row">
            <input 
               type="text" id="iden_details_0"
               class="form-control iden_details" readonly />
            
            <input type="hidden" id="iden_0" name="iden[]" class="iden" />
            <input type="hidden" id="gst_type_0" name="gst_type[]" class="gst_type" />
            <input type="hidden" id="gst_category_0" name="gst_category[]" class="gst_category" />
            <input type="hidden" id="net_amount_0" name="net_amount[]" class="net_amount" />
            <input type="hidden" id="gst_amount_0" name="gst_amount[]" class="gst_amount" />
         </td>

         <td>
            <input 
               type="number" 
               id="amount_0" name="amount[]" 
               class="form-control amount" readonly />
         </td>

         <td>
            <input 
               type="text" 
               id="remarks_0" name="remarks[]" 
               class="form-control remarks" readonly />
         </td>
      </tr>
   </tbody>
</table>

<!-- Modal :: Entry -->
<div id="entryModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-body">
               <input type="hidden" id="entry_id" />

               <div class="row mt-10">
                  <label class="control-label col-12">Chart Of Account <span class="cl-red">*</span></label>
                  <div class="col-12">
                     <select id="coa" class="form-control">'
                        <?php echo $coa_list; ?>
                     </select>
                  </div>
               </div>

               <hr />

               <div class="row mt-10 customer_field" style="display: none">
                  <label class="control-label col-12">Control Account <span class="cl-red">*</span></label>
                  <div class="col-12">
                     <select id="customer" class="form-control">
                        <?php echo $customers; ?>'
                     </select>
                  </div>
               </div>

               <div class="row mt-10 supplier_field" style="display: none">
                  <label class="control-label col-12">Control Account <span class="cl-red">*</span></label>
                  <div class="col-12">
                     <select id="supplier" class="form-control">
                        <?php echo $suppliers; ?>'
                     </select>
                  </div>
               </div>

               <div class="row mt-10 gst_field" style="display: none">
                  <div class="col-4">
                     <label class="control-label">Net Purchase $ <span class="cl-red">*</span></label>
                     <input type="number" id="net_amount" class="form-control" />
                  </div>
               </div>

               <div class="row mt-10 gst_field" style="display: none">
                  <div class="col-12">
                     <label class="control-label">GST Input Category <span class="cl-red">*</span></label>
                     <select id="gst_category" class="form-control">
                        <?php echo $gst_input_categories; ?>'
                     </select>
                     <input type="hidden" id="gst_rate" value="<?php echo $std_gst_rate; ?>" />
                     <input type="hidden" id="gst_type" value="I" />
                  </div>
               </div>

               <div class="row mt-10 gst_field" style="display: none">
                  <div class="col-4">
                     <label class="control-label">GST Amount $ <span class="cl-red">*</span></label>
                     <input type="number" id="gst_amount" class="form-control" />
                  </div>
               </div>

               <div class="row mt-10 amount_field" style="display: none">
                  <div class="col-4">
                     <label class="control-label">Amount <span class="cl-red">*</span></label>
                     <input type="number" id="amount" class="form-control" />
                  </div>
               </div>

               <div class="row mt-10 remarks_field" style="display: none">
                  <div class="col-12">
                     <label class="control-label">Remarks</label>
                     <input type="text" id="remarks" class="form-control" />
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#entryModal">Cancel</button>
               <button type="button" class="btn btn-info btn-sm float-right" id="btn_save_item">SAVE</button>
            </div>
         </div>
      </div>
   </div>
</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="/assets/js/exit.js"></script>

<script type="text/javascript">
   var row_number = 0;
   $(function() {

      $('#pay_to').focus();

      $('select').select2();

      $(".btn_add_item").on('click', function() {
         if(!isFormValid()) {
            return false;
         }

         $('#process').val('add');

         clear_inputs();
         $('#entryModal').modal('show');
      });

      $(document).on('change', '#coa', function() {
         var accn = $('option:selected', this).val();
         
         $("#customer, #supplier").hide();
         $('#customer').select2("destroy").val('').select2();
         $('#supplier').select2("destroy").val('').select2();
         $("#amount").val("");

         $('.customer_field, .supplier_field, .gst_field, .amount_field, .remarks_field').hide();

         if(accn !== "") {            

            if(accn == "CA001") {

               $('.customer_field, .amount_field, .remarks_field').show();
               
            } else if(accn == "CL001") {
               
               $('.supplier_field, .amount_field, .remarks_field').show();

            } else if(accn == "CL300") {

               $('.gst_field, .remarks_field').show();

            } else {
               
               $('.amount_field, .remarks_field').show();
               $("#amount").focus();

            }
         }
      });

      $(document).on('change', '#customer', function() {
         var customer = $('option:selected', this).val();

         if(customer !== "") {
            $("#amount").focus();
         }
      });

      $(document).on('change', '#supplier', function() {
         var supplier = $('option:selected', this).val();

         if(supplier !== "") {
            $("#amount").focus();
         }
      });

      $(document).on('change', '#gst_category', function() {
         if($(this).val() !== '') {
            var gst_code = $('option:selected', this).val();
            var gst_text = $('option:selected', this).text();
            $('#gst_rate').val("0");
            $.post('/petty_cash/ajax/get_gst_rate', {
               gst_id: gst_id
            }, function (gst_rate) {
               $('#gst_rate').val(gst_rate);
               get_gst_amount();
            });
         } else {
            $('#net_amount').focus();
         }
      });

      $(document).on('change', '#net_amount', function() {
         if($(this).val() !== "") {
            get_gst_amount();
         }
      });

      $(document).on("change", "#amount", function(e) {
         if($.trim($(this).val()) !== "") {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);
         }
      });

      // add entry to transaction
      $("#btn_save_item").on('click', function () {

         if(!isModalValid()) {
            return false;
         }

         save_entry();
      });

      // EDIT
      $(document).on('click', '.dt_edit', function () {

         if(!isFormValid()) {
            return;
         }

         row_number = $(this).closest('tr').attr('id');

         $('#entry_id').val($('#entry_id_'+row_number).val());

         $('#coa').select2("destroy");
         $('#coa').val($('#coa_'+row_number).val());
         $('#coa').select2();

         if($('#coa_'+row_number).val() == "CA001") {

            $('#customer').select2("destroy");
            $('#customer').val($('#iden_'+row_number).val());
            $('#customer').select2();
            $('.customer_field').show();

         } else if($('#coa_'+row_number).val() == "CL001") {

            $('#supplier').select2("destroy");
            $('#supplier').val($('#iden_'+row_number).val());
            $('#supplier').select2();
            $('.supplier_field').show();

         }  else if($('#coa_'+row_number).val() == "CL300") {

            $('#gst_type').val($('#gst_type_'+row_number).val());

            $('#net_amount').val($('#net_amount_'+row_number).val());

            $('#gst_category').select2("destroy");
            $('#gst_category').val($('#gst_category_'+row_number).val());
            $('#gst_category').select2();

            $('#gst_amount').val($('#gst_amount_'+row_number).val());

            $('.gst_field').show();
            $('.amount_field').hide();
         }

         $('#amount').val($('#amount_'+row_number).val());
         $('#remarks').val($('#remarks_'+row_number).val());

         $('#process').val('edit');
         $('#edit_id').val(row_number);

         $('.entry_field').show();

         $('#entryModal').modal('show');
      });

      $("#btn_print").on('click', function() {
         var entries = $('#tbl_items tbody tr').length;
         if(entries > 0) {
            $("#form_").attr("target", "_blank");
            $("#form_").attr("action", '/petty_cash/print_stage_1');
            $("#form_").submit();
         }
		});

      // submit
      $("#btn_submit").on('click', function (e) {

         if(!isFormValid() || $('#tbl_items > tbody > tr').length == 0) {
            return false;
         }

         $("#form_").attr("target", "_self");
         $("#form_").attr("action", '/petty_cash/save');
         $('#form_').submit();
      });


   }); // document ends

   function clear_inputs() {
      $('#entry_id').val('');
      $('#edit_id').val('');     

      $('#coa').select2("destroy").val('').select2();
      $('#customer').select2("destroy").val('').select2();
      $('#supplier').select2("destroy").val('').select2();

      $('#amount').val('');
      $('#remarks').val('');

      $('#net_amount').val('');
      $('#gst_category').select2("destroy");
      $('#gst_category').val('TX');
      $('#gst_category').select2();
      $('#gst_amount').val('');

      $('.customer_field, .supplier_field, .gst_field, .amount_field, remarks_field').hide();
   }

   function isFormValid() {
      var valid = true;
      if($('#pay_to').val() == "") {
         $("#pay_to").focus();
         valid = false;
      } else if($('#doc_date').val() == "") {
         $("#doc_date").focus();
         valid = false;
      }
      return valid;
   }

   function isModalValid() {
      var valid = true;
      if($('#coa').val() == "") {
         $("#coa").select2('open');
         valid = false;
      } else if($('#coa').val() == "CA001" && $('#customer').val() == "") {
         $("#customer").select2('open');
         valid = false;
      } else if($('#coa').val() == "CL001" && $('#supplier').val() == "") {
         $("#supplier").select2('open');
         valid = false;
      } else if($('#coa').val() == "CL300" && $('#net_amount').val() == "") {
         $('#net_amount').focus();
         valid = false;
      } else if($('#coa').val() == "CL300" && $('#gst_category').val() == "") {
         $("#gst_category").select2('open');
         valid = false;
      } else if($('#coa').val() == "CL300" && $('#gst_amount').val() == "") {
         $('#gst_amount').focus();
         valid = false;
      } else if($('#coa').val() !== "CL300" && $('#amount').val() == "") {
         $('#amount').focus();
         valid = false;
      }
      return valid;
   }   

   function get_gst_amount() {
      var net_amount = Number($('#net_amount').val());
      var gst_rate = Number($('#gst_rate').val());
      var gst_amount = Number(0);

      if(net_amount !== "" && gst_rate !== "") {
         gst_amount = Math.round(net_amount * gst_rate) / 100;
      } else {
         gst_amount = 0;
      }

      $('#gst_amount').val(gst_amount.toFixed(2));
      $('#amount').val(gst_amount.toFixed(2));
   }

   function process_total() {
      var grand_total = 0;
      $("#tbl_items tbody tr").each(function() {
         row_number = $(this).attr("id");

         // Calculate Grand-Total
         grand_total += Number($('#amount_'+row_number).val());
         $('#grand_total').val(grand_total.toFixed(2));
      });
   }

   function save_entry() {
      // header values
      var doc_date = $("#doc_date").val();
      var ref_no = $("#ref_no").val();
      var pay_to = $("#pay_to").val();

      // body values
      var entry_id = $('#entry_id').val();
      var coa = $("#coa").val();
      var iden = '';
      if(coa == "CA001") {
         iden = $('#customer').val();
      } else if(coa == "CL001") {
         iden = $('#customer').val();
      }

      var gst_type = '';
      var net_amount = 0;
      var gst_category = '';
      var gst_amount = 0;
      if(coa == "CL300") {
         gst_type = $('#gst_type').val();
         net_amount = $('#net_amount').val();
         gst_category = $('#gst_category').val();
         gst_amount = $('#gst_amount').val();
      }

      var amount = $("#amount").val();
      var remarks = $("#remarks").val();

      // footer values
      var received_by = $("#received_by").val();
      var approved_by = $("#approved_by").val();

      $.post('/petty_cash/ajax/save_entry', {
         entry_id: entry_id,
         pay_to: pay_to,
         doc_date: doc_date,
         ref_no: ref_no,
         coa: coa,
         amount: amount,
         gst_type: gst_type,
         gst_category: gst_category,
         net_amount: net_amount,
         gst_amount: gst_amount,
         iden: iden,
         remarks: remarks,
         received_by: received_by,
         approved_by: approved_by
      }, function(entry_id) {
         $("#entry_id").val($.trim(entry_id));

         manage_entry();
      });
   }

   function manage_entry() {
      if($('#process').val() == 'add') { // New Row
         $row = $("#tbl_clone tbody tr").clone();
      } else if($('#process').val() == "edit") { // Existing Row
         $row = $('tr[id="'+$("#edit_id").val()+'"]');
      }

      $row.find('input.entry_id').val($('#entry_id').val());

      $row.find('input.coa_desc').val($("#coa>option:selected").text());
      $row.find('input.coa').val($("#coa").val());      

      if($("#coa").val() == "CA001") {
         $row.find('input.iden').val($('#customer').val());
         $row.find('input.iden_details').val($("#customer>option:selected").text());
      } else if($("#coa").val() == "CL001") {
         $row.find('input.iden').val($('#supplier').val());
         $row.find('input.iden_details').val($("#supplier>option:selected").text());
      }

      $row.find('input.amount').val($('#amount').val());
      $row.find('input.remarks').val($('#remarks').val());

      $row.find('input.gst_type').val($('#gst_type').val());
      $row.find('input.net_amount').val($('#net_amount').val());
      $row.find('input.gst_category').val($('#gst_category').val());
      $row.find('input.gst_amount').val($('#gst_amount').val());

      if($('#process').val() == "add") {
         // append new row to the table
         $('#tbl_items').append($row);
         sortTblRowsByID();
      }

      $('#tbl_items .ca_row').hide();
      if($("#coa").val() == "CA001" || $("#coa").val() == "CL001") {
         $('#tbl_items .ca_row').show();
      }

      $('#tbl_items').show();

      process_total();

      $('#entryModal').modal('hide');
   }

   function sortTblRowsByID() {
      var row_number = 0;
      var DELIMITER;
      var parts;
      $("#tbl_items tbody tr").each(function () {
         $(this).find('input, select, button, textarea').each(function() {
            var id = $(this).attr('id') || null;

            if(id) {
               DELIMITER = "_";
               parts = id.split(DELIMITER);
               parts[parts.length - 1] = row_number;
               id = parts.join(DELIMITER);
               console.log("ID >>> "+id);
               $(this).attr('id', id);
            }
         });

         $(this).attr('id', row_number);
         row_number = row_number + 1;
      });
   }   
</script>
