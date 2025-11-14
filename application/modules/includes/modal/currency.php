<!-- Add Currency Modal -->
<div id="addCurrencyModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <form autocomplete="off" id="frm_currency" method="post" action="#">
            <div class="modal-header">
               <h3 class="modal-title">Add Currency</h3>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-6">
                     <label for="currency" class="control-label">Currency Code</label><br />
                     <input 
                        type="text"
                        name="currency" id="currency" 
                        maxlength="3" style="width: 130px" class="form-control" required />
                     <span class="currency_error error" style="display: none;">Duplicate Currency disallowed</span>
                  </div>
                  <div class="col-6">
                     <label for="rate" class="control-label">X-Rate</label><br />
                     <input 
                        type="number" 
                        name="rate" id="rate"
                        class="form-control"
                        onKeyPress="if(this.value.length==7) return false;" style="width: 130px" required digits>
                  </div>
               </div>

               <div class="row" style="margin-top: 10px">
                  <div class="col-12">
                     <label for="description" class="control-label">Description</label><br />
                     <input class="form-control" name="description" id="description" type="text" maxlength="250" required />
                  </div>
               </div>
            </div>
            <div class="modal-footer justify-content-between">
               <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" data-target="#addCurrencyModal">Cancel</button>
               <button type="button" id="btn-currency-modal-save" class="btn btn-danger btn-sm float-right">Save</button>
            </div>
         </form>
      </div>
   </div>
</div>