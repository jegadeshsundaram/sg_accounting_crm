<!-- Add Supplier Modal - starts -->
<div id="addSupplierModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <h4 style="margin: 0;" class="dsply_item_header">New Supplier</h4>
               <span style="margin: 0; font-size: 0.7rem;"><strong>Note: </strong>Create Supplier to use here</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#addSupplierModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body">
               <div class="row" style="margin-bottom: 15px">
                  <label for="suplr_code" class="control-label col-12">Code</label>
                  <div class="col-12">
                  <input 
                     type="text"
                     name="suplr_code" id="suplr_code" 
                     class="form-control w-150" maxlength="12" />
                  <span class="suplr_code_error error" style="display: none;">Duplicate code disallowed</span>
                  </div>
               </div>
               <div class="row" style="margin-bottom: 15px">
                  <label for="suplr_name" class="control-label col-12">Name</label>
                  <div class="col-12">
                  <input 
                     type="text"
                     name="suplr_name" id="suplr_name" 
                     class="form-control w-300"  maxlength="50" />
                  </div>
               </div>
               <div class="row" style="margin-bottom: 15px">
                  <label for="suplr_currency" class="control-label col-12">Currency</label>
                  <div class="col-12">
                     <select class="form-control select2" name="suplr_currency" id="suplr_currency">
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
               <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#addSupplierModal">Cancel</button>
               <button type="button" id="btn-modal-supplier-save" class="btn btn-danger btn-sm float-right">Save</button>
            </div>
         </div>
      </div>
   </div>
</div>