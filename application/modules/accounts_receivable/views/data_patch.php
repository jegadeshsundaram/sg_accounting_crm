<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Accounts Receivable</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">AR</li>
               <li class="breadcrumb-item">Opening Balance</li>
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

            <!-- Curency Rate - hidde field -->
            <input type="hidden" id="customer_currency" value="<?php echo $customer_currency; ?>" />
            <input type="hidden" id="currency_rate" value="<?php echo $currency_rate; ?>" />

            <!-- Edit Id - hidden field --> 
            <input type="hidden" id="edit_id" />

            <form autocomplete="off" id="form_" method="post" action="<?php echo $save_url; ?>">
               <div class="card card-default">
                  <div class="card-header">
                     <h5>OB Data Patch</h5>
                     <a href="/accounts_receivable/" class="btn btn-outline-secondary btn-sm float-right">Back</a>
                  </div>
                  <div class="card-body">
                     <div class="row">
                        <div class="col-md-4">
                           <label class="control-label">To : </label><br />
                           <select name="customer" id="customer" class="form-control" style="pointer-events: none; background: #f5f5f5" onclick="return false;" onkeydown="return false;">
                              <?php echo $customers; ?>
                           </select>
                        </div>
                     </div>
                     
                     <br /><br />

                     <div class="row">
                        <div class="col-md-12 table-responsive">
                           <table id="tbl_items" class="table table-custom" style="min-width: 1400px; width: 100%;">
                              <thead>
                                 <tr>
                                    <th class="w-130">Action</th>
                                    <th class="w-110">Entry</th>
                                    <th class="w-140">Date</th>
                                    <th class="w-180">Reference</th>
                                    <th class="w-200">Amount <span class="f_curr" style="display: <?php echo $customer_currency == 'SGD' ? 'none' : 'inline-block'; ?>">(<?php echo $customer_currency; ?>)</span> $</th>
                                    <th class="w-200 dv_local" style="display: <?php echo $customer_currency == 'SGD' ? 'none' : 'table-cell'; ?>">Amount <strong>(SGD)</strong> $</th>
                                    <th>Remarks</th>
                                    <th></th>
                                 </tr>
                              </thead>
                              <tbody>

                              <?php 
                              $i = 0;
                              $this->db->select('*');
                              $this->db->from('accounts_receivable');
                              $this->db->where(['customer_code' => $customer_code, 'tran_type' => 'OPBAL']);
                              if($ref_no != '') {
                                 $this->db->where(['customer_code' => $customer_code, 'doc_ref_no' => $ref_no, 'tran_type' => 'OPBAL']);
                              }
                              $this->db->order_by('sign ASC, doc_date ASC, doc_ref_no ASC');
                              $query = $this->db->get();
                              $ob_entries = $query->result();
                              foreach ($ob_entries as $value) { ?>
                                 <tr id="<?php echo $i; ?>">
                                    <td>
                                       <!-- Field : Entry Unique ID from DB -->
                                       <input 
                                          type="hidden" 
                                          id="entry_id_<?php echo $i; ?>" name="entry_id[]"
                                          value="<?php echo $value->ar_id; ?>" />
                                       
                                       <input 
                                          type="hidden" 
                                          id="sign_<?php echo $i; ?>" name="sign[]" 
                                          value="<?php echo $value->sign; ?>"
                                          class="sign" />
                                    
                                       <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                                       <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                                    </td>

                                    <td>
                                       <input 
                                          type="text" 
                                          id="entry_type_<?php echo $i; ?>" 
                                          value="<?php echo $value->sign == '+' ? 'Debit' : 'Credit'; ?>"
                                          class="form-control entry_type" readonly />
                                    </td>
                                    
                                    <td>
                                       <input 
                                          type="text" 
                                          id="doc_date_<?php echo $i; ?>" name="doc_date[]" 
                                          value="<?php echo date('d-m-Y', strtotime($value->doc_date)); ?>"
                                          class="form-control doc_date" readonly />
                                    </td>
                                    
                                    <td>
                                       <input 
                                          type="text" 
                                          id="ref_no_<?php echo $i; ?>" name="ref_no[]" 
                                          value="<?php echo $value->doc_ref_no; ?>"
                                          class="form-control ref_no" readonly />
                                    </td>
                                    
                                    <td>
                                       <input 
                                          type="number" 
                                          id="foreign_amount_<?php echo $i; ?>" name="foreign_amount[]" 
                                          value="<?php echo $value->f_amt; ?>"
                                          class="form-control foreign_amount" readonly />
                                    </td>
                                    
                                    <td class="dv_local" style="display: <?php echo $customer_currency == 'SGD' ? 'none' : 'table-cell'; ?>">
                                       <input 
                                          type="number" 
                                          id="local_amount_<?php echo $i; ?>" name="local_amount[]" 
                                          value="<?php echo $value->total_amt; ?>"
                                          class="form-control local_amount" readonly />
                                    </td>
                                    
                                    <td>
                                       <input 
                                          type="text" 
                                          id="remarks_<?php echo $i; ?>" name="remarks[]" 
                                          value="<?php echo $value->remarks; ?>"
                                          class="form-control remarks" 
                                          readonly />
                                    </td>
                                    
                                 </tr>
                                 <?php
                           ++$i;
               }
               ?>
                              </tbody>
                           </table>
                        </div>
                     </div>

                  </div>
                  <div class="card-footer">
                     <a href="/accounts_receivable/" class="btn btn-info btn-sm">Cancel</a>                  
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
               <input type="hidden" id="entry_id" />

               <div class="row mt-10">
                  <div class="col-6">
                     <label class="control-label">Entry Type <span class="cl-red">*</span></label>
                  </div>
                  <div class="col-6">
                     <input type="radio" id="entry_debit" name="entry_type" value="+" class="radio-inp" autocomplete="off" checked="checked">
                     <label class="radio-lbl" for="entry_debit">DEBIT</label>
                     <input type="radio" id="entry_credit" name="entry_type" value="-" class="radio-inp" autocomplete="off">
                     <label class="radio-lbl" for="entry_credit">CREDIT</label>
                  </div>
               </div>

               <hr />

               <div class="row mt-10">
                  <div class="col-6">
                     <label class="control-label">Date <span class="cl-red">*</span></label>
                     <input 
                        type="text" id="doc_date" 
                        class="form-control dp_full_date w-120" />
                  </div>
                  <div class="col-6">
                     <label class="control-label">Reference <span class="cl-red">*</span></label>
                     <input 
                        type="text" id="ref_no" 
                        class="form-control" />
                     <span class="double_ref error" style="display: none">Duplicate reference disallowed</span>
                     <input type="hidden" id="original_ref_no" />
                  </div>
               </div>
               
               <div class="row mt-10">
                  <div class="col-6">
                     <label class="control-label">Amount <span class="f_curr" style="font-weight: bold"></span> <span class="cl-red">*</span></label>
                     <input 
                        type="number" id="foreign_amount" 
                        class="form-control" />
                  </div>
                  <div class="col-6 dv_local" style="display: none">
                     <label class="control-label">Amount <strong>(SGD)</strong> <span class="cl-red">*</span></label>
                     <input 
                        type="number" id="local_amount"
                        class="form-control" />
                  </div>
               </div>

               <div class="row mt-10">
                  <div class="col-md-12 col-12">
                     <label class="control-label">Remarks</label>
                     <input 
                        type="text" id="remarks" 
                        class="form-control" />
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#entryModal">Cancel</button>
               <button type="button" class="btn btn-info btn-sm float-right" id="btn_save_entry">Done</button>
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

var row_number;
$(function() { // document starts

   $('select').select2();

   var customer_code = $("#customer").val();
   $("#customer").select2('destroy');

   // EDIT
   $(document).on('click', '.dt_edit', function () {

      row_number = $(this).closest('tr').attr('id');

      $('#entry_id').val($('#entry_id_'+row_number).val());

      if($('#sign_'+row_number).val() == "+") {
         $('#entry_debit').prop("checked", true);
      } else if($('#sign_'+row_number).val() == "-") {
         $('#entry_credit').prop("checked", true);
      }

      $('#doc_date').val($('#doc_date_'+row_number).val());
      $('#ref_no').val($('#ref_no_'+row_number).val());
      $('#original_ref_no').val($('#ref_no_'+row_number).val());

      $('#foreign_amount').val($('#foreign_amount_'+row_number).val());
      $('#local_amount').val($('#local_amount_'+row_number).val());
      $('#remarks').val($('#remarks_'+row_number).val());

      if($('#customer_currency').val() == "SGD") {
         $('.f_curr').html('');
         $('.f_curr, .dv_local').hide();
      } else {
         $('.f_curr').html('('+$('#customer_currency').val()+')');
         $('.f_curr, .dv_local').show();
      }

      $('#edit_id').val(row_number);
      $('#entryModal').modal('show');
   });

   var double_ref = 0;
   $(document).on("change", "#ref_no", function() {
      var current_ref = $(this).val();
      var original_ref = $('#original_ref').val();
      double_ref = 0;
      $(".double_ref").hide();

      if(current_ref !== "" && current_ref !== original_ref) {

         $.post('/accounts_receivable/ajax/double_ref', {
            customer_code: customer_code,
            ref_no: current_ref
         }, function(ref) {
            if(parseInt(ref) == 0) {
               $(".double_ref").hide();
               double_ref = 0;

            } else {
               $(".double_ref").show();
               double_ref = 1;
            }
         });
         
      } else {
         double_ref == 0;
      }
   });

   $(document).on("keyup", "#foreign_amount", function() {
      if($(this).val() !== "") {
         get_local_amount();
      }
   });

   $(document).on("change", "#foreign_amount, #local_amount", function() {
      var amount = 0;
      if($.trim($(this).val()) !== "") {
         var amount = parseFloat($(this).val()).toFixed(2);
         $(this).val(amount);
      }
   });

   // add entry to transaction
   $("#btn_save_entry").on('click', function () {
      if(!isFormValid() || !isModalValid()) {
         return false;
      }

      if(double_ref == 1) {
         $('#ref_no').focus();
         return false;
      }

      manage_entry();
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
                  $.post('/accounts_receivable/ajax/delete_ar_ob_entry', {
                     customer_code: customer_code,
                     ar_id: $('#entry_id_'+row_number).val()
                  }, function (status) {
                     if($.trim(status) == 'deleted') {
                        toastr.success("Entry deleted!");
                        $('tr#'+row_number).remove();
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

   // submit
   $("#btn_submit").on('click', function () {
      if($('#tbl_items > tbody > tr').length == 0) {
         return false;
      }

      $('#form_').submit();
   });

}); // document ends  


function clear_inputs() {
   $('#entry_id').val('');
   $('#edit_id').val('');

   $('#doc_date').val('');
   
   $('#ref_no').val('');
   $(".double_ref").hide();

   $('#foreign_amount').val('');
   $('#local_amount').val('');
}

function isFormValid() {
   var valid = true;
   if($('#customer').val() == "") {
      $("#customer").select2('open');
      valid = false;
   }
   return valid;
}

function isModalValid() {
   var valid = true;
   if($('#doc_date').val() == "") {
      $('#doc_date').focus();
      valid = false;
   } else if($('#ref_no').val() == "") {
      $('#ref_no').focus();
      valid = false;
   } else if($('#foreign_amount').val() == "") {
      $('#foreign_amount').focus();
      valid = false;
   } else if($("#customer_currency").val() == "SGD" && $('#local_amount').val() == "") {
      $('#local_amount').focus();
      valid = false;
   }
   return valid;
}

function manage_entry() {

   $row = $('tr[id="'+$("#edit_id").val()+'"]');

   $row.find('input.entry_id').val($('#entry_id').val());
   var entry_type = $("input[name='entry_type']:checked").val();
   if(entry_type == "+") {
      $row.find('input.entry_type').val("Debit");
   } else {
      $row.find('input.entry_type').val("Credit");
   }
   $row.find('input.sign').val(entry_type);

   $row.find('input.doc_date').val($('#doc_date').val());
   $row.find('input.ref_no').val($('#ref_no').val());

   $row.find('input.foreign_amount').val($('#foreign_amount').val());
   $row.find('input.local_amount').val($('#local_amount').val());

   $row.find('input.remarks').val($('#remarks').val());

   $('#entryModal').modal('hide');
}

function get_local_amount() {
   var exchange_rate = $("#currency_rate").val();
   var foreign_amount = $('#foreign_amount').val();
   
   var local_amount = 0;

   if(exchange_rate !== "" && foreign_amount !== "") {
      local_amount = Number(foreign_amount) / Number(exchange_rate);
      $('#local_amount').val(local_amount.toFixed(2));
   } else {
      $('#local_amount').val('');
   }
}
</script>
