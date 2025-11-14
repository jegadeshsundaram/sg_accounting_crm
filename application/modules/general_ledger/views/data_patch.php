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
            <input type="hidden" id="edit_id" />
            <input type="hidden" id="total_debits_credits" />
            <input type="hidden" id="iden_exist" value="<?php echo $iden_exist; ?>" />

            <form autocomplete="off" id="frm_" method="post">
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
                        <?php if ($iden_exist) { ?>
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
                        <?php } else { ?>
                           <input type="hidden" name="iden" id="iden" value="" />
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
                           <table id="tbl_" class="table" style="width: 800px;">
                              <thead>
                                 <tr>
                                    <th class="w-80">Action</th>
                                    <th>Account</th>
                                    <th class="w-150 txt-right">Debit Amount $</th>
                                    <th class="w-150 txt-right">Credit Amount $</th>
                                 </tr>
                              </thead>
                              <tbody>
                              <?php
                                 $i = 0;
                                 $where = ['ref_no' => $ref_no, 'tran_type' => $tran_type];
                                 if($supplier !== "") {
                                    $where = ['ref_no' => $ref_no, 'iden' => $supplier, 'tran_type' => $tran_type];                                    
                                 }
                                 $gl_data = $this->custom->getRows('gl', $where);
                                 foreach ($gl_data as $value) {
                                    $amount = $value->total_amount;
                                    ++$i;
                                 ?>

                                 <tr id="<?php echo $i; ?>">
                                    <td style="width: 50px">
                                       <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>

                                       <!-- Hidden Fields -->
                                       <input type="hidden" id="gl_id_<?php echo $i; ?>" name="gl_id[]" value="<?php echo $value->gl_id; ?>" class="gl_id" />
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
                                          id="dr_amount_<?php echo $i; ?>" name="dr_amount[]" 
                                          value="<?php echo $value->sign == '+' ? $amount : ''; ?>"
                                          class="form-control-dsply txt-right dr_amount" readonly />
                                    </td>

                                    <td>
                                       <input 
                                          type="number" 
                                          id="cr_amount_<?php echo $i; ?>" name="cr_amount[]" 
                                          value="<?php echo $value->sign == '-' ? $amount : ''; ?>"
                                          class="form-control-dsply txt-right cr_amount" readonly />
                                    </td>
                                    
                                 </tr>

                                 <?php ++$i; } ?>
                              </tbody>

                              <tfoot>
                                 <tr>
                                    <td></td>
                                    <td style="text-align: right; color: blue;">Sub Total</td>
                                    <td style="text-align: right; font-weight: bold" id="debits_total">$0.00</td>
                                    <td style="text-align: right; font-weight: bold" id="credits_total">$0.00</td>
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
               <input type="hidden" id="sign" />

               <div class="row mt-10">
                  <label class="col-12 control-label">Account <span class="cl-red">*</span></label>
                  <div class="col-12">
                     <select id="coa" class="form-control">
                        <?php echo $coa_options; ?>
                     </select>
                  </div>
               </div>

               <div class="row mt-10">
                  <div class="col-6">
                     <label class="control-label">Entry <span class="cl-red">*</span></label>
                     <input 
                        type="text" id="entry_type" 
                        class="form-control w-100" readonly />
                  </div>
                  <div class="col-6">
                     <label class="control-label">Amount <span class="cl-red">*</span></label>
                     <input 
                        type="number" id="amount" 
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
   <div class="modal-dialog modal-confirm modal-sm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <span style="margin: 0; display: block;"><span style="color: red;">INVALID!</span> Total of Debit and Credit are Not Equal</span>
            </div>
         </div>
         <div class="card-body">
            <div class="row mt-10">
               <label class="col-7 control-label">Debit Total</label>
               <div class="col-5 txt-right" id="debit_total"></div>
            </div>
            <div class="row mt-10">
               <label class="col-7 control-label">Credit Total</label>
               <div class="col-5 txt-right" id="credit_total"></div>
            </div>
            <hr />
            <div class="row mt-10" style="color: blue;">
               <label class="col-7 control-label">Total Difference </label>
               <div class="col-5 txt-right" id="diff_amount"></div>
            </div>
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
   
      calculate_debits_credits(false);

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
         $('#sign').val($('#sign_'+row_number).val());
         
         $('#coa').select2("destroy");
         $('#coa').val($('#coa_'+row_number).val());
         $('#coa').select2();
         
         if($('#sign').val() == "+") {
            $('#amount').val($('#dr_amount_'+row_number).val());
            $('#entry_type').val("Debit");
         } else if($('#sign').val() == "-") {
            $('#amount').val($('#cr_amount_'+row_number).val());
            $('#entry_type').val("Credit");
         }

         $('#edit_id').val(row_number);
         $('#entryModal').modal('show');
      });

      $(document).on("change", "#amount", function() {
         var amount = 0;
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);
         }
      });

      // add entry to transaction
      $("#btn_save_entry").on('click', function () {
         if(!isFormValid() || !isModalValid()) {
            return false;
         }       

         manage_entry();
      });
      
      $('#btn_submit').on('click', function() {

         if(double_ref == 1) {
            $('#ref_no').focus();
            return false;
         }

         calculate_debits_credits(true);

      });

      $('#btn-confirm-yes').on('click', function() {
         var url = "/general_ledger/save_patched_data";
         $("#frm_").attr("action", url);         
         $("#frm_").submit();

         $("#confirmSubmitModal").modal('hide');
      });     

   }); // document ends

   function isFormValid() {
      var valid = true;
      if($('#doc_date').val() == "") {
         $('#doc_date').focus();
         valid = false;

      } else if($('#ref_no').val() == "") {
         $('#ref_no').focus();
         valid = false;

      } else if($('#iden_exist').val() && $('#iden').val() == "") {
         $("#iden").select2('open');
         valid = false;
      }

      return valid;
   }

   function isModalValid() {
      var valid = true;
      if($('#coa').val() == "") {
         $("#coa").select2('open');
         valid = false;

      } else if($('#amount').val() == "") {
         $("#amount").focus();
         valid = false;
      }

      return valid;
   }

   function manage_entry() {
      $row = $('tr[id="'+$("#edit_id").val()+'"]');

      $row.find('input.gl_id').val($('#gl_id').val());

      $row.find('input.coa').val($('#coa').val());
      $row.find('input.coa_desc').val($("#coa>option:selected").text());

      if($('#sign').val() == "+") {
         $row.find('input.dr_amount').val($('#amount').val());
      } else if($('#sign').val() == "-") {
         $row.find('input.cr_amount').val($('#amount').val());
      }      

      calculate_debits_credits();

      $('#entryModal').modal('hide');
   }

   function calculate_debits_credits(submit) {
      var debit_total = 0;
      var credit_total = 0;

      $('.dr_amount').each(function (index, element) {
         var debit_current = $(this).val();
         debit_total = Number(debit_total) + Number(debit_current);
      });
      $('#debits_total').html(debit_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + " <span style='color: sandybrown; font-weight: bold; display: none'>DR</span>");

      $('.cr_amount').each(function (index, element) {
         var credit_current = $(this).val();
         credit_total = Number(credit_total) + Number(credit_current);
      });
      $('#credits_total').html(credit_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + " <span style='color: darkseagreen; font-weight: bold; display: none'>CR</span>");      

      if(submit) {
         var diff_amount = debit_total.toFixed(2) - credit_total.toFixed(2);

         console.log("Diff Amount >> "+diff_amount);
         console.log("Credit Amount >> "+credit_total);
         console.log("Debit Amount >> "+debit_total);

         if(diff_amount == 0) {

            $('#confirmSubmitModal .modal-title').html("Confirm ALERT");
            $('#confirmSubmitModal .modal-body').html("Patching will be automatic for AR, AP & Foreign Bank. However, If you made any changes to GST Amount, You must patch GST Datafile separetely.");
            $("#confirmSubmitModal").modal();

         } else {

            debit_total = debit_total.toFixed(2);
            credit_total = credit_total.toFixed(2);

            $('#debit_total').html("$"+debit_total);
            $('#credit_total').html("$"+credit_total);
            $('#diff_amount').html("$"+diff_amount.toFixed(2));
            $('#diff_amount').html($('#diff_amount').html().replace('-',''));

            $('#amountModal').modal();
            
         }
      }
   }
</script>