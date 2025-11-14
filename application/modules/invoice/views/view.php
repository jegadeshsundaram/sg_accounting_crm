<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Invoice</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item"><a href="/invoice">Invoice</a></li>
               <li class="breadcrumb-item active">View</li>
            </ol>
         </div>
      </div>
   </div>
</div>

<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <div class="card card-default">
               <div class="card-header">
                  <h5><?php echo $mt_data->invoice_ref_no; ?></h5>
                  <a href="/invoice/listing" class="btn btn-outline-dark btn-sm float-right">
                     <i class="fa-solid fa-angles-left" aria-hidden="true"></i> Back
                  </a>
               </div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-md-7">
                        <strong>To,</strong><br />
                        <strong><?php echo $customer->name; ?> (<?php echo $customer->code; ?>)</strong><br />
                        <?php echo $customer_address; ?><br />
                     </div>
                     <div class="col-md-5" style="margin-top: 20px">
                        <div class="fl-right">
                           <strong>Date:</strong> <?php echo date('d-m-Y', strtotime($mt_data->modified_on)); ?><br />

                           <strong>Staff-in-charge:</strong>
                           <?php echo $employee_data['name']; ?> (<?php echo $employee_data['code']; ?>)<br />                     

                           <?php if ($employee_data['department'] !== null && $employee_data['department'] !== '') { ?>
                              <u>Department:</u> <?php echo $employee_data['department']; ?>
                           <?php } ?> <br />

                           <?php if ($employee_data['email'] !== '') { ?>
                              <u>Email:</u> <?php echo $employee_data['email']; ?>
                           <?php } ?>

                           <?php if ($mt_data->quotation_ref_no !== null) { ?>
                              <br /><br /><u>Cross Reference:</u> <?php echo $mt_data->quotation_ref_no; ?>
                           <?php } ?>
                        </div>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-12" style="margin: 15px 0">
                        <?php echo $mt_data->header_notes; ?>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-12 table-responsive">
                        <table class="table table-hover" style="min-width: 1200px; width: 100%;">
                           <thead>
                              <tr>
                                 <th style="width: 15px; text-align: center">#</th>
                                 <th>Item</th>
                                 <th style="width: 100px">Quantity</th>
                                 <th style="width: 140px; text-align: right"><span style="display: block; color: red;">(<?php echo $customer_currency; ?>)</span>Unit Price</th>
                                 <th style="width: 100px; text-align: right">Disc (%)</th>
                                 <th style="width: 150px; text-align: right"><span style="display: block; color: red;">(<?php echo $customer_currency; ?>)</span>Amount</th>
                                 <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                                 <th style="min-width: 200px">GST Code & Desc</th>
                                 <th style="width: 140px; text-align: right">GST Amount</th>
                                 <?php } ?>
                              </tr>
                           </thead>
                           <tbody>
                              <?php
                              $i = 1;
                  foreach ($pr_data as $value) {
                      $billing_data = $this->custom->getSingleRow('master_billing', ['billing_id' => $value->billing_id]);
                      $gst_description = $this->custom->getSingleValue('ct_gst', 'gst_description', ['gst_code' => $value->gst_category]); ?>
                              <tr>
                                 <!-- 1. sno -->
                                 <td style="text-align: center"><?php echo $i; ?></td>

                                 <!-- 2. description -->
                                 <td>
                                    <?php echo $billing_data->stock_code.' : '.$billing_data->billing_description; ?>
                                    <?php if ($value->details != '') { ?>
                                       <span style="white-space: normal; font-style: oblique; color: dimgray"><?php echo $value->details; ?></span>
                                    <?php } ?>
                                 </td>

                                 <?php if ($billing_data->billing_uom != '' && $billing_data->billing_uom != null) { ?>
                                    <!-- 3. Quantity & UOM -->
                                    <td>
                                       <?php echo $value->quantity.' '.$billing_data->billing_uom; ?>
                                    </td>

                                    <!-- 5. Unit Price -->
                                    <td style="text-align: right">
                                       <?php echo number_format($value->unit_price, 2); ?>
                                    </td>

                                    <!-- 6. Discount -->
                                    <td style="text-align: right">
                                       <?php echo $value->discount; ?>
                                    </td>

                                 <?php } else { ?>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                 <?php } ?>

                                 <!-- 7. Product Total -->
                                 <td style="text-align: right">
                                    <?php echo number_format($value->amount, 2); ?>
                                 </td>

                                 <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                                    <!-- 8. gst code & description -->
                                    <td valign="top">
                                    <?php
                         if ($value->gst_category != '') {
                             echo $value->gst_category.' : '.$gst_description;
                         } else {
                             echo 'N/A';
                         }
                                     ?>
                                    </td>

                                    <!-- 9. gst - derived amount -->
                                    <td valign="top" style="text-align: right">
                                    <?php echo number_format($value->gst_amount, 2); ?>
                                    </td>
                                 <?php } ?>

                              </tr>
                              <?php
                              ++$i;
                  }
                  ?>
                           </tbody>
                        </table>
                     </div>
                  </div>

                  <div class="row">
                     <div class="col-md-12 table-responsive">
                        <table id="tbl_total" class="table fl-right" style="width: 450px">
                           <tbody>
                              <tr>
                                 <td style="width: 290px">Subtotal</td>
                                 <td colspan="2"><?php echo number_format($mt_data->sub_total, 2); ?></td>
                              </tr>

                              <tr>
                                 <td>Lump Sum Discount</td>
                                 <td>
                                    <?php if ($mt_data->lsd_code == 'P') { ?>
                                       <?php echo number_format($mt_data->lsd_percentage, 2); ?>%
                                    <?php } ?>
                                 </td>
                                 <td>(<?php echo number_format($mt_data->lsd_value, 2); ?>)</td>
                              </tr>

                              <tr>
                                 <td style="width: 215px;">Net after Lump Discount</td>
                                 <td colspan="2">
                                    <?php echo number_format($mt_data->net_after_lsd, 2); ?>
                                 </td>
                              </tr>

                              <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                              <tr>
                                 <td>GST Total <strong>(<?php echo $customer_currency; ?>)</strong></td>
                                 <td colspan="2">+<?php echo number_format($mt_data->f_gst_total, 2); ?></td>
                              </tr>
                              <?php } ?>

                              <tr>
                                 <td>Net Total <?php echo $this->ion_auth->isGSTMerchant() ? '(Incl Tax)' : ''; ?> <strong>(<?php echo $customer_currency; ?>)</strong></td>
                                 <td colspan="2"><?php echo number_format($mt_data->f_net_total, 2); ?></td>
                              </tr>

                              <?php if ($customer_currency !== $system_currency) {?>
                                 <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                                 <tr style="border-top: 2px solid gainsboro;">
                                    <td>GST Total @ <u><?php echo $customer_currency_rate; ?></u> <strong>(<?php echo $system_currency; ?>)</strong></td>
                                    <td colspan="2"><?php echo number_format($mt_data->gst_total, 2); ?></td>
                                 </tr>
                                 <?php }?>

                                 <tr>
                                    <td>Net Total <?php echo $this->ion_auth->isGSTMerchant() ? '(Incl Tax)' : ''; ?> @ <u><?php echo $customer_currency_rate; ?></u> <strong>(<?php echo $system_currency; ?>)</strong></td>
                                    <td colspan="2"><?php echo number_format($mt_data->net_total, 2); ?></td>
                                 </tr>

                              <?php } ?>

                           </tbody>
                        </table>
                     </div>
                  </div>
                  
                  <?php if ($mt_data->payment_terms != '') { ?>
		            <div class="row">
                     <div class="col-md-12" style="margin: 15px 0">
                        <strong>Payment Terms : </strong><?php echo $mt_data->payment_terms; ?>
                     </div>
                  </div>
                  <?php } ?>

                  <?php if ($mt_data->order_notes != '') { ?>
		            <div class="row">
                     <div class="col-md-12" style="margin: 15px 0">
                        <strong>Order Notes : </strong><?php echo $mt_data->order_notes; ?>
                     </div>
                  </div>
                  <?php } ?>
                  
                  <?php if ($mt_data->footer_notes != '') { ?>
                  <div class="row">
                     <div class="col-md-12" style="margin: 15px 0">
                        <?php echo $mt_data->footer_notes; ?>
                     </div>
                  </div>
                  <?php } ?>
               </div>
               <div class="card-footer">
                  <a href="/invoice/listing" class="btn btn-info btn-sm">Back</a>
                  <?php if ($mt_data->status == 'CONFIRMED') { ?>
                     <a href="/invoice/manage/edit/<?php echo $invoice_id; ?>" class="btn btn-warning btn-sm float-right">Edit Invoice</a>
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<style> 
   #tbl_total td:first-child {
      border-bottom: 1px solid #fff;
      font-weight: 500;
      color: black;
      font-style: italic;
   }
   #tbl_total td {
      text-align: right;
      border: none;
      border-bottom-width: medium;
      border-bottom-style: none;
      border-bottom-color: currentcolor;
      border-bottom: 1px solid gainsboro;
   }
</style>
