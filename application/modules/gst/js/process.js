// document starts
$(function() {

   var row_number = '';
   var double_ref = 0;

   $('select').select2();

   $(document).on("change", "#ref_no", function(e) {
      var ref_no = $(this).val();
      double_ref = 0;

      if(ref_no !== "") {

         // if page is edit and user try changing different ref and again changing to same one
         if(ref_no == $('#original_ref_no').val()) {
            return false;
         }

         $.post('/gst/ajax/double_ob', {
            ref_no: $("#ref_no").val()
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
   });

   $(".btn_add_item").on('click', function() {
      if(!isFormValid()) {
         return false;
      }

      $('#process').val('add');

      clear_inputs();
      $('#entryModal').modal('show');
   });

   $(document).on('change', '#gst_category', function() {
      var gst = $('option:selected', this).val();
      $('.entry_field').hide();

      if(gst !== "") {
         $('.entry_field').show();
      }
   });

   $(document).on("change", "#amount", function() {
      if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
         $(this).val(Number($(this).val()).toFixed(2));
      }
   });
   
   // EDIT
   $(document).on('click', '.dt_edit', function () {

      if(!isFormValid()) {
         return;
      }

      row_number = $(this).closest('tr').attr('id');

      $('#ob_id').val($('#ob_id_'+row_number).val());

      $('#gst_category').select2("destroy");
      $('#gst_category').val($('#gst_code_'+row_number).val());
      $('#gst_category').select2();

      $('#iden').select2("destroy");
      $('#iden').val($('#iden_'+row_number).val());
      $('#iden').select2();

      $('#amount').val($('#amount_'+row_number).val());      

      $('.entry_field').show();
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
                  $.post('/gst/ajax/delete_ob_entry', {
                     ob_id: $('#ob_id_'+row_number).val()
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

   // save entry
   $("#btn_save").on('click', function () {

      if(!isFormValid() || !isModalValid()) {
         return false;
      }

      if(double_ref == 1) {
         $('#ref_no').focus();
         return false;
      }

      save();
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

   $('#gst_category').select2("destroy").val('').select2();
   $('#iden').select2("destroy").val('').select2();
   $('#amount').val('');

   $('.entry_field').hide();
}

function isFormValid() {
   var valid = true;
   if($('#doc_date').val() == "") {
      $("#doc_date").focus();
      valid = false;
   } else if($('#ref_no').val() == "") {
      $("#ref_no").focus();
      valid = false;
   }
   return valid;
}

function isModalValid() {
   var valid = true;
   if($('#gst_category').val() == "") {
      $("#gst_category").select2('open');
      valid = false;
   } else if($('#amount').val() == "") {
      $('#amount').focus();
      valid = false;
   }
   return valid;
}

function save() {
   // header values
   var doc_date = $("#doc_date").val();
   var ref_no = $("#ref_no").val();
   var remarks = $("#remarks").val();
   var gst_type = $("#gst_type").val();

   // body values
   var ob_id = $("#ob_id").val();
   var gst_category = $("#gst_category").val();
   var iden = $("#iden").val();
   var amount = $("#amount").val();

   $.post('/gst/ajax/save_ob', {
      ob_id: ob_id,
      date: doc_date,
      dref: ref_no,
      iden: iden,
      rema: remarks,
      gsttype: gst_type,
      gstcate: gst_category,
      amou: amount
   }, function(ob_id) {
      $("#ob_id").val($.trim(ob_id));

      manage_entry();
   });
}

function manage_entry() {

   if($('#process').val() == 'add') { // New Row
      $row = $("#tbl_clone tbody tr").clone();
   } else if($('#process').val() == "edit") { // Existing Row
      $row = $('tr[id="'+$("#edit_id").val()+'"]');
   }

   $row.find('input.ob_id').val($('#ob_id').val());

   $row.find('input.gst_desc').val($("#gst_category>option:selected").text());
   $row.find('input.gst_code').val($("#gst_category").val());

   $row.find('input.iden_details').val($("#iden>option:selected").text());
   $row.find('input.iden').val($("#iden").val());

   $row.find('input.amount').val($("#amount").val());   
   
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