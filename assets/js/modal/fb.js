
$(function() {

   // Start - Add Foreign Bank - Modal
   $(document).on('click', '#add_fb_lnk', function() {
      $("#fb_id").select2('close');

      $('.fb_code_error').hide();
      $('#fb_code').val("");
      $('#fb_name').val("");
      $("#cstmr_currency").val(null).trigger("change");
      
      $('#addForeignBankModal').modal();
   });

   $(document).on("change", "#fb_code", function(e) {
      $.post('/master_files/ajax/double_fb', {
         code: $(this).val()
      }, function(data) {
         if (data == 1) {
            $('.fb_code_error').show();
            $('#btn-modal-fb-save').prop("disabled", true);
         } else {
            $('.fb_code_error').hide();
            $('#btn-modal-fb-save').prop("disabled", false);
         }
      });
   });

   $("#btn-modal-fb-save").click(function() {
      if($('#fb_code').val() == "") {
         $('#fb_code').focus();
      } else if($('#fb_name').val() == "") {
         $('#fb_name').focus();
      } else if($('#cstmr_currency').val() == "") {
         $('#cstmr_currency').select2('open');
      } else {
         save_foreign_bank();
         $('#addForeignBankModal').modal('hide');
      }
   });

   $("#btn-modal-fb-cancel").click(function() {
      $('#addForeignBankModal').modal('hide');
   });
   // End - Add Foreign Bank - Modal

}); // document ends

   function save_foreign_bank() {
      var fb_code = $("#fb_code").val();
      var fb_name = $("#fb_name").val();
      var cstmr_currency = $("#cstmr_currency").val();

      var input_id;
      if($('#batch_select_entry').val()) {
         input_id = "fb_id_"+ $('#batch_select_entry').val();
      } else {
         input_id = "fb_id";
      }

      if(fb_code !== "" && fb_name !== "" && cstmr_currency !== "") {
         $.post('/master_files/ajax/save_foreign_bank', {
            fb_code: fb_code,
            fb_name: fb_name,
            cstmr_currency: cstmr_currency
         }, function(data) {
            var obj = $.parseJSON(data);
            if(obj.fb_id !== "") {
               $('<option value="'+obj.fb_id+'">' + fb_name + ' ( ' + fb_code + ') '+obj.currency_code+'</option>').appendTo("#"+input_id);

               $("#"+input_id+" option").not(':first').sort(function(a, b) {
                  return a.text > b.text;
               }).appendTo("#"+input_id);
               $("#"+input_id).select2('open');

               $("#"+input_id).val(obj.input_id).change();

            }
         });
      }
   }