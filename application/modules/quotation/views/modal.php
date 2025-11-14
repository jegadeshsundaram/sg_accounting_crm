<!-- Item Modal -->
<div id="entryModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-body">

               <!-- product / service -->
               <div class="row mt-10">
                  <div class="col-12">
                     <label class="control-label">Product / Service<span class="cl-red">*</span></label>
                     <select id="billing" class="form-control">
                        <?php echo $billings; ?>
                     </select>
                     <input type="hidden" id="billing_type" />
                  </div>
               </div>

               <!-- quantity / uom / unit price -->
               <div class="row mt-10 entry_field qty" style="display: none">
                  <div class="col-4">
                     <label class="control-label">Quantity<span class="cl-red">*</span></label>
                     <input 
                        type="number" id="quantity" class="form-control" 
                        onkeypress="if(this.value.length == 6) return false;" />
                  </div>
                  <div class="col-3">
                     <label class="control-label">UOM<span class="cl-red">*</span></label>
                     <input 
                        type="text" 
                        id="uom" class="form-control" readonly />
                  </div>
                  <div class="col-5">
                     <label class="control-label">Unit Price<span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="unit_price" class="form-control" readonly />
                  </div>
               </div>

               <!-- discount / item amount -->
               <div class="row mt-10 entry_field" style="display: none">
                  <div class="col-4 disc">
                     <label class="control-label">Discount (%)</label>
                     <input 
                        type="number" id="discount" class="form-control" 
                        oninput="validateDiscount(this)" />
                  </div>
                  <div class="col-8">
                     <label class="control-label">Amount<span class="cl-red">*</span></label>
                     <input 
                        type="text" 
                        id="amount" class="form-control" readonly />
                  </div>
               </div>

               <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                  <!-- gst category -->
                  <div class="row mt-10 entry_field" style="display: none">
                     <div class="col-12">
                        <label class="control-label">GST Category<span class="cl-red">*</span></label>
                        <select id="gst_category" class="form-control">
                           <?php echo $gsts; ?>
                        </select>
                     </div>
                  </div>

                  <!-- gst rate / gst amount -->
                  <div class="row mt-10 entry_field" style="display: none">
                     <div class="col-2">
                        <label class="control-label">Rate %</label>
                        <input 
                           type="number" 
                           id="gst_rate" class="form-control" readonly />
                     </div>
               
                     <div class="col-5">
                        <label class="control-label">GST Amount</label>
                        <input 
                           type="number" 
                           id="gst_amount" class="form-control" />
                     </div>
                  </div>
               <?php } ?>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#entryModal">Cancel</button>
               <button type="button" class="btn btn-info btn-sm float-right" id="btn_save_item">Submit</button>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Model of new row -->
<table id="tbl_clone" style="display: none">
   <tbody>
      <tr id="0">
         <td>
            <!-- billing id and description -->
            <input type="hidden" id="billing_id_0" name="billing_id[]" class="billing_id" />
            <input type="text" id="billing_desc_0" class="billing_desc" readonly />
            
            <!-- additional description -->
            <button type="button" class="btn btn-outline-info btn-sm btn_add_details" style="margin-left: 10px"><i class="fa-solid fa-plus"></i> Details</button>
            <textarea id="item_details_0" name="item_details[]" class="d-none item_details"></textarea>
            
            <div class="row">
               <div style="float: left; width: 125px; padding: 10px; margin-top: 14px;">
                  <a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                  <a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
               </div>

               <div style="float: left; width: 120px; padding: 10px">
                  <label for="quantity_0" class="control-label">Quantity</label>
                  <input 
                     type="number" 
                     id="quantity_0" name="quantity[]" 
                     class="form-control quantity" readonly />
               </div>

               <div style="float: left; width: 100px; padding: 10px">
                  <label for="uom_0" class="control-label">UOM</label>
                  <input 
                     type="text" 
                     id="uom_0" name="uom[]"
                     class="form-control uom" readonly />
               </div>

               <div style="float: left; width: 160px; padding: 10px">
                  <label for="unit_price_0" class="control-label">Unit Price</label>
                  <input 
                     type="number" 
                     id="unit_price_0" name="unit_price[]" 
                     class="form-control unit_price" readonly />
               </div>

               <div style="float: left; width: 85px; padding: 10px">
                  <label for="discount_0" class="control-label">Discount</label>
                  <input 
                     type="number" 
                     id="discount_0" name="discount[]" 
                     class="form-control discount" readonly />
               </div>

               <div style="float: left; width: 200px; padding: 10px">
                  <label for="amount_0" class="control-label float-right" style="padding-right: 16px;">Amount</label>
                  <input 
                     type="number" 
                     id="amount_0" name="amount[]" 
                     class="form-control txt-right amount" readonly />
               </div>

               <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                  <div style="float: left; width: 400px; padding: 10px">
                     <label for="gst_desc_0" class="control-label">GST Category</label>
                     <input type="text" id="gst_desc_0" class="form-control gst_desc" readonly />
                     
                     <input type="hidden" id="gst_code_0" name="gst_code[]" class="gst_code" />
                     <input type="hidden" id="gst_rate_0" name="gst_rate[]" class="gst_rate" />
                  </div>

                  <div style="float: left; width: 200px; padding: 10px">
                     <label for="gst_amount_0" class="control-label float-right" style="padding-right: 16px;">GST Amount</label>
                     <input 
                        type="number" 
                        id="gst_amount_0" name="gst_amount[]" 
                        class="form-control txt-right gst_amount" readonly />
                  </div>
               <?php } ?>
            </div>
         </td>
      </tr>
   </tbody>
</table>

<!--Modal for set unit price for zero price products and services -->
<div id="setUnitPriceModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <span class="float-left" style="margin: 0;">Set Unit Price</span>
               <span style="margin: 0;font-size: 0.7rem; float: left;"><strong>Note: </strong>Price for the selected product is 0, you can set price here.</span>
               <button id="btn_close_price_modal" type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body">
               <div class="row" style="margin-bottom: 15px">
                  <label for="billing_item" class="col-md-4 control-label float-right">Product : </label>
                  <div class="col-md-8">
                     <span class="billing_details" style="font-size: 0.9rem"></span>
                  </div>
               </div>
               <div class="row">
                  <label for="billing_price_per_uom" class="col-md-4 control-label float-right">Price per UOM : </label>
                  <div class="col-md-8">
                     <input 
                        type="number" 
                        id="billing_price_per_uom" 
                        min="0" max="999999999.99"
                        class="form-control w-200" required />
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <div class="row">
                  <div class="col-md-6">
                     <input type="hidden" id="modal_billing_id" />
                     <button type="button" class="btn btn-secondary btn-sm btn-block" id="btn_add_special_price">Add as Special Price</button>
                  </div>
                  <div class="col-md-6">
                     <button type="button" class="btn btn-secondary btn-sm btn-block float-right" id="btn_update_billing">Update in Billing Master</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!--Modal for add Special GST Category (SRCA-S)
  Whenever user selects SRCA-S for Product or Service item,
  if the customer has registration number, then he will be allowed to select this special gst Category
  If the customer DOES NOT have gst registration number, then this popup will be displayed
  -->
<div id="specialGSTModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="modal-header">
            <h3 class="modal-title" style="text-align: center">Special GST : SRCA-S</h3>
         </div>
         <div class="modal-body">
            There is no GST Registered Number inputted for this Customer. <br />
            To use this special GST, the selected customer must have GST Registered Number.<br /><br />

            Continue with Other GST Category?
         </div>
         <div class="modal-footer justify-content-between" style="text-align: center">            
            <button type="button" id="btn-special-gst-no" class="btn btn-info btn-sm">No</button>
            <button type="button" id="btn-special-gst-yes" class="btn btn-danger btn-sm float-right">Yes</button>
         </div>
      </div>
   </div>
</div>

<!-- Modal :: Item Description - starts -->
<div id="detailsModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <h4 style="margin: 0;" class="dsply_item_header">Details</h4>
               <span style="margin: 0; font-size: 0.7rem;"><strong>Note: </strong>Add details to product / service for reference.</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#detailsModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body">
               <div class="row" style="margin-bottom: 5px">
                  <div class="col-12 dsply_item_code_desc">
                  </div>
               </div>
               <div class="row" style="margin-bottom: 15px">
                  <label for="item_details" class="control-label col-12">Additional Details</label>
                  <div class="col-12">
                     <textarea id="item_details" class="form-control"></textarea>
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailsModal">Cancel</button>
               <button type="button" id="btn-desc-modal-save" class="btn btn-danger btn-sm float-right">Save</button>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- Modal :: Item Description - ends -->

<!-- Alert Modal - starts -->
<div id="alertModal" class="modal fade" data-backdrop="static">
      <div class="modal-dialog modal-confirm">
         <div class="modal-content">
            <div class="modal-header">
               <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body" style="color: dimgray; font-style: italic;"></div>
            <div class="modal-footer">
               <button id="btn-alert-modal-ok" type="button" class="btn btn-danger float-right btn-sm">OK</button>
            </div>
         </div>
      </div>
   </div>
<!-- Alert Modal - starts -->

<style>
   .card-header span {
      padding-top: 5px;
      color: dimgray;
      font-size: 1.2rem;
      letter-spacing: 1px;
   }
   table {
      /*cursor: pointer;*/
   }
   tr.odd {
      border-bottom: 2px solid #ebebeb;
   }
   textarea {
      -webkit-box-sizing: border-box;
      -moz-box-sizing: border-box;
      box-sizing: border-box;

      max-width: 100% !important;
      height: 75px !important;
   }
   .dsply_customer_details {
      padding: 10px 10px 10px 20px;
      color: dimgray;
      border-radius: 5px;
   }
   #tbl_items td {
      border-bottom: 1px solid gainsboro !important;
      padding: 1.2rem .2rem;
   }
   #tbl_total td {
      text-align: right;
      border: none;
      border-bottom: 1px solid gainsboro;
   }
   #tbl_total td:first-child { 
      border-bottom: 1px solid #fff;
      font-weight: 500;
      color: black;
      font-style: italic;
   }
   #tbl_total td:first-child span {
      font-weight: bold;
   }   
   .item_amount, .item_gst_amount {
      text-align: right;
   }   
   .dataTables_info {
      float: none !important;
      text-align: center;
      padding-bottom: 0.755em;
   }
   .dataTables_wrapper .dataTables_paginate {
      float: none !important;
      text-align: center !important;
   }
   .dataTables_wrapper .dataTables_paginate .paginate_button {
      padding: 0.3em 0.7em !important;
   }
   .control-label {
      display: flex;
      font-size: .9rem;
      padding-bottom: 1px;
      margin-bottom: 0 !important;
   }
   .form-control {
      height: 35px;
      font-size: 1rem;
      padding-left: 8px;
      padding-right: 10px;
   }
   .form-control[readonly] {
      background-color: #f5f5f5 !important;
   }
   .billing_desc {
      text-overflow: ellipsis;
      overflow: hidden; 
      width: 300px;
      height: 35px;
      font-size: .9rem;
      padding: 0 6px 0 8px;
      border: 1px solid #ced4da;
      border-radius: .25rem;
      white-space: nowrap;
      color: #495057;
   }
   .gst_desc {
      text-overflow: ellipsis;
      overflow: hidden;
      white-space: nowrap;
   }
   a.dt-btn:hover {
      background: gainsboro;
   }
   .select2-container {
      width: 100% !important;
   }
   .input-group-text {
      line-height: 0.5;
   }
</style>
