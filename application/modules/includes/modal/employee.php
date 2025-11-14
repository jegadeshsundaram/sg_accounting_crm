<!-- Add Employee Modal - starts -->
<div id="addEmployeeModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <h4 style="margin: 0;" class="dsply_item_header">New Staff</h4>
               <span style="margin: 0; font-size: 0.7rem;"><strong>Note: </strong>Create Staff to assign this quotation</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#addEmployeeModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body">
               <div class="row" style="margin-bottom: 15px">
                  <label for="employee_code" class="control-label col-12">Code</label>
                  <div class="col-12">
                  <input 
                     type="text"
                     name="employee_code" id="employee_code" 
                     class="form-control w-150" maxlength="12" />
                  <span class="employee_code_error error" style="display: none;">Duplicate code disallowed</span>
                  </div>
               </div>

               <div class="row" style="margin-bottom: 15px">
                  <label for="employee_name" class="control-label col-12">Name</label>
                  <div class="col-12">
                  <input 
                     type="text"
                     name="employee_name" id="employee_name" 
                     class="form-control w-300" maxlength="50" />
                  </div>
               </div>

               <div class="row" style="margin-bottom: 15px">
                  <label for="department_id" class="control-label col-12">Department</label>
                  <div class="col-12">
                     <select class="form-control select2" name="department_id" id="department_id">
                        <?php echo $department_options; ?>
                     </select>
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#addEmployeeModal">Cancel</button>
               <button type="button" id="btn-modal-employee-save" class="btn btn-danger btn-sm float-right">Save</button>
            </div>
         </div>
      </div>
   </div>
</div>