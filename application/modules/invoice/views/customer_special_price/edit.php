<div class="content-header">
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0 header_txt">Customer Price - Edit</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i></a></li>
               <li class="breadcrumb-item"><a href="/invoice">Invoice</a></li>
               <li class="breadcrumb-item"><a href="/invoice/customer_price">Customer Price</a></li>
               <li class="breadcrumb-item active">Edit</li>
            </ol>
         </div>
      </div>
   </div>
</div>

<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
            <form autocomplete="off" id="form_" method="post" action="#">
               <input type="hidden" name="process" value="update" />
               <div class="card card-default">
                  <div class="card-body">
                     <div class="row">
                        <div class="col-md-6">
                           <label for="customer_id">Customer</label><br />
                           <select id="customer_id" name="customer_id" class="form-control select2">
                              <?php echo $customer_options; ?>
                           </select>
                        </div>
                     </div>
                     <div class="row table-responsive" style="margin-top: 20px">
                        <div class="col-md-12">
                           <table class="table" id="tbl_" style="min-width: 720px; width: 100%">
                              <thead>
                                 <tr>
                                    <th>Product</th>
                                    <th>UOM</th>
                                    <th>Price per UOM</th>
                                    <th></th>
                                 </tr>
                              </thead>
                              <tbody>

                              <?php
                              $i = 0;
                              $table = 'customer_price';
                              $where = ['customer_code' => $customer_code];
                              $order_by = 'stock_code';
                              $query = $this->inv_model->get_tbl_data($table, $where, $order_by);
                              $list = $query->result();                              
                              foreach ($list as $record) {
                                  $billing_data = $this->custom->getMultiValues('master_billing', 'billing_id, billing_uom', ['stock_code' => $record->stock_code]);

                                  $product_options = $this->custom->createDropdownSelect('master_billing', ['billing_id', 'stock_code', 'billing_description'], '', ['- ', ' ', ' '], [], ['billing_id' => $billing_data->billing_id]); ?>

                                 <tr id="<?php echo $i; ?>">
                                    <td style="width: 400px">
                                       <select id="product_id_<?php echo $i; ?>" name="product_id[]" class="form-control ddm_product">
                                          <?php echo $product_options; ?>
                                       </select>
                                    </td>
                                    <td style="width: 90px">
                                       <input type="text" id="uom_<?php echo $i; ?>" value="<?php echo $billing_data->billing_uom; ?>" class="form-control" readonly />
                                    </td>
                                    <td style="width: 170px">
                                       <input type="number" id="unit_cost_<?php echo $i; ?>" name="unit_cost[]" value="<?php echo $record->billing_price_per_uom; ?>" class="form-control inp_unit_cost" />
                                    </td>
                                    <td>
                                       <button type="button" class="btn btn-danger btn-sm btn_delete_row">Delete</button>
                                    </td>
                                 </tr>
                                 <?php
                           ++$i;
                              }
                              ?>
                              </tbody>
                              <tfoot>
                                 <tr>
                                    <td colspan="4">
                                       <a href="#" class="btn_add_row btn btn-success btn-sm"><i class="fa-solid fa-plus"></i> Add Item</a>
                                    </td>
                                 </tr>
                              </tfoot>
                           </table>
                        </div>
                     </div>
                  </div>
                  <div class="card-footer">
                     <a href="/invoice/customer_price" class="btn btn-info btn-cancel">Back</a>
                     <button type="button" id="btn_submit" class="btn btn-warning float-right">Submit</button>
                  </div>
               </div> <!-- card - ends -->

            </form>
         </div>
      </div>

   </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" integrity="sha512-0V10q+b1Iumz67sVDL8LPFZEEavo6H/nBSyghr7mm9JEQkOAm91HNoZQRvQdjennBb/oEuW+8oZHVpIKq+d25g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js" integrity="sha512-zP5W8791v1A6FToy+viyoyUUyjCzx+4K8XZCKzW28AnCoepPNIXecxh9mvGuy3Rt78OzEsU+VCvcObwAMvBAww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<style>
   select {
      width: 100%;
   }
</style>
<script>
   // document starts
   $(function() {

      if (window.location.href.indexOf("view_customer_price") > -1) {
         $('.header_txt').html("Customer Price - View");
         $("input").prop('disabled', true);
         $("select").prop('disabled', true);
         $('#btn_submit').hide();
         $('.btn_add_row').hide();
         $('.btn_delete_row').hide();
         $('.btn-info').addClass('float-right');         
         return false;
      }

      $('select').select2();

      $(document).on('change', '.ddm_product', function() {
         var product_id = $('option:selected', this).val();         
         var row_number = $(this).closest('tr').attr("id");

         if(product_id == "") {
            $("#uom_"+row_number).val("");
            $("#unit_cost_"+row_number).val("");        
            return;
         }

         var same_product = false;
         $("#tbl_ tbody tr").each(function (i, val) {            
            current_row_number = $(this).closest('tr').attr("id");
            current_product_id = $("#product_id_"+i).val();

            if(row_number !== current_row_number) {
               if(product_id == current_product_id) {

                  //$(this).closest('tr').find('select').val("");
                  //$(this).closest('tr').find('input').val("");

                  $('#errorAlertModal .modal-title').html("Product Exists");
                  $('#errorAlertModal .modal-body').html("Please choose any other product");
                  $('#errorAlertModal').modal();
                  same_product = true;
                  return;
               }
            }
            i++;
         });

         $("#uom_"+row_number).val("");
         $("#unit_cost_"+row_number).val("");

         if(product_id !== "" && !same_product) {
            $.post('/invoice/customer_special_price/get_product_details', {
               billing_id: product_id,
               cust_id: $("#customer_id").val()
            }, function (data) {
               var json = $.parseJSON(data);

               $("#uom_"+row_number).val(json.billing_uom);
               $("#unit_cost_"+row_number).val(json.billing_price_per_uom);

               $("#unit_cost_"+row_number).focus();
            });
         }
      });

      $(document).on("change", ".inp_unit_cost", function() {
         if($.trim($(this).val()) !== "" && $(this).val() !== 0) {
            var amount = parseFloat($(this).val()).toFixed(2);
            $(this).val(amount);
         }
      });

      // btn - add new item 
      $(document).on('click', '.btn_add_row', function() {
         $row_valid = true;

         if($('#customer_id').val() == "") {
            $row_valid = false;
            $("#customer_id").select2('open');
         } else {
            $("#tbl_ tbody tr").each(function (i, val) {
               $product = $(this).find("select.ddm_product").val();
               $unit_cost = $(this).find("input.inp_unit_cost").val();
               current_row_number = $(this).attr("id");
               if($product == "") {
                  $("#product_id_"+current_row_number).select2('open');
                  $row_valid = false;
               } else if($unit_cost == "") {
                  $("#unit_cost_"+current_row_number).focus();
                  $row_valid = false;
               }
            });
         }

         if(!$row_valid) {
            return;
         }

         $tr = $(this).closest('table').find('tbody tr:last');
         var allTrs = $tr.closest('table').find('tbody tr');
         var lastTr = allTrs[allTrs.length-1];
         $(lastTr).find('select').select2("destroy"); // remove select 2 before clone tr
         $new_row = $(lastTr).clone();
         
         $new_row.find('td').each(function() {
            var el = $(this).find(':first-child');
            var id = el.attr('id') || null;
            console.log("Last Row Field ID :: "+id);
            if(id) {
               // Get id number from ID text
               // Ex: unit_price_123 :: last_record_number = 123
               var last_record_number = id.split("_").pop();

               var prefix = id.substr(0, (id.length-(last_record_number.length)));
               el.attr('id', prefix+(+last_record_number+1));
            }
         });
         $new_row.find('input').val('');
         $new_row.find('select').val('');
         $last_no = $new_row.attr('id');
         $new_row.attr('id', parseInt($last_no) + 1);
         $tr.closest('table').append($new_row);
         $(lastTr).find('select').select2(); // add select2 again
         $new_row.find('select').select2(); // add select2 again
      });

      var delete_row_id = -1;
      $(document).on('click', '.btn_delete_row', function() {
         delete_row_id = $(this).closest('tr').attr("id");
         $('#confirmDeleteModal .modal-body').html("Click 'Yes' to delete the selected row");
         $("#confirmDeleteModal").modal();
      });

      $('#btn-confirm-delete-yes').click(function() {
         $('tr#'+delete_row_id).remove();
         $("#confirmDeleteModal").modal('hide');
      });

      $("#btn_submit").click(function() {
         var url = "/invoice/customer_special_price/save";
         $('#form_').attr("action", url);
         $('#form_').submit();
      });

   }); // document ends
</script>