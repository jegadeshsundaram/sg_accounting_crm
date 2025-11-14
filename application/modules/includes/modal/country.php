
<!-- Add Country Modal -->
<div id="addCountryModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="modal-header">
            <h3 class="modal-title">Add Country</h3>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-12">
                  <label for="country_code" class="control-label">Country Code</label><br />
                  <input 
                     type="text"
                     name="country_code" id="country_code" 
                     style="width: 130px" class="form-control" />
                  <span class="country_code_error" style="display: none; color: red;">Duplicate code disallowed</span>
               </div>
               <div class="col-12">
                  <label for="country_name" class="control-label">Country Name</label><br />
                  <input 
                     type="text"
                     name="country_name" id="country_name" 
                     maxlength="60" 
                     class="form-control" />
               </div>
            </div>
         </div>
         <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default btn-sm btn-cancel" data-dismiss="modal" data-target="#addCountryModal">Cancel</button>
            <button type="button" id="btn-country-modal-save" class="btn btn-danger btn-sm float-right" style="float: right">Save</button>
         </div>
      </div>
   </div>
</div>