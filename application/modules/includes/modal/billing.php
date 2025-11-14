
<!-- Add Billing Modal - starts -->
<div id="addBillingModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <h4 style="margin: 0;" class="dsply_item_header">New Billing</h4>
               <span style="margin: 0; font-size: 0.7rem;"><strong>Note: </strong>Create Billing item to use in this quotation</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#addBillingModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body">
               <!-- Field: Code -->
               <div class="row" style="margin-bottom: 15px">
                  <label for="billing_code" class="control-label col-12">Code</label>
                  <div class="col-12">
                  <input 
                     type="text"
                     name="billing_code" id="billing_code" 
                     class="form-control w-150" maxlength="25" />
                  <span class="billing_code_error error" style="display: none;">Duplicate code disallowed</span>
                  </div>
               </div>

               <!-- Field: Description -->
               <div class="row" style="margin-bottom: 15px">
                  <label for="billing_description" class="control-label col-12">Description</label>
                  <div class="col-12">
                     <textarea name="billing_description" id="billing_description" class="form-control"></textarea>
                  </div>
               </div>

               <!-- Field: Type -->
               <div class="row" style="margin-bottom: 15px">
                  <label for="billing_type" class="col-md-12 control-label">Billing Type : </label>
                  <div class="col-md-12">
                     <select name="billing_type" id="billing_type" class="form-control w-120">
                        <option value="">-- Select --</option>
                        <option value="Product">Product</option>
                        <option value="Service">Service</option>
                     </select>
                  </div>
               </div>

               <div class="form-group row">
                  <div class="col-6">
                     <!-- Field: UOM -->
                     <div class="row">
                        <label for="billing_uom" class="col-md-12 control-label">UOM : </label>
                        <div class="col-md-12">
                           <input 
                              type="text" 
                              name="billing_uom" id="billing_uom" 
                              maxlength="10" class="form-control w-150" />
                        </div>
                     </div>
                  </div>
                  <div class="col-6">
                     <!-- Field: Price -->
                     <div class="form-group row price_field">
                        <label for="billing_price" class="col-md-12 control-label">Price per UOM : </label>
                        <div class="col-md-12">
                           <input 
                              type="number" 
                              name="billing_price" id="billing_price" 
                              maxlength="12" onKeyPress="if(this.value.length == 12) return false;"
                              class="form-control w-150" />
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <button type="button" id="btn-modal-billing-cancel" class="btn btn-info btn-sm">Cancel</button>
               <button type="button" id="btn-modal-billing-save" class="btn btn-danger btn-sm float-right">Save</button>
            </div>
         </div>
      </div>
   </div>
</div>