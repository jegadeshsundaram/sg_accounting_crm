<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Stock</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Stock</li>
               <li class="breadcrumb-item">Datapatch</li>
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

            <form autocomplete="off" id="form_" method="post" action="<?php echo $save_url; ?>">
               <div class="card card-default">
                  <div class="card-header">
                     <h5>Datapatch Opening Balance</h5>
                  </div>
                  <div class="card-body">
                  <div class="row form-group">
                        <label class="control-label col-md-3">Date : </label>
                        <div class="col-md-3">
                           <input 
                              type="text" 
                              id="doc_date" name="doc_date" 
                              value="<?php echo date('d-m-Y', strtotime($doc_date)); ?>"
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
                              value="<?php echo $ref_no; ?>"
                              class="form-control ref_no w-150" 
                              maxlength="12" />
                           <input type="hidden" id="original_ref_no" value="<?php echo $ref_no; ?>" />
                           <span id="ref_error" style="display: none; color: red;">Duplicate reference disallowed</span>
                        </div>
                     </div>
                     <br />
                     <div class="row form-group">
                        <div class="col-md-12 table-responsive">
                           <table id="tbl_items" class="table table-custom" style="min-width: 1000px; width: 100%;">
                              <thead>
                                 <tr>
                                    <th class="w-150">Action</th>
                                    <th class="w-350">Item</th>
                                    <th class="w-120">UOM</th>
                                    <th class="w-150">Quantity</th>
                                    <th class="w-200">Unit Cost</th>
                                    <th class="w-350">Remarks</th>
                                    <th></th>
                                 </tr>
                              </thead>
                              <tbody>

                              <?php
                              $i = 0;
                              $this->db->select('*');
                              $this->db->from('stock');
                              $this->db->where(['created_on' => $doc_date, 'ref_no' => $ref_no, 'stock_type' => 'OPBAL']);
                              $this->db->order_by('stock_id', 'ASC');
                              $query = $this->db->get();
                              $ob_entries = $query->result();
                              foreach ($ob_entries as $value) {
                                 $billing = $this->custom->getMultiValues('master_billing', 'stock_code, billing_description, billing_uom', ['billing_id' => $value->product_id]);
                              ?>
                                 <tr id="<?php echo $i; ?>">
                                    <td>
                                       <!-- Field : Entry Unique ID from DB -->
                                       <input 
                                          type="hidden" 
                                          id="ob_id_<?php echo $i; ?>" name="ob_id[]"
                                          value="<?php echo $value->stock_id; ?>" />
                                       
                                       <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                                       <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                                    </td>

                                    <td>
                                       <input 
                                          type="text" 
                                          id="product_desc_<?php echo $i; ?>" 
                                          value="<?php echo $billing->stock_code.' : '.$billing->billing_description; ?>"
                                          class="form-control product_desc" readonly />
                                       
                                       <input 
                                          type="hidden" 
                                          id="product_id_<?php echo $i; ?>" name="product_id[]" 
                                          value="<?php echo $value->product_id; ?>" class="product_id" />
                                    </td>
                                    
                                    <td>
                                       <input 
                                          type="text" id="uom_<?php echo $i; ?>"
                                          value="<?php echo $billing->billing_uom; ?>"
                                          class="form-control uom" readonly />
                                    </td>
                                  
                                    <td>
                                       <input 
                                          type="number" 
                                          id="quantity_<?php echo $i; ?>" name="quantity[]" 
                                          value="<?php echo $value->quantity; ?>"
                                          class="form-control quantity" readonly />
                                    </td>
                                    
                                    <td>
                                       <input 
                                          type="number" 
                                          id="unit_cost_<?php echo $i; ?>" name="unit_cost[]" 
                                          value="<?php echo $value->unit_cost; ?>"
                                          class="form-control unit_cost" readonly />
                                    </td>

                                    <td>
                                       <input 
                                          type="text" 
                                          id="remarks_<?php echo $i; ?>" name="remarks[]" 
                                          value="<?php echo $value->remark; ?>"
                                          class="form-control remarks" readonly />
                                    </td>
                                 </tr>
                                 <?php
                                    ++$i;
                                 } ?>
                              </tbody>
                           </table>
                        </div>
                     </div>

                  </div>
                  <div class="card-footer">
                     <a href="/stock/" class="btn btn-info btn-sm">Cancel</a>                  
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
               <input type="hidden" id="ob_id" />

               <div class="row mt-10">
                  <label class="control-label col-12">Product <span class="cl-red">*</span></label>
                  <div class="col-12">
                     <select id="product" class="form-control">
                        <?php echo $products; ?>
                     </select>
                     <input type="hidden" id="original_product_id" />
                  </div>
               </div>

               <hr />

               <div class="row mt-10">
                  <div class="col-3">
                     <label class="control-label">UOM <span class="cl-red">*</span></label>
                     <input 
                        type="text" 
                        id="uom" class="form-control" readonly />
                  </div>
                  <div class="col-4">
                     <label class="control-label">Quantity <span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="quantity" class="form-control" />
                  </div>
                  <div class="col-5">
                     <label class="control-label">Unit Cost <span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="unit_cost" class="form-control" />
                  </div>
               </div>

               <div class="row mt-10">
                  <div class="col-12">
                     <label class="control-label">Remarks</label>
                     <input type="text" id="remarks" class="form-control" />
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#entryModal">Cancel</button>
               <button type="button" class="btn btn-info btn-sm float-right" id="btn_save_item">Done</button>
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

<script type="text/javascript">
   var row_number;
   $(function() { // document starts
      
      $('select').select2();

      var double_ref = 0;
      $(document).on("change", "#ref_no", function() {
         var current_ref = $(this).val();
         var original_ref = $('#original_ref_no').val();
         double_ref = 0;
         $("#ref_error").hide();

         if(current_ref !== "" && current_ref !== original_ref) {

            $.post('/stock/ajax/double_stock_ob', {
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
            
         } else {
            double_ref == 0;
         }
      });

      // EDIT
      $(document).on('click', '.dt_edit', function () {

         if(!isFormValid()) {
            return;
         }

         row_number = $(this).closest('tr').attr('id');

         $('#ob_id').val($('#ob_id_'+row_number).val());

         $('#original_product_id').val($('#product_id_'+row_number).val());
         $('#product').select2("destroy");
         $('#product').val($('#product_id_'+row_number).val());
         $('#product').select2();

         $('#uom').val($('#uom_'+row_number).val());
         $('#quantity').val($('#quantity_'+row_number).val());
         $('#unit_cost').val($('#unit_cost_'+row_number).val());
         $('#remarks').val($('#remarks_'+row_number).val());

         $('#edit_id').val(row_number);

         $('#entryModal').modal('show');
      });

      $(document).on("change", "#product", function() {
         var current_product_id = $('option:selected',this).val();
         var original_product_id = $('#original_product_id').val();

         if (current_product_id !== "") {
         
            if(original_product_id !== '' && original_product_id == current_product_id) {
               return false;
            }

            var product_exists = false;
            $("#tbl_items").find('.product_id').each(function() {
               if(current_product_id == $(this).val()) {
                  product_exists = true;

                  $('#entryModal').modal('hide');

                  $('#errorAlertModal .modal-title').html("Duplicate Product");
                  $('#errorAlertModal .modal-body').html("Selected Product already used. Please choose other product.");
                  $('#errorAlertModal').modal();

                  $("#product").val("").trigger("change");
               }
            });

            if(!product_exists) {
               $.post('/stock/ajax/get_product_uom', {
                  product_id: current_product_id
               }, function(uom) {
                  $("#uom").val(uom);
                  $("#quantity").focus();
               });
            }
         }
      });

      $('#errorAlertModal').on('hidden.bs.modal', function() {
         $('#entryModal').modal('show');
      });

      $(document).on("change", "#unit_cost", function() {
         var amount = 0;
         if($.trim($(this).val()) !== "") {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);
         }
      });

      // add entry to transaction
      $("#btn_save_item").on('click', function () {
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
                     $.post('/stock/ajax/delete_stock_ob_entry', {
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

      // submit
      $("#btn_submit").on('click', function () {
         if(!isFormValid() || $('#tbl_items > tbody > tr').length == 0) {
            return false;
         }

         if(double_ref == 1) {
            $('#ref_no').focus();
            return false;
         }

         $('#form_').submit();
      });

      
   }); // document ends

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
      if($('#product').val() == "") {
         $("#product").select2('open');
         valid = false;
      } else if($('#quantity').val() == "") {
         $('#quantity').focus();
         valid = false;
      } else if($('#unit_cost').val() == "") {
         $('#unit_cost').focus();
         valid = false;
      }
      return valid;
   }

function manage_entry() {
   $row = $('tr[id="'+$("#edit_id").val()+'"]');

   $row.find('input.ob_id').val($('#ob_id').val());
   
   $row.find('input.product_desc').val($("#product>option:selected").text());
   $row.find('input.product_id').val($("#product").val());

   $row.find('input.uom').val($('#uom').val());
   $row.find('input.quantity').val($('#quantity').val());
   $row.find('input.unit_cost').val($('#unit_cost').val());
   $row.find('input.remarks').val($('#remarks').val());

   $('#entryModal').modal('hide');
}
</script>