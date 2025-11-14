// QUOTATION process.js
var table = "";
var billing_id = 0;
var billing_details = "";
var billing_uom = "";
var billing_type = "";
var processing_row_number = 0;
var customer_id = "";
var employee_id = "";
var default_gst_rate = 0;

// document starts
$(function() {

   var page = $('#page').val();
   console.log(":: Page :: "+page);

   $('select').select2();

   $('#customer_id')
    .select2()
    .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append('<a id="lnk_add_customer" class="add_lnk" title="Add Customer"><i class="fa fa-plus"></i> New Customer</a>');
   });

   $('#employee_id')
    .select2()
    .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append('<a id="lnk_add_employee" class="add_lnk" title="New Staff"><i class="fa fa-plus"></i> New Staff</a>');
   });

   $('#department_id')
    .select2()
    .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append('<a id="lnk_add_department" class="add_lnk" title="New Department"><i class="fa fa-plus"></i> New Department</a>');
   });

   if(page == "new") {
      // Reference - duplicate check
      var ref_no = $('#quotation_ref_no').val();
      var ref_parts = ref_no.split('.');
      $.post('/quotation/ajax/quotation_new_reference', {
         text_prefix: ref_parts[0],
         number_suffix: ref_parts[1]
      }, function (data) {
         if (data == 1) {
            window.location.href = "/quotation";
         }
      });
   }      

   if(page == "edit") {
      // Edit Page
      customer_id = $("#customer_id").val();
      employee_id = $("#employee_id option:selected").val();
      console.log(":: Customer ID :: "+customer_id);
      console.log(":: Employee ID :: "+employee_id);
      
      $("#customer_id").select2('destroy'); 
   }

   // customer select
   $("#customer_id").change(function() {
      customer_id = $("#customer_id option:selected").val();
      if (customer_id !== "") {
         $.post('/quotation/ajax/get_customer_details', {
            customer_id: customer_id
         }, function (data) {
            var obj = $.parseJSON(data);

            // address details, phone, email & currency
            $(".dsply_customer_details").html(obj.customer_address);
            $(".dsply_customer_details").show();
            
            // hidden fields
            $('#customer_code').val(obj.customer_code);
            $('#customer_currency').val(obj.customer_currency);
            $('#customer_currency_rate').val(obj.currency_rate);
            $('.dsply_customer_currency').html(obj.customer_currency);
            $('.dsply_customer_currency_rate').html(obj.currency_rate);

            var system_currency = $('#system_currency').val();
            if(obj.customer_currency !== system_currency) {
               $('.local_values').show();
            } else {
               $('.local_values').hide();
            }
         });
      } else {
         // address details, phone, email & currency
         $(".dsply_customer_details").html("").hide();
      }
   });

   // employee select
   $("#employee_id").change(function(event) {
      employee_id = $("#employee_id option:selected").val();
      if (employee_id !== "") {
         $('#header_notes').focus();
      }
   });

   // btn - add new item 
   $(document).on('click', '.btn_add_item', function() {
      if(!isFormValid()) {
         return;
      }

      $('#process').val('add');
      $('#billing').val('').select2();
      $('#edit_id').val('');

      clear_inputs();
      if(page == "new") {
         $('.entry_field').hide();
      }
      $('#entryModal').modal('show');
   });

   // customer select
   $('#billing').change(function() {

      clear_inputs();

      billing_id = $("#billing option:selected").val();
      if (billing_id !== "") {

         var billing_exists = false;
         $("#tbl_items").find('.billing_id').each(function() {
            if(billing_id === $(this).val()) {

               $("#entryModal").modal('hide');

               // Item already used in the table
               $('#alertModal .modal-title').html("Item Exists!");
               $('#alertModal .modal-body').html("The selected Product / Service item is already used in this quotation. <br /><br />Please select any other");
               $('#alertModal').modal();

               billing_exists = true;
            }
         });
         
         if(billing_exists) {
            return false;
         }

         $.post('/quotation/ajax/get_billing', {
            billing_id: billing_id,
            customer_code: $('#customer_code').val()
         }, function (data) {
            var obj = $.parseJSON(data);
            $('#billing_id').val(billing_id);
            $('#billing_type').val(obj.billing_type);

            // Selected item is Product & Billing Price = 0, Open SetUnitPrice Modal
            if(obj.billing_type == "Product" && obj.unit_price == 0) {

               $('#quantity').val("1");
               $('#uom').val(obj.billing_uom);
               $('#unit_price').val(obj.unit_price);
               
               $('#modal_billing_id').val(billing_id);
               $('.billing_details').text(obj.billing_details);

               $('#entryModal').modal('hide');
               $("#setUnitPriceModal").modal();

            // If selected item is "Service" and "UOM" is not used then
            // hide quantity, unit price and discount fields
            // show unit price in item amount field with editable
            } else if(obj.billing_type == "Service" && obj.billing_uom == "") {
   
               $('#quantity').prop("readonly", true);
               $('#discount').prop("readonly", true);
               
               $('#amount').prop('readonly', false);
               $('#amount').val(obj.unit_price);

            } else {
               
               $('#quantity').val("1");
               $('#uom').val(obj.billing_uom);
               $('#unit_price').val(obj.unit_price);

               process_item_amount();
            }

            $('.entry_field').show();

            process_modal_gst();
         });
      } else {
         $('.entry_field').show();
      }
   });

   $('#btn-alert-modal-ok').click(function() {
      $('#alertModal').modal('hide');
      $('#billing').val('').select2();
      $("#entryModal").modal();
   });

   $('#setUnitPriceModal').on('shown.bs.modal', function () {
      $('#billing_price_per_uom').focus();
   });

   $('#btn_close_price_modal').click(function() {
      $("#setUnitPriceModal").modal('hide');
      $('#billing').val('').select2();
      $("#entryModal").modal();
   });

   $(document).on("change", "#billing_price_per_uom", function() {
      if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
         var amount = parseFloat($(this).val()).toFixed(2);
         $(this).val(amount);
      }
   });

   var set_price_tbl = "";

   $('#btn_add_special_price').click(function() {
      if($('#billing_price_per_uom').val() == "") {
         $('#billing_price_per_uom').focus();
      } else {

         $("#setUnitPriceModal").modal('hide');

         set_price_tbl = "customer_price";
         $('#confirmSubmitModal .modal-title').html("<i class='fa fa-info'></i> Confirm Special Price");
         $('#confirmSubmitModal .modal-body').html("Set price will be added as special price for this customer <br /><br > Are you sure?");
         $("#confirmSubmitModal").modal();
      }
   });

   $('#btn_update_billing').click(function() {
      if($('#billing_price_per_uom').val() == "") {
         $('#billing_price_per_uom').focus();
      } else {
         $("#setUnitPriceModal").modal('hide');

         set_price_tbl = "master_billing";
         $('#confirmSubmitModal .modal-title').html("<i class='fa fa-info'></i> Confirm Billing Price");
         $('#confirmSubmitModal .modal-body').html("Set price will be updated in Billing Master which will be same for all the customer's <br /><br > Are you sure?");
         $("#confirmSubmitModal").modal();
      }
   });

   $('#btn-confirm-no').click(function() {
      $("#confirmSubmitModal").modal('hide');
      $("#setUnitPriceModal").modal();
   });

   $('#btn-confirm-yes').click(function() {
      $.post('/quotation/ajax/set_unit_price', {
         tbl: set_price_tbl,
         customer_id: customer_id,
         billing_id: $('#modal_billing_id').val(),
         billing_price_per_uom: $('#billing_price_per_uom').val()
      }, function (data) {
         if(data == "error") {
         } else {
            $('#unit_price').val($('#billing_price_per_uom').val());
            process_item_amount();
            $("#confirmSubmitModal").modal('hide');
            $("#entryModal").modal();
         }
      });
   });

   // quantity, discount change
   $(document).on('change', '#quantity, #discount', function() {
      process_item_amount();
   });

   // gst category change
   $(document).on('change', '#gst_category', function() {
      var gst_code = $('option:selected', this).val();

      if(gst_code !== "") {

         // customer must inputted gst number if the Special GST Category needs to be used
         if(gst_code == "SRCA-S") {
            $.post('/quotation/ajax/get_customer_gst_number', {
               customer_id: customer_id
            }, function (data) {
               if (data == 0) { // gst number not inputted
                  $('#specialGSTModal').modal();
               }
            });
         }

         // get gst rate for the selected GST Category and calculate Item GST Amount
         $.post('/quotation/ajax/get_gst_details', {
            gst_code: gst_code
         }, function (data) {
            if(data !== "") {
               var obj = $.parseJSON(data);
               $('#gst_rate').val(obj.gst_percentage);

               process_modal_gst();
            }
         });
      } else {
         $('#gst_amount').val("");
      }
   });

   $("#btn-special-gst-yes").click(function() {
      $("#specialGSTModal").modal('hide');
      $("#gst_category").val("SR").trigger("change");
      $("#gst_category").select2("open");
   });

   $("#btn-special-gst-no").click(function() {
      $("#specialGSTModal").modal('hide');

      $.confirm({
         title: '<i class="fa fa-info"></i> Abort Quotation',
         content: 'Are you sure to abort this process ?',
         buttons: {
            yes: {
               btnClass: 'btn-warning',
               action: function(){
                  window.location = "/quotation";
               }
            },
            no: {
               btnClass: 'btn-dark',
               action: function(){
                  $("#gst_category").val("SR").trigger("change");
               }
            },
         }
      });
   });

   // EDIT
   $(document).on('click', '.dt_edit', function () {

      if(!isFormValid()) {
         return;
      }
      
      row_number = $(this).closest('tr').attr('id');

      $('#billing').select2("destroy");
      $('#billing').val($('#billing_id_'+row_number).val());
      $('#billing').select2();

      $('#quantity').val($('#quantity_'+row_number).val());
      $('#uom').val($('#uom_'+row_number).val());
      $('#unit_price').val($('#unit_price_'+row_number).val());

      $('#discount').val($('#discount_'+row_number).val());
      $('#amount').val($('#amount_'+row_number).val());

      $('#gst_category').select2("destroy");
      $('#gst_category').val($('#gst_code_'+row_number).val());
      $('#gst_category').select2();

      $('#gst_rate').val($('#gst_rate_'+row_number).val());
      $('#gst_amount').val($('#gst_amount_'+row_number).val());
      
      $('#process').val('edit');
      $('#edit_id').val(row_number);

      $('.entry_field').show();

      $('#entryModal').modal('show');
   });

   $(document).on('click', '#btn_save_item', function() {
      if(isEntryValid()) {
         if($('#process').val() == 'add') { // New Row
            $row = $("#tbl_clone tbody tr").clone();
         } else if($('#process').val() == "edit") { // Existing Row
            $row = $('tr[id="'+$("#edit_id").val()+'"]');
         }


         $row.find('input.billing_desc').val($("#billing>option:selected").text());
         $row.find('input.billing_id').val($('#billing').val());

         $row.find('input.quantity').val($('#quantity').val());
         $row.find('input.uom').val($('#uom').val());
         $row.find('input.unit_price').val($('#unit_price').val());
         $row.find('input.discount').val($('#discount').val());

         $row.find('input.amount').val($('#amount').val());

         $row.find('input.gst_desc').val($("#gst_category>option:selected").text());
         $row.find('input.gst_code').val($('#gst_category').val());
         $row.find('input.gst_rate').val($('#gst_rate').val());
         $row.find('input.gst_amount').val($('#gst_amount').val());

         if($('#process').val() == "add") {
            // append new row to the table
            $('#tbl_items').append($row);
            
            $('#tbl_total').show();
            $('.ft').show();
            $('.notes').show();
            $('.btns').show();

            sortTblRowsByID();
         }
   
         process_subtotal();

         $('#entryModal').modal('hide');
         $('#tbl_items').show();

         row_number = $row.attr('id');
         $('html, body').animate({
            scrollTop: $('#billing_desc_'+row_number).offset().top
         }, 'slow');
      }
   });

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

   // lsdModal - starts
   $(document).on('change', '#lsd_percentage', function() {
      var disc_perc = Number($(this).val());
      console.log(disc_perc);
      if (disc_perc < 0) {
         $(this).val("");
      } else if (disc_perc >= 100) {
         $(this).val("");
      } else {
         $(this).val(disc_perc.toFixed(2));
         $('#lsd_value').val("");
      }
   });

   $(document).on('change', '#lsd_value', function() {
      var disc_val = Number($(this).val());
      var subtotal = Number($('#sub_total').val());
      if (disc_val < 0) {
         $(this).val("");
      } else if (disc_val > subtotal) {
         $(this).val("");
      } else {
         $(this).val(disc_val.toFixed(2));
         $('#lsd_percentage').val("");
      }
   });

   $('#lsdModal').on('shown.bs.modal', function () {
      //$('#lsd_percentage').focus();
   });

   $("#btn-lsd-modal-apply").click(function() {
      process_lsd();

      $("#lsdModal").modal('hide');
   });

   $("#btn-lsd-modal-cancel").click(function() {
      $("#lsdModal").modal('hide');
   });
   // lsdModal - ends

   $(document).on('click', '.btn_add_details', function() {
      processing_row_number = $(this).closest('tr').attr("id");
      var billing_id = $('#billing_id_'+processing_row_number).val();
      var item_details = "";
      if(billing_id !== "") {
         $.post('/quotation/ajax/get_billing_details', {
            billing_id: billing_id
         }, function(data) {
            var obj = $.parseJSON(data);
            $(".dsply_item_header").html(obj.billing_type + " Details");
            $(".dsply_item_code_desc").html(obj.billing_code +" : "+obj.billing_description);

            item_details = $('#item_details_'+processing_row_number).val();
            $('#item_details').val(item_details);

            $('#detailsModal').modal();
         });
      }
   });

   // ID :: item_details is from modal
   // ID :: item_details_CURRENT_ROW from Itemized Entries
   $(document).on('click', '#btn-desc-modal-save', function() {
      var item_details = $('#item_details').val();
      if(item_details !== "") {
         $('#item_details_'+processing_row_number).val(item_details);
      } else {
         $('#item_details_'+processing_row_number).val("");
      }

      console.log("Item Details :: "+$('#item_details_'+processing_row_number).val());
      $('#detailsModal').modal('hide');
   });

   // delete item
   var delete_row_id = -1;
   $(document).on('click', '.btn_delete_row', function() {
      delete_row_id = $(this).closest('tr').attr("id");
      $('#confirmDeleteModal .modal-body').html("Click 'Yes' to delete the current item");
      $("#confirmDeleteModal").modal();
   });

   // item delete = YES
   $('#btn-confirm-delete-yes').click(function() {
      $('tr#'+delete_row_id).remove();

      if($('#tbl_items tbody tr').length == 0) {
         $('.ft').hide();
         $('#tbl_total').hide();
         $('.footer_notes').hide();
         $('#btn_print').hide();
      }

      $(".sno").each(function (i) {
         $(this).html(i+1);
      });

      process_subtotal();

      $("#confirmDeleteModal").modal('hide');
   });

   // print PDF
   $("#btn_print").on('click', function (e) {
      $("#form_").attr("action", '/quotation/print_stage_1');
      $("#form_").attr("target", "_blank");
      $('#form_').submit();
   });

   // submit
   $("#btn_submit").on('click', function (e) {

      if(!isFormValid()) {
         return;
      }

      if($('#tbl_items tbody tr').length == 0) {
         toastr.error("Please add item!");
         return;
      }

      var action = "";
      if(page == "edit") {
         action = "/"+page;
      }
      
      $("#form_").attr("action", '/quotation/save'+action);
      $("#form_").attr("target", "_self");
      $('#form_').submit();
   });

}); // document ends

function isFormValid() {
   var valid = true;

   if(customer_id == "") {
      valid = false;
      $("#customer_id").select2('open');
      toastr.error("Required: Customer");
   } else if(employee_id == "") {
      valid = false;
      $("#employee_id").select2('open');
      toastr.error("Required: Staff");
   }

   return valid;
}

function clear_inputs() {

   $('#quantity').prop("readonly", false);
   $('#discount').prop("readonly", false);

   $('#quantity').val('');
   $('#uom').val('');
   $('#unit_price').val('');
   $('#discount').val('');
   $('#amount').val('');

   $('#gst_category').val('SR').select2();

   $('#gst_rate').val($('#std_gst_rate').val());
   $('#gst_amount').val('');   
}

function isEntryValid() {
   var valid = true;
   if($('#billing').val() == "") {
      $("#billing").select2('open');
      valid = false;
   } else if($("#uom").val() !== '' && $('#quantity').val() == "") {
      $('#quantity').focus();
      valid = false;
   } else if($('#amount').val() == "") {
      $('#amount').focus();
      valid = false;
   } else if($('#gst_category').val() == "") {
      $("#gst_category").select2('open');
      valid = false;
   } else if($('#gst_amount').val() == "") {
      $('#gst_amount').focus();
      valid = false;
   }

   return valid;
}

function roundOf(value) {
   var roundOfValue = Math.round(value * 100) / 100;
   return roundOfValue;
}

// Function to validate the discount field (Product)
function validateDiscount(data) {
   var disc = Number(data.value);
   if (disc < 0) {
      data.value = '';
   } else if (disc >= 100) {
      data.value = '';
   } else {
      console.log("Invalid discount")
   }
}

function process_item_amount() {

   console.log(" :: 1. Process Item Amount :: ");

   var quantity = 0;
   var unit_price = 0;
   var discount = 0;
   var item_amount = 0;

   quantity = $("#quantity").val();
   unit_price = $("#unit_price").val();
   discount = $("#discount").val();
   if(discount == "") {
      discount = 0;
   }
   item_amount = quantity * unit_price;
   discount_value = item_amount * discount / 100;
   
   console.log(" :: Item Amount Before Discount >>> "+item_amount);
   console.log(" :: Discount In Value >>> "+discount_value);

   $('#amount').val((item_amount - discount_value).toFixed(2));
   console.log(" :: Item Amount After Discount >>> "+$('#amount').val());

   process_modal_gst();
}

function process_subtotal() {
   
   console.log(" :: 2. Process Sub-Total :: ");

   var sub_total = 0;
   $("#tbl_items tbody tr").each(function() {
      row_number = $(this).attr("id");
      console.log("Row "+(Number(row_number) + 1)+" Values");

      // Calculate Sub-Total
      sub_total += Number($('#amount_'+row_number).val());
      $('.dsply_sub_total').html(sub_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
      $('#sub_total').val(sub_total.toFixed(2));
   });

   console.log(" :: Subtotal :: "+sub_total.toFixed(2));

   process_lsd();
}

function process_lsd() {

   console.log(" :: 3. Process LSD :: ");

   var lsd_percentage = 0;
   if($('#lsd_percentage').val() !== "") {
      lsd_percentage = $('#lsd_percentage').val();
   }

   var lsd_value = 0;
   if($('#lsd_value').val() !== "") {
      lsd_value = $('#lsd_value').val();
   }

   var subtotal = $('#sub_total').val();

   if(lsd_percentage > 0) {
      console.log(":: LSD Code :: P");
      
      // If user inputs lsd in percentage then calculate lsd value also
      lsd_value = subtotal * lsd_percentage / 100;

      $("#lsd_code").val("P");
      $("input[name='lsd_percentage']").val(roundOf(lsd_percentage).toFixed(2));
      $("input[name='lsd_value']").val(roundOf(lsd_value).toFixed(2));

      $('.dsply_lsd_percentage').show();
      $('.dsply_lsd_percentage').html(roundOf(lsd_percentage).toFixed(2) + "%");

      console.log(" :: lsd_precentage (p) :: "+ roundOf(lsd_percentage).toFixed(2));
      console.log(" :: lsd_value (p) :: "+ roundOf(lsd_value).toFixed(2));

   } else if(lsd_value > 0) {

      console.log(" :: LSD Code :: V");

      // If user inputs lsd in value then calculate lsd percentage also
      lsd_percentage = lsd_value * 100 / subtotal;               

      $("#lsd_code").val("V");
      $("input[name='lsd_percentage']").val(lsd_percentage);
      $("input[name='lsd_value']").val(roundOf(lsd_value).toFixed(2));

      $('.dsply_lsd_percentage').html("");
      $('.dsply_lsd_percentage').hide();

      console.log(" :: lsd_precentage (v) :: "+ lsd_percentage);
      console.log(" :: lsd_value (v) :: "+ roundOf(lsd_value).toFixed(2));

   } else {
      
      $("#lsd_code").val("");
      $("input[name='lsd_percentage']").val("0");
      $("input[name='lsd_value']").val("0");

      $('.dsply_lsd_percentage').html("");
      $('.dsply_lsd_percentage').hide();
   }

   $('.dsply_lsd_amount').html("(" + roundOf(lsd_value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ")");

   process_values();
}

function process_values() {
   console.log(":: 4. Process Final Values ::");

   var row_number = 0;
   var item_amount = 0;
   var item_gst_rate = 0;
   
   var foreign_gst_total = 0;
   var foreign_net_total = 0;

   var local_gst_total = 0;
   var local_net_total = 0;
   var customer_currency_rate = $('#customer_currency_rate').val();
   
   var sub_total = Number($('#sub_total').val());
   var lsd_percentage = parseFloat($("input[name='lsd_percentage']").val());
   var lsd_value = parseFloat($("input[name='lsd_value']").val());

   // Calculate Net-Value After LSD
   $("#net_after_lsd").val((sub_total - lsd_value).toFixed(2));
   $('.dsply_net_after_lsd').html((sub_total - lsd_value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

   var GST_registered_merchant = $('#GST_registered_merchant').val();
   $("#tbl_items tbody tr").each(function() {
      row_number = $(this).attr("id");         
      
      if(GST_registered_merchant == "true") {
         // Calculate Item GST and Pro-Rating            
         item_amount = $('#amount_'+row_number).val();
         item_gst_rate = $('#gst_rate_'+row_number).val();

         if(lsd_percentage !== 0) { // pro-rating
            item_gst_amount = (item_amount - (item_amount * lsd_percentage / 100)) * (item_gst_rate / 100);
         } else {
            item_gst_amount = item_amount * item_gst_rate / 100;
         }
         $('#gst_amount_'+row_number).val(item_gst_amount.toFixed(2));
         console.log(" :: Item GST Amount >>> "+$('#gst_amount_'+row_number).val());         

         // Calculate GST-Total
         foreign_gst_total += Number($('#gst_amount_'+row_number).val());
         $('#f_gst_total').val(foreign_gst_total.toFixed(2));
         $('.dsply_f_gst_total').html(foreign_gst_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
      }
   });

   // Calculate Grand Net-Total Adding GST
   foreign_net_total = Number($("#net_after_lsd").val()) + foreign_gst_total;
   $("#f_net_total").val(foreign_net_total.toFixed(2));
   $('.dsply_f_net_incl_gst').html(foreign_net_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

   if(GST_registered_merchant == "true") {
      // calculate gst total in SYSTEM CURRENCY
      local_gst_total = foreign_gst_total / customer_currency_rate;
      $("#gst_total").val(local_gst_total.toFixed(2));
      $('.dsply_gst_total').html(local_gst_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
   }

   // calculate net total in SYSTEM CURRENCY
   local_net_total = foreign_net_total / customer_currency_rate;
   $("#net_total").val(local_net_total.toFixed(2));
   $('.dsply_net_incl_gst').html(local_net_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
}

function process_modal_gst() {
   var lsd_percentage = parseFloat($("input[name='lsd_percentage']").val());
   var item_amount = $('#amount').val();
   var item_gst_rate = $('#gst_rate').val();
   var item_gst_amount = 0;

   if(lsd_percentage !== 0) { // pro-rating
      item_gst_amount = (item_amount - (item_amount * lsd_percentage / 100)) * (item_gst_rate / 100);
   } else {
      item_gst_amount = item_amount * item_gst_rate / 100;
   }
   $('#gst_amount').val(item_gst_amount.toFixed(2));
}
