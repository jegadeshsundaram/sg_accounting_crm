<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Restore Petty Cash</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
              <li class="breadcrumb-item active">Petty Cash</li>
              <li class="breadcrumb-item active">Restore</li>
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
            <form id="form_" method="post" enctype="multipart/form-data" action="<?php echo $save_url; ?>">
               <div class="card card-default">

                  <div class="card-header">
                     SQL File Upload
                  </div>

                  <div class="card-body">

                     <div class="form-group row" style="margin-top: 30px; margin-bottom: 30px">
                        <div class="col-md-4">
                           <label id="upload-btn" for="db_file" class="btn btn-secondary btn-block">Upload File (.sql)</label>
                        </div>
                        <div class="col-md-8">
                           <input type="text" readonly="true" placeholder="Uploaded file name will display here" name="uploaded_file_details" id="uploaded_file_details" style="line-height: 38px; width: 100%;" />
                           <input class="form-control inputfile ignore" name="db_file" id="db_file" type="file" />
                        </div>
                     </div>
                  </div>

                  <div class="card-footer">
                     <a href="/petty_cash/df_options" class="btn btn-info">Cancel</a>
                     <button type="button" id="btn_submit" class="btn btn-warning float-right">Submit</button>
                  </div>

               </div>
            </form>
         </div>
      </div>

   </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<style>
   ::placeholder {
      color: gray;
      font-size: 0.9rem;
      font-style: italic;
   }

   .card-header {
      font-size: 1.2rem;
      color: dimgray;
      font-style: italic;
      font-variant: petite-caps;
      font-weight: 505;
      letter-spacing: 1px;
   }

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

   input {
      outline: 0;
      border-width: 0 0 1px;
      border-color: dimgray;
      color: dimgray;
   }
   input:focus {
      outline: 0;
      border-width: 0 0 1px;
      border-color: lightgray
   }

</style>
<script type="text/javascript">
   $(document).ready(function() {
      $.validator.addMethod("extension", function (value, element, param) {
         param = typeof param === "string" ? param.replace(/,/g, '|') : "sql";
         return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
      });

      $('#form_').validate({
         rules: {
            db_file: {
               required: true,
               extension: true
            }
         },
         messages: {
            db_file: {
               required: "Please upload SQL",
               extension: "Invalid File Format! Please upload in SQL Format"
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

      $('#btn_submit').click(function() {
         if($('#form_').valid()) {
            $('#db_file-error').hide();
            $('#confirmSubmitModal .modal-title').html("<i class='fa fa-info'></i> Restore");
            $('#confirmSubmitModal .modal-body').html("Please do backup before proceed to restore.<br />Restore will overwrite Petty Cash data files in CRM System");
            $("#confirmSubmitModal").modal();
         }
      });

      $('#btn-confirm-yes').click(function() {
         $("#confirmSubmitModal").modal('hide');
         $('#form_').submit();
      });

   });
</script>
