<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Receipt</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Receipt</li>
               <li class="breadcrumb-item active">Options</li>
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
            <input type="hidden" id="module" value="receipt" />
            <div class="card card-default">
               <div class="card-header options">
                  <h5>Options</h5>
               </div>
               <div class="card-body opt-lnk">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#settingsModal">
                           Settings <span>Settings of Receipt Template</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/receipt/create">
                           Create <span>Create Receipt for any customer</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/receipt/listing">
                           Listing <span>Confirmed / Posted / Deleted Receipts</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#utilityModal">
                           Utilities > Datafiles<span>Backup / Restore / Zap of Receipt Datafile's</span>
                        </a>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <a href="/dashboard" class="btn btn-warning btn-sm float-right" style="font-size: 1rem;">
                     <i class="fa-solid fa-angles-left"></i> Dashboard
                  </a>
               </div>
            </div>
         </div>
      </div>

   </div> <!-- container-fluid - ends -->
</div>

<div id="settingsModal" class="modal fade" data-backdrop="static">
   <div class="modal-dialog modal-confirm">
      <div class="modal-content">
         <div class="card card-default" style="margin-bottom: 0rem;">
            <div class="card-header">
               <span style="margin: 0; display: block;">SETTINGS</span>
               <button type="button" class="btn btn-outline-dark btn-sm float-right" style="position: absolute; top: 15px; right: 20px;" data-toggle="modal" data-target="#settingsModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="card-body">
               <div class="row" style="margin-bottom: 15px">
                  <div class="col-6">
                     <div class="row">
                        <label for="text_prefix" class="col-12 control-label float-right">Text Prefix <span class="req">*</span><span class="txt">{Max: 4 Characters}</span></label>
                        <div class="col-12">
                           <input type="text" id="text_prefix" maxlength="4" class="form-control w-120" />
                        </div>
                     </div>
                  </div>
                  <div class="col-6">
                     <div class="row">
                        <label for="number_suffix" class="col-12 control-label float-right">Number Suffix <span class="req">*</span><span class="txt">{Max: 6 digits}</span></label>
                        <div class="col-12">
                           <input type="number" id="number_suffix" class="form-control w-120" onkeypress="if(this.value.length==6) return false;" />
                           <span class="suffix_error error" style="display: none;"></span>
                        </div>
                     </div>
                  </div>
               </div>               
            </div>
            <div class="card-footer">
               <a class="btn btn-info btn-sm" data-toggle="modal" data-target="#settingsModal">CANCEL</a>
               <button type="button" name="btn_submit" id="btn_submit" class="btn btn-warning btn-sm float-right">SUBMIT</button>
            </div>
         </div>
      </div>
   </div>
</div>

<?php require_once APPPATH.'/modules/includes/modal/utility.php'; ?>
<script src="/assets/js/modal/utility.js"></script>

<script type="text/javascript">

   var same_reference_exists = 0;
   $(function() {

      $('#settingsModal').on('shown.bs.modal', function() {
         $.post('/receipt/ajax/get_settings', {
         }, function (settings) {
            var obj = $.parseJSON(settings);

            $('#text_prefix').val(obj.settings['text_prefix']);
            $('#number_suffix').val(obj.settings['number_suffix']);

            if($('#text_prefix').val() == "") {
               $('#text_prefix').focus();
            }
         });
      });

      $(document).on("change", "#text_prefix, #number_suffix", function(e) {
         var text_prefix = $('#text_prefix').val();
         var number_suffix = $('#number_suffix').val();
         $.post('/receipt/ajax/double_receipt', {
            text_prefix: text_prefix, 
            number_suffix: number_suffix
         }, function(data) {
            if (data == 1) {
               same_reference_exists = 1;
               $('.suffix_error').text('Receipt reference ' + text_prefix + '.' + (parseInt(number_suffix) + 1) +' is already in the system, please change suffix number.');
               $('.suffix_error').show();
            } else {
               same_reference_exists = 0;
               $('.suffix_error').text('');
               $('.suffix_error').hide();
            }
         });
      });

      $('#btn_submit').click(function() {
         if(same_reference_exists == 0 && isFormValid()) {
            $.post('/receipt/ajax/save_settings', {
               text_prefix: $('#text_prefix').val(),
               number_suffix: $('#number_suffix').val()
            }, function(res) {
               toastr.success(res);
               $('#settingsModal').modal('hide');
            });
         }
      });
   }); // document ends

   function isFormValid() {
      var valid = true;
      if($('#text_prefix').val() == "") {
         $('#text_prefix').focus();
         valid = false;
      } else if($('#number_suffix').val() == "") {
         $('#number_suffix').focus();
         valid = false;
      }

      return valid;
   }
</script>