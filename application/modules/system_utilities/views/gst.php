<section class="content-header">
   <?php
      $list = ['active' => 'GST Migration'];
      echo breadcrumb($list);
   ?>
</section><br />
<section class="content">
   <?php echo get_flash_message('message'); ?>
   <div class="row">
      <div class="col-md-12">
         <div class="box box-info">
            <div class="box-header with-border">
               <h3 class="box-title">Import GST</h3>
            </div>

            <form autocomplete="off" id="form_import" class="form-horizontal validate" enctype="multipart/form-data" method="post" action="<?php echo $save_url; ?>">
               <br /><br />
               <div class="row">
                  <div class="col-sm-1"></div>
                  <div class="col-sm-8 form-group">
                  <div class="col-sm-3">
                     <label id="upload-btn" for="db_file" style="color: #f5f5f5; border-radius: 3px; text-align: center; cursor: pointer; width: 100%; padding: 10px; background: dodgerblue;">Upload GST File (.dbf)</label>
                  </div>
                  <div class="col-sm-9">
                     <input type="text" readonly="true" placeholder="Upload file will display here" name="uploaded_file_details" id="uploaded_file_details" style="line-height: 38px; width: 100%; border: none; border-bottom: 1px solid gray;" />
                     <input class="form-control inputfile ignore" name="db_file" id="db_file" type="file" />
                  </div>
                  </div>
                  <div class="col-sm-3"></div>
               </div>

               <br /><br />
               <div class="box-footer">
                  <a href="/data_migration/options" class="btn btn-default">Cancel</a>
                  <input type="button" id="submit_btn" name="submit_btn" class="btn btn-info pull-right" value="Submit" />
               </div>
            </form>
         </div>
      </div>
   </div>
</section>
<style>
   .inputfile {
      width: 0.1px;
      height: 0.1px;
      opacity: 0;
      overflow: hidden;
      position: absolute;
      z-index: -1;
   }
   .error {
      color: red;
      font-weight: normal;
   }
   .error:before {
      content: "* "
   }
</style>
<script type="text/javascript">
   $(document).ready(function() {
      $.validator.addMethod("extension", function (value, element, param) {
         param = typeof param === "string" ? param.replace(/,/g, '|') : "dbf";
         return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
      });

      $('#form_import').validate({
         rules: {
            db_file: {
               required: true,
               extension: true
            }
         },
         messages: {
            db_file: {
               required: "Please upload DBF",
               extension: "Invalid File Format! Please upload in DBF Format"
            }
         },
         highlight: function() {
            $('#uploaded_file_details').css("border-bottom", "1px solid red");
         },
         unhighlight: function() {
            $('#uploaded_file_details').css("border-bottom", "1px solid gray");
         },
         errorPlacement: function(error, element) {
            error.insertAfter(element);
         }
      });

      $('#db_file').change(function() {
         $('#db_file-error').hide();
         var file = this.files[0].name;
         $('#uploaded_file_details').val(file);
      });

      $('#submit_btn').click(function () {
         $('#alert-success').hide();
         if($("#form_import").valid()) {
            $('#db_file-error').hide();
            $.confirm({
               title:"<i class='fa fa-info'></i> DATA MIGRATION",
               text: "<span style='color: red; font-size: 14px'><i>Please do backup before proceed.</i></span><br /><br /><span style='color: #000; font-size: 14px'>Process will over-write corresponding GST TABLE in CRM System</span>",
               cancelButton: "No",
               confirm: function(button) {
                  $("#form_import").submit();
               },
               cancel: function(button) {
               }
            });
         } else {
            $("#form_import").validate();
         }
      });     

   });
</script>
