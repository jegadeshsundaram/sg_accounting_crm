<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0">Petty Cash</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item">Petty cash</li>
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
            <input type="hidden" id="module" value="petty_cash" />
            <div class="card card-default">
               <div class="card-header options">
                  <h5>Options</h5>
               </div>
               <div class="card-body opt-lnk">
                  <div class="row">
                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#settingsModal">
                           Settings <span>Settings of Petty Cash Template</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a href="/petty_cash/listing">
                           Manage Vouchers <span>Issue / Manage Petty cash voucher's</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a id="btn_audit_trail">
                           Audit Trail <span>Audit trail of petty cash accounts</span>
                        </a>
                     </div>

                     <div class="col-xl-4 col-md-6">
                        <a data-toggle="modal" data-target="#utilityModal">
                           Utilities > Datafiles<span>Backup / Restore / Zap of Petty cash Datafile's</span>
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script type="text/javascript">
   var same_reference_exists = 0;
   $(function() {

      $('#settingsModal').on('shown.bs.modal', function() {
         $.post('/petty_cash/ajax/get_settings', {
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
         $.post('/petty_cash/ajax/duplicate', {
            text_prefix: text_prefix, 
            number_suffix: number_suffix
         }, function(data) {
            if (data == 1) {
               same_reference_exists = 1;
               $('.suffix_error').text('Petty Cash reference ' + text_prefix + '.' + (parseInt(number_suffix) + 1) +' is already in the system, please change suffix number.');
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
            $.post('/petty_cash/ajax/save_settings', {
               text_prefix: $('#text_prefix').val(),
               number_suffix: $('#number_suffix').val()
            }, function(res) {
               toastr.success(res);
               $('#settingsModal').modal('hide');
            });
         }
      });

      $("#btn_audit_trail").on('click', function() {
         $.confirm({
            title: '<i class="fa fa-info"></i> Confirm Print',
            content: 'Are you sure to print audit?',
            buttons: {
               yes: {
                  btnClass: 'btn-warning',
                  action: function(){
                     window.location.href="/petty_cash/print_audit"
                  }
               },
               no: {
                  btnClass: 'btn-dark',
                  action: function(){
                  }
               },
            }
         });
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