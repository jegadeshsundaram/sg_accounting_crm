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
               <li class="breadcrumb-item active">Options</li>
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
            <form autocomplete="off" id="frm_" method="post">
               <input type="hidden" id="process" name="process" />
               <input type="hidden" id="edit_id" name="edit_id" />

               <div class="card card-default">
                  <div class="card-header">
                     <h5>Credit Purchase</h5>
                     <a href="/ez_entry/batch_purchase" class="btn btn-outline-dark btn-sm float-right">
                        <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                     </a>
                  </div>
                  <div class="card-body">
                     <div class="row">
                        <div class="col-md-6 col-6">
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

                        <div class="col-md-6 col-6">
                           <div class="row form-group">
                              <label class="control-label col-md-4">Reference<span class="cl-red">*</span></label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    id="ref_no" name="ref_no" 
                                    value="<?php echo $ref_no; ?>"
                                    maxlength="12" class="form-control ref_no w-150">
                                 
                                 <span class="error-ref error" style="display: none;">Duplicate reference disallowed</span>                                 
                                 <input type="hidden" id="original_ref_no" value="<?php echo $ref_no; ?>" />
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-6">
                           <div class="row form-group">
                              <label class="control-label col-md-4">Supplier<span class="cl-red">*</span></label>
                              <div class="col-md-8">
                                 <select id="supplier" name="supplier" class="form-control supplier">
                                    <?php echo $suppliers; ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6 currency_rate" style="display: <?php echo ($currency == '' || $currency == 'SGD') ? 'none' : 'block'; ?>">
                           <div class="row form-group">
                              <label class="control-label col-md-4">Currency & Rate<span class="cl-red">*</span></label>
                              <div class="col-md-8">
                                 <input type="hidden" id="currency" name="currency" value="<?php echo $currency; ?>" />
                                 <div class="input-group w-180">
                                    <div class="input-group-prepend">
                                       <span class="input-group-text currency"><?php echo $currency; ?></span>
                                    </div>
                                    <input 
                                       type="number" 
                                       id="exchange_rate" name="exchange_rate" 
                                       value="<?php echo $exchange_rate; ?>"
                                       class="form-control exchange_rate w-120" readonly />
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>

                     <br />
                     <div class="row">
                        <div class="col-md-12 table-responsive">
                           <table id="tbl_items" class="table table-custom" style="min-width: 1350px; width: 100%; display: <?php echo $page == 'edit' ? '' : 'none'; ?>">
                              <thead>
                                 <tr>
                                    <th>Action</th>
                                    <th>Purchase Account</th>
                                    <th class="f_curr_dv txt-right">Amount <span class="f_curr" style="display: <?php echo $currency != 'SGD' ? '' : 'none'; ?>">(<?php echo $currency != 'SGD' ? $currency : ''; ?>)</span></th>
                                    <th class="l_curr_dv txt-right" style="display: <?php echo $currency != 'SGD' ? '' : 'none'; ?>">Amount <span>(SGD)</span></th>
                                    <th>GST Code & Rate (%)</th>
                                    <th class="f_curr_dv txt-right">GST Amount <span class="f_curr" style="display: <?php echo $currency != 'SGD' ? '' : 'none'; ?>">(<?php echo $currency != 'SGD' ? $currency : ''; ?>)</span></th>
                                    <th class="l_curr_dv txt-right" style="display: <?php echo $currency != 'SGD' ? '' : 'none'; ?>">GST Amount <span>(SGD)</span></th>
                                    <th></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                    if ($page == 'edit') {
                                        $i = 0;
                                        $this->db->select('*');
                                        $this->db->from('ez_purchase');
                                        $this->db->where(['doc_date' => $doc_date, 'ref_no' => $ref_no, 'supplier_id' => $supplier_id]);
                                        $query = $this->db->get();
                                        $pr_entries = $query->result();
                                        foreach ($pr_entries as $value) { ?>
                                 <tr id="<?php echo $i; ?>">
                                    <td class="w-140">
                                       <input 
                                          type="hidden" 
                                          id="entry_id_<?php echo $i; ?>" name="entry_id[]" 
                                          value="<?php echo $value->pb_id; ?>"
                                          class="entry_id" />

                                       <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                                       <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                                    </td>
                                    
                                    <td class="w-250">
                                       <input 
                                          type="hidden" 
                                          id="purchase_accn_<?php echo $i; ?>" name="purchase_accn[]" 
                                          value="<?php echo $value->purchase_accn; ?>"
                                          class="purchase_accn" />

                                       <?php $accn_desc = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->purchase_accn]); ?>
                                       <input 
                                          type="text" 
                                          id="purchase_accn_desc_<?php echo $i; ?>"  
                                          value="<?php echo $value->purchase_accn .' : '. $accn_desc; ?>"
                                          class="form-control-dsply purchase_accn_desc" />
                                    </td>
                                    
                                    <td class="f_curr_dv w-180">
                                       <input 
                                          type="number" 
                                          id="foreign_amount_<?php echo $i; ?>" name="foreign_amount[]" 
                                          value="<?php echo $value->foreign_amount; ?>"
                                          class="form-control-dsply txt-right foreign_amount" readonly />
                                    </td>         
                                    
                                    <td class="l_curr_dv w-180" style="display: <?php echo $currency != 'SGD' ? '' : 'none'; ?>">
                                       <input 
                                          type="number" 
                                          id="local_amount_<?php echo $i; ?>" name="local_amount[]" 
                                          value="<?php echo $value->local_amount; ?>"
                                          class="form-control-dsply txt-right local_amount" readonly />
                                    </td>
                                    
                                    <td>
                                       <input 
                                          type="hidden" 
                                          id="gst_category_<?php echo $i; ?>" name="gst_category[]" 
                                          value="<?php echo $value->gst_code; ?>"
                                          class="gst_category" />

                                       <?php $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $value->gst_code]); ?>                                       
                                       <input 
                                          type="hidden" 
                                          id="gst_rate_<?php echo $i; ?>" name="gst_rate[]" 
                                          value="<?php echo $gst_rate; ?>"
                                          class="gst_rate" />

                                       <?php $gst_desc = $this->custom->getSingleValue('ct_gst', 'gst_description', ['gst_code' => $value->gst_code]); ?>
                                       <input 
                                          type="text" 
                                          id="gst_desc_<?php echo $i; ?>"
                                          value="<?php echo $value->gst_code.' : '.$gst_desc.' RATE :'.$gst_rate; ?>"
                                          class="form-control-dsply gst_desc" readonly />
                                    </td>

                                    <td class="f_curr_dv w-180">
                                       <input 
                                          type="number" 
                                          id="foreign_gst_amount_<?php echo $i; ?>" name="foreign_gst_amount[]"
                                          value="<?php echo $value->foreign_gst_amount; ?>" 
                                          class="form-control-dsply txt-right foreign_gst_amount" readonly />
                                    </td>

                                    <td class="l_curr_dv w-180" style="display: <?php echo $currency != 'SGD' ? '' : 'none'; ?>">
                                       <input 
                                          type="number" 
                                          id="local_gst_amount_<?php echo $i; ?>" name="local_gst_amount[]" 
                                          value="<?php echo $value->local_gst_amount; ?>" 
                                          class="form-control-dsply txt-right local_gst_amount" readonly />
                                    </td>
                                 </tr>
                                 <?php
                                    ++$i;
                                        }
                                    } ?>
                              </tbody>
                           </table>
                        </div>
                     </div>

                     <br />

                     <div class="row">
                        <div class="col-md-12">
                           <a href="#" class="btn_add btn btn-outline-danger btn-sm" style="margin-right: 10px"><i class="fa-solid fa-plus"></i> ADD ENTRY</a>
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
         
         <td class="w-250">
            <input 
               type="hidden" 
               id="purchase_accn_0" name="purchase_accn[]" 
               class="purchase_accn" />

            <input 
               type="text" 
               id="purchase_accn_desc_0"  
               class="form-control-dsply purchase_accn_desc" />
         </td>
         
         <td class="f_curr_dv w-180">
            <input 
               type="number" 
               id="foreign_amount_0" name="foreign_amount[]" 
               class="form-control-dsply txt-right foreign_amount" readonly />
         </td>         
         
         <td class="l_curr_dv w-180" style="display: none">
            <input 
               type="number" 
               id="local_amount_0" name="local_amount[]" 
               class="form-control-dsply txt-right local_amount" readonly />
         </td>
         
         <td>
            <input 
               type="hidden" 
               id="gst_category_0" name="gst_category[]" class="gst_category" />
            
            <input 
               type="hidden" 
               id="gst_rate_0" name="gst_rate[]" class="gst_rate" />

            <input 
               type="text" 
               id="gst_desc_0" 
               class="form-control-dsply gst_desc" readonly>
         </td>

         <td class="f_curr_dv w-180">
            <input 
               type="number" 
               id="foreign_gst_amount_0" name="foreign_gst_amount[]" 
               class="form-control-dsply txt-right foreign_gst_amount" readonly />
         </td>

         <td class="l_curr_dv w-180" style="display: none">
            <input 
               type="number" 
               id="local_gst_amount_0" name="local_gst_amount[]" 
               class="form-control-dsply txt-right local_gst_amount" readonly />
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
                  <div class="col-6 f_amt">
                     <label class="control-label f_curr_dv">Amount <span class="f_curr" style="display: none"></span> <span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="foreign_amount" name="foreign_amount" 
                        class="form-control foreign_amount" />
                  </div>
                  <div class="col-6 l_curr_dv" style="display: none">
                     <label class="control-label">Amount <span class="l_curr">(SGD)</span> <span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="local_amount" name="local_amount" 
                        class="form-control local_amount" />
                  </div>
               </div>

               <div class="row mt-10">
                  <div class="col-12">
                     <label class="control-label">GST Category<span class="cl-red">*</span></label>
                     <select id="gst_category" name="gst_category" class="form-control gst_category">
                        <?php echo $gsts; ?>
                     </select>
                  </div>
               </div>

               <div class="row mt-10">
               <div class="col-2">
                     <label class="control-label">Rate %</label>
                     <input 
                        type="number" 
                        id="gst_rate" name="gst_rate" 
                        value="<?php echo $std_gst_rate; ?>"
                        class="form-control gst_rate" readonly />
                  </div>
                  <div class="col-5 f_curr_dv">
                     <label class="control-label">GST Amount <span class="f_curr" style="display: <?php echo $currency != 'SGD' ? '' : 'none'; ?>"></span> <span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="foreign_gst_amount" name="foreign_gst_amount" 
                        class="form-control foreign_gst_amount" />
                  </div>
                  <div class="col-5 l_curr_dv" style="display: <?php echo $currency != 'SGD' ? '' : 'none'; ?>">
                     <label class="control-label">GST Amount <span class="l_curr">(SGD)</span> <span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="local_gst_amount" name="local_gst_amount" 
                        class="form-control local_gst_amount" />
                  </div>
               </div>

               <div class="row mt-10">
                  <div class="col-md-12 col-12">
                     <label class="control-label">Purchase Account<span class="cl-red">*</span></label>
                     <select id="purchase_accn" name="purchase_accn" class="form-control purchase_accn">
                        <?php echo $purchase_accns; ?>
                     </select>
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

<?php require_once APPPATH.'/modules/includes/modal/supplier.php'; ?>

<style>
   table thead th span {
      color: red;
   }
   .f_curr, .l_curr {
      font-weight: bold;
      margin-left: 5px;
   }   
</style>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>

   var duplicate_ref = false;

   // document starts
   $(function() {

      var row_number = '';      
      $('select').select2();

      $(document).on("change", "#doc_date", function() {
         if($(this).val() == "") {
            return false;
         }

         if($('#tbl_items > tbody > tr').length > 0) {
            update_items();
         }
      });

      
      $(document).on("change", "#ref_no", function() {
         if($(this).val() == "" || $('#supplier').val() == "") {
            return false;
         }

         double_ref();
      });

      $(document).on('change', '#supplier', function() {
         var supplier_id = $('option:selected', this).val();
         if(supplier_id == "") {
            return false;
         }

         $("#exchange_rate").val('');

         $.post('/ez_entry/ajax/get_supplier_details', {
            supplier_id: supplier_id
         }, function (data) {
            var obj = $.parseJSON(data);
            $("#currency").val(obj.currency);
            $(".currency").text(obj.currency);
            $("#exchange_rate").val(obj.currency_amount);

            $('.f_curr').text("("+obj.currency+")");
            $('.currency_rate').show();

            if(obj.currency == "SGD") {
               $('.currency_rate label').text("Currency");
               $('#exchange_rate').hide();

               $('.f_curr, .l_curr_dv').hide();

               $("#tbl_items").css("min-width", "1000px");
            } else {
               $('.currency_rate label').text("Currency & Rate");
               $('#exchange_rate').show();

               $('.f_curr, .l_curr_dv').show();
               $("#tbl_items").css("min-width", "1350px");
            }

            double_ref();

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

      $('#entryModal').on('shown.bs.modal', function() {

         $('.f_curr').hide();
         $('.f_curr').html('');
         if($('#currency').val() !== "SGD") {
            $('.f_curr').show();
            $('.f_curr').html("("+$('#currency').val()+")");
         }
         $('#foreign_amount').focus();
      });

      $(document).on("keyup", "#foreign_amount", function() {
         if($(this).val() !== "") {
            row_number = '';
            get_local_amount(row_number);
         } else {
            $("#foreign_gst_amount").val('');
         }
      });

      $(document).on("change", "#foreign_amount", function() {
         var amount = 0;
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);
         } else {
            $("#local_amount").val('');
            $("#foreign_gst_amount").val('');
            $("#local_gst_amount").val('');
         }
      });
     
      $(document).on("change", "#exchange_rate", function() {
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            
            $(this).val(Number($(this).val()).toFixed(5));

            $.post('/ez_entry/ajax/update_exchange_rate', {
               ref_no: $('#ref_no').val(),
               exchange_rate: $(this).val(),
               tbl: 'ez_purchase'
            }, function (data) {
               if($.trim(data) == "updated") {
                  update_items();
               }
            });
         } else {
            $("#local_amount").val('');
         }
      });

      $(document).on("change", "#local_amount", function() {
         var amount = 0;
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);
            
            row_number = '';
            get_gst_amount(row_number);
         } else {
            $("#local_gst_amount").val('');
         }
      });

      $(document).on('change', '#gst_category', function() {
         var gst_category = $('option:selected', this).val();
         var gst_percentage = Number(0);

         if(gst_category !== "") {
            $.post('/ez_entry/ajax/get_gst_details', {
               gst_code: gst_category
            }, function (data) {
               if(data !== "") {
                  var obj = $.parseJSON(data);
                  gst_percentage = Number(obj.gst_percentage);
                  $("#gst_rate").val(gst_percentage);

                  row_number = '';
                  get_gst_amount(row_number);
               }
            });
         }
      });

      // EDIT
      $(document).on('click', '.dt_edit', function () {

         if(!isFormValid() || duplicate_ref) {
            return;
         }

         //clear_inputs();

         row_number = $(this).closest('tr').attr('id');

         $('#entry_id').val($('#entry_id_'+row_number).val());
         $('#foreign_amount').val($('#foreign_amount_'+row_number).val());
         $('#local_amount').val($('#local_amount_'+row_number).val());

         $('#gst_category').select2("destroy");
         $('#gst_category').val($('#gst_category_'+row_number).val());
         $('#gst_category').select2();

         $('#gst_rate').val($('#gst_rate_'+row_number).val());
         $('#foreign_gst_amount').val($('#foreign_gst_amount_'+row_number).val());
         $('#local_gst_amount').val($('#local_gst_amount_'+row_number).val());

         $('#purchase_accn').select2("destroy");
         $('#purchase_accn').val($('#purchase_accn_'+row_number).val());
         $('#purchase_accn').select2();
        
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
                     $.post('/ez_entry/ajax/delete_purchase_entry', {
                        entry_id: $('#entry_id_'+row_number).val()
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

      // add entry to transaction
      $("#btn_save_entry").on('click', function (e) {

         if(!isFormValid() || !isModalValid()) {
            return false;
         }

         row_number = '';
         save(row_number);
      });

      // save to batch
      $("#btn_save_to_batch").on('click', function (e) {
         if($('#tbl_items > tbody > tr').length > 0 && isFormValid()) {
            $('#process').val('save');

            toastr.success("Purchase saved to batch!");
            window.location.href = '/ez_entry/batch_purchase';
         }
      });

      // post to GL
      $("#btn_post_to_accounts").on('click', function (e) {
         if($('#tbl_items > tbody > tr').length > 0 && isFormValid()) {
            $.confirm({
               title: '<i class="fa fa-info"></i> Confirm POST to GL',
               content: 'Are you sure to Post?</strong>',
               buttons: {
                  yes: {
                     btnClass: 'btn-warning',
                     action: function() {
                        $('#process').val('post');
                        post_purchase();
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

   function double_ref() {
      duplicate_ref = false;
      $(".error-ref").hide();
      
      $.post('/ez_entry/ajax/double_purchase', {
         ref_no: $('#ref_no').val(),
         supplier_id: $('#supplier').val()
      }, function(ref) {
         if (ref > 0) {
            duplicate_ref = true;
            $("#ref_no").focus();
            $(".error-ref").show();
         } else {
            if($('#tbl_items > tbody > tr').length > 0) {
               update_items();
            }
         }
      });
   }

   function clear_inputs() {
      $('#entry_id').val('');
      $('#edit_id').val('');

      $('#foreign_amount').val('');
      $('#local_amount').val('');

      $('#gst_category').select2("destroy");
      $('#gst_category').val('TX');
      $('#gst_category').select2();

      $('#gst_rate').val('<?php echo $std_gst_rate; ?>');
      $('#foreign_gst_amount').val('');
      $('#local_gst_amount').val('');

      $('#purchase_accn').select2("destroy");
      $('#purchase_accn').val('C0001');
      $('#purchase_accn').select2();
   }

   function isFormValid() {
      var valid = true;
      if($('#doc_date').val() == "") {
         $('#doc_date').focus();
         valid = false;
      } else if($('#ref_no').val() == "") {
         $('#ref_no').focus();
         valid = false;
      } else if($('#supplier').val() == "") {
         $("#supplier").select2('open');
         valid = false;
      }

      return valid;
   }

   function isModalValid() {
      var valid = true;
      if($('#foreign_amount').val() == "") {
         $('#foreign_amount').focus();
         valid = false;
      } else if($("#currency").val() == "SGD" && $('#local_amount').val() == "") {
         $('#local_amount').focus();
         valid = false;
      } else if($('#gst_category').val() == "") {
         $("#gst_category").select2('open');
         valid = false;
      } else if($('#foreign_gst_amount').val() == "") {
         $('#foreign_gst_amount').focus();
         valid = false;
      } else if($("#currency").val() == "SGD" && $('#local_gst_amount').val() == "") {
         $('#local_gst_amount').focus();
         valid = false;
      } else if($('#purchase_accn').val() == "") {
         $("#purchase_accn").select2('open');
         valid = false;
      }

      return valid;
   }

   function get_local_amount(row_number) {
      var exchange_rate = $("#exchange_rate").val();
      var famt = $('#foreign_amount'+row_number).val();
      
      var local_amount = 0;

      if(exchange_rate !== "" && famt !== "") {
         local_amount = Number(famt) / Number(exchange_rate);
         $('#local_amount'+row_number).val(local_amount.toFixed(2));

         get_gst_amount(row_number);

      } else {
         $('#local_amount'+row_number).val('');
      }
   }

   function get_gst_amount(row_number) {
      var foreign_amount = 0;
      var foreign_gst_amount = 0;
      var local_amount = 0;
      var local_gst_amount = 0;
      var gst_rate = 0;

      foreign_amount = Number(0);
      foreign_gst_amount = Number(0);
      local_amount = Number(0);
      local_gst_amount = Number(0);
      gst_rate = Number(0);

      foreign_amount = Number($('#foreign_amount'+row_number).val());
      local_amount = Number($('#local_amount'+row_number).val());
      gst_rate = Number($('#gst_rate'+row_number).val());

      if(foreign_amount !== "" && foreign_amount !== 0) {
         foreign_gst_amount = Math.round(foreign_amount * gst_rate) / 100;
      } else {
         foreign_gst_amount = 0;
      }

      if(local_amount !== "" && local_amount !== 0) {
         local_gst_amount = Math.round(local_amount * gst_rate) / 100;
      } else {
         local_gst_amount = 0;
      }

      $('#foreign_gst_amount'+row_number).val(foreign_gst_amount.toFixed(2));
      $('#local_gst_amount'+row_number).val(local_gst_amount.toFixed(2));
   }

   function save(row_number) {

      // header values
      var doc_date = $("#doc_date").val();
      var ref_no = $("#ref_no").val();
      var supplier = $("#supplier").val();
      var exchange_rate = $("#exchange_rate").val();

      // body values
      var entry_id = $("#entry_id"+row_number).val();
      var purchase_accn = $("#purchase_accn"+row_number).val();

      var foreign_amount = $("#foreign_amount"+row_number).val();      
      var local_amount = $("#local_amount"+row_number).val();

      var gst_category = $("#gst_category"+row_number).val();
      var foreign_gst_amount = $("#foreign_gst_amount"+row_number).val();
      var local_gst_amount = $("#local_gst_amount"+row_number).val();      

      $.post('/ez_entry/ajax/save_purchase', {
         entry_id: entry_id,
         doc_date: doc_date,
         ref_no: ref_no,
         supplier: supplier,
         purchase_accn: purchase_accn,
         foreign_amount: foreign_amount,
         exchange_rate: exchange_rate,
         local_amount: local_amount,
         gst_category: gst_category,
         foreign_gst_amount: foreign_gst_amount,
         local_gst_amount: local_gst_amount,
      }, function(entry_id) {
         console.log(">>> Auto Save - Entry ID >>> "+entry_id);
         $("#entry_id"+row_number).val($.trim(entry_id));

         console.log(">>> Process >>>> "+$('#process').val());

         if($('#process').val() == 'add' || $('#process').val() == 'edit') { // add / edit
            manage_entry();
         }
      });
   }

   function manage_entry() {

      if($('#process').val() == 'add') { // New Row
         $row = $("#tbl_clone tbody tr").clone();
      } else if($('#process').val() == "edit") { // Existing Row
         $row = $('tr[id="'+$("#edit_id").val()+'"]');
      }

      console.log(">>> Manage Entry >>> Entry ID >>> "+$('#entry_id').val());

      $row.find('input.entry_id').val($('#entry_id').val());
      $row.find('input.foreign_amount').val($('#foreign_amount').val());
      $row.find('input.local_amount').val($('#local_amount').val());
      $row.find('input.gst_category').val($('#gst_category').val());
      $row.find('input.gst_rate').val($('#gst_rate').val());
      $row.find('input.gst_desc').val($("#gst_category>option:selected").text());
      $row.find('input.foreign_gst_amount').val($('#foreign_gst_amount').val());
      $row.find('input.local_gst_amount').val($('#local_gst_amount').val());
      $row.find('input.purchase_accn').val($('#purchase_accn').val());
      $row.find('input.purchase_accn_desc').val($("#purchase_accn>option:selected").text());

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

   function update_items() {
      
      $('#process').val("");

      $("#tbl_items tbody tr").each(function () {
         row_number = '_'+$(this).attr('id');
         get_local_amount(row_number);
         
         save(row_number);
      });
   }
   

   function post_purchase() {
      row_number = $("#tbl_items>tbody>tr:first").attr('id');
      $.post('/ez_entry/ajax/post_purchase', {
         rowID: $('#entry_id_'+row_number).val()
      }, function (status) {
         if($.trim(status) == 'posted') {
            toastr.success("Post Success!");
            window.location.href = '/ez_entry/batch_purchase';
         } else {
            toastr.error("Post Error!");
         }
      });
   }
</script>
