$(function() { // document starts

   var row_number = '';

   $('select').select2();

   var double_ref = 0;
   $(document).on("change", "#ref_no", function(e) {
      
      var ref_no = $(this).val();
      double_ref = 0;

      $("#ref_error").hide();

      if(ref_no !== "") {

         // if page is edit and user try changing different ref and again changing to same one
         if(ref_no == $('#original_ref_no').val()) {
            return false;
         }

         var supplier_id = $("#supplier_id").val();
         if(supplier_id !== '') {
            check_double_ref();
         }
      }
   });

   $("#supplier").change(function(event) {
      var supplier_id = $('option:selected', this).val();
      var ref_no = $("#ref_no").val();
      if(supplier_id !== '' && ref_no !== '') {
         check_double_ref();
      }
   });

   $(".btn_add_item").on('click', function() {
      if(!isFormValid()) {
         return false;
      }     

      $('#process').val('add');

      clear_inputs();
      $('#entryModal').modal('show');
   });

   $(document).on("change", "#product", function() {
      $('.entry_field').hide();
      var current_product_id = $('option:selected',this).val();
      var original_product_id = $('#original_product_id').val();

      if (current_product_id !== "") {
      
         if(original_product_id !== '' && original_product_id == current_product_id) {
            $('.entry_field').show();
            return false;
         }

         var product_exists = false;
         $("#tbl_items").find('.product_id').each(function() {
            if(current_product_id == $(this).val()) {
               product_exists = true;

               $('#entryModal').modal('hide');

               $('#errorAlertModal .modal-title').html("Duplicate Product");
               $('#errorAlertModal .modal-body').html("Selected Product already used. Please choose other product.");
               $('#errorAlertModal').modal();

               $("#product").val("").trigger("change");
            }
         });

         if(!product_exists) {
            $.post('/stock/ajax/get_product_uom', {
               product_id: current_product_id
            }, function(uom) {
               $("#uom").val(uom);
               $('.entry_field').show();
               $("#quantity").focus();
            });
         }
      }
   });

   $('#errorAlertModal').on('hidden.bs.modal', function() {
      $('#entryModal').modal('show');
   });

   $(document).on("change", "#unit_cost", function() {
      var amount = 0;
      if($.trim($(this).val()) !== "") {
         var amount = parseFloat($(this).val()).toFixed(2);
         $(this).val(amount);
      }
   });

   // add entry to transaction
   $("#btn_save_item").on('click', function () {

      if(!isFormValid() || !isModalValid()) {
         return false;
      }

      if(double_ref == 1) {
         $('#ref_no').focus();
         return false;
      }
      
      save_entry();
   });

   // EDIT
   $(document).on('click', '.dt_edit', function () {

      if(!isFormValid()) {
         return;
      }

      row_number = $(this).closest('tr').attr('id');

      $('#purchase_id').val($('#purchase_id_'+row_number).val());
      
      $('#original_product_id').val($('#product_id_'+row_number).val());
      $('#product').select2("destroy");
      $('#product').val($('#product_id_'+row_number).val());
      $('#product').select2();

      $('#uom').val($('#uom_'+row_number).val());
      $('#quantity').val($('#quantity_'+row_number).val());
      $('#unit_cost').val($('#unit_cost_'+row_number).val());
      $('#remarks').val($('#remarks_'+row_number).val());
      
      $('#process').val('edit');
      $('#edit_id').val(row_number);

      $('.entry_field').show();

      $('#entryModal').modal('show');
   });

   // DELETE
   $(document).on('click', '.dt_delete', function () {
      row_number = $(this).closest('tr').attr("id");
      $.confirm({
         title: '<i class="fa fa-info"></i> Confirm Delete',
         content: 'Are you sure to Delete?</strong>',
         buttons: {
            yes: {
               btnClass: 'btn-warning',
               action: function() {
                  $.post('/stock/ajax/delete_purchase_entry', {
                     purchase_id: $('#purchase_id_'+row_number).val()
                  }, function (status) {
                     if($.trim(status) == 'deleted') {
                        toastr.success("Entry deleted!");
                        $('tr#'+row_number).remove();

                        if($('#tbl_items > tbody > tr').length > 0) {
                           sortTblRowsByID();
                        } else {
                           $('#tbl_items').hide();
                        }
                     } else {
                        toastr.error("Post Error!");
                     }
                  });
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

   // submit
   $("#btn_submit").on('click', function () {
      if(!isFormValid() || $('#tbl_items > tbody > tr').length == 0) {
         return false;
      }

      if(double_ref == 1) {
         $('#ref_no').focus();
         return false;
      }

      $('#form_').submit();
   });

}); // document ends

function isFormValid() {
   var valid = true;
   if($('#doc_date').val() == "") {
      $("#doc_date").focus();
      valid = false;
   } else if($('#ref_no').val() == "") {
      $("#ref_no").focus();
      valid = false;
   } else if($('#supplier').val() == "") {
      $("#supplier").select2('open');
      valid = false;
   }
   return valid;
}

function clear_inputs() {
   $('#purchase_id').val('');
   $('#edit_id').val('');
   $('.entry_field').hide();

   $('#product').select2("destroy").val('').select2();
   $('#original_product_id').val('');

   $('#uom').val('');
   $('#quantity').val('');
   $('#unit_cost').val('');
   $('#remarks').val('');
}

function isModalValid() {
   var valid = true;
   if($('#product').val() == "") {
      $("#product").select2('open');
      valid = false;
   } else if($('#quantity').val() == "") {
      $('#quantity').focus();
      valid = false;
   } else if($('#unit_cost').val() == "") {
      $('#unit_cost').focus();
      valid = false;
   }
   return valid;
}

function save_entry() {

   // header values
   var doc_date = $("#doc_date").val();
   var ref_no = $("#ref_no").val();
   var supplier = $("#supplier").val();

   // body values
   var purchase_id = $("#purchase_id").val();
   var product_id = $("#product").val();
   var quantity = $("#quantity").val();
   var unit_cost = $("#unit_cost").val();
   var remarks = $("#remarks").val();

   $.post('/stock/ajax/save_purchase_entry', {
      purchase_id: purchase_id,
      doc_date: doc_date,
      ref_no: ref_no,
      supplier_id: supplier,
      product_id: product_id,
      quantity: quantity,
      unit_cost: unit_cost,
      remarks: remarks
   }, function(purchase_id) {
      $("#purchase_id").val($.trim(purchase_id));

      manage_entry();
   });
}

function manage_entry() {

   if($('#process').val() == 'add') { // New Row
      $row = $("#tbl_clone tbody tr").clone();
   } else if($('#process').val() == "edit") { // Existing Row
      $row = $('tr[id="'+$("#edit_id").val()+'"]');
   }

   $row.find('input.purchase_id').val($('#purchase_id').val());
   
   $row.find('input.product_desc').val($("#product>option:selected").text());
   $row.find('input.product_id').val($("#product").val());

   $row.find('input.uom').val($('#uom').val());
   $row.find('input.quantity').val($('#quantity').val());
   $row.find('input.unit_cost').val($('#unit_cost').val());
   $row.find('input.remarks').val($('#remarks').val());

   if($('#process').val() == "add") {
      // append new row to the table
      $('#tbl_items').append($row);
      sortTblRowsByID();
   }

   $('#tbl_items').show();

   $('#entryModal').modal('hide');
}

function sortTblRowsByID() {
   var row_number = 0;
   var DELIMITER;
   var parts;
   $("#tbl_items tbody tr").each(function () {
      $(this).find('input, select, button, textarea').each(function() {
         var id = $(this).attr('id') || null;

         if(id) {
            DELIMITER = "_";
            parts = id.split(DELIMITER);
            parts[parts.length - 1] = row_number;
            id = parts.join(DELIMITER);
            console.log("ID >>> "+id);
            $(this).attr('id', id);
         }
      });

      $(this).attr('id', row_number);
      row_number = row_number + 1;
   });
}

function check_double_ref() {
   // Double Check Reference in GL and Purchase batch Tables
   $.post('/stock/ajax/double_purchase', {
      ref_no: $("#ref_no").val(),
      supplier_id: $("#supplier_id").val()
   }, function(ref) {
      if (ref > 0) {
         double_ref = 1;
         $("#ref_error").show();
      } else {
         double_ref = 0;
         $("#ref_error").hide();
      }
   });
}
