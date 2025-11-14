
$(function() {
  
   // Start - Add New Supplier - Modal
   $(document).on('click', '#add_supplier_lnk', function() {
      $("#supplier_id").select2('close');

      $('.supplier_code_error').hide();
      $('#supplier_code').val("");
      $('#supplier_name').val("");
      $("#supplier_cstmr_currency").val(null).trigger("change");

      $('#addSupplierModal').modal();
   });

   $(document).on("change", "#supplier_code", function(e) {
      $.post('/master_files/ajax/double_supplier', {
         code: $(this).val()
      }, function(data) {
         if (data == 1) {
            $('.supplier_code_error').show();
            $('#btn-modal-supplier-save').prop("disabled", true);
         } else {
            $('.supplier_code_error').hide();
            $('#btn-modal-supplier-save').prop("disabled", false);
         }
      });
   });

   $("#btn-modal-supplier-save").click(function() {
      if($('#supplier_code').val() == "") {
         $('#supplier_code').focus();
      } else if($('#supplier_name').val() == "") {
         $('#supplier_name').focus();
      } else if($('#supplier_cstmr_currency').val() == "") {
         $('#supplier_cstmr_currency').select2('open');
      } else {
         save_supplier();
         $('#addSupplierModal').modal('hide');
      }
   });

   $("#btn-modal-supplier-cancel").click(function() {
      $('#addSupplierModal').modal('hide');
   });
   // End - Add New Supplier - Modal

}); // document ends

   function save_supplier() {
      var supplier_code = $("#supplier_code").val();
      var supplier_name = $("#supplier_name").val();
      var cstmr_currency = $("#supplier_cstmr_currency").val();

      var input_id;
      if($('#batch_select_entry').val()) {
         input_id = "supplier_id_"+ $('#batch_select_entry').val();
      } else {
         input_id = "supplier_id";
      }

      if(supplier_code !== "" && supplier_name !== "" && cstmr_currency !== "") {
         $.post('/master_files/ajax/save_supplier', {
            supplier_code: supplier_code,
            supplier_name: supplier_name,
            cstmr_currency: cstmr_currency
         }, function(data) {
            var obj = $.parseJSON(data);
            if(obj.supplier_id !== "") {
               var current_entry = $('#batch_select_entry').val();
               $('<option value="'+obj.supplier_id+'">' + supplier_name + ' ( ' + supplier_code + ') '+obj.currency_code+'</option>').appendTo("#"+input_id);

               $("#"+input_id+" option").not(':first').sort(function(a, b) {
                  return a.text > b.text;
               }).appendTo("#"+input_id);

               //$("#"+input_id).select2('open');

               $("#"+input_id).val(obj.supplier_id).change();
            }
         });
      }
   }