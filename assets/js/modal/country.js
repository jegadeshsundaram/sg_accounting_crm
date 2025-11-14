$(function() {
   
   $('#country_id')
      .select2()
      .on('select2:open', () => {
         $(".select2-results:not(:has(a))").append('<a id="lnk_add_country" class="add_lnk" title="Add Country"><i class="fa fa-plus"></i> New Country</a>');
      });
      
   // Add Country - Start 
   $(document).on('click', '#lnk_add_country', function() {
      $("#country_id").select2('close');

      $('.country_code_error').hide();
      $('#country_code').val("");
      $('#country_name').val("");

      $('#addCountryModal').modal();
   });

   $('#addCountryModal').on('shown.bs.modal', function () {
      $('#country_code').focus();
   });

   $(document).on("keyup", "#country_code", function(e) {
      $.post('/combo_tables/ajax/double_country', {
         country_code: $(this).val()
      }, function(data) {
         if (data == 1) {
            $('.country_code_error').show();
            $('#btn-country-modal-save').prop("disabled", true);
         } else {
            $('.country_code_error').hide();
            $('#btn-country-modal-save').prop("disabled", false);
         }
      });
   });

   $('#btn-country-modal-save').click(function() {
      if($('#country_code').val() == "") {
         $('#country_code').focus();
      } else if($('#country_name').val() == "") {
         $('#country_name').focus();      
      } else {
         save_country();
         $('#addCountryModal').modal('hide');
      }
   });     
   // Add Currency - End 

}); // document ends

function save_country() {
   var country_code = $('#country_code').val();
   var country_name = $('#country_name').val();

   if(country_code !== "" && country_name !== "") {
      $.post('/master_files/ajax/save_country', {
         country_code: country_code,
         country_name: country_name
      }, function(data) {
         var obj = $.parseJSON(data);
         if(obj.country_id !== "") {
            $('<option value="'+obj.country_id+'">' + country_name + '</option>').appendTo("#country_id");  

            $('#country_id option').not(':first').sort(function(a, b) {
               return a.text > b.text;
            }).appendTo('#country_id');

            $("#country_id").val(obj.country_id).change();
         }
      });
   }
}