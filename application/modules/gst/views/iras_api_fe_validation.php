<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">GST Returns</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">GST</li>
               <li class="breadcrumb-item active">IRAS API</li>
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
            <form id="frm_" action="#" method="POST">
               <div class="card card-default">  
                  <div class="card-header">
                     <h5>Front-End Validation <span style="color: blue; font-weight: bold">(
                     <?php if ($form_type == 'F5' || $form_type == 'F8') { ?>
                        FORM 5
                     <?php } ?>

                     <?php if ($form_type == 'F7') { ?>
                        FORM 7
                     <?php } ?>)</span>
                     </h5>
                  </div>
                  <div class="card-body">
                  
                     <?php if ($fe_val_success) { ?>
                        <div class="row box">
                           <div class="col-md-12" style="background: honeydew; border-radius: 5px; padding: 10px">
                              <span style="color: green">All validations are passed.</span><br />
                              <strong>Form Type:</strong> <?php echo $form_type; ?><br />
                              <strong>Period:</strong> <?php echo $start_date; ?> <i>to</i> <?php echo $end_date; ?>
                              <br /><br />
                              <a href="/gst/iras_api_generate_json" class="btn bg-purple">Generate JSON Request</a>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- SNO :: 5 || FE Validation 2 -->
                     <?php if ($fe_val_2) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              <div class="form-group row">
                                 <label class="col-md-7 control-label">Box 3 <span class="hint">Total Value of Exempt Supplies</span></label>
                                 <div class="col-md-5">
                                    <input type="text" id="box_3_value" name="box_3_value" value="<?php echo $box_3_value; ?>" class="form-control w-200" readonly />
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label class="col-md-7 control-label">Box 7 <span class="hint">Input Tax and Refunds Claimed</span></label>
                                 <div class="col-md-5">
                                    <input type="text" id="box_7_value" name="box_7_value" value="<?php echo $box_7_value; ?>" class="form-control w-200" readonly />
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <span>Rule:</span> <br />
                                    Box 7 > 0 and Box 3 <> 0 and any of the supplies contains Non-Regulation 33 Exempt Supplies (Tax Code: "ESN33")
                                 </div>
                                 <div class="vmsg">
                                    “Input tax incurred in the making of exempt supplies is not claimable unless the De Minimis Rule is satisfied. Please review if De Minimis Rule is satisfied.”
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- SNO :: 6 || FE Validation 3 -->
                     <?php if ($fe_val_3) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              <div class="form-group row">
                                 <label class="col-md-7 control-label">Box <?php echo $form_type == 'F7' ? '15' : '13'; ?> <span class="hint">Revenue</span></label>
                                 <div class="col-md-5">
                                    <input type="text" id="box_13_value" name="box_13_value" value="null" class="form-control" readonly />
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <span>Rule:</span> <br />
                                    <?php echo $form_type == 'F7' ? 'Box 15 ' : 'Box 13'; ?> is null
                                 </div>
                                 <div class="vmsg">
                                    “Revenue for the accounting period box is not entered with figure.”
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- SNO :: 7 to 10 || FE Validation 4 -->
                     <input type="hidden" name="grp_id" id="grp_id" value="<?php echo $grp_id; ?>" />
                     <?php if ($fe_val_4) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              <div class="form-group row">
                                 <label class="col-md-7 control-label">Box 1 <span class="hint">Total Value of Standard-Rated Supplies</span></label>
                                 <div class="col-md-5">
                                    <input type="text" id="box_1_value" name="box_1_value" value="<?php echo $box_1_value; ?>" class="form-control" readonly />
                                 </div>
                              </div>

                              <div class="form-group row">
                                 <label class="col-md-7 control-label">Box 6 <span class="hint"><?php echo $form_type == 'F7' ? 'Revised' : ''; ?> Output Tax</span></label>
                                 <div class="col-md-5">
                                    <input type="text" id="box_6_value" name="box_6_value" value="<?php echo $box_6_value; ?>" class="form-control" readonly />
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <span>Rule:</span> <br />
                                    <?php echo $fe_val_4_rule; ?>
                                 </div>
                                 <div class="vmsg">
                                    “<?php echo $fe_val_4_text; ?>” <br /><br />
                                    <div style="color: #000">
                                       Otherwise, please indicate reasons by clicking on the following: <br />

                                       <input 
                                       type="checkbox" 
                                       id="grp1BadDebtRecoveryChk" 
                                       name="grp1BadDebtRecoveryChk" 
                                       class="grp_chk" 
                                       value="<?php echo $grp1BadDebtRecoveryChk; ?>"                               
                                       <?php echo $grp1BadDebtRecoveryChk == '1' ? 'checked' : ''; ?> /> <label for="grp1BadDebtRecoveryChk" style="margin-left: 10px">GST on Bad Debt Recovery</label>
                                       <br />
                                       <input 
                                       type="checkbox" 
                                       id="grp1PriorToRegChk" 
                                       name="grp1PriorToRegChk" 
                                       class="grp_chk" 
                                       value="<?php echo $grp1PriorToRegChk; ?>"                               
                                       <?php echo $grp1PriorToRegChk == '1' ? 'checked' : ''; ?> /> <label for="grp1PriorToRegChk" style="margin-left: 10px">GST collected prior to registration</label>
                                       <br />
                                       <input 
                                       type="checkbox" 
                                       id="grp1OtherReasonChk" 
                                       name="grp1OtherReasonChk" 
                                       class="grp_chk" 
                                       title="others"
                                       value="<?php echo $grp1OtherReasonChk; ?>"                               
                                       <?php echo $grp1OtherReasonChk == '1' ? 'checked' : ''; ?> /> <label for="grp1OtherReasonChk" style="margin-left: 10px">Others, please specify reasons</label>
                                       
                                       <br />
                                       <input type="text" id="grp1OtherReasons" name="grp1OtherReasons" value="<?php echo $grp1OtherReasons; ?>" class="form-control others_input" maxlength="200" style="margin-top: 10px; margin-bottom: 10px; min-width: 320px" /> <br />

                                       <input type="button" id="save_grp1_reason_others" value="SAVE REASON" class="btn bg-purple btn-sm" />
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- SNO :: 11 to 13 || FE Validation 5 -->
                     <?php if ($fe_val_5) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              <div class="form-group row">
                                 <label class="col-md-7 control-label">Box 5 <span class="hint">Total <?php echo $form_type == 'F7' ? 'Revised' : ''; ?> Value of Taxable Purchases</span></label>
                                 <div class="col-md-5">
                                    <input type="text" id="box_5_value" name="box_5_value" value="<?php echo $box_5_value; ?>" class="form-control" readonly />
                                 </div>
                              </div>

                              <div class="form-group row">
                                 <label class="col-md-7 control-label">Box 7 <span class="hint"><?php echo $form_type == 'F7' ? 'Revised' : ''; ?> Input Tax and Refunds Claimed</span></label>
                                 <div class="col-md-5">
                                    <input type="text" id="box_7_value" name="box_7_value" value="<?php echo $box_7_value; ?>" class="form-control" readonly />
                                 </div>
                              </div>
                              
                              <?php if (($form_type == 'F5' || $form_type == 'F8') && $fe_val_5_box_display == 'Box_9') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 9 <span class="hint">Total Value of Goods Imported Under MES/3PL/Other Approved Scheme</span</label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_9_value" name="box_9_value" value="<?php echo $box_9_value; ?>" class="form-control" readonly />
                                    </div>
                                 </div>
                              <?php } ?>
                              
                              <?php if ($form_type == 'F7' && $fe_val_5_box_display == 'Box_11') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 11 <span class="hint">Total Value of Goods Imported Under MES/3PL/Other Approved Scheme</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_11_value" name="box_11_value" value="<?php echo $box_11_value; ?>" class="form-control" readonly />
                                    </div>
                                 </div>
                              <?php } ?>

                           </div>
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <span>Rule:</span> <br />
                                    <?php echo $fe_val_5_rule; ?>
                                 </div>
                                 <div class="vmsg">
                                    “<?php echo $fe_val_5_text; ?>” <br /><br />
                                    <div style="color: #000">
                                       Otherwise, please indicate reasons by clicking on the following: <br />

                                       <input 
                                          type="checkbox" 
                                          id="grp2TouristRefundChk" 
                                          name="grp2TouristRefundChk" 
                                          class="grp_chk" 
                                          value="<?php echo $grp2TouristRefundChk; ?>"
                                          <?php echo $grp2TouristRefundChk == '1' ? 'checked' : ''; ?> /> <label for="grp2TouristRefundChk" style="margin-left: 10px">Tourist Refund</label>

                                       <br />
                                       <input 
                                          type="checkbox" 
                                          id="grp2AppvBadDebtReliefChk" 
                                          name="grp2AppvBadDebtReliefChk" 
                                          class="grp_chk" 
                                          value="<?php echo $grp2AppvBadDebtReliefChk; ?>"
                                          <?php echo $grp2AppvBadDebtReliefChk == '1' ? 'checked' : ''; ?> /> <label for="grp2AppvBadDebtReliefChk" style="margin-left: 10px">Bad Debts Relief</label>
                                       <br />

                                       <input 
                                          type="checkbox" 
                                          id="grp2CreditNotesChk" 
                                          name="grp2CreditNotesChk" 
                                          class="grp_chk" 
                                          value="<?php echo $grp2CreditNotesChk; ?>"                               
                                          <?php echo $grp2CreditNotesChk == '1' ? 'checked' : ''; ?> /> <label for="grp2CreditNotesChk" style="margin-left: 10px">Credit Notes</label>

                                       <br />

                                       <input 
                                          type="checkbox" 
                                          id="grp2OtherReasonsChk" 
                                          name="grp2OtherReasonsChk" 
                                          class="grp_chk" 
                                          title="others" 
                                          value="<?php echo $grp2OtherReasonsChk; ?>"                               
                                          <?php echo $grp2OtherReasonsChk == '1' ? 'checked' : ''; ?> /> <label for="grp2OtherReasonsChk" style="margin-left: 10px">Others, please specify reasons</label>

                                       <br />
                                       <input type="text" id="grp2OtherReasons" name="grp2OtherReasons" value="<?php echo $grp2OtherReasons; ?>" class="form-control others_input" maxlength="200" style="min-width: 320px; margin-top: 10px; margin-bottom: 10px;" /> <br />

                                       <input type="button" id="save_grp2_reason_others" value="SAVE REASON" class="btn bg-purple btn-sm" />
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- SNO :: 14 || FE Validation 6-->
                     <?php if ($fe_val_6) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              <div class="form-group row">
                                 <label class="col-md-7 control-label">Box 5 <span class="hint">Total <?php echo $form_type == 'F7' ? 'Revised' : ''; ?> Value of Taxable Purchases</span></label>
                                 <div class="col-md-5">
                                    <input type="text" id="box_5_value" name="box_5_value" value="<?php echo $box_5_value; ?>" class="form-control" readonly />
                                 </div>
                              </div>

                              <?php if ($form_type == 'F5' || $form_type == 'F8') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 9 <span class="hint">Total Value of Goods Imported Under MES/3PL/Other Approved Scheme</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_9_value" name="box_9_value" value="<?php echo $box_9_value; ?>" class="form-control" readonly />
                                    </div>
                                 </div>
                              <?php } ?>
                              
                              <?php if ($form_type == 'F7') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 11 <span class="hint">Total value of goods imported under import GST suspension schemes (e.g.Major Exporter Scheme/Approved 3rd Party Logistics Company)</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_11_value" name="box_11_value" value="<?php echo $box_11_value; ?>" class="form-control" readonly />
                                    </div>
                                 </div>
                              <?php } ?>

                           </div>
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <span>Rule:</span> <br />
                                    <?php echo $fe_val_6_rule; ?>
                                 </div>
                                 <div class="vmsg">
                                    “Taxable purchases should include MES/3PL/Other Approved Schemes and therefore be greater than MES/3PL/Other Approved Schemes figures. Please re-enter the value of taxable purchases.” <br /><br />
                                    <div style="color: #000">                                    
                                    Otherwise, please indicate reasons by clicking on the following: <br />
                                    <input 
                                       type="checkbox" 
                                       id="grp3CreditNotesChk" 
                                       name="grp3CreditNotesChk" 
                                       class="grp_chk" 
                                       value="<?php echo $grp3CreditNotesChk; ?>"                               
                                       <?php echo $grp3CreditNotesChk == '1' ? 'checked' : ''; ?> /> <label for="grp3CreditNotesChk" style="margin-left: 10px">Credit Notes</label>
                                       
                                       <br />

                                    <input 
                                       type="checkbox" 
                                       id="grp3OtherReasonsChk" 
                                       name="grp3OtherReasonsChk" 
                                       class="grp_chk" 
                                       title="others" 
                                       value="<?php echo $grp3OtherReasonsChk; ?>"                               
                                       <?php echo $grp3OtherReasonsChk == '1' ? 'checked' : ''; ?> /> <label for="grp3OtherReasonsChk" style="margin-left: 10px">Others, please specify reasons</label>

                                       <br />
                                       <input type="text" id="grp3OtherReasons" name="grp3OtherReasons" value="<?php echo $grp3OtherReasons; ?>" class="form-control others_input" maxlength="200" style="min-width: 320px; margin-top: 10px; margin-bottom: 10px;" /> <br />

                                       <input type="button" id="save_grp3_reason_others" value="SAVE REASON" class="btn bg-purple btn-sm" />
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- SNO :: 15 || FE Validation 7 -->
                     <?php if ($fe_val_7) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              
                              <?php if (($form_type == 'F5' || $form_type == 'F8') && $fe_val_7_box_display == 'Box_10') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 10 <span class="hint">Did you claim for GST you had refunded to tourists?</span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_10_yes" 
                                          name="box_10_option" 
                                          value="1" 
                                          <?php echo $box_10_option == '1' ? 'checked' : ''; ?> /> <label for="box_10_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_10_no" 
                                          name="box_10_option" 
                                          value="0" 
                                          <?php echo $box_10_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_10_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_10_value" name="box_10_value" 
                                          value="<?php echo $box_10_value; ?>" 
                                          class="form-control w-200" readonly />
                                    </div>
                                 </div>
                              <?php } ?>

                              <?php if (($form_type == 'F5' || $form_type == 'F8') && $fe_val_7_box_display == 'Box_11') { ?>
                                 <div class="form-group row">
                                    <label for="box_11_value" class="col-md-7 control-label">Box 11 : <span class="hint">Did you make any bad debt relief claims and/or refund claims for reverse charge transactions?</span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_11_yes" 
                                          name="box_11_option" 
                                          value="1" 
                                          class="rd_btn"
                                          <?php echo $box_11_option == '1' ? 'checked' : ''; ?> /> <label for="box_11_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_11_no" 
                                          name="box_11_option" 
                                          value="0" 
                                          class="rd_btn"
                                          <?php echo $box_11_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_11_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_11_value" name="box_11_value" 
                                          value="<?php echo $box_11_value; ?>" 
                                          class="form-control w-200" readonly />
                                    </div>
                                 </div>
                              <?php } ?>

                              <?php if ($fe_val_7_box_display == 'Box_12') { ?>
                                 <div class="form-group row">
                                    <label for="box_11_value" class="col-md-7 control-label">Box 12 
                                       <span class="hint">
                                          <?php if ($form_type == 'F5' || $form_type == 'F8') { ?>
                                             Did you make any pre-registration claims?
                                          <?php } if ($form_type == 'F7') { ?>
                                             Did you claim for GST you had refunded to tourists?
                                          <?php } ?>
                                       </span>
                                    </label>

                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_12_yes" 
                                          name="box_12_option" 
                                          value="1" 
                                          class="rd_btn"
                                          <?php echo $box_12_option == '1' ? 'checked' : ''; ?> /> <label for="box_12_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_12_no" 
                                          name="box_12_option" 
                                          value="0" 
                                          class="rd_btn"
                                          <?php echo $box_12_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_12_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_12_value" name="box_12_value" 
                                          value="<?php echo $box_12_value; ?>" 
                                          class="form-control w-200" style="display: <?php echo $box_12_option == '1' ? 'block' : 'none'; ?>;" readonly />
                                    </div>
                                 </div>
                              <?php } ?>

                              <?php if (($form_type == 'F7') && $fe_val_7_box_display == 'Box_13') { ?>
                                 <div class="form-group row">
                                    <label for="box_13_value" class="col-md-7 control-label">Box 13 : <span class="hint">Did you make any bad debt relief claims and/or refund claims for reverse charge transactions?</span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_13_yes" 
                                          name="box_13_option" 
                                          value="1" 
                                          class="rd_btn"
                                          <?php echo $box_13_option == '1' ? 'checked' : ''; ?> /> <label for="box_13_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_13_no" 
                                          name="box_13_option" 
                                          value="0" 
                                          class="rd_btn"
                                          <?php echo $box_13_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_13_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_13_value" name="box_13_value" 
                                          value="<?php echo $box_13_value; ?>" 
                                          class="form-control inp w-200" />
                                    </div>
                                 </div>
                              <?php } ?>

                              <?php if (($form_type == 'F7') && $fe_val_7_box_display == 'Box_14') { ?>
                                 <div class="form-group row">
                                    <label for="box_14_value" class="col-md-7 control-label">Box 14 : <span class="hint">Did you make any pre-registration claims?</span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_14_yes" 
                                          name="box_14_option" 
                                          value="1" 
                                          <?php echo $box_14_option == '1' ? 'checked' : ''; ?> /> <label for="box_14_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_14_no" 
                                          name="box_14_option" 
                                          value="0" 
                                          <?php echo $box_14_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_14_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_14_value" name="box_14_value" 
                                          value="<?php echo $box_14_value; ?>" 
                                          class="form-control inp w-200" />
                                    </div>
                                 </div>
                              <?php } ?>

                              <div class="form-group row">
                                 <label for="box_7_value" class="col-md-7 control-label">Box 7 
                                    <?php if ($form_type == 'F7') { ?>
                                       <span class="hint">Revised input tax and refunds claimed (exclude disallowed input tax)</span>                                    
                                    <?php } else { ?>
                                       <span class="hint">Input Tax and Refunds Claimed</span>
                                    <?php } ?>
                                 </label>
                                 <div class="col-md-5">
                                    <input type="text" id="box_7_value" name="box_7_value" value="<?php echo $box_7_value; ?>" class="form-control w-200" readonly />
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <span>Rule:</span> <br />
                                    <?php echo $fe_val_7_rule; ?>
                                 </div>
                                 <div class="vmsg">
                                    “<?php echo $fe_val_7_text; ?>”
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- SNO :: 16 || FE Validation 8 -->
                     <?php if ($fe_val_8) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              
                              <?php if (($form_type == 'F5' || $form_type == 'F8') && $fe_val_8_box_display == 'Box_10') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 10 <span class="hint">Did you claim for GST you had refunded to tourists?</span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_10_yes" 
                                          name="box_10_option" 
                                          value="1" 
                                          <?php echo $box_10_option == '1' ? 'checked' : ''; ?> /> <label for="box_10_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_10_no" 
                                          name="box_10_option" 
                                          value="0" 
                                          <?php echo $box_10_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_10_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_10_value" name="box_10_value" 
                                          value="<?php echo $box_10_value; ?>" 
                                          class="form-control w-200" readonly />
                                    </div>
                                 </div>
                              <?php } ?>

                              <?php if (($form_type == 'F5' || $form_type == 'F8') && $fe_val_8_box_display == 'Box_11') { ?>
                                 <div class="form-group row">
                                    <label for="box_11_value" class="col-md-7 control-label">Box 11 : <span class="hint">Did you make any bad debt relief claims and/or refund claims for reverse charge transactions?</span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_11_yes" 
                                          name="box_11_option" 
                                          value="1" 
                                          <?php echo $box_11_option == '1' ? 'checked' : ''; ?> /> <label for="box_11_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_11_no" 
                                          name="box_11_option" 
                                          value="0" 
                                          <?php echo $box_11_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_11_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_11_value" name="box_11_value" 
                                          value="<?php echo $box_11_value; ?>" 
                                          class="form-control w-200" readonly />
                                    </div>
                                 </div>
                              <?php } ?>

                              <?php if ($fe_val_8_box_display == 'Box_12') { ?>
                                 <div class="form-group row">
                                    <label for="box_11_value" class="col-md-7 control-label">Box 12 
                                       <span class="hint">
                                          <?php if ($form_type == 'F5' || $form_type == 'F8') { ?>
                                             Did you make any pre-registration claims?
                                          <?php } if ($form_type == 'F7') { ?>
                                             Did you claim for GST you had refunded to tourists?
                                          <?php } ?>
                                       </span>
                                    </label>

                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_12_yes" 
                                          name="box_12_option" 
                                          value="1" 
                                          <?php echo $box_12_option == '1' ? 'checked' : ''; ?> /> <label for="box_12_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_12_no" 
                                          name="box_12_option" 
                                          value="0" 
                                          <?php echo $box_12_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_12_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_12_value" name="box_12_value" 
                                          value="<?php echo $box_12_value; ?>" 
                                          class="form-control w-200" style="display: <?php echo $box_12_option == '1' ? 'block' : 'none'; ?>;" readonly />
                                    </div>
                                 </div>
                              <?php } ?>

                              <?php if (($form_type == 'F7') && $fe_val_8_box_display == 'Box_13') { ?>
                                 <div class="form-group row">
                                    <label for="box_13_value" class="col-md-7 control-label">Box 13 : <span class="hint">Did you make any bad debt relief claims and/or refund claims for reverse charge transactions?</span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_13_yes" 
                                          name="box_13_option" 
                                          value="1" 
                                          class="rd_btn"
                                          <?php echo $box_13_option == '1' ? 'checked' : ''; ?> /> <label for="box_13_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_13_no" 
                                          name="box_13_option" 
                                          value="0" 
                                          class="rd_btn"
                                          <?php echo $box_13_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_13_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_13_value" name="box_13_value" 
                                          value="<?php echo $box_13_value; ?>" 
                                          class="form-control inp w-200" />
                                    </div>
                                 </div>
                              <?php } ?>

                              <?php if (($form_type == 'F7') && $fe_val_8_box_display == 'Box_14') { ?>
                                 <div class="form-group row">
                                    <label for="box_14_value" class="col-md-7 control-label">Box 14 : <span class="hint">Did you make any pre-registration claims?</span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_14_yes" 
                                          name="box_14_option" 
                                          value="1" 
                                          <?php echo $box_14_option == '1' ? 'checked' : ''; ?> /> <label for="box_14_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_14_no" 
                                          name="box_14_option" 
                                          value="0" 
                                          <?php echo $box_14_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_14_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_14_value" name="box_14_value" 
                                          value="<?php echo $box_14_value; ?>" 
                                          class="form-control inp w-200" />
                                    </div>
                                 </div>
                              <?php } ?>
                              
                           </div>
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <span>Rule:</span> <br />
                                    <?php echo $fe_val_8_rule; ?>
                                 </div>
                                 <div class="vmsg">
                                    “<?php echo $fe_val_8_text; ?>”
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- SNO :: 17 to 18 || FE Validation 9 -->
                     <?php if ($fe_val_9) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              <div class="form-group row">
                                 <label class="col-md-7 control-label">Start Date <span class="hint">Start Period covered by this return (YYYY-MM-DD)</span></label>
                                 <div class="col-md-5">
                                    <input type="text" id="start_date" name="start_date" value="<?php echo $start_date; ?>" class="form-control w-150" readonly />
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label class="col-md-7 control-label">End Date <span class="hint">End Period covered by this return (YYYY-MM-DD)</span></label>
                                 <div class="col-md-5">
                                    <input type="text" id="end_date" name="end_date" value="<?php echo $end_date; ?>" class="form-control w-150" readonly />
                                 </div>
                              </div>

                              <?php if (($form_type == 'F5' || $form_type == 'F8') && $fe_val_9_box_display == 'Box_14') { ?>
                                 <div class="form-group row">
                                    <label for="box_14_value" class="col-md-7 control-label">Box 14 : <span class="hint"><?php echo $fe_val_9_rule; ?></span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_14_yes" 
                                          name="box_14_option" 
                                          value="1" 
                                          <?php echo $box_14_option == '1' ? 'checked' : ''; ?> /> <label for="box_14_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_14_no" 
                                          name="box_14_option" 
                                          value="0" 
                                          <?php echo $box_14_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_14_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_14_value" name="box_14_value" 
                                          value="<?php echo $box_14_value; ?>" 
                                          class="form-control inp w-150" readonly />
                                    </div>
                                 </div>
                              <?php } ?>
                              
                              <?php if (($form_type == 'F5' || $form_type == 'F8') && $fe_val_9_box_display == 'Box_15') { ?>
                                 <div class="form-group row">
                                    <label for="box_15_value" class="col-md-7 control-label">Box 15 : <span class="hint"><?php echo $fe_val_9_rule; ?></span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_15_yes" 
                                          name="box_15_option" 
                                          value="1" 
                                          <?php echo $box_15_option == '1' ? 'checked' : ''; ?> /> <label for="box_15_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_15_no" 
                                          name="box_15_option" 
                                          value="0" 
                                          <?php echo $box_15_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_15_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_15_value" name="box_15_value" 
                                          value="<?php echo $box_15_value; ?>" 
                                          class="form-control inp w-150" readonly />
                                    </div>
                                 </div>
                              <?php } ?>
                              
                              <?php if ($form_type == 'F7' && $fe_val_9_box_display == 'Box_16') { ?>
                                 <div class="form-group row">
                                    <label for="box_16_value" class="col-md-7 control-label">Box 16 : <span class="hint"><?php echo $fe_val_9_rule; ?></span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_16_yes" 
                                          name="box_16_option" 
                                          value="1" 
                                          <?php echo $box_16_option == '1' ? 'checked' : ''; ?> /> <label for="box_16_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_16_no" 
                                          name="box_16_option" 
                                          value="0" 
                                          <?php echo $box_16_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_16_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_16_value" name="box_16_value" 
                                          value="<?php echo $box_16_value; ?>" 
                                          class="form-control inp w-150" readonly />
                                    </div>
                                 </div>
                              <?php } ?>

                              <?php if ($form_type == 'F7' && $fe_val_9_box_display == 'Box_17') { ?>
                                 <div class="form-group row">
                                    <label for="box_17_value" class="col-md-7 control-label">Box 17 : <span class="hint"><?php echo $fe_val_9_rule; ?></span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_17_yes" 
                                          name="box_17_option" 
                                          value="1" 
                                          <?php echo $box_17_option == '1' ? 'checked' : ''; ?> /> <label for="box_16_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_17_no" 
                                          name="box_17_option" 
                                          value="0" 
                                          <?php echo $box_17_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_17_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_17_value" name="box_17_value" 
                                          value="<?php echo $box_17_value; ?>" 
                                          class="form-control inp w-150" readonly />
                                    </div>
                                 </div>
                              <?php } ?>

                           </div>
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <span>Rule:</span> <br />
                                    <?php echo $fe_val_9_txt; ?> <br />
                                    <?php echo $fe_val_9_box_display.': '.$fe_val_9_rule; ?>
                                 </div>
                                 <div class="vmsg">
                                    <?php if ($fe_val_9_error_display) { ?>
                                       “Invalid input.”
                                    <?php } ?>
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- SNO :: 19 || FE Validation 10 -->
                     <?php if ($fe_val_10) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              <div class="form-group row">
                                 <label class="col-md-7 control-label">Start Date <span class="hint">Start Period covered by this return (YYYY-MM-DD)</span></label>
                                 <div class="col-md-5">
                                    <input type="text" id="start_date" name="start_date" value="<?php echo $start_date; ?>" class="form-control w-150" readonly />
                                 </div>
                              </div>
                              <div class="form-group row">
                                 <label class="col-md-7 control-label">End Date <span class="hint">End Period covered by this return (YYYY-MM-DD)</span></label>
                                 <div class="col-md-5">
                                    <input type="text" id="end_date" name="end_date" value="<?php echo $end_date; ?>" class="form-control w-150" readonly />
                                 </div>
                              </div>

                              <?php if (($form_type == 'F5' || $form_type == 'F8') && $fe_val_10_box_display == 'Box_16' && $fe_val_10_use) { ?>
                                 <div class="form-group row">
                                    <label for="box_16_value" class="col-md-7 control-label">Box 16 : <span class="hint"><?php echo $fe_val_10_rule; ?></span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_16_yes" 
                                          name="box_16_option" 
                                          value="1" 
                                          <?php echo $box_16_option == '1' ? 'checked' : ''; ?> /> <label for="box_16_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_16_no" 
                                          name="box_16_option" 
                                          value="0" 
                                          <?php echo $box_16_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_16_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_16_value" name="box_16_value" 
                                          value="<?php echo $box_16_value; ?>" 
                                          class="form-control inp w-150" readonly />
                                    </div>
                                 </div>
                              <?php } ?>
                              
                              <?php if (($form_type == 'F5' || $form_type == 'F8') && $fe_val_10_box_display == 'Box_17' && $fe_val_10_use) { ?>
                                 <div class="form-group row">
                                    <label for="box_17_value" class="col-md-7 control-label">Box 17 : <span class="hint"><?php echo $fe_val_10_rule; ?></span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_17_yes" 
                                          name="box_17_option" 
                                          value="1" 
                                          <?php echo $box_17_option == '1' ? 'checked' : ''; ?> /> <label for="box_16_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_17_no" 
                                          name="box_17_option" 
                                          value="0" 
                                          <?php echo $box_17_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_17_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_17_value" name="box_17_value" 
                                          value="<?php echo $box_17_value; ?>" 
                                          class="form-control inp w-200" />
                                    </div>
                                 </div>
                              <?php } ?>

                              <?php if ($form_type == 'F7' && $fe_val_10_box_display == 'Box_18') { ?>
                                 <div class="form-group row">
                                    <label for="box_18_value" class="col-md-7 control-label">Box 18 : <span class="hint"><?php echo $fe_val_10_rule; ?></span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_18_yes" 
                                          name="box_18_option" 
                                          value="1" 
                                          <?php echo $box_18_option == '1' ? 'checked' : ''; ?> /> <label for="box_16_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_18_no" 
                                          name="box_18_option" 
                                          value="0" 
                                          <?php echo $box_18_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_18_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_18_value" name="box_18_value" 
                                          value="<?php echo $box_18_value; ?>" 
                                          class="form-control inp w-200" />
                                    </div>
                                 </div>
                              <?php } ?>

                              <?php if ($form_type == 'F7' && $fe_val_10_box_display == 'Box_19') { ?>
                                 <div class="form-group row">
                                    <label for="box_19_value" class="col-md-7 control-label">Box 19 : <span class="hint"><?php echo $fe_val_10_rule; ?></span></label>
                                    <div class="col-md-5">
                                       <input 
                                          type="radio" 
                                          id="box_19_yes" 
                                          name="box_19_option" 
                                          value="1" 
                                          <?php echo $box_19_option == '1' ? 'checked' : ''; ?> /> <label for="box_19_yes" style="font-weight: normal">Yes</label>

                                       <input 
                                          type="radio" 
                                          id="box_19_no" 
                                          name="box_19_option" 
                                          value="0" 
                                          <?php echo $box_19_option == '0' ? 'checked' : ''; ?> 
                                          style="margin-left: 10px" /> <label for="box_19_no" style="font-weight: normal">No</label>
                                       
                                       <br />

                                       <input 
                                          type="number" 
                                          id="box_19_value" name="box_19_value" 
                                          value="<?php echo $box_19_value; ?>" 
                                          class="form-control inp w-200" />
                                    </div>
                                 </div>
                              <?php } ?>
                           </div>
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <?php if ($fe_val_10_use) { ?>
                                       <span>Rule:</span> <br />
                                       If taxpayer selects “Yes” for return with accounting period on/or after 1 Jan 2023: <br />
                                       <?php echo $fe_val_10_box_display.': '.$fe_val_10_rule; ?>
                                    <?php } ?>

                                    <?php if ($form_type == 'F5' || $form_type == 'F8') { ?>
                                          The GST return does not have Box 16 and 17 for accounting period before 1 Jan 2023
                                    <?php } elseif ($form_type == 'F7') { ?>
                                       The GST return does not have Box 18 and 19 for accounting period before 1 Jan 2023                                       
                                    <?php } ?>
                                 </div>
                                 <div class="vmsg">
                                    <?php if ($fe_val_10_error_display) { ?>
                                       “Invalid input.”
                                    <?php } ?>
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- SNO :: 20 || FE Validation 11 -->
                     <?php if ($fe_val_11) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              <?php if ($form_type == 'F5' || $form_type == 'F8') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 19 <span class="hint">Add: Deferred import GST payable</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_19_value" name="box_19_value" value="<?php echo $box_19_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 21 <span class="hint">Total value of goods imported under Import GST Deferment Scheme</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_21_value" name="box_21_value" value="<?php echo $box_21_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                              <?php } ?>
                              <?php if ($form_type == 'F7') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 20 <span class="hint">Revised deferred import GST payable</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_20_value" name="box_20_value" value="<?php echo $box_20_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 26 <span class="hint">Revised value of goods imported under Import GST Deferment Scheme</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_26_value" name="box_26_value" value="<?php echo $box_26_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                              <?php } ?>
                           </div>
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <span>Rule:</span> <br />
                                    <?php echo $fe_val_11_rule; ?>
                                 </div>
                                 <div class="vmsg">
                                    “Total value of goods imported under IGDS is in positive value, deferred import GST payable should not be in negative value. Please re-enter value.”
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- SNO 21 || FE Validation 12 -->
                     <?php if ($fe_val_12) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              <?php if ($form_type == 'F5' || $form_type == 'F8') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 19 <span class="hint">Add: Deferred import GST payable</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_19_value" name="box_19_value" value="<?php echo $box_19_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 21 <span class="hint">Total value of goods imported under Import GST Deferment Scheme</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_21_value" name="box_21_value" value="<?php echo $box_21_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                              <?php } ?>
                              <?php if ($form_type == 'F7') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 20 <span class="hint">Revised deferred import GST payable</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_20_value" name="box_20_value" value="<?php echo $box_20_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 26 <span class="hint">Revised value of goods imported under Import GST Deferment Scheme</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_26_value" name="box_26_value" value="<?php echo $box_26_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                              <?php } ?>
                           </div>
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <span>Rule:</span> <br />
                                    <?php echo $fe_val_12_rule; ?>
                                 </div>
                                 <div class="vmsg">
                                    “Total value of goods imported under IGDS is completed; deferred import GST payable should not be zero value. Please re-enter value.”
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- SNO :: 22 || FE Validation 13 -->
                     <?php if ($fe_val_13) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              <?php if ($form_type == 'F5' || $form_type == 'F8') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 19 <span class="hint">Add: Deferred import GST payable</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_19_value" name="box_19_value" value="<?php echo $box_19_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 21 <span class="hint">Total value of goods imported under Import GST Deferment Scheme</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_21_value" name="box_21_value" value="<?php echo $box_21_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                              <?php } ?>
                              <?php if ($form_type == 'F7') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 20 <span class="hint">Revised deferred import GST payable</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_20_value" name="box_20_value" value="<?php echo $box_20_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 26 <span class="hint">Revised value of goods imported under Import GST Deferment Scheme</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_26_value" name="box_26_value" value="<?php echo $box_26_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                              <?php } ?>
                           </div>
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <span>Rule:</span> <br />
                                    <?php echo $fe_val_13_rule; ?>
                                 </div>
                                 <div class="vmsg">
                                    “As deferred import GST payable is completed, Total value of goods imported under IGDS should not be zero value. Please re-enter the value.”
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- SNO :: 23 || FE Validation 14 -->
                     <?php if ($fe_val_14) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              <?php if ($form_type == 'F5' || $form_type == 'F8') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 19 <span class="hint">Add: Deferred import GST payable</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_19_value" name="box_19_value" value="<?php echo $box_19_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 21 <span class="hint">Total value of goods imported under Import GST Deferment Scheme</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_21_value" name="box_21_value" value="<?php echo $box_21_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                              <?php } ?>
                              <?php if ($form_type == 'F7') { ?>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 20 <span class="hint">Revised deferred import GST payable</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_20_value" name="box_20_value" value="<?php echo $box_20_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                                 <div class="form-group row">
                                    <label class="col-md-7 control-label">Box 26 <span class="hint">Revised value of goods imported under Import GST Deferment Scheme</span></label>
                                    <div class="col-md-5">
                                       <input type="text" id="box_26_value" name="box_26_value" value="<?php echo $box_26_value; ?>" class="form-control w-150" readonly />
                                    </div>
                                 </div>
                              <?php } ?>
                           </div>
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <span>Rule:</span> <br />
                                    <?php echo $fe_val_14_rule; ?>
                                 </div>
                                 <div class="vmsg">
                                    “Total value of goods imported under IGDS should be more than deferred import GST payable. Please re-enter the value.”
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>

                     <!-- FE Validation 15 -->
                     <?php if ($fe_val_15) { ?>
                        <div class="row box">
                           <div class="col-md-6">
                              <div class="vbox">
                                 <div class="vrule">
                                    <span>Rule:</span> <br />
                                    Contact Telephone Number
                                 </div>
                                 <div class="vmsg">
                                 “Telephone number entered must be a 8-digit local number.”
                                 </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>
                  </div>
                  <div class="card-footer">
                     <a href="/gst/iras_api" class="btn btn-info btn-cancel">Back</a>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>      

<style type="text/css">
   .control-label {
      padding-top: 0px !important;
   }

   .box {
      border: 2px dotted gainsboro;
      padding: 15px 15px 15px 15px;
      background: floralwhite;
   }

   .vbox {
      border: 1px solid lavender;
      background: antiquewhite;
   }

   .vrule {
      padding: 5px 10px;
      color: #000;
      border: 0px solid gainsboro;
   }   

   .vrule span {
      letter-spacing: 2px;
      text-decoration: underline;
      font-weight: bold;
   }

   .vmsg {
      padding: 10px;
      color: red;
      border: 0px solid gainsboro;
   }
   label {
      font-weight: normal;
   }
   
   .hint {
      display: block;
      color: brown;
      font-size: 14px;
      font-weight: normal;
   }
</style>

<script>
   $(document).ready(function() {

      $(".card").click(function() {
         $('#message_area').html("");
      });

      $('input[name=box_10_option]').attr("disabled", true);
      $('input[name=box_11_option]').attr("disabled", true);
      $('input[name=box_12_option]').attr("disabled", true);
      $('input[name=box_13_option]').attr("disabled", true);
      $('input[name=box_14_option]').attr("disabled", true);
      $('input[name=box_15_option]').attr("disabled", true);
      $('input[name=box_16_option]').attr("disabled", true);
      $('input[name=box_17_option]').attr("disabled", true);

      $(".grp_chk").change(function() {
         if($(this).prop('checked')) {
            $(this).val("1");
            if($(this).attr("title") == "others") {
               //$(this).closest('div').find('.others_input').show();
               $(this).closest('div').find('.others_input').focus();
               $(this).closest('div').find('.others_input').val("");
            }
         } else {
            $(this).val("0");
            if($(this).attr("title") == "others") {
               //$(this).closest('div').find('.others_input').hide();
               $(this).closest('div').find('.others_input').val("");
            }
         }
      });

      $("#save_grp1_reason_others").click(function() {
         if($("#grp1BadDebtRecoveryChk").val() !== "1" &&  $("#grp1PriorToRegChk").val() !== "1" && $("#grp1OtherReasonChk").val() !== "1") {
            toastr.error("Please indicate any reason to save");
            return false;
         } else if($("#grp1OtherReasonChk").val() == "1" && $('#grp1OtherReasons').val() == "") {
            toastr.error("Other reasons text box cannot be empty");
            return false;
         } else if($("#grp1OtherReasonChk").val() !== "1" && $('#grp1OtherReasons').val() !== "") {
            toastr.error('The option “Others" cannot be Un-checked');
            $('#grp1OtherReasons').focus();
            return false;
         }

         $.post('/gst/ajax/save_grp1_reasons', {
            grp_id: $("#grp_id").val(),
            grp1BadDebtRecoveryChk: $("#grp1BadDebtRecoveryChk").val(),
            grp1PriorToRegChk: $("#grp1PriorToRegChk").val(),
            grp1OtherReasonChk: $("#grp1OtherReasonChk").val(),
            grp1OtherReasons: $("#grp1OtherReasons").val()
         }, function (data) {
            if(data !== "") {
               var obj = $.parseJSON(data);
               if(obj.grp_id !== "Update") {
                  $("#grp_id").val(obj.grp_id);
               }
               toastr.success('Values Saved');
            }
         });
      });

      $("#save_grp2_reason_others").click(function() {
         if($("#grp2TouristRefundChk").val() !== "1" &&  $("#grp2AppvBadDebtReliefChk").val() !== "1" && $("#grp2CreditNotesChk").val() !== "1" && $("#grp2OtherReasonsChk").val() !== "1") {
            toastr.error("Please indicate any reason to save");
            return false;
         } else if($("#grp2OtherReasonsChk").val() == "1" && $('#grp2OtherReasons').val() == "") {
            toastr.error("Please input other reasons");
            return false;
         } else if($("#grp2OtherReasonsChk").val() !== "1" && $('#grp2OtherReasons').val() !== "") {
            toastr.error("Please choose others option");
            return false;
         }

         $.post('/gst/ajax/save_grp2_reasons', {
            grp_id: $("#grp_id").val(),
            grp2TouristRefundChk: $("#grp2TouristRefundChk").val(),
            grp2AppvBadDebtReliefChk: $("#grp2AppvBadDebtReliefChk").val(),
            grp2CreditNotesChk: $("#grp2CreditNotesChk").val(),
            grp2OtherReasonsChk: $("#grp2OtherReasonsChk").val(),
            grp2OtherReasons: $("#grp2OtherReasons").val()
         }, function (data) {
            if(data !== "") {
               var obj = $.parseJSON(data);
               if(obj.grp_id !== "Update") {
                  $("#grp_id").val(obj.grp_id);
               }
               toastr.success('Values Saved');
            }
         });
      });
      

      $("#save_grp3_reason_others").click(function() {
         if($("#grp3CreditNotesChk").val() !== "1" && $("#grp3OtherReasonsChk").val() !== "1") {
            toastr.error("Please indicate any reason to save");
            return false;
         } else if($("#grp3OtherReasonsChk").val() == "1" && $('#grp3OtherReasons').val() == "") {
            toastr.error("Please input other reasons");
            return false;
         } else if($("#grp3OtherReasonsChk").val() !== "1" && $('#grp3OtherReasons').val() !== "") {
            toastr.error("Please choose others option");
            return false;
         }

         $.post('/gst/ajax/save_grp3_reasons', {
            grp_id: $("#grp_id").val(),
            grp3CreditNotesChk: $("#grp3CreditNotesChk").val(),
            grp3OtherReasonsChk: $("#grp3OtherReasonsChk").val(),
            grp3OtherReasons: $("#grp3OtherReasons").val()
         }, function (data) {
            if(data !== "") {
               var obj = $.parseJSON(data);
               if(obj.grp_id !== "Update") {
                  $("#grp_id").val(obj.grp_id);
               }
               toastr.success('Values Saved');
            }
         });
      });

   });
</script>