<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">EZ Entry</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">EZ Entry</li>
               <li class="breadcrumb-item active">Payment</li>
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
               <input type="hidden" id="process" name="process" />
               <input type="hidden" id="edit_id" name="edit_id" />
               <input type="hidden" id="original_ref_no" value="<?php echo $ref_no; ?>" />
               <input type="hidden" id="page" value="<?php echo $page; ?>" />

               <form autocomplete="off" id="frm_" method="post">                  

                  <div class="card card-default">
                     <div class="card-header">
                        <h5 style="float: left; margin-top: 8px;">Other Payment</h5>
                        <a href="/ez_entry/other_payment" class="btn btn-outline-dark btn-sm float-right">
                           <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                        </a>
                     </div>
                     <div class="card-body">
                        <div class="row">
                           <div class="col-md-6 col-12">
                              <div class="row form-group">
                                    <label class="control-label col-md-4">Date<span class="cl-red">*</span></label>
                                    <div class="col-md-8">
                                       <input 
                                          type="text" 
                                          id="doc_date" name="doc_date" 
                                          value="<?php echo $page == 'edit' ? date('d-m-Y', strtotime($doc_date)) : ''; ?>"
                                          class="form-control dp_full_date doc_date w-120" placeholder="dd-mm-yyyy" />
                                    </div>
                              </div>
                           </div>

                           <div class="col-md-6 col-12">
                              <div class="row form-group">
                                    <label class="control-label col-md-4">Reference<span class="cl-red">*</span></label>
                                    <div class="col-md-8">
                                       <input 
                                          type="text" 
                                          id="ref_no" name="ref_no" 
                                          value="<?php echo $ref_no; ?>"
                                          maxlength="12" class="form-control ref_no w-180">
                                       <span class="error-ref error" style="display: none;">Duplicate reference disallowed</span>
                                    </div>
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6 col-12">
                              <div class="row form-group">
                                 <label class="control-label col-md-4">Remarks</label>
                                 <div class="col-md-8">
                                    <input 
                                       type="text" 
                                       id="remarks" name="remarks" 
                                       value="<?php echo $remarks; ?>"
                                       maxlength="250" class="form-control w-350" />
                                 </div>
                              </div>
                           </div>
                        </div>

                        <br />
                        <div class="row">
                           <div class="col-md-12 table-responsive">
                              <table id="tbl_items" class="table table-custom" style="width: 700px; display: <?php echo $page == 'edit' ? '' : 'none'; ?>">
                                 <thead>
                                    <tr>
                                       <th>Action</th>
                                       <th>Account $</th>
                                       <th class="txt-right">Amount</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                 <?php
                                 if ($page == 'edit') {
                                       $i = 0;
                                       $this->db->select('*');
                                       $this->db->from('ez_payment');
                                       $this->db->where(['doc_date' => $doc_date, 'ref_no' => $ref_no]);
                                       $query = $this->db->get();
                                       $payment_entries = $query->result();
                                       foreach ($payment_entries as $value) { ?>
                                    <tr id="<?php echo $i; ?>">

                                       <td class="w-140">
                                          <input 
                                             type="hidden" 
                                             id="entry_id_<?php echo $i; ?>" name="entry_id[]" 
                                             value="<?php echo $value->batch_id; ?>"
                                             class="entry_id" />
                                          
                                          <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                                          <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                                       </td>

                                       <td>
                                          <input 
                                             type="hidden" 
                                             id="accn_<?php echo $i; ?>" name="accn[]" 
                                             value="<?php echo $value->accn; ?>"
                                             class="accn" />
                                          
                                          <?php $accn_desc = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]); ?>
                                          <input 
                                             type="text" 
                                             id="accn_desc_<?php echo $i; ?>" 
                                             value="<?php echo $value->accn.' : '.$accn_desc; ?>"
                                             class="form-control-dsply accn_desc" readonly />
                                          
                                          <input 
                                             type="hidden" 
                                             id="gst_type_<?php echo $i; ?>" name="gst_type[]"
                                             value="<?php echo $value->gst_type; ?>" class="gst_type" />

                                          <input 
                                             type="hidden" 
                                             id="gst_category_<?php echo $i; ?>" name="gst_category[]" 
                                             value="<?php echo $value->gst_category; ?>" class="gst_category" />

                                          <input 
                                             type="hidden" 
                                             id="net_purchase_<?php echo $i; ?>" name="net_purchase[]" 
                                             value="<?php echo $value->net_amount; ?>" class="net_purchase" />
                                          
                                          <input 
                                             type="hidden" 
                                             id="gst_amount_<?php echo $i; ?>" name="gst_amount[]" 
                                             value="<?php echo $value->gst_amount; ?>" class="gst_amount" />

                                          <?php $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $value->gst_category]); ?>
                                          <input 
                                             type="hidden" 
                                             id="gst_rate_<?php echo $i; ?>" 
                                             value="<?php echo $gst_rate; ?>" class="gst_rate" />
                                       </td>
                                       
                                       <td class="w-180">
                                          <input 
                                             type="number" 
                                             id="amount_<?php echo $i; ?>" name="amount[]" 
                                             value="<?php echo $value->total_amount; ?>"
                                             class="form-control-dsply txt-right amount" readonly />
                                       </td>
                                    </tr>
                                    <?php
                                       ++$i;
                                       }
                                 } ?>
                                 </tbody>
                                 <tfoot>
                                    <tr>
                                       <td></td>
                                       <td style="text-align: right; color: blue; font-style: italic">Sub Total</td>
                                       <td class="sub_total" style="color: gray; font-weight: bold; text-align: right">0.00</td>
                                    </tr>
                                 </tfoot>
                              </table>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-12">
                              <a href="#" class="btn_add btn btn-outline-danger btn-sm" style="margin-right: 10px"><i class="fa-solid fa-plus"></i> ADD ENTRY</a>
                           </div>
                        </div>

                        <div class="dv_bank" style="display: <?php echo $page == 'edit' ? '' : 'none'; ?>">
                           <br />
                           <hr />

                           <div class="row form-group">
                              <label for="bank" class="control-label col-md-2">Bank<span class="cl-red">*</span></label>
                              <div class="col-md-4">
                                 <select id="bank" name="bank" class="form-control w-350">
                                    <?php echo $banks; ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="card-footer">
                        <button type="button" id="btn_save_to_batch" class="btn btn-primary btn-sm">SAVE TO BATCH</button>
                        <button type="button" id="btn_post_to_accounts" class="btn btn-info btn-sm float-right">POST TO ACCOUNTS</button>
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
         <td class="w-140">
            <input 
               type="hidden" 
               id="entry_id_0" name="entry_id[]" 
               class="entry_id" />
            
            <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
            <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
         </td>
         
         <td>            
            <input 
               type="hidden" 
               id="accn_0" name="accn[]" 
               class="accn" />

            <input 
               type="text" 
               id="accn_desc_0"  
               class="form-control-dsply accn_desc" readonly />

               <input type="hidden" id="gst_type_0" name="gst_type[]" class="gst_type" />
               <input type="hidden" id="gst_category_0" name="gst_category[]" class="gst_category" />
               <input type="hidden" id="net_purchase_0" name="net_purchase[]" class="net_purchase" />
               <input type="hidden" id="gst_amount_0" name="gst_amount[]" class="gst_amount" />

               <input type="hidden" id="gst_rate_0" class="gst_rate" />
         </td>
         
         <td>
            <input 
               type="number" 
               id="amount_0" name="amount[]" 
               class="form-control-dsply txt-right amount" readonly />
         </td>
      </tr>
   </tbody>
</table>


<!-- Transaction Modal -->
<div id="entryModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-body">
               <input type="hidden" id="entry_id" name="entry_id" />

               <div class="row mt-10">
                  <div class="col-12">
                     <label class="control-label">Chart Of Account <span class="cl-red">*</span></label>
                     <select id="accn" class="form-control">
                        <?php echo $co_accns; ?>
                     </select>
                  </div>
               </div>
               
               <hr />

               <div class="row mt-10 gst_type_field" style="display: none">
                  <div class="col-4">
                     <label class="control-label">GST Type <span class="cl-red">*</span></label>
                  </div>
                  <div class="col-8">
                     <input type="radio" id="input_tax" name="gst_type" value="I" class="radio-inp" autocomplete="off">
                     <label class="radio-lbl" for="input_tax">INPUT TAX</label>
                     <input type="radio" id="settlement_tax" name="gst_type" value="S" class="radio-inp" autocomplete="off">
                     <label class="radio-lbl" for="settlement_tax">SETTLEMENT</label>

                     <input type="hidden" id="gst_type" />
                  </div>
               </div>

               <div class="row mt-10 amount_field" style="display: none">
                  <div class="col-6">
                     <label class="control-label">Amount <span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="amount" class="form-control" />
                  </div>
               </div>

               <div class="row mt-10 gst_input_field" style="display: none">
                  <div class="col-6">
                     <label class="control-label">Net Purchase $ <span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="net_purchase" class="form-control" />
                  </div>
               </div>

               <div class="row mt-10 gst_input_field" style="display: none">
                  <div class="col-12">
                     <label class="control-label">GST Category <span class="cl-red">*</span></label>
                     <select id="gst_category" class="form-control">
                        <?php echo $input_gsts; ?>
                     </select>
                     <input type="hidden" id="gst_rate" />
                  </div>
               </div>

               <div class="row mt-10 gst_input_field" style="display: none">
                  <div class="col-6">
                     <label class="control-label">GST Amount $ <span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="gst_amount" class="form-control" />
                  </div>
               </div>

            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#entryModal">Cancel</button>
               <button type="button" class="btn btn-info btn-sm float-right" id="btn_save_entry">SAVE</button>
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
   var row_number = '';
   var duplicate_ref = false;

   // document starts
   $(function() {

      $('select').select2();

      if($('#page').val() == "edit") {
         process_total();
      }

      $(document).on("change", "#doc_date", function() {
         if($(this).val() == "") {
            return false;
         }

         if($('#tbl_items > tbody > tr').length > 0) {
            save_payment(); // if there is any changes in the date when we do submit, this will save all the records with updated ref once again
         }
      });
      
      $(document).on("change", "#ref_no", function() {
         var ref_no = $(this).val();
         if($(this).val() == "") {
            return false;
         }         
         
         // if page is edit and user try changing different ref and again changing to same one
         if(ref_no == $('#original_ref_no').val()) {
            return false;
         }

         $(".error-ref").hide();
         duplicate_ref = false;

         $.post('/ez_entry/ajax/double_payment', {
            ref_no: $(this).val()
         }, function(ref) {
            if (ref > 0) {
               duplicate_ref = true;
               $("#ref_no").focus();
               $(".error-ref").show();
            } else {
               if($('#tbl_items > tbody > tr').length > 0) {
                  save_payment(); // if there is any changes in the ref when we do submit, this will save all the records with updated ref once again
               }
            }
         });
      });
      
      $(".btn_add").on('click', function() {
         if(!isFormValid()) {
            return false;
         } else if(duplicate_ref) {
            $('#ref_no').focus();
            return false;
         }

         $('#process').val('add');

         clear_inputs();
         $('#entryModal').modal('show');
      });      

      $(document).on('change', '#accn', function() {
         var accn = $('option:selected', this).val();

         $('.gst_type_field, .gst_input_field, .amount_field').hide();
         $('#input_tax').prop("checked", false);
         $('#settlement_tax').prop("checked", false);
         $('#net_purchase').val("");
         $('#gst_category').select2("destroy").val("").select2();
         $('#gst_rate').val("");
         $('#gst_amount').val("");
         $('#amount').val("");

         if(accn !== "") {
            if(accn == "CL300") {
               $('.gst_type_field').show();
            } else {
               $('.amount_field').show();
            }
         }
      });

      $('input[type=radio][name=gst_type]').change(function() {
         $('#net_purchase').val("");
         $('#gst_category').select2("destroy").val("").select2();
         $('#gst_rate').val("");
         $('#gst_amount').val("");
         $('#amount').val("");
         $('#gst_type').val("");
         
         $('.gst_input_field, .amount_field').hide();

         if (this.value == 'I') {
            $('#gst_type').val("I");
            $('.gst_input_field').show();
         } else if (this.value == 'S') {
            $('#gst_type').val("S");
            $('.amount_field').show();
            $('#amount').focus();
         }
      });

      $(document).on("change", "#net_purchase", function() {
         if($(this).val() !== "") {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);

            if($('#gst_rate').val() !== "") {
               get_gst_amount();
            }
         }
      });

      $(document).on('change', '#gst_category', function() {
         var code = $('option:selected', this).val();
         $('#gst_rate').val("");
         if(code !== "") {
            $.post('/ez_entry/ajax/get_gst_details', {
               gst_code: code
            }, function (data) {
               if(data !== "") {
                  var obj = $.parseJSON(data);
                  $('#gst_rate').val(Number(obj.gst_percentage));
                  
                  if($('#net_purchase').val() !== "") {
                     get_gst_amount();
                  }
               }
            });
         }
      });

      $(document).on("change", "#amount, #gst_amount", function() {
         var amount = 0;
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);
         }
      });

      $(document).on('click', '.dt_edit', function() {

         if(!isFormValid() || duplicate_ref) {
            return;
         }

         $('#input_tax').prop("checked", false);
         $('#settlement_tax').prop("checked", false);
         $('#net_purchase').val("");
         $('#gst_category').select2("destroy").val("").select2();
         $('#gst_rate').val("");
         $('#gst_amount').val("");
         $('#amount').val("");
         $('#gst_type').val("");
         
         $('.gst_type_field, .gst_input_field, .amount_field').hide();

         row_number = $(this).closest('tr').attr('id');
         $('#entry_id').val($('#entry_id_'+row_number).val());

         $('#accn').select2("destroy");
         $('#accn').val($('#accn_'+row_number).val());
         $('#accn').select2();

         if($('#accn_'+row_number).val() == "CL300") {

            $('#gst_type').val($('#gst_type_'+row_number).val());
            $('.gst_type_field').show();

            if($('#gst_type').val() == "I") {
               
               $('#input_tax').prop("checked", true);
               
               $('.gst_input_field').show();
               
               $('#net_purchase').val($('#net_purchase_'+row_number).val());

               $('#gst_category').select2("destroy");
               $('#gst_category').val($('#gst_category_'+row_number).val());
               $('#gst_category').select2();

               $('#gst_rate').val($('#gst_rate_'+row_number).val());
               $('#gst_amount').val($('#gst_amount_'+row_number).val());

            } else if($('#gst_type').val() == "S") {
               
               $('#settlement_tax').prop("checked", true);
               $('.amount_field').show();
               $('#amount').val($('#amount_'+row_number).val());
            }

            
         } else {
            $('.amount_field').show();
            $('#amount').val($('#amount_'+row_number).val());
         }

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
                     $.post('/ez_entry/ajax/delete_payment_entry', {
                        entry_id: $('#entry_id_'+row_number).val()
                     }, function (status) {
                        if($.trim(status) == 'deleted') {
                           toastr.success("Entry deleted!");
                           $('tr#'+row_number).remove();

                           if($('#tbl_items > tbody > tr').length > 0) {
                              sortTblRowsByID();
                           } else {
                              $('#tbl_items').hide();
                              $('.dv_bank').hide();
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

      // add entry to transaction
      $("#btn_save_entry").on('click', function () {
         if(!isFormValid() || !isModalValid()) {
            return false;
         }

         row_number = '';
         save(row_number);
      });

      // save to batch
      $("#btn_save_to_batch").on('click', function () {

         if($('#bank').val() == "") {
            $("#bank").select2('open');
            return false;
         }

         if($('#tbl_items > tbody > tr').length > 0 && isFormValid()) {
            toastr.success("Payment Saved!");
            window.location.href = '/ez_entry/other_payment';
         }
      });

      // post to GL
      $("#btn_post_to_accounts").on('click', function () {
         if($('#bank').val() == "") {
            $("#bank").select2('open');
            return false;
         }

         if($('#tbl_items > tbody > tr').length > 0 && isFormValid()) {
            $.confirm({
               title: '<i class="fa fa-info"></i> Confirm POST to GL',
               content: 'Are you sure to Post?</strong>',
               buttons: {
                  yes: {
                     btnClass: 'btn-warning',
                     action: function() {
                        post();
                     }
                  },
                  no: {
                     btnClass: 'btn-dark',
                     action: function(){
                     }
                  },
               }
            });
         }
      });

   }); // document ends

   function clear_inputs() {
      $('#entry_id').val('');
      $('#edit_id').val('');

      $('#accn').select2("destroy").val('').select2();

      $('#net_purchase').val("");
      $('#gst_category').select2("destroy").val("").select2();
      $('#gst_rate').val("");
      $('#gst_amount').val("");
      $('#amount').val("");
      $('#gst_type').val("");
      $('.gst_type_field, .gst_input_field, .amount_field').hide();
   }

   function isFormValid() {
      var valid = true;
      if($('#doc_date').val() == "") {
         $('#doc_date').focus();
         valid = false;
      } else if($('#ref_no').val() == "") {
         $('#ref_no').focus();
         valid = false;
      }

      return valid;
   }

   function isModalValid() {
      var valid = true;
     
      if($('#accn').val() == "") {
         $("#accn").select2('open');
         valid = false;
      
      } else if($('#accn').val() == "CL300" && $("input[type='radio'][name='gst_type']:checked").length == 0) {
         console.log(">>> 1 >>> ");
         $("input[type='radio'][name='gst_type']").focus();
         valid = false;
      
      } else if($("input[type='radio'][name='gst_type']:checked").val() == "I" && $('#net_purchase').val() == "") {
         console.log(">>> 2 >>> ");
         $('#net_purchase').focus();
         valid = false;
      
      } else if($("input[type='radio'][name='gst_type']:checked").val() == "I" && $('#gst_category').val() == "") {
         console.log(">>> 3 >>> ");
         $("#gst_category").select2('open');
         valid = false;
      
      } else if($("input[type='radio'][name='gst_type']:checked").val() == "I" && $('#gst_amount').val() == "") {
         console.log(">>> 4 >>> ");
         $('#gst_amount').focus();
         valid = false;
      
      } else if($("input[type='radio'][name='gst_type']:checked").val() == "S" && $('#amount').val() == "") {
         console.log(">>> 5 >>> ");
         $('#amount').focus();
         valid = false;
      
      } else if($('#accn').val() !== "CL300" && $('#amount').val() == "") {
         console.log(">>> 6 >>> ");
         $('#amount').focus();
         valid = false;
      }

      return valid;
   }

   function get_gst_amount() {
      var net_purchase = $('#net_purchase').val();
      var gst_rate = $('#gst_rate').val();
      var gst_amount = Math.round(net_purchase * gst_rate) / 100;
      $('#gst_amount').val(parseFloat(gst_amount).toFixed(2));
   }   

   function process_total() {
      var sub_total = 0;
      $('.amount').each(function () {
         sub_total += Number($(this).val());
      });

      $('.sub_total').html(sub_total.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
   }

   function save(row_number) {
      var entry_id = $("#entry_id"+ row_number).val();

      var doc_date = $("#doc_date").val();
      var ref_no = $("#ref_no").val();
      var remarks = $("#remarks").val();

      var accn = $("#accn"+row_number).val();
      var amount = $("#amount"+row_number).val();

      var gst_type = $("#gst_type"+row_number).val();
      var net_purchase = 0;
      var gst_category = "";
      var gst_amount = 0;

      if(accn == "CL300" && gst_type == "I") {
         net_purchase = $('#net_purchase'+row_number).val();
         gst_category = $('#gst_category'+row_number).val();
         gst_amount = $('#gst_amount'+row_number).val();
         amount = $("#gst_amount"+row_number).val();
      }

      $.post('/ez_entry/ajax/save_payment', {
         entry_id: entry_id,
         doc_date: doc_date,
         ref_no: ref_no,
         remarks: remarks,
         accn: accn,         
         amount: amount,
         gst_type: gst_type,
         gst_category: gst_category,
         net_amount: net_purchase,
         gst_amount: gst_amount,
         bank: $('#bank').val()
      }, function(entry_id) {
         if(entry_id !== "") {
            $("#entry_id"+row_number).val($.trim(entry_id));

            if($('#process').val() == 'add' || $('#process').val() == 'edit') { // add / edit
               manage_entry();
            }
         }
      });
   }

   function manage_entry() {
      if($('#process').val() == 'add') { // New Row
         $row = $("#tbl_clone tbody tr").clone();
      } else if($('#process').val() == "edit") { // Existing Row
         $row = $('tr[id="'+$("#edit_id").val()+'"]');
      }

      $row.find('input.entry_id').val($('#entry_id').val());
      $row.find('input.accn').val($('#accn').val());
      $row.find('input.accn_desc').val($("#accn>option:selected").text());
      $row.find('input.gst_type').val($('#gst_type').val());
      $row.find('input.gst_category').val($('#gst_category').val());
      $row.find('input.gst_rate').val($('#gst_rate').val());
      $row.find('input.net_purchase').val($('#net_purchase').val());
      $row.find('input.gst_amount').val($('#gst_amount').val());

      $row.find('input.amount').val($('#amount').val());
      if($('#accn').val() == "CL300" && $('#gst_type').val() == "I") {
         $row.find('input.amount').val($('#gst_amount').val());
      }

      if($('#process').val() == "add") {
         // append new row to the table
         $('#tbl_items').append($row);
         sortTblRowsByID();
      }
      
      process_total();
      
      $('#tbl_items').show();
      $('.dv_bank').show();      

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

   function save_payment() {
      $('#process').val("");
      $("#tbl_items tbody tr").each(function () {
         row_number = '_'+$(this).attr('id');
         save(row_number);
      });
   }

   function post() {
      row_number = $("#tbl_items>tbody>tr:first").attr('id');
      $.post('/ez_entry/ajax/post_payment', {
         rowID: $('#entry_id_'+row_number).val()
      }, function (status) {
         if($.trim(status) == 'posted') {
            toastr.success("Post Success!");
            window.location.href = '/ez_entry/other_payment';
         } else {
            toastr.error("Post Error!");
         }
      });
   }
</script>
