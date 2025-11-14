<div id="addDepartmentModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="modal-header">
            <h3 class="modal-title" style="text-align: center">Add Department</h3>
         </div>
         <div class="modal-body">
            <div class="row">               
               <label for="department_code" class="control-label col-3">Code</label>
               <div class="col-9">
                  <input 
                     type="text"
                     name="department_code" id="department_code"
                     class="form-control w-150" 
                     maxlength="12" />
                  <span class="department_code_error error" style="display: none;">Duplicate code disallowed</span>
               </div>
            </div>
            <div class="row" style="margin-top: 10px">               
               <label for="department_code" class="control-label col-3">Name</label>
               <div class="col-9">
                  <input 
                     type="text"
                     name="department_name" id="department_name"
                     class="form-control" />
               </div>
            </div>
         </div>
         <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" data-target="#addDepartmentModal">Cancel</button>
            <button type="button" id="btn-modal-department-save" class="btn btn-danger btn-sm float-right">Save</button>
         </div>
      </div>
   </div>
</div>