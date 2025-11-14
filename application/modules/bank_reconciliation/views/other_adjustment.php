<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Bank Reconcilation</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Bank Reconciiation</li>
               <li class="breadcrumb-item">Ot.Adj</li>
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
            <form autocomplete="off" id="form_" method="post">
               <input type="hidden" name="tran_type" value="A" />
               <div class="card card-default">
                  <div class="card-header">
                     <h5>Other Adjustment</h5>
                     <a href="/bank_reconciliation" class="btn btn-outline-dark btn-sm float-right">
                        <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                     </a>
                  </div>
                  <div class="card-body">
                     <div class="row form-group">
                        <div class="col-md-8">
                           <label class="control-label" for="bank">Bank</label> <br />
                           <select id="bank" name="bank" class="form-control">
                              <?php echo $bank_list; ?>
                           </select>
                        </div>
                     </div>
                     <div class="row form-group fbank">
                        <div class="col-md-8">
                           <label class="control-label" for="fbank">Foreign Bank</label> <br />
                           <select id="fbank" name="fbank" class="form-control">
                              <?php echo $fb_list; ?>
                           </select>
                        </div>
                     </div>
                     <div class="row form-group d-none">
                        <div class="col-md-3">
                           <label class="control-label" for="start_date">Start Date : </label> <br />
                           <input 
                           type="text" 
                           id="start_date" name="start_date" readonly
                           class="form-control" style="max-width: 110px;" />
                        </div>
                        <div class="col-md-3">
                           <label class="control-label" for="end_date">End Date : </label> <br />
                           <input 
                           type="text" 
                           id="end_date" name="end_date" readonly
                           class="form-control" style="max-width: 110px;" />
                        </div>
                     </div>

                     <div class="row table-responsive">
                        <div class="col-md-12">
                           <table id="tbl_items" class="table" style="min-width: 1400px; width: 100%;">
                              <thead>
                                 <?php
                                    $entries = $this->custom->getCount('bank_recon_current', ['tran_type' => 'A', 'accounted' => 'n']);
               ?>
                                 <tr style="<?php echo $entries > 0 ? '' : 'display: none'; ?>">
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Remarks</th>
                                    <th>Amount</th>
                                    <th>Sign</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                              <?php
               $i = 0;
               $this->db->select('*');
               $this->db->from('bank_recon_current');
               $this->db->where(['tran_type' => 'A', 'accounted' => 'n']);
               $query = $this->db->get();
               $batch_entries = $query->result();
               foreach ($batch_entries as $key => $value) {
                   $document_date = $value->doc_date;
                   $document_reference = $value->doc_ref;
                   $document_remarks = $value->remarks; ?>
                        <tr id="<?php echo $i; ?>">
                           <td style="width: 140px;">
                              <input type="hidden" id="recon_item_id_<?php echo $i; ?>" name="recon_item_id[<?php echo $i; ?>]" value="<?php echo $value->br_id; ?>" />

                              <input
                                 type="text"
                                 id="doc_date_<?php echo $i; ?>"
                                 name="doc_date[<?php echo $i; ?>]"
                                 class="form-control dp_full_date doc_date"
                                 placeholder="dd-mm-yyyy" 
                                 value="<?php echo date('d-m-Y', strtotime($document_date)); ?>" />
                           </td>

                           <td style="width: 170px;">
                              <input
                                 type="text"
                                 id="doc_ref_<?php echo $i; ?>"
                                 name="doc_ref[<?php echo $i; ?>]"
                                 class="form-control doc_ref"
                                 maxlength="12" 
                                 value="<?php echo $document_reference; ?>"
                                 style="width: 170px;" />
                                 <span class="error-ref" style="display: none; color: red; font-size: 12px;">Duplicate reference disallowed</span>
                           </td>

                           <td>
                              <input
                                 type="text"
                                 id="remarks_<?php echo $i; ?>"
                                 name="remarks[<?php echo $i; ?>]"
                                 class="form-control"
                                 maxlength="250" 
                                 value="<?php echo $document_remarks; ?>" />
                           </td>

                           <td style="width: 250px">
                              <input
                                 type="number"
                                 id="amount_<?php echo $i; ?>"
                                 name="amount[<?php echo $i; ?>]"
                                 class="form-control amount" 
                                 value="<?php echo $value->amount; ?>" />
                           </td>

                           <td style="width: 200px;">
                              <select id="sign_<?php echo $i; ?>" name="sign[<?php echo $i; ?>]" class="form-control sign">
                                 <option value="">-- Select Entry --</option>                              
                                 <option value="+" <?php if ($value->sign == '+') {
                                     echo 'selected="selected"';
                                 } ?>>DEBIT</option>
                                 <option value="-" <?php if ($value->sign == '-') {
                                     echo 'selected="selected"';
                                 } ?>>CREDIT</option>
                              </select>
                           </td>
                          
                           <td style="width: 100px; text-align: right">
                              <button type="button" class="btn btn-danger btn_delete_row">Delete</button>
                           </td>

                        </tr>

                        <?php
                          ++$i;
               }
               ?>
                              </tbody>

                              <tfoot>
                                 <tr>
                                    <td colspan="6" style="border-top: 1px solid gainsboro; padding: .2rem 1rem">                                      
                                       <a href="#" class="btn_add_item btn btn-success btn-sm"><i class="fa-solid fa-plus"></i> Add Entry</a>
                                    </td>
                                 </tr>
                              </tfoot>
                           </table>
                        </div>
                     </div>

                  </div>
                  <div class="card-footer">
                     <a href="/bank_reconciliation" class="btn btn-info btn-sm">Cancel</a>
                     <button type="button" id="btn_submit" class="btn btn-warning btn-sm float-right">SUBMIT</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
      
   </div>
</div>

<!-- Model of new row -->
<table id="tbl_clone" style="display: none">
   <tbody>
      <tr id="0">
         <td style="width: 140px">
            <input type="hidden" id="recon_item_id_0" name="recon_item_id[]" />
            <input
               type="text"
               id="doc_date_0"
               name="doc_date[]"
               class="form-control dp_full_date doc_date"
               placeholder="dd-mm-yyyy" />
         </td>

         <td style="width: 180px">
            <input
               type="text"
               id="doc_ref_0"
               name="doc_ref[]"
               class="form-control doc_ref"
               maxlength="12" />
               <span class="error-ref" style="display: none; color: red; font-size: 12px;">Duplicate reference disallowed</span>
         </td>

         <td>
            <input
               type="text"
               id="remarks_0"
               name="remarks[]"
               class="form-control"
               maxlength="250" />
         </td>

         <td style="width: 250px">
            <input
               type="number"
               id="amount_0"
               name="amount[]"
               class="form-control amount"  />
         </td>

         <td style="width: 200px">
            <select id="sign_0" name="sign[]" class="form-control select2 sign">
               <option value="">-- Select Entry --</option>
               <option value="+">DEBIT</option>
               <option value="-">CREDIT</option>
            </select>
         </td>
      
         <td style="width: 100px; text-align: right">
            <button type="button" class="btn btn-danger btn_delete_row">Delete</button>
         </td>
      </tr>
   </tbody>
</table>
<style type="text/css">
   .select2 {
      max-width: 400px;
   }
</style>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>

   var same_reference_exists = 0;
   var row_number = 0;
   var populate_data_by_reference = false;

   // document starts
   $(function() {

      $('select').select2();

      $("#bank").attr('disabled', true);
      $("#fbank").attr('disabled', true);

      bank = $("#bank option:selected").val();

      if(bank == "CA110") {
         $('.fbank').show();
      } else {
         $('.fbank').hide();
      }

      if(bank !== "") {
         $.post('/bank_reconciliation/ajax/get_adj_details', {
            bank: bank
         }, function(data) {
            var obj = $.parseJSON(data);
            $('#start_date').val(obj.start_date);
            $('#end_date').val(obj.end_date);
         });
      }

      $(document).on("change", ".doc_ref", function() {
         row_number = $(this).closest('tr').attr("id");
         var ref = $(this).val();         

         if(ref !== "") {
            validate_reference(row_number);
         } else {
            same_reference_exists = 0;
            $(this).closest('td').find('.error-ref').css("display", "none");
         }
      });

      $(document).on("change", ".amount", function() {
         if($.trim($(this).val()) !== "") {
            var input_value = $(this).val();
            $(this).val(Number(input_value).toFixed(2));
         } else {
            $(this).focus();
         }
      });

      // btn - add new item 
      $(document).on('click', '.btn_add_item', function() {
         add_entry();
      });

      // delete item
      var delete_row_id = -1;
      $(document).on('click', '.btn_delete_row', function() {
         delete_row_id = $(this).closest('tr').attr("id");
         $('#confirmDeleteModal .modal-body').html("Click 'Yes' to delete the current entry");
         $("#confirmDeleteModal").modal();
      });

      // item delete = YES
      $('#btn-confirm-delete-yes').click(function() {
         var id = $("#recon_item_id_"+delete_row_id).val();
         if(id !== "") {
            delete_entry(id);
         }

         $('tr#'+delete_row_id).remove();

         if($('#tbl_items tbody tr').length == 0) {
            $('#tbl_items thead tr').hide();
         }

         $("#confirmDeleteModal").modal('hide');
      });

      // submit
      $("#btn_submit").on('click', function (e) {

         if($('#tbl_items tbody tr').length == 0) {
            return;
         }

         $.confirm({
            title: '<i class="fa fa-info"></i> Confirm Submit',
            content: 'Are you sure to Submit ?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function(){
                     var save_url = "/bank_reconciliation/save_current";
                     $("#form_").attr("action", save_url);
                     $("#bank").attr('disabled', false);
                     $("#fbank").attr('disabled', false);
                     $('#form_').submit();
                  }
               },
               no: {
                  btnClass: 'btn-dark',
                  action: function(){
                  }
               },
            }
         });
      });

   }); // document ends

   function add_entry() {
      $row_valid = true;
         
      $("#tbl_items tbody tr").each(function (i, val) {
         row_number = $(this).attr("id");

         $doc_date = $("#doc_date_"+row_number).val();
         $doc_ref = $("#doc_ref_"+row_number).val();
         $amount = $("#amount_"+row_number).val();
         $sign = $("#sign_"+row_number).val();

         if($doc_date == "") {
            $("#doc_date_"+row_number).focus();
            $row_valid = false;
         } else if($doc_ref == "") {
            $("#doc_ref_"+row_number).focus();
            $row_valid = false;
         } else if($amount == "") {
            $("#amount_"+row_number).focus();
            $row_valid = false;
         } else if($sign == "") {
            $("#sign_"+row_number).select2('open');
            $row_valid = false;
         }
      });

      if(!$row_valid) {
         return;
      }

      $tr = $('#tbl_items tbody tr:last');
      //var allTrs = $tr.closest('table').find('tbody tr');
      //var lastTr = allTrs[allTrs.length-1];
      //$(lastTr).find('select').select2("destroy"); // remove select 2 before clone tr
      //$new_row = $(lastTr).clone();

      var lastTr = $("#tbl_clone tbody tr");
      $(lastTr).find('select').select2("destroy"); // remove select 2 before clone tr

      $new_row = $("#tbl_clone tbody tr").clone();

      // Input & Select fields id attribute value increment
      var last_record_number = $('#tbl_items tbody tr:last').attr('id');
      var current_record_number;      
      if(typeof last_record_number === "undefined") {
         last_record_number = 0;
         current_record_number = 0;
      } else {
         current_record_number = Number(last_record_number) + 1;
      }
      console.log(">>> last_record_number >>> "+last_record_number);
      console.log(">>> current_record_number >>> "+current_record_number);

      $new_row.find('input, select, button, textarea').each(function() {
         var id = $(this).attr('id') || null;

         if(id) {
            // Get id number from ID text
            // Ex: unit_price_123 :: last_record_number = 123
            //var last_record_number = id.split("_").pop();
            //console.log(">>> last_record_number" + last_record_number);

            console.log(">>> ID Last >>> "+id);

            id = id.replace('_0', '_'+current_record_number);

            console.log(">>> ID Current >>> "+id);

            $(this).attr('id', id);
         }
      });

      // Datepicker
      $new_row.find("input.doc_date")
      .removeClass('hasDatepicker')
      .removeData('datepicker')
      .unbind()
      .datepicker({
         changeMonth: true,
         changeYear: true,
         dateFormat: 'dd-mm-yy',
         yearRange: '-3:+1'
      });

      // empty all the input values in new row
      $new_row.find('input').val('');
      $new_row.find('select').val('');

      // Table row Id number increment
      $last_no = $new_row.attr('id');
      $new_row.attr('id', current_record_number);

      // append new row to the table
      //$tr.closest('table').append($new_row);

      if($('#tbl_items tr').length > 0) {
         $('#tbl_items thead tr').show();
      }
      $('#tbl_items').append($new_row);

      // add select2 again
      $(lastTr).find('select').select2();
      $new_row.find('select').select2();

      // set current row number to public variable
      current_entry = $new_row.attr('id');
      console.log("current_entry >>> "+current_entry);

      console.log("populate_data_by_reference >>> "+populate_data_by_reference);
      if(populate_data_by_reference) {
         $.post('/bank_reconciliation/ajax/populate_data_by_reference', {
            entry_id: $('#ref').val(),
            recon: $('#recon_bank').val()
         }, function(data) {
            var obj = $.parseJSON(data);
            $("#doc_date_"+ current_entry).val(obj.doc_date);
            $("#doc_ref_"+ current_entry).val(obj.ref_no);
            $("#remarks_"+ current_entry).val(obj.remarks);
            $("#amount_"+ current_entry).val(obj.amount);
            $("#sign_"+ current_entry).val(obj.sign).change();
         });
      }

      // Handler for .ready() called.
      $('html, body').animate({
         scrollTop: $('#'+current_entry).offset().top
      }, 'slow');
   }

   function validate_reference(current_entry) {
      var current_ref = $("#doc_ref_"+current_entry).val();
      // Double Check reference with other transactions reference
      $('.doc_ref').each(function() {
         other_entry = $(this).closest('tr').attr("id");
         other_ref = $(this).val();
         if(current_entry !== other_entry) {
            if(current_ref.toLowerCase() == other_ref.toLowerCase()) {
               console.log(">>>>> Internal Reference Exists >>>> ");
               same_reference_exists = 1;
               $("#doc_ref_"+current_entry).focus();
               $("#doc_ref_"+current_entry).closest('td').find('.error-ref').css("display", "block");
               return false;
            } else {
               console.log(">>>>> Internal Reference NOT Exists >>>> ");
               same_reference_exists = 0;
               $("#doc_ref_"+current_entry).closest('td').find('.error-ref').css("display", "none");
            }
         } else {
         same_reference_exists = 0;
         console.log(">>>>> NO Reference Check >>>> ");
         }
      });

      if(same_reference_exists == 0) {
         $.post('/bank_reconciliation/ajax/double_reference', {
            ref_id: current_ref
         }, function(data) {
            if (data == 1) {
               console.log(">>>>> External Reference Exists >>>> ");
               same_reference_exists = 1;
               $("#doc_ref_"+current_entry).focus();
               $("#doc_ref_"+current_entry).closest('td').find('.error-ref').css("display", "block");
            } else {
               console.log(">>>>> External Reference NOT Exists >>>> ");
               same_reference_exists = 0;
               $("#doc_ref_"+current_entry).closest('td').find('.error-ref').css("display", "none");
            }
         });
      }
   }

   function delete_entry(id) {
      $.post('/bank_reconciliation/ajax/delete_recon', {
         recon_id: id,
         tbl: "bank_recon_current"
      }, function(data) {
         var obj = $.parseJSON(data);
         if(obj.deleted !== "") {
            console.log("Delete Recon ID :: "+id);
         }
      });
   }
</script>