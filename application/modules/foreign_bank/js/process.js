var row_number;
$(function() { // document starts

   var page = $('#page').val();
   console.log(":: Page :: "+page);
   
   $('select').select2();  
   
   // New Page
   // show prompt on exit - start
   $('.main-sidebar a').click(function(e) {
      var items = $('#tbl_items tbody tr').length;
      if(page == "new" && items > 0) {
         e.preventDefault();
         $.confirm({
            title: '<i class="fa fa-info"></i> Exit Foreign Bank',
            content: 'Insufficient data to submit',
            buttons: {
               continue: {
                  btnClass: 'btn-dark',
                  action: function(){
                  }
               },
               abort: {
                  btnClass: 'btn-warning',
                  action: function(){
                     window.location = "/foreign_bank/ob";
                  }
               }
            }
         });
      }
   });
   // show prompt on exit - end  
   

   // bank select
   $(document).on("change", ".bank", function() {
      row_number = $(this).closest('tr').attr("id");
      code = $('option:selected', this).val();
      if (code !== "") {
         $.post('/foreign_bank/ajax/get_bank_data', {
            code: code
         }, function(data) {
            var obj = $.parseJSON(data);
            $('#currency_'+row_number).val(obj.currency);
            $('#currency_rate_'+row_number).val(obj.currency_rate);

            if(obj.currency == "SGD") {
               $("#local_amount_"+row_number).attr("readonly", true);
            } else {
               $("#local_amount_"+row_number).attr("readonly", false);
            }

            $('#document_date_'+row_number).focus();
         });
      }
   });

   $(document).on("change", ".document_date", function() {
      if($(this).val() !== "") {
         $('#ui-datepicker-div').hide();
         row_number = $(this).closest('tr').attr("id");
         save_entry(row_number);
      }
   });

   
   $(document).on("change", ".document_reference", function() {
      if($(this).val() !== "") {
         row_number = $(this).closest('tr').attr("id");
         save_entry(row_number);
      }
   });
   
   // This is commented since David requested that "User is allowed to enter opening balance for 2 or more foreign banks under same reference on Feb 08, 2021"
   var same_reference_exists = false;
   $(document).on("change", ".ref-no_NOT_USED", function() {
      var current_ref = $(this).val();
      row_number = $(this).closest('tr').attr("id");
      same_reference_exists = false;

      if(current_ref !== "") {
         hide_error_alert($(this));

         // Double Check reference with other transactions reference
         $('.ref-no').each(function() {
            var other_row_number = $(this).closest('tr').attr("id");
            if(row_number !== other_row_number) {
               if(current_ref.toLowerCase() == $(this).val().toLowerCase()) {
                  console.log(">>>>> Internal Reference Exists >>>> ");
                  same_reference_exists = true;
                  $("#doc_ref_no\\["+row_number+"\\]").closest('td').find('.error-ref').css("display", "block");
               } else {
                  console.log(">>>>> Internal Reference NOT Exists >>>> ");
                  same_reference_exists = false;
                  $("#doc_ref_no\\["+row_number+"\\]").closest('td').find('.error-ref').css("display", "none");
               }
            } else {
               console.log(">>>>> NO Reference Check >>>> ");
            }
         });

         if(!same_reference_exists) {
            // Double Check Reference in GL and Sales batch Tables
            $.post('/foreign_bank/foreign_bank_ajax/validate_doc_reference', {
               ref_id: current_ref
            }, function(data) {
               if (data == 1) {
                  console.log(">>>>> External Reference Exists >>>> ");
                  same_reference_exists = true;
                  $("#doc_ref_no\\["+row_number+"\\]").closest('td').find('.error-ref').css("display", "block");
               } else {
                  console.log(">>>>> External Reference NOT Exists >>>> ");
                  same_reference_exists = false;
                  $("#doc_ref_no\\["+row_number+"\\]").closest('td').find('.error-ref').css("display", "none");

                  save_entry(row_number);
               }
            });
         }
      } else {
         same_reference_exists = false;
         $(this).closest('td').find('.error-ref').css("display", "none");
      }
   });

   $(document).on("keyup", ".foreign_amount", function() {
      row_number = $(this).closest('tr').attr("id");
      if($.trim($(this).val()) !== "") {
         var currency_rate = $("#currency_rate_"+row_number).val();
         var local_amount = 0;
         var foreign_amount = $(this).val();

         if(currency_rate !== "") {
            local_amount = Number(foreign_amount) / Number(currency_rate);
            $("#local_amount_"+row_number).val(local_amount.toFixed(2));
         }
      } else {
         $(this).val("");
      }
   });

   $(document).on("change", ".foreign_amount, .local_amount", function() {
      var amount = 0;
      row_number = $(this).closest('tr').attr("id");
      if($.trim($(this).val()) !== "") {
         amount = parseFloat($(this).val()).toFixed(2);
         $(this).val(amount);

         save_entry(row_number);
      }
   });

   $(document).on("change", ".remarks", function() {
      if($(this).val() !== "") {
         row_number = $(this).closest('tr').attr("id");
         save_entry(row_number);
      }
   });

   $(document).on("change", ".sign", function() {
      row_number = $(this).closest('tr').attr("id");
      sign = $('option:selected', this).val();
      if (sign !== "") {
         save_entry(row_number);
      }
   });

   // delete item
   var delete_row_id = -1;
   $(document).on('click', '.btn_delete_row', function() {

      var rowCount = $('#tbl_items tbody tr').length;

      if(rowCount == 1) {
         alert("First row can not be deleted");
         return;
      }

      delete_row_id = $(this).closest('tr').attr("id");
      $('#confirmDeleteModal .modal-body').html("Click 'Yes' to delete the current item");
      $("#confirmDeleteModal").modal();
   });

   // item delete = YES
   $('#btn-confirm-delete-yes').click(function() {

      var delete_ob_id = $("#ar_ob_id_"+delete_row_id).val();
      if(delete_ob_id !== "") {
         delete_entry(delete_ob_id);
      }         

      $('tr#'+delete_row_id).remove();

      $("#confirmDeleteModal").modal('hide');
   });

   // btn - add new item 
   $(document).on('click', '.btn_add_item', function() {
      $row_valid = true;
      
      $("#tbl_items tbody tr").each(function (i, val) {
         $row_number = $(this).attr("id");

         $bank = $("#bank_"+$row_number).val();         
         $document_date = $("#document_date_"+$row_number).val();
         $document_refrence = $("#document_refrence_"+$row_number).val();
         $foreign_amount = $("#foreign_amount_"+$row_number).val();
         $local_amount = $("#local_amount_"+$row_number).val();
         $sign = $("#sign_"+$row_number).val();

         if($bank == "") {
            $("#bank_"+$row_number).select2('open');
            $row_valid = false;
         } else if($document_date == "") {
            $("#document_date_"+$row_number).focus();
            $row_valid = false;
         } else if($document_refrence == "") {
            $("#document_refrence_"+$row_number).focus();
            $row_valid = false;
         } else if($foreign_amount == "") {
            $("#foreign_amount_"+$row_number).focus();
            $row_valid = false;
         } else if($sign == "") {
            $("#sign_"+$row_number).select2('open');
            $row_valid = false;
         }
      });
      

      if(!$row_valid) {
         return;
      }

      $tr = $(this).closest('table').find('tbody tr:last');
      var allTrs = $tr.closest('table').find('tbody tr');
      var lastTr = allTrs[allTrs.length-1];
      $(lastTr).find('select').select2("destroy"); // remove select 2 before clone tr
      $new_row = $(lastTr).clone();

      // Input & Select fields id attribute value increment
      $new_row.find('input, select, button, textarea').each(function() {
         var id = $(this).attr('id') || null;

         if(id) {
            // Get id number from ID text
            // Ex: unit_price_123 :: last_record_number = 123
            var last_record_number = id.split("_").pop();
            console.log(">>> last_record_number" + last_record_number);
            var prefix = id.substr(0, (id.length-(last_record_number.length)));
            $(this).attr('id', prefix+(+last_record_number+1));
         }
      });

      // Datepicker
      $new_row.find("input.document_date")
      .removeClass('hasDatepicker')
      .removeData('datepicker')
      .unbind()
      .datepicker({
         changeMonth: true,
         changeYear: true,
         dateFormat: 'dd-mm-yy',
         yearRange: '-3:+1'
      });

      // empty all the input values in new row
      $new_row.find('input').val('');
      $new_row.find('select').val('');

      // Table row Id number increment
      $last_no = $new_row.attr('id');
      $new_row.attr('id', parseInt($last_no) + 1);

      // append new row to the table
      $tr.closest('table').append($new_row);

      // add select2 again
      $(lastTr).find('select').select2();
      $new_row.find('select').select2();

      // set current row number to public variable
      processing_row_number = $new_row.attr('id');

      // Handler for .ready() called.
      $('html, body').animate({
         scrollTop: $('#'+processing_row_number).offset().top
      }, 'slow');
   });

   // submit
   $("#btn_submit").on('click', function (e) {

      if(!$('#form_').valid()) {
         return;
      }

      if(!validate()) {
         return;
      }

      $.confirm({
         title: '<i class="fa fa-info"></i> Confirm Submit',
         content: 'Are you sure to Submit ?',
         buttons: {
            yes: {
               btnClass: 'btn-warning',
               action: function(){
                  $('#form_').submit();
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

}); // document ends

function save_entry(row_number) {
   var ob_id = $("#ob_id_"+ row_number).val();
   var bank = $("#bank_"+ row_number).val();   
   var document_date = $("#document_date_"+ row_number).val();
   var document_reference = $("#document_reference_"+ row_number).val();
   var foreign_amount = $("#foreign_amount_"+ row_number).val();
   var local_amount = $("#local_amount_"+ row_number).val();   
   var sign = $("#sign_"+ row_number).val();
   var remarks = $("#remarks_"+ row_number).val();

   console.log("ob_id >> "+ob_id);

   if(bank !== "" && document_date !== "" && document_reference !== "" && foreign_amount !== "" && local_amount !== "") {

      $.post('/foreign_bank/ajax/save_ob', {
         ob_id: ob_id,
         bank: bank,
         document_date: document_date,
         document_reference: document_reference,
         remarks: remarks,
         foreign_amount: foreign_amount,
         local_amount: local_amount,
         sign: sign
      }, function(data) {
         var obj = $.parseJSON(data);
         if(obj.ob_id !== "") {
            $("#ob_id_"+ row_number).val(obj.ob_id);
         }
      });
   }
}

function delete_entry(ob_id) {
   $.post('/foreign_bank/ajax/delete_ob_item', {
      ob_id: ob_id
   }, function(data) {
      var obj = $.parseJSON(data);
      if(obj.deleted !== "") {
         console.log("auto saved ob deleted. ID:: "+ob_id);
      }
   });
}

function validate() {
   var error = [];
   var i = 0;
   var valid = true;
   var row_number = 0;

   $("#tbl_items tbody tr").each(function (i, val) {
      row_number = $(this).attr("id");

      if($("#sign_"+row_number).val() == "") {
         error[i++] = "Sign";
         valid = false;
      }

      if($("#document_date_"+row_number).val() == "") {
         error[i++] = "Document Date";
         valid = false;
      }

      if($("#document_reference_"+row_number).val() == "") {
         error[i++] = "Document Reference";
         valid = false;
      }
      
      if($("#foreign_amount_"+row_number).val() == "") {
         error[i++] = "Foreign Amount";
         valid = false;
      }
      
      if($("#local_amount_"+row_number).val() == "") {
         error[i++] = "Local Amount";
         valid = false;
      }

   });

   return valid;
}

function validate_double_reference() {
   var row_number;
   var current_ref;
   var other_row_number;
   
   double_reference = 0;

   $($(".document_reference").get().reverse()).each(function() {
      console.log('reverse :: '+$(this).val());
      row_number = $(this).closest('tr').attr("id");
      current_ref = $("#document_reference_"+row_number).val();

      console.log("Current Row Number :: "+row_number);
      console.log("Current Document Reference :: "+current_ref);

      $(".document_reference").each(function() {
         other_row_number = $(this).closest('tr').attr("id");

         if(row_number !== other_row_number) {
            if(current_ref.toLowerCase() == $(this).val().toLowerCase()) {
               console.log(">>>>> Internal Reference Exists >>>> ");
               double_reference = 1;
               $("#document_reference_"+row_number).closest('td').find('.error-ref').css("display", "block");
               $("#document_reference_"+row_number).focus();
               return false;
            } else {
               console.log(">>>>> Internal Reference NOT Exists >>>> ");
               double_reference = 0;
               $("#document_reference_"+row_number).closest('td').find('.error-ref').css("display", "none");
            }
         } else {
            console.log(">>>>> Same row's :: Reference Check NOT needed! >>>> ");
         }

      });

   });

   return true;
}