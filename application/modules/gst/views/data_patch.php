<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">GST</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">GST</li>
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
            <!-- Edit Id - hidden field --> 
            <input type="hidden" id="edit_id" />
            <input type="hidden" id="tran_type" value="<?php echo $tran_type; ?>" />

            <form autocomplete="off" id="form_" method="post">
               <div class="card card-default">
                  <div class="card-header">
                     <h5>Data Patch</h5>
                     <a href="/gst/" class="btn btn-outline-dark btn-sm float-right">
                        <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                     </a>
                  </div>
                  <div class="card-body">
                     <div class="row form-group">
                        <label for="doc_date" class="control-label col-md-3">Date : </label>
                        <div class="col-md-3">
                           <input 
                              type="text" 
                              id="doc_date" name="doc_date" 
                              value="<?php echo date('d-m-Y', strtotime($doc_date)); ?>"
                              class="form-control dp_full_date doc_date w-150" 
                              placeholder="dd-mm-yyyy" required />
                        </div>
                     </div>

                     <div class="row form-group">
                        <label for="ref_no" class="control-label col-md-3">Reference : </label><br />
                        <div class="col-md-3">
                           <input 
                              type="text" 
                              id="ref_no" name="ref_no" 
                              value="<?php echo $ref_no; ?>"
                              class="form-control ref_no w-150" 
                              maxlength="12" />
                           
                           <input type="hidden" id="original_ref_no" value="<?php echo $ref_no; ?>" />
                           <span id="ref_error" style="display: none; color: red;">Duplicate reference disallowed</span>
                        </div>
                     </div>

                     <div class="row form-group">
                        <label for="remarks" class="control-label col-md-3">Remarks : </label><br />
                        <div class="col-md-3">
                           <textarea id="remarks" name="remarks" class="form-control" maxlength="250"><?php echo $remarks; ?></textarea>
                        </div>
                     </div>

                     <br />
                     <div class="row form-group">
                        <div class="col-md-12 table-responsive">
                           <table id="tbl_items" class="table" style="min-width: 1400px; width: 100%;">
                              <thead>
                                 <tr>
                                    <th class="w-150">Action</th>
                                    <th>IDEN</th>
                                    <th class="w-180">Amount</th>
                                    <th>Category</th>
                                    <th class="w-120">Rate (%)</th>
                                    <th class="w-180">Tax Amount</th>
                                 </tr>
                              </thead>
                              <?php
                                 $i = 0;
                                 $gst_data = $this->custom->getRows('gst', ['dref' => $ref_no]);
                                 foreach ($gst_data as $value) {
                              ?>
                              <tbody>
                                 <tr id="<?php echo $i; ?>">
                                    <td>
                                       <input 
                                          type="hidden" 
                                          id="gst_id_<?php echo $i; ?>" name="gst_id[]" 
                                          value="<?php echo $value->gst_id; ?>" class="gst_id" />

                                       <input 
                                          type="hidden" 
                                          id="gst_type_<?php echo $i; ?>" name="gst_type[]" 
                                          value="<?php echo $value->gsttype; ?>" class="gst_type" />

                                       <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                                       <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                                    </td>
                                    
                                    <td>
                                       <?php 
                                          $iden_details = '';
                                          if ($value->iden == 'Input Tax' || $value->iden == 'Output Tax' || $value->iden == 'Reverse Input' || $value->iden == 'Reverse Output' || $value->iden == 'Settlement' || $value->iden == 'Rebate') { 
                                             $iden_details = $value->iden;
                                          } else {
                                             if ($value->gsttype == 'O') {
                                                $name = $this->custom->getSingleValue('master_customer', 'name', ['code' => $value->iden]);
                                                $iden_details = $name.' ('.$value->iden.')';
                                             } elseif ($value->gsttype == 'I') {
                                                $name = $this->custom->getSingleValue('master_supplier', 'name', ['code' => $value->iden]);
                                                $iden_details = $name.' ('.$value->iden.')';
                                             }
                                          }
                                       ?>
                                       <input 
                                          type="hidden" 
                                          id="iden_<?php echo $i; ?>" name="iden[]" 
                                          class="iden" value="<?php echo $value->iden; ?>" />
                                       
                                       <input 
                                          type="text" 
                                          id="iden_details_<?php echo $i; ?>" 
                                          class="form-control-dsply iden_details" value="<?php echo $iden_details; ?>" readonly />
                                    </td>

                                    <td>
                                       <?php if ($value->gsttype !== 'S' && $value->gsttype !== 'R') { ?>
                                          <input 
                                             type="number" 
                                             id="amount_<?php echo $i; ?>" name="amount[]" 
                                             class="form-control-dsply amount w-200" 
                                             value="<?php echo $value->amou; ?>" readonly />
                                       <?php } else { ?>
                                          <input 
                                             type="text" 
                                             id="amount_<?php echo $i; ?>" name="amount[]" 
                                             class="form-control-dsply amount w-200" 
                                             value="N/A" readonly />
                                       <?php } ?>
                                    </td>

                                    <td>
                                       <?php if ($value->gsttype !== 'S' && $value->gsttype !== 'R') { 
                                          $gstcate = $value->gstcate;
                                          $gstperc = $value->gstperc;
                                          $gst_details = $value->gstcate.' : '.$this->custom->getSingleValue('ct_gst', 'gst_description', ['gst_code' => $value->gstcate]);
                                       } else { 
                                          $gstcate = 'N/A';
                                          $gstperc = 'N/A';
                                          $gst_details = 'N/A';
                                       } ?>

                                       <input 
                                          type="hidden" 
                                          id="gst_category_<?php echo $i; ?>" name="gst_category[]" 
                                          class="gst_category" value="<?php echo $gstcate; ?>" />
                                       
                                          <input type="text" id="gst_details_<?php echo $i; ?>"
                                          class="form-control-dsply gst_details" value="<?php echo $gst_details; ?>"
                                    </td>
                                    
                                    <td>
                                       <input 
                                          type="text" 
                                          id="gst_percentage_<?php echo $i; ?>" name="gst_percentage[]" 
                                          class="form-control-dsply gst_percentage w-80" value="<?php echo $gstperc; ?>" 
                                          readonly />
                                    </td>

                                    <td>
                                       <input 
                                          type="number" 
                                          id="gst_amount_<?php echo $i; ?>" name="gst_amount[]" 
                                          class="form-control-dsply gst_amount w-150" 
                                          value="<?php echo $value->gstamou; ?>" readonly />
                                    </td>
                                 </tr>
                                 <?php
                          ++$i;
               }
               ?>
                           </table>
                        </div>
                     </div>
                  </div>
                  <div class="card-footer">
                     <a href="/gst" class="btn btn-info btn-sm">Cancel</a>
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
               <input type="hidden" id="gst_id" />
               <input type="hidden" id="gst_type" />               

               <div class="row mt-10">
                  <label class="col-12 control-label">IDEN <span class="cl-red">*</span></label>
                  <div class="col-12 iden" style="display: none">
                     <input type="text" id="iden" class="form-control" />
                  </div>
                  <div class="col-12 customer" style="display: none">
                     <select id="customer" class="form-control">
                        <?php echo $customers; ?>
                     </select>
                  </div>
                  <div class="col-12 supplier" style="display: none">
                     <select id="supplier" class="form-control">
                        <?php echo $suppliers; ?>
                     </select>
                  </div>
               </div>

               <div class="row mt-10 amount_field">
                  <div class="col-6">
                     <label class="control-label">Amount <span class="cl-red">*</span></label>
                     <input 
                        type="number" id="amount" 
                        class="form-control w-120" />
                  </div>
               </div>

               <div class="row mt-10 gst_category_field">
                  <label class="col-12 control-label">GST Category <span class="cl-red">*</span></label>
                  <div class="col-12 purchase_gst" style="display: none">
                     <select id="purchase_gst" class="form-control">
                        <?php echo $purchase_gsts; ?>
                     </select>
                  </div>
                  <div class="col-12 supply_gst" style="display: none">
                     <select id="supply_gst" class="form-control">
                        <?php echo $supply_gsts; ?>
                     </select>
                  </div>
               </div>
               
               <div class="row mt-10">
                  <div class="col-6 gst_percentage_field">
                     <label class="control-label">Rate (%) <span class="f_curr" style="font-weight: bold"></span> <span class="cl-red">*</span></label>
                     <input 
                        type="number" id="gst_percentage" 
                        class="form-control w-120" readonly />
                  </div>
                  <div class="col-6 gst_amount_field">
                     <label class="control-label">Tax Amount <span class="cl-red">*</span></label>
                     <input 
                        type="number" id="gst_amount"
                        class="form-control" />
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

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
   var row_number;
   // document starts
   $(function() {
      $('select').select2();

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

         $('#gst_id').val($('#gst_id_'+row_number).val());
         $('#gst_type').val($('#gst_type_'+row_number).val());
         $('#iden').val($('#iden_'+row_number).val()) ;

         $('.supplier').hide();
         $('.customer').hide();
         $('.iden').hide();
         $('.amount_field').show();
         $('.gst_category_field').show();
         $('.gst_percentage_field').show();
        
         if($('#iden').val() == 'Input Tax' || 
            $('#iden').val() == 'Output Tax' || 
            $('#iden').val() == 'Reverse Input' || 
            $('#iden').val() == 'Reverse Output' || 
            $('#iden').val() == 'Rebate') {
            
            $('#iden').val($('#iden_'+row_number).val());
            $('#iden').prop("readonly", true);
            $('.iden').show();

         } else if($('#iden').val() == 'Settlement') {

            $('#iden').val($('#iden_'+row_number).val());
            $('#iden').prop("readonly", true);
            $('.iden').show();

            $('.amount_field').hide();
            $('.gst_category_field').hide();
            $('.gst_percentage_field').hide();

         } else {

            if($('#gst_type').val() == "O") {
               $('.customer').show();
               $('#customer').select2("destroy");
               $('#customer').val($('#iden_'+row_number).val());
               $('#customer').select2();
            } else if($('#gst_type').val() == "I") {
               $('.supplier').show();
               $('#supplier').select2("destroy");
               $('#supplier').val($('#iden_'+row_number).val());
               $('#supplier').select2();
            }
         }

         $('#amount').val($('#amount_'+row_number).val());

         if($('#gst_type').val() == "I" || $('#gst_type').val() == "IR") {
            $('.supply_gst').hide();
            $('.purchase_gst').show();
            
            $('#purchase_gst').select2("destroy");
            $('#purchase_gst').val($('#gst_category_'+row_number).val());
            $('#purchase_gst').select2();

         } else if($('#gst_type').val() == "O" || $('#gst_type').val() == "OR") {
            $('.purchase_gst').hide();
            $('.supply_gst').show();

            $('#supply_gst').select2("destroy");
            $('#supply_gst').val($('#gst_category_'+row_number).val());
            $('#supply_gst').select2();
         }

         $('#gst_percentage').val($('#gst_percentage_'+row_number).val());
         $('#gst_amount').val($('#gst_amount_'+row_number).val());

         $('#edit_id').val(row_number);
         $('#entryModal').modal('show');
      });

      $(document).on("change", "#amount, #gst_amount", function() {
         var amount = 0;
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);
         }
      });

      $(document).on("keyup", "#amount", function() {
         get_gst_amount();
      });
      
      $(document).on('change', '#purchase_gst, #supply_gst', function() {
         var gst_code = $('option:selected', this).val();
         get_gst_rate(gst_code);
      });

      // add entry to transaction
      $("#btn_save_entry").on('click', function () {
         if(!isFormValid() || !isModalValid()) {
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
                     $.post('/gst/ajax/delete_gst', {
                        gst_id: $('#gst_id_'+row_number).val()
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

      $('#btn_submit').on('click', function() {
         if($('#tbl_items > tbody > tr').length == 0) {
            return false;
         } else if(double_ref == 1) {
            $('#ref_no').focus();
            return false;
         }

         var url = "/gst/save_patched_data";
         $("#form_").attr("action", url);
         $("#form_").submit();
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
      }

      return valid;
   }

   function isModalValid() {
      var valid = true;
      if($('#gst_type').val() == "O" && $('#iden').val() !== 'Output Tax' && $('#customer').val() == "") {
         $("#customer").select2('open');
         valid = false;
         console.log("Req::Customer");
      } else if($('#gst_type').val() == "I" && $('#iden').val() !== 'Input Tax' && $('#supplier').val() == "") {
         $("#supplier").select2('open');
         valid = false;
         console.log("Req::Supplier");
      } else if($('#gst_type').val() !== "S" && $('#amount').val() == "") {
         $('#amount').focus();
         valid = false;
         console.log("Req::Amount");
      } else if($('#gst_type').val() !== "S" && $('#gst_category').val() == "") {
         $("#gst_category").select2('open');
         valid = false;
         console.log("Req::Gst Category");
      } else if($('#gst_amount').val() == "") {
         $('#gst_amount').focus();
         valid = false;
         console.log("Req::GST Amount");
      }
      return valid;
   }

   function get_gst_rate(gst_code) {
      var gst_percentage = Number(0);
      if(gst_code !== "") {
         $.post('/gst/ajax/get_gst_rate', {
            gst_code: gst_code
         }, function (data) {
            if(data !== "") {
               var obj = $.parseJSON(data);
               gst_percentage = Number(obj.gst_percentage);
               $("#gst_percentage").val(gst_percentage.toFixed(2));
               get_gst_amount();
            }
         });
      } else {
         $('#gst_percentage').val("");
         $('#gst_amount').val("");
      }
   }

   function get_gst_amount() {
      var amount = 0;
      var gst_amount = 0;
      var gst_rate = 0;

      amount = Number(0);
      gst_amount = Number(0);
      gst_rate = Number(0);

      amount = Number($("#amount").val());
      gst_rate = Number($("#gst_percentage").val());

      if(amount !== "" && amount !== 0) {
         gst_amount = Math.round(amount * gst_rate) / 100;
      } else {
         gst_amount = 0;
      }

      $("#gst_amount").val(gst_amount.toFixed(2));
   }

   function manage_entry() {
      $row = $('tr[id="'+$("#edit_id").val()+'"]');

      $row.find('input.gst_id').val($('#gst_id').val());

      $row.find('input.iden').val($('#iden').val());

      $row.find('input.amount').val($('#amount').val());
      $row.find('input.gst_percentage').val($('#gst_percentage').val());

      if($('#gst_type').val() == "S" || $('#gst_type').val() == "R") { // settlement
         $row.find('input.amount').val('N/A');
         $row.find('input.gst_category').val('N/A');
         $row.find('input.gst_percentage').val('N/A');
      
      } else if($('#gst_type').val() == "I") { // input tax
         $row.find('input.gst_category').val($('#purchase_gst').val());
         $row.find('input.gst_details').val($("#purchase_gst>option:selected").text());

         if($('#iden').val() !== "Input Tax") {
            $row.find('input.iden').val($('#supplier').val());
            $row.find('input.iden_details').val($('#supplier>option:selected').text());
         }

      } else if($('#gst_type').val() == "O") { // output tax
         $row.find('input.gst_category').val($('#supply_gst').val());
         $row.find('input.gst_details').val($("#supply_gst>option:selected").text());

         if($('#iden').val() !== "Output Tax") {
            $row.find('input.iden').val($('#customer').val());
            $row.find('input.iden_details').val($('#customer>option:selected').text());
         }
      }

      $row.find('input.gst_amount').val($('#gst_amount').val());

      $('#entryModal').modal('hide');
   }
</script>