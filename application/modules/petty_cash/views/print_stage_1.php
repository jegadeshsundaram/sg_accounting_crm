<!DOCTYPE html>
<html>
    <head>
        <style type="text/css">
            body { font-size: 1.3rem }
            .tbl-display { border-collapse: collapse;}
            .tbl-display th { background: #fff; color: #000; border-bottom: 1px solid #ccc; padding: 15px; text-align: left; font-weight: bold;}
            .tbl-display td {
                border-top: 1px solid #ccc;
                padding: 15px; text-align: left;
            }
            
            .tbl-display tr:nth-child(even) {background-color: #f8f9fa;}
        </style>
    </head>
<body>
   <div style="width: 100%; margin: auto;">
      <table style="width: 1000px; text-align: center;" align="center">
         <tr>
            <td><h2><?php echo $company_details->company_name; ?></h2></td>
         </tr>
         <tr><td height="20"></td></tr>
         <tr>
            <td><h4>PETTY CASH VOUCHER</h4></td>
         </tr>
      </table>

      <hr />
      
      <table style="width: 1000px;" align="center">
         <tr><td colspan="2" height="10"></td></tr>
         <tr>
            <td style="width: 50%" valign="top">
               <strong>Pay To,</strong> <br /><?php echo $pay_to; ?>
            </td>
            <td style="width: 50%; text-align: right" valign="top">
               <span style="width: 150px; padding: 10px;"><strong>Date <strong></span> <?php echo strtoupper(date('M j, Y', strtotime($document_date))); ?> <br /><br />
               <span style="width: 150px; padding: 10px;"><strong>Voucher#<strong></span> <?php echo $document_reference; ?>
            </td>
         </tr>         
      </table>
      <br />
      <table class="tbl-display" style="width: 1000px;" align="center">
         <thead>
            <tr>
               <th align="center">#</th>
               <th valign="bottom">Account & Description</th>
               <th valign="bottom" align="right">Amount</th>
               <th valign="bottom" style="width: 200px">Remarks</th>
            </tr>
         </thead>
         <tbody>
         <?php
            $i = 1;
            $total_amount = 0;
            $batch_entry = $this->custom->getRows('petty_cash_batch', ['ref_no' => $document_reference]);
            foreach ($batch_entry as $value) {
                $coa_description = $this->custom->getSingleValue('chart_of_account', 'description', ['accn' => $value->accn]);
                $total_amount += $value->amount; ?>
                
            <tr>
               <td style="width: 25px; text-align: center">
                  <span class="row_number"><?php echo $i; ?></span>
               </td>

               <td style="width: 300px">
                  <?php echo $value->accn.' : '.$coa_description; ?>
               </td>

               <td style="width: 150px" align="right">
                  <?php echo number_format($value->amount, 2); ?>
               </td>

               <td style="width: 350px">
                  <?php echo $value->remarks; ?>
               </td>
            </tr>
            <?php
            ++$i;
            }
            ?>

            <tr>
               <td colspan="2" style="color: blue" align="right">
                  <strong>Sub Total</strong>
               </td>
               <td align="right">
                  <strong><?php echo number_format($total_amount, 2); ?></strong>
               </td>
               <td></td>
            </tr>

         </tbody>
      </table>
      <br /><br /><br /><br />
      <table style="width: 1000px;" align="center">
         <tr>
            <td style="width: 300px">
               <strong>Received By,</strong><br /><br />
               <hr /><br />
               (<?php echo $received_by; ?>)
            </td>
            <td></td>
            <td style="width: 300px; text-align: right">
               <strong>Approved By,</strong><br /><br />
               <hr /><br />
               (<?php echo $approved_by; ?>)
            </td>
         </tr>
      </table>
   </div>
</body>
</html>