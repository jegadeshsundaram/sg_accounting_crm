<!DOCTYPE html>
<html>
<head>
   <style type="text/css">
      body {
         font-size: 1.3rem;
         font-family: sans-serif;
      }
      .tbl-items { border-collapse: collapse;}
      .tbl-items th { border-bottom: 2px solid dimgray; padding: 15px; text-align: left; text-transform: uppercase; font-variant-caps: titling-caps; font-weight: bold;}
      .tbl-items td {
         border-top: 1px solid gainsboro;
         padding: 15px; text-align: left;
      }      
      
      .tbl-total td {
         padding: 10px 15px;
         border-bottom: 1px solid #f8f8f8;
         text-align: right;
      }
      .tbl-total .first {
         font-weight: normal;
         border-bottom: none;
         background: #fff;
      }
      .tbl-total .second {
         background: #e6e6e6;
      }
      .logo-border {
         border-bottom: 3px solid #ccc;
      }
   </style>
</head>
<body>
   <?php $document_date = strtoupper(date('M j, Y', strtotime($mt_data->modified_on))); ?>

   <div style="width: 100%; margin: auto;">

      <!-- Company logo and other information -->
      <table style="width: 1000px;" align="center">
         <tr>
            <td valign="top">
               <?php echo $company_details; ?>
            </td>

            <td valign="top" align="right">
               <br />
               <span style="font-size: 2rem; color: #434c5e;">
                  <?php if ($this->ion_auth->isGSTMerchant()) {
                      echo 'TAX';
                  } else {
                      echo 'SALES';
                  } ?> INVOICE</span> <br />

               <span style="width: 150px; font-weight: normal; letter-spacing: 5px; color: #FF43A4; padding-top: 5px; font-size: 1.2rem;"><?php echo $mt_data->invoice_ref_no; ?></span><br /><br />

               <div style="width: 150px; padding: 10px; font-weight: normal; letter-spacing: 2px; color: dimgray; border-bottom: 1px solid #ccc; font-style: italic; font-size: 1rem;">
                  Date of Invoice <br />
                  <span style="padding: 10px; border-top: 1px solid #ccc; letter-spacing: 1px; color: #000; background: #f5f5f5;">
                     <?php echo $document_date; ?>
                  </span>
               </div>
            </td>
         </tr>

         <tr>
            <td colspan="2" class="logo-border" height="20"></td>
         </tr>
      </table>

      <br />

      <!-- Customer address and employee information -->
      <table style="width: 1000px;" cellspacing="0" cellpadding="0" align="center">
         <tr>
            <td style="width: 65%; padding: 0; margin: 0" valign="top">
               <table width="100%" style="margin-top: 10px; color: 000">
                  <tr>
                     <td>
                        <span style="color: dimgray">TO</span>
                     </td>
                  </tr>

                  <tr>
                     <td>
                        <span style="font-weight: bold;"><?php echo $customer_name; ?> (<?php echo $customer_code; ?>)</span><br />

                        <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                           <?php if ($special_gst_srcas_exist) { ?>
                              <i>GST Reg No: </i><?php echo $customer_gst_number; ?> <br />
                           <?php } ?>
                        <?php } ?>

                        <?php echo $customer_address; ?> <br />
                     </td>
                  </tr>
               </table>
            </td>
            <td style="width: 35%; text-align: right">
               <span style="color: dimgray">Staff-in-Charge</span> <br />
               <?php echo $employee_data->name; ?> (<?php echo $employee_data->code; ?>)<br />
               
               <?php if ($employee_data->email != '') {
                   echo $employee_data->email.'<br />';
               } ?>

               <?php $department = $this->custom->getSingleValue('master_department', 'name', ['d_id' => $employee_data->department_id]);
   if ($department != '') {
       echo $department.' Dept';
   }
   if ($mt_data->quotation_ref_no != '') { ?>
               <br /><br />
               <u>Cross Reference</u>: <?php echo $mt_data->quotation_ref_no; ?>
               <?php } ?>
            </td>
         </tr>
      </table>

      <table><tr><td height="25"></td></tr></table>

      <!-- header information -->
      <?php if ($mt_data->header_notes !== '') { ?>
         <table style="width: 1000px; background: #f5f5f5;" align="center">
            <tr>
               <td style="text-align: center">
                  <span style="font-family: cursive; color: #C62168; font-style: italic;"><?php echo $mt_data->header_notes; ?></span>
               </td>
            </tr>
         </table>
      <?php } ?>

      <table><tr><td height="20"></td></tr></table>

      <!-- product and service information -->
      <table class="tbl-items" style="width: 1000px;" align="center">
         <thead>
            <tr>
               <th valign="bottom" style="padding-left: 6px; padding-right: 6px; width: 20px">#</th>
               <th style="width: 370px;" valign="bottom">Item</th>
               <th style="width: 80px;" valign="bottom">Quantity</th>
               <th style="width: 140px; text-align: right" valign="bottom"><span style="color: dimgray; font-weight: normal">(<?php echo $customer_currency; ?>)</span><br />Unit Price</th>
               <th style="width: 110px; text-align: right" valign="bottom">Disc %</th>
               <th style="width: 150px; text-align: right" valign="bottom"><span style="color: dimgray; font-weight: normal">(<?php echo $customer_currency; ?>)</span><br />Amount</th>
               <?php if ($this->ion_auth->isGSTMerchant()) { ?>
               <th style="width: 250px;" valign="bottom">GST Code & Desc</th>
               <th style="width: 125px; text-align: right" valign="bottom">GST $</th>
               <?php } ?>
            </tr>
         </thead>
         <tbody>
         <?php
   $i = 1;
   foreach ($pr_data as $value) {
       $billing_data = $this->custom->getSingleRow('master_billing', ['billing_id' => $value->billing_id]); ?>
            <tr style="page-break-inside: avoid;">
               <!-- 1. sno -->
               <td valign="top" style="text-align: center; padding-left: 6px; padding-right: 6px;"><?php echo $i; ?></td>

               <!-- 2. description -->
               <td valign="top">
                  <span style="color: #000;"><?php echo '('.$billing_data->stock_code.') '.$billing_data->billing_description; ?></span><br />
                  <?php if ($value->details != '') { ?>
                     <br />
                     <span style="font-style: oblique; color: dimgray; text-align: center">
                        <?php echo $value->details; ?>
                     </span>
                  <?php } ?>
               </td>

               <?php if ($billing_data->billing_uom != '' && $billing_data->billing_uom != null) { ?>
                  <!-- 3. quantity & UOM -->
                  <td style="text-align: right" valign="top">
                     <?php echo $value->quantity.' '.$billing_data->billing_uom; ?>
                  </td>

                  <!-- 4. unit price -->
                  <td style="text-align: right" valign="top">
                     <?php echo number_format($value->unit_price, 2); ?>
                  </td>

                  <!-- 5. discount -->
                  <td style="text-align: right" valign="top">
                     <?php if ($value->discount > 0) {
                         echo $value->discount;
                     } ?>
                  </td>
               <?php } else { ?>
                  <td></td>
                  <td></td>
                  <td></td>
               <?php } ?>

               <!-- 6. item total -->
               <td style="text-align: right" valign="top">
                  <?php echo number_format($value->amount, 2); ?>
               </td>

               <?php if ($this->ion_auth->isGSTMerchant()) {
                   $gst_data = $this->custom->getMultiValues('ct_gst', 'gst_description, gst_rate', ['gst_code' => $value->gst_category]); ?>
                  <!-- 7. gst code & description -->
                  <td valign="top">
                     <?php echo $value->gst_category.' : '.$gst_data->gst_description.' ('.$gst_data->gst_rate.'%)'; ?>
                  </td>

                  <!-- 8. gst - derived amount -->
                  <td valign="top" style="text-align: right">
                     <?php echo number_format($value->gst_amount, 2); ?>
                  </td>
               <?php } ?>

            </tr>
         <?php ++$i; } ?>
         </tbody>
      </table>
    
      <br />

      <table style="width: 1000px;" align="center">
         <tr>
            <td>
               <!-- amount and gst information -->
               <table align="right" class="tbl-total" style="width: 100%; page-break-inside: avoid">
                  <tbody>
                     <tr>
                        <td class="first">Subtotal</td>
                        <td class="second" width="350">
                           <?php echo number_format($mt_data->sub_total, 2); ?>
                        </td>
                     </tr>

                     <tr>
                        <td class="first">Lump Sum Discount</td>

                        <td class="second" style="text-align: left">
                           <table style="width: 100%" cellspacing="0" cellpadding="0">
                              <tr>
                                 <td style="border: none; text-align: left;">
                                    <?php if ($mt_data->lsd_code == 'P') { ?>
                                       <span style="padding: 5px; background: #fff"><?php echo floatval($mt_data->lsd_percentage).' %'; ?></span>
                                    <?php } ?>
                                 </td>
                                 <td align="right" style="border: none; padding: 0">
                                    (<?php echo number_format($mt_data->lsd_value, 2); ?>)
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>

                     <tr>
                        <td class="first">Net after Lump Sum Discount</td>
                        <td class="second">
                           <?php echo number_format($mt_data->net_after_lsd, 2); ?>
                        </td>
                     </tr>

                     <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                        <tr>
                           <td class="first">GST Total <strong>(<?php echo $customer_currency; ?>)</strong></td>
                           <td class="second">
                              +<?php echo number_format($mt_data->f_gst_total, 2); ?>
                           </td>
                        </tr>
                     <?php } ?>

                     <tr>
                        <td class="first">Net Total <?php echo $this->ion_auth->isGSTMerchant() ? '(Incl Tax)' : ''; ?> <strong>(<?php echo $customer_currency; ?>)</strong></td>
                        <td class="second">
                           <?php echo number_format($mt_data->f_net_total, 2); ?>
                        </td>
                     </tr>

                     <?php if ($customer_currency !== $system_currency) { ?>
                        <?php if ($this->ion_auth->isGSTMerchant()) { ?>
                           <tr>
                              <td class="first">GST Total @ <u><?php echo $currency_rate; ?></u> <strong><?php echo '('.$system_currency.')'; ?></strong></td>
                              <td class="second">
                                 <?php echo number_format($mt_data->gst_total, 2); ?>
                              </td>
                           </tr>
                        <?php } ?>

                        <tr>
                           <td class="first">Net Total <?php echo $this->ion_auth->isGSTMerchant() ? '(Incl Tax)' : ''; ?> @ <u><?php echo $currency_rate; ?></u> <strong><?php echo '('.$system_currency.')'; ?></strong></td>
                           <td class="second">
                              <?php echo number_format($mt_data->net_total, 2); ?>
                           </td>
                        </tr>
                     <?php } ?>
                  </tbody>
               </table>
            </td>
         </tr>
      </table>

      <table><tr><td height="15"></td></tr></table>

      <div style="page-break-inside: avoid">
         <?php if ($this->ion_auth->isGSTMerchant() && $special_gst_srcas_exist) { ?>
            <br />
            <table style="width: 1000px;" align="center">
               <tr>
                  <td>Sale made under customer accounting. Customer to account for GST of <?php echo $special_gst_srcas_amount; ?></td>
               </tr>
            </table>
         <?php } ?>

         <!-- footer information -->
         <br /><br />

         <?php if ($mt_data->payment_terms != '') { ?>
         <table style="width: 1000px;">
            <tr>
               <td>
                  <strong>Payment Terms :</strong> <?php echo $mt_data->payment_terms; ?>
               </td>
            </tr>
         </table>
         <?php } ?>

         <table><tr><td height="10"></td></tr></table>

         <?php if ($mt_data->order_notes != '') { ?>
         <table style="width: 1000px;">
            <tr>
               <td><strong>Order Notes :</strong> <?php echo $mt_data->order_notes; ?></td>
            </tr>
         </table>
         <?php } ?>

         <table><tr><td height="10"></td></tr></table>


         <?php if ($mt_data->footer_notes != '') { ?>
         <table style="width: 1000px; background: #f5f5f5;" align="center">
            <tr>
               <td style="text-align: center">
                  <span style="font-family: cursive; color: #C62168; font-style: italic;"><?php echo $mt_data->footer_notes; ?></span>
               </td>
            </tr>
         </table>
         <?php } ?>

         <table><tr><td height="10"></td></tr></table>

         <br /><br />
         <!-- customer Signature and date -->
         <table style="width: 1000px;" align="center">
            <tr>
               <td>
                  Customer Signature and Co Stamp<br /> <br />
                  Name: <br />
                  Date:
               </td>
            </tr>
         </table>
      </div>
   </div>
</body>
</html>
