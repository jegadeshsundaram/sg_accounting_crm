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
               <input type="hidden" name="ci_id" id="ci_id" value="<?php echo $ci_id; ?>" />
               <div class="card card-default">  
                  <div class="card-header">
                     <h5>Contact Information</h5>
                  </div>
                  <div class="card-body">                    

                     <div class="form-group row">
                        <label for="contact_person" class="col-xl-4 control-label">Name : <span class="hint">Name of the Contact Person</span></label>
                        <div class="col-xl-8">
                           <input 
                              type="text" 
                              id="contact_person" name="contact_person" 
                              value="<?php echo $name; ?>" class="form-control w-200" required />
                        </div>
                     </div>

                     <div class="form-group row">
                        <label for="contact_number" class="col-xl-4 control-label">Phone : <span class="hint">Telephone Number of the Contact Person</span></label>
                        <div class="col-xl-8">
                           <input 
                              type="text" 
                              id="contact_number" name="contact_number" 
                              value="<?php echo $phone; ?>" class="form-control w-150" required />
                        </div>
                     </div>

                     <div class="form-group row">
                        <label for="contact_email" class="col-xl-4 control-label">Email : <span class="hint">Email Address of the Contact Person</span></label>
                        <div class="col-xl-8">
                           <input 
                              type="email" 
                              id="contact_email" name="contact_email" 
                              value="<?php echo $email; ?>" class="form-control w-350" required />
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

<script>
   // document starts
   $(function() {

      $(".card").click(function() {
         $('#message_area').html("");
      });

      $("#declaration_item_1").change(function() {
         if($(this).prop('checked')) {
            $("#declaration_item_1").val("yes");
         } else {
            $("#declaration_item_1").val("no");
         }
      });

      $("#declaration_item_2").change(function() {
         if($(this).prop('checked')) {
            $("#declaration_item_2").val("yes");
         } else {
            $("#declaration_item_2").val("no");
         }
      });

      $('#btn_submit').on('click', function() {
         if(!$('#frm_').valid()) {
            return;
         }

         var url = '/gst/save_contact_info';
         $("#frm_").attr("action", url);
         $("#frm_").submit();
      });

  });
</script>
