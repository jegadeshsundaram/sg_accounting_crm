
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

   // 2. Start - Add New Supplier - Modal
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

   // 4. Start - Add New Product / Service - Modal
   $(document).on('click', '#lnk_add_billing', function() {
      $("#billing_id").select2('close');

      $('.billing_code_error').hide();
      $('#billing_code').val("");
      $('#billing_description').val("");
      $('#billing_uom').val("");
      $('#billing_price').val("");
      $("#billing_type").val(null).trigger("change");

      $('#addBillingModal').modal();
   });

   $('#addBillingModal').on('shown.bs.modal', function () {
      $('#billing_code').focus();
   });

   $(document).on('click', '#add_product_service_lnk', function() {
      $('.billing_code_error').hide();
      $('#billing_code').val("");
      $('#billing_description').val("");
      $('#billing_uom').val("");
      $('#billing_price').val("");

      $("#billing_type_product_btn").show();
      $("#billing_type_service_btn").show();
      $(".billing_type_change_lnk").hide();

      $("#product_service_ddm").select2('close');

      $('#billingModal').modal('hide');
      $('#addProductServiceModal').modal();
   });

   $('#addProductServiceModal').on('shown.bs.modal', function () {
      $('#stock_code').focus();
   });

   $(document).on("change", "#billing_code", function(e) {
      $.post('/master_files/ajax/double_billing', {
         code: $(this).val()
      }, function(data) {
         if (data == 1) {
            $('.billing_code_error').show();
            $('#btn-modal-billing-save').prop("disabled", true);
         } else {
            $('.billing_code_error').hide();
            $('#btn-modal-billing-save').prop("disabled", false);
         }
      });
   });

   $("#billing_type_product_btn").click(function() {
      $("#billing_type_service_btn").hide();
      $(".billing_type_change_lnk").show();
      $("#billing_type").val("Product");
      
      $('.uom_field').show();
      $('.billing_price_field').show();
   });

   $("#billing_type_service_btn").click(function() {
      $("#billing_type_product_btn").hide();
      $(".billing_type_change_lnk").show();
      $("#billing_type").val("Service");

      $('.uom_field').show();
      $('.billing_price_field').show();
   });

   $(".billing_type_change_lnk").click(function() {
      $("#billing_type_product_btn").show();
      $("#billing_type_service_btn").show();
      $(".billing_type_change_lnk").hide();
      $("#billing_type").val("");
      $("#billing_uom").val("");
      $("#billing_price").val("");

      $('.uom_field').hide();
      $('.billing_price_field').hide();
   });

   $(document).on("change", "#billing_price", function() {
      if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
         var price = parseFloat($(this).val()).toFixed(2);
         $(this).val(price);
      }
   });

   $("#btn-modal-billing-save").click(function() {
      if($('#billing_code').val() == "") {
         $('#billing_code').focus();
      } else if($('#billing_description').val() == "") {
         $('#billing_description').focus();
      } else if($("#billing_type").val() == "") {
         $('#billing_type').select2('open');
      } else if($("#billing_type").val() == "Product" && $('#billing_uom').val() == "") {
         $('#billing_uom').focus();
      } else if($("#billing_type").val() == "Product" && $('#billing_price').val() == "") {
         $('#billing_price').focus();
      } else {
         save_billing();
         $('#addBillingModal').modal('hide');
         $('#billingModal').modal();
      }
   });

   $("#btn-modal-billing-cancel").click(function() {
      $('#addBillingModal').modal('hide');
      $('#billingModal').modal();
   });
   // End - Add New Product / Service - Modal

   // 5. Start - Add Foreign Bank - Modal
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

   function save_billing() {
      var billing_code = $("#billing_code").val();
      var billing_description = $("#billing_description").val();
      var billing_type = $('#billing_type').val();
      var billing_uom = $('#billing_uom').val();
      var billing_price = $('#billing_price').val();  

      if(billing_code !== "" && billing_description !== "" && billing_type !== "") {
         $.post('/master_files/ajax/save_billing', {
            billing_code: billing_code,
            billing_description: billing_description,
            billing_type: billing_type,
            billing_uom: billing_uom,
            billing_price: billing_price
         }, function(data) {
            var obj = $.parseJSON(data);
            if(obj.billing_id !== "") {
               $('#dt_billing').DataTable().ajax.reload();
               $('#dt_billing').DataTable().search(billing_description).draw();
            }
         });
      }
   }

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