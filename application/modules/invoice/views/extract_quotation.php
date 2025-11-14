<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Extract Quotation</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item"><a href="/invoice">Invoice</a></li>
               <li class="breadcrumb-item active">Extract</li>
            </ol>
         </div>
      </div>
   </div>
</div>

<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">

            <!-- this variable will be used in invoice.js file -->
            <?php if ($this->ion_auth->isGSTMerchant()) { ?>
               <input type="hidden" id="GST_registered_merchant" value="true" />
            <?php } else {?>
               <input type="hidden" id="GST_registered_merchant" value="false" />
            <?php } ?>

            <!-- customer code - hidden field --> 
            <input type="hidden" id="customer_code" value="<?php echo $customer_data->customer_code; ?>" />
            
            <!-- customer currency - hidden field --> 
            <input type="hidden" id="customer_currency" value="<?php echo $customer_currency; ?>" />            

            <!-- System currency - hidden field --> 
            <input type="hidden" id="system_currency" value="<?php echo $system_currency; ?>" />            

            <!-- Page - hidden field --> 
            <input type="hidden" id="page" value="extract" />

            <!-- form - starts -->
            <form autocomplete="off" id="form_" method="post">
               
               <!-- Qutation Id - hidden field -->
               <input 
                  type="hidden" 
                  name="quotation_id" value="<?php echo $quotation_id; ?>" />

               <!-- Invoice refernce number - hidden field -->
               <input
                  type='hidden'
                  name='invoice_ref_no' id="invoice_ref_no"
                  value="<?php echo $invoice_ref_no; ?>" />

               <div class="card card-default">
                  <div class="card-header">
                     <h5><?php echo $invoice_ref_no; ?></h5>
                     <a href="/invoice" class="btn btn-outline-dark btn-sm float-right">
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
                                 <select name="employee_id" id="employee_id" class="form-control" style="pointer-events: none; background: #f5f5f5" onclick="return false;" onkeydown="return false;">
                                    <?php echo $employee_options; ?>
                                 </select>
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="cross_reference" class="col-md-4 control-label">Cross Reference : </label>
                              <div class="col-md-8">
                                 <input type="hidden" name="quotation_ref_no" value="<?php echo $quotation_ref_no; ?>" />
                                 <span style="display: block; padding-top: 10px; color: dimgray; letter-spacing: 1px;"><?php echo $quotation_ref_no; ?></span>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-12" style="margin: 15px 0 30px">
                           <label for="header_notes" class="control-label">Header Notes</label>
                           <textarea id="header_notes" name="header_notes" class="form-control" placeholder="Optional!"><?php echo $header_notes; ?></textarea>
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
                $billing_details = $billing_data->stock_code.' : '.$billing_data->billing_description;

                $gst_options = $this->custom->createDropdownSelect('ct_gst', ['gst_code', 'gst_code', 'gst_description', 'gst_rate'], 'GST Category', [' : ', '(RATE : ', ')'], ['gst_type' => 'supply'], ['gst_code' => $value->gst_category]);

                $item_gst_rate = $this->custom->getSingleValue('ct_gst', 'gst_rate', ['gst_code' => $value->gst_category]);
                $service_without_uom = 'false';
                $readonly = '';
                // If item is Service and NO "UOM", then the fields Quantity, Unit Price and Discount are NOT NEEDED.
                if ($billing_data->billing_type == 'Service' && $billing_data->uom == '') {
                    $service_without_uom = 'true';
                    $readonly = 'readonly';
                    $value->quantity = '';
                    $value->unit_price = '';
                    $value->discount = '';
                }
                $readonly = 'readonly'; // All fields are readonly in extract quotation
                ?>

                                 <tr id="<?php echo $i; ?>">
                                    <td>
                                       <button type="button" id="btn_billing_select_<?php echo $i; ?>" class="btn btn-outline-secondary btn-sm btn_billing_select" disabled><i class="fa-regular fa-rectangle-list"></i> <?php echo substr($billing_details, 0, 40).'...'; ?> </button>
                                       <input type="hidden" id="billing_id_<?php echo $i; ?>" name="billing_id[]" class="billing_id" value="<?php echo $value->billing_id; ?>" />
                                       <input type="hidden" id="service_without_uom_<?php echo $i; ?>" value="<?php echo $service_without_uom; ?>" class="service_without_uom" />
                                       
                                       <button type="button" class="btn btn-outline-info btn-sm btn_add_details" style="margin-left: 10px" disabled><i class="fa-solid fa-plus"></i> Details</button>
                                       <textarea id="item_details_<?php echo $i; ?>" name="item_details[]" class="d-none item_details"><?php echo $value->details; ?></textarea>
                                       
                                       <div class="row">
                                          <div style="float: left; width: 130px; padding: 10px">
                                             <label for="quantity_<?php echo $i; ?>" class="control-label">Quantity</label>
                                             <input 
                                                type="number" 
                                                id="quantity_<?php echo $i; ?>" name="quantity[]" 
                                                value="<?php echo $value->quantity; ?>"
                                                class="form-control quantity" 
                                                onkeypress="if(this.value.length == 6) return false;" <?php echo $readonly; ?> />
                                          </div>
                                          <div style="float: left; width: 100px; padding: 10px">
                                             <label for="uom_<?php echo $i; ?>" class="control-label">UOM</label>
                                             <input 
                                                type="text" 
                                                id="uom_<?php echo $i; ?>" name="uom[]"
                                                value="<?php echo $billing_data->billing_uom; ?>"
                                                class="form-control uom" readonly />
                                          </div>

                                          <div style="float: left; width: 180px; padding: 10px">
                                             <label for="unit_price_<?php echo $i; ?>" class="control-label">Unit Price</label>
                                             <input 
                                                type="number" 
                                                id="unit_price_<?php echo $i; ?>" name="unit_price[]" 
                                                value="<?php echo $value->unit_price; ?>"
                                                class="form-control unit_price" readonly />
                                          </div>

                                          <div style="float: left; width: 130px; padding: 10px">
                                             <label for="discount_<?php echo $i; ?>" class="control-label">Discount</label>
                                             <input 
                                                type="number" 
                                                id="discount_<?php echo $i; ?>" name="discount[]" 
                                                value="<?php echo $value->discount; ?>"
                                                min="0" max="99"
                                                class="form-control discount" <?php echo $readonly; ?> />
                                          </div>

                                          <div style="float: left; width: 200px; padding: 10px">
                                             <label for="item_amount_<?php echo $i; ?>" class="control-label float-right" style="padding-right: 16px;">Amount</label>
                                             <input 
                                                type="number" 
                                                id="item_amount_<?php echo $i; ?>" name="item_amount[]" 
                                                value="<?php echo $value->amount; ?>"
                                                class="form-control item_amount" readonly />
                                          </div>

                                          <div style="float: left; width: 400px; padding: 10px">
                                             <label for="gst_category_<?php echo $i; ?>" class="control-label">GST Category</label>
                                             <select id="gst_category_<?php echo $i; ?>" name="gst_category[]" class="form-control gst_category" style="pointer-events: none; background: #f5f5f5" onclick="return false;" onkeydown="return false;">
                                                <?php echo $gst_options; ?>
                                             </select>

                                             <input type="hidden" id="item_gst_rate_<?php echo $i; ?>" name="item_gst_rate[]" value="<?php echo $item_gst_rate; ?>" class="item_gst_rate" />
                                          </div>

                                          <div style="float: left; width: 200px; padding: 10px">
                                             <label for="item_gst_amount_<?php echo $i; ?>" class="control-label float-right" style="padding-right: 16px;">GST Amount</label>
                                             <input 
                                                type="number" 
                                                id="item_gst_amount_<?php echo $i; ?>" name="item_gst_amount[]" 
                                                value="<?php echo $value->gst_amount; ?>"
                                                class="form-control item_gst_amount" readonly />
                                          </div>

                                          <div style="float: left; width: 50px; padding: 10px; margin-top: 34px;">
                                             <button type="button" class="btn btn-outline-danger btn-sm btn_delete_row float-right" disabled><i class="fa fa-trash"></i></button>
                                          </div>

                                       </div>

                                    </td>
                                 </tr>
                                 <?php ++$i;
            } ?>
                              </tbody>
                              <tfoot>
                                 <tr>
                                    <td style="border-top: 2px solid dimgray; padding: .2rem 1rem">
                                       <div class="row">
                                          <div style="float: left; width: 230px; padding: 10px">
                                             <a href="#" class="btn_add_item btn btn-success btn-sm d-none"><i class="fa-solid fa-plus"></i> Add Item</a>
                                          </div>

                                          <div style="float: left; width: 310px; padding: 10px;">
                                             <label class="control-label" style="float: right;">Total Amount</label>
                                          </div>

                                          <div style="float: left; width: 200px; text-align: right; padding: 17px 20px 10px 20px;" class="dsply_sub_total">
                                             <?php echo number_format($mt_data->sub_total, 2); ?>
                                          </div>

                                          <div style="float: left; width: 400px; padding: 10px;">
                                             <label class="control-label" style="float: right;">Total GST Amount</label>
                                          </div>

                                          <div style="float: left; width: 200px; text-align: right; padding: 17px 20px 10px 20px;" class="dsply_gst_total">
                                             <?php echo $mt_data->gst_total; ?>
                                          </div>
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
                                    <td>Subtotal</td>
                                    <td class="dsply_sub_total"><?php echo number_format($mt_data->sub_total, 2); ?></td>
                                 </tr>

                                 <tr>
                                    <td>
                                       Lump Sum Discount <br />
                                       <a class="d-none" style="color: #007bff; font-size: .9rem; text-decoration: underline; letter-spacing: 1px; cursor: pointer" data-toggle="modal" data-target="#lsdModal">Apply</a>
                                    </td>
                                    <td style="vertical-align: bottom">
                                       <?php if ($mt_data->lsd_code == 'P') { ?>
                                          <span class="dsply_lsd_percentage float-left" style="color: blue; font-size: 0.9rem;"><?php echo number_format($mt_data->lsd_percentage, 2); ?>%</span>
                                       <?php } ?>
                                       <span class="dsply_lsd_amount float-right">(<?php echo number_format($mt_data->lsd_value, 2); ?>)</span>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td style="width: 250px;">Net after Lump Sum Discount</td>
                                    <td class="dsply_net_after_lsd"><?php echo number_format($mt_data->net_after_lsd, 2); ?></td>
                                 </tr>
                                 
                                 <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                                    <tr>
                                       <td>GST Total</td>
                                       <td>+<span class="dsply_gst_total"><?php echo number_format($mt_data->gst_total, 2); ?></span></td>
                                    </tr>
                                 <?php } ?>

                                 <tr>
                                    <td>Net Total (Incl Tax)</td>
                                    <td class="dsply_net_incl_gst"><?php echo number_format($mt_data->net_total, 2); ?></td>
                                 </tr>

                                 <?php if ($customer_currency !== $system_currency) {
                                     if ($this->ion_auth->isGSTMerchant()) { ?>
                                    <tr class="foreign_values">
                                       <td>GST Total <?php echo '('.$system_currency.')'; ?></td>
                                       <td class="dsply_f_gst_total"><?php echo number_format($mt_data->f_gst_total, 2); ?></td>
                                    </tr>
                                    <?php }?>
                                    <tr class="foreign_values">
                                       <td>Net Total (Incl Tax) <?php echo '('.$system_currency.')'; ?></td>
                                       <td class="dsply_f_net_incl_gst"><?php echo number_format($mt_data->f_net_total, 2); ?></td>
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
                           <input type='hidden' id="net_total" name="net_total" value="<?php echo $mt_data->net_total; ?>" />
                           <input type='hidden' id="f_gst_total" name="f_gst_total" value="<?php echo $mt_data->f_gst_total; ?>" />
                           <input type='hidden' id="f_net_total" name="f_net_total" value="<?php echo $mt_data->f_net_total; ?>" />

                        </div>
                     </div>                 
                     

                     <div class="row">
                        <div class="col-md-12" style="margin: 15px 0 30px">
                           <label for="payment_terms" class="control-label">Payment Terms</label>
                           <textarea id="payment_terms" name="payment_terms" class="form-control" placeholder="Optional!"></textarea>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-12" style="margin: 15px 0 30px">
                           <label for="notes" class="control-label">Order Notes</label>
                           <textarea id="order_notes" name="order_notes" class="form-control" placeholder="Optional!"></textarea>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-12" style="margin: 15px 0 30px">
                           <label for="footer_notes" class="control-label">Footer Notes</label>
                           <textarea id="footer_notes" name="footer_notes" class="form-control" placeholder="Optional!"><?php echo $footer_notes; ?></textarea>
                        </div>
                     </div>

                  </div>
                  <div class="card-footer">
                     <a href="/invoice" class="btn btn-info btn-sm">Cancel</a>
                     <button type="button" id="btn_print" class="btn btn-dark btn-sm">Print</button>
                     <button type="button" id="btn_submit" class="btn btn-warning btn-sm float-right">Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<style>
   .card-header span {
      padding-top: 5px;
      color: dimgray;
      font-size: 1.2rem;
      letter-spacing: 1px;
   }
   table {
      /*cursor: pointer;*/
   }
   tr.odd {
      border-bottom: 2px solid #ebebeb;
   }

   .select2 {
      width: 100% !important;
   }

   textarea {
      -webkit-box-sizing: border-box;
      -moz-box-sizing: border-box;
      box-sizing: border-box;

      max-width: 100% !important;
      height: 75px !important;
   }
   .control-label {
      display: flex;
   }
   .dsply_customer_details {
      padding: 10px 10px 10px 20px;
      color: dimgray;
      border-radius: 5px;
   }
   #tbl_items td {
      border-bottom: 1px solid gainsboro !important;
      padding: 1.2rem;
   }
   #tbl_total td {
      text-align: right;
      border: none;
      border-bottom: 1px solid gainsboro;
   }
   #tbl_total td:first-child { 
      border-bottom: 1px solid #fff;
      font-weight: 500;
      color: black;
      font-style: italic;
   }
   .item_amount, .item_gst_amount {
      text-align: right;
   }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
// document starts
$(function() {
   // print invoice
   $("#btn_print").on('click', function (e) {

      $.confirm({
         title: '<i class="fa fa-info"></i> Confirm Print',
         content: 'Are you sure to print this Quotation ?',
         buttons: {
            yes: {
               btnClass: 'btn-warning',
               action: function(){
                  $("#form_").attr("action", '/invoice/print_stage_1');
                  $("#form_").attr("target", "_blank");
                  $('#form_').submit();
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

   // submit invoice
   $("#btn_submit").on('click', function (e) {
      
      $.confirm({
         title: '<i class="fa fa-info"></i> Confirm Submit',
         content: 'Are you sure to Submit this Quotation ?',
         buttons: {
            yes: {
               btnClass: 'btn-warning',
               action: function(){
                  $("#form_").attr("action", '/invoice/create');
                  $("#form_").attr("target", "_self");
                  $('#form_').submit();
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

}); // document ends  
</script>