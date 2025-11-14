<body style="font-family: sans-serif; font-size: 12pt;">

   <div style="width: 100%;">

      <!-- Compamy logo and other information -->
      <table width="98%" align="center">
         <tr>
            <td valign="top">
               <?php echo $this->custom->populateCompanyHeader(); ?>
            </td>
            <td valign="bottom" align="right">
               <span style="font-size: 1rem; color: dimgray;">PAYMENT #<span> <br />

               <span style="width: 150px; font-weight: normal; letter-spacing: 1px; color: #FF43A4; padding-top: 5px; font-size: 1rem;"><?php echo $mt_data->payment_ref_no; ?></span><br /><br />

               <div style="width: 150px; padding: 10px; font-weight: normal; color: dimgray; border-bottom: 1px solid #ccc; font-size: 1rem;">
                  Date of Payment <br />
                  <span style="padding: 10px; border-top: 1px solid #ccc; letter-spacing: 1px; color: #000; background: #f5f5f5;">
                     <?php echo strtoupper(date('M j, Y', strtotime($mt_data->modified_on))); ?>
                  </span>
               </div>
            </td>
         </tr>
      </table>

      <div style="width: 1000px; border-bottom: 1px solid gray; margin-bottom: 20px;">&nbsp;</div>

      <!-- Supplier information -->
      <table width="100%" align="center" style="border-collapse: collapse">
         <tr>
            <td width="62%">
               <span style="color: dimgray">To</span>
               <table style="margin-top: 5px;">
                  <tr>
                     <td>
                        <strong><?php echo $supplier_name_code; ?></strong>
                     </td>
                  </tr>
                  <tr>
                     <td style="margin-top: 5px;">
                        <?php echo $supplier_address; ?>
                     </td>
                  </tr>
               </table>
            </td>
            <td width="38%">
               <br /><br />
               <table align="right">
                  <tr>
                     <td height="25"><strong>Bank:</strong></td>
                     <td><?php echo $mt_data->bank; ?></td>
                  </tr>
                  <tr>
                     <td height="25"><strong>Cheque No:</strong></td>
                     <td><?php echo $mt_data->cheque; ?></td>
                  </tr>
                  <tr>
                     <td height="25"><strong>Amount:</strong></td>
                     <td><span style="color: red"><?php echo $mt_data->currency; ?></span> <?php echo number_format($mt_data->amount, 2); ?> DR</td>
                  </tr>
               </table>
            </td>
         </tr>
      </table>

      <table><tr><td height="25">&nbsp;</td></tr></table>

      <?php if ($entry != '0') { ?>
      <table align="center" style="width: 400px; border-collapse: collapse">
         <tr>
            <td>Payment details as follows :</td>
         </tr>
      </table>
      <br />

      <table align="center" style="width: 400px; border-collapse: collapse;">
         <tr>
            <th style="width: 120px; text-align: left; padding: 5px 5px 5px 10px; border: 1px solid #ccc;">Reference</th>
            <th style="width: 170px; text-align: right; padding: 5px 5px 5px 10px; border: 1px solid #ccc;">Amount</th>
         </tr>
         <tbody>
            <?php echo $documentToRow; ?>
         </tbody>
      </table>

      <table><tr><td height="50">&nbsp;</td></tr></table>
      <?php }  ?>

      <table width="98%" align="center">
         <tr>
            <td><strong>Remarks:</strong><br /> <?php echo $mt_data->other_reference; ?></td>
         </tr>
      </table>

   </div>

</body>
