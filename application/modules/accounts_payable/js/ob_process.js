$(function() { // document starts
   var row_number = '';

   $('select').select2();   

   // Supplier select
   $("#supplier_id").change(function() {

      var supplier_id = $('option:selected', this).val();
      if(supplier_id == "") {
         return false;
      }

      $("#supplier_currency").val('');
      $("#currency_rate").val('');

      $.post('/accounts_payable/ajax/get_supplier', {
         supplier_id: supplier_id
      }, function(data) {
         var obj = $.parseJSON(data);

         $("#supplier_currency").val(obj.supplier_currency);
         $("#currency_rate").val(obj.currency_rate);

         if(obj.supplier_currency == "SGD") {
            $('.f_curr').html('');
            $('.f_curr, .dv_local').hide();
         } else {
            $('.f_curr').html('('+obj.supplier_currency+')');
            $('.f_curr, .dv_local').show();
         }

         if($('#tbl_items > tbody > tr').length > 0) {
            update_items();
         }

         $('.btn_add').show();

      });
   });

   $(".btn_add").on('click', function() {
      if(!isFormValid()) {
         return false;
      }     

      $('#process').val('add');

      clear_inputs();
      $('#entryModal').modal('show');
   });

   var double_ref = 0;
   $(document).on("change", "#ref_no", function() {
      var current_ref = $(this).val();
      double_ref = 0;

      if(current_ref !== "") {

            $.post('/accounts_payable/ajax/double_ob_ref', {
               supplier_id: $("#supplier_id").val(),
               ref_no: current_ref
            }, function(ref) {
               if(parseInt(ref) == 0) {
                  $(".double_ref").hide();
                  double_ref = 0;

               } else {
                  $(".double_ref").show();
                  double_ref = 1;
               }
            });
         
      } else {
         double_ref == 0;
      }
   });

   $(document).on("keyup", "#foreign_amount", function() {
      if($(this).val() !== "") {
         row_number = '';
         get_local_amount(row_number);
      }
   });

   $(document).on("change", "#foreign_amount, #local_amount", function() {
      var amount = 0;
      if($.trim($(this).val()) !== "") {
         var amount = parseFloat($(this).val()).toFixed(2);
         $(this).val(amount);
      }
   });

   // EDIT
   $(document).on('click', '.dt_edit', function () {

      if(!isFormValid()) {
         return;
      }

      row_number = $(this).closest('tr').attr('id');

      $('#entry_id').val($('#entry_id_'+row_number).val());
      
      if($('#entry_'+row_number).val() == "+") {
         $('#entry_debit').prop("checked", true);
      } else if($('#entry_'+row_number).val() == "-") {
         $('#entry_credit').prop("checked", true);
      }

      $('#doc_date').val($('#doc_date_'+row_number).val());
      $('#ref_no').val($('#ref_no_'+row_number).val());
      $('#foreign_amount').val($('#foreign_amount_'+row_number).val());
      $('#local_amount').val($('#local_amount_'+row_number).val());
      $('#remarks').val($('#remarks_'+row_number).val());

      if($('#supplier_currency').val() == "SGD") {
         $('.f_curr').html('');
         $('.f_curr, .dv_local').hide();
      } else {
         $('.f_curr').html('('+$('#supplier_currency').val()+')');
         $('.f_curr, .dv_local').show();
      }
     
      $('#process').val('edit');
      $('#edit_id').val(row_number);
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
                  $.post('/accounts_payable/ajax/delete_ob_entry', {
                     entry_id: $('#entry_id_'+row_number).val()
                  }, function (status) {
                     if($.trim(status) == 'deleted') {
                        toastr.success("Entry deleted!");
                        $('tr#'+row_number).remove();

                        if($('#tbl_items > tbody > tr').length > 0) {
                           sortTblRowsByID();
                        } else {
                           $('#tbl_items').hide();
                           $('.btn_add').hide();
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

   // add entry to transaction
   $("#btn_save_entry").on('click', function () {

      if(!isFormValid() || !isModalValid()) {
         return false;
      }

      if(double_ref == 1) {
         $('#ref_no').focus();
         return false;
      }
      
      save_entry();
   });

   // submit
   $("#btn_submit").on('click', function () {
      if(!isFormValid() || $('#tbl_items > tbody > tr').length == 0) {
         return false;
      }

      $('#form_').submit();
   });

}); // document ends

function clear_inputs() {
   $('#entry_id').val('');
   $('#edit_id').val('');

   $('#doc_date').val('');
   $('#ref_no').val('');
   $('#foreign_amount').val('');
   $('#local_amount').val('');
}

function isFormValid() {
   var valid = true;
   if($('#supplier_id').val() == "") {
      $("#supplier_id").select2('open');
      valid = false;
   }
   return valid;
}

function isModalValid() {
   var valid = true;
   if($('#doc_date').val() == "") {
      $('#doc_date').focus();
      valid = false;
   } else if($('#ref_no').val() == "") {
      $('#ref_no').focus();
      valid = false;
   } else if($('#foreign_amount').val() == "") {
      $('#foreign_amount').focus();
      valid = false;
   } else if($("#supplier_currency").val() == "SGD" && $('#local_amount').val() == "") {
      $('#local_amount').focus();
      valid = false;
   }
   return valid;
}

function save_entry() {

   // header values
   var supplier_id = $("#supplier_id").val();
   
   // body values
   var ob_id = $("#entry_id").val();
   var entry_type = $("input[name='entry_type']:checked").val();
   
   var doc_date = $("#doc_date").val();
   var ref_no = $("#ref_no").val();
   var foreign_amount = $("#foreign_amount").val();
   var local_amount = $("#local_amount").val();
   var remarks = $("#remarks").val();

   $.post('/accounts_payable/ajax/save_ob_entry', {
      ob_id: ob_id,
      supplier_id: supplier_id,
      doc_date: doc_date,
      ref_no: ref_no,
      foreign_amount: foreign_amount,
      local_amount: local_amount,
      sign: entry_type,
      remarks: remarks
   }, function(entry_id) {
      $("#entry_id").val($.trim(entry_id));

      manage_entry();
   });
}

function manage_entry() {

   if($('#process').val() == 'add') { // New Row
      $row = $("#tbl_clone tbody tr").clone();
   } else if($('#process').val() == "edit") { // Existing Row
      $row = $('tr[id="'+$("#edit_id").val()+'"]');
   }

   $row.find('input.entry_id').val($('#entry_id').val());
   var entry_type = $("input[name='entry_type']:checked").val();
   if(entry_type == "+") {
      $row.find('input.entry_type').val("Debit");
   } else {
      $row.find('input.entry_type').val("Credit");
   }
   $row.find('input.entry').val(entry_type);

   $row.find('input.doc_date').val($('#doc_date').val());
   $row.find('input.ref_no').val($('#ref_no').val());

   $row.find('input.foreign_amount').val($('#foreign_amount').val());
   $row.find('input.local_amount').val($('#local_amount').val());

   $row.find('input.remarks').val($('#remarks').val());
   
   alert($('#supplier_currency').val());
   
   if($('#supplier_currency').val() == "SGD") {
      $('.f_curr').html('');
      $('.f_curr, .dv_local').hide();
   } else {
      $('.f_curr').html('('+$('#supplier_currency').val()+')');
      $('.f_curr, .dv_local').show();
   }

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

function get_local_amount(row_number) {
   var exchange_rate = $("#currency_rate").val();
   var foreign_amount = $('#foreign_amount'+row_number).val();
   
   var local_amount = 0;

   if(exchange_rate !== "" && foreign_amount !== "") {
      local_amount = Number(foreign_amount) / Number(exchange_rate);
      $('#local_amount'+row_number).val(local_amount.toFixed(2));
   } else {
      $('#local_amount'+row_number).val('');
   }
}

function update_items() {
   $("#tbl_items tbody tr").each(function () {
      row_number = '_'+$(this).attr('id');
      get_local_amount(row_number);      
   });
}
