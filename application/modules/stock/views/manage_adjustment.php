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
               <li class="breadcrumb-item">Adjustment</li>
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
            <input type="hidden" id="process" />
            <input type="hidden" id="edit_id" />

            <form autocomplete="off" id="form_" method="post" action="<?php echo $save_url; ?>">
               <div class="card card-default">
                  <div class="card-header">
                     <h5>Adjustment</h5>
                     <a href="/stock/adjustment" class="btn btn-outline-secondary btn-sm float-right">Back</a>
                  </div>
                  <div class="card-body">

                     <div class="row form-group">
                        <label class="control-label col-md-3">Date : </label>
                        <div class="col-md-3">
                           <input 
                              type="text" 
                              id="doc_date" name="doc_date" 
                              value="<?php echo $page == 'edit' ? date('d-m-Y', strtotime($doc_date)) : ''; ?>"
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
                           <table id="tbl_items" class="table table-custom" style="min-width: 1000px; width: 100%; display: <?php echo $page == 'edit' ? 'inline-table' : 'none'; ?>">
                              <thead>
                                 <tr>
                                    <th class="w-130">Action</th>
                                    <th class="w-350">Product</th>
                                    <th class="w-120">UOM</th>
                                    <th class="w-150">Quantity</th>
                                    <th class="w-40">Sign</th>
                                    <th>Remarks</th>
                                    <th></th>
                                 </tr>
                              </thead>
                              <tbody>
                              <?php
                                 if ($page == 'edit') {
                                    $i = 0;
                                    $this->db->select('*');
                                    $this->db->from('stock_adjustment');
                                    $this->db->where(['document_date' => $doc_date, 'document_reference' => $ref_no, 'status' => 'C']);
                                    $this->db->order_by('adj_id', 'asc');
                                    $query = $this->db->get();
                                    $entries = $query->result();
                                    foreach ($entries as $value) {
                                       $billing = $this->custom->getMultiValues('master_billing', 'stock_code, billing_uom, billing_description', ['billing_id' => $value->product_id]);
                                 ?>
                                 <tr id="<?php echo $i; ?>">
                                    <td>
                                       <!-- Field : Entry Unique ID from DB -->
                                       <input 
                                          type="hidden" 
                                          id="adj_id_0" name="adj_id[]" 
                                          value="<?php echo $value->adj_id; ?>" 
                                          class="adj_id" />
                                       
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
                                          type="text" 
                                          id="sign_<?php echo $i; ?>" name="sign[]"
                                          value="<?php echo $value->sign; ?>"
                                          class="form-control sign" readonly />
                                    </td>

                                    <td>
                                    <input 
                                          type="text" 
                                          id="remarks_<?php echo $i; ?>" name="remarks[]" 
                                          value="<?php echo $value->remarks; ?>"
                                          class="form-control remarks" readonly />
                                    </td>
                                 </tr>
                                 <?php ++$i;
                                    }
                                 }
                                ?>
                              </tbody>
                           </table>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-12">
                           <a class="btn_add_item btn btn-outline-danger btn-sm" style="margin-right: 10px;"><i class="fa-solid fa-plus"></i> ADD ITEM</a>
                        </div>
                     </div>

                  </div>
                  <div class="card-footer">
                     <a href="/stock/adjustment" class="btn btn-info btn-sm">Cancel</a>                  
                     <button type="button" id="btn_submit" class="btn btn-warning btn-sm float-right">Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>


<!-- Modal :: Clone New Row -->
<table id="tbl_clone" style="display: none">
   <tbody>
      <tr id="0">
         <td>
            <input 
               type="hidden" 
               id="adj_id_0" name="adj_id[]" class="adj_id" />

            <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
            <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
         </td>
         
         <td>
            <input 
               type="text" 
               id="product_desc_0" 
               class="form-control product_desc" readonly />
            
            <input 
               type="hidden" 
               id="product_id_0" name="product_id[]" class="product_id" />
         </td>

         <td>
            <input 
               type="text" id="uom_0"
               class="form-control uom" readonly />
         </td>

         <td>
            <input 
               type="number" 
               id="quantity_0" name="quantity[]" 
               class="form-control quantity" readonly />
         </td>

         <td>
            <input 
               type="sign" 
               id="sign_0" name="sign[]" 
               class="form-control sign" readonly />
         </td>

         <td>
            <input 
               type="text" 
               id="remarks_0" name="remarks[]" 
               class="form-control remarks" readonly />
         </td>
      </tr>
   </tbody>
</table>

<!-- Modal :: Entry -->
<div id="entryModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-body">
               <input type="hidden" id="adj_id" />

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

               <div class="row mt-10 entry_field" style="display: none">
                  <div class="col-3">
                     <label class="control-label">UOM <span class="cl-red">*</span></label>
                     <input 
                        type="text" 
                        id="uom" class="form-control" />
                  </div>
                  <div class="col-4">
                     <label class="control-label">Quantity <span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="quantity" class="form-control" />
                  </div>
                  <div class="col-5">
                     <label class="control-label">Sign <span class="cl-red">*</span></label>
                     <select id="sign" class="form-control">
                        <option value="">-- Select --</option>
                        <option value="+">+</option>
                        <option value="-">-</option>
                     </select>
                  </div>
               </div>

               <div class="row mt-10 entry_field" style="display: none">
                  <div class="col-12">
                     <label class="control-label">Remarks</label>
                     <input type="text" id="remarks" class="form-control" />
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#entryModal">Cancel</button>
               <button type="button" class="btn btn-info btn-sm float-right" id="btn_save_item">SAVE</button>
            </div>
         </div>
      </div>
   </div>
</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript" src="/application/modules/stock/js/adj_process.js"></script>
