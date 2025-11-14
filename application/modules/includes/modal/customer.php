<!-- Add Customer Modal - starts -->
<div id="addCustomerModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <h4 style="margin: 0;" class="dsply_item_header">New Customer</h4>
               <span style="margin: 0; font-size: 0.7rem;"><strong>Note: </strong>Create Customer for this quotation</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#addCustomerModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body">
               <div class="row" style="margin-bottom: 15px">
                  <label for="cstmr_code" class="control-label col-12">Code</label>
                  <div class="col-12">
                  <input 
                     type="text"
                     name="cstmr_code" id="cstmr_code" 
                     class="form-control w-150" maxlength="12" />
                  <span class="cstmr_code_error error" style="display: none;">Duplicate code disallowed</span>
                  </div>
               </div>
               <div class="row" style="margin-bottom: 15px">
                  <label for="cstmr_name" class="control-label col-12">Name</label>
                  <div class="col-12">
                  <input 
                     type="text"
                     name="cstmr_name" id="cstmr_name" 
                     class="form-control w-300"  maxlength="50" />
                  </div>
               </div>
               <div class="row" style="margin-bottom: 15px">
                  <label for="cstmr_currency" class="control-label col-12">Currency</label>
                  <div class="col-12">
                     <select class="form-control select2" name="cstmr_currency" id="cstmr_currency">
                        <option value="">-- Select --</option>
                        <?php
                           $this->db->select('*');
                        $this->db->from('ct_currency');
                        $this->db->order_by('code', 'ASC');
                        $query = $this->db->get();
                        $currencies = $query->result();
                        $i = 1;
                        foreach ($currencies as $key => $value) { ?>
                           <option value="<?php echo $value->currency_id; ?>"><?php echo $value->code.' : '.$value->description.' ('.$value->rate.')'; ?></option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#addCustomerModal">Cancel</button>
               <button type="button" id="btn-modal-customer-save" class="btn btn-danger btn-sm float-right">Save</button>
            </div>
         </div>
      </div>
   </div>
</div>