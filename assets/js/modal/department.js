
$(function() {

   $('#department_id')
      .select2()
      .on('select2:open', () => {
         $(".select2-results:not(:has(a))").append('<a id="lnk_add_department" class="add_lnk" title="Add Department"><i class="fa fa-plus"></i> New Department</a>');
      });

   // Start - Add New Deparment - Modal
   $(document).on('click', '#lnk_add_department', function() {
      $("#department_id").select2('close');

      $('#department_code').val("");
      $('#department_name').val("");
      $('#addEmployeeModal').modal('hide');

      $('#addDepartmentModal').modal();
   });

   $('#addDepartmentModal').on('shown.bs.modal', function () {
      $('#department_code').focus();
   });

   $(document).on("change", "#department_code", function(e) {
      $.post('/master_files/ajax/double_department', {
         code: $(this).val()
      }, function(data) {
         if (data == 1) {
            $('.department_code_error').show();
            $('#btn-modal-department-save').prop("disabled", true);
         } else {
            $('.department_code_error').hide();
            $('#btn-modal-department-save').prop("disabled", false);
         }
      });
   });

   $("#btn-modal-department-save").click(function() {
      if($('#department_code').val() == "") {
         $('#department_code').focus();
      } else if($('#department_name').val() == "") {
         $('#department_name').focus();
      } else {
         save_department();
         $('#addDepartmentModal').modal('hide');
         $('#addEmployeeModal').modal();
      }
   });

   $("#btn-modal-department-cancel").click(function() {
      $('#addDepartmentModal').modal('hide');
      $('#addEmployeeModal').modal();
   });
   // End - Add New Deparment - Modal   

}); // document ends
  
   function save_department() {
      var code = $("#department_code").val();
      var name = $("#department_name").val();   

      if(code !== "" && name !== "") {

         $.post('/master_files/ajax/save_department', {
            code: code,
            name: name
         }, function(data) {
            var obj = $.parseJSON(data);
            if(obj.department_id !== "") {
               $('<option value="'+obj.department_id+'">' + name + ' ( ' + code + ' )</option>').appendTo("#department_id");  

               $("#department_id option").not(':first').sort(function(a, b) {
                  return a.text > b.text;
               }).appendTo('#department_id');

               $("#department_id").val(obj.department_id).change();
            }
         });
      }
   }