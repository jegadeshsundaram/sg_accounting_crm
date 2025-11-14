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
               <input type="hidden" name="fv7_id" id="fv7_id" value="<?php echo $fv_id; ?>" />
               <div class="card card-default">  
                  <div class="card-header">
                     <h5 style="color: blue">FORM 7 Values</h5>
                  </div>
                  <div class="card-body">

                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group row">
                              <label for="box_1_value" class="col-xl-6 control-label">Box 1 : <span class="hint">Total Value of Standard-Rated Supplies</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_1_value" name="box_1_value" 
                                    value="<?php echo $box_1_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_2_value" class="col-xl-6 control-label">Box 2 : <span class="hint">Total Value of Zero-Rated Supplies</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_2_value" name="box_2_value" 
                                    value="<?php echo $box_2_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_3_value" class="col-xl-6 control-label">Box 3 : <span class="hint">Total Value of Exempt Supplies</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_3_value" name="box_3_value" 
                                    value="<?php echo $box_3_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_4_value" class="col-xl-6 control-label">Box 4 : <span class="hint">Total Supplies (Total of Box (1) + Box (2) + Box (3))</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_4_value" name="box_4_value" 
                                    value="<?php echo $box_4_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_5_value" class="col-xl-6 control-label">Box 5 : <span class="hint">Total revised value of taxable purchases <br />(exclude purchases where input tax is disallowed)</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_5_value" name="box_5_value" 
                                    value="<?php echo $box_5_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_6_value" class="col-xl-6 control-label">Box 6 : <span class="hint">Revised Output Tax</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_6_value" name="box_6_value" 
                                    value="<?php echo $box_6_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_7_value" class="col-xl-6 control-label">Box 7 : <span class="hint">Revised input tax and refunds claimed<br />(exclude disallowed input tax)</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_7_value" name="box_7_value" 
                                    value="<?php echo $box_7_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_8_value" class="col-xl-6 control-label">Box 8 : <span class="hint"><?php echo $box_8_desc; ?></span></label>
                              <div class="col-xl-6">
                                 <input type="hidden" name="box_8_option" value="<?php echo $box_8_option; ?>" />
                                 <input 
                                    type="number" 
                                    id="box_8_value" name="box_8_value" 
                                    value="<?php echo $box_8_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_9_value" class="col-xl-6 control-label">Box 9 : <span class="hint">Less: Net GST paid/claimed previously for this accounting period</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_9_value" name="box_9_value" 
                                    value="<?php echo $box_9_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_10_value" class="col-xl-6 control-label">Box 10 : <span class="hint">Equals: Difference to be paid to / claimed from IRAS</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_10_value" name="box_10_value" 
                                    value="<?php echo $box_10_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_11_value" class="col-xl-6 control-label">Box 11 : <span class="hint">Total value of goods imported under import GST suspension schemes (e.g.Major Exporter Scheme/ Approved 3rd Party Logistics Company)</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_11_value" name="box_11_value" 
                                    value="<?php echo $box_11_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_12_value" class="col-xl-6 control-label">Box 12 : <span class="hint">Did you claim for GST you had refunded to tourists?</span></label>
                              <div class="col-xl-6">
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
                                    class="form-control inp w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_13_value" class="col-xl-6 control-label">Box 13 : <span class="hint">Did you make any bad debt relief claims and/or refund claims for reverse charge transactions?</span></label>
                              <div class="col-xl-6">
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
                        
                           <div class="form-group row">
                              <label for="box_14_value" class="col-xl-6 control-label">Box 14 : <span class="hint">Did you make any pre-registration claims?</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="radio" 
                                    id="box_14_yes" 
                                    name="box_14_option" 
                                    value="1" 
                                    class="rd_btn"
                                    <?php echo $box_14_option == '1' ? 'checked' : ''; ?> /> <label for="box_14_yes" style="font-weight: normal">Yes</label>

                                 <input 
                                    type="radio" 
                                    id="box_14_no" 
                                    name="box_14_option" 
                                    value="0" 
                                    class="rd_btn"
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
                        
                           <div class="form-group row">
                              <label for="box_15_value" class="col-xl-6 control-label">Box 15 : <span class="hint">Revenue for the accounting period</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_15_value" name="box_15_value" 
                                    value="<?php echo $box_15_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6">

                           <div class="form-group row">
                              <label for="box_16_value" class="col-xl-6 control-label">Box 16 : <span class="hint">Did you import services and/ or low-value goods subject to GST under reverse charge?</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="radio" 
                                    id="box_16_yes" 
                                    name="box_16_option" 
                                    value="1" 
                                    class="rd_btn"
                                    <?php echo $box_16_option == '1' ? 'checked' : ''; ?> /> <label for="box_16_yes" style="font-weight: normal">Yes</label>

                                 <input 
                                    type="radio" 
                                    id="box_16_no" 
                                    name="box_16_option" 
                                    value="0" 
                                    class="rd_btn"
                                    <?php echo $box_16_option == '0' ? 'checked' : ''; ?> 
                                    style="margin-left: 10px" /> <label for="box_16_no" style="font-weight: normal">No</label>
                                 
                                 <br />

                                 <input 
                                    type="number" 
                                    id="box_16_value" name="box_16_value" 
                                    value="<?php echo $box_16_value; ?>" 
                                    class="form-control inp w-200" readonly />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_17_value" class="col-xl-6 control-label">Box 17 : <span class="hint">Did you operate an electronic marketplace to supply remote services (includes digital and non-digital services) subject to GST on behalf of third-party suppliers?</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="radio" 
                                    id="box_17_yes" 
                                    name="box_17_option" 
                                    value="1" 
                                    class="rd_btn"
                                    <?php echo $box_17_option == '1' ? 'checked' : ''; ?> /> <label for="box_17_yes" style="font-weight: normal">Yes</label>

                                 <input 
                                    type="radio" 
                                    id="box_17_no" 
                                    name="box_17_option" 
                                    value="0" 
                                    class="rd_btn"
                                    <?php echo $box_17_option == '0' ? 'checked' : ''; ?> 
                                    style="margin-left: 10px" /> <label for="box_17_no" style="font-weight: normal">No</label>
                                 
                                 <br />

                                 <input 
                                    type="number" 
                                    id="box_17_value" name="box_17_value" 
                                    value="<?php echo $box_17_value; ?>" 
                                    class="form-control inp w-200" readonly />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_18_value" class="col-xl-6 control-label">Box 18 : <span class="hint">Did you operate as a redeliverer, or an electronic marketplace to supply imported low-value goods subject to GST on behalf of third-party suppliers?</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="radio" 
                                    id="box_18_yes" 
                                    name="box_18_option" 
                                    value="1" 
                                    class="rd_btn"
                                    <?php echo $box_18_option == '1' ? 'checked' : ''; ?> /> <label for="box_18_yes" style="font-weight: normal">Yes</label>

                                 <input 
                                    type="radio" 
                                    id="box_18_no" 
                                    name="box_18_option" 
                                    value="0" 
                                    class="rd_btn"
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

                           <div class="form-group row">
                              <label for="box_19_value" class="col-xl-6 control-label">Box 19: <span class="hint">Did you make your own supply of imported low-value goods that is subject to GST?</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="radio" 
                                    id="box_19_yes" 
                                    name="box_19_option" 
                                    value="1" 
                                    class="rd_btn"
                                    <?php echo $box_19_option == '1' ? 'checked' : ''; ?> /> <label for="box_19_yes" style="font-weight: normal">Yes</label>

                                 <input 
                                    type="radio" 
                                    id="box_19_no" 
                                    name="box_19_option" 
                                    value="0" 
                                    class="rd_btn"
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

                           <div class="form-group row">
                              <label for="box_20_value" class="col-xl-6 control-label">Box 20 <span class="hint">Revised deferred import GST payable</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_20_value" name="box_20_value" 
                                    value="<?php echo $box_20_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_21_value" class="col-xl-6 control-label">Box 21 <span class="hint">Less: Deferred import GST payable previously declared for this accounting period</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_21_value" name="box_21_value" 
                                    value="<?php echo $box_21_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_22_value" class="col-xl-6 control-label">Box 22 <span class="hint">Equals: Diffference in deferred import GST payable</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_22_value" name="box_22_value" 
                                    value="<?php echo $box_22_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_23_value" class="col-xl-6 control-label">Box 23 <span class="hint">Add: Difference in Net GST (per box 10 above)</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_23_value" name="box_23_value" 
                                    value="<?php echo $box_23_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_24_value" class="col-xl-6 control-label">Box 24 <span class="hint">Equal: Difference in total tax to be paid to /claimed from IRAS</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_24_value" name="box_24_value" 
                                    value="<?php echo $box_24_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_25_value" class="col-xl-6 control-label">Box 25 <span class="hint">Revised total tax to be paid to / claimed from IRAS (Box 8 + Box 20)</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_25_value" name="box_25_value" 
                                    value="<?php echo $box_25_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="box_26_value" class="col-xl-6 control-label">Box 26 <span class="hint">Total Revised value of goods imported under Import GST Deferment Scheme</span></label>
                              <div class="col-xl-6">
                                 <input 
                                    type="number" 
                                    id="box_26_value" name="box_26_value" 
                                    value="<?php echo $box_26_value; ?>" 
                                    class="form-control w-200" />
                              </div>
                           </div>

                           <div class="form-group row">
                              <label for="error_description" class="col-xl-12 control-label">Errors <span class="hint">Description of Errors</span></label>
                              <div class="col-xl-12">
                                 <textarea id="error_description" name="error_description" rows="4" cols="60"><?php echo $error_description; ?></textarea>
                              </div>
                           </div>

                        </div>
                     </div>

                  </div>
                  <div class="card-footer">
                     <a href="/gst/iras_api" class="btn btn-info btn-cancel">Back</a>
                     <button type="button" id="btn_submit" class="btn btn-warning float-right">SAVE & CONTINUE</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<style>
.hint {
   display: block;
   color: brown;
   font-size: 14px;
   font-weight: normal;
}
.control-label {
   padding-top: 0px !important;
   font-weight: bold !important;
}
.form-group
{
  margin-bottom: 20px;
}
</style>

<script>

   // document starts
   $(function() {

      $(".card").click(function() {
         $('#message_area').html("");
      });
      
      //$('input[name=box_14_option]').attr("disabled", true);
      //$('input[name=box_15_option]').attr("disabled", true);

      $(document).on("keyup", "#box_9_value", function() {
         if($.trim($(this).val()) !== "") {
            var box_6_value = $("#box_6_value").val();
            var box_7_value = $("#box_7_value").val();            
            var box_9_value = $(this).val();

            var net_gst = box_6_value - box_7_value;
            var net_difference = net_gst - box_9_value;

            $('#box_10_value').val(net_difference.toFixed(2));            
         } else {
            $('#box_10_value').val("");
         }
      });

      $(document).on("keyup", "#box_19_value", function() {
         if($.trim($(this).val()) !== "") {
            var box_18_value = $("#box_18_value").val();
            var box_19_value = $(this).val();

            var net_difference = box_18_value - box_19_value;

            $('#box_20_value').val(net_difference.toFixed(2));            
         } else {
            $('#box_20_value').val("");
         }
      });

      $(".rd_btn").change(function() {
         if($(this).val() == "1") {
            //$(this).closest('div').find('.inp').show();
            $(this).closest('div').find('.inp').focus();
            $(this).closest('div').find('.inp').val("");
         } else {
            //$(this).closest('div').find('.inp').hide();
            $(this).closest('div').find('.inp').val("");
         }
      });

      $('#btn_submit').click(function(e) {
         var url = '/gst/save_form_7';
         $("#frm_").attr("action", url);
         $("#frm_").submit();
      });

  });
</script>
