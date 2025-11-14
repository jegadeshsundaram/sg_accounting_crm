<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Accounts Receivable</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">AR</li>
               <li class="breadcrumb-item">Opening Balance</li>
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
            <!-- Curency Rate - hidde field -->
            <input type="hidden" id="customer_currency" value="<?php echo $customer_currency; ?>" />
            <input type="hidden" id="currency_rate" value="<?php echo $currency_rate; ?>" />

            <!-- Page - hidden field --> 
            <input type="hidden" id="process" />
            <input type="hidden" id="edit_id" />

            <form autocomplete="off" id="form_" method="post" action="<?php echo $save_url; ?>">
               <div class="card card-default">
                  <div class="card-header">
                     <h5>Opening Balance</h5>
                     <a href="/accounts_receivable/batch_ob_listing" class="btn btn-outline-secondary btn-sm float-right">Back</a>
                  </div>
                  <div class="card-body">
                     <div class="row">
                        <div class="col-md-4">
                           <label class="control-label">Customer : </label><br />
                           <select name="customer_id" id="customer_id" class="form-control" required>
                              <?php echo $customers; ?>
                           </select>
                        </div>
                     </div>
                     
                     <br /><br />

                     <div class="row">
                        <div class="col-md-12 table-responsive">
                           <table id="tbl_items" class="table table-custom" style="min-width: 1400px; width: 100%; display: <?php echo $page == 'edit' ? 'inline-table' : 'none'; ?>">
                              <thead>
                                 <tr>
                                    <th class="w-130">Action</th>
                                    <th class="w-110">Entry</th>
                                    <th class="w-140">Date</th>
                                    <th class="w-180">Reference</th>
                                    <th class="w-200">Amount <span class="f_curr" style="display: <?php echo $customer_currency == 'SGD' ? 'none' : 'inline-block'; ?>">(<?php echo $customer_currency; ?>)</span> $</th>
                                    <th class="w-200 dv_local" style="display: <?php echo $customer_currency == 'SGD' ? 'none' : 'table-cell'; ?>">Amount <strong>(SGD)</strong> $</th>
                                    <th>Remarks</th>
                                 </tr>
                              </thead>
                              <tbody>
                              <?php
                                 if ($page == 'edit') {
                                    $i = 0;
                                    $this->db->select('*');
                                    $this->db->from('ar_open');
                                    $this->db->where(['customer_id' => $customer_id, 'status' => 'C']);
                                    $query = $this->db->get();
                                    $ob_entries = $query->result();
                                    foreach ($ob_entries as $value) {
                              ?>
                                 <tr id="<?php echo $i; ?>">
                                    <td>
                                       <input 
                                          type="hidden" 
                                          id="entry_id_<?php echo $i; ?>" name="entry_id[]" 
                                          value="<?php echo $value->ar_ob_id; ?>"
                                          class="entry_id" />

                                       <input 
                                          type="hidden" 
                                          id="entry_<?php echo $i; ?>" name="entry[]" 
                                          value="<?php echo $value->sign; ?>"
                                          class="entry" />

                                       <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                                       <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                                    </td>
                                    
                                    <td>
                                       <input 
                                          type="text" 
                                          id="entry_type_<?php echo $i; ?>" 
                                          value="<?php echo $value->sign == '+' ? 'Debit' : 'Credit'; ?>"
                                          class="form-control entry_type" readonly />
                                    </td>

                                    <td>
                                       <input 
                                          type="text" 
                                          id="doc_date_<?php echo $i; ?>" name="doc_date[]" 
                                          value="<?php echo date('d-m-Y', strtotime($value->document_date)); ?>"
                                          class="form-control doc_date" readonly />
                                    </td>

                                    <td>
                                       <input 
                                          type="text" 
                                          id="ref_no_<?php echo $i; ?>" name="ref_no[]" 
                                          value="<?php echo $value->document_reference; ?>"
                                          class="form-control ref_no" readonly />
                                    </td>
                                    
                                    <td>
                                       <input 
                                          type="number" 
                                          id="foreign_amount_<?php echo $i; ?>" name="foreign_amount[]" 
                                          value="<?php echo $value->foreign_amount; ?>"
                                          class="form-control foreign_amount" readonly />
                                    </td>
                                    
                                    <td class="dv_local" style="display: <?php echo $customer_currency == 'SGD' ? 'none' : 'table-cell'; ?>">
                                       <input 
                                          type="number" 
                                          id="local_amount_<?php echo $i; ?>" name="local_amount[]" 
                                          value="<?php echo $value->local_amount; ?>"
                                          class="form-control local_amount" readonly />
                                    </td>

                                    <td>
                                       <input 
                                          type="text" 
                                          id="remarks_<?php echo $i; ?>" name="remarks[]" 
                                          value="<?php echo $value->remarks; ?>"
                                          class="form-control remarks" readonly />
                                    </td>
                                 </tr>
                                 <?php 
                                    ++$i;
                                    }
                                 }
                                ?>
                              </tbody>
                           </table>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-12">
                           <a class="btn_add btn btn-outline-danger btn-sm" style="margin-right: 10px; display: <?php echo $page == 'edit' ? 'inline' : 'none'; ?>"><i class="fa-solid fa-plus"></i> ADD ENTRY</a>
                        </div>
                     </div>

                  </div>
                  <div class="card-footer">
                     <a href="/accounts_receivable/batch_ob_listing" class="btn btn-info btn-sm">Cancel</a>                  
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
            <input type="hidden" id="entry_id_0" name="entry_id[]" class="entry_id" />
            <input type="hidden" id="entry_0" name="entry[]" class="entry" />

            <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
				<a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
         </td>
         
         <td>
            <input 
               type="text" 
               id="entry_type" class="form-control entry_type" readonly />
         </td>

         <td>
            <input 
               type="text" 
               id="doc_date_0" name="doc_date[]" class="form-control doc_date" readonly />
         </td>

         <td>
            <input 
               type="text" 
               id="ref_no_0" name="ref_no[]" class="form-control ref_no" readonly />
         </td>
         
         <td>
            <input 
               type="number" 
               id="foreign_amount_0" name="foreign_amount[]" 
               class="form-control foreign_amount" readonly />
         </td>
         
         <td class="dv_local">
            <input 
               type="number" 
               id="local_amount_0" name="local_amount[]" 
               class="form-control local_amount" readonly />
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
               <input type="hidden" id="entry_id" />

               <div class="row mt-10">
                  <div class="col-6">
                     <label class="control-label">Entry Type <span class="cl-red">*</span></label>
                  </div>
                  <div class="col-6">
                     <input type="radio" id="entry_debit" name="entry_type" value="+" class="radio-inp" autocomplete="off" checked="checked">
                     <label class="radio-lbl" for="entry_debit">DEBIT</label>
                     <input type="radio" id="entry_credit" name="entry_type" value="-" class="radio-inp" autocomplete="off">
                     <label class="radio-lbl" for="entry_credit">CREDIT</label>
                  </div>
               </div>

               <hr />

               <div class="row mt-10">
                  <div class="col-6">
                     <label class="control-label">Date <span class="cl-red">*</span></label>
                     <input 
                        type="text" 
                        id="doc_date" name="doc_date" 
                        class="form-control dp_full_date w-120" />
                  </div>
                  <div class="col-6">
                     <label class="control-label">Reference <span class="cl-red">*</span></label>
                     <input 
                        type="text" 
                        id="ref_no" name="ref_no" 
                        class="form-control" />
                     <span class="double_ref error" style="display: none">Duplicate reference disallowed</span>
                  </div>
               </div>
               
               <div class="row mt-10">
                  <div class="col-6">
                     <label class="control-label">Amount <span class="f_curr" style="font-weight: bold"></span> <span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="foreign_amount" class="form-control" />
                  </div>
                  <div class="col-6 dv_local" style="display: none">
                     <label class="control-label">Amount <strong>(SGD)</strong> <span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="local_amount"class="form-control" />
                  </div>
               </div>

               <div class="row mt-10">
                  <div class="col-md-12 col-12">
                     <label class="control-label">Remarks</label>
                     <input type="text" id="remarks" class="form-control" />
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#entryModal">Cancel</button>
               <button type="button" class="btn btn-info btn-sm float-right" id="btn_save_entry">Submit</button>
            </div>
         </div>
      </div>
   </div>
</div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script type="text/javascript" src="/application/modules/accounts_receivable/js/ob_process.js"></script>
