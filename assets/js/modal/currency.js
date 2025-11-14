$(function() {

   $('#currency_id')
      .select2()
      .on('select2:open', () => {
         $(".select2-results:not(:has(a))").append('<a id="lnk_add_currency" class="add_lnk" title="Add Currency"><i class="fa fa-plus"></i> New Currency</a>');
      });
      
   // Add Currency - Start 
   $(document).on('click', '#lnk_add_currency', function() {
      $("#currency_id").select2('close');

      $('.currency_error').hide();
      $('#currency').val("");
      $('#description').val("");
      $('#rate').val("");

      $('#addCurrencyModal').modal();
   });

   $('#addCurrencyModal').on('shown.bs.modal', function () {
      $('#currency').focus();
   });

   $(document).on("keyup", "#currency", function(e) {
      $.post('/combo_tables/ajax/double_currency', {
         code: $(this).val()
      }, function(data) {
         if (data == 1) {
            $('.currency_error').show();
            $('#btn-currency-modal-save').prop("disabled", true);
         } else {
            $('.currency_error').hide();
            $('#btn-currency-modal-save').prop("disabled", false);
         }
      });
   });

   $(document).on("change", "#rate", function(e) {
      if($(this).val() !== "") {
         $(this).val(Number($(this).val()).toFixed(5));
      }
   });

   $('#btn-currency-modal-save').click(function() {
      if($('#frm_currency').valid()) {
         save_currency();
         $('#addCurrencyModal').modal('hide');
      }
   });

   $('#btn-modal-cancel').click(function() {
      $('#addCurrencyModal').modal('hide');
   });
   // Add Currency - End

}); // document ends


function save_currency() {
   var code = $('#currency').val();
   var description = $('#description').val();
   var rate = $('#rate').val();

   if(code !== "" && description !== "" && rate !== "") {

      $.post('/master_files/ajax/save_currency', {
         code: code,
         description: description,
         rate: rate
      }, function(data) {
      var obj = $.parseJSON(data);
         if(obj.currency_id !== "") {
            $('<option value="'+obj.currency_id+'">' + code + ' : ' + description + ' (Rate: ' + rate + ')</option>').appendTo("#currency_id");  

            $('#currency_id option').not(':first').sort(function(a, b) {
               return a.text > b.text;
            }).appendTo('#currency_id');

            $("#currency_id").val(obj.currency_id).change();
         }
      });
   }
}