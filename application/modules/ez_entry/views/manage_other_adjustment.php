<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">EZ Entry</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">EZ Entry</li>
               <li class="breadcrumb-item active">Adjustment</li>
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
            <form autocomplete="off" id="frm_" method="post">
                  
               <input type="hidden" id="process" />
               <input type="hidden" id="edit_id" />
               <input type="hidden" id="original_ref_no" value="<?php echo $ref_no; ?>" />
               <input type="hidden" id="page" value="<?php echo $page; ?>" />

               <div class="card card-default">
                  <div class="card-header">
                     <h5>Other Adjustment</h5>
                     <a href="/ez_entry/other_adjustment" class="btn btn-outline-dark btn-sm float-right">
                        <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                     </a>
                  </div>
                  <div class="card-body">
                     <div class="row">
                        <div class="col-md-6 col-5">
                           <div class="row form-group">
                              <label class="control-label col-md-4">Date<span class="cl-red">*</span></label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    id="doc_date" name="doc_date" 
                                    value="<?php echo $page == 'edit' ? date('d-m-Y', strtotime($doc_date)) : ''; ?>"
                                    class="form-control dp_full_date doc_date w-120" placeholder="dd-mm-yyyy" />
                              </div>
                           </div>
                        </div>

                        <div class="col-md-6 col-7">
                           <div class="row form-group">
                              <label class="control-label col-md-4">Reference<span class="cl-red">*</span></label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    id="ref_no" name="ref_no" 
                                    value="<?php echo $ref_no; ?>"
                                    maxlength="12" class="form-control ref_no w-180">
                                 <span class="error-ref error" style="display: none;">Duplicate reference disallowed</span>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-6 col-12">
                           <div class="row form-group">
                              <label class="control-label col-md-4">Remarks</label>
                              <div class="col-md-8">
                                 <input 
                                    type="text" 
                                    id="remarks" name="remarks" 
                                    value="<?php echo $remarks; ?>"
                                    maxlength="250" class="form-control w-350" />
                              </div>
                           </div>
                        </div>
                     </div>

                     <br />
                     <div class="row">
                        <div class="col-md-12 table-responsive">
                           <table id="tbl_items" class="table table-custom" style="min-width: 1400px; width: 100%; display: <?php echo $page == 'edit' ? 'block' : 'none'; ?>">
                              <thead>
                                 <tr>
                                    <th class="w-120">Action</th>
                                    <th class="w-350">Chart Of Account</th>
                                    <th class="w-350 ca_field" style="<?php echo $ca_field == '1' ? '' : 'display: none'; ?>">Control Account</th>
                                    <th class="w-180 txt-right er_field" style="<?php echo $er_field == '1' ? '' : 'display: none'; ?>">Exchange Rate</th>
                                    <th class="w-200 txt-right">Debit Amount $</th>
                                    <th class="w-200 txt-right">Credit Amount $</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                 if ($page == 'edit') {
                                    $i = 0;
                                    $this->db->select('*');
                                    $this->db->from('ez_adjustment');
                                    $this->db->where(['doc_date' => $doc_date, 'ref_no' => $ref_no]);
                                    $query = $this->db->get();
                                    $entries = $query->result();
                                    foreach ($entries as $value) { ?>
                                 <tr id="<?php echo $i; ?>">
                                    <td>
                                       <input 
                                          type="hidden" 
                                          id="entry_id_<?php echo $i; ?>" name="entry_id[]" 
                                          value="<?php echo $value->batch_id; ?>" class="entry_id" />

                                       <input 
                                          type="hidden" 
                                          id="sign_<?php echo $i; ?>" name="sign[]" 
                                          value="<?php echo $value->sign; ?>" class="sign" />
                                       
                                       <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                                       <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                                    </td>
                                    
                                    <td>
                                       <input 
                                          type="hidden" 
                                          id="accn_<?php echo $i; ?>" name="accn[]" 
                                          value="<?php echo $value->accn; ?>"
                                          class="accn" />

                                       <?php $accn_desc = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]); ?>
                                       <input 
                                          type="text" 
                                          id="accn_desc_<?php echo $i; ?>" 
                                          value="<?php echo $value->accn.' : '.$accn_desc; ?>"
                                          class="form-control-dsply accn_desc" readonly />


                                       <input 
                                          type="hidden" 
                                          id="gst_type_<?php echo $i; ?>" name="gst_type[]"
                                          value="<?php echo $value->gst_type; ?>" class="gst_type" />

                                       <input 
                                          type="hidden" 
                                          id="gst_category_<?php echo $i; ?>" name="gst_category[]" 
                                          value="<?php echo $value->gst_category; ?>" class="gst_category" />

                                       <input 
                                          type="hidden" 
                                          id="net_amount_<?php echo $i; ?>" name="net_amount[]" 
                                          value="<?php echo $value->net_amount; ?>" class="net_amount" />
                                       
                                       <input 
                                          type="hidden" 
                                          id="gst_amount_<?php echo $i; ?>" name="gst_amount[]" 
                                          value="<?php echo $value->gst_amount; ?>" class="gst_amount" />

                                       <?php $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $value->gst_category]); ?>
                                       <input 
                                          type="hidden" 
                                          id="gst_rate_<?php echo $i; ?>" value="<?php echo $gst_rate; ?>" class="gst_rate" />
                                    </td>

                                    <td class="ca_field" style="<?php echo $ca_field == '1' ? '' : 'display: none'; ?>">
                                       <?php 
                                          $ca_text = '';
                                          $currency_id = 0;
                                          $currency = "SGD";
                                          if($value->accn == "CA001") {
                                             $iden = $this->custom->getMultiValues('master_customer', 'name, code, currency_id', ['customer_id' => $value->control_account]);
                                             $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $iden->currency_id]);
                                             $ca_text = $iden->name.' ('.$iden->code.') '.$currency;
                                          } elseif($value->accn == "CL001") {
                                             $iden = $this->custom->getMultiValues('master_supplier', 'name, code, currency_id', ['supplier_id' => $value->control_account]);
                                             $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $iden->currency_id]);
                                             $ca_text = $iden->name.' ('.$iden->code.') '.$currency;
                                          } elseif($value->accn == "CA110") {
                                             $iden = $this->custom->getMultiValues('master_foreign_bank', 'fb_name, fb_code, currency_id', ['fb_id' => $value->control_account]);
                                             $currency = $this->custom->getSingleValue('ct_currency', 'code', ['currency_id' => $iden->currency_id]);
                                             $ca_text = $iden->fb_name.' ('.$iden->fb_code.') '.$currency;
                                          }

                                          $x_rate = '';
                                          if($value->accn == "CA001" || $value->accn == "CL001" || $value->accn == "CA110") {
                                             $x_rate = $value->exchange_rate;
                                          }
                                       ?>
                                       <input 
                                          type="text" 
                                          id="iden_<?php echo $i; ?>" 
                                          value="<?php echo $ca_text; ?>" 
                                          class="form-control-dsply iden" readonly />

                                       <input 
                                          type="hidden" 
                                          id="control_account_<?php echo $i; ?>" name="control_account[]" 
                                          value="<?php echo $value->control_account; ?>" class="control_account" />
                                    </td>
                                    
                                    <td class="er_field" style="<?php echo $er_field == '1' ? '' : 'display: none'; ?>">
                                       <input type="hidden" id="currency_<?php echo $i; ?>" value="<?php echo $currency; ?>" class="currency" />
                                       <input 
                                          type="number" 
                                          id="exchange_rate_<?php echo $i; ?>" name="exchange_rate[]" 
                                          value="<?php echo $currency == 'SGD' ? '' : $x_rate; ?>"
                                          class="form-control-dsply txt-right exchange_rate" readonly />
                                    </td>

                                    <td>
                                       <?php 
                                       $dr_foreign = false;
                                       if($value->sign == "+" && $currency !== "SGD") {
                                          $dr_foreign = true;
                                       } ?>
                                       <div class="input-group mb-2 dr_famt_field" style="<?php echo $dr_foreign ? '' : 'display: none'; ?>">
                                          <div class="input-group-prepend">
                                             <span class="input-group-text f_curr"><?php echo $currency; ?></span>
                                          </div>
                                          <input 
                                             type="number" 
                                             id="dr_foreign_amount_<?php echo $i; ?>" name="dr_foreign_amount[]" 
                                             value="<?php echo $value->sign == '+' ? $value->foreign_amount : ''; ?>"
                                             class="form-control dr_foreign_amount txt-right" readonly />
                                       </div>
                                       
                                       <div class="input-group dr_lamt_field" style="<?php echo $value->sign == '+' ? '' : 'display: none'; ?>">
                                          <div class="input-group-prepend">
                                             <span class="input-group-text">SGD</span>
                                          </div>
                                          <input 
                                             type="number" 
                                             id="dr_local_amount_<?php echo $i; ?>" name="dr_local_amount[]" 
                                             value="<?php echo $value->sign == '+' ? $value->local_amount : ''; ?>"
                                             class="form-control dr_local_amount txt-right" readonly />
                                       </div>
                                    </td>

                                    <td>
                                       <?php 
                                       $cr_foreign = false;
                                       if($value->sign == "-" && $currency !== "SGD") {
                                          $cr_foreign = true;
                                       } ?>
                                       <div class="input-group cr_famt_field" style="<?php echo $cr_foreign ? '' : 'display: none'; ?>">
                                          <div class="input-group-prepend">
                                             <span class="input-group-text f_curr"><?php echo $currency; ?></span>
                                          </div>
                                          <input 
                                             type="number" 
                                             id="cr_foreign_amount_<?php echo $i; ?>" name="cr_foreign_amount[]" 
                                             value="<?php echo $value->sign == '-' ? $value->foreign_amount : ''; ?>"
                                             class="form-control cr_foreign_amount txt-right" readonly />
                                       </div>

                                       <div class="input-group cr_lamt_field" style="<?php echo $value->sign == '-' ? '' : 'display: none'; ?>">
                                          <div class="input-group-prepend">
                                             <span class="input-group-text">SGD</span>
                                          </div>
                                          <input  
                                             type="number" 
                                             id="cr_local_amount_<?php echo $i; ?>" name="cr_local_amount[]" 
                                             value="<?php echo $value->sign == '-' ? $value->local_amount : ''; ?>"
                                             class="form-control cr_local_amount txt-right" readonly />
                                       </div>
                                       
                                    </td>

                                    <td>
                                       
                                    </td>
                                 </tr>
                                 <?php
                                    ++$i;
                                    }
                                 } ?>
                              </tbody>
                              <tfoot>
                                 <tr>
                                    <td></td>
                                    <td class="ca_field" style="<?php echo $ca_field == '1' ? '' : 'display: none'; ?>"></td>
                                    <td class="er_field" style="<?php echo $er_field == '1' ? '' : 'display: none'; ?>"></td>
                                    <td style="text-align: right; color: blue; font-style: italic;">Sub Total (SGD)</td>
                                    <td class="dr_total" style="text-align: right; padding-right: 25px; color: gray; font-weight: bold;">0.00</td>
                                    <td class="cr_total" style="text-align: right; padding-right: 25px; color: gray; font-weight: bold;">0.00</td>
                                 </tr>
                              </tfoot>
                           </table>
                        </div>
                     </div>

                     <br />

                     <div class="row">
                        <div class="col-md-12">
                           <a href="#" class="btn_add_entry btn btn-outline-danger btn-sm" style="margin-right: 10px"><i class="fa-solid fa-plus"></i> ADD ENTRY</a>
                        </div>
                     </div>
                        
                  </div>
                  <div class="card-footer">
                     <button type="button" id="btn_save_to_batch" class="btn btn-primary btn-sm">SAVE TO BATCH</button>
                     <button type="button" id="btn_post_to_accounts" class="btn btn-info btn-sm float-right">POST TO ACCOUNTS</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Model of new row -->
<table id="tbl_clone" style="display: none">
   <tbody>
      <tr id="0">
         <td>
            <input type="hidden" id="entry_id_0" name="entry_id[]" class="entry_id" />
            <input type="hidden" id="sign_0" name="sign[]" class="sign" />

            <a class="ct-btn blue dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
            <a class="ct-btn red dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
         </td>

         <td>
            <input type="hidden" id="accn_0" name="accn[]" class="accn" />
            <input type="text" id="accn_desc_0" class="form-control-dsply accn_desc" readonly />

            <input type="hidden" id="gst_type_0" name="gst_type[]" class="gst_type" />
            <input type="hidden" id="gst_category_0" name="gst_category[]" class="gst_category" />
            <input type="hidden" id="net_amount_0" name="net_amount[]" class="net_amount" />
            <input type="hidden" id="gst_amount_0" name="gst_amount[]" class="gst_amount" />
            <input type="hidden" id="gst_rate_0" class="gst_rate" />
         </td>

         <td class="ca_field" style="display: none">
            <input type="hidden" id="control_account_0" name="control_account[]" class="control_account" readonly />
            <input type="text" id="iden_0" class="form-control-dsply iden" readonly />
         </td>
         
         <td class="er_field" style="display: none">
            <input type="hidden" id="currency_0" class="currency" />
            <input type="number" id="exchange_rate_0" name="exchange_rate[]" class="form-control-dsply txt-right exchange_rate" readonly />
         </td>

         <td>
            <div class="input-group mb-2 dr_famt_field" style="display: none">
               <div class="input-group-prepend">
                  <span class="input-group-text f_curr"></span>
               </div>
               <input 
                  type="number" 
                  id="dr_foreign_amount_0" name="dr_foreign_amount[]" 
                  class="form-control dr_foreign_amount txt-right" readonly />
            </div>
            
            <div class="input-group dr_lamt_field" style="display: none">
               <div class="input-group-prepend">
                  <span class="input-group-text">SGD</span>
               </div>
               <input 
                  type="number" 
                  id="dr_local_amount_0" name="dr_local_amount[]" 
                  class="form-control dr_local_amount txt-right" readonly />
            </div>
         </td>

         <td>
            <div class="input-group cr_famt_field" style="display: none">
               <div class="input-group-prepend">
                  <span class="input-group-text f_curr"></span>
               </div>
               <input 
                  type="number" 
                  id="cr_foreign_amount_0" name="cr_foreign_amount[]" 
                  class="form-control cr_foreign_amount txt-right" readonly />
            </div>

            <div class="input-group cr_lamt_field" style="display: none">
               <div class="input-group-prepend">
                  <span class="input-group-text">SGD</span>
               </div>
               <input  
                  type="number" 
                  id="cr_local_amount_0" name="cr_local_amount[]" 
                  class="form-control cr_local_amount txt-right" readonly />
            </div>
         </td>
      </tr>
   </tbody>
</table>

<!-- Transaction Modal -->
<div id="entryModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-body">
               <input type="hidden" id="entry_id" />
               <input type="hidden" id="sign" value="+" />

               <div class="row mt-10">
                  <div class="col-4">
                     <label class="control-label">Entry Type<span class="cl-red">*</span></label>
                  </div>
                  <div class="col-8">
                     <input type="radio" id="entry_debit" name="entry_type" value="+" class="radio-inp" autocomplete="off" checked="checked">
                     <label class="radio-lbl" for="entry_debit">DEBIT</label>
                     <input type="radio" id="entry_credit" name="entry_type" value="-" class="radio-inp" autocomplete="off">
                     <label class="radio-lbl" for="entry_credit">CREDIT</label>

                     <input type="hidden" id="sign" />
                  </div>
               </div>
               
               <hr />

               <div class="row form-group mt-10">
                  <div class="col-12">
                     <label class="control-label">Chart Of Account<span class="cl-red">*</span></label>
                     <select id="accn" class="form-control">
                        <?php echo $co_accns; ?>
                     </select>
                  </div>
               </div>

               <div class="row form-group mt-10 customer_field" style="display: none">
                  <div class="col-12">
                     <label class="control-label">Customer<span class="cl-red">*</span></label>
                     <select id="customer" class="form-control customer">
                        <?php echo $customers; ?>
                     </select>
                  </div>
               </div>

               <div class="row form-group mt-10 supplier_field" style="display: none">
                  <div class="col-12">
                     <label class="control-label">Supplier<span class="cl-red">*</span></label>
                     <select id="supplier" class="form-control supplier">
                        <?php echo $suppliers; ?>
                     </select>
                  </div>
               </div>

               <div class="row form-group mt-10 foreign_bank_field" style="display: none">
                  <div class="col-12">
                     <label class="control-label">Foreign Bank<span class="cl-red">*</span></label>
                     <select id="foreign_bank" class="form-control foreign_bank">
                        <?php echo $foreign_banks; ?>
                     </select>
                  </div>
               </div>

               <div class="row form-group mt-10 gst_options_field" style="display: none">
                  <div class="col-12">
                     <label class="control-label">GST Type<span class="cl-red">*</span></label>
                     <select id="gst_type" class="form-control">
                        <option value="">--Select--</option>
                        <option value="I">Input Tax</option>
                        <option value="OR">Reverse Output Tax</option>
                        <option value="S">Settlement</option>
                     </select>
                  </div>
               </div>

               <div class="row form-group mt-10 currency_field" style="display: none">
                  <div class="col-6">
                     <label class="control-label">Currency</label>
                     <input 
                        type="text" 
                        id="currency" class="form-control w-120" value="SGD" readonly />
                  </div>
                  <div class="col-6">
                     <label class="control-label">Exchange Rate<span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="exchange_rate" class="form-control w-180" />
                  </div>
               </div>

               <div class="row form-group mt-10">
                  <div class="col-6 famt_field" style="display: none">
                     <label class="control-label">Amount <span class="f_curr"></span><span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="foreign_amount" class="form-control" />
                  </div>
                  <div class="col-6 lamt_field" style="display: none">
                     <label class="control-label">Amount <span>(SGD)</span><span class="cl-red">*</span></label>
                     <input 
                        type="number" 
                        id="local_amount" class="form-control" />
                  </div>
               </div>

               <!-- GST Type :: Input -->
               <div class="input_tax_field" style="display: none">
                  <div class="row form-group mt-10">
                     <div class="col-12">
                        <label class="control-label">Net Purchase $<span class="cl-red">*</span></label>
                        <input 
                           type="number" 
                           id="purchase_amount" class="form-control" />
                     </div>
                  </div>
                  <div class="row form-group mt-10">
                     <div class="col-12">
                        <label class="control-label">GST Input Category<span class="cl-red">*</span></label>
                        <select id="gst_input_category" class="form-control">
                           <?php echo $gst_input_categories; ?>
                        </select>
                     </div>
                  </div>
                  <div class="row form-group mt-10">
                     <div class="col-6">
                        <label class="control-label">GST Rate %</label>
                        <input 
                           type="number" 
                           id="gst_input_rate" class="form-control w-80" readonly />
                     </div>

                     <div class="col-6">
                        <label class="control-label">GST Amount $<span class="cl-red">*</span></label>
                        <input 
                           type="number" 
                           id="gst_input_amount" class="form-control" />
                     </div>
                  </div>
               </div>

               <!-- GST Type :: Reverse Output -->
               <div class="output_tax_field" style="display: none">
                  <div class="row form-group mt-10">
                     <div class="col-12">
                        <label class="control-label">Net Sales $<span class="cl-red">*</span></label>
                        <input 
                           type="number" 
                           id="sales_amount" class="form-control" />
                     </div>
                  </div>
                  <div class="row form-group mt-10">
                     <div class="col-12">
                        <label class="control-label">GST Output Category<span class="cl-red">*</span></label>
                        <select id="gst_output_category" class="form-control">
                           <?php echo $gst_output_categories; ?>
                        </select>
                     </div>
                  </div>
                  <div class="row form-group mt-10">
                     <div class="col-6">
                        <label class="control-label">GST Rate %</label>
                        <input 
                           type="number" 
                           id="gst_output_rate" class="form-control w-80" readonly />
                     </div>
                     <div class="col-6">
                        <label class="control-label">GST Amount $<span class="cl-red">*</span></label>
                        <input 
                           type="number" 
                           id="gst_output_amount" class="form-control" />
                     </div>
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#entryModal">Cancel</button>
               <button type="button" class="btn btn-info btn-sm float-right" id="btn_save_entry">SAVE</button>
            </div>
         </div>
      </div>
   </div>
</div>

<div id="totalModal" class="modal fade in" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <h4 class="modal-title"><span style="color: red;">INVALID!</span> Total of Debit and Credit are Not Equal</h4>
            </div>
            <div class="card-body">
               <p>Debit &amp; Credit Amount Details</p>
               <table class="total_table">
                  <tbody>
                     <tr>
                        <td style="width: 60%;">Total Debit Amount <span style="color: red; font-weight: bold;">[SGD]</span> </td>
                        <td id="debit_total" align="right"></td>
                     </tr>
                     <tr>
                        <td>Total Credit Amount <span style="color: red; font-weight: bold;">[SGD]</span> </td>
                        <td id="credit_total" align="right"></td>
                     </tr>
                     <tr>
                        <td style="color: blue;">Total Amount Difference <span style="color: red; font-weight: bold;">[SGD]</span> </td>
                        <td style="color: blue;" id="debit_credit_diff_total" align="right"></td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div class="card-footer" style="text-align: center;">
               <button type="button" data-toggle="modal" data-target="#totalModal" class="btn btn-warning btn-sm">CLOSE</button>
            </div>
         </div>
      </div>
   </div>
</div>

<style>
   tfoot {
      border-top: 3px solid lightgray;
   }   
   
   .total_table {
      width: 100%;
   }

   .total_table td {
      border: 1px solid #ccc;
      padding: 5px;
      text-align: right;
   }

   .input-group-text {
      background-color: white;
      border: none;
   }
</style>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    var page = "";

    // document starts
    $(function() {

      $('select').select2();

      page = $('#page').val();
      if(page == "edit") {
         process_total(false);
      }

      var row_number = '';
      var duplicate_ref = false;
      
      $(document).on("change", "#doc_date, #remarks", function() {
         if($(this).val() == "") {
            return false;
         }

         save_adjustment();
      });
      
      $(document).on("change", "#ref_no", function() {
         if($(this).val() == "") {
            return false;
         }

         duplicate_ref = false;
         $(".error-ref").hide();

         
         if(page == "edit" && $(this).val() == $('#original_ref_no').val()) {
            $(".error-ref").hide();
            return;
         }

         // Double Check Reference in GL and adjustment tbl
         $.post('/ez_entry/ajax/double_adjustment', {
            ref_no: $(this).val()
         }, function(ref) {
            if (ref > 0) {
               duplicate_ref = true;
               $("#ref_no").focus();
               $(".error-ref").show();
            } else {
               $(".error-ref").hide();
               save_adjustment();
            }
         });
      });

      $(".btn_add_entry").on('click', function() {
         if(!isFormValid()) {
            return false;
         } else if(duplicate_ref) {
            $('#ref_no').focus();
            return false;
         }

         clear();

         $('#sign').val('+');
         $('#entry_debit').prop("checked", true);

         $('#process').val('add');
         $('#entryModal').modal('show');
      });

      $('input[type=radio][name=entry_type]').change(function() {
         if (this.value == '+') {
            $('#sign').val("+");
         } else if (this.value == '-') {
            $('#sign').val("-");
         }
      });

      $(document).on('change', '#accn', function() {
         var accn = $('option:selected', this).val();
         
         if(accn !== "") {
            $('.customer_field').hide();
            $('.supplier_field').hide();
            $('.foreign_bank_field').hide();
            $('.gst_options_field, .input_tax_field, .output_tax_field').hide();

            $('.currency_field').hide();
            $('.famt_field').hide();
            $('.lamt_field').hide();
            $('#currency').val("");
            $('#exchange_rate').val("");
            $('#foreign_amount').val("");
            $('#local_amount').val("");

            $('#customer').select2("destroy").val("").select2();
            $('#supplier').select2("destroy").val("").select2();
            $('#foreign_bank').select2("destroy").val("").select2();
            $('#gst_type').select2("destroy").val("").select2();
            
            if(accn == "CA001") { // debtors control account

               $('.customer_field').show();

            } else if(accn == "CL001") { // creditors control account

               $('.supplier_field').show();

            } else if(accn == "CA110") { // foreign bank account

               $('.foreign_bank_field').show();

            } else if(accn == "CL300") { // goods & services account

               $('.gst_options_field').show();

            } else { // other accounts
               $('.famt_field').show();
            }
            
            $('#foreign_amount').focus();
         }
      });

      $(document).on('change', '#customer', function() {
         $('#currency').val("");
         $('#exchange_rate').val("");
         $('#foreign_amount').val("");
         $('#local_amount').val("");

         $('.currency_field').hide();
         $('.famt_field').hide();
         $('.lamt_field').hide();

         var id = $('option:selected', this).val();
         if(id == "") {
            return false;
         }

         $.post('/ez_entry/ajax/get_customer_details', {
            customer_id: id
         }, function (data) {
            var obj = $.parseJSON(data);
            $("#currency").val(obj.currency);
            $("#exchange_rate").val(obj.currency_amount);

            if(obj.currency == "SGD") {
               $('.famt_field').show();
            } else {
               $('.currency_field').show();
               $('.famt_field').show();
               $('.lamt_field').show();
            }

            $('#foreign_amount').focus();
         });
      });

      $(document).on('change', '#supplier', function() {
         $('#currency').val("");
         $('#exchange_rate').val("");
         $('#foreign_amount').val("");
         $('#local_amount').val("");

         $('.currency_field').hide();
         $('.famt_field').hide();
         $('.lamt_field').hide();

         var id = $('option:selected', this).val();
         if(id == "") {
            return false;
         }

         $.post('/ez_entry/ajax/get_supplier_details', {
            supplier_id: id
         }, function (data) {
            var obj = $.parseJSON(data);
            $("#currency").val(obj.currency);
            $("#exchange_rate").val(obj.currency_amount);

            if(obj.currency == "SGD") {
               $('.famt_field').show();
            } else {
               $('.currency_field').show();
               $('.famt_field').show();
               $('.lamt_field').show();
            }

            $('#foreign_amount').focus();
         });
      });

      $(document).on('change', '#foreign_bank', function() {
         $('#currency').val("");
         $('#exchange_rate').val("");
         $('#foreign_amount').val("");
         $('#local_amount').val("");

         $('.currency_field').hide();
         $('.famt_field').hide();
         $('.lamt_field').hide();

         var id = $('option:selected', this).val();
         if(id == "") {
            return false;
         }

         $.post('/ez_entry/ajax/get_fbank_details', {
            fb_id: id
         }, function (data) {
            var obj = $.parseJSON(data);
            $("#currency").val(obj.currency_code);
            $("#exchange_rate").val(obj.currency_rate);

            if(obj.currency_code == "SGD") {
               $('.famt_field').show();
            } else {
               $('.currency_field').show();
               $('.famt_field').show();
               $('.lamt_field').show();
            }

            $('#foreign_amount').focus();
         });
      });

      $(document).on('change', '#gst_type', function() {
         var gst_type = $('option:selected', this).val();

         $('.input_tax_field, .output_tax_field, .famt_field').hide();
         $('#purchase_amount').val("");
         $('#gst_input_category').select2("destroy").val("").select2();
         $('#gst_input_amount').val("");
         $('#gst_input_rate').val("");

         $('#sales_amount').val("");
         $('#gst_output_category').select2("destroy").val("").select2();
         $('#gst_output_amount').val("");
         $('#gst_output_rate').val("");

         $('#foreign_amount').val("");

         if(gst_type == "I") {
            $('.input_tax_field').show();
            $('#purchase_amount').focus();
         } else if(gst_type == "OR") {
            $('.output_tax_field').show();
            $('#sales_amount').focus();
         } else if(gst_type == "S") {
            $('.famt_field').show();
            $('#foreign_amount').focus();
         }
      });

      $(document).on("change", "#purchase_amount", function() {
         if($(this).val() !== "") {

            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);

            if($('#gst_input_rate').val() !== "") {
               get_gst_amount('gst_input_amount', $(this).val(), $('#gst_input_rate').val());
            }
         }
      });     

      $(document).on('change', '#gst_input_category', function() {
         var gst_category = $('option:selected', this).val();
         $.post('/ez_entry/ajax/get_gst_details', {
            gst_code: gst_category
         }, function (data) {
            if(data !== "") {
               var obj = $.parseJSON(data);
               $('#gst_input_rate').val(Number(obj.gst_percentage));

               get_gst_amount('gst_input_amount', $('#purchase_amount').val(), $('#gst_input_rate').val());
            }
         });
      });

      $(document).on("change", "#sales_amount", function() {
         if($(this).val() !== "") {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);

            if($('#gst_output_rate').val() !== "") {
               get_gst_amount('gst_output_amount', $(this).val(), $('#gst_output_rate').val());
            }
         }
      });

      $(document).on('change', '#gst_output_category', function() {
         var gst_category = $('option:selected', this).val();
         $.post('/ez_entry/ajax/get_gst_details', {
            gst_code: gst_category
         }, function (data) {
            if(data !== "") {
               var obj = $.parseJSON(data);
               $('#gst_output_rate').val(Number(obj.gst_percentage));
               
               get_gst_amount('gst_output_amount', $('#sales_amount').val(), $('#gst_output_rate').val());
            }
         });
      });

      $(document).on("keyup", "#foreign_amount", function() {
         if($(this).val() !== "") {
            get_local_amount();
         }
      });

      $(document).on("change", "#foreign_amount", function() {
         var amount = 0;
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);
         } else {
            $("#local_amount").val('');
         }
      });

      $(document).on("change", "#exchange_rate", function() {
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            $(this).val(Number($(this).val()).toFixed(5));
            get_local_amount();
         }
      });    

      $(document).on('click', '.dt_edit', function() {

         clear();

         row_number = $(this).closest('tr').attr('id');
         
         edit_entry(row_number);

         $('#process').val('edit');
         $('#edit_id').val(row_number);

         $('#entryModal').modal('show');
      });      

      // DELETE
      $(document).on('click', '.dt_delete', function () {
         row_number = $(this).closest('tr').attr("id");
         $.confirm({
            title: '<i class="fa fa-info"></i> Confirm Delete',
            content: 'Are you sure to Delete?</strong>',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function() {
                     $.post('/ez_entry/ajax/delete_adjustment_entry', {
                        entry_id: $('#entry_id_'+row_number).val()
                     }, function (status) {
                        if($.trim(status) == 'deleted') {
                           toastr.success("Deleted!");
                           $('tr#'+row_number).remove();

                           if($('#tbl_items > tbody > tr').length > 0) {
                              sortTblRowsByID();
                           } else {
                              $('#tbl_items').hide();
                              $('.dv_bank').hide();
                           }

                           // if any row with accn = CA001 || CL001 || CA110 then table will display with control account and exchange rate rows
                           var ca_field = false;
                           var er_field = false;
                           $("#tbl_items tbody tr").each(function () {
                              row_number = $(this).attr('id');
                              if($("#accn_"+row_number).val() == 'CA001' || $("#accn_"+row_number).val() == 'CL001' || $("#accn_"+row_number).val() == 'CA110') {
                                 ca_field = true;

                                 if($("#currency_"+row_number).val() !== "SGD") {
                                    er_field = true;
                                 }
                              }
                           });

                           if(!ca_field) {
                              $('#tbl_items .ca_field').hide();
                           }
                           if(!er_field) {
                              $('#tbl_items .er_field').hide();
                           }


                           process_total(false);
                           
                        } else {
                           toastr.error("Post Error!");
                        }
                     });
                  }
               },
               no: {
                  btnClass: 'btn-dark',
                  action: function(){
                  }
               },
            }
         });
      });

      // add entry to transaction
      $("#btn_save_entry").on('click', function (e) {

         if(!isFormValid() || !isModalValid()) {
            return false;
         }

         save();
      });

      // save to batch
      $("#btn_save_to_batch").on('click', function (e) {
         if(isFormValid() && isItemsValid()) {
            toastr.success("SAVED!");
            window.location.href = '/ez_entry/other_adjustment';
         }
      });

      // post to GL
      $("#btn_post_to_accounts").on('click', function (e) {
         if(isFormValid() && $('#tbl_items > tbody > tr').length > 0) {
            process_total(true);           
         }
      });

   }); // document ends


   function isFormValid() {
      var valid = true;
      if($('#doc_date').val() == "") {
         $('#doc_date').focus();
         valid = false;
      } else if($('#ref_no').val() == "") {
         $('#ref_no').focus();
         valid = false;
      }

      return valid;
   }

   function isModalValid() {
      var valid = true;
      if($('#accn').val() == "") {
         $("#accn").select2('open');
         valid = false;

      } else if($('#accn').val() == "CA001" && $('#customer').val() == "") {
         $("#customer").select2('open');
         valid = false;

      } else if($('#accn').val() == "CL001" && $('#supplier').val() == "") {
         $("#supplier").select2('open');
         valid = false;

      } else if($('#accn').val() == "CA110" && $('#foreign_bank').val() == "") {
         $("#foreign_bank").select2('open');
         valid = false;

      } else if($('#accn').val() == "CL300" && $('#gst_type').val() == "") {
         $("#gst_type").select2('open');
         valid = false;

      } else if($('#gst_type').val() == "I" && $('#purchase_amount').val() == "") {
         $('#purchase_amount').focus();
         valid = false;

      } else if($('#gst_type').val() == "I" && $('#gst_input_category').val() == "") {
         $("#gst_input_category").select2('open');
         valid = false;

      } else if($('#gst_type').val() == "I" && $('#gst_input_amount').val() == "") {
         $('#gst_input_amount').focus();
         valid = false;
      
      } else if($('#gst_type').val() == "OR" && $('#sales_amount').val() == "") {
         $('#sales_amount').focus();
         valid = false;

      } else if($('#gst_type').val() == "OR" && $('#gst_output_category').val() == "") {
         $("#gst_output_category").select2('open');
         valid = false;

      } else if($('#gst_type').val() == "OR" && $('#gst_output_amount').val() == "") {
         $('#gst_output_amount').focus();
         valid = false;

      } else if($('#accn').val() !== "CL300" && $('#foreign_amount').val() == "") {
         $('#foreign_amount').focus();
         valid = false;
      }

      return valid;
   }

   function clear() {
      $('#entry_id').val('');
      $('#edit_id').val('');

      $('#sign').val('');

      $('.customer_field').hide();
      $('.supplier_field').hide();
      $('.foreign_bank_field').hide();

      $('.currency_field, .famt_field, .lamt_field').hide();
      $('#entryModal .f_curr').text("");

      $('.gst_options_field, .input_tax_field, .output_tax_field').hide();

      $('#accn').select2("destroy").val('').select2();
      $('#customer').select2("destroy").val('').select2();
      $('#supplier').select2("destroy").val('').select2();
      $('#foreign_bank').select2("destroy").val('').select2();

      $('#currency').val('');
      $('#exchange_rate').val('');

      $('#foreign_amount').val('');
      $('#local_amount').val('');
   }  

   function isItemsValid() {
      var valid = true;

      $("#tbl_items tbody tr").each(function (i, val) {
         row_number = $(this).attr("id");

         if($("#accn_"+row_number).val() == "") {
            $("#accn_"+row_number).select2('open');
            valid = false;

         } else if($("#amount_"+row_number).val() == "") {
            $("#amount_"+row_number).focus();
            valid = false;
         }
      
      });

      return valid;
   }

   function get_local_amount() {
      var exchange_rate = $("#exchange_rate").val();
      var famt = $('#foreign_amount').val();
      
      var local_amount = 0;

      if(exchange_rate !== "") {
         local_amount = Number(famt) / Number(exchange_rate);
         $('#local_amount').val(local_amount.toFixed(2));
      } else {
         $('#local_amount').val(Number(famt).toFixed(2));
      }
   }

   function get_gst_amount(gst_field, amount, gst_rate) {
      var gst_amount = Math.round(amount * gst_rate) / 100;

      $('#'+gst_field).val(gst_amount.toFixed(2));
   }

   // if any changes done in doc_date or ref_no after adding entries, then system will insert all the entries again with updated header
   function save_adjustment() {
      $("#tbl_items tbody tr").each(function () {
         clear();
         $('#process').val("");

         row_number = $(this).attr('id');
         edit_entry(row_number);

         save();
      });
   }

   function amount_display(row_number) {

      var sign = $('#sign_'+row_number).val();

      $('.currency_field').hide();
      $('.famt_field').hide();
      $('.lamt_field').hide();

      var currency = $('#currency_'+row_number).val();
      $('#currency').val(currency);

      if(currency == "SGD") {

         $('.famt_field').show();

      } else { // other currencies
         
         $('#exchange_rate').val($('#exchange_rate_'+row_number).val());

         $('.currency_field').show();
         $('.famt_field').show();
         $('.lamt_field').show();
      }

      if(sign == "+") {
         $('#foreign_amount').val($('#dr_foreign_amount_'+row_number).val());
         $('#local_amount').val($('#dr_local_amount_'+row_number).val());
      } else if(sign == "-") {
         $('#foreign_amount').val($('#cr_foreign_amount_'+row_number).val());
         $('#local_amount').val($('#cr_local_amount_'+row_number).val());
      }

   }

   function edit_entry(row_number) {
      $('#entry_id').val($('#entry_id_'+row_number).val());
         
      var sign = $('#sign_'+row_number).val();
      $('#sign').val(sign);

      if(sign == "+") {
         $('#entry_debit').prop("checked", true);
      } else if(sign == "-") {
         $('#entry_credit').prop("checked", true);
      }

      var accn = $('#accn_'+row_number).val();
      var control_account = $('#control_account_'+row_number).val();

      $('#accn').select2("destroy").val(accn).select2();

      if(accn == "CA001") { // debtors control account

         $('#customer').select2("destroy").val(control_account).select2();
         $('.customer_field').show();

         amount_display(row_number);

      } else if(accn == "CL001") { // creditors control account

         $('#supplier').select2("destroy").val(control_account).select2();
         $('.supplier_field').show();

         amount_display(row_number);

      } else if(accn == "CA110") { // foreign bank account

         $('#foreign_bank').select2("destroy").val(control_account).select2();
         $('.foreign_bank_field').show();

         amount_display(row_number);

      } else if(accn == "CL300") { // goods & services account

         var gst_type = $('#gst_type_'+row_number).val();
         var gst_category = $('#gst_category_'+row_number).val();
         
         $('#gst_type').select2("destroy").val(gst_type).select2();
         $('.gst_options_field').show();

         if(gst_type == "I") {
            
            $('#purchase_amount').val($('#net_amount_'+row_number).val());               
            $('#gst_input_category').select2("destroy").val(gst_category).select2();
            $('#gst_input_amount').val($('#gst_amount_'+row_number).val());
            $('#gst_rate').val($('#gst_rate_'+row_number).val());
            $('#gst_input_rate').val($('#gst_rate_'+row_number).val());

            $('.input_tax_field').show();

         } else if(gst_type == "OR") {

            $('#sales_amount').val($('#net_amount_'+row_number).val());
            $('#gst_output_category').select2("destroy").val(gst_category).select2();
            $('#gst_output_amount').val($('#gst_amount_'+row_number).val());
            $('#gst_output_rate').val($('#gst_rate_'+row_number).val());

            $('.output_tax_field').show();

         } else if(gst_type == "S") {
            
            if(sign == "+") {
               $('#foreign_amount').val($('#dr_foreign_amount_'+row_number).val());
            } else if(sign == "-") {
               $('#foreign_amount').val($('#cr_foreign_amount_'+row_number).val());
            }

            $('.famt_field').show();

         }
      } else { // other accounts
         $('.famt_field').show();

         if(sign == "+") {
            $('#foreign_amount').val($('#dr_foreign_amount_'+row_number).val());
            $('#local_amount').val($('#dr_local_amount_'+row_number).val());
         } else if(sign == "-") {
            $('#foreign_amount').val($('#cr_foreign_amount_'+row_number).val());
            $('#local_amount').val($('#cr_local_amount_'+row_number).val());
         }
      }
   }

   function save() {
      var entry_id = $("#entry_id").val();

      // header values
      var doc_date = $("#doc_date").val();
      var ref_no = $("#ref_no").val();
      var remarks = $("#remarks").val();

      // body values
      var accn = $("#accn").val();
      var foreign_amount = $("#foreign_amount").val();
      var local_amount = $("#local_amount").val();
      var sign = $('#sign').val();
      

      var control_account = 0;
      if(accn == "CA001") {
         control_account = $('#customer').val();
      } else if(accn == "CL001") {
         control_account = $('#supplier').val();
      } else if(accn == "CA110") {
         control_account = $('#foreign_bank').val();
      }

      var exchange_rate = $('#exchange_rate').val();

      var gst_type = '';
      var gst_category = '';
      var net_amount = 0;
      var gst_amount = 0;      

      if(accn == "CL300") {
         
         gst_type = $('#gst_type').val();

         if(gst_type == "I") {
            gst_category = $('#gst_input_category').val();
            net_amount = $('#purchase_amount').val();
            gst_amount = $('#gst_input_amount').val();

            foreign_amount = gst_amount;
            local_amount = gst_amount;

         } else if(gst_type == "OR") {
            gst_category = $('#gst_output_category').val();
            net_amount = $('#sales_amount').val();
            gst_amount = $('#gst_output_amount').val();

            foreign_amount = gst_amount;
            local_amount = gst_amount;
         }
      }

      $.post('/ez_entry/ajax/save_adjustment', {
         entry_id: entry_id,
         doc_date: doc_date,
         ref_no: ref_no,
         remarks: remarks,
         accn: accn,
         control_account: control_account,
         exchange_rate: exchange_rate,
         foreign_amount: foreign_amount,
         local_amount: local_amount,
         sign: sign,
         gst_type: gst_type,
         gst_category: gst_category,
         net_amount: net_amount,
         gst_amount: gst_amount
      }, function(entry_id) {
         if(entry_id !== "") {
            $("#entry_id").val($.trim(entry_id));

            if($('#process').val() == 'add' || $('#process').val() == 'edit') { // add / edit
               manage_entry();
            }
         }
      });
   }

   function manage_entry() {
      if($('#process').val() == 'add') { // New Row
         $row = $("#tbl_clone tbody tr").clone();
      } else if($('#process').val() == "edit") { // Existing Row
         $row = $('tr[id="'+$("#edit_id").val()+'"]');
      }
      
      $row.find('input.entry_id').val($('#entry_id').val());

      var sign = $('#sign').val();
      var accn = $('#accn').val();

      console.log(">>> SIGN >>> "+sign);

      $row.find('input.sign').val(sign);
      $row.find('input.accn').val(accn);
      $row.find('input.accn_desc').val($("#accn option:selected").text());
      
      var control_account = 0;
      var ca_field = false;      

      if(accn == "CA001") {
         ca_field = true;
         control_account = $('#customer').val();
         $row.find('input.iden').val($("#customer option:selected").text());
      } else if(accn == "CL001") {
         ca_field = true;
         control_account = $('#supplier').val();
         $row.find('input.iden').val($("#supplier option:selected").text());
      } else if(accn == "CA110") {
         ca_field = true;
         control_account = $('#foreign_bank').val();
         $row.find('input.iden').val($("#foreign_bank option:selected").text());
      }

      var er_field = false;
      if($('#currency').val() !== "" && $('#currency').val() !== "SGD") {
         er_field = true;
      }

      if(ca_field) {
         $('#tbl_items .ca_field').show();
         $row.find('td.ca_field').show();

         if(er_field) {
            $('#tbl_items .er_field').show();
            $row.find('td.er_field').show();
         }
      }

      $row.find('input.control_account').val(control_account);
      $row.find('input.exchange_rate').val($('#exchange_rate').val());
      $row.find('input.currency').val($('#currency').val());

      foreign_amount = $('#foreign_amount').val();
      local_amount = $('#local_amount').val();

      var gst_type, gst_category, net_amount, gst_amount, gst_rate;
      gst_type = $('#gst_type').val();
      if(gst_type == "I") {
         gst_category = $('#gst_input_category').val();
         net_amount = $('#purchase_amount').val();
         gst_amount = $('#gst_input_amount').val();
         gst_rate = $('#gst_input_rate').val();

         foreign_amount = gst_amount;
         local_amount = gst_amount;

      } else if(gst_type == "OR") {
         gst_category = $('#gst_output_category').val();
         net_amount = $('#sales_amount').val();
         gst_amount = $('#gst_output_amount').val();
         gst_rate = $('#gst_output_rate').val();

         foreign_amount = gst_amount;
         local_amount = gst_amount;
      
      } else if(gst_type == "S") {
         foreign_amount = $('#foreign_amount').val();
         local_amount = $('#foreign_amount').val();
      }

      $row.find('input.gst_type').val(gst_type);
      $row.find('input.gst_category').val(gst_category);
      $row.find('input.gst_rate').val(gst_rate);
      $row.find('input.net_amount').val(net_amount);
      $row.find('input.gst_amount').val(gst_amount);

      $row.find('input.dr_foreign_amount').val("");
      $row.find('input.dr_local_amount').val("");
      $row.find('input.cr_foreign_amount').val("");
      $row.find('input.cr_local_amount').val("");

      $row.find('.dr_famt_field, .dr_lamt_field').hide();
      $row.find('.cr_famt_field, .cr_lamt_field').hide();

      console.log(">>> CURRENCY >>> "+$('#currency').val());

      if(sign == "+") { // debit 
         $row.find('input.dr_foreign_amount').val(foreign_amount);
         $row.find('input.dr_local_amount').val(local_amount);
         
         if($('#currency').val() !== "" && $('#currency').val() !== "SGD") {
            $row.find('.dr_famt_field').show();
            $row.find('.dr_lamt_field').show();
            $row.find('.dr_famt_field span.f_curr').text($('#currency').val());
         } else {
            $row.find('.dr_lamt_field').show();
         }

      } else if(sign == "-") { // credit
         $row.find('input.cr_foreign_amount').val(foreign_amount);
         $row.find('input.cr_local_amount').val(local_amount);

         if($('#currency').val() !== "" && $('#currency').val() !== "SGD") {
            $row.find('.cr_famt_field').show();
            $row.find('.cr_lamt_field').show();
            $row.find('.cr_famt_field span.f_curr').text($('#currency').val());
         } else {
            $row.find('.cr_lamt_field').show();
         }
      }

      if($('#process').val() == "add") {
         // append new row to the table
         $('#tbl_items').append($row);
         sortTblRowsByID();
         $('#tbl_items').show();
      }

      process_total(false);

      $('#entryModal').modal('hide');
   }

   function sortTblRowsByID() {
      var row_number = 0;
      var DELIMITER;
      var parts;
      $("#tbl_items tbody tr").each(function () {
         $(this).find('input, select, button, textarea').each(function() {
            var id = $(this).attr('id') || null;

            if(id) {
               DELIMITER = "_";
               parts = id.split(DELIMITER);
               parts[parts.length - 1] = row_number;
               id = parts.join(DELIMITER);
               $(this).attr('id', id);
            }
         });

         $(this).attr('id', row_number);
         row_number = row_number + 1;
      });
   }

   function process_total(post) {
      var dr_total = 0;
      var cr_total = 0;
      var row_number = 0;

      $("#tbl_items tbody tr").each(function () {
         row_number = $(this).attr('id');
         if($('#sign_'+row_number).val() == "+") {
            dr_total += Number($('#dr_local_amount_'+row_number).val());
         } else if($('#sign_'+row_number).val() == "-") {
            cr_total += Number($('#cr_local_amount_'+row_number).val());
         }
      });

      $('.dr_total').html(dr_total.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
      $('.cr_total').html(cr_total.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
     
      if(post) {

         var diff_amount = dr_total.toFixed(2) - cr_total.toFixed(2);

         if(diff_amount == 0) {
            $.confirm({
               title: '<i class="fa fa-info"></i> Confirm POST to GL',
               content: 'Are you sure to Post?</strong>',
               buttons: {
                  yes: {
                     btnClass: 'btn-warning',
                     action: function() {
                        post();
                     }
                  },
                  no: {
                     btnClass: 'btn-dark',
                     action: function(){
                     }
                  },
               }
            });

         } else {
            $('#debit_total').html(dr_total.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            $('#credit_total').html(cr_total.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));

            var diff_amount = parseFloat(dr_total) - parseFloat(cr_total);
            if(diff_amount < 0) {
               diff_amount = (-1) * diff_amount;
            }
            $('#debit_credit_diff_total').html(diff_amount.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));

            $('#totalModal').modal('show');
            return false;
         }
               
      }
   }

   function post() {
      row_number = $("#tbl_items>tbody>tr:first").attr('id');
      $.post('/ez_entry/ajax/post_adjustment', {
         rowID: $('#entry_id_'+row_number).val()
      }, function (status) {
         if($.trim(status) == 'posted') {
            toastr.success("Post Success!");
            window.location.href = '/ez_entry/other_adjustment';
         } else {
            toastr.error("Post Error!");
         }
      });
   }
</script>
