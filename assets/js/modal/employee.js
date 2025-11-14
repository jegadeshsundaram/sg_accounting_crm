
$(function() {  

   // Start - Add New Employee - Modal  
   $(document).on('click', '#lnk_add_employee', function() {
      $("#employee_id").select2('close');

      $('.employee_code_error').hide();
      $('#employee_code').val("");
      $('#employee_name').val("");
      $("#department_id").val(null).trigger("change");
      
      $('#addEmployeeModal').modal();
   });

   $('#addEmployeeModal').on('shown.bs.modal', function () {
      $('#employee_code').focus();
   });

   $(document).on("change", "#employee_code", function(e) {
      $.post('/master_files/ajax/double_employee', {
         code: $(this).val()
      }, function(data) {
         if (data == 1) {
            $('.employee_code_error').show();
            $('#btn-modal-employee-save').prop("disabled", true);
         } else {
            $('.employee_code_error').hide();
            $('#btn-modal-employee-save').prop("disabled", false);
         }
      });
   });

   $("#btn-modal-employee-save").click(function() {
      if($('#employee_code').val() == "") {
         $('#employee_code').focus();
      } else if($('#employee_name').val() == "") {
         $('#employee_name').focus();
      } else if($('#department_id').val() == "") {
         $('#department_id').select2('open');
      } else {
         save_employee();
         $('#addEmployeeModal').modal('hide');
      }
   });

   $("#btn-modal-employee-cancel").click(function() {
      $('#addEmployeeModal').modal('hide');
   });
   // End - Add New Employee - Modal

}); // document ends

   function save_employee() {
      var employee_code = $("#employee_code").val();
      var employee_name = $("#employee_name").val();
      var department_id = $("#department_id").val();

      if(employee_code !== "" && employee_name !== "" && department_id !== "") {
         $.post('/master_files/ajax/save_employee', {
            employee_code: employee_code,
            employee_name: employee_name,
            department_id: department_id
         }, function(data) {
            var obj = $.parseJSON(data);
            if(obj.employee_id !== "") {
               $('<option value="'+obj.employee_id+'">' + employee_name + ' ( ' + employee_code + ')</option>').appendTo("#employee_id");

               $("#employee_id option").not(':first').sort(function(a, b) {
                  return a.text > b.text;
               }).appendTo('#employee_id');

               //$('#employee_id').select2('open');

               $('#employee_id').val(obj.employee_id).change();
            }
         });
      }
   }