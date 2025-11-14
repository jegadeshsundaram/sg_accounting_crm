
$(function() {
   
   // Start - Add New Product / Service - Modal
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

}); // document ends

   function save_billing() {
      var billing_code = $("#billing_code").val();
      var billing_description = $("#billing_description").val();
      var billing_type = $('#billing_type').val();
      var billing_uom = $('#billing_uom').val();
      var billing_price = $('#billing_price').val();  

      if(billing_code !== "" && billing_description !== "" && billing_type !== "") {
         $.post('/quotation/ajax/save_billing', {
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