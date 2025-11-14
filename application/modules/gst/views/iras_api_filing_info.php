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
               <input type="hidden" name="fi_id" id="fi_id" value="<?php echo $fi_id; ?>" />
               <div class="card card-default">  
                  <div class="card-header">
                     <h5>Filing Information</h5>
                  </div>
                  <div class="card-body">
                     <div class="form-group row">
                        <label for="tax_ref_no" class="col-xl-4 control-label txt-right">Tax Reference Number : <span class="hint">Tax Reference Number of Company / Business</span></label>
                        <div class="col-xl-8">
                           <input 
                              type="text" 
                              id="tax_ref_no" name="tax_ref_no" 
                              value="<?php echo $tax_ref_no; ?>" 
                              class="form-control w-300" required />
                        </div>
                     </div>

                     <div class="form-group row">
                        <label for="form_type" class="col-xl-4 control-label txt-right">Form Type : <span class="hint">Form Type of the GST Return</span></label>
                        <div class="col-xl-8">
                           <select name="form_type" id="form_type" class="form-control w-300" required>
                              <option value="">Select Form</option>
                              <option value="F5" <?php if ($form_type == 'F5') {
                                  echo 'selected="selected"';
                              } ?>>Form 5</option>
                              <option value="F7" <?php if ($form_type == 'F7') {
                                  echo 'selected="selected"';
                              } ?>>Form 7</option>
                              <option value="F8" <?php if ($form_type == 'F8') {
                                  echo 'selected="selected"';
                              } ?>>Form 8</option>
                           </select>
                        </div>
                     </div>

                     <div class="form-group row">
                        <label for="start_date" class="col-xl-4 control-label txt-right">Start Date : <span class="hint">Start Period covered by this return</span></label>
                        <div class="col-xl-8">
                           <input 
                              type="text" 
                              id="start_date" name="start_date" 
                              placeholder="dd-mm-yyyy"
                              value="<?php echo $start_date; ?>" class="form-control dp_full_date w-120" required />
                        </div>
                     </div>

                     <div class="form-group row">
                        <label for="start_date" class="col-xl-4 control-label txt-right">End Date : <span class="hint">End Period covered by this return</span></label>
                        <div class="col-xl-8">
                           <input 
                              type="text" 
                              id="end_date" name="end_date" 
                              placeholder="dd-mm-yyyy"
                              value="<?php echo $end_date; ?>" class="form-control dp_full_date w-120" required />
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
   font-size: 13px;
   font-weight: normal;
   opacity: 0.8;
}
.control-label {
   padding-top: 0px !important;
}
</style>

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
   // document starts
   $(function() {
      $('select').select2();

      $('#btn_submit').on('click', function() {
         if(!$('#frm_').valid()) {
            return;
         }

         var url = '/gst/save_filing_info';
         $("#frm_").attr("action", url);
         $("#frm_").submit();
      });

   });
</script>
