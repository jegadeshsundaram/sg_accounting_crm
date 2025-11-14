
$(function() {
   
   // 1. Start - Add New Customer - Modal
   $(document).on('click', '#lnk_add_customer', function() {
      $("#customer_id").select2('close');

      $('.cstmr_code_error').hide();
      $('#cstmr_code').val("");
      $('#cstmr_name').val("");
      $("#cstmr_currency").val(null).trigger("change");

      $('#addCustomerModal').modal();
   });

   $('#addCustomerModal').on('shown.bs.modal', function () {
      $('#cstmr_code').focus();
   });

   $(document).on("change", "#cstmr_code", function(e) {
      $.post('/master_files/ajax/double_customer', {
         code: $(this).val()
      }, function(data) {
         if (data == 1) {
            $('.cstmr_code_error').show();
            $('#btn-modal-customer-save').prop("disabled", true);
         } else {
            $('.cstmr_code_error').hide();
            $('#btn-modal-customer-save').prop("disabled", false);
         }
      });
   });

   $("#btn-modal-customer-save").click(function() {
      if($('#cstmr_code').val() == "") {
         $('#cstmr_code').focus();
      } else if($('#cstmr_name').val() == "") {
         $('#cstmr_name').focus();
      } else if($('#cstmr_currency').val() == "") {
         $('#cstmr_currency').select2('open');
      } else {
         save_customer();
         $('#addCustomerModal').modal('hide');
      }
   });

   $("#btn-modal-customer-cancel").click(function() {
      $('#addCustomerModal').modal('hide');
   });
   // End - Add New Customer - Modal

}); // document ends


   function save_customer() {
      var code = $("#cstmr_code").val();
      var name = $("#cstmr_name").val();
      var currency = $("#cstmr_currency").val();

      var input_id;
      if($('#batch_select_entry').val()) {
         input_id = "customer_id_"+ $('#batch_select_entry').val();
      } else {
         input_id = "customer_id";
      }

      if(code !== "" && name !== "" && currency !== "") {
         $.post('/master_files/ajax/save_customer', {
            code: code,
            name: name,
            currency_id: currency
         }, function(data) {
            var obj = $.parseJSON(data);
            if(obj.customer_id !== "") {
               $('<option value="'+obj.customer_id+'">' + name + ' ( ' + code + ') '+obj.currency+'</option>').appendTo("#"+input_id);

               $("#"+input_id+" option").not(':first').sort(function(a, b) {
                  return a.text > b.text;
               }).appendTo("#"+input_id);

               $("#"+input_id).val(obj.customer_id).change();
            }
         });
      }
   }