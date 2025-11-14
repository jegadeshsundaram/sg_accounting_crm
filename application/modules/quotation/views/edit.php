<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Quotation</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item"><a href="/quotation">Quotation</a></li>
               <li class="breadcrumb-item active">Edit</li>
            </ol>
         </div>
      </div>
   </div>
</div>

<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">

            <!-- this variable will be used for gst calculations -->
            <?php if ($this->ion_auth->isGSTMerchant()) { ?>
               <input type="hidden" id="GST_registered_merchant" value="true" />
            <?php } else {?>
               <input type="hidden" id="GST_registered_merchant" value="false" />
            <?php } ?>

            <!-- customer code - hidden field --> 
            <input type="hidden" id="customer_code" value="<?php echo $customer->code; ?>" />
            
            <!-- customer currency - hidden field --> 
            <input type="hidden" id="customer_currency" value="<?php echo $customer_currency; ?>" />

            <!-- System currency - hidden field --> 
            <input type="hidden" id="system_currency" value="<?php echo $system_currency; ?>" />

            <!-- Page - hidden field --> 
            <input type="hidden" id="page" value="edit" />
            <input type="hidden" id="std_gst_rate" value="<?php echo $std_gst_rate; ?>" />

            <input type="hidden" id="process" name="process" />
            <input type="hidden" id="edit_id" name="edit_id" />

            <!-- form - starts -->
            <form autocomplete="off" id="form_" method="post">
               <!-- Id - hidden field -->
               <input type="hidden" name="quotation_id" value="<?php echo $quotation_id; ?>" />

               <!-- refernce number - hidden field -->
               <input
                  type='hidden'
                  name='quotation_ref_no' id="quotation_ref_no"
                  value="<?php echo $mt_data->quotation_ref_no; ?>" />

               <div class="card card-default">
                  <div class="card-header">
                     <h5><?php echo $mt_data->quotation_ref_no; ?></h5>
                     <a href="/quotation/listing" class="btn btn-outline-dark btn-sm float-right">
                        <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                     </a>
                  </div>
                  <div class="card-body">

                     <div class="row">
                        <div class="col-md-5">
                           <!-- Customer dropdown list -->
                           <label for="customer_id" class="control-label f-bold">To,</label>
                           <select name="customer_id" id="customer_id" class="form-control" style="pointer-events: none; background: #f5f5f5" onclick="return false;" onkeydown="return false;">
                              <?php echo $customer_options; ?>
                           </select>

                           <!-- Display customer details upon selection -->
                           <div class="dsply_customer_details">
                              <?php echo $customer_address; ?>
                           </div>
                        </div>

                        <div class="col-md-2"></div>

                        <div class="col-md-5" style="margin-top: 20px">
                           <div class="form-group row">
                              <label for="created_on" class="col-md-4 control-label">Date : </label>
                              <div class="col-md-8">
                                 <input 
                                    type="text"
                                    id="created_on" name="created_on"
                                    class="form-control dp_full_date w-120"
                                    placeholder="dd-mm-yyyy"
                                    value="<?php echo date('d-m-Y', strtotime($mt_data->modified_on)); ?>" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="employee_id" class="col-md-4 control-label">Staff-in-charge : </label>
                              <div class="col-md-8">
                                 <span id="employee_display"></span>
                                 <select name="employee_id" id="employee_id" class="form-control">
                                    <?php echo $employee_options; ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-12" style="margin: 15px 0 30px">
                           <label for="header_notes" class="control-label">Header Notes</label>
                           <textarea id="header_notes" name="header_notes" class="form-control" placeholder="Optional!"><?php echo $mt_data->header_notes; ?></textarea>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-12 table-responsive">
                           <table id="tbl_items" class="table table-borderless" style="min-width: 1450px; width: 100%;">
                              <tbody>
                              <?php
                              $i = 0;
            foreach ($pr_data as $value) {
                $billing_data = $this->custom->getSingleRow('master_billing', ['billing_id' => $value->billing_id]);
                $billing_desc = $billing_data->stock_code.' : '.$billing_data->billing_description;

                $gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $value->gst_category]);
                $gst_desc = $this->custom->getSingleValue('ct_gst', 'gst_description', ['gst_code' => $value->gst_category]);

                $service_without_uom = 'false';
                // If item is Service and NO "UOM", then the fields Quantity, Unit Price and Discount are NOT NEEDED.
                if ($billing_data->billing_type == 'Service' && $billing_data->uom == '') {
                    $service_without_uom = 'true';
                    $value->quantity = '';
                    $value->unit_price = '';
                    $value->discount = '';
                }
                ?>
                                 <tr id="<?php echo $i; ?>">
                                    <td>
                                       <!-- billing id and description -->
                                       <input type="hidden" id="billing_id_<?php echo $i; ?>" name="billing_id[]" class="billing_id" value="<?php echo $value->billing_id; ?>" />
                                       <input type="text" id="billing_desc_<?php echo $i; ?>" class="billing_desc" value="<?php echo substr($billing_desc, 0, 60).'...'; ?>" readonly />
                                       
                                       
                                       <!-- additional description -->
                                       <button type="button" class="btn btn-outline-info btn-sm btn_add_details" style="margin-left: 10px"><i class="fa-solid fa-plus"></i> Details</button>
                                       <textarea id="item_details_<?php echo $i; ?>" name="item_details[]" class="d-none item_details"><?php echo $value->details; ?></textarea>
                                       
                                       <div class="row">

                                          <div style="float: left; width: 125px; padding: 10px; margin-top: 14px;">
                                             <a class="dt-btn dt_edit"><i class="fa fa-pencil"></i><span>Edit</span></a>
                                             <a class="dt-btn dt_delete"><i class="fa fa-trash"></i><span>Del</span></a>
                                          </div>

                                          <div style="float: left; width: 120px; padding: 10px">
                                             <label for="quantity_<?php echo $i; ?>" class="control-label">Quantity</label>
                                             <input 
                                                type="number" 
                                                id="quantity_<?php echo $i; ?>" name="quantity[]" 
                                                value="<?php echo $value->quantity; ?>"
                                                class="form-control quantity" readonly />
                                          </div>

                                          <div style="float: left; width: 100px; padding: 10px">
                                             <label for="uom_<?php echo $i; ?>" class="control-label">UOM</label>
                                             <input 
                                                type="text" 
                                                id="uom_<?php echo $i; ?>" name="uom[]"
                                                value="<?php echo $billing_data->billing_uom; ?>"
                                                class="form-control uom" readonly />
                                          </div>

                                          <div style="float: left; width: 160px; padding: 10px">
                                             <label for="unit_price_<?php echo $i; ?>" class="control-label">Unit Price</label>
                                             <input 
                                                type="number" 
                                                id="unit_price_<?php echo $i; ?>" name="unit_price[]" 
                                                value="<?php echo $value->unit_price; ?>"
                                                class="form-control unit_price" readonly />
                                          </div>

                                          <div style="float: left; width: 85px; padding: 10px">
                                             <label for="discount_<?php echo $i; ?>" class="control-label">Discount</label>
                                             <input 
                                                type="number" 
                                                id="discount_<?php echo $i; ?>" name="discount[]" 
                                                value="<?php echo $value->discount; ?>"
                                                class="form-control discount" readonly />
                                          </div>

                                          <div style="float: left; width: 200px; padding: 10px">
                                             <label for="amount_<?php echo $i; ?>" class="control-label float-right" style="padding-right: 16px;">Amount</label>
                                             <input 
                                                type="number" 
                                                id="amount_<?php echo $i; ?>" name="amount[]" 
                                                value="<?php echo $value->amount; ?>"
                                                class="form-control txt-right amount" readonly />
                                          </div>

                                          <!-- ONLY GST User can access these fields -->
                                          <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                                             <div style="float: left; width: 400px; padding: 10px">
                                                <label for="gst_desc_<?php echo $i; ?>" class="control-label">GST Category</label>
                                                <input type="text" id="gst_desc_<?php echo $i; ?>" class="form-control gst_desc" value="<?php echo $value->gst_category.' : '.$gst_desc; ?>" readonly />
                                                
                                                <input type="hidden" id="gst_code_<?php echo $i; ?>" name="gst_code[]" class="gst_code" value="<?php echo $value->gst_category; ?>" />
                                                <input type="hidden" id="gst_rate_<?php echo $i; ?>" name="gst_rate[]" class="gst_rate" value="<?php echo $gst_rate; ?>" />
                                             </div>

                                             <div style="float: left; width: 200px; padding: 10px">
                                                <label for="gst_amount_<?php echo $i; ?>" class="control-label float-right" style="padding-right: 16px;">GST Amount</label>
                                                <input 
                                                   type="number" 
                                                   id="gst_amount_<?php echo $i; ?>" name="gst_amount[]" 
                                                   value="<?php echo $value->gst_amount; ?>"
                                                   class="form-control txt-right gst_amount" readonly />
                                             </div>
                                          <?php } ?>

                                       </div>

                                    </td>
                                 </tr>
                                 <?php ++$i;
            } ?>
                              </tbody>
                              <tfoot>
                                 <tr>
                                    <td style="border-top: 2px solid dimgray; padding: .2rem 0rem">
                                       <div class="row">
                                          <div style="float: left; width: 262px; padding: 10px">
                                             <a href="#" class="btn_add_item btn btn-success btn-sm"><i class="fa-solid fa-plus"></i> Add Item</a>
                                          </div>

                                          <div style="float: left; width: 325px; padding: 10px;" class="ft">
                                             <label class="control-label" style="float: right;">Total Amount</label>
                                          </div>

                                          <div style="float: left; width: 205px; text-align: right; padding: 17px 20px 10px 20px;" class="dsply_sub_total ft">
                                             <?php echo number_format($mt_data->sub_total, 2); ?>
                                          </div>

                                          <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                                          <div style="float: left; width: 390px; padding: 10px;" class="ft">
                                             <label class="control-label" style="float: right;">Total GST Amount</label>
                                          </div>

                                          <div style="float: left; width: 210px; text-align: right; padding: 17px 20px 10px 20px;" class="dsply_f_gst_total ft">
                                             <?php echo number_format($mt_data->f_gst_total, 2); ?>
                                          </div>
                                          <?php } ?>
                                       </div>
                                    </td>
                                 </tr>
                              </tfoot>
                           </table>
                           
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-12 table-responsive">
                           <table id="tbl_total" class="table fl-right" style="width: 450px">
                              <tbody>
                                 <tr>
                                    <td style="width: 290px;">Subtotal</td>
                                    <td class="dsply_sub_total"><?php echo number_format($mt_data->sub_total, 2); ?></td>
                                 </tr>

                                 <tr>
                                    <td>
                                       Lump Sum Discount <br /> 
                                       <a style="color: #007bff; font-size: .9rem; text-decoration: underline; letter-spacing: 1px; cursor: pointer" data-toggle="modal" data-target="#lsdModal">Apply</a>
                                    </td>
                                    <td style="vertical-align: bottom">
                                       <?php if ($mt_data->lsd_code == 'P') { ?>
                                          <span class="dsply_lsd_percentage float-left" style="color: blue; font-size: 0.9rem;"><?php echo number_format($mt_data->lsd_percentage, 2); ?>%</span>
                                       <?php } ?>
                                       <span class="dsply_lsd_amount float-right">(<?php echo number_format($mt_data->lsd_value, 2); ?>)</span>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>Net after Lump Sum Discount</td>
                                    <td class="dsply_net_after_lsd"><?php echo number_format($mt_data->net_after_lsd, 2); ?></td>
                                 </tr>

                                 <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                                 <tr>
                                    <td>GST Total (<span class="dsply_customer_currency"><?php echo $customer_currency; ?></span>)</td>
                                    <td>+<span class="dsply_f_gst_total"><?php echo number_format($mt_data->f_gst_total, 2); ?></span></td>
                                 </tr>
                                 <?php } ?>

                                 <tr>
                                    <td>Net Total <?php echo $this->ion_auth->isGSTMerchant() ? '(Incl Tax)' : ''; ?> (<span class="dsply_customer_currency"><?php echo $customer_currency; ?></span>)</td>
                                    <td class="dsply_f_net_incl_gst"><?php echo number_format($mt_data->f_net_total, 2); ?></td>
                                 </tr>

                                 <?php if ($customer_currency !== $system_currency) {
                                     if ($this->ion_auth->isGSTMerchant()) { ?>
                                    <tr class="local_values" style="border-top: 2px solid gainsboro;">
                                       <td>GST Total @ <u><span class="dsply_customer_currency_rate"><?php echo $customer_currency_rate; ?></span></u> (<span><?php echo $system_currency; ?></span>)</td>
                                       <td class="dsply_gst_total"><?php echo number_format($mt_data->gst_total, 2); ?></td>
                                    </tr>
                                    <?php } ?>

                                    <tr class="local_values">
                                       <td>Net Total <?php echo $this->ion_auth->isGSTMerchant() ? '(Incl Tax)' : ''; ?> @ <u><span class="dsply_customer_currency_rate"><?php echo $customer_currency_rate; ?></span></u> (<span><?php echo $system_currency; ?></span>)</td>
                                       <td class="dsply_net_incl_gst"><?php echo number_format($mt_data->net_total, 2); ?></td>
                                    </tr>
                                 <?php } ?>

                              </tbody>
                           </table>

                           <!-- Hidden fields -->
                           <input type="hidden" id="customer_currency_rate" name="customer_currency_rate" value="<?php echo $customer_currency_rate; ?>" />

                           <input type='hidden' id="sub_total" name="sub_total" value="<?php echo $mt_data->sub_total; ?>" />
                           
                           <input type='hidden' id="lsd_code" name="lsd_code" value="<?php echo $mt_data->lsd_code; ?>" />
                           <input type='hidden' name="lsd_percentage" value="<?php echo $mt_data->lsd_percentage; ?>" /> <!-- id used in other place -->
                           <input type='hidden' name="lsd_value" value="<?php echo $mt_data->lsd_value; ?>" /> <!-- id used in other place -->
                           <input type='hidden' id="net_after_lsd" name="net_after_lsd" value="<?php echo $mt_data->net_after_lsd; ?>" />

                           <input type='hidden' id="gst_total" name="gst_total" value="<?php echo $mt_data->gst_total; ?>" />
                           <input type='hidden' id="f_gst_total" name="f_gst_total" value="<?php echo $mt_data->f_gst_total; ?>" />

                           <input type='hidden' id="net_total" name="net_total" value="<?php echo $mt_data->net_total; ?>" />
                           <input type='hidden' id="f_net_total" name="f_net_total" value="<?php echo $mt_data->f_net_total; ?>" />

                        </div>
                     </div>

                     <div class="row notes">
                        <div class="col-md-12" style="margin: 15px 0 30px">
                           <label for="footer_notes" class="control-label">Footer Notes</label>
                           <textarea id="footer_notes" name="footer_notes" class="form-control" placeholder="Optional!"><?php echo $mt_data->footer_notes; ?></textarea>
                        </div>
                     </div>

                  </div>
                  <div class="card-footer btns">
                     <a href="/quotation/listing" class="btn btn-info btn-sm">EXIT</a>
                     <button type="button" id="btn_print" class="btn btn-dark btn-sm">PRINT PDF</button>
                     <button type="button" id="btn_submit" class="btn btn-warning btn-sm float-right">SUBMIT</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Lump Sum Discount Modal - starts -->
<div id="lsdModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <h4 style="margin: 0;">Lump Sum Discount</h4>
               <span style="margin: 0; font-size: 0.7rem;"><strong>Note: </strong>Lump Discount will be applied to all items in the quotation.</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute;top: 20px;right: 20px;" data-toggle="modal" data-target="#lsdModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body">
               <div class="row" style="margin-bottom: 5px">
                  <label for="lsd_percentage" class="control-label col-4 float-right" style="display: block">In Percentage</label>
                  <div class="col-8">
                     <div class="input-group mb-3 w-150">
                        <div class="input-group-prepend">
                           <span class="input-group-text">%</span>
                        </div>
                        <input 
                           type="number" 
                           id="lsd_percentage" class="form-control" 
                           value="<?php echo $mt_data->lsd_code == 'P' ? $mt_data->lsd_percentage : ''; ?>" />
                     </div>
                  </div>
               </div>
               <div class="row" style="margin-bottom: 15px">
                  <div class="col-12" style="text-align: center; font-size: 1.5rem; color: gray; font-style: italic;">
                     -- OR --
                  </div>
               </div>
               <div class="row" style="margin-bottom: 15px">
                  <label for="lsd_value" class="control-label col-4 float-right" style="display: block">In Value</label>
                  <div class="col-8">
                     <div class="input-group mb-3 w-200">
                        <div class="input-group-prepend">
                           <span class="input-group-text">$</span>
                        </div>
                        <input 
                            type="number" 
                            id="lsd_value" class="form-control" 
                            value="<?php echo $mt_data->lsd_code == 'V' ? $mt_data->lsd_value : ''; ?>" />
                     </div>
                  </div>
               </div>
               
            </div>
            <div class="card-footer">
               <button type="button" id="btn-lsd-modal-cancel" class="btn btn-info btn-sm">Cancel</button>
               <button type="button" id="btn-lsd-modal-apply" class="btn btn-danger btn-sm float-right">Apply</button>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- Lump Sum Discount Modal - ends -->

<!--Modals -->
<?php require_once 'modal.php'; ?>
<?php require_once APPPATH.'/modules/includes/modal/customer.php'; ?>
<?php require_once APPPATH.'/modules/includes/modal/employee.php'; ?>
<?php require_once APPPATH.'/modules/includes/modal/department.php'; ?>
<?php require_once APPPATH.'/modules/includes/modal/billing.php'; ?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.css" />
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script type="text/javascript" src="/application/modules/quotation/js/process.js"></script>

<script src="/assets/js/datatable.js"></script>

<script src="/assets/js/modal/customer.js"></script>
<script src="/assets/js/modal/employee.js"></script>
<script src="/assets/js/modal/department.js"></script>
<script src="/assets/js/modal/billing.js"></script>
