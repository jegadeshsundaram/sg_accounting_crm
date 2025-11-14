<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">General Ledger</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Opening Balance</li>
               <li class="breadcrumb-item">Manage</li>
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

            <input type="hidden" id="redirect_url" value="/general_ledger" />
            <input type="hidden" id="process" />
            <input type="hidden" id="edit_id" />

            <form autocomplete="off" id="form_" method="post" action="<?php echo $save_url; ?>">
               <div class="card card-default">
                  <div class="card-header">
                     <h5>Opening Balance</h5>
                     <a href="/general_ledger/opening_balance" class="btn btn-outline-dark btn-sm float-right">
                        <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                     </a>
                  </div>
                  <div class="card-body">

                     <div class="row form-group">
                        <label class="control-label col-md-3">Date : </label>
                        <div class="col-md-3">
                           <input 
                              type="text" 
                              id="doc_date" name="doc_date" 
                              value="<?php echo $page == 'edit' ? date('d-m-Y', strtotime($doc_date)) : ''; ?>"
                              class="form-control dp_full_date doc_date w-150" 
                              placeholder="dd-mm-yyyy" />
                        </div>
                     </div>

                     <div class="row form-group">
                        <label class="control-label col-md-3">Reference : </label><br />
                        <div class="col-md-3">
                           <input 
                              type="text" 
                              id="ref_no" name="ref_no" 
                              class="form-control ref_no w-150" 
                              value="<?php echo $ref_no; ?>"
                              maxlength="12" />
                           
                           <input type="hidden" id="original_ref_no" value="<?php echo $ref_no; ?>" />
                           <span id="ref_error" style="display: none; color: red;">Duplicate reference disallowed</span>
                        </div>
                     </div>

                     <div class="row form-group">
                        <label class="control-label col-md-3">Remarks : </label><br />
                        <div class="col-md-3">
                           <input 
                              type="text" 
                              id="remarks" name="remarks" 
                              class="form-control remarks w-450" 
                              value="<?php echo $remarks; ?>"
                              maxlength="250" />
                        </div>
                     </div>

                     <br />
                     
                     <div class="row">
                        <div class="col-md-12 table-responsive">
                           <table id="tbl_items" class="table table-custom" style="min-width: 1000px; width: 100%; display: <?php echo $page == 'edit' ? 'inline-table' : 'none'; ?>">
                              <thead>
                                 <tr>
                                    <th class="w-130">Action</th>
                                    <th>Account</th>
                                    <th class="w-250 txt-right">Debit $</th>
                                    <th class="w-250 txt-right">Credit $</th>
                                 </tr>
                              </thead>

                              <tbody>
                                 <?php
                                 if ($page == 'edit') {
                                    $i = 0;
                                    $this->db->select('*');
                                    $this->db->from('gl_open');
                                    $this->db->where(['doc_date' => $doc_date, 'ref_no' => $ref_no, 'status' => 'C']);
                                    $query = $this->db->get();
                                    $ob_entries = $query->result();
                                    foreach ($ob_entries as $value) { ?>

                                 <tr id="<?php echo $i; ?>">
                                    <td>
                                       <input 
                                          type="hidden" 
                                          id="ob_id_<?php echo $i; ?>" name="ob_id[]" 
                                          value="<?php echo $value->ob_id; ?>"
                                          class="ob_id" />
                                       
                                       <input 
                                          type="hidden"
                                          id="sign_<?php echo $i; ?>" name="sign[]"
                                          value="<?php echo $value->sign; ?>"
                                          class="sign" />

                                       <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                                       <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                                    </td>
                                    
                                    <td>
                                       <?php $accn_desc = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]); ?>
                                       <input 
                                          type="text" 
                                          id="accn_desc_<?php echo $i; ?>" 
                                          value="<?php echo $value->accn.' : '.$accn_desc; ?>"
                                          class="form-control-dsply accn_desc" readonly />
                                       
                                       <input 
                                          type="hidden" 
                                          id="accn_<?php echo $i; ?>" name="accn[]" 
                                          value="<?php echo $value->accn; ?>" class="accn" />
                                    </td>

                                    <td>
                                       <input 
                                          type="number" 
                                          id="debit_amount_<?php echo $i; ?>" name="debit_amount[]" 
                                          value="<?php echo $value->sign == '+' ? $value->total_amount : ''; ?>"
                                          class="form-control-dsply txt-right debit_amount" readonly />
                                    </td>

                                    <td>
                                       <input 
                                          type="number" 
                                          id="credit_amount_<?php echo $i; ?>" name="credit_amount[]" 
                                          value="<?php echo $value->sign == '-' ? $value->total_amount : ''; ?>"
                                          class="form-control-dsply txt-right credit_amount" readonly />
                                    </td>
                                 </tr>
                                 <?php 
                                    ++$i;
                                    }
                                 }
                                ?>
                              </tbody>

                              <tfoot>
                                 <tr>
                                    <td></td>
                                    <td style="font-size: 18px; text-align: right; padding-right: 10px; padding-top: 10px; color: blue"><span id="sub_total" style="display: none">Sub Total</span></td>
                                    <td style="font-size: 18px; padding-left: 10px;"><span id="debit_grand_total" class="form-control" style="display: none">$0.00</span></td>
                                    <td style="font-size: 18px; padding-left: 10px;"><span id="credit_grand_total" class="form-control" style="display: none">$0.00</span></td>
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

                  </div>
                  <div class="card-footer">
                     <a href="/general_ledger/opening_balance" class="btn btn-info btn-sm">Cancel</a>                  
                     <button type="button" id="btn_submit" class="btn btn-warning btn-sm float-right">Submit</button>
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
         <td>
            <input 
               type="hidden" 
               id="ob_id_0" name="ob_id[]" 
               class="ob_id" />

            <input 
               type="hidden"
               id="sign_0" name="sign[]"
               class="sign" />

            <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
            <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
         </td>
         
         <td>
            <input 
               type="text" 
               id="accn_desc_0" 
               class="form-control-dsply accn_desc" readonly />
            
            <input 
               type="hidden" 
               id="accn_0" name="accn[]" class="accn" />
         </td>

         <td>
            <input 
               type="number" 
               id="debit_amount_0" name="debit_amount[]" 
               class="form-control-dsply txt-right debit_amount" readonly />
         </td>

         <td>
            <input 
               type="number" 
               id="credit_amount_0" name="credit_amount[]" 
               class="form-control-dsply txt-right credit_amount" readonly />
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
                  <label class="control-label col-12">Account <span class="cl-red">*</span></label>
                  <div class="col-12">
                     <select id="accn" class="form-control">
                        <?php echo $coa_list; ?>
                     </select>
                  </div>
               </div>

               <hr />

               <div class="row mt-10 entry_field" style="display: none">
                  <div class="col-6">
                     <label class="control-label">Amount</label>
                     <input type="number" id="amount" class="form-control" />
                  </div>
                  <div class="col-6" style="float: right">
                     <label class="control-label mb-10" style="margin-top: 5px; margin-bottom: 5px !important;">Entry</label><br />
                     <input type="radio" id="entry_debit" name="entry_type" value="+" class="radio-inp" autocomplete="off" checked="checked">
                     <label class="radio-lbl" for="entry_debit">DEBIT</label>
                     <input type="radio" id="entry_credit" name="entry_type" value="-" class="radio-inp" autocomplete="off">
                     <label class="radio-lbl" for="entry_credit">CREDIT</label>
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#entryModal">Cancel</button>
               <button type="button" class="btn btn-info btn-sm float-right" id="btn_save">SAVE</button>
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
<script src="/assets/js/exit.js"></script>

<script>
   // document starts
   $(function() {

      var row_number = '';
      var double_ref = 0;

      $('select').select2();
      
      $(document).on("change", "#ref_no", function(e) {
         var ref_no = $(this).val();
         double_ref = 0;

         if(ref_no !== "") {

            // if page is edit and user try changing different ref and again changing to same one
            if(ref_no == $('#original_ref_no').val()) {
               return false;
            }

            $.post('/general_ledger/ajax/double_ob', {
               ref_no: $("#ref_no").val()
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

      $(".btn_add_item").on('click', function() {
         if(!isFormValid()) {
            return false;
         }

         $('#process').val('add');

         clear_inputs();
         $('#entryModal').modal('show');
      });

      $(document).on('change', '#accn', function() {
         var accn = $('option:selected', this).val();
         $('.entry_field').hide();

         if(accn !== "") {
            $('.entry_field').show();
         }
      });

      $(document).on("change", "#amount", function(e) {
         if($.trim($(this).val()) !== "") {
            $(this).val(Number($(this).val()).toFixed(2));
         }
      });

      // EDIT
      $(document).on('click', '.dt_edit', function () {

         if(!isFormValid()) {
            return;
         }

         row_number = $(this).closest('tr').attr('id');

         $('#entry_id').val($('#ob_id_'+row_number).val());

         $('#accn').select2("destroy");
         $('#accn').val($('#accn_'+row_number).val());
         $('#accn').select2();

         if($('#sign_'+row_number).val() == "+") {
            $('#entry_debit').prop("checked", true);
            $('#amount').val($('#debit_amount_'+row_number).val());
         } else if($('#sign_'+row_number).val() == "-") {
            $('#entry_credit').prop("checked", true);
            $('#amount').val($('#credit_amount_'+row_number).val());
         }

         $('.entry_field').show();
         $('#process').val('edit');
         $('#edit_id').val(row_number);
         $('#entryModal').modal('show');
      });

      // DELETE
      $(document).on('click', '.dt_delete', function () {
         row_number = $(this).closest('tr').attr("id");
         $.confirm({
            title: '<i class="fa fa-info"></i> Confirm Delete',
            content: 'Are you sure to Delete?</strong>',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function() {
                     $.post('/general_ledger/ajax/delete_ob_entry', {
                        ob_id: $('#ob_id_'+row_number).val()
                     }, function (status) {
                        if($.trim(status) == 'deleted') {
                           toastr.success("Entry deleted!");
                           $('tr#'+row_number).remove();

                           if($('#tbl_items > tbody > tr').length > 0) {
                              sortTblRowsByID();
                           } else {
                              $('#tbl_items').hide();
                           }
                        } else {
                           toastr.error("Post Error!");
                        }
                     });
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

      // save entry
      $("#btn_save").on('click', function () {

         if(!isFormValid() || !isModalValid()) {
            return false;
         }

         if(double_ref == 1) {
            $('#ref_no').focus();
            return false;
         }

         save();
      });

      // submit 
      $("#btn_submit").on('click', function () {
         if(!isFormValid() || $('#tbl_items > tbody > tr').length == 0) {
            return false;
         }

         $('#form_').submit();
      });

   }); // document ends

   function clear_inputs() {
      $('#entry_id').val('');
      $('#edit_id').val('');     

      $('#accn').select2("destroy").val('').select2();

      $('#amount').val('');      

      $('.entry_field').hide();
   }

   function isFormValid() {
      var valid = true;
      if($('#doc_date').val() == "") {
         $("#doc_date").focus();
         valid = false;
      } else if($('#ref_no').val() == "") {
         $("#ref_no").focus();
         valid = false;
      }
      return valid;
   }

   function isModalValid() {
      var valid = true;
      if($('#accn').val() == "") {
         $("#accn").select2('open');
         valid = false;
      } else if($('#amount').val() == "") {
         $('#amount').focus();
         valid = false;
      }
      return valid;
   }

   function save() {
      // header values
      var doc_date = $("#doc_date").val();
      var ref_no = $("#ref_no").val();
      var remarks = $("#remarks").val();

      // body values
      var entry_id = $("#entry_id").val();
      var accn = $("#accn").val();
      var amount = $("#amount").val();
      var entry_type = $("input[name='entry_type']:checked").val();

      $.post('/general_ledger/ajax/save_ob', {
         ob_id: entry_id,
         doc_date: doc_date,
         ref_no: ref_no,
         remarks: remarks,
         accn: accn,
         amount: amount,
         sign: entry_type         
      }, function(ob_id) {
         $("#entry_id").val($.trim(ob_id));

         manage_entry();
      });
   }

   function manage_entry() {

      if($('#process').val() == 'add') { // New Row
         $row = $("#tbl_clone tbody tr").clone();
      } else if($('#process').val() == "edit") { // Existing Row
         $row = $('tr[id="'+$("#edit_id").val()+'"]');
      }

      $row.find('input.ob_id').val($('#entry_id').val());

      $row.find('input.accn_desc').val($("#accn>option:selected").text());
      $row.find('input.accn').val($("#accn").val());     

      var entry_type = $("input[name='entry_type']:checked").val();
      if(entry_type == "+") {
         $row.find('input.credit_amount').val("");
         $row.find('input.debit_amount').val($('#amount').val());
      } else {
         $row.find('input.debit_amount').val("");
         $row.find('input.credit_amount').val($('#amount').val());
      }
      $row.find('input.sign').val(entry_type);
      
      if($('#process').val() == "add") {
         // append new row to the table
         $('#tbl_items').append($row);
         sortTblRowsByID();
      }

      $('#tbl_items').show();

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

   function total_debits() {
      var debit_total = 0;
      $('.debit_amount').each(function () {
         debit_total += Number($(this).val());
      });

      $('#debit_grand_total').html(debit_total.toFixed(2) + " <span style='color: sandybrown; font-weight: bold;'>DR</span>");
   }

   function total_credits() {
      var credit_total = 0;
      $('.credit_amount').each(function () {
         credit_total += Number($(this).val());
      });

      $('#credit_grand_total').html(credit_total.toFixed(2) + " <span style='color: darkseagreen; font-weight: bold;'>CR</span>");
   }
</script>